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
        $this->middleware(function ($request, $next) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if (!$user) return $next($request);

            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_expenses', 'read'))) {
                abort(403, 'You do not have permission to access this module.');
            }

            return $next($request);
        });

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
        $user = auth()->user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_expenses', 'apply'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to apply for expenses.'], 403);
        }

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
        $user = \Illuminate\Support\Facades\Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_expenses', 'write'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to perform this action.'], 403);
        }

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
        $user = \Illuminate\Support\Facades\Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_expenses', 'delete'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to perform this action.'], 403);
        }

        $box = Box::where('slug', $box_slug)->firstOrFail();

        try {
            $this->expenseService->delete($box, $id, auth()->id());
            return response()->json(['success' => true, 'message' => 'Expense deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        }
    }
}
