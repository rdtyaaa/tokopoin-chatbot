<?php

namespace App\Http\Middleware;

use App\Enums\Settings\CacheKey;
use App\Models\Currency;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
 
class CurrencyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
 
        try {


            $cachedCurrency = Cache::get(CacheKey::API_CURRENCY->value);

            $currency =  ($request->hasHeader('currency-uid') && $request->header('currency-uid') )
                                ? Currency::where('uid',$request->header('currency-uid'))
                                                ->first()
                                : default_currency();

    
            $cachedCurrency = Cache::rememberForever(CacheKey::API_CURRENCY->value, function () use($currency) {
                         return $currency ;
            });

            if($cachedCurrency->symbol !=   $currency->symbol){
                Cache::forget(CacheKey::API_CURRENCY->value);
                $cachedCurrency = Cache::rememberForever(CacheKey::API_CURRENCY->value, function () use($currency) {
                    return $currency ;
                });
            }


        } catch (\Throwable $th) {
           
        }
    
        return $next($request);
    }
}

