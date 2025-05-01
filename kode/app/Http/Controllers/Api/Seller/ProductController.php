<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Seller\DigitalProductStoreRequest;
use App\Http\Requests\Api\Seller\DigitalProductUpdateRequest;
use App\Http\Requests\Api\Seller\ProductStoreRequest;
use App\Http\Requests\Api\Seller\ProductUpdateRequest;
use App\Http\Resources\Seller\ProductCollection;
use App\Http\Resources\Seller\ProductResource;
use App\Http\Services\Seller\ProductService;
use App\Models\Cart;
use App\Models\DigitalProductAttribute;
use App\Models\DigitalProductAttributeValue;
use App\Models\PlanSubscription;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductStock;
use App\Models\Seller;
use App\Rules\General\FileExtentionCheckRule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
class ProductController extends Controller
{
    
    protected ? Seller $seller;
    protected ? PlanSubscription $subscription = null;

    public function __construct(protected ProductService $productService){


        $this->middleware(function ($request, $next) {

            $this->seller = auth()->guard('seller:api')->user()?->load(['sellerShop']);

            $this->subscription = PlanSubscription::where('seller_id',$this->seller ->id)
                                     ->where('status',PlanSubscription::RUNNING)
                                     ->where('expired_date','>' ,Carbon::now()->toDateTimeString())
                                     ->first();
    
            //check subscriptions
            if(!$this->subscription)  return api(['errors' => [translate('You dont have any runnig subscription')]])
                                                                   ->fails(__('response.fail'));
        
             // Shop status check
            if(@$this->seller->sellerShop && $this->seller->sellerShop->status == 1 ) return $next($request);

            return api(['errors' => [translate('Your store is not approve yet')]])->fails(__('response.fail'));
 
        });
    }






    /**
     * Get product list 
     * 
     * @param string $type
     *
     * @return JsonResponse
     */
    public function list(string $type) :JsonResponse {


        return api([ 
            'products'                  => new ProductCollection($this->productService->getProductList($type ,$this->seller)),
        ])->success(__('response.success'));

    } 







    /**
     * Delete a specific seller product
     *
     * @param string $uid
     * @return JsonResponse
     */
    public function delete(string $uid) : JsonResponse {

        $product = Product::with(['wishlist','order','digitalProductAttribute'])->where("seller_id",$this->seller->id)
                        ->where('uid',$uid)
                        ->first();
        if(!$product)  return api(['errors' => ['No product found']])->fails(__('response.fail'));

        $cartCount = Cart::where('product_id',$product->id)->count();

        if($cartCount == 0 && $product->order->count() == 0  && $product->wishlist->count() == 0 )  {
            $product->delete();
            return api(
                [
                    'message' => translate('Product deleted successfully'),
                ])->success(__('response.success'));
        }

        return api(['errors' => [translate('This product has order or added in Cart or added in wishList . Please Try Again')]])->fails(__('response.fail'));

   
    }


    /**
     * Restore a specific product 
     *
     * @param string $uid
     * @return JsonResponse
     */
    public function restore(string $uid) :JsonResponse {

        $product = Product::onlyTrashed()->where("seller_id",$this->seller->id)
                                        ->where('uid',$uid)
                                        ->first();

        if(!$product)  return api(['errors' => ['No product found']])->fails(__('response.fail'));
        $product->restore();
        return api(
            [
                'message' => translate('Product restored successfully'),
            ])->success(__('response.success'));


    }



    /**
     * Permanently delete a product 
     *
     * @param string $uid
     * @return JsonResponse
     */
    public function permanentDelete(string  $uid) :JsonResponse {


        $product = Product::with(
            
            [    
                'digitalProductAttribute' 
                ,'digitalProductAttribute.digitalProductAttributeValueKey',
                'shippingDelivery','gallery','stock','rating'
            ]
            )
                                  
                            
            ->onlyTrashed()->where("seller_id",$this->seller->id)
            ->where('uid',$uid)
            ->first();

        if(!$product)  return api(['errors' => ['No product found']])->fails(__('response.fail'));

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

        return api(
            [
                'message' => translate('Product deleted successfully'),
            ])->success(__('response.success'));

    }



    

