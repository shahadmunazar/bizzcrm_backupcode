<?php
namespace App\Jobs;

use App\Models\BoxCustomField;
use App\Models\BulkUpload;
use App\Models\Lead;
use App\Models\SystemField;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProcessBulkUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $filePath;
    protected array $mappings; // Format: ['csv_column' => 'system_field_id']
    protected bool $createDuplicate;
    protected bool $assignToEnabled;
    protected bool $statusEnabled;
    protected int $boxId;
    protected int $userId;
    protected ?int $bulkUploadId = null;
    protected ?string $uniqueField = null;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run before timing out.
     */
    public int $timeout = 7200; // 2 hours timeout for large uploads

    /**
     * Indicate if the job should be marked as failed on timeout.
     */
    public bool $failOnTimeout = false;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, array $mappings, bool $createDuplicate, int $boxId, int $userId, bool $assignToEnabled = false, bool $statusEnabled = false)
    {
        $this->filePath        = $filePath;
        $this->mappings        = $mappings;
        $this->createDuplicate = $createDuplicate;
        $this->assignToEnabled = $assignToEnabled;
        $this->statusEnabled   = $statusEnabled;
        $this->boxId           = $boxId;
        $this->userId          = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Log job start
        Log::info("ProcessBulkUploadJob started", [
            'file_path' => $this->filePath,
            'box_id'    => $this->boxId,
            'user_id'   => $this->userId,
        ]);
        
        $box = \App\Models\Box::find($this->boxId);
        $this->uniqueField = $box->lead_required_field ?? 'email';

        // Find bulk upload record - try multiple ways
        // Normalize file path (remove leading/trailing slashes for comparison)
        $normalizedPath  = trim($this->filePath, '/');
        $normalizedPath2 = ltrim($this->filePath, '/');
        $normalizedPath3 = $this->filePath;

        $bulkUpload = BulkUpload::where(function ($query) use ($normalizedPath, $normalizedPath2, $normalizedPath3) {
            $query->where('file_path', $normalizedPath)
                ->orWhere('file_path', $normalizedPath2)
                ->orWhere('file_path', $normalizedPath3)
                ->orWhere('file_path', '/' . $normalizedPath)
                ->orWhere('file_path', '/' . $normalizedPath2);
        })
            ->where('box_id', $this->boxId)
            ->where('user_id', $this->userId)
            ->first();

        // If not found, try without user_id (in case of mismatch)
        if (! $bulkUpload) {
            $bulkUpload = BulkUpload::where(function ($query) use ($normalizedPath, $normalizedPath2, $normalizedPath3) {
                $query->where('file_path', $normalizedPath)
                    ->orWhere('file_path', $normalizedPath2)
                    ->orWhere('file_path', $normalizedPath3)
                    ->orWhere('file_path', '/' . $normalizedPath)
                    ->orWhere('file_path', '/' . $normalizedPath2);
            })
                ->where('box_id', $this->boxId)
                ->latest()
                ->first();
        }

        // If still not found, try by file name only (most recent)
        if (! $bulkUpload) {
            $fileName   = basename($this->filePath);
            $bulkUpload = BulkUpload::where('file_name', $fileName)
                ->where('box_id', $this->boxId)
                ->latest()
                ->first();
        }

        // Store bulk upload ID for use in lead insertion
        if ($bulkUpload) {
            $this->bulkUploadId = $bulkUpload->id;
            Log::info("Bulk upload record found", ['bulk_upload_id' => $this->bulkUploadId]);
        } else {
            Log::warning("Bulk upload record not found", [
                'file_path' => $this->filePath,
                'box_id'    => $this->boxId,
                'user_id'   => $this->userId,
            ]);
        }

        $stats = [
            'total'          => 0,
            'processed'      => 0, // Rows processed so far
            'successful'     => 0,
            'skipped'        => 0,
            'duplicates'     => 0,
            'percentage'     => 0,
            'status'         => 'processing', // processing, completed, failed
            'errors'         => [],
            'bulk_upload_id' => $this->bulkUploadId,
        ];

        // Update cache immediately to show job has started (even if record not found)
        $cacheKey = "bulk_upload_stats_{$this->boxId}_" . md5($this->filePath);
        cache()->put($cacheKey, $stats, 3600);

        // Also try alternative cache keys in case file path format differs
        $altCacheKey1 = "bulk_upload_stats_{$this->boxId}_" . md5(ltrim($this->filePath, '/'));
        $altCacheKey2 = "bulk_upload_stats_{$this->boxId}_" . md5(trim($this->filePath, '/'));
        cache()->put($altCacheKey1, $stats, 3600);
        cache()->put($altCacheKey2, $stats, 3600);

        try {
            $fullPath = storage_path('app/public/' . $this->filePath);

            // Update bulk upload record - processing started
            if ($bulkUpload) {
                $bulkUpload->update([
                    'status'     => 'processing',
                    'started_at' => now(),
                ]);
                Log::info("Bulk upload record updated to processing", ['bulk_upload_id' => $this->bulkUploadId]);
            }

            if (! file_exists($fullPath)) {
                Log::error("Bulk upload file not found: $fullPath");
                return;
            }

            $fileExtension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

            // Standard lead fields that go to leads table (28 system fields + legacy fields)
            $standardLeadFields = [
                // 28 System Fields (actual DB columns only)
                'name', 'lead_name', 'email', 'sec_email', 'phone', 'phone_type',
                'lead_type', 'company_name', 'designation', 'amount', 'currency',
                'lead_source_id', 'lead_status_id', 'industry', 'assigned_to',
                'country_id', 'state_id', 'city_id', 'address', 'postal_code',
                'location', 'tags', 'description', 'matured_date', 'visibility', 'status',
                'created_at', 'updated_at', 'last_updated_by',

                // Legacy/additional fields (for backward compatibility)
                'mobile', 'query', 'ip_address', 'drug_license', 'gstin', 'experience',
                'is_matured', 'is_paid', 'gstin_number', 'pan_number', 'company_id', 'last_name',
            ];

            // Get all system fields and custom fields for mapping
            // Note: mappings are in format ['csv_column' => 'field_name'] where field_name is either:
            // - SystemField.field/field_name for system fields
            // - BoxCustomField.col_name for custom fields

            $systemFields = SystemField::where('box_id', $this->boxId)
                ->where('object', 'leads')
                ->get();

            $customFields = BoxCustomField::where('box_id', $this->boxId)
                ->where('object', 'leads')
                ->get();

            // Build field mapping: field_name => [type, db_column, name, is_standard, is_custom]
            // field_name is what comes from the mapping (SystemField.field/field_name or BoxCustomField.col_name)
            $fieldMapping = [];

            // Create a map of custom field IDs to col_names for quick lookup
            $customFieldMap = $customFields->keyBy('id')->map(function ($cf) {
                return $cf->col_name;
            });

            // Map system fields by field name (this is the value sent from frontend)
            foreach ($systemFields as $sf) {
                // Check if this system field is actually a custom field (has field_refernce_key)
                $isCustomField = ! empty($sf->field_refernce_key);

                if ($isCustomField && isset($customFieldMap[$sf->field_refernce_key])) {
                    // This is a custom field - use the col_name from BoxCustomField as the key
                    // Frontend sends col_name for custom fields
                    $customFieldColName                = $customFieldMap[$sf->field_refernce_key];
                    $fieldMapping[$customFieldColName] = [
                        'type'        => 'custom',
                        'db_column'   => $customFieldColName, // Column name in data_{box_id} table
                        'name'        => $sf->name,           // Display name
                        'is_standard' => false,
                        'is_custom'   => true,
                    ];
                } else {
                    // This is a real system field - goes to leads table
                    // Frontend sends field or field_name for system fields
                    $fieldName = $sf->field ?: $sf->field_name;

                    // Map system field names to actual database columns
                    $dbColumn = $this->mapFieldToDbColumn($fieldName);

                    $isStandard               = in_array($dbColumn, $standardLeadFields);
                    $fieldMapping[$fieldName] = [
                        'type'        => 'system',
                        'db_column'   => $dbColumn, // Actual database column name in leads table
                        'name'        => $sf->name, // Display name
                        'is_standard' => $isStandard,
                        'is_custom'   => false,
                    ];
                }
            }

            // Map custom fields by col_name (direct custom fields)
            foreach ($customFields as $cf) {
                // Only add if not already added via SystemField mapping
                if (! isset($fieldMapping[$cf->col_name])) {
                    $fieldMapping[$cf->col_name] = [
                        'type'        => 'custom',
                        'db_column'   => $cf->col_name, // Column name in data_{box_id} table
                        'name'        => $cf->name,     // Display name
                        'is_standard' => false,
                        'is_custom'   => true,
                    ];
                }
            }

            $tableName       = 'data_' . $this->boxId;
            $chunkSize       = 100; // Process 100 rows at a time (reduced for memory efficiency)
            $leadsChunk      = [];
            $customDataChunk = [];

            // First, count total rows to calculate percentage
            $totalRows = 0;
            if (in_array($fileExtension, ['xlsx', 'xls'])) {
                // For Excel, count rows
                $spreadsheet = IOFactory::load($fullPath);
                $worksheet   = $spreadsheet->getActiveSheet();
                $highestRow  = $worksheet->getHighestRow();
                $totalRows   = $highestRow - 1; // Subtract header row
            } else {
                // For CSV, count rows
                if (($handle = fopen($fullPath, 'r')) !== false) {
                    $rowCount = 0;
                    while (($row = fgetcsv($handle)) !== false) {
                        $rowCount++;
                    }
                    fclose($handle);
                    $totalRows = $rowCount - 1; // Subtract header row
                }
            }

            $stats['total']          = $totalRows;
            $stats['status']         = 'processing';
            $stats['started_at']     = now()->toDateTimeString();
            $stats['bulk_upload_id'] = $this->bulkUploadId;

            // Update cache immediately with total rows (multiple keys to ensure it's found)
            $cacheKey     = "bulk_upload_stats_{$this->boxId}_" . md5($this->filePath);
            $altCacheKey1 = "bulk_upload_stats_{$this->boxId}_" . md5(ltrim($this->filePath, '/'));
            $altCacheKey2 = "bulk_upload_stats_{$this->boxId}_" . md5(trim($this->filePath, '/'));
            cache()->put($cacheKey, $stats, 3600);
            cache()->put($altCacheKey1, $stats, 3600);
            cache()->put($altCacheKey2, $stats, 3600);

            Log::info("Cache updated with total rows", [
                'total_rows' => $totalRows,
                'file_path'  => $this->filePath,
                'cache_key'  => $cacheKey,
            ]);

            // Update database record with total rows immediately
            if ($bulkUpload) {
                $bulkUpload->update([
                    'total_rows' => $totalRows,
                    'status'     => 'processing',
                    'started_at' => now(),
                ]);
                Log::info("Bulk upload total rows updated", [
                    'bulk_upload_id' => $this->bulkUploadId,
                    'total_rows'     => $totalRows,
                ]);
            } else {
                Log::warning("Cannot update bulk upload record - record not found", [
                    'file_path' => $this->filePath,
                    'box_id'    => $this->boxId,
                ]);
            }

            // Check for cancellation flag
            $cancelKey = "bulk_upload_cancel_{$this->boxId}_" . md5($this->filePath);
            if (cache()->get($cancelKey)) {
                $stats['status']       = 'cancelled';
                $stats['cancelled_at'] = now()->toDateTimeString();
                $this->storeStatistics($stats);
                Log::info("Bulk upload cancelled before processing");
                return;
            }

            // Debug: Log mappings and field mapping structure (limited to prevent memory issues)
            Log::info("Bulk upload started", [
                'box_id'              => $this->boxId,
                'total_rows'          => $totalRows,
                'mappings_count'      => count($this->mappings),
                'mappings_sample'     => array_slice($this->mappings, 0, 5, true), // Only first 5
                'system_fields_count' => $systemFields->count(),
                'custom_fields_count' => $customFields->count(),
                'field_mapping_keys'  => array_keys(array_slice($fieldMapping, 0, 10, true)), // Only keys, first 10
            ]);

            // Store initial statistics (job started) - update from pending to processing
            $this->storeStatistics($stats);

            if (in_array($fileExtension, ['xlsx', 'xls'])) {
                // Process Excel file
                $spreadsheet        = IOFactory::load($fullPath);
                $worksheet          = $spreadsheet->getActiveSheet();
                $highestRow         = $worksheet->getHighestRow();
                $highestColumn      = $worksheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                // Get headers from first row
                $headers = [];
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $cellValue = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
                    if ($cellValue !== null && trim($cellValue) !== '') {
                        $headers[] = trim($cellValue);
                    }
                }

                // Process data rows (starting from row 2)
                for ($row = 2; $row <= $highestRow; $row++) {
                    $stats['processed']++;
                    try {
                        $rowData = [];

                        // Get row data
                        for ($col = 1; $col <= $highestColumnIndex; $col++) {
                            $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $rowData[] = $cellValue !== null ? trim($cellValue) : '';
                        }

                        $result = $this->processRow($rowData, $headers, $fieldMapping, $customFields, $standardLeadFields, $tableName, $row);

                        if ($result['status'] === 'success') {
                            // Set process_uploading to 'Success' for successful leads
                            if (isset($result['leadData'])) {
                                $result['leadData']['process_uploading'] = 'Success';
                            }
                            $leadsChunk[] = $result['leadData'];
                            // Always add custom data entry (even if empty) to maintain index alignment
                            $customDataChunk[] = $result['customData'] ?? [];
                            $stats['successful']++;
                        } elseif ($result['status'] === 'duplicate') {
                            // Always add duplicate to leads table with process_uploading = 'Duplicate'
                            if (isset($result['leadData']) && $result['leadData'] !== null) {
                                $result['leadData']['process_uploading'] = 'Duplicate';
                                $leadsChunk[] = $result['leadData'];
                                $customDataChunk[] = $result['customData'] ?? [];
                            }
                            $stats['duplicates']++;
                        } else {
                            // Add skipped rows to leads table with process_uploading = 'Skipped'
                            if (isset($result['leadData'])) {
                                $result['leadData']['process_uploading'] = 'Skipped';
                                $leadsChunk[] = $result['leadData'];
                                $customDataChunk[] = $result['customData'] ?? [];
                            }
                            $stats['skipped']++;
                            $errorMsg          = "Row $row: " . ($result['message'] ?? 'Unknown error');
                            if (count($stats['errors']) < 50) {
                                $stats['errors'][] = substr($errorMsg, 0, 300);
                            }
                            // Log first few errors for debugging (limited data to prevent memory issues)
                            if ($row <= 3) {
                                Log::warning("Row $row skipped", [
                                    'status'          => $result['status'] ?? 'unknown',
                                    'message'         => substr($result['message'] ?? 'no message', 0, 300),
                                    'leadData_keys'   => isset($result['leadData']) ? array_keys($result['leadData']) : [],
                                    'customData_keys' => isset($result['customData']) ? array_keys($result['customData']) : [],
                                ]);
                            }
                        }

                        // Check for cancellation flag
                        $cancelKey = "bulk_upload_cancel_{$this->boxId}_" . md5($this->filePath);
                        if (cache()->get($cancelKey)) {
                            $stats['status']       = 'cancelled';
                            $stats['cancelled_at'] = now()->toDateTimeString();
                            $this->storeStatistics($stats);
                            Log::info("Bulk upload cancelled at row $row");
                            break; // Exit the loop
                        }

                        // Calculate percentage
                        if ($stats['total'] > 0) {
                            $stats['percentage'] = round(($stats['processed'] / $stats['total']) * 100, 2);
                        }

                        // Update statistics every 50 rows for better real-time progress tracking
                        // Reduced frequency to save memory
                        if ($stats['processed'] % 50 == 0) {
                            $this->storeStatistics($stats);
                            // Periodic garbage collection
                            if ($stats['processed'] % 200 == 0) {
                                gc_collect_cycles();
                            }
                        }

                        if (count($leadsChunk) >= $chunkSize) {
                            try {
                                $chunkResult = $this->insertChunk($leadsChunk, $customDataChunk, $tableName);
                                if (! empty($chunkResult['errors'])) {
                                    if (count($stats['errors']) < 50) {
                                        $stats['errors'] = array_merge($stats['errors'], array_slice($chunkResult['errors'], 0, 50 - count($stats['errors'])));
                                    }
                                }
                            } catch (\Exception $e) {
                                if (count($stats['errors']) < 50) {
                                    $stats['errors'][] = "Chunk insert failed: " . substr($e->getMessage(), 0, 300);
                                }
                                Log::error("Chunk insert error: " . substr($e->getMessage(), 0, 300));
                            }
                            $leadsChunk      = [];
                            $customDataChunk = [];

                            // Force garbage collection after each chunk to free memory
                            gc_collect_cycles();

                            // Update statistics in cache for real-time progress
                            $this->storeStatistics($stats);
                        }
                    } catch (\Exception $e) {
                        $stats['skipped']++;
                        $stats['errors'][] = "Row $row: " . $e->getMessage();
                        Log::error("Bulk upload error at row $row: " . $e->getMessage());
                    }
                }
            } else {
                // Process CSV file
                if (($handle = fopen($fullPath, 'r')) !== false) {
                    $headers   = fgetcsv($handle); // Get header row
                    $rowNumber = 1;

                    while (($row = fgetcsv($handle)) !== false) {
                        $rowNumber++;
                        $stats['processed']++;
                        try {
                            $result = $this->processRow($row, $headers, $fieldMapping, $customFields, $standardLeadFields, $tableName, $rowNumber);

                            if ($result['status'] === 'success') {
                                // Set process_uploading to 'Success' for successful leads
                                if (isset($result['leadData'])) {
                                    $result['leadData']['process_uploading'] = 'Success';
                                }
                                $leadsChunk[] = $result['leadData'];
                                // Always add custom data entry (even if empty) to maintain index alignment
                                $customDataChunk[] = $result['customData'] ?? [];
                                $stats['successful']++;
                            } elseif ($result['status'] === 'duplicate') {
                                // Always add duplicate to leads table with process_uploading = 'Duplicate'
                                if (isset($result['leadData']) && $result['leadData'] !== null) {
                                    $result['leadData']['process_uploading'] = 'Duplicate';
                                    $leadsChunk[] = $result['leadData'];
                                    $customDataChunk[] = $result['customData'] ?? [];
                                }
                                $stats['duplicates']++;
                            } else {
                                // Add skipped rows to leads table with process_uploading = 'Skipped'
                                if (isset($result['leadData'])) {
                                    $result['leadData']['process_uploading'] = 'Skipped';
                                    $leadsChunk[] = $result['leadData'];
                                    $customDataChunk[] = $result['customData'] ?? [];
                                }
                                $stats['skipped']++;
                                $errorMsg          = "Row $rowNumber: " . ($result['message'] ?? 'Unknown error');
                                if (count($stats['errors']) < 50) {
                                    $stats['errors'][] = substr($errorMsg, 0, 300);
                                }
                                // Log first few errors for debugging (limited data)
                                if ($rowNumber <= 3) {
                                    Log::warning("Row $rowNumber skipped", [
                                        'status'          => $result['status'] ?? 'unknown',
                                        'message'         => substr($result['message'] ?? 'no message', 0, 300),
                                        'leadData_keys'   => isset($result['leadData']) ? array_keys($result['leadData']) : [],
                                        'customData_keys' => isset($result['customData']) ? array_keys($result['customData']) : [],
                                    ]);
                                }
                            }

                            // Check for cancellation flag
                            $cancelKey = "bulk_upload_cancel_{$this->boxId}_" . md5($this->filePath);
                            if (cache()->get($cancelKey)) {
                                $stats['status']       = 'cancelled';
                                $stats['cancelled_at'] = now()->toDateTimeString();
                                $this->storeStatistics($stats);
                                Log::info("Bulk upload cancelled at row $rowNumber");
                                break; // Exit the loop
                            }

                            // Calculate percentage
                            if ($stats['total'] > 0) {
                                $stats['percentage'] = round(($stats['processed'] / $stats['total']) * 100, 2);
                            }

                            // Update statistics every 50 rows for better real-time progress tracking
                            // Reduced frequency to save memory
                            if ($stats['processed'] % 50 == 0) {
                                $this->storeStatistics($stats);
                                // Periodic garbage collection
                                if ($stats['processed'] % 200 == 0) {
                                    gc_collect_cycles();
                                }
                            }

                            // Process chunks when they reach the chunk size
                            if (count($leadsChunk) >= $chunkSize) {
                                try {
                                    $chunkResult = $this->insertChunk($leadsChunk, $customDataChunk, $tableName);
                                    if (! empty($chunkResult['errors'])) {
                                        if (count($stats['errors']) < 50) {
                                            $stats['errors'] = array_merge($stats['errors'], array_slice($chunkResult['errors'], 0, 50 - count($stats['errors'])));
                                        }
                                    }
                                } catch (\Exception $e) {
                                    if (count($stats['errors']) < 50) {
                                        $stats['errors'][] = "Chunk insert failed: " . substr($e->getMessage(), 0, 300);
                                    }
                                    Log::error("Chunk insert error: " . substr($e->getMessage(), 0, 300));
                                }
                                $leadsChunk      = [];
                                $customDataChunk = [];

                                // Force garbage collection after each chunk to free memory
                                gc_collect_cycles();

                                // Update statistics immediately after chunk insert for real-time progress
                                $this->storeStatistics($stats);
                            }
                        } catch (\Exception $e) {
                            $stats['skipped']++;
                            $stats['errors'][] = "Row $rowNumber: " . $e->getMessage();
                            Log::error("Bulk upload error at row $rowNumber: " . $e->getMessage());
                        }
                    }
                    fclose($handle);
                }
            }

            // Insert remaining chunks
            if (! empty($leadsChunk)) {
                try {
                    $chunkResult = $this->insertChunk($leadsChunk, $customDataChunk, $tableName);
                    if (! empty($chunkResult['errors'])) {
                        if (count($stats['errors']) < 50) {
                            $stats['errors'] = array_merge($stats['errors'], array_slice($chunkResult['errors'], 0, 50 - count($stats['errors'])));
                        }
                    }
                } catch (\Exception $e) {
                    if (count($stats['errors']) < 50) {
                        $stats['errors'][] = "Final chunk insert failed: " . substr($e->getMessage(), 0, 300);
                    }
                    Log::error("Final chunk insert error: " . substr($e->getMessage(), 0, 300));
                }
            }

            // Final percentage
            $stats['percentage'] = 100;
            $stats['status']     = 'completed';

            // Log final statistics
            Log::info("Bulk upload completed", [
                'box_id'       => $this->boxId,
                'total'        => $stats['total'],
                'processed'    => $stats['processed'],
                'successful'   => $stats['successful'],
                'skipped'      => $stats['skipped'],
                'duplicates'   => $stats['duplicates'],
                'percentage'   => $stats['percentage'],
                'errors_count' => count($stats['errors']),
            ]);

            // Store final statistics in database or cache for retrieval
            $this->storeStatistics($stats);

            // Close only the current database connection, not all connections
            // This prevents connection pool exhaustion without breaking the queue worker
            try {
                DB::connection()->disconnect();
            } catch (\Exception $e) {
                // Ignore disconnect errors
            }
            gc_collect_cycles();

            Log::info("ProcessBulkUploadJob completed successfully", [
                'box_id' => $this->boxId,
                'bulk_upload_id' => $this->bulkUploadId,
            ]);

        } catch (\Exception $e) {
            $stats['status']   = 'failed';
            $stats['errors'][] = $e->getMessage();
            $this->storeStatistics($stats);
            Log::error("Bulk upload job failed: " . $e->getMessage());

            // Close only the current database connection
            try {
                DB::connection()->disconnect();
                gc_collect_cycles();
            } catch (\Exception $cleanupError) {
                // Ignore cleanup errors
            }

            // Don't re-throw - let Laravel handle the failure
            // Re-throwing can cause the queue worker to stop
        } finally {
            // CRITICAL: Always restore database connection for next job
            try {
                $connection = DB::connection();
                try {
                    $pdo = $connection->getPdo();
                    if (!$pdo) {
                        DB::reconnect();
                    }
                } catch (\Exception $e) {
                    DB::reconnect();
                }
            } catch (\Exception $e) {
                try {
                    DB::purge();
                    DB::reconnect();
                } catch (\Exception $e2) {
                    Log::warning('Could not restore database connection in ProcessBulkUploadJob finally block', [
                        'error' => $e2->getMessage(),
                        'box_id' => $this->boxId,
                    ]);
                }
            }

            // Force garbage collection
            gc_collect_cycles();

            Log::info('ProcessBulkUploadJob finished (finally block)', [
                'box_id' => $this->boxId,
                'bulk_upload_id' => $this->bulkUploadId,
            ]);
        }
    }

    /**
     * Process a single row and return lead data and custom data
     *
     * DATA ISOLATION & BOX SCOPING:
     * - All lookups (source, country, state, city, user, status) are scoped by box_id
     * - Duplicate checking is always scoped by box_id to prevent cross-box conflicts
     * - Users must belong to the current box to be assigned
     * - Sources and statuses are filtered by box_id
     *
     * @param array $rowData Raw row data from Excel/CSV
     * @param array $headers Column headers from the file
     * @param array $fieldMapping Field mapping configuration
     * @param mixed $customFieldsCollection Custom fields for this box
     * @param array $standardLeadFields List of standard lead table columns
     * @param string $tableName Custom data table name (data_{box_id})
     * @param int $rowNumber Row number for error tracking
     * @return array ['status' => 'success|error|duplicate', 'leadData' => [...], 'customData' => [...], 'message' => '...']
     */
    protected function processRow(array $rowData, array $headers, array $fieldMapping, $customFieldsCollection, array $standardLeadFields, string $tableName, int $rowNumber): array
    {
        $leadData   = [];
        $customData = [];

        // Check if mappings exist
        if (empty($this->mappings)) {
            Log::error("No mappings found for row $rowNumber");
            return [
                'status'  => 'error',
                'message' => 'No field mappings configured',
            ];
        }

        // Simple logic: Only process explicitly mapped fields
        // If mapped to system field → add to leads table
        // If mapped to custom field → add to custom data table (only if column exists)
        foreach ($this->mappings as $csvColumn => $fieldName) {
            if (empty($fieldName)) {
                continue; // Skip unmapped columns
            }

            // Find CSV column index
            $columnIndex = false;
            foreach ($headers as $idx => $header) {
                if (strcasecmp(trim($header), trim($csvColumn)) === 0) {
                    $columnIndex = $idx;
                    break;
                }
            }

            if ($columnIndex === false || ! isset($rowData[$columnIndex])) {
                continue; // Column not found, skip
            }

            // Get value from CSV
            $value = $rowData[$columnIndex];
            $value = is_null($value) ? '' : trim((string) $value);

            // Skip empty values (but allow '0')
            if ($value === '' && $value !== '0') {
                continue;
            }

            // Check if field exists in mapping
            if (! isset($fieldMapping[$fieldName])) {
                continue; // Field not in mapping, skip it
            }

            $fieldInfo = $fieldMapping[$fieldName];

            // Route to appropriate table based on field type
            if (isset($fieldInfo['is_custom']) && $fieldInfo['is_custom'] === true) {
                // Custom field → goes to data_{box_id} table
                $dbColumn              = $fieldInfo['db_column'];
                $customData[$dbColumn] = $value;
            } else {
                // System field → goes to leads table
                $dbColumn            = $fieldInfo['db_column'];
                $leadData[$dbColumn] = $value;
            }
        }

        // Debug logging for first few rows to understand mapping issues (limited data)
        if ($rowNumber <= 2) {
            // Show sample mapping to verify data flow
            $sampleMapping = [];
            foreach (array_slice($this->mappings, 0, 3, true) as $csvCol => $fieldId) {
                $sampleMapping[] = "CSV: '$csvCol' → Field: '$fieldId'";
            }

            Log::info("Row $rowNumber processing", [
                'mappings_count'  => count($this->mappings),
                'sample_mappings' => $sampleMapping,
                'headers_count'   => count($headers),
                'rowData_count'   => count($rowData),
                'leadData_keys'   => array_keys($leadData),
                'customData_keys' => array_keys($customData),
                'has_name'        => isset($leadData['name']),
                'name_value'      => isset($leadData['name']) ? substr($leadData['name'], 0, 50) : 'NOT SET',
                'leadData_sample' => array_slice($leadData, 0, 3, true), // First 3 fields
            ]);
        }

        // Validate required fields - check if we have at least name or some data
        $hasName = isset($leadData['name']) && trim($leadData['name']) !== '';
        $hasData = ! empty($leadData) || ! empty($customData);

        if (! $hasName && ! $hasData) {
            // Still return leadData for skipped rows so they can be added to leads table
            return [
                'status'    => 'error',
                'message'   => 'No data to import - missing name field and all other fields are empty',
                'leadData'  => $leadData, // Return leadData so it can be inserted with process_uploading = 'Skipped'
                'customData' => $customData,
            ];
        }

        // If no name but we have other data, try to use first available field as name
        if (! $hasName && ! empty($leadData)) {
            // Try to find a name-like field
            foreach (['lead_name', 'full_name', 'customer_name', 'contact_name'] as $nameField) {
                if (isset($leadData[$nameField]) && ! empty(trim($leadData[$nameField]))) {
                    $leadData['name'] = trim($leadData[$nameField]);
                    unset($leadData[$nameField]);
                    break;
                }
            }

            // If still no name, use first non-empty value
            if (! isset($leadData['name']) || empty($leadData['name'])) {
                foreach ($leadData as $key => $value) {
                    if (! empty(trim($value))) {
                        $leadData['name'] = trim($value);
                        break;
                    }
                }
            }
        }

        // Final check - if still no name, create a default name from available data
        if (! isset($leadData['name']) || trim($leadData['name']) === '') {
            // Try to create name from email or phone
            if (! empty($leadData['email'])) {
                $leadData['name'] = explode('@', $leadData['email'])[0];
            } elseif (! empty($leadData['phone'])) {
                $leadData['name'] = 'Lead ' . $leadData['phone'];
            } elseif (! empty($customData)) {
                // Use first custom field value as name
                $firstCustomValue = reset($customData);
                $leadData['name'] = ! empty($firstCustomValue) ? (string) $firstCustomValue : 'Imported Lead ' . $rowNumber;
            } else {
                // Last resort - use row number
                $leadData['name'] = 'Imported Lead ' . $rowNumber;
            }
        }

        // Convert lead_name to name if present
        if (isset($leadData['lead_name']) && ! empty($leadData['lead_name'])) {
            if (! isset($leadData['name']) || empty($leadData['name'])) {
                $leadData['name'] = trim($leadData['lead_name']);
            }
            unset($leadData['lead_name']);
        }

        // Add required fields
        $leadData['box_id']          = $this->boxId;
        $leadData['added_by']        = $this->userId;
        $leadData['last_updated_by'] = $this->userId;

        // Set defaults for fields that have database defaults
        if (! isset($leadData['country_id'])) {
            $leadData['country_id'] = 101; // Default country ID
        }
        if (! isset($leadData['lead_type'])) {
            $leadData['lead_type'] = 'Direct Lead';
        }
        if (! isset($leadData['created_from'])) {
            $leadData['created_from'] = 'Bulk Upload';
        }
        if (! isset($leadData['is_matured'])) {
            $leadData['is_matured'] = 'No';
        }
        if (! isset($leadData['is_paid'])) {
            $leadData['is_paid'] = '0';
        }

        // Handle boolean/enum fields - convert string values to proper format
        if (isset($leadData['is_matured'])) {
            $isMatured              = strtolower(trim($leadData['is_matured']));
            $leadData['is_matured'] = (in_array($isMatured, ['yes', '1', 'true', 'y'])) ? 'Yes' : 'No';
        }
        if (isset($leadData['is_paid'])) {
            $isPaid              = strtolower(trim($leadData['is_paid']));
            $leadData['is_paid'] = (in_array($isPaid, ['yes', '1', 'true', 'y'])) ? '1' : '0';
        }

        // Ensure numeric fields are properly formatted
        if (isset($leadData['amount'])) {
            $leadData['amount'] = is_numeric($leadData['amount']) ? (float) $leadData['amount'] : null;
        }

        // Handle date fields
        if (isset($leadData['matured_date']) && ! empty($leadData['matured_date'])) {
            try {
                $leadData['matured_date'] = Carbon::parse($leadData['matured_date'])->toDateString();
            } catch (\Exception $e) {
                $leadData['matured_date'] = null;
            }
        }

        // Always check for duplicates to mark them appropriately
        // IMPORTANT: Duplicate check uses the box's lead_required_field
        $email = isset($leadData['email']) && !empty(trim($leadData['email'])) ? strtolower(trim($leadData['email'])) : null;
        $phone = isset($leadData['phone']) && !empty(trim($leadData['phone'])) ? trim($leadData['phone']) : null;
        $secEmail = isset($leadData['sec_email']) && !empty(trim($leadData['sec_email'])) ? strtolower(trim($leadData['sec_email'])) : null;
        $secPhone = isset($leadData['sec_phone']) && !empty(trim($leadData['sec_phone'])) ? trim($leadData['sec_phone']) : null;

        $existingLeads = null;

        if ($email || $phone || $secEmail || $secPhone) {
            $uniqueField = $this->uniqueField ?? 'email';
            
            $canCheckDuplicates = true;
            if ($uniqueField === 'email' && !$email && !$secEmail) {
                $uniqueField = 'fallback';
            } elseif ($uniqueField === 'phone' && !$phone && !$secPhone) {
                $uniqueField = 'fallback';
            }

            if ($canCheckDuplicates) {
                $existingLeads = Lead::where('box_id', $this->boxId)
                ->where(function ($q) use ($email, $phone, $secEmail, $secPhone, $uniqueField) {
                    if ($uniqueField === 'email') {
                        $q->where(function ($inner) use ($email, $secEmail) {
                            if ($email) {
                                $inner->whereRaw('LOWER(TRIM(email)) = ?', [$email])
                                      ->orWhereRaw('LOWER(TRIM(sec_email)) = ?', [$email]);
                            }
                            if ($secEmail) {
                                $inner->orWhereRaw('LOWER(TRIM(email)) = ?', [$secEmail])
                                      ->orWhereRaw('LOWER(TRIM(sec_email)) = ?', [$secEmail]);
                            }
                            if (!$email && !$secEmail) {
                                $inner->whereRaw('1 = 0');
                            }
                        });
                    } elseif ($uniqueField === 'phone') {
                        $q->where(function ($inner) use ($phone, $secPhone) {
                            if ($phone) {
                                $inner->whereRaw('TRIM(phone) = ?', [$phone])
                                      ->orWhereRaw('TRIM(sec_phone) = ?', [$phone])
                                      ->orWhereRaw('TRIM(mobile) = ?', [$phone]);
                            }
                            if ($secPhone) {
                                $inner->orWhereRaw('TRIM(phone) = ?', [$secPhone])
                                      ->orWhereRaw('TRIM(sec_phone) = ?', [$secPhone])
                                      ->orWhereRaw('TRIM(mobile) = ?', [$secPhone]);
                            }
                            if (!$phone && !$secPhone) {
                                $inner->whereRaw('1 = 0');
                            }
                        });
                    } else {
                        // Default fallback / Combined Check
                        $q->where(function ($inner) use ($email, $phone, $secEmail, $secPhone) {
                            $isFirst = true;
                            if ($email) {
                                $inner->whereRaw('LOWER(TRIM(email)) = ?', [$email])
                                      ->orWhereRaw('LOWER(TRIM(sec_email)) = ?', [$email]);
                                $isFirst = false;
                            }
                            if ($secEmail) {
                                if ($isFirst) {
                                    $inner->whereRaw('LOWER(TRIM(email)) = ?', [$secEmail])
                                          ->orWhereRaw('LOWER(TRIM(sec_email)) = ?', [$secEmail]);
                                    $isFirst = false;
                                } else {
                                    $inner->orWhereRaw('LOWER(TRIM(email)) = ?', [$secEmail])
                                          ->orWhereRaw('LOWER(TRIM(sec_email)) = ?', [$secEmail]);
                                }
                            }
                            if ($phone) {
                                if ($isFirst) {
                                    $inner->whereRaw('TRIM(phone) = ?', [$phone])
                                          ->orWhereRaw('TRIM(sec_phone) = ?', [$phone])
                                          ->orWhereRaw('TRIM(mobile) = ?', [$phone]);
                                    $isFirst = false;
                                } else {
                                    $inner->orWhereRaw('TRIM(phone) = ?', [$phone])
                                          ->orWhereRaw('TRIM(sec_phone) = ?', [$phone])
                                          ->orWhereRaw('TRIM(mobile) = ?', [$phone]);
                                }
                            }
                            if ($secPhone) {
                                if ($isFirst) {
                                    $inner->whereRaw('TRIM(phone) = ?', [$secPhone])
                                          ->orWhereRaw('TRIM(sec_phone) = ?', [$secPhone])
                                          ->orWhereRaw('TRIM(mobile) = ?', [$secPhone]);
                                } else {
                                    $inner->orWhereRaw('TRIM(phone) = ?', [$secPhone])
                                          ->orWhereRaw('TRIM(sec_phone) = ?', [$secPhone])
                                          ->orWhereRaw('TRIM(mobile) = ?', [$secPhone]);
                                }
                            }
                            if ($isFirst) { // if nothing was set
                                $inner->whereRaw('1 = 0');
                            }
                        });
                    }
                })
                // Exclude leads from current bulk upload (they're being processed now)
                ->where(function ($query) {
                    if ($this->bulkUploadId) {
                        $query->where('bulk_upload_id', '!=', $this->bulkUploadId)
                            ->orWhereNull('bulk_upload_id');
                    } else {
                        $query->whereNull('bulk_upload_id');
                    }
                })
                ->get();
            }
        }

        if ($existingLeads && $existingLeads->count() > 0) {
            $updatedCount = 0;
            
            foreach ($existingLeads as $existingLead) {
                // Handle updates for existing duplicate leads
                $updateData = [];

                // Update assigned_to if enabled
                if ($this->assignToEnabled) {
                    $assignedToId = null;
                    if (isset($leadData['assigned_to']) && !empty($leadData['assigned_to'])) {
                        if (is_numeric($leadData['assigned_to'])) {
                            $assignedToId = (int) $leadData['assigned_to'];
                        } else {
                            $assignedToId = $this->findUserFromExcel([$leadData['assigned_to']], ['assigned_to'], 'assigned_to');
                        }
                    } else {
                        $ownerColumn  = array_search('assigned_to', $this->mappings) ?: (isset($this->mappings['owner_mapping']) ? $this->mappings['owner_mapping'] : null);
                        $assignedToId = $this->findUserFromExcel($rowData, $headers, $ownerColumn);
                    }
                    
                    if ($assignedToId && $existingLead->assigned_to != $assignedToId) {
                        $updateData['assigned_to'] = $assignedToId;
                        $updateData['assignment_type'] = 'bulk_upload';
                        $updateData['change_assigned_to_time'] = now();
                        $updateData['last_updated_by'] = $this->userId;
                    }
                }

                // Update status if enabled
                if ($this->statusEnabled) {
                    $statusId = null;
                    if (isset($leadData['lead_status_id']) && !empty($leadData['lead_status_id'])) {
                        if (is_numeric($leadData['lead_status_id'])) {
                            $statusId = (int) $leadData['lead_status_id'];
                        } else {
                            $statusId = $this->findStatusFromExcel([$leadData['lead_status_id']], ['lead_status_id'], 'lead_status_id');
                        }
                    } else {
                        $statusColumn = array_search('lead_status_id', $this->mappings) ?: null;
                        $statusId = $this->findStatusFromExcel($rowData, $headers, $statusColumn);
                    }
                    
                    if ($statusId && $existingLead->lead_status_id != $statusId) {
                        $updateData['lead_status_id'] = $statusId;
                        $updateData['change_lead_status_time'] = now();
                        $updateData['last_updated_by'] = $this->userId;
                    }
                }

                // Execute the update
                if (!empty($updateData)) {
                    $oldAssignedTo = $existingLead->assigned_to;
                    $oldStatusId = $existingLead->lead_status_id;
                    
                    $existingLead->update($updateData);
                    $updatedCount++;

                    // Handle Pivot Table and Assignment History if assigned_to changed
                    if (isset($updateData['assigned_to'])) {
                        if ($oldAssignedTo) {
                            $existingLead->assignees()->detach($oldAssignedTo);
                            \App\Models\LeadAssignmentHistory::create([
                                'lead_id' => $existingLead->id,
                                'user_id' => $oldAssignedTo,
                                'assigned_by' => $this->userId,
                                'action' => 'removed',
                            ]);
                        }
                        $existingLead->assignees()->attach($updateData['assigned_to'], ['assigned_by' => $this->userId]);
                        \App\Models\LeadAssignmentHistory::create([
                            'lead_id' => $existingLead->id,
                            'user_id' => $updateData['assigned_to'],
                            'assigned_by' => $this->userId,
                            'action' => 'assigned',
                        ]);

                        // Add Activity log for assignment change
                        $oldUser = \App\Models\User::find($oldAssignedTo);
                        $newUser = \App\Models\User::find($updateData['assigned_to']);
                        
                        \App\Models\Activity::create([
                            'box_id' => $this->boxId,
                            'activity_id' => $existingLead->id,
                            'type' => 'history',
                            'subject' => 'Lead Updated (Bulk Upload Duplicate)',
                            'description' => "Assigned to " . ($newUser->name ?? 'Unassigned'),
                            'activity_date' => now(),
                            'added_by' => $this->userId,
                            'last_updated_by' => $this->userId,
                            'status' => 'Close',
                            'completed_date' => now(),
                            'params' => json_encode([
                                'previous_assignee_name' => $oldUser->name ?? 'Unassigned',
                                'current_assignee_name' => $newUser->name ?? 'Unassigned'
                            ]),
                        ]);
                        
                        // Send Transfer Notification
                        if ($newUser) {
                            \App\Jobs\SendNewLeadNotificationJob::dispatch($existingLead->id, $newUser->id, 'transfer', $this->userId);
                        }
                    }
                    
                    // Handle Status History and Activity Log if status changed
                    if (isset($updateData['lead_status_id'])) {
                        \Illuminate\Support\Facades\DB::table('lead_status_histories')->insert([
                            'box_id'        => $this->boxId,
                            'lead_id'       => $existingLead->id,
                            'old_status_id' => $oldStatusId,
                            'new_status_id' => $updateData['lead_status_id'],
                            'changed_by'    => $this->userId,
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ]);
                        
                        $newStatus = \App\Models\ContactStage::find($updateData['lead_status_id']);
                        \App\Models\Activity::create([
                            'box_id' => $this->boxId,
                            'activity_id' => $existingLead->id,
                            'type' => 'history',
                            'subject' => 'Lead Status Updated (Bulk Upload Duplicate)',
                            'description' => "Status changed to " . ($newStatus->name ?? 'Unknown'),
                            'activity_date' => now(),
                            'added_by' => $this->userId,
                            'last_updated_by' => $this->userId,
                            'status' => 'Close',
                            'completed_date' => now(),
                            'params' => json_encode([
                                'lead_status_id' => $updateData['lead_status_id'],
                                'lead_status_name' => $newStatus->name ?? 'Unknown'
                            ]),
                        ]);
                    }
                }
            }

            // If createDuplicate is true, we still want to insert a new record but mark it as duplicate
            // If createDuplicate is false, we skip the new record insertion (leadData = null)
            if ($this->createDuplicate) {
                $leadData['duplicate_leads'] = 1;
            }

            return [
                'status'     => 'duplicate',
                'message'    => 'Duplicate leads found (' . $existingLeads->count() . ')' . ($updatedCount > 0 ? ' and updated' : ''),
                'leadData'   => $this->createDuplicate ? $leadData : null, 
                'customData' => $this->createDuplicate ? $customData : null,
            ];
        }

        // Handle assign_to for new leads if enabled
        if ($this->assignToEnabled) {
             // If field exists in leadData (mapped system field), check if it's a name and convert to ID
            if (isset($leadData['assigned_to']) && !empty($leadData['assigned_to']) && !is_numeric($leadData['assigned_to'])) {
                // Prepare a mock row/headers for the method if needed, but easier to use direct lookup if possible
                // findUserFromExcel expects row/headers, let's just use it with a simple one-value mapping
                $assignedToId = $this->findUserFromExcel([$leadData['assigned_to']], ['assigned_to'], 'assigned_to');
                if ($assignedToId) {
                    $leadData['assigned_to'] = $assignedToId;
                    $leadData['assignment_type'] = 'bulk_upload';
                } else {
                    unset($leadData['assigned_to']);
                }
            } elseif (isset($leadData['assigned_to']) && is_numeric($leadData['assigned_to'])) {
                $leadData['assignment_type'] = 'bulk_upload';
            }
            
            // If not already set, try to find from Excel columns
            if (! isset($leadData['assigned_to'])) {
                $ownerColumn  = isset($this->mappings['owner_mapping']) ? $this->mappings['owner_mapping'] : null;
                $assignedToId = $this->findUserFromExcel($rowData, $headers, $ownerColumn);
                if ($assignedToId) {
                    $leadData['assigned_to'] = $assignedToId;
                    $leadData['assignment_type'] = 'bulk_upload';
                }
            }
        }

        // Handle status for new leads if enabled
        if ($this->statusEnabled) {
            // If field exists in leadData (mapped system field), check if it's a name and convert to ID
            if (isset($leadData['lead_status_id']) && !empty($leadData['lead_status_id']) && !is_numeric($leadData['lead_status_id'])) {
                $statusId = $this->findStatusFromExcel([$leadData['lead_status_id']], ['lead_status_id'], 'lead_status_id');
                if ($statusId) {
                    $leadData['lead_status_id'] = $statusId;
                } else {
                    unset($leadData['lead_status_id']);
                }
            }

            // If not already set, try to find from Excel columns
            if (! isset($leadData['lead_status_id'])) {
                $statusColumn = isset($this->mappings['status_mapping']) ? $this->mappings['status_mapping'] : null;
                $statusId     = $this->findStatusFromExcel($rowData, $headers, $statusColumn);
                if ($statusId) {
                    $leadData['lead_status_id'] = $statusId;
                }
            }
        }

        // Lookup source ID by name if source is provided as text
        if (isset($leadData['lead_source_id']) && !empty($leadData['lead_source_id']) && !is_numeric($leadData['lead_source_id'])) {
            $sourceId = $this->findSourceFromExcel($leadData['lead_source_id']);
            if ($sourceId) {
                $leadData['lead_source_id'] = $sourceId;
            } else {
                unset($leadData['lead_source_id']); // Remove invalid source
            }
        }

        // If source is not set, try to find it from Excel columns
        if (! isset($leadData['lead_source_id'])) {
            $sourceId = $this->findSourceFromExcelRow($rowData, $headers);
            if ($sourceId) {
                $leadData['lead_source_id'] = $sourceId;
            }
        }

        // Lookup country ID by name if country is provided as text
        if (isset($leadData['country_id']) && !empty($leadData['country_id']) && !is_numeric($leadData['country_id'])) {
            $countryId = $this->findCountryFromExcel($leadData['country_id']);
            if ($countryId) {
                $leadData['country_id'] = $countryId;
            } else {
                $leadData['country_id'] = 101; // Default to India if not found
            }
        }

        // If country is not set, try to find it from Excel columns
        if (! isset($leadData['country_id']) || empty($leadData['country_id'])) {
            $countryId = $this->findCountryFromExcelRow($rowData, $headers);
            if ($countryId) {
                $leadData['country_id'] = $countryId;
            }
        }

        // Lookup state ID by name if state is provided as text
        if (isset($leadData['state_id']) && !empty($leadData['state_id']) && !is_numeric($leadData['state_id'])) {
            $stateId = $this->findStateFromExcel($leadData['state_id'], $leadData['country_id'] ?? null);
            if ($stateId) {
                $leadData['state_id'] = $stateId;
            } else {
                unset($leadData['state_id']); // Remove invalid state
            }
        }

        // If state is not set, try to find it from Excel columns
        if (! isset($leadData['state_id'])) {
            $stateId = $this->findStateFromExcelRow($rowData, $headers, $leadData['country_id'] ?? null);
            if ($stateId) {
                $leadData['state_id'] = $stateId;
            }
        }

        // Lookup city ID by name if city is provided as text
        if (isset($leadData['city_id']) && !empty($leadData['city_id']) && !is_numeric($leadData['city_id'])) {
            $cityId = $this->findCityFromExcel($leadData['city_id'], $leadData['state_id'] ?? null);
            if ($cityId) {
                $leadData['city_id'] = $cityId;
            } else {
                unset($leadData['city_id']); // Remove invalid city
            }
        }

        // If city is not set, try to find it from Excel columns
        if (! isset($leadData['city_id'])) {
            $cityId = $this->findCityFromExcelRow($rowData, $headers, $leadData['state_id'] ?? null);
            if ($cityId) {
                $leadData['city_id'] = $cityId;
            }
        }

        // Lookup industry ID by name if industry is provided as text
        if (isset($leadData['industry']) && !empty($leadData['industry']) && !is_numeric($leadData['industry'])) {
            $industryId = $this->findIndustryFromExcel($leadData['industry']);
            if ($industryId) {
                $leadData['industry'] = $industryId;
            } else {
                unset($leadData['industry']); // Remove invalid industry
            }
        }

        // If industry is not set, try to find it from Excel columns
        if (! isset($leadData['industry'])) {
            $industryId = $this->findIndustryFromExcelRow($rowData, $headers);
            if ($industryId) {
                $leadData['industry'] = $industryId;
            }
        }

        // FINAL STEP: Ensure ID fields are integers or null (after all lookups completed)
        $idFields = ['country_id', 'state_id', 'city_id', 'lead_source_id', 'lead_status_id', 'assigned_to', 'last_activity_id', 'last_note_id', 'industry'];
        foreach ($idFields as $field) {
            if (isset($leadData[$field]) && $leadData[$field] !== null && $leadData[$field] !== '') {
                $leadData[$field] = (int) $leadData[$field];
                if ($leadData[$field] <= 0) {
                     $leadData[$field] = null;
                }
            } else {
                $leadData[$field] = null;
            }
        }

        // Add timestamps
        $leadData['created_at'] = now();
        $leadData['updated_at'] = now();

        // Store custom data with lead_id placeholder (will be set after lead creation)
        $customDataWithPlaceholder            = $customData;
        $customDataWithPlaceholder['lead_id'] = null; // Will be set in insertChunk

        return [
            'status'     => 'success',
            'leadData'   => $leadData,
            'customData' => $customDataWithPlaceholder,
        ];
    }

    /**
     * Insert chunk of leads and custom data using bulk insert for better performance
     */
    protected function insertChunk(array $leadsChunk, array $customDataChunk, string $tableName): array
    {
        $result = [
            'inserted' => 0,
            'errors'   => [],
        ];

        if (empty($leadsChunk)) {
            return $result;
        }

        // Use smaller transactions to reduce memory usage
        DB::beginTransaction();
        try {
            $now     = now();
            $leadIds = [];

            // Only include fields that actually exist in the leads table (per migration)
            $allowedFields = [
                // Core system fields (28 fields)
                'box_id', 'name', 'email', 'sec_email', 'phone', 'phone_type', 'lead_type',
                'company_name', 'designation', 'amount', 'currency', 'lead_status_id',
                'lead_source_id', 'industry', 'assigned_to', 'country_id', 'state_id',
                'city_id', 'address', 'postal_code', 'location', 'tags', 'description',
                'matured_date', 'visibility', 'status', 'added_by', 'last_updated_by',

                // Legacy/additional fields for backward compatibility
                'bulk_upload_id', 'process_uploading', 'duplicate_leads', 'last_name', 'mobile', 'query', 'ip_address',
                'drug_license', 'gstin', 'experience', 'created_from', 'last_activity_id',
                'last_note_id', 'is_matured', 'is_paid', 'trashed_at', 'gstin_number',
                'pan_number', 'company_id', 'created_at', 'updated_at',
            ];

            // Insert leads one by one to get reliable IDs
            // This is more reliable than bulk insert + query for IDs
            foreach ($leadsChunk as $index => $leadData) {
                try {
                    // Ensure timestamps are set
                    if (! isset($leadData['created_at'])) {
                        $leadData['created_at'] = $now;
                    }
                    if (! isset($leadData['updated_at'])) {
                        $leadData['updated_at'] = $now;
                    }

                    // Filter to only include allowed fields
                    $filteredLeadData = array_intersect_key($leadData, array_flip($allowedFields));

                    // Add bulk_upload_id to track which bulk upload created this lead
                    if ($this->bulkUploadId) {
                        $filteredLeadData['bulk_upload_id'] = $this->bulkUploadId;
                    }

                    // Set process_uploading based on what was set in processRow
                    // It should be 'Success', 'Duplicate', or 'Skipped'
                    // If not set, default to 'Success' for backward compatibility
                    if (!isset($filteredLeadData['process_uploading']) || empty($filteredLeadData['process_uploading'])) {
                        $filteredLeadData['process_uploading'] = 'Success';
                    }

                    // Set duplicate_leads flag for CRM visibility
                    // 1 = Duplicate, 0 = Active Lead
                    $isDuplicateStatus = ($filteredLeadData['process_uploading'] === 'Duplicate');
                    $filteredLeadData['duplicate_leads'] = $isDuplicateStatus ? 1 : 0;

                    // Ensure required fields have values
                    if (empty($filteredLeadData['name']) || trim($filteredLeadData['name']) === '') {
                        throw new \Exception("Name is required");
                    }

                    // Ensure name is not too long (max 55 chars per migration)
                    if (strlen($filteredLeadData['name']) > 55) {
                        $filteredLeadData['name'] = substr($filteredLeadData['name'], 0, 55);
                    }

                    // Ensure phone is not too long (max 15 chars per migration)
                    if (isset($filteredLeadData['phone']) && strlen($filteredLeadData['phone']) > 15) {
                        $filteredLeadData['phone'] = substr($filteredLeadData['phone'], 0, 15);
                    }
                    if (isset($filteredLeadData['mobile']) && strlen($filteredLeadData['mobile']) > 15) {
                        $filteredLeadData['mobile'] = substr($filteredLeadData['mobile'], 0, 15);
                    }

                    // Ensure postal_code is not too long (max 55 chars per migration)
                    if (isset($filteredLeadData['postal_code']) && strlen($filteredLeadData['postal_code']) > 55) {
                        $filteredLeadData['postal_code'] = substr($filteredLeadData['postal_code'], 0, 55);
                    }

                    // Set defaults for fields that have database defaults (if not set)
                    if (! isset($filteredLeadData['country_id']) || $filteredLeadData['country_id'] === null) {
                        $filteredLeadData['country_id'] = 101; // Default country ID
                    }
                    if (! isset($filteredLeadData['is_matured']) || $filteredLeadData['is_matured'] === null) {
                        $filteredLeadData['is_matured'] = 'No';
                    }
                    if (! isset($filteredLeadData['is_paid']) || $filteredLeadData['is_paid'] === null) {
                        $filteredLeadData['is_paid'] = '0';
                    }
                    if (! isset($filteredLeadData['lead_type']) || $filteredLeadData['lead_type'] === null) {
                        $filteredLeadData['lead_type'] = 'Direct Lead';
                    }
                    if (! isset($filteredLeadData['created_from']) || $filteredLeadData['created_from'] === null) {
                        $filteredLeadData['created_from'] = 'Bulk Upload';
                    }

                    // Default values to prevent SQL strict mode errors
                    if (! isset($filteredLeadData['phone'])) {
                        $filteredLeadData['phone'] = '';
                    }
                    if (! isset($filteredLeadData['email'])) {
                        $filteredLeadData['email'] = '';
                    }

                    // Ensure foreign key fields are integers or null
                    $foreignKeyFields = ['country_id', 'state_id', 'city_id', 'lead_source_id', 'lead_status_id',
                        'added_by', 'last_updated_by', 'assigned_to', 'last_activity_id', 'last_note_id'];
                    foreach ($foreignKeyFields as $field) {
                        if (isset($filteredLeadData[$field]) && $filteredLeadData[$field] !== null && $filteredLeadData[$field] !== '') {
                            $filteredLeadData[$field] = (int) $filteredLeadData[$field];
                            // If it's 0 or negative, set to null (invalid foreign key)
                            if ($filteredLeadData[$field] <= 0) {
                                $filteredLeadData[$field] = null;
                            }
                        } else {
                            $filteredLeadData[$field] = null;
                        }
                    }

                    // Insert lead and get ID
                    $leadId          = DB::table('leads')->insertGetId($filteredLeadData);
                    $leadIds[$index] = $leadId;
                    $result['inserted']++;

                    if ($isDuplicateStatus) {
                        \App\Jobs\SendNewLeadNotificationJob::dispatch($leadId, $this->userId, 'duplicate');
                    }

                    // Record Status History manually (since DB::table bypasses model events)
                    if (!empty($filteredLeadData['lead_status_id'])) {
                        DB::table('lead_status_histories')->insert([
                            'box_id'        => $this->boxId,
                            'lead_id'       => $leadId,
                            'old_status_id' => null,
                            'new_status_id' => $filteredLeadData['lead_status_id'],
                            'changed_by'    => $this->userId,
                            'created_at'    => $now,
                            'updated_at'    => $now,
                        ]);
                    }

                    // Unset filtered data to free memory immediately
                    unset($filteredLeadData);

                } catch (\Exception $e) {
                    // Log error for this specific lead but continue with others
                    $leadName           = $leadData['name'] ?? 'Unknown';
                    $errorMsg           = "Error inserting lead '{$leadName}': " . substr($e->getMessage(), 0, 300);
                    if (count($result['errors']) < 50) {
                        $result['errors'][] = $errorMsg;
                    }
                    Log::error($errorMsg, [
                        'lead_name'      => $leadName,
                    ]);
                    // Don't add to leadIds for failed inserts
                }

                // Unset leadData to free memory
                unset($leadData);
            }

            // Free memory after processing all leads
            unset($leadsChunk);

            if ($result['inserted'] > 0) {
                Log::info("Successfully inserted " . $result['inserted'] . " leads into database");
            }

            // Prepare custom data for bulk insert (only for existing custom field columns)
            if (! empty($customDataChunk) && ! empty($leadIds)) {
                // Ensure table exists (but don't create new columns)
                $this->ensureCustomTable($tableName);

                // Check which columns exist in the custom table (don't create new ones)
                $existingColumns = Schema::hasTable($tableName) ? Schema::getColumnListing($tableName) : [];

                // Map custom data to lead IDs
                $customInserts = [];
                foreach ($customDataChunk as $index => $customData) {
                    if (isset($leadIds[$index]) && ! empty($customData)) {
                        // Remove null lead_id if it exists
                        unset($customData['lead_id']);

                        // Prepare custom data row
                        $customRow = [
                            'lead_id'    => $leadIds[$index],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];

                        // Only add custom field values if the column exists in the table
                        foreach ($customData as $key => $value) {
                            if ($key !== 'lead_id' && $key !== 'created_at' && $key !== 'updated_at') {
                                // Only add if column exists (from existing custom fields)
                                if (in_array($key, $existingColumns)) {
                                    $customRow[$key] = $value !== null ? (string) $value : null;
                                }
                            }
                        }

                        // Only add if there are custom fields (not just lead_id and timestamps)
                        if (count($customRow) > 3) {
                            $customInserts[] = $customRow;
                        }
                    }
                }

                // Bulk insert custom data
                if (! empty($customInserts)) {
                    try {
                        DB::table($tableName)->insert($customInserts);
                        Log::info("Inserted " . count($customInserts) . " custom data records into {$tableName}");
                    } catch (\Exception $e) {
                        if (count($result['errors']) < 50) {
                            $result['errors'][] = "Error inserting custom data: " . substr($e->getMessage(), 0, 300);
                        }
                        Log::error("Error inserting custom data into {$tableName}: " . substr($e->getMessage(), 0, 300), [
                            'table_name'           => $tableName,
                            'custom_inserts_count' => count($customInserts),
                        ]);
                    }
                    // Free memory after custom data insert
                    unset($customInserts);
                }
            }

            // Free memory
            unset($customDataChunk, $leadIds, $customInserts);

            DB::commit();

            // Force garbage collection after transaction commit
            gc_collect_cycles();
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMsg           = "Error inserting chunk: " . substr($e->getMessage(), 0, 300);
            if (count($result['errors']) < 50) {
                $result['errors'][] = $errorMsg;
            }
            Log::error($errorMsg);
            throw $e;
        }

        return $result;
    }

    /**
     * Ensure custom table exists (basic structure only - columns are created when custom fields are added)
     */
    protected function ensureCustomTable(string $tableName): void
    {
        // Only create table if it doesn't exist (with basic structure)
        // Don't add new columns - columns are created when custom fields are added via the UI
        if (! Schema::hasTable($tableName)) {
            Schema::create($tableName, function ($table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('lead_id')->index();
                $table->timestamps();
            });
        }
    }

    /**
     * Store statistics for retrieval
     */
    /**
     * Find user ID from Excel row data by email or name
     * IMPORTANT: Prioritizes users within the current box (box_id) for data isolation
     */
    protected function findUserFromExcel(array $rowData, array $headers, ?string $specificColumn = null): ?int
    {
        // If specific column is provided (from owner_mapping), use only that column
        $possibleColumns = $specificColumn
            ? [$specificColumn]
            : ['assigned_to', 'assign_to', 'owner', 'owner_email', 'owner_name', 'user_email', 'user_name', 'assigned_user', 'assigned_user_email', 'assigned_user_name', 'Owner Email', 'Owner', 'Assigned To'];

        foreach ($possibleColumns as $columnName) {
            // Find column index
            $columnIndex = false;
            foreach ($headers as $idx => $header) {
                // Exact match first, then partial match
                if (trim($header) === $columnName ||
                    strcasecmp(trim($header), $columnName) === 0 ||
                    stripos(trim($header), $columnName) !== false) {
                    $columnIndex = $idx;
                    break;
                }
            }

            if ($columnIndex !== false && isset($rowData[$columnIndex])) {
                $value = trim((string) $rowData[$columnIndex]);
                if (empty($value)) {
                    continue;
                }

                // Priority 1: Email Match (Box Scoped)
                $boxUserByEmail = \App\Models\BoxUser::where('box_id', $this->boxId)
                    ->whereHas('user', function ($query) use ($value) {
                         $query->where('email', $value);
                    })->first();
                
                if ($boxUserByEmail) {
                    return $boxUserByEmail->user_id;
                }

                // Priority 2: User ID Match (Box Scoped)
                if (is_numeric($value)) {
                    $valId = (int)$value;
                    $hasBoxAccess = \App\Models\BoxUser::where('box_id', $this->boxId)
                        ->where('user_id', $valId)
                        ->exists();
                    
                    if ($hasBoxAccess) {
                        return $valId;
                    }

                    // Priority 3: BoxUser (pivot) ID Match
                    $boxUserById = \App\Models\BoxUser::where('box_id', $this->boxId)
                        ->where('id', $valId)
                        ->first();
                    
                    if ($boxUserById) {
                        return $boxUserById->user_id;
                    }
                }

                // Fallback: Name Match (Exact then Partial)
                $boxUserByName = \App\Models\BoxUser::where('box_id', $this->boxId)
                    ->whereHas('user', function ($query) use ($value) {
                        $query->where('name', $value);
                    })
                    ->first();

                if ($boxUserByName) {
                    return $boxUserByName->user_id;
                }

                // Partial Name match
                $boxUserByPartialName = \App\Models\BoxUser::where('box_id', $this->boxId)
                    ->whereHas('user', function ($query) use ($value) {
                        $query->where('name', 'like', '%' . $value . '%');
                    })
                    ->first();

                if ($boxUserByPartialName) {
                    return $boxUserByPartialName->user_id;
                }
            }
        }

        return null;
    }

    /**
     * Find ContactStage ID from Excel row data by status name
     */
    protected function findStatusFromExcel(array $rowData, array $headers, ?string $specificColumn = null): ?int
    {
        // If specific column is provided (from status_mapping), use only that column
        $possibleColumns = $specificColumn
            ? [$specificColumn]
            : ['status', 'lead_status', 'contact_stage', 'stage', 'lead_stage', 'Lead Status', 'Status'];

        foreach ($possibleColumns as $columnName) {
            // Find column index
            $columnIndex = false;
            foreach ($headers as $idx => $header) {
                // Exact match first, then partial match
                if (trim($header) === $columnName ||
                    strcasecmp(trim($header), $columnName) === 0 ||
                    stripos(trim($header), $columnName) !== false) {
                    $columnIndex = $idx;
                    break;
                }
            }

            if ($columnIndex !== false && isset($rowData[$columnIndex])) {
                $statusName = trim((string) $rowData[$columnIndex]);
                if (empty($statusName)) {
                    continue;
                }

                // If value is numeric, assume it's an ID
                if (is_numeric($statusName)) {
                    $statusId = (int)$statusName;
                    $exists = \App\Models\ContactStage::where('box_id', $this->boxId)->where('id', $statusId)->exists();
                    if ($exists) {
                        return $statusId;
                    }
                }

                // Find ContactStage by name - exact first
                $contactStage = \App\Models\ContactStage::where('box_id', $this->boxId)
                    ->where('name', $statusName)
                    ->first();

                if ($contactStage) {
                    return $contactStage->id;
                }

                // Partial match
                $contactStage = \App\Models\ContactStage::where('box_id', $this->boxId)
                    ->where('name', 'like', '%' . $statusName . '%')
                    ->first();

                if ($contactStage) {
                    return $contactStage->id;
                }
            }
        }

        return null;
    }

    /**
     * Find Source ID from name string
     */
    protected function findSourceFromExcel(string $sourceName): ?int
    {
        $sourceName = trim($sourceName);
        if (empty($sourceName)) {
            return null;
        }

        // If numeric, assume it's an ID
        if (is_numeric($sourceName)) {
            $sourceId = (int)$sourceName;
            $exists = \App\Models\CrmSource::where('box_id', $this->boxId)->where('id', $sourceId)->exists();
            if ($exists) {
                return $sourceId;
            }
        }

        // Find CrmSource by name - exact match first
        $source = \App\Models\CrmSource::where('box_id', $this->boxId)
            ->where('name', $sourceName)
            ->where('status', true)
            ->first();

        if ($source) {
            return $source->id;
        }

        // Partial match
        $source = \App\Models\CrmSource::where('box_id', $this->boxId)
            ->where('name', 'like', '%' . $sourceName . '%')
            ->where('status', true)
            ->first();

        if ($source) {
            return $source->id;
        }

        return null;
    }

    /**
     * Find Source ID from Excel row data by source name
     */
    protected function findSourceFromExcelRow(array $rowData, array $headers): ?int
    {
        // Look for columns that might contain source
        $possibleColumns = ['source', 'lead_source', 'lead source', 'crm_source', 'origin'];

        foreach ($possibleColumns as $columnName) {
            // Find column index
            $columnIndex = false;
            foreach ($headers as $idx => $header) {
                if (stripos(trim($header), $columnName) !== false || strcasecmp(trim($header), $columnName) === 0) {
                    $columnIndex = $idx;
                    break;
                }
            }

            if ($columnIndex !== false && isset($rowData[$columnIndex])) {
                $sourceName = trim((string) $rowData[$columnIndex]);
                if (empty($sourceName)) {
                    continue;
                }

                return $this->findSourceFromExcel($sourceName);
            }
        }

        return null;
    }

    /**
     * Find Industry ID from name string
     */
    protected function findIndustryFromExcel(string $industryName): ?int
    {
        $industryName = trim($industryName);
        if (empty($industryName)) {
            return null;
        }

        // If numeric, assume it's an ID
        if (is_numeric($industryName)) {
            $industryId = (int)$industryName;
            $exists = \App\Models\Industry::where('box_id', $this->boxId)->where('id', $industryId)->exists();
            if ($exists) {
                return $industryId;
            }
        }

        // Find Industry by name scoped to box_id - exact match first
        $industry = \App\Models\Industry::where('box_id', $this->boxId)
            ->where('name', $industryName)
            ->where('status', 'Enabled')
            ->first();

        if ($industry) {
            return $industry->id;
        }

        // Partial match
        $industry = \App\Models\Industry::where('box_id', $this->boxId)
            ->where('name', 'like', '%' . $industryName . '%')
            ->where('status', 'Enabled')
            ->first();

        if ($industry) {
            return $industry->id;
        }

        return null;
    }

    /**
     * Find Industry ID from Excel row data by industry name
     */
    protected function findIndustryFromExcelRow(array $rowData, array $headers): ?int
    {
        // Look for columns that might contain industry
        $possibleColumns = ['industry', 'industry_name', 'industry name', 'sector', 'business_type'];

        foreach ($possibleColumns as $columnName) {
            // Find column index
            $columnIndex = false;
            foreach ($headers as $idx => $header) {
                if (stripos(trim($header), $columnName) !== false || strcasecmp(trim($header), $columnName) === 0) {
                    $columnIndex = $idx;
                    break;
                }
            }

            if ($columnIndex !== false && isset($rowData[$columnIndex])) {
                $industryName = trim((string) $rowData[$columnIndex]);
                if (empty($industryName)) {
                    continue;
                }

                return $this->findIndustryFromExcel($industryName);
            }
        }

        return null;
    }

    /**
     * Find Country ID from name string
     */
    protected function findCountryFromExcel(string $countryName): ?int
    {
        $countryName = trim($countryName);
        if (empty($countryName)) {
            return null;
        }

        // If numeric, assume it's an ID
        if (is_numeric($countryName)) {
            $countryId = (int)$countryName;
            $exists = \App\Models\Country::where('id', $countryId)->exists();
            if ($exists) {
                return $countryId;
            }
        }

        // Try exact match first
        $country = \App\Models\Country::where('name', $countryName)->first();

        if ($country) {
            return $country->id;
        }

        // Try partial match
        $country = \App\Models\Country::where('name', 'like', '%' . $countryName . '%')->first();

        if ($country) {
            return $country->id;
        }

        return null;
    }

    /**
     * Find Country ID from Excel row data by country name
     */
    protected function findCountryFromExcelRow(array $rowData, array $headers): ?int
    {
        // Look for columns that might contain country
        $possibleColumns = ['country', 'country_name', 'country name', 'nation'];

        foreach ($possibleColumns as $columnName) {
            // Find column index
            $columnIndex = false;
            foreach ($headers as $idx => $header) {
                if (stripos(trim($header), $columnName) !== false || strcasecmp(trim($header), $columnName) === 0) {
                    $columnIndex = $idx;
                    break;
                }
            }

            if ($columnIndex !== false && isset($rowData[$columnIndex])) {
                $countryName = trim((string) $rowData[$columnIndex]);
                if (empty($countryName)) {
                    continue;
                }

                return $this->findCountryFromExcel($countryName);
            }
        }

        return null;
    }

    /**
     * Find State ID from name string
     */
    protected function findStateFromExcel(string $stateName, ?int $countryId = null): ?int
    {
        $stateName = trim($stateName);
        if (empty($stateName)) {
            return null;
        }

        // If numeric, assume it's an ID
        if (is_numeric($stateName)) {
            $stateId = (int)$stateName;
            $query = \App\Models\State::query();
            if ($countryId) {
                $query->where('country_id', $countryId);
            }
            $exists = $query->where('id', $stateId)->exists();
            if ($exists) {
                return $stateId;
            }
        }

        // Build query for name match
        $query = \App\Models\State::query();

        // If country is specified, filter by country
        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        // Try exact match first
        $state = $query->where('name', $stateName)->first();

        if ($state) {
            return $state->id;
        }

        // Try partial match
        $query = \App\Models\State::query();
        if ($countryId) {
            $query->where('country_id', $countryId);
        }
        $state = $query->where('name', 'like', '%' . $stateName . '%')->first();

        if ($state) {
            return $state->id;
        }

        return null;
    }

    /**
     * Find State ID from Excel row data by state name
     */
    protected function findStateFromExcelRow(array $rowData, array $headers, ?int $countryId = null): ?int
    {
        // Look for columns that might contain state
        $possibleColumns = ['state', 'state_name', 'state name', 'province', 'region'];

        foreach ($possibleColumns as $columnName) {
            // Find column index
            $columnIndex = false;
            foreach ($headers as $idx => $header) {
                if (stripos(trim($header), $columnName) !== false || strcasecmp(trim($header), $columnName) === 0) {
                    $columnIndex = $idx;
                    break;
                }
            }

            if ($columnIndex !== false && isset($rowData[$columnIndex])) {
                $stateName = trim((string) $rowData[$columnIndex]);
                if (empty($stateName)) {
                    continue;
                }

                return $this->findStateFromExcel($stateName, $countryId);
            }
        }

        return null;
    }

    /**
     * Find City ID from name string
     */
    protected function findCityFromExcel(string $cityName, ?int $stateId = null): ?int
    {
        $cityName = trim($cityName);
        if (empty($cityName)) {
            return null;
        }

        // If numeric, assume it's an ID
        if (is_numeric($cityName)) {
            $cityId = (int)$cityName;
            $query = \App\Models\City::query();
            if ($stateId) {
                $query->where('state_id', $stateId);
            }
            $exists = $query->where('id', $cityId)->exists();
            if ($exists) {
                return $cityId;
            }
        }

        // Build query for name match
        $query = \App\Models\City::query();

        // If state is specified, filter by state
        if ($stateId) {
            $query->where('state_id', $stateId);
        }

        // Try exact match first
        $city = $query->where('name', $cityName)->first();

        if ($city) {
            return $city->id;
        }

        // Try partial match
        $query = \App\Models\City::query();
        if ($stateId) {
            $query->where('state_id', $stateId);
        }
        $city = $query->where('name', 'like', '%' . $cityName . '%')->first();

        if ($city) {
            return $city->id;
        }

        return null;
    }

    /**
     * Find City ID from Excel row data by city name
     */
    protected function findCityFromExcelRow(array $rowData, array $headers, ?int $stateId = null): ?int
    {
        // Look for columns that might contain city
        $possibleColumns = ['city', 'city_name', 'city name', 'town', 'locality'];

        foreach ($possibleColumns as $columnName) {
            // Find column index
            $columnIndex = false;
            foreach ($headers as $idx => $header) {
                if (stripos(trim($header), $columnName) !== false || strcasecmp(trim($header), $columnName) === 0) {
                    $columnIndex = $idx;
                    break;
                }
            }

            if ($columnIndex !== false && isset($rowData[$columnIndex])) {
                $cityName = trim((string) $rowData[$columnIndex]);
                if (empty($cityName)) {
                    continue;
                }

                return $this->findCityFromExcel($cityName, $stateId);
            }
        }

        return null;
    }

    protected function storeStatistics(array $stats): void
    {
        // Ensure bulk_upload_id is included in stats
        if (! isset($stats['bulk_upload_id']) && $this->bulkUploadId) {
            $stats['bulk_upload_id'] = $this->bulkUploadId;
        }

        // Store in cache for 1 hour (multiple keys to ensure it's found)
        $cacheKey     = "bulk_upload_stats_{$this->boxId}_" . md5($this->filePath);
        $altCacheKey1 = "bulk_upload_stats_{$this->boxId}_" . md5(ltrim($this->filePath, '/'));
        $altCacheKey2 = "bulk_upload_stats_{$this->boxId}_" . md5(trim($this->filePath, '/'));
        cache()->put($cacheKey, $stats, 3600);
        cache()->put($altCacheKey1, $stats, 3600);
        cache()->put($altCacheKey2, $stats, 3600);

        // Update database record - try multiple ways to find it
        $normalizedPath  = trim($this->filePath, '/');
        $normalizedPath2 = ltrim($this->filePath, '/');

        $bulkUpload = BulkUpload::where(function ($query) use ($normalizedPath, $normalizedPath2) {
            $query->where('file_path', $normalizedPath)
                ->orWhere('file_path', $normalizedPath2)
                ->orWhere('file_path', $this->filePath)
                ->orWhere('file_path', '/' . $normalizedPath)
                ->orWhere('file_path', '/' . $normalizedPath2);
        })
            ->where('box_id', $this->boxId)
            ->where('user_id', $this->userId)
            ->first();

        // If not found, try without user_id
        if (! $bulkUpload) {
            $bulkUpload = BulkUpload::where(function ($query) use ($normalizedPath, $normalizedPath2) {
                $query->where('file_path', $normalizedPath)
                    ->orWhere('file_path', $normalizedPath2)
                    ->orWhere('file_path', $this->filePath)
                    ->orWhere('file_path', '/' . $normalizedPath)
                    ->orWhere('file_path', '/' . $normalizedPath2);
            })
                ->where('box_id', $this->boxId)
                ->latest()
                ->first();
        }

        // If still not found, try by file name
        if (! $bulkUpload) {
            $fileName   = basename($this->filePath);
            $bulkUpload = BulkUpload::where('file_name', $fileName)
                ->where('box_id', $this->boxId)
                ->latest()
                ->first();
        }

        if ($bulkUpload) {
            $updateData = [
                'total_rows'      => $stats['total'] ?? 0,
                'processed_rows'  => $stats['processed'] ?? 0,
                'successful_rows' => $stats['successful'] ?? 0,
                'skipped_rows'    => $stats['skipped'] ?? 0,
                'duplicate_rows'  => $stats['duplicates'] ?? 0,
                'percentage'      => $stats['percentage'] ?? 0,
                'status'          => $stats['status'] ?? 'processing',
            ];

            // Update timestamps based on status
            if (($stats['status'] ?? '') === 'processing' && ! $bulkUpload->started_at) {
                $updateData['started_at'] = now();
            }

            if (($stats['status'] ?? '') === 'completed') {
                $updateData['completed_at'] = now();
                $updateData['processed_at'] = now();
            }

            if (($stats['status'] ?? '') === 'failed' || ($stats['status'] ?? '') === 'cancelled') {
                $updateData['processed_at'] = now();
                if (isset($stats['errors']) && ! empty($stats['errors'])) {
                    $updateData['error_message'] = is_array($stats['errors'])
                        ? implode("\n", array_slice($stats['errors'], 0, 5))
                        : $stats['errors'];
                }
            }

            $bulkUpload->update($updateData);
        }
    }

    /**
     * Map system field names to actual database column names
     * Handles special cases where field name differs from database column
     */
    protected function mapFieldToDbColumn(string $fieldName): string
    {
        // Special mappings where field name differs from database column
        // This handles cases where CSV might use friendly names instead of DB column names
        $specialMappings = [
            // Value mappings
            'value'         => 'amount',

            // Source mappings (all map to lead_source_id)
            'source'        => 'lead_source_id',
            'lead_source'   => 'lead_source_id',
            'lead source'   => 'lead_source_id',
            'crm_source'    => 'lead_source_id',

            // Status mappings (all map to lead_status_id)
            'lead_status'   => 'lead_status_id',
            'lead status'   => 'lead_status_id',
            'contact_stage' => 'lead_status_id',

            // Owner mappings
            'owner'         => 'assigned_to',
            'owner_email'   => 'assigned_to',
            'owner_name'    => 'assigned_to',

            // Name mappings
            'lead_name'     => 'name',
            'lead name'     => 'name',
        ];

        // Return special mapping if exists, otherwise return the field name as-is
        return $specialMappings[strtolower($fieldName)] ?? $fieldName;
    }
}
