<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\InstallerManager;
use Illuminate\Http\Response as HttpResponse;
class SoftwareVerification
{
    use InstallerManager;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
     

   
   
        if(!$this->is_installed()){

            if ($request->expectsJson() || $request->isXmlHttpRequest() || $request->is('api/*') ) {
                return api([
                    'errors' => ['Your software is not installed yet']])->fails(__('response.fail'),HttpResponse::HTTP_FORBIDDEN ,2000000);
   
            }
            return redirect()->route('install.init');
        }
        return $next($request);
    }
}
