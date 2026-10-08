<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BulkUploadFullReport implements WithMultipleSheets
{
    protected $bulkUploadId;
    protected $boxId;
    protected $headers;
    protected $mappings;
    protected $summary;

    public function __construct($bulkUploadId, $boxId, $headers, $mappings, $summary)
    {
        $this->bulkUploadId = $bulkUploadId;
        $this->boxId = $boxId;
        $this->headers = $headers;
        $this->mappings = $mappings;
        $this->summary = $summary;
    }

    public function sheets(): array
    {
        // Return array of sheet exports - each sheet implements WithTitle for sheet naming
        return [
            new ProcessedExport($this->bulkUploadId, $this->boxId, $this->headers, $this->mappings),
            new DuplicateExport($this->bulkUploadId, $this->boxId, $this->headers, $this->mappings),
            new SkippedExport($this->bulkUploadId, $this->boxId, $this->headers, $this->mappings),
            new SummaryExport($this->summary),
        ];
    }
}

