<?php
namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Box;
use App\Models\BoxUser;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MarkAllUsersAbsentCommand extends Command
{
    protected $signature   = 'attendance:mark-absent';
    protected $description = 'Cron: Mark Absent / Weekly Off / Holiday respecting cutoff times';

    protected string $date;

    public function handle(): int
    {
        $this->date = Carbon::today()->toDateString();
        $today      = Carbon::today();

        $this->info("Starting attendance marking cron for date {$this->date}");
        Log::info("Starting attendance marking cron for date {$this->date}");

        $totalBoxesProcessed = 0;
        $totalUsersProcessed = 0;

        // Get all enabled boxes ordered by ID (for consistent processing)
        $boxes = Box::where('status', 'Enabled')->orderBy('id')->get();

        foreach ($boxes as $box) {
            $this->info("Processing Box ID {$box->id} ({$box->name})");
            Log::info("Processing Box ID {$box->id} ({$box->name})");

            $processedUsersCount = $this->processBoxFully($box, $today);

            $totalUsersProcessed += $processedUsersCount;
            $totalBoxesProcessed++;
        }

        $this->info("Attendance marking completed: Processed {$totalBoxesProcessed} boxes and {$totalUsersProcessed} users.");
        Log::info("Attendance marking completed: Processed {$totalBoxesProcessed} boxes and {$totalUsersProcessed} users.");

        return Command::SUCCESS;
    }

    /**
     * Process all users for a single box in date sequence.
     */
    protected function processBoxFully(Box $box, Carbon $today): int
    {
        if (! $box->attendace_module_date) {
            Log::info("Skipping Box ID {$box->id} - No attendance module date set.");
            return 0;
        }

        $moduleStartDate = Carbon::parse($box->attendace_module_date)->startOfDay();

        // Get all enabled box users for this box ordered by ID
        $boxUsers = BoxUser::where('box_id', $box->id)
            ->whereNotNull('user_id')
            ->where('status', 'Enabled')
            ->with(['user', 'shift', 'box'])
            ->orderBy('id')
            ->get();

        if ($boxUsers->isEmpty()) {
            return 0;
        }

        // Loop through dates first, then users, to ensure the ID sequence (12, 13, 14...) per date
        for ($date = $moduleStartDate->copy(); $date->lessThanOrEqualTo($today); $date->addDay()) {
            $this->info("Checking attendance for all users on date: {$date->toDateString()}");
            foreach ($boxUsers as $boxUser) {
                $this->processBoxUserForDate($boxUser, $date, $moduleStartDate);
            }
        }

        return $boxUsers->count();
    }

    /**
     * Process attendance for a single BoxUser on a specific date.
     */
    protected function processBoxUserForDate(BoxUser $boxUser, Carbon $date, Carbon $moduleStartDate): void
    {
        $user = $boxUser->user;
        if (! $user) {
            return;
        }

        // 1. Joining date check
        $joiningDate = $boxUser->joining_date
            ? Carbon::parse($boxUser->joining_date)->startOfDay()
            : $moduleStartDate;

        // Skip if date is before user's joining date or box's module start date
        if ($date->lt($joiningDate) || $date->lt($moduleStartDate)) {
            return;
        }

        // 2. Already marked check
        if ($this->alreadyMarked($boxUser, $date)) {
            return;
        }

        $dayName = $date->format('l');
        $status  = $this->determineAttendanceStatus($boxUser, $dayName, $date);

        if (! $status) {
            return;
        }

        // 3. Cutoff check for today
        if ($date->isToday()) {
            $cutoffTime = $this->calculateCutoffTime($boxUser);
            if ($cutoffTime && now()->lessThanOrEqualTo($cutoffTime)) {
                return;
            }
        }

        // 4. Mark Attendance
        $this->markAttendance($boxUser, $status, $date);
    }

    /**
     * Check if attendance is already marked.
     */
    protected function alreadyMarked(BoxUser $boxUser, Carbon $date): bool
    {
        return Attendance::where('box_user_id', $boxUser->id)
            ->whereDate('attendance_date', $date)
            ->exists();
    }

    /**
     * Calculate cutoff time for marking attendance.
     */
    protected function calculateCutoffTime(BoxUser $boxUser): ?Carbon
    {
        $box   = $boxUser->box;
        $shift = $boxUser->shift;

        $startTimeStr = null;

        if ($shift && ! empty($shift->in_time)) {
            $startTimeStr = $shift->in_time;
        } elseif (! empty($box?->day_start_time)) {
            $startTimeStr = $box->day_start_time;
        }

        if (! $startTimeStr) {
            return null;
        }

        $startTime = $this->parseTime($startTimeStr);
        if (! $startTime) {
            return null;
        }

        $relaxationMinutes = $boxUser->relaxation_time ?? $box?->relaxation_time ?? 10;

        return $startTime->copy()->addMinutes((int) $relaxationMinutes);
    }

    /**
     * Determine attendance status for user on a given day.
     */
    protected function determineAttendanceStatus(BoxUser $boxUser, string $dayName, Carbon $date): ?string
    {
        $user  = $boxUser->user;
        $shift = $boxUser->shift;
        $box   = $boxUser->box;

        $normalizedDay = $this->normalizeDay($dayName);

        if ($this->isHoliday($boxUser, $date)) {
            Log::info("{$user->name} | {$date->toDateString()} is Holiday");
            return 'Holiday';
        }

        if ($shift) {
            $weeklyOffs = array_filter([
                $this->normalizeDay($shift->weekly_off_1 ?? ''),
                $this->normalizeDay($shift->weekly_off_2 ?? ''),
            ]);

            if (in_array($normalizedDay, $weeklyOffs)) {
                Log::info("{$user->name} | {$date->toDateString()} is Weekly Off (Shift)");
                return 'Weekly Off';
            }

            return 'Absent';
        }

        $workingDays = $box?->working_days ?? [];
        if (empty($workingDays)) {
            Log::info("{$user->name} | {$date->toDateString()} No working days defined, marking Weekly Off.");
            return 'Weekly Off';
        }

        $normalizedWorkingDays = collect($workingDays)
            ->map(fn($d) => $this->normalizeDay($d))
            ->unique()
            ->toArray();

        if (! in_array($normalizedDay, $normalizedWorkingDays)) {
            Log::info("{$user->name} | {$date->toDateString()} is Weekly Off (Box)");
            return 'Weekly Off';
        }

        return 'Absent';
    }

    /**
     * Check if a date is holiday for the box.
     */
    protected function isHoliday(BoxUser $boxUser, Carbon $date): bool
    {
        return Holiday::where('box_id', $boxUser->box_id)
            ->whereDate('date', $date)
            ->exists();
    }

    /**
     * Mark attendance record.
     */
    protected function markAttendance(BoxUser $boxUser, string $status, Carbon $date): void
    {
        Attendance::create([
            'box_user_id'        => $boxUser->id,
            'box_id'             => $boxUser->box_id,
            'created_by'         => $boxUser->user_id,
            'updated_by'         => $boxUser->user_id,
            'attendance_date'    => $date->toDateString(),
            'status'             => $status,
            'check_in'           => null,
            'check_out'          => null,
            'break_duration'     => null,
            'late_duration'      => null,
            'production_hours'   => null,
            'over_time'          => null,
            'check_attendace_by' => 'N/A',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        Log::info("Marked attendance '{$status}' for {$boxUser->user->name} on {$date->toDateString()}");
    }

    /**
     * Parse time string to Carbon.
     */
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

    /**
     * Normalize day string.
     */
    protected function normalizeDay(?string $day): string
    {
        return ucfirst(strtolower(trim($day ?? '')));
    }
}
