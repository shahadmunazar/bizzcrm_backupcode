<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Services\LeaveTransactionService;

class LeaveTransactionController extends BaseController
{
    protected $leaveTransactionService;

    public function __construct(LeaveTransactionService $leaveTransactionService)
    {
        $this->leaveTransactionService = $leaveTransactionService;
    }

    public function calculate_leave_transactions_all(Request $request)
    {
        return $this->leaveTransactionService->calculate_leave_transactions_all($request);
    }

    public function calculate_team_leave_transactions_all(Request $request)
    {
        return $this->leaveTransactionService->calculate_team_leave_transactions_all($request);
    }

    public function store_leave_transaction(Request $request)
    {
        return $this->leaveTransactionService->store_leave_transaction($request);
    }

    public function Leave_dataTransaction(Request $request)
    {
        return $this->leaveTransactionService->Leave_dataTransaction($request);
    }

    public function exportLeaveTransactions(Request $request)
    {
        return $this->leaveTransactionService->exportLeaveTransactions($request);
    }

    public function downloadLeaveExport(Request $request)
    {
        return $this->leaveTransactionService->downloadLeaveExport($request);
    }
}
