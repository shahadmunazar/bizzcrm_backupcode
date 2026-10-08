<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use App\Models\BoxUser;
use Illuminate\Http\Request;

class AttendaceFrontendController extends Controller
{
    public function admin_attendace()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'admin');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access admin attendance.');
        }
        return view('frontend.attendance.admin_attendace');
    }

    public function process_attendance(Request $request, $box_slug)
    {
        $year = $request->input('year');
        $month = $request->input('month');

        if (!$year || !$month) {
            return response()->json(['success' => false, 'message' => 'Year and month are required']);
        }

        // Dispatch job to process attendance in the background
        \App\Jobs\ProcessManualBiometricAttendance::dispatch($year, $month, $box_slug);

        \Log::info("Manual attendance process Job dispatched for {$year}-{$month} in box: {$box_slug}");

        return response()->json([
            'success' => true,
            'message' => "Attendance processing for {$month}-{$year} has been queued successfully and will complete in the background."
        ]);
    }

    public function team_attendace()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'team_admin');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access team attendance.');
        }
        return view('frontend.attendance.team_attendace');
    }

    public function attendace_controller()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'calendar');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access the attendance calendar.');
        }
        $countries = Country::all();
        return view('frontend.attendance.calendar', compact('countries'));
    }

    public function biometric_machine()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'biometric');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access biometric settings.');
        }
        return view('frontend.attendance.biometric');
    }

    public function attendance_employee()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'employee');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access employee attendance.');
        }
        return view('frontend.attendance.attendance_employee');
    }


    public function holiday_type()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'holiday_type');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access holiday types.');
        }
        return view('frontend.attendance.holiday_type');
    }

    public function attendance_shift()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'shift');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access shift settings.');
        }
        return view('frontend.attendance.shift_attendance');
    }

    public function holiday_attendance()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'holiday');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access holidays.');
        }
        return view('frontend.attendance.holiday');
    }

    public function lead_type()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'leave_type');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access leave types.');
        }
        return view('frontend.attendance.leave_type');
    }

    public function Leave_Transaction_all()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'leave_transaction');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access leave transactions.');
        }
        return view('frontend.attendance.leave_transaction_all');
    }
    public function Leave_Transaction_all_team()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'team_leave_transaction');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access team leave transactions.');
        }
        return view('frontend.attendance.leave_transaction_all_team');
    }

    public function Leave_Transaction()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('attendance', 'read', 'leave_transaction');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            abort(403, 'You do not have permission to access leave transactions.');
        }
        return view('frontend.attendance.leave_transaction');
    }
}
