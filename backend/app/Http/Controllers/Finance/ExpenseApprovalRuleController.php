<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Box;
use Illuminate\Http\Request;
use App\Services\Finance\ExpenseApprovalRuleService;

/**
 * Controller for managing Expense Approval Rules.
 * These rules dictate the minimum/maximum expense amounts that certain roles can approve.
 */
class ExpenseApprovalRuleController extends Controller
{
    protected $ruleService;

    public function __construct(ExpenseApprovalRuleService $ruleService)
    {
        $this->middleware(function ($request, $next) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if (!$user) return $next($request);

            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_approval_rules', 'read'))) {
                abort(403, 'You do not have permission to access this module.');
            }

            return $next($request);
        });

        $this->ruleService = $ruleService;
    }

    /**
     * Display the Approval Rules settings page.
     *
     * @param Request $request
     * @param string $box_slug
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->firstOrFail();
        return view('finance.settings.approval_rules', compact('box'));
    }

    /**
     * API Endpoint to fetch all active approval rules for the box.
     *
     * @param Request $request
     * @param string $box_slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->firstOrFail();
        $rules = $this->ruleService->getAll($box);

        return response()->json(['data' => $rules]);
    }

    /**
     * API Endpoint to store a new approval rule.
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

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_approval_rules', 'create'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to perform this action.'], 403);
        }

        $box = Box::where('slug', $box_slug)->firstOrFail();
        
        $validated = $request->validate([
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'required_roles' => 'required|array',
        ]);

        $this->ruleService->create($box, $validated, auth()->id());

        return response()->json(['success' => true, 'message' => 'Approval Rule created successfully.']);
    }

    /**
     * API Endpoint to update an existing approval rule.
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

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_approval_rules', 'write'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to perform this action.'], 403);
        }

        $box = Box::where('slug', $box_slug)->firstOrFail();

        $validated = $request->validate([
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'required_roles' => 'required|array',
        ]);

        $this->ruleService->update($box, $id, $validated, auth()->id());

        return response()->json(['success' => true, 'message' => 'Approval Rule updated successfully.']);
    }

    /**
     * API Endpoint to permanently delete an approval rule.
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

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_approval_rules', 'delete'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to perform this action.'], 403);
        }

        $box = Box::where('slug', $box_slug)->firstOrFail();
        $this->ruleService->delete($box, $id);

        return response()->json(['success' => true, 'message' => 'Approval Rule deleted successfully.']);
    }

    /**
     * API Endpoint to toggle the active status of an approval rule.
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

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_approval_rules', 'write'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to perform this action.'], 403);
        }

        $box = Box::where('slug', $box_slug)->firstOrFail();
        $this->ruleService->toggleStatus($box, $id);

        return response()->json(['success' => true, 'message' => 'Approval Rule status updated.']);
    }
}
