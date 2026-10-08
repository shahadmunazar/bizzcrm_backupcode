<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\BoxUser;
use App\Models\ProjectCategory;
use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ProjectSettingsController extends Controller
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

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'read', 'projects'))) {
            return false;
        }

        return $boxUser->box_id;
    }

    // --- PROJECT TYPES ---

    public function types(Request $request)
    {
        $boxId = $this->checkPermission($request);
        if ($boxId === false) {
             abort(403, 'You do not have permission to access project settings.');
        }

        if ($request->ajax()) {
            $query = ProjectType::where('box_id', $boxId)->with(['creator', 'updater']);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('created_by', function ($row) { return $row->creator ? $row->creator->name : 'N/A'; })
                ->addColumn('updated_by', function ($row) { return $row->updater ? $row->updater->name : 'N/A'; })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center"><a href="javascript:void(0);" class="btn btn-primary btn-sm edit-type-btn me-2" data-id="' . $row->id . '" data-name="' . $row->name . '" data-status="' . $row->status . '"><i class="ti ti-edit"></i> Edit</a>';
                    $btn .= '<a href="javascript:void(0);" class="btn btn-danger btn-sm delete-type-btn" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a></div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('settings.project_types');
    }

    public function storeType(Request $request)
    {
        $boxId = $this->checkPermission($request);
        if ($boxId === false) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('project_types')->where(function ($query) use ($boxId) {
                    return $query->where('box_id', $boxId)->whereNull('deleted_at');
                }),
            ],
            'status' => 'required|boolean',
        ]);

        ProjectType::create([
            'box_id' => $boxId,
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return response()->json(['status' => true, 'message' => 'Project Type created successfully.']);
    }

    public function updateType(Request $request)
    {
        $boxId = $this->checkPermission($request);
        if ($boxId === false) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'id' => 'required|exists:project_types,id',
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('project_types')->where(function ($query) use ($boxId) {
                    return $query->where('box_id', $boxId)->whereNull('deleted_at');
                })->ignore($request->id),
            ],
            'status' => 'required|boolean',
        ]);

        $type = ProjectType::where('box_id', $boxId)->findOrFail($request->id);
        $type->update([
            'name' => $request->name,
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['status' => true, 'message' => 'Project Type updated successfully.']);
    }

    public function destroyType(Request $request)
    {
        $boxId = $this->checkPermission($request);
        if ($boxId === false) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'id' => 'required|exists:project_types,id',
        ]);

        $type = ProjectType::where('box_id', $boxId)->findOrFail($request->id);
        $type->update(['deleted_by' => Auth::id()]);
        $type->delete();

        return response()->json(['status' => true, 'message' => 'Project Type deleted successfully.']);
    }

    // --- PROJECT CATEGORIES ---

    public function categories(Request $request)
    {
        $boxId = $this->checkPermission($request);
        if ($boxId === false) {
             abort(403, 'You do not have permission to access project settings.');
        }

        if ($request->ajax()) {
            $query = ProjectCategory::where('box_id', $boxId)->with(['creator', 'updater']);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('created_by', function ($row) { return $row->creator ? $row->creator->name : 'N/A'; })
                ->addColumn('updated_by', function ($row) { return $row->updater ? $row->updater->name : 'N/A'; })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center"><a href="javascript:void(0);" class="btn btn-primary btn-sm edit-category-btn me-2" data-id="' . $row->id . '" data-name="' . $row->name . '" data-status="' . $row->status . '"><i class="ti ti-edit"></i> Edit</a>';
                    $btn .= '<a href="javascript:void(0);" class="btn btn-danger btn-sm delete-category-btn" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a></div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('settings.project_categories');
    }

    public function storeCategory(Request $request)
    {
        $boxId = $this->checkPermission($request);
        if ($boxId === false) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('project_categories')->where(function ($query) use ($boxId) {
                    return $query->where('box_id', $boxId)->whereNull('deleted_at');
                }),
            ],
            'status' => 'required|boolean',
        ]);

        ProjectCategory::create([
            'box_id' => $boxId,
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return response()->json(['status' => true, 'message' => 'Project Category created successfully.']);
    }

    public function updateCategory(Request $request)
    {
        $boxId = $this->checkPermission($request);
        if ($boxId === false) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'id' => 'required|exists:project_categories,id',
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('project_categories')->where(function ($query) use ($boxId) {
                    return $query->where('box_id', $boxId)->whereNull('deleted_at');
                })->ignore($request->id),
            ],
            'status' => 'required|boolean',
        ]);

        $category = ProjectCategory::where('box_id', $boxId)->findOrFail($request->id);
        $category->update([
            'name' => $request->name,
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['status' => true, 'message' => 'Project Category updated successfully.']);
    }

    public function destroyCategory(Request $request)
    {
        $boxId = $this->checkPermission($request);
        if ($boxId === false) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'id' => 'required|exists:project_categories,id',
        ]);

        $category = ProjectCategory::where('box_id', $boxId)->findOrFail($request->id);
        $category->update(['deleted_by' => Auth::id()]);
        $category->delete();

        return response()->json(['status' => true, 'message' => 'Project Category deleted successfully.']);
    }
}
