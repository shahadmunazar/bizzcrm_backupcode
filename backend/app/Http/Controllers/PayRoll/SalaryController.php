<?php

namespace App\Http\Controllers\PayRoll;
use App\Http\Controllers\Controller;
use App\Models\{BoxUser,Box,Holiday,LeaveRequest,LeaveType,SalaryReport,SalaryTransaction, LeaveTransaction,Attendance,Salary, SalaryDeduction, SalaryEarning, Team,SalaryHistory, Department};
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\PayRoll\SalarySettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\SalaryExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class SalaryController extends Controller
{

    public function generateSlip(Request $request)
    {
        try {
            $request->validate([
                'box_user_ids' => 'required|array|min:1',
                'box_user_ids.*' => 'required|integer|exists:box_users,id',
                'month' => 'required|integer|min:1|max:12',
                'year' => 'required|integer|min:2000|max:2100',
            ]);
            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $adminBoxUser = BoxUser::where('user_id', $user->id)->first();
            $isBoxAdmin = $adminBoxUser && strtolower($adminBoxUser->staff_role ?? '') === 'admin';
            $canCreate = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'generate', 'slip');

            if (!$isGlobalAdmin && !$isBoxAdmin && !$canCreate) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to generate salary slips.',
                ], 403);
            }

            if (!$adminBoxUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Box not found for current user.',
                ], 403);
            }
            $userId = $user->id;
            $boxId = $adminBoxUser->box_id;
            $boxUsers = BoxUser::whereIn('id', $request->box_user_ids)
                ->where('box_id', $boxId)
                ->with(['shift', 'box', 'bankDetails', 'attendances', 'latestSalary.salary_settings'])
                ->get();
            if ($boxUsers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid employees found for selection.',
                ], 404);
            }
            $year = $request->year;
            $month = $request->month;
            $periodStart = Carbon::create($year, $month, 1)->startOfDay();
            $periodEnd = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
            $decimalHoursToHHMM = fn($decimal) => ($decimal === null || $decimal <= 0) ? '--:--' : sprintf('%02d:%02d', floor($decimal), round(($decimal - floor($decimal)) * 60));
            $parseWorkingDays = fn($input) => array_unique(array_map('trim', explode(',', is_array($input) ? implode(',', $input) : ($input ?? ''))));
            $reports = [];
            $generatedCount = 0;
            DB::transaction(function () use ($boxUsers, $boxId, $userId, $periodStart, $periodEnd, $decimalHoursToHHMM, $parseWorkingDays, &$reports, &$generatedCount) {
                foreach ($boxUsers as $boxUser) {
                    $userPeriodEnd = $periodEnd->copy();
                    if ($boxUser->resignation_date) {
                        $resignationDate = Carbon::parse($boxUser->resignation_date)->endOfDay();
                        if ($resignationDate->between($periodStart, $periodEnd)) {
                            $userPeriodEnd = $resignationDate;
                        } elseif ($resignationDate->lt($periodStart)) {
                            continue;
                        }
                    }

                    $leaveRequests = LeaveRequest::where('box_id', $boxId)
                        ->where('box_user_id', $boxUser->id)
                        ->where('status', 'approved')
                        ->where(function ($query) use ($periodStart, $userPeriodEnd) {
                            $query->whereBetween('from_date', [$periodStart, $userPeriodEnd])
                                ->orWhereBetween('to_date', [$periodStart, $userPeriodEnd])
                                ->orWhere(function ($q) use ($periodStart, $userPeriodEnd) {
                                    $q->where('from_date', '<', $periodStart)
                                        ->where('to_date', '>', $userPeriodEnd);
                                });
                        })->get();

                    // Count holidays for this user period
                    $periodHolidays = Holiday::with('holidayType')
                        ->where('box_id', $boxId)
                        ->where('status', 'Active')
                        ->whereBetween('date', [$periodStart, $userPeriodEnd])
                        ->get();

                    $userTotalHolidays = 0;
                    foreach ($periodHolidays as $holiday) {
                        $calcInCalendar = 'No';
                        if ($holiday->holidayType) {
                            $calcInCalendar = $holiday->holidayType->calculate_in_calendar;
                        } elseif ($holiday->holiday_type_id) {
                            $hType = \App\Models\HolidayType::find($holiday->holiday_type_id);
                            if ($hType) {
                                $calcInCalendar = $hType->calculate_in_calendar;
                            }
                        } elseif ($holiday->holiday_type) {
                            $hType = \App\Models\HolidayType::where('box_id', $boxId)
                                ->where('holiday_type', $holiday->holiday_type)
                                ->first();
                            if ($hType) {
                                $calcInCalendar = $hType->calculate_in_calendar;
                            }
                        }

                        if (strcasecmp($calcInCalendar, 'Yes') === 0) {
                            $userTotalHolidays++;
                        }
                    }
                    $userLeaveDays = 0; // Will be calculated directly from Attendance records below
                    $salary = $boxUser->latestSalary;
                    $shift = $boxUser->shift;
                    $hasShift = $boxUser->shift_id && optional($shift)->shift_enabled;
                    $totalWorkingDays = null;
                    $userWorkingDays = $parseWorkingDays($boxUser->working_days ?? $boxUser->box->working_days ?? 'Monday,Tuesday,Wednesday,Thursday,Friday');
                    $dailyHours = $weeklyHours = $monthlyHours = $totalWorkingDaysWeek = $totalWorkingDaysMonth = $totalOvertimeHours = 0;
                    $totalWeeklyOff = 0;
                    $weeklyHours = $monthlyHours = '--:--';
                    if ($hasShift) {
                        $userWorkingDays = $this->getWorkingDaysFromShift($shift);
                        if (!empty($shift->shift_hours)) {
                            [$hours, $minutes] = array_pad(explode(':', $shift->shift_hours), 2, 0);
                            $dailyHours = (int)$hours + ((int)$minutes / 60);
                        }
                        $weekly = $this->calculateWorkingDaysAndHours($periodStart, $userPeriodEnd, $shift, $userWorkingDays);
                        $monthly = $this->calculateWorkingDaysMonthsAndHours($periodStart, $userPeriodEnd, $shift, $userWorkingDays, $dailyHours);
                        $totalWorkingDaysWeek = $weekly['working_days'];
                        $totalWorkingDaysMonth = $monthly['working_days'];
                        $totalWeeklyOff = $monthly['total_weekly_offs'];
                        $weeklyHours = $decimalHoursToHHMM($totalWorkingDaysWeek * $dailyHours);
                        $monthlyHours = $decimalHoursToHHMM($totalWorkingDaysMonth * $dailyHours);
                    }
                    $attendances = Attendance::where('box_id', $boxId)
                        ->where('box_user_id', $boxUser->id)
                        ->whereBetween('attendance_date', [$periodStart->toDateString(), $userPeriodEnd->toDateString()])
                        ->get();

                    // ✅ Re-evaluate attendance statuses based on holiday rules
                    $attendances->transform(function ($attendance) use ($boxId, $leaveRequests) {
                        $status = strtolower(trim($attendance->status ?? ''));
                        $attendanceStatus = strtolower(trim($attendance->attendance_status ?? ''));
                        
                        if (str_contains($status, 'punch out miss') || str_contains($attendanceStatus, 'punch out miss')) {
                            $attendance->status = 'Absent';
                            $status = 'absent';
                        }
                        
                        if (str_contains($status, 'holiday') || str_contains($status, 'gazetted') || str_contains($status, 'comp off')) {
                            $holidayObj = Holiday::with('holidayType')
                                ->where('box_id', $boxId)
                                ->where('status', 'Active')
                                ->whereDate('date', $attendance->attendance_date)
                                ->first();
                                
                            if ($holidayObj) {
                                $calcInCalendar = 'No';
                                if ($holidayObj->holidayType) {
                                    $calcInCalendar = $holidayObj->holidayType->calculate_in_calendar;
                                } elseif ($holidayObj->holiday_type_id) {
                                    $hType = \App\Models\HolidayType::find($holidayObj->holiday_type_id);
                                    if ($hType) {
                                        $calcInCalendar = $hType->calculate_in_calendar;
                                    }
                                } elseif ($holidayObj->holiday_type) {
                                    $hType = \App\Models\HolidayType::where('box_id', $boxId)
                                        ->where('holiday_type', $holidayObj->holiday_type)
                                        ->first();
                                    if ($hType) {
                                        $calcInCalendar = $hType->calculate_in_calendar;
                                    }
                                }
                                
                                if (strcasecmp($calcInCalendar, 'Yes') === 0) {
                                    $hasApprovedLeave = $leaveRequests->contains(function ($leave) use ($attendance) {
                                        $leaveStart = Carbon::parse($leave->from_date)->startOfDay();
                                        $leaveEnd = Carbon::parse($leave->to_date)->endOfDay();
                                        $attDate = Carbon::parse($attendance->attendance_date)->startOfDay();
                                        return $attDate->between($leaveStart, $leaveEnd);
                                    });
                                    
                                    if (!$hasApprovedLeave) {
                                        $attendance->status = 'Absent';
                                    } else {
                                        $attendance->status = 'Holiday Approved';
                                    }
                                } else {
                                    $attendance->status = 'Holiday';
                                }
                            }
                        }
                        return $attendance;
                    });
                    $presentCount = $attendances->filter(function ($attendance) {
                        $status = strtolower(trim($attendance->status ?? ''));
                        $attStatus = strtolower(trim($attendance->attendance_status ?? ''));
                        return $status === 'present' || $attStatus === 'present' || $status === 'p' || $attStatus === 'p';
                    })->count();
                    
                    $absentCount = $attendances->filter(function ($attendance) {
                        $status = strtolower(trim($attendance->status ?? ''));
                        $attStatus = strtolower(trim($attendance->attendance_status ?? ''));
                        return $status === 'absent' || $attStatus === 'absent' || $status === 'a' || $attStatus === 'a';
                    })->count();

                    // Count comp off days from attendance records (case-insensitive check)
                    $compOffCount = $attendances->filter(function ($attendance) {
                        $status = strtolower(trim($attendance->status ?? ''));
                        $attStatus = strtolower(trim($attendance->attendance_status ?? ''));
                        return $status === 'comp off' || $attStatus === 'comp off' || $status === 'co' || $attStatus === 'co';
                    })->count();

                    // Count half days (counts as 0.5 days)
                    $halfDayCount = $attendances->filter(function ($attendance) {
                        $status = strtolower(trim($attendance->status ?? ''));
                        $attStatus = strtolower(trim($attendance->attendance_status ?? ''));
                        return $status === 'half day' || $status === 'half_day' || $attStatus === 'half day' || $attStatus === 'half_day' || $status === 'hd' || $attStatus === 'hd';
                    })->count();

                    // Count short leave days (counts as 0.25 days - half of half day)
                    $shortLeaveCount = $attendances->filter(function ($attendance) {
                        $status = strtolower(trim($attendance->status ?? ''));
                        $attStatus = strtolower(trim($attendance->attendance_status ?? ''));
                        return $status === 'short leave' || $status === 'short_leave' || $attStatus === 'short leave' || $attStatus === 'short_leave' || $status === 'sl' || $attStatus === 'sl';
                    })->count();

                    // Count punch out miss days (counts as full day - present but missed punch out)
                    $punchOutMissCount = $attendances->filter(function ($attendance) {
                        $status = strtolower(trim($attendance->status ?? ''));
                        $attStatus = strtolower(trim($attendance->attendance_status ?? ''));
                        return $status === 'punch out miss' || $status === 'punch_out_miss' || $attStatus === 'punch out miss' || $attStatus === 'punch_out_miss';
                    })->count();

                    // Fetch dynamic leave types for this box to match against attendance statuses (e.g. ML, CL)
                    $leaveTypes = \App\Models\LeaveType::where('box_id', $boxId)
                        ->where('status', 'Active')
                        ->get();
                        
                    $validLeaveStatuses = [];
                    foreach ($leaveTypes as $leaveType) {
                        $name = strtolower(trim($leaveType->leave_type));
                        if (!empty($name)) {
                            $validLeaveStatuses[] = $name;
                            // Generate acronym from first letters (e.g., "Medical Leave" -> "ml")
                            if (preg_match_all('/\b\w/', $name, $matches)) {
                                $validLeaveStatuses[] = implode('', $matches[0]);
                            }
                        }
                        if (!empty($leaveType->leave_code)) {
                            $validLeaveStatuses[] = strtolower(trim($leaveType->leave_code));
                        }
                    }
                    $validLeaveStatuses = array_filter(array_unique($validLeaveStatuses), function($val) {
                        return !in_array($val, ['comp off', 'co', 'half day', 'hd', 'present', 'p', 'absent', 'a', 'holiday', 'punch out miss']);
                    });

                    // Count paid leaves from attendance records (full day leaves)
                    $userLeaveDays = $attendances->filter(function ($attendance) use ($validLeaveStatuses) {
                        $status = strtolower(trim($attendance->status ?? ''));
                        $attStatus = strtolower(trim($attendance->attendance_status ?? ''));
                        
                        // Check if status or attStatus matches exactly or has a suffix like 'ml-approved'
                        foreach ($validLeaveStatuses as $leaveMatch) {
                            if ($status === $leaveMatch || str_starts_with($status, $leaveMatch . '-') || str_starts_with($status, $leaveMatch . ' ')) {
                                return true;
                            }
                            if ($attStatus === $leaveMatch || str_starts_with($attStatus, $leaveMatch . '-') || str_starts_with($attStatus, $leaveMatch . ' ')) {
                                return true;
                            }
                        }
                        
                        $isStatusLeave = str_contains($status, 'leave') && !str_contains($status, 'short');
                        $isAttStatusLeave = str_contains($attStatus, 'leave') && !str_contains($attStatus, 'short');
                        
                        return $isStatusLeave || $isAttStatusLeave;
                    })->count();
                    $netSalary = optional($salary)->net_salary;
                    $daysInMonth = $periodStart->daysInMonth; // Total days in   the month (e.g., 31 for December)

                    // Calculate actual weekly offs in the period
                    // Count weekly off days from shift configuration
                    $weeklyOffDays = 0;
                    if ($hasShift && $shift) {
                        // Get weekly off days from shift (weekly_off_1, weekly_off_2, weekly_off_3, weekly_off_4)
                        $weeklyOffDaysList = array_filter([
                            $shift->weekly_off_1,
                            $shift->weekly_off_2,
                            $shift->weekly_off_3,
                            $shift->weekly_off_4,
                        ]);

                        // Count how many weekly off days occur in the period
                        $period = CarbonPeriod::create($periodStart, $userPeriodEnd);
                        foreach ($period as $date) {
                            $dayName = $date->format('l'); // Full day name (Monday, Tuesday, etc.)
                            if (in_array($dayName, $weeklyOffDaysList)) {
                                // Check if this date is not a holiday
                                $holidayObj = Holiday::with('holidayType')
                                    ->where('box_id', $boxId)
                                    ->where('status', 'Active')
                                    ->whereDate('date', $date->toDateString())
                                    ->first();
                                $isHoliday = false;
                                if ($holidayObj) {
                                    $calcInCalendar = 'No';
                                    if ($holidayObj->holidayType) {
                                        $calcInCalendar = $holidayObj->holidayType->calculate_in_calendar;
                                    } elseif ($holidayObj->holiday_type_id) {
                                        $hType = \App\Models\HolidayType::find($holidayObj->holiday_type_id);
                                        if ($hType) {
                                            $calcInCalendar = $hType->calculate_in_calendar;
                                        }
                                    } elseif ($holidayObj->holiday_type) {
                                        $hType = \App\Models\HolidayType::where('box_id', $boxId)
                                            ->where('holiday_type', $holidayObj->holiday_type)
                                            ->first();
                                        if ($hType) {
                                            $calcInCalendar = $hType->calculate_in_calendar;
                                        }
                                    }

                                    if (strcasecmp($calcInCalendar, 'Yes') === 0) {
                                        $isHoliday = true;
                                    } else {
                                        $isHoliday = false;
                                    }
                                }

                                // Only count as weekly off if it's not a holiday
                                if (!$isHoliday) {
                                    $weeklyOffDays++;
                                }
                            }
                        }
                    } else {
                        // If no shift, use the calculated weekly offs from the method
                        $weeklyOffDays = $totalWeeklyOff;
                    }

                    // Count holidays dynamically from evaluated attendance records
                    $holidayDays = $attendances->filter(function ($attendance) {
                        $status = strtolower(trim($attendance->status ?? ''));
                        $attStatus = strtolower(trim($attendance->attendance_status ?? ''));
                        return $status === 'holiday' || $status === 'holiday approved' || $attStatus === 'holiday' || $attStatus === 'holiday approved';
                    })->count();

                    // Calculate working days = Total days in month - Weekly offs - Holidays
                    $totalWorkingDaysInMonth = $daysInMonth - $weeklyOffDays - $holidayDays;

                    // Always use 30 days as standard for salary calculation
                    $standardDays = 30;

                    $perDaySalary = 0;
                    $deductionDays = 0;
                    $deductionAmount = 0;
                    $finalSalary = 0;

                    $salarySetting = optional($salary)->salarySetting;
                    $compOffTypeSetting = $salarySetting ? $salarySetting->comp_off_type : 'paid';

                    $encashableDays = 0;
                    $compOffEncashAmount = 0;

                    if ($compOffTypeSetting === 'paid') {
                        $compOffLeaveType = LeaveType::where('box_id', $boxId)
                            ->where(function ($query) {
                                $query->where('leave_type', 'like', '%Compensatory Off%')
                                      ->orWhere('leave_type', 'like', '%Comp Off%')
                                      ->orWhere('leave_type', 'like', '%comp off%');
                            })
                            ->first();
                        if ($compOffLeaveType) {
                            $encashableDays = $compOffCount;
                            if ($encashableDays > 0) {
                                $latestTxn = LeaveTransaction::where('box_user_id', $boxUser->id)
                                    ->where('leave_type_id', $compOffLeaveType->id)
                                    ->latest('id')
                                    ->first();
                                $availableBalance = $latestTxn ? $latestTxn->balance_leave : 0;

                                $encashRemark = 'Comp Off encashed for ' . $periodStart->format('F Y');
                                $existingEncashment = LeaveTransaction::where('box_user_id', $boxUser->id)
                                    ->where('leave_type_id', $compOffLeaveType->id)
                                    ->where('debit_leave', '>', 0)
                                    ->where('transaction_remarks', $encashRemark)
                                    ->first();

                                if (!$existingEncashment) {
                                    LeaveTransaction::create([
                                        'box_id' => $boxId,
                                        'box_user_id' => $boxUser->id,
                                        'leave_type_id' => $compOffLeaveType->id,
                                        'date' => $userPeriodEnd->format('Y-m-d'),
                                        'debit_leave' => $encashableDays,
                                        'balance_leave' => max(0, $availableBalance - $encashableDays),
                                        'total_leave' => $encashableDays,
                                        'transaction_remarks' => $encashRemark,
                                        'status' => 'Approved',
                                        'created_by' => $userId,
                                        'applied_date' => $userPeriodEnd->format('Y-m-d'),
                                        'approved_date' => now(),
                                    ]);
                                }
                            }
                        }
                    }

                                        $presentDays = $presentCount;
                    $absentDays = $absentCount; // Absent days from attendance records
                    
                    // IF unpaid, they do not get paid for the extra holiday they worked, so compOffDays should be excluded or kept?
                    // The standard calculates paidDays, but final salary depends ONLY on absent deductions.
                    $compOffDays = $compOffCount;
                    $halfDays = $halfDayCount;
                    $shortLeaveDays = $shortLeaveCount;
                    $punchOutMissDays = $punchOutMissCount;

                    // Calculate Paid Days based on standard attendance formula
                    // Paid Days = (Present + Holidays + Leaves + Weekly Offs) - (Half Days * 0.5) - (Short Leaves * 0.25) - (Punch Out Miss * 1.0)
                    $paidDays = ($presentDays * 1.0) +
                        ($holidayDays * 1.0) +
                        ($userLeaveDays * 1.0) +
                        ($weeklyOffDays * 1.0) -
                        ($halfDays * 0.5) -
                        ($shortLeaveDays * 0.25) -
                        ($punchOutMissDays * 1.0);
                    $paidDays = max(0, round($paidDays, 2));

                    // Dynamic paid days for display only
                    $dynamicPaidDays = $paidDays;

                    // Account for inactive days (joined mid-month or resigned mid-month)
                    $userPeriodStartForCalc = $periodStart->copy();
                    if ($boxUser->joining_date) {
                        $joiningDate = Carbon::parse($boxUser->joining_date)->startOfDay();
                        if ($joiningDate->between($periodStart, $periodEnd)) {
                            $userPeriodStartForCalc = $joiningDate;
                        }
                    }
                    $userPeriodEndForCalc = $periodEnd->copy();
                    if ($boxUser->resignation_date) {
                        $resignationDate = Carbon::parse($boxUser->resignation_date)->endOfDay();
                        if ($resignationDate->between($periodStart, $periodEnd)) {
                            $userPeriodEndForCalc = $resignationDate;
                        }
                    }
                    
                    $activeDays = $userPeriodStartForCalc->diffInDays($userPeriodEndForCalc) + 1;
                    $inactiveDays = max(0, $daysInMonth - max(0, $activeDays));

                    $structureEarnings = [];
                    $structureDeductions = [];
                    if ($salary) {
                        if ($salary->earnings) {
                            $earningFields = ['basic', 'da', 'hra', 'conveyance', 'allowance', 'medical_allowance', 'special_allowance', 'others'];
                            foreach ($earningFields as $comp) {
                                if (!empty($salary->earnings->$comp)) {
                                    $structureEarnings[$comp] = [
                                        'actual' => $salary->earnings->$comp,
                                    ];
                                }
                            }
                        }
                        if ($salary->deductions) {
                            $deductionFields = ['tds', 'esi', 'pf', 'prof_tax', 'labour_welfare', 'othersdeductions', 'salary_advance_recovery', 'canteen_deduction', 'attendance_loss'];
                            foreach ($deductionFields as $comp) {
                                if (!empty($salary->deductions->$comp)) {
                                    $structureDeductions[$comp] = [
                                        'actual' => $salary->deductions->$comp,
                                    ];
                                }
                            }
                        }
                    }

                    // Deduct inactive days to prevent paying for Weekly Offs/Holidays before joining
                    if ($inactiveDays > 0) {
                        $paidDays -= $inactiveDays;
                        $paidDays = max(0, round($paidDays, 2));
                    }

                    // Cap standard paid days at standard base days (30)
                    if ($paidDays > $standardDays) {
                        $paidDays = $standardDays;
                    }
                    
                    $netPaidDays = $paidDays + $encashableDays; // Add encashable Comp Offs to get Net Paid Days

                    if ($netSalary && $standardDays > 0) {
                        $perDaySalary = round($netSalary / $standardDays, 2);

                        // Calculate deductions based purely on standard days minus paid days
                        $deductionDays = $standardDays - $paidDays;
                        if ($deductionDays < 0) {
                            $deductionDays = 0;
                        }

                        $deductionAmount = round($perDaySalary * $deductionDays, 2);
                        $compOffEncashAmount = round($perDaySalary * $encashableDays, 2);
                        
                        $finalSalary = round($netSalary - $deductionAmount + $compOffEncashAmount, 2);
                        
                        // Prorate components
                        foreach ($structureEarnings as $comp => $val) {
                            $structureEarnings[$comp]['prorated'] = round(($val['actual'] / $standardDays) * $paidDays, 2);
                        }
                        foreach ($structureDeductions as $comp => $val) {
                            // Deductions like PF/ESI might be fixed or prorated, standard is prorating if absent
                            $structureDeductions[$comp]['prorated'] = round(($val['actual'] / $standardDays) * $paidDays, 2);
                        }

                        if ($finalSalary < 0) {
                            $finalSalary = 0;
                        }
                    } elseif ($netSalary) {
                        $perDaySalary = 0;
                        $deductionDays = 0;
                        $deductionAmount = 0;
                        $finalSalary = 0;
                    }
                    
                    $salaryReport = SalaryReport::updateOrCreate(
                        [
                            'box_user_id' => $boxUser->id,
                            'box_id' => $boxId,
                            'year' => $periodStart->year,
                            'month' => $periodStart->format('m'),
                        ],
                        [
                            'salary_id' => optional($salary)->id,
                            'gross_salary' => $netSalary,
                            'total_earnings' => $netSalary + $compOffEncashAmount,
                            'total_deductions' => $deductionAmount,
                            'net_salary' => $finalSalary, // Final salary after all calculations
                            'earnings_breakdown' => [
                                'present_days' => $presentCount,
                                'comp_off_days' => $compOffDays,
                                'comp_off_encashed_days' => $encashableDays,
                                'comp_off_encash_amount' => $compOffEncashAmount,
                                'half_days' => $halfDays,
                                'half_days_value' => round($halfDays * 0.5, 2), // Half days count as 0.5
                                'short_leave_days' => $shortLeaveDays,
                                'short_leave_days_value' => round($shortLeaveDays * 0.25, 2), // Short leave counts as 0.25
                                'punch_out_miss_days' => $punchOutMissDays,
                                'weekly_off_days' => $weeklyOffDays,
                                'holiday_days' => $holidayDays,
                                'leave_days' => $userLeaveDays,
                                'paid_days' => $paidDays, // Standard Calculation paid days
                                'dynamic_paid_days' => $dynamicPaidDays, // Dynamic display paid days
                                'per_day_salary' => $perDaySalary,
                                'structure_earnings' => $structureEarnings,
                                'structure_deductions' => $structureDeductions,
                                'total_days_in_month' => $daysInMonth,
                                'total_working_days_in_month' => $totalWorkingDaysInMonth, // Days - Weekly Offs - Holidays
                                'calculation_base_days' => $standardDays, // Always 30 for salary calculation
                            ],
                            'deductions_breakdown' => [
                                'absent_days' => $absentDays,
                                'absent_days_from_attendance' => $absentDays, // Actual absent days from attendance records
                                'punch_out_miss_days' => $punchOutMissDays,
                                'deduction_days' => $deductionDays, // Total deduction days (30 - paid days)
                                'deduction_amount' => $deductionAmount,
                                'deduction_per_day' => $perDaySalary,
                            ],
                            'total_days_in_month' => $daysInMonth,
                            'total_working_days' => $totalWorkingDaysInMonth, // Days - Weekly Offs - Holidays
                            'total_working_days_month' => $totalWorkingDaysMonth,
                            'total_weekly_offs' => $weeklyOffDays,
                            'total_holidays' => $holidayDays,
                            'comp_off_days' => $compOffDays,
                            'leave_days' => $userLeaveDays,
                            'paid_days' => $paidDays, // Paid days = Present + Comp Off (for salary calculation)
                            'calculation_base_days' => $standardDays, // Always 30 for salary calculation
                            'is_locked' => false,
                            'created_by' => $userId,
                            'updated_by' => $userId,
                        ]
                    );
                    SalaryTransaction::create([
                        'salary_report_id' => $salaryReport->id,
                        'user_bank_account_id' => optional($boxUser->bankDetails)->first()?->id ?? null,
                        'payment_date' => now(),
                        'amount' => $finalSalary,
                        'payment_method' => 'system_generated',
                        'transaction_ref' => 'SR-' . strtoupper(uniqid()),
                        'status' => 'pending',
                        'remarks' => 'Auto generated salary report for ' . $periodStart->format('F Y'),
                        'created_by' => $userId,
                    ]);

                    $reports[] = [
                        'user_id' => $boxUser->id,
                        'name' => $boxUser->first_name ?? 'Unknown',
                        'daily_hours' => $dailyHours,
                        'weekly_hours' => $weeklyHours,
                        'monthly_hours' => $monthlyHours,
                        'total_working_days_week' => $totalWorkingDaysWeek,
                        'total_working_days_month' => $totalWorkingDaysMonth,
                        'total_working_days' => $totalWorkingDays,
                        'total_overtime_hours' => $totalOvertimeHours,
                        'absent_days' => $absentDays,
                        'worked_days' => $presentCount,
                        'present_days' => $presentCount,
                        'total_days_in_month' => $daysInMonth,
                        'weekly_off_days' => $weeklyOffDays,
                        'holiday_days' => $holidayDays,
                        'comp_off_days' => $compOffDays,
                        'half_days' => $halfDays,
                        'half_days_value' => round($halfDays * 0.5, 2),
                        'short_leave_days' => $shortLeaveDays,
                        'short_leave_days_value' => round($shortLeaveDays * 0.25, 2),
                        'punch_out_miss_days' => $punchOutMissDays,
                        'leave_days' => $userLeaveDays,
                        'paid_days' => $paidDays, // Present + Comp Off + Half Days (0.5) + Short Leave (0.25) + Punch Out Miss
                        'total_working_days_in_month' => $totalWorkingDaysInMonth, // Days - Weekly Offs - Holidays
                        'total_working_days_month' => $totalWorkingDaysMonth,
                        'calculation_base_days' => $standardDays, // Always 30
                        'period_start' => $periodStart->toDateString(),
                        'period_end' => $userPeriodEnd->toDateString(),
                        'per_day_salary' => $perDaySalary,
                        'deduction_days' => $deductionDays,
                        'deduction_amount' => $deductionAmount,
                        'encashable_days' => $encashableDays,
                        'comp_off_encash_amount' => $compOffEncashAmount,
                        'net_salary' => $netSalary,
                        'final_salary' => $finalSalary,
                    ];

                    $generatedCount++;
                }
            });

            // If only one report generated, redirect to salary slip page with that report
            if ($generatedCount === 1 && !empty($reports)) {
                $report = $reports[0];
                // Get the full salary report from database
                $salaryReport = SalaryReport::with([
                    'boxUser.user',
                    'boxUser.departmentRelation',
                    'boxUser.designationRelation',
                    'boxUser.box',
                    'transactions'
                ])
                    ->where('box_user_id', $report['user_id'])
                    ->where('box_id', $boxId)
                    ->where('year', $year)
                    ->where('month', $month)
                    ->first();

                if ($salaryReport) {
                    // Store report ID in session for redirect
                    session(['salary_report_id' => $salaryReport->id]);

                    $boxSlug = request()->route('box_slug');
                    $redirectUrl = url("/{$boxSlug}/salary-slip?report_id={$salaryReport->id}");

                    return response()->json([
                        'success' => true,
                        'message' => "Salary slip generated successfully.",
                        'redirect' => $redirectUrl,
                        'report_id' => $salaryReport->id,
                        'data' => $reports,
                        'salaryReport'=>$salaryReport
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Salary slips generated successfully for {$generatedCount} employees.",
                'data' => $reports,
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating salary slips: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    protected function getWorkingDaysFromShift($shift)
    {
        return $shift->working_days ? explode(',', $shift->working_days) : ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    }

    protected function calculateWorkingDaysAndHours($startDate, $endDate, $shift, $userWorkingDays)
    {
        $period               = CarbonPeriod::create($startDate, $endDate);
        $userTotalWorkingDays = count($userWorkingDays);
        $fullDayWeeks         = $shift->saturday_offs ?? [];
        $halfDayWeeks         = $shift->saturday_half_days ?? [];
        $totalWorkingDays     = 0.0;
        $workingSaturdays     = [];
        foreach ($period as $date) {
            if ($date->format('l') !== 'Saturday') {
                continue;
            }
            $weekNumber = ceil($date->day / 7);
            if (in_array($weekNumber, $fullDayWeeks)) {
                continue;
            }
            if (in_array($weekNumber, $halfDayWeeks)) {
                $totalWorkingDays += 0.5;
                $workingSaturdays[] = $date->toDateString();
            } else {
                $totalWorkingDays += 1.0;
                $workingSaturdays[] = $date->toDateString();
            }
        }

        foreach ($period as $date) {
            $dayName = $date->format('l');
            if ($dayName === 'Saturday') {
                continue;
            }
            if (in_array($dayName, $userWorkingDays)) {
                $totalWorkingDays += 1.0;
            }
        }

        return [
            'working_days' => $totalWorkingDays,
        ];
    }

    public function calculateWorkingDaysMonthsAndHours($startOfMonth, $endOfMonth, $shift, $userWorkingDays, $dailyHours)
    {
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

        if (is_string($dailyHours) && str_contains($dailyHours, ':')) {
            [$hours, $minutes] = explode(':', $dailyHours);
            $dailyHours        = (float) $hours + ((float) $minutes / 60);
        }

        $totaldaysWorkign = 0;
        $totalOffFays = 0;

        $fullDayWeeks         = $shift->saturday_offs ?? [];
        $halfDayWeeks         = $shift->saturday_half_days ?? [];

        // Count working days similar to logic above but simpler for month summary if needed
        foreach ($period as $date) {
            $dayName = $date->format('l');
            $weekNumber = ceil($date->day / 7);

            if ($dayName === 'Saturday') {
                if (in_array($weekNumber, $fullDayWeeks)) {
                    $totalOffFays++;
                } elseif (in_array($weekNumber, $halfDayWeeks)) {
                    $totaldaysWorkign += 0.5;
                } else {
                    // Check if Saturday is in working days (usually yes if not off)
                    $totaldaysWorkign += 1;
                }
            } else {
                if (in_array($dayName, $userWorkingDays)) {
                    $totaldaysWorkign++;
                } else {
                    $totalOffFays++;
                }
            }
        }

        return [
            'working_days' => $totaldaysWorkign,
            'total_weekly_offs' => $totalOffFays
        ];
    }

    public function searchEmployees(Request $request)
    {
        $term = $request->input('q');
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'salary');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json(['results' => []]);
        }
        
        if (!$boxUser) {
            return response()->json(['results' => []]);
        }

        $employees = BoxUser::where('box_id', $boxUser->box_id)
            ->whereHas('user', function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            })
            ->orWhere('employee_id', 'like', "%{$term}%")
            ->orWhere('phone', 'like', "%{$term}%")
            ->with(['user:id,name,email', 'departmentRelation:id,name'])
            ->limit(20)
            ->get();

        $results = $employees->map(function ($emp) {
            return [
                'id' => $emp->id,
                'text' => ($emp->user->name ?? 'Unknown') . ' (' . ($emp->departmentRelation->name ?? 'No Dept') . ')',
                'employee_id' => $emp->employee_id,
                'email' => $emp->user->email ?? '',
                'phone' => $emp->phone ?? ''
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function getAllSalaryEmployee()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'salary');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view employee salaries.'
            ], 403);
        }

        if (!$boxUser) {
            return response()->json([
                'success' => false,
                'message' => 'Box not found for this user.',
            ], 404);
        }
        $employees = BoxUser::where('box_id', $boxUser->box_id)
            ->with('user')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $employees,
        ]);
    }

    public function getFilterOptions(Request $request)
    {
        try {
            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $findBoxId = BoxUser::where('user_id', $user->id)->select('id', 'box_id', 'staff_role')->first();
            $isBoxAdmin = $findBoxId && strtolower($findBoxId->staff_role ?? '') === 'admin';
            $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'salary');

            if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view this data',
                    'data' => []
                ], 403);
            }

            if (!$findBoxId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Box not found',
                    'data' => [
                        'departments' => [],
                        'designations' => []
                    ]
                ], 404);
            }

            // Fetch departments based on box_id
            $departments = Department::where('box_id', $findBoxId->box_id)
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();

            // Fetch designations (teams) based on box_id
            $designations = Team::where('box_id', $findBoxId->box_id)
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'departments' => $departments,
                    'designations' => $designations
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching filter options',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function list(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $findBoxId = BoxUser::where('user_id', $user->id)->select('id', 'box_id', 'staff_role')->first();
        $isBoxAdmin = $findBoxId && strtolower($findBoxId->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'salary');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view employee salaries.'
            ], 403);
        }

        if (!$findBoxId) {
            return response()->json(['data' => []], 200);
        }

        // Query from salaries table with box_user relationship
        $query = Salary::where('box_id', $findBoxId->box_id)
            ->with([
                'boxuser.user',
                'boxuser.departmentRelation',
                'boxuser.designationRelation',
                'earnings',
                'deductions',
                'creator',
                'updater'
            ]);

        // Apply sorting based on request
        $sortOrder = $request->input('sort_order', 'recent');
        if ($sortOrder === 'recent') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sortOrder === 'descending') {
            $query->orderBy('id', 'desc');
        } elseif ($sortOrder === 'ascending') {
            $query->orderBy('id', 'asc');
        } else {
            $query->orderBy('id', 'desc'); // Default
        }

        return DataTables::eloquent($query)
            ->filter(function ($q) use ($request) {
                $search = $request->input('search.value');
                if ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('gross_salary', 'like', "%{$search}%")
                            ->orWhere('net_salary', 'like', "%{$search}%")
                            ->orWhereHas('boxuser', function ($q2) use ($search) {
                                $q2->where('employee_id', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%")
                                    ->orWhereHas('user', function ($q3) use ($search) {
                                        $q3->where('name', 'like', "%{$search}%")
                                            ->orWhere('email', 'like', "%{$search}%");
                                    });
                            });
                    });
                }

                // Department filter
                $departmentId = $request->input('department_id');
                if ($departmentId && $departmentId !== '') {
                    $q->whereHas('boxuser', function ($query) use ($departmentId) {
                        $query->where('department', $departmentId);
                    });
                }

                // Designation filter
                $designationId = $request->input('designation_id');
                if ($designationId && $designationId !== '') {
                    $q->whereHas('boxuser', function ($query) use ($designationId) {
                        $query->where('designation', $designationId);
                    });
                }

                // Date range filter
                $fromDate = $request->input('from_date');
                $toDate = $request->input('to_date');

                if ($fromDate) {
                    $q->whereDate('created_at', '>=', $fromDate);
                }

                if ($toDate) {
                    $q->whereDate('created_at', '<=', $toDate);
                }
            })
            ->addColumn('emp_id', function ($salary) {
                return $salary->boxuser->id ?? '-';
            })
            ->addColumn('name', function ($salary) {
                return $salary->boxuser->user->name ?? '-';
            })
            ->addColumn('email', function ($salary) {
                return $salary->boxuser->user->email ?? '-';
            })
            ->addColumn('phone', function ($salary) {
                return $salary->boxuser->phone ?? '-';
            })
            ->addColumn('designation', function ($salary) {
                return $salary->boxuser->designationRelation->name ?? '-';
            })
            ->addColumn('joining_date', function ($salary) {
                return $salary->boxuser?->joining_date ? \Carbon\Carbon::parse($salary->boxuser->joining_date)->format('d M Y') : '-';
            })
            ->addColumn('full_profile_image', function ($salary) {
                $boxUser = $salary->boxuser;
                return ($boxUser && $boxUser->user && $boxUser->profile_image_path)
                    ? asset('storage/' . $boxUser->profile_image_path)
                    : null;
            })
            ->addColumn('net_salary', function ($salary) {
                return $salary->net_salary ?? 0;
            })
            ->addColumn('gross_salary', function ($salary) {
                return $salary->gross_salary ?? 0;
            })
            ->addColumn('ctc_amount', function ($salary) {
                return $salary->ctc_amount ?? 0;
            })
            ->addColumn('effective_from', function ($salary) {
                return optional($salary->effective_from)->format('d M Y') ?? '-';
            })
            ->addColumn('effective_to', function ($salary) {
                return optional($salary->effective_to)->format('d M Y') ?? '-';
            })
            ->addColumn('created_at', function ($salary) {
                return optional($salary->created_at)->format('d M Y H:i') ?? '-';
            })
            ->addColumn('updated_at', function ($salary) {
                return optional($salary->updated_at)->format('d M Y H:i') ?? '-';
            })
            ->addColumn('created_by', function ($salary) {
                return $salary->creator->name ?? '-';
            })
            ->addColumn('updated_by', function ($salary) {
                return $salary->updater->name ?? '-';
            })
            ->addColumn('salary_exists', function ($salary) {
                $latestReport = SalaryReport::where('box_user_id', $salary->box_user_id)
                    ->where('box_id', $salary->box_id)
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();
                if ($latestReport) {
                    return '<a href="javascript:void(0);" class="view-payslip-btn badge badge-success badge-md" data-report-id="' . $latestReport->id . '">View Payslip</a>';
                }
                return '<span class="badge badge-dark badge-md">Generate Slip</span>';
            })
            ->addColumn('final_salary', function ($salary) {
                $latestReport = SalaryReport::where('box_user_id', $salary->box_user_id)
                    ->where('box_id', $salary->box_id)
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();
                if ($latestReport) {
                    return number_format($latestReport->net_salary ?? 0, 2);
                }
                return '-';
            })
            ->addColumn('latest_salary', function ($salary) {
                return [
                    'id' => $salary->id,
                    'salary_settings_id' => $salary->salary_settings_id
                ];
            })
            ->addColumn('latest_salary_report', function ($salary) {
                $latestReport = SalaryReport::where('box_user_id', $salary->box_user_id)
                    ->where('box_id', $salary->box_id)
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();
                if ($latestReport) {
                    return [
                        'id' => $latestReport->id,
                        'final_salary' => $latestReport->net_salary ?? 0,
                        'month' => $latestReport->month,
                        'year' => $latestReport->year,
                    ];
                }
                return null;
            })
            ->rawColumns(['name', 'salary_exists'])
            ->make(true);
    }

    public function export(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response('Unauthorized', 401);
            }

            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $adminBoxUser = BoxUser::where('user_id', $user->id)->first();
            $isBoxAdmin = $adminBoxUser && strtolower($adminBoxUser->staff_role ?? '') === 'admin';
            $canExport = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'export', 'salary');
            
            if (!$isGlobalAdmin && !$isBoxAdmin && !$canExport) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'You do not have permission to export salary data.'], 403);
                }
                return back()->with('error', 'You do not have permission to export salary data.');
            }

            if (!$adminBoxUser) {
                return back()->with('error', 'Unauthorized: Box not found for current user.');
            }

            $boxId = $adminBoxUser->box_id;
            Log::info('Salary export requested', [
                'box_id' => $boxId,
                'request' => $request->all()
            ]);
            $filters = $request->only(['department_id', 'designation_id', 'sort_order', 'date_range', 'search', 'export_month', 'export_year']);
            $filters['box_id'] = $boxId;

            $fields = $request->input('fields', []);
            $exportType = $request->input('export_type', 'excel');

            if (empty($fields)) {
                return back()->with('error', 'Please select at least one field to export.');
            }

            $allExportFields = [
                'emp_id' => 'Emp ID',
                'name' => 'Name',
                'email' => 'Email',
                'phone' => 'Phone',
                'designation' => 'Designation',
                'joining_date' => 'Joining Date',
                'salary' => 'Annual CTC',
                'final_salary' => 'Final Salary',
                'created_at' => 'Created Date',
                'updated_at' => 'Updated Date',
                'created_by' => 'Created By',
                'updated_by' => 'Updated By'
            ];

            $selectedFieldNames = [];
            foreach ($fields as $field) {
                if (isset($allExportFields[$field])) {
                    $selectedFieldNames[$field] = $allExportFields[$field];
                }
            }

            $export = new SalaryExport($filters, $fields, $selectedFieldNames);
            $fileName = 'Salary_Report_' . now()->format('Y-m-d_H-i-s');

            Log::info('Generating export file', [
                'type' => $exportType,
                'fileName' => $fileName,
                'fieldsCount' => count($fields)
            ]);

            if ($exportType === 'pdf') {
                $data = $export->collection();
                $headings = array_values($selectedFieldNames);
                $rows = [];
                foreach ($data as $item) {
                    $rows[] = $export->map($item);
                }

                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.salary_pdf', [
                    'headings' => $headings,
                    'rows' => $rows,
                    'title' => 'Employee Salary Report',
                    'date' => now()->format('d M Y H:i')
                ])->setPaper('a4', 'landscape');

                return $pdf->download($fileName . '.pdf');
            }

            return \Maatwebsite\Excel\Facades\Excel::download($export, $fileName . '.xlsx');

        } catch (\Exception $e) {
            Log::error('Salary Export Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => substr($e->getTraceAsString(), 0, 1000)
            ]);
            return back()->with('error', 'An error occurred during export: ' . $e->getMessage());
        }
    }

    public function listSalaryReports(Request $request)
    {
        try {
            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $adminBoxUser = BoxUser::where('user_id', $user->id)->first();
            $isBoxAdmin = $adminBoxUser && strtolower($adminBoxUser->staff_role ?? '') === 'admin';
            $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'report');

            if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view salary reports.',
                ], 403);
            }


            if (!$adminBoxUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Box not found for current user.',
                ], 403);
            }

            $boxId = $adminBoxUser->box_id;

            $query = SalaryReport::with([
                'boxUser.user',
                'boxUser.departmentRelation',
                'boxUser.designationRelation',
                'transactions'
            ])
                ->where('box_id', $boxId)
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->orderByDesc('id');

            // Date range filter
            if ($request->has('from_date') && $request->has('to_date')) {
                $fromDate = Carbon::parse($request->from_date)->startOfDay();
                $toDate = Carbon::parse($request->to_date)->endOfDay();

                $query->where(function ($q) use ($fromDate, $toDate) {
                    $q->whereYear('created_at', '>=', $fromDate->year)
                        ->whereMonth('created_at', '>=', $fromDate->month)
                        ->whereYear('created_at', '<=', $toDate->year)
                        ->whereMonth('created_at', '<=', $toDate->month);
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('employee_id', function ($report) {
                    return $report->boxUser->employee_id ?? 'N/A';
                })
                ->addColumn('employee_name', function ($report) {
                    return $report->boxUser->user->name ?? $report->boxUser->first_name ?? 'N/A';
                })
                ->addColumn('month_year', function ($report) {
                    return Carbon::create($report->year, $report->month, 1)->format('F Y');
                })
                ->addColumn('present_days', function ($report) {
                    $breakdown = $report->earnings_breakdown ?? [];
                    return $breakdown['present_days'] ?? 0;
                })
                ->addColumn('paid_days', function ($report) {
                    return number_format($report->paid_days ?? 0, 2);
                })
                ->addColumn('gross_salary', function ($report) {
                    return '₹' . number_format($report->gross_salary ?? 0, 2);
                })
                ->addColumn('total_deductions', function ($report) {
                    return '₹' . number_format($report->total_deductions ?? 0, 2);
                })
                ->addColumn('final_salary', function ($report) {
                    // Use net_salary as final salary (already calculated)
                    return '₹' . number_format($report->net_salary ?? 0, 2);
                })
                ->addColumn('status', function ($report) {
                    $latestTransaction = $report->transactions->last();
                    if ($latestTransaction) {
                        $status = $latestTransaction->status ?? 'pending';
                        $badgeClass = $status === 'paid' ? 'bg-success' : ($status === 'pending' ? 'bg-warning' : 'bg-secondary');
                        return '<span class="badge ' . $badgeClass . '">' . ucfirst($status) . '</span>';
                    }
                    return '<span class="badge bg-secondary">N/A</span>';
                })
                ->addColumn('payslip', function ($report) {
                    return '<a href="javascript:void(0);" class="btn btn-sm btn-primary view-payslip-btn"
                             data-report-id="' . $report->id . '">
                             <i class="ti ti-file-text me-1"></i>View Payslip
                             </a>';
                })
                ->addColumn('actions', function ($report) {
                    return '<div class="d-flex align-items-center gap-2">
                                <a href="javascript:void(0);" class="btn btn-sm btn-info view-report-details"
                                   data-report-id="' . $report->id . '"
                                   data-bs-toggle="tooltip"
                                   title="View Details">
                                   <i class="ti ti-eye"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['status', 'payslip', 'actions'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error listing salary reports: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getSalaryReportById(Request $request)
    {
        try {
            $reportId = $request->input('report_id') ?? session('salary_report_id');

            if (!$reportId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salary report ID not provided.',
                ], 400);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $adminBoxUser = BoxUser::where('user_id', $user->id)->first();
            $isBoxAdmin = $adminBoxUser && strtolower($adminBoxUser->staff_role ?? '') === 'admin';
            $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'report');

            if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view salary reports.',
                ], 403);
            }

            if (!$adminBoxUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Box not found for current user.',
                ], 403);
            }

            $salaryReport = SalaryReport::with([
                'boxUser.user',
                'boxUser.departmentRelation',
                'boxUser.designationRelation',
                'boxUser.box',
                'transactions',
                'salary'
            ])
                ->where('id', $reportId)
                ->where('box_id', $adminBoxUser->box_id)
                ->first();

            if (!$salaryReport) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salary report not found.',
                ], 404);
            }

            // Clear session after fetching
            session()->forget('salary_report_id');

            return response()->json([
                'success' => true,
                'data' => $salaryReport,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching salary report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getSalary(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'salary');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view employee salary details.'
            ], 403);
        }

        $id     = $request->emp_id;
        $salary = Salary::with(['earnings', 'payrollDeduction', 'payrollOvertime', 'payrollAddition', 'deductions', 'boxuser.user', 'history'])
            ->find($id);
        if (! $salary) {
            return response()->json([
                'success' => false,
                'message' => 'Salary record not found.',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data'    => $salary,
        ]);
    }

    public function update(Request $request)
    {
        // Simple validation - same as store
        $validator = Validator::make($request->all(), [
            'salary_id'        => 'required|exists:salaries,id',
            'box_user_id'      => 'required|integer|exists:box_users,id',
            'gross_salary'     => 'required|numeric|min:0',
            'salary_settings_id' => 'required|integer|exists:salary_settings,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->select('id', 'box_id', 'staff_role')->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canUpdate = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'update', 'salary');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canUpdate) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update employee salaries.'
            ], 403);
        }

        $userId = $user->id;

        if (! $boxUser) {
            return response()->json([
                'success' => false,
                'message' => 'Box user not found for the authenticated user.',
            ], 404);
        }

        $boxId = $boxUser->box_id;

        DB::beginTransaction();

        try {
            $salary = Salary::with(['earnings', 'deductions'])->findOrFail($request->salary_id);

            // Store old values for history
            $oldValue = [
                'earnings'   => $salary->earnings->toArray(),
                'deductions' => $salary->deductions->toArray(),
                'net_salary' => $salary->net_salary,
                'gross_salary' => $salary->gross_salary,
            ];

            // Use gross_salary as CTC and calculate all components using salary calculator (same as store)
            $ctc = $request->gross_salary;
            $salarySettingId = $request->salary_settings_id;

            // Determine if CTC is monthly or yearly (assume yearly if >= 100000, otherwise monthly)
            $period = ($ctc >= 100000) ? 'yearly' : 'monthly';

            // Create request for calculation
            $calcRequest = new Request([
                'ctc' => $ctc,
                'period' => $period,
                'salary_setting_id' => $salarySettingId
            ]);
            $calcRequest->setUserResolver(function () {
                return Auth::user();
            });

            // Call the calculation method
            $salarySettingController = new SalarySettingController();
            $calcResponse = $salarySettingController->calculateSalary($calcRequest);

            if ($calcResponse->getStatusCode() !== 200) {
                throw new \Exception('Failed to calculate salary components.');
            }

            $calcData = json_decode($calcResponse->getContent(), true);
            if (!isset($calcData['success']) || !$calcData['success'] || !isset($calcData['data'])) {
                throw new \Exception('Invalid calculation response.');
            }

            $calculated = $calcData['data'];

            // Get calculated totals
            $grossSalary = $calculated['totals']['monthly_gross'] ?? 0;
            $totalDeductions = $calculated['totals']['monthly_deductions'] ?? 0;
            $netSalary = $calculated['totals']['monthly_net'] ?? 0;

            $earningsData = [];

            // Map calculated earnings to database fields
            if (isset($calculated['earnings'])) {
                foreach ($calculated['earnings'] as $field => $amounts) {
                    $monthlyAmount = $amounts['monthly'] ?? 0;
                    $earningsData[$field] = $monthlyAmount;

                    // Handle special sum fields
                    if ($field === 'da') {
                        $earningsData['da_sum_amount'] = $monthlyAmount;
                    }
                    if ($field === 'hra') {
                        $earningsData['hra_sum_amount'] = $monthlyAmount;
                    }
                }
            }

            // Ensure all earnings fields exist (set to 0 if not calculated)
            $earningsFields = [
                'basic',
                'da',
                'hra',
                'conveyance',
                'allowance',
                'medical_allowance',
                'others',
                'special_allowance',
                'leave_travel_allowance',
                'city_compensatory_allowance',
                'sales_target_incentives',
                'shift_allowance',
                'transport_allowance',
                'food_allowance',
                'overtime_allowance',
                'uniform_allowance',
                'mobile_internet_allowance',
                'project_allowance',
                'education_allowance',
                'professional_allowance',
                'arrears_earnings'
            ];
            foreach ($earningsFields as $field) {
                if (!isset($earningsData[$field])) {
                    $earningsData[$field] = 0;
                }
            }
            if (!isset($earningsData['da_sum_amount'])) $earningsData['da_sum_amount'] = 0;
            if (!isset($earningsData['hra_sum_amount'])) $earningsData['hra_sum_amount'] = 0;
            $earningsData['updated_by'] = $userId;

            // Prepare deductions data from calculation results
            $deductionsData = [];

            // Map calculated deductions to database fields (handle field name mapping)
            if (isset($calculated['deductions'])) {
                foreach ($calculated['deductions'] as $field => $amounts) {
                    $monthlyAmount = $amounts['monthly'] ?? 0;

                    // Map field names: employee_pf -> pf, employee_esi -> esi, professional_tax -> prof_tax
                    $formField = $field;
                    if ($field === 'employee_pf') $formField = 'pf';
                    if ($field === 'employee_esi') $formField = 'esi';
                    if ($field === 'professional_tax') $formField = 'prof_tax';

                    $deductionsData[$formField] = $monthlyAmount;

                    // Handle sum amounts
                    if ($field === 'employee_pf') {
                        $deductionsData['pf_sum_amount'] = $monthlyAmount;
                    }
                    if ($field === 'employee_esi') {
                        $deductionsData['esi_sum_amoutn'] = $monthlyAmount; // Note: Database column has typo
                    }
                    if ($field === 'tds') {
                        $deductionsData['tds_sum_amount'] = $monthlyAmount;
                    }
                }
            }

            // Ensure all deduction fields exist (set to 0 if not calculated)
            $deductionFields = [
                'tds',
                'esi',
                'pf',
                'leave',
                'prof_tax',
                'labour_welfare',
                'othersdeductions',
                'salary_advance_recovery',
                'loan_emi_recovery',
                'canteen_deduction',
                'attendance_loss',
                'notice_period_recovery',
                'arrears_recovery',
                'insurance_premium_employee'
            ];
            foreach ($deductionFields as $field) {
                if (!isset($deductionsData[$field])) {
                    $deductionsData[$field] = 0;
                }
            }
            if (!isset($deductionsData['tds_sum_amount'])) $deductionsData['tds_sum_amount'] = 0;
            if (!isset($deductionsData['esi_sum_amoutn'])) $deductionsData['esi_sum_amoutn'] = 0; // Note: Database column has typo
            if (!isset($deductionsData['pf_sum_amount'])) $deductionsData['pf_sum_amount'] = 0;
            $deductionsData['updated_by'] = $userId;

            // Update main salary
            $salary->fill([
                'box_user_id'                 => $request->box_user_id,
                'salary_settings_id'          => $salarySettingId,
                'salary_payroll_deduction_id' => $request->salary_payroll_deduction_id ?? null,
                'salary_payroll_overtime_id'  => $request->salary_payroll_overtime_id ?? null,
                'salary_payroll_addition_id'  => $request->salary_payroll_addition_id ?? null,
                'gross_salary'                => $grossSalary,
                'ctc_amount'                  => $ctc, // Store original CTC amount entered by user
                'total_deductions'            => $totalDeductions,
                'net_salary'                  => $netSalary,
                'updated_by'                  => $userId,
            ])->save();

            // Update related earnings
            $salary->earnings->update($earningsData);

            // Update related deductions
            $salary->deductions->update($deductionsData);

            // Store new values for history
            $newValue = [
                'earnings'   => $salary->earnings->fresh()->toArray(),
                'deductions' => $salary->deductions->fresh()->toArray(),
                'net_salary' => $salary->net_salary,
                'gross_salary' => $salary->gross_salary,
            ];

            // Capture salary settings snapshot with calculated amounts for history (ONLY for history, NOT saved to salaries table)
            $settingsSnapshot = null;
            if ($salary->salary_settings_id) {
                // Always capture fresh snapshot from salary settings with calculated amounts
                $salarySetting = \App\Models\SalarySetting::where('id', $salary->salary_settings_id)
                    ->where('box_id', $boxId)
                    ->first();
                if ($salarySetting) {
                    $customData = \App\Models\SalarySetting::getCustomFieldsData($salarySetting->id, $boxId);
                    $settingsSnapshot = array_merge($salarySetting->toArray(), $customData);

                    // Add ALL calculated amounts to snapshot (complete calculation data from API)
                    $settingsSnapshot['_calculated_amounts'] = [
                        'ctc_amount' => $ctc,
                        'period' => $period,
                        'totals' => $calculated['totals'] ?? [
                            'monthly_gross' => $grossSalary,
                            'yearly_gross' => $calculated['totals']['yearly_gross'] ?? ($grossSalary * 12),
                            'monthly_deductions' => $totalDeductions,
                            'yearly_deductions' => $calculated['totals']['yearly_deductions'] ?? ($totalDeductions * 12),
                            'monthly_employer_contributions' => $calculated['totals']['monthly_employer_contributions'] ?? 0,
                            'yearly_employer_contributions' => $calculated['totals']['yearly_employer_contributions'] ?? 0,
                            'monthly_net' => $netSalary,
                            'yearly_net' => $calculated['totals']['yearly_net'] ?? ($netSalary * 12),
                            'monthly_total_earnings' => $calculated['totals']['monthly_total_earnings'] ?? 0,
                            'yearly_total_earnings' => $calculated['totals']['yearly_total_earnings'] ?? 0,
                            'monthly_total_reimbursements' => $calculated['totals']['monthly_total_reimbursements'] ?? 0,
                            'yearly_total_reimbursements' => $calculated['totals']['yearly_total_reimbursements'] ?? 0,
                            'monthly_total_onetime' => $calculated['totals']['monthly_total_onetime'] ?? 0,
                            'yearly_total_onetime' => $calculated['totals']['yearly_total_onetime'] ?? 0,
                        ],
                        'earnings' => $calculated['earnings'] ?? [], // All earnings including custom fields
                        'deductions' => $calculated['deductions'] ?? [], // All deductions including custom fields
                        'reimbursements' => $calculated['reimbursements'] ?? [], // All reimbursements including custom fields
                        'contributions' => $calculated['contributions'] ?? [], // All contributions including custom fields
                        'onetime' => $calculated['onetime'] ?? [], // All onetime components including custom fields
                        'metadata' => $calculated['metadata'] ?? [], // Complete metadata from calculation
                    ];

                    $settingsSnapshot['_snapshot_metadata'] = [
                        'captured_at' => now()->toDateTimeString(),
                        'salary_setting_id' => $salarySetting->id,
                        'salary_setting_name' => $salarySetting->name ?? null,
                        'box_id' => $boxId,
                        'calculation_basis' => [
                            'ctc_amount' => $ctc,
                            'period' => $period,
                            'calculated_at' => now()->toDateTimeString(),
                        ],
                    ];
                }
            }

            // NOTE: Snapshot is NOT saved to salaries table, only to salary_history for audit trail

            // Create history record
            SalaryHistory::create([
                'salary_id'   => $salary->id,
                'box_user_id' => $request->box_user_id,
                'box_id'      => $boxId,
                'changed_by'  => $userId,
                'change_type' => 'update',
                'remarks'     => $request->increment_reason ?? 'Salary updated',
                'old_value'   => $oldValue,
                'new_value'   => $newValue,
                'salary_settings_snapshot' => $settingsSnapshot, // Complete JSON snapshot
                'created_by'  => $userId,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Salary updated successfully!',
                'data'    => $salary->load(['earnings', 'deductions', 'payrollDeduction', 'payrollOvertime', 'payrollAddition', 'salarySetting']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating salary: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // Step 1: Simple validation - only required fields
        $validator = Validator::make($request->all(), [
            'box_user_id'        => 'required|integer|exists:box_users,id',
            'gross_salary'       => 'required|numeric|min:0',
            'salary_settings_id' => 'required|integer|exists:salary_settings,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->select('id', 'box_id', 'staff_role')->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canCreate = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'create', 'salary');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canCreate) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to create employee salaries.'
            ], 403);
        }

        $userId = $user->id;

        if (! $boxUser) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Box user not found for the authenticated user.',
            ], 404);
        }

        $boxId = $boxUser->box_id;

        DB::beginTransaction();

        try {
            // Step 2: Use gross_salary as CTC and calculate all components using salary calculator
            $ctc = $request->gross_salary;
            $salarySettingId = $request->salary_settings_id;

            // Determine if CTC is monthly or yearly (assume yearly if >= 100000, otherwise monthly)
            $period = ($ctc >= 100000) ? 'yearly' : 'monthly';

            // Create request for calculation
            $calcRequest = new Request([
                'ctc' => $ctc,
                'period' => $period,
                'salary_setting_id' => $salarySettingId
            ]);
            $calcRequest->setUserResolver(function () {
                return Auth::user();
            });

            // Call the calculation method
            $salarySettingController = new SalarySettingController();
            $calcResponse = $salarySettingController->calculateSalary($calcRequest);

            if ($calcResponse->getStatusCode() !== 200) {
                throw new \Exception('Failed to calculate salary components.');
            }

            $calcData = json_decode($calcResponse->getContent(), true);
            if (!isset($calcData['success']) || !$calcData['success'] || !isset($calcData['data'])) {
                throw new \Exception('Invalid calculation response.');
            }

            $calculated = $calcData['data'];

            // Get calculated totals
            $grossSalary = $calculated['totals']['monthly_gross'] ?? 0;
            $totalDeductions = $calculated['totals']['monthly_deductions'] ?? 0;
            $netSalary = $calculated['totals']['monthly_net'] ?? 0;

            // Prepare earnings data from calculation results
            $earningsData = [
                'salary_id' => null, // Will be set after salary creation
                'box_user_id' => $request->box_user_id,
                'box_id' => $boxId,
                'created_by' => $userId,
            ];

            // Map calculated earnings to database fields
            if (isset($calculated['earnings'])) {
                foreach ($calculated['earnings'] as $field => $amounts) {
                    $monthlyAmount = $amounts['monthly'] ?? 0;
                    $earningsData[$field] = $monthlyAmount;

                    // Handle special sum fields
                    if ($field === 'da') {
                        $earningsData['da_sum_amount'] = $monthlyAmount;
                    }
                    if ($field === 'hra') {
                        $earningsData['hra_sum_amount'] = $monthlyAmount;
                    }
                }
            }

            // Ensure all earnings fields exist (set to 0 if not calculated)
            $earningsFields = [
                'basic',
                'da',
                'hra',
                'conveyance',
                'allowance',
                'medical_allowance',
                'others',
                'special_allowance',
                'leave_travel_allowance',
                'city_compensatory_allowance',
                'sales_target_incentives',
                'shift_allowance',
                'transport_allowance',
                'food_allowance',
                'overtime_allowance',
                'uniform_allowance',
                'mobile_internet_allowance',
                'project_allowance',
                'education_allowance',
                'professional_allowance',
                'arrears_earnings'
            ];
            foreach ($earningsFields as $field) {
                if (!isset($earningsData[$field])) {
                    $earningsData[$field] = 0;
                }
            }
            if (!isset($earningsData['da_sum_amount'])) $earningsData['da_sum_amount'] = 0;
            if (!isset($earningsData['hra_sum_amount'])) $earningsData['hra_sum_amount'] = 0;

            // Prepare deductions data from calculation results
            $deductionsData = [
                'salary_id' => null, // Will be set after salary creation
                'box_user_id' => $request->box_user_id,
                'box_id' => $boxId,
                'created_by' => $userId,
            ];

            // Map calculated deductions to database fields (handle field name mapping)
            if (isset($calculated['deductions'])) {
                foreach ($calculated['deductions'] as $field => $amounts) {
                    $monthlyAmount = $amounts['monthly'] ?? 0;

                    // Map field names: employee_pf -> pf, employee_esi -> esi, professional_tax -> prof_tax
                    $formField = $field;
                    if ($field === 'employee_pf') $formField = 'pf';
                    if ($field === 'employee_esi') $formField = 'esi';
                    if ($field === 'professional_tax') $formField = 'prof_tax';

                    $deductionsData[$formField] = $monthlyAmount;

                    // Handle sum amounts
                    if ($field === 'employee_pf') {
                        $deductionsData['pf_sum_amount'] = $monthlyAmount;
                    }
                    if ($field === 'employee_esi') {
                        $deductionsData['esi_sum_amoutn'] = $monthlyAmount; // Note: Database column has typo
                    }
                    if ($field === 'tds') {
                        $deductionsData['tds_sum_amount'] = $monthlyAmount;
                    }
                }
            }

            // Ensure all deduction fields exist (set to 0 if not calculated)
            $deductionFields = [
                'tds',
                'esi',
                'pf',
                'leave',
                'prof_tax',
                'labour_welfare',
                'othersdeductions',
                'salary_advance_recovery',
                'loan_emi_recovery',
                'canteen_deduction',
                'attendance_loss',
                'notice_period_recovery',
                'arrears_recovery',
                'insurance_premium_employee'
            ];
            foreach ($deductionFields as $field) {
                if (!isset($deductionsData[$field])) {
                    $deductionsData[$field] = 0;
                }
            }
            if (!isset($deductionsData['tds_sum_amount'])) $deductionsData['tds_sum_amount'] = 0;
            if (!isset($deductionsData['esi_sum_amoutn'])) $deductionsData['esi_sum_amoutn'] = 0; // Note: Database column has typo
            if (!isset($deductionsData['pf_sum_amount'])) $deductionsData['pf_sum_amount'] = 0;

            // Step 3: Create Salary record
            $salary = Salary::create([
                'box_user_id'                 => $request->box_user_id,
                'box_id'                      => $boxId,
                'salary_settings_id'          => $salarySettingId,
                'salary_payroll_deduction_id' => $request->salary_payroll_deduction_id ?? null,
                'salary_payroll_overtime_id'  => $request->salary_payroll_overtime_id ?? null,
                'salary_payroll_addition_id'  => $request->salary_payroll_addition_id ?? null,
                'effective_from'              => now(),
                'gross_salary'                => $grossSalary,
                'ctc_amount'                  => $ctc, // Store original CTC amount entered by user
                'total_deductions'            => $totalDeductions,
                'net_salary'                  => $netSalary,
                'increment_reason'            => $request->increment_reason ?? 'Initial salary creation',
                'created_by'                  => $userId,
            ]);

            // Step 4: Create earnings with calculated values
            $earningsData['salary_id'] = $salary->id;
            SalaryEarning::create($earningsData);

            // Step 5: Create deductions with calculated values
            $deductionsData['salary_id'] = $salary->id;
            SalaryDeduction::create($deductionsData);

            // Step 6: Capture salary settings snapshot with calculated amounts (ONLY for history, NOT saved to salaries table)
            $settingsSnapshot = null;
            if ($salarySettingId) {
                $salarySetting = \App\Models\SalarySetting::where('id', $salarySettingId)
                    ->where('box_id', $boxId)
                    ->first();
                if ($salarySetting) {
                    $customData = \App\Models\SalarySetting::getCustomFieldsData($salarySettingId, $boxId);
                    $settingsSnapshot = array_merge($salarySetting->toArray(), $customData);

                    // Add ALL calculated amounts to snapshot (complete calculation data from API)
                    $settingsSnapshot['_calculated_amounts'] = [
                        'ctc_amount' => $ctc,
                        'period' => $period,
                        'totals' => $calculated['totals'] ?? [
                            'monthly_gross' => $grossSalary,
                            'yearly_gross' => $calculated['totals']['yearly_gross'] ?? ($grossSalary * 12),
                            'monthly_deductions' => $totalDeductions,
                            'yearly_deductions' => $calculated['totals']['yearly_deductions'] ?? ($totalDeductions * 12),
                            'monthly_employer_contributions' => $calculated['totals']['monthly_employer_contributions'] ?? 0,
                            'yearly_employer_contributions' => $calculated['totals']['yearly_employer_contributions'] ?? 0,
                            'monthly_net' => $netSalary,
                            'yearly_net' => $calculated['totals']['yearly_net'] ?? ($netSalary * 12),
                            'monthly_total_earnings' => $calculated['totals']['monthly_total_earnings'] ?? 0,
                            'yearly_total_earnings' => $calculated['totals']['yearly_total_earnings'] ?? 0,
                            'monthly_total_reimbursements' => $calculated['totals']['monthly_total_reimbursements'] ?? 0,
                            'yearly_total_reimbursements' => $calculated['totals']['yearly_total_reimbursements'] ?? 0,
                            'monthly_total_onetime' => $calculated['totals']['monthly_total_onetime'] ?? 0,
                            'yearly_total_onetime' => $calculated['totals']['yearly_total_onetime'] ?? 0,
                        ],
                        'earnings' => $calculated['earnings'] ?? [], // All earnings including custom fields
                        'deductions' => $calculated['deductions'] ?? [], // All deductions including custom fields
                        'reimbursements' => $calculated['reimbursements'] ?? [], // All reimbursements including custom fields
                        'contributions' => $calculated['contributions'] ?? [], // All contributions including custom fields
                        'onetime' => $calculated['onetime'] ?? [], // All onetime components including custom fields
                        'metadata' => $calculated['metadata'] ?? [], // Complete metadata from calculation
                    ];

                    $settingsSnapshot['_snapshot_metadata'] = [
                        'captured_at' => now()->toDateTimeString(),
                        'salary_setting_id' => $salarySetting->id,
                        'salary_setting_name' => $salarySetting->name ?? null,
                        'box_id' => $boxId,
                        'calculation_basis' => [
                            'ctc_amount' => $ctc,
                            'period' => $period,
                            'calculated_at' => now()->toDateTimeString(),
                        ],
                    ];
                }
            }

            // Step 7: Create Salary History
            SalaryHistory::create([
                'salary_id'   => $salary->id,
                'box_user_id' => $request->box_user_id,
                'box_id'      => $boxId,
                'changed_by'  => $userId,
                'change_type' => 'create',
                'remarks'     => $request->increment_reason ?? 'Initial salary creation',
                'old_value'   => null,
                'new_value'   => [
                    'net_salary' => $netSalary,
                    'gross_salary' => $grossSalary
                ],
                'salary_settings_snapshot' => $settingsSnapshot, // Complete JSON snapshot
                'created_by'  => $userId,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Employee salary added successfully.',
                'data'    => $salary->load(['earnings', 'deductions', 'payrollDeduction', 'payrollOvertime', 'payrollAddition', 'salarySetting']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function salary_delete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:salaries,id',
        ]);

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->firstOrFail();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canDelete = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'delete', 'salary');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canDelete) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete employee salaries.'
            ], 403);
        }

        DB::beginTransaction();

        try {
            $salary = Salary::with(['earnings', 'deductions', 'history'])->find($request->id);

            if (! $salary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salary not found.',
                ], 404);
            }

            // Delete related earnings, deductions, history
            $salary->earnings()->delete();
            $salary->deductions()->delete();
            $salary->history()->delete();

            // Delete the main salary
            $salary->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Salary and all related data deleted successfully.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }
}
