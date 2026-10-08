<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\SalaryReport;
use Carbon\Carbon;

class SalaryReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filters;
    protected $fields;
    protected $fieldNames;

    public function __construct($filters, $fields, $fieldNames)
    {
        $this->filters = $filters;
        $this->fields = $fields;
        $this->fieldNames = $fieldNames;
    }

    public function collection()
    {
        $query = SalaryReport::where('box_id', $this->filters['box_id'])
            ->with([
                'boxUser.user',
                'boxUser.departmentRelation',
                'boxUser.designationRelation',
                'transactions'
            ]);

        // Apply search
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('gross_salary', 'like', "%{$search}%")
                    ->orWhere('net_salary', 'like', "%{$search}%")
                    ->orWhereHas('boxUser', function ($q2) use ($search) {
                        $q2->where('employee_id', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($q3) use ($search) {
                                $q3->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // Apply filters
        if (!empty($this->filters['designation_id'])) {
            $query->whereHas('boxUser', function ($q) {
                $q->where('designation', $this->filters['designation_id']);
            });
        }

        // Apply Month and Year filters if provided
        if (!empty($this->filters['export_month'])) {
            $query->where('month', str_pad($this->filters['export_month'], 2, '0', STR_PAD_LEFT));
        }

        if (!empty($this->filters['export_year'])) {
            $query->where('year', $this->filters['export_year']);
        }

        if (!empty($this->filters['date_range'])) {
            $dates = explode(' - ', $this->filters['date_range']);
            if (count($dates) == 2) {
                try {
                    $from = Carbon::parse(trim($dates[0]))->startOfDay();
                    $to = Carbon::parse(trim($dates[1]))->endOfDay();
                    $query->whereBetween('created_at', [$from, $to]);
                } catch (\Exception $e) {
                    try {
                        $from = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                        $to = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                        $query->whereBetween('created_at', [$from, $to]);
                    } catch (\Exception $e2) {
                        // Ignore
                    }
                }
            }
        }

        // Apply sorting
        $sortOrder = $this->filters['sort_order'] ?? 'recent';
        if ($sortOrder === 'recent') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sortOrder === 'descending') {
            $query->orderBy('id', 'desc');
        } elseif ($sortOrder === 'ascending') {
            $query->orderBy('id', 'asc');
        } else {
            $query->orderBy('year', 'desc')->orderBy('month', 'desc')->orderBy('id', 'desc');
        }

        return $query->get();
    }

    public function headings(): array
    {
        return array_values($this->fieldNames);
    }

    public function map($report): array
    {
        $data = [];
        foreach ($this->fields as $field) {
            switch ($field) {
                case 'emp_id':
                    $data[] = $report->boxUser->employee_id ?? $report->boxUser->id ?? '-';
                    break;
                case 'name':
                    $data[] = $report->boxUser->user->name ?? $report->boxUser->first_name ?? '-';
                    break;
                case 'email':
                    $data[] = $report->boxUser->user->email ?? '-';
                    break;
                case 'phone':
                    $data[] = $report->boxUser->phone ?? '-';
                    break;
                case 'designation':
                    $data[] = $report->boxUser->designationRelation->name ?? '-';
                    break;
                case 'joining_date':
                    $data[] = optional($report->boxUser->joining_date)->format('d M Y') ?? '-';
                    break;
                case 'month_year':
                    $data[] = Carbon::create($report->year, $report->month, 1)->format('F Y');
                    break;
                case 'total_holidays':
                    $data[] = number_format($report->total_holidays ?? 0, 2);
                    break;
                case 'gross_salary':
                    $data[] = number_format($report->gross_salary ?? 0, 2);
                    break;
                case 'net_salary':
                    $data[] = number_format($report->net_salary ?? 0, 2);
                    break;
                case 'total_deductions':
                    $data[] = number_format($report->total_deductions ?? 0, 2);
                    break;
                case 'paid_days':
                    $data[] = number_format($report->paid_days ?? 0, 2);
                    break;
                case 'status':
                    $latestTransaction = $report->transactions->last();
                    $data[] = $latestTransaction ? ucfirst($latestTransaction->status) : 'N/A';
                    break;
                case 'created_at':
                    $data[] = optional($report->created_at)->format('d M Y H:i') ?? '-';
                    break;
                default:
                    $data[] = '';
            }
        }
        return $data;
    }
}







