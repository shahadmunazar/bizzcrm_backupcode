<?php

namespace App\Http\Controllers\FieldTracking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationTrackingFrontendController extends Controller
{
    /**
     * Display the Location Tracking Dashboard.
     */
    public function index(Request $request, $box_slug)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->firstOrFail();
        
        // Pass necessary variables to the view, e.g. API keys if needed
        // Assuming a layout exists, we will use 'fieldstracking.tracking-dashboard'
        
        return view('fieldstracking.tracking-dashboard', compact('box'));
    }
}
