<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{BiometricMachine, Box, BoxUser, CompanyBranch};
use App\Helpers\PermissionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Validator, Storage};
use Carbon\Carbon;

class BiometricMachineApiController extends Controller
{
    public function getTimezones()
    {
        try {
            $timezones = \DateTimeZone::listIdentifiers();
            return response()->json([
                'success' => true,
                'data' => $timezones
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllBranch(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $boxSlug = $request->route('box_slug');
            $box = Box::where('slug', $boxSlug)->first();
            if (!$box) {
                return response()->json(['success' => false, 'message' => 'Box not found'], 404);
            }

            $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web') || $user->hasRole(['admin', 'superadmin'], 'api');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            // Note: PermissionHelper might expect web guard, but we'll try to let it pass if box admin
            $hasPermission = true; 

            if (!$isGlobalAdmin && !$isBoxAdmin) {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            $branches = CompanyBranch::where('box_id', $box->id)->get();
            return response()->json([
                'success' => true,
                'data' => $branches
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch branches.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getBiometric(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $boxSlug = $request->route('box_slug');
            $box = Box::where('slug', $boxSlug)->first();
            if (!$box) {
                return response()->json(['success' => true, 'data' => []], 200);
            }

            $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web') || $user->hasRole(['admin', 'superadmin'], 'api');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!$isGlobalAdmin && !$isBoxAdmin) {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            $query = BiometricMachine::where('box_id', $box->id)->with(['box', 'branch'])->orderBy('id', 'desc');

            $perPage = $request->input('per_page', 25);
            $paginator = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $paginator->items(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch biometric machines',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $boxSlug = $request->route('box_slug');
            $id = $request->route('id');
            $box = Box::where('slug', $boxSlug)->first();
            if (!$box) {
                return response()->json(['success' => false, 'message' => 'Box not found'], 404);
            }

            $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web') || $user->hasRole(['admin', 'superadmin'], 'api');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!$isGlobalAdmin && !$isBoxAdmin) {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            $machine = BiometricMachine::where('box_id', $box->id)
                ->with(['box', 'branch'])
                ->find($id);

            if (!$machine) {
                return response()->json([
                    'success' => false,
                    'message' => 'Biometric Machine not found'
                ], 404);
            }

            $transformedData = [
                'id' => $machine->id,
                'machine_no' => $machine->machine_no,
                'machine_name' => $machine->machine_name,
                'machine_type' => $machine->machine_type,
                'serial_no' => $machine->serial_no,
                'server_port_no' => $machine->server_port_no,
                'branch_id' => $machine->branch_id,
                'branch_name' => $machine->branch ? $machine->branch->branch_name : $machine->branch_name,
                'country_id' => $machine->country_id,
                'state_id' => $machine->state_id,
                'city_id' => $machine->city_id,
                'timezone' => $machine->timezone,
                'access_control' => $machine->access_control,
                'status' => $machine->status,
                'created_at' => $machine->created_at,
                'updated_at' => $machine->updated_at,
            ];

            return response()->json([
                'success' => true,
                'data' => $transformedData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch biometric machine details',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $boxSlug = $request->route('box_slug');
            $box = Box::where('slug', $boxSlug)->first();
            if (!$box) {
                return response()->json(['success' => false, 'message' => 'Box not found'], 404);
            }

            $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web') || $user->hasRole(['admin', 'superadmin'], 'api');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!$isGlobalAdmin && !$isBoxAdmin) {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'machine_no' => 'required|string|max:255',
                'machine_type' => 'nullable|string|max:255',
                'serial_no' => 'nullable|string|max:255',
                'server_port_no' => 'nullable|string|max:255',
                'machine_name' => 'nullable|string|max:255',
                'branch_name' => 'nullable|string|max:255',
                'country_id' => 'nullable|exists:countries,id',
                'branch_id' => 'nullable|exists:company_branches,id',
                'state_id' => 'nullable|exists:states,id',
                'city_id' => 'nullable|exists:cities,id',
                'timezone' => 'nullable|string|max:255',
                'access_control' => 'nullable|string|max:255',
                'status' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $validated = $validator->validated();
            $machine = BiometricMachine::create(array_merge(
                $request->only([
                    'machine_no', 'machine_type', 'serial_no', 'server_port_no',
                    'machine_name', 'country_id', 'state_id',
                    'city_id', 'timezone', 'access_control', 'status'
                ]),
                [
                    'box_id' => $box->id,
                    'created_by' => $user->id,
                    'branch_name' => $request->input('branch_id') ?? $request->input('branch_name')
                ]
            ));

            return response()->json([
                'success' => true,
                'message' => 'Biometric Machine created successfully',
                'data' => $machine
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create biometric machine',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $boxSlug = $request->route('box_slug');
            $box = Box::where('slug', $boxSlug)->first();
            if (!$box) {
                return response()->json(['success' => false, 'message' => 'Box not found'], 404);
            }

            $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web') || $user->hasRole(['admin', 'superadmin'], 'api');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!$isGlobalAdmin && !$isBoxAdmin) {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:biometric_machines,id',
                'machine_no' => 'nullable|string|max:255',
                'machine_type' => 'nullable|string|max:255',
                'serial_no' => 'nullable|string|max:255',
                'server_port_no' => 'nullable|string|max:255',
                'machine_name' => 'nullable|string|max:255',
                'branch_name' => 'nullable|string|max:255',
                'branch_id' => 'nullable|exists:company_branches,id',
                'country_id' => 'nullable|exists:countries,id',
                'state_id' => 'nullable|exists:states,id',
                'city_id' => 'nullable|exists:cities,id',
                'timezone' => 'nullable|string|max:255',
                'access_control' => 'nullable|string|max:255',
                'status' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $validated = $validator->validated();
            $biometric = BiometricMachine::where('box_id', $box->id)->findOrFail($validated['id']);

            $biometric->update(array_merge(
                $request->only([
                    'machine_no', 'machine_type', 'serial_no', 'server_port_no',
                    'machine_name', 'country_id', 'state_id',
                    'city_id', 'timezone', 'access_control', 'status'
                ]),
                [
                    'updated_by' => $user->id,
                    'branch_name' => $request->input('branch_id') ?? $request->input('branch_name')
                ]
            ));

            return response()->json([
                'success' => true,
                'message' => 'Biometric Machine updated successfully',
                'data' => $biometric
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update biometric machine',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $boxSlug = $request->route('box_slug');
            $box = Box::where('slug', $boxSlug)->first();
            if (!$box) {
                return response()->json(['success' => false, 'message' => 'Box not found'], 404);
            }

            $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web') || $user->hasRole(['admin', 'superadmin'], 'api');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!$isGlobalAdmin && !$isBoxAdmin) {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'You do not have permission to perform this action.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:biometric_machines,id'
            ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $biometric = BiometricMachine::where('box_id', $box->id)->findOrFail($request->id);
            $biometric->update(['deleted_by' => $user->id]);
            $biometric->delete();

            return response()->json([
                'success' => true,
                'message' => 'Biometric Machine deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete biometric machine',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
