<?php

namespace Database\Seeders;

use App\Models\SmsGateway;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SMSgatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {



        DB::table('sms_gateways')->truncate();

        $gateways = [
            [
                'gateway_code'       => '101VON',
                'name'               => 'vonage',
                'credential'         => [
                    'api_key'    => '@@',
                    'api_secret' => '@@',
                    'sender_id'  => '@@',
                ],
            ],
            [
                'gateway_code'       => '103BIRD',
                'name'               => 'messagebird',
                'credential'         => [
                    'access_key'          => '@@',
                    'sender_id'           => '@@',

                ],
            ],
            [
                'gateway_code'       => '102TWI',
                'name'               => 'twilio',
                'credential'         => [
                    'account_sid'    => '@@',
                    'auth_token'     => '@@',
                    'from_number'    => '@@',
                ],
            ],
            [
                'gateway_code'       => '104INFO',
                'name'               => 'infobip',
                'credential'         => [
                    'sender_id'           => '@@',
                    'infobip_api_key'     => '@@',
                    'infobip_base_url'    => '@@',
                ],
            ],
        ];


        collect($gateways)
          ->each(function(array $gateway){
            SmsGateway::firstOrCreate(['gateway_code' => Arr::get($gateway,'gateway_code')],$gateway);
        });
    }
}
