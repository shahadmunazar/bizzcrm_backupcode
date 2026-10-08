@extends('layouts.main')

@section('title', 'Job Applicants')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Job Applicants</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url(request()->route('box_slug').'/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Job Applicants</li>
                    </ul>
                </div>
                <div class="col-auto float-end ms-auto">
                    <a href="javascript:void(0);" class="btn btn-white" data-bs-toggle="modal" data-bs-target="#manageColumnsModal"><i class="ti ti-columns me-1"></i>Manage Columns</a>
                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#add_applicant_modal" class="btn add-btn"><i class="fa fa-plus"></i> Add Applicant</a>
                </div>
            </div>
        </div>
        <!-- /Page Header -->


        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped custom-table job-applicants-datatable mb-0">
                        <thead>
                            <tr>
                                <th>Applicant ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Alternate Mobile</th>
                                <th>DOB</th>
                                <th>Gender</th>
                                <th>Marital Status</th>
                                <th>Father Name</th>
                                <th>Mother Name</th>
                                <th>Nationality</th>
                                <th>Aadhaar</th>
                                <th>PAN</th>
                                <th>Passport</th>
                                <th>Current Address</th>
                                <th>Permanent Address</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Country</th>
                                <th>Pincode</th>
                                <th>Applied Position</th>
                                <th>Preferred Dept</th>
                                <th>Employment Type</th>
                                <th>Preferred Location</th>
                                <th>Expected Joining</th>
                                <th>Notice Period</th>
                                <th>Current Emp. Status</th>
                                <th>Total Experience</th>
                                <th>Relevant Experience</th>
                                <th>Current Company</th>
                                <th>Last Company</th>
                                <th>Current Designation</th>
                                <th>Last Designation</th>
                                <th>Current Salary</th>
                                <th>Expected Salary</th>
                                <th>Last Working Date</th>
                                <th>Technical Skills</th>
                                <th>Soft Skills</th>
                                <th>Certifications</th>
                                <th>Languages</th>
                                <th>Hobbies</th>
                                <th>Strengths</th>
                                <th>Weaknesses</th>
                                <th>Achievements</th>
                                <th>LinkedIn</th>
                                <th>GitHub</th>
                                <th>Portfolio</th>
                                <th>Source</th>
                                <th>Interview Status</th>
                                <th>Interview Date</th>
                                <th>Interview Time</th>
                                <th>Interview Mode</th>
                                <th>Interviewer Name</th>
                                <th>HR Remarks</th>
                                <th>Candidate Status</th>
                                <th>Offer Status</th>
                                <th>Joining Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        
<!-- Manage Columns Modal -->
<div class="modal fade" id="manageColumnsModal" tabindex="-1" aria-labelledby="manageColumnsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageColumnsModalLabel">Manage Columns</h5>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="showAllColumns">Show All</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="hideAllColumns">Hide All</button>
                </div>
                <div class="list-group" id="columnsList" style="max-height: 400px; overflow-y: auto;">
                    <!-- Columns will be populated dynamically -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="applyColumnChanges">Apply Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Applicant Modal -->
        <div class="modal custom-modal fade" id="add_applicant_modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <form action="{{ route('hr.job.applicants.store', ['box_slug' => request()->route('box_slug')]) }}" method="POST" enctype="multipart/form-data" id="addApplicantForm">
                        @csrf
                        
        
    
    <div class="modal-header">
        <h5 class="modal-title">Add Job Applicant</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    
    <div class="modal-body">
                    @csrf
                    
                    <ul class="nav nav-tabs nav-tabs-bottom">
                        <li class="nav-item"><a class="nav-link active" href="#applicant-tab1" data-bs-toggle="tab">Personal Info</a></li>
                        <li class="nav-item"><a class="nav-link" href="#applicant-tab2" data-bs-toggle="tab">Address Info</a></li>
                        <li class="nav-item"><a class="nav-link" href="#applicant-tab3" data-bs-toggle="tab">Job Info</a></li>
                        <li class="nav-item"><a class="nav-link" href="#applicant-tab4" data-bs-toggle="tab">Education & Skills</a></li>
                        <li class="nav-item"><a class="nav-link" href="#applicant-tab5" data-bs-toggle="tab">Experience Details</a></li>
                        <li class="nav-item"><a class="nav-link" href="#applicant-tab6" data-bs-toggle="tab">Documents</a></li>
                        <li class="nav-item"><a class="nav-link" href="#applicant-tab7" data-bs-toggle="tab">Additional Info</a></li>
                        <li class="nav-item"><a class="nav-link" href="#applicant-tab8" data-bs-toggle="tab">HR Details</a></li>
                    </ul>

                    <div class="tab-content">
                        <!-- Tab 1: Personal Info -->
                        <div class="tab-pane show active" id="applicant-tab1">
                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Full Name <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="full_name" value="{{ old('full_name') }}" >
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Email Address</label>
                                        <input class="form-control" type="email" name="email_address" value="{{ old('email_address') }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Mobile Number</label>
                                        <input class="form-control" type="text" name="mobile_number" value="{{ old('mobile_number') }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Alternate Mobile Number</label>
                                        <input class="form-control" type="text" name="alternate_mobile_number" value="{{ old('alternate_mobile_number') }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Date of Birth</label>
                                        <input class="form-control" type="date" name="dob" value="{{ old('dob') }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Gender</label>
                                        <select class="form-select" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Marital Status</label>
                                        <select class="form-select" name="marital_status">
                                            <option value="">Select Status</option>
                                            <option value="Single" {{ old('marital_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                            <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Nationality</label>
                                        <input class="form-control" type="text" name="nationality" value="{{ old('nationality') }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Father's Name</label>
                                        <input class="form-control" type="text" name="father_name" value="{{ old('father_name') }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Mother's Name</label>
                                        <input class="form-control" type="text" name="mother_name" value="{{ old('mother_name') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Aadhaar Number</label>
                                        <input class="form-control" type="text" name="aadhaar_number" value="{{ old('aadhaar_number') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>PAN Number</label>
                                        <input class="form-control" type="text" name="pan_number" value="{{ old('pan_number') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Passport Number</label>
                                        <input class="form-control" type="text" name="passport_number" value="{{ old('passport_number') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Address Info -->
                        <div class="tab-pane" id="applicant-tab2">
                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Current Address</label>
                                        <textarea class="form-control" name="current_address" rows="3">{{ old('current_address') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Permanent Address</label>
                                        <textarea class="form-control" name="permanent_address" rows="3">{{ old('permanent_address') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>City</label>
                                        <input class="form-control" type="text" name="city" value="{{ old('city') }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>State</label>
                                        <input class="form-control" type="text" name="state" value="{{ old('state') }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Country</label>
                                        <input class="form-control" type="text" name="country" value="{{ old('country') }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Pincode</label>
                                        <input class="form-control" type="text" name="pincode" value="{{ old('pincode') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Job Info -->
                        <div class="tab-pane" id="applicant-tab3">
                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Applied Position</label>
                                        <input class="form-control" type="text" name="applied_position" value="{{ old('applied_position') }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Preferred Department</label>
                                        <input class="form-control" type="text" name="preferred_department" value="{{ old('preferred_department') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Employment Type</label>
                                        <select class="form-select" name="employment_type">
                                            <option value="">Select</option>
                                            <option value="Full-Time" {{ old('employment_type') == 'Full-Time' ? 'selected' : '' }}>Full-Time</option>
                                            <option value="Part-Time" {{ old('employment_type') == 'Part-Time' ? 'selected' : '' }}>Part-Time</option>
                                            <option value="Contract" {{ old('employment_type') == 'Contract' ? 'selected' : '' }}>Contract</option>
                                            <option value="Internship" {{ old('employment_type') == 'Internship' ? 'selected' : '' }}>Internship</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Preferred Location</label>
                                        <input class="form-control" type="text" name="preferred_location" value="{{ old('preferred_location') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Expected Joining Date</label>
                                        <input class="form-control" type="date" name="expected_joining_date" value="{{ old('expected_joining_date') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Notice Period</label>
                                        <input class="form-control" type="text" name="notice_period" value="{{ old('notice_period') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Current Employment Status</label>
                                        <input class="form-control" type="text" name="current_employment_status" value="{{ old('current_employment_status') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Total Experience</label>
                                        <input class="form-control" type="text" name="total_experience" value="{{ old('total_experience') }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Current Salary (CTC)</label>
                                        <input class="form-control" type="number" step="0.01" name="current_salary" value="{{ old('current_salary') }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Expected Salary (CTC)</label>
                                        <input class="form-control" type="number" step="0.01" name="expected_salary" value="{{ old('expected_salary') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 4: Education & Skills -->
                        <div class="tab-pane" id="applicant-tab4">
                            <div class="row mt-3">
                                <div class="col-sm-12 mb-3">
                                    <h5>Education Details</h5>
                                    <input type="hidden" name="education_details" id="education_details" value="[]">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="education_table">
                                            <thead>
                            <tr>
                                <th>Applicant ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Alternate Mobile</th>
                                <th>DOB</th>
                                <th>Gender</th>
                                <th>Marital Status</th>
                                <th>Father Name</th>
                                <th>Mother Name</th>
                                <th>Nationality</th>
                                <th>Aadhaar</th>
                                <th>PAN</th>
                                <th>Passport</th>
                                <th>Current Address</th>
                                <th>Permanent Address</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Country</th>
                                <th>Pincode</th>
                                <th>Applied Position</th>
                                <th>Preferred Dept</th>
                                <th>Employment Type</th>
                                <th>Preferred Location</th>
                                <th>Expected Joining</th>
                                <th>Notice Period</th>
                                <th>Current Emp. Status</th>
                                <th>Total Experience</th>
                                <th>Relevant Experience</th>
                                <th>Current Company</th>
                                <th>Last Company</th>
                                <th>Current Designation</th>
                                <th>Last Designation</th>
                                <th>Current Salary</th>
                                <th>Expected Salary</th>
                                <th>Last Working Date</th>
                                <th>Technical Skills</th>
                                <th>Soft Skills</th>
                                <th>Certifications</th>
                                <th>Languages</th>
                                <th>Hobbies</th>
                                <th>Strengths</th>
                                <th>Weaknesses</th>
                                <th>Achievements</th>
                                <th>LinkedIn</th>
                                <th>GitHub</th>
                                <th>Portfolio</th>
                                <th>Source</th>
                                <th>Interview Status</th>
                                <th>Interview Date</th>
                                <th>Interview Time</th>
                                <th>Interview Mode</th>
                                <th>Interviewer Name</th>
                                <th>HR Remarks</th>
                                <th>Candidate Status</th>
                                <th>Offer Status</th>
                                <th>Joining Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                                            <tbody>
                                                <!-- Populated by JS -->
                                            </tbody>
                                        </table>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="addEducationRow()">+ Add Education</button>
                                    </div>
                                </div>
                                <hr>
                                <div class="col-sm-6 mt-3">
                                    <div class="form-group">
                                        <label>Technical Skills</label>
                                        <textarea class="form-control" name="technical_skills" rows="3">{{ old('technical_skills') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-3">
                                    <div class="form-group">
                                        <label>Soft Skills</label>
                                        <textarea class="form-control" name="soft_skills" rows="3">{{ old('soft_skills') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Certifications</label>
                                        <textarea class="form-control" name="certifications" rows="2">{{ old('certifications') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Languages Known</label>
                                        <textarea class="form-control" name="languages_known" rows="2">{{ old('languages_known') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 5: Experience Details -->
                        <div class="tab-pane" id="applicant-tab5">
                            <div class="row mt-3">
                                <div class="col-sm-12 mb-3">
                                    <h5>Experience Details</h5>
                                    <input type="hidden" name="experience_details" id="experience_details" value="[]">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="experience_table">
                                            <thead>
                            <tr>
                                <th>Applicant ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Alternate Mobile</th>
                                <th>DOB</th>
                                <th>Gender</th>
                                <th>Marital Status</th>
                                <th>Father Name</th>
                                <th>Mother Name</th>
                                <th>Nationality</th>
                                <th>Aadhaar</th>
                                <th>PAN</th>
                                <th>Passport</th>
                                <th>Current Address</th>
                                <th>Permanent Address</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Country</th>
                                <th>Pincode</th>
                                <th>Applied Position</th>
                                <th>Preferred Dept</th>
                                <th>Employment Type</th>
                                <th>Preferred Location</th>
                                <th>Expected Joining</th>
                                <th>Notice Period</th>
                                <th>Current Emp. Status</th>
                                <th>Total Experience</th>
                                <th>Relevant Experience</th>
                                <th>Current Company</th>
                                <th>Last Company</th>
                                <th>Current Designation</th>
                                <th>Last Designation</th>
                                <th>Current Salary</th>
                                <th>Expected Salary</th>
                                <th>Last Working Date</th>
                                <th>Technical Skills</th>
                                <th>Soft Skills</th>
                                <th>Certifications</th>
                                <th>Languages</th>
                                <th>Hobbies</th>
                                <th>Strengths</th>
                                <th>Weaknesses</th>
                                <th>Achievements</th>
                                <th>LinkedIn</th>
                                <th>GitHub</th>
                                <th>Portfolio</th>
                                <th>Source</th>
                                <th>Interview Status</th>
                                <th>Interview Date</th>
                                <th>Interview Time</th>
                                <th>Interview Mode</th>
                                <th>Interviewer Name</th>
                                <th>HR Remarks</th>
                                <th>Candidate Status</th>
                                <th>Offer Status</th>
                                <th>Joining Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                                            <tbody>
                                                <!-- Populated by JS -->
                                            </tbody>
                                        </table>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="addExperienceRow()">+ Add Experience</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 6: Documents -->
                        <div class="tab-pane" id="applicant-tab6">
                            <div class="row mt-3">
                                @php
                                    $docFields = [
                                        'profile_photo' => 'Profile Photo',
                                        'resume' => 'Resume / CV',
                                        'cover_letter' => 'Cover Letter',
                                        'passport_photo' => 'Passport Size Photograph',
                                        'aadhaar_card_doc' => 'Aadhaar Card',
                                        'pan_card_doc' => 'PAN Card',
                                        'experience_letter' => 'Experience Letter',
                                        'salary_slips' => 'Salary Slips',
                                        'educational_certificates' => 'Educational Certificates',
                                        'portfolio_doc' => 'Portfolio Document'
                                    ];
                                @endphp

                                @foreach($docFields as $field => $label)
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ $label }}</label>
                                        <input class="form-control" type="file" name="{{ $field }}">
                                        
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Tab 7: Additional Info -->
                        <div class="tab-pane" id="applicant-tab7">
                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Hobbies</label>
                                        <textarea class="form-control" name="hobbies" rows="2">{{ old('hobbies') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Achievements</label>
                                        <textarea class="form-control" name="achievements" rows="2">{{ old('achievements') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>LinkedIn Profile</label>
                                        <input class="form-control" type="text" name="linkedin_profile" value="{{ old('linkedin_profile') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>GitHub Profile</label>
                                        <input class="form-control" type="text" name="github_profile" value="{{ old('github_profile') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Portfolio Website</label>
                                        <input class="form-control" type="text" name="portfolio_website" value="{{ old('portfolio_website') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Reference Name</label>
                                        <input class="form-control" type="text" name="reference_name" value="{{ old('reference_name') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Reference Contact</label>
                                        <input class="form-control" type="text" name="reference_contact" value="{{ old('reference_contact') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Source (How did you hear about us?)</label>
                                        <input class="form-control" type="text" name="source" value="{{ old('source') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 8: HR Details -->
                        <div class="tab-pane" id="applicant-tab8">
                            <div class="row mt-3">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Candidate Status</label>
                                        <select class="form-select" name="candidate_status">
                                            @foreach(['Applied', 'Shortlisted', 'Interview Scheduled', 'Selected', 'Rejected', 'On Hold'] as $cStatus)
                                                <option value="{{ $cStatus }}" {{ old('candidate_status', 'Applied') == $cStatus ? 'selected' : '' }}>{{ $cStatus }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Interview Status</label>
                                        <input class="form-control" type="text" name="interview_status" value="{{ old('interview_status') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Interview Mode</label>
                                        <select class="form-select" name="interview_mode">
                                            <option value="">Select</option>
                                            <option value="Online" {{ old('interview_mode') == 'Online' ? 'selected' : '' }}>Online</option>
                                            <option value="Offline" {{ old('interview_mode') == 'Offline' ? 'selected' : '' }}>Offline</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Interview Date</label>
                                        <input class="form-control" type="date" name="interview_date" value="{{ old('interview_date') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Interview Time</label>
                                        <input class="form-control" type="time" name="interview_time" value="{{ old('interview_time') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Interviewer Name</label>
                                        <input class="form-control" type="text" name="interviewer_name" value="{{ old('interviewer_name') }}">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>HR Remarks</label>
                                        <textarea class="form-control" name="hr_remarks" rows="3">{{ old('hr_remarks') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <div class="submit-section d-flex justify-content-between mt-4 mb-4">
                        <div>
                            <button type="button" class="btn btn-white border prev-tab" data-modal="#add_applicant_modal"><i class="fa fa-arrow-left"></i> Previous</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary cancel-btn" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary submit-btn" id="save-applicant-btn">Save Applicant</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-info text-white next-tab" data-modal="#add_applicant_modal">Next <i class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>






                    </form>
                </div>
            </div>
        </div>
        <!-- /Add Applicant Modal -->


        <!-- Edit Applicant Modal -->
        <div class="modal custom-modal fade" id="edit_applicant_modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <form action="" method="POST" enctype="multipart/form-data" id="editApplicantForm">
                        @csrf
                        <input type="hidden" name="id" value="">
                        
        
    
    <div class="modal-header">
        <h5 class="modal-title">Edit Job Applicant</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    
    <div class="modal-body">
                    @csrf
                    
                    <ul class="nav nav-tabs nav-tabs-bottom">
                        <li class="nav-item"><a class="nav-link active" href="#edit-applicant-tab1" data-bs-toggle="tab">Personal Info</a></li>
                        <li class="nav-item"><a class="nav-link" href="#edit-applicant-tab2" data-bs-toggle="tab">Address Info</a></li>
                        <li class="nav-item"><a class="nav-link" href="#edit-applicant-tab3" data-bs-toggle="tab">Job Info</a></li>
                        <li class="nav-item"><a class="nav-link" href="#edit-applicant-tab4" data-bs-toggle="tab">Education & Skills</a></li>
                        <li class="nav-item"><a class="nav-link" href="#edit-applicant-tab5" data-bs-toggle="tab">Experience Details</a></li>
                        <li class="nav-item"><a class="nav-link" href="#edit-applicant-tab6" data-bs-toggle="tab">Documents</a></li>
                        <li class="nav-item"><a class="nav-link" href="#edit-applicant-tab7" data-bs-toggle="tab">Additional Info</a></li>
                        <li class="nav-item"><a class="nav-link" href="#edit-applicant-tab8" data-bs-toggle="tab">HR Details</a></li>
                    </ul>

                    <div class="tab-content">
                        <!-- Tab 1: Personal Info -->
                        <div class="tab-pane show active" id="edit-applicant-tab1">
                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Full Name <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="full_name" value="" >
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Email Address</label>
                                        <input class="form-control" type="email" name="email_address" value="">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Mobile Number</label>
                                        <input class="form-control" type="text" name="mobile_number" value="">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Alternate Mobile Number</label>
                                        <input class="form-control" type="text" name="alternate_mobile_number" value="">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Date of Birth</label>
                                        <input class="form-control" type="date" name="dob" value="">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Gender</label>
                                        <select class="form-select" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="Male" >Male</option>
                                            <option value="Female" >Female</option>
                                            <option value="Other" >Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Marital Status</label>
                                        <select class="form-select" name="marital_status">
                                            <option value="">Select Status</option>
                                            <option value="Single" >Single</option>
                                            <option value="Married" >Married</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Nationality</label>
                                        <input class="form-control" type="text" name="nationality" value="">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Father's Name</label>
                                        <input class="form-control" type="text" name="father_name" value="">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Mother's Name</label>
                                        <input class="form-control" type="text" name="mother_name" value="">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Aadhaar Number</label>
                                        <input class="form-control" type="text" name="aadhaar_number" value="">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>PAN Number</label>
                                        <input class="form-control" type="text" name="pan_number" value="">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Passport Number</label>
                                        <input class="form-control" type="text" name="passport_number" value="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Address Info -->
                        <div class="tab-pane" id="edit-applicant-tab2">
                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Current Address</label>
                                        <textarea class="form-control" name="current_address" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Permanent Address</label>
                                        <textarea class="form-control" name="permanent_address" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>City</label>
                                        <input class="form-control" type="text" name="city" value="">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>State</label>
                                        <input class="form-control" type="text" name="state" value="">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Country</label>
                                        <input class="form-control" type="text" name="country" value="">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Pincode</label>
                                        <input class="form-control" type="text" name="pincode" value="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Job Info -->
                        <div class="tab-pane" id="edit-applicant-tab3">
                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Applied Position</label>
                                        <input class="form-control" type="text" name="applied_position" value="">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Preferred Department</label>
                                        <input class="form-control" type="text" name="preferred_department" value="">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Employment Type</label>
                                        <select class="form-select" name="employment_type">
                                            <option value="">Select</option>
                                            <option value="Full-Time" >Full-Time</option>
                                            <option value="Part-Time" >Part-Time</option>
                                            <option value="Contract" >Contract</option>
                                            <option value="Internship" >Internship</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Preferred Location</label>
                                        <input class="form-control" type="text" name="preferred_location" value="">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Expected Joining Date</label>
                                        <input class="form-control" type="date" name="expected_joining_date" value="">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Notice Period</label>
                                        <input class="form-control" type="text" name="notice_period" value="">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Current Employment Status</label>
                                        <input class="form-control" type="text" name="current_employment_status" value="">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Total Experience</label>
                                        <input class="form-control" type="text" name="total_experience" value="">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Current Salary (CTC)</label>
                                        <input class="form-control" type="number" step="0.01" name="current_salary" value="">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Expected Salary (CTC)</label>
                                        <input class="form-control" type="number" step="0.01" name="expected_salary" value="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 4: Education & Skills -->
                        <div class="tab-pane" id="edit-applicant-tab4">
                            <div class="row mt-3">
                                <div class="col-sm-12 mb-3">
                                    <h5>Education Details</h5>
                                    <input type="hidden" name="education_details" id="edit_education_details" value="[]">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="edit_education_table">
                                            <thead>
                            <tr>
                                <th>Applicant ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Alternate Mobile</th>
                                <th>DOB</th>
                                <th>Gender</th>
                                <th>Marital Status</th>
                                <th>Father Name</th>
                                <th>Mother Name</th>
                                <th>Nationality</th>
                                <th>Aadhaar</th>
                                <th>PAN</th>
                                <th>Passport</th>
                                <th>Current Address</th>
                                <th>Permanent Address</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Country</th>
                                <th>Pincode</th>
                                <th>Applied Position</th>
                                <th>Preferred Dept</th>
                                <th>Employment Type</th>
                                <th>Preferred Location</th>
                                <th>Expected Joining</th>
                                <th>Notice Period</th>
                                <th>Current Emp. Status</th>
                                <th>Total Experience</th>
                                <th>Relevant Experience</th>
                                <th>Current Company</th>
                                <th>Last Company</th>
                                <th>Current Designation</th>
                                <th>Last Designation</th>
                                <th>Current Salary</th>
                                <th>Expected Salary</th>
                                <th>Last Working Date</th>
                                <th>Technical Skills</th>
                                <th>Soft Skills</th>
                                <th>Certifications</th>
                                <th>Languages</th>
                                <th>Hobbies</th>
                                <th>Strengths</th>
                                <th>Weaknesses</th>
                                <th>Achievements</th>
                                <th>LinkedIn</th>
                                <th>GitHub</th>
                                <th>Portfolio</th>
                                <th>Source</th>
                                <th>Interview Status</th>
                                <th>Interview Date</th>
                                <th>Interview Time</th>
                                <th>Interview Mode</th>
                                <th>Interviewer Name</th>
                                <th>HR Remarks</th>
                                <th>Candidate Status</th>
                                <th>Offer Status</th>
                                <th>Joining Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                                            <tbody>
                                                <!-- Populated by JS -->
                                            </tbody>
                                        </table>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="addEditEducationRow()">+ Add Education</button>
                                    </div>
                                </div>
                                <hr>
                                <div class="col-sm-6 mt-3">
                                    <div class="form-group">
                                        <label>Technical Skills</label>
                                        <textarea class="form-control" name="technical_skills" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-3">
                                    <div class="form-group">
                                        <label>Soft Skills</label>
                                        <textarea class="form-control" name="soft_skills" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Certifications</label>
                                        <textarea class="form-control" name="certifications" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Languages Known</label>
                                        <textarea class="form-control" name="languages_known" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 5: Experience Details -->
                        <div class="tab-pane" id="edit-applicant-tab5">
                            <div class="row mt-3">
                                <div class="col-sm-12 mb-3">
                                    <h5>Experience Details</h5>
                                    <input type="hidden" name="experience_details" id="edit_experience_details" value="[]">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="edit_experience_table">
                                            <thead>
                            <tr>
                                <th>Applicant ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Alternate Mobile</th>
                                <th>DOB</th>
                                <th>Gender</th>
                                <th>Marital Status</th>
                                <th>Father Name</th>
                                <th>Mother Name</th>
                                <th>Nationality</th>
                                <th>Aadhaar</th>
                                <th>PAN</th>
                                <th>Passport</th>
                                <th>Current Address</th>
                                <th>Permanent Address</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Country</th>
                                <th>Pincode</th>
                                <th>Applied Position</th>
                                <th>Preferred Dept</th>
                                <th>Employment Type</th>
                                <th>Preferred Location</th>
                                <th>Expected Joining</th>
                                <th>Notice Period</th>
                                <th>Current Emp. Status</th>
                                <th>Total Experience</th>
                                <th>Relevant Experience</th>
                                <th>Current Company</th>
                                <th>Last Company</th>
                                <th>Current Designation</th>
                                <th>Last Designation</th>
                                <th>Current Salary</th>
                                <th>Expected Salary</th>
                                <th>Last Working Date</th>
                                <th>Technical Skills</th>
                                <th>Soft Skills</th>
                                <th>Certifications</th>
                                <th>Languages</th>
                                <th>Hobbies</th>
                                <th>Strengths</th>
                                <th>Weaknesses</th>
                                <th>Achievements</th>
                                <th>LinkedIn</th>
                                <th>GitHub</th>
                                <th>Portfolio</th>
                                <th>Source</th>
                                <th>Interview Status</th>
                                <th>Interview Date</th>
                                <th>Interview Time</th>
                                <th>Interview Mode</th>
                                <th>Interviewer Name</th>
                                <th>HR Remarks</th>
                                <th>Candidate Status</th>
                                <th>Offer Status</th>
                                <th>Joining Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                                            <tbody>
                                                <!-- Populated by JS -->
                                            </tbody>
                                        </table>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="addEditExperienceRow()">+ Add Experience</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 6: Documents -->
                        <div class="tab-pane" id="edit-applicant-tab6">
                            <div class="row mt-3">
                                @php
                                    $docFields = [
                                        'profile_photo' => 'Profile Photo',
                                        'resume' => 'Resume / CV',
                                        'cover_letter' => 'Cover Letter',
                                        'passport_photo' => 'Passport Size Photograph',
                                        'aadhaar_card_doc' => 'Aadhaar Card',
                                        'pan_card_doc' => 'PAN Card',
                                        'experience_letter' => 'Experience Letter',
                                        'salary_slips' => 'Salary Slips',
                                        'educational_certificates' => 'Educational Certificates',
                                        'portfolio_doc' => 'Portfolio Document'
                                    ];
                                @endphp

                                @foreach($docFields as $field => $label)
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{ $label }}</label>
                                        <input class="form-control" type="file" name="{{ $field }}">
                                        <div class="edit-file-link mt-1" id="edit_file_link_{{ $field }}"></div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Tab 7: Additional Info -->
                        <div class="tab-pane" id="edit-applicant-tab7">
                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Hobbies</label>
                                        <textarea class="form-control" name="hobbies" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Achievements</label>
                                        <textarea class="form-control" name="achievements" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>LinkedIn Profile</label>
                                        <input class="form-control" type="text" name="linkedin_profile" value="">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>GitHub Profile</label>
                                        <input class="form-control" type="text" name="github_profile" value="">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Portfolio Website</label>
                                        <input class="form-control" type="text" name="portfolio_website" value="">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Reference Name</label>
                                        <input class="form-control" type="text" name="reference_name" value="">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Reference Contact</label>
                                        <input class="form-control" type="text" name="reference_contact" value="">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Source (How did you hear about us?)</label>
                                        <input class="form-control" type="text" name="source" value="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 8: HR Details -->
                        <div class="tab-pane" id="edit-applicant-tab8">
                            <div class="row mt-3">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Candidate Status</label>
                                        <select class="form-select" name="candidate_status">
                                            @foreach(['Applied', 'Shortlisted', 'Interview Scheduled', 'Selected', 'Rejected', 'On Hold'] as $cStatus)
                                                <option value="{{ $cStatus }}" >{{ $cStatus }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Interview Status</label>
                                        <input class="form-control" type="text" name="interview_status" value="">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Interview Mode</label>
                                        <select class="form-select" name="interview_mode">
                                            <option value="">Select</option>
                                            <option value="Online" >Online</option>
                                            <option value="Offline" >Offline</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Interview Date</label>
                                        <input class="form-control" type="date" name="interview_date" value="">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Interview Time</label>
                                        <input class="form-control" type="time" name="interview_time" value="">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Interviewer Name</label>
                                        <input class="form-control" type="text" name="interviewer_name" value="">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>HR Remarks</label>
                                        <textarea class="form-control" name="hr_remarks" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <div class="submit-section d-flex justify-content-between mt-4 mb-4">
                        <div>
                            <button type="button" class="btn btn-white border prev-tab" data-modal="#edit_applicant_modal"><i class="fa fa-arrow-left"></i> Previous</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary cancel-btn" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary submit-btn" id="edit-save-applicant-btn">Update Applicant</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-info text-white next-tab" data-modal="#edit_applicant_modal">Next <i class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>






                    </form>
                </div>
            </div>
        </div>
        <!-- /Edit Applicant Modal -->


    </div>
</div>



@push('scripts')
<script>
    // Tab Navigation
    $('.next-tab').on('click', function() {
        const $modal = $($(this).data('modal'));
        const $activeTab = $modal.find('.nav-link.active');
        const $nextTab = $activeTab.parent().next().find('.nav-link');
        if ($nextTab.length) {
            const tab = new bootstrap.Tab($nextTab[0]);
            tab.show();
        }
    });

    $('.prev-tab').on('click', function() {
        const $modal = $($(this).data('modal'));
        const $activeTab = $modal.find('.nav-link.active');
        const $prevTab = $activeTab.parent().prev().find('.nav-link');
        if ($prevTab.length) {
            const tab = new bootstrap.Tab($prevTab[0]);
            tab.show();
        }
    });

    // Reset modals on close so they go back to the first tab
    $('#add_applicant_modal, #edit_applicant_modal').on('hidden.bs.modal', function () {
        const firstTab = $(this).find('.nav-link').first()[0];
        if (firstTab) {
            new bootstrap.Tab(firstTab).show();
        }
    });

    // Dynamic Rows Logic
    window.addEducationData = [];
    window.addExperienceData = [];
    window.editEducationData = [];
    window.editExperienceData = [];

    // --- ADD MODAL FUNCTIONS ---
    function renderAddEducation() {
        let html = '';
        addEducationData.forEach((item, index) => {
            html += `
            <tr>
                <td><input type="text" class="form-control form-control-sm" value="${item.degree || ''}" onchange="updateAddEdu(${index}, 'degree', this.value)"></td>
                <td><input type="text" class="form-control form-control-sm" value="${item.specialization || ''}" onchange="updateAddEdu(${index}, 'specialization', this.value)"></td>
                <td><input type="text" class="form-control form-control-sm" value="${item.college || ''}" onchange="updateAddEdu(${index}, 'college', this.value)"></td>
                <td><input type="text" class="form-control form-control-sm" value="${item.passing_year || ''}" onchange="updateAddEdu(${index}, 'passing_year', this.value)"></td>
                <td><input type="text" class="form-control form-control-sm" value="${item.percentage || ''}" onchange="updateAddEdu(${index}, 'percentage', this.value)"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeAddEdu(${index})"><i class="fa fa-trash"></i></button></td>
            </tr>`;
        });
        $('#education_table tbody').html(html);
        $('#education_details').val(JSON.stringify(addEducationData));
    }
    function addEducationRow() { addEducationData.push({ degree: '', specialization: '', college: '', passing_year: '', percentage: '' }); renderAddEducation(); }
    function updateAddEdu(index, field, value) { addEducationData[index][field] = value; renderAddEducation(); }
    function removeAddEdu(index) { addEducationData.splice(index, 1); renderAddEducation(); }

    function renderAddExperience() {
        let html = '';
        addExperienceData.forEach((item, index) => {
            html += `
            <tr>
                <td><input type="text" class="form-control form-control-sm" value="${item.company || ''}" onchange="updateAddExp(${index}, 'company', this.value)"></td>
                <td><input type="text" class="form-control form-control-sm" value="${item.designation || ''}" onchange="updateAddExp(${index}, 'designation', this.value)"></td>
                <td><input type="date" class="form-control form-control-sm" value="${item.from_date || ''}" onchange="updateAddExp(${index}, 'from_date', this.value)"></td>
                <td><input type="date" class="form-control form-control-sm" value="${item.to_date || ''}" onchange="updateAddExp(${index}, 'to_date', this.value)"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeAddExp(${index})"><i class="fa fa-trash"></i></button></td>
            </tr>`;
        });
        $('#experience_table tbody').html(html);
        $('#experience_details').val(JSON.stringify(addExperienceData));
    }
    function addExperienceRow() { addExperienceData.push({ company: '', designation: '', from_date: '', to_date: '' }); renderAddExperience(); }
    function updateAddExp(index, field, value) { addExperienceData[index][field] = value; renderAddExperience(); }
    function removeAddExp(index) { addExperienceData.splice(index, 1); renderAddExperience(); }

    // --- EDIT MODAL FUNCTIONS ---
    function renderEditEducation() {
        let html = '';
        editEducationData.forEach((item, index) => {
            html += `
            <tr>
                <td><input type="text" class="form-control form-control-sm" value="${item.degree || ''}" onchange="updateEditEdu(${index}, 'degree', this.value)"></td>
                <td><input type="text" class="form-control form-control-sm" value="${item.specialization || ''}" onchange="updateEditEdu(${index}, 'specialization', this.value)"></td>
                <td><input type="text" class="form-control form-control-sm" value="${item.college || ''}" onchange="updateEditEdu(${index}, 'college', this.value)"></td>
                <td><input type="text" class="form-control form-control-sm" value="${item.passing_year || ''}" onchange="updateEditEdu(${index}, 'passing_year', this.value)"></td>
                <td><input type="text" class="form-control form-control-sm" value="${item.percentage || ''}" onchange="updateEditEdu(${index}, 'percentage', this.value)"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeEditEdu(${index})"><i class="fa fa-trash"></i></button></td>
            </tr>`;
        });
        $('#edit_education_table tbody').html(html);
        $('#edit_education_details').val(JSON.stringify(editEducationData));
    }
    function addEditEducationRow() { editEducationData.push({ degree: '', specialization: '', college: '', passing_year: '', percentage: '' }); renderEditEducation(); }
    function updateEditEdu(index, field, value) { editEducationData[index][field] = value; renderEditEducation(); }
    function removeEditEdu(index) { editEducationData.splice(index, 1); renderEditEducation(); }

    function renderEditExperience() {
        let html = '';
        editExperienceData.forEach((item, index) => {
            html += `
            <tr>
                <td><input type="text" class="form-control form-control-sm" value="${item.company || ''}" onchange="updateEditExp(${index}, 'company', this.value)"></td>
                <td><input type="text" class="form-control form-control-sm" value="${item.designation || ''}" onchange="updateEditExp(${index}, 'designation', this.value)"></td>
                <td><input type="date" class="form-control form-control-sm" value="${item.from_date || ''}" onchange="updateEditExp(${index}, 'from_date', this.value)"></td>
                <td><input type="date" class="form-control form-control-sm" value="${item.to_date || ''}" onchange="updateEditExp(${index}, 'to_date', this.value)"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeEditExp(${index})"><i class="fa fa-trash"></i></button></td>
            </tr>`;
        });
        $('#edit_experience_table tbody').html(html);
        $('#edit_experience_details').val(JSON.stringify(editExperienceData));
    }
    function addEditExperienceRow() { editExperienceData.push({ company: '', designation: '', from_date: '', to_date: '' }); renderEditExperience(); }
    function updateEditExp(index, field, value) { editExperienceData[index][field] = value; renderEditExperience(); }
    function removeEditExp(index) { editExperienceData.splice(index, 1); renderEditExperience(); }

    $(document).ready(function() {
        renderAddEducation();
        renderAddExperience();
        renderEditEducation();
        renderEditExperience();
    });

    // --- FORM SUBMISSIONS ---
    $('#addApplicantForm').on('submit', function(e) {
        e.preventDefault();
        $('#education_details').val(JSON.stringify(addEducationData));
        $('#experience_details').val(JSON.stringify(addExperienceData));

        let form = $(this);
        let formData = new FormData(form[0]);
        let btn = $('#save-applicant-btn');
        let originalText = btn.html();
        
        btn.html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        btn.prop('disabled', true);
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                btn.html(originalText);
                btn.prop('disabled', false);
                if(response.success) {
                    $('#add_applicant_modal').modal('hide');
                    showToast(response.message, 'success');
                    window.location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                btn.html(originalText);
                btn.prop('disabled', false);
                alert('Validation error or server error occurred. Please check all fields.');
            }
        });
    });

    $('#editApplicantForm').on('submit', function(e) {
        e.preventDefault();
        $('#edit_education_details').val(JSON.stringify(editEducationData));
        $('#edit_experience_details').val(JSON.stringify(editExperienceData));

        let form = $(this);
        let formData = new FormData(form[0]);
        let btn = $('#edit-save-applicant-btn');
        let originalText = btn.html();
        
        btn.html('<i class="fa fa-spinner fa-spin"></i> Updating...');
        btn.prop('disabled', true);

        // We will define the update route dynamically in fetchApplicantDetails
        let actionUrl = form.attr('action');
        
        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                btn.html(originalText);
                btn.prop('disabled', false);
                if(response.success) {
                    $('#edit_applicant_modal').modal('hide');
                    showToast(response.message, 'success');
                    window.location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                btn.html(originalText);
                btn.prop('disabled', false);
                alert('Validation error or server error occurred. Please check all fields.');
            }
        });
    });

    
    // Manage Columns Logic
    let table = null;
    let columnDefinitions = [];

    function initializeColumnDefinitions() {
        const thead = document.querySelector('.job-applicants-datatable thead tr');
        if (thead) {
            const thElements = Array.from(thead.querySelectorAll('th'));
            columnDefinitions = thElements.map((th, index) => ({
                index: index,
                name: th.textContent.trim() || `Column ${index + 1}`,
                // By default, make first 7 columns visible, rest hidden
                visible: index < 7 || index === thElements.length - 1
            }));
            // Always keep the Action column visible
            columnDefinitions[columnDefinitions.length - 1].alwaysVisible = true;
        }
    }

    function saveColumnPreferences() {
        const visibleColumns = columnDefinitions.filter(c => c.visible).map(c => c.index);
        localStorage.setItem('jobApplicantsTableColumns', JSON.stringify(visibleColumns));
    }

    function loadColumnPreferences(callback) {
        const stored = localStorage.getItem('jobApplicantsTableColumns');
        initializeColumnDefinitions(); // Default initialization

        if (stored) {
            try {
                const visibleColumns = JSON.parse(stored);
                columnDefinitions.forEach(col => {
                    col.visible = visibleColumns.includes(col.index);
                });
            } catch (e) {
                console.error("Error loading column preferences");
            }
        }
        
        // Ensure Action column is always visible
        columnDefinitions[columnDefinitions.length - 1].visible = true;
        
        if (callback) callback();
    }

    function populateColumnsModal() {
        const columnsList = $('#columnsList');
        columnsList.empty();

        columnDefinitions.forEach(col => {
            if (col.alwaysVisible) return; // Skip Action column from toggling

            const listItem = $(`
                <div class="list-group-item">
                    <div class="form-check">
                        <input class="form-check-input column-toggle" type="checkbox"
                               data-column-index="${col.index}"
                               id="col_${col.index}"
                               ${col.visible ? 'checked' : ''}>
                        <label class="form-check-label" for="col_${col.index}">
                            ${col.name}
                        </label>
                    </div>
                </div>
            `);
            columnsList.append(listItem);
        });
    }

    // Load preferences BEFORE table initialization
    loadColumnPreferences(function() {
        // Destroy existing datatable if it exists (BizzCRM's default script might have initialized it)
        if ($.fn.DataTable.isDataTable('.datatable')) {
            $('.job-applicants-datatable').DataTable().destroy();
        }

        // Initialize DataTable with specific column visibility
        table = $('.job-applicants-datatable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('hr.job.applicants.data', ['box_slug' => request()->route('box_slug')]) }}",
                "type": "POST",
                "headers": {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            "columns": [
                { data: 'applicant_id', name: 'applicant_id' },
                { data: 'full_name', name: 'full_name' },
                { data: 'email_address', name: 'email_address' },
                { data: 'mobile_number', name: 'mobile_number' },
                { data: 'alternate_mobile_number', name: 'alternate_mobile_number' },
                { data: 'dob', name: 'dob' },
                { data: 'gender', name: 'gender' },
                { data: 'marital_status', name: 'marital_status' },
                { data: 'father_name', name: 'father_name' },
                { data: 'mother_name', name: 'mother_name' },
                { data: 'nationality', name: 'nationality' },
                { data: 'aadhaar_number', name: 'aadhaar_number' },
                { data: 'pan_number', name: 'pan_number' },
                { data: 'passport_number', name: 'passport_number' },
                { data: 'current_address', name: 'current_address' },
                { data: 'permanent_address', name: 'permanent_address' },
                { data: 'city', name: 'city' },
                { data: 'state', name: 'state' },
                { data: 'country', name: 'country' },
                { data: 'pincode', name: 'pincode' },
                { data: 'applied_position', name: 'applied_position' },
                { data: 'preferred_department', name: 'preferred_department' },
                { data: 'employment_type', name: 'employment_type' },
                { data: 'preferred_location', name: 'preferred_location' },
                { data: 'expected_joining_date', name: 'expected_joining_date' },
                { data: 'notice_period', name: 'notice_period' },
                { data: 'current_employment_status', name: 'current_employment_status' },
                { data: 'total_experience', name: 'total_experience' },
                { data: 'relevant_experience', name: 'relevant_experience' },
                { data: 'current_company_name', name: 'current_company_name' },
                { data: 'last_company_name', name: 'last_company_name' },
                { data: 'current_designation', name: 'current_designation' },
                { data: 'last_designation', name: 'last_designation' },
                { data: 'current_salary', name: 'current_salary' },
                { data: 'expected_salary', name: 'expected_salary' },
                { data: 'last_working_date', name: 'last_working_date' },
                { data: 'technical_skills', name: 'technical_skills' },
                { data: 'soft_skills', name: 'soft_skills' },
                { data: 'certifications', name: 'certifications' },
                { data: 'languages_known', name: 'languages_known' },
                { data: 'hobbies', name: 'hobbies' },
                { data: 'strengths', name: 'strengths' },
                { data: 'weaknesses', name: 'weaknesses' },
                { data: 'achievements', name: 'achievements' },
                { data: 'linkedin_profile', name: 'linkedin_profile' },
                { data: 'github_profile', name: 'github_profile' },
                { data: 'portfolio_website', name: 'portfolio_website' },
                { data: 'source', name: 'source' },
                { data: 'interview_status', name: 'interview_status' },
                { data: 'interview_date', name: 'interview_date' },
                { data: 'interview_time', name: 'interview_time' },
                { data: 'interview_mode', name: 'interview_mode' },
                { data: 'interviewer_name', name: 'interviewer_name' },
                { data: 'hr_remarks', name: 'hr_remarks' },
                { data: 'candidate_status', name: 'candidate_status' },
                { data: 'offer_letter_status', name: 'offer_letter_status' },
                { data: 'joining_status', name: 'joining_status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            "bFilter": true,
            "bLengthChange": true,
            "scrollX": true,
            "columnDefs": columnDefinitions.map(col => ({
                "targets": [col.index],
                "visible": col.visible
            }))
        });
    });

    $('#manageColumnsModal').on('show.bs.modal', function() {
        populateColumnsModal();
    });

    $('#showAllColumns').on('click', function() {
        $('.column-toggle').prop('checked', true);
    });

    $('#hideAllColumns').on('click', function() {
        $('.column-toggle').prop('checked', false);
    });

    $('#applyColumnChanges').on('click', function() {
        $('.column-toggle').each(function() {
            const columnIndex = parseInt($(this).data('column-index'));
            const isVisible = $(this).is(':checked');
            const col = columnDefinitions.find(c => c.index === columnIndex);
            if (col) {
                col.visible = isVisible;
            }
        });

        saveColumnPreferences();

        if (table) {
            columnDefinitions.forEach(col => {
                table.column(col.index).visible(col.visible, false);
            });
            table.columns.adjust().draw(false);
        }

        $('#manageColumnsModal').modal('hide');
    });


    // --- FETCH EDIT DATA ---
    function fetchApplicantDetails(id) {
        $.ajax({
            url: `{{ route('hr.job.applicants.get-details', ['box_slug' => request()->route('box_slug'), 'id' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', id),
            type: 'GET',
            success: function(response) {
                if(response.success && response.applicant) {
                    let applicant = response.applicant;
                    let form = $('#editApplicantForm');
                    
                    form[0].reset(); // Reset form first
                    form.attr('action', `{{ route('hr.job.applicants.update', ['box_slug' => request()->route('box_slug'), 'id' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', id));
                    form.find('input[name="id"]').val(applicant.id);

                    // Populate simple fields
                    $.each(applicant, function(key, value) {
                        if (value !== null && typeof value !== 'object') {
                            let input = form.find(`[name="${key}"]`);
                            if (input.length > 0) {
                                if (input.attr('type') === 'file') {
                                    return true; // Skip file inputs
                                }
                                // Format dates if it's a date input
                                if (input.attr('type') === 'date' && value.includes('T')) {
                                    value = value.split('T')[0];
                                }
                                input.val(value);
                            }
                        }
                    });

                    // Set Selects manually if needed ($.each might handle it but let's be safe)
                    if(applicant.gender) form.find('select[name="gender"]').val(applicant.gender);
                    if(applicant.marital_status) form.find('select[name="marital_status"]').val(applicant.marital_status);
                    if(applicant.employment_type) form.find('select[name="employment_type"]').val(applicant.employment_type);
                    if(applicant.candidate_status) form.find('select[name="candidate_status"]').val(applicant.candidate_status);
                    if(applicant.interview_mode) form.find('select[name="interview_mode"]').val(applicant.interview_mode);

                    // JSON Data
                    try { editEducationData = typeof applicant.education_details === 'string' ? JSON.parse(applicant.education_details) : (applicant.education_details || []); } catch(e) { editEducationData = []; }
                    try { editExperienceData = typeof applicant.experience_details === 'string' ? JSON.parse(applicant.experience_details) : (applicant.experience_details || []); } catch(e) { editExperienceData = []; }
                    
                    renderEditEducation();
                    renderEditExperience();

                    // File Links
                    let docFields = ['profile_photo', 'resume', 'cover_letter', 'passport_photo', 'aadhaar_card_doc', 'pan_card_doc', 'experience_letter', 'salary_slips', 'educational_certificates', 'portfolio_doc'];
                    docFields.forEach(field => {
                        let linkDiv = $(`#edit_file_link_${field}`);
                        if(applicant[field]) {
                            let assetUrl = `{{ asset('storage') }}/${applicant[field]}`;
                            linkDiv.html(`<small class="text-success">Current file: <a href="${assetUrl}" target="_blank">View</a></small>`);
                        } else {
                            linkDiv.empty();
                        }
                    });

                    $('#edit_applicant_modal').modal('show');
                } else {
                    showToast('Failed to load applicant details.', 'danger');
                }
            },
            error: function() {
                showToast('Error fetching applicant data.', 'danger');
            }
        });
    }

    function deleteApplicantAjax(url) {
        $.ajax({
            url: url,
            type: 'GET',
            cache: false,
            success: function(response) {
                if(response.success) {
                    $('.modal').modal('hide');
                    showToast(response.message, 'success');
                    window.location.reload();
                } else {
                    showToast('Error deleting applicant.', 'danger');
                }
            },
            error: function() {
                showToast('Failed to delete applicant.', 'danger');
            }
        });
    }

    // Update Wizard Buttons on Tab Change
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        let $activeTab = $(e.target);
        let $nav = $activeTab.closest('.nav-tabs');
        let $modal = $activeTab.closest('.modal');
        
        let allTabs = $nav.find('a[data-bs-toggle="tab"]');
        let currentIndex = allTabs.index($activeTab);
        let totalTabs = allTabs.length;
        
        let $prevBtn = $modal.find('.prev-tab');
        let $nextBtn = $modal.find('.next-tab');
        let $saveBtn = $modal.find('.submit-btn');
        
        if (currentIndex === 0) {
            $prevBtn.hide();
        } else {
            $prevBtn.show();
        }
        
        if (currentIndex >= totalTabs - 1) {
            $nextBtn.hide();
            $saveBtn.show();
        } else {
            $nextBtn.show();
            $saveBtn.hide();
        }
    });

    // Run once on load to set initial state
    $(document).ready(function() {
        $('#add_applicant_modal .submit-btn').hide();
        $('#edit_applicant_modal .submit-btn').hide();
        $('#add_applicant_modal .prev-tab').hide();
        $('#edit_applicant_modal .prev-tab').hide();
    });

</script>
@endpush

@endsection
