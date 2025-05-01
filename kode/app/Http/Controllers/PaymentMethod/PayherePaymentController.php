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
class PayherePaymentController extends Controller
{
  

    public $paymentMethod;
    public function __construct(){
        $this->paymentMethod = PaymentMethod::with(['currency'])
                                                ->where('unique_code', 'PAYHERE101')
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



                $siteName = site_settings('site_name');
                $gateway           = $this->paymentMethod->payment_parameter; 
                
 
                if($paymentLog->type == PaymentType::WALLET->value ){

                    if($paymentLog->seller){
                        $seller     = @$paymentLog->seller;
                        $email      = $seller->email;
                        $phone      = $seller->phone;
                        $first_name = $seller->name;
                        $last_name  = $seller->name;
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
                        $last_name  = $user->name;
                        $address    = @$user->address->address;
                        $zip        = @$user->address->zip;
                        $city       = @$user->address->city;
                        $state      = @$user->address->zip;
                    }


                }else{
                        $order =  @$paymentLog->order;

                        $phone      = @$order->billingAddress ? @$order->billingAddress->phone : @$order->billing_information->phone;
                        $email      = @$order->billingAddress ? @$order->billingAddress->email : @$order->billing_information->email;
                        $first_name = @$order->billingAddress ? @$order->billingAddress->first_name : @$order->billing_information->first_name;
                        $last_name  = @$order->billingAddress ? @$order->billingAddress->last_name : @$order->billing_information->last_name;
                        $address    = @$order->billingAddress ? @$order->billingAddress->address->address : @$order->billing_information->address;
                        $country    = @$order->billingAddress ?  @$order->billingAddress->country->name : @$order->billing_information->country ;
                        $zip        = @$order->billingAddress ?  @$order->billingAddress->zip : @$order->billing_information->zip ;
                        $city       = @$order->billingAddress ?  @$order->billingAddress->city->name : @$order->billing_information->city ;
                        $state      = @$order->billingAddress ?  @$order->billingAddress->state->name : @$order->billing_information->state ;
                }


                $send['method']      = 'post';
                $send['url']         = 'https://www.payhere.lk/pay/checkout';


                $send['val'] = [
                    'hash'              => strtoupper(
                                                        md5(
                                                            env('PAYHERE_MERCHANT_ID').
                                                            $paymentTrackNumber.
                                                            round($paymentLog->final_amount, 2).
                                                            $this->paymentMethod->currency->name.
                                                            strtoupper(md5($gateway->secret_key)) 
                                                        )
                                                    ),
                    'country'            => "Sri Lanka",
                    'city'               => @$city,
                    'address'            => @$address,
                    'phone'              => @$phone,
                    'email'              => @$email,
                    'last_name'          => @$last_name,
                    'first_name'         => @$first_name,
                    'amount'             => @round($paymentLog->final_amount,2),
                    'currency'           => $this->paymentMethod->currency->name,
                    'items'              =>'Checkout Payment',
                    'order_id'           => $paymentTrackNumber,
                    'custom_1'           => $paymentTrackNumber,
                    'custom_2'           => '',
                    'merchant_id'        => $gateway->merchant_id,
                    'return_url'         => route('payhere.return'),
                    'notify_url'         => route('payhere.callback',["trx_code" => $paymentLog->trx_number,"type" => 'success']),
                    'cancel_url'         => route('payhere.callback',["trx_code" => $paymentLog->trx_number,"type" => 'failed'])

                ];

                return view('user.payment.redirect',[
                    'title'         => translate('Payment with Payumoney'),
                    'paymentMethod' => $this->paymentMethod,
                    'paymentLog'    => $paymentLog,
                    'data'          => (object) $send
                ]);

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

        $gateway           = $this->paymentMethod->payment_parameter; 

        if($type == 'success'){
            
            $merchant_id      = $_POST['merchant_id'];
            $order_id         = $_POST['order_id'];
            $payhere_amount   = $_POST['payhere_amount'];
            $payhere_currency = $_POST['payhere_currency'];
            $status_code      = $_POST['status_code'];
            $md5sig           = $_POST['md5sig'];

            $local_md5sig = strtoupper(md5($merchant_id . $order_id . $payhere_amount . $payhere_currency . $status_code . strtoupper(md5($gateway->secret_key))));
    
            if (($local_md5sig === $md5sig) and ($status_code == 2)) {

                if($paymentLog->type == PaymentType::ORDER->value ){

                    PaymentInsert::paymentUpdate($paymentLog->trx_number);
                    Order::where('id',$paymentLog->order_id)->update([
                        'payment_info'=>  json_encode($request->all())
                    ]);
                }else{
                    $response = WalletRecharge::walletUpdate($paymentLog);
                }


                return $this->paymentResponse($request,$paymentLog ,true);
            }
        }

        return $this->paymentResponse($request,$paymentLog);

    }


    public function paymentReturn(){

        if(request()->expectsJson())  return json_encode([
                                                            "status"  => true,
                                                            "message" => translate('Payment process completed')
                                                        ]);



        return redirect()->route('home')->with('success',translate('Payment process completed'));
    }

}
