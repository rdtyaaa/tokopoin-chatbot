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
class SenangpayPaymentController extends Controller
{
  

    public $paymentMethod;
    public function __construct(){
        $this->paymentMethod = PaymentMethod::with(['currency'])
                                    ->where('unique_code', 'SENANGPAY107')
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

                $siteName = site_settings('site_name');
                $gateway           = $this->paymentMethod->payment_parameter; 
                
                if($paymentLog->type == PaymentType::WALLET->value ){

                    if($paymentLog->seller){
                        $seller     = @$paymentLog->seller;
                        $phone      = $seller->phone;
                        $email      = $seller->email;
                        $first_name      = $seller->first_name;
                    }
                    if($paymentLog->user){
                        $user     = @$paymentLog->user;
                        $phone      = $user->phone;
                        $email      = $user->email;
                        $first_name = $user->name;
                    }

                }
                else{

                
                    $order      =  @$paymentLog->order;

                    $phone      = @$order->billingAddress ? @$order->billingAddress->phone : @$order->billing_information->phone;
                    $email      = @$order->billingAddress ? @$order->billingAddress->email : @$order->billing_information->email;
                    $first_name = @$order->billingAddress ? @$order->billingAddress->first_name : @$order->billing_information->first_name;
                }
           

                $send['method']      = 'post';
                $send['url']         = 'https://app.senangpay.my/payment/'. $gateway->merchant_id ;


                $send['val'] = [
                  
                    'amount'   => round($paymentLog->final_amount , 2),
                    'name'     => @$first_name,
                    'email'    => @$email ,
                    'phone'    => @$phone,
                    'hash'     => md5($gateway->secret_key . urldecode(round($paymentLog->final_amount , 2))),
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


        if ($request['status_id'] == 1) {


            if($paymentLog->type == PaymentType::ORDER->value ){
                PaymentInsert::paymentUpdate($paymentLog->trx_number);
                Order::where('id',$paymentLog->order_id)->update([
                    'payment_info'=>  json_encode($request->all())
                ]);
            }
            else{
                $response = WalletRecharge::walletUpdate($paymentLog);
            }


            return $this->paymentResponse($request,$paymentLog ,true);
        }

        return $this->paymentResponse($request,$paymentLog);

    }




}
