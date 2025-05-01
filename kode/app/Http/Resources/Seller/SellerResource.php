<?php

namespace App\Http\Resources\Seller;

use App\Enums\StatusEnum;
use App\Http\Resources\SellerConversationResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerResource extends JsonResource
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
            'id'          => $this->id,
            "fcm_token"       => $this->fcm_token,
            'name'        => $this->name,
            'username'    => $this->username,
            'email'       => $this->email,
            'rating'      => (int) $this->rating ?? 0 ,
            'phone'       => $this->phone,
            'is_kyc_verified'       => $this->kyc_status == StatusEnum::true->status() ? true : false  ,
            'address'     => $this->address,
            'balance'     => (double)api_short_amount($this->balance),
            'is_banned'   => $this->status == 2 ? true : false,
            'image'       => show_image(file_path()['profile']['seller']['path']."/".$this->image,file_path()['profile']['seller']['size']),

            'shop'        => new SellerShopResource(@$this->sellerShop),
        ];


        if($this->relationLoaded('latestConversation') && @$this->latestConversation ){
            $data['latest_conversation'] = new SellerConversationResource(@$this->latestConversation);
        }

        return $data;


    }
}