    /**
     * Get product details
     *
     * @param string $uid
     * @return JsonResponse
     */
    public function details(string $uid) : JsonResponse {


        $product  = Product::with(['category', 'brand', 'subCategory', 'order','gallery','stock','review','review.customer','shippingDelivery','digitalProductAttribute','digitalProductAttribute.digitalProductAttributeValueKey'])
                                    ->sellerProduct()
                                    ->where('seller_id', $this->seller->id)
                                    ->where('uid', $uid)
                                    ->search()
                                    ->latest()
                                    ->first();


         if(!$product)  return api(['errors' => ['No product found']])->fails(__('response.fail'));


         return api([ 
            'product'                  => new ProductResource($product),
        ])->success(__('response.success'));


    }






    /* ==========================================================================
           ==================================================================
       ===========================================================================

            # DIGITAL PRODUCT SECTION  START

       =============================================================================
             ================================================================
       ============================================================================= 
    */





    /**
     * Store a digital product
     *
     * @param DigitalProductStoreRequest $request
     * @return JsonResponse
     */
    public function  digitalStore(DigitalProductStoreRequest $request) : JsonResponse {

        

       if($this->subscription->total_product < 1 ) return api(['errors'=> [translate('You dont have enough product balance to add a new product')]])->fails(__('response.fail'));

    
        $response = $this->productService->storeDigitalProduct($request ,$this->seller);

        $stauts   = Arr::get($response ,'status',false);

        switch (true) {
            case $stauts :

                $this->subscription->total_product -=1;
                $this->subscription->save();

                return api(
                    [
                        'message' => Arr::get($response ,'message'),
                    ])->success(__('response.success'));

                break;
            
            default:
    
                return api(
                    ['errors'=> [Arr::get($response ,'message')]])->fails(__('response.fail'));
                break;
        }


 
    }


    /**
     * Store a digital product
     *
     * @param DigitalProductUpdateRequest $request
     * @return JsonResponse
     */
    public function digitalUpdate(DigitalProductUpdateRequest $request) : JsonResponse {



        if($this->subscription->total_product < 1 ) return api(['errors'=> [translate('You dont have enough product balance to add a new product')]])->fails(__('response.fail'));


        $product  = Product::digital()
                        ->where("seller_id",$this->seller->id)
                        ->where('uid',$request->input('uid'))
                        ->first();

        if(!$product)  return api(['errors' => ['No product found']])->fails(__('response.fail'));
        
        $response = $this->productService->updateDigitalProduct($request ,$this->seller , $product);

        $stauts   = Arr::get($response ,'status',false);

        switch (true) {
            case $stauts :


                return api(
                    [
                        'message' => Arr::get($response ,'message'),
                    ])->success(__('response.success'));

                break;
            
            default:
    
                return api(
                    ['errors'=> [Arr::get($response ,'message')]])->fails(__('response.fail'));
                break;
        }
    }





