<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
class ApiLang
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

            $locale = $request->hasHeader('api-lang') 
                         ? $request->header('api-lang')
                         : @(Language::default()->first())->code ?? 'en';
         
            App::setLocale($locale);
        } catch (\Throwable $th) {
     
        }
        return $next($request);
    }
}
