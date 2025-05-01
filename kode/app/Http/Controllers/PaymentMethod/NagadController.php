<?php

namespace App\Http\Controllers\PaymentMethod;

use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use Karim007\LaravelNagad\Facade\NagadPayment;

use Illuminate\Http\Request;
use App\Models\PaymentLog;
use App\Http\Utility\PaymentInsert;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Models\Order;
use App\Models\PaymentMethod;
use Config;

class NagadController extends Controller
{
    private $tnxID;
    public $nagadHost;
    private $tnxStatus = false;
    public $amount;
    private $merchantAdditionalInfo = [];




    public $paymentMethod;
    public function __construct(){
        $this->paymentMethod = PaymentMethod::where('unique_code', 'NAGAD104')->first();

    }


    public function config($trxCode)
    {
      
        if(!$this->paymentMethod){
            return false;
        }
        $sandbox = true;
        if ($this->paymentMethod->payment_parameter->environment == 'live') {
            $sandbox = false;
        } 


        $config = [
            'sandbox'         =>  $sandbox ,
            'merchant_id'     =>  $this->paymentMethod->payment_parameter->merchant_id ,
            'merchant_number' =>  $this->paymentMethod->payment_parameter->merchant_number ,
            'public_key'      =>  $this->paymentMethod->payment_parameter->public_key ,
            'private_key'     =>  $this->paymentMethod->payment_parameter->private_key ,
            'callbackURL'     =>  route('nagad.callback', $trxCode) ,
            "timezone"        => "Asia/Dhaka"
        ];

        
        \Config::set('nagad',  $config );
        
  

        return true;

    }
    
    public function payment($trx_code = null)
    {


        try {
            $paymentTrackNumber =  $trx_code ??  session()->get('payment_track');
            $paymentLog = PaymentLog::with(['user','order','seller','paymentGateway'])
                              ->where('status', PaymentLog::PENDING)->where('trx_number', $paymentTrackNumber)->first();
            if(!$paymentLog || !$this->config($paymentLog->trx_number)){
                return redirect()->route('home')->with('error', translate('Invalaid Transaction'));
            }
       
            date_default_timezone_set('Asia/Dhaka');
    
    
            $nagadHost   = "http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs";
    
            if ($this->paymentMethod->payment_parameter->environment == 'live'){
                $nagadHost = "https://api.mynagad.com/api/dfs";
            }
            
    
            $redirectUrl = $this->tnxID($paymentTrackNumber)
                                ->amount(round($paymentLog->final_amount,2))
                                ->getRedirectUrl($nagadHost);
    
    
            if ( $redirectUrl) {return redirect()->away( $redirectUrl);}
    
            return redirect()->route('home')->with('error',  translate("Invalid Request"));
        } catch (\Exception $ex) {
            return back()->with('error',$ex->getMessage());
        }


      

    }




    public function callBack(Request $request ,$trx_code)
    {

        $paymentLog = PaymentLog::with(['user','order','seller','paymentGateway'])
                             ->where('status', PaymentLog::PENDING)
                             ->where('trx_number', $trx_code)
                             ->first();

        if(!$paymentLog || !$this->config($paymentLog->trx_number)){
            abort(404);
        }

        if($request->status && $request->status == "Success"){

            if($paymentLog->type == PaymentType::ORDER->value ){
                    PaymentInsert::paymentUpdate($paymentLog->trx_number);
                    Order::where('id',$paymentLog->order_id)->update([
                        'payment_info'=>  json_encode(request()->all())
                    ]);
            }
            else{
                $response = WalletRecharge::walletUpdate($paymentLog);
            }
            return $this->paymentResponse($request,$paymentLog ,true );

        }

        return $this->paymentResponse($request,$paymentLog);

    }


    

    public function tnxID($id,$status=false)
    {
        $this->tnxID = $id;
        $this->tnxStatus = $status;
        return $this;
    }


    public function amount($amount)
    {
        $this->amount = $amount;
        return $this;
    }


