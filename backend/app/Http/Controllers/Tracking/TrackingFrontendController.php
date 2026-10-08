<?php
namespace App\Http\Controllers\Tracking;

use App\Http\Controllers\Controller;

class TrackingFrontendController extends Controller
{
    public function index()
    {
        return view('frontend.tracking.index');
    }
}
