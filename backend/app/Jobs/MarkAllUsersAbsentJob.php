<?php
namespace App\Jobs;

use App\Models\Attendance;
use App\Models\BoxUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class MarkAllUsersAbsentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $boxId;
    protected $date;

    public function __construct($boxId, $date)
    {
        $this->boxId = $boxId;
        $this->date  = Carbon::parse($date)->toDateString();
    }

    public function middleware()
    {
        return [
            new WithoutOverlapping(),
            new ThrottlesExceptions(5, 1),
        ];
    }

    public function handle()
    {
        Log::info("Starting MarkAllUsersAbsentJob for box_id: {$this->boxId} on date: {$this->date}");

        $boxUsers = $this->getBoxUsers();

        foreach ($boxUsers as $boxUser) {
            $this->processBoxUser($boxUser);
        }

        Log::info("MarkAllUsersAbsentJob completed for box_id: {$this->boxId} on date: {$this->date}");
    }

    protected function getBoxUsers()
    {
        return BoxUser::where('box_id', $this->boxId)
            ->whereNotNull('user_id')
            ->with(['user', 'shift', 'box'])
            ->get();
    }

    protected function processBoxUser(BoxUser $boxUser)
    {
        $user = $boxUser->user;
        if (! $user) {
            Log::warning("Skipping BoxUser ID {$boxUser->id} - No assigned user.");
            return;
        }

        $now     = Carbon::now();
        $dayName = Carbon::parse($this->date)->format('l');

        $cutoffTime = $this->calculateCutoffTime($boxUser);
        if (! $cutoffTime) {
            Log::warning("Skipping {$user->name} (BoxUser ID {$boxUser->id}) - No valid start times found.");
            return;
        }

        // Check if current time is after the cutoff time for marking absent
        if ($now->lessThanOrEqualTo($cutoffTime)) {
            Log::info("Skipping {$user->name} - Current time is before cutoff.");
            return;
        }

        $status = $this->determineAttendanceStatus($boxUser, $dayName);
        if (! $status) {
            return;
        }

        if ($this->alreadyMarked($boxUser)) {
            Log::info("Attendance already exists for {$user->name} on {$this->date}");
            return;
        }

        $this->markAbsent($boxUser, $status);
    }

    // protected function calculateCutoffTime(BoxUser $boxUser): ?Carbon
    // {
    //     $box   = $boxUser->box;
    //     $shift = $boxUser->shift;

    //     // Priority: 1. Shift Time, 2. User specific Time, 3. Box default Time
    //     $startTimeStr = null;
    //     if ($shift && !empty($shift->in_time)) {
    //         $startTimeStr = $shift->in_time;
    //     } elseif (!$boxUser->user_box_timing && !empty($boxUser->day_start_time)) {
    //         $startTimeStr = $boxUser->day_start_time;
    //     } else {
    //         $startTimeStr = $box?->day_start_time;
    //     }

    //     if (empty($startTimeStr)) {
    //         return null;
    //     }

    //     $effectiveStartTime = $this->parseTime($startTimeStr);
    //     if (!$effectiveStartTime) {
    //         return null;
    //     }

    //     // Relaxation time priority: User specific, then Box default, then fallback to 10
    //     $relaxationMinutes = $boxUser->relaxation_time
    //         ?? $box?->relaxation_time
    //         ?? 10;

    //     return $effectiveStartTime->copy()->addMinutes((int)$relaxationMinutes);
    // }

    protected function calculateCutoffTime(BoxUser $boxUser): ?Carbon
    {
        $box   = $boxUser->box;
        $shift = $boxUser->shift;

        // 1️⃣ Shift assigned → use shift time
        if ($shift && ! empty($shift->in_time)) {
            $startTimeStr = $shift->in_time;
        }
        // 2️⃣ No shift → use box time ONLY
        elseif (! empty($box?->day_start_time)) {
            $startTimeStr = $box->day_start_time;
        } else {
            return null;
        }

        $startTime = $this->parseTime($startTimeStr);
        if (! $startTime) {
            return null;
        }

        $relaxationMinutes = $boxUser->relaxation_time ?? $box?->relaxation_time ?? 10;

        return $startTime->copy()->addMinutes((int) $relaxationMinutes);
    }

    // protected function determineAttendanceStatus(BoxUser $boxUser, string $dayName): ?string
    // {
    //     $user  = $boxUser->user;
    //     $shift = $boxUser->shift;
    //     $box   = $boxUser->box;
    //     $normalizedDay = $this->normalizeDay($dayName);
    //     if ($shift) {
    //         $weeklyOff1 = $this->normalizeDay($shift->weekly_off_1 ?? '');
    //         $weeklyOff2 = $this->normalizeDay($shift->weekly_off_2 ?? '');
    //         $weeklyOffs = array_filter([$weeklyOff1, $weeklyOff2]);

    //         Log::debug("[SHIFT] {$user->name} | Weekly Offs: " . json_encode($weeklyOffs) . " | Today: {$normalizedDay}");

    //         if (in_array($normalizedDay, $weeklyOffs)) {
    //             Log::info("Skipping {$user->name} - Today ({$normalizedDay}) is a weekly off (via shift).");
    //             return 'Weekly Off';
    //         }
    //         return 'Absent';
    //     }
    //     $workingDays = (!$boxUser->user_box_timing && !empty($boxUser->working_days))
    //         ? $boxUser->working_days
    //         : ($box?->working_days ?? []);
    //     if (empty($workingDays)) {
    //         Log::info("[BOX/USER] {$user->name} - No working days set. Assuming weekly off.");
    //         return 'Weekly Off';
    //     }

    //     // Ensure workingDays is a normalized array of days
    //     $flatWorkingDays = collect($workingDays)
    //         ->flatMap(function($value) {
    //             // Handle cases where day strings might be comma-separated or stored differently
    //             return is_string($value) ? explode(',', $value) : [$value];
    //         })
    //         ->map(fn($day) => $this->normalizeDay($day))
    //         ->filter()
    //         ->unique()
    //         ->values()
    //         ->toArray();

    //     Log::debug("[BOX/USER] {$user->name} | Working Days: " . json_encode($flatWorkingDays) . " | Today: {$normalizedDay}");

    //     if (in_array($normalizedDay, $flatWorkingDays)) {
    //         return 'Absent';
    //     }

    //     Log::info("Skipping {$user->name} - Today ({$normalizedDay}) is not a working day.");
    //     return 'Weekly Off';
    // }

    protected function determineAttendanceStatus(BoxUser $boxUser, string $dayName): ?string
    {
        $user  = $boxUser->user;
        $shift = $boxUser->shift;
        $box   = $boxUser->box;

        $today = $this->normalizeDay($dayName);

        /**
         * 1️⃣ SHIFT HAS HIGHEST PRIORITY
         */
        if ($shift) {
            $weeklyOffs = array_filter([
                $this->normalizeDay($shift->weekly_off_1 ?? ''),
                $this->normalizeDay($shift->weekly_off_2 ?? ''),
            ]);

            Log::debug("[SHIFT] {$user->name} | Weekly Offs: " . json_encode($weeklyOffs));

            if (in_array($today, $weeklyOffs)) {
                return 'Weekly Off';
            }

            return 'Absent';
        }

        /**
         * 2️⃣ NO SHIFT → USE BOX WORKING DAYS
         */
        $workingDays = $box?->working_days ?? [];

        if (empty($workingDays)) {
            Log::info("[BOX] {$user->name} - No working days defined → Weekly Off");
            return 'Weekly Off';
        }

        $normalizedWorkingDays = collect($workingDays)
            ->map(fn($day) => $this->normalizeDay($day))
            ->unique()
            ->toArray();
        Log::debug("[BOX] {$user->name} | Working Days: " . json_encode($normalizedWorkingDays));
        if (! in_array($today, $normalizedWorkingDays)) {
            return 'Weekly Off';
        }
        return 'Absent';
    }

    protected function alreadyMarked(BoxUser $boxUser): bool
    {
        return Attendance::where('box_user_id', $boxUser->id)
            ->whereDate('attendance_date', $this->date)
            ->exists();
    }

    protected function markAbsent(BoxUser $boxUser, string $status)
    {
        Attendance::create([
            'box_user_id'        => $boxUser->id,
            'box_id'             => $boxUser->box_id,
            'created_by'         => $boxUser->user_id,
            'updated_by'         => $boxUser->user_id,
            'attendance_date'    => $this->date,
            'status'             => $status,
            'check_in'           => null,
            'check_attendace_by' => 'N/A',
            'check_out'          => null,
            'break_duration'     => null,
            'late_duration'      => null,
            'production_hours'   => null,
            'over_time'          => null,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        Log::info("Marked {$status} for {$boxUser->user->name} (BoxUser ID {$boxUser->id}) on {$this->date}");
    }

    protected function parseTime(?string $time): ?Carbon
    {
        if (empty($time)) {
            return null;
        }

        $time = trim($time);

        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            $time .= ':00';
        }

        try {
            return Carbon::parse("{$this->date} {$time}", config('app.timezone'));
        } catch (\Exception $e) {
            Log::error("Invalid time format: '{$time}' - " . $e->getMessage());
            return null;
        }
    }

    protected function normalizeDay(?string $day): string
    {
        return ucfirst(strtolower(trim($day ?? '')));
    }
}
