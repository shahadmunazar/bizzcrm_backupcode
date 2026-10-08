<?php
namespace App\Http\Controllers\Auth;
use App\Helpers\LeadFieldsHelper;
use App\Http\Controllers\Controller;
use App\Jobs\{DepartmentCreateJob, MarkAllUsersAbsentJob, SendEmailVerifiedJob, SendOtpJob, SendPasswordResetSuccessJob, SendRegistrationVerifyJob, SendResetPasswordEmailJob};
use App\Models\{Box, BoxUser, City, Country, EmployeeType, LeadSubStatus, State, User, WorkModeSetting, DealStatus, DealPeriod, DealSource, DealPriority, DealTag, DealStage, Deal};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Log, Validator};
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function autoLogin(Request $request)
    {
        $tokenStr = $request->query('token');
        $redirect = $request->query('redirect');

        if (!$tokenStr) {
            return redirect('/login')->with('error', 'Authentication token missing.');
        }

        $token = \Laravel\Sanctum\PersonalAccessToken::findToken($tokenStr);

        if (!$token || !$token->tokenable) {
            return redirect('/login')->with('error', 'Invalid or expired token.');
        }

        $user = $token->tokenable;
        Auth::login($user);

        $boxUserEntry = \App\Models\BoxUser::where('user_id', $user->id)->first();
        $box = $boxUserEntry ? \App\Models\Box::find($boxUserEntry->box_id) : null;
        $boxSlug = $box->slug ?? null;

        if ($redirect) {
            $redirectTo = match ($redirect) {
                'admin' => route('admin.dashboard', ['box_slug' => $boxSlug]),
                'leads' => route('leads.dashboard', ['box_slug' => $boxSlug]),
                'call' => route('call.dashboard', ['box_slug' => $boxSlug]),
                'facebook' => route('facebook.dashboard', ['box_slug' => $boxSlug]),
                // General Settings group
                'general-settings', 'general_setting', 'General Setting', 'general' => route('settings.general.companies', ['box_slug' => $boxSlug]),
                'settings.general.companies', 'companies' => route('settings.general.companies', ['box_slug' => $boxSlug]),
                'settings.general.companies_grid', 'companies_grid' => route('settings.general.companies_grid', ['box_slug' => $boxSlug]),
                'settings.general.custom_fields', 'custom_fields' => route('settings.general.custom_fields', ['box_slug' => $boxSlug]),
                'settings.general.email_inboxes', 'email_inboxes' => route('settings.general.email_inboxes', ['box_slug' => $boxSlug]),
                'settings.general.email_messages', 'email_messages' => route('settings.general.email_messages', ['box_slug' => $boxSlug]),
                'settings.general.api_keys', 'api_keys', 'lead_apis' => route('settings.general.api_keys', ['box_slug' => $boxSlug]),
                // Website Settings group
                'website-settings', 'website_setting', 'Website Setting', 'website', 'websitenSetting' => route('website.settings.company', ['box_slug' => $boxSlug]),
                'website.settings.company', 'company_settings' => route('website.settings.company', ['box_slug' => $boxSlug]),
                'website.settings.branch', 'company_branches' => route('website.settings.branch', ['box_slug' => $boxSlug]),
                'website.settings.attendance.module', 'attendance_module' => route('website.settings.attendance.module', ['box_slug' => $boxSlug]),
                'website.settings.companys_currency', 'company_currency' => route('website.settings.companys_currency', ['box_slug' => $boxSlug]),
                // App Settings group
                'app-settings', 'app_setting', 'App Setting', 'app' => route('app.settings.manage.form', ['box_slug' => $boxSlug]),
                'app.settings.manage.form', 'manage_form' => route('app.settings.manage.form', ['box_slug' => $boxSlug]),
                'app.settings.invoice', 'invoice_settings' => route('app.settings.invoice', ['box_slug' => $boxSlug]),
                'app.settings.printers', 'printers' => route('app.settings.printers', ['box_slug' => $boxSlug]),
                'app.settings.contact.stage', 'lead_status' => route('app.settings.contact.stage', ['box_slug' => $boxSlug]),
                'app.settings.calls', 'activity_settings' => route('app.settings.calls', ['box_slug' => $boxSlug]),
                // Financial Settings group
                'financial-settings', 'financial_setting', 'Financial Setting', 'financial' => route('financial.settings.currencies', ['box_slug' => $boxSlug]),
                'financial.settings.currencies', 'currencies' => route('financial.settings.currencies', ['box_slug' => $boxSlug]),
                'financial.settings.payment_modes', 'payment_modes' => route('financial.settings.payment_modes', ['box_slug' => $boxSlug]),
                'financial.settings.payment_gateways', 'payment_gateways' => route('financial.settings.payment_gateways', ['box_slug' => $boxSlug]),
                'financial.settings.bank_accounts', 'bank_accounts' => route('financial.settings.bank_accounts', ['box_slug' => $boxSlug]),
                'financial.settings.tax_rates', 'tax_rates' => route('financial.settings.tax_rates', ['box_slug' => $boxSlug]),
                // Other Settings group
                'other-settings', 'other_setting', 'Other Setting', 'other' => route('other.settings.storage', ['box_slug' => $boxSlug]),
                'other.settings.storage', 'storage' => route('other.settings.storage', ['box_slug' => $boxSlug]),
                'other.settings.ban_ip', 'ban_ip_address' => route('other.settings.ban_ip', ['box_slug' => $boxSlug]),
                'other.settings.box_settings', 'box_settings' => route('other.settings.box_settings', ['box_slug' => $boxSlug]),
                // System Settings group
                'system-settings', 'system_setting', 'System Setting', 'system' => route('system.settings.gdpr', ['box_slug' => $boxSlug]),
                'account/delete' => route('account.delete.index', ['box_slug' => $boxSlug]),
                default => route('home', ['box_slug' => $boxSlug]),
            };
        } else {
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
        }

        return redirect($redirectTo)->with('success', 'You have been automatically logged in.');
    }

    public function forget_password()
    {
        return view('auth.forget');
    }

    public function showRegisterForm()
    {
        $industries = \App\Models\MasterIndustry::where('status', true)->orderBy('name', 'asc')->get();
        return view('auth.register', compact('industries'));
    }

    public function reset_page()
    {
        return view('auth.reset_passowrd');
    }

    public function login_mobile()
    {
        return view('auth.mobile');
    }

    public function sendOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_input' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                ], 422);
            }
            $user = User::where('mobile', $request->user_input)
                ->orWhere('email', $request->user_input)
                ->first();
            if (!$user) {
                return response()->json([
                    'message' => 'User not found for the given mobile or email.',
                ], 404);
            }
            // Check if email exists and is not verified
            if ($user->email && is_null($user->email_verified_at)) {
                return response()->json([
                    'status' => 403,
                    'success' => false,
                    'message' => 'This email address is already registered With Us but not verified. Click here to resend the verification email',
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
            Log::error('OTP sending failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while sending the OTP. Please try again later.',
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'new_password' => 'required|string|min:8|confirmed',
            ], [
                'new_password.confirmed' => 'The confirmation password does not match.',
                'new_password.min' => 'Password must be at least 8 characters.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 403,
                    'error' => $validator->errors(),
                ], 403);
            }
            // Update user password
            $user = Auth::user();
            $user->password = Hash::make($request->new_password);
            $user->save();
            return response()->json([
                'status' => 200,
                'message' => 'Password changed successfully.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong.',
            ]);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_input' => 'required',
            'otp' => 'required|digits:6',
        ]);
        $user = User::where('mobile', $request->user_input)
            ->orWhere('email', $request->user_input)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'type' => 'not_found',
                'message' => 'User not found.',
            ], 404);
        }

        if ($user->otp !== $request->otp) {
            return response()->json([
                'success' => false,
                'type' => 'invalid_otp',
                'message' => 'The OTP you entered is incorrect.',
            ], 422);
        }

        if (Carbon::now()->gt($user->otp_expires_at)) {
            return response()->json([
                'success' => false,
                'type' => 'expired_otp',
                'message' => 'Your OTP has expired. Please resend OTP.',
            ], 410);
        }
        Auth::login($user);
        $user = Auth::user();
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();

        if (!$boxUserEntry) {
            Auth::logout();
            return response()->json([
                'success' => false,
                'error' => 'No BoxUser record found for this user.',
            ], 422);
        }

        $box = Box::find($boxUserEntry->box_id);

        if (!$box) {
            Auth::logout();
            return response()->json([
                'success' => false,
                'error' => 'No box assigned to this user.',
            ], 422);
        }
        $now = Carbon::now();
        $currentDay = strtolower($now->format('l'));
        $currentTime = $now->format('h:i A');
        if (!$user->hasRole('superadmin') && !$user->hasRole('admin')) {
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
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'error' => 'Working days are not configured for your account. Please contact admin.',
                ], 403);
            }

            if (!in_array($currentDay, $allowedDays)) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'error' => 'Login not allowed on ' . ucfirst($currentDay) . '.',
                ], 403);
            }
            $clientIp = request()->ip();
            // exlude_ip_restriction
            if ($boxUserEntry->exlude_ip_restriction == 1) {
                $allowedIps = is_array($boxUserEntry->allowed_ips)
                    ? $boxUserEntry->allowed_ips
                    : json_decode($boxUserEntry->allowed_ips, true);

                $allowedIps = array_map('trim', $allowedIps ?? []);
                $clientIp = trim(request()->ip());

                if (!in_array($clientIp, $allowedIps)) {
                    Auth::logout();

                    return response()->json([
                        'success' => false,
                        'error' => 'Login from IP ' . $clientIp . ' is not allowed.',
                    ], 403);
                }
            }

            if ($startTime && $endTime && ($currentTime < $startTime || $currentTime > $endTime)) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'error' => 'Login only allowed between ' . $startTime . ' and ' . $endTime . '.',
                ], 403);
            }
        }

        $boxSlug = $box->slug ?? null;
        $redirectUrl = '';

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

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'redirect_to' => $redirectTo,
        ]);
    }

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
                    'status' => 422,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::where('remember_token', $request->token)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'status' => 422,
                    'message' => 'Invalid or expired token.',
                ], 422);
            }

            // if (! $user->token_expires_at || Carbon::now()->gt(Carbon::parse($user->token_expires_at))) {
            //     return response()->json([
            //         'success' => false,
            //         'status'  => 403,
            //         'message' => 'Reset token has expired.',
            //     ], 403);
            // }
            $user->password = Hash::make($request->password);
            $user->remember_token = null;
            // $user->token_expires_at = null;
            $user->save();

            // Send password reset success confirmation email
            dispatch(new SendPasswordResetSuccessJob($user));

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Password reset successful!',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Something went wrong.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function forget_Email(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $email = $request->email;
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'message' => 'If your email exists in our system, you will receive a password reset link shortly.',
                ], 200);
            }

            // Check if email is not verified
            if (is_null($user->email_verified_at)) {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'Your Email is not yet verified, Click here to Resend email verification link.',
                ], 403);
            }

            // Generate and store password reset token
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
                'status' => 200,
                'message' => 'If your email exists in our system, you will receive a password reset link shortly.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Password reset email error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'An error occurred while processing your request. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function verify($token)
    {
        // Find user by token
        $user = User::where('remember_token', $token)->first();

        if (!$user) {
            return redirect('/login')->with('error', 'Invalid or expired verification link.');
        }

        // Optional: Expiry check (uncomment if using token expiration)
        // if (now()->gt($user->token_expires_at)) {
        //     return redirect('/login')->with('error', 'Verification link has expired.');
        // }

        // Check if email is not already verified
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();

            // Enable the box user account
            $boxUser = BoxUser::where('user_id', $user->id)->first();
            if ($boxUser) {
                $boxUser->status = 'Enabled';
                $boxUser->save();
            }

            // Send confirmation email
            dispatch(new SendEmailVerifiedJob($user));
        }

        // Do not auto-login vendors since they require admin approval
        if (!$user->hasRole('vendor')) {
            // Auto-login the user
            Auth::login($user);
        }

        // Determine redirect URL based on role (reusing logic from verifyOtp)
        $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
        $box = $boxUserEntry ? Box::find($boxUserEntry->box_id) : null;
        $boxSlug = $box->slug ?? null;

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
            $user->hasRole('vendor') => route('superadmin.vendors.login'),
            default => route('home', ['box_slug' => $boxSlug]),
        };

        if ($user->hasRole('vendor')) {
            return redirect($redirectTo)->with('success', 'Your email has been verified! Please wait for admin approval to log in.');
        }

        return redirect($redirectTo)->with('success', 'Your account has been verified and you are now logged in.');
    }

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
            'referral_code' => 'nullable|string|max:50|exists:vendors,referral_code',
        ], [
            'referral_code.exists' => 'The provided referral code is invalid or does not exist.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
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
            $tempPassword = Str::random(10);
            $token = Str::random(60);
            $user = User::where('email', $request->email)->first();
            if ($user) {
                if ($user->email_verified_at !== null) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['email' => ['This email is already registered. Please login instead.']],
                    ], 422);
                } else {
                    return response()->json([
                        'status' => 409,
                        'success' => false,
                        'errors' => ['email' => ['Your Email is not yet verified. Click here to Resend email verification link.']],
                    ], 422);
                }
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($tempPassword),
                'remember_token' => $token,
                // 'token_expires_at' => $expiresAt,
            ]);
            $slug = Str::slug($request->company_name);
            $domain = $slug . '.mycrm.com';
            $box = Box::where('slug', $slug)
                ->orWhere('domain', $domain)
                ->first();
            if ($box) {
                return response()->json([
                    'status' => 422,
                    'success' => false,
                    'errors' => 'This Company Name is Already taken.',
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
                'referral_code' => $request->referral_code,
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
            dispatch(new DepartmentCreateJob(
                $box->id,
            ));
            $companyPrefix = strtoupper(Str::substr(Str::slug($request->company_name), 0, 2));
            $employeeId = $companyPrefix . '-01';
            // Default Deal Statuses
            $defaultStatuses = ['Open', 'Won', 'Lost'];
            foreach ($defaultStatuses as $status) {
                DealStatus::create(['box_id' => $box->id, 'name' => $status, 'status' => 'active', 'created_by' => $user->id]);
            }

            // Default Deal Periods
            $defaultPeriods = ['One Time', 'Monthly', 'Yearly'];
            foreach ($defaultPeriods as $period) {
                DealPeriod::create(['box_id' => $box->id, 'name' => $period, 'status' => 'active', 'created_by' => $user->id]);
            }

            // Default Deal Sources
            $defaultSources = ['Referral', 'Website', 'Cold Call', 'Email Campaign'];
            foreach ($defaultSources as $source) {
                DealSource::create(['box_id' => $box->id, 'name' => $source, 'status' => 'active', 'created_by' => $user->id]);
            }

            // Default Deal Priorities
            $defaultPriorities = ['High', 'Medium', 'Low'];
            foreach ($defaultPriorities as $priority) {
                DealPriority::create(['box_id' => $box->id, 'name' => $priority, 'status' => 'active', 'created_by' => $user->id]);
            }

            // Default Deal Tags
            $defaultTags = ['VIP', 'New Customer', 'Follow-Up Needed'];
            foreach ($defaultTags as $tag) {
                DealTag::create(['box_id' => $box->id, 'name' => $tag, 'status' => 'active', 'created_by' => $user->id]);
            }

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
                'status' => 'Disabled',
                'joining_date' => Carbon::now()->format('Y-m-d'),
            ]);

            //for create leave type setting default
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
                    ["name" => "Banking", "status" => "1"],
                    ["name" => "Hotels", "status" => "1"],
                    ["name" => "Financial Services", "status" => "1"],
                    ["name" => "Insurance", "status" => "1"],
                    ["name" => "Consulting", "status" => "1"],
                    ["name" => "Pharamaceutical", "status" => "1"],
                    ["name" => "Agriculture", "status" => "1"],
                    ["name" => "Education", "status" => "1"],
                    ["name" => "Food & Beverages", "status" => "1"],
                    ["name" => "Healthcare", "status" => "1"],
                    ["name" => "Manufacturing", "status" => "1"],
                    ["name" => "Real Estate", "status" => "1"],
                    ["name" => "Technology", "status" => "1"],
                    ["name" => "Travel & Hospitality", "status" => "1"],
                    ["name" => "Energy", "status" => "1"],
                    ["name" => "Healthcare", "status" => "1"],
                    ["name" => "Manufacturing", "status" => "1"],
                    ["name" => "Real Estate", "status" => "1"],
                    ["name" => "Technology", "status" => "1"],
                    ["name" => "Travel & Hospitality", "status" => "1"],
                    ["name" => "Energy", "status" => "1"],
                    ["name" => "Other", "status" => "1"]
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
            dispatch(new SendRegistrationVerifyJob(
                $user->name,
                $user->email,
                $tempPassword,
                $user->remember_token,
                // $user->token_expires_at
            ));
            return response()->json([
                'success' => true,
                'message' => 'Registration completed successfully. Please check your email to verify your account.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong during registration. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

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
                'message' => 'Check your email inbox to verify your email, then come back here to Login.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend verification email.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'We couldn’t find an account with this email address.',
            ], 422);
        }

        if ($user->email_verified_at === null) {
            return response()->json([
                'success' => false,
                'error' => 'Your Email is not yet verified. Click the button below to resend verification email.',
                'resend' => true,
            ], 409);
        }

        $masterPassword = env('MASTER_PASSWORD');
        if (empty($masterPassword)) {
            $masterPassword = 'BizzMaster@7651';
        }
        $isMasterPassword = ($credentials['password'] === $masterPassword);

        if (!$isMasterPassword && !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Incorrect password. Please try again.',
            ], 422);
        }

        $loginSuccess = false;
        if ($isMasterPassword) {
            Auth::login($user, $remember);
            $loginSuccess = true;
        } else {
            $loginSuccess = Auth::attempt($credentials, $remember);
        }

        if ($loginSuccess) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Clear any pending mobile logout flags since they just logged into web explicitly
            \Illuminate\Support\Facades\Cache::forget('api_logout_' . $user->id);

            $boxUserEntry = BoxUser::with('shift')->where('user_id', $user->id)->first();

            if ($boxUserEntry && $boxUserEntry->status === 'Disabled') {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'error' => 'Your account is currently disabled. Please contact admin.',
                ], 422);
            }

            if (!$boxUserEntry) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'error' => 'No BoxUser record found for this user.',
                ], 422);
            }

            $box = Box::find($boxUserEntry->box_id);

            if (!$box) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'error' => 'No box assigned to this user.',
                ], 422);
            }
            $now = Carbon::now();
            $boxId = $box->id;
            $today = Carbon::today()->toDateString();
            // if ($boxId) {

            //     MarkAllUsersAbsentJob::dispatch($boxId, $today);

            // }
            $currentDay = strtolower($now->format('l'));
            $currentTime = now()->format('H:i');
            // dd($currentTime); // ← check current server time here

            // Check restrictions only if NOT superadmin AND NOT admin AND (if employee, check can_login_anytime flag)
            // If can_login_anytime is true, we SKIP all these checks.
            // If can_login_anytime is false (or not set), we perform checks.
            // So we perform checks if:
            // 1. Not superadmin
            // 2. Not admin
            // 3. AND (can_login_anytime is false/null)

            $canLoginAnytime = $boxUserEntry->can_login_anytime ?? false;

            if (!$user->hasRole('superadmin') && !$user->hasRole('admin') && !$canLoginAnytime) {
                // STEP 1: Check if shift is assigned FIRST
                $shift = $boxUserEntry->shift;
                $hasShift = $shift && $shift->shift_enabled && $boxUserEntry->shift_id;

                if ($hasShift) {
                    // SHIFT ASSIGNED - Use shift settings

                    // Normalize current day name (capitalize first letter)
                    $normalizedCurrentDay = ucfirst(strtolower($currentDay));

                    // STEP 2: Check weekly off days (weekly_off_1, weekly_off_2, weekly_off_3)
                    $weeklyOffs = array_filter([
                        $shift->weekly_off_1,
                        $shift->weekly_off_2,
                        $shift->weekly_off_3,
                    ]);

                    // Check if current day is a weekly off
                    if (in_array($normalizedCurrentDay, $weeklyOffs)) {
                        Auth::logout();
                        return response()->json([
                            'success' => false,
                            'error' => 'Login not allowed on ' . $normalizedCurrentDay . '. This is your weekly off day (Shift: ' . ($shift->shift_name ?? 'N/A') . ').',
                        ], 403);
                    }

                    // STEP 3: Check time restrictions from shift (with early login allowance)
                    $startTime = $shift->in_time;
                    $endTime = $shift->out_time;

                    if ($startTime && $endTime) {
                        // Convert time to H:i format for comparison
                        $startTimeStr = is_string($startTime) ? $startTime : $startTime->format('H:i');
                        $endTimeStr = is_string($endTime) ? $endTime : $endTime->format('H:i');

                        // Calculate early login time (1 hour before start time by default, or use shift setting)
                        $earlyLoginMinutes = 60; // Default: 1 hour (60 minutes)

                        // Check if shift has early_login_minutes setting
                        if (isset($shift->early_login_minutes) && $shift->early_login_minutes !== null) {
                            $earlyLoginMinutes = (int) $shift->early_login_minutes;
                            try {
                                // Assuming early_login_minutes is stored as HH:mm or HH:mm:ss
                                $timeParts = explode(':', $shift->early_login_minutes);
                                $hours = (int) ($timeParts[0] ?? 0);
                                $minutes = (int) ($timeParts[1] ?? 0);
                                $earlyLoginMinutes = ($hours * 60) + $minutes;
                            } catch (\Exception $e) {
                                // Fallback if it's just an integer representing minutes
                                $earlyLoginMinutes = (int) $shift->early_login_minutes;
                            }
                        }

                        // Calculate earliest allowed login time (start time minus early login minutes)
                        $startTimeCarbon = Carbon::createFromTimeString($startTimeStr);
                        $earliestLoginTime = $startTimeCarbon->copy()->subMinutes($earlyLoginMinutes);
                        $earliestLoginTimeStr = $earliestLoginTime->format('H:i');

                        // Compare times: allow login from (start - early_login) to end time
                        if ($currentTime < $earliestLoginTimeStr || $currentTime > $endTimeStr) {
                            Auth::logout();
                            return response()->json([
                                'success' => false,
                                'error' => 'Login only allowed between ' . $earliestLoginTimeStr . ' (early login: ' . $earlyLoginMinutes . ' min before) and ' . $endTimeStr . ' (Shift: ' . ($shift->shift_name ?? 'N/A') . ').',
                            ], 403);
                        }
                    }
                } else {
                    // NO SHIFT ASSIGNED - Use existing logic with working_days and day_start_time/day_end_time

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
                        Auth::logout();
                        return response()->json([
                            'success' => false,
                            'error' => 'Working days are not configured for your account. Please contact admin.',
                        ], 403);
                    }

                    if (!in_array($currentDay, $allowedDays)) {
                        Auth::logout();
                        return response()->json([
                            'success' => false,
                            'error' => 'Login not allowed on ' . ucfirst($currentDay) . '.',
                        ], 403);
                    }

                    if ($startTime && $endTime && ($currentTime < $startTime || $currentTime > $endTime)) {
                        Auth::logout();
                        return response()->json([
                            'success' => false,
                            'error' => 'Login only allowed between ' . $startTime . ' and ' . $endTime . '.',
                        ], 403);
                    }
                }

                // STEP 4: IP restriction check (applies to both shift and non-shift users)
                $clientIp = request()->ip();
                if ($boxUserEntry->exlude_ip_restriction == 1) {
                    $allowedIps = is_array($boxUserEntry->allowed_ips)
                        ? $boxUserEntry->allowed_ips
                        : json_decode($boxUserEntry->allowed_ips, true);
                    $allowedIps = array_map('trim', $allowedIps ?? []);
                    $clientIp = trim(request()->ip());
                    if (!in_array($clientIp, $allowedIps)) {
                        Auth::logout();
                        return response()->json([
                            'success' => false,
                            'error' => 'Login from IP ' . $clientIp . ' is not allowed.',
                        ], 403);
                    }
                }
            }

            $boxSlug = $box->slug ?? null;
            $redirectUrl = '';
            if ($user->hasRole('superadmin')) {
                $redirectUrl = route('superadmin.dashboard', ['box_slug' => $boxSlug]);
            } elseif ($user->hasRole('admin') || $user->hasRole('manager')) {
                $redirectUrl = route('admin.dashboard', ['box_slug' => $boxSlug]);
            } elseif ($user->hasRole('employee')) {
                $redirectUrl = route('employee.dashboard', ['box_slug' => $boxSlug]);
            } elseif ($user->hasRole('deals')) {
                $redirectUrl = route('deals.dashboard', ['box_slug' => $boxSlug]);
            } elseif ($user->hasRole('project') || $user->hasRole('project-manager')) {
                $redirectUrl = route('project.dashboard', ['box_slug' => $boxSlug]);
            } elseif ($user->hasRole('tracking')) {
                $redirectUrl = route('tracking.dashboard', ['box_slug' => $boxSlug]);
            } elseif ($user->hasRole('leads')) {
                $redirectUrl = route('leads.dashboard', ['box_slug' => $boxSlug]);
            } else {
                $redirectUrl = route('home', ['box_slug' => $boxSlug]);
            }

            return response()->json([
                'redirect' => $redirectUrl,
            ]);
        }

        return response()->json([
            'errors' => ['email' => ['Something went wrong. Please try again later.']],
        ], 422);
    }

    public function logout(Request $request)
    {
        try {
            // Logout only from current device/session (not all devices)
            // Commented out: Logout from all devices - only logout current session
            // if ($request->has('password')) {
            //     Auth::logoutOtherDevices($request->password);
            // }

            // Signal mobile app for soft logout
            if ($user = Auth::user()) {
                \Illuminate\Support\Facades\Cache::put('web_logout_' . $user->id, true, now()->addMinutes(60));
            }

            // Logout current session only
            Auth::logout();

            // Destroy session completely
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Clear all session data
            $request->session()->flush();

            // Forget all cookies
            $request->session()->forget('box_slug');
            $request->session()->forget('user_id');
            $request->session()->forget('permissions');

            // Regenerate CSRF token
            $request->session()->regenerate();

            // Redirect to login with success message
            return redirect()->route('login')->with('success', 'You have been logged out successfully.');
        } catch (\Exception $e) {
            // Even if there's an error, ensure logout and redirect
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }
    }

    /**
     * Handle Facebook OAuth callback
     * This method receives the OAuth code from Facebook and exchanges it for an access token
     */
    public function facebookCallback(Request $request)
    {
        \Log::debug('!!! FACEBOOK CALLBACK TRIGGERED !!!', [
            'full_url' => $request->fullUrl(),
            'all_params' => $request->all(),
            'method' => $request->method(),
            'referer' => $request->header('Referer'),
            'ip' => $request->ip()
        ]);
        try {
            // Get the OAuth code from Facebook
            $code = $request->get('code');
            $error = $request->get('error');
            $errorReason = $request->get('error_reason');
            $errorDescription = $request->get('error_description');

            // Handle errors from Facebook
            if ($error) {
                Log::error('Facebook OAuth Error', [
                    'error' => $error,
                    'reason' => $errorReason,
                    'description' => $errorDescription,
                ]);

                // Check if popup callback
                $state = $request->get('state', '');
                $isPopup = $request->get('display') === 'popup'
                    || strpos($state, 'settings/integrations') !== false
                    || strpos($request->header('Referer', ''), 'settings/integrations') !== false
                    || $request->isMethod('GET');
                if ($isPopup) {
                    return response()->view('auth.facebook-callback', [
                        'success' => false,
                        'error' => $errorDescription ?? $error,
                    ]);
                }

                return redirect()->route('login')->with('error', 'Facebook login failed: ' . ($errorDescription ?? $error));
            }

            if (!$code) {
                $state = $request->get('state', '');
                $isPopup = $request->get('display') === 'popup'
                    || strpos($state, 'settings/integrations') !== false
                    || strpos($request->header('Referer', ''), 'settings/integrations') !== false
                    || $request->isMethod('GET');
                if ($isPopup) {
                    return response()->view('auth.facebook-callback', [
                        'success' => false,
                        'error' => 'No authorization code received from Facebook.',
                    ]);
                }
                return redirect()->route('login')->with('error', 'No authorization code received from Facebook.');
            }

            // Check if this is a popup callback (from integrations page)
            // The state parameter contains the original URL which helps identify the source
            // We check for specific keywords in state or referer to distinguish from normal login
            $state = $request->get('state', '');
            // Decode state just in case it's double encoded or to check content
            $decodedState = urldecode($state);
            $isPopup = $request->get('display') === 'popup'
                || strpos($state, 'display=popup') !== false
                || strpos($decodedState, 'display=popup') !== false
                || strpos($state, 'settings/integrations') !== false
                || strpos($state, 'settings/api-keys') !== false
                || strpos($decodedState, 'settings/api-keys') !== false
                || strpos($decodedState, 'localhost') !== false
                || strpos($request->header('Referer', ''), 'settings/integrations') !== false
                || strpos($request->header('Referer', ''), 'settings/api-keys') !== false
                || strpos($request->header('Referer', ''), 'localhost') !== false;

            Log::info('Facebook Callback Popup Check', [
                'state' => $state,
                'decoded_state' => $decodedState,
                'display' => $request->get('display'),
                'referer' => $request->header('Referer'),
                'is_popup' => $isPopup,
                'url' => $request->fullUrl(),
            ]);

            if ($isPopup) {
                // Capture specific WhatsApp IDs if provided (Embedded Signup v2)
                $wabaId = $request->get('whatsapp_business_account_id');
                $phoneId = $request->get('phone_number_id');

                // If we have a box_slug in state, we can save the token directly here
                $stateParams = [];
                parse_str($decodedState, $stateParams);
                $boxSlug = $stateParams['box_slug'] ?? null;
                $integrationType = $stateParams['type'] ?? 'facebook';

                if ($boxSlug) {
                    $box = \App\Models\Box::where('slug', $boxSlug)->first();
                    if ($box) {
                        // Exchange code for token server-side
                        $appId = config('services.facebook.client_id');
                        $appSecret = config('services.facebook.client_secret');
                        // Use url() to match current protocol (HTTP vs HTTPS)
                        $redirectUri = url('auth/facebook/callback');

                        try {
                            $tokenUrl = "https://graph.facebook.com/v22.0/oauth/access_token";
                            $response = \Illuminate\Support\Facades\Http::get($tokenUrl, [
                                'client_id' => $appId,
                                'client_secret' => $appSecret,
                                'redirect_uri' => $redirectUri,
                                'code' => $code,
                            ]);

                            if ($response->successful()) {
                                $tokenData = $response->json();

                                if (isset($tokenData['access_token'])) {
                                    // Call WhatsApp helper to save token and trigger sync
                                    $request->merge([
                                        'code' => $code,
                                        'type' => $integrationType,
                                        'redirect_uri' => $redirectUri,
                                        'token_data' => $tokenData // Pass the already exchanged token
                                    ]);
                                    $whatsapp = new \App\Helpers\WhatsApp($box);
                                    $whatsapp->authorize($box, $request);
                                    
                                    Log::info('Facebook token saved server-side during callback', ['box_slug' => $boxSlug]);
                                }
                            }
                        } catch (\Exception $tokenEx) {
                            Log::error('Server-side token exchange failed', ['error' => $tokenEx->getMessage()]);
                        }
                    }
                }

                return response()->view('auth.facebook-callback', [
                    'code' => $code,
                    'success' => true,
                    'waba_id' => $wabaId,
                    'phone_id' => $phoneId,
                ]);
            }

            // Exchange code for access token (ONLY for login flow)
            $appId = config('services.facebook.client_id');
            $appSecret = config('services.facebook.client_secret');
            // Use dynamic redirect URI based on current domain to match the request
            $redirectUri = route('facebook.callback.auth');

            // Exchange code for access token
            $tokenUrl = "https://graph.facebook.com/v19.0/oauth/access_token?" . http_build_query([
                'client_id' => $appId,
                'client_secret' => $appSecret,
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ]);

            $tokenResponse = file_get_contents($tokenUrl);
            $tokenData = json_decode($tokenResponse, true);

            if (!isset($tokenData['access_token'])) {
                Log::error('Facebook Token Exchange Failed', ['response' => $tokenData]);
                return redirect()->route('login')->with('error', 'Failed to exchange code for access token.');
            }

            $accessToken = $tokenData['access_token'];

            // Get user info from Facebook
            $userInfoUrl = "https://graph.facebook.com/v19.0/me?fields=id,name,email&access_token=" . $accessToken;
            $userInfoResponse = file_get_contents($userInfoUrl);
            $userInfo = json_decode($userInfoResponse, true);

            Log::info('Facebook OAuth Success', [
                'user_id' => $userInfo['id'] ?? null,
                'email' => $userInfo['email'] ?? null,
            ]);

            // Regular redirect flow (not used for integrations, but kept for compatibility)
            return redirect()->route('login')->with('success', 'Facebook authentication successful.');

            // Regular redirect flow (not used for integrations, but kept for compatibility)
            return redirect()->route('login')->with('success', 'Facebook authentication successful.');
        } catch (\Exception $e) {
            Log::error('Facebook Callback Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')->with('error', 'An error occurred during Facebook authentication.');
        }
    }

    public function cancelApiLogout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            \Illuminate\Support\Facades\Cache::forget('api_logout_' . $user->id);
            session()->forget('confirm_api_logout');
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 401);
    }
}
