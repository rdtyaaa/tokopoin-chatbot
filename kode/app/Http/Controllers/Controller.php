<?php

namespace App\Http\Controllers;

use App\Enums\RewardPointStatus;
use App\Enums\StatusEnum;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\PaymentLog;
use App\Models\PaymentMethod;
use App\Models\RewardPointLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;



    public function paymentResponse(Request $request , PaymentLog $log, bool $status = false , ) :mixed{

        $message = $status ? translate("Thank you for your payment") : translate("Your transaction is failed");
        if($request->expectsJson()){
            return json_encode([
                "status"  => $status,
                "message" =>  $message 
            ]);
        }


        $routeName =  $status ? 'payment.success' : 'payment.failed';
        
        if($log->seller)  {
            $routeName = 'seller.' . $routeName;
            Auth::guard('seller')->loginUsingId($log->seller->id);
        }

        if($log->user)  {
            Auth::guard('web')->loginUsingId($log->user->id);
        }


        return redirect()->route( $routeName ,[
              'trx_number' => $log->trx_number ,
              'status'     => $status ? 'success' : 'error'] )->with( $status ? 'success' : 'error',  $message );


    }

    
    public function getPaymentURL(PaymentMethod $paymentMethod , PaymentLog $paymentLog) {


        $paymentMethods = [
            'PAYEER105'      => 'payeer',
            'PAYU101'        => 'payumoney',
            'PAYHERE101'     => 'payhere', 
            'PAYKU108'       => 'payku',
            'PHONEPE102'     => 'phonepe',
            'SENANGPAY107'   => 'senangpay',
            'NGENIUS111'     => 'ngenius',
            'STRIPE101'      => 'stripe',
            'WEBXPAY109'     => 'webxpay',
        ];

        $method = Arr::get($paymentMethods , $paymentMethod->unique_code ); 

        if(!$method) return null;
        
        return  route($method.".payment",['trx_code' => $paymentLog->trx_number]);

    }


    public  function curlPostRequestWithHeaders($url, $headers, $postParam = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postParam));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public  function curlGetRequestWithHeaders($url, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }


    public function sendDigitalOrderCommission(Order $order) : void{

        $order   = $order->load(['digitalProductOrder','digitalProductOrder.product','digitalProductOrder.product.seller']);
        $product =  @$order->digitalProductOrder->product;
        $seller  =  @$product->seller;


        OrderDetails::where('order_id',$order->id)->update(['status' => Order::DELIVERED]);

        if($product && $seller){

            $commission  = site_settings('seller_commission_status') ==  StatusEnum::true->status() 
                                ? (($order->amount * site_settings('seller_commission'))/100) 
                                : 0;
            $finalAmount = $order->amount - $commission;
            $seller->balance += $finalAmount;
            $seller->save();
            $transaction = Transaction::create([
                'seller_id' => $seller->id,
                'amount' => $finalAmount,
                'post_balance' => $seller->balance,
                'transaction_type' => Transaction::PLUS,
                'transaction_number' => trx_number(),
                'details' =>  $order->order_id.' order number amount added',
            ]);
        }


    }


  





}
