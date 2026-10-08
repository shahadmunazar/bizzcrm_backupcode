<?php

namespace App\Http\Controllers\Deals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DealsFrontendController extends Controller
{
    public function index(){
        return view('frontend.deals.index');
    }
}
