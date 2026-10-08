<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Accounts\InvoiceSetting;
use App\Models\BoxUser;

class InvoiceTemplateController extends Controller
{
    protected function getBoxContext()
    {
        $boxSlug = request()->segment(1);
        return BoxUser::whereHas('box', function ($query) use ($boxSlug) {
            $query->where('slug', $boxSlug);
        })->where('user_id', auth()->id())->firstOrFail();
    }

    public function index($box_slug)
    {
        $boxUser = $this->getBoxContext();
        $company_id = request()->query('company_id');
        $query = ['box_id' => $boxUser->box_id];
        if ($company_id) {
            $query['company_id'] = $company_id;
        }
        
        $settings = InvoiceSetting::firstOrCreate(
            $query,
            [
                'invoice_prefix' => 'INV-',
                'template_style' => 'template_1',
            ]
        );
        
        $activeTemplate = $settings->template_style ?? 'template_1';

        $templates = [
            [
                'id' => 'template_1',
                'name' => 'Tax Invoice (Standard)',
                'description' => 'The standard vertical tax invoice layout.',
                'image' => asset('assets/img/icons/file-invoice.svg'), // Placeholder icon
            ],
            [
                'id' => 'template_2',
                'name' => 'Challan / Credit Note',
                'description' => 'Alternative layout tailored for challans.',
                'image' => asset('assets/img/icons/file-invoice.svg'),
            ],
            [
                'id' => 'template_3',
                'name' => 'Purchase Order (Landscape)',
                'description' => 'A horizontal, wider layout for purchase orders.',
                'image' => asset('assets/img/icons/file-invoice.svg'),
            ]
        ];

        return view('accounts.invoice-templates', compact('templates', 'activeTemplate', 'company_id'));
    }

    public function setActive(Request $request, $box_slug)
    {
        $boxUser = $this->getBoxContext();
        $templateId = $request->input('template_id');

        $settings = InvoiceSetting::firstOrNew(['box_id' => $boxUser->box_id]);
        $settings->template_style = $templateId;
        $settings->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Active invoice template updated successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Active invoice template updated successfully!');
    }
    public function preview($box_slug, $style)
    {
        $boxUser = $this->getBoxContext();
        $box = $boxUser->box;

        $settings = InvoiceSetting::firstOrNew(['box_id' => $boxUser->box_id]);
        
        $invoice = \App\Models\Accounts\Invoice::with(['company', 'lead', 'items'])
            ->where('box_id', $boxUser->box_id)
            ->latest()
            ->first();
            
        if (!$invoice) {
            // Dummy invoice if none exists
            $invoice = new \App\Models\Accounts\Invoice();
            $invoice->invoice_no = 'PREVIEW-123';
            $invoice->amount = 17700.00;
            $invoice->paid_amount = 0;
            $invoice->balance_amount = 17700.00;
            $invoice->currency = '₹';
            $invoice->invoice_date = now();
            $invoice->due_date = now()->addDays(5);
        }

        return view('accounts.invoices.templates.' . $style, compact('invoice', 'settings', 'box'));
    }
}
