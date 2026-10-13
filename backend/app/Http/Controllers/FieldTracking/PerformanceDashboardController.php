<?php

namespace App\Http\Controllers\FieldTracking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Box;
use App\Models\BoxUser;
use App\Models\Department;
use App\Models\Team;
use App\Models\TargetAssignment;
use App\Models\PerformanceResult;
use Illuminate\Support\Facades\Auth;

class PerformanceDashboardController extends Controller
{
    public function employeeDashboard($boxSlug)
    {
        return view('fieldstracking.employee-dashboard');
    }

    public function performanceDashboard($boxSlug)
    {
        return view('fieldstracking.performance-dashboard');
    }

    private function getBoxUser($boxId, $userId)
    {
        return BoxUser::where('box_id', $boxId)->where('user_id', $userId)->first();
    }

    public function getDepartments(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json([]);

        $boxUser = $this->getBoxUser($box->id, Auth::id());
        if (!$boxUser) return response()->json([]);

        $query = Department::where('box_id', $box->id);

        $isGlobalAdmin = Auth::user() && Auth::user()->hasRole(['admin', 'superadmin'], 'web');
        
        if ($isGlobalAdmin || $boxUser->hasRole('admin')) {
            // Admin sees all departments
        } elseif ($boxUser->hasRole('manager')) {
            $query->where('id', $boxUser->department);
        } elseif ($boxUser->hasRole('team_leader')) {
            $query->where('id', $boxUser->department);
        } else {
            // Employee sees their own department
            $query->where('id', $boxUser->department);
        }

        return response()->json($query->get());
    }

    public function getTeams(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json([]);

        $boxUser = $this->getBoxUser($box->id, Auth::id());
        if (!$boxUser) return response()->json([]);

        $query = Team::where('box_id', $box->id);
        
        if ($request->has('department_id') && !empty($request->department_id)) {
            $query->where('department_id', $request->department_id);
        }

        $isGlobalAdmin = Auth::user() && Auth::user()->hasRole(['admin', 'superadmin'], 'web');
        
        if ($isGlobalAdmin || $boxUser->hasRole('admin')) {
            // Admin sees all teams (filtered by department if provided)
        } elseif ($boxUser->hasRole('manager')) {
            $query->where('department_id', $boxUser->department);
        } elseif ($boxUser->hasRole('team_leader')) {
            $query->where('id', $boxUser->designation); // Assuming designation is team_id
        } else {
            $query->where('id', $boxUser->designation);
        }

        return response()->json($query->get());
    }

    public function getEmployees(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json([]);

        $boxUser = $this->getBoxUser($box->id, Auth::id());
        if (!$boxUser) return response()->json([]);

        $query = BoxUser::with('user')->where('box_id', $box->id);

        if ($request->has('team_id') && !empty($request->team_id)) {
            $query->where('designation', $request->team_id);
        } elseif ($request->has('department_id') && !empty($request->department_id)) {
            $query->where('department', $request->department_id);
        }

        $isGlobalAdmin = Auth::user() && Auth::user()->hasRole(['admin', 'superadmin'], 'web');
        
        if ($isGlobalAdmin || $boxUser->hasRole('admin')) {
            // Admin sees all
        } elseif ($boxUser->hasRole('manager')) {
            $query->where('department', $boxUser->department);
        } elseif ($boxUser->hasRole('team_leader')) {
            $query->where('designation', $boxUser->designation);
        } else {
            // Employee sees only themselves
            $query->where('id', $boxUser->id);
        }

        return response()->json($query->get());
    }

