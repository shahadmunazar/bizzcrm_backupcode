<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Finance\AllowanceTypeService;
use App\Services\Finance\ExpenseCategoryService;
use App\Services\Finance\ExpenseApprovalRuleService;
use App\Models\BoxUser;
use Illuminate\Support\Facades\Auth;

class FinanceSettingsApiController extends Controller
{
    protected $allowanceTypeService;
    protected $expenseCategoryService;
    protected $expenseApprovalRuleService;

    public function __construct(
        AllowanceTypeService $allowanceTypeService,
        ExpenseCategoryService $expenseCategoryService,
        ExpenseApprovalRuleService $expenseApprovalRuleService
    ) {
        $this->allowanceTypeService = $allowanceTypeService;
        $this->expenseCategoryService = $expenseCategoryService;
        $this->expenseApprovalRuleService = $expenseApprovalRuleService;
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

    // ==========================================
    // ALLOWANCE TYPES
    // ==========================================

    public function getAllowanceTypes()
    {
        try {
            $box = $this->getBox();
            $data = $this->allowanceTypeService->getAll($box);
            return response()->json(['success' => true, 'status' => 200, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function storeAllowanceType(Request $request)
    {
        try {
            $box = $this->getBox();
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
            ]);
            $this->allowanceTypeService->create($box, $validated, Auth::id());
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Allowance Type created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function updateAllowanceType(Request $request, $id)
    {
        try {
            $box = $this->getBox();
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
            ]);
            $this->allowanceTypeService->update($box, $id, $validated, Auth::id());
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Allowance Type updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function deleteAllowanceType($id)
    {
        try {
            $box = $this->getBox();
            $this->allowanceTypeService->delete($box, $id);
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Allowance Type deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function toggleAllowanceTypeStatus($id)
    {
        try {
            $box = $this->getBox();
            $this->allowanceTypeService->toggleStatus($box, $id);
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Allowance Type status updated.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    // ==========================================
    // EXPENSE CATEGORIES
    // ==========================================

    public function getExpenseCategories()
    {
        try {
            $box = $this->getBox();
            $data = $this->expenseCategoryService->getAll($box);
            return response()->json(['success' => true, 'status' => 200, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function storeExpenseCategory(Request $request)
    {
        try {
            $box = $this->getBox();
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);
            $this->expenseCategoryService->create($box, $validated, Auth::id());
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Expense Category created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function updateExpenseCategory(Request $request, $id)
    {
        try {
            $box = $this->getBox();
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);
            $this->expenseCategoryService->update($box, $id, $validated, Auth::id());
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Expense Category updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function deleteExpenseCategory($id)
    {
        try {
            $box = $this->getBox();
            $this->expenseCategoryService->delete($box, $id);
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Expense Category deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function toggleExpenseCategoryStatus($id)
    {
        try {
            $box = $this->getBox();
            $this->expenseCategoryService->toggleStatus($box, $id);
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Expense Category status updated.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    // ==========================================
    // EXPENSE APPROVAL RULES
    // ==========================================

    public function getApprovalRules()
    {
        try {
            $box = $this->getBox();
            $data = $this->expenseApprovalRuleService->getAll($box);
            return response()->json(['success' => true, 'status' => 200, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function storeApprovalRule(Request $request)
    {
        try {
            $box = $this->getBox();
            $validated = $request->validate([
                'min_amount' => 'required|numeric|min:0',
                'max_amount' => 'nullable|numeric|min:0',
                'required_roles' => 'required|array',
            ]);
            $this->expenseApprovalRuleService->create($box, $validated, Auth::id());
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Approval Rule created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function updateApprovalRule(Request $request, $id)
    {
        try {
            $box = $this->getBox();
            $validated = $request->validate([
                'min_amount' => 'required|numeric|min:0',
                'max_amount' => 'nullable|numeric|min:0',
                'required_roles' => 'required|array',
            ]);
            $this->expenseApprovalRuleService->update($box, $id, $validated, Auth::id());
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Approval Rule updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function deleteApprovalRule($id)
    {
        try {
            $box = $this->getBox();
            $this->expenseApprovalRuleService->delete($box, $id);
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Approval Rule deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function toggleApprovalRuleStatus($id)
    {
        try {
            $box = $this->getBox();
            $this->expenseApprovalRuleService->toggleStatus($box, $id);
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Approval Rule status updated.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }
}
