<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Services\Finance\ExpenseService;
use App\Models\Box;

/**
 * Controller for Employee Expenses.
 * Manages the front-end facing interfaces and API endpoints for employees
 * to submit, update, and delete their own expenses.
 */
class ExpenseController extends Controller
{
    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    /**
     * Display the Expense submission and list view.
     *
     * @param Request $request
     * @param string $box_slug
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->firstOrFail();
        $categories = ExpenseCategory::where('box_id', $box->id)->where('is_active', true)->get();
        return view('finance.expenses.index', compact('box', 'categories'));
    }

    /**
     * API Endpoint to fetch the authenticated user's submitted expenses.
     *
     * @param Request $request
     * @param string $box_slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->firstOrFail();
        $expenses = $this->expenseService->getAllForUser($box, auth()->id());
            
        return response()->json(['data' => $expenses]);
    }

    /**
     * API Endpoint to submit a new expense.
     *
     * @param Request $request
     * @param string $box_slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->firstOrFail();

        $validated = $request->validate([
            'expense_date' => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'receipts.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120' // 5MB max
        ]);

        $this->expenseService->create($box, $validated, auth()->id(), $request->file('receipts'));

        return response()->json(['success' => true, 'message' => 'Expense submitted successfully.']);
    }

    /**
     * API Endpoint to update an existing expense (if it is pending/draft).
     *
     * @param Request $request
     * @param string $box_slug
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $box_slug, $id)
    {
        $box = Box::where('slug', $box_slug)->firstOrFail();

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

        try {
            $this->expenseService->update(
                $box,
                $id,
                $validated,
                auth()->id(),
                $request->file('receipts'),
                $request->input('remove_receipts')
            );
            return response()->json(['success' => true, 'message' => 'Expense updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        }
    }

    /**
     * API Endpoint to permanently delete an expense.
     *
     * @param Request $request
     * @param string $box_slug
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $box_slug, $id)
    {
        $box = Box::where('slug', $box_slug)->firstOrFail();

        try {
            $this->expenseService->delete($box, $id, auth()->id());
            return response()->json(['success' => true, 'message' => 'Expense deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        }
    }
}
