<?php

namespace App\Services\Finance;

use App\Models\Expense;
use App\Models\Box;
use App\Models\EmployeeAllowance;

/**
 * Service class for handling Finance Dashboard data.
 * This class abstracts the logic for generating metrics and summaries 
 * displayed on the main finance dashboard.
 */
class FinanceDashboardService
{
    /**
     * Get aggregate data (totals) for the finance dashboard.
     * Calculates the total amount for both 'Pending' and 'Approved' expenses.
     *
     * @param Box $box The current active box/workspace.
     * @param string $startDate 
     * @param string $endDate
     * @return array An array containing 'pending_amount' and 'approved_amount'.
     */
    public function getDashboardData(Box $box, string $startDate, string $endDate): array
    {
        // Add time to dates to cover full day
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';
        
        // Optimize by fetching all sums in a single database query using conditional aggregation.
        $totals = Expense::where('box_id', $box->id)
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->selectRaw("
                COALESCE(SUM(CASE WHEN status = 'Pending' THEN amount ELSE 0 END), 0) as pending_amount,
                COALESCE(SUM(CASE WHEN status = 'Approved' THEN amount ELSE 0 END), 0) as approved_amount,
                COALESCE(SUM(CASE WHEN status = 'Paid' THEN amount ELSE 0 END), 0) as paid_amount,
                COALESCE(SUM(CASE WHEN status = 'Rejected' THEN amount ELSE 0 END), 0) as rejected_amount
            ")->first();
            
        // Fetch recent expenses for the activity feed
        $recentExpenses = Expense::with(['boxUser.user', 'category'])
            ->where('box_id', $box->id)
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Fetch category wise expenses for chart
        $categoryExpenses = Expense::where('expenses.box_id', $box->id)
            ->whereBetween('expenses.created_at', [$startDateTime, $endDateTime])
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->selectRaw('expense_categories.name as category_name, SUM(expenses.amount) as total_amount')
            ->groupBy('expense_categories.name')
            ->get();
            
        // Expense Trend (Count per day)
        $expenseTrend = Expense::where('box_id', $box->id)
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->selectRaw('DATE(created_at) as date, COUNT(id) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
            
        // Allowance Totals
        $allowanceTotals = EmployeeAllowance::where('box_id', $box->id)
            ->whereBetween('business_date', [$startDate, $endDate])
            ->selectRaw("
                COALESCE(SUM(CASE WHEN status = 'Approved' THEN amount ELSE 0 END), 0) as approved_allowance,
                COALESCE(SUM(CASE WHEN status = 'Pending' THEN amount ELSE 0 END), 0) as pending_allowance
            ")->first();

        return [
            'pending_amount' => (float) $totals->pending_amount,
            'approved_amount' => (float) $totals->approved_amount,
            'paid_amount' => (float) $totals->paid_amount,
            'rejected_amount' => (float) $totals->rejected_amount,
            'recent_expenses' => $recentExpenses,
            'category_expenses' => $categoryExpenses,
            'expense_trend' => $expenseTrend,
            'allowances' => [
                'approved' => (float) ($allowanceTotals->approved_allowance ?? 0),
                'pending' => (float) ($allowanceTotals->pending_allowance ?? 0)
            ]
        ];
    }
}
