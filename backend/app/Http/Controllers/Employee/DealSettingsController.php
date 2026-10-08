<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BoxUser;
use App\Models\DealStatus;
use App\Models\DealPeriod;
use App\Models\DealSource;
use App\Models\DealPriority;
use App\Models\DealTag;

class DealSettingsController extends Controller
{
    private function getBoxId() {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        return $boxUser ? $boxUser->box_id : null;
    }

    private function getValidationRules($table, $id = null)
    {
        $box_id = $this->getBoxId();
        $rule = \Illuminate\Validation\Rule::unique($table)->where(function ($query) use ($box_id) {
            return $query->where('box_id', $box_id)->whereNull('deleted_at');
        });
        if ($id) {
            $rule->ignore($id);
        }
        return [
            'name' => ['required', 'string', 'max:255', $rule],
            'status' => 'required|in:active,inactive'
        ];
    }

    public function index($box_slug)
    {
        return view('user_management.deal_settings');
    }

    public function getAllJson($box_slug)
    {
        $box_id = $this->getBoxId();
        if (!$box_id) return response()->json([]);

        return response()->json([
            'statuses' => DealStatus::where('box_id', $box_id)->orderBy('id', 'desc')->get(),
            'periods' => DealPeriod::where('box_id', $box_id)->orderBy('id', 'desc')->get(),
            'sources' => DealSource::where('box_id', $box_id)->orderBy('id', 'desc')->get(),
            'priorities' => DealPriority::where('box_id', $box_id)->orderBy('id', 'desc')->get(),
            'tags' => DealTag::where('box_id', $box_id)->orderBy('id', 'desc')->get(),
        ]);
    }

    // STATUSES
    public function storeStatus(Request $request, $box_slug)
    {
        $request->validate($this->getValidationRules('deal_statuses'));
        DealStatus::create([
            'box_id' => $this->getBoxId(),
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => Auth::id()
        ]);
        return response()->json(['success' => true, 'message' => 'Status added successfully.']);
    }
    public function updateStatus(Request $request, $box_slug, $id)
    {
        $request->validate($this->getValidationRules('deal_statuses', $id));
        $item = DealStatus::where('box_id', $this->getBoxId())->findOrFail($id);
        $item->update(['name' => $request->name, 'status' => $request->status, 'updated_by' => Auth::id()]);
        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }
    public function destroyStatus($box_slug, $id)
    {
        $item = DealStatus::where('box_id', $this->getBoxId())->findOrFail($id);
        $item->update(['deleted_by' => Auth::id()]);
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Status deleted successfully.']);
    }

    // PERIODS
    public function storePeriod(Request $request, $box_slug)
    {
        $request->validate($this->getValidationRules('deal_periods'));
        DealPeriod::create([
            'box_id' => $this->getBoxId(),
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => Auth::id()
        ]);
        return response()->json(['success' => true, 'message' => 'Period added successfully.']);
    }
    public function updatePeriod(Request $request, $box_slug, $id)
    {
        $request->validate($this->getValidationRules('deal_periods', $id));
        $item = DealPeriod::where('box_id', $this->getBoxId())->findOrFail($id);
        $item->update(['name' => $request->name, 'status' => $request->status, 'updated_by' => Auth::id()]);
        return response()->json(['success' => true, 'message' => 'Period updated successfully.']);
    }
    public function destroyPeriod($box_slug, $id)
    {
        $item = DealPeriod::where('box_id', $this->getBoxId())->findOrFail($id);
        $item->update(['deleted_by' => Auth::id()]);
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Period deleted successfully.']);
    }

    // SOURCES
    public function storeSource(Request $request, $box_slug)
    {
        $request->validate($this->getValidationRules('deal_sources'));
        DealSource::create([
            'box_id' => $this->getBoxId(),
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => Auth::id()
        ]);
        return response()->json(['success' => true, 'message' => 'Source added successfully.']);
    }
    public function updateSource(Request $request, $box_slug, $id)
    {
        $request->validate($this->getValidationRules('deal_sources', $id));
        $item = DealSource::where('box_id', $this->getBoxId())->findOrFail($id);
        $item->update(['name' => $request->name, 'status' => $request->status, 'updated_by' => Auth::id()]);
        return response()->json(['success' => true, 'message' => 'Source updated successfully.']);
    }
    public function destroySource($box_slug, $id)
    {
        $item = DealSource::where('box_id', $this->getBoxId())->findOrFail($id);
        $item->update(['deleted_by' => Auth::id()]);
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Source deleted successfully.']);
    }

    // PRIORITIES
    public function storePriority(Request $request, $box_slug)
    {
        $request->validate($this->getValidationRules('deal_priorities'));
        DealPriority::create([
            'box_id' => $this->getBoxId(),
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => Auth::id()
        ]);
        return response()->json(['success' => true, 'message' => 'Priority added successfully.']);
    }
    public function updatePriority(Request $request, $box_slug, $id)
    {
        $request->validate($this->getValidationRules('deal_priorities', $id));
        $item = DealPriority::where('box_id', $this->getBoxId())->findOrFail($id);
        $item->update(['name' => $request->name, 'status' => $request->status, 'updated_by' => Auth::id()]);
        return response()->json(['success' => true, 'message' => 'Priority updated successfully.']);
    }
    public function destroyPriority($box_slug, $id)
    {
        $item = DealPriority::where('box_id', $this->getBoxId())->findOrFail($id);
        $item->update(['deleted_by' => Auth::id()]);
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Priority deleted successfully.']);
    }

    // TAGS
    public function storeTag(Request $request, $box_slug)
    {
        $request->validate($this->getValidationRules('deal_tags'));
        DealTag::create([
            'box_id' => $this->getBoxId(),
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => Auth::id()
        ]);
        return response()->json(['success' => true, 'message' => 'Tag added successfully.']);
    }
    public function updateTag(Request $request, $box_slug, $id)
    {
        $request->validate($this->getValidationRules('deal_tags', $id));
        $item = DealTag::where('box_id', $this->getBoxId())->findOrFail($id);
        $item->update(['name' => $request->name, 'status' => $request->status, 'updated_by' => Auth::id()]);
        return response()->json(['success' => true, 'message' => 'Tag updated successfully.']);
    }
    public function destroyTag($box_slug, $id)
    {
        $item = DealTag::where('box_id', $this->getBoxId())->findOrFail($id);
        $item->update(['deleted_by' => Auth::id()]);
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Tag deleted successfully.']);
    }
}
