<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MiddlewareServiceProvider extends ServiceProvider
{
    public function register()
    {
        // You can leave this empty if not registering anything here
    }

    public function boot()
    {
        // Register your middleware aliases here
        app('router')->aliasMiddleware('check.roles.box', \App\Http\Middleware\CheckRolesAuth::class);

        // Add other middleware aliases if needed
        // app('router')->aliasMiddleware('another.middleware', \App\Http\Middleware\AnotherMiddleware::class);
    }
}
