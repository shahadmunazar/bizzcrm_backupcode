<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BoxUser;
use App\Services\Finance\ReimbursementService;
use Illuminate\Support\Facades\Auth;

class FinanceReimbursementApiController extends Controller
{
    protected $reimbursementService;

    public function __construct(ReimbursementService $reimbursementService)
    {
        $this->reimbursementService = $reimbursementService;
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

    public function getReimbursements(Request $request)
    {
        try {
            $box = $this->getBox();
            $status = $request->input('status', 'Approved'); // Can be Approved or Paid
            
            $expenses = $this->reimbursementService->getExpensesByStatus($box, $status);
            
            return response()->json(['success' => true, 'status' => 200, 'data' => $expenses]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function storeReimbursement(Request $request)
    {
        try {
            $box = $this->getBox();

            $validated = $request->validate([
                'expense_id' => 'required|exists:expenses,id',
                'payment_reference' => 'nullable|string|max:255',
                'payment_date' => 'required|date',
            ]);

            $this->reimbursementService->processPayment($box, $validated, Auth::id());
            
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Payment processed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 400, 'message' => $e->getMessage()]);
        }
    }
}
