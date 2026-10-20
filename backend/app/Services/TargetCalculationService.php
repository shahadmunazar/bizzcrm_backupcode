<?php

namespace App\Services;

use App\Models\TargetTemplate;
use App\Models\TargetAssignment;
use App\Models\PerformanceResult;
use App\Models\BoxUser;

class TargetCalculationService
{
    /**
     * Assigns a target template to a user, evaluating any salary multipliers.
     */
    public function assignToEmployee(TargetTemplate $template, BoxUser $employee, $departmentId = null)
    {
        $salarySnapshot = $employee->latestSalary ? $employee->latestSalary->amount : 0;
        $calculatedTarget = null;

        if ($template->calculation_method === 'Salary Based') {
            $calculatedTarget = $salarySnapshot * $template->salary_multiplier;
        }

        return TargetAssignment::updateOrCreate([
            'target_template_id' => $template->id,
            'box_user_id' => $employee->id,
            'department_id' => $departmentId,
        ], [
            'calculated_target' => $calculatedTarget,
            'salary_snapshot' => $salarySnapshot,
            'salary_multiplier' => $template->salary_multiplier,
            'status' => 'Active'
        ]);
    }

    public function assignToDepartment(TargetTemplate $template, $departmentId)
    {
        return TargetAssignment::updateOrCreate([
            'target_template_id' => $template->id,
            'box_user_id' => null, // Department level
            'department_id' => $departmentId,
        ], [
            'status' => 'Active'
        ]);
    }

    public function assignToTeam(TargetTemplate $template, $departmentId, $teamId)
    {
        return TargetAssignment::updateOrCreate([
            'target_template_id' => $template->id,
            'box_user_id' => null, // Team level
            'department_id' => $departmentId,
            'team_id' => $teamId,
        ], [
            'status' => 'Active'
        ]);
    }

