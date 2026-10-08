<?php

namespace App\Services;

use App\Models\{Attendance, BoxUser, Holiday, LeaveType, LeaveTransaction};
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Log;

class AttendanceCalculationService
{
    private $holidayCache = [];

    /**
     * Determine if a date is a weekly off for a given box user
     */
    public function isWeeklyOff($boxUser, $date)
    {
        if (!$boxUser) {
            return false;
        }

        $dayName = ucfirst(strtolower(Carbon::parse($date)->format('l')));

        if (isset($boxUser->shift) && $boxUser->shift) {
            $shift         = $boxUser->shift;
            $weeklyOffDays = array_filter([
                $shift->weekly_off_1 ?? null,
                $shift->weekly_off_2 ?? null,
                $shift->weekly_off_3 ?? null,
            ]);
            if (in_array($dayName, $weeklyOffDays, true)) {
                return true;
            }
        }

        if (!empty($boxUser->working_days)) {
            $userWorkingDays = is_array($boxUser->working_days)
                ? $boxUser->working_days
                : explode(',', $boxUser->working_days);
            $userWorkingDays = array_map('trim', $userWorkingDays);
            return !in_array($dayName, $userWorkingDays, true);
        }

        if ($boxUser->box && !empty($boxUser->box->working_days)) {
            $boxWorkingDays = is_array($boxUser->box->working_days)
                ? $boxUser->box->working_days
                : explode(',', $boxUser->box->working_days);
            $boxWorkingDays = array_map('trim', $boxWorkingDays);
            return !in_array($dayName, $boxWorkingDays, true);
        }

        return false;
    }

    /**
     * Calculate detailed status based on production hours, shift rules, weekly offs, and holidays.
     */
    public function calculateDetailedStatus($attendance, $boxUser, $date, $boxId)
    {
        // Step 1: Check holidays first (highest priority)
        $cacheKey = "holiday_{$boxId}_{$date}";
        if (!isset($this->holidayCache[$cacheKey])) {
            $this->holidayCache[$cacheKey] = Holiday::with('holidayType')
                ->where('box_id', $boxId)
                ->whereDate('date', $date)
                ->get()
                ->filter(function ($h) {
                    if ($h->holidayType) {
                        return strcasecmp($h->holidayType->calculate_in_calendar, 'Yes') === 0;
                    }
                    return true;
                })->first();
        }
        $holiday = $this->holidayCache[$cacheKey];
        $isHoliday = (bool)$holiday;

        if ($isHoliday) {
            $holidayType = $holiday->holiday_type;

            if (!$holidayType && $holiday->holidayType) {
                $holidayType = $holiday->holidayType->name;
            }

            if (strtoupper($holidayType) !== 'RH' && strtoupper($holidayType) !== 'RESTRICTED HOLIDAY') {
                return 'Holiday';
            }
        }

        // Step 2: Check weekly off
        $isWeeklyOff = $this->isWeeklyOff($boxUser, $date);

        // Step 3: Check if user has shift assigned
        $shift = $boxUser->shift ?? null;
        $hasShift = $shift && isset($shift->shift_enabled) && $shift->shift_enabled;

        // Step 4: Handle users WITHOUT shift assignment
        if (!$hasShift) {
            $attendanceDate = Carbon::parse($date);
            $dayName = ucfirst(strtolower($attendanceDate->format('l')));

            if ($isWeeklyOff) {
                return 'Weekly Off';
            }

            if ($attendance && $attendance->check_in && $attendance->check_out) {
                return 'Present'; 
            }

            $isWorkingDay = false;

            if (!empty($boxUser->working_days)) {
                $userWorkingDays = is_array($boxUser->working_days)
                    ? $boxUser->working_days
                    : explode(',', $boxUser->working_days);
                $userWorkingDays = array_map('trim', $userWorkingDays);
                $isWorkingDay = in_array($dayName, $userWorkingDays, true);
            }
            elseif ($boxUser->box && !empty($boxUser->box->working_days)) {
                $boxWorkingDays = is_array($boxUser->box->working_days)
                    ? $boxUser->box->working_days
                    : explode(',', $boxUser->box->working_days);
                $boxWorkingDays = array_map('trim', $boxWorkingDays);
                $isWorkingDay = in_array($dayName, $boxWorkingDays, true);
            }

            if (!$isWorkingDay) {
                return 'Weekly Off'; 
            }

            if ($attendance && $attendance->check_in && !$attendance->check_out) {
                if ($attendanceDate->isToday()) {
                    return 'Present'; 
                }
                return 'Punch Out Miss'; 
            }

            return 'Absent';
        }

        // Step 5: Handle users WITH shift assignment
        if ($isWeeklyOff) {
            return 'Weekly Off';
        }

        if (!$attendance) {
            return 'Absent';
        }

        if (!$attendance->check_out && !Carbon::parse($date)->isToday()) {
            return 'Punch Out Miss';
        }

        $productionHours = $attendance->production_hours;

        if (!$productionHours) {
            if ($attendance->check_in && $attendance->check_out) {
                return 'Present'; 
            }
            return 'Present'; 
        }

        // Calculate status based on production hours against shift rules
        if ($shift) {
            try {
                $productionSeconds      = CarbonInterval::createFromFormat('H:i:s', $productionHours)->totalSeconds;
                $absentBelowSeconds     = CarbonInterval::createFromFormat('H:i:s', $shift->absent_below ?? '00:00:00')->totalSeconds;
                $halfDayBelowSeconds    = CarbonInterval::createFromFormat('H:i:s', $shift->half_day_below ?? '00:00:00')->totalSeconds;
                $shortLeaveBelowSeconds = CarbonInterval::createFromFormat('H:i:s', $shift->short_leave_below ?? '00:00:00')->totalSeconds;
                $shiftHoursSeconds      = CarbonInterval::createFromFormat('H:i:s', $shift->shift_hours ?? '00:00:00')->totalSeconds;
                $relaxationSeconds      = CarbonInterval::createFromFormat('H:i:s', $shift->relaxation_time ?? '00:00:00')->totalSeconds;

                if ($productionSeconds >= $shiftHoursSeconds) {
                    return 'Present';
                }
                if ($productionSeconds <= $absentBelowSeconds) {
                    return 'Absent';
                }
                if ($productionSeconds <= $halfDayBelowSeconds) {
                    return 'Half Day';
                }
                $adjustedShortLeaveThreshold = max(0, $shortLeaveBelowSeconds - $relaxationSeconds);
                if ($productionSeconds <= $adjustedShortLeaveThreshold) {
                    return 'Short Leave';
                }

                return 'Present';
            } catch (\Exception $e) {
                Log::warning("⚠️ Error calculating status from production hours: " . $e->getMessage());
                return 'Present'; 
            }
        }

        return 'Present';
    }

