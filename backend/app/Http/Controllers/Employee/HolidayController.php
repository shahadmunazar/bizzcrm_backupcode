<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\{BoxUser, Holiday, Country};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Validator, DB};
use App\Helpers\PermissionHelper;
use App\Http\Controllers\BaseController;
use App\Services\HolidayService;

class HolidayController extends BaseController
{
    protected $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    public function GetAllRegional(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = PermissionHelper::hasModulePermission('attendance', 'read', 'holiday');
        
        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to access regional data.'
            ], 403);
        }
        
        try {
            $search = $request->input('search');
            $countries = $this->holidayService->getAllRegional($search);

            return response()->json([
                'status' => true,
                'data'   => $countries,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Error fetching countries',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function holiday_update(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canUpdate = PermissionHelper::hasModulePermission('attendance', 'update', 'holiday');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canUpdate) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to update holidays.'
            ], 403);
        }

        try {
            $validator = Validator::make($request->all(), [
                'id'               => 'required|exists:holidays,id',
                'title'            => 'nullable|string|max:255',
                'date'             => 'nullable|date',
                'holiday_code'     => 'nullable|string|max:100',
                'description'      => 'nullable|string',
                'holiday_type'     => 'nullable|string',
                'holiday_type_id'  => 'nullable',
                'status'           => 'nullable|in:Active,Inactive',
                'country_region'   => 'nullable|array',
                'country_region.*' => 'integer|exists:countries,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $holiday = $this->holidayService->updateHoliday($request->id, $request->all(), $user->id);

            if (!$holiday) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Holiday not found.',
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Holiday updated successfully.',
                'data'    => $holiday,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'An error occurred.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function holiday_post(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canCreate = PermissionHelper::hasModulePermission('attendance', 'create', 'holiday');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canCreate) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to create holidays.'
            ], 403);
        }

        try {
            $validator = Validator::make($request->all(), [
                'titles'           => 'required|string|max:255',
                'date'             => 'required|date',
                'holiday_code'     => 'nullable|string|max:100',
                'description'      => 'nullable|string',
                'holiday_type_id'  => 'nullable',
                'holiday_type'     => 'nullable|string',
                'status'           => 'required|in:Active,Inactive',
                'country_region'   => 'required|array',
                'country_region.*' => 'integer|exists:countries,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            if (! $boxUser) {
                return response()->json(['status' => false, 'message' => 'Box user not found'], 404);
            }

            $holiday = $this->holidayService->createHoliday($request->all(), $boxUser->box_id, $user->id);

            return response()->json([
                'status'  => true,
                'message' => 'Holiday added successfully.',
                'data'    => $holiday,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function getHolidayData(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = PermissionHelper::hasModulePermission('attendance', 'read', 'holiday');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to view holiday data.'
            ], 403);
        }

        if (! $boxUser) {
            return response()->json(['status' => false, 'message' => 'Box user not found'], 404);
        }

        $selectedYear = $request->input('year', date('Y'));

        $data = $this->holidayService->getHolidayData($boxUser->box_id, $selectedYear);

        return response()->json([
            'status'       => true,
            'years'        => $data['years'],
            'selectedYear' => $selectedYear,
            'holidays'     => $data['holidays'],
        ]);
    }

    public function holiday_delete(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canDelete = PermissionHelper::hasModulePermission('attendance', 'delete', 'holiday');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canDelete) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to delete holidays.'
            ], 403);
        }

        try {
            $id      = $request->id;
            $holiday = Holiday::find($id);

            if (! $holiday) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Holiday not found.',
                ], 404);
            }

            $isLinked = $this->holidayService->checkHolidayLinked($holiday);
            $forceDelete = filter_var($request->force, FILTER_VALIDATE_BOOLEAN);

            if ($isLinked && !$forceDelete) {
                return response()->json([
                    'status'  => 'warning',
                    'message' => 'This holiday is already used in attendance or linked anywhere. Are you sure you want to force delete it?'
                ]);
            }

            $this->holidayService->deleteHoliday($holiday, $user->id);

            return response()->json([
                'status'  => true,
                'message' => 'Holiday deleted successfully.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'An error occurred while deleting.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }
}
