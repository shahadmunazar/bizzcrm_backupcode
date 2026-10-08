<?php

namespace App\Imports;

use App\Models\Product;
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

class ProductImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue, WithEvents
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

            $requiredFields = ['product_name', 'code', 'packing', 'company', 'salt', 'hsn_sac', 'erp_to_erp'];
            $missing = [];
            foreach ($requiredFields as $req) {
                if (empty($row[$req])) {
                    $missing[] = $req;
                }
            }

            if (!empty($missing)) {
                $errors[] = "Missing required fields (" . implode(', ', $missing) . ") for row code: " . ($row['code'] ?? 'Unknown');
                continue;
            }

            // Look up the IDs by name as requested to verify existence
            $companyObj = \App\Models\Company::where('box_id', $this->boxId)->where('bill_name', $row['company'])->first();
            $saltObj = \App\Models\ProductSalt::where('box_id', $this->boxId)->where('name', $row['salt'])->first();
            $hsnObj = \App\Models\ProductHsn::where('box_id', $this->boxId)->where('code', $row['hsn_sac'])->first();

            if (!$companyObj) {
                $errors[] = "Company '{$row['company']}' not found for row code: {$row['code']}";
                continue;
            }
            if (!$saltObj) {
                $errors[] = "Salt '{$row['salt']}' not found for row code: {$row['code']}";
                continue;
            }
            if (!$hsnObj) {
                $errors[] = "HSN/SAC '{$row['hsn_sac']}' not found for row code: {$row['code']}";
                continue;
            }

            try {
                // Check if product code already exists for this box
                $exists = Product::where('box_id', $this->boxId)
                    ->where('code', $row['code'])
                    ->exists();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                Product::create([
                    'box_id' => $this->boxId,
                    'code' => $row['code'],
                    'product_name' => $row['product_name'],
                    'type' => $row['type'] ?? 'Goods',
                    'status' => $row['status'] ?? 'Yes',
                    'packing' => $row['packing'] ?? null,
                    'unit' => $row['unit'] ?? null,
                    'company' => $row['company'] ?? null,
                    'salt' => $row['salt'] ?? null,
                    'hsn_sac' => $row['hsn_sac'] ?? null,
                    'erp_to_erp' => $row['erp_to_erp'] ?? null,
                    
                    // Pricing & Tax
                    'mrp' => isset($row['mrp']) ? (float)$row['mrp'] : 0,
                    'purchase_rate' => isset($row['purchase_rate']) ? (float)$row['purchase_rate'] : 0,
                    'cost' => isset($row['cost']) ? (float)$row['cost'] : 0,
                    'rate_a' => isset($row['rate_a']) ? (float)$row['rate_a'] : 0,
                    'rate_b' => isset($row['rate_b']) ? (float)$row['rate_b'] : 0,
                    'rate_c' => isset($row['rate_c']) ? (float)$row['rate_c'] : 0,
                    
                    'sgst_percent' => isset($row['sgst_percent']) ? (float)$row['sgst_percent'] : 0,
                    'cgst_percent' => isset($row['cgst_percent']) ? (float)$row['cgst_percent'] : 0,
                    'igst_percent' => isset($row['igst_percent']) ? (float)$row['igst_percent'] : 0,
                    'cess_percent' => isset($row['cess_percent']) ? (float)$row['cess_percent'] : 0,
                    
                    // Inventory
                    'minimum_qty' => isset($row['minimum_qty']) ? (int)$row['minimum_qty'] : null,
                    'maximum_qty' => isset($row['maximum_qty']) ? (int)$row['maximum_qty'] : null,
                    'reorder_qty' => isset($row['reorder_qty']) ? (int)$row['reorder_qty'] : null,
                ]);

                $successCount++;

            } catch (\Exception $e) {
                Log::error("Product Bulk Import Error on row [{$row['code']}]: " . $e->getMessage());
                $errors[] = "Error processing Product Code [{$row['code']}]: " . $e->getMessage();
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
        return 500; // slightly smaller chunk since products have many columns
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
