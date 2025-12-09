<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

// BugSnag can increase the PHP memory limit when app runs out of memory to ensure events can be delivered.
(new \Bugsnag\BugsnagLaravel\OomBootstrapper)->bootstrap();

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            Route::middleware('web')
                ->prefix('api/v1')
                ->group(base_path('routes/auth.php'));

            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            if (! app()->environment('production')) {
                Route::middleware('web')
                    ->group(base_path('routes/dev_web.php'));

                Route::middleware('api')
                    ->prefix('api/v1')
                    ->group(base_path('routes/dev_api.php'));
            }
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'api/*/public/*',
        ]);
        $middleware->api([
            \App\Http\Middleware\LocalizationHeader::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'signed.invite' => \App\Http\Middleware\ValidateSignature::class,
            'log.incoming' => \App\Http\Middleware\LogIncomingRequest::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
