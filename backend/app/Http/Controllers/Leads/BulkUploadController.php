<?php
namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateBulkUploadReportJob;
use App\Models\BoxCustomField;
use App\Models\BoxUser;
use App\Models\BulkUpload;
use App\Models\SystemField;
use App\Exports\SampleFileExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BulkUploadController extends Controller
{
    /**
     * Upload CSV/Excel file and return column headers
     */
    public function uploadCsv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => [
                'required',
                'file',
                'max:51200', // 50MB max
                function ($attribute, $value, $fail) {
                    $allowedExtensions = ['csv', 'txt', 'xlsx', 'xls'];
                    $allowedMimes      = [
                        'text/csv',
                        'text/plain',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/excel',
                        'application/x-excel',
                        'application/x-msexcel',
                        'application/octet-stream', // Sometimes Excel files are detected as this
                    ];

                    $extension = strtolower($value->getClientOriginalExtension());
                    $mimeType  = $value->getMimeType();

                    // Accept if extension OR mime type matches
                    if (! in_array($extension, $allowedExtensions) && ! in_array($mimeType, $allowedMimes)) {
                        $fail('The file must be a CSV or Excel file (csv, txt, xlsx, xls). Received: ' . $extension . ' (' . $mimeType . ')');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            // Check if file exists
            if (! $request->hasFile('file')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No file uploaded.',
                ], 422);
            }

            $file = $request->file('file');

                              // Check file size
            $maxSize = 51200; // 50MB in KB
            if ($file->getSize() > ($maxSize * 1024)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'File size exceeds maximum allowed size of 50MB.',
                ], 422);
            }

            // Ensure storage directory exists
            if (! Storage::disk('public')->exists('bulk_uploads')) {
                Storage::disk('public')->makeDirectory('bulk_uploads');
            }

            // Get user and box info first (needed for duplicate check)
            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (! $boxUser) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Box user not found',
                ], 404);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('integrations', 'create', 'bulk_upload'))) {
                 return response()->json(['status' => 'error', 'message' => 'You do not have permission to upload files.'], 403);
            }

            $originalFileName = $file->getClientOriginalName();
            $fileExtension = strtolower($file->getClientOriginalExtension());

            // Always create a new file - never reuse or replace existing files
            // Generate unique filename for new file
            $fileName = time() . '_' . Str::random(10) . '.' . $fileExtension;

            // Store new file
            try {
                $path = $file->storeAs('bulk_uploads', $fileName, 'public');
            } catch (\Exception $storeException) {
                Log::error('File storage error: ' . $storeException->getMessage(), [
                    'original_file_name' => $originalFileName,
                    'file_name' => $fileName,
                    'file_size' => $file->getSize(),
                    'trace'     => $storeException->getTraceAsString(),
                ]);

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Failed to store file. Please check storage permissions.',
                ], 500);
            }

            if (! $path) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Failed to store file.',
                ], 500);
            }

            // Parse file to get headers
            $filePath = storage_path('app/public/' . $path);

            // Check if file exists after storage
            if (! file_exists($filePath)) {
                Log::error('File not found after storage', [
                    'file_path'   => $filePath,
                    'stored_path' => $path,
                ]);

                return response()->json([
                    'status'  => 'error',
                    'message' => 'File was uploaded but not found. Please check storage configuration.',
                ], 500);
            }

            // Detect file type and get headers
            try {
                if (in_array($fileExtension, ['xlsx', 'xls'])) {
                    $headers = $this->getExcelHeaders($filePath);
                } else {
                    $headers = $this->getCsvHeaders($filePath);
                }
            } catch (\Exception $parseException) {
                Log::error('File parsing error: ' . $parseException->getMessage(), [
                    'file_path'      => $filePath,
                    'file_extension' => $fileExtension,
                    'trace'          => $parseException->getTraceAsString(),
                ]);

                // Delete the uploaded file if parsing fails
                Storage::disk('public')->delete($path);

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Failed to parse file. Please ensure the file is a valid CSV or Excel file.',
                ], 500);
            }

            // Always create a new bulk upload record (never update existing)
            $bulkUpload = BulkUpload::create([
                'box_id'             => $boxUser->box_id,
                'user_id'            => $userId,
                'file_name'          => $fileName,
                'original_file_name' => $originalFileName,
                'file_path'          => $path,
                'file_type'          => $fileExtension,
                'file_size'          => $file->getSize(),
                'status'             => 'pending',
            ]);

            return response()->json([
                'status'             => 'success',
                'message'            => 'File uploaded successfully',
                'file_path'          => $path,
                'file_name'          => $fileName,
                'original_file_name' => $originalFileName,
                'file_type'          => $fileExtension,
                'headers'            => $headers,
                'bulk_upload_id'     => $bulkUpload->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Upload CSV error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to upload file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get CSV headers
     */
    private function getCsvHeaders($filePath)
    {
        $headers = [];

        if (! file_exists($filePath)) {
            throw new \Exception('CSV file not found: ' . $filePath);
        }

        if (! is_readable($filePath)) {
            throw new \Exception('CSV file is not readable: ' . $filePath);
        }

        $handle = @fopen($filePath, 'r');
        if ($handle === false) {
            throw new \Exception('Failed to open CSV file: ' . $filePath);
        }

        try {
            // Try to detect encoding and convert to UTF-8 if needed
            $firstLine = fgets($handle);
            rewind($handle);

            // Read headers
            $headers = fgetcsv($handle);

            if ($headers === false) {
                throw new \Exception('Failed to read CSV headers from file.');
            }

            // Clean headers - remove BOM and trim
            $headers = array_map(function ($header) {
                // Remove BOM if present
                $header = preg_replace('/^\xEF\xBB\xBF/', '', $header);
                return trim($header);
            }, $headers);

        } finally {
            fclose($handle);
        }

        return array_filter($headers); // Remove empty headers
    }

    /**
     * Get Excel headers
     */
    private function getExcelHeaders($filePath)
    {
        if (! file_exists($filePath)) {
            throw new \Exception('Excel file not found: ' . $filePath);
        }

        if (! is_readable($filePath)) {
            throw new \Exception('Excel file is not readable: ' . $filePath);
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet   = $spreadsheet->getActiveSheet();
            $headers     = [];

            // Get first row as headers
            $highestColumn      = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellValue = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
                if ($cellValue !== null && trim($cellValue) !== '') {
                    $headers[] = trim($cellValue);
                }
            }

            return array_filter($headers); // Remove empty headers
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            Log::error('PhpSpreadsheet error: ' . $e->getMessage());
            throw new \Exception('Failed to read Excel file. Please ensure the file is a valid Excel format.');
        } catch (\Exception $e) {
            Log::error('Excel parsing error: ' . $e->getMessage());
            throw new \Exception('Failed to read Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Get all system fields for mapping
     */
    public function getAllMapings()
    {
        try {
            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (! $boxUser) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Box user not found',
                ], 404);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('integrations', 'read', 'bulk_upload'))) {
                 return response()->json(['status' => 'error', 'message' => 'You do not have permission to view mappings.'], 403);
            }

            // Auto-sync system fields to ensure all standard fields are available for mapping
            \App\Console\Commands\SyncMissingSystemFields::syncForBox($boxUser->box_id);

            $systemFields = SystemField::where('box_id', $boxUser->box_id)
                ->where('object', 'leads')
                ->orderBy('sequence')
                ->get();

            $customFields = BoxCustomField::where('box_id', $boxUser->box_id)
                ->where('object', 'leads')
                ->where('status', 'Enabled')
                ->orderBy('ordering')
                ->get();

            return response()->json([
                'status'        => 'success',
                'system_fields' => $systemFields,
                'custom_fields' => $customFields,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch mappings: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save field mappings and create custom fields for unmapped columns
     */
    public function saveMapping(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_path'        => 'required|string',
            'mappings'         => 'required|array',
            'create_duplicate' => 'nullable|boolean',
            'assign_to_enabled' => 'nullable|boolean',
            'status_enabled'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (! $boxUser) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Box user not found',
                ], 404);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('integrations', 'import', 'bulk_upload'))) {
                 return response()->json(['status' => 'error', 'message' => 'You do not have permission to save mappings.'], 403);
            }

            $mappings        = $request->mappings; // Format: ['uploaded_column' => 'field_id']
            $filePath        = $request->file_path;
            $createDuplicate = $request->create_duplicate ?? false;
            $assignToEnabled = $request->assign_to_enabled ?? false;
            $statusEnabled   = $request->status_enabled ?? false;
            $boxId           = $boxUser->box_id;

            // Source is now handled as a regular system field in mappings, no special handling needed

            // Get all valid field identifiers from database (current state)
            $validFieldIds = [];
            $fieldDetails  = []; // Store field details for better validation

            // Get system fields - use field or field_name (but not if it has field_refernce_key)
            $systemFields = SystemField::where('box_id', $boxId)
                ->where('object', 'leads')
                ->get();

            foreach ($systemFields as $field) {
                // If field_refernce_key is set, it's actually a custom field reference
                if ($field->field_refernce_key) {
                    // This will be handled by custom fields lookup
                    continue;
                }
                // Real system field
                $fieldId = $field->field ?: $field->field_name;
                if ($fieldId) {
                    $validFieldIds[$fieldId] = true;
                    $fieldDetails[$fieldId]  = [
                        'type'  => 'system',
                        'name'  => $field->name,
                        'field' => $fieldId,
                    ];
                }
            }

            // Get custom fields - use col_name
            $customFields = BoxCustomField::where('box_id', $boxId)
                ->where('object', 'leads')
                ->get();

            foreach ($customFields as $field) {
                $validFieldIds[$field->col_name] = true;
                $fieldDetails[$field->col_name]  = [
                    'type'     => 'custom',
                    'name'     => $field->name,
                    'col_name' => $field->col_name,
                ];
            }

            // Also add system fields that reference custom fields (by their col_name)
            foreach ($systemFields as $field) {
                if ($field->field_refernce_key) {
                    $customField = $customFields->find($field->field_refernce_key);
                    if ($customField) {
                        $validFieldIds[$customField->col_name] = true;
                        if (! isset($fieldDetails[$customField->col_name])) {
                            $fieldDetails[$customField->col_name] = [
                                'type'     => 'custom',
                                'name'     => $customField->name,
                                'col_name' => $customField->col_name,
                            ];
                        }
                    }
                }
            }

            // Check if there's a saved mapping in session and compare with current fields
            $sessionMapping  = session('bulk_upload_mapping');
            $removedFields   = [];
            $updatedMappings = [];

            if ($sessionMapping && isset($sessionMapping['mappings']) && is_array($sessionMapping['mappings'])) {
                // Compare session mappings with current valid fields
                foreach ($sessionMapping['mappings'] as $uploadedColumn => $savedFieldId) {
                    // Check if the saved field still exists
                    if (! isset($validFieldIds[$savedFieldId])) {
                        // Field was deleted, remove from mapping
                        $removedFields[] = [
                            'column' => $uploadedColumn,
                            'field'  => $savedFieldId,
                            'reason' => 'Field was deleted or no longer exists',
                        ];
                        // Remove from incoming mappings if it exists
                        if (isset($mappings[$uploadedColumn]) && $mappings[$uploadedColumn] === $savedFieldId) {
                            unset($mappings[$uploadedColumn]);
                        }
                    } else {
                        // Field still exists, keep it in updated mappings
                        $updatedMappings[$uploadedColumn] = $savedFieldId;
                    }
                }
            }

            // Validate and filter mappings - only keep valid ones
            $validMappings   = [];
            $invalidMappings = [];
            foreach ($mappings as $uploadedColumn => $fieldId) {
                if (empty($fieldId) || trim($fieldId) === '') {
                    continue; // Skip empty mappings
                }

                // Trim the fieldId to handle whitespace issues
                $fieldId = trim($fieldId);

                if (isset($validFieldIds[$fieldId])) {
                    $validMappings[$uploadedColumn] = $fieldId;
                } else {
                    // Log invalid mapping for debugging
                    $invalidMappings[] = "Column '{$uploadedColumn}' → Field '{$fieldId}' (not found or deleted)";
                }
            }

            // Merge with updated mappings from session (if they're still valid)
            foreach ($updatedMappings as $uploadedColumn => $fieldId) {
                // Only add if not already in validMappings (request takes precedence)
                if (! isset($validMappings[$uploadedColumn])) {
                    $validMappings[$uploadedColumn] = $fieldId;
                }
            }

            // Log if there are invalid mappings or removed fields
            if (! empty($invalidMappings) || ! empty($removedFields)) {
                Log::warning('Bulk upload: Field mappings updated', [
                    'invalid_count'    => count($invalidMappings),
                    'removed_count'    => count($removedFields),
                    'invalid_mappings' => array_slice($invalidMappings, 0, 10),                 // Log first 10
                    'removed_fields'   => array_slice($removedFields, 0, 10),                   // Log first 10
                    'valid_field_ids'  => array_keys(array_slice($validFieldIds, 0, 20, true)), // Log first 20 valid IDs
                ]);
            }

            // Store mapping in session for processing
            session([
                'bulk_upload_mapping' => [
                    'file_path'        => $filePath,
                    'mappings'         => $validMappings,
                    'create_duplicate' => $createDuplicate,
                    'assign_to_enabled' => $assignToEnabled,
                    'status_enabled'   => $statusEnabled,
                    'box_id'           => $boxId,
                ],
            ]);

            if (empty($validMappings)) {
                return response()->json([
                    'status'           => 'error',
                    'message'          => 'No valid field mappings found. Please ensure you have mapped at least one field.',
                    'mapped_count'     => 0,
                    'invalid_mappings' => $invalidMappings ?? [],
                ], 422);
            }

            $warningMessages = [];
            if (! empty($invalidMappings)) {
                $warningMessages[] = 'Some mappings were invalid and skipped.';
            }
            if (! empty($removedFields)) {
                $warningMessages[] = count($removedFields) . ' mapping(s) were removed because the fields no longer exist.';
            }

            return response()->json([
                'status'         => 'success',
                'message'        => 'Mapping saved successfully.',
                'mapped_count'   => count($validMappings),
                'invalid_count'  => count($invalidMappings ?? []),
                'removed_count'  => count($removedFields ?? []),
                'removed_fields' => $removedFields ?? [],
                'warnings'       => ! empty($warningMessages) ? implode(' ', $warningMessages) : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to save mapping: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process the CSV import with mappings (using queue)
     */
    public function processImport(Request $request)
    {
        try {
            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (! $boxUser) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Box user not found',
                ], 404);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('integrations', 'import', 'bulk_upload'))) {
                 return response()->json(['status' => 'error', 'message' => 'You do not have permission to import files.'], 403);
            }

            // Get mapping data from request first, fallback to session
            $mappingData = null;
            if ($request->has('file_path') && $request->has('mappings')) {
                // Get from request (direct submission)
                $mappingData = [
                    'file_path'        => $request->file_path,
                    'mappings'         => $request->mappings,
                    'create_duplicate' => $request->create_duplicate ?? false,
                    'assign_to_enabled' => $request->assign_to_enabled ?? false,
                    'status_enabled'   => $request->status_enabled ?? false,
                    'box_id'           => $boxUser->box_id,
                ];
            } else {
                // Get from session (legacy support)
                $mappingData = session('bulk_upload_mapping');
            }

            if (! $mappingData) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Mapping not found. Please upload and map fields again.',
                ], 404);
            }

            $filePath        = $mappingData['file_path'];
            $mappings        = $mappingData['mappings']; // Already validated in saveMapping
            $createDuplicate = $mappingData['create_duplicate'];
            $assignToEnabled = $mappingData['assign_to_enabled'] ?? false;
            $statusEnabled   = $mappingData['status_enabled'] ?? false;
            $boxId           = $mappingData['box_id'];

            // Find or create bulk upload record
            $bulkUpload = BulkUpload::where('file_path', $filePath)
                ->where('box_id', $boxId)
                ->where('user_id', $userId)
                ->first();

            if (! $bulkUpload) {
                // If record doesn't exist, create it
                $baseFileName = basename($filePath);
                $bulkUpload = BulkUpload::create([
                    'box_id'             => $boxId,
                    'user_id'            => $userId,
                    'file_name'          => $baseFileName,
                    'original_file_name' => $mappingData['original_file_name'] ?? $baseFileName, // Use original if provided
                    'file_path'          => $filePath,
                    'file_type'          => pathinfo($filePath, PATHINFO_EXTENSION),
                    'file_size'          => Storage::disk('public')->exists($filePath) ? Storage::disk('public')->size($filePath) : 0,
                    'status'             => 'pending',
                    'mappings'           => $mappings,
                    'create_duplicate'   => $createDuplicate,
                ]);
            } else {
                // Update existing record with mappings
                $bulkUpload->update([
                    'mappings'         => $mappings,
                    'create_duplicate' => $createDuplicate,
                    'status'           => 'pending',
                ]);
            }

            // Initialize statistics immediately (before job starts)
            $statsKey     = "bulk_upload_stats_{$boxId}_" . md5($filePath);
            $initialStats = [
                'total'          => 0,
                'processed'      => 0,
                'successful'     => 0,
                'skipped'        => 0,
                'duplicates'     => 0,
                'percentage'     => 0,
                'status'         => 'pending', // pending, processing, completed, failed, cancelled
                'errors'         => [],
                'started_at'     => now()->toDateTimeString(),
                'bulk_upload_id' => $bulkUpload->id,
            ];
            cache()->put($statsKey, $initialStats, 3600);

            // Dispatch job to queue for processing
            \App\Jobs\ProcessBulkUploadJob::dispatch(
                $filePath,
                $mappings,
                $createDuplicate,
                $boxId,
                $userId,
                $assignToEnabled,
                $statusEnabled
            );

            // Clear session
            session()->forget('bulk_upload_mapping');

            // Store file path in cache for statistics retrieval
            $cacheKey = "bulk_upload_file_{$boxId}_" . md5($filePath);
            cache()->put($cacheKey, $filePath, 3600);

            // Store job identifier for cancellation
            $jobIdKey = "bulk_upload_job_{$boxId}_" . md5($filePath);
            cache()->put($jobIdKey, [
                'file_path'  => $filePath,
                'box_id'     => $boxId,
                'user_id'    => $userId,
                'started_at' => now()->toDateTimeString(),
            ], 3600);

            return response()->json([
                'status'    => 'success',
                'message'   => 'Import job has been queued. Processing will start shortly. You will be notified once completed.',
                'queued'    => true,
                'file_path' => $filePath,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to queue import: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Stop/Cancel the import process
     */
    public function stopImport(Request $request)
    {
        try {
            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (! $boxUser) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Box user not found',
                ], 404);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('integrations', 'import', 'bulk_upload'))) {
                 return response()->json(['status' => 'error', 'message' => 'You do not have permission to stop import.'], 403);
            }

            $filePath    = $request->file_path;
            $deleteFiles = $request->boolean('delete_files', false); // Optional: delete files when cancelling

            if (! $filePath) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'File path is required',
                ], 400);
            }

            // Set cancellation flag in cache
            $cancelKey = "bulk_upload_cancel_{$boxUser->box_id}_" . md5($filePath);
            cache()->put($cancelKey, true, 3600);

            // Update statistics to show cancelled status
            $statsKey              = "bulk_upload_stats_{$boxUser->box_id}_" . md5($filePath);
            $stats                 = cache()->get($statsKey, []);
            $stats['status']       = 'cancelled';
            $stats['cancelled_at'] = now()->toDateTimeString();
            cache()->put($statsKey, $stats, 3600);

            // Update database record
            $bulkUpload = BulkUpload::where('file_path', $filePath)
                ->where('box_id', $boxUser->box_id)
                ->where('user_id', $userId)
                ->first();

            if ($bulkUpload) {
                $bulkUpload->update([
                    'status'        => 'cancelled',
                    'processed_at'  => now(),
                    'error_message' => 'Import cancelled by user' . ($deleteFiles ? ' and files deleted' : ''),
                ]);

                // Delete files if requested
                if ($deleteFiles) {
                    // Delete the uploaded file from storage
                    if ($bulkUpload->file_path && Storage::disk('public')->exists($bulkUpload->file_path)) {
                        Storage::disk('public')->delete($bulkUpload->file_path);
                    }

                    // Clear all cache entries
                    cache()->forget($statsKey);
                    cache()->forget("bulk_upload_file_{$boxUser->box_id}_" . md5($filePath));
                    cache()->forget("bulk_upload_job_{$boxUser->box_id}_" . md5($filePath));
                    cache()->forget($cancelKey);

                    // Delete the database record
                    $bulkUpload->delete();

                    return response()->json([
                        'status'    => 'success',
                        'message'   => 'Import has been stopped and files have been deleted.',
                        'cancelled' => true,
                        'deleted'   => true,
                    ]);
                }
            }

            return response()->json([
                'status'    => 'success',
                'message'   => 'Import has been stopped. The process will cancel after the current row is processed.',
                'cancelled' => true,
                'deleted'   => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to stop import: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get import statistics
     */
    public function getImportStatistics(Request $request)
    {
        try {
            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (! $boxUser) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Box user not found',
                ], 404);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('integrations', 'read', 'bulk_upload'))) {
                 return response()->json(['status' => 'error', 'message' => 'You do not have permission to view import statistics.'], 403);
            }

            $filePath = $request->file_path;
            if (! $filePath) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'File path is required',
                ], 400);
            }

            // Try multiple cache keys in case file path format differs
            $cacheKey     = "bulk_upload_stats_{$boxUser->box_id}_" . md5($filePath);
            $altCacheKey1 = "bulk_upload_stats_{$boxUser->box_id}_" . md5(ltrim($filePath, '/'));
            $altCacheKey2 = "bulk_upload_stats_{$boxUser->box_id}_" . md5(trim($filePath, '/'));

            $stats = cache()->get($cacheKey);
            if (! $stats) {
                $stats = cache()->get($altCacheKey1);
            }
            if (! $stats) {
                $stats = cache()->get($altCacheKey2);
            }

            // Also check database for more accurate stats
            // Normalize file path for comparison
            $normalizedPath  = trim($filePath, '/');
            $normalizedPath2 = ltrim($filePath, '/');

            // Try exact match first with multiple path variations
            $bulkUpload = BulkUpload::where(function ($query) use ($filePath, $normalizedPath, $normalizedPath2) {
                $query->where('file_path', $filePath)
                    ->orWhere('file_path', $normalizedPath)
                    ->orWhere('file_path', $normalizedPath2)
                    ->orWhere('file_path', '/' . $normalizedPath)
                    ->orWhere('file_path', '/' . $normalizedPath2);
            })
                ->where('box_id', $boxUser->box_id)
                ->first();

            // If not found, try by file name only (most recent)
            if (! $bulkUpload) {
                $bulkUpload = BulkUpload::where('box_id', $boxUser->box_id)
                    ->where('file_name', basename($filePath))
                    ->latest()
                    ->first();
            }

            // Log for debugging
            if (! $bulkUpload) {
                Log::warning("Bulk upload record not found in getImportStatistics", [
                    'file_path' => $filePath,
                    'box_id'    => $boxUser->box_id,
                    'file_name' => basename($filePath),
                ]);
            }

            // If database record exists, use it as source of truth
            if ($bulkUpload) {
                $dbStats = [
                    'total'          => $bulkUpload->total_rows ?? 0,
                    'processed'      => $bulkUpload->processed_rows ?? 0,
                    'successful'     => $bulkUpload->successful_rows ?? 0,
                    'skipped'        => $bulkUpload->skipped_rows ?? 0,
                    'duplicates'     => $bulkUpload->duplicate_rows ?? 0,
                    'percentage'     => (float) ($bulkUpload->percentage ?? 0),
                    'status'         => $bulkUpload->status ?? 'pending',
                    'errors'         => $bulkUpload->error_message ? [$bulkUpload->error_message] : [],
                    'started_at'     => $bulkUpload->started_at ? $bulkUpload->started_at->toDateTimeString() : null,
                    'bulk_upload_id' => $bulkUpload->id,
                ];

                // Merge cache stats with DB stats (DB takes precedence for counts, cache for real-time updates)
                if ($stats) {
                    // Use DB for final counts, but keep cache for real-time processing updates
                    if ($bulkUpload->status === 'processing') {
                        // During processing, merge cache with DB - use cache for processed/successful counts (more frequent updates)
                        // but use DB for total_rows (more accurate)
                        $stats = array_merge($dbStats, [
                            'processed'  => max($stats['processed'] ?? 0, $dbStats['processed']),
                            'successful' => max($stats['successful'] ?? 0, $dbStats['successful']),
                            'skipped'    => max($stats['skipped'] ?? 0, $dbStats['skipped']),
                            'duplicates' => max($stats['duplicates'] ?? 0, $dbStats['duplicates']),
                            'percentage' => $stats['percentage'] ?? $dbStats['percentage'],
                        ]);
                        // Ensure total comes from DB (more accurate)
                        $stats['total'] = $dbStats['total'];
                    } else {
                        // For completed/failed/cancelled, use DB as source of truth
                        $stats = $dbStats;
                    }
                } else {
                    $stats = $dbStats;
                }
            }

            if (! $stats) {
                // Check if job is queued but not started yet
                // Return pending status with message
                return response()->json([
                    'status'  => 'pending',
                    'message' => 'Statistics not available yet. Import may still be processing.',
                    'stats'   => [
                        'total'      => 0,
                        'processed'  => 0,
                        'successful' => 0,
                        'skipped'    => 0,
                        'duplicates' => 0,
                        'percentage' => 0,
                        'status'     => 'pending',
                        'errors'     => [],
                    ],
                ]);
            }

            // Ensure stats has all required fields
            $stats = array_merge([
                'total'      => 0,
                'processed'  => 0,
                'successful' => 0,
                'skipped'    => 0,
                'duplicates' => 0,
                'percentage' => 0,
                'status'     => 'pending',
                'errors'     => [],
            ], $stats);

            return response()->json([
                'status' => 'success',
                'stats'  => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to get statistics: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Datatable endpoint for bulk uploads list (Yajra DataTables server-side).
     */
    public function bulkUploadsData(Request $request)
    {
        try {
            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (! $boxUser) {
                return DataTables::of(BulkUpload::query()->whereRaw('1 = 0'))->make(true);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('integrations', 'read', 'bulk_upload'))) {
                 return DataTables::of(BulkUpload::query()->whereRaw('1 = 0'))->make(true);
            }

            $query = BulkUpload::where('box_id', $boxUser->box_id)
                ->with('user:id,name')
                ->withCount('leads')
                ->orderByDesc('id');

            return DataTables::of($query)
                ->addColumn('file_size_formatted', function (BulkUpload $upload) {
                    return $upload->formatted_file_size;
                })
                ->addColumn('uploaded_by_name', function (BulkUpload $upload) {
                    return $upload->user->name ?? 'Unknown';
                })
                ->addColumn('uploaded_at', function (BulkUpload $upload) {
                    return $upload->created_at ? $upload->created_at->format('d-m-Y h:i A') : '';
                })
                ->addColumn('processed_at_formatted', function (BulkUpload $upload) {
                    return $upload->processed_at ? $upload->processed_at->format('d-m-Y h:i A') : '-';
                })
                ->addColumn('file_type', function (BulkUpload $upload) {
                    return strtoupper($upload->file_type);
                })
                ->addColumn('report_progress', function (BulkUpload $upload) {
                    return (float) ($upload->report_progress ?? 0);
                })
                ->addColumn('leads_count', function (BulkUpload $upload) {
                    return $upload->leads_count ?? 0;
                })
                ->addColumn('original_file_name', function (BulkUpload $upload) {
                    return $upload->original_file_name ?? $upload->file_name;
                })
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Bulk uploads datatable error', [
                'message' => $e->getMessage(),
            ]);
            return response()->json([
                'status'  => false,
                'message' => 'Failed to load bulk uploads.',
            ], 500);
        }
    }

    /**
     * Get all bulk uploads for the current user's box
     */
    public function getBulkUploads(Request $request)
    {
        try {
            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (! $boxUser) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Box user not found',
                ], 404);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('integrations', 'read', 'bulk_upload'))) {
                 return response()->json(['status' => 'error', 'message' => 'You do not have permission to view bulk uploads.'], 403);
            }

            $bulkUploads = BulkUpload::where('box_id', $boxUser->box_id)
                ->with('user:id,name')
                ->withCount('leads')
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($upload) {
                    // Use stored report_file_url if available, otherwise generate it
                    // This ensures we always have the complete URL
                    $reportFileUrl = $upload->report_file_url;
                    if (!$reportFileUrl && $upload->report_file_path && Storage::disk('public')->exists($upload->report_file_path)) {
                        $reportFileUrl = url(Storage::disk('public')->url($upload->report_file_path));
                        // Update the database with the URL for future use
                        $upload->update(['report_file_url' => $reportFileUrl]);
                    }


                    return [
                        'id'                 => $upload->id,
                        'file_name'          => $upload->file_name,
                        'original_file_name' => $upload->original_file_name ?? $upload->file_name,
                        'file_size'          => $upload->formatted_file_size,
                        'file_type'          => strtoupper($upload->file_type),
                        'file_path'          => $upload->file_path,
                        'status'             => $upload->status,
                        'report_status'      => $upload->report_status,
                        'report_progress'    => (float) ($upload->report_progress ?? 0),
                        'report_file_path'   => $upload->report_file_path,
                        'report_file_url'    => $reportFileUrl,
                        'total_rows'         => $upload->total_rows,
                        'processed_rows'     => $upload->processed_rows,
                        'successful_rows'    => $upload->successful_rows,
                        'skipped_rows'       => $upload->skipped_rows,
                        'duplicate_rows'     => $upload->duplicate_rows,
                        'percentage'         => $upload->percentage,
                        'leads_count'        => $upload->leads_count ?? 0,
                        'uploaded_by'        => $upload->user->name ?? 'Unknown',
                        'uploaded_at'        => $upload->created_at->format('d-m-Y h:i A'),
                        'processed_at'       => $upload->processed_at ? $upload->processed_at->format('d-m-Y h:i A') : null,
                        'error_message'      => $upload->error_message,
                    ];
                });

            return response()->json([
                'status'       => 'success',
                'bulk_uploads' => $bulkUploads,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to get bulk uploads: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fetch headers for an existing bulk upload file so user can remap columns.
     */
    public function getUploadHeaders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (! $boxUser) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Box user not found',
                ], 404);
            }

            $bulkUpload = BulkUpload::where('id', $request->id)
                ->where('box_id', $boxUser->box_id)
                ->first();

            if (! $bulkUpload) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Bulk upload not found',
                ], 404);
            }

            $storedPath = ltrim($bulkUpload->file_path, '/');
            $fullPath   = storage_path('app/public/' . $storedPath);

            if (! file_exists($fullPath)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Stored file not found. Please re-upload the file.',
                ], 404);
            }

            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            try {
                if (in_array($extension, ['xlsx', 'xls'])) {
                    $headers = $this->getExcelHeaders($fullPath);
                } else {
                    $headers = $this->getCsvHeaders($fullPath);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Failed to read stored file: ' . $e->getMessage(),
                ], 500);
            }

            return response()->json([
                'status'             => 'success',
                'headers'            => $headers,
                'file_path'          => $bulkUpload->file_path,
                'file_type'          => $bulkUpload->file_type,
                'create_duplicate'   => (bool) $bulkUpload->create_duplicate,
                'assign_to_enabled'  => false,
                'status_enabled'     => false,
                'mappings'           => $bulkUpload->mappings ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch file headers: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a single bulk upload
     */
    public function deleteBulkUpload(Request $request)
    {
        try {
            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (! $boxUser) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Box user not found',
                ], 404);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('integrations', 'delete', 'bulk_upload'))) {
                 return response()->json(['status' => 'error', 'message' => 'You do not have permission to delete this upload.'], 403);
            }

            $uploadId = $request->id;
            if (! $uploadId) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Upload ID is required',
                ], 400);
            }

            $bulkUpload = BulkUpload::where('id', $uploadId)
                ->where('box_id', $boxUser->box_id)
                ->first();

            if (! $bulkUpload) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Bulk upload not found',
                ], 404);
            }

            $fileName      = $bulkUpload->file_name;
            $filePath      = $bulkUpload->file_path;
            $wasProcessing = $bulkUpload->status === 'processing';

            // If import is currently processing, cancel it first
            if ($wasProcessing) {
                // Set cancellation flag in cache
                $cancelKey = "bulk_upload_cancel_{$boxUser->box_id}_" . md5($filePath);
                cache()->put($cancelKey, true, 3600);

                // Update statistics to show cancelled status
                $statsKey              = "bulk_upload_stats_{$boxUser->box_id}_" . md5($filePath);
                $stats                 = cache()->get($statsKey, []);
                $stats['status']       = 'cancelled';
                $stats['cancelled_at'] = now()->toDateTimeString();
                cache()->put($statsKey, $stats, 3600);

                // Update database record to cancelled
                $bulkUpload->update([
                    'status'        => 'cancelled',
                    'processed_at'  => now(),
                    'error_message' => 'Import cancelled before deletion',
                ]);
            }

            // Delete the uploaded file from storage
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            if ($bulkUpload->report_file_path && Storage::disk('public')->exists($bulkUpload->report_file_path)) {
                Storage::disk('public')->delete($bulkUpload->report_file_path);
            }

            // Clear cache entries
            $cacheKey = "bulk_upload_stats_{$boxUser->box_id}_" . md5($filePath);
            cache()->forget($cacheKey);
            cache()->forget("bulk_upload_file_{$boxUser->box_id}_" . md5($filePath));
            cache()->forget("bulk_upload_job_{$boxUser->box_id}_" . md5($filePath));
            cache()->forget("bulk_upload_cancel_{$boxUser->box_id}_" . md5($filePath));

            // Delete the database record
            $bulkUpload->delete();

            $message = 'Bulk upload "' . $fileName . '" has been deleted successfully.';
            if ($wasProcessing) {
                $message = 'Bulk upload "' . $fileName . '" has been deleted successfully. The import was cancelled before deletion.';
            }

            return response()->json([
                'status'  => 'success',
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('Delete bulk upload error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete bulk upload: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete multiple bulk uploads
     */
    public function deleteAllBulkUploads(Request $request)
    {
        try {
            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (! $boxUser) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Box user not found',
                ], 404);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('integrations', 'delete', 'bulk_upload'))) {
                 return response()->json(['status' => 'error', 'message' => 'You do not have permission to delete uploads.'], 403);
            }

            $uploadIds = $request->ids;
            if (! $uploadIds || ! is_array($uploadIds) || empty($uploadIds)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No uploads selected for deletion',
                ], 400);
            }

            $bulkUploads = BulkUpload::whereIn('id', $uploadIds)
                ->where('box_id', $boxUser->box_id)
                ->get();

            if ($bulkUploads->isEmpty()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No bulk uploads found',
                ], 404);
            }

            $deletedCount   = 0;
            $deletedFiles   = [];
            $cancelledCount = 0;

            foreach ($bulkUploads as $bulkUpload) {
                // If import is currently processing, cancel it first
                if ($bulkUpload->status === 'processing') {
                    $filePath = $bulkUpload->file_path;

                    // Set cancellation flag in cache
                    $cancelKey = "bulk_upload_cancel_{$boxUser->box_id}_" . md5($filePath);
                    cache()->put($cancelKey, true, 3600);

                    // Update statistics to show cancelled status
                    $statsKey              = "bulk_upload_stats_{$boxUser->box_id}_" . md5($filePath);
                    $stats                 = cache()->get($statsKey, []);
                    $stats['status']       = 'cancelled';
                    $stats['cancelled_at'] = now()->toDateTimeString();
                    cache()->put($statsKey, $stats, 3600);

                    // Update database record to cancelled
                    $bulkUpload->update([
                        'status'        => 'cancelled',
                        'processed_at'  => now(),
                        'error_message' => 'Import cancelled before deletion',
                    ]);

                    $cancelledCount++;
                }
                // Delete the uploaded file from storage
                if ($bulkUpload->file_path && Storage::disk('public')->exists($bulkUpload->file_path)) {
                    Storage::disk('public')->delete($bulkUpload->file_path);
                }
                if ($bulkUpload->report_file_path && Storage::disk('public')->exists($bulkUpload->report_file_path)) {
                    Storage::disk('public')->delete($bulkUpload->report_file_path);
                }

                // Clear cache entries
                $filePath = $bulkUpload->file_path;
                $cacheKey = "bulk_upload_stats_{$boxUser->box_id}_" . md5($filePath);
                cache()->forget($cacheKey);
                cache()->forget("bulk_upload_file_{$boxUser->box_id}_" . md5($filePath));
                cache()->forget("bulk_upload_job_{$boxUser->box_id}_" . md5($filePath));
                cache()->forget("bulk_upload_cancel_{$boxUser->box_id}_" . md5($filePath));

                $deletedFiles[] = $bulkUpload->file_name;
                $bulkUpload->delete();
                $deletedCount++;
            }

            $message = "Successfully deleted {$deletedCount} bulk upload(s).";
            if ($cancelledCount > 0) {
                $message .= " {$cancelledCount} import(s) were cancelled before deletion.";
            }

            return response()->json([
                'status'          => 'success',
                'message'         => $message,
                'deleted_count'   => $deletedCount,
                'cancelled_count' => $cancelledCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Delete all bulk uploads error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete bulk uploads: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download sample CSV/Excel file with system fields as headers
     */
    public function downloadSample(Request $request)
    {
        try {
            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (! $boxUser) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Box user not found',
                ], 404);
            }

            $fileType = $request->get('type', 'csv'); // csv or xlsx
            $fileType = in_array($fileType, ['csv', 'xlsx']) ? $fileType : 'csv';

            // Get all system and custom fields
            $systemFields = SystemField::where('box_id', $boxUser->box_id)
                ->where('object', 'leads')
                ->orderBy('sequence')
                ->get();

            $customFields = BoxCustomField::where('box_id', $boxUser->box_id)
                ->where('object', 'leads')
                ->where('status', 'Enabled')
                ->orderBy('ordering')
                ->get();

            // Combine fields
            $headers = [];
            foreach ($systemFields as $field) {
                $headers[] = $field->name;
            }
            foreach ($customFields as $field) {
                $headers[] = $field->name;
            }

            if (empty($headers)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No system fields found',
                ], 404);
            }

            if ($fileType === 'xlsx') {
                // Generate Excel file using Laravel-Excel
                $fileName = 'bulk_upload_sample_' . time() . '.xlsx';

                // Prepare data array with headers and sample row
                $data = [$headers]; // Headers row

                // Add sample data row (optional - can be left empty)
                $sampleData = array_fill(0, count($headers), '');
                if (count($headers) > 0) {
                    $sampleData[0] = 'Sample Data 1';
                }
                if (count($headers) > 1) {
                    $sampleData[1] = 'sample@example.com';
                }
                $data[] = $sampleData;

                // Use Laravel-Excel to generate the file
                $export = new SampleFileExport($data);
                $tempPath = 'temp/' . $fileName;

                Excel::store($export, $tempPath, 'public');

                $fullPath = storage_path('app/public/' . $tempPath);

                return response()->download($fullPath, $fileName, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ])->deleteFileAfterSend(true);
            } else {
                // Generate CSV file
                $fileName = 'bulk_upload_sample_' . time() . '.csv';
                $tempFile = tempnam(sys_get_temp_dir(), $fileName);
                $handle   = fopen($tempFile, 'w');

                // Add BOM for UTF-8 (helps Excel recognize encoding)
                fwrite($handle, "\xEF\xBB\xBF");

                // Write headers
                fputcsv($handle, $headers);

                // Add sample data row (optional)
                $sampleData    = array_fill(0, count($headers), '');
                $sampleData[0] = 'Sample Data 1';
                if (count($headers) > 1) {
                    $sampleData[1] = 'sample@example.com';
                }
                fputcsv($handle, $sampleData);

                fclose($handle);

                return response()->download($tempFile, $fileName, [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                ])->deleteFileAfterSend(true);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to generate sample file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Request generation of a bulk upload report (CSV) through a queued job.
     */
    public function requestReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (! $boxUser) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Box user not found',
                ], 404);
            }

            $bulkUpload = BulkUpload::where('id', $request->id)
                ->where('box_id', $boxUser->box_id)
                ->first();

            if (! $bulkUpload) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Bulk upload not found',
                ], 404);
            }

            $downloadUrl = route('bulk.report.download', [
                'box_slug' => request()->route('box_slug'),
            ]) . '?id=' . $bulkUpload->id;

            if (
                $bulkUpload->report_status === 'completed'
                && $bulkUpload->report_file_path
                && Storage::disk('public')->exists($bulkUpload->report_file_path)
            ) {
                // Use stored report_file_url if available, otherwise generate it
                $reportFileUrl = $bulkUpload->report_file_url;
                if (!$reportFileUrl) {
                    $reportFileUrl = $this->generateReportFileUrl($bulkUpload->report_file_path);
                    // Update the database with the URL for future use
                    if ($reportFileUrl) {
                        $bulkUpload->update(['report_file_url' => $reportFileUrl]);
                    }
                }

                return response()->json([
                    'status'          => 'ready',
                    'download_url'   => $downloadUrl,
                    'report_file_path' => $bulkUpload->report_file_path,
                    'report_file_url' => $reportFileUrl,
                    'progress'       => 100,
                ]);
            }

            if ($bulkUpload->report_status === 'processing') {
                return response()->json([
                    'status'  => 'processing',
                    'message' => 'Report is already being generated. We will notify you once it is ready.',
                    'progress'=> (float) $bulkUpload->report_progress,
                ]);
            }

            $bulkUpload->update([
                'report_status'       => 'processing',
                'report_requested_at' => now(),
                'report_requested_by' => $userId,
                'report_file_path'    => null,
                'report_generated_at' => null,
                'report_error'        => null,
                'report_progress'     => 0,
            ]);

            GenerateBulkUploadReportJob::dispatch($bulkUpload->id, $boxUser->box_id, $userId);

            return response()->json([
                'status'  => 'processing',
                'message' => 'Report generation started. This may take a moment.',
                'progress'=> 0,
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk upload report request failed', [
                'message' => $e->getMessage(),
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to start report generation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Return current report generation status for polling.
     */
    public function reportStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (! $boxUser) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Box user not found',
                ], 404);
            }

            $bulkUpload = BulkUpload::where('id', $request->id)
                ->where('box_id', $boxUser->box_id)
                ->first();

            if (! $bulkUpload) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Bulk upload not found',
                ], 404);
            }

            $response = [
                'status' => $bulkUpload->report_status,
                'message' => null,
                'progress' => (float) $bulkUpload->report_progress,
            ];

            // Add informative messages based on status
            if ($bulkUpload->report_status === 'processing') {
                $progress = (float) $bulkUpload->report_progress;
                if ($progress == 0) {
                    $response['message'] = 'Report generation is starting... Please wait.';
                } elseif ($progress < 50) {
                    $response['message'] = 'Reading and processing data...';
                } elseif ($progress < 90) {
                    $response['message'] = 'Generating report file...';
                } else {
                    $response['message'] = 'Finalizing report... Almost done!';
                }
            } elseif ($bulkUpload->report_status === 'completed') {
                $response['message'] = 'Report is ready for download.';
            } elseif ($bulkUpload->report_status === 'failed') {
                $response['message'] = $bulkUpload->report_error ?: 'Report generation failed.';
            }

            if (
                $bulkUpload->report_status === 'completed'
                && $bulkUpload->report_file_path
                && Storage::disk('public')->exists($bulkUpload->report_file_path)
            ) {
                // Use stored report_file_url if available, otherwise generate it
                $reportFileUrl = $bulkUpload->report_file_url;
                if (!$reportFileUrl && $bulkUpload->report_file_path) {
                    $reportFileUrl = $this->generateReportFileUrl($bulkUpload->report_file_path);
                    // Update the database with the URL for future use
                    if ($reportFileUrl) {
                        $bulkUpload->update(['report_file_url' => $reportFileUrl]);
                    }
                }

                $response['status']       = 'ready';
                $response['download_url'] = route('bulk.report.download', [
                    'box_slug' => request()->route('box_slug'),
                ]) . '?id=' . $bulkUpload->id;
                $response['report_file_path'] = $bulkUpload->report_file_path;
                $response['report_file_url'] = $reportFileUrl;
                $response['progress'] = 100;
            } elseif ($bulkUpload->report_status === 'failed') {
                $response['message'] = $bulkUpload->report_error ?: 'Report generation failed.';
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch report status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Stream the generated report file to the browser.
     */
    public function downloadReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $userId  = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();

        if (! $boxUser) {
            return redirect()->back()->with('error', 'Box user not found');
        }

        $bulkUpload = BulkUpload::where('id', $request->id)
            ->where('box_id', $boxUser->box_id)
            ->first();

        if (! $bulkUpload || ! $bulkUpload->report_file_path) {
            return redirect()->back()->with('error', 'Report not found. Please generate it again.');
        }

        // Check format preference (default to xlsx, but support csv)
        $format = $request->get('format', 'xlsx');

        // Handle both old paths (without folder) and new paths (with folder structure)
        $reportFilePath = $bulkUpload->report_file_path;

        // If path doesn't include the folder structure, try to find it in the folder
        if (!str_contains($reportFilePath, 'bulk_reports/' . $bulkUpload->id . '/')) {
            // Extract filename from old path
            $fileName = basename($reportFilePath);
            // Try new folder structure path
            $newPath = 'bulk_reports/' . $bulkUpload->id . '/' . $fileName;
            if (Storage::disk('public')->exists($newPath)) {
                $reportFilePath = $newPath;
                // Update database with correct path for future downloads
                $bulkUpload->update(['report_file_path' => $newPath]);
            }
        }

        if ($format === 'csv') {
            // Try CSV file first
            $csvFilePath = str_replace('.xlsx', '.csv', $reportFilePath);
            if (Storage::disk('public')->exists($csvFilePath)) {
        $downloadName = 'bulk-upload-report-' . $bulkUpload->id . '.csv';
                return Storage::disk('public')->download($csvFilePath, $downloadName, [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                ]);
            }
        }

        // Default to Excel
        if (! Storage::disk('public')->exists($reportFilePath)) {
            return redirect()->back()->with('error', 'Report file missing. Please regenerate the report.');
        }

        $downloadName = 'bulk-upload-report-' . $bulkUpload->id . '.xlsx';

        return Storage::disk('public')->download($reportFilePath, $downloadName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Serve bulk report file directly via URL (public access)
     * URL format: /bulk_reports/{bulkUploadId}/{fileName}
     */
    public function serveReportFile($bulkUploadId, $fileName)
    {
        try {
            // Construct the file path
            $filePath = 'bulk_reports/' . $bulkUploadId . '/' . $fileName;

            // Check if file exists
            if (!Storage::disk('public')->exists($filePath)) {
                abort(404, 'Report file not found');
            }

            // Verify the bulk upload exists and matches
            $bulkUpload = BulkUpload::find($bulkUploadId);
            if (!$bulkUpload || $bulkUpload->report_file_path !== $filePath) {
                abort(404, 'Report file not found');
            }

            // Return the file
            return Storage::disk('public')->response($filePath, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $e) {
            Log::error('Error serving report file', [
                'bulk_upload_id' => $bulkUploadId,
                'file_name' => $fileName,
                'error' => $e->getMessage(),
            ]);
            abort(404, 'Report file not found');
        }
    }

    /**
     * Generate report file URL in format: /storage/bulk_reports/{id}/{filename}
     */
    protected function generateReportFileUrl($filePath)
    {
        if (!$filePath) {
            return null;
        }

        // Use standard Laravel storage URL format: /storage/path/to/file
        // This generates: http://127.0.0.1:8000/storage/bulk_reports/72/bulk_upload_72_report_20251128_214339.xlsx
        return url(Storage::disk('public')->url($filePath));
    }
}
