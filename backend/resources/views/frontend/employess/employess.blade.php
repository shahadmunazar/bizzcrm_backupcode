@extends('layouts.main')
@section('content')
@section('title', 'Employee List')

<div class="page-wrapper">
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Employee </h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="#"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            Employee
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Employee List </li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                <div class="me-2 mb-2">
                    <div class="d-flex align-items-center border bg-white rounded p-1 me-2 icon-list">
                        <a href="{{ box_route('manage_users.employee_list') }}"
                            class="btn btn-icon btn-sm active bg-primary text-white me-1"><i
                                class="ti ti-list-tree"></i></a>
                        <a href="{{ box_route('employee.grid') }}" class="btn btn-icon btn-sm"><i
                                class="ti ti-layout-grid"></i></a>
                    </div>
                </div>
                <div class="me-2 mb-2">
                    <div class="dropdown">
                        <a href="javascript:void(0);"
                            class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                            data-bs-toggle="dropdown">
                            <i class="ti ti-file-export me-1"></i>Export
                        </a>
                        <ul class="dropdown-menu  dropdown-menu-end p-3">
                            <li>
                                <a href="javascript:void(0);" id="export-pdf" class="dropdown-item rounded-1"><i
                                        class="ti ti-file-type-pdf me-1"></i>Export as PDF </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" id="export-excel" class="dropdown-item rounded-1"><i
                                        class="ti ti-file-type-xls me-1"></i>Export as Excel </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="mb-2">
                    @can('attendance.admin.create')
                    <a href="#" data-bs-toggle="modal" data-bs-target="#add_employee"
                        class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add
                        Employee </a>
                    @endcan
                </div>
                <div class="head-icons ms-2">
                    <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-original-title="Collapse" id="collapse-header">
                        <i class="ti ti-chevrons-up"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center overflow-hidden">
                            <div>
                                <span class="avatar avatar-lg bg-dark rounded-circle"><i class="ti ti-users"></i></span>
                            </div>
                            <div class="ms-2 overflow-hidden">
                                <p class="fs-12 fw-medium mb-1 text-truncate">Total Employee </p>
                                <h4 id="total_employee_count">0 </h4>
                            </div>
                        </div>
                        <div>
                            <span class="badge badge-soft-purple badge-sm fw-normal">
                                <i class="ti ti-arrow-wave-right-down"></i>
                                +19.01%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Total Plans -->

            <!-- Total Plans -->
            <div class="col-lg-3 col-md-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center overflow-hidden">
                            <div>
                                <span class="avatar avatar-lg bg-success rounded-circle"><i
                                        class="ti ti-user-share"></i></span>
                            </div>
                            <div class="ms-2 overflow-hidden">
                                <p class="fs-12 fw-medium mb-1 text-truncate">Active </p>
                                <h4 id="active_employee_count">0 </h4>
                            </div>
                        </div>
                        <div>
                            <span class="badge badge-soft-primary badge-sm fw-normal">
                                <i class="ti ti-arrow-wave-right-down"></i>
                                +19.01%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Total Plans -->

            <!-- Inactive Plans -->
            <div class="col-lg-3 col-md-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center overflow-hidden">
                            <div>
                                <span class="avatar avatar-lg bg-danger rounded-circle"><i
                                        class="ti ti-user-pause"></i></span>
                            </div>
                            <div class="ms-2 overflow-hidden">
                                <p class="fs-12 fw-medium mb-1 text-truncate">InActive </p>
                                <h4 id="inactive_employee_count">0 </h4>
                            </div>
                        </div>
                        <div>
                            <span class="badge badge-soft-dark badge-sm fw-normal">
                                <i class="ti ti-arrow-wave-right-down"></i>
                                +19.01%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Inactive Companies -->

            <!-- No of Plans  -->
            <div class="col-lg-3 col-md-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center overflow-hidden">
                            <div>
                                <span class="avatar avatar-lg bg-info rounded-circle"><i
                                        class="ti ti-user-plus"></i></span>
                            </div>
                            <div class="ms-2 overflow-hidden">
                                <p class="fs-12 fw-medium mb-1 text-truncate">New Joiners </p>
                                <h4 id="new_joiners_count">0 </h4>
                            </div>
                        </div>
                        <div>
                            <span class="badge badge-soft-secondary badge-sm fw-normal">
                                <i class="ti ti-arrow-wave-right-down"></i>
                                +19.01%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /No of Plans -->

        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                <h5>Employees List </h5>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="me-3">
                        <button type="button" class="btn btn-white d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                            <i class="ti ti-columns me-1"></i>Manage Columns
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="custom-datatable-filter table-responsive">
                    <table class="table" id="employee-list-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="no-sort">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox" id="select-all" />
                                    </div>
                                </th>
                                <th>First Name </th>
                                <th>Last Name </th>
                                <th>Emp ID </th>
                                <th>Designation </th>
                                <th>Reporting To</th>
                                <th>Department </th>
                                <th>Employee Type </th>
                                <th>Working Mode </th>
                                <th>Mobile No. </th>
                                <th>Email ID </th>
                                <th>Joining Date </th>
                                <th>Call Logs</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>

                        </thead>
                        <tbody id="employeeTableBody">
                        </tbody>
                    </table>

<div class="spinner-border" role="status">
  <span class="sr-only">Loading...</span>
</div>


                </div>








            </div>
        </div>

    </div>

<!-- Manage Columns Modal -->
<div class="modal fade" id="manageColumnsModal" tabindex="-1" aria-labelledby="manageColumnsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageColumnsModalLabel">Manage Columns</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="showAllColumns">Show All</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="hideAllColumns">Hide All</button>
                </div>
                <div class="list-group" id="columnsList">
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



</div>
<!-- /Page Wrapper -->




<!-- Add Employee -->

<div class="modal fade" id="add_employee">
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
            <form id="employeeForm" method="POST" enctype="multipart/form-data">
                <div class="contact-grids-tab">
                    <ul class="nav nav-underline" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="info-tab" data-bs-toggle="tab"
                                data-bs-target="#basic-info" type="button" role="tab"
                                aria-selected="true">Basic Information </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="address-tab" data-bs-toggle="tab" data-bs-target="#address"
                                type="button" role="tab" aria-selected="false">Permissions </button>
                        </li>
                    </ul>
                </div>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="basic-info" role="tabpanel"
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
                                        <label class="form-label">Gender</label>
                                        <select class="select" name="gender">
                                            <option value="" disabled selected>Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
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
                                        <input type="text" name="phone" id="phone" class="form-control"
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
                                        <select class="select" name="staff_role" id="staff_role">
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
                                        <select class="select" name="emp_type" id="emp_type_add">
                                            <option disabled selected>Select Type</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="department" class="form-label">Department</label>
                                            <select class="form-select select" name="department" id="department">
                                                <option disabled selected>Select Department</option>
                                                <!-- Options go here -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="designation_visible" class="form-label">Designation</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="designation_visible" placeholder="Select Designation" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                                <input type="hidden" name="designation" id="designation">
                                                <button class="btn btn-outline-secondary" type="button" onclick="if($('#department').val()) { window.openDesignationTree($('#department').val(), '#designation'); } else { showErrorToast('Please select a department first'); }"><i class="ti ti-sitemap"></i> Select</button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Reporting Manager (Add) -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="reporting_manager_add" class="form-label">Reporting
                                                Manager</label>
                                            <select class="form-select select2" name="reporting_manager_id"
                                                id="reporting_manager_add">
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
                                                value="1" checked="checked" onclick="checkMe(this.checked);">
                                            <label class="form-check-label" for="checkebox3">
                                                Use Box Login Timing
                                            </label>
                                        </div>
                                    </div>

                                </div>
                                <div class="row" id="divcheck" style="display:none;">
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
                    <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab"
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


<!-- Modal Edit employee-->
<div class="modal fade" id="customEmployeeIdModal" tabindex="-1" aria-labelledby="customEmployeeIdModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form id="customEmployeeIdForm" class="modal-content" novalidate>
            <div class="modal-header">
                <h5 class="modal-title" id="customEmployeeIdModalLabel">Custom Employee ID</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Please enter a unique and valid Employee ID.</p>
                <div class="mb-3">
                    <label for="customEmployeeIdInput" class="form-label">Employee ID <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="customEmployeeIdInput" name="custom_employee_id"
                        required minlength="2" placeholder="Enter custom Employee ID" />
                    <div class="invalid-feedback">
                        Employee ID is required and must be at least 2 characters.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Employee ID</button>
            </div>
        </form>
    </div>
</div>


<!-- Edit Employee -->
<div class="modal fade" id="edit_employee">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <h4 class="modal-title me-2">Edit Employee </h4><span>Employee ID : <span
                            id="edit_top_header_employee"></span> </span>
                </div>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="UpdateFormEmPloyee" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="id" id="edit_employee_id">
                <input type="hidden" name="cropped_image" id="edit_cropped_image" value="">
                <div class="contact-grids-tab">
                    <ul class="nav nav-underline" id="myTab2" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="info-tab2" data-bs-toggle="tab"
                                data-bs-target="#basic-info2" type="button" role="tab"
                                aria-selected="true">Basic Information </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="address-tab2" data-bs-toggle="tab"
                                data-bs-target="#address2" type="button" role="tab"
                                aria-selected="false">Permissions </button>
                        </li>
                    </ul>
                </div>
                <div class="tab-content" id="myTabContent2">
                    <div class="tab-pane fade show active" id="basic-info2" role="tabpanel"
                        aria-labelledby="info-tab2" tabindex="0">
                        <div class="modal-body pb-0 ">
                            <div class="row">
                                <div class="col-md-12">
                                    <div
                                        class="d-flex align-items-center flex-wrap row-gap-3 bg-light w-100 rounded p-3 mb-4">
                                        <div
                                            class="d-flex align-items-center justify-content-center avatar avatar-xxl rounded-circle border border-dashed me-2 flex-shrink-0 text-dark frames">
                                            <div class="uploaded_image"><i class="ti ti-photo text-gray-2 fs-16"></i></div>
                                        </div>
                                        <div class="profile-upload">
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
                                </div>
                                <div class="col-md-12">
                                    <div class="modal uploadimageModal" id="edit_uploadimageModal">
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
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">First Name <span class="text-danger"> * </span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Enter First Name" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Last Name </label>
                                        <input type="text" class="form-control" id="last_name" name="last_name"
                                            placeholder="Enter Last Name" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Gender</label>
                                        <select class="select" name="gender" id="edit_gender">
                                            <option value="" disabled selected>Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Employee ID <span class="text-danger"> *
                                            </span></label>
                                        <input type="text" name="employee_id" id="employee_id"
                                            class="form-control" placeholder="Employee ID" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email <span class="text-danger"> * </span></label>
                                        <input type="email" name="email" id="email" class="form-control"
                                            placeholder="Email" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Mobile Number <span class="text-danger"> *
                                            </span></label>
                                        <input type="text" name="phone" id="phones" class="form-control"
                                            placeholder="Mobile Number" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Joining Date <span class="text-danger"> *
                                            </span></label>
                                        <input type="date" name="joining_date" id="joining_date"
                                            class="form-control" placeholder="dd/mm/yyyy" />
                                    </div>
                                </div>

                                {{-- <div class="col-md-6">
                                    <label for="staff_role" class="form-label">User Role</label>
                                    <select class="form-select" name="staff_role" id="staff_roles" required>
                                        <option value="" disabled selected>Select Staff</option>
                                        <option value="employee">Employee</option>
                                        <option value="manager">Manager</option>
                                        <option value="project-manager">Project Manager</option>
                                        <option value="seo-expert">SEO Expert</option>
                                        <option value="web-designer">Web Designer</option>
                                        <option value="sales-executive">Sales Executive</option>
                                    </select>
                                </div> --}}
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Employee Type</label>
                                        <select class="form-select" name="emp_type" id="emp_type_edit">
                                            <option value="" disabled selected>Select Type</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Department (Edit) -->
                                <div class="col-md-6" id="department_edit_container">
                                    <div class="mb-3">
                                        <label class="form-label">Department</label>
                                        <select id="departmentedit" name="department" class="select form-control">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Designation (Edit) -->
                                <div class="col-md-6" id="designation_edit_container">
                                    <div class="mb-3">
                                        <label class="form-label" for="designationedit_visible">Designation</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="designationedit_visible" placeholder="Select Designation" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                            <input type="hidden" name="designation" id="designationedit">
                                            <button class="btn btn-outline-secondary" type="button" onclick="if($('#departmentedit').val()) { window.openDesignationTree($('#departmentedit').val(), '#designationedit'); } else { showErrorToast('Please select a department first'); }"><i class="ti ti-sitemap"></i> Select</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reporting Manager (Edit) -->
                                <div class="col-md-6" id="reporting_manager_edit_container">
                                    <div class="mb-3">
                                        <label class="form-label">Reporting Manager</label>
                                        <select id="reporting_manager_edit" name="reporting_manager_id"
                                            class="form-select select2">
                                            <option value="">Select Reporting Manager</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Can Login Anytime (Edit) -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label d-block">Can Login Anytime</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="can_login_anytime" id="can_login_anytime_yes_edit" value="1">
                                            <label class="form-check-label" for="can_login_anytime_yes_edit">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="can_login_anytime" id="can_login_anytime_no_edit" value="0">
                                            <label class="form-check-label" for="can_login_anytime_no_edit">No</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Call Logs (Edit) -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label d-block">Enable Call Logs</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="call_logs" id="call_logs_yes_edit" value="1">
                                            <label class="form-check-label" for="call_logs_yes_edit">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="call_logs" id="call_logs_no_edit" value="0">
                                            <label class="form-check-label" for="call_logs_no_edit">No</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- WhatsApp Access (Edit) -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label d-block">WhatsApp Agent Access</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="has_whatsapp_access" id="has_whatsapp_access_yes_edit" value="1" {{ ($whatsappLimit ?? 0) == 0 ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="has_whatsapp_access_yes_edit">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="has_whatsapp_access" id="has_whatsapp_access_no_edit" value="0" {{ ($crmLimit ?? 0) == 0 && ($whatsappLimit ?? 0) > 0 ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="has_whatsapp_access_no_edit">No</label>
                                        </div>
                                        @if(($crmLimit ?? 0) == 0 && ($whatsappLimit ?? 0) > 0)
                                            <small class="text-danger d-block mt-1">You only have a WhatsApp plan. Users must be WhatsApp Agents.</small>
                                        @elseif(($whatsappLimit ?? 0) == 0)
                                            <small class="text-danger d-block mt-1">You do not have an active WhatsApp plan.</small>
                                        @else
                                            <small class="text-muted d-block mt-1">Allows the user to respond to WhatsApp messages.</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="card-body d-sm-flex align-items-center justify-content-between">
                                        <div class="form-check form-check-md">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="exlude_ip_restriction" name="exlude_ip_restriction">
                                            <label class="form-check-label" for="checkebox1">
                                                Exclude from IP Restriction
                                            </label>
                                        </div>
                                        <div class="form-check form-check-md d-flex align-items-center">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="exclude_round_robin" name="exclude_round_robin">
                                            <label class="form-check-label" for="checkebox2">
                                                Exclude from Round Robin
                                            </label>
                                        </div>
                                        <div class="form-check form-check-md d-flex align-items-center">
                                            <input class="form-check-input" type="checkbox" id="user_box_timing"
                                                name="user_box_timing" value="1"
                                                onclick="toggleCustomTimingFields(this.checked)">
                                            <label class="form-check-label" for="checkebox3">
                                                Use Box Login Timing
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="divcheckedit" style="display:none;">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="col-form-label">Working Days <span
                                                    class="text-danger">*</span></label>
                                            <select class="select2" multiple name="working_days[]" id="working_days">
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
                                            <input type="text" id="day_start_time" name="day_start_time"
                                                class="form-control timepicker">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-form-label">Day End Time <span
                                                class="text-danger">*</span></label>
                                        <div class="mb-3 icon-form">
                                            <span class="form-icon"><i class="ti ti-calendar-check"></i></span>
                                            <input type="text" id="day_end_time" name="day_end_time"
                                                class="form-control timepicker">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="col-form-label">Allowed IP (X.X.X.X and press enter) <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="allowed_ips" name="allowed_ips[]"
                                                class="form-control">
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
                    <div class="tab-pane fade" id="address2" role="tabpanel" aria-labelledby="address-tab2"
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
                                <table class="table" id="edit-permissions-table">
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
<!-- /Edit Employee -->

