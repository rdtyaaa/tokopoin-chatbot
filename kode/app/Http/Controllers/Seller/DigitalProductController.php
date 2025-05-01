<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Models\DigitalProductAttribute;
use App\Http\Requests\DigitalProductRequest;
use App\Http\Requests\DigitalProductUpdateRequest;
use App\Models\Cart;
use App\Models\DigitalProductAttributeValue;
use App\Models\PlanSubscription;
use App\Models\Tax;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DigitalProductController extends Controller
{
    public function __construct()
    {
         $this->middleware('sellercheckstatus');
    }

    public function index()
    {
        $title  = translate('Manage digital product');
        $seller = auth()->guard('seller')->user();
        $digitalProducts = Product::sellerProduct()->digital()->where('seller_id', $seller->id)->orderBy('id', 'DESC')->with('category','subCategory')->paginate(site_settings('pagination_number',10));

        return view('seller.digital.index', compact('title', 'digitalProducts'));
    }

    public function new()
    {
        $title = translate('Manage digital new product');
        $seller = auth()->guard('seller')->user();
        $digitalProducts = Product::sellerProduct()->digital()->where('seller_id', $seller->id)->new()->orderBy('id', 'DESC')->with('category','subCategory')->paginate(site_settings('pagination_number',10));
        return view('seller.digital.index', compact('title', 'digitalProducts'));
    }

    public function approved()
    {
        $title = translate('Manage digital approved product');
        $seller = auth()->guard('seller')->user();
        $digitalProducts = Product::sellerProduct()->digital()->where('seller_id', $seller->id)->published()->orderBy('id', 'DESC')->with('category','subCategory')->paginate(site_settings('pagination_number',10));
        return view('seller.digital.index', compact('title', 'digitalProducts'));
    }

    public function create()
    {
        $title      = translate('Add new digital product');
        $categories = Category::where('status', '1')->with('parent')->select('id', 'name')->get();
        $taxes = Tax::active()->get();
        return view('seller.digital.create', compact('title', 'categories','taxes'));
    }

    public function store(DigitalProductRequest $request)
    {

        $subscription = PlanSubscription::where('seller_id',Auth::guard('seller')->user()->id)->where('status',1)->first();
        if(!$subscription){
           return back()->with('error',translate('You dont have any runnig subscription'));
        }
        if($subscription->total_product < 1 ){
            return back()->with('error',translate('You dont have enough product balance to add a new product'));
        }
        $featuredImage = null;

        if($request->hasFile('featured_image')){
            try {
                $featuredImage = store_file($request->featured_image,file_path()['product']['featured']['path']);
            }catch (\Exception $exp) {

            }
        }
        $seller = auth()->guard('seller')->user();

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
            'slug'=> make_slug($request->slug),
            'point'=> ($request->point),
            'seller_id'=> $seller->id,
            'product_type'=> Product::DIGITAL,
            'category_id'=> $request->category_id,
            'sub_category_id'=> $request->subcategory_id,
            'description'=> build_dom_document($request->description,'seller_digital_des'.rand(10,200)),
            'meta_title'=> $request->meta_title ?? null,
            'meta_keywords'=> $request->meta_keywords ?? null,
            'meta_description'=> $request->meta_description ?? null,
            'meta_image'=> $featuredImage,
            'featured_image'=> $featuredImage,
            'status'=> Product::NEW,
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

       $subscription->total_product -=1;

       $subscription->save();



        return back()->with('success', translate("Digital product has been created"));
    }

    public function edit($id)
    {
        $title = translate('Update digital product');
        $seller = auth()->guard('seller')->user();
        $categories = Category::where('status', '1')->select('id', 'name')->with('parent')->get();
        $product = Product::sellerProduct()->digital()->where('seller_id', $seller->id)->where('id', $id)->firstOrFail();

        $taxes = Tax::with(['products'=> function(BelongsToMany $q) use($product) :BelongsToMany{
            return $q->where('product_id', $product->id);
        }])->active()->get();

        return view('seller.digital.edit', compact('title', 'categories', 'product','taxes'));
    }


    public function update(DigitalProductUpdateRequest $request, $id)
    {
        $seller = auth()->guard('seller')->user();
        $product = Product::sellerProduct()->digital()->where('seller_id', $seller->id)->where('id', $id)->firstOrFail();
         $featuredImage = $product->featured_image;

        if($request->hasFile('featured_image')){
            try {
                $featuredImage = store_file($request->featured_image,file_path()['product']['featured']['path'],null, $product->featured_image);
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
            'name'=> $request->name,
            'slug'=> make_slug($request->slug),
            'seller_id'=> $seller->id,
            'point'=> ($request->point),
            'product_type'=> Product::DIGITAL,
            'category_id'=> $request->category_id,
            'sub_category_id'=> $request->subcategory_id,
            'description'=> build_dom_document($request->description,'seller_digital_des_editA'.rand(10,200)),
            'meta_title'=> $request->meta_title ?? null,
            'meta_keywords'=> $request->meta_keywords ?? null,
            'meta_description'=> $request->meta_description ?? null,
            'meta_image'=> $featuredImage,
            'featured_image'=> $featuredImage,
            'status'=> Product::NEW,
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


    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:products,id'
        ]);

        $product = Product::with(['digitalProductAttribute'=>function($q){
            return $q->with(['digitalProductAttributeValueKey']);
        }])->sellerProduct()->digital()->where('id',$request->id)->first();

        $cart = Cart::where('product_id', $request->id)->get();

         if(count($product->order) == 0 &&  count($product->wishlist) == 0 && count($cart) == 0)  {
            $product->delete();

            return back()->with('success', translate("Product has been deleted"));
         }

         else{
            return back()->with('error', translate("This Product Has Order or Added In Cart Or In WishList, Plese Try Again"));
         }
    }

    public function attribute($id)
    {
        $seller = auth()->guard('seller')->user();
        $product = Product::sellerProduct()->digital()->where('seller_id', $seller->id)->where('id', $id)->firstOrFail();
        $title = ucfirst($product->name)." Attribute List";
        $digitalProductAttributes = DigitalProductAttribute::where('product_id', $product->id)->latest()->paginate(site_settings('pagination_number',10));
        return view('seller.digital.attribute', compact('title', 'digitalProductAttributes', 'product'));
    }


    public function attributeStore(Request $request)
    {
        $data = $this->validate($request,[
            'product_id' => 'required|exists:products,id',
            'name' => 'required|max:255',
            'short_details' =>'',
            'price' => 'required|numeric|gt:0',
        ]);

        $seller = auth()->guard('seller')->user();
        $product = Product::sellerProduct()->digital()->where('seller_id', $seller->id)->where('id', $request->product_id)->firstOrFail();
        DigitalProductAttribute::create($data);
        return back()->with('success', translate("Digital product attribute has been created"));

    }

    public function attributeEdit($id)
    {
        $seller = auth()->guard('seller')->user();
        $digitalProductAttribute = DigitalProductAttribute::findOrFail($id);
        $product = Product::sellerProduct()->digital()->where('seller_id', $seller->id)->where('id', $digitalProductAttribute->product_id)->firstOrFail();
        $title = "Attribute value store for ".$digitalProductAttribute->name;
        $digitalProductAttributeValues = DigitalProductAttributeValue::where('digital_product_attribute_id', $digitalProductAttribute->id)->paginate(site_settings('pagination_number',10));
        return view('seller.digital.attribute_edit', compact('title', 'digitalProductAttribute', 'digitalProductAttributeValues'));
    }

    public function attributeValueStore(Request $request, $id)
    {
        $this->validate($request,[
            'name'        => 'required',
            'file'        => ['nullable',new FileExtentionCheckRule(['jpg', 'jpeg', 'png', 'jfif', 'webp', 'heif','pdf', 'doc', 'exel','csv'],'file')],
        ]);

      
        $seller = auth()->guard('seller')->user();
        $digitalAttribute = DigitalProductAttribute::findOrFail($id);
        $product = Product::sellerProduct()->digital()->where('seller_id', $seller->id)->where('id', $digitalAttribute->product_id)->firstOrFail();


        
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




      

        return back()->with('success', translate("Attribute value stored"));
    }

    public function attributeValueDelete(Request $request)
    {
        $seller = auth()->guard('seller')->user();
        $value = DigitalProductAttributeValue::findOrFail($request->id);
        $digitalProductAttribute = DigitalProductAttribute::findOrFail($value->digital_product_attribute_id);
        $product = Product::sellerProduct()->digital()->where('seller_id', $seller->id)->where('id', $digitalProductAttribute->product_id)->firstOrFail();
        if($value->file) @unlink(file_path()['product']['attribute']['path'] . '/' . $value->file);
        $value->delete();
        return back()->with('success', translate("Attribute value has been deleted"));
    }




    public function attributeDelete(Request $request)
    {

        $seller = Auth::guard('seller')->user();
        $this->validate($request, [
            'id' => 'required|exists:digital_product_attributes,id'
        ]);

        $digitalProductAttribute = DigitalProductAttribute::with(['digitalProductAttributeValueKey'])
                                        ->where('id', $request->id)
                                        ->firstOrfail();

        $product = Product::where('id',$digitalProductAttribute->product_id)
                                ->where("seller_id",$seller->id)
                                ->firstOrfail();


        if($digitalProductAttribute->digitalProductAttributeValueKey->count() >  0){
            foreach($digitalProductAttribute->digitalProductAttributeValueKey as $value){
                if($value->file) @unlink(file_path()['product']['attribute']['path'] . '/' . $value->file);
                $value->delete();
            }
        } 

            

        $digitalProductAttribute->delete();
        

        return back()->with('success', translate("Attribute value has been deleted"));
    }



    public function restore(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:products,id'
        ]);
        $seller = Auth::guard('seller')->user();
        $product = Product::sellerProduct()->digital()->where('seller_id', $seller->id)->where('id', $request->id)->restore();
        return back()->with('success', translate("Product has been restore"));
    }



    public function attributeUpdate(Request $request)
    {
       $this->validate($request, [
            'id'  => 'required',
            'name' => 'required|max:255',
            'price' => 'required|numeric|gt:0',
            'status' => 'required|in:1,0',
        ]);
   
        $seller = Auth::guard('seller')->user();
        $digitalProductAttribute = DigitalProductAttribute::where('id', $request->id)->firstOrfail();

        $product = Product::where('id',$digitalProductAttribute->product_id)
                                ->where("seller_id",$seller->id)
                                ->firstOrfail();

        $digitalProductAttribute->name = $request->name;
        $digitalProductAttribute->short_details = $request->short_details;
        $digitalProductAttribute->price = $request->price;
        $digitalProductAttribute->status = $request->status;
        $digitalProductAttribute->save();
        return back()->with('success', translate("Digital product attribute has been updated"));
    }


    public function attributeValueUpdate(Request $request)
    {

        $this->validate($request,[
            'id'        => 'required',
            'name'        => 'required',
            'file'        => ['nullable',new FileExtentionCheckRule(['jpg', 'jpeg', 'png', 'jfif', 'webp', 'heif','pdf', 'doc', 'exel','csv'],'file')],
            'status'      => 'required:in:0,1'
        ]);


        $seller = Auth::guard('seller')->user();

        $value = DigitalProductAttributeValue::where('id', $request->id)
                                                ->firstOrfail();
                                                
        $digitalAttribute = DigitalProductAttribute::where('id',$value->digital_product_attribute_id)->firstOrfail();

        $product = Product::where('id',$digitalAttribute->product_id)
                                            ->where("seller_id",$seller->id)
                                            ->firstOrfail();
            
        $file = $value->file;
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
}
