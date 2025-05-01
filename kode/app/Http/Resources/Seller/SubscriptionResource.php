<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'plan'          => new PlanResource($this->plan),
            'total_product' => $this->total_product,
            'expired_date'  => get_date_time( $this->expired_date),
            'creation_date' => $this->created_at,
            'human_readable_time'               => diff_for_humans($this->created_at),
            'date_time'                         => get_date_time($this->created_at),
            'status'        => $this->status,
            'status_enum'    => [
                'Running'    => 1,
                'Expired'   => 2,
                'Requested'    => 3,
                'Inactive'     => 4,
            ],


        ];
    }
}
