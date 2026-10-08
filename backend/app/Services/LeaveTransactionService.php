<?php
namespace App\Services;

use App\Models\{Box, BoxUser, LeaveRequest, LeaveTransaction, LeaveType};
use App\Jobs\ExportLeaveTransactionJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log, Storage};
use Yajra\DataTables\Facades\DataTables;

class LeaveTransactionService
{
     public function calculate_leave_transactions_all(Request $request)
    {
        try {
            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (!$boxUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Box user not found',
                    'data' => [],
                    'summary' => []
                ], 404);
            }

            $boxId = $boxUser->box_id;
            $year = $request->get('year');
            $month = $request->get('month');

            // If no month/year filter, return all existing transactions
            if (!$year || !$month) {
                return $this->getAllExistingLeaveTransactions($boxId);
            }

            // Get all box_users for this box
            $allBoxUsers = BoxUser::where('box_id', $boxId)
                ->with(['user', 'departmentRelation', 'designationRelation'])
                ->get();

            // Get all active leave types for this box
            $leaveTypes = LeaveType::where('box_id', $boxId)
                ->where('status', 'Active')
                ->whereNotNull('credit_frequency')
                ->get();

            $calculatedTransactions = [];

            foreach ($allBoxUsers as $boxUserItem) {
                if (empty($boxUserItem->joining_date)) {
                    continue;
                }

                $joiningDate = Carbon::parse($boxUserItem->joining_date);
                $currentMonthStart = Carbon::create($year, $month, 1)->startOfDay();
                $currentMonthEnd = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

                // Skip if user joined after the current processing month
                if ($joiningDate->gt($currentMonthEnd)) {
                    continue;
                }

                foreach ($leaveTypes as $leaveType) {
                    $frequency = $leaveType->credit_frequency;
                    $creditDays = (float) $leaveType->credit_days;

                    // Check if this month should be credited based on frequency
                    $shouldCredit = match ($frequency) {
                        'Monthly' => true,
                        'Alternate Month' => $month % 2 === 1,
                        'Quarterly' => in_array($month, [1, 4, 7, 10]),
                        'Half Yearly' => in_array($month, [1, 7]),
                        'Yearly' => $month === 1,
                        default => false,
                    };

                    if (!$shouldCredit) {
                        continue;
                    }

                    // Check if user should receive credit for this month
                    $joiningYear = $joiningDate->year;
                    $joiningMonth = $joiningDate->month;

                    if ($joiningYear > $year || ($joiningYear === $year && $joiningMonth > $month)) {
                        continue;
                    }

                    // Calculate base credit leave
                    $baseCreditLeave = match ($frequency) {
                        'Monthly' => $creditDays,
                        'Alternate Month' => $month % 2 === 1 ? $creditDays : 0,
                        'Quarterly' => in_array($month, [1, 4, 7, 10]) ? $creditDays : 0,
                        'Half Yearly' => in_array($month, [1, 7]) ? $creditDays : 0,
                        'Yearly' => $month === 1 ? $creditDays : 0,
                        default => 0,
                    };

                    if ($baseCreditLeave <= 0) {
                        continue;
                    }

                    // Calculate prorated credit
                    $creditLeave = $baseCreditLeave;
                    if ($joiningYear === $year && $joiningMonth === $month) {
                        $totalDaysInMonth = $currentMonthEnd->day;
                        $daysFromJoining = $totalDaysInMonth - $joiningDate->day + 1;
                        $creditLeave = round(($baseCreditLeave / $totalDaysInMonth) * $daysFromJoining, 2);
                    }

                    if ($creditLeave <= 0) {
                        continue;
                    }

                    // Get existing transaction to check if already exists
                    $existingTransaction = LeaveTransaction::where('box_id', $boxId)
                        ->where('box_user_id', $boxUserItem->id)
                        ->where('leave_type_id', $leaveType->id)
                        ->whereYear('date', $year)
                        ->whereMonth('date', $month)
                        ->first();

                    // Get previous balance
                    $lastTransaction = LeaveTransaction::where('box_id', $boxId)
                        ->where('box_user_id', $boxUserItem->id)
                        ->where('leave_type_id', $leaveType->id)
                        ->orderBy('date', 'desc')
                        ->orderBy('id', 'desc')
                        ->first();

                    $previousBalance = $lastTransaction?->balance_leave ?? 0;
                    $newBalance = $previousBalance + $creditLeave;

                    // Generate remarks
                    $isProrated = ($joiningYear === $year && $joiningMonth === $month);
                    $transactionDate = Carbon::create($year, $month, 1);

                    if ($isProrated) {
                        $daysInMonth = $transactionDate->endOfMonth()->day;
                        $daysFromJoining = $daysInMonth - $joiningDate->day + 1;
                        $remarks = "Credited {$creditLeave} day(s) (prorated {$daysFromJoining}/{$daysInMonth} days) for '{$leaveType->leave_type}' in {$transactionDate->format('F Y')} - User joined on {$joiningDate->format('d M Y')}";
                    } else {
                        $remarks = "Credited {$creditLeave} day(s) for '{$leaveType->leave_type}' in {$transactionDate->format('F Y')} (frequency: {$frequency})";
                    }

                    $calculatedTransactions[] = [
                        'box_user_id' => $boxUserItem->id,
                        'employee_id' => $boxUserItem->employee_id ?? 'N/A',
                        'employee_name' => $boxUserItem->user->name ?? 'N/A',
                        'department' => $boxUserItem->departmentRelation->name ?? 'N/A',
                        'designation' => $boxUserItem->designationRelation->name ?? 'N/A',
                        'joining_date' => $joiningDate->format('Y-m-d'),
                        'leave_type_id' => $leaveType->id,
                        'leave_type' => $leaveType->leave_type,
                        'credit_frequency' => $frequency,
                        'credit_days' => $creditDays,
                        'transaction_date' => $transactionDate->format('Y-m-d'),
                        'credit_leave' => $creditLeave,
                        'previous_balance' => $previousBalance,
                        'new_balance' => $newBalance,
                        'transaction_remarks' => $remarks,
                        'already_exists' => $existingTransaction ? true : false,
                        'existing_transaction_id' => $existingTransaction?->id,
                    ];
                }
            }

