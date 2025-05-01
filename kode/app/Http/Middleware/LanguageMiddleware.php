<?php

namespace App\Http\Middleware;

use App\Models\Currency;
use Closure;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

use Illuminate\Support\Facades\Cache;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle($request, Closure $next)
    {
        try {
            if(session()->has('locale')){
                $locale = session()->get('locale');
            }
            else{
                $locale = (Language::where('is_default',"1")->first())->code;
            }
            App::setLocale($locale);
            session()->put('locale', $locale);
    
            $this->setCurrency();
        } catch (\Throwable $th) {
           
        }
        return $next($request);
    }

    public function setCurrency(){

      
        if(!session()->has('web_currency')){
            $currency = (Currency::default()->first());
            session()->put('web_currency',$currency);
        }
    }

   
}
