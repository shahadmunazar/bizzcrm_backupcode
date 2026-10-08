<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\EmployeeType;
use App\Models\Box;
use App\Models\BoxUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmployeeTypeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();
            if (!$boxUser) {
                return response()->json(['status' => 'error', 'message' => 'Box user not found.'], 404);
            }

            // We can check permissions if needed, similar to branches
            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            // Optional: permission check
            // if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'employee_type'))) {
            //      return response()->json(['status' => 'error', 'message' => 'You do not have permission to view employee types.'], 403);
            // }

            if ($boxUser->staff_role == 'admin') {
                $box = Box::where('superadmin_id', $userId)->first();
            } else {
                $box = Box::where('id', $boxUser->box_id)->first();
            }

            if (!$box) {
                return response()->json(['status' => 'error', 'message' => 'No box found for this user.'], 404);
            }

            $types = EmployeeType::where(function($q) use ($box) {
                    $q->where('box_id', $box->id)
                      ->orWhereNull('box_id');
                })
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Employee types fetched successfully.',
                'data' => $types
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch employee types.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
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

            if ($boxUser->staff_role == 'admin') {
                $box = Box::where('superadmin_id', $userId)->first();
            } else {
                $box = Box::where('id', $boxUser->box_id)->first();
            }

            if (!$box) {
                return response()->json(['status' => 'error', 'message' => 'No box found for this user.'], 404);
            }

            $type = EmployeeType::create([
                'box_id'   => $box->id,
                'name'     => $request->name,
                'status'   => $request->status,
                'added_by' => $userId,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Employee type added successfully.',
                'data'    => $type
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to create employee type.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'     => 'required|exists:employee_types,id',
            'name'   => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $userId = Auth::id();
            $type = EmployeeType::findOrFail($request->id);

            $type->update([
                'name'       => $request->name,
                'status'     => $request->status,
                'updated_by' => $userId,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Employee type updated successfully.',
                'data'    => $type
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update employee type.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:employee_types,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $type = EmployeeType::findOrFail($request->id);
            $type->update(['deleted_by' => Auth::id()]);
            $type->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Employee type deleted successfully.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete employee type.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }
}
