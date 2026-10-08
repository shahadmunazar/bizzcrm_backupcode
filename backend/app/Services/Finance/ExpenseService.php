<?php

namespace App\Services\Finance;

use App\Models\Expense;
use App\Models\Box;
use App\Models\BoxUser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

/**
 * Service class for handling Employee Expenses.
 * Manages expense submission, editing, deletion, and receipt file uploads.
 */
class ExpenseService
{
    /**
     * Retrieve all expenses submitted by a specific user in a box.
     *
     * @param Box $box The current active box.
     * @param int $userId The ID of the authenticated user.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllForUser(Box $box, $userId)
    {
        $boxuser = BoxUser::where('user_id', $userId)
            ->where('box_id', $box->id)
            ->firstOrFail();

        return Expense::with(['category', 'receipts'])
            ->where('box_id', $box->id)
            ->where('boxuser_id', $boxuser->id)
            ->orderBy('expense_date', 'desc')
            ->get();
    }

    /**
     * Create a new expense and upload any provided receipts.
     *
     * @param Box $box The current active box.
     * @param array $data Validated expense data.
     * @param int $userId The ID of the user submitting the expense.
     * @param array|null $receipts Array of UploadedFile instances for receipts.
     * @return Expense
     */
    public function create(Box $box, array $data, $userId, $receipts = null)
    {
        $boxuser = BoxUser::where('user_id', $userId)
            ->where('box_id', $box->id)
            ->firstOrFail();

        $data['box_id'] = $box->id;
        $data['boxuser_id'] = $boxuser->id;
        $data['status'] = 'Pending';

        $expense = Expense::create($data);

        // Process any uploaded receipt files
        $this->handleReceiptUploads($expense, $receipts);

        return $expense;
    }

    /**
     * Update an existing expense, including removing/adding receipts.
     * Can only be performed if the expense is still in Draft or Pending state.
     *
     * @param Box $box The current active box.
     * @param int $id Expense ID.
     * @param array $data Validated expense data.
     * @param int $userId The ID of the user updating the expense.
     * @param array|null $receipts Array of new UploadedFile instances.
     * @param array|null $removeReceiptIds IDs of existing receipts to delete.
     * @return Expense
     * @throws \Exception If the expense is already approved/paid.
     */
    public function update(Box $box, $id, array $data, $userId, $receipts = null, $removeReceiptIds = null)
    {
        $boxuser = BoxUser::where('user_id', $userId)
            ->where('box_id', $box->id)
            ->firstOrFail();

        $expense = Expense::where('box_id', $box->id)
            ->where('boxuser_id', $boxuser->id)
            ->findOrFail($id);

        if (!in_array($expense->status, ['Draft', 'Pending'])) {
            throw new \Exception('You cannot edit an expense that is already ' . $expense->status);
        }

        $expense->update($data);

        // Handle deletion of existing receipts requested by the user
        if (!empty($removeReceiptIds)) {
            $receiptsToDelete = $expense->receipts()->whereIn('id', $removeReceiptIds)->get();
            foreach ($receiptsToDelete as $receipt) {
                Storage::disk('public')->delete($receipt->file_path);
                $receipt->delete();
            }
        }

        // Handle uploading new receipts
        $this->handleReceiptUploads($expense, $receipts);

        return $expense;
    }

    /**
     * Permanently delete an expense and its associated files.
     *
     * @param Box $box The current active box.
     * @param int $id Expense ID.
     * @param int $userId The ID of the user deleting the expense.
     * @return bool
     * @throws \Exception If the expense is already approved/paid.
     */
    public function delete(Box $box, $id, $userId)
    {
        $boxuser = BoxUser::where('user_id', $userId)
            ->where('box_id', $box->id)
            ->firstOrFail();

        $expense = Expense::where('box_id', $box->id)
            ->where('boxuser_id', $boxuser->id)
            ->findOrFail($id);

        if (!in_array($expense->status, ['Draft', 'Pending'])) {
            throw new \Exception('You cannot delete an expense that is already ' . $expense->status);
        }

        // Receipts and files should ideally be handled via Eloquent cascade or manual deletion here
        // If DB has onDelete('cascade') for receipts, we only need to delete files from storage.
        // For absolute safety, let's delete the physical files:
        foreach ($expense->receipts as $receipt) {
            Storage::disk('public')->delete($receipt->file_path);
        }

        return $expense->delete();
    }

    /**
     * Helper method to store uploaded files and create receipt records.
     *
     * @param Expense $expense The parent expense model.
     * @param array|null $receipts Array of uploaded files.
     * @return void
     */
    protected function handleReceiptUploads(Expense $expense, $receipts)
    {
        if (!empty($receipts)) {
            foreach ($receipts as $file) {
                if ($file instanceof UploadedFile) {
                    $path = $file->store('expenses/receipts', 'public');
                    $expense->receipts()->create([
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientMimeType(),
                    ]);
                }
            }
        }
    }
}
