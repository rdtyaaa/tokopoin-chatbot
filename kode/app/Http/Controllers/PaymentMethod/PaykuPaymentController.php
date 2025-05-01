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
class PaykuPaymentController extends Controller
{
  

    public $paymentMethod;
    public function __construct(){
        $this->paymentMethod = PaymentMethod::with(['currency'])
                                                ->where('unique_code', 'PAYKU108')
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



                $gateway           = $this->paymentMethod->payment_parameter; 


                if($paymentLog->type == PaymentType::WALLET->value ){

                    if($paymentLog->seller){
                        $seller     = @$paymentLog->seller;
                        $email      = $seller->email;
                    }
                    elseif($paymentLog->user){
                        $user     = @$paymentLog->user;
                        $email      = $user->email;
                    }

                }else{

                    $order =  @$paymentLog->order;
                    $email      = @$order->billingAddress ? @$order->billingAddress->email : @$order->billing_information->email;

                }
   
              

                $url =  $gateway->base_url.'/api/transaction';

           
                $client = new \GuzzleHttp\Client();
                
                $body = $client->request('POST', $url, [
                    'json' => [
                        'email'     => $email,
                        'order'     => $paymentTrackNumber,
                        'subject'   => 'Order Payment',
                        'amount'    => (int)round($paymentLog->final_amount),
                        'payment'   => 1,
                        'urlreturn' => route('payku.return'),
                        'urlnotify' => route('payku.callback',["trx_code" => $paymentLog->trx_number])
                     ],
                    'headers' => [
                        'Authorization' => 'Bearer '.$gateway->public_token
                    ]
                   ])->getBody();

                $response = json_decode($body);

       

                if($response->url) return redirect($response->url);
        
                return back()->with('error',translate('Invalid payment credentials'));


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


        if($request->status == 'success'){

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



    
    public function paymentReturn(){

        if(request()->expectsJson())   return json_encode([
                                                            "status"  => true,
                                                            "message" => translate('Payment process completed')
                                                        ]);


        return redirect()->route('home')->with('success',translate('Payment process completed'));
    }




}
