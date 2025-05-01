<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Config;
class SocialLoginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            $google    = json_decode(site_settings('s_login_google_info'),true);
            $facebook  = json_decode(site_settings('s_login_facebook_info'),true);
         
            if(is_array($google)  &&  is_array($facebook)){
             
                if(@$google['g_status'] == '1'){
                    $googleConfig = array(
                        'client_id'     => $google['g_client_id'],
                        'client_secret' => $google['g_client_secret'],
                        'redirect' => url('auth/google/callback'),
                    );
                    Config::set('services.google', $googleConfig);
                }
                
                if(@$facebook['f_status'] == '1'){
                    $facebookConfig = array(
                        'client_id'     => $facebook['f_client_id'],
                        'client_secret' =>  $facebook['f_client_secret'],
                        'redirect'      => url('auth/facebook/callback'),
                    );
                    Config::set('services.facebook', $facebookConfig);
                }
                
            }
        }catch(\Exception $exception){

        }
    }
}