    public function getPerformance(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json([]);

        $boxUser = $this->getBoxUser($box->id, Auth::id());
        if (!$boxUser) return response()->json([]);

        $query = \App\Models\TargetAssignment::with(['template.metrics', 'actuals', 'performance', 'boxUser.user', 'boxUser.departmentRelation', 'boxUser.designationRelation'])
            ->whereHas('template', function($q) use ($box) {
                $q->where('box_id', $box->id);
            });

        // Filter by user selection
        if ($request->has('employee_id') && !empty($request->employee_id)) {
            $query->where('box_user_id', $request->employee_id);
        } elseif ($request->has('team_id') && !empty($request->team_id)) {
            $query->where('team_id', $request->team_id)
                  ->orWhereHas('boxUser', function($q) use ($request) {
                      $q->where('designation', $request->team_id);
                  });
        } elseif ($request->has('department_id') && !empty($request->department_id)) {
            $query->where('department_id', $request->department_id)
                  ->orWhereHas('boxUser', function($q) use ($request) {
                      $q->where('department', $request->department_id);
                  });
        }

        $isGlobalAdmin = Auth::user() && Auth::user()->hasRole(['admin', 'superadmin'], 'web');
        
        // Apply RBAC filters
        if ($request->has('my_performance')) {
            $query->where('box_user_id', $boxUser->id);
        } elseif ($isGlobalAdmin || $boxUser->hasRole('admin')) {
            // No additional filter
        } elseif ($boxUser->hasRole('manager')) {
            $query->where(function($q) use ($boxUser) {
                $q->where('department_id', $boxUser->department)
                  ->orWhereHas('boxUser', function($bQ) use ($boxUser) {
                      $bQ->where('department', $boxUser->department);
                  });
            });
        } elseif ($boxUser->hasRole('team_leader')) {
            $query->where(function($q) use ($boxUser) {
                $q->where('team_id', $boxUser->designation)
                  ->orWhereHas('boxUser', function($bQ) use ($boxUser) {
                      $bQ->where('designation', $boxUser->designation);
                  });
            });
        } else {
            $query->where('box_user_id', $boxUser->id);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $assignments = $query->orderBy('id', 'desc')->get();
        
        $service = new \App\Services\TargetCalculationService();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $service->syncActualsBatch($assignments, $startDate, $endDate, $box->id);

        // Calculate aggregate stats
        $totalTargets = $assignments->count();
        $completedTargets = $assignments->where('status', 'Completed')->count();
        $pendingTargets = $assignments->where('status', '!=', 'Completed')->count();
        
        $totalAchievement = 0;
        $targetsWithScores = 0;
        // Get all eligible employees first to ensure everyone is listed
        $userQuery = BoxUser::with('user')->where('box_id', $box->id);

        if ($request->has('employee_id') && !empty($request->employee_id)) {
            $userQuery->where('id', $request->employee_id);
        } elseif ($request->has('team_id') && !empty($request->team_id)) {
            $userQuery->where('designation', $request->team_id);
        } elseif ($request->has('department_id') && !empty($request->department_id)) {
            $userQuery->where('department', $request->department_id);
        }

        if ($isGlobalAdmin || $boxUser->hasRole('admin')) {
            // All users
        } elseif ($boxUser->hasRole('manager')) {
            $userQuery->where('department', $boxUser->department);
        } elseif ($boxUser->hasRole('team_leader')) {
            $userQuery->where('designation', $boxUser->designation);
        } else {
            $userQuery->where('id', $boxUser->id);
        }

        $allEligibleUsers = $userQuery->get();
        
        $employeeScores = [];
        foreach ($allEligibleUsers as $eu) {
            $name = trim($eu->first_name . ' ' . $eu->last_name);
            if (!$name && $eu->user) $name = $eu->user->name;
            $employeeScores[$eu->id] = [
                'id' => $eu->id,
                'name' => $name ?: 'Unknown',
                'department' => $eu->department ?: 'N/A',
                'designation' => $eu->designation ?: 'N/A',
                'total_score' => 0,
                'count' => 0,
                'assigned_templates' => []
            ];
        }

        // Teams Logic
        $teamQuery = \App\Models\Team::where('box_id', $box->id);
        if ($request->has('department_id') && !empty($request->department_id)) {
            $teamQuery->where('department_id', $request->department_id);
        }
        $allEligibleTeams = $teamQuery->get();

        $teamScores = [];
        foreach ($allEligibleTeams as $tm) {
            $teamScores[$tm->id] = [
                'name' => $tm->name ?: 'Unknown Team',
                'total_score' => 0,
                'count' => 0,
                'assigned_templates' => []
            ];
        }

        foreach ($assignments as $assignment) {
            if ($assignment->box_user_id) {
                $empId = $assignment->box_user_id;
                
                if (!isset($employeeScores[$empId])) {
                    $name = $assignment->boxUser->first_name . ' ' . $assignment->boxUser->last_name;
                    if (!trim($name) && $assignment->boxUser->user) {
                        $name = $assignment->boxUser->user->name;
                    }
                    $employeeScores[$empId] = [
                        'id' => $empId,
                        'name' => $name,
                        'department' => $assignment->boxUser->department ?: 'N/A',
                        'designation' => $assignment->boxUser->designation ?: 'N/A',
                        'total_score' => 0,
                        'count' => 0,
                        'assigned_templates' => []
                    ];
                }

                $templateName = $assignment->template ? $assignment->template->name : 'Unknown Template';
                $employeeScores[$empId]['assigned_templates'][] = [
                    'template' => $templateName,
                    'status' => $assignment->status,
                    'score' => $assignment->performance ? round($assignment->performance->achievement_percentage, 2) : null
                ];

                if ($assignment->performance) {
                    $score = (float) $assignment->performance->achievement_percentage;
                    $totalAchievement += $score;
                    $targetsWithScores++;

                    $employeeScores[$empId]['total_score'] += $score;
                    $employeeScores[$empId]['count']++;
                }
            } elseif ($assignment->team_id) {
                // It's a Team Target
                $tmId = $assignment->team_id;
                
                if (!isset($teamScores[$tmId])) {
                    // Try to fetch team name directly if not loaded
                    $teamModel = \App\Models\Team::find($tmId);
                    $teamScores[$tmId] = [
                        'name' => $teamModel ? $teamModel->name : 'Unknown Team',
                        'total_score' => 0,
                        'count' => 0,
                        'assigned_templates' => []
                    ];
                }

                $templateName = $assignment->template ? $assignment->template->name : 'Unknown Template';
                $teamScores[$tmId]['assigned_templates'][] = [
                    'template' => $templateName,
                    'status' => $assignment->status,
                    'score' => $assignment->performance ? round($assignment->performance->achievement_percentage, 2) : null
                ];

                if ($assignment->performance) {
                    $score = (float) $assignment->performance->achievement_percentage;
                    $totalAchievement += $score;
                    $targetsWithScores++;

                    $teamScores[$tmId]['total_score'] += $score;
                    $teamScores[$tmId]['count']++;
                }
            }
        }

        $avgAchievement = $targetsWithScores > 0 ? $totalAchievement / $targetsWithScores : 0;

        foreach ($employeeScores as &$emp) {
            $emp['avg_score'] = $emp['count'] > 0 ? $emp['total_score'] / $emp['count'] : 0;
        }

        foreach ($teamScores as &$tm) {
            $tm['avg_score'] = $tm['count'] > 0 ? $tm['total_score'] / $tm['count'] : 0;
        }

        // Convert to array of values for sorting, and filter out those with 0 assignments
        $employeeScores = array_values(array_filter($employeeScores, function($emp) {
            return count($emp['assigned_templates']) > 0;
        }));

        $teamScores = array_values(array_filter($teamScores, function($tm) {
            return count($tm['assigned_templates']) > 0;
        }));

        usort($employeeScores, function($a, $b) {
            return $b['avg_score'] <=> $a['avg_score'];
        });

        $topPerformers = array_slice($employeeScores, 0, 5);
        $lowPerformers = array_slice(array_reverse($employeeScores), 0, 5);

        $myRank = null;
        $teamSize = null;
        if ($request->has('my_performance')) {
            if ($boxUser->designation) {
                $teamUsers = \App\Models\BoxUser::where('box_id', $box->id)->where('designation', $boxUser->designation)->pluck('id');
                $teamSize = $teamUsers->count();
                
                $teamAssignments = \App\Models\TargetAssignment::with(['template.metrics', 'actuals', 'performance', 'boxUser'])
                    ->whereIn('box_user_id', $teamUsers)
                    ->get();
                
                $service->syncActualsBatch($teamAssignments, $startDate, $endDate, $box->id);

                $teamScoresList = [];
                foreach ($teamAssignments as $a) {
                    $score = $a->performance ? (float) $a->performance->achievement_percentage : 0;
                    if (!isset($teamScoresList[$a->box_user_id])) {
                        $teamScoresList[$a->box_user_id] = ['total' => 0, 'count' => 0];
                    }
                    $teamScoresList[$a->box_user_id]['total'] += $score;
                    $teamScoresList[$a->box_user_id]['count']++;
                }

                $avgScores = [];
                foreach ($teamUsers as $uid) {
                    if (isset($teamScoresList[$uid]) && $teamScoresList[$uid]['count'] > 0) {
                        $avgScores[$uid] = $teamScoresList[$uid]['total'] / $teamScoresList[$uid]['count'];
                    } else {
                        $avgScores[$uid] = 0;
                    }
                }
                
                arsort($avgScores);
                
                $rank = 1;
                $topTeamPerformer = null;
                $firstLoop = true;

                foreach ($avgScores as $uid => $score) {
                    if ($firstLoop) {
                        $topUser = \App\Models\BoxUser::with('user')->find($uid);
                        if ($topUser) {
                            $name = trim($topUser->first_name . ' ' . $topUser->last_name);
                            if (!$name && $topUser->user) $name = $topUser->user->name;
                            $topTeamPerformer = $name ?: 'Unknown';
                        }
                        $firstLoop = false;
                    }
                    if ($uid == $boxUser->id) {
                        $myRank = $rank;
                    }
                    $rank++;
                }
                // Calculate Last Month Top Performer
                $lmStart = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $lmEnd = now()->subMonth()->endOfMonth()->format('Y-m-d');
                $lmUserIds = $teamAssignments->pluck('boxUser.user_id')->filter()->unique()->values()->all();
                $lmMetricsMap = $service->prefetchMetricsForUsers($lmUserIds, $lmStart, $lmEnd, $box->id);
                $lmScores = [];
                
                foreach ($teamAssignments as $a) {
                    if (!$a->box_user_id) continue;
                    $score = $service->calculateScoreFromMetrics($a, $lmMetricsMap);
                    if (!isset($lmScores[$a->box_user_id])) {
                        $lmScores[$a->box_user_id] = ['total' => 0, 'count' => 0];
                    }
                    $lmScores[$a->box_user_id]['total'] += $score;
                    $lmScores[$a->box_user_id]['count']++;
                }

                $lmAvgScores = [];
                foreach ($teamUsers as $uid) {
                    if (isset($lmScores[$uid]) && $lmScores[$uid]['count'] > 0) {
                        $lmAvgScores[$uid] = $lmScores[$uid]['total'] / $lmScores[$uid]['count'];
                    }
                }
                
                arsort($lmAvgScores);
                $lastMonthPerformer = null;
                if (!empty($lmAvgScores)) {
                    $lmTopId = array_key_first($lmAvgScores);
                    $topUser = \App\Models\BoxUser::with('user')->find($lmTopId);
                    if ($topUser) {
                        $name = trim($topUser->first_name . ' ' . $topUser->last_name);
                        if (!$name && $topUser->user) $name = $topUser->user->name;
                        $lastMonthPerformer = $name ?: 'Unknown';
                    }
                }
            }
        } else {
            // Global last month top performer (for performance-dashboard)
            $lmStart = now()->subMonth()->startOfMonth()->format('Y-m-d');
            $lmEnd = now()->subMonth()->endOfMonth()->format('Y-m-d');
            $lmUserIds = $assignments->pluck('boxUser.user_id')->filter()->unique()->values()->all();
            $lmMetricsMap = $service->prefetchMetricsForUsers($lmUserIds, $lmStart, $lmEnd, $box->id);
            $lmScores = [];
            
            foreach ($assignments as $a) {
                if (!$a->box_user_id) continue;
                $score = $service->calculateScoreFromMetrics($a, $lmMetricsMap);
                if (!isset($lmScores[$a->box_user_id])) {
                    $lmScores[$a->box_user_id] = ['total' => 0, 'count' => 0];
                }
                $lmScores[$a->box_user_id]['total'] += $score;
                $lmScores[$a->box_user_id]['count']++;
            }
            
            $lmAvgScores = [];
            foreach ($lmScores as $uid => $data) {
                if ($data['count'] > 0) {
                    $lmAvgScores[$uid] = $data['total'] / $data['count'];
                }
            }
            
            arsort($lmAvgScores);
            if (!empty($lmAvgScores)) {
                $lmTopId = array_key_first($lmAvgScores);
                $topUser = \App\Models\BoxUser::with('user')->find($lmTopId);
                if ($topUser) {
                    $name = trim($topUser->first_name . ' ' . $topUser->last_name);
                    if (!$name && $topUser->user) $name = $topUser->user->name;
                    $lastMonthPerformer = $name ?: 'Unknown';
                }
            }
        }

        return response()->json([
            'assignments' => $assignments,
            'stats' => [
                'total_targets' => $totalTargets,
                'completed_targets' => $completedTargets,
                'pending_targets' => $pendingTargets,
                'overall_achievement' => round($avgAchievement, 2),
                'top_performers' => $topPerformers,
                'low_performers' => $lowPerformers,
                'all_performers' => $employeeScores,
                'team_performers' => $teamScores,
                'my_rank' => $myRank,
                'team_size' => $teamSize,
                'top_team_performer' => $topTeamPerformer ?? null,
                'last_month_performer' => $lastMonthPerformer ?? null
            ]
        ]);
    }
}