    public function getRedirectUrl($nagadHost)
    {

        $DateTime = Date('YmdHis');
        $MerchantID = config('nagad.merchant_id');
        $invoiceNo =  $this->tnxStatus ? rand(000000,999999) :'Inv'.Date('YmdH').rand(1000, 10000);
        $merchantCallbackURL = config('nagad.callback_url');
        

        $SensitiveData = [
            'merchantId' => config('nagad.merchant_id'),
            'datetime' => $DateTime,
            'orderId' => $invoiceNo,
            'challenge' =>$this->generateRandomString()
        ];
        

        $PostData = array(
            'accountNumber' =>config('nagad.merchant_number'),
            'dateTime' => $DateTime,
            'sensitiveData' =>$this->EncryptDataWithPublicKey(json_encode($SensitiveData)),
            'signature' =>$this->SignatureGenerate(json_encode($SensitiveData))
        );

        $initializeUrl = $nagadHost."/check-out/initialize/".$MerchantID."/" . $invoiceNo;
  

        $Result_Data =$this->HttpPostMethod($initializeUrl,$PostData);

    
        if (isset($Result_Data['sensitiveData']) && isset($Result_Data['signature'])) {
            if ($Result_Data['sensitiveData'] != "" && $Result_Data['signature'] != "") {

                $PlainResponse = json_decode($this->DecryptDataWithPrivateKey($Result_Data['sensitiveData']), true);

                if (isset($PlainResponse['paymentReferenceId']) && isset($PlainResponse['challenge'])) {

                    $paymentReferenceId = $PlainResponse['paymentReferenceId'];
                    $randomserver = $PlainResponse['challenge'];

                    $SensitiveDataOrder = array(
                        'merchantId' =>  config('nagad.merchant_id'),
                        'orderId' => $invoiceNo,
                        'currencyCode' => '050',
                        'amount' => $this->amount,
                        'challenge' => $randomserver
                    );


                    if($this->tnxID !== ''){
                        $this->merchantAdditionalInfo['tnx_id'] =  $this->tnxID;
                    }

                    $PostDataOrder = array(
                        'sensitiveData' =>$this->EncryptDataWithPublicKey(json_encode($SensitiveDataOrder)),
                        'signature' =>$this->SignatureGenerate(json_encode($SensitiveDataOrder)),
                        'merchantCallbackURL' => $merchantCallbackURL,
                        'additionalMerchantInfo' => (object)$this->merchantAdditionalInfo
                    );

                    $OrderSubmitUrl = $nagadHost."/check-out/complete/" . $paymentReferenceId;
                    $Result_Data_Order =$this->HttpPostMethod($OrderSubmitUrl, $PostDataOrder);
                        if ($Result_Data_Order['status'] == "Success") {
                            $callBackUrl = ($Result_Data_Order['callBackUrl']);
                            return $callBackUrl;
                        }
                        else {
                            echo json_encode($Result_Data_Order);
                        }
                } else {
                    echo json_encode($PlainResponse);
                }
            }
        }
        else{
            return null;
        }

    }


    public  function generateRandomString($length = 40)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Generate public key
     */
    public  function EncryptDataWithPublicKey($data)
    {
        $pgPublicKey = config('nagad.public_key');
  
        $public_key = "-----BEGIN PUBLIC KEY-----\n" . $pgPublicKey . "\n-----END PUBLIC KEY-----";
 
        $key_resource = openssl_get_publickey($public_key);
        openssl_public_encrypt($data, $crypttext, $key_resource);
        return base64_encode($crypttext);
    }

    /**
     * Generate signature
     */
    public  function SignatureGenerate($data)
    {
        $merchantPrivateKey = config('nagad.private_key');
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";

        openssl_sign($data, $signature, $private_key, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    /**
     * get clinet ip
     */
    public  function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public  function DecryptDataWithPrivateKey($crypttext)
    {
        $merchantPrivateKey = config('nagad.private_key');
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey. "\n-----END RSA PRIVATE KEY-----";
        openssl_private_decrypt(base64_decode($crypttext), $plain_text, $private_key);
        return $plain_text;
    }

    public  function HttpPostMethod($PostURL, $PostData)
    {
        $url = curl_init($PostURL);
        
      
        $posttoken = json_encode($PostData);
        $header = array(
            'Content-Type:application/json',
            'X-KM-Api-Version:v-0.2.0',
            'X-KM-IP-V4:' . $this->get_client_ip(),
            'X-KM-Client-Type:PC_WEB'
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $posttoken);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($url, CURLOPT_SSL_VERIFYPEER, 0);
        $resultdata = curl_exec($url);
        $curl_error = curl_error($url);
        
   
      
        if (!empty($curl_error)) {
            return [
                'error' => $curl_error
            ];
        }
        
   
        $ResultArray = json_decode($resultdata, true);

        curl_close($url);
        return $ResultArray;

    }

    public  function HttpGet($url)
    {
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/0 (Windows; U; Windows NT 0; zh-CN; rv:3)");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $file_contents = curl_exec($ch);
        echo curl_error($ch);
        curl_close($ch);
        return $file_contents;
    }
   

}
