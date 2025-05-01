<?php

namespace App\Http\Controllers\Api;

use App\Enums\BrandStatus;
use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Enums\ProductType;
use App\Http\Controllers\Controller;
use App\Http\Resources\CamapaignProductResource;
use App\Http\Resources\DigitalProductCollection;
use App\Http\Resources\DigitalProductResource;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\CampaignProduct;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductRating;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{


    /**
     * Get Product details
     *
     * @param string $uid
     * @param string|null $camp_uid
     * @return JsonResponse
     */
    public function view(string $uid ,string $camp_uid = null): JsonResponse {
 
        $campaignProduct =  null;
        $product         = Product::with([
                                           'campaigns','gallery','review','order','stock','order','rating','shippingDelivery','shippingDelivery.shippingDelivery','seller','seller.sellerShop'
                                        ])
                                        ->whereIn('status', [ProductStatus::NEW, ProductStatus::PUBLISHED])
                                        ->where('product_type', ProductType::PHYSICAL_PRODUCT)
                                        ->where('uid', $uid)
                                        ->first();

        if(!$product) return api(['errors' => ['Product Not found']])->fails(__('response.fail'));

        $relatedProduct  = Product::with([
                                         'gallery','review','order','stock','order','rating','shippingDelivery','shippingDelivery.shippingDelivery','seller','seller.sellerShop'
                                         ])
                                ->where('category_id', $product->category_id)
                                ->where('id',"!=", $product->id)
                                ->where(fn (Builder $query) :Builder =>
                                      $query->whereNull('seller_id')
                                            ->whereIn('status', [ProductStatus::NEW, ProductStatus::PUBLISHED])
                                            ->orWhereNotNull('seller_id')
                                            ->whereIn('status', [ProductStatus::PUBLISHED]))
                                ->where('product_type', ProductType::PHYSICAL_PRODUCT)
                                ->take(10)
                                ->get();
     


        if($camp_uid){
            $campaign             =  Campaign::where('uid',$camp_uid)->first();
            $campaignProduct      =  CampaignProduct::where('campaign_id', $campaign->id)
                                        ->where('product_id',$product->id)
                                        ->first();

            return api([
                'product'         => (new CamapaignProductResource($campaignProduct)),
                'related_product' => new ProductCollection($relatedProduct),
            ])->success(__('response.success'));
        }

 
        return api([
            'product'         =>  (new ProductResource($product)),
            'related_product' => new ProductCollection($relatedProduct),
        ])->success(__('response.success'));
    

    }


    /**
     * Digital Product  Details
     *
     * @param string $uid
     * @return JsonResponse
     */
    public function digitalProductDetails(string $uid ): JsonResponse {

        $digital_product = Product::with(['digitalProductAttribute','seller','seller.sellerShop'])
                                ->where('uid', $uid)->digital()
                                ->first();

        if(!$digital_product)  return api(['errors' => ['Product Not found']])->fails(__('response.fail'));
                        
        $relatedProduct  = Product::with(['digitalProductAttribute','seller','seller.sellerShop'])->digital() 
                                ->where('category_id', $digital_product->category_id)
                                ->take(10)
                                ->get();
        return api([
            'digital_product' => (new DigitalProductResource($digital_product)),
            'related_product' => new DigitalProductCollection($relatedProduct),
        ])->success(__('response.success'));

    }

    /**
     * Search Product
     * 
     * @return JsonResponse
     */
     public function search(Request $request) : JsonResponse {
   
        $products = Product::with([
            'gallery','review','order','stock','order','rating','shippingDelivery','shippingDelivery.shippingDelivery','seller','seller.sellerShop'
            ])->where(fn (Builder $query) :Builder =>
                                            $query->whereNull('seller_id')
                                                ->whereIn('status', [ProductStatus::NEW, ProductStatus::PUBLISHED])
                                                ->orWhereNotNull('seller_id')
                                                ->whereIn('status', [ProductStatus::PUBLISHED]))
                            ->where('product_type', ProductType::PHYSICAL_PRODUCT)
                            ->when($request->name ,function($query) use($request){
                                $query->where('name', 'like', "%$request->name%");
                            })
                            ->when($request->category_uid ,function($query) use($request){
                                $category = Category::where('status', CategoryStatus::ACTIVE)
                                                      ->where('uid', $request->category_uid)
                                                      ->first();

                                $query->where('category_id', $category->id );
                            })
                            ->when($request->brand_uid ,function($query) use($request){
                                $brand = Brand::where('status', BrandStatus::ACTIVE)
                                              ->where('uid',$request->brand_uid )
                                              ->first();
                                $query->where('brand_id', $brand->id );
                            })->paginate(site_settings('pagination_number',10))
                            ->appends($request->all());
     


        return api([
            'products' =>  (new ProductCollection($products)),
        ])->success(__('response.success'));

     }

    /**
     * Product Review 
     *
     * @param Request $request
     */
    public function review(Request $request) : JsonResponse {

        $product                   = Product::where('uid', $request->product_uid)
                                                ->first();
        
        if(!$product) return api(['errors' => ['Product Not found']])->fails(__('response.fail')); 

        $user                      = auth()->user();
        $productRating             = new ProductRating();
        $productRating->user_id    = $user->id;
        $productRating->product_id = $product->id;
        $productRating->rating     = $request->rate;
        $productRating->review     = $request->review;
        $productRating->save();

        return api(['message' => translate('Product Reviewd Successfully')])->success(__('response.success'));

    }



    /**
     * Get all products
     *
     * @return JsonResponse
     */
    public function products() : JsonResponse{


        $products = Product::with((['gallery','review','order','stock','order','rating','shippingDelivery','shippingDelivery.shippingDelivery','seller','seller.sellerShop']))->physical()
                        ->where(fn (Builder $query) :Builder =>
                        $query->whereNull('seller_id')
                                ->whereIn('status', [ProductStatus::NEW, ProductStatus::PUBLISHED])
                                ->orWhereNotNull('seller_id')
                                ->whereIn('status', [ProductStatus::PUBLISHED]))
                        ->when(request()->input('name') ,function($query) {
                            $query->where('name', 'like', "%".request()->input('name')."%");
                        })
                        ->when(request()->input('category_uid') ,function($query) {
                            $category = Category::where('status', CategoryStatus::ACTIVE)->where('uid',request()->input('category_uid'))->first();
                            $query->where('category_id', $category->id );
                        })
                        ->when(request()->input('brand_uid') ,function($query) {
                            $brand = Brand::where('status', BrandStatus::ACTIVE)->where('uid',request()->input('brand_uid') )->first();
                            $query->where('brand_id', $brand->id );
                        })->when(request()->input('sort_by') ,function($query) {
                            if(request()->input('sort_by') == "hightolow"){
                                $query->orderByRaw("CASE WHEN discount!=0 THEN discount ELSE price END DESC");
                            }
                            elseif(request()->input('sort_by') == "lowtohigh"){
                                $query->orderByRaw("CASE WHEN discount!=0 THEN discount ELSE price END ASC");
                            }
                            else{
                                $query->latest();
                            }
                        })->when(!request()->input('sort_by') ,function($query) {
                                $query->latest();
                        })->when(request()->input('search_min') &&  request()->input('serach_max') ,function($query) {
                            $query->whereBetween('price', [request()->input('search_min',0), request()->input('serach_max')]);
                        })->paginate(paginate_number(12))
                        ->appends(request()->all());

        return api([
            'products' =>  (new ProductCollection($products)),
        ])->success(__('response.success'));

    }


    



}