    /**
     * Prefetch metric values in batch for a list of user IDs within a date range.
     * Returns a nested map: [$userId => [$metricName => $value]]
     */
    public function prefetchMetricsForUsers(array $userIds, $startDateStr = null, $endDateStr = null, $boxId = null)
    {
        if (empty($userIds)) {
            return [];
        }

        $isFilter = ($startDateStr && $endDateStr);
        if ($isFilter) {
            $startOfMonth = \Carbon\Carbon::parse($startDateStr)->startOfDay();
            $endOfMonth = \Carbon\Carbon::parse($endDateStr)->endOfDay();
        } else {
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();
        }

        $cacheKey = "target_metrics_" . ($boxId ?: 0) . "_" . md5(implode(',', $userIds) . '_' . ($startDateStr ?: 'cur') . '_' . ($endDateStr ?: 'cur'));

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function() use ($userIds, $startOfMonth, $endOfMonth, $boxId) {
            $metricsMap = [];
            foreach ($userIds as $uid) {
                $metricsMap[$uid] = [
                    'New Lead' => 0,
                    'No of task Closed manually' => 0,
                    'No of Matured' => 0,
                    'Amount of Matured' => 0,
                    'Sales Amount' => 0,
                    'New Customer' => 0,
                    'Customer Visit' => 0,
                    'No of Call Outgoing + Incoming' => 0,
                ];
            }

            // 1. New Lead
            $leadQ = \App\Models\Lead::whereIn('assigned_to', $userIds)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            if ($boxId) $leadQ->where('box_id', $boxId);
            $newLeads = $leadQ->groupBy('assigned_to')
                ->selectRaw('assigned_to, count(*) as cnt')
                ->pluck('cnt', 'assigned_to');
            foreach ($newLeads as $uid => $cnt) {
                if (isset($metricsMap[$uid])) $metricsMap[$uid]['New Lead'] = (int)$cnt;
            }

            // 2. No of task Closed manually
            $actQ = \App\Models\Activity::whereIn('assigned_to', $userIds)
                ->where('status', 'Close')
                ->whereBetween('completed_date', [$startOfMonth, $endOfMonth]);
            if ($boxId) $actQ->where('box_id', $boxId);
            $closedTasks = $actQ->groupBy('assigned_to')
                ->selectRaw('assigned_to, count(*) as cnt')
                ->pluck('cnt', 'assigned_to');
            foreach ($closedTasks as $uid => $cnt) {
                if (isset($metricsMap[$uid])) $metricsMap[$uid]['No of task Closed manually'] = (int)$cnt;
            }

            // 3 & 4. No of Matured, Amount of Matured & Sales Amount
            $maturedQ = \App\Models\Lead::whereIn('assigned_to', $userIds)
                ->where('is_matured', 'Yes')
                ->whereBetween('updated_at', [$startOfMonth, $endOfMonth]);
            if ($boxId) $maturedQ->where('box_id', $boxId);
            $maturedData = $maturedQ->groupBy('assigned_to')
                ->selectRaw('assigned_to, count(*) as cnt, coalesce(sum(amount), 0) as total_amount')
                ->get();
            foreach ($maturedData as $row) {
                if (isset($metricsMap[$row->assigned_to])) {
                    $metricsMap[$row->assigned_to]['No of Matured'] = (int)$row->cnt;
                    $metricsMap[$row->assigned_to]['Amount of Matured'] = (float)$row->total_amount;
                    $metricsMap[$row->assigned_to]['Sales Amount'] = (float)$row->total_amount;
                }
            }

            // 5. New Customer
            $custQ = \App\Models\Lead::whereIn('assigned_to', $userIds)
                ->where('status', 'Customer')
                ->whereBetween('updated_at', [$startOfMonth, $endOfMonth]);
            if ($boxId) $custQ->where('box_id', $boxId);
            $customers = $custQ->groupBy('assigned_to')
                ->selectRaw('assigned_to, count(*) as cnt')
                ->pluck('cnt', 'assigned_to');
            foreach ($customers as $uid => $cnt) {
                if (isset($metricsMap[$uid])) $metricsMap[$uid]['New Customer'] = (int)$cnt;
            }

            // 6. Customer Visit
            $visitQ = \App\Models\Activity::whereIn('assigned_to', $userIds)
                ->where('activity_type', 'Visit')
                ->where('status', 'Close')
                ->whereBetween('completed_date', [$startOfMonth, $endOfMonth]);
            if ($boxId) $visitQ->where('box_id', $boxId);
            $visits = $visitQ->groupBy('assigned_to')
                ->selectRaw('assigned_to, count(*) as cnt')
                ->pluck('cnt', 'assigned_to');
            foreach ($visits as $uid => $cnt) {
                if (isset($metricsMap[$uid])) $metricsMap[$uid]['Customer Visit'] = (int)$cnt;
            }

            // 7. No of Call Outgoing + Incoming
            $callQ = \App\Models\Activity::whereIn('assigned_to', $userIds)
                ->where('activity_type', 'like', '%Call%')
                ->whereBetween('activity_date', [$startOfMonth, $endOfMonth]);
            if ($boxId) $callQ->where('box_id', $boxId);
            $calls = $callQ->groupBy('assigned_to')
                ->selectRaw('assigned_to, count(*) as cnt')
                ->pluck('cnt', 'assigned_to');
            foreach ($calls as $uid => $cnt) {
                if (isset($metricsMap[$uid])) $metricsMap[$uid]['No of Call Outgoing + Incoming'] = (int)$cnt;
            }

            return $metricsMap;
        });
    }

    /**
     * Compute weighted score for an assignment in memory from pre-calculated metrics map.
     */
    public function calculateScoreFromMetrics(TargetAssignment $assignment, array $metricsMap)
    {
        $boxUser = $assignment->boxUser;
        if (!$boxUser || !$boxUser->user_id) return 0;
        
        $userId = $boxUser->user_id;
        $userMetrics = isset($metricsMap[$userId]) ? $metricsMap[$userId] : $metricsMap;
        $template = $assignment->template;
        if (!$template || !$template->metrics) return 0;

        $totalWeightedScore = 0;
        foreach ($template->metrics as $metric) {
            $actualValue = $userMetrics[$metric->metric_name] ?? 0;
            $targetValue = $metric->target_value;

            if ($template->calculation_method === 'Salary Based' && $metric->metric_name === 'Sales Amount') {
                $targetValue = $assignment->calculated_target;
            }

            if ($targetValue > 0) {
                $achievementPct = ($actualValue / $targetValue) * 100;
            } else {
                $achievementPct = $actualValue > 0 ? 100 : 0;
            }

            $weightedScore = ($achievementPct * $metric->weight) / 100;
            $totalWeightedScore += $weightedScore;
        }

        return $totalWeightedScore;
    }

    /**
     * Synchronize actuals in batch for an entire collection of assignments.
     */
    public function syncActualsBatch($assignments, $startDateStr = null, $endDateStr = null, $boxId = null)
    {
        if (empty($assignments) || $assignments->isEmpty()) {
            return;
        }

        $userIds = $assignments->pluck('boxUser.user_id')->filter()->unique()->values()->all();
        $metricsMap = $this->prefetchMetricsForUsers($userIds, $startDateStr, $endDateStr, $boxId);

        foreach ($assignments as $assignment) {
            $this->syncActuals($assignment, $startDateStr, $endDateStr, $metricsMap);
        }
    }

    /**
     * Synchronize actuals based on the live data (leads, tasks, etc.) for the current month.
     */
    public function syncActuals(TargetAssignment $assignment, $startDateStr = null, $endDateStr = null, $prefetchedMetrics = null)
    {
        $template = $assignment->template;
        $boxUser = $assignment->boxUser;
        
        if (!$boxUser || !$boxUser->user_id) return; // Only process for employee targets currently
        
        $userId = $boxUser->user_id;
        
        $isFilter = ($startDateStr && $endDateStr);

        if ($isFilter) {
            $startOfMonth = \Carbon\Carbon::parse($startDateStr)->startOfDay();
            $endOfMonth = \Carbon\Carbon::parse($endDateStr)->endOfDay();
        } else {
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();
        }
        
        $period = $startOfMonth->copy()->startOfMonth()->format('Y-m-d'); 

        $actualsCollection = collect();

        foreach ($template->metrics as $metric) {
            $actualValue = 0;

            if ($prefetchedMetrics !== null) {
                $actualValue = $prefetchedMetrics[$userId][$metric->metric_name] ?? 0;
            } else {
                if ($metric->metric_name === 'New Lead') {
                    $actualValue = \App\Models\Lead::where('assigned_to', $userId)
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->count();
                } elseif ($metric->metric_name === 'No of task Closed manually') {
                    $actualValue = \App\Models\Activity::where('assigned_to', $userId)
                        ->where('status', 'Close')
                        ->whereBetween('completed_date', [$startOfMonth, $endOfMonth])
                        ->count();
                } elseif ($metric->metric_name === 'No of Matured') {
                    $actualValue = \App\Models\Lead::where('assigned_to', $userId)
                        ->where('is_matured', 'Yes')
                        ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
                        ->count();
                } elseif ($metric->metric_name === 'Amount of Matured' || $metric->metric_name === 'Sales Amount') {
                    $actualValue = \App\Models\Lead::where('assigned_to', $userId)
                        ->where('is_matured', 'Yes')
                        ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
                        ->sum('amount');
                } elseif ($metric->metric_name === 'New Customer') {
                    $actualValue = \App\Models\Lead::where('assigned_to', $userId)
                        ->where('status', 'Customer')
                        ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
                        ->count();
                } elseif ($metric->metric_name === 'Customer Visit') {
                    $actualValue = \App\Models\Activity::where('assigned_to', $userId)
                        ->where('activity_type', 'Visit')
                        ->where('status', 'Close')
                        ->whereBetween('completed_date', [$startOfMonth, $endOfMonth])
                        ->count();
                } elseif ($metric->metric_name === 'No of Call Outgoing + Incoming') {
                    $actualValue = \App\Models\Activity::where('assigned_to', $userId)
                        ->where('activity_type', 'like', '%Call%')
                        ->whereBetween('activity_date', [$startOfMonth, $endOfMonth])
                        ->count();
                }
            }

            if ($isFilter) {
                // In memory only for filtered dashboards
                $actualsCollection->push(new \App\Models\TargetActual([
                    'target_assignment_id' => $assignment->id,
                    'metric_id' => $metric->id,
                    'period' => $period,
                    'actual_value' => $actualValue
                ]));
            } else {
                \App\Models\TargetActual::updateOrCreate([
                    'target_assignment_id' => $assignment->id,
                    'metric_id' => $metric->id,
                    'period' => $period
                ], [
                    'actual_value' => $actualValue
                ]);
            }
        }

        if ($isFilter) {
            $assignment->setRelation('actuals', $actualsCollection);
        }

        $this->calculatePerformance($assignment, $isFilter, $actualsCollection);
    }

    /**
     * Calculates the achievement and weighted score for a given target assignment.
     */
    public function calculatePerformance(TargetAssignment $assignment, $isFilter = false, $actualsCollection = null)
    {
        $template = $assignment->template;
        $metrics = $template->metrics;
        
        $totalWeightedScore = 0;
        $overallTargetValue = 0;
        $overallActualValue = 0;

        foreach ($metrics as $metric) {
            if ($isFilter && $actualsCollection) {
                $actualMatch = $actualsCollection->firstWhere('metric_id', $metric->id);
                $actualSum = $actualMatch ? $actualMatch->actual_value : 0;
            } else {
                $actualSum = $assignment->actuals()->where('metric_id', $metric->id)->sum('actual_value');
            }
            
            $targetValue = $metric->target_value;

            // If salary based and this is the primary metric (e.g. Sales Amount), override target with calculated
            if ($template->calculation_method === 'Salary Based' && $metric->metric_name === 'Sales Amount') {
                $targetValue = $assignment->calculated_target;
            }

            if ($targetValue > 0) {
                $achievementPct = ($actualSum / $targetValue) * 100;
            } else {
                $achievementPct = $actualSum > 0 ? 100 : 0;
            }

            $weightedScore = ($achievementPct * $metric->weight) / 100;
            $totalWeightedScore += $weightedScore;

            $overallTargetValue += $targetValue;
            $overallActualValue += $actualSum;
        }

        $status = $this->determineStatus($totalWeightedScore);

        if ($isFilter) {
            $perfObj = new PerformanceResult([
                'target_assignment_id' => $assignment->id,
                'box_user_id' => $assignment->box_user_id,
                'department_id' => $assignment->department_id,
                'team_id' => $assignment->team_id,
                'target_value' => $overallTargetValue,
                'actual_value' => $overallActualValue,
                'achievement_percentage' => $totalWeightedScore,
                'weighted_score' => $totalWeightedScore,
                'performance_status' => $status,
                'calculated_at' => now(),
            ]);
            $assignment->setRelation('performance', $perfObj);
            return $perfObj;
        }

        return PerformanceResult::updateOrCreate([
            'target_assignment_id' => $assignment->id,
            'box_user_id' => $assignment->box_user_id,
            'department_id' => $assignment->department_id,
        ], [
            'target_value' => $overallTargetValue,
            'actual_value' => $overallActualValue,
            'achievement_percentage' => $totalWeightedScore, // Overall achievement is the sum of weighted scores
            'weighted_score' => $totalWeightedScore,
            'performance_status' => $status,
            'calculated_at' => now(),
        ]);
    }

    public function determineStatus($score)
    {
        if ($score >= 110) return 'Outstanding';
        if ($score >= 100) return 'Excellent';
        if ($score >= 90) return 'Good';
        if ($score >= 75) return 'Average';
        if ($score >= 60) return 'Needs Improvement';
        return 'Poor';
    }
}
