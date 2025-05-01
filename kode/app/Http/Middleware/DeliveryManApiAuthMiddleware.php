<?php

namespace App\Http\Middleware;

use App\Enums\Settings\TokenKey;
use App\Enums\StatusEnum;
use App\Models\DeliveryMan;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as FoundationResponse ;

class DeliveryManApiAuthMiddleware
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
        $deliveryman = auth()->guard('delivery_man:api')->user();

        switch (true) {
            case $deliveryman && $deliveryman->status == StatusEnum::false->status():
                $deliveryman->tokens()->delete();
                return api(['errors' => ['Your account has been deactivated by the system admin']])->fails(__('response.error'));
            case $deliveryman && $deliveryman->tokenCan('role:'.TokenKey::DELIVERY_MAN_TOKEN_ABILITIES->value):
                $this->updateLastLoginTime($deliveryman);
                return $next($request);
            default:
         
                return api(['errors' => ['Invalid token']])->fails(__('response.fail'));
        }
    }


    public function updateLastLoginTime(DeliveryMan $deliveryman) : void{
        $currentTime = now();
        if (is_null($deliveryman->last_login_time) || $deliveryman->last_login_time->diffInMinutes($currentTime) >= 2) {
            
            $deliveryman->last_login_time = $currentTime;
            $deliveryman->save();
        }
    }
}
