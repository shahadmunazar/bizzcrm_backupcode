@php
    $modalAuthUser = \Illuminate\Support\Facades\Auth::user();
    $modalBoxUser = $modalAuthUser ? \App\Models\BoxUser::where('user_id', $modalAuthUser->id)->first() : null;
    $modalBox = $modalBoxUser ? \App\Models\Box::find($modalBoxUser->box_id) : null;
    $modalCrmLimit = $modalBox ? ($modalBox->total_users ?? 0) : 0;
    $modalWhatsappLimit = $modalAuthUser ? \App\Helpers\SubscriptionHelper::getWhatsAppAgentLimit($modalAuthUser) : 0;
@endphp
<div class="modal fade" id="global_add_employee">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <h4 class="modal-title me-2">Add New Employee </h4><span>Employee ID : <span
                            id="top_header_employee"></span> </span>
                </div>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="globalEmployeeForm" method="POST" enctype="multipart/form-data">
                <div class="contact-grids-tab">
                    <ul class="nav nav-underline" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="global-info-tab" data-bs-toggle="tab"
                                data-bs-target="#global-basic-info" type="button" role="tab"
                                aria-selected="true">Basic Information </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="global-address-tab" data-bs-toggle="tab" data-bs-target="#global-address"
                                type="button" role="tab" aria-selected="false">Permissions </button>
                        </li>
                    </ul>
                </div>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="global-basic-info" role="tabpanel"
                        aria-labelledby="info-tab" tabindex="0">
                        <div class="modal-body pb-0 ">
                            <div class="row">
                                <div class="col-md-12">
                                    <div
                                        class="d-flex align-items-center flex-wrap row-gap-3 bg-light w-100 rounded p-3 mb-4">
                                        <div
                                            class="d-flex align-items-center justify-content-center avatar avatar-xxl rounded-circle border border-dashed me-2 flex-shrink-0 text-dark frames">
                                            <div class="uploaded_image"><i class="ti ti-photo text-gray-2 fs-16"></i></div>
                                        </div>
                                        <div class="profile-upload my-crop">
                                            <div class="mb-2">
                                                <h6 class="mb-1">Upload Profile Image </h6>
                                                <p class="fs-12">Image should be below 4 mb </p>
                                            </div>
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn btn btn-sm btn-primary me-2">
                                                    Upload
                                                    <input type="file" name="profile_image"
                                                        class="form-control image-sign" multiple="" />
                                                </div>
                                                <a href="javascript:void(0);" class="btn btn-light btn-sm">Cancel </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal uploadimageModal" id="add_uploadimageModal">
                                        <div class="modal-dialog">
                                            <div class="modal-content text-center">
                                            <div class="modal-body" style="max-height:inherit;">
                                                <div class="image_demo"></div>
                                                <button type="button" class="btn w-100 btn-success crop_image">
                                                Upload
                                                </button>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>









                           <!-- <div class="col-md-12">
                                    <div
                                        class="d-flex align-items-center flex-wrap row-gap-3 bg-light w-100 rounded p-3 mb-4">
                                        <div
                                            class="d-flex align-items-center justify-content-center avatar avatar-xxl rounded-circle border border-dashed me-2 flex-shrink-0 text-dark frames">
                                            <i class="ti ti-photo text-gray-2 fs-16"></i>
                                        </div>
                                        <div class="profile-upload my-crop">
                                            <div class="mb-2">
                                                <h6 class="mb-1">Upload Profile Image </h6>
                                                <p class="fs-12">Image should be below 4 mb </p>
                                            </div>
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn btn btn-sm btn-primary me-2">
                                                    Upload
                                                    <input type="file" name="profile_image"
                                                        class="form-control image-sign" multiple="" />
                                                </div>
                                                <a href="javascript:void(0);" class="btn btn-light btn-sm">Cancel </a>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->






                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">First Name <span class="text-danger"> * </span></label>
                                        <input type="text" class="form-control" name="name"
                                            placeholder="Enter First Name" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Last Name </label>
                                        <input type="text" class="form-control" name="last_name"
                                            placeholder="Enter Last Name" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Employee ID <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="employee_id" id="employee_id_input"
                                            class="form-control" placeholder="Employee ID" readonly />
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email <span class="text-danger"> * </span></label>
                                        <input type="email" name="email" class="form-control"
                                            placeholder="Email" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Mobile Number <span class="text-danger"> *
                                            </span></label>
                                        <input type="text" name="phone" id="global_phone" class="form-control"
                                            placeholder="Mobile Number" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Joining Date <span class="text-danger"> *
                                            </span></label>
                                        <div class="input-icon-end position-relative">
                                            <input type="date" class="form-control datePicker" name="joining_date"
                                                placeholder="MM/DD/YYYY" readonly>
                                            <span class="input-icon-addon">
                                                <i class="ti ti-calendar text-gray-7"></i>
                                            </span>
                                        </div>

                                    </div>
                                </div>
                               {{-- <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">User Role </label>
                                        <select class="select" name="staff_role" id="global_staff_role">
                                            <option disabled selected>Select Staff </option>
                                            <option value="employee">Employee </option>
                                            <option value="manager">Manager </option>
                                            <option value="project-manager">Project Manager </option>
                                            <option value="ser export"> SEO Expert </option>
                                            <option value="web designer">Web Designer </option>
                                            <option value="sales executive">Sales Executive </option>
                                        </select>
                                    </div>
                                </div> --}}
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Employee Type</label>
                                        <select class="select" name="emp_type" id="global_emp_type_add">
                                            <option disabled selected>Select Type</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="department" class="form-label">Department</label>
                                            <select class="form-select select" name="department" id="global_department">
                                                <option disabled selected>Select Department</option>
                                                <!-- Options go here -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="designation_visible" class="form-label">Designation</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="global_designation_visible" placeholder="Select Designation" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                                <input type="hidden" name="designation" id="global_designation">
                                                <button class="btn btn-outline-secondary" type="button" onclick="if($('#global_department').val()) { window.openDesignationTree($('#global_department').val(), '#global_designation'); } else { alert('Please select a department first'); }"><i class="ti ti-sitemap"></i> Select</button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Reporting Manager (Add) -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="reporting_manager_add" class="form-label">Reporting
                                                Manager</label>
                                            <select class="form-select select2" name="reporting_manager_id"
                                                id="global_reporting_manager_add">
                                                <option value="">Select Reporting Manager</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Can Login Anytime (Add) -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label d-block">Can Login Anytime</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="can_login_anytime" id="can_login_anytime_yes_add" value="1" checked>
                                                <label class="form-check-label" for="can_login_anytime_yes_add">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="can_login_anytime" id="can_login_anytime_no_add" value="0">
                                                <label class="form-check-label" for="can_login_anytime_no_add">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Call Logs (Add) -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label d-block">Enable Call Logs</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="call_logs" id="call_logs_yes_add" value="1" checked>
                                                <label class="form-check-label" for="call_logs_yes_add">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="call_logs" id="call_logs_no_add" value="0">
                                                <label class="form-check-label" for="call_logs_no_add">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- WhatsApp Access (Add) -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label d-block">WhatsApp Agent Access</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="has_whatsapp_access" id="has_whatsapp_access_yes_add" value="1" {{ $modalCrmLimit == 0 && $modalWhatsappLimit > 0 ? 'checked' : '' }} {{ $modalWhatsappLimit == 0 ? 'disabled' : '' }}>
                                                <label class="form-check-label" for="has_whatsapp_access_yes_add">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="has_whatsapp_access" id="has_whatsapp_access_no_add" value="0" {{ $modalCrmLimit == 0 && $modalWhatsappLimit > 0 ? 'disabled' : 'checked' }}>
                                                <label class="form-check-label" for="has_whatsapp_access_no_add">No</label>
                                            </div>
                                            @if($modalCrmLimit == 0 && $modalWhatsappLimit > 0)
                                                <small class="text-danger d-block mt-1">You only have a WhatsApp plan. Users must be WhatsApp Agents.</small>
                                            @elseif($modalWhatsappLimit == 0)
                                                <small class="text-danger d-block mt-1">You do not have an active WhatsApp plan.</small>
                                            @else
                                                <small class="text-muted d-block mt-1">Allows the user to respond to WhatsApp messages.</small>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="" class="form-label">Web Punch-In/ Punch Out Allowed
                                                Days</label>
                                            <select class="select2" multiple="multiple">
                                                <option>Monday</option>
                                                <option>Tuesday</option>
                                                <option>Wednesday</option>
                                                <option>Thursday</option>
                                                <option>Friday</option>
                                                <option>Saturday</option>
                                                <option>Sunday</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="card-body d-sm-flex align-items-center justify-content-between">
                                        <div class="form-check form-check-md">
                                            <input class="form-check-input" type="checkbox"
                                                name="exlude_ip_restriction" value="1">
                                            <label class="form-check-label" for="checkebox1">
                                                Exclude from IP Restriction
                                            </label>
                                        </div>
                                        <div class="form-check form-check-md d-flex align-items-center">
                                            <input class="form-check-input" type="checkbox"
                                                name="exclude_round_robin" value="1">
                                            <label class="form-check-label" for="checkebox2">
                                                Exclude from Round Robin
                                            </label>
                                        </div>
                                        <div class="form-check form-check-md d-flex align-items-center">
                                            <input class="form-check-input" type="checkbox" name="user_box_timing"
                                                value="1" checked="checked" onclick="globalCheckMe(this.checked);">
                                            <label class="form-check-label" for="checkebox3">
                                                Use Box Login Timing
                                            </label>
                                        </div>
                                    </div>

                                </div>
                                <div class="row" id="global_divcheck" style="display:none;">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="col-form-label">Working Days <span
                                                    class="text-danger">*</span></label>
                                            <select class="select2" multiple name="working_days[]">
                                                <option value="Sunday">Sunday</option>
                                                <option value="Monday">Monday</option>
                                                <option value="Tuesday">Tuesday</option>
                                                <option value="Wednesday">Wednesday</option>
                                                <option value="Thursday">Thursday</option>
                                                <option value="Friday">Friday</option>
                                                <option value="Saturday">Saturday</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-form-label">Day Start Time <span
                                                class="text-danger">*</span></label>
                                        <div class="mb-3 icon-form">
                                            <span class="form-icon"><i class="ti ti-calendar-check"></i></span>
                                            <input type="text" name="day_start_time"
                                                class="form-control timepicker">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-form-label">Day End Time <span
                                                class="text-danger">*</span></label>
                                        <div class="mb-3 icon-form">
                                            <span class="form-icon"><i class="ti ti-calendar-check"></i></span>
                                            <input type="text" name="day_end_time"
                                                class="form-control timepicker">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="col-form-label">Allowed IP (X.X.X.X and press enter) <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="allowed_ips[]" class="form-control">
                                        </div>
                                    </div>
                                </div>



                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-light border me-2"
                                data-bs-dismiss="modal">Cancel </button>
                            <button type="submit" class="btn btn-primary">Save </button>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="global-address" role="tabpanel" aria-labelledby="address-tab"
                        tabindex="0">
                        <div class="modal-body">
                            <div class="card bg-light-500 shadow-none">
                                <div
                                    class="card-body d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                    <h6>Enable Options </h6>
                                    <div class="d-flex align-items-center justify-content-end">
                                        <div class="form-check form-switch me-2">
                                            <label class="form-check-label mt-0">
                                                <input class="form-check-input me-2 all-modules-switch" type="checkbox"
                                                    role="switch" />
                                                Enable all Module
                                            </label>
                                        </div>
                                        <div class="form-check d-flex align-items-center">
                                            <label class="form-check-label mt-0">
                                                <input class="form-check-input select-all-checkbox" type="checkbox" />
                                                Select All
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive border rounded">
                                <table class="table" id="add-permissions-table">
                                    <thead>
                                        <tr>
                                            <th>Module</th>
                                            <th>Read</th>
                                            <th>Create</th>
                                            <th>Update</th>
                                            <th>Delete</th>
                                            <th>Generate</th>
                                            <th>Assigned</th>
                                            <th>Approved</th>
                                            <th>Disapproved</th>
                                            <th>Import</th>
                                            <th>Export</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="form-check form-switch me-2">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            role="switch" checked="" />
                                                        Holidays
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox"
                                                            checked="" />
                                                        Read
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Write
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Create
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox"
                                                            checked="" />
                                                        Delete
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Import
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Export
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check form-switch me-2">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            role="switch" />
                                                        Leaves
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Read
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Write
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Create
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Delete
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Import
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Export
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check form-switch me-2">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            role="switch" />
                                                        Clients
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Read
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Write
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Create
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Delete
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Import
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Export
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check form-switch me-2">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            role="switch" />
                                                        Projects
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Read
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Write
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Create
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Delete
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Import
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Export
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check form-switch me-2">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            role="switch" />
                                                        Tasks
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Read
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Write
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Create
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Delete
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Import
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Export
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check form-switch me-2">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            role="switch" />
                                                        Chats
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Read
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Write
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Create
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Delete
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Import
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Export
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check form-switch me-2">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            role="switch" checked="" />
                                                        Assets
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Read
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Write
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox"
                                                            checked="" />
                                                        Create
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Delete
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox"
                                                            checked="" />
                                                        Import
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Export
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check form-switch me-2">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            role="switch" />
                                                        Timing Sheets
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Read
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Write
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Create
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Delete
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Import
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check d-flex align-items-center">
                                                    <label class="form-check-label mt-0">
                                                        <input class="form-check-input" type="checkbox" />
                                                        Export
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-light border me-2"
                                data-bs-dismiss="modal">Cancel </button>
                            <button type="submit" class="btn btn-primary">Save </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Employee -->


