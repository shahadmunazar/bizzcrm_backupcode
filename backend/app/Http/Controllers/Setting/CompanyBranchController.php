<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\CompanyBranch;
use App\Models\Box;
use App\Models\BoxUser;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompanyBranchController extends Controller
{

    public function branchindex(Request $request)
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

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'branch'))) {
                 return response()->json(['status' => 'error', 'message' => 'You do not have permission to view branches.'], 403);
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

            $branches = CompanyBranch::where('box_id', $box->id)
                ->with(['box', 'country', 'state', 'city', 'creator'])
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Branch list fetched successfully.',
                'data' => $branches
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch branch list.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function branchstore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_name'       => 'required|string|max:255',
            'pincode'    => 'required|string|max:10',
            'address'    => 'required|string',
            'country_id' => 'required|exists:countries,id',
            'state_id'   => 'required|exists:states,id',
            'city_id'    => 'required|exists:cities,id',
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

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'create', 'branch'))) {
                 return response()->json(['status' => 'error', 'message' => 'You do not have permission to add branches.'], 403);
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

            $branch = CompanyBranch::create([
                'box_id'     => $box->id,
                'name'       => $request->branch_name,
                'pincode'    => $request->pincode,
                'address'    => $request->address,
                'country_id' => $request->country_id,
                'state_id'   => $request->state_id,
                'city_id'    => $request->city_id,
                'created_by' => $userId,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Branch added successfully.',
                'data'    => $branch
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to create branch.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update a branch (AJAX)
     */
    public function branchupdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id'  => 'required|exists:company_branches,id',
            'branch_name'       => 'required|string|max:255',
            'pincode'    => 'required|string|max:10',
            'address'    => 'required|string',
            'country_id' => 'required|exists:countries,id',
            'state_id'   => 'required|exists:states,id',
            'city_id'    => 'required|exists:cities,id',
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

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'update', 'branch'))) {
                 return response()->json(['status' => 'error', 'message' => 'You do not have permission to update branches.'], 403);
            }

            $branch = CompanyBranch::findOrFail($request->branch_id);

            $branch->update([
                'name'       => $request->branch_name,
                'pincode'    => $request->pincode,
                'address'    => $request->address,
                'country_id' => $request->country_id,
                'state_id'   => $request->state_id,
                'city_id'    => $request->city_id,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Branch updated successfully.',
                'data'    => $branch
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update branch.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }


    public function branchdestroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|exists:company_branches,id',
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

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'delete', 'branch'))) {
                 return response()->json(['status' => 'error', 'message' => 'You do not have permission to delete branches.'], 403);
            }

            $branch = CompanyBranch::findOrFail($request->branch_id);
            $branch->update(['deleted_by' => Auth::id()]);
            $branch->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Branch deleted successfully.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete branch.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }
}
