<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\PaymentMode;
use App\Models\BoxUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PaymentModeController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return redirect(box_route('home'))->with('error', 'Box not found.');
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'read', 'payment_modes'))) {
             if ($request->ajax()) {
                 return response()->json(['status' => false, 'message' => 'You do not have permission to view payment modes.'], 403);
             }
             abort(403, 'You do not have permission to access payment modes settings.');
        }

        $boxId = $boxUser->box_id;

        if ($request->ajax()) {
            $query = PaymentMode::where('box_id', $boxId)->with(['creator', 'updater']);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status_badge', function ($row) {
                    $class = $row->status == 'Enabled' ? 'bg-success' : 'bg-danger';
                    return '<span class="badge ' . $class . '">' . $row->status . '</span>';
                })
                ->addColumn('added_by', function ($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('updated_by', function ($row) {
                    return $row->updater->name ?? 'N/A';
                })
                ->addColumn('actions', function ($row) {
                    return '
                        <div class="dropdown table-action">
                            <a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item edit-payment-mode-btn" href="javascript:void(0);"
                                    data-id="' . $row->id . '"
                                    data-name="' . e($row->name) . '"
                                    data-status="' . $row->status . '">
                                    <i class="fa-solid fa-pencil text-blue"></i> Edit
                                </a>
                                <a class="dropdown-item delete-payment-mode-btn" href="javascript:void(0);"
                                    data-id="' . $row->id . '">
                                    <i class="fa-regular fa-trash-can text-danger"></i> Delete
                                </a>
                            </div>
                        </div>';
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }

        return view('settings.payment-modes');
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return response()->json(['status' => false, 'message' => 'Box not found.']);
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'create', 'payment_modes'))) {
             return response()->json(['status' => false, 'message' => 'You do not have permission to create payment modes.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        PaymentMode::create([
            'box_id' => $boxUser->box_id,
            'name' => $request->name,
            'status' => $request->status ?? 'Enabled',
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        return response()->json(['status' => true, 'message' => 'Payment mode added successfully!']);
    }

    public function update(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return response()->json(['status' => false, 'message' => 'Box not found.']);
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'update', 'payment_modes'))) {
             return response()->json(['status' => false, 'message' => 'You do not have permission to update payment modes.'], 403);
        }

        $request->validate([
            'id' => 'required|exists:payment_modes,id',
            'name' => 'required|string|max:255',
        ]);

        $paymentMode = PaymentMode::where('id', $request->id)->where('box_id', $boxUser->box_id)->first();
        if (!$paymentMode) {
            return response()->json(['status' => false, 'message' => 'Payment mode not found.']);
        }

        $paymentMode->update([
            'name' => $request->name,
            'status' => $request->status ?? 'Enabled',
            'updated_by' => $userId,
        ]);

        return response()->json(['status' => true, 'message' => 'Payment mode updated successfully!']);
    }

    public function destroy(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return response()->json(['status' => false, 'message' => 'Box not found.']);
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'delete', 'payment_modes'))) {
             return response()->json(['status' => false, 'message' => 'You do not have permission to delete payment modes.'], 403);
        }

        $paymentMode = PaymentMode::where('id', $request->id)->where('box_id', $boxUser->box_id)->first();
        if (!$paymentMode) {
            return response()->json(['status' => false, 'message' => 'Payment mode not found.']);
        }

        $paymentMode->update(['deleted_by' => $userId]);
        $paymentMode->delete();

        return response()->json(['status' => true, 'message' => 'Payment mode deleted successfully!']);
    }
}
