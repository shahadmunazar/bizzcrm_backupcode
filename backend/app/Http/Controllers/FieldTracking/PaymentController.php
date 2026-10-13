<?php

namespace App\Http\Controllers\FieldTracking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function paymentAdmin() { return view('fieldstracking.payment-admin'); }
    public function paymentGateways() { return view('fieldstracking.payment-gateways'); }
    public function payment() { return view('fieldstracking.payment'); }
    public function payments(Request $request, $box_slug) { 
        $box = \App\Models\Box::where('slug', $box_slug)->firstOrFail();

        $totalPaid = \App\Models\Expense::where('box_id', $box->id)->where('status', 'Paid')->sum('amount');
        $totalApproved = \App\Models\Expense::where('box_id', $box->id)->where('status', 'Approved')->sum('amount');
        $totalRejected = \App\Models\Expense::where('box_id', $box->id)->where('status', 'Rejected')->sum('amount');
        $totalPending = \App\Models\Expense::where('box_id', $box->id)->where('status', 'Pending')->sum('amount');

        return view('fieldstracking.payments', compact('box', 'totalPaid', 'totalApproved', 'totalRejected', 'totalPending')); 
    }

    public function getPaymentsData(Request $request, $box_slug) {
        $box = \App\Models\Box::where('slug', $box_slug)->firstOrFail();
        
        $payments = \App\Models\Expense::where('box_id', $box->id)
                        ->with(['boxUser.user', 'category', 'approvals.approver', 'payment.payer']);
                        
        return \Yajra\DataTables\Facades\DataTables::of($payments)
            ->addColumn('employee', function ($row) {
                if ($row->boxUser && $row->boxUser->user) {
                    $name = trim($row->boxUser->user->name);
                    return $name ?: 'N/A';
                }
                return 'N/A';
            })
            ->addColumn('category', function ($row) {
                return $row->category ? $row->category->name : 'N/A';
            })
            ->addColumn('date', function ($row) {
                return \Carbon\Carbon::parse($row->expense_date)->format('d/m/Y');
            })
            ->addColumn('amount', function ($row) {
                return '₹' . number_format($row->amount, 2);
            })
            ->addColumn('approved_by', function ($row) {
                if ($row->approvals->count() > 0) {
                    $latestApproval = $row->approvals->last();
                    if ($latestApproval->approver) {
                        $name = trim($latestApproval->approver->name);
                        return $name ?: 'N/A';
                    }
                }
                return 'N/A';
            })
            ->addColumn('processed_by', function ($row) {
                if ($row->payment && $row->payment->payer) {
                    $name = trim($row->payment->payer->name);
                    return $name ?: 'N/A';
                }
                return 'N/A';
            })
            ->addColumn('payment_details', function ($row) {
                if ($row->payment) {
                    $ref = $row->payment->payment_reference ?? 'N/A';
                    $date = $row->payment->payment_date ? \Carbon\Carbon::parse($row->payment->payment_date)->format('d/m/Y') : 'N/A';
                    return "Ref: $ref <br> Date: $date";
                }
                return 'N/A';
            })
            ->addColumn('status', function ($row) {
                $class = 'badge-secondary-transparent';
                if ($row->status == 'Paid') $class = 'badge-success-transparent';
                elseif ($row->status == 'Approved') $class = 'badge-info-transparent';
                elseif ($row->status == 'Rejected') $class = 'badge-danger-transparent';
                elseif ($row->status == 'Pending') $class = 'badge-warning-transparent';
                return '<span class="badge ' . $class . '">' . $row->status . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="dropdown table-action text-end">
                        <a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="javascript:void(0);">
                                <i class="ti ti-eye text-info"></i>View
                            </a>
                        </div>
                    </div>
                ';
            })
            ->rawColumns(['status', 'action', 'payment_details'])
            ->make(true);
    }
}
