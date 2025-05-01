<?php

namespace App\Http\Controllers;

use App\Enums\Settings\CacheKey;
use App\Http\Services\Frontend\FrontendService;
use App\Models\Banner;
use Carbon\Carbon;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Currency;
use App\Models\PageSetup;
use App\Models\Subscriber;
use App\Models\TodayDeals;;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\CompareProductList;
use App\Models\Campaign;
use App\Models\ContactUs;
use App\Models\Faq;
use App\Models\GeneralSetting;
use App\Models\ShippingDelivery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function GuzzleHttp\json_encode;
use App\Http\Services\Frontend\ProductService;
use App\Models\FlashDeal;
use App\Models\Language;
use App\Models\ProductRating;
use App\Models\Testimonial;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\RedirectResponse;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;


class FrontendController extends Controller
{

    public FrontendService $frontendService;
    public ProductService $productService;
    public function __construct()  {

        $this->frontendService = new FrontendService();
        $this->productService  = new ProductService();
    }




    /**
     * Language switcher method
     *
     * @param  string | null  $code
     * @return RedirectResponse
     */
    public function languageChange(?  string $code = null) :RedirectResponse{


        if(!Language::where('code', $code)->exists()){
            $code = 'en';
        }
        optimize_clear();
        session()->put('locale', $code);

        App::setLocale($code);

        return back()->with('success',translate('Language switched successfully'));
    }


    public function productStock(Request $request){

        return $this->productService->productStock($request);
    }


    public function index()
    {
        $title = translate('Home Page');
        $banners =  Cache::get(CacheKey::BANNERS->value);
        $now = Carbon::now()->toDateTimeString();
        $campaigns = Campaign:: with('products')->where('status','1')
                            ->where('start_time',"<=",$now)
                            ->where('end_time',">=",$now)->take(4)->get();
        return view('frontend.home', compact('title','campaigns'));
    }

    /**
     * news latter subscription
     *
     * @param Request $request
     */
    public function newsLatterSubscribe(Request $request)
    {
        if($request->dont_show){
            session()->forget('dont_show');
            if(!session()->has('dont_show'))
            {
              session()->put('dont_show', 'true');
            }
        }
        $request->validate([
            'email' => 'required|email',
        ]);
        $subscribeUserExist = Subscriber::where('email', $request->email)->first();


        $status = 'error';
        $message = translate("Already you are subscribed");


        if (!$subscribeUserExist) {
            $subscriber = new Subscriber();
            $subscriber->email = $request->email;
            $subscriber->save();
            $status = 'success';
            $message = translate("Subscribed Successfully");

        }

        return back()->with($status,$message);

    }



    public function flashDeal($slug){
        $title     = translate('Flash Deal');

        $flashDeal = FlashDeal::where('slug',$slug)->where('status',1)->firstOrfail();


        $products  = Product::with((['gallery','review','order','stock','order']))
        ->whereIn('id',@$flashDeal->products ?? [])->latest()->paginate(site_settings('pagination_number',10))->appends(request()->all());

        return view('frontend.flash_deal', compact('title', 'products','flashDeal'));
    }

    /**
     * news letter close
     *
     * @param Request $request
     */
    public function newsLatterClose(Request $request)
    {
        if($request->data){
            session()->forget('dont_show');
            if(!session()->has('dont_show'))
            {
              session()->put('dont_show', 'true');
            }
        }
        return json_encode([
            'success' => true,
        ]);
    }

    // all category page
    public function allCategory(){
        $title = translate('All Categories');
        $listings = Category::where('status', '1')->whereNull('parent_id')->orderBy('serial', 'ASC')->with(['parent','product','houseProduct'])->paginate(site_settings('pagination_number',10));
        return view('frontend.catagories', compact('title', 'listings'));
    }
    // top category page
    public function topCategory(){
        $title = translate('Top Categories');
        $listings = Category::where('status', '1')->whereNull('parent_id')->where('top', '1')->orderBy('serial', 'ASC')->with(['parent','product','houseProduct'])->paginate(site_settings('pagination_number',10));
        return view('frontend.catagories', compact('title', 'listings'));
    }
    // all brand page
    public function allBrand(){
        $title = translate('All Brands');
        $brands = Brand::with(['product','houseProduct'])->withCount('product')->where('status', '1')->orderBy('serial', 'ASC')->paginate(site_settings('pagination_number',10));
        return view('frontend.brands', compact('title', 'brands'));
    }
    //top brands
    public function topBrand(){
        $title = translate('Top Brands');
        $brands = Brand::with(['product','houseProduct'])->withCount('product')->where('status', '1')->where('top', Brand::YES)->orderBy('serial', 'ASC')->paginate(site_settings('pagination_number',10));
        return view('frontend.brands', compact('title', 'brands'));
    }
    public function supportFaq()
    {

        $title = translate('Support');
        $faqs =  Faq::where('status','1')->latest()->get();
        return view('frontend.faq', compact('title', 'faqs'));
    }

