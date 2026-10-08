<?php
namespace App\Console\Commands;

use App\Models\Box;
use App\Models\BoxUser;
use App\Models\LeaveTransaction;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RunProcessHolidayLeave extends Command
{
    protected $signature   = 'leave:process-holiday';
    protected $description = 'Process and credit leave per box, leave type, and user based on frequency';

    protected Carbon $processDate;

    public function __construct()
    {
        parent::__construct();
        $this->processDate = Carbon::now();
// $this->processDate = Carbon::create(2025, 1, 1, 0, 2, 0); // Try month 1, 2, 4, 7, etc.
    }

    public function handle()
    {
        $year  = $this->processDate->year;
        $month = $this->processDate->month;
        $this->info("🗓️ Running leave processing for: {$this->processDate->toDateTimeString()}");
        $boxes = Box::where('status', 'Enabled')->get();
        foreach ($boxes as $box) {
            $this->processBox($box, $year, $month);
        }
    }

    protected function processBox(Box $box, int $year, int $month): void
    {
        $this->info("📦 Processing Box ID: {$box->id}");
        $totalNumberOfDays = LeaveType::where('box_id', $box->id)
            ->whereYear('created_at', $year)
            ->sum('number_of_days');
        $this->info("ℹ️ Total leave days for Box {$box->id} in year {$year}: {$totalNumberOfDays}");
        $leaveTypes = LeaveType::where('box_id', $box->id)
            ->whereNotNull('credit_frequency')
            ->get();foreach ($leaveTypes as $leaveType) {
            $this->processLeaveType($box, $leaveType, $year, $month, $totalNumberOfDays);
        }
        $this->info(str_repeat('=', 50));
    }

    protected function processLeaveType(Box $box, LeaveType $leaveType, int $year, int $month, int $totalNumberOfDays): void
    {
        $frequency  = $leaveType->credit_frequency;
        $leaveName  = $leaveType->leave_type;
        $creditDays = $leaveType->credit_days;
        $this->info("📝 Leave Type: {$leaveName} ({$frequency})");

        if (! $this->shouldCreditLeave($frequency, $month)) {
            $this->info("ℹ️ No leave credit for this cycle/month");
            $this->line(str_repeat('-', 40));
            return;
        }

        $processingMonthEndDate = Carbon::create($year, $month)->endOfMonth();

        // Fetch users joined in current processing year and on or before this month end
        $users = BoxUser::where('box_id', $box->id)
            ->whereYear('joining_date', $year)
            ->whereDate('joining_date', '<=', $processingMonthEndDate)
            ->get();

        foreach ($users as $user) {
            $joiningDate = Carbon::parse($user->joining_date);

            // Calculate months left in year including joining month
            $monthsLeft = 12 - $joiningDate->month + 1;

            // Total leave entitlement prorated for joining month
            $totalLeaveEntitlement = round($creditDays * $monthsLeft, 2);

            // Only credit leave if user joined on or before current month
            if ($joiningDate->month <= $month) {

                $creditLeave = 0;

                switch ($frequency) {
                    case 'Monthly':
                        $creditLeave = $creditDays;
                        break;

                    case 'Alternate Month':
                        if ($month % 2 === 1) {
                            $creditLeave = $creditDays;
                        }
                        break;

                    case 'Quarterly':
                        if (in_array($month, [1, 4, 7, 10])) {
                            $creditLeave = $creditDays;
                        }
                        break;

                    case 'Half Yearly':
                        if (in_array($month, [1, 7])) {
                            $creditLeave = $creditDays;
                        }
                        break;

                    case 'Yearly':
                        if ($month === 1) {
                            $creditLeave = $creditDays;
                        }
                        break;
                }
                $creditLeave = round($creditLeave, 2);
                if ($creditLeave > 0) {
                    // Check if a LeaveTransaction already exists for this user, leave type, and box in this year
                    $existingTransaction = LeaveTransaction::where('box_id', $box->id)
                        ->where('box_user_id', $user->id)
                        ->where('leave_type_id', $leaveType->id)
                        ->whereYear('date', $year)
                        ->first();

                    if ($existingTransaction) {
                        // Update existing transaction by adding leave credits
                        // $existingTransaction->credit_leave += $creditLeave;
                        // $existingTransaction->balance_leave += $creditLeave;
                        // $existingTransaction->total_leave = $totalLeaveEntitlement; // Update entitlement if needed
                        $existingTransaction->save();

                        $this->info("✅ Updated LeaveTransaction for User ID: {$user->id} with additional {$creditLeave} day(s). Total Entitlement: {$totalLeaveEntitlement}");
                    } else {
                        // Create new transaction if none exists yet
                        LeaveTransaction::create([
                            'box_id'        => $box->id,
                            'box_user_id'   => $user->id,
                            'leave_type_id' => $leaveType->id,
                            'date'          => $this->processDate->toDateString(),
                            'credit_leave'  => $creditLeave,
                            'debit_leave'   => 0,
                            'balance_leave' => $creditLeave,
                            'total_leave'   => $totalLeaveEntitlement,
                            'applied_date'  => $this->processDate->toDateString(),
                            'approved_date' => $this->processDate->toDateString(),
                        ]);
                        $this->info("✅ Created LeaveTransaction for User ID: {$user->id} with {$creditLeave} day(s). Total Entitlement: {$totalLeaveEntitlement}");
                    }
                } else {
                    $this->info("ℹ️ No leave credited for User ID: {$user->id} this month due to frequency");
                }
            }
        }

        $this->line(str_repeat('-', 40));
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

}
