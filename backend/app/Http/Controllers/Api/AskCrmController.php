<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AIService;
use App\Models\Activity;
use App\Models\Lead;

class AskCrmController extends Controller
{
    public function __construct(private AIService $aiService)
    {
    }

    public function search(Request $request, $box_slug = null)
    {
        $request->validate([
            'query' => 'required|string',
        ]);

        $structuredQuery = $this->aiService->translateSearchToQuery($request->input('query'));
        
        if (!$structuredQuery) {
            return response()->json(['error' => 'Could not process query'], 400);
        }

        $box = \App\Models\Box::where('slug', $box_slug)->first();
        $boxId = $box ? $box->id : null;

        $results = [];

        // Very basic prototype implementation for executing the dynamic search
        $modelsToSearch = $structuredQuery['models'] ?? ['Activity', 'Lead'];
        
        if (in_array('Activity', $modelsToSearch)) {
            $activityQuery = Activity::where('box_id', $boxId);
            // A production version would intelligently map 'conditions' to where clauses.
            // For now, we rely on the DB structure and simple mapping or fallback to full-text.
            $activityQuery->orderBy('id', 'desc')->limit(10);
            $results['activities'] = $activityQuery->get();
        }

        if (in_array('Lead', $modelsToSearch)) {
            $leadQuery = Lead::where('box_id', $boxId);
            $leadQuery->orderBy('id', 'desc')->limit(10);
            $results['leads'] = $leadQuery->get();
        }

        return response()->json([
            'parsed_intent' => $structuredQuery,
            'results' => $results
        ]);
    }
}
