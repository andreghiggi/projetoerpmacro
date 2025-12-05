<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

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
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
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
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            // \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'valid' => \App\Http\Middleware\Valid::class,
        'validNfce' => \App\Http\Middleware\ValidNfce::class,
        'authh' => \App\Http\Middleware\Authh::class,
        'validaEmpresa' => \App\Http\Middleware\ValidaEmpresa::class,
        'verificaEmpresa' => \App\Http\Middleware\VerificaEmpresa::class,
        'validaPlano' => \App\Http\Middleware\ValidaPlano::class,
        'validaNFe' => \App\Http\Middleware\ValidaNFe::class,
        'validaNFCe' => \App\Http\Middleware\ValidaNFCe::class,
        'validaCTe' => \App\Http\Middleware\ValidaCTe::class,
        'validaMDFe' => \App\Http\Middleware\ValidaMDFe::class,
        'verificaMaster' => \App\Http\Middleware\VerificaMaster::class,
        'authCardapio' => \App\Http\Middleware\AuthCardapio::class,
        'authDelivery' => \App\Http\Middleware\AuthDelivery::class,
        'validaCashBack' => \App\Http\Middleware\ValidaCashBack::class,
        'validaEcommerce' => \App\Http\Middleware\ValidaEcommerce::class,
        'validaDelivery' => \App\Http\Middleware\ValidaDelivery::class,
        'validaApiToken' => \App\Http\Middleware\ValidaApiToken::class,
        'validaApiTokenSuperAdmin' => \App\Http\Middleware\ValidaApiTokenSuperAdmin::class,
        'validaSuporte' => \App\Http\Middleware\ValidaSuporte::class,
        'validaContrato' => \App\Http\Middleware\ValidaContrato::class,
        'validaCardapio' => \App\Http\Middleware\ValidaCardapio::class,
        'authApp' => \App\Http\Middleware\AuthApp::class,
        'impersonate' => \App\Http\Middleware\Impersonate::class,
        'impersonateContador' => \App\Http\Middleware\ImpersonateContador::class,
    ];

    protected $routeMiddleware = [
        'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
    ];
}
