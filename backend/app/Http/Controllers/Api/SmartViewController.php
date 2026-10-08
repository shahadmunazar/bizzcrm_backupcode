<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmartView;
use App\Services\AIService;

class SmartViewController extends Controller
{
    public function __construct(private AIService $aiService)
    {
    }

    public function index($box_slug = null)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->first();
        $boxId = $box ? $box->id : null;
        
        return response()->json(SmartView::where('user_id', auth()->id())->where('box_id', $boxId)->get());
    }

    public function store(Request $request, $box_slug = null)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'module' => 'required|string|in:activity,lead,whatsapp,project,deal,all',
            'natural_language_instruction' => 'required|string',
        ]);

        $rules = $this->aiService->generateSmartViewRules($request->natural_language_instruction);

        $box = \App\Models\Box::where('slug', $box_slug)->first();
        $boxId = $box ? $box->id : null;

        // Merge manual filters from request with AI generated filters
        $manualFilters = $request->input('filters', []);
        $aiFilters = $rules['filters'] ?? [];
        $mergedFilters = array_merge($manualFilters, $aiFilters);

        $smartView = SmartView::create([
            'box_id' => $boxId,
            'user_id' => auth()->id(),
            'name' => $request->name,
            'module' => $request->module,
            'natural_language_instruction' => $request->natural_language_instruction,
            'filters' => $mergedFilters,
            'ai_rules' => $rules['ai_rules'] ?? [],
            'priority' => $rules['priority'] ?? 'Normal',
        ]);

        return response()->json($smartView, 201);
    }

    public function showRecords($box_slug, SmartView $smartView, $module)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->first();
        $boxId = $box ? $box->id : null;

        $modelClass = match ($module) {
            'activity' => \App\Models\Activity::class,
            'lead' => \App\Models\Lead::class,
            'whatsapp' => \App\Models\WhatsAppMessage::class,
            'project' => \App\Models\Project::class,
            'deal' => \App\Models\Deal::class,
            default => null,
        };

        if (!$modelClass) {
            return response()->json(['error' => 'Invalid module'], 400);
        }

        $query = $modelClass::where('box_id', $boxId);

        if (is_array($smartView->filters)) {
            foreach ($smartView->filters as $filter) {
                if (isset($filter['field'], $filter['operator'], $filter['value'])) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn((new $modelClass)->getTable(), $filter['field'])) {
                        $query->where($filter['field'], $filter['operator'], $filter['value']);
                    }
                }
            }
        }

        if (!empty($smartView->ai_rules)) {
            $aiMatchedIds = \App\Models\AiInsight::where('box_id', $boxId)
                ->where('analyzable_type', $modelClass)
                ->whereRaw('JSON_CONTAINS(matched_smart_views, ?)', [(string)$smartView->id])
                ->pluck('analyzable_id')
                ->toArray();
            
            $query->whereIn('id', $aiMatchedIds);
        }

        $records = $query->orderBy('id', 'desc')->limit(50)->get();

        return response()->json([
            'smart_view' => $smartView,
            'records' => $records
        ]);
    }
}
