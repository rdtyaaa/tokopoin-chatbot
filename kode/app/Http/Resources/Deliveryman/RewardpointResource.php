<?php

namespace App\Http\Resources\Deliveryman;

use App\Http\Resources\Seller\OrderResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardpointResource extends JsonResource
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
            'id'                    => $this->id,
            'order'                 => $this->order ? new OrderResource($this->order) : null,
            'post_point'            => $this->post_point,
            'point'                 => $this->point,
            'created_at'            => $this->created_at,
            'human_readable_time'   => diff_for_humans($this->created_at),
            'details'               => $this->details,
        ];
    }
}
