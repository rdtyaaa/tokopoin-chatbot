<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
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
            'uid'           => $this->uid,
            'name'          => $this->name,
            'code'          => $this->code,
            'discount'      => api_short_amount((double)$this->value,2),
            'discount_type' => $this->type == 1? 'fixed' :"percentage",
        ];
    }
}
