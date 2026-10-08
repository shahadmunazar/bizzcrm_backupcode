<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProcessedExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading, WithTitle
{
    protected $bulkUploadId;
    protected $boxId;
    protected $headers;
    protected $mappings;

    public function __construct($bulkUploadId, $boxId, $headers, $mappings)
    {
        $this->bulkUploadId = $bulkUploadId;
        $this->boxId = $boxId;
        $this->headers = $headers;
        $this->mappings = $mappings;
    }

    public function query()
    {
        return Lead::query()
            ->where('bulk_upload_id', $this->bulkUploadId)
            ->where('box_id', $this->boxId)
            ->whereRaw("LOWER(TRIM(COALESCE(process_uploading, ''))) = ?", ['success'])
            ->orderBy('id');
    }

    public function headings(): array
    {
        // Start with Bulk Upload ID, then original headers and Process Uploading
        $h = $this->headers;
        if (!in_array('Process Uploading', $h)) {
            $h[] = 'Process Uploading';
        }
        // Prepend Bulk Upload ID column
        array_unshift($h, 'Bulk Upload ID');
        return $h;
    }

    public function map($lead): array
    {
        // First column: Bulk Upload ID
        $row = [
            $lead->bulk_upload_id,
        ];

        // Then map all original headers
        foreach ($this->headers as $header) {
            $field = $this->mappings[$header] ?? null;
            if ($field) {
                $value = $this->getLeadFieldValue($lead, $field);
            } else {
                // Try to match header name directly
                $headerLower = strtolower(trim($header));
                $value = $this->getLeadFieldValueByHeader($lead, $headerLower);
            }
            $row[] = $value;
        }

        if (!in_array('Process Uploading', $this->headers)) {
            $row[] = $lead->process_uploading ?? 'Success';
        }

        return $row;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function title(): string
    {
        return 'Processed Data';
    }

    protected function getLeadFieldValue($lead, $fieldKey)
    {
        switch ($fieldKey) {
            case 'lead_status_id':
            case 'lead_status':
                return $lead->leadStatus->name ?? $lead->lead_status_id ?? '';
            case 'assigned_to':
            case 'owner_id':
                return $lead->assignedTo->name ?? $lead->assigned_to ?? '';
            case 'lead_source_id':
            case 'source':
                return $lead->source->name ?? $lead->lead_source_id ?? '';
            case 'company_name':
            case 'company':
                return $lead->company_name ?? '';
            default:
                $value = data_get($lead, $fieldKey, '');
                if ($value === null) {
                    return '';
                }
                if ($value instanceof \Carbon\Carbon) {
                    return $value->format('Y-m-d H:i:s');
                }
                return is_scalar($value) ? (string) $value : json_encode($value);
        }
    }

    protected function getLeadFieldValueByHeader($lead, $headerLower)
    {
        $fieldMap = [
            'name' => 'name',
            'lead name' => 'name',
            'full name' => 'name',
            'email' => 'email',
            'e-mail' => 'email',
            'email address' => 'email',
            'phone' => 'phone',
            'mobile' => 'phone',
            'phone number' => 'phone',
            'contact number' => 'phone',
            'company' => 'company_name',
            'company name' => 'company_name',
        ];

        $fieldKey = $fieldMap[$headerLower] ?? null;
        if ($fieldKey) {
            return $this->getLeadFieldValue($lead, $fieldKey);
        }

        if (isset($lead->$headerLower)) {
            $value = $lead->$headerLower;
            if ($value instanceof \Carbon\Carbon) {
                return $value->format('Y-m-d H:i:s');
            }
            return is_scalar($value) ? (string) $value : '';
        }

        return '';
    }
}

