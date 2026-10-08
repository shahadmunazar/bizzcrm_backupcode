<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\WorkModeSetting;
use App\Models\Box;
use App\Models\BoxUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WorkModeController extends Controller
{
    /**
     * Fetch list of Work Mode Settings
     */
    public function work_mode_index(Request $request)
    {
        try {
            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();
            if (!$boxUser) {
                return response()->json(['status' => 'error', 'message' => 'Box user not found.'], 404);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'work_mode'))) {
                 return response()->json(['status' => 'error', 'message' => 'You do not have permission to view work modes.'], 403);
            }

            if($boxUser->staff_role == 'admin'){
                $box = Box::where('superadmin_id', $userId)->first();
            }else{
                $box = Box::where('id', $boxUser->box_id)->first();
            }

            if (!$box) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No box found for this user.'
                ], 404);
            }

            $workModes = WorkModeSetting::where('box_id', $box->id)
                ->with(['box'])
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'status'  => 'success',
                'message' => 'Work mode settings fetched successfully.',
                'data'    => $workModes
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch work mode settings.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new Work Mode Setting
     */
    public function work_mode_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();
            if (!$boxUser) {
                return response()->json(['status' => 'error', 'message' => 'Box user not found.'], 404);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'create', 'work_mode'))) {
                 return response()->json(['status' => 'error', 'message' => 'You do not have permission to add work modes.'], 403);
            }

            if($boxUser->staff_role == 'admin'){
                $box = Box::where('superadmin_id', $userId)->first();
            }else{
                $box = Box::where('id', $boxUser->box_id)->first();
            }

            if (!$box) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No box found for this user.'
                ], 404);
            }

            $workMode = WorkModeSetting::create([
                'box_id'     => $box->id,
                'name'       => $request->name,
                'status'     => $request->status,
                'created_by' => $userId,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Work mode setting added successfully.',
                'data'    => $workMode
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to create work mode setting.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing Work Mode Setting
     */
    public function work_mode_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'     => 'required|exists:work_mode_settings,id',
            'name'   => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();
            if (!$boxUser) {
                return response()->json(['status' => 'error', 'message' => 'Box user not found.'], 404);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'update', 'work_mode'))) {
                 return response()->json(['status' => 'error', 'message' => 'You do not have permission to update work modes.'], 403);
            }

            $workMode = WorkModeSetting::findOrFail($request->id);

            $workMode->update([
                'name'       => $request->name,
                'status'     => $request->status,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Work mode setting updated successfully.',
                'data'    => $workMode
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update work mode setting.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a Work Mode Setting (Soft Delete)
     */
    public function work_mode_destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:work_mode_settings,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();
            if (!$boxUser) {
                return response()->json(['status' => 'error', 'message' => 'Box user not found.'], 404);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'delete', 'work_mode'))) {
                 return response()->json(['status' => 'error', 'message' => 'You do not have permission to delete work modes.'], 403);
            }

            $workMode = WorkModeSetting::findOrFail($request->id);
            $workMode->update(['deleted_by' => Auth::id()]);
            $workMode->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Work mode setting deleted successfully.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete work mode setting.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }
}
