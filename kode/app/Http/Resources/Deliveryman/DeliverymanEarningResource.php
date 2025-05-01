<?php

namespace App\Http\Resources\Deliveryman;

use App\Http\Resources\OrderResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliverymanEarningResource extends JsonResource
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
            'order' => new OrderResource($this->order),
            "amount"            => api_short_amount($this->amount),
            "details"           => $this->details,
            "created_at"        => ($this->created_at),
            'human_readable_time'               => diff_for_humans($this->created_at),
            'date_time'                         => get_date_time($this->created_at),
        ];
    }
}
