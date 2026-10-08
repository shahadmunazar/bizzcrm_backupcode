<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Http\Controllers\Employee\AttendaceController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class UpdateAttendanceStatuses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $attendanceIds;

    /**
     * Create a new job instance.
     *
     * @param array $attendanceIds
     */
    public function __construct(array $attendanceIds)
    {
        $this->attendanceIds = $attendanceIds;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $controller = new AttendaceController();
        Attendance::with(['boxUser', 'boxUser.shift', 'boxUser.box'])
            ->whereIn('id', $this->attendanceIds)
            ->whereNull('status_updated_at')
            ->chunkById(100, function ($attendances) use ($controller) {
                foreach ($attendances as $attendance) {
                    $newStatus = $controller->calculateStatus($attendance);
                    if (strtolower($attendance->status) !== strtolower($newStatus)) {
                        $attendance->status = $newStatus;
                        $attendance->status_updated_at = Carbon::now();
                        $attendance->save();
                    } elseif (is_null($attendance->status_updated_at)) {
                        $attendance->status_updated_at = Carbon::now();
                        $attendance->save();
                    }
                }
            });
    }
}
