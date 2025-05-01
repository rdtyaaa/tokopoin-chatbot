<?php

namespace App\Http\Middleware;

use App\Enums\Settings\TokenKey;
use App\Enums\Status;
use App\Enums\StatusEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as FoundationResponse ;
class SellerApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) :FoundationResponse
    {

        
        $seller = auth()->guard('seller:api')->user();

        switch (true) {
            case $seller && $seller->status == 2:
                $seller->tokens()->delete();
                return api(['errors' => ['Your account has been deactivated by the system admin']])->fails(__('response.error'));
            case $seller && $seller->tokenCan('role:' . TokenKey::SELLER_TOKEN_ABILITIES->value):
                return $next($request);
            default:
         
                return api(['errors' => ['Invalid token']])->fails(__('response.fail'));
        }

    }
}
