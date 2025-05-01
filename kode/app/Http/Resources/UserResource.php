<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'uid'             => $this->uid,
            "fcm_token"       => $this->fcm_token,
            'id'              => $this->id,
            'name'            => $this->name,
            'username'        => $this->username,
            'email'           => $this->email,
            'phone'           => $this->phone,
            'point'           => $this->point,
            'balance'         => (double)api_short_amount($this->balance),
            'image'           => show_image(file_path()['profile']['user']['path'].'/'.$this->image),
            'address'         => $this->address ? $this->address : (object)[],
            'country'         => new CountryResource($this->country),
        ];


        if($this->relationLoaded('billingAddress') && @$this->billingAddress ){
            $data['billing_address'] = new AddressCollection($this->billingAddress);
        }

        if($this->relationLoaded('latestDeliveryManMessage') && @$this->latestDeliveryManMessage ){
            $data['latest_conversation'] = new DeliverymanConversationResource(@$this->latestDeliveryManMessage);
        }

        
        if($this->relationLoaded('latestSellerMessage') && @$this->latestSellerMessage ){
            $data['latest_conversation'] = new SellerConversationResource(@$this->latestSellerMessage);
        }

        return $data;
        
    }
}
