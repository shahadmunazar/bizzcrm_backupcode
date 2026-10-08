<?php

namespace App\Imports;

use App\Models\ProductSalt;
use App\Models\BulkImport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Support\Facades\Log;

class ProductSaltImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue, WithEvents
{
    protected $boxId;
    protected $userId;
    protected $bulkImportId;

    public function __construct($boxId, $userId, $bulkImportId)
    {
        $this->boxId = $boxId;
        $this->userId = $userId;
        $this->bulkImportId = $bulkImportId;
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

            if (empty($row['name'])) {
                $errors[] = "Missing required field 'name' for a row.";
                continue;
            }

            try {
                $exists = ProductSalt::where('box_id', $this->boxId)
                    ->where('name', $row['name'])
                    ->exists();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                ProductSalt::create([
                    'box_id' => $this->boxId,
                    'name' => $row['name'],
                    'status' => 'Yes',
                ]);

                $successCount++;

            } catch (\Exception $e) {
                Log::error("Salt Bulk Import Error on {$row['name']}: " . $e->getMessage());
                $errors[] = "Error [{$row['name']}]: " . $e->getMessage();
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
