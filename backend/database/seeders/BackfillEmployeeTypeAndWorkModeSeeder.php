<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BoxUser;
use App\Models\Box;
use App\Models\EmployeeType;
use App\Models\WorkModeSetting;

class BackfillEmployeeTypeAndWorkModeSeeder extends Seeder
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
            // Find 'Full-Time' Employee Type for this box (with fallback to global/create)
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

            // Find 'Work from Office' Work Mode (fallback to system default if box specific doesn't exist)
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

            // If both are found, update users in this box who don't have them set yet
            if ($fullTimeType && $workFromOfficeMode) {
                BoxUser::where('box_id', $box->id)
                    ->where(function($q) {
                        $q->whereNull('emp_type')
                          ->orWhereNull('work_mode_id')
                          ->orWhere('emp_type', '')
                          ->orWhere('emp_type', 0)
                          ->orWhere('work_mode_id', 0)
                          ->orWhere('work_mode_id', '');
                    })
                    ->update([
                        'emp_type' => $fullTimeType->id,
                        'work_mode_id' => $workFromOfficeMode->id,
                    ]);
            }
        }
    }
}
