<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\BaseController;
use App\Models\{Attendance, Box, BoxUser, Holiday, LeaveRequest, LeaveTransaction, LeaveType};
use App\Jobs\ProcessLeaveToEmployee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log, Storage, Validator};
use Yajra\DataTables\Facades\DataTables;

class LeaveController extends BaseController
{
    public function LeaveRequestStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'leave_type'           => 'required|exists:leave_types,id',
                'from_date'            => 'required|date',
                'to_date'              => 'required|date|after_or_equal:from_date',
                'total_days'           => 'required|numeric',
                'contact_during_leave' => 'nullable|string|max:255',
                'reason'               => 'required|string|max:1000',
                'attachment'           => 'nullable|file',
                'applied_for'          => 'nullable|string',
                'employee_id'          => 'nullable|exists:box_users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $findLeaveType = LeaveType::where('id', $request->leave_type)
                ->select('leave_code', 'leave_type', 'id')
                ->first();

            if (! $findLeaveType) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Leave type not found.',
                ], 404);
            }
            $leaveCode     = !empty($findLeaveType->leave_code) ? $findLeaveType->leave_code : $findLeaveType->leave_type;
            $fromDate      = Carbon::parse($request->from_date)->format('Y-m-d');
            $toDate        = Carbon::parse($request->to_date)->format('Y-m-d');
            $requestedDays = $request->total_days;
            $userId        = Auth::id();

            if ($request->has('employee_id') && !empty($request->employee_id)) {
                $boxUser = BoxUser::find($request->employee_id);
            } else {
                $boxUser = BoxUser::where('user_id', $userId)->select('id', 'box_id')->first();
            }

            if (! $boxUser) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Box user not found.',
                ], 404);
            }

            // Check if all requested dates fall on weekly offs
            $boxUserWithRelations = \App\Models\BoxUser::with(['shift', 'box'])->find($boxUser->id);
            $start = Carbon::parse($fromDate);
            $end   = Carbon::parse($toDate);
            $allWeeklyOffs = true;
            $invalidDayNames = [];
            
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $dayName = ucfirst(strtolower($date->format('l')));
                $isWeeklyOff = false;
                
                if ($boxUserWithRelations && $boxUserWithRelations->shift) {
                    $shift = $boxUserWithRelations->shift;
                    $weeklyOffDays = array_filter([
                        $shift->weekly_off_1 ?? null,
                        $shift->weekly_off_2 ?? null,
                        $shift->weekly_off_3 ?? null,
                    ]);
                    if (in_array($dayName, $weeklyOffDays, true)) {
                        $isWeeklyOff = true;
                    }
                }

                if (!$isWeeklyOff && $boxUserWithRelations) {
                    if (!empty($boxUserWithRelations->working_days)) {
                        $userWorkingDays = is_array($boxUserWithRelations->working_days)
                            ? $boxUserWithRelations->working_days
                            : (json_decode($boxUserWithRelations->working_days, true) ?? explode(',', $boxUserWithRelations->working_days));
                        $userWorkingDays = array_map('trim', $userWorkingDays);
                        if (in_array($dayName, $userWorkingDays)) {
                            $isWeeklyOff = false;
                        } else {
                            $isWeeklyOff = true;
                        }
                    } elseif ($boxUserWithRelations->box && !empty($boxUserWithRelations->box->working_days)) {
                        $boxWorkingDays = is_array($boxUserWithRelations->box->working_days)
                            ? $boxUserWithRelations->box->working_days
                            : (json_decode($boxUserWithRelations->box->working_days, true) ?? explode(',', $boxUserWithRelations->box->working_days));
                        $boxWorkingDays = array_map('trim', $boxWorkingDays);
                        if (in_array($dayName, $boxWorkingDays)) {
                            $isWeeklyOff = false;
                        } else {
                            $isWeeklyOff = true;
                        }
                    }
                }
                
                if (!$isWeeklyOff) {
                    $allWeeklyOffs = false;
                    break;
                } else {
                    $invalidDayNames[] = $dayName;
                }
            }

            if ($allWeeklyOffs) {
                $dayNamesStr = implode(', ', array_unique($invalidDayNames));
                return response()->json([
                    'status'  => false,
                    'message' => "You cannot apply for leave on your weekly off ($dayNamesStr).",
                ], 422);
            }
            
            // Recalculate requested days if it's a multi-day leave
            if ($fromDate != $toDate) {
                $actualDays = 0;
                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                    $dayName = ucfirst(strtolower($date->format('l')));
                    $isWeeklyOff = false;
                    
                    if ($boxUserWithRelations && $boxUserWithRelations->shift) {
                        $shift = $boxUserWithRelations->shift;
                        $weeklyOffDays = array_filter([
                            $shift->weekly_off_1 ?? null,
                            $shift->weekly_off_2 ?? null,
                            $shift->weekly_off_3 ?? null,
                        ]);
                        if (in_array($dayName, $weeklyOffDays, true)) {
                            $isWeeklyOff = true;
                        }
                    }

                    if (!$isWeeklyOff && $boxUserWithRelations) {
                        if (!empty($boxUserWithRelations->working_days)) {
                            $userWorkingDays = is_array($boxUserWithRelations->working_days)
                                ? $boxUserWithRelations->working_days
                                : (json_decode($boxUserWithRelations->working_days, true) ?? explode(',', $boxUserWithRelations->working_days));
                            $userWorkingDays = array_map('trim', $userWorkingDays);
                            if (in_array($dayName, $userWorkingDays)) {
                                $isWeeklyOff = false;
                            } else {
                                $isWeeklyOff = true;
                            }
                        } elseif ($boxUserWithRelations->box && !empty($boxUserWithRelations->box->working_days)) {
                            $boxWorkingDays = is_array($boxUserWithRelations->box->working_days)
                                ? $boxUserWithRelations->box->working_days
                                : (json_decode($boxUserWithRelations->box->working_days, true) ?? explode(',', $boxUserWithRelations->box->working_days));
                            $boxWorkingDays = array_map('trim', $boxWorkingDays);
                            if (in_array($dayName, $boxWorkingDays)) {
                                $isWeeklyOff = false;
                            } else {
                                $isWeeklyOff = true;
                            }
                        }
                    }
                    
                    if (!$isWeeklyOff) {
                        $actualDays++;
                    }
                }
                $requestedDays = $actualDays;
            }

            $overlappingLeave = LeaveRequest::where('box_user_id', $boxUser->id)
                ->whereIn('status', ['pending', 'approved'])
                ->where('from_date', '<=', $toDate)
                ->where('to_date', '>=', $fromDate)
                ->exists();

            if ($overlappingLeave) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Leave already applied for one or more selected dates.',
                ], 422);
            }

            $leaveTypeId       = $request->leave_type;
            $latestTransaction = LeaveTransaction::where('leave_type_id', $leaveTypeId)
                ->where('box_user_id', $boxUser->id)
                ->latest('id')
                ->latest('created_at')
                ->first();

            $latestBalance = $latestTransaction ? $latestTransaction->balance_leave : 0;

            $holidayType = \App\Models\HolidayType::where('box_id', $boxUser->box_id)
                ->where('holiday_type', $findLeaveType->leave_type)
                ->where('calculate_in_calendar', 'Yes')
                ->first();

            if ($holidayType) {
                $usedDays = \App\Models\LeaveTransaction::where('box_id', $boxUser->box_id)
                    ->where('box_user_id', $boxUser->id)
                    ->where('leave_type_id', $leaveTypeId)
                    ->where('debit_leave', '>', 0)
                    ->sum('debit_leave');
                
                $maxLimit = $holidayType->max_limit;
                $availableBalance = max(0, $maxLimit - $usedDays);

                if ($requestedDays > $availableBalance) {
                    return response()->json([
                        'status'  => false,
                        'message' => "You cannot request more than your available maximum limit of {$availableBalance} days for this Holiday Type.",
                    ], 422);
                }

                $latestBalance = $availableBalance;
            }

            $paidDays   = min($requestedDays, $latestBalance);
            $unpaidDays = max($requestedDays - $latestBalance, 0);

            if ($unpaidDays > 0 && ! $request->has('proceed_as_unpaid')) {
                return response()->json([
                    'status'      => false,
                    'extra_leave' => true,
                    'message'     => "You have {$latestBalance} paid days. {$unpaidDays} days will be unpaid.",
                    'requested'  => $requestedDays,
                    'available'  => $latestBalance,
                    'difference' => $unpaidDays,
                ], 422);
            }

            DB::transaction(function () use (
                $boxUser, $userId, $leaveTypeId, $requestedDays, $fromDate, $toDate,
                $request, $paidDays, $unpaidDays, $latestTransaction, $leaveCode, $latestBalance
            ) {
                // Save leave request
                $leaveRequest                       = new LeaveRequest();
                $leaveRequest->box_id               = $boxUser->box_id;
                $leaveRequest->box_user_id          = $boxUser->id;
                $leaveRequest->leave_type           = $leaveTypeId;
                $leaveRequest->from_date            = $fromDate;
                $leaveRequest->to_date              = $toDate;
                $leaveRequest->paid_days            = $paidDays;
                $leaveRequest->unpaid_days          = $unpaidDays;
                $leaveRequest->total_days           = $paidDays + $unpaidDays;
                $leaveRequest->contact_during_leave = $request->contact_during_leave;
                $leaveRequest->reason               = $request->reason;
                $leaveRequest->status               = 'pending';
                $leaveRequest->applied_for          = $request->applied_for;
                $leaveRequest->created_by           = $userId;

                if ($request->hasFile('attachment')) {
                    $file                     = $request->file('attachment');
                    $fileName                 = time() . '_' . $file->getClientOriginalName();
                    $filePath                 = $file->storeAs('attachments/leaves', $fileName, 'public');
                    $leaveRequest->attachment = $filePath;
                }
                $leaveRequest->save();

                // Attendance records
                $start = Carbon::parse($fromDate);
                $end   = Carbon::parse($toDate);
                for ($date = $start; $date->lte($end); $date->addDay()) {
                    $attendanceDate = $date->format('Y-m-d');
                    Attendance::updateOrCreate(
                        [
                            'box_user_id'     => $boxUser->id,
                            'attendance_date' => $attendanceDate,
                        ],
                        [
                            'box_id'          => $boxUser->box_id,
                            'user_id'         => $userId,
                            'status'          => $leaveCode . '-pending',
                            'created_by'      => $userId,
                            'check_in'        => null,
                            'check_out'       => null,
                            'is_auto_punch'   => 0,
                            'is_manual_entry' => 1,
                            'reason'          => $request->reason,
                        ]
                    );
                }

                if ($paidDays > 0) {
                    $newBalance    = $latestBalance - $paidDays;
                    $newTotalLeave = $latestTransaction ? $latestTransaction->total_leave - $paidDays : 0;

                    LeaveTransaction::create([
                        'box_id'              => $boxUser->box_id,
                        'box_user_id'         => $boxUser->id,
                        'leave_type_id'       => $leaveTypeId,
                        'date'                => now()->toDateString(),
                        'leave_request_id'    => $leaveRequest->id,
                        'credit_leave'        => 0,
                        'debit_leave'         => $paidDays,
                        'opening_balance'     => $latestBalance,
                        'balance_leave'       => $newBalance,
                        'total_leave'         => $newTotalLeave,
                        'status'              => 'pending',
                        'transaction_remarks' => 'Paid leave - ' . $leaveCode,
                        'applied_date'        => now(),
                        'approved_date'       => null,
                    ]);
                }

                if ($unpaidDays > 0) {
                    LeaveTransaction::create([
                        'box_id'              => $boxUser->box_id,
                        'box_user_id'         => $boxUser->id,
                        'leave_type_id'       => $leaveTypeId,
                        'date'                => now()->toDateString(),
                        'leave_request_id'    => $leaveRequest->id,
                        'credit_leave'        => 0,
                        'debit_leave'         => $unpaidDays,
                        'opening_balance'     => 0,
                        'balance_leave'       => 0,
                        'total_leave'         => 0,
                        'status'              => 'pending',
                        'transaction_remarks' => 'Unpaid leave - ' . $leaveCode,
                        'applied_date'        => now(),
                        'approved_date'       => null,
                    ]);
                }
            });
            return response()->json([
                'status'  => true,
                'message' => 'Leave request submitted successfully.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function LeaveRequestUpdate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'leave_request_id'     => 'required|exists:leave_requests,id',
                'leave_type'           => 'required|exists:leave_types,id',
                'from_date'            => 'required|date',
                'to_date'              => 'required|date|after_or_equal:from_date',
                'total_days'           => 'required|numeric',
                'contact_during_leave' => 'nullable|string|max:255',
                'reason'               => 'required|string|max:1000',
                'attachment'           => 'nullable|file',
                'applied_for'          => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->select('id', 'box_id')->firstOrFail();

            $leaveRequest = LeaveRequest::findOrFail($request->leave_request_id);
            $leaveTypeId  = $request->leave_type;

            $leaveType = LeaveType::where('id', $leaveTypeId)->select('id', 'leave_code')->first();
            if (! $leaveType) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Leave type not found.',
                ], 404);
            }
            $leaveCode = !empty($leaveType->leave_code) ? $leaveType->leave_code : $leaveType->leave_type;

            $fromDate      = Carbon::parse($request->from_date)->format('Y-m-d');
            $toDate        = Carbon::parse($request->to_date)->format('Y-m-d');
            $requestedDays = floatval($request->total_days);

            $hasOverlap = LeaveRequest::where('box_user_id', $boxUser->id)
                ->where('id', '!=', $leaveRequest->id)
                ->whereIn('status', ['pending', 'approved'])
                ->where('from_date', '<=', $toDate)
                ->where('to_date', '>=', $fromDate)
                ->exists();

            if ($hasOverlap) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Leave already applied for one or more selected dates.',
                ], 422);
            }

            $latestTransaction = LeaveTransaction::where('leave_type_id', $leaveTypeId)
                ->where('box_user_id', $boxUser->id)
                ->latest('id')
                ->latest('created_at')
                ->first();

            $availableBalance = $latestTransaction
                ? $latestTransaction->balance_leave + $leaveRequest->total_days
                : $leaveRequest->total_days;

            $holidayType = \App\Models\HolidayType::where('box_id', $boxUser->box_id)
                ->where('holiday_type', $leaveType->leave_type)
                ->where('calculate_in_calendar', 'Yes')
                ->first();

            if ($holidayType) {
                $usedDays = \App\Models\LeaveTransaction::where('box_id', $boxUser->box_id)
                    ->where('box_user_id', $boxUser->id)
                    ->where('leave_type_id', $leaveTypeId)
                    ->where('debit_leave', '>', 0)
                    ->where('leave_request_id', '!=', $leaveRequest->id)
                    ->sum('debit_leave');
                
                $maxLimit = $holidayType->max_limit;
                $availableBalance = max(0, $maxLimit - $usedDays);

                if ($requestedDays > $availableBalance) {
                    return response()->json([
                        'status'  => false,
                        'message' => "You cannot request more than your available maximum limit of {$availableBalance} days for this Holiday Type.",
                    ], 422);
                }
            } else {
                if ($requestedDays > $availableBalance) {
                    return response()->json([
                        'status'  => false,
                        'message' => "Insufficient leave balance. You have {$availableBalance} day(s) available.",
                    ], 422);
                }
            }

            DB::transaction(function () use (
                $request, $boxUser, $userId, $leaveRequest, $fromDate, $toDate, $leaveTypeId,
                $requestedDays, $leaveCode, $latestTransaction
            ) {
                $oldFromDate  = $leaveRequest->from_date;
                $oldToDate    = $leaveRequest->to_date;
                $oldTotalDays = floatval($leaveRequest->total_days);
                $oldLeaveType = $leaveRequest->leave_type;

                $changesDetected = (
                    $leaveTypeId != $oldLeaveType ||
                    $fromDate !== $oldFromDate ||
                    $toDate !== $oldToDate ||
                    $requestedDays != $oldTotalDays
                );

                $oldDates = collect(Carbon::parse($oldFromDate)->daysUntil(Carbon::parse($oldToDate)->addDay()))->map->toDateString()->toArray();
                $newDates = collect(Carbon::parse($fromDate)->daysUntil(Carbon::parse($toDate)->addDay()))->map->toDateString()->toArray();

                $datesToRemove = array_diff($oldDates, $newDates);
                $datesToAdd    = array_diff($newDates, $oldDates);

                if (! empty($datesToRemove)) {
                    Attendance::where('box_user_id', $boxUser->id)
                        ->whereIn('attendance_date', $datesToRemove)
                        ->delete();
                }

                foreach ($datesToAdd as $date) {
                    Attendance::updateOrCreate(
                        [
                            'box_user_id'     => $boxUser->id,
                            'attendance_date' => $date,
                        ],
                        [
                            'box_id'          => $boxUser->box_id,
                            'user_id'         => $userId,
                            'status'          => 'pending-' . $leaveCode,
                            'created_by'      => $userId,
                            'check_in'        => null,
                            'check_out'       => null,
                            'is_auto_punch'   => 0,
                            'is_manual_entry' => 1,
                            'reason'          => $request->reason,
                        ]
                    );
                }

                $leaveRequest->update([
                    'leave_type'           => $leaveTypeId,
                    'from_date'            => $fromDate,
                    'to_date'              => $toDate,
                    'total_days'           => $requestedDays,
                    'contact_during_leave' => $request->contact_during_leave,
                    'reason'               => $request->reason,
                    'applied_for'          => $request->applied_for,
                    'status'               => 'pending',
                ]);

                if ($request->hasFile('attachment')) {
                    if ($leaveRequest->attachment && Storage::disk('public')->exists($leaveRequest->attachment)) {
                        Storage::disk('public')->delete($leaveRequest->attachment);
                    }
                    $file     = $request->file('attachment');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('attachments/leaves', $fileName, 'public');
                    $leaveRequest->update(['attachment' => $filePath]);
                }

                if ($changesDetected) {
                    $existingTransaction = LeaveTransaction::where('leave_request_id', $leaveRequest->id)->first();

                    if ($leaveTypeId != $oldLeaveType) {
                        LeaveTransaction::create([
                            'box_id'              => $boxUser->box_id,
                            'box_user_id'         => $boxUser->id,
                            'leave_type_id'       => $oldLeaveType,
                            'leave_request_id'    => $leaveRequest->id,
                            'date'                => now()->toDateString(),
                            'credit_leave'        => $oldTotalDays,
                            'debit_leave'         => 0,
                            'opening_balance'     => $latestTransaction ? $latestTransaction->balance_leave : 0,
                            'balance_leave'       => $latestTransaction ? $latestTransaction->balance_leave + $oldTotalDays : $oldTotalDays,
                            'total_leave'         => $latestTransaction ? $latestTransaction->total_leave + $oldTotalDays : $oldTotalDays,
                            'status'              => 'pending',
                            'transaction_remarks' => 'Leave type changed - refund',
                            'applied_date'        => now(),
                            'approved_date'       => null,
                        ]);
                        LeaveTransaction::create([
                            'box_id'              => $boxUser->box_id,
                            'box_user_id'         => $boxUser->id,
                            'leave_type_id'       => $leaveTypeId,
                            'leave_request_id'    => $leaveRequest->id,
                            'date'                => now()->toDateString(),
                            'credit_leave'        => 0,
                            'debit_leave'         => $requestedDays,
                            'opening_balance'     => $latestTransaction ? $latestTransaction->balance_leave : 0,
                            'balance_leave'       => $latestTransaction ? $latestTransaction->balance_leave - $requestedDays : 0 - $requestedDays,
                            'total_leave'         => $latestTransaction ? $latestTransaction->total_leave - $requestedDays : 0 - $requestedDays,
                            'status'              => 'pending',
                            'transaction_remarks' => 'Leave type changed - debit',
                            'applied_date'        => now(),
                            'approved_date'       => null,
                        ]);
                    } else {
                        $openingBalance = $latestTransaction ? $latestTransaction->balance_leave + $oldTotalDays : $oldTotalDays;
                        $newBalance     = $openingBalance - $requestedDays;
                        $diffDays       = $requestedDays - $oldTotalDays;
                        $newTotalLeave  = $latestTransaction ? $latestTransaction->total_leave - $diffDays : 0;

                        if ($existingTransaction) {
                            $existingTransaction->update([
                                'credit_leave'        => $diffDays < 0 ? abs($diffDays) : 0,
                                'debit_leave'         => $diffDays > 0 ? $diffDays : 0,
                                'opening_balance'     => $openingBalance,
                                'balance_leave'       => $newBalance,
                                'total_leave'         => $newTotalLeave,
                                'status'              => 'pending',
                                'transaction_remarks' => 'Updated leave request',
                                'applied_date'        => now(),
                                'approved_date'       => null,
                            ]);
                        } else {
                            LeaveTransaction::create([
                                'box_id'              => $boxUser->box_id,
                                'box_user_id'         => $boxUser->id,
                                'leave_type_id'       => $leaveTypeId,
                                'leave_request_id'    => $leaveRequest->id,
                                'date'                => now()->toDateString(),
                                'credit_leave'        => $diffDays < 0 ? abs($diffDays) : 0,
                                'debit_leave'         => $diffDays > 0 ? $diffDays : 0,
                                'opening_balance'     => $openingBalance,
                                'balance_leave'       => $newBalance,
                                'total_leave'         => $newTotalLeave,
                                'status'              => 'pending',
                                'transaction_remarks' => 'New transaction for updated request',
                                'applied_date'        => now(),
                                'approved_date'       => null,
                            ]);
                        }
                    }
                }
            });

            return response()->json([
                'status'        => true,
                'message'       => 'Leave request updated successfully.',
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function LeaveRequestDelete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:leave_requests,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $userId = Auth::id();
            $leaveRequest = LeaveRequest::findOrFail($request->id);
            $boxUser = BoxUser::where('user_id', $userId)->select('id', 'box_id')->first();
            if (! $boxUser) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Box user not found',
                ], 404);
            }

            $oldFromDate  = Carbon::parse($leaveRequest->from_date)->format('Y-m-d');
            $oldToDate    = Carbon::parse($leaveRequest->to_date)->format('Y-m-d');
            $oldTotalDays = floatval($leaveRequest->total_days);
            $oldLeaveType = $leaveRequest->leave_type;

            $oldDates = collect(range(0, Carbon::parse($oldFromDate)->diffInDays($oldToDate)))
                ->map(fn($i) => Carbon::parse($oldFromDate)->addDays($i)->format('Y-m-d'))->toArray();

            $leaveTransaction = LeaveTransaction::where('leave_request_id', $leaveRequest->id)
                ->where('box_user_id', $boxUser->id)
                ->orderByDesc('id')
                ->first();

            $leaveType = LeaveType::find($oldLeaveType);
            $leaveCode = $leaveType ? (!empty($leaveType->leave_code) ? $leaveType->leave_code : $leaveType->leave_type) : 'Unknown';

            DB::transaction(function () use (
                $leaveRequest, $boxUser, $oldDates, $oldTotalDays, $oldLeaveType, $leaveTransaction, $leaveCode, $userId
            ) {
                if (! empty($oldDates)) {
                    $boxUserWithRelations = \App\Models\BoxUser::with(['shift', 'box'])->find($boxUser->id);
                    
                    foreach ($oldDates as $dateStr) {
                        $date = \Carbon\Carbon::parse($dateStr);
                        $dayName = ucfirst(strtolower($date->format('l')));
                        
                        $isHoliday = false;
                        $holiday = \App\Models\Holiday::with('holidayType')
                            ->where('box_id', $boxUser->box_id)
                            ->whereDate('date', $dateStr)
                            ->first();
                            
                        if ($holiday) {
                            if ($holiday->holidayType) {
                                $isHoliday = (strcasecmp($holiday->holidayType->calculate_in_calendar, 'Yes') === 0);
                            } else {
                                $isHoliday = true;
                            }
                        }

                        $isWeeklyOff = false;
                        if ($boxUserWithRelations && $boxUserWithRelations->shift) {
                            $shift = $boxUserWithRelations->shift;
                            $weeklyOffDays = array_filter([
                                $shift->weekly_off_1 ?? null,
                                $shift->weekly_off_2 ?? null,
                                $shift->weekly_off_3 ?? null,
                            ]);
                            if (in_array($dayName, $weeklyOffDays, true)) {
                                $isWeeklyOff = true;
                            }
                        }

                        if (!$isWeeklyOff && $boxUserWithRelations) {
                            if (!empty($boxUserWithRelations->working_days)) {
                                $userWorkingDays = is_array($boxUserWithRelations->working_days)
                                    ? $boxUserWithRelations->working_days
                                    : (json_decode($boxUserWithRelations->working_days, true) ?? explode(',', $boxUserWithRelations->working_days));
                                $userWorkingDays = array_map('trim', $userWorkingDays);
                                if (in_array($dayName, $userWorkingDays)) {
                                    $isWeeklyOff = false;
                                } else {
                                    $isWeeklyOff = true;
                                }
                            } elseif ($boxUserWithRelations->box && !empty($boxUserWithRelations->box->working_days)) {
                                $boxWorkingDays = is_array($boxUserWithRelations->box->working_days)
                                    ? $boxUserWithRelations->box->working_days
                                    : (json_decode($boxUserWithRelations->box->working_days, true) ?? explode(',', $boxUserWithRelations->box->working_days));
                                $boxWorkingDays = array_map('trim', $boxWorkingDays);
                                if (in_array($dayName, $boxWorkingDays)) {
                                    $isWeeklyOff = false;
                                } else {
                                    $isWeeklyOff = true;
                                }
                            }
                        }

                        $status = 'absent';
                        if ($isHoliday) {
                            $status = 'holiday';
                        } elseif ($isWeeklyOff) {
                            $status = 'weekly off';
                        }

                        \App\Models\Attendance::updateOrCreate(
                            [
                                'box_user_id'     => $boxUser->id,
                                'attendance_date' => $dateStr,
                            ],
                            [
                                'box_id'          => $boxUser->box_id,
                                'status'          => $status,
                                'user_id'         => $userId,
                            ]
                        );
                    }
                }

                $openingBalance = $leaveTransaction ? $leaveTransaction->balance_leave : 0;
                $newBalance     = $openingBalance + $oldTotalDays;
                $newTotalLeave  = $leaveTransaction ? $leaveTransaction->total_leave + $oldTotalDays : $oldTotalDays;

                LeaveTransaction::create([
                    'box_id'              => $boxUser->box_id,
                    'box_user_id'         => $boxUser->id,
                    'leave_type_id'       => $oldLeaveType,
                    'leave_request_id'    => $leaveRequest->id,
                    'date'                => now()->toDateString(),
                    'credit_leave'        => $oldTotalDays,
                    'debit_leave'         => 0,
                    'opening_balance'     => $openingBalance,
                    'balance_leave'       => $newBalance,
                    'total_leave'         => $newTotalLeave,
                    'status'              => 'pending',
                    'transaction_remarks' => 'Deleted leave reversed - ' . $leaveCode,
                    'applied_date'        => $leaveRequest->created_at,
                    'approved_date'       => now(),
                ]);

                $leaveRequest->delete();
            });

            return response()->json([
                'status'  => true,
                'message' => 'Leave request deleted and days credited back successfully.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function GetLeaveBlance(Request $request)
    {
        try {
            $user    = Auth::id();
            
            if ($request->has('employee_id') && !empty($request->employee_id)) {
                $boxUser = BoxUser::find($request->employee_id);
            } else {
                $boxUser = BoxUser::where('user_id', $user)->select('id', 'box_id')->first();
            }

            if (! $boxUser) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Box user not found.',
                ], 404);
            }
            $leaveTypeId = $request->input('leave_type_id');
            if (! $leaveTypeId) {
                return response()->json([
                    'status'  => false,
                    'message' => 'leave_type_id is required.',
                ], 422);
            }
            $latestTransaction = LeaveTransaction::where('leave_type_id', $leaveTypeId)
                ->where('box_user_id', $boxUser->id)
                ->orderByDesc('id')
                ->first();
            if (! $latestTransaction) {
                return response()->json([
                    'status'  => false,
                    'message' => 'No leave transactions found.',
                ], 404);
            }
            return response()->json([
                'status'  => true,
                'message' => 'Latest leave balance fetched successfully.',
                'data'    => [
                    'opening_balance' => $latestTransaction->opening_balance,
                    'credit_leave'    => $latestTransaction->credit_leave,
                    'debit_leave'     => $latestTransaction->debit_leave,
                    'balance_leave'   => $latestTransaction->balance_leave,
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function UpdateAdminRequest(Request $request)
    {
        try {
            $request->validate([
                'leave_id' => 'required|exists:leave_requests,id',
                'status'   => 'required|string|in:approved,rejected',
                'reason'   => 'required_if:status,rejected|nullable|string|max:1000',
            ]);
            $userId       = Auth::id();
            $leaveRequest = LeaveRequest::findOrFail($request->leave_id);
            $leaveType    = LeaveType::find($leaveRequest->leave_type);
            if (! $leaveType) {
                return response()->json(['message' => 'Leave type not found.'], 404);
            }
            $leaveCode     = !empty($leaveType->leave_code) ? $leaveType->leave_code : $leaveType->leave_type;
            $newStatus     = strtolower($request->status);
            $currentStatus = strtolower($leaveRequest->status);
            $fromDate      = Carbon::parse($leaveRequest->from_date)->format('Y-m-d');
            $toDate        = Carbon::parse($leaveRequest->to_date)->format('Y-m-d');
            $totalDays     = floatval($leaveRequest->total_days);
            $boxUserId     = $leaveRequest->box_user_id;
            $boxId         = $leaveRequest->box_id;
            $leaveTypeId   = $leaveRequest->leave_type;
            $leaveUserId   = $leaveRequest->user_id;

            if ($currentStatus === $newStatus) {
                return response()->json(['message' => 'Leave is already ' . ucfirst($newStatus) . '.'], 400);
            }

            DB::transaction(function () use (
                $newStatus, $userId, $leaveRequest, $fromDate, $toDate, $totalDays,
                $boxUserId, $boxId, $leaveTypeId, $leaveUserId, $request, $leaveCode
            ) {
                $dateRange = collect(range(0, Carbon::parse($fromDate)->diffInDays($toDate)))
                    ->map(fn($i) => Carbon::parse($fromDate)->addDays($i)->format('Y-m-d'));
                $lastTransaction = LeaveTransaction::where('box_id', $boxId)
                     ->where('leave_type_id', $leaveTypeId)
                     ->where('box_user_id', $boxUserId)
                     ->orderByDesc('id')
                     ->first();
                $openingBalance    = $lastTransaction ? $lastTransaction->balance_leave : 0;
                $openingTotalLeave = $lastTransaction ? $lastTransaction->total_leave : 0;

                if ($newStatus === 'rejected') {
                    Attendance::where('box_user_id', $boxUserId)
                        ->whereBetween('attendance_date', [$fromDate, $toDate])
                        ->delete();
                    LeaveTransaction::create([
                        'box_user_id'         => $boxUserId,
                        'box_id'              => $boxId,
                        'leave_type_id'       => $leaveTypeId,
                        'date'                => now()->toDateString(),
                        'credit_leave'        => $totalDays,
                        'debit_leave'         => 0,
                        'opening_balance'     => $openingBalance,
                        'balance_leave'       => $openingBalance + $totalDays,
                        'total_leave'         => $openingTotalLeave + $totalDays,
                        'status'              => 'rejected',
                        'transaction_remarks' => "Leave rejected - {$leaveCode}" . ($request->reason ? " (Reason: {$request->reason})" : ""),
                        'applied_date'        => $leaveRequest->created_at,
                        'approved_date'       => now(),
                    ]);
                }
                if ($newStatus === 'approved') {
                    foreach ($dateRange as $date) {
                        Attendance::updateOrCreate(
                            [
                                'box_user_id'     => $boxUserId,
                                'attendance_date' => $date,
                            ],
                            [
                                'box_id'          => $boxId,
                                'user_id'         => $leaveUserId,
                                'status'          => $leaveCode . '-approved',
                                'check_in'        => null,
                                'check_out'       => null,
                                'is_auto_punch'   => 0,
                                'is_manual_entry' => 1,
                                'reason'          => $leaveRequest->reason,
                                'created_by'      => $userId,
                            ]
                        );
                    }
                    if ($lastTransaction) {
                        $lastTransaction->update([
                            'status'              => 'approved',
                            'transaction_remarks' => 'Leave approved - ' . $leaveCode . ($request->reason ? " (Remarks: {$request->reason})" : ""),
                            'approved_date'       => now(),
                        ]);
                    }
                }
                $leaveRequest->update([
                    'status'      => ucfirst($newStatus),
                    'approved_by' => $userId,
                    'reason'      => $request->reason ?? $leaveRequest->reason,
                ]);
            });
            return response()->json(['message' => 'Leave status updated successfully.']);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to update leave status.', 'error' => $th->getMessage()], 500);
        }
    }

    public function CreditLeaves(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate(['month' => 'required|date_format:Y-m']);
            $processDate = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
            ProcessLeaveToEmployee::dispatch($processDate);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Leave credit job dispatched successfully for ' . $processDate->format('F Y') . '.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Error: ' . $th->getMessage()], 500);
        }
    }

    public function getEmployeeRequestData(Request $request)
    {
        try {
            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();
            if (! $boxUser) {
                return response()->json(['error' => 'No box user found.'], 404);
            }
            $leaveTypeFilter = $request->input('leave_type');
            $statusFilter    = $request->input('status');

            $query = LeaveRequest::with([
                'boxUser:id,user_id,department,designation,profile_image_path,employee_id',
                'boxUser.user:id,name',
                'boxUser.departmentRelation:id,name',
                'boxUser.designationRelation:id,name',
                'leaveType:id,leave_type',
                'approvedByUser:id,name',
            ])->where('box_user_id', $boxUser->id)->latest();

            $dateRange = $request->input('date_range');
            if (! empty($dateRange) && str_contains($dateRange, ' - ')) {
                [$start, $end] = explode(' - ', $dateRange);
                try {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($start))->format('Y-m-d');
                    $endDate   = Carbon::createFromFormat('d/m/Y', trim($end))->format('Y-m-d');
                    $query->whereBetween('from_date', [$startDate, $endDate]);
                } catch (\Exception $e) {
                    // Ignore invalid date format
                }
            }

            if (! empty($leaveTypeFilter)) { $query->where('leave_type', $leaveTypeFilter); }
            if (! empty($statusFilter)) { $query->where('status', $statusFilter); }

            $summaryQuery = clone $query;
            $summary = [
                'total'    => (clone $summaryQuery)->sum('total_days'),
                'approved' => (clone $summaryQuery)->where('status', 'approved')->sum('total_days'),
                'pending'  => (clone $summaryQuery)->where('status', 'pending')->sum('total_days'),
                'rejected' => (clone $summaryQuery)->whereIn('status', ['declined', 'rejected'])->sum('total_days'),
            ];

            // Comparison Stats (e.g., vs Last Month)
            $prevSummary = ['total' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0];
            $comparisonLabel = 'Last Month';
            
            $prevQuery = LeaveRequest::where('box_user_id', $boxUser->id);
            if (! empty($leaveTypeFilter)) { $prevQuery->where('leave_type', $leaveTypeFilter); }

            if (! empty($dateRange) && str_contains($dateRange, ' - ')) {
                [$start, $end] = explode(' - ', $dateRange);
                try {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($start));
                    $endDate   = Carbon::createFromFormat('d/m/Y', trim($end));
                    $daysDiff  = $startDate->diffInDays($endDate) + 1;
                    
                    $prevStart = (clone $startDate)->subDays($daysDiff);
                    $prevEnd   = (clone $startDate)->subDay();
                    
                    $prevQuery->whereBetween('from_date', [$prevStart->format('Y-m-d'), $prevEnd->format('Y-m-d')]);
                    $comparisonLabel = 'Previous ' . $daysDiff . ' Days';
                } catch (\Exception $e) {}
            } else {
                // Default: Compare This Month vs last Month
                $prevStart = now()->subMonth()->startOfMonth();
                $prevEnd   = now()->subMonth()->endOfMonth();
                $prevQuery->whereBetween('from_date', [$prevStart->format('Y-m-d'), $prevEnd->format('Y-m-d')]);
            }

            $prevSummary = [
                'total'    => (clone $prevQuery)->sum('total_days'),
                'approved' => (clone $prevQuery)->where('status', 'approved')->sum('total_days'),
                'pending'  => (clone $prevQuery)->where('status', 'pending')->sum('total_days'),
                'rejected' => (clone $prevQuery)->whereIn('status', ['declined', 'rejected'])->sum('total_days'),
            ];

            $calculateGrowth = function($current, $previous) {
                if ($previous == 0) return $current > 0 ? 100 : 0;
                return round((($current - $previous) / $previous) * 100, 2);
            };

            $growth = [
                'total'    => $calculateGrowth($summary['total'], $prevSummary['total']),
                'approved' => $calculateGrowth($summary['approved'], $prevSummary['approved']),
                'pending'  => $calculateGrowth($summary['pending'], $prevSummary['pending']),
                'rejected' => $calculateGrowth($summary['rejected'], $prevSummary['rejected']),
            ];

            // Chart Data ... (rest of the chart logic remains same)
            $year = $request->input('year', date('Y'));
            $chartDataRaw = LeaveRequest::where('box_user_id', $boxUser->id)
                ->whereYear('from_date', $year)
                ->get();
            $leaveTypes = LeaveType::where('box_id', $boxUser->box_id)->get();
            $formattedSeries = [];
            foreach ($leaveTypes as $type) {
                $data = [];
                for ($m = 1; $m <= 12; $m++) {
                    $count = $chartDataRaw->filter(function ($leave) use ($m, $type) {
                        return Carbon::parse($leave->from_date)->month == $m && $leave->leave_type == $type->id;
                    })->sum('total_days');
                    $data[] = (float)$count;
                }
                $formattedSeries[] = ['name' => $type->leave_type, 'data' => $data];
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('leave_type', fn($req) => $req->leaveType->leave_type ?? '-')
                ->addColumn('leave_type_id', fn($req) => $req->leave_type)
                ->addColumn('employee', function ($req) {
                    $user        = $req->boxUser?->user;
                    $department  = $req->boxUser?->departmentRelation;
                    $imagePath   = $req->boxUser?->profile_image_path;
                    $designation = $req->boxUser?->designationRelation;
                    $employeeId  = $req->boxUser?->employee_id;
                    return [
                        'name'         => $user?->name ?? 'N/A',
                        'image'        => $imagePath ? asset('storage/' . $imagePath) : null,
                        'department'   => $department?->name ?? 'N/A',
                        'employee_id'  => $employeeId,
                        'desigination' => $designation?->name ?? 'N/A',
                    ];
                })
                ->addColumn('from', fn($req) => Carbon::parse($req->from_date)->format('d M Y'))
                ->addColumn('to', fn($req) => Carbon::parse($req->to_date)->format('d M Y'))
                ->addColumn('no_of_days', fn($req) => $req->total_days . ' Days')
                ->addColumn('status', fn($req) => match (strtolower($req->status)) {
                    'approved' => 'Approved',
                    'declined', 'rejected' => 'Declined',
                    'pending'  => 'Pending',
                    default    => ucfirst($req->status),
                })
                ->addColumn('approved_by', fn($req) => $req->approvedByUser?->name ?? 'N/A')
                ->addColumn('attachment', fn($req) => $req->attachment ? asset('storage/' . $req->attachment) : null)
                ->addColumn('action', fn($req) => [
                    'edit_id'   => $req->id,
                    'delete_id' => $req->id,
                ])
                ->with([
                    'summary' => $summary,
                    'growth' => $growth,
                    'comparisonLabel' => $comparisonLabel,
                    'chartData' => $formattedSeries
                ])
                ->toJson();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function employeeLeaveStats(Request $request)
    {
        try {
            $user        = Auth::id();
            $year        = $request->year ?? now()->year;
            $today       = Carbon::now()->startOfDay();
            $currentYear = $today->year;

            $boxUser = BoxUser::where('user_id', $user)->select('id', 'box_id', 'joining_date')->first();
            if (! $boxUser) { return response()->json(['error' => 'User not found in box.'], 404); }

            $nextHoliday = Holiday::where('box_id', $boxUser->box_id)->whereDate('date', '>=', $today)->orderBy('date', 'asc')->select('title', 'date', 'id')->first();
            
            $joiningDate  = Carbon::parse($boxUser->joining_date);
            $joiningYear  = $joiningDate->year;
            $joiningMonth = $joiningDate->month;

            $daysActive = ($joiningYear > $year) ? 0 : 
                (Carbon::parse($joiningYear == $year ? $joiningDate : Carbon::create($year, 1, 1))->diffInDays($year == $currentYear ? $today : Carbon::create($year, 12, 31)) + 1);

            $leaveTypes = LeaveType::where('box_id', $boxUser->box_id)->select('id', 'leave_type', 'credit_days', 'number_of_days')->get();
            $adjustedLeaveTypes = $leaveTypes->map(function ($leave) use ($joiningYear, $joiningMonth, $year) {
                $monthsCounted = ($joiningYear != $year) ? 12 : (12 - ($joiningMonth - 1));
                return [
                    'leave_type'     => $leave->leave_type,
                    'credit_days'    => (int) $leave->credit_days,
                    'prorated_days'  => round(((int)$leave->credit_days / 12) * $monthsCounted),
                    'months_counted' => $monthsCounted,
                ];
            });

            $totalProratedLeaveDays = $adjustedLeaveTypes->sum(fn($leave) => (int)$leave['credit_days'] * $leave['months_counted']);
            $totalAbsent = Attendance::whereYear('attendance_date', $year)->where('box_user_id', $boxUser->id)->where('status', 'absent')->count();
            $totalLeavePending = LeaveRequest::where('box_user_id', $boxUser->id)->where('status', 'pending')->whereYear('created_at', $year)->count();
            $totalLeaveApproved = LeaveRequest::where('box_user_id', $boxUser->id)->where('status', 'approved')->whereYear('created_at', $year)->count();

            return response()->json(['data' => [
                'year' => $year, 'leave_types' => $adjustedLeaveTypes, 'total_prorated_leave_days' => $totalProratedLeaveDays, 
                'days_active_in_year' => $daysActive, 'total_absent' => $totalAbsent, 
                'leave_requests' => ['pending' => $totalLeavePending, 'approved' => $totalLeaveApproved],
                'nextHoliday' => $nextHoliday,
            ]]);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong.', 'details' => $th->getMessage()], 500);
        }
    }

    public function getLeaveDashboardMobile(Request $request)
    {
        try {
            $user = Auth::user();
            $boxUser = BoxUser::where('user_id', $user->id)->first();
            if (!$boxUser) {
                return response()->json(['error' => 'No box user found.'], 404);
            }
            $boxId = $boxUser->box_id;

            // Base query for leaves
            $query = LeaveRequest::with([
                'boxUser.user:id,name',
                'boxUser.designationRelation:id,name',
                'leaveType:id,leave_type'
            ])->where('box_id', $boxId)->latest();

            // Filters
            if ($request->filled('date_from')) {
                $query->where('from_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->where('to_date', '<=', $request->date_to);
            }
            if ($request->filled('leave_type')) {
                $query->whereHas('leaveType', function($q) use ($request) {
                    $q->where('leave_type', $request->leave_type);
                });
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            // Get stats for the box
            // total_present = count of Attendance present today (or we can just mock it if Attendance logic is complex)
            // But we can query Attendance model
            $today = \Carbon\Carbon::now()->format('Y-m-d');
            $totalPresent = \App\Models\Attendance::where('box_id', $boxId)
                ->where('attendance_date', $today)
                ->where('status', 'Present')
                ->count();
                
            $totalUsers = BoxUser::where('box_id', $boxId)->count();

            // the query applies filters, but for stats maybe we want overall stats based on the same date filter?
            // If date_from/date_to is provided, we can count within that range, but typically dashboard stats are overall or for the period.
            // Let's use the filtered query for stats to reflect the current view
            $planned = (clone $query)->where('status', 'Approved')->count();
            $unplanned = (clone $query)->where('status', 'Rejected')->count();
            $pending = (clone $query)->where('status', 'Pending')->count();

            $stats = [
                'total_present' => "{$totalPresent}/{$totalUsers}",
                'planned' => $planned,
                'unplanned' => $unplanned,
                'pending_requests' => $pending,
            ];

            $perPage = $request->input('per_page', 15);
            $leaves = $query->paginate($perPage);
            
            // Format leaves
            $formattedLeaves = $leaves->map(function ($leave) {
                return [
                    'id' => $leave->id,
                    'name' => $leave->boxUser->user->name ?? 'Unknown',
                    'designation' => $leave->boxUser->designationRelation->name ?? '',
                    'leave_type' => $leave->leaveType->leave_type ?? '--',
                    'days' => $leave->total_days,
                    'from_date' => $leave->from_date,
                    'to_date' => $leave->to_date,
                    'status' => $leave->status,
                ];
            });

            return response()->json([
                'stats' => $stats,
                'data' => [
                    'leaves' => $formattedLeaves,
                    'current_page' => $leaves->currentPage(),
                    'last_page' => $leaves->lastPage(),
                    'total' => $leaves->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