    public function shop()
    {
        $title = translate('Shop');
        $sellers = Seller::active()->whereHas('sellerShop', function($query){
                $query->where('status', 1);
            })->whereHas('subscription',function($q){
                $q->where('status', 1);
            })->with(['sellerShop','follow','product'=>function($query){
                $query->where('status','1');
            }])->paginate(site_settings('pagination_number',10));
        return view('frontend.shop', compact('title', 'sellers'));
    }





    public function product()
    {
        $title = translate('All products');
        $products = Product::search()->physical()
                ->whereHas('category', function($query){
                    $query->where('status', '1');
                })
                ->with((['gallery','review','order','stock','order']))
                ->where(function ($query) {
                    $query->whereNull('seller_id')
                        ->whereIn('status', [0, 1])
                        ->orWhereNotNull('seller_id')
                        ->whereIn('status', [1]);
                })
                ->latest()
                ->paginate(site_settings('pagination_number',10))->appends(request()->all());

                session()->forget('search_min');
                session()->forget('search_max');

        return view('frontend.product', compact('title', 'products'));
    }
    public function bestProduct()
    {
        $title = translate('Best Selling products');
        $products = Product::search()->physical()->published()->where('best_selling_item_status', '2')->with(['brand','rating','order'])->paginate(site_settings('pagination_number',10));
        return view('frontend.product', compact('title','products'));
    }


    public function digitalProduct()
    {
        $title = translate('All digital products');
        $digital_products = Product::with(['digitalProductAttribute'])->digital()->latest()
                ->whereHas('category', function($query){
                    $query->where('status', '1');
                })->where(function ($query) {
                    $query->whereNull('seller_id')
                        ->whereIn('status', [0, 1])
                        ->orWhereNotNull('seller_id')
                        ->whereIn('status', [1]);
                })
                ->paginate(site_settings('pagination_number',10));
        return view('frontend.digital_product', compact('title', 'digital_products'));
    }

    public function featuredProduct()
    {


        $title = translate('Todays Deal products');
        $products = Product::search()->physical()->where('status', Product::PUBLISHED)->featured()
                ->whereHas('category', function($query){
                    $query->active(); })

                ->latest()->with('gallery','review','order','stock','order')->paginate(site_settings('pagination_number',10))->appends(request()->all());
        return view('frontend.product', compact('title', 'products'));
    }
    public function newProduct()
    {
        $title = translate('New products');
        $products = Product::physical()->whereNull('seller_id')->where('status', '0')
                ->whereHas('category', function($query){
                    $query->active(); })
                ->latest()->with((['gallery','review','order','stock','order']))
                ->paginate(site_settings('pagination_number',10))->appends(request()->all());
        return view('frontend.product', compact('title', 'products'));
    }

    public function blog()
    {
        $title = translate('Blogs');
        $blogs = Blog::search()->latest()->where('status','1')->paginate(site_settings('pagination_number',10))->appends(request()->all());
        $recentPosts = Blog::orderBy('id', 'DESC')->take(6)->get();
        return view('frontend.blog', compact('title', 'blogs', 'recentPosts'));
    }


    public function categoryBlog($slug,$id)
    {
        $title = ucwords(str_replace('-', ' ', $slug));
        $blogs = Blog::search()->where('category_id', $id)->paginate(site_settings('pagination_number',10))->appends(request()->all());
        $recentPosts = Blog::orderBy('id', 'DESC')->take(6)->get();
        return view('frontend.blog', compact('title', 'blogs', 'recentPosts'));
    }


    public function blogDetails($slug, $id)
    {
        $title = translate('Blog Details');
        $blog = Blog::findOrFail($id);
        $recentPosts = Blog::orderBy('id', 'DESC')->take(6)->get();
        return view('frontend.blog_details', compact('title', 'blog', 'recentPosts'));
    }


    public function pageSetup($slug,$id)
    {
        $page = PageSetup::findOrFail($id);
        $title = $page->name;
        return view('frontend.page_setup', compact('title','page'));

    }
    public function contact()
    {
        $title = translate('Contact us');
        return view('frontend.contact', compact('title'));
    }


