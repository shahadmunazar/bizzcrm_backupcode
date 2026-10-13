<?php
namespace App\Http\Controllers\Employee;
use App\Http\Controllers\Controller;
use App\Jobs\{ExportAttendanceJob, MarkAllUsersAbsentJob, UpdateAttendanceStatuses, GenerateShiftForBoxEmployees};
use App\Models\{Attendance, AttendanceBreak, Team, Activity, AttendanceCorrection, AttendanceRealtimeLogs, BiometricMachine, Box, BoxUser, Country, Department, Holiday, HolidayType, LeaveRequest, Shift, LeaveTransaction, LeaveType};
use Carbon\{Carbon, CarbonInterval, CarbonPeriod};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Artisan, Auth, DB, Log, Storage, Validator};
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\BaseController;

class AttendaceController extends BaseController
{

    public function MachinePunchIn()
    {
        return view('frontend.attendance.machinetest');
    }

    /**
     * Process biometric attendance and return console output
     */
    public function ProcessBiometricAttendance(Request $request)
    {
        try {
            // Call the artisan command
            $exitCode = Artisan::call('attendance:process-biometric');

            // Get command output (this includes all $this->info() messages)
            $output = Artisan::output();

            // Parse output lines
            $outputLines = [];
            if (!empty($output)) {
                $lines = explode("\n", trim($output));
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        $outputLines[] = $line;
                    }
                }
            }

            // Extract summary information from output
            $summary = [
                'machines_processed' => 0,
                'logs_found' => 0,
                'user_combinations' => 0,
                'combinations_processed' => 0,
                'machines_updated' => 0
            ];

