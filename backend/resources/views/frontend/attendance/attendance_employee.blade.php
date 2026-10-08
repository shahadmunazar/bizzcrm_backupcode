@extends('layouts.main')
@section('content')
@section('title', 'Employee Attendance')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Employee Attendance </h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="index.html"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            Employee
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Employee Attendance </li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                <div class="me-2 mb-2">
                    <div class="d-flex align-items-center border bg-white rounded p-1 me-2 icon-list">
                        <a href="{{ box_route('employee.attendace') }}"
                            class="btn btn-icon btn-sm active bg-primary text-white me-1"><i
                                class="ti ti-brand-days-counter"></i></a>
                        <a href="{{ box_route('employee.attendace.admin') }}" class="btn btn-icon btn-sm"><i
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
                                <a href="javascript:void(0);" class="dropdown-item rounded-1"><i
                                        class="ti ti-file-type-pdf me-1"></i>Export as PDF </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" id="export-attendance-btn"
                                    class="dropdown-item rounded-1">
                                    <i class="ti ti-file-type-xls me-1"></i> Export as Excel
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="mb-2">
                    <a href="#" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal"
                        data-bs-target="#attendance_report"><i class="ti ti-file-analytics me-2"></i>Report </a>
                </div>
                <div class="ms-2 head-icons">
                    <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-original-title="Collapse" id="collapse-header">
                        <i class="ti ti-chevrons-up"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <div class="row">
            <div class="col-xl-3 col-lg-4 d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <!-- Attendance Date & Time -->
                        <div class="mb-4 text-center">
                            <h6 class="fw-medium text-gray-5 mb-1">Attendance</h6>
                            <h4 id="attendance_date_time">--:-- --, -- --- ----</h4>
                        </div>

                        <!-- Circle Progress Bar -->
                        <div class="attendance-circle-progress attendance-progress mx-auto mb-3" data-value="65">
                            <span class="progress-left">
                                <span class="progress-bar border-success"></span>
                            </span>
                            <span class="progress-right">
                                <span class="progress-bar border-success"></span>
                            </span>
                            {{-- <div class="total-work-hours text-center w-100">
                                <span class="fs-13 d-block mb-1">Total Hours</span>
                                <h6 id="total_work_hours">--:--:--</h6>
                            </div> --}}
                            <div class="total-work-hours text-center w-100">
                                <span class="fs-13 d-block mb-1" id="nextBreak"
                                    style="color: #198754; font-weight: 600;">--:--</span>
                                <span id="breakend"
                                    style="color: #0d6efd; font-weight: 600; font-size: 1rem;">--:--:--</span>
                                <span style="color: #0d6efd; font-weight: 600; font-size: 1rem;"> - </span>
                                <span id="breakstart"
                                    style="color: #0d6efd; font-weight: 600; font-size: 1rem;">--:--</span>
                            </div>
                        </div>

                        <!-- Status, Punch Time, and Buttons -->
                        <div class="text-center">
                            <!-- Production Badge -->
                            <div class="badge badge-dark badge-md mb-3">
                                <span id="production_hours">--</span>
                                <span id="production_stopwatch" style="display:none;">00:00:00</span>
                            </div>

                            <!-- Punch Time Info -->
                            <div id="punch_time_info">
                                <h6 class="fw-medium d-flex align-items-center justify-content-center mb-4">
                                    <i class="ti ti-fingerprint text-primary me-1"></i>
                                    <span id="punch_time_label">Punch In at</span>&nbsp;
                                    {{-- <span id="start_time">--</span> --}}
                                </h6>
                            </div>

                            <!-- Attendance Status -->
                            <div id="attendance_result" class="mb-3">
                                <strong>Status:</strong> <span id="attendance_status_text">--</span>
                            </div>

                            <!-- Punch Button -->
                            <button id="attendance_mark" class="btn w-100 d-none">Loading...</button>

                            <!-- Take Break Button -->
                            <button type="submit" id="take_break_btn"
                                class="btn btn-warning w-100 d-none mt-2 tea-break" data-status="start">
                                Loading...
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-9 col-lg-8 d-flex">
                <div class="row flex-fill">
                    <!-- Today Hours -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="border-bottom mb-2 pb-2">
                                    <span class="avatar avatar-sm bg-primary mb-2"><i
                                            class="ti ti-clock-stop"></i></span>
                                    <h2 class="mb-2">
                                        <span id="production_today_hours">--</span> /
                                        <span class="fs-20 text-gray-5" id="total_today_hours_emp">--</span>
                                    </h2>
                                    <p class="fw-medium text-truncate">Total Hours Today</p>
                                </div>
                                <div>
                                    <p class="d-flex align-items-center fs-13">
                                        <span class="avatar avatar-xs rounded-circle bg-success flex-shrink-0 me-2">
                                            <i class="ti ti-arrow-up fs-12"></i>
                                        </span>
                                        <span>5% This Week</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Weekly Hours -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="border-bottom mb-2 pb-2">
                                    <span class="avatar avatar-sm bg-dark mb-2"><i class="ti ti-clock-up"></i></span>
                                    <h2 class="mb-2">
                                        <span id="weekly_production_hours">--</span> /
                                        <span class="fs-20 text-gray-5" id="weeklyTotalTime">--</span>
                                    </h2>
                                    <p class="fw-medium text-truncate">Total Hours Week</p>
                                </div>
                                <div>
                                    <p class="d-flex align-items-center fs-13">
                                        <span class="avatar avatar-xs rounded-circle bg-success flex-shrink-0 me-2">
                                            <i class="ti ti-arrow-up fs-12"></i>
                                        </span>
                                        <span>7% Last Week</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Hours -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="border-bottom mb-2 pb-2">
                                    <span class="avatar avatar-sm bg-info mb-2"><i
                                            class="ti ti-calendar-up"></i></span>
                                    <h2 class="mb-2">
                                        <span id="monthly_production_hours">--</span> /
                                        <span class="fs-20 text-gray-5" id="monthlyTotalTime">--</span>
                                    </h2>
                                    <p class="fw-medium text-truncate">Total Hours Month</p>
                                </div>
                                <div>
                                    <p class="d-flex align-items-center fs-13 text-truncate">
                                        <span class="avatar avatar-xs rounded-circle bg-danger flex-shrink-0 me-2">
                                            <i class="ti ti-arrow-down fs-12"></i>
                                        </span>
                                        <span>8% Last Month</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Overtime (Placeholder IDs - update if needed) -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="border-bottom mb-2 pb-2">
                                    <span class="avatar avatar-sm bg-pink mb-2"><i
                                            class="ti ti-calendar-star"></i></span>
                                    <h2 class="mb-2">
                                        <span id="monthly_production_overtime">--:--</span> /
                                        <span class="fs-20 text-gray-5" id="getOverTimeData">--:--</span>
                                    </h2>
                                    <p class="fw-medium text-truncate">Overtime this Month</p>
                                </div>
                                <div>
                                    <p class="d-flex align-items-center fs-13 text-truncate">
                                        <span class="avatar avatar-xs rounded-circle bg-danger flex-shrink-0 me-2">
                                            <i class="ti ti-arrow-down fs-12"></i>
                                        </span>
                                        <span>6% Last Month</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-3">
                                        <div class="mb-3">
                                            <p class="d-flex align-items-center mb-1"><i
                                                    class="ti ti-point-filled text-dark-transparent me-1"></i>Total
                                                Working hours </p>
                                            <h3 id="totalWorkingHours">--:-- </h3>
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="mb-3">
                                            <p class="d-flex align-items-center mb-1"><i
                                                    class="ti ti-point-filled text-success me-1"></i>Productive Hours
                                            </p>
                                            <h3 id="productiveHours">--:--</h3>
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="mb-3">
                                            <p class="d-flex align-items-center mb-1"><i
                                                    class="ti ti-point-filled text-warning me-1"></i>Break hours </p>
                                            <h3 id="breakHours">--:-- </h3>
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="mb-3">
                                            <p class="d-flex align-items-center mb-1"><i
                                                    class="ti ti-point-filled text-info me-1"></i>Overtime </p>
                                            <h3 id="overtimeHours">--:--</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="progress bg-transparent-dark mb-3" style="height: 24px;"
                                            id="attendanceProgressBar">

                                        </div>

                                    </div>
                                    <div class="co-md-12">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2"
                                            id="timeLabels">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                <h5>Employee Attendance </h5>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="me-3">
                        <div class="input-icon-end position-relative">
                            <input type="text" id="dateRange" class="form-control date-range bookingrange"
                                placeholder="dd/mm/yyyy - dd/mm/yyyy" />

                            <span class="input-icon-addon">
                                <i class="ti ti-chevron-down"></i>
                            </span>

                        </div>
                    </div>
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
                        <button id="correctionBtn"
                            class="btn btn-white dropdown-toggle d-inline-flex align-items-center" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Correction Status
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-3">
                            <li><a class="dropdown-item status-correction" href="javascript:void(0);">Approved</a>
                            </li>
                            <li><a class="dropdown-item status-correction" href="javascript:void(0);">Pending</a></li>
                            <li>
                                <a class="dropdown-item clear-filter" data-filter="correction_status"
                                    href="javascript:void(0);">
                                    Clear Status
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="me-3">
                        <button type="button" class="btn btn-white d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                            <i class="ti ti-columns me-1"></i>Manage Columns
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="custom-datatable-filter table-responsive">
                    <table id="attendanceTable" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Sr.No</th>
                                <th>Attendance Date</th>
                                <th>Check In</th>
                            <th>Correction Status</th>
                            <th>Status</th>
                            {{-- <th>Machine</th> --}}
                            <th>Marked Via</th>
                            <th>Check Out</th>
                                <th>Break</th>
                                <th>Late</th>
                                <th>Overtime</th>
                                <th>Shift</th>
                                <th>Working Hours</th>
                                <th>Production Hours</th>
                            </tr>
                        </thead>
                    </table>

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

    @include('frontend.attendance.partials.attendance_correction_modal')

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
                                        <th>Action</th>
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

    <!-- Break Correction Modal -->
    <div class="modal fade" id="breakCorrectionModal" tabindex="-1" aria-labelledby="breakCorrectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Break Correction Request</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="breakCorrectionForm" method="POST">
                    @csrf
                    <input type="hidden" name="correction_type" value="break">
                    <input type="hidden" name="attendace_id" id="breakCorrectionAttendanceId">
                    <input type="hidden" name="break_id" id="breakCorrectionBreakId">
                    <div class="modal-body pb-0">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="alert alert-info">
                                    <i class="ti ti-info-circle me-2"></i>
                                    <strong>Current Break:</strong>
                                    <div id="currentBreakInfo" class="mt-2"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Break Start <span class="text-danger">*</span></label>
                                    <div class="input-icon-end position-relative">
                                        <input type="datetime-local" id="breakStartCorrection" name="break_start" class="form-control" required>
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Break End</label>
                                    <div class="input-icon-end position-relative">
                                        <input type="datetime-local" id="breakEndCorrection" name="break_end" class="form-control">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar"></i>
                                        </span>
                                    </div>
                                    <small class="text-muted">Leave empty if break is still ongoing</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Reason <span class="text-danger">*</span></label>
                                    <div class="input-icon-end position-relative">
                                        <textarea name="reason" class="form-control" rows="3" placeholder="Enter reason for break correction..." required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="breakCorrectionSubmitBtn" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('frontend.attendance.partials.add_leave_modal')
    <div class="modal fade" id="unpaidLeaveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Unpaid Leave</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="unpaidLeaveInfo"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmUnpaidLeave" class="btn btn-primary">Proceed as Unpaid Leave</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Punch Out Miss Modal -->
    <div class="modal fade" id="punchOutMissModal" tabindex="-1" aria-labelledby="punchOutMissModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="punchOutMissForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="punchOutMissModalLabel">Handle Missing Punch</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="attendance_id" id="attendance_id" />

                        <div class="mb-3">
                            <label for="punchType" class="form-label">Select Punch Type</label>
                            <select name="punch_type" id="punchType" class="form-select" required>
                                <option value="" disabled selected>Select Punch Type</option>
                                <option value="in">Punch In</option>
                                <option value="out">Punch Out</option>
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="timePickerWrapper">
                            <label for="punchTime" class="form-label">Select Time</label>
                            <input type="time" name="punch_time" id="punchTime" class="form-control" required />
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="toastMessage" class="toast align-items-center text-white bg-danger border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body" id="toastBody"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>



    

