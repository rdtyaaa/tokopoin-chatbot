<?php

namespace App\Http\Controllers\PaymentMethod;

use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use App\Http\Services\CurlService;
use Illuminate\Http\Request;
use App\Models\PaymentLog;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Utility\PaymentInsert;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Models\Order;

class MercadopagoController extends Controller
{


    const SANDBOX = false;
    public $paymentMethod;
    public function __construct(){
        $this->paymentMethod = PaymentMethod::with(['currency'])
                                ->where('unique_code', 'MERCADO101')
                                ->first();

    }

    public function payment($trx_code = null)
    {
        try {
                $paymentTrackNumber =  $trx_code ?? session()->get('payment_track');

                $paymentLog = PaymentLog::with(['user','order','seller'])->where('status', PaymentLog::PENDING)
                                        ->where('trx_number', $paymentTrackNumber)
                                        ->first();

                if(!$paymentLog || !$this->paymentMethod)  return redirect()->route('home')->with('error', translate('Invalaid Transaction'));


                $name = @$paymentLog?->seller ? @$paymentLog?->seller->name : @$paymentLog?->user->name; 


                $url         = "https://api.mercadopago.com/checkout/preferences?access_token=" . $this->paymentMethod->payment_parameter->access_token;
                $headers = ["Content-Type: application/json"];

      
                $postParam = [
                    'items' => [
                        [
                            'id' => $paymentLog->trx_number,
                            'title' => "Payment",
                            'description' => 'Payment from '. $name,
                            'quantity' => 1,
                            'currency_id' => $this->paymentMethod->currency->name,
                            'unit_price' =>  round($paymentLog->final_amount,2)
                        ]
                    ],
                    'payer' => [
                        'email' => $paymentLog->seller ? $paymentLog->seller->email : @optional($paymentLog->user)->email ,
                    ],
                    'back_urls' => [
                        'success' => route('payment.success',$paymentLog->trx_number),
                        'pending' => '',
                        'failure' => route('payment.failed',$paymentLog->trx_number),
                    ],
                    'notification_url' => route("mercado.callback",['trx_code' =>$paymentLog->trx_number ]),
             
                    'auto_return' => 'approved'
                ];
                $response = CurlService::curlPostRequestWithHeaders($url, $headers, $postParam);
                $response = json_decode($response);

                $send['preference']  =  $paymentLog->trx_number;

                
                if(isset($response->auto_return) && $response->auto_return == 'approved') {
                    $send['redirect']     = true;
                    $send['redirect_url'] = $response->init_point;

                    if (self::SANDBOX)  $send['redirect_url'] = $response->sandbox_init_point;

                    $view =  'user.payment.mercado';
                    if($paymentLog->seller) $view =  'seller.deposit.mercado';

                    return view(  $view ,[
                        'title' => translate('Payment with Mercadopago'),
                        'paymentMethod' =>  $this->paymentMethod,
                        'paymentLog'    => $paymentLog,
                        'data'          => (object) $send
                    ]);
                }

              return back()->with("error",translate("Invalid Payment Parameters"));

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


        $paymentId     = json_decode(json_encode($request->all()))?->data->id;
        $url           = "https://api.mercadopago.com/v1/payments/" . $paymentId. "?access_token=" . $this->paymentMethod->payment_parameter->access_token;
        $response      = CurlService::curlGetRequest($url);
        $paymentData   = json_decode($response);

        if (isset($paymentData->status) && $paymentData->status == 'approved') {


                if($paymentLog->type == PaymentType::ORDER->value ){
                    PaymentInsert::paymentUpdate($paymentLog->trx_number);
                    Order::where('id',$paymentLog->order_id)->update([
                        'payment_info'=>  json_encode($paymentData)
                    ]);
                }else{
                    $response = WalletRecharge::walletUpdate($paymentLog);
                }



                return $this->paymentResponse($request,$paymentLog ,true );
        }

        return $this->paymentResponse($request,$paymentLog);

    }
}
