<?php

namespace App\Http\Controllers\PaymentMethod;

use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PaymentMethod;
use App\Models\PaymentLog;
use App\Http\Utility\PaymentInsert;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Models\GeneralSetting;
use App\Models\Order;
use Session;


require_once('stripe-php/init.php');


class StripePaymentController extends Controller
{
  
    public $paymentMethod;
    public function __construct(){

        $this->paymentMethod = PaymentMethod::with(['currency'])->where('unique_code', 'STRIPE101')->first();
    }


    public function payment($trx_code = null)
    {
     
        $paymentTrackNumber = $trx_code ?? session()->get('payment_track');
        $paymentLog         = PaymentLog::with(['user','order','seller'])->where('status', PaymentLog::PENDING)->where('trx_number', $paymentTrackNumber)->first();

        if(!$paymentLog || !$this->paymentMethod) return redirect()->route('home')->with('error', translate('Invalaid Transaction'));


  
        $basic = GeneralSetting::first();
        
        \Stripe\Stripe::setApiKey($this->paymentMethod->payment_parameter->secret_key);
        
          try {
              
             if($paymentLog->type == PaymentType::WALLET->value ){

                if($paymentLog->seller){
                    $seller     = @$paymentLog->seller;
                    $email      = $seller->email;
                    $first_name = $seller->name;
                    $address    = @$seller->address->address;
                    $zip        = @$seller->address->zip;
                    $city       = @$seller->address->city;
                    $state      = @$seller->address->state;
                }

                if($paymentLog->user){
                    $user     = @$paymentLog->user;
                    $email      = $user->email;
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
    
              $address = array(
        
                   'email' => @$email,
                   'name'  =>  @$first_name,
                   'address' => [
                        'line1'       => @$address,
                        'postal_code' =>  @$zip,
                        'city'        => @$city,
                        'state'       =>  @$state,
                        'country'     => 'US',
                    ],
               
            );
       
            $customer = \Stripe\Customer::create($address);
      
            $response = \Stripe\Checkout\Session::create([
                'customer'   => $customer->id,
                'line_items' => [
                    [
                
                    'price_data' => [
                      'currency' => $this->paymentMethod->currency->name,
                      'unit_amount' => round($paymentLog->final_amount,2) * 100,
                        'product_data' => [
                           'name' => $basic->site_name,
                      ],
                    ],
     
                    'quantity' => 1,
                  ]
                
                ],
                'mode' => 'payment',
                'cancel_url'   => route('stripe.callback',["trx_code" => $paymentTrackNumber,"type"=>'failed']),
                'success_url'  => route('stripe.callback',["trx_code" => $paymentTrackNumber,"type"=>'success']),

            ]);
            
         
     
          if(isset( $response->id)){
              session()->put('payment_id',$response->id);
              return redirect($response->url);
          }

        return back()->with('error',translate('Invalid payload or secret key in stripe session'));
        } catch (\Exception $e) {
            return back()->with('error',$e->getMessage());
        }

    
    }


    public function callBack(Request $request ,$trx_code ,$type = null)
    {

        $paymentLog = PaymentLog::with(['user','order','seller','paymentGateway'])
                          ->where('status', PaymentLog::PENDING)
                          ->where('trx_number', $trx_code)
                          ->first();
                          

        if(!$paymentLog || !$this->paymentMethod) abort(404);
    
   
        if ($type == 'success') {
            
            $paymentID =       $request->input('payment_id',session()->get('payment_id'))  ;
            $stripe = new \Stripe\StripeClient($this->paymentMethod->payment_parameter->secret_key);
            $session =  $stripe->checkout->sessions->retrieve($paymentID, []);
            
            if($session->object == 'checkout.session' &&  $session->payment_status == 'paid'){

                if($paymentLog->type == PaymentType::ORDER->value ){
                    PaymentInsert::paymentUpdate(trx: $paymentLog->trx_number);
                    Order::where('id',$paymentLog->order_id)->update([
                        'payment_info'=>  json_encode($request->all())
                    ]);
                }
                else{
                    $response = WalletRecharge::walletUpdate($paymentLog);
                }
                session()->forget('payment_id');
                return $this->paymentResponse($request,$paymentLog ,true );
                
            }

            return $this->paymentResponse($request,$paymentLog);

        }

        return $this->paymentResponse($request,$paymentLog);


    }

}
