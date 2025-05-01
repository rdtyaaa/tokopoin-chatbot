<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'uid'                => $this->uid,
            'pirce'              => api_short_amount((double)$this->price),
            'qty'                => $this->quantity,
            'total'              => api_short_amount((double)$this->total),
            'original_total'     => api_short_amount((double)($this->original_price-$this->total_taxes)*$this->quantity),
            'total_taxes'        => api_short_amount((double)$this->total_taxes*$this->quantity),
            'discount'           => api_short_amount((double)$this->discount*$this->quantity),
            'varitent' => $this->attributes_value,
            'product'  => new ProductResource($this->product),
            'taxes'    => $this->taxes,
        ];
    }
}
