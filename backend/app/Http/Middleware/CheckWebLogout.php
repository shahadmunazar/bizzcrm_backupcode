<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CheckWebLogout
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('api/cancel-web-logout') || $request->is('cancel-web-logout')) {
            return $next($request);
        }

        $user = $request->user();
        if ($user) {
            if (Cache::has('web_logout_' . $user->id)) {
                return response()->json([
                    'error' => 'Unauthenticated',
                    'reason' => 'confirm_web_logout',
                    'message' => 'You have logged out from another device. What would you like to do?'
                ], 401)->header('X-Logout-Reason', 'confirm_web_logout');
            }
        }
        return $next($request);
    }
}
