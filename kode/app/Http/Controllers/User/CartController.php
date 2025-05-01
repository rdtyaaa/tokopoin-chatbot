<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Validator;
use App\Http\Services\Frontend\ProductService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    protected ProductService $productService;

    protected ? User $user;
    public function __construct()
    {
        $this->productService = new ProductService();
        $this->middleware(function ($request, $next) {
            $this->user = auth_user('web');

            return $next($request);
        });
    }

    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:products,id',
        ],[
            'product_id.required' => "Product must be selected",
            'product_id.exists' => "Product doesn't exists",
        ]);

        if ($validator->fails()) return response()->json(['validation' => $validator->errors()]);

        $response = $this->productService->addToCart($request);
        return response()->json($response);
    }

    public function getCartData()
    {
        $response = $this->productService->getCart($this->user);
        return view('frontend.partials.cart_item', [
            'items' => $response['latest_item'],
            'subtotal' => $response['sub_total'],
        ]);
    }

    public function totalCartAmount(){
        $response = $this->productService->getCart($this->user);
        return  short_amount($response['sub_total'],false);
    }




    /**
     * Get total cart items count
     *
     * @return integer
     */
    public function cartTotalItem() : int {

        return $this->productService->getCartItem($this->user)->count();
    }



    /**
     * Delete a item form cart
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request) : JsonResponse{

        $isDeleted  = $this->productService->deleteCartItem($request->input('id'),$this->user);

        $items      = $this->productService->getCartItem($this->user);
        if($isDeleted){
            session()->forget('coupon');
        }



        return response()->json([
            'status'        =>  $isDeleted ,
            'message'       =>  $isDeleted  ? translate('The product item has been deleted from the cart')
                                      : translate('Cart item not found'),

            'order_summary' => view('frontend.partials.order_summary', compact('items'))->render()

        ]);
    }




    /**
     * Get cart view
     *
     * @return View | array
     */
    public function viewCart() : View | array {
        $title = translate('Shopping Cart');

        $items = $this->productService->getCartItem($this->user);

        if(request()->ajax()){
            return [
                'html' => view('frontend.ajax.cart_list', compact('title', 'items'))->render()
            ];
        }
        return view('frontend.view_cart', compact('title', 'items'));
    }


    public function updateCart(Request $request)
    {
        $this->validate($request,[
            'id' => 'required|exists:carts,id',
            'quantity' => 'required|integer|min:0',
        ]);
        return $this->productService->updateCartItem($request);
    }

}
