<?php

namespace App\Http\Middleware;

use App\Enums\StatusEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as FoundationResponse ;
use Illuminate\Http\Response;
class DeliverymanKycMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Symfony\Component\HttpFoundation\Response as FoundationResponse
     */
    public function handle(Request $request, Closure $next) :FoundationResponse
    {


        if((site_settings('deliveryman_kyc_verification') == StatusEnum::true->status())){

            $deliveryman = auth()->guard('delivery_man:api')->user();

            if(!$deliveryman->is_kyc_verified ||  $deliveryman->is_kyc_verified == StatusEnum::false->status()){
                return api([
                    'errors' => [translate('Please apply for KYC verification')]])->fails(__('response.fail'),Response::HTTP_FORBIDDEN ,6000000);

            }
        }

        return $next($request);
    }
}
