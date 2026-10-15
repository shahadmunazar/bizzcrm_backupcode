<?php

namespace App\Services;

use App\Models\{Holiday, Attendance, LeaveType, LeaveTransaction, Country, SalaryReport};
use Carbon\Carbon;

class HolidayService
{
    public function getAllRegional($search)
    {
        $query = Country::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query->orderBy('name')->get(['id', 'name']);
    }

    public function createHoliday($data, $boxId, $userId)
    {
        $holiday                  = new Holiday();
        $holiday->box_id          = $boxId;
        $holiday->title           = $data['titles'] ?? $data['title'];
        $holiday->country_region  = $data['country_region'];
        $holiday->holiday_type_id = $data['holiday_type_id'];
        $holiday->date            = Carbon::parse($data['date'])->format('Y-m-d');
        $holiday->holiday_code    = $data['holiday_code'] ?? null;
        $holiday->holiday_type    = $data['holiday_type'] ?? null;
        $holiday->description     = $data['description'] ?? null;
        $holiday->status          = $data['status'];

        if (empty($holiday->holiday_type_id) && !empty($holiday->holiday_code)) {
            $ht = HolidayType::where('box_id', $boxId)
                ->where(function($q) use ($holiday) {
                    $q->where('holiday_code', $holiday->holiday_code)
                      ->orWhere('holiday_type', $holiday->holiday_code);
                })->first();
            if ($ht) {
                $holiday->holiday_type_id = $ht->id;
                if (empty($holiday->holiday_type)) {
                    $holiday->holiday_type = $ht->holiday_type;
                }
            }
        } elseif (!empty($holiday->holiday_type_id) && empty($holiday->holiday_type)) {
            $ht = HolidayType::find($holiday->holiday_type_id);
            if ($ht) {
                $holiday->holiday_type = $ht->holiday_type;
            }
        }

        $holiday->created_by      = $userId;
        $holiday->save();

        $this->applyHolidayAttendanceLogic($holiday, $userId, 'Comp Off earned due to newly added holiday');

        return $holiday;
    }

    public function updateHoliday($id, $data, $userId)
    {
        $holiday = Holiday::find($id);
        if (!$holiday) return null;

        $oldDate = Carbon::parse($holiday->date)->format('Y-m-d');
        $dateChanged = false;
        $newDate = null;

        if (array_key_exists('title', $data)) {
            $holiday->title = $data['title'];
        }
        if (array_key_exists('date', $data)) {
            $newDate = Carbon::parse($data['date'])->format('Y-m-d');
            if ($oldDate !== $newDate) {
                $dateChanged = true;
            }
            $holiday->date = $newDate;
        }
        if (array_key_exists('holiday_code', $data)) {
            $holiday->holiday_code = $data['holiday_code'];
        }
        if (array_key_exists('description', $data)) {
            $holiday->description = $data['description'];
        }
        if (array_key_exists('status', $data)) {
            $holiday->status = $data['status'];
        }
        if (array_key_exists('holiday_type_id', $data)) {
            $holiday->holiday_type_id = $data['holiday_type_id'];
        }
        if (array_key_exists('holiday_type', $data)) {
            $holiday->holiday_type = $data['holiday_type'];
        }
        if (array_key_exists('country_region', $data)) {
            $holiday->country_region = $data['country_region'];
        }

        if (empty($holiday->holiday_type_id) && !empty($holiday->holiday_code)) {
            $ht = HolidayType::where('box_id', $holiday->box_id)
                ->where(function($q) use ($holiday) {
                    $q->where('holiday_code', $holiday->holiday_code)
                      ->orWhere('holiday_type', $holiday->holiday_code);
                })->first();
            if ($ht) {
                $holiday->holiday_type_id = $ht->id;
                if (empty($holiday->holiday_type)) {
                    $holiday->holiday_type = $ht->holiday_type;
                }
            }
        } elseif (!empty($holiday->holiday_type_id) && empty($holiday->holiday_type)) {
            $ht = HolidayType::find($holiday->holiday_type_id);
            if ($ht) {
                $holiday->holiday_type = $ht->holiday_type;
            }
        }

        $holiday->updated_by = $userId;
        $holiday->save();

        if ($dateChanged && $newDate) {
            $this->revertHolidayAttendanceLogic($holiday->box_id, $oldDate, $userId, 'Comp Off reversed due to holiday date change');
            $this->applyHolidayAttendanceLogic($holiday, $userId, 'Comp Off earned due to holiday date change');
        }

        return $holiday;
    }

    public function deleteHoliday($holiday, $userId)
    {
        $this->revertHolidayAttendanceLogic($holiday->box_id, $holiday->date, $userId, 'Comp Off reversed due to holiday deletion');

        $holiday->deleted_by = $userId;
        $holiday->save();
        $holiday->delete();
    }

    public function checkHolidayLinked($holiday)
    {
        $isLinked = Attendance::where('box_id', $holiday->box_id)
            ->whereDate('attendance_date', $holiday->date)
            ->exists();

        if (!$isLinked) {
            $isLinked = SalaryReport::where('box_id', $holiday->box_id)
                ->whereYear('month', Carbon::parse($holiday->date)->year)
                ->whereMonth('month', Carbon::parse($holiday->date)->month)
                ->exists();
        }

        return $isLinked;
    }

