<?php

namespace App\Http\Services\Seller;

use App\Enums\Settings\TokenKey;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Jobs\SendMailJob;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderStatus;
use App\Models\Seller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class OrderService extends Controller
{


    /**
     * Get seller order list
     *
     * @param Seller $seller
     * @return LengthAwarePaginator
     */
    public function getOrderList(string $type  ,Seller $seller) :LengthAwarePaginator{

        $orderStatus      = request()->input('status');

        $delevaryStatuses = Order::delevaryStatus();


        return Order::with(['orderStatus','log','customer','paymentMethod','shipping','shipping','orderDetails' => fn(HasMany $q) =>
                        $q->with('product')->sellerOrderProduct()->whereHas('product', fn(Builder $query)  => $query->where('seller_id', $seller->id)),
                        'deliveryman','orderRatings','orderRatings.user','billingAddress.user'
                        ])
                        ->when( $orderStatus && Arr::exists($delevaryStatuses,$orderStatus) , fn(Builder $q) => $q->where("status",Arr::get(   $delevaryStatuses,$orderStatus)))
                        ->when($type == 'physical', fn(Builder $q) =>  $q->physicalOrder())
                        ->when($type == 'digital',  fn(Builder $q) =>  $q->digitalOrder())
                        ->whereHas('orderDetails',  fn(Builder $q) =>
                            $q->whereHas('product', fn(Builder $query) => $query->where('seller_id', $seller->id)))
                        ->date()
                        ->search()
                        ->orderBy('id', 'DESC')
                        ->paginate(site_settings('pagination_number',10))
                        ->appends(request()->all());


    }



    /**
     * Get a specific order details
     *
     * @param string $orderNumber
     * @param Seller $seller
     * @return array
     */
    public function orderDetails(string $orderNumber  ,Seller $seller ): array {


        $order = Order::sellerOrder($seller->id)
                         ->with(['log','orderStatus','customer','paymentMethod','shipping','orderDetails.product','shipping.method','orderDetails' => fn(HasMany $q) => $q->sellerOrderProduct()->whereHas('product', fn(Builder $query)  => $query->where('seller_id', $seller->id))
                                        ])
                         ->whereHas('orderDetails', fn(Builder $q) =>
                                $q->whereHas('product', fn(Builder $query) =>
                                    $query->where('seller_id', $seller->id)))
                         ->where('order_id', $orderNumber)
                         ->first();

        if(! $order) return [ 'status'  => false, 'message' => translate("Invalid order number")];

        return [
            'order'    =>  $order,
            'status'   => true,
        ];


    }



    /**
     * Update order status
     *
     * @param Order $order
     * @param Request $request
     * @return array
     */
    public function updateOrderStatus(Order $order , Request $request ,Seller $seller) :array {



        if($order->status ==  Order::DELIVERED)  return [ 'status'  => false, 'message' => translate("Order Already Delivered")];
        if($order->status == Order::RETURN)  return [ 'status'  => false, 'message' => translate("Order Already Returned")];

        $phone      = @$order->billingAddress ? @$order->billingAddress->phone : @$order->billing_information->phone;
        $email      = @$order->billingAddress ? @$order->billingAddress->email : @$order->billing_information->email;
        $first_name = @$order->billingAddress ? @$order->billingAddress->first_name : @$order->billing_information->first_name;
        $address    = @$order->billingAddress ? @$order->billingAddress->address->address : @$order->billing_information->address;

        $mailCode = [
            'order_number'             => $order->order_id,
            'time'                     => Carbon::now(),
            'payment_status'           => $order->payment_status == Order::PAID ? 'Paid' :"Unpaid",
            'amount'                   => api_short_amount($order->amount),
            'customer_phone'           => @$phone ?? 'N/A',
            'customer_email'           => @$email,
            'customer_name'            => @$first_name ?? "N/A",
            'customer_address'         => @$address ?? 'N/A',
        ];

        $order->status = $request->status;
        $order->save();


        $notificationFor = (object)[
            "first_name" => $first_name,
            "email" =>   $email,
        ];

        if($order->status == Order::CONFIRMED) SendMailJob::dispatch($notificationFor,'ORDER_CONFIRMED',$mailCode);

        $order->orderDetails->each->update(['status' =>  $order->status]);

        if($order->status  == Order::DELIVERED){

            foreach ($order->orderDetails as $key => $value) {
                $commission  = 0;
                if(site_settings('seller_commission_status') ==  StatusEnum::true->status()){
                    $commission  = (($value->total_price * site_settings('seller_commission'))/100);
                }

                $finalAmount = $value->total_price - $commission;            
                $seller->balance += $finalAmount;
                $seller->save();

                $transaction = Transaction::create([
                    'seller_id' => $seller->id,
                    'amount' => $finalAmount,
                    'post_balance' => $seller->balance,
                    'transaction_type' => Transaction::PLUS,
                    'transaction_number' => trx_number(),
                    'details' => 'Amount added for this order '.$order->order_id,
                ]);
                
            }
        }

        $orderStatus = new OrderStatus();
        $orderStatus->order_id        = $order->id;
        $orderStatus->delivery_status = $request->input("status");
        $orderStatus->payment_status  = $order->status ==  Order::DELIVERED 
                                                        ? ORDER::PAID 
                                                        : ORDER::UNPAID;
        $orderStatus->delivery_note   = $request->input("delivery_note");
        $orderStatus->save();

        return [
            'message'  => translate("Order status updated"),
            'status'   => true,
        ];


    }


}
