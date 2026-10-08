<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AssetType;
use App\Models\BoxUser;
use Illuminate\Support\Facades\Auth;

class AssetTypeController extends Controller
{
    public function index()
    {
        $box_id = Auth::user()->boxUser->box_id ?? BoxUser::where('user_id', Auth::id())->value('box_id');
        $types = AssetType::where('box_id', $box_id)->get();
        return response()->json(['success' => true, 'data' => $types]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ]);

        $box_id = Auth::user()->boxUser->box_id ?? BoxUser::where('user_id', Auth::id())->value('box_id');

        $exists = AssetType::where('box_id', $box_id)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Asset Type with this name already exists.'], 422);
        }

        $type = AssetType::create([
            'box_id' => $box_id,
            'name' => $request->name,
            'status' => $request->status ?? 'active',
            'added_by' => Auth::id(),
            'last_updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Asset Type added successfully.', 'data' => $type]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:asset_types,id',
            'name' => 'required|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ]);

        $assetType = AssetType::findOrFail($request->id);

        if ($assetType->name !== $request->name) {
            $exists = AssetType::where('box_id', $assetType->box_id)
                ->where('name', $request->name)
                ->exists();

            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Asset Type with this name already exists.'], 422);
            }
        }

        $assetType->update([
            'name' => $request->name,
            'status' => $request->status ?? 'active',
            'last_updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Asset Type updated successfully.', 'data' => $assetType]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:asset_types,id',
        ]);

        $type = AssetType::findOrFail($request->id);

        // Optionally check if it is being used in Assets table
        if ($type->assets()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Asset Type is in use and cannot be deleted.'], 403);
        }

        $type->delete();

        return response()->json(['success' => true, 'message' => 'Asset Type deleted successfully.']);
    }
}
