<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Box;
use Illuminate\Support\Facades\Schema;

class UpdateBoxLeaveCreditSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $boxes = Box::all();
        
        $hasFromDateColumn = Schema::hasColumn('boxes', 'attendace_module_from_date');

        foreach ($boxes as $box) {
            $createdAt = $box->created_at;
            if (!$createdAt) {
                continue;
            }

            $time = now()->addMinutes(5)->format('H:i');
            $day = now()->format('j');
            // Format for the 'Select Date & Time' field (e.g., 09/29/2026 02:30 PM)
            $dateTime = $createdAt->format('m/d/Y h:i A');

            $updateData = [
                'attendace_module_date' => $dateTime,
                'leave_credit_mode' => 'specific_number',
                'leave_credit_time' => $time,
                'leave_credit_selected_days' => $day,
                'start_day_of_every_month' => null,
                'last_day_of_every_month' => null,
            ];

            if ($hasFromDateColumn) {
                $updateData['attendace_module_from_date'] = $day;
            }

            $box->update($updateData);
        }

        $this->command->info('Successfully updated Leave Credit Settings (Day and Time set according to Box creation date) for all boxes.');
    }
}
