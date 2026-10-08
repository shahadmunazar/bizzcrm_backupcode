<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Models\JobApplicant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JobApplicantController extends Controller
{
    private function getBoxId()
    {
        $boxUser = Auth::user()->box_user ?? \App\Models\BoxUser::where('user_id', Auth::id())->first();
        return $boxUser->box_id ?? null;
    }

    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
    {
        $boxId = $this->getBoxId();
        if (!$boxId) {
            abort(403, 'Unauthorized action.');
        }

if ($request->ajax() || $request->has('draw')) {
            $data = JobApplicant::ofBox($boxId)->latest()->get();
            $box_slug = $request->route('box_slug') ?? 'bizzcrm';
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('full_name', function($row){
                    $imgSrc = $row->profile_photo ? asset('storage/'.$row->profile_photo) : asset('assets/img/profiles/avatar-02.jpg');
                    return '<h2 class="table-avatar">
                                <a href="javascript:void(0)" onclick="fetchApplicantDetails('.$row->id.')" class="avatar"><img alt="" src="'.$imgSrc.'"></a>
                                <a href="javascript:void(0)" onclick="fetchApplicantDetails('.$row->id.')">'.$row->full_name.'</a>
                            </h2>';
                })
                ->addColumn('candidate_status', function($row){
                    $color = 'text-info';
                    if($row->candidate_status == 'Hired') $color = 'text-success';
                    elseif($row->candidate_status == 'Rejected') $color = 'text-danger';
                    
                    $status = $row->candidate_status ?? 'Applied';
                    return '<div class="dropdown action-label">
                                <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-regular fa-circle-dot '.$color.'"></i> '.$status.'
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#"><i class="fa-regular fa-circle-dot text-info"></i> Applied</a>
                                    <a class="dropdown-item" href="#"><i class="fa-regular fa-circle-dot text-success"></i> Shortlisted</a>
                                    <a class="dropdown-item" href="#"><i class="fa-regular fa-circle-dot text-danger"></i> Rejected</a>
                                    <a class="dropdown-item" href="#"><i class="fa-regular fa-circle-dot text-success"></i> Hired</a>
                                </div>
                            </div>';
                })
                ->addColumn('action', function($row) use ($box_slug) {
                    $deleteUrl = route('hr.job.applicants.delete', ['box_slug' => $box_slug, 'id' => $row->id]);
                    return '<div class="dropdown dropdown-action text-end">
                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="fetchApplicantDetails('.$row->id.')"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_applicant_'.$row->id.'"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
                                </div>
                            </div>
                            
                            <!-- Delete Modal (Appended per row for simplicity or could be centralized) -->
                            <div class="modal custom-modal fade" id="delete_applicant_'.$row->id.'" role="dialog">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="form-header">
                                                <h3>Delete Applicant</h3>
                                                <p>Are you sure want to delete?</p>
                                            </div>
                                            <div class="modal-btn delete-action">
                                                <div class="row">
                                                    <div class="col-6 col-md-5">
                                                        <a href="javascript:void(0)" onclick="deleteApplicantAjax(\''.$deleteUrl.'\')" class="btn btn-danger w-100 py-2 font-weight-bold" style="border-radius: 8px;">Yes, Delete</a>
                                                    </div>
                                                    <div class="col-6 col-md-5">
                                                        <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-secondary w-100 py-2 font-weight-bold" style="border-radius: 8px;">Cancel</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                })
                ->editColumn('dob', function($row){ return $row->dob ? Carbon::parse($row->dob)->format('d-m-Y') : '-'; })
                ->editColumn('expected_joining_date', function($row){ return $row->expected_joining_date ? Carbon::parse($row->expected_joining_date)->format('d-m-Y') : '-'; })
                ->editColumn('last_working_date', function($row){ return $row->last_working_date ? Carbon::parse($row->last_working_date)->format('d-m-Y') : '-'; })
                ->editColumn('interview_date', function($row){ return $row->interview_date ? Carbon::parse($row->interview_date)->format('d-m-Y') : '-'; })
                ->rawColumns(['full_name', 'candidate_status', 'action'])
                ->make(true);
        }

        return view('frontend.hr.job_applicants.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $boxId = $this->getBoxId();
        return view('frontend.hr.job_applicants.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $boxId = $this->getBoxId();

        $request->validate([
            'full_name' => 'required|string|max:255',
            // 'email_address' => 'required|email|max:255',
        ]);

        $data = $request->except(['_token', 'documents', 'education_details', 'experience_details']);
        $data['box_id'] = $boxId;
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        // Generate Applicant ID
        $count = JobApplicant::ofBox($boxId)->count() + 1;
        $data['applicant_id'] = 'APP-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        $data['application_date'] = now();

        // Handle JSON arrays
        if ($request->has('education_details')) {
            $data['education_details'] = json_decode($request->education_details, true) ?? [];
        }
        if ($request->has('experience_details')) {
            $data['experience_details'] = json_decode($request->experience_details, true) ?? [];
        }

        // Handle File Uploads dynamically
        $fileFields = [
            'profile_photo', 'resume', 'cover_letter', 'passport_photo', 
            'aadhaar_card_doc', 'pan_card_doc', 'experience_letter', 
            'salary_slips', 'educational_certificates', 'portfolio_doc'
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('job_applicants/' . $boxId, 'public');
                $data[$field] = $path;
            }
        }

        $applicant = JobApplicant::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Job Applicant created successfully.',
            'applicant' => $applicant
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($box_slug, $id)
    {
        $boxId = $this->getBoxId();
        $applicant = JobApplicant::ofBox($boxId)->findOrFail($id);

        return view('frontend.hr.job_applicants.form', compact('applicant'));
    }

    public function getDetails($box_slug, $id)
    {
        \Illuminate\Support\Facades\Log::info("getDetails called. ID: $id, BoxID: " . $this->getBoxId());
        $boxId = $this->getBoxId();
        $applicant = JobApplicant::ofBox($boxId)->findOrFail($id);

        return response()->json([
            'success' => true,
            'applicant' => $applicant
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $box_slug, $id)
    {
        $boxId = $this->getBoxId();
        $applicant = JobApplicant::ofBox($boxId)->findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:255',
        ]);

        $data = $request->except(['_token', 'documents', 'education_details', 'experience_details']);
        $data['updated_by'] = Auth::id();

        if ($request->has('education_details')) {
            $data['education_details'] = json_decode($request->education_details, true) ?? [];
        }
        if ($request->has('experience_details')) {
            $data['experience_details'] = json_decode($request->experience_details, true) ?? [];
        }

        $fileFields = [
            'profile_photo', 'resume', 'cover_letter', 'passport_photo', 
            'aadhaar_card_doc', 'pan_card_doc', 'experience_letter', 
            'salary_slips', 'educational_certificates', 'portfolio_doc'
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if exists
                if ($applicant->$field) {
                    Storage::disk('public')->delete($applicant->$field);
                }
                $path = $request->file($field)->store('job_applicants/' . $boxId, 'public');
                $data[$field] = $path;
            }
        }

        $applicant->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Job Applicant updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($box_slug, $id)
    {
        $boxId = $this->getBoxId();
        $applicant = JobApplicant::ofBox($boxId)->findOrFail($id);
        
        // Optionally delete files
        $fileFields = [
            'profile_photo', 'resume', 'cover_letter', 'passport_photo', 
            'aadhaar_card_doc', 'pan_card_doc', 'experience_letter', 
            'salary_slips', 'educational_certificates', 'portfolio_doc'
        ];
        foreach ($fileFields as $field) {
            if ($applicant->$field) {
                Storage::disk('public')->delete($applicant->$field);
            }
        }

        $applicant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job Applicant deleted successfully.'
        ]);
    }
}
