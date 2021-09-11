<?php /** @noinspection ALL */

namespace App\Http;

use App\Http\Middleware\BlogVerifier;
use App\Http\Middleware\DorcasAuthViaUrlToken;
use App\Http\Middleware\CheckActiveSubscriptionPlan;
use App\Http\Middleware\PaidPlanGate;
use App\Http\Middleware\ProfessionalModeAccessGate;
use App\Http\Middleware\ProtectUiConfigurationAccess;
use App\Http\Middleware\RequireRole;
use App\Http\Middleware\ResolveCustomSubdomain;
use App\Http\Middleware\SetPageMessages;
use App\Http\Middleware\StoreVerifier;
use App\Http\Middleware\VendorsModeAccessGate;
use App\Http\Middleware\ViewModeMiddleware;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The priority-sorted list of middleware.
     *
     * Forces the listed middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        DorcasAuthViaUrlToken::class,
        \Illuminate\Auth\Middleware\Authenticate::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
        ResolveCustomSubdomain::class,
        ProtectUiConfigurationAccess::class,
        ViewModeMiddleware::class,
        SetPageMessages::class
    ];
    
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\DorcasProxyHandler::class,
            DorcasAuthViaUrlToken::class,
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            CheckActiveSubscriptionPlan::class,
            ResolveCustomSubdomain::class,
            ProtectUiConfigurationAccess::class,
            ViewModeMiddleware::class,
            SetPageMessages::class
            
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'pay_gate' => PaidPlanGate::class,
        'professional_only' => ProfessionalModeAccessGate::class,
        'vendor_only' => VendorsModeAccessGate::class,
        'blog_verifier' => BlogVerifier::class,
        'web_store' => StoreVerifier::class,
        'require_role' => RequireRole::class,
        'edition_business_only' =>  \App\Http\Middleware\DorcasBusinessGateOnly::class,
        'edition_multitenant_only' =>  \App\Http\Middleware\DorcasMultiTenantGateOnly::class,
        'edition_commercial_only' =>  \App\Http\Middleware\DorcasCommercialGateOnly::class,
    ];
}
