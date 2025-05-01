<?php

namespace App\Http\Resources;

use App\Enums\ProductStatus;
use App\Enums\StatusEnum;
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

 

        return [
            "id"              => $this->id,
            "name"            => @$this->sellerShop->name,
            'logo'            => show_image(file_path()['shop_logo']['path'].'/'.@$this->sellerShop->shop_logo),
            'banner'          => show_image(file_path()['shop_first_image']['path'].'/'.@$this->sellerShop->shop_first_image),
            'created_at'      => diff_for_humans($this->created_at),
            'email'           => ($this->email),
            'phone'           => ($this->phone),
            'rating'          => $this->rating ? $this->rating : 0,
            'total_products'  => $this->product_count,
            'short_details'   => $this->sellerShop->short_details,
            'whatsapp_number' => @$this->sellerShop->whatsapp_number,
            'is_whatsapp_order_active'  => 
                                            @$this->sellerShop->whatsapp_order == StatusEnum::true->status() 
                                               ? true : false,
            'total_followers' => $this->follow->count(),
            'link'            => route('seller.store.visit',[make_slug($this->sellerShop->name), $this->id]),
            'followers'       => (array)$this->follow->pluck('following_id')->toArray()
     ];
    }
}
