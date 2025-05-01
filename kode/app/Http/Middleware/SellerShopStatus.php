<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SellerShopStatus
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
            $seller = auth()->guard('seller')->user();
            if (@$seller->sellerShop->status == 1) {
                return $next($request);
            } else {
                return redirect()->route('seller.dashboard')->with('error',"Your Store Is Not Approve Yet!!");
            }
        }
        abort(403);
    }
}
