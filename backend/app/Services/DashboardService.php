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
    public function getDashboardData(int $boxId, int $userId, ?string $startDateStr = null, ?string $endDateStr = null, ?int $chartYear = null, ?int $targetUserId = null): array
    {
        $today = Carbon::now();

        // 1. Centralize dates
        $dates = $this->getDateRanges($today, $startDateStr, $endDateStr, $chartYear);

        // 2. Fetch required reference data with caching
        $referenceData = $this->getReferenceData($boxId);

        $filterUserId = $targetUserId;

        // 3. Compile everything
        return array_merge(
            $this->getKpis($boxId, $dates, $referenceData, $filterUserId),
            $this->getActivityData($boxId, $dates, $filterUserId),
            $this->getCharts($boxId, $dates, $referenceData, $filterUserId),
            $this->getRecentData($boxId, $userId, $filterUserId)
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
     * Consolidated query for Lead KPIs using targeted indexed counts.
     */
    private function getKpis(int $boxId, array $dates, array $referenceData, ?int $filterUserId = null): array
    {
        $lostStatusIds = $referenceData['lostStatusIds'] ?: [];

        $baseQuery = Lead::where('box_id', $boxId);
        if ($filterUserId) {
            $baseQuery->where('assigned_to', $filterUserId);
        }

        // 1. Total leads (uses index)
        $totalLeads = (clone $baseQuery)->count();

        // 2. New leads this period (uses leads_box_del_created_idx)
        $newLeads = (clone $baseQuery)
            ->whereBetween('created_at', [$dates['periodStart'], $dates['periodEnd']])
            ->count();

        // 3. New leads last period (uses leads_box_del_created_idx)
        $newLeadsLastPeriod = (clone $baseQuery)
            ->whereBetween('created_at', [$dates['previousPeriodStart'], $dates['previousPeriodEnd']])
            ->count();

        // Total leads at end of last period (total - leads created after last period)
        $leadsAfterLastPeriod = (clone $baseQuery)
            ->where('created_at', '>', $dates['previousPeriodEnd'])
            ->count();
        $totalLeadsLastWeek = max(0, $totalLeads - $leadsAfterLastPeriod);

        // 4. Lost leads
        $lostLeadsThisPeriod = 0;
        $lostLeadsLastPeriod = 0;
        if (!empty($lostStatusIds)) {
            $lostLeadsThisPeriod = (clone $baseQuery)
                ->whereIn('lead_status_id', $lostStatusIds)
                ->whereBetween('updated_at', [$dates['periodStart'], $dates['periodEnd']])
                ->count();

            $lostLeadsLastPeriod = (clone $baseQuery)
                ->whereIn('lead_status_id', $lostStatusIds)
                ->whereBetween('updated_at', [$dates['previousPeriodStart'], $dates['previousPeriodEnd']])
                ->count();
        }

        // 5. Unassigned leads (uses leads_box_del_assigned_idx)
        $unassignedLeads = (clone $baseQuery)
            ->whereNull('assigned_to')
            ->where('duplicate_leads', 0)
            ->count();

        // 6. Duplicate leads (uses leads_box_del_duplicate_idx)
        $duplicateLeads = (clone $baseQuery)
            ->where('duplicate_leads', 1)
            ->count();

        // 7. Trash leads query (needs soft deleted)
        $trashQuery = Lead::where('box_id', $boxId)->onlyTrashed();
        if ($filterUserId) {
            $trashQuery->where('assigned_to', $filterUserId);
        }
        $trashLeads = $trashQuery->count();

        return [
            'totalLeads'         => $totalLeads,
            'totalLeadsChange'   => $this->calculatePercentageChange($totalLeads, $totalLeadsLastWeek),
            
            'newLeads'           => $newLeads,
            'newLeadsChange'     => $this->calculatePercentageChange($newLeads, $newLeadsLastPeriod),
            
            'lostLeads'          => $lostLeadsThisPeriod,
            'lostLeadsChange'    => $this->calculatePercentageChange($lostLeadsThisPeriod, $lostLeadsLastPeriod),
            
            'unassignedLeads'    => $unassignedLeads,
            'duplicateLeads'     => $duplicateLeads,
            'trashLeads'         => $trashLeads,
        ];
    }

    /**
     * Consolidated query for Activity KPIs.
     */
    private function getActivityData(int $boxId, array $dates, ?int $filterUserId = null): array
    {
        $taskTypes = ['task', 'call', 'email', 'meeting', 'whatsapp'];
        
        $lastTaskCalcTime = Box::whereKey($boxId)->value('last_task_calculation_time') ?? '00:30';
        if (substr_count($lastTaskCalcTime, ':') == 1) {
            $lastTaskCalcTime .= ':00';
        }

        $totalActivitiesQuery = Activity::where('box_id', $boxId);
        if ($filterUserId) $totalActivitiesQuery->where('assigned_to', $filterUserId);

        // Cache total activities for 2 minutes to prevent scanning ~1M rows repeatedly
        $totalActivities = Cache::remember(
            "box:{$boxId}:total_activities" . ($filterUserId ? ":{$filterUserId}" : ""),
            now()->addMinutes(2),
            fn() => (clone $totalActivitiesQuery)->count()
        );

        // Fast calculation: total minus activities created after previousPeriodEnd
        $activitiesAfterLastPeriod = (clone $totalActivitiesQuery)
            ->where('created_at', '>', $dates['previousPeriodEnd'])
            ->count();
        $totalActivitiesLastWeek = max(0, $totalActivities - $activitiesAfterLastPeriod);

        $periodStart = clone $dates['periodStart'];
        $periodEnd = clone $dates['periodEnd'];
        
        $activityStatsQuery = Activity::where('box_id', $boxId)
            ->whereIn('type', $taskTypes)
            // Critical index bound to prevent full table scan over all activities
            ->where('created_at', '>=', $periodStart->copy()->subDays(60));
            
        if ($filterUserId) {
            $activityStatsQuery->where('assigned_to', $filterUserId);
        }

        $activityStats = $activityStatsQuery->selectRaw("
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
    private function getCharts(int $boxId, array $dates, array $referenceData, ?int $filterUserId = null): array
    {
        // 1. Pipeline Stages (Leads by Status)
        $pipelineQuery = Lead::where('box_id', $boxId)
            ->whereBetween('created_at', [$dates['chartStart'], $dates['chartEnd']])
            ->whereNotNull('lead_status_id');
        if ($filterUserId) $pipelineQuery->where('assigned_to', $filterUserId);

        $pipelineData = $pipelineQuery->select('lead_status_id', DB::raw('count(*) as count'))
            ->groupBy('lead_status_id')
            ->get()
            ->map(function ($item) use ($referenceData) {
                $status = $referenceData['statuses']->firstWhere('id', $item->lead_status_id);
                return [
                    'name'  => $status->name ?? 'Unknown',
                    'count' => $item->count,
                ];
            });

        // 2. Lead Status Data (All-time counts cached for 5 mins)
        $statusCounts = Cache::remember(
            "box:{$boxId}:all_time_status_counts" . ($filterUserId ? ":{$filterUserId}" : ""),
            now()->addMinutes(5),
            function () use ($boxId, $filterUserId) {
                $statusCountsQuery = Lead::where('box_id', $boxId)
                    ->whereNotNull('lead_status_id');
                if ($filterUserId) $statusCountsQuery->where('assigned_to', $filterUserId);

                return $statusCountsQuery->select('lead_status_id', DB::raw('COUNT(*) as count'))
                    ->groupBy('lead_status_id')
                    ->pluck('count', 'lead_status_id');
            }
        );
            
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

        // 3. Source Stats (All-time counts and values cached for 5 mins)
        $sourceStats = Cache::remember(
            "box:{$boxId}:all_time_source_stats" . ($filterUserId ? ":{$filterUserId}" : ""),
            now()->addMinutes(5),
            function () use ($boxId, $filterUserId) {
                $sourceStatsQuery = Lead::where('box_id', $boxId)
                    ->whereNotNull('lead_source_id');
                if ($filterUserId) $sourceStatsQuery->where('assigned_to', $filterUserId);

                return $sourceStatsQuery->select('lead_source_id', DB::raw('count(*) as count'), DB::raw('sum(amount) as value'))
                    ->groupBy('lead_source_id')
                    ->get()
                    ->keyBy('lead_source_id');
            }
        );
            
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
        $companiesQuery = Lead::where('box_id', $boxId)
            ->whereBetween('created_at', [$dates['chartStart'], $dates['chartEnd']])
            ->whereNotNull('company_name');
        if ($filterUserId) $companiesQuery->where('assigned_to', $filterUserId);

        $companiesData = $companiesQuery->select('company_name', DB::raw('count(*) as count'), DB::raw('sum(amount) as value'))
            ->groupBy('company_name')
            ->orderByDesc('value')
            ->limit(5)
            ->get();

        // 5. New Leads Chart (Dynamic Range)
        $daysToShow = $dates['daysToShow'];
        $chartStartDate = $dates['today']->copy()->subDays($daysToShow)->startOfDay();

        $chartDates = [];
        $counts = [];
        
        $dailyCountsQuery = Lead::where('box_id', $boxId)
            ->where('created_at', '>=', $chartStartDate);
        if ($filterUserId) $dailyCountsQuery->where('assigned_to', $filterUserId);

        $dailyCounts = $dailyCountsQuery->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
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
        $lostReasonQuery = Lead::where('box_id', $boxId)
            ->whereBetween('created_at', [$dates['chartStart'], $dates['chartEnd']])
            ->whereNotNull('lost_reason_id');
        if ($filterUserId) $lostReasonQuery->where('assigned_to', $filterUserId);

        $lostReasonData = $lostReasonQuery->select('lost_reason_id', DB::raw('count(*) as count'))
            ->with('lostReason:id,name')
            ->groupBy('lost_reason_id')
            ->get()
            ->map(fn ($item) => [
                'name'  => $item->lostReason->name ?? 'Unknown',
                'count' => $item->count,
            ]);

        // 7. Top Countries & States
        $topCountriesQuery = Lead::where('box_id', $boxId)
            ->where('created_at', '>=', $dates['periodStart'])
            ->whereNotNull('country_id');
        if ($filterUserId) $topCountriesQuery->where('assigned_to', $filterUserId);

        $topCountriesData = $topCountriesQuery->select('country_id', DB::raw('count(*) as count'))
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

        $topStatesQuery = Lead::where('box_id', $boxId)
            ->where('created_at', '>=', $dates['periodStart'])
            ->whereNotNull('state_id');
        if ($filterUserId) $topStatesQuery->where('assigned_to', $filterUserId);

        $topStatesData = $topStatesQuery->select('state_id', DB::raw('count(*) as count'))
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
        
        $contactedRawQuery = Lead::where('box_id', $boxId)
            ->whereBetween('created_at', [$dates['chartStart'], $dates['chartEnd']])
            ->where(function ($q) use ($contactedStatusIds) {
                if (!empty($contactedStatusIds)) {
                    $q->whereIn('lead_status_id', $contactedStatusIds)->orWhereNotNull('last_activity_id');
                } else {
                    $q->whereNotNull('last_activity_id');
                }
            });
        if ($filterUserId) $contactedRawQuery->where('assigned_to', $filterUserId);

        $contactedRaw = $contactedRawQuery->select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->pluck('count', 'month')
            ->all();

        $notContactedRawQuery = Lead::where('box_id', $boxId)
            ->whereBetween('created_at', [$dates['chartStart'], $dates['chartEnd']])
            ->where(function ($q) use ($notContactedStatusIds) {
                if (!empty($notContactedStatusIds)) {
                    $q->whereIn('lead_status_id', $notContactedStatusIds)->whereNull('last_activity_id');
                } else {
                    $q->whereNull('last_activity_id');
                }
            });
        if ($filterUserId) $notContactedRawQuery->where('assigned_to', $filterUserId);

        $notContactedRaw = $notContactedRawQuery->select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
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
        $monthlySourceCountsQuery = Lead::where('box_id', $boxId)
            ->whereBetween('created_at', [$dates['chartStart'], $dates['chartEnd']])
            ->whereNotNull('lead_source_id');
        if ($filterUserId) $monthlySourceCountsQuery->where('assigned_to', $filterUserId);

        $monthlySourceCounts = $monthlySourceCountsQuery->select('lead_source_id', DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
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

        // 10. Assigned Leads by User per Month
        $assignedByUserMonthlyQuery = Lead::where('box_id', $boxId)
            ->whereBetween('created_at', [$dates['chartStart'], $dates['chartEnd']])
            ->whereNotNull('assigned_to');
            
        if ($filterUserId) $assignedByUserMonthlyQuery->where('assigned_to', $filterUserId);

        $assignedByUserMonthlyRaw = $assignedByUserMonthlyQuery->select('assigned_to', DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
            ->with('assignedTo:id,name')
            ->groupBy('assigned_to', 'month')
            ->get();
            
        $userSeries = [];
        $groupedByUser = $assignedByUserMonthlyRaw->groupBy('assigned_to');
        $userColors = [];
        
        $uIdx = 0;
        foreach ($groupedByUser as $uid => $userRecords) {
            $monthlyData = array_fill(1, 12, 0);
            foreach ($userRecords as $stat) {
                $monthlyData[$stat->month] = $stat->count;
            }
            $u = $userRecords->first()->assignedTo;
            $name = $u ? trim($u->name ?? '') : 'Unknown';
            if(!$name) $name = 'Unknown';
            $userSeries[] = [
                'name' => $name,
                'data' => array_values($monthlyData),
            ];
            $userColors[] = $palette[$uIdx % count($palette)];
            $uIdx++;
        }

        if (empty($userSeries)) {
            $userSeries[] = [
                'name' => 'No Data',
                'data' => array_fill(0, 12, 0),
            ];
            $userColors[] = '#d3d3d3';
        }

        // 11. User Performance: Total Assigned Leads vs Matured Leads vs Done Activities (cached for 5 mins)
        $chartYearKey = $dates['chartYear'];
        $userPerformanceChartData = Cache::remember(
            "box:{$boxId}:user_perf_chart:{$chartYearKey}" . ($filterUserId ? ":{$filterUserId}" : ""),
            now()->addMinutes(5),
            function () use ($boxId, $dates, $referenceData, $filterUserId) {
                $matureStageIds = $referenceData['statuses']
                    ->filter(fn ($status) => str_contains(strtolower($status->name), 'mature'))
                    ->pluck('id')
                    ->toArray();
                $matureStageSql = !empty($matureStageIds) ? ' OR lead_status_id IN (' . implode(',', $matureStageIds) . ')' : '';

                $userLeadStatsQuery = Lead::where('box_id', $boxId)
                    ->whereBetween('created_at', [$dates['chartStart'], $dates['chartEnd']])
                    ->whereNotNull('assigned_to');

                if ($filterUserId) {
                    $userLeadStatsQuery->where('assigned_to', $filterUserId);
                }

                $userLeadStats = $userLeadStatsQuery->select(
                    'assigned_to',
                    DB::raw('COUNT(*) as total_assigned'),
                    DB::raw("SUM(CASE WHEN is_matured = 1 OR matured_date IS NOT NULL{$matureStageSql} THEN 1 ELSE 0 END) as matured_count")
                )
                ->groupBy('assigned_to')
                ->orderByDesc('total_assigned')
                ->limit(15)
                ->get();

                $assignedUserIds = $userLeadStats->pluck('assigned_to')->filter()->unique()->toArray();

                $doneActivities = [];
                if (!empty($assignedUserIds)) {
                    $doneActivities = Activity::where('box_id', $boxId)
                        ->whereBetween('created_at', [$dates['chartStart'], $dates['chartEnd']])
                        ->whereIn('status', ['Close', 'Closed', 'Completed', 'Done'])
                        ->whereIn('assigned_to', $assignedUserIds)
                        ->select('assigned_to', DB::raw('COUNT(*) as count'))
                        ->groupBy('assigned_to')
                        ->pluck('count', 'assigned_to')
                        ->toArray();
                }

                $usersMap = !empty($assignedUserIds) ? \App\Models\User::whereIn('id', $assignedUserIds)->pluck('name', 'id') : collect();

                $userPerfCategories = [];
                $totalAssignedData = [];
                $maturedData = [];
                $doneActivitiesData = [];

                foreach ($userLeadStats as $stat) {
                    $uid = $stat->assigned_to;
                    $rawName = $usersMap[$uid] ?? ('User #' . $uid);
                    $userName = trim($rawName) ?: ('User #' . $uid);

                    $userPerfCategories[] = $userName;
                    $totalAssignedData[] = (int) $stat->total_assigned;
                    $maturedData[] = (int) $stat->matured_count;
                    $doneActivitiesData[] = (int) ($doneActivities[$uid] ?? 0);
                }

                return [
                    'categories' => $userPerfCategories,
                    'series' => [
                        [
                            'name' => 'Total Assigned Leads',
                            'data' => $totalAssignedData,
                        ],
                        [
                            'name' => 'Matured Leads',
                            'data' => $maturedData,
                        ],
                        [
                            'name' => 'Done Activities',
                            'data' => $doneActivitiesData,
                        ],
                    ],
                    'colors' => ['#7367F0', '#28C76F', '#FF9F43'],
                ];
            }
        );

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
            ],
            'assignedByUserMonthlyData' => [
                'labels' => $months,
                'series' => $userSeries,
                'colors' => $userColors
            ],
            'userPerformanceChartData' => $userPerformanceChartData,
        ];
    }

    /**
     * Fetch recent records.
     */
    private function getRecentData(int $boxId, int $userId, ?int $filterUserId = null): array
    {
        $recentBound = now()->subDays(30);

        $recentLeadsQuery = Lead::where('box_id', $boxId)
            ->where('created_at', '>=', $recentBound);
        if ($filterUserId) $recentLeadsQuery->where('assigned_to', $filterUserId);

        $recentLeads = $recentLeadsQuery->select('id', 'name', 'last_name', 'company_name', 'email', 'phone', 'lead_status_id', 'lead_source_id', 'assigned_to', 'created_at')
            ->with([
                'leadStatus:id,name',
                'leadSource:id,name',
                'assignedTo:id,name',
            ])
            ->latest('id')
            ->limit(7)
            ->get();

        $recentActivitiesQuery = Activity::where('box_id', $boxId)
            ->where('created_at', '>=', $recentBound)
            ->whereIn('type', ['task', 'call', 'email', 'meeting', 'whatsapp']);
        if ($filterUserId) $recentActivitiesQuery->where('assigned_to', $filterUserId);

        $recentActivities = $recentActivitiesQuery->select('id', 'type', 'subject', 'activity_date', 'status', 'created_at', 'activity_id')
            ->latest('id')
            ->limit(5)
            ->get();

        $followUpDataQuery = Activity::where('box_id', $boxId)
            ->where('created_at', '>=', $recentBound)
            ->whereIn('type', ['task', 'call', 'email', 'meeting', 'whatsapp'])
            ->where('status', 'Open')
            ->whereNotNull('activity_id');
        if ($filterUserId) $followUpDataQuery->where('assigned_to', $filterUserId);

        $followUpData = $followUpDataQuery->select('id', 'type', 'subject', 'activity_date', 'status', 'activity_id', 'created_at')
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
