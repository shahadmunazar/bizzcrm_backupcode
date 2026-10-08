<?php
namespace App\Http\Controllers\Setting;
use App\Http\Controllers\Controller;
use App\Models\BoxUser;
use App\Models\Company;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DivisionController extends Controller
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

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'read', 'divisions'))) {
             if ($request->ajax()) {
                 return response()->json(['status' => false, 'message' => 'You do not have permission to view divisions.'], 403);
             }
             abort(403, 'You do not have permission to access divisions settings.');
        }

        $boxId = $boxUser->box_id;

        if ($request->ajax()) {
            $query = Division::where('box_id', $boxId)->with(['company', 'creator']);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('company_name', function ($row) {
                    return $row->company->bill_name ?? 'N/A';
                })
                ->addColumn('added_by', function ($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('status_badge', function ($row) {
                    $class = $row->status == 'Enable' ? 'bg-success' : 'bg-danger';
                    return '<span class="badge ' . $class . '">' . $row->status . '</span>';
                })
                ->addColumn('actions', function ($row) {
                    // Using .attr() compatible data attributes as requested/fixed previously
                    return '
                        <div class="dropdown table-action">
                            <a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item edit-division-btn" href="javascript:void(0);"
                                    data-id="' . $row->id . '"
                                    data-name="' . e($row->name) . '"
                                    data-company-id="' . $row->company_id . '"
                                    data-auto-map-location="' . $row->auto_map_location . '"
                                    data-limit-member="' . $row->limit_member . '"
                                    data-max-members="' . $row->max_members . '"
                                    data-status="' . $row->status . '">
                                    <i class="fa-solid fa-pencil text-blue"></i> Edit
                                </a>
                                <a class="dropdown-item delete-division-btn" href="javascript:void(0);"
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

        return view('settings.divisions', compact('companies'));
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

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'create', 'divisions'))) {
             return response()->json(['status' => false, 'message' => 'You do not have permission to create divisions.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'max_members' => 'nullable|integer|min:0',
        ]);

        Division::create([
            'box_id' => $boxUser->box_id,
            'company_id' => $request->company_id,
            'name' => $request->name,
            'auto_map_location' => $request->has('auto_map_location') ? 1 : 0, // checkbox handling or select
            'limit_member' => $request->has('limit_member') ? 1 : 0,
            'max_members' => $request->max_members ?? 0,
            'status' => $request->status ?? 'Enable',
            'created_by' => $userId, // Using created_by for relation consistency
            'added_by' => $userId,   // Keeping for existing structure if needed
        ]);

        return response()->json(['status' => true, 'message' => 'Division added successfully!']);
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

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'update', 'divisions'))) {
             return response()->json(['status' => false, 'message' => 'You do not have permission to update divisions.'], 403);
        }

        $request->validate([
            'id' => 'required|exists:divisions,id',
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'max_members' => 'nullable|integer|min:0',
        ]);

        $division = Division::where('id', $request->id)->where('box_id', $boxUser->box_id)->first();
        if (!$division) {
            return response()->json(['status' => false, 'message' => 'Division not found.']);
        }

        $division->update([
            'company_id' => $request->company_id,
            'name' => $request->name,
            'auto_map_location' => $request->has('auto_map_location') ? 1 : 0,
            'limit_member' => $request->has('limit_member') ? 1 : 0,
            'max_members' => $request->max_members ?? 0,
            'status' => $request->status ?? 'Enable',
            'updated_by' => $userId,
        ]);

        return response()->json(['status' => true, 'message' => 'Division updated successfully!']);
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

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'delete', 'divisions'))) {
             return response()->json(['status' => false, 'message' => 'You do not have permission to delete divisions.'], 403);
        }

        $division = Division::where('id', $request->id)->where('box_id', $boxUser->box_id)->first();
        if (!$division) {
            return response()->json(['status' => false, 'message' => 'Division not found.']);
        }

        $division->update(['deleted_by' => $userId]);
        $division->delete();

        return response()->json(['status' => true, 'message' => 'Division deleted successfully!']);
    }
}
