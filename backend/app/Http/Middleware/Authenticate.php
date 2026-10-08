<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // If AJAX request, return JSON response with redirect
        if ($request->expectsJson() || $request->ajax()) {
            return null; // Let the handle method handle it
        }

        // Otherwise redirect to login
        return route('login');
    }

    /**
     * Handle an unauthenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function unauthenticated($request, array $guards)
    {
        // If AJAX request, throw exception with JSON response
        if ($request->expectsJson() || $request->ajax()) {
            throw new \Illuminate\Auth\AuthenticationException(
                'Your session has expired. Please login again.',
                $guards,
                $this->redirectTo($request)
            );
        }

        // Otherwise, parent will handle redirect using redirectTo method
        parent::unauthenticated($request, $guards);
    }
}
