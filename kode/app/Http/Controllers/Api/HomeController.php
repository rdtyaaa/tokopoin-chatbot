<?php

namespace App\Http\Controllers\Api;

use App\Enums\BrandStatus;
use App\Enums\CategoryStatus;
use App\Enums\ProductFeaturedStatus;
use App\Enums\ProductStatus;
use App\Enums\ProductSuggestedStatus;
use App\Enums\ProductType;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\BannerCollection;
use App\Http\Resources\BrandCollection;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CampaignCollection;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CouponCollection;
use App\Http\Resources\CurrencyCollection;
use App\Http\Resources\DigitalProductCollection;
use App\Http\Resources\FlashDealResource;
use App\Http\Resources\FrontendCollection;
use App\Http\Resources\HomeCategoryCollection;
use App\Http\Resources\LanguageCollection;
use App\Http\Resources\LanguageResource;
use App\Http\Resources\PagesCollection;
use App\Http\Resources\PaymentMethodCollection;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\SellerCollection;
use App\Http\Resources\SellerResource;
use App\Http\Resources\SettingResource;
use App\Http\Resources\ShippingCollection;
use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\FlashDeal;
use App\Models\Follower;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\MenuCategory;
use App\Models\PageSetup;
use App\Models\Product;
use App\Models\Seller;
use App\Models\ShippingDelivery;
use Illuminate\Http\JsonResponse;
use App\Enums\Settings\CacheKey;
use App\Enums\Settings\GlobalConfig;
use App\Http\Resources\CityCollection;
use App\Http\Resources\CountryCollection;
use App\Http\Resources\CurrencyResource;
use App\Http\Resources\PaymentLogResource;
use App\Http\Resources\RewardPointCollection;
use App\Http\Resources\ZoneCollection;
use App\Http\Resources\ZoneResource;
use App\Models\City;
use App\Models\Country;
use App\Models\PaymentLog;
use App\Models\PlanSubscription;
use App\Models\RewardPointLog;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
class HomeController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse{

        $categories       = Category::where('status', CategoryStatus::ACTIVE)->get();

        $brands           = Brand::where('status', BrandStatus::ACTIVE)->get();

        $todayDeals       = Product::with(
                                        [
                                            'review','review.customer','stock','brand','category','gallery','shippingDelivery','shippingDelivery.shippingDelivery',
                                            'shippingDelivery.shippingDelivery.method','order','seller',
                                            'seller.sellerShop'
                                        ])
                                    ->where('featured_status', ProductFeaturedStatus::YES)
                                    ->where('product_type', ProductType::PHYSICAL_PRODUCT)
                                    ->paginate(site_settings('pagination_number',10));
        
        $banners          =  Cache::get(CacheKey::BANNERS->value);
        
 
        $newArrival       = Product::inhouseProduct()->with(
                                    [
                                        'review','review.customer','stock','brand','category','gallery','shippingDelivery','shippingDelivery.shippingDelivery',
                                        'shippingDelivery.shippingDelivery.method','order','seller'
                                        ,'seller.sellerShop'
                                    ])->where('status', ProductStatus::NEW)
                                        ->where('product_type', ProductType::PHYSICAL_PRODUCT)
                                        ->paginate(site_settings('pagination_number',10));

        $bestSelling      = Product::with(
                                        [
                                            'review','review.customer','stock','brand','category','gallery','shippingDelivery','shippingDelivery.shippingDelivery',
                                            'shippingDelivery.shippingDelivery.method','order','seller'
                                            ,'seller.sellerShop'
                                        ])
                                       ->where(fn (Builder $query) :Builder =>
                                            $query->whereNull('seller_id')
                                                    ->whereIn('status', [ProductStatus::NEW, ProductStatus::PUBLISHED])
                                                    ->orWhereNotNull('seller_id')
                                                    ->whereIn('status', [ProductStatus::PUBLISHED])
                                        )
                                    ->where('product_type', ProductType::PHYSICAL_PRODUCT)
                                    ->where('best_selling_item_status',ProductStatus::BESTSELLING)
                                    ->paginate(site_settings('pagination_number',10));

