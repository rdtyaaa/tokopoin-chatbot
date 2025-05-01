<?php

namespace App\Http\Controllers\PaymentMethod;

use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentLog;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Utility\PaymentInsert;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Models\Order;

class WebxpayPaymentController extends Controller
{



    public $paymentMethod;
    public function __construct(){

        $this->paymentMethod = PaymentMethod::with(['currency'])
                                                ->where('unique_code', 'WEBXPAY109')
                                                ->first();

    }

    public function payment($trx_code = null)
    {



        try {
            
            $paymentTrackNumber = $trx_code ?? session()->get('payment_track');
            $paymentLog         = PaymentLog::with(['user','order','seller'])
                                                            ->where('status', PaymentLog::PENDING)
                                                            ->where('trx_number', $paymentTrackNumber)
                                                            ->first();

          
                if(!$paymentLog || !$this->paymentMethod)  return redirect()->route('home')
                ->with('error', translate('Invalaid Transaction'));
                 
                 $gwCredential = $this->paymentMethod->payment_parameter;



                 if($paymentLog->type == PaymentType::WALLET->value ){

                    if($paymentLog->seller){
                        $seller     = @$paymentLog->seller;
                        $email      = $seller->email;
                        $first_name = $seller->name;
                        $phone      = $seller->phone;
                        $address    = @$seller->address->address;
                        $zip        = @$seller->address->zip;
                        $city       = @$seller->address->city;
                        $state      = @$seller->address->state;
                    }
    
                    if($paymentLog->user){
                        $user     = @$paymentLog->user;
                        $email      = $user->email;
                        $phone      = $user->phone;
                        $first_name = $user->name;
                        $address    = @$user->address->address;
                        $zip        = @$user->address->zip;
                        $city       = @$user->address->city;
                        $state      = @$user->address->state;
                    }
      
      
                 }
                 else{
    
                    $order      = @$paymentLog->order;

                    $email      = @$order->billingAddress 
                                            ? @$order->billingAddress->email 
                                            : @$order->billing_information->email;


                    $phone      = @$order->billingAddress 
                                        ? @$order->billingAddress->phone 
                                        : @$order->billing_information->phone;

                    $first_name = @$order->billingAddress 
                                            ? @$order->billingAddress->first_name 
                                            : @$order->billing_information->first_name;
                    $address    = @$order->billingAddress 
                                            ? @$order->billingAddress->address->address 
                                            : @$order->billing_information->address;
                    $zip        = @$order->billingAddress 
                                            ? @$order->billingAddress->zip 
                                            : @$order->billing_information->zip ;
                    $city       = @$order->billingAddress 
                                            ? @$order->billingAddress->city->name 
                                            : @$order->billing_information->city ;
                    $state      = @$order->billingAddress 
                                            ? @$order->billingAddress->state->name 
                                            : @$order->billing_information->state ;
    
                 }
    


                
                 $baseURL      = $gwCredential->is_sandbox == 1 
                                           ? "https://stagingxpay.info/index.php?route=checkout/billing" 
                                           : "https://webxpay.com/index.php?route=checkout/billing" ;
                                           
                                           
                $custom_fields =  [
                    	'trx_id'   => $paymentTrackNumber,
                ];
                
                $custom_fields = implode('|', array: $custom_fields);
                $plaintext = $paymentTrackNumber."|".round($paymentLog->final_amount,2);
                $publickey = $gwCredential->public_key;

                openssl_public_encrypt($plaintext, $encrypt, $publickey);
                $payment = base64_encode($encrypt);
                $custom_fields = base64_encode($custom_fields);
                
            
                $send['first_name']       = $first_name;
                $send['last_name']        = $first_name;
                $send['email']        = $email;
                $send['contact_number']        = $phone ;
                $send['address_line_one']        = $address;
                $send['address_line_two']        = $address;
                $send['city']        = $city;
                $send['cms']        = "PHP";
                $send['method']        = "POST";
                $send['base_url']        =  $baseURL;
                $send['process_currency']        =  $this->paymentMethod->currency->name;
                $send['custom_fields']        =  $custom_fields ;
                $send['secret_key']        =  $gwCredential->secret_key;
                $send['enc_method']        =  "JCs3J+6oSz4V0LgE0zi/Bg==";
                $send['payment']        =  $payment;

                return view('user.payment.webxpay',[
                    'title' => translate('Payment with Webxpay'),
                    'paymentMethod' =>  $this->paymentMethod,
                    'paymentLog' => $paymentLog,
                    'data'       => (object) $send
                ]);




        } catch (\Exception $ex) {
            return back()->with('error',$ex->getMessage());
        }

     

    }


    public function callBack(Request $request ,$trx_code = null,$type = null)
    {
        
            if(!$this->paymentMethod) abort(404);
            
            $gwCredential  = $this->paymentMethod->payment_parameter;
            $payment       = base64_decode($_POST ["payment"]);
            $signature     = base64_decode($_POST ["signature"]);
            $custom_fields = base64_decode($_POST ["custom_fields"]);

            $publickey = $gwCredential->public_key;
            openssl_public_decrypt($signature, $value, $publickey);
            
            $signature_status = false ;
            
            if($value == $payment) $signature_status = true ;
            
            $responseVariables = explode('|', $payment);      
            
            if($signature_status == true){

            	$custom_fields_varible = explode('|', $custom_fields);
            	$trxNumber             = @$custom_fields_varible[0] ?? null;

    	        $paymentLog            = PaymentLog::with(['user','order','seller','paymentGateway'])
                                                        ->where('status', PaymentLog::PENDING)
                                                        ->where('trx_number', 	$trxNumber )
                                                        ->first();
                if(!$paymentLog) abort(404);

                if($paymentLog->type == PaymentType::ORDER->value ){
                    PaymentInsert::paymentUpdate(trx: $paymentLog->trx_number);
                    Order::where('id',$paymentLog->order_id)->update([
                        'payment_info'=>  json_encode($request->all())
                    ]);
                }
                else{
                    $response = WalletRecharge::walletUpdate($paymentLog);
                }
                
                return $this->paymentResponse($request,$paymentLog ,true );

            }

            return redirect()->route('home')->with('error',translate('Invalid transaction'));


    }
}
