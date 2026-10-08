<?php

namespace App\Jobs;

use App\Exports\LeadsExport;
use App\Mail\ExportMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class ExportLeadsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $fileName;
    public string $directory;
    public int $boxId;
    public array $filters;
    public int $userId;

    public function __construct(string $directory, string $fileName, int $boxId, array $filters = [], int $userId = 0)
    {
        $this->directory = $directory;
        $this->fileName = $fileName;
        $this->boxId = $boxId;
        $this->filters = $filters;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        try {
            if (!Storage::disk('public')->exists($this->directory)) {
                Storage::disk('public')->makeDirectory($this->directory);
            }

            // Force CSV extension for memory-efficient streaming
            $csvFileName = str_replace('.xlsx', '.csv', $this->fileName);
            $filePathRelative = "{$this->directory}/{$csvFileName}";
            $fullPath = Storage::disk('public')->path($filePathRelative);

            $export = new LeadsExport($this->boxId, $this->filters);
            $query = $export->query();
            $count = $query->count();
            
            // Open file for writing
            $handle = fopen($fullPath, 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fputs($handle, "\xEF\xBB\xBF");
            
            // Write Headings
            fputcsv($handle, $export->headings());
            
            // Chunk and stream data to file
            $query->chunk(1000, function($leads) use ($handle, $export) {
                foreach ($leads as $lead) {
                    $row = $export->map($lead);
                    fputcsv($handle, $row);
                }
            });
            
            fclose($handle);
            
            // Send Mail
            if ($this->userId) {
                $user = User::find($this->userId);
                if ($user && $user->email) {
                    try {
                        Mail::to($user->email)->send(new ExportMail($fullPath, $count));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Export Email Fetch Failed: ' . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Export Job Failed: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
        }
    }
}
