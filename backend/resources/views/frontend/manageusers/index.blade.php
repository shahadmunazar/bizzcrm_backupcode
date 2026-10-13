@extends('layouts.main')
@section('content')
@section('title', 'Manage Users')
    <div class="page-wrapper">
        <div class="content">
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Employee </h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="index.html"><i class="ti ti-smart-home"></i></a>
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
                            <a href="employees.html" class="btn btn-icon btn-sm active bg-primary text-white me-1"><i
                                    class="ti ti-list-tree"></i></a>
                            <a href="employees-grid.html" class="btn btn-icon btn-sm"><i class="ti ti-layout-grid"></i></a>
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
                        <a href="#" data-bs-toggle="modal" data-bs-target="#add_employee"
                            class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add
                            Employee </a>
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

                </div>
                <div class="card-body p-0">
                    <div class="custom-datatable-filter table-responsive">
                        <table class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th class="no-sort">
                                        <div class="form-check form-check-md">
                                            <input class="form-check-input" type="checkbox" id="select-all" />
                                        </div>
                                    </th>
                                    <th>Name </th>
                                    <th>Emp ID </th>
                                    <th>Staff Role </th>
                                    <th>Mobile No. </th>
                                    <th>Email ID </th>
                                    <th>Joining Date </th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="employeeTableBody">
                            </tbody>
                        </table>
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
                        <h4 class="modal-title me-2">Add New Employee </h4><span>Employee ID : Emp-001 </span>
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
                                                <i class="ti ti-photo text-gray-2 fs-16"></i>
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
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Name <span class="text-danger"> * </span></label>
                                            <input type="text" class="form-control" name="name"
                                                placeholder="Enter Name" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Employee ID <span class="text-danger"> *
                                                </span></label>
                                            <input type="text" name="employee_id" class="form-control"
                                                placeholder="Employee ID" />
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
                                            <input type="text" name="phone" class="form-control"
                                                placeholder="Mobile Number" />
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Joining Date <span class="text-danger"> *
                                                </span></label>
                                            <input type="date" name="joining_date" class="form-control"
                                                placeholder="dd/mm/yyyy" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">User Role </label>
                                            <select class="select" name="staff_role">
                                                <option disabled selected>Select Staff </option>
                                                <option value="employee">Employee </option>
                                                <option value="manager">Manager </option>
                                                <option value="project-manager">Project Manager </option>
                                                <option value="ser export"> SEO Expert </option>
                                                <option value="web designer">Web Designer </option>
                                                <option value="sales executive">Sales Executive </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="department" class="form-label">Department</label>
                                                <select class="form-select select" name="department" id="department">
                                                    <option value="">Select Department</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="designation" class="form-label">Designation</label>
                                                <select class="form-select select" name="designation" id="designation">
                                                    <option value="">Select Designation</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="mb-3" id="reporting_manager_add_container">
                                                <label class="form-label">Reporting Manager</label>
                                                <select id="reporting_manager_add" name="reporting_manager_id" class="select form-control">
                                                    <option value="">Select Reporting Manager</option>
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
                                                    <input class="form-check-input me-2" type="checkbox"
                                                        role="switch" />
                                                    Enable all Module
                                                </label>
                                            </div>
                                            <div class="form-check d-flex align-items-center">
                                                <label class="form-check-label mt-0">
                                                    <input class="form-check-input" type="checkbox" checked="" />
                                                    Select All
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive border rounded">
                                    <table class="table">
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
                                <button type="submit" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#success_modal">Save </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Employee -->

    <!-- Edit Employee -->
    <div class="modal fade" id="edit_employee">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex align-items-center">
                        <h4 class="modal-title me-2">Edit Employee </h4><span>Employee ID : ___ -0024 </span>
                    </div>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="UpdateFormEmPloyee" method="POST" enctype="multipart/form-data">

                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="id" id="edit_employee_id">
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
                                                <img src="{{ asset('assets/img/users/user-13.jpg') }}" alt="img"
                                                    class="rounded-circle" id="profile_img" />
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
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Name <span class="text-danger"> * </span></label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                placeholder="Enter Name" />
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
                                            <input type="text" name="phone" id="phone" class="form-control"
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

                                    <div class="col-md-6">
                                        <label for="staff_role" class="form-label">User Role</label>
                                        <select class="form-select" name="staff_role" id="staff_role" required>
                                            <option value="" disabled selected>Select Staff</option>
                                            <option value="employee">Employee</option>
                                            <option value="manager">Manager</option>
                                            <option value="project-manager">Project Manager</option>
                                            <option value="seo-expert">SEO Expert</option>
                                            <option value="web-designer">Web Designer</option>
                                            <option value="sales-executive">Sales Executive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Department</label>
                                            <select id="departmentedit" name="department" class="select form-control">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Designation</label>
                                            <select id="designationedit" name="designation" class="select form-control">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3" id="reporting_manager_edit_container">
                                            <label class="form-label">Reporting Manager</label>
                                            <select id="reporting_manager_edit" name="reporting_manager_id" class="select form-control">
                                                <option value="">Select Reporting Manager</option>
                                            </select>
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
                                                    <input class="form-check-input me-2" type="checkbox"
                                                        role="switch" />
                                                    Enable all Module
                                                </label>
                                            </div>
                                            <div class="form-check d-flex align-items-center">
                                                <label class="form-check-label mt-0">
                                                    <input class="form-check-input" type="checkbox" checked="" />
                                                    Select All
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive border rounded">
                                    <table class="table">
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
                                <button type="submit" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#success_modal">Save </button>
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
                        <p class="mb-3">Stephan Peralt has been _____ with Client ID : <span class="text-primary">#EMP
                                -
                                0001 </span>
                        </p>
                        <div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="{{ box_route('manage.users') }}" class="btn btn-dark w-100">Back to List
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ box_route('users.details') }}" class="btn btn-primary w-100">Detail
                                        Page
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Add Client Success -->

    <!-- Delete Modal -->
    <div class="modal fade" id="delete_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
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
                        Are you sure you want to <strong id="toggle_action_text">change</strong> the employee status?
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
                let deleteEmployeeId = null;
                let toggleEmployeeId = null;
                let allEmployees = [];
                init();

                function init() {
                    loadEmployees();
                    bindFormSubmission();
                    bindExportButtons();
                    bindModalEvents();
                    bindEditButton();
                    bindStatusToggle();
                    bindEditFormSubmission();
                }
                // 1. Load employees via AJAX and update UI
                function loadEmployees() {
                    $.ajax({
                        url: '{{ box_route('fetch.employee') }}',
                        type: 'GET',
                        success: function(response) {
                            allEmployees = response.data || [];
                            updateDashboardStats(response.stats || {});
                            populateDataTable(allEmployees);
                        },
                        error: function() {
                            alert('Failed to load employees.');
                        }
                    });
                }

                function updateDashboardStats(stats) {
                    $('#total_employee_count').text(stats.total ?? 0);
                    $('#active_employee_count').text(stats.active ?? 0);
                    $('#inactive_employee_count').text(stats.inactive ?? 0);
                    $('#new_joiners_count').text(stats.new_joiners ?? 0);
                }
                // 2. Populate DataTable with employees
       function populateDataTable(employees) {
    const employeeDetailsRoute =
        '{{ route('users.details', ['box_slug' => request()->route('box_slug')]) }}';

    const dataSet = employees.map(emp => {
        const profileImage = emp.profile_image_path
            ? `<img src="/storage/${emp.profile_image_path}" class="img-fluid rounded-circle" alt="img" />`
            : `<div class="letter-avatar" data-name="${emp.name || 'U'}">${(emp.name || 'U').charAt(0).toUpperCase()}</div>`;

        return [
            // Checkbox column
            `<div class="form-check form-check-md">
                <input class="form-check-input" type="checkbox" data-employee-id="${emp.id}" />
            </div>`,

            // Name + avatar
            `<div class="d-flex align-items-center">
                <a href="${employeeDetailsRoute}?emp_id=${emp.id}" class="avatar avatar-md">
                    ${profileImage}
                </a>
                <div class="ms-2">
                    <p class="text-dark mb-0">
                        <a href="${employeeDetailsRoute}?emp_id=${emp.id}">${emp.name}</a>
                    </p>
                </div>
            </div>`,

            // Employee ID
            `<a href="${employeeDetailsRoute}?emp_id=${emp.id}">${emp.employee_id}</a>`,

            // Staff Role
            emp.staff_role ?? 'N/A',

            // Phone
            emp.phone ?? 'N/A',

            // Email
            emp.email ?? 'N/A',

            // Joining Date
            emp.joining_date ? new Date(emp.joining_date).toLocaleDateString() : 'N/A',

            // Actions
            `<div class="action-icon d-inline-flex flex-column align-items-start" style="gap: 10px;">
                <div class="d-inline-flex" style="gap: 15px;">
                    <a href="#" class="edit-employee-btn" data-bs-toggle="modal" data-bs-target="#edit_employee" data-emp-id="${emp.id}" title="Edit">
                        <i class="ti ti-edit"></i>
                    </a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#delete_modal" data-emp-id="${emp.id}" title="Delete">
                        <i class="ti ti-trash"></i>
                    </a>
                    <a href="#" class="toggle-status" data-emp-id="${emp.id}" title="${emp.status === 'Enabled' ? 'Set Inactive' : 'Set Active'}">
                        ${emp.status === 'Enabled'
                            ? '<i class="ti ti-toggle-right text-success"></i>'
                            : '<i class="ti ti-toggle-left text-danger"></i>'}
                    </a>
                </div>
      <div class="mt-2">
    ${!emp.email_verified_at
        ? `<button class="btn btn-sm btn-outline-danger resend-verification-btn" data-emp-id="${emp.user_id}" title="Resend Verification">
                <i class="fa-solid fa-envelope"></i>
           </button>`
        : ''}
</div>


            </div>`
        ];
    });

    if ($.fn.dataTable.isDataTable('.table')) {
        employeeTable.clear().rows.add(dataSet).draw();
    } else {
        employeeTable = $('.table').DataTable({
            data: dataSet,
            columns: [
                { title: '', orderable: false },
                { title: 'Name' },
                { title: 'Emp ID' },
                { title: 'Staff Role' },
                { title: 'Mobile No.' },
                { title: 'Email ID' },
                { title: 'Joining Date' },
                { title: 'Action', orderable: false }
            ],
            order: [[1, 'asc']],
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            responsive: true,

            drawCallback: function () {
                bindResendVerification();
                assignAvatarColors();
            }
        });
    }
}

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
    $('.resend-verification-btn').off('click').on('click', function () {
        const empId = $(this).data('emp-id');
        const $btn = $(this);
        if (!empId) return;

        $btn.prop('disabled', true).text('Sending...');

        $.ajax({
            url: '{{ route('users.resend.verification', ['box_slug' => request()->route('box_slug')]) }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                userid: empId
            },
            success: function (response) {
                if (response.status) {
                    alert(response.message);
                    $btn
                        .text('Message Sent')
                        .removeClass('btn-outline-danger')
                        .addClass('btn-outline-success')
                        .prop('disabled', false);
                } else {
                    alert(response.message || 'Failed to resend verification.');
                    $btn.prop('disabled', false).text('Resend Verification');
                }
            },
            error: function () {
                alert('An unexpected error occurred.');
                $btn.prop('disabled', false).text('Resend Verification');
            }
        });
    });
}


                // 3. Bind form submission for add/edit employee
                function bindFormSubmission() {
                    $('#employeeForm').on('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        $.ajax({
                            url: '{{ route('store.users', ['box_slug' => request()->route('box_slug')]) }}',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                $('#employeeForm')[0].reset();
                                $('#edit_employee').modal('hide');
                                showSuccessModal(response.data);
                                loadEmployees();
                            },
                            error: function(xhr) {
                                if (xhr.responseJSON?.errors) {
                                    console.error(xhr.responseJSON.errors);
                                    alert('Validation error. Check console.');
                                } else if (xhr.responseJSON?.error) {
                                    alert(xhr.responseJSON.error);
                                } else {
                                    alert('Something went wrong.');
                                }
                            }
                        });
                    });
                }

                function showSuccessModal(employee) {
                    const name = employee.name ?? 'Employee';
                    const empId = employee.employee_id ?? '#EMP - ????';
                    $('#success_modal h5').text(`${name} Added Successfully`);
                    $('#success_modal p').html(
                        `${name} has been successfully added with Client ID : <span class="text-primary">#${empId}</span>`
                    );
                    $('#success_modal').modal('show');
                }
                // 4. Export buttons bind
                function bindExportButtons() {
                    $('#export-excel').on('click', function(e) {
                        e.preventDefault();
                        window.location.href =
                            '{{ route('employees.export.excel', ['box_slug' => request()->route('box_slug')]) }}';
                    });
                    $('#export-pdf').on('click', function(e) {
                        e.preventDefault();
                        window.location.href =
                            '{{ route('employees.export.pdf', ['box_slug' => request()->route('box_slug')]) }}';
                    });
                }
                // 5. Bind delete modal and deletion logic
                function bindModalEvents() {
                    $(document).on('click', '[data-bs-target="#delete_modal"]', function() {
                        deleteEmployeeId = $(this).data('emp-id');
                        $('#delete_count').text(1);
                        $('#delete_modal_text').text(
                            'You want to delete this employee, this action cannot be undone.');
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
                                alert(response.message || 'Employee deleted successfully.');
                                loadEmployees();
                                deleteEmployeeId = null;
                            },
                            error: function(xhr) {
                                $('#delete_modal').modal('hide');
                                alert(xhr.responseJSON?.error || 'Failed to delete employee.');
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
                        const isCurrentlyActive = $(this).find('i').hasClass('ti-toggle-right');
                        const toggleNewStatus = isCurrentlyActive ? 'inactive' : 'active';
                        $('#toggle_action_text').text(`set this employee as ${toggleNewStatus}`);
                        $('#toggle_status_modal').modal('show');
                    });
                    $('#confirm_toggle_btn').off('click').on('click', function() {
                        if (!toggleEmployeeId) return;
                        $.ajax({
                            url: '{{ route('users.toggle', ['box_slug' => request()->route('box_slug')]) }}',
                            type: 'PUT',
                            data: {
                                employee_id: toggleEmployeeId
                            },
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function() {
                                $('#toggle_status_modal').modal('hide');
                                loadEmployees();
                                toggleEmployeeId = null;
                            },
                            error: function(xhr) {
                                $('#toggle_status_modal').modal('hide');
                                alert(xhr.responseJSON?.message || 'Failed to update status.');
                                toggleEmployeeId = null;
                            }
                        });
                    });
                }
                // 7. Bind edit button to populate the edit form
                let allTeamsByDepartment = {};
                let departmentOptions = {};
                let teamOptions = {};
                $(document).ready(function() {
                    fetchDepartmentsAndTeams(function() {
                        bindEditButton();
                        // Initialize manager lists with all users initially
                        populateReportingManagerSelect($('#reporting_manager_add'), null, null, null);
                        populateReportingManagerSelect($('#reporting_manager_edit'), null, null, null);
                    });

                    $('#department').on('change', function() {
                        const deptId = $(this).val();
                        populateTeams(deptId);
                        // Refresh reporting manager list based on new department
                        populateReportingManagerSelect($('#reporting_manager_add'), deptId || null, null, null);
                    });

                    $('#designation').on('change', function() {
                        const deptId = $('#department').val();
                        const desigId = $(this).val();
                        // Refresh reporting manager list based on department + designation hierarchy
                        populateReportingManagerSelect($('#reporting_manager_add'), deptId || null, desigId || null, null);
                    });

                    $('#departmentedit').on('change', function() {
                        const deptId = $(this).val();
                        populateEditTeams(deptId);
                        const currentSelected = $('#reporting_manager_edit').val();
                        populateReportingManagerSelect($('#reporting_manager_edit'), deptId || null, null, currentSelected);
                    });

                    $('#designationedit').on('change', function() {
                        const deptId = $('#departmentedit').val();
                        const desigId = $(this).val();
                        const currentSelected = $('#reporting_manager_edit').val();
                        populateReportingManagerSelect($('#reporting_manager_edit'), deptId || null, desigId || null, currentSelected);
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
                                teamOptions = {};
                                $('#department, #departmentedit').empty().append(
                                    '<option value="">Select Department</option>');
                                departments.forEach(dept => {
                                    departmentOptions[dept.id] = dept.name;
                                    teamOptions[dept.id] = dept.all_teams || [];
                                    $('#department, #departmentedit').append(
                                        `<option value="${dept.id}">${dept.name}</option>`
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
                function populateTeams(departmentId, selectedTeamId = '') {
                    const $designation = $('#designation');
                    $designation.empty().append('<option value="">Select Team</option>');
                    const teams = teamOptions[departmentId] || [];
                    teams.forEach(team => {
                        const selected = team.id == selectedTeamId ? 'selected' : '';
                        $designation.append(`<option value="${team.id}" ${selected}>${team.name}</option>`);
                    });
                }

                function populateEditTeams(departmentId, selectedTeamId = '') {
                    const $designation = $('#designationedit');
                    $designation.empty().append('<option value="">Select Team</option>');
                    const teams = teamOptions[departmentId] || [];
                    teams.forEach(team => {
                        const selected = team.id == selectedTeamId ? 'selected' : '';
                        $designation.append(`<option value="${team.id}" ${selected}>${team.name}</option>`);
                    });
                }
                function bindEditButton() {
                    $(document).off('click', '.edit-employee-btn').on('click', '.edit-employee-btn', function(e) {
                        e.preventDefault();
                        const empId = parseInt($(this).data('emp-id'), 10);
                        const employee = allEmployees.find(emp => emp.id === empId);
                        if (!employee) {
                            alert('Employee not found.');
                            return;
                        }
                        // Reset the form before populating
                        $('#UpdateFormEmPloyee')[0].reset();
                        $('#working_days').val([]).trigger('change');
                        // Basic Info
                        $('#edit_employee_id').val(employee.id);
                        $('#name').val(employee.name || '');
                        $('#employee_id').val(employee.employee_id || '');
                        $('#email').val(employee.email || '');
                        $('#phone').val(employee.phone || '');
                        $('#joining_date').val(employee.joining_date || '');
                        $('#staff_role').val(employee.staff_role || '');
                        // Department & Designation
                        $('#departmentedit').val(employee.department).trigger('change');
                        populateEditTeams(employee.department, employee.designation);
                        $('#designationedit').val(employee.designation).trigger('change');

                        // Populate Reporting Manager in edit modal
                        const deptId = employee.department || null;
                        const designationId = employee.designation || null;
                        const reportingManagerId = employee.reporting_manager_id || null;
                        populateReportingManagerSelect($('#reporting_manager_edit'), deptId, designationId, reportingManagerId);

                        const profileImage = employee.profile_image_path ?
                            `/storage/${employee.profile_image_path.replace(/\\/g, '/')}` :
                            'assets/img/users/user-13.jpg';
                        $('#profile_img').attr('src', profileImage);
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
                        $('#edit_employee').modal('show');
                    });

                    // Reset form on modal close
                    $('#edit_employee').on('hidden.bs.modal', function() {
                        $('#UpdateFormEmPloyee')[0].reset();
                        $('#profile_img').attr('src', '');
                        $('#edit_employee_id').val('');
                        $('#working_days').val([]).trigger('change');
                        $('#allowed_ips').val('');
                        $('#exlude_ip_restriction, #exclude_round_robin, #user_box_timing').prop('checked',
                            false);
                        $('#divcheckedit').hide();
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
                        const empId = $('#edit_employee_id').val();
                        const formData = new FormData(this);
                        formData.append('id', empId);
                        $('#UpdateFormEmPloyee input[type="checkbox"]').each(function() {
                            const name = $(this).attr('name');
                            const isChecked = $(this).is(':checked') ? '1' : '0';
                            formData.delete(name);
                            formData.append(name, isChecked);
                        });
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
                                if (response.status) {
                                    $('#UpdateFormEmPloyee')[0].reset();
                                    $('#edit_employee').modal('hide');
                                    // toastr.success(response.message); // Show success message
                                    loadEmployees(); // Reload list
                                } else {
                                    toastr.error(response.message || 'Update failed.');
                                }
                            },
                            error: function(xhr) {
                                if (xhr.responseJSON?.errors) {
                                    console.error(xhr.responseJSON.errors);
                                    toastr.error('Validation error. Check console.');
                                } else {
                                    toastr.error('Something went wrong.');
                                }
                            }
                        });
                    });
                }

                // Hierarchical Reporting Manager Logic
                function populateReportingManagerSelect($select, filterDept = null, filterDesignation = null, selectedId = null) {
                    $select.prop('disabled', true).empty().append('<option value="">Loading managers...</option>');

                    const currentEditingEmpId = $('#edit_employee_id').val();

                    $.ajax({
                        url: '{{ route('employee.get.box.users', ['box_slug' => request()->route('box_slug')]) }}',
                        method: 'GET',
                        data: {
                            department: filterDept,
                            superior_to_designation: filterDesignation,
                            exclude_id: currentEditingEmpId || null,
                            include_id: selectedId || null
                        },
                        success: function(response) {
                            $select.prop('disabled', false);
                            if (response.status && Array.isArray(response.data)) {
                                // Find selected designation depth for hierarchy calculation
                                let selectedDepth = null;
                                if (filterDesignation && teamOptions[filterDept]) {
                                    const found = teamOptions[filterDept].find(t => String(t.id) === String(filterDesignation));
                                    if (found) {
                                        selectedDepth = (found.depth !== undefined && found.depth !== null) ? parseInt(found.depth) : null;
                                    }
                                }
                                
                                renderReportingManagerOptions($select, response.data, selectedId, filterDesignation, selectedDepth);

                                // Auto-select logic for ADD form (when no selectedId is provided)
                                if (!selectedId && response.data.length > 0 && selectedDepth !== null) {
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
                            console.error('Error fetching superior users:', error);
                            $select.prop('disabled', false).empty().append('<option value="">Error loading managers</option>');
                        }
                    });
                }

                function renderReportingManagerOptions($select, users, selectedId = null, activeFilterDesignation = null, selectedDepth = null) {
                    $select.empty().append('<option value="">Select Reporting Manager</option>');
                    
                    let hierarchyRec = [];
                    let others = [];
                    const currentEditingEmpId = $('#edit_employee_id').val();

                    // Sort: Admins first, then by depth (closest to selectedDepth-1)
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
                        if (currentEditingEmpId && String(emp.id) === String(currentEditingEmpId)) return;
                        
                        const isCurrentSelected = selectedId && String(emp.id) === String(selectedId);
                        let reason = "";
                        let highlightType = ""; 

                        if (activeFilterDesignation) {
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
                            .attr('selected', selectedId && String(emp.id) === String(selectedId))
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
                        const label = hierarchyRec.length > 0 ? "Other Employees" : "All Employees";
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

                function reportingManagerTemplateResult(data) {
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
                                    '<div>' +
                                        '<div style="font-weight: 600; color: #333;">' + data.text.split(' [')[0] + '</div>' +
                                        '<div style="font-size: 0.75rem; color: #666;">' + ($el.data('designation-name') || '') + '</div>' +
                                    '</div>' +
                                    '<span class="badge ' + badgeClass + '" style="font-size: 0.65rem;">' + badgeText + '</span>' +
                                '</div>');
                    }
                    return data.text;
                }

                function reportingManagerTemplateSelection(data) {
                    if (!data.id) return data.text;
                    return data.text.split(' (')[0]; 
                }
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
    @endpush
@endsection
