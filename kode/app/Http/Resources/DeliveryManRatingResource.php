<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryManRatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

       $data = [
          'id'         => $this->id,
          'rating'     => $this->rating,
          'message'    => $this->message,
          'order'      => [
                  'id'  => $this->order?->id,
                  'uid' => $this->order?->uid,
                  'order_number' => $this->order?->order_id,
         ],
          'created_at' => get_date_time($this->created_at),
       ];


       if($this->relationLoaded('user') && @$this->user ){
         $data ['user'] = new UserResource($this->user);
       }


       return $data;

    }
}
