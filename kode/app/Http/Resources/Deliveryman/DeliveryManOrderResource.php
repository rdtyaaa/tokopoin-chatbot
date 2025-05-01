<?php

namespace App\Http\Resources\Deliveryman;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryManOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data =  [
            'id'              => $this->id,
            'status'          => $this->status,
            'note'            => $this->note,
            'feedback'        => $this->feedback,
            'pickup_location'  => $this->pickup_location,
            'amount'          => api_short_amount($this->amount),
            'details'         => $this->details,
            'assign_to'       => new DeliveryManResource($this->deliveryMan),
            'assign_by'       => new DeliveryManResource($this->assignBy),
            'created_at'      => diff_for_humans($this->created_at),
            'time_line'       => $this->time_line,
        ];
        if($this->relationLoaded('order')){
            $data['order'] = [
                'order_id'    => $this->order->id,
                'order_number' => $this->order->order_id,
            ];
        }

        return $data;
    }
}
