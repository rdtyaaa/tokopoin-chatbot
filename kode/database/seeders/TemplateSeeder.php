<?php

namespace Database\Seeders;

use App\Models\EmailTemplates;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $templates =   [ 
                            'otp_verification' =>  [
                                
                                "name"      => 'OTP Verificaton',
                                "subject"   => "OTP Verificaton",
                                "body"      => "Your Otp {{otp_code}} and request time {{time}}",
                                "sms_body"  => "Your Otp {{otp_code}} and request time {{time}}",
                                "codes" => ([
                                    'otp_code'         => "OTP (One time password)",
                                    'time'             => "Time",
                                ])
                            ],

                      ];

        collect($templates)
            ->except(EmailTemplates::pluck('slug')->toArray())
            ->each(function(array $template , string $slug){
                EmailTemplates::firstOrCreate(['slug' => $slug],$template);
        });
        
    }
}
