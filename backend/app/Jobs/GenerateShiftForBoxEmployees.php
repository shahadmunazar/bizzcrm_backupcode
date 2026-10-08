<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Models\LeaveTransaction;
use App\Models\LeaveType;
use App\Models\Shift;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateShiftForBoxEmployees implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $latestLeaveTransactionCache = [];

    public function __construct(
        private int $boxId,
        private int $shiftId,
        private ?string $month = null,
        private ?string $year = null
    ) {}

    public function handle(): void
    {
        // Load Shift
        $shift = Shift::find($this->shiftId);
        if (! $shift) {
            Log::warning("Shift {$this->shiftId} not found.");
            return;
        }

        // Date Range - Always use provided month/year from request, ignore shift date constraints
        if ($this->month && $this->year) {
            // Always use the requested month and year to set the date range (start to end of month)
            // Don't check shift start_from_date or end_date - use the requested period directly
            $start = Carbon::create($this->year, $this->month, 1)->startOfMonth();
            $end = Carbon::create($this->year, $this->month, 1)->endOfMonth();
            
            if ($shift->start_from_date) {
                $shiftStart = Carbon::parse($shift->start_from_date)->startOfDay();
                if ($shiftStart->gt($start)) {
                    $start = $shiftStart;
                }
            }
            if ($shift->end_date) {
                $shiftEnd = Carbon::parse($shift->end_date)->endOfDay();
                if ($shiftEnd->lt($end)) {
                    $end = $shiftEnd;
                }
            }

            if ($start->gt($end)) {
                $end = $start->copy()->addDay();
            }

            Log::info("Generating shifts for requested month/year (respecting shift date constraints)", [
                'shift_id' => $this->shiftId,
                'box_id' => $this->boxId,
                'requested_month' => Carbon::create($this->year, $this->month, 1)->format('Y-m'),
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'shift_start_from_date' => $shift->start_from_date,
                'shift_end_date' => $shift->end_date,
            ]);
        } else {
            // Fallback: if month/year not provided in request, use shift's effective period dates
            if ($shift->start_from_date && $shift->end_date) {
                $start = Carbon::parse($shift->start_from_date);
                $end = Carbon::parse($shift->end_date);
            } elseif ($shift->start_from_date) {
                $start = Carbon::parse($shift->start_from_date);
                $end = $start->copy()->endOfMonth();
            } elseif ($shift->end_date) {
                $end = Carbon::parse($shift->end_date);
                $start = $end->copy()->startOfMonth();
            } else {
                // Last resort: use current month
                $start = now()->startOfMonth();
                $end = now()->endOfMonth();
            }

            if ($start->gt($end)) {
                $end = $start->copy()->addDay();
            }

            Log::info("Using shift dates (month/year not provided in request)", [
                'shift_id' => $this->shiftId,
                'box_id' => $this->boxId,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
            ]);
        }

        // Weekly Off Days
        $weeklyOffDays = collect([
            $shift->weekly_off_1,
            $shift->weekly_off_2,
            $shift->weekly_off_3,
            $shift->weekly_off_4,
        ])->filter()
            ->map(fn($day) => strtolower($day))
            ->unique()
            ->values();

        Log::info("DEBUG SHIFT LOADED", [
            'shift_id'        => $shift->id,
            'box_id'          => $this->boxId,
            'start_from_date' => $start->toDateString(),
            'end_date'        => $end->toDateString(),
            'weekly_off_days' => $weeklyOffDays,
        ]);

        $compOffLeaveType = $this->resolveCompOffLeaveType();

        // Fetch Holidays
        $holidays = Holiday::where('box_id', $this->boxId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        // Fetch all users in the box
        $boxUsers = \App\Models\BoxUser::where('box_id', $this->boxId)->get();
        $period = \Carbon\CarbonPeriod::create($start, $end);

        foreach ($boxUsers as $boxUser) {
            // Get existing attendances for this user in the date range
            $existingAttendances = Attendance::where('box_id', $this->boxId)
                ->where('box_user_id', $boxUser->id)
                ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
                ->get()
                ->keyBy(function($item) {
                    return \Carbon\Carbon::parse($item->attendance_date)->toDateString();
                });

            foreach ($period as $date) {
                $attendanceDate = $date->toDateString();
                $dayName  = strtolower($date->format('l'));
                $isHoliday = in_array($attendanceDate, $holidays);

                $attendance = $existingAttendances->get($attendanceDate);

                if (!$attendance) {
                    $attendance = new Attendance([
                        'box_id' => $this->boxId,
                        'box_user_id' => $boxUser->id,
                        'attendance_date' => $attendanceDate,
                        'status' => 'absent'
                    ]);
                }

                $hasPunch = ! empty($attendance->check_in) || ! empty($attendance->check_out);
                
                $oldStatus           = $attendance->status;
                $oldStatusNormalized = strtolower(trim((string) $oldStatus));

                $newStatus = $oldStatus;

                if ($isHoliday) {
                    if ($hasPunch) {
                        $newStatus = 'Comp Off';
                    } elseif ($oldStatusNormalized === 'present') {
                        $newStatus = 'Comp Off';
                    } elseif ($oldStatusNormalized === 'absent' || empty($oldStatusNormalized)) {
                        $newStatus = 'Holiday';
                    } else {
                        $newStatus = $oldStatus;
                    }
                } elseif ($weeklyOffDays->contains($dayName)) {
                    if ($hasPunch) {
                        $newStatus = 'Comp Off';
                    } elseif ($oldStatusNormalized === 'present') {
                        $newStatus = 'Comp Off';
                    } elseif ($oldStatusNormalized === 'absent' || empty($oldStatusNormalized)) {
                        $newStatus = 'Weekly Off';
                    } else {
                        $newStatus = $oldStatus;
                    }
                } else {
                    if ($oldStatusNormalized === 'weekly off' || $oldStatusNormalized === 'holiday') {
                        $newStatus = 'Absent';
                    } elseif ($oldStatusNormalized === 'comp off') {
                        $newStatus = 'Present';
                    } else {
                        $newStatus = $oldStatus;
                    }
                }

                $attendance->status  = $newStatus;
                $newStatusNormalized = strtolower(trim((string) $newStatus));

                if ($attendance->isDirty() || !$attendance->exists) {
                    $attendance->save();

                    $statusChangedToPresent = $newStatusNormalized === 'present' && $oldStatusNormalized === 'comp off';

                    if ($newStatusNormalized === 'comp off') {
                        $this->creditCompOffLeave($attendance, $compOffLeaveType, $isHoliday ? 'Holiday' : $dayName);
                    } elseif ($statusChangedToPresent) {
                        $this->debitCompOffLeave($attendance, $compOffLeaveType);
                    }

                    Log::info("DEBUG ATTENDANCE UPDATED", [
                        'attendance_id' => $attendance->id,
                        'box_user_id'   => $boxUser->id,
                        'date'          => $attendance->attendance_date,
                        'day_name'      => $dayName,
                        'punched'       => $hasPunch ? 'Yes' : 'No',
                        'old_status'    => $oldStatus,
                        'new_status'    => $attendance->status,
                    ]);
                }
            }
        }

        Log::info("DEBUG: Attendance regeneration completed for Box {$this->boxId}, Shift {$this->shiftId}");
    }

    private function resolveCompOffLeaveType(): ?LeaveType
    {
        $leaveType = LeaveType::where('box_id', $this->boxId)
            ->where('status', 'Active')
            ->where('leave_type', 'Compensatory Off Leave')
            ->first();

        if (! $leaveType) {
            Log::info("Comp Off leave type (weekly off) not found for Box {$this->boxId}; skip leave transactions.");
        }

        return $leaveType;
    }

    private function creditCompOffLeave(Attendance $attendance, ?LeaveType $leaveType, string $dayName): void
    {
        if (! $leaveType) {
            return;
        }

        $boxUserId = $attendance->box_user_id;
        if (! $boxUserId) {
            return;
        }

        if ($this->hasLeaveTransactionForDate($boxUserId, $leaveType->id, $attendance->attendance_date)) {
            Log::info("Comp Off credit already exists", [
                'box_id'          => $attendance->box_id,
                'box_user_id'     => $boxUserId,
                'attendance_date' => $attendance->attendance_date,
            ]);
            return;
        }

        $latestTransaction = $this->getLatestLeaveTransaction($boxUserId, $leaveType->id);
        $openingBalance    = (float) ($latestTransaction->balance_leave ?? 0);
        $newBalance        = round($openingBalance + 1, 2);
        $totalLeave        = round((float) ($latestTransaction->total_leave ?? 0) + 1, 2);

        $formattedDate = Carbon::parse($attendance->attendance_date)->format('l, Y-m-d');
        $contextDay    = ucfirst($dayName);

        $transaction = LeaveTransaction::create([
            'box_id'              => $attendance->box_id,
            'box_user_id'         => $boxUserId,
            'leave_type_id'       => $leaveType->id,
            'date'                => $attendance->attendance_date,
            'credit_leave'        => 1,
            'debit_leave'         => 0,
            'opening_balance'     => $openingBalance,
            'balance_leave'       => $newBalance,
            'total_leave'         => $totalLeave,
            'status'              => 'approved',
            'transaction_remarks' => "Comp Off earned for {$formattedDate} ({$contextDay})",
            'applied_date'        => $attendance->attendance_date,
            'approved_date'       => now(),
        ]);

        $this->setLatestLeaveTransaction($boxUserId, $leaveType->id, $transaction);

        Log::info("Comp Off credit recorded", [
            'box_id'               => $attendance->box_id,
            'box_user_id'          => $boxUserId,
            'attendance_id'        => $attendance->id,
            'leave_transaction_id' => $transaction->id,
            'balance_leave'        => $newBalance,
        ]);

        $this->logLeaveTransactionTotals($boxUserId, $leaveType->id);
    }

    private function debitCompOffLeave(Attendance $attendance, ?LeaveType $leaveType): void
    {
        if (! $leaveType) {
            return;
        }
        $boxUserId = $attendance->box_user_id;
        if (! $boxUserId) {
            return;
        }
        $latestTransaction = $this->getLatestLeaveTransaction($boxUserId, $leaveType->id);
        $openingBalance    = (float) ($latestTransaction->balance_leave ?? 0);
        $newBalance        = round($openingBalance - 1, 2);
        $totalLeave        = round((float) ($latestTransaction->total_leave ?? 0) - 1, 2);
        $formattedDate     = Carbon::parse($attendance->attendance_date)->format('l, Y-m-d');
        $transaction = LeaveTransaction::create([
            'box_id'              => $attendance->box_id,
            'box_user_id'         => $boxUserId,
            'leave_type_id'       => $leaveType->id,
            'date'                => $attendance->attendance_date,
            'credit_leave'        => 0,
            'debit_leave'         => 1,
            'opening_balance'     => $openingBalance,
            'balance_leave'       => $newBalance,
            'total_leave'         => $totalLeave,
            'status'              => 'approved',
            'transaction_remarks' => "Comp Off reverted for {$formattedDate}",
            'applied_date'        => now(),
            'approved_date'       => now(),
        ]);
        $this->setLatestLeaveTransaction($boxUserId, $leaveType->id, $transaction);
        $this->logLeaveTransactionTotals($boxUserId, $leaveType->id);
    }

    private function hasLeaveTransactionForDate(int $boxUserId, int $leaveTypeId, string $date): bool
    {
        return LeaveTransaction::where('box_user_id', $boxUserId)
            ->where('leave_type_id', $leaveTypeId)
            ->whereDate('date', $date)
            ->exists();
    }

    private function getLatestLeaveTransaction(int $boxUserId, int $leaveTypeId): ?LeaveTransaction
    {
        $cacheKey = $this->getCacheKey($boxUserId, $leaveTypeId);

        if (! array_key_exists($cacheKey, $this->latestLeaveTransactionCache)) {
            $this->latestLeaveTransactionCache[$cacheKey] = LeaveTransaction::where('box_user_id', $boxUserId)
                ->where('leave_type_id', $leaveTypeId)
                ->orderByDesc('id')
                ->first();
        }
        return $this->latestLeaveTransactionCache[$cacheKey];
    }

    private function setLatestLeaveTransaction(int $boxUserId, int $leaveTypeId, ?LeaveTransaction $transaction): void
    {
        $this->latestLeaveTransactionCache[$this->getCacheKey($boxUserId, $leaveTypeId)] = $transaction;
    }

    private function getCacheKey(int $boxUserId, int $leaveTypeId): string
    {
        return "{$boxUserId}_{$leaveTypeId}";
    }

    private function logLeaveTransactionTotals(int $boxUserId, int $leaveTypeId): void
    {
        $totals = LeaveTransaction::where('box_user_id', $boxUserId)
            ->where('leave_type_id', $leaveTypeId)
            ->selectRaw('SUM(COALESCE(credit_leave,0)) as total_credit, SUM(COALESCE(debit_leave,0)) as total_debit')
            ->first();

        $calculatedBalance = round(($totals->total_credit ?? 0) - ($totals->total_debit ?? 0), 2);
        $latestBalance     = (float) ($this->getLatestLeaveTransaction($boxUserId, $leaveTypeId)?->balance_leave ?? 0);

        Log::info('Leave transaction totals verified', [
            'box_user_id'          => $boxUserId,
            'leave_type_id'        => $leaveTypeId,
            'total_creditaccrued'  => round($totals->total_credit ?? 0, 2),
            'total_debitaccrued'   => round($totals->total_debit ?? 0, 2),
            'net_balance'          => $calculatedBalance,
            'latest_transaction_balance' => $latestBalance,
        ]);
    }
}