    public function sellerStore($slug,$id)
    {
        $seller = Seller::with(['sellerShop'])->active()->whereHas('sellerShop', function($query){
            $query->where('status', '1');
        })->where('id', $id)->firstOrFail();

        $sellers = Seller::with(['sellerShop'])->active()->whereHas('sellerShop', function($query){
            $query->where('status', '1');
        })->whereHas('subscription',function($q){
            $q->where('status', '1');
        })->with(['product'=>function($query){
            $query->where('status','1');
        } , 'product.review','product.stock'])->where('id','!=',$id)->take(6)->get();

        $title = ucfirst($seller->sellerShop->name) . " store";
        $products = Product::with(['seller','brand', 'review', 'order','stock'])
        ->where('seller_id',$id)->whereIn('status',  ['1'])
        ->where('product_type', '102')
        ->whereHas('category', function($query){
            $query->active(); })
        ->paginate(site_settings('pagination_number',10));

        $digital_products =  Product::with(['seller'])
                   ->where('seller_id',$id)
                   ->whereIn('status',  ['1'])
                   ->where('product_type', '101')
                   ->with(['brand', 'review', 'order','digitalProductAttribute'])
                   ->paginate(site_settings('pagination_number',10));

        return view('frontend.seller_store', compact('title', 'seller', 'products','digital_products','sellers'));
    }

    public function productCategory($slug, $id,$type='physical')
    {
        $category = Category::where('status', '1')->where('id', $id)->firstOrFail();

        $title = ucfirst(get_translation($category->name)) .translate(' all products');
        $products = Product::search()->where(function($query) use ($id) {
            $query->where('category_id', $id)
                  ->orWhere('sub_category_id',  $id );
        })
        ->wherein('status', ['0','1'])
                    ->with('review','order','stock','digitalProductAttribute');


        if($type == 'digital') {
            $digital_products =  $products->where('product_type','101')
                                        ->latest()
                                        ->where(function ($query) {
                                            $query->whereNull('seller_id')
                                                ->whereIn('status', [0, 1])
                                                ->orWhereNotNull('seller_id')
                                                ->whereIn('status', [1]);
                                        })
                                        ->paginate(site_settings('pagination_number',10))
                                        ->appends(request()->all());
            $view = "frontend.digital_product";
            return view( 'frontend.digital_product' , compact('title', 'digital_products'));
        }
        else{
            $products =  $products->where('product_type','102')
            ->where(function ($query) {
                $query->whereNull('seller_id')
                    ->whereIn('status', [0, 1])
                    ->orWhereNotNull('seller_id')
                    ->whereIn('status', [1]);
            })
            ->latest()->paginate(site_settings('pagination_number',10))->
            appends(request()->all());

            return view( 'frontend.product' , compact('title', 'products'));
        }

    }

    public function productSubCategory($slug, $id)
    {
        $category = Category::where('status', '1')->whereNotNull('parent_id')->where('id', $id)->firstOrFail();
        $title = ucfirst(get_translation($category->name)) . " all products";
        $products = Product::search()->where('sub_category_id',$id)->wherein('status', ['0','1'])
                            ->whereHas('category', function($query){
                                $query->where('status', '1'); })
                            ->physical()
                            ->where(function ($query) {
                                $query->whereNull('seller_id')
                                    ->whereIn('status', [0, 1])
                                    ->orWhereNotNull('seller_id')
                                    ->whereIn('status', [1]);
                            })->with('review', 'order','stock')->paginate(site_settings('pagination_number',10))->appends(request()->all());

        return view('frontend.product', compact('title', 'products'));
    }

    public function productBrand($slug, $id)
    {
        $brand = Brand::where('status', '1')->where('id', $id)->firstOrFail();
        $title = ucfirst(get_translation($brand->name)) . " all products";

        $products = Product::search()->where('brand_id', $id)
                ->whereHas('category', function($query){
                    $query->where('status', '1'); })
                ->physical()->where(function ($query) {
                    $query->whereNull('seller_id')
                        ->whereIn('status', [0, 1])
                        ->orWhereNotNull('seller_id')
                        ->whereIn('status', [1]);
                })->with('review', 'stock', 'order')->paginate(site_settings('pagination_number',10))->appends(request()->all());

        return view('frontend.product', compact('title', 'products'));
    }

