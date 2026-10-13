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
        
        $templates = TargetTemplate::where('box_id', $boxId)->get();
        $employees = BoxUser::where('box_id', $boxId)->get();
        return view('fieldstracking.target-template', compact('templates', 'employees')); 
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
            $allEmployees = BoxUser::where('box_id', $box->id)
                ->where(function($q) use ($box) {
                    $q->whereNull('staff_role')->orWhere('staff_role', '!=', 'admin');
                })
                ->where('user_id', '!=', $box->superadmin_id)
                ->get();

            if ($allEmployees->isEmpty()) {
                $allEmployees = BoxUser::where('box_id', $box->id)->get();
            }

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

    public function myTarget($boxSlug) { 
        return view('fieldstracking.my-target'); 
    }

    public function getMyAssignments(Request $request, $boxSlug)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        $user = auth()->user();
        if (!$box || !$user) return response()->json([]);

        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
        if (!$boxUser) return response()->json([]);

        $query = \App\Models\TargetAssignment::with(['template.metrics', 'actuals', 'performance'])
                            ->where('box_user_id', $boxUser->id);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('template', function($tQ) use ($search) {
                    $tQ->where('name', 'like', "%{$search}%")
                       ->orWhere('frequency', 'like', "%{$search}%");
                })
                ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $assignments = $query->orderBy('id', 'desc')->get();
        return response()->json($assignments);
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
        
        if ($request->has('start_date') && $request->has('end_date')) {
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

        $templates = $query->orderBy('id', 'desc')->get();
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
    
    public function teamTargets() { 
        return view('fieldstracking.team-targets'); 
    }

    public function getAllAssignments(Request $request, $boxSlug)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json([]);

        $query = \App\Models\TargetAssignment::with([
            'template', 
            'template.metrics',
            'boxUser.user', 
            'performance',
            'actuals'
        ])
        ->whereHas('template', function($q) use ($box) {
            $q->where('box_id', $box->id);
        });

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
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
                           $uQ->where('name', 'like', "%{$search}%");
                       });
                })
                ->orWhere('department_id', 'like', "%{$search}%")
                ->orWhere('team_id', 'like', "%{$search}%");
            });
        }

        $order = $request->input('order', 'desc');
        if (!in_array(strtolower($order), ['asc', 'desc'])) {
            $order = 'desc';
        }

        $assignments = $query->orderBy('id', $order)->get();
        
        $service = new \App\Services\TargetCalculationService();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $service->syncActualsBatch($assignments, $startDate, $endDate, $box->id);

        return response()->json($assignments);
    }
}