<!-- Add Employee Success -->
<div class="modal fade" id="success_modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center p-3">
                    <span class="avatar avatar-lg avatar-rounded bg-success mb-3"><i
                            class="ti ti-check fs-24"></i></span>
                    <h5 class="mb-2">Employee Added Successfully </h5>
                    <p class="mb-3"><span id="success_emp_name">Stephan Peralt</span> has been added with Emp-ID : <span class="text-primary" id="success_emp_id">#EMP-0001</span>
                    </p>
                    <div>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ box_route('manage_users.employee_list') }}" class="btn btn-dark w-100">Back to List
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ box_route('employee.details') }}"
                                    class="btn btn-primary w-100 btn-detail-page" id="success_detail_link">
                                    Detail Page
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Permission Success -->
<div class="modal fade" id="update_success_modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center p-3">
                    <span class="avatar avatar-lg avatar-rounded bg-success mb-3"><i
                            class="ti ti-check fs-24"></i></span>
                    <h5 class="mb-2">Permission Updated Successfully </h5>
                    <p class="mb-3"><span id="update_success_emp_name"></span> has been updated with Emp-ID : <span class="text-primary" id="update_success_emp_id"></span>
                    </p>
                    <div>
                        <div class="row g-2">
                            <div class="col-6">
                                <button type="button" class="btn btn-dark w-100" id="back_to_list_btn" data-bs-dismiss="modal">Back to List</button>
                            </div>
                            <div class="col-6">
                                <a href="#" class="btn btn-primary w-100 btn-detail-page" id="update_success_detail_link">
                                    Detail Page
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Update Permission Success -->

<!-- Delete Modal -->
<div class="modal fade" id="delete_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1">Confirm Delete</h4>
                <p class="mb-3" id="delete_modal_text">
                    You want to delete <strong id="delete_count">1</strong> marked item(s), this action cannot be
                    undone.
                </p>
                <div class="d-flex justify-content-center">
                    <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                    <button id="confirm_delete_btn" class="btn btn-danger">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- /Delete Modal -->

<!-- Toggle Status Modal -->
<div class="modal fade" id="toggle_status_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-warning text-warning mb-3">
                    <i class="ti ti-refresh fs-36"></i>
                </span>
                <h4 class="mb-1">Confirm Status Change</h4>
                <p class="mb-3" id="toggle_modal_text">
                    Are you sure you want to set this employee status as
                    <strong id="toggle_action_text" class="text-capitalize">Active</strong>?
                </p>

                <div class="d-flex justify-content-center">
                    <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                    <button id="confirm_toggle_btn" class="btn btn-warning">Yes, Proceed</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Toggle Status Modal -->
<style>
    #employee-list-table tbody tr td .employee-clickable-content {
        transition: background-color 0.2s, color 0.2s;
    }
    #employee-list-table tbody tr td .employee-clickable-content:hover {
        background-color: #e6f2ff;
        color: #0d6efd !important;
        text-decoration: underline;
        cursor: pointer;
        border-radius: 3px;
        padding: 2px 4px;
        display: inline-block;
    }
    .letter-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-weight: bold;
        font-size: 16px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        text-transform: uppercase;
    }
</style>







