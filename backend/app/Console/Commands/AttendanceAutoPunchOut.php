<?php
namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Box;
use App\Models\BoxUser;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AttendanceAutoPunchOut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:auto-punchout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically punch out employees who forgot to punch out.';

    public function handle()
    {
        $this->info('Starting auto punch-out process...');
        $today       = Carbon::today();
        $currentTime = Carbon::now();
        Log::info('=== AUTO PUNCH-OUT COMMAND STARTED ===', [
            'timestamp'       => $currentTime->toDateTimeString(),
            'processing_date' => $today->toDateString(),
            'note'            => 'Only processing attendance records for current date',
        ]);
        $punchOutCount    = 0;
        $autoPunchedUsers = [];

        try {
            $boxes = Box::all();
            Log::info("Processing auto punch-out for " . $boxes->count() . " box(es)");
            foreach ($boxes as $box) {
                $boxUsers = BoxUser::where('box_id', $box->id)
                    ->where('status', 'Enabled')
                    ->get();
                Log::info("Processing Box ID: {$box->id} ({$box->name}) - Found {$boxUsers->count()} enabled box user(s)");
                foreach ($boxUsers as $boxUser) {
                    $attendance = Attendance::where('box_user_id', $boxUser->id)
                        ->where('box_id', $box->id)
                        // Process any open attendance up to and including today
                        ->whereDate('attendance_date', '<=', $today)
                        ->whereNotNull('check_in')
                        ->whereNull('check_out')
                        ->where(function ($query) {
                            $query->whereNull('status')
                                ->orWhere('status', '')
                                ->orWhere('status', 'Present')
                                ->orWhere('status', 'Comp Off');
                        })
                        ->orderBy('attendance_date') // oldest first
                        ->first();

                    if (! $attendance) {
                        continue;
                    }

                    $expectedPunchOutTime = $this->calculateExpectedPunchOutTime($boxUser, $box, $attendance);
                    if (! $expectedPunchOutTime) {
                        Log::warning("Could not calculate expected punch-out time", [
                            'box_user_id'   => $boxUser->id,
                            'box_id'        => $box->id,
                            'attendance_id' => $attendance->id,
                            'employee_id'   => $boxUser->employee_id,
                            'employee_name' => ($boxUser->first_name ?? '') . ' ' . ($boxUser->last_name ?? ''),
                        ]);
                        continue;
                    }
                    if ($currentTime->greaterThan($expectedPunchOutTime) || $currentTime->equalTo($expectedPunchOutTime)) {
                        // Only update status to 'Punch Out Miss' and mark as auto-punch
                        // Do NOT update check_out, working_hours, or production_hours (keep them null)
                        $updateData = [
                            'is_auto_punch' => true,
                            'status'        => 'Punch Out Miss',
                            'updated_by'    => null,
                        ];

                        $attendance->update($updateData);

                        if ($boxUser->user) {
                            $boxUser->user->notify(new \App\Notifications\MissedPunchOutNotification($attendance->attendance_date, $expectedPunchOutTime->format('Y-m-d H:i:s')));
                        }
                        Log::info("Auto punch-out triggered - Time threshold reached (status only)", [
                            'box_user_id'             => $boxUser->id,
                            'attendance_id'           => $attendance->id,
                            'check_in'                => $attendance->check_in,
                            'expected_punch_out'      => $expectedPunchOutTime->format('H:i:s'),
                            'current_time'            => $currentTime->format('H:i:s'),
                            'time_difference_minutes' => $currentTime->diffInMinutes($expectedPunchOutTime),
                            'note'                    => 'Only status updated, check_out/working_hours/production_hours remain null',
                        ]);
                        $autoPunchedUsers[] = [
                            'box_user_id'        => $boxUser->id,
                            'employee_id'        => $boxUser->employee_id ?? 'N/A',
                            'employee_name'      => trim(($boxUser->first_name ?? '') . ' ' . ($boxUser->last_name ?? '')),
                            'box_id'             => $box->id,
                            'box_name'           => $box->name ?? 'N/A',
                            'attendance_id'      => $attendance->id,
                            'attendance_date'    => $attendance->attendance_date,
                            'check_in'           => $attendance->check_in,
                            'check_out'          => null,
                            'expected_punch_out' => $expectedPunchOutTime->format('H:i:s'),
                            'working_hours'      => null,
                            'production_hours'   => null,
                            'punch_out_time'     => $currentTime->toDateTimeString(),
                        ];

                        $punchOutCount++;
                        $this->info("Auto punched out (status only): {$boxUser->first_name} {$boxUser->last_name} (BoxUser ID: {$boxUser->id}, Employee ID: {$boxUser->employee_id})");
                        Log::info("AUTO PUNCH-OUT EXECUTED (status only)", [
                            'box_user_id'             => $boxUser->id,
                            'employee_id'             => $boxUser->employee_id ?? 'N/A',
                            'employee_name'           => trim(($boxUser->first_name ?? '') . ' ' . ($boxUser->last_name ?? '')),
                            'box_id'                  => $box->id,
                            'box_name'                => $box->name ?? 'N/A',
                            'attendance_id'           => $attendance->id,
                            'attendance_date'         => $attendance->attendance_date,
                            'check_in_time'           => $attendance->check_in,
                            'check_out_time'          => null,
                            'expected_punch_out_time' => $expectedPunchOutTime->format('H:i:s'),
                            'working_hours'           => null,
                            'production_hours'        => null,
                            'status'                  => 'Punch Out Miss',
                            'is_auto_punch'           => true,
                            'punch_out_timestamp'     => $currentTime->toDateTimeString(),
                            'note'                    => 'Only status updated, check_out/working_hours/production_hours remain null',
                        ]);
                    }
                }
            }
            $this->info("Auto punch-out completed. Total punched out: {$punchOutCount}");
            Log::info("=== AUTO PUNCH-OUT COMMAND COMPLETED ===", [
                'total_punched_out'     => $punchOutCount,
                'total_users_processed' => count($autoPunchedUsers),
                'timestamp'             => Carbon::now()->toDateTimeString(),
            ]);
            if (count($autoPunchedUsers) > 0) {
                Log::info("AUTO PUNCH-OUT SUMMARY - All Users Auto-Punched", [
                    'total_count' => count($autoPunchedUsers),
                    'users'       => $autoPunchedUsers,
                ]);
            } else {
                Log::info("AUTO PUNCH-OUT SUMMARY - No users were auto-punched");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error in auto punch-out: " . $e->getMessage());
            Log::error("AUTO PUNCH-OUT ERROR", [
                'error_message' => $e->getMessage(),
                'error_trace'   => $e->getTraceAsString(),
                'timestamp'     => Carbon::now()->toDateTimeString(),
            ]);
            return Command::FAILURE;
        }
    }

    /**
     *
     *
     * @param BoxUser $boxUser
     * @param Box $box
     * @param Attendance $attendance
     * @return Carbon|null
     */
    private function calculateExpectedPunchOutTime(BoxUser $boxUser, Box $box, Attendance $attendance): ?Carbon
    {
        try {
            $attendanceDate = Carbon::parse($attendance->attendance_date);
            $checkInTime    = Carbon::parse($attendanceDate->format('Y-m-d') . ' ' . $attendance->check_in);

            // Priority 1: Use shift configuration if available
            if ($boxUser->shift_id) {
                $shift = Shift::find($boxUser->shift_id);

                if ($shift && $shift->in_time && $shift->out_time) {
                    $shiftInTime  = Carbon::parse($attendanceDate->format('Y-m-d') . ' ' . $shift->in_time);
                    $shiftOutTime = Carbon::parse($attendanceDate->format('Y-m-d') . ' ' . $shift->out_time);

                    // Handle overnight shift (out < in)
                    if ($shiftOutTime->lt($shiftInTime)) {
                        $shiftOutTime->addDay();
                    }

                    // 1) Base duration = out_time - in_time (shift_hours)
                    $shiftDurationSeconds = $shiftInTime->diffInSeconds($shiftOutTime);

                    // 2) Start from ACTUAL check-in, not shift in_time
                    $expectedPunchOut = $checkInTime->copy()->addSeconds($shiftDurationSeconds);

                    // 3) Add overtime if configured
                    if ($shift->overtime_allowed) {
                        $overtimeMinutes = $this->timeToMinutes($shift->overtime_allowed);
                        $beforeOtTime    = $expectedPunchOut->format('H:i:s');
                        $expectedPunchOut->addMinutes($overtimeMinutes);

                        Log::info("Overtime added to expected punch-out", [
                            'box_user_id'      => $boxUser->id,
                            'overtime_allowed' => $shift->overtime_allowed,
                            'overtime_minutes' => $overtimeMinutes,
                            'time_before_ot'   => $beforeOtTime,
                            'time_after_ot'    => $expectedPunchOut->format('H:i:s'),
                        ]);
                    }

                    // 4) Add auto_punchout_time if configured
                    if ($shift->auto_punchout_time) {
                        $autoPunchOutMinutes = $this->timeToMinutes($shift->auto_punchout_time);
                        $beforeAutoTime      = $expectedPunchOut->format('H:i:s');
                        $expectedPunchOut->addMinutes($autoPunchOutMinutes);

                        Log::info("Auto punch-out time added from shift", [
                            'box_user_id'           => $boxUser->id,
                            'auto_punchout_time'    => $shift->auto_punchout_time,
                            'auto_punchout_minutes' => $autoPunchOutMinutes,
                            'time_before_auto'      => $beforeAutoTime,
                            'time_after_auto'       => $expectedPunchOut->format('H:i:s'),
                        ]);
                    }

                    Log::info("Expected punch-out calculated from shift", [
                        'box_user_id'            => $boxUser->id,
                        'shift_id'               => $shift->id,
                        'check_in'               => $attendance->check_in,
                        'shift_duration_seconds' => $shiftDurationSeconds,
                        'overtime_allowed'       => $shift->overtime_allowed ?? 'N/A',
                        'auto_punchout_time'     => $shift->auto_punchout_time ?? 'N/A',
                        'expected_punch_out'     => $expectedPunchOut->format('Y-m-d H:i:s'),
                    ]);

                    return $expectedPunchOut;
                }
            }

            // Priority 2: Use day_end_time (user or box level) - when shift is not assigned
            $userDayEndTime = null;
            $boxDayEndTime  = null;

            // Check box_user day_end_time first
            if ($boxUser->day_end_time) {
                $userDayEndTime = Carbon::parse($attendanceDate->format('Y-m-d') . ' ' . $boxUser->day_end_time);
            }

            // Check box day_end_time
            if ($box->day_end_time) {
                $boxDayEndTime = Carbon::parse($attendanceDate->format('Y-m-d') . ' ' . $box->day_end_time);
            }

            // Determine which day_end_time to use (prioritize user over box, use later if both exist)
            if ($userDayEndTime && $boxDayEndTime) {
                // Use the later of the two times
                $expectedTime = $userDayEndTime->greaterThan($boxDayEndTime) ? $userDayEndTime : $boxDayEndTime;
            } elseif ($userDayEndTime) {
                $expectedTime = $userDayEndTime;
            } elseif ($boxDayEndTime) {
                $expectedTime = $boxDayEndTime;
            } else {
                // Fallback: check-in + 10 hours (default work day)
                $expectedTime = $checkInTime->copy()->addHours(10);
            }

            // Add 2 hours buffer after day end time (default when shift not assigned)
            $expectedTime->addHours(2);

            Log::info("Expected punch-out calculated from day_end_time (shift not assigned)", [
                'box_user_id'        => $boxUser->id,
                'shift_assigned'     => false,
                'user_day_end_time'  => $boxUser->day_end_time ?? 'N/A',
                'box_day_end_time'   => $box->day_end_time ?? 'N/A',
                'check_in'           => $attendance->check_in,
                'day_end_time_used'  => $expectedTime->copy()->subHours(2)->format('H:i:s'),
                'expected_punch_out' => $expectedTime->format('Y-m-d H:i:s'),
            ]);

            return $expectedTime;
        } catch (\Exception $e) {
            Log::error("Error calculating expected punch-out time: " . $e->getMessage(), [
                'box_user_id'   => $boxUser->id,
                'box_id'        => $box->id,
                'attendance_id' => $attendance->id,
                'error_trace'   => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Convert time string (H:i:s) to minutes
     *
     * @param string $time
     * @return int
     */
    private function timeToMinutes(string $time): int
    {
        try {
            $parts   = explode(':', $time);
            $hours   = (int) ($parts[0] ?? 0);
            $minutes = (int) ($parts[1] ?? 0);
            return ($hours * 60) + $minutes;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     *
     *
     * @param Attendance $attendance
     * @param string|null
     * @return array|null
     */
    private function calculateWorkingHours(Attendance $attendance, ?string $checkOutTime = null): ?array
    {
        try {
            if (! $attendance->check_in) {
                return null;
            }
            $checkOut = $checkOutTime ?? $attendance->check_out;
            if (! $checkOut) {
                return null;
            }

            $date       = Carbon::parse($attendance->attendance_date);
            $checkInDT  = Carbon::parse($date->format('Y-m-d') . ' ' . $attendance->check_in);
            $checkOutDT = Carbon::parse($date->format('Y-m-d') . ' ' . $checkOut);
            if ($checkOutDT->lt($checkInDT)) {
                $checkOutDT->addDay();
            }

            $workedSeconds = $checkInDT->diffInSeconds($checkOutDT);
            $workingHours  = gmdate('H:i:s', $workedSeconds);

            $breakSeconds = 0;
            if ($attendance->break_duration) {
                $breakParts   = explode(':', $attendance->break_duration);
                $breakHours   = (int) ($breakParts[0] ?? 0);
                $breakMinutes = (int) ($breakParts[1] ?? 0);
                $breakSecs    = (int) ($breakParts[2] ?? 0);
                $breakSeconds = ($breakHours * 3600) + ($breakMinutes * 60) + $breakSecs;
            }

            $netSeconds      = max(0, $workedSeconds - $breakSeconds);
            $productionHours = gmdate('H:i:s', $netSeconds);

            return [
                'working_hours'    => $workingHours,
                'production_hours' => $productionHours,
            ];
        } catch (\Exception $e) {
            Log::error("Error calculating working hours: " . $e->getMessage(), [
                'attendance_id' => $attendance->id,
            ]);
            return null;
        }
    }
}
