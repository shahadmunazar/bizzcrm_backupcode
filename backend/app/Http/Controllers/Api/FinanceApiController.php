<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Finance\FinanceDashboardService;
use App\Models\BoxUser;
use Illuminate\Support\Facades\Auth;

class FinanceApiController extends Controller
{
    protected $dashboardService;

    public function __construct(FinanceDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function dashboard(Request $request)
    {
        try {
            $user = Auth::id();

            $boxUser = BoxUser::with('box')->where('user_id', $user)->first();
            if (!$boxUser || !$boxUser->box) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Box user or box not found.',
                ], 404);
            }

            $box = $boxUser->box;

            $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
            $endDate = $request->input('end_date', now()->format('Y-m-d'));

            // Fetch optimized metrics from the service layer
            $data = $this->dashboardService->getDashboardData($box, $startDate, $endDate);

            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Something went wrong while fetching finance dashboard data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
