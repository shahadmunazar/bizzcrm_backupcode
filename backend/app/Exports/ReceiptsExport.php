<?php

namespace App\Exports;

use App\Models\Accounts\Receipt;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class ReceiptsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Receipt::query()->with(['company', 'lead', 'bank']);

        if (!empty($this->filters['box_id'])) {
            $query->where('box_id', $this->filters['box_id']);
        }

        if (!empty($this->filters['start_date'])) {
            $query->whereDate('receipt_date', '>=', $this->filters['start_date']);
        }
        if (!empty($this->filters['end_date'])) {
            $query->whereDate('receipt_date', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['bookingrange'])) {
            $range = explode(' - ', $this->filters['bookingrange']);
            if (count($range) == 2) {
                $start = \Carbon\Carbon::parse($range[0])->startOfDay();
                $end = \Carbon\Carbon::parse($range[1])->endOfDay();
                $query->whereBetween('receipt_date', [$start, $end]);
            }
        }

        if (!empty($this->filters['lead_name'])) {
            $query->whereHas('lead', function ($q) {
                $q->where('name', 'like', '%' . $this->filters['lead_name'] . '%');
            });
        }

        if (!empty($this->filters['company_id'])) {
            $query->where('company_id', $this->filters['company_id']);
        }

        if (!empty($this->filters['payment_mode'])) {
            $query->where('payment_mode', $this->filters['payment_mode']);
        }

        if (!empty($this->filters['search_global'])) {
            $search = $this->filters['search_global'];
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', '%' . $search . '%')
                    ->orWhereHas('lead', function ($ql) use ($search) {
                        $ql->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('company', function ($qc) use ($search) {
                        $qc->where('bill_name', 'like', '%' . $search . '%');
                    });
            });
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Invoice Number',
            'Lead/Customer',
            'Company',
            'Date',
            'Amount',
            'Payment Mode',
            'Bank',
            'Reference',
            'Created At'
        ];
    }

    public function map($receipt): array
    {
        return [
            $receipt->id,
            $receipt->invoice_number,
            $receipt->lead ? $receipt->lead->name : '-',
            $receipt->company ? $receipt->company->bill_name : '-',
            $receipt->receipt_date,
            $receipt->amount,
            $receipt->payment_mode,
            $receipt->bank ? $receipt->bank->name : '-',
            $receipt->reference,
            $receipt->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
