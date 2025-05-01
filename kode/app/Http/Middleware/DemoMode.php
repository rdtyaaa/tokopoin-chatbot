<?php

namespace App\Http\Middleware;

use App\Enums\StatusEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class DemoMode
{
   public function handle(Request $request, Closure $next): Response
    {
        
        try {
           $demoRoute = [
                'admin.general.setting.store',
                'admin.social.login.update',
                'admin.general.app.setting.update',
                'admin.plugin.update',
                'admin.general.ai.configuration.update',
                'admin.seller.mode',
                'admin.debug.mode',
                'admin.security.dos.update',
                'admin.system.update',
                'admin.seo.update',
                'admin.gateway.payment.update',
                'admin.mail.update',
                'admin.global.template.update',
                'admin.mail.send.method',
                'admin.mail.templates.update',
                'admin.subscriber.send.mail.submit',
                'admin.contact.send.mail',
                'admin.frontend.section.update',
                'admin.home.category.update',
          ];
            if(strtolower(env('APP_MODE')) == 'demo'){
                if( in_array(Route::currentRouteName(),$demoRoute) || request()->routeIs('*delete*') || request()->routeIs('*destroy*') ||  request()->routeIs("admin.addon.store") || request()->routeIs('*status.update*') || request()->routeIs('*update.status*') ){
                    if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                        return response()->json(response_status('This Function Is Not Available For Website Demo Mode','error'), 403);
                    }
                    return back()->with('error','This Function Is Not Available For Website Demo Mode');
                }
            }
            return $next($request);
        } catch (\Throwable $th) {
        }
   
    }
}
