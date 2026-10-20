<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\BaseController;
use App\Models\{Box, BoxUser, Shift};
use App\Jobs\GenerateShiftForBoxEmployees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Validator};
use Yajra\DataTables\Facades\DataTables;

class ShiftController extends BaseController
{
    public function shiftemployeassign(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUser) {
            return response()->json(['status' => 404, 'message' => 'Box User not found']);
        }
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = strtolower($boxUser->staff_role ?? '') === 'admin';
        $canCreate = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'create', 'details_shift');
        $canUpdate = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'update', 'details_shift');
        
        if (!$isGlobalAdmin && !$isBoxAdmin && !$canCreate && !$canUpdate) {
            return response()->json(['status' => 403, 'message' => 'You do not have permission to assign shifts.']);
        }
        try {
            $box = Box::where('id', $boxUser->box_id)->select('id', 'name')->first();
            if (!$box) {
                return response()->json(['status' => 404, 'message' => 'Box not found']);
            }

            $user = BoxUser::where('box_id', $box->id)
                ->where('id', $request->employee_id)
                ->first();
            if (!$user) {
                return response()->json(['status' => 404, 'message' => 'Employee not found in this box']);
            }
            $updated = BoxUser::where('box_id', $box->id)
                ->where('id', $request->employee_id)
                ->update(['shift_id' => $request->shift_id]);
            if ($updated) {
                return response()->json(['status' => 200, 'message' => 'Shift assigned successfully']);
            } else {
                return response()->json(['status' => 400, 'message' => 'Failed to assign shift (no changes made or error)']);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => 'Server error', 'error' => $th->getMessage()]);
        }
    }

    public function attendance_shift(Request $request)
    {
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'details_shift');
        
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
             return response()->json(['status' => 403, 'success' => false, 'message' => 'You do not have permission to view shifts.']);
        }

        try {
            if (!$boxUser) {
                 return response()->json(['status' => 404, 'success' => false, 'message' => 'Box User not found.']);
            }
            
            $box = Box::where('id', $boxUser->box_id)->select('id')->first();
            if (! $box) {
                return response()->json(['status' => 404, 'success' => false, 'message' => 'Box not found.']);
            }
            $shifts = Shift::where('box_id', $box->id)->select('id', 'code')->get();
            return response()->json([
                'status'  => 200,
                'success' => true,
                'data'    => $shifts,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 500,
                'success' => false,
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ]);
        }
    }

    public function store_shift(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUser) {
            return response()->json(['status' => 404, 'message' => 'Box User not found']);
        }
        $box = Box::find($boxUser->box_id);
        
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web') || $user->id === 1;
        $isBoxSuperAdmin = $box && $box->superadmin_id == $user->id;
        $isBoxAdmin = (strtolower($boxUser->staff_role ?? '') === 'admin') || $isBoxSuperAdmin;
        $canCreate = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'create', 'shift');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canCreate) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to create shifts.'
            ], 403);
        }
        try {
            $rules = [
                'id'                    => 'nullable|exists:shifts,id',
                'code'                  => 'nullable|string|max:255',
                'shift_name'            => 'required|string|max:255',

                // Time fields (Allow string, format will be normalized)
                'in_time'               => 'nullable|string',
                'out_time'              => 'nullable|string',
                'shift_hours'           => 'nullable|string',

                'shift_enabled'         => 'nullable|boolean',
                'rest_enabled'          => 'nullable|boolean',

                'lunch_out'             => 'nullable|string',
                'lunch_in'              => 'nullable|string',
                'lunch_hours'           => 'nullable|string',

                'short_break_enabled'   => 'nullable|boolean',
                'short_break_out'       => 'nullable|string',
                'short_break_in'        => 'nullable|string',
                'short_break_hours'     => 'nullable|string',

                'short_break_enabled_2' => 'nullable|boolean',
                'short_break_out_2'     => 'nullable|string',
                'short_break_in_2'      => 'nullable|string',
                'short_break_hours_2'   => 'nullable|string',

                'half_day_enabled'      => 'nullable|boolean',
                'half_day_start_time'   => 'nullable|string',
                'half_day_end_time'     => 'nullable|string',
                'half_day_hours'        => 'nullable|string',

                'absent_below'          => 'nullable|string',
                'absent_below_hours'    => 'nullable|numeric|min:0',
                'absent_below_minutes'  => 'nullable|numeric|min:0|max:59',
                'half_day_below'        => 'nullable|string',
                'half_day_below_hours'  => 'nullable|numeric|min:0',
                'half_day_below_minutes'=> 'nullable|numeric|min:0|max:59',
                'short_leave_below'     => 'nullable|string',
                'short_leave_below_hours' => 'nullable|numeric|min:0',
                'short_leave_below_minutes' => 'nullable|numeric|min:0|max:59',

                'overtime_allowed'      => 'nullable|string',
                'check_over_time'       => 'nullable|boolean',

                'weekly_off_1'          => 'nullable|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
                'weekly_off_2'          => 'nullable|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
                'weekly_off_3'          => 'nullable|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
                'weekly_off_4'          => 'nullable|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',

                'saturday_offs'         => 'nullable|array',
                'saturday_half_days'    => 'nullable|array',

                'half_day_time'         => 'nullable|string',

                'early_login_punchIn'   => 'nullable|string',
                'early_login_minutes'   => 'nullable|string',
                'max_punch_in_allowed'  => 'nullable|integer|min:1|max:100',

                'short_leave_deduction' => 'nullable|string',
                'auto_punchout_time'    => 'nullable|string',
                'half_day_deduction'    => 'nullable|string',
                'absent_deduction'      => 'nullable|string',

                'start_from_date'       => 'nullable|date',
                'end_date'              => 'nullable|date|after_or_equal:start_from_date',

                'relaxation_time'       => 'nullable|string',
                'max_relaxations_allowed' => 'nullable|integer|min:0',
                'punch_calculation_mode' => 'required|in:all_in_out,first_in_last_out',
            ];

            $requestData = $request->all();
            
            // Handle hours/minutes split inputs for thresholds
            $combineThreshold = function($prefix) use ($request) {
                if ($request->filled($prefix . '_hours') || $request->filled($prefix . '_minutes')) {
                    $h = (int)$request->input($prefix . '_hours', 0);
                    $m = (int)$request->input($prefix . '_minutes', 0);
                    return sprintf('%02d:%02d:00', $h, $m);
                }
                return $request->input($prefix);
            };

            $requestData['absent_below'] = $combineThreshold('absent_below');
            $requestData['half_day_below'] = $combineThreshold('half_day_below');
            $requestData['short_leave_below'] = $combineThreshold('short_leave_below');

            $integerFields = ['max_relaxations_allowed', 'max_punch_in_allowed'];
            foreach ($integerFields as $field) {
                if (isset($requestData[$field])) {
                    $val = trim((string)$requestData[$field]);
                    if ($val === '') {
                        $requestData[$field] = null;
                    } elseif (is_numeric($val)) {
                        $requestData[$field] = (int)$val;
                    }
                }
            }

            $validator = Validator::make($requestData, $rules);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();

            // Convert AM/PM to 24-hour format
            $convertTime = function ($value) {
                if (!$value) return null;
                try {
                    return date("H:i:s", strtotime($value));
                } catch (\Exception $e) {
                    return null;
                }
            };

            // Normalize all time fields
            $timeFields = [
                'in_time', 'out_time', 'lunch_out', 'lunch_in',
                'short_break_out', 'short_break_in',
                'short_break_out_2', 'short_break_in_2',
                'half_day_start_time', 'half_day_end_time',
                'auto_punchout_time'
            ];

            foreach ($timeFields as $field) {
                if (isset($data[$field])) {
                    $data[$field] = $convertTime($data[$field]);
                }
            }

            // Duration fields (keep as HH:MM:SS)
            $durationFields = [
                'shift_hours', 'lunch_hours', 'short_break_hours',
                'short_break_hours_2', 'half_day_hours',
                'absent_below', 'half_day_below', 'short_leave_below',
                'overtime_allowed', 'relaxation_time', 'early_login_minutes'
            ];

            foreach ($durationFields as $field) {
                if (isset($data[$field]) && !empty($data[$field])) {
                    $value = $data[$field];
                    // If it's a numeric value (decimal hours), convert to HH:MM:SS
                    if (is_numeric($value)) {
                        $hours = floor($value);
                        $minutes = round(($value - $hours) * 60);
                        $data[$field] = sprintf('%02d:%02d:00', $hours, $minutes);
                    } else {
                        $data[$field] = $value;
                    }
                } else {
                    $data[$field] = null;
                }
            }

            $userId = Auth::id();
            $box = BoxUser::where('user_id', $userId)->select('box_id')->first();

            $data['box_id'] = $box->box_id;
            $data['created_by'] = $userId;

            // Convert checkboxes to Boolean
            $checkboxes = [
                'shift_enabled', 'rest_enabled', 'short_break_enabled',
                'short_break_enabled_2', 'half_day_enabled', 'check_over_time'
            ];

            foreach ($checkboxes as $cb) {
                $data[$cb] = $request->has($cb) ? 1 : 0;
            }

            // Handle punch calculation mode (radio buttons)
            if ($request->has('punch_calculation_mode')) {
                $data['all_in_out_calculation'] = ($request->punch_calculation_mode === 'all_in_out') ? 1 : 0;
                $data['first_in_last_out_calculation'] = ($request->punch_calculation_mode === 'first_in_last_out') ? 1 : 0;
            }

            // Create or Update
            if ($request->id) {
                $shift = Shift::findOrFail($request->id);
                $data['updated_by'] = $userId;
                $shift->update($data);

                return response()->json(['message' => 'Shift updated successfully.']);
            }

            Shift::create($data);
            return response()->json(['message' => 'Shift created successfully.']);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function generate_shift_for_all_employees(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canGenerate = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'generate', 'shift');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canGenerate) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to generate shifts.'
            ], 403);
        }

        // Validate required fields using Validator::make()
        $validator = Validator::make($request->all(), [
            'shift_id' => 'required|integer|exists:shifts,id',
            'month' => 'required|string|size:2|in:01,02,03,04,05,06,07,08,09,10,11,12',
            'year' => 'required|string|size:4|digits:4',
        ], [
            'shift_id.required' => 'Shift ID is required.',
            'shift_id.integer' => 'Shift ID must be an integer.',
            'shift_id.exists' => 'The selected shift does not exist.',
            'month.required' => 'Month is required.',
            'month.size' => 'Month must be 2 digits.',
            'month.in' => 'Month must be between 01 and 12.',
            'year.required' => 'Year is required.',
            'year.size' => 'Year must be 4 digits.',
            'year.digits' => 'Year must be exactly 4 digits.',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $userId  = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->select('box_id')->firstOrFail();
        $shift   = Shift::where('id', $validated['shift_id'])
            ->where('box_id', $boxUser->box_id)
            ->firstOrFail();

        // Get validated month and year from request
        $month = $validated['month'];
        $year = $validated['year'];

        // Dispatch job with month and year
        GenerateShiftForBoxEmployees::dispatch($boxUser->box_id, $shift->id, $month, $year);

        BoxUser::where('box_id', $boxUser->box_id)->update(['shift_id' => $shift->id]);

        $monthName = \Carbon\Carbon::create($year, $month, 1)->format('F Y');
        return response()->json([
            'message' => 'Shift assigned and attendance recalculation queued for ' . $monthName . '.',
            'status'  => 200,
        ]);
    }

    public function updateShift(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUser) {
            return response()->json(['status' => 404, 'message' => 'Box User not found']);
        }
        $box = Box::find($boxUser->box_id);

        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web') || $user->id === 1;
        $isBoxSuperAdmin = $box && $box->superadmin_id == $user->id;
        $isBoxAdmin = (strtolower($boxUser->staff_role ?? '') === 'admin') || $isBoxSuperAdmin;
        $canUpdate = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'update', 'shift');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canUpdate) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to update shifts.'
            ], 403);
        }
        try {
            $rules = [
                'shift_id'              => 'required|integer|exists:shifts,id',
                'shift_code'            => 'nullable|string|max:255',
                'shift_type'            => 'nullable|string|max:255',
                'shift_in_time'         => ['nullable', 'string'],
                'shift_out_time'        => ['nullable', 'string'],
                'shift_hours'           => ['nullable', 'string'],
                'shift_enabled'         => 'nullable|boolean',
                'rest_enabled'          => 'nullable|boolean',
                'lunch_out'             => ['nullable', 'string'],
                'lunch_in'              => ['nullable', 'string'],
                'lunch_hours'           => ['nullable', 'string'],
                'short_break_enabled'   => 'nullable|boolean',
                'short_break_out'       => ['nullable', 'string'],
                'short_break_in'        => ['nullable', 'string'],
                'short_break_hours'     => ['nullable', 'string'],
                'short_break_enabled_2' => 'nullable|boolean',
                'short_break_outs_2'    => ['nullable', 'string'],
                'short_break_ins_2'     => ['nullable', 'string'],
                'short_break_hours_2'   => ['nullable', 'string'],
                'half_day_enabled'      => 'nullable|boolean',
                'half_day_start_time'   => ['nullable', 'string'],
                'half_day_end_time'     => ['nullable', 'string'],
                'half_day_hours'        => ['nullable', 'string'],
                'absent_below'          => ['nullable', 'string'],
                'absent_below_hours'    => 'nullable|integer|min:0',
                'absent_below_minutes'  => 'nullable|integer|min:0|max:59',
                'half_day_limit'        => ['nullable', 'string'],
                'half_day_limit_hours'  => 'nullable|integer|min:0',
                'half_day_limit_minutes'=> 'nullable|integer|min:0|max:59',
                'short_leave_limit'     => ['nullable', 'string'],
                'short_leave_limit_hours' => 'nullable|integer|min:0',
                'short_leave_limit_minutes' => 'nullable|integer|min:0|max:59',
                'overtime_allowed'      => ['nullable', 'string'],
                'weekly_off_1'          => 'nullable|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,null',
                'weekly_off_2'          => 'nullable|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,null',
                'weekly_off_3'          => 'nullable|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,null',
                'weekly_off_4'          => 'nullable|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,null',
                'saturday_offs'         => 'nullable|array',
                'saturday_half_days'    => 'nullable|array',
                'check_over_time'       => 'nullable',
                'half_day_time'         => 'nullable',
                'early_login_punchIn'   => 'nullable',
                'early_login_minutes'   => 'nullable',
                'max_punch_in_allowed'  => 'nullable|integer|min:1|max:100',
                'relaxation_time'       => 'nullable',
                'max_relaxations_allowed' => 'nullable|integer|min:0',
                'auto_punchout_time'    => 'nullable',
                'short_leave_deduction' => 'nullable',
                'half_day_deduction'    => 'nullable',
                'absent_deduction'      => 'nullable',
                'start_from_date'       => 'nullable|date',
                'end_date'              => 'nullable|date|after_or_equal:start_from_date',
                'punch_calculation_mode' => 'required|in:all_in_out,first_in_last_out',
            ];

            $requestData = $request->all();
            
            // Handle hours/minutes split inputs for thresholds
            $combineThreshold = function($prefix) use ($request) {
                if ($request->filled($prefix . '_hours') || $request->filled($prefix . '_minutes')) {
                    $h = (int)$request->input($prefix . '_hours', 0);
                    $m = (int)$request->input($prefix . '_minutes', 0);
                    return sprintf('%02d:%02d:00', $h, $m);
                }
                return $request->input($prefix);
            };

            $requestData['absent_below'] = $combineThreshold('absent_below');
            $requestData['half_day_limit'] = $combineThreshold('half_day_limit');
            $requestData['short_leave_limit'] = $combineThreshold('short_leave_limit');

            $validator = Validator::make($requestData, $rules);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $validated = $validator->validated();
            $userId    = Auth::id();

            // Get box_id from BoxUser
            $box = BoxUser::where('user_id', $userId)->select('id', 'box_id')->first();
            if (! $box) {
                return response()->json(['message' => 'Tenant (Box) not found for user.'], 404);
            }

            // Find the shift
            $shift = Shift::where('id', $validated['shift_id'])
                ->where('box_id', $box->box_id)
                ->first();

            if (! $shift) {
                return response()->json(['message' => 'Shift not found or access denied.'], 404);
            }

            // Format time helper (handles AM/PM and numeric values)
            $formatTime = function($time) {
                if (empty($time)) {
                    return null;
                }
                if (is_numeric($time)) {
                    $hours = floor($time);
                    $minutes = round(($time - $hours) * 60);
                    return sprintf('%02d:%02d:00', $hours, $minutes);
                }
                try {
                    return date("H:i:s", strtotime($time));
                } catch (\Exception $e) {
                    return null;
                }
            };

            // Prepare data for update
            $data = [
                'code'                  => $validated['shift_code'] ?? null,
                'shift_name'            => $validated['shift_type'] ?? null,
                'in_time'               => $formatTime($validated['shift_in_time'] ?? null),
                'out_time'              => $formatTime($validated['shift_out_time'] ?? null),
                'shift_hours'           => $validated['shift_hours'] ?? null,
                'shift_enabled'         => $request->input('shift_enabled') == 1,
                'rest_enabled'          => $request->input('rest_enabled') == 1,
                'lunch_out'             => $formatTime($validated['lunch_out'] ?? null),
                'lunch_in'              => $formatTime($validated['lunch_in'] ?? null),
                'lunch_hours'           => $validated['lunch_hours'] ?? null,
                'short_break_enabled'   => $request->input('short_break_enabled') == 1,
                'short_break_out'       => $formatTime($validated['short_break_out'] ?? null),
                'short_break_in'        => $formatTime($validated['short_break_in'] ?? null),
                'short_break_hours'     => $validated['short_break_hours'] ?? null,
                'short_break_enabled_2' => $request->input('short_break_enabled_2') == 1,
                'short_break_out_2'     => $formatTime($validated['short_break_outs_2'] ?? null),
                'short_break_in_2'      => $formatTime($validated['short_break_ins_2'] ?? null),
                'short_break_hours_2'   => $validated['short_break_hours_2'] ?? null,
                'half_day_enabled'      => $request->input('half_day_enabled') == 1,
                'half_day_start_time'   => $formatTime($validated['half_day_start_time'] ?? null),
                'half_day_end_time'     => $formatTime($validated['half_day_end_time'] ?? null),
                'half_day_hours'        => $validated['half_day_hours'] ?? null,
                'absent_below'          => $validated['absent_below'] ?? null,
                'half_day_below'        => $validated['half_day_limit'] ?? null,
                'short_leave_below'     => $validated['short_leave_limit'] ?? null,
                'overtime_allowed'      => $validated['overtime_allowed'] ?? null,
                'weekly_off_1'          => (! empty($validated['weekly_off_1']) && $validated['weekly_off_1'] !== 'null') ? $validated['weekly_off_1'] : null,
                'weekly_off_2'          => (! empty($validated['weekly_off_2']) && $validated['weekly_off_2'] !== 'null') ? $validated['weekly_off_2'] : null,
                'weekly_off_3'          => (! empty($validated['weekly_off_3']) && $validated['weekly_off_3'] !== 'null') ? $validated['weekly_off_3'] : null,
                'weekly_off_4'          => (! empty($validated['weekly_off_4']) && $validated['weekly_off_4'] !== 'null') ? $validated['weekly_off_4'] : null,
                'saturday_offs'         => $validated['saturday_offs'] ?? [],
                'saturday_half_days'    => $validated['saturday_half_days'] ?? [],
                'check_over_time'       => $request->input('check_over_time') == 1,
                'half_day_time'         => $validated['half_day_time'] ?? null,
                'short_leave_deduction' => $validated['short_leave_deduction'] ?? null,
                'relaxation_time'       => $validated['relaxation_time'] ?? null,
                'max_relaxations_allowed' => $validated['max_relaxations_allowed'] ?? null,
                'half_day_deduction'    => $validated['half_day_deduction'] ?? null,
                'early_login_punchIn'   => $validated['early_login_punchIn'] ?? null,
                'early_login_minutes'   => $validated['early_login_minutes'] ?? null,
                'max_punch_in_allowed'  => $validated['max_punch_in_allowed'] ?? null,
                'auto_punchout_time'    => $formatTime($validated['auto_punchout_time'] ?? null),
                'all_in_out_calculation' => ($validated['punch_calculation_mode'] ?? '') === 'all_in_out' ? 1 : 0,
                'first_in_last_out_calculation' => ($validated['punch_calculation_mode'] ?? '') === 'first_in_last_out' ? 1 : 0,
                'absent_deduction'      => $validated['absent_deduction'] ?? null,
                'box_id'                => $box->box_id,
                'updated_by'            => $userId,
            ];

            // Handle effective period dates - allow setting to null if explicitly sent as empty
            if ($request->has('start_from_date')) {
                $data['start_from_date'] = !empty($validated['start_from_date']) ? $validated['start_from_date'] : null;
            }

            if ($request->has('end_date')) {
                $data['end_date'] = !empty($validated['end_date']) ? $validated['end_date'] : null;
            }

            $shift->update($data);

            return response()->json(['message' => 'Shift updated successfully.']);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function deleteShift(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUser) {
            return response()->json(['status' => 404, 'message' => 'Box User not found']);
        }
        $box = Box::find($boxUser->box_id);

        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web') || $user->id === 1;
        $isBoxSuperAdmin = $box && $box->superadmin_id == $user->id;
        $isBoxAdmin = (strtolower($boxUser->staff_role ?? '') === 'admin') || $isBoxSuperAdmin;
        $canDelete = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'delete', 'shift');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canDelete) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to delete shifts.'
            ], 403);
        }
        try {
            $validator = Validator::make($request->all(), [
                'shift_id' => 'required|integer|exists:shifts,id',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422);
            }
            $shift             = Shift::findOrFail($request->shift_id);
            $shift->deleted_by = Auth::id();
            $shift->save();
            $shift->delete();
            return response()->json(['message' => 'Shift deleted successfully.']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong while deleting the shift.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function ShiftDetails(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'shift');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to view shift details.'
            ], 403);
        }
        try {
            $shiftId = $request->input('shiftId');
            if (! $shiftId) {
                return response()->json([
                    'message' => 'Shift ID is required.',
                ], 400);
            }
            $shift = Shift::find($shiftId);
            if (! $shift) {
                return response()->json([
                    'message' => 'Shift not found.',
                ], 404);
            }
            return response()->json([
                'success' => true,
                'status'  => 200,
                'message' => 'Shift details fetched successfully.',
                'data'    => $shift,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to fetch shift data.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function Shiftdata(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'shift');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to view shift data.'
            ], 403);
        }
        try {
            $userId = Auth::id();
            $box    = BoxUser::where('user_id', $userId)->first();
            if (! $box) {
                return response()->json([
                    'message' => 'Failed to fetch shift data.',
                    'error'   => 'No box found for this user.',
                ], 404);
            }
            $query = Shift::where('box_id', $box->box_id);
            if ($request->filled('search_shift')) {
                $search = $request->input('search_shift');
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%$search%")
                        ->orWhere('in_time', 'like', "%$search%")
                        ->orWhere('out_time', 'like', "%$search%")
                        ->orWhere('shift_hours', 'like', "%$search%")
                        ->orWhere('lunch_out', 'like', "%$search%")
                        ->orWhere('lunch_in', 'like', "%$search%")
                        ->orWhere('short_break_out', 'like', "%$search%")
                        ->orWhere('half_day_start_time', 'like', "%$search%")
                        ->orWhere('half_day_end_time', 'like', "%$search%");
                });
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('created_at', function ($shift) {
                    return $shift->created_at ? $shift->created_at->format('d-m-y H:i:s') : null;
                })
                ->toJson();

        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\Log::error('Shiftdata DataTables Error: ' . $th->getMessage() . "\n" . $th->getTraceAsString());
            return response()->json([
                'message' => 'Failed to fetch shift data.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }
}
