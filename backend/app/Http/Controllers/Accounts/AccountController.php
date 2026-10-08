<?php
namespace App\Http\Controllers\Accounts;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Lead, Company, Bank, Division, BoxUser, PaymentMode};
use App\Models\Accounts\Receipt;
use Carbon\Carbon;
use App\Jobs\ExportReceiptsJob;
use Illuminate\Support\Facades\{Auth, Storage};
use Yajra\DataTables\Facades\DataTables;



class AccountController extends Controller
{
    /**
     * Get the current user's box context.
     */
    protected function getBoxContext()
    {
        return BoxUser::where('user_id', Auth::id())->firstOrFail();
    }

    /**
     * Common validation rules for Receipts.
     */
    protected function getValidationRules()
    {
        return [
            'lead_id' => 'nullable|exists:leads,id',
            'company_id' => 'required|exists:companies,id',
            'receipt_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payment_mode' => 'required|string|max:100',
            'bank_id' => 'nullable|exists:banks,id',
            'division_id' => 'nullable|exists:divisions,id',
            'reference' => 'nullable|string|max:255',
        ];
    }

    /**
     * Display a listing of receipts or return DataTables data.
     */
    public function receipts(Request $request)
    {
        if (!$request->ajax()) {
            $boxUser = $this->getBoxContext();
            $company_id = $request->query('company_id');
            $company = null;
            if ($company_id) {
                $company = \App\Models\Company::where('box_id', $boxUser->box_id)->find($company_id);
            }
            return view('accounts.receipts', compact('company_id', 'company'));
        }

        $boxUser = $this->getBoxContext();
        $query = Receipt::where('box_id', $boxUser->box_id)
            ->with(['company:id,bill_name', 'lead:id,name,last_name', 'bank:id,name']);

        $this->applyFilters($query, $request);

        $totalAmount = (clone $query)->sum('amount');

        return DataTables::of($query->latest('id'))
            ->addIndexColumn()
            ->addColumn('action', fn($row) => $this->getActionButtons($row))
            ->editColumn('receipt_date', fn($row) => $row->receipt_date ? $row->receipt_date->format('d M Y') : '-')
            ->addColumn('company_name', fn($row) => $row->company?->bill_name ?? '-')
            ->addColumn('bank_name', fn($row) => $row->bank?->name ?? '-')
            ->rawColumns(['action'])
            ->with('total_amount', $totalAmount)
            ->make(true);
    }

    /**
     * Apply search filters to the query.
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('start_date')) {
            $query->whereDate('receipt_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('receipt_date', '<=', $request->end_date);
        }
        if ($request->filled('bookingrange')) {
            $range = explode(' - ', $request->bookingrange);
            if (count($range) === 2) {
                $query->whereBetween('receipt_date', [
                    Carbon::parse($range[0])->startOfDay(),
                    Carbon::parse($range[1])->endOfDay()
                ]);
            }
        }
        if ($request->filled('lead_name')) {
            $query->whereHas('lead', fn($q) => $q->where('name', 'like', "%{$request->lead_name}%"));
        }
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->filled('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }
        if ($request->filled('search_global')) {
            $search = $request->search_global;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('lead', fn($ql) => $ql->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('company', fn($qc) => $qc->where('bill_name', 'like', "%{$search}%"));
            });
        }
    }

    /**
     * Generate action buttons for DataTables.
     */
    private function getActionButtons($row)
    {
        return '
        <div class="dropdown table-action">
            <a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item edit-receipt" href="#" data-id="'.$row->id.'"><i class="ti ti-edit text-blue"></i> Edit</a>
                <a class="dropdown-item delete-receipt" href="#" data-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#delete_invoices"><i class="ti ti-trash text-danger"></i> Delete</a>
            </div>
        </div>';
    }

    /**
     * Dispatch Receipt export job.
     */
    public function exportReceipts(Request $request)
    {
        $boxUser = $this->getBoxContext();
        $format = $request->input('format', 'excel');
        $fileName = 'receipts_export_' . time() . ($format === 'excel' ? '.xlsx' : '.pdf');
        
        $filters = $request->only(['start_date', 'end_date', 'bookingrange', 'lead_name', 'company_id', 'payment_mode', 'search_global']);
        $filters['box_id'] = $boxUser->box_id;

        ExportReceiptsJob::dispatch($filters, $format, $fileName, Auth::id());

        return response()->json([
            'success' => true, 
            'message' => 'Your export is being processed. You will be notified when ready.',
            'file_name' => $fileName
        ]);
    }

    public function checkExportStatus(Request $request)
    {
        $path = 'exports/' . $request->query('file_name');
        return response()->json(['success' => true, 'ready' => Storage::disk('public')->exists($path)]);
    }

