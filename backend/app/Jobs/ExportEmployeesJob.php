<?php

namespace App\Jobs;

use App\Exports\EmployeesExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ExportEmployeesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $fileName;
    public string $directory;
    public int $boxId;

    public function __construct(string $directory, string $fileName, int $boxId)
    {
        $this->directory = $directory;
        $this->fileName = $fileName;
        $this->boxId = $boxId;
    }

    public function handle(): void
    {
        if (!Storage::disk('public')->exists($this->directory)) {
            Storage::disk('public')->makeDirectory($this->directory);
        }

        $filePath = "{$this->directory}/{$this->fileName}";

        Excel::store(new EmployeesExport($this->boxId), $filePath, 'public');
    }
}