    public function getHolidayData($boxId, $selectedYear)
    {
        $years = Holiday::where('box_id', $boxId)
            ->selectRaw('YEAR(`date`) as year')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        $holidays = Holiday::where('box_id', $boxId)
            ->whereYear('date', $selectedYear)
            ->orderBy('date', 'asc')
            ->get();

        $allCountryIds = $holidays->pluck('country_region')->flatten()->unique()->filter()->values();
        $countries = Country::whereIn('id', $allCountryIds)
            ->select('id', 'name')
            ->get()
            ->keyBy('id');

        $holidays->transform(function ($holiday) use ($countries) {
            $countryIds = $holiday->country_region;

            if (is_string($countryIds)) {
                $countryIds = json_decode($countryIds, true) ?: [];
            } elseif (! is_array($countryIds)) {
                $countryIds = [];
            }
            $holiday->countries = collect($countryIds)->map(function ($id) use ($countries) {
                return $countries->has($id) ? $countries->get($id)->name : null;
            })->filter();

            // Auto-resolve holiday_type_id and holiday_type if missing
            if (empty($holiday->holiday_type_id) && !empty($holiday->holiday_code)) {
                $ht = \App\Models\HolidayType::where('box_id', $holiday->box_id)
                    ->where(function($q) use ($holiday) {
                        $q->where('holiday_code', $holiday->holiday_code)
                          ->orWhere('holiday_type', $holiday->holiday_code);
                    })->first();
                if ($ht) {
                    $holiday->holiday_type_id = $ht->id;
                    if (empty($holiday->holiday_type)) {
                        $holiday->holiday_type = $ht->holiday_type;
                    }
                }
            } elseif (!empty($holiday->holiday_type_id) && empty($holiday->holiday_type)) {
                $ht = \App\Models\HolidayType::find($holiday->holiday_type_id);
                if ($ht) {
                    $holiday->holiday_type = $ht->holiday_type;
                }
            }

            return $holiday;
        });

        return [
            'years' => $years,
            'holidays' => $holidays,
        ];
    }

    protected function applyHolidayAttendanceLogic($holiday, $userId, $remark = 'Comp Off earned due to newly added holiday')
    {
        $holidayDate = Carbon::parse($holiday->date);
        $today = Carbon::today();

        if ($holidayDate->lte($today)) {
            $holiday->load('holidayType');
            $attendancesToUpdate = Attendance::where('box_id', $holiday->box_id)
                ->whereDate('attendance_date', $holiday->date)
                ->get();

            if (optional($holiday->holidayType)->calculate_in_calendar === 'Yes') {
                $approvedUserIds = \App\Models\LeaveRequest::where('box_id', $holiday->box_id)
                    ->where('status', 'approved')
                    ->whereDate('from_date', '<=', $holiday->date)
                    ->whereDate('to_date', '>=', $holiday->date)
                    ->pluck('box_user_id')
                    ->toArray();

                $attendancesToUpdate = $attendancesToUpdate->whereIn('box_user_id', $approvedUserIds);
            }
            foreach ($attendancesToUpdate as $attendance) {
                if (strtolower($attendance->status) == 'present') {
                    $attendance->status = 'Comp Off';
                    $attendance->save();

                    $compOffType = LeaveType::where('box_id', $attendance->box_id)
                        ->where('leave_type', 'Comp Off')
                        ->first();

                    if ($compOffType) {
                        $latestTxn = LeaveTransaction::where('box_user_id', $attendance->box_user_id)
                            ->where('leave_type_id', $compOffType->id)
                            ->latest()
                            ->first();

                        $previousBalance = $latestTxn->balance_leave ?? 0;

                        LeaveTransaction::create([
                            'box_id'              => $attendance->box_id,
                            'box_user_id'         => $attendance->box_user_id,
                            'leave_type_id'       => $compOffType->id,
                            'date'                => Carbon::now()->format('Y-m-d'),
                            'credit_leave'        => 1,
                            'balance_leave'       => $previousBalance + 1,
                            'total_leave'         => 1,
                            'transaction_remarks' => $remark,
                            'status'              => 'Approved',
                            'created_by'          => $userId,
                            'applied_date'        => $holiday->date,
                            'approved_date'       => now(),
                        ]);
                    }
                } elseif (strtolower($attendance->status) == 'absent') {
                    $attendance->status = 'Holiday';
                    $attendance->save();
                }
            }
        }
    }

    protected function revertHolidayAttendanceLogic($boxId, $date, $userId, $remark = 'Comp Off reversed due to holiday deletion')
    {
        $attendancesToRevert = Attendance::where('box_id', $boxId)
            ->whereDate('attendance_date', $date)
            ->get();

        foreach ($attendancesToRevert as $attendance) {
            if (strtolower($attendance->status) == 'holiday') {
                $attendance->status = 'Absent';
                $attendance->save();
            } elseif (strtolower($attendance->status) == 'comp off') {
                $attendance->status = 'Present';
                $attendance->save();
                
                $compOffType = LeaveType::where('box_id', $attendance->box_id)
                    ->where('leave_type', 'Comp Off')
                    ->first();

                if ($compOffType) {
                    $latestTxn = LeaveTransaction::where('box_user_id', $attendance->box_user_id)
                        ->where('leave_type_id', $compOffType->id)
                        ->latest()
                        ->first();

                    $previousBalance = $latestTxn->balance_leave ?? 0;
                    LeaveTransaction::create([
                        'box_id'              => $attendance->box_id,
                        'box_user_id'         => $attendance->box_user_id,
                        'leave_type_id'       => $compOffType->id,
                        'date'                => Carbon::now()->format('Y-m-d'),
                        'debit_leave'         => 1,
                        'balance_leave'       => max(0, $previousBalance - 1),
                        'total_leave'         => 1,
                        'transaction_remarks' => $remark,
                        'status'              => 'Approved',
                        'created_by'          => $userId,
                        'applied_date'        => now(),
                        'approved_date'       => now(),
                    ]);
                }
            }
        }
    }
}
