<?php

namespace App\Http\Middleware;

use App\Helpers\BoxPermissionHelper;
use App\Models\BoxUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBoxPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            // If AJAX request, return JSON response
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'Your session has expired. Please login again.',
                    'redirect' => route('login')
                ], 401);
            }
            // Otherwise redirect to login
            return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
        }

        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();

        if (!$boxUser) {
            // If AJAX request, return JSON response
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'BoxUser not found. Please login again.',
                    'redirect' => route('login')
                ], 403);
            }
            // Otherwise redirect to login
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('login')->with('error', 'Your account is not properly configured. Please contact administrator.');
        }

        // Check permission using helper (supports multiple permissions separated by |)
        $permissions = explode('|', $permission);
        $hasPermission = false;
        
        foreach ($permissions as $perm) {
            if (BoxPermissionHelper::boxUserHasPermission($boxUser, trim($perm))) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            // If AJAX request, return JSON response
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'You do not have permission to access this resource.',
                    'redirect' => route('login')
                ], 403);
            }
            // Otherwise redirect to login
            return redirect()->route('login')->with('error', 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