    public function downloadExport(Request $request)
    {
        $path = 'exports/' . $request->query('file_name');
        return Storage::disk('public')->exists($path) 
            ? Storage::disk('public')->download($path)
            : abort(404, 'Export file not found.');
    }

    /**
     * Fetch data for receipt creation/editing modals.
     */
    public function getReceiptFormData(Request $request) 
    {
        $boxUser = $this->getBoxContext();
        $boxId = $boxUser->box_id;
        $response = [];

        if ($request->has('fetch_initial')) {
             $response['leads'] = Lead::where('box_id', $boxId)->select('id', 'name', 'last_name', 'company_name')->get();
             $response['companies'] = Company::where('box_id', $boxId)->select('id', 'bill_name')->get();
         $response['payment_modes'] = PaymentMode::where('box_id', $boxId)->where('status', 'Enabled')->get();
        }

        if ($request->filled('company_id')) {
             $response['banks'] = Bank::where('box_id', $boxId)->where('company_id', $request->company_id)->get();
             $response['divisions'] = Division::where('box_id', $boxId)->where('company_id', $request->company_id)->get();
        }
        
        return response()->json($response);
    }

    /**
     * Store a new receipt.
     */
    public function store(Request $request)
    {
        $boxUser = $this->getBoxContext();
        $validated = $request->validate($this->getValidationRules());

        $receipt = new Receipt($validated);
        $receipt->box_id = $boxUser->box_id;
        $receipt->box_user_id = $boxUser->id;
        $receipt->created_by = Auth::id();
        $receipt->updated_by = Auth::id();
        $receipt->save();

        // Add history entry if lead_id is present
        if (!empty($receipt->lead_id)) {
            $user = Auth::user();
            $descriptionParts = [
                "Receipt created by " . ($user->name ?? 'User'),
                "Amount: " . $receipt->amount
            ];
            
            if (!empty($receipt->invoice_number)) {
                $descriptionParts[] = "Invoice Number: " . $receipt->invoice_number;
            }
            if (!empty($receipt->receipt_date)) {
                $receiptDateStr = $receipt->receipt_date instanceof \Carbon\Carbon ? $receipt->receipt_date->format('d M Y') : $receipt->receipt_date;
                $descriptionParts[] = "Receipt Date: " . $receiptDateStr;
            }

            \App\Models\Activity::create([
                'box_id' => $boxUser->box_id,
                'activity_id' => $receipt->lead_id,
                'type' => 'receipt',
                'subject' => 'Receipt Created',
                'description' => implode("\n", $descriptionParts),
                'activity_date' => now(),
                'added_by' => Auth::id(),
                'last_updated_by' => Auth::id(),
                'status' => 'Close',
                'completed_date' => now(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Receipt created successfully']);
    }

    /**
     * Update an existing receipt.
     */
    public function update(Request $request)
    {
        $boxUser = $this->getBoxContext();
        $receipt = Receipt::where('box_id', $boxUser->box_id)->findOrFail($request->input('id'));
        
        $validated = $request->validate($this->getValidationRules());
        $receipt->update(array_merge($validated, ['updated_by' => Auth::id()]));

        return response()->json(['success' => true, 'message' => 'Receipt updated successfully']);
    }

    /**
     * Fetch a single receipt record.
     */
    public function show(Request $request)
    {
        $boxUser = $this->getBoxContext();
        $receipt = Receipt::where('box_id', $boxUser->box_id)->findOrFail($request->input('id'));
        return response()->json(['success' => true, 'data' => $receipt]);
    }
    
    /**
     * Soft delete a receipt.
     */
    public function destroy(Request $request)
    {
        $boxUser = $this->getBoxContext();
        $receipt = Receipt::where('box_id', $boxUser->box_id)->findOrFail($request->input('id'));
        
        $receipt->update(['deleted_by' => Auth::id()]);
        $receipt->delete();

        return response()->json(['success' => true, 'message' => 'Receipt deleted successfully']);
    }

    /**
     * Get receipts for a specific lead.
     */
    public function getLeadReceipts(Request $request)
    {
        $boxUser = $this->getBoxContext();
        $leadId = $request->input('lead_id');
        
        if (!$leadId) {
            return response()->json(['success' => false, 'message' => 'Lead ID is required']);
        }

        $receipts = Receipt::where('box_id', $boxUser->box_id)
            ->where('lead_id', $leadId)
            ->with(['company:id,bill_name', 'bank:id,name', 'creator:id,name', 'division:id,name'])
            ->latest()
            ->get();

        return response()->json(['success' => true, 'data' => $receipts]);
    }
}
