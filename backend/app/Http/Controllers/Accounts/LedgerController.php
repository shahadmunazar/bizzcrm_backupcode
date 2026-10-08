<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Lead, Company, BoxUser};
use App\Models\Accounts\{Receipt, Invoice};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class LedgerController extends Controller
{
    /**
     * Get the current user's box context.
     */
    protected function getBoxContext()
    {
        return BoxUser::where('user_id', Auth::id())->firstOrFail();
    }

    /**
     * Receipts and Payments Summary (Ledger Index)
     */
    public function index(Request $request)
    {
        $boxUser = $this->getBoxContext();
        $company_id = $request->query('company_id');
        $company = null;
        if ($company_id) {
            $company = Company::where('box_id', $boxUser->box_id)->find($company_id);
        }

        if (!$request->ajax()) {
            return view('accounts.ledger.index', compact('company_id', 'company'));
        }

        // Base query for leads that have either receipts or invoices for this company
        $query = Lead::where('leads.box_id', $boxUser->box_id)
            ->where('leads.is_matured', 'Yes')
            ->select('leads.id', 'leads.name', 'leads.last_name', 'leads.company_name');


        $startDate = null;
        $endDate = null;

        // Extract date filters if provided
        if ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
        }
        if ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        }
        if ($request->filled('bookingrange')) {
            $range = explode(' - ', $request->bookingrange);
            if (count($range) === 2) {
                $startDate = Carbon::parse($range[0])->startOfDay();
                $endDate = Carbon::parse($range[1])->endOfDay();
            }
        }

        // Add sum subqueries for Receipts (Credit) and Invoices (Debit)
        // Only added once to avoid PDO/MySQL alias conflicts when using HAVING
        $query->addSelect([
            'total_receipts' => \App\Models\Accounts\Receipt::selectRaw('COALESCE(SUM(amount), 0)')
                ->whereColumn('lead_id', 'leads.id')
                ->when($company_id, fn($query, $cid) => $query->where('company_id', $cid))
                ->whereNull('deleted_at')
                ->when($startDate, fn($q) => $q->where('receipt_date', '>=', $startDate))
                ->when($endDate, fn($q) => $q->where('receipt_date', '<=', $endDate)),
            'total_invoices' => \App\Models\Accounts\Invoice::selectRaw('COALESCE(SUM(amount), 0)')
                ->whereColumn('lead_id', 'leads.id')
                ->when($company_id, fn($query, $cid) => $query->where('company_id', $cid))
                ->whereNull('deleted_at')
                ->when($startDate, fn($q) => $q->where('invoice_date', '>=', $startDate))
                ->when($endDate, fn($q) => $q->where('invoice_date', '<=', $endDate)),
        ]);

        // Only fetch leads that have a positive total in either receipts or invoices for the selected filters
        $query->havingRaw('total_receipts > 0 OR total_invoices > 0');


        if ($request->ajax() || $request->wantsJson()) {
            $leads = $query->get();
            
            $sources = $leads->map(function($row) {
                return [
                    'id' => $row->id,
                    'name' => trim($row->name . ' ' . $row->last_name),
                    'company_name' => $row->company_name ?? null,
                    'invoices_amount' => $row->total_invoices > 0 ? number_format((float)$row->total_invoices, 2, '.', '') : null,
                    'receipts_amount' => $row->total_receipts > 0 ? number_format((float)$row->total_receipts, 2, '.', '') : null,
                    'can_edit' => true,
                    'next_follow_up' => null
                ];
            });

            $total_credit = $sources->sum('receipts_amount');
            $total_debit = $sources->sum('invoices_amount');

            return response()->json([
                'errors' => false,
                'data' => [
                    'sources' => $sources,
                    'total_credit' => round($total_credit, 2),
                    'total_debit' => round($total_debit, 2)
                ]
            ]);
        }
    }

    /**
     * Account Ledger Details (Show)
     */
    public function show(Request $request, $box_slug, $lead_id)
    {
        $boxUser = $this->getBoxContext();
        $company_id = $request->query('company_id');
        $company = null;
        if ($company_id) {
            $company = Company::where('box_id', $boxUser->box_id)->find($company_id);
        }

        $lead = Lead::where('box_id', $boxUser->box_id)
            ->findOrFail($lead_id);

        if (!$request->ajax()) {
            return view('accounts.ledger.show', compact('company_id', 'company', 'lead'));
        }

        // We need to union Receipts and Invoices for a chronological ledger
        
        // 1. Receipts (Credits)
        $receipts = DB::table('receipts')
            ->select(
                'receipt_date as transaction_date',
                'payment_mode as particulars',
                DB::raw("'Receipt' as voucher_type"),
                'invoice_number as voucher_no',
                DB::raw("0.00 as debit_amount"),
                'amount as credit_amount',
                'created_at'
            )
            ->where('lead_id', $lead_id)
            ->when($company_id, function($q) use ($company_id) {
                return $q->where('company_id', $company_id);
            })
            ->whereNull('deleted_at');

        // 2. Invoices (Debits)
        $invoices = DB::table('invoices')
            ->select(
                'invoice_date as transaction_date',
                'remarks as particulars',
                DB::raw("'Invoice' as voucher_type"),
                'invoice_no as voucher_no',
                'amount as debit_amount',
                DB::raw("0.00 as credit_amount"),
                'created_at'
            )
            ->where('lead_id', $lead_id)
            ->when($company_id, function($q) use ($company_id) {
                return $q->where('company_id', $company_id);
            })
            ->whereNull('deleted_at');

        // Filter by Date Range if provided
        if ($request->filled('start_date')) {
            $receipts->whereDate('receipt_date', '>=', $request->start_date);
            $invoices->whereDate('invoice_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $receipts->whereDate('receipt_date', '<=', $request->end_date);
            $invoices->whereDate('invoice_date', '<=', $request->end_date);
        }
        if ($request->filled('bookingrange')) {
            $range = explode(' - ', $request->bookingrange);
            if (count($range) === 2) {
                $start = Carbon::parse($range[0])->startOfDay();
                $end = Carbon::parse($range[1])->endOfDay();
                $receipts->whereBetween('receipt_date', [$start, $end]);
                $invoices->whereBetween('invoice_date', [$start, $end]);
            }
        }

        $ledgerQuery = $receipts->unionAll($invoices)->orderBy('transaction_date', 'asc')->orderBy('created_at', 'asc');

        $transactions = $ledgerQuery->get();
        
        $balance = 0.00;
        $totalDebit = 0.00;
        $totalCredit = 0.00;
        foreach ($transactions as $txn) {
            $balance += $txn->debit_amount;
            $balance -= $txn->credit_amount;
            $txn->running_balance = $balance;
            
            $totalDebit += $txn->debit_amount;
            $totalCredit += $txn->credit_amount;
        }

        return DataTables::of($transactions)
            ->editColumn('transaction_date', fn($row) => Carbon::parse($row->transaction_date)->format('Y-m-d'))
            ->editColumn('debit_amount', fn($row) => $row->debit_amount > 0 ? number_format($row->debit_amount, 2) : '')
            ->editColumn('credit_amount', fn($row) => $row->credit_amount > 0 ? number_format($row->credit_amount, 2) : '')
            ->addColumn('balance', fn($row) => number_format($row->running_balance, 2))
            ->with([
                'total_debit' => number_format($totalDebit, 2),
                'total_credit' => number_format($totalCredit, 2),
                'closing_balance' => number_format($balance, 2)
            ])
            ->make(true);
    }
}
