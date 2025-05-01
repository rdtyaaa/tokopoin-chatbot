<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusResource extends JsonResource
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
            'id'                => $this->id,
            'payment_note'      => $this->payment_note,
            'payment_status'    => $this->payment_status,
            'delivery_note'     => $this->delivery_note,
            'delivery_status'   => $this->delivery_status,
            'created_at'        => $this->created_at,
        ];
    }
}