            // Calculate summary per user
            $summary = [];
            foreach ($allBoxUsers as $boxUserItem) {
                $userTransactions = array_filter($calculatedTransactions, function($t) use ($boxUserItem) {
                    return $t['box_user_id'] == $boxUserItem->id;
                });

                $totalCredits = array_sum(array_column($userTransactions, 'credit_leave'));
                $transactionCount = count($userTransactions);

                $summary[] = [
                    'box_user_id' => $boxUserItem->id,
                    'employee_id' => $boxUserItem->employee_id ?? 'N/A',
                    'employee_name' => $boxUserItem->user->name ?? 'N/A',
                    'total_credits' => round($totalCredits, 2),
                    'transaction_count' => $transactionCount,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Leave transactions calculated successfully',
                'data' => $calculatedTransactions,
                'summary' => $summary,
                'total' => count($calculatedTransactions),
                'is_calculated' => true,
            ]);

        } catch (\Throwable $th) {
            Log::error('Error calculating leave transactions: ' . $th->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $th->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while calculating leave transactions.',
                'error' => $th->getMessage(),
                'data' => [],
                'summary' => [],
            ], 500);
        }
    }

    /**
     * Calculate leave transactions for team logic
     */
    public function calculate_team_leave_transactions_all(Request $request)
    {
        try {
            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (!$boxUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Box user not found',
                    'data' => [],
                    'summary' => []
                ], 404);
            }

            $boxId = $boxUser->box_id;
            $year = $request->get('year');
            $month = $request->get('month');

            $allSubordinateIds = BoxUser::getAllSubordinateIds($boxId, $userId, $boxUser);

            // If no month/year filter, return all existing transactions
            if (!$year || !$month) {
                return $this->getAllExistingTeamLeaveTransactions($boxId, $allSubordinateIds);
            }

            // Get all box_users for this box that are subordinates
            $allBoxUsers = BoxUser::where('box_id', $boxId)
                ->whereIn('id', $allSubordinateIds)
                ->with(['user', 'departmentRelation', 'designationRelation'])
                ->get();

            // Get all active leave types for this box
            $leaveTypes = LeaveType::where('box_id', $boxId)
                ->where('status', 'Active')
                ->whereNotNull('credit_frequency')
                ->get();

            $calculatedTransactions = [];

            foreach ($allBoxUsers as $boxUserItem) {
                if (empty($boxUserItem->joining_date)) {
                    continue;
                }

                $joiningDate = Carbon::parse($boxUserItem->joining_date);
                $currentMonthStart = Carbon::create($year, $month, 1)->startOfDay();
                $currentMonthEnd = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

                // Skip if user joined after the current processing month
                if ($joiningDate->gt($currentMonthEnd)) {
                    continue;
                }

                foreach ($leaveTypes as $leaveType) {
                    $frequency = $leaveType->credit_frequency;
                    $creditDays = (float) $leaveType->credit_days;

                    // Check if this month should be credited based on frequency
                    $shouldCredit = match ($frequency) {
                        'Monthly' => true,
                        'Alternate Month' => $month % 2 === 1,
                        'Quarterly' => in_array($month, [1, 4, 7, 10]),
                        'Half Yearly' => in_array($month, [1, 7]),
                        'Yearly' => $month === 1,
                        default => false,
                    };

                    if (!$shouldCredit) {
                        continue;
                    }

                    // Check if user should receive credit for this month
                    $joiningYear = $joiningDate->year;
                    $joiningMonth = $joiningDate->month;

                    if ($joiningYear > $year || ($joiningYear === $year && $joiningMonth > $month)) {
                        continue;
                    }

                    // Calculate base credit leave
                    $baseCreditLeave = match ($frequency) {
                        'Monthly' => $creditDays,
                        'Alternate Month' => $month % 2 === 1 ? $creditDays : 0,
                        'Quarterly' => in_array($month, [1, 4, 7, 10]) ? $creditDays : 0,
                        'Half Yearly' => in_array($month, [1, 7]) ? $creditDays : 0,
                        'Yearly' => $month === 1 ? $creditDays : 0,
                        default => 0,
                    };

                    if ($baseCreditLeave <= 0) {
                        continue;
                    }

                    // Calculate prorated credit
                    $creditLeave = $baseCreditLeave;
                    if ($joiningYear === $year && $joiningMonth === $month) {
                        $totalDaysInMonth = $currentMonthEnd->day;
                        $daysFromJoining = $totalDaysInMonth - $joiningDate->day + 1;
                        $creditLeave = round(($baseCreditLeave / $totalDaysInMonth) * $daysFromJoining, 2);
                    }

                    if ($creditLeave <= 0) {
                        continue;
                    }

                    // Get existing transaction to check if already exists
                    $existingTransaction = LeaveTransaction::where('box_id', $boxId)
                        ->where('box_user_id', $boxUserItem->id)
                        ->where('leave_type_id', $leaveType->id)
                        ->whereYear('date', $year)
                        ->whereMonth('date', $month)
                        ->first();

                    // Get previous balance
                    $lastTransaction = LeaveTransaction::where('box_id', $boxId)
                        ->where('box_user_id', $boxUserItem->id)
                        ->where('leave_type_id', $leaveType->id)
                        ->orderBy('date', 'desc')
                        ->orderBy('id', 'desc')
                        ->first();

                    $previousBalance = $lastTransaction?->balance_leave ?? 0;
                    $newBalance = $previousBalance + $creditLeave;

                    // Generate remarks
                    $isProrated = ($joiningYear === $year && $joiningMonth === $month);
                    $transactionDate = Carbon::create($year, $month, 1);

                    if ($isProrated) {
                        $daysInMonth = $transactionDate->endOfMonth()->day;
                        $daysFromJoining = $daysInMonth - $joiningDate->day + 1;
                        $remarks = "Credited {$creditLeave} day(s) (prorated {$daysFromJoining}/{$daysInMonth} days) for '{$leaveType->leave_type}' in {$transactionDate->format('F Y')} - User joined on {$joiningDate->format('d M Y')}";
                    } else {
                        $remarks = "Credited {$creditLeave} day(s) for '{$leaveType->leave_type}' in {$transactionDate->format('F Y')} (frequency: {$frequency})";
                    }

                    $calculatedTransactions[] = [
                        'box_user_id' => $boxUserItem->id,
                        'employee_id' => $boxUserItem->employee_id ?? 'N/A',
                        'employee_name' => $boxUserItem->user->name ?? 'N/A',
                        'department' => $boxUserItem->departmentRelation->name ?? 'N/A',
                        'designation' => $boxUserItem->designationRelation->name ?? 'N/A',
                        'joining_date' => $joiningDate->format('Y-m-d'),
                        'leave_type_id' => $leaveType->id,
                        'leave_type' => $leaveType->leave_type,
                        'credit_frequency' => $frequency,
                        'credit_days' => $creditDays,
                        'transaction_date' => $transactionDate->format('Y-m-d'),
                        'credit_leave' => $creditLeave,
                        'previous_balance' => $previousBalance,
                        'new_balance' => $newBalance,
                        'transaction_remarks' => $remarks,
                        'already_exists' => $existingTransaction ? true : false,
                        'existing_transaction_id' => $existingTransaction?->id,
                    ];
                }
            }

            // Calculate summary per user
            $summary = [];
            foreach ($allBoxUsers as $boxUserItem) {
                $userTransactions = array_filter($calculatedTransactions, function($t) use ($boxUserItem) {
                    return $t['box_user_id'] == $boxUserItem->id;
                });

                $totalCredits = array_sum(array_column($userTransactions, 'credit_leave'));
                $transactionCount = count($userTransactions);

                $summary[] = [
                    'box_user_id' => $boxUserItem->id,
                    'employee_id' => $boxUserItem->employee_id ?? 'N/A',
                    'employee_name' => $boxUserItem->user->name ?? 'N/A',
                    'total_credits' => round($totalCredits, 2),
                    'transaction_count' => $transactionCount,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Team leave transactions calculated successfully',
                'data' => $calculatedTransactions,
                'summary' => $summary,
                'total' => count($calculatedTransactions),
                'is_calculated' => true,
            ]);

        } catch (\Throwable $th) {
            Log::error('Error calculating team leave transactions: ' . $th->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $th->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while calculating team leave transactions.',
                'error' => $th->getMessage(),
                'data' => [],
                'summary' => [],
            ], 500);
        }
    }

    /**
     * Get all existing team leave transactions for subordinates
     */
    protected function getAllExistingTeamLeaveTransactions($boxId, $allSubordinateIds)
    {
        // Get all box_users for this box that are subordinates
        $allBoxUsers = BoxUser::where('box_id', $boxId)
            ->whereIn('id', $allSubordinateIds)
            ->with(['user', 'departmentRelation', 'designationRelation'])
            ->get();

        // Get all leave transactions for this box and subordinates
        $allTransactions = LeaveTransaction::where('box_id', $boxId)
            ->whereIn('box_user_id', $allSubordinateIds)
            ->with(['leaveType', 'boxUser.user', 'boxUser.departmentRelation', 'boxUser.designationRelation', 'leaveRequest'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $transactionsData = [];
        $summary = [];

        foreach ($allTransactions as $transaction) {
            $boxUserItem = $transaction->boxUser;
            if (!$boxUserItem) continue;

            $transactionsData[] = [
                'box_user_id' => $boxUserItem->id,
                'employee_id' => $boxUserItem->employee_id ?? 'N/A',
                'employee_name' => $boxUserItem->user->name ?? 'N/A',
                'department' => $boxUserItem->departmentRelation->name ?? 'N/A',
                'designation' => $boxUserItem->designationRelation->name ?? 'N/A',
                'joining_date' => $boxUserItem->joining_date ? Carbon::parse($boxUserItem->joining_date)->format('Y-m-d') : null,
                'leave_type_id' => $transaction->leave_type_id,
                'leave_type' => $transaction->leaveType->leave_type ?? 'N/A',
                'credit_frequency' => $transaction->leaveType->credit_frequency ?? 'N/A',
                'credit_days' => $transaction->leaveType->credit_days ?? 0,
                'transaction_date' => Carbon::parse($transaction->date)->format('Y-m-d'),
                'credit_leave' => $transaction->credit_leave ?? 0,
                'debit_leave' => $transaction->debit_leave ?? 0,
                'previous_balance' => $transaction->opening_balance ?? 0,
                'new_balance' => $transaction->balance_leave ?? 0,
                'transaction_remarks' => $transaction->transaction_remarks ?? '',
                'applied_date'        => $transaction->applied_date ? Carbon::parse($transaction->applied_date)->format('d M Y') : ($transaction->leaveRequest ? Carbon::parse($transaction->leaveRequest->created_at)->format('d M Y') : '-'),
                'approved_date'       => $transaction->approved_date ? Carbon::parse($transaction->approved_date)->format('d M Y') : (($transaction->leaveRequest && $transaction->leaveRequest->status === 'approved') ? Carbon::parse($transaction->leaveRequest->updated_at)->format('d M Y') : ($transaction->created_at ? Carbon::parse($transaction->created_at)->format('d M Y') : '-')),
                'already_exists'      => true,
                'transaction_id'      => $transaction->id,
            ];
        }

        // Calculate summary per user
        foreach ($allBoxUsers as $boxUserItem) {
            $userTransactions = LeaveTransaction::where('box_id', $boxId)
                ->where('box_user_id', $boxUserItem->id)
                ->where('credit_leave', '>', 0)
                ->get();

            $totalCredits = $userTransactions->sum('credit_leave');
            $transactionCount = $userTransactions->count();

            // Get latest balance for each leave type
            $leaveTypeBalances = LeaveTransaction::where('box_id', $boxId)
                ->where('box_user_id', $boxUserItem->id)
                ->get()
                ->groupBy('leave_type_id')
                ->map(function ($transactions) {
                    return $transactions->sortByDesc('date')->sortByDesc('id')->first()->balance_leave ?? 0;
                });

            $summary[] = [
                'box_user_id' => $boxUserItem->id,
                'employee_id' => $boxUserItem->employee_id ?? 'N/A',
                'employee_name' => $boxUserItem->user->name ?? 'N/A',
                'total_credits' => round($totalCredits, 2),
                'transaction_count' => $transactionCount,
                'current_balances' => $leaveTypeBalances->toArray(),
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'All existing team leave transactions retrieved successfully',
            'data' => $transactionsData,
            'summary' => $summary,
            'total' => count($transactionsData),
            'is_calculated' => false,
        ]);
    }

    /**
     * Get all existing leave transactions for all users in a box
     */
    protected function getAllExistingLeaveTransactions($boxId)
    {
        // Get all box_users for this box
        $allBoxUsers = BoxUser::where('box_id', $boxId)
            ->with(['user', 'departmentRelation', 'designationRelation'])
            ->get();

        // Get all leave transactions for this box
        $allTransactions = LeaveTransaction::where('box_id', $boxId)
            ->with(['leaveType', 'boxUser.user', 'boxUser.departmentRelation', 'boxUser.designationRelation', 'leaveRequest'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $transactionsData = [];
        $summary = [];

        foreach ($allTransactions as $transaction) {
            $boxUserItem = $transaction->boxUser;
            if (!$boxUserItem) continue;

            $transactionsData[] = [
                'box_user_id' => $boxUserItem->id,
                'employee_id' => $boxUserItem->employee_id ?? 'N/A',
                'employee_name' => $boxUserItem->user->name ?? 'N/A',
                'department' => $boxUserItem->departmentRelation->name ?? 'N/A',
                'designation' => $boxUserItem->designationRelation->name ?? 'N/A',
                'joining_date' => $boxUserItem->joining_date ? Carbon::parse($boxUserItem->joining_date)->format('Y-m-d') : null,
                'leave_type_id' => $transaction->leave_type_id,
                'leave_type' => $transaction->leaveType->leave_type ?? 'N/A',
                'credit_frequency' => $transaction->leaveType->credit_frequency ?? 'N/A',
                'credit_days' => $transaction->leaveType->credit_days ?? 0,
                'transaction_date' => Carbon::parse($transaction->date)->format('Y-m-d'),
                'credit_leave' => $transaction->credit_leave ?? 0,
                'debit_leave' => $transaction->debit_leave ?? 0,
                'previous_balance' => $transaction->opening_balance ?? 0,
                'new_balance' => $transaction->balance_leave ?? 0,
                'transaction_remarks' => $transaction->transaction_remarks ?? '',
                'applied_date'        => $transaction->applied_date ? Carbon::parse($transaction->applied_date)->format('d M Y') : ($transaction->leaveRequest ? Carbon::parse($transaction->leaveRequest->created_at)->format('d M Y') : '-'),
                'approved_date'       => $transaction->approved_date ? Carbon::parse($transaction->approved_date)->format('d M Y') : (($transaction->leaveRequest && $transaction->leaveRequest->status === 'approved') ? Carbon::parse($transaction->leaveRequest->updated_at)->format('d M Y') : ($transaction->created_at ? Carbon::parse($transaction->created_at)->format('d M Y') : '-')),
                'already_exists'      => true,
                'transaction_id'      => $transaction->id,
            ];
        }

        // Calculate summary per user
        foreach ($allBoxUsers as $boxUserItem) {
            $userTransactions = LeaveTransaction::where('box_id', $boxId)
                ->where('box_user_id', $boxUserItem->id)
                ->where('credit_leave', '>', 0)
                ->get();

            $totalCredits = $userTransactions->sum('credit_leave');
            $transactionCount = $userTransactions->count();

            // Get latest balance for each leave type
            $leaveTypeBalances = LeaveTransaction::where('box_id', $boxId)
                ->where('box_user_id', $boxUserItem->id)
                ->get()
                ->groupBy('leave_type_id')
                ->map(function ($transactions) {
                    return $transactions->sortByDesc('date')->sortByDesc('id')->first()->balance_leave ?? 0;
                });

            $summary[] = [
                'box_user_id' => $boxUserItem->id,
                'employee_id' => $boxUserItem->employee_id ?? 'N/A',
                'employee_name' => $boxUserItem->user->name ?? 'N/A',
                'total_credits' => round($totalCredits, 2),
                'transaction_count' => $transactionCount,
                'current_balances' => $leaveTypeBalances->toArray(),
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'All existing leave transactions retrieved successfully',
            'data' => $transactionsData,
            'summary' => $summary,
            'total' => count($transactionsData),
            'is_calculated' => false,
        ]);
    }

    public function store_leave_transaction(Request $request)
    {
        try {
            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (!$boxUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Box user not found',
                ], 404);
            }

            $boxId = $boxUser->box_id;
            $boxUserId = $request->input('box_user_id');
            $leaveTypes = $request->input('leave_types', []);

            // Validation
            if (!$boxUserId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employee is required.',
                ], 422);
            }

            if (empty($leaveTypes) || !is_array($leaveTypes)) {
                return response()->json([
                    'status' => false,
                    'message' => 'At least one leave type is required.',
                ], 422);
            }

            // Verify box_user belongs to same box
            $targetBoxUser = BoxUser::where('id', $boxUserId)
                ->where('box_id', $boxId)
                ->first();

            if (!$targetBoxUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employee not found or does not belong to your box.',
                ], 404);
            }

            $createdTransactions = [];

            DB::beginTransaction();

            foreach ($leaveTypes as $leaveTypeId => $leaveData) {
                // Validate leave type exists and belongs to box
                $leaveType = LeaveType::where('id', $leaveTypeId)
                    ->where('box_id', $boxId)
                    ->where('status', 'Active')
                    ->first();

                if (!$leaveType) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => "Leave type with ID {\$leaveTypeId} not found or inactive.",
                    ], 404);
                }

                $creditLeave = (float) ($leaveData['credit_leave'] ?? 0);
                $transactionDate = $leaveData['transaction_date'] ?? now()->format('Y-m-d');
                $remarks = $leaveData['remarks'] ?? '';

                if ($creditLeave <= 0) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => "Credit leave must be greater than 0 for {\$leaveType->leave_type}.",
                    ], 422);
                }

                // Get previous balance
                $lastTransaction = LeaveTransaction::where('box_id', $boxId)
                    ->where('box_user_id', $boxUserId)
                    ->where('leave_type_id', $leaveTypeId)
                    ->orderBy('date', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                $previousBalance = $lastTransaction?->balance_leave ?? 0;
                $newBalance = $previousBalance + $creditLeave;

                // Create transaction
                $transaction = LeaveTransaction::create([
                    'box_id' => $boxId,
                    'box_user_id' => $boxUserId,
                    'leave_type_id' => $leaveTypeId,
                    'date' => $transactionDate,
                    'credit_leave' => $creditLeave,
                    'debit_leave' => 0,
                    'opening_balance' => $previousBalance,
                    'balance_leave' => $newBalance,
                    'total_leave'         => $newBalance,
                    'transaction_remarks' => $remarks ?: "Manually credited {$creditLeave} day(s) for '{$leaveType->leave_type}' on {$transactionDate}",
                    'applied_date'        => now(),
                    'approved_date'       => now(),
                ]);

                $createdTransactions[] = $transaction;
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => count($createdTransactions) . ' leave transaction(s) added successfully.',
                'data' => $createdTransactions,
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error storing leave transaction: ' . $th->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $th->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while adding leave transactions.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
     public function Leave_dataTransaction(Request $request)
    {
        try {
            $userId  = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (! $boxUser) {
                // Check if DataTables request
                if ($request->has('draw')) {
                    return DataTables::of(collect([]))->make(true);
                }
                return response()->json([
                    'status'  => false,
                    'message' => 'Box user not found',
                ], 404);
            }

            $boxId            = $boxUser->box_id;
            $year             = $request->get('year', $request->get('year', now()->year));
            $joiningDate      = Carbon::parse($boxUser->joining_date ?? now());
            $currentYearStart = Carbon::create($year, 1, 1)->startOfMonth();
            $currentYearEnd   = Carbon::create($year, 12, 31)->startOfMonth();

            $eligibleMonthStart = $joiningDate->year == $year
                ? Carbon::create($year, $joiningDate->month, 1)->startOfMonth()
                : $currentYearStart;

            $monthsEligible = $eligibleMonthStart->diffInMonths($currentYearEnd) + 1;

            // Base query
            $query = LeaveTransaction::with(['leaveType', 'leaveRequest'])
                ->where('box_user_id', $boxUser->id)
                ->whereYear('date', $year);

            // Apply date range filter
            if ($request->filled('dateRange')) {
                $dates = explode(' - ', $request->dateRange);
                if (count($dates) === 2) {
                    try {
                        $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                        $endDate   = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                        $query->whereBetween('date', [$startDate, $endDate]);
                    } catch (\Exception $e) {
                        // Invalid date format, skip filter
                    }
                }
            }

            // Apply Leave Types filter
            if ($request->filled('leaveTypes') && is_array($request->leaveTypes)) {
                $query->whereHas('leaveType', function ($q) use ($request) {
                    $q->whereIn('leave_type', $request->leaveTypes);
                });
            }

            // Apply Credit/Debit filters
            if ($request->filled('creditDebitFilters') && is_array($request->creditDebitFilters)) {
                $filters = $request->creditDebitFilters;
                $query->where(function ($q) use ($filters) {
                    if (in_array('credit', $filters)) {
                        $q->orWhere('credit_leave', '>', 0);
                    }
                    if (in_array('debit', $filters)) {
                        $q->orWhere('debit_leave', '>', 0);
                    }
                });
            }

            // Check if this is a DataTables request
            if ($request->has('draw')) {
                // Return DataTables format
                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('formatted_date', function ($row) {
                        return Carbon::parse($row->date)->format('d M Y');
                    })
                    ->addColumn('leave_type_name', function ($row) {
                        return $row->leaveType?->leave_type ?? '-';
                    })
                    ->addColumn('formatted_credit', function ($row) {
                        return (float) $row->credit_leave;
                    })
                    ->addColumn('formatted_debit', function ($row) {
                        return (float) $row->debit_leave;
                    })
                    ->addColumn('formatted_balance', function ($row) {
                        return (float) $row->balance_leave;
                    })
                    ->addColumn('lapsed_leave', function ($row) {
                        return (float) $row->lapsed_leave;
                    })
                    ->addColumn('carried_forward_leave', function ($row) {
                        return (float) $row->carried_forward_leave;
                    })
                    ->addColumn('transaction_type', function ($row) {
                        if ($row->credit_leave > 0) {
                            return 'credit';
                        } elseif ($row->debit_leave > 0) {
                            return 'debit';
                        }
                        return 'neutral';
                    })
                    ->addColumn('applied_date', function ($row) {
                        return $row->applied_date ? Carbon::parse($row->applied_date)->format('d M Y') : ($row->leaveRequest ? Carbon::parse($row->leaveRequest->created_at)->format('d M Y') : '-');
                    })
                    ->addColumn('approved_date', function ($row) {
                        if ($row->approved_date) {
                            return Carbon::parse($row->approved_date)->format('d M Y');
                        }
                        if ($row->leaveRequest && $row->leaveRequest->status === 'approved') {
                            return Carbon::parse($row->leaveRequest->updated_at)->format('d M Y');
                        }
                        return $row->created_at ? Carbon::parse($row->created_at)->format('d M Y') : '-';
                    })
                    ->rawColumns(['transaction_remarks'])
                    ->orderColumn('formatted_date', function ($query, $order) {
                        $query->orderBy('date', $order);
                    })
                    ->orderColumn('leave_type_name', function ($query, $order) {
                        $query->orderBy('leave_type_id', $order);
                    })
                    ->filterColumn('leave_type_name', function ($query, $keyword) {
                        $query->whereHas('leaveType', function ($q) use ($keyword) {
                            $q->where('leave_type', 'like', "%{$keyword}%");
                        });
                    })
                    ->filterColumn('formatted_date', function ($query, $keyword) {
                        $query->whereRaw("DATE_FORMAT(date, '%d %b %Y') like ?", ["%{$keyword}%"]);
                    })
                    ->make(true);
            }

            // Regular request - return JSON with transactions and leave types
            $transactions = $query
                ->orderBy('leave_type_id', 'asc')
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($row) {
                    return [
                        'date'                => Carbon::parse($row->date)->format('d M Y'),
                        'leave_type'          => $row->leaveType?->leave_type ?? '-',
                        'credit_leave'        => $row->credit_leave,
                        'opening_balance'     => $row->opening_balance,
                        'transaction_remarks' => $row->transaction_remarks,
                        'debit_leave'         => $row->debit_leave,
                        'lapsed_leave'        => $row->lapsed_leave,
                        'carried_forward_leave' => $row->carried_forward_leave,
                        'balance_leave'       => $row->balance_leave,
                        'applied_date'        => $row->leaveRequest ? Carbon::parse($row->leaveRequest->created_at)->format('d M Y') : '-',
                        'approved_date'       => ($row->leaveRequest && $row->leaveRequest->status === 'approved') ? Carbon::parse($row->leaveRequest->updated_at)->format('d M Y') : ($row->created_at ? Carbon::parse($row->created_at)->format('d M Y') : '-'),
                    ];
                });

            $transactionsByYear = [$year => $transactions];

            // Pre-fetch leave requests grouped by leave_type_id for efficiency
            $leaveRequestsGrouped = LeaveRequest::where('box_user_id', $boxUser->id)
                ->whereIn('status', ['approved', 'pending'])
                ->whereYear('from_date', $year)
                ->get()
                ->groupBy('leave_type');

            // Get latest balance from transactions for each leave type
            $latestBalances = LeaveTransaction::where('box_user_id', $boxUser->id)
                ->whereYear('date', $year)
                ->get()
                ->groupBy('leave_type_id')
                ->map(function ($transactions) {
                    return $transactions->sortByDesc('date')->sortByDesc('id')->first()->balance_leave ?? 0;
                });

            $leaveTypes = LeaveType::where('box_id', $boxId)
                ->where('status', 'Active')
                ->orderBy('id', 'asc')  // Order by ID to match processing order
                ->get()
                ->map(function ($type) use ($monthsEligible, $leaveRequestsGrouped, $latestBalances) {
                    $totalCreditable = 0;
                    if (strtolower($type->credit_frequency) == 'monthly') {
                        $totalCreditable = round($monthsEligible * (float) $type->credit_days, 2);
                    }

                    $leavesTaken = 0;
                    if (isset($leaveRequestsGrouped[$type->id])) {
                        $leavesTaken = $leaveRequestsGrouped[$type->id]->sum('total_days'); // Correct field here
                    }

                    // Use actual balance from latest transaction, fallback to calculation
                    $balanceLeave = $latestBalances->get($type->id) ?? ($totalCreditable - $leavesTaken);

                    return [
                        'id'                    => $type->id,
                        'box_id'                => $type->box_id,
                        'leave_type'            => $type->leave_type,
                        'credit_frequency'      => $type->credit_frequency,
                        'credit_days'           => $type->credit_days,
                        'number_of_days'        => $type->number_of_days,
                        'leave_code'            => $type->leave_code,
                        'leave_color'           => $type->leave_color,
                        'leave_icon'            => $type->leave_icon,
                        'leave_credit_type'     => $type->leave_credit_type,
                        'carry_forward'         => $type->carry_forward,
                        'carry_forward_days'    => $type->carry_forward_days,
                        'status'                => $type->status,
                        'months_eligible'       => $monthsEligible,
                        'total_creditable_days' => $totalCreditable,
                        'leaves_taken'          => $leavesTaken,
                        'balance_leave'         => $balanceLeave,
                    ];
                });

            // Get all available years from database for this box_user (based on date field)
            $availableYears = LeaveTransaction::where('box_user_id', $boxUser->id)
                ->whereNotNull('date')
                ->selectRaw('YEAR(date) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->toArray();

            // If no transactions exist, include current year
            if (empty($availableYears)) {
                $availableYears = [now()->year];
            }

            // Check if request is to fetch all years data in one call
            if ($request->get('fetch_all_years') == 'true') {
                $allTransactionsByYear = [];
                $allLeaveTypes = [];

                // Fetch data for all available years
                foreach ($availableYears as $yr) {
                    $yearQuery = LeaveTransaction::with('leaveType')
                        ->where('box_user_id', $boxUser->id)
                        ->whereYear('date', $yr)
                        ->orderBy('leave_type_id', 'asc')
                        ->orderBy('date', 'desc')
                        ->orderBy('id', 'desc')
                        ->get()
                        ->map(function ($row) {
                            return [
                                'date'                => Carbon::parse($row->date)->format('d M Y'),
                                'leave_type'          => $row->leaveType?->leave_type ?? '-',
                                'credit_leave'        => $row->credit_leave,
                                'opening_balance'     => $row->opening_balance,
                                'transaction_remarks' => $row->transaction_remarks,
                                'debit_leave'         => $row->debit_leave,
                                'balance_leave'       => $row->balance_leave,
                                'applied_date'        => $row->applied_date ? Carbon::parse($row->applied_date)->format('d M Y') : ($row->leaveRequest ? Carbon::parse($row->leaveRequest->created_at)->format('d M Y') : '-'),
                                'approved_date'       => $row->approved_date ? Carbon::parse($row->approved_date)->format('d M Y') : (($row->leaveRequest && $row->leaveRequest->status === 'approved') ? Carbon::parse($row->leaveRequest->updated_at)->format('d M Y') : ($row->created_at ? Carbon::parse($row->created_at)->format('d M Y') : '-')),
                            ];
                        });

                    $allTransactionsByYear[$yr] = $yearQuery;
                }

                // Get leave types (same for all years)
                $allLeaveTypes = LeaveType::where('box_id', $boxId)
                    ->where('status', 'Active')
                    ->orderBy('id', 'asc')
                    ->get()
                    ->map(function ($type) use ($boxUser) {
                        $currentYear = now()->year;
                        $joiningDate = Carbon::parse($boxUser->joining_date ?? now());
                        $currentYearStart = Carbon::create($currentYear, 1, 1)->startOfMonth();
                        $currentYearEnd = Carbon::create($currentYear, 12, 31)->startOfMonth();
                        $eligibleMonthStart = $joiningDate->year == $currentYear
                            ? Carbon::create($currentYear, $joiningDate->month, 1)->startOfMonth()
                            : $currentYearStart;
                        $monthsEligible = $eligibleMonthStart->diffInMonths($currentYearEnd) + 1;

                        $totalCreditable = 0;
                        if (strtolower($type->credit_frequency) == 'monthly') {
                            $totalCreditable = round($monthsEligible * (float) $type->credit_days, 2);
                        }

                        return [
                            'id'                    => $type->id,
                            'box_id'                => $type->box_id,
                            'leave_type'            => $type->leave_type,
                            'credit_frequency'      => $type->credit_frequency,
                            'credit_days'           => $type->credit_days,
                            'number_of_days'        => $type->number_of_days,
                            'leave_code'            => $type->leave_code,
                            'leave_color'           => $type->leave_color,
                            'leave_icon'            => $type->leave_icon,
                            'leave_credit_type'     => $type->leave_credit_type,
                            'carry_forward'         => $type->carry_forward,
                            'carry_forward_days'    => $type->carry_forward_days,
                            'status'                => $type->status,
                            'months_eligible'       => $monthsEligible,
                            'total_creditable_days' => $totalCreditable,
                        ];
                    });

                return response()->json([
                    'status'            => true,
                    'leaveTransactions' => $allTransactionsByYear, // All years data
                    'leaveTypes'        => $allLeaveTypes,
                    'availableYears'    => $availableYears,
                    'currentYear'       => now()->year, // Return current year for default expansion
                ]);
            }

            return response()->json([
                'status'            => true,
                'leaveTransactions' => $transactionsByYear,
                'leaveTypes'        => $leaveTypes,
                'availableYears'    => $availableYears, // Return all years that have data in database
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while fetching leave transactions.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function exportLeaveTransactions(Request $request)
    {
        try {
            $user = Auth::user();
            $box = Box::where('slug', $request->route('box_slug'))->first();
            if (!$box) return response()->json(['error' => 'Box not found'], 404);

            $fileName = "leave_transaction_export_{$user->id}_" . now()->format('Ymd_His') . ".csv";
            $filters = $request->only(['year', 'dateRange', 'leaveTypes', 'creditDebitFilters']);

            ExportLeaveTransactionJob::dispatch($user->id, $box->id, 'csv', $fileName, $filters);

            return response()->json(['status' => 'success', 'message' => 'Export started...', 'file_name' => $fileName]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function downloadLeaveExport(Request $request)
    {
        $file = $request->route('file');
        $path = 'exports/' . $file;
        if (!Storage::disk('public')->exists($path)) abort(404);
        return Storage::disk('public')->download($path);
    }
}
