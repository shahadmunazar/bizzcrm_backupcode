<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Box;
use App\Models\BoxUser;
use App\Models\LeaveType;
use App\Services\AdminDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminFrontendController extends Controller
{
    /**
     * Web Dashboard view or JSON API response based on request format.
     */
    public function index(Request $request, $box_slug)
    {
        // Support direct API consumption from the dashboard URL
        if ($request->wantsJson() || $request->query('format') === 'json' || $request->is('api/*')) {
            return $this->apiDashboardComplete($request, $box_slug);
        }

        $box = Box::where('slug', $box_slug)->first();
        $boxId = $box ? $box->id : null;
        
        $jobApplicantsCount = 0;
        $employees = collect();
        $leaveTypes = collect();

        if ($boxId) {
            $jobApplicantsCount = \App\Models\JobApplicant::ofBox($boxId)->count();
            
            $employees = BoxUser::with('user')
                ->where('box_id', $boxId)
                ->whereHas('user', function ($q) {
                    $q->whereDoesntHave('roles', function ($q2) {
                        $q2->where('name', 'super_admin');
                    });
                })
                ->get();
                
            $leaveTypes = LeaveType::where('box_id', $boxId)->get();
        }

        return view('frontend.admin.index', compact('jobApplicantsCount', 'employees', 'leaveTypes'));
    }

    /**
     * Unified Admin Dashboard API endpoint.
     * Consolidates all widgets: attendance, tasks, performers, employee types, departments, and activities.
     */
    public function apiDashboardComplete(Request $request, $box_slug = null)
    {
        try {
            $slug = $box_slug ?? $request->route('box_slug') ?? $request->get('box_slug');
            $box = null;

            if ($slug) {
                $box = Box::where('slug', $slug)->first();
            }

            if (!$box && Auth::check()) {
                $userId = Auth::id();
                $box = BoxUser::where('user_id', $userId)->first()?->box 
                    ?? Box::where('superadmin_id', $userId)->first();
            }

            if (!$box) {
                return response()->json([
                    'success' => false,
                    'message' => 'Box not found.',
                ], 404);
            }

            $service = app(AdminDashboardService::class);
            $dashboardData = $service->getDashboardData($box, $request);

            return response()->json([
                'success' => true,
                'data' => $dashboardData,
            ], 200);

        } catch (\Throwable $e) {
            \Log::error('Error fetching admin dashboard data: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}
