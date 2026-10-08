<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BoxUser;
use App\Models\Box;
use App\Models\Department;
use App\Models\Team;

class BackfillEmployeeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $boxes = Box::all();

        foreach ($boxes as $box) {
            // Determine prefix for this box
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

            // Find 'Marketing' department for this box
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

            // Get BoxUsers without employee_id for this box
            $usersToBackfill = BoxUser::where('box_id', $box->id)
                ->where(function($q) {
                    $q->whereNull('employee_id')->orWhere('employee_id', '');
                })
                ->orderBy('id', 'asc')
                ->get();

            if ($usersToBackfill->count() > 0) {
                // Find latest number
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
                    
                    // Assign random designation from marketing if available
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
}
