<?php

namespace App\Http\Controllers\PaymentMethod;

use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Utility\PaymentInsert;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PaymentLog;

class PaystackPayment extends Controller
{


    public $paymentMethod;
    public function __construct(){
        $this->paymentMethod = PaymentMethod::with(['currency'])->where('unique_code', 'PAYSTACK103')->first();

    }


    public function payment($trx_code = null)
    {

        try {
            $paymentTrackNumber = $trx_code ?? session()->get('payment_track');
            $paymentLog = PaymentLog::with(['user','order','seller'])
                                     ->where('status', PaymentLog::PENDING)
                                     ->where('trx_number', $paymentTrackNumber)
                                     ->first();
    
            if(!$paymentLog ||   !$this->paymentMethod)  return redirect()->route('home')->with('error', translate('Invalaid Transaction'));
    
    
            $send['key']      = $this->paymentMethod->payment_parameter->public_key;
            $send['email']    = $paymentLog->seller 
                                    ? $paymentLog->seller->email 
                                    : optional($paymentLog->user)->email;
            
            
            $send['amount']   = round($paymentLog->final_amount * 100,2);
            $send['currency'] = $this->paymentMethod->currency->name;
            $send['ref']      = $paymentTrackNumber;

            $view =  'user.payment.paystack';
            if($paymentLog->seller) $view =  'seller.deposit.paystack';

            return view($view ,[
                'title'         => translate('Payment with Paystack'),
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

        $paymentLog = PaymentLog::with(['user','order','seller','paymentGateway'])
                                     ->where('status', PaymentLog::PENDING)
                                    ->where('trx_number', $trx_code)
                                    ->first();
        if(!$paymentLog || !$this->paymentMethod)       abort(404);

        $secret_key  = $this->paymentMethod->payment_parameter->secret_key;
        $url         = 'https://api.paystack.co/transaction/verify/' . $trx_code;
        $headers = [
            "Authorization: Bearer {$secret_key}"
        ];
        $response = $this->curlGetRequestWithHeaders($url, $headers);

        $response = json_decode($response, true);


        if ($response && isset($response['data'])) {

            if ($response['data']['status'] == 'success') {    
                if($paymentLog->type == PaymentType::ORDER->value ){
                    PaymentInsert::paymentUpdate($paymentLog->trx_number);
                    Order::where('id',$paymentLog->order_id)->update([
                        'payment_info'=>  json_encode($response)
                    ]);
                }
                else{
                    $response = WalletRecharge::walletUpdate($paymentLog);
                }

                return $this->paymentResponse($request,$paymentLog ,true );
            }
        } 

        return $this->paymentResponse($request,$paymentLog);


    }
}
