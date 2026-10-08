<?php

namespace App\Services\Finance;

use App\Models\Expense;
use App\Models\ExpenseApprovalRule;
use App\Models\ExpenseApproval;
use App\Models\Box;
use Illuminate\Support\Facades\DB;

/**
 * Service class for handling Expense Approvals.
 * Manages the logic for determining which expenses require which role's approval,
 * and securely processing those approval decisions.
 */
class ExpenseApprovalService
{
    /**
     * Get all pending expenses that the current user is authorized to approve.
     *
     * @param Box $box The current active box.
     * @param array $userRoles Roles assigned to the current user.
     * @param bool $isAdmin True if user is a system admin/superadmin (can approve anything).
     * @return \Illuminate\Support\Collection List of authorized pending expenses.
     */
    public function getPendingForUser(Box $box, $userRoles, $isAdmin = false)
    {
        // Fetch all pending expenses for the current box
        $expenses = Expense::with(['boxUser.user', 'category', 'receipts'])
            ->where('box_id', $box->id)
            ->where('status', 'Pending')
            ->get();

        // Fetch active approval rules for the box
        $rules = ExpenseApprovalRule::where('box_id', $box->id)
            ->where('is_active', true)
            ->get();

        // Filter expenses that the current user is eligible to approve
        $filteredExpenses = $expenses->filter(function($expense) use ($userRoles, $rules, $isAdmin) {
            // Admins can approve any expense regardless of rules
            if ($isAdmin) return true;

            // Find the active rule that applies to this expense amount
            $rule = $rules->first(function($r) use ($expense) {
                return $expense->amount >= $r->min_amount && 
                       (is_null($r->max_amount) || $expense->amount <= $r->max_amount);
            });

            // If no rule covers this amount, no one (except admins) can approve it
            if (!$rule) return false;

            // Check if the user has at least one of the roles required by the rule
            $requiredRoles = $rule->required_roles ?? [];
            return count(array_intersect($userRoles, $requiredRoles)) > 0;
        });

        return $filteredExpenses->values();
    }

    /**
     * Process an approval or rejection for an expense.
     *
     * @param Box $box The current active box.
     * @param Expense $expense The expense model being processed.
     * @param array $data Validated input data (status, comment).
     * @param int $userId ID of the user performing the approval.
     * @param array $userRoles Roles of the user.
     * @param bool $isAdmin Is the user an admin?
     * @return Expense The updated expense record.
     * @throws \Exception If validation or authorization fails.
     */
    public function processApproval(Box $box, Expense $expense, array $data, $userId, $userRoles, $isAdmin = false)
    {
        // Prevent cross-box tampering
        if ($expense->box_id !== $box->id) {
            throw new \Exception('Invalid expense record.');
        }

        // Prevent double processing
        if ($expense->status !== 'Pending') {
            throw new \Exception('This expense has already been processed.');
        }

        // Double check eligibility securely on the backend
        $rule = ExpenseApprovalRule::where('box_id', $box->id)
            ->where('is_active', true)
            ->where('min_amount', '<=', $expense->amount)
            ->where(function($q) use ($expense) {
                $q->whereNull('max_amount')->orWhere('max_amount', '>=', $expense->amount);
            })
            ->first();

        $requiredRoles = $rule ? ($rule->required_roles ?? []) : [];
        $matchingRoles = array_intersect($userRoles, $requiredRoles);

        if (!$isAdmin && (!$rule || empty($matchingRoles))) {
            throw new \Exception('You are not authorized to process this expense amount.');
        }

        // Determine which role the user is utilizing to approve this
        $roleToSave = current($matchingRoles);
        if (!$roleToSave && $isAdmin) {
            $roleToSave = in_array('admin', $userRoles) ? 'admin' : 'superadmin';
        }

        // Execute DB updates safely within a transaction
        return DB::transaction(function() use ($expense, $data, $userId, $roleToSave) {
            // Update the main expense status
            $expense->status = $data['status'];
            $expense->save();

            // Create an audit trail record in expense_approvals table
            ExpenseApproval::create([
                'expense_id' => $expense->id,
                'approver_id' => $userId,
                'role_approved_as' => $roleToSave,
                'status' => $data['status'],
                'comment' => $data['comment'] ?? null,
            ]);

            return $expense;
        });
    }
}
