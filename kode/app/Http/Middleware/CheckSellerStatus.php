<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSellerStatus
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
       
         if (Auth::guard('seller')->check()) {
            $seller = Auth::guard('seller')->user();
            if ($seller->status == 1) {
                return $next($request);
            }
                Auth::guard('seller')->logout();
                return redirect()->route('seller.login')->with('error', 'Your account is banned by admin');
            
        }
    }
}