</div>


@push('scripts')
    <script>
        $(document).ready(function() {
            let stopwatchInterval = null;
            let stopwatchStartTime = null;
            let timeSegments = [];
            let punchInTime = null;
            let productionSeconds = 0; // Declare here to fix ReferenceError
            let breakInterval = null; // For break stopwatch
            let breakStartTime = null; // For break stopwatch
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const savedStartTime = parseInt(localStorage.getItem('stopwatch_start_time'));
            const savedProdSeconds = parseInt(localStorage.getItem('production_seconds')) || 0;
            if (savedStartTime) {
                productionSeconds = savedProdSeconds;
                startStopwatch(savedStartTime);
                console.log("⏱️ Resumed stopwatch after page reload");
            }
            const savedBreakStartTime = parseInt(localStorage.getItem('break_start_time'));
            if (savedBreakStartTime && localStorage.getItem('break_status') === 'started') {
                startBreakStopwatch(savedBreakStartTime);
                console.log("⏱️ Resumed break stopwatch after page reload");
            }
            let attendanceTable;
            let filters = {
                status: '',
                status_correction: '',
                sort_by: '',
                from_date: '',
                to_date: ''
            };

            // Column definitions with names
            const columnDefinitions = [
                { index: 0, name: 'Sr.No', visible: true },
                { index: 1, name: 'Attendance Date', visible: true },
                { index: 2, name: 'Check In', visible: true },
                { index: 3, name: 'Correction Status', visible: true },
                { index: 4, name: 'Status', visible: true },
                { index: 5, name: 'Marked Via', visible: true },
                { index: 6, name: 'Check Out', visible: true },
                { index: 7, name: 'Break', visible: true },
                { index: 8, name: 'Late', visible: true },
                { index: 9, name: 'Overtime', visible: true },
                { index: 10, name: 'Shift', visible: true },
                { index: 11, name: 'Working Hours', visible: true },
                { index: 12, name: 'Production Hours', visible: true }
            ];

            // Page identifier for column preferences
            const pageIdentifier = 'attendance_employee';

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

            // Apply column visibility (without triggering drawCallback)
            function applyColumnVisibility() {
                if (typeof attendanceTable !== 'undefined' && attendanceTable && $.fn.DataTable.isDataTable('#attendanceTable')) {
                    columnDefinitions.forEach(col => {
                        try {
                            attendanceTable.column(col.index).visible(col.visible, false);
                        } catch (e) {
                            console.error('Error applying column visibility:', e);
                        }
                    });
                    // Only adjust columns, don't redraw to avoid infinite loop
                    attendanceTable.columns.adjust();
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

            // Load preferences on page load BEFORE table initialization (synchronous)
            loadColumnPreferences(function() {
                // Preferences loaded, now initialize table
                initializeDataTable();
            });

            // Initialize DataTable
            function initializeDataTable() {
                // Check if DataTable already exists and destroy it first
                if ($.fn.DataTable.isDataTable('#attendanceTable')) {
                    $('#attendanceTable').DataTable().destroy();
                }
                
                attendanceTable = $('#attendanceTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('fetch.check.attendance', ['box_slug' => request()->route('box_slug')]) }}',
                        type: 'GET',
                        data: function(d) {
                            d.status = filters.status;
                            d.status_correction = filters.status_correction;
                            d.sort_by = filters.sort_by;
                            d.from_date = filters.from_date;
                            d.to_date = filters.to_date;
                        }
                    },
                    columns: [{
                            data: null,
                            orderable: false,
                            searchable: false,
                            visible: Boolean(columnDefinitions[0] && columnDefinitions[0].visible),
                            render: (data, type, row, meta) =>
                                meta.row + meta.settings._iDisplayStart + 1
                        },
                        {
                            data: 'date',
                            name: 'attendance_date',
                            visible: Boolean(columnDefinitions[1] && columnDefinitions[1].visible)
                        },
                        {
                            data: 'check_in',
                            name: 'check_in',
                            visible: Boolean(columnDefinitions[2] && columnDefinitions[2].visible)
                        },
                        {
                            data: 'corrections_statuses',
                            name: 'corrections_statuses',
                            visible: Boolean(columnDefinitions[3] && columnDefinitions[3].visible)
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false,
                            visible: Boolean(columnDefinitions[4] && columnDefinitions[4].visible),
                            render: function(data, type, row) {
                                const status = (data || 'N/A').toLowerCase();
                                const badgeStyles = {
                                    'present': 'background-color: #198754; color: white;',
                                    'absent': 'background-color: #dc3545; color: white;',
                                    'half day': 'background-color: #ffc107; color: black;',
                                    'short leave': 'background-color: #0d6efd; color: white;',
                                    'punch out miss': 'background-color: #285481; color: white;',
                                    'comp off': 'background-color: #0dcaf0; color: black;',
                                    'n/a': 'background-color: #6c757d; color: white;'
                                };
                                const style = badgeStyles[status] || badgeStyles['n/a'];
                                const label = status ? status.charAt(0).toUpperCase() + status
                                    .slice(1) : 'N/A';
                                return `
                            <div class="dropdown">
                                <button class="btn btn-sm dropdown-toggle d-inline-flex align-items-center"
                                    type="button"
                                    id="requestDropdown${row.id}"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                    style="padding: 4px 10px; border-radius: 12px; font-size: 12px; ${style}">
                                    <i class="ti ti-alert-circle me-1"></i>${label}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="requestDropdown${row.id}">
                                    <li><a class="dropdown-item open-attendance-modal"
                                           href="javascript:void(0);"
                                           data-id="${row.id}"
                                           data-bs-toggle="modal"
                                           data-bs-target="#attendance_request">
                                           Attendance Request
                                    </a></li>
                                    <li><a class="dropdown-item open-apply-leave-modal"
                                           href="javascript:void(0);"
                                           data-date="${row.raw_date}"
                                           data-bs-toggle="modal"
                                           data-bs-target="#add_leaves">
                                           Apply Leave
                                    </a></li>
                                </ul>
                            </div>`;
                            }
                        },
                        {{-- {
                            data: 'machine',
                            name: 'machine',
                            orderable: false,
                            searchable: false
                        }, --}}
                        {
                            data: 'check_attendace_by',
                            name: 'check_attendace_by',
                            orderable: false,
                            searchable: false,
                            visible: Boolean(columnDefinitions[5] && columnDefinitions[5].visible),
                            render: function(data, type, row) {
                                return formatAttendanceSource(data);
                            }
                        },
                        {
                            data: 'check_out',
                            name: 'check_out',
                            visible: Boolean(columnDefinitions[6] && columnDefinitions[6].visible)
                        },
                        {
                            data: 'break_duration',
                            name: 'break_minutes',
                            visible: Boolean(columnDefinitions[7] && columnDefinitions[7].visible),
                            render: function(data, type, row) {
                                if (data && data !== '-' && data !== null) {
                                    return `<span class="badge clickable-break" 
                                          data-attendance-id="${row.id}" 
                                          data-bs-toggle="modal" 
                                          data-bs-target="#breaksModal"
                                          style="cursor: pointer; background-color: #6f42c1; color: white;">
                                        <i class="ti ti-clock-pause me-1"></i>${data}
                                    </span>`;
                                }
                                return '-';
                            }
                        },
                        {
                            data: 'late_duration',
                            name: 'late_minutes',
                            visible: Boolean(columnDefinitions[8] && columnDefinitions[8].visible)
                        },
                        {
                            data: 'over_time',
                            name: 'overtime_minutes',
                            visible: Boolean(columnDefinitions[9] && columnDefinitions[9].visible)
                        },
                        {
                            data: 'shift',
                            name: 'shift',
                            orderable: false,
                            searchable: false,
                            visible: Boolean(columnDefinitions[10] && columnDefinitions[10].visible),
                            render: function(data, type, row) {
                                const shift = row.box_user?.shift?.code ?? null;
                                const shiftId = row.box_user?.shift?.id ?? null;
                                if (shift && shiftId) {
                                    return `
                                <button class="btn btn-sm btn-primary shift-btn"
                                    data-id="${shiftId}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#shiftInfoModal">${shift}</button>`;
                                } else {
                                    return `<button class="btn btn-sm btn-secondary" disabled>--</button>`;
                                }
                            }
                        },
                        {
                            data: 'working_hours',
                            name: 'working_hours',
                            visible: Boolean(columnDefinitions[11] && columnDefinitions[11].visible)
                        },
                        {
                            data: 'production_hours',
                            name: 'production_hours',
                            visible: Boolean(columnDefinitions[12] && columnDefinitions[12].visible)
                        }
                    ],
                    columnDefs: [{
                        targets: [3],
                        className: 'text-center'
                    }],
                    drawCallback: function() {
                        // Note: Don't call applyColumnVisibility here to avoid infinite loop
                    }
                });
            }
            
            // Note: Column visibility is already set during DataTable initialization via visible: columnDefinitions[X].visible
            // No need to call applyColumnVisibility here as it would be redundant

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
                
                // Apply column visibility and redraw table immediately
                if (attendanceTable && $.fn.DataTable.isDataTable('#attendanceTable')) {
                    columnDefinitions.forEach(col => {
                        try {
                            attendanceTable.column(col.index).visible(col.visible, false);
                        } catch (e) {
                            console.error('Error applying column visibility:', e);
                        }
                    });
                    attendanceTable.columns.adjust();
                    // Redraw only once after all changes
                    attendanceTable.draw(false);
                }
                
                // Close modal
                $('#manageColumnsModal').modal('hide');
            });
            fetchAttendance();
            $('.status-option').on('click', function() {
                filters.status = $(this).data('value');
                $('#statusBtn').text($(this).text().trim());
                attendanceTable.ajax.reload();
            });
            $('.status-correction').on('click', function() {
                filters.status = '';
                $('#statusBtn').text('Select Status');
                filters.status_correction = $(this).text().trim();
                $('#correctionBtn').text(filters.status_correction);
                attendanceTable.ajax.reload();
            });
            $('.sort-option').on('click', function() {
                filters.sort_by = $(this).text().trim().toLowerCase();
                $('#sortBtn').text('Sort By: ' + $(this).text().trim());
                attendanceTable.ajax.reload();
            });
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
                attendanceTable.ajax.reload();
            });
            $('.date-range.bookingrange').on('change', function() {
                const val = $(this).val();
                if (val.includes(' - ')) {
                    const [from, to] = val.split(' - ');
                    filters.from_date = from.trim();
                    filters.to_date = to.trim();
                } else {
                    filters.from_date = '';
                    filters.to_date = '';
                }
                attendanceTable.ajax.reload();
            });

            function saveSegments() {
                localStorage.setItem('time_segments', JSON.stringify(timeSegments));
            }
            // Helper to add new segment
            function addSegment(type, startTime = null) {
                const now = startTime || Date.now();
                const lastSeg = timeSegments[timeSegments.length - 1];
                if (lastSeg && !lastSeg.end) {
                    if (lastSeg.type === type) return;
                    lastSeg.end = now;
                }
                timeSegments.push({
                    type,
                    start: now,
                    end: null
                });
                saveSegments();
            }
            // Close last segment
            function endLastSegment(endTime = null) {
                const now = endTime || Date.now();
                if (timeSegments.length && !timeSegments[timeSegments.length - 1].end) {
                    timeSegments[timeSegments.length - 1].end = now;
                }
                saveSegments();
            }
            // Parse time string to timestamp (IST)
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
            // Format timestamp to HH:mm AM/PM
            function formatTimestamp(timestamp) {
                const date = new Date(timestamp);
                return date.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            }
            // Format seconds to "HHh MMm SSs"
            function formatTime(seconds) {
                const hrs = Math.floor(seconds / 3600);
                const mins = Math.floor((seconds % 3600) / 60);
                const secs = seconds % 60;
                return `${hrs.toString().padStart(2, '0')}h ${mins.toString().padStart(2, '0')}m ${secs.toString().padStart(2, '0')}s`;
            }

            function formatAttendanceSource(source) {
                const normalized = (source || 'web').toLowerCase();
                const label = normalized === 'machine' ? 'Machine' : 'Web';
                const badgeClass = normalized === 'machine' ? 'bg-info' : 'bg-secondary';
                return `<span class="badge ${badgeClass}">${label}</span>`;
            }
            // Initialize segments from hourly chart
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
            function renderRealtimeProgressBar() {
                const $bar = $('#attendanceProgressBar');
                const $timeLabels = $('#timeLabels');
                $bar.empty().css('display', 'flex');
                $timeLabels.empty().css('display', 'flex');
                // Fetch summary
                $.get('{{ route('check.attendance.summary', ['box_slug' => request()->route('box_slug')]) }}')
                    .done((res) => {
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
                            outTime,
                            hourly_chart: hourlyChart
                        } = res.data;
                        //Initialize in-memory time segments again
                        initializeSegmentsFromHourlyChart(hourlyChart, shiftStart, shiftEnd, inTime, outTime);
                        // Update totals
                        $('#totalWorkingHours').text(totalWorkingHours);
                        $('#productiveHours').text(productiveHours);
                        $('#breakHours').text(breakHours);
                        $('#overtimeHours').text(overtime);
                        // Render time labels (ensure no duplicates; normalize whitespace)
                        const normalizedHourLabels = (hourLabels || []).map(label =>
                            (label ?? '').toString().trim()
                        ).filter(label => label.length > 0);
                        const uniqueHourLabels = [...new Set(normalizedHourLabels)];
                        const labelWidth = (100 / uniqueHourLabels.length).toFixed(2);
                        uniqueHourLabels.forEach(label => {
                            $timeLabels.append(
                                `<span class="fs-10 text-center" style="flex: 0 0 ${labelWidth}%;">${label}</span>`
                            );
                        });
                        // Render each time segment into the progress bar
                        apiSegments.forEach(item => {
                            const colorClass = item.type === 'productive' ? 'bg-success' :
                                item.type === 'break' ? 'bg-warning' :
                                item.type === 'overtime' ? 'bg-info' :
                                'bg-white';
                            $bar.append(`
                <div class="progress-bar ${colorClass} rounded me-1"
                     role="progressbar"
                     style="flex: 0 0 ${item.percentage}%"
                     title="${item.start} - ${item.end || 'now'} (${item.type})">
                </div>
            `);
                        });
                    });
            }
            renderRealtimeProgressBar();
            // Live update progress bar every 2.5 minutes
            setInterval(() => {
                console.log("Progress bar updating...");
                renderRealtimeProgressBar();
            }, 3600000); // 1 hour = 3600000 milliseconds
            // Event: Mark Attendance
            $('#attendance_mark').click(function() {
                const $btn = $(this);
                const isAbsent = $btn.data('status') === 'Absent';
                const action = isAbsent ? 'in' : 'out';
                if (action === 'out' && localStorage.getItem('break_status') === 'started') {
                    showToast('You cannot punch out while on a break.', 'warning');
                    return;
                }
                $btn.prop('disabled', true).text('Processing...');
                const payload = {
                    punch: action,
                    status: 'Present'
                };
                if (action === 'out') {
                    payload.production_seconds = productionSeconds;
                }
                if (!navigator.geolocation) {
                    showToast('Geolocation is not supported by your browser.', 'warning');
                    $btn.prop('disabled', false).text(isAbsent ? 'Punch In' : 'Punch Out');
                    return;
                }

                const executePunch = async (latitude, longitude) => {
                    let locationStr = '';
                    
                    if (latitude && longitude) {
                        try {
                            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`);
                            const data = await response.json();
                            locationStr = data.display_name || '';
                        } catch (e) {
                            console.error('Error fetching location name:', e);
                        }
                    }

                    payload.latitude = latitude;
                    payload.longitude = longitude;
                    payload.location = locationStr;

                    $.post('{{ route('mark.attendace', ['box_slug' => request()->route('box_slug')]) }}', payload)
                        .done(() => {
                            if (action === 'in') {
                                // Punched In
                                $btn.data('status', 'Present').text('Punch Out').removeClass('btn-success').addClass('btn-danger');
                                punchInTime = Date.now();
                                productionSeconds = 0;
                                localStorage.setItem('production_seconds', productionSeconds);
                                addSegment('productive', punchInTime);
                                startStopwatch(punchInTime);
                                console.log('Punched In: Stopwatch started');
                            } else {
                                // Punched Out
                                $btn.data('status', 'Absent').text('Punch In').removeClass('btn-danger').addClass('btn-success');
                                stopStopwatch();
                                endLastSegment(Date.now());
                                productionSeconds = 0;
                                localStorage.removeItem('production_seconds');
                                clearBreakData();
                                console.log('Punched Out: Stopwatch stopped');
                            }
                            if ($.fn.DataTable.isDataTable('#attendanceTable')) {
                                $('#attendanceTable').DataTable().ajax.reload(null, false);
                            }
                            fetchAttendance();
                        })
                        .fail((xhr) => {
                            let message = 'An error occurred';
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.message) message = response.message;
                            } catch (e) {
                                console.error('Failed to parse JSON:', e);
                            }
                            showToast(message, 'warning');
                            console.error(xhr.responseText);
                            // Revert text on failure
                            $btn.text(isAbsent ? 'Punch In' : 'Punch Out');
                        })
                        .always(() => {
                            $btn.prop('disabled', false);
                        });
                };

                const fetchLocationAndPunch = () => {
                    // Temporarily bypass all location tracking as requested
                    showToast('Location tracking bypassed. Punching in/out...', 'info');
                    executePunch(null, null);
                };

                const fallbackToLocalhostOrError = (error) => {
                    // Unused now that location is bypassed
                    executePunch(null, null);
                };

                // Bypassed Permissions check
                fetchLocationAndPunch();
            });
            // Event: Take Break
            $('#take_break_btn').click(function() {
                const $btn = $(this);
                const action = $btn.data('status') === 'start' ? 'start' : 'end';
                const attendanceId = localStorage.getItem('attendance_id');
                if (!attendanceId) {
                    showToast('Attendance ID not found. Please mark attendance first.', 'warning');
                    resetBreakButton(action);
                    return;
                }
                $btn.prop('disabled', true).text('Processing...');
                $.post('{{ route('break.toggle', ['box_slug' => request()->route('box_slug')]) }}', {
                    action,
                    attendance_id: attendanceId
                }).done((res) => {
                    updateBreakUI(res);
                    updateBreakButtonState(action);
                    if (action === 'start') {
                        endLastSegment();
                        addSegment('break');
                        stopStopwatch();
                        startBreakStopwatch(Date.now());
                    } else {
                        endLastSegment();
                        addSegment('productive');
                        stopBreakStopwatch();
                        startStopwatch(Date.now());
                    }
                    fetchAttendance();
                }).fail((xhr) => {
                    showToast('Failed to update break status.', 'danger');
                    resetBreakButton(action);
                }).always(() => {
                    $btn.prop('disabled', false);
                });
            });

            function startBreakStopwatch(startTime = null) {
                if (breakInterval) return; // prevent duplicate intervals
                breakStartTime = startTime || parseInt(localStorage.getItem('break_start_time')) || Date.now();
                localStorage.setItem('break_start_time', breakStartTime);
                $('#production_stopwatch').text('Break: 00h 00m 00s').show();
                breakInterval = setInterval(() => {
                    const elapsed = Math.floor((Date.now() - breakStartTime) / 1000);
                    $('#production_stopwatch').text('Break: ' + formatTime(elapsed));
                }, 1000);
            }

            function stopBreakStopwatch() {
                if (!breakInterval) return;
                clearInterval(breakInterval);
                breakInterval = null;
                breakStartTime = null;
                localStorage.removeItem('break_start_time');
                $('#production_stopwatch').text('').hide();
            }
            // Initialize timepicker for attendance correction modal

            $(document).on('click', '.open-attendance-modal', function() {
                const attendanceId = $(this).data('id'); // get data-id from clicked item
                window.selectedAttendanceId = attendanceId; // Store in window for form submission
            });
            $(document).on('click', '.punch-out-miss-btn', function() {
                const attendanceId = $(this).data('id');
                console.log('Handle Punch Out Miss for ID:', attendanceId);
                // Optionally set the ID inside the modal (e.g. in a hidden input)
                $('#punchOutMissModal input[name="attendance_id"]').val(attendanceId);
                // Show the modal
                $('#punchOutMissModal').modal('show');
            });
            $('#punchType').on('change', function() {
                const selected = $(this).val();
                if (selected === 'in' || selected === 'out') {
                    $('#timePickerWrapper').removeClass('d-none');
                } else {
                    $('#timePickerWrapper').addClass('d-none');
                }
            });
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
                            .addClass(
                                `badge ${data.shift_enabled ? 'bg-success' : 'bg-secondary'}`);
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
                            .addClass(
                                `badge ${data.rest_enabled ? 'bg-success' : 'bg-secondary'}`);
                        // Short Breaks
                        $('#break1').text(
                            `${formatTime(data.short_break_out)} - ${formatTime(data.short_break_in)}`
                        );
                        $('#break2').text(
                            `${formatTime(data.short_break_out_2)} - ${formatTime(data.short_break_in_2)}`
                        );
                        $('#totalBreak').text(data.short_break_hours ?? '-');
                        $('#shortBreakStatus').text(data.short_break_enabled ? 'Enabled' :
                                'Disabled')
                            .removeClass()
                            .addClass(
                                `badge ${data.short_break_enabled ? 'bg-success' : 'bg-secondary'}`
                            );
                        // Half Day
                        $('#halfDayStatus').text(data.half_day_enabled ? 'Enabled' : 'Disabled')
                            .removeClass()
                            .addClass(
                                `badge ${data.half_day_enabled ? 'bg-success' : 'bg-secondary'}`
                            );
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
                        $('#overtimeStatus').text(data.check_over_time == 1 ? 'Enabled' :
                                'Disabled')
                            .removeClass()
                            .addClass(
                                `badge ${data.check_over_time == 1 ? 'bg-success' : 'bg-secondary'}`
                            );
                        $('#overtimeAllowed').text(data.overtime_allowed ?? '-');
                        // Weekly Off
                        let offDays = [data.weekly_off_1, data.weekly_off_2, data.weekly_off_3,
                            data.weekly_off_4
                        ].filter(Boolean);
                        $('#weeklyOffDays').html(offDays.map(day =>
                            `<span class="badge bg-secondary me-1">${day}</span>`).join(
                            '') || '-');
                        // Saturday Offs
                        $('#satOffs').html(data.saturday_offs?.length ?
                            data.saturday_offs.map(week =>
                                `<span class="badge bg-info me-1">${week}</span>`).join(
                                '') :
                            '-'
                        );
                        // Saturday Half Days
                        $('#satHalfDays').html(data.saturday_half_days?.length ?
                            data.saturday_half_days.map(week =>
                                `<span class="badge bg-warning text-dark me-1">${week}</span>`
                            ).join('') :
                            '-'
                        );
                        // Show the modal
                        $('#shiftInfoModal').modal('show');
                    },
                    error: function(xhr) {
                        showToast('Failed to fetch shift details.', 'danger');
                    }
                });

                function formatTime(time) {
                    return time ? time.slice(0, 5) : '-';
                }
            });
            $('#export-attendance-btn').on('click', function() {
                $.ajax({
                    url: "{{ route('export.attendace', ['box_slug' => request()->route('box_slug')]) }}",
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        type: 'employee'
                    },
                    success: function(response) {
                        showToast(response.message || 'Export started!', 'info');
                        console.log(response);
                        if (response.file_url) {
                            waitForFileDownload(response.file_url);
                        }
                    },
                    error: function(xhr, status, error) {
                        showToast('Export failed. Check console for details.', 'danger');
                        console.error('Export Error:', xhr.responseText);
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
                            showToast('Your download should start now!', 'success');
                        },
                        error: function() {
                            console.log('Waiting for file to be ready...');
                        }
                    });
                }, 3000);
            }
            $('#export-attendance-btn').prop('disabled', true);
            $('#export-attendance-btn').prop('disabled', false);

            function startStopwatch(startTime = null) {
                // Prevent multiple intervals
                if (stopwatchInterval) {
                    console.warn('Stopwatch already running, ignoring start request.');
                    return;
                }
                stopwatchStartTime = startTime || Date.now();
                const storedSeconds = parseInt(localStorage.getItem('production_seconds'), 10) || 0;
                if (isNaN(storedSeconds) || storedSeconds < 0) {
                    console.warn('Invalid production_seconds in localStorage, resetting to 0.');
                    productionSeconds = 0;
                } else {
                    productionSeconds = storedSeconds;
                }
                localStorage.setItem('stopwatch_start_time', stopwatchStartTime);
                $('#production_stopwatch').show();
                // Start interval for updating stopwatch
                stopwatchInterval = setInterval(() => {
                    const now = Date.now();
                    const elapsedSinceStart = Math.floor((now - stopwatchStartTime) / 1000);
                    const totalElapsed = productionSeconds + elapsedSinceStart;
                    $('#production_stopwatch').text('Production: ' + formatTime(totalElapsed));
                    localStorage.setItem('live_production_seconds', totalElapsed);
                    const maxAllowedSeconds = 23 * 60 * 60;
                    if (totalElapsed >= maxAllowedSeconds) {
                        // Max time reached – frontend only stops timer.
                        // Auto punch-out is now handled by backend cron job; we keep this comment for reference.
                        console.log(
                            'Stopping stopwatch after reaching max allowed seconds (handled by cron job).'
                        );
                        stopStopwatch();
                        $('#production_stopwatch').text('Production: ' + formatTime(productionSeconds))
                            .show();
                    }
                }, 1000);
            }

            function stopStopwatch() {
                if (!stopwatchInterval) return;
                clearInterval(stopwatchInterval);
                stopwatchInterval = null;
                const elapsedSinceStart = Math.floor((Date.now() - stopwatchStartTime) / 1000);
                productionSeconds += elapsedSinceStart;
                if (isNaN(productionSeconds) || productionSeconds < 0) {
                    console.warn('Invalid production seconds, resetting to 0.');
                    productionSeconds = 0;
                }
                localStorage.setItem('production_seconds', productionSeconds);
                $('#production_stopwatch').hide();
                localStorage.removeItem('stopwatch_start_time');
                localStorage.removeItem('live_production_seconds');
                stopwatchStartTime = null;
                endLastSegment(Date.now());
            }

            function autoPunchOut() {
                const status = 'auto_punch_out';
                const attendanceId = localStorage.getItem('attendance_id');
                const payload = {
                    status: status,
                    timestamp: new Date().toISOString(),
                    duration: productionSeconds,
                    attendance_id: attendanceId
                };
                $.ajax({
                    url: '{{ route('auto.attendance', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify(payload),
                    success: function(response) {
                        console.log('Auto punch-out successful:', response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Auto punch-out failed:', error);
                        console.log('Response:', xhr.responseText);
                    }
                });
            }

            function getCurrentProductionSeconds() {
                const storedStart = parseInt(localStorage.getItem('stopwatch_start_time'));
                const storedProd = parseInt(localStorage.getItem('production_seconds')) || 0;
                if (storedStart) {
                    const elapsed = Math.floor((Date.now() - storedStart) / 1000);
                    return storedProd + elapsed;
                }
                return storedProd;
            }
            getCurrentProductionSeconds();
            // Break Button UI
            function restoreBreakButtonState() {
                const $breakBtn = $('#take_break_btn');
                const breakStatus = localStorage.getItem('break_status');
                $breakBtn
                    .data('status', breakStatus === 'started' ? 'end' : 'start')
                    .text(breakStatus === 'started' ? 'End Break' : 'Take Break')
                    .removeClass('d-none');
            }

            function updateBreakButtonState(action) {
                const $breakBtn = $('#take_break_btn');
                if (action === 'start') {
                    $breakBtn.data('status', 'end').text('End Break');
                    localStorage.setItem('break_status', 'started');
                } else {
                    $breakBtn.data('status', 'start').text('Take Break');
                    localStorage.setItem('break_status', 'ended');
                }
            }

            function resetBreakButton(action) {
                $('#take_break_btn')
                    .prop('disabled', false)
                    .text(action === 'start' ? 'Take Break' : 'End Break');
            }

            function updateBreakUI(response) {
                if (response?.message) {
                    $('#break_status_msg').text(response.message);
                }
            }
            // Clear Data
            function clearBreakData() {
                localStorage.removeItem('attendance_id');
                localStorage.removeItem('break_status');
                localStorage.removeItem('stopwatch_start_time');
                localStorage.removeItem('time_segments');
                localStorage.removeItem('production_seconds');
                localStorage.removeItem('break_start_time');
                localStorage.removeItem('live_production_seconds');
                productionSeconds = 0;
                stopwatchStartTime = null;
                clearInterval(stopwatchInterval);
                stopwatchInterval = null;
                $('#production_stopwatch').text('00h 00m 00s').hide();
                timeSegments = [];
                saveSegments();
            }

            function fetchAttendance() {
                $.get('{{ route('check.attendance', ['box_slug' => request()->route('box_slug')]) }}')
                    .done((res) => {
                        if (!res.success) return;
                        const data = res.data;
                        const attendance = data.attendance || {};
                        const status = data.status || 'Absent';
                        const lowerStatus = status.toLowerCase();
                        // Remove 'approved' from isPunchInAllowed
                        const isPunchInAllowed = (
                            lowerStatus === 'absent' ||
                            lowerStatus.startsWith('holiday') ||
                            lowerStatus.startsWith('pending') ||
                            lowerStatus === 'weekly off' ||
                            lowerStatus === 'not a working day'
                        );
                        const checkInTime = attendance.check_in ?? '--';
                        const checkOutTime = attendance.check_out ?? '--';
                        const production = attendance.production_hours?.split(':').slice(0, 2).join(':') ||
                            '00:00';
                        const attendanceId = attendance.id;
                        const daily_hours = data.daily_hours;
                        if (data.nextBreak) {
                            const nextBreakName = data.nextBreak.name || 'Upcoming Break';
                            const breakStart = data.nextBreak.start || '--:--';
                            const breakEnd = data.nextBreak.end || '--:--';
                            $('#nextBreak').text(nextBreakName);
                            $('#breakstart').text(breakStart);
                            $('#breakend').text(breakEnd);
                        } else {
                            $('#nextBreak').text('No Upcoming Break');
                            $('#breakstart').text('--:--');
                            $('#breakend').text('--:--');
                        }
                        if (attendanceId) localStorage.setItem('attendance_id', attendanceId);
                        $('#attendance_status_text').text(status);
                        $('#total_work_hours, #production_today_hours').text(production);
                        $('#total_today_hours_emp').text(data.daily_hours ?? '--:--');
                        $('#weekly_production_hours').text(data.weekly_production_hours ?? '--:--');
                        $('#weeklyTotalTime').text(data.weekly_hours ?? '--:--');
                        $('#monthly_production_hours').text(data.monthly_production_hours ?? '--:--');
                        $('#monthlyTotalTime').text(data.monthly_hours ?? '--:--');
                        $('#getOverTimeData').text(data.total_overtime_hours ?? '--');
                        $('#monthly_production_overtime').text(data.monthly_production_overtime ?? '--');
                        const punchLabel = isPunchInAllowed ?
                            `Punch In at ${data.start_time ?? '--'}` :
                            `Punch Out at ${data.end_time ?? '--'}`;
                        $('#punch_time_label').text(punchLabel);
                        $('#attendance_date_time').text(
                            `${checkOutTime !== '--' ? checkOutTime : checkInTime}, ${data.date ?? '--'}`
                        );
                        const $punchBtn = $('#attendance_mark');
                        const $breakBtn = $('#take_break_btn');
                        const breakStatus = localStorage.getItem('break_status');

                        // ✅ If today is a leave day, disable everything & hide timers
                        if (lowerStatus.startsWith('approved') || lowerStatus.includes('leave')) {
                            $punchBtn
                                .text('On Leave Today')
                                .prop('disabled', true)
                                .removeClass('btn-success btn-danger btn-secondary d-none')
                                .addClass('btn-info btn-on-leave disabled');
                            $breakBtn.addClass('d-none');
                            clearBreakData();
                            stopStopwatch();
                            $('#production_hours').hide();
                            $('#production_stopwatch').hide();
                            return;
                        }
                        // ✅ If status is "Punch Out Miss", stop stopwatch, stop progress & lock actions
                        if (lowerStatus === 'punch out miss') {
                            // Ensure no live timers or break intervals keep running
                            stopStopwatch();
                            clearBreakData();
                            localStorage.removeItem('attendance_id');

                            $punchBtn
                                .text('Punch Out Miss')
                                .prop('disabled', true)
                                .removeClass('btn-success btn-danger btn-secondary d-none')
                                .addClass('btn-secondary disabled');

                            $breakBtn.addClass('d-none');

                            // Show final production from DB (no live timer)
                            $('#production_hours').show().text(production);
                            $('#production_stopwatch').hide();

                            // Do not start any new segments or live progress updates for today
                            return;
                        }
                        if (isPunchInAllowed) {
                            // Allow Punch In
                            timeSegments = [];
                            saveSegments();
                            $punchBtn
                                .text('Punch In')
                                .removeClass('btn-danger')
                                .addClass('btn-success')
                                .data('status', 'Absent')
                                .removeClass('d-none')
                                .prop('disabled', false);
                            $breakBtn.addClass('d-none');
                            clearBreakData();
                            $('#production_hours').show();
                            $('#production_stopwatch').hide();
                        } else {
                            $punchBtn
                                .text('Punch Out')
                                .removeClass('btn-success')
                                .addClass('btn-danger')
                                .data('status', 'Present')
                                .removeClass('d-none');
                            if (checkOutTime !== '--') {
                                // ✅ Already punched out (including Punch Out Miss) – hard stop timers
                                stopStopwatch();
                                endLastSegment(parseTime(checkOutTime));
                                $punchBtn.prop('disabled', true).text('Already Punched Out').addClass(
                                    'disabled');
                                $breakBtn.addClass('d-none');
                                clearBreakData();
                                $('#production_hours').show().text(production);
                                $('#production_stopwatch').hide();
                            } else {
                                const isOnBreak = breakStatus === 'started';
                                const lastSeg = timeSegments[timeSegments.length - 1];
                                if (!lastSeg || lastSeg.end) {
                                    addSegment(isOnBreak ? 'break' : 'productive');
                                } else if (lastSeg.type !== (isOnBreak ? 'break' : 'productive')) {
                                    endLastSegment();
                                    addSegment(isOnBreak ? 'break' : 'productive');
                                }
                                if (isOnBreak) {
                                    $punchBtn.prop('disabled', true).text('On Break').addClass('disabled');
                                    restoreBreakButtonState();
                                    stopStopwatch();
                                    startBreakStopwatch();
                                    $('#production_hours').hide();
                                    $('#production_stopwatch').show();
                                } else if (data.can_take_break) {
                                    restoreBreakButtonState();
                                    $punchBtn.prop('disabled', false).removeClass('disabled');
                                    startStopwatch(Date.now());
                                    $('#production_hours').hide();
                                    $('#production_stopwatch').show();
                                } else {
                                    $breakBtn.addClass('d-none');
                                    localStorage.removeItem('break_status');
                                    $punchBtn.prop('disabled', false).removeClass('disabled');
                                    startStopwatch(Date.now());
                                    $('#production_hours').hide();
                                    $('#production_stopwatch').show();
                                }
                            }
                        }
                        // Refresh summary/progress bar once after attendance data loaded
                        // Avoid double-calling the summary API here (renderRealtimeProgressBar already fetches it)
                        renderRealtimeProgressBar();
                    })
                    .fail(() => {
                        $('#attendance_result').html(
                            `<span class="text-danger">Unable to load attendance.</span>`
                        );
                    });
            }


            $('#add_leaves').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                if (button.length) {
                    var date = button.data('date');
                    if (date) {
                        $('#from_date').val(date).trigger('change');
                    }
                }
                $.ajax({
                    url: '{{ route('leave.type.request', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            let optionsHtml =
                                '<option disabled selected>Select Option</option>';
                            response.data.forEach(function(leaveType) {
                                optionsHtml +=
                                    `<option value="${leaveType.id}">${leaveType.leave_type}</option>`;
                            });
                            $('#select_leave_type').html(optionsHtml);
                        } else {
                            console.warn('Failed to load leave data.');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading data:', xhr.responseText);
                    }
                });
            });
            $('#select_leave_type').on('change', function() {
                let selectedLeaveTypeId = $(this).val();
                $.ajax({
                    url: '{{ route('get.leave.balance', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'GET',
                    data: {
                        leave_type_id: selectedLeaveTypeId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#leave_balance_msg').text('');
                        if (response.status) {
                            $('#add_leaves input[name="leave_balance"]').val(response.data
                                .balance_leave || '0');
                        } else {
                            $('#leave_balance_msg').text(response.message || 'No balance found');
                            $('#add_leaves input[name="leave_balance"]').val('0');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching leave details:', xhr.responseText);
                        $('#leave_balance_msg').text('Error fetching balance');
                        $('#add_leaves input[name="leave_balance"]').val('0');
                    }
                });
            });
            $('#leaves_apply').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let url = '{{ route('leave.request.store', ['box_slug' => request()->route('box_slug')]) }}';
                let formData = new FormData(form[0]);

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            showToast(response.message || 'Leave request submitted successfully!', 'success');
                            $('#add_leaves').modal('hide');
                            form.trigger('reset');
                            $('#file-preview').empty();
                        } else {
                            // Backend returned status: false
                            showToast(response.message || 'Failed to submit leave request.', 'danger');
                        }
                    },
                    error: function(xhr) {
                        let res = xhr.responseJSON;

                        if (res?.errors) {
                            // Validation errors
                            let errorMessages = Object.values(res.errors).flat().join('\n');
                            showToast('Validation errors:\n' + errorMessages, 'danger');
                        } else if (res?.extra_leave) {
                            // Insufficient leave balance -> show modal
                            $('#unpaidLeaveInfo').html(`
                                Requested days: <b>${res.requested}</b><br>
                                Available balance: <b>${res.available}</b><br>
                                Difference (unpaid days): <b>${res.difference}</b><br><br>
                                Do you want to proceed as unpaid leave?
                            `);

                            // Show the modal
                            let unpaidModal = new bootstrap.Modal(document.getElementById('unpaidLeaveModal'));
                            unpaidModal.show();

                            // Confirm button click
                            $('#confirmUnpaidLeave').off('click').on('click', function() {
                                formData.append('proceed_as_unpaid', 1);

                                $.ajax({
                                    type: 'POST',
                                    url: url,
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    success: function(resp) {
                                        showToast(resp.message || 'Leave request submitted successfully as unpaid leave!', 'success');
                                        $('#add_leaves').modal('hide');
                                        form.trigger('reset');
                                        $('#file-preview').empty();
                                        unpaidModal.hide();
                                    },
                                    error: function(err) {
                                        showToast(err.responseJSON?.message || 'Something went wrong.', 'danger');
                                    }
                                });
                            });
                        } else if (res?.message) {
                            showToast(res.message, 'danger');
                        } else {
                            showToast('Something went wrong. Please try again.', 'danger');
                        }
                    }
                });
            });

            $('input[name="attachment"]').on('change', function(e) {
                const file = e.target.files[0];
                const preview = $('#file-preview');
                preview.empty();
                if (file) {
                    const fileType = file.type;
                    const fileName = file.name;
                    const fileSize = (file.size / 1024).toFixed(5);

                    let previewHtml = '';
                    if (fileType.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewHtml = `
                        <div class="border p-2 rounded position-relative">
                            <img src="${e.target.result}" alt="${fileName}" style="max-width: 200px; max-height: 200px;">
                            <p class="mb-1 mt-1"><small><strong>${fileName}</strong> (${fileSize} KB)</small></p>
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-file">×</button>
                        </div>
                    `;
                            preview.html(previewHtml);
                        };
                        reader.readAsDataURL(file);
                    } else if (fileType === 'application/pdf') {
                        previewHtml = `
                    <div class="border p-2 rounded d-flex align-items-center position-relative">
                        <i class="ti ti-file-text fs-2 text-primary me-2"></i>
                        <div>
                            <p class="mb-1"><strong>${fileName}</strong></p>
                            <small class="text-muted">${fileSize} KB - PDF</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger ms-auto remove-file">Remove</button>
                    </div>
                `;
                        preview.html(previewHtml);
                    } else if (fileType === 'application/msword' || fileType ===
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                        previewHtml = `
                    <div class="border p-2 rounded d-flex align-items-center position-relative">
                        <i class="ti ti-file-word fs-2 text-blue me-2"></i>
                        <div>
                            <p class="mb-1"><strong>${fileName}</strong></p>
                            <small class="text-muted">${fileSize} KB - Document</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger ms-auto remove-file">Remove</button>
                    </div>
                `;
                        preview.html(previewHtml);
                    } else {
                        previewHtml = `
                    <div class="border p-2 rounded d-flex align-items-center position-relative">
                        <i class="ti ti-file fs-2 text-secondary me-2"></i>
                        <div>
                            <p class="mb-1"><strong>${fileName}</strong></p>
                            <small class="text-muted">${fileSize} KB</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger ms-auto remove-file">Remove</button>
                    </div>
                `;
                        preview.html(previewHtml);
                    }
                }
            });
            $(document).on('click', '.remove-file', function() {
                $('input[name="attachment"]').val('');
                $('#file-preview').empty();
            });

            function calculateTotalDays() {
                let appliedFor = $('#applied_for').val();
                if (!appliedFor) {
                    $("input[name='total_days']").val('');
                    return;
                }

                let factor = 1;
                if (appliedFor === 'half_day') factor = 0.5;
                else if (appliedFor === 'short_leave') factor = 0.25;

                let fromDate = new Date($("input[name='from_date']").val());
                let toDate = new Date($("input[name='to_date']").val());
                if (!isNaN(fromDate.getTime()) && !isNaN(toDate.getTime())) {
                    let timeDiff = toDate.getTime() - fromDate.getTime();
                    let dayDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
                    if (dayDiff > 0) {
                        let total = dayDiff * factor;
                        $("input[name='total_days']").val(total.toFixed(2));
                    } else {
                        $("input[name='total_days']").val('');
                    }
                } else {
                    $("input[name='total_days']").val('');
                }
            }

            $("input[name='from_date'], input[name='to_date'], #applied_for").on('change', function() {
                calculateTotalDays();
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
                                    // Ongoing breaks: Orange/Yellow background
                                    rowClass = 'table-warning';
                                    rowStyle = 'background-color: #fff3cd; border-left: 4px solid #ffc107; font-weight: 500;';
                                } else {
                                    // Completed breaks: Light green/blue background
                                    rowClass = '';
                                    rowStyle = 'background-color: #d1e7dd; border-left: 4px solid #198754;';
                                }
                                
                                // Request correction button - Only show for ongoing/active breaks
                                let requestButton = '';
                                if (isActive) {
                                    // Only allow correction requests for ongoing breaks
                                    requestButton = `
                                        <button class="btn btn-sm btn-warning request-break-correction" 
                                                data-break-id="${breakItem.id}"
                                                data-break-start="${breakItem.break_start}"
                                                data-break-end="${breakItem.break_end || ''}"
                                                data-attendance-id="${attendanceId}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#breakCorrectionModal"
                                                title="Request Break Correction">
                                            <i class="ti ti-edit me-1"></i>Request Correction
                                        </button>
                                    `;
                                } else {
                                    // Completed breaks cannot be corrected
                                    requestButton = `
                                        <span class="badge bg-secondary" title="Break correction not available for completed breaks">
                                            <i class="ti ti-lock me-1"></i>Completed
                                        </span>
                                    `;
                                }
                                
                                tableRows += `
                                    <tr class="${rowClass}" style="${rowStyle}">
                                        <td style="font-weight: ${isActive ? '600' : '500'};">${index + 1}</td>
                                        <td style="font-weight: ${isActive ? '600' : '500'};">${breakStart}</td>
                                        <td style="font-weight: ${isActive ? '600' : '500'};">${breakEnd}</td>
                                        <td style="font-weight: ${isActive ? '600' : '500'};">${duration}</td>
                                        <td>${statusBadge}</td>
                                        <td>${requestButton}</td>
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

            // Break Correction Request Handler
            $(document).on('click', '.request-break-correction', function() {
                const breakId = $(this).data('break-id');
                const breakStart = $(this).data('break-start');
                const breakEnd = $(this).data('break-end');
                const attendanceId = $(this).data('attendance-id');

                // Set form values
                $('#breakCorrectionAttendanceId').val(attendanceId);
                $('#breakCorrectionBreakId').val(breakId);

                // Format current break info
                const currentStart = breakStart ? formatDateTime(breakStart) : 'Not set';
                const currentEnd = breakEnd ? formatDateTime(breakEnd) : 'Ongoing (Not ended)';
                $('#currentBreakInfo').html(`
                    <div><strong>Start:</strong> ${currentStart}</div>
                    <div><strong>End:</strong> ${currentEnd}</div>
                `);

                // Convert datetime to local datetime-local format
                if (breakStart) {
                    const startDate = new Date(breakStart);
                    const startLocal = startDate.toISOString().slice(0, 16);
                    $('#breakStartCorrection').val(startLocal);
                } else {
                    $('#breakStartCorrection').val('');
                }

                if (breakEnd) {
                    const endDate = new Date(breakEnd);
                    const endLocal = endDate.toISOString().slice(0, 16);
                    $('#breakEndCorrection').val(endLocal);
                } else {
                    $('#breakEndCorrection').val('');
                }
            });

            // Break Correction Form Submit
            $('#breakCorrectionForm').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const $submitBtn = $('#breakCorrectionSubmitBtn');
                const formData = $form.serializeArray();

                // Disable button and show loading
                $submitBtn.prop('disabled', true).html('<i class="ti ti-loader spin me-2"></i>Submitting...');

                $.ajax({
                    url: '{{ route('saved.correction', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'POST',
                    data: $.param(formData),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.status) {
                            showToast(res.message || 'Break correction request submitted successfully!', 'success');
                            $('#breakCorrectionModal').modal('hide');
                            $form[0].reset();
                            // Reload breaks data
                            const attendanceId = $('#breakCorrectionAttendanceId').val();
                            if (attendanceId) {
                                loadBreaksData(attendanceId);
                            }
                        } else {
                            showToast('Failed: ' + (res.message || 'Unknown error'), 'danger');
                        }
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON?.errors || {};
                        let message = xhr.responseJSON?.message || 'An error occurred';
                        showToast(message + '\n' + JSON.stringify(errors), 'danger');
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false).html('Submit Request');
                    }
                });
            });
        });
    </script>
@endpush
@endsection
