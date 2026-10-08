<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\BoxUser;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    private function checkPermission(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return false;
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'read', 'products'))) {
            return false;
        }

        return $boxUser->box_id;
    }

    public function index(Request $request)
    {
        $boxId = $this->checkPermission($request);
        if ($boxId === false) {
             abort(403, 'You do not have permission to access products.');
        }

        if ($request->ajax()) {
            $query = Product::where('box_id', $boxId)->with('creator');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('created_by', function ($row) {
                    return $row->creator ? ($row->creator->first_name . ' ' . $row->creator->last_name) : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center"><a href="javascript:void(0);" class="btn btn-primary btn-sm edit-product-btn me-2" data-id="' . $row->id . '" data-product_name="' . $row->product_name . '" data-mrp="' . $row->mrp . '" data-status="' . $row->status . '"><i class="ti ti-edit"></i> Edit</a>';
                    $btn .= '<a href="javascript:void(0);" class="btn btn-danger btn-sm delete-product-btn" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a></div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('settings.products');
    }

    public function store(Request $request)
    {
        $boxId = $this->checkPermission($request);
        if ($boxId === false) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'product_name' => 'required|string|max:255',
            'mrp' => 'nullable|numeric',
            'status' => 'required|boolean',
        ]);

        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();

        Product::create([
            'box_id' => $boxId,
            'box_user_id' => $boxUser ? $boxUser->id : null,
            'product_name' => $request->product_name,
            'mrp' => $request->mrp,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return response()->json(['status' => true, 'message' => 'Product created successfully.']);
    }

    public function update(Request $request)
    {
        $boxId = $this->checkPermission($request);
        if ($boxId === false) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'id' => 'required|exists:products,id',
            'product_name' => 'required|string|max:255',
            'mrp' => 'nullable|numeric',
            'status' => 'required|boolean',
        ]);

        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();

        $product = Product::where('box_id', $boxId)->findOrFail($request->id);
        $product->update([
            'box_user_id' => $boxUser ? $boxUser->id : null,
            'product_name' => $request->product_name,
            'mrp' => $request->mrp,
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['status' => true, 'message' => 'Product updated successfully.']);
    }

    public function destroy(Request $request)
    {
        $boxId = $this->checkPermission($request);
        if ($boxId === false) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'id' => 'required|exists:products,id',
        ]);

        $product = Product::where('box_id', $boxId)->findOrFail($request->id);
        $product->update(['deleted_by' => Auth::id()]);
        $product->delete();

        return response()->json(['status' => true, 'message' => 'Product deleted successfully.']);
    }
}
