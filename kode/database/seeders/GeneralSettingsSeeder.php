<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GeneralSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $general = general_setting();

       if(is_null($general->otp_configuration)){
         $general->otp_configuration = [
            'login_with_password' => 1,
            'email_otp'           => 0,
            'phone_otp'           => 0,
         ];
         $general->save();
       }
    }
}
