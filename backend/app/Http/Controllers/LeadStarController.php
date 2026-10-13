<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeadStar;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;

class LeadStarController extends Controller
{
    public function toggleStar(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
        ]);

        $userId = Auth::id();
        $leadId = $request->lead_id;

        $star = LeadStar::where('user_id', $userId)->where('lead_id', $leadId)->first();

        if ($star) {
            $star->delete();
            return response()->json(['status' => true, 'is_starred' => false, 'message' => 'Lead removed from favourites']);
        } else {
            LeadStar::create([
                'user_id' => $userId,
                'lead_id' => $leadId,
            ]);
            return response()->json(['status' => true, 'is_starred' => true, 'message' => 'Lead added to favourites']);
        }
    }
}
