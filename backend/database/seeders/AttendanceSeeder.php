<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BoxUser;
use App\Models\Attendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = BoxUser::all();
        $startDate = Carbon::now()->subMonths(3)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $period = new CarbonPeriod($startDate, $endDate);

        foreach ($users as $user) {
            foreach ($period as $date) {
                $status = rand(0, 1) ? 'Present' : 'Absent';
                if ($status === 'Present') {
                    $checkIn = Carbon::createFromTime(rand(8, 10), rand(0, 59), 0)->setDateFrom($date);
                    $checkOut = Carbon::createFromTime(rand(16, 19), rand(0, 59), 0)->setDateFrom($date);
                    $breakDuration = '01:00:00';

                    $workingHours = $checkOut->diffInSeconds($checkIn) - 3600;
                    $workingHoursFormatted = gmdate('H:i:s', $workingHours);

                    $lateDuration = $checkIn->diffInMinutes(Carbon::createFromTime(9, 0, 0)) > 0
                        ? gmdate('H:i:s', $checkIn->diffInSeconds(Carbon::createFromTime(9, 0, 0)))
                        : '00:00:00';
                } else {
                    $checkIn = $checkOut = $breakDuration = $workingHoursFormatted = $lateDuration = null;
                }

                Attendance::create([
                    'box_user_id'     => $user->id,
                    'box_id'          => $user->box_id ?? null,
                    'created_by'      => 1,
                    'updated_by'      => 1,
                    'attendance_date' => $date->toDateString(),
                    'status'          => $status,
                    'is_auto_punch'   => 0,
                    'check_in'        => $checkIn ? $checkIn->format('H:i:s') : null,
                    'check_out'       => $checkOut ? $checkOut->format('H:i:s') : null,
                    'break_duration'  => $breakDuration,
                    'late_duration'   => $lateDuration,
                    'production_hours'=> $workingHoursFormatted,
                    'working_hours'   => $workingHoursFormatted,
                    'over_time'       => '00:00:00',
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }

        $this->command->info('Attendance records generated for last 3 months!');
    }
}
