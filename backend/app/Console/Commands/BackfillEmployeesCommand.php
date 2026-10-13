<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BoxUser;
use App\Models\Box;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Team;
use App\Models\EmployeeType;
use App\Models\WorkModeSetting;

class BackfillEmployeesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:backfill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfills missing employee IDs for box users and system employees.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting backfill for box users...');
        $this->backfillBoxUsers();
        
        $this->info('Starting backfill for system employees...');
        $this->backfillSystemEmployees();

        $this->info('Starting backfill for employee types and work modes...');
        $this->backfillTypesAndModes();

        $this->info('Backfill completed successfully.');
    }

    private function backfillBoxUsers()
    {
        $boxes = Box::all();

        foreach ($boxes as $box) {
            $dynamicPrefixSetting = $box->dynamic_employee_id;
            $baseString = $dynamicPrefixSetting == 0 ? $box->name : $dynamicPrefixSetting;
            $words = preg_split('/[\s\-]+/', $baseString ?? $box->slug ?? '');
            
            $prefix = '';
            foreach ($words as $word) {
                if (!empty($word)) {
                    $prefix .= strtoupper(substr($word, 0, 1));
                }
            }
            if (empty($prefix)) {
                $prefix = 'EMP';
            }

            $marketingDept = Department::where('box_id', $box->id)
                ->where('name', 'Marketing')
                ->first();

            $teamIds = [];
            if ($marketingDept) {
                $teamIds = Team::where('box_id', $box->id)
                    ->where('department_id', $marketingDept->id)
                    ->pluck('id')
                    ->toArray();
            }

            $usersToBackfill = BoxUser::where('box_id', $box->id)
                ->where(function($q) {
                    $q->whereNull('employee_id')->orWhere('employee_id', '');
                })
                ->orderBy('id', 'asc')
                ->get();

            if ($usersToBackfill->count() > 0) {
                $latestUser = BoxUser::where('box_id', $box->id)
                    ->where('employee_id', 'like', "{$prefix}-%")
                    ->orderByDesc('id')
                    ->first();
                
                $currentNumber = 1;
                if ($latestUser && preg_match("/{$prefix}-(\d+)/", $latestUser->employee_id, $matches)) {
                    $currentNumber = (int) $matches[1] + 1;
                }

                foreach ($usersToBackfill as $user) {
                    $newEmpId = sprintf("%s-%02d", $prefix, $currentNumber);
                    
                    if ($marketingDept && !empty($teamIds)) {
                        $randomTeamId = $teamIds[array_rand($teamIds)];
                        $user->department = $marketingDept->id;
                        $user->designation = $randomTeamId;
                    }

                    $user->employee_id = $newEmpId;
                    $user->save();

                    $currentNumber++;
                }
            }
        }
    }

    private function backfillSystemEmployees()
    {
        $prefix = 'EMP';

        $marketingDept = Department::whereNull('box_id')
            ->where('name', 'Marketing')
            ->first();

        $teamIds = [];
        if ($marketingDept) {
            $teamIds = Team::whereNull('box_id')
                ->where('department_id', $marketingDept->id)
                ->pluck('id')
                ->toArray();
        }

        $employeesToBackfill = Employee::where(function($q) {
                $q->whereNull('employee_id')->orWhere('employee_id', '');
            })
            ->orderBy('id', 'asc')
            ->get();

        if ($employeesToBackfill->count() > 0) {
            $latestEmployee = Employee::where('employee_id', 'like', "{$prefix}-%")
                ->orderByDesc('id')
                ->first();
            
            $currentNumber = 1;
            if ($latestEmployee && preg_match("/{$prefix}-(\d+)/", $latestEmployee->employee_id, $matches)) {
                $currentNumber = (int) $matches[1] + 1;
            }

            foreach ($employeesToBackfill as $employee) {
                $newEmpId = sprintf("%s-%02d", $prefix, $currentNumber);
                
                if ($marketingDept && !empty($teamIds)) {
                    $randomTeamId = $teamIds[array_rand($teamIds)];
                    $employee->department = $marketingDept->id;
                    $employee->designation = $randomTeamId;
                }

                $employee->employee_id = $newEmpId;
                $employee->save();

                $currentNumber++;
            }
        }
    }

    private function backfillTypesAndModes()
    {
        $boxes = Box::all();

        foreach ($boxes as $box) {
            $fullTimeType = EmployeeType::where('box_id', $box->id)
                ->where('name', 'Full-Time')
                ->first();

            $workFromOfficeMode = WorkModeSetting::where('box_id', $box->id)
                ->where('name', 'Work from Office')
                ->first();
                
            if (!$workFromOfficeMode) {
                $workFromOfficeMode = WorkModeSetting::whereNull('box_id')
                    ->where('name', 'Work from Office')
                    ->first();
            }

            if ($fullTimeType && $workFromOfficeMode) {
                BoxUser::where('box_id', $box->id)
                    ->where(function($q) {
                        $q->whereNull('emp_type')
                          ->orWhereNull('work_mode_id')
                          ->orWhere('emp_type', '')
                          ->orWhere('work_mode_id', 0);
                    })
                    ->update([
                        'emp_type' => $fullTimeType->id,
                        'work_mode_id' => $workFromOfficeMode->id,
                    ]);
            }
        }
    }
}
