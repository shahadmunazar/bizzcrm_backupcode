<?php
namespace App\Jobs;

use App\Models\BulkUpload;
use App\Models\Lead;
use App\Exports\BulkUploadFullReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class GenerateBulkUploadReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $bulkUploadId;
    public int $boxId;
    public int $requestedBy;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run before timing out.
     */
    public int $timeout = 3600; // 1 hour timeout

    /**
     * Indicate if the job should be marked as failed on timeout.
     */
    public bool $failOnTimeout = false;

    /**
     * Create a new job instance.
     */
    public function __construct(int $bulkUploadId, int $boxId, int $requestedBy)
    {
        $this->bulkUploadId = $bulkUploadId;
        $this->boxId        = $boxId;
        $this->requestedBy  = $requestedBy;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Outer try-catch to ensure queue worker never stops, even on unexpected errors
        try {
        $bulkUpload = BulkUpload::find($this->bulkUploadId);

        if (! $bulkUpload) {
            Log::warning('GenerateBulkUploadReportJob: bulk upload not found', ['bulk_upload_id' => $this->bulkUploadId]);
            return;
        }

        $bulkUpload->update([
            'report_status'       => 'processing',
            'report_error'        => null,
            'report_requested_at' => $bulkUpload->report_requested_at ?? now(),
            'report_requested_by' => $this->requestedBy,
            'report_progress'     => 0,
            'report_file_path'    => null,
            'report_generated_at' => null,
        ]);

        try {
            if ($bulkUpload->report_file_path && Storage::disk('public')->exists($bulkUpload->report_file_path)) {
                Storage::disk('public')->delete($bulkUpload->report_file_path);
            }

                   // Save report inside a folder per bulk upload id
                   $reportDir = 'bulk_reports/' . $bulkUpload->id;
                $disk      = Storage::disk('public');
            if (! $disk->exists($reportDir)) {
                $disk->makeDirectory($reportDir);
            }

            $fileName = 'bulk_upload_' . $bulkUpload->id . '_report_' . now()->format('Ymd_His') . '.xlsx';
            $filePath = $reportDir . '/' . $fileName;

                // Get headers and mappings from bulk upload
                $mappings = $bulkUpload->mappings ?? [];

                // Extract headers from mappings (keys are CSV headers)
                $headers = !empty($mappings) ? array_keys($mappings) : [];

                // If no mappings, try to get from original file
                if (empty($headers)) {
                    $originalFilePath = storage_path('app/public/' . $bulkUpload->file_path);
                    if (file_exists($originalFilePath)) {
                        $fileExtension = strtolower(pathinfo($bulkUpload->file_name, PATHINFO_EXTENSION));
                        if ($fileExtension === 'csv') {
                            if (($handle = fopen($originalFilePath, 'r')) !== false) {
                                $firstBytes = fread($handle, 3);
                                if ($firstBytes !== "\xEF\xBB\xBF") {
                                    rewind($handle);
                                }
                                $csvHeaders = fgetcsv($handle, 0, ',', '"', '\\');
                                if ($csvHeaders !== false && is_array($csvHeaders)) {
                                    $headers = array_map(function ($header) {
                                        $header = str_replace("\xEF\xBB\xBF", '', (string) $header);
                                        $header = trim($header);
                                        $header = preg_replace('/[\x00-\x1F\x7F]/', '', $header);
                                        return $header;
                                    }, $csvHeaders);
                                }
                                fclose($handle);
                            }
                        }
                    }
                }

                // Fallback headers if still empty
                if (empty($headers)) {
                    $headers = ['Name', 'Email', 'Phone', 'Company'];
                }

                // Sanitize headers
                $headers = array_map(function ($header) {
                    return $header !== null ? trim((string) $header) : '';
                }, $headers);
                $headers = array_filter($headers, function ($h) {
                    return $h !== '';
                });
                $headers = array_values($headers);

                if (empty($headers)) {
                    $headers = ['Data'];
                }

                // Calculate statistics for summary (case-insensitive to handle any variations)
                $totalLeads = Lead::where('bulk_upload_id', $bulkUpload->id)
                ->where('box_id', $this->boxId)
                ->count();
                $processedTotal = Lead::where('bulk_upload_id', $bulkUpload->id)
                    ->where('box_id', $this->boxId)
                    ->whereRaw("LOWER(TRIM(process_uploading)) = ?", ['success'])
                    ->count();
                $duplicateTotal = Lead::where('bulk_upload_id', $bulkUpload->id)
                    ->where('box_id', $this->boxId)
                    ->whereRaw("LOWER(TRIM(process_uploading)) = ?", ['duplicate'])
                    ->count();
                $skippedTotal = Lead::where('bulk_upload_id', $bulkUpload->id)
                    ->where('box_id', $this->boxId)
                    ->whereRaw("LOWER(TRIM(process_uploading)) = ?", ['skipped'])
                    ->count();

                // Build summary data
                $summary = [
                    ['Bulk Upload Report Summary'],
                    [],
                    ['Bulk Upload ID:', $bulkUpload->id],
                    ['File Name:', $bulkUpload->file_name ?? 'N/A'],
                    ['Upload Date:', $bulkUpload->created_at ? $bulkUpload->created_at->format('Y-m-d H:i:s') : 'N/A'],
                    [],
                    ['Total Leads:', $totalLeads],
                    ['Successfully Processed:', $processedTotal, $totalLeads > 0 ? round(($processedTotal / $totalLeads) * 100, 2) . '%' : '0%'],
                    ['Duplicates Found:', $duplicateTotal, $totalLeads > 0 ? round(($duplicateTotal / $totalLeads) * 100, 2) . '%' : '0%'],
                    ['Skipped:', $skippedTotal, $totalLeads > 0 ? round(($skippedTotal / $totalLeads) * 100, 2) . '%' : '0%'],
                    [],
                    ['Categorization Rules:'],
                    ['Processed:', 'Leads successfully created with process_uploading = Success'],
                    ['Duplicate:', 'Leads with process_uploading = Duplicate'],
                    ['Skipped:', 'Leads with process_uploading = Skipped'],
                ];

                // Add field mappings if available
                if (!empty($mappings)) {
                    $summary[] = [];
                    $summary[] = ['Field Mappings:'];
                    $summary[] = ['CSV Column', 'Mapped To'];
                    foreach ($mappings as $csvColumn => $mappedField) {
                        $summary[] = [$csvColumn, is_array($mappedField) ? json_encode($mappedField) : $mappedField];
                    }
                }

                // Update progress to 20%
                BulkUpload::where('id', $bulkUpload->id)->update(['report_progress' => 20]);

                // Use Laravel-Excel with streaming for memory-efficient export
                Log::info('Starting Laravel-Excel export with streaming', [
                    'bulk_upload_id' => $bulkUpload->id,
                    'file_path' => $filePath,
                ]);

                // Create the export instance
                $export = new BulkUploadFullReport(
                    $bulkUpload->id,
                    $this->boxId,
                    $headers,
                    $mappings,
                    $summary
                );

                // Store the file using Laravel-Excel (streaming, memory-efficient)
                Excel::store($export, $filePath, 'public');

                // Update progress to 90%
                BulkUpload::where('id', $bulkUpload->id)->update(['report_progress' => 90]);

                // Verify file was created
                $fullPath = storage_path('app/public/' . $filePath);
                if (!file_exists($fullPath)) {
                    Log::error('File was not created after Laravel-Excel export', [
                        'file_path' => $fullPath,
                        'bulk_upload_id' => $this->bulkUploadId,
                    ]);
                    $bulkUpload->update([
                        'report_status' => 'failed',
                        'report_error' => 'File was not created after export',
                        'report_progress' => 0,
                    ]);
                    return;
                }

                $fileSize = filesize($fullPath);
                if ($fileSize === 0) {
                    Log::error('File is empty after Laravel-Excel export', [
                        'file_path' => $fullPath,
                        'bulk_upload_id' => $this->bulkUploadId,
                    ]);
                $bulkUpload->update([
                        'report_status' => 'failed',
                        'report_error' => 'File is empty after export',
                        'report_progress' => 0,
                ]);
                return;
            }

                Log::info('Laravel-Excel export completed successfully', [
                    'file_path' => $filePath,
                    'file_size' => $fileSize,
                    'file_size_kb' => round($fileSize / 1024, 2),
                ]);

                // Force garbage collection
                gc_collect_cycles();

                // Close only the current database connection, not all connections
                // This prevents connection pool exhaustion without breaking the queue worker
                try {
                    DB::connection()->disconnect();
                } catch (\Exception $e) {
                    // Ignore disconnect errors
                }

                // Generate complete URL for the report file
                $reportFileUrl = url(Storage::disk('public')->url($filePath));

                $bulkUpload->update([
                    'report_status'       => 'completed',
                    'report_file_path'    => $filePath,
                    'report_file_url'     => $reportFileUrl,
                    'report_generated_at' => now(),
                    'report_error'        => null,
                    'report_progress'     => 100,
                ]);

                Log::info('GenerateBulkUploadReportJob completed successfully', [
                    'bulk_upload_id' => $this->bulkUploadId,
                ]);
            } catch (\Exception $e) {
                Log::error('Error in GenerateBulkUploadReportJob', [
                    'bulk_upload_id' => $this->bulkUploadId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

            $bulkUpload->update([
                    'report_status' => 'failed',
                    'report_error' => $e->getMessage(),
                    'report_progress' => 0,
            ]);
                return;
            }
        } catch (\Throwable $th) {
            Log::error('GenerateBulkUploadReportJob failed', [
                'bulk_upload_id' => $this->bulkUploadId,
                'message'        => $th->getMessage(),
                'trace'          => $th->getTraceAsString(),
            ]);

            try {
                // Close only the current database connection
                try {
                    DB::connection()->disconnect();
                } catch (\Exception $e) {
                    // Ignore disconnect errors
                }

                // Force garbage collection
                gc_collect_cycles();
            } catch (\Exception $e) {
                // Ignore cleanup errors
            }

            // Update bulk upload status
            try {
                $bulkUpload = BulkUpload::find($this->bulkUploadId);
                if ($bulkUpload) {
            $bulkUpload->update([
                        'report_status'   => 'failed',
                        'report_error'    => $th->getMessage(),
                'report_progress' => 0,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to update bulk upload status after error', [
                    'bulk_upload_id' => $this->bulkUploadId,
                    'error'          => $e->getMessage(),
                ]);
            }

            // Don't re-throw - let Laravel's queue system handle the failure
            // Re-throwing can cause the queue worker to stop
            // The job will be marked as failed automatically by Laravel
        } finally {
            // CRITICAL: Always restore database connection for next job
            // This prevents the queue worker from stopping
            try {
                // Check if connection exists and is alive
                $connection = DB::connection();
                try {
                    $pdo = $connection->getPdo();
                    if (!$pdo) {
                        DB::reconnect();
                    }
                } catch (\Exception $e) {
                    // Connection is dead, reconnect
                    DB::reconnect();
                }
            } catch (\Exception $e) {
                // If reconnect fails, try to establish a fresh connection
                try {
                    DB::purge();
                    DB::reconnect();
                } catch (\Exception $e2) {
                    // Last resort - log but don't throw
                    Log::warning('Could not restore database connection in finally block', [
                        'error' => $e2->getMessage(),
                        'bulk_upload_id' => $this->bulkUploadId,
                    ]);
                }
            }

            // Force garbage collection to free memory
            gc_collect_cycles();

            // Log job completion (success or failure)
            Log::info('GenerateBulkUploadReportJob finished (finally block)', [
                'bulk_upload_id' => $this->bulkUploadId,
            ]);
        }
    }

    /**
     * Handle a job failure.
     * This method is called when the job fails, allowing us to clean up without stopping the worker.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateBulkUploadReportJob failed permanently', [
            'bulk_upload_id' => $this->bulkUploadId,
            'message'        => $exception->getMessage(),
        ]);

        try {
            $bulkUpload = BulkUpload::find($this->bulkUploadId);
            if ($bulkUpload) {
                $bulkUpload->update([
                    'report_status'   => 'failed',
                    'report_error'    => $exception->getMessage(),
                    'report_progress' => 0,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update bulk upload status in failed() method', [
                'bulk_upload_id' => $this->bulkUploadId,
                'error'          => $e->getMessage(),
            ]);
        }
    }
}


