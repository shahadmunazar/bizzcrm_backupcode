<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Box;
use App\Models\BoxUser;
use App\Models\Department;
use App\Models\Team;
use App\Models\Shift;
use App\Models\EmployeeType;
use App\Models\WorkModeSetting;
use App\Jobs\DepartmentCreateJob;
use App\Jobs\CreateStandardShiftJob;

class BackfillSystemEmployeeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prefix = 'EMP';

        // Cache Standard Shifts by Box ID for fast assignment
        $standardShifts = Shift::where('shift_name', 'Standard Shift')->pluck('id', 'box_id')->toArray();

        $boxes = Box::all();

        foreach ($boxes as $box) {
            // Ensure standard shift exists for this box
            if (!isset($standardShifts[$box->id])) {
                (new CreateStandardShiftJob($box->id))->handle();
                $standardShifts = Shift::where('shift_name', 'Standard Shift')->pluck('id', 'box_id')->toArray();
            }

            // Ensure departments exist for this box
            $departments = Department::where('box_id', $box->id)->get();
            if ($departments->isEmpty()) {
                (new DepartmentCreateJob($box->id))->handle();
                $departments = Department::where('box_id', $box->id)->get();
            }

            // 1. Ensure 'Marketing' department exists for this box
            $marketingDept = Department::where('box_id', $box->id)
                ->where('name', 'Marketing')
                ->first();
            if (!$marketingDept) {
                $marketingDept = Department::create([
                    'box_id'     => $box->id,
                    'name'       => 'Marketing',
                    'user_id'    => $box->superadmin_id ?? 1,
                    'created_by' => $box->superadmin_id ?? 1,
                    'status'     => 1,
                ]);
            }

            // 2. Ensure 'Marketing Executive' team/designation exists under Marketing department
            $marketingExecTeam = Team::where('box_id', $box->id)
                ->where('department_id', $marketingDept->id)
                ->where('name', 'Marketing Executive')
                ->first();
            if (!$marketingExecTeam) {
                $marketingExecTeam = Team::create([
                    'box_id'        => $box->id,
                    'department_id' => $marketingDept->id,
                    'name'          => 'Marketing Executive',
                    'user_id'       => $box->superadmin_id ?? 1,
                    'created_by'    => $box->superadmin_id ?? 1,
                    'status'        => 1,
                ]);
                $marketingExecTeam->saveAsRoot();
            }

            // Find or fallback Full-Time Employee Type for this box
            $fullTimeType = EmployeeType::where('box_id', $box->id)
                ->where('name', 'Full-Time')
                ->first();
            if (!$fullTimeType) {
                $fullTimeType = EmployeeType::whereNull('box_id')
                    ->where('name', 'Full-Time')
                    ->first();
            }
            if (!$fullTimeType) {
                $fullTimeType = EmployeeType::firstOrCreate([
                    'box_id' => $box->id,
                    'name'   => 'Full-Time',
                ], ['status' => 'active']);
            }

            // Find or fallback Work from Office Work Mode for this box
            $workFromOfficeMode = WorkModeSetting::where('box_id', $box->id)
                ->where('name', 'Work from Office')
                ->first();
            if (!$workFromOfficeMode) {
                $workFromOfficeMode = WorkModeSetting::whereNull('box_id')
                    ->where('name', 'Work from Office')
                    ->first();
            }
            if (!$workFromOfficeMode) {
                $workFromOfficeMode = WorkModeSetting::firstOrCreate([
                    'box_id' => $box->id,
                    'name'   => 'Work from Office',
                ], ['status' => 1]);
            }

            // Get all employees for this box
            $boxUsers = BoxUser::where('box_id', $box->id)->orderBy('id', 'asc')->get();
            if ($boxUsers->isEmpty()) {
                continue;
            }

            // Calculate sequence number for this box
            $latestBoxUser = BoxUser::where('box_id', $box->id)
                ->where('employee_id', 'like', "{$prefix}-%")
                ->orderByDesc('id')
                ->first();

            $currentNumber = 1;
            if ($latestBoxUser && preg_match("/{$prefix}-(\d+)/", $latestBoxUser->employee_id, $matches)) {
                $currentNumber = (int) $matches[1] + 1;
            }

            // Identify owner
            $ownerUser = $boxUsers->firstWhere('user_id', $box->superadmin_id) ?? $boxUsers->first();
            $managerCandidateId = $ownerUser ? $ownerUser->id : null;

            foreach ($boxUsers as $boxUser) {
                $isOwner = ($boxUser->id === ($ownerUser->id ?? null));

                // Set staff_role if missing
                if (empty($boxUser->staff_role)) {
                    $boxUser->staff_role = $isOwner ? 'admin' : 'employee';
                }

                // Set employee_id if missing
                if (empty($boxUser->employee_id)) {
                    $boxUser->employee_id = sprintf("%s-%02d", $prefix, $currentNumber++);
                }

                // Set joining_date if missing
                if (empty($boxUser->joining_date)) {
                    $boxUser->joining_date = $boxUser->created_at ? \Carbon\Carbon::parse($boxUser->created_at)->format('Y-m-d') : now()->format('Y-m-d');
                }

                // Set shift_id if missing
                if (empty($boxUser->shift_id) && isset($standardShifts[$box->id])) {
                    $boxUser->shift_id = $standardShifts[$box->id];
                }

                // Assign ONLY Department: Marketing and Designation: Marketing Executive
                $boxUser->department = $marketingDept->id;
                $boxUser->designation = $marketingExecTeam->id;

                // Set Employee Type (Full-Time) if missing
                if (empty($boxUser->emp_type) && $fullTimeType) {
                    $boxUser->emp_type = $fullTimeType->id;
                }

                // Set Working Mode (Work from Office) if missing
                if (empty($boxUser->work_mode_id) && $workFromOfficeMode) {
                    $boxUser->work_mode_id = $workFromOfficeMode->id;
                }

                // Set reporting manager for non-owners
                if (empty($boxUser->reporting_manager_id) && !$isOwner && $managerCandidateId) {
                    $boxUser->reporting_manager_id = $managerCandidateId;
                }

                $boxUser->save();
            }
        }

        // Also handle any orphaned box_users whose box does not exist
        $orphanedUsers = BoxUser::where(function($q) {
            $q->whereNull('employee_id')
              ->orWhere('employee_id', '')
              ->orWhereNull('emp_type')
              ->orWhereNull('work_mode_id')
              ->orWhereNull('department')
              ->orWhereNull('designation');
        })->get();

        if ($orphanedUsers->isNotEmpty()) {
            $defaultType = EmployeeType::whereNull('box_id')->where('name', 'Full-Time')->first();
            $defaultMode = WorkModeSetting::whereNull('box_id')->where('name', 'Work from Office')->first();
            $defaultMarketing = Department::where('name', 'Marketing')->first();
            $defaultExec = $defaultMarketing ? Team::where('department_id', $defaultMarketing->id)->where('name', 'Marketing Executive')->first() : null;
            $orphanNumber = 9000;

            foreach ($orphanedUsers as $orphan) {
                if (empty($orphan->employee_id)) {
                    $orphan->employee_id = sprintf("%s-%04d", $prefix, $orphanNumber++);
                }
                if ($defaultMarketing) {
                    $orphan->department = $defaultMarketing->id;
                }
                if ($defaultExec) {
                    $orphan->designation = $defaultExec->id;
                }
                if (empty($orphan->emp_type) && $defaultType) {
                    $orphan->emp_type = $defaultType->id;
                }
                if (empty($orphan->work_mode_id) && $defaultMode) {
                    $orphan->work_mode_id = $defaultMode->id;
                }
                if (empty($orphan->joining_date)) {
                    $orphan->joining_date = $orphan->created_at ? \Carbon\Carbon::parse($orphan->created_at)->format('Y-m-d') : now()->format('Y-m-d');
                }
                $orphan->save();
            }
        }
    }
}
