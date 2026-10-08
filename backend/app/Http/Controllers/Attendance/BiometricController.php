<?php

namespace App\Http\Controllers\Attendance;
use App\Http\Controllers\Controller;
use App\Models\{BiometricMachine,Box,BoxUser,CompanyBranch};
use App\Helpers\PermissionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,Validator,Storage};
use Yajra\DataTables\Facades\DataTables;

class BiometricController extends Controller
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


public function getAllBranch()
{
    try {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $boxSlug = request()->route('box_slug');
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) {
             return response()->json(['status' => 'error', 'message' => 'Box not found'], 404);
        }

        $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $hasPermission = PermissionHelper::hasModulePermission('attendance', 'read', 'biometric');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
            return response()->json([
                'status' => 404,
                'message' => 'You do not have permission to perform this action.',
            ], 404);
        }

        $branches = CompanyBranch::where('box_id', $box->id)
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => $branches
        ]);
    } catch (\Throwable $th) {
        return response()->json([
            'status' => 'error',
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
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $boxSlug = $request->route('box_slug');
            $box = Box::where('slug', $boxSlug)->first();
            if (!$box) {
                 return response()->json(['data' => []], 200);
            }

            $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            $hasPermission = PermissionHelper::hasModulePermission('attendance', 'read', 'biometric');

            if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
                if ($request->ajax()) {
                    return DataTables::of(collect([]))->make(true);
                }
                return response()->json([
                    'status' => 404,
                    'message' => 'You do not have permission to perform this action.',
                ], 404);
            }

            $machines = BiometricMachine::where('box_id', $box->id)
                ->with(['box', 'branch']);

            if ($request->ajax()) {
                return DataTables::of($machines)
                    ->addIndexColumn()
                    ->make(true);
            }

            return view('biometric_machines.index'); // Blade view fallback
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch biometric machines',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $boxSlug = $request->route('box_slug');
            $box = Box::where('slug', $boxSlug)->first();
            if (!$box) {
                 return response()->json(['error' => 'Box not found'], 404);
            }

            $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            $hasPermission = PermissionHelper::hasModulePermission('attendance', 'create', 'biometric');

            if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
                return response()->json([
                    'status' => 404,
                    'message' => 'You do not have permission to perform this action.',
                ], 404);
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
                return response()->json(['errors' => $validator->errors()], 422);
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
                'message' => 'Biometric Machine created successfully',
                'data' => $machine
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
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
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $boxSlug = $request->route('box_slug');
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) {
             return response()->json(['error' => 'Box not found'], 404);
        }

        $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $hasPermission = PermissionHelper::hasModulePermission('attendance', 'update', 'biometric');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
            return response()->json([
                'status' => 404,
                'message' => 'You do not have permission to perform this action.',
            ], 404);
        }

        //  Validate request
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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get validated data
        $validated = $validator->validated();

        // Find biometric machine
        $biometric = BiometricMachine::findOrFail($validated['id']);

        // Update record
        $biometric->update(array_merge(
            $request->only([
                'machine_no', 'machine_type', 'serial_no', 'server_port_no',
                'machine_name', 'country_id', 'state_id',
                'city_id', 'timezone', 'access_control', 'status'
            ]),
            [
                'updated_by' => Auth::id(),
                'branch_name' => $request->input('branch_id') ?? $request->input('branch_name')
            ]
        ));

        // Return success
        return response()->json([
            'message' => 'Biometric Machine updated successfully',
            'data' => $biometric
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
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
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $boxSlug = $request->route('box_slug');
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) {
             return response()->json(['error' => 'Box not found'], 404);
        }

        $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $hasPermission = PermissionHelper::hasModulePermission('attendance', 'delete', 'biometric');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
            return response()->json([
                'status' => 404,
                'message' => 'You do not have permission to perform this action.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:biometric_machines,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $biometric = BiometricMachine::findOrFail($request->id);
        $biometric->update(['deleted_by' => Auth::id()]);
        $biometric->delete();

        return response()->json(['message' => 'Biometric Machine deleted successfully'], 200);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to delete biometric machine',
            'message' => $e->getMessage()
        ], 500);
    }
}


    public function export(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $boxSlug = $request->route('box_slug');
            $box = Box::where('slug', $boxSlug)->first();
            if (!$box) {
                 return response()->json(['error' => 'Box not found'], 404);
            }

            $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
            $hasPermission = PermissionHelper::hasModulePermission('attendance', 'export', 'biometric');

            if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
                return response()->json([
                    'status' => 404,
                    'message' => 'You do not have permission to perform this action.',
                ], 404);
            }
            
            $type = $request->input('type', 'csv');
            if ($type === 'excel') $type = 'csv'; // normalize

            $timestamp = now()->format('Ymd_His');
            $extension = $type === 'pdf' ? 'pdf' : 'csv';
            $fileName = "biometric_export_{$user->id}_{$timestamp}.{$extension}";
            
            \App\Jobs\ExportBiometricDataJob::dispatch($user->id, $box->id, $boxSlug, $type, $fileName);

            return response()->json([
                'status' => 'success',
                'message' => 'Export started...',
                'file_name' => $fileName
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to start export.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadExport(Request $request)
    {
         $user = Auth::user();
         if (!$user) {
             abort(401);
         }
         
         $boxSlug = $request->route('box_slug');
         $box = Box::where('slug', $boxSlug)->first();
         if (!$box) abort(404);

         $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
         $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
         $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
         $hasPermission = PermissionHelper::hasModulePermission('attendance', 'export', 'biometric');

         if (!$isGlobalAdmin && !$isBoxAdmin && !$hasPermission) {
             abort(403);
         }

         $file = $request->route('file');
         if (!$file || basename($file) !== $file) {
             abort(400);
         }
         
         $path = 'exports/' . $file;
         
         if (!Storage::disk('public')->exists($path)) {
             abort(404);
         }
         
         return Storage::disk('public')->download($path);
    }
}
