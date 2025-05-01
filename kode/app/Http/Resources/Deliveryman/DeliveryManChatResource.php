<?php

namespace App\Http\Resources\Deliveryman;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryManChatResource extends JsonResource
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
            'id'                =>  $this->id,
            'message'           =>  $this->message,
            'files'             =>  collect($this->files)->map(function($name){
                                       return [$name => @show_image(file_path()['chat']['path'].'/'.$name)];
                                    }),
            'is_seen'           =>  $this->is_seen == 1 ? true : false,
            'sender_id'         =>  $this->sender_id,
            'receiver_id'       =>  $this->receiver_id,
            'created_at'        =>  $this->created_at
        ];
    }
}
