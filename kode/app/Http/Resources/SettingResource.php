<?php

namespace App\Http\Resources;

use App\Enums\Settings\GlobalConfig;
use App\Enums\ShippingOption;
use App\Enums\StatusEnum;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class SettingResource extends JsonResource
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
        if(site_settings('app_settings')){
             $pages =  json_decode(site_settings('app_settings'),true);
             foreach($pages  as $key=>$data){
                $data['image']           = show_image(file_path()['onboarding_image']['path'].'/'.$data['image']);
                $onboarding_data[$key]   = $data;
             }
    
        }

       $country       = collect(GlobalConfig::COUNTRIES)->where('code',site_settings('country'))->first();


       $googleLogin     = site_settings('s_login_google_info') 
                                    ? json_decode(site_settings('s_login_google_info'),true) 
                                    : null;

       $facebookLogin  = site_settings('s_login_google_info') ? 
                                     json_decode(site_settings('s_login_facebook_info'),true)
                                     : null;

        $reward_point_by = null;

        if(site_settings('reward_point_by',0) == StatusEnum::true->status()){
            $reward_point_by    = 'order_amount_based';
        }elseif(site_settings('reward_point_by',0) == StatusEnum::false->status()){
            $reward_point_by  = 'product_based';
        }


        $rewardPointConfigurations = !is_array(site_settings('order_amount_based_reward_point',[])) 
                                                    ? json_decode(site_settings('order_amount_based_reward_point',[]),true) 
                                                    : [];




       return [
           'onboarding_pages'    => site_settings('app_settings') ? array_values($onboarding_data)  : (object)[],

           "site_name"           => site_settings('site_name'),
           'site_logo'           => show_image(file_path()['site_logo']['path'].'/'.site_settings('site_logo') ,file_path()['site_logo']['size']),
           'cash_on_delevary'    => site_settings('cash_on_delivery',StatusEnum::false->status()) == StatusEnum::true->status() 
                                                       ? true 
                                                       : false ,
            'digital_payment'   =>  site_settings('digital_payment') ==  StatusEnum::true->status() ? true : false ,
            'offline_payment'   =>  site_settings('offline_payment') ==  StatusEnum::true->status() ? true : false ,

            'minimum_order_amount_check' => site_settings('minimum_order_amount_check') ==  StatusEnum::true->status() ? true :false, 
            'minimum_order_amount' => (double)site_settings('minimum_order_amount',0) , 


           'address'             => site_settings('address'),
           'country'             => Arr::get(@$country ?? [] , 'name','Austria'),
           'copyright_text'      => site_settings('copyright_text'),
           "primary_color"       => site_settings("primary_color"),
           "font_color"          => site_settings("font_color"),
           "secondary_color"     => site_settings("secondary_color"),
           "email"               => site_settings('mail_from'),
           "phone"               => site_settings('phone'),
           "whatsapp_phone"      => site_settings('whats_app_number'),
           "whatsapp_order"      => site_settings('whatsapp_order',StatusEnum::false->status()) == StatusEnum::true->status()
                                                       ? true : false ,
           "wp_order_templates_key"  => array_keys(GlobalConfig::WP_ORDER),

            "wp_physical_order_message"      => site_settings('wp_order_message'),
            "wp_digital_order_message"       => site_settings('wp_digital_order_message'),
            
           "currency_position_is_left" => site_settings('currency_position',StatusEnum::true->status()) 
                                                          == StatusEnum::true->status()
                                                           ? true : false,

           "guest_checkout"      => guest_checkout(),
           "filter_min_range"    => api_short_amount( (double) site_settings('search_min',0),2),
           "filter_max_range"    => api_short_amount( (double) site_settings('search_max',0)),
           
           'shipping_configuration' => json_decode(site_settings('shipping_configuration')),
           'shipping_type_enum'     => array_keys(ShippingOption::toArray()),

           'strong_password'     => site_settings('strong_password') == StatusEnum::true->status() ? true :false,
           "google_oauth"        =>   ($googleLogin && is_array( $googleLogin) &&  $googleLogin['g_status'] == StatusEnum::true->status()) 
                                                 ?  $googleLogin
                                                 : (object) [],
           
            "facebook_oauth"      => ($facebookLogin && is_array( $facebookLogin) &&  $facebookLogin['f_status'] == StatusEnum::true->status()) 
                                                            ?  $facebookLogin
                                                            : (object) [],
                                                        

           'phone_otp'              => site_settings('phone_otp_login', StatusEnum::false->status()) == StatusEnum::true->status() ? true : false,
           'email_otp'              => site_settings('email_otp_login', StatusEnum::false->status()) == StatusEnum::true->status() ? true : false,
           'login_with_password'    => site_settings('login_with_password', StatusEnum::true->status()) == StatusEnum::true->status() ? true : false,
           'delivery_man_module'             => site_settings('delivery_man_module') == StatusEnum::true->status(),
           'chat_with_deliveryman'              => site_settings('chat_with_customer') == StatusEnum::true->status(),
            
           'min_deposit_amount'       => (int)(site_settings('customer_min_deposit_amount',0)),
           'max_deposit_amount'       => (int)(site_settings('customer_max_deposit_amount',0)),
           'wallet_system'            => site_settings('customer_wallet') == StatusEnum::true->status(),

           'reward_point_system'      => site_settings('club_point_system') == StatusEnum::true->status(),


           'reward_point_by'          => $reward_point_by,
           'customer_wallet_point_conversion_rate'  =>  (int) site_settings('customer_wallet_point_conversion_rate',1), 
           'default_order_based_reward_point'     => (int) site_settings('default_reward_point',0), //based on amount

           'reward_point_configurations' => $rewardPointConfigurations


       ];

    }


}
