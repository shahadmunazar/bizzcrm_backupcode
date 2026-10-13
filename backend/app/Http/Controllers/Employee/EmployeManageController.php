<?php
namespace App\Http\Controllers\Employee;
use App\Http\Controllers\BaseController;
use App\Http\Requests\StoreEmployeeRequest;
use App\Jobs\{ChangeEmployeeIDPrefix,ExportEmployeesJob,SendRegistrationVerifyJob};
use App\Models\{AssignEmployeesOffice,BiometricMachine,Box,BoxUser,CompanyBranch,Department,Employee,EmployeeType,EmployeeBankDetail,EmployeeEducation,EmployeeEmergencyContact,EmployeeExperience,Employeefamily,Team,User,UserMachineMapping,WorkModeSetting};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{Auth,DB,Hash,Log,Storage,Validator};
use App\Helpers\PermissionHelper;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
class EmployeManageController extends BaseController
{

    public function mapToAssign(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['status' => 404, 'message' => 'Unauthorized'], 404);
            }
            $userId = $user->id;

            $boxSlug = request()->route('box_slug');
            $box = Box::where('slug', $boxSlug)->first();
            if (!$box) {
                return response()->json(['status' => 404, 'message' => 'Box not found'], 404);
            }

            $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            
            // Determine Permission based on Action (Create vs Update)
            $permissionType = $request->input('id') ? 'update' : 'create';
            $hasPermission = PermissionHelper::hasModulePermission('attendance', $permissionType, 'details_biometric');

            if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
                return response()->json([
                    'status' => 404, 
                    'message' => 'You do not have permission to perform this action.',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'emp_id'               => 'required|exists:box_users,id',
                'machine_user_id'      => 'required',
                'biometric_machine_id' => 'required|exists:biometric_machines,id',
                'id'                   => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }
            $empId         = $request->emp_id;
            $machineUserId = $request->machine_user_id;
            $biometricId   = $request->biometric_machine_id;
            $mappingId     = $request->id;
            $duplicate     = UserMachineMapping::where('box_user_id', $empId)
                ->where('machine_user_id', $machineUserId)
                ->where('biometric_machine_id', $biometricId)
                ->when($mappingId, function ($q) use ($mappingId) {
                    $q->where('id', '!=', $mappingId);
                })
                ->first();

            if ($duplicate) {
                return response()->json([
                    'status'  => false,
                    'message' => 'This machine is already assigned to this user. Duplicate mapping not allowed.',
                ], 409);
            }

            /** UPDATE CASE */
            if ($mappingId) {
                $mapping = UserMachineMapping::where('id', $mappingId)
                    ->where('box_id', $box->id)
                    ->first();

                if (! $mapping) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Mapping not found.',
                    ], 404);
                }

                $mapping->update([
                    'box_user_id'          => $empId,
                    'machine_user_id'      => $machineUserId,
                    'biometric_machine_id' => $biometricId,
                    'updated_by'           => $userId,
                ]);

                return response()->json([
                    'status'  => true,
                    'message' => 'Machine mapping updated successfully.',
                ]);
            }
            UserMachineMapping::create([
                'box_user_id'          => $empId,
                'box_id'               => $box->id,
                'machine_user_id'      => $machineUserId,
                'biometric_machine_id' => $biometricId,
                'created_by'           => $userId,
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'Machine mapped to user successfully.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while mapping the machine.',
                'error'   => $th->getMessage(),
                'line'    => $th->getLine(),
                'file'    => $th->getFile(),
            ], 500);
        }
    }

    public function get_all_machine()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
            }

            $boxSlug = request()->route('box_slug');
            $box = Box::where('slug', $boxSlug)->first();
            if (!$box) {
                return response()->json(['status' => false, 'message' => 'Box not found'], 404);
            }

            $getAllBiometric = BiometricMachine::where('box_id', $box->id)
                ->where('status', 'active')
                ->get();

            return response()->json([
                'status'   => true,
                'machines' => $getAllBiometric,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function UpdateWorkMode(Request $request)
    {
        try {
            // Check permission: (Module, Action, SubModule)
            // This checks for: attendance.details_work_mode.update
            $hasPermission = PermissionHelper::hasModulePermission('attendance', 'update', 'details_work_mode');
            
            $user = Auth::user();
            $boxUser = BoxUser::where('user_id', $user->id)->first();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
                return response()->json([
                    'status' => 404, 
                    'message' => 'You do not have permission to perform this action.',
                ], 404);
            }
            $validated = $request->validate([
                'employee_id'        => 'required|exists:box_users,id',
                'work_mode_id'       => 'required',
                'working_web_days'   => 'nullable|array',
                'working_web_days.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            ]);

            // Find the employee
            $employee = BoxUser::findOrFail($validated['employee_id']);

            // Update work mode
            $employee->work_mode_id = $validated['work_mode_id'];

            // Store days as JSON (or array, if using cast)
            $employee->working_web_days = $validated['working_web_days'] ?? [];
            $employee->save();

            return response()->json([
                'status'  => true,
                'message' => 'Working mode updated successfully',
                'data'    => $employee,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function GetAllBranchOffice()
    {
        try {
            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();
            $box    = Box::where('id', $boxUser->box_id)->first();
            if (! $box) {
                return response()->json([
                    'status'  => false,
                    'message' => 'No box found for this user.',
                ], 404);
            }
            $branches = CompanyBranch::where('box_id', $box->id)->get();
            if ($branches->isEmpty()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'No branch offices found for this box.',
                    'data'    => [],
                ], 200);
            }
            return response()->json([
                'status'  => true,
                'message' => 'Branch offices retrieved successfully.',
                'data'    => $branches,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'An error occurred while fetching branch offices.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function GetAllWorkMode()
    {
        try {
            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();
            $box    = Box::where('id', $boxUser->box_id)->first();
            if (! $box) {
                return response()->json([
                    'status'  => false,
                    'message' => 'No box found for this user.',
                ], 404);
            }
            $workModes = WorkModeSetting::where('box_id', $box->id)->get();
            return response()->json([
                'status' => true,
                'data'   => $workModes,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function checkemploye_id(Request $request)
    {
        try {
            $user_id = Auth::id();

            $box = Box::where('superadmin_id', $user_id)
                ->first(['id', 'dynamic_employee_id']);

            if (! $box) {
                return response()->json([
                    'status'          => true,
                    'employee_id'     => null,
                    'dynamic_enabled' => false,
                    'from_type'       => null,
                ]);
            }

            // If dynamic_employee_id is disabled
            if ($box->dynamic_employee_id == 0) {
                $latestUser = BoxUser::whereHas('box', function ($q) use ($user_id) {
                    $q->where('superadmin_id', $user_id);
                })
                    ->whereNotNull('employee_id')
                    ->orderByDesc('id')
                    ->first();

                if ($latestUser && preg_match("/([A-Z]+)-(\d+)/", $latestUser->employee_id, $matches)) {
                    $prefix     = $matches[1];
                    $number     = (int) $matches[2] + 1;
                    $employeeId = sprintf("%s-%02d", $prefix, $number);
                    $fromType   = $latestUser->type ?? null;
                } else {
                    $prefix     = 'EMP';
                    $number     = 1;
                    $employeeId = sprintf("%s-%02d", $prefix, $number);
                    $fromType   = null;
                }

                return response()->json([
                    'status'          => true,
                    'employee_id'     => $employeeId,
                    'dynamic_enabled' => false,
                    'from_type'       => $fromType ?? 'not_available',
                ]);
            }

            // Dynamic prefix is enabled: use dynamic_employee_id as base
            $prefix = strtoupper(substr(Str::slug($box->dynamic_employee_id), 0, 2));

            $latestUser = BoxUser::where('box_id', $box->id)
                ->where('employee_id', 'like', "{$prefix}-%")
                ->orderByDesc('id')
                ->first();

            if ($latestUser && preg_match("/{$prefix}-(\d+)/", $latestUser->employee_id, $matches)) {
                $number = (int) $matches[1] + 1;
            } else {
                $number = 1;
            }

            $employeeId = sprintf("%s-%02d", $prefix, $number);

            return response()->json([
                'status'          => true,
                'employee_id'     => $employeeId,
                'dynamic_enabled' => true,
                'from_type'       => $box->dynamic_employee_id,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'          => true,
                'employee_id'     => null,
                'dynamic_enabled' => false,
                'from_type'       => null,
            ]);
        }
    }

    public function UpdateEmploye_id(Request $request)
    {
        try {
            $request->validate([
                'dynamic_employee_id'   => 'nullable|string|in:0,1',
                'custom_employee_id'    => 'nullable|string|min:2',
                'change_dynamic_prefix' => 'nullable|string|in:0,1',
            ]);
            $user_id = Auth::id();
            $box     = Box::where('superadmin_id', $user_id)->first(['id', 'slug', 'name']);
            if (! $box) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Box not found for current user.',
                ], 404);
            }

            // Handle dynamic_employee_id
            if ($request->filled('dynamic_employee_id')) {
                $box->dynamic_employee_id = $request->dynamic_employee_id;

                if ($request->dynamic_employee_id == '0') {
                    // Reset when disabled
                    $box->custom_employee_id    = null;
                    $box->change_dynamic_prefix = null;
                } else {
                    // If enabled, only clear prefix change flag
                    $box->change_dynamic_prefix = null;
                }
            }

            // Handle custom employee ID
            if ($request->filled('custom_employee_id')) {
                $box->custom_employee_id = $request->custom_employee_id;
            }

            // Handle change_dynamic_prefix flag
            if ($request->filled('change_dynamic_prefix')) {
                $box->change_dynamic_prefix = $request->change_dynamic_prefix;
                $box->save();
                if ($request->change_dynamic_prefix == '1') {
                    dispatch(new ChangeEmployeeIDPrefix(
                        $box->id,
                        $box->change_dynamic_prefix
                    ));
                }
            }

            $box->save();

            return response()->json([
                'status'                => true,
                'message'               => 'Box settings updated successfully.',
                'dynamic_enabled'       => $box->dynamic_employee_id === '1',
                'saved_prefix'          => $box->custom_employee_id ?? strtoupper(substr($box->name, 0, 2)),
                'change_dynamic_prefix' => $box->change_dynamic_prefix,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function store_employee(StoreEmployeeRequest $request)
    {
        // Check permission to create employee
        $this->requirePermission('attendance.admin.create', 'You do not have permission to create employees.');

        DB::beginTransaction();

        try {
            $authUser             = Auth::user();
            $boxUser              = BoxUser::with('box')->where('user_id', $authUser->id)->firstOrFail();
            $box_id               = $boxUser->box_id;
            $boxName              = $boxUser->box->name;
            $dynamicPrefixSetting = $boxUser->box->dynamic_employee_id;
            $requestedEmployeeId  = $request->employee_id;
            $expectedPrefix       = strtoupper(Str::substr(Str::slug(
                $dynamicPrefixSetting == 0 ? $boxName : $dynamicPrefixSetting
            ), 0, 2));
            if (! Str::startsWith($requestedEmployeeId, $expectedPrefix . '-')) {
                return response()->json([
                    'error' => "Invalid prefix. Expected prefix is `{$expectedPrefix}`. Your employee ID should start with: {$expectedPrefix}-",
                ], 422);
            }
            $existingEmployee = BoxUser::where('box_id', $box_id)
                ->where('employee_id', $requestedEmployeeId)
                ->first();
            if ($existingEmployee) {
                $lastEmployee = BoxUser::where('box_id', $box_id)
                    ->where('employee_id', 'like', "{$expectedPrefix}-%")
                    ->orderByDesc('id')
                    ->first();
                $lastId     = $lastEmployee ? $lastEmployee->employee_id : $expectedPrefix . '-01';
                $nextNumber = ($lastEmployee && preg_match("/{$expectedPrefix}-(\d+)/", $lastId, $matches))
                    ? str_pad((int) $matches[1] + 1, 2, '0', STR_PAD_LEFT)
                    : '01';
                $suggestedId = "{$expectedPrefix}-{$nextNumber}";
                return response()->json([
                    'error' => "The employee ID `{$requestedEmployeeId}` has already been taken. Last generated ID was: {$lastId}. You should use employee_id: {$suggestedId}",
                ], 422);
            }
            // Detailed cross-box checks follow below...


            if ($request->filled('phone')) {
                $duplicatePhone = BoxUser::where('phone', $request->phone)
                    ->with('box')
                    ->first();
                if ($duplicatePhone) {
                    $boxName = $duplicatePhone->box->name ?? 'another box';
                    return response()->json([
                        'success' => false,
                        'message' => "This phone number is already registered in box: `{$boxName}`. Duplicate numbers are not allowed across boxes.",
                        'errors' => ['phone' => ["Already in use in `{$boxName}`"]],
                    ], 422);
                }
            }

            if ($request->filled('email')) {
                $existingUser = User::where('email', $request->email)->first();
                if ($existingUser) {
                    $boxUser = BoxUser::where('user_id', $existingUser->id)
                        ->with('box')
                        ->first();
                    if ($boxUser) {
                        $boxName = $boxUser->box->name ?? 'another box';
                        return response()->json([
                            'success' => false,
                            'message' => "This email address is already registered in box: `{$boxName}`. Duplicate emails are not allowed across boxes.",
                            'errors' => ['email' => ["Already in use in `{$boxName}`"]],
                        ], 422);
                    }
                }
            }
            // Validating User Limits based on CRM and WhatsApp plans
            $crmLimit = $box->total_users ?? 0;
            $whatsappLimit = \App\Helpers\SubscriptionHelper::getWhatsAppAgentLimit($authUser);
            $totalBoxUsers = BoxUser::where('box_id', $box_id)->count();
            $usedWhatsAppAgentsCount = BoxUser::where('box_id', $box_id)->where('has_whatsapp_access', 1)->count();
            $hasWhatsappAccess = $request->has('has_whatsapp_access') && $request->has_whatsapp_access == 1;

            if ($crmLimit == 0 && $whatsappLimit == 0) {
                return response()->json(['error' => 'You do not have any active plans that allow adding users.'], 422);
            }

            // Automatically handle WhatsApp access based on plans (without showing errors)
            if ($crmLimit == 0 && $whatsappLimit > 0) {
                $hasWhatsappAccess = true;
            } else if ($crmLimit > 0 && $whatsappLimit == 0) {
                $hasWhatsappAccess = false;
            }

            if ($hasWhatsappAccess) {
                if ($whatsappLimit > 0 && $usedWhatsAppAgentsCount >= $whatsappLimit) {
                    return response()->json(['error' => "WhatsApp Agent limit reached. Your plan allows up to {$whatsappLimit} agents."], 422);
                }
            }

            if ($crmLimit > 0) {
                if ($totalBoxUsers >= $crmLimit) {
                    return response()->json(['error' => "User limit reached. Your CRM plan allows up to {$crmLimit} users."], 422);
                }
            }

            $tempPassword = Str::random(10);
            $token        = Str::random(60);
            
            $fullName = $request->name;
            if ($request->filled('last_name')) {
                $fullName .= ' ' . $request->last_name;
            }

            $user         = User::create([
                'name'           => $fullName,
                'email'          => $request->email,
                'password'       => Hash::make($tempPassword),
                'remember_token' => $token,
            ]);
            // Assign role based on staff_role
            $staffRole = $request->staff_role ?? 'employee';
            if (strtolower($staffRole) === 'admin') {
                $user->assignRole('admin');
            } else {
                $user->assignRole('employee');
            }
            $imagePath = null;
            if ($request->hasFile('profile_image')) {
                $file      = $request->file('profile_image');
                $imageName = Str::slug($request->name . '-' . now()->timestamp) . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('employees/profile_images', $imageName, 'public');
            }

            $employee = BoxUser::create([
                'has_whatsapp_access'   => $hasWhatsappAccess ? 1 : 0,
                'user_id'               => $user->id,
                'box_id'                => $box_id,
                'added_by'              => $authUser->id,
                'first_name'            => $request->name,
                'last_name'             => $request->last_name,
                'employee_id'           => $requestedEmployeeId,
                'staff_role'            => $request->staff_role ?? 'employee',
                'emp_type'              => $request->emp_type,
                'email'                 => $request->email,
                'username'              => $request->email,
                'phone'                 => $request->phone,
                'joining_date'          => $request->joining_date ?? now()->format('Y-m-d'),
                'department'            => (strtolower($staffRole) === 'admin') ? 'Owner' : $request->department,
                'designation'           => (strtolower($staffRole) === 'admin') ? 'Owner' : $request->designation,
                'reporting_manager_id'  => $request->reporting_manager_id,
                'can_login_anytime'     => $request->has('can_login_anytime') ? (int)$request->can_login_anytime : 1,
                'call_logs'             => $request->has('call_logs') ? (int)$request->call_logs : 1,
                'profile_image'         => $imageName,
                'status'                => 'Disabled',
                'profile_image_path'    => $imagePath,
                'user_box_timing'       => $request->has('user_box_timing') ? '1' : '0',
                'day_start_time'        => $request->day_start_time,
                'day_end_time'          => $request->day_end_time,
                'working_days'          => $request->has('working_days') ? $request->working_days : null,
                'exlude_ip_restriction' => $request->has('exlude_ip_restriction') ? '1' : '0',
                'exclude_round_robin'   => $request->has('exclude_round_robin') ? '1' : '0',
                'allowed_ips'           => $request->has('allowed_ips') ? $request->allowed_ips : null,
                'has_whatsapp_access'   => $hasWhatsappAccess,
            ]);

            // Handle permissions - manage only from user_id, not box_id
            // If user is admin, skip permission assignment (admins have all permissions via role)
            $staffRole = strtolower($request->staff_role ?? 'employee');
            if ($staffRole !== 'admin') {
                if ($request->has('permissions')) {
                    $permissionsData = $request->permissions;
                    // Handle JSON string from frontend
                    if (is_string($permissionsData)) {
                        $permissionsData = json_decode($permissionsData, true);
                    }

                    // Ensure it's an array (even if empty)
                    if (! is_array($permissionsData)) {
                        $permissionsData = [];
                    }

                    // Remove duplicates and filter out invalid values
                    $permissionsData = array_filter(array_unique($permissionsData), function ($id) {
                        return is_numeric($id) && $id > 0;
                    });

                    $permissionsIdsToSync = [];
                    if (! empty($permissionsData)) {
                        $permissionsIdsToSync = Permission::whereIn('id', $permissionsData)
                            ->where('guard_name', 'web')
                            ->pluck('id')
                            ->toArray();
                    }
                    // Sync permissions (empty array removes all)
                    $user->syncPermissions($permissionsIdsToSync);
                } else {
                    // If permissions field is not provided for new employee, set empty permissions
                    $user->syncPermissions([]);
                }
            } else {
                // Admin users don't need individual permissions - they have all via role
                $user->syncPermissions([]);
            }

            // Clear permission cache to ensure changes take effect immediately
            $user->forgetCachedPermissions();

            DB::commit();
            dispatch(new SendRegistrationVerifyJob(
                $user->name,
                $user->email,
                $tempPassword,
                $user->remember_token
            ));

            return response()->json([
                'message' => 'Employee saved successfully.',
                'data'    => $employee->load('bankDetails'),
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error'   => 'Something went wrong.',
                'details' => $th->getMessage(),
            ], 500);
        }
    }

    public function ResendEmployeeVerifcation(Request $request)
    {
        try {
            $request->validate([
                'userid' => 'required|exists:users,id',
            ]);
            $user                    = User::findOrFail($request->userid);
            $tempPassword            = Str::random(10);
            $token                   = Str::random(60);
            $user->password          = Hash::make($tempPassword);
            $user->remember_token    = $token;
            $user->email_verified_at = null;
            $user->save();
            dispatch(new SendRegistrationVerifyJob(
                $user->name,
                $user->email,
                $tempPassword,
                $token
            ));
            return response()->json([
                'status'  => true,
                'message' => 'Verification email has been resent successfully.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while resending verification.',
            ], 500);
        }
    }

    public function fetch_employee(Request $request)
    {
        if (!$this->hasPermission('attendance.admin.read') && !$this->hasPermission('attendance.team_admin.read')) {
            return response()->json(['error' => 'You do not have permission to view employees.'], 403);
        }
        try {
            $user_id = Auth::id();
            $boxUser = BoxUser::where('user_id', $user_id)->first();
            $box_id = $boxUser ? $boxUser->box_id : null;
            $box = Box::where('id', $box_id)->first();
            if (! $box) {
                return response()->json(['error' => 'Box not found.'], 404);
            }
            $query = BoxUser::with([
                'user.permissions', 
                'departmentRelation.permissions', 
                'designationRelation.permissions', 
                'employeeType',
                'workMode',
                'reportingManager.user'
            ])
                ->where('box_id', $box->id);

            // Team Filtering Logic
            $isGlobalAdmin = Auth::user()->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            $canReadAll = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'admin');
            $canReadTeam = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'team_admin');
            
            $teamOnly = $request->get('team_only', false);
            if (!$isGlobalAdmin && !$isBoxAdmin && !$canReadAll && $canReadTeam) {
                $teamOnly = true;
            }

            if ($teamOnly) {
                $userId = Auth::id();
                $subordinateBoxUserIds = BoxUser::where('box_id', $box->id)
                    ->where('reporting_manager_id', $boxUser->id)
                    ->pluck('id')
                    ->toArray();

                $departmentIds = \App\Models\Department::where('user_id', $userId)->pluck('id')->toArray();
                $teamIds = \App\Models\Team::where('user_id', $userId)->pluck('id')->toArray();

                $additionalSubordinates = BoxUser::where('box_id', $box->id)
                    ->where(function ($q) use ($departmentIds, $teamIds) {
                        if (!empty($departmentIds)) {
                            $q->orWhereIn('department', $departmentIds);
                        }
                        if (!empty($teamIds)) {
                            $q->orWhereIn('designation', $teamIds);
                        }
                    })
                    ->pluck('id')
                    ->toArray();

                $allSubordinateIds = array_unique(array_merge($subordinateBoxUserIds, $additionalSubordinates));
                
                // Include self if you want to see yourself, or exclude if only subordinates
                // Usually, you should see yourself in the list if you're an employee too.
                $allSubordinateIds[] = $boxUser->id;
                $allSubordinateIds = array_unique($allSubordinateIds);

                $query->whereIn('id', $allSubordinateIds);
            }
            $activeCount     = (clone $query)->where('status', 'Enabled')->count();
            $inactiveCount   = (clone $query)->where('status', 'Disabled')->count();
            $newJoineesCount = (clone $query)
                ->whereDate('joining_date', '>=', now()->subDays(7)->toDateString())
                ->count();
            return DataTables::of($query)
                ->addColumn('email', function ($emp) {
                    return $emp->user->email ?? null;
                })
                ->addColumn('name', function ($emp) {
                    return $emp->user->name ?? null;
                })
                ->addColumn('user_id', function ($emp) {
                    return $emp->user->id ?? null;
                })
                ->addColumn('employee_id', function ($emp) {
                    return $emp->employee_id ?? null;
                })
                ->addColumn('department_name', function ($emp) {
                    if (strtolower($emp->staff_role) === 'admin') return '';
                    return $emp->departmentRelation?->name ?? '-';
                })
                ->addColumn('designation_name', function ($emp) {
                    if (strtolower($emp->staff_role) === 'admin') return '';
                    return $emp->designationRelation?->name ?? '-';
                })
                ->addColumn('employee_type_name', function ($emp) {
                    if (strtolower($emp->staff_role) === 'admin') return 'NA';
                    return $emp->employeeType?->name ?? '-';
                })
                ->addColumn('work_mode_name', function ($emp) {
                    return $emp->workMode?->name ?? '-';
                })
                ->addColumn('reporting_manager_name', function ($emp) {
                    if (strtolower($emp->staff_role ?? '') === 'admin') return '';
                    return optional($emp->reportingManager)->user->name ?? (optional($emp->reportingManager)->username ?? '-');
                })
                ->addColumn('joining_date', function ($emp) {
                    return optional($emp->joining_date)->format('Y-m-d');
                })
                ->addColumn('status', function ($emp) {
                    return $emp->status;
                })
                ->addColumn('profile_image_url', function ($emp) {
                    return $emp->profile_image_path
                        ? asset('storage/' . $emp->profile_image_path)
                        : asset('images/default-avatar.png');
                })
                ->addColumn('profile_image_path', function ($emp) {
                    return $emp->profile_image_path ?? null;
                })
                ->addColumn('permissions', function ($emp) {
                    $direct = [];
                    $inherited = [];
                    
                    // 1. Direct User Permissions
                    if ($emp->user && $emp->user->relationLoaded('permissions')) {
                        $direct = $emp->user->permissions->pluck('id')->toArray();
                    } elseif ($emp->user) {
                         // Fallback if not loaded for some reason
                         $direct = $emp->user->permissions()->pluck('id')->toArray();
                    }
                    
                    // 2. Inherited from Department
                    if ($emp->departmentRelation && $emp->departmentRelation->relationLoaded('permissions')) {
                        $inherited = array_merge($inherited, $emp->departmentRelation->permissions->pluck('id')->toArray());
                    } elseif ($emp->departmentRelation) {
                        $inherited = array_merge($inherited, $emp->departmentRelation->permissions()->pluck('id')->toArray());
                    }
                    
                    // 3. Inherited from Designation (Team)
                    if ($emp->designationRelation && $emp->designationRelation->relationLoaded('permissions')) {
                         $inherited = array_merge($inherited, $emp->designationRelation->permissions->pluck('id')->toArray());
                    } elseif ($emp->designationRelation) {
                         $inherited = array_merge($inherited, $emp->designationRelation->permissions()->pluck('id')->toArray());
                    }
                    
                    return [
                        'direct' => array_map('intval', $direct),
                         // Ensure unique and integer values
                        'inherited' => array_values(array_unique(array_map('intval', $inherited)))
                    ];
                })
                ->with([
                    'summary' => [
                        'recordsTotal'   => $query->count(),
                        'active_users'   => $activeCount,
                        'inactive_users' => $inactiveCount,
                        'new_joinees'    => $newJoineesCount,
                    ],
                ])
                ->rawColumns(['status']) // Enable HTML rendering for status badges
                ->make(true);
        } catch (\Throwable $th) {
            return response()->json([
                'error'   => 'Failed to fetch employees.',
                'details' => $th->getMessage(),
            ], 500);
        }
    }

    public function getEmployeeTypes()
    {
        try {
            $user_id = Auth::id();
            $boxUser = BoxUser::where('user_id', $user_id)->first();
            
            if (!$boxUser) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Box user not found'
                ]);
            }

            $types = EmployeeType::where('box_id', $boxUser->box_id)
                ->where('status', 'active')
                ->select('id', 'name')
                ->get();

            return response()->json([
                'status' => 200,
                'data' => $types
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ]);
        }
    }

    /**
     * Get all available permissions grouped by module for current box
     */
    public function getPermissions(Request $request)
    {
        try {
            $allPermissions = Permission::where('guard_name', 'web')
                ->orderBy('name')
                ->get();

            $permissionsData = [];
            
            // Map module keys to display names from the helper for better formatting
            $moduleNames = [];
            foreach (PermissionHelper::allModules() as $key => $data) {
                $moduleNames[$key] = $data['name'];
            }

            foreach ($allPermissions as $permission) {
                if ($permission->name === 'all.permissions') {
                    $permissionsData['All Modules'][] = [
                        'id'             => $permission->id,
                        'name'           => $permission->name,
                        'module'         => 'All Modules',
                        'sub_module'     => 'All',
                        'sub_module_key' => 'all',
                        'action'         => 'All Permissions',
                        'action_key'     => 'all',
                    ];
                    continue;
                }

                $parts = explode('.', $permission->name);
                if (count($parts) >= 2) {
                    $moduleKey = $parts[0];
                    $moduleName = $moduleNames[$moduleKey] ?? strtoupper(str_replace('_', ' ', $moduleKey));
                    
                    $subModule = count($parts) === 3 ? $parts[1] : 'all';
                    $action = $parts[count($parts)-1];

                    $permissionsData[$moduleName][] = [
                        'id'             => $permission->id,
                        'name'           => $permission->name,
                        'module'         => $moduleName,
                        'sub_module'     => $subModule === 'all' ? 'General' : ucfirst(str_replace('_', ' ', $subModule)),
                        'sub_module_key' => $subModule,
                        'action'         => ucfirst(str_replace('_', ' ', $action)),
                        'action_key'     => $action,
                    ];
                } else {
                    $permissionsData['Other'][] = [
                        'id'             => $permission->id,
                        'name'           => $permission->name,
                        'module'         => 'Other',
                        'sub_module'     => 'General',
                        'sub_module_key' => 'general',
                        'action'         => ucfirst($permission->name),
                        'action_key'     => $permission->name,
                    ];
                }
            }

            return response()->json([
                'status' => true,
                'data'   => $permissionsData,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch permissions.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function Gridfetch_employee(Request $request)
    {
        if (!$this->hasPermission('attendance.admin.read') && !$this->hasPermission('attendance.team_admin.read')) {
            return response()->json(['error' => 'You do not have permission to view employees.'], 403);
        }
        try {
            $user_id = Auth::id();
            $boxUser = BoxUser::where('user_id',$user_id)->first();
            $box     = Box::where('id', $boxUser->box_id)->first();
            if (! $box) {
                return response()->json(['error' => 'Box not found.'], 404);
            }

            $perPage = 12;
            $page    = (int) $request->input('page', 1);

            $baseQuery = BoxUser::with(['user', 'departmentRelation', 'designationRelation', 'reportingManager.user'])
                ->where('box_id', $box->id);

            // Team Filtering Logic
            $isGlobalAdmin = Auth::user()->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            $canReadAll = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'admin');
            $canReadTeam = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'team_admin');
            
            $teamOnly = $request->get('team_only', false);
            if (!$isGlobalAdmin && !$isBoxAdmin && !$canReadAll && $canReadTeam) {
                $teamOnly = true;
            }

            if ($teamOnly) {
                $subordinateBoxUserIds = BoxUser::where('box_id', $box->id)
                    ->where('reporting_manager_id', $boxUser->id)
                    ->pluck('id')
                    ->toArray();

                $departmentIds = \App\Models\Department::where('user_id', $user_id)->pluck('id')->toArray();
                $teamIds = \App\Models\Team::where('user_id', $user_id)->pluck('id')->toArray();

                $additionalSubordinates = BoxUser::where('box_id', $box->id)
                    ->where(function ($q) use ($departmentIds, $teamIds) {
                        if (!empty($departmentIds)) {
                            $q->orWhereIn('department', $departmentIds);
                        }
                        if (!empty($teamIds)) {
                            $q->orWhereIn('designation', $teamIds);
                        }
                    })
                    ->pluck('id')
                    ->toArray();

                $allSubordinateIds = array_unique(array_merge($subordinateBoxUserIds, $additionalSubordinates));
                $allSubordinateIds[] = $boxUser->id;
                $allSubordinateIds = array_unique($allSubordinateIds);

                $baseQuery->whereIn('id', $allSubordinateIds);
            }

            // Fetch the paginated results as a collection
            $employees = (clone $baseQuery)
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            // Counts
            $activeCount     = (clone $baseQuery)->where('status', 'Enabled')->count();
            $inactiveCount   = (clone $baseQuery)->where('status', 'Disabled')->count();
            $newJoineesCount = (clone $baseQuery)
                ->whereDate('joining_date', '>=', now()->subDays(7)->toDateString())
                ->count();

            return DataTables::of($employees)
                ->addColumn('email', fn($emp) => $emp->user->email ?? null)
                ->addColumn('name', fn($emp) => $emp->user->name ?? null)
                ->addColumn('department_name', function ($emp) {
                    if (strtolower($emp->staff_role ?? '') === 'admin') return '';
                    return $emp->departmentRelation?->name ?? '-';
                })
                ->addColumn('designation_name', function ($emp) {
                    if (strtolower($emp->staff_role ?? '') === 'admin') return '';
                    return $emp->designationRelation?->name ?? '-';
                })
                ->addColumn('reporting_manager_name', function ($emp) {
                    if (strtolower($emp->staff_role ?? '') === 'admin') return '';
                    return optional($emp->reportingManager)->user->name ?? (optional($emp->reportingManager)->username ?? '-');
                })
                ->addColumn('joining_date', fn($emp) => optional($emp->joining_date)->format('Y-m-d'))
                ->addColumn('status', fn($emp) => $emp->status)
                ->addColumn('profile_image_url', function ($emp) {
                    return $emp->profile_image_path
                        ? asset('storage/' . $emp->profile_image_path)
                        : asset('images/default-avatar.png');
                })
                ->editColumn('profile_image_path', function () {
                    // Remove the profile_image_path field from the output
                    return null;
                })
                ->with([
                    'summary' => [
                        'recordsTotal'   => $baseQuery->count(),
                        'active_users'   => $activeCount,
                        'inactive_users' => $inactiveCount,
                        'new_joinees'    => $newJoineesCount,
                    ],
                ])
                ->rawColumns(['status'])
                ->make(true);
        } catch (\Throwable $th) {
            return response()->json([
                'error'   => 'Failed to fetch employees.',
                'details' => $th->getMessage(),
            ], 500);
        }
    }

    public function DeleteAssignBio(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['status' => 404, 'message' => 'Unauthorized'], 404);
            }

            $boxSlug = request()->route('box_slug');
            $box = Box::where('slug', $boxSlug)->first();
            if (!$box) {
                return response()->json(['status' => 404, 'message' => 'Box not found'], 404);
            }

            $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            $hasPermission = PermissionHelper::hasModulePermission('attendance', 'delete', 'details_biometric');

            if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
                return response()->json([
                    'status' => 404, 
                    'message' => 'You do not have permission to perform this action.',
                ], 404);
            }
            $id            = $request->id;
            $AssignMachine = UserMachineMapping::where('id', $id)->first();
            $AssignMachine->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Machine Delete from user successfully.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Get all box users for a specific box (for Reporting Manager dropdown, etc.)
     */
    public function getBoxUsers(Request $request)
    {
        try {
            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (! $boxUser) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Box user not found',
                ], 404);
            }

            $boxId     = $boxUser->box_id;
            // Handle boxId more robustly from route if available
            $boxSlugFromRoute = request()->route('box_slug');
            if ($boxSlugFromRoute) {
                $box = \App\Models\Box::where('slug', $boxSlugFromRoute)->first();
                if ($box) {
                    $boxId = $box->id;
                }
            }

            $excludeId = $request->get('exclude_id', null); // Optional: exclude specific user ID
            $includeId = $request->get('include_id', null); // Optional: include specific user ID
            $teamOnly  = $request->get('team_only', false); // Optional: filter by team subordinates
            $superiorToDesignationId = $request->get('superior_to_designation');

            // Auto-enforce team_only if user has team permission but not global admin permission
            $isGlobalAdmin = Auth::user()->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            $canReadAll = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'admin');
            $canReadTeam = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'team_admin');

            if (!$isGlobalAdmin && !$isBoxAdmin && !$canReadAll && $canReadTeam) {
                $teamOnly = true;
            }

            $query = BoxUser::with([
                'user:id,name,email,mobile',
                'designationRelation:id,name',
                'departmentRelation:id,name',
                'assignedOffices.branch:id,name',
            ])
                ->where('box_id', $boxId)
                ->whereNotNull('user_id')
                ->when($excludeId, function ($q) use ($excludeId) {
                    return $q->where('id', '!=', $excludeId);
                });

            // Hierarchy Filtering (Server-Side) is removed here because we want to return ALL 
            // eligible users to the frontend. The frontend's renderReportingManagerOptions 
            // function will automatically sort and group them into "Direct Hierarchy" 
            // (primary recommendations) and "Other Employees", allowing the user to select 
            // anyone while still seeing the smartest recommendations at the top.
            if ($superiorToDesignationId) {
                // We no longer strictly filter by designation.
            }
                
            if ($teamOnly && !$superiorToDesignationId) {
                $subordinateBoxUserIds = BoxUser::where('box_id', $boxId)
                    ->where('reporting_manager_id', $boxUser->id)
                    ->pluck('id')
                    ->toArray();

                $departmentIds = \App\Models\Department::where('user_id', $userId)->pluck('id')->toArray();
                $teamIds = \App\Models\Team::where('user_id', $userId)->pluck('id')->toArray();

                $additionalSubordinates = BoxUser::where('box_id', $boxId)
                    ->where(function ($q) use ($departmentIds, $teamIds) {
                        if (!empty($departmentIds)) {
                            $q->orWhereIn('department', $departmentIds);
                        }
                        if (!empty($teamIds)) {
                            $q->orWhereIn('designation', $teamIds);
                        }
                    })
                    ->pluck('id')
                    ->toArray();

                $allSubordinateIds = array_unique(array_merge($subordinateBoxUserIds, $additionalSubordinates));
                
                $query->whereIn('id', $allSubordinateIds);
            }

            // Fetch team depths for the frontend
            $teamDepths = \App\Models\Team::withDepth()->where('box_id', $boxId)->pluck('depth', 'id')->toArray();

            $boxUsers = $query->orderBy('first_name', 'asc')
                ->orderBy('last_name', 'asc')
                ->select('id', 'user_id', 'employee_id', 'first_name', 'last_name', 'designation', 'department', 'staff_role', 'reporting_manager_id')
                ->get()
                ->map(function ($emp) use ($teamDepths) {
                    $reportOfficeNames = $emp->assignedOffices->pluck('branch.name')->filter()->join(', ');
                    $isOwner = strtolower($emp->staff_role ?? '') === 'admin';

                    return [
                        'id'                  => $emp->id,
                        'user_id'             => $emp->user_id,
                        'name'                => $emp->user?->name ?? trim($emp->first_name . ' ' . $emp->last_name),
                        'email'               => $emp->user?->email ?? '',
                        'phone'               => $emp->user?->mobile ?? '',
                        'employee_id'         => $emp->employee_id,
                        'designation'         => $emp->designation, // Add designation ID for filtering
                        'designation_name'    => $isOwner ? 'Owner' : ($emp->designationRelation?->name ?? ''),
                        'designation_depth'   => $teamDepths[$emp->designation] ?? 0,
                        'department'          => $emp->department, // Add department ID for filtering
                        'department_name'     => $isOwner ? 'Owner' : ($emp->departmentRelation?->name ?? ''),
                        'reporting_manager_id'=> $emp->reporting_manager_id,
                        'report_office_names' => $reportOfficeNames,
                        'staff_role'          => $emp->staff_role,
                    ];
                });

            return response()->json([
                'status'  => true,
                'message' => 'Box users fetched successfully',
                'data'    => $boxUsers,
                'count'   => $boxUsers->count(),
            ], 200);
        } catch (\Throwable $th) {
            Log::error('Get Box Users Error: ' . $th->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while fetching box users.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function employee_details_admin(Request $request)
    {
        try {
            $req_emp_id = $request->input('req_emp_id');
            $user_id    = Auth::id();

            $employee = BoxUser::with([
                'bankDetails:id,box_user_id,bank_type,bank_name,account_number,branch,ifsc_code',
                'families',
                'educations:id,box_user_id,institute,degree,start_year,end_year',
                'experiences:id,box_user_id,company_name,position,start_date,end_date,current_working',
                'emergencyContacts:id,box_user_id,name,relation,phone,type,phone_alt',
                'bankStatutory:id,box_user_id,salary_basis,salary_amount,payment_type,pf_contribution,pf_no,pf_rate,pf_additional,pf_total,esi_contribution,esi_no,esi_rate,esi_additional,esi_total',
                'user:id,name,email',
                'shift:id,code',
                'workMode:id,name',
                'assignedOffices.branch:id,name',
                'machineMappings.machine:id,serial_no,machine_name',
                'reportingManager.user:id,name',
                'employeeType:id,name',
                'assignedAssets.asset:id,asset_name,serial_number,status',
            ])
                ->find($req_emp_id, [
                    'id',
                    'user_id',
                    'pan_number',
                    'employee_id',
                    'esi_no',
                    'pf_no',
                    'resignation_date',
                    'phone',
                    'joining_date',
                    'status',
                    'first_name',
                    'last_name',
                    'company',
                    'username',
                    'assign_tour',
                    'staff_role',
                    'assign_customer',
                    'about',
                    'department',
                    'gender',
                    'birthday',
                    'designation',
                    'passport_no',
                    'passport_exp_date',
                    'nationality',
                    'religion',
                    'address',
                    'marital_status',
                    'work_mode_id',
                    'working_web_days',
                    'Employment_of_spouse',
                    'no_of_children',
                    'profile_image_path',
                    'report_office',
                    'shift_id',
                    'marriage_date',
                    'reporting_manager_id',
                    'emp_type',
                    'box_id',
                    'is_location_tracking'
                ]);
            if (! $employee) {
                return response()->json([
                    'error' => 'Employee not found.',
                ], 404);
            }

            $findDepartMent = Department::where('id', $employee->department)->select('id', 'name')->first();
            $findTeams      = Team::where('id', $employee->designation)->select('id', 'name')->first();

            $employeeData = $employee->toArray();

            if (! empty($employee->joining_date)) {
                $employeeData['joining_date'] = Carbon::parse($employee->joining_date)->format('d-m-Y');
            }
            if (! empty($employee->birthday)) {
                $employeeData['birthday'] = Carbon::parse($employee->birthday)->format('d-m-Y');
            }
            if (! empty($employee->passport_exp_date)) {
                $employeeData['passport_exp_date'] = Carbon::parse($employee->passport_exp_date)->format('d-m-Y');
            }
            $employeeData['profile_image_url'] = (! empty($employee->profile_image_path) && Storage::disk('public')->exists($employee->profile_image_path))
                ? url('storage/' . $employee->profile_image_path)
                : url('assets/img/default-avatar.jpg');
            if (! empty($employee->user)) {
                $employeeData['name']  = $employee->user->name;
                $employeeData['email'] = $employee->user->email;
            }
            $isOwner = strtolower($employee->staff_role ?? '') === 'admin';
            $employeeData['department_name']  = $isOwner ? 'Owner' : ($findDepartMent?->name ?? '-');
            $employeeData['department_id']    = $findDepartMent?->id;
            $employeeData['designation_name'] = $isOwner ? 'Owner' : ($findTeams?->name ?? '-');
            $employeeData['designation_id']   = $findTeams?->id;
            $employeeData['employee_type_name'] = $isOwner ? 'NA' : ($employee->employeeType->name ?? '-');

            // Fetch Permissions for this specific employee
            $directPermissions = $employee->user ? $employee->user->permissions()->pluck('id')->toArray() : [];
            $inheritedPermissions = [];
            if ($employee->departmentRelation) {
                $deptPerms = $employee->departmentRelation->permissions()->pluck('id')->toArray();
                $inheritedPermissions = array_unique(array_merge($inheritedPermissions, $deptPerms));
            }
            if ($employee->designationRelation) {
                $teamPerms = $employee->designationRelation->permissions()->pluck('id')->toArray();
                $inheritedPermissions = array_unique(array_merge($inheritedPermissions, $teamPerms));
            }
            if ($employee->user) {
                $rolePerms = $employee->user->getPermissionsViaRoles()->pluck('id')->toArray();
                $inheritedPermissions = array_unique(array_merge($inheritedPermissions, $rolePerms));
            }
            $employeeData['permissions'] = [
                'direct' => $directPermissions,
                'inherited' => $inheritedPermissions
            ];

            // Use box_id from the employee record to ensure we get all departments for this company box
            $allDepartments = Department::with('allTeams')->where('box_id', $employee->box_id)->get();
            $employeeData['departments']      = $allDepartments;

            // Get all box users for reporting manager dropdown (exclude current employee)
            // Fetch all users from the same box, regardless of status
            $allEmployees = BoxUser::with([
                'user:id,name',
                'designationRelation:id,name',
                'departmentRelation:id,name',
                'assignedOffices.branch:id,name',
            ])
                ->where('box_id', $employee->box_id) // Filter by box_id to get all users in the same box
                ->where('id', '!=', $req_emp_id)     // Exclude current employee (can't be their own manager)
                ->whereNotNull('user_id')            // Only users with valid user_id
                ->orderBy('first_name', 'asc')
                ->orderBy('last_name', 'asc')
                ->select('id', 'user_id', 'employee_id', 'first_name', 'last_name', 'designation', 'department')
                ->get()
                ->map(function ($emp) {
                    $reportOfficeNames = $emp->assignedOffices->pluck('branch.name')->filter()->join(', ');

                    return [
                        'id'                  => $emp->id,
                        'name'                => $emp->user?->name ?? trim($emp->first_name . ' ' . $emp->last_name),
                        'employee_id'         => $emp->employee_id,
                        'designation_name'    => $emp->designationRelation?->name ?? '',
                        'department_name'     => $emp->departmentRelation?->name ?? '',
                        'report_office_names' => $reportOfficeNames,
                    ];
                });

            $employeeData['all_employees'] = $allEmployees;

            // Check if employee has assigned branches
            $hasAssignedBranches                   = ! empty($employee->assignedOffices) && $employee->assignedOffices->count() > 0;
            $employeeData['has_assigned_branches'] = $hasAssignedBranches;

            return response()->json([
                'message' => 'Employee details fetched successfully.',
                'data'    => $employeeData,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error'   => 'Something went wrong.',
                'details' => $th->getMessage(),
            ], 500);
        }
    }

    public function delete_employee(Request $request)
    {
        // Check permission to delete employee
        $this->requirePermission('attendance.admin.delete', 'You do not have permission to delete employees.');

        $employeeId = $request->input('employee_id');

        if (! $employeeId) {
            return response()->json(['error' => 'Employee ID is required.'], 400);
        }

        try {
            $boxUser = \App\Models\BoxUser::with([
                'bankDetails',
                'bankStatutory',
                'educations',
                'experiences',
                'families',
                'emergencyContacts',
                'attendances',
                'user',
            ])->find($employeeId);

            if (! $boxUser) {
                return response()->json(['error' => 'Employee not found.'], 404);
            }
            $boxUser->bankDetails()->delete();
            $boxUser->bankStatutory()->delete();
            $boxUser->educations()->delete();
            $boxUser->experiences()->delete();
            $boxUser->families()->delete();
            $boxUser->emergencyContacts()->delete();
            $boxUser->attendances()->delete();
            $boxUser->delete();
            return response()->json(['message' => 'Employee and associated data deleted successfully.'], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error'   => 'Failed to delete employee.',
                'details' => $th->getMessage(),
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        // Check export permission
        $this->requirePermission('attendance.admin.export', 'You do not have permission to export employees.');

        try {
            $user = Auth::user();

            $boxUser = BoxUser::where('user_id', $user->id)->with('box')->first();
            if (! $boxUser) {
                return response()->json([
                    'error' => 'Box user not found.',
                ], 404);
            }

            $boxId       = $boxUser->box_id;
            $boxName     = optional($boxUser->box)->name ?? 'unknown_box';
            $safeBoxName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $boxName);

            $directory = "exports/employees/{$safeBoxName}";
            $fileName  = 'employees_export_' . now()->format('Y_m_d_H_i_s') . '_' . \Str::random(5) . '.xlsx';

            // dd($boxId);
            ExportEmployeesJob::dispatch($directory, $fileName, $boxId);
            $fileUrl = url(Storage::url("{$directory}/{$fileName}"));
            return response()->json([
                'message'  => 'Employee export started. You will be notified once it is ready.',
                'file_url' => $fileUrl, // Optional: you can store and notify later when file is ready
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error'   => 'Failed to start employee export.',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function exportPDF()
    {
        // Check export permission
        $this->requirePermission('attendance.admin.export', 'You do not have permission to export employees.');

        $employees = Employee::with('bankDetail')->get();
        $pdf       = Pdf::loadView('employees.employee_pdf', compact('employees'));

        return $pdf->download('employees_report.pdf');
    }

    public function employetoggle(Request $request)
    {
        try {
            $employeeId = $request->input('employee_id');
            $status     = $request->input('status'); // Get status from request
            if (! in_array($status, ['Enabled', 'Disabled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status value provided.',
                ], 422);
            }

            $employee         = BoxUser::find($employeeId);
            $employee->status = $status; // Set to requested status
            $employee->save();

            return response()->json([
                'success' => true,
                'message' => "Employee status updated to {$employee->status}.",
                'data' => $employee,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function employee_update(Request $request)
    {
        // Check permission to update employee - allow both full admin and basic details update
        $hasAdminUpdate = PermissionHelper::hasModulePermission('attendance', 'update', 'admin');
        $hasDetailsUpdate = PermissionHelper::hasModulePermission('attendance', 'update', 'details');
        $hasDeptUpdate = PermissionHelper::hasModulePermission('attendance', 'update', 'details_department');
        $hasDesigUpdate = PermissionHelper::hasModulePermission('attendance', 'update', 'details_designation');

        if (!$hasAdminUpdate && !$hasDetailsUpdate) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to update employees.'
            ], 403);
        }

        try {
            // Normalize parameters for AJAX requests (e.g. from employee details permissions tab)
            if ($request->has('req_emp_id') && !$request->has('id')) {
                $request->merge(['id' => $request->req_emp_id]);
            }
            if ($request->has('permissions_data') && !$request->has('permissions')) {
                $request->merge(['permissions' => $request->permissions_data]);
            }

            // Convert permissions JSON string to array if needed (before validation)
            if ($request->has('permissions') && is_string($request->permissions)) {
                $decoded = json_decode($request->permissions, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $request->merge(['permissions' => $decoded]);
                } else {
                    $request->merge(['permissions' => []]);
                }
            }

            $validated = $request->validate([
                'id'                    => 'required|integer|exists:box_users,id',
                'name'                  => 'nullable|string|max:255',
                'first_name'            => 'nullable|string|max:255',
                'last_name'             => 'nullable|string|max:255',
                'employee_id'           => 'nullable|string|max:50',
                'joining_date'          => 'nullable|date',
                'username'              => 'nullable|string|max:255',
                'email'                 => 'nullable|email|max:255',
                'password'              => 'nullable|string|min:6|same:confirm_password',
                'confirm_password'      => 'nullable|string|min:6',
                'report_office'         => 'nullable',
                'report_office.*'       => 'nullable',
                'phone'                 => 'nullable|string|max:20',
                'company'               => 'nullable|string|max:255',
                'department'            => 'nullable|string|max:255',
                'designation'           => 'nullable|string|max:255',
                'can_login_anytime'     => 'nullable',
                'call_logs'             => 'nullable',
                'about'                 => 'nullable|string|max:1000',
                'profile_image'         => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
                'cropped_image'         => 'nullable|string',
                'passport_no'           => 'nullable|string',
                'passport_exp_date'     => 'nullable|date',
                'nationality'           => 'nullable|string',
                'religion'              => 'nullable|string',
                'gender'                => 'nullable|string',
                'birthday'              => 'nullable|date',
                'address'               => 'nullable|string',
                'marital_status'        => 'nullable|string',
                'Employment_of_spouse'  => 'nullable|string',
                'no_of_children'        => 'nullable|integer',
                'exlude_ip_restriction' => 'nullable',
                'exclude_round_robin'   => 'nullable',
                'user_box_timing'       => 'nullable',
                'working_days'          => 'nullable|array',
                'working_days.*'        => 'string|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
                'day_start_time'        => 'nullable',
                'day_end_time'          => 'nullable',
                'esi_no'                => 'nullable',
                'pf_no'                 => 'nullable',
                'resignation_date'      => 'nullable',
                'allowed_ips'           => 'nullable|array',
                'allowed_ips.*'         => 'nullable|ip',
                'marriage_date'         => 'nullable',
                'reporting_manager_id'  => 'nullable|integer|exists:box_users,id',
                'staff_role'            => 'nullable|string|max:255',
                'emp_type'              => 'nullable|string|max:255',
                'permissions'           => 'nullable|array',
                'permissions.*'         => 'integer|exists:permissions,id',
                'has_whatsapp_access'   => 'nullable',
            ]);

            // Ensure first_name and last_name are set correctly
            if (empty($validated['first_name']) && !empty($validated['name'])) {
                $validated['first_name'] = $validated['name'];
            }

            $employee = BoxUser::with('box')->findOrFail($validated['id']);

            if ($request->filled('phone')) {
                $duplicatePhone = BoxUser::where('phone', $request->phone)
                    ->where('id', '!=', $request->id)
                    ->with('box')
                    ->first();
                if ($duplicatePhone) {
                    $boxName = $duplicatePhone->box->name ?? 'another box';
                    return response()->json([
                        'status' => false,
                        'message' => "This phone number is already registered in box: `{$boxName}`. Duplicate numbers are not allowed across boxes.",
                        'errors' => ['phone' => ["Already in use in `{$boxName}`"]],
                    ], 422);
                }
            }

            if ($request->filled('email')) {
                $existingUser = User::where('email', $request->email)
                    ->where('id', '!=', $employee->user_id)
                    ->first();
                if ($existingUser) {
                    $boxUser = BoxUser::where('user_id', $existingUser->id)
                        ->with('box')
                        ->first();
                    if ($boxUser) {
                        $boxName = $boxUser->box->name ?? 'another box';
                        return response()->json([
                            'status' => false,
                            'message' => "This email address is already registered in box: `{$boxName}`. Duplicate emails are not allowed across boxes.",
                            'errors' => ['email' => ["Already in use in `{$boxName}`"]],
                        ], 422);
                    }
                }
            }

            $box = \App\Models\Box::find($employee->box_id);
            $crmLimit = $box->total_users ?? 0;
            $whatsappLimit = \App\Helpers\SubscriptionHelper::getWhatsAppAgentLimit(Auth::user());
            
            $hasWhatsappAccess = $employee->has_whatsapp_access;
            if ($request->has('has_whatsapp_access')) {
                $hasWhatsappAccess = $request->has_whatsapp_access == 1 ? 1 : 0;
            }

            // Automatically manage based on plans
            if ($crmLimit == 0 && $whatsappLimit > 0) {
                $hasWhatsappAccess = 1;
            } else if ($crmLimit > 0 && $whatsappLimit == 0) {
                $hasWhatsappAccess = 0;
            }

            if ($hasWhatsappAccess == 1 && $employee->has_whatsapp_access != 1) {
                if ($whatsappLimit > 0) {
                    $usedWhatsAppAgentsCount = BoxUser::where('box_id', $employee->box_id)->where('has_whatsapp_access', 1)->count();
                    if ($usedWhatsAppAgentsCount >= $whatsappLimit) {
                        return response()->json([
                            'status' => false,
                            'message' => "WhatsApp Agent limit reached. Your plan allows up to {$whatsappLimit} agents.",
                            'errors' => ['has_whatsapp_access' => ["Limit reached"]]
                        ], 422);
                    }
                }
            }
            $employee->has_whatsapp_access = $hasWhatsappAccess;

            $fillable = [
                'first_name',
                'last_name',
                'username',
                'phone',
                'company',
                'department',
                'designation',
                'about',
                'passport_no',
                'gender',
                'birthday',
                'address',
                'nationality',
                'religion',
                'marital_status',
                'Employment_of_spouse',
                'no_of_children',
                'exlude_ip_restriction',
                'exclude_round_robin',
                'user_box_timing',
                'working_days',
                'esi_no',
                'pf_no',
                'day_start_time',
                'day_end_time',
                'allowed_ips',
                'marriage_date',
                'reporting_manager_id',
                'can_login_anytime',
                'call_logs',
                'staff_role',
                'emp_type',
            ];

            // Check permissions for department and designation if they are being changed
            if (isset($validated['department']) && $validated['department'] != $employee->department) {
                if (!$hasAdminUpdate && !$hasDeptUpdate) {
                    return response()->json([
                        'status' => false,
                        'message' => 'You do not have permission to update the department.'
                    ], 403);
                }
            }

            if (isset($validated['designation']) && $validated['designation'] != $employee->designation) {
                if (!$hasAdminUpdate && !$hasDesigUpdate) {
                    return response()->json([
                        'status' => false,
                        'message' => 'You do not have permission to update the designation.'
                    ], 403);
                }
            }

            // If user is/becomes admin, force department and designation to "Owner"
            $currentRole = $validated['staff_role'] ?? $employee->staff_role ?? '';
            if (strtolower($currentRole) === 'admin') {
                $employee->department = 'Owner';
                $employee->designation = 'Owner';
                unset($validated['department']);
                unset($validated['designation']);
            }

            foreach ($fillable as $field) {
                if (array_key_exists($field, $validated)) {
                    $employee->$field = $validated[$field];
                }
            }
            // Explicitly handle can_login_anytime if it's not in validated (e.g. unchecked checkbox/select default)
            if ($request->has('can_login_anytime')) {
                $employee->can_login_anytime = (int)$request->can_login_anytime;
            }
            if ($request->has('call_logs')) {
                $employee->call_logs = (int)$request->call_logs;
            }

            if (! empty($validated['employee_id'])) {
                $existing = BoxUser::where('box_id', $employee->box_id)
                    ->where('employee_id', $validated['employee_id'])
                    ->where('id', '!=', $employee->id)
                    ->first();

                if ($existing) {
                    return response()->json([
                        'status'  => false,
                        'message' => "The employee ID '{$validated['employee_id']}' has already been taken by another employee in this box.",
                    ], 422);
                }
                $employee->employee_id = $validated['employee_id'];
            }

            // Parse date fields
            $dateFields = ['joining_date', 'birthday', 'resignation_date', 'passport_exp_date', 'marriage_date'];
            foreach ($dateFields as $field) {
                if ($request->filled($field)) {
                    $dateValue = $request->$field;
                    try {
                        $parsedDate = \Carbon\Carbon::createFromFormat('Y-m-d', $dateValue);
                    } catch (\Exception $e1) {
                        try {
                            $parsedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $dateValue);
                        } catch (\Exception $e2) {
                            return response()->json([
                                'status' => false,
                                'error'  => "Invalid date format for {$field}. Use YYYY-MM-DD or DD-MM-YYYY.",
                            ], 422);
                        }
                    }
                    $employee->$field = $parsedDate->format('Y-m-d');
                }
            }

            // Update linked user (users table) - name, email, mobile
            $findUser = User::find($employee->user_id);
            if ($findUser) {
                $emailChanged = false;
                $userNeedsSave = false;
                $tempPassword = null; // Initialize for email verification

                // Update name in users table
                $fullName = $validated['name'] ?? $findUser->name;
                if (array_key_exists('last_name', $validated)) {
                    $firstName = $validated['name'] ?? $validated['first_name'] ?? $employee->first_name ?? '';
                    $lastName = $validated['last_name'] ?? $employee->last_name ?? '';
                    $fullName = trim($firstName . ' ' . $lastName);
                }

                if ($fullName !== $findUser->name) {
                    $findUser->name = $fullName;
                    $userNeedsSave = true;
                }

                // Update email in users table - only set email_verified_at to null when email actually changes
                // Only process if email field is provided in the request
                if (array_key_exists('email', $validated) && !empty($validated['email'])) {
                    $newEmail = trim(strtolower($validated['email']));
                    $currentEmail = trim(strtolower($findUser->email ?? ''));
                    
                    // Only update if email actually changed (case-insensitive comparison)
                    if ($newEmail !== $currentEmail) {
                        $findUser->email             = trim($validated['email']); // Store with original case
                        $findUser->email_verified_at = null; // Email not verified when changed
                        $findUser->remember_token    = Str::random(60);
                        $emailChanged                = true;
                        $userNeedsSave               = true;
                        $tempPassword                = Str::random(10);
                        $findUser->password          = Hash::make($tempPassword);
                    }
                }

                // Update mobile/phone in users table (map phone to mobile)
                if (array_key_exists('phone', $validated) && !empty($validated['phone'])) {
                    $newPhone = $validated['phone'];
                    $oldPhone = $employee->getOriginal('phone');
                    
                    // Only check for conflicts if the phone number is actually being changed
                    // This allows saving other details (like permissions) even if there's an existing phone conflict
                    if ($newPhone !== $oldPhone) {
                        // SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'xxxx' for key 'users_mobile_unique'
                        // Check if another user already has this phone number
                        $conflictingUser = User::where('mobile', $newPhone)
                            ->where('id', '!=', $findUser->id)
                            ->with('employee') // Eager load employee relation (BoxUser)
                            ->first();

                        if ($conflictingUser) {
                            $conflictName = $conflictingUser->name;
                            $conflictEmpId = $conflictingUser->employee ? $conflictingUser->employee->employee_id : 'No ID';
                            $message = "Cannot update: The phone number '{$newPhone}' is already assigned to '{$conflictName}' ({$conflictEmpId}). Please use a unique phone number or remove it from the other user.";
                            
                            return response()->json([
                                'status' => false,
                                'message' => $message,
                                'errors' => ['phone' => [$message]]
                            ], 422);
                        }
                    }

                    if ($newPhone !== $findUser->mobile) {
                        $findUser->mobile = $newPhone;
                        $userNeedsSave = true;
                    }
                }

                // Update password if provided and email didn't change
                if (!empty($validated['password']) && !$emailChanged) {
                    $findUser->password = Hash::make($validated['password']);
                    $userNeedsSave = true;
                }

                // Save user changes
                if ($userNeedsSave) {
                    $findUser->save();
                }

                // Send verification email only when email is actually changed
                if ($emailChanged && !empty($tempPassword)) {
                    dispatch(new SendRegistrationVerifyJob(
                        $findUser->name,
                        $findUser->email,
                        $tempPassword,
                        $findUser->remember_token
                    ));
                }
            }

            // Note: box_users table fields (first_name, last_name, phone) are already updated above
            // in the fillable array loop, so they will be saved when $employee->save() is called

            // Handle profile image - prioritize cropped_image over profile_image file
            // Check cropped_image first (has priority)
            if ($request->has('cropped_image') && !empty($request->cropped_image)) {
                // Handle base64 cropped image
                $croppedImage = $request->cropped_image;
                if (preg_match('/^data:image\/(\w+);base64,/', $croppedImage, $matches)) {
                    $imageData = base64_decode(substr($croppedImage, strpos($croppedImage, ',') + 1));
                    $extension = $matches[1];
                    $imageName = 'employee-' . Str::random(8) . '-' . time() . '.' . $extension;
                    $imagePath = 'employees/profile_images/' . $imageName;

                    // Store the image
                    Storage::disk('public')->put($imagePath, $imageData);

                    // Delete old image if exists
                    if ($employee->profile_image_path) {
                        Storage::disk('public')->delete($employee->profile_image_path);
                    }

                    $employee->profile_image      = $imageName;
                    $employee->profile_image_path = $imagePath;
                }
            } elseif ($request->hasFile('profile_image')) {
                // Handle file upload only if cropped_image is not present
                $image                        = $request->file('profile_image');
                $imageName                    = 'employee-' . Str::random(8) . '-' . time() . '.' . $image->getClientOriginalExtension();
                $imagePath                    = $image->storeAs('employees/profile_images', $imageName, 'public');

                // Delete old image if exists
                if ($employee->profile_image_path) {
                    Storage::disk('public')->delete($employee->profile_image_path);
                }

                $employee->profile_image      = $imageName;
                $employee->profile_image_path = $imagePath;
            }

            // Save employee first
            $employee->save();

            // Handle report_office assignments
            // report_office can be null, empty string, string "null", or an array
            if (isset($validated['report_office'])) {
                $reportOffice = $validated['report_office'];

                // Convert string "null" to actual null
                if ($reportOffice === 'null' || $reportOffice === '') {
                    $reportOffice = null;
                }

                // If report_office is empty string, null, or not an array, treat as empty
                if (empty($reportOffice) || $reportOffice === null) {
                    // Remove all assignments
                    DB::transaction(function () use ($employee) {
                        AssignEmployeesOffice::where('box_user_id', $employee->id)->delete();
                    });

                    // Set report_office to null in employee record
                    $employee->report_office = null;
                    $employee->save();
                } elseif (is_array($reportOffice)) {
                    // Filter out empty strings and null values from the array
                    $reportOffice = array_values(array_filter($reportOffice, function ($value) {
                        return $value !== '' && $value !== null && $value !== false;
                    }));

                    // If array is empty after filtering, remove all assignments
                    if (empty($reportOffice)) {
                        DB::transaction(function () use ($employee) {
                            AssignEmployeesOffice::where('box_user_id', $employee->id)->delete();
                        });

                        $employee->report_office = null;
                        $employee->save();
                    } else {
                        // Array has valid values - process normally
                        DB::transaction(function () use ($employee, $reportOffice) {
                            // Remove old assignments
                            AssignEmployeesOffice::where('box_user_id', $employee->id)->delete();

                            $boxId = $employee->box_id;

                            // Insert new assignments
                            foreach ($reportOffice as $branchId) {
                                AssignEmployeesOffice::create([
                                    'box_user_id'      => $employee->id,
                                    'branch_office_id' => $branchId,
                                    'box_id'           => $boxId,
                                    'created_by'       => Auth::id(),
                                ]);
                            }
                        });

                        // Store all assigned report office IDs in employee record as JSON
                        $employee->report_office = json_encode($reportOffice);
                        $employee->save();
                    }
                }
            }

            // Update user role based on staff_role
            if (isset($validated['staff_role']) && $employee->user) {
                $staffRole = strtolower($validated['staff_role']);
                $user      = $employee->user;

                // Refresh user to ensure we have fresh data before role/permission sync
                $user->refresh();

                // If the user being updated is already an admin, don't allow changing their role via this method
                // as per user requirement: "if that is admin then don't change roles also"
                if ($user->hasRole('admin')) {
                    // Keep admin role, do nothing
                } elseif ($staffRole === 'admin') {
                    // Remove employee role and assign admin role
                    $user->removeRole('employee');
                    if (! $user->hasRole('admin')) {
                        $user->assignRole('admin');
                    }
                } else {
                    // Remove admin role and assign employee role
                    $user->removeRole('admin');
                    if (! $user->hasRole('employee')) {
                        $user->assignRole('employee');
                    }
                }
            }

            // Handle permissions - manage only from user_id, not box_id
            // If user is admin, skip permission assignment (admins have all permissions via role)
            $user = $employee->user;
            if (! $user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'User not found for this employee.',
                ], 404);
            }

            // Ensure we have fresh data for the user model
            $user->refresh();

            $currentStaffRole = strtolower($employee->staff_role ?? 'employee');
            if ($currentStaffRole !== 'admin') {
                // Only sync permissions if user has administrative update permission
                if ($request->has('permissions') && $hasAdminUpdate) {
                    $permissionsData = $request->permissions;
                    // Handle JSON string from frontend
                    if (is_string($permissionsData)) {
                        $permissionsData = json_decode($permissionsData, true);
                    }

                    // Ensure it's an array (even if empty)
                    if (! is_array($permissionsData)) {
                        $permissionsData = [];
                    }

                    // Remove duplicates and filter out invalid values
                    $permissionsData = array_filter(array_unique($permissionsData), function ($id) {
                        return is_numeric($id) && $id > 0;
                    });

                    $permissionsIdsToSync = [];
                    if (! empty($permissionsData)) {
                        $permissionsIdsToSync = Permission::whereIn('id', $permissionsData)
                            ->where('guard_name', 'web')
                            ->pluck('id')
                            ->toArray();
                    }
                    // Sync permissions (empty array removes all)
                    $user->syncPermissions($permissionsIdsToSync);
                }
                // If permissions field is not provided in update, keep existing permissions
                // Don't change them - only update when explicitly provided
            } else {
                // Admin users don't need individual permissions - they have all via role
                $user->syncPermissions([]);
            }

            // Clear permission cache to ensure changes take effect immediately
            $user->forgetCachedPermissions();

            // Final refresh to ensure subsequent queries in this request (if any) see new state
            $user->refresh();
            $employee->refresh();

            // Prepare response data with profile_image_url and updated user data
            $responseData = $employee->toArray();

            // Include updated user data (name, email, mobile, email_verified_at)
            if ($findUser) {
                $responseData['name'] = $findUser->name;
                $responseData['email'] = $findUser->email;
                $responseData['mobile'] = $findUser->mobile;
                $responseData['email_verified_at'] = $findUser->email_verified_at;
            }

            // Always include profile_image_url in response
            if ($employee->profile_image_path) {
                $imagePath = str_replace('\\', '/', $employee->profile_image_path);
                $responseData['profile_image_url'] = asset('storage/' . $imagePath);
                $responseData['profile_image_path'] = $imagePath;
            } else {
                $responseData['profile_image_url'] = null;
                $responseData['profile_image_path'] = null;
            }

            return response()->json([
                'status'  => true,
                'message' => 'Employee updated successfully',
                'data'    => $responseData,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to update employee',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function removeOfficeAssignment(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|exists:box_users,id',
                'office_id'   => 'required|exists:branch_offices,id',
            ]);

            $employeeId = $validated['employee_id'];
            $officeId   = $validated['office_id'];

            // Find and delete the office assignment
            $assignment = AssignEmployeesOffice::where('box_user_id', $employeeId)
                ->where('branch_office_id', $officeId)
                ->first();

            if (! $assignment) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Office assignment not found.',
                ], 404);
            }

            // Delete the assignment from assign_employees_offices table
            $assignment->delete();

            // Update the report_office field in box_users table
            $employee = BoxUser::find($employeeId);
            if ($employee) {
                // Update report_office JSON field
                if ($employee->report_office) {
                    $currentOffices = json_decode($employee->report_office, true) ?? [];
                    $updatedOffices = array_filter($currentOffices, function ($id) use ($officeId) {
                        return (string) $id != (string) $officeId; // Compare as strings to handle type differences
                    });
                    $employee->report_office = ! empty($updatedOffices) ? json_encode(array_values($updatedOffices)) : null;
                } else {
                    // If report_office is null, set it to empty array
                    $employee->report_office = null;
                }
                $employee->save();
            }

            return response()->json([
                'status'  => true,
                'message' => 'Office assignment removed successfully.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to remove office assignment.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function employee_family(Request $request)
    {
        $familyId = $request->input('id');
        $permissionType = $familyId ? 'update' : 'create';
        $canPerform = PermissionHelper::hasModulePermission('attendance', $permissionType, 'details_family');
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUser) {
             return response()->json(['status' => false, 'message' => 'User not associated with any box.'], 403);
        }

        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canPerform) {
             return response()->json(['status' => false, 'message' => 'You do not have permission to '.($familyId ? 'update' : 'add').' family information.'], 403);
        }

        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'employee_id'  => 'required|exists:box_users,id',
                'id'           => 'nullable|exists:employee_families,id', // family member id for update
                'name'         => 'nullable|string',
                'relationship' => 'nullable|string',
                'dob'          => 'nullable|string',
                'contact'      => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            
            // Get authenticated user and box info - already fetched above as $user and $boxUser
            $authUser = $user; 
            // $boxUser already fetched
            $box_id   = $boxUser->box_id;

            // Find the employee to whom the family member is related
            $employee = BoxUser::find($request->employee_id);
            if (! $employee) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Employee not found',
                ], 404);
            }

            // Update case
            if ($request->filled('id')) {
                $familyMember = $employee->families()->where('id', $request->id)->first();

                if (! $familyMember) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Family member not found for update.',
                    ], 404);
                }

                $familyMember->update([
                    'updated_by'   => $authUser->id,
                    // 'box_id'       => $box_id, // Usually box_id shouldn't change, but if it must ensure logic allows it. Assuming family belongs to employee's box or current context.
                    'name'         => $request->name,
                    'relationship' => $request->relationship,
                    'dob'          => $request->dob,
                    'contact'      => $request->contact,
                ]);

                return response()->json([
                    'status'  => true,
                    'message' => 'Family member updated successfully.',
                ], 200);
            }

            // Create case
            $familyMember = $employee->families()->create([
                'created_by'   => $authUser->id,
                'box_id'       => $box_id,
                'name'         => $request->name,
                'relationship' => $request->relationship,
                'dob'          => $request->dob,
                'contact'      => $request->contact,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Family member added successfully.',
                'id'      => $familyMember->id,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function employee_education(Request $request)
    {
        $educationId = $request->input('id');
        $permissionType = $educationId ? 'update' : 'create';
        $hasPermission = PermissionHelper::hasModulePermission('attendance', $permissionType, 'details_education');
        
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
            return response()->json([
                'status' => 404, 
                'message' => 'You do not have permission to perform this action.',
            ], 404);
        }

        try {
            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|exists:box_users,id',
                'id'          => 'nullable|exists:employee_educations,id',
                'institute'   => 'nullable|string',
                'degree'      => 'nullable|string',
                'start_year'  => 'nullable|string',
                'end_year'    => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $authUser = Auth::user();
            $boxUser  = BoxUser::where('user_id', $authUser->id)->first();
            $box_id   = $boxUser ? $boxUser->box_id : null;

            $employee = BoxUser::find($request->employee_id);
            if (! $employee) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Employee not found.',
                ], 404);
            }

            if ($request->filled('id')) {
                $education = $employee->educations()->where('id', $request->id)->first();
                if (! $education) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Education record not found.',
                    ], 404);
                }

                $education->update([
                    'box_id'     => $box_id,
                    'updated_by' => $authUser->id,
                    'institute'  => $request->institute,
                    'degree'     => $request->degree,
                    'start_year' => $request->start_year,
                    'end_year'   => $request->end_year,
                ]);

                return response()->json([
                    'status'  => true,
                    'message' => 'Education updated successfully.',
                ], 200);
            }

            // Create new education
            $education = $employee->educations()->create([
                'box_user_id' => $employee->id,
                'box_id'      => $box_id,
                'created_by'  => $authUser->id,
                'institute'   => $request->institute,
                'degree'      => $request->degree,
                'start_year'  => $request->start_year,
                'end_year'    => $request->end_year,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Education added successfully.',
                'id'      => $education->id,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function employee_experience(Request $request)
    {
        $experienceId = $request->input('id');
        $permissionType = $experienceId ? 'update' : 'create';
        $hasPermission = PermissionHelper::hasModulePermission('attendance', $permissionType, 'details_experience');
        
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
            return response()->json([
                'status' => 404, 
                'message' => 'You do not have permission to perform this action.',
            ], 404);
        }

        try {
            $validator = Validator::make($request->all(), [
                'employee_id'     => 'required|exists:box_users,id',
                'company_name'    => 'required|string|max:255',
                'position'        => 'required|string|max:255',
                'start_date'      => 'nullable|date',
                'end_date'        => 'nullable|date',
                'current_working' => 'nullable',
                'id'              => 'nullable|exists:employee_experiences,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            $authUser = Auth::user();
            $employee = BoxUser::find($request->employee_id);
            $boxUser  = BoxUser::where('user_id', $authUser->id)->first();
            $box_id   = $boxUser?->box_id;
            $data     = [
                'box_id'          => $box_id,
                'company_name'    => $request->company_name,
                'position'        => $request->position,
                'start_date'      => $request->start_date,
                'end_date'        => $request->end_date,
                'current_working' => $request->current_working,
                'updated_by'      => $authUser->id,
            ];

            if ($request->filled('id')) {
                $experience = $employee->experiences()->where('id', $request->id)->first();
                if (! $experience) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Experience not found.',
                    ], 404);
                }
                $data['updated_by'] = $authUser->id;
                $experience->update($data);
                return response()->json([
                    'status'  => true,
                    'message' => 'Experience updated successfully.',
                ], 200);
            } else {
                $data['created_by'] = $authUser->id;
                $employee->experiences()->create($data);
                return response()->json([
                    'status'  => true,
                    'message' => 'Experience added successfully.',
                ], 201);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function employee_bank_safety(Request $request)
    {
        $this->requirePermission('attendance.details_bank.update', 'You do not have permission to update bank information.');
        try {
            //  Validate request
            $validator = Validator::make($request->all(), [
                'employee_id'      => 'required|exists:box_users,id',
                'salary_basis'     => 'nullable|string',
                'salary_amount'    => 'nullable|string',
                'payment_type'     => 'nullable|string',
                'pf_contribution'  => 'nullable|string',
                'pf_no'            => 'nullable|string',
                'pf_rate'          => 'nullable|string',
                'pf_additional'    => 'nullable|string',
                'pf_total'         => 'nullable|string',
                'esi_contribution' => 'nullable|string',
                'esi_no'           => 'nullable|string',
                'esi_rate'         => 'nullable|string',
                'esi_additional'   => 'nullable|string',
                'esi_total'        => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            //  Get employee and box_id in one go
            $authUser = Auth::user();
            $boxUser  = BoxUser::with('bankStatutory') // eager load relation if needed
                ->where('user_id', $authUser->id)
                ->first();

            if (! $boxUser || ! $boxUser->box_id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Authenticated user missing valid box_id.',
                ], 403);
            }

            $employee = BoxUser::find($request->employee_id);
            if (! $employee) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Employee not found.',
                ], 404);
            }

            //  Prepare data
            $data = $request->only([
                'salary_basis',
                'salary_amount',
                'payment_type',
                'pf_contribution',
                'pf_no',
                'pf_rate',
                'pf_additional',
                'pf_total',
                'esi_contribution',
                'esi_no',
                'esi_rate',
                'esi_additional',
                'esi_total',
            ]);

            $data['box_id']     = $boxUser->box_id;
            $data['updated_by'] = $authUser->id;

            //  Insert/update with audit tracking
            $bankStatutory = $employee->bankStatutory()->updateOrCreate(
                ['box_user_id' => $employee->id],
                $data
            );

            if ($bankStatutory->wasRecentlyCreated) {
                $bankStatutory->created_by = $authUser->id;
                $bankStatutory->save();
            }

            return response()->json([
                'status'  => true,
                'message' => $bankStatutory->wasRecentlyCreated
                    ? 'Bank/statutory info added successfully.'
                    : 'Bank/statutory info updated successfully.',
            ], 200);
        } catch (\Throwable $th) {
            \Log::error('Failed to save employee bank/statutory info', [
                'user_id' => Auth::id(),
                'error'   => $th->getMessage(),
                'trace'   => $th->getTraceAsString(),
                'input'   => $request->all(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function update_bank_employee(Request $request)
    {
        $bankId = $request->input('bank_id');
        $permissionType = $bankId ? 'update' : 'create';
        
        $canPerform = PermissionHelper::hasModulePermission('attendance', $permissionType, 'details_bank');

        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUser) {
             return response()->json(['status' => false, 'message' => 'User not associated with any box.'], 403);
        }

        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canPerform) {
             return response()->json(['status' => false, 'message' => 'You do not have permission to '.($bankId ? 'update' : 'add').' bank information.'], 403);
        }
        $validated = $request->validate([
            'employee_id'    => 'required|exists:box_users,id',
            'bank_name'      => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'ifsc_code'      => 'required|string|max:20',
            'branch'         => 'required|string|max:255',
            'bank_type'      => 'required|in:primary,secondary',
            'bank_id'        => 'nullable|exists:employee_bank_details,id',
        ]);

        $authUser = Auth::user();
        $boxUser  = BoxUser::where('user_id', $authUser->id)->first();

        $employee = BoxUser::findOrFail($validated['employee_id']);

        $data = [
            'box_id'         => $boxUser->box_id,
            'bank_name'      => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'ifsc_code'      => $validated['ifsc_code'],
            'branch'         => $validated['branch'],
            'bank_type'      => $validated['bank_type'],
            'updated_by'     => $authUser->id,
        ];

        if (! empty($validated['bank_id'])) {
            $bankDetail = $employee->bankDetails()->where('id', $validated['bank_id'])->first();
            if (! $bankDetail) {
                return response()->json(['status' => false, 'message' => 'Bank detail not found for update.'], 404);
            }
            $bankDetail->update($data);
            $message = 'Bank details updated successfully.';
        } else {
            $data['created_by'] = $authUser->id;
            $bankDetail         = $employee->bankDetails()->create($data);
            $message            = 'Bank details saved successfully.';
        }

        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $bankDetail,
        ]);
    }

    public function employee_emergency(Request $request)
    {
        $this->requirePermission('attendance.details_emergency.update', 'You do not have permission to update emergency contact information.');
        try {
            $validator = Validator::make($request->all(), [
                'employee_id'          => 'required|exists:box_users,id',
                'id'                   => 'nullable|exists:employee_emergency_contacts,id', // top-level ID for backward support
                'contacts'             => 'required|array|min:1',
                'contacts.*.id'        => 'nullable|exists:employee_emergency_contacts,id',
                'contacts.*.name'      => 'nullable|string|max:255',
                'contacts.*.relation'  => 'nullable|string|max:255',
                'contacts.*.phone'     => 'nullable|string|max:20',
                'contacts.*.phone_alt' => 'nullable|string|max:20',
                'contacts.*.type'      => 'nullable|in:primary,secondary',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $authUser = Auth::user();
            $boxUser  = BoxUser::where('user_id', $authUser->id)->first();
            $employee = BoxUser::findOrFail($request->employee_id);
            $box_id   = $boxUser->box_id;

            $contacts = $request->contacts;

            // Apply top-level id to first contact if not already set
            if (! empty($request->id) && empty($contacts[0]['id'])) {
                $contacts[0]['id'] = $request->id;
            }

            $responseData = [];

            foreach ($contacts as $contact) {
                if (! empty($contact['id'])) {
                    $existing = $employee->emergencyContacts()->where('id', $contact['id'])->first();
                    if (! $existing) {
                        return response()->json([
                            'status'  => false,
                            'message' => "Emergency contact with ID {$contact['id']} not found.",
                        ], 404);
                    }

                    $updateData = [
                        'name'       => $contact['name'] ?? $existing->name,
                        'relation'   => $contact['relation'] ?? $existing->relation,
                        'phone'      => $contact['phone'] ?? $existing->phone,
                        'phone_alt'  => $contact['phone_alt'] ?? $existing->phone_alt,
                        'type'       => $contact['type'] ?? $existing->type,
                        'updated_by' => $authUser->id,
                    ];

                    $existing->update($updateData);
                    $responseData[] = $existing;
                } elseif (
                    ! empty($contact['name']) &&
                    ! empty($contact['phone']) &&
                    ! empty($contact['type'])
                ) {
                    $createData = [
                        'box_user_id' => $employee->id,
                        'box_id'      => $box_id,
                        'name'        => $contact['name'],
                        'relation'    => $contact['relation'] ?? null,
                        'phone'       => $contact['phone'],
                        'phone_alt'   => $contact['phone_alt'] ?? null,
                        'type'        => $contact['type'],
                        'created_by'  => $authUser->id,
                        'updated_by'  => $authUser->id,
                    ];

                    $newContact     = $employee->emergencyContacts()->create($createData);
                    $responseData[] = $newContact;
                }
                // If fields are incomplete, skip contact
            }

            return response()->json([
                'status'  => true,
                'message' => 'Emergency contact(s) saved successfully.',
                'data'    => $responseData,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function delete_bank(Request $request)
    {
        $canDelete = PermissionHelper::hasModulePermission('attendance', 'delete', 'details_bank');

        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canDelete) {
             return response()->json(['status' => false, 'message' => 'You do not have permission to delete bank information.'], 403);
        }

        try {
            $bankId = $request->input('bank_id');

            $bank = EmployeeBankDetail::findOrFail($bankId);

            $bank->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bank deleted successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bank: ' . $th->getMessage(),
            ], 500);
        }
    }

    //delete_family

    public function delete_family_info(Request $request)
    {
        try {
            // Check permission: (Module, Action, SubModule)
            // This checks for: attendance.details_family.delete
            $hasPermission = PermissionHelper::hasModulePermission('attendance', 'delete', 'details_family');
            
            $user = Auth::user();
            $boxUser = BoxUser::where('user_id', $user->id)->first();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
                return response()->json([
                    'status' => 404, 
                    'message' => 'You do not have permission to perform this action.',
                ], 404);
            }
            $familyId = $request->input('family_id');
            $family   = Employeefamily::findOrFail($familyId);

            $family->delete();

            return response()->json([
                'success' => true,
                'message' => 'Family member deleted successfully.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete family member: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function delete_emp_education(Request $request)
    {
        try {
            // Check permission: (Module, Action, SubModule)
            // This checks for: attendance.details_education.delete
            $hasPermission = PermissionHelper::hasModulePermission('attendance', 'delete', 'details_education');
            
            $user = Auth::user();
            $boxUser = BoxUser::where('user_id', $user->id)->first();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
                return response()->json([
                    'status' => 404, 
                    'message' => 'You do not have permission to perform this action.',
                ], 404);
            }
            $educationId = $request->input('education_id');
            $education   = EmployeeEducation::findOrFail($educationId); // throws 404 if not found

            $education->delete();

            return response()->json([
                'success' => true,
                'message' => 'Education record deleted successfully.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete education record: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function delete_emp_experience(Request $request)
    {
        try {
            // Check permission: (Module, Action, SubModule)
            // This checks for: attendance.details_experience.delete
            $hasPermission = PermissionHelper::hasModulePermission('attendance', 'delete', 'details_experience');
            
            $user = Auth::user();
            $boxUser = BoxUser::where('user_id', $user->id)->first();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
                return response()->json([
                    'status' => 404, 
                    'message' => 'You do not have permission to perform this action.',
                ], 404);
            }
            $experienceId = $request->input('experience_id');
            $experience   = EmployeeExperience::findOrFail($experienceId); // throws 404 if not found

            $experience->delete();

            return response()->json([
                'success' => true,
                'message' => 'Experience record deleted successfully.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete experience record: ' . $th->getMessage(),
            ], 500);
        }
    }

    //

    public function delete_emp_emergency(Request $request)
    {
        try {
            $emergencyId = $request->input('emergency_id');
            $emergency   = EmployeeEmergencyContact::findOrFail($emergencyId);
            $emergency->delete();

            return response()->json([
                'success' => true,
                'message' => 'Emergency contact deleted successfully.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete emergency contact.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function department_manage(Request $request)
    {
        try {
            $user_id       = Auth::id();
            $boxUser = BoxUser::where('user_id', $user_id)->first();
            $box_id = $boxUser ? $boxUser->box_id : null;

            $getDepartment = Department::where('box_id', $box_id)->with('allTeams')
                ->where('status', '1')
                ->get();
            return response()->json([
                'status'  => 200,
                'data'    => $getDepartment,
                'message' => 'All Department And Team Is Retrieved',
                'success' => true,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 500,
                'message' => 'Something went wrong',
                'error'   => $th->getMessage(),
                'success' => false,
            ]);
        }
    }

    public function role_permission()
    {
        return view('employees.crm_setting.role_permission');
    }

    public function delete_shift_employe(Request $request)
    {
        $canDelete = PermissionHelper::hasModulePermission('attendance', 'delete', 'details_shift');
        
        // Check Admin/Superadmin/BoxAdmin
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canDelete) {
            return response()->json(['message' => 'You do not have permission to delete shifts.'], 403);
        }

        try {
            // $userid = Auth::id(); // Already fetched above
            // $boxuser = BoxUser::where('user_id',$userId)->first(); // Already fetched above as $boxUser
            
            if (!$boxUser) {
                return response()->json(['message' => 'Box User not found'], 404);
            }

            $box = Box::where('id', $boxUser->box_id)->select('id')->first();
            if (!$box) {
                return response()->json(['message' => 'Box not found'], 404);
            }
            $emp_id  = $request->input('emp_id');
            $updated = BoxUser::where('box_id', $box->id)
                ->where('id', $emp_id)
                ->update(['shift_id' => null]);
            if ($updated) {
                return response()->json(['message' => 'Shift removed successfully']);
            } else {
                return response()->json(['message' => 'Employee or box not found'], 404);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
    public function shiftemployeassign(Request $request)
    {
        $canCreate = PermissionHelper::hasModulePermission('attendance', 'create', 'details_shift');
        $canUpdate = PermissionHelper::hasModulePermission('attendance', 'update', 'details_shift');
        if (!$canCreate && !$canUpdate) {
            return response()->json([
                'status' => 404, 
                'message' => 'You do not have permission to assign shifts.'
            ], 404);
        }

        try {
            $currentUserId = Auth::id();
            $currentBoxUser = BoxUser::where('user_id', $currentUserId)->first();
            
            if (!$currentBoxUser) {
                return response()->json(['status' => 404, 'message' => 'Current user not associated with any box'], 404);
            }

            $box = Box::where('id', $currentBoxUser->box_id)->select('id', 'name')->first();
            if (!$box) {
                return response()->json(['status' => 404, 'message' => 'Box not found'], 404);
            }

            $employeeId = $request->input('employee_id'); // Match the request input name used in JS
            $shiftId = $request->input('shift_id');

            // Validate shift exists and belongs to the same box (optional but safer)
            /*
            $shiftExists = \App\Models\Shift::where('id', $shiftId)->where('box_id', $box->id)->exists();
            if (!$shiftExists) {
                return response()->json(['status' => 404, 'message' => 'Shift not found'], 404);
            }
            */

            $user = BoxUser::where('box_id', $box->id)
                ->where('id', $employeeId)
                ->first();

            if (!$user) {
                return response()->json(['status' => 404, 'message' => 'Employee not found in this box'], 404);
            }

            $updated = BoxUser::where('box_id', $box->id)
                ->where('id', $employeeId)
                ->update(['shift_id' => $shiftId]);

            if ($updated) {
                return response()->json(['status' => 200, 'message' => 'Shift assigned successfully']);
            } else {
                return response()->json(['status' => 400, 'message' => 'Failed to assign shift (no changes made or error)']);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => 'Server error', 'error' => $th->getMessage()], 500);
        }
    }

    public function toggleLocationTracking(Request $request)
    {
        try {
            $empId = $request->input('emp_id');
            $status = $request->input('status');

            $employee = BoxUser::where('user_id', $empId)
                ->orWhere('id', $empId)
                ->first();

            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
            }

            $employee->is_location_tracking = $status ? 1 : 0;
            $employee->save();

            return response()->json(['success' => true, 'message' => 'Location tracking status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
}