@push('scripts')
    <script>
        $(document).ready(function() {
            let employeeTable;

            // Column definitions with names
            const columnDefinitions = [
                { index: 0, name: 'Checkbox', visible: true },
                { index: 1, name: 'First Name', visible: true },
                { index: 2, name: 'Last Name', visible: true },
                { index: 3, name: 'Emp ID', visible: true },
                { index: 4, name: 'Designation', visible: true },
                { index: 5, name: 'Reporting To', visible: true },
                { index: 6, name: 'Department', visible: true },
                { index: 7, name: 'Employee Type', visible: true },
                { index: 8, name: 'Working Mode', visible: true },
                { index: 9, name: 'Mobile No.', visible: true },
                { index: 10, name: 'Email ID', visible: true },
                { index: 11, name: 'Joining Date', visible: true },
                { index: 12, name: 'Call Logs', visible: true },
                { index: 13, name: 'Status', visible: true },
                { index: 14, name: 'Action', visible: true }
            ];

            // Page identifier for column preferences
            const pageIdentifier = 'employee_list';

            // Load column visibility preferences from database
            function loadColumnPreferences(callback) {
                $.ajax({
                    url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug")]) }}',
                    type: 'GET',
                    data: {
                        page_identifier: pageIdentifier
                    },
                    async: true, // Async is required by modern browser policies
                    success: function(response) {
                        if (response.success && response.data && Array.isArray(response.data) && response.data.length > 0) {
                            try {
                                const preferences = response.data;
                                preferences.forEach(pref => {
                                    // Convert string index to number
                                    const prefIndex = parseInt(pref.index);
                                    const col = columnDefinitions.find(c => c.index === prefIndex);
                                    if (col) {
                                        // Handle both boolean and string values ("true"/"false")
                                        if (typeof pref.visible === 'string') {
                                            col.visible = pref.visible === 'true' || pref.visible === '1';
                                        } else {
                                            col.visible = pref.visible !== undefined ? Boolean(pref.visible) : true;
                                        }
                                    }
                                });

                                // STRICT: For admins, always show Action column (index 10) regardless of preferences
                                // Check if user is admin from PermissionHelper or response
                                const isAdmin = (window.PermissionHelper && window.PermissionHelper.isAdmin) ||
                                              (response.is_admin === true) ||
                                              (response.staff_role === 'admin');
                                
                                if (isAdmin) {
                                    const actionColumn = columnDefinitions.find(c => c.index === 13);
                                    if (actionColumn) {
                                        actionColumn.visible = true; // Force Action column visible for admins
                                    }
                                }

                                if (callback) callback();
                            } catch (e) {
                                console.error('Error loading column preferences:', e);
                                // Fallback: show all columns
                                columnDefinitions.forEach(col => col.visible = true);
                                if (callback) callback();
                            }
                        } else {
                            // No preferences found or empty response - show all columns by default
                            columnDefinitions.forEach(col => col.visible = true);
                            if (callback) callback();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching column preferences:', error);
                        // Fallback: show all columns on error
                        columnDefinitions.forEach(col => col.visible = true);
                        if (callback) callback();
                    }
                });
            }

            // Save column visibility preferences to database
            function saveColumnPreferences() {
                $.ajax({
                    url: '{{ route("settings.column.preferences.save", ["box_slug" => request()->route("box_slug")]) }}',
                    type: 'POST',
                    data: {
                        page_identifier: pageIdentifier,
                        column_preferences: columnDefinitions,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('Column preferences saved successfully');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error saving column preferences:', error);
                    }
                });
            }

            // Apply column visibility
            function applyColumnVisibility() {
                if (employeeTable) {
                    // STRICT: For admins, always show Action column (index 11) regardless of preferences
                    const isAdmin = (window.PermissionHelper && window.PermissionHelper.isAdmin) || false;
                    if (isAdmin) {
                        const actionColumn = columnDefinitions.find(c => c.index === 13);
                        if (actionColumn) {
                            actionColumn.visible = true; // Force Action column visible for admins
                        }
                    }

                    columnDefinitions.forEach(col => {
                        try {
                            employeeTable.column(col.index).visible(col.visible, false);
                        } catch (e) {
                            console.error('Error applying column visibility:', e);
                        }
                    });
                    // Adjust column widths after visibility change
                    employeeTable.columns.adjust();
                }
            }

            // Populate columns modal
            function populateColumnsModal() {
                const columnsList = $('#columnsList');
                columnsList.empty();

                // Check if user is admin
                const isAdmin = (window.PermissionHelper && window.PermissionHelper.isAdmin) ||
                               (window.PermissionHelper && window.PermissionHelper.staffRole === 'admin') || false;

                columnDefinitions.forEach(col => {
                    // For admins, Action column (index 12) should always be checked and disabled
                    const isActionColumn = col.index === 12;
                    const isDisabled = isAdmin && isActionColumn;

                    const listItem = $(`
                        <div class="list-group-item">
                            <div class="form-check">
                                <input class="form-check-input column-toggle" type="checkbox"
                                       data-column-index="${col.index}"
                                       id="col_${col.index}"
                                       ${col.visible ? 'checked' : ''}
                                       ${isDisabled ? 'disabled' : ''}>
                                <label class="form-check-label" for="col_${col.index}" ${isDisabled ? 'style="opacity: 0.6;"' : ''}>
                                    ${col.name}${isDisabled ? ' <span class="text-muted">(Always visible for admins)</span>' : ''}
                                </label>
                            </div>
                        </div>
                    `);
                    columnsList.append(listItem);
                });
            }

            // Load preferences on page load
            loadColumnPreferences(function() {
                // Preferences loaded, ready to use
                init();
            });
            let deleteEmployeeId = null;
            let toggleEmployeeId = null;
            let allEmployees = [];
            let allBoxUsers = []; // For Reporting Manager dropdown (add & edit)

            let allPermissions = {}; // Store all permissions grouped by module
            let employeePermissions = []; // Store current employee's permissions

            function init() {
                loadEmployees();
                fetchBoxUsersForReportingManager();
                loadPermissions();
                loadUserPermissions(); // Load current user's permissions for frontend checks
                bindFormSubmission();
                // bindExportButtons();
                bindModalEvents();
                bindEditButton();
                bindStatusToggle();
                bindEditFormSubmission();
                bindPermissionsHandlers();
                // Note: applyPermissionBasedUI() is called after permissions are loaded in loadUserPermissions()
            }

            // Load current user's permissions for frontend checks
            function loadUserPermissions() {
                $.ajax({
                    url: '{{ route('user.permissions', ['box_slug' => request()->route("box_slug")]) }}' + '?t=' + new Date().getTime(),
                    type: 'GET',
                    cache: false,
                    success: function(response) {
                        if (window.PermissionHelper) {
                            window.PermissionHelper.userPermissions = response.permissions || [];
                            window.PermissionHelper.isAdmin = response.is_admin || false;
                            window.PermissionHelper.staffRole = response.staff_role || '';

                            // STRICT: For admins, always show Action column
                            if (window.PermissionHelper.isAdmin || window.PermissionHelper.staffRole === 'admin') {
                                const actionColumn = columnDefinitions.find(c => c.index === 13);
                                if (actionColumn) {
                                    actionColumn.visible = true; // Force Action column visible for admins
                                }
                                // Re-apply column visibility after admin status is confirmed
                                if (employeeTable) {
                                    applyColumnVisibility();
                                }
                            }

                            applyPermissionBasedUI(); // Apply immediately after loading
                        }
                    },
                    error: function() {
                        console.error('Failed to load user permissions.');
                    }
                });
            }

            // Apply permission-based UI changes
            function applyPermissionBasedUI() {
                if (!window.PermissionHelper) {
                    console.warn('PermissionHelper not loaded');
                    return;
                }

                // Check if user is admin (check both isAdmin and staffRole)
                const isAdmin = window.PermissionHelper.isAdmin ||
                               (window.PermissionHelper.staffRole && window.PermissionHelper.staffRole.toLowerCase() === 'admin');

                // If user is admin, show all action buttons and don't hide anything
                if (isAdmin) {
                    // Explicitly show all action buttons for admins
                    $('[data-bs-target="#add_employee"]').show();
                    $('.edit-employee-btn').show();
                    $('[data-bs-target="#delete_modal"]').show();
                    $('.toggle-status').show();
                    $('#export-excel, #export-pdf').prop('disabled', false).removeClass('disabled');
                    return;
                }

                // Hide/Show Add Employee button
                if (!window.PermissionHelper.hasPermission('attendance.admin.create')) {
                    $('[data-bs-target="#add_employee"]').hide();
                } else {
                    $('[data-bs-target="#add_employee"]').show();
                }

                // Hide/Show Edit buttons
                if (!window.PermissionHelper.hasPermission('attendance.admin.update')) {
                    $('.edit-employee-btn').hide();
                } else {
                    $('.edit-employee-btn').show();
                }

                // Hide/Show Delete buttons
                if (!window.PermissionHelper.hasPermission('attendance.admin.delete')) {
                    $('[data-bs-target="#delete_modal"]').hide();
                } else {
                    $('[data-bs-target="#delete_modal"]').show();
                }

                // Enable/Disable Export buttons - check for export permission specifically
                if (!window.PermissionHelper.hasPermission('attendance.admin.export')) {
                    $('#export-excel, #export-pdf').prop('disabled', true).addClass('disabled');
                } else {
                    $('#export-excel, #export-pdf').prop('disabled', false).removeClass('disabled');
                }

                // Hide/Show Toggle Status buttons
                if (!window.PermissionHelper.hasPermission('attendance.admin.update')) {
                    $('.toggle-status').hide();
                } else {
                    $('.toggle-status').show();
                }
            }

            // Load all available permissions
            function loadPermissions() {
                $.ajax({
                    url: '{{ route('get.permissions', ['box_slug' => request()->route('box_slug')]) }}' + '?t=' + new Date().getTime(),
                    type: 'GET',
                    cache: false,
                    success: function(response) {
                        if (response.status && response.data) {
                            allPermissions = response.data;
                            renderPermissionsTable('#address tbody', 'add');
                            renderPermissionsTable('#address2 tbody', 'edit');
                        }
                    },
                    error: function() {
                        console.error('Failed to load permissions.');
                    }
                });
            }

            // Render permissions table dynamically
            function renderPermissionsTable(selector, mode) {
                const $tbody = $(selector);
                $tbody.empty();

                const actions = ['Read', 'Create', 'Update', 'Delete', 'Generate', 'Assigned', 'Approved', 'Disapproved', 'Import', 'Export'];
                const actionMap = {
                    'Read': 'read',
                    'Create': 'create',
                    'Update': 'update',
                    'Delete': 'delete',
                    'Generate': 'generate',
                    'Assigned': 'assigned',
                    'Approved': 'approved',
                    'Disapproved': 'disapproved',
                    'Import': 'import',
                    'Export': 'export'
                };

                // Add "All Permissions" row first
                if (allPermissions['All Modules']) {
                    const allPerms = allPermissions['All Modules'];
                    const allPermissionsPerm = allPerms.find(p => p.name === 'all.permissions');

                    if (allPermissionsPerm) {
                        const $allRow = $('<tr class="table-primary"></tr>');
                        $allRow.append(`
                            <td colspan="11">
                                <div class="form-check form-switch me-2">
                                    <label class="form-check-label mt-0 fw-bold">
                                        <input class="form-check-input me-2 all-permissions-checkbox-${mode}"
                                            type="checkbox" role="switch"
                                            data-permission-id="${allPermissionsPerm.id}" />
                                        <span class="text-primary">All Permissions (Grants access to all modules and actions)</span>
                                    </label>
                                </div>
                            </td>
                        `);
                        $tbody.append($allRow);
                    }
                }
                // Group permissions by module and sub-module
                Object.keys(allPermissions).forEach(module => {
                    // Skip "All Modules" as it's handled separately
                    if (module === 'All Modules') return;

                    const permissions = allPermissions[module];

                    // Group by sub-module
                    const subModuleGroups = {};
                    permissions.forEach(perm => {
                        const subModule = perm.sub_module || 'All';
                        const subModuleKey = perm.sub_module_key || 'all';

                        if (!subModuleGroups[subModule]) {
                            subModuleGroups[subModule] = {
                                key: subModuleKey,
                                permissions: []
                            };
                        }
                        subModuleGroups[subModule].permissions.push(perm);
                    });

                    // Create rows for each sub-module
                    Object.keys(subModuleGroups).forEach(subModule => {
                        const subModuleData = subModuleGroups[subModule];
                        const subModuleKey = subModuleData.key;
                        const subModulePerms = subModuleData.permissions;

                        const $row = $('<tr></tr>');

                        // Sub-module name (indented for sub-modules)
                        const displayName = subModule === 'All' ? module : `${module} - ${subModule}`;
                        const indentClass = subModule === 'All' ? '' : 'ps-4';

                        $row.append(`
                            <td class="${indentClass}">
                                <div class="form-check form-switch me-2">
                                    <label class="form-check-label mt-0">
                                        <input class="form-check-input me-2 submodule-switch-${mode}"
                                            type="checkbox" role="switch"
                                            data-module="${module}"
                                            data-submodule="${subModuleKey}" />
                                        ${displayName}
                                    </label>
                                </div>
                            </td>
                        `);

                        // Action checkboxes
                        actions.forEach(action => {
                            const actionKey = actionMap[action];
                            const perm = subModulePerms.find(p =>
                                (p.action_key === actionKey || p.action === action) &&
                                (p.sub_module_key === subModuleKey || p.sub_module_key === 'all')
                            );

                            if (perm) {
                                $row.append(`
                                    <td>
                                        <div class="form-check d-flex align-items-center">
                                            <label class="form-check-label mt-0">
                                                <input class="form-check-input permission-checkbox-${mode}"
                                                    type="checkbox" data-permission-id="${perm.id}"
                                                    data-module="${module}"
                                                    data-submodule="${subModuleKey}"
                                                    data-action="${actionKey}" />
                                            </label>
                                        </div>
                                    </td>
                                `);
                            } else {
                                $row.append(`<td></td>`);
                            }
                        });

                        $tbody.append($row);
                    });
                });

                // Bind sub-module switch handlers
                $(`.submodule-switch-${mode}`).on('change', function() {
                    const module = $(this).data('module');
                    const subModule = $(this).data('submodule');
                    const isChecked = $(this).is(':checked');
                    $(`.permission-checkbox-${mode}[data-module="${module}"][data-submodule="${subModule}"]`).prop('checked', isChecked);
                    updateSelectAll(mode);
                });

                // Bind individual checkbox handlers
                $(`.permission-checkbox-${mode}`).on('change', function() {
                    const module = $(this).data('module');
                    const subModule = $(this).data('submodule');
                    updateSubModuleSwitch(mode, module, subModule);
                    updateSelectAll(mode);
                });

                // Bind "All Permissions" checkbox handler
                $(`.all-permissions-checkbox-${mode}`).on('change', function() {
                    const isChecked = $(this).is(':checked');
                    if (isChecked) {
                        // Check all permissions when "All Permissions" is checked
                        $(`.permission-checkbox-${mode}`).prop('checked', true);
                        $(`.submodule-switch-${mode}`).prop('checked', true);
                    } else {
                        // Uncheck all when "All Permissions" is unchecked
                        $(`.permission-checkbox-${mode}`).prop('checked', false);
                        $(`.submodule-switch-${mode}`).prop('checked', false);
                    }
                    updateSelectAll(mode);
                });

                // When any individual permission changes, update "All Permissions" checkbox
                $(`.permission-checkbox-${mode}`).on('change', function() {
                    const $allCheckboxes = $(`.permission-checkbox-${mode}`);
                    const allChecked = $allCheckboxes.length > 0 &&
                        $allCheckboxes.length === $allCheckboxes.filter(':checked').length;
                    $(`.all-permissions-checkbox-${mode}`).prop('checked', allChecked);
                });
            }

            // Update sub-module switch based on individual checkboxes
            function updateSubModuleSwitch(mode, module, subModule) {
                const $checkboxes = $(`.permission-checkbox-${mode}[data-module="${module}"][data-submodule="${subModule}"]`);
                const anyChecked = $checkboxes.filter(':checked').length > 0;
                $(`.submodule-switch-${mode}[data-module="${module}"][data-submodule="${subModule}"]`).prop('checked', anyChecked);
            }

            // Update "Select All" checkbox and "Enable all Module" switch
            function updateSelectAll(mode) {
                const $allCheckboxes = $(`.permission-checkbox-${mode}`);
                const total = $allCheckboxes.length;
                const checked = $allCheckboxes.filter(':checked').length;
                const allChecked = total > 0 && total === checked;
                const anyChecked = checked > 0;
                
                // Update header controls
                $(`#select-all-${mode}, .select-all-checkbox`).prop('checked', allChecked);
                $(`.all-modules-switch`).prop('checked', anyChecked);
                
                // Update "All Permissions" row checkbox if it exists
                $(`.all-permissions-checkbox-${mode}`).prop('checked', allChecked);
            }

            // Bind permissions handlers
            function bindPermissionsHandlers() {
                // Header "Select All" handler
                $(document).on('change', '.select-all-checkbox', function() {
                    const isChecked = $(this).is(':checked');
                    const mode = $(this).closest('.tab-pane').attr('id') === 'address2' ? 'edit' : 'add';
                    $(`.permission-checkbox-${mode}`).prop('checked', isChecked);
                    $(`.submodule-switch-${mode}`).prop('checked', isChecked);
                    $(`.all-permissions-checkbox-${mode}`).prop('checked', isChecked);
                    if (!isChecked) {
                        $(`.permission-checkbox-${mode}`).parent().find('.badge-inherited').remove();
                    }
                    updateSelectAll(mode);
                });

                // Header "Enable all Module" handler
                $(document).on('change', '.all-modules-switch', function() {
                    const isChecked = $(this).is(':checked');
                    const mode = $(this).closest('.tab-pane').attr('id') === 'address2' ? 'edit' : 'add';
                    // Act like check all
                    $(`.submodule-switch-${mode}`).prop('checked', isChecked);
                    $(`.permission-checkbox-${mode}`).prop('checked', isChecked);
                    $(`.all-permissions-checkbox-${mode}`).prop('checked', isChecked);
                    if (!isChecked) {
                        $(`.permission-checkbox-${mode}`).parent().find('.badge-inherited').remove();
                    }
                    updateSelectAll(mode);
                });

                // Remove inherited badge when unchecked manually
                $(document).on('change', '.permission-checkbox-add, .permission-checkbox-edit', function() {
                    if (!this.checked) {
                        $(this).parent().find('.badge-inherited').remove();
                    }
                });
            }

            // Get selected permissions - only returns enabled (direct) checked permissions
            // Collect all checked permissions (including those that were inherited)
            function getSelectedPermissions(mode) {
                const selected = [];

                // Check if "All Permissions" is selected
                const $allPermCheckbox = $(`.all-permissions-checkbox-${mode}:checked`);
                if ($allPermCheckbox.length > 0) {
                     const permId = $allPermCheckbox.data('permission-id');
                     if (permId) selected.push(permId);
                } 
                
                // Collect checked individual permissions
                $(`.permission-checkbox-${mode}:checked`).each(function() {
                    const permId = $(this).data('permission-id');
                    if (permId) selected.push(permId);
                });

                // Remove duplicates and return unique array
                return [...new Set(selected)];
            }

            // Populate permissions for edit
            function populateEmployeePermissions(permissionData) {
                // permissionData is expected to be { direct: [], inherited: [] }
                const direct = permissionData?.direct || [];
                const inherited = permissionData?.inherited || [];
                
                // Reset all checkboxes first
                $(`.permission-checkbox-edit, .submodule-switch-edit, .all-permissions-checkbox-edit`)
                    .prop('checked', false)
                    .prop('disabled', false)
                    .parent().find('.badge-inherited').remove();

                // Helper to mark a label as inherited
                const addInheritedBadge = ($el) => {
                    if ($el.parent().find('.badge-inherited').length === 0) {
                        $el.parent().append('<span class="badge bg-warning ms-1 badge-inherited" style="font-size: 0.7em;">Inherited</span>');
                    }
                };

                // Rule: If direct permissions exist, they are the ONLY truth.
                // If direct is empty, we fallback to inherited.
                // This allows unselecting inherited permissions by explicitly setting a custom direct set.
                let effectiveChecked = [];
                if (direct && direct.length > 0) {
                    effectiveChecked = direct;
                } else {
                    effectiveChecked = inherited;
                }

                // 1. Mark all inherited items with a badge
                inherited.forEach(id => {
                    const $chk = $(`.permission-checkbox-edit[data-permission-id="${id}"]`);
                    if ($chk.length) addInheritedBadge($chk);
                });

                // Check for inherited "All Permissions"
                let allPermId = null;
                if (allPermissions['All Modules']) {
                    const allPerm = allPermissions['All Modules'].find(p => p.name === 'all.permissions');
                    if (allPerm) {
                        allPermId = allPerm.id;
                        if (inherited.includes(allPermId)) addInheritedBadge($(`.all-permissions-checkbox-edit`));
                    }
                }

                // 2. Check the effective permissions
                effectiveChecked.forEach(id => {
                    // Check individual permission
                    const $chk = $(`.permission-checkbox-edit[data-permission-id="${id}"]`);
                    if ($chk.length) $chk.prop('checked', true);

                    // Check if it's "All Permissions"
                    if (allPermId && id === allPermId) {
                        $(`.all-permissions-checkbox-edit`).prop('checked', true);
                        $(`.permission-checkbox-edit`).prop('checked', true);
                        $(`.submodule-switch-edit`).prop('checked', true);
                    }
                });

                // 3. Update Sub-module Switches based on current checkbox states
                Object.keys(allPermissions).forEach(module => {
                    if (module !== 'All Modules') {
                        const permissions = allPermissions[module];
                        const subModules = [...new Set(permissions.map(p => p.sub_module_key || 'all'))];
                        subModules.forEach(subModule => {
                            updateSubModuleSwitch('edit', module, subModule);
                        });
                    }
                });
                
                updateSelectAll('edit');
            }
            // 1. Load employees via AJAX and update UI
            function loadEmployees(callback) {
                // Add timestamp to prevent caching
                const timestamp = new Date().getTime();
                $.ajax({
                    url: '{{ box_route('fetch.employee') }}' + '?t=' + timestamp,
                    type: 'GET',
                    cache: false,
                    success: function(response) {
                        // DataTables returns data in response.data array
                        allEmployees = response.data || [];
                        const stats = response.summary || {};
                        updateDashboardStats(stats || {});
                        populateDataTable(allEmployees);

                        // Debug: Log employee data to verify employee_id is present
                        if (allEmployees.length > 0) {
                            // console.log('Sample employee data:', allEmployees[0]);
                        }

                        // Execute callback if provided
                        if (typeof callback === 'function') {
                            setTimeout(callback, 300); // Wait for table to render
                        }
                    },
                    error: function() {
                        alert('Failed to load employees.');
                        if (typeof callback === 'function') {
                            callback();
                        }
                    }
                });
            }

            // Function to refresh a specific employee's image in the table
            function refreshEmployeeImageInTable(employeeId, imageUrl) {
                if (!employeeId || !imageUrl) return;

                // Find the image in the table by employee ID
                $('.employee-profile-img[data-emp-id="' + employeeId + '"]').each(function() {
                    const $img = $(this);
                    // Add cache-busting to force reload
                    const separator = imageUrl.includes('?') ? '&' : '?';
                    const newSrc = imageUrl.replace(/\\/g, '/') + separator + 't=' + new Date().getTime() + '&id=' + employeeId;

                    // Force reload by changing src
                    $img.attr('src', newSrc);

                    // Also try to reload the image
                    $img[0].src = newSrc;
                });

                // Also update any letter avatars if image exists
                if (imageUrl) {
                    const employee = allEmployees.find(emp => emp.id === employeeId);
                    if (employee) {
                        // Find potential containers - either existing image or letter avatar
                        const $img = $('.employee-profile-img[data-emp-id="' + employeeId + '"]');
                        const $letterAvatar = $('.letter-avatar[data-name="' + (employee.name || 'U') + '"]').parent(); // Use parent if it's a wrapper, or adjust selector based on DOM
                        
                        // If we found an image, update its parent
                        if ($img.length) {
                             const $avatarContainer = $img.closest('a, div.avatar'); // More specific container
                             if ($avatarContainer.length) {
                                const separator = imageUrl.includes('?') ? '&' : '?';
                                const newSrc = imageUrl.replace(/\\/g, '/') + separator + 't=' + new Date().getTime() + '&id=' + employeeId;
                                const imageHtml = `<img src="${newSrc}" class="img-fluid rounded-circle employee-profile-img" alt="img" data-emp-id="${employeeId}" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'letter-avatar\\' data-name=\\'${employee.name || 'U'}\\'>${(employee.name || 'U').charAt(0).toUpperCase()}</div>';" />`;
                                $avatarContainer.html(imageHtml);
                             }
                        } else {
                            // If we didn't find an image, look for a letter avatar to replace
                             // This part is tricky because we need a reliable way to find the specific employee's avatar container if it's currently a letter.
                             // The datatable row remake in `populateDataTable` is the primary way this gets fixed on full reload.
                             // But for partial updates, we might need a specific ID on the wrapper.
                             // For now, let's rely on `loadEmployees` re-rendering the table which `populateDataTable` does.
                        }
                    }
                }
            }


            // Fetch all box users for Reporting Manager dropdown (add & edit)
            function fetchBoxUsersForReportingManager() {
                $.ajax({
                    url: '{{ route('employee.get.box.users', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status && Array.isArray(response.data)) {
                            allBoxUsers = response.data;

                            // For ADD EMPLOYEE form: show ALL box users by default (no exclusions, no filters)
                            // This ensures when opening Add Employee, Reporting Manager dropdown lists every box user
                            populateReportingManagerSelect($('#reporting_manager_add'), null, null,
                                null);
                        } else {
                            allBoxUsers = [];
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching box users for reporting manager:', error);
                        allBoxUsers = [];
                    }
                });
            }

            // Populate Reporting Manager select with smarter recommendations based on hierarchy and existing assignments
            function populateReportingManagerSelect($select, filterDept = null, filterDesignation = null,
                selectedId = null) {
                if (!$select || !$select.length) return;

                // 1. If a designation is selected, fetch superiors dynamically from the server
                if (filterDesignation) {
                    const currentEditingEmpId = $('#edit_employee_id').val();
                    $select.empty().append('<option value="">Loading superiors...</option>').prop('disabled', true);
                    
                    $.ajax({
                        url: '{{ route('employee.get.box.users', ['box_slug' => request()->route('box_slug')]) }}',
                        type: 'GET',
                        data: { 
                            superior_to_designation: filterDesignation, 
                            exclude_id: currentEditingEmpId || null,
                            include_id: selectedId || null
                        },
                        success: function(response) {
                            $select.prop('disabled', false);
                            if (response.status && Array.isArray(response.data)) {
                                // Find selected designation depth to pass for sorting
                                let selectedDepth = null;
                                if (window.teamOptions) {
                                    Object.values(window.teamOptions).forEach(deptTeams => {
                                        if (selectedDepth === null) {
                                            const found = deptTeams.find(t => String(t.id) === String(filterDesignation));
                                            if (found) {
                                                selectedDepth = (found.depth !== undefined && found.depth !== null) ? parseInt(found.depth) : null;
                                                console.log('Detected Selected Designation Depth:', selectedDepth, found.name);
                                            }
                                        }
                                    });
                                }
                                renderReportingManagerOptions($select, response.data, selectedId, filterDesignation, selectedDepth);

                                // Auto-select logic: If no manager is currently selected (e.g., in ADD form),
                                // automatically select the first "Immediate Superior" found.
                                if (!selectedId && response.data.length > 0 && selectedDepth !== null) {
                                    const targetDepth = selectedDepth - 1;
                                    const firstImmediate = response.data.find(u => parseInt(u.designation_depth || 0) === targetDepth);
                                    if (firstImmediate) {
                                        console.log('Auto-selecting immediate superior:', firstImmediate.name);
                                        $select.val(firstImmediate.id).trigger('change');
                                    }
                                }
                            } else {
                                $select.empty().append('<option value="">No superiors found</option>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching superior users:', error);
                            $select.prop('disabled', false).empty().append('<option value="">Error loading managers</option>');
                        }
                    });
                } else {
                    // 2. If no designation is selected, show ALL box users (everyone) from the pre-loaded list
                    if (typeof allBoxUsers !== 'undefined' && Array.isArray(allBoxUsers)) {
                        renderReportingManagerOptions($select, allBoxUsers, selectedId, null);
                    }
                }
            }

            // Helper to render the options into the Select2 dropdown
            function renderReportingManagerOptions($select, users, selectedId = null, activeFilterDesignation = null, selectedDepth = null) {
                $select.empty().append('<option value="">Select Reporting Manager</option>');

                // Find existing RMs for recommendations (from global allEmployees)
                let existingRMs = new Set();
                if (activeFilterDesignation && typeof allEmployees !== 'undefined') {
                    allEmployees.forEach(emp => {
                        if (String(emp.designation) === String(activeFilterDesignation) && emp.reporting_manager_id) {
                            existingRMs.add(String(emp.reporting_manager_id));
                        }
                    });
                }

                let hierarchyRec = [];
                let otherRec = [];
                let others = [];
                const currentEditingEmpId = $('#edit_employee_id').val();

                // Sort users: 1. Admins, 2. By depth (closest to selectedDepth first)
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
                    // Don't list the employee themselves
                    if (currentEditingEmpId && String(emp.id) === String(currentEditingEmpId)) return;

                    const isCurrentSelected = selectedId && String(emp.id) === String(selectedId);
                    
                    // If we are filtering by designation, the API already returned only valid superiors + admins.
                    let isHierarchy = activeFilterDesignation ? true : false;
                    let isOtherRec = false;
                    let reason = "";
                    let highlightType = ""; // "immediate", "higher", "admin"

                    if (isHierarchy) {
                        const isAdmin = emp.staff_role && emp.staff_role.toLowerCase() === 'admin';
                        const empDepth = parseInt(emp.designation_depth || 0);
                        
                        console.log(`Checking user ${emp.name}: Depth=${empDepth}, SelectedDepth-1=${selectedDepth - 1}`);

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
                    }

                    // Existing RM recommendation
                    if (existingRMs.has(String(emp.id))) {
                        if (isHierarchy) {
                            reason += " & Existing RM";
                        } else {
                            isOtherRec = true;
                            reason = " (Existing RM)";
                        }
                    }

                    if (isHierarchy || isCurrentSelected) {
                        hierarchyRec.push({ ...emp, recommendationReason: reason, isHierarchy: true, highlightType: highlightType });
                    } else if (isOtherRec) {
                        otherRec.push({ ...emp, recommendationReason: reason, isHierarchy: false });
                    } else {
                        others.push(emp);
                    }
                });

                // Helper to create option element
                const createOption = (emp) => {
                    const suffix = emp.recommendationReason || "";
                    const designation = emp.designation_name || 'No Designation';
                    return $('<option></option>')
                        .val(emp.id)
                        .text(`${emp.name}${emp.employee_id ? ' (' + emp.employee_id + ')' : ''}${suffix} [${designation}]`)
                        .attr('selected', selectedId && String(emp.id) === String(selectedId))
                        .data('is-hierarchy', !!emp.isHierarchy)
                        .data('highlight-type', emp.highlightType || "")
                        .data('is-existing-rm', !!(emp.recommendationReason && emp.recommendationReason.includes('Existing RM')))
                        .data('designation-name', designation);
                };

                if (hierarchyRec.length > 0) {
                    const $hGroup = $('<optgroup label="Direct Hierarchy (Primary Recommendations)"></optgroup>');
                    hierarchyRec.forEach(emp => $hGroup.append(createOption(emp)));
                    $select.append($hGroup);
                }

                if (otherRec.length > 0) {
                    const $oGroup = $('<optgroup label="Other Suggested Managers"></optgroup>');
                    otherRec.forEach(emp => $oGroup.append(createOption(emp)));
                    $select.append($oGroup);
                }

                if (others.length > 0) {
                    const label = (hierarchyRec.length > 0 || otherRec.length > 0) ? "Other Employees" : "All Employees";
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
                        dropdownParent: $select.closest('.modal'),
                        templateResult: reportingManagerTemplateResult,
                        templateSelection: reportingManagerTemplateSelection
                    });
                }
            }

            // Extract templates to keep it clean
            function reportingManagerTemplateResult(data) {
                if (!data.id) return data.text;
                var $el = $(data.element);
                var isHierarchy = $el.data('is-hierarchy') === true || $el.data('is-hierarchy') === "true";
                var highlightType = $el.data('highlight-type') || "";
                
                if (isHierarchy) {
                    let bgColor = "#fff3e0"; // Default orange for higher hierarchy
                    let borderColor = "#ff9800";
                    let badgeText = "Hierarchy Parent";
                    let badgeClass = "bg-warning";
                    
                    if (highlightType === "immediate") {
                        bgColor = "#e8f5e9"; // Light green for immediate superior
                        borderColor = "#4caf50";
                        badgeText = "Immediate Superior";
                        badgeClass = "bg-success";
                    } else if (highlightType === "admin") {
                        bgColor = "#f3e5f5"; // Light purple for admin
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

            function reportingManagerTemplateSelection(data) {
                if (!data.id) return data.text;
                var $el = $(data.element);
                var isHierarchy = $el.data('is-hierarchy') === true || $el.data('is-hierarchy') === "true";
                var highlightType = $el.data('highlight-type') || "";

                if (isHierarchy) {
                    let iconClass = "ti ti-crown text-warning";
                    if (highlightType === "immediate") {
                        iconClass = "ti ti-star-filled text-success";
                    }
                    return $('<span><i class="' + iconClass + ' me-1"></i> ' + data.text + '</span>');
                }
                return data.text;
            }

            function initEmployeeIdHandler({
                checkboxSelector,
                inputSelector,
                spanPrint,
                clearBtnSelector,
                modalId,
                fetchEmployeeIdUrl,
                updateEmployeeIdUrl
            }) {
                const $checkbox = $(checkboxSelector);
                const $input = $(inputSelector);
                const $spanPrint = $(spanPrint); // Header span element
                const $clearBtn = $(clearBtnSelector);
                const $modalForm = $('#customEmployeeIdForm');
                const $modalInput = $('#customEmployeeIdInput');
                const modalEl = document.getElementById(modalId);
                const modal = modalEl ? new bootstrap.Modal(modalEl) : null;

                // Helper function to update both input and header span
                function updateEmployeeIdDisplay(employeeId) {
                    $input.val(employeeId);
                    if ($spanPrint.length) {
                        $spanPrint.text(employeeId || '');
                    }
                }

                // Fetch default employee ID
                async function fetchDefaultEmployeeId() {
                    try {
                        const response = await $.get(fetchEmployeeIdUrl);

                        if (response.status && response.employee_id) {
                            updateEmployeeIdDisplay(response.employee_id);

                            if (response.dynamic_enabled) {
                                $checkbox.prop('checked', true);
                                $input.prop('readonly', false);
                            } else {
                                $checkbox.prop('checked', false);
                                $input.prop('readonly', true);
                            }
                        }
                    } catch (error) {
                        console.error('Failed to fetch default employee ID:', error);
                    }
                }

                // Submit modal form
                $modalForm.on('submit', async function(e) {
                    e.preventDefault();

                    const customPrefix = $modalInput.val().trim();

                    if (customPrefix.length < 2) {
                        $modalInput.addClass('is-invalid');
                        return;
                    }

                    $modalInput.removeClass('is-invalid');

                    try {
                        const res = await $.ajax({
                            url: updateEmployeeIdUrl,
                            type: 'PUT',
                            data: {
                                employee_prefix: customPrefix,
                                _token: '{{ csrf_token() }}'
                            }
                        });

                        if (res.status && res.employee_id) {
                            updateEmployeeIdDisplay(res.employee_id);
                            $input.prop('readonly', false);
                            modal.hide();
                        }
                    } catch (err) {
                        console.error('Failed to save custom employee ID:', err);
                    }
                });

                // Handle checkbox toggle
                $checkbox.on('change', async function() {
                    if ($(this).is(':checked')) {
                        $input.prop('readonly', false);
                        $modalInput.val('');
                        if (modal) modal.show();
                    } else {
                        // Send employee_prefix = 0
                        try {
                            const res = await $.ajax({
                                url: updateEmployeeIdUrl,
                                type: 'PUT',
                                data: {
                                    employee_prefix: 0,
                                    _token: '{{ csrf_token() }}'
                                }
                            });

                            if (res.status && res.employee_id) {
                                updateEmployeeIdDisplay(res.employee_id);
                                $input.prop('readonly', true);
                            }
                        } catch (err) {
                            console.error('Failed to disable dynamic ID:', err);
                        }
                    }
                });

                // Initial fetch
                fetchDefaultEmployeeId();
            }

            // Initialize with config
            initEmployeeIdHandler({
                checkboxSelector: '#custom_employee_id_checkbox',
                inputSelector: '#employee_id_input',
                spanPrint: '#top_header_employee',
                clearBtnSelector: '#clear_employee_id', // Optional if removed
                modalId: 'customEmployeeIdModal',
                fetchEmployeeIdUrl: "{{ route('checkEmployeePrefix', ['box_slug' => request()->route('box_slug')]) }}",
                updateEmployeeIdUrl: "{{ route('updateEmployeeId', ['box_slug' => request()->route('box_slug')]) }}"
            });






            function updateDashboardStats(stats) {
                $('#total_employee_count').text(stats.recordsTotal || 0);
                $('#active_employee_count').text(stats.active_users || 0);
                $('#inactive_employee_count').text(stats.inactive_users || 0);
                $('#new_joiners_count').text(stats.new_joinees || 0);
            }

            function populateDataTable(employees) {
                const employeeDetailsRoute =
                    '{{ route('employee.details', ['box_slug' => request()->route('box_slug')]) }}';
                const dataSet = employees.map(emp => {
                    // Add cache-busting to image URLs
                    const imageUrl = emp.profile_image_path ?
                        `${emp.profile_image_url}?t=${new Date().getTime()}&id=${emp.id}` :
                        null;
                    const profileImage = emp.profile_image_path ?
                        `<img src="${imageUrl}" class="img-fluid rounded-circle employee-profile-img" alt="img" data-emp-id="${emp.id}" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'letter-avatar\\' data-name=\\'${emp.name || 'U'}\\'>${(emp.name || 'U').charAt(0).toUpperCase()}</div>';" />` :
                        `<div class="letter-avatar" data-name="${emp.name || 'U'}">${(emp.name || 'U').charAt(0).toUpperCase()}</div>`;
                    return [
                        `<div class="form-check form-check-md">
                <input class="form-check-input row-checkbox" type="checkbox" data-employee-id="${emp.id}" />
            </div>`,
                        `<div class="d-flex align-items-center">
                <a href="${employeeDetailsRoute}?emp_id=${emp.id}" class="avatar avatar-md">
                    ${profileImage}
                </a>
                <div class="ms-2">
                    <p class="text-dark mb-0">
                        <a href="${employeeDetailsRoute}?emp_id=${emp.id}">${emp.first_name || emp.name}</a>
                    </p>
                </div>
            </div>`,
                        emp.last_name || 'N/A',
                        `<a href="${employeeDetailsRoute}?emp_id=${emp.id}">${emp.employee_id}</a>`,
                        emp.staff_role === 'admin' ? 'Owner' : (emp.designation_name ?? 'N/A'),
                        emp.staff_role === 'admin' ? '' : (emp.reporting_manager_name ?? 'N/A'),
                        emp.staff_role === 'admin' ? 'Owner' : (emp.department_name ?? 'N/A'),
                        emp.staff_role === 'admin' ? 'NA' : (emp.employee_type_name ?? 'N/A'),
                        emp.work_mode_name ?? 'N/A',
                        emp.phone ?? 'N/A',
                        emp.email ?? 'N/A',
                        emp.joining_date ? new Date(emp.joining_date).toLocaleDateString() : 'N/A',
                        emp.call_logs ? '<span class="badge badge-soft-success">Enabled</span>' : '<span class="badge badge-soft-danger">Disabled</span>',
                        emp.status === 'Enabled' ? '<span class="badge badge-soft-success">Enabled</span>' : '<span class="badge badge-soft-danger">Disabled</span>',
                        `<div class="action-icon d-inline-flex flex-column align-items-start" style="gap: 10px;">
                <div class="d-inline-flex" style="gap: 15px;">
                    <a href="#" class="edit-employee-btn" data-bs-toggle="modal" data-bs-target="#edit_employee" data-emp-id="${emp.id}" title="Edit">
                        <i class="ti ti-edit"></i>
                    </a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#delete_modal" data-emp-id="${emp.id}" data-emp-name="${emp.first_name || emp.name || ''}" data-emp-email="${emp.email || ''}" data-emp-phone="${emp.phone || ''}" title="Delete">
                        <i class="ti ti-trash"></i>
                    </a>
                    <a href="#"
   class="toggle-status"
   data-emp-id="${emp.id}"
   data-status="${emp.status}"
   title="${emp.status === 'Enabled' ? 'Set Inactive' : 'Set Active'}">
    ${emp.status === 'Enabled'
        ? '<i class="ti ti-toggle-right text-success"></i>'
        : '<i class="ti ti-toggle-left text-danger"></i>'}
</a>

                </div>
      <div class="mt-2">
    ${!emp.user.email_verified_at
        ? `<button class="btn btn-sm btn-outline-danger resend-verification-btn" data-emp-id="${emp.user_id}" title="Resend Verification">
                            <i class="fa-solid fa-envelope"></i>
                       </button>`
        : ''}
</div>


            </div>`
                    ];
                });

                // Check if table element exists
                const $table = $('#employee-list-table');
                if ($table.length === 0) {
                    console.error('Table element not found');
                    return;
                }

                if ($.fn.dataTable.isDataTable('#employee-list-table')) {
                    // Clear and reload table data
                    employeeTable.clear().rows.add(dataSet).draw(false); // false = don't reset paging

                    // Force refresh of all images in the table with new cache-busting
                    setTimeout(function() {
                        // First, update all existing images
                        $('.employee-profile-img').each(function() {
                            var $img = $(this);
                            var empId = $img.data('emp-id');
                            if (empId) {
                                // Find the employee data from the employees array passed to populateDataTable
                                var employee = employees.find(function(emp) {
                                    return emp.id == empId;
                                });

                                if (employee && employee.profile_image_url) {
                                    // Use the employee's image URL with cache-busting
                                    var imageUrl = employee.profile_image_url.replace(/\\/g, '/');
                                    var separator = imageUrl.includes('?') ? '&' : '?';
                                    var newSrc = imageUrl + separator + 't=' + new Date().getTime() + '&id=' + empId;
                                    $img.attr('src', newSrc);
                                    // Force reload
                                    $img[0].src = newSrc;
                                } else {
                                    // If no image URL, just update cache-busting
                                    var currentSrc = $img.attr('src');
                                    if (currentSrc) {
                                        var baseUrl = currentSrc.split('?')[0];
                                        var newSrc = baseUrl + '?t=' + new Date().getTime() + '&id=' + empId;
                                        $img.attr('src', newSrc);
                                        $img[0].src = newSrc;
                                    }
                                }
                            }
                        });
                    }, 150);

                    // Apply column visibility after data reload
                    setTimeout(function() {
                        applyColumnVisibility();
                        // Show action buttons for admins after table reload
                        if (window.PermissionHelper) {
                            applyPermissionBasedUI();
                        }
                    }, 200);
                } else {
                    // STRICT: For admins, always show Action column (index 11) before table initialization
                    const isAdmin = (window.PermissionHelper && window.PermissionHelper.isAdmin) ||
                                   (window.PermissionHelper && window.PermissionHelper.staffRole === 'admin') || false;
                    if (isAdmin) {
                        const actionColumn = columnDefinitions.find(c => c.index === 14);
                        if (actionColumn) {
                            actionColumn.visible = true; // Force Action column visible for admins
                        }
                    }

                    // Ensure table element exists before initializing DataTable
                    if ($table.length === 0) {
                        console.error('Table element not found for DataTable initialization');
                        return;
                    }

                    employeeTable = $table.DataTable({
                        data: dataSet,
                        columns: [{
                                title: '',
                                orderable: false,
                                visible: columnDefinitions[0].visible
                            },
                            {
                                title: 'First Name',
                                visible: columnDefinitions[1].visible
                            },
                            {
                                title: 'Last Name',
                                visible: columnDefinitions[2].visible
                            },
                            {
                                title: 'Emp ID',
                                visible: columnDefinitions[3].visible
                            },
                            {
                                title: 'Designation',
                                visible: columnDefinitions[4].visible
                            },
                            {
                                title: 'Reporting To',
                                visible: columnDefinitions[5].visible
                            },
                            {
                                title: 'Department',
                                visible: columnDefinitions[6].visible
                            },
                            {
                                title: 'Employee Type',
                                visible: columnDefinitions[7].visible
                            },
                            {
                                title: 'Working Mode',
                                visible: columnDefinitions[8].visible
                            },
                            {
                                title: 'Mobile No.',
                                visible: columnDefinitions[9].visible
                            },
                            {
                                title: 'Email ID',
                                visible: columnDefinitions[10].visible
                            },
                            {
                                title: 'Joining Date',
                                visible: columnDefinitions[11].visible
                            },
                            {
                                title: 'Call Logs',
                                visible: columnDefinitions[12].visible
                            },
                            {
                                title: 'Status',
                                visible: columnDefinitions[13].visible
                            },
                            {
                                title: 'Action',
                                orderable: false,
                                visible: columnDefinitions[14].visible
                            }
                        ],
                        order: [
                            [1, 'asc']
                        ],
                        pageLength: 10,
                        lengthMenu: [5, 10, 25, 50],
                        responsive: true,
                        createdRow: function(row, data, dataIndex) {
                            // Bind click specifically to the highlighted content area
                            $(row).on('click', function(e) {
                                // 1. Prevent redirection if user is selecting text (copying)
                                if (window.getSelection().toString().length > 0) {
                                    return;
                                }

                                // 2. Prevent redirection if clicking on checkbox, buttons, or existing links
                                if ($(e.target).closest('.form-check, a, button, i').length > 0) {
                                    return;
                                }

                                // 3. User Restriction: "redirect when click on highlighted"
                                var $clickable = $(e.target).closest('.employee-clickable-content');
                                if ($clickable.length > 0) {
                                    const empId = employees[dataIndex].id;
                                    window.location.href = `${employeeDetailsRoute}?emp_id=${empId}`;
                                }
                            });
                        },
                        drawCallback: function() {
                            var api = this.api();
                            api.rows({ page: 'current' }).every(function () {
                                var row = this.node();
                                $('td', row).each(function () {
                                    var $cell = $(this);
                                    
                                    // Skip checkbox column
                                    if ($cell.find('input[type="checkbox"]').length > 0) return;
                                    // Skip Action column
                                    if ($cell.find('.action-icon').length > 0) return;

                                    var text = $cell.text().trim();
                                    var emptyValues = ['', '--', '-', '—', 'N/A', 'n/a', 'NA'];
                                    if (emptyValues.includes(text)) return;

                                    if ($cell.find('input, button, a, .dropdown, img, .letter-avatar').length > 0) {
                                        // Wrap raw text nodes
                                        $cell.contents().filter(function() {
                                            return this.nodeType === 3 && this.textContent.trim().length > 0; 
                                        }).wrap('<span class="employee-clickable-content"></span>');
                                        
                                        $cell.children('span:not([class])').addClass('employee-clickable-content');
                                        $cell.children('div:not([class])').addClass('employee-clickable-content');
                                    } else {
                                        $cell.wrapInner('<span class="employee-clickable-content"></span>');
                                    }
                                });
                            });

                            // Apply column visibility after table is drawn
                            applyColumnVisibility();
                            bindResendVerification();
                            assignAvatarColors();
                            // Re-apply permission-based UI whenever table redraws (only if PermissionHelper is loaded)
                            if (window.PermissionHelper) {
                                applyPermissionBasedUI();
                            }
                        }
                    });

                    // Apply initial column visibility after table initialization
                    setTimeout(function() {
                        applyColumnVisibility();
                        // Show action buttons for admins after table initialization
                        if (window.PermissionHelper) {
                            applyPermissionBasedUI();
                        }
                    }, 200);
                }

                // Bind global header search to DataTable
                $('.nav-search-inputs input[type="text"]').off('keyup search').on('keyup search', function() {
                    if (employeeTable) {
                        employeeTable.search(this.value).draw();
                    }
                });

                // Prevent form submission from global search on this page
                $('.nav-search-inputs form').off('submit').on('submit', function(e) {
                    e.preventDefault();
                });
            }

            // Open Manage Columns Modal
            $('#manageColumnsModal').on('show.bs.modal', function() {
                populateColumnsModal();
            });

            // Show All Columns
            $('#showAllColumns').on('click', function() {
                $('.column-toggle').prop('checked', true);
            });

            // Hide All Columns
            $('#hideAllColumns').on('click', function() {
                $('.column-toggle').prop('checked', false);
            });

            // Apply Column Changes
            $('#applyColumnChanges').on('click', function() {
                $('.column-toggle').each(function() {
                    const columnIndex = parseInt($(this).data('column-index'));
                    const isVisible = $(this).is(':checked');
                    const col = columnDefinitions.find(c => c.index === columnIndex);
                    if (col) {
                        col.visible = isVisible;
                    }
                });

                // STRICT: For admins, always keep Action column (index 11) visible
                const isAdmin = (window.PermissionHelper && window.PermissionHelper.isAdmin) || false;
                if (isAdmin) {
                    const actionColumn = columnDefinitions.find(c => c.index === 13);
                    if (actionColumn) {
                        actionColumn.visible = true; // Force Action column visible for admins
                        // Also update the checkbox in modal
                        $('#col_12').prop('checked', true);
                    }
                }

                saveColumnPreferences();

                // Apply column visibility and redraw table immediately
                if (employeeTable) {
                    // Update column visibility
                    columnDefinitions.forEach(col => {
                        try {
                            employeeTable.column(col.index).visible(col.visible, false);
                        } catch (e) {
                            console.error('Error applying column visibility:', e);
                        }
                    });
                    // Adjust columns and redraw
                    employeeTable.columns.adjust().draw(false);
                }

                // Close modal
                $('#manageColumnsModal').modal('hide');
            });

            function getColorFromName(name) {
                const colors = [
                    "#f44336", "#e91e63", "#9c27b0", "#673ab7", "#3f51b5",
                    "#2196f3", "#03a9f4", "#00bcd4", "#009688", "#4caf50",
                    "#8bc34a", "#cddc39", "#ffc107", "#ff9800", "#ff5722"
                ];
                const char = name.trim().charAt(0).toUpperCase();
                const index = char.charCodeAt(0) % colors.length;
                return colors[index];
            }

            function assignAvatarColors() {
                document.querySelectorAll('.letter-avatar[data-name]').forEach(el => {
                    const name = el.getAttribute('data-name') || '';
                    el.style.backgroundColor = getColorFromName(name);
                });
            }

            function bindResendVerification() {
                $('.resend-verification-btn').off('click').on('click', function() {
                    const empId = $(this).data('emp-id');
                    const $btn = $(this);
                    if (!empId) return;
                    $btn.prop('disabled', true).text('Sending...');
                    $.ajax({
                        url: '{{ route('employees.resend.verification', ['box_slug' => request()->route('box_slug')]) }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            userid: empId
                        },
                        success: function(response) {
                            if (response.status) {
                                showSuccessToast(response.message);
                                $btn
                                    .text('Message Sent')
                                    .removeClass('btn-outline-danger')
                                    .addClass('btn-outline-success')
                                    .prop('disabled', false);
                            } else {
                                showErrorToast(response.message || 'Failed to resend verification.');
                                $btn.prop('disabled', false).text('Resend Verification');
                            }
                        },
                        error: function() {
                            showErrorToast('An unexpected error occurred.');
                            $btn.prop('disabled', false).text('Resend Verification');
                        }
                    });
                });
            }
            // 3. Bind form submission for add/edit employee
            function bindFormSubmission() {
                const $form = $('#employeeForm');
                const phoneInput = document.querySelector('#phone');
                $form.on('submit', function(e) {
                    e.preventDefault();
                    $form.find('.is-invalid').removeClass('is-invalid');
                    $form.find('.invalid-feedback').remove();
                    let isValid = true;


            
                    function markInvalid($input, message = 'This field is required') {
                        isValid = false;
                        
                        // If the input is hidden (like designation), target its visible counterpart
                        let $target = $input;
                        if ($input.attr('type') === 'hidden') {
                            const id = $input.attr('id');
                            if (id && $(`#${id}_visible`).length) {
                                $target = $(`#${id}_visible`);
                            }
                        }
                        
                        $target.addClass('is-invalid');
                        
                        // Handle input-group cases for error message placement
                        let $errorTarget = $target;
                        if ($target.parent('.input-group').length) {
                            $errorTarget = $target.parent('.input-group');
                        }

                        if ($errorTarget.next('.invalid-feedback').length === 0) {
                            $errorTarget.after(`<div class="invalid-feedback d-block">${message}</div>`);
                        }
                    }
                    const requiredFields = [{
                            name: 'name',
                            label: 'First Name'
                        },
                        {
                            name: 'employee_id',
                            label: 'Employee ID'
                        },
                        {
                            name: 'email',
                            label: 'Email'
                        },
                        {
                            name: 'joining_date',
                            label: 'Joining Date'
                        }
                    ];
                    requiredFields.forEach(field => {
                        const $input = $form.find(`[name="${field.name}"]`);
                        if (!$input.val().trim()) {
                            markInvalid($input, `${field.label} is required`);
                        }
                    });
                    const $department = $form.find('select[name="department"]');
                    if (!$department.val()) {
                        markInvalid($department, 'Department is required');
                    }
                    const $designation = $form.find('[name="designation"]');
                    if (!$designation.val()) {
                        markInvalid($designation, 'Designation is required');
                    }
                    /* const $staff_role = $form.find('select[name="staff_role"]');
                    if (!$staff_role.val()) {
                        markInvalid($staff_role, 'Staff Role is required');
                    } */
                    const phoneVal = phoneInput.value.trim();
                    if (!phoneVal) {
                        markInvalid($(phoneInput), 'Phone number is required');
                    } else if (!/^\d{10}$/.test(phoneVal)) {
                        markInvalid($(phoneInput), 'Phone number must be exactly 10 digits');
                    }
                    // Timing validations if user_box_timing not checked
                    if (!$form.find('input[name="user_box_timing"]').is(':checked')) {
                        const workingDays = $form.find('select[name="working_days[]"]');
                        const dayStart = $form.find('input[name="day_start_time"]');
                        const dayEnd = $form.find('input[name="day_end_time"]');
                        const allowedIp = $form.find('input[name="allowed_ips[]"]');
                        if (!workingDays.val()) markInvalid(workingDays, 'Select at least one working day');
                        if (!dayStart.val()) markInvalid(dayStart, 'Start time is required');
                        if (!dayEnd.val()) markInvalid(dayEnd, 'End time is required');
                        if (!allowedIp.val()) markInvalid(allowedIp, 'Enter allowed IP');
                    }
                    if (!isValid) return;
                    // Submit via AJAX
                    const formData = new FormData(this);
                    // Add permissions to form data
                    const permissions = getSelectedPermissions('add');
                    formData.append('permissions', JSON.stringify(permissions));
                    const $submitBtn = $form.find('button[type="submit"]');
                    $submitBtn.prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: '{{ route('store.employee', ['box_slug' => request()->route('box_slug')]) }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $submitBtn.prop('disabled', false).text('Save');
                            $form[0].reset();
                            $('#add_employee').modal('hide');
                            showSuccessModal(response.data);
                            loadEmployees();

                            // Reload page to update sidebar with new permissions
                            // This ensures sidebar reflects permission changes immediately
                            setTimeout(function() {
                                window.location.reload();
                            }, 1500);
                        },
                        error: function(xhr) {
                            $submitBtn.prop('disabled', false).text('Save');
                            
                            // Show top-level message in toast if present
                            if (xhr.responseJSON?.message) {
                                showErrorToast(xhr.responseJSON.message);
                            }

                            if (xhr.responseJSON?.errors) {
                                Object.keys(xhr.responseJSON.errors).forEach(field => {
                                    const $field = $form.find(`[name="${field}"]`);
                                    markInvalid($field, xhr.responseJSON.errors[field][0]);
                                });
                            } else if (!xhr.responseJSON?.message) {
                                // Only show generic alert if no specific message was shown
                                showErrorToast('Something went wrong.');
                            }
                        }
                    });
                });
                // Clear errors on input/change
                $form.find('input, select').on('input change', function() {
                    $(this).removeClass('is-invalid');
                    
                    // Also clear error on visible counterpart if this is a hidden input
                    const id = $(this).attr('id');
                    if (id && $(`#${id}_visible`).length) {
                        $(`#${id}_visible`).removeClass('is-invalid');
                    }
                    
                    if ($(this).parent('.input-group').length) {
                        $(this).parent('.input-group').next('.invalid-feedback').remove();
                    } else {
                        $(this).next('.invalid-feedback').remove();
                    }
                });

                // Also clear errors when the visible counterpart is explicitly interacted with or changes
                $form.find('[id$="_visible"]').on('input change', function() {
                    $(this).removeClass('is-invalid');
                    const hiddenId = $(this).attr('id').replace('_visible', '');
                    $(`#${hiddenId}`).removeClass('is-invalid');
                    
                    if ($(this).parent('.input-group').length) {
                        $(this).parent('.input-group').next('.invalid-feedback').remove();
                    } else {
                        $(this).next('.invalid-feedback').remove();
                    }
                });
            }

            function showSuccessModal(employee) {
                const name = employee.name ?? 'Employee';
                const empId = employee.employee_id ?? employee.id ?? 'EMP-????';

                $('#success_emp_name').text(name);
                $('#success_emp_id').text(`#${empId}`);

                const baseDetailUrl = "{{ route('employee.details', ['box_slug' => request()->route('box_slug')]) }}";
                $('#success_detail_link').attr('href', `${baseDetailUrl}?emp_id=${employee.id}`);

                // Show modal
                $('#success_modal').modal('show');
            }

            // 4. Export buttons bind
            // function bindExportButtons() {
            //     $('#export-excel').on('click', function(e) {
            //         e.preventDefault();
            //         window.location.href =
            //             '{{ route('employees.export.excel', ['box_slug' => request()->route('box_slug')]) }}';
            //     });
            //     $('#export-pdf').on('click', function(e) {
            //         e.preventDefault();
            //         window.location.href =
            //             '{{ route('employees.export.pdf', ['box_slug' => request()->route('box_slug')]) }}';
            //     });
            // }
            $('#export-excel').on('click', function() {
                $.ajax({
                    url: "{{ route('employees.export.excel', ['box_slug' => request()->route('box_slug')]) }}",
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        showSuccessToast(response.message || 'Export started!');
                        console.log(response);

                        if (response.file_url) {
                            waitForFileDownload(response.file_url);
                        }
                    },
                    error: function(xhr, status, error) {
                        showErrorToast('Export failed. Check console for details.');
                        console.error(xhr.responseText);
                    }
                });
            });


            function waitForFileDownload(fileUrl) {
                const interval = setInterval(function() {
                    $.ajax({
                        url: fileUrl,
                        type: 'HEAD',
                        success: function() {
                            clearInterval(interval);
                            const link = document.createElement('a');
                            link.href = fileUrl;
                            link.download = '';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                            showSuccessToast('Your download should start now!');
                        },
                        error: function() {
                            console.log('Waiting for file to be ready...');
                        }
                    });
                }, 3000);
            }
            $('#export-excel').prop('disabled', true);
            $('#export-excel').prop('disabled', false);

            // 5. Bind delete modal and deletion logic
            function bindModalEvents() {
                $(document).on('click', '[data-bs-target="#delete_modal"]', function() {
                    deleteEmployeeId = $(this).data('emp-id');
                    const empName = $(this).data('emp-name');
                    const empEmail = $(this).data('emp-email');
                    const empPhone = $(this).data('emp-phone');
                    
                    $('#delete_count').text(1);
                    
                    let detailsHtml = `<div class="p-3 bg-light rounded mt-3 mb-3 text-start">`;
                    detailsHtml += `<h5 class="mb-1 text-dark fw-bold">${empName || 'This employee'}</h5>`;
                    
                    if (empEmail || empPhone) {
                        detailsHtml += `<div class="d-flex flex-column gap-1 mt-2">`;
                        if (empEmail) {
                            detailsHtml += `<div class="d-flex align-items-center text-muted fs-14">
                                                <i class="ti ti-mail me-2"></i> <span class="text-truncate">${empEmail}</span>
                                            </div>`;
                        }
                        if (empPhone) {
                            detailsHtml += `<div class="d-flex align-items-center text-muted fs-14">
                                                <i class="ti ti-phone me-2"></i> <span>${empPhone}</span>
                                            </div>`;
                        }
                        detailsHtml += `</div>`;
                    }
                    detailsHtml += `</div>`;
                    
                    $('#delete_modal_text').html(
                        `Are you sure you want to delete this employee?
                        ${detailsHtml}
                        <div class="text-danger fw-medium">This action cannot be undone.</div>`
                    );
                });
                $('#confirm_delete_btn').on('click', function() {
                    if (!deleteEmployeeId) return;
                    $.ajax({
                        url: '{{ route('delete.employee', ['box_slug' => request()->route('box_slug')]) }}',
                        type: 'DELETE',
                        data: {
                            employee_id: deleteEmployeeId
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#delete_modal').modal('hide');
                            showSuccessToast(response.message || 'Employee deleted successfully.');
                            loadEmployees();
                            deleteEmployeeId = null;
                        },
                        error: function(xhr) {
                            $('#delete_modal').modal('hide');
                            showErrorToast(xhr.responseJSON?.error || xhr.responseJSON?.message || 'Failed to delete employee.');
                            deleteEmployeeId = null;
                        }
                    });
                });
            }
            // 6. Bind status toggle buttons logic
            function bindStatusToggle() {
                $(document).off('click', '.toggle-status').on('click', '.toggle-status', function(e) {
                    e.preventDefault();

                    toggleEmployeeId = $(this).data('emp-id');
                    const isCurrentlyActive = $(this).find('i').hasClass(
                    'ti-toggle-right'); // Assuming 'ti-toggle-right' = active
                    const toggleNewStatus = isCurrentlyActive ? 'Disabled' :
                    'Enabled'; // Toggle to opposite

                    // Set confirmation text
                    $('#toggle_action_text').text(`Set this employee as ${toggleNewStatus}`);
                    $('#toggle_status_modal').modal('show');

                    // Store the new status in a data attribute
                    $('#confirm_toggle_btn').data('new-status', toggleNewStatus);
                });

                $('#confirm_toggle_btn').off('click').on('click', function() {
                    if (!toggleEmployeeId) return;

                    const status = $(this).data('new-status'); // "Enabled" or "Disabled"

                    $.ajax({
                        url: '{{ route('employee.toggle', ['box_slug' => request()->route('box_slug')]) }}',
                        type: 'PUT',
                        data: {
                            employee_id: toggleEmployeeId,
                            status: status
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function() {
                            $('#toggle_status_modal').modal('hide');
                            showSuccessToast('Status updated successfully');
                            loadEmployees(); // Reload table or list
                            toggleEmployeeId = null;
                        },
                        error: function(xhr) {
                            $('#toggle_status_modal').modal('hide');
                            showErrorToast(xhr.responseJSON?.message || 'Failed to update status.');
                            toggleEmployeeId = null;
                        }
                    });
                });
            }

            // 7. Bind edit button to populate the edit form
            let allTeamsByDepartment = {};
            let departmentOptions = {};
            window.teamOptions = {};
            $(document).ready(function() {
                fetchDepartmentsAndTeams(function() {
                    bindEditButton();
                });

                // Add Employee - Department change
                $('#department').on('change', function() {
                    const deptId = $(this).val();
                    populateTeams(deptId);
                    const designationId = $('#designation').val();
                    populateReportingManagerSelect($('#reporting_manager_add'), deptId || null,
                        designationId || null);
                });

                // Add Employee - Designation change
                $('#designation').on('change', function() {
                    const deptId = $('#department').val();
                    const designationId = $(this).val();
                    populateReportingManagerSelect($('#reporting_manager_add'), deptId || null,
                        designationId || null);
                });

                // Add Employee - Fetch Types on Modal Show
                $('#add_employee').on('show.bs.modal', function() {
                    fetchEmployeeTypes();
                    // Ensure reporting manager select is populated (show everyone initially)
                    populateReportingManagerSelect($('#reporting_manager_add'), null, null, null);
                });

                // Edit Employee - Department change
                $('#departmentedit').on('change', function() {
                    const deptId = $(this).val();
                    populateEditTeams(deptId);
                    const designationId = $('#designationedit').val();
                    populateReportingManagerSelect($('#reporting_manager_edit'), deptId || null,
                        designationId || null);
                });

                // Edit Employee - Designation change
                $('#designationedit').on('change', function() {
                    const deptId = $('#departmentedit').val();
                    const designationId = $(this).val();
                    populateReportingManagerSelect($('#reporting_manager_edit'), deptId || null,
                        designationId || null);
                });
            });

            function fetchDepartmentsAndTeams(callback = () => {}) {
                $.ajax({
                    url: '{{ route('department.manage', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 200) {
                            const departments = response.data;
                            departmentOptions = {};
                            window.teamOptions = {};
                            $('#department, #departmentedit').empty().append(
                                '<option value="">Select Department</option>');
                            departments.forEach(dept => {
                                departmentOptions[dept.id] = dept.name;
                                window.teamOptions[dept.id] = dept.all_teams || [];
                                $('#department, #departmentedit').append(
                                    `<option value="${dept.id}">${dept.name} (Dept)</option>`
                                );
                            });
                            callback();
                        } else {
                            console.error('API returned non-200 response');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching departments:', error);
                    }
                });
            }

            function fetchEmployeeTypes(callback = () => {}) {
                $.ajax({
                    url: '{{ route('employee.get.types', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 200) {
                            const types = response.data;
                            const $addSelect = $('#emp_type_add');
                            const $editSelect = $('#emp_type_edit');
                            
                            // Keep selected value if any
                            const currentAddVal = $addSelect.val();
                            const currentEditVal = $editSelect.val();

                            $addSelect.empty().append('<option disabled selected>Select Type</option>');
                            $editSelect.empty().append('<option value="" disabled selected>Select Type</option>');
                            
                            types.forEach(type => {
                                const option = `<option value="${type.id}">${type.name}</option>`;
                                $addSelect.append(option);
                                $editSelect.append(option);
                            });

                             // Restore if possible (though for Add it's usually new, for Edit it matters if we are refreshing)
                            if (currentEditVal) $editSelect.val(currentEditVal);

                            callback();
                        }
                    },
                    error: function() {
                        console.error('Failed to fetch employee types');
                        callback();
                    }
                });
            }

            function populateTeams(departmentId, selectedTeamId = '') {
                const $designationVisible = $('#designation_visible');
                const $designationHidden = $('#designation');
                
                // Clear out value if department changes
                $designationVisible.val('');
                $designationHidden.val('').trigger('change');
            }

            function populateEditTeams(departmentId, selectedTeamId = '') {
                const $designationEditVisible = $('#designationedit_visible');
                const $designationEditHidden = $('#designationedit');
                
                // Set the correct team name programmatically if edit is passing ID
                if (selectedTeamId) {
                    const teams = window.teamOptions[departmentId] || [];
                    const selectedTeam = teams.find(t => t.id == selectedTeamId);
                    if (selectedTeam) {
                        $designationEditVisible.val(selectedTeam.name + ' (Team)');
                        $designationEditHidden.val(selectedTeam.id).trigger('change');
                    }
                } else if (!window.isEditingEmployee) {
                    $designationEditVisible.val('');
                    $designationEditHidden.val('').trigger('change');
                }
            }

            window.isEditingEmployee = false;

            function bindEditButton() {
                $(document).off('click', '.edit-employee-btn').on('click', '.edit-employee-btn', function(e) {
                    e.preventDefault();
                    window.isEditingEmployee = true;
                    
                    const empId = parseInt($(this).data('emp-id'), 10);
                    const employee = allEmployees.find(emp => emp.id === empId);
                    if (!employee) {
                        showErrorToast('Employee not found.');
                        return;
                    }
                    
                    fetchEmployeeTypes(function() {
                        $('#UpdateFormEmPloyee')[0].reset();
                        $('#working_days').val([]).trigger('change');
    
                        // CRITICAL: Set employee ID FIRST before anything else
                        $('#edit_employee_id').val(employee.id);

                    // Store employee ID in form data to preserve it during image upload
                    $('#UpdateFormEmPloyee').data('employee-id', employee.id);

                    // Also store in a backup location
                    $('#UpdateFormEmPloyee').attr('data-employee-id-backup', employee.id);

                    // Clear any previous cropped image (but preserve if user just cropped)
                    const existingCroppedImage = $('#edit_cropped_image').val();
                    if (!existingCroppedImage) {
                        $('#edit_cropped_image').val('');
                        $('#UpdateFormEmPloyee').data('cropped-image', null);
                    }
                    $('#name').val(employee.first_name || employee.name || '');
                    $('#last_name').val(employee.last_name || '');
                    if (employee.gender) {
                        $('#edit_gender').val(employee.gender).trigger('change');
                    } else {
                        $('#edit_gender').val('').trigger('change');
                    }
                    const employeeId = employee.employee_id || employee.id || '';
                    $('#employee_id').val(employeeId);

                    // Update employee ID in header
                    $('#edit_top_header_employee').text(employeeId);

                    // Debug log
                    console.log('Edit employee - Employee ID:', employeeId, 'Full employee object:',
                        employee);

                    $('#email').val(employee.email || '');
                    $('#phones').val(employee.phone || '');
                    $('#joining_date').val(employee.joining_date || '');
                    $('#staff_roles').val(employee.staff_role).trigger('change');
                    
                    if (employee.staff_role === 'admin') {
                        // For Admin, hide/empty department and designation
                        $('#departmentedit').val('').trigger('change').prop('disabled', true);
                        $('#designationedit_visible').val('Owner');
                        $('#designationedit').val('Owner').trigger('change');
                        $('#designationedit_visible').closest('.input-group').find('button').prop('disabled', true);
                        
                        // Hide containers
                        $('#department_edit_container').hide();
                        $('#designation_edit_container').hide();
                        $('#reporting_manager_edit_container').hide();
                        $('#joining_date').closest('.col-md-6').hide();
                        
                        // Set Employee Type to NA
                        if ($('#emp_type_edit').find('option[value="NA"]').length === 0) {
                            $('#emp_type_edit').append(new Option('NA', 'NA'));
                        }
                        $('#emp_type_edit').val('NA').trigger('change').prop('disabled', true);
                        $('#joining_date').prop('disabled', true);
                    } else {
                        // Reset for normal employees
                        $('#department_edit_container').show();
                        $('#designation_edit_container').show();
                        $('#reporting_manager_edit_container').show();
                        $('#joining_date').closest('.col-md-6').show();
                        
                        $('#departmentedit').prop('disabled', false);
                        $('#designationedit_visible').closest('.input-group').find('button').prop('disabled', false);
                        $('#emp_type_edit').prop('disabled', false);
                        $('#joining_date').prop('disabled', false);
                        
                        $('#departmentedit').val(employee.department).trigger('change');
                        populateEditTeams(employee.department, employee.designation);
                        $('#designationedit').val(employee.designation).trigger('change');
                        $('#emp_type_edit').val(employee.emp_type).trigger('change');

                        // Populate Reporting Manager in edit modal (filter by department & designation)
                        const deptId = employee.department || null;
                        const designationId = employee.designation || null;
                        const reportingManagerId = employee.reporting_manager_id || null;
                        populateReportingManagerSelect($('#reporting_manager_edit'), deptId, designationId,
                            reportingManagerId);
                    }
                    
                    // Set Can Login Anytime
                    const canLoginAnytimeValue = employee.can_login_anytime ? 1 : 0;
                    $(`#UpdateFormEmPloyee input[name="can_login_anytime"][value="${canLoginAnytimeValue}"]`).prop('checked', true);

                    // Set Call Logs
                    const callLogsValue = employee.call_logs === undefined ? 1 : (employee.call_logs ? 1 : 0);
                    $(`#UpdateFormEmPloyee input[name="call_logs"][value="${callLogsValue}"]`).prop('checked', true);
                    
                    // Set WhatsApp Access
                    const whatsappAccessValue = employee.has_whatsapp_access ? 1 : 0;
                    $(`#UpdateFormEmPloyee input[name="has_whatsapp_access"][value="${whatsappAccessValue}"]`).prop('checked', true);

                    // Handle profile image or letter avatar - using class-based structure
                    const $imageContainer = $('#UpdateFormEmPloyee').find('.uploaded_image');

                    if (employee.profile_image_path) {
                        // Show image if available - add cache-busting to ensure fresh image
                        let imageUrl = employee.profile_image_url || '';
                        imageUrl = imageUrl.replace(/\\/g, '/');
                        // Add cache-busting parameter
                        const separator = imageUrl.includes('?') ? '&' : '?';
                        imageUrl = `${imageUrl}${separator}t=${new Date().getTime()}&id=${employee.id}`;
                        const imageHtml = `<img src="${imageUrl}" class="img-fluid rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" alt="img" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'letter-avatar\\' data-name=\\'${employee.name || 'U'}\\'>${(employee.name || 'U').charAt(0).toUpperCase()}</div>';" />`;
                        $imageContainer.html(imageHtml);
                    } else {
                        // Show letter avatar if no image
                        const firstName = (employee.name || 'U').charAt(0).toUpperCase();
                        $imageContainer.html(`<div class="letter-avatar" data-name="${employee.name || 'U'}">${firstName}</div>`);
                    }
                    $('#exlude_ip_restriction').prop('checked', employee.exlude_ip_restriction === true ||
                        employee.exlude_ip_restriction === 1);
                    $('#exclude_round_robin').prop('checked', employee.exclude_round_robin === true ||
                        employee.exclude_round_robin === 1);
                    const useBoxTiming = employee.user_box_timing === false || employee.user_box_timing ==
                        0 || employee.user_box_timing == "0";
                    $('#user_box_timing').prop('checked', useBoxTiming);
                    toggleCustomTimingFields(useBoxTiming);
                    if (useBoxTiming) {
                        $('#day_start_time').val(employee.day_start_time || '');
                        $('#day_end_time').val(employee.day_end_time || '');

                        if (Array.isArray(employee.working_days)) {
                            $('#working_days').val(employee.working_days).trigger('change');
                        }

                        if (Array.isArray(employee.allowed_ips)) {
                            $('#allowed_ips').val(employee.allowed_ips.join(', '));
                        }
                    } else {
                        $('#divcheckedit').hide();
                    }

                    // Populate permissions (using Spatie, permissions are in the data array)
                    const permissions = employee.permissions || [];
                    populateEmployeePermissions(permissions);

                    $('#edit_employee').modal('show');
                    setTimeout(() => { window.isEditingEmployee = false; }, 500);
                });
            });
                // Reset form on modal close
                $('#edit_employee').on('hidden.bs.modal', function() {
                    // Only reset if image upload is not active
                    const isImageUploadActive = $('#UpdateFormEmPloyee').data('image-upload-active');
                    if (!isImageUploadActive) {
                        $('#UpdateFormEmPloyee')[0].reset();
                        // Reset image container to default state
                        $('#UpdateFormEmPloyee').find('.uploaded_image').html('<i class="ti ti-photo text-gray-2 fs-16"></i>');
                        $('#edit_employee_id').val('');
                        $('#edit_cropped_image').val('');
                        $('#edit_top_header_employee').text(''); // Clear header employee ID
                        $('#working_days').val([]).trigger('change');
                        $('#allowed_ips').val('');
                        $('#exlude_ip_restriction, #exclude_round_robin, #user_box_timing').prop('checked',
                            false);
                        $('#divcheckedit').hide();
                        // Clear form data
                        $('#UpdateFormEmPloyee').data('employee-id', null);
                        $('#UpdateFormEmPloyee').data('cropped-image', null);
                    }
                });
            }

            function formatDate(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr);
                if (isNaN(d)) return '';
                return `${('0' + d.getDate()).slice(-2)}-${('0' + (d.getMonth() + 1)).slice(-2)}-${d.getFullYear()}`;
            }

            function bindEditFormSubmission() {
                $('#UpdateFormEmPloyee').on('submit', function(e) {
                    e.preventDefault();

                    // Get employee ID from multiple sources to ensure we have it
                    let empId = $('#edit_employee_id').val();
                    if (!empId) {
                        // Try to get from form data
                        empId = $('#UpdateFormEmPloyee').data('employee-id');
                    }
                    if (!empId) {
                        // Try to get from the edit button data
                        const $editBtn = $('.edit-employee-btn.active, .edit-employee-btn:last');
                        if ($editBtn.length) {
                            empId = $editBtn.data('emp-id');
                        }
                    }

                    // Validate that employee ID is present
                    if (!empId) {
                        showErrorToast('Employee ID is missing. Please refresh and try again.');
                        console.error('Employee ID not found. Form data:', {
                            hiddenInput: $('#edit_employee_id').val(),
                            formData: $('#UpdateFormEmPloyee').data('employee-id'),
                            editBtn: $('.edit-employee-btn').data('emp-id')
                        });
                        return;
                    }

                    const formData = new FormData(this);

                    // CRITICAL: Always set the employee ID in formData (use set to overwrite if exists)
                    formData.set('id', empId);

                    // Also ensure it's in the hidden input
                    $('#edit_employee_id').val(empId);

                    // Get cropped image from multiple sources
                    let croppedImage = $('#edit_cropped_image').val();
                    if (!croppedImage) {
                        // Try to get from form data
                        croppedImage = $('#UpdateFormEmPloyee').data('cropped-image');
                    }

                    // Ensure cropped_image is included if it exists
                    if (croppedImage) {
                        formData.set('cropped_image', croppedImage);
                        // Also ensure it's in the hidden input
                        $('#edit_cropped_image').val(croppedImage);
                    }

                    // Debug: Log what we're sending
                    console.log('Submitting form with:', {
                        employeeId: empId,
                        hasCroppedImage: !!croppedImage,
                        formDataKeys: Array.from(formData.keys())
                    });
                    $('#UpdateFormEmPloyee input[type="checkbox"][name]').each(function() {
                        const name = $(this).attr('name');
                        const isChecked = $(this).is(':checked') ? '1' : '0';
                        formData.delete(name);
                        formData.append(name, isChecked);
                    });
                    // Add permissions to form data
                    const permissions = getSelectedPermissions('edit');
                    formData.append('permissions', JSON.stringify(permissions));
                    const $submitBtn = $(this).find('button[type="submit"]');
                    $submitBtn.prop('disabled', true).text('Updating...');

                    $.ajax({
                        url: '{{ route('employee.update', ['box_slug' => request()->route('box_slug')]) }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $submitBtn.prop('disabled', false).text('Save');
                            if (response.status) {
                                // Update the employee in allEmployees array with new data
                                if (response.data && response.data.id) {
                                    const empIndex = allEmployees.findIndex(emp => emp.id === response.data.id);
                                    if (empIndex !== -1) {
                                        // Update the employee data with new image URL
                                        allEmployees[empIndex] = {
                                            ...allEmployees[empIndex],
                                            ...response.data,
                                            profile_image_url: response.data.profile_image_url || allEmployees[empIndex].profile_image_url,
                                            profile_image_path: response.data.profile_image_path || allEmployees[empIndex].profile_image_path
                                        };
                                    } else {
                                        // Employee not in array, add it
                                        allEmployees.push(response.data);
                                    }
                                }

                                // Update image in edit form BEFORE closing modal (if modal is still open)
                                if (response.data && response.data.profile_image_url && $('#edit_employee').hasClass('show')) {
                                    const $imageContainer = $('#UpdateFormEmPloyee').find('.uploaded_image');
                                    if ($imageContainer.length > 0) {
                                        // Add cache-busting to ensure fresh image
                                        let imageUrl = response.data.profile_image_url.replace(/\\/g, '/');
                                        const separator = imageUrl.includes('?') ? '&' : '?';
                                        imageUrl = `${imageUrl}${separator}t=${new Date().getTime()}&id=${response.data.id}`;
                                        const imageHtml = `<img src="${imageUrl}" class="img-fluid rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" alt="img" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'letter-avatar\\' data-name=\\'${response.data.name || 'U'}\\'>${(response.data.name || 'U').charAt(0).toUpperCase()}</div>';" />`;
                                        $imageContainer.html(imageHtml);
                                    }
                                }

                                $('#UpdateFormEmPloyee')[0].reset();
                                $('#edit_employee').modal('hide');

                                // Show success modal for update
                                showUpdateSuccessModal(response.data);

                                // Reload list to update table - use callback to ensure table is updated
                                loadEmployees(function() {
                                    // After table is loaded, force refresh the specific employee's image
                                    if (response.data && response.data.id) {
                                        refreshEmployeeImageInTable(response.data.id, response.data.profile_image_url);
                                    }
                                });

                                // Sidebar verification: If sidebar needs refreshing for new permissions,
                                // it's usually better to do it when they actually navigate.
                                // But if they click "Back to List" or close modal, we don't reload.
                                // The user asked for dynamic update and detail page access.
                            } else {
                                showErrorToast(response.message || 'Update failed.');
                            }
                        },
                        error: function(xhr) {
                            $submitBtn.prop('disabled', false).text('Save');
                            
                            // Show top-level message in toast if present
                            if (xhr.responseJSON?.message) {
                                showErrorToast(xhr.responseJSON.message);
                            }

                            if (xhr.responseJSON?.errors) {
                                Object.keys(xhr.responseJSON.errors).forEach(field => {
                                    const $field = $('#UpdateFormEmPloyee').find(`[name="${field}"]`);
                                    // Use the markInvalid function if available, or just add class
                                    $field.addClass('is-invalid');
                                    if ($field.next('.invalid-feedback').length === 0) {
                                        $field.after(`<div class="invalid-feedback d-block">${xhr.responseJSON.errors[field][0]}</div>`);
                                    }
                                });
                            } else if (!xhr.responseJSON?.message) {
                                showErrorToast('Something went wrong.');
                            }
                        }
                    });
                });
            }

            function showUpdateSuccessModal(employee) {
                const name = employee.name ?? 'Employee';
                const empId = employee.employee_id ?? employee.id ?? '????';

                $('#update_success_emp_name').text(name);
                $('#update_success_emp_id').text(`#${empId}`);

                const baseDetailUrl = "{{ route('employee.details', ['box_slug' => request()->route('box_slug')]) }}";
                $('#update_success_detail_link').attr('href', `${baseDetailUrl}?emp_id=${employee.id}`);

                $('#update_success_modal').modal('show');
            }

            // Handle "Back to List" button click - reload page
            $(document).on('click', '#back_to_list_btn', function() {
                // Reload the page after a short delay to allow modal to close
                setTimeout(function() {
                    window.location.reload();
                }, 300);
            });
        });
    </script>
    <script>
        function checkMe(selected) {
            if (selected) {
                document.getElementById("divcheck").style.display = "none";
            } else {
                document.getElementById("divcheck").style.display = "";
            }

        }

        function toggleCustomTimingFields(selected) {
            if (selected) {
                document.getElementById("divcheckedit").style.display = "none";
            } else {
                document.getElementById("divcheckedit").style.display = "";
            }
        }
    </script>
    <script src="{{ asset('js/permissions.js') }}"></script>
    <script>
        // Initialize PermissionHelper if not already loaded
        if (typeof window.PermissionHelper === 'undefined') {
            window.PermissionHelper = {
                userPermissions: [],
                isAdmin: false, // Will be set from server
                hasPermission: function(permission) {
                    // Admins have all permissions
                    if (this.isAdmin) {
                        return true;
                    }
                    // Check for "all.permissions" master permission
                    if (this.userPermissions.includes('all.permissions')) {
                        return true;
                    }
                    // Check for module-level "all" permission
                    const parts = permission.split('.');
                    if (parts.length >= 2) {
                        const moduleAllPermission = parts[0] + '.all';
                        if (this.userPermissions.includes(moduleAllPermission)) {
                            return true;
                        }
                    }
                    // Check specific permission
                    return this.userPermissions.includes(permission);
                }
            };
        }

        // Note: Admin status is loaded via loadUserPermissions() function above
    </script>

    <!-- Croppie CSS and Scripts - Only for Employee List Page -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.js"></script>

    <style>
        .croppie-container .cr-slider-wrap {
            width: 95%;
            margin: 0 6px;
            text-align: center;
            padding-top: 5px;
        }
        .cr-viewport {
            outline: none;
            box-shadow: 0;
        }
        .cr-boundary {
            width: 100%;
        }
    </style>

    <!-- Image Crop Script - External File -->
    <script src="{{ asset('assets/js/image-cropper.js') }}"></script>

    <!-- Toast Notification Container -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 11000;">
        <div id="toastMessage" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastBody">
                    <!-- Toast message will be inserted here -->
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        // Toast notification functions
        function showSuccessToast(message) {
            const $toast = $('#toastMessage');
            const $toastBody = $('#toastBody');

            // Change toast to success style
            $toast.removeClass('bg-danger bg-success bg-warning').addClass('bg-success');

            // Set success message
            $toastBody.html(`
                <div class="d-flex align-items-center">
                    <i class="ti ti-check-circle me-2 fs-5"></i>
                    <div class="flex-grow-1">
                        <strong>Success!</strong><br>
                        <small>${message}</small>
                    </div>
                </div>
            `);

            // Show toast with auto-hide
            if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                const toast = new bootstrap.Toast($toast[0], {
                    autohide: true,
                    delay: 5000
                });
                toast.show();
            } else {
                alert(message);
            }
        }

        function showErrorToast(message) {
            const $toast = $('#toastMessage');
            const $toastBody = $('#toastBody');

            // Change toast to danger style
            $toast.removeClass('bg-danger bg-success bg-warning').addClass('bg-danger');

            // Set error message
            $toastBody.html(`
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-circle me-2 fs-5"></i>
                    <div class="flex-grow-1">
                        <strong>Error!</strong><br>
                        <small>${message}</small>
                    </div>
                </div>
            `);

            // Show toast with auto-hide
            if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                const toast = new bootstrap.Toast($toast[0], {
                    autohide: true,
                    delay: 5000
                });
                toast.show();
            } else {
                alert(message);
            }
        }
        
        // Open Designation Tree Modal when Department is selected
        $(document).on('change', '#department', function(e) {
            var val = $(this).val();
            if (val && typeof window.openDesignationTree === 'function') {
                window.openDesignationTree(val, '#designation');
            }
        });

        $(document).on('change', '#departmentedit', function(e) {
            if (window.isEditingEmployee) return;
            
            var val = $(this).val();
            if (val && typeof window.openDesignationTree === 'function') {
                window.openDesignationTree(val, '#designationedit');
            }
        });
    </script>
    
    @include('frontend.employess.components.designation_tree_modal')

@endpush
@endsection

