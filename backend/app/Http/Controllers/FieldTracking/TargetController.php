<?php

namespace App\Http\Controllers\FieldTracking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    public function targetTemplate() { return view('fieldstracking.target-template'); }
    public function myTarget() { return view('fieldstracking.my-target'); }
    public function teamTargets() { return view('fieldstracking.team-targets'); }
}
