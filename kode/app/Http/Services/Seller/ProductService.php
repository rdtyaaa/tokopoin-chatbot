<?php

namespace App\Http\Services\Seller;

use App\Enums\Settings\TokenKey;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Utility\ProductGallery;
use App\Jobs\SendMailJob;
use App\Models\DigitalProductAttribute;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductShippingDelivery;
use App\Models\ProductStock;
use App\Models\Seller;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProductService extends Controller
{

 
    /**
     * Get seller product list
     *
     * @param Seller $seller
     * @return LengthAwarePaginator
     */
    public function getProductList(string $type  ,Seller $seller) :LengthAwarePaginator{

        $status = request()->input("status");

       return Product::with(['category', 'brand', 'subCategory', 'order','gallery','stock','review','review.customer','shippingDelivery','digitalProductAttribute','digitalProductAttribute.digitalProductAttributeValueKey'])
                            ->sellerProduct()
                            ->where('seller_id', $seller->id)
                            ->search()
                            ->when($type == 'digital', fn(Builder $query) : Builder => $query->digital())
                            ->when($type == 'physical', fn(Builder $query) : Builder => $query->physical())
                            ->when($status  && $status == 'trashed' , fn(Builder $query) : Builder => $query->onlyTrashed())
                            ->when($status  && $status == 'refuse' , fn(Builder $query) : Builder => $query->inactive())
                            ->when($status  && $status == 'new' , fn(Builder $query) : Builder => $query->new())
                            ->when($status  && $status == 'approved' , fn(Builder $query) : Builder => $query->published())
                            ->latest()
                            ->paginate(site_settings('pagination_number',10));
    }



  


    /**
     * Store a digital product 
     *
     * @param Seller $seller
     * @param Request $request
     * @return array
     */
    public function storeDigitalProduct(Request $request , Seller $seller  ) :array {


        DB::transaction(function()   use($request ,$seller) : void{

            $featuredImage = null;

       
            if($request->hasFile('featured_image')){
                try {
                    $featuredImage = store_file($request->file('featured_image'),file_path()['product']['featured']['path']);
                }catch (\Exception $exp) {
                   
                }
            }

            $userInformationData = [];


            if ($request->has('data_name') && is_array($request->input('data_name'))) {

                for ($i=0; $i<count($request->data_name); $i++){
                    $array = [];
                    $array['data_label'] = $request->data_label[$i];
                    $array['data_required'] = $request->required[$i];
                    $array['data_value'] = @$request->option_value[$i] ?? null;
                    $array['data_name'] = strtolower(str_replace(' ', '_', $request->data_name[$i]));
                    $array['type'] = $request->type[$i];
                    $userInformationData[$array['data_name']] = $array;
                }
            }
    

                
            $product = Product::create([
                'name'              => $request->input("name"),
                'seller_id'         => $seller->id,
                'point'=> ($request->point),
                'slug'              => make_slug($request->input('slug')),
                'product_type'      => Product::DIGITAL,
                'category_id'       => $request->input("category_id"),
                'sub_category_id'   => $request->input("sub_category_id"),
                'description'       => build_dom_document($request->input("description"),'seller_digital_des'.rand(10,200)),
                'meta_title'        => $request->input("meta_title"), 
                'meta_keywords'     => $request->input("meta_keywords"),
                'meta_description'  => $request->input('meta_description') ,
                'meta_image'        => $featuredImage,
                'featured_image'    => $featuredImage,
                'status'            => Product::NEW,
                'custom_fileds' => $userInformationData

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

            if (request()->input('attribute_option')) {

                $attributeOptions = collect(request()->input('attribute_option'));
                $names = $attributeOptions->get('name', []);
                $prices = $attributeOptions->get('price', []);
            
                $attributes = collect($names)->map(fn (string $name, int $index) :array  =>  
                    [
                        'uid'        => str_unique(),
                        'product_id' => $product->id,
                        'name'       => $name ?? 'N/A',
                        'price'      => @$prices[$index] ?? 0,
                    ]
                );

                DigitalProductAttribute::insert($attributes->toArray());
            }

        });



       return [
            'status'     => true,
            'message'   => translate('Product created successfully'),
       ];
    }



    
    /**
     * Update  a digital  product 
     *
     * @param Seller $seller
     * @param Product $product
     * @param Request $request
     * @return array
     */
    public function updateDigitalProduct (Request $request , Seller $seller  ,Product $product) :array {

        DB::transaction(function()   use($request ,$product) : void{

            $featuredImage = $product->featured_image;

       
            if($request->hasFile('featured_image')){
                try {
                    $featuredImage = store_file($request->file('featured_image'),file_path()['product']['featured']['path'], null , $product->featured_image);
                }catch (\Exception $exp) {
                   
                }
            }

            $userInformationData = [];
            if ( $request->has('data_name') && is_array($request->input('data_name')) ) {
                for ($i=0; $i<count($request->data_name); $i++){
                    $array = [];
                    $array['data_label'] = $request->data_label[$i];
                    $array['data_required'] = $request->required[$i];
                    $array['data_value'] = @$request->option_value[$i] ?? null;
                    $array['data_name'] = strtolower(str_replace(' ', '_', $request->data_name[$i]));
                    $array['type'] = $request->type[$i];
                    $userInformationData[$array['data_name']] = $array;
                }
            }
                
            $product->update([
                'name'              => $request->input("name"),
                'slug'              => make_slug($request->input('slug')),
                'point'=> ($request->point),
                'category_id'       => $request->input("category_id"),
                'sub_category_id'   => $request->input("sub_category_id"),
                'description'       => build_dom_document($request->input("description"),'seller_digital_des'.rand(10,200)),
                'meta_title'        => $request->input("meta_title"), 
                'meta_keywords'     => $request->input("meta_keywords"),
                'meta_description'  => $request->input('meta_description') ,
                'meta_image'        => $featuredImage,
                'featured_image'    => $featuredImage,
                'custom_fileds'     => $userInformationData
            ]);
 
            if(site_settings('seller_product_status_update_permission') == StatusEnum::true->status() && 
                ($product->status ==  Product::INACTIVE  || $product->status ==  Product::PUBLISHED
                 )){

                    if(in_array($request->input("status"),[Product::PUBLISHED, Product::INACTIVE])){
                        $product->status = $request->input("status");
                    }
            }else{
                $product->status = Product::NEW;
            }

            $product->save();



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

      
        });



       return [
            'status'     => true,
            'message'   => translate('Product updated successfully'),
       ];
    }


    


    /**
     * Store a product 
     *
     * @param Request $request
     * @param Seller $seller
     * @return array
     */
    public function store(Request $request ,Seller $seller) :array {



        DB::transaction(function()   use($request ,$seller) : void{
            $featuredImage = null;
        
        
            if($request->hasFile('featured_image')){
                try {
                    $featuredImage = store_file($request->featured_image,file_path()['product']['featured']['path']);
                }catch (\Exception $exp) {
    
                }
            }

            $product = Product::create([
                'name'             => $request->input('name'),
                'slug'             => make_slug($request->input('slug')),
                'seller_id'        => $seller->id,
                'product_type'     => Product::PHYSICAL,
                'price'            => $request->input('price'),
                'point'=> ($request->point),
                
                'weight'=> $request->input('weight',0),
                'shipping_fee'         => $request->input('shipping_fee'),
                'shipping_fee_multiply'=> $request->input('shipping_fee_multiply',0),
                'discount'         => $request->input('discount_percentage') 
                                            ? $request->input("price") - ($request->input("price") * $request->input('discount_percentage') / 100)
                                            : null,
                'discount_percentage'  => $request->input('discount_percentage') ?? null,
                'minimum_purchase_qty' => $request->input('minimum_purchase_qty'),
                'maximum_purchase_qty' => $request->input("maximum_purchase_qty"),
                'brand_id'             => $request->input('brand_id') ?? null,
                'category_id'          => $request->input('category_id'),
                'sub_category_id'      => $request->input('sub_category_id'),
                'short_description'    => build_dom_document( $request->input("short_description"),'seller_short_descripiton'.rand(10,1000)),
                'description'          => build_dom_document($request->input('description'),'seller_descripiton'.rand(10,1000)),
                'featured_image'       => $featuredImage,
                'meta_title'           => $request->input('meta_title'),
                'meta_image'           => $featuredImage,
                'meta_keywords'        => $request->input("meta_keywords") ?? null,
                'meta_description'     => $request->input('meta_description'),
                'warranty_policy'      => $request->input("warranty_policy"),
                'status'               => Product::NEW,
            ]);

            #STORE TAXES

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


            # STORE GALLERY IMAGES
            if($request->hasFile('gallery_image')){
                $galleryImage = array_filter($request->gallery_image);
                ProductGallery::imageStore($request, $galleryImage, $product->id);
            }


            # STORE SHIPPING
            if($request->input("shipping_delivery_id")){
                foreach($request->shipping_delivery_id as $value){
                    ProductShippingDelivery::create([
                        'product_id'           => $product->id,
                        'shipping_delivery_id' => $value
                    ]);
                }
            }

            $collection = collect($request);

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

        


        $this->stockStore($request->only([
                'choice_no','product_id'
        ]), $product);

        });


        return [
            'status'     => true,
            'message'   => translate('Product created successfully'),
       ];
    }





    /**
     * update a product 
     *
     * @param Request $request
     * @param Seller $seller
     * @param Product $seller
     * @return array
     */
    public function update(Request $request ,Seller $seller ,Product $product) :array {



        DB::transaction(function()   use($request ,$seller ,$product) : void{
            $featuredImage =  $product->featured_image;
        
        
            if($request->hasFile('featured_image')){
                try {
                    $featuredImage = store_file($request->file("featured_image"),file_path()['product']['featured']['path'], null,   $featuredImage);
                }catch (\Exception $exp) {
    
                }
            }

            $product->update([
                'name'             => $request->input('name'),
                'point'=> ($request->point),
                'slug'             => make_slug($request->input('slug')),
                'price'            => $request->input('price'),
                'weight'=> $request->input('weight',0),
                'shipping_fee'         => $request->input('shipping_fee'),
                'shipping_fee_multiply'=> $request->input('shipping_fee_multiply',0),
                'discount'         => $request->input('discount_percentage') 
                                            ? $request->input("price") - ($request->input("price") * $request->input('discount_percentage') / 100)
                                            : null,
                'discount_percentage'  => $request->input('discount_percentage') ?? null,
                'minimum_purchase_qty' => $request->input('minimum_purchase_qty'),
                'maximum_purchase_qty' => $request->input("maximum_purchase_qty"),
                'brand_id'             => $request->input('brand_id') ?? null,
                'category_id'          => $request->input('category_id'),
                'sub_category_id'      => $request->input('sub_category_id'),
                'short_description'    => build_dom_document( $request->input("short_description"),'seller_short_descripiton'.rand(10,1000)),
                'description'          => build_dom_document($request->input('description'),'seller_descripiton'.rand(10,1000)),
                'featured_image'       => $featuredImage,
                'meta_title'           => $request->input('meta_title'),
                'meta_image'           => $featuredImage,
                'meta_keywords'        => $request->input("meta_keywords") ?? null,
                'meta_description'     => $request->input('meta_description'),
                'warranty_policy'      => $request->input("warranty_policy"),
            ]);





            if(
                site_settings('seller_product_status_update_permission') == StatusEnum::true->status() && 
               ($product->status ==  Product::INACTIVE  || 
                $product->status ==  Product::PUBLISHED
             )){

                if(in_array($request->input("status"),[Product::PUBLISHED, Product::INACTIVE])){
                    $product->status = $request->input("status");
                }
            }else{
                $product->status = Product::NEW;
            }

            $product->save();
            #STORE TAXES
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
    

            # STORE GALLERY IMAGES
            if($request->hasFile('gallery_image')){
                $galleryImage = array_filter($request->gallery_image);
                ProductGallery::imageStore($request, $galleryImage, $product->id);
            }

            # STORE SHIPPING
            if($request->input("shipping_delivery_id")){

                ProductShippingDelivery::where('product_id',$product->id)->delete();
                
                foreach($request->shipping_delivery_id as $value){
                    ProductShippingDelivery::create([
                        'product_id'           => $product->id,
                        'shipping_delivery_id' => $value
                    ]);
                }
            }

            $collection = collect($request);

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

        

        $product->stock()->delete();

        $this->stockStore($request->only([
                'choice_no','product_id'
        ]), $product);

        });


        return [
            'status'     => true,
            'message'   => translate('Product update successfully'),
       ];
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
                $stock->price = request()['price_' . str_replace('.', '_', $str)];
                $stock->qty = request()['qty_' . str_replace('.', '_', $str)];
                $stock->save();
            }
        }
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





  
    

}