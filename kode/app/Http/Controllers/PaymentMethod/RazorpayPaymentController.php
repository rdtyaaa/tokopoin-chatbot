<?php

namespace App\Http\Controllers\PaymentMethod;

use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\PaymentMethod;
use App\Models\PaymentLog;
use App\Http\Utility\PaymentInsert;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Models\GeneralSetting;
use App\Models\Order;
require_once('razorpay-php/Razorpay.php');
class RazorpayPaymentController extends Controller
{
    public $paymentMethod;
    public function __construct(){
        $this->paymentMethod = PaymentMethod::with(['currency'])->where('unique_code', 'RAZORPAY106')->first();

    }


    public function payment($trx_code = null)
    {

        try {
            $paymentTrackNumber = $trx_code ??  session()->get('payment_track');
            $paymentLog         = PaymentLog::with(['user','order','seller'])
                                       ->where('status', PaymentLog::PENDING)
                                       ->where('trx_number', $paymentTrackNumber)
                                       ->first();
    
            if(!$paymentLog || !$this->paymentMethod){
                return redirect()->route('home')->with('error', translate('Invalaid Transaction'));
            }
    
            
            $api_key          = $this->paymentMethod->payment_parameter->razorpay_key ?? '';
            $api_secret       = $this->paymentMethod->payment_parameter->razorpay_secret ?? '';
            $razorPayApi      = new Api($api_key, $api_secret);
    
            $finalAmount      = round($paymentLog->final_amount * 100, 2);
            $gatewayCurrency  = $this->paymentMethod->currency->name;
    
            $trx =  $paymentTrackNumber ;
            $razorOrder = $razorPayApi->order->create(
                array(
                    'receipt'         => $trx,
                    'amount'          => $finalAmount,
                    'currency'        => $gatewayCurrency,
                    'payment_capture' => '0'
                )
            );
    

            $val['key']             = $api_key;
            $val['amount']          = $finalAmount;
            $val['currency']        = $gatewayCurrency;
            $val['order_id']        = $razorOrder['id'];
            $val['buttontext']      = "Payment via Razorpay";
            $val['name']            = $paymentLog->seller ? $paymentLog->seller->name : optional($paymentLog->user)->username;
            $val['description']     = "Payment By Razorpay";
            $val['image']           = show_image('assets/images/backend/AdminLogoIcon/'.site_settings('admin_logo_sm'));
            $val['prefill.name']    = $paymentLog->seller ? $paymentLog->seller->name : optional($paymentLog->user)->username;
            $val['prefill.email']   = $paymentLog->seller ? $paymentLog->seller->email : optional($paymentLog->user)->email;
            $val['prefill.contact'] = $paymentLog->seller ? $paymentLog->seller->phone : optional($paymentLog->user)->phone;
            $val['theme.color']     = "#2ecc71";
            $send['val']            = $val;
    
            $send['method']       = 'POST';
            $send['url']          = route('razorpay.callback',[$trx]);
            $send['custom']       = $trx;
            $send['checkout_js']  = "https://checkout.razorpay.com/v1/checkout.js";
            $send['view']         = 'user.payment.razorpay';
    
            $view =  'user.payment.razorpay';
            if($paymentLog->seller) $view =  'seller.deposit.razorpay';
            return view($view,[
                'title' => translate('Payment with Razorpay'),
                'paymentMethod' =>  $this->paymentMethod,
                'paymentLog' => $paymentLog,
                'data'       => (object) $send
            ]);
        } catch (\Exception $ex) {
           return back()->with("error",translate($ex->getMessage()));
        }
       

    }


    public function callBack(Request $request ,$trx_code ,$type = null)
    {

        $paymentLog = PaymentLog::with(['user','order','seller','paymentGateway'])
                            ->where('status', PaymentLog::PENDING)
                            ->where('trx_number', $trx_code)
                            ->first();

        if(!$paymentLog || !$this->paymentMethod){
            abort(404);
        }

        $api_secret          = $this->paymentMethod->payment_parameter->razorpay_secret;
        $signature           = hash_hmac('sha256', $request->razorpay_order_id . "|" . $request->razorpay_payment_id, $api_secret);

        if ($signature == $request->razorpay_signature) {
            
            if($paymentLog->type == PaymentType::ORDER->value ){
                PaymentInsert::paymentUpdate($paymentLog->trx_number);
                Order::where('id',$paymentLog->order_id)->update([
                    'payment_info'=>  json_encode($request->all())
                ]);
            }else{
                $response = WalletRecharge::walletUpdate($paymentLog);
            }


            return $this->paymentResponse($request,$paymentLog ,true );
        }
        return $this->paymentResponse($request,$paymentLog);
    }
}
