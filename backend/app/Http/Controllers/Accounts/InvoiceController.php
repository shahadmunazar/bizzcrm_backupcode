<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\BoxUser;

class InvoiceController extends Controller
{
    /**
     * Get the current user's box context.
     */
    protected function getBoxContext()
    {
        return BoxUser::where('user_id', Auth::id())->firstOrFail();
    }

    /**
     * Show the invoice creation page.
     */
    public function index()
    {
        return view('accounts.invoice');
    }

    public function list(Request $request)
    {
        $boxUser = $this->getBoxContext();
        $company_id = $request->query('company_id');
        $company = null;
        if ($company_id) {
            $company = \App\Models\Company::where('box_id', $boxUser->box_id)->find($company_id);
        }
        return view('accounts.invoices', compact('company_id', 'company'));
    }
    
    /**
     * Get invoices data as JSON.
     */
    public function getInvoicesData(Request $request)
    {
        $boxUser = $this->getBoxContext();
        $company_id = $request->query('company_id');
        
        $query = \App\Models\Accounts\Invoice::where('box_id', $boxUser->box_id)
            ->with(['lead', 'company', 'items.product']);
            
        if ($company_id) {
            $query->where('company_id', $company_id);
        }
        
        $invoices = $query->orderBy('id', 'desc')->get();
            
        // Map the data to return a cleaner structure if desired, or just return directly
        $data = $invoices->map(function ($invoice) {
            return [
                'id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no,
                'company_id' => $invoice->company_id,
                'lead_id' => $invoice->lead_id,
                'company_name' => $invoice->company->bill_name ?? $invoice->company->name ?? 'N/A',
                'lead_name' => $invoice->lead->name ?? 'N/A',
                'items' => $invoice->items->map(function($item) {
                    return [
                        'id' => $item->id,
                        'package_id' => $item->package_id,
                        'product_name' => $item->product->product_name ?? 'N/A',
                        'qty' => $item->qty,
                        'msp' => $item->msp,
                        'amount' => $item->amount,
                    ];
                }),
                'invoice_date' => $invoice->invoice_date,
                'due_date' => $invoice->due_date,
                'amount' => $invoice->amount ?? 0,
                'amount_with_tax' => $invoice->amount_with_tax ?? $invoice->amount ?? 0,
                'paid_amount' => $invoice->paid_amount ?? 0,
                'balance_amount' => $invoice->balance_amount ?? 0,
                'status' => $invoice->status ?? 'Unpaid',
                'remarks' => $invoice->remarks,
            ];
        });
            
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Store a newly created invoice.
     */
    
    public function ajaxClients(Request $request)
    {
        $boxUser = $this->getBoxContext();
        $term = $request->q;
        $page = $request->page ?: 1;
        
        $query = \App\Models\Company::where('box_id', $boxUser->box_id);
        
        if ($term) {
            $query->where('bill_name', 'like', '%' . $term . '%');
        }
        
        $results = $query->select('id', 'bill_name')
            ->orderBy('id', 'desc')
            ->paginate(15, ['*'], 'page', $page);
            
        $items = [];
        foreach ($results as $company) {
            $items[] = [
                'id' => $company->id,
                'text' => $company->bill_name
            ];
        }
        
        return response()->json([
            'results' => $items,
            'pagination' => [
                'more' => $results->hasMorePages()
            ]
        ]);
    }

    public function ajaxLeads(Request $request)
    {
        $boxUser = $this->getBoxContext();
        $term = $request->q;
        $page = $request->page ?: 1;
        
        $query = \App\Models\Lead::where('box_id', $boxUser->box_id);
        
        if ($term) {
            $query->where('name', 'like', '%' . $term . '%');
        }
        
        $results = $query->select('id', 'name')
            ->orderBy('id', 'desc')
            ->paginate(15, ['*'], 'page', $page);
            
        $items = [];
        foreach ($results as $lead) {
            $items[] = [
                'id' => $lead->id,
                'text' => $lead->name
            ];
        }
        
        return response()->json([
            'results' => $items,
            'pagination' => [
                'more' => $results->hasMorePages()
            ]
        ]);
    }

    public function ajaxProducts(Request $request)
    {
        $term = $request->q;
        $page = $request->page ?: 1;
        
        $query = \App\Models\Product::query();
        
        if ($term) {
            $query->where('product_name', 'like', '%' . $term . '%')
                  ->orWhere('code', 'like', '%' . $term . '%');
        }
        
        $results = $query->select('id', 'product_name', 'code', 'mrp', 'hsn_sac')
            ->orderBy('product_name', 'asc')
            ->paginate(15, ['*'], 'page', $page);
            
        $items = [];
        foreach ($results as $product) {
            $items[] = [
                'id' => $product->id,
                'text' => $product->product_name . ($product->code ? ' (' . $product->code . ')' : ''),
                'rate' => $product->mrp ?: 0,
                'hsn' => $product->hsn_sac
            ];
        }
        
        return response()->json([
            'results' => $items,
            'pagination' => [
                'more' => $results->hasMorePages()
            ]
        ]);
    }

    public function store(Request $request)
    {
        try {
            $boxUser = $this->getBoxContext();
            
            $validated = $request->validate([
                'company_id' => 'required|exists:companies,id',
                'lead_id' => 'required|exists:leads,id',
                'amount' => 'required|numeric',
                'currency' => 'nullable|string',
                'invoice_date' => 'required|date',
                'due_date' => 'nullable|date',
                'payment_method' => 'nullable|string',
                'status' => 'required|string',
                'remarks' => 'nullable|string',
            ]);
            
            $invoice = new \App\Models\Accounts\Invoice();
            $invoice->box_id = $boxUser->box_id;
            $invoice->company_id = $validated['company_id'];
            $invoice->lead_id = $validated['lead_id'];
            $invoice->amount = $validated['amount'];
            $invoice->amount_with_tax = $validated['amount']; // Can add tax logic later
            $invoice->paid_amount = $request->input('paid_amount', 0);
            $invoice->balance_amount = $request->input('balance_amount', $invoice->amount - $invoice->paid_amount);
            $invoice->invoice_date = \Carbon\Carbon::parse($validated['invoice_date']);
            if (!empty($validated['due_date'])) {
                $invoice->due_date = \Carbon\Carbon::parse($validated['due_date']);
            }
            $invoice->status = $validated['status'];
            $invoice->remarks = $validated['remarks'] ?? null;
            $invoice->added_by = Auth::id();
            $invoice->last_updated_by = Auth::id();
            
            // Generate invoice_no dynamically
            $latestInvoice = \App\Models\Accounts\Invoice::where('box_id', $boxUser->box_id)->orderBy('id', 'desc')->first();
            $nextNumber = $latestInvoice ? intval(preg_replace('/[^0-9]/', '', $latestInvoice->invoice_no)) + 1 : 1;
            $invoice->invoice_no = 'INV-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $invoice->system_number = $nextNumber;
            
            $invoice->save();
            
            if ($request->has('products') && is_array($request->products)) {
                foreach ($request->products as $item) {
                    if (!empty($item['product_id'])) {
                        $invoice->items()->create([
                            'box_id' => $boxUser->box_id,
                            'lead_id' => $invoice->lead_id,
                            'package_id' => $item['product_id'],
                            'qty' => $item['qty'] ?? 1,
                            'msp' => $item['rate'] ?? 0,
                            'amount' => $item['amount'] ?? 0,
                            'sgst' => 0,
                            'cgst' => 0,
                            'igst' => 0,
                        ]);
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update an existing invoice.
     */
    public function update(Request $request, $box_slug, $id)
    {
        try {
            $boxUser = $this->getBoxContext();
            
            $validated = $request->validate([
                'company_id' => 'required|exists:companies,id',
                'lead_id' => 'required|exists:leads,id',
                'amount' => 'required|numeric',
                'paid_amount' => 'nullable|numeric',
                'balance_amount' => 'nullable|numeric',
                'invoice_date' => 'required|date',
                'due_date' => 'nullable|date',
                'status' => 'required|string',
                'remarks' => 'nullable|string',
            ]);
            
            $invoice = \App\Models\Accounts\Invoice::where('box_id', $boxUser->box_id)
                                                   ->where('id', $id)
                                                   ->firstOrFail();
                                                   
            $invoice->company_id = $validated['company_id'];
            $invoice->lead_id = $validated['lead_id'];
            $invoice->amount = $validated['amount'];
            $invoice->amount_with_tax = $validated['amount'];
            $invoice->paid_amount = $request->input('paid_amount', 0);
            $invoice->balance_amount = $request->input('balance_amount', $invoice->amount - $invoice->paid_amount);
            
            $invoice->invoice_date = \Carbon\Carbon::parse($validated['invoice_date']);
            if (isset($validated['due_date'])) {
                $invoice->due_date = !empty($validated['due_date']) ? \Carbon\Carbon::parse($validated['due_date']) : null;
            }
            $invoice->status = $validated['status'];
            $invoice->remarks = $validated['remarks'] ?? null;
            $invoice->last_updated_by = Auth::id();
            
            $invoice->save();
            
            if ($request->has('products') && is_array($request->products)) {
                $invoice->items()->delete();
                foreach ($request->products as $item) {
                    if (!empty($item['product_id'])) {
                        $invoice->items()->create([
                            'box_id' => $boxUser->box_id,
                            'lead_id' => $invoice->lead_id,
                            'package_id' => $item['product_id'],
                            'qty' => $item['qty'] ?? 1,
                            'msp' => $item['rate'] ?? 0,
                            'amount' => $item['amount'] ?? 0,
                            'sgst' => 0,
                            'cgst' => 0,
                            'igst' => 0,
                        ]);
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update invoice: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete an invoice.
     */
    public function destroy($box_slug, $id)
    {
        try {
            $boxUser = $this->getBoxContext();
            
            $invoice = \App\Models\Accounts\Invoice::where('box_id', $boxUser->box_id)
                                                   ->where('id', $id)
                                                   ->firstOrFail();
                                                   
            $invoice->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show invoice details.
     */
    public function details($box_slug, $id)
    {
        $boxUser = $this->getBoxContext();
        
        $invoice = \App\Models\Accounts\Invoice::where('box_id', $boxUser->box_id)
                                               ->with(['lead', 'company'])
                                               ->where('id', $id)
                                               ->firstOrFail();
                                               
        $box = \App\Models\Box::find($boxUser->box_id);
                                               
        $settings = \App\Models\Accounts\InvoiceSetting::firstOrCreate(
            ['box_id' => $boxUser->box_id],
            [
                'invoice_prefix' => 'INV-',
                'template_style' => 'template_1',
            ]
        );
                                               
        return view('accounts.invoice-details', compact('invoice', 'box', 'settings'));
    }

    /**
     * Print invoice.
     */
    public function print($box_slug, $id)
    {
        $boxUser = $this->getBoxContext();
        
        $invoice = \App\Models\Accounts\Invoice::where('box_id', $boxUser->box_id)
                                               ->with(['lead', 'company'])
                                               ->where('id', $id)
                                               ->firstOrFail();
                                               
        $box = \App\Models\Box::find($boxUser->box_id);

        $settings = \App\Models\Accounts\InvoiceSetting::firstOrCreate(
            ['box_id' => $boxUser->box_id],
            [
                'invoice_prefix' => 'INV-',
                'template_style' => 'template_1',
            ]
        );
        $template = $settings->template_style ?? 'template_1';
        return view('accounts.invoices.templates.' . $template, compact('invoice', 'settings', 'box'));
    }

    /**
     * Show invoice grid view.
     */
    /**
     * Show invoice grid view.
     */
    public function grid(Request $request)
    {
        $boxUser = $this->getBoxContext();
        $company_id = $request->query('company_id');
        $company = null;
        if ($company_id) {
            $company = \App\Models\Company::where('box_id', $boxUser->box_id)->find($company_id);
        }
        return view('accounts.invoice-grid', compact('company_id', 'company'));
    }

    /**
     * Show invoice settings view.
     */
    public function settings($box_slug)
    {
        $boxUser = $this->getBoxContext();
        $company_id = request()->query('company_id');
        $query = ['box_id' => $boxUser->box_id];
        if ($company_id) {
            $query['company_id'] = $company_id;
        }
        
        $settings = \App\Models\Accounts\InvoiceSetting::firstOrCreate(
            $query,
            [
                'invoice_prefix' => 'INV-',
                'invoice_due_days' => 5,
                'enable_round_off' => true,
                'round_off_type' => 'Up',
                'show_company_details' => true
            ]
        );
        return view('accounts.invoice-settings', compact('settings', 'company_id'));
    }

    /**
     * Save invoice settings.
     */
    public function saveSettings(Request $request, $box_slug)
    {
        $boxUser = $this->getBoxContext();
        
        $validated = $request->validate([
            'invoice_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'invoice_logo_base64' => 'nullable|string',
            'logo_width' => 'nullable|integer|min:10|max:1000',
            'logo_height' => 'nullable|integer|min:10|max:1000',
            'invoice_prefix' => 'nullable|string|max:50',
            'invoice_due_days' => 'nullable|integer|min:0',
            'enable_round_off' => 'nullable|boolean',
            'round_off_type' => 'nullable|string|in:Up,Down',
            'show_company_details' => 'nullable|boolean',
            'header_terms' => 'nullable|string',
            'footer_terms' => 'nullable|string',
        ]);

        $settings = \App\Models\Accounts\InvoiceSetting::firstOrNew(['box_id' => $boxUser->box_id]);

        if ($request->filled('invoice_logo_base64')) {
            $base64Data = $request->input('invoice_logo_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                $type = strtolower($type[1]);
                if (in_array($type, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $base64Data = base64_decode($base64Data);
                    $filename = time() . '_cropped.' . $type;
                    $path = 'logos/' . $filename;
                    \Illuminate\Support\Facades\Storage::disk('public')->put($path, $base64Data);
                    $settings->invoice_logo = 'storage/' . $path;
                }
            }
        } elseif ($request->hasFile('invoice_logo')) {
            $file = $request->file('invoice_logo');
            $originalName = $file->getClientOriginalName();
            $sanitizedName = preg_replace('/[^A-Za-z0-9\-\.]/', '_', $originalName);
            $filename = time() . '_' . $sanitizedName;
            // Store in the 'public' disk (storage/app/public/logos)
            $path = $file->storeAs('logos', $filename, 'public');
            $settings->invoice_logo = 'storage/' . $path;
        }

        $settings->logo_width = $validated['logo_width'] ?? 250;
        $settings->logo_height = $validated['logo_height'] ?? 80;
        $settings->invoice_prefix = $validated['invoice_prefix'] ?? 'INV-';
        $settings->invoice_due_days = $validated['invoice_due_days'] ?? 5;
        $settings->enable_round_off = $request->has('enable_round_off');
        $settings->round_off_type = $validated['round_off_type'] ?? 'Up';
        $settings->show_company_details = $request->has('show_company_details');
        $settings->header_terms = $validated['header_terms'] ?? null;
        $settings->footer_terms = $validated['footer_terms'] ?? null;
        
        $settings->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Invoice settings updated successfully!']);
        }

        return redirect()->back()->with('success', 'Invoice settings updated successfully!');
    }

    /**
     * Download invoice as PDF.
     */
    public function downloadPdf($box_slug, $id)
    {
        $boxUser = $this->getBoxContext();
        
        $invoice = \App\Models\Accounts\Invoice::where('box_id', $boxUser->box_id)
                                               ->with(['lead', 'company'])
                                               ->where('id', $id)
                                               ->firstOrFail();
                                               
        $box = \App\Models\Box::find($boxUser->box_id);

        $settings = \App\Models\Accounts\InvoiceSetting::firstOrCreate(
            ['box_id' => $boxUser->box_id],
            [
                'invoice_prefix' => 'INV-',
                'template_style' => 'template_1',
            ]
        );
        
        $activeTemplate = $settings->template_style ?? 'template_1';
        
        // Prepare base64 logo for PDF to avoid SSL/network issues
        // Try to use Box logo first, then Invoice Setting logo, then default PNG
        $logoPath = public_path('assets/img/logo.png');
        if ($box->logo && file_exists(storage_path('app/public/' . $box->logo))) {
            $logoPath = storage_path('app/public/' . $box->logo);
        } elseif (!empty($settings->invoice_logo) && file_exists(storage_path('app/public/' . $settings->invoice_logo))) {
            $logoPath = storage_path('app/public/' . $settings->invoice_logo);
        }

        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $ext = pathinfo($logoPath, PATHINFO_EXTENSION);
            if(strtolower($ext) === 'svg') {
                $logoBase64 = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($logoPath));
            } else {
                $mime = mime_content_type($logoPath);
                $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        }
        
        $orientation = ($activeTemplate === 'template_3') ? 'landscape' : 'portrait';
        $pdf = Pdf::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView("accounts.invoices.templates.{$activeTemplate}", compact('invoice', 'box', 'settings', 'logoBase64'))
            ->setPaper('A4', $orientation);
            
        return $pdf->download("invoice-{$invoice->invoice_no}.pdf");
    }
}
