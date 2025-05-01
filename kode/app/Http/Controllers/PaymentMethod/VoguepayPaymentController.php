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

class VoguepayPaymentController extends Controller
{


    const SANDBOX = false;
    public $paymentMethod;
    public function __construct(){
        $this->paymentMethod = PaymentMethod::with(['currency'])
                                ->where('unique_code', 'VOGUEPAY102')
                                ->first();

    }

    public function payment($trx_code = null)
    {
        try {
                $paymentTrackNumber =  $trx_code ?? session()->get('payment_track');

                $paymentLog = PaymentLog::with(['user'])->where('status', PaymentLog::PENDING)
                                   ->where('trx_number', $paymentTrackNumber)
                                   ->first();

                if(!$paymentLog || !$this->paymentMethod) return redirect()->route('home')
                                                                  ->with('error', translate('Invalaid Transaction'));


                $send['v_merchant_id']  = $this->paymentMethod->payment_parameter->merchant_id;
                $send['notify_url']     = route('vogue.callback',[$paymentLog->trx_number]);
                $send['cur']            = $this->paymentMethod->currency->name;
                $send['merchant_ref']   = $paymentLog->trx_number;
                $send['memo']           = 'Payment';
                $send['store_id']       = $paymentLog->id;
                $send['custom']         = $paymentLog->trx_number;
                $send['Buy']            = round($paymentLog->final_amount,2);

                return view('user.payment.voguepay',[
                    'title' => translate('Payment with Mercadopago'),
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

        $paymentLog = PaymentLog::where('status', PaymentLog::PENDING)->where('trx_number', $trx_code)
                                                                      ->first();
        if(!$paymentLog || !$this->paymentMethod) abort(404);

    }
}
