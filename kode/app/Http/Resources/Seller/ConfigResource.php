<?php

namespace App\Http\Resources\Seller;

use App\Enums\StatusEnum;
use App\Models\Order;
use App\Models\SupportTicket;
use Illuminate\Http\Resources\Json\JsonResource;

class ConfigResource extends JsonResource
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
            'registration'                   => site_settings('seller_registration') == StatusEnum::true->status()
                                                            ? true 
                                                            : false ,
            'delevary_status'                =>  Order::delevaryStatus(),
            'ticket_priority'                =>  SupportTicket::priority(),
            
            'seller_min_deposit_amount'       => (int)(site_settings('seller_min_deposit_amount',0)),
            'seller_max_deposit_amount'       => (int)(site_settings('seller_max_deposit_amount',0)),


            'seller_product_status_update_permission'        => (site_settings('seller_product_status_update_permission') == StatusEnum::true->status()),
            'seller_kyc_verification'        => (site_settings('seller_kyc_verification') == StatusEnum::true->status()),
            'kyc_config'                     => json_decode(site_settings('seller_kyc_settings')),
            'order_delivery_permission'                => site_settings('seller_order_delivery_permission') == StatusEnum::true->status(),

            "currency_position_is_left"      => site_settings('currency_position',StatusEnum::true->status()) 
                                                    == StatusEnum::true->status()
                                                       ? true : false,


            'delivery_man_module'             => site_settings('delivery_man_module') == StatusEnum::true->status(),
            'chat_with_customer'              => site_settings('chat_with_customer') == StatusEnum::true->status(),
        ];
    }
}
