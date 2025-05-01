<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use App\Models\MailConfiguration;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
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
            $mail = MailConfiguration::where('name', 'SMTP')->where('status', 1)->first();
            if($mail){
                $config = array(
                    'driver'     => @$mail->driver_information->driver,
                    'host'       => @$mail->driver_information->host,
                    'port'       => @$mail->driver_information->port,
                    'from'       => array('address' => @$mail->driver_information->from->address, 'name' => @$mail->driver_information->from->name),
                    'encryption' => @$mail->driver_information->encryption,
                    'username'   => @$mail->driver_information->username,
                    'password'   => @$mail->driver_information->password,
                    'sendmail'   => '/usr/sbin/sendmail -bs',
                    'pretend'    => false,
                );
                Config::set('mail', $config);
            }
        }catch (\Exception $ex) {

        }
    }
}
