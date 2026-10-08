<?php

namespace App\Jobs;

use App\Exports\AttendanceAdminExport;
use App\Exports\AttendanceEmployeeExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ExportAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $boxName;
    public string $fileName;
    public string $type;
    public int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $boxName, string $fileName, string $type, int $userId)
    {
        $this->boxName = $boxName;
        $this->fileName = $fileName;
        $this->type = $type;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $safeBoxName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $this->boxName);
        $directory = "exports/attendance/{$safeBoxName}";
        $path = "{$directory}/{$this->fileName}";
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }
        if ($this->type == 'employee') {
            Excel::store(new AttendanceEmployeeExport($safeBoxName, $this->userId), $path, 'public');
        } else {
            Excel::store(new AttendanceAdminExport($safeBoxName), $path, 'public');
        }
    }
}
