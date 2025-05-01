<?php

namespace App\Http\Controllers\PaymentMethod;

use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PaymentMethod;
use App\Models\PaymentLog;


use Illuminate\Support\Facades\Redirect;

use App\Http\Utility\PaymentInsert;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Models\GeneralSetting;
use App\Models\Order;

class PaypalPaymentController extends Controller
{


    public $paymentMethod;
    public function __construct(){
        $this->paymentMethod = PaymentMethod::with(['currency'])->where('unique_code', 'PAYPAL102')->first();

    }


    public  function payment($trx_code = null)
    {


        try {
            $siteName   = site_settings('site_name');

            $paymentTrackNumber = $trx_code ?? session()->get('payment_track');
            $paymentLog = PaymentLog::with(['user','order','seller'])
                              ->where('status', PaymentLog::PENDING)
                              ->where('trx_number', $paymentTrackNumber)
                              ->first();
    
            if(!$paymentLog ||   !$this->paymentMethod){
                return redirect()->route('home')->with('error', translate('Invalaid Transaction'));
            }
    
            $param['cleint_id']   = $this->paymentMethod->payment_parameter->client_id;
            $param['description'] = "Payment To {$siteName} Account";
            $param['custom_id']   = $paymentLog->trx_number;
            $param['amount']      = round($paymentLog->final_amount,2);
            $param['currency']    = $this->paymentMethod->currency->name;

            $view =  'user.payment.paypal';
            if($paymentLog->seller) $view =  'seller.deposit.paypal';
    
            return view($view,[
                'title' => translate('Payment with Paypal'),
                'paymentMethod' =>  $this->paymentMethod,
                'paymentLog' => $paymentLog,
                'data'       => (object) $param
            ]);
    
        } catch (\Exception $ex) {
            return back()->with('error',$ex->getMessage());
        }

      
    }


    public function callBack(Request $request ,$trx_code , mixed $type = null)
    {

        $paymentLog = PaymentLog::with(['user','order','seller','paymentGateway'])
                                ->where('status', PaymentLog::PENDING)
                                ->where('trx_number', $trx_code)
                                ->first();

        if(!$paymentLog) abort(404);

        $url         = "https://api.paypal.com/v2/checkout/orders/{$type}";
        $client_id   = $this->paymentMethod->payment_parameter->client_id;
        $secret      = $this->paymentMethod->payment_parameter->secret;
        $headers = [
            'Content-Type:application/json',
            'Authorization:Basic ' . base64_encode("{$client_id}:{$secret}")
        ];
        $response     = $this->curlGetRequestWithHeaders($url, $headers);
        $paymentData  = json_decode($response, true);

        
        if (isset($paymentData['status']) && $paymentData['status'] == 'COMPLETED') {

            if ($paymentData['purchase_units'][0]['amount']['currency_code'] == $this->paymentMethod->currency->name) {

                if($paymentLog->type == PaymentType::ORDER->value ){
                    PaymentInsert::paymentUpdate($paymentLog->trx_number);
                    Order::where('id',$paymentLog->order_id)->update([
                        'payment_info'=>  json_encode($paymentData)
                    ]);
                }
                else{
                    $response = WalletRecharge::walletUpdate($paymentLog);
                }
                return $this->paymentResponse($request,$paymentLog,true );
            } 
        } 

        return $this->paymentResponse($request,$paymentLog);
        
    }


}

