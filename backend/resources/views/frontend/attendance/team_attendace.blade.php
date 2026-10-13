@extends('layouts.main')
@section('content')
@section('title', 'Team Attendance')

<style>
    .time-badge {
        padding: 3px 6px;
        background: #eef2f7;
        border-radius: 4px;
        font-weight: 500;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
    }

    .status-enabled {
        background-color: #d4edda;
        color: #155724;
    }

    .status-disabled {
        background-color: #f8d7da;
        color: #721c24;
    }

    .day-badge {
        margin-right: 5px;
        padding: 4px 8px;
        background-color: #e9ecef;
        border-radius: 4px;
        font-size: 13px;
    }
</style>
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Team Attendance </h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="index.html"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            Employee
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Team Attendance </li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                <div class="me-2 mb-2">
                    <div class="d-flex align-items-center border bg-white rounded p-1 me-2 icon-list">
                        <a href="{{ box_route('employee.attendace') }}" class="btn btn-icon btn-sm  me-1"><i
                                class="ti ti-brand-days-counter"></i></a>
                        <a href="{{ box_route('employee.attendance.calendar') }}" class="btn btn-icon btn-sm active bg-primary text-white"><i
                                class="ti ti-calendar-event"></i></a>
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
                                <a href="javascript:void(0);" id="export-attendance-pdf" class="dropdown-item rounded-1"><i
                                        class="ti ti-file-type-pdf me-1"></i>Export as PDF </a>
                            </li>
                            <li>
    <a href="javascript:void(0);" id="export-attendance-btn" class="dropdown-item rounded-1">
        <i class="ti ti-file-type-xls me-1"></i> Export as Excel
    </a>
