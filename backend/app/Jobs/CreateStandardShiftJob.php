<?php

namespace App\Jobs;

use App\Models\Box;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateStandardShiftJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Optional Box ID to create standard shift for.
     *
     * @var int|null
     */
    public $boxId;

    /**
     * Create a new job instance.
     *
     * @param int|null $boxId
     */
    public function __construct($boxId = null)
    {
        $this->boxId = $boxId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $boxes = $this->boxId
            ? Box::where('id', $this->boxId)->get()
            : Box::all();

        if ($boxes->isEmpty()) {
            Log::info("CreateStandardShiftJob: No Box found for processing.");
            return;
        }

        foreach ($boxes as $box) {
            $existingShift = Shift::where('box_id', $box->id)
                ->where('shift_name', 'Standard Shift')
                ->first();

            if (!$existingShift) {
                $inTime = '09:30:00';
                $outTime = '18:30:00';

                // Calculate shift hours
                $in = Carbon::parse($inTime);
                $out = Carbon::parse($outTime);

                // Handle overnight shifts
                if ($out->lessThan($in)) {
                    $out->addDay();
                }

                $shiftHours = $in->diff($out)->format('%H:%I');

                // 1. Calculate lunch times (45 mins, 4 hours after shift starts)
                $lunchOut = $in->copy()->addHours(4);
                $lunchIn = $lunchOut->copy()->addMinutes(45);
                $lunchHours = '00:45';

                // 2. Short Break 1 (Morning Tea - 15 mins, 2 hours after shift starts)
                $shortBreakOut = $in->copy()->addHours(2);
                $shortBreakIn = $shortBreakOut->copy()->addMinutes(15);
                $shortBreakHours = '00:15';

                // 3. Short Break 2 (Evening Tea - 15 mins, 6.5 hours after shift starts)
                $shortBreakOut2 = $in->copy()->addHours(6)->addMinutes(30);
                $shortBreakIn2 = $shortBreakOut2->copy()->addMinutes(15);
                $shortBreakHours2 = '00:15';

                // Calculate half day times
                $halfDayHours = '04:30';
                $halfDayEnd = $in->copy()->addHours(4)->addMinutes(30);

                Shift::create([
                    'box_id'          => $box->id,
                    'code'            => 'STD-' . str_pad($box->id, 4, '0', STR_PAD_LEFT),
                    'shift_name'      => 'Standard Shift',
                    'in_time'         => $in->format('H:i:s'),
                    'out_time'        => $out->format('H:i:s'),
                    'shift_hours'     => $shiftHours,
                    'shift_enabled'   => true,
                        
                    // Break settings
                    'rest_enabled'    => true,
                    'lunch_out'       => $lunchOut->format('H:i:s'),
                    'lunch_in'        => $lunchIn->format('H:i:s'),
                    'lunch_hours'     => $lunchHours,
                    'short_break_enabled' => true,
                    'short_break_out' => $shortBreakOut->format('H:i:s'),
                    'short_break_in'  => $shortBreakIn->format('H:i:s'),
                    'short_break_hours'=> $shortBreakHours,
                    'short_break_enabled_2' => true,
                    'short_break_out_2' => $shortBreakOut2->format('H:i:s'),
                    'short_break_in_2' => $shortBreakIn2->format('H:i:s'),
                    'short_break_hours_2'=> $shortBreakHours2,
                    
                    // Half day / rules
                    'half_day_enabled'    => true,
                    'half_day_start_time' => $in->format('H:i:s'),
                    'half_day_end_time'   => $halfDayEnd->format('H:i:s'),
                    'half_day_hours'      => $halfDayHours,
                    'absent_below'        => '02:00',
                    'relaxation_time'     => '00:15:00',
                    'half_day_below'      => '04:30',
                    'short_leave_below'   => '08:00',
                    'max_relaxations_allowed' => 3,
                    'overtime_allowed'    => true,
                    'absent_deduction'    => 1.0,
                    'half_day_deduction'  => 0.5,
                    'short_leave_deduction'=> 0.25,
                    
                    // Weekly offs (Standard IT: Sat & Sun)
                    'weekly_off_1'    => 'Sunday',
                    'weekly_off_2'    => 'Saturday',
                    'weekly_off_3'    => null,
                    'weekly_off_4'    => null,
                    'saturday_offs'   => [],
                    'saturday_half_days' => [],
                    
                    // Additional configuration
                    'check_over_time' => true,
                    'early_login_punchIn' => true,
                    'early_login_minutes' => '01:00:00',
                    'max_punch_in_allowed' => 4,
                    'auto_punchout_time' => '23:59:00',
                    'all_in_out_calculation' => true,
                    'first_in_last_out_calculation' => false,
                    'half_day_time' => '04:30:00',

                    'start_from_date' => now()->toDateString(),
                    'end_date'        => now()->addYears(10)->toDateString(),
                    'created_by'      => $box->superadmin_id ?? 1,
                ]);

                Log::info("CreateStandardShiftJob: Successfully created Standard Shift for Box ID: {$box->id}");
            }
        }
    }
}
