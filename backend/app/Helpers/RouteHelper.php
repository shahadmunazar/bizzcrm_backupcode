<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/**
 * Override the default `route()` helper to inject box_slug
 */
if (! function_exists('box_route')) {
    function box_route($name, $parameters = [], $absolute = true)
    {
        // Try to get slug from parameters, then session, then current route
        $boxSlug = $parameters['box_slug'] ?? session('box_slug') ?? request()->route('box_slug');

        // If still not found, check authenticated user
        if (! $boxSlug && Auth::check()) {
            $user = Auth::user();
            $boxSlug = $user->box?->slug ?? $user->boxes()->first()?->slug;
        }

        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }

        if ($boxSlug && !isset($parameters['box_slug'])) {
            $parameters = array_merge(['box_slug' => $boxSlug], $parameters);
        }

        // Final check: if the route name implies it's a box route but we have no slug,
        // we might want to avoid the crash, but Laravel's route() will throw.
        // We'll let it throw so the developer knows the context is missing, 
        // but now we've exhausted all lookup options.
        
        return route($name, $parameters, $absolute);
    }
}

/**
 * Override redirect()->route to use the overridden route()
 */
if (! function_exists('redirect_route')) {
    function redirect_route($name, $parameters = [], $status = 302, $headers = [])
    {
        return redirect(box_route($name, $parameters), $status, $headers);
    }
}
