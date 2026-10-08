<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'check.subscription' => \App\Http\Middleware\CheckSubscription::class,
            'check.roles.box'    => \App\Http\Middleware\CheckRolesAuth::class,
            'permission.box'     => \App\Http\Middleware\CheckBoxPermission::class,
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\CheckApiLogout::class,
        ]);
        $middleware->api(append: [
            \App\Http\Middleware\CheckWebLogout::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'api/webhook/*',
            'api/callerdesk',
            'webhook/facebook',
            'webhook/whatsapp',
        ]);
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'is_logged_out' => true,
                    'message' => 'Your session expired or you were logged out because your account was accessed from another device.'
                ], 401);
            }
        });

        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User does not have the right permissions.',
                    'error'   => $e->getMessage()
                ], 403);
            }
        });
    })->create();
