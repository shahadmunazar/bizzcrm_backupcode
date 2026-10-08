<?php

namespace App\Imports;

use App\Models\ProductHsn;
use App\Models\BulkImport;
use App\Services\ProductHsnService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ProductHsnImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue, WithEvents
{
    protected $boxId;
    protected $userId;
    protected $bulkImportId;
    protected $service;

    public function __construct($boxId, $userId, $bulkImportId)
    {
        $this->boxId = $boxId;
        $this->userId = $userId;
        $this->bulkImportId = $bulkImportId;
        $this->service = new ProductHsnService();
    }

    public function collection(Collection $rows)
    {
        $bulkImport = BulkImport::find($this->bulkImportId);
        if (!$bulkImport) {
            return;
        }

        $successCount = 0;
        $skippedCount = 0;
        $emptyRowsCount = 0;
        $errors = $bulkImport->errors ?? [];

        foreach ($rows as $row) {
            $isEmpty = true;
            foreach ($row->toArray() as $val) {
                if ($val !== null && $val !== '') {
                    $isEmpty = false;
                    break;
                }
            }
            if ($isEmpty) {
                $emptyRowsCount++;
                continue;
            }

            if (empty($row['code']) || !isset($row['sgst']) || !isset($row['cgst']) || !isset($row['igst'])) {
                $errors[] = "Missing required fields (Code, SGST, CGST, IGST) for row with Code: {$row['code']}";
                continue;
            }

            try {
                $fromDate = null;
                if (!empty($row['effective_date_from'])) {
                    $val = trim($row['effective_date_from']);
                    if (is_numeric($val)) {
                        $fromDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format('Y-m-d');
                    } else {
                        $time = strtotime(str_replace('/', '-', $val));
                        $fromDate = $time ? date('Y-m-d', $time) : null;
                    }
                }

                $toDate = null;
                if (!empty($row['effective_date_to'])) {
                    $val = trim($row['effective_date_to']);
                    if (is_numeric($val)) {
                        $toDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format('Y-m-d');
                    } else {
                        $time = strtotime(str_replace('/', '-', $val));
                        $toDate = $time ? date('Y-m-d', $time) : null;
                    }
                }

                $this->service->validateDateOverlap($this->boxId, $row['code'], $fromDate, $toDate);

                ProductHsn::create([
                    'box_id' => $this->boxId,
                    'code' => $row['code'],
                    'short_name' => $row['short_name'] ?? null,
                    'sgst' => $row['sgst'],
                    'cgst' => $row['cgst'],
                    'igst' => $row['igst'],
                    'cess' => $row['cess'] ?? 0.00,
                    'type' => $row['type'] ?? 'Goods',
                    'uqc' => $row['uqc'] ?? null,
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'created_by' => $this->userId,
                    'status' => 'Yes',
                ]);

                $successCount++;

            } catch (ValidationException $e) {
                $errorMsg = collect($e->errors())->flatten()->first();
                if (str_contains(strtolower($errorMsg), 'overlap')) {
                    $skippedCount++;
                    continue;
                }
                $errors[] = "Error [{$row['code']}]: {$errorMsg}";
            } catch (\Exception $e) {
                Log::error("HSN Bulk Import Error: " . $e->getMessage());
                $errors[] = "Error [{$row['code']}]: Format error or unexpected issue.";
            }
        }

        $processedThisChunk = $rows->count() - $emptyRowsCount;
        
        if ($processedThisChunk > 0) {
            $bulkImport->increment('processed_rows', $processedThisChunk);
        }
        if ($emptyRowsCount > 0) {
            $bulkImport->decrement('total_rows', $emptyRowsCount);
        }
        
        $bulkImport->increment('success_rows', $successCount);
        if ($skippedCount > 0) {
            $bulkImport->increment('skipped_rows', $skippedCount);
        }
        if (count($errors) > 0) {
            $bulkImport->update(['errors' => $errors]);
        }

        $bulkImport->refresh();
        if ($bulkImport->processed_rows >= $bulkImport->total_rows) {
            $bulkImport->update(['status' => 'completed']);
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function(BeforeImport $event) {
                $totalRows = array_sum($event->reader->getTotalRows()) - 1; // -1 for header
                $bulkImport = BulkImport::find($this->bulkImportId);
                if ($bulkImport) {
                    if ($totalRows > 0) {
                        $bulkImport->update(['total_rows' => $totalRows]);
                    } else {
                        $bulkImport->update(['total_rows' => 0, 'status' => 'completed']);
                    }
                }
            },
            ImportFailed::class => function(ImportFailed $event) {
                $bulkImport = BulkImport::find($this->bulkImportId);
                if ($bulkImport) {
                    $errors = $bulkImport->errors ?? [];
                    $errors[] = "Import failed: " . $event->getException()->getMessage();
                    $bulkImport->update([
                        'status' => 'failed',
                        'errors' => $errors
                    ]);
                }
            },
        ];
    }
}