    public function productDetails($slug, $id ,$campSlug = null)
    {

        $url = url()->previous();
        if(Str::contains( $url , ['campaigns'])){
            $campaignSlug = session()->get('campaign');
        }

        else{
            $campaignSlug ='';
            if($campSlug != null){
                $campaignSlug = $campSlug;
            }
        }

        $title = translate('Product details');
        $product = Product::with(
            [
                    'campaigns' => fn(BelongsToMany $q) :BelongsToMany  =>  $q->where('slug',$campaignSlug)
                    ,'shippingDelivery'
                    ,'shippingDelivery.shippingDelivery'
                    ,'shippingDelivery.shippingDelivery.method'
                    ,'stock'
                    ,'review'
                    ,'brand'
                    ,'order'
                    ,'gallery'
                    ,'seller'
                    ,'seller.sellerShop'
                    ,'review'
                    ,'review.customer'

            ])->where('id', $id)
                 ->firstOrFail();


        $products = Product::with([ 'brand', 'order','shippingDelivery','shippingDelivery.shippingDelivery','shippingDelivery.shippingDelivery.method','stock','review','review.customer'])->physical()
                ->whereHas('category', function($query){
                    $query->where('status', '1'); })
                ->where('category_id', $product->category_id)
                ->where(function ($query) {
                    $query->whereNull('seller_id')
                        ->whereIn('status', [0, 1])
                        ->orWhereNotNull('seller_id')
                        ->whereIn('status', [1]);
                })
                ->take(6)->get();
        return view('frontend.product_details', compact('title', 'product', 'products'));
    }





    /**
     * Get digital product view
     *
     * @param string $slug
     * @param integer $id
     * @return View
     */
    public function digitalProductDetails(string $slug, int $id) :View {

        $digital_product = Product::where('id', $id)
                                        ->digital()
                                        ->firstOrFail();

        $title = ucfirst($digital_product->name)." product details";

        $digital_products = Product::with(['seller','seller.sellerShop'])->digital()
                                    ->where('id','!=',$digital_product->id)
                                    ->where('category_id', $digital_product->category_id)
                                    ->where(function ($query) {
                                        $query->whereNull('seller_id')
                                            ->whereIn('status', [Product::NEW, Product::PUBLISHED])
                                            ->orWhereNotNull('seller_id')
                                            ->whereIn('status', [Product::PUBLISHED]);
                                    })
                                    ->latest()
                                    ->take(6)
                                    ->get();

        $paymentMethods   = active_payment_methods();



        return view('frontend.digital_product_details', compact('title', 'digital_product', 'digital_products', 'paymentMethods'));
    }






    public function compare()
    {
        $title = translate('Compare');
        $user = Auth::user();
        if($user){
            $items = CompareProductList::where('customer_id', $user->id)->with('product','product.review','product.stock','product.brand','product.category')->get();
            return view('frontend.compare', compact('title', 'items'));
        }

        else{
            return redirect()->route('home')->with('error',translate('Please Login First'));
        }

    }

    /**comapre list newly implemented */
    public function compareStore(Request $request)
    {
        return  $this->productService->addToCompare($request);
    }

