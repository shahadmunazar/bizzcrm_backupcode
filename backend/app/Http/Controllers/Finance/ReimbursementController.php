<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Box;
use App\Services\Finance\ReimbursementService;

/**
 * Controller for Expense Reimbursements.
 * Provides endpoints for finance teams to view approved expenses
 * and process their final payment/reimbursement.
 */
class ReimbursementController extends Controller
{
    protected $reimbursementService;

    public function __construct(ReimbursementService $reimbursementService)
    {
        $this->middleware(function ($request, $next) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if (!$user) return $next($request);

            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_reimbursements', 'read'))) {
                abort(403, 'You do not have permission to access this module.');
            }

            return $next($request);
        });

        $this->reimbursementService = $reimbursementService;
    }

    /**
     * Display the Expense Reimbursements list view.
     *
     * @param Request $request
     * @param string $box_slug
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->firstOrFail();
        return view('finance.reimbursements.index', compact('box'));
    }

    /**
     * API Endpoint to fetch expenses by status (e.g. Approved vs Paid).
     *
     * @param Request $request
     * @param string $box_slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->firstOrFail();
        $status = $request->input('status', 'Approved');
        
        $expenses = $this->reimbursementService->getExpensesByStatus($box, $status);
        return response()->json(['data' => $expenses]);
    }

    /**
     * API Endpoint to process a payment (reimbursement) for an approved expense.
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

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_reimbursements', 'process'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to process payments.'], 403);
        }

        $box = Box::where('slug', $box_slug)->firstOrFail();

        $validated = $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'payment_reference' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
        ]);

        try {
            $this->reimbursementService->processPayment($box, $validated, auth()->id());
            return response()->json(['success' => true, 'message' => 'Payment processed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
