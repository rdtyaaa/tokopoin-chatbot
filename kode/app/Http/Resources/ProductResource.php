<?php

namespace App\Http\Resources;

use App\Models\Campaign;
use App\Models\CampaignProduct;
use App\Models\ShippingDelivery;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class ProductResource extends JsonResource
{


 
    public $campaignProduct = null;

    public function campaign($campaignProduct){
        $this->campaignProduct =  $campaignProduct;
    }
   
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

            
        $feature_price  = @$this->stock?->first()->price ?? 0;

        $discount  = api_short_amount((double)cal_discount(@$this->discount_percentage?? 0,   $feature_price));
        if($this->pivot){
            $discount  = api_short_amount((double)discount( $feature_price,$this->pivot->discount,$this->pivot->discount_type));
        }
     
        $gallery_image = [];
        foreach($this->gallery as $gallery) {
            array_push( $gallery_image, show_image(file_path()['product']['gallery']['path'].'/'.$gallery->image));
        }

        $varient =  [];
        if($this->attributes_value){
            foreach (json_decode($this->attributes_value) as $key => $attr_val){
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
        }




        $varient_price = [];

        if($this->stock){
            foreach($this->stock as $stock){
                $price  = $stock->price ;
                $base_discount             = ((double)cal_discount($this->discount_percentage,$price));
                $varient_discount          = api_short_amount($base_discount);

                if($this->pivot)  {
                    $base_discount         =  (double)discount($price,$this->pivot->discount,$this->pivot->discount_type);
                    $varient_discount          = api_short_amount($base_discount);
                }


                $varient_price [$stock->attribute_value] = [
                    'qty'                   => $stock->qty,
                    'base_price'            => $stock->price,
                    'display_name'          => $stock->display_name ? $stock->display_name  : $stock->attribute_value  ,
                    'base_discount'         => $base_discount,
                    'price'                 => api_short_amount((double)$price),
                    'discount'              => $varient_discount ,
                ];
            }
        }

        $shipping_data =  [];

     
        if(($this->shippingDelivery)){
            $ids             = $this->shippingDelivery->pluck("shipping_delivery_id")->toArray();
            $shipping_data   = ShippingDelivery::with(['method'])->whereIn('id',$ids )->get();
        }

        $reviews =  [];

        if($this->review){
            $reviewData  = array();
            foreach($this->review as $review){
                $reviewData ['user']    = $review->customer?->name;
                $reviewData ['profile'] = show_image(file_path()['profile']['user']['path'].'/'.$review->customer?->image);
                $reviewData ['review']  = $review->review;
                $reviewData ['rating']  = (int) $review->rating ;
            }
            array_push($reviews, $reviewData);
        }


        return [
            'id'                    => $this->id,
            'uid'                   => $this->uid,
            'name'                  => $this->name,
            'slug'                  => $this->slug,
            'order'                 => $this->order->count(),
            'brand'                 => $this->brand? json_decode( $this->brand->name,true) :(object)[], 
            'category'              => $this->category? json_decode( $this->category->name,true) : (object)[], 

            'price'                  => api_short_amount((double)$feature_price),
            'weight'                 => round($this->weight,site_settings('digit_after_decimal',2)),
            'shipping_fee'           => round($this->shipping_fee,site_settings('digit_after_decimal',2)),
            'shipping_fee_multiply_by_qty'           => $this->shipping_fee == 0 ? false : true,
            
            'discount_amount'       => $discount ,
            'short_description'     => $this->short_description,
            'description'           => $this->description,
            'maximum_purchase_qty'  => $this->maximum_purchase_qty,
            'minimum_purchaseqty'   => $this->minimum_purchase_qty,
            'club_point'            => $this->point,
            'rating' => [
                'total_review' => count($this->review),
                'avg_rating'   =>  $this->review->avg('rating') ? ($this->review->avg('rating')) :0 ,
                'review'       => count($this->review) > 0  ? ($reviews) : (object)[]
            ],
            'featured_image'   => show_image(file_path()['product']['featured']['path'].'/'.$this->featured_image),
            'gallery_image'    => $gallery_image ,
            'varient'          => $varient,
            'varient_price'    => $varient_price,
            'shipping_info'    => new ShippingCollection( $shipping_data),
            'url'              => route('product.details',[$this->slug ? $this->slug :  make_slug($this->name),$this->id]),
            'seller'           => new SellerResource($this->seller),

            'taxes' => new TaxCollection($this->taxes)
        ];
    
    }
}
