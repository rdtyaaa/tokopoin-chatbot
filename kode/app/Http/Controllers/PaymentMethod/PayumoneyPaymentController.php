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
class PayumoneyPaymentController extends Controller
{
  

    public $paymentMethod;



    public function __construct(){
        $this->paymentMethod = PaymentMethod::with(['currency'])
                                                ->where('unique_code', 'PAYU101')
                                                ->first();

    }

    public function payment($trx_code = null)
    {
 

        try {


                $paymentTrackNumber = $trx_code ?? session()->get('payment_track');
                
                $paymentLog = PaymentLog::with(['user','order','seller'])
                                              ->where('status', PaymentLog::PENDING)
                                              ->where('trx_number', $paymentTrackNumber)
                                              ->first();

                if(!$paymentLog || !$this->paymentMethod)  return redirect()->route('home')
                                                                        ->with('error', translate('Invalaid Transaction'));

                $gateway           = $this->paymentMethod->payment_parameter;                                                     


                if($paymentLog->type == PaymentType::WALLET->value ){

                        if($paymentLog->seller){
                            $seller     = @$paymentLog->seller;
                            $email      = $seller->email;
                            $first_name = $seller->name;
                
                        }
                        elseif($paymentLog->user){
                            $user     = @$paymentLog->user;
                            $email      = $user->email;
                            $first_name = $user->name;
                        }
        
                }
                else{
    
                    $order =  @$paymentLog->order;

                    $email      = @$order->billingAddress ? @$order->billingAddress->email : @$order->billing_information->email;
                    $first_name = @$order->billingAddress ? @$order->billingAddress->first_name : @$order->billing_information->first_name;
    
                 }

                $hashSequence      = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";

                $hashVarsSeq       = explode('|', $hashSequence);
                $hash_string       = '';
                $send['val'] = [
                    'key'              => $gateway->merchant_key ?? '',
                    'txnid'            => $paymentLog->trx_number,
                    'amount'           => round($paymentLog->final_amount,2),
                    'firstname'        => @$first_name ,
                    'email'            => @$email,
                    'productinfo'      => $paymentLog->trx_number ?? 'Order',
                    'surl'             => route('payumoney.callback',["trx_code" => $paymentLog->trx_number,"type" => 'success']),
                    'furl'             => route('payumoney.callback',["trx_code" => $paymentLog->trx_number,"type" => 'failed']),
                    'service_provider' => site_settings('site_name'),
                ];
        

                foreach ($hashVarsSeq as $hash_var) {
                    $hash_string .= $send['val'][$hash_var] ?? '';
                    $hash_string .= '|';
                }

                $hash_string .= $gateway->salt;

                $send['val']['hash'] = strtolower(hash('sha512', $hash_string));


                $send['view']        = 'user.payment.redirect';
                $send['method']      = 'post';
                $send['url']         = 'https://secure.payu.in/_payment';

                
                return view('user.payment.redirect',[
                    'title'         => translate('Payment with Payumoney'),
                    'paymentMethod' =>  $this->paymentMethod,
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

        $gateway           = $this->paymentMethod->payment_parameter;       
        if(!$paymentLog || !$this->paymentMethod) abort(404);

        if($type == 'success'){
            if ($gateway->merchant_key   == $request->key && $trx_code == $request->txnid ) {

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
        }
       
        return $this->paymentResponse($request,$paymentLog);

    }

}
