<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\SubscriptionHelper;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null $feature
     */
    public function handle(Request $request, Closure $next, $feature = null): Response
    {
        if (!SubscriptionHelper::hasValidSubscription()) {
            $boxSlug = request()->route('box_slug') ?? 'admin';

            // Avoid infinite redirect if already on any subscription-related page
            if ($request->routeIs('subscription.*') || $request->is('*/subscription-plan*')) {
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'No active subscription found. Please upgrade to continue using this feature.'], 403);
            }

            return redirect()->route('subscription.index', ['box_slug' => $boxSlug])
                ->with('error', 'No active subscription found. Please upgrade your plan.');
        }

        if ($feature && !SubscriptionHelper::hasFeature($feature)) {
            $boxSlug = request()->route('box_slug') ?? 'admin';

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => "Your plan does not support '$feature'. Please upgrade."], 403);
            }

            return redirect()->route('subscription.index', ['box_slug' => $boxSlug])
                ->with('error', "Your plan does not support the '$feature' feature. Please upgrade your plan.");
        }

        return $next($request);
    }
}
