<?php

namespace App\Exports;

use App\Models\BoxUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $boxId;

    public function __construct($boxId)
    {
        $this->boxId = $boxId;
    }

    public function collection()
    {
        return BoxUser::where('box_id', $this->boxId)
            ->with('bankDetails')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'User ID',
            'Name',
            'PAN Number',
            'Employee ID',
            'Phone',
            'Email',
            'Joining Date',
            'Department',
            'Profile Image',
            'Assign Tour',
            'Assign Customer',
            'Profile Image Path',
            'Status',
            'Bank Name',
            'Account Number',
            'Branch',
            'IFSC Code',
            'Created At',
            'Updated At',
            'Deleted At',
        ];
    }

    public function map($employee): array
    {
        return [
            $employee->id,
            $employee->user_id,
            $employee->name,
            $employee->pan_number,
            $employee->employee_id,
            $employee->phone,
            $employee->email,
            optional($employee->joining_date)->format('Y-m-d'),
            $employee->department,
            $employee->profile_image,
            $employee->assign_tour,
            $employee->assign_customer,
            $employee->profile_image_path,
            $employee->status,
            optional($employee->bankDetail)->bank_name,
            optional($employee->bankDetail)->account_number,
            optional($employee->bankDetail)->branch,
            optional($employee->bankDetail)->ifsc_code,
            optional($employee->created_at)->toDateTimeString(),
            optional($employee->updated_at)->toDateTimeString(),
            optional($employee->deleted_at)->toDateTimeString(),
        ];
    }
}
