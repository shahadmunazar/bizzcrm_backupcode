<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoxUser;
use App\Models\LeaveType;

class AdminFrontendController extends Controller
{
    public function index($box_slug)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->first();
        $boxId = $box ? $box->id : null;
        
        $jobApplicantsCount = 0;
        $employees = collect();
        $leaveTypes = collect();

        if ($boxId) {
            $jobApplicantsCount = \App\Models\JobApplicant::ofBox($boxId)->count();
            
            $employees = BoxUser::with('user')
                ->where('box_id', $boxId)
                ->whereHas('user', function ($q) {
                    $q->whereDoesntHave('roles', function ($q2) {
                        $q2->where('name', 'super_admin');
                    });
                })
                ->get();
                
            $leaveTypes = LeaveType::where('box_id', $boxId)->get();
        }

        return view('frontend.admin.index', compact('jobApplicantsCount', 'employees', 'leaveTypes'));
    }
}
