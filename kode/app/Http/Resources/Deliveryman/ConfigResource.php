<?php

namespace App\Http\Resources\Deliveryman;

use App\Enums\StatusEnum;
use App\Models\Order;
use App\Models\SupportTicket;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

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


        $onboarding_data =  [];
        if(site_settings('deliveryman_app_settings')){
             $pages =  json_decode(site_settings('deliveryman_app_settings'),true);
             foreach($pages  as $key=>$data){
                $data['image']           = show_image(file_path()['onboarding_image']['path'].'/'.$data['image']);
                $onboarding_data[$key]   = $data;
             }
    
        }


        $rewardPointConfigurations = !is_array(site_settings('deliveryman_reward_point_configuration',[])) 
                                                    ? json_decode(site_settings('deliveryman_reward_point_configuration',[]),true) 
                                                    : [];



        $rewardAmountConfiguration = !is_array(site_settings('deliveryman_reward_amount_configuration',[])) 
                                                    ? json_decode(site_settings('deliveryman_reward_amount_configuration',[]),true) 
                                                    : [];

        return [

            'map_api_key'            => site_settings('gmap_client_key'),
            'delevary_status'        => Order::delevaryStatus(),
            'ticket_priority'        => SupportTicket::priority(),
            'digit_after_decimal'    => site_settings('digit_after_decimal',0),

            "currency_position_is_left" => site_settings('currency_position',StatusEnum::true->status()) 
                                                    == StatusEnum::true->status()
                                                    ? true : false,

            'onboarding_pages'               => site_settings('app_settings') ? array_values($onboarding_data)  : (object)[],
            
            'deliveryman_kyc_verification'    => site_settings('deliveryman_kyc_verification') == StatusEnum::true->status(),
            'deliveryman_registration'        => site_settings('deliveryman_registration') == StatusEnum::true->status(),
            'delivery_man_module'             => site_settings('delivery_man_module') == StatusEnum::true->status(),
            'chat_with_customer'              => site_settings('chat_with_customer') == StatusEnum::true->status(),
            'chat_with_deliveryman'           => site_settings('chat_with_deliveryman') == StatusEnum::true->status(),
            'order_assign'                    => site_settings('order_assign') == StatusEnum::true->status(),
            'decline_assign_request'          => site_settings('deliveryman_assign_cancel') == StatusEnum::true->status(),
            'referral_system'                 => site_settings('deliveryman_referral_system') == StatusEnum::true->status(),
            'review_request'                  => site_settings('review_request') == StatusEnum::true->status(),
            'deliveryman_club_point_system'   => site_settings('deliveryman_club_point_system') == StatusEnum::true->status(),
            'deliveryman_default_reward_point'         => (int) site_settings('deliveryman_default_reward_point',0),
            'deliveryman_reward_point_expired_after'   => (int) site_settings('deliveryman_reward_point_expired_after',0),
            'reward_point_configurations'              => $rewardPointConfigurations,
            'reward_amount_configurations'             => $rewardAmountConfiguration,
            'deliveryman_referral_reward_point'        => site_settings('deliveryman_referral_reward_point',0),
            "order_verification" => site_settings('order_verification') == StatusEnum::true->status(),
            
             'default_order_status' =>  Arr::get(array_flip(Order::delevaryStatus()),site_settings('order_status_after_payment',Order::PLACED))  
            
        ];
    }
}
