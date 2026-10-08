<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterSettingsController extends Controller
{
    public function index(Request $request, $box_slug)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->firstOrFail();
        return view('finance.settings.index', compact('box'));
    }
}
