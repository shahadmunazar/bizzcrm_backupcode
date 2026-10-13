<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\{BoxUser, EmployeeType, Asset, AssetType, AssignedAsset, HrDocument, Todo};
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeFrontendController extends Controller
{

    public function Leave_EmployeeAdmin(Request $request)
    {
        $box = $request->get('box');
        if (!$box) {
            return redirect()->back()->with('error', 'Box not found');
        }

        $now = \Carbon\Carbon::now();
        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = $now->copy()->endOfYear();

        // 1. Fetch Key Stats for Cards
        $stats = [
            'total' => \App\Models\LeaveRequest::where('box_id', $box->id)->count(),
            'approved' => \App\Models\LeaveRequest::where('box_id', $box->id)->where('status', 'Approved')->count(),
            'pending' => \App\Models\LeaveRequest::where('box_id', $box->id)->where('status', 'Pending')->count(),
            'declined' => \App\Models\LeaveRequest::where('box_id', $box->id)->whereIn('status', ['Rejected', 'Declined'])->count(),
        ];

        // Last Month Stats for percentage calculation (Optional, but makes it look premium)
        $lastMonthStart = \Carbon\Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = \Carbon\Carbon::now()->subMonth()->endOfMonth();

        $lastMonthStats = [
            'total' => \App\Models\LeaveRequest::where('box_id', $box->id)->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count(),
            'approved' => \App\Models\LeaveRequest::where('box_id', $box->id)->where('status', 'Approved')->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count(),
            'pending' => \App\Models\LeaveRequest::where('box_id', $box->id)->where('status', 'Pending')->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count(),
            'declined' => \App\Models\LeaveRequest::where('box_id', $box->id)->whereIn('status', ['Rejected', 'Declined'])->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count(),
        ];

        // Calculate simple trend percentages
        foreach ($stats as $key => $value) {
            $prev = $lastMonthStats[$key] ?? 0;
            $stats[$key . '_trend'] = $prev > 0 ? round((($value - $prev) / $prev) * 100, 2) : ($value > 0 ? 100 : 0);
        }

        // 2. Fetch Chart Data (Leaves by Month and Type)
        // We'll group by month and leave_type name
        $chartDataRaw = \App\Models\LeaveRequest::where('box_id', $box->id)
            ->whereBetween('from_date', [$startOfYear->format('Y-m-d'), $endOfYear->format('Y-m-d')])
            ->with('leaveType')
            ->get();

        $monthlyData = [];
        $categories = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Initialize structure
        $types = \App\Models\LeaveType::where('box_id', $box->id)->pluck('leave_type')->toArray();
        if (empty($types)) {
            $types = ['Annual', 'Casual', 'Medical', 'Others'];
        }

        $series = [];
        foreach ($types as $type) {
            $series[$type] = array_fill(0, 12, 0);
        }

        foreach ($chartDataRaw as $leave) {
            $month = \Carbon\Carbon::parse($leave->from_date)->month - 1; // 0-indexed for array
            $typeName = $leave->leaveType->leave_type ?? 'Others';

            if (isset($series[$typeName])) {
                $series[$typeName][$month] += $leave->total_days ?? 1;
            }
            else {
                // Fallback for types not strictly in the current box list if any
                $series['Others'][$month] = ($series['Others'][$month] ?? 0) + ($leave->total_days ?? 1);
            }
        }

        // Format for ApexCharts
        $formattedSeries = [];
        foreach ($series as $name => $data) {
            $cleanData = array_fill(0, 12, 0);
            foreach ($data as $monthIndex => $val) {
                $cleanData[$monthIndex] = $val;
            }
            $formattedSeries[] = ['name' => $name, 'data' => array_values($cleanData)];
        }

        return view('frontend.attendance.adminrequest', compact('stats', 'formattedSeries', 'categories'));
    }

    /**
     * API endpoint to fetch the Admin Leave Dashboard info (Stats + Chart Data).
     */
    public function apiAdminLeaveDashboard(Request $request)
    {
        try {
            $user = Auth::user();
            $boxUser = BoxUser::where('user_id', $user->id)->first();
            
            if (!$boxUser) {
                return response()->json(['success' => false, 'message' => 'Box user not found'], 404);
            }
            
            $boxId = $boxUser->box_id;
            $now = \Carbon\Carbon::now();
            $startOfYear = $now->copy()->startOfYear();
            $endOfYear = $now->copy()->endOfYear();

            // 1. Fetch Key Stats for Cards
            $stats = [
                'total' => \App\Models\LeaveRequest::where('box_id', $boxId)->count(),
                'approved' => \App\Models\LeaveRequest::where('box_id', $boxId)->where('status', 'Approved')->count(),
                'pending' => \App\Models\LeaveRequest::where('box_id', $boxId)->where('status', 'Pending')->count(),
                'declined' => \App\Models\LeaveRequest::where('box_id', $boxId)->whereIn('status', ['Rejected', 'Declined'])->count(),
            ];

            // Last Month Stats for percentage calculation
            $lastMonthStart = \Carbon\Carbon::now()->subMonth()->startOfMonth();
            $lastMonthEnd = \Carbon\Carbon::now()->subMonth()->endOfMonth();

            $lastMonthStats = [
                'total' => \App\Models\LeaveRequest::where('box_id', $boxId)->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count(),
                'approved' => \App\Models\LeaveRequest::where('box_id', $boxId)->where('status', 'Approved')->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count(),
                'pending' => \App\Models\LeaveRequest::where('box_id', $boxId)->where('status', 'Pending')->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count(),
                'declined' => \App\Models\LeaveRequest::where('box_id', $boxId)->whereIn('status', ['Rejected', 'Declined'])->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count(),
            ];

            // Calculate simple trend percentages
            foreach ($stats as $key => $value) {
                $prev = $lastMonthStats[$key] ?? 0;
                $stats[$key . '_trend'] = $prev > 0 ? round((($value - $prev) / $prev) * 100, 2) : ($value > 0 ? 100 : 0);
            }

            // 2. Fetch Chart Data (Leaves by Month and Type)
            $chartDataRaw = \App\Models\LeaveRequest::where('box_id', $boxId)
                ->whereBetween('from_date', [$startOfYear->format('Y-m-d'), $endOfYear->format('Y-m-d')])
                ->with('leaveType')
                ->get();

            $categories = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $types = \App\Models\LeaveType::where('box_id', $boxId)->pluck('leave_type')->toArray();
            
            if (empty($types)) {
                $types = ['Annual', 'Casual', 'Medical', 'Others'];
            }

            $series = [];
            foreach ($types as $type) {
                $series[$type] = array_fill(0, 12, 0);
            }

            foreach ($chartDataRaw as $leave) {
                $month = \Carbon\Carbon::parse($leave->from_date)->month - 1; // 0-indexed for array
                $typeName = $leave->leaveType->leave_type ?? 'Others';

                if (isset($series[$typeName])) {
                    $series[$typeName][$month] += $leave->total_days ?? 1;
                } else {
                    $series['Others'][$month] = ($series['Others'][$month] ?? 0) + ($leave->total_days ?? 1);
                }
            }

            // Format for ApexCharts
            $formattedSeries = [];
            foreach ($series as $name => $data) {
                $cleanData = array_fill(0, 12, 0);
                foreach ($data as $monthIndex => $val) {
                    $cleanData[$monthIndex] = $val;
                }
                $formattedSeries[] = ['name' => $name, 'data' => array_values($cleanData)];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'chart' => [
                        'series' => $formattedSeries,
                        'categories' => $categories
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch admin leave dashboard.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function employee_assets()
    {
        $box_id = Auth::user()->boxUser->box_id ?? BoxUser::where('user_id', Auth::id())->value('box_id');
        $assetTypes = AssetType::where('box_id', $box_id)->get();
        $assets = Asset::where('box_id', $box_id)->with(['assetType', 'assignedToBoxUser'])->get();
        $boxUsers = BoxUser::where('box_id', $box_id)->with('user:id,name')->get();
        $assignmentHistory = AssignedAsset::where('box_id', $box_id)->with(['asset', 'boxUser.user'])->latest()->get();

        return view('frontend.attendance.employee.assets', compact('assetTypes', 'assets', 'boxUsers', 'assignmentHistory'));
    }
    public function employee_hr_documents()
    {
        $box_id = Auth::user()->boxUser->box_id ?? BoxUser::where('user_id', Auth::id())->value('box_id');
        
        $documents = HrDocument::where('box_id', $box_id)->latest()->get();

        return view('frontend.attendance.employee.hr_documents', compact('documents'));
    }

    public function store_hr_document(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,png,jpeg|max:10240',
        ]);

        $boxUser = Auth::user()->boxUser ?? BoxUser::where('user_id', Auth::id())->first();
        $box_id = $boxUser->box_id ?? null;
        $box_user_id = $boxUser->id ?? null;
        $user_id = Auth::id();

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            // Store file securely
            $path = $file->storeAs('hr_documents', $filename, 'public');

            HrDocument::create([
                'box_id' => $box_id,
                'box_user_id' => $box_user_id,
                'name' => $request->name,
                'category' => $request->category,
                'file_path' => $path,
                'uploaded_by' => $user_id,
            ]);

            return redirect()->back()->with('success', 'Document uploaded successfully.');
        }

        return redirect()->back()->with('error', 'File upload failed.');
    }

    public function delete_hr_document($id)
    {
        $document = HrDocument::findOrFail($id);
        
        // Ensure user belongs to the same box
        $box_id = Auth::user()->boxUser->box_id ?? BoxUser::where('user_id', Auth::id())->value('box_id');
        if ($document->box_id != $box_id) {
            abort(403, 'Unauthorized access.');
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->back()->with('success', 'Document deleted successfully.');
    }
    public function employee_letter_generation()
    {
        return view('frontend.attendance.employee.letter_generation');
    }

    public function employe_dashboard()
    {
        $user = Auth::user();
        $user_id = $user->id;

        $employee = BoxUser::where('user_id', $user_id)
            ->with([
                'shift',
                'box',
                'departmentRelation',
                'designationRelation',
                'assignedOffices.branch:id,name',
            ])
            ->first();

        // Pre-format report offices (from assignedOffices) for easy use in Blade
        $reportOffices = collect();
        if ($employee) {
            $reportOffices = $employee->assignedOffices
                ? $employee->assignedOffices->map(function ($assignment) {
                    return [
                        'id' => $assignment->branch_office_id,
                        'name' => $assignment->branch->name ?? 'Office',
                        'logo' => $assignment->branch->logo ?? null,
                    ];
                })
                : collect();
        }

        // Fetch recent notifications
        $notifications = $user->notifications()->take(10)->get();

        $latestLeave = \App\Models\LeaveRequest::where('created_by', $user_id)->latest()->first();
        $latestAttendance = \App\Models\AttendanceCorrection::where('created_by', $user_id)->latest()->first();

        $latestRequest = null;
        $requestType = '';

        if ($latestLeave && $latestAttendance) {
            if ($latestLeave->created_at > $latestAttendance->created_at) {
                $latestRequest = $latestLeave;
                $requestType = 'Leave';
            } else {
                $latestRequest = $latestAttendance;
                $requestType = 'Attendance';
            }
        } elseif ($latestLeave) {
            $latestRequest = $latestLeave;
            $requestType = 'Leave';
        } elseif ($latestAttendance) {
            $latestRequest = $latestAttendance;
            $requestType = 'Attendance';
        }

        return view('frontend.employess.index', compact('employee', 'user', 'reportOffices', 'notifications', 'latestRequest', 'requestType'));
    }

    /**
     * API endpoint to fetch dashboard initial info.
     */
    public function apiDashboardInfo()
    {
        try {
            $user = Auth::user();
            $user_id = $user->id;

            $employee = BoxUser::where('user_id', $user_id)
                ->with([
                    'shift',
                    'box',
                    'departmentRelation',
                    'designationRelation',
                    'assignedOffices.branch:id,name',
                ])
                ->first();

            $reportOffices = collect();
            if ($employee) {
                $reportOffices = $employee->assignedOffices
                    ? $employee->assignedOffices->map(function ($assignment) {
                        return [
                            'id' => $assignment->branch_office_id,
                            'name' => $assignment->branch->name ?? 'Office',
                            'logo' => $assignment->branch->logo ?? null,
                        ];
                    })
                    : collect();
            }

            $latestLeave = \App\Models\LeaveRequest::where('created_by', $user_id)->latest()->first();
            $latestAttendance = \App\Models\AttendanceCorrection::where('created_by', $user_id)->latest()->first();

            $latestRequest = null;
            $requestType = '';

            if ($latestLeave && $latestAttendance) {
                if ($latestLeave->created_at > $latestAttendance->created_at) {
                    $latestRequest = $latestLeave;
                    $requestType = 'Leave';
                } else {
                    $latestRequest = $latestAttendance;
                    $requestType = 'Attendance';
                }
            } elseif ($latestLeave) {
                $latestRequest = $latestLeave;
                $requestType = 'Leave';
            } elseif ($latestAttendance) {
                $latestRequest = $latestAttendance;
                $requestType = 'Attendance';
            }

            $isPrivilegedUser = \App\Helpers\PermissionHelper::isPrivilegedUser();

            return response()->json([
                'success' => true,
                'data' => [
                    'employee' => $employee,
                    'report_offices' => $reportOffices,
                    'latest_request' => $latestRequest,
                    'request_type' => $requestType,
                    'permissions' => [
                        'can_view_leave_types' => $isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute('employee.leads_type'),
                        'can_view_leads' => $isPrivilegedUser || \App\Helpers\PermissionHelper::hasModulePermission('leads', 'read'),
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard info.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to fetch EVERYTHING for the dashboard in a single request.
     */
    public function apiDashboardComplete(Request $request)
    {
        try {
            $attendanceController = app(\App\Http\Controllers\Employee\AttendaceController::class);

            $infoResponse = $this->apiDashboardInfo()->getData(true);
            $notificationsResponse = $this->getLatestNotifications()->getData(true);
            $teamMembersResponse = $this->recentTeamMembers()->getData(true);
            
            $statsResponse = $attendanceController->employeeDashboardStats($request)->getData(true);
            $overviewResponse = $attendanceController->attendanceOverview($request)->getData(true);

            return response()->json([
                'success' => true,
                'data' => [
                    'info' => $infoResponse['data'] ?? null,
                    'notifications' => $notificationsResponse['data'] ?? [],
                    'team_members' => $teamMembersResponse['data'] ?? [],
                    'stats' => $statsResponse['data'] ?? null,
                    'overview' => [
                        'present' => $overviewResponse['present'] ?? 0,
                        'punch_out_miss' => $overviewResponse['punch_out_miss'] ?? 0,
                        'late' => $overviewResponse['late'] ?? 0,
                        'half_day' => $overviewResponse['half_day'] ?? 0,
                        'absent' => $overviewResponse['absent'] ?? 0,
                    ],
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch complete dashboard data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX endpoint to fetch latest notifications.
     */
    public function getLatestNotifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->take(6)->get()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'data' => $notification->data,
                'type' => class_basename($notification->type),
                'created_at' => $notification->created_at->diffForHumans(),
                'read_at' => $notification->read_at,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * Get latest 6 team members for dashboard "Team Members" widget.
     * Now filtered by current employee's box_id (same box only).
     */
    public function recentTeamMembers()
    {
        $currentUserId = Auth::id();

        // Find current employee's box_id
        $currentBoxUser = BoxUser::where('user_id', $currentUserId)->first();
        if (!$currentBoxUser || !$currentBoxUser->box_id) {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'Box user not found for current user',
            ]);
        }

        $boxId = $currentBoxUser->box_id;

        $members = BoxUser::with([
            'user:id,name',
            'designationRelation:id,name',
            'departmentRelation:id,name',
        ])
            ->where('box_id', $boxId)
            ->whereNotNull('user_id')
            ->where('id', '!=', $currentBoxUser->id)
            ->latest()
            ->take(6)
            ->get()
            ->map(function (BoxUser $user) {
                $isOwner = strtolower($user->staff_role ?? '') === 'admin';
                return [
                    'id' => $user->id,
                    'name' => $user->user->name ?? trim($user->first_name . ' ' . $user->last_name),
                    'initial' => strtoupper(mb_substr($user->user->name ?? ($user->first_name ?? 'U'), 0, 1)),
                    'employee_id' => $user->employee_id,
                    'profile_image_path' => $user->profile_image_path,
                    'designation' => $isOwner ? 'Owner' : ($user->designationRelation?->name ?? null),
                    'department' => $isOwner ? 'Owner' : ($user->departmentRelation?->name ?? null),
                ];
        });

        return response()->json([
            'status' => true,
            'data' => $members,
        ]);
    }

    public function employess()
    {
        $this->authorize('attendance.admin.read');
        $boxUser = BoxUser::where('user_id', Auth::id())->first();
        if (!$boxUser) {
            return redirect()->back()->with('error', 'Box User Not Found');
        }
        $box = \App\Models\Box::find($boxUser->box_id);
        $crmLimit = $box ? ($box->total_users ?? 0) : 0;
        $whatsappLimit = \App\Helpers\SubscriptionHelper::getWhatsAppAgentLimit(Auth::user());

        return view('frontend.employess.employess', compact('crmLimit', 'whatsappLimit'));
    }

    public function employee_grid()
    {
        $this->authorize('attendance.admin.read');
        return view('frontend.employess.employee_grid');
    }

    public function employee_details()
    {
        $this->authorize('attendance.admin.read');
        return view('frontend.employess.employee_details');

    }

    public function employee_salary()
    {
        return view('frontend.employess.employee_salary');
    }

    public function employee_report()
    {
        return view('frontend.employess.employee_report');
    }

    public function leaves()
    {
        return view('frontend.employess.leaves');
    }

    public function leaves_employee()
    {
        return view('frontend.employess.leaves_employee');
    }

    public function leave_settings()
    {
        return view('frontend.employess.leave_settings');
    }

    public function todo(Request $request)
    {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        
        $todos = collect();
        if ($boxUserEntry) {
            $query = Todo::with('creator')->where('box_id', $boxUserEntry->box_id);
            
            if ($boxUserEntry->staff_role !== 'admin') {
                $query->where('box_user_id', $boxUserEntry->id);
            }
            
            if ($request->filled('date_range')) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) == 2) {
                    try {
                        $start = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                        $end = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                        $query->whereBetween('due_date', [$start, $end]);
                    } catch (\Exception $e) {
                        // ignore invalid dates
                    }
                }
            }

            if ($request->filled('sort')) {
                switch ($request->sort) {
                    case 'asc':
                        $query->orderBy('title', 'asc');
                        break;
                    case 'desc':
                        $query->orderBy('title', 'desc');
                        break;
                    case 'recent_viewed':
                        $query->orderBy('updated_at', 'desc');
                        break;
                    case 'recent_added':
                        $query->orderBy('created_at', 'desc');
                        break;
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }
            
            $todos = $query->get();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $todos
            ]);
        }

        $boxUsers = BoxUser::where('box_id', $boxUserEntry->box_id ?? 0)->get();

        return view('user_management.todo', compact('boxUsers'));
    }

    public function taskStats($box_slug)
    {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        
        if (!$boxUserEntry) {
            return response()->json(['success' => false, 'message' => 'User not associated with any box.'], 403);
        }

        $boxUsers = BoxUser::where('box_id', $boxUserEntry->box_id)->get();
        $todos = Todo::where('box_id', $boxUserEntry->box_id)->get();

        $stats = [];
        foreach ($boxUsers as $bUser) {
            $userId = (string)$bUser->id;
            
            $pending = 0;
            $inProgress = 0;
            $completed = 0;

            foreach ($todos as $todo) {
                $responsible = [];
                if (is_array($todo->responsible_persons)) {
                    $responsible = $todo->responsible_persons;
                } else if (is_string($todo->responsible_persons)) {
                    $decoded = json_decode($todo->responsible_persons, true);
                    if (is_array($decoded)) {
                        $responsible = $decoded;
                    } else {
                        $responsible = [$todo->responsible_persons];
                    }
                }
                
                if (in_array($userId, $responsible) || in_array((int)$userId, $responsible)) {
                    $status = strtolower($todo->status ?? 'pending');
                    if ($status === 'completed') {
                        $completed++;
                    } elseif ($status === 'in progress') {
                        $inProgress++;
                    } else {
                        $pending++;
                    }
                }
            }

            $stats[] = [
                'user_name' => trim($bUser->first_name . ' ' . $bUser->last_name),
                'avatar' => $bUser->profile_image_path ? asset($bUser->profile_image_path) : asset('assets/img/profiles/avatar-19.jpg'),
                'pending' => $pending,
                'in_progress' => $inProgress,
                'completed' => $completed,
                'total' => $pending + $inProgress + $completed
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function storeTodo(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'due_date' => 'required|date',
            'description' => 'required|string',
        ]);

        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();

        if (!$boxUserEntry) {
            return redirect()->back()->with('error', 'User not associated with any box.');
        }

        $tags = null;
        if ($request->filled('tags')) {
            $tags = $request->tags;
        }

        $todo = Todo::create([
            'box_id' => $boxUserEntry->box_id,
            'box_user_id' => $boxUserEntry->id,
            'title' => $request->title,
            'category' => $request->category,
            'responsible_persons' => $request->responsible_persons,
            'start_date' => $request->start_date ? \Carbon\Carbon::parse($request->start_date) : null,
            'due_date' => $request->due_date ? \Carbon\Carbon::parse($request->due_date) : null,
            'tags' => $tags,
            'priority' => $request->priority ?? 'Medium',
            'status' => $request->status ?? 'Active',
            'description' => $request->description,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task created successfully.',
                'data' => $todo
            ]);
        }

        return redirect()->back()->with('success', 'Task created successfully.');
    }

    public function updateTodo(Request $request, $box_slug, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'due_date' => 'required|date',
            'description' => 'required|string',
        ]);

        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();

        if (!$boxUserEntry) {
            return response()->json(['success' => false, 'message' => 'User not associated with any box.'], 403);
        }

        $todo = Todo::where('box_id', $boxUserEntry->box_id)->findOrFail($id);

        $tags = null;
        if ($request->filled('tags')) {
            $tags = $request->tags;
        }

        $todo->update([
            'title' => $request->title,
            'category' => $request->category,
            'responsible_persons' => $request->responsible_persons,
            'start_date' => $request->start_date ? \Carbon\Carbon::parse($request->start_date) : null,
            'due_date' => $request->due_date ? \Carbon\Carbon::parse($request->due_date) : null,
            'tags' => $tags,
            'priority' => $request->priority ?? 'Medium',
            'status' => $request->status ?? 'Active',
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully.',
            'data' => $todo
        ]);
    }

    public function updateStatus(Request $request, $box_slug, $id)
    {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();

        if (!$boxUserEntry) {
            return response()->json(['success' => false, 'message' => 'User not associated with any box.'], 403);
        }

        $todo = Todo::where('box_id', $boxUserEntry->box_id)->findOrFail($id);

        // Permissions: Admin can do anything. Employee can only mark as done if they created it or are assigned to it.
        if ($boxUserEntry->staff_role !== 'admin') {
            $isCreator = ($todo->box_user_id == $boxUserEntry->id);
            $userId = (string)$boxUserEntry->id;
            
            $responsible = [];
            if (is_array($todo->responsible_persons)) {
                $responsible = $todo->responsible_persons;
            } else if (is_string($todo->responsible_persons)) {
                $decoded = json_decode($todo->responsible_persons, true);
                if (is_array($decoded)) {
                    $responsible = $decoded;
                } else {
                    $responsible = [$todo->responsible_persons];
                }
            }

            $isAssigned = in_array($userId, $responsible) || in_array((int)$userId, $responsible);

            if (!$isCreator && !$isAssigned) {
                return response()->json(['success' => false, 'message' => 'You do not have permission to update this task.'], 403);
            }
        }

        $todo->update([
            'status' => $request->status ?? 'Completed'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully.',
            'data' => $todo
        ]);
    }

    public function destroyTodo($box_slug, $id)
    {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();

        if (!$boxUserEntry) {
            return response()->json(['success' => false, 'message' => 'User not associated with any box.'], 403);
        }

        $todo = Todo::where('box_id', $boxUserEntry->box_id)->findOrFail($id);
        $todo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully.'
        ]);
    }



    public function deals()
    {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUserEntry) abort(403, 'User not assigned to any box.');

        $totalDeals = \App\Models\Deal::where('box_id', $boxUserEntry->box_id)->count();

        return view('user_management.deals', compact('totalDeals'));
    }

    public function searchLeads(Request $request)
    {
        $boxUserEntry = \App\Models\BoxUser::where('user_id', \Illuminate\Support\Facades\Auth::id())->first();
        $box_id = $boxUserEntry ? $boxUserEntry->box_id : null;
        
        $search = $request->get('q', '');
        $page = $request->get('page', 1);
        $perPage = 20;

        $query = \App\Models\Lead::where('box_id', $box_id);
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $totalCount = $query->count();
        $leads = $query->orderBy('id', 'desc')->skip(($page - 1) * $perPage)->take($perPage)->get();
        
        $results = $leads->map(function($lead) {
            return [
                'id' => $lead->id,
                'text' => $lead->name . ($lead->phone ? " ({$lead->phone})" : "")
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $totalCount
            ]
        ]);
    }

    public function getDealFormOptions()
    {
        $boxUserEntry = \App\Models\BoxUser::where('user_id', \Illuminate\Support\Facades\Auth::id())->first();
        $box_id = $boxUserEntry ? $boxUserEntry->box_id : null;
        
        $pipelines = \App\Models\Pipeline::where('box_id', $box_id)->get();
        $projects = \App\Models\Project::where('box_id', $box_id)->get();
        $contacts = \App\Models\ProjectContact::whereHas('project', function($query) use ($box_id) {
            $query->where('box_id', $box_id);
        })->get();
        $users = \App\Models\User::whereIn('id', function($query) use ($boxUserEntry) {
            $query->select('user_id')->from('box_users')->where('box_id', $boxUserEntry->box_id);
        })->get();

        $statuses = \App\Models\DealStatus::where('box_id', $box_id)->where('status', 'active')->get();
        $periods = \App\Models\DealPeriod::where('box_id', $box_id)->where('status', 'active')->get();
        $sources = \App\Models\DealSource::where('box_id', $box_id)->where('status', 'active')->get();
        $priorities = \App\Models\DealPriority::where('box_id', $box_id)->where('status', 'active')->get();
        $tags = \App\Models\DealTag::where('box_id', $box_id)->where('status', 'active')->get();

        return response()->json([
            'success' => true,
            'pipelines' => $pipelines,
            'projects' => $projects,
            'contacts' => $contacts,
            'users' => $users,
            'statuses' => $statuses,
            'periods' => $periods,
            'sources' => $sources,
            'priorities' => $priorities,
            'tags' => $tags
        ]);
    }

    public function exportDeals(Request $request)
    {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUserEntry) return redirect()->back()->with('error', 'No assigned box found.');

        $export = new \App\Exports\DealsExport($boxUserEntry->box_id, $request->start_date, $request->end_date);
        
        $type = $request->get('type', 'xlsx');
        $fileName = 'deals_export_' . date('Y_m_d_H_i_s');

        if ($type == 'pdf') {
            return \Maatwebsite\Excel\Facades\Excel::download($export, $fileName . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }

        return \Maatwebsite\Excel\Facades\Excel::download($export, $fileName . '.xlsx');
    }

    public function getDealsJson()
    {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUserEntry) return response()->json([]);

        $query = \App\Models\Deal::with(['project', 'contact', 'lead', 'creator', 'pipeline', 'dealStatus', 'dealPeriod', 'dealSource', 'dealPriority'])->where('box_id', $boxUserEntry->box_id);
        
        if (request()->has('start_date') && request()->has('end_date') && request('start_date') && request('end_date')) {
            $startDate = date('Y-m-d 00:00:00', strtotime(request('start_date')));
            $endDate = date('Y-m-d 23:59:59', strtotime(request('end_date')));
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if (request()->has('draw')) {
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('deal_name', function ($row) {
                    return '<a href="#" class="title-name text-dark fw-bold">'.$row->name.'</a>';
                })
                ->addColumn('stage_name', function ($row) {
                    return $row->stage ?? 'Unassigned';
                })
                ->addColumn('formatted_deal_value', function ($row) {
                    return $row->deal_value ? $row->currency . ' ' . number_format((float)$row->deal_value, 2) : '-';
                })
                ->addColumn('formatted_tags', function ($row) {
                    if (!$row->tags) return '-';
                    $tags = is_string($row->tags) ? json_decode($row->tags, true) : $row->tags;
                    if (is_array($tags) && count($tags) > 0) {
                        $tagNames = \App\Models\DealTag::whereIn('id', $tags)->pluck('name')->toArray();
                        return implode(', ', $tagNames);
                    }
                    return $tags;
                })
                ->addColumn('expected_close_date', function ($row) {
                    return $row->expected_close_date ? date('d M Y', strtotime($row->expected_close_date)) : '-';
                })
                ->addColumn('pipeline_name', function ($row) {
                    return $row->pipeline ? $row->pipeline->name : '-';
                })
                ->addColumn('contact_name', function ($row) {
                    return $row->contact ? $row->contact->name : '-';
                })
                ->addColumn('project_name', function ($row) {
                    return $row->project ? $row->project->name : '-';
                })
                ->addColumn('period_name', function ($row) {
                    return $row->dealPeriod ? $row->dealPeriod->name : '-';
                })
                ->addColumn('source_name', function ($row) {
                    return $row->dealSource ? $row->dealSource->name : '-';
                })
                ->addColumn('priority_name', function ($row) {
                    return $row->dealPriority ? $row->dealPriority->name : '-';
                })
                ->addColumn('due_date', function ($row) {
                    return $row->due_date ? date('d M Y', strtotime($row->due_date)) : '-';
                })
                ->addColumn('creator_name', function ($row) {
                    if (!$row->assignees) return '-';
                    $assignees = is_string($row->assignees) ? json_decode($row->assignees, true) : $row->assignees;
                    if (is_array($assignees) && count($assignees) > 0) {
                        $userNames = \App\Models\User::whereIn('id', $assignees)->pluck('name')->toArray();
                        return implode(', ', $userNames);
                    }
                    return '-';
                })
                ->addColumn('action', function ($row) {
                    return '
                    <div class="dropdown table-action">
                        <a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item edit-btn" href="#" data-id="'.$row->id.'"><i class="ti ti-edit text-blue"></i> Edit</a>
                            <a class="dropdown-item delete-btn" href="#" data-id="'.$row->id.'"><i class="ti ti-trash text-danger"></i> Delete</a>
                        </div>
                    </div>';
                })
                ->rawColumns(['action', 'deal_name'])
                ->make(true);
        }

        return response()->json($query->get());
    }

    public function getPipelinesJson()
    {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUserEntry) return response()->json([]);

        $pipelines = \App\Models\Pipeline::where('box_id', $boxUserEntry->box_id)->orderBy('id', 'desc')->get();
        return response()->json($pipelines);
    }

    public function getDealStatusesJson() {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUserEntry) return response()->json([]);
        return response()->json(\App\Models\DealStatus::where('box_id', $boxUserEntry->box_id)->get());
    }
    public function getDealPeriodsJson() {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUserEntry) return response()->json([]);
        return response()->json(\App\Models\DealPeriod::where('box_id', $boxUserEntry->box_id)->get());
    }
    public function getDealSourcesJson() {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUserEntry) return response()->json([]);
        return response()->json(\App\Models\DealSource::where('box_id', $boxUserEntry->box_id)->get());
    }
    public function getDealPrioritiesJson() {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUserEntry) return response()->json([]);
        return response()->json(\App\Models\DealPriority::where('box_id', $boxUserEntry->box_id)->get());
    }
    public function getDealTagsJson() {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUserEntry) return response()->json([]);
        return response()->json(\App\Models\DealTag::where('box_id', $boxUserEntry->box_id)->get());
    }

    public function getProjectsJson()
    {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUserEntry) return response()->json([]);

        $projects = \App\Models\Project::where('box_id', $boxUserEntry->box_id)->orderBy('id', 'desc')->get();
        return response()->json($projects);
    }

    public function getUsersJson()
    {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUserEntry) return response()->json([]);

        $users = \App\Models\User::whereIn('id', function($query) use ($boxUserEntry) {
            $query->select('user_id')
                  ->from('box_users')
                  ->where('box_id', $boxUserEntry->box_id);
        })->get();
        return response()->json($users);
    }

    public function storePipeline(Request $request, $box_slug)
    {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'stages' => 'nullable|array',
            'access' => 'nullable|string|in:all,specific',
            'access_users' => 'nullable|array',
        ]);

        $pipeline = \App\Models\Pipeline::create([
            'box_id' => $boxUserEntry->box_id,
            'name' => $request->name,
            'stages' => $request->stages ?? [],
            'access' => $request->access,
            'access_users' => $request->access_users ?? [],
            'created_by' => $user->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Pipeline created successfully', 'pipeline' => $pipeline]);
    }

    public function updatePipeline(Request $request, $box_slug, $id)
    {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUserEntry) return response()->json(['success' => false, 'message' => 'Unauthorized']);

        $pipeline = \App\Models\Pipeline::where('id', $id)->where('box_id', $boxUserEntry->box_id)->first();
        if (!$pipeline) return response()->json(['success' => false, 'message' => 'Pipeline not found']);

        $pipeline->update([
            'name' => $request->name,
            'stages' => $request->stages ?? [],
            'access' => $request->access,
            'access_users' => $request->access_users ?? [],
        ]);

        return response()->json(['success' => true, 'message' => 'Pipeline updated successfully']);
    }

    public function destroyPipeline($box_slug, $id)
    {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUserEntry) return response()->json(['success' => false, 'message' => 'Unauthorized']);

        $pipeline = \App\Models\Pipeline::where('id', $id)->where('box_id', $boxUserEntry->box_id)->first();
        if (!$pipeline) return response()->json(['success' => false, 'message' => 'Pipeline not found']);

        $pipeline->delete();

        return response()->json(['success' => true, 'message' => 'Pipeline deleted successfully']);
    }

    public function storeDeal(Request $request, $box_slug)
    {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'stage' => 'nullable|string',
            'assignees' => 'nullable|array',
        ]);

        $deal = \App\Models\Deal::create([
            'box_id' => $boxUserEntry->box_id,
            'name' => $request->name,
            'pipeline_id' => $request->pipeline_id,
            'stage' => $request->stage,
            'assignees' => $request->assignees,
            'status_id' => $request->status,
            'deal_value' => $request->deal_value,
            'currency' => $request->currency,
            'period_id' => $request->period,
            'period_value' => $request->period_value,
            'source_id' => $request->source,
            'priority_id' => $request->priority,
            'tags' => $request->tags,
            'lead_id' => $request->lead_id ? (is_array($request->lead_id) ? $request->lead_id[0] : $request->lead_id) : null,
            'project_id' => $request->project_id ? (is_array($request->project_id) ? $request->project_id[0] : $request->project_id) : null,
            'contact_id' => $request->contact_id ? (is_array($request->contact_id) ? $request->contact_id[0] : $request->contact_id) : null,
            'due_date' => $request->due_date ? date('Y-m-d', strtotime($request->due_date)) : null,
            'expected_close_date' => $request->expected_close_date ? date('Y-m-d', strtotime($request->expected_close_date)) : null,
            'created_by' => $user->id
        ]);

        return response()->json(['success' => true, 'message' => 'Deal created successfully.', 'data' => $deal]);
    }

    public function updateDeal(Request $request, $box_slug, $id)
    {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'stage' => 'nullable|string',
            'assignees' => 'nullable|array',
        ]);

        $deal = \App\Models\Deal::where('box_id', $boxUserEntry->box_id)->findOrFail($id);
        $deal->update([
            'name' => $request->name,
            'pipeline_id' => $request->pipeline_id,
            'stage' => $request->stage,
            'assignees' => $request->assignees,
            'status_id' => $request->status,
            'deal_value' => $request->deal_value,
            'currency' => $request->currency,
            'period_id' => $request->period,
            'period_value' => $request->period_value,
            'source_id' => $request->source,
            'priority_id' => $request->priority,
            'tags' => $request->tags,
            'lead_id' => $request->lead_id ? (is_array($request->lead_id) ? $request->lead_id[0] : $request->lead_id) : null,
            'project_id' => $request->project_id ? (is_array($request->project_id) ? $request->project_id[0] : $request->project_id) : null,
            'contact_id' => $request->contact_id ? (is_array($request->contact_id) ? $request->contact_id[0] : $request->contact_id) : null,
            'due_date' => $request->due_date ? date('Y-m-d', strtotime($request->due_date)) : null,
            'expected_close_date' => $request->expected_close_date ? date('Y-m-d', strtotime($request->expected_close_date)) : null,
        ]);

        return response()->json(['success' => true, 'message' => 'Deal updated successfully.', 'data' => $deal]);
    }

    public function destroyDeal($box_slug, $id)
    {
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();

        $deal = \App\Models\Deal::where('box_id', $boxUserEntry->box_id)->findOrFail($id);
        $deal->delete();

        return response()->json(['success' => true, 'message' => 'Deal deleted successfully.']);
    }
}