        $digital_products = Product::with(['digitalProductAttribute','seller','seller.sellerShop'])
                                    ->digital()
                                    ->where(fn (Builder $query) :Builder =>
                                        $query->whereNull('seller_id')
                                                ->whereIn('status', [ProductStatus::NEW, ProductStatus::PUBLISHED])
                                                ->orWhereNotNull('seller_id')
                                                ->whereIn('status', [ProductStatus::PUBLISHED]))
                                    ->latest()
                                    ->whereHas('category', fn(Builder $query) :Builder =>
                                        $query->active()
                                    )->paginate(site_settings('pagination_number',10));



        $suggestedProducts = Product::with([
                                                'review','review.customer','stock','brand','category','gallery','shippingDelivery','shippingDelivery.shippingDelivery','shippingDelivery.shippingDelivery.method','order','seller',
                                                'seller.sellerShop'
                                            ])
                                            ->whereIn('status', [ProductStatus::NEW, ProductStatus::PUBLISHED])
                                            ->where('is_suggested', ProductSuggestedStatus::YES)
                                            ->where('product_type', ProductType::PHYSICAL_PRODUCT)
                                            ->paginate(site_settings('pagination_number',10));
        
        $now              = Carbon::now()->toDateTimeString();
        $campaigns        = Campaign::where('status',Status::ACTIVE)
                                    ->where('start_time',"<=",$now)
                                    ->where('end_time',">=",$now)
                                    ->paginate(site_settings('pagination_number',10));
        
        $sellers          = Seller::active()
                                ->with(['follow'])
                                ->withCount(['product' => fn(Builder $q): Builder => $q->whereIn('status', [ProductStatus::PUBLISHED])])
                                ->whereHas('sellerShop', fn(Builder $query): Builder => 
                                    $query->where('status', Status::ACTIVE)
                                )->whereHas('subscription',fn(Builder $q): Builder =>
                                    $q->where('status', PlanSubscription::RUNNING)
                                )->with('sellerShop')->paginate(site_settings('pagination_number',10));


        
        $flashDeal      = FlashDeal::where('status',Status::ACTIVE)
                                ->where('start_date',"<=",$now)
                                ->where('end_date',">=",$now)
                                ->first();
    
   
        $homeCategories = MenuCategory::whereHas('category',fn(Builder $q): Builder => $q->whereHas('product'))
                                   ->with([
                                            'category','category.product','category.product.review','category.product.review.customer','category.product.stock','category.product.brand','category.product.category','category.product.gallery'
                                        ])
                                        ->orderBy('serial')
                                        ->get();