<!-- Add Employee Success (Global) -->
<div class="modal fade" id="global_success_modal" role="dialog" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center p-3">
                    <span class="avatar avatar-lg avatar-rounded bg-success mb-3"><i
                            class="ti ti-check fs-24"></i></span>
                    <h5 class="mb-2">Employee Added Successfully </h5>
                    <p class="mb-3"><span id="global_success_emp_name">Stephan Peralt</span> has been added with Emp-ID : <span class="text-primary" id="global_success_emp_id">#EMP-0001</span>
                    </p>
                    <div id="global_success_password_container" class="mb-3" style="display:none;">
                        <p class="mb-1 text-muted fs-13">Login Password:</p>
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <span class="fw-bold fs-16 text-dark" id="global_success_emp_password"></span>
                            <button class="btn btn-sm btn-icon btn-light" type="button" onclick="copyGlobalPassword()" title="Copy Password">
                                <i class="ti ti-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <div class="row g-2">
                            <div class="col-12">
                                <button type="button" class="btn btn-dark w-100" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function copyGlobalPassword() {
        const pwd = document.getElementById('global_success_emp_password').innerText;
        navigator.clipboard.writeText(pwd).then(() => {
            if (typeof showToast === 'function') showToast('Password copied to clipboard', 'success');
        });
    }

    function showGlobalSuccessModal(employee) {
        const name = employee.name ?? 'Employee';
        const empId = employee.employee_id ?? employee.id ?? 'EMP-????';
        const password = employee.password ?? employee.generated_password ?? employee.plain_password ?? '';

        document.getElementById('global_success_emp_name').innerText = name;
        document.getElementById('global_success_emp_id').innerText = '#' + empId;

        if (password) {
            document.getElementById('global_success_emp_password').innerText = password;
            document.getElementById('global_success_password_container').style.display = 'block';
        } else {
            document.getElementById('global_success_password_container').style.display = 'none';
        }

        $('#global_success_modal').modal('show');
    }
