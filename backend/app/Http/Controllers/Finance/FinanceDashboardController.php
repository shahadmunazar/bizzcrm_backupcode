<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Finance\FinanceDashboardService;
use App\Models\Box;

/**
 * Controller for the Finance Dashboard.
 * Handles serving the main dashboard view and fetching aggregate financial metrics.
 */
class FinanceDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(FinanceDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the main finance dashboard view.
     *
     * @param Request $request
     * @param string $box_slug
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->firstOrFail();
        return view('finance.dashboard.index', compact('box'));
    }

    public function getData(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->firstOrFail();
        
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        // Fetch optimized metrics from the service layer
        $data = $this->dashboardService->getDashboardData($box, $startDate, $endDate);
        
        return response()->json($data);
    }
}
