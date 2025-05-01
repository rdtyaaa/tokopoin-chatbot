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
class PayeerPaymentController extends Controller
{
  

    public $paymentMethod;
    public function __construct(){
        $this->paymentMethod = PaymentMethod::with(['currency'])
                                                ->where('unique_code', 'PAYEER105')
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



                $siteName = site_settings('site_name');

                $m_amount = number_format($paymentLog->amount, 2, '.', "");
                $arHash = [
                    trim($this->paymentMethod->payment_parameter->merchant_id),
                    $paymentLog->trx_number,
                    $m_amount,
                    $this->paymentMethod->currency->name,
                    base64_encode("Payment To $siteName"),
                    trim($this->paymentMethod->payment_parameter->secret_key)
                ];

                $val['m_shop']     = trim($this->paymentMethod->payment_parameter->merchant_id);
                $val['m_orderid']  = $paymentLog->trx_number;
                $val['m_amount']   = $m_amount;
                $val['m_curr']     = $this->paymentMethod->currency->name;
                $val['m_desc']     = base64_encode("Deposit To $siteName");
                $val['m_sign']     = strtoupper(hash('sha256', implode(":", $arHash)));

                $send['val']       = $val;
                $send['method']    = 'get';
                $send['url']       = 'https://payeer.com/merchant';

           
                return view('user.payment.redirect',[
                    'title'         => translate('Payment with PAYEER'),
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
        if(!$paymentLog || !$this->paymentMethod) abort(404);


        if (isset($request->m_operation_id) && isset($request->m_sign)) {

            $sign_hash = strtoupper(hash('sha256', implode(":", array(
                $request->m_operation_id,
                $request->m_operation_ps,
                $request->m_operation_date,
                $request->m_operation_pay_date,
                $request->m_shop,
                $request->m_orderid,
                $request->m_amount,
                $request->m_curr,
                $request->m_desc,
                $request->m_status,
                $this->paymentMethod->payment_parameter->secret_key
            ))));

            if ($request->m_sign != $sign_hash) {
                return $this->paymentResponse($request,$paymentLog);
            } else {
                if ( $request->m_curr == $this->paymentMethod->currency->name && $request->m_status == 'success') {

                       
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
            }
        }


        return $this->paymentResponse($request,$paymentLog);

    }

}
