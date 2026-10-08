<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompanyExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $boxId;

    public function __construct($boxId)
    {
        $this->boxId = $boxId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Company::where('box_id', $this->boxId)
            ->with(['state', 'city', 'creator', 'updater'])
            ->orderBy('bill_name', 'asc')
            ->get();
    }

    public function map($company): array
    {
        $params = $company->params ?? [];

        return [
            $company->id,
            $company->bill_name,
            $company->bill_phone,
            $company->pan,
            $company->gstin,
            $company->bill_address,
            $company->bill_pin_code,
            $company->state ? $company->state->name : '',
            $company->city ? $company->city->name : '',
            $company->status,
            $company->creator ? $company->creator->name : '',
            $company->updater ? $company->updater->name : '',
            isset($params['orientation']) ? ($params['orientation'] == 'P' ? 'Portrait' : 'Landscape') : '',
            $params['packing'] ?? 'No',
            $params['hsn'] ?? 'No',
            $params['mrp'] ?? 'No',
            $params['jurisdiction'] ?? '',
            $company->created_at ? $company->created_at->format('Y-m-d H:i:s') : '',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Company Name',
            'Phone',
            'PAN',
            'GSTIN',
            'Address',
            'Pin Code',
            'State',
            'City',
            'Status',
            'Added By',
            'Last Updated By',
            'Orientation',
            'Show Packing',
            'Show HSN',
            'Show MRP',
            'Jurisdiction',
            'Created At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
