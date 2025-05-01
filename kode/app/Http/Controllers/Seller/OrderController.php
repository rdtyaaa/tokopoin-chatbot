<?php

namespace App\Http\Controllers\Seller;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Jobs\SendMailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;

class OrderController extends Controller
{

    public function index()
    {
        $title = translate('Seller all orders');
        $seller = Auth::guard('seller')->user();
        $orders = Order::with(['log','customer','paymentMethod','orderDetails' => function($q){
            return $q->sellerOrderProduct()->whereHas('product', function($query)  {
                $query->where('seller_id', Auth::guard('seller')->user()->id);
            });
        },'shipping','orderDetails.product','shipping.method'])->physicalOrder()->whereHas('orderDetails', function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->date()->search()->orderBy('id', 'DESC')->paginate(site_settings('pagination_number',10));
        return view('seller.order.index', compact('title', 'orders'));
    }

    public function placed()
    {
        $title = translate('Seller placed orders');
        $seller = Auth::guard('seller')->user();
        $orders = Order::with(['log','paymentMethod','orderDetails' => function($q){
            return $q->sellerOrderProduct()->whereHas('product', function($query)  {
                $query->where('seller_id', Auth::guard('seller')->user()->id);
            });
        },'customer','shipping','shipping.method','orderDetails.product'])->sellerOrder()->physicalOrder()->date()->search()->placed()->whereHas('orderDetails', function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->orderBy('id', 'DESC')->paginate(site_settings('pagination_number',10));
        return view('seller.order.index', compact('title', 'orders'));
    }

    public function confirmed()
    {
        $title = translate('Seller confirmed orders');
        $seller = Auth::guard('seller')->user();
        $orders = Order::with(['log','paymentMethod','orderDetails' => function($q){
            return $q->sellerOrderProduct()->whereHas('product', function($query)  {
                $query->where('seller_id', Auth::guard('seller')->user()->id);
            });
        },'customer','shipping','shipping.method','orderDetails.product'])->sellerOrder()->physicalOrder()->date()->search()->confirmed()->whereHas('orderDetails', function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->orderBy('id', 'DESC')->paginate(site_settings('pagination_number',10));
        return view('seller.order.index', compact('title', 'orders'));
    }

    public function return()
    {
        $title = translate('Seller return orders');
        $seller = Auth::guard('seller')->user();
        $orders = Order::with(['log','paymentMethod','orderDetails' => function($q){
            return $q->sellerOrderProduct()->whereHas('product', function($query)  {
                $query->where('seller_id', Auth::guard('seller')->user()->id);
            });
        },'customer','shipping','shipping.method','orderDetails.product'])->sellerOrder()->physicalOrder()->date()->search()->return()->whereHas('orderDetails', function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->orderBy('id', 'DESC')->paginate(site_settings('pagination_number',10));
        return view('seller.order.index', compact('title', 'orders'));
    }


    public function pending()
    {
        $title = translate('Seller pending orders');
        $seller = Auth::guard('seller')->user();
        $orders = Order::with(['log','paymentMethod','orderDetails' => function($q){
            return $q->sellerOrderProduct()->whereHas('product', function($query)  {
                $query->where('seller_id', Auth::guard('seller')->user()->id);
            });
        },'customer','shipping','shipping.method','orderDetails.product'])->sellerOrder()->physicalOrder()->date()->search()->pending()->whereHas('orderDetails', function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->orderBy('id', 'DESC')->paginate(site_settings('pagination_number',10));
        return view('seller.order.index', compact('title', 'orders'));
    }

    public function processing()
    {
        $title = translate('Seller processing orders');
        $seller = Auth::guard('seller')->user();
        $orders = Order::with(['log','paymentMethod','orderDetails' => function($q){
            return $q->sellerOrderProduct()->whereHas('product', function($query)  {
                $query->where('seller_id', Auth::guard('seller')->user()->id);
            });
        },'customer','shipping','shipping.method','orderDetails.product'])->sellerOrder()->physicalOrder()->date()->search()->processing()->whereHas('orderDetails', function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->orderBy('id', 'DESC')->paginate(site_settings('pagination_number',10));
        return view('seller.order.index', compact('title', 'orders'));
    }

    public function shipped()
    {
        $title = translate('Seller shipped orders');
        $seller = Auth::guard('seller')->user();
        $orders = Order::with(['log','paymentMethod','orderDetails' => function($q){
            return $q->sellerOrderProduct()->whereHas('product', function($query)  {
                $query->where('seller_id', Auth::guard('seller')->user()->id);
            });
        },'customer','shipping','shipping.method','orderDetails.product'])->sellerOrder()->physicalOrder()->date()->search()->shipped()->whereHas('orderDetails', function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->orderBy('id', 'DESC')->paginate(site_settings('pagination_number',10));
        return view('seller.order.index', compact('title', 'orders'));
    }

    public function delivered()
    {
        $title = translate('Seller delivered orders');
        $seller = Auth::guard('seller')->user();
        $orders = Order::with(['log','paymentMethod','orderDetails' => function($q){
            return $q->sellerOrderProduct()->whereHas('product', function($query)  {
                $query->where('seller_id', Auth::guard('seller')->user()->id);
            });
        },'customer','shipping','shipping.method','orderDetails.product'])->sellerOrder()->physicalOrder()->date()->search()->delivered()->whereHas('orderDetails', function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->orderBy('id', 'DESC')->paginate(site_settings('pagination_number',10));
        return view('seller.order.index', compact('title', 'orders'));
    }

    public function cancel()
    {
        $title = translate('Seller cancel orders');
        $seller = Auth::guard('seller')->user();
        $orders = Order::with(['log','paymentMethod','orderDetails' => function($q){
            return $q->sellerOrderProduct()->whereHas('product', function($query)  {
                $query->where('seller_id', Auth::guard('seller')->user()->id);
            });
        },'customer','shipping','shipping.method','orderDetails.product'])->sellerOrder()->date()->search()->physicalOrder()->cancel()->whereHas('orderDetails', function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->orderBy('id', 'DESC')->paginate(site_settings('pagination_number',10));
        return view('seller.order.index', compact('title', 'orders'));
    }


    public function details($id)
    {
        $title = translate('Seller order details');
        $seller = Auth::guard('seller')->user();
        $order = Order::sellerOrder()->physicalOrder()->whereHas('orderDetails', function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->where('id', $id)->firstOrFail();

        $orderDeatils = OrderDetails::where('order_id', $order->id)->sellerOrderProduct()->whereHas('product', function($query) use ($seller){
            $query->where('seller_id', $seller->id);
        })->with('product')->get();

        $orderStatus  = OrderStatus::where('order_id', $id)->latest()->get();
        return view('seller.order.details', compact('title', 'order', 'orderDeatils','orderStatus'));
    }


    public function orderStatusUpdate(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required|in:2,3,4,5,7,1,0',
        ]);
        $seller = Auth::guard('seller')->user();
        $order = Order::sellerOrder()->physicalOrder()->whereHas('orderDetails', function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->where('id', $id)->firstOrFail();
        $orderDeatils = OrderDetails::where('order_id', $order->id)->sellerOrderProduct()->whereHas('product', function($query) use ($seller){
            $query->where('seller_id', $seller->id);
        })->get();


        if($order->status == 5) return back()->with('error',translate('This order has already been delivered'));
        if($order->status == 7) return back()->with('error',translate('This order has already been Returned'));

        $phone      = @$order->billingAddress ? @$order->billingAddress->phone : @$order->billing_information->phone;
        $email      = @$order->billingAddress ? @$order->billingAddress->email : @$order->billing_information->email;
        $first_name = @$order->billingAddress ? @$order->billingAddress->first_name : @$order->billing_information->first_name;
        $address    = @$order->billingAddress ? @$order->billingAddress->address->address : @$order->billing_information->address;

        $mailCode = [
            'order_number'     => $order->order_id,
            'time'             => Carbon::now(),
            'payment_status'   => $order->payment_status == Order::PAID ? 'Paid' :"Unpaid",
            'amount'           => show_amount($order->amount),
            'customer_phone'           => @$phone ?? 'N/A',
            'customer_email'           => @$email,
            'customer_name'            => @$first_name ?? "N/A",
            'customer_address'         => @$address ?? 'N/A',
        ];

        $user = (object)[
            "first_name" => $first_name,
            "email"      =>   $email,
        ];

        $order->status = $request->status;
        $order->save();
        if($order->status == Order::CANCEL){
            SendMailJob::dispatch($user,'ORDER_CANCEL',$mailCode);
        }
        elseif($order->status == Order::DELIVERED){
            SendMailJob::dispatch($user,'ORDER_DELIVERED',$mailCode);
        }
        elseif($order->status == Order::CONFIRMED){
            SendMailJob::dispatch($user,'ORDER_CONFIRMED',$mailCode);
        }


        foreach ($orderDeatils as $key => $value) {
            
            $value->status = $request->status;
            $value->save();

            if($request->status == Order::DELIVERED){

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

        if($order->status == 5){
            $order->payment_status = Order::PAID;
            $order->save();
        }


        $orderStatus = new OrderStatus();

        $orderStatus->order_id        = $id;

        $orderStatus->delivery_status = $request->status;

        if($request->delivery_note!='')  $orderStatus->delivery_note   = $request->delivery_note;


        $orderStatus->payment_status  = $request->status == 5 ? Order::PAID : Order::UNPAID ;

        $orderStatus->save();

        return back()->with('success',translate("Order status has been updated"));


       

    }



    public function printInvoice($id)
    {
        $title = translate('Print invoice');
         $seller = Auth::guard('seller')->user();
        $order = Order::sellerOrder()->physicalOrder()->whereHas('orderDetails', function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->where('id', $id)->firstOrFail();
        $orderDeatils = OrderDetails::where('order_id', $order->id)->sellerOrderProduct()->whereHas('product', function($query) use ($seller){
            $query->where('seller_id', $seller->id);
        })->with('product')->get();
        return view('seller.order.print', compact('title', 'order', 'orderDeatils'));
    }
}
