<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EmployeesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Jobs\SendRegistrationVerifyJob;
use App\Models\Box;
use App\Models\BoxUser;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeBankDetail;
use App\Models\EmployeeEducation;
use App\Models\EmployeeEmergencyContact;
use App\Models\EmployeeExperience;
use App\Models\Employeefamily;
use App\Models\Team;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
class ManageUserController extends Controller
{
    public function index(){
        return view('frontend.manageusers.index');
    }

    public function users_details(){
        return view('frontend.manageusers.details');
    }

    public function ResendusersVerifcation(Request $request){
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

    public function delete_users(Request $request)
    {
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


    public function store_users(StoreEmployeeRequest $request)
    {
        DB::beginTransaction();

        try {
            $authUser            = Auth::user();
            $boxUser             = BoxUser::where('user_id', $authUser->id)->with('box')->first();
            $box_id              = $boxUser->box_id;
            $requestedEmployeeId = $request->employee_id;
            $boxName             = $boxUser->box->name;
            $expectedPrefix      = strtoupper(Str::substr(Str::slug($boxName), 0, 2));
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
                $lastId = $lastEmployee ? $lastEmployee->employee_id : $expectedPrefix . '-01';
                if ($lastEmployee && preg_match("/{$expectedPrefix}-(\d+)/", $lastId, $matches)) {
                    $nextNumber = str_pad((int) $matches[1] + 1, 2, '0', STR_PAD_LEFT);
                } else {
                    $nextNumber = '01';
                }
                $suggestedId = $expectedPrefix . '-' . $nextNumber;
                return response()->json([
                    'error' => "The employee ID `{$requestedEmployeeId}` has already been taken. Last generated ID was: {$lastId}. You should use employee_id: {$suggestedId}",
                ], 422);
            }
            $existingUser = User::where('email', $request->email)->first();
            if ($existingUser) {
                return response()->json([
                    'error' => 'The email has already been taken.',
                ], 422);
            }
            $tempPassword = Str::random(10);
            $token        = Str::random(60);
            $user         = User::create([
                'name'           => $request->name,
                'email'          => $request->email,
                'password'       => Hash::make($tempPassword),
                'remember_token' => $token,
            ]);
            // Assign employee role to user
            $user->assignRole('employee');
            $imageName = null;
            $imagePath = null;
            if ($request->hasFile('profile_image')) {
                $file      = $request->file('profile_image');
                $imageName = Str::slug($request->name . '-' . now()->timestamp) . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('employees/profile_images', $imageName, 'public');
            }
            $employee = BoxUser::create([
                'user_id'               => $user->id,
                'box_id'                => $box_id,
                'added_by'              => $authUser->id,
                'name'                  => $request->name,
                'employee_id'           => $requestedEmployeeId,
                'staff_role'            => $request->staff_role ?? 'admin',
                'email'                 => $request->email,
                'username'              => $request->email,
                'phone'                 => $request->phone,
                'joining_date'          => $request->joining_date ?? now()->format('Y-m-d'),
                'department'            => $request->department,
                'designation'           => $request->designation,
                'profile_image'         => $imageName,
                'profile_image_path'    => $imagePath,
                'user_box_timing'       => $request->has('user_box_timing') ? '1' : '0',
                'day_start_time'        => $request->day_start_time,
                'day_end_time'          => $request->day_end_time,
                'working_days'          => $request->has('working_days') ? $request->working_days : null,
                'exlude_ip_restriction' => $request->has('exlude_ip_restriction') ? '1' : '0',
                'exclude_round_robin'   => $request->has('exclude_round_robin') ? '1' : '0',
                'allowed_ips'           => $request->has('allowed_ips') ? $request->allowed_ips : null,
            ]);
            if ($request->filled('permissions')) {
                $user->syncPermissions($request->permissions);
            }
            DB::commit();
            dispatch(new SendRegistrationVerifyJob(
                $user->name,
                $user->email,
                $tempPassword,
                $user->remember_token,
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

public function userstoggle(Request $request)
    {
        try {
            $employeeId       = $request->input('employee_id');
            $employee         = BoxUser::findOrFail($employeeId);
            $employee->status = $employee->status === 'Enabled' ? 'Disabled' : 'Enabled';
            $employee->save();
            return response()->json([
                'success' => true,
                'message' => "Employee status updated to {$employee->status}.",
                'data'    => $employee,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

}