            // Parse summary from output
            foreach ($outputLines as $line) {
                if (preg_match('/(\d+)\s+machines/', $line, $matches)) {
                    $summary['machines_processed'] = (int) $matches[1];
                }
                if (preg_match('/(\d+)\s+total logs/', $line, $matches)) {
                    $summary['logs_found'] = (int) $matches[1];
                }
                if (preg_match('/(\d+)\s+unique user-date combinations/', $line, $matches)) {
                    $summary['user_combinations'] = (int) $matches[1];
                }
                if (preg_match('/(\d+)\s+user-date combinations processed/', $line, $matches)) {
                    $summary['combinations_processed'] = (int) $matches[1];
                }
                if (preg_match('/(\d+)\s+machine sync times/', $line, $matches)) {
                    $summary['machines_updated'] = (int) $matches[1];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Biometric attendance processed successfully.',
                'exit_code' => $exitCode,
                'output' => $output,
                'output_lines' => $outputLines,
                'summary' => $summary,
                'output_count' => count($outputLines)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing biometric attendance.',
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ], 500);
        }
    }

    public function PunchInUser(Request $request)
    {
        try {
            $request->validate([
                'machine_id' => 'required|exists:biometric_machines,serial_no',
                'user_id' => 'required',
            ]);

            $userIdFormatted = str_pad($request->user_id, 8, '0', STR_PAD_LEFT);
            $ioTime = now()->format('YmdHis');
            $logJson = [
                "fk_bin_data_lib" => "M50",
                "user_id" => $userIdFormatted,
                "verify_mode" => 2,
                "io_mode" => 1,
                "io_time" => $ioTime,
            ];
            $punch = AttendanceRealtimeLogs::create([
                'device_id' => $request->machine_id,
                'user_id' => $userIdFormatted,
                'verify_mode' => 2,
                'io_mode' => 1,
                'io_time' => $ioTime,
                'log_image' => null,
                'log_data_json' => json_encode($logJson),
                'received_at' => now(),
                'client_ip' => $request->ip(),
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Punch logged successfully.',
                'data' => $punch,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function GetAllMachines()
    {
        try {
            $machines = BiometricMachine::where('status', 'Active')
                ->select('id', 'machine_no', 'serial_no', 'machine_type', 'machine_name')
                ->orderBy('machine_no', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Active machines fetched successfully.',
                'data' => $machines,
            ], 200);

        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while fetching machines.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function AttendaceVIew()
    {
        return view('frontend.attendance.performace_report');
    }
    public function export_attendace_pdf(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canExport = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'export', 'admin');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canExport) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to export attendance reports.'
            ], 403);
        }

        try {
            $user = Auth::user();
            $userId = $user->id;
            $BoxUser = BoxUser::where('user_id', $userId)->first();
            $box = Box::where('id', $BoxUser->box_id)
                ->select('id', 'superadmin_id', 'name', 'working_days', 'day_start_time', 'day_end_time')
                ->first();

            if (!$box) {
                return response()->json([
                    'error' => true,
                    'message' => 'Box not found for this user.',
                ], 404);
            }

            $boxId = $box->id;
            $month = (int) $request->input('month', now()->format('m'));
            $year = (int) $request->input('year', now()->format('Y'));
            $employeeId = $request->input('employee_id');
            $scope = $request->get('scope');

            $allMonthlyHolidays = Holiday::with('holidayType')
                ->where('box_id', $boxId)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->get();

            $monthlyHolidays = $allMonthlyHolidays
                ->filter(function ($holiday) use ($boxId) {
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
                    return strcasecmp($calcInCalendar, 'No') === 0;
                })->values();

                $monthlyHolidayCount = $monthlyHolidays->count();
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            $appliedHolidayCodes = \App\Models\HolidayType::where('box_id', $boxId)
                ->where('calculate_in_calendar', 'Yes')
                ->pluck('holiday_code')
                ->map(fn($code) => strtolower(trim($code)))
                ->toArray();

            $employeesQuery = BoxUser::with([
                'user',
                'attendances' => fn($query) => $query
                    ->whereYear('attendance_date', $year)
                    ->whereMonth('attendance_date', $month),
                'departmentRelation',
                'designationRelation',
                'shift',
                'box',
            ])->where('box_id', $boxId);

            if ($scope === 'team' && $boxUser) {
                $subordinateIds = $this->getAllSubordinateIds($boxId, $user, $boxUser);
                $employeesQuery->whereIn('id', $subordinateIds);
            }

            if ($employeeId) {
                $employeesQuery->where('id', $employeeId);
            }

            $employees = $employeesQuery->get();

            // Helper conversions
            $decimalToHHMM = function ($decimal) {
                if ($decimal === null || $decimal <= 0) {
                    return '--:--';
                }

                $hours = floor($decimal);
                $minutes = round(($decimal - $hours) * 60);
                if ($minutes == 60) {
                    $hours++;
                    $minutes = 0;
                }
                return sprintf('%02d:%02d', $hours, $minutes);
            };

            $decimalToHMS = function ($decimal) {
                if ($decimal === null || $decimal <= 0) {
                    return '--:--:--';
                }

                $hours = floor($decimal);
                $minutes = floor(($decimal - $hours) * 60);
                $seconds = round((($decimal - $hours) * 60 - $minutes) * 60);
                if ($seconds == 60) {
                    $minutes++;
                    $seconds = 0;
                }
                if ($minutes == 60) {
                    $hours++;
                    $minutes = 0;
                }
                return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            };

            // ✅ Main data builder
            $allData = $employees->map(function ($employee) use ($daysInMonth, $month, $year, $decimalToHHMM, $decimalToHMS, $monthlyHolidayCount, $monthlyHolidays, $appliedHolidayCodes, $allMonthlyHolidays) {
                $attendanceRecords = $employee->attendances;
                $shift = $employee->shift;
                $hasShift = $employee->shift_id && optional($shift)->shift_enabled;
                $dailyHours = 8;

                // Determine daily working hours
                if ($shift && isset($shift->shift_hours) && str_contains($shift->shift_hours, ':')) {
                    [$h, $m] = array_pad(explode(':', $shift->shift_hours), 2, 0);
                    $dailyHours = (int) $h + ((int) $m / 60);
                } elseif ($shift && $shift->in_time && $shift->out_time) {
                    $dailyHours = Carbon::parse($shift->in_time)->diffInHours(Carbon::parse($shift->out_time));
                } elseif ($employee->box && $employee->box->day_start_time && $employee->box->day_end_time) {
                    $dailyHours = Carbon::parse($employee->box->day_start_time)->diffInHours(Carbon::parse($employee->box->day_end_time));
                }

                // Working days parsing
                $parseDays = function ($input) {
                    if (!$input) {
                        return [];
                    }

                    if (is_array($input)) {
                        $days = [];
                        foreach ($input as $item) {
                            $days = array_merge($days, array_map('trim', explode(',', $item)));
                        }
                        return array_unique($days);
                    } elseif (is_string($input)) {
                        $decoded = json_decode($input, true);
                        if (is_array($decoded)) {
                            return array_unique(array_map('trim', $decoded));
                        }
                        return array_unique(array_map('trim', explode(',', $input)));
                    }
                    return [];
                };

                $userWorkingDays = $parseDays($employee->working_days ?? $employee->box->working_days);
                if (empty($userWorkingDays)) {
                    $userWorkingDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                }

                // ✅ Calculate total working days in month
                $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

                if ($hasShift) {
                    $monthlyCalc = $this->calculateWorkingDaysMonthsAndHours($startOfMonth, $endOfMonth, $shift, $userWorkingDays, $dailyHours);
                    $totalWorkingDaysMonth = $monthlyCalc['working_days'];
                } else {
                    $period = CarbonPeriod::create($startOfMonth, $endOfMonth);
                    $totalWorkingDaysMonth = collect($period)
                        ->filter(fn($day) => in_array($day->format('l'), $userWorkingDays))
                        ->reject(fn($day) => $monthlyHolidays->contains('date', $day->toDateString()))
                        ->count();
                }

                // ✅ Fetch leave data
                $totalLeavesData = LeaveRequest::with('leaveType')
                    ->where('box_user_id', $employee->id)
                    ->where('status', 'approved')
                    ->where(function ($query) use ($month, $year) {
                        $query->whereYear('from_date', $year)
                            ->whereMonth('from_date', $month)
                            ->orWhere(function ($q) use ($month, $year) {
                                $q->whereYear('to_date', $year)
                                    ->whereMonth('to_date', $month);
                            });
                    })
                    ->get();

                // Separate regular leaves from applied holidays
                $regularLeavesData = $totalLeavesData->filter(function ($leave) use ($appliedHolidayCodes) {
                    $leaveCode = optional($leave->leaveType)->leave_code;
                    return !in_array(strtolower(trim((string)$leaveCode)), $appliedHolidayCodes);
                });

                $totalLeaves = 0;
                $paidDays = 0;
                $unpaidDays = 0;
                
                foreach ($regularLeavesData as $leave) {
                    $correctedTotalDays = 0;
                    if ($leave->from_date == $leave->to_date) {
                        $correctedTotalDays = $leave->total_days;
                    } else {
                        $leaveStart = Carbon::parse($leave->from_date);
                        $leaveEnd = Carbon::parse($leave->to_date);
                        for ($date = $leaveStart->copy(); $date->lte($leaveEnd); $date->addDay()) {
                            $dateStr = $date->toDateString();
                            $dayName = $date->format('l');
                            $isWeeklyOff = !in_array($dayName, $userWorkingDays);
                            $isHoliday = $monthlyHolidays->contains('date', $dateStr);
                            if (!$isWeeklyOff && !$isHoliday) {
                                $correctedTotalDays += 1;
                            }
                        }
                    }
                    $totalLeaves += $correctedTotalDays;
                    
                    $adjustedPaid = min($leave->paid_days, $correctedTotalDays);
                    $adjustedUnpaid = max(0, $correctedTotalDays - $adjustedPaid);
                    
                    $paidDays += $adjustedPaid;
                    $unpaidDays += $adjustedUnpaid;
                }

                // ✅ SUM of Late Duration and Early Checkout
                $totalLateSeconds = 0;
                $totalEarlyOutSeconds = 0;

                foreach ($attendanceRecords as $record) {
                    if ($record->late_duration) {
                        try {
                            $interval = CarbonInterval::createFromFormat('H:i:s', $record->late_duration);
                            $totalLateSeconds += $interval->totalSeconds;
                        } catch (\Exception $e) {
                        }
                    }

                    if ($record->early_check_out) {
                        try {
                            $interval = CarbonInterval::createFromFormat('H:i:s', $record->early_check_out);
                            $totalEarlyOutSeconds += $interval->totalSeconds;
                        } catch (\Exception $e) {
                        }
                    }
                }

                $totalLateDuration = gmdate('H:i:s', $totalLateSeconds);
                $totalEarlyCheckOutDuration = gmdate('H:i:s', $totalEarlyOutSeconds);

                // ✅ Calculate expected vs actual hours
                $weeklyOff = $daysInMonth - $totalWorkingDaysMonth;
                $totalWorkingDaysMonth -= $monthlyHolidayCount;
                if ($totalWorkingDaysMonth < 0) {
                    $totalWorkingDaysMonth = 0;
                }

                $expectedHoursDecimal = $dailyHours * $totalWorkingDaysMonth;
                $expectedHours = $decimalToHHMM($expectedHoursDecimal);
                $totalProductionHours = 0;
                $totalOverTimeHours = 0;

                $dayWiseStatus = [];

                // ✅ Loop through each day
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
                    $record = $attendanceRecords->firstWhere('attendance_date', $date);

                    // ✅ Normalize and determine status
                    $status = $record ? trim($record->status) : 'Absent';
                    $attendanceStatus = $record ? trim($record->attendance_status) : '';
                    
                    if (str_contains(strtolower($status), 'punch out') || str_contains(strtolower($attendanceStatus), 'punch out')) {
                        $status = 'Absent';
                    }
                    
                    $isHoliday = $monthlyHolidays->contains('date', $date);
                    if ($isHoliday && (!$record || strtolower($status) === 'absent')) {
                        $status = 'Holiday';
                    }

                    // Check holiday logic per user request
                    if (strtolower($status) === 'holiday') {
                        $holidayOnDate = $allMonthlyHolidays->first(function($h) use ($date) {
                            return \Carbon\Carbon::parse($h->date)->toDateString() === $date;
                        });

                        if ($holidayOnDate) {
                            $calcInCalendar = 'No';
                            if ($holidayOnDate->holidayType) {
                                $calcInCalendar = $holidayOnDate->holidayType->calculate_in_calendar;
                            } elseif ($holidayOnDate->holiday_type_id) {
                                $hType = \App\Models\HolidayType::find($holidayOnDate->holiday_type_id);
                                if ($hType) {
                                    $calcInCalendar = $hType->calculate_in_calendar;
                                }
                            } elseif ($holidayOnDate->holiday_type) {
                                $hType = \App\Models\HolidayType::where('box_id', $holidayOnDate->box_id)
                                    ->where('holiday_type', $holidayOnDate->holiday_type)
                                    ->first();
                                if ($hType) {
                                    $calcInCalendar = $hType->calculate_in_calendar;
                                }
                            }

                            if (strcasecmp($calcInCalendar, 'Yes') === 0) {
                                $hasApprovedLeave = isset($totalLeavesData) ? $totalLeavesData->contains(function ($leave) use ($date) {
                                    return $leave->from_date <= $date && $leave->to_date >= $date;
                                }) : false;

                                if (!$hasApprovedLeave) {
                                    $status = 'Absent';
                                    $isHoliday = false;
                                } else {
                                    $status = 'Holiday Approved';
                                    $isHoliday = true;
                                }
                            } else {
                                $status = 'Holiday';
                                $isHoliday = true;
                            }
                        }
                    }

                    // Extract the leave code or base status (remove -approved, -pending)
                    $baseStatus = explode('-', $status)[0];
                    $baseStatus = trim($baseStatus);
                    
                    if (strlen($baseStatus) <= 3) {
                        // If it's already a short code like CL, ML, H, use it directly
                        $shortStatus = strtoupper($baseStatus);
                    } else {
                        // Otherwise, take the first letter of each word (up to 2 letters)
                        $shortStatus = strtoupper(substr(
                            collect(preg_split('/[\s_]+/', $baseStatus))->map(fn($w) => substr($w, 0, 1))->implode(''),
                            0,
                            2
                        ));
                    }

                    $productionHours = 0;
                    if ($record && $record->production_hours) {
                        [$h, $m, $s] = array_pad(explode(':', $record->production_hours), 3, 0);
                        $productionHours = $h + ($m / 60) + ($s / 3600);
                        $totalProductionHours += $productionHours;
                    }

                    $overTimeHours = 0;
                    if ($record && $record->over_time) {
                        [$h, $m, $s] = array_pad(explode(':', $record->over_time), 3, 0);
                        $overTimeHours = $h + ($m / 60) + ($s / 3600);
                        $totalOverTimeHours += $overTimeHours;
                    }

                    $dayWiseStatus[] = [
                        'day_number' => $day,
                        'date' => $date,
                        'status' => $shortStatus,
                        'full_status' => ucwords(str_replace('-', ' ', $status)),
                        'check_in' => $record->check_in ?? null,
                        'check_out' => $record->check_out ?? null,
                        'working_hours' => $decimalToHHMM($productionHours),
                        'over_time' => $decimalToHHMM($overTimeHours),
                        'is_holiday' => $isHoliday,
                        'late_duration' => $record->late_duration ?? '--:--:--',
                        'early_checkout' => $record->early_check_out ?? '--:--:--',
                    ];
                }

                // ✅ Count attendance types
                $totalPresent = collect($dayWiseStatus)->where('full_status', 'Present')->count();
                $totalAbsent = collect($dayWiseStatus)->where('full_status', 'Absent')->count();
                $totalCompOffCount = collect($dayWiseStatus)->where('full_status', 'Comp Off')->count();
                
                $employeeHolidayCount = collect($dayWiseStatus)->filter(function ($day) use ($appliedHolidayCodes) {
                    if ($day['is_holiday']) return true; // Global holiday
                    if (in_array(strtolower(trim($day['status'])), $appliedHolidayCodes)) return true; // Applied holiday leave
                    
                    return stripos($day['full_status'], 'Holiday') !== false || stripos($day['full_status'], 'Gazetted') !== false;
                })->count();

                $efficiency = $expectedHoursDecimal > 0
                    ? round(($totalProductionHours / $expectedHoursDecimal) * 100, 2)
                    : 0;

                // ✅ Add Comp Off to paid days
                $totalPaidDays = $totalPresent + $employeeHolidayCount + $weeklyOff + $paidDays + $totalCompOffCount;

                $shortHoursDecimal = max($expectedHoursDecimal - $totalProductionHours, 0);
                $shortHours = $decimalToHHMM($shortHoursDecimal);

                return [
                    'employee_id' => $employee->employee_id,
                    'employee_name' => optional($employee->user)->name,
                    'department' => (strtolower($employee->staff_role ?? '') === 'admin') ? 'Owner' : (optional($employee->departmentRelation)->name ?? '-'),
                    'designation' => (strtolower($employee->staff_role ?? '') === 'admin') ? 'Owner' : (optional($employee->designationRelation)->name ?? '-'),
                    'shift_name' => optional($employee->shift)->shift_name,
                    'day_wise_status' => $dayWiseStatus,
                    'summary' => [
                        'total_present' => $totalPresent,
                        'total_absent' => $totalAbsent,
                        'total_leave' => $totalLeaves,
                        'total_comp_off' => $totalCompOffCount,
                        'total_wo' => $weeklyOff,
                        'total_holidays' => $employeeHolidayCount,
                        'total_working_days_month' => $totalWorkingDaysMonth,
                        'expected_hours' => $expectedHours,
                        'total_production_hours' => $decimalToHHMM($totalProductionHours),
                        'short_hours' => $shortHours,
                        'total_overtime_hours' => $decimalToHHMM($totalOverTimeHours),
                        'total_late_duration' => $totalLateDuration,
                        'total_early_checkout' => $totalEarlyCheckOutDuration,
                        'efficiency_percent' => $efficiency,
                        'daysInMonth' => $daysInMonth,
                        'totalPaidDays' => $totalPaidDays,
                        'paid_days' => $paidDays,
                        'unpaidDays' => $unpaidDays,
                    ],
                ];
            });

            // ✅ Add report period
            $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
            $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

            // ✅ Final Response
            $responseData = [
                'box' => [
                    'id' => $box->id,
                    'name' => $box->name,
                ],
                'from_date' => $startOfMonth,
                'to_date' => $endOfMonth,
                'month' => $month,
                'year' => $year,
                'days_in_month' => $daysInMonth,
                'total_holidays' => $monthlyHolidayCount,
                'holiday_dates' => $monthlyHolidays->pluck('date'),
                'total_employees' => $employees->count(),
                'performance_data' => $allData,
            ];

            if ($request->has('is_download')) {
                $pdf = Pdf::loadView('frontend.attendance.performance_report_pdf', ['data' => $responseData])
                    ->setPaper('a4', 'landscape');
                return $pdf->download('attendance_report_' . $month . '_' . $year . '.pdf');
            }

            return response()->json([
                'success' => true,
                'data' => $responseData,
            ]);

        } catch (\Throwable $th) {
            Log::error("Error in export_attendance_pdf: " . $th->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Something went wrong.',
                'exception' => $th->getMessage(),
            ], 500);
        }
    }

    public function export_attendace_admin(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canExport = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'export', 'admin');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canExport) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to export attendance reports.'
            ], 403);
        }

        try {
            $validated = $request->validate([
                'type' => 'required|in:admin,employee',
            ]);
            $type = $validated['type'];
            $user = Auth::user();
            $boxUser = BoxUser::where('user_id', $user->id)->with('box')->firstOrFail();
            $boxName = optional($boxUser->box)->name ?? 'unknown_box';
            $safeBoxName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $boxName);
            $fileName = 'attendance_export_' . now()->format('Y_m_d_H_i_s') . '_' . Str::random(5) . '.xlsx';
            $relativePath = "exports/attendance/{$safeBoxName}/{$fileName}";
            ExportAttendanceJob::dispatch($boxName, $fileName, $type, $user->id);
            return response()->json([
                'message' => 'Export started. You will be notified once it is ready.',
                'file_url' => url(Storage::url($relativePath)),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed.',
                'messages' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Failed to start export process.',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function GetUpcomingBirthday()
    {
        try {
            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();
            if (!$boxUser) {
                return response()->json(['data' => []]);
            }
            $boxId = $boxUser->box_id;
            $today = now()->startOfDay();
            $limitDate = now()->addDays(30)->endOfDay();
            $usersInBox = BoxUser::where('box_id', $boxId)
                ->whereNotNull('birthday')
                ->with(['departmentRelation', 'user'])
                ->get()
                ->map(function ($user) use ($today, $limitDate) {
                    $birthdayThisYear = \Carbon\Carbon::parse($user->birthday)->year($today->year);
                    if ($birthdayThisYear->lt($today)) {
                        $birthdayThisYear->addYear();
                    }

                    if ($birthdayThisYear->between($today, $limitDate)) {
                        $imageUrl = $user->profile_image_path
                            ? asset('storage/' . $user->profile_image_path)
                            : null;
                        $isOwner = strtolower($user->staff_role ?? '') === 'admin';
                        return [
                            'name' => $user->user->name ?? 'No Name',
                            'date' => $birthdayThisYear->format('d-m-Y'),
                            'designation' => $isOwner ? 'Owner' : ($user->departmentRelation?->name ?? 'No Designation'),
                            'image' => $imageUrl,
                        ];
                    }
                    return null;
                })
                ->filter() // remove nulls
                ->values();
            return response()->json(['data' => $usersInBox]);
        } catch (\Throwable $th) {
            return response()->json(['data' => []], 500);
        }
    }

    public function attendanceOverview(Request $request)
    {
        try {
            $year = $request->year ?? now()->year;
            $month = str_pad($request->month ?? now()->month, 2, '0', STR_PAD_LEFT);
            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)
                ->select('id', 'box_id')
                ->first();
            
            if (!$boxUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not linked to a BoxUser.',
                ], 403);
            }

            $attendances = Attendance::where('box_user_id', $boxUser->id)
                ->whereYear('attendance_date', $year)
                ->whereMonth('attendance_date', $month)
                ->get();
                
            $totalDays = $attendances->count();

            // Calculate Present
            $presentCount = $attendances->filter(function ($a) {
                return in_array(strtolower(trim($a->status ?? '')), ['present']);
            })->count();

            // Calculate Punch Out Miss
            $punchOutMissCount = $attendances->filter(function ($a) {
                $status = strtolower(trim($a->status ?? ''));
                $attStatus = strtolower(trim($a->attendance_status ?? ''));
                return $status === 'punch out miss' || $status === 'punch_out_miss' || $attStatus === 'punch out miss' || $attStatus === 'punch_out_miss';
            })->count();

            // Calculate Absent
            $absentCount = $attendances->filter(function ($a) {
                $status = strtolower(trim($a->status ?? ''));
                return in_array($status, ['absent']);
            })->count();

            // Calculate Weekly Off
            $weeklyOffCount = $attendances->filter(function ($a) {
                $status = strtolower(trim($a->status ?? ''));
                return str_contains($status, 'weekly off');
            })->count();

            // Calculate Holiday
            $holidayCount = $attendances->filter(function ($a) {
                $status = strtolower(trim($a->status ?? ''));
                return str_contains($status, 'holiday') || str_contains($status, 'gazetted') || str_contains($status, 'comp off');
            })->count();

            // Calculate Half Day
            $halfDayCount = $attendances->filter(function ($a) {
                $status = strtolower(trim($a->status ?? ''));
                return str_contains($status, 'half day');
            })->count();

            // Calculate Short Leave
            $shortLeaveCount = $attendances->filter(function ($a) {
                $status = strtolower(trim($a->status ?? ''));
                return str_contains($status, 'short leave');
            })->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_attendance' => $totalDays,
                    'raw_counts' => [
                        'present' => $presentCount,
                        'punch_out_miss' => $punchOutMissCount,
                        'absent' => $absentCount,
                        'weekly_off' => $weeklyOffCount,
                        'holiday' => $holidayCount,
                        'half_day' => $halfDayCount,
                        'short_leave' => $shortLeaveCount
                    ]
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => app()->isProduction() ? null : $th->getMessage(),
            ], 500);
        }
    }

    public function employeeDashboardStats(Request $request)
    {
        try {
            $year = $request->year ?? now()->year;
            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)
                ->select('id', 'box_id')
                ->first();
            if (!$boxUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not linked to a BoxUser.',
                ], 403);
            }
            $leaveQuery = LeaveRequest::where('box_user_id', $boxUser->id)
                ->whereYear('from_date', $year);

            $appliedLeaveCount = (clone $leaveQuery)->count();
            $approvedLeaveCount = (clone $leaveQuery)->where('status', 'approved')->count();
            $rejectedLeaveCount = (clone $leaveQuery)->whereIn('status', ['rejected', 'declined'])->count();
            $pendingLeaveCount = (clone $leaveQuery)->where('status', 'pending')->count();

            $baseQuery = Attendance::where('box_user_id', $boxUser->id)
                ->whereYear('attendance_date', $year);
            $presentCount = (clone $baseQuery)->where('status', 'present')->count();
            $absentCount = (clone $baseQuery)->where('status', 'absent')->count();
            $lateCount = (clone $baseQuery)->whereNotNull('late_duration')->count();
            $onTimeCount = (clone $baseQuery)->whereNull('late_duration')->count();
            return response()->json([
                'success' => true,
                'data' => [
                    'present' => $presentCount,
                    'absent' => $absentCount,
                    'late' => $lateCount,
                    'on_time' => $onTimeCount,
                    'approved_leaves' => $approvedLeaveCount,
                    'applied_leaves' => $appliedLeaveCount,
                    'rejected_leaves' => $rejectedLeaveCount,
                    'pending_leaves' => $pendingLeaveCount,
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => app()->isProduction() ? null : $th->getMessage(),
            ], 500);
        }
    }

    public function auto_attendance(Request $request)
    {
        try {
            $user = Auth::user();

            // Get box user
            $boxUser = BoxUser::where('user_id', $user->id)->first();

            if (!$boxUser) {
                return response()->json(['error' => 'Box user not found'], 404);
            }

            // Get attendance record
            $attendanceId = $request->input('attendance_id');

            if ($attendanceId) {
                $attendance = Attendance::where('id', $attendanceId)
                    ->where('box_user_id', $boxUser->id)
                    ->first();
            } else {
                $attendance = Attendance::where('box_user_id', $boxUser->id)
                    ->whereDate('created_at', now()->toDateString())
                    ->latest()
                    ->first();
            }

            if (!$attendance) {
                return response()->json(['error' => 'Attendance record not found'], 404);
            }
            if ($attendance->check_out || $attendance->status === 'Punch Out Miss') {
                return response()->json(['message' => 'Already punched out or marked as missed.'], 200);
            }
            $attendance->update([
                'check_out' => now(),
                'status' => 'Punch Out Miss',
                'is_auto_punch' => true,
                'production_hours' => '00:00:00',
                'working_hours' => '00:00:00',
                'updated_by' => $user->id,
            ]);
            return response()->json(['message' => 'Auto punch-out (miss) updated successfully']);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Server error',
                'details' => $th->getMessage(),
            ], 500);
        }
    }


    private function formatBoxUser($boxUser, $name = null)
    {
        return [
            'name' => $name ?? $boxUser?->user?->name ?? 'N/A',
            'image' => $boxUser?->profile_image_path
                ? asset('storage/' . $boxUser->profile_image_path)
                : null,
            'department' => $boxUser?->departmentRelation?->name ?? 'N/A',
            'employee_id' => $boxUser?->employee_id ?? 'N/A',
        ];
    }

    private function getAllSubordinateIds($boxId, $user, $boxUser)
    {
        return BoxUser::getAllSubordinateIds($boxId, is_object($user) ? $user->id : $user, $boxUser);
    }

    public function getAdminRequestdata(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'admin_leaves');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to view leave requests.'
            ], 403);
        }
        try {
            $userId = Auth::id();
            $boxUser = BoxUser::select('box_id')
                ->where('user_id', $userId)
                ->first();
            if (!$boxUser) {
                return response()->json(['error' => 'No box user found for the authenticated user.'], 404);
            }
            $query = LeaveRequest::with([
                'boxUser.user:id,name',
                'boxUser.departmentRelation',
                'boxUser.designationRelation',
                'leaveType:id,leave_type',
                'approvedByUser:id,name',
            ])
                ->where('box_id', $boxUser->box_id)
                ->orderBy('id', 'desc');

            if ($request->filled('leave_type')) {
                $query->where('leave_type', $request->leave_type);
            }
            if ($request->filled('status')) {
                $status = strtolower($request->status);
                if (in_array($status, ['declined', 'rejected'])) {
                    $query->whereIn('status', ['declined', 'rejected']);
                } else {
                    $query->where('status', $status);
                }
            }
            if ($request->filled('date_range')) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) == 2) {
                    try {
                        $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                        $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                        $query->where(function ($q) use ($startDate, $endDate) {
                            $q->whereBetween('from_date', [$startDate, $endDate])
                                ->orWhereBetween('to_date', [$startDate, $endDate]);
                        });
                    } catch (\Exception $e) {

                    }
                }
            }
            $summaryQuery = clone $query;
            $total = $summaryQuery->count();
            $approved = (clone $summaryQuery)->where('status', 'approved')->count();
            $pending = (clone $summaryQuery)->where('status', 'pending')->count();
            $declined = (clone $summaryQuery)->whereIn('status', ['declined', 'rejected'])->count();
            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('leave_type', fn($req) => $req->leaveType->leave_type ?? '-')
                ->addColumn('leave_type_id', fn($req) => $req->leave_type)
                ->addColumn('employee', function ($req) {
                    $user = $req->boxUser?->user;
                    $department = $req->boxUser?->departmentRelation;
                    $imagePath = $req->boxUser?->profile_image_path;
                    $designation = $req->boxUser?->designationRelation;
                    $employeeId = $req->boxUser?->employee_id;
                    return [
                        'name' => $user?->name ?? 'N/A',
                        'image' => $imagePath ? asset('storage/' . $imagePath) : null,
                        'department' => $department?->name ?? $req->boxUser?->department ?? 'N/A',
                        'employee_id' => $employeeId,
                        'designation' => $designation?->name ?? $req->boxUser?->designation ?? 'N/A',
                    ];
                })
                ->addColumn('from', fn($req) => Carbon::parse($req->from_date)->format('d M Y'))
                ->addColumn('to', fn($req) => Carbon::parse($req->to_date)->format('d M Y'))
                ->addColumn('no_of_days', fn($req) => $req->total_days . ' Days')
                ->addColumn('status', fn($req) => match (strtolower($req->status)) {
                    'approved' => 'Approved',
                    'declined', 'rejected' => 'Declined',
                    'pending' => 'Pending',
                    default => ucfirst($req->status),
                })
                ->addColumn('approved_by', fn($req) => $req->approvedByUser?->name ?? 'N/A')
                ->addColumn('attachment', fn($req) => $req->attachment ? asset('storage/' . $req->attachment) : null)
                ->addColumn('action', fn($req) => [
                    'edit_id' => $req->id,
                    'delete_id' => $req->id,
                ])
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value'] ?? null) {
                        $search = strtolower($search);
                        $query->where(function ($q) use ($search) {
                            $q->whereHas('approvedByUser', function ($q2) use ($search) {
                                $q2->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                            })
                                ->orWhereHas('boxUser.user', function ($q3) use ($search) {
                                    $q3->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                                })
                                ->orWhereHas('boxUser', function ($q4) use ($search) {
                                    $q4->whereRaw('LOWER(employee_id) LIKE ?', ["%{$search}%"])
                                        ->orWhereHas('designationRelation', fn($q5) => $q5->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]))
                                        ->orWhereRaw('LOWER(designation) LIKE ?', ["%{$search}%"])
                                        ->orWhereHas('departmentRelation', fn($q6) => $q6->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]))
                                        ->orWhereRaw('LOWER(department) LIKE ?', ["%{$search}%"]);
                                })
                                ->orWhereRaw('LOWER(contact_during_leave) LIKE ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(reason) LIKE ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(applied_for) LIKE ?', ["%{$search}%"]);
                        });
                    }
                })
                ->with([
                    'summary' => [
                        'total' => $total,
                        'approved' => $approved,
                        'pending' => $pending,
                        'declined' => $declined,
                    ],
                ])
                ->toJson();
        } catch (\Throwable $th) {
            report($th);
            return response()->json([
                'error' => 'An error occurred while fetching leave requests: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function update_attendance_correction(Request $request)
    {
        $request->validate([
            'correctionId' => 'required|exists:attendance_corrections,id',
            'reason' => 'required|string|max:1000',
            'status' => 'required|string|in:approved,rejected,pending',
        ]);
        try {
            $attendanceCorrection = AttendanceCorrection::findOrFail($request->input('correctionId'));
            $attendanceCorrection->reason = $request->input('reason', $attendanceCorrection->reason);
            $attendanceCorrection->status = $request->input('status');
            $attendanceCorrection->save();
            if ($request->status === 'approved' && $attendanceCorrection->wasChanged('status')) {
                $attendance = Attendance::find($attendanceCorrection->attendance_id);
                if (!$attendance) {
                    return response()->json([
                        'message' => 'Attendance record not found.',
                    ], 404);
                }

                // Handle break correction
                if ($attendanceCorrection->correction_type === 'break' && $attendanceCorrection->break_id) {
                    $break = AttendanceBreak::find($attendanceCorrection->break_id);
                    if ($break) {
                        // Update break_start if provided in correction
                        if ($attendanceCorrection->break_start) {
                            $break->break_start = Carbon::parse($attendanceCorrection->break_start)->format('Y-m-d H:i:s');
                        }

                        // Update break_end if provided in correction (even if it was NULL before)
                        if ($attendanceCorrection->break_end) {
                            $break->break_end = Carbon::parse($attendanceCorrection->break_end)->format('Y-m-d H:i:s');
                            $break->break_status = 0; // Mark as completed break

                            // Recalculate duration if both start and end exist (use existing break_start if not updated)
                            $breakStart = $break->break_start;
                            if ($breakStart && $break->break_end) {
                                $startDT = Carbon::parse($breakStart);
                                $endDT = Carbon::parse($break->break_end);
                                $duration = max(0, $startDT->diffInSeconds($endDT));
                                $break->duration = gmdate('H:i:s', $duration);
                            }
                        } else {
                            // If break_end is not provided in correction but break_start is updated, keep it as ongoing
                            if ($attendanceCorrection->break_start && !$break->break_end) {
                                $break->break_status = 1; // Keep as ongoing break
                                $break->duration = null; // No duration yet
                            }
                        }

                        $break->save();

                        // Recalculate total break duration for the attendance
                        $allBreaks = AttendanceBreak::where('attendance_id', $attendance->id)
                            ->whereNotNull('duration')
                            ->get();

                        $totalBreakSeconds = 0;
                        foreach ($allBreaks as $b) {
                            if ($b->duration) {
                                $totalBreakSeconds += self::timeToSeconds($b->duration);
                            }
                        }

                        // Update attendance break_duration
                        $attendance->break_duration = self::secondsToTime($totalBreakSeconds);
                        $attendance->save();

                        // Recalculate working hours and production hours immediately after break update
                        $attendance->refresh();
                        $attDate = Carbon::parse($attendance->attendance_date)->toDateString();
                        $checkIn = strtotime("$attDate {$attendance->check_in}");
                        $checkOut = strtotime("$attDate {$attendance->check_out}");
                        if ($checkOut < $checkIn) {
                            $checkOut = strtotime('+1 day', $checkOut);
                        }
                        $workingSeconds = max(0, $checkOut - $checkIn);

                        // Use the updated break_duration for production hours calculation
                        $breakDuration = $attendance->break_duration ?? '00:00:00';
                        $breakSeconds = self::timeToSeconds($breakDuration);
                        $netWorkingSeconds = max(0, $workingSeconds - $breakSeconds);

                        // Update working_hours and production_hours with recalculated values
                        $attendance->update([
                            'working_hours' => self::secondsToTime($workingSeconds),
                            'production_hours' => self::secondsToTime($netWorkingSeconds),
                        ]);
                    }
                } else {
                    // Handle attendance correction (existing logic)
                    $checkInTime = ($attendanceCorrection->punch_in_time && $attendanceCorrection->punch_in !== 'miss')
                        ? $attendanceCorrection->punch_in_time
                        : $attendance->check_in;
                    $checkOutTime = ($attendanceCorrection->punch_out_time && $attendanceCorrection->punch_out !== 'miss')
                        ? $attendanceCorrection->punch_out_time
                        : $attendance->check_out;

                    $attendanceDate = Carbon::parse($attendance->attendance_date);
                    $dayName = strtolower($attendanceDate->format('l'));
                    $isWeeklyOff = false;
                    $isHoliday = Holiday::with('holidayType')
                        ->where('box_id', $attendance->box_id)
                        ->whereDate('date', $attendance->attendance_date)
                        ->get()
                        ->filter(function ($h) use ($attendance) {
                            $calcInCalendar = 'No';
                            if ($h->holidayType) {
                                $calcInCalendar = $h->holidayType->calculate_in_calendar;
                            } elseif ($h->holiday_type_id) {
                                $hType = \App\Models\HolidayType::find($h->holiday_type_id);
                                if ($hType) {
                                    $calcInCalendar = $hType->calculate_in_calendar;
                                }
                            } elseif ($h->holiday_type) {
                                $hType = \App\Models\HolidayType::where('box_id', $attendance->box_id)
                                    ->where('holiday_type', $h->holiday_type)
                                    ->first();
                                if ($hType) {
                                    $calcInCalendar = $hType->calculate_in_calendar;
                                }
                            }
                            return strcasecmp($calcInCalendar, 'Yes') === 0;
                        })->isNotEmpty();

                    $boxUser = BoxUser::with('shift', 'box')->where('id', $attendance->box_user_id)->first();
                    if ($boxUser) {
                        $shift = $boxUser->shift;
                        if ($shift && $shift->shift_enabled) {
                            $weeklyOffDays = collect([
                                $shift->weekly_off_1,
                                $shift->weekly_off_2,
                                $shift->weekly_off_3,
                                $shift->weekly_off_4,
                            ])->filter()->map(fn($day) => strtolower($day))->unique();
                            if ($weeklyOffDays->contains($dayName)) {
                                $isWeeklyOff = true;
                            }
                        } elseif ($boxUser->working_days) {
                            $userWorkingDays = is_array($boxUser->working_days) ? $boxUser->working_days : (json_decode($boxUser->working_days, true) ?? explode(',', $boxUser->working_days));
                            $userWorkingDays = array_map('trim', array_map('strtolower', $userWorkingDays));
                            if (!in_array($dayName, $userWorkingDays)) {
                                $isWeeklyOff = true;
                            }
                        } elseif ($boxUser->box && $boxUser->box->working_days) {
                            $boxWorkingDays = is_array($boxUser->box->working_days) ? $boxUser->box->working_days : (json_decode($boxUser->box->working_days, true) ?? explode(',', $boxUser->box->working_days));
                            $boxWorkingDays = array_map('trim', array_map('strtolower', $boxWorkingDays));
                            if (!in_array($dayName, $boxWorkingDays)) {
                                $isWeeklyOff = true;
                            }
                        }
                    }

                    $oldStatus = strtolower(trim((string) $attendance->status));
                    $targetStatus = 'present';
                    if ($isWeeklyOff || $isHoliday || $oldStatus === 'weekly off' || $oldStatus === 'holiday') {
                        $targetStatus = 'Comp Off';
                    }


                    $attendance->update([
                        'check_in' => $checkInTime,
                        'check_out' => $checkOutTime,
                        'status' => $targetStatus,
                    ]);

                    if ($targetStatus === 'Comp Off') {
                        $compOffLeaveType = LeaveType::where('box_id', $attendance->box_id)
                            ->where('status', 'Active')
                            ->where(function ($query) {
                                $query->where('leave_type', 'like', '%Compensatory Off%')
                                    ->orWhere('leave_type', 'like', '%Comp Off%')
                                    ->orWhere('leave_type', 'like', '%comp off%');
                            })->first();
                        if ($compOffLeaveType) {
                            $alreadyCredited = LeaveTransaction::where('box_user_id', $attendance->box_user_id)
                                ->where('leave_type_id', $compOffLeaveType->id)
                                ->whereDate('date', $attendance->attendance_date)
                                ->exists();
                            if (!$alreadyCredited) {
                                $latestTransaction = LeaveTransaction::where('box_user_id', $attendance->box_user_id)
                                    ->where('leave_type_id', $compOffLeaveType->id)
                                    ->orderByDesc('id')
                                    ->first();

                                $openingBalance = (float) ($latestTransaction->balance_leave ?? 0);
                                $newBalance = round($openingBalance + 1, 2);
                                $totalLeave = round((float) ($latestTransaction->total_leave ?? 0) + 1, 2);

                                LeaveTransaction::create([
                                    'box_id' => $attendance->box_id,
                                    'box_user_id' => $attendance->box_user_id,
                                    'leave_type_id' => $compOffLeaveType->id,
                                    'date' => $attendance->attendance_date,
                                    'credit_leave' => 1,
                                    'debit_leave' => 0,
                                    'opening_balance' => $openingBalance,
                                    'balance_leave' => $newBalance,
                                    'total_leave' => $totalLeave,
                                    'status' => 'approved',
                                    'transaction_remarks' => "Comp Off earned for " . $attendanceDate->format('l, Y-m-d') . " (" . ($isHoliday ? 'Holiday' : ucfirst($dayName)) . ") via Correction Approval",
                                    'applied_date' => $attendanceCorrection->created_at,
                                    'approved_date' => now(),
                                ]);
                            }
                        }
                    }

                    $attendance->refresh();
                    $attDate = Carbon::parse($attendance->attendance_date)->toDateString();
                    $checkIn = strtotime("$attDate {$attendance->check_in}");
                    $checkOut = strtotime("$attDate {$attendance->check_out}");
                    if ($checkOut < $checkIn) {
                        $checkOut = strtotime('+1 day', $checkOut);
                    }
                    $workingSeconds = max(0, $checkOut - $checkIn);

                    // Get break duration (handle null/empty case)
                    $breakDuration = $attendance->break_duration ?? '00:00:00';
                    $breakSeconds = self::timeToSeconds($breakDuration);
                    $netWorkingSeconds = max(0, $workingSeconds - $breakSeconds);

                    $attendance->update([
                        'working_hours' => self::secondsToTime($workingSeconds),
                        'production_hours' => self::secondsToTime($netWorkingSeconds),
                    ]);
                }
            }
            return response()->json([
                'message' => 'Attendance correction updated successfully',
                'data' => $attendanceCorrection,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to update attendance correction',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    protected static function timeToSeconds($time)
    {
        if (!$time || !preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
            return 0;
        }

        [$hours, $minutes, $seconds] = explode(':', $time);

        return ((int) $hours * 3600) + ((int) $minutes * 60) + (int) $seconds;
    }

    protected static function secondsToTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }


    public function SavedAttendanceCoreection(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'attendace_id' => 'required|exists:attendances,id',
                'correction_type' => 'nullable|in:attendance,break',
                'break_id' => 'nullable|exists:attendance_break,id',
                'break_start' => 'nullable|date',
                'break_end' => 'nullable|date',
                'punch_in' => 'nullable|boolean',
                'punch_in_time' => 'nullable|string',
                'punch_out' => 'nullable|boolean',
                'punch_out_time' => 'nullable|string',
                'reason' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $userId = Auth::id();

            $boxUser = BoxUser::with('shift', 'box')
                ->where('user_id', $userId)
                ->select('id', 'user_id', 'box_id', 'day_start_time', 'day_end_time')
                ->first();

            if (!$boxUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.'
                ], 404);
            }

            $attendance = Attendance::find($request->attendace_id);
            if (!$attendance) {
                return response()->json([
                    'status' => false,
                    'message' => 'Attendance not found.'
                ], 404);
            }

            // RESOLVE SHIFT TIME
            // -----------------------------------------
            $relaxationTime = 0;
            if ($boxUser->shift) {
                $shiftIn = $boxUser->shift->in_time;
                $shiftOut = $boxUser->shift->out_time;
                $relaxationTime = (int) ($boxUser->shift->relaxation_time ?? 0);
            } elseif ($boxUser->day_start_time) {
                $shiftIn = $boxUser->day_start_time;
                $shiftOut = $boxUser->day_end_time;
            } else {
                $shiftIn = $boxUser->box->day_start_time ?? null;
                $shiftOut = $boxUser->box->day_end_time ?? null;
            }

            if ($shiftIn) $shiftIn = Carbon::parse($shiftIn)->setTimezone(config('app.timezone'))->format('H:i:s');
            if ($shiftOut) $shiftOut = Carbon::parse($shiftOut)->setTimezone(config('app.timezone'))->format('H:i:s');

            // -----------------------------------------
            // UNIVERSAL TIME PARSER (12h + 24h fixed)
            // -----------------------------------------
            $parseTime = function ($timeString) {

                if (!$timeString)
                    return null;

                $timeString = trim($timeString);

                try {
                    // CASE A: Contains AM/PM
                    if (preg_match('/\bAM\b|\bPM\b/i', $timeString)) {

                        // Extract numeric hour + minute
                        [$hour, $minute] = explode(':', preg_replace('/\s*(AM|PM)/i', '', $timeString));

                        // Fix invalid hour: "18:00 PM" → convert 18 → 6 PM
                        if ((int) $hour > 12) {
                            $hour = (int) $hour - 12;
                        }

                        // Rebuild to valid format
                        $normalized = sprintf(
                            "%02d:%s %s",
                            $hour,
                            $minute,
                            stripos($timeString, 'PM') !== false ? 'PM' : 'AM'
                        );

                        return Carbon::createFromFormat('h:i A', $normalized)->format('H:i:s');
                    }

                    // CASE B: 24-hour without seconds
                    if (preg_match('/^\d{2}:\d{2}$/', $timeString)) {
                        return Carbon::createFromFormat('H:i', $timeString)->format('H:i:s');
                    }

                    // CASE C: 24-hour with seconds
                    if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $timeString)) {
                        return Carbon::createFromFormat('H:i:s', $timeString)->format('H:i:s');
                    }

                    // CASE D: Try Carbon auto-parse
                    return Carbon::parse($timeString)->format('H:i:s');

                } catch (\Exception $e) {
                    throw new \Exception("Invalid time format: {$timeString}");
                }
            };

            // PARSE INCOMING TIMES
            $punchInTime = $parseTime($request->punch_in_time);
            $punchOutTime = $parseTime($request->punch_out_time);

            // -----------------------------------------
            // VALIDATIONS FOR TODAY
            // -----------------------------------------
            $attendanceDate = Carbon::parse($attendance->attendance_date);
            $nowTime = Carbon::now()->format('H:i:s');

            if ($attendanceDate->isToday()) {
                // Calculate shift end with grace time
                $shiftOutWithGrace = null;
                if ($shiftOut) {
                    $shiftOutWithGrace = Carbon::parse($shiftOut)->addMinutes($relaxationTime)->format('H:i:s');
                }

                // Global check: Prevent any correction request before shift end + grace time for today's attendance
                if ($shiftOutWithGrace && $nowTime < $shiftOutWithGrace) {
                    return response()->json([
                        'status' => false,
                        'message' => "Attendance correction for today can only be requested after shift end + grace period ($shiftOutWithGrace).",
                    ], 422);
                }

                if ($punchInTime && $punchInTime > $nowTime) {
                    return response()->json([
                        'status' => false,
                        'message' => "Punch-in time cannot be in the future.",
                    ], 422);
                }

                if ($punchOutTime && $punchOutTime > $nowTime) {
                    return response()->json([
                        'status' => false,
                        'message' => "Punch-out time cannot be in the future.",
                    ], 422);
                }

                if ($punchInTime && $punchOutTime && $punchOutTime < $punchInTime) {
                    return response()->json([
                        'status' => false,
                        'message' => "Punch-out time cannot be earlier than punch-in time.",
                    ], 422);
                }

                // Specific check for punch_out time vs shift end (to be extra sure, though global check covers it)
                if ($punchOutTime && $shiftOut && $punchOutTime < $shiftOut) {
                    return response()->json([
                        'status' => false,
                        'message' => "Punch-out time ($punchOutTime) cannot be before shift end ($shiftOut).",
                    ], 422);
                }
            }

            // -----------------------------------------
            // UPSERT CORRECTION
            // -----------------------------------------
            $correctionType = $request->correction_type ?? 'attendance';

            // Validate break correction - only allow for ongoing breaks
            if ($correctionType === 'break' && $request->break_id) {
                $break = AttendanceBreak::find($request->break_id);
                if (!$break) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Break not found.',
                    ], 404);
                }

                // Check if break is completed (break_end is not NULL and break_status is 0)
                if ($break->break_end !== null && $break->break_status == 0) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Break correction cannot be requested for completed breaks. Only ongoing breaks can be corrected.',
                    ], 422);
                }
            }

            // Build attributes based on correction type
            if ($correctionType === 'break') {
                $attributes = [
                    'attendance_id' => $attendance->id,
                    'box_user_id' => $boxUser->id,
                    'correction_type' => 'break',
                    'break_id' => $request->break_id,
                ];
            } else {
                $attributes = [
                    'attendance_id' => $attendance->id,
                    'box_user_id' => $boxUser->id,
                    'correction_type' => 'attendance',
                ];
            }

            // Build values based on correction type
            if ($correctionType === 'break') {
                $values = [
                    'box_id' => $boxUser->box_id,
                    'break_start' => $request->break_start ? Carbon::parse($request->break_start)->format('Y-m-d H:i:s') : null,
                    'break_end' => $request->break_end ? Carbon::parse($request->break_end)->format('Y-m-d H:i:s') : null,
                    'reason' => $request->reason,
                    'updated_by' => $userId,
                    'status' => 'pending'
                ];
            } else {
                $values = [
                    'box_id' => $boxUser->box_id,
                    'punch_in' => (bool) $request->punch_in,
                    'punch_in_time' => $punchInTime,
                    'punch_out' => (bool) $request->punch_out,
                    'punch_out_time' => $punchOutTime,
                    'reason' => $request->reason,
                    'updated_by' => $userId,
                    'status' => 'pending'
                ];
            }

            $correction = AttendanceCorrection::where($attributes)->first();

            if ($correction) {
                $correction->update($values);
                $message = 'Attendance correction updated successfully.';
            } else {
                $correction = AttendanceCorrection::create(array_merge($attributes, $values, [
                    'created_by' => $userId
                ]));
                $message = 'Attendance correction submitted successfully.';
            }

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => $correction
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $th->getMessage() . "\nTrace:\n" . $th->getTraceAsString(),
            ], 500);
        }
    }


    // public function SavedAttendanceCoreection(Request $request)
