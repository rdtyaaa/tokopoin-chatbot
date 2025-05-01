<?php
namespace App\Http\Utility;

use App\Enums\PaymentType;
use App\Enums\StatusEnum;
use App\Models\PaymentMethod;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\PaymentLog;
use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Transaction;
use App\Models\DigitalProductAttribute;
use App\Models\GeneralSetting;
use App\Http\Utility\SendMail;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Jobs\SendMailJob;

class PaymentInsert
{
    

  

    /**
     * Create  a payment
     * 
     * @param PaymentMethod $paymentMethod
     * @param int | string | null  $orderId
     * @param User | null  $user
     * 
     * @return PaymentLog
     */
    public static function paymentCreate(PaymentMethod $paymentMethod,? User $user = null , int | string $orderId = null): PaymentLog{
     
    
        $order_id =  !$orderId  ? session('order_id') : $orderId;
      
        $order = Order::where('order_id',$order_id)
                            ->where('payment_status', Order::UNPAID)
                            ->first();
        if(!$order) abort(404);

        $charge                   = ($order->amount * $paymentMethod->percent_charge / 100);

        $final_amount             = ($order->amount  + $charge )*$paymentMethod->rate;

        $paymentLog = PaymentLog::create([
            'order_id'     => $order->id,
            'user_id'      => $user ? $user->id : null,
            'method_id'    => $paymentMethod->id,
            'charge'       => $charge,
            'rate'         => $paymentMethod->rate,
            'amount'       => $order->amount ,
            'final_amount' => $final_amount,
            'trx_number'   => trx_number(),
            'status'       => PaymentLog::PENDING,
            'type'         => PaymentType::ORDER->value,
        ]);
   
        session()->put('payment_track', $paymentLog->trx_number);

        return $paymentLog;
    }

    public static function paymentUpdate($trx , $paymentId=null)
    {
      

        $paymentData = PaymentLog::where('trx_number', $trx)->first();
        if($paymentData && $paymentData->status == PaymentLog::PENDING){
          
            $paymentData->status = 2;
            $paymentData->save();
            $user = User::find($paymentData->user_id);
            if(@$user){
                Cart::where('user_id', $user->id)->delete();
            }
            else{
                Cart::whereNotNull('session_id')->where('session_id', session()->get('session_id'))->delete();
            }
    
            $order = Order::with(['digitalProductOrder','orderDetails'])->where('id', $paymentData->order_id)->first();



            $guestUser =  $order->billingAddress ? 
                             [
                                'email' =>  $order->billingAddress->email
                             ]  :  $order->billing_information;
            
            
        
            $transaction = Transaction::create([
                'user_id'            => $user  ? $user->id : null,
                'amount'             => $paymentData->amount,
                'post_balance'       => 0,
                'transaction_type'   => Transaction::PLUS,
                'transaction_number' => trx_number(),
                'guest_user'         =>  !$user ?      $guestUser : null,
                'details'            => 'Payment Via ' . $paymentData->paymentGateway->name,
            ]);


            $order->payment_status= Order::PAID;
            $order->status = site_settings('order_status_after_payment',Order::PLACED);

            if($order->payment_type == 2){
                $order->payment_method_id= $paymentData->method_id;
                if($paymentId !=null){
                    $order->payment_id = $paymentId;
                }
            }


            if($order->order_type == Order::DIGITAL){

                $digitalProductAttribute = DigitalProductAttribute::find(@$order->digitalProductOrder->digital_product_attribute_id);

                $order->status = Order::DELIVERED;
                $order->save();

                OrderDetails::where('order_id',$order->id)->update([
                    'status' => Order::DELIVERED
                ]);
        
                $product = Product::find($order->digitalProductOrder->product_id);
                if($product && $product->seller_id){
                    $commission  = 0;
                    if(site_settings('seller_commission_status') ==  StatusEnum::true->status()){
                        $commission  = (($order->amount * site_settings('seller_commission'))/100);
                    }
        
                    $finalAmount = $order->amount - $commission;

                    $seller = Seller::findOrFail($product->seller_id);
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
            }else{
                OrderDetails::where('order_id',$order->id)->update([
                    'status' => site_settings('order_status_after_payment',Order::PLACED)
                ]);
            }


            $order->save();
    
            $mailCode = [
                'trx' => $paymentData->trx_number,
                'amount' => ($paymentData->final_amount),
                'charge' => ($paymentData->charge),
                'currency' => @session()->get('web_currency')->name,
                'rate' => ($paymentData->rate),
                'method_name' => $paymentData->paymentGateway->name,
                'method_currency' => $paymentData->paymentGateway->currency->name,
            ];


            $email      = @$order->billingAddress ? @$order->billingAddress->email : @$order->billing_information->email;
            $first_name = @$order->billingAddress ?  @$order->billingAddress->first_name : @$order->billing_information->first_name;
    

            $billingInfo = (object)[
                "first_name" => $first_name,
                "email"      =>   $email,
            ];
            

            SendMailJob::dispatch($user?? $billingInfo,'PAYMENT_CONFIRMED',$mailCode);
        }
    }
}



