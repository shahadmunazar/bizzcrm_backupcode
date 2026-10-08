<?php

namespace App\Console\Commands;

use App\Models\Box;
use App\Models\BoxUser;
use App\Models\LeaveTransaction;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessLeaveCredit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:process-credit {--date= : Process for specific date (Y-m-d format)} {--queue : Process via queue} {--force : Force run regardless of schedule (bypasses date/time checks)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and credit leave for all boxes based on leave type credit frequencies';

    
    public function handle(): int
    {
        $now = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : Carbon::now();

        $forceMode = $this->option('force');
        Log::info("🗓️ Processing leave credits for: {$now->format('F Y')}");
        Log::info("📅 Date: {$now->toDateString()} | ⏰ Time: {$now->format('H:i')}");
        if ($forceMode) {
            Log::info("⚡ FORCE MODE: Bypassing schedule checks - will process all eligible boxes");
        }
        $this->newLine();

        $boxes = Box::where('status', 'Enabled')->get();
        if ($boxes->isEmpty()) {
            $this->warn('⚠️ No enabled boxes found.');
            return Command::SUCCESS;
        }

        Log::info("📦 Found {$boxes->count()} enabled box(es)");
        $this->newLine();

        $totalProcessed = 0;
        $totalSkipped   = 0;
        $totalCredited  = 0;

        foreach ($boxes as $box) {
            $this->info("Checking Box ID: {$box->id} - {$box->name}");
            Log::info("Checking Box ID: {$box->id} - {$box->name}");

            // Skip schedule check if --force option is used
            if (! $forceMode && ! $this->shouldRunForBox($box, $now)) {
                Log::info("⏭️  Skipping Box ID {$box->id} — not scheduled to run now.");
                $totalSkipped++;
                continue;
            }

            if ($forceMode) {
                Log::info("⚡ FORCE MODE: Processing Box ID {$box->id} - {$box->name} (bypassing schedule)");
            }

            Log::info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            Log::info("📦 Running for Box ID {$box->id} - {$box->name}");
            Log::info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

            // Fetch leave types
            $leaveTypes = LeaveType::where('box_id', $box->id)
                ->whereNotNull('credit_frequency')
                ->where('status', 'Active')
                ->get();

            Log::info("  📝 Found " . $leaveTypes->count() . " active leave types for Box ID: {$box->id}");

            if ($leaveTypes->isEmpty()) {
                Log::info("  ⚠️ No leave types with credit frequency found for Box ID: {$box->id}");
                $this->warn("  ⚠️ No leave types with credit frequency found.");
                $this->newLine();
                continue;
            }

            // Leave types eligible this month
            $eligibleLeaveTypes = $leaveTypes->filter(function ($lt) use ($now) {
                return $this->shouldProcessLeaveType($lt->credit_frequency, $now->month);
            });

            Log::info("  📝 " . $eligibleLeaveTypes->count() . " leave type(s) eligible for processing in month {$now->month}");

            if ($eligibleLeaveTypes->isEmpty()) {
                Log::info("  ⏭️ No leave types scheduled for this month for Box ID: {$box->id}");
                $this->warn("  ⏭️ No leave types scheduled for this month.");
                $this->newLine();
                continue;
            }

            $eligibleLeaveTypes = $eligibleLeaveTypes->sortBy('id')->values();

            $boxProcessed = 0;
            $boxCredited  = 0;

            foreach ($eligibleLeaveTypes as $leaveTypeIndex => $leaveType) {

                Log::info("  ➤ Processing Leave Type " . ($leaveTypeIndex + 1) . ": {$leaveType->leave_type}");

                DB::transaction(function () use ($box, $leaveType, $now, &$boxProcessed, &$boxCredited) {
                    try {
                        $result = $this->processLeaveType($box, $leaveType, $now->year, $now->month);

                        if ($result['processed']) {
                            $boxProcessed++;
                            $boxCredited += $result['credited_count'];
                        }
                    } catch (\Exception $e) {
                        Log::error("    ✗ Error in processLeaveType: " . $e->getMessage());
                        throw $e;
                    }
                });

                $this->newLine();
            }

            $totalProcessed += $boxProcessed;
            $totalCredited += $boxCredited;

            Log::info("  ✔ Box Summary: {$boxProcessed} processed, {$boxCredited} users credited");
            $this->newLine();
        }

        // Final summary
        Log::info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        Log::info("📊 Overall Summary:");
        Log::info("   ✓ Total Processed: {$totalProcessed}");
        Log::info("   ⏭️ Total Skipped:  {$totalSkipped}");
        Log::info("   👥 Total Credited: {$totalCredited}");
        Log::info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        return Command::SUCCESS;
    }

    private function shouldRunForBox(Box $box, Carbon $now): bool
    {
        $mode       = $box->leave_credit_mode;
        $creditTime = $box->leave_credit_time;

        if (! $mode || ! $creditTime) {
            Log::info("Skipped Box {$box->id}: Missing mode or credit time.");
            return false;
        }

        try {
            // Handle different time formats
            $configuredTime = $this->parseTimeTo24Hour($creditTime);
            if (! $configuredTime) {
                Log::info("Box {$box->id} → Invalid time format: {$creditTime}");
                return false;
            }
        } catch (\Exception $e) {
            Log::info("Box {$box->id} → Invalid time format: {$creditTime} - Error: {$e->getMessage()}");
            return false;
        }

        $currentTime = $now->format('H:i');

        // Log both times
        Log::info("Box {$box->id} Time Check → Configured: {$configuredTime}, Now: {$currentTime}");

        if ($configuredTime !== $currentTime) {
            Log::info("Box {$box->id} skipped — Time mismatch. Configured: {$configuredTime}, Now: {$currentTime}");
            return false;
        }

        if ($mode === 'last_day_of_every_month') {
            $res = $now->isSameDay($now->copy()->endOfMonth());
            Log::info("Box {$box->id} Mode: LAST DAY → Result: " . ($res ? "RUN" : "SKIP"));
            return $res;
        }

        if ($mode === 'start_day_of_every_month') {
            $res = $now->isSameDay($now->copy()->startOfMonth());
            Log::info("Box {$box->id} Mode: START DAY → Result: " . ($res ? "RUN" : "SKIP"));
            return $res;
        }

        if ($mode === 'specific_number') {

            $selectedDay = (int) $box->leave_credit_selected_days;

            Log::info("Box {$box->id} Mode: SPECIFIC DAY → Selected: {$selectedDay}, Today: {$now->day}");

            if ($selectedDay < 1 || $selectedDay > 31) {
                Log::info("Box {$box->id} Invalid selected day: {$selectedDay}");
                return false;
            }

            return $now->day === $selectedDay;
        }

        Log::info("Box {$box->id} Unknown mode: {$mode}");
        return false;
    }

    /**
     * Parse time string to 24-hour format (H:i)
     * Handles formats like: "22:35", "10:35 PM", "22:35 PM" (invalid but will fix)
     */
    protected function parseTimeTo24Hour(string $timeString): ?string
    {
        if (empty($timeString)) {
            return null;
        }

        // Remove extra spaces
        $timeString = trim($timeString);

        // Check if it contains AM/PM
        $hasAmPm = preg_match('/(AM|PM)/i', $timeString);

        if ($hasAmPm) {
            // Has AM/PM indicator
            try {
                // Try to parse as 12-hour format first
                $parsed = Carbon::parse($timeString);
                return $parsed->format('H:i');
            } catch (\Exception $e) {
                // If parsing fails, try to fix common issues
                // Remove AM/PM if hour is >= 12 (invalid format like "22:35 PM")
                $timeWithoutAmPm = preg_replace('/\s*(AM|PM)/i', '', $timeString);
                $parts           = explode(':', $timeWithoutAmPm);

                if (count($parts) === 2) {
                    $hour   = (int) trim($parts[0]);
                    $minute = (int) trim($parts[1]);

                    // If hour is >= 12, it's already in 24-hour format, just remove AM/PM
                    if ($hour >= 12 && $hour <= 23) {
                        return sprintf('%02d:%02d', $hour, $minute);
                    }

                    // Otherwise, try to parse as 12-hour format
                    $amPm = preg_match('/PM/i', $timeString) ? 'PM' : 'AM';
                    if ($amPm === 'PM' && $hour < 12) {
                        $hour += 12;
                    } elseif ($amPm === 'AM' && $hour === 12) {
                        $hour = 0;
                    }
                    return sprintf('%02d:%02d', $hour, $minute);
                }
            }
        } else {
            // No AM/PM, assume 24-hour format
            try {
                // Validate format HH:MM
                if (preg_match('/^(\d{1,2}):(\d{2})$/', $timeString, $matches)) {
                    $hour   = (int) $matches[1];
                    $minute = (int) $matches[2];

                    if ($hour >= 0 && $hour <= 23 && $minute >= 0 && $minute <= 59) {
                        return sprintf('%02d:%02d', $hour, $minute);
                    }
                }
            } catch (\Exception $e) {
                // Fall through to return null
            }
        }

        return null;
    }

    /**
     * Get effective start date for leave credit calculation
     *
     * Logic:
     * 1. If attendace_module_date is set AND joining_date < attendace_module_date
     *    → Use attendace_module_date (user joined before module was implemented)
     * 2. If attendace_module_date is set AND joining_date >= attendace_module_date
     *    → Use joining_date (user joined after module was implemented)
     * 3. If attendace_module_date is not set
     *    → Use joining_date (default behavior)
     *
     * Extracts only the date portion from attendace_module_date (ignores time)
     * Both dates are normalized to start of day for accurate comparison
     *
     * @param Box $box The box containing attendace_module_date
     * @param Carbon $joiningDate User's joining date
     * @return Carbon Effective start date for leave credit calculations
     */
    protected function getEffectiveStartDate(Box $box, Carbon $joiningDate): Carbon
    {
        // Check if attendace_module_date is set
        if ($box->attendace_module_date) {
            try {
                // Parse the date string (format: "MM/DD/YYYY hh:mm AM/PM" or similar)
                // Extract only the date portion, ignoring time
                $attendaceModuleDate = Carbon::parse($box->attendace_module_date);

                // Normalize to start of day for date-only comparison (ignore time)
                $attendaceModuleDateOnly = $attendaceModuleDate->copy()->startOfDay();
                $joiningDateOnly         = $joiningDate->copy()->startOfDay();

                // If joining date is before attendace_module_date, use attendace_module_date
                if ($joiningDateOnly->lt($attendaceModuleDateOnly)) {
                    Log::info("    📅 Using attendace_module_date ({$attendaceModuleDateOnly->format('Y-m-d')}) instead of joining_date ({$joiningDateOnly->format('Y-m-d')})");
                    return $attendaceModuleDateOnly;
                }
            } catch (\Exception $e) {
                Log::warning("    ⚠️ Invalid attendace_module_date format: {$box->attendace_module_date} - Error: {$e->getMessage()}");
            }
        }

        // Default: use joining date (normalized to start of day)
        return $joiningDate->copy()->startOfDay();
    }

    /**
     * Get transaction date based on leave_credit_mode
     *
     * @param Box $box The box with leave_credit_mode settings
     * @param int $year Year for the transaction
     * @param int $month Month for the transaction
     * @return Carbon Transaction date based on mode
     */
    protected function getTransactionDate(Box $box, int $year, int $month): Carbon
    {
        $mode = $box->leave_credit_mode;

        if ($mode === 'specific_number') {
            // Use the selected day number
            $selectedDay = (int) $box->leave_credit_selected_days;
            if ($selectedDay >= 1 && $selectedDay <= 31) {
                // Ensure the day is valid for the month (e.g., Feb 31 becomes Feb 28/29)
                $lastDayOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->day;
                $day            = min($selectedDay, $lastDayOfMonth);
                return Carbon::create($year, $month, $day);
            }
            // Fallback to 1st if invalid
            return Carbon::create($year, $month, 1);
        }

        if ($mode === 'start_day_of_every_month') {
            // Use the 1st day of the month
            return Carbon::create($year, $month, 1);
        }

        if ($mode === 'last_day_of_every_month') {
            // Use the last day of the month
            return Carbon::create($year, $month, 1)->endOfMonth();
        }

        // Default: use 1st day of the month
        return Carbon::create($year, $month, 1);
    }

    /**
     * Process a single leave type for a box
     */
    protected function processLeaveType(Box $box, LeaveType $leaveType, int $year, int $month): array
    {
        $frequency  = $leaveType->credit_frequency;
        $leaveName  = $leaveType->leave_type;
        $creditDays = (float) $leaveType->credit_days;

        Log::info("  📝 Processing '{$leaveName}' (Frequency: {$frequency})");

        $currentMonthStart = Carbon::create($year, $month, 1)->startOfDay();
        $currentMonthEnd   = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        // Get attendace_module_date if set (for user filtering)
        $attendaceModuleDate = null;
        if ($box->attendace_module_date) {
            try {
                $attendaceModuleDate = Carbon::parse($box->attendace_module_date)->startOfDay();
            } catch (\Exception $e) {
                Log::warning("    ⚠️ Invalid attendace_module_date format in user query: {$box->attendace_module_date}");
            }
        }

        // Query users: include if joining_date <= currentMonthEnd
        // (effective start date will be calculated per user later)
        $users = BoxUser::where('box_id', $box->id)
            ->whereDate('joining_date', '<=', $currentMonthEnd)
            ->whereNotNull('joining_date')
            ->get();

        Log::info("    👥 Found " . $users->count() . " user(s) with valid joining date");

        if ($users->isEmpty()) {
            Log::info("    ⚠️ No users found for Box ID: {$box->id}");
            $this->warn("    ⚠️ No users found for Box ID: {$box->id}");
            return ['processed' => false, 'credited_count' => 0];
        }

        $creditedCount   = 0;
        $skippedCount    = 0;
        $backfilledCount = 0;
        $totalBalanceForYear = 0;

        foreach ($users as $user) {
            $joiningDate = Carbon::parse($user->joining_date);

            // Get effective start date (attendace_module_date or joining_date)
            $effectiveStartDate = $this->getEffectiveStartDate($box, $joiningDate);

            // Verify effective start date is valid
            if (! $effectiveStartDate || ! $effectiveStartDate->isValid()) {
                Log::warning("    ⚠️ Invalid effective start date for User ID: {$user->id}");
                $skippedCount++;
                continue;
            }

            // Check if user has any previous credit transactions for this leave type
            $hasAnyTransaction = LeaveTransaction::where('box_id', $box->id)
                ->where('box_user_id', $user->id)
                ->where('leave_type_id', $leaveType->id)
                ->where('credit_leave', '>', 0) // Only check credit transactions
                ->exists();

            // If user has no transactions, backfill all missing months from effective start date
            if (! $hasAnyTransaction) {
                $backfilled = $this->backfillMissingCredits($box, $user, $leaveType, $effectiveStartDate, $year, $month, $frequency, $creditDays, $leaveName);
                if ($backfilled > 0) {
                    $backfilledCount += $backfilled;
                    $creditedCount += $backfilled;
                }
                // Continue to next user after backfilling
                continue;
            }

            // User has existing transactions - process current month only

            // ✅ YEARLY RESET LOGIC: If month is January, check if previous year's balance should be carried forward or lapsed
            if ($month === 1) {
                // Get current balance before reset
                $lastTx = LeaveTransaction::where('box_id', $box->id)
                    ->where('box_user_id', $user->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->orderBy('date', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();
                $currentBal = $lastTx?->balance_leave ?? 0;
                Log::info("    🔄 Checking yearly reset for User ID {$user->id} (Current Balance: {$currentBal})");
                $this->handleYearlyCarryForwardOrLapse($box, $user, $leaveType, $year, $currentBal);
            }

            // Check if user should receive credit for this month based on effective start date
            if (! $this->shouldCreditUserForMonth($effectiveStartDate, $year, $month)) {
                $skippedCount++;
                continue;
            }

            // Calculate base credit leave based on frequency
            $baseCreditLeave = $this->calculateCreditLeave($frequency, $month, $creditDays);

            if ($baseCreditLeave <= 0) {
                $skippedCount++;
                continue;
            }

            // Calculate prorated credit based on effective start date
            $creditLeave = $this->calculateProratedCredit($effectiveStartDate, $year, $month, $baseCreditLeave, $frequency);

            if ($creditLeave <= 0) {
                $skippedCount++;
                continue;
            }

            // Get transaction date based on leave_credit_mode
            $transactionDate = $this->getTransactionDate($box, $year, $month);

            // ✅ STRICT CHECK: Verify transaction doesn't already exist for this month/year/user/leave_type
            // Check for ANY credit transaction in this month (not just exact date match)
            $existingTransaction = LeaveTransaction::where('box_id', $box->id)
                ->where('box_user_id', $user->id)
                ->where('leave_type_id', $leaveType->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('credit_leave', '>', 0) // Only check credit transactions
                ->first();

            if ($existingTransaction) {
                // Transaction already exists for this month - skip to avoid duplicate
                Log::info("    ⏭️ User ID {$user->id}: Credit already exists for {$year}-{$month} (Transaction ID: {$existingTransaction->id})");
                $skippedCount++;
                // Still add to total balance for reporting
                $totalBalanceForYear += (float) ($existingTransaction->balance_leave ?? 0);
                continue;
            }

            // ✅ Get the user's previous balance (most recent transaction) to ensure correct balance calculation
            // This re-fetches balance in case a yearly reset happened above
            $lastTransaction = LeaveTransaction::where('box_id', $box->id)
                ->where('box_user_id', $user->id)
                ->where('leave_type_id', $leaveType->id)
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $previousBalance = $lastTransaction?->balance_leave ?? 0;
            $newBalance      = $previousBalance + $creditLeave;

            Log::info("    💰 User ID {$user->id}: Balance before credit: {$previousBalance}, Credit: {$creditLeave}, New Balance: {$newBalance}");

            $remarks = $this->generateTransactionRemarks($creditLeave, $leaveName, $transactionDate, $effectiveStartDate, $frequency);

            // ✅ Create transaction with duplicate prevention
            // Transactions are created one by one, grouped by leave type
            try {
                $transaction = LeaveTransaction::create([
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

                $creditedCount++;
                $totalBalanceForYear += $newBalance;
            } catch (\Illuminate\Database\QueryException $e) {
                // Handle potential duplicate key errors
                if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'UNIQUE')) {
                    $this->warn("    ⚠️ Duplicate transaction prevented for User ID: {$user->id}, Month: {$year}-{$month}");
                    $skippedCount++;
                } else {
                    throw $e;
                }
            }
        }

        $backfillMsg = $backfilledCount > 0 ? ", {$backfilledCount} backfilled" : "";
        Log::info("    📊 Summary for {$leaveName}: Credited {$creditedCount} user(s), Total Balance for Year: {$totalBalanceForYear}");
        Log::info("    ✓ Processed {$creditedCount} user(s), skipped {$skippedCount} user(s){$backfillMsg}");
        $this->newLine();

        return ['processed' => true, 'credited_count' => $creditedCount];
    }

    /**
     * Backfill missing credits for a user from effective start date to current month
     * Processes all missing months if user has no previous transactions
     * ✅ Prevents duplicate credits by checking existing transactions
     * Uses effective start date (attendace_module_date if joining_date is before it, otherwise joining_date)
     */
    protected function backfillMissingCredits(Box $box, BoxUser $user, LeaveType $leaveType, Carbon $effectiveStartDate, int $currentYear, int $currentMonth, string $frequency, float $creditDays, string $leaveName): int
    {
        $creditedMonths = 0;
        $skippedMonths  = 0;
        $startYear      = $effectiveStartDate->year;
        $startMonth     = $effectiveStartDate->month;

        // Start from effective start date month, iterate to current month
        $startDate = Carbon::create($startYear, $startMonth, 1);
        $endDate   = Carbon::create($currentYear, $currentMonth, 1)->endOfMonth();

        // ✅ Get all existing credit transactions for this user and leave type to prevent duplicates
        $existingTransactions = LeaveTransaction::where('box_id', $box->id)
            ->where('box_user_id', $user->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('credit_leave', '>', 0) // Only check credit transactions
            ->get()
            ->keyBy(function ($transaction) {
                $date = Carbon::parse($transaction->date);
                return "{$date->year}-{$date->month}";
            });

        // Start balance from 0 (since we only backfill if user has no transactions)
        // If there are existing transactions, we'll update balance as we iterate
        $runningBalance = 0;

        // If there are existing transactions, get the latest balance
        if ($existingTransactions->isNotEmpty()) {
            $lastTransaction = LeaveTransaction::where('box_id', $box->id)
                ->where('box_user_id', $user->id)
                ->where('leave_type_id', $leaveType->id)
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->first();
            $runningBalance = $lastTransaction?->balance_leave ?? 0;
        }

        // ✅ Iterate through each month from joining date to current month
        // This ensures transactions are created in chronological order (one by one)
        $currentIterateDate = $startDate->copy();

        while ($currentIterateDate->lte($endDate)) {
            $processYear  = $currentIterateDate->year;
            $processMonth = $currentIterateDate->month;
            $monthKey     = "{$processYear}-{$processMonth}";
            // Get transaction date based on leave_credit_mode
            $transactionDate = $this->getTransactionDate($box, $processYear, $processMonth);

            // ✅ YEARLY RESET LOGIC: If month is January, check if previous year's balance should be carried forward or lapsed
            if ($processMonth === 1) {
                $runningBalance = $this->handleYearlyCarryForwardOrLapse($box, $user, $leaveType, $processYear, $runningBalance);
            }

            // ✅ STRICT CHECK: Verify transaction doesn't already exist for this month/year/user/leave_type
            if ($existingTransactions->has($monthKey)) {
                // Update running balance from existing transaction
                $existing       = $existingTransactions->get($monthKey);
                $runningBalance = $existing->balance_leave;
                $skippedMonths++;
                Log::debug("    ⏭️ Month {$monthKey}: Credit already exists (Transaction ID: {$existing->id})");
                $currentIterateDate->addMonth();
                continue;
            }

            // ✅ DOUBLE CHECK: Query database one more time to ensure no duplicate
            // Check for ANY credit transaction in this month (not just exact date match)
            $existingTransaction = LeaveTransaction::where('box_id', $box->id)
                ->where('box_user_id', $user->id)
                ->where('leave_type_id', $leaveType->id)
                ->whereYear('date', $processYear)
                ->whereMonth('date', $processMonth)
                ->where('credit_leave', '>', 0) // Only check credit transactions
                ->first();

            if ($existingTransaction) {
                // Transaction exists for this month - skip to avoid duplicate
                $runningBalance = $existingTransaction->balance_leave;
                $skippedMonths++;
                Log::debug("    ⏭️ Month {$monthKey}: Credit already exists (Transaction ID: {$existingTransaction->id})");
                $currentIterateDate->addMonth();
                continue;
            }

            // Check if this month should be credited based on frequency
            if (! $this->shouldProcessLeaveType($frequency, $processMonth)) {
                $currentIterateDate->addMonth();
                continue;
            }

            // Check if user should receive credit for this month based on effective start date
            if (! $this->shouldCreditUserForMonth($effectiveStartDate, $processYear, $processMonth)) {
                $currentIterateDate->addMonth();
                continue;
            }

            // Calculate base credit leave based on frequency
            $baseCreditLeave = $this->calculateCreditLeave($frequency, $processMonth, $creditDays);

            if ($baseCreditLeave <= 0) {
                $currentIterateDate->addMonth();
                continue;
            }

            // Calculate prorated credit based on effective start date (only for start month)
            $creditLeave = $this->calculateProratedCredit($effectiveStartDate, $processYear, $processMonth, $baseCreditLeave, $frequency);

            if ($creditLeave <= 0) {
                $currentIterateDate->addMonth();
                continue;
            }

            $newBalance     = $runningBalance + $creditLeave;
            Log::info("    💰 User ID {$user->id}: (Backfill) Balance before: {$runningBalance}, Credit: {$creditLeave}, New Balance: {$newBalance}");
            $runningBalance = $newBalance; // Update running balance for next iteration

            $remarks = $this->generateTransactionRemarks($creditLeave, $leaveName, $transactionDate, $effectiveStartDate, $frequency);

            // ✅ Create transaction with duplicate prevention
            // Transactions are created one by one in chronological order
            try {
                $transaction = LeaveTransaction::create([
                    'box_id'              => $box->id,
                    'box_user_id'         => $user->id,
                    'leave_type_id'       => $leaveType->id,
                    'date'                => $transactionDate->toDateString(),
                    'credit_leave'        => $creditLeave,
                    'debit_leave'         => 0,
                    'balance_leave'       => $newBalance,
                    'total_leave'         => $newBalance,
                    'transaction_remarks' => $remarks . ' (Backfilled)',
                    'applied_date'        => now(),
                    'approved_date'       => now(),
                ]);

                $this->line("    ↻ Backfilled credit for User ID: {$user->id} - {$processYear}-{$processMonth} ({$creditLeave} days)");
                $creditedMonths++;
            } catch (\Illuminate\Database\QueryException $e) {
                // Handle potential duplicate key errors
                if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'UNIQUE')) {
                    $this->warn("    ⚠️ Duplicate transaction prevented for User ID: {$user->id}, Month: {$processYear}-{$processMonth}");
                    $skippedMonths++;
                } else {
                    throw $e;
                }
            }

            $currentIterateDate->addMonth();
        }

        if ($creditedMonths > 0) {
            $skipMsg = $skippedMonths > 0 ? " (skipped {$skippedMonths} existing)" : "";
            Log::info("    ✓ Backfilled {$creditedMonths} month(s) for User ID: {$user->id}{$skipMsg}");
        }

        return $creditedMonths;
    }

    /**
     * Check if leave type should be processed based on frequency and current month
     */
    protected function shouldProcessLeaveType(string $frequency, int $month): bool
    {
        return match ($frequency) {
            'Monthly'         => true,                            // Process every month
            'Alternate Month' => $month % 2 === 1,                // Process in odd months (1, 3, 5, 7, 9, 11)
            'Quarterly'       => in_array($month, [1, 4, 7, 10]), // Process in Jan, Apr, Jul, Oct
            'Half Yearly'     => in_array($month, [1, 7]),        // Process in Jan, Jul
            'Yearly'          => $month === 1,                    // Process only in January
            default           => false,
        };
    }

    /**
     * Check if user should receive credit for the given month based on effective start date
     * (attendace_module_date if joining_date is before it, otherwise joining_date)
     */
    protected function shouldCreditUserForMonth(Carbon $effectiveStartDate, int $year, int $month): bool
    {
        $startYear  = $effectiveStartDate->year;
        $startMonth = $effectiveStartDate->month;

        // Effective start date is in a future year
        if ($startYear > $year) {
            return false;
        }

        // Effective start date is in a future month of the same year
        if ($startYear === $year && $startMonth > $month) {
            return false;
        }

        // Effective start date is in the same month or earlier - eligible for credit
        return true;
    }

    /**
     * Calculate credit leave based on frequency
     */
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

    /**
     * Calculate prorated credit based on effective start date
     * (attendace_module_date if joining_date is before it, otherwise joining_date)
     */
    protected function calculateProratedCredit(Carbon $effectiveStartDate, int $year, int $month, float $baseCredit, string $frequency): float
    {
        $startYear  = $effectiveStartDate->year;
        $startMonth = $effectiveStartDate->month;
        $startDay   = $effectiveStartDate->day;

        $currentMonthEnd = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        // If effective start date is in a previous month/year, give full credit
        if ($startYear < $year || ($startYear === $year && $startMonth < $month)) {
            return round($baseCredit, 2);
        }

        // If effective start date is in the current processing month, calculate proration
        if ($startYear === $year && $startMonth === $month) {
            $totalDaysInMonth = $currentMonthEnd->day;
            $daysFromStart    = $totalDaysInMonth - $startDay + 1;

            // Prorate based on days from effective start date
            $proratedCredit = ($baseCredit / $totalDaysInMonth) * $daysFromStart;

            return round($proratedCredit, 2);
        }

        // Default: full credit
        return round($baseCredit, 2);
    }

    /**
     * Generate transaction remarks with effective start date context
     * (attendace_module_date if joining_date is before it, otherwise joining_date)
     */
    protected function generateTransactionRemarks(float $creditLeave, string $leaveName, Carbon $transactionDate, Carbon $effectiveStartDate, string $frequency): string
    {
        $startYear        = $effectiveStartDate->year;
        $startMonth       = $effectiveStartDate->month;
        $transactionYear  = $transactionDate->year;
        $transactionMonth = $transactionDate->month;

        // Check if this is a prorated credit
        $isProrated = ($startYear === $transactionYear && $startMonth === $transactionMonth);

        if ($isProrated) {
            $daysInMonth   = $transactionDate->endOfMonth()->day;
            $daysFromStart = $daysInMonth - $effectiveStartDate->day + 1;
            return "Credited {$creditLeave} day(s) (prorated {$daysFromStart}/{$daysInMonth} days) for '{$leaveName}' in {$transactionDate->format('F Y')} - Effective start date: {$effectiveStartDate->format('d M Y')}";
        }
        return "Credited {$creditLeave} day(s) for '{$leaveName}' in {$transactionDate->format('F Y')} (frequency: {$frequency})";
    }

    /**
     * Handle year-end carry forward or lapse logic
     * Runs when the processing month is January
     */
    protected function handleYearlyCarryForwardOrLapse(Box $box, BoxUser $user, LeaveType $leaveType, int $year, float $currentBalance): float
    {
        if ($currentBalance <= 0) {
            return 0;
        }

        // ✅ Check if yearly reset already processed for this year change
        $existingReset = LeaveTransaction::where('box_id', $box->id)
            ->where('box_user_id', $user->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('transaction_remarks', 'LIKE', 'Yearly reset%')
            ->whereYear('date', $year)
            ->whereMonth('date', 1)
            ->exists();

        if ($existingReset) {
            Log::info("    ⏭️ Yearly reset already processed for User ID {$user->id}, Leave Type ID {$leaveType->id} in {$year}");
            return $currentBalance;
        }

        $carryForward     = $leaveType->carry_forward === 'yes';
        $carryForwardDays = (float) ($leaveType->carry_forward_days ?? 0);
        $newBalance       = $currentBalance;
        $debitAmount      = 0;
        $lapsedAmount     = 0;
        $cfAmount         = 0;
        $remarks          = "";

        if ($carryForward) {
            // Capped carry forward
            if ($carryForwardDays > 0 && $currentBalance > $carryForwardDays) {
                $lapsedAmount = $currentBalance - $carryForwardDays;
                $cfAmount     = $carryForwardDays;
                $debitAmount  = $lapsedAmount;
                $newBalance   = $carryForwardDays;
                $remarks      = "Yearly reset: Carried forward {$cfAmount} days (capped from {$currentBalance} days), Lapsed {$lapsedAmount} days";
            } else {
                // Unlimited carry forward or within limit
                $cfAmount = $currentBalance;
                $remarks  = "Yearly reset: Carried forward {$cfAmount} days";
                // Even if no debit is needed for the balance, we might want to record the CF amount
                // But typically we only create a transaction if the balance changes (debit/credit)
                // However, the user wants to "check" these columns, so let's create a transaction to show the CF
                $debitAmount = 0;
            }
        } else {
            // No carry forward - lapse everything
            $lapsedAmount = $currentBalance;
            $cfAmount     = 0;
            $debitAmount  = $currentBalance;
            $newBalance   = 0;
            $remarks      = "Yearly reset: Lapsed {$lapsedAmount} days (carry forward disabled)";
        }

        // Create transaction to record the yearly reset (lapse and/or carry forward)
        try {
            LeaveTransaction::create([
                'box_id'                => $box->id,
                'box_user_id'           => $user->id,
                'leave_type_id'         => $leaveType->id,
                'date'                  => Carbon::create($year, 1, 1)->toDateString(), // Recorded on Jan 1st
                'credit_leave'          => 0,
                'debit_leave'           => $debitAmount,
                'lapsed_leave'          => $lapsedAmount,
                'carried_forward_leave' => $cfAmount,
                'balance_leave'         => $newBalance,
                'total_leave'           => $newBalance,
                'transaction_remarks'   => $remarks,
                'applied_date'          => now(),
                'approved_date'         => now(),
            ]);
            Log::info("    📉 User ID {$user->id}: {$remarks}");
        } catch (\Exception $e) {
            Log::error("    ✗ Error in yearly reset for User ID {$user->id}: " . $e->getMessage());
        }

        return $newBalance;
    }
}
