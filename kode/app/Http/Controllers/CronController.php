<?php

namespace App\Http\Controllers;

use App\Enums\RewardPointStatus;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use App\Models\PlanSubscription;
use App\Models\Product;
use App\Models\RewardPointLog;
use App\Models\Setting;
use Carbon\Carbon;
use DateTime;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
class CronController extends Controller
{
    public function handle()
    {

        $products = Product::where('product_type','102')->whereNull('seller_id')->where('status','0')->cursor();
        foreach( $products  as $product){

            $current_date = new DateTime();
            $created_date = new DateTime($product->created_at);
            $interval = $created_date->diff($current_date);

            if(site_settings('status_expiry') && $interval->days > (int) site_settings('status_expiry') ){
                $product->status =  1;
                $product->save();
            }
        }

        $subscriptions = PlanSubscription::where('status', PlanSubscription::RUNNING)->get();
        foreach($subscriptions as $subscription){
            $expiredTime = $subscription->expired_date->addDays($subscription->plan->duration); 
            $now = Carbon::now()->toDateTimeString();
            if($now > $expiredTime){
                $subscription->status = PlanSubscription::EXPIRED;
                $subscription->save();
            }
        }

        $rewardLogs = RewardPointLog::pending()->get();


        foreach($rewardLogs as $log){

            $now = Carbon::now()->toDateTimeString();
            if($log->expired_at && $now > $log->expired_at){
                $log->status = RewardPointStatus::EXPIRED->value;
                $log->save();
            }
        }



        Setting::updateOrInsert(
            ['key'    => 'last_cron_run'],
            ['value'  => Carbon::now()]
        );
    }



      /**
     *Import demo database
     *
     *@return void
     */
    public function importDemoDB() : void {

        if(is_demo()){
            Artisan::call('db:wipe', ['--force' => true]);
            $sqlFile = resource_path('database/cartuser.sql');
            DB::unprepared(file_get_contents($sqlFile));
            optimize_clear();
        }
    }
}
