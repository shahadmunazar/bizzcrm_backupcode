<?php

namespace App\Http\Controllers;

use App\Models\ProductSalt;
use Illuminate\Http\Request;

class ProductSaltController extends Controller
{
    public function index(Request $request)
    {
        $box_id = request()->attributes->get('box')->id ?? null;
        
        if ($request->ajax()) {
            $query = ProductSalt::with('creator', 'updater', 'deleter')->where('box_id', $box_id);
            if (request()->has('company_id')) {
                $query->where('company_id', request('company_id'));
            }
            $salts = $query->get();
            return \Yajra\DataTables\Facades\DataTables::of($salts)
                ->addIndexColumn()
                ->addColumn('checkbox', function($salt) {
                    return '<input type="checkbox" class="salt-checkbox" value="'.$salt->id.'">';
                })
                ->addColumn('created_by_name', function($salt) {
                    return $salt->creator ? $salt->creator->name : '-';
                })
                ->addColumn('updated_by_name', function($salt) {
                    return $salt->updater ? $salt->updater->name : '-';
                })
                ->addColumn('deleted_by_name', function($salt) {
                    return $salt->deleter ? $salt->deleter->name : '-';
                })
                ->editColumn('created_at', function($salt) {
                    return $salt->created_at ? $salt->created_at->format('d M Y h:i A') : '-';
                })
                ->editColumn('updated_at', function($salt) {
                    return $salt->updated_at ? $salt->updated_at->format('d M Y h:i A') : '-';
                })
                ->editColumn('deleted_at', function($salt) {
                    return $salt->deleted_at ? $salt->deleted_at->format('d M Y h:i A') : '-';
                })
                ->addColumn('action', function($salt) {
                    return '<a class="btn btn-sm btn-info edit-salt-btn" href="#" data-bs-toggle="modal" data-bs-target="#editSaltModal" data-id="'.$salt->id.'" data-name="'.$salt->name.'" data-status="'.$salt->status.'">
                                <i class="fa-solid fa-pencil"></i>
                            </a>
                            <a class="btn btn-sm btn-danger delete-salt-btn" href="#" data-id="'.$salt->id.'">
                                <i class="fa-regular fa-trash-can"></i>
                            </a>';
                })
                ->editColumn('status', function($salt) {
                    $badgeClass = $salt->status == 'Yes' ? 'success' : 'danger';
                    return '<span class="badge bg-inverse-'.$badgeClass.'">'.$salt->status.'</span>';
                })
                ->rawColumns(['checkbox', 'action', 'status'])
                ->make(true);
        }

        $company_id = request('company_id');
        return view('product_salts.index', compact('company_id'));
    }

    public function store(Request $request, $box_slug)
    {
        $box_id = request()->attributes->get('box')->id ?? null;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|string'
        ]);

        $salt = ProductSalt::create([
            'box_id' => $box_id,
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Product Salt created successfully.', 'data' => $salt]);
    }

    public function update(Request $request, $box_slug, $id)
    {
        $box_id = request()->attributes->get('box')->id ?? null;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|string'
        ]);

        $salt = ProductSalt::where('box_id', $box_id)->findOrFail($id);
        $salt->update([
            'name' => $request->name,
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Product Salt updated successfully.', 'data' => $salt]);
    }

    public function destroy(Request $request, $box_slug, $id)
    {
        $box_id = request()->attributes->get('box')->id ?? null;
        $salt = ProductSalt::where('box_id', $box_id)->findOrFail($id);

        if (\App\Models\Product::where('salt', $salt->name)->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete Salt '{$salt->name}' because it is currently used in one or more products."
            ], 400);
        }

        $salt->update(['deleted_by' => auth()->id()]);
        $salt->delete();

        return response()->json(['success' => true, 'message' => 'Product Salt deleted successfully.']);
    }

    public function bulkDestroy(Request $request, $box_slug)
    {
        $box_id = request()->attributes->get('box')->id ?? null;
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No items selected.'], 400);
        }

        $salts = ProductSalt::where('box_id', $box_id)->whereIn('id', $ids)->get();
        $deletedCount = 0;
        $failedNames = [];

        foreach ($salts as $salt) {
            if (\App\Models\Product::where('salt', $salt->name)->exists()) {
                $failedNames[] = $salt->name;
                continue;
            }

            $salt->update(['deleted_by' => auth()->id()]);
            $salt->delete();
            $deletedCount++;
        }

        if ($deletedCount === 0) {
            return response()->json([
                'success' => false, 
                'message' => 'Could not delete the selected Salts as they are currently in use.'
            ], 400);
        }
        
        $msg = "Successfully deleted {$deletedCount} Salt(s).";
        if (count($failedNames) > 0) {
            $msg .= " Some were skipped because they are in use: " . implode(', ', $failedNames);
        }

        return response()->json(['success' => true, 'message' => $msg]);
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
            'entity_type' => 'ProductSalt',
            'file_path' => $path,
            'status' => 'processing',
        ]);

        $import = new \App\Imports\ProductSaltImport($box_id, auth()->id(), $bulkImport->id);
        
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
            'Content-Disposition' => 'attachment; filename="salt_import_template.csv"',
        ];

        $columns = ['name', 'status'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Sample data row
            fputcsv($file, ['Sample Salt 1', 'Yes']);
            fputcsv($file, ['Sample Salt 2', 'Yes']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
