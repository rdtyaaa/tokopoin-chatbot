<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Settings\NotificationType;
use App\Http\Controllers\Controller;
use App\Traits\Notify;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\DigitalProductAttribute;
use App\Models\Category;
use App\Models\DigitalProductAttributeValue;
use App\Http\Requests\DigitalProductRequest;
use App\Http\Requests\DigitalProductUpdateRequest;
use App\Models\Cart;
use App\Models\Tax;
use App\Rules\General\FileExtentionCheckRule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Image;
class DigitalProductController extends Controller

{

    use Notify;
    public function __construct()
    {
        $this->middleware(['permissions:view_product'])->only('index');
        $this->middleware(['permissions:create_product'])->only('create','store');
        $this->middleware(['permissions:update_product'])->only('edit','update');
        $this->middleware(['permissions:delete_product'])->only('delete');
    }

    public function seller()
    {
        $title = translate('Seller digital products');
        $sellerDigitalProducts = Product::sellerProduct()
                                            ->digital()
                                            ->orderBy('id', 'DESC')
                                            ->search()
                                            ->with('category', 'seller', 'subCategory')
                                            ->paginate(site_settings('pagination_number',10));

        return view('admin.digital_product.seller', compact('title', 'sellerDigitalProducts'));
    }

    public function sellerTrashedProduct()
    {
        $title = translate('Seller digital trashed product showing item');
        
        $sellerDigitalProducts = Product::with(['seller','category','order'])
                                                    ->sellerProduct()
                                                    ->digital()
                                                    ->onlyTrashed()
                                                    ->search()
                                                    ->orderBy('id', 'DESC')
                                                    ->with('category','subCategory')
                                                    ->paginate(site_settings('pagination_number',10));

        return view('admin.digital_product.seller', compact('title', 'sellerDigitalProducts'));
    }

    public function sellerProductDetails($id)
    {
        $title = translate('Seller digital product details');
        $sellerDigitalProduct = Product::with(['shippingDelivery','shippingDelivery.shippingDelivery'
                                                ])->sellerProduct()
                                                ->digital()
                                                ->search()
                                                ->where('id', $id)
                                                ->firstOrFail();
        return view('admin.digital_product.seller_details', compact('title', 'sellerDigitalProduct'));
    }


