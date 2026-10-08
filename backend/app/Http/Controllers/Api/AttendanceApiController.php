<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\BoxUser;
use App\Jobs\UpdateAttendanceStatuses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AttendanceApiController extends Controller
{
    public function adminAttendance(Request $request)
    {
        try {
            $user = Auth::id();

            $boxUser = BoxUser::where('user_id', $user)->select('id', 'box_id')->first();
            if (!$boxUser) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Box user not found.',
                ], 404);
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
                ->where('box_id', $boxId)
                ->whereDate('attendance_date', '<=', $today)
                ->orderBy('attendance_date', 'desc')
                ->orderBy('id', 'desc');

            // Apply filters
            if ($request->filled('from_date') && $request->filled('to_date')) {
                try {
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

            if ($request->filled('search')) {
                $keyword = $request->search;
                $query->whereHas('boxUser.user', function($q) use($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            }

            /**
             * Update attendance statuses if:
             * - Date is not today => immediately update
             * - If today => compare with end time
             */
            $attendanceToUpdate = [];
            $attendanceItemsForUpdate = (clone $query)->whereNull('status_updated_at')->get();

            foreach ($attendanceItemsForUpdate as $attendance) {
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
                    // Extract just the time portion if endTime happens to be a full date string
                    $timeOnly = Carbon::parse($endTime)->format('H:i:s');
                    $fullEndDateTime = Carbon::parse($attendanceDate . ' ' . $timeOnly);
                    
                    if (now()->greaterThanOrEqualTo($fullEndDateTime)) {
                        $attendanceToUpdate[] = $attendance->id;
                    }
                } catch (\Exception $e) {
                    // Ignore invalid end times
                }
            }

            // Dispatch update job for qualified attendance IDs
            if (!empty($attendanceToUpdate)) {
                UpdateAttendanceStatuses::dispatch($attendanceToUpdate);
            }

            $perPage = $request->input('per_page', 25);
            $paginator = $query->paginate($perPage);

            // Transform the data to match Datatables fields exactly
            $paginator->getCollection()->transform(function ($attendance) {
                $userProfileImage = $attendance->boxUser->user->profile_image_path ?? null;
                $boxProfileImage = $attendance->boxUser->profile_image_path ?? null;
                $profileImageUrl = $userProfileImage
                        ? asset('storage/' . $userProfileImage)
                        : ($boxProfileImage ? asset('storage/' . $boxProfileImage) : null);

                $attendance->user_name = $attendance->boxUser && $attendance->boxUser->user
                    ? ($attendance->boxUser->user->name ?? 'N/A')
                    : 'N/A';
                $attendance->email = $attendance->boxUser && $attendance->boxUser->user
                    ? ($attendance->boxUser->user->email ?? 'N/A')
                    : 'N/A';
                $attendance->profile_image = $profileImageUrl;
                $attendance->profile_image_url = $profileImageUrl;
                $attendance->corrections_statuses = $attendance->corrections ? $attendance->corrections->toArray() : [];

                if (!$attendance->boxUser) {
                    $attendance->box_user = [
                        'id' => null,
                        'employee_id' => null,
                        'designation_relation' => ['name' => null],
                        'department_relation' => ['name' => null],
                        'shift' => null
                    ];
                } else {
                    $attendance->box_user = [
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
                }
                
                $attendance->status = strtolower($attendance->status ?? 'N/A');
                
                try {
                    $attendance->attendance_date = $attendance->attendance_date
                        ? Carbon::parse($attendance->attendance_date)->format('Y-m-d H:i:s')
                        : 'N/A';
                } catch (\Exception $e) {
                    // fallback
                }
                
                return $attendance;
            });

            return response()->json([
                'success' => true,
                'data' => $paginator->items(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ]
            ]);

        } catch (\Throwable $th) {
            Log::error('API Admin Attendance Error: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'An error occurred while fetching employee attendance.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function teamAttendance(Request $request)
    {
        try {
            $user = Auth::id();

            $boxUser = BoxUser::where('user_id', $user)->select('id', 'box_id')->first();
            if (!$boxUser) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Box user not found.',
                ], 404);
            }

            $boxId = $boxUser->box_id;
            $today = Carbon::today()->toDateString();
            $userModel = Auth::user();

            $allSubordinateIds = BoxUser::getAllSubordinateIds($boxId, $userModel->id, $boxUser);

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
                ->where('box_id', $boxId)
                ->whereIn('box_user_id', $allSubordinateIds) // Filter by subordinates only
                ->whereDate('attendance_date', '<=', $today)
                ->orderBy('attendance_date', 'desc')
                ->orderBy('id', 'desc');

            // Apply filters
            if ($request->filled('from_date') && $request->filled('to_date')) {
                try {
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

            if ($request->filled('search')) {
                $keyword = $request->search;
                $query->whereHas('boxUser.user', function($q) use($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            }

            /**
             * Update attendance statuses if:
             * - Date is not today => immediately update
             * - If today => compare with end time
             */
            $attendanceToUpdate = [];
            $attendanceItemsForUpdate = (clone $query)->whereNull('status_updated_at')->get();

            foreach ($attendanceItemsForUpdate as $attendance) {
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
                    $timeOnly = Carbon::parse($endTime)->format('H:i:s');
                    $fullEndDateTime = Carbon::parse($attendanceDate . ' ' . $timeOnly);
                } catch (\Exception $e) {
                    continue; // Skip invalid end time formats
                }
                
                if (now()->greaterThanOrEqualTo($fullEndDateTime)) {
                    $attendanceToUpdate[] = $attendance->id;
                }
            }

            // Dispatch update job for qualified attendance IDs
            if (!empty($attendanceToUpdate)) {
                UpdateAttendanceStatuses::dispatch($attendanceToUpdate);
            }

            $perPage = $request->input('per_page', 25);
            $paginator = $query->paginate($perPage);

            // Transform the data
            $paginator->getCollection()->transform(function ($attendance) {
                $userProfileImage = $attendance->boxUser->user->profile_image_path ?? null;
                $boxProfileImage = $attendance->boxUser->profile_image_path ?? null;
                $profileImageUrl = $userProfileImage
                        ? asset('storage/' . $userProfileImage)
                        : ($boxProfileImage ? asset('storage/' . $boxProfileImage) : null);

                $attendance->user_name = $attendance->boxUser && $attendance->boxUser->user
                    ? ($attendance->boxUser->user->name ?? 'N/A')
                    : 'N/A';
                $attendance->email = $attendance->boxUser && $attendance->boxUser->user
                    ? ($attendance->boxUser->user->email ?? 'N/A')
                    : 'N/A';
                $attendance->profile_image = $profileImageUrl;
                $attendance->profile_image_url = $profileImageUrl;
                $attendance->corrections_statuses = $attendance->corrections ? $attendance->corrections->toArray() : [];

                if (!$attendance->boxUser) {
                    $attendance->box_user = [
                        'id' => null,
                        'employee_id' => null,
                        'designation_relation' => ['name' => null],
                        'department_relation' => ['name' => null],
                        'work_mode' => ['name' => null],
                        'shift' => null
                    ];
                } else {
                    $attendance->box_user = [
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
                }
                
                $attendance->status = strtolower($attendance->status ?? 'N/A');
                
                try {
                    $attendance->attendance_date = $attendance->attendance_date
                        ? Carbon::parse($attendance->attendance_date)->format('Y-m-d H:i:s')
                        : 'N/A';
                } catch (\Exception $e) {
                    // fallback
                }
                
                return $attendance;
            });

            return response()->json([
                'success' => true,
                'data' => $paginator->items(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ]
            ]);

        } catch (\Throwable $th) {
            Log::error('API Team Attendance Error: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'An error occurred while fetching team attendance.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function employeeAttendance(Request $request)
    {
        try {
            $user = Auth::id();

            $boxUser = BoxUser::where('user_id', $user)->select('id', 'box_id')->first();
            if (!$boxUser) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Box user not found.',
                ], 404);
            }

            $boxId = $boxUser->box_id;
            $today = Carbon::today()->toDateString();
            
            $query = Attendance::with([
                'boxUser.user',
                'corrections.break',
                'boxUser.departmentRelation',
                'boxUser.shift',
                'machine'
            ])
            ->where('box_user_id', $boxUser->id)
            ->whereDate('attendance_date', '<=', $today);

            // Apply Filters (Same as Admin/Team)
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

            $perPage = $request->input('per_page', 25);
            $paginator = $query->paginate($perPage);

            // Calculate metrics for current result set
            $totals = [
                'total_records' => $paginator->total(),
                'total_present' => 0,
                'total_absent' => 0,
                'total_late' => 0,
                'total_half_day' => 0,
            ];

            // Re-clone query for totals across all pages for the filtered set
            $totalsQuery = clone $query;
            $allFilteredRecords = $totalsQuery->get(['status']);
            foreach ($allFilteredRecords as $record) {
                if (strtolower($record->status) === 'present') {
                    $totals['total_present']++;
                } elseif (strtolower($record->status) === 'absent') {
                    $totals['total_absent']++;
                } elseif (strtolower($record->status) === 'late') {
                    $totals['total_late']++;
                } elseif (strtolower($record->status) === 'half day') {
                    $totals['total_half_day']++;
                }
            }

            // Transform data structure to match expected output
            $transformedData = collect($paginator->items())->map(function ($attendance) {
                $employee = $attendance->boxUser->user;
                $profileImage = null;
                if ($employee && $employee->profile) {
                    if (filter_var($employee->profile, FILTER_VALIDATE_URL)) {
                        $profileImage = $employee->profile;
                    } else {
                        $profileImage = url('storage/' . ltrim($employee->profile, '/'));
                    }
                } else {
                    $profileImage = url('assets/img/profiles/avatar-02.jpg');
                }

                $shift = clone $attendance; 
                if ($attendance->boxUser && $attendance->boxUser->shift) {
                    $shift->shift_name = $attendance->boxUser->shift->name;
                    $shift->shift_start = $attendance->boxUser->shift->start_time;
                    $shift->shift_end = $attendance->boxUser->shift->end_time;
                }

                return [
                    'id' => $attendance->id,
                    'attendance_date' => $attendance->attendance_date,
                    'status' => $attendance->status,
                    'check_in' => $attendance->check_in,
                    'check_out' => $attendance->check_out,
                    'late_minutes' => $attendance->late_minutes,
                    'early_minutes' => $attendance->early_minutes,
                    'work_hours' => $attendance->work_hours,
                    'overtime' => $attendance->overtime,
                    'break_duration' => $attendance->break_duration,
                    'correction_status' => $attendance->corrections->pluck('status')->last() ?? 'none',
                    'machine' => $attendance->machine ? $attendance->machine->machine_name : null,
                    'employee' => [
                        'id' => $employee ? $employee->id : null,
                        'name' => $employee ? $employee->name : 'N/A',
                        'email' => $employee ? $employee->email : 'N/A',
                        'profile_image' => $profileImage,
                        'department' => $attendance->boxUser->departmentRelation ? $attendance->boxUser->departmentRelation->name : 'N/A',
                        'designation' => $attendance->boxUser->designation,
                        'employee_id' => $attendance->boxUser->employee_id,
                    ],
                    'shift' => [
                        'name' => $shift->shift_name ?? 'N/A',
                        'start_time' => $shift->shift_start ?? null,
                        'end_time' => $shift->shift_end ?? null,
                    ],
                ];
            });

            return response()->json([
                'success' => true,
                'totals' => $totals,
                'data' => $transformedData,
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Failed to fetch attendance data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
