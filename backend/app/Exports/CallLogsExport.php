<?php

namespace App\Exports;

use App\Models\CallLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CallLogsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Name',
            'User Name',
            'Device ID',
            'From',
            'To',
            'Type',
            'Status',
            'Duration (sec)',
            'Start Time',
            'End Time',
            'Start Lat',
            'Start Long',
            'End Lat',
            'End Long',
            'Raw Timestamp'
        ];
    }

    public function map($row): array
    {
        return [
            $row->name ?? '-',
            $row->creator ? $row->creator->name . ' (' . $row->creator->email . ')' : '-',
            $row->device_id ?? '-',
            $row->from ?? '-',
            $row->to ?? '-',
            ucfirst($row->type) ?? '-',
            ucfirst($row->status) ?? '-',
            $row->duration ?? '-',
            $row->start_time ? \Carbon\Carbon::parse($row->start_time)->format('Y-m-d h:i:s A') : '-',
            $row->end_time ? \Carbon\Carbon::parse($row->end_time)->format('Y-m-d h:i:s A') : '-',
            $row->start_lat ?? '-',
            $row->start_long ?? '-',
            $row->end_lat ?? '-',
            $row->end_long ?? '-',
            $row->rawTimestamp ? \Carbon\Carbon::createFromTimestamp(intval($row->rawTimestamp / 1000))->format('Y-m-d h:i:s A') : '-'
        ];
    }
}
