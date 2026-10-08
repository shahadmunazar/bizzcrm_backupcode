<?php

namespace App\Jobs;

use App\Exports\ReceiptsExport;
use App\Models\Accounts\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ExportReceiptsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filters;
    protected $format;
    protected $fileName;
    protected $userId;

    public function __construct($filters, $format, $fileName, $userId)
    {
        $this->filters = $filters;
        $this->format = $format;
        $this->fileName = $fileName;
        $this->userId = $userId;
    }

    public function handle()
    {
        $filePath = 'exports/' . $this->fileName;

        if ($this->format === 'excel') {
            Excel::store(new ReceiptsExport($this->filters), $filePath, 'public');
        } elseif ($this->format === 'pdf') {
            // Re-run the query logic for PDF (or reuse Export class if possible, but PDF usually needs a view)
            $export = new ReceiptsExport($this->filters);
            $receipts = $export->query()->get();
            
            $pdf = Pdf::loadView('accounts.receipts_pdf', compact('receipts'));
            Storage::disk('public')->put($filePath, $pdf->output());
        }

        // Here you could send a notification to the user or update a 'downloads' table
        // For now we just log it
        \Log::info("Export completed: " . $this->fileName . " for user " . $this->userId);
    }
}
