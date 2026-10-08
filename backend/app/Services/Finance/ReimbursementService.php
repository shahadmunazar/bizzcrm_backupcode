<?php

namespace App\Services\Finance;

use App\Models\Expense;
use App\Models\ExpensePayment;
use App\Models\Box;
use Illuminate\Support\Facades\DB;

/**
 * Service class for managing Expense Reimbursements.
 * Handles the final step in the expense flow: issuing payments to employees.
 */
class ReimbursementService
{
    /**
     * Retrieve a list of expenses filtered by status (default is 'Approved').
     *
     * @param Box $box The current active box.
     * @param string $status The status to filter by ('Approved', 'Paid', etc).
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getExpensesByStatus(Box $box, $status = 'Approved')
    {
        return Expense::with(['boxUser.user', 'payment.payer', 'approvals.approver'])
            ->where('box_id', $box->id)
            ->where('status', $status)
            ->get();
    }

    /**
     * Process a reimbursement payment for an approved expense.
     * Updates the expense status to 'Paid' and creates a payment record.
     *
     * @param Box $box The current active box.
     * @param array $data Validated payment data (expense_id, payment_date, reference).
     * @param int $userId ID of the user issuing the payment.
     * @return ExpensePayment
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function processPayment(Box $box, array $data, $userId)
    {
        $expense = Expense::where('box_id', $box->id)
            ->where('id', $data['expense_id'])
            ->where('status', 'Approved')
            ->firstOrFail();

        return DB::transaction(function() use ($expense, $data, $userId) {
            // Update the main expense record
            $expense->status = 'Paid';
            $expense->save();

            // Create the permanent payment record
            $payment = ExpensePayment::create([
                'expense_id' => $expense->id,
                'payer_id' => $userId,
                'payment_reference' => $data['payment_reference'] ?? null,
                'payment_date' => $data['payment_date'],
                'amount' => $expense->amount,
            ]);

            return $payment;
        });
    }
}