    /**
     * Handle Comp Off credit when user works on Holiday or Weekly Off
     */
    public function handleCompOffCredit($attendance, $boxUser, $date)
    {
        $productionHours = $attendance->production_hours;
        if (!$productionHours) return;

        try {
            $productionSeconds = CarbonInterval::createFromFormat('H:i:s', $productionHours)->totalSeconds;
            $shift = $boxUser->shift;
            $creditAmount = 0;

            if ($shift) {
                $absentBelowSeconds     = $shift->absent_below ? CarbonInterval::createFromFormat('H:i:s', $shift->absent_below)->totalSeconds : 0;
                $halfDayBelowSeconds    = $shift->half_day_below ? CarbonInterval::createFromFormat('H:i:s', $shift->half_day_below)->totalSeconds : 0;
                $shortLeaveBelowSeconds = $shift->short_leave_below ? CarbonInterval::createFromFormat('H:i:s', $shift->short_leave_below)->totalSeconds : 0;
                $shiftHoursSeconds      = $shift->shift_hours ? CarbonInterval::createFromFormat('H:i:s', $shift->shift_hours)->totalSeconds : 0;
                $relaxationSeconds      = ($shift->relaxation_time ?? '00:00:00') ? CarbonInterval::createFromFormat('H:i:s', $shift->relaxation_time ?? '00:00:00')->totalSeconds : 0;
                
                $adjustedShortLeaveThreshold = max(0, $shortLeaveBelowSeconds - $relaxationSeconds);
                
                if ($productionSeconds >= $shiftHoursSeconds) {
                    $creditAmount = 1.0;
                } elseif ($productionSeconds < $absentBelowSeconds) {
                    $creditAmount = 0;
                } elseif ($productionSeconds < $halfDayBelowSeconds) {
                    $deduction = (float)($shift->half_day_deduction ?? 50); 
                    $creditAmount = max(0, 1.0 - ($deduction / 100));
                } elseif ($productionSeconds < $shortLeaveBelowSeconds) {
                    $deduction = (float)($shift->short_leave_deduction ?? 25); 
                    $creditAmount = max(0, 1.0 - ($deduction / 100));
                } else {
                    $creditAmount = 1.0;
                }
            } else {
                if ($productionSeconds >= 8 * 3600) {
                    $creditAmount = 1.0;
                } elseif ($productionSeconds >= 4 * 3600) {
                    $creditAmount = 0.5;
                }
            }

            if ($creditAmount > 0) {
                $compOffType = LeaveType::where('status', 'Active')
                    ->where('box_id', $boxUser->box_id)
                    ->where(function($q) {
                        $q->where('leave_type', 'Comp Off')
                          ->orWhere('leave_type', 'Compensatory Off Leave')
                          ->orWhere('leave_type', 'like', 'Comp%Off%');
                    })
                    ->first();

                if ($compOffType) {
                    $alreadyCredited = LeaveTransaction::where('box_user_id', $boxUser->id)
                        ->where('leave_type_id', $compOffType->id)
                        ->whereDate('date', $date)
                        ->exists();

                    if (!$alreadyCredited) {
                        $latestTxn = LeaveTransaction::where('box_user_id', $boxUser->id)
                            ->where('leave_type_id', $compOffType->id)
                            ->latest()
                            ->first();

                        $previousBalance = $latestTxn->balance_leave ?? 0;
                        
                        $isWeeklyOff = $this->isWeeklyOff($boxUser, $date);
                        $dayName = Carbon::parse($date)->format('l');
                        LeaveTransaction::create([
                            'box_id'              => $boxUser->box_id,
                            'box_user_id'         => $boxUser->id,
                            'leave_type_id'       => $compOffType->id,
                            'date'                => $date,
                            'credit_leave'        => $creditAmount,
                            'balance_leave'       => $previousBalance + $creditAmount,
                            'total_leave'         => $creditAmount,
                            'transaction_remarks' => 'Comp Off earned (' . $creditAmount . ') for working on ' .
                            ($isWeeklyOff ? 'Weekly Off' : 'Holiday') . ' (' . $dayName . ')',
                            'status'              => 'Approved',
                            'created_by'          => null,
                            'applied_date'        => $date,
                            'approved_date'       => now(),
                        ]);
                        Log::info("🎁 Comp Off Credit Added: {$creditAmount} for {$date} for BoxUser {$boxUser->id}");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Error calculating Comp Off credit: " . $e->getMessage());
        }
    }
}
