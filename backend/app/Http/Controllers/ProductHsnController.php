<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductHsnService;

class ProductHsnController extends Controller
{
    protected $productHsnService;

    public function __construct(ProductHsnService $productHsnService)
    {
        $this->productHsnService = $productHsnService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $box_id = request()->attributes->get('box')->id ?? null;
            return $this->productHsnService->getDatatables($box_id);
        }

        $company_id = request('company_id');
        return view('product_hsns.index', compact('company_id'));
    }

    public function store(Request $request, $box_slug)
    {
        $box_id = request()->attributes->get('box')->id ?? null;
        
        $validatedData = $request->validate([
            'code' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:255',
            'sgst' => 'required|numeric',
            'cgst' => 'required|numeric',
            'igst' => 'required|numeric',
            'type' => 'required|string',
            'uqc' => 'nullable|string|max:255',
            'cess' => 'required|numeric',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'status' => 'required|string'
        ]);

        $hsn = $this->productHsnService->store($validatedData, $box_id, auth()->id());

        return response()->json(['success' => true, 'message' => 'Product HSN created successfully.', 'data' => $hsn]);
    }

    public function update(Request $request, $box_slug, $id)
    {
        $box_id = request()->attributes->get('box')->id ?? null;
        
        $validatedData = $request->validate([
            'code' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:255',
            'sgst' => 'required|numeric',
            'cgst' => 'required|numeric',
            'igst' => 'required|numeric',
            'type' => 'required|string',
            'uqc' => 'nullable|string|max:255',
            'cess' => 'required|numeric',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'status' => 'required|string'
        ]);

        $hsn = $this->productHsnService->update($validatedData, $box_id, $id, auth()->id());

        return response()->json(['success' => true, 'message' => 'Product HSN updated successfully.', 'data' => $hsn]);
    }

    public function destroy(Request $request, $box_slug, $id)
    {
        $box_id = request()->attributes->get('box')->id ?? null;
        
        $result = $this->productHsnService->delete($box_id, $id, auth()->id());

        if (isset($result['success']) && !$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 400);
        }

        return response()->json(['success' => true, 'message' => 'Product HSN deleted successfully.']);
    }

    public function bulkDestroy(Request $request, $box_slug)
    {
        $box_id = request()->attributes->get('box')->id ?? null;
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No items selected.'], 400);
        }

        $result = $this->productHsnService->bulkDelete($box_id, $ids, auth()->id());
        
        if ($result['deleted_count'] === 0) {
            return response()->json([
                'success' => false, 
                'message' => 'Could not delete the selected HSNs as they are currently in use.'
            ], 400);
        }
        
        $msg = "Successfully deleted {$result['deleted_count']} HSN(s).";
        if (count($result['failed_codes']) > 0) {
            $msg .= " Some were skipped because they are in use: " . implode(', ', $result['failed_codes']);
        }

        return response()->json(['success' => true, 'message' => $msg]);
    }

    public function history($box_slug, $id)
    {
        $box_id = request()->attributes->get('box')->id ?? null;
        
        $history = $this->productHsnService->getHistory($box_id, $id);

        return response()->json(['success' => true, 'data' => $history]);
    }

    public function getDetailsByCode($box_slug, $code)
    {
        $box_id = request()->attributes->get('box')->id ?? null;
        $hsn = $this->productHsnService->getDetailsByCode($box_id, $code);

        if ($hsn) {
            return response()->json(['success' => true, 'data' => $hsn]);
        }

        return response()->json(['success' => false, 'message' => 'HSN details not found'], 404);
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
            'entity_type' => 'ProductHsn',
            'file_path' => $path,
            'status' => 'processing',
        ]);

        $import = new \App\Imports\ProductHsnImport($box_id, auth()->id(), $bulkImport->id);
        
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
            'Content-Disposition' => 'attachment; filename="hsn_import_template.csv"',
        ];

        $columns = ['code', 'short_name', 'sgst', 'cgst', 'igst', 'cess', 'type', 'uqc', 'effective_date_from', 'effective_date_to'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Sample data row
            fputcsv($file, ['8471', 'Computers', '9.00', '9.00', '18.00', '0.00', 'Goods', 'NOS', '2026-01-01', '']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
