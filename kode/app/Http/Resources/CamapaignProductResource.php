<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\ShippingDelivery;
use Illuminate\Http\Resources\Json\JsonResource;

class CamapaignProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
    

        $product =  Product::with(['gallery','review','order','stock','order','rating','shippingDelivery','shippingDelivery.shippingDelivery'])->where('id',$this->product_id)->first();

        $reviews =  [];
        if($product->review){
            $reviewData  = array();
            foreach($product->review as $review){
                $reviewData ['user']    = $review->customer?->name;
                $reviewData ['profile'] = show_image(file_path()['profile']['user']['path'].'/'.$review->customer?->image);
                $reviewData ['review']  = $review->review;
                $reviewData ['rating']  = (int) $review->rating ;
            }
            array_push($reviews, $reviewData);
        }


        $feature_price = @$product->stock?->first()->price ?? 0;

        $discount  = api_short_amount((double)discount($feature_price ,$product->discount,$product->discount_type));

        $gallery_image = [];
        foreach($product->gallery as $gallery){
            array_push( $gallery_image, show_image(file_path()['product']['gallery']['path'].'/'.$gallery->image));
        }

        $varient =  [];
        foreach (json_decode($product->attributes_value) as $key => $attr_val){
            $stock =  [];
            $attributeOption =  get_cached_attributes()->find($attr_val->attribute_id); 
            $attributValues  =  @$attributeOption->value;
            if( $attributeOption){
                foreach ($attr_val->values as $key => $value){

                    $displayName =  $value;
                    if($attributValues){
                      $attributeValue =  $attributValues->where('name',$value)->first();
                      if($attributeValue){
                          $displayName = $attributeValue->display_name 
                                              ?  $attributeValue->display_name 
                                              : $attributeValue->name;
                      }
                    }

                    array_push($stock,    [
                        'name'         => $value,
                        'display_name' => $displayName,
                    ]);

                }
                array_push($varient,    [
                    'name'      => $attributeOption->name,
                    'stock_attributes'     => $stock,
                ]);
            }
        }

        $varient_price = [];



        foreach($product->stock as $stock){

            $price = $stock->price ;

            $base_discount     = ((double)discount($price ,$product->discount,$product->discount_type));
            $varient_discount  = api_short_amount((double)discount($price ,$product->discount,$product->discount_type));

            $varient_price [$stock->attribute_value] = [

                'qty'                   => $stock->qty,
                'base_price'            => $stock->price,
                'display_name'          => $stock->display_name ? $stock->display_name  : $stock->attribute_value  ,
                'base_discount'         => $base_discount,
                'price'                 => api_short_amount((double)$price),
                'discount'              => $varient_discount ,
            ];
        }


        $shipping_data =  [];
        if($product->shippingDelivery){

            $ids            = $product->shippingDelivery
                                      ->pluck("shipping_delivery_id")
                                      ->toArray();

            $shipping_data  = ShippingDelivery::whereIn('id',$ids )->get();
        }


        return [
            'uid'                 => $product->uid,
            'name'                => $product->name,
            'weight'              => round($product->weight,site_settings('digit_after_decimal',2)),

            'shipping_fee'           => round($product->shipping_fee,site_settings('digit_after_decimal',2)),
            'shipping_fee_multiply_by_qty'           => $product->shipping_fee == 0 ? false : true,
            'order'               => $product->order->count(),
            'price'               => api_short_amount((double)$feature_price),
            'discount_amount'     => $discount,
            'short_description'   => $product->short_description,
            'description'         => $product->description,
            'club_point'          => $this->point,
            'brand'               => $product->brand? json_decode( $product->brand->name,true) :(object)[], 
            'category'            => $product->category? json_decode($product->category->name,true) : (object)[], 
            'rating'              =>    [
                                            'total_review' => count($product->review),
                                            'avg_rating' =>  $product->review->avg('rating') ? ($product->review->avg('rating')) :0 ,
                                            'review' => count($product->review) > 0 ? ($reviews) : (object)[]
                                        ],
            'featured_image'      => show_image(file_path()['product']['featured']['path'].'/'.$product->featured_image),
            'gallery_image'       => $gallery_image ,
            'varient'             => $varient,
            'varient_price'       => $varient_price,
            'shipping_info'       => new ShippingCollection(  $shipping_data),
            'url'                 => route('product.details',[$product->slug ? $product->slug :  make_slug($product->name),$product->id]),
            
            'taxes' =>  new TaxCollection($product->taxes)
        ];
    }
}
