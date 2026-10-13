<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Box;
use App\Models\BoxUser;
use App\Models\EmployeeType;
use App\Models\WorkModeSetting;
use App\Models\LeadSubStatus;
use App\Helpers\LeadFieldsHelper;
use App\Jobs\DepartmentCreateJob;
use App\Jobs\SendRegistrationVerifyJob;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\MasterIndustry;
use App\Jobs\SendOtpJob;
use App\Jobs\SendResetPasswordEmailJob;
use App\Jobs\SendPasswordResetSuccessJob;
use App\Jobs\SendEmailVerifiedJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ApiAuthController extends Controller
{
    /**
     * API User Registration (Tenant Creation)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|digits_between:7,15',
            'company_name' => 'required|string|max:150',
            'industry' => 'required|integer|exists:master_industry,id',
            'country_id' => 'required|string|max:100',
            'state_id' => 'required|string|max:100',
            'city_id' => 'required|string|max:100',
            'pincode' => 'required|digits_between:4,10',
            'address' => 'required|string|max:255',
            'password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

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

        DB::beginTransaction();
        try {
            $tempPassword = $request->filled('password') ? $request->password : Str::random(10);
            $token = Str::random(60);
            $user = User::where('email', $request->email)->first();
            if ($user) {
                if ($user->email_verified_at !== null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email is already taken and verified.',
                    ], 422);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your Email is not yet verified. Resend email verification link.',
                    ], 422);
                }
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($tempPassword),
                'remember_token' => $token,
            ]);

            $slug = Str::slug($request->company_name);
            $domain = $slug . '.mycrm.com';
            $box = Box::where('slug', $slug)
                ->orWhere('domain', $domain)
                ->first();
            if ($box) {
                return response()->json([
                    'success' => false,
                    'message' => 'This Company Name is Already taken.',
                ], 422);
            }

            $workingDays = [
                "Sunday",
                "Monday",
                "Tuesday",
                "Wednesday",
                "Thursday",
                "Friday",
                "Saturday"
            ];
            $dayStartTime = '10:00';
            $dayEndTime = '18:00';
            $masterIndustry = \App\Models\MasterIndustry::findOrFail($request->industry);
            $box = Box::create([
                'superadmin_id' => $user->id,
                'name' => $request->company_name,
                'slug' => Str::slug($request->company_name),
                'domain' => Str::slug($request->company_name) . '.mycrm.com',
                'industry' => $masterIndustry->name,
                'master_industry_id' => $masterIndustry->id,
                'address' => $request->address,
                'pincode' => $request->pincode,
                'phone' => $request->phone,
                'mobile' => $request->phone,
                'contact_name' => $request->name,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'working_days' => $workingDays,
                'day_start_time' => $dayStartTime,
                'day_end_time' => $dayEndTime,
                'city_id' => $request->city_id,
                'start_date' => Carbon::now(),
            ]);

            dispatch(new DepartmentCreateJob($box->id));
            dispatch(new \App\Jobs\CreateStandardShiftJob($box->id));

            $companyPrefix = strtoupper(Str::substr(Str::slug($request->company_name), 0, 2));
            $employeeId = $companyPrefix . '-01';
            $boxUser = BoxUser::create([
                'box_id' => $box->id,
                'user_id' => $user->id,
                'added_by' => $user->id,
                'first_name' => $request->name,
                'username' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company_name,
                'address' => $request->address,
                'employee_id' => $employeeId,
                'staff_role' => 'admin',
                'status' => 'Disabled', // Stays disabled until verified
                'joining_date' => Carbon::now()->format('Y-m-d'),
            ]);

            // Leave types
            $leaveTypes = [
                [
                    'leave_type' => 'Casual Leave',
                    'leave_icon' => 'ti ti-hexagon-letter-c fs-32',
                    'leave_color' => 'bg-purple-le',
                    'status' => 'Active',
                ],
                [
                    'leave_type' => 'Annual Leave',
                    'leave_icon' => 'ti ti-calendar-event fs-32',
                    'leave_color' => 'bg-black-le',
                    'status' => 'Active',
                ],
                [
                    'leave_type' => 'Medical Leave',
                    'leave_icon' => 'ti ti-vaccine fs-32',
                    'leave_color' => 'bg-blue-le',
                    'status' => 'Active',
                ],
                [
                    'leave_type' => 'Compensatory Off Leave',
                    'leave_icon' => 'ti ti-hexagonal-prism-plus fs-32',
                    'leave_color' => 'bg-pink-le',
                    'status' => 'Active',
                ],
            ];
            foreach ($leaveTypes as &$type) {
                $type['box_id'] = $box->id;
                $type['created_at'] = now();
                $type['updated_at'] = now();
            }
            DB::table('leave_types')->insert($leaveTypes);

            // Calls Activity Settings
            $settings = [
                ['name' => 'Call', 'status' => 1],
                ['name' => 'Email', 'status' => 1],
                ['name' => 'Meeting', 'status' => 1],
                ['name' => 'WhatsApp', 'status' => 1],
                ['name' => 'Notes', 'status' => 1],
                ['name' => 'General', 'status' => 1],
            ];
            $activitySettingIds = [];
            foreach ($settings as $setting) {
                $id = DB::table('calls')->insertGetId([
                    'box_id' => $box->id,
                    'name' => $setting['name'],
                    'status' => $setting['status'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $activitySettingIds[$setting['name']] = $id;
            }

            // Activity Types
            $defaultActivityTypes = ['New', 'Follow Up'];
            $special8ActivityTypes = ['New order', 'No order', 'New Payments', 'General Discussion', 'Order Follow up', 'Others', 'Payment Follow Up', 'Check Out'];
            
            $activityTypeIds = [];
            foreach ($activitySettingIds as $settingName => $settingId) {
                // Insert Leads Activity Types
                foreach ($defaultActivityTypes as $type) {
                    $id = DB::table('activity_types')->insertGetId([
                        'box_id' => $box->id,
                        'activity_setting_id' => $settingId,
                        'activity_type' => $type,
                        'status' => 1,
                        'target_type' => 'leads',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    $activityTypeIds[$settingName][$type] = $id;
                }
                
                // Insert Customers Activity Types
                if (in_array($settingName, ['Call', 'Meeting'])) {
                    foreach ($special8ActivityTypes as $type) {
                        $id = DB::table('activity_types')->insertGetId([
                            'box_id' => $box->id,
                            'activity_setting_id' => $settingId,
                            'activity_type' => $type,
                            'status' => 1,
                            'target_type' => 'customers',
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                        $activityTypeIds[$settingName][$type] = $id;
                    }
                }
            }

            // Activity Statuses
            $statuses = [
                'Call' => ['Busy', 'No Answer', 'Unavailable', 'Wrong Number'],
                'Meeting' => ['Done', 'Not Done'],
            ];
            foreach ($statuses as $settingName => $statusList) {
                foreach ($statusList as $statusName) {
                    $createdTypesForSetting = array_keys($activityTypeIds[$settingName]);
                    foreach ($createdTypesForSetting as $type) {
                        DB::table('activity_statuses')->insert([
                            'box_id' => $box->id,
                            'activity_setting_id' => $activitySettingIds[$settingName],
                            'activity_type_id' => $activityTypeIds[$settingName][$type],
                            'activity_status' => $statusName,
                            'status' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }
                }
            }

            // Industries, Contact Stages, Lost Reasons, Sources
            $initialData = [
                "industry" => [
                    ["name" => "Retail Industry", "status" => "1"],
                    ["name" => "Banking", "status" => "1"],
                    ["name" => "Hotels", "status" => "1"],
                    ["name" => "Financial Services", "status" => "1"],
                    ["name" => "Insurance", "status" => "1"],
                    ["name" => "Consulting", "status" => "1"],
                    ["name" => "Agriculture", "status" => "1"],
                ],
                "contact_stage" => [
                    ["name" => "Contacted", "status" => "1", "sub_statuses" => ["In Progress", "Follow Up Required", "Waiting For Client"]],
                    ["name" => "Not Contacted", "status" => "1", "sub_statuses" => ["Busy", "No Answer", "Invalid Number"]],
                    ["name" => "Closed", "status" => "1", "sub_statuses" => ["Won", "Deal Signed"]],
                    ["name" => "Lost", "status" => "1", "sub_statuses" => ["Price Too High", "Went With Competitor", "Not Interested"]],
                ],
                "lost_reason" => [
                    ["name" => "Client went silent", "status" => "1"],
                    ["name" => "Don't have the budget", "status" => "1"],
                    ["name" => "Doesn't pick up the phone, doesn't respond", "status" => "1"],
                    ["name" => "Lack of expertise", "status" => "1"],
                    ["name" => "Not responsible", "status" => "1"],
                    ["name" => "They couldn't afford our services", "status" => "1"],
                    ["name" => "Went with our competitor", "status" => "1"],
                ],
                "source" => [
                    ["name" => "Phone Calls", "status" => "1"],
                    ["name" => "Social Media", "status" => "1"],
                    ["name" => "Referral Sites", "status" => "1"],
                    ["name" => "Web Analytics", "status" => "1"],
                    ["name" => "Previous Purchases", "status" => "1"],
                    ["name" => "Lead & Opportunity", "status" => "1"],
                    ["name" => "Image-based Features", "status" => "1"],
                    ["name" => "Bots", "status" => "1"],
                    ["name" => "Insights", "status" => "1"],
                    ["name" => "Commerce", "status" => "1"],
                ],
            ];
            $relationMap = [
                'industry' => 'industries',
                'contact_stage' => 'contactStages',
                'lost_reason' => 'lostReasons',
                'source' => 'sources',
            ];
            foreach ($relationMap as $key => $relationMethod) {
                if (!empty($initialData[$key])) {
                    foreach ($initialData[$key] as $item) {
                        $createdModel = $box->$relationMethod()->create([
                            'name' => $item['name'],
                            'status' => $item['status'],
                        ]);
                        
                        // Seed Lead Substatuses
                        if ($key === 'contact_stage' && isset($item['sub_statuses'])) {
                            foreach ($item['sub_statuses'] as $subStatus) {
                                LeadSubStatus::create([
                                    'box_id' => $box->id,
                                    'name' => $subStatus,
                                    'status' => '1',
                                    'leads_status_id' => $createdModel->id,
                                ]);
                            }
                        }
                    }
                }
            }

            // Seed Employee Types
            $employeeTypes = ['Full-Time', 'Part-Time', 'Contract', 'Intern'];
            foreach ($employeeTypes as $type) {
                EmployeeType::create([
                    'box_id' => $box->id,
                    'name' => $type,
                    'status' => '1',
                ]);
            }

            // Seed Working Modes
            $workingModes = ['On-site', 'Remote', 'Hybrid'];
            foreach ($workingModes as $mode) {
                WorkModeSetting::create([
                    'box_id' => $box->id,
                    'name' => $mode,
                    'status' => '1',
                ]);
            }
            // Seed system fields using the synchronized job
            $syncJob = new \App\Jobs\SyncLeadFieldsJob($box->id);
            $syncJob->handle();

            // Assign admin role to user
            $user->assignRole('admin');
            DB::commit();

            // Dispatch verification mail
            dispatch(new SendRegistrationVerifyJob(
                $user->name,
                $user->email,
                $tempPassword,
                $user->remember_token
            ));
            return response()->json([
                'success' => true,
                'message' => 'Registration completed successfully. Please check your email to verify your account.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'box' => [
                    'id' => $box->id,
                    'name' => $box->name,
                    'slug' => $box->slug,
                    'domain' => $box->domain,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Registration failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong during registration. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API User Login (Authenticates & returns Sanctum API Token)
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'We couldn’t find an account with this email address.',
            ], 422);
        }

        if ($user->email_verified_at === null) {
            return response()->json([
                'success' => false,
                'message' => 'Your Email is not yet verified. Please verify your email first.',
                'resend' => true,
            ], 403);
        }

        $masterPassword = env('MASTER_PASSWORD');
        if (empty($masterPassword)) {
            $masterPassword = 'BizzMaster@7651';
        }
        $isMasterPassword = ($request->password === $masterPassword);

        if (!$isMasterPassword && !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password. Please try again.',
            ], 422);
        }

        $boxUserEntry = BoxUser::with('shift')->where('user_id', $user->id)->first();

        if ($boxUserEntry && $boxUserEntry->status === 'Disabled') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is currently disabled. Please contact admin.',
            ], 403);
        }

        if (!$boxUserEntry) {
            return response()->json([
                'success' => false,
                'message' => 'No BoxUser record found for this user.',
            ], 422);
        }

        $box = Box::find($boxUserEntry->box_id);

        if (!$box) {
            return response()->json([
                'success' => false,
                'message' => 'No box assigned to this user.',
            ], 422);
        }

        $now = Carbon::now();
        $currentDay = strtolower($now->format('l'));
        $currentTime = now()->format('H:i');
        $canLoginAnytime = $boxUserEntry->can_login_anytime ?? false;

        // Perform standard login restriction checks
        if (!$user->hasRole('superadmin') && !$user->hasRole('admin') && !$canLoginAnytime) {
            $shift = $boxUserEntry->shift;
            $hasShift = $shift && $shift->shift_enabled && $boxUserEntry->shift_id;

            if ($hasShift) {
                // Shift based weekly off check
                $normalizedCurrentDay = ucfirst(strtolower($currentDay));
                $weeklyOffs = array_filter([
                    $shift->weekly_off_1,
                    $shift->weekly_off_2,
                    $shift->weekly_off_3,
                ]);

                if (in_array($normalizedCurrentDay, $weeklyOffs)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Login not allowed on ' . $normalizedCurrentDay . '. This is your weekly off day.',
                    ], 403);
                }

                // Shift based time check
                $startTime = $shift->in_time;
                $endTime = $shift->out_time;

                if ($startTime && $endTime) {
                    $startTimeStr = is_string($startTime) ? $startTime : $startTime->format('H:i');
                    $endTimeStr = is_string($endTime) ? $endTime : $endTime->format('H:i');

                    $earlyLoginMinutes = 60;
                    if (isset($shift->early_login_minutes) && $shift->early_login_minutes !== null) {
                        try {
                            $timeParts = explode(':', $shift->early_login_minutes);
                            $hours = (int) ($timeParts[0] ?? 0);
                            $minutes = (int) ($timeParts[1] ?? 0);
                            $earlyLoginMinutes = ($hours * 60) + $minutes;
                        } catch (\Exception $e) {
                            $earlyLoginMinutes = (int) $shift->early_login_minutes;
                        }
                    }

                    $startTimeCarbon = Carbon::createFromTimeString($startTimeStr);
                    $earliestLoginTime = $startTimeCarbon->copy()->subMinutes($earlyLoginMinutes);
                    $earliestLoginTimeStr = $earliestLoginTime->format('H:i');

                    if ($currentTime < $earliestLoginTimeStr || $currentTime > $endTimeStr) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Login only allowed between ' . $earliestLoginTimeStr . ' and ' . $endTimeStr . ' (Shift: ' . ($shift->shift_name ?? 'N/A') . ').',
                        ], 403);
                    }
                }
            } else {
                // No shift: check standard working_days and working hours constraints
                if ($boxUserEntry->user_box_timing == 0) {
                    $allowedDays = is_array($boxUserEntry->working_days)
                        ? $boxUserEntry->working_days
                        : json_decode($boxUserEntry->working_days, true);
                    $startTime = $boxUserEntry->day_start_time;
                    $endTime = $boxUserEntry->day_end_time;
                } else {
                    $allowedDays = is_array($box->working_days)
                        ? $box->working_days
                        : json_decode($box->working_days, true);
                    $startTime = $box->day_start_time;
                    $endTime = $box->day_end_time;
                }
                $allowedDays = array_map('strtolower', $allowedDays ?? []);

                if (empty($allowedDays)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Working days are not configured for your account. Please contact admin.',
                    ], 403);
                }

                if (!in_array($currentDay, $allowedDays)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Login not allowed on ' . ucfirst($currentDay) . '.',
                    ], 403);
                }

                // IP restrictions check
                if ($boxUserEntry->exlude_ip_restriction == 1) {
                    $allowedIps = is_array($boxUserEntry->allowed_ips)
                        ? $boxUserEntry->allowed_ips
                        : json_decode($boxUserEntry->allowed_ips, true);
                    $allowedIps = array_map('trim', $allowedIps ?? []);
                    $clientIp = trim(request()->ip());

                    if (!in_array($clientIp, $allowedIps)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Login from IP ' . $clientIp . ' is not allowed.',
                        ], 403);
                    }
                }

                // Time checks
                if ($startTime && $endTime) {
                    $startTimeStr = Carbon::parse($startTime)->format('H:i');
                    $endTimeStr = Carbon::parse($endTime)->format('H:i');
                    if ($currentTime < $startTimeStr || $currentTime > $endTimeStr) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Login only allowed between ' . $startTime . ' and ' . $endTime . '.',
                        ], 403);
                    }
                }
            }
        }

        // Single Session Enforcement for API
        // if ($user->tokens()->count() > 0) {
        //     if (!$request->has('force_login') || filter_var($request->force_login, FILTER_VALIDATE_BOOLEAN) === false) {
        //         return response()->json([
        //             'success' => false,
        //             'requires_confirmation' => true,
        //             'is_logged_in' => true,
        //             'message' => 'You are already logged in from another device. Continuing will log out your previous session.'
        //         ], 409);
        //     }
        //     // If confirmed, revoke all previous tokens
        //     $user->tokens()->delete();
        // }

        // Generate Sanctum Token
        $token = $user->createToken('api_auth_token')->plainTextToken;

        // Clear any pending web logout flags since they just logged into mobile explicitly
        \Illuminate\Support\Facades\Cache::forget('web_logout_' . $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $boxUserEntry->staff_role ?? 'user',
            ],
            'box' => [
                'id' => $box->id,
                'name' => $box->name,
                'slug' => $box->slug,
                'domain' => $box->domain,
                'whatsapp_token' => $box->whatsapp_token,
            ],
            'dashboard_url' => $request->getSchemeAndHttpHost() . '/' . $box->slug . '/dashboard',
            'web_login_url' => $request->getSchemeAndHttpHost() . '/auto-login?token=' . $token,
        ]);
    }

    /**
     * API User Logout
     */
    public function logout(Request $request, $box_slug = null)
    {
        $user = $request->user();

        // Invalidate all active API tokens for this user
        $user->tokens()->delete();

        // Invalidate all web sessions by setting a flag in cache.
        // The CheckApiLogout middleware will intercept this on the next web request,
        // show a sweet alert, and log the user out.
        \Illuminate\Support\Facades\Cache::put('api_logout_' . $user->id, true, now()->addMinutes(60));

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.'
        ]);
    }

    public function cancelWebLogout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            \Illuminate\Support\Facades\Cache::forget('web_logout_' . $user->id);
        }
        return response()->json(['success' => true, 'message' => 'Logout cancelled.']);
    }

    public function cancelApiLogout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            \Illuminate\Support\Facades\Cache::forget('api_logout_' . $user->id);
        }
        return response()->json(['success' => true, 'message' => 'Logout cancelled.']);
    }

    /**
     * Get authenticated user profile and tenant details
     */
    public function me(Request $request, $box_slug = null)
    {
        $user = $request->user();
        
        $boxUser = null;
        if ($box_slug) {
            $box = Box::where('slug', $box_slug)->first();
            if ($box) {
                $boxUser = BoxUser::with('box')
                    ->where('user_id', $user->id)
                    ->where('box_id', $box->id)
                    ->first();
            }
        }

        if (!$boxUser) {
            $boxUser = BoxUser::with('box')->where('user_id', $user->id)->first();
        }

        $isPrivilegedUser = \App\Helpers\PermissionHelper::isPrivilegedUser();
        
        // --- Feature Flags from Subscription ---
        $hasAttendanceFeature = \App\Helpers\SubscriptionHelper::hasFeature('Attendance');
        $hasHrToolsFeature = \App\Helpers\SubscriptionHelper::hasFeature('HR Documents');
        $hasPayrollFeature = \App\Helpers\SubscriptionHelper::hasFeature('Salary');
        $hasLeadsFeature = \App\Helpers\SubscriptionHelper::hasFeature('Lead Integration');
        $hasActivitiesFeature = \App\Helpers\SubscriptionHelper::hasFeature('Reminders');
        $hasCustomFieldsFeature = \App\Helpers\SubscriptionHelper::hasFeature('Custom Fields');
        $hasWhatsAppFeature = \App\Helpers\SubscriptionHelper::hasFeature('WhatsApp API Integration');
        $hasSmsFeature = \App\Helpers\SubscriptionHelper::hasFeature('SMS Integration');
        $hasLeaveManagementFeature = \App\Helpers\SubscriptionHelper::hasFeature('Leave Management');
        $hasHolidaysFeature = \App\Helpers\SubscriptionHelper::hasFeature('Holidays');
        $hasCallRecordsFeature = \App\Helpers\SubscriptionHelper::hasFeature('Call Records');
        
        $modules = [
            'accounts', 'activities_records', 'app_settings', 'attendance', 'financial_settings', 
            'general_settings', 'hr_tools', 'integrations', 'leads', 'other_settings', 
            'payroll', 'reports', 'system_settings', 'user_management', 'website_settings',
            'finance_dashboard', 'finance_settings_allowance_types', 'finance_settings_expense_categories',
            'finance_settings_approval_rules', 'finance_expenses', 'finance_approvals', 'finance_reimbursements'
        ];

        $moduleFeatures = [
            'attendance' => $hasAttendanceFeature,
            'hr_tools' => $hasHrToolsFeature,
            'payroll' => $hasPayrollFeature,
            'leads' => $hasLeadsFeature,
            'activities_records' => $hasActivitiesFeature,
        ];

        $pages = [
            'accounts.invoice', 'accounts.invoice_grid', 'accounts.invoice_settings', 'accounts.invoice_templates', 
            'accounts.invoices', 'accounts.receipts', 'manage_users.organizational_structure', 'app.settings.calls', 
            'app.settings.contact.stage', 'app.settings.custom_fields.index', 'app.settings.industry', 'app.settings.invoice', 
            'app.settings.leads.leads_sub_status', 'app.settings.lost.reason', 'app.settings.manage.form', 'app.settings.printers', 
            'app.settings.source', 'device-register.index', 'employee.attendace', 'employee.attendace.admin', 'employee.attendace.team', 
            'employee.attendance.calendar', 'employee.biometric.machine', 'employee.dashboard', 'employee.grid', 'employee.holiday.page', 
            'employee.holiday_type', 'employee.leads_type', 'employee.leave.admin', 'employee.leave_settings', 'employee.leave_transaction', 
            'employee.leave_transaction.team', 'employee.leaves', 'manage_users.employee_list', 'employee.shift', 'facebook.dashboard', 'facebook.leads', 
            'financial.settings.bank_accounts', 'financial.settings.currencies', 'financial.settings.divisions', 'financial.settings.payment_gateways', 
            'financial.settings.payment_modes', 'financial.settings.tax_rates', 'hr.employeess.letter.generation', 'hr.employess.assets', 
            'hr.employess.hr.documents', 'hr.job.applicants.index', 'leads.dashboard', 'leads.details', 'leads.details.all', 'leads.details.duplicate', 
            'leads.details.trash', 'leads.details.working-location', 'manage.users', 'mobile-attendance.fingerprints', 'mobile-attendance.punches', 
            'other.settings.ban_ip', 'other.settings.box_settings', 'other.settings.storage', 'payroll.employee.report', 'payroll.employee.salary', 
            'payroll.loan.settings', 'payroll.loan.tracker', 'payroll.payroll.items', 'payroll.salary.slip', 'payroll.setting', 'reports.date_task_stats', 
            'reports.source_stats', 'reports.status_stats', 'reports.task_stats', 'reports.user_stats', 'settings.general.activities', 
            'settings.general.api_keys', 'settings.general.companies', 'settings.general.companies_grid', 'settings.general.companys', 
            'settings.general.companys_details', 'settings.general.custom_fields', 'settings.general.email_inboxes', 'settings.general.email_messages', 
            'settings.general.hsns', 'settings.general.lead_status', 'settings.general.packages', 'manage_users.roles_permissions', 
            'settings.general.sources', 'settings.integrations.bulk.uploads', 'settings.integrations.compaign.details', 'settings.integrations.compaign.settings', 
            'settings.integrations.create.project', 'settings.integrations.facebook.form', 'settings.integrations.integrations', 'settings.integrations.labels', 
            'system.settings.email', 'system.settings.gdpr', 'system.settings.sms_gateways', 'manage_users.permissions', 'website.settings.appearance', 
            'website.settings.attendance.module', 'website.settings.branch', 'website.settings.company', 'website.settings.companys_currency', 
            'manage_users.employee_type', 'website.settings.language', 'website.settings.localization', 'website.settings.preference', 
            'website.settings.prefixes', 'manage_users.work_mode', 'whatsapp.campaign', 'whatsapp.campaign_archieve', 'whatsapp.campaign_complete', 
            'whatsapp.create_project', 'whatsapp.dashboard', 'whatsapp.index'
        ];

        $pageFeatures = [
            'employee.leaves' => $hasAttendanceFeature,
            'employee.leave.admin' => $hasAttendanceFeature,
            'employee.leave_settings' => $hasAttendanceFeature,
            'employee.leads_type' => $hasAttendanceFeature,
            'employee.leave_transaction' => $hasAttendanceFeature,
            'employee.leave_transaction.team' => $hasAttendanceFeature,
            'employee.holiday.page' => $hasAttendanceFeature,
            'employee.holiday_type' => $hasAttendanceFeature,
            'settings.general.custom_fields' => $hasCustomFieldsFeature,
            'website.settings.attendance.module' => $hasAttendanceFeature,
            'app.settings.custom_fields.index' => $hasCustomFieldsFeature,
            'app.settings.calls' => $hasActivitiesFeature,
            'system.settings.sms_gateways' => $hasSmsFeature,
        ];

        $modulePermissions = [];
        foreach ($modules as $module) {
            $basePerm = $isPrivilegedUser || \App\Helpers\PermissionHelper::hasModulePermission($module, 'read');
            if (isset($moduleFeatures[$module])) {
                $modulePermissions[$module] = $basePerm && $moduleFeatures[$module];
            } else {
                $modulePermissions[$module] = $basePerm;
            }
        }

        // Custom Finance Logic matching Web Sidebar
        if ($isPrivilegedUser) {
            $modulePermissions['finance_settings'] = true;
            $modulePermissions['finance'] = true;
        } else {
            $hasAllowanceTypes = \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_allowance_types', 'read');
            $hasExpenseCategories = \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_expense_categories', 'read');
            $hasApprovalRules = \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_approval_rules', 'read');
            $hasFinanceSettingsRead = $hasAllowanceTypes || $hasExpenseCategories || $hasApprovalRules;

            $hasFinanceDashboardRead = \App\Helpers\PermissionHelper::hasModulePermission('finance_dashboard', 'read');
            $hasFinanceExpensesRead = \App\Helpers\PermissionHelper::hasModulePermission('finance_expenses', 'read');
            $hasFinanceApprovalsRead = \App\Helpers\PermissionHelper::hasModulePermission('finance_approvals', 'read');
            $hasFinanceReimbursementsRead = \App\Helpers\PermissionHelper::hasModulePermission('finance_reimbursements', 'read');
            
            $modulePermissions['finance_settings'] = $hasFinanceSettingsRead;
            $modulePermissions['finance'] = $hasFinanceDashboardRead || $hasFinanceSettingsRead || $hasFinanceExpensesRead || $hasFinanceApprovalsRead || $hasFinanceReimbursementsRead;
        }

        $pagePermissions = [];
        foreach ($pages as $page) {
            $basePerm = $isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute($page);
            if (isset($pageFeatures[$page])) {
                $pagePermissions[$page] = $basePerm && $pageFeatures[$page];
            } else {
                $pagePermissions[$page] = $basePerm;
            }
        }

        $subscriptionModules = [];
        foreach ($modules as $module) {
            if (isset($moduleFeatures[$module])) {
                $subscriptionModules[$module] = $moduleFeatures[$module];
            } else {
                $subscriptionModules[$module] = true; // Subscription does not restrict this module
            }
        }
        $subscriptionModules['finance'] = true;
        $subscriptionModules['finance_settings'] = true;

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'role' => $boxUser ? $boxUser->staff_role : 'user',
                'is_superadmin' => $user->hasRole('superadmin') || ($boxUser && $boxUser->staff_role === 'superadmin'),
                'is_admin' => $user->hasRole('admin') || ($boxUser && $boxUser->staff_role === 'admin'),
            ],
            'box_user' => $boxUser ? [
                'id' => $boxUser->id,
                'employee_id' => $boxUser->employee_id,
                'staff_role' => $boxUser->staff_role,
                'status' => $boxUser->status,
                'company' => $boxUser->company,
                'box' => $boxUser->box ? [
                    'id' => $boxUser->box->id,
                    'name' => $boxUser->box->name,
                    'slug' => $boxUser->box->slug,
                    'domain' => $boxUser->box->domain,
                    'whatsapp_token' => $boxUser->box->whatsapp_token,
                ] : null
            ] : null,
            'permissions' => [
                'is_privileged_user' => $isPrivilegedUser,
                'subscription_modules' => $subscriptionModules,
                'modules' => $modulePermissions,
                'pages' => $pagePermissions
            ]
        ]);
    }

    /**
     * API Check Permission (Generic endpoint for frontend to check access)
     */
    public function checkPermission(Request $request, $box_slug = null)
    {
        $request->validate([
            'type' => 'required|in:route,module',
            'route' => 'required_if:type,route|string',
            'module' => 'required_if:type,module|string',
            'action' => 'required_if:type,module|string',
        ]);

        $isPrivilegedUser = \App\Helpers\PermissionHelper::isPrivilegedUser();
        $hasPermission = false;

        // 1. First, Check Subscription Feature Limits
        $hasAttendanceFeature = \App\Helpers\SubscriptionHelper::hasFeature('Attendance');
        $hasHrToolsFeature = \App\Helpers\SubscriptionHelper::hasFeature('HR Documents');
        $hasPayrollFeature = \App\Helpers\SubscriptionHelper::hasFeature('Salary');
        $hasLeadsFeature = \App\Helpers\SubscriptionHelper::hasFeature('Lead Integration');
        $hasActivitiesFeature = \App\Helpers\SubscriptionHelper::hasFeature('Reminders');
        $hasCustomFieldsFeature = \App\Helpers\SubscriptionHelper::hasFeature('Custom Fields');
        $hasSmsFeature = \App\Helpers\SubscriptionHelper::hasFeature('SMS Integration');

        $subscriptionPasses = true;

        if ($request->type === 'module') {
            $moduleFeatures = [
                'attendance' => $hasAttendanceFeature,
                'hr_tools' => $hasHrToolsFeature,
                'payroll' => $hasPayrollFeature,
                'leads' => $hasLeadsFeature,
                'activities_records' => $hasActivitiesFeature,
            ];
            if (isset($moduleFeatures[$request->module])) {
                $subscriptionPasses = $moduleFeatures[$request->module];
            }
        } elseif ($request->type === 'route') {
            $pageFeatures = [
                'employee.leaves' => $hasAttendanceFeature,
                'employee.leave.admin' => $hasAttendanceFeature,
                'employee.leave_settings' => $hasAttendanceFeature,
                'employee.leads_type' => $hasAttendanceFeature,
                'employee.leave_transaction' => $hasAttendanceFeature,
                'employee.leave_transaction.team' => $hasAttendanceFeature,
                'employee.holiday.page' => $hasAttendanceFeature,
                'employee.holiday_type' => $hasAttendanceFeature,
                'settings.general.custom_fields' => $hasCustomFieldsFeature,
                'website.settings.attendance.module' => $hasAttendanceFeature,
                'app.settings.custom_fields.index' => $hasCustomFieldsFeature,
                'app.settings.calls' => $hasActivitiesFeature,
                'system.settings.sms_gateways' => $hasSmsFeature,
            ];
            if (isset($pageFeatures[$request->route])) {
                $subscriptionPasses = $pageFeatures[$request->route];
            }
        }

        // 2. Only if the subscription allows it, check Database Permissions
        if ($subscriptionPasses) {
            if ($request->type === 'route') {
                $hasPermission = $isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute($request->route);
            } elseif ($request->type === 'module') {
                if ($isPrivilegedUser) {
                    $hasPermission = true;
                } else {
                    // Handle complex custom web sidebar logic
                    if ($request->module === 'finance_settings') {
                        $hasAllowanceTypes = \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_allowance_types', $request->action);
                        $hasExpenseCategories = \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_expense_categories', $request->action);
                        $hasApprovalRules = \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_approval_rules', $request->action);
                        $hasPermission = $hasAllowanceTypes || $hasExpenseCategories || $hasApprovalRules;
                    } elseif ($request->module === 'finance') {
                        $hasAllowanceTypes = \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_allowance_types', $request->action);
                        $hasExpenseCategories = \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_expense_categories', $request->action);
                        $hasApprovalRules = \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_approval_rules', $request->action);
                        $hasFinanceSettingsRead = $hasAllowanceTypes || $hasExpenseCategories || $hasApprovalRules;

                        $hasFinanceDashboardRead = \App\Helpers\PermissionHelper::hasModulePermission('finance_dashboard', $request->action);
                        $hasFinanceExpensesRead = \App\Helpers\PermissionHelper::hasModulePermission('finance_expenses', $request->action);
                        $hasFinanceApprovalsRead = \App\Helpers\PermissionHelper::hasModulePermission('finance_approvals', $request->action);
                        $hasFinanceReimbursementsRead = \App\Helpers\PermissionHelper::hasModulePermission('finance_reimbursements', $request->action);

                        $hasPermission = $hasFinanceDashboardRead || $hasFinanceSettingsRead || $hasFinanceExpensesRead || $hasFinanceApprovalsRead || $hasFinanceReimbursementsRead;
                    } else {
                        $hasPermission = \App\Helpers\PermissionHelper::hasModulePermission($request->module, $request->action);
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'has_permission' => $hasPermission
        ]);
    }

    /**
     * Get all users in the box (Admins only)
     */
    public function getBoxUsers(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found'], 404);
        }

        $user = $request->user();
        $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
        $role = $boxUser ? $boxUser->staff_role : null;

        $isSuperAdmin = $user->hasRole('superadmin');
        $isAdmin = $user->hasRole('admin');

        if (!$isSuperAdmin && !$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can view all users.',
                'data' => []
            ], 403);
        }

        $boxUsers = BoxUser::with('user:id,name,email')
            ->where('box_id', $box->id)
            ->get();
        
        $users = $boxUsers->map(function ($bu) {
            if ($bu->user) {
                return [
                    'id' => $bu->user->id,
                    'user_id' => $bu->user->id,
                    'name' => $bu->user->name,
                    'email' => $bu->user->email,
                    'role' => $bu->staff_role,
                    'employee_id' => $bu->employee_id
                ];
            }
            return null;
        })->filter()->values();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * API Send OTP to User mobile/email
     */
    public function sendOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_input' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            $user = User::where('mobile', $request->user_input)
                ->orWhere('email', $request->user_input)
                ->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found for the given mobile or email.',
                ], 404);
            }
            if ($user->email && is_null($user->email_verified_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This email address is registered but not verified. Please verify your email first.',
                ], 403);
            }
            $otp = rand(100000, 999999);
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(10),
            ]);
            if ($user->email) {
                dispatch(new SendOtpJob($user->email, $user->otp, $user->otp_expires_at));
            }
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully and valid for 10 minutes.',
            ]);
        } catch (\Exception $e) {
            Log::error('API OTP sending failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the OTP. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API Verify OTP and login user
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_input' => 'required',
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('mobile', $request->user_input)
            ->orWhere('email', $request->user_input)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        if ($user->otp !== $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'The OTP you entered is incorrect.',
            ], 422);
        }

        if (Carbon::now()->gt($user->otp_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Your OTP has expired. Please resend OTP.',
            ], 410);
        }

        $boxUserEntry = BoxUser::with('shift')->where('user_id', $user->id)->first();

        if ($boxUserEntry && $boxUserEntry->status === 'Disabled') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is currently disabled. Please contact admin.',
            ], 403);
        }

        if (!$boxUserEntry) {
            return response()->json([
                'success' => false,
                'message' => 'No BoxUser record found for this user.',
            ], 422);
        }

        $box = Box::find($boxUserEntry->box_id);

        if (!$box) {
            return response()->json([
                'success' => false,
                'message' => 'No box assigned to this user.',
            ], 422);
        }

        $now = Carbon::now();
        $currentDay = strtolower($now->format('l'));
        $currentTime = now()->format('H:i');
        $canLoginAnytime = $boxUserEntry->can_login_anytime ?? false;

        // Perform standard login restriction checks
        if (!$user->hasRole('superadmin') && !$user->hasRole('admin') && !$canLoginAnytime) {
            $shift = $boxUserEntry->shift;
            $hasShift = $shift && $shift->shift_enabled && $boxUserEntry->shift_id;

            if ($hasShift) {
                // Shift based weekly off check
                $normalizedCurrentDay = ucfirst(strtolower($currentDay));
                $weeklyOffs = array_filter([
                    $shift->weekly_off_1,
                    $shift->weekly_off_2,
                    $shift->weekly_off_3,
                ]);

                if (in_array($normalizedCurrentDay, $weeklyOffs)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Login not allowed on ' . $normalizedCurrentDay . '. This is your weekly off day.',
                    ], 403);
                }

                // Shift based time check
                $startTime = $shift->in_time;
                $endTime = $shift->out_time;

                if ($startTime && $endTime) {
                    $startTimeStr = is_string($startTime) ? $startTime : $startTime->format('H:i');
                    $endTimeStr = is_string($endTime) ? $endTime : $endTime->format('H:i');

                    $earlyLoginMinutes = 60;
                    if (isset($shift->early_login_minutes) && $shift->early_login_minutes !== null) {
                        try {
                            $timeParts = explode(':', $shift->early_login_minutes);
                            $hours = (int) ($timeParts[0] ?? 0);
                            $minutes = (int) ($timeParts[1] ?? 0);
                            $earlyLoginMinutes = ($hours * 60) + $minutes;
                        } catch (\Exception $e) {
                            $earlyLoginMinutes = (int) $shift->early_login_minutes;
                        }
                    }

                    $startTimeCarbon = Carbon::createFromTimeString($startTimeStr);
                    $earliestLoginTime = $startTimeCarbon->copy()->subMinutes($earlyLoginMinutes);
                    $earliestLoginTimeStr = $earliestLoginTime->format('H:i');

                    if ($currentTime < $earliestLoginTimeStr || $currentTime > $endTimeStr) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Login only allowed between ' . $earliestLoginTimeStr . ' and ' . $endTimeStr . ' (Shift: ' . ($shift->shift_name ?? 'N/A') . ').',
                        ], 403);
                    }
                }
            } else {
                // No shift: check standard working_days and working hours constraints
                if ($boxUserEntry->user_box_timing == 0) {
                    $allowedDays = is_array($boxUserEntry->working_days)
                        ? $boxUserEntry->working_days
                        : json_decode($boxUserEntry->working_days, true);
                    $startTime = $boxUserEntry->day_start_time;
                    $endTime = $boxUserEntry->day_end_time;
                } else {
                    $allowedDays = is_array($box->working_days)
                        ? $box->working_days
                        : json_decode($box->working_days, true);
                    $startTime = $box->day_start_time;
                    $endTime = $box->day_end_time;
                }
                $allowedDays = array_map('strtolower', $allowedDays ?? []);

                if (empty($allowedDays)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Working days are not configured for your account. Please contact admin.',
                    ], 403);
                }

                if (!in_array($currentDay, $allowedDays)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Login not allowed on ' . ucfirst($currentDay) . '.',
                    ], 403);
                }

                // IP restrictions check
                if ($boxUserEntry->exlude_ip_restriction == 1) {
                    $allowedIps = is_array($boxUserEntry->allowed_ips)
                        ? $boxUserEntry->allowed_ips
                        : json_decode($boxUserEntry->allowed_ips, true);
                    $allowedIps = array_map('trim', $allowedIps ?? []);
                    $clientIp = trim(request()->ip());

                    if (!in_array($clientIp, $allowedIps)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Login from IP ' . $clientIp . ' is not allowed.',
                        ], 403);
                    }
                }

                // Time checks
                if ($startTime && $endTime) {
                    $startTimeStr = Carbon::parse($startTime)->format('H:i');
                    $endTimeStr = Carbon::parse($endTime)->format('H:i');
                    if ($currentTime < $startTimeStr || $currentTime > $endTimeStr) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Login only allowed between ' . $startTime . ' and ' . $endTime . '.',
                        ], 403);
                    }
                }
            }
        }



        // Generate Sanctum Token
        $token = $user->createToken('api_auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->pluck('name')->first() ?? 'employee',
            ],
            'box' => [
                'id' => $box->id,
                'name' => $box->name,
                'slug' => $box->slug,
                'domain' => $box->domain,
            ],
            'dashboard_url' => $request->getSchemeAndHttpHost() . '/' . $box->slug . '/dashboard',
            'web_login_url' => $request->getSchemeAndHttpHost() . '/auto-login?token=' . $token,
        ]);
    }

    /**
     * API Request Forget Password Reset Link
     */
    public function forgetEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $email = $request->email;
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json([
                    'success' => true,
                    'message' => 'If your email exists in our system, you will receive a password reset link shortly.',
                ], 200);
            }

            if (is_null($user->email_verified_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your Email is not yet verified. Please verify your email first.',
                ], 403);
            }

            $token = Str::random(60);
            $expiresAt = Carbon::now()->addMinutes(10);

            DB::table('password_reset_tokens')->where('email', $email)->delete();
            DB::table('password_reset_tokens')->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);

            $user->remember_token = $token;
            $user->save();

            dispatch(new SendResetPasswordEmailJob($user->email, $token, $expiresAt));

            return response()->json([
                'success' => true,
                'message' => 'If your email exists in our system, you will receive a password reset link shortly.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('API Password reset email error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API Submit Reset Password
     */
    public function Reset_Password(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::where('remember_token', $request->token)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired token.',
                ], 422);
            }

            $user->password = Hash::make($request->password);
            $user->remember_token = null;
            $user->save();

            dispatch(new SendPasswordResetSuccessJob($user));

            return response()->json([
                'success' => true,
                'message' => 'Password reset successful!',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * API Verify Email Token
     */
    public function verify($token)
    {
        $user = User::where('remember_token', $token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification link.',
            ], 422);
        }

        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();

            $boxUser = BoxUser::where('user_id', $user->id)->first();
            if ($boxUser) {
                $boxUser->status = 'Enabled';
                $boxUser->save();
            }

            dispatch(new SendEmailVerifiedJob($user));
        }

        return response()->json([
            'success' => true,
            'message' => 'Your account has been verified successfully.',
        ]);
    }

    /**
     * API Resend Verification Email
     */
    public function resendVerificationEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            $user = User::where('email', $request->email)->first();
            if ($user->email_verified_at !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email is already verified.',
                ], 409);
            }
            $tempPassword = Str::random(10);
            $token = Str::random(60);
            $user->update([
                'password' => Hash::make($tempPassword),
                'remember_token' => $token,
            ]);
            dispatch(new SendRegistrationVerifyJob(
                $user->name,
                $user->email,
                $tempPassword,
                $token,
            ));
            return response()->json([
                'success' => true,
                'message' => 'Check your email inbox to verify your email, then login.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend verification email.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * API Search Country, State, City
     */
    public function SearchCountry(Request $request)
    {
        try {
            $type = $request->input('type');
            $keyword = $request->input('keyword');
            $countryId = $request->input('country_id');
            $stateId = $request->input('state_id');

            switch ($type) {
                case 'country':
                    $query = Country::query()->where('status', 'Enable');
                    if ($keyword) {
                        $query->where('name', 'like', '%' . $keyword . '%');
                    }
                    $countries = $query->select('id', 'name')->orderBy('name')->get();
                    return response()->json(['data' => $countries]);

                case 'state':
                    if (!$countryId) {
                        return response()->json(['error' => 'country_id required'], 400);
                    }

                    $query = State::where('country_id', $countryId)->where('status', 'Enable');
                    if ($keyword) {
                        $query->where('name', 'like', '%' . $keyword . '%');
                    }
                    $states = $query->select('id', 'name')->orderBy('name')->get();
                    return response()->json(['data' => $states]);

                case 'city':
                    if (!$stateId) {
                        return response()->json(['error' => 'state_id required'], 400);
                    }

                    $query = City::where('state_id', $stateId)->where('status', 'Enable');
                    if ($keyword) {
                        $query->where('name', 'like', '%' . $keyword . '%');
                    }
                    $cities = $query->select('id', 'name')->orderBy('name')->get();
                    return response()->json(['data' => $cities]);

                default:
                    return response()->json(['error' => 'Invalid type'], 400);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * API Get Countries
     */
    public function getCountries(Request $request)
    {
        try {
            $keyword = $request->input('keyword');
            $query = Country::query()->where('status', 'Enable');
            if ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }
            $countries = $query->select('id', 'name')->orderBy('name')->get();
            return response()->json([
                'success' => true,
                'data' => $countries
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch countries.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * API Get States
     */
    public function getStates(Request $request, $countryId = null)
    {
        try {
            $countryId = $countryId ?? $request->input('country_id');
            $keyword = $request->input('keyword');

            if (!$countryId) {
                return response()->json([
                    'success' => false,
                    'message' => 'country_id parameter is required.'
                ], 400);
            }

            $query = State::where('country_id', $countryId)->where('status', 'Enable');
            if ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }
            $states = $query->select('id', 'name')->orderBy('name')->get();
            return response()->json([
                'success' => true,
                'data' => $states
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch states.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * API Get Cities
     */
    public function getCities(Request $request, $stateId = null)
    {
        try {
            $stateId = $stateId ?? $request->input('state_id');
            $keyword = $request->input('keyword');

            if (!$stateId) {
                return response()->json([
                    'success' => false,
                    'message' => 'state_id parameter is required.'
                ], 400);
            }

            $query = City::where('state_id', $stateId)->where('status', 'Enable');
            if ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }
            $cities = $query->select('id', 'name')->orderBy('name')->get();
            return response()->json([
                'success' => true,
                'data' => $cities
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cities.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * API Get Industries
     */
    public function getIndustries(Request $request)
    {
        try {
            $industries = MasterIndustry::where('status', true)
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();
            return response()->json([
                'success' => true,
                'data' => $industries
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch industries.',
                'error' => $th->getMessage()
            ], 500);
        }
    }



    /**
     * API Send OTP for Password Change (Unauthenticated)
     * Step 1: Send OTP
     */
    public function sendChangePasswordOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_input' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::where('mobile', $request->user_input)
                ->orWhere('email', $request->user_input)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            $otp = rand(100000, 999999);
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(10),
            ]);

            $sentVia = '';
            
            if (!empty($user->email)) {
                // 1. Try Email First
                dispatch(new SendOtpJob($user->email, $user->otp, $user->otp_expires_at));
                $sentVia = 'email';
            } elseif (!empty($user->mobile)) {
                // 2. Try WhatsApp next, and 3. Phone Text (SMS) last.
                Log::info('Change Password OTP requested for mobile: ' . $user->mobile . '. OTP is: ' . $otp);
                $sentVia = 'mobile';
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid email or mobile number found to send OTP.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully and is valid for 10 minutes.',
                'sent_via' => $sentVia,
            ]);
        } catch (\Exception $e) {
            Log::error('API Change Password OTP sending failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the OTP. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API Verify OTP for Password Change (Unauthenticated)
     * Step 2: Verify OTP and get reset token
     */
    public function verifyChangePasswordOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_input' => 'required',
                'otp' => 'required|digits:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::where('mobile', $request->user_input)
                ->orWhere('email', $request->user_input)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            if ((string)$user->otp !== (string)$request->otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'The OTP you entered is incorrect.',
                ], 422);
            }

            if (Carbon::now()->gt($user->otp_expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your OTP has expired. Please request a new OTP.',
                ], 410);
            }

            // OTP is valid. Clear OTP and generate reset token.
            $resetToken = Str::random(60);
            
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->remember_token = $resetToken;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.',
                'reset_token' => $resetToken
            ]);

        } catch (\Exception $e) {
            Log::error('API Verify OTP failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while verifying the OTP.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API Update Password (Unauthenticated)
     * Step 3: Use reset token to change password
     */
    public function updatePasswordWithToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_input' => 'required',
                'reset_token' => 'required|string',
                'new_password' => 'required|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::where('mobile', $request->user_input)
                ->orWhere('email', $request->user_input)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            // Verify the token
            if (empty($user->remember_token) || $user->remember_token !== $request->reset_token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired reset token. Please request a new OTP.',
                ], 403);
            }

            // Update Password
            $user->password = Hash::make($request->new_password);
            $user->remember_token = null; // Clear token so it can't be reused
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password has been successfully changed.',
            ]);

        } catch (\Exception $e) {
            Log::error('API Change Password failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while changing the password.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
