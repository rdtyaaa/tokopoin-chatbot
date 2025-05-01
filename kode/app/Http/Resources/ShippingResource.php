<?php

namespace App\Http\Resources;

use App\Enums\StatusEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingResource extends JsonResource
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
            "id"           => $this->id,
            "uid"          => $this->uid,
            'method_name'  => $this->name,
            'duration'     => $this->duration,
            'description'  => $this->description,
            'price_configuration'  => $this->price_configuration,
            'image'        => show_image(file_path()['shipping_method']['path'].'/'.$this->image),
            'is_free_shipping' => $this->free_shipping  == StatusEnum::true->status() ? true : false 
        ];

        if($this->free_shipping  == StatusEnum::false->status()){
            $data ['shipping_type']  = @$this->shipping_type;
        }
        return $data;
 
    }
}
