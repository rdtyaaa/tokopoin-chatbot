<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Settings\NotificationType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Order;
use App\Traits\Notify;
use Illuminate\Notifications\Notifiable;


class SellerProductController extends Controller
{


    use Notify;
    public function __construct(){
        $this->middleware(['permissions:view_seller']);
    }

    public function index()
    {
        $title = translate('Seller all products');
        $products = Product::latest()
                                ->sellerProduct()
                                ->search()
                                ->physical()
                                ->with('seller', 'category', 'order')
                                ->paginate(site_settings('pagination_number',10));
        return view('admin.seller_product.index', compact('title', 'products'));
    }

    public function new()
    {
        $title = translate('Seller new products');
        $products = Product::sellerProduct()
                                ->physical()
                                ->search()
                                ->new()
                                ->latest()
                                ->with('seller', 'category', 'order')
                                ->paginate(site_settings('pagination_number',10));
        return view('admin.seller_product.index', compact('title', 'products'));
    }

    public function approved()
    {
        $title = translate('Seller approved products');
        $products = Product::sellerProduct()
                                     ->physical()
                                     ->search()
                                     ->published()
                                     ->latest()
                                     ->with('seller', 'category', 'order')
                                     ->paginate(site_settings('pagination_number',10));

        return view('admin.seller_product.index', compact('title', 'products'));
    }

    public function refuse()
    {
        $title = translate('Seller cancel products');
        $products = Product::sellerProduct()
                                    ->physical()
                                    ->search()
                                    ->inactive()
                                    ->latest()
                                    ->with('seller', 'category', 'order')
                                    ->paginate(site_settings('pagination_number',10));
        return view('admin.seller_product.index', compact('title', 'products'));
    }

    public function trashed()
    {
        $title = translate('Trashed products');
        $products = Product::with(['seller','category','order'])
                                    ->sellerProduct()
                                    ->search()
                                    ->onlyTrashed()
                                    ->paginate(site_settings('pagination_number',10));

        return view('admin.seller_product.index', compact('title', 'products'));
    }

    public function details($id)
    {
        $title = translate('Product details');
        $product = Product::with(['shippingDelivery'=>function($q){
            return $q->with(['shippingDelivery']);
        }])->withCount('rating')->whereNotNull('seller_id')->where('id', $id)->firstOrFail();
        return view('admin.seller_product.details', compact('title','product'));
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:products,id',
        ]);
        $product = Product::whereNotNull('seller_id')->where('id', $request->id)->delete();
        return back()->with('success',translate("Seller product has been deleted"));
    }

    public function approvedBy(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:products,id',
        ]);


        $product = Product::sellerProduct()->physical()->where('id', $request->id)->firstOrFail();
        $product->status = Product::PUBLISHED;
        $product->save();
        return back()->with('success',translate("Seller product has been approved"));
    }

    public function cancelBy(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:products,id',
        ]);

        $product = Product::sellerProduct()->physical()->where('id', $request->id)->firstOrFail();
        $product->status = Product::INACTIVE;
        $product->save();
        return back()->with('success',translate("Seller product has been inactive"));
    }

    public function restore(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:products,id'
        ]);
        $product = Product::whereNotNull('seller_id')->onlyTrashed()->where('id', $request->id)->restore();
        return back()->with('success',translate("Seller product has been restore"));
    }




    public function singleProductAllOrder($id)
    {
        $product = Product::sellerProduct()->physical()->where('id', $id)->firstOrFail();
        $title = ucfirst($product->name). " all orders log";
        $orders = Order::sellerOrder()->physicalOrder()->whereHas('orderDetails', function($q) use ($product){
            $q->where('product_id', $product->id);
        })->orderBy('id', 'DESC')->with('customer')->paginate(site_settings('pagination_number',10));
        return view('admin.seller_order.index', compact('title', 'orders'));
    }

    public function singleProductPlacedOrder($id)
    {
        $product = Product::sellerProduct()->physical()->where('id', $id)->firstOrFail();
        $title = ucfirst($product->name). " placed orders log";
        $orders = Order::sellerOrder()->physicalOrder()->whereHas('orderDetails', function($q) use ($product){
            $q->where('product_id', $product->id)->where('status', 1);
        })->orderBy('id', 'DESC')->with('customer')->paginate(site_settings('pagination_number',10));
        return view('admin.seller_order.index', compact('title', 'orders'));
    }

    public function singleProductDeliveredOrder($id)
    {
        $product = Product::sellerProduct()->physical()->where('id', $id)->firstOrFail();
        $title = ucfirst($product->name). " delivered orders log";
         $orders = Order::sellerOrder()->physicalOrder()->whereHas('orderDetails', function($q) use ($product){
            $q->where('product_id', $product->id)->where('status', 5);
        })->orderBy('id', 'DESC')->with('customer')->paginate(site_settings('pagination_number',10));
        return view('admin.seller_order.index', compact('title', 'orders'));
    }


    public function sellerProductUpdateStatus(Request $request)
    {
        $product = Product::with(['seller'])->where('id', $request->id)->first();
        $product->status = $request->status;
        $product->save();


        #FIREBASE NOTIFICATIONS
        if($product->seller &&  $product->seller->fcm_token){
            $payload = (object) [
                "title"               => translate('Product'),
                "message"             => translate('Your product status has been updated by system admin'),
                "product_uid"         => $product->uid,
                "type"                => NotificationType::PRODUCT_UPDATE->value,
            ];
            $this->fireBaseNotification($product->seller->fcm_token,$payload);
        }

        return back()->with('success',translate("Product status has been successfully updated"));
    }
}
