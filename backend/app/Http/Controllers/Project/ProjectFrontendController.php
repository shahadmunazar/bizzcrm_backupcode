<?php
namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;

class ProjectFrontendController extends Controller
{
    public function index()
    {
        return view('frontend.projects.index');
    }
}
