<?php
namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class AttendanceEmployeeExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $boxName;
    protected $userId;

    public function __construct(string $boxName, int $userId)
    {
        $this->boxName = $boxName;
        $this->userId = $userId;
    }

    public function collection(): Collection
    {
        $today = Carbon::today()->toDateString();

        return Attendance::with([
                'boxUser.user',
                'boxUser.box',
                'boxUser.shift',
                'corrections'
            ])
            ->whereHas('boxUser.box', function ($query) {
                $query->where('name', $this->boxName);
            })
            ->whereHas('boxUser', function ($query) {
                $query->where('user_id', $this->userId);
            })
            ->whereDate('attendance_date', '<=', $today)
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'User Name',
            'Box',
            'Date',
            'Check In',
            'Check Out',
            'Status',
            'Corrections',
            'Break (Min)',
            'Late (Min)',
            'Overtime (Min)',
            'Shift',
            'Production Hours',
        ];
    }

    public function map($attendance): array
    {
        $user = optional($attendance->boxUser->user)->name;
        $box = optional($attendance->boxUser->box)->name;
        $date = Carbon::parse($attendance->attendance_date)->format('d M Y');
        $checkIn = $attendance->check_in ? Carbon::parse($attendance->check_in)->format('h:i A') : '--';
        $checkOut = $attendance->check_out ? Carbon::parse($attendance->check_out)->format('h:i A') : '--';
        $status = ucfirst($attendance->status ?? 'N/A');
        $corrections = $attendance->corrections->pluck('status')->implode(', ') ?: 'N/A';
        $break = $attendance->break_minutes ? $attendance->break_minutes . ' Min' : '-';
        $late = $attendance->late_minutes ? $attendance->late_minutes . ' Min' : '-';
        $overtime = $attendance->overtime_minutes ? $attendance->overtime_minutes . ' Min' : '-';
        $shift = optional($attendance->boxUser->shift)->code ?? 'No Shift';

        // Format production hours
        if (!empty($attendance->production_hours)) {
            [$h, $m, $s] = explode(':', $attendance->production_hours);
            $formattedProduction = intval($h) . ' Hrs ' . intval($m) . ' Min';
        } else {
            $formattedProduction = '--';
        }

        return [
            $attendance->id,
            $user,
            $box,
            $date,
            $checkIn,
            $checkOut,
            $status,
            $corrections,
            $break,
            $late,
            $overtime,
            $shift,
            $formattedProduction,
        ];
    }
}
