<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Team;

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

        // Find 'Marketing' department for system (box_id is null)
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

        // Get Employees without employee_id
        $employeesToBackfill = Employee::where(function($q) {
                $q->whereNull('employee_id')->orWhere('employee_id', '');
            })
            ->orderBy('id', 'asc')
            ->get();

        if ($employeesToBackfill->count() > 0) {
            // Find latest number for the system
            $latestEmployee = Employee::where('employee_id', 'like', "{$prefix}-%")
                ->orderByDesc('id')
                ->first();
            
            $currentNumber = 1;
            if ($latestEmployee && preg_match("/{$prefix}-(\d+)/", $latestEmployee->employee_id, $matches)) {
                $currentNumber = (int) $matches[1] + 1;
            }

            foreach ($employeesToBackfill as $employee) {
                $newEmpId = sprintf("%s-%02d", $prefix, $currentNumber);
                
                // Assign random designation from marketing if available
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
}
