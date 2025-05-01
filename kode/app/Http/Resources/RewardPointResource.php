<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardPointResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $product = $this->product;

        if($product){

            $productResource  = $product->product_type == Product::DIGITAL 
                                        ?  new DigitalProductResource($product)
                                        :  new ProductResource($product);
                                    
        }

        return [
            'id'                    => $this->id,
            'product'               => $product ? [
                                        'resource'       => $productResource,
                                        'is_digital'     =>  $product->product_type == Product::DIGITAL,
                                    ]  : null,
        
            'order'                 => new OrderResource($this->order),
            'point'                 => $this->point,
            'status'                => $this->status,
            'expired_at'            => $this->expired_at,
            'created_at'            => $this->created_at,
            'redeemed_at'           => $this->redeemed_at,
            'details'               => $this->details,
        ];
    }
}
