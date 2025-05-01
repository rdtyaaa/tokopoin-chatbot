<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class DigitalAttributeResource extends JsonResource
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
          'id'            => $this->id,
          'uid'           => $this->uid,
          'name'          => $this->name,
          'status'        => $this->status == 1 ? "Active" : 'Sold' ,
          'price'         => api_short_amount($this->price),
          'short_details' => ($this->short_details),
          'values'        => new DigitalAttributeValueCollection($this->digitalProductAttributeValueKey),
          'created_at'    => diff_for_humans($this->created_at)
       ];
    }
}
