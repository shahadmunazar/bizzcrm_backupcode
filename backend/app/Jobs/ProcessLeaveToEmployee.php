<?php
namespace App\Jobs;

use App\Models\Box;
use App\Models\BoxUser;
use App\Models\LeaveTransaction;
use App\Models\LeaveType;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessLeaveToEmployee implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected CarbonInterface $processDate;

    public function __construct(CarbonInterface $processDate)
    {
        $this->processDate = $processDate;
    }

    public function handle(): void
    {
        $year  = $this->processDate->year;
        $month = $this->processDate->month;

        Log::info("🗓️ Running leave processing for: {$this->processDate->format('F Y')}");

        $boxes = Box::where('status', 'Enabled')->get();

        foreach ($boxes as $box) {
            DB::transaction(function () use ($box, $year, $month) {
                $this->processBox($box, $year, $month);
            });
        }
    }

    protected function processBox(Box $box, int $year, int $month): void
    {
        Log::info("📦 Processing Box ID: {$box->id}");

        $leaveTypes = LeaveType::where('box_id', $box->id)
            ->whereNotNull('credit_frequency')
            ->get();

        foreach ($leaveTypes as $leaveType) {
            $this->processLeaveType($box, $leaveType, $year, $month);
        }

        Log::info(str_repeat('=', 60));
    }

    protected function processLeaveType(Box $box, LeaveType $leaveType, int $year, int $month): void
    {
        $frequency  = $leaveType->credit_frequency;
        $leaveName  = $leaveType->leave_type;
        $creditDays = (float) $leaveType->credit_days;

        Log::info("📝 Leave Type: {$leaveName} ({$frequency})");

        // Check if this month should be credited based on frequency
        if (! $this->shouldCreditLeave($frequency, $month)) {
            Log::info("ℹ️ Skipping month {$month} for {$leaveName} (frequency: {$frequency})");
            return;
        }

        $currentMonthStart = Carbon::create($year, $month, 1)->startOfDay();
        $currentMonthEnd   = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        // Get users who joined on or before the end of current processing month
        $users = BoxUser::where('box_id', $box->id)
            ->whereDate('joining_date', '<=', $currentMonthEnd)
            ->get();

        foreach ($users as $user) {
            if (empty($user->joining_date)) {
                Log::info("⚠️ User ID: {$user->id} has no joining date. Skipping...");
                continue;
            }

            $joiningDate = Carbon::parse($user->joining_date);

            // Skip if user joined after the current processing month
            if ($joiningDate->gt($currentMonthEnd)) {
                continue;
            }

            // Check if user should receive credit for this month based on joining date
            if (! $this->shouldCreditUserForMonth($joiningDate, $year, $month)) {
                Log::info("ℹ️ User ID: {$user->id} joined on {$joiningDate->format('Y-m-d')} - not eligible for credit in {$year}-{$month}");
                continue;
            }

            // Calculate base credit leave based on frequency
            $baseCreditLeave = $this->calculateCreditLeave($frequency, $month, $creditDays);

            if ($baseCreditLeave <= 0) {
                continue;
            }

            // Calculate prorated credit based on joining date
            $creditLeave = $this->calculateProratedCredit($joiningDate, $year, $month, $baseCreditLeave, $frequency);

            if ($creditLeave <= 0) {
                continue;
            }

            $transactionDate = Carbon::create($year, $month, 1);

            // ✅ Check if this month's transaction already exists
            $existingTransaction = LeaveTransaction::where('box_id', $box->id)
                ->where('box_user_id', $user->id)
                ->where('leave_type_id', $leaveType->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->first();

            if ($existingTransaction) {
                Log::info("⚠️ LeaveTransaction already exists for User ID: {$user->id}, Month: {$month}, Year: {$year}. Skipping...");
                continue;
            }

            // ✅ Get the user's previous balance
            $lastTransaction = LeaveTransaction::where('box_id', $box->id)
                ->where('box_user_id', $user->id)
                ->where('leave_type_id', $leaveType->id)
                ->orderBy('date', 'desc')
                ->first();

            $previousBalance = $lastTransaction?->balance_leave ?? 0;
            $newBalance      = $previousBalance + $creditLeave;

            $remarks = $this->generateTransactionRemarks($creditLeave, $leaveName, $transactionDate, $joiningDate, $frequency);

            LeaveTransaction::create([
                'box_id'              => $box->id,
                'box_user_id'         => $user->id,
                'leave_type_id'       => $leaveType->id,
                'date'                => $transactionDate->toDateString(),
                'credit_leave'        => $creditLeave,
                'debit_leave'         => 0,
                'balance_leave'       => $newBalance,
                'total_leave'         => $newBalance,
                'transaction_remarks' => $remarks,
                'applied_date'        => now(),
                'approved_date'       => now(),
            ]);

            Log::info("✅ Credited {$creditLeave} leave day(s) to User ID: {$user->id} ({$leaveName}) | Joined: {$joiningDate->format('Y-m-d')} | New Balance: {$newBalance}");
        }

        Log::info(str_repeat('-', 40));
    }

    protected function calculateCreditLeave(string $frequency, int $month, float $creditDays): float
    {
        return match ($frequency) {
            'Monthly'         => $creditDays,
            'Alternate Month' => $month % 2 === 1 ? $creditDays : 0,
            'Quarterly'       => in_array($month, [1, 4, 7, 10]) ? $creditDays : 0,
            'Half Yearly'     => in_array($month, [1, 7]) ? $creditDays : 0,
            'Yearly'          => $month === 1 ? $creditDays : 0,
            default           => 0,
        };
    }

    protected function shouldCreditLeave(string $frequency, int $month): bool
    {
        return match ($frequency) {
            'Monthly'         => true,
            'Alternate Month' => $month % 2 === 1,
            'Quarterly'       => in_array($month, [1, 4, 7, 10]),
            'Half Yearly'     => in_array($month, [1, 7]),
            'Yearly'          => $month === 1,
            default           => false,
        };
    }

    /**
     * Check if user should receive credit for the given month based on joining date
     */
    protected function shouldCreditUserForMonth(Carbon $joiningDate, int $year, int $month): bool
    {
        $joiningYear  = $joiningDate->year;
        $joiningMonth = $joiningDate->month;

        // User joined in a future year
        if ($joiningYear > $year) {
            return false;
        }

        // User joined in a future month of the same year
        if ($joiningYear === $year && $joiningMonth > $month) {
            return false;
        }

        // User joined in the same month or earlier - eligible for credit
        return true;
    }

    /**
     * Calculate prorated credit based on joining date
     * For users joining mid-month, credit is prorated based on days remaining in month
     */
    protected function calculateProratedCredit(Carbon $joiningDate, int $year, int $month, float $baseCredit, string $frequency): float
    {
        $joiningYear  = $joiningDate->year;
        $joiningMonth = $joiningDate->month;
        $joiningDay   = $joiningDate->day;

        $currentMonthStart = Carbon::create($year, $month, 1)->startOfDay();
        $currentMonthEnd   = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        // If user joined in a previous month/year, give full credit
        if ($joiningYear < $year || ($joiningYear === $year && $joiningMonth < $month)) {
            return round($baseCredit, 2);
        }

        // If user joined in the current processing month, calculate proration
        if ($joiningYear === $year && $joiningMonth === $month) {
            // Calculate days from joining date to end of month (inclusive)
            $totalDaysInMonth = $currentMonthEnd->day;
            $daysFromJoining  = $totalDaysInMonth - $joiningDay + 1;

            // Prorate based on days worked
            $proratedCredit = ($baseCredit / $totalDaysInMonth) * $daysFromJoining;

            Log::info("📊 Prorating leave for User joined on {$joiningDate->format('Y-m-d')}: {$daysFromJoining}/{$totalDaysInMonth} days = {$proratedCredit} (base: {$baseCredit})");

            return round($proratedCredit, 2);
        }

        // Default: full credit
        return round($baseCredit, 2);
    }

    /**
     * Generate transaction remarks with joining date context
     */
    protected function generateTransactionRemarks(float $creditLeave, string $leaveName, Carbon $transactionDate, Carbon $joiningDate, string $frequency): string
    {
        $joiningYear      = $joiningDate->year;
        $joiningMonth     = $joiningDate->month;
        $transactionYear  = $transactionDate->year;
        $transactionMonth = $transactionDate->month;

        // Check if this is a prorated credit
        $isProrated = ($joiningYear === $transactionYear && $joiningMonth === $transactionMonth);

        if ($isProrated) {
            $daysInMonth     = $transactionDate->endOfMonth()->day;
            $daysFromJoining = $daysInMonth - $joiningDate->day + 1;
            return "Credited {$creditLeave} day(s) (prorated {$daysFromJoining}/{$daysInMonth} days) for '{$leaveName}' in {$transactionDate->format('F Y')} - User joined on {$joiningDate->format('d M Y')}";
        }

        return "Credited {$creditLeave} day(s) for '{$leaveName}' in {$transactionDate->format('F Y')} (frequency: {$frequency})";
    }
}
