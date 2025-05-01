<?php

namespace App\Providers;

use App\Enums\ProductStatus;
use App\Enums\Settings\CacheKey;
use App\Http\Services\Frontend\FrontendService;
use Illuminate\Support\ServiceProvider;

use App\Models\Currency;
use App\Models\Product;
use App\Models\Withdraw;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Seller;

use App\Models\Faq;
use App\Models\FlashDeal;
use App\Models\KycLog;
use App\Models\NewsLatter;
use App\Models\Subscriber;
use App\Models\PageSetup;
use App\Models\PaymentLog;
use App\Models\SupportTicket;
use App\Models\Testimonial;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Schema::defaultStringLength(191);

        $forntendService =  new FrontendService();

        try {
            Paginator::useBootstrap();



           
            $view['openAi']  = site_settings('open_ai_setting',null) 
                                    ? json_decode(site_settings('open_ai_setting',null)) 
                                    : null;

            
  
            
            $view['subscribers'] =  Cache::remember(CacheKey::SUBSCRIBER->value,24 * 60, fn() =>  Subscriber::latest()->pluck('email'));
               
            

            $view['newsLatter'] =   NewsLatter::first();

            $view['breadcrumb'] =   frontend_section('breadcrumb');

            $view['menus']      =   Cache::remember(CacheKey::MENU->value,24 * 60, fn ()=> Menu::all());
      
            $view['faqs']       =   Cache::remember(CacheKey::FAQ->value,24 * 60, fn() => Faq::where('status','1')->latest()->get());
                                            
           
            $view['languages']  =   Cache::remember(CacheKey::LANGUAGE->value,24 * 60, fn() =>  $forntendService->language());
            



            $view['currencys']  =   Cache::remember(CacheKey::CURRENCIES->value,24 * 60, fn() =>  Currency::active()
                                                                                                    ->select('id', 'name', 'rate')
                                                                                                    ->get());
            


            $view['categories'] =  Cache::remember(CacheKey::FRONTEND_CATEGORIES->value,24 * 60, fn() => Category::where('status', '1')
                                                            ->whereNull('parent_id')
                                                            ->orderBy('serial', 'ASC')
                                                            ->with(['parent','product','houseProduct','physicalProduct'])
                                                            ->withCount(['parent','product','houseProduct','digitalProduct','physicalProduct'])
                                                            ->take(10)->get());



   

            $banners    = Cache::remember(CacheKey::BANNERS->value,24 * 60, fn() =>  Banner::where('status', '1')
                                                                                         ->orderBy('serial_id', 'ASC')
                                                                                         ->get());
            
            

            $pageSetups =  Cache::remember(CacheKey::PAGES->value,24 * 60, fn() =>   PageSetup::latest()->orderBy('id', 'ASC')->get());



                                            
            
            
            $view['seller_new_digital_product_count']     =  Cache::remember(CacheKey::SELLER_NEW_DIGITAL_PRODUCT->value,24 * 60, fn() =>  Product::sellerProduct()->digital()->new()->count());
            


            $view['seller_new_physical_product_count']    = Cache::remember(CacheKey::SELLER_NEW_PHYSICAL_PRODUCT->value,24 * 60, fn() =>  Product::sellerProduct()->physical()->new()->count());
      
            
            

           
            $view['physical_product_order_count']         =  Cache::remember(CacheKey::PHYSICAL_ORDER_COUNT->value,24 * 60, fn() =>      Order::physicalOrder()->inhouseOrder()->placed()->count());
      
            
            
        
            $view['physical_product_seller_order_count']  =  Cache::remember(CacheKey::PHYSICAL_SELLER_ORDER_COUNT->value,24 * 60, fn() =>      Order::physicalOrder()->sellerOrder()->placed()->count());
            



            $view['withdraw_pending_log_count']           =   Cache::remember(CacheKey::WITHDRAW_PENDING_LOG_COUNT->value,24 * 60, fn() =>       Withdraw::where('status', '!=', 0)->where('status',2)->count());



            $view['deposit_pending_log_count']           =     PaymentLog::deposit()->pending()->count();



            $view['requested_kyc_log']           =   KycLog::pending()->count();
            
           



            $view['running_ticket']                       =  Cache::remember(CacheKey::RUNNING_TICKET->value,24 * 60, fn() =>          SupportTicket::where('status',1)->count());


            $newProducts = Cache::remember(CacheKey::FRONTEND_NEW_PRODUCTS->value,24 * 60, 
                                fn() =>  Product::inhouseProduct()
                                                                ->physical()->new()
                                                                ->inRandomOrder()
                                                                ->with(['review','brand','stock'])
                                                                ->take(20)
                                                                ->get());
            
            $view['new_products']                         =  $newProducts;
                                            
            
  


            view()->share($view);

            view()->composer('frontend.partials.seo', function ($view) {
                $view->with([
                    'seo_content' => frontend_section('seo-section')
                ]);
            });


            view()->composer('frontend.partials.footer', function ($view) use($pageSetups) {
                $view->with([
                    'pageSetups' => $pageSetups,
                ]);
            });

            view()->composer('frontend.section.banner', function ($view) use($banners) {
                $view->with([
                    'banners' => $banners,
                ]);
            });

            $todayDealProducts =  Cache::remember(CacheKey::FRONTEND_TODAYS_DEAL_PRODUCTS->value,24 * 60, 
                                            fn() =>   Product::where('featured_status','2')
                                                            ->physical()->published()
                                                            ->latest()
                                                            ->with(['review','stock'])
                                                            ->take(8)
                                                            ->get());


            view()->composer('frontend.section.today_deals', function ($view) use ($todayDealProducts) {
                $view->with([
                    'todays_deals_products'=> $todayDealProducts,
                ]);
            });
      

         

            view()->composer('frontend.section.digital_product', function ($view) {
                $view->with([
                    'digital_products'=> Cache::remember(CacheKey::FRONTEND_DIGITAL_PRODUCTS->value,24 * 60, 
                                            fn() =>         Product::with(['digitalProductAttribute','review'])
                                            ->digital()
                                            ->where(function ($query) {
                                                $query->whereNull('seller_id')
                                                    ->whereIn('status', [ProductStatus::NEW, ProductStatus::PUBLISHED])
                                                    ->orWhereNotNull('seller_id')
                                                    ->whereIn('status', [ProductStatus::PUBLISHED]);
                                            })
                                            ->inRandomOrder()
                                            ->take(8)
                                            ->get())
                    
                    
            
                ]);
            });
            

            
            view()->composer('frontend.section.flash_deal', function ($view) {
                $now = Carbon::now();
               
                $view->with([
                    'flashDeal' => FlashDeal::where('status','1')
                                        ->where('start_date',"<=",$now)
                                        ->where('end_date',">=",$now)->first()
                ]);
            });


            view()->composer(['frontend.section.top_brand', 'frontend.partials.sidebar'], function ($view) {
                $view->with([
                    'brands'=> 
                                  Cache::remember(CacheKey::TOP_BRANDS->value,24 * 60, 
                                            fn() =>   Brand::with(['houseProduct'])
                                            ->where('status', '1')
                                            ->where('top', Brand::YES)
                                            ->orderBy('serial', 'ASC')
                                            ->get()),
                ]);
            });
            view()->composer(['frontend.section.top_category'], function ($view) {
                $view->with([
                    'top_categories'=>  Cache::remember(CacheKey::TOP_CATEGORIES->value,24 * 60, 
                    fn() =>   Category::with(['houseProduct','product','houseSubCateProduct'])
                                        ->whereHas('physicalProduct')
                                        ->where('status', '1')
                                        ->where('top', '1')
                                        ->orderBy('serial', 'ASC')->get()),
                ]);
            });

            view()->composer('frontend.section.best_selling_product', function ($view) {
                $view->with([
                    'best_selling_products'=>Cache::remember(CacheKey::FRONTEND_BEST_SELLING_PRODUCTS->value,24 * 60, 
                    fn() =>   Product::with(['gallery','review','brand','stock'])->physical()->published()->where('best_selling_item_status', '2')->inRandomOrder()->take(6)->get()),

                ]);
            });



            view()->composer('frontend.section.top_product', function ($view) {
                $view->with([
                    'top_products'=> 

                    Cache::remember(CacheKey::FRONTEND_TOP_PRODUCTS->value,24 * 60, 
                                                fn() =>         Product::with(['gallery','review','brand','stock',])
                                                ->physical()
                                                ->published()
                                                ->top()
                                                ->inRandomOrder()
                                                ->take(6)
                                                ->get()),
   
                ]);
            });
          

            $best_sellers = Cache::remember(CacheKey::FRONTEND_BEST_SELLER->value,24 * 60, 
                                fn() =>          Seller::with(['follow','product'=>function($query){
                                                        $query->where('status','1');
                                                    } , 'product.review','product.stock'])->where('status', 1)
                                                        ->whereHas('sellerShop', function($query){
                                                            $query->where('status', '1');
                                                        })->whereHas('subscription',function($q){
                                                            $q->where('status', 1);
                                                        })->where('best_seller_status', 2)->take(10)->with('sellerShop')->get());



            view()->composer('frontend.section.best_seller', function ($view) use( $best_sellers ) {
                $view->with([
                    'bestsellers'=>  $best_sellers  ,
                ]);
            });
            view()->composer('frontend.section.trending_product', function ($view) use( $best_sellers ) {
                $view->with([
                    'bestsellers'=>  $best_sellers,
                ]);
            });



            $testimonials = Cache::remember(CacheKey::TESTIMONIAL->value,24 * 60, 
                                        fn() => Testimonial::where('status',1)->latest()->get());
            
            
      

            view()->composer('frontend.section.testimonial', function ($view) use( $testimonials ) {
                $view->with([
                    'testimonials'=>  $testimonials,
                ]);
            });
            view()->composer('auth.login', function ($view) use( $testimonials ) {
                $view->with([
                    'testimonials'=>  $testimonials,
                ]);
            });
        }catch(\Exception $ex) {

        }

    }
}
