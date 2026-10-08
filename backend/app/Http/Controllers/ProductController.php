<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $box_id = request()->attributes->get('box')->id ?? null;
        
        $query = Product::latest();
        if (request()->has('company_id')) {
            $query->where('company_id', request('company_id'));
        }
        $products = $query->get();
        
        $companies = \App\Models\Company::where('box_id', $box_id)->get();
        $salts = \App\Models\ProductSalt::where('box_id', $box_id)->get();
        $hsns = \App\Models\ProductHsn::where('box_id', $box_id)->get();
        return view('products.index', compact('products', 'companies', 'salts', 'hsns'));
    }

    public function create()
    {
        // Need to get box_id to filter. Assuming it's in request attributes.
        $box_id = request()->attributes->get('box')->id ?? null;
        $companies = \App\Models\Company::where('box_id', $box_id)->get();
        $salts = \App\Models\ProductSalt::where('box_id', $box_id)->get();
        $hsns = \App\Models\ProductHsn::where('box_id', $box_id)->get();

        return view('products.create', compact('companies', 'salts', 'hsns'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'status' => 'nullable|string',
            'type' => 'nullable|string',
            'hide' => 'nullable|boolean',
            'code' => 'required|string',
            'product_name' => 'required|string|unique:products,product_name',
            'packing' => 'required|string',
            'unit' => 'nullable|string',
            'decimal' => 'nullable|boolean',
            'fast_search' => 'nullable|string',
            'color_type' => 'nullable|string',
            'item_type' => 'nullable|string',
            'company' => 'required|string',
            'company_status' => 'nullable|string',
            'salt' => 'required|string',
            'salt_status' => 'nullable|string',
            'hsn_sac' => 'required|string',
            'hsn_status' => 'nullable|string',
            'erp_to_erp' => 'required|string',
            'local_tax' => 'nullable|string',
            'central_tax' => 'nullable|string',
            'sgst_percent' => 'nullable|numeric|max:999.99',
            'cgst_percent' => 'nullable|numeric|max:999.99',
            'igst_percent' => 'nullable|numeric|max:999.99',
            'cess_percent' => 'nullable|numeric|max:999.99',
            'cess_amount' => 'nullable|numeric|max:9999999.99',
            'csr' => 'nullable|numeric|max:9999999.99',
            'mrp' => 'nullable|numeric|max:9999999.99',
            'purchase_rate' => 'nullable|numeric|max:9999999.99',
            'cost' => 'nullable|numeric|max:9999999.99',
            'rate_a' => 'nullable|numeric|max:9999999.99',
            'rate_b' => 'nullable|numeric|max:9999999.99',
            'rate_c' => 'nullable|numeric|max:9999999.99',
            'conv_box' => 'nullable|numeric|max:9999999.99',
            'conv_cas' => 'nullable|numeric|max:9999999.99',
            'negative_stock' => 'nullable|boolean',
            'minimum_qty' => 'nullable|integer|max:99999999',
            'maximum_qty' => 'nullable|integer|max:99999999',
            'reorder_qty' => 'nullable|integer|max:99999999',
            'reorder_days' => 'nullable|integer|max:99999999',
            'volume_discount' => 'nullable|numeric|max:9999999.99',
            'on_qty_volume' => 'nullable|numeric|max:9999999.99',
            'item_discount' => 'nullable|numeric|max:9999999.99',
            'special_disc' => 'nullable|numeric|max:9999999.99',
            'discount_type' => 'nullable|string',
            'on_quantity_disc' => 'nullable|integer|max:99999999',
            'maximum_discount_percent' => 'nullable|numeric|max:9999999.99',
            'purchase_disc' => 'nullable|numeric|max:9999999.99',
            'f6_rate_plus_minus' => 'nullable|string',
            'free_scheme' => 'nullable|string',
            'valid_from' => 'nullable|string',
            'minimum_margin_percent' => 'nullable|numeric|max:9999999.99',
            'disc_less' => 'nullable|numeric|max:9999999.99',
            'manufacturer' => 'nullable|string',
            'narcotic' => 'nullable|boolean',
            'schedule_h' => 'nullable|boolean',
            'schedule_h1' => 'nullable|boolean',
            'schedule_h1_type' => 'nullable|string',
        ]);

        $numericFields = [
            'sgst_percent', 'cgst_percent', 'igst_percent', 'cess_percent', 'cess_amount',
            'csr', 'mrp', 'purchase_rate', 'cost', 'rate_a', 'rate_b', 'rate_c', 'conv_box', 'conv_cas',
            'minimum_qty', 'maximum_qty', 'reorder_qty', 'reorder_days', 'volume_discount',
            'on_qty_volume', 'item_discount', 'special_disc', 'on_quantity_disc',
            'maximum_discount_percent', 'purchase_disc', 'minimum_margin_percent', 'disc_less'
        ];
        
        foreach ($numericFields as $field) {
            if (!isset($validatedData[$field]) || $validatedData[$field] === null) {
                $validatedData[$field] = 0;
            }
        }
        
        $company_id = request('company_id');
        if (!$company_id && isset($validatedData['company'])) {
            $comp = \App\Models\Company::where('bill_name', $validatedData['company'])->first();
            if ($comp) {
                $company_id = $comp->id;
            }
        }
        
        if ($company_id) {
            $validatedData['company_id'] = $company_id;
        }

        $product = Product::create($validatedData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'product' => $product,
                'edit_url' => box_route('accounts.products.edit', $product->id),
                'delete_url' => box_route('accounts.products.destroy', $product->id),
            ]);
        }

        return redirect()->route('accounts.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Request $request, $box_slug, Product $product)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'product' => $product,
                'update_url' => box_route('accounts.products.update', $product->id),
            ]);
        }

        $box_id = request()->attributes->get('box')->id ?? null;
        $companies = \App\Models\Company::where('box_id', $box_id)->get();
        $salts = \App\Models\ProductSalt::where('box_id', $box_id)->get();
        $hsns = \App\Models\ProductHsn::where('box_id', $box_id)->get();

        return view('products.edit', compact('product', 'companies', 'salts', 'hsns'));
    }

    public function update(Request $request, $box_slug, Product $product)
    {
        $validatedData = $request->validate([
            'status' => 'nullable|string',
            'type' => 'nullable|string',
            'hide' => 'nullable|boolean',
            'code' => 'required|string',
            'product_name' => 'required|string|unique:products,product_name,' . $product->id,
            'packing' => 'required|string',
            'unit' => 'nullable|string',
            'decimal' => 'nullable|boolean',
            'fast_search' => 'nullable|string',
            'color_type' => 'nullable|string',
            'item_type' => 'nullable|string',
            'company' => 'required|string',
            'company_status' => 'nullable|string',
            'salt' => 'required|string',
            'salt_status' => 'nullable|string',
            'hsn_sac' => 'required|string',
            'hsn_status' => 'nullable|string',
            'erp_to_erp' => 'required|string',
            'local_tax' => 'nullable|string',
            'central_tax' => 'nullable|string',
            'sgst_percent' => 'nullable|numeric|max:999.99',
            'cgst_percent' => 'nullable|numeric|max:999.99',
            'igst_percent' => 'nullable|numeric|max:999.99',
            'cess_percent' => 'nullable|numeric|max:999.99',
            'cess_amount' => 'nullable|numeric|max:9999999.99',
            'csr' => 'nullable|numeric|max:9999999.99',
            'mrp' => 'nullable|numeric|max:9999999.99',
            'purchase_rate' => 'nullable|numeric|max:9999999.99',
            'cost' => 'nullable|numeric|max:9999999.99',
            'rate_a' => 'nullable|numeric|max:9999999.99',
            'rate_b' => 'nullable|numeric|max:9999999.99',
            'rate_c' => 'nullable|numeric|max:9999999.99',
            'conv_box' => 'nullable|numeric|max:9999999.99',
            'conv_cas' => 'nullable|numeric|max:9999999.99',
            'negative_stock' => 'nullable|boolean',
            'minimum_qty' => 'nullable|integer|max:99999999',
            'maximum_qty' => 'nullable|integer|max:99999999',
            'reorder_qty' => 'nullable|integer|max:99999999',
            'reorder_days' => 'nullable|integer|max:99999999',
            'volume_discount' => 'nullable|numeric|max:9999999.99',
            'on_qty_volume' => 'nullable|numeric|max:9999999.99',
            'item_discount' => 'nullable|numeric|max:9999999.99',
            'special_disc' => 'nullable|numeric|max:9999999.99',
            'discount_type' => 'nullable|string',
            'on_quantity_disc' => 'nullable|integer|max:99999999',
            'maximum_discount_percent' => 'nullable|numeric|max:9999999.99',
            'purchase_disc' => 'nullable|numeric|max:9999999.99',
            'f6_rate_plus_minus' => 'nullable|string',
            'free_scheme' => 'nullable|string',
            'valid_from' => 'nullable|string',
            'minimum_margin_percent' => 'nullable|numeric|max:9999999.99',
            'disc_less' => 'nullable|numeric|max:9999999.99',
            'manufacturer' => 'nullable|string',
            'narcotic' => 'nullable|boolean',
            'schedule_h' => 'nullable|boolean',
            'schedule_h1' => 'nullable|boolean',
            'schedule_h1_type' => 'nullable|string',
        ]);

        $numericFields = [
            'sgst_percent', 'cgst_percent', 'igst_percent', 'cess_percent', 'cess_amount',
            'csr', 'mrp', 'purchase_rate', 'cost', 'rate_a', 'rate_b', 'rate_c', 'conv_box', 'conv_cas',
            'minimum_qty', 'maximum_qty', 'reorder_qty', 'reorder_days', 'volume_discount',
            'on_qty_volume', 'item_discount', 'special_disc', 'on_quantity_disc',
            'maximum_discount_percent', 'purchase_disc', 'minimum_margin_percent', 'disc_less'
        ];
        
        foreach ($numericFields as $field) {
            if (!isset($validatedData[$field]) || $validatedData[$field] === null) {
                $validatedData[$field] = 0;
            }
        }
        
        $company_id = request('company_id');
        if (!$company_id && isset($validatedData['company'])) {
            $comp = \App\Models\Company::where('bill_name', $validatedData['company'])->first();
            if ($comp) {
                $company_id = $comp->id;
            }
        }
        
        if ($company_id) {
            $validatedData['company_id'] = $company_id;
        }

        $product->update($validatedData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'product' => $product,
                'edit_url' => box_route('accounts.products.edit', $product->id),
                'delete_url' => box_route('accounts.products.destroy', $product->id),
            ]);
        }

        return redirect()->route('accounts.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, $box_slug, Product $product)
    {
        if ($this->isProductInUse($product)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not delete the Product as it is currently in use.'
                ], 400);
            }
            return redirect()->route('accounts.products.index')->with('error', 'Could not delete the Product as it is currently in use.');
        }

        $product->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.'
            ]);
        }

        return redirect()->route('accounts.products.index')->with('success', 'Product deleted successfully.');
    }

    public function bulkDestroy(Request $request, $box_slug)
    {
        $box_id = request()->attributes->get('box')->id ?? null;
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No items selected.'], 400);
        }

        $products = Product::where('id', '>', 0)->whereIn('id', $ids)->get(); // Box context assumed correct
        $deletedCount = 0;
        $failedNames = [];

        foreach ($products as $product) {
            if ($this->isProductInUse($product)) {
                $failedNames[] = $product->product_name ?? $product->code;
                continue;
            }

            $product->delete();
            $deletedCount++;
        }

        if ($deletedCount === 0) {
            return response()->json([
                'success' => false, 
                'message' => 'Could not delete the selected Products as they are currently in use.'
            ], 400);
        }
        
        $msg = "Successfully deleted {$deletedCount} Product(s).";
        if (count($failedNames) > 0) {
            $msg .= " Some were skipped because they are in use: " . implode(', ', $failedNames);
        }

        return response()->json(['success' => true, 'message' => $msg]);
    }

    private function isProductInUse(Product $product)
    {
        // Prevent deletion if the product is linked elsewhere.
        // Currently, you can expand this when Product is linked to InvoiceItem or other models.
        // e.g. return \App\Models\Accounts\InvoiceItem::where('package_id', $product->id)->exists();
        return false;
    }

    public function import(Request $request, $box_slug)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        $box_id = request()->attributes->get('box')->id ?? null;
        
        // Store the file persistently so the queue worker can access it
        $path = $request->file('file')->store('imports');
        
        // Create the tracking record
        $bulkImport = \App\Models\BulkImport::create([
            'box_id' => $box_id,
            'user_id' => auth()->id(),
            'entity_type' => 'Product',
            'file_path' => $path,
            'status' => 'processing',
        ]);

        $import = new \App\Imports\ProductImport($box_id, auth()->id(), $bulkImport->id);
        
        try {
            // Queue the import
            \Maatwebsite\Excel\Facades\Excel::queueImport($import, $path);
            
            return response()->json(['success' => true, 'message' => 'Import has been queued. You can track its progress below.']);
        } catch (\Exception $e) {
            $bulkImport->update(['status' => 'failed', 'errors' => [$e->getMessage()]]);
            return response()->json(['success' => false, 'message' => 'There was an error queuing the file: ' . $e->getMessage()], 400);
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="product_import_template.csv"',
        ];

        $columns = [
            'product_name', 'code', 'type', 'status', 'packing', 'unit', 'company', 'salt', 'hsn_sac', 'erp_to_erp',
            'mrp', 'purchase_rate', 'cost', 'rate_a', 'rate_b', 'rate_c', 
            'sgst_percent', 'cgst_percent', 'igst_percent', 'cess_percent',
            'minimum_qty', 'maximum_qty', 'reorder_qty'
        ];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Sample data row
            fputcsv($file, [
                'Sample Paracetamol 500mg', 'PARA500', 'Goods', 'Yes', '10X10', 'Strips', 'Sample Pharma', 'Paracetamol', '300490', 'ERP-01',
                '50.00', '35.00', '35.00', '40.00', '45.00', '48.00',
                '6', '6', '12', '0',
                '100', '500', '200'
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
// Refreshing IDE
