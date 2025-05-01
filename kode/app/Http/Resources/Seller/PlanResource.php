<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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
            'id'                 => $this->id,
            'name'               => $this->name,
            'amount'             => api_short_amount($this->amount),
            'total_product'      => $this->total_product,
            'duration'           => $this->duration,
            'duration_unit'      => 'DAY',
        ];
    }
}