    /**
     * Store a fdigital product attribute
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function attributeStore(Request $request) : JsonResponse {

        $validator = Validator::make($request->all(),[
            'uid'           => 'required|exists:products,uid',
            'name'          => 'required|max:255',
            'short_details' => 'max:255',
            'price'         => 'required|numeric|gt:0',
        ]);

        if ($validator->fails())  return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));



        $product = Product::sellerProduct()
                        ->digital()->where('seller_id', $this->seller->id)
                        ->where('uid', $request->input('uid'))->first();


        if(!$product)  return api(['errors' => ['No product found']])->fails(__('response.fail'));
        
        DigitalProductAttribute::create([
            "product_id"    => $product->id,
            "name"          => $request->input('name'),
            "price"         => $request->input('price'),
            "short_details" => $request->input('short_details'),
        ]);


        return api([ 
            'product'                  => new ProductResource($product->load(['digitalProductAttribute','digitalProductAttribute.digitalProductAttributeValueKey'])),
        ])->success(__('response.success'));


    }


    

    /**
     * Store atrribute value
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function attributeValueStore(Request $request) : JsonResponse {

        $validator = Validator::make($request->all(),[
            'uid'           => 'required|exists:digital_product_attributes,uid',
            'name'        => 'required',
            'file'        => ['nullable',new FileExtentionCheckRule(['jpg', 'jpeg', 'png', 'jfif', 'webp', 'heif','pdf', 'doc', 'exel','csv'],'file')],
        ]);


        if ($validator->fails())  return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

        $digitalAttribute  = DigitalProductAttribute::where('uid',$request->input('uid'))->first();

        if(!$digitalAttribute)  return api(['errors' => ['Attribute not found']])->fails(__('response.fail'));

        $product = Product::sellerProduct()
                                ->digital()->where('seller_id', $this->seller->id)
                                ->where('id', $digitalAttribute->product_id)
                                ->first();


        if(!$product)  return api(['errors' => ['No product found']])->fails(__('response.fail'));


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



        return api(
            [
                'product'                  => new ProductResource($product->load(['digitalProductAttribute','digitalProductAttribute.digitalProductAttributeValueKey'])),
            ])->success(__('response.success'));

    }




       /**
     * Store atrribute value
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function attributeValueUpdate(Request $request) : JsonResponse {

        $validator = Validator::make($request->all(),[
            'uid'           => 'required|exists:digital_product_attributes,uid',
            'value_uid'     => 'required|exists:digital_product_attribute_values,uid',
            'name'          => 'required',
            'status'        => 'required|in:1,0',
            'file'          => ['nullable',new FileExtentionCheckRule(['jpg', 'jpeg', 'png', 'jfif', 'webp', 'heif','pdf', 'doc', 'exel','csv'],'file')],
        ]);


        if ($validator->fails())  return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

        $digitalAttribute  = DigitalProductAttribute::where('uid',$request->input('uid'))->first();

        if(!$digitalAttribute)  return api(['errors' => ['Attribute not found']])->fails(__('response.fail'));

        $product = Product::sellerProduct()
                                ->digital()->where('seller_id', $this->seller->id)
                                ->where('id', $digitalAttribute->product_id)
                                ->first();

        if(!$product)  return api(['errors' => ['No product found']])->fails(__('response.fail'));


        $value = DigitalProductAttributeValue::where('uid',$request->input('value_uid'))->first();
        if(!$value)  return api(['errors' => ['No Attribute value found']])->fails(__('response.fail'));


        $file = $value->file;
        if($request->hasFile('file')){
            try {
                $file = upload_new_file($request->file,file_path()['product']['attribute']['path']);
            }catch (\Exception $exp) {

            }
        }

        $value->update([
            'name'  => $request->name,
            'value' => $request->value,
            'file'  => @$file,
            'status'=> $request->status
        ]);

        return api(
            [
                'product'                  => new ProductResource($product->load(['digitalProductAttribute','digitalProductAttribute.digitalProductAttributeValueKey'])),
            ])->success(__('response.success'));

    }






    /**
     * Attribute value delete
     *
     * @param string $uid
     * @return JsonResponse
     */
    public function attributeValueDelete(string $uid)  : JsonResponse {

        $attribute = DigitalProductAttributeValue::where('uid',$uid)->first();

        if(!$attribute)  return api(['errors' => [translate('Invalud attribute value')]])->fails(__('response.fail'));

        $digitalAttribute  = DigitalProductAttribute::where('id',$attribute->digital_product_attribute_id)->first();



        if(!$digitalAttribute)  return api(['errors' => ['Attribute not found']])->fails(__('response.fail'));

        $product = Product::sellerProduct()
                                ->digital()->where('seller_id', $this->seller->id)
                                ->where('id', $digitalAttribute->product_id)
                                ->first();


        if(!$product)  return api(['errors' => ['No product found']])->fails(__('response.fail'));


        if($attribute->file) @unlink(file_path()['product']['attribute']['path'] . '/' . $attribute->file);

        $attribute->delete();

        return api(
            [
                'product'                  => new ProductResource($product->load(['digitalProductAttribute','digitalProductAttribute.digitalProductAttributeValueKey'])),
            ])->success(__('response.success'));


    }



