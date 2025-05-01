<?php

namespace App\Http\Controllers\PaymentMethod;

use App\Http\Controllers\Controller;
use App\Http\Services\CurlService;
use Illuminate\Http\Request;
use App\Models\PaymentLog;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Utility\PaymentInsert;
use App\Models\Order;

class CashmaalPaymentController extends Controller
{


    public $paymentMethod;
    public function __construct(){
        $this->paymentMethod = PaymentMethod::with(['currency'])
                                ->where('unique_code', 'CASHMAAL103')
                                ->first();

    }

    public function payment($trx_code = null)
    {
        try {
                $paymentTrackNumber = $trx_code ??  session()->get('payment_track');

                $paymentLog = PaymentLog::with(['user'])->where('status', PaymentLog::PENDING)
                                   ->where('trx_number', $paymentTrackNumber)
                                   ->first();

                if(!$paymentLog || !$this->paymentMethod)  return redirect()->route('home')->with('error', translate('Invalaid Transaction'));


                $val['pay_method']    = " ";
                $val['amount']        = round($paymentLog->final_amount, 2);
                $val['currency']      = $this->paymentMethod->currency->name;
                $val['succes_url']    = route('cashmaal.callback',["trx_code" => $paymentLog->trx_number,"type" => 'success']);
                $val['cancel_url']    = route('cashmaal.callback',["trx_code" => $paymentLog->trx_number,"type" => 'failed']);
                $val['client_email']  = optional($paymentLog->user)->email;
                $val['web_id']        = $this->paymentMethod->payment_parameter->web_id;
                $val['order_id']      = $paymentLog->trx_number;
                $val['addi_info']     = "Payment";
        
                $send['url']          = 'https://www.cashmaal.com/Pay/';
                $send['method']       = 'post';
                $send['view']         = 'user.payment.redirect';
                $send['val']          = $val;
        

                return view('user.payment.redirect',[
                    'title' => translate('Payment with CASHMAAL'),
                    'paymentMethod' =>  $this->paymentMethod,
                    'paymentLog' => $paymentLog,
                    'data'       => (object) $send
                ]);


        } catch (\Exception $ex) {
            return back()->with('error',$ex->getMessage());
        }
    }


    public function callBack(Request $request ,$trx_code ,$type = null)
    {

        $paymentLog = PaymentLog::where('status', PaymentLog::PENDING)->where('trx_number', $trx_code)
                                                                   ->first();
        if(!$paymentLog || !$this->paymentMethod) abort(404);

        if($type == 'success' && $request->currency == $this->paymentMethod->currency->name){
            Order::where('id',$paymentLog->order_id)->update([
                'payment_info'=>  json_encode($request->all())]);
            return $this->paymentResponse($request,$paymentLog->trx_number ,true );
        }

        return $this->paymentResponse($request,$paymentLog->trx_number);

    }
}
