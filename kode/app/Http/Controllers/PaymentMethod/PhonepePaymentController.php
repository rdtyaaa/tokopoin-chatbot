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
class PhonepePaymentController extends Controller
{
  

    public $paymentMethod;



    public function __construct(){
        $this->paymentMethod = PaymentMethod::with(['currency'])
                                                ->where('unique_code', 'PHONEPE102')
                                                ->first();

    }

    public function payment($trx_code = null)
    {
 

        try {

                $paymentTrackNumber = $trx_code ?? session()->get('payment_track');
                
                $paymentLog = PaymentLog::with(['paymentGateway','seller','user','order'])
                                              ->where('status', PaymentLog::PENDING)
                                              ->where('trx_number', $paymentTrackNumber)
                                              ->first();

                if(!$paymentLog || !$this->paymentMethod)  return redirect()->route('home')
                                                                        ->with('error', translate('Invalaid Transaction'));

                $gateway  =  $this->paymentMethod->payment_parameter;                                                     


  
                if($paymentLog->type == PaymentType::WALLET->value ){

                    if($paymentLog->seller){
                        $seller     = @$paymentLog->seller;
                        $phone      = $seller->phone;
                    }
                    elseif($paymentLog->user){
                        $user     = @$paymentLog->user;
                        $phone      = $user->phone;
                    }

                }else{

                    $order =  @$paymentLog->order;
                    $phone      = @$order->billingAddress ? @$order->billingAddress->phone : @$order->billing_information->phone;

                }




                $salt_key   = $gateway->salt_key;
                $salt_index = $gateway->salt_index;


        
  
                $baseURL = "https://api.phonepe.com/apis/hermes/pg/v1/pay";

                $metaData = [
                    'merchantId' => $gateway->merchant_id,
                    'merchantTransactionId' => $paymentTrackNumber,
                    'merchantUserId' => $paymentLog->user? $paymentLog->user->id : $paymentTrackNumber ,
                    'amount' =>  round($paymentLog->final_amount,2) * 100,
                    'redirectUrl' =>  route('phonepe.return'),
                    'redirectMode' => 'POST',
                    'callbackUrl' =>  route('phonepe.callback',["trx_code" => $paymentLog->trx_number]),
                    'mobileNumber' =>  @$phone,
                    "paymentInstrument" => [
                        "type" => "PAY_PAGE"
                    ],
                ];

                $encriptedPayload = base64_encode(json_encode($metaData));

                $hashedkey =  hash('sha256', $encriptedPayload . "/pg/v1/pay" . $salt_key) . '###' . $salt_index;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $baseURL);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'X-VERIFY: ' . $hashedkey . '',
                    'accept: application/json',
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "\n{\n  \"request\": \"$encriptedPayload\"\n}\n");
        
                $response = curl_exec($ch);
                $result = (json_decode($response));

   

                if(@$result->data && @$result->data->instrumentResponse && @$result->data->instrumentResponse->redirectInfo->url){
                    return redirect()->away($result->data->instrumentResponse->redirectInfo->url);
                }


                return back()->with('error',translate('Invalid payment credential'));



        } catch (\Exception $ex) {
            return back()->with('error',$ex->getMessage());
        }

    }


    public function callBack(Request $request ,$trx_code ,$type = null)
    {


        $paymentLog = PaymentLog::with(['paymentGateway','seller','user','order'])
                                    ->where('status', PaymentLog::PENDING)
                                    ->where('trx_number', $trx_code)
                                    ->first();

        if(!$paymentLog || !$this->paymentMethod) abort(404);

        $requestResponse      = $request->all();
        $response             = $requestResponse['response'];

        $response             = json_decode(base64_decode($response));

        if ($response->code  == 'PAYMENT_SUCCESS') {

            if($paymentLog->type == PaymentType::ORDER->value ){
                PaymentInsert::paymentUpdate($paymentLog->trx_number);
                Order::where('id',$paymentLog->order_id)->update([
                    'payment_info'=>  json_encode($request->all())
                ]);
            }else{
                $response = WalletRecharge::walletUpdate($paymentLog);
            }

            return $this->paymentResponse($request,$paymentLog,true);
        }

        return $this->paymentResponse($request,$paymentLog);

    }


    
    public function paymentReturn(Request $request){

        if ($request['code'] == 'PAYMENT_SUCCESS') {
            return redirect()->route('home')->with('success',translate('Payment process completed'));
        }

        return redirect()->route('home')->with('error',translate('Payment process failed'));
    }

}
