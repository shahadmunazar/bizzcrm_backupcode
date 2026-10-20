<?php

namespace App\Http\Controllers\FieldTracking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TargetTemplate;
use App\Models\TargetAssignment;
use App\Models\BoxUser;

class TargetController extends Controller
{
    public function targetTemplate($boxSlug) { 
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box) abort(404);
        $boxId = $box->id;
        
        $templates = TargetTemplate::with('metrics')->where('box_id', $boxId)->orderBy('id', 'desc')->get();
        $employees = BoxUser::where('box_id', $boxId)->get();
        return view('fieldstracking.target-template', compact('templates', 'employees', 'boxSlug')); 
    }
    
    public function storeTemplate(Request $request, $boxSlug)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Invalid box'], 404);
        $boxId = $box->id;
        
        $template = TargetTemplate::create([
            'box_id' => $boxId,
            'name' => $request->name,
            'department_id' => $request->department_id,
            'target_type' => 'Generic', // Templates are now generic, type is decided at assignment
            'calculation_method' => $request->calculation_method, // e.g. Fixed Target or Salary Based
            'frequency' => $request->frequency,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'salary_multiplier' => $request->salary_multiplier,
            'created_by' => auth()->id() ?? 1,
        ]);

        if ($request->has('metrics')) {
            foreach ($request->metrics as $metric) {
                $template->metrics()->create([
                    'metric_type' => $metric['type'],
                    'metric_name' => $metric['name'],
                    'target_value' => $metric['target_value'] ?? 0,
                    'unit' => $metric['unit'] ?? null,
                    'weight' => $metric['weight'] ?? 0,
                ]);
            }
        }
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Enterprise Target Template created successfully.']);
        }
        return redirect()->back()->with('success', 'Enterprise Target Template created successfully.');
    }

    public function getTemplate(Request $request, $boxSlug, $id)
    {
        $template = TargetTemplate::with('metrics')->find($id);
        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Template not found'], 404);
        }
        return response()->json(['success' => true, 'template' => $template]);
    }

    public function updateTemplate(Request $request, $boxSlug, $id)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Invalid box'], 404);

        $template = TargetTemplate::where('box_id', $box->id)->find($id);
        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Template not found'], 404);
        }

        $template->update([
            'name' => $request->name,
            'department_id' => $request->department_id,
            'calculation_method' => $request->calculation_method,
            'frequency' => $request->frequency,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'salary_multiplier' => $request->salary_multiplier,
        ]);

        if ($request->has('metrics')) {
            $template->metrics()->delete(); // clear old metrics
            foreach ($request->metrics as $metric) {
                $template->metrics()->create([
                    'metric_type' => $metric['type'],
                    'metric_name' => $metric['name'],
                    'target_value' => $metric['target_value'] ?? 0,
                    'unit' => $metric['unit'] ?? null,
                    'weight' => $metric['weight'] ?? 0,
                ]);
            }
        }
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Target Template updated successfully.']);
        }
        return redirect()->back()->with('success', 'Target Template updated successfully.');
    }

    public function deleteTemplate(Request $request, $boxSlug, $id)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Invalid box'], 404);

        $template = TargetTemplate::where('box_id', $box->id)->find($id);
        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Template not found'], 404);
        }

        // Optional: delete related metrics/assignments if cascading delete isn't set up
        $template->metrics()->delete();
        $template->assignments()->delete();
        
        $template->delete();

        return response()->json(['success' => true, 'message' => 'Template deleted successfully.']);
    }

    public function toggleStatus(Request $request, $boxSlug, $id)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Invalid box'], 404);

        $template = TargetTemplate::where('box_id', $box->id)->find($id);
        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Template not found'], 404);
        }

        if ($request->has('status') && !empty($request->status)) {
            $newStatus = in_array(strtolower((string)$request->status), ['1', 'true', 'active']) ? 'Active' : 'Inactive';
        } else {
            $newStatus = ($template->status === 'Inactive') ? 'Active' : 'Inactive';
        }

        $template->status = $newStatus;
        $template->save();

        return response()->json([
            'success' => true,
            'message' => "Template marked as {$newStatus}.",
            'status' => $newStatus,
            'is_active' => ($newStatus === 'Active')
        ]);
    }

    public function storeAssignment(Request $request, $boxSlug)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Invalid box'], 404);
        
        $template = TargetTemplate::findOrFail($request->target_template_id);
        $service = new \App\Services\TargetCalculationService();
        
        if ($request->target_type === 'Employee Target') {
            if ($request->has('employee_ids')) {
                foreach($request->employee_ids as $employeeId) {
                    $employee = BoxUser::find($employeeId);
                    if ($employee) {
                        $service->assignToEmployee($template, $employee, $request->department_id);
                    }
                }
            }
        } elseif ($request->target_type === 'All Employees') {
            $allEmployees = BoxUser::where('box_id', $box->id)->get();

            foreach($allEmployees as $employee) {
                $service->assignToEmployee($template, $employee, $employee->department);
            }
        } elseif ($request->target_type === 'Department Target') {
            $service->assignToDepartment($template, $request->department_id);
        } elseif ($request->target_type === 'Team Target') {
            if ($request->has('team_ids')) {
                foreach($request->team_ids as $teamId) {
                    $service->assignToTeam($template, $request->department_id, $teamId);
                }
            }
        }
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Targets assigned successfully.']);
        }
        return redirect()->back()->with('success', 'Targets assigned successfully.');
    }

    private function getAvailableTargetMonths($boxId)
    {
        $templateStartDate = TargetTemplate::where('box_id', $boxId)->min('start_date') ?: '2026-01-01';
        $startMonth = \Carbon\Carbon::parse($templateStartDate)->startOfMonth();
        $endMonth = now()->startOfMonth();

        $availableMonths = [
            [
                'key' => 'all',
                'label' => 'All Months',
                'is_current' => false,
                'start_date' => $startMonth->toDateString(),
                'end_date' => now()->endOfMonth()->toDateString(),
            ]
        ];

        $cur = $endMonth->copy();
        while ($cur->greaterThanOrEqualTo($startMonth)) {
            $availableMonths[] = [
                'key' => $cur->format('Y-m'),
                'label' => $cur->format('F Y'),
                'is_current' => ($cur->format('Y-m') === now()->format('Y-m')),
                'start_date' => $cur->copy()->startOfMonth()->toDateString(),
                'end_date' => $cur->copy()->endOfMonth()->toDateString(),
            ];
            $cur->subMonth();
        }
        return $availableMonths;
    }

    private function buildMultiMonthAssignments($assignments, $availableMonths, $boxId)
    {
        if ($assignments->isEmpty()) return collect();

        $monthItems = array_values(array_filter($availableMonths, function($m) {
            return $m['key'] !== 'all';
        }));

        if (empty($monthItems)) return $assignments;

        $service = new \App\Services\TargetCalculationService();
        $userIds = $assignments->pluck('boxUser.user_id')->filter()->unique()->values()->all();

        $allStart = $monthItems[count($monthItems) - 1]['start_date'] . ' 00:00:00';
        $allEnd = $monthItems[0]['end_date'] . ' 23:59:59';

        // 1. Leads
        $leads = \App\Models\Lead::whereIn('assigned_to', $userIds)
            ->where('box_id', $boxId)
            ->whereBetween('created_at', [$allStart, $allEnd])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as m, assigned_to, count(*) as cnt')
            ->groupBy('m', 'assigned_to')->get();

        // 2. Closed Tasks
        $closedTasks = \App\Models\Activity::whereIn('assigned_to', $userIds)
            ->where('box_id', $boxId)
            ->where('status', 'Close')
            ->whereBetween('completed_date', [$allStart, $allEnd])
            ->selectRaw('DATE_FORMAT(completed_date, "%Y-%m") as m, assigned_to, count(*) as cnt')
            ->groupBy('m', 'assigned_to')->get();

        // 3. Matured
        $matured = \App\Models\Lead::whereIn('assigned_to', $userIds)
            ->where('box_id', $boxId)
            ->where('is_matured', 'Yes')
            ->whereBetween('updated_at', [$allStart, $allEnd])
            ->selectRaw('DATE_FORMAT(updated_at, "%Y-%m") as m, assigned_to, count(*) as cnt, sum(amount) as total_amount')
            ->groupBy('m', 'assigned_to')->get();

        // 4. Customers
        $customers = \App\Models\Lead::whereIn('assigned_to', $userIds)
            ->where('box_id', $boxId)
            ->where('status', 'Customer')
            ->whereBetween('updated_at', [$allStart, $allEnd])
            ->selectRaw('DATE_FORMAT(updated_at, "%Y-%m") as m, assigned_to, count(*) as cnt')
            ->groupBy('m', 'assigned_to')->get();

        // 5. Visits
        $visits = \App\Models\Activity::whereIn('assigned_to', $userIds)
            ->where('box_id', $boxId)
            ->where('activity_type', 'Visit')
            ->where('status', 'Close')
            ->whereBetween('completed_date', [$allStart, $allEnd])
            ->selectRaw('DATE_FORMAT(completed_date, "%Y-%m") as m, assigned_to, count(*) as cnt')
            ->groupBy('m', 'assigned_to')->get();

        // 6. Calls
        $calls = \App\Models\Activity::whereIn('assigned_to', $userIds)
            ->where('box_id', $boxId)
            ->where('activity_type', 'like', '%Call%')
            ->whereBetween('activity_date', [$allStart, $allEnd])
            ->selectRaw('DATE_FORMAT(activity_date, "%Y-%m") as m, assigned_to, count(*) as cnt')
            ->groupBy('m', 'assigned_to')->get();

        $monthlyMap = [];
        foreach ($monthItems as $m) {
            $mKey = $m['key'];
            $monthlyMap[$mKey] = [];
            foreach ($userIds as $uid) {
                $monthlyMap[$mKey][$uid] = [
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
        }

        foreach ($leads as $r) { if (isset($monthlyMap[$r->m][$r->assigned_to])) $monthlyMap[$r->m][$r->assigned_to]['New Lead'] = (int)$r->cnt; }
        foreach ($closedTasks as $r) { if (isset($monthlyMap[$r->m][$r->assigned_to])) $monthlyMap[$r->m][$r->assigned_to]['No of task Closed manually'] = (int)$r->cnt; }
        foreach ($matured as $r) {
            if (isset($monthlyMap[$r->m][$r->assigned_to])) {
                $monthlyMap[$r->m][$r->assigned_to]['No of Matured'] = (int)$r->cnt;
                $monthlyMap[$r->m][$r->assigned_to]['Amount of Matured'] = (float)$r->total_amount;
                $monthlyMap[$r->m][$r->assigned_to]['Sales Amount'] = (float)$r->total_amount;
            }
        }
        foreach ($customers as $r) { if (isset($monthlyMap[$r->m][$r->assigned_to])) $monthlyMap[$r->m][$r->assigned_to]['New Customer'] = (int)$r->cnt; }
        foreach ($visits as $r) { if (isset($monthlyMap[$r->m][$r->assigned_to])) $monthlyMap[$r->m][$r->assigned_to]['Customer Visit'] = (int)$r->cnt; }
        foreach ($calls as $r) { if (isset($monthlyMap[$r->m][$r->assigned_to])) $monthlyMap[$r->m][$r->assigned_to]['No of Call Outgoing + Incoming'] = (int)$r->cnt; }

        $result = collect();
        foreach ($monthItems as $m) {
            $mKey = $m['key'];
            $mLabel = $m['label'];
            $metricsForThisMonth = $monthlyMap[$mKey] ?? [];

            foreach ($assignments as $a) {
                $score = $service->calculateScoreFromMetrics($a, $metricsForThisMonth);
                $perfStatus = $service->determineStatus($score);

                $actualsList = [];
                $userId = $a->boxUser ? $a->boxUser->user_id : null;
                $userMetrics = $metricsForThisMonth[$userId] ?? [];

                if ($a->template && $a->template->metrics) {
                    foreach ($a->template->metrics as $metric) {
                        $actualsList[] = (object)[
                            'metric_id' => $metric->id,
                            'actual_value' => (float)($userMetrics[$metric->metric_name] ?? 0),
                            'metric_name' => $metric->metric_name,
                            'target_value' => (float)$metric->target_value,
                            'metric_type' => $metric->metric_type,
                        ];
                    }
                }

                $carbonDt = \Carbon\Carbon::parse($mKey . '-01');
                $item = clone $a;
                $item->month_key = $mKey;
                $item->month_label = $mLabel;
                $item->month_name = $carbonDt->format('F');
                $item->year = $carbonDt->format('Y');
                $item->performance = (object)[
                    'achievement_percentage' => round($score, 1),
                    'performance_status' => $perfStatus
                ];
                $item->actuals = collect($actualsList);
                $result->push($item);
            }
        }

        return $result;
    }

    public function myTarget($boxSlug)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box) abort(404);

        $user = auth()->user();
        $boxUser = $user ? \App\Models\BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first() : null;
        $isAdmin = $boxUser && ($boxUser->staff_role === 'admin' || $user->id == $box->superadmin_id);

        $availableMonths = $this->getAvailableTargetMonths($box->id);
        $selectedMonth = now()->format('Y-m');

        $assignments = collect();
        $employees = $isAdmin ? BoxUser::with('user')->where('box_id', $box->id)->get() : collect();

        $topPerformer = null;
        $perfStats = null;

        return view('fieldstracking.my-target', compact('assignments', 'boxSlug', 'boxUser', 'isAdmin', 'employees', 'availableMonths', 'selectedMonth', 'topPerformer', 'perfStats'));
    }

    public function getMyAssignments(Request $request, $boxSlug)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        $user = auth()->user();
        if (!$box || !$user) return response()->json(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);

        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
        if (!$boxUser) return response()->json(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);

        $isAdmin = ($boxUser->staff_role === 'admin' || $user->id == $box->superadmin_id);

        $query = \App\Models\TargetAssignment::with(['template.metrics', 'actuals', 'performance', 'boxUser.user'])
            ->whereHas('template', function($q) use ($box) {
                $q->where('box_id', $box->id);
            });

        if ($isAdmin) {
            // Admin can view all targets, or filter by specific employee
            if ($request->has('employee_id') && !empty($request->employee_id) && $request->employee_id !== 'all') {
                $query->where('box_user_id', $request->employee_id);
            }
        } else {
            // Regular employee only sees their own assigned targets
            $query->where('box_user_id', $boxUser->id);
        }

        if ($request->has('frequency') && !empty($request->frequency) && $request->frequency !== 'all') {
            if (is_array($request->frequency)) {
                $query->whereHas('template', function($tQ) use ($request) {
                    $tQ->whereIn('frequency', $request->frequency);
                });
            } else {
                $query->whereHas('template', function($tQ) use ($request) {
                    $tQ->where('frequency', $request->frequency);
                });
            }
        }

        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            if (is_array($request->status)) {
                $query->whereIn('status', $request->status);
            } else {
                $query->where('status', $request->status);
            }
        }

        $search = $request->input('search_query');
        if (empty($search) && $request->has('search')) {
            $rawSearch = $request->input('search');
            $search = is_array($rawSearch) ? ($rawSearch['value'] ?? '') : $rawSearch;
        }
        $search = is_string($search) ? trim($search) : '';

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->whereHas('template', function($tQ) use ($search) {
                    $tQ->where('name', 'like', "%{$search}%")
                       ->orWhere('frequency', 'like', "%{$search}%");
                })
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhereHas('boxUser.user', function($uQ) use ($search) {
                    $uQ->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $sort = $request->input('custom_sort', $request->input('sort'));
        if ($sort === 'name_asc') {
            $query->join('target_templates', 'target_assignments.target_template_id', '=', 'target_templates.id')
                  ->orderBy('target_templates.name', 'asc')
                  ->select('target_assignments.*');
        } elseif ($sort === 'name_desc') {
            $query->join('target_templates', 'target_assignments.target_template_id', '=', 'target_templates.id')
                  ->orderBy('target_templates.name', 'desc')
                  ->select('target_assignments.*');
        } else {
            $query->orderBy('target_assignments.id', 'desc');
        }

        return $this->processTargetAssignmentsDataTable($query, $request, $box, $isAdmin);
    }

    public function getAssignmentHistory(Request $request, $boxSlug, $id)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Invalid box'], 404);

        $assignment = TargetAssignment::with(['template.metrics', 'boxUser.user'])->find($id);
        if (!$assignment) return response()->json(['success' => false, 'message' => 'Assignment not found'], 404);

        $templateStartDate = $assignment->template ? ($assignment->template->start_date ?: '2026-01-01') : '2026-01-01';
        $startMonth = \Carbon\Carbon::parse($templateStartDate)->startOfMonth();
        $endMonth = now()->startOfMonth();

        $months = [];
        $cur = $endMonth->copy();
        while ($cur->greaterThanOrEqualTo($startMonth)) {
            $months[] = [
                'key' => $cur->format('Y-m'),
                'label' => $cur->format('F Y'),
                'start_date' => $cur->copy()->startOfMonth()->toDateString(),
                'end_date' => $cur->copy()->endOfMonth()->toDateString(),
            ];
            $cur->subMonth();
        }

        $service = new \App\Services\TargetCalculationService();
        $userId = $assignment->boxUser ? $assignment->boxUser->user_id : null;

        $history = [];
        foreach ($months as $m) {
            $metricsMap = $userId ? $service->prefetchMetricsForUsers([$userId], $m['start_date'], $m['end_date'], $box->id) : [];
            $score = $service->calculateScoreFromMetrics($assignment, $metricsMap);
            $userMetrics = $metricsMap[$userId] ?? [];

            $metricBreakdown = [];
            if ($assignment->template && $assignment->template->metrics) {
                foreach ($assignment->template->metrics as $metric) {
                    $actVal = $userMetrics[$metric->metric_name] ?? 0;
                    $metricBreakdown[] = [
                        'name' => $metric->metric_name,
                        'type' => $metric->metric_type,
                        'target_value' => (float)$metric->target_value,
                        'actual_value' => (float)$actVal,
                        'weight' => (float)$metric->weight,
                    ];
                }
            }

            $history[] = [
                'month_key' => $m['key'],
                'month_label' => $m['label'],
                'score' => round($score, 1),
                'status' => $service->determineStatus($score),
                'metrics' => $metricBreakdown
            ];
        }

        return response()->json([
            'success' => true,
            'assignment_id' => $assignment->id,
            'template_name' => $assignment->template ? $assignment->template->name : '',
            'employee_name' => $assignment->boxUser && $assignment->boxUser->user ? $assignment->boxUser->user->name : '',
            'history' => $history
        ]);
    }

    public function getDepartments(Request $request, $boxSlug)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json([]);
        $departments = \App\Models\Department::where('box_id', $box->id)->get();
        return response()->json($departments);
    }

    public function getTemplates(Request $request, $boxSlug)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json([]);
        
        $query = TargetTemplate::with('metrics')->where('box_id', $box->id);
        
        if ($request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('target_type', 'like', "%{$search}%")
                  ->orWhere('frequency', 'like', "%{$search}%")
                  ->orWhere('calculation_method', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($request->has('frequency') && !empty($request->frequency) && $request->frequency !== 'all') {
            if (is_array($request->frequency)) {
                $query->whereIn('frequency', $request->frequency);
            } else {
                $query->where('frequency', $request->frequency);
            }
        }

        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            if (is_array($request->status)) {
                $query->whereIn('status', $request->status);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->has('calculation_method') && !empty($request->calculation_method) && $request->calculation_method !== 'all') {
            if (is_array($request->calculation_method)) {
                $query->whereIn('calculation_method', $request->calculation_method);
            } else {
                $query->where('calculation_method', $request->calculation_method);
            }
        }

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'recent_add':
                    $query->orderBy('id', 'desc');
                    break;
                case 'recent_view':
                    $query->orderBy('updated_at', 'desc');
                    break;
                default:
                    $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $templates = $query->get();
        return response()->json($templates);
    }

    public function getEmployees(Request $request, $boxSlug)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json([]);
        $employees = \App\Models\BoxUser::with('user')->where('box_id', $box->id)->get();
        return response()->json($employees);
    }

    public function getTeams(Request $request, $boxSlug)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json([]);
        $query = \App\Models\Team::where('box_id', $box->id);
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        return response()->json($query->get());
    }
    
    public function teamTargets($boxSlug)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box) abort(404);

        $user = auth()->user();
        $boxUser = $user ? \App\Models\BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first() : null;
        $isAdmin = $boxUser && ($boxUser->staff_role === 'admin' || $user->id == $box->superadmin_id);

        $availableMonths = $this->getAvailableTargetMonths($box->id);
        $selectedMonth = now()->format('Y-m');

        $assignments = collect();
        $employees = BoxUser::with('user')->where('box_id', $box->id)->get();
        $teams = \App\Models\Team::where('box_id', $box->id)->get();
        $departments = \App\Models\Department::where('box_id', $box->id)->get();

        $topPerformer = null;
        $perfStats = null;

        return view('fieldstracking.team-targets', compact('assignments', 'boxSlug', 'boxUser', 'isAdmin', 'employees', 'teams', 'departments', 'availableMonths', 'selectedMonth', 'topPerformer', 'perfStats'));
    }

    public function getAllAssignments(Request $request, $boxSlug)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);

        $query = \App\Models\TargetAssignment::with([
            'template.metrics', 
            'boxUser.user', 
            'performance',
            'actuals',
            'department',
            'team'
        ])
        ->whereHas('template', function($q) use ($box) {
            $q->where('box_id', $box->id);
        });

        // Filter by employee
        if ($request->has('employee_id') && !empty($request->employee_id) && $request->employee_id !== 'all') {
            $query->where('box_user_id', $request->employee_id);
        }

        // Filter by team
        if ($request->has('team_id') && !empty($request->team_id) && $request->team_id !== 'all') {
            $query->where('team_id', $request->team_id);
        }

        // Filter by department
        if ($request->has('department_id') && !empty($request->department_id) && $request->department_id !== 'all') {
            $query->where('department_id', $request->department_id);
        }

        // Filter by frequency
        if ($request->has('frequency') && !empty($request->frequency) && $request->frequency !== 'all') {
            if (is_array($request->frequency)) {
                $query->whereHas('template', function($tQ) use ($request) {
                    $tQ->whereIn('frequency', $request->frequency);
                });
            } else {
                $query->whereHas('template', function($tQ) use ($request) {
                    $tQ->where('frequency', $request->frequency);
                });
            }
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            if (is_array($request->status)) {
                $query->whereIn('status', $request->status);
            } else {
                $query->where('status', $request->status);
            }
        }

        $search = $request->input('search_query');
        if (empty($search) && $request->has('search')) {
            $rawSearch = $request->input('search');
            $search = is_array($rawSearch) ? ($rawSearch['value'] ?? '') : $rawSearch;
        }
        $search = is_string($search) ? trim($search) : '';

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->whereHas('template', function($tQ) use ($search) {
                    $tQ->where('name', 'like', "%{$search}%")
                       ->orWhere('frequency', 'like', "%{$search}%");
                })
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhereHas('boxUser', function($bQ) use ($search) {
                    $bQ->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhereHas('user', function($uQ) use ($search) {
                           $uQ->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                       });
                })
                ->orWhereHas('team', function($tmQ) use ($search) {
                    $tmQ->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('department', function($dpQ) use ($search) {
                    $dpQ->where('name', 'like', "%{$search}%");
                });
            });
        }

        $sort = $request->input('custom_sort', $request->input('sort'));
        if ($sort === 'name_asc') {
            $query->join('target_templates', 'target_assignments.target_template_id', '=', 'target_templates.id')
                  ->orderBy('target_templates.name', 'asc')
                  ->select('target_assignments.*');
        } elseif ($sort === 'name_desc') {
            $query->join('target_templates', 'target_assignments.target_template_id', '=', 'target_templates.id')
                  ->orderBy('target_templates.name', 'desc')
                  ->select('target_assignments.*');
        } else {
            $query->orderBy('target_assignments.id', 'desc');
        }

        return $this->processTargetAssignmentsDataTable($query, $request, $box, true);
    }

    private function processTargetAssignmentsDataTable($query, Request $request, $box, $isAdmin = true)
    {
        $availableMonths = $this->getAvailableTargetMonths($box->id);

        if ($request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $startCarbon = \Carbon\Carbon::parse($startDate);
            $endCarbon = \Carbon\Carbon::parse($endDate);
            if ($startCarbon->isSameMonth($endCarbon)) {
                $monthKey = $startCarbon->format('Y-m');
                $monthLabel = $startCarbon->format('F Y');
            } else {
                $monthKey = 'custom';
                $monthLabel = $startCarbon->format('M d, Y') . ' - ' . $endCarbon->format('M d, Y');
            }
            $monthName = $startCarbon->format('M');
            $year = $startCarbon->format('Y');
        } else {
            $monthInput = $request->get('month', now()->format('Y-m'));
            $yearInput = $request->get('year');
            if (!empty($yearInput) && is_numeric($monthInput) && (int)$monthInput >= 1 && (int)$monthInput <= 12) {
                $monthKey = $yearInput . '-' . str_pad($monthInput, 2, '0', STR_PAD_LEFT);
            } elseif (!empty($yearInput) && empty($request->get('month'))) {
                $monthKey = (string)$yearInput;
            } else {
                $monthKey = $monthInput;
            }
        }

        $draw = intval($request->input('draw', 1));
        $start = max(0, intval($request->input('start', 0)));
        $length = intval($request->input('length', 10));
        if ($length <= 0) $length = 10;

        $service = new \App\Services\TargetCalculationService();

        // -------------------------------------------------------------
        // Case A: All Months (Virtual Pagination over available months)
        // -------------------------------------------------------------
        if ($monthKey === 'all') {
            $monthItems = array_values(array_filter($availableMonths, fn($m) => $m['key'] !== 'all'));
            $M = count($monthItems);

            $perfSummary = $this->computePerformanceSummary($box, $monthKey, $startDate ?? null, $endDate ?? null);
            $empRanks = $perfSummary['employee_ranks'] ?? [];

            $baseAssignments = $query->get()->sort(function($a, $b) use ($empRanks) {
                $rA = $empRanks[$a->box_user_id] ?? 999;
                $rB = $empRanks[$b->box_user_id] ?? 999;
                return $rA <=> $rB;
            })->values();

            $N = $baseAssignments->count();
            $totalVirtual = $N * $M;

            if ($totalVirtual === 0 || $M === 0 || $N === 0) {
                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => []
                ]);
            }

            $endIdx = min($start + $length, $totalVirtual);

            // Group userIds needed by month for only the requested page slice
            $monthGroups = [];
            for ($i = $start; $i < $endIdx; $i++) {
                $mIdx = intdiv($i, $N);
                $aIdx = $i % $N;
                if (!isset($monthItems[$mIdx]) || !isset($baseAssignments[$aIdx])) continue;
                $m = $monthItems[$mIdx];
                $a = $baseAssignments[$aIdx];
                $uId = $a->boxUser ? $a->boxUser->user_id : null;
                if ($uId) {
                    $monthGroups[$m['key']][$uId] = true;
                }
            }

            // Prefetch metrics only for those specific users in their respective months
            $metricsCache = [];
            foreach ($monthGroups as $mK => $uIds) {
                $sDate = $mK . '-01';
                $eDate = \Carbon\Carbon::parse($sDate)->endOfMonth()->toDateString();
                $metricsCache[$mK] = $service->prefetchMetricsForUsers(array_keys($uIds), $sDate, $eDate, $box->id);
            }

            // Build page items
            $pageItems = collect();
            for ($i = $start; $i < $endIdx; $i++) {
                $mIdx = intdiv($i, $N);
                $aIdx = $i % $N;
                if (!isset($monthItems[$mIdx]) || !isset($baseAssignments[$aIdx])) continue;
                $m = $monthItems[$mIdx];
                $a = $baseAssignments[$aIdx];
                $carbonDt = \Carbon\Carbon::parse($m['key'] . '-01');

                $uId = $a->boxUser ? $a->boxUser->user_id : null;
                $userMetrics = $metricsCache[$m['key']][$uId] ?? [];
                $score = $service->calculateScoreFromMetrics($a, $userMetrics);
                $perfStatus = $service->determineStatus($score);

                $actualsList = [];
                if ($a->template && $a->template->metrics) {
                    foreach ($a->template->metrics as $metric) {
                        $actualsList[] = (object)[
                            'metric_id' => $metric->id,
                            'actual_value' => (float)($userMetrics[$metric->metric_name] ?? 0),
                            'metric_name' => $metric->metric_name,
                            'target_value' => (float)$metric->target_value,
                            'metric_type' => $metric->metric_type,
                        ];
                    }
                }

                $item = clone $a;
                $item->month_key = $m['key'];
                $item->month_label = $m['label'];
                $item->month_name = $carbonDt->format('F');
                $item->year = $carbonDt->format('Y');
                $item->performance = (object)[
                    'achievement_percentage' => round($score, 1),
                    'performance_status' => $perfStatus
                ];
                $item->actuals = collect($actualsList);
                $item->rank = $empRanks[$a->box_user_id] ?? 999;
                $pageItems->push($item);
            }

            $formattedRows = $pageItems->map(fn($row) => $this->formatRowData($row, $isAdmin))->values();

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $totalVirtual,
                'recordsFiltered' => $totalVirtual,
                'data' => $formattedRows,
                'top_performer' => $perfSummary['top_performer'] ?? null,
                'summary' => $perfSummary['summary'] ?? null
            ]);
        }

        // -------------------------------------------------------------
        // Case B: Single Month or Custom Date Range (Direct DB Pagination)
        // -------------------------------------------------------------
        if ($monthKey !== 'custom') {
            if (preg_match('/^\d{4}-\d{2}$/', $monthKey)) {
                $startDate = $monthKey . '-01';
                $endDate = \Carbon\Carbon::parse($startDate)->endOfMonth()->toDateString();
                $carbonDt = \Carbon\Carbon::parse($startDate);
                $monthLabel = $carbonDt->format('F Y');
                $monthName = $carbonDt->format('F');
                $year = $carbonDt->format('Y');
            } elseif (preg_match('/^\d{4}$/', $monthKey)) {
                $startDate = $monthKey . '-01-01';
                $endDate = $monthKey . '-12-31';
                $carbonDt = \Carbon\Carbon::parse($startDate);
                $monthLabel = 'Year ' . $monthKey;
                $monthName = 'All';
                $year = $monthKey;
            } else {
                $startDate = now()->startOfMonth()->toDateString();
                $endDate = now()->endOfMonth()->toDateString();
                $carbonDt = \Carbon\Carbon::parse($startDate);
                $monthLabel = $carbonDt->format('F Y');
                $monthName = $carbonDt->format('F');
                $year = $carbonDt->format('Y');
            }
        }

        $perfSummary = $this->computePerformanceSummary($box, $monthKey, $startDate ?? null, $endDate ?? null);
        $empRanks = $perfSummary['employee_ranks'] ?? [];

        $allRecords = $query->get();
        $totalRecords = $allRecords->count();

        $userIds = $allRecords->pluck('boxUser.user_id')->filter()->unique()->values()->all();
        $metricsMap = $service->prefetchMetricsForUsers($userIds, $startDate, $endDate, $box->id);

        foreach ($allRecords as $a) {
            $a->month_label = $monthLabel;
            $a->month_key = $monthKey;
            $a->month_name = $monthName;
            $a->year = $year;

            $userId = $a->boxUser ? $a->boxUser->user_id : null;
            $userMetrics = $metricsMap[$userId] ?? [];
            $score = $service->calculateScoreFromMetrics($a, $userMetrics);
            $perfStatus = $service->determineStatus($score);

            $actualsList = [];
            if ($a->template && $a->template->metrics) {
                foreach ($a->template->metrics as $metric) {
                    $actualsList[] = (object)[
                        'metric_id' => $metric->id,
                        'actual_value' => (float)($userMetrics[$metric->metric_name] ?? 0),
                        'metric_name' => $metric->metric_name,
                        'target_value' => (float)$metric->target_value,
                        'metric_type' => $metric->metric_type,
                    ];
                }
            }

            $a->performance = (object)[
                'achievement_percentage' => round($score, 1),
                'performance_status' => $perfStatus
            ];
            $a->actuals = collect($actualsList);
            $a->rank = $empRanks[$a->box_user_id] ?? 999;
        }

        // Sort: show according to top performer show all into desc order like is used into performance dashboard
        $customSort = $request->input('custom_sort');
        if ($customSort === 'name_asc') {
            $sortedRecords = $allRecords->sortBy(fn($r) => $r->template ? $r->template->name : '')->values();
        } elseif ($customSort === 'name_desc') {
            $sortedRecords = $allRecords->sortByDesc(fn($r) => $r->template ? $r->template->name : '')->values();
        } else {
            // Default: Top Performer First, in descending order of performance score!
            $sortedRecords = $allRecords->sort(function($a, $b) {
                $rankA = $a->rank ?? 999;
                $rankB = $b->rank ?? 999;
                if ($rankA !== $rankB) {
                    return $rankA <=> $rankB; // Rank 1 before Rank 2
                }
                $scoreA = $a->performance ? $a->performance->achievement_percentage : 0;
                $scoreB = $b->performance ? $b->performance->achievement_percentage : 0;
                return $scoreB <=> $scoreA; // Highest score first
            })->values();
        }

        $pageRecords = $sortedRecords->slice($start, $length)->values();
        $formattedRows = $pageRecords->map(fn($row) => $this->formatRowData($row, $isAdmin))->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $formattedRows,
            'top_performer' => $perfSummary['top_performer'] ?? null,
            'summary' => $perfSummary['summary'] ?? null
        ]);
    }

    public function getTopPerformerData(Request $request, $boxSlug)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

        $monthKey = $request->input('month', now()->format('Y-m'));
        $data = $this->computePerformanceSummary($box, $monthKey, $request->input('start_date'), $request->input('end_date'));

        return response()->json(array_merge(['success' => true], $data));
    }

    public function computePerformanceSummary($box, $monthKey = null, $startDateStr = null, $endDateStr = null)
    {
        $monthKey = $monthKey ?: now()->format('Y-m');

        if ($startDateStr && $endDateStr) {
            $startDate = $startDateStr;
            $endDate = $endDateStr;
            $startCarbon = \Carbon\Carbon::parse($startDate);
            $endCarbon = \Carbon\Carbon::parse($endDate);
            if ($startCarbon->isSameMonth($endCarbon)) {
                $monthLabel = $startCarbon->format('F Y');
            } else {
                $monthLabel = $startCarbon->format('M d, Y') . ' - ' . $endCarbon->format('M d, Y');
            }
        } elseif ($monthKey === 'this_month' || $monthKey === now()->format('Y-m')) {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
            $monthLabel = now()->format('F Y');
        } elseif ($monthKey === 'last_month') {
            $startDate = now()->subMonth()->startOfMonth()->toDateString();
            $endDate = now()->subMonth()->endOfMonth()->toDateString();
            $monthLabel = now()->subMonth()->format('F Y');
        } elseif ($monthKey === 'all') {
            $templateStartDate = TargetTemplate::where('box_id', $box->id)->min('start_date') ?: '2026-01-01';
            $startDate = \Carbon\Carbon::parse($templateStartDate)->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
            $monthLabel = 'All Months';
        } elseif (preg_match('/^\d{4}-\d{2}$/', $monthKey)) {
            $startDate = $monthKey . '-01';
            $endDate = \Carbon\Carbon::parse($startDate)->endOfMonth()->toDateString();
            $monthLabel = \Carbon\Carbon::parse($startDate)->format('F Y');
        } elseif (preg_match('/^\d{4}$/', $monthKey)) {
            $startDate = $monthKey . '-01-01';
            $endDate = $monthKey . '-12-31';
            $monthLabel = 'Year ' . $monthKey;
        } else {
            $startDate = $monthKey . '-01';
            $endDate = \Carbon\Carbon::parse($startDate)->endOfMonth()->toDateString();
            $monthLabel = \Carbon\Carbon::parse($startDate)->format('F Y');
        }

        $cacheKey = "perf_summary_" . $box->id . "_" . md5($startDate . '_' . $endDate);

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function() use ($box, $startDate, $endDate, $monthLabel) {
            $assignments = TargetAssignment::with(['template.metrics', 'boxUser.user', 'boxUser.designationRelation'])
                ->whereHas('template', fn($q) => $q->where('box_id', $box->id))
                ->whereNotNull('box_user_id')
                ->get();

            $userIds = $assignments->pluck('boxUser.user_id')->filter()->unique()->values()->all();
            $service = new \App\Services\TargetCalculationService();
            $metricsMap = $service->prefetchMetricsForUsers($userIds, $startDate, $endDate, $box->id);

            $empScores = [];
            $totalScoresSum = 0;
            $totalScoresCount = 0;
            $achievedCount = 0;
            $totalTasksClosed = 0;
            $totalLeadsMatured = 0;

            foreach ($metricsMap as $uId => $metrics) {
                $totalTasksClosed += ($metrics['No of task Closed manually'] ?? 0);
                $totalLeadsMatured += ($metrics['No of Matured'] ?? 0);
            }

            foreach ($assignments as $a) {
                $uid = $a->box_user_id;
                $uIdSys = $a->boxUser ? $a->boxUser->user_id : null;
                $userMetrics = $metricsMap[$uIdSys] ?? [];
                $score = $service->calculateScoreFromMetrics($a, $userMetrics);

                if (!isset($empScores[$uid])) {
                    $empScores[$uid] = [
                        'boxUser' => $a->boxUser,
                        'total' => 0,
                        'count' => 0
                    ];
                }
                $empScores[$uid]['total'] += $score;
                $empScores[$uid]['count']++;

                $totalScoresSum += $score;
                $totalScoresCount++;
                if ($score >= 100) $achievedCount++;
            }

            // Calculate average for each employee and sort descending (like performance-dashboard)
            foreach ($empScores as $uid => &$item) {
                $item['avg'] = $item['count'] > 0 ? ($item['total'] / $item['count']) : 0;
            }
            unset($item);

            uasort($empScores, function($a, $b) {
                return $b['avg'] <=> $a['avg'];
            });

            $empRanks = [];
            $rankCounter = 1;
            foreach ($empScores as $uid => $item) {
                $empRanks[$uid] = $rankCounter++;
            }

            $topEmpId = !empty($empScores) ? array_key_first($empScores) : null;
            $bestEmp = $topEmpId && isset($empScores[$topEmpId]) ? $empScores[$topEmpId]['boxUser'] : null;
            $highestAvg = $topEmpId && isset($empScores[$topEmpId]) ? $empScores[$topEmpId]['avg'] : 0;

            if (!$bestEmp && $assignments->isNotEmpty()) {
                $first = $assignments->first();
                $bestEmp = $first ? $first->boxUser : null;
                $highestAvg = 0;
            }

            $topPerformer = null;
            if ($bestEmp) {
                $empName = trim(($bestEmp->first_name ?? '') . ' ' . ($bestEmp->last_name ?? ''));
                if (!$empName && $bestEmp->user) $empName = $bestEmp->user->name;
                if (!$empName) $empName = 'Employee #' . $bestEmp->id;
                $role = $bestEmp->designationRelation ? $bestEmp->designationRelation->name : ($bestEmp->designation ?? 'Team Member');
                $initials = strtoupper(substr($empName, 0, 2));

                $avatar = $bestEmp->profile_image_path
                    ? (filter_var($bestEmp->profile_image_path, FILTER_VALIDATE_URL) ? $bestEmp->profile_image_path : asset('storage/' . $bestEmp->profile_image_path))
                    : null;

                $scoreVal = max(0, round($highestAvg, 1));

                $topPerformer = [
                    'id' => $bestEmp->id,
                    'name' => $empName,
                    'role' => $role,
                    'score' => $scoreVal,
                    'score_formatted' => $scoreVal . '%',
                    'avatar' => $avatar,
                    'initials' => $initials,
                    'month_label' => $monthLabel,
                ];
            }

            $avgTeamScore = $totalScoresCount > 0 ? round($totalScoresSum / $totalScoresCount, 1) : 0;
            $summary = [
                'team_avg_score' => $avgTeamScore,
                'total_assignments' => $totalScoresCount,
                'targets_achieved' => $achievedCount,
                'completion_rate' => $totalScoresCount > 0 ? round(($achievedCount / $totalScoresCount) * 100, 1) : 0,
                'tasks_closed' => $totalTasksClosed,
                'leads_matured' => $totalLeadsMatured,
                'month_label' => $monthLabel
            ];

            return [
                'top_performer' => $topPerformer,
                'summary' => $summary,
                'employee_ranks' => $empRanks
            ];
        });
    }

    private function formatRowData($row, $isAdmin = true)
    {
        $checkbox = '<label class="checkboxs"><input type="checkbox" class="target-checkbox" value="' . $row->id . '"><span class="checkmarks"></span></label>';

        $assignedToText = 'Unknown';
        $assignedSubText = '';
        $initials = 'TM';

        if ($row->box_user_id && $row->boxUser) {
            $emp = $row->boxUser;
            $userObj = $emp->user;
            $empName = trim(($emp->first_name ?? '') . ' ' . ($emp->last_name ?? ''));
            if (!$empName && $userObj) $empName = $userObj->name;
            if (!$empName) $empName = 'Employee #' . $emp->id;
            $assignedToText = e($empName);
            $assignedSubText = e($userObj ? $userObj->email : ($emp->phone ?? ''));
            $initials = strtoupper(substr($empName, 0, 2));
        } elseif ($row->team_id && $row->team) {
            $assignedToText = e($row->team->name);
            $assignedSubText = 'Team Assignment';
            $initials = 'TM';
        } elseif ($row->department_id && $row->department) {
            $assignedToText = e($row->department->name);
            $assignedSubText = 'Department Assignment';
            $initials = 'DP';
        }

        $rankBadge = '';
        if (isset($row->rank) && $row->rank > 0 && $row->rank < 999) {
            if ($row->rank === 1) {
                $rankBadge = '<span class="badge bg-warning text-white shadow-sm px-2 py-1 me-2" title="Rank #1 Top Performer"><i class="ti ti-crown"></i> #1</span>';
            } elseif ($row->rank === 2) {
                $rankBadge = '<span class="badge bg-secondary text-white shadow-sm px-2 py-1 me-2" title="Rank #2"><i class="ti ti-medal"></i> #2</span>';
            } elseif ($row->rank === 3) {
                $rankBadge = '<span class="badge text-white shadow-sm px-2 py-1 me-2" style="background-color: #cd7f32;" title="Rank #3"><i class="ti ti-award"></i> #3</span>';
            } else {
                $rankBadge = '<span class="badge bg-light text-dark border px-2 py-1 me-2" title="Rank #' . $row->rank . '">#' . $row->rank . '</span>';
            }
        }

        $assignedMemberHtml = '
            <div class="d-flex align-items-center">
                ' . $rankBadge . '
                <span class="avatar avatar-sm bg-primary-light text-primary rounded-circle me-2 fw-bold d-flex align-items-center justify-content-center" style="width:32px; height:32px; font-size:12px;">
                    ' . $initials . '
                </span>
                <div>
                    <h6 class="fs-13 fw-medium mb-0">' . $assignedToText . '</h6>
                    <small class="text-muted">' . $assignedSubText . '</small>
                </div>
            </div>
        ';

        $tpl = $row->template;
        $name = e($tpl ? $tpl->name : 'Untitled Target');
        $calc = ($tpl && $tpl->calculation_method) ? '<br><small class="text-muted">' . e($tpl->calculation_method) . '</small>' : '';
        $targetNameHtml = '<span class="fw-medium text-dark">' . $name . '</span>' . $calc;

        $mName = e($row->month_name ?? '');
        $y = e($row->year ?? '');
        if (!$mName || !$y) {
            if (isset($row->month_key) && str_contains($row->month_key, '-')) {
                $parts = explode('-', $row->month_key);
                $y = $parts[0];
                $mName = \Carbon\Carbon::parse($row->month_key . '-01')->format('F');
            } else {
                $mName = now()->format('F');
                $y = now()->format('Y');
            }
        }
        $monthYearHtml = '
            <div class="d-flex flex-column align-items-start">
                <span class="badge bg-soft-primary text-primary fw-bold fs-12 mb-1">
                    <i class="ti ti-calendar me-1"></i>' . $mName . ' ' . $y . '
                </span>
                <div class="fs-11 text-muted">
                    <span>Month: <strong class="text-dark">' . $mName . '</strong></span><br>
                    <span>Year: <strong class="text-dark">' . $y . '</strong></span>
                </div>
            </div>
        ';

        $frequencyHtml = e($row->template ? $row->template->frequency : '-');

        $html = '<div class="text-muted fs-11 mb-1"><i class="ti ti-calculator me-1 text-primary"></i>Calculated for: <strong class="text-dark">' . $mName . ' ' . $y . '</strong></div>';

        if ($tpl && $tpl->metrics && $tpl->metrics->count() > 0) {
            $mBadges = [];
            foreach ($tpl->metrics as $metric) {
                $actObj = null;
                if ($row->actuals) {
                    $actObj = $row->actuals->firstWhere('metric_id', $metric->id);
                }
                $actVal = 0;
                if ($actObj) {
                    $actVal = is_array($actObj) ? ($actObj['actual_value'] ?? 0) : ($actObj->actual_value ?? 0);
                }
                $targetVal = $metric->target_value ?? 0;
                $curr = ($metric->metric_type === 'Currency') ? '₹' : '';
                $isReached = ($actVal >= $targetVal);
                $badgeClass = $isReached ? 'badge-success' : 'badge-soft-info text-info';
                $mBadges[] = '<span class="badge badge-pill ' . $badgeClass . ' me-1 mb-1">' . e($metric->metric_name) . ': ' . $curr . number_format($actVal) . ' / ' . $curr . number_format($targetVal) . '</span>';
            }
            $html .= implode('', $mBadges);
        } else {
            $html .= '<span class="text-muted">-</span>';
        }

        $score = $row->performance ? $row->performance->achievement_percentage : null;
        if ($score !== null) {
            $perfStatus = $row->performance->performance_status ?? 'Pending';
            $perfClass = 'bg-secondary';
            if ($perfStatus === 'Outstanding') $perfClass = 'bg-success';
            elseif ($perfStatus === 'Excellent') $perfClass = 'bg-primary';
            elseif ($perfStatus === 'Good') $perfClass = 'bg-info';
            elseif ($perfStatus === 'Average') $perfClass = 'bg-warning';
            elseif ($perfStatus === 'Needs Improvement') $perfClass = 'bg-dark';
            elseif ($perfStatus === 'Poor') $perfClass = 'bg-danger';

            $scoreHtml = '
                <div class="d-flex flex-column align-items-start">
                    <span class="badge badge-pill bg-soft-success text-success fw-bold fs-12 mb-1">' . number_format($score, 1) . '%</span>
                    <span class="badge badge-pill ' . $perfClass . ' fs-10 mb-1">' . e($perfStatus) . '</span>
                    <span class="text-muted fs-10">Calculated for ' . $mName . ' ' . $y . '</span>
                </div>
            ';
        } else {
            $scoreHtml = '<span class="text-muted small">Pending calculation</span>';
        }

        $status = $row->status ?? 'Active';
        $statusBadge = ($status === 'Inactive') ? 'bg-danger' : 'bg-success';
        $statusHtml = '<span class="badge badge-pill ' . $statusBadge . '">' . e($status) . '</span>';

        $actionHtml = '
            <div class="text-end">
                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary btn-view-target-details" data-id="' . $row->id . '">
                    <i class="ti ti-chart-bar me-1"></i> View Details & History
                </a>
            </div>
        ';

        return [
            'id' => $row->id,
            'checkbox' => $checkbox,
            'assigned_member' => $assignedMemberHtml,
            'target_name' => $targetNameHtml,
            'month_year' => $monthYearHtml,
            'frequency' => $frequencyHtml,
            'target_vs_achieved' => $html,
            'score_performance' => $scoreHtml,
            'status' => $statusHtml,
            'action' => $actionHtml,
        ];
    }
}