    /**
     * Attribute delete
     *
     * @param string $uid
     * @return JsonResponse
     */
    public function attributeDelete(string $uid)  : JsonResponse {

     
  
        $digitalAttribute  = DigitalProductAttribute::with(['digitalProductAttributeValueKey'])
                                                        ->where('uid',$uid)
                                                        ->first();



        if(!$digitalAttribute)  return api(['errors' => ['Attribute not found']])->fails(__('response.fail'));

        $product = Product::sellerProduct()
                                ->digital()->where('seller_id', $this->seller->id)
                                ->where('id', $digitalAttribute->product_id)
                                ->first();


        if(!$product)  return api(['errors' => ['No product found']])->fails(__('response.fail'));


        if($digitalAttribute->digitalProductAttributeValueKey->count() >  0){
            foreach($digitalAttribute->digitalProductAttributeValueKey as $value){
                if($value->file) @unlink(file_path()['product']['attribute']['path'] . '/' . $value->file);
                $value->delete();
            }
        } 



        $digitalAttribute->delete();

        return api(
            [
                'product'                  => new ProductResource($product->load(['digitalProductAttribute','digitalProductAttribute.digitalProductAttributeValueKey'])),
            ])->success(__('response.success'));


    }




    /**
     * Attribute delete
     *
     * @return JsonResponse
     */
    public function attributeUpdate(Request $request)  : JsonResponse {

        $validator = Validator::make($request->all(),[
            'uid'           => 'required|exists:digital_product_attributes,uid',
            'name'          => 'required|max:255',
            'price'         => 'required|numeric|gt:0',
            'status'        => 'required|in:1,0',
        ]);

        if ($validator->fails())  return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

     
  
        $digitalAttribute  = DigitalProductAttribute::with(['digitalProductAttributeValueKey'])
                                                        ->where('uid',$request->uid)
                                                        ->first();



        if(!$digitalAttribute)  return api(['errors' => ['Attribute not found']])->fails(__('response.fail'));

        $product = Product::sellerProduct()
                                ->digital()->where('seller_id', $this->seller->id)
                                ->where('id', $digitalAttribute->product_id)
                                ->first();

        if(!$product)  return api(['errors' => ['No product found']])->fails(__('response.fail'));


        $digitalAttribute->name          = $request->name;
        $digitalAttribute->short_details = $request->short_details;
        $digitalAttribute->price         = $request->price;
        $digitalAttribute->status        = $request->status;
        $digitalAttribute->save();

        return api(
            [
                'product'                  => new ProductResource($product->load(['digitalProductAttribute','digitalProductAttribute.digitalProductAttributeValueKey'])),
            ])->success(__('response.success'));


    }







    /*  ==========================================================================
           ==================================================================
        ===========================================================================

        # DIGITAL PRODUCT SECTION  END

        =============================================================================
                ================================================================
        ============================================================================= 
    */



    

    /* ==========================================================================
           ==================================================================
       ===========================================================================

            # PHYISICAL PRODUCT SECTION  START

       =============================================================================
             ================================================================
       ============================================================================= 
    */





