<?php

namespace App\Services\Finance;

use App\Models\ExpenseCategory;
use App\Models\Box;

/**
 * Service class for managing Expense Categories.
 * Provides basic CRUD operations for expense categorization.
 */
class ExpenseCategoryService
{
    /**
     * Retrieve all expense categories for a specific box.
     *
     * @param Box $box The current active box.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(Box $box)
    {
        return ExpenseCategory::with(['creator', 'updater'])->where('box_id', $box->id)->get();
    }

    /**
     * Create a new expense category.
     *
     * @param Box $box The current active box.
     * @param array $data Validated category data (name).
     * @param int $userId ID of the user creating the category.
     * @return ExpenseCategory
     */
    public function create(Box $box, array $data, $userId)
    {
        $data['box_id'] = $box->id;
        $data['created_by'] = $userId;
        $data['is_active'] = true;

        return ExpenseCategory::create($data);
    }

    /**
     * Update an existing expense category.
     *
     * @param Box $box The current active box.
     * @param int $id Category ID.
     * @param array $data Validated category data.
     * @param int $userId ID of the user updating the category.
     * @return ExpenseCategory
     */
    public function update(Box $box, $id, array $data, $userId)
    {
        $category = ExpenseCategory::where('box_id', $box->id)->findOrFail($id);
        $data['updated_by'] = $userId;
        $category->update($data);
        return $category;
    }

    /**
     * Permanently delete an expense category.
     *
     * @param Box $box The current active box.
     * @param int $id Category ID.
     * @return bool
     */
    public function delete(Box $box, $id)
    {
        $category = ExpenseCategory::where('box_id', $box->id)->findOrFail($id);
        return $category->delete();
    }

    /**
     * Toggle the active status of an expense category.
     *
     * @param Box $box The current active box.
     * @param int $id Category ID.
     * @return bool
     */
    public function toggleStatus(Box $box, $id)
    {
        $category = ExpenseCategory::where('box_id', $box->id)->findOrFail($id);
        $category->is_active = !$category->is_active;
        return $category->save();
    }
}
