<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CheckApiLogout
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('cancel-api-logout') || $request->is('*/cancel-api-logout')) {
            return $next($request);
        }

        if (Auth::check()) {
            if (Cache::has('api_logout_' . Auth::id())) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'error' => 'Unauthenticated', 
                        'reason' => 'confirm_api_logout',
                        'message' => 'You have logged out from another device. What would you like to do?'
                    ], 401)->header('X-Logout-Reason', 'confirm_api_logout');
                }

                // If not AJAX, we just flash the reason so the page can show the modal
                session()->flash('confirm_api_logout', true);
            }
        }
        return $next($request);
    }
}
