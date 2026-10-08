<?php
namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BoxUser;
use App\Models\Lead;
use App\Models\Activity;
use App\Models\CrmSource;
use App\Models\Country;
use App\Models\ContactStage;
use App\Models\LeadAssignmentHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeadsFrontendController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        $boxId = $boxUser ? $boxUser->box_id : null;

        $latestLead = Lead::where('box_id', $boxId)->latest('created_at')->first();
        $defaultYear = $latestLead ? Carbon::parse($latestLead->created_at)->year : date('Y');

        $availableYears = Lead::where('box_id', $boxId)
            ->whereNotNull('created_at')
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [date('Y')];
        }

        return view('frontend.leads.index', compact('defaultYear', 'availableYears'));
    }

    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }

    public function dashboardData(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->select('box_id')->first();
        
        if (!$boxUser || !$boxUser->box_id) {
            return response()->json(['error' => 'Box ID not found'], 403);
        }

        $startDateStr = $request->get('start_date');
        $endDateStr = $request->get('end_date');
        $chartYear = $request->get('chart_year');
        
        $cacheKey = "dashboard:{$boxUser->box_id}:{$userId}:{$startDateStr}:{$endDateStr}:{$chartYear}";

        $data = \Illuminate\Support\Facades\Cache::remember(
            $cacheKey,
            now()->addSeconds(30),
            function () use ($boxUser, $userId, $startDateStr, $endDateStr, $chartYear) {
                $service = app(\App\Services\DashboardService::class);
                return $service->getDashboardData(
                    $boxUser->box_id,
                    $userId,
                    $startDateStr,
                    $endDateStr,
                    $chartYear
                );
            }
        );

        return response()->json($data);
    }

    public function uploadSummernoteFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120', // max 5MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/uploads/summernote', $filename);

            // Get the public URL for the stored file
            $url = \Storage::url($path);

            return response()->json([
                'success' => true,
                'url' => asset($url),
                'filename' => $file->getClientOriginalName(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No file uploaded.',
        ], 400);
    }
}
