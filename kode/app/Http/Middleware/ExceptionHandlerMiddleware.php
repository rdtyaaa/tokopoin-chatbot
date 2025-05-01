<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as FoundationResponse ;
class ExceptionHandlerMiddleware
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
       
        try {
            return $next($request);
        } catch (\Exception $e) {
            return api(['errors'=>[strip_tags($e->getMessage())]])
            ->fails(__('response.fail'));
        }
    }
}