    public function replicate ($id)
    {

        $product = Product::with(['digitalProductAttribute'])->find($id);
        $replicatedProduct = $product->replicate();
        $replicatedProduct->created_at = Carbon::now();
        $replicatedProduct->save();

        return back()->with('success',translate("Product Replicated"));

    }


    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:products,id'
        ]);

        $product = Product::with(['digitalProductAttribute'=>function($q){
            return $q->with(['digitalProductAttributeValueKey']);
        }])->Digital()->inhouseProduct()->where('id',$request->id)->first();

        $cart = Cart::where('product_id', $request->id)->get();

         if(count($product->order) == 0 &&  count($product->wishlist) == 0 && count($cart) == 0)  {
            $product->delete();
            return back()->with('success', translate("Product has been deleted"));
         }


       return back()->with('error', translate("This Product Has Order or Added In Cart Or In WishList, Plese Try Again"));

    }
    public function sellerProductAttributeValue($id)
    {
        $title = translate('Seller digital product attribute value');
        $sellerDigitalProductAttributeValues = DigitalProductAttributeValue::where('digital_product_attribute_id', $id)->latest()->paginate(site_settings('pagination_number',10));
        return view('admin.digital_product.seller_attribute_value', compact('title', 'sellerDigitalProductAttributeValues'));
    }


    public function sellerProductItem(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|in:new,published,inactive,all',
        ]);
        $type = $request->type;
        $title = "Seller ".$type." digital product showing item";
        $sellerDigitalProducts = Product::sellerProduct()->digital();
        if($type == "new"){
            $sellerDigitalProducts = $sellerDigitalProducts->new();
        }elseif($type == "published"){
            $sellerDigitalProducts = $sellerDigitalProducts->published();
        }elseif($type == "inactive"){
            $sellerDigitalProducts = $sellerDigitalProducts->inactive();
        }
        $sellerDigitalProducts = $sellerDigitalProducts->search()->orderBy('id', 'DESC')->with('category','subCategory')->paginate(site_settings('pagination_number',10));
        return view('admin.digital_product.seller', compact('title', 'sellerDigitalProducts', 'type'));
    }

    public function sellerProductDelete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:products,id',
        ]);
        $product = Product::sellerProduct()->digital()->where('id', $request->id)->delete();
        return back()->with('success', translate("Seller digital product has been deleted"));
    }

    public function sellerProductApprovedBy(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:products,id',
        ]);
        $product = Product::with(['seller'])->sellerProduct()->digital()->where('id', $request->id)->firstOrFail();
        $product->status = Product::PUBLISHED;
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




        return back()->with('success', translate("Seller digital product has been approved"));
    }

    public function sellerProductInactive(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:products,id',
        ]);
        $product = Product::with(['seller'])->sellerProduct()->digital()->where('id', $request->id)->firstOrFail();
        $product->status = Product::INACTIVE;
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



        return back()->with('success', translate("Seller digital product has been inactive"));
    }

    public function sellerProductRestore(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:products,id'
        ]);
        $product = Product::sellerProduct()->digital()->onlyTrashed()->where('id', $request->id)->restore();
         return back()->with('success', translate("Seller digital product has been restore"));
    }

    public function index()
    {
        $title = translate('In-house digital products');
        $inhouseDigitalProducts = Product::inhouseProduct()->digital()->search()->orderBy('id', 'DESC')->with('category','subCategory')->paginate(site_settings('pagination_number',10));
        return view('admin.digital_product.index', compact('title', 'inhouseDigitalProducts'));
    }
    public function trashed()
    {
        $title = translate('In-house digital trashed products');
        $inhouseDigitalProducts = Product::inhouseProduct()->digital()->onlyTrashed()->search()->orderBy('id', 'DESC')->with('category','subCategory')->paginate(site_settings('pagination_number',10));
        return view('admin.digital_product.index', compact('title', 'inhouseDigitalProducts'));
    }

    public function create()
    {
        $title = translate('Add new digital product');
        $categories = Category::where('status', '1')->with('parent')->select('id', 'name')->get();
        $taxes = Tax::active()->get();
        return view('admin.digital_product.create', compact('title', 'categories','taxes'));
    }

    public function store(DigitalProductRequest $request)
    {
        $metaImage = null; $featuredImage = null;
        if($request->hasFile('meta_image')){
            try {
                $metaImage = store_file($request->meta_image, file_path()['seo_image']['path']);
            }catch (\Exception $exp) {

            }
        }
        if($request->hasFile('featured_image')){

            try {
                $featuredImage = store_file($request->featured_image,file_path()['product']['featured']['path']);
            }catch (\Exception $exp) {

            }
        }

        $userInformationData = [];
        if ($request->has('data_name')) {
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
            'name'=> $request->name,
            'point'=> ($request->point),
            'slug'=> make_slug($request->slug),
            'product_type' => Product::DIGITAL,
            'category_id'=> $request->category_id,
            'sub_category_id'=> $request->subcategory_id,
            'description'=> build_dom_document($request->description,'digital_description'.rand(10,22000)),
            'meta_title'=> $request->meta_title ?? null,
            'meta_keywords'=> $request->meta_keywords ?? null,
            'meta_description'=> $request->meta_description ?? null,
            'meta_image'=> $metaImage,
            'featured_image'=> $featuredImage,
            'status'=> $request->status,
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



        if($request->attribute_option){
            $i = 0;
            foreach(@$request->attribute_option['name'] as $val){


                    DigitalProductAttribute::create([
                        'product_id' => $product->id,
                        'name'       => @$request->attribute_option['name'][$i]?? "N/A",
                        'price'      => @$request->attribute_option['price'][$i] ?? 0,
                    ]);

                    $i++;

            }
       }

        return back()->with('success', translate("Digital product has been created"));
    }

    public function edit($id)
    {
        $title = translate('Digital product update');
        $product = Product::inhouseProduct()->digital()->where('id',$id)->firstOrFail();
        $categories = Category::where('status', '1')->select('id', 'name')->with('parent')->get();
        $taxes = Tax::with(['products'=> function(BelongsToMany $q) use($product) :BelongsToMany{
            return $q->where('product_id', $product->id);
        }])->active()->get();

        return view('admin.digital_product.edit', compact('title', 'product', 'categories','taxes'));
    }

    public function update(DigitalProductUpdateRequest $request, $id)
    {
        $product = Product::inhouseProduct()->digital()->where('id', $id)->firstOrFail();
        $metaImage = $product->meta_image; $featuredImage = $product->featured_image;
        if($request->hasFile('meta_image')){
            try {
                $metaImage = store_file($request->meta_image, file_path()['seo_image']['path'], null, $product->meta_image);
            }catch (\Exception $exp) {

            }
        }
        if($request->hasFile('featured_image')){
            try {
                $featuredImage = store_file($request->featured_image,file_path()['product']['featured']['path'],null , $featuredImage);
            }catch (\Exception $exp) {

            }
        }


        $userInformationData = [];
        if ($request->has('data_name')) {
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
            'product_type' => Product::DIGITAL,
            'name'=> $request->name,
            'point'=> ($request->point),
            'slug'=> make_slug($request->slug),
            'category_id'=> $request->category_id,
            'sub_category_id'=> $request->subcategory_id,
            'description'=> build_dom_document($request->description,'update_digital_description'.rand(10,22000)),
            'meta_title'=> $request->meta_title ?? null,
            'meta_keywords'=> $request->meta_keywords ?? null,
            'meta_description'=> $request->meta_description ?? null,
            'meta_image'=> $metaImage,
            'featured_image'=> $featuredImage,
            'status'=> $request->status,
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

            $product->taxes()->detach();
            $product->taxes()->attach($taxes);
        }


         return back()->with('success', translate("Digital product has been updated"));
    }

    public function attribute($id)
    {

        $product = Product::digital()->where('id', $id)->firstOrFail();
        $title = ucfirst($product->name)." Attribute List";
        $digitalProductAttributes = DigitalProductAttribute::where('product_id', $product->id)->paginate(site_settings('pagination_number',10));
        return view('admin.digital_product.attribute', compact('title', 'digitalProductAttributes', 'product'));
    }

    public function attributeStore(Request $request)
    {
        $data = $this->validate($request, [
            'product_id' => 'required|exists:products,id',
            'name' => 'required|max:255',
            'price' => 'required|numeric|gt:0',
        ]);

        $digitalProductAttribute = new DigitalProductAttribute();
        $digitalProductAttribute->product_id = $request->product_id;
        $digitalProductAttribute->name = $request->name;
        $digitalProductAttribute->short_details = $request->short_details;
        $digitalProductAttribute->price = $request->price;
        $digitalProductAttribute->save();

         return back()->with('success', translate("Digital product attribute has been created"));
    }

    public function attributeDetails($id)
    {
        $digitalProductAttribute = DigitalProductAttribute::where('id', $id)->first();
        $title = "Attribute value store for ".$digitalProductAttribute->name;
        $digitalProductAttributeValues = DigitalProductAttributeValue::where('digital_product_attribute_id', $digitalProductAttribute->id)->paginate(site_settings('pagination_number',10));
        return view('admin.digital_product.attribute_edit', compact('title', 'digitalProductAttribute', 'digitalProductAttributeValues'));
    }

    public function attributeUpdate(Request $request)
    {
       $this->validate($request, [
            'id'  => 'required',
            'name' => 'required|max:255',
            'price' => 'required|numeric|gt:0',
            'status' => 'required|in:1,0',
        ]);
        $digitalProductAttribute = DigitalProductAttribute::where('id', $request->id)->firstOrfail();
        $digitalProductAttribute->name = $request->name;
        $digitalProductAttribute->short_details = $request->short_details;
        $digitalProductAttribute->price = $request->price;
        $digitalProductAttribute->status = $request->status;
        $digitalProductAttribute->save();
        return back()->with('success', translate("Digital product attribute has been updated"));
    }

    public function attributeDelete(Request $request)
    {
        $this->validate($request, [
            'id'  => 'required',
        ]);

        $response = 'success';
        $message = translate("Digital product attribute has been deleted");

        $digitalProductAttribute = DigitalProductAttribute::with(['digitalProductAttributeValueKey'])
                                     ->where('id', $request->id)
                                     ->first();
        if($digitalProductAttribute->digitalProductAttributeValueKey->count() >  0){

            foreach($digitalProductAttribute->digitalProductAttributeValueKey as $value){
                if($value->file) @unlink(file_path()['product']['attribute']['path'] . '/' . $value->file);
                $value->delete();
            }

        } 

        $digitalProductAttribute->delete();
         return back()->with( $response,  $message);
    }

    public function attributeValueStore(Request $request, $id)
    {

        $this->validate($request,[
            'name'        => 'required',
            'file'        => ['nullable',new FileExtentionCheckRule(['jpg', 'jpeg', 'png', 'jfif', 'webp', 'heif','pdf', 'doc', 'exel','csv'],'file')],
        ]);
        $digitalAttribute = DigitalProductAttribute::where('id',$id)->firstOrfail();


        if($request->hasFile('file')){
            try {
                $file = upload_new_file($request->file,file_path()['product']['attribute']['path']);
            }catch (\Exception $exp) {

            }
        }


        DigitalProductAttributeValue::create([
            'digital_product_attribute_id' => $digitalAttribute->id,
            'name'  => $request->name,
            'value' => $request->value,
            'file'  => @$file,
            'status'=>1
        ]);



         return back()->with('success', translate("Attribute value store for").$digitalAttribute->name);
    }



    public function attributeValueUpdate(Request $request)
    {

        $this->validate($request,[
            'id'        => 'required',
            'name'        => 'required',
            'file'        => ['nullable',new FileExtentionCheckRule(['jpg', 'jpeg', 'png', 'jfif', 'webp', 'heif','pdf', 'doc', 'exel','csv'],'file')],
            'status'      => 'required:in:0,1'
        ]);

        $value = DigitalProductAttributeValue::where('id', $request->id)
                                                ->firstOrfail();
                                                
        $digitalAttribute = DigitalProductAttribute::where('id',$value->digital_product_attribute_id)->firstOrfail();

        
        $file  = $value->file;
        if($request->hasFile('file')){
            try {
                $file = upload_new_file($request->file, file_path()['product']['attribute']['path'] , $value->file);
            }catch (\Exception $exp) {

            }
        }

        $value->update([
            'name'  => $request->name,
            'value' => $request->value,
            'file'  => @$file,
            'status'=> $request->status,
        ]);

        return back()->with('success', translate("Attribute value updated for").$digitalAttribute->name);
    }


    public function attributeValueDownload($id)
    {
        $value = DigitalProductAttributeValue::where('id', $id)->firstOrfail();

        $file = $value->file;
        $path = file_path()['product']['attribute']['path'].'/'.$file;
        $title = make_slug('file').'-'.$file;
        $mimetype = mime_content_type($path);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($path);


    }

    public function attributeValueDelete(Request $request)
    {
        $value = DigitalProductAttributeValue::where('id', $request->id)->firstOrfail();

        if($value->file) @unlink(file_path()['product']['attribute']['path'] . '/' . $value->file);

        $value->delete();
        return back()->with('success', translate("Attribute value has been deleted"));
    }

}