        /**
         * Store a product
         *
         * @param ProductStoreRequest $request
         * @return JsonResponse
         */
       public function store(ProductStoreRequest $request) : JsonResponse {

           if($this->subscription->total_product < 1 ) return api(['errors'=> [translate('You dont have enough product balance to add a new product')]])->fails(__('response.fail'));

            $response = $this->productService->store($request ,$this->seller);

            $stauts   = Arr::get($response ,'status',false);

            switch (true) {
                case $stauts :


                    return api(
                        [
                            'message' => Arr::get($response ,'message'),
                        ])->success(__('response.success'));

                    break;
                
                default:
        
                    return api(
                        ['errors'=> [Arr::get($response ,'message')]])->fails(__('response.fail'));
                    break;
            }

       }



        /**
         * Update a product
         *
         * @param ProductStoreRequest $request
         * @return JsonResponse
         */
        public function update(ProductUpdateRequest $request) : JsonResponse {



            $product  = Product::physical()
                                    ->where("seller_id",$this->seller->id)
                                    ->where('uid',$request->input('uid'))
                                    ->first();


            if(!$product)  return api(['errors' => ['No product found']])->fails(__('response.fail'));


            $response = $this->productService->update($request ,$this->seller , $product);

            $stauts   = Arr::get($response ,'status',false);

            switch (true) {
                case $stauts :


                    return api(
                        [
                            'message' => Arr::get($response ,'message'),
                        ])->success(__('response.success'));

                    break;
                
                default:
        
                    return api(
                        ['errors'=> [Arr::get($response ,'message')]])->fails(__('response.fail'));
                    break;
            }


        }




        
        /**
         * Delete a product galleryImage
         *
         * @param string $id
         * @return JsonResponse
         */
        public function galleryDelete(string $id) : JsonResponse {


            $productGallery = ProductImage::find($id);

            if(!$productGallery)  return api(['errors' => ['No image found']])->fails(__('response.fail'));

            $product = Product::sellerProduct()
                                ->physical()
                                ->where('seller_id', $this->seller->id)
                                ->where('id', $productGallery->product_id)
                                ->first();


            if(!$product)  return api(['errors' => ['No product found']])->fails(__('response.fail'));


            $file = file_path()['product']['gallery']['path'].'/'.$productGallery->image;

            if(file_exists($file))  @unlink($file);



            $productGallery->delete();


            return api(
                [
                    'message' => translate('Product gallery deleted  successfully'),
                ])->success(__('response.success'));


        }






        /**
         * Update product stock
         *
         * @param Request $request
         * @return JsonResponse
         */
        public function stockUpdate(Request $request) : JsonResponse {


    
            $validator = Validator::make($request->all(),[
                'uid'                        => 'required|exists:products,uid',
                'stock'                      => 'required|array',
                'stock.id.*'                 => 'required|exists:product_stocks,id',
                'stock.price.*'              => 'required|numeric|gt:0',
                'stock.quantity.*'           => 'required|numeric|gt:0',
            ]);
    
            if ($validator->fails()){
                return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));
            }


            $product  = Product::physical()
                                ->where("seller_id",$this->seller->id)
                                ->where('uid',$request->input('uid'))
                                ->first();


            if(!$product)  return api(['errors' => ['No product found']])->fails(__('response.fail'));


            
            $stocks = collect(request()->input('stock'));
            $ids        = $stocks->get('id', []);
            $prices     = $stocks->get('price', []);
            $quantities = $stocks->get('quantity', []);
        
            $attributes = collect(  $ids )->map(function (int $id, int $index) use( $product , $prices , $quantities )  {  

     
                ProductStock::where("product_id" ,$product->id)->where('id',$id)
                                   ->update([
                                       "qty" =>  @$quantities[$index] ,
                                       "price" =>  @$prices[$index] 
                                   ]);
   
            });

            return api(
                [
                    'message' => translate('Product stock updated'),
                ])->success(__('response.success'));








        }




    /* ==========================================================================
           ==================================================================
       ===========================================================================

            # PHYISICAL PRODUCT SECTION  END

       =============================================================================
             ================================================================
       ============================================================================= 
    */






















































    

    


}
