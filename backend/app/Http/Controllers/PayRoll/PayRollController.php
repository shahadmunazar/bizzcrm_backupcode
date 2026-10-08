<?php
namespace App\Http\Controllers\PayRoll;

use App\Http\Controllers\Controller;
use App\Models\BoxUser;
use App\Models\PayrollAddition;
use App\Models\PayrollDeduction;
use App\Models\PayrollOvertime;
use App\Models\SalarySetting; // Assuming you have this model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class PayRollController extends Controller
{

    // PayRollDelete_deduction

    public function PayRollDelete_deduction(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canDelete = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'delete', 'items');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canDelete) {
            return response()->json(['status' => 'error', 'message' => 'Permission denied'], 403);
        }

        try {
            // Get ID from request
            $id      = $request->input('id');
            $payroll = PayrollDeduction::findOrFail($id);

            // Track who deleted it (optional)
            $payroll->deleted_by = Auth::id();
            $payroll->save();

            // Soft delete (make sure PayrollOvertime uses SoftDeletes)
            $payroll->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Deduction  deleted successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete overtime',
            ], 500);
        }
    }

    public function PayRollUpdate_deduction(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canUpdate = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'update', 'items');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canUpdate) {
            return response()->json(['status' => 'error', 'message' => 'Permission denied'], 403);
        }

        try {
            // ✅ Validate incoming data
            $validated = $request->validate([
                'id'                  => 'required|integer|exists:payroll_deductions,id',
                'name'                => 'required|string|max:255',
                'rate'                => 'required|numeric|min:0', // frontend sends "rate"
                'is_unit_calculation' => 'required|in:0,1',
                'assignee_type'       => 'required|string|in:no_assignee,all,select_employee',
                'box_user_id'         => 'nullable|integer',
            ]);

            // ✅ Fetch record
            $payroll = PayrollDeduction::findOrFail($validated['id']);

            // ✅ Map payload to model fields
            $payroll->name                = $validated['name'];
            $payroll->amount              = $validated['rate']; // ✅ "rate" from frontend → "amount" in DB
            $payroll->is_unit_calculation = (int) $validated['is_unit_calculation'];
            $payroll->assignee_type       = $validated['assignee_type'];
            $payroll->box_user_id         = $validated['assignee_type'] === 'select_employee'
                ? ($validated['box_user_id'] ?? null)
                : null;
            $payroll->updated_by = Auth::id();

            // ✅ Save updates
            $payroll->save();

            // ✅ Return success JSON
            return response()->json([
                'status'  => 'success',
                'message' => 'Payroll deduction updated successfully.',
                'data'    => $payroll,
            ]);

        } catch (\Throwable $th) {
            // ❌ Return detailed error for debugging
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update payroll deduction.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function getPayrollList_deduction(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'items');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view payroll deductions.'
            ], 403);
        }

        $userId = $user->id;

        // Find box for the logged-in user
        $findBoxUser = BoxUser::where('user_id', $userId)
            ->select('id', 'box_id')
            ->first();

        if (! $findBoxUser) {
            return response()->json([
                'message' => 'User not assigned to any box.',
            ], 404);
        }
        // dd($findBoxUser);
        // Query payroll overtimes for this box
        $payrolls = PayrollDeduction::where('box_id', $findBoxUser->box_id);

        return DataTables::of($payrolls)
            ->addColumn('status', function ($row) {
                return $row->status ? '1' : '0';
            })
            ->make(true); // return JSON only
    }
    public function StorePayrolldeduction(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canCreate = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'create', 'items');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canCreate) {
             return response()->json(['status' => 'error', 'message' => 'Permission denied'], 403);
        }

        // Validate the request
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'amount'           => 'required|numeric',
            'unit_calculation' => 'nullable|boolean',
            'assignee'         => 'required|string',
            'status'           => 'required|boolean',
        ]);

        try {
            $userId = Auth::id();

            // Find box for the logged-in user
            $findBoxUser = BoxUser::where('user_id', $userId)
                ->select('id', 'box_id')
                ->first();

            if (! $findBoxUser) {
                return response()->json([
                    'message' => 'User not assigned to any box.',
                ], 404);
            }
            // Create the deduction
            $deduction = PayrollDeduction::create([
                'name'                => $validated['name'],
                'amount'              => $validated['amount'],
                'box_id'              => $findBoxUser->box_id,
                'is_unit_calculation' => $validated['unit_calculation'] ?? 0,
                'assignee_type'       => $validated['assignee'],
                'status'              => $validated['status'],
                'created_by'          => Auth::id(), // track who created it
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Deduction added successfully!',
                'data'    => $deduction,
            ]);

        } catch (\Throwable $th) {
            // Log error for debugging

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to add deduction.' . $th,
            ], 500);
        }
    }

    public function PayRollDelete_overtime(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canDelete = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'delete', 'items');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canDelete) {
            return response()->json(['status' => 'error', 'message' => 'Permission denied'], 403);
        }

        try {
            // Get ID from request
            $id      = $request->input('id');
            $payroll = PayrollOvertime::findOrFail($id);

            // Track who deleted it (optional)
            $payroll->deleted_by = Auth::id();
            $payroll->save();

            // Soft delete (make sure PayrollOvertime uses SoftDeletes)
            $payroll->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Overtime deleted successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete overtime',
            ], 500);
        }
    }

    public function PayRollUpdate_overtime(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canUpdate = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'update', 'items');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canUpdate) {
            return response()->json(['status' => 'error', 'message' => 'Permission denied'], 403);
        }

        // Validate incoming request
        $validated = $request->validate([
            'id'        => 'required|exists:payroll_overtimes,id',
            'name'      => 'required|string|max:255',
            'rate_type' => 'required|string|max:255',
            'rate'      => 'required|numeric',
            'status'    => 'required|boolean',
        ]);

        // Find the record
        $payroll = PayrollOvertime::findOrFail($validated['id']);

        // Update the record with updated_by and status
        $payroll->update([
            'name'       => $validated['name'],
            'rate_type'  => $validated['rate_type'],
            'rate'       => $validated['rate'],
            'status'     => $validated['status'],
            'updated_by' => Auth::id(), // Set the currently authenticated user
        ]);

        return response()->json(['message' => 'Overtime updated successfully!']);
    }

    public function getPayrollList_overtime(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'items');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view payroll overtime.'
            ], 403);
        }

        $userId = $user->id;

        // Find box for the logged-in user
        $findBoxUser = BoxUser::where('user_id', $userId)
            ->select('id', 'box_id')
            ->first();

        if (! $findBoxUser) {
            return response()->json([
                'message' => 'User not assigned to any box.',
            ], 404);
        }

        // Query payroll overtimes for this box
        $payrolls = PayrollOvertime::where('box_id', $findBoxUser->box_id);

        return DataTables::of($payrolls)
            ->addColumn('status', function ($row) {
                return $row->status ? '1' : '0';
            })
            ->make(true); // return JSON only
    }

    public function StorePayrollovertime(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canCreate = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'create', 'items');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canCreate) {
             return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'name'      => 'required|string|max:255',
                'rate_type' => 'required|string|max:50',
                'rate'      => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $userId = Auth::id();

            $findBoxUser = BoxUser::where('user_id', $userId)
                ->select('id', 'box_id')
                ->first();

            if (! $findBoxUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not assigned to any box.',
                ], 404);
            }

            // Save overtime
            $overtime              = new PayrollOvertime();
            $overtime->box_id      = $findBoxUser->box_id;
            $overtime->box_user_id = $findBoxUser->id;
            $overtime->name        = $request->name;
            $overtime->rate_type   = $request->rate_type;
            $overtime->rate        = $request->rate;
            $overtime->created_by  = $userId;
            $overtime->save();

            return response()->json([
                'success' => true,
                'message' => 'Overtime added successfully!',
                'data'    => $overtime,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add overtime. Please try again.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function getPayrollList(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'items');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json(['status' => false, 'message' => 'Permission denied'], 403);
        }

        $userId = $user->id;

        // Find box for the logged-in user
        $findBoxUser = BoxUser::where('user_id', $userId)
            ->select('id', 'box_id')
            ->first();

        if (! $findBoxUser) {
            return response()->json([
                'status'  => false,
                'message' => 'User not assigned to any box.',
            ], 404);
        }

        // Query payroll additions for this box
        $payrolls = PayrollAddition::where('box_id', $findBoxUser->box_id);

        return DataTables::of($payrolls)
            ->addColumn('status', function ($row) {
                return $row->status ? '1' : '0';
            })
            ->make(true); // return JSON only
    }

    public function StorePayrollAddition(Request $request, $model)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canCreate = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'create', 'items');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canCreate) {
             return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        try {
            $rules = [
                'name'               => 'required|string|max:255',
                'category_name'      => 'nullable|string|max:255',
                'amount'             => 'nullable|numeric|min:0',
                'rate'               => 'nullable|numeric|min:0',
                'assignee_type'      => 'nullable|string',
                'select_employees'   => 'nullable|array',
                'select_employees.*' => 'integer|exists:box_users,id',
            ];
            $validator   = Validator::make($request->all(), $rules);
            $userId      = Auth::id();
            $findBoxUser = BoxUser::where('user_id', $userId)
                ->select('id', 'box_id')
                ->first();
            if (! $findBoxUser) {
                return response()->json([
                    'status'  => false,
                    'message' => 'User not assigned to any box.',
                ], 404);
            }
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                ], 422);
            }
            $data                        = $request->only(['name', 'assignee_type', 'category_name', 'amount', 'rate']);
            $data['is_unit_calculation'] = $request->has('is_unit_calculation');
            $data['status']              = true;
            $data['box_id']              = $findBoxUser->box_id;
            $data['box_user_id']         = $findBoxUser->id;
            $data['created_by']          = $userId;
            $data['employee_ids']        = $request->select_employees ? json_encode($request->select_employees) : null;
            $item                        = PayrollAddition::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Payroll item added successfully.',
                'data'    => $item,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function PayRollUpdate(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canUpdate = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'update', 'items');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canUpdate) {
            return response()->json(['status' => 'error', 'message' => 'Permission denied'], 403);
        }

        try {
            // Validate request
            $request->validate([
                'id'                  => 'required|integer|exists:payroll_additions,id',
                'name'                => 'required|string|max:255',
                'category_name'       => 'required|string|max:255',
                'amount'              => 'required|numeric',
                'is_unit_calculation' => 'required|boolean',
                'assignee_type'       => 'required|string|in:no_assignee,all,select_employee',
                'box_user_id'         => 'nullable|integer',
                'status'              => 'nullable',
            ]);

            $payroll = PayrollAddition::findOrFail($request->id);

            // Update fields
            $payroll->name                = $request->name;
            $payroll->category_name       = $request->category_name;
            $payroll->amount              = $request->amount;
            $payroll->is_unit_calculation = $request->is_unit_calculation;
            $payroll->assignee_type       = $request->assignee_type;
            $payroll->box_user_id         = $request->assignee_type === 'select_employee' ? $request->box_user_id : null;
            $payroll->status              = $request->status;
            $payroll->updated_by          = Auth::id();

            // Save changes
            $payroll->save();

            // Return success response
            return response()->json([
                'status'  => 'success',
                'message' => 'Payroll updated successfully',
                'data'    => $payroll,
            ]);
        } catch (\Throwable $th) {
            // Log the error for debugging

            // Return error response
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update payroll',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function PayRollDelete(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canDelete = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'delete', 'items');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canDelete) {
            return response()->json(['status' => 'error', 'message' => 'Permission denied'], 403);
        }

        try {
            // Get ID from request body
            $id      = $request->input('id');
            $payroll = PayrollAddition::findOrFail($id);

            // Optional: track who deleted it
            $payroll->deleted_by = Auth::id();
            $payroll->save();

            $payroll->delete(); // Soft delete if using SoftDeletes

            return response()->json([
                'status'  => 'success',
                'message' => 'Payroll deleted successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete payroll',
            ], 500);
        }
    }

    public function GetAllEmployee(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'items');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json(['status' => false, 'message' => 'Permission denied'], 403);
        }

        try {
            $userId      = Auth::id();
            $findBoxUser = BoxUser::where('user_id', $userId)
                ->select('box_id')
                ->first();
            if (! $findBoxUser) {
                return response()->json([
                    'status'  => false,
                    'message' => 'User not assigned to any box.',
                ], 404);
            }
            $boxId          = $findBoxUser->box_id;
            $salarySettings = SalarySetting::where('box_id', $boxId)
                ->get();
            $employees = BoxUser::where('box_id', $boxId)->with('user', 'departmentRelation')
                ->select('id', 'user_id', 'department', 'first_name')
                ->get();
            $GetOvertime  = PayrollOvertime::where('box_id', $boxId)->get();
            $getAddition  = PayrollAddition::where('box_id', $boxId)->get();
            $getDeduction = PayrollDeduction::where('box_id', $boxId)->get();
            return response()->json([
                'status'  => true,
                'message' => 'Employees and salary settings fetched successfully.',
                'data'    => [
                    'employees'       => $employees,
                    'salary_settings' => $salarySettings,
                    'GetOvertime'     => $GetOvertime,
                    'getAddition'     => $getAddition,
                    'getDeduction'    => $getDeduction,
                ],
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function PayRollSetting(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'setting');
        // dd($canRead);

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access salary settings.');
        }

        $boxSlug = $request->route('box_slug');
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        return view('frontend.payroll.salary_setting', compact('box'));
    }

    public function payroll()
    {
        return view('frontend.payroll.index');
    }

    public function salary_slip(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'slip');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access salary slips.');
        }

        $reportId = $request->query('report_id');
        $report = null;
        $totalProductionHours = '00:00';
        $totalOTHours = '00:00';
        if ($reportId) {
            $report = \App\Models\SalaryReport::with([
                'boxUser.user',
                'boxUser.departmentRelation',
                'boxUser.designationRelation',
                'box'
            ])->find($reportId);

            if ($report) {
                // Calculate total production and OT hours from attendances
                $attendances = \App\Models\Attendance::where('box_user_id', $report->box_user_id)
                    ->whereYear('attendance_date', $report->year)
                    ->whereMonth('attendance_date', $report->month)
                    ->get();
                
                $prodSeconds = 0;
                $otSeconds = 0;
                
                foreach ($attendances as $att) {
                    $dailyProdSeconds = 0;

                    if ($att->production_hours && $att->production_hours !== '00:00:00') {
                        $parts = explode(':', $att->production_hours);
                        if (count($parts) >= 2) {
                            $dailyProdSeconds = ($parts[0] * 3600) + ($parts[1] * 60) + ($parts[2] ?? 0);
                        }
                    } elseif ($att->check_in && $att->check_out) {
                        $checkIn = strtotime($att->check_in);
                        $checkOut = strtotime($att->check_out);
                        if ($checkOut && $checkIn && $checkOut >= $checkIn) {
                            $diffSeconds = $checkOut - $checkIn;
                            
                            $breakSeconds = 0;
                            if ($att->break_duration && $att->break_duration !== '00:00:00') {
                                $parts = explode(':', $att->break_duration);
                                if (count($parts) >= 2) {
                                    $breakSeconds = ($parts[0] * 3600) + ($parts[1] * 60) + ($parts[2] ?? 0);
                                }
                            }
                            
                            $dailyProdSeconds = max(0, $diffSeconds - $breakSeconds);
                        }
                    }
                    $prodSeconds += $dailyProdSeconds;

                    if ($att->over_time && $att->over_time !== '00:00:00') {
                        $parts = explode(':', $att->over_time);
                        if (count($parts) >= 2) {
                            $otSeconds += ($parts[0] * 3600) + ($parts[1] * 60) + ($parts[2] ?? 0);
                        }
                    }
                }
                
                $totalProductionHours = sprintf('%02d:%02d', floor($prodSeconds / 3600), floor(($prodSeconds % 3600) / 60));
                $totalOTHours = sprintf('%02d:%02d', floor($otSeconds / 3600), floor(($otSeconds % 3600) / 60));
            }
        }

        if ($request->has('download_pdf') && $report) {
            $amountInWords = $this->numberToWords(floor($report->net_salary ?? 0));
            $pdf = Pdf::loadView('frontend.payroll.salary_slip_pdf', compact('report', 'amountInWords', 'totalProductionHours', 'totalOTHours'));
            return $pdf->download('salary_slip_' . ($report->boxUser->employee_id ?? $report->id) . '.pdf');
        }

        return view('frontend.payroll.salary_slip', compact('report', 'totalProductionHours', 'totalOTHours'));
    }

    private function numberToWords($num)
    {
        $a = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven',
            'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $b = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        
        $num = (string) $num;
        if (strlen($num) > 9) return 'Overflow';
        
        $padded = str_pad($num, 9, '0', STR_PAD_LEFT);
        preg_match('/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/', $padded, $n);
        
        if (!$n) return '';
        
        $str = '';
        
        // Crore
        if ($n[1] != '00') {
            $val = (int)$n[1];
            $str .= ($val < 20 ? $a[$val] : $b[(int)$n[1][0]] . ' ' . $a[(int)$n[1][1]]) . ' Crore ';
        }
        
        // Lakh
        if ($n[2] != '00') {
            $val = (int)$n[2];
            $str .= ($val < 20 ? $a[$val] : $b[(int)$n[2][0]] . ' ' . $a[(int)$n[2][1]]) . ' Lakh ';
        }
        
        // Thousand
        if ($n[3] != '00') {
            $val = (int)$n[3];
            $str .= ($val < 20 ? $a[$val] : $b[(int)$n[3][0]] . ' ' . $a[(int)$n[3][1]]) . ' Thousand ';
        }
        
        // Hundred
        if ($n[4] != '0') {
            $val = (int)$n[4];
            $str .= $a[$val] . ' Hundred ';
        }
        
        // Tens and Ones
        if ($n[5] != '00') {
            if ($str != '') $str .= 'and ';
            $val = (int)$n[5];
            $str .= ($val < 20 ? $a[$val] : $b[(int)$n[5][0]] . ' ' . $a[(int)$n[5][1]]);
        }
        
        return trim($str);
    }
    public function employe_salary(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'salary');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
             abort(403, 'You do not have permission to access employee salary.');
        }

        $boxSlug = $request->route('box_slug');
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        return view('frontend.payroll.employee_salary', compact('box'));
    }

    public function payroll_items()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'items');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access payroll items.');
        }

        return view('frontend.payroll.items');
    }

    public function payroll_overtime()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'items');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access payroll items.');
        }

        return view('frontend.payroll.overtime');
    }

    public function loan_settings()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'update', 'loan_settings');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access loan settings.');
        }

        return view('frontend.payroll.loan_settings');
    }

    public function loan_tracker()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'loan_tracker');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access loan tracker.');
        }

        return view('frontend.payroll.loan_tracker');
    }


    public function payroll_deduction()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'items');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access payroll items.');
        }

        return view('frontend.payroll.deduction');
    }

    public function employe_salary_report(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'report');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access salary reports.');
        }

        $boxSlug = $request->route('box_slug');
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        return view('frontend.payroll.salary_report', compact('box'));
    }
}
