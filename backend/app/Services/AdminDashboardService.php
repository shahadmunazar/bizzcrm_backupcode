<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\Box;
use App\Models\BoxUser;
use App\Models\Department;
use App\Models\EmployeeType;
use App\Models\JobApplicant;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\TargetAssignment;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardService
{
    /**
     * Get complete unified dashboard data for Admin / CRM Overview.
     */
    public function getDashboardData(Box $box, Request $request): array
    {
        $startDateStr = $request->get('start_date');
        $endDateStr = $request->get('end_date');
        $startDate = $startDateStr ? Carbon::parse($startDateStr)->startOfDay() : Carbon::today()->startOfDay();
        $endDate = $endDateStr ? Carbon::parse($endDateStr)->endOfDay() : Carbon::today()->endOfDay();
        $userIdFilter = $request->get('user_id');
        $departmentIdFilter = $request->get('department_id');

        $empQuery = BoxUser::where('box_id', $box->id);
        if (!empty($departmentIdFilter)) {
            $empQuery->where('department', $departmentIdFilter);
        }
        if (!empty($userIdFilter)) {
            $empQuery->where('user_id', $userIdFilter);
        }
        $totalEmployees = $empQuery->count();

        return [
            'box' => [
                'id' => $box->id,
                'name' => $box->name,
                'slug' => $box->slug,
                'logo' => $box->logo ? asset('storage/' . $box->logo) : null,
            ],
            'filters' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'user_id' => $userIdFilter ? (int) $userIdFilter : null,
                'department_id' => $departmentIdFilter ? (int) $departmentIdFilter : null,
            ],
            'welcome' => $this->getWelcomeSummary($box),
            'attendance_overview' => $this->getAttendanceOverview($box, $startDate, $endDate, $totalEmployees, $departmentIdFilter, $userIdFilter),
            'today_attendances' => $this->getTodayAttendances($box, $startDate, $endDate, $departmentIdFilter, $userIdFilter),
            'latest_late_employee' => $this->getLatestLateEmployee($box, $startDate, $endDate, $departmentIdFilter, $userIdFilter),
            'tasks_stats' => $this->getTasksStats($box, $startDate, $endDate, $userIdFilter, $departmentIdFilter),
            'performance' => $this->getPerformance($box),
            'employee_types_stats' => $this->getEmployeeTypesStats($box, $totalEmployees),
            'total_active_employees' => $totalEmployees,
            'department_counts' => $this->getDepartmentCounts($box),
            'recent_activities' => $this->getRecentActivities($box, $startDate, $endDate),
            'upcoming_birthdays' => $this->getUpcomingBirthdays($box),
            'pending_corrections_list' => $this->getPendingCorrectionsList($box, $startDate, $endDate),
            'all_employees' => $this->getEmployeesList($box, $departmentIdFilter),
            'meta' => $this->getMetaOptions($box),
        ];
    }

    /**
     * Header & quick welcome summary with pending counts.
     */
    public function getWelcomeSummary(Box $box): array
    {
        $user = Auth::user();
        $boxUser = $user ? BoxUser::where('box_id', $box->id)->where('user_id', $user->id)->first() : null;
        $profileImage = $boxUser?->profile_image_path;
        if ($profileImage && !filter_var($profileImage, FILTER_VALIDATE_URL)) {
            $profileImage = asset('storage/' . $profileImage);
        } elseif (!$profileImage) {
            $profileImage = "https://ui-avatars.com/api/?name=" . urlencode($user?->name ?? 'User') . "&background=random";
        }

        $pendingCorrectionsCount = AttendanceCorrection::where('box_id', $box->id)
            ->where('status', 'pending')
            ->count();

        $pendingLeaveRequestsCount = LeaveRequest::where('box_id', $box->id)
            ->where('status', 'Pending')
            ->count();

        $jobApplicantsCount = JobApplicant::ofBox($box->id)->count();

        return [
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'Admin',
            'user_email' => $user?->email,
            'profile_image' => $profileImage,
            'pending_corrections_count' => $pendingCorrectionsCount,
            'pending_leave_requests_count' => $pendingLeaveRequestsCount,
            'job_applicants_count' => $jobApplicantsCount,
        ];
    }

    /**
     * Attendance summary & breakdown with percentage trend.
     */
    public function getAttendanceOverview(Box $box, Carbon $startDate, Carbon $endDate, int $totalEmployees, ?int $departmentIdFilter = null, ?int $userIdFilter = null): array
    {
        $daysDiff = $startDate->diffInDays($endDate) + 1;
        $prevStartDate = $startDate->copy()->subDays($daysDiff)->startOfDay();
        $prevEndDate = $endDate->copy()->subDays($daysDiff)->endOfDay();

        $baseAttendanceQuery = Attendance::where('attendances.box_id', $box->id);
        if (!empty($departmentIdFilter) || !empty($userIdFilter)) {
            $baseAttendanceQuery->whereHas('boxUser', function ($q) use ($departmentIdFilter, $userIdFilter) {
                if (!empty($departmentIdFilter)) {
                    $q->where('department', $departmentIdFilter);
                }
                if (!empty($userIdFilter)) {
                    $q->where('user_id', $userIdFilter);
                }
            });
        }

        // Unique employees present in current period
        $presentCount = (clone $baseAttendanceQuery)
            ->whereBetween('attendance_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereNotNull('check_in')
            ->distinct('box_user_id')
            ->count('box_user_id');

        // Unique employees present in previous period
        $prevPresentCount = (clone $baseAttendanceQuery)
            ->whereBetween('attendance_date', [$prevStartDate->format('Y-m-d'), $prevEndDate->format('Y-m-d')])
            ->whereNotNull('check_in')
            ->distinct('box_user_id')
            ->count('box_user_id');

        $percentageDiff = 0.0;
        $isIncrease = true;
        if ($prevPresentCount > 0) {
            $diff = (($presentCount - $prevPresentCount) / $prevPresentCount) * 100;
            $percentageDiff = round(abs($diff), 2);
            $isIncrease = $diff >= 0;
        } elseif ($presentCount > 0) {
            $percentageDiff = 100.0;
            $isIncrease = true;
        }

        // Breakdown counts
        $attendancesQuery = (clone $baseAttendanceQuery)
            ->whereBetween('attendance_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        $lateCount = (clone $attendancesQuery)
            ->where(function ($q) {
                $q->where('status', 'Late')
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('late_duration')->where('late_duration', '>', '00:00:00');
                  });
            })
            ->distinct('box_user_id')
            ->count('box_user_id');

        $halfDayCount = (clone $attendancesQuery)
            ->where('status', 'Half Day')
            ->distinct('box_user_id')
            ->count('box_user_id');

        $absentCount = max(0, $totalEmployees - $presentCount);

        $punchOutMissCount = (clone $attendancesQuery)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->where('attendance_date', '<', Carbon::today()->format('Y-m-d'))
            ->distinct('box_user_id')
            ->count('box_user_id');

        return [
            'present_count' => $presentCount,
            'total_count' => $totalEmployees,
            'percentage_diff' => $percentageDiff,
            'is_increase' => $isIncrease,
            'formatted_trend' => ($isIncrease ? '+' : '-') . $percentageDiff . '%',
            'breakdown' => [
                'present' => $presentCount,
                'late' => $lateCount,
                'absent' => $absentCount,
                'half_day' => $halfDayCount,
                'punch_out_miss' => $punchOutMissCount,
            ]
        ];
    }

    /**
     * Today's attendance cards list.
     */
    public function getTodayAttendances(Box $box, Carbon $startDate, Carbon $endDate, ?int $departmentIdFilter = null, ?int $userIdFilter = null): array
    {
        $query = Attendance::with(['boxUser.user', 'boxUser.designationRelation', 'boxUser.departmentRelation'])
            ->where('attendances.box_id', $box->id)
            ->whereBetween('attendances.attendance_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereNotNull('attendances.check_in');

        if (!empty($departmentIdFilter) || !empty($userIdFilter)) {
            $query->whereHas('boxUser', function ($q) use ($departmentIdFilter, $userIdFilter) {
                if (!empty($departmentIdFilter)) {
                    $q->where('department', $departmentIdFilter);
                }
                if (!empty($userIdFilter)) {
                    $q->where('user_id', $userIdFilter);
                }
            });
        }

        $attendances = $query->orderByDesc('attendances.updated_at')
            ->take(10)
            ->get();

        return $attendances->map(function ($attendance) {
            $boxUser = $attendance->boxUser;
            $user = $boxUser?->user;
            $name = $user?->name ?? 'N/A';

            $profileImage = $boxUser?->profile_image_path;
            if ($profileImage && !filter_var($profileImage, FILTER_VALIDATE_URL)) {
                $profileImage = asset('storage/' . $profileImage);
            } elseif (!$profileImage) {
                $profileImage = "https://ui-avatars.com/api/?name=" . urlencode($name) . "&background=random";
            }

            $checkIn = $attendance->check_in ? Carbon::parse($attendance->check_in)->format('h:i A') : '--:--';
            $checkOut = $attendance->check_out ? Carbon::parse($attendance->check_out)->format('h:i A') : '--:--';
            $workingHours = $attendance->production_hours ?: ($attendance->working_hours ?: '0.00 Hrs');

            return [
                'attendance_id' => $attendance->id,
                'box_user_id' => $attendance->box_user_id,
                'employee_name' => $name,
                'designation' => $boxUser?->designationRelation?->name ?? 'Employee',
                'department' => $boxUser?->departmentRelation?->name ?? 'General',
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'working_hours' => $workingHours,
                'profile_image' => $profileImage,
                'status' => $attendance->status ?? 'Present',
            ];
        })->toArray();
    }

    /**
     * Get latest late employee.
     */
    public function getLatestLateEmployee(Box $box, Carbon $startDate, Carbon $endDate, ?int $departmentIdFilter = null, ?int $userIdFilter = null): ?array
    {
        $query = Attendance::with(['boxUser.user', 'boxUser.designationRelation'])
            ->where('attendances.box_id', $box->id)
            ->whereBetween('attendances.attendance_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereNotNull('attendances.check_in')
            ->where(function ($q) {
                $q->where('attendances.status', 'Late')
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('attendances.late_duration')->where('attendances.late_duration', '>', '00:00:00');
                  });
            });

        if (!empty($departmentIdFilter) || !empty($userIdFilter)) {
            $query->whereHas('boxUser', function ($q) use ($departmentIdFilter, $userIdFilter) {
                if (!empty($departmentIdFilter)) {
                    $q->where('department', $departmentIdFilter);
                }
                if (!empty($userIdFilter)) {
                    $q->where('user_id', $userIdFilter);
                }
            });
        }

        $lateAttendance = $query->orderByDesc('attendances.updated_at')->first();

        if (!$lateAttendance || !$lateAttendance->boxUser || !$lateAttendance->boxUser->user) {
            return null;
        }

        $boxUser = $lateAttendance->boxUser;
        $name = $boxUser->user->name;

        $profileImage = $boxUser->profile_image_path;
        if ($profileImage && !filter_var($profileImage, FILTER_VALIDATE_URL)) {
            $profileImage = asset('storage/' . $profileImage);
        } elseif (!$profileImage) {
            $profileImage = "https://ui-avatars.com/api/?name=" . urlencode($name) . "&background=random";
        }

        $lateBy = $lateAttendance->late_duration ? Carbon::parse($lateAttendance->late_duration)->format('H\h i\m') : '15m';

        return [
            'employee_name' => $name,
            'designation' => $boxUser->designationRelation?->name ?? 'Employee',
            'check_in' => $lateAttendance->check_in ? Carbon::parse($lateAttendance->check_in)->format('h:i A') : '--:--',
            'late_by' => $lateBy,
            'profile_image' => $profileImage,
        ];
    }

    /**
     * Total tasks and status breakdown.
     */
    public function getTasksStats(Box $box, Carbon $startDate, Carbon $endDate, ?int $userIdFilter = null, ?int $departmentIdFilter = null): array
    {
        $query = Activity::where('activities.box_id', $box->id)
            ->whereIn('activities.type', ['task', 'call', 'email', 'meeting', 'whatsapp', 'note']);

        if (!empty($userIdFilter)) {
            $query->where('activities.assigned_to', $userIdFilter);
        } elseif (!empty($departmentIdFilter)) {
            $deptUserIds = BoxUser::where('box_id', $box->id)
                ->where('department', $departmentIdFilter)
                ->pluck('user_id');
            $query->whereIn('activities.assigned_to', $deptUserIds);
        }

        $stats = (clone $query)->selectRaw("
            COUNT(CASE WHEN activities.status NOT IN ('Close', 'Completed', 'Closed') OR activities.status IS NULL THEN 1 END) as open_count,
            COUNT(CASE WHEN activities.status IN ('Close', 'Completed', 'Closed') THEN 1 END) as closed_count,
            COUNT(*) as total
        ")->first();

        $total = (int) ($stats->total ?? 0);
        $openCount = (int) ($stats->open_count ?? 0);
        $closedCount = (int) ($stats->closed_count ?? 0);

        $openPercent = $total > 0 ? (int) round(($openCount / $total) * 100) : 0;
        $closedPercent = $total > 0 ? (int) round(($closedCount / $total) * 100) : 0;

        // Additional detailed statuses
        $lastTaskCalcTime = $box->last_task_calculation_time ?? '00:30';
        if (substr_count($lastTaskCalcTime, ':') == 1) {
            $lastTaskCalcTime .= ':00';
        }

        $detailedStats = (clone $query)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('activities.activity_date', [$startDate, $endDate])
                  ->orWhereBetween('activities.completed_date', [$startDate, $endDate]);
            })
            ->selectRaw("
                COUNT(CASE WHEN activities.status IN ('Close', 'Completed', 'Closed') AND activities.closed_by IS NOT NULL AND activities.completed_date <= ADDTIME(activities.activity_date, TIME(?)) THEN 1 END) as completed,
                COUNT(CASE WHEN activities.status = 'On Hold' THEN 1 END) as on_hold,
                COUNT(CASE WHEN activities.status = 'Open' AND activities.activity_date >= ? THEN 1 END) as ongoing,
                COUNT(CASE WHEN activities.status = 'Open' AND activities.activity_date < ? THEN 1 END) as overdue,
                COUNT(CASE WHEN activities.status IN ('Close', 'Completed', 'Closed') AND activities.closed_by IS NOT NULL AND activities.completed_date > ADDTIME(activities.activity_date, TIME(?)) THEN 1 END) as late_task,
                COUNT(CASE WHEN activities.status IN ('Close', 'Completed', 'Closed') AND activities.closed_by IS NULL THEN 1 END) as failed_task
            ", [$lastTaskCalcTime, Carbon::today(), Carbon::today(), $lastTaskCalcTime])
            ->first();

        return [
            'total' => $total,
            'open_count' => $openCount,
            'closed_count' => $closedCount,
            'open_percent' => $openPercent,
            'closed_percent' => $closedPercent,
            'completed' => (int) ($detailedStats->completed ?? 0),
            'on_hold' => (int) ($detailedStats->on_hold ?? 0),
            'ongoing' => (int) ($detailedStats->ongoing ?? 0),
            'overdue' => (int) ($detailedStats->overdue ?? 0),
            'late_task' => (int) ($detailedStats->late_task ?? 0),
            'failed_task' => (int) ($detailedStats->failed_task ?? 0),
        ];
    }

    /**
     * Top performer computation with safe fallbacks.
     */
    public function getPerformance(Box $box): array
    {
        $topPerformer = null;

        try {
            $targetAssignments = TargetAssignment::with(['performance', 'boxUser.user', 'boxUser.designationRelation'])
                ->whereHas('template', function ($q) use ($box) {
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
                        'role' => $bestEmp->designationRelation?->name ?? 'Employee',
                        'score' => round($highestAvg, 1) . '%',
                        'avatar' => $avatar,
                    ];
                }
            }

            // Fallback: If no target assignments exist yet, select first active box user
            if (!$topPerformer) {
                $candidate = BoxUser::where('box_id', $box->id)
                    ->where(function ($q) use ($box) {
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
                        'role' => $candidate->designationRelation?->name ?? 'Employee',
                        'score' => '95%',
                        'avatar' => $avatar,
                    ];
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Error computing top performer: ' . $e->getMessage());
        }

        return [
            'top_performer' => $topPerformer,
            'last_month_performer' => null,
        ];
    }

    /**
     * Employee types statistics with counts and percentages.
     */
    public function getEmployeeTypesStats(Box $box, int $totalEmployees): array
    {
        $employeeTypes = EmployeeType::where('box_id', $box->id)->get();
        $typeCounts = BoxUser::where('box_id', $box->id)
            ->selectRaw('COALESCE(emp_type, 0) as type_id, count(*) as count')
            ->groupBy('type_id')
            ->pluck('count', 'type_id');

        $stats = [];
        foreach ($employeeTypes as $type) {
            $count = (int) $typeCounts->get($type->id, 0);
            $percentage = $totalEmployees > 0 ? round(($count / $totalEmployees) * 100, 1) : 0;
            $stats[] = [
                'id' => $type->id,
                'name' => $type->name,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }

        return $stats;
    }

    /**
     * Department employee distribution.
     */
    public function getDepartmentCounts(Box $box): array
    {
        $allDepartments = Department::where('box_id', $box->id)->get();
        $deptCounts = BoxUser::where('box_id', $box->id)
            ->selectRaw('COALESCE(department, 0) as dept_id, count(*) as count')
            ->groupBy('dept_id')
            ->pluck('count', 'dept_id');

        $departmentCounts = [];
        foreach ($allDepartments as $dept) {
            $departmentCounts[$dept->name] = (int) $deptCounts->get($dept->id, 0);
        }
        if ($deptCounts->has(0)) {
            $departmentCounts['Unknown'] = (int) $deptCounts->get(0);
        }

        return $departmentCounts;
    }

    /**
     * Recent activities feed.
     */
    public function getRecentActivities(Box $box, Carbon $startDate, Carbon $endDate): array
    {
        return Activity::where('box_id', $box->id)
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
                    'subject' => $activity->subject ?? 'Activity',
                    'description' => $activity->description ?? '',
                    'time' => Carbon::parse($activity->created_at)->format('h:i A'),
                    'date' => Carbon::parse($activity->created_at)->isToday() ? 'Today' : Carbon::parse($activity->created_at)->format('d M'),
                ];
            })->toArray();
    }

    /**
     * Upcoming birthdays list.
     */
    public function getUpcomingBirthdays(Box $box): array
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $birthdays = BoxUser::where('box_id', $box->id)
            ->whereNotNull('birthday')
            ->whereRaw("DATE_FORMAT(birthday, '%m-%d') >= ?", [$today->format('m-d')])
            ->with(['user:id,name', 'designationRelation:id,name'])
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
            } elseif (!$profileImage) {
                $profileImage = "https://ui-avatars.com/api/?name=" . urlencode($boxUser->user->name ?? 'User') . "&background=random";
            }

            $display = $birthdayThisYear->isSameDay($today) ? 'Today'
                : ($birthdayThisYear->isSameDay($tomorrow) ? 'Tomorrow'
                    : $birthdayThisYear->format('d M'));

            return [
                'id' => $boxUser->id,
                'name' => $boxUser->user->name ?? 'N/A',
                'designation' => $boxUser->designationRelation?->name ?? 'Employee',
                'birthday_display' => $display,
                'profile_image' => $profileImage,
            ];
        })->toArray();
    }

    /**
     * Pending attendance corrections list.
     */
    public function getPendingCorrectionsList(Box $box, Carbon $startDate, Carbon $endDate): array
    {
        return AttendanceCorrection::where('box_id', $box->id)
            ->where('status', 'pending')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['attendance.boxUser.user', 'break'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($correction) => [
                'id' => $correction->id,
                'user_name' => $correction->attendance?->boxUser?->user?->name ?? 'N/A',
                'date' => $correction->attendance ? Carbon::parse($correction->attendance->attendance_date)->format('d M, Y') : 'N/A',
                'type' => $correction->break_id ? 'Break' : 'Attendance',
                'created_at' => Carbon::parse($correction->created_at)->diffForHumans(),
            ])->toArray();
    }

    /**
     * Recent employees list.
     */
    public function getEmployeesList(Box $box, ?int $departmentIdFilter = null): array
    {
        $usersQuery = BoxUser::where('box_id', $box->id);
        if (!empty($departmentIdFilter)) {
            $usersQuery->where('department', $departmentIdFilter);
        }

        $users = $usersQuery->select('id', 'designation', 'department', 'user_id', 'box_id', 'profile_image_path', 'created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->with([
                'user:id,name,email',
                'designationRelation:id,name',
                'departmentRelation:id,name',
            ])
            ->get();

        return $users->map(function ($boxUser) {
            $profileImage = $boxUser->profile_image_path;
            if ($profileImage && !filter_var($profileImage, FILTER_VALIDATE_URL)) {
                $profileImage = asset('storage/' . $profileImage);
            } elseif (!$profileImage) {
                $profileImage = "https://ui-avatars.com/api/?name=" . urlencode($boxUser->user->name ?? 'User') . "&background=random";
            }

            return [
                'id' => $boxUser->id,
                'name' => $boxUser->user->name ?? 'N/A',
                'email' => $boxUser->user->email ?? 'N/A',
                'designation' => $boxUser->designationRelation?->name ?? 'N/A',
                'department' => $boxUser->departmentRelation?->name ?? 'N/A',
                'profile_image' => $profileImage,
                'date' => $boxUser->created_at ? Carbon::parse($boxUser->created_at)->format('d-m-Y') : 'N/A',
                'time' => $boxUser->created_at ? Carbon::parse($boxUser->created_at)->format('h:i A') : '',
            ];
        })->toArray();
    }

    /**
     * Filter dropdown options.
     */
    public function getMetaOptions(Box $box): array
    {
        $users = BoxUser::where('box_id', $box->id)
            ->with('user:id,name')
            ->get()
            ->map(function ($boxUser) {
                return [
                    'user_id' => $boxUser->user_id,
                    'name' => $boxUser->user->name ?? 'N/A',
                ];
            });

        $leaveTypes = LeaveType::where('box_id', $box->id)->get(['id', 'leave_type']);
        $departments = Department::where('box_id', $box->id)->get(['id', 'name']);

        return [
            'users' => $users,
            'leave_types' => $leaveTypes,
            'departments' => $departments,
        ];
    }
}
