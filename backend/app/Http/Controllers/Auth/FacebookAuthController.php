<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\DepartmentCreateJob;
use App\Models\{Box, BoxUser, EmployeeType, LeadSubStatus, MasterIndustry, User};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Http, Log};
use Illuminate\Support\Str;

class FacebookAuthController extends Controller
{
    public function redirect(Request $request)
    {
        $clientId = config('services.facebook.client_id');
        $redirectUri = config('services.facebook.redirect_login');
        $action = $request->input('action', 'login');

        // Capture the dynamic base URL the user clicked login from
        $state = base64_encode(json_encode([
            'current_url' => url('/'),
            'action' => $action
        ]));

        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'state' => $state,
            'config_id' => config('services.facebook.config_id')
        ];

        return redirect("https://www.facebook.com/v19.0/dialog/oauth?" . http_build_query($params));
    }

    public function callback(Request $request)
    {
        $code = $request->input('code');
        $error = $request->input('error');
        $stateData = json_decode(base64_decode($request->input('state')), true);
        $redirectBack = $stateData['current_url'] ?? url('/');
        $action = $stateData['action'] ?? 'login';

        if ($error || !$code) {
            return redirect($redirectBack . '/login')->with('error', 'Facebook authorization failed.');
        }

        $clientId = config('services.facebook.client_id');
        $clientSecret = config('services.facebook.client_secret');
        $redirectUri = config('services.facebook.redirect_login');

        // Exchange code for tokens
        $tokenUrl = "https://graph.facebook.com/v19.0/oauth/access_token";
        $response = Http::get($tokenUrl, [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'code' => $code,
        ]);

        if ($response->failed()) {
            return redirect($redirectBack . '/login')->with('error', 'Failed to get tokens from Facebook.');
        }

        $tokens = $response->json();
        
        $userResponse = Http::get('https://graph.facebook.com/v19.0/me', [
            'fields' => 'id,name,email',
            'access_token' => $tokens['access_token']
        ]);
        
        if ($userResponse->failed()) {
            return redirect($redirectBack . '/login')->with('error', 'Failed to get user info from Facebook.');
        }

        $userInfo = $userResponse->json();
        $email = $userInfo['email'] ?? null;
        
        if (!$email) {
            return redirect($redirectBack . '/login')->with('error', 'Please ensure your Facebook account has a verified email address.');
        }
        
        $name = $userInfo['name'] ?? explode('@', $email)[0];

        $user = User::where('email', $email)->first();

        if ($user) {
            if ($action === 'register') {
                return redirect($redirectBack . '/register')->with('error', 'This email is already registered. Please login instead.');
            }

            // Login existing user
            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
                $user->save();
            }
            Auth::login($user);
            return $this->redirectUser($user, $redirectBack);
        }

        // Register new user (Option 1)
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(24)),
                'email_verified_at' => now(), // Auto-verify since Facebook verified it
            ]);

            // Generate Slug and Company Name
            $emailPrefix = explode('@', $email)[0];
            $baseSlug = Str::slug($emailPrefix);
            $slug = $baseSlug;
            $counter = 1;

            while (Box::where('slug', $slug)->orWhere('domain', $slug . '.mycrm.com')->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $companyName = ucfirst($emailPrefix) . ' Inc';

            // Get a default industry (e.g., 'Other' or the first one)
            $masterIndustry = MasterIndustry::where('status', true)->first();
            $industryName = $masterIndustry ? $masterIndustry->name : 'Other';
            $industryId = $masterIndustry ? $masterIndustry->id : 0;

            $workingDays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            
            $box = Box::create([
                'superadmin_id' => $user->id,
                'name' => $companyName,
                'slug' => $slug,
                'domain' => $slug . '.mycrm.com',
                'industry' => $industryName,
                'master_industry_id' => $industryId,
                'address' => 'Not Provided',
                'pincode' => '000000',
                'phone' => '0000000000',
                'mobile' => '0000000000',
                'contact_name' => $name,
                'country_id' => '101', // Default India
                'state_id' => null,
                'working_days' => $workingDays,
                'day_start_time' => '10:00',
                'day_end_time' => '18:00',
                'city_id' => null,
                'start_date' => Carbon::now(),
            ]);

            dispatch(new DepartmentCreateJob($box->id));

            $companyPrefix = strtoupper(Str::substr($slug, 0, 2));
            $employeeId = $companyPrefix . '-01';

            $boxUser = BoxUser::create([
                'box_id' => $box->id,
                'user_id' => $user->id,
                'added_by' => $user->id,
                'first_name' => $name,
                'username' => $email,
                'phone' => '0000000000',
                'company' => $companyName,
                'address' => 'Not Provided',
                'employee_id' => $employeeId,
                'staff_role' => 'admin',
                'status' => 'Enabled', // Auto-enable
                'joining_date' => Carbon::now()->format('Y-m-d'),
            ]);

            // Default Leave Types
            $leaveTypes = [
                ['leave_type' => 'Casual Leave', 'leave_icon' => 'ti ti-hexagon-letter-c fs-32', 'leave_color' => 'bg-purple-le', 'status' => 'Active'],
                ['leave_type' => 'Annual Leave', 'leave_icon' => 'ti ti-calendar-event fs-32', 'leave_color' => 'bg-black-le', 'status' => 'Active'],
                ['leave_type' => 'Medical Leave', 'leave_icon' => 'ti ti-vaccine fs-32', 'leave_color' => 'bg-blue-le', 'status' => 'Active'],
                ['leave_type' => 'Compensatory Off Leave', 'leave_icon' => 'ti ti-hexagonal-prism-plus fs-32', 'leave_color' => 'bg-pink-le', 'status' => 'Active'],
            ];
            foreach ($leaveTypes as &$type) {
                $type['box_id'] = $box->id;
                $type['created_at'] = now();
                $type['updated_at'] = now();
            }
            DB::table('leave_types')->insert($leaveTypes);

            // Activity Settings
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

            $initialData = [
                "industry" => [
                    ["name" => "Retail Industry", "status" => "1"],
                    ["name" => "Technology", "status" => "1"],
                    ["name" => "Other", "status" => "1"]
                ],
                "contact_stage" => [
                    ["name" => "Contacted", "status" => "1", "sub_statuses" => ["In Progress", "Follow Up Required"]],
                    ["name" => "Closed", "status" => "1", "sub_statuses" => ["Won", "Deal Signed"]],
                    ["name" => "Lost", "status" => "1", "sub_statuses" => ["Price Too High"]],
                ],
                "lost_reason" => [
                    ["name" => "Client went silent", "status" => "1"],
                    ["name" => "Don't have the budget", "status" => "1"],
                ],
                "source" => [
                    ["name" => "Phone Calls", "status" => "1"],
                    ["name" => "Web Analytics", "status" => "1"],
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

            $employeeTypes = ['Full-Time', 'Part-Time', 'Contract', 'Intern'];
            foreach ($employeeTypes as $type) {
                EmployeeType::create([
                    'box_id' => $box->id,
                    'name' => $type,
                    'status' => 1
                ]);
            }

            // Seed Working Modes
            $workingModes = ['On-site', 'Remote', 'Hybrid'];
            foreach ($workingModes as $mode) {
                \App\Models\WorkModeSetting::create([
                    'box_id' => $box->id,
                    'name' => $mode,
                    'status' => '1',
                ]);
            }

            // Sync lead fields
            $syncJob = new \App\Jobs\SyncLeadFieldsJob($box->id);
            $syncJob->handle();

            // Assign admin role to the new user
            $user->assignRole('admin');

            DB::commit();

            Auth::login($user);
            return $this->redirectUser($user, $redirectBack);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Facebook Auth Registration Failed: ' . $e->getMessage());
            return redirect($redirectBack . '/register')->with('error', 'Registration failed. Please try again.');
        }
    }

    private function redirectUser($user, $redirectBack = null)
    {
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        $box = $boxUserEntry ? Box::find($boxUserEntry->box_id) : null;
        $boxSlug = $box->slug ?? null;

        // If we have a specific base URL we came from, use it instead of generating a generic route
        if ($redirectBack && $redirectBack !== url('/')) {
            $baseRedirect = rtrim($redirectBack, '/');
            $pathToAppend = match (true) {
                $user->hasRole('superadmin') => '/superadmin/dashboard',
                $user->hasRole('admin'),
                $user->hasRole('manager') => '/admin/dashboard',
                $user->hasRole('employee') => '/employee/dashboard',
                $user->hasRole('deals') => '/deals/dashboard',
                $user->hasRole('project'),
                $user->hasRole('project-manager') => '/project/dashboard',
                $user->hasRole('tracking') => '/tracking/dashboard',
                $user->hasRole('leads') => '/leads/dashboard',
                default => '/home',
            };
            return redirect($baseRedirect . $pathToAppend)->with('success', 'Logged in successfully!');
        }

        $redirectTo = match (true) {
            $user->hasRole('superadmin') => route('superadmin.dashboard', ['box_slug' => $boxSlug]),
            $user->hasRole('admin'),
            $user->hasRole('manager') => route('admin.dashboard', ['box_slug' => $boxSlug]),
            $user->hasRole('employee') => route('employee.dashboard', ['box_slug' => $boxSlug]),
            $user->hasRole('deals') => route('deals.dashboard', ['box_slug' => $boxSlug]),
            $user->hasRole('project'),
            $user->hasRole('project-manager') => route('project.dashboard', ['box_slug' => $boxSlug]),
            $user->hasRole('tracking') => route('tracking.dashboard', ['box_slug' => $boxSlug]),
            $user->hasRole('leads') => route('leads.dashboard', ['box_slug' => $boxSlug]),
            default => route('home', ['box_slug' => $boxSlug]),
        };

        return redirect($redirectTo)->with('success', 'Logged in successfully!');
    }
}
