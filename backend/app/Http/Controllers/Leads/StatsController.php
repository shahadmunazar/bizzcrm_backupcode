<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReportStatsService;

class StatsController extends Controller
{
    protected $statsService;

    public function __construct(ReportStatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function sourceStats($box_slug)
    {
        return view('frontend.reports.source_stats', compact('box_slug'));
    }

    public function commonFiltersAjax($box_slug)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->firstOrFail();
        
        $users = \App\Models\BoxUser::with('user')->where('box_id', $box->id)->get();
        $stages = \App\Models\ContactStage::where('box_id', $box->id)->get();
        $sources = \App\Models\CrmSource::where('box_id', $box->id)->orderBy('name', 'asc')->get();

        return response()->json([
            'status' => true,
            'users' => $users,
            'stages' => $stages,
            'sources' => $sources
        ]);
    }

    public function sourceStatsAjax(Request $request, $box_slug)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->firstOrFail();
        
        $data = $this->statsService->getSourceStats($box->id, $request);
        $html = view('frontend.reports.partials._source_stats_rows', $data)->render();

        return response()->json(['status' => true, 'html' => $html]);
    }

    public function statusStats($box_slug)
    {
        return view('frontend.reports.status_stats', compact('box_slug'));
    }

    public function statusStatsAjax(Request $request, $box_slug)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->firstOrFail();
        
        $data = $this->statsService->getStatusStats($box->id, $request);
        $html = view('frontend.reports.partials._status_stats_rows', $data)->render();

        return response()->json(['status' => true, 'html' => $html]);
    }

    public function taskStats($box_slug)
    {
        return view('frontend.reports.task_stats', compact('box_slug'));
    }

    public function taskStatsAjax(Request $request, $box_slug)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->firstOrFail();
        
        $data = $this->statsService->getTaskStats($box->id, $request);
        $html = view('frontend.reports.partials._task_stats_rows', $data)->render();

        return response()->json(['status' => true, 'html' => $html]);
    }

    public function dateTaskStats($box_slug)
    {
        return view('frontend.reports.date_task_stats', compact('box_slug'));
    }

    public function dateTaskStatsAjax(Request $request, $box_slug)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->firstOrFail();
        
        $data = $this->statsService->getDateTaskStats($box->id, $request);
        $html = view('frontend.reports.partials._date_task_stats_rows', $data)->render();

        return response()->json(['status' => true, 'html' => $html]);
    }

    public function userStats($box_slug)
    {
        return view('frontend.reports.user_stats', compact('box_slug'));
    }

    public function userStatsAjax(Request $request, $box_slug)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->firstOrFail();
        
        $data = $this->statsService->getUserStats($box->id, $request);
        $html = view('frontend.reports.partials._user_stats_rows', $data)->render();

        return response()->json(['status' => true, 'html' => $html]);
    }
}
