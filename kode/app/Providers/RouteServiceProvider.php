<?php

namespace App\Providers;

use App\Enums\StatusEnum;
use App\Http\Middleware\SoftwareVerification;
use App\Models\GeneralSetting;
use App\Models\Visitor;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;
class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/user/dashboard';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {

        Route::middleware(['web'])->group(base_path('routes/install.php'));
        Route::middleware(['web'])->group(base_path('routes/web.php'));
        Route::middleware(['web'])->group(base_path('routes/auth.php'));
        Route::middleware(['web'])->group(base_path('routes/admin.php'));
        Route::middleware(['web'])->group(base_path('routes/seller.php'));
        Route::middleware(['api'])->prefix('api')->group(base_path('routes/api.php'));

        #SELLER  API V1
        Route::prefix('api/seller/v1/api')
                ->middleware('api')
                ->group(base_path('routes/api/seller/v1/api.php'));

        #DELIVERY MAN  API V1
                Route::prefix('api/delivery_man/v1/api')
                        ->middleware('api')
                        ->group(base_path('routes/api/delivery_man/v1/api.php'));


        try {
            if(DB::connection()->getDatabaseName()){
                $this->configureRateLimiting();
            }
        } catch (\Throwable $th) {
          
        }
    }

   

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {

    
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });

        try {
    
            RateLimiter::for('refresh', function(Request $request){
                if(Schema::hasTable('general_settings')){

            
                    $security = site_settings('security_settings',null) 
                    ?  json_decode( site_settings('security_settings',null)) 
                    : null ;

                    if(@$security->dos_prevent == StatusEnum::true->status()){

                        $key          = 'dos.'.get_real_ip(); 
                        $maxAttempt   = (int)  @$security->dos_attempts?? 10;
                        $sec          = (int)  @$security->dos_attempts_in_second?? 10;
                        if(RateLimiter::tooManyAttempts($key,$maxAttempt)){

                            $ipinfo         = get_ip_info();
                            $ipAddress      = get_real_ip();
                            $ip             = Visitor::insertOrupdtae($ipAddress,$ipinfo);
             
                            if(@$security->dos_security == 'block_ip'){
                                $ip->is_blocked = StatusEnum::true->status();
                                $ip->save();
                            }
                            else{

                                session()->put("security_captcha",true);
                            }
                            
                        }
                        else{
                            RateLimiter::hit($key,$sec);
                        }
                    }
                }
            });

        } catch (\Throwable $th) {
         
        }
    }
}
