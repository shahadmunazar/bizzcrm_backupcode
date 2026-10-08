<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Activity;
use App\Models\ContactStage;
use App\Models\CrmSource;
use App\Models\Box;
use App\Models\LeadAssignmentHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    /**
     * Get the consolidated dashboard data
     */
    public function getDashboardData(int $boxId, int $userId, ?string $startDateStr = null, ?string $endDateStr = null, ?int $chartYear = null): array
    {
        $today = Carbon::now();

        // 1. Centralize dates
        $dates = $this->getDateRanges($today, $startDateStr, $endDateStr, $chartYear);

        // 2. Fetch required reference data with caching
        $referenceData = $this->getReferenceData($boxId);

        // 3. Compile everything
        return array_merge(
            $this->getKpis($boxId, $dates, $referenceData),
            $this->getActivityData($boxId, $dates),
            $this->getCharts($boxId, $dates, $referenceData),
            $this->getRecentData($boxId, $userId)
        );
    }

    /**
     * Consolidate date calculations to avoid repeating them and ensure accuracy.
     */
    private function getDateRanges(Carbon $today, ?string $startDateStr, ?string $endDateStr, ?int $chartYear): array
    {
        $periodStart = $startDateStr ? Carbon::parse($startDateStr)->startOfDay() : $today->copy()->startOfDay();
        $periodEnd = $endDateStr ? Carbon::parse($endDateStr)->endOfDay() : $today->copy()->endOfDay();
        
        $daysDiff = $periodStart->diffInDays($periodEnd) + 1;
        
        $previousPeriodStart = $periodStart->copy()->subDays($daysDiff)->startOfDay();
        $previousPeriodEnd = $periodEnd->copy()->subDays($daysDiff)->endOfDay();

        $chartYear = (is_numeric($chartYear) && $chartYear > 2000) ? (int) $chartYear : $today->year;

        $daysToShow = min(364, max(0, $daysDiff - 1));

        return [
            'today'               => $today,
            'periodStart'         => $periodStart,
            'periodEnd'           => $periodEnd,
            'previousPeriodStart' => $previousPeriodStart,
            'previousPeriodEnd'   => $previousPeriodEnd,
            'daysToShow'          => $daysToShow,

            'chartStart'          => Carbon::create($chartYear, 1, 1)->startOfYear(),
            'chartEnd'            => Carbon::create($chartYear, 12, 31)->endOfYear(),
            'chartYear'           => $chartYear,
        ];
    }

    /**
     * Fetch static/config data like statuses and sources, cached for a short period.
     */
    private function getReferenceData(int $boxId): array
    {
        $statuses = Cache::remember(
            "box:{$boxId}:contact-stages",
            now()->addMinutes(10),
            fn () => ContactStage::where('box_id', $boxId)
                ->where('status', 1)
                ->orderBy('priority')
                ->get(['id', 'name', 'color'])
        );

        $sources = Cache::remember(
            "box:{$boxId}:crm-sources",
            now()->addMinutes(10),
            fn () => CrmSource::where('box_id', $boxId)
                ->select('id', 'name')
                ->get()
        );

        // Calculate specific status IDs for KPI usage
        $lostStatusIds = $statuses
            ->filter(fn ($status) => str_contains(strtolower($status->name), 'lost'))
            ->pluck('id')
            ->toArray();

        $contactedStatusIds = $statuses
            ->where('name', 'Contacted')
            ->pluck('id')
            ->toArray();

        $notContactedStatusIds = $statuses
            ->whereIn('name', ['New', 'Not Contacted'])
            ->pluck('id')
            ->toArray();

        return [
            'statuses'              => $statuses,
            'sources'               => $sources,
            'lostStatusIds'         => $lostStatusIds,
            'contactedStatusIds'    => $contactedStatusIds,
            'notContactedStatusIds' => $notContactedStatusIds,
        ];
    }

    /**
     * Consolidated query for Lead KPIs.
     */
    private function getKpis(int $boxId, array $dates, array $referenceData): array
    {
        $lostStatusIdsString = implode(',', $referenceData['lostStatusIds'] ?: [0]);

        $leadStats = Lead::where('box_id', $boxId)
            ->selectRaw("
                COUNT(*) as total_leads,
                
                SUM(created_at <= ?) as total_leads_last_period,

                SUM(created_at BETWEEN ? AND ?) as new_leads_this_period,
                SUM(created_at BETWEEN ? AND ?) as new_leads_last_period,

                SUM(
                    lead_status_id IN ($lostStatusIdsString) 
                    AND updated_at BETWEEN ? AND ?
                ) as lost_leads_this_period,

                SUM(
                    lead_status_id IN ($lostStatusIdsString) 
                    AND updated_at BETWEEN ? AND ?
                ) as lost_leads_last_period,

                SUM(assigned_to IS NULL AND duplicate_leads = 0) as unassigned_leads,
                
                SUM(duplicate_leads = 1) as duplicate_leads
            ", [
                $dates['previousPeriodEnd'],
                
                $dates['periodStart'], $dates['periodEnd'],
                $dates['previousPeriodStart'], $dates['previousPeriodEnd'],

                $dates['periodStart'], $dates['periodEnd'],
                $dates['previousPeriodStart'], $dates['previousPeriodEnd'],
            ])
            ->first();

        // Trash leads query (needs soft deleted)
        $trashLeads = Lead::where('box_id', $boxId)->onlyTrashed()->count();

        $totalLeads = (int) $leadStats->total_leads;
        $totalLeadsLastWeek = (int) $leadStats->total_leads_last_period;
        
        $newLeads = (int) $leadStats->new_leads_this_period;
        $newLeadsLastWeek = (int) $leadStats->new_leads_last_period;
        
        $lostLeads = (int) $leadStats->lost_leads_this_period;
        $lostLeadsLastWeek = (int) $leadStats->lost_leads_last_period;

        return [
            'totalLeads'         => $totalLeads,
            'totalLeadsChange'   => $this->calculatePercentageChange($totalLeads, $totalLeadsLastWeek),
            
            'newLeads'           => $newLeads,
            'newLeadsChange'     => $this->calculatePercentageChange($newLeads, $newLeadsLastWeek),
            
            'lostLeads'          => $lostLeads,
            'lostLeadsChange'    => $this->calculatePercentageChange($lostLeads, $lostLeadsLastWeek),
            
            'unassignedLeads'    => (int) $leadStats->unassigned_leads,
            'duplicateLeads'     => (int) $leadStats->duplicate_leads,
            'trashLeads'         => $trashLeads,
        ];
    }

    /**
     * Consolidated query for Activity KPIs.
     */
    private function getActivityData(int $boxId, array $dates): array
    {
        $taskTypes = ['task', 'call', 'email', 'meeting', 'whatsapp'];
        
        $lastTaskCalcTime = Box::whereKey($boxId)->value('last_task_calculation_time') ?? '00:30';
        if (substr_count($lastTaskCalcTime, ':') == 1) {
            $lastTaskCalcTime .= ':00';
        }

        // Fast counts without scanning `type` over millions of rows
        $totalActivities = Activity::where('box_id', $boxId)->count();

        $totalActivitiesLastWeek = Activity::where('box_id', $boxId)
            ->where('created_at', '<=', $dates['previousPeriodEnd'])
            ->count();

        $periodStart = clone $dates['periodStart'];
        $periodEnd = clone $dates['periodEnd'];
        
        $activityStats = Activity::where('box_id', $boxId)
            ->whereIn('type', $taskTypes)
            // Critical index bound to prevent full table scan over all activities
            ->where('created_at', '>=', $periodStart->copy()->subDays(60))
            ->selectRaw("
                SUM(
                    status = 'Open' 
                    AND activity_date BETWEEN ? AND ?
                ) as todays_tasks,

                SUM(status = 'Open' AND activity_date BETWEEN ? AND ?) as open_tasks,

                SUM(
                    status IN ('Close', 'Closed', 'Completed', 'Done') 
                    AND closed_by IS NOT NULL 
                    AND COALESCE(completed_date, updated_at) <= ADDTIME(activity_date, TIME(?))
                    AND COALESCE(completed_date, updated_at) BETWEEN ? AND ?
                ) as closed_on_time_tasks,

                SUM(
                    status IN ('Close', 'Closed', 'Completed', 'Done') 
                    AND closed_by IS NOT NULL 
                    AND COALESCE(completed_date, updated_at) > ADDTIME(activity_date, TIME(?))
                    AND COALESCE(completed_date, updated_at) BETWEEN ? AND ?
                ) as closed_late_tasks,

                SUM(
                    status IN ('Close', 'Closed', 'Completed', 'Done') 
                    AND closed_by IS NULL
                    AND COALESCE(completed_date, updated_at) BETWEEN ? AND ?
                ) as failed_tasks
            ", [
                $dates['today']->copy()->startOfDay(), $dates['today']->copy()->endOfDay(),

                $periodStart, $periodEnd,

                $lastTaskCalcTime,
                $periodStart, $periodEnd,

                $lastTaskCalcTime,
                $periodStart, $periodEnd,

                $periodStart, $periodEnd,
            ])
            ->first();

        return [
            'totalActivities'       => $totalActivities,
            'totalActivitiesChange' => $this->calculatePercentageChange($totalActivities, $totalActivitiesLastWeek),
            
            'todaysTasks'           => (int) ($activityStats->todays_tasks ?? 0),
            'openTasks'             => (int) ($activityStats->open_tasks ?? 0),
            'closedOnTimeTasks'     => (int) ($activityStats->closed_on_time_tasks ?? 0),
            'closedLateTasks'       => (int) ($activityStats->closed_late_tasks ?? 0),
            'failedTasks'           => (int) ($activityStats->failed_tasks ?? 0),
        ];
    }

    /**
     * Fetch all charts related to leads and sources.
     */
    private function getCharts(int $boxId, array $dates, array $referenceData): array
    {
        // 1. Pipeline Stages (Leads by Status)
        $pipelineData = Lead::where('box_id', $boxId)
            ->whereBetween('created_at', [$dates['chartStart'], $dates['chartEnd']])
            ->whereNotNull('lead_status_id')
            ->select('lead_status_id', DB::raw('count(*) as count'))
            ->groupBy('lead_status_id')
            ->get()
            ->map(function ($item) use ($referenceData) {
                $status = $referenceData['statuses']->firstWhere('id', $item->lead_status_id);
                return [
                    'name'  => $status->name ?? 'Unknown',
                    'count' => $item->count,
                ];
            });

        // 2. Lead Status Data (All-time counts)
        $statusCounts = Lead::where('box_id', $boxId)
            ->whereNotNull('lead_status_id')
            ->select('lead_status_id', DB::raw('COUNT(*) as count'))
            ->groupBy('lead_status_id')
            ->pluck('count', 'lead_status_id');
            
        $statusData = $referenceData['statuses']->map(function ($status) use ($statusCounts) {
            $colors = ['#7367F0', '#28C76F', '#EA5455', '#FF9F43', '#00CFDD', '#4B4B46', '#1E90FF', '#FF1493', '#FFD700', '#8A2BE2'];
            $color = $status->color ?: $colors[abs(crc32($status->name)) % count($colors)];
            
            return [
                'id'    => $status->id,
                'name'  => $status->name,
                'count' => (int) ($statusCounts[$status->id] ?? 0),
                'color' => $color,
            ];
        });

        // 3. Source Stats
        $sourceStats = Lead::where('box_id', $boxId)
            ->whereNotNull('lead_source_id')
            ->select('lead_source_id', DB::raw('count(*) as count'), DB::raw('sum(amount) as value'))
            ->groupBy('lead_source_id')
            ->get()
            ->keyBy('lead_source_id');
            
        $sourceData = $referenceData['sources']->map(function ($source) use ($sourceStats) {
                $stats = $sourceStats->get($source->id);
                return [
                    'name'  => $source->name,
                    'count' => $stats ? (int)$stats->count : 0,
                    'value' => $stats ? (float)($stats->value ?? 0) : 0,
                ];
            })
            ->filter(fn ($item) => $item['count'] > 0)
            ->sortByDesc('count')
            ->values();

        // 4. Leads by Company (Top 5 by Value)
        $companiesData = Lead::where('box_id', $boxId)
            ->whereBetween('created_at', [$dates['chartStart'], $dates['chartEnd']])
            ->whereNotNull('company_name')
            ->select('company_name', DB::raw('count(*) as count'), DB::raw('sum(amount) as value'))
            ->groupBy('company_name')
            ->orderByDesc('value')
            ->limit(5)
            ->get();

        // 5. New Leads Chart (Dynamic Range)
        $daysToShow = $dates['daysToShow'];
        $chartStartDate = $dates['today']->copy()->subDays($daysToShow)->startOfDay();

        $chartDates = [];
        $counts = [];
        
        $dailyCounts = Lead::where('box_id', $boxId)
            ->where('created_at', '>=', $chartStartDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        for ($i = $daysToShow; $i >= 0; $i--) {
            $date = $dates['today']->copy()->subDays($i);
            $chartDates[] = $date->format('d M');
            $counts[] = $dailyCounts->get($date->format('Y-m-d'), 0);
        }

        $newLeadsChartData = [
            'labels' => $chartDates,
            'data'   => $counts,
        ];

        // 6. Lost Leads By Reason
        $lostReasonData = Lead::where('box_id', $boxId)
            ->whereBetween('created_at', [$dates['chartStart'], $dates['chartEnd']])
            ->whereNotNull('lost_reason_id')
            ->select('lost_reason_id', DB::raw('count(*) as count'))
            ->with('lostReason:id,name')
            ->groupBy('lost_reason_id')
            ->get()
            ->map(fn ($item) => [
                'name'  => $item->lostReason->name ?? 'Unknown',
                'count' => $item->count,
            ]);

        // 7. Top Countries & States
        $topCountriesData = Lead::where('box_id', $boxId)
            ->where('created_at', '>=', $dates['periodStart'])
            ->whereNotNull('country_id')
            ->select('country_id', DB::raw('count(*) as count'))
            ->with('country:id,name,code')
            ->groupBy('country_id')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'name'  => $item->country->name ?? 'Unknown',
                'code'  => $item->country->code ?? '',
                'count' => $item->count,
            ]);

        $topStatesData = Lead::where('box_id', $boxId)
            ->where('created_at', '>=', $dates['periodStart'])
            ->whereNotNull('state_id')
            ->select('state_id', DB::raw('count(*) as count'))
            ->with('state:id,name')
            ->groupBy('state_id')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'name'  => $item->state->name ?? 'Unknown',
                'count' => $item->count,
            ]);

        // 8. Contacted vs Not Contacted Monthly Breakdown
        $contactedStatusIds = $referenceData['contactedStatusIds'];
        $notContactedStatusIds = $referenceData['notContactedStatusIds'];
        
        $contactedRaw = Lead::where('box_id', $boxId)
            ->whereBetween('created_at', [$dates['chartStart'], $dates['chartEnd']])
            ->where(function ($q) use ($contactedStatusIds) {
                if (!empty($contactedStatusIds)) {
                    $q->whereIn('lead_status_id', $contactedStatusIds)->orWhereNotNull('last_activity_id');
                } else {
                    $q->whereNotNull('last_activity_id');
                }
            })
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->pluck('count', 'month')
            ->all();

        $notContactedRaw = Lead::where('box_id', $boxId)
            ->whereBetween('created_at', [$dates['chartStart'], $dates['chartEnd']])
            ->where(function ($q) use ($notContactedStatusIds) {
                if (!empty($notContactedStatusIds)) {
                    $q->whereIn('lead_status_id', $notContactedStatusIds)->whereNull('last_activity_id');
                } else {
                    $q->whereNull('last_activity_id');
                }
            })
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->pluck('count', 'month')
            ->all();

        $contactedMonthly = array_fill(1, 12, 0);
        $notContactedMonthly = array_fill(1, 12, 0);

        foreach ($contactedRaw as $monthNum => $count) {
            $contactedMonthly[$monthNum] = $count;
        }
        foreach ($notContactedRaw as $monthNum => $count) {
            $notContactedMonthly[$monthNum] = $count;
        }

        // 9. Leads By Sources Monthly Breakdown
        $sourceSeries = [];
        $monthlySourceCounts = Lead::where('box_id', $boxId)
            ->whereBetween('created_at', [$dates['chartStart'], $dates['chartEnd']])
            ->whereNotNull('lead_source_id')
            ->select('lead_source_id', DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
            ->groupBy('lead_source_id', 'month')
            ->get()
            ->groupBy('lead_source_id');


        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $sourceColors = [];
        $palette = ['#f3475c', '#28C76F', '#FF9F43', '#7367F0', '#00CFDD', '#4B4B46', '#1E90FF', '#FF1493', '#FFD700', '#8A2BE2'];
        
        foreach ($referenceData['sources'] as $idx => $source) {
            $monthlyData = array_fill(1, 12, 0);
            if ($monthlySourceCounts->has($source->id)) {
                foreach ($monthlySourceCounts[$source->id] as $stat) {
                    $monthlyData[$stat->month] = $stat->count;
                }
            }
            $sourceSeries[] = [
                'name' => $source->name,
                'data' => array_values($monthlyData),
            ];
            $sourceColors[] = $palette[$idx % count($palette)];
        }

        return [
            'pipelineData'        => $pipelineData,
            'statusData'          => $statusData,
            'sourceData'          => $sourceData,
            'companiesData'       => $companiesData,
            'newLeadsChartData'   => $newLeadsChartData,
            'lostReasonData'      => $lostReasonData,
            'topCountriesData'    => $topCountriesData,
            'topStatesData'       => $topStatesData,
            'contactStatusData'   => [
                'labels' => $months,
                'series' => [
                    [
                        'name'  => 'Contacted',
                        'data'  => array_values($contactedMonthly),
                        'color' => '#22C55E'
                    ],
                    [
                        'name'  => 'Not Contacted',
                        'data'  => array_values($notContactedMonthly),
                        'color' => '#EF4444'
                    ]
                ],
                'colors' => ['#22C55E', '#EF4444']
            ],
            'sourceStatusData'    => [
                'labels' => $months,
                'series' => $sourceSeries,
                'colors' => $sourceColors
            ]
        ];
    }

    /**
     * Fetch recent records.
     */
    private function getRecentData(int $boxId, int $userId): array
    {
        $recentBound = now()->subDays(30);

        $recentLeads = Lead::where('box_id', $boxId)
            ->where('created_at', '>=', $recentBound)
            ->select('id', 'name', 'last_name', 'company_name', 'email', 'phone', 'lead_status_id', 'lead_source_id', 'assigned_to', 'created_at')
            ->with([
                'leadStatus:id,name',
                'leadSource:id,name',
                'assignedTo:id,name',
            ])
            ->latest('id')
            ->limit(7)
            ->get();

        $recentActivities = Activity::where('box_id', $boxId)
            ->where('created_at', '>=', $recentBound)
            ->whereIn('type', ['task', 'call', 'email', 'meeting', 'whatsapp'])
            ->select('id', 'type', 'subject', 'activity_date', 'status', 'created_at', 'activity_id')
            ->latest('id')
            ->limit(5)
            ->get();

        $followUpData = Activity::where('box_id', $boxId)
            ->where('created_at', '>=', $recentBound)
            ->whereIn('type', ['task', 'call', 'email', 'meeting', 'whatsapp'])
            ->where('status', 'Open')
            ->whereNotNull('activity_id')
            ->select('id', 'type', 'subject', 'activity_date', 'status', 'activity_id', 'created_at')
            ->with([
                'lead:id,name,last_name,designation,email,phone,location'
            ])
            ->orderBy('activity_date', 'asc')
            ->limit(5)
            ->get();

        $recentAssignedLeads = LeadAssignmentHistory::select('lead_assignment_histories.*')
            ->join('leads', 'leads.id', '=', 'lead_assignment_histories.lead_id')
            ->where('lead_assignment_histories.user_id', $userId)
            ->where('leads.box_id', $boxId)
            ->where('lead_assignment_histories.created_at', '>=', now()->subDays(60))
            ->with([
                'lead' => function ($q) {
                    $q->with(['leadSource:id,name', 'leadStatus:id,name', 'assignedTo:id,name']);
                },
                'assignedBy:id,name'
            ])
            ->latest('lead_assignment_histories.id')
            ->limit(7)
            ->get();

        return [
            'recentLeads'         => $recentLeads,
            'recentActivities'    => $recentActivities,
            'followUpData'        => $followUpData,
            'recentAssignedLeads' => $recentAssignedLeads,
        ];
    }

    /**
     * Helper to calculate percentage change.
     */
    private function calculatePercentageChange($current, $previous): string
    {
        if ($previous == 0) {
            return $current > 0 ? '+100%' : '0%';
        }
        $change = (($current - $previous) / $previous) * 100;
        return ($change > 0 ? '+' : '') . round($change, 1) . '%';
    }
}
