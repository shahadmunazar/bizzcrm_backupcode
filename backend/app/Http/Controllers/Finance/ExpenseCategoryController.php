<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Box;
use Illuminate\Http\Request;
use App\Services\Finance\ExpenseCategoryService;

/**
 * Controller for managing Expense Categories.
 * Provides endpoints for creating, editing, and listing the categories 
 * that users can assign to their expenses.
 */
class ExpenseCategoryController extends Controller
{
    protected $categoryService;

    public function __construct(ExpenseCategoryService $categoryService)
    {
        $this->middleware(function ($request, $next) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if (!$user) return $next($request);

            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_expense_categories', 'read'))) {
                abort(403, 'You do not have permission to access this module.');
            }

            return $next($request);
        });

        $this->categoryService = $categoryService;
    }

    /**
     * Display the Expense Categories settings page.
     *
     * @param Request $request
     * @param string $box_slug
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->firstOrFail();
        return view('finance.settings.expense_categories', compact('box'));
    }

    /**
     * API Endpoint to fetch all categories for the active box.
     *
     * @param Request $request
     * @param string $box_slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->firstOrFail();
        $categories = $this->categoryService->getAll($box);

        return response()->json(['data' => $categories]);
    }

    /**
     * API Endpoint to store a new expense category.
     *
     * @param Request $request
     * @param string $box_slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $box_slug)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_expense_categories', 'create'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to perform this action.'], 403);
        }

        $box = Box::where('slug', $box_slug)->firstOrFail();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $this->categoryService->create($box, $validated, auth()->id());

        return response()->json(['success' => true, 'message' => 'Expense Category created successfully.']);
    }

    /**
     * API Endpoint to update an existing expense category.
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

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_expense_categories', 'write'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to perform this action.'], 403);
        }

        $box = Box::where('slug', $box_slug)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $this->categoryService->update($box, $id, $validated, auth()->id());

        return response()->json(['success' => true, 'message' => 'Expense Category updated successfully.']);
    }

    /**
     * API Endpoint to permanently delete an expense category.
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

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_expense_categories', 'delete'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to perform this action.'], 403);
        }

        $box = Box::where('slug', $box_slug)->firstOrFail();
        $this->categoryService->delete($box, $id);

        return response()->json(['success' => true, 'message' => 'Expense Category deleted successfully.']);
    }

    /**
     * API Endpoint to toggle the active status of a category.
     *
     * @param Request $request
     * @param string $box_slug
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Request $request, $box_slug, $id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_expense_categories', 'write'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to perform this action.'], 403);
        }

        $box = Box::where('slug', $box_slug)->firstOrFail();
        $this->categoryService->toggleStatus($box, $id);

        return response()->json(['success' => true, 'message' => 'Expense Category status updated.']);
    }
}
