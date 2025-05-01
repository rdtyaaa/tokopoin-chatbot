<?php

namespace Database\Seeders;

use App\Enums\Settings\GlobalConfig;
use App\Models\EmailTemplates;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use App\Enums\Settings\CacheKey;
use App\Enums\Status;
use App\Enums\StatusEnum;
use App\Models\PaymentMethod;
use Carbon\Carbon;

class UpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
  
        try {

            /** V2.1 */


            $migrations =  [
                'database/migrations/2024_09_03_173736_create_delivery_man_conversations_table.php',
                'database/migrations/2024_09_03_161000_create_delivery_man_orders_table.php',
            ];

            foreach ($migrations as $migration) {
                Artisan::call('migrate',
                    array(
                        '--path' => $migration,
                        '--force' => true));
            }

            //#WEBXPAY
            $method =  PaymentMethod::firstOrCreate(['unique_code' => 'WEBXPAY109']);
            $method->name           =  'Webxpay';
            $method->currency_id    =  1;
            $method->percent_charge =  1;
            $method->rate           =  1;
            $method->payment_parameter   =  [
                'public_key'       => '@@@@',
                'secret_key'       => '@@@@',
                'api_username'     => '@@@',
                'api_password'     => '@@@',
                'callback_url'     => '@@@',
                'is_sandbox'       => 0,
            ];
            $method->status = PaymentMethod::ACTIVE;
            $method->type   = PaymentMethod::AUTOMATIC;
            $method->save();


            $queries = [
                "ALTER TABLE `delivery_men` ADD `is_kyc_verified` TINYINT NULL AFTER `kyc_data`",
                "ALTER TABLE `delivery_men` ADD `is_online` TINYINT NULL AFTER `status`",
                "ALTER TABLE `delivery_men` ADD `last_login_time` TIMESTAMP NULL AFTER `kyc_data`",
                "ALTER TABLE `delivery_men` ADD `enable_push_notification` TINYINT NULL AFTER `balance`",
                "ALTER TABLE `kyc_logs` ADD `deliveryman_id` BIGINT UNSIGNED NULL AFTER `seller_id`",
                "ALTER TABLE `deliveryman_earning_logs` ADD `assigned_id` BIGINT UNSIGNED NULL AFTER `id`",
                "ALTER TABLE `reward_point_logs` ADD `delivery_man_id` BIGINT UNSIGNED NULL AFTER `user_id`",
                "ALTER TABLE `delivery_men` ADD `point` VARCHAR(191) NOT NULL DEFAULT '0' AFTER `is_online`",
                "ALTER TABLE `reward_point_logs` ADD `post_point` MEDIUMINT NOT NULL DEFAULT '0' AFTER `category_id`",
                "ALTER TABLE `delivery_men` ADD `referral_code` TEXT NULL AFTER `country_id`",
                "ALTER TABLE `delivery_men` ADD `referral_id` BIGINT UNSIGNED NULL AFTER `id`",
            ];
    
            foreach ($queries as $query) {
                DB::statement($query);
            }


        

        } catch (\Throwable $th) {
        
        }
       
       
      
    }
}
