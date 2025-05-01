<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    { 
        
        return [
            'id'           => $this->id,
            'uid'          => $this->uid,
            'attribute'    => $this->attribute_value,
            'qty'          => $this->qty,
            'price'        => $this->price,
        ];
    }
}
