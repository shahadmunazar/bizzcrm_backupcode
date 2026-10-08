<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\BoxUser;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaxRateController extends Controller
{
    private function getBoxId()
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        return $boxUser ? $boxUser->box_id : null;
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return response()->json(['data' => []]);
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'read', 'tax_rates'))) {
             return response()->json(['status' => 'error', 'message' => 'You do not have permission to view tax rates.'], 403);
        }

        $boxId = $boxUser->box_id;
        $taxRates = TaxRate::where('box_id', $boxId)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $taxRates
        ]);
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'create', 'tax_rates'))) {
             return response()->json(['status' => 'error', 'message' => 'You do not have permission to create tax rates.'], 403);
        }

        $boxId = $boxUser->box_id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:Enabled,Disabled',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $taxRate = TaxRate::create([
            'box_id' => $boxId,
            'name' => $request->name,
            'rate' => $request->rate,
            'status' => $request->status,
            'added_by' => Auth::id(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tax rate added successfully',
            'data' => $taxRate
        ]);
    }

    public function update(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'update', 'tax_rates'))) {
             return response()->json(['status' => 'error', 'message' => 'You do not have permission to update tax rates.'], 403);
        }

        $boxId = $boxUser->box_id;

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:tax_rates,id',
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:Enabled,Disabled',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $taxRate = TaxRate::where('box_id', $boxId)->find($request->id);

        if (!$taxRate) {
            return response()->json(['status' => 'error', 'message' => 'Tax rate not found'], 404);
        }

        $taxRate->update([
            'name' => $request->name,
            'rate' => $request->rate,
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tax rate updated successfully',
            'data' => $taxRate
        ]);
    }

    public function destroy(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'delete', 'tax_rates'))) {
             return response()->json(['status' => 'error', 'message' => 'You do not have permission to delete tax rates.'], 403);
        }

        $boxId = $boxUser->box_id;

        $taxRate = TaxRate::where('box_id', $boxId)->find($request->id);

        if (!$taxRate) {
            return response()->json(['status' => 'error', 'message' => 'Tax rate not found'], 404);
        }

        $taxRate->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Tax rate deleted successfully'
        ]);
    }
}
