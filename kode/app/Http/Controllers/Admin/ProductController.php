<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductSuggestedStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use App\Models\ShippingDelivery;
use App\Models\ProductImage;
use App\Models\ProductStock;
use App\Models\ProductShippingDelivery;
use App\Models\Order;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Utility\ProductGallery;
use App\Models\AttributeValue;
use App\Models\Cart;
use App\Models\DigitalProductAttributeValue;
use App\Models\ProductRating;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permissions:view_product'])->only('inhouseProduct','top','search', 'details','bestSellingItem','featuredStatus',"orderItem",'orderPlaced','orderDelivered');
        $this->middleware(['permissions:create_product'])->only('create','store');
        $this->middleware(['permissions:update_product'])->only('edit','update');
        $this->middleware(['permissions:delete_product'])->only('delete','restore','permanentDelete','productGalleryImageDelete');
    }

    public function inhouseProduct()
    {
        $title = translate('In-house products');
        $inhouseProducts = Product::search()->latest()->inhouseProduct()->physical()->with('category', 'brand', 'subCategory', 'order')->paginate(site_settings('pagination_number',10));
        return view('admin.product.index', compact('title', 'inhouseProducts'));
    }



    public function reviews($id){

        $product = Product::findOrfail($id);
        $title   = $product->name . " - Reviews";
        $reviews = ProductRating::with('customer')->where('product_id',$product->id)->paginate(site_settings('pagination_number',10));

        return view('admin.product.reviews', compact('title', 'product','reviews'));
    }

    public function reviewDelete($id){

        $review = ProductRating::findOrfail($id);
        $review->delete();

        return back()->with('success',translate('Review deleted successfully'));



    }

    public function details($id)
    {
        $title = translate('Product details');
        $product = Product::with(['shippingDelivery',"shippingDelivery.shippingDelivery",'rating','rating.customer'])->withCount(['rating'])->where('id', $id)->firstOrFail();


        return view('admin.product.detail', compact('title', 'product'));
    }


    public function replicate ($id)
    {


        DB::transaction(function () use ($id){
            $product = Product::with(['stock','gallery'])->find($id);
            $replicatedProduct = $product->replicate();

            $replicatedProduct->created_at = Carbon::now();
            $replicatedProduct->save();


            if($product->stock->count() > 0){
                foreach ($product->stock as $stock) {
                    $clonedstock = $stock->replicate();
                    $clonedstock->save();
                    $replicatedProduct->stock()->save($clonedstock);
                }
            }
            if($product->gallery->count() > 0){
                foreach ($product->gallery as $gallery) {
                    $clonedgallery = $gallery->replicate();
                    $clonedgallery->save();
                    $replicatedProduct->gallery()->save($clonedgallery);
                }
            }
        });


        return back()->with('success',translate("Product Replicated"));

    }


    public function create()
    {
        $title = translate('Add new physical product');
        $attributes = Attribute::with('value')->get();
        $brands = Brand::where('status', 1)->select('id', 'name')->get();
        $shippingDeliveries = ShippingDelivery::get(['name','id']);
        $categories = Category::where('status', "1")->select('id', 'name')->with('parent')->get();
        $taxes = Tax::active()->get();
        return view('admin.product.create', compact('title', 'categories', 'brands', 'attributes', 'shippingDeliveries','taxes'));
    }

    public function edit($id)
    {
        $title = translate('Product Update');
        $attributes = Attribute::with(['value'])->get();
        $product = Product::inhouseProduct()->physical()->where('id', $id)->firstOrFail();
        $brands = Brand::where('status', 1)->select('id', 'name')->get();
        $taxes = Tax::with(['products'=> function(BelongsToMany $q) use($product) :BelongsToMany{
            return $q->where('product_id', $product->id);
        }])->active()->get();

        $categories = Category::where('status', "1")->select('id', 'name')->with('parent')->get();
        $shippingDeliveries = ShippingDelivery::get(['name','id']);
        return view('admin.product.edit', compact('title', 'product', 'attributes', 'brands', 'categories', 'shippingDeliveries','taxes'));
    }

    public function store(ProductRequest $request)
    {

        $featuredImage = null;



        if($request->hasFile('featured_image')){
            try {
                $featuredImage = store_file($request->featured_image,file_path()['product']['featured']['path']);
            }catch (\Exception $exp) {

            }
        }

        $product = Product::create([
            'name'=> $request->name,
            'slug'=> make_slug($request->slug),
            'point'=> ($request->point),
            'product_type' => Product::PHYSICAL,
            'price'=> $request->price,
            'weight'=> $request->input('weight',0),
            'shipping_fee'=> $request->shipping_fee,
            'shipping_fee_multiply'=> $request->shipping_fee_multiply ?  $request->shipping_fee_multiply : 0,
            'discount'=> $request->discount_percentage ? $request->price - ($request->price * $request->discount_percentage / 100) : null,
            'discount_percentage'=> $request->discount_percentage ?? null,
            'minimum_purchase_qty'=> $request->minimum_purchase_qty,
            'maximum_purchase_qty'=> $request->maximum_purchase_qty,
            'brand_id'=> $request->brand_id ?? null,
            'category_id'=> $request->category_id,
            'sub_category_id'=> $request->subcategory_id,
            'short_description'=>build_dom_document($request->short_description,'short_descripiton'.rand(10,1000)),
            'description'=> build_dom_document($request->description,'descripiton'.rand(10,1000)),
            'warranty_policy'=> $request->warranty_policy,
            'featured_image'=> $featuredImage,
            'status'=> $request->status,
            'meta_title'=> $request->meta_title,
            "featured_status" => $request->featured_status ? $request->featured_status :"1",
            'meta_image'=> $featuredImage,
            'meta_keywords'=> $request->meta_keywords ?? null,
            'meta_description'=> $request->meta_description,
        ]);



        if($request->input('tax_id') && is_array($request->input('tax_id'))){

            $taxes = [];
            $taxAmounts  = $request->input('tax_amount');
            $taxTypes    = $request->input('tax_type');
            foreach($request->input('tax_id') as $index => $taxId){
                $val['product_id'] =   $product->id;
                $val['tax_id']     =   $taxId;
                $val['amount']     =   @$taxAmounts[$index] ?? 0;
                $val['type']     =   @$taxTypes[$index] ?? 0;
                array_push($taxes,$val);
            }
            $product->taxes()->attach($taxes);
        }




        $collection = collect( $request);

        $choice_options = array();
        if (isset($collection['choice_no']) && $collection['choice_no']) {
            $str = '';
            $item = array();
            
            foreach ($collection['choice_no'] as $key => $no) {
                $str = 'choice_options_' . $no;
                $item['attribute_id'] = $no;
                $attribute_data = array();
                foreach ($collection[$str] as $key => $eachValue) {
                    array_push($attribute_data, $eachValue);
                }
                unset($collection[$str]);

                $item['values'] = $attribute_data;
                array_push($choice_options, $item);
            }
        }
        $choice_options = json_encode($choice_options, JSON_UNESCAPED_UNICODE);
        if (isset($collection['choice_no']) && $collection['choice_no']) {
            $attributes = json_encode($collection['choice_no']);
            unset($collection['choice_no']);
        } else {
            $attributes = json_encode(array());
        }
        $product->attributes_value =   $choice_options;
        $product->attributes =   $attributes;
        $product->save();



        if($request->hasFile('gallery_image')){
            $galleryImage = array_filter($request->gallery_image);
            ProductGallery::imageStore($request, $galleryImage, $product->id);
        }


        if($request->shipping_delivery_id){
            if($request->shipping_delivery_id[0] == 0){
                $shippingDeliveries = ShippingDelivery::pluck('id');

                foreach($shippingDeliveries as $value){
                    ProductShippingDelivery::create([
                        'product_id' => $product->id,
                        'shipping_delivery_id' => $value
                    ]);
                }
            }
            else{
                foreach($request->shipping_delivery_id as $value){
                    ProductShippingDelivery::create([
                        'product_id' => $product->id,
                        'shipping_delivery_id' => $value
                    ]);
                }
            }
        }

         $this->stockStore($request->only([
             'choice_no','product_id'
        ]), $product);

        return back()->with('success', translate("Product has been created"));
    }

    public function stockStore(array $data, $product){

        $collection = collect($data);
        $options = array();

        if ($collection->has('choice_no')) {
            foreach ($collection['choice_no'] as $key => $no) {
                $name = 'choice_options_' . $no;
                $data = array();

                foreach (request()[$name] as $key => $eachValue) {
                    array_push($data, $eachValue);
                }
                array_push($options, $data);
            }
        }

        $combinations = $this->combineAttr($options);


        $variant = '';
        if (count($combinations[0]) > 0) {
            $product->variant_product = 1;
            $product->save();
            foreach ($combinations as $key => $combination) {

                $str = $this->combinationString($combination, $collection);

                $stock = new ProductStock();
                $stock->product_id = $product->id;
                $stock->attribute_value = $str;
                $stock->display_name = request()['display_name_' . str_replace('.', '_', $str)];;
                $stock->price = request()['price_' . str_replace('.', '_', $str)];
                $stock->qty = request()['qty_' . str_replace('.', '_', $str)];
                $stock->save();
            }
        }
    }

    public function stock_edit(Request $request)
    {

        $product = Product::findOrFail($request->id);
        $options = array();


        $product_name = $request->name;
        $unit_price = $request->unit_price;

        if ($request->has('choice_no') && count($request->input('choice_no',[])) > 0) {
            foreach ($request->input('choice_no',[]) as $key => $no) {
                $name = 'choice_options_' . $no;
                $data = array();

                if(isset($request[$name])){
                    foreach ($request[$name] as $key => $item) {
                        array_push($data, $item);
                    }
                    array_push($options, $data);
                }
            }
        }

        $combinations = $this->combineAttr($options);
        return view('admin.product.combination_edit', compact('combinations', 'unit_price', 'product_name','product'));

    }


    public static function combinationString($combination, $collection)
    {
        $str = '';
        foreach ($combination as $key => $item) {
            if ($key > 0) {
                $str .= '-' . str_replace(' ', '', $item);
            } else {

                    $str .= str_replace(' ', '', $item);

            }
        }
        return $str;
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        $product = Product::inhouseProduct()->physical()->where('id', $id)->firstOrFail();
        $featuredImage = $product->featured_image;

        if($request->hasFile('featured_image')){
            try {
                $featuredImage = store_file($request->featured_image,file_path()['product']['featured']['path']);
            }catch (\Exception $exp){

            }
        }


        $product->update([
            'name'=> $request->name,
            'slug'=> make_slug($request->slug),
            'point'=> ($request->point),
            'product_type' => Product::PHYSICAL,
            'price'=> $request->price,
            'weight'=> $request->input('weight',0),
            'shipping_fee'=> $request->shipping_fee,
            'shipping_fee_multiply'=> $request->shipping_fee_multiply ?  $request->shipping_fee_multiply : 0,
            'discount'=> $request->discount_percentage ? $request->price - ($request->price * $request->discount_percentage / 100) : null,
            'discount_percentage'=> $request->discount_percentage ?? null,
            'minimum_purchase_qty'=> $request->minimum_purchase_qty,
            'maximum_purchase_qty'=> $request->maximum_purchase_qty,
            'brand_id'=> $request->brand_id,
            'category_id'=> $request->category_id,
            'sub_category_id'=> $request->subcategory_id,
            'short_description'=>  build_dom_document($request->short_description),
            'description'=> build_dom_document($request->description),
            'warranty_policy' => $request->warranty_policy,
            'featured_image'=> $featuredImage,
            'status'=> $request->status,
            'meta_title'=> $request->meta_title,
            'meta_keywords'=> $request->meta_keywords ?? null,
            'meta_description'=> $request->meta_description,
            'meta_image'=> $featuredImage ,
        ]);


        if($request->input('tax_id') && is_array($request->input('tax_id'))){

            $taxes = [];
            $taxAmounts  = $request->input('tax_amount');
            $taxTypes    = $request->input('tax_type');
            foreach($request->input('tax_id') as $index => $taxId){
                $val['product_id'] =   $product->id;
                $val['tax_id']     =   $taxId;
                $val['amount']     =   @$taxAmounts[$index] ?? 0;
                $val['type']     =   @$taxTypes[$index] ?? 0;
                array_push($taxes,$val);
            }

            $product->taxes()->detach();
            $product->taxes()->attach($taxes);
        }


        if($request->hasFile('gallery_image')){
            $galleryImage = array_filter($request->gallery_image);
            ProductGallery::imageStore($request, $galleryImage, $product->id);
        }

        $collection = collect( $request);

        $choice_options = array();
        if (isset($collection['choice_no']) && $collection['choice_no']) {
            $str = '';
            $item = array();
        
            foreach ($collection['choice_no'] as $key => $no) {
                $str = 'choice_options_' . $no;
                $item['attribute_id'] = $no;
                $attribute_data = array();
                foreach ($collection[$str] as $key => $eachValue) {
                    array_push($attribute_data, $eachValue);
                }
                unset($collection[$str]);

                $item['values'] = $attribute_data;
                array_push($choice_options, $item);
            }
        }
        $choice_options = json_encode($choice_options, JSON_UNESCAPED_UNICODE);
        if (isset($collection['choice_no']) && $collection['choice_no']) {
            $attributes = json_encode($collection['choice_no']);
            unset($collection['choice_no']);
        } else {
            $attributes = json_encode(array());
        }
        $product->attributes_value =   $choice_options;
        $product->attributes =   $attributes;
        $product->save();
        foreach ($product->stock as $key => $stock) {
            $stock->delete();
        }

        $this->stockStore($request->only([
            'choice_no','product_id'
        ]), $product);

        ProductShippingDelivery::where('product_id',$product->id)->delete();

        if($request->shipping_delivery_id){

            if($request->shipping_delivery_id[0] == 0){
                $shippingDeliveries = ShippingDelivery::pluck('id');

                foreach($shippingDeliveries as $value){
                    ProductShippingDelivery::create([
                        'product_id' => $product->id,
                        'shipping_delivery_id' => $value
                    ]);
                }
            }
            else{
                foreach($request->shipping_delivery_id as $value){
                    ProductShippingDelivery::create([
                        'product_id' => $product->id,
                        'shipping_delivery_id' => $value
                    ]);
                }
            }
        }

        return back()->with('success', translate("Product has been updated"));
    }


    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:products,id'
        ]);

        $product = Product::inhouseProduct()->where('id',$request->id)->first();
        $cart = Cart::where('product_id', $request->id)->get();

         if( count($product->exoffer) == 0 && count($product->order) == 0 && count($product->digitalProductAttribute) == 0  && count($product->wishlist) == 0 && count($cart) == 0)  {
            $product->delete();
            return back()->with('success', translate("Product has been deleted"));
         }

         else{
            return back()->with('error', translate("This Product Has Order or Added in Cart or Added in WishList or Added In Exclusive . Please Try Again"));
         }
    }

    public function trashed()
    {
        $title = translate('Trashed products');
        $inhouseProducts = Product::search()->with(['category','order'])->inhouseProduct()->onlyTrashed()->paginate(site_settings('pagination_number',10));
        return view('admin.product.index', compact('title', 'inhouseProducts'));
    }

    public function restore(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:products,id'
        ]);
        $product = Product::onlyTrashed()->where('id', $request->id)->restore();

        return back()->with('success', translate("Product has been restore"));
    }

    /**
     * parmanaent delete form trash
     */
    public function permanentDelete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:products,id'
        ]);
        $product = Product::with(['digitalProductAttribute'=>function($q){
            return $q->with(['digitalProductAttributeValueKey']);
        }])->onlyTrashed()->where('id', $request->id)->first();

        $product->shippingDelivery()->delete();
        $product->gallery()->delete();
        $product->stock()->delete();
        $product->rating()->delete();
        if($product->digitalProductAttribute){

            foreach($product->digitalProductAttribute as  $digitalProductAttribute ){
                DigitalProductAttributeValue::where('digital_product_attribute_id',$digitalProductAttribute->id)->delete();
            }
            $product->digitalProductAttribute()->delete();
        }
        $product->forceDelete();

        return back()->with('success', translate("Product Permenently Deleted"));
    }


    public function orderItem($id)
    {
        $product = Product::findOrFail($id);
        $title = ucfirst($product->name). " all orders log";
        $orders = Order::inhouseOrder()->physicalOrder()->whereHas('orderDetails', function($q) use ($id){
            $q->where('product_id','like',"$id");
        })->orderBy('id', 'DESC')->with('customer')->paginate(site_settings('pagination_number',10));
        return view('admin.order.index', compact('title', 'orders'));
    }

    public function orderPlaced($id)
    {
        $product = Product::findOrFail($id);
        $title = ucfirst($product->name). " placed orders log";
        $orders = Order::inhouseOrder()->physicalOrder()->placed()->whereHas('orderDetails', function($q) use ($id){
            $q->where('product_id','like',"$id");
        })->orderBy('id', 'DESC')->with('customer')->paginate(site_settings('pagination_number',10));
        return view('admin.order.index', compact('title', 'orders'));
    }

    public function orderDelivered($id)
    {
        $product = Product::findOrFail($id);
        $title = ucfirst($product->name). " delivered orders log";
        $orders = Order::inhouseOrder()->physicalOrder()->delivered()->whereHas('orderDetails', function($q) use ($id){
            $q->where('product_id','like',"$id");
        })->orderBy('id', 'DESC')->with('customer')->paginate(site_settings('pagination_number',10));
        return view('admin.order.index', compact('title', 'orders'));
    }

    public function productGalleryImageDelete($id)
    {
        $productGallery = ProductImage::findOrFail($id);
        $product = Product::inhouseProduct()->physical()->where('id', $productGallery->product_id)->firstOrFail();
        $file = file_path()['product']['gallery']['path'].'/'.$productGallery->image;
        if(file_exists($file)){
            @unlink($file);
        }
        $productGallery->delete();

        return back()->with('success', translate("Gallery image has been deleted"));
    }

    public function search(Request $request ,$scope)
    {

        $request->validate([
            'searchFilter'=>'required',
        ]);

        if($request->option_value == 'Select Menu'){

            return back()->with('success', translate("Please Select A Value Form Select Box"));
        }
        $search = $request->searchFilter;

        $title = '';

        $inhouseProducts = Product::with(['category','brand','order'])->inhouseProduct()->physical()->orderBy('id','desc');

        if($request->option_value == 'product_name'){
            $inhouseProducts->Where('name', 'like', "%$search%");
        }
        if($request->option_value == 'category'){
            $inhouseProducts->whereHas('category', function($q) use ($search){
                    $q->where('name','like',"%$search%");
            });
        }
        if($request->option_value == 'brand'){
            $inhouseProducts->whereHas('brand', function($q) use ($search){
                $q->where('name','like',"%$search%");
        });
        }
        if($request->option_value == 'price'){
            $inhouseProducts->Where('price', 'like', "%$search%");
        }

        if ($scope == 'trashed') {
            $inhouseProducts->onlyTrashed();
            $title .= 'trashed';
        }

        $title .= "Inhouse order search by -" . $search;

        $inhouseProducts = $inhouseProducts->paginate(site_settings('pagination_number',10));
        return view('admin.product.index', compact('title', 'inhouseProducts', 'search'));
    }

    public function top($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->top_status = $product->top_status == 1 ? Product::YES : Product::NO;
        $product->save();
        return back()->with('success', translate("Product top status has been updated"));
    }


    public function featuredStatus($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->featured_status = $product->featured_status == 1 ? Product::YES : Product::NO;
        $product->save();
        return back()->with('success', translate("Product featured status has been updated"));
    }

    public function bestSellingItem($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->best_selling_item_status = $product->best_selling_item_status == 1 ? Product::YES : Product::NO;
        $product->save();
        return back()->with('success', translate("Product best selling item status has been updated"));
    }
    public function suggestedItem($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->is_suggested = $product->is_suggested == 0 ? ProductSuggestedStatus::YES : ProductSuggestedStatus::NO;
        $product->save();
        return back()->with('success', translate("Product status has been updated"));
    }

    public  function combination(Request $request) {

        $options = array();

        $unit_price   = $request->price;
        $product_name = $request->name;

        if ($request->has('choice_no') && count($request->input('choice_no',[])) > 0) {

            foreach ($request->input('choice_no',[]) as $key => $no) {

                $name = 'choice_options_' . $no;
                $data = array();

                if(isset($request[$name])){
                    foreach ($request[$name] as $key => $item) {
                        array_push($data, $item);
                    }
                    array_push($options, $data);
                }

            }
        }

        $combinations = $this->combineAttr($options);
       

        return view('admin.product.combination', compact('combinations', 'unit_price', 'product_name'));

    }

    public  function combineAttr($arrays) {

        $resultArr = array(array());

        foreach ($arrays as $key => $arr_values) {
            $tempArr = array();
            foreach ($resultArr as $resultArr_item) {
                foreach ($arr_values as $arr_val) {
                    $tempArr[] = array_merge($resultArr_item, array($key => $arr_val));
                }
            }
            $resultArr = $tempArr;
        }
        return $resultArr;
    }

    public function attrValue(Request $request){
        $attrValues = AttributeValue::with('attribute')->where('attribute_id', $request->attribute_id)->get();
        $html = '';
        foreach ($attrValues as $value) {
            $name =  $value->display_name ? $value->display_name : unslug($value->name);
            $html .= '<option value="' . $value->name . '">' . $name  . '</option>';
        }
        echo json_encode($html);
    }
}
