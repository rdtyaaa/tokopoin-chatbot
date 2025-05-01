<?php

namespace App\Http\Services\Frontend;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Utility\OrderNotification;
use App\Models\ShippingDelivery;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\ProductStock;
use App\Http\Utility\SendMail;
use App\Jobs\SendMailJob;
use App\Models\Currency;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class CheckoutService extends Controller
{



    /**
     * Cart calculation
     *
     * @param mixed $items
     * @return array
     */
    public function calculate(mixed $items) : array {

        $response = [];
        $shippingConfiguration =  json_decode(site_settings('shipping_configuration'));
        $couponAmount = 0; $totalCartAmount = 0; $totalQuantity = 0;  $discount=0 ;$taxes = 0;$original_price = 0;
        
        $flatShippingRate      =  @$shippingConfiguration->standard_shipping_fee ?? 0;
        
        $shippingCharge=(@$shippingConfiguration->shipping_option == 'FLAT') ? $flatShippingRate : 0;

        foreach($items as $item){
            $totalCartAmount += $item->total;
            $totalQuantity   += $item->quantity;
            $discount += ($item->discount*$item->quantity);
            $taxes += $item->total_taxes*$item->quantity;
            $original_price += (($item->original_price-$item->total_taxes )*$item->quantity);
            if(@$shippingConfiguration->shipping_option == 'PRODUCT_CENTRIC'){
                $shippingFees =  $item->product->shipping_fee;
                if($item->product->shipping_fee_multiply == 1 )$shippingFees *=$item->quantity;

                $shippingCharge+=$shippingFees;
            }

        }
        if(session()->has('coupon')){
            $coupon       = Coupon::where('code', session()->get('coupon')['code'])->first();
            $couponAmount = round(($coupon->discount($totalCartAmount)));
        }

        $response['coupon_amount']     =    $couponAmount;
        $response['regular_discount']  =    $discount;
        $response['total_taxes']       =    $taxes;
        $response['total_cart_amount'] =    $totalCartAmount;
        $response['original_price'] =    $original_price;
        $response['total_quantity']    =    $totalQuantity;
        $response['shippingCharge']    =    $shippingCharge;

        return $response;

    }





    /**
     * Check minimum order amount
     *
     * @param mixed $items
     * @param Currency $currency
     * @param boolean $isAPI
     * @return boolean
     */
    public function miniMumOrderAmountCheck(mixed $items ,Currency $currency, bool $isAPI = false) : bool{


        $cartTotal = $items->sum("total");

        $amount = !$isAPI
                  ? short_amount($cartTotal,false,false)
                  : api_short_amount((double)$cartTotal);


        $convertedAmount = default_currency_converter($amount,$currency);


        return $convertedAmount >= (double) site_settings('minimum_order_amount',0)
                                        ? true
                                        : false;


    }




    /**
     * Get shippig data
     *
     * @param Request $request
     * @return array
     */
    public function shippingData(Request $request , ? User $user) : array {

        $response['shipping_delivery'] = ShippingDelivery::find($request->shipping_method);

        return $response;

    }



    /**
     * Create order
     *
     * @param Request $request
     * @param array $cal
     * @param array $shippingData
     * @param User|null $user
     * @return Order
     */
    public function createOrder(Request $request ,array $cal,array | null $shippingData , ? User $user = null ,$addressId) : Order {

        $shipping       = Arr::get($shippingData,'shipping_delivery');

        $totalAmount    = (Arr::get($cal ,'total_cart_amount',0) - Arr::get($cal ,'coupon_amount',0)) ;


        $shippingCharge = (Arr::get($cal ,'shippingCharge',0));



        $totalAmount    = $totalAmount  +   $shippingCharge ;

        return  Order::create([
            'shipping_deliverie_id' => @$shipping->id,

            'customer_id'           => $user ? $user->id :null,
            'qty'                   => $cal['total_quantity'],
            'order_id'              => site_settings('order_prefix').random_number(),
            'shipping_charge'       => $shippingCharge ,
            'discount'              => Arr::get($cal ,'coupon_amount',0) +  Arr::get($cal ,'regular_discount',0),

            'amount'                => $totalAmount,
            'original_amount'       => Arr::get($cal ,'original_price',0),
            'total_taxes'           => Arr::get($cal ,'total_taxes',0),

            'payment_type'          => $request->input('payment_id') == StatusEnum::false->status() ?
                                                 Order::COD:Order::PAYMENT_METHOD,
            'payment_status'        => Order::UNPAID,
            'order_type'            => Order::PHYSICAL,
            'status'                => site_settings('default_order_status',Order::PLACED),
            'address_id'            => $addressId

        ]);

    }



    /**
     * Store order details
     *
     * @param mixed $items
     * @param Order $order
     * @return void
     */
    public function createOrderDetails(mixed $items, Order $order) : void {



        foreach($items as $item){

            if($item->attributes_value){

                $productStock = ProductStock::where('product_id', $item->product_id)
                                            ->where('attribute_value', $item->attributes_value)
                                            ->first();
                if($productStock){
                    $productStock->qty -= $item->quantity;
                    if($productStock->qty < 0) $productStock->qty  = 0;
                    $productStock->save();
                }
            }

            OrderDetails::create([
                'order_id'       => $order->id,
                'product_id'     => $item->product_id,
                'quantity'       => $item->quantity,
                'attribute'      => $item->attributes_value,
                'original_price' => ($item->original_price - $item->total_taxes) * $item->quantity,
                'total_price'    => $item->total,
                'total_taxes'    => $item->total_taxes*$item->quantity,
                'discount'       => ($item->discount*$item->quantity),
                'status'         => site_settings('default_order_status',Order::PLACED)
            ]);


        }

    }

    /** send email to  user */
    public function notifyUser($order , ? Currency $currency = null){

        session()->put('order_id', $order->order_id);
        if(session()->has('coupon'))        session()->forget('coupon');


        $phone      = @$order->billingAddress ? @$order->billingAddress->phone : @$order->billing_information->phone;
        $email      = @$order->billingAddress ? @$order->billingAddress->email : @$order->billing_information->email;
        $first_name = @$order->billingAddress ? @$order->billingAddress->first_name : @$order->billing_information->first_name;
        $address    = @$order->billingAddress ? @$order->billingAddress->address->address : @$order->billing_information->address;

        $mailCode = [
            'order_number'     => $order->order_id,
            'time'             => Carbon::now(),
            'payment_status'   => $order->payment_status == Order::PAID ? 'Paid' :"Unpaid",
            'amount'           => show_amount($order->amount ,$currency?$currency->symbol : null),
            'customer_phone'           => @$phone ?? 'N/A',
            'customer_email'           => @$email,
            'customer_name'            => @$first_name ?? "N/A",
            'customer_address'         => @$address ?? 'N/A',
        ];

        $notificationFor = (object)[
            "first_name" => $first_name,
            "email" =>   $email,
        ];

        $user = auth()->user() ? auth()->user() : $notificationFor;


        SendMailJob::dispatch($user,'ORDER_PLACED',$mailCode);


        //send notification to system admin

        OrderNotification::placed(order :$order , currency : $currency);


    }




    /**
     * Clear cart item
     *
     * @param Collection $items
     * @return bool
     */
    public function cleanCart(Collection $items) : bool{

        try {
            $items->each->delete();
            return true;

        } catch (\Throwable $th) {
           return false;
        }

    }




}
