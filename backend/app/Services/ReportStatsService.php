<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Activity;
use App\Models\ContactStage;
use App\Models\CrmSource;
use App\Models\BoxUser;
use Carbon\Carbon;
use DB;

class ReportStatsService
{
    private function applyFilters($query, $request, $dateColumn = 'created_at', $userColumn = 'assigned_to')
    {
        if ($request->has('user_id') && $request->user_id != 'all' && !empty($request->user_id)) {
            $query->where($userColumn, $request->user_id);
        }
        
        if ($request->has('date_range') && !empty($request->date_range)) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) == 2) {
                $start = Carbon::parse(trim($dates[0]))->startOfDay();
                $end = Carbon::parse(trim($dates[1]))->endOfDay();
                $query->whereBetween($dateColumn, [$start, $end]);
            }
        }

        return $query;
    }

    public function getSourceStats($boxId, $request)
    {
        $sources = CrmSource::where('box_id', $boxId)->orderBy('name', 'asc')->get();
        $stages = ContactStage::where('box_id', $boxId)->get();

        $query = Lead::select('lead_source_id', 'lead_status_id', 
                DB::raw('count(*) as total'),
                DB::raw('SUM(CASE WHEN duplicate_leads = 0 THEN 1 ELSE 0 END) as actual_leads'),
                DB::raw('SUM(CASE WHEN duplicate_leads = 1 THEN 1 ELSE 0 END) as duplicate_leads'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->where('box_id', $boxId)
            ->whereNotNull('lead_source_id');
            
        $this->applyFilters($query, $request, 'created_at', 'assigned_to');
        
        $leadCounts = $query->groupBy('lead_source_id', 'lead_status_id')->get();
            
        $stats = [];
        $sourceTotals = [];
        $sourceAmounts = [];
        foreach ($sources as $source) {
            $stats[$source->id] = [];
            $sourceTotals[$source->id] = 0;
            $sourceAmounts[$source->id] = 0;
            foreach ($stages as $stage) {
                $stats[$source->id][$stage->id] = [
                    'actual' => 0,
                    'duplicate' => 0,
                    'total' => 0
                ];
            }
        }
        
        foreach ($leadCounts as $count) {
            if (isset($stats[$count->lead_source_id][$count->lead_status_id])) {
                $stats[$count->lead_source_id][$count->lead_status_id] = [
                    'actual' => $count->actual_leads,
                    'duplicate' => $count->duplicate_leads,
                    'total' => $count->total
                ];
            }
            if (isset($sourceTotals[$count->lead_source_id])) {
                $sourceTotals[$count->lead_source_id] += $count->total;
                $sourceAmounts[$count->lead_source_id] += $count->total_amount;
            }
        }
        
        $sort = $request->get('sort');
        if ($sort == 'asc') {
            $sources = $sources->sortBy(function($source) use ($sourceTotals) {
                return $sourceTotals[$source->id] ?? 0;
            })->values();
        } elseif ($sort == 'desc') {
            $sources = $sources->sortByDesc(function($source) use ($sourceTotals) {
                return $sourceTotals[$source->id] ?? 0;
            })->values();
        } elseif ($sort == 'recent') {
            $sources = $sources->sortByDesc('created_at')->values();
        }

        return [
            'sources' => $sources,
            'stages' => $stages,
            'stats' => $stats,
            'sourceTotals' => $sourceTotals,
            'sourceAmounts' => $sourceAmounts
        ];
    }

    public function getStatusStats($boxId, $request)
    {
        $sources = CrmSource::where('box_id', $boxId)->orderBy('name', 'asc')->get();
        $stages = ContactStage::where('box_id', $boxId)->get();

        $query = Lead::select('lead_source_id', 'lead_status_id', 
                DB::raw('count(*) as total'),
                DB::raw('SUM(CASE WHEN duplicate_leads = 0 THEN 1 ELSE 0 END) as actual_leads'),
                DB::raw('SUM(CASE WHEN duplicate_leads = 1 THEN 1 ELSE 0 END) as duplicate_leads'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->where('box_id', $boxId)
            ->whereNotNull('lead_status_id');
            
        $this->applyFilters($query, $request, 'created_at', 'assigned_to');
        
        $leadCounts = $query->groupBy('lead_source_id', 'lead_status_id')->get();
            
        $stats = [];
        $stageTotals = [];
        $stageAmounts = [];
        foreach ($stages as $stage) {
            $stats[$stage->id] = [];
            $stageTotals[$stage->id] = 0;
            $stageAmounts[$stage->id] = 0;
            foreach ($sources as $source) {
                $stats[$stage->id][$source->id] = [
                    'actual' => 0,
                    'duplicate' => 0,
                    'total' => 0
                ];
            }
        }
        
        foreach ($leadCounts as $count) {
            if (isset($stats[$count->lead_status_id][$count->lead_source_id])) {
                $stats[$count->lead_status_id][$count->lead_source_id] = [
                    'actual' => $count->actual_leads,
                    'duplicate' => $count->duplicate_leads,
                    'total' => $count->total
                ];
            }
            if (isset($stageTotals[$count->lead_status_id])) {
                $stageTotals[$count->lead_status_id] += $count->total;
                $stageAmounts[$count->lead_status_id] += $count->total_amount;
            }
        }
        
        $sort = $request->get('sort');
        if ($sort == 'asc') {
            $stages = $stages->sortBy(function($stage) use ($stageTotals) {
                return $stageTotals[$stage->id] ?? 0;
            })->values();
        } elseif ($sort == 'desc') {
            $stages = $stages->sortByDesc(function($stage) use ($stageTotals) {
                return $stageTotals[$stage->id] ?? 0;
            })->values();
        } elseif ($sort == 'recent') {
            $stages = $stages->sortByDesc('created_at')->values();
        }

        return [
            'stages' => $stages,
            'sources' => $sources,
            'stats' => $stats,
            'stageTotals' => $stageTotals,
            'stageAmounts' => $stageAmounts
        ];
    }

    public function getTaskStats($boxId, $request)
    {
        $users = BoxUser::with('user')->where('box_id', $boxId)->get();
        $stages = ContactStage::where('box_id', $boxId)->get();

        $query = Activity::join('leads', 'activities.activity_id', '=', 'leads.id')
            ->where('activities.box_id', $boxId)
            ->where('leads.box_id', $boxId)
            ->whereIn('activities.type', ['task', 'call', 'email', 'meeting', 'whatsapp', 'note'])
            ->whereNotNull('activities.assigned_to')
            ->select(
                'activities.assigned_to', 
                'leads.lead_status_id', 
                DB::raw("COUNT(CASE WHEN activities.status = 'Open' OR (activities.status IN ('Close', 'Completed', 'Closed') AND activities.created_at != activities.completed_date) THEN 1 END) as total"),
                DB::raw("COUNT(CASE WHEN activities.status IN ('Close', 'Completed', 'Closed') AND activities.created_at != activities.completed_date THEN 1 END) as completed")
            );
            
        $this->applyFilters($query, $request, 'activities.activity_date', 'activities.assigned_to');

        $taskCounts = $query->groupBy('activities.assigned_to', 'leads.lead_status_id')->get();
            
        $stats = [];
        $userTotals = [];
        foreach ($users as $bUser) {
            $userId = $bUser->user_id;
            $stats[$userId] = [];
            $userTotals[$userId] = ['completed' => 0, 'total' => 0];
            foreach ($stages as $stage) {
                $stats[$userId][$stage->id] = ['completed' => 0, 'total' => 0];
            }
        }
        
        foreach ($taskCounts as $count) {
            if (isset($stats[$count->assigned_to][$count->lead_status_id])) {
                $stats[$count->assigned_to][$count->lead_status_id] = [
                    'completed' => $count->completed,
                    'total' => $count->total
                ];
                $userTotals[$count->assigned_to]['completed'] += $count->completed;
                $userTotals[$count->assigned_to]['total'] += $count->total;
            }
        }
        
        $sort = $request->get('sort', 'desc');
        if ($sort == 'asc') {
            $users = $users->sortBy(function($u) use ($userTotals) {
                return $userTotals[$u->user_id]['total'] ?? 0;
            })->values();
        } elseif ($sort == 'desc') {
            $users = $users->sortByDesc(function($u) use ($userTotals) {
                return $userTotals[$u->user_id]['total'] ?? 0;
            })->values();
        } elseif ($sort == 'recent') {
            $users = $users->sortByDesc('created_at')->values();
        }

        return [
            'users' => $users,
            'stages' => $stages,
            'stats' => $stats,
            'userTotals' => $userTotals
        ];
    }

    public function getDateTaskStats($boxId, $request)
    {
        $stages = ContactStage::where('box_id', $boxId)->get();

        $query = Activity::join('leads', 'activities.activity_id', '=', 'leads.id')
            ->where('activities.box_id', $boxId)
            ->where('leads.box_id', $boxId)
            ->whereIn('activities.type', ['task', 'call', 'email', 'meeting', 'whatsapp', 'note'])
            ->select(
                DB::raw('DATE(activities.activity_date) as activity_date'),
                'leads.lead_status_id', 
                DB::raw("COUNT(CASE WHEN activities.status = 'Open' OR (activities.status IN ('Close', 'Completed', 'Closed') AND activities.created_at != activities.completed_date) THEN 1 END) as total"),
                DB::raw("COUNT(CASE WHEN activities.status IN ('Close', 'Completed', 'Closed') AND activities.created_at != activities.completed_date THEN 1 END) as completed")
            );
            
        $this->applyFilters($query, $request, 'activities.activity_date', 'activities.assigned_to');

        $taskCounts = $query->groupBy('activity_date', 'leads.lead_status_id')->get();
            
        $dates = $taskCounts->pluck('activity_date')->filter()->map(function($d) {
            return Carbon::parse($d)->format('Y-m-d');
        })->unique()->values();
        
        $stats = [];
        $dateTotals = [];
        
        foreach ($dates as $date) {
            $stats[$date] = [];
            $dateTotals[$date] = ['completed' => 0, 'total' => 0];
            foreach ($stages as $stage) {
                $stats[$date][$stage->id] = ['completed' => 0, 'total' => 0];
            }
        }
        
        foreach ($taskCounts as $count) {
            if (!$count->activity_date) continue;
            $actDate = Carbon::parse($count->activity_date)->format('Y-m-d');
            if (isset($stats[$actDate][$count->lead_status_id])) {
                $stats[$actDate][$count->lead_status_id] = [
                    'completed' => $count->completed,
                    'total' => $count->total
                ];
                $dateTotals[$actDate]['completed'] += $count->completed;
                $dateTotals[$actDate]['total'] += $count->total;
            }
        }
        
        $sort = $request->get('sort', 'desc');
        if ($sort == 'asc') {
            $dates = $dates->sortBy(function($d) use ($dateTotals) {
                return $dateTotals[$d]['total'] ?? 0;
            })->values();
        } elseif ($sort == 'desc') {
            $dates = $dates->sortByDesc(function($d) use ($dateTotals) {
                return $dateTotals[$d]['total'] ?? 0;
            })->values();
        } elseif ($sort == 'recent') {
            $dates = $dates->sortDesc()->values();
        }

        return [
            'dates' => $dates,
            'stages' => $stages,
            'stats' => $stats,
            'dateTotals' => $dateTotals
        ];
    }

    public function getUserStats($boxId, $request)
    {
        $users = BoxUser::with('user')->where('box_id', $boxId)->get();
        $stages = ContactStage::where('box_id', $boxId)->get();

        $query = Lead::where('box_id', $boxId)
            ->whereNotNull('assigned_to')
            ->select(
                'assigned_to', 
                'lead_status_id', 
                DB::raw("COUNT(*) as total"),
                DB::raw('SUM(CASE WHEN duplicate_leads = 0 THEN 1 ELSE 0 END) as actual_leads'),
                DB::raw('SUM(CASE WHEN duplicate_leads = 1 THEN 1 ELSE 0 END) as duplicate_leads'),
                DB::raw("SUM(CASE WHEN is_matured = 1 THEN amount ELSE 0 END) as matured_amount")
            );
            
        $this->applyFilters($query, $request, 'created_at', 'assigned_to');

        $leadCounts = $query->groupBy('assigned_to', 'lead_status_id')->get();
            
        $stats = [];
        $userTotals = [];
        $userMaturedAmounts = [];
        foreach ($users as $bUser) {
            $userId = $bUser->user_id;
            $stats[$userId] = [];
            $userTotals[$userId] = 0;
            $userMaturedAmounts[$userId] = 0;
            foreach ($stages as $stage) {
                $stats[$userId][$stage->id] = [
                    'actual' => 0,
                    'duplicate' => 0,
                    'total' => 0
                ];
            }
        }
        
        foreach ($leadCounts as $count) {
            if (isset($stats[$count->assigned_to][$count->lead_status_id])) {
                $stats[$count->assigned_to][$count->lead_status_id] = [
                    'actual' => $count->actual_leads,
                    'duplicate' => $count->duplicate_leads,
                    'total' => $count->total
                ];
                $userTotals[$count->assigned_to] += $count->total;
                $userMaturedAmounts[$count->assigned_to] += $count->matured_amount;
            }
        }
        
        $sort = $request->get('sort', 'desc');
        if ($sort == 'asc') {
            $users = $users->sortBy(function($u) use ($userTotals) {
                return $userTotals[$u->user_id] ?? 0;
            })->values();
        } elseif ($sort == 'desc') {
            $users = $users->sortByDesc(function($u) use ($userTotals) {
                return $userTotals[$u->user_id] ?? 0;
            })->values();
        } elseif ($sort == 'recent') {
            $users = $users->sortByDesc('created_at')->values();
        }

        return [
            'users' => $users,
            'stages' => $stages,
            'stats' => $stats,
            'userTotals' => $userTotals,
            'userMaturedAmounts' => $userMaturedAmounts
        ];
    }
}