// {
//     try {

    //         $validator = Validator::make($request->all(), [
//             'attendace_id'   => 'required|exists:attendances,id',
//             'punch_in'       => 'nullable|boolean',
//             'punch_in_time'  => 'nullable|string',
//             'punch_out'      => 'nullable|boolean',
//             'punch_out_time' => 'nullable|string',
//             'reason'         => 'nullable|string|max:255',
//         ]);

    //         if ($validator->fails()) {
//             return response()->json([
//                 'status'  => false,
//                 'message' => 'Validation errors',
//                 'errors'  => $validator->errors(),
//             ], 422);
//         }

    //         $userId = Auth::id();

    //         $boxUser = BoxUser::with('shift','box')
//             ->where('user_id', $userId)
//             ->select('id','user_id','box_id','day_start_time','day_end_time')
//             ->first();

    //         if (! $boxUser) {
//             return response()->json([
//                 'status'  => false,
//                 'message' => 'User not found.'
//             ], 404);
//         }
//         $attendance = Attendance::find($request->attendace_id);
//         if (! $attendance) {
//             return response()->json([
//                 'status'  => false,
//                 'message' => 'Attendance not found.'
//             ], 404);
//         }

    //         if ($boxUser->shift) {
//             $shiftIn  = $boxUser->shift->in_time;
//             $shiftOut = $boxUser->shift->out_time;

    //         } elseif (!empty($boxUser->day_start_time)) {
//             $shiftIn  = $boxUser->day_start_time;
//             $shiftOut = $boxUser->day_end_time;

    //         } else {
//             $shiftIn  = $boxUser->box->day_start_time ?? null;
//             $shiftOut = $boxUser->box->day_end_time ?? null;
//         }

    //         $punchInTime = $request->punch_in_time
//             ? Carbon::createFromFormat('h:i A', $request->punch_in_time)->format('H:i:s')
//             : null;

    //         $punchOutTime = $request->punch_out_time
//             ? Carbon::createFromFormat('h:i A', $request->punch_out_time)->format('H:i:s')
//             : null;

    //         $attendanceDate = Carbon::parse($attendance->attendance_date);
//         $nowTime        = Carbon::now()->format('H:i:s');

    //         if ($attendanceDate->isToday()) {

    //             // 1️⃣ RULE — Punch IN cannot be in the future
//             if ($punchInTime && $punchInTime > $nowTime) {
//                 return response()->json([
//                     'status' => false,
//                     'message' => "You cannot set a punch-in time later than the current time.",
//                 ], 422);
//             }

    //             // 2️⃣ RULE — Punch OUT cannot be in the future
//             if ($punchOutTime && $punchOutTime > $nowTime) {
//                 return response()->json([
//                     'status' => false,
//                     'message' => "You cannot set a punch-out time later than the current time.",
//                 ], 422);
//             }

    //             // 3️⃣ RULE — Punch OUT must be after Punch IN
//             if ($punchInTime && $punchOutTime && $punchOutTime < $punchInTime) {
//                 return response()->json([
//                     'status'  => false,
//                     'message' => 'Punch-out time cannot be earlier than punch-in time.'
//                 ], 422);
//             }

    //             // 4️⃣ RULE — Cannot punch-out before shift end if shift not completed yet
//             if ($punchOutTime && $shiftOut && $nowTime < $shiftOut) {
//                 return response()->json([
//                     'status' => false,
//                     'message' => "You cannot request punch-out before your shift ends at ($shiftOut).",
//                 ], 422);
//             }
//         }


    //         // ---------------------------------------------------
//         // SAVE / UPDATE CORRECTION
//         // ---------------------------------------------------
//         $attributes = [
//             'attendance_id' => $attendance->id,
//             'box_user_id'   => $boxUser->id,
//         ];

    //         $values = [
//             'box_id'         => $boxUser->box_id,
//             'punch_in'       => (bool) $request->punch_in,
//             'punch_in_time'  => $punchInTime,
//             'punch_out'      => (bool) $request->punch_out,
//             'punch_out_time' => $punchOutTime,
//             'reason'         => $request->reason,
//             'updated_by'     => $userId,
//             'status'         => 'pending'
//         ];

    //         $correction = AttendanceCorrection::where($attributes)->first();

    //         if ($correction) {
//             $correction->update($values);
//             $message = 'Attendance correction updated successfully.';
//         } else {
//             $correction = AttendanceCorrection::create(array_merge(
//                 $attributes,
//                 $values,
//                 ['created_by' => $userId]
//             ));
//             $message = 'Attendance correction created successfully.';
//         }

    //         return response()->json([
//             'status'  => true,
//             'message' => $message,
//             'data'    => $correction
//         ]);

    //     } catch (\Throwable $th) {

    //         return response()->json([
