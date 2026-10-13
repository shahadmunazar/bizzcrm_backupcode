<?php
namespace App\Http\Middleware;

use App\Models\Box;
use App\Models\BoxUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRolesAuth
{
    /**
     * Handle an incoming request and check if user has required roles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles  Roles to check against (accepts multiple)
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();   

        if (!$user) {
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

        if (!$user->hasAnyRole($roles)) {
            // If they are impersonating but hitting a route they don't have access to,
            // assume they refreshed their original tab, so we revert them to their original user.
            // (Only do this for standard page loads, not background AJAX calls!)
            if (session()->has('impersonated_by') && !$request->ajax() && !$request->expectsJson() && in_array('superadmin', $roles)) {
                $originalUserId = session('impersonated_by');
                Auth::loginUsingId($originalUserId);
                session()->forget('impersonated_by');
                // Reload the current request with the restored superadmin user
                return redirect($request->fullUrl());
            }

            // If AJAX request, return JSON response
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'Unauthorized: You do not have permission.',
                    'redirect' => route('login')
                ], 403);
            }
            // Otherwise redirect to a safe place
            return redirect()->route('login')->with('error', 'Unauthorized: You do not have permission to access this resource.');
        }

        $boxSlug = $request->route('box_slug');

        $box = Box::where('slug', $boxSlug)->first();

        if (!$box) {
            // If AJAX request, return JSON response
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'Box not found.',
                    'redirect' => route('login')
                ], 404);
            }
            // Otherwise redirect
            return redirect()->route('login')->with('error', 'Box not found. Please login again.');
        }

        $isInBox = BoxUser::where('box_id', $box->id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$isInBox) {
            // If AJAX request, return JSON response
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'You are not a member of this box.',
                    'redirect' => route('login')
                ], 403);
            }
            
            if ($user->hasRole('superadmin')) {
                // Get the superadmin box slug
                $superadminBox = \App\Models\Box::where('superadmin_id', $user->id)->first();
                $redirectSlug = $superadminBox ? $superadminBox->slug : 'demobizzcom';
                return redirect()->route('superadmin.company', ['box_slug' => $redirectSlug])->with('error', 'You are no longer impersonating.');
            }
            
            // Otherwise redirect
            return redirect()->route('login')->with('error', 'You are not a member of this box. Please login again.');
        }
        view()->share('box_slug', $boxSlug);
        session(['box_slug' => $boxSlug]);
        $request->attributes->set('box_slug', $boxSlug);
        $request->attributes->set('box', $box);

        return $next($request);
    }

}
