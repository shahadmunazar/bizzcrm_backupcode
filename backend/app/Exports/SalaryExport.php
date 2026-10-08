<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Salary;
use App\Models\SalaryReport;
use Carbon\Carbon;

class SalaryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
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
        $query = Salary::where('box_id', $this->filters['box_id'])
            ->with([
                'boxuser.user',
                'boxuser.departmentRelation',
                'boxuser.designationRelation',
                'creator',
                'updater'
            ]);

        // Apply search
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('gross_salary', 'like', "%{$search}%")
                    ->orWhere('net_salary', 'like', "%{$search}%")
                    ->orWhereHas('boxuser', function ($q2) use ($search) {
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
        if (!empty($this->filters['department_id'])) {
            $query->whereHas('boxuser', function ($q) {
                $q->where('department', $this->filters['department_id']);
            });
        }

        if (!empty($this->filters['designation_id'])) {
            $query->whereHas('boxuser', function ($q) {
                $q->where('designation', $this->filters['designation_id']);
            });
        }

        // Apply Month and Year filters if provided
        if (!empty($this->filters['export_month'])) {
            $query->whereMonth('created_at', $this->filters['export_month']);
        }

        if (!empty($this->filters['export_year'])) {
            $query->whereYear('created_at', $this->filters['export_year']);
        }

        if (!empty($this->filters['date_range'])) {
            $dates = explode(' - ', $this->filters['date_range']);
            if (count($dates) == 2) {
                try {
                    // Try parsing as M/D/YYYY or MM/DD/YYYY (common for the date picker used)
                    $from = Carbon::parse(trim($dates[0]))->startOfDay();
                    $to = Carbon::parse(trim($dates[1]))->endOfDay();
                    $query->whereBetween('created_at', [$from, $to]);
                } catch (\Exception $e) {
                    // Fallback to other common formats if Carbon::parse fails
                    try {
                        $from = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                        $to = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                        $query->whereBetween('created_at', [$from, $to]);
                    } catch (\Exception $e2) {
                        // Ignore if all parsing fails
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
            $query->orderBy('id', 'desc');
        }

        return $query->get();
    }

    public function headings(): array
    {
        return array_values($this->fieldNames);
    }

    public function map($salary): array
    {
        $data = [];
        foreach ($this->fields as $field) {
            switch ($field) {
                case 'emp_id':
                    $data[] = $salary->boxuser->id ?? '-';
                    break;
                case 'name':
                    $data[] = $salary->boxuser->user->name ?? '-';
                    break;
                case 'email':
                    $data[] = $salary->boxuser->user->email ?? '-';
                    break;
                case 'phone':
                    $data[] = $salary->boxuser->phone ?? '-';
                    break;
                case 'designation':
                    $data[] = $salary->boxuser->designationRelation->name ?? '-';
                    break;
                case 'joining_date':
                    $data[] = optional($salary->boxuser->joining_date)->format('d M Y') ?? '-';
                    break;
                case 'salary':
                    $data[] = $salary->ctc_amount ?? 0;
                    break;
                case 'final_salary':
                    $latestReport = SalaryReport::where('box_user_id', $salary->box_user_id)
                        ->where('box_id', $salary->box_id)
                        ->orderBy('year', 'desc')
                        ->orderBy('month', 'desc')
                        ->orderBy('id', 'desc')
                        ->first();
                    $data[] = $latestReport ? number_format($latestReport->net_salary ?? 0, 2) : '-';
                    break;
                case 'created_at':
                    $data[] = optional($salary->created_at)->format('d M Y H:i') ?? '-';
                    break;
                case 'updated_at':
                    $data[] = optional($salary->updated_at)->format('d M Y H:i') ?? '-';
                    break;
                case 'created_by':
                    $data[] = $salary->creator->name ?? '-';
                    break;
                case 'updated_by':
                    $data[] = $salary->updater->name ?? '-';
                    break;
                default:
                    $data[] = '';
            }
        }
        return $data;
    }
}

