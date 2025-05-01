<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\DigitalProductAttribute;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DigitalProductOrderController extends Controller
{
    public function index()
    {
        $title = translate('Digital product order');
        $seller = Auth::guard('seller')->user();
        $orders = Order::sellerOrder()->digitalOrder()->whereHas('orderDetails', function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->orderBy('id', 'DESC')->with('customer')->paginate(site_settings('pagination_number',10));
        return view('seller.digital_order.index', compact('title', 'orders'));
    }

    public function search(Request $request)
    {


        $request->validate([
            'searchFilter'=>'required',
        ]);

        if($request->option_value == 'Select Menu'){
            return  back()->with('error',translate("Please Select A Value Form Select Box"));
        }


        $search = $request->searchFilter;
        $title = "Seller order search by -" . $search;
        $seller = Auth::guard('seller')->user();
        $orders = Order::with(['customer'])->sellerOrder()->digitalOrder()->whereHas('orderDetails', function($q) use ($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        });

        if($request->option_value == 'amount'){
            $orders->where('amount', 'like', "%$search%");
        }
        if($request->option_value == 'order_number'){
            $orders->where('order_id', 'like', "%$search%");
        }
        if($request->option_value == 'customer_name'){
            $orders->whereHas('customer', function($q) use ($search){
                $q->where('name','like',"%$search%");
            });
        }
        if($request->option_value == 'customer_email'){
            $orders->whereHas('customer', function($q) use ($search){
                $q->where('email','like',"%$search%");
            });
        }

        $title = "Search by -" . $search;
        $orders = $orders->orderBy('id','desc')->paginate(site_settings('pagination_number',10));
        return view('seller.digital_order.index', compact('title','orders','search'));
    }

    public function dateSearch(Request $request)
    {
         $this->validate($request, [
            'date' => 'required',
        ]);
        $seller = Auth::guard('seller')->user();
        $searchDate = explode('-',$request->date);
        $firstDate = $searchDate[0];
        $lastDate = $searchDate[1];
        $matchDate = "/\d{2}\/\d{2}\/\d{4}/";
        if ($firstDate && !preg_match($matchDate,$firstDate)) {
            return  back()->with('error',translate("Invalid order search date format"));
        }
        if ($lastDate && !preg_match($matchDate,$lastDate)) {
            return  back()->with('error',translate("Invalid order search date format"));
        }
        if ($firstDate) {
            $orders =  Order::sellerOrder()->digitalOrder()->whereHas('orderDetails', function($q) use ($seller){
                $q->whereHas('product', function($query) use ($seller){
                    $query->where('seller_id', $seller->id);
                });
            })->whereDate('created_at',Carbon::parse($firstDate));
        }
        if($lastDate){
            $orders =  Order::sellerOrder()->digitalOrder()->whereHas('orderDetails', function($q) use ($seller){
                $q->whereHas('product', function($query) use ($seller){
                    $query->where('seller_id', $seller->id);
                });
            })->whereDate('created_at','>=',Carbon::parse($firstDate))->whereDate('created_at','<=',Carbon::parse($lastDate));
        }
        $orders = $orders->orderBy('id','desc')->paginate(site_settings('pagination_number',10));
        $searchDate = $request->date;
        $title = 'orders search by ' . $searchDate;
        return view('seller.digital_order.index', compact('title','orders','searchDate'));
    }

    public function details($id)
    {
        $title = translate('Digital product order details');
        $seller = Auth::guard('seller')->user();
        $order = Order::sellerOrder()->digitalOrder()->whereHas('orderDetails',function($q)use($seller){
            $q->whereHas('product', function($query) use ($seller){
                $query->where('seller_id', $seller->id);
            });
        })->where('id', $id)->with('paymentMethod')->firstOrFail();

        $orderDetail = OrderDetails::where('order_id', $order->id)->first();

        $digitalProductAttributes = DigitalProductAttribute::where('id', $orderDetail->digital_product_attribute_id)->first();

        return view('seller.digital_order.details', compact('title', 'order', 'orderDetail', 'digitalProductAttributes'));
    }
}
