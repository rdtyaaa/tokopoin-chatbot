<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class GuestCheckout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) :Response
    {

        $user    = Auth::guard('api')->user();

        $apiUser = Auth::guard('web')->user();


        if(!guest_checkout() && !$user && !$apiUser){

            if ($request->expectsJson() || $request->isXmlHttpRequest() || $request->is('api/*') ) {
                return api([
                    'errors' => ['You Need To Login First']])->fails(__('response.fail'),HttpResponse::HTTP_FORBIDDEN ,404);
            }

            if ($request->isMethod('get')) {
                session()->put('request_route',Route::currentRouteName());
            }

            return redirect()->route('login')->with('error',translate('You Need To Login First'));
        }
        
        return $next($request);
    }
}
