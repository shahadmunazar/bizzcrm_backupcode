<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BoxUser;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BankController extends Controller
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

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'read', 'bank_accounts'))) {
             if ($request->ajax()) {
                 return response()->json(['status' => false, 'message' => 'You do not have permission to view bank accounts.'], 403);
             }
             abort(403, 'You do not have permission to access bank accounts settings.');
        }

        $boxId = $boxUser->box_id;

        if ($request->ajax()) {
            $query = Bank::where('box_id', $boxId)->with(['company', 'creator', 'updater']);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('company_name', function ($row) {
                    return $row->company->bill_name ?? 'N/A';
                })
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
                                <a class="dropdown-item edit-bank-btn" href="javascript:void(0);"
                                    data-id="' . $row->id . '"
                                    data-name="' . e($row->name) . '"
                                    data-company-id="' . $row->company_id . '"
                                    data-account-no="' . e($row->account_no) . '"
                                    data-ifsc="' . e($row->ifsc) . '"
                                    data-address="' . e($row->address) . '"
                                    data-show-pdf="' . $row->show_pdf . '"
                                    data-status="' . $row->status . '">
                                    <i class="fa-solid fa-pencil text-blue"></i> Edit
                                </a>
                                <a class="dropdown-item delete-bank-btn" href="javascript:void(0);"
                                    data-id="' . $row->id . '">
                                    <i class="fa-regular fa-trash-can text-danger"></i> Delete
                                </a>
                            </div>
                        </div>';
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }

        $companies = Company::where('box_id', $boxId)->get();

        return view('settings.bank-accounts', compact('companies'));
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

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'create', 'bank_accounts'))) {
             return response()->json(['status' => false, 'message' => 'You do not have permission to create bank accounts.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required',
            'account_no' => 'required|string|max:50',
            'ifsc' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        Bank::create([
            'box_id' => $boxUser->box_id,
            'company_id' => $request->company_id,
            'name' => $request->name,
            'account_no' => $request->account_no,
            'ifsc' => $request->ifsc,
            'address' => $request->address,
            'show_pdf' => $request->show_pdf ?? 'No',
            'status' => $request->status ?? 'Enabled',
            'added_by' => $userId,
            'last_updated_by' => $userId,
        ]);

        return response()->json(['status' => true, 'message' => 'Bank account added successfully!']);
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

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'update', 'bank_accounts'))) {
             return response()->json(['status' => false, 'message' => 'You do not have permission to update bank accounts.'], 403);
        }

        $request->validate([
            'id' => 'required|exists:banks,id',
            'name' => 'required|string|max:255',
            'company_id' => 'required',
            'account_no' => 'required|string|max:50',
            'ifsc' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        $bank = Bank::where('id', $request->id)->where('box_id', $boxUser->box_id)->first();
        if (!$bank) {
            return response()->json(['status' => false, 'message' => 'Bank account not found.']);
        }

        $bank->update([
            'company_id' => $request->company_id,
            'name' => $request->name,
            'account_no' => $request->account_no,
            'ifsc' => $request->ifsc,
            'address' => $request->address,
            'show_pdf' => $request->show_pdf ?? 'No',
            'status' => $request->status ?? 'Enabled',
            'last_updated_by' => $userId,
        ]);

        return response()->json(['status' => true, 'message' => 'Bank account updated successfully!']);
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

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'delete', 'bank_accounts'))) {
             return response()->json(['status' => false, 'message' => 'You do not have permission to delete bank accounts.'], 403);
        }

        $bank = Bank::where('id', $request->id)->where('box_id', $boxUser->box_id)->first();
        if (!$bank) {
            return response()->json(['status' => false, 'message' => 'Bank account not found.']);
        }

        $bank->delete();

        return response()->json(['status' => true, 'message' => 'Bank account deleted successfully!']);
    }
}