    public function compareDelete($id)
    {
        $user = Auth::user();
        $compareProduct = CompareProductList::where('customer_id', $user->id)->where('id', $id)->firstOrFail();
        $compareProduct->delete();
        return back()->with('success',translate('Compare product has been deleted'));
    }

    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors());
        }
        $subscribeUserExist = Subscriber::where('email', $request->email)->first();
        if (!$subscribeUserExist) {
            $subscriber = new Subscriber();
            $subscriber->email = $request->email;
            $subscriber->save();
            return response()->json(['success' => 'Subscribed Successfully']);
        }else {
            return response()->json(['error' => 'Already you are subscribed']);
        }
    }


    public function currencyChange($currency = null)
    {

        $currency = Currency::where('id', $currency)->first();

        if($currency){
            session()->put('web_currency', $currency );
        }
        session()->forget('coupon');
        return redirect()->back();
    }

    public function websetupMenu($slug, $id)
    {
        $page = PageSetup::where('id', $id)->firstOrFail();
        $title = @$page->name;
        return view('frontend.page_setup', compact('title', 'page'));
    }


    public function quickview(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:products,id',
        ]);
        if($validator->fails()) {
            return response()->json($validator->errors());
        }
        $slug = '';
        if($request->slug){
            $slug =  $request->slug;
        }




        $product = Product::with(
            [
                    'campaigns' => fn(BelongsToMany $q) :BelongsToMany  =>  $q->where('slug',$slug)
                    ,'shippingDelivery'
                    ,'shippingDelivery.shippingDelivery'
                    ,'shippingDelivery.shippingDelivery.method'
                    ,'stock'
                    ,'review'
                    ,'brand'
                    ,'order'
                    ,'gallery'
                    ,'seller'
                    ,'seller.sellerShop'
                    ,'review'
                    ,'review.customer'

            ])->where('id', $request->id)
                 ->firstOrFail();



        $title = translate('Product Details');
        return view('frontend.partials.quick_view', compact('product', 'title'));
    }





    public function defaultImageCreate($size=null)
    {

        $width = explode('x',$size)[0];
        $height = explode('x',$size)[1];
        $img = Image::canvas( $width,$height ,'#ccc');
        $text = $width . 'X' . $height;

        $fontSize     = 20;
        if($width > 100 && $height > 100){
            $fontSize     = 60;
        }

        $img->text($text, $width / 2,  $height / 2, function ($font) use($fontSize) {
            $font->file(realpath('assets/global/fonts') . DIRECTORY_SEPARATOR . 'RobotoMono-Regular.ttf');
            $font->color('#000');
            $font->align('center');
            $font->valign('middle');
            $font->size($fontSize);
        });
        return $img->response('png');

    }

     public function store(Request $request)
     {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required',
        ]);
        $contactUs = new ContactUs();
        $contactUs->name = $request->name;
        $contactUs->email = $request->email;
        $contactUs->subject = $request->subject;
        $contactUs->message = $request->message;
        $contactUs->save();
        return back()->with('success',translate('Message Send Successfully. Thank you to contact us.'));
     }

     /**
      * live product search method start
      */
       public function productLiveSearch(){

            $search = request()->searchData;
            $category =  request()->category;
            $products = Product::physical()->whereIn('status', ['1','0']);

            if($category){
                $products  = $products->where('category_id',$category);
            }
            $products =  $products->where('name', 'like', "%$search%")->where(function ($query) {
                $query->whereNull('seller_id')
                    ->whereIn('status', [0, 1])
                    ->orWhereNotNull('seller_id')
                    ->whereIn('status', [1]);
            })->latest()->take(6)->get()->map(function (Product $product) {
                $product->featured_image = show_image(file_path()['product']['featured']['path'].'/'.$product->featured_image,file_path()['product']['featured']['size']);
                return $product;
            });
            $success = false;
            if(count($products)>0){
               $success = true;
            }

            return json_encode([
                'success'=>  $success,
                'products'=>  $products
            ]);
       }


     /**
      * live product shipiing method start
      */
       public function shippingMethod(){
        $search = request()->searchData;

        $shippingMethod = ShippingDelivery::with(['method'])->where('name', $search)->where('status',
                1)->first();

        $generel = GeneralSetting::first();

        return json_encode([
            'shippingMethod'=>$shippingMethod,
            'generel'=>$generel
        ]);

       }

       /**
        * camapaign section start
        */
        public function campaign(){
            $title = translate('Campaign');
            $now = Carbon::now()->toDateTimeString();
            $campaigns = Campaign:: with('products')->where('status','1')
            ->where('start_time',"<=",$now)
            ->where('end_time',">=",$now)->get();
            return view('frontend.campaigns', compact('title', 'campaigns'));
        }



        /**
         * Feedback store
         *
         * @param Request $request
         *
         */
        public function feedback(Request $request){


            $request->validate([
                'author'       => 'required|max:191',
                'designation'  => 'required|max:191',
                'quote'        => 'required',
                'rating'       => 'required|gt:0|lte:5',
                'image'        => ['nullable','image',new FileExtentionCheckRule(file_format())],

            ]);

            $testimonial               =  new Testimonial();
            $testimonial->author       =  $request->author;
            $testimonial->quote        =  $request->quote;
            $testimonial->rating       =  $request->rating;
            $testimonial->designation  =  $request->designation;
            $testimonial->status       =  0;


            if($request->hasFile('image')){
                try{
                    $image = store_file($request->image, file_path()['testimonial']['path']);
                }catch (\Exception $exp){

                }
            }

            $testimonial->image = @$image;
            $testimonial->save();


           return back()->with("success",translate('Thank your for your review'));
        }


       /**
        * camapaign section start
        */
        public function campaignDetails($slug){

            session()->forget('campaign');
            session()->put('campaign',$slug);
            $title = "Campaign Details";
            $now = Carbon::now()->toDateTimeString();
            $campaign = Campaign:: with(['products','products.brand','products.review','products.stock'])->where('status','1')
            ->where('end_time',">=",$now)
            ->where('slug',$slug)->first();
            return view('frontend.campaign_details', compact('title', 'campaign'));
        }






        /**
         * Get product review
         *
         * @param Request $request
         * @return array
         */
        public function getProductReview(Request $request) :array{

            $request->validate([
                'page' => 'required|numeric',
                'id'   => 'required|exists:products,id',
            ]);

            $reviews =   $this->productService->getReviews($request);


            return ([
                'status'                   => true,
                'next_page'                => $reviews->hasMorePages(),
                "review_html"              => view('frontend.partials.review', compact('reviews'))->render(),
            ]);


        }


}
