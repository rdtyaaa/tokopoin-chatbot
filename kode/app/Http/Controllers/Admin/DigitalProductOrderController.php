<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\DigitalProductAttribute;

class DigitalProductOrderController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permissions:view_order'])->only('inhouse','search','seller','digitalOrderDetails','shipped','delivered','cancel','printInvoice');
    }
    public function inhouse()
    {
        $title = translate('In-house digital product orders');
        $orders = Order::inhouseOrder()->search()->date()->digitalOrder()->orderBy('id', 'DESC')->with('customer')->paginate(site_settings('pagination_number',10));
        return view('admin.digital_order.index', compact('title', 'orders'));
    }

    public function search(Request $request, $scope)
    {

        $customerSearch = $request->customer_search;
        $orderSearch = $request->order_search;
        $title = '';
        $orders = Order::inhouseOrder()->search()->date()->digitalOrder()->orderBy('id', 'DESC');

        if( $customerSearch && $orderSearch){
            $title = "Order Search by -" . $customerSearch .' and '. $orderSearch;
            $orders->whereHas('customer', function($q) use ($customerSearch){
                $q->where('name','like',"%$customerSearch%");
            })->Where('order_id', 'like', "%$orderSearch%");
        }
         if($customerSearch){
            $title = "Order Search by -" . $customerSearch;
            $orders->whereHas('customer', function($q) use ($customerSearch){
                $q->where('name','like',"%$customerSearch%");
            });
        } if($orderSearch) {
            $title = "Order Search by -" . $orderSearch;
            $orders->Where('order_id', 'like', "%$orderSearch%");
        }

        if($scope == 'inhouse'){

           $orders->inhouseOrder();
        }
        else if($scope == 'seller'){

            $orders->sellerOrder();
        }

         $orders = $orders->with('customer')->paginate(site_settings('pagination_number',10));

        return view('admin.digital_order.index', compact('title', 'orders', 'orderSearch', 'customerSearch'));
    }

    public function seller()
    {
        $title = translate('Seller digital product orders');
        $orders = Order::sellerOrder()->digitalOrder()->search()->date()->orderBy('id', 'DESC')->with('customer', 'digitalProductOrder','digitalProductOrder.product','digitalProductOrder.product.seller')->paginate(site_settings('pagination_number',10));
        return view('admin.digital_order.index', compact('title', 'orders'));
    }

    public function digitalOrderDetails($order_id)
    {
        $title = translate('Digital order details');
        $order = Order::where('id', $order_id)->digitalOrder()->with('paymentMethod')->firstOrFail();
        $orderDetail = OrderDetails::where('order_id', $order->id)->first();
        $digitalProductAttributes = DigitalProductAttribute::where('id', $orderDetail->digital_product_attribute_id)->first();
        return view('admin.digital_order.details', compact('title', 'orderDetail','order','digitalProductAttributes'));
    }
    public function orderStatusUpdate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:order_details,id',
            'status' => 'required|in:2,3,4,5,6',
        ]);
        $orderDeatils = OrderDetails::where('id', $request->id)->first();
        $orderDeatils->status = $request->status;
        $orderDeatils->save();
 
        return back()->with('success',translate('Order product status has been updated'));
    }
}
