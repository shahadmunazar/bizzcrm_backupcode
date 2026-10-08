<?php

namespace App\Services;

use App\Models\{Box, BoxUser, LeaveType};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Validator};
use Yajra\DataTables\Facades\DataTables;

class LeaveTypeService
{
    public function LeaveTyperequest(Request $request)
    {
        try {
            $user    = Auth::id();
            $boxUser = BoxUser::where('user_id', $user)->select('id', 'box_id')->first();
            if (! $boxUser) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Box user not found',
                ], 404);
            }

            // Sync HolidayType with calculate_in_calendar='Yes' into LeaveType
            $holidayTypes = \App\Models\HolidayType::where('box_id', $boxUser->box_id)
                ->where('calculate_in_calendar', 'Yes')
                ->get();
                
            foreach ($holidayTypes as $ht) {
                $leaveType = \App\Models\LeaveType::firstOrCreate(
                    [
                        'box_id' => $boxUser->box_id,
                        'leave_type' => $ht->holiday_type,
                    ],
                    [
                        'status' => 'Active',
                        'number_of_days' => $ht->max_limit,
                        'leave_code' => $ht->holiday_code,
                        'holiday_off' => 0,
                        'weekly_off' => 0,
                        'overtime_equal_shift' => 0,
                        'created_by' => $user,
                        'leave_color' => 'bg-pink-le',
                        'leave_icon' => 'ti ti-calendar-check fs-32',
                    ]
                );

                $needsUpdate = false;
                if ($leaveType->leave_code !== $ht->holiday_code) {
                    $leaveType->leave_code = $ht->holiday_code;
                    $needsUpdate = true;
                }
                if ($leaveType->number_of_days != $ht->max_limit) {
                    $leaveType->number_of_days = $ht->max_limit;
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

                $hasTransaction = \App\Models\LeaveTransaction::where('box_user_id', $boxUser->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->exists();

                if (!$hasTransaction && $ht->max_limit > 0) {
                    \App\Models\LeaveTransaction::create([
                        'box_id' => $boxUser->box_id,
                        'box_user_id' => $boxUser->id,
                        'leave_type_id' => $leaveType->id,
                        'date' => now()->toDateString(),
                        'credit_leave' => $ht->max_limit,
                        'debit_leave' => 0,
                        'opening_balance' => 0,
                        'balance_leave' => $ht->max_limit,
                        'total_leave' => $ht->max_limit,
                        'status' => 'Approved',
                        'transaction_remarks' => 'Initial credit for Holiday Type: ' . $ht->holiday_type,
                        'applied_date' => now(),
                        'approved_date' => now(),
                        'created_by' => $user,
                    ]);
                }
            }

            $leaveTypes = LeaveType::where('box_id', $boxUser->box_id)->get();
            return response()->json([
                'status'  => true,
                'message' => 'Leave types fetched successfully',
                'data'    => $leaveTypes,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function leave_type_update(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id',$userId)->first();
        if (!$boxUser) {
            return response()->json(['message' => 'Box User not found.'], 404);
        }
        $box    = Box::where('id', $boxUser->box_id)->first();
        if (! $box) {
            return response()->json([
                'message' => 'Box not found for the authenticated user.',
            ], 404);
        }

        $isGlobalAdmin = Auth::user()->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $hasPermission = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'update', 'leave_type');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
             return response()->json([
                'message' => 'You do not have permission to perform this action.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'leave_id'                     => 'required|exists:leave_types,id',
            'leave_type'                   => 'sometimes|string|max:255',
            'credit_frequency'             => 'sometimes|string|nullable',
            'credit_days'                  => 'sometimes|numeric|min:1|max:365|nullable',
            'number_of_days'               => 'sometimes|numeric|min:0|nullable',
            'leave_code'                   => 'sometimes|string|max:255|nullable',
            'status'                       => 'sometimes|in:Active,Inactive',
            'leave_color'                  => 'nullable',
            'leave_icon'                   => 'nullable',
            'holiday_off'                  => 'nullable|boolean',
            'holiday_off_details'          => 'nullable|string|max:255',
            'weekly_off'                   => 'nullable|boolean',
            'weekly_off_details'           => 'nullable|string|max:255',
            'overtime_equal_shift'         => 'nullable|boolean',
            'overtime_equal_shift_details' => 'nullable|string|max:255',
            'carry_forward'                => 'nullable|in:yes,no',
            'carry_forward_days'           => 'required_if:carry_forward,yes|nullable|numeric|min:1|max:365',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        try {
            $leaveType = LeaveType::where('box_id', $box->id)
                ->where('id', $validated['leave_id'])
                ->firstOrFail();

            $errors = [];

            if (! empty($validated['leave_type'])) {
                $checkLeaveTypeIsAlready = LeaveType::where('box_id', $box->id)
                    ->where('leave_type', $validated['leave_type'])
                    ->where('id', '!=', $leaveType->id)
                    ->first();
                if ($checkLeaveTypeIsAlready) {
                    $errors[] = 'Leave type with the same name already exists for this box.';
                }
            }

            $checkFlags = LeaveType::where('box_id', $box->id)
                ->where('id', '!=', $leaveType->id)
                ->where(function ($query) {
                    $query->where('weekly_off', 1)
                        ->orWhere('holiday_off', 1)
                        ->orWhere('overtime_equal_shift', 1);
                })
                ->first();

            if ($checkFlags) {
                if (! empty($validated['weekly_off']) && $validated['weekly_off'] && $checkFlags->weekly_off) {
                    $errors[] = 'A leave type with weekly_off already exists for this box.';
                }
                if (! empty($validated['holiday_off']) && $validated['holiday_off'] && $checkFlags->holiday_off) {
                    $errors[] = 'A leave type with holiday_off already exists for this box.';
                }
                if (! empty($validated['overtime_equal_shift']) && $validated['overtime_equal_shift'] && $checkFlags->overtime_equal_shift) {
                    $errors[] = 'A leave type with overtime_equal_shift already exists for this box.';
                }
            }

            if (! empty($errors)) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors'  => $errors,
                ], 422);
            }

            $updatableFields = [
                'leave_type',
                'credit_frequency',
                'credit_days',
                'number_of_days',
                'leave_code',
                'status',
                'leave_icon',
                'leave_color',
                'holiday_off',
                'holiday_off_details',
                'weekly_off',
                'weekly_off_details',
                'overtime_equal_shift',
                'overtime_equal_shift_details',
                'carry_forward',
                'carry_forward_days',
            ];

            foreach ($updatableFields as $field) {
                if (array_key_exists($field, $validated)) {
                    $leaveType->$field = $validated[$field];
                }
            }

            $anyCompOff = $leaveType->holiday_off || $leaveType->weekly_off || $leaveType->overtime_equal_shift;
            if (! $anyCompOff && ! empty($leaveType->credit_days) && ! empty($leaveType->credit_frequency)) {
                $frequencyMap = [
                    'Monthly'         => 12,
                    'Alternate Month' => 6,
                    'Quarterly'       => 4,
                    'Half Yearly'     => 2,
                    'Yearly'          => 1,
                ];

                $frequencyCount     = $frequencyMap[$leaveType->credit_frequency] ?? 0;
                $expectedTotalLeave = $leaveType->credit_days * $frequencyCount;
                $total_leave        = $leaveType->number_of_days ?? 0;

                if ($total_leave != $expectedTotalLeave) {
                    return response()->json([
                        'message' => 'Invalid leave configuration.',
                        'errors'  => [
                            'number_of_days' => [
                                "The total number of leave days ($total_leave) must equal credit days × frequency per year ($expectedTotalLeave = {$leaveType->credit_days} × $frequencyCount).",
                            ],
                        ],
                    ], 422);
                }
            }

            $leaveType->updated_by = $userId;
            $leaveType->save();

            return response()->json([
                'message' => 'Leave type updated successfully.',
                'data'    => $leaveType,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Leave type not found for this box.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update leave type.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function leave_destroy(Request $request)
    {
        $userId = Auth::id();
        $id     = $request->id;

        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
             return response()->json(['message' => 'Box not found for user.'], 404);
        }
        $box = Box::find($boxUser->box_id);

        if (!$box) {
             return response()->json(['message' => 'Box not found.'], 404);
        }

        $isGlobalAdmin = Auth::user()->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $hasPermission = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'delete', 'leave_type');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
             return response()->json([
                'message' => 'You do not have permission to perform this action.',
            ], 403);
        }

        try {
            $leaveType = LeaveType::where('box_id', $box->id)
                ->where('id', $id)
                ->firstOrFail();

            $leaveType->deleted_by = $userId;
            $leaveType->save();

            $leaveType->delete();

            return response()->json([
                'message' => 'Leave type deleted successfully',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Leave type not found for this tenant',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete leave type',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function getDataLeaveType(Request $request)
    {
        try {
            $userId = Auth::id();
            $boxuser = BoxUser::where('user_id', $userId)->first();
            if (!$boxuser) {
                return response()->json(['message' => 'Box User not found.'], 404);
            }
            $box    = Box::where('id', $boxuser->box_id)->first();
            if (! $box) {
                return response()->json(['message' => 'Box not found.'], 404);
            }
            $query = LeaveType::where('box_id', $box->id);
            if ($request->filled('search_leave')) {
                $search = $request->input('search_leave');
                $query->where(function ($q) use ($search) {
                    $q->where('leave_type', 'like', "%$search%")
                        ->orWhere('credit_frequency', 'like', "%$search%")
                        ->orWhere('credit_days', 'like', "%$search%")
                        ->orWhere('number_of_days', 'like', "%$search%")
                        ->orWhere('leave_code', 'like', "%$search%")
                        ->orWhere('status', 'like', "%$search%");
                });
            }
            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->toJson();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to fetch leave types.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function leave_type_store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'leave_type'                   => 'nullable|string|max:255',
                'credit_frequency'             => 'nullable|string',
                'credit_days'                  => 'nullable|numeric|min:1|max:365',
                'number_of_days'               => 'nullable|numeric|min:0',
                'leave_code'                   => 'nullable|string|max:255',
                'status'                       => 'nullable|in:Active,Inactive',
                'leave_credit_type'            => 'nullable',
                'carry_forward'                => 'nullable|in:yes,no',
                'leave_color'                  => 'nullable',
                'leave_icon'                   => 'nullable',
                'carry_forward_days'           => 'required_if:carry_forward,yes|nullable|numeric|min:1|max:365',
                'holiday_off'                  => 'nullable|boolean',
                'holiday_off_details'          => 'nullable|string|max:255',
                'weekly_off'                   => 'nullable|boolean',
                'weekly_off_details'           => 'nullable|string|max:255',
                'overtime_equal_shift'         => 'nullable|boolean',
                'overtime_equal_shift_details' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $validated                         = $validator->validated();
            $validated['holiday_off']          = $request->boolean('holiday_off', false);
            $validated['weekly_off']           = $request->boolean('weekly_off', false);
            $validated['overtime_equal_shift'] = $request->boolean('overtime_equal_shift', false);

            $user_id = Auth::id();
            $boxUser = BoxUser::where('user_id',$user_id)->first();
            if (!$boxUser) {
                return response()->json(['message' => 'Box User not found.'], 404);
            }
            $box     = Box::where('id', $boxUser->box_id)->first();

            if (! $box) {
                return response()->json([
                    'message' => 'Box not found for the authenticated user.',
                ], 404);
            }

            $isGlobalAdmin = Auth::user()->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            $hasPermission = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'create', 'leave_type');

            if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
                 return response()->json([
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            $errors = [];

            $checkLeaveTypeIsAlready = LeaveType::where('box_id', $box->id)
                ->where('leave_type', $validated['leave_type'])
                ->first();
            if ($checkLeaveTypeIsAlready) {
                $errors[] = 'Leave type with the same name already exists for this box.';
            }

            $checkFlags = LeaveType::where('box_id', $box->id)
                ->where(function ($query) {
                    $query->where('weekly_off', 1)
                        ->orWhere('holiday_off', 1)
                        ->orWhere('overtime_equal_shift', 1);
                })
                ->first();

            if ($checkFlags) {
                if ($validated['weekly_off'] && $checkFlags->weekly_off) {
                    $errors[] = 'A leave type with weekly_off already exists for this box.';
                }
                if ($validated['holiday_off'] && $checkFlags->holiday_off) {
                    $errors[] = 'A leave type with holiday_off already exists for this box.';
                }
                if ($validated['overtime_equal_shift'] && $checkFlags->overtime_equal_shift) {
                    $errors[] = 'A leave type with overtime_equal_shift already exists for this box.';
                }
            }

            if (! empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => implode(' ', $errors),
                ], 422);
            }

            $compOffSelected = $validated['holiday_off'] || $validated['weekly_off'] || $validated['overtime_equal_shift'];

            if ($compOffSelected) {
                $dataToSave = [
                    'leave_type'                   => $validated['leave_type'] ?? null,
                    'leave_code'                   => $validated['leave_code'] ?? null,
                    'status'                       => $validated['status'] ?? 'Active',
                    'leave_color'                  => $validated['leave_color'] ?? null,
                    'leave_icon'                   => $validated['leave_icon'] ?? null,
                    'holiday_off'                  => $validated['holiday_off'],
                    'holiday_off_details'          => $validated['holiday_off_details'] ?? null,
                    'weekly_off'                   => $validated['weekly_off'],
                    'weekly_off_details'           => $validated['weekly_off_details'] ?? null,
                    'overtime_equal_shift'         => $validated['overtime_equal_shift'],
                    'overtime_equal_shift_details' => $validated['overtime_equal_shift_details'] ?? null,
                    'credit_frequency'             => null,
                    'credit_days'                  => null,
                    'number_of_days'               => null,
                    'leave_credit_type'            => null,
                    'carry_forward'                => null,
                    'carry_forward_days'           => null,
                    'credit_applied'               => 0,
                ];
            } else {
                $credit_days      = $validated['credit_days'] ?? 0;
                $credit_frequency = $validated['credit_frequency'] ?? '';
                $total_leave      = $validated['number_of_days'] ?? 0;

                $frequencyCount = match ($credit_frequency) {
                    'Monthly'         => 12,
                    'Alternate Month' => 6,
                    'Quarterly'       => 4,
                    'Half Yearly'     => 2,
                    'Yearly'          => 1,
                    default           => 0,
                };

                $expectedTotalLeave = $frequencyCount * $credit_days;

                if ($total_leave != $expectedTotalLeave) {
                    return response()->json([
                        'message' => 'Invalid leave configuration.',
                        'errors'  => [
                            'number_of_days' => [
                                "The total number of leave days ($total_leave) must equal credit days × frequency per year ($expectedTotalLeave = $credit_days × $frequencyCount).",
                            ],
                        ],
                    ], 422);
                }

                $currentMonth = Carbon::now()->month;
                $final_credit = match ($credit_frequency) {
                    'Monthly'         => $currentMonth * $credit_days,
                    'Alternate Month' => intdiv($currentMonth + 1, 2) * $credit_days,
                    'Quarterly'       => (intdiv($currentMonth - 1, 3) + 1) * $credit_days,
                    'Half Yearly'     => ($currentMonth >= 7 ? 2 : 1) * $credit_days,
                    'Yearly'          => $credit_days,
                    default           => 0,
                };

                $final_credit = min($final_credit, $total_leave);

                $dataToSave                   = $validated;
                $dataToSave['credit_applied'] = $final_credit;
            }

            $dataToSave['box_id']     = $box->id;
            $dataToSave['created_by'] = $user_id;

            LeaveType::create($dataToSave);

            return response()->json([
                'message'        => 'Leave type created successfully.',
                'credit_applied' => $dataToSave['credit_applied'] ?? 0,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'An error occurred while creating leave type.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    public function ToggleLeaveStatus(Request $request)
    {
        $request->validate([
            'id'     => 'required|integer|exists:leave_types,id',
            'status' => 'required|in:Active,Inactive',
        ]);
        $leaveType         = LeaveType::find($request->id);
        $leaveType->status = $request->status;
        $leaveType->save();
        return response()->json(['success' => true, 'message' => 'Leave status updated successfully.']);
    }
}
