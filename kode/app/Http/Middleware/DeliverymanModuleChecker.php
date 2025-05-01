<?php

namespace App\Http\Middleware;

use App\Enums\StatusEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as FoundationResponse ;
use Illuminate\Http\Response;
class DeliverymanModuleChecker
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

        if(!site_settings('delivery_man_module') == StatusEnum::true->status()) {
            return api([
                'errors' => [translate('Deliveryman module is currently inactive')]])->fails(__('response.fail'),Response::HTTP_FORBIDDEN ,6000);
        }
        return $next($request);
    }
}
