<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPreference;
use App\Services\AIService;

class PreferenceController extends Controller
{
    public function index($box_slug = null)
    {
        $user = Auth::user();
        $box = \App\Models\Box::where('slug', $box_slug)->first();
        $boxId = $box ? $box->id : null;

        $userPref = UserPreference::where('user_id', $user->id)->where('box_id', $boxId)->first();
        $preferences = $userPref ? $userPref->preferences : [];

        return view('preferences.index', compact('preferences', 'box_slug'));
    }

    public function parseAi(Request $request, $box_slug = null)
    {
        $aiService = app(AIService::class);
        $request->validate(['natural_language' => 'required|string']);
        
        $parsedJson = $aiService->parseNaturalLanguagePreference($request->natural_language);

        if ($parsedJson) {
            $user = Auth::user();
            $box = \App\Models\Box::where('slug', $box_slug)->first();
            $boxId = $box ? $box->id : null;

            $userPref = UserPreference::firstOrCreate(['user_id' => $user->id, 'box_id' => $boxId]);
            
            // Merge existing preferences with the new parsed ones
            $existing = $userPref->preferences ?? [];
            $merged = array_replace_recursive($existing, $parsedJson);
            
            $userPref->preferences = $merged;
            $userPref->save();
            
            return redirect()->back()->with('success', 'AI successfully applied your preference.');
        }

        return redirect()->back()->with('error', 'AI could not understand your preference. Please try again.');
    }

    public function reset($box_slug = null)
    {
        $user = Auth::user();
        $box = \App\Models\Box::where('slug', $box_slug)->first();
        $boxId = $box ? $box->id : null;

        UserPreference::where('user_id', $user->id)->where('box_id', $boxId)->delete();
        
        return redirect()->back()->with('success', 'Preferences reset to defaults.');
    }
}
