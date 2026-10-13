<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Services\Finance\ExpenseService;
use App\Models\BoxUser;
use Illuminate\Support\Facades\Auth;

class FinanceExpenseApiController extends Controller
{
    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    private function getBox()
    {
        $user = Auth::id();
        $boxUser = BoxUser::with('box')->where('user_id', $user)->first();
        if (!$boxUser || !$boxUser->box) {
            abort(404, 'Box user or box not found.');
        }
        return $boxUser->box;
    }

    public function getExpenses(Request $request)
    {
        try {
            $box = $this->getBox();
            $expenses = $this->expenseService->getAllForUser($box, Auth::id());
            return response()->json(['success' => true, 'status' => 200, 'data' => $expenses]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function storeExpense(Request $request)
    {
        try {
            $box = $this->getBox();

            $validated = $request->validate([
                'expense_date' => 'required|date',
                'expense_category_id' => 'required|exists:expense_categories,id',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'nullable|string',
                'description' => 'nullable|string',
                'location' => 'nullable|string',
                'receipts.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120' // 5MB max
            ]);

            $this->expenseService->create($box, $validated, Auth::id(), $request->file('receipts'));

            return response()->json(['success' => true, 'status' => 200, 'message' => 'Expense submitted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function updateExpense(Request $request, $id)
    {
        try {
            $box = $this->getBox();

            $validated = $request->validate([
                'expense_date' => 'required|date',
                'expense_category_id' => 'required|exists:expense_categories,id',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'nullable|string',
                'description' => 'nullable|string',
                'location' => 'nullable|string',
                'receipts.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'remove_receipts' => 'nullable|array',
                'remove_receipts.*' => 'exists:expense_receipts,id'
            ]);

            $this->expenseService->update(
                $box,
                $id,
                $validated,
                Auth::id(),
                $request->file('receipts'),
                $request->input('remove_receipts')
            );
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Expense updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 403, 'message' => $e->getMessage()]);
        }
    }

    public function deleteExpense($id)
    {
        try {
            $box = $this->getBox();
            $this->expenseService->delete($box, $id, Auth::id());
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Expense deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 403, 'message' => $e->getMessage()]);
        }
    }
}
