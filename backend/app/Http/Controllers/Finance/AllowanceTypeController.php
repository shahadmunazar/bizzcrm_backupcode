<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\AllowanceType;
use App\Models\Box;
use Illuminate\Http\Request;
use App\Services\Finance\AllowanceTypeService;

/**
 * Controller for managing Allowance Types.
 * Handles standard CRUD operations and active status toggling for allowances.
 */
class AllowanceTypeController extends Controller
{
    protected $allowanceTypeService;

    public function __construct(AllowanceTypeService $allowanceTypeService)
    {
        $this->middleware(function ($request, $next) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if (!$user) return $next($request);

            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_allowance_types', 'read'))) {
                abort(403, 'You do not have permission to access this module.');
            }

            return $next($request);
        });

        $this->allowanceTypeService = $allowanceTypeService;
    }

    /**
     * Display the Allowance Types settings page.
     *
     * @param Request $request
     * @param string $box_slug
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->firstOrFail();
        return view('finance.settings.allowance_types', compact('box'));
    }

    /**
     * API Endpoint to fetch all allowance types for the active box.
     *
     * @param Request $request
     * @param string $box_slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->firstOrFail();
        $types = $this->allowanceTypeService->getAll($box);
        return response()->json(['data' => $types]);
    }

    /**
     * API Endpoint to store a new allowance type.
     *
     * @param Request $request
     * @param string $box_slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $box_slug)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_allowance_types', 'create'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to perform this action.'], 403);
        }

        $box = Box::where('slug', $box_slug)->firstOrFail();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'calculation_type' => 'required|string|in:Fixed,Per KM,Per Day',
            'amount' => 'required|numeric|min:0',
            'roles' => 'nullable|array',
            'roles.*' => 'string'
        ]);

        $this->allowanceTypeService->create($box, $validated, auth()->id());

        return response()->json(['success' => true, 'message' => 'Allowance Type created successfully.']);
    }

    /**
     * API Endpoint to update an existing allowance type.
     *
     * @param Request $request
     * @param string $box_slug
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $box_slug, $id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_allowance_types', 'write'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to perform this action.'], 403);
        }

        $box = Box::where('slug', $box_slug)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'calculation_type' => 'required|string|in:Fixed,Per KM,Per Day',
            'amount' => 'required|numeric|min:0',
            'roles' => 'nullable|array',
            'roles.*' => 'string'
        ]);

        $this->allowanceTypeService->update($box, $id, $validated, auth()->id());

        return response()->json(['success' => true, 'message' => 'Allowance Type updated successfully.']);
    }

    /**
     * API Endpoint to permanently delete an allowance type.
     *
     * @param Request $request
     * @param string $box_slug
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $box_slug, $id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_allowance_types', 'delete'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to perform this action.'], 403);
        }

        $box = Box::where('slug', $box_slug)->firstOrFail();
        $this->allowanceTypeService->delete($box, $id);

        return response()->json(['success' => true, 'message' => 'Allowance Type deleted successfully.']);
    }

    /**
     * API Endpoint to toggle the active status of an allowance type.
     *
     * @param Request $request
     * @param string $box_slug
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Request $request, $box_slug, $id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_allowance_types', 'write'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to perform this action.'], 403);
        }

        $box = Box::where('slug', $box_slug)->firstOrFail();
        $this->allowanceTypeService->toggleStatus($box, $id);
        
        return response()->json(['success' => true, 'message' => 'Allowance Type status updated.']);
    }
}
