<?php

namespace App\Http;

use App\Http\Middleware\ApiLang;
use App\Http\Middleware\CurrencyMiddleware;
use App\Http\Middleware\DeliveryManApiAuthMiddleware;
use App\Http\Middleware\DeliverymanKycMiddleware;
use App\Http\Middleware\DeliverymanModuleChecker;
use App\Http\Middleware\ExceptionHandlerMiddleware;
use App\Http\Middleware\GuestCheckout;
use App\Http\Middleware\MaintenanceModeMiddleware;
use App\Http\Middleware\ManualUpdateMiddleware;
use App\Http\Middleware\PermissionCheck;
use App\Http\Middleware\Sanitization;
use App\Http\Middleware\SecurityMiddleware;
use App\Http\Middleware\SellerModeCheck;
use App\Http\Middleware\SoftwareVerification;
use Illuminate\Foundation\Events\MaintenanceModeEnabled;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use App\Http\Middleware\DemoMode;
use App\Http\Middleware\SellerApiAuthMiddleware;
use App\Http\Middleware\SellerKycMiddleware;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
       \App\Http\Middleware\Cors::class,
        \App\Http\Middleware\TrustProxies::class,
       \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            SoftwareVerification::class,
            \App\Http\Middleware\LanguageMiddleware::class,
            ManualUpdateMiddleware::class

        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            SoftwareVerification::class,
            'throttle:refresh',
           
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            ManualUpdateMiddleware::class
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'api.lang' => ApiLang::class,
        'api.currency' => CurrencyMiddleware::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'sellerMode.status.check' => SellerModeCheck::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'seller' => \App\Http\Middleware\AuthenticateSeller::class,
        'seller.guest' => \App\Http\Middleware\RedirectIfSeller::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'admin' => \App\Http\Middleware\IfNotCheckAdmin::class,
        'admin.guest' => \App\Http\Middleware\IfCheckAdmin::class,
        'sellercheckstatus' => \App\Http\Middleware\SellerShopStatus::class,
        'checkUserStatus' => \App\Http\Middleware\CheckUserStatus::class,
        'checkSellerStatus' => \App\Http\Middleware\CheckSellerStatus::class,
        'sellerRegAllow' => \App\Http\Middleware\SellerRegistrationAllow::class,
        'prevent.back.history' =>\App\Http\Middleware\PreventBackHistoryMiddleWare::class,
        'permissions' => PermissionCheck::class,
        'sanitizer' => Sanitization::class,
        'dos.security'     => SecurityMiddleware::class,
        'maintenance.mode' => MaintenanceModeMiddleware::class,
        'guest.checkout'   => GuestCheckout::class,
        'demo'             => DemoMode::class,
        'handle.exception'             => ExceptionHandlerMiddleware::class,
        'seller.api.token'             => SellerApiAuthMiddleware::class,
        'deliveryman.api.token'=> DeliveryManApiAuthMiddleware::class,
        'deliveryman.module.checker'=> DeliverymanModuleChecker::class,
        'deliveryman.kyc'=> DeliverymanKycMiddleware::class,
        'software.verification' => SoftwareVerification::class,
        'seller.kyc' => SellerKycMiddleware::class,

    ];
}