        return api([
            'shops'              => new SellerCollection($sellers),
            'banners'            => new BannerCollection($banners),
            'categories'         => new CategoryCollection($categories),
            'brands'             => new BrandCollection($brands),
            'today_deals'        => new ProductCollection($todayDeals),
            'suggested_products' => new ProductCollection($suggestedProducts),
            'new_arrival'        => new ProductCollection($newArrival),
            'best_selling'       => new ProductCollection($bestSelling),
            'campaigns'          => new CampaignCollection($campaigns),
            'digital_product'    => new DigitalProductCollection($digital_products),
            'flash_deals'        => $flashDeal ? new FlashDealResource($flashDeal) : (object)[],
            'home_category'      => new HomeCategoryCollection($homeCategories), 
        ])->success(__('response.success'));
        
    }

    /**
     * Get all brand products
     * @param string $uid
     * @return JsonResponse
     */
    public function brandProduct(string $uid): JsonResponse
    {

        $brand    = Brand::where('status', BrandStatus::ACTIVE)
                            ->where('uid', $uid)
                            ->first();
        
        if(!$brand) return api(['errors' => ['Brand not found']])->fails(__('response.fail'));

        $products = Product::with(
                     [
                        'review','review.customer','stock','brand','category','gallery','shippingDelivery','shippingDelivery.shippingDelivery','shippingDelivery.shippingDelivery.method','order','seller','seller.sellerShop'
                     ])->where(fn (Builder $query) :Builder =>
                            $query->whereNull('seller_id')
                                        ->whereIn('status', [ProductStatus::NEW, ProductStatus::PUBLISHED])
                                        ->orWhereNotNull('seller_id')
                                        ->whereIn('status', [ProductStatus::PUBLISHED]))
                        ->where('product_type', ProductType::PHYSICAL_PRODUCT)
                        ->where('brand_id', $brand->id)->paginate(site_settings('pagination_number',10));

        return api([
            'brand'    => new BrandResource($brand),
            'products' => new ProductCollection($products),
        ])->success(__('response.success'));
    }


    /**
     * Get all Category product
     * 
     * @param string $uid
     * @return JsonResponse
     */
    public function getCategoryByProduct(string $uid): JsonResponse
    {
        $category = Category::where('status', CategoryStatus::ACTIVE)
                                                ->where('uid', $uid)
                                                ->first();
        if(!$category) return api(['errors' => ['Category not found']])->fails(__('response.fail'));

        $products = Product::with(['review','review.customer','stock','brand','category','gallery','shippingDelivery','shippingDelivery.shippingDelivery','shippingDelivery.shippingDelivery.method','order','seller','seller.sellerShop'])->where(fn (Builder $query) :Builder =>
                            $query->whereNull('seller_id')
                                    ->whereIn('status', [ProductStatus::NEW, ProductStatus::PUBLISHED])
                                    ->orWhereNotNull('seller_id')
                                    ->whereIn('status', [ProductStatus::PUBLISHED]))
                            ->where('product_type', ProductType::PHYSICAL_PRODUCT)
                            ->where('category_id', $category->id)
                            ->orWhere('sub_category_id',$category->id)
                            ->paginate(site_settings('pagination_number',10));

        return api([
            'category' => new CategoryResource($category),
            'products' => new ProductCollection($products),
        ])->success(__('response.success'));
    }




    public function getPaymentLog(int | string $trx_code) : JsonResponse {

        $paymentLog  = PaymentLog::where('trx_number', $trx_code)
                            ->first();
        if(!$paymentLog)  return api(['errors' => ['No log found']])->fails(__('response.fail'));

        return api(
            [
                'payment_log'  => new PaymentLogResource($paymentLog),
            ]
         )->success(__('response.success'));


    }


    /**
     * Campagin details
     * 
     * @param string $uid
     * @return JsonResponse
     */

     public function campaignDetails(string $uid): JsonResponse  {

         $campaign = Campaign::with(
                        [
                            'products','products.review','products.review.customer','products.stock','products.brand','products.category','products.gallery','products.shippingDelivery','products.shippingDelivery.shippingDelivery','products.shippingDelivery.shippingDelivery.method','products.order','products.seller','products.seller.sellerShop'
                        ])
                       ->where('status',Status::ACTIVE)
                       ->where('uid', $uid)
                       ->first();

         if(!$campaign)  return api(['errors' => ['Camaign Not found']])->fails(__('response.fail'));

         return api([
             'campaign' => new CampaignResource($campaign),
             'products' => new ProductCollection($campaign->products()->paginate(site_settings('pagination_number',10))),
         ])->success(__('response.success'));
     }





     
     /**
      * Get all configuration
      *
      * @return JsonResponse
      */
      public function config(): JsonResponse {

        $paymentMethods           = active_payment_methods();

        $manualPaymentMethods     = active_manual_payment_methods();


        $languages          = Language::active()->get();

        $defaultLanguage    = Language::default()->first();

        $currencies         = Currency::active()->get();

        

        $frontends          = Frontend::active()->get();

        $coupons            = Coupon::valid()->get();

        $ShippingDeliveries = ShippingDelivery::active()
                                        ->orderBy('id', 'DESC')
                                        ->get();

        $pages              = PageSetup::active()->get();

        $countries          = Country::visible()->with(['states'=>fn(HasMany $q) => $q->visible(),'states.cities'=>fn(HasMany $q) => $q->visible()])->get();

        $cities          = City::visible()->get();


        $zones = Zone::with(['countries'=>fn(BelongsToMany $q) => $q->visible()])->active()->get();

          return api([
            "phone_codes"             => GlobalConfig::TELEPHONE_CODES,
            'countries'               => new CountryCollection($countries),
            'cities'                  => new CityCollection($cities),
            'settings'                => new SettingResource(general_setting()),
            'pages'                   => new PagesCollection($pages),
            'payment_methods'         => new PaymentMethodCollection($paymentMethods),
            'manual_payment_methods'  => new PaymentMethodCollection($manualPaymentMethods),
            'languages'               => new LanguageCollection($languages),
            'default_language'        => new LanguageResource($defaultLanguage),
            'currency'                => new CurrencyCollection($currencies),
            'default_currency'        => new CurrencyResource(default_currency()),
            'coupons'                 => new CouponCollection($coupons),
            'frontend_section'        => new FrontendCollection($frontends),
            'shipping_data'           => new ShippingCollection($ShippingDeliveries),
            'shipping_zones'          => new ZoneCollection($zones),

          ])->success(__('response.success'));
      }

      
      /**
       * translate a static  word
       */
      public function translate($keyword):JsonResponse{

        return api([
            'keyword' => translate($keyword),
        ])->success(__('response.success'));
      }



    /**
     * Seller shop api
     *
     * @return JsonResponse
     */
    public function shop() :JsonResponse {
        
        $sellers = Seller::with(['follow'])->active()
                       ->withCount(['product' => fn(Builder $q): Builder => $q->whereIn('status', [ProductStatus::PUBLISHED])])
                        ->whereHas('sellerShop', fn(Builder $query): Builder =>
                            $query->where('status',  Status::ACTIVE)
                        )->whereHas('subscription',fn(Builder $q): Builder =>
                            $q->where('status', PlanSubscription::RUNNING)
                        )->with('sellerShop')->latest()->get();

  
        return api([
            'shops'    => new SellerCollection($sellers),
        ])->success(__('response.success'));

    }


 
      
    /**
     * Seller shop visit
     * 
     * @param int |string $id
     *
     * @return JsonResponse
     */
    public function shopVisit(int | string $id) :JsonResponse {

        $seller            = Seller::with(['product','follow'])
                                        ->active()
                                        ->whereHas('sellerShop')
                                        ->where('id', $id)->first();

        if(!$seller) return api(['errors' => ['Shop Not found']])->fails(__('response.fail'));

        $sellers           = Seller::active()
                                   ->withCount(['product' => fn(Builder $q): Builder => $q->whereIn('status', [ProductStatus::PUBLISHED])])
                                    ->latest()
                                    ->whereHas('sellerShop', fn(Builder $query): Builder =>
                                        $query->where('status',  Status::ACTIVE)
                                    )->whereHas('subscription',fn(Builder $q): Builder =>
                                        $q->where('status', PlanSubscription::RUNNING)
                                    )->with(['sellerShop','follow'])
                                    ->where('id','!=',$id)
                                    ->take(10)
                                    ->get();
            

        $products          = Product::with(
                                     [
                                        'seller','review','review.customer','stock','brand','category','gallery','shippingDelivery','shippingDelivery.shippingDelivery','shippingDelivery.shippingDelivery.method','order','seller','seller.sellerShop'
                                     ])
                                    ->latest()
                                    ->where('seller_id',$id)
                                    ->whereIn('status', [ProductStatus::PUBLISHED])
                                    ->where('product_type', ProductType::PHYSICAL_PRODUCT)
                                    ->whereHas('category', fn(Builder $query) :Builder =>
                                        $query->active()
                                    )
                                    ->paginate(site_settings('pagination_number',10));

        $digital__products =  Product::with(['digitalProductAttribute','seller','seller.sellerShop'])
                                    ->latest()
                                    ->where('seller_id',$id)
                                    ->whereIn('status', [ProductStatus::PUBLISHED])
                                    ->digital()
                                    ->paginate(site_settings('pagination_number',10));

        return api([
            'shop'             => new SellerResource($seller),
            'related_shops'    => new SellerCollection($sellers),
            'products'         => (new ProductCollection($products)),
            'digital_products' => new DigitalProductCollection($digital__products),
        ])->success(__('response.success'));

    }


      /**
       * Seller shop api
       *
       * @param int |string $shopId
       * 
       * @return JsonResponse
       */
      public function shopFollow(int | string $shopId) :JsonResponse {
  
        $customer = Auth()->user();
        $seller   = Seller::where('id', $shopId)
                            ->where('status', Status::ACTIVE)
                            ->first();
        $follow   = Follower::where('following_id', $customer->id)
                             ->where('seller_id', $seller->id)
                             ->first();

        $messsage = translate('Unfollowed Successfully');

        if($follow){
            $follow->delete();
        }else{
            $follow                = new Follower();
            $follow->following_id  = $customer->id;
            $follow->seller_id     = $seller->id;
            $follow->save();
            $messsage = translate('Followed Successfully');
        }
  
          return api([
            'message' =>  $messsage,
          ])->success(__('response.success'));
  
      }




   

     


}