//             'status'  => false,
//             'message' => 'Something went wrong: ' . $th->getMessage(),
//         ], 500);
//     }
// }

    public function GetCalendarData(Request $request)
    {
        try {
            $user = Auth::id();
            $boxUser = BoxUser::with('shift', 'box')->where('user_id', $user)->first();
            if (!$boxUser) {
                return response()->json(['error' => 'Box user not found'], 404);
            }
            $joiningDate = Carbon::parse($boxUser->joining_date);
            $today = Carbon::today();
            $start = $this->parseDateOrDefault($request->query('start'), Carbon::now()->startOfMonth());
            $end = $this->parseDateOrDefault($request->query('end'), Carbon::now()->endOfMonth());
            $holidays = $this->getHolidays($boxUser->box_id, $start, $end);
            [$weeklyOff1, $weeklyOff2, $weeklyOff3, $weeklyOff4, $offWeeks, $halfWeeks] = $this->extractShiftData($boxUser);
            $workingDays = $this->getWorkingDays($boxUser, $weeklyOff1, $weeklyOff2);
            $attendances = $this->getAttendances($boxUser->id, $start, $end);
            $dates = $this->generateDateRange($start, $end);
            $mapped = $dates->map(fn($date) => $this->mapDateToAttendance(
                $date,
                $attendances,
                $weeklyOff1,
                $weeklyOff2,
                $weeklyOff3,
                $weeklyOff4,
                $offWeeks,
                $halfWeeks,
                $workingDays,
                $boxUser,
                $joiningDate,
                $today,
                $holidays
            ));
            return response()->json(['data' => $mapped]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    // Helpers:
    private function parseDateOrDefault($dateString, Carbon $default)
    {
        return $dateString ? Carbon::parse($dateString) : $default;
    }
    private function getHolidays($boxId, Carbon $start, Carbon $end)
    {
        return Holiday::with('holidayType')
            ->where('box_id', $boxId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->filter(function ($holiday) use ($boxId) {
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
                return strcasecmp($calcInCalendar, 'Yes') === 0;
            })
            ->keyBy(fn($h) => Carbon::parse($h->date)->toDateString());
    }
    private function extractShiftData(BoxUser $boxUser): array
    {
        $shift = $boxUser->shift;
        if (!$shift) {
            return [null, null, null, null, [], []];
        }
        return [
            ucfirst(strtolower($shift->weekly_off_1 ?? '')),
            ucfirst(strtolower($shift->weekly_off_2 ?? '')),
            ucfirst(strtolower($shift->weekly_off_3 ?? '')),
            ucfirst(strtolower($shift->weekly_off_4 ?? '')),
            $this->normalizeWeekArray($shift->saturday_offs ?? []),
            $this->normalizeWeekArray($shift->saturday_half_days ?? [])
        ];
    }
    private function getWorkingDays(BoxUser $boxUser, $weeklyOff1, $weeklyOff2): array
    {
        if (!empty($weeklyOff1) || !empty($weeklyOff2)) {
            return [];
        }
        $workingDays = $boxUser->working_days;
        if (is_string($workingDays)) {
            $workingDays = json_decode($workingDays, true);
        }
        if (empty($workingDays) && $boxUser->box) {
            $workingDays = $boxUser->box->working_days;
            if (is_string($workingDays)) {
                $workingDays = json_decode($workingDays, true);
            }
        }
        if (!is_array($workingDays)) {
            $workingDays = [];
        }
        return array_map(fn($d) => ucfirst(strtolower((string)$d)), $workingDays);
    }

    private function getAttendances($boxUserId, Carbon $start, Carbon $end)
    {
        return Attendance::where('box_user_id', $boxUserId)
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->attendance_date)->toDateString());
    }

    private function generateDateRange(Carbon $start, Carbon $end)
    {
        $dates = collect();
        $cur = $start->copy();
        while ($cur->lte($end)) {
            $dates->push($cur->copy());
            $cur->addDay();
        }
        return $dates;
    }

    private function mapDateToAttendance(
        Carbon $date,
        $attendances,
        $weeklyOff1,
        $weeklyOff2,
        $weeklyOff3,
        $weeklyOff4,
        $offWeeks,
        $halfWeeks,
        $workingDays,
        BoxUser $boxUser,
        Carbon $joiningDate,
        Carbon $today,
        $holidays
    ) {
        $dateStr = $date->toDateString();
        $dayName = $date->format('l');

        $defaultData = (object) [
            'attendance_date' => $dateStr,
            'id' => null,
            'check_in' => null,
            'check_out' => null,
            'break_duration' => null,
            'late_duration' => null,
            'production_hours' => null,
            'working_hours' => null,
            'over_time' => null,
            'holiday_code' => null,
            'holiday_title' => null,
        ];

        $attendance = $attendances->get($dateStr);
        if ($attendance) {
            $status = strtolower($attendance->status ?? '');
            $isWeekOff = $this->checkWeeklyOff($dayName, $weeklyOff1, $weeklyOff2, $workingDays)[0];
            // Preserve recorded Comp Off even on weekly off days
            if ($isWeekOff && $status === 'comp off') {
                return $this->formatAttendanceData($attendance, 'Comp Off');
            }

            if ($status === 'punch out miss') {
                return $this->formatAttendanceData($attendance, 'Punch Out Miss');
            }

            if (in_array($status, ['present', 'paid leave', 'short leave', 'half day', 'absent'])) {
                return $this->formatAttendanceData($attendance, ucfirst($status));
            }

            $calculated = $this->calculateStatus($attendance, $boxUser);
            return $this->formatAttendanceData($attendance, $calculated);
        }

        // Holiday check
        if ($holidays->has($dateStr)) {
            $holiday = $holidays->get($dateStr);
            $isMandatory = optional($holiday->holidayType)->is_mandatory === 'Yes';
                return $this->formatAttendanceData(
                    (object) array_merge((array) $defaultData, [
                        'holiday_code' => $holiday->title ?? 'Holiday',
                        'holiday_title' => $holiday->title ?? 'Holiday',
                        'holiday_type_id' => $holiday->holiday_type_id,
                        'is_mandatory' => $isMandatory ? 'Yes' : 'No',
                        'can_edit' => !$isMandatory,
                    ]),
                    $holiday->title ?? 'Holiday'
                );
        }

        // Weekly Off check
        [$isWeeklyOff, $weeklyOffReason] = $this->checkWeeklyOff($dayName, $weeklyOff1, $weeklyOff2, $workingDays);

        $weekOfMonth = ceil($date->day / 7);
        $weekLabels = ['1st', '2nd', '3rd', '4th', '5th'];
        $weekLabel = $weekLabels[$weekOfMonth - 1] ?? '';

        $isHalfDay = ($dayName === $weeklyOff3 && in_array($weekLabel, $offWeeks));
        $isHalfdays = ($dayName === $weeklyOff4 && in_array($weekLabel, $halfWeeks));

        if ($isWeeklyOff || $isHalfDay) {
            $label = $isHalfDay ? "Weekly Off ({$weekLabel})" : "Weekly Off";
            return $this->formatAttendanceData(
                (object) array_merge((array) $defaultData, [
                    'can_edit' => false,
                ]),
                $label
            );
        }

        if ($isHalfdays) {
            $findOutHalfDayType = $boxUser->shift->half_day_time ?? 'Half Day';
            return $this->formatAttendanceData($defaultData, "Weekly off - ({$findOutHalfDayType})");
        }
        if ($date->gt($today)) {
            return $this->formatAttendanceData($defaultData, 'N/A');
        }

        if ($date->lt($joiningDate)) {
            return $this->formatAttendanceData($defaultData, 'Not Joined');
        }

        return $this->formatAttendanceData($defaultData, 'Absent');
    }

    private function normalizeWeekArray($value)
    {
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            return array_map('trim', explode(',', $value));
        }
        return [];
    }

    private function checkWeeklyOff(string $dayName, ?string $weeklyOff1, ?string $weeklyOff2, array $workingDays): array
    {
        if ($weeklyOff1 || $weeklyOff2) {
            if ($dayName === $weeklyOff1 || $dayName === $weeklyOff2) {
                return [true, "Matched shift weekly off: $dayName"];
            }
        } elseif (!empty($workingDays)) {
            if (!in_array($dayName, $workingDays)) {
                return [true, "$dayName is not in workingDays list"];
            }
        }
        return [false, null];
    }

    private function formatAttendanceData($attendance, $status)
    {
        return [
            'title' => $status,
            'start' => $attendance->attendance_date,
            'className' => $this->getStatusClass($status),
            'attendance_id' => $attendance->id,
            'check_in' => $attendance->check_in,
            'check_out' => $attendance->check_out,
            'break_duration' => $attendance->break_duration,
            'late_duration' => $attendance->late_duration,
            'production_hours' => $attendance->production_hours,
            'working_hours' => $attendance->working_hours,
            'over_time' => $attendance->over_time,
            'holiday_code' => $attendance->holiday_code ?? null,
            'holiday_title' => $attendance->holiday_title ?? null,
            'holiday_type_id' => $attendance->holiday_type_id ?? null,
            'is_mandatory' => $attendance->is_mandatory ?? 'No',
            'can_edit' => $attendance->can_edit ?? true,
        ];
    }

    private function getStatusClass($status)
    {
        $cleanStatus = strtolower(trim(preg_replace('/\s*\(.*?\)/', '', $status)));
        switch ($cleanStatus) {
            case 'present':
                return 'new-bg-green';
            case 'absent':
                return 'bg-danger';
            case 'paid leave':
                return 'bg-success';
            case 'short leave':
                return 'new-bg-orange';
            case 'half day':
                return 'new-bg-yellow';
            case 'punch out miss':
                return 'bg-warning text-white';
            case 'weekly off -':
                return 'new-bg-light-yellow';
            case 'holiday':
                return 'new-bg-blue';
            case 'rh holiday':
                return 'new-bg-skyblue';
            case 'weekly off':
                return 'new-bg-purple';
            case 'comp off':
                return 'bg-warning text-white';
            case 'not joined':
                return 'bg-dark text-white';
            case 'n/a':
                return 'bg-light text-muted';
            case 'pending':
                return 'bg-pending';
            case 'approved':
                return 'bg-approved';
            default:
                return 'bg-info';
        }
    }

    
    
    public function UserTaskProgress(Request $request)
    {
        return view("frontend.admin.user_tasks_progress");
    }

    public function UserTaskProgressData(Request $request)
    {
        try {
            $userId = \Illuminate\Support\Facades\Auth::id();
            $box = \App\Models\Box::where("superadmin_id", $userId)->firstOrFail();
            
            $startDateStr = $request->get("start_date");
            $endDateStr = $request->get("end_date");

            // Fetch users for the box
            $boxUsers = \App\Models\BoxUser::where("box_id", $box->id)
                ->with("user:id,name,email")
                ->get();

            // Caching for ultra-fast ms load times
            $cacheKey = "user_task_progress_data_{$box->id}_" . md5($startDateStr . $endDateStr);
            $userData = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(15), function () use ($box, $startDateStr, $endDateStr, $boxUsers) {
                $userIds = $boxUsers->pluck('user_id')->toArray();

                // Optimized to avoid filesort, hitting box_id index primarily
                $activityQuery = \Illuminate\Support\Facades\DB::table('activities')
                    ->where('box_id', $box->id)
                    ->whereIn('type', ['task', 'call', 'email', 'meeting', 'whatsapp', 'note']);

                if (!empty($startDateStr) && !empty($endDateStr)) {
                    $activityQuery->whereBetween('activity_date', [$startDateStr, $endDateStr]);
                }

                $rawStats = $activityQuery
                    ->select('assigned_to', 'status', \Illuminate\Support\Facades\DB::raw('COUNT(id) as cnt'))
                    ->groupBy('assigned_to', 'status')
                    ->get();

                // Process stats in PHP for blazing fast aggregation
                $statsByUser = [];
                $closedStatuses = ['Close', 'Completed', 'Closed'];
                foreach ($rawStats as $row) {
                    $uid = $row->assigned_to;
                    $status = $row->status;
                    $count = (int) $row->cnt;

                    if (!isset($statsByUser[$uid])) {
                        $statsByUser[$uid] = ['open' => 0, 'closed' => 0];
                    }

                    if (in_array($status, $closedStatuses)) {
                        $statsByUser[$uid]['closed'] += $count;
                    } else {
                        $statsByUser[$uid]['open'] += $count;
                    }
                }

                $data = [];
                foreach ($boxUsers as $bUser) {
                    if (!$bUser->user) continue;
                    $uId = $bUser->user_id;

                    $userStat = $statsByUser[$uId] ?? ['open' => 0, 'closed' => 0];
                    
                    $open = $userStat['open'];
                    $closed = $userStat['closed'];
                    $total = $open + $closed;
                    
                    $openPercent = $total > 0 ? round(($open / $total) * 100) : 0;
                    $closedPercent = $total > 0 ? round(($closed / $total) * 100) : 0;

                    $profileImage = $bUser->profile_image_path;
                    if ($profileImage && !filter_var($profileImage, FILTER_VALIDATE_URL)) {
                        $profileImage = asset("storage/" . $profileImage);
                    } else if (!$profileImage) {
                        $userNameUrl = urlencode($bUser->user->name ?? 'User');
                        $profileImage = "https://ui-avatars.com/api/?name={$userNameUrl}&background=random&color=fff&size=128"; // Dynamic first letter avatar
                    }

                    $data[] = [
                        "user_id" => $uId,
                        "name" => $bUser->user->name,
                        "email" => $bUser->user->email,
                        "profile_image" => $profileImage,
                        "total" => $total,
                        "open" => $open,
                        "closed" => $closed,
                        "open_percent" => $openPercent,
                        "closed_percent" => $closedPercent
                    ];
                }

                // Sort by Closed Tasks descending to find the Top Performer!
                usort($data, function($a, $b) {
                    if ($b["closed"] == $a["closed"]) {
                        return $b["total"] <=> $a["total"];
                    }
                    return $b["closed"] <=> $a["closed"];
                });

                return $data;
            });

            return response()->json([
                "success" => true,
                "data" => $userData
            ]);
        } catch (\Throwable $th) {
            return response()->json(["success" => false, "message" => $th->getMessage()], 500);
        }
    }

    public function AdminTotalTasksStats(Request $request)
    {
        try {
            $userId = Auth::id();
            $box = \App\Models\Box::where('superadmin_id', $userId)->firstOrFail();
            
            $startDateStr = $request->get('start_date');
            $endDateStr = $request->get('end_date');
            $userIdFilter = $request->get('user_id');

            $query = \App\Models\Activity::where('activities.box_id', $box->id)
                ->whereIn('activities.type', ['task', 'call', 'email', 'meeting', 'whatsapp', 'note']);
                
            if (!empty($userIdFilter)) {
                $query->where('activities.assigned_to', $userIdFilter);
            }
            if (!empty($startDateStr) && !empty($endDateStr)) {
                $query->whereBetween('activities.activity_date', [$startDateStr, $endDateStr]);
            }

            $stats = $query->selectRaw("
                COUNT(CASE WHEN activities.status NOT IN ('Close', 'Completed', 'Closed') OR activities.status IS NULL THEN 1 END) as open_count,
                COUNT(CASE WHEN activities.status IN ('Close', 'Completed', 'Closed') THEN 1 END) as closed_count,
                COUNT(CASE WHEN activities.status NOT IN ('Close', 'Completed', 'Closed') OR activities.status IS NULL THEN 1 END) +
                COUNT(CASE WHEN activities.status IN ('Close', 'Completed', 'Closed') THEN 1 END) as total
            ")->first();

            // Fetch users for dropdown instantly
            $boxUsers = \App\Models\BoxUser::where('box_id', $box->id)
                ->with('user:id,name')
                ->get()
                ->map(function ($boxUser) {
                    return [
                        'user_id' => $boxUser->user_id,
                        'name' => $boxUser->user->name ?? 'N/A'
                    ];
                });

            return response()->json([
                'success' => true,
                'users' => $boxUsers,
                'data' => [
                    'total' => (int) $stats->total,
                    'open_count' => (int) $stats->open_count,
                    'closed_count' => (int) $stats->closed_count,
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function EmployeeTotalTasksStats(Request $request)
    {
        try {
            $userId = Auth::id();
            $boxUser = \App\Models\BoxUser::where('user_id', $userId)->firstOrFail();
            
            $startDateStr = $request->get('start_date');
            $endDateStr = $request->get('end_date');

            $query = \App\Models\Activity::where('activities.box_id', $boxUser->box_id)
                ->whereIn('activities.type', ['task', 'call', 'email', 'meeting', 'whatsapp', 'note'])
                ->where('activities.assigned_to', $userId);
                
            if (!empty($startDateStr) && !empty($endDateStr)) {
                $query->whereBetween('activities.activity_date', [$startDateStr, $endDateStr]);
            }

            $stats = $query->selectRaw("
                COUNT(CASE WHEN activities.status NOT IN ('Close', 'Completed', 'Closed') OR activities.status IS NULL THEN 1 END) as open_count,
                COUNT(CASE WHEN activities.status IN ('Close', 'Completed', 'Closed') THEN 1 END) as closed_count,
                COUNT(CASE WHEN activities.status NOT IN ('Close', 'Completed', 'Closed') OR activities.status IS NULL THEN 1 END) +
                COUNT(CASE WHEN activities.status IN ('Close', 'Completed', 'Closed') THEN 1 END) as total
            ")->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => (int) $stats->total,
                    'open_count' => (int) $stats->open_count,
                    'closed_count' => (int) $stats->closed_count,
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function AdminAttendaceList(Request $request)
    {
        try {
            $start = microtime(true);
            $userId = Auth::id();
            $box = Box::where('superadmin_id', $userId)->firstOrFail();
            
            $startDateStr = $request->get('start_date');
            $endDateStr = $request->get('end_date');
            $startDate = $startDateStr ? Carbon::parse($startDateStr)->startOfDay() : Carbon::today();
            $endDate = $endDateStr ? Carbon::parse($endDateStr)->endOfDay() : Carbon::today()->endOfDay();
            
            $daysDiff = $startDate->diffInDays($endDate) + 1;
            $prevStartDate = $startDate->copy()->subDays($daysDiff)->startOfDay();
            $prevEndDate = $endDate->copy()->subDays($daysDiff)->endOfDay();

            $tomorrow = $startDate->copy()->addDay();

            \Log::info('Time Setup: ' . (microtime(true) - $start)); $start = microtime(true);

            // Core Dashboard Data
            $boxUsers = $this->getRecentBoxUsers($box->id);
            \Log::info('Time getRecentBoxUsers: ' . (microtime(true) - $start)); $start = microtime(true);

            $upcomingBirthdays = $this->getUpcomingBirthdays($box->id, $startDate, $tomorrow);
            \Log::info('Time getUpcomingBirthdays: ' . (microtime(true) - $start)); $start = microtime(true);

            $totalEmployees = $box->users()->count();
            \Log::info('Time totalEmployees: ' . (microtime(true) - $start)); $start = microtime(true);

            [$formattedAttendances, $lateEmployees] = $this->getTodayAttendances($box->id, $startDate, $endDate);
            \Log::info('Time getTodayAttendances: ' . (microtime(true) - $start)); $start = microtime(true);

            $attendanceSummary = $this->getAttendanceSummary($box->id, $totalEmployees, $startDate, $endDate, $prevStartDate, $prevEndDate);
            \Log::info('Time getAttendanceSummary: ' . (microtime(true) - $start)); $start = microtime(true);

            $attendanceBreakdown = $this->getAttendanceBreakdown($box->id, $totalEmployees, $startDate, $endDate);
            \Log::info('Time getAttendanceBreakdown: ' . (microtime(true) - $start)); $start = microtime(true);

            $latestLateEmployee = collect($lateEmployees)->sortByDesc('updated_at')->first();
            if ($latestLateEmployee) {
                unset($latestLateEmployee['updated_at']);
            }

            // 1. Grouped Pending Counts
            $pendingRequests = [
                'attendance_corrections' => AttendanceCorrection::where('box_id', $box->id)->whereBetween('created_at', [$startDate, $endDate])->where('status', 'pending')->count(),
                'leave_requests' => LeaveRequest::where('box_id', $box->id)->whereBetween('created_at', [$startDate, $endDate])->where('status', 'pending')->count(),
            ];

            // 2. Optimized Department Counts (Single Query instead of Loop)
            $allDepartments = \App\Models\Department::where('box_id', $box->id)->get();
            $deptCounts = BoxUser::where('box_id', $box->id)
                ->selectRaw('department, count(*) as count')
                ->groupBy('department')
                ->pluck('count', 'department');

            $departmentCounts = [];
            foreach ($allDepartments as $dept) {
                $departmentCounts[$dept->name] = $deptCounts->get($dept->id, 0);
            }
            if ($deptCounts->has(null)) {
                $departmentCounts['Unknown'] = $deptCounts->get(null);
            }

            // 3. Optimized Recent Activities (Eager Loading to prevent N+1)
            $recentActivities = Activity::where('box_id', $box->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->with(['addedByUser.box_user_new'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($activity) {
                    $user = $activity->addedByUser;
                    $boxUser = $user?->box_user_new;

                    $profileImage = $boxUser && $boxUser->profile_image_path
                        ? asset('storage/' . $boxUser->profile_image_path)
                        : "https://ui-avatars.com/api/?name=" . urlencode($user->name ?? 'U') . "&background=random";

                    return [
                        'id' => $activity->id,
                        'user_name' => $user->name ?? 'Unknown',
                        'user_image' => $profileImage,
                        'subject' => $activity->subject,
                        'description' => $activity->description,
                        'time' => \Carbon\Carbon::parse($activity->created_at)->format('h:i A'),
                        'date' => \Carbon\Carbon::parse($activity->created_at)->isToday() ? 'Today' : \Carbon\Carbon::parse($activity->created_at)->format('d M'),
                    ];
                });

            // 4. Optimized Employee Types Statistics (Single Query instead of Loop)
            $employeeTypes = \App\Models\EmployeeType::where('box_id', $box->id)->get();
            $typeCounts = BoxUser::where('box_id', $box->id)
                ->selectRaw('emp_type, count(*) as count')
                ->groupBy('emp_type')
                ->pluck('count', 'emp_type');

            $employeeTypesStats = [];
            foreach ($employeeTypes as $type) {
                $count = $typeCounts->get($type->id, 0);
                $employeeTypesStats[] = [
                    'name' => $type->name,
                    'count' => $count,
                    'percentage' => $totalEmployees > 0 ? round(($count / $totalEmployees) * 100, 1) : 0
                ];
            }

            $lastTaskCalcTime = Box::find($box->id)->last_task_calculation_time ?? '00:30';
            if (substr_count($lastTaskCalcTime, ':') == 1) {
                $lastTaskCalcTime .= ':00';
            }

            \Log::info('Time Other Stats 1: ' . (microtime(true) - $start)); $start = microtime(true);

            $taskStatsRaw = Activity::where('activities.box_id', $box->id)
                ->whereIn('activities.type', ['task', 'call', 'email', 'meeting', 'whatsapp', 'note'])
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('activities.activity_date', [$startDate, $endDate])
                          ->orWhereBetween('activities.completed_date', [$startDate, $endDate]);
                })
                ->selectRaw("
                    COUNT(CASE WHEN activities.status = 'Open' AND activities.activity_date BETWEEN ? AND ? THEN 1 END) +
                    COUNT(CASE WHEN activities.status IN ('Close', 'Completed', 'Closed') AND activities.completed_date BETWEEN ? AND ? THEN 1 END) as total,
                    COUNT(CASE WHEN activities.status = 'Open' AND activities.activity_date BETWEEN ? AND ? THEN 1 END) as open_count,
                    COUNT(CASE WHEN activities.status IN ('Close', 'Completed', 'Closed') AND activities.closed_by IS NOT NULL AND activities.completed_date <= ADDTIME(activities.activity_date, TIME(?)) AND activities.completed_date BETWEEN ? AND ? THEN 1 END) as completed,
                    COUNT(CASE WHEN activities.status = 'On Hold' AND activities.activity_date BETWEEN ? AND ? THEN 1 END) as on_hold,
                    COUNT(CASE WHEN activities.status = 'Open' AND activities.activity_date < ? AND activities.activity_date BETWEEN ? AND ? THEN 1 END) as overdue,
                    COUNT(CASE WHEN activities.status = 'Open' AND activities.activity_date >= ? AND activities.activity_date BETWEEN ? AND ? THEN 1 END) as ongoing,
                    COUNT(CASE WHEN activities.status IN ('Close', 'Completed', 'Closed') AND activities.closed_by IS NOT NULL AND activities.completed_date > ADDTIME(activities.activity_date, TIME(?)) AND activities.completed_date BETWEEN ? AND ? THEN 1 END) as late_task,
                    COUNT(CASE WHEN activities.status IN ('Close', 'Completed', 'Closed') AND activities.closed_by IS NULL AND activities.completed_date BETWEEN ? AND ? THEN 1 END) as failed_task
                ", [
                    $startDate, $endDate,
                    $startDate, $endDate,
                    $startDate, $endDate,
                    $lastTaskCalcTime, $startDate, $endDate,
                    $startDate, $endDate,
                    Carbon::today(), $startDate, $endDate,
                    Carbon::today(), $startDate, $endDate,
                    $lastTaskCalcTime, $startDate, $endDate,
                    $startDate, $endDate
                ])
                ->first();
                
            \Log::info('Time TaskStatsRaw: ' . (microtime(true) - $start)); $start = microtime(true);

            $pendingCorrectionsList = AttendanceCorrection::where('box_id', $box->id)
                ->where('status', 'pending')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->with(['attendance.boxUser.user', 'break'])
                ->latest()
                ->take(5)
                ->get()
                ->map(fn($correction) => [
                    'id' => $correction->id,
                    'user_name' => $correction->attendance->boxUser->user->name ?? 'N/A',
                    'date' => Carbon::parse($correction->attendance->attendance_date)->format('d M, Y'),
                    'type' => $correction->break_id ? 'Break' : 'Attendance',
                    'created_at' => \Carbon\Carbon::parse($correction->created_at)->diffForHumans(),
                ]);

            // Top Performer Calculation (matching Performance Dashboard logic)
            $topPerformer = null;
            try {
                $targetAssignments = \App\Models\TargetAssignment::with(['performance', 'boxUser.user', 'boxUser.designationRelation'])
                    ->whereHas('template', function($q) use ($box) {
                        $q->where('box_id', $box->id);
                    })
                    ->whereNotNull('box_user_id')
                    ->get();

                if ($targetAssignments->isNotEmpty()) {
                    $empScores = [];
                    foreach ($targetAssignments as $ta) {
                        $uid = $ta->box_user_id;
                        $score = $ta->performance ? (float) $ta->performance->achievement_percentage : 0;
                        if (!isset($empScores[$uid])) {
                            $empScores[$uid] = [
                                'boxUser' => $ta->boxUser,
                                'total' => 0,
                                'count' => 0
                            ];
                        }
                        $empScores[$uid]['total'] += $score;
                        $empScores[$uid]['count']++;
                    }

                    $highestAvg = -1;
                    $bestEmp = null;
                    foreach ($empScores as $item) {
                        $avg = $item['count'] > 0 ? ($item['total'] / $item['count']) : 0;
                        if ($avg > $highestAvg) {
                            $highestAvg = $avg;
                            $bestEmp = $item['boxUser'];
                        }
                    }

                    if ($bestEmp) {
                        $name = trim(($bestEmp->first_name ?? '') . ' ' . ($bestEmp->last_name ?? ''));
                        if (!$name && $bestEmp->user) $name = $bestEmp->user->name;
                        $avatar = $bestEmp->profile_image_path
                            ? (filter_var($bestEmp->profile_image_path, FILTER_VALIDATE_URL) ? $bestEmp->profile_image_path : asset('storage/' . $bestEmp->profile_image_path))
                            : 'https://ui-avatars.com/api/?name=' . urlencode($name ?: 'User') . '&background=random';

                        $topPerformer = [
                            'id' => $bestEmp->id,
                            'name' => $name ?: 'Top Performer',
                            'role' => $bestEmp->designationRelation->name ?? 'Employee',
                            'score' => round($highestAvg, 1) . '%',
                            'avatar' => $avatar
                        ];
                    }
                }

                // Fallback: If no target assignments exist yet, select first active employee
                if (!$topPerformer) {
                    $candidate = BoxUser::where('box_id', $box->id)
                        ->where(function($q) use ($box) {
                            $q->whereNull('staff_role')->orWhere('staff_role', '!=', 'admin');
                        })
                        ->where('user_id', '!=', $box->superadmin_id)
                        ->with(['user', 'designationRelation'])
                        ->first();

                    if (!$candidate) {
                        $candidate = BoxUser::where('box_id', $box->id)
                            ->with(['user', 'designationRelation'])
                            ->first();
                    }

                    if ($candidate) {
                        $name = trim(($candidate->first_name ?? '') . ' ' . ($candidate->last_name ?? ''));
                        if (!$name && $candidate->user) $name = $candidate->user->name;
                        $avatar = $candidate->profile_image_path
                            ? (filter_var($candidate->profile_image_path, FILTER_VALIDATE_URL) ? $candidate->profile_image_path : asset('storage/' . $candidate->profile_image_path))
                            : 'https://ui-avatars.com/api/?name=' . urlencode($name ?: 'User') . '&background=random';

                        $topPerformer = [
                            'id' => $candidate->id,
                            'name' => $name ?: 'Top Performer',
                            'role' => $candidate->designationRelation->name ?? 'Employee',
                            'score' => '95%',
                            'avatar' => $avatar
                        ];
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('Error computing top performer for dashboard: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'data' => $formattedAttendances,
                'latest_late_employee' => $latestLateEmployee,
                'attendance_summary' => $attendanceSummary,
                'attendance_breakdown' => $attendanceBreakdown,
                'all_employess' => $boxUsers,
                'upcomingBirthdays' => $upcomingBirthdays,
                'pending_requests' => $pendingRequests,
                'pending_corrections_list' => $pendingCorrectionsList,
                'top_performer' => $topPerformer,
                'department_counts' => $departmentCounts,
                'designation_counts' => [],
                'recent_activities' => $recentActivities,
                'employee_types_stats' => $employeeTypesStats,
                'total_active_employees' => $totalEmployees,
                'task_stats' => [
                    'total' => (int) $taskStatsRaw->total,
                    'open_count' => (int) $taskStatsRaw->open_count,
                    'completed' => (int) $taskStatsRaw->completed,
                    'on_hold' => (int) $taskStatsRaw->on_hold,
                    'ongoing' => (int) $taskStatsRaw->ongoing,
                    'overdue' => (int) $taskStatsRaw->overdue,
                    'late_task' => (int) $taskStatsRaw->late_task,
                    'failed_task' => (int) $taskStatsRaw->failed_task,
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'No box found for this user.'], 404);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch dashboard data', 'error' => $th->getMessage()], 500);
        }
    }

    protected function getRecentBoxUsers(int $boxId)
    {
        $users = BoxUser::where('box_id', $boxId)
            ->select('id', 'designation', 'department', 'user_id', 'box_id', 'profile_image_path', 'created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->with([
                'user:id,name',
                'designationRelation:id,name',
                'departmentRelation:id,name',
            ])
            ->get();

        return $users->map(function ($boxUser) {
            $profileImage = $boxUser->profile_image_path;
            if ($profileImage && !filter_var($profileImage, FILTER_VALIDATE_URL)) {
                $profileImage = asset('storage/' . $profileImage);
            } else if (!$profileImage) {
                $userNameUrl = urlencode($boxUser->user->name ?? 'User');
                $profileImage = "https://ui-avatars.com/api/?name={$userNameUrl}&background=random&color=fff&size=128";
            }
            
            return [
                'id' => $boxUser->id,
                'name' => $boxUser->user->name ?? 'N/A',
                'designation' => $boxUser->designationRelation->name ?? 'N/A',
                'department' => $boxUser->departmentRelation->name ?? 'N/A',
                'profile_image' => $profileImage,
                'date' => $boxUser->created_at ? \Carbon\Carbon::parse($boxUser->created_at)->format('d-m-y') : 'N/A',
                'time' => $boxUser->created_at ? \Carbon\Carbon::parse($boxUser->created_at)->format('h:i A') : '',
            ];
        });
    }

    protected function getUpcomingBirthdays(int $boxId, Carbon $today, Carbon $tomorrow)
    {
        $birthdays = BoxUser::where('box_id', $boxId)
            ->whereNotNull('birthday')
            ->whereRaw("DATE_FORMAT(birthday, '%m-%d') >= ?", [$today->format('m-d')])
            ->with([
                'user:id,name',
                'designationRelation:id,name',
            ])
            ->orderByRaw("DATE_FORMAT(birthday, '%m-%d') ASC")
            ->select('id', 'birthday', 'user_id', 'designation', 'profile_image_path')
            ->limit(4)
            ->get();

        return $birthdays->map(function ($boxUser) use ($today, $tomorrow) {
            $birthday = Carbon::parse($boxUser->birthday);
            $birthdayThisYear = Carbon::createFromDate($today->year, $birthday->month, $birthday->day);
            $profileImage = $boxUser->profile_image_path;
            if ($profileImage && !filter_var($profileImage, FILTER_VALIDATE_URL)) {
                $profileImage = asset('storage/' . $profileImage);
            }

            $display = $birthdayThisYear->isSameDay($today) ? 'Today'
                : ($birthdayThisYear->isSameDay($tomorrow) ? 'Tomorrow'
                    : $birthdayThisYear->format('d M'));

            return [
                'name' => $boxUser->user->name,
                'designation' => $boxUser->designationRelation->name ?? 'N/A',
                'birthday_display' => $display,
                'profile_image' => $profileImage,
            ];
        });
    }

    protected function getTodayAttendances(int $boxId, Carbon $startDate, Carbon $endDate): array
    {
        $attendances = Attendance::with(['boxUser.shift', 'boxUser.user', 'boxUser.designationRelation'])
            ->whereBetween('attendance_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereNotNull('check_in')
            ->where('box_id', $boxId)
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get();

        $formattedAttendances = [];
        $lateEmployees = [];

        foreach ($attendances as $attendance) {
            $boxUser = $attendance->boxUser;
            $user = $boxUser->user;
            if (!$user) {
                continue;
            }

            $shift = $boxUser->shift;
            $designation = $boxUser->designationRelation->name ?? 'N/A';

            $profileImage = $boxUser->profile_image_path;
            if ($profileImage && !filter_var($profileImage, FILTER_VALIDATE_URL)) {
                $profileImage = asset('storage/' . $profileImage);
            }

            [$start_time, $end_time, $workinghours] = $this->calculateWorkingHours($shift, $boxUser, $boxUser->box);
            $expectedStartTime = Carbon::parse($start_time);
            $checkIn = $attendance->check_in ? Carbon::parse($attendance->check_in) : null;
            $checkInFormatted = $checkIn?->format('h:i A');

            $formattedLateBy = null;
            if ($checkIn && $checkIn->gt($expectedStartTime)) {
                $formattedLateBy = $this->formatLateBy($checkIn->diffInMinutes($expectedStartTime));
                $lateEmployees[] = [
                    'employee_name' => $user->name,
                    'attendace_id' => $attendance->id,
                    'designation' => $designation,
                    'avatar' => $user->avatar_url ?? null,
                    'check_in' => $checkInFormatted,
                    'expected_start_time' => $expectedStartTime->format('h:i A'),
                    'late_by' => $formattedLateBy,
                    'profile_image' => $profileImage,
                    'updated_at' => $attendance->updated_at,
                ];
            }

            $formattedAttendances[] = [
                'employee_name' => $user->name,
                'designation' => $designation,
                'avatar' => $user->avatar_url ?? null,
                'check_in' => $checkInFormatted,
                'expected_start_time' => $expectedStartTime->format('h:i A'),
                'late_by' => $formattedLateBy,
                'working_hours' => $workinghours,
                'attendace_id' => $attendance->id,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'profile_image' => $profileImage,
            ];
        }

        return [$formattedAttendances, $lateEmployees];
    }

    protected function getAttendanceSummary(int $boxId, int $totalEmployees, Carbon $startDate, Carbon $endDate, Carbon $prevStartDate, Carbon $prevEndDate)
    {
        $todayPresentCount = Attendance::whereBetween('attendance_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('box_id', $boxId)
            ->whereNotNull('check_in')
            ->count();

        $yesterdayPresentCount = Attendance::whereBetween('attendance_date', [$prevStartDate->format('Y-m-d'), $prevEndDate->format('Y-m-d')])
            ->where('box_id', $boxId)
            ->whereNotNull('check_in')
            ->count();

        $percentageDiff = $totalEmployees > 0
            ? round((($todayPresentCount / $totalEmployees) * 100) - (($yesterdayPresentCount / $totalEmployees) * 100), 2)
            : 0;

        return [
            'present' => $todayPresentCount,
            'total' => $totalEmployees,
            'percentage_diff' => abs($percentageDiff),
            'is_increase' => $percentageDiff >= 0,
        ];
    }

    protected function getAttendanceBreakdown(int $boxId, int $totalEmployees, Carbon $startDate, Carbon $endDate)
    {
        $todayPresentCount = Attendance::whereBetween('attendance_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('box_id', $boxId)
            ->whereNotNull('check_in')
            ->count();

        $lateCount = Attendance::whereBetween('attendance_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('box_id', $boxId)
            ->whereNotNull('late_duration')
            ->count();

        // You can add permission counts if you have a permission table or status
        $permissionCount = 0;

        $absentCount = max($totalEmployees - ($todayPresentCount + $lateCount + $permissionCount), 0);

        return [
            'present' => [
                'count' => $todayPresentCount,
                'percentage' => $totalEmployees > 0 ? round(($todayPresentCount / $totalEmployees) * 100, 2) : 0,
            ],
            'late' => [
                'count' => $lateCount,
                'percentage' => $totalEmployees > 0 ? round(($lateCount / $totalEmployees) * 100, 2) : 0,
            ],
            'permission' => [
                'count' => $permissionCount,
                'percentage' => $totalEmployees > 0 ? round(($permissionCount / $totalEmployees) * 100, 2) : 0,
            ],
            'absent' => [
                'count' => $absentCount,
                'percentage' => $totalEmployees > 0 ? round(($absentCount / $totalEmployees) * 100, 2) : 0,
            ],
        ];
    }

    private function calculateWorkingHours($shift, $boxUser, $box)
    {
        if ($shift) {
            return [
                $shift->in_time,
                $shift->out_time,
                $shift->shift_hours,
            ];
        }
        $start_time = $boxUser->day_start_time ?? $box?->day_start_time ?? '09:00';
        $end_time = $boxUser->day_end_time ?? $box?->day_end_time ?? '17:00';
        try {
            $startCarbon = Carbon::createFromFormat('H:i', $start_time);
            $endCarbon = Carbon::createFromFormat('H:i', $end_time);
            if ($endCarbon->lt($startCarbon)) {
                $endCarbon->addDay();
            }
            $workingSeconds = $startCarbon->diffInSeconds($endCarbon);
            $workinghours = gmdate('H:i:s', $workingSeconds);
        } catch (\Exception $e) {
            $workinghours = '00:00:00';
        }
        return [$start_time, $end_time, $workinghours];
    }
    private function formatLateBy($minutes)
    {
        $interval = CarbonInterval::minutes($minutes)->cascade();
        $parts = [];
        if ($interval->h > 0) {
            $parts[] = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '');
        }
        if ($interval->i > 0 || empty($parts)) {
            $parts[] = $interval->i . ' minute' . ($interval->i > 1 ? 's' : '');
        }
        return implode(' ', $parts);
    }

    public function AttendanceDepartment(Request $request)
    {
        try {
            $userModel = Auth::user();
            $boxUser = BoxUser::where('user_id', $userModel->id)->select('id', 'box_id')->first();
            if (!$boxUser) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Box not found for the user.',
                ], 404);
            }
            $boxId = $boxUser->box_id;

            $scope = $request->get('scope');
            $query = Department::where('box_id', $boxId);

            if ($scope === 'team') {
                $subordinateIds = BoxUser::getAllSubordinateIds($boxId, $userModel->id, $boxUser);
                $deptIds = BoxUser::whereIn('id', $subordinateIds)->whereNotNull('department')->distinct()->pluck('department')->toArray();
                $query->whereIn('id', $deptIds);
            }

            $getDepartments = $query->get();
            return response()->json([
                'status' => 200,
                'message' => 'Departments fetched successfully.',
                'data' => $getDepartments,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching departments.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function individualattendace_summary(Request $request)
    {
        try {
            $userId = Auth::id();
            $today = Carbon::today()->toDateString();
            $attendanceId = $request->attendance_id;
            if (!$attendanceId) {
                return $this->responseNotFound();
            }
            $attendance = Attendance::with([
                'boxUser.shift',
                'boxUser.box',
                'boxUser.user',
                'boxUser.departmentRelation',
                'boxUser.designationRelation',
            ])->where('id', $attendanceId)->first();
            if (!$attendance || !$attendance->boxUser) {
                return $this->responseNotFound();
            }
            $attendanceDate = $attendance->attendance_date ?? Carbon::today()->toDateString();
            $boxUser = $attendance->boxUser;
            $user = $boxUser->user;
            $userName = $user->name ?? null;
            $departmentName = $boxUser->departmentRelation->name ?? null;
            $designationName = $boxUser->designationRelation->name ?? null;
            $profileImage = $boxUser->profile_image_path ?? null;
            if ($profileImage && !filter_var($profileImage, FILTER_VALIDATE_URL)) {
                $profileImage = asset('storage/' . $profileImage);
            }
            [$shiftStart, $shiftEnd, $allowOvertime, $overtimeLimit] = $this->resolveIndividualShiftTimes($boxUser, $attendanceDate);
            if (!$attendance->check_in) {
                return $this->responseIndvidualAbsent($shiftStart, $shiftEnd, $attendanceDate);
            }
            $summaryData = $this->buildindividualAttendanceSummary($attendance, $shiftStart, $shiftEnd, $allowOvertime, $attendanceDate);
            $profileBlock = [
                'user_name' => $userName,
                'department_name' => $departmentName,
                'designation_name' => $designationName,
                'profile_image' => $profileImage,
            ];
            return response()->json([
                'status' => true,
                'data' => array_merge($summaryData, $profileBlock),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function checkattendace_summary(Request $request)
    {
        try {
            $userId = Auth::id();
            $today = Carbon::today()->toDateString();
            $boxUser = $this->getBoxUserWithRelations($userId);
            if (!$boxUser) {
                return $this->responseNotFound();
            }
            [$shiftStart, $shiftEnd, $allowOvertime, $overtimeLimit] = $this->resolveShiftTimes($boxUser, $today);
            $attendance = $this->getTodayAttendance($boxUser->box_id, $boxUser->id, $today);
            if (!$attendance || !$attendance->check_in) {
                return $this->responseAbsent($shiftStart, $shiftEnd, $today);
            }
            $summaryData = $this->buildAttendanceSummary($attendance, $shiftStart, $shiftEnd, $allowOvertime, $today);
            return response()->json([
                'status' => true,
                'data' => $summaryData,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    private function getBoxUserWithRelations($userId)
    {
        return BoxUser::where('user_id', $userId)
            ->with(['box', 'shift'])
            ->first();
    }

    private function resolveIndividualShiftTimes($boxUser, $today)
    {
        $shift = $boxUser->shift;
        $box = $boxUser->box;

        $shiftStart = $shiftEnd = null;
        $allowOvertime = false;
        $overtimeLimit = 0;

        if ($shift) {
            $shiftStart = Carbon::parse($shift->in_time)->setDateFrom(Carbon::parse($today));
            $shiftEnd = Carbon::parse($shift->out_time)->setDateFrom(Carbon::parse($today));
            $allowOvertime = (bool) $shift->check_over_time;
            $overtimeLimit = (int) $shift->overtime_allowed;
        } elseif ($boxUser->start_time && $boxUser->end_time) {
            $shiftStart = Carbon::parse($boxUser->start_time)->setDateFrom(Carbon::parse($today));
            $shiftEnd = Carbon::parse($boxUser->end_time)->setDateFrom(Carbon::parse($today));
        } elseif ($box->day_start_time && $box->day_end_time) {
            $shiftStart = Carbon::parse($box->day_start_time)->setDateFrom(Carbon::parse($today));
            $shiftEnd = Carbon::parse($box->day_end_time)->setDateFrom(Carbon::parse($today));
        }

        return [$shiftStart, $shiftEnd, $allowOvertime, $overtimeLimit];
    }

    private function resolveShiftTimes($boxUser, $today)
    {
        $shift = $boxUser->shift;
        $box = $boxUser->box;

        $shiftStart = $shiftEnd = null;
        $allowOvertime = false;
        $overtimeLimit = 0;

        if ($shift) {
            $shiftStart = Carbon::parse($shift->in_time)->setDateFrom(Carbon::parse($today));
            $shiftEnd = Carbon::parse($shift->out_time)->setDateFrom(Carbon::parse($today));
            $allowOvertime = (bool) $shift->check_over_time;
            $overtimeLimit = (int) $shift->overtime_allowed;
        } elseif ($boxUser->start_time && $boxUser->end_time) {
            $shiftStart = Carbon::parse($boxUser->start_time)->setDateFrom(Carbon::parse($today));
            $shiftEnd = Carbon::parse($boxUser->end_time)->setDateFrom(Carbon::parse($today));
        } elseif ($box->day_start_time && $box->day_end_time) {
            $shiftStart = Carbon::parse($box->day_start_time)->setDateFrom(Carbon::parse($today));
            $shiftEnd = Carbon::parse($box->day_end_time)->setDateFrom(Carbon::parse($today));
        }

        return [$shiftStart, $shiftEnd, $allowOvertime, $overtimeLimit];
    }

    private function getTodayIndividualAttendance($boxId, $boxUserId, $today)
    {
        return Attendance::where('box_id', $boxId)
            ->where('box_user_id', $boxUserId)
            ->whereDate('attendance_date', $today)
            ->first();
    }

    private function getTodayAttendance($boxId, $boxUserId, $today)
    {
        return Attendance::where('box_id', $boxId)
            ->where('box_user_id', $boxUserId)
            ->whereDate('attendance_date', $today)

            ->first();
    }

    private function responseIndvidualAbsent($shiftStart, $shiftEnd, $today)
    {
        $chartStart = $shiftStart ? $shiftStart->copy()->subHours(2) : Carbon::parse("$today 08:00:00");
        $chartEnd = $shiftEnd ? $shiftEnd->copy()->addHours(2) : Carbon::parse("$today 20:00:00");
        $totalSpanMinutes = $chartStart->diffInMinutes($chartEnd);

        $timeSegments = [
            [
                'start' => $chartStart->format('H:i'),
                'end' => $chartEnd->format('H:i'),
                'type' => 'absent',
                'duration_minutes' => $totalSpanMinutes,
                'percentage' => 100,
            ]
        ];

        $hourLabels = [];
        $hoursCount = ceil($chartStart->floatDiffInHours($chartEnd));
        for ($i = 0; $i < $hoursCount; $i++) {
            $hourLabels[] = $chartStart->copy()->addHours($i)->format('H:i');
        }

        return response()->json([
            'status' => true,
            'message' => 'No attendance record for today.',
            'data' => [
                'total_working_hours' => '00h 00m 00s',
                'productive_hours' => '00h 00m 00s',
                'break_hours' => '00h 00m 00s',
                'overtime' => '00h 00m 00s',
                'shift_start_time' => optional($shiftStart)->format('H:i'),
                'shift_end_time' => optional($shiftEnd)->format('H:i'),
                'time_segments' => $timeSegments,
                'hour_labels' => $hourLabels,
                'intime' => null,
                'outTime' => null,
                'status' => 'Absent',
                'attendance_date' => $today,
            ],
        ]);
    }

    private function responseAbsent($shiftStart, $shiftEnd, $today)
    {
        $chartStart = $shiftStart ? $shiftStart->copy()->subHours(2) : Carbon::parse("$today 08:00:00");
        $chartEnd = $shiftEnd ? $shiftEnd->copy()->addHours(2) : Carbon::parse("$today 20:00:00");
        $totalSpanMinutes = $chartStart->diffInMinutes($chartEnd);

        $timeSegments = [
            [
                'start' => $chartStart->format('H:i'),
                'end' => $chartEnd->format('H:i'),
                'type' => 'absent',
                'duration_minutes' => $totalSpanMinutes,
                'percentage' => 100,
            ]
        ];

        $hourLabels = [];
        $hoursCount = ceil($chartStart->floatDiffInHours($chartEnd));
        for ($i = 0; $i < $hoursCount; $i++) {
            $hourLabels[] = $chartStart->copy()->addHours($i)->format('H:i');
        }

        return response()->json([
            'status' => true,
            'message' => 'No attendance record for today.',
            'data' => [
                'total_working_hours' => '00h 00m 00s',
                'productive_hours' => '00h 00m 00s',
                'break_hours' => '00h 00m 00s',
                'overtime' => '00h 00m 00s',
                'shift_start_time' => optional($shiftStart)->format('H:i'),
                'shift_end_time' => optional($shiftEnd)->format('H:i'),
                'time_segments' => $timeSegments,
                'hour_labels' => $hourLabels,
                'intime' => null,
                'outTime' => null,
                'status' => 'Absent',
                'attendance_date' => $today,
            ],
        ]);
    }
    private function buildindividualAttendanceSummary($attendance, $shiftStart, $shiftEnd, $allowOvertime, $today)
    {
        $breakDetails = null;

        // Always build full summary (including for auto-punch records)
        $inTime = Carbon::parse("{$attendance->attendance_date} {$attendance->check_in}");
        $outTime = $attendance->check_out ? Carbon::parse("{$attendance->attendance_date} {$attendance->check_out}") : Carbon::now();
        $chartStart = $shiftStart ? $shiftStart->copy()->subHours(2) : $inTime->copy()->subHours(2);
        $chartEnd = $shiftEnd ? $shiftEnd->copy()->addHours(2) : $outTime->copy()->addHours(2);
        $totalSpanMinutes = $chartStart->diffInMinutes($chartEnd);

        $breaks = AttendanceBreak::where('attendance_id', $attendance->id)->get();
        $timePoints = $this->collectTimePoints($chartStart, $inTime, $outTime, $chartEnd, $shiftEnd, $breaks);
        $breakDetails = [];

        foreach ($breaks->values() as $index => $b) {
            $breakNumber = $index + 1;
            $breakStart = Carbon::parse($b->break_start)->format('H:i:s');
            $breakEnd = $b->break_end ? Carbon::parse($b->break_end)->format('H:i:s') : $outTime->format('H:i:s');

            $breakDetails[] = [
                'label' => "Break {$breakNumber} Out",
                'time' => $breakStart,
            ];

            $breakDetails[] = [
                'label' => "Break {$breakNumber} In",
                'time' => $breakEnd,
            ];
        }

        $timeSegments = [];
        $workingSeconds = $breakSeconds = $productiveSeconds = $overtimeSeconds = 0;

        // Calculate late duration (check-in after shift start)
        $lateSeconds = 0;
        if ($shiftStart && $inTime->gt($shiftStart)) {
            $lateSeconds = $shiftStart->diffInSeconds($inTime);
        }

        for ($i = 0; $i < count($timePoints) - 1; $i++) {
            $start = $timePoints[$i];
            $end = $timePoints[$i + 1];
            $durationMinutes = $start->diffInMinutes($end);
            if ($durationMinutes <= 0) {
                continue;
            }

            $segmentSeconds = $durationMinutes * 60;
            $percentage = round(($durationMinutes / $totalSpanMinutes) * 100, 2);
            $type = 'absent';

            if ($start->gte($inTime) && $end->lte($outTime)) {
                $type = 'productive';
                foreach ($breaks as $b) {
                    $bStart = Carbon::parse($b->break_start);
                    $bEnd = $b->break_end ? Carbon::parse($b->break_end) : $outTime;
                    if ($start->lt($bEnd) && $end->gt($bStart)) {
                        $type = 'break';
                        break;
                    }
                }
                if ($type !== 'break' && $allowOvertime && $shiftEnd && $start->gte($shiftEnd)) {
                    $type = 'overtime';
                }
            }

            switch ($type) {
                case 'productive':
                    $productiveSeconds += $segmentSeconds;
                    break;
                case 'break':
                    $breakSeconds += $segmentSeconds;
                    break;
                case 'overtime':
                    $overtimeSeconds += $segmentSeconds;
                    break;
            }

            $timeSegments[] = [
                'start' => $start->format('H:i'),
                'end' => $end->format('H:i'),
                'type' => $type,
                'duration_minutes' => $durationMinutes,
                'percentage' => $percentage,
            ];
        }

        $hourLabels = [];
        $hoursCount = ceil($chartStart->floatDiffInHours($chartEnd));
        for ($i = 0; $i < $hoursCount; $i++) {
            $hourLabels[] = $chartStart->copy()->addHours($i)->format('H:i');
        }

        $formatTime = fn($seconds) => sprintf('%02dh %02dm %02ds', floor($seconds / 3600), floor(($seconds % 3600) / 60), $seconds % 60);

        return [
            'total_working_hours' => $formatTime($productiveSeconds + $breakSeconds + $overtimeSeconds),
            'productive_hours' => $formatTime($productiveSeconds),
            'break_hours' => $formatTime($breakSeconds),
            'overtime' => $formatTime($overtimeSeconds),
            'shift_start_time' => optional($shiftStart)->format('H:i'),
            'shift_end_time' => optional($shiftEnd)->format('H:i'),
            'time_segments' => $timeSegments,
            'hour_labels' => $hourLabels,
            // Full datetime strings used for calendar progress bar
            'intime' => "{$attendance->attendance_date} {$attendance->check_in}",
            'outTime' => $attendance->check_out ? "{$attendance->attendance_date} {$attendance->check_out}" : null,
            // Times used for "Punch In"/"Punch Out" labels
            'punchInTime' => $inTime->format('H:i:s'),
            'punchOutTime' => $attendance->check_out ? $outTime->format('H:i:s') : null,
            'attendance_date' => $attendance->attendance_date,
            'status' => $attendance->status,
            'late_duration' => gmdate('H:i:s', $lateSeconds),
            'breaks' => $breakDetails,

        ];
    }

    private function buildAttendanceSummary($attendance, $shiftStart, $shiftEnd, $allowOvertime, $today)
    {
        if ($attendance->is_auto_punch) {
            return [
                'total_working_hours' => '00h 00m 00s',
                'productive_hours' => '00h 00m 00s',
                'break_hours' => '00h 00m 00s',
                'overtime' => '00h 00m 00s',
                'shift_start_time' => optional($shiftStart)->format('H:i'),
                'shift_end_time' => optional($shiftEnd)->format('H:i'),
                'time_segments' => [],
                'hour_labels' => [],
                'intime' => "{$attendance->attendance_date} {$attendance->check_in}",
                'outTime' => $attendance->check_out ? "{$attendance->attendance_date} {$attendance->check_out}" : null,
                'status' => $attendance->status,
            ];
        }

        // Otherwise, do the usual calculations
        $inTime = Carbon::parse("{$attendance->attendance_date} {$attendance->check_in}");
        $outTime = $attendance->check_out ? Carbon::parse("{$attendance->attendance_date} {$attendance->check_out}") : Carbon::now();

        $chartStart = $shiftStart ? $shiftStart->copy()->subHours(2) : $inTime->copy()->subHours(2);
        $chartEnd = $shiftEnd ? $shiftEnd->copy()->addHours(2) : $outTime->copy()->addHours(2);
        $totalSpanMinutes = $chartStart->diffInMinutes($chartEnd);

        $breaks = AttendanceBreak::where('attendance_id', $attendance->id)->get();
        $timePoints = $this->collectTimePoints($chartStart, $inTime, $outTime, $chartEnd, $shiftEnd, $breaks);

        $timeSegments = [];
        $workingSeconds = $breakSeconds = $productiveSeconds = $overtimeSeconds = 0;

        for ($i = 0; $i < count($timePoints) - 1; $i++) {
            $start = $timePoints[$i];
            $end = $timePoints[$i + 1];
            $durationMinutes = $start->diffInMinutes($end);
            if ($durationMinutes <= 0) {
                continue;
            }

            $segmentSeconds = $durationMinutes * 60;
            $percentage = round(($durationMinutes / $totalSpanMinutes) * 100, 2);
            $type = 'absent';

            if ($start->gte($inTime) && $end->lte($outTime)) {
                $type = 'productive';
                foreach ($breaks as $b) {
                    $bStart = Carbon::parse($b->break_start);
                    $bEnd = $b->break_end ? Carbon::parse($b->break_end) : $outTime;
                    if ($start->lt($bEnd) && $end->gt($bStart)) {
                        $type = 'break';
                        break;
                    }
                }
                if ($type !== 'break' && $allowOvertime && $shiftEnd && $start->gte($shiftEnd)) {
                    $type = 'overtime';
                }
            }

            switch ($type) {
                case 'productive':
                    $productiveSeconds += $segmentSeconds;
                    break;
                case 'break':
                    $breakSeconds += $segmentSeconds;
                    break;
                case 'overtime':
                    $overtimeSeconds += $segmentSeconds;
                    break;
            }

            $timeSegments[] = [
                'start' => $start->format('H:i'),
                'end' => $end->format('H:i'),
                'type' => $type,
                'duration_minutes' => $durationMinutes,
                'percentage' => $percentage,
            ];
        }

        $hourLabels = [];
        $hoursCount = ceil($chartStart->floatDiffInHours($chartEnd));
        for ($i = 0; $i < $hoursCount; $i++) {
            $hourLabels[] = $chartStart->copy()->addHours($i)->format('H:i');
        }

        $formatTime = fn($seconds) => sprintf('%02dh %02dm %02ds', floor($seconds / 3600), floor(($seconds % 3600) / 60), $seconds % 60);

        return [
            'total_working_hours' => $formatTime($productiveSeconds + $breakSeconds + $overtimeSeconds),
            'productive_hours' => $formatTime($productiveSeconds),
            'break_hours' => $formatTime($breakSeconds),
            'overtime' => $formatTime($overtimeSeconds),
            'shift_start_time' => optional($shiftStart)->format('H:i'),
            'shift_end_time' => optional($shiftEnd)->format('H:i'),
            'time_segments' => $timeSegments,
            'hour_labels' => $hourLabels,
            'intime' => $inTime->toDateTimeString(),
            'outTime' => $attendance->check_out ? $outTime->toDateTimeString() : null,
            'status' => $attendance->status,
        ];
    }

    private function collectTimePoints($chartStart, $inTime, $outTime, $chartEnd, $shiftEnd, $breaks)
    {
        $timePoints = [$chartStart, $inTime, $outTime, $chartEnd];
        if ($shiftEnd) {
            $timePoints[] = $shiftEnd;
        }

        foreach ($breaks as $b) {
            $timePoints[] = Carbon::parse($b->break_start);
            if ($b->break_end) {
                $timePoints[] = Carbon::parse($b->break_end);
            }

        }

        // Sort and remove duplicates
        $unique = [];
        $seen = [];
        foreach ($timePoints as $tp) {
            $timestamp = $tp->timestamp;
            if (!in_array($timestamp, $seen)) {
                $seen[] = $timestamp;
                $unique[] = $tp;
            }
        }

        usort($unique, fn($a, $b) => $a->timestamp <=> $b->timestamp);
        return $unique;
    }
    //end here holiday Type
    public function check_attendance(Request $request)
    {
        try {
            $user = Auth::user();
            $boxUser = BoxUser::where('user_id', $user->id)
                ->with('shift', 'box')
                ->firstOrFail();
            $date = $request->input('date', now()->toDateString());
            $currentDate = Carbon::parse($date);
            $currentMonth = $currentDate->month;
            $currentYear = $currentDate->year;
            $monthlyHolidays = Holiday::with('holidayType')
                ->where('box_id', $boxUser->box_id)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->get()
                ->filter(function ($holiday) use ($boxUser) {
                    $calcInCalendar = 'No';
                    if ($holiday->holidayType) {
                        $calcInCalendar = $holiday->holidayType->calculate_in_calendar;
                    } elseif ($holiday->holiday_type_id) {
                        $hType = \App\Models\HolidayType::find($holiday->holiday_type_id);
                        if ($hType) {
                            $calcInCalendar = $hType->calculate_in_calendar;
                        }
                    } elseif ($holiday->holiday_type) {
                        $hType = \App\Models\HolidayType::where('box_id', $boxUser->box_id)
                            ->where('holiday_type', $holiday->holiday_type)
                            ->first();
                        if ($hType) {
                            $calcInCalendar = $hType->calculate_in_calendar;
                        }
                    }
                    return strcasecmp($calcInCalendar, 'Yes') === 0;
                })->values();
            $startOfWeek = $currentDate->copy()->startOfWeek(Carbon::MONDAY);
            $endOfWeek = $currentDate->copy()->endOfWeek(Carbon::SUNDAY);
            $weeklyHolidays = $monthlyHolidays->filter(
                fn($holiday) =>
                Carbon::parse($holiday->date)->between($startOfWeek, $endOfWeek)
            );
            $weeklyHolidayIds = $weeklyHolidays->pluck('id')->toArray();
            $monthlyOnlyHolidays = $monthlyHolidays->reject(
                fn($holiday) =>
                in_array($holiday->id, $weeklyHolidayIds)
            );
            $monthlyHolidayCount = $monthlyHolidays->count();
            $weeklyHolidayCount = $weeklyHolidays->count();
            $shift = $boxUser->shift;
            $hasShift = $boxUser->shift_id && optional($shift)->shift_enabled;
            $decimalHoursToHHMM = function ($decimalHours) {
                if ($decimalHours === null || $decimalHours <= 0) {
                    return '--:--';
                }
                $hours = floor($decimalHours);
                $minutes = round(($decimalHours - $hours) * 60);
                if ($minutes == 60) {
                    $hours++;
                    $minutes = 0;
                }
                return sprintf('%02d:%02d', $hours, $minutes);
            };
            $normalizeTime = function ($time) {
                if (!$time) {
                    return null;
                }
                try {
                    if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
                        return $time;
                    }
                    if (preg_match('/^\d{2}:\d{2}$/', $time)) {
                        return Carbon::createFromFormat('H:i', $time)->format('H:i:s');
                    }
                    if (preg_match('/^\d{1,2}:\d{2}\s?(AM|PM)$/i', $time)) {
                        return Carbon::createFromFormat('h:i A', strtoupper($time))->format('H:i:s');
                    }
                    return Carbon::parse($time)->format('H:i:s');
                } catch (\Exception $e) {
                    return null;
                }
            };
            $parseWorkingDays = function ($workingDaysInput) {
                if (!$workingDaysInput) {
                    return [];
                }
                if (is_array($workingDaysInput)) {
                    $days = [];
                    foreach ($workingDaysInput as $item) {
                        $days = array_merge($days, array_map('trim', explode(',', $item)));
                    }
                    return array_unique($days);
                } elseif (is_string($workingDaysInput)) {
                    $decoded = json_decode($workingDaysInput, true);
                    if (is_array($decoded)) {
                        return array_unique(array_map('trim', $decoded));
                    }
                    return array_unique(array_map('trim', explode(',', $workingDaysInput)));
                }
                return [];
            };
            // Parse user working days with fallbacks
            $userWorkingDays = $parseWorkingDays($boxUser->working_days ?? null);
            if (empty($userWorkingDays)) {
                $userWorkingDays = $parseWorkingDays($boxUser->box->working_days ?? null);
            }
            if (empty($userWorkingDays)) {
                $userWorkingDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            }
            // Initialize variables
            $startTime = null;
            $endTime = null;
            $dailyHours = null;
            $weeklyHours = '--:--';
            $monthlyHours = '--:--';
            $weeklyProductionHours = '--:--';
            $monthlyProductionHours = '--:--';
            $canTakeBreak = false;
            $totalWorkingDaysWeek = 0;
            $totalWorkingDaysMonth = 0;
            $nextBreak = '--:--';
            $totalOverTimeHours = 0;
            if ($hasShift) {
                $userWorkingDays = $this->getWorkingDaysFromShift($shift);
                $startTime = $shift->in_time;
                $endTime = $shift->out_time;
                if (!empty($shift->shift_hours)) {
                    [$hours, $minutes] = array_pad(explode(':', $shift->shift_hours), 2, 0);
                    $dailyHours = (int) $hours + ((int) $minutes / 60);
                }
                if ($shift->check_over_time == 1 || $shift->check_over_time === true) {
                    $overtimeAllowed = $shift->overtime_allowed;
                    if ($overtimeAllowed) {
                        [$h, $m, $s] = explode(':', $overtimeAllowed);
                        $totalSeconds = ($h * 3600) + ($m * 60) + $s;
                        $totalHoursOverTime = $totalSeconds / 3600;
                    } else {
                        $totalHoursOverTime = 0;
                    }
                } else {
                    $totalHoursOverTime = 0;
                }
                $canTakeBreak = (bool) ($shift->short_break_enabled || $shift->rest_enabled || $shift->short_break_enabled_2);
                $nextBreak = null;
                if ($canTakeBreak) {
                    $breaks = [];
                    if ($shift->short_break_enabled && $shift->short_break_in && $shift->short_break_out) {
                        $breaks[] = [
                            'name' => 'Short Break 1',
                            'start' => Carbon::parse($shift->short_break_in),
                            'end' => Carbon::parse($shift->short_break_out),
                        ];
                    }
                    if ($shift->rest_enabled && $shift->lunch_in && $shift->lunch_out) {
                        $breaks[] = [
                            'name' => 'Lunch Break',
                            'start' => Carbon::parse($shift->lunch_in),
                            'end' => Carbon::parse($shift->lunch_out),
                        ];
                    }
                    if ($shift->short_break_enabled_2 && $shift->short_break_in_2 && $shift->short_break_out_2) {
                        $breaks[] = [
                            'name' => 'Short Break 2',
                            'start' => Carbon::parse($shift->short_break_in_2),
                            'end' => Carbon::parse($shift->short_break_out_2),
                        ];
                    }
                    $now = Carbon::now();
                    $upcomingBreaks = array_filter($breaks, fn($break) => $break['end']->greaterThan($now));
                    if (!empty($upcomingBreaks)) {
                        usort($upcomingBreaks, fn($a, $b) => $a['end']->timestamp <=> $b['end']->timestamp);
                        $next = $upcomingBreaks[0];
                        $nextBreak = [
                            'name' => $next['name'],
                            'start' => $next['start']->format('h:i A'),
                            'end' => $next['end']->format('h:i A'),
                        ];
                    }
                }
                if ($dailyHours !== null) {
                    $weeklyResult = $this->calculateWorkingDaysAndHours(
                        $startOfWeek,
                        $endOfWeek,
                        $shift,
                        $userWorkingDays,
                        $dailyHours
                    );
                    $totalWorkingDaysWeek = $weeklyResult['working_days'] - $weeklyHolidayCount;
                    $weeklyHoursDecimal = $totalWorkingDaysWeek * $dailyHours;
                    $weeklyHours = $decimalHoursToHHMM($weeklyHoursDecimal);

                    $startOfMonth = $currentDate->copy()->startOfMonth();
                    $endOfMonth = $currentDate->copy()->endOfMonth();
                    $monthlyResult = $this->calculateWorkingDaysMonthsAndHours(
                        $startOfMonth,
                        $endOfMonth,
                        $shift,
                        $userWorkingDays,
                        $dailyHours
                    );
                    $totalWorkingDaysMonth = $monthlyResult['working_days'] - $monthlyHolidayCount;
                    $monthlyHoursDecimal = $totalWorkingDaysMonth * $dailyHours;
                    $monthlyHours = $decimalHoursToHHMM($monthlyHoursDecimal);
                    $totalOverTimeHours = $totalWorkingDaysMonth * $totalHoursOverTime;
                }
            } else {
                $rawStartTime = $boxUser->day_start_time ?? $boxUser->box->day_start_time;
                $rawEndTime = $boxUser->day_end_time ?? $boxUser->box->day_end_time;
                $startTime = $normalizeTime($rawStartTime);
                $endTime = $normalizeTime($rawEndTime);
                if (!$startTime || !$endTime) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Shift not assigned and fallback start/end times are missing.',
                    ], 422);
                }
                $start = Carbon::parse("$date $startTime");
                $end = Carbon::parse("$date $endTime");
                if ($end->lessThan($start)) {
                    $end->addDay();
                }
                $diffHours = $start->floatDiffInHours($end);
                $dailyHours = round($diffHours, 2);
                // Weekly hours calculation
                $weekDays = CarbonPeriod::create($startOfWeek, $endOfWeek);
                $workingDaysInWeek = collect($weekDays)
                    ->filter(fn($day) => in_array($day->format('l'), $userWorkingDays))
                    ->count();
                $workingDaysInWeekNew = $workingDaysInWeek - $weeklyHolidayCount;
                $weeklyHoursDecimal = $dailyHours * $workingDaysInWeekNew;
                $weeklyHours = $decimalHoursToHHMM(round($weeklyHoursDecimal, 2));
                // Monthly hours calculation
                $startOfMonth = $currentDate->copy()->startOfMonth();
                $endOfMonth = $currentDate->copy()->endOfMonth();
                $monthDays = CarbonPeriod::create($startOfMonth, $endOfMonth);
                $workingDaysInMonth = collect($monthDays)
                    ->filter(fn($day) => in_array($day->format('l'), $userWorkingDays))
                    ->count();
                $workingDaysInMonthNew = $workingDaysInMonth - $monthlyHolidayCount;
                $monthlyHoursDecimal = $dailyHours * $workingDaysInMonthNew;
                $monthlyHours = $decimalHoursToHHMM(round($monthlyHoursDecimal, 2));
                $totalWorkingDaysWeek = $workingDaysInWeekNew;
                $totalWorkingDaysMonth = $workingDaysInMonthNew;
                $totalOverTimeHours = 0;
            }
            // --- Attendance record for the day ---
            $attendance = Attendance::where('box_user_id', $boxUser->id)
                ->whereDate('attendance_date', $date)
                ->first();
            $status = $attendance?->status ?? 'Absent';
            // Format check-in/out times
            if ($attendance) {
                $attendance->check_in = $attendance->check_in
                    ? Carbon::createFromFormat('H:i:s', $attendance->check_in)->format('h:i A')
                    : null;
                $attendance->check_out = $attendance->check_out
                    ? Carbon::createFromFormat('H:i:s', $attendance->check_out)->format('h:i A')
                    : null;
            }
            // Calculate today's production hours
            $todayProductionHours = '--:--';
            if ($attendance && !empty($attendance->production_hours)) {
                [$h, $m, $s] = explode(':', $attendance->production_hours);
                $decimalHoursToday = ((int) $h) + ((int) $m / 60) + ((int) $s / 3600);
                $totalMinutesToday = round($decimalHoursToday * 60);
                $hoursToday = floor($totalMinutesToday / 60);
                $minutesToday = $totalMinutesToday % 60;
                $todayProductionHours = sprintf('%02d:%02d', $hoursToday, $minutesToday);
            }
            // Calculate Weekly Production Hours
            $weeklyAttendance = Attendance::where('box_user_id', $boxUser->id)
                ->whereBetween('attendance_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
                ->whereNotNull('production_hours')
                ->get();
            $totalDecimalHoursWeek = 0;
            foreach ($weeklyAttendance as $record) {
                if ($record->production_hours) {
                    [$h, $m, $s] = explode(':', $record->production_hours);
                    $decimalHours = ((int) $h) + ((int) $m / 60) + ((int) $s / 3600);
                    $totalDecimalHoursWeek += $decimalHours;
                }
            }
            $totalMinutesWeek = round($totalDecimalHoursWeek * 60);
            $hoursWeek = floor($totalMinutesWeek / 60);
            $minutesWeek = $totalMinutesWeek % 60;
            $weeklyProductionHours = sprintf('%02d:%02d', $hoursWeek, $minutesWeek);
            $monthlyAttendance = Attendance::where('box_user_id', $boxUser->id)
                ->whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
                ->where(function ($query) {
                    $query->whereNotNull('production_hours')
                        ->orWhereNotNull('over_time');
                })
                ->get();
            $totalDecimalHoursMonth = 0;
            $totalDecimalHoursOverTime = 0;
            foreach ($monthlyAttendance as $record) {
                if ($record->production_hours) {
                    [$h, $m, $s] = explode(':', $record->production_hours);
                    $decimalHours = ((int) $h) + ((int) $m / 60) + ((int) $s / 3600);
                    $totalDecimalHoursMonth += $decimalHours;
                }
                if ($record->over_time) {
                    [$h, $m, $s] = explode(':', $record->over_time);
                    $decimalHours = ((int) $h) + ((int) $m / 60) + ((int) $s / 3600);
                    $totalDecimalHoursOverTime += $decimalHours;
                }
            }
            $totalMinutesMonth = round($totalDecimalHoursMonth * 60);
            $hoursMonth = floor($totalMinutesMonth / 60);
            $minutesMonth = $totalMinutesMonth % 60;
            $monthlyProductionHours = sprintf('%02d:%02d', $hoursMonth, $minutesMonth);
            $totalMinutesOverTimeMonth = round($totalDecimalHoursOverTime * 60);
            $hoursOverTime = floor($totalMinutesOverTimeMonth / 60);
            $minutesOverTime = $totalMinutesOverTimeMonth % 60;
            $monthlyOverTime = sprintf('%02d:%02d', $hoursOverTime, $minutesOverTime);
            return response()->json([
                'success' => true,
                'data' => [
                    'attendance' => $attendance,
                    'start_time' => $startTime ? Carbon::parse($startTime)->format('h:i A') : null,
                    'end_time' => $endTime ? Carbon::parse($endTime)->format('h:i A') : null,
                    'daily_hours' => $dailyHours !== null ? $decimalHoursToHHMM($dailyHours) : '--:--',
                    'weekly_hours' => $weeklyHours,
                    'monthly_hours' => $monthlyHours,
                    'status' => $status,
                    'today_production_hours' => $todayProductionHours,
                    'weekly_production_hours' => $weeklyProductionHours,
                    'monthly_production_hours' => $monthlyProductionHours,
                    'monthly_production_overtime' => $monthlyOverTime,
                    'can_take_break' => $canTakeBreak,
                    'nextBreak' => $nextBreak,
                    'date' => $currentDate->format('d M Y'),
                    'total_working_days_week' => $totalWorkingDaysWeek,
                    'total_working_days_month' => $totalWorkingDaysMonth,
                    'total_overtime_hours' => $decimalHoursToHHMM($totalOverTimeHours),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    private function getWorkingDaysFromShift($shift): array
    {
        $allDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        $weeklyOffs = array_filter([
            $shift->weekly_off_1 ?? null,
            $shift->weekly_off_2 ?? null,
        ]);
        $weeklyOffs = array_values(array_unique(array_map('trim', array_filter($weeklyOffs))));
        return array_values(array_diff($allDays, $weeklyOffs));
    }
    // calculateWorkingDaysMonthsAndHours
    public function calculateWorkingDaysMonthsAndHours($startOfMonth, $endOfMonth, $shift, $userWorkingDays, $dailyHours)
    {
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

        if (is_string($dailyHours) && str_contains($dailyHours, ':')) {
            [$hours, $minutes] = explode(':', $dailyHours);
            $dailyHours = (float) $hours + ((float) $minutes / 60);
        }

        $halfDayHours = $dailyHours * 0.5;
        $weeklyOff1 = $shift->weekly_off_1 ?? null;
        $weeklyOff2 = $shift->weekly_off_2 ?? null;
        $weeklyOff3 = $shift->weekly_off_3 ?? null;
        $weeklyOff4 = $shift->weekly_off_4 ?? null;

        $fullDayWeeks = $shift->saturday_offs ?? [];      // e.g. ['1st', '3rd']
        $halfDayWeeks = $shift->saturday_half_days ?? []; // e.g. ['2nd', '4th']

        // Track counts
        $weekdayCounts = []; // Total times each day appears
        $off1Count = 0;
        $off2Count = 0;
        $off3Count = 0;
        $off4Count = 0;

        $totalDays = 0;
        $totalWorkingDays = 0.0;
        $totalHours = 0.0;

        foreach ($period as $date) {
            $totalDays++;

            $dayName = $date->format('l');
            $dayOfMonth = $date->day;
            $weekOfMonth = ceil($dayOfMonth / 7);           // 1 to 5
            $weekLabel = $this->getOrdinal($weekOfMonth); // "1st", "2nd", etc.
            $weekdayCounts[$dayName] = ($weekdayCounts[$dayName] ?? 0) + 1;
            if ($dayName === $weeklyOff1) {
                $off1Count++;
            }

            if ($dayName === $weeklyOff2) {
                $off2Count++;
            }

            if ($dayName === $weeklyOff3) {
                $off3Count++;
            }

            if ($dayName === $weeklyOff4) {
                $off4Count++;
            }

            // Skip if not a user's working day
            if (!in_array($dayName, $userWorkingDays)) {
                continue;
            }
            // Handle full-day weekly offs (1,2,3)
            if (in_array($dayName, [$weeklyOff1, $weeklyOff2, $weeklyOff3])) {
                continue;
            }
            // Handle half-day off (weekly_off_4)
            if ($dayName === $weeklyOff4) {
                $totalWorkingDays += 0.5;
                $totalHours += $halfDayHours;
                continue;
            }
            // Regular ful day
            $totalWorkingDays += 1;
            $totalHours += $dailyHours;
        }
        $fulldaysWorkign = count($fullDayWeeks);
        $halfDays = count($halfDayWeeks);
        $countAshalfdays = $halfDays * 0.5;

        $totalWorkingDaysInAmonths = $totalDays;
        $totalOffFays = $off1Count + $off2Count + $fulldaysWorkign + $countAshalfdays;
        $totaldaysWorkign = $totalWorkingDaysInAmonths - $totalOffFays;
        return ['working_days' => $totaldaysWorkign];
    }

    private function getOrdinal($number)
    {
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        } else {
            return $number . ($ends[$number % 10] ?? 'th');
        }
    }

    public function calculateWorkingDaysAndHours($startDate, $endDate, $shift, $userWorkingDays)
    {
        $period = CarbonPeriod::create($startDate, $endDate);
        $userTotalWorkingDays = count($userWorkingDays);
        $fullDayWeeks = $shift->saturday_offs ?? [];
        $halfDayWeeks = $shift->saturday_half_days ?? [];
        $totalWorkingDays = 0.0;
        $workingSaturdays = [];
        foreach ($period as $date) {
            if ($date->format('l') !== 'Saturday') {
                continue;
            }
            $weekOfMonth = ceil($date->day / 7);
            $weekLabel = ['1st', '2nd', '3rd', '4th', '5th'][$weekOfMonth - 1] ?? '';
            if (in_array($weekLabel, $fullDayWeeks)) {
                $totalWorkingDays -= 1;
                $workingSaturdays[] = $date->format('Y-m-d') . ' (Full Day)';
            } elseif (in_array($weekLabel, $halfDayWeeks)) {
                $totalWorkingDays -= 0.5;
                $workingSaturdays[] = $date->format('Y-m-d') . ' (Half Day)';
            }
        }
        return [
            'working_days' => $userTotalWorkingDays + $totalWorkingDays,
            'working_saturdays' => $workingSaturdays,
        ];
    }

    public function countWeeklyOffOccurrences($startDate, $endDate, $weeklyOffs)
    {
        $period = CarbonPeriod::create($startDate, $endDate);
        $occurrences = [];
        foreach ($weeklyOffs as $dayName) {
            $occurrences[$dayName] = 0;
        }
        foreach ($period as $date) {
            $dayName = $date->format('l');
            if (in_array($dayName, $weeklyOffs)) {
                $occurrences[$dayName]++;
            }
        }

        return $occurrences;
    }

    public function fetchAllAttendanceRecords(Request $request)
    {
        try {
            $user = Auth::user();
            $boxUser = BoxUser::where('user_id', $user->id)->firstOrFail();
            $today = Carbon::today()->toDateString();

            $query = Attendance::with(['boxUser.shift', 'corrections.break', 'machine'])
                ->where('box_user_id', $boxUser->id)
                ->whereDate('attendance_date', '<=', $today);

            //Apply filters (from frontend)
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('status_correction')) {
                $query->whereHas('corrections', function ($q) use ($request) {
                    $q->where('status', $request->status_correction);
                });
            }

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereBetween('attendance_date', [
                    Carbon::parse($request->from_date)->startOfDay(),
                    Carbon::parse($request->to_date)->endOfDay(),
                ]);
            }

            if ($request->filled('sort_by')) {
                switch ($request->sort_by) {
                    case 'latest':
                        $query->latest('attendance_date');
                        break;
                    case 'oldest':
                        $query->oldest('attendance_date');
                        break;
                    case 'present':
                        $query->where('status', 'present')->latest('attendance_date');
                        break;
                    case 'absent':
                        $query->where('status', 'absent')->latest('attendance_date');
                        break;
                    default:
                        $query->latest('attendance_date');
                }
            } else {
                $query->latest('attendance_date');
            }

            // DataTables formatting
            return DataTables::eloquent($query)
                ->addColumn('raw_date', fn($row) => Carbon::parse($row->attendance_date)->format('Y-m-d'))
                ->addColumn('date', fn($row) => Carbon::parse($row->attendance_date)->format('d M Y'))
                ->addColumn('check_in', fn($row) => $row->check_in ? Carbon::parse($row->check_in)->format('h:i A') : '--')
                ->addColumn('status', function ($row) {
                    $status = strtolower($row->status ?? 'N/A');
                    return $status;
                })
                ->addColumn('corrections_statuses', function ($row) {
                    if ($row->corrections->isEmpty()) {
                        return '<span class="">N/A</span>';
                    }
                    return $row->corrections->map(function ($correction) {
                        $badgeClass = match ($correction->status) {
                            'approved' => 'bg-success',
                            'pending' => 'bg-warning text-dark',
                            'rejected' => 'bg-danger',
                            default => 'bg-secondary',
                        };
                        return '<span class="badge ' . $badgeClass . '">' . ucfirst($correction->status) . '</span>';
                    })->implode(' ');
                })
                ->addColumn('check_out', fn($row) => $row->check_out ? Carbon::parse($row->check_out)->format('h:i A') : '--')
                ->addColumn('break', fn($row) => $row->break_minutes ? $row->break_minutes . ' Min' : '-')
                ->addColumn('late', fn($row) => $row->late_minutes ? $row->late_minutes . ' Min' : '-')
                ->addColumn('overtime', fn($row) => $row->overtime_minutes ? $row->overtime_minutes . ' Min' : '-')
                ->addColumn('shift', function ($row) {
                    return $row->boxUser && $row->boxUser->shift
                        ? $row->boxUser->shift->name ?? 'Unnamed Shift'
                        : 'No Shift';
                })
                ->addColumn('machine', function ($row) {
                    return $row->machine ? $row->machine->serial_no : '--';
                })
                ->addColumn('production_hours', function ($row) {
                    if (!empty($row->production_hours)) {
                        [$h, $m, $s] = explode(':', $row->production_hours);
                        $totalMinutes = ((int) $h * 60) + (int) $m;
                        $badge = $totalMinutes >= (8.5 * 60) ? 'badge-success' : 'badge-danger';
                        $formatted = intval($h) . ' Hrs ' . intval($m) . ' Min';
                    } else {
                        $badge = 'badge-secondary';
                        $formatted = '--';
                    }
                    return '<span class="badge ' . $badge . ' d-inline-flex align-items-center">
                            <i class="ti ti-clock-hour-11 me-1"></i>' . $formatted . '
                        </span>';
                })
                ->rawColumns(['status', 'production_hours', 'corrections_statuses'])
                ->make(true);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function mark_attendace(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'nullable|string',
                'attendance_date' => 'nullable|date',
                'punch' => 'required|string|in:in,out',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'location' => 'nullable|string',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            $authUser = Auth::user();
            $boxUser = BoxUser::where('user_id', $authUser->id)->with('shift', 'box')->firstOrFail();
            $shift = $boxUser->shift;
            if (!$shift) {
                $startTime = null;
                $endTime = null;

                if (!empty($boxUser->day_start_time) && !empty($boxUser->day_end_time)) {
                    $startTime = Carbon::parse($boxUser->day_start_time);
                    $endTime = Carbon::parse($boxUser->day_end_time);
                } elseif ($boxUser->box && !empty($boxUser->box->day_start_time) && !empty($boxUser->box->day_end_time)) {
                    $startTime = Carbon::parse($boxUser->box->day_start_time);
                    $endTime = Carbon::parse($boxUser->box->day_end_time);
                }

                if (!$startTime || !$endTime) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Shift and fallback times are not defined.',
                    ], 422);
                }
                if ($endTime->lt($startTime)) {
                    $endTime->addDay();
                }
                $diffInSeconds = $startTime->diffInSeconds($endTime);
                $shiftHours = gmdate('H:i:s', $diffInSeconds);
                $shift = (object) [
                    'shift_hours' => $shiftHours,
                    'short_break_hours' => '00:00:00',
                    'check_over_time' => false,
                    'max_over_time' => null,
                ];
            }
            $currentDay = ucfirst(strtolower(Carbon::now()->format('l')));
            $isWeeklyOff = false;
            $shift = $boxUser->shift ?? null;
            $checkAllowedWebPuch = $boxUser->work_mode_id;
            if ($checkAllowedWebPuch != null) {
                $checkDays = is_string($boxUser->working_web_days) 
                    ? json_decode($boxUser->working_web_days, true) 
                    : $boxUser->working_web_days;
                $checkDays = is_array($checkDays) ? $checkDays : [];
                if (in_array($currentDay, $checkDays)) {
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Web punch-in is not permitted for you today ($currentDay). Please contact the admin if you require access.",
                    ], 403);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Web punch-in is not allowed for your account. Please contact the admin to enable Web Punch In so you can log in for web punch.',
                ], 403);
            }
            if ($shift) {
                $weeklyOffDays = array_filter([
                    $shift->weekly_off_1 ?? null,
                    $shift->weekly_off_2 ?? null,
                    $shift->weekly_off_3 ?? null,
                ]);
                if (in_array($currentDay, $weeklyOffDays, true)) {
                    $isWeeklyOff = true;
                }
                $checkIsEarlyLogin = $shift->early_login_punchIn;
                $shiftStartTime = $shift->in_time;
                $currentTime = Carbon::now();
                $shiftStart = Carbon::createFromFormat('H:i:s', $shiftStartTime);

                if ($checkIsEarlyLogin == 'no') {
                    $earlyLogin = $shift->early_login_minutes ?? '00:00:00';

                    $parts = explode(':', $earlyLogin);
                    $hours = (int) ($parts[0] ?? 0);
                    $minutes = (int) ($parts[1] ?? 0);
                    $seconds = (int) ($parts[2] ?? 0);
                    $shiftStart = $shiftStart->subHours($hours)->subMinutes($minutes)->subSeconds($seconds);
                }

                if ($currentTime->lt($shiftStart)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You cannot punch in before your allowed time (' . $shiftStart->format('H:i:s') . ').',
                    ], 403);
                }

            } else {
                // Fallback: check boxUser working days or box working days
                if (!empty($boxUser->working_days)) {
                    $userWorkingDays = is_array($boxUser->working_days)
                        ? $boxUser->working_days
                        : (json_decode($boxUser->working_days, true) ?? explode(',', $boxUser->working_days));
                    $userWorkingDays = array_map('trim', $userWorkingDays);

                    if (!in_array($currentDay, $userWorkingDays, true)) {
                        $isWeeklyOff = true;
                    }
                }
                if (!$isWeeklyOff && $boxUser->box && !empty($boxUser->box->working_days)) {
                    $boxWorkingDays = is_array($boxUser->box->working_days)
                        ? $boxUser->box->working_days
                        : (json_decode($boxUser->box->working_days, true) ?? explode(',', $boxUser->box->working_days));
                    $boxWorkingDays = array_map('trim', $boxWorkingDays);

                    if (!in_array($currentDay, $boxWorkingDays, true)) {
                        $isWeeklyOff = true;
                    }
                }
            }
            $attendance_date = $request->input('attendance_date', Carbon::now()->toDateString());
            $holiday = Holiday::with('holidayType')
                ->where('box_id', $boxUser->box_id)
                ->whereDate('date', $attendance_date)
                ->get()
                ->filter(function ($h) use ($boxUser) {
                    $calcInCalendar = 'No';
                    if ($h->holidayType) {
                        $calcInCalendar = $h->holidayType->calculate_in_calendar;
                    } elseif ($h->holiday_type_id) {
                        $hType = \App\Models\HolidayType::find($h->holiday_type_id);
                        if ($hType) {
                            $calcInCalendar = $hType->calculate_in_calendar;
                        }
                    } elseif ($h->holiday_type) {
                        $hType = \App\Models\HolidayType::where('box_id', $boxUser->box_id)
                            ->where('holiday_type', $h->holiday_type)
                            ->first();
                        if ($hType) {
                            $calcInCalendar = $hType->calculate_in_calendar;
                        }
                    }
                    return strcasecmp($calcInCalendar, 'No') === 0;
                })->first();
            $isHoliday = (bool) $holiday;

            $attendanceStatus = $request->input('status', 'Present');

            if ($isWeeklyOff) {
                $attendanceStatus = 'Comp Off';
            } elseif ($holiday) {
                $holidayType = $holiday->holiday_type;
                if (!$holidayType && $holiday->holidayType) {
                    $holidayType = $holiday->holidayType->name;
                }
                if (strtoupper($holidayType) === 'RH' || strtoupper($holidayType) === 'RESTRICTED HOLIDAY') {
                    $attendanceStatus = 'Present';
                } else {
                    $attendanceStatus = 'Comp Off';
                }
            }
            $currentDateTime = Carbon::now();
            $attendance_date = $request->input('attendance_date', $currentDateTime->toDateString());
            $punch = $request->input('punch');
            $attendance = Attendance::where('box_user_id', $boxUser->id)
                ->whereDate('attendance_date', $attendance_date)
                ->first();

            if ($punch === 'in') {
                if ($attendance && $attendance->check_in) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Already punched in today.',
                    ]);
                }

                $checkInTime = $currentDateTime->toTimeString();
                $expectedStartTime = $shift->in_time ?? $boxUser->day_start_time ?? optional($boxUser->box)->day_start_time;
                $lateDuration = null;

                if ($expectedStartTime) {
                    try {
                        $cleanExpectedStartTime = Carbon::parse($expectedStartTime)->format('H:i:s');
                        $expectedDateTime = Carbon::parse($attendance_date . ' ' . $cleanExpectedStartTime);
                        if ($currentDateTime->gt($expectedDateTime)) {
                            $lateSeconds = $expectedDateTime->diffInSeconds($currentDateTime);
                            $lateDuration = gmdate('H:i:s', $lateSeconds);
                        }
                    } catch (\Exception $e) {
                        $lateDuration = null;
                    }
                }

                if ($attendance) {
                    $attendance->update([
                        'check_in' => $checkInTime,
                        'status' => $attendanceStatus,
                        'updated_by' => $authUser->id,
                        'late_duration' => $lateDuration,
                        'check_attendace_by' => 'web',
                        'punch_in_latitude' => $request->input('latitude'),
                        'punch_in_longitude' => $request->input('longitude'),
                        'punch_in_location' => $request->input('location'),
                    ]);
                } else {
                    $attendance = Attendance::create([
                        'box_user_id' => $boxUser->id,
                        'box_id' => $boxUser->box_id,
                        'status' => $attendanceStatus,
                        'late_duration' => $lateDuration,
                        'attendance_date' => $attendance_date,
                        'check_in' => $checkInTime,
                        'created_by' => $authUser->id,
                        'check_attendace_by' => 'web',
                        'punch_in_latitude' => $request->input('latitude'),
                        'punch_in_longitude' => $request->input('longitude'),
                        'punch_in_location' => $request->input('location'),
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => $isWeeklyOff ? 'Punch In successful (Comp Off).' : 'Punch In successful.',
                    'attendance' => $attendance,
                    'weekly_off' => $isWeeklyOff,
                ]);
            }

            //  Punch Out
            // =======================
//    PUNCH OUT SECTION
// =======================
            if ($punch === 'out') {

                if (!$attendance || !$attendance->check_in) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot punch out without punching in first.',
                    ]);
                }

                if ($attendance->check_out) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Already punched out today.',
                    ]);
                }

                $cleanCheckInTime = Carbon::parse($attendance->check_in)->format('H:i:s');
                $checkInDateTime = Carbon::parse($attendance->attendance_date . ' ' . $cleanCheckInTime);
                $checkOutDateTime = $currentDateTime;

                if ($checkOutDateTime->lt($checkInDateTime)) {
                    $checkOutDateTime->addDay();
                }

                // Total working seconds
                $totalWorkedSeconds = $checkInDateTime->diffInSeconds($checkOutDateTime);

                // Breaks calculation
                $breaks = AttendanceBreak::where('attendance_id', $attendance->id)->get();
                $totalBreakSeconds = 0;

                foreach ($breaks as $break) {
                    if ($break->break_start && $break->break_end) {
                        $start = Carbon::parse($break->break_start);
                        $end = Carbon::parse($break->break_end);

                        if ($end->lt($start)) {
                            $end->addDay();
                        }

                        $totalBreakSeconds += $start->diffInSeconds($end);
                    }
                }

                // Allowed break seconds
                $allowedBreakSeconds = 0;
                try {
                    if ($shift->rest_enabled && $shift->lunch_hours) {
                        $allowedBreakSeconds += CarbonInterval::createFromFormat('H:i:s', $shift->lunch_hours)->totalSeconds;
                    }
                    if ($shift->short_break_enabled && $shift->short_break_hours) {
                        $allowedBreakSeconds += CarbonInterval::createFromFormat('H:i:s', $shift->short_break_hours)->totalSeconds;
                    }
                    if ($shift->short_break_enabled_2 && $shift->short_break_hours_2) {
                        $allowedBreakSeconds += CarbonInterval::createFromFormat('H:i:s', $shift->short_break_hours_2)->totalSeconds;
                    }
                } catch (\Exception $e) {
                }

                $breakDifference = $totalBreakSeconds - $allowedBreakSeconds;
                $netWorkedSeconds = $totalWorkedSeconds - max(0, $breakDifference);
                $shiftWorkSeconds = 0;
                try {
                    $shiftWorkSeconds = $shift->shift_hours
                        ? CarbonInterval::createFromFormat('H:i:s', $shift->shift_hours)->totalSeconds
                        : 0;
                } catch (\Exception $e) {
                }
                $overtimeSeconds = 0;
                $finalProductionSeconds = $netWorkedSeconds;

                if ($netWorkedSeconds >= $shiftWorkSeconds && !empty($shift->check_over_time)) {
                    $extraSeconds = $netWorkedSeconds - $shiftWorkSeconds;

                    if (!is_null($shift->max_over_time)) {
                        $maxOvertimeSeconds = $shift->max_over_time * 60;
                        $overtimeSeconds = min($extraSeconds, $maxOvertimeSeconds);
                    } else {
                        $overtimeSeconds = $extraSeconds;
                    }
                    $finalProductionSeconds = $shiftWorkSeconds + $overtimeSeconds;
                } else {
                    $finalProductionSeconds = min($netWorkedSeconds, $shiftWorkSeconds);
                }
                $expectedEndTime = $shift->out_time ?? $boxUser->day_end_time ?? optional($boxUser->box)->day_end_time;
                $earlyCheckOut = null;
                if ($expectedEndTime) {
                    $cleanExpectedEndTime = Carbon::parse($expectedEndTime)->format('H:i:s');
                    $expectedEndDateTime = Carbon::parse($attendance_date . ' ' . $cleanExpectedEndTime);

                    if ($checkOutDateTime->lt($expectedEndDateTime)) {
                        $earlySeconds = $checkOutDateTime->diffInSeconds($expectedEndDateTime);
                        $earlyCheckOut = gmdate('H:i:s', $earlySeconds);
                    }
                }
                if ($isWeeklyOff || $isHoliday) {
                    $attendance->status = 'Comp Off';
                    $creditAmount = 0;
                    try {
                        if ($shift) {
                            $absentBelowSeconds = $shift->absent_below ? CarbonInterval::createFromFormat('H:i:s', $shift->absent_below)->totalSeconds : 0;
                            $halfDayBelowSeconds = $shift->half_day_below ? CarbonInterval::createFromFormat('H:i:s', $shift->half_day_below)->totalSeconds : 0;
                            $shortLeaveBelowSeconds = $shift->short_leave_below ? CarbonInterval::createFromFormat('H:i:s', $shift->short_leave_below)->totalSeconds : 0;
                            $shiftHoursSeconds = $shift->shift_hours ? CarbonInterval::createFromFormat('H:i:s', $shift->shift_hours)->totalSeconds : 0;
                            $relaxationSeconds = ($shift->relaxation_time ?? '00:00:00') ? CarbonInterval::createFromFormat('H:i:s', $shift->relaxation_time ?? '00:00:00')->totalSeconds : 0;
                            $adjustedShortLeaveThreshold = max(0, $shortLeaveBelowSeconds - $relaxationSeconds);
                            if ($finalProductionSeconds >= $shiftHoursSeconds) {
                                $creditAmount = 1.0;
                            } elseif ($finalProductionSeconds < $absentBelowSeconds) {
                                $creditAmount = 0;
                            } elseif ($finalProductionSeconds < $halfDayBelowSeconds) {
                                $deduction = $shift->half_day_deduction ?? 50;
                                $creditAmount = max(0, 1.0 - ($deduction / 100));
                            } elseif ($finalProductionSeconds < $shortLeaveBelowSeconds) {
                                $deduction = $shift->short_leave_deduction ?? 25;
                                $creditAmount = max(0, 1.0 - ($deduction / 100));
                            } else {
                                $creditAmount = 1.0;
                            }
                        } else {
                            if ($finalProductionSeconds >= 8 * 3600) {
                                $creditAmount = 1.0;
                            } elseif ($finalProductionSeconds >= 4 * 3600) {
                                $creditAmount = 0.5;
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error("Error calculating Comp Off credit: " . $e->getMessage());
                        $creditAmount = $finalProductionSeconds >= (4 * 3600) ? 0.5 : 0;
                        if ($finalProductionSeconds >= (8 * 3600))
                            $creditAmount = 1.0;
                    }

                    if ($creditAmount > 0) {
                        $compOffType = LeaveType::where('status', 'Active')
                            ->where('box_id', $boxUser->box_id)
                            ->where(function ($q) {
                                $q->where('leave_type', 'Comp Off')
                                    ->orWhere('leave_type', 'Compensatory Off Leave')
                                    ->orWhere('leave_type', 'like', 'Comp%Off%');
                            })
                            ->first();

                        if ($compOffType) {
                            $alreadyCredited = LeaveTransaction::where('box_user_id', $boxUser->id)
                                ->where('leave_type_id', $compOffType->id)
                                ->whereDate('date', $attendance_date)
                                ->exists();

                            if (!$alreadyCredited) {
                                $latestTxn = LeaveTransaction::where('box_user_id', $boxUser->id)
                                    ->where('leave_type_id', $compOffType->id)
                                    ->latest()
                                    ->first();

                                $previousBalance = $latestTxn->balance_leave ?? 0;
                                LeaveTransaction::create([
                                    'box_id' => $boxUser->box_id,
                                    'box_user_id' => $boxUser->id,
                                    'leave_type_id' => $compOffType->id,
                                    'date' => $attendance_date,
                                    'credit_leave' => $creditAmount,
                                    'balance_leave' => $previousBalance + $creditAmount,
                                    'total_leave' => $creditAmount,
                                    'transaction_remarks' => 'Comp Off earned (' . $creditAmount . ') for working on ' .
                                        ($isHoliday ? 'Holiday' : 'Weekly Off') . ' (' . $currentDay . ')',
                                    'status' => 'Approved',
                                    'created_by' => $authUser->id,
                                    'applied_date' => $attendance_date,
                                    'approved_date' => now(),
                                ]);
                            }
                        }
                    }
                }

                if ($boxUser->shift) {
                    $attendance->production_hours = gmdate('H:i:s', $finalProductionSeconds);
                    if (!($isWeeklyOff || $isHoliday)) {
                        $finalStatus = $this->calculateStatus($attendance);
                    } else {
                        $finalStatus = 'Comp Off';
                    }
                } else {
                    $finalStatus = $attendance->status;
                }

                // Update final data
                $attendance->update([
                    'check_out' => $checkOutDateTime->toTimeString(),
                    'production_hours' => gmdate('H:i:s', $finalProductionSeconds),
                    'over_time' => gmdate('H:i:s', $overtimeSeconds),
                    'working_hours' => gmdate('H:i:s', $totalWorkedSeconds),
                    'early_check_out' => $earlyCheckOut ?? '00:00:00',
                    'status' => $finalStatus,
                    'updated_by' => $authUser->id,
                    'check_attendace_by' => 'web',
                    'punch_out_latitude' => $request->input('latitude'),
                    'punch_out_longitude' => $request->input('longitude'),
                    'punch_out_location' => $request->input('location'),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => ($isWeeklyOff || $isHoliday) ? 'Punch Out successful (Comp Off).' : 'Punch Out successful.',
                    'attendance' => $attendance,
                    'production_hours' => gmdate('H:i:s', $finalProductionSeconds),
                    'actual_worked_time' => gmdate('H:i:s', $netWorkedSeconds),
                    'overtime' => gmdate('H:i:s', $overtimeSeconds),
                    'total_break_time' => gmdate('H:i:s', $totalBreakSeconds),
                    'break_difference' => gmdate('H:i:s', abs($breakDifference)),
                    'weekly_off' => $isWeeklyOff,
                    'is_holiday' => $isHoliday,
                    'credit_amount' => ($isWeeklyOff || $isHoliday) ? ($creditAmount ?? 0) : 0,
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Invalid punch type.',
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function take_break(Request $request)
    {
        try {
            $validated = $request->validate([
                'attendance_id' => 'required|integer|exists:attendances,id',
                'action' => 'required|string|in:start,end',
            ]);

            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->select('id', 'box_id')->first();

            if (!$boxUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Box user record not found for authenticated user.',
                ], 404);
            }

            $boxUserId = $boxUser->id;
            $boxId = $boxUser->box_id;
            $attendanceId = $validated['attendance_id'];
            $action = $validated['action'];

            if ($action === 'start') {
                $activeBreak = AttendanceBreak::where('attendance_id', $attendanceId)
                    ->where('box_user_id', $boxUserId)
                    ->whereNull('break_end')
                    ->first();

                if ($activeBreak) {
                    return response()->json([
                        'success' => false,
                        'message' => 'An active break is already in progress.',
                    ], 409);
                }

                $break = AttendanceBreak::create([
                    'attendance_id' => $attendanceId,
                    'box_user_id' => $boxUserId,
                    'box_id' => $boxId,
                    'break_start' => now(),
                    'break_status' => true,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Break started.',
                    'break' => $break,
                ]);
            }

            if ($action === 'end') {
                $break = AttendanceBreak::where('attendance_id', $attendanceId)
                    ->where('box_user_id', $boxUserId)
                    ->whereNull('break_end')
                    ->latest('break_start')
                    ->first();

                if (!$break) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No active break found to end.',
                    ], 404);
                }

                $start = Carbon::parse($break->break_start);
                $end = now();
                $totalSeconds = $start->diffInSeconds($end);
                $formatted = gmdate('H:i:s', $totalSeconds);

                $break->update([
                    'break_end' => $end,
                    'duration' => $formatted,
                    'break_status' => false,
                ]);

                // Optional: update break_duration on attendance record
                Attendance::where('id', $attendanceId)
                    ->update(['break_duration' => $formatted]);

                return response()->json([
                    'success' => true,
                    'message' => 'Break ended.',
                    'break' => $break,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid action.',
            ], 400);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process break.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function attendace_admin_stats(Request $request)
    {
        try {
            $user = Auth::user();
            $boxUser = BoxUser::where('user_id', $user->id)->first();

            if (!$boxUser) {
                $box = Box::where('superadmin_id', $user->id)->first();
                if (!$box) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Box not found for the authenticated user.',
                    ]);
                }
                $boxId = $box->id;
            } else {
                $boxId = $boxUser->box_id;
            }

            $scope = $request->get('scope');
            $allSubordinateIds = null;
            
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'team_admin');

            if ($scope === 'team' && $boxUser && !$isGlobalAdmin && !$isBoxAdmin) {
                if ($canRead) {
                    $allSubordinateIds = BoxUser::getAllSubordinateIds($boxId, $user->id, $boxUser);
                } else {
                    return response()->json([
                        'status' => 403,
                        'message' => 'You do not have permission to access team attendance.'
                    ]);
                }
            }

            $totalUsersCount = BoxUser::where('box_id', $boxId);
            if ($allSubordinateIds !== null) {
                $totalUsersCount->whereIn('id', $allSubordinateIds);
            }
            $totalBoxUsers = $totalUsersCount->count();

            $today = Carbon::today()->toDateString();
            $yesterday = Carbon::yesterday()->toDateString();

            // Helper to get attendance counts
            $getAttendanceCount = function ($date, $status = null, $isLate = false) use ($boxId, $allSubordinateIds) {
                $q = Attendance::where('box_id', $boxId)->whereDate('attendance_date', $date);
                if ($allSubordinateIds !== null) {
                    $q->whereIn('box_user_id', $allSubordinateIds);
                }
                if ($status) {
                    $q->whereRaw('LOWER(status) = ?', [strtolower($status)]);
                }
                if ($isLate) {
                    $q->whereNotNull('late_duration');
                }
                return $q->count();
            };

            // Attendance counts
            $todayPresent = $getAttendanceCount($today, 'present');
            $todayAbsent = $getAttendanceCount($today, 'absent');
            $yesterdayPresent = $getAttendanceCount($yesterday, 'present');
            $yesterdayAbsent = $getAttendanceCount($yesterday, 'absent');

            // Helper to get leave counts
            $getLeaveCount = function ($date, $status) use ($boxId, $allSubordinateIds) {
                $q = LeaveRequest::where('box_id', $boxId)
                    ->where('status', $status)
                    ->whereDate('from_date', '<=', $date)
                    ->whereDate('to_date', '>=', $date);
                if ($allSubordinateIds !== null) {
                    $q->whereIn('box_user_id', $allSubordinateIds);
                }
                return $q->count();
            };

            $todayApprovedLeaves = $getLeaveCount($today, 'approved');
            $yesterdayApprovedLeaves = $getLeaveCount($yesterday, 'approved');

            // Unapproved Leave Requests (based on existing logic which counts absents as unapproved leaves)
            $todayUnapprovedLeaves = $todayAbsent;
            $yesterdayUnapprovedLeaves = $yesterdayAbsent;

            $todayLateLogins = $getAttendanceCount($today, null, true);
            $yesterdayLateLogins = $getAttendanceCount($yesterday, null, true);

            $lateLoginDiff = $yesterdayLateLogins > 0
                ? round((($todayLateLogins - $yesterdayLateLogins) / $yesterdayLateLogins) * 100, 2)
                : ($todayLateLogins > 0 ? 100 : 0);

            $presentDiff = $yesterdayPresent > 0
                ? round((($todayPresent - $yesterdayPresent) / $yesterdayPresent) * 100, 2)
                : ($todayPresent > 0 ? 100 : 0);

            $absentDiff = $yesterdayAbsent > 0
                ? round((($todayAbsent - $yesterdayAbsent) / $yesterdayAbsent) * 100, 2)
                : ($todayAbsent > 0 ? 100 : 0);

            $approvedLeaveDiff = $yesterdayApprovedLeaves > 0
                ? round((($todayApprovedLeaves - $yesterdayApprovedLeaves) / $yesterdayApprovedLeaves) * 100, 2)
                : ($todayApprovedLeaves > 0 ? 100 : 0);

            $unapprovedLeaveDiff = $yesterdayUnapprovedLeaves > 0
                ? round((($todayUnapprovedLeaves - $yesterdayUnapprovedLeaves) / $yesterdayUnapprovedLeaves) * 100, 2)
                : ($todayUnapprovedLeaves > 0 ? 100 : 0);

            return response()->json([
                'status' => 200,
                'data' => [
                    'box_id' => $boxId,
                    'total_users' => $totalBoxUsers,
                    'today' => [
                        'present' => $todayPresent,
                        'absent' => $todayAbsent,
                        'approved_leaves' => $todayApprovedLeaves,
                        'unapproved_leaves' => $todayUnapprovedLeaves,
                        'late_logins' => $todayLateLogins,
                    ],
                    'yesterday' => [
                        'present' => $yesterdayPresent,
                        'absent' => $yesterdayAbsent,
                        'approved_leaves' => $yesterdayApprovedLeaves,
                        'unapproved_leaves' => $yesterdayUnapprovedLeaves,
                        'late_logins' => $yesterdayLateLogins,
                    ],
                    'diff_percent' => [
                        'present' => $presentDiff,
                        'absent' => $absentDiff,
                        'approved_leaves' => $approvedLeaveDiff,
                        'unapproved_leaves' => $unapprovedLeaveDiff,
                        'late_logins' => $lateLoginDiff,
                    ]
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Error fetching attendance stats',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function attendace_employee(Request $request)
    {
        try {
            $user = Auth::id();

            $boxUser = BoxUser::where('user_id', $user)->select('id', 'box_id')->first();
            if (!$boxUser) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Box user not found.',
                ]);
            }

            $boxId = $boxUser->box_id;
            $today = Carbon::today()->toDateString();
            
            $query = Attendance::with([
                'boxUser.user',
                'corrections.break',
                'boxUser.departmentRelation',
                'boxUser.designationRelation',
                'boxUser.shift',
                'boxUser.box',
                'machine',
            ])
                ->where('attendances.box_id', $boxId)
                ->where('attendances.attendance_date', '<=', $today . ' 23:59:59')
                ->orderBy('attendances.attendance_date', 'desc')
                ->orderBy('attendances.id', 'desc');

            // Apply filters
            if ($request->filled('from_date') && $request->filled('to_date')) {
                try {
                    // Try multiple date formats
                    $dateFormats = ['m/d/Y', 'Y-m-d', 'd/m/Y', 'm-d-Y', 'Y/m/d'];
                    $from = null;
                    $to = null;

                    foreach ($dateFormats as $format) {
                        try {
                            $from = Carbon::createFromFormat($format, $request->from_date);
                            $to = Carbon::createFromFormat($format, $request->to_date);
                            break;
                        } catch (\Exception $e) {
                            continue;
                        }
                    }

                    if ($from && $to) {
                        $query->whereBetween('attendance_date', [$from->startOfDay(), $to->endOfDay()]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error parsing date filters: ' . $e->getMessage(), [
                        'from_date' => $request->from_date,
                        'to_date' => $request->to_date
                    ]);
                }
            }

            if ($request->filled('department')) {
                $query->whereHas('boxUser', function ($q) use ($request) {
                    $q->where('department', $request->department);
                });
            }

            if ($request->filled('status')) {
                $query->whereRaw('LOWER(status) = ?', [strtolower($request->status)]);
            }

            if ($request->filled('status_correction')) {
                $statusCorrection = strtolower($request->status_correction);
                $query->whereHas('corrections', function ($q) use ($statusCorrection) {
                    if ($statusCorrection === 'approved') {
                        $q->whereRaw('LOWER(status) = ?', ['approved']);
                    } else {
                        $q->whereRaw('LOWER(status) = ?', [$statusCorrection]);
                    }
                });
            }

            /**
             * Update attendance statuses if:
             * - Date is not today => immediately update
             * - If today => compare with end time
             */
            $attendanceToUpdate = [];

            $attendanceItems = (clone $query)
                ->where('attendances.attendance_date', '>=', now()->subDays(7)->startOfDay())
                ->whereNull('status_updated_at')
                ->limit(100)
                ->get();

            foreach ($attendanceItems as $attendance) {
                if (!$attendance || !$attendance->boxUser) {
                    continue;
                }

                $attendanceDate = Carbon::parse($attendance->attendance_date)->toDateString();
                if ($attendanceDate !== $today) {
                    $attendanceToUpdate[] = $attendance->id;
                    continue;
                }
                $shiftEnd = optional($attendance->boxUser->shift)->end_time ?? null;
                $userEndTime = $attendance->boxUser->day_end_time ?? null;
                $boxEndTime = $attendance->boxUser->box->day_end_time ?? null;
                $endTime = $shiftEnd ?? $userEndTime ?? $boxEndTime;
                if (!$endTime) {
                    continue;
                }

                try {
                    $endTime = Carbon::parse($endTime)->format('H:i:s');
                } catch (\Exception $e) {
                    // ignore
                }

                $fullEndDateTime = Carbon::parse($attendanceDate . ' ' . $endTime);
                if (now()->greaterThanOrEqualTo($fullEndDateTime)) {
                    $attendanceToUpdate[] = $attendance->id;
                }
            }

            // Dispatch update job for qualified attendance IDs
            if (!empty($attendanceToUpdate)) {
                Attendance::whereIn('id', $attendanceToUpdate)->update(['status_updated_at' => now()]);
                UpdateAttendanceStatuses::dispatchAfterResponse($attendanceToUpdate);
            }

            // Return DataTable response
            return DataTables::eloquent($query)
                ->addColumn('user_name', function ($attendance) {
                    return $attendance->boxUser && $attendance->boxUser->user
                        ? ($attendance->boxUser->user->name ?? 'N/A')
                        : 'N/A';
                })
                ->addColumn('email', function ($attendance) {
                    return $attendance->boxUser && $attendance->boxUser->user
                        ? ($attendance->boxUser->user->email ?? 'N/A')
                        : 'N/A';
                })
                ->addColumn('profile_image', function ($attendance) {
                    if (!$attendance->boxUser)
                        return null;
                    $userProfileImage = $attendance->boxUser->user->profile_image_path ?? null;
                    $boxProfileImage = $attendance->boxUser->profile_image_path ?? null;
                    return $userProfileImage
                        ? asset('storage/' . $userProfileImage)
                        : ($boxProfileImage ? asset('storage/' . $boxProfileImage) : null);
                })
                ->addColumn('profile_image_url', function ($attendance) {
                    if (!$attendance->boxUser)
                        return null;
                    $userProfileImage = $attendance->boxUser->user->profile_image_path ?? null;
                    $boxProfileImage = $attendance->boxUser->profile_image_path ?? null;
                    return $userProfileImage
                        ? asset('storage/' . $userProfileImage)
                        : ($boxProfileImage ? asset('storage/' . $boxProfileImage) : null);
                })
                ->addColumn('corrections_statuses', function ($attendance) {
                    return $attendance->corrections ? $attendance->corrections->toArray() : [];
                })
                ->addColumn('box_user', function ($attendance) {
                    if (!$attendance->boxUser) {
                        return [
                            'id' => null,
                            'employee_id' => null,
                            'designation_relation' => ['name' => null],
                            'department_relation' => ['name' => null],
                            'shift' => null
                        ];
                    }
                    return [
                        'id' => $attendance->boxUser->id ?? null,
                        'employee_id' => $attendance->boxUser->employee_id ?? null,
                        'designation_relation' => [
                            'name' => ($attendance->boxUser->designationRelation->name ?? null)
                        ],
                        'department_relation' => [
                            'name' => ($attendance->boxUser->departmentRelation->name ?? null)
                        ],
                        'shift' => $attendance->boxUser->shift ? [
                            'id' => $attendance->boxUser->shift->id ?? null,
                            'code' => $attendance->boxUser->shift->code ?? null
                        ] : null
                    ];
                })
                ->editColumn('status', function ($attendance) {
                    $status = strtolower($attendance->status ?? 'N/A');
                    return $status;
                })
                ->editColumn('attendance_date', function ($attendance) {
                    try {
                        return $attendance->attendance_date
                            ? Carbon::parse($attendance->attendance_date)->format('Y-m-d H:i:s')
                            : 'N/A';
                    } catch (\Exception $e) {
                        return $attendance->attendance_date ?? 'N/A';
                    }
                })
                ->filterColumn('user_name', function($query, $keyword) {
                    $query->whereHas('boxUser.user', function($q) use($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['profile_image', 'profile_image_url', 'status'])
                ->make(true);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching employee attendance.',
                'error' => $th->getMessage(),
            ]);
        }
    }
    public function team_attendace_employee(Request $request)
    {
        try {
            $user = Auth::id();

            $boxUser = BoxUser::where('user_id', $user)->first();
            if (!$boxUser) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Box user not found.',
                ]);
            }

            $boxId = $boxUser->box_id;
            $today = Carbon::today()->toDateString();
            $userModel = Auth::user();

            $isGlobalAdmin = $userModel->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'team_admin');

            $allSubordinateIds = null;
            if (!$isGlobalAdmin && !$isBoxAdmin) {
                if ($canRead) {
                    $allSubordinateIds = BoxUser::getAllSubordinateIds($boxId, $userModel->id, $boxUser);
                } else {
                    return response()->json([
                        'status' => 403,
                        'message' => 'You do not have permission to access team attendance.'
                    ]);
                }
            }

            $query = Attendance::with([
                'boxUser.user',
                'corrections.break',
                'boxUser.departmentRelation',
                'boxUser.designationRelation',
                'boxUser.shift',
                'boxUser.workMode',
                'boxUser.box',
                'machine',
            ])
                ->where('attendances.box_id', $boxId)
                ->where('attendances.attendance_date', '<=', $today . ' 23:59:59')
                ->orderBy('attendances.attendance_date', 'desc')
                ->orderBy('attendances.id', 'desc');

            if ($allSubordinateIds !== null) {
                $query->whereIn('attendances.box_user_id', $allSubordinateIds);
            }

            // Apply filters
            if ($request->filled('from_date') && $request->filled('to_date')) {
                try {
                    // Try multiple date formats
                    $dateFormats = ['m/d/Y', 'Y-m-d', 'd/m/Y', 'm-d-Y', 'Y/m/d'];
                    $from = null;
                    $to = null;

                    foreach ($dateFormats as $format) {
                        try {
                            $from = Carbon::createFromFormat($format, $request->from_date);
                            $to = Carbon::createFromFormat($format, $request->to_date);
                            break;
                        } catch (\Exception $e) {
                            continue;
                        }
                    }

                    if ($from && $to) {
                        $query->whereBetween('attendance_date', [$from->startOfDay(), $to->endOfDay()]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error parsing date filters: ' . $e->getMessage(), [
                        'from_date' => $request->from_date,
                        'to_date' => $request->to_date
                    ]);
                }
            }

            if ($request->filled('department')) {
                $query->whereHas('boxUser', function ($q) use ($request) {
                    $q->where('department', $request->department);
                });
            }

            if ($request->filled('status')) {
                $query->whereRaw('LOWER(status) = ?', [strtolower($request->status)]);
            }

            if ($request->filled('status_correction')) {
                $statusCorrection = strtolower($request->status_correction);
                $query->whereHas('corrections', function ($q) use ($statusCorrection) {
                    if ($statusCorrection === 'approved') {
                        $q->whereRaw('LOWER(status) = ?', ['approved']);
                    } else {
                        $q->whereRaw('LOWER(status) = ?', [$statusCorrection]);
                    }
                });
            }

            /**
             * Update attendance statuses if:
             * - Date is not today => immediately update
             * - If today => compare with end time
             */
            $attendanceToUpdate = [];

            $attendanceItems = (clone $query)
                ->where('attendances.attendance_date', '>=', now()->subDays(7)->startOfDay())
                ->whereNull('status_updated_at')
                ->limit(100)
                ->get();

            foreach ($attendanceItems as $attendance) {
                if (!$attendance || !$attendance->boxUser) {
                    continue;
                }

                $attendanceDate = Carbon::parse($attendance->attendance_date)->toDateString();
                if ($attendanceDate !== $today) {
                    $attendanceToUpdate[] = $attendance->id;
                    continue;
                }
                $shiftEnd = optional($attendance->boxUser->shift)->end_time ?? null;
                $userEndTime = $attendance->boxUser->day_end_time ?? null;
                $boxEndTime = $attendance->boxUser->box->day_end_time ?? null;
                $endTime = $shiftEnd ?? $userEndTime ?? $boxEndTime;
                if (!$endTime) {
                    continue;
                }

                try {
                    $endTime = Carbon::parse($endTime)->format('H:i:s');
                } catch (\Exception $e) {
                    // ignore
                }

                $fullEndDateTime = Carbon::parse($attendanceDate . ' ' . $endTime);
                if (now()->greaterThanOrEqualTo($fullEndDateTime)) {
                    $attendanceToUpdate[] = $attendance->id;
                }
            }

            // Dispatch update job for qualified attendance IDs
            if (!empty($attendanceToUpdate)) {
                Attendance::whereIn('id', $attendanceToUpdate)->update(['status_updated_at' => now()]);
                UpdateAttendanceStatuses::dispatchAfterResponse($attendanceToUpdate);
            }

            // Return DataTable response
            return DataTables::eloquent($query)
                ->addColumn('user_name', function ($attendance) {
                    return $attendance->boxUser && $attendance->boxUser->user
                        ? ($attendance->boxUser->user->name ?? 'N/A')
                        : 'N/A';
                })
                ->addColumn('email', function ($attendance) {
                    return $attendance->boxUser && $attendance->boxUser->user
                        ? ($attendance->boxUser->user->email ?? 'N/A')
                        : 'N/A';
                })
                ->addColumn('profile_image', function ($attendance) {
                    if (!$attendance->boxUser)
                        return null;
                    $userProfileImage = $attendance->boxUser->user->profile_image_path ?? null;
                    $boxProfileImage = $attendance->boxUser->profile_image_path ?? null;
                    return $userProfileImage
                        ? asset('storage/' . $userProfileImage)
                        : ($boxProfileImage ? asset('storage/' . $boxProfileImage) : null);
                })
                ->addColumn('profile_image_url', function ($attendance) {
                    if (!$attendance->boxUser)
                        return null;
                    $userProfileImage = $attendance->boxUser->user->profile_image_path ?? null;
                    $boxProfileImage = $attendance->boxUser->profile_image_path ?? null;
                    return $userProfileImage
                        ? asset('storage/' . $userProfileImage)
                        : ($boxProfileImage ? asset('storage/' . $boxProfileImage) : null);
                })
                ->addColumn('corrections_statuses', function ($attendance) {
                    return $attendance->corrections ? $attendance->corrections->toArray() : [];
                })
                ->addColumn('box_user', function ($attendance) {
                    if (!$attendance->boxUser) {
                        return [
                            'id' => null,
                            'employee_id' => null,
                            'designation_relation' => ['name' => null],
                            'department_relation' => ['name' => null],
                            'work_mode' => ['name' => null],
                            'shift' => null
                        ];
                    }
                    return [
                        'id' => $attendance->boxUser->id ?? null,
                        'employee_id' => $attendance->boxUser->employee_id ?? null,
                        'designation_relation' => [
                            'name' => ($attendance->boxUser->designationRelation->name ?? null)
                        ],
                        'department_relation' => [
                            'name' => ($attendance->boxUser->departmentRelation->name ?? null)
                        ],
                        'work_mode' => [
                            'name' => ($attendance->boxUser->workMode->name ?? null)
                        ],
                        'shift' => $attendance->boxUser->shift ? [
                            'id' => $attendance->boxUser->shift->id ?? null,
                            'code' => $attendance->boxUser->shift->code ?? null
                        ] : null
                    ];
                })
                ->editColumn('status', function ($attendance) {
                    $status = strtolower($attendance->status ?? 'N/A');
                    return $status;
                })
                ->editColumn('attendance_date', function ($attendance) {
                    try {
                        return $attendance->attendance_date
                            ? Carbon::parse($attendance->attendance_date)->format('Y-m-d H:i:s')
                            : 'N/A';
                    } catch (\Exception $e) {
                        return $attendance->attendance_date ?? 'N/A';
                    }
                })
                ->rawColumns(['profile_image', 'profile_image_url', 'status'])
                ->make(true);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching employee attendance.',
                'error' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Calculate status based on production hours, shift, and time rules.
     */
    public function calculateStatus($attendance)
    {
        if (strtolower($attendance->status ?? '') === 'punch out miss') {
            return 'Punch Out Miss';
        }
        if (strtolower($attendance->status ?? '') === 'absent') {
            return 'absent';
        }
        if (!empty($attendance->check_in) && empty($attendance->check_out)) {
            $attendanceDate = Carbon::parse($attendance->attendance_date)->toDateString();
            $today = Carbon::today()->toDateString();
            if ($attendanceDate < $today) {
                return 'Punch Out Miss';
            }
        }
        $boxUser = $attendance->boxUser;
        $box = $boxUser->box;
        $shift = $boxUser->shift;
        $productionHours = $attendance->production_hours;
        if (!$productionHours) {
            return ucfirst($attendance->status ?? 'N/A');
        }
        if ($shift) {
            $absentBelow = $shift->absent_below;
            $halfDayBelow = $shift->half_day_below;
            $shortLeaveBelow = $shift->short_leave_below;
            $shiftHours = $shift->shift_hours;
            $relaxationTime = $shift->relaxation_time ?? '00:00:00';
            try {
                $productionSeconds = CarbonInterval::createFromFormat('H:i:s', $productionHours)->totalSeconds;
                $absentBelowSeconds = CarbonInterval::createFromFormat('H:i:s', $absentBelow)->totalSeconds;
                $halfDayBelowSeconds = CarbonInterval::createFromFormat('H:i:s', $halfDayBelow)->totalSeconds;
                $shortLeaveBelowSeconds = CarbonInterval::createFromFormat('H:i:s', $shortLeaveBelow)->totalSeconds;
                $shiftHoursSeconds = CarbonInterval::createFromFormat('H:i:s', $shiftHours)->totalSeconds;
                $relaxationSeconds = CarbonInterval::createFromFormat('H:i:s', $relaxationTime)->totalSeconds;
            } catch (\Exception $e) {
                return ucfirst($attendance->status ?? 'N/A');
            }
            if ($productionSeconds >= $shiftHoursSeconds) {
                return 'present';
            }
            if ($productionSeconds < $absentBelowSeconds) {
                return 'absent';
            }
            if ($productionSeconds < $halfDayBelowSeconds) {
                return 'half_day';
            }
            $adjustedShortLeaveThreshold = max(0, $shortLeaveBelowSeconds - $relaxationSeconds);
            if ($productionSeconds < $adjustedShortLeaveThreshold) {
                return 'short_leave';
            }
            return ucfirst($attendance->status ?? 'N/A');
        }
        $startTime = $boxUser->day_start_time ?? $box->day_start_time;
        $endTime = $boxUser->day_end_time ?? $box->day_end_time;
        if (!$startTime || !$endTime) {
            return ucfirst($attendance->status ?? 'N/A');
        }
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        $expectedHours = $end->diffInMinutes($start) / 60;
        try {
            $productionSeconds = CarbonInterval::createFromFormat('H:i:s', $productionHours)->totalSeconds;
        } catch (\Exception $e) {
            return ucfirst($attendance->status ?? 'N/A');
        }

        if ($productionSeconds < ($expectedHours * 3600)) {
            return 'half_day';
        }
    }

    public function edit_attendace(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'attendance_id' => 'nullable|integer|exists:attendances,id',
                'employee_id' => 'required|integer|exists:employees,id',
                'attendance_date' => 'required|date',
                'status' => 'required|string|in:Present,Absent,Half Day,Short Leave,Punch Out Miss,Comp Off',
                'check_in' => 'required|string',
                'check_out' => 'required|string',
                'break_duration' => 'nullable|string',
                'late_duration' => 'nullable|string',
                'production_hours' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                    'status' => 422,
                ], 422);
            }

            $validated = $validator->validated();

            if (!empty($validated['attendance_id'])) {
                $attendance = Attendance::find($validated['attendance_id']);
                if ($attendance) {
                    $attendance->update($validated);
                    $message = 'Attendance updated successfully (by attendance_id).';
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Attendance record not found with provided ID.',
                        'status' => 404,
                    ], 404);
                }
            } else {

                $attendance = Attendance::where('employee_id', $validated['employee_id'])
                    ->where('attendance_date', $validated['attendance_date'])
                    ->first();

                if ($attendance) {
                    $attendance->update($validated);
                    $message = 'Attendance updated successfully (by employee_id and date).';
                } else {
                    $attendance = Attendance::create($validated);
                    $message = 'Attendance created successfully.';
                }
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => 200,
                'data' => $attendance,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'error' => $th->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function today_absenties(Request $request)
    {
        try {
            $today = Carbon::today()->toDateString();
            $userModel = Auth::user();
            $boxUser = BoxUser::where('user_id', $userModel->id)->first();
            if (!$boxUser) {
                return response()->json([
                    'status' => 404,
                    'message' => 'User is not assigned to any box.',
                ], 404);
            }
            $boxId = $boxUser->box_id;

            $scope = $request->get('scope');
            $allSubordinateIds = null;

            $isGlobalAdmin = $userModel->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'team_admin');

            if ($scope === 'team' && !$isGlobalAdmin && !$isBoxAdmin) {
                if ($canRead) {
                    $allSubordinateIds = BoxUser::getAllSubordinateIds($boxId, $userModel->id, $boxUser);
                } else {
                    return response()->json([
                        'status' => 403,
                        'message' => 'You do not have permission to access team attendance.'
                    ]);
                }
            }

            $query = BoxUser::where('box_id', $boxId)->with('departmentRelation', 'user');

            if ($allSubordinateIds !== null) {
                $query->whereIn('id', $allSubordinateIds);
            }

            $absentEmployees = $query->where(function ($q) use ($today) {
                $q->whereHas('attendances', function ($subQ) use ($today) {
                    $subQ->whereDate('attendance_date', $today)
                        ->whereRaw('LOWER(status) = ?', ['absent']);
                })
                    ->orWhereDoesntHave('attendances', function ($subQ) use ($today) {
                        $subQ->whereDate('attendance_date', $today);
                    });
            })
                ->with([
                    'attendances' => function ($q) use ($today) {
                        $q->whereDate('attendance_date', $today);
                    }
                ])
                ->get()
                ->map(function ($employee) {
                    $employee->profile_image_url = $employee->profile_image_path
                        ? url('storage/' . $employee->profile_image_path)
                        : null;
                    return $employee;
                });

            return response()->json([
                'status' => 200,
                'data' => $absentEmployees,
                'message' => 'Absent employees retrieved successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Error fetching today\'s absent employees',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function getAttendanceBreaks(Request $request)
    {
        try {
            $attendanceId = $request->input('attendance_id');

            if (!$attendanceId) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Attendance ID is required',
                ], 400);
            }

            $breaks = AttendanceBreak::where('attendance_id', $attendanceId)
                ->orderBy('break_start', 'asc')
                ->get();

            $formattedBreaks = $breaks->map(function ($break) {
                return [
                    'id' => $break->id,
                    'break_start' => $break->break_start,
                    'break_end' => $break->break_end,
                    'duration' => $break->duration,
                    'break_status' => $break->break_status,
                    'status_label' => $break->break_status == 1 ? 'Active' : ($break->break_status == 0 ? 'Inactive' : 'Unknown'),
                ];
            });

            return response()->json([
                'status' => 200,
                'data' => $formattedBreaks,
                'total_breaks' => $breaks->count(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Error fetching attendance breaks',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function getDashboardStatistics(Request $request)
    {
        try {
            $userId = Auth::id();
            $box = Box::where('superadmin_id', $userId)->first();

            if (!$box) {
                $boxUser = BoxUser::where('user_id', $userId)->first();
                if (!$boxUser) {
                    return response()->json(['success' => false, 'message' => 'Box not found'], 404);
                }
                $box = $boxUser->box;
            }

            // 1. Fetch all departments for this box
            $allDepartments = \App\Models\Department::where('box_id', $box->id)->get();

            // 2. Count users per department
            $departmentCounts = [];
            foreach ($allDepartments as $dept) {
                // Ensure we count users assigned to this department
                $count = BoxUser::where('box_id', $box->id)
                    ->where('department', $dept->id)
                    ->count();
                $departmentCounts[$dept->name] = $count;
            }
            // Optional: Include empty/unknown department if any users have it
            $unknownDeptCount = BoxUser::where('box_id', $box->id)
                ->whereNull('department')
                ->count();
            if ($unknownDeptCount > 0) {
                $departmentCounts['Unknown'] = $unknownDeptCount;
            }

            // Teams/Designations not needed as per request
            $designationCounts = [];

            return response()->json([
                'success' => true,
                'department_counts' => $departmentCounts,
                'designation_counts' => $designationCounts
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $th->getMessage()
            ], 500);
        }
    }

}