</script>


@push('scripts')
<script>
    function globalCheckMe(selected) {
        if (selected) {
            document.getElementById("global_divcheck").style.display = "none";
        } else {
            document.getElementById("global_divcheck").style.display = "";
        }
    }
    
    document.addEventListener('DOMContentLoaded', function () {
        const globalModal = document.getElementById('global_add_employee');
        if (globalModal) {
            
            let globalModalFetched = false;
            // State to store teams for dynamic dropdown
            let globalTeamOptions = {};

            // Re-initialize select2 if needed
            $(globalModal).on('show.bs.modal', function () {
                if (typeof jQuery !== 'undefined' && $.fn.select2) {
                    $(globalModal).find('.select, .select2').each(function() {
                        if ($(this).hasClass("select2-hidden-accessible")) {
                            $(this).select2('destroy');
                        }
                    });
                    
                    $(globalModal).find('.select, .select2').select2({
                        dropdownParent: $('#global_add_employee')
                    });
                }
                
                if (globalModalFetched) return;
                globalModalFetched = true;

                // Fetch Employee ID
                $.ajax({
                    url: "{{ route('checkEmployeePrefix', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
                    method: 'GET',
                    success: function(response) {
                        if (response.status && response.employee_id) {
                            $('#employee_id_input').val(response.employee_id);
                        }
                    }
                });

                // Fetch Employee Types
                $.ajax({
                    url: "{{ route('employee.get.types', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 200 && response.data) {
                            const $addSelect = $('#global_emp_type_add');
                            $addSelect.empty().append('<option disabled selected>Select Type</option>');
                            response.data.forEach(type => {
                                $addSelect.append(`<option value="${type.id}">${type.name}</option>`);
                            });
                            $addSelect.trigger('change');
                        }
                    }
                });

                // Fetch Departments and Teams
                $.ajax({
                    url: "{{ route('department.manage', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 200 && response.data) {
                            const $deptSelect = $('#global_department');
                            $deptSelect.empty().append('<option value="">Select Department</option>');
                            globalTeamOptions = {};
                            window.teamOptions = {};
                            response.data.forEach(dept => {
                                globalTeamOptions[dept.id] = dept.all_teams || [];
                                window.teamOptions[dept.id] = dept.all_teams || [];
                                $deptSelect.append(`<option value="${dept.id}">${dept.name} (Dept)</option>`);
                            });
                            $deptSelect.trigger('change');
                        }
                    }
                });

                // Fetch All Box Users for Reporting Manager (Initally all)
                populateGlobalReportingManager(null, null);
                
                function populateGlobalReportingManager(filterDept = null, filterDesignation = null) {
                    const $select = $('#global_reporting_manager_add');
                    if (!$select || !$select.length) return;

                    if (filterDesignation) {
                        $select.empty().append('<option value="">Loading superiors...</option>').prop('disabled', true);
                        
                        $.ajax({
                            url: "{{ route('employee.get.box.users', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
                            type: 'GET',
                            data: { 
                                superior_to_designation: filterDesignation
                            },
                            success: function(response) {
                                $select.prop('disabled', false);
                                if (response.status && Array.isArray(response.data)) {
                                    let selectedDepth = null;
                                    if (window.teamOptions) {
                                        Object.values(window.teamOptions).forEach(deptTeams => {
                                            if (selectedDepth === null) {
                                                const found = deptTeams.find(t => String(t.id) === String(filterDesignation));
                                                if (found) {
                                                    selectedDepth = (found.depth !== undefined && found.depth !== null) ? parseInt(found.depth) : null;
                                                }
                                            }
                                        });
                                    }
                                    renderGlobalReportingManagerOptions($select, response.data, filterDesignation, selectedDepth);

                                    // Auto-select
                                    if (response.data.length > 0 && selectedDepth !== null) {
                                        const targetDepth = selectedDepth - 1;
                                        const firstImmediate = response.data.find(u => parseInt(u.designation_depth || 0) === targetDepth);
                                        if (firstImmediate) {
                                            $select.val(firstImmediate.id).trigger('change');
                                        }
                                    }
                                } else {
                                    $select.empty().append('<option value="">No superiors found</option>');
                                }
                            },
                            error: function(xhr, status, error) {
                                $select.prop('disabled', false).empty().append('<option value="">Error loading managers</option>');
                            }
                        });
                    } else {
                        // If no designation selected, load all
                        $.ajax({
                            url: "{{ route('employee.get.box.users', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
                            type: 'GET',
                            success: function(response) {
                                if (response.status && Array.isArray(response.data)) {
                                    renderGlobalReportingManagerOptions($select, response.data, null, null);
                                }
                            }
                        });
                    }
                }

                function renderGlobalReportingManagerOptions($select, users, activeFilterDesignation = null, selectedDepth = null) {
                    $select.empty().append('<option value="">Select Reporting Manager</option>');
                    let hierarchyRec = [];
                    let others = [];

                    users.sort((a, b) => {
                        const aAdmin = (a.staff_role || '').toLowerCase() === 'admin' ? 0 : 1;
                        const bAdmin = (b.staff_role || '').toLowerCase() === 'admin' ? 0 : 1;
                        if (aAdmin !== bAdmin) return aAdmin - bAdmin;
                        
                        if (selectedDepth !== null) {
                            const aDist = Math.abs((a.designation_depth || 0) - (selectedDepth - 1));
                            const bDist = Math.abs((b.designation_depth || 0) - (selectedDepth - 1));
                            return aDist - bDist;
                        }
                        return (a.designation_depth || 0) - (b.designation_depth || 0);
                    });

                    users.forEach(emp => {
                        let isHierarchy = activeFilterDesignation ? true : false;
                        let reason = "";
                        let highlightType = ""; 

                        if (isHierarchy) {
                            const isAdmin = emp.staff_role && emp.staff_role.toLowerCase() === 'admin';
                            const empDepth = parseInt(emp.designation_depth || 0);
                            
                            if (isAdmin) {
                                reason = " (Administrator)";
                                highlightType = "admin";
                            } else if (selectedDepth !== null && empDepth === (selectedDepth - 1)) {
                                reason = " (Immediate Superior)";
                                highlightType = "immediate";
                            } else {
                                reason = " (Higher Hierarchy)";
                                highlightType = "higher";
                            }
                            hierarchyRec.push({ ...emp, recommendationReason: reason, isHierarchy: true, highlightType: highlightType });
                        } else {
                            others.push(emp);
                        }
                    });

                    const createOption = (emp) => {
                        const suffix = emp.recommendationReason || "";
                        const designation = emp.designation_name || 'No Designation';
                        return $('<option></option>')
                            .val(emp.id)
                            .text(`${emp.name}${emp.employee_id ? ' (' + emp.employee_id + ')' : ''}${suffix} [${designation}]`)
                            .data('is-hierarchy', !!emp.isHierarchy)
                            .data('highlight-type', emp.highlightType || "")
                            .data('designation-name', designation);
                    };

                    if (hierarchyRec.length > 0) {
                        const $hGroup = $('<optgroup label="Direct Hierarchy (Primary Recommendations)"></optgroup>');
                        hierarchyRec.forEach(emp => $hGroup.append(createOption(emp)));
                        $select.append($hGroup);
                    }

                    if (others.length > 0) {
                        const label = (hierarchyRec.length > 0) ? "Other Employees" : "All Employees";
                        const $othersGroup = $('<optgroup label="' + label + '"></optgroup>');
                        others.forEach(emp => $othersGroup.append(createOption(emp)));
                        $select.append($othersGroup);
                    }

                    if ($.fn.select2) {
                        if ($select.data('select2')) { $select.select2('destroy'); }
                        $select.select2({
                            placeholder: 'Select Reporting Manager',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#global_add_employee'),
                            templateResult: globalReportingManagerTemplateResult,
                            templateSelection: globalReportingManagerTemplateSelection
                        });
                    }
                }

                function globalReportingManagerTemplateResult(data) {
                    if (!data.id) return data.text;
                    var $el = $(data.element);
                    var isHierarchy = $el.data('is-hierarchy') === true || $el.data('is-hierarchy') === "true";
                    var highlightType = $el.data('highlight-type') || "";
                    
                    if (isHierarchy) {
                        let bgColor = "#fff3e0"; 
                        let borderColor = "#ff9800";
                        let badgeText = "Hierarchy Parent";
                        let badgeClass = "bg-warning";
                        
                        if (highlightType === "immediate") {
                            bgColor = "#e8f5e9"; 
                            borderColor = "#4caf50";
                            badgeText = "Immediate Superior";
                            badgeClass = "bg-success";
                        } else if (highlightType === "admin") {
                            bgColor = "#f3e5f5"; 
                            borderColor = "#9c27b0";
                            badgeText = "Administrator";
                            badgeClass = "bg-secondary";
                        }

                        return $('<div style="display: flex; justify-content: space-between; align-items: center; background-color: ' + bgColor + '; padding: 4px 8px; border-radius: 4px; border-left: 4px solid ' + borderColor + '; margin: 1px 0;">' +
                            '<span><strong>' + data.text.split(' [')[0] + '</strong><br/><small class="text-muted">' + ($el.data('designation-name') || '') + '</small></span>' +
                            '<span class="badge ' + badgeClass + ' text-white" style="font-size: 10px;">' + badgeText + '</span>' +
                            '</div>');
                    }
                    return data.text;
                }

                function globalReportingManagerTemplateSelection(data) {
                    if (!data.id) return data.text;
                    var $el = $(data.element);
                    var isHierarchy = $el.data('is-hierarchy') === true || $el.data('is-hierarchy') === "true";
                    if (isHierarchy) {
                        return data.text.split(' [')[0] + ' (Superior)';
                    }
                    return data.text.split(' [')[0];
                }

                // Bind department change to populate teams and reporting managers
                $('#global_department').on('change', function() {
                    const deptId = $(this).val();
                    const $teamSelect = $('#global_team');
                    $teamSelect.empty().append('<option value="">Select Team (Optional)</option>');
                    
                    if (deptId && globalTeamOptions[deptId]) {
                        globalTeamOptions[deptId].forEach(team => {
                            $teamSelect.append(`<option value="${team.id}">${team.name} (Team)</option>`);
                        });
                    }
                    $teamSelect.trigger('change');

                    const designationId = $('#global_designation').val();
                    populateGlobalReportingManager(deptId || null, designationId || null);

                    if (deptId && typeof window.openDesignationTree === 'function') {
                        window.openDesignationTree(deptId, '#global_designation');
                    }
                });

                // Bind designation change
                $('#global_designation').on('change', function() {
                    const deptId = $('#global_department').val();
                    const designationId = $(this).val();
                    populateGlobalReportingManager(deptId || null, designationId || null);
                });
            });

            // Form Submit Logic
            $('#globalEmployeeForm').on('submit', function (e) {
                e.preventDefault();
                const $form = $(this);
                
                // Simple validation
                let isValid = true;
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.invalid-feedback').remove();

                const phoneInput = document.getElementById('global_phone');
                if (phoneInput) {
                    const phoneVal = phoneInput.value.trim();
                    if (phoneVal && !/^\d{10}$/.test(phoneVal)) {
                        $(phoneInput).addClass('is-invalid');
                        $(phoneInput).after('<div class="invalid-feedback d-block">Phone number must be exactly 10 digits</div>');
                        isValid = false;
                    }
                }

                if (!isValid) return false;

                const form = this;
                const formData = new FormData(form);
                
                // Add permissions to form data
                let permissions = [];
                $form.find('.permission-checkbox:checked').each(function() {
                    permissions.push($(this).val());
                });
                formData.append('permissions', JSON.stringify(permissions));

                var boxSlug = "{{ request()->route('box_slug') ?? session('box_slug') ?? 'default' }}";
                var submitUrl = "{{ route('store.employee', ['box_slug' => ':slug']) }}".replace(':slug', boxSlug);

                const submitBtn = $(form).find('button[type="submit"]');
                const originalText = submitBtn.text();
                submitBtn.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: submitUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        submitBtn.prop('disabled', false).text(originalText);
                        
                        $('#global_add_employee').modal('hide');
                        $form[0].reset();
                        
                        if (response.data) {
                            showGlobalSuccessModal(response.data);
                        } else {
                            if (typeof showToast === 'function') showToast(response.message || 'Employee added successfully', 'success');
                        }
                        
                        if (typeof loadEmployees === 'function') {
                            loadEmployees(); // Refresh list if on employees page
                        }
                        
                        // If we are on the Lead Create page, try to refresh dropdowns
                        if (typeof fetchEmployeesForDropdowns === 'function') {
                            fetchEmployeesForDropdowns(); // Assuming this function exists on lead page
                        } else if (typeof fetchAndPopulateUsersDetail === 'function') {
                            // On lead page, we might trigger a refresh of users
                            let deptId = $('#department').val() || null;
                            let desigId = $('#designation').val() || null;
                            if (deptId || desigId) {
                                fetchAndPopulateUsersDetail($('#assigned_to'), deptId, desigId);
                            }
                        }
                    },
                    error: function (xhr) {
                        submitBtn.prop('disabled', false).text(originalText);
                        if (typeof showToast === 'function') showToast('An error occurred. Please check the form.', 'danger');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            for (let field in errors) {
                                let fieldEl = $form.find(`[name="${field}"]`);
                                fieldEl.addClass('is-invalid');
                                fieldEl.after(`<div class="invalid-feedback d-block">${errors[field][0]}</div>`);
                            }
                        }
                    }
                });
            });
        }
    });
</script>
@endpush
