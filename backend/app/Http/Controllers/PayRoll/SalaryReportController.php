<?php

namespace App\Http\Controllers\PayRoll;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Box;
use App\Models\BoxUser;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\SalaryReport;
use App\Models\SalaryTransaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SalaryReportController extends Controller
{

    public function GetSalaryReport(Request $request)
    {
        try {
            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $boxUser = BoxUser::where('user_id', $user->id)->first();
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'report');

            if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view salary reports.'
                ], 403);
            }

            if (! $boxUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'No box user record found for this account.',
                    'data'    => [],
                ], 404);
            }
            $query = SalaryReport::with([
                'transactions',
                'boxUser.user',
                'boxUser.bankDetails',
                'boxUser.departmentRelation',
                'boxUser.designationRelation',
                'boxUser.box',
                'salary'
            ])
                ->where('box_id', $boxUser->box_id)
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->orderByDesc('id');
            if ($request->ajax() || $request->expectsJson()) {
                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('id', function ($row) {
                        return $row->id;
                    })
                    ->addColumn('emp_id', function ($row) {
                        return $row->boxUser->employee_id ?? $row->boxUser->id ?? '-';
                    })
                    ->addColumn('name', function ($row) {
                        return $row->boxUser->user->name ?? $row->boxUser->first_name ?? 'Unknown';
                    })
                    ->addColumn('email', function ($row) {
                        return $row->boxUser->user->email ?? '-';
                    })
                    ->addColumn('phone', function ($row) {
                        return $row->boxUser->phone ?? '-';
                    })
                    ->addColumn('department', function ($row) {
                        return $row->boxUser->departmentRelation->name ?? '-';
                    })
                    ->addColumn('joining_date', function ($row) {
                        return $row->boxUser?->joining_date ? \Carbon\Carbon::parse($row->boxUser->joining_date)->format('d M Y') : '-';
                    })
                    ->addColumn('month_year', function ($row) {
                        return Carbon::create($row->year, $row->month, 1)->format('F Y');
                    })
                    ->addColumn('gross_salary', fn($row) => number_format($row->gross_salary ?? 0, 2))
                    ->addColumn('final_salary', function ($row) {
                        $finalSalary = $row->net_salary ?? 0;
                        return number_format($finalSalary, 2, '.', '');
                    })
                    ->addColumn('total_deductions', fn($row) => number_format($row->total_deductions ?? 0, 2))
                    ->addColumn('transaction_status', function ($row) {
                        $latestTransaction = $row->transactions->last();
                        return $latestTransaction ? ucfirst($latestTransaction->status) : 'N/A';
                    })
                    ->addColumn('action', function ($row) {
                        $boxSlug = request()->route('box_slug');
                        return [
                            'id'   => $row->id,
                            'view' => true,
                            'payslip_url' => url("/{$boxSlug}/salary-slip?report_id={$row->id}"),
                        ];
                    })
                    ->rawColumns(['month_year'])
                    ->make(true);
            }
            $reports = $query->get();
            return response()->json([
                'success' => true,
                'message' => 'Salary reports retrieved successfully.',
                'data'    => $reports,
            ], 200);
        } catch (\Throwable $th) {
            Log::error('Error fetching salary reports: ' . $th->getMessage(), [
                'user_id' => Auth::id(),
                'trace'   => $th->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching salary reports.',
                'error'   => $th->getMessage(),
                'data'    => [],
            ], 500);
        }
    }

    public function getSalaryReportDetails(Request $request)
    {
        try {
            $reportId = $request->input('report_id');

            if (!$reportId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salary report ID not provided.',
                ], 400);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $boxUser = BoxUser::where('user_id', $user->id)->first();
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'report');

            if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view salary reports.'
                ], 403);
            }

            if (!$boxUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'No box user record found for this account.',
                ], 404);
            }

            $salaryReport = SalaryReport::with([
                'transactions',
                'boxUser.user',
                'boxUser.bankDetails',
                'boxUser.departmentRelation',
                'boxUser.designationRelation',
                'boxUser.box',
                'salary.earnings',
                'salary.deductions',
                'salary.salarySetting'
            ])
                ->where('id', $reportId)
                ->where('box_id', $boxUser->box_id)
                ->first();

            // Ensure salary relationship is loaded with snapshot
            if ($salaryReport && $salaryReport->salary) {
                $salaryReport->salary->load('salarySetting');
            }

            if (!$salaryReport) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salary report not found.',
                ], 404);
            }

            // Get salary settings data - prefer snapshot if available, otherwise fetch from salary_settings table
            $salarySettingsData = null;
            $customFieldsDefinitions = [];

            if ($salaryReport->salary) {
                // Priority 1: Use snapshot from salary (preserves exact state at assignment time)
                if ($salaryReport->salary->salary_settings_snapshot) {
                    $salarySettingsData = $salaryReport->salary->salary_settings_snapshot;

                    // Get custom field definitions for this box
                    $customFieldsDefinitions = \App\Models\BoxCustomField::where('box_id', $boxUser->box_id)
                        ->where('object', 'like', 'salary_%')
                        ->get()
                        ->map(function ($field) {
                            return [
                                'id' => $field->id,
                                'name' => $field->name,
                                'object' => $field->object,
                                'type' => $field->type,
                                'label' => $field->label ?? $field->name,
                            ];
                        })
                        ->toArray();

                    $salarySettingsData['custom_fields_definitions'] = $customFieldsDefinitions;
                }
                // Priority 2: Fallback to fetching from salary_settings table
                elseif ($salaryReport->salary->salary_settings_id) {
                    $salarySetting = \App\Models\SalarySetting::where('id', $salaryReport->salary->salary_settings_id)
                        ->where('box_id', $boxUser->box_id)
                        ->first();

                    if ($salarySetting) {
                        // Get custom fields data from tenant table
                        $customData = \App\Models\SalarySetting::getCustomFieldsData(
                            $salarySetting->id,
                            $boxUser->box_id
                        );

                        // Get custom field definitions for this box
                        $customFieldsDefinitions = \App\Models\BoxCustomField::where('box_id', $boxUser->box_id)
                            ->where('object', 'like', 'salary_%')
                            ->get()
                            ->map(function ($field) {
                                return [
                                    'id' => $field->id,
                                    'name' => $field->name,
                                    'object' => $field->object,
                                    'type' => $field->type,
                                    'label' => $field->label ?? $field->name,
                                ];
                            })
                            ->toArray();

                        // Merge base salary setting data with custom fields
                        $baseData = $salarySetting->toArray();

                        // Ensure component_types and custom_fields_max_amounts are arrays
                        if (isset($baseData['component_types']) && is_string($baseData['component_types'])) {
                            $baseData['component_types'] = json_decode($baseData['component_types'], true) ?? [];
                        }
                        if (isset($baseData['custom_fields_max_amounts']) && is_string($baseData['custom_fields_max_amounts'])) {
                            $baseData['custom_fields_max_amounts'] = json_decode($baseData['custom_fields_max_amounts'], true) ?? [];
                        }

                        // Combine base data with custom data
                        $salarySettingsData = array_merge($baseData, $customData);
                        $salarySettingsData['custom_fields_definitions'] = $customFieldsDefinitions;
                    }
                }
            }

            // Convert to array to ensure proper JSON encoding with relationships
            $reportData = $salaryReport->toArray();

            // Ensure earnings_breakdown and deductions_breakdown are arrays
            if (isset($reportData['earnings_breakdown']) && is_string($reportData['earnings_breakdown'])) {
                $reportData['earnings_breakdown'] = json_decode($reportData['earnings_breakdown'], true) ?? [];
            }
            if (isset($reportData['deductions_breakdown']) && is_string($reportData['deductions_breakdown'])) {
                $reportData['deductions_breakdown'] = json_decode($reportData['deductions_breakdown'], true) ?? [];
            }

            // Ensure relationships are included in the array
            if ($salaryReport->relationLoaded('boxUser')) {
                $reportData['box_user'] = $salaryReport->boxUser ? $salaryReport->boxUser->toArray() : null;
                if ($salaryReport->boxUser && $salaryReport->boxUser->relationLoaded('user')) {
                    $reportData['box_user']['user'] = $salaryReport->boxUser->user ? $salaryReport->boxUser->user->toArray() : null;
                }
                if ($salaryReport->boxUser && $salaryReport->boxUser->relationLoaded('departmentRelation')) {
                    $reportData['box_user']['department_relation'] = $salaryReport->boxUser->departmentRelation ? $salaryReport->boxUser->departmentRelation->toArray() : null;
                }
                if ($salaryReport->boxUser && $salaryReport->boxUser->relationLoaded('designationRelation')) {
                    $reportData['box_user']['designation_relation'] = $salaryReport->boxUser->designationRelation ? $salaryReport->boxUser->designationRelation->toArray() : null;
                }
            }

            if ($salaryReport->relationLoaded('salary')) {
                $reportData['salary'] = $salaryReport->salary ? $salaryReport->salary->toArray() : null;
                if ($salaryReport->salary && $salaryReport->salary->relationLoaded('earnings')) {
                    $reportData['salary']['earnings'] = $salaryReport->salary->earnings ? $salaryReport->salary->earnings->toArray() : null;
                }
                if ($salaryReport->salary && $salaryReport->salary->relationLoaded('deductions')) {
                    $reportData['salary']['deductions'] = $salaryReport->salary->deductions ? $salaryReport->salary->deductions->toArray() : null;
                }
            }

            if ($salaryReport->relationLoaded('transactions')) {
                $reportData['transactions'] = $salaryReport->transactions ? $salaryReport->transactions->map(function ($t) {
                    return $t->toArray();
                })->toArray() : [];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'report' => $reportData,
                    'salary_settings' => $salarySettingsData,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching salary report details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching salary report details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function GenearetSalaryReport(Request $request)
    {
        try {
            $request->validate([
                'month' => 'required|integer|min:1|max:12',
                'year' => 'required|integer|min:2000|max:2100',
            ]);

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $adminBoxUser = BoxUser::where('user_id', $user->id)->first();
            $isBoxAdmin = $adminBoxUser && strtolower($adminBoxUser->staff_role ?? '') === 'admin';
            $canCreate = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'create', 'report');

            if (!$isGlobalAdmin && !$isBoxAdmin && !$canCreate) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to generate salary reports.'
                ], 403);
            }
            $userId = $user->id;

            if (!$adminBoxUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Box not found for current user.',
                ], 403);
            }

            $boxId = $adminBoxUser->box_id;

            // Get all employees in the box
            $boxUsers = BoxUser::where('box_id', $boxId)
                ->with(['shift', 'box', 'bankDetails', 'attendances', 'latestSalary.salary_settings'])
                ->get();

            if ($boxUsers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No employees found in this box.',
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

                    // Check for resignation date within this month
                    if ($boxUser->resignation_date) {
                        $resignationDate = Carbon::parse($boxUser->resignation_date)->endOfDay();
                        if ($resignationDate->between($periodStart, $periodEnd)) {
                            $userPeriodEnd = $resignationDate;
                        } elseif ($resignationDate->lt($periodStart)) {
                            // If resigned before the month starts, skip this user
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

                    // Calculate holidays for this user period
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
                    $userLeaveDays = 0;
                    foreach ($leaveRequests as $leave) {
                        $leaveStart = Carbon::parse($leave->from_date)->lt($periodStart) ? $periodStart : Carbon::parse($leave->from_date);
                        $leaveEnd = Carbon::parse($leave->to_date)->gt($userPeriodEnd) ? $userPeriodEnd : Carbon::parse($leave->to_date);
                        if ($leaveStart->lte($leaveEnd)) {
                            $userLeaveDays += $leaveStart->diffInDays($leaveEnd) + 1;
                        }
                    }
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
                        return strtolower(trim($attendance->status)) === 'present';
                    })->count();
                    $absentCount = $attendances->filter(function ($attendance) {
                        return strtolower(trim($attendance->status)) === 'absent';
                    })->count();
                    
                    // Count comp off days from attendance records (case-insensitive check)
                    $compOffCount = $attendances->filter(function ($attendance) {
                        return strtolower($attendance->status) === 'comp off';
                    })->count();

                    // Count half days (counts as 0.5 days)
                    $halfDayCount = $attendances->filter(function ($attendance) {
                        $status = strtolower($attendance->status ?? '');
                        return $status === 'half day' || $status === 'half_day';
                    })->count();

                    // Count short leave days (counts as 0.25 days - half of half day)
                    $shortLeaveCount = $attendances->filter(function ($attendance) {
                        $status = strtolower($attendance->status ?? '');
                        return $status === 'short leave' || $status === 'short_leave';
                    })->count();

                    // Count punch out miss days (counts as full day - present but missed punch out)
                    $punchOutMissCount = $attendances->filter(function ($attendance) {
                        $status = strtolower($attendance->status ?? '');
                        return $status === 'punch out miss' || $status === 'punch_out_miss';
                    })->count();

                    $netSalary = optional($salary)->net_salary;
                    $daysInMonth = $periodStart->daysInMonth; // Total days in the month (e.g., 31 for December)

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
                        return $status === 'holiday' || $status === 'holiday approved';
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
                    $presentDays = $presentCount;
                    $absentDays = $absentCount;
                    $compOffDays = $compOffCount;
                    $halfDays = $halfDayCount;
                    $shortLeaveDays = $shortLeaveCount;
                    $punchOutMissDays = $punchOutMissCount;

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
                            $creditedLeavesCount = LeaveTransaction::where('box_user_id', $boxUser->id)
                                ->where('leave_type_id', $compOffLeaveType->id)
                                ->whereYear('date', $periodStart->year)
                                ->whereMonth('date', $periodStart->month)
                                ->where('credit_leave', '>', 0)
                                ->sum('credit_leave');

                            $latestTxn = LeaveTransaction::where('box_user_id', $boxUser->id)
                                ->where('leave_type_id', $compOffLeaveType->id)
                                ->latest('id')
                                ->first();

                            $availableBalance = $latestTxn ? $latestTxn->balance_leave : 0;
                            $encashableDays = min((int)$creditedLeavesCount, (int)$availableBalance);

                            if ($encashableDays > 0) {
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

                    $compOffPaidWeight = ($compOffTypeSetting === 'paid') ? 1.0 : 0.0;
                    
                    // Dynamic paid days for display only
                    $dynamicPaidDays = ($presentDays * 1.0) +
                        ($weeklyOffDays * 1.0) +
                        ($holidayDays * 1.0) +
                        ($userLeaveDays * 1.0) +
                        ($compOffDays * $compOffPaidWeight) +
                        ($halfDays * 0.5) +
                        ($shortLeaveDays * 0.25) -
                        ($punchOutMissDays * 1.0) -
                        ($absentDays * 1.0);
                    $dynamicPaidDays = max(0, round($dynamicPaidDays, 2));

                    // Standard paid days for calculation
                    $paidDays = ($presentDays * 1.0) +
                        ($compOffDays * $compOffPaidWeight) +
                        ($weeklyOffDays * 1.0) +
                        ($holidayDays * 1.0) +
                        ($halfDays * 0.5) +
                        ($shortLeaveDays * 0.25) -
                        ($punchOutMissDays * 1.0);
                    $paidDays = max(0, round($paidDays, 2));

                    // Cap standard paid days at standard base days
                    if ($paidDays > $standardDays) {
                        $paidDays = $standardDays;
                    }
                   
                    if ($netSalary && $standardDays > 0) {
                        $perDaySalary = round($netSalary / $standardDays, 2);
                        $compOffUnpaidWeight = ($compOffTypeSetting === 'paid') ? 0.0 : 1.0;
                        $deductionDays = $absentDays + 
                                         ($compOffDays * $compOffUnpaidWeight) + 
                                         ($halfDays * 0.5) + 
                                         ($shortLeaveDays * 0.75) +
                                         ($punchOutMissDays * 1.0);
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
                        
                        // Total deductions
                        $deductionDays += $inactiveDays;

                        if ($deductionDays > $standardDays) {
                            $deductionDays = $standardDays;
                        }

                        $deductionAmount = round($perDaySalary * $deductionDays, 2);
                        $compOffEncashAmount = round($perDaySalary * $encashableDays, 2);
                        
                        $finalSalary = round($netSalary - $deductionAmount + $compOffEncashAmount, 2);

                        if ($finalSalary < 0) {
                            $finalSalary = 0;
                        }
                    } elseif ($netSalary) {
                        // If no salary defined, set defaults
                        $perDaySalary = 0;
                        $deductionDays = 0;
                        $deductionAmount = 0;
                        $finalSalary = 0;
                    }

                    // Update or create salary report - this will update if same month/year already exists
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

                    // Only create transaction if report was just created (not updated)
                    if ($salaryReport->wasRecentlyCreated) {
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
                    } else {
                        // Update existing transaction if report was updated
                        $existingTransaction = SalaryTransaction::where('salary_report_id', $salaryReport->id)
                            ->where('payment_method', 'system_generated')
                            ->first();
                        if ($existingTransaction) {
                            $existingTransaction->update([
                                'amount' => $finalSalary,
                                'updated_at' => now(),
                            ]);
                        }
                    }

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

            return response()->json([
                'success' => true,
                'message' => "Salary reports generated successfully for {$generatedCount} employees.",
                'data' => $reports,
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating salary report: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while generating the salary report: ' . $e->getMessage(),
            ], 500);
        }
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
            $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'report');

            if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'You do not have permission to export salary reports.'], 403);
                }
                 return back()->with('error', 'You do not have permission to export salary reports.');
            }
            $userId = $user->id;

            if (!$adminBoxUser) {
                return back()->with('error', 'Unauthorized: Box not found for current user.');
            }

            $boxId = $adminBoxUser->box_id;
            Log::info('Salary Report export requested', [
                'box_id' => $boxId,
                'request' => $request->all()
            ]);

            $filters = $request->only(['designation_id', 'sort_order', 'date_range', 'search', 'export_month', 'export_year']);
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
                'month_year' => 'Month/Year',
                'total_holidays' => 'Holidays',
                'gross_salary' => 'Gross Salary',
                'net_salary' => 'Final Salary',
                'total_deductions' => 'Total Deductions',
                'paid_days' => 'Paid Days',
                'status' => 'Status',
                'created_at' => 'Created Date'
            ];

            $selectedFieldNames = [];
            foreach ($fields as $field) {
                if (isset($allExportFields[$field])) {
                    $selectedFieldNames[$field] = $allExportFields[$field];
                }
            }

            $export = new \App\Exports\SalaryReportExport($filters, $fields, $selectedFieldNames);
            $fileName = 'Salary_Reports_' . now()->format('Y-m-d_H-i-s');

            Log::info('Generating export file', [
                'type' => $exportType,
                'fileName' => $fileName,
                'fieldsCount' => count($fields)
            ]);

            ob_end_clean(); // Prevent output buffering issues

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
                    'title' => 'Salary Reports',
                    'date' => now()->format('d M Y H:i')
                ])->setPaper('a4', 'landscape');

                return $pdf->download($fileName . '.pdf');
            }

            return \Maatwebsite\Excel\Facades\Excel::download($export, $fileName . '.xlsx');

        } catch (\Exception $e) {
            Log::error('Salary Report Export Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => substr($e->getTraceAsString(), 0, 1000)
            ]);
            return back()->with('error', 'An error occurred during export: ' . $e->getMessage());
        }
    }
    // public function GenearetSalaryReport(Request $request)
    // {
    //     try {
    //         $userId = Auth::id();

    //         $box = Box::where('superadmin_id', $userId)->first();
    //         if (!$box) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Box not found for this superadmin.',
    //             ], 404);
    //         }

    //         $boxUsers = BoxUser::where('box_id', $box->id)
    //             ->with(['shift', 'box', 'bankDetails', 'attendances', 'latestSalary.salary_settings'])
    //             ->get();

    //         if ($boxUsers->isEmpty()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'No employees found in this box.',
    //             ], 404);
    //         }

    //         $year = $request->input('year', now()->year);
    //         $month = $request->input('month', now()->month);

    //         $periodStart = Carbon::create($year, $month, 1)->startOfDay();
    //         $periodEnd = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

    //         $totalHolidays = Holiday::where('box_id', $box->id)
    //             ->where('status', 'Active')
    //             ->whereBetween('date', [$periodStart, $periodEnd])
    //             ->count();

    //         $leaveRequests = LeaveRequest::where('box_id', $box->id)
    //             ->where('status', 'approved')
    //             ->where(function ($query) use ($periodStart, $periodEnd) {
    //                 $query->whereBetween('from_date', [$periodStart, $periodEnd])
    //                       ->orWhereBetween('to_date', [$periodStart, $periodEnd])
    //                       ->orWhere(function ($q) use ($periodStart, $periodEnd) {
    //                           $q->where('from_date', '<', $periodStart)
    //                             ->where('to_date', '>', $periodEnd);
    //                       });
    //             })->get();

    //         $totalLeaveDays = 0;
    //         foreach ($leaveRequests as $leave) {
    //             $leaveStart = Carbon::parse($leave->from_date)->lt($periodStart) ? $periodStart : Carbon::parse($leave->from_date);
    //             $leaveEnd = Carbon::parse($leave->to_date)->gt($periodEnd) ? $periodEnd : Carbon::parse($leave->to_date);
    //             $totalLeaveDays += $leaveStart->diffInDays($leaveEnd) + 1;
    //         }

    //         $decimalHoursToHHMM = fn($decimal) =>
    //             ($decimal === null || $decimal <= 0) ? '--:--' : sprintf('%02d:%02d', floor($decimal), round(($decimal - floor($decimal)) * 60));

    //         $parseWorkingDays = fn($input) => array_unique(array_map('trim', explode(',', is_array($input) ? implode(',', $input) : ($input ?? ''))));

    //         $reports = [];

    //         DB::transaction(function () use ($boxUsers, $box, $userId, $periodStart, $periodEnd, $totalHolidays, $totalLeaveDays, $decimalHoursToHHMM, $parseWorkingDays, &$reports) {
    //             foreach ($boxUsers as $boxUser) {
    //                 $salary = $boxUser->latestSalary;
    //                 $shift = $boxUser->shift;
    //                 $hasShift = $boxUser->shift_id && optional($shift)->shift_enabled;
    //                 $totalWorkingDays = null;
    //                 $userWorkingDays = $parseWorkingDays($boxUser->working_days ?? $boxUser->box->working_days ?? 'Monday,Tuesday,Wednesday,Thursday,Friday');

    //                 $dailyHours = $weeklyHours = $monthlyHours = $totalWorkingDaysWeek = $totalWorkingDaysMonth = $totalOvertimeHours = 0;
    //                 $weeklyHours = $monthlyHours = '--:--';

    //                 if ($hasShift) {
    //                     $userWorkingDays = $this->getWorkingDaysFromShift($shift);
    //                     if (!empty($shift->shift_hours)) {
    //                         [$hours, $minutes] = array_pad(explode(':', $shift->shift_hours), 2, 0);
    //                         $dailyHours = (int)$hours + ((int)$minutes / 60);
    //                     }
    //                     $weekly = $this->calculateWorkingDaysAndHours($periodStart, $periodEnd, $shift, $userWorkingDays);
    //                     $monthly = $this->calculateWorkingDaysMonthsAndHours($periodStart, $periodEnd, $shift, $userWorkingDays, $dailyHours);

    //                     $totalWorkingDaysWeek = $weekly['working_days'];
    //                     $totalWorkingDaysMonth = $monthly['working_days'];
    //                     $totalWeeklyOff = $monthly['total_weekly_offs'];

    //                     $weeklyHours = $decimalHoursToHHMM($totalWorkingDaysWeek * $dailyHours);
    //                     $monthlyHours = $decimalHoursToHHMM($totalWorkingDaysMonth * $dailyHours);
    //                 }

    //                 $attendances = Attendance::where('box_id', $box->id)
    //                     ->where('box_user_id', $boxUser->id)
    //                     ->whereBetween('attendance_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
    //                     ->get();

    //                 $presentCount = $attendances->where('status', 'Present')->count();
    //                 $absentCount = $attendances->where('status', 'Absent')->count();

    //                 $netSalary = optional($salary)->net_salary;
    //                 $finalSalary = $deductionAmount = $deductionDays = $perDaySalary = 0;

    //                 if ($netSalary) {
    //                     $daysInMonth = $periodStart->daysInMonth;
    //                     $perDaySalary = $daysInMonth > 0 ? round($netSalary / $daysInMonth, 2) : 0;
    //                     $totalWorkingDays = $totalWorkingDaysMonth ?: $daysInMonth;
    //                     $presentDays = $presentCount;

    //                     if ($presentDays >= $totalWorkingDays) {
    //                         $deductionDays = $deductionAmount = 0;
    //                         $finalSalary = $netSalary;
    //                     } else {
    //                         $deductionDays = $totalWorkingDays - $presentDays;
    //                         $deductionAmount = round($perDaySalary * $deductionDays, 2);
    //                         $finalSalary = round($netSalary - $deductionAmount, 2);
    //                     }
    //                 }

    //                 $salaryReport = SalaryReport::updateOrCreate(
    //                     [
    //                         'box_user_id' => $boxUser->id,
    //                         'box_id' => $box->id,
    //                         'year' => $periodStart->year,
    //                         'month' => $periodStart->format('m'),
    //                     ],
    //                     [
    //                         'salary_id' => optional($salary)->id,
    //                         'gross_salary' => $netSalary,
    //                         'total_earnings' => $netSalary,
    //                         'total_deductions' => $deductionAmount,
    //                         'net_salary' => $finalSalary,
    //                         'earnings_breakdown' => [
    //                             'present_days' => $presentCount,
    //                             'per_day_salary' => $perDaySalary,
    //                         ],
    //                         'deductions_breakdown' => [
    //                             'absent_days' => $deductionDays,
    //                             'deduction_amount' => $deductionAmount,
    //                         ],
    //                         'total_working_days' => $totalWorkingDays,
    //                         'total_weekly_offs' => $totalWeeklyOff,
    //                         'total_holidays' => $totalHolidays,
    //                         'leave_days' => $totalLeaveDays,
    //                         'paid_days' => $totalWorkingDaysMonth,
    //                         'is_locked' => false,
    //                         'created_by' => $userId,
    //                         'updated_by' => $userId,
    //                     ]
    //                 );

    //                 SalaryTransaction::create([
    //                     'salary_report_id' => $salaryReport->id,
    //                     'user_bank_account_id' => optional($boxUser->bankDetails)->id ?? null,
    //                     'payment_date' => now(),
    //                     'amount' => $finalSalary,
    //                     'payment_method' => 'system_generated',
    //                     'transaction_ref' => 'SR-' . strtoupper(uniqid()),
    //                     'status' => 'pending',
    //                     'remarks' => 'Auto generated salary report for ' . $periodStart->format('F Y'),
    //                     'created_by' => $userId,
    //                 ]);

    //                 $reports[] = [
    //                     'user_id' => $boxUser->id,
    //                     'name' => $boxUser->first_name ?? 'Unknown',
    //                     'daily_hours' => $dailyHours,
    //                     'weekly_hours' => $weeklyHours,
    //                     'monthly_hours' => $monthlyHours,
    //                     'total_working_days_week' => $totalWorkingDaysWeek,
    //                     'total_working_days_month' => $totalWorkingDaysMonth,
    //                     'total_overtime_hours' => $totalOvertimeHours,
    //                     'absent_days' => $absentCount,
    //                     'worked_days' => $presentCount,
    //                     'period_start' => $periodStart->toDateString(),
    //                     'period_end' => $periodEnd->toDateString(),
    //                     'per_day_salary' => $perDaySalary,
    //                     'deduction_days' => $deductionDays,
    //                     'deduction_amount' => $deductionAmount,
    //                     'final_salary' => $finalSalary,
    //                 ];
    //             }
    //         });

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Salary report generated successfully for all employees.',
    //             'data' => $reports,
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Error generating salary report: ' . $e->getMessage());

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An error occurred while generating the salary report: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }


    protected function getWorkingDaysFromShift($shift)
    {
        return $shift->working_days ? explode(',', $shift->working_days) : ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    }
    public function calculateWorkingDaysMonthsAndHours($startOfMonth, $endOfMonth, $shift, $userWorkingDays, $dailyHours)
    {
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

        if (is_string($dailyHours) && str_contains($dailyHours, ':')) {
            [$hours, $minutes] = explode(':', $dailyHours);
            $dailyHours        = (float) $hours + ((float) $minutes / 60);
        }
        $halfDayHours = $dailyHours * 0.5;
        $weeklyOff1   = $shift->weekly_off_1 ?? null;
        $weeklyOff2   = $shift->weekly_off_2 ?? null;
        $weeklyOff3   = $shift->weekly_off_3 ?? null;
        $weeklyOff4   = $shift->weekly_off_4 ?? null;

        $fullDayWeeks = $shift->saturday_offs ?? [];
        $halfDayWeeks = $shift->saturday_half_days ?? [];


        $weekdayCounts = [];
        $off1Count     = 0;
        $off2Count     = 0;
        $off3Count     = 0;
        $off4Count     = 0;

        $totalDays        = 0;
        $totalWorkingDays = 0.0;
        $totalHours       = 0.0;

        foreach ($period as $date) {
            $totalDays++;

            $dayName                 = $date->format('l');
            $dayOfMonth              = $date->day;
            $weekOfMonth             = ceil($dayOfMonth / 7);
            $weekLabel               = $this->getOrdinal($weekOfMonth);
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


            if (! in_array($dayName, $userWorkingDays)) {
                continue;
            }

            if (in_array($dayName, [$weeklyOff1, $weeklyOff2, $weeklyOff3])) {
                continue;
            }

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
        $halfDays        = count($halfDayWeeks);
        $countAshalfdays = $halfDays * 0.5;



        $totalWorkingDaysInAmonths = $totalDays;
        $totalOffFays              = $off1Count + $off2Count + $fulldaysWorkign + $countAshalfdays;
        $totaldaysWorkign          = $totalWorkingDaysInAmonths - $totalOffFays;
        // dd($totalOffFays);
        return [
            'working_days' => $totaldaysWorkign,
            'total_weekly_offs' => $totalOffFays
        ];
    }

    public function calculateWorkingDaysAndHours($startDate, $endDate, $shift, $userWorkingDays)
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
            $weekOfMonth = ceil($date->day / 7);
            $weekLabel   = ['1st', '2nd', '3rd', '4th', '5th'][$weekOfMonth - 1] ?? '';
            if (in_array($weekLabel, $fullDayWeeks)) {
                $totalWorkingDays -= 1;
                $workingSaturdays[] = $date->format('Y-m-d') . ' (Full Day)';
            } elseif (in_array($weekLabel, $halfDayWeeks)) {
                $totalWorkingDays -= 0.5;
                $workingSaturdays[] = $date->format('Y-m-d') . ' (Half Day)';
            }
        }
        return [
            'working_days'      => $userTotalWorkingDays + $totalWorkingDays,
            'working_saturdays' => $workingSaturdays,
        ];
    }

    public function getOrdinal($number)
    {
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        if (($number % 100) >= 11 && ($number % 100) <= 13) {
            return $number . 'th';
        }

        return $number . $ends[$number % 10];
    }

    public function deleteSalaryReport(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:salary_reports,id',
        ]);

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canDelete = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'delete', 'report');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canDelete) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete salary reports.'
            ], 403);
        }

        DB::beginTransaction();

        try {
            $report = SalaryReport::with(['transactions'])->find($request->id);

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salary report not found.',
                ], 404);
            }

            if ($boxUser && $report->box_id != $boxUser->box_id && !$isGlobalAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: This report belongs to another box.',
                ], 403);
            }

            // Delete associated transactions
            $report->transactions()->delete();

            // Delete the report (it uses SoftDeletes)
            $report->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Salary report deleted successfully.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while deleting the report.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }
}
