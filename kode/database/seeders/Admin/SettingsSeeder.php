<?php

namespace Database\Seeders\Admin;

use App\Models\GeneralSetting;
use App\Models\PluginSetting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GeneralSetting::where('id',1)->delete();


        GeneralSetting::create([
            "site_name"=>'demo_site',
            "cod"=>'Active',
            "copyright_text" =>'test',
            "seller_mode" =>'DeActive',
            "pagination_number" =>10,
            "primary_color" =>"2C3539",
            "secondary_color" =>"FF4500",
            "font_color"=>"fff",
            "currency_name"=>"BDT",
            "currency_symbol"=>"৳",
            "sms_gateway_id"=>4,
            "email_gateway_id"=>1,
            "commission" => 00,
            "mail_from"=>'demo@gmail.com',
            "phone"=>'009999999',
            "email_template"=>'hi {{name}}, {{message}}',
            "email_notification"=>1,
            "sms_notification"=>2,
            "s_login_google_info"=>'{"g_client_id":"97280043298-kli3ras40qps42chksb3sfcsh9376qdo.apps.googleusercontent.com","s_login_facebook_info":"{"f_client_id":"708633780578618","f_client_secret":"6e916829cd34aa97ecaa5fb786fc6ce7","f_status":"1"}',
            "seller_reg_allow" => 1
        ]);
    }
}
