<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateSeller
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
        if (Auth::guard('seller')->check()){
            return $next($request);
        }elseif(Auth::guard('seller')->user() && Auth::guard('seller')->user()->status == 2){
            Auth::guard('seller')->logout();
            return redirect()->route('seller.login')->with('error',translate('Your account is banned admin'));
        }else{
            return redirect()->route('seller.login');
        }
    }
}
