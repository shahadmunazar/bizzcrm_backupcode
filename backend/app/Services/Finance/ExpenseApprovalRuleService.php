<?php

namespace App\Services\Finance;

use App\Models\ExpenseApprovalRule;
use App\Models\Box;

/**
 * Service class for managing Expense Approval Rules.
 * Rules dictate which roles can approve expenses based on min/max amounts.
 */
class ExpenseApprovalRuleService
{
    /**
     * Retrieve all approval rules for a specific box.
     *
     * @param Box $box The current active box.
     * @return \Illuminate\Support\Collection Transformed collection of rules.
     */
    public function getAll(Box $box)
    {
        $rules = ExpenseApprovalRule::with(['creator', 'updater'])->where('box_id', $box->id)->get();
        
        // Decode JSON roles into a comma-separated string for easy frontend display
        $rules->transform(function ($rule) {
            $rule->roles_list = is_array($rule->required_roles) ? implode(', ', $rule->required_roles) : '';
            return $rule;
        });

        return $rules;
    }

    /**
     * Create a new expense approval rule.
     *
     * @param Box $box The current active box.
     * @param array $data Validated rule data (min_amount, max_amount, required_roles).
     * @param int $userId ID of the user creating the rule.
     * @return ExpenseApprovalRule
     */
    public function create(Box $box, array $data, $userId)
    {
        $data['box_id'] = $box->id;
        $data['is_active'] = true;
        $data['created_by'] = $userId;

        return ExpenseApprovalRule::create($data);
    }

    /**
     * Update an existing expense approval rule.
     *
     * @param Box $box The current active box.
     * @param int $id ID of the rule.
     * @param array $data Validated rule data.
     * @param int $userId ID of the user updating the rule.
     * @return ExpenseApprovalRule
     */
    public function update(Box $box, $id, array $data, $userId)
    {
        $rule = ExpenseApprovalRule::where('box_id', $box->id)->findOrFail($id);
        $data['updated_by'] = $userId;
        $rule->update($data);
        return $rule;
    }

    /**
     * Permanently delete an expense approval rule.
     *
     * @param Box $box The current active box.
     * @param int $id ID of the rule.
     * @return bool
     */
    public function delete(Box $box, $id)
    {
        $rule = ExpenseApprovalRule::where('box_id', $box->id)->findOrFail($id);
        return $rule->delete();
    }

    /**
     * Toggle the active status of an expense approval rule.
     *
     * @param Box $box The current active box.
     * @param int $id ID of the rule.
     * @return bool
     */
    public function toggleStatus(Box $box, $id)
    {
        $rule = ExpenseApprovalRule::where('box_id', $box->id)->findOrFail($id);
        $rule->is_active = !$rule->is_active;
        return $rule->save();
    }
}
