<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceAdminExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $boxName;

    public function __construct(string $boxName)
    {
        $this->boxName = $boxName;
    }

    public function collection(): Collection
    {
        return Attendance::with([
            'boxUser.user',
            'boxUser.departmentRelation',
            'boxUser.designationRelation',
            'boxUser.shift',
            'boxUser.box',
            'corrections',
        ])->whereHas('boxUser.box', function($query) {
            $query->where('name', $this->boxName);
        })->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'User Name',
            'Email',
            'Department',
            'Designation',
            'Shift',
            'Box Name',
            'Date',
            'Check In',
            'Check Out',
            'Status',
            'Correction Statuses',
        ];
    }
    public function map($attendance): array
    {
        return [
            $attendance->id,
            optional($attendance->boxUser->user)->name,
            optional($attendance->boxUser->user)->email,
            optional($attendance->boxUser->departmentRelation)->name,
            optional($attendance->boxUser->designationRelation)->name,
            optional($attendance->boxUser->shift)->name,
            optional($attendance->boxUser->box)->code,
            $attendance->attendance_date,
            $attendance->check_in,
            $attendance->check_out,
            ucfirst($attendance->status ?? 'N/A'),
            $attendance->corrections->pluck('status')->implode(', '),
        ];
    }
}
