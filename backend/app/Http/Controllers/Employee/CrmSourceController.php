<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\ActivityStatus;
use App\Models\ActivityType;
use App\Models\BoxUser;
use App\Models\Call;
use App\Models\ContactStage;
use App\Models\CrmSource;
use App\Models\Industry;
use App\Models\LeadSubStatus;
use App\Models\LostReason;
use App\Models\Lead;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Cache;

class CrmSourceController extends Controller
{
    /* -------------------------------------------------------------------------- */
    /*                                View Methods                                */
    /* -------------------------------------------------------------------------- */

    public function source()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('app_settings', 'read', 'source'))) {
            abort(403, 'You do not have permission to access lead source settings.');
        }

        return view('frontend.employess.crm_setting.source');
    }

    public function lost_reason()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('app_settings', 'read', 'lost_reason'))) {
            abort(403, 'You do not have permission to access lost reason settings.');
        }

        return view('frontend.employess.crm_setting.lost_reason');
    }

    public function contact_stage()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('app_settings', 'read', 'contact_stage'))) {
            abort(403, 'You do not have permission to access lead status settings.');
        }

        $box_id = $boxUser ? $boxUser->box_id : null;
        $approvers = [];
        if ($box_id) {
            $approvers = \App\Models\BoxUser::with(['user', 'departmentRelation', 'designationRelation'])
                ->where('box_id', $box_id)
                ->where('status', 'Active')
                ->get()
                ->map(function ($bu) {
                    $dept = $bu->departmentRelation ? $bu->departmentRelation->name : 'No Dept';
                    $desig = $bu->designationRelation ? $bu->designationRelation->name : 'No Desig';
                    return (object)[
                        'id' => $bu->user_id,
                        'name' => ($bu->user ? $bu->user->name : 'Unknown User') . " ($desig - $dept)"
                    ];
                });
        }

        return view('frontend.employess.crm_setting.contact_stage', compact('approvers'));
    }

    public function industry()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('app_settings', 'read', 'industry'))) {
            abort(403, 'You do not have permission to access industry settings.');
        }

        return view('frontend.employess.crm_setting.industry');
    }

    public function calls()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('app_settings', 'read', 'calls'))) {
            abort(403, 'You do not have permission to access activity settings.');
        }

        return view('frontend.employess.crm_setting.calls');
    }

    public function show()
    {
        return view('frontend.employess.crm_setting.activitytype');
    }

    public function activityStatuus()
    {
        return view('frontend.employess.crm_setting.activitystatus');
    }

    public function CrmLeadsSubStatuus()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $isPrivilegedUser = $isGlobalAdmin || $isBoxAdmin;

        if (!($isPrivilegedUser || \App\Helpers\PermissionHelper::hasModulePermission('leads', 'read', 'leads_sub_status'))) {
            abort(403, 'You do not have permission to access lead sub status settings.');
        }

        $box_id = $this->getAuthenticatedBoxId();
        $contactStages = ContactStage::where('box_id', $box_id)->where('status', 1)->get();
        
        $approvers = [];
        if ($box_id) {
            $approvers = \App\Models\BoxUser::with(['user', 'departmentRelation', 'designationRelation'])
                ->where('box_id', $box_id)
                ->where('status', 'Active')
                ->get()
                ->map(function ($bu) {
                    $dept = $bu->departmentRelation ? $bu->departmentRelation->name : 'No Dept';
                    $desig = $bu->designationRelation ? $bu->designationRelation->name : 'No Desig';
                    return (object)[
                        'id' => $bu->user_id,
                        'name' => ($bu->user ? $bu->user->name : 'Unknown User') . " ($desig - $dept)"
                    ];
                });
        }

        return view('frontend.employess.crm_setting.leadssubstatus', compact('contactStages', 'isPrivilegedUser', 'approvers'));
    }

    /* -------------------------------------------------------------------------- */
    /*                              Data Fetch Methods                            */
    /* -------------------------------------------------------------------------- */

    /**
     * Get Activities List for Dropdowns
     */
    public function getActivitiesList(Request $request): JsonResponse
    {
        try {
            $box_id = $this->getAuthenticatedBoxId();

            $data = Call::select('id', 'name as title')
                ->where('box_id', $box_id)
                ->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching activities list: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch activities list.',
            ], 500);
        }
    }

    public function get_source(Request $request)
    {
        try {
            $box_id = $this->getAuthenticatedBoxId();
            $query = CrmSource::select('id', 'name as title', 'created_at', 'status')
                ->where('box_id', $box_id)
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->filterColumn('title', fn($query, $keyword) => $query->where('name', 'like', "%{$keyword}%"))
                ->addColumn('created_date', fn($item) => $item->created_at->format('d M Y, h:i A'))
                ->addColumn('status_badge', fn($item) => $item->status == 1
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>')
                ->addColumn('actions', function ($item) {
                    return '
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="dropdown table-action">
                            <a href="#" class="action-icon" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item edit-source-btn" href="#"
                                    data-id="' . $item->id . '"
                                    data-name="' . e($item->title) . '"
                                    data-status="' . $item->status . '"
                                    data-bs-toggle="modal"
                                    data-bs-target="#edit_source">
                                    <i class="ti ti-edit text-blue"></i> Edit
                                </a>
                                <a class="dropdown-item delete-source-btn" href="#"
                                    data-id="' . $item->id . '"
                                    data-bs-toggle="modal"
                                    data-bs-target="#delete_source">
                                    <i class="ti ti-trash text-danger"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>';
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error fetching sources: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch sources.');
        }
    }

    public function get_lostreason(Request $request)
    {
        try {
            $box_id = $this->getAuthenticatedBoxId();
            $query = LostReason::select('id', 'name as title', 'created_at', 'status')
                ->where('box_id', $box_id)
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->filter(function ($query) use ($request) {
                    if (!empty($request->search['value'])) {
                        $search = strtolower($request->search['value']);
                        $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    }
                })
                ->addColumn('created_date', fn($item) => $item->created_at->format('d M Y, h:i A'))
                ->addColumn('status_badge', fn($item) => $item->status == 1
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>')
                ->addColumn('actions', function ($item) {
                    return '
                    <div class="dropdown table-action">
                        <a href="#" class="action-icon" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item edit_reason-btn" href="#"
                                data-id="' . $item->id . '"
                                data-name="' . e($item->title) . '"
                                data-status="' . $item->status . '"
                                data-bs-toggle="modal"
                                data-bs-target="#edit_reason">
                                <i class="ti ti-edit text-blue"></i> Edit
                            </a>
                            <a class="dropdown-item delete_reason-btn" href="#"
                                data-id="' . $item->id . '"
                                data-bs-toggle="modal"
                                data-bs-target="#delete_reason">
                                <i class="ti ti-trash text-danger"></i> Delete
                            </a>
                        </div>
                    </div>';
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error fetching lost reasons: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch lost reasons.');
        }
    }

    public function get_industry(Request $request)
    {
        try {
            $box_id = $this->getAuthenticatedBoxId();
            $query = Industry::select('id', 'name as title', 'created_at', 'status')
                ->where('box_id', $box_id)
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->filter(function ($query) use ($request) {
                    if (!empty($request->search['value'])) {
                        $searchTerm = strtolower($request->search['value']);
                        $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"]);
                    }
                })
                ->editColumn('created_at', fn($item) => $item->created_at->format('d M Y, h:i a'))
                ->editColumn('status', fn($item) => $item->status == 1 ? 'Active' : 'Inactive')
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error fetching industry data: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch industries.');
        }
    }

    public function get_contact_stage(Request $request)
    {
        try {
            $box_id = $this->getAuthenticatedBoxId();
            $query = ContactStage::select(
                'id',
                'name',
                'created_at',
                'status',
                'is_task_priority',
                'task_priority',
                'color',
                'reminder_mandatory',
                'notification',
                'priority',
                'is_default',
                'is_final',
                'display_amount_field',
                'no_activity_alert',
                'no_activity_alert_unit',
                'requires_approval',
                'approval_department_id',
                'approval_designation_id'
            )
                ->with(['approvalDepartment:id,name', 'approvalDesignation:id,name'])
                ->where('box_id', $box_id)
                ->orderBy('id', 'desc');

            return DataTables::of($query)
                ->filter(function ($query) use ($request) {
                    if (!empty($request->search['value'])) {
                        $search = strtolower($request->search['value']);
                        $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    }
                })
                ->order(function ($query) use ($request) {
                    if ($request->order) {
                        $orderColIndex = $request->order[0]['column'];
                        $orderDir = $request->order[0]['dir'];
                        $columns = ['id', 'name', 'created_at', 'status'];
                        $orderCol = $columns[$orderColIndex] ?? 'created_at';
                        $query->orderBy($orderCol, $orderDir);
                    }
                })
                ->addColumn('requires_approval_badge', function ($item) {
                    return $item->requires_approval 
                        ? '<span class="badge bg-warning">Yes</span>' 
                        : '<span class="badge bg-secondary">No</span>';
                })
                ->addColumn('department_name', function ($item) {
                    return $item->requires_approval && $item->approvalDepartment ? $item->approvalDepartment->name : '-';
                })
                ->addColumn('designation_name', function ($item) {
                    return $item->requires_approval && $item->approvalDesignation ? $item->approvalDesignation->name : '-';
                })
                ->rawColumns(['requires_approval_badge'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error fetching contact stages: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch contact stages.');
        }
    }

    public function get_calls(Request $request)
    {
        try {
            $box_id = $this->getAuthenticatedBoxId();
            $query = Call::select('id', 'name as title', 'created_at', 'activity_status', 'status')
                ->where('box_id', $box_id)
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->filterColumn('title', function ($query, $keyword) {
                    $query->whereRaw("LOWER(name) LIKE ?", ["%" . strtolower($keyword) . "%"]);
                })
                ->editColumn('created_at', fn($row) => $row->created_at->format('d M Y, h:i a'))
                ->addColumn('action', function ($row) {
                    $activityTypeUrl = route('activity-type.show', ['box_slug' => request()->route('box_slug')]) . '?activity_id=' . $row->id;
                    return '
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="dropdown table-action">
                            <a href="#" class="action-icon" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item edit_calls-btn" href="#"
                                    data-id="' . $row->id . '"
                                    data-title="' . $row->title . '"
                                    data-status="' . $row->status . '"
                                    data-bs-toggle="modal"
                                    data-bs-target="#edit_calls">
                                    <i class="ti ti-edit text-blue"></i> Edit
                                </a>
                                <a class="dropdown-item delete_reason-btn" href="#"
                                    data-id="' . $row->id . '"
                                    data-bs-toggle="modal"
                                    data-bs-target="#delete_calls">
                                    <i class="ti ti-trash text-danger"></i> Delete
                                </a>
                            </div>
                            <a href="' . $activityTypeUrl . '" class="action-icon" title="View Activity Type">
                                <i style="color:#ef990c;" class="fa fa-filter" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error fetching call reasons: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch call reasons: ' . $e->getMessage()], 500);
        }
    }

    public function GetActivityTypes(Request $request): JsonResponse
    {
        try {
            $id = $request->input('activity_setting_id');
            $box_id = $this->getAuthenticatedBoxId();

            $query = ActivityType::select('activity_types.id', 'activity_types.activity_type as title', 'activity_types.created_at', 'activity_types.status', 'activity_types.activity_setting_id', 'activity_types.target_type', 'calls.name as setting_name')
                ->leftJoin('calls', 'calls.id', '=', 'activity_types.activity_setting_id')
                ->where('activity_types.box_id', $box_id)
                ->when($id, function($q) use ($id) {
                    return $q->where('activity_types.activity_setting_id', $id);
                })
                ->orderBy('activity_types.created_at', 'desc');

            if (!empty($request->search)) {
                $query->whereRaw('LOWER(activity_types.activity_type) LIKE ?', ['%' . strtolower($request->search) . '%']);
            }

            $results = $query->get()->map(function ($item) {
                $item->created_date = $item->created_at->format('d M Y, h:i a');
                return $item;
            });

            return response()->json(['success' => true, 'status' => 200, 'data' => $results]);
        } catch (\Throwable $th) {
            return $this->errorResponse('Something went wrong: ' . $th->getMessage());
        }
    }

    public function getActivitiesByTarget(Request $request): JsonResponse
    {
        $box_id = $this->getAuthenticatedBoxId();
        
        // Defaults to 'Call' and 'customers' if not provided
        $settingName = $request->input('setting_name', 'Call');
        $targetType = $request->input('target_type', 'customers');

        $callSetting = \DB::table('calls')
            ->where('box_id', $box_id)
            ->where('name', $settingName)
            ->first();

        if (!$callSetting) return response()->json(['success' => false, 'data' => []]);

        $types = \DB::table('activity_types')
            ->where('box_id', $box_id)
            ->where('activity_setting_id', $callSetting->id)
            ->whereIn('target_type', [$targetType, 'both'])
            ->where('status', 1)
            ->get();
        
        return response()->json(['success' => true, 'data' => $types]);
    }

    public function getActivtiyStatus(Request $request): JsonResponse
    {
        try {
            $box_id = $this->getAuthenticatedBoxId();

            $query = ActivityStatus::select(
                    'activity_statuses.id', 
                    'activity_statuses.activity_status as title', 
                    'activity_statuses.created_at', 
                    'activity_statuses.status', 
                    'activity_statuses.activity_type_id', 
                    'activity_statuses.activity_setting_id',
                    'calls.name as setting_name',
                    'activity_types.activity_type as type_name'
                )
                ->leftJoin('calls', 'calls.id', '=', 'activity_statuses.activity_setting_id')
                ->leftJoin('activity_types', 'activity_types.id', '=', 'activity_statuses.activity_type_id')
                ->where('activity_statuses.box_id', $box_id)
                ->orderBy('activity_statuses.created_at', 'desc');

            if (!empty($request->activity_type_id)) {
                $query->where('activity_statuses.activity_type_id', $request->activity_type_id);
            }
            if (!empty($request->activity_setting_id)) {
                $query->where('activity_statuses.activity_setting_id', $request->activity_setting_id);
            }
            if (!empty($request->search)) {
                $query->whereRaw('LOWER(activity_statuses.activity_status) LIKE ?', ['%' . strtolower($request->search) . '%']);
            }

            $results = $query->get()->map(function ($item) {
                $item->created_date = optional($item->created_at)->format('d M Y, h:i a');
                return $item;
            });

            return response()->json(['success' => true, 'status' => 200, 'data' => $results]);
        } catch (\Throwable $th) {
            Log::error('Error in getActivtiyStatus: ' . $th->getMessage());
            return $this->errorResponse('Server error: ' . $th->getMessage());
        }
    }

    public function getLeadSubStatus(Request $request)
    {
        try {
            $permissionDetails = $this->checkPermission('lead_sub_status', 'read');

            if (!$permissionDetails['allowed']) {
                return $this->errorResponse($permissionDetails['message'], 403);
            }

            $box_id = $this->getAuthenticatedBoxId();
            $leadStatusId = $request->input('lead_status_id');

            $query = LeadSubStatus::query()
                ->select(
                    'lead_sub_status.id', 
                    'lead_sub_status.name', 
                    'lead_sub_status.status', 
                    'lead_sub_status.created_at', 
                    'contact_stage.name as parent_status', 
                    'lead_sub_status.leads_status_id',
                    'lead_sub_status.requires_approval',
                    'lead_sub_status.approval_department_id',
                    'lead_sub_status.approval_designation_id'
                )
                ->with(['approvalDepartment:id,name', 'approvalDesignation:id,name'])
                ->leftJoin('contact_stage', 'contact_stage.id', '=', 'lead_sub_status.leads_status_id')
                ->where('lead_sub_status.box_id', $box_id)
                ->orderBy('lead_sub_status.id', 'desc');

            if (!empty($leadStatusId) && $leadStatusId > 0) {
                $query->where('leads_status_id', $leadStatusId);
            }

            return DataTables::eloquent($query)
                ->filter(function ($query) use ($request) {
                    if (!empty($request->search['value'])) {
                        $search = strtolower($request->search['value']);
                        $query->whereRaw('LOWER(lead_sub_status.name) LIKE ?', ["%{$search}%"]);
                    }
                })
                ->addColumn('requires_approval_badge', function ($item) {
                    return $item->requires_approval 
                        ? '<span class="badge bg-warning">Yes</span>' 
                        : '<span class="badge bg-secondary">No</span>';
                })
                ->addColumn('department_name', function ($item) {
                    return $item->requires_approval && $item->approvalDepartment ? $item->approvalDepartment->name : '-';
                })
                ->addColumn('designation_name', function ($item) {
                    return $item->requires_approval && $item->approvalDesignation ? $item->approvalDesignation->name : '-';
                })
                ->rawColumns(['requires_approval_badge'])
                ->make(true);

        } catch (\Throwable $th) {
            return $this->errorResponse('Something went wrong: ' . $th->getMessage());
        }
    }

    /* -------------------------------------------------------------------------- */
    /*                         Generic Unified CRUD Methods                       */
    /* -------------------------------------------------------------------------- */

    public function store_source(Request $request): JsonResponse
    {
        $permissionDetails = $this->checkPermission($request->type, 'create');
        if (!$permissionDetails['allowed']) {
            return $this->errorResponse($permissionDetails['message'], 403);
        }

        // Support bulk creation if 'names' array is present, otherwise single 'name'
        if ($request->has('names') && is_array($request->names)) {
            $validator = Validator::make($request->all(), [
                'type' => 'required|string',
                'names' => 'required|array',
                'names.*' => 'required|string|distinct|max:255',
                'status' => 'required|boolean',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'type' => 'required|string',
                'name' => 'required|string|max:255',
                'status' => 'required|boolean',
                'is_task_priority' => 'nullable|integer',
                'task_priority' => 'nullable',
                'color' => 'nullable|string',
                'reminder_mandatory' => 'nullable|string',
                'notification' => 'nullable|string',
                'priority' => 'nullable|integer',
                'is_default' => 'nullable|string',
                'is_final' => 'nullable|string',
                'display_amount_field' => 'nullable|string',
                'no_activity_alert' => 'nullable|integer',
                'no_activity_alert_unit' => 'nullable|string',
                'requires_approval' => 'nullable|boolean',
                'approval_department_id' => 'nullable|integer',
                'approval_designation_id' => 'nullable|integer',
            ]);
        }

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        $modelClass = $this->getModelByType($request->type);
        if (!$modelClass) {
            return $this->errorResponse('Invalid type.', 400);
        }

        $box_id = $this->getAuthenticatedBoxId();
        $user_id = Auth::id();
        $typeLabel = $this->getTypeLabel($request->type);

        $names = $request->has('names') ? $request->names : [$request->name];
        $createdItems = [];
        $failedItems = [];

        foreach ($names as $name) {
            // Check existence
            $exists = $modelClass::where('box_id', $box_id)
                ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                ->whereNull('deleted_at')
                ->exists();

            if ($exists) {
                $failedItems[] = $name;
                continue;
            }

            $data = [
                'box_id' => $box_id,
                'user_id' => $user_id,
                'name' => $name,
                'status' => $request->status,
                'created_by' => $user_id,
                'is_task_priority' => $request->is_task_priority ?? 0,
                'task_priority' => $request->task_priority,
                'color' => $request->color,
                'reminder_mandatory' => $request->reminder_mandatory ?? 'No',
                'notification' => $request->notification ?? 'No',
                'priority' => $request->priority ?? 0,
                'is_default' => $request->is_default ?? 'No',
                'is_final' => $request->is_final ?? 'No',
                'display_amount_field' => $request->display_amount_field ?? 'No',
                'no_activity_alert' => $request->no_activity_alert ?? 0,
                'no_activity_alert_unit' => $request->no_activity_alert_unit ?? 'Days',
            ];

            if (in_array($request->type, ['contact_stage', 'lead_sub_status'])) {
                $data['requires_approval'] = $request->boolean('requires_approval');
                $data['approval_department_id'] = $request->approval_department_id;
                $data['approval_designation_id'] = $request->approval_designation_id;
            }

            $createdItems[] = $modelClass::create($data);
        }

        if (count($createdItems) === 0 && count($failedItems) > 0) {
            return $this->errorResponse("All provided items already exist.", 409);
        }

        return response()->json([
            'success' => true,
            'message' => count($createdItems) . " $typeLabel(s) created successfully.",
            'data' => count($createdItems) === 1 ? $createdItems[0] : $createdItems, // Return single object if single created for compat
            'failed' => $failedItems
        ], 201);


    }

    public function update(Request $request): JsonResponse
    {
        $permissionDetails = $this->checkPermission($request->type, 'update');
        if (!$permissionDetails['allowed']) {
            return $this->errorResponse($permissionDetails['message'], 403);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'type' => 'required|string',
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'is_task_priority' => 'nullable|integer',
            'task_priority' => 'nullable',
            'color' => 'nullable|string',
            'reminder_mandatory' => 'nullable|string',
            'notification' => 'nullable|string',
            'priority' => 'nullable|integer',
            'is_default' => 'nullable|string',
            'is_final' => 'nullable|string',
            'display_amount_field' => 'nullable|string',
            'no_activity_alert' => 'nullable|integer',
            'no_activity_alert_unit' => 'nullable|string',
            'requires_approval' => 'nullable|boolean',
            'approval_department_id' => 'nullable|integer',
            'approval_designation_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        $modelClass = $this->getModelByType($request->type);
        if (!$modelClass) {
            return $this->errorResponse('Invalid type.', 400);
        }

        try {
            $item = $modelClass::findOrFail($request->id);
            $typeLabel = $this->getTypeLabel($request->type);

            // Duplicate check (excluding self)
            $exists = $modelClass::where('box_id', $item->box_id)
                ->whereRaw('LOWER(name) = ?', [strtolower($request->name)])
                ->where('id', '!=', $request->id)
                ->whereNull('deleted_at')
                ->exists();

            if ($exists) {
                return $this->errorResponse("$typeLabel name conflicts with existing records.", 409);
            }

            $updateData = [
                'name' => $request->name,
                'status' => $request->status,
                'is_task_priority' => $request->is_task_priority ?? 0,
                'task_priority' => $request->task_priority,
                'color' => $request->color,
                'reminder_mandatory' => $request->reminder_mandatory ?? 'No',
                'notification' => $request->notification ?? 'No',
                'priority' => $request->priority ?? 0,
                'is_default' => $request->is_default ?? 'No',
                'is_final' => $request->is_final ?? 'No',
                'display_amount_field' => $request->display_amount_field ?? 'No',
                'no_activity_alert' => $request->no_activity_alert ?? 0,
                'no_activity_alert_unit' => $request->no_activity_alert_unit ?? 'Days',
                'updated_by' => Auth::id(),
            ];

            if (in_array($request->type, ['contact_stage', 'lead_sub_status'])) {
                $updateData['requires_approval'] = $request->boolean('requires_approval');
                $updateData['approval_department_id'] = $request->approval_department_id;
                $updateData['approval_designation_id'] = $request->approval_designation_id;
            }

            $item->update($updateData);

            return response()->json(['success' => true, 'message' => "$typeLabel updated successfully."]);

        } catch (\Exception $e) {
            return $this->errorResponse("Update failed: " . $e->getMessage());
        }


    }

    public function delete(Request $request): JsonResponse
    {
        $permissionDetails = $this->checkPermission($request->type, 'delete');
        if (!$permissionDetails['allowed']) {
            return $this->errorResponse($permissionDetails['message'], 403);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'type' => 'required|string|in:source,lost_reason,contact_stage,industry,call_reason',
            'force' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        $modelClass = $this->getModelByType($request->type);
        if (!$modelClass) {
            return $this->errorResponse('Invalid type.', 400);
        }

        try {
            $item = $modelClass::findOrFail($request->id);
            $typeLabel = $this->getTypeLabel($request->type);
            $isForce = $request->boolean('force', false);

            if (!$isForce && $this->checkUsage($request->type, $item->id)) {
                return $this->errorResponse("Cannot delete: This {$typeLabel} is currently in use.", 409);
            }

            if ($isForce) {
                $this->deleteMappedData($request->type, $item->id);
            }

            $item->update(['deleted_by' => Auth::id()]);
            $item->delete();

            return response()->json(['success' => true, 'message' => "$typeLabel deleted successfully."]);

        } catch (\Exception $e) {
            return $this->errorResponse("Delete failed: " . $e->getMessage());
        }
    }

    private function deleteMappedData(string $type, int $id): void
    {
        try {
            switch ($type) {
                case 'call_reason': // Activity Settings (Call)
                    $typeIds = \App\Models\ActivityType::where('activity_setting_id', $id)->pluck('id');
                    if ($typeIds->isNotEmpty()) {
                        \App\Models\Activity::whereIn('activity_type', $typeIds)->delete();
                    }
                    $statusIds = \App\Models\ActivityStatus::where('activity_setting_id', $id)->pluck('id');
                    if ($statusIds->isNotEmpty()) {
                        \App\Models\Activity::whereIn('activity_status_id', $statusIds)->delete();
                    }
                    \App\Models\Activity::where('activity_setting_id', $id)->delete();
                    
                    \App\Models\ActivityType::where('activity_setting_id', $id)->delete();
                    \App\Models\ActivityStatus::where('activity_setting_id', $id)->delete();
                    break;
                case 'activity_type':
                    \App\Models\Activity::where('activity_type', (string) $id)->delete();
                    break;
                case 'activity_status':
                    \App\Models\Activity::where('activity_status_id', $id)->delete();
                    break;
            }
        } catch (\Exception $e) {
            Log::error("Force delete mapped data error for $type: " . $e->getMessage());
        }


    }

    public function toggleStatus(Request $request): JsonResponse
    {
        $permissionDetails = $this->checkPermission('source', 'update');
        if (!$permissionDetails['allowed']) {
            return $this->errorResponse($permissionDetails['message'], 403);
        }

        try {
            $source = CrmSource::findOrFail($request->id);
            $source->status = $request->status ? 1 : 0;
            $source->save();
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Status updated successfully.']);
        } catch (\Exception $e) {
            Log::error('Status toggle error: ' . $e->getMessage());
            return $this->errorResponse('Failed to update status.', 500);
        }
    }

    /* -------------------------------------------------------------------------- */
    /*                         Specific CRUD Methods                              */
    /* -------------------------------------------------------------------------- */

    public function storeActivityType(Request $request): JsonResponse
    {
        $permissionDetails = $this->checkPermission('activity_type', 'create');
        if (!$permissionDetails['allowed']) {
            return $this->errorResponse($permissionDetails['message'], 403);
        }

        // Check for bulk input
        if ($request->has('titles') && is_array($request->titles)) {
            $validated = $request->validate([
                'titles' => 'required|array',
                'titles.*' => 'required|string|max:255',
                'activity_setting_ids' => 'required|array',
                'activity_setting_ids.*' => 'integer',
                'status' => 'required|in:0,1',
                'target_type' => 'nullable|in:leads,customers,both',
            ]);
        } else {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'activity_setting_id' => 'required|integer',
                'status' => 'required|in:0,1',
                'target_type' => 'nullable|in:leads,customers,both',
            ]);
        }

        $box_id = $this->getAuthenticatedBoxId();

        $titles = $request->has('titles') ? $request->titles : [$request->title];
        $settingIds = $request->has('activity_setting_ids') ? $request->activity_setting_ids : [$request->activity_setting_id];

        $created = [];
        $failed = 0;

        foreach ($settingIds as $settingId) {
            foreach ($titles as $title) {
                // Check duplicate per setting
                $exists = ActivityType::where('box_id', $box_id)
                    ->where('activity_setting_id', $settingId)
                    ->whereRaw('LOWER(activity_type) = ?', [strtolower($title)])
                    ->whereNull('deleted_at')
                    ->exists();

                if ($exists) {
                    $failed++;
                    continue;
                }

                $created[] = ActivityType::create([
                    'box_id' => $box_id,
                    'activity_type' => $title,
                    'activity_setting_id' => $settingId,
                    'status' => $validated['status'],
                    'target_type' => $validated['target_type'] ?? 'both',
                ]);
            }
        }

        if (count($created) === 0 && $failed > 0) {
            return $this->errorResponse("All provided Activity Types already exist for the selected Activities.", 409);
        }

        return response()->json([
            'success' => true,
            'message' => count($created) . " Type(s) added successfully.",
            'data' => count($created) > 0 ? $created[count($created) - 1] : null // return last one for selection logic
        ]);
    }

    public function storeActivitStatus(Request $request): JsonResponse
    {
        $permissionDetails = $this->checkPermission('activity_status', 'create');
        if (!$permissionDetails['allowed']) {
            return $this->errorResponse($permissionDetails['message'], 403);
        }

        // Check for bulk input
        if ($request->has('titles') && is_array($request->titles)) {
            $validated = $request->validate([
                'titles' => 'required|array',
                'titles.*' => 'required|string|max:255',
                'activity_type_ids' => 'required|array',
                'activity_type_ids.*' => 'integer',
                'status' => 'required|in:0,1',
            ]);
        } else {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'activity_type_id' => 'required|integer',
                'status' => 'required|in:0,1',
            ]);
        }

        $box_id = $this->getAuthenticatedBoxId();

        $titles = $request->has('titles') ? $request->titles : [$request->title];
        $typeIds = $request->has('activity_type_ids') ? $request->activity_type_ids : [$request->activity_type_id];

        $created = [];
        $failed = 0;

        foreach ($typeIds as $typeId) {
            $findActivityType = ActivityType::where('id', $typeId)->first();
            if (!$findActivityType)
                continue;

            foreach ($titles as $title) {
                // Check duplicate
                $exists = ActivityStatus::where('box_id', $box_id)
                    ->where('activity_type_id', $typeId)
                    ->whereRaw('LOWER(activity_status) = ?', [strtolower($title)])
                    ->whereNull('deleted_at')
                    ->exists();

                if ($exists) {
                    $failed++;
                    continue;
                }

                $created[] = ActivityStatus::create([
                    'box_id' => $box_id,
                    'activity_status' => $title,
                    'activity_type_id' => $typeId,
                    'activity_setting_id' => $findActivityType->activity_setting_id,
                    'status' => $validated['status'],
                ]);
            }
        }

        if (count($created) === 0 && $failed > 0) {
            return $this->errorResponse("All provided Statuses already exist for the selected Types.", 409);
        }

        return response()->json([
            'success' => true,
            'message' => count($created) . " Status(es) added successfully.",
            'data' => count($created) > 0 ? $created[count($created) - 1] : null
        ]);
    }

    public function StoreLeadsubstatus(Request $request): JsonResponse
    {
        $permissionDetails = $this->checkPermission('lead_sub_status', 'create');
        if (!$permissionDetails['allowed']) {
            return $this->errorResponse($permissionDetails['message'], 403);
        }

        if ($request->has('titles') && is_array($request->titles)) {
            $validated = $request->validate([
                'titles' => 'required|array',
                'titles.*' => 'required|string|max:255',
                'leads_status_ids' => 'required|array',
                'leads_status_ids.*' => 'integer',
                'status' => 'required|in:0,1',
                'requires_approval' => 'nullable|boolean',
                'approval_department_id' => 'nullable|integer',
                'approval_designation_id' => 'nullable|integer',
            ]);
        } else {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'leads_status_id' => 'required|integer',
                'status' => 'required|in:0,1',
                'requires_approval' => 'nullable|boolean',
                'approval_department_id' => 'nullable|integer',
                'approval_designation_id' => 'nullable|integer',
            ]);
        }

        $box_id = $this->getAuthenticatedBoxId();

        $titles = $request->has('titles') ? $request->titles : [$request->title];
        $statusIds = $request->has('leads_status_ids') ? $request->leads_status_ids : [$request->leads_status_id];

        $created = [];

        foreach ($statusIds as $statusId) {
            foreach ($titles as $title) {
                // Check for duplicate
                $exists = LeadSubStatus::where('box_id', $box_id)
                    ->where('leads_status_id', $statusId)
                    ->whereRaw('LOWER(name) = ?', [strtolower($title)])
                    ->whereNull('deleted_at')
                    ->exists();

                if ($exists) {
                    continue; // Skip duplicates
                }

                $created[] = LeadSubStatus::create([
                    'box_id' => $box_id,
                    'name' => $title,
                    'leads_status_id' => $statusId,
                    'status' => $validated['status'],
                    'requires_approval' => $request->boolean('requires_approval'),
                    'approval_department_id' => $request->approval_department_id,
                    'approval_designation_id' => $request->approval_designation_id,
                ]);
            }
        }

        if (empty($created) && count($titles) > 0) {
            return $this->errorResponse('Lead Sub Status(es) with these name(s) already exist for the selected Statuses.', 409);
        }

        return response()->json(['success' => true, 'data' => $created, 'message' => count($created) . ' Lead Sub Status(es) created successfully.']);
    }

    public function updateActivitytype(Request $request): JsonResponse
    {
        $permissionDetails = $this->checkPermission('activity_type', 'update');
        if (!$permissionDetails['allowed']) {
            return $this->errorResponse($permissionDetails['message'], 403);
        }

        $validated = $request->validate([
            'id' => 'required|integer',
            'title' => 'required|string|max:255',
            'status' => 'required|in:0,1',
            'target_type' => 'nullable|in:leads,customers,both',
        ]);

        $activity = ActivityType::findOrFail($request->id);
        $activity->activity_type = $validated['title'];
        $activity->status = $validated['status'];
        if (isset($validated['target_type'])) {
            $activity->target_type = $validated['target_type'];
        }
        $activity->save();

        return response()->json(['success' => true, 'message' => 'Activity Type updated successfully.', 'data' => $activity]);
    }

    public function updateActivityStatus(Request $request): JsonResponse
    {
        $permissionDetails = $this->checkPermission('activity_status', 'update');
        if (!$permissionDetails['allowed']) {
            return $this->errorResponse($permissionDetails['message'], 403);
        }

        $validated = $request->validate([
            'id' => 'required|integer',
            'title' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ]);

        $activity = ActivityStatus::findOrFail($request->id);
        $activity->activity_status = $validated['title'];
        $activity->status = $validated['status'];
        $activity->save();

        return response()->json(['success' => true, 'message' => 'Activity Status updated successfully.', 'data' => $activity]);
    }

    public function updateLeadSubStatus(Request $request): JsonResponse
    {
        $permissionDetails = $this->checkPermission('lead_sub_status', 'update');
        if (!$permissionDetails['allowed']) {
            return $this->errorResponse($permissionDetails['message'], 403);
        }

        $validated = $request->validate([
            'id' => 'required|integer',
            'title' => 'required|string|max:255',
            'leads_status_id' => 'required|integer',
            'status' => 'required|in:0,1',
            'requires_approval' => 'nullable|boolean',
            'approval_department_id' => 'nullable|integer',
            'approval_designation_id' => 'nullable|integer',
        ]);

        $activity = LeadSubStatus::findOrFail($request->id);
        $activity->name = $validated['title'];
        $activity->leads_status_id = $validated['leads_status_id'];
        $activity->status = $validated['status'];
        $activity->requires_approval = $request->boolean('requires_approval');
        $activity->approval_department_id = $request->approval_department_id;
        $activity->approval_designation_id = $request->approval_designation_id;
        $activity->save();

        return response()->json(['success' => true, 'message' => 'Lead Sub Status updated successfully.', 'data' => $activity]);
    }

    public function destroyActivityTypeActual(Request $request): JsonResponse
    {
        $permissionDetails = $this->checkPermission('activity_type', 'delete');
        if (!$permissionDetails['allowed']) {
            return $this->errorResponse($permissionDetails['message'], 403);
        }

        $id = $request->input('id');
        if ($this->checkUsage('activity_type', (int) $id)) {
            return $this->errorResponse('Cannot delete: This Activity Type is in use.', 409);
        }

        $activity = ActivityType::findOrFail($id);
        $activity->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }

    public function destroyActivityStatus(Request $request): JsonResponse
    {
        $permissionDetails = $this->checkPermission('activity_status', 'delete');
        if (!$permissionDetails['allowed']) {
            return $this->errorResponse($permissionDetails['message'], 403);
        }

        $id = $request->input('id');
        if ($this->checkUsage('activity_status', (int) $id)) {
            return $this->errorResponse('Cannot delete: This Activity Status is in use.', 409);
        }

        $activity = ActivityStatus::findOrFail($id);
        $activity->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }

    public function destroyLeadSubStatys(Request $request): JsonResponse
    {
        $permissionDetails = $this->checkPermission('lead_sub_status', 'delete');
        if (!$permissionDetails['allowed']) {
            return $this->errorResponse($permissionDetails['message'], 403);
        }

        $id = $request->input('id');
        if ($this->checkUsage('lead_sub_status', (int) $id)) {
            return $this->errorResponse('Cannot delete: This Item is in use.', 409);
        }

        $activity = LeadSubStatus::findOrFail($id);
        $activity->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }

    /* -------------------------------------------------------------------------- */
    /*                              Helper Methods                                */
    /* -------------------------------------------------------------------------- */

    public function massDestroy(Request $request): JsonResponse
    {
        $permissionDetails = $this->checkPermission($request->type, 'delete');
        if (!$permissionDetails['allowed']) {
            return $this->errorResponse($permissionDetails['message'], 403);
        }

        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer',
            'type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        $modelClass = $this->getModelByType($request->type);
        if (!$modelClass) {
            return $this->errorResponse('Invalid type.', 400);
        }

        $ids = $request->ids;
        $deletedCount = 0;
        $inUseCount = 0;

        foreach ($ids as $id) {
            try {
                if ($this->checkUsage($request->type, (int) $id)) {
                    $inUseCount++;
                    continue;
                }

                $item = $modelClass::find($id);
                if ($item) {
                    // Check if model uses SoftDeletes or has deleted_by column before updating
                    // All these models seem to have SoftDeletes and deleted_by
                    $item->update(['deleted_by' => Auth::id()]);
                    $item->delete();
                    $deletedCount++;
                }
            } catch (\Exception $e) {
                Log::error("Mass delete error for ID $id: " . $e->getMessage());
            }
        }

        $message = "$deletedCount item(s) deleted successfully.";
        if ($inUseCount > 0) {
            $message .= " $inUseCount item(s) could not be deleted because they are in use.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'deleted_count' => $deletedCount,
            'failed_count' => $inUseCount
        ]);
    }

    private function fetchDataTable($modelClass, Request $request)
    {
        try {
            $box_id = $this->getAuthenticatedBoxId();
            $query = $modelClass::select('id', 'name as title', 'created_at', 'status')
                ->where('box_id', $box_id)
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->filter(function ($query) use ($request) {
                    if (!empty($request->search['value'])) {
                        $search = strtolower($request->search['value']);
                        $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    }
                })
                ->addColumn('created_date', fn($item) => $item->created_at->format('d M Y, h:i A'))
                ->addColumn('status_badge', fn($item) => $item->status == 1
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>')
                ->addColumn('actions', function ($item) use ($request) {
                    $modalEdit = match (true) {
                        $item instanceof CrmSource => '#edit_source',
                        $item instanceof LostReason => '#edit_reason',
                        default => '#edit_modal'
                    };
                    $modalDelete = match (true) {
                        $item instanceof CrmSource => '#delete_source',
                        $item instanceof LostReason => '#delete_reason',
                        default => '#delete_modal'
                    };

                    // Note: Front-end expects specific classes/attributes, keeping close to original logic but dynamic
                    // For safety, I'll return the original hardcoded HTML structure if preferred, OR use a generic definition.
                    // Given the frontend code likely uses specific classes like `edit-source-btn`, I'll use the original block logic
                    // but wrapped in this helper if possible? 
                    // Actually, the original code had distinct action HTML for `get_source` vs `get_lostreason`.
                    // To keep it "production ready" but safe, I will revert to specific methods calling a helper ONLY for query, or keep them separate.
                    // I'll keep them consistent with the View methods.
                    return ''; // The caller handles this or we need custom closure
                })
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error fetching data: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch data.', 500);
        }
    }

    // I decided NO to fully unify `fetchDataTable` due to specific HTML badge/action differences in the original code 
    // that might be relied upon by JS. I will implement them specifically but cleaner.

    private function getAuthenticatedBoxId()
    {
        $user = Auth::id();
        $boxUser = BoxUser::where('user_id', $user)->first();
        if (!$boxUser) {
            throw new \Exception('BoxUser not found for authenticated user.');
        }
        return $boxUser->box_id;
    }

    private function getTypeLabel(string $type): string
    {
        return match ($type) {
            'source' => 'Source',
            'lost_reason' => 'Lost Reason',
            'contact_stage' => 'Contact Stage',
            'industry' => 'Industry',
            'call_reason' => 'Activity',
            'activity_type' => 'Activity Type',
            'activity_status' => 'Activity Status',
            'lead_sub_status' => 'Lead Sub Status',
            default => ucfirst(str_replace('_', ' ', $type)),
        };
    }

    private function getModelByType(string $type): ?string
    {
        return match ($type) {
            'source' => CrmSource::class,
            'lost_reason' => LostReason::class,
            'industry' => Industry::class,
            'contact_stage' => ContactStage::class,
            'call_reason' => Call::class,
            'activity_type' => ActivityType::class,
            'activity_status' => ActivityStatus::class,
            'lead_sub_status' => LeadSubStatus::class,
            default => null,
        };
    }

    private function checkUsage(string $type, int $id): bool
    {
        try {
            return match ($type) {
                'source' => Lead::where('lead_source_id', $id)->exists(),
                'industry' => Lead::where('industry', $id)->exists(),
                'contact_stage' => Lead::where('lead_status_id', $id)
                    ->orWhere('last_lead_status', $id)->exists(),

                'call_reason' => (function () use ($id) {
                        $typeIds = ActivityType::where('activity_setting_id', $id)->pluck('id');
                        if ($typeIds->isNotEmpty() && Activity::whereIn('activity_type', $typeIds)->exists()) {
                            return true;
                        }
                        $statusIds = ActivityStatus::where('activity_setting_id', $id)->pluck('id');
                        if ($statusIds->isNotEmpty() && Activity::whereIn('activity_status_id', $statusIds)->exists()) {
                            return true;
                        }
                        return false;
                    })(),

                'activity_type' => Activity::where('activity_type', (string) $id)->exists(),
                'activity_status' => Activity::where('activity_status_id', $id)->exists(),

                'lead_sub_status' => Schema::hasColumn('leads', 'lead_sub_status_id')
                ? Lead::where('lead_sub_status_id', $id)->exists()
                : false,

                'lost_reason' => Schema::hasColumn('leads', 'lost_reason_id')
                ? Lead::where('lost_reason_id', $id)->exists()
                : false,

                default => false,
            };
        } catch (\Exception $e) {
            Log::error("Dependency check error for $type: " . $e->getMessage());
            return false;
        }
    }

    private function checkPermission(string $type, string $action): array
    {
        $user = Auth::user();
        if ($user->hasRole(['admin', 'superadmin'], 'web')) {
            return ['allowed' => true];
        }

        $boxUser = BoxUser::where('user_id', $user->id)->first();
        if ($boxUser && strtolower($boxUser->staff_role ?? '') === 'admin') {
            return ['allowed' => true];
        }

        $subModule = match ($type) {
            'source' => 'source',
            'lost_reason' => 'lost_reason',
            'contact_stage' => 'contact_stage',
            'industry' => 'industry',
            'call_reason' => 'calls',
            'activity_type' => 'calls',
            'activity_status' => 'calls',
            'lead_sub_status' => 'leads_sub_status',
            default => null,
        };

        if (!$subModule) {
            return ['allowed' => false, 'message' => 'Invalid setting type.'];
        }

        $allowed = \App\Helpers\PermissionHelper::hasModulePermission($subModule === 'leads_sub_status' ? 'leads' : 'app_settings', $action, $subModule);

        return [
            'allowed' => $allowed,
            'message' => $allowed ? '' : "You do not have permission to $action " . $this->getTypeLabel($type) . "."
        ];
    }

    private function errorResponse(string $message, int $code = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }
}
