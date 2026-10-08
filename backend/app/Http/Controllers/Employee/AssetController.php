<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\BoxUser;
use App\Models\AssignedAsset;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function index()
    {
        $box_id = Auth::user()->boxUser->box_id ?? BoxUser::where('user_id', Auth::id())->value('box_id');
        $assets = Asset::where('box_id', $box_id)->with(['assetType', 'assignedToBoxUser'])->get();
        return response()->json(['success' => true, 'data' => $assets]);
    }

    public function history(Request $request)
    {
        $box_id = Auth::user()->boxUser->box_id ?? BoxUser::where('user_id', Auth::id())->value('box_id');
        $query = AssignedAsset::where('assigned_assets.box_id', $box_id)->with(['asset', 'boxUser.user']);
        if ($request->has('box_user_id')) {
            $query->where('box_user_id', $request->box_user_id);
        }
        $history = $query->latest()->get();
        return response()->json(['success' => true, 'data' => $history]);
    }

    public function available()
    {
        $box_id = Auth::user()->boxUser->box_id ?? BoxUser::where('user_id', Auth::id())->value('box_id');
        $assets = Asset::where('box_id', $box_id)->where('status', 'available')->get();
        return response()->json(['success' => true, 'data' => $assets]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'asset_name' => 'required|string|max:255',
            'asset_type_id' => 'required|exists:asset_types,id',
            'serial_number' => 'required|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_date' => 'nullable|date',
        ]);

        $box_id = Auth::user()->boxUser->box_id ?? BoxUser::where('user_id', Auth::id())->value('box_id');

        $exists = Asset::where('box_id', $box_id)
            ->where('serial_number', $request->serial_number)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Asset with this serial number already exists.'], 422);
        }
        $asset = Asset::create([
            'box_id' => $box_id,
            'asset_name' => $request->asset_name,
            'asset_type_id' => $request->asset_type_id,
            'brand' => $request->brand,
            'model' => $request->model,
            'serial_number' => $request->serial_number,
            'vendor' => $request->vendor,
            'purchase_date' => $request->purchase_date,
            'cost' => $request->cost,
            'warranty_end_date' => $request->warranty_end_date,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'assigned_date' => $request->assigned_date,
            'status' => $request->assigned_to ? 'assigned' : 'available',
            'added_by' => Auth::id(),
            'last_updated_by' => Auth::id(),
        ]);

        if ($asset->assigned_to) {
            AssignedAsset::create([
                'asset_id' => $asset->id,
                'box_user_id' => $asset->assigned_to,
                'box_id' => $asset->box_id,
                'assigned_date' => $asset->assigned_date ?? now(),
                'added_by' => Auth::id(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Asset added successfully.', 'data' => $asset]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:assets,id',
            'asset_name' => 'required|string|max:255',
            'asset_type_id' => 'required|exists:asset_types,id',
            'serial_number' => 'required|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_date' => 'nullable|date',
        ]);

        $asset = Asset::findOrFail($request->id);

        if ($asset->serial_number !== $request->serial_number) {
            $exists = Asset::where('box_id', $asset->box_id)
                ->where('serial_number', $request->serial_number)
                ->exists();

            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Asset with this serial number already exists.'], 422);
            }
        }

        $oldUserId = $asset->getOriginal('assigned_to');
        $newUserId = $request->assigned_to;

        $asset->update([
            'asset_name' => $request->asset_name,
            'asset_type_id' => $request->asset_type_id,
            'brand' => $request->brand,
            'model' => $request->model,
            'serial_number' => $request->serial_number,
            'vendor' => $request->vendor,
            'purchase_date' => $request->purchase_date,
            'cost' => $request->cost,
            'warranty_end_date' => $request->warranty_end_date,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'assigned_date' => $request->assigned_date,
            'status' => $request->assigned_to ? 'assigned' : ($asset->status == 'assigned' ? 'available' : $asset->status),
            'last_updated_by' => Auth::id(),
        ]);

        if ($oldUserId != $newUserId) {
            // Close old assignment if exists
            if ($oldUserId) {
                AssignedAsset::where('asset_id', $asset->id)
                    ->where('box_user_id', $oldUserId)
                    ->whereNull('returned_date')
                    ->update(['returned_date' => now()]);
            }
            // Create new assignment if user selected
            if ($newUserId) {
                AssignedAsset::create([
                    'asset_id' => $asset->id,
                    'box_user_id' => $newUserId,
                    'box_id' => $asset->box_id,
                    'assigned_date' => $request->assigned_date ?? now(),
                    'added_by' => Auth::id(),
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Asset updated successfully.', 'data' => $asset]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:assets,id',
        ]);

        $asset = Asset::findOrFail($request->id);
        $asset->delete();

        return response()->json(['success' => true, 'message' => 'Asset deleted successfully.']);
    }

    public function assignAsset(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'box_user_id' => 'required|exists:box_users,id',
            'assigned_date' => 'required|date',
        ]);

        $asset = Asset::findOrFail($request->asset_id);

        if ($asset->status !== 'available') {
            return response()->json(['success' => false, 'message' => 'Asset is not available for assignment.'], 422);
        }

        $asset->update([
            'assigned_to' => $request->box_user_id,
            'assigned_date' => $request->assigned_date,
            'status' => 'assigned',
            'last_updated_by' => Auth::id(),
        ]);

        AssignedAsset::create([
            'asset_id' => $asset->id,
            'box_user_id' => $request->box_user_id,
            'box_id' => $asset->box_id,
            'assigned_date' => $request->assigned_date,
            'added_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Asset assigned successfully.']);
    }

    public function updateHistory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:assigned_assets,id',
            'assigned_date' => 'required|date',
            'returned_date' => 'nullable|date',
        ]);

        $log = AssignedAsset::findOrFail($request->id);
        $log->update([
            'assigned_date' => $request->assigned_date,
            'returned_date' => $request->returned_date,
        ]);

        return response()->json(['success' => true, 'message' => 'History record updated successfully.']);
    }

    public function destroyHistory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:assigned_assets,id',
        ]);

        AssignedAsset::findOrFail($request->id)->delete();

        return response()->json(['success' => true, 'message' => 'History record deleted successfully.']);
    }
}