</li>

                        </ul>
                    </div>
                </div>
                {{-- <div class="mb-2">
                    <a href="#" class="btn btn-primary d-flex align-items-center"
                        data-bs-target="#attendance_report" data-bs-toggle="modal"><i
                            class="ti ti-file-analytics me-2"></i>Report </a>
                </div> --}}
                <div class="ms-2 head-icons">
                    <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-original-title="Collapse" id="collapse-header">
                        <i class="ti ti-chevrons-up"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- /Breadcrumb -->

        {{-- <div class="card border-0">
            <div class="card-body">
                <div class="row align-items-center mb-4">
                    <div class="col-md-5">
                        <div class="mb-3 mb-md-0">
                            <h4 class="mb-1">Attendance Details Today </h4>
                            <p>Data from the <b><span id="TotalEmployeHe"></span></b> of employees </p>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="d-flex align-items-center justify-content-md-end">
                            <h6>Total Absentees today</h6>
                            <div class="avatar-list-stacked avatar-group-sm ms-4" id="absentAvatarsContainer">
                                <!-- Avatars will be appended here dynamically -->
                                <a class="avatar bg-primary avatar-rounded text-fixed-white fs-12"
                                    href="#standard-modal" id="loadAbsentEmployees" data-bs-toggle="modal">
                                    +0
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="border rounded">
                    <div class="row gx-0">
                        <div class="col-md col-sm-4 border-end">
                            <div class="p-3">
                                <span class="fw-medium mb-1 d-block">Present </span>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5>250 </h5>
                                    <span class="badge badge-success d-inline-flex align-items-center">
                                        <i class="ti ti-arrow-wave-right-down me-1"></i>
                                        +1%
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md col-sm-4 border-end">
                            <div class="p-3">
                                <span class="fw-medium mb-1 d-block">Late Login </span>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5>45 </h5>
                                    <span class="badge badge-danger d-inline-flex align-items-center">
                                        <i class="ti ti-arrow-wave-right-down me-1"></i>
                                        -1%
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md col-sm-4 border-end">
                            <div class="p-3">
                                <span class="fw-medium mb-1 d-block">Uninformed </span>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5>15 </h5>
                                    <span class="badge badge-danger d-inline-flex align-items-center">
                                        <i class="ti ti-arrow-wave-right-down me-1"></i>
                                        -12%
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md col-sm-4 border-end">
                            <div class="p-3">
                                <span class="fw-medium mb-1 d-block">Permisson </span>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5>03 </h5>
                                    <span class="badge badge-success d-inline-flex align-items-center">
                                        <i class="ti ti-arrow-wave-right-down me-1"></i>
                                        +1%
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md col-sm-4">
                            <div class="p-3">
                                <span class="fw-medium mb-1 d-block">Absent </span>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5>12 </h5>
                                    <span class="badge badge-danger d-inline-flex align-items-center">
                                        <i class="ti ti-arrow-wave-right-down me-1"></i>
                                        -19%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="card border-0">
            <div class="card-body">
                <div class="row align-items-center mb-4">
                    <div class="col-md-5">
                        <div class="mb-3 mb-md-0">
                            <h4 class="mb-1">Attendance Details Today </h4>
                            <p>Data from the <b><span id="TotalEmployeHe"></span></b> of employees </p>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="d-flex align-items-center justify-content-md-end">
                            <h6>Total Absentees today</h6>
                            <div class="avatar-list-stacked avatar-group-sm ms-4" id="absentAvatarsContainer">
                                <!-- Avatars will be appended here dynamically -->
                                <a class="avatar bg-primary avatar-rounded text-fixed-white fs-12"
                                    href="#standard-modal" id="loadAbsentEmployees" data-bs-toggle="modal">
                                    +0
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border rounded">
                    <div class="row gx-0">
                        <!-- Present -->
                        <div class="col-md col-sm-4 border-end">
                            <div class="p-3">
                                <span class="fw-medium mb-1 d-block">Present</span>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 id="stat-present">0</h5>
                                    <span id="badge-present"
                                        class="badge badge-success d-inline-flex align-items-center">
                                        <i class="ti ti-arrow-wave-right-down me-1"></i>
                                        +0%
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Late Login -->
                        <div class="col-md col-sm-4 border-end">
                            <div class="p-3">
                                <span class="fw-medium mb-1 d-block">Late Login</span>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 id="stat-late_login">0</h5>
                                    <span id="badge-late_login"
                                        class="badge badge-danger d-inline-flex align-items-center">
                                        <i class="ti ti-arrow-wave-right-down me-1"></i>
                                        +0%
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Uninformed -->
                        <div class="col-md col-sm-4 border-end">
                            <div class="p-3">
                                <span class="fw-medium mb-1 d-block">Uninformed</span>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 id="stat-unapproved_leaves">0</h5>
                                    <span id="badge-unapproved_leaves"
                                        class="badge badge-danger d-inline-flex align-items-center">
                                        <i class="ti ti-arrow-wave-right-down me-1"></i>
                                        +0%
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Permission -->
                        <div class="col-md col-sm-4 border-end">
                            <div class="p-3">
                                <span class="fw-medium mb-1 d-block">Permisson</span>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 id="stat-approved_leaves">0</h5>
                                    <span id="badge-approved_leaves"
                                        class="badge badge-success d-inline-flex align-items-center">
                                        <i class="ti ti-arrow-wave-right-down me-1"></i>
                                        +0%
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Absent -->
                        <div class="col-md col-sm-4">
                            <div class="p-3">
                                <span class="fw-medium mb-1 d-block">Absent</span>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 id="stat-absent">0</h5>
                                    <span id="badge-absent" class="badge badge-danger d-inline-flex align-items-center">
                                        <i class="ti ti-arrow-wave-right-down me-1"></i>
                                        +0%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                <h5>Admin Attendance </h5>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="me-3">
                        <button type="button" class="btn btn-white d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                            <i class="ti ti-columns me-1"></i>Manage Columns
                        </button>
                    </div>

                    <!-- Date Range -->
                    <div class="me-3 d-flex align-items-center">
                        <div class="input-icon-end position-relative">
                            <input type="text" id="dateRange" class="form-control date-range bookingrange"
                                placeholder="dd/mm/yyyy - dd/mm/yyyy" />
                            <span class="input-icon-addon">
                                <i class="ti ti-chevron-down"></i>
                            </span>
                        </div>
                        <button type="button" id="clearDateRange" class="btn btn-outline-secondary btn-icon btn-sm ms-2 d-none clear-filter"
                            data-filter="date_range" title="Clear Date Range Filter">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <!-- Department Dropdown Only -->
                    <div class="dropdown me-3">
                        <button id="departmentBtn"
                            class="btn btn-white dropdown-toggle d-inline-flex align-items-center" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Department
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-3" id="departmentDropdown">
                            <!-- Department list will be injected dynamically here -->
                            <li><span class="dropdown-item text-muted">Loading...</span></li>
                        </ul>

                    </div>


                    <!-- Status Dropdown -->
                    <div class="dropdown me-3">
                        <button id="statusBtn" class="btn btn-white dropdown-toggle d-inline-flex align-items-center"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Select Status
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-3">
                            <li><a class="dropdown-item status-option" data-value="present"
                                    href="javascript:void(0);">Present</a></li>
                            <li><a class="dropdown-item status-option" data-value="absent"
                                    href="javascript:void(0);">Absent</a></li>
                            <li><a class="dropdown-item status-option" data-value="half_day"
                                    href="javascript:void(0);">Half Day</a></li>
                            <li><a class="dropdown-item status-option" data-value="short_leave"
                                    href="javascript:void(0);">Short Leave</a></li>
                            <li><a class="dropdown-item status-option" data-value="Punch Out Miss"
                                    href="javascript:void(0);">Punch Out Miss</a></li>
                            <li><a class="dropdown-item status-option" data-value="comp_off"
                                    href="javascript:void(0);">Comp Off</a></li>
                            <li><a class="dropdown-item clear-filter" data-filter="status"
                                    href="javascript:void(0);">Clear Status</a></li>
                        </ul>
                    </div>
                    <div class="dropdown me-3">
    <button id="correctionBtn" class="btn btn-white dropdown-toggle d-inline-flex align-items-center"
        type="button" data-bs-toggle="dropdown" aria-expanded="false">
        Correction Status
    </button>
    <ul class="dropdown-menu dropdown-menu-end p-3">
        <li><a class="dropdown-item status-correction" href="javascript:void(0);">Approved</a></li>
        <li><a class="dropdown-item status-correction" href="javascript:void(0);">Pending</a></li>
        <li><a class="dropdown-item status-correction" href="javascript:void(0);">Rejected</a></li>
        <li>
            <a class="dropdown-item clear-filter" data-filter="correction_status" href="javascript:void(0);">
                Clear Status
            </a>
        </li>
    </ul>
</div>

                    <!-- Sort Dropdown -->
                </div>

            </div>
            <div class="card-body p-0">
                <div class="custom-datatable-filter table-responsive">
                    <table class="table datatable" id="attendace-admin">
                        <thead class="thead-light">
                            <tr>
                                <th class="no-sort">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox" id="select-all" />
                                    </div>
                                </th>
                                <th>Sr.No</th>
                                <th>Employee </th>
                                <th>Designation</th>
                                <th>Department</th>
                                <th>Working Mode</th>
                                <th>Shift</th>
                                <th>Attendace date</th>
                                <th>Status </th>
                                <th>Marked Via</th>
                                <th>Check In </th>
                                <th>Check Out </th>
                                <th>Break </th>
                                <th>Late </th>
                                <th>Overtime</th>
                                <th>Working hour</th>
                                <th>Production Hours </th>
                                <th>Machine</th>
                                <th>Attendace Correction</th>
                                {{-- <th></th> --}}
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    

</div>
<!-- /Page Wrapper -->

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





<!-- Export Confirmation Modal -->
<div class="modal fade" id="exportConfirmModal" tabindex="-1" aria-labelledby="exportConfirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="exportConfirmLabel">Export Attendance PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <p>Please confirm the month for which you want to export the attendance report.</p>

                <div class="mb-3">
                    <label class="form-label">Select Month</label>
                    <input type="month" class="form-control" id="creditMonth" name="month" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmExportBtn" class="btn btn-primary">Export PDF</button>
            </div>
        </div>
    </div>
</div>


<!-- Total Absenties Today List -->
<div id="standard-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Total Absenties Today</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table text-nowrap table-hover">

                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>












<!-- Edit Attendance -->
<div class="modal fade" id="edit_attendance">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Attendance</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="attendanceForm" method="POST">
                <!-- Hidden Fields -->
                <input type="hidden" name="employee_id" />
                <input type="hidden" name="attendance_id" />
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <div class="input-icon position-relative w-100 me-2">
                                    <input type="date" name="attendance_date" class="form-control ps-3"
                                        id="attendance_date_picker" />
                                    <span class="input-icon-addon">
                                        <i class="ti ti-calendar"></i>
                                    </span>
                                </div>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Check In</label>
                                <div class="input-icon position-relative w-100">
                                    <input type="text" name="check_in" class="form-control timepicker ps-3" />
                                    <span class="input-icon-addon">
                                        <i class="ti ti-clock-hour-3"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Check Out</label>
                                <div class="input-icon position-relative w-100">
                                    <input type="text" name="check_out" class="form-control timepicker ps-3" />
                                    <span class="input-icon-addon">
                                        <i class="ti ti-clock-hour-3"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Break</label>
                                <input type="text" name="break_duration" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Late</label>
                                <input type="text" name="late_duration" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Production Hours</label>
                                <div class="input-icon position-relative w-100">
                                    <input type="text" name="production_hours"
                                        class="form-control timepicker ps-3" />
                                    <span class="input-icon-addon">
                                        <i class="ti ti-clock-hour-3"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="select form-control" name="status">
                                    <option value="">Select</option>
                                    <option value="Present">Present</option>
                                    <option value="Absent">Absent</option>
                                    <option value="Half Day">Half Day</option>
                                    <option value="Short Leave">Short Leave</option>
                                    <option value="Punch Out Miss">Punch Out Miss</option>
                                    <option value="Comp Off">Comp Off</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="saved_changes" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- /Edit Attendance -->

<!-- Attendance Report -->
{{-- <div class="modal fade" id="attendance_report">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Attendance </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card shadow-none bg-transparent-light">
                    <div class="card-body pb-1">
                        <div class="row align-items-center">
                            <div class="col-lg-4">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="avatar avatar-sm avatar-rounded flex-shrink-0 me-2">
                                        <img src="assets/img/profiles/avatar-02.jpg" alt="Img" />
                                    </span>
                                    <div>
                                        <h6 class="fw-medium">Anthony Lewis </h6>
                                        <span>UI/UX Team </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="mb-3 text-sm-end">
                                            <span>Date </span>
                                            <p class="text-gray-9 fw-medium">15 Apr 2025 </p>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3 text-sm-end">
                                            <span>Punch in at </span>
                                            <p class="text-gray-9 fw-medium">09:00 AM </p>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3 text-sm-end">
                                            <span>Punch out at </span>
                                            <p class="text-gray-9 fw-medium">06:45 PM </p>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3 text-sm-end">
                                            <span>Status </span>
                                            <p class="text-gray-9 fw-medium">Present </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-none border mb-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3">
                                <div class="mb-4">
                                    <p class="d-flex align-items-center mb-1"><i
                                            class="ti ti-point-filled text-dark-transparent me-1"></i>Total
                                        Working hours </p>
                                    <h3>12h 36m </h3>
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="mb-4">
                                    <p class="d-flex align-items-center mb-1"><i
                                            class="ti ti-point-filled text-success me-1"></i>Productive Hours
                                    </p>
                                    <h3>08h 36m </h3>
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="mb-4">
                                    <p class="d-flex align-items-center mb-1"><i
                                            class="ti ti-point-filled text-warning me-1"></i>Break hours </p>
                                    <h3>22m 15s </h3>
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="mb-4">
                                    <p class="d-flex align-items-center mb-1"><i
                                            class="ti ti-point-filled text-info me-1"></i>Overtime </p>
                                    <h3>02h 15m </h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <div class="progress bg-transparent-dark mb-3" style="height: 24px;">
                                    <div class="progress-bar bg-success rounded me-2" role="progressbar"
                                        style="width: 18%;"></div>
                                    <div class="progress-bar bg-warning rounded me-2" role="progressbar"
                                        style="width: 5%;"></div>
                                    <div class="progress-bar bg-success rounded me-2" role="progressbar"
                                        style="width: 28%;"></div>
                                    <div class="progress-bar bg-warning rounded me-2" role="progressbar"
                                        style="width: 17%;"></div>
                                    <div class="progress-bar bg-success rounded me-2" role="progressbar"
                                        style="width: 22%;"></div>
                                    <div class="progress-bar bg-warning rounded me-2" role="progressbar"
                                        style="width: 5%;"></div>
                                    <div class="progress-bar bg-info rounded me-2" role="progressbar"
                                        style="width: 3%;"></div>
                                    <div class="progress-bar bg-info rounded" role="progressbar" style="width: 2%;">
                                    </div>
                                </div>

                            </div>
                            <div class="co-md-12">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-10">06:00 </span>
                                    <span class="fs-10">07:00 </span>
                                    <span class="fs-10">08:00 </span>
                                    <span class="fs-10">09:00 </span>
                                    <span class="fs-10">10:00 </span>
                                    <span class="fs-10">11:00 </span>
                                    <span class="fs-10">12:00 </span>
                                    <span class="fs-10">01:00 </span>
                                    <span class="fs-10">02:00 </span>
                                    <span class="fs-10">03:00 </span>
                                    <span class="fs-10">04:00 </span>
                                    <span class="fs-10">05:00 </span>
                                    <span class="fs-10">06:00 </span>
                                    <span class="fs-10">07:00 </span>
                                    <span class="fs-10">08:00 </span>
                                    <span class="fs-10">09:00 </span>
                                    <span class="fs-10">10:00 </span>
                                    <span class="fs-10">11:00 </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}
<div class="modal fade" id="attendance_report">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Attendance</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card shadow-none bg-transparent-light">
                    <div class="card-body pb-1">
                        <div class="row align-items-center">
                            <div class="col-lg-4">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="avatar avatar-sm avatar-rounded flex-shrink-0 me-2"
                                        id="modalProfileImage"></span>
                                    <div>
                                        <h6 class="fw-medium" id="modalUserName">-</h6>
                                        <span id="modalDesignation">-</span>
                                        /<span id="designation_name">-</span>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="mb-3 text-sm-end">
                                            <span>Date</span>
                                            <p class="text-gray-9 fw-medium" id="modalDate">--</p>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3 text-sm-end">
                                            <span>Punch in at</span>
                                            <p class="text-gray-9 fw-medium" id="modalCheckIn">--:--</p>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3 text-sm-end">
                                            <span>Punch out at</span>
                                            <p class="text-gray-9 fw-medium" id="modalCheckOut">--:--</p>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3 text-sm-end">
                                            <span>Status</span>
                                            <p class="text-gray-9 fw-medium" id="modalStatus">--</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-none border mb-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 text-center">
                                <div class="mb-4">
                                    <p class="d-flex align-items-center mb-1"><i
                                            class="ti ti-point-filled text-dark-transparent me-1"></i>Total Working
                                        Hours</p>
                                    <h3 id="totalWorkingHours">--h --m</h3>
                                </div>
                            </div>
                            <div class="col-xl-3 text-center">
                                <div class="mb-4">
                                    <p class="d-flex align-items-center mb-1"><i
                                            class="ti ti-point-filled text-success me-1"></i>Productive Hours</p>
                                    <h3 id="productiveHours">--h --m</h3>
                                </div>
                            </div>
                            <div class="col-xl-3 text-center">
                                <div class="mb-4">
                                    <p class="d-flex align-items-center mb-1"><i
                                            class="ti ti-point-filled text-warning me-1"></i>Break Hours</p>
                                    <h3 id="breakHours">--h --m</h3>
                                </div>
                            </div>
                            <div class="col-xl-3 text-center">
                                <div class="mb-4">
                                    <p class="d-flex align-items-center mb-1"><i
                                            class="ti ti-point-filled text-info me-1"></i>Overtime</p>
                                    <h3 id="overtimeHours">--h --m</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <div id="attendanceProgressBar" class="progress bg-transparent-dark mb-3"
                                    style="height: 24px;">
                                    <!-- Progress segments will be appended here -->
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div id="timeLabels" class="d-flex align-items-center justify-content-between fs-10">
                                    <!-- Hour labels will be appended here -->
                                </div>
                            </div>
                        </div> <!-- end row -->
                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div> <!-- end modal-body -->
        </div> <!-- end modal-content -->
    </div> <!-- end modal-dialog -->
</div> <!-- end modal -->
<div class="modal fade" id="shiftInfoModal" tabindex="-1" aria-labelledby="shiftInfoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shiftInfoModalLabel">
                    <i class="bi bi-clock-history"></i> Shift Information
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4">
                    <!-- Basic Information -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header fw-semibold">
                                <i class="bi bi-info-circle me-1"></i> Basic Information
                            </div>
                            <div class="card-body small">
                                <p><strong>Shift ID:</strong> <span id="shiftId">-</span></p>
                                <p><strong>Code:</strong> <span id="shiftName">-</span></p>
                                <p><strong>Type:</strong> <span id="shiftType">-</span></p>
                                <p><strong>Status:</strong> <span class="badge" id="shiftStatus">-</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Shift Time -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header fw-semibold">
                                <i class="bi bi-clock me-1"></i> Shift Time
                            </div>
                            <div class="card-body small">
                                <p><strong>In Time:</strong> <span id="inTime">-</span></p>
                                <p><strong>Out Time:</strong> <span id="outTime">-</span></p>
                                <p><strong>Total Hours:</strong> <span id="totalHours">-</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Rest Time -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-egg-fried me-1"></i> Rest Time</span>
                                <span class="badge" id="restStatus">-</span>
                            </div>
                            <div class="card-body small">
                                <p><strong>Lunch Out:</strong> <span id="lunchOut">-</span></p>
                                <p><strong>Lunch In:</strong> <span id="lunchIn">-</span></p>
                                <p><strong>Duration:</strong> <span id="lunchDuration">-</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Short Breaks -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-cup-hot me-1"></i> Short Breaks</span>
                                <span class="badge" id="shortBreakStatus">-</span>
                            </div>
                            <div class="card-body small">
                                <p><strong>Break 1:</strong> <span id="break1">-</span></p>
                                <p><strong>Break 2:</strong> <span id="break2">-</span></p>
                                <p><strong>Total Break:</strong> <span id="totalBreak">-</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Half Day -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-calendar-day me-1"></i> Half Day</span>
                                <span class="badge" id="halfDayStatus">-</span>
                            </div>
                            <div class="card-body small">
                                <p><strong>Start Time:</strong> <span id="halfStartTime">-</span></p>
                                <p><strong>End Time:</strong> <span id="halfEndTime">-</span></p>
                                <p><strong>Duration:</strong> <span id="halfDuration">-</span></p>
                                <p><strong>Half Day From:</strong> <span id="halfDayFrom">-</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Deduction Rules -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header fw-semibold">
                                <i class="bi bi-exclamation-circle me-1"></i> Deduction Rules
                            </div>
                            <div class="card-body small">
                                <p>
                                    <strong>Absent If &lt;:</strong> <span id="absentBelow">-</span><br>
                                    <small class="text-muted">Deduction: <span class="text-danger fw-bold"
                                            id="absentDeduction">-</span></small>
                                </p>
                                <p>
                                    <strong>Half Day If &lt;:</strong> <span id="halfDayBelow">-</span><br>
                                    <small class="text-muted">Deduction: <span class="text-warning fw-bold"
                                            id="halfDayDeduction">-</span></small>
                                </p>
                                <p>
                                    <strong>Short Leave If &lt;:</strong> <span id="shortLeaveBelow">-</span><br>
                                    <small class="text-muted">Deduction: <span class="text-primary fw-bold"
                                            id="shortLeaveDeduction">-</span></small>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Overtime -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-alarm me-1"></i> Overtime</span>
                                <span class="badge" id="overtimeStatus">-</span>
                            </div>
                            <div class="card-body small">
                                <p><strong>Max Allowed:</strong> <span id="overtimeAllowed">-</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Weekly Off -->
                    <div class="col-md-8">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header fw-semibold">
                                <i class="bi bi-calendar-week me-1"></i> Weekly Off
                            </div>
                            <div class="card-body small">
                                <p>
                                    <strong>Off Days:</strong>
                                    <span id="weeklyOffDays">-</span>
                                </p>
                                <p>
                                    <strong>Sat Offs:</strong>
                                    <span id="satOffs">-</span>
                                </p>
                                <p>
                                    <strong>Sat Half Days:</strong>
                                    <span id="satHalfDays">-</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="correctionsModal" tabindex="-1" aria-labelledby="correctionsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable">
        <form id="correctionForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="correctionsModalLabel">
                    <i class="bi bi-pencil-square"></i> Attendance Corrections
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div id="correctionsContainer" class="row g-4">
                    <div class="col-12">
                        <div class="card p-3">
                            <h6 id="correctionTypeTitle">Correction Request</h6>
                            <input type="hidden" id="correctionId" name="correctionId" />
                            <input type="hidden" id="correctionType" name="correctionType" />

                            <div class="mb-2">
                                <label><strong>Employee Name:</strong></label>
                                <span id="employeeName"></span>
                            </div>
                            <div class="mb-2">
                                <label><strong>Employee ID:</strong></label>
                                <span id="employeeId"></span>
                            </div>

                            <!-- Attendance Correction Fields -->
                            <div class="mb-2">
                                <label><strong>Attendance Date:</strong></label>
                                <span id="requestedDate"></span>
                            </div>
                            <div class="mb-2">
                                <label><strong>Requested Date & Time:</strong></label>
                                <span id="createdDate"></span>
                            </div>
                            <div id="attendanceCorrectionFields">
                            <div class="mb-2">
                                <label><strong>Check-in Status:</strong></label>
                                <span id="checkInStatus"></span> / <span id="checkInTime"></span>
                            </div>

                            <div class="mb-2">
                                <label><strong>Check-out Status:</strong></label>
                                <span id="checkOutStatus"></span> / <span id="checkOutTime"></span>
                                </div>
                            </div>

                            <!-- Break Correction Fields -->
                            <div id="breakCorrectionFields" style="display: none;">
                                <div class="mb-2">
                                    <label><strong>Break Start:</strong></label>
                                    <span id="breakStartTime"></span>
                                </div>

                                <div class="mb-2">
                                    <label><strong>Break End:</strong></label>
                                    <span id="breakEndTime"></span>
                                </div>

                                <div class="mb-2">
                                    <label><strong>Requested Break Start:</strong></label>
                                    <span id="requestedBreakStart"></span>
                                </div>

                                <div class="mb-2">
                                    <label><strong>Requested Break End:</strong></label>
                                    <span id="requestedBreakEnd"></span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="reason1" class="form-label"><strong>Reason:</strong></label>
                                <textarea id="reason1" name="reason1" class="form-control" rows="2" placeholder="Enter reason..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="status1" class="form-label"><strong>Status:</strong></label>
                                <select id="status1" name="status1" class="form-select" required>
                                    <option value="" disabled selected>Select approval status</option>
                                    <option value="rejected">Reject</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approve</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>

<!-- Breaks Analysis Modal -->
<div class="modal fade" id="breaksModal" tabindex="-1" aria-labelledby="breaksModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="breaksModalLabel">
                    <i class="ti ti-clock-pause me-2"></i>Breaks Analysis
                </h5>
                <button type="button" class="btn-close" id="breaksModalClose" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="breaksLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading breaks data...</p>
                </div>
                <div id="breaksContent" style="display: none;">
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-muted mb-0">Total Breaks: <span id="totalBreaksCount" class="badge bg-primary">0</span></h6>
                        <span id="activeBreakIndicator" class="badge bg-warning text-dark" style="display: none;">
                            <i class="ti ti-clock-play me-1"></i>On Break
                        </span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Break Start</th>
                                    <th>Break End</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="breaksTableBody">
                                <!-- Breaks will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="noBreaksMessage" class="alert alert-info" style="display: none;">
                        <i class="ti ti-info-circle me-2"></i>No breaks found for this attendance.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="breaksModalCloseBtn">Close</button>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    $(document).ready(function() {
    let cachedAbsentees = null;
    const urlParams = new URLSearchParams(window.location.search);
    let filters = {
        from_date: urlParams.get('from_date') || '',
        to_date: urlParams.get('to_date') || '',
        department: urlParams.get('department') || '',
        status: urlParams.get('status') || '',
        status_correction: urlParams.get('status_correction') || '',
        sort_by: urlParams.get('sort_by') || '',
        scope: 'team',
    };

    // Update UI based on URL parameters
    if (filters.status_correction) {
        $('#correctionBtn').text(filters.status_correction);
    }
    if (filters.status) {
        // Need to find the label for the status value, but for now just show the value
        $('#statusBtn').text(filters.status.charAt(0).toUpperCase() + filters.status.slice(1));
    }
    if (filters.from_date && filters.to_date) {
        $('#dateRange').val(`${filters.from_date} - ${filters.to_date}`);
        $('#clearDateRange').removeClass('d-none');
    }


    // Initialize
    loadDepartmentsDropdown();
    loadAbsentAvatars();
    // fetchEmployees();

    /** ===============================
     * 1. Load Departments Dropdown
     * =============================== */
    function loadDepartmentsDropdown() {
        $.ajax({
            url: '{{ route('get.attendance.department', ['box_slug' => request()->route('box_slug')]) }}',
            method: 'GET',
            data: { scope: 'team' },
            success: function(response) {
                const $dropdown = $('#departmentDropdown').empty();

                if (response.status === 200 && response.data.length > 0) {
                    response.data.forEach(dept => {
                        $dropdown.append(`
                            <li>
                                <a class="dropdown-item department-option" href="javascript:void(0);" data-dept-id="${dept.id}">
                                    ${dept.name}
                                </a>
                            </li>
                        `);
                    });

                    // Append "Clear Department" button just once
                    $dropdown.append(`
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item clear-filter text-danger" data-filter="department" href="javascript:void(0);">
                                Clear Department
                            </a>
                        </li>
                    `);
                } else {
                    $dropdown.append(
                        '<li><span class="dropdown-item text-muted">No departments found</span></li>'
                    );
                }
            },
            error: function(xhr) {
                $('#departmentDropdown').html(
                    '<li><span class="dropdown-item text-danger">Failed to load</span></li>'
                );
                console.error("Error loading departments:", xhr.responseText);
            }
        });
    }

    /** ===============================
     * 2. Load Absent Employees
     * =============================== */
    function loadAbsentAvatars() {
        if (cachedAbsentees) {
            renderAbsentAvatars(cachedAbsentees);
            return;
        }

        $.ajax({
            url: '{{ route('today.absenties', ['box_slug' => request()->route('box_slug')]) }}',
            method: 'GET',
            data: { scope: 'team' },
            success: function(response) {
                if (response.status === 200) {
                    cachedAbsentees = response.data;
                    renderAbsentAvatars(cachedAbsentees);
                }
            },
            error: function(xhr) {
                $('#standard-modal tbody').html(
                    '<tr><td class="text-danger text-center">Failed to load data.</td></tr>'
                );
                console.error("Error fetching absentees:", xhr.responseText);
            }
        });
    }

    function renderAbsentAvatars(data) {
        const avatarsContainer = $('#absentAvatarsContainer').empty();
        const tbody = $('#standard-modal tbody').empty();
        const maxAvatars = 4;

        const visible = data.slice(0, maxAvatars);
        visible.forEach(emp => {
            const name = emp?.user?.name || 'N/A';
            const image = emp.profile_image_url;
            const firstLetter = name.charAt(0).toUpperCase();
            const avatarHTML = image ?
                `<img src="${image}" alt="${name}" class="border border-white rounded-circle" width="40" height="40" />` :
                `<div class="letter-avatar avatar avatar-md rounded-circle d-flex align-items-center justify-content-center"
                     data-name="${name}" style="width: 40px; height: 40px; color: white; font-weight: bold;">
                     ${firstLetter}
                </div>`;

            avatarsContainer.append(`
                <a href="#standard-modal" data-bs-toggle="modal" class="avatar avatar-rounded me-1">${avatarHTML}</a>
            `);
        });

        const remaining = data.length - visible.length;
        if (remaining > 0) {
            avatarsContainer.append(`
                <a class="avatar bg-primary avatar-rounded text-fixed-white fs-12" href="#standard-modal" id="loadAbsentEmployees" data-bs-toggle="modal">
                    +${remaining}
                </a>
            `);
        }

        if (data.length === 0) {
            tbody.append('<tr><td class="text-center text-muted">No absentees found today.</td></tr>');
        } else {
            data.forEach(emp => {
                const name = emp?.user?.name || 'N/A';
                const image = emp.profile_image_url;
                const designation = emp?.department_relation?.name || 'N/A';
                const firstLetter = name.charAt(0).toUpperCase();

                const avatarHTML = image ?
                    `<img src="${image}" alt="${name}" class="rounded-circle" width="40" height="40">` :
                    `<div class="letter-avatar avatar avatar-sm rounded-circle d-flex align-items-center justify-content-center"
                         data-name="${name}" style="width: 40px; height: 40px; color: white; font-weight: bold;">
                         ${firstLetter}
                    </div>`;

                tbody.append(`
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2 avatar-rounded">${avatarHTML}</div>
                                <div class="lh-1">
                                    <span>${name}</span><br>
                                    <small class="text-muted">${designation}</small>
                                </div>
                            </div>
                        </td>
                    </tr>
                `);
            });
        }

        assignAvatarColors();
    }

    function FetchStates() {
        $.ajax({
            url: '{{ route('attendance.admin.states', ['box_slug' => request()->route('box_slug')]) }}',
            method: 'GET',
            data: { scope: 'team' },
            success: function(response) {
                if (response.status === 200 && response.data) {
                    const data = response.data;

                    // Total employees
                    $('#TotalEmployeHe').text(data.total_users);

                    // Absent count in top-right avatar
                    $('#loadAbsentEmployees').text(`+${data.today.absent}`);

                    // Define metrics and map to element IDs
                    const metrics = {
                        present: {
                            value: data.today.present,
                            percent: data.diff_percent.present,
                            id: 'present'
                        },
                        absent: {
                            value: data.today.absent,
                            percent: data.diff_percent.absent,
                            id: 'absent'
                        },
                        late_login: {
                            value: data.today.late_logins,
                            percent: data.diff_percent.late_logins,
                            id: 'late_login'
                        },
                        approved_leaves: {
                            value: data.today.approved_leaves,
                            percent: data.diff_percent.approved_leaves,
                            id: 'approved_leaves'
                        },
                        unapproved_leaves: {
                            value: data.today.unapproved_leaves,
                            percent: data.diff_percent.unapproved_leaves,
                            id: 'unapproved_leaves'
                        }
                    };

                    // Loop and update
                    for (const key in metrics) {
                        const metric = metrics[key];
                        const badgeClass = metric.percent >= 0 ? 'badge-success' : 'badge-danger';
                        const sign = metric.percent >= 0 ? '+' : '';

                        // Update number
                        $(`#stat-${metric.id}`).text(metric.value);

                        // Update badge
                        $(`#badge-${metric.id}`)
                            .removeClass('badge-success badge-danger')
                            .addClass(badgeClass)
                            .html(`
                                <i class="ti ti-arrow-wave-right-down me-1"></i>
                                ${sign}${metric.percent}%
                            `);
                    }
                }
            },
            error: function(xhr) {
                console.error('Error fetching attendance stats:', xhr.responseText);
            }
        });
    }

    FetchStates();

    // Column definitions with names
    const columnDefinitions = [
        { index: 0, name: 'Checkbox', visible: true },
        { index: 1, name: 'Sr.No', visible: true },
        { index: 2, name: 'Employee', visible: true },
        { index: 3, name: 'Designation', visible: true },
        { index: 4, name: 'Department', visible: true },
        { index: 5, name: 'Working Mode', visible: true },
        { index: 6, name: 'Shift', visible: true },
        { index: 7, name: 'Attendace date', visible: true },
        { index: 8, name: 'Status', visible: true },
        { index: 9, name: 'Marked Via', visible: true },
        { index: 10, name: 'Check In', visible: true },
        { index: 11, name: 'Check Out', visible: true },
        { index: 12, name: 'Break', visible: true },
        { index: 13, name: 'Late', visible: true },
        { index: 14, name: 'Overtime', visible: true },
        { index: 15, name: 'Working hour', visible: true },
        { index: 16, name: 'Production Hours', visible: true },
        { index: 17, name: 'Machine', visible: true },
        { index: 18, name: 'Attendace Correction', visible: true }
    ];

    // Page identifier for column preferences
    const pageIdentifier = 'attendance_admin';

    // Load column visibility preferences from database
    function loadColumnPreferences(callback) {
        $.ajax({
            url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug")]) }}',
            type: 'GET',
            data: {
                page_identifier: pageIdentifier
            },
            async: false, // Make synchronous to ensure preferences are loaded before table initialization
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
        const $table = $('#attendace-admin');
        if ($.fn.DataTable.isDataTable($table)) {
            columnDefinitions.forEach(col => {
                $table.DataTable().column(col.index).visible(col.visible);
            });
        }
    }

    // Populate columns modal
    function populateColumnsModal() {
        const columnsList = $('#columnsList');
        columnsList.empty();

        columnDefinitions.forEach(col => {
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

    // Load preferences on page load (synchronous)
    loadColumnPreferences(function() {
        // Preferences loaded, ready to use
    });

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

        saveColumnPreferences();
        applyColumnVisibility();

        // Reload table if it exists
        const $table = $('#attendace-admin');
        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().draw();
        }

        // Close modal
        $('#manageColumnsModal').modal('hide');
    });

    // Apply initial column visibility after a short delay
    setTimeout(function() {
        applyColumnVisibility();
    }, 500);

    /** ===============================
     * 3. Fetch Attendance Records
     * =============================== */
    function fetchEmployees() {
        if (!document.getElementById('custom-badge-styles')) {
            const style = document.createElement('style');
            style.id = 'custom-badge-styles';
            style.innerHTML = `
                .badge-orange {
                    background-color: orange !important;
                    color: white !important;
                }
                .badge-success {
                    background-color: #28a745 !important;
                    color: white !important;
                }
                .badge-danger {
                    background-color: #dc3545 !important;
                    color: white !important;
                }
                .badge-warning {
                    background-color: #ffc107 !important;
                    color: black !important;
                }
                .badge-secondary {
                    background-color: #6c757d !important;
                    color: white !important;
                }
            `;
            document.head.appendChild(style);
        }

        // Proceed with AJAX
        const $table = $('#attendace-admin');
        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().clear().destroy();
        }

        let attendanceTable = $table.DataTable({
            serverSide: true,
            processing: true,
            responsive: true,
            autoWidth: false,
            order: [[7, 'desc']], // Default sort by attendance date (column 7) descending
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            ajax: {
                url: '{{ route('team.attendace.employee', ['box_slug' => request()->route('box_slug')]) }}',
                data: function(d) {
                    d.from_date = filters.from_date;
                    d.to_date = filters.to_date;
                    d.department = filters.department;
                    d.status = filters.status;
                    d.status_correction = filters.status_correction;
                    d.scope = filters.scope;
                }
            },
            columns: [
                { data: null, defaultContent: '<div class="form-check form-check-md"><input class="form-check-input" type="checkbox" /></div>', orderable: false, searchable: false },
                { data: 'id', name: 'id', render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, searchable: false },
                { 
                    data: 'user_name', name: 'user_name', 
                    render: function(data, type, att) {
                        const name = att.user_name || 'N/A';
                        const imageHTML = att.profile_image ?
                            `<img src="${att.profile_image}" class="avatar avatar-md rounded-circle" width="40" height="40" />` :
                            `<div class="letter-avatar avatar avatar-md rounded-circle d-flex align-items-center justify-content-center"
                                 data-name="${name}" style="width: 40px; height: 40px; color: white; font-weight: bold;">
                                 ${name.charAt(0).toUpperCase()}
                             </div>`;
                        return `<div class="d-flex align-items-center">${imageHTML}<div class="ms-2"><h6 class="fw-medium mb-0">${name}</h6></div></div>`;
                    }
                },
                { data: 'box_user.designation_relation.name', name: 'boxUser.designationRelation.name', defaultContent: '-' },
                { data: 'box_user.department_relation.name', name: 'boxUser.departmentRelation.name', defaultContent: '-' },
                { data: 'box_user.work_mode.name', name: 'boxUser.workMode.name', defaultContent: '-' },
                { 
                    data: 'box_user.shift.code', name: 'boxUser.shift.code',
                    render: function(data, type, att) {
                        const shift = att.box_user?.shift?.code ?? null;
                        const shiftid = att.box_user?.shift?.id ?? null;
                        return `<button class="btn btn-sm btn-primary shift-btn" ${shift && shiftid ? `data-id="${shiftid}" data-bs-toggle="modal" data-bs-target="#shiftInfoModal"` : 'disabled'}>${shift ?? '--'}</button>`;
                    }
                },
                { 
                    data: 'attendance_date', name: 'attendance_date',
                    render: function(data) {
                        return data ? (typeof formatDate === 'function' ? formatDate(data) : data) : '-';
                    }
                },
                { 
                    data: 'status', name: 'status',
                    render: function(data) {
                        const status = data || 'N/A';
                        const normalizedStatus = status.toLowerCase();
                        let badgeClass = 'badge-secondary';
                        if (normalizedStatus === 'present') badgeClass = 'badge-success';
                        else if (normalizedStatus === 'absent') badgeClass = 'badge-danger';
                        else if (normalizedStatus === 'half day') badgeClass = 'badge-warning';
                        else if (normalizedStatus === 'short leave') badgeClass = 'badge-orange';
                        else if (normalizedStatus === 'comp off') badgeClass = 'badge-info';
                        return `<span class="badge ${badgeClass}">${status}</span>`;
                    }
                },
                { 
                    data: 'check_attendace_by', name: 'check_attendace_by',
                    render: function(data) {
                        return typeof formatAttendanceSource === 'function' ? formatAttendanceSource(data) : (data || '-');
                    }
                },
                { 
                    data: 'check_in', name: 'check_in',
                    render: function(data) { return typeof formatTime === 'function' ? formatTime(data) : (data || '-'); }
                },
                { 
                    data: 'check_out', name: 'check_out',
                    render: function(data) { return typeof formatTime === 'function' ? formatTime(data) : (data || '-'); }
                },
                { 
                    data: 'break_duration', name: 'break_duration',
                    render: function(data, type, att) {
                        return data && data !== '-' ? `<span class="badge clickable-break" data-attendance-id="${att.id}" data-bs-toggle="modal" data-bs-target="#breaksModal" style="cursor: pointer; background-color: #6f42c1; color: white;"><i class="ti ti-clock-pause me-1"></i>${data}</span>` : '-';
                    }
                },
                { data: 'late_duration', name: 'late_duration', defaultContent: '-' },
                { data: 'over_time', name: 'over_time', defaultContent: '-' },
                { data: 'working_hours', name: 'working_hours', defaultContent: '-' },
                { 
                    data: 'production_hours', name: 'production_hours',
                    render: function(data, type, att) {
                        return `<span class="badge badge-danger d-inline-flex align-items-center clickable-production" data-id="${att.id}" data-bs-toggle="modal" data-bs-target="#attendance_report" style="cursor: pointer;"><i class="ti ti-clock-hour-11 me-1"></i>${data || '0.00 Hrs'}</span>`;
                    }
                },
                { data: 'machine.serial_no', name: 'machine.serial_no', defaultContent: '-' },
                { 
                    data: 'corrections_statuses', name: 'corrections_statuses',
                    render: function(data, type, att) {
                        const name = att.user_name || 'N/A';
                        const corrections = att.corrections_statuses;
                        const hasCorrections = Array.isArray(corrections) && corrections.length > 0;
                        if (!hasCorrections) return 'N/A';
                        
                        const pendingReq = corrections.find(c => c.status === 'pending');
                        const approvedReq = corrections.find(c => c.status === 'approved');
                        
                        if (pendingReq) {
                            return `<button class="btn btn-sm btn-warning corrections-btn" data-id="${att.id}" data-date="${att.attendance_date}" data-emp-name="${name}" data-emp-id="${att.box_user?.employee_id || '-'}" data-corrections='${JSON.stringify(corrections)}' data-bs-toggle="modal" data-bs-target="#correctionsModal">View Pending</button>`;
                        } else if (approvedReq) {
                            return `<span class="badge bg-success">Approved</span>`;
                        } else {
                            const rejectedReq = corrections.find(c => c.status === 'rejected');
                            if (rejectedReq) return `<span class="badge bg-danger">Rejected</span>`;
                            return `<span class="badge bg-secondary">Correction</span>`;
                        }
                    }
                }
            ],
            drawCallback: function(settings) {
                if (typeof applyColumnVisibility === 'function') {
                    applyColumnVisibility();
                }
            }
        });

    }
//    $('#export-attendance-pdf').on('click', function () {
//     $.ajax({
//         url: "{{ route('export.attendace.pdf', ['box_slug' => request()->route('box_slug')]) }}",
//         method: "POST",
//         headers: {
//             'X-CSRF-TOKEN': '{{ csrf_token() }}'
//         },
//         success: function (response) {
//             console.log(response.data);
//             localStorage.setItem('attendanceReport', JSON.stringify(response.data));
//             window.location.href = "{{ box_route('attendance.report.view') }}"; // new page to show report
//         },
//         error: function (xhr) {
//             alert('Export failed. Check console for details.');
//             console.error(xhr.responseText);
//         }
//     });
// });

$('#export-attendance-pdf').on('click', function () {
        $('#exportConfirmModal').modal('show');
    });

    // Handle confirm button
    $('#confirmExportBtn').on('click', function () {
        const monthInput = $('#creditMonth').val();

        if (!monthInput) {
            console.warn('Month selection is required for export.');
            return;
        }

        // Split the input into year and month
        const [year, month] = monthInput.split('-');

        $('#exportConfirmModal').modal('hide'); // Close modal

        $.ajax({
            url: "{{ route('export.attendace.pdf', ['box_slug' => request()->route('box_slug')]) }}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: { month, year, scope: 'team' },
            beforeSend: function () {
                $('#confirmExportBtn').prop('disabled', true).text('Exporting...');
            },
            success: function (response) {
                console.log(response.data);
                localStorage.setItem('attendanceReport', JSON.stringify(response.data));
                window.location.href = "{{ box_route('attendance.report.view') }}";
            },
            error: function (xhr) {
                const response = xhr.responseJSON;
                const msg = (response && response.message) ? response.message : 'Export failed. Check console for details.';
                console.error(msg, xhr.responseText);
            },
            complete: function () {
                $('#confirmExportBtn').prop('disabled', false).text('Export PDF');
            }
        });
    });


function waitForFileDownload(fileUrl) {
    const interval = setInterval(function () {
        $.ajax({
            url: fileUrl,
            type: 'HEAD',
            success: function () {
                clearInterval(interval);
                const link = document.createElement('a');
                link.href = fileUrl;
                link.download = '';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                console.log('Your download should start now!');
            },
            error: function () {
                console.log('Waiting for file to be ready...');
            }
        });
    }, 3000);
}
$('#export-attendance-btn').prop('disabled', true);
$('#export-attendance-btn').prop('disabled', false);


    $('#correctionForm').on('submit', function(e) {
        e.preventDefault();

        const correctionId = $('#correctionId').val();
        const reason = $('#reason1').val();
        const status = $('#status1').val();

        if (!status) {
            console.warn('Status selection is required.');
            return;
        }
        if (!correctionId) {
            console.error('Correction ID missing.');
            return;
        }

        const payload = {
            correctionId: correctionId,
            reason: reason,
            status: status
        };

        $.ajax({
            url: "{{ route('update.corrections', ['box_slug' => request()->route('box_slug')]) }}",
            type: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Correction updated successfully.');
                const modalElement = document.getElementById('correctionsModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                fetchEmployees();
                modal.hide();
            },
            error: function(xhr) {
                console.error('Error updating correction:', xhr.responseText);
            }
        });
    });

    // Attendance correction
    $(document).on('click', '.corrections-btn', function() {
        const correctionsJson = $(this).attr('data-corrections');
        const corrections = JSON.parse(correctionsJson);

        if (!corrections || corrections.length === 0) {
            console.warn('No corrections found.');
            return;
        }

        const correction = corrections[0]; // example: first correction
        const correctionType = correction.correction_type || 'attendance';

        // Set employee details
        $('#employeeName').text($(this).attr('data-emp-name') || '-');
        $('#employeeId').text($(this).attr('data-emp-id') || '-');

        // Set correction type
        $('#correctionType').val(correctionType);

        if (correctionType === 'break') {
            // Show break correction fields, hide attendance fields
            $('#attendanceCorrectionFields').hide();
            $('#breakCorrectionFields').show();
            $('#correctionTypeTitle').text('Break Correction Request');

            // Set break correction data
            $('#breakStartTime').text(correction.break?.break_start ? formatDateTime(correction.break.break_start) : 'N/A');
            $('#breakEndTime').text(correction.break?.break_end ? formatDateTime(correction.break.break_end) : 'Ongoing');
            $('#requestedBreakStart').text(correction.break_start ? formatDateTime(correction.break_start) : 'N/A');
            $('#requestedBreakEnd').text(correction.break_end ? formatDateTime(correction.break_end) : 'Not specified');
        } else {
            // Show attendance correction fields, hide break fields
            $('#attendanceCorrectionFields').show();
            $('#breakCorrectionFields').hide();
            $('#correctionTypeTitle').text('Attendance Correction Request');

         // Normalize statuses: if '1', show 'Missed'
        const checkInStatus = (correction.punch_in === "1" || correction.punch_in === 1) ?
            'Missed' : (correction.punch_in || 'N/A');
        const checkOutStatus = (correction.punch_out === "1" || correction.punch_out === 1) ?
            'Missed' : (correction.punch_out || 'N/A');

            // Set attendance correction data
        $('#checkInStatus').text(checkInStatus);
        $('#checkInTime').text(correction.punch_in_time || '-');
        $('#checkOutStatus').text(checkOutStatus);
        $('#checkOutTime').text(correction.punch_out_time || '-');
        }

        const dateFromAttr = $(this).attr('data-date');
        $('#requestedDate').text(dateFromAttr ? formatDate(dateFromAttr) : 'N/A');

        const createdAt = correction.created_at;
        $('#createdDate').text(createdAt ? formatDateTime(createdAt) : 'N/A');

        // Common fields
        $('#reason1').val(correction.reason || '');
        $('#status1').val(correction.status || '').trigger('change');
        $('#correctionId').val(correction.id);

        // Show modal (optional if using bootstrap modal)
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        const modalElement = document.getElementById('correctionsModal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    });

    // Shift show
    $(document).on('click', '.shift-btn', function() {
        const shiftId = $(this).data('id');
        console.log("Shift ID clicked:", shiftId);

        $.ajax({
            url: '{{ route('get.shift.details', ['box_slug' => request()->route('box_slug')]) }}',
            type: 'GET',
            data: {
                shiftId: shiftId
            },
            success: function(response) {
                console.log("Shift Details Response:", response);
                const data = response.data;
                // Basic Info
                $('#shiftId').text(data.id ?? '-');
                $('#shiftName').text(data.code ?? '-');
                $('#shiftType').text(data.shift_name ?? '-');
                $('#shiftStatus').text(data.shift_enabled ? 'Enabled' : 'Disabled')
                    .removeClass()
                    .addClass(`badge ${data.shift_enabled ? 'bg-success' : 'bg-secondary'}`);

                // Shift Time
                $('#inTime').text(formatTime(data.in_time));
                $('#outTime').text(formatTime(data.out_time));
                $('#totalHours').text(data.shift_hours ?? '-');

                // Rest Time
                $('#lunchOut').text(formatTime(data.lunch_out));
                $('#lunchIn').text(formatTime(data.lunch_in));
                $('#lunchDuration').text(data.lunch_hours ?? '-');
                $('#restStatus').text(data.rest_enabled ? 'Enabled' : 'Disabled')
                    .removeClass()
                    .addClass(`badge ${data.rest_enabled ? 'bg-success' : 'bg-secondary'}`);

                // Short Breaks
                $('#break1').text(
                    `${formatTime(data.short_break_out)} - ${formatTime(data.short_break_in)}`
                );
                $('#break2').text(
                    `${formatTime(data.short_break_out_2)} - ${formatTime(data.short_break_in_2)}`
                );
                $('#totalBreak').text(data.short_break_hours ?? '-');
                $('#shortBreakStatus').text(data.short_break_enabled ? 'Enabled' : 'Disabled')
                    .removeClass()
                    .addClass(`badge ${data.short_break_enabled ? 'bg-success' : 'bg-secondary'}`);

                // Half Day
                $('#halfDayStatus').text(data.half_day_enabled ? 'Enabled' : 'Disabled')
                    .removeClass()
                    .addClass(`badge ${data.half_day_enabled ? 'bg-success' : 'bg-secondary'}`);
                $('#halfStartTime').text(formatTime(data.half_day_start_time));
                $('#halfEndTime').text(formatTime(data.half_day_end_time));
                $('#halfDuration').text(data.half_day_hours ?? '-');
                $('#halfDayFrom').text(data.half_day_time ?? '-');

                // Deduction Rules
                $('#absentBelow').text(data.absent_below ?? '-');
                $('#absentDeduction').text(data.absent_deduction ?? '-');

                $('#halfDayBelow').text(data.half_day_below ?? '-');
                $('#halfDayDeduction').text(data.half_day_deduction ?? '-');

                $('#shortLeaveBelow').text(data.short_leave_below ?? '-');
                $('#shortLeaveDeduction').text(data.short_leave_deduction ?? '-');

                // Overtime
                $('#overtimeStatus').text(data.check_over_time == 1 ? 'Enabled' : 'Disabled')
                    .removeClass()
                    .addClass(`badge ${data.check_over_time == 1 ? 'bg-success' : 'bg-secondary'}`);
                $('#overtimeAllowed').text(data.overtime_allowed ?? '-');

                // Weekly Off
                let offDays = [data.weekly_off_1, data.weekly_off_2, data.weekly_off_3,
                    data.weekly_off_4
                ].filter(Boolean);
                $('#weeklyOffDays').html(offDays.map(day =>
                    `<span class="badge bg-secondary me-1">${day}</span>`).join('') || '-');

                // Saturday Offs
                $('#satOffs').html(data.saturday_offs?.length ?
                    data.saturday_offs.map(week =>
                        `<span class="badge bg-info me-1">${week}</span>`).join('') : '-'
                );

                // Saturday Half Days
                $('#satHalfDays').html(data.saturday_half_days?.length ?
                    data.saturday_half_days.map(week =>
                        `<span class="badge bg-warning text-dark me-1">${week}</span>`).join('') : '-'
                );

                // Show the modal
                $('#shiftInfoModal').modal('show');
            },
            error: function(xhr) {
                console.error("Error fetching shift details:", xhr.responseJSON);
            }
        });

        function formatTime(time) {
            return time ? time.slice(0, 5) : '-';
        }
    });

    if (localStorage.getItem('fromDashboard') || filters.status_correction === 'Pending') {
        if (localStorage.getItem('fromDashboard')) {
            filters.status_correction = 'Pending';
            $('#correctionBtn').text('Pending');
            localStorage.removeItem('fromDashboard');
        }
        fetchEmployees();
    } else {
        fetchEmployees();
    }

    /** ===============================
     * 4. Filters and Event Binding
     * =============================== */
    $(document).on('click', '.department-option', function() {
        const selectedDeptName = $(this).text().trim();
        const selectedDeptId = $(this).data('dept-id');
        filters.department = selectedDeptId; // Send ID in AJAX
        $('#departmentBtn').text(selectedDeptName); // Just show the name visually

        fetchEmployees();
    });

    $('.status-option').on('click', function() {
        const statusValue = $(this).data('value'); // Value for backend
        const statusLabel = $(this).text().trim(); // Label for UI

        filters.status = statusValue;
        $('#statusBtn').text(statusLabel); // Show label in button
        fetchEmployees();
    });

    $('.status-correction').on('click', function () {
    // Reset status filter to avoid conflicts
    filters.status = '';
    $('#statusBtn').text('Select Status');

    // Set correction status
    filters.status_correction = $(this).text().trim();
    $('#correctionBtn').text(filters.status_correction);

    fetchEmployees();
});


    $('.sort-option').on('click', function() {
        filters.sort_by = $(this).text().trim().toLowerCase();
        $('#sortBtn').text('Sort By: ' + $(this).text().trim());
        fetchEmployees();
    });

    $('.date-range.bookingrange').on('change', function() {
        const val = $(this).val();
        if (val.includes(' - ')) {
            const [from, to] = val.split(' - ');
            filters.from_date = from.trim();
            filters.to_date = to.trim();
            $('#clearDateRange').removeClass('d-none');
        } else {
            filters.from_date = '';
            filters.to_date = '';
            $('#clearDateRange').addClass('d-none');
        }
        fetchEmployees();
    });





    $(document).on('click', '#loadAbsentEmployees', loadAbsentAvatars);
    $('#standard-modal').on('show.bs.modal', loadAbsentAvatars);
    $(document).on('click', '.clear-filter', function() {
        const filterKey = $(this).data('filter');
        switch (filterKey) {
            case 'department':
                filters.department = '';
                $('#departmentBtn').text('Department');
                break;

            case 'status':
                filters.status = '';
                $('#statusBtn').text('Select Status');
                break;

            case 'correction_status':
                filters.status_correction = ''; // Correct filter key
                $('#correctionBtn').text('Correction Status');
                break;

            case 'sort_by':
                filters.sort_by = '';
                $('#sortBtn').text('Sort By: Last 7 Days');
                break;

            case 'date_range':
                filters.from_date = '';
                filters.to_date = '';
                $('#dateRange').val('');
                break;
        }

        fetchEmployees();
    });

    /** ===============================
     * 5. Attendance Edit Modal
     * =============================== */
    $(document).on('click', '.edit-attendance-btn', function() {
        const modal = $('#edit_attendance');
        modal.find('input[name="employee_id"]').val($(this).data('employee-id'));
        modal.find('input[name="attendance_id"]').val($(this).data('attendance-id') || '');
        modal.find('input[name="attendance_date"]').val($(this).data('attendance-date'));
        modal.find('input[name="check_in"]').val($(this).data('check-in'));
        modal.find('input[name="check_out"]').val($(this).data('check-out'));
        modal.find('input[name="break_duration"]').val($(this).data('break'));
        modal.find('input[name="late_duration"]').val($(this).data('late'));
        modal.find('input[name="production_hours"]').val($(this).data('production-hours'));
        modal.find('select[name="status"]').val($(this).data('status')?.charAt(0).toUpperCase() + $(this).data('status')?.slice(1).toLowerCase());
    });

    $('#attendanceForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: '{{ route('edit_attendance', ['box_slug' => request()->route('box_slug')]) }}',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                $('#edit_attendance').modal('hide');
                cachedAbsentees = null;
                $('#edit_attendance').on('hidden.bs.modal', fetchEmployees);
            },
            error: function(xhr) {
                console.error("Error saving attendance:", xhr.responseJSON?.message || "An error occurred.");
            }
        });
    });

    $('#saved_changes').on('click', function() {
        $('#attendanceForm').submit();
    });

    /** ===============================
     * 6. Utilities
     * =============================== */
    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return `${String(date.getDate()).padStart(2, '0')}-${String(date.getMonth() + 1).padStart(2, '0')}-${date.getFullYear()}`;
    }

    function formatTime(timeStr) {
        if (!timeStr) return '-';
        const [h, m] = timeStr.split(':');
        const hour = parseInt(h);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const formattedHour = hour % 12 || 12;
        return `${formattedHour}:${m} ${ampm}`;
    }
 function formatAttendanceSource(source) {
    const normalized = (source || '').toLowerCase();

    if (!normalized || normalized === 'n/a') {
    return `<span class="badge" style="background:#ffb84d; color:#000;">N/A</span>`;
    }

    const label = normalized === 'machine' ? 'Machine' : 'Web';
    const badgeClass = normalized === 'machine' ? 'bg-info' : 'bg-secondary';

    return `<span class="badge ${badgeClass}">${label}</span>`;
}



    function getColorFromName(name) {
        const colors = ["#f44336", "#e91e63", "#9c27b0", "#673ab7", "#3f51b5", "#2196f3", "#03a9f4",
            "#00bcd4", "#009688", "#4caf50", "#8bc34a", "#cddc39", "#ffc107", "#ff9800", "#ff5722"
        ];
        const index = name ? name.charCodeAt(0) % colors.length : 0;
        return colors[index];
    }

    function assignAvatarColors() {
        document.querySelectorAll('.letter-avatar[data-name]').forEach(el => {
            el.style.backgroundColor = getColorFromName(el.getAttribute('data-name') || '');
        });
    }

    function saveSegments() {
        localStorage.setItem('time_segments', JSON.stringify(timeSegments));
    }

    function parseTime(timeStr) {
        if (!timeStr) return null;
        const today = new Date().toISOString().split('T')[0];
        let date;
        if (timeStr.includes('T')) {
            date = new Date(timeStr);
        } else {
            const [hours, minutes, seconds] = timeStr.split(':').map(Number);
            date = new Date(`${today}T${hours}:${minutes}:${seconds}.000Z`);
        }
        return date.getTime() + (5.5 * 60 * 60 * 1000); // Adjust to IST
    }

    function initializeSegmentsFromHourlyChart(hourlyChart, shiftStart, shiftEnd, inTime, outTime) {
        timeSegments = [];
        if (!hourlyChart || !hourlyChart.length) return;
        const today = new Date().toISOString().split('T')[0];
        const shiftStartTs = parseTime(shiftStart);
        const shiftEndTs = parseTime(shiftEnd);
        const inTimeTs = inTime ? parseTime(inTime) : null;
        if (!inTimeTs) {
            hourlyChart.forEach(item => {
                const segStart = parseTime(`${today}T${item.label}:00.000Z`);
                timeSegments.push({
                    type: item.type,
                    start: segStart,
                    end: segStart + (item.duration_minutes * 60 * 1000)
                });
            });
            saveSegments();
            return;
        }
        // Absent before check-in
        if (inTimeTs > shiftStartTs) {
            timeSegments.push({
                type: 'absent',
                start: shiftStartTs - (2 * 60 * 60 * 1000),
                end: inTimeTs
            });
        }
        let currentTime = inTimeTs;
        hourlyChart.forEach(item => {
            const segStart = parseTime(`${today}T${item.label}:00.000Z`);
            if (segStart < currentTime) return;
            const segEnd = segStart + (item.duration_minutes * 60 * 1000);
            timeSegments.push({
                type: item.type,
                start: currentTime,
                end: segEnd
            });
            currentTime = segEnd;
        });
        if (!outTime) {
            timeSegments.push({
                type: 'productive',
                start: currentTime,
                end: null
            });
        }
        saveSegments();
    }

    // Render progress bar
    function renderRealtimeProgressBar(attendanceId) {
        const $bar = $('#attendanceProgressBar');
        const $timeLabels = $('#timeLabels');
        $bar.empty().css('display', 'flex');
        $timeLabels.empty().css('display', 'flex');

        $.ajax({
            url: '{{ route('individual.attendance.summary', ['box_slug' => request()->route('box_slug')]) }}',
            method: 'GET',
            data: {
                attendance_id: attendanceId
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (!res.status) return;

                const {
                    time_segments: apiSegments = [],
                    hour_labels: hourLabels = [],
                    total_working_hours: totalWorkingHours = '00h 00m 00s',
                    productive_hours: productiveHours = '00h 00m 00s',
                    break_hours: breakHours = '00h 00m 00s',
                    overtime: overtime = '00h 00m 00s',
                    shift_start_time: shiftStart,
                    shift_end_time: shiftEnd,
                    intime: inTime,
                    status,
                    punchInTime: intimeFormatted,
                    punchOutTime: outTime,
                    attendance_date,
                    user_name,
                    department_name,
                    designation_name,
                    profile_image,
                    hourly_chart: hourlyChart
                } = res.data;

                // Initialize segments
                initializeSegmentsFromHourlyChart(hourlyChart, shiftStart, shiftEnd, inTime, outTime);

                // Fill summary details
                $('#totalWorkingHours').text(totalWorkingHours);
                $('#productiveHours').text(productiveHours);
                $('#breakHours').text(breakHours);
                $('#overtimeHours').text(overtime);
                $('#modalStatus').text(status);
                $('#modalCheckIn').text(intimeFormatted || '-');
                $('#modalCheckOut').text(outTime || '-');
                $('#modalDate').text(attendance_date);
                $('#modalUserName').text(user_name || '-');
                $('#modalDesignation').text(department_name || '-');
                $('#designation_name').text(designation_name || '-');
                const $modalAvatar = $('#modalProfileImage');
                if (profile_image) {
                    $modalAvatar.html(`
                        <img src="${profile_image}" class="avatar-sm avatar-rounded" alt="Profile Image" />
                    `);
                } else {
                    const initial = user_name ? user_name.charAt(0).toUpperCase() : '?';
                    $modalAvatar.html(`
                        <div class="letter-avatar avatar avatar-sm avatar-rounded d-flex align-items-center justify-content-center"
                             data-name="${user_name || 'User'}"
                             style="color: white; font-weight: bold;">
                             ${initial}
                        </div>
                    `);
                    assignAvatarColors();
                }
                // Render hour labels
                const labelWidth = (100 / hourLabels.length).toFixed(2);
                hourLabels.forEach(label => {
                    $timeLabels.append(
                        `<span class="fs-10 text-center" style="flex: 0 0 ${labelWidth}%;">${label}</span>`
                    );
                });

                // Render time segments
                apiSegments.forEach(item => {
                    const colorClass = item.type === 'productive' ? 'bg-success' :
                        item.type === 'break' ? 'bg-warning' :
                        item.type === 'overtime' ? 'bg-info' :
                        'bg-light';
                    $bar.append(`
                        <div class="progress-bar ${colorClass} rounded me-1"
                             role="progressbar"
                             style="flex: 0 0 ${item.percentage}%"
                             title="${item.start} - ${item.end || 'now'} (${item.type})">
                        </div>
                    `);
                });
            },
            error: function() {
                $('#attendance_report .modal-body').html(
                    '<p class="text-danger">Failed to load data.</p>'
                );
            }
        });
    }

    $(document).on('click', '.custom-btn-close', function() {
        const modalId = '#attendance_report';
        const modalEl = document.querySelector(modalId);
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) {
            modalInstance.hide();
        }
        setTimeout(() => {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
        }, 300);
    });

    $(document).on('click', '.clickable-production', function() {
        const attendanceId = $(this).data('id');
        const modal = new bootstrap.Modal(document.getElementById('attendance_report'));
        modal.show();
        renderRealtimeProgressBar(attendanceId);
    });

    // Breaks Modal Handler
    $(document).on('click', '.clickable-break', function() {
        const attendanceId = $(this).data('attendance-id');
        const modalElement = document.getElementById('breaksModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
        loadBreaksData(attendanceId);
    });

    // Properly close breaks modal and clean up backdrop
    function closeBreaksModal() {
        const modalElement = document.getElementById('breaksModal');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
        // Clean up backdrop after a short delay
        setTimeout(function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('overflow', '');
            $('body').css('padding-right', '');
        }, 300);
    }

    // Close button handlers
    $(document).on('click', '#breaksModalClose, #breaksModalCloseBtn', function() {
        closeBreaksModal();
    });

    // Close on backdrop click
    $('#breaksModal').on('click', function(e) {
        if ($(e.target).hasClass('modal') && $(e.target).attr('id') === 'breaksModal') {
            closeBreaksModal();
        }
    });

    function loadBreaksData(attendanceId) {
        // Show loading, hide content
        $('#breaksLoading').show();
        $('#breaksContent').hide();
        $('#breaksTableBody').empty();
        $('#noBreaksMessage').hide();
        $('#activeBreakIndicator').hide();

        $.ajax({
            url: '{{ route('get.attendance.breaks', ['box_slug' => request()->route('box_slug')]) }}',
            method: 'GET',
            data: {
                attendance_id: attendanceId
            },
            success: function(response) {
                $('#breaksLoading').hide();
                $('#breaksContent').show();

                if (response.status === 200 && response.data && response.data.length > 0) {
                    const breaks = response.data;
                    $('#totalBreaksCount').text(response.total_breaks || breaks.length);

                    let tableRows = '';
                    let hasActiveBreak = false;

                    breaks.forEach((breakItem, index) => {
                        const breakStart = formatDateTime(breakItem.break_start);
                        // Check if break is active/ongoing (break_end is null or break_status is 1)
                        const isActive = !breakItem.break_end || breakItem.break_end === null || breakItem.break_status == 1;
                        const breakEnd = isActive ? '<span class="text-warning fw-bold">On Break</span>' : formatDateTime(breakItem.break_end);
                        const duration = isActive ? '<span class="text-muted">-</span>' : `<strong>${breakItem.duration || '-'}</strong>`;

                        // Status badge
                        let statusBadge = '';
                        if (isActive) {
                            statusBadge = '<span class="badge bg-warning text-dark"><i class="ti ti-clock-play me-1"></i>Active</span>';
                            hasActiveBreak = true;
                        } else {
                            statusBadge = '<span class="badge bg-success"><i class="ti ti-check me-1"></i>Completed</span>';
                        }

                        // Row styling with different colors for ongoing vs completed breaks
                        let rowClass = '';
                        let rowStyle = '';
                        if (isActive) {
                            // Ongoing breaks: Orange/Yellow background with left border
                            rowClass = 'table-warning';
                            rowStyle = 'background-color: #fff3cd; border-left: 4px solid #ffc107; font-weight: 500;';
                        } else {
                            // Completed breaks: Light green background with left border
                            rowClass = '';
                            rowStyle = 'background-color: #d1e7dd; border-left: 4px solid #198754;';
                        }

                        tableRows += `
                            <tr class="${rowClass}" style="${rowStyle}">
                                <td style="font-weight: ${isActive ? '600' : '500'};">${index + 1}</td>
                                <td style="font-weight: ${isActive ? '600' : '500'};">${breakStart}</td>
                                <td style="font-weight: ${isActive ? '600' : '500'};">${breakEnd}</td>
                                <td style="font-weight: ${isActive ? '600' : '500'};">${duration}</td>
                                <td>${statusBadge}</td>
                            </tr>
                        `;
                    });

                    // Show active break indicator if any break is active
                    if (hasActiveBreak) {
                        $('#activeBreakIndicator').show();
                    } else {
                        $('#activeBreakIndicator').hide();
                    }

                    $('#breaksTableBody').html(tableRows);
                    $('#noBreaksMessage').hide();
                } else {
                    $('#totalBreaksCount').text('0');
                    $('#breaksTableBody').empty();
                    $('#noBreaksMessage').show();
                    $('#activeBreakIndicator').hide();
                }
            },
            error: function(xhr) {
                $('#breaksLoading').hide();
                $('#breaksContent').show();
                $('#breaksTableBody').html(`
                    <tr>
                        <td colspan="5" class="text-center text-danger">
                            <i class="ti ti-alert-circle me-2"></i>Failed to load breaks data.
                        </td>
                    </tr>
                `);
                console.error('Error loading breaks:', xhr.responseText);
            }
        });
    }

    function formatDateTime(dateTimeStr) {
        if (!dateTimeStr) return '-';
        try {
            const date = new Date(dateTimeStr);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            let hours = date.getHours();
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            return `${day}-${month}-${year} ${hours}:${minutes} ${ampm}`;
        } catch (e) {
            return dateTimeStr;
        }
    }
});
</script>
@endpush
@endsection
