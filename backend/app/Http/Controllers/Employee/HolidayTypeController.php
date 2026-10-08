<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\{BoxUser, HolidayType};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Validator, DB};
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Http\Controllers\BaseController;

class HolidayTypeController extends BaseController
{
    public function HolidayPrefill(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'holiday_type');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access holiday types.'
            ], 403);
        }

        try {
            if (! $boxUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Box user not found',
                ], 404);
            }
            $holidayTypes = HolidayType::where('box_id', $boxUser->box_id)
                ->where('status', '1')
                ->get(['id', 'holiday_type', 'holiday_code', 'is_mandatory']);
            $codes = $holidayTypes->map(function ($item) {
                return [
                    'id'           => $item->id,
                    'value'        => $item->holiday_type,
                    'label'        => $item->holiday_type,
                    'holiday_code' => $item->holiday_code ?? '',
                    'is_mandatory' => (bool) $item->is_mandatory,
                ];
            });
            return response()->json([
                'success' => true,
                'codes'   => $codes,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error',
            ], 500);
        }
    }

    public function getHolidayTypes(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'holiday_type');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view holiday types.'
            ], 403);
        }

        if (! $boxUser) {
            return response()->json([
                'success' => false,
                'message' => 'Box user not found',
            ], 404);
        }

        $query = HolidayType::where('box_id', $boxUser->box_id);

        return DataTables::of($query)
            ->addColumn('created_at', function ($row) {
                return $row->created_at
                    ? Carbon::parse($row->created_at)->format('d M, Y h:i A')
                    : '-';
            })
            ->make(true);
    }

    public function holidayStore(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canCreate = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'create', 'holiday_type');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canCreate) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create holiday types.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'holiday_type' => 'required|string|max:255',
            'holiday_code' => 'required|string|max:100',
            'is_mandatory' => 'nullable',
            'max_limit'    => 'nullable',
            'status'       => 'required|in:0,1',
            'calculate_in_calendar' => 'nullable|in:Yes,No',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        if (! $boxUser) {
            return response()->json([
                'success' => false,
                'message' => 'Box user not found',
            ], 404);
        }

        $holiday = HolidayType::create([
            'box_id'       => $boxUser->box_id,
            'holiday_type' => $request->holiday_type,
            'holiday_code' => $request->holiday_code,
            'is_mandatory' => $request->is_mandatory,
            'max_limit'    => $request->max_limit,
            'status'       => $request->status,
            'calculate_in_calendar' => $request->calculate_in_calendar ?? 'No',
            'created_by'   => $user->id,
        ]);

        if ($holiday->calculate_in_calendar === 'Yes') {
            $this->syncHolidayTypeToLeave($holiday, $boxUser->box_id, $user->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Holiday Type created successfully',
            'data'    => $holiday,
        ]);
    }

    public function holidayUpdate(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canUpdate = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'update', 'holiday_type');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canUpdate) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update holiday types.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'id'           => 'required|exists:holiday_type,id',
            'holiday_type' => 'required|string|max:255',
            'holiday_code' => 'required|string|max:100',
            'is_mandatory' => 'nullable',
            'max_limit'    => 'nullable',
            'status'       => 'required|in:0,1',
            'calculate_in_calendar' => 'nullable|in:Yes,No',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        if (! $boxUser) {
            return response()->json([
                'success' => false,
                'message' => 'Box user not found',
            ], 404);
        }

        $holiday = HolidayType::where('id', $request->id)
            ->where('box_id', $boxUser->box_id)
            ->first();

        if (! $holiday) {
            return response()->json([
                'success' => false,
                'message' => 'Holiday Type not found in your box',
            ], 404);
        }

        $oldHolidayType = $holiday->holiday_type;

        $holiday->update([
            'holiday_type' => $request->holiday_type,
            'holiday_code' => $request->holiday_code,
            'status'       => $request->status,
            'is_mandatory' => $request->is_mandatory,
            'max_limit'    => $request->max_limit,
            'calculate_in_calendar' => $request->calculate_in_calendar ?? 'No',
            'updated_by'   => $user->id,
        ]);

        if (($request->calculate_in_calendar ?? 'No') === 'No') {
            // Find the corresponding Leave Type
            $leaveType = \App\Models\LeaveType::where('box_id', $boxUser->box_id)
                ->where('leave_type', $oldHolidayType)
                ->first();

            if ($leaveType) {
                // Set LeaveType to Inactive instead of deleting
                $leaveType->status = 'Inactive';
                $leaveType->save();

                // Zero out balances for all users with a final transaction
                $allBoxUsers = \App\Models\BoxUser::where('box_id', $boxUser->box_id)->get();
                foreach ($allBoxUsers as $bu) {
                    $lastTransaction = \App\Models\LeaveTransaction::where('box_user_id', $bu->id)
                        ->where('leave_type_id', $leaveType->id)
                        ->orderBy('date', 'desc')
                        ->orderBy('id', 'desc')
                        ->first();
                        
                    if ($lastTransaction && $lastTransaction->balance_leave > 0) {
                        \App\Models\LeaveTransaction::create([
                            'box_id' => $boxUser->box_id,
                            'box_user_id' => $bu->id,
                            'leave_type_id' => $leaveType->id,
                            'date' => now()->toDateString(),
                            'credit_leave' => 0,
                            'debit_leave' => $lastTransaction->balance_leave,
                            'opening_balance' => $lastTransaction->balance_leave,
                            'balance_leave' => 0,
                            'total_leave' => 0,
                            'status' => 'Approved',
                            'transaction_remarks' => 'Balance cleared: Holiday Type no longer calculated in calendar',
                            'applied_date' => now(),
                            'approved_date' => now(),
                            'created_by' => $user->id,
                        ]);
                    }
                }
            }
        } elseif ($oldHolidayType !== $request->holiday_type) {
            // If the name changed and it's still 'Yes', update the Leave Type name
            $leaveType = \App\Models\LeaveType::where('box_id', $boxUser->box_id)
                ->where('leave_type', $oldHolidayType)
                ->first();

            if ($leaveType) {
                $leaveType->leave_type = $request->holiday_type;
                $leaveType->save();
                
                \App\Models\LeaveRequest::where('leave_type', $oldHolidayType)
                    ->where('box_id', $boxUser->box_id)
                    ->update(['leave_type' => $request->holiday_type]);
            }
        }
        
        if (($request->calculate_in_calendar ?? 'No') === 'Yes') {
            $this->syncHolidayTypeToLeave($holiday, $boxUser->box_id, $user->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Holiday Type updated successfully',
            'data'    => $holiday,
        ]);
    }

    public function deleteHoliday(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canDelete = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'delete', 'holiday_type');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canDelete) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete holiday types.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:holiday_type,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        if (! $boxUser) {
            return response()->json([
                'success' => false,
                'message' => 'Box user not found',
            ], 404);
        }

        $holiday = HolidayType::where('id', $request->id)
            ->where('box_id', $boxUser->box_id)
            ->first();

        if (! $holiday) {
            return response()->json([
                'success' => false,
                'message' => 'Holiday Type not found in your box',
            ], 404);
        }

        $holidayTypeStr = $holiday->holiday_type;
        $holiday->deleted_by = $user->id;
        $holiday->save();
        $holiday->delete();

        // Instead of hard deleting, zero out balances and mark LeaveType as Inactive
        $leaveType = \App\Models\LeaveType::where('box_id', $boxUser->box_id)
            ->where('leave_type', $holidayTypeStr)
            ->first();

        if ($leaveType) {
            $leaveType->status = 'Inactive';
            $leaveType->save();

            $allBoxUsers = \App\Models\BoxUser::where('box_id', $boxUser->box_id)->get();
            foreach ($allBoxUsers as $bu) {
                $lastTransaction = \App\Models\LeaveTransaction::where('box_user_id', $bu->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->orderBy('date', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();
                    
                if ($lastTransaction && $lastTransaction->balance_leave > 0) {
                    \App\Models\LeaveTransaction::create([
                        'box_id' => $boxUser->box_id,
                        'box_user_id' => $bu->id,
                        'leave_type_id' => $leaveType->id,
                        'date' => now()->toDateString(),
                        'credit_leave' => 0,
                        'debit_leave' => $lastTransaction->balance_leave,
                        'opening_balance' => $lastTransaction->balance_leave,
                        'balance_leave' => 0,
                        'total_leave' => 0,
                        'status' => 'Approved',
                        'transaction_remarks' => 'Balance cleared: Holiday Type deleted',
                        'applied_date' => now(),
                        'approved_date' => now(),
                        'created_by' => $user->id,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Holiday Type deleted successfully',
        ]);
    }

    private function syncHolidayTypeToLeave($holiday, $boxId, $userId)
    {
        $leaveType = \App\Models\LeaveType::firstOrCreate(
            [
                'box_id' => $boxId,
                'leave_type' => $holiday->holiday_type,
            ],
            [
                'status' => 'Active',
                'number_of_days' => $holiday->max_limit,
                'leave_code' => $holiday->holiday_code,
                'holiday_off' => 0,
                'weekly_off' => 0,
                'overtime_equal_shift' => 0,
                'created_by' => $userId,
                'leave_color' => 'bg-pink-le',
                'leave_icon' => 'ti ti-calendar-check fs-32',
            ]
        );

        $needsUpdate = false;
        if ($leaveType->leave_code !== $holiday->holiday_code) {
            $leaveType->leave_code = $holiday->holiday_code;
            $needsUpdate = true;
        }
        if ($leaveType->status !== 'Active') {
            $leaveType->status = 'Active';
            $needsUpdate = true;
        }
        if ($leaveType->number_of_days != $holiday->max_limit) {
            $leaveType->number_of_days = $holiday->max_limit;
            $needsUpdate = true;
        }
        if (empty($leaveType->leave_color)) {
            $leaveType->leave_color = 'bg-pink-le';
            $needsUpdate = true;
        }
        if (empty($leaveType->leave_icon)) {
            $leaveType->leave_icon = 'ti ti-calendar-check fs-32';
            $needsUpdate = true;
        }
        
        if ($needsUpdate) {
            $leaveType->save();
        }

        // Apply initial transaction to all users in the box
        $allBoxUsers = \App\Models\BoxUser::where('box_id', $boxId)->get();
        foreach ($allBoxUsers as $bu) {
            $hasTransaction = \App\Models\LeaveTransaction::where('box_user_id', $bu->id)
                ->where('leave_type_id', $leaveType->id)
                ->exists();

            if (!$hasTransaction && $holiday->max_limit > 0) {
                \App\Models\LeaveTransaction::create([
                    'box_id' => $boxId,
                    'box_user_id' => $bu->id,
                    'leave_type_id' => $leaveType->id,
                    'date' => now()->toDateString(),
                    'credit_leave' => $holiday->max_limit,
                    'debit_leave' => 0,
                    'opening_balance' => 0,
                    'balance_leave' => $holiday->max_limit,
                    'total_leave' => $holiday->max_limit,
                    'status' => 'Approved',
                    'transaction_remarks' => 'Initial credit for Holiday Type: ' . $holiday->holiday_type,
                    'applied_date' => now(),
                    'approved_date' => now(),
                    'created_by' => $userId,
                ]);
            }
        }
    }
}
