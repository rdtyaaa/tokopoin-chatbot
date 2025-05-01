<?php

namespace App\Http\Controllers\User;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\DigitalProductAttribute;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\PaymentMethod;
use App\Http\Utility\PaymentInsert;
use App\Http\Utility\SendMail;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Jobs\SendMailJob;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DigitalProductOrderController extends Controller
{


    protected ? User $user;
    public function __construct() {

        $this->middleware(function ($request, $next) {
            $this->user = auth_user('web');
            return $next($request);
        });
    }
    
    
    /**
     * Store a digital order
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request) :RedirectResponse {

        $rules = [
            'digital_attribute_id' => 'required',
            'digital_product_id'   => 'required|exists:products,id',
            'payment_id'           => 'required',
        ];

        if(!$this->user)  $rules['email'] = 'required|email';

        if(auth_user('web') && site_settings('customer_wallet') == StatusEnum::true->status()){
            $rules+=[
                'wallet_payment' => 'required|in:1,0',
            ];
            unset( $rules['payment_id']);
        }



        $request->validate($rules);

        $user = $this->user;

        $product                 = Product::digital()
                                                ->where('id', $request->input('digital_product_id'))
                                                ->firstOrFail();
 

        $digitalProductAttribute = DigitalProductAttribute::where('product_id', $product->id)
                                                            ->where('id', $request->input('digital_attribute_id'))
                                                            ->available()
                                                            ->firstOrfail();


        if($product->custom_fileds){
            $customInfo =  [];
            foreach($product->custom_fileds as $key => $value){
                $customInfo [$value->data_name] =  $request->input($value->data_name);
            }
        }

 
        if( !$request->wallet_payment || $request->wallet_payment          ==  StatusEnum::false->status() || 
          site_settings('customer_wallet')   ==  StatusEnum::false->status()){
                        $paymentMethod           = PaymentMethod::where('id', $request->input('payment_id'))
                                                                ->active()
                                                                ->first();

             if(!$paymentMethod)  return back()->with("error",translate("Invalid payment gateway"));
        }




        if(site_settings('minimum_order_amount_check') == StatusEnum::true->status()){

            $cartAmount = default_currency_converter(short_amount($digitalProductAttribute->price,false,false),session()->get('web_currency'));
            if($cartAmount < (double) site_settings('minimum_order_amount',0)){
               return back()->with('error',translate('Minimun order amount should be ').show_amount((double)site_settings('minimum_order_amount'),default_currency()->symbol));
            }

        }



        $price      =  ($digitalProductAttribute->price);
        $taxes      =  getTaxes(@$product ,$price);
        $price      =  $price  + $taxes;



        
        $billing_information =   $request->input('email') 
                                                ? ['email' => $request->input('email'),'username' => $request->input('email')]
                                                : null;
        if($user){
            $billing_information = ['email' => $user->email,'username' => $user->name ?? 'N/A'];
        }


        $order = Order::create([
            'customer_id'         => $user?->id,
            'order_id'            => site_settings('order_prefix').random_number(),
            'amount'              => $price ,
            'original_amount'     => $digitalProductAttribute->price,
            'total_taxes'         => $taxes ,
            'payment_type'        => Order::PAYMENT_METHOD,
            'payment_status'      => Order::UNPAID,
            'status'              => site_settings('default_order_status',Order::PLACED),
            'order_type'          => Order::DIGITAL,
            'billing_information' => $billing_information,
            'custom_information'  => @$customInfo ?? null,
        ]);

        OrderDetails::create([
            'order_id'                     => $order->id,
            'product_id'                   => $product->id,
            'digital_product_attribute_id' => $digitalProductAttribute->id,
            'quantity'                     => 1,
            'total_price'                  => $order->amount,
            'original_price'               => $order->original_amount,
            'total_taxes'                  => $order->total_taxes,
            'status'                       => site_settings('default_order_status',Order::PLACED)
        ]);

        $mailCode = [
            'order_number' => $order->order_id,
        ];
        
        SendMailJob::dispatch($user ?? $order->billing_information,'DIGITAL_PRODUCT_ORDER',$mailCode);


         #HANDLE WALLET PAYMENT
         if(auth_user('web') && $request->wallet_payment ==  StatusEnum::true->status() && 
         site_settings('customer_wallet') == StatusEnum::true->status()){

            $user = auth_user('web');

            if( $user->balance   <   $order->amount)  return redirect()->back()->with('error', translate("Insufficient Wallet balance !!"));

            $order->status         = Order::DELIVERED;
            $order->payment_status = Order::PAID;
            $order->wallet_payment = Order::WALLET_PAYMENT;
            $order->save();



            $transaction = Transaction::create([
                'user_id'            => $user  ? $user->id : null,
                'amount'             => $order->amount,
                'post_balance'       => $user->balance,
                'transaction_type'   => Transaction::MINUS,
                'transaction_number' => trx_number(),
                'details'            => 'Order Payement for order id: '.$order->order_id .' Via Wallet',
            ]);

            $user->balance -= $order->amount;
            $user->save();

            if(@$user && site_settings('club_point_system') == StatusEnum::true->status()){
                WalletRecharge::generatePointLog($user ,$order);
            }

            $this->sendDigitalOrderCommission($order);

            return redirect()->route('order.success', $order->order_id)->with('success', translate("Your order has been submitted"));

         }


        session()->put('order_id', $order->order_id);

        PaymentInsert::paymentCreate($paymentMethod,$this->user);

        return redirect()->route('user.payment.confirm');
    }


    public function digitalOrderCancel(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:orders,id'
        ]);
        $user = Auth::user();
        $order = Order::where('id', $request->id)->where('customer_id', $user->id)->firstOrFail();
        if($order->payment_status == 1){
            $order->status = Order::CANCEL;
            $order->save();
            $orderDetail = OrderDetails::where('order_id', $order->id)->first();
            $orderDetail->status = Order::CANCEL;
            $orderDetail->save();
            return back()->with('success',translate('Digital Order has been canceled'));
        }else{
            return back()->with('error',translate('Invalid digital Order'));
        }
    }
}
