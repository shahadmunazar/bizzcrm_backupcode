<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\BoxUser;
use App\Models\ExpenseApprovalRule;
use App\Models\ExpenseApproval;
use Illuminate\Support\Facades\DB;
use App\Services\Finance\ExpenseApprovalService;
use Illuminate\Support\Facades\Auth;

class FinanceApprovalApiController extends Controller
{
    protected $approvalService;

    public function __construct(ExpenseApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
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

    public function getApprovals(Request $request)
    {
        try {
            $box = $this->getBox();
            
            $user = Auth::user();
            $userRoles = $user->getRoleNames()->toArray();
            $isAdmin = in_array('admin', $userRoles) || in_array('superadmin', $userRoles);

            $expenses = $this->approvalService->getPendingForUser($box, $userRoles, $isAdmin);

            return response()->json(['success' => true, 'status' => 200, 'data' => $expenses]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function storeApproval(Request $request, $id)
    {
        try {
            $box = $this->getBox();
            
            $expense = Expense::where('box_id', $box->id)->findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:Approved,Rejected',
                'comment' => 'required_if:status,Rejected|nullable|string|max:1000'
            ]);

            if ($expense->status !== 'Pending') {
                return response()->json(['success' => false, 'status' => 400, 'message' => 'This expense has already been processed.'], 400);
            }

            $user = Auth::user();
            $userRoles = $user->getRoleNames()->toArray();
            $isAdmin = in_array('admin', $userRoles) || in_array('superadmin', $userRoles);

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

            if (!$isAdmin && (!$rule || empty($matchingRoles))) {
                return response()->json(['success' => false, 'status' => 403, 'message' => 'You are not authorized to process this expense amount.'], 403);
            }

            DB::transaction(function() use ($expense, $validated, $user, $matchingRoles, $isAdmin) {
                $expense->status = $validated['status'];
                $expense->save();

                ExpenseApproval::create([
                    'expense_id' => $expense->id,
                    'approver_id' => $user->id,
                    'role_approved_as' => $isAdmin ? 'admin' : current($matchingRoles),
                    'status' => $validated['status'],
                    'comment' => $validated['comment'] ?? null,
                ]);
            });

            return response()->json(['success' => true, 'status' => 200, 'message' => 'Expense ' . strtolower($validated['status']) . ' successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }
}
