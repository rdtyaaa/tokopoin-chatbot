<?php

namespace App\Http\Resources;

use App\Http\Resources\Deliveryman\DeliveryManResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliverymanConversationResource extends JsonResource
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
            'id'               =>  $this->id,
            'message'          =>  $this->message,
            'files'            =>  collect($this->files)->map(function($name){
                                       return [$name => @show_image(file_path()['chat']['path'].'/'.$name)];
                                   }),
            'is_seen'          =>  $this->is_seen == 1 ? true : false,
            'sender_role'      =>  [
                "role"  =>  $this->sender_role,
                "user"  =>  $this->sender_role == 'customer' 
                                ? new UserResource($this->customer) 
                                : new DeliveryManResource($this->deliveryMan)
            ],
            'created_at'      =>  $this->created_at
        ];
    }
}
