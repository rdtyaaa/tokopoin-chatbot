<?php

namespace App\Http\Controllers\PaymentMethod;

use App\Enums\PaymentType;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Utility\PaymentInsert;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\PaymentLog;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
class NgeniusPaymentController extends Controller
{
  

    public $paymentMethod;



    public function __construct(){
        $this->paymentMethod = PaymentMethod::with(['currency'])
                                                ->where('unique_code', 'NGENIUS111')
                                                ->first();

    }

    public function payment($trx_code = null)
    {
 

        try {

                $paymentTrackNumber =  $trx_code ?? session()->get('payment_track');
                
                $paymentLog = PaymentLog::with(['paymentGateway','seller','user','order'])
                                              ->where('status', PaymentLog::PENDING)
                                              ->where('trx_number', $paymentTrackNumber)
                                              ->first();

                if(!$paymentLog || !$this->paymentMethod)  return redirect()->route('home')
                                                                            ->with('error', translate('Invalaid Transaction'));
                $billingInfo =  @$paymentLog->order->billing_information;


                $gateway           = $this->paymentMethod->payment_parameter; 

                $order = new \StdClass();

                $order->action = "SALE";                                        
                $order->amount = new \stdClass();
                $order->amount->currencyCode = $this->paymentMethod->currency->name; //AED                          
                $order->amount->value =round($paymentLog->amount * 100);                                 
                $order->language = "en";                                       
                $order->merchantOrderReference = time();                                        
                $order->merchantAttributes = new \stdClass();
                $order->merchantAttributes->redirectUrl =  route('ngenius.callback',["trx_code" => $paymentLog->trx_number]);

                $order = json_encode($order);


                $outletRef = $gateway->outlet_id;
                $txnServiceURL = $this->getUrl('gateway') . "/transactions/outlets/$outletRef/orders";             
                $access_token = $this->accessToken();

                $orderCreateHeaders = array("Authorization: Bearer " . $access_token, "Content-Type: application/vnd.ni-payment.v2+json", "Accept: application/vnd.ni-payment.v2+json");
                $orderCreateResponse = $this->invokeCurlRequest("POST", $txnServiceURL, $orderCreateHeaders, $order);

                $orderCreateResponse = json_decode($orderCreateResponse);

                $paymentLink    = @$orderCreateResponse?->_links->payment->href;     
                $orderReference = @$orderCreateResponse->reference;   
                         
                session()->save();

                if(@$paymentLink){
                    return redirect($paymentLink);
                }

               return back()->with('error',translate('Invalid payment credentials'));

        } catch (\Exception $ex) {
            return back()->with('error',$ex->getMessage());
        }

    }


    
    public  function accessToken()
    {
        $apikey = $this->paymentMethod->payment_parameter->api_key; 

        $idServiceURL = $this->getURL('identity'); 
              
        $tokenHeaders = array("Authorization: Basic $apikey", "Content-Type: application/x-www-form-urlencoded");
        $tokenResponse = $this->invokeCurlRequest("POST", $idServiceURL, $tokenHeaders, http_build_query(array('grant_type' => 'client_credentials')));
        $tokenResponse = json_decode($tokenResponse);
        $access_token = @$tokenResponse?->access_token;
        return $access_token;
    }



    public  function getURL($key)
    {
        $url['identity'] = "https://identity-uat.ngenius-payments.com/auth/realms/ni/protocol/openid-connect/token";
        $url['gateway']  = "https://api-gateway-uat.ngenius-payments.com";
        return $url[$key];
    }


    public  function invokeCurlRequest($type, $url, $headers, $post)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($type == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }

        $server_output = curl_exec($ch);

        curl_close($ch);

        return $server_output;

    }


    public function callBack(Request $request ,$trx_code ,$type = null)
    {


        $paymentLog = PaymentLog::with(['paymentGateway','seller','user','order'])->where('status', PaymentLog::PENDING)
                                    ->where('trx_number', $trx_code)
                                    ->first();


        if(!$paymentLog || !$this->paymentMethod) abort(404);



        try {
            if (request()->has('ref')) {

                $ref = request()->has('ref');
    
                $outletRef     = $this->paymentMethod->payment_parameter->outlet_id; 
                $orderCheckURL = $this->getUrl('gateway') . "/transactions/outlets/$outletRef/orders/$ref";            
                $access_token  = $this->accessToken();
        
                $headers             = array("Authorization: Bearer " . $access_token);
                $orderStatusResponse = $this->invokeCurlRequest("GET", $orderCheckURL, $headers, null);
        
                $orderStatusResponse = json_decode($orderStatusResponse);
    
                if(@$orderStatusResponse->_embedded->payment[0]->state == "CAPTURED") {


                    if($paymentLog->type == PaymentType::ORDER->value ){
                        PaymentInsert::paymentUpdate($paymentLog->trx_number);
                        Order::where('id',$paymentLog->order_id)->update([
                            'payment_info'=>  json_encode($request->all())
                        ]);
                    }
                    else{
                        $response = WalletRecharge::walletUpdate($paymentLog);
                    }

                    return $this->paymentResponse($request,$paymentLog ,true );
        
                }
    
                
            }
        } catch (\Throwable $th) {
            
        }

    
        return $this->paymentResponse($request,$paymentLog);
    }

}
