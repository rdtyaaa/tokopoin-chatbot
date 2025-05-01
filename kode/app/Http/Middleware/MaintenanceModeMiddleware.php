<?php

namespace App\Http\Middleware;

use App\Enums\StatusEnum;
use App\Models\GeneralSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as FoundationResponse ;

use function response;

class MaintenanceModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next) :FoundationResponse
    {
        try {

            if(site_settings('maintenance_mode') ==  StatusEnum::true->status()){
                if ($request->expectsJson() || $request->isXmlHttpRequest() || $request->is('api/*') ) {
                    return api([
                        'errors' => ['We are sorry for the inconvenience, but we are performing some essential maintenance on our website. Please check back soon']])->fails(__('response.fail'),Response::HTTP_FORBIDDEN ,1000000);
       
                }
                return redirect()->route('maintenance.mode');
            }
            return $next($request);

        } catch (\Exception $ex) {
            
        }
        return $next($request);
    }
}
