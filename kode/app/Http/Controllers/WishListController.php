<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\Frontend\ProductService;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class WishListController extends Controller
{

    protected ? User $user;
    public function __construct(private ProductService $productService){
        $this->middleware(function ($request, $next) {
            $this->user = auth_user('web');
            return $next($request);
        });
    }


    /**
     * Add and remove to w
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse{

        if(!$this->user) return response()->json(['status' => false ,'message' => translate("Please login first")]);
        
        $product = Product::where('id', $request->input('product_id'))
                                              ->first();

        if(!$product) return  response()->json(['status' => false ,'message' => translate("Product doesn't exist")]);

        return  response()->json($this->productService->wishList($product,$this->user));
    }


    public function wishItemCount()
    {
        return $this->productService->wishListItems();
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:wish_lists,id',
        ]);
        return $this->productService->wishListItemsDelete($request);
    }

    public function compareItemCount()
    {
        return $this->productService->compareCount();
    }
}
