<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Traits\InstallerManager;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Hash;

class Handler extends ExceptionHandler
{

    use InstallerManager;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
    
        $this->reportable(function (\Exception $e) {
            if(request()->expectsJson() || request()->isXmlHttpRequest() || request()->is('api/*') ) {
                 abort(500,strip_tags($e->getMessage()));
            }
        });
    }




        /**
         * Render an exception into an HTTP response.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Throwable  $exception
         * @return \Symfony\Component\HttpFoundation\Response
         *
         * @throws \Throwable
         */
        public function render($request, Throwable $exception)
        {

            // if( $exception && (request()->expectsJson() || request()->isXmlHttpRequest() || request()->is('api/*'))){
            //     if(!$this->is_installed()){
            //         return api([
            //             'errors' => ['Your software is not installed yet']])->fails(__('response.fail'),HttpResponse::HTTP_FORBIDDEN ,2000000); 
            //     }
            //     return api(['errors'=>[strip_tags($exception->getMessage())]])
            //     ->fails(__('response.fail'));
            // }
        
            return parent::render($request, $exception);

        }
}
