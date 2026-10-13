<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Box;
use App\Models\ExpenseApprovalRule;
use App\Models\ExpenseApproval;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\Finance\ExpenseApprovalService;

/**
 * Controller for Expense Approvals.
 * Manages the endpoints for managers/admins to view pending expenses 
 * and approve or reject them based on authorization rules.
 */
class ExpenseApprovalController extends Controller
{
    protected $approvalService;

    public function __construct(ExpenseApprovalService $approvalService)
    {
        $this->middleware(function ($request, $next) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if (!$user) return $next($request);

            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_approvals', 'read'))) {
                abort(403, 'You do not have permission to access this module.');
            }

            return $next($request);
        });

        $this->approvalService = $approvalService;
    }

    /**
     * Display the Expense Approvals list page.
     *
     * @param Request $request
     * @param string $box_slug
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->firstOrFail();
        return view('finance.approvals.index', compact('box'));
    }

    /**
     * API Endpoint to fetch pending expenses the current user is authorized to approve.
     *
     * @param Request $request
     * @param string $box_slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->firstOrFail();
        
        $user = auth()->user();
        $userRoles = $user->getRoleNames()->toArray();
        $isAdmin = in_array('admin', $userRoles) || in_array('superadmin', $userRoles);

        $expenses = $this->approvalService->getPendingForUser($box, $userRoles, $isAdmin);

        return response()->json(['data' => $expenses]);
    }

    /**
     * API Endpoint to process (Approve or Reject) an expense.
     *
     * @param Request $request
     * @param string $box_slug
     * @param Expense $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $box_slug, Expense $expense)
    {
        $user = auth()->user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_approvals', 'approve'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to approve expenses.'], 403);
        }

        $box = Box::where('slug', $box_slug)->firstOrFail();

        $validated = $request->validate([
            'status' => 'required|in:Approved,Rejected',
            'comment' => 'required_if:status,Rejected|nullable|string|max:1000'
        ]);

        if ($expense->status !== 'Pending') {
            return response()->json(['success' => false, 'message' => 'This expense has already been processed.'], 400);
        }

        $user = auth()->user();
        $userRoles = $user->getRoleNames()->toArray();

        // Double check eligibility
        $rule = ExpenseApprovalRule::where('box_id', $box->id)
            ->where('is_active', true)
            ->where('min_amount', '<=', $expense->amount)
            ->where(function($q) use ($expense) {
                $q->whereNull('max_amount')->orWhere('max_amount', '>=', $expense->amount);
            })
            ->first();

        $requiredRoles = $rule ? ($rule->required_roles ?? []) : [];
        $matchingRoles = array_intersect($userRoles, $requiredRoles);

        if (!$isGlobalAdmin && !$isBoxAdmin) {
            if (!$rule || empty($matchingRoles)) {
                return response()->json(['success' => false, 'message' => 'You are not authorized to process this expense amount.'], 403);
            }
        }

        $roleApprovedAs = ($isGlobalAdmin || $isBoxAdmin) ? 'admin' : current($matchingRoles);

        DB::transaction(function() use ($expense, $validated, $user, $roleApprovedAs) {
            $expense->status = $validated['status'];
            $expense->save();

            ExpenseApproval::create([
                'expense_id' => $expense->id,
                'approver_id' => $user->id,
                'role_approved_as' => $roleApprovedAs,
                'status' => $validated['status'],
                'comment' => $validated['comment'] ?? null,
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Expense ' . strtolower($validated['status']) . ' successfully.']);
    }
}
