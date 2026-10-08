<?php

namespace App\Http\Controllers\FieldTracking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function trackingDashboard() { return view('fieldstracking.tracking-dashboard'); }
}
