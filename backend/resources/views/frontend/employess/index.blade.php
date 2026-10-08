@extends('layouts.main')
@section('content')
@section('title', 'Employee Dashboard')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Employee Dashboard </h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="index.html"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            Dashboard
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Employee Dashboard </li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
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
                                <a href="javascript:void(0);" class="dropdown-item rounded-1"><i
                                        class="ti ti-file-type-xls me-1"></i>Export as Excel </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="input-icon w-120 position-relative mb-2">
                    <span class="input-icon-addon">
                        <i class="ti ti-calendar text-gray-9"></i>
                    </span>
                    <input type="text" class="form-control datetimepicker" value="15 Apr 2025" />
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

        @if(isset($latestRequest) && $latestRequest)
        <div class="alert bg-secondary-transparent alert-dismissible fade show mb-4">
            Your {{ $requestType }} Request on "{{ \Carbon\Carbon::parse($latestRequest->created_at)->format('jS F Y') }}" is currently {{ ucfirst(strtolower($latestRequest->status)) }}!
            <button type="button" class="btn-close fs-14" data-bs-dismiss="alert" aria-label="Close"><i
                    class="ti ti-x"></i></button>
        </div>
        @endif
        <div class="row">
                <!-- Attendance Overview -->
                <div class="col-xxl-4 col-xl-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Attendance Overview </h5>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <select id="overviewYearSelect" class="form-select form-select-sm" style="width: 100px; padding: 4px 8px;"></select>
                                <select id="overviewMonthSelect" class="form-select form-select-sm" style="width: 130px; padding: 4px 8px;">
                                    <option value="01">January</option>
                                    <option value="02">February</option>
                                    <option value="03">March</option>
                                    <option value="04">April</option>
                                    <option value="05">May</option>
                                    <option value="06">June</option>
                                    <option value="07">July</option>
                                    <option value="08">August</option>
                                    <option value="09">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                        </div>
                        {{-- <div class="card-body">
                            <div class="chartjs-wrapper-demo position-relative mb-4">
                                <canvas id="attendancedd" height="200"></canvas>
                                <div class="position-absolute text-center attendance-canvas">
                                    <p class="fs-13 mb-1">Total Attendance </p>
                                    <h3 id="total_addedance">120 </h3>
                                </div>
                            </div>
                            <h6 class="mb-3">Status </h6>
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-success me-1"></i>Present </p>
                                <p class="f-13 fw-medium text-gray-9 mb-2">59% </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-secondary me-1"></i>Late </p>
                                <p class="f-13 fw-medium text-gray-9 mb-2">21% </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-warning me-1"></i>Permission </p>
                                <p class="f-13 fw-medium text-gray-9 mb-2">2% </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-danger me-1"></i>Absent </p>
                                <p class="f-13 fw-medium text-gray-9 mb-2">15% </p>
                            </div>
                            <div
                                class="bg-light br-5 box-shadow-xs p-2 pb-0 d-flex align-items-center justify-content-between flex-wrap">
                                <div class="d-flex align-items-center">
                                    <p class="mb-2 me-2">Total Absenties </p>
                                    <div class="avatar-list-stacked avatar-group-sm mb-2">
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="" alt="img" />
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="" alt="img" />
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img src="" alt="img" />
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img src="" alt="img" />
                                        </span>
                                        <a class="avatar bg-primary avatar-rounded text-fixed-white fs-10"
                                            href="javascript:void(0);">
                                            +1
                                        </a>
                                    </div>
                                </div>
                                <a href="leaves.html" class="fs-13 link-primary text-decoration-underline mb-2">View Details
                                </a>
                            </div>
                        </div> --}}
                        <div class="card-body">
                            <div class="chartjs-wrapper-demo position-relative mb-4">
                                <canvas id="attendance" height="200"></canvas>
                                <div class="position-absolute text-center attendance-canvas">
                                    <p class="fs-13 mb-1">Total Attendance </p>
                                    <h3 id="total_attendance">120</h3>
                                </div>
                            </div>
                            <h6 class="mb-3">Status </h6>
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-success me-1"></i>Present </p>
                                <p id="overview_present" class="f-13 fw-medium text-gray-9 mb-2">0</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-warning me-1"></i>Punch Out Miss </p>
                                <p id="overview_punchoutmiss" class="f-13 fw-medium text-gray-9 mb-2">0</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-danger me-1"></i>Absent </p>
                                <p id="overview_absent" class="f-13 fw-medium text-gray-9 mb-2">0</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-secondary me-1"></i>Weekly Off </p>
                                <p id="overview_weeklyoff" class="f-13 fw-medium text-gray-9 mb-2">0</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-info me-1"></i>Holiday </p>
                                <p id="overview_holiday" class="f-13 fw-medium text-gray-9 mb-2">0</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-primary me-1"></i>Half Day </p>
                                <p id="overview_halfday" class="f-13 fw-medium text-gray-9 mb-2">0</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-pink me-1"></i>Short Leave </p>
                                <p id="overview_shortleave" class="f-13 fw-medium text-gray-9 mb-2">0</p>
                            </div>

                            <div
                                class="bg-light br-5 box-shadow-xs p-2 pb-0 d-flex align-items-center justify-content-between flex-wrap">
                                <div class="d-flex align-items-center">
                                    <p class="mb-2 me-2">Total Absentees </p>
                                    <div id="absentees_avatars" class="avatar-list-stacked avatar-group-sm mb-2">
                                        <!-- Avatars will be appended here -->
                                    </div>
                                </div>
                                <a href="{{ box_route('employee.attendace.admin') }}"
                                    class="fs-13 link-primary text-decoration-underline mb-2">View
                                    Details </a>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- /Attendance Overview -->































        <!-- start from here -->

            <!--<div class="col-xl-4 d-flex">
                <div class="card position-relative flex-fill">
                    <div class="card-header bg-dark">
                        <div class="d-flex align-items-center">
                            <span class="avatar avatar-lg avatar-rounded border-white border-2 flex-shrink-0 me-2">
                                @if(!empty($employee->profile_image_path))
                                    <img src="{{ asset('storage/' . $employee->profile_image_path) }}"
                                        alt="Employee Image" />
                                @else
                                    @php
                                        $employeeName = Auth::user()->name ?? 'User';
                                        $employeeInitial = strtoupper(mb_substr($employeeName, 0, 1));
                                    @endphp
                                    <div class="letter-avatar avatar avatar-lg avatar-rounded d-flex align-items-center justify-content-center"
                                         data-name="{{ $employeeName }}"
                                         style="color:#fff; font-size:18px; font-weight:600;">
                                        {{ $employeeInitial }}
                                    </div>
                                @endif
                            </span>

                            <div>
                                <h5 class="text-white mb-1">{{ Auth::user()->name }}</h5>
                                <div class="d-flex align-items-center">
                                    <p class="text-white fs-12 mb-0"> {{ $employee->departmentRelation?->name }} </p>
                                    <span class="mx-1"><i class="ti ti-point-filled text-primary"></i></span>
                                    <p class="fs-12">{{ $employee->designationRelation?->name }} </p>
                                </div>
                            </div>
                        </div>
                        <a href="#" class="btn btn-icon btn-sm text-white rounded-circle edit-top"><i
                                class="ti ti-edit"></i></a> 
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="d-block mb-1 fs-13">Phone Number </span>
                            <p class="text-gray-9">{{ $employee->phone }} </p>
                        </div>
                        <div class="mb-3">
                            <span class="d-block mb-1 fs-13">Email Address </span>
                            <p class="text-gray-9">
                                <a href="mailto:{{ Auth::user()->email }}">{{ Auth::user()->email }}</a>
                            </p>

                        </div>
                        <div class="mb-3">
                            <span class="d-block mb-1 fs-13">Report Office </span>
                            @if(isset($reportOffices) && $reportOffices->count())
                                @foreach($reportOffices as $office)
                                    @php
                                        $initial = strtoupper(mb_substr($office['name'], 0, 1));
                                    @endphp
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="letter-avatar avatar avatar-xs avatar-rounded d-flex align-items-center justify-content-center me-2"
                                             data-name="{{ $office['name'] }}"
                                             style="color: #fff; font-size: 10px; font-weight: 600;">
                                            {{ $initial }}
                                        </div>
                                        <p class="text-gray-9 mb-0">{{ $office['name'] }}</p>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-gray-9 mb-0">-</p>
                            @endif
                        </div>
                        <div>
                            <span class="d-block mb-1 fs-13">Joined on </span>
                            <p class="text-gray-9">{{ \Carbon\Carbon::parse($employee->joining_date)->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div> -->
            {{-- <div class="col-xl-5 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                            <h5>Leave Details </h5>
                            <div class="dropdown">
                                <a href="javascript:void(0);"
                                    class="btn btn-white border btn-sm d-inline-flex align-items-center"
                                    data-bs-toggle="dropdown">
                                    <i class="ti ti-calendar me-1"></i>2024
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end p-3">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">2024 </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">2023 </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">2022 </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="mb-3">
                                        <p class="d-flex align-items-center"><i
                                                class="ti ti-circle-filled fs-8 text-dark me-1"></i>
                                            <span class="text-gray-9 fw-semibold me-1">1254 </span>
                                            on time
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="d-flex align-items-center"><i
                                                class="ti ti-circle-filled fs-8 text-success me-1"></i>
                                            <span class="text-gray-9 fw-semibold me-1">32 </span>
                                            Late Attendance
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="d-flex align-items-center"><i
                                                class="ti ti-circle-filled fs-8 text-primary me-1"></i>
                                            <span class="text-gray-9 fw-semibold me-1">658 </span>
                                            Work From Home
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="d-flex align-items-center"><i
                                                class="ti ti-circle-filled fs-8 text-danger me-1"></i>
                                            <span class="text-gray-9 fw-semibold me-1">14 </span>
                                            Absent
                                        </p>
                                    </div>
                                    <div>
                                        <p class="d-flex align-items-center"><i
                                                class="ti ti-circle-filled fs-8 text-warning me-1"></i>
                                            <span class="text-gray-9 fw-semibold me-1">68 </span>
                                            Sick Leave
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 d-flex justify-content-md-end">
                                    <div id="leaves_chart"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="todo1" />
                                    <label class="form-check-label" for="todo1">Better than <span
                                            class="text-gray-9">85% </span> of Employees </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="col-xl-5 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                            <h5>Leave Details</h5>
                            <div class="dropdown">
                                <a href="javascript:void(0);"
                                    class="btn btn-white border btn-sm d-inline-flex align-items-center"
                                    id="yearDropdownToggle" data-bs-toggle="dropdown">
                                    <i class="ti ti-calendar me-1"></i>
                                    <span id="selectedYear">Year</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end p-3" id="yearDropdownMenu">
                                    <!-- JS will insert years here -->
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <p class="d-flex align-items-center">
                                        <i class="ti ti-circle-filled fs-8 text-dark me-1"></i>
                                        <span class="text-gray-9 fw-semibold me-1" id="onTimeCount">0</span>
                                        on time
                                    </p>
                                    <p class="d-flex align-items-center">
                                        <i class="ti ti-circle-filled fs-8 text-success me-1"></i>
                                        <span class="text-gray-9 fw-semibold me-1" id="lateCount">0</span>
                                        Late Attendance
                                    </p>
                                    <p class="d-flex align-items-center">
                                        <i class="ti ti-circle-filled fs-8 text-primary me-1"></i>
                                        <span class="text-gray-9 fw-semibold me-1" id="wfhCount">0</span>
                                        Work From Home
                                    </p>
                                    <p class="d-flex align-items-center">
                                        <i class="ti ti-circle-filled fs-8 text-danger me-1"></i>
                                        <span class="text-gray-9 fw-semibold me-1" id="absentCount">0</span>
                                        Absent
                                    </p>
                                    <p class="d-flex align-items-center">
                                        <i class="ti ti-circle-filled fs-8 text-warning me-1"></i>
                                        <span class="text-gray-9 fw-semibold me-1" id="leaveCount">0</span>
                                        Sick Leave
                                    </p>
                                    <p class="d-flex align-items-center">
                                        <i class="ti ti-circle-filled fs-8 text-info me-1"></i>
                                        <span class="text-gray-9 fw-semibold me-1" id="appliedCount">0</span>
                                        Applied
                                    </p>
                                    <p class="d-flex align-items-center">
                                        <i class="ti ti-circle-filled fs-8 text-success me-1"></i>
                                        <span class="text-gray-9 fw-semibold me-1" id="approvedCount">0</span>
                                        Approved
                                    </p>
                                    <p class="d-flex align-items-center">
                                        <i class="ti ti-circle-filled fs-8 text-danger me-1"></i>
                                        <span class="text-gray-9 fw-semibold me-1" id="rejectedCount">0</span>
                                        Rejected
                                    </p>
                                    <p class="d-flex align-items-center">
                                        <i class="ti ti-circle-filled fs-8 text-warning me-1"></i>
                                        <span class="text-gray-9 fw-semibold me-1" id="pendingCount">0</span>
                                        Pending
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 d-flex justify-content-md-end">
                                    <div id="leaves_chart"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="todo1" />
                                    <label class="form-check-label" for="todo1">
                                        Better than <span class="text-gray-9">85%</span> of Employees
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                            <h5>Leave Details </h5>
                            <div class="dropdown">
                                <a href="javascript:void(0);"
                                    class="btn btn-white border btn-sm d-inline-flex align-items-center"
                                    data-bs-toggle="dropdown" id="leaveSelectedYear">
                                    <i class="ti ti-calendar me-1"></i><span>2024</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end p-3" id="leaveYearDropdownMenu"></ul>

                            </div>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <span class="d-block mb-1">Total Leaves </span>
                                    <h4 id="totalProratedLeaveDays">0</h4>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <span class="d-block mb-1">Taken </span>
                                    <h4 id="leaveRequestsApproved">0</h4>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <span class="d-block mb-1">Absent </span>
                                    <h4 id="totalAbsent">0</h4>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <span class="d-block mb-1">Request </span>
                                    <h4 id="leaveRequestsPending">0</h4>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <span class="d-block mb-1">Worked Days </span>
                                    <h4 id="daysActiveInYear">0</h4>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <span class="d-block mb-1">Loss of Pay </span>
                                    <h4 id="lossOfPay">0</h4>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div>
                                    <a href="#" class="btn btn-dark w-100" data-bs-toggle="modal"
                                        data-bs-target="#add_leaves">Apply New Leave </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-4 d-flex">
                <div class="card flex-fill border-primary attendance-bg">
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
                                class="btn btn-warning d-none mt-2 tea-break btn w-100 d-none" data-status="start">
                                Loading...
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8 d-flex">
                <div class="row flex-fill">
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="border-bottom mb-3 pb-2">
                                    <span class="avatar avatar-sm bg-primary mb-2">
                                        <i class="ti ti-clock-stop"></i>
                                    </span>
                                    <h3 class="mb-2">
                                        <span id="production_today_hours">0</span> /
                                        <span class="fs-20 text-gray-5" id="total_today_hours_emp">0</span>
                                    </h3>
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

                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="border-bottom mb-3 pb-2">
                                    <span class="avatar avatar-sm bg-dark mb-2">
                                        <i class="ti ti-clock-up"></i>
                                    </span>
                                    <h3 class="mb-2">
                                        <span id="weekly_production_hours">0</span> /
                                        <span class="fs-20 text-gray-5" id="weeklyTotalTime">0</span>
                                    </h3>
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

                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="border-bottom mb-3 pb-2">
                                    <span class="avatar avatar-sm bg-info mb-2">
                                        <i class="ti ti-calendar-up"></i>
                                    </span>
                                    <h3 class="mb-2">
                                        <span id="monthly_production_hours">0</span> /
                                        <span class="fs-20 text-gray-5" id="monthlyTotalTime">0</span>
                                    </h3>
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

                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="border-bottom mb-3 pb-2">
                                    <span class="avatar avatar-sm bg-pink mb-2"><i
                                            class="ti ti-calendar-star"></i></span>
                                    {{-- <h2 class="mb-2" id="monthly_production_overtime" >16 / <span class="fs-20 text-gray-5" id="getOverTimeData"> 28 </span></h2> --}}
                                    <h3 class="mb-2">
                                        <span id="monthly_production_overtime">16</span> /
                                        <span class="fs-20 text-gray-5" id="getOverTimeData">28</span>
                                    </h3>


                                    <p class="fw-medium text-truncate">Overtime this Month </p>
                                </div>
                                <div>
                                    <p class="d-flex align-items-center fs-13 text-truncate">
                                        <span class="avatar avatar-xs rounded-circle bg-danger flex-shrink-0 me-2">
                                            <i class="ti ti-arrow-down fs-12"></i>
                                        </span>
                                        <span>6% Last Month </span>
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
                                        <div class="mb-4">
                                            <p class="d-flex align-items-center mb-1"><i
                                                    class="ti ti-point-filled text-dark-transparent me-1"></i>Total
                                                Working hours </p>
                                            <h3 id="totalWorkingHours">--:-- </h3>
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="mb-4">
                                            <p class="d-flex align-items-center mb-1"><i
                                                    class="ti ti-point-filled text-success me-1"></i>Productive Hours
                                            </p>
                                            <h3 id="productiveHours">--:-- </h3>
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="mb-4">
                                            <p class="d-flex align-items-center mb-1"><i
                                                    class="ti ti-point-filled text-warning me-1"></i>Break hours </p>
                                            <h3 id="breakHours">--:-- </h3>
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="mb-4">
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
        <!--<div class="row">
            <div class="col-xl-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                            <h5>Projects </h5>
                            <div class="dropdown">
                                <a href="javascript:void(0);"
                                    class="btn btn-white border-0 dropdown-toggle border btn-sm d-inline-flex align-items-center"
                                    data-bs-toggle="dropdown">
                                    Ongoing Projects
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end p-3">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">All Projects
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Ongoing Projects
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4 shadow-none mb-md-0">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <h6>Office Management </h6>
                                            <div class="dropdown">
                                                <a href="javascript:void(0);" class="d-inline-flex align-items-center"
                                                    data-bs-toggle="dropdown">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end p-3">
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item rounded-1"
                                                            data-bs-toggle="modal" data-bs-target="#edit_task"><i
                                                                class="ti ti-edit me-2"></i>Edit </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item rounded-1"
                                                            data-bs-toggle="modal" data-bs-target="#delete_modal"><i
                                                                class="ti ti-trash me-2"></i>Delete </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center mb-3">
                                                <a href="javascript:void(0);" class="avatar">
                                                    <img src="" class="img-fluid rounded-circle"
                                                        alt="img" />
                                                </a>
                                                <div class="ms-2">
                                                    <h6 class="fw-normal"><a href="javascript:void(0);">Anthony Lewis
                                                        </a></h6>
                                                    <span class="fs-13 d-block">Project Leader </span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center mb-3">
                                                <a href="javascript:void(0);"
                                                    class="avatar bg-soft-primary rounded-circle">
                                                    <i class="ti ti-calendar text-primary fs-16"></i>
                                                </a>
                                                <div class="ms-2">
                                                    <h6 class="fw-normal">14 Jan 2024 </h6>
                                                    <span class="fs-13 d-block">Deadline </span>
                                                </div>
                                            </div>
                                            <div
                                                class="d-flex align-items-center justify-content-between bg-transparent-light border border-dashed rounded p-2 mb-3">
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="avatar avatar-sm bg-success-transparent rounded-circle me-1"><i
                                                            class="ti ti-checklist fs-16"></i></span>
                                                    <p>Tasks : <span class="text-gray-9">6 </span> /10 </p>
                                                </div>
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                    <a class="avatar bg-primary avatar-rounded text-fixed-white fs-12 fw-medium"
                                                        href="javascript:void(0);">
                                                        +2
                                                    </a>
                                                </div>
                                            </div>
                                            <div
                                                class="bg-soft-secondary p-2 rounded d-flex align-items-center justify-content-between">
                                                <p class="text-secondary mb-0 text-truncate">Time Spent </p>
                                                <h5 class="text-secondary text-truncate">65/120 <span
                                                        class="fs-14 fw-normal">Hrs </span></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card shadow-none mb-0">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <h6>Office Management </h6>
                                            <div class="dropdown">
                                                <a href="javascript:void(0);" class="d-inline-flex align-items-center"
                                                    data-bs-toggle="dropdown">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end p-3">
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item rounded-1"
                                                            data-bs-toggle="modal" data-bs-target="#edit_task"><i
                                                                class="ti ti-edit me-2"></i>Edit </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item rounded-1"
                                                            data-bs-toggle="modal" data-bs-target="#delete_modal"><i
                                                                class="ti ti-trash me-2"></i>Delete </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center mb-3">
                                                <a href="javascript:void(0);" class="avatar">
                                                    <img src="" class="img-fluid rounded-circle"
                                                        alt="img" />
                                                </a>
                                                <div class="ms-2">
                                                    <h6 class="fw-normal"><a href="javascript:void(0);">Anthony Lewis
                                                        </a></h6>
                                                    <span class="fs-13 d-block">Project Leader </span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center mb-3">
                                                <a href="javascript:void(0);"
                                                    class="avatar bg-soft-primary rounded-circle">
                                                    <i class="ti ti-calendar text-primary fs-16"></i>
                                                </a>
                                                <div class="ms-2">
                                                    <h6 class="fw-normal">14 Jan 2024 </h6>
                                                    <span class="fs-13 d-block">Deadline </span>
                                                </div>
                                            </div>
                                            <div
                                                class="d-flex align-items-center justify-content-between bg-transparent-light border border-dashed rounded p-2 mb-3">
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="avatar avatar-sm bg-success-transparent rounded-circle me-1"><i
                                                            class="ti ti-checklist fs-16"></i></span>
                                                    <p>Tasks : <span class="text-gray-9">6 </span> /10 </p>
                                                </div>
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                    <a class="avatar bg-primary avatar-rounded text-fixed-white fs-12 fw-medium"
                                                        href="javascript:void(0);">
                                                        +2
                                                    </a>
                                                </div>
                                            </div>
                                            <div
                                                class="bg-soft-secondary p-2 rounded d-flex align-items-center justify-content-between">
                                                <p class="text-secondary mb-0 text-truncate">Time Spent </p>
                                                <h5 class="text-secondary text-truncate">65/120 <span
                                                        class="fs-14 fw-normal">Hrs </span></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                            <h5>Tasks </h5>
                            <div class="dropdown">
                                <a href="javascript:void(0);"
                                    class="btn btn-white border-0 dropdown-toggle border btn-sm d-inline-flex align-items-center"
                                    data-bs-toggle="dropdown">
                                    All Projects
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end p-3">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">All Projects
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Ongoing Projects
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item border rounded mb-3 p-2">
                                <div class="row align-items-center row-gap-3">
                                    <div class="col-md-8">
                                        <div class="todo-inbox-check d-flex align-items-center">
                                            <span><i class="ti ti-grid-dots me-2"></i></span>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" />
                                            </div>
                                            <span class="me-2 d-flex align-items-center rating-select"><i
                                                    class="ti ti-star-filled filled"></i></span>
                                            <div class="strike-info">
                                                <h4 class="fs-14 text-truncate">Patient appointment booking </h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div
                                            class="d-flex align-items-center justify-content-md-end flex-wrap row-gap-3">
                                            <span class="badge bg-soft-pink d-inline-flex align-items-center me-2"><i
                                                    class="fas fa-circle fs-6 me-1"></i>Onhold </span>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item border rounded mb-3 p-2">
                                <div class="row align-items-center row-gap-3">
                                    <div class="col-md-8">
                                        <div class="todo-inbox-check d-flex align-items-center">
                                            <span><i class="ti ti-grid-dots me-2"></i></span>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" />
                                            </div>
                                            <span class="me-2 rating-select d-flex align-items-center"><i
                                                    class="ti ti-star"></i></span>
                                            <div class="strike-info">
                                                <h4 class="fs-14 text-truncate">Appointment booking with payment </h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div
                                            class="d-flex align-items-center justify-content-md-end flex-wrap row-gap-3">
                                            <span class="badge bg-transparent-purple d-flex align-items-center me-2"><i
                                                    class="fas fa-circle fs-6 me-1"></i>Inprogress </span>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item border rounded mb-3 p-2">
                                <div class="row align-items-center row-gap-3">
                                    <div class="col-md-8">
                                        <div class="todo-inbox-check d-flex align-items-center">
                                            <span><i class="ti ti-grid-dots me-2"></i></span>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" />
                                            </div>
                                            <span class="me-2 rating-select d-flex align-items-center"><i
                                                    class="ti ti-star"></i></span>
                                            <div class="strike-info">
                                                <h4 class="fs-14 text-truncate">Patient and Doctor video ____________
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div
                                            class="d-flex align-items-center justify-content-md-end flex-wrap row-gap-3">
                                            <span class="badge badge-soft-success align-items-center me-2"><i
                                                    class="fas fa-circle fs-6 me-1"></i>Completed </span>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item border rounded mb-3 p-2">
                                <div class="row align-items-center row-gap-3">
                                    <div class="col-md-8">
                                        <div class="todo-inbox-check d-flex align-items-center todo-strike-content">
                                            <span><i class="ti ti-grid-dots me-2"></i></span>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" checked="" />
                                            </div>
                                            <span class="me-2 rating-select d-flex align-items-center"><i
                                                    class="ti ti-star"></i></span>
                                            <div class="strike-info">
                                                <h4 class="fs-14 text-truncate">Private chat module </h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div
                                            class="d-flex align-items-center justify-content-md-end flex-wrap row-gap-3">
                                            <span
                                                class="badge badge-secondary-transparent d-flex align-items-center me-2"><i
                                                    class="fas fa-circle fs-6 me-1"></i>Pending </span>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item border rounded p-2">
                                <div class="row align-items-center row-gap-3">
                                    <div class="col-md-8">
                                        <div class="todo-inbox-check d-flex align-items-center">
                                            <span><i class="ti ti-grid-dots me-2"></i></span>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" />
                                            </div>
                                            <span class="me-2 rating-select d-flex align-items-center"><i
                                                    class="ti ti-star"></i></span>
                                            <div class="strike-info">
                                                <h4 class="fs-14 text-truncate">Go-Live and Post-Implementation Support
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div
                                            class="d-flex align-items-center justify-content-md-end flex-wrap row-gap-3">
                                            <span class="badge bg-transparent-purple d-flex align-items-center me-2"><i
                                                    class="fas fa-circle fs-6 me-1"></i>Inprogress </span>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src=""
                                                            alt="img" />
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <div class="row">
            <!-- <div class="col-xl-5 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                            <h5>Performance </h5>
                            <div class="dropdown">
                                <a href="javascript:void(0);"
                                    class="btn btn-white border btn-sm d-inline-flex align-items-center"
                                    data-bs-toggle="dropdown">
                                    <i class="ti ti-calendar me-1"></i>2024
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end p-3">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">2024 </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">2023 </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">2022 </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div>
                            <div class="bg-light d-flex align-items-center rounded p-2">
                                <h3 class="me-2">98% </h3>
                                <span class="badge badge-outline-success bg-success-transparent rounded-pill me-1">12%
                                </span>
                                <span>vs last years </span>
                            </div>
                            <div id="performance_chart2"></div>
                        </div>
                    </div>
                </div>
            </div> -->
            <!--<div class="col-xl-4 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                            <h5>My Skills </h5>
                            <div class="dropdown">
                                <a href="javascript:void(0);"
                                    class="btn btn-white border btn-sm d-inline-flex align-items-center"
                                    data-bs-toggle="dropdown">
                                    <i class="ti ti-calendar me-1"></i>2024
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end p-3">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">2024 </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">2023 </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">2022 </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div>
                            <div class="border border-dashed bg-transparent-light rounded p-2 mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="d-block border border-2 h-12 border-primary rounded-5 me-2"></span>
                                        <div>
                                            <h6 class="fw-medium mb-1">Figma </h6>
                                            <p>Updated : 15 May 2025 </p>
                                        </div>
                                    </div>
                                    <div class="circle-progress circle-progress-md" data-value='95'>
                                        <span class="progress-left">
                                            <span class="progress-bar border-primary"></span>
                                        </span>
                                        <span class="progress-right">
                                            <span class="progress-bar border-primary"></span>
                                        </span>
                                        <div class="progress-value">95% </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border border-dashed bg-transparent-light rounded p-2 mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="d-block border border-2 h-12 border-success rounded-5 me-2"></span>
                                        <div>
                                            <h6 class="fw-medium mb-1">HTML </h6>
                                            <p>Updated : 12 May 2025 </p>
                                        </div>
                                    </div>
                                    <div class="circle-progress circle-progress-md" data-value='85'>
                                        <span class="progress-left">
                                            <span class="progress-bar border-success"></span>
                                        </span>
                                        <span class="progress-right">
                                            <span class="progress-bar border-success"></span>
                                        </span>
                                        <div class="progress-value">85% </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border border-dashed bg-transparent-light rounded p-2 mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <span class="d-block border border-2 h-12 border-purple rounded-5 me-2"></span>
                                        <div>
                                            <h6 class="fw-medium mb-1">CSS </h6>
                                            <p>Updated : 12 May 2025 </p>
                                        </div>
                                    </div>
                                    <div class="circle-progress circle-progress-md" data-value='70'>
                                        <span class="progress-left">
                                            <span class="progress-bar border-purple"></span>
                                        </span>
                                        <span class="progress-right">
                                            <span class="progress-bar border-purple"></span>
                                        </span>
                                        <div class="progress-value">70% </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border border-dashed bg-transparent-light rounded p-2 mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <span class="d-block border border-2 h-12 border-info rounded-5 me-2"></span>
                                        <div>
                                            <h6 class="fw-medium mb-1">Wordpress </h6>
                                            <p>Updated : 15 May 2025 </p>
                                        </div>
                                    </div>
                                    <div class="circle-progress circle-progress-md" data-value='61'>
                                        <span class="progress-left">
                                            <span class="progress-bar border-info"></span>
                                        </span>
                                        <span class="progress-right">
                                            <span class="progress-bar border-info"></span>
                                        </span>
                                        <div class="progress-value">61% </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border border-dashed bg-transparent-light rounded p-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <span class="d-block border border-2 h-12 border-dark rounded-5 me-2"></span>
                                        <div>
                                            <h6 class="fw-medium mb-1">Javascript </h6>
                                            <p>Updated : 13 May 2025 </p>
                                        </div>
                                    </div>
                                    <div class="circle-progress circle-progress-md" data-value='58'>
                                        <span class="progress-left">
                                            <span class="progress-bar border-dark"></span>
                                        </span>
                                        <span class="progress-right">
                                            <span class="progress-bar border-dark"></span>
                                        </span>
                                        <div class="progress-value">58% </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <!--<div class="col-xl-3 d-flex">
                <div class="flex-fill">
                    <div class="card card-bg-5 bg-dark mb-3">
                        <div class="card-body">
                            {{-- <div class="text-center">
                                <h5 class="text-white mb-4">Team Birthday </h5>
                                <span class="avatar avatar-xl avatar-rounded mb-2">
                                    <img src="" alt="Img" />
                                </span>
                                <div class="mb-3">
                                    <h6 class="text-white fw-medium mb-1">Andrew Jermia </h6>
                                    <p>IOS Developer </p>
                                </div>
                                <a href="#" class="btn btn-sm btn-primary">Send Wishes </a>
                            </div> --}}
                            <div class="text-center">
                                <h5 class="text-white mb-4">Team Birthday</h5>

                                <span class="avatar avatar-xl avatar-rounded mb-2 position-relative d-inline-flex align-items-center justify-content-center">
                                    <img id="birthdayImage" src="" alt="Img" class="d-none" />
                                    <div id="birthdayLetterAvatar"
                                        class="letter-avatar text-white d-none d-flex align-items-center justify-content-center rounded-circle border border-2"
                                        data-name=""
                                        style="width: 64px; height: 64px; font-size: 24px; font-weight: 600;">
                                    </div>
                                </span>

                                <span>
                                    <h5 id="birthdayDate" class="text-white"></h5>
                                </span>

                                <div class="mb-3">
                                    <h6 id="birthdayName" class="text-white fw-medium mb-1"></h6>
                                    <p id="birthdayDesignation" class="text-white"></p>
                                </div>

                                <a href="#" class="btn btn-sm btn-primary">Send Wishes</a>
                                <button id="openUpcomingBtn" class="btn btn-sm btn-info mt-2">Upcoming
                                    Birthdays</button>
                            </div>



                        </div>
                    </div>
                    <div class="card bg-secondary mb-3">
                        <div class="card-body d-flex align-items-center justify-content-between p-3">
                            <div>
                                <h5 class="text-white mb-1">Leave Policy </h5>
                                <p class="text-white">Last Updated : Today </p>
                            </div>
                            <a href="#" class="btn btn-white btn-sm px-3">View All </a>
                        </div>
                    </div>
                    <div class="card bg-warning">
                        <div class="card-body d-flex align-items-center justify-content-between p-3">
                            <div>
                                <h5 class="mb-1">Next Holiday </h5>
                                <p class="text-gray-9"> <span id="titleholiday">-</span>, <span
                                        id="holiday_date">-</span> </p>
                            </div>
                            <a href="{{ box_route('employee.holiday.page') }}" class="btn btn-white btn-sm px-3">View
                                All </a>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
        <div class="row">
            <div class="col-xl-4 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <h5>Team Members </h5>
                            <div>
                                <a href="#" class="btn btn-light btn-sm">View All </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="teamMembersContainer">
                        <p class="text-muted mb-0">Loading team members...</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-5 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <h5>Notifications </h5>
                            <div>
                                <a href="#" class="btn btn-light btn-sm">View All </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="notification-list">
                            @forelse($notifications as $notification)
                                <div class="d-flex align-items-start mb-4">
                                    <div class="avatar flex-shrink-0">
                                        <div class="letter-avatar avatar avatar-rounded d-flex align-items-center justify-content-center border border-2"
                                             data-name="{{ $notification->data['title'] ?? 'N' }}"
                                             style="background-color: #f3f4f6; color: #374151; width: 40px; height: 40px; font-weight: 600;">
                                            {{ strtoupper(mb_substr($notification->data['title'] ?? ($notification->data['message'] ?? 'N'), 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="ms-2">
                                        <h6 class="fs-14 fw-medium text-truncate mb-1">{{ $notification->data['message'] ?? 'New Notification' }}</h6>
                                        <p class="fs-13 mb-0">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <p class="text-muted">No new notifications</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>




            <div class="col-xl-3 d-flex">
                <div class="flex-fill">
                    <div class="card card-bg-5 bg-dark mb-3">
                        <div class="card-body">
                            {{-- <div class="text-center">
                                <h5 class="text-white mb-4">Team Birthday </h5>
                                <span class="avatar avatar-xl avatar-rounded mb-2">
                                    <img src="" alt="Img" />
                                </span>
                                <div class="mb-3">
                                    <h6 class="text-white fw-medium mb-1">Andrew Jermia </h6>
                                    <p>IOS Developer </p>
                                </div>
                                <a href="#" class="btn btn-sm btn-primary">Send Wishes </a>
                            </div> --}}
                            <div class="text-center">
                                <h5 class="text-white mb-4">Team Birthday</h5>

                                <span class="avatar avatar-xl avatar-rounded mb-2 position-relative d-inline-flex align-items-center justify-content-center">
                                    <img id="birthdayImage" src="" alt="Img" class="d-none" />
                                    <div id="birthdayLetterAvatar"
                                        class="letter-avatar text-white d-none d-flex align-items-center justify-content-center rounded-circle border border-2"
                                        data-name=""
                                        style="width: 64px; height: 64px; font-size: 24px; font-weight: 600;">
                                    </div>
                                </span>

                                <span>
                                    <h5 id="birthdayDate" class="text-white"></h5>
                                </span>

                                <div class="mb-3">
                                    <h6 id="birthdayName" class="text-white fw-medium mb-1"></h6>
                                    <p id="birthdayDesignation" class="text-white"></p>
                                </div>

                                <a href="#" class="btn btn-sm btn-primary">Send Wishes</a>
                                <button id="openUpcomingBtn" class="btn btn-sm btn-info mt-2">Upcoming
                                    Birthdays</button>
                            </div>



                        </div>
                    </div>
                    <div class="card bg-secondary mb-3">
                        <div class="card-body d-flex align-items-center justify-content-between p-3">
                            <div>
                                <h5 class="text-white mb-1">Leave Policy </h5>
                                <p class="text-white">Last Updated : Today </p>
                            </div>
                            <a href="#" class="btn btn-white btn-sm px-3">View All </a>
                        </div>
                    </div>
                    <div class="card bg-warning">
                        <div class="card-body d-flex align-items-center justify-content-between p-3">
                            <div>
                                <h5 class="mb-1">Next Holiday </h5>
                                <p class="text-gray-9"> <span id="titleholiday">-</span>, <span
                                        id="holiday_date">-</span> </p>
                            </div>
                            <a href="{{ box_route('employee.holiday.page') }}" class="btn btn-white btn-sm px-3">View
                                All </a>
                        </div>
                    </div>
                </div>
            </div> 





            <!-- <div class="col-xl-4 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                            <h5>Meetings Schedule </h5>
                            <div class="dropdown">
                                <a href="javascript:void(0);"
                                    class="btn btn-white border btn-sm d-inline-flex align-items-center"
                                    data-bs-toggle="dropdown">
                                    <i class="ti ti-calendar me-1"></i>Today
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end p-3">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Today </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">This Month </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">This Year </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body schedule-timeline">
                        <div class="d-flex align-items-start">
                            <div class="d-flex align-items-center active-time">
                                <span>09:25 AM </span>
                                <span><i class="ti ti-point-filled text-primary fs-20"></i></span>
                            </div>
                            <div class="flex-fill ps-3 pb-4 timeline-flow">
                                <div class="bg-light p-2 rounded">
                                    <p class="fw-medium text-gray-9 mb-1">Marketing Strategy Presentation </p>
                                    <span>Marketing </span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <div class="d-flex align-items-center active-time">
                                <span>09:20 AM </span>
                                <span><i class="ti ti-point-filled text-secondary fs-20"></i></span>
                            </div>
                            <div class="flex-fill ps-3 pb-4 timeline-flow">
                                <div class="bg-light p-2 rounded">
                                    <p class="fw-medium text-gray-9 mb-1">Design Review Hospital, doctors __________
                                        Project </p>
                                    <span>Review </span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <div class="d-flex align-items-center active-time">
                                <span>09:18 AM </span>
                                <span><i class="ti ti-point-filled text-warning fs-20"></i></span>
                            </div>
                            <div class="flex-fill ps-3 pb-4 timeline-flow">
                                <div class="bg-light p-2 rounded">
                                    <p class="fw-medium text-gray-9 mb-1">Birthday Celebration of Employee </p>
                                    <span>Celebration </span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <div class="d-flex align-items-center active-time">
                                <span>09:10 AM </span>
                                <span><i class="ti ti-point-filled text-success fs-20"></i></span>
                            </div>
                            <div class="flex-fill ps-3 timeline-flow">
                                <div class="bg-light p-2 rounded">
                                    <p class="fw-medium text-gray-9 mb-1">Update of Project Flow </p>
                                    <span>Development </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>-->
        </div>
    </div>

    

</div>
<!-- /Page Wrapper -->


<!-- Upcoming Birthdays Modal -->
<!-- Modal -->
<div class="modal fade" id="upcomingBirthdayModal" tabindex="-1" aria-labelledby="upcomingBirthdayModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-md">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white rounded-top px-4 py-3">
                <h5 class="modal-title" id="upcomingBirthdayModalLabel">Upcoming Birthdays</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <ul id="birthdayList" class="list-group list-group-flush">
                    <!-- Static list items inserted by jQuery -->
                </ul>
            </div>
        </div>
    </div>
</div>



<!-- Add Leaves -->
@include('frontend.attendance.partials.add_leave_modal')
<!-- /Add Leaves -->

<!-- Edit Task -->
<div class="modal fade" id="edit_task">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Task </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form action="employee-dashboard.html">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Todo Title </label>
                                <input type="text" class="form-control" value="Patient appointment booking" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Due Date </label>
                                <div class="input-icon-end position-relative">
                                    <input type="text" class="form-control datetimepicker"
                                        placeholder="dd/mm/yyyy" />
                                    <span class="input-icon-addon">
                                        <i class="ti ti-calendar text-gray-7"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Project </label>
                                <select class="select">
                                    <option />Select
                                    <option selected="" />Office Management
                                    <option />Clinic Management
                                    <option />Educational Platform
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label me-2">Team Members </label>
                                <input class="input-tags form-control" placeholder="Add new" type="text"
                                    data-role="tagsinput" name="Label" value="Jerald,Andrew,Philip,Davis" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tag </label>
                                <select class="select">
                                    <option />Select
                                    <option />Internal
                                    <option selected="" />Projects
                                    <option />Meetings
                                    <option />Reminder
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status </label>
                                <select class="select">
                                    <option />Select
                                    <option selected="" />Inprogress
                                    <option />Completed
                                    <option />Pending
                                    <option />Onhold
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Priority </label>
                                <select class="select">
                                    <option />Select
                                    <option selected="" />Medium
                                    <option />High
                                    <option />Low
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Who Can See this ____? </label>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="flexRadioDefault"
                                        id="flexRadioDefault4" />
                                    <label class="form-check-label text-dark" for="flexRadioDefault4">
                                        Public
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="flexRadioDefault"
                                        id="flexRadioDefault5" checked="" />
                                    <label class="form-check-label text-dark" for="flexRadioDefault5">
                                        Private
                                    </label>
                                </div>
                                <div class="form-check ">
                                    <input class="form-check-input" type="radio" name="flexRadioDefault"
                                        id="flexRadioDefault6" />
                                    <label class="form-check-label text-dark" for="flexRadioDefault6">
                                        Admin Only
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Descriptions </label>
                                <div class="summernote"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Upload Attachment </label>
                            <div class="bg-light rounded p-2">
                                <div class="profile-uploader border-bottom mb-2 pb-2">
                                    <div class="drag-upload-btn btn btn-sm btn-white border px-3">
                                        Select File
                                        <input type="file" class="form-control image-sign" multiple="" />
                                    </div>
                                </div>
                                <div
                                    class="d-flex align-items-center justify-content-between border-bottom mb-2 pb-2">
                                    <div class="d-flex align-items-center">
                                        <h6 class="fs-12 fw-medium me-1">Logo.zip </h6>
                                        <span class="badge badge-soft-info">21MB </span>
                                    </div>
                                    <a href="#" class="btn btn-sm btn-icon"><i class="ti ti-trash"></i></a>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <h6 class="fs-12 fw-medium me-1">Files.zip </h6>
                                        <span class="badge badge-soft-info">25MB </span>
                                    </div>
                                    <a href="#" class="btn btn-sm btn-icon"><i class="ti ti-trash"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel </button>
                    <button type="submit" class="btn btn-primary">Save </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Edit Task -->

<!-- Delete Modal -->
<div class="modal fade" id="delete_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1">Confirm Delete </h4>
                <p class="mb-3">You want to delete ___ the marked items, this ____ be undone once you ______. </p>
                <div class="d-flex justify-content-center">
                    <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel </a>
                    <a href="employee-dashboard.html" class="btn btn-danger">Yes, Delete </a>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Unpaid Leave Confirmation Modal -->
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
                <button type="button" id="confirmUnpaidLeave" class="btn btn-primary">Proceed as Unpaid
                    Leave</button>
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

            const leaveDropdownMenu = $('#leaveYearDropdownMenu');
            const leaveSelectedYearElement = $('#leaveSelectedYear span');
            const currentYear = new Date().getFullYear();
            const startYear = 2020;

            // Generate year dropdown for leave section
            function generateLeaveYearDropdown(selectedYear) {
                leaveDropdownMenu.empty();
                for (let y = currentYear + 1; y >= startYear; y--) {
                    const isActive = (y === selectedYear) ? 'fw-bold text-primary' : '';
                    leaveDropdownMenu.append(`
            <li>
                <a href="javascript:void(0);" class="dropdown-item rounded-1 ${isActive}" data-year="${y}">
                    ${y}
                </a>
            </li>
        `);
                }
            }

            const staticBirthdays = [{
                    name: "Alice Johnson",
                    date: "12-11-2025",
                    designation: "Marketing Manager",
                    image: "https://randomuser.me/api/portraits/women/44.jpg"
                },
                {
                    name: "Bob Smith",
                    date: "15-11-2025",
                    designation: "Software Engineer",
                    image: "https://randomuser.me/api/portraits/men/45.jpg"
                },
                {
                    name: "Charlie Lee",
                    date: "20-11-2025",
                    designation: "HR Specialist",
                    image: "https://randomuser.me/api/portraits/men/46.jpg"
                },
                {
                    name: "Diana Prince",
                    date: "25-11-2025",
                    designation: "Product Designer",
                    image: "https://randomuser.me/api/portraits/women/47.jpg"
                },
                {
                    name: "Evan Davis",
                    date: "28-11-2025",
                    designation: "Sales Executive",
                    image: "https://randomuser.me/api/portraits/men/48.jpg"
                }
            ];

            $('#openUpcomingBtn').on('click', function() {
                $('#birthdayList').empty();
                $('#birthdayList').append('<li class="list-group-item">Loading...</li>');

                $.ajax({
                    url: '{{ route('get.upcoming.birthday', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'GET',
                    success: function(response) {
                        $('#birthdayList').empty();

                        const birthdays = response.data; // access the data array

                        if (!birthdays || birthdays.length === 0) {
                            $('#birthdayList').append(
                                '<li class="list-group-item">No upcoming birthdays found.</li>'
                                );
                            return;
                        }

                        birthdays.forEach(function(birthday) {
                            const hasImage = birthday.image && birthday.image !==
                                '/default-avatar.png';
                            const firstLetter = birthday.name ? birthday.name.charAt(0)
                                .toUpperCase() : '?';
                            const bgColor = getColorFromName(birthday.name || '');

                            const listItem = `
        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center">
                ${hasImage
                    ? `<img src="${birthday.image}" alt="${birthday.name}" class="rounded-circle me-3" style="width:48px; height:48px; object-fit:cover; border: 2px solid #0d6efd;">`
                    : `<div class="rounded-circle d-flex align-items-center justify-content-center me-3 text-white fw-bold" style="width:48px; height:48px; background-color:${bgColor}; border: 2px solid #0d6efd;">${firstLetter}</div>`
                }
                <div>
                    <strong class="text-primary">${birthday.name}</strong><br>
                    <small class="text-muted">${birthday.designation || 'No designation'}</small>
                </div>
            </div>
            <span class="badge rounded-pill bg-info text-dark px-3 py-2 fs-6">${birthday.date}</span>
        </li>
    `;

                            $('#birthdayList').append(listItem);
                        });
                    },
                    error: function(xhr, status, error) {
                        $('#birthdayList').empty();
                        $('#birthdayList').append(
                            '<li class="list-group-item text-danger">Failed to load birthdays.</li>'
                            );
                        console.error(error);
                    }
                });

                $('#upcomingBirthdayModal').modal('show');
            });

            // Load Team Members (latest 6) via AJAX for dashboard widget
            function loadTeamMembers() {
                const $container = $('#teamMembersContainer');
                if (!$container.length) return;

                $container.html('<p class="text-muted mb-0">Loading team members...</p>');

                $.ajax({
                    url: '{{ route('employee.dashboard.teamMembers', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        if (!res.status || !Array.isArray(res.data) || res.data.length === 0) {
                            $container.html('<p class="text-muted mb-0">No team members to display.</p>');
                            return;
                        }

                        let html = '';
                        res.data.forEach(function(m, idx) {
                            const name = m.name || 'Unknown';
                            const initial = m.initial || (name ? name.charAt(0).toUpperCase() : 'U');
                            const designation = m.designation || 'No Designation';
                            const department = m.department ? ' • ' + m.department : '';
                            const empId = m.employee_id || ('EMP' + m.id);
                            const marginClass = idx < res.data.length - 1 ? 'mb-4' : '';
                            const hasImage = m.profile_image_path && m.profile_image_path !== '';
                            const imageUrl = hasImage ? `{{ asset('storage') }}/${m.profile_image_path}` : '';

                            html += `
                                <div class="d-flex align-items-center justify-content-between ${marginClass}">
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar flex-shrink-0">
                                            ${hasImage
                                                ? `<img src="${imageUrl}" alt="Employee Image" class="avatar-rounded border border-2">`
                                                : `<div class="letter-avatar avatar avatar-sm avatar-rounded d-flex align-items-center justify-content-center"
                                                        data-name="${name}"
                                                        style="color:#fff;font-size:12px;font-weight:600;">
                                                     ${initial}
                                                   </div>`
                                            }
                                        </a>
                                        <div class="ms-2">
                                            <h6 class="fs-14 fw-medium text-truncate mb-1">
                                                <a href="javascript:void(0);">${name}</a>
                                            </h6>
                                            <p class="fs-13 mb-0 text-muted">
                                                ${designation}${department}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light text-dark">
                                            ${empId}
                                        </span>
                                    </div>
                                </div>
                            `;
                        });

                        $container.html(html);
                        // Apply colors to any letter avatars we just rendered
                        assignAvatarColors();
                    },
                    error: function() {
                        $container.html('<p class="text-danger mb-0">Failed to load team members.</p>');
                    }
                });
            }

            // Initial load
            loadTeamMembers();




            // Fetch leave stats from backend and update UI
            function fetchLeaveStats(year) {
                leaveSelectedYearElement.text(year);

                $.ajax({
                    url: "{{ route('employee.leave.stats', ['box_slug' => request()->route('box_slug')]) }}",
                    method: 'GET',
                    data: {
                        year
                    },
                    success: function(response) {
                        try {
                            if (response.data !== undefined) {
                                const data = response.data;

                                $('#totalProratedLeaveDays').text(data.total_prorated_leave_days ?? 0);
                                $('#daysActiveInYear').text(data.days_active_in_year ?? 0);
                                $('#totalAbsent').text(data.total_absent ?? 0);
                                $('#leaveRequestsPending').text(data.leave_requests?.pending ?? 0);
                                $('#leaveRequestsApproved').text(data.leave_requests?.approved ?? 0);

                                const nextHoliday = data.nextHoliday;
                                if (nextHoliday && typeof nextHoliday === 'object') {
                                    $('#titleholiday').text(nextHoliday.title ?? '-');
                                    $('#holiday_date').text(nextHoliday.date ? new Date(nextHoliday
                                        .date).toLocaleDateString() : '-');
                                } else {
                                    $('#titleholiday').text('-');
                                    $('#holiday_date').text('-');
                                }

                                const birthday = data.birthday_data;
                                if (birthday && typeof birthday === 'object') {
                                    const name = birthday.user && birthday.user.name ? birthday.user
                                        .name : 'N/A';
                                    const imageUrl = birthday.profile_image_url;

                                    $('#birthdayName').text(name);
                                    $('#birthdayDesignation').text(birthday.department_relation &&
                                        birthday.department_relation.name ? birthday
                                        .department_relation.name : 'N/A');
                                    const isoDate = birthday
                                    .birthday; // e.g., "2025-10-21T18:30:00.000000Z"
                                    const date = new Date(isoDate);

                                    // Format to dd-mm-yyyy
                                    const day = String(date.getDate()).padStart(2, '0');
                                    const month = String(date.getMonth() + 1).padStart(2,
                                    '0'); // Months are 0-indexed
                                    const year = date.getFullYear();

                                    const formattedDate = `${day}-${month}-${year}`;

                                    $('#birthdayDate').text(formattedDate);
                                    if (imageUrl) {
                                        $('#birthdayImage').attr('src', imageUrl).removeClass('d-none');
                                        $('#birthdayLetterAvatar').addClass('d-none');
                                    } else {
                                        const firstLetter = name.charAt(0).toUpperCase();
                                        const bgColor = getColorFromName(firstLetter);

                                        $('#birthdayLetterAvatar')
                                            .text(firstLetter)
                                            .attr('data-name', firstLetter)
                                            .css('background-color', bgColor)
                                            .removeClass('d-none');

                                        $('#birthdayImage').addClass('d-none');
                                    }
                                } else {
                                    $('#birthdayImage').addClass('d-none');
                                    $('#birthdayLetterAvatar').addClass('d-none');
                                    $('#birthdayName').text('No upcoming birthdays');
                                    $('#birthdayDesignation').text('');
                                }

                            } else if (response.error) {
                                showToast('Error: ' + response.error, 'warning');
                            } else {
                                showToast('Unexpected response from server.', 'warning');
                            }
                        } catch (error) {
                            console.error('Error processing leave stats response:', error);
                            showToast('An error occurred while updating leave stats.', 'warning');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Leave AJAX Error:', error);
                        showToast('Failed to load leave details.', 'warning');
                    }
                });
            }


            function getColorFromName(name) {
                const colors = [
                    "#f44336", "#e91e63", "#9c27b0", "#673ab7", "#3f51b5", "#2196f3", "#03a9f4",
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

            // Handle year dropdown click for leave section
            leaveDropdownMenu.on('click', '.dropdown-item', function() {
                const selectedYear = $(this).data('year');
                generateLeaveYearDropdown(selectedYear);
                fetchLeaveStats(selectedYear);
            });

            // Initialize leave section on page load
            generateLeaveYearDropdown(currentYear);
            fetchLeaveStats(currentYear);

            // Apply dynamic colors to any letter avatars (e.g., Report Office, birthdays)
            assignAvatarColors();



            const dropdownMenu = $('#yearDropdownMenu');
            const selectedYearElement = $('#selectedYear');
            // const startYear = 2020;
            function generateYearDropdown(selectedYear) {
                dropdownMenu.empty();
                const years = [];
                for (let y = currentYear + 1; y >= startYear; y--) {
                    years.push(y);
                }

                years.forEach(year => {
                    const isActive = (year == selectedYear) ? 'fw-bold text-primary' : '';
                    dropdownMenu.append(`
                <li>
                    <a href="javascript:void(0);" class="dropdown-item rounded-1 ${isActive}" data-year="${year}">
                        ${year}
                    </a>
                </li>
            `);
                });
            }

            function fetchDashboardStats(year) {
                selectedYearElement.text(year);

                $.ajax({
                    url: '{{ route('employess.dashboard.stats', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'GET',
                    data: {
                        year
                    },
                    success: function(response) {
                        if (response.success) {
                            const data = response.data;
                            const onTime = data.on_time ?? 0;
                            const late = data.late ?? 0;
                            const absent = data.absent ?? 0;
                            const leaves = data.approved_leaves ?? 0;
                            const wfh = data.work_from_home ?? 0;
                            const applied = data.applied_leaves ?? 0;
                            const rejected = data.rejected_leaves ?? 0;
                            const pending = data.pending_leaves ?? 0;

                            // Update text values
                            $('#onTimeCount').text(onTime);
                            $('#lateCount').text(late);
                            $('#absentCount').text(absent);
                            $('#leaveCount').text(leaves);
                            $('#wfhCount').text(wfh);
                            $('#appliedCount').text(applied);
                            $('#approvedCount').text(leaves);
                            $('#rejectedCount').text(rejected);
                            $('#pendingCount').text(pending);

                            // Update chart data
                            if (donut) {
                                donut.updateSeries([late, leaves, absent, wfh, onTime, applied, rejected, pending]);
                            }
                        } else {
                            showToast('Error: ' + response.message, 'warning');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        showToast('Failed to load dashboard stats.', 'warning');
                    }
                });
            }




            // Init
            const defaultYear = currentYear;
            generateYearDropdown(defaultYear);
            fetchDashboardStats(defaultYear);


            $('#add_leaves').on('show.bs.modal', function() {


                $.ajax({
                    url: '{{ route('leave.type.request', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log("Leave types response:", response);

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
                let url =
                    '{{ route('leave.request.store', ['box_slug' => request()->route('box_slug')]) }}';
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
                            showToast(response.message || 'Leave request submitted successfully!', 'warning');
                            $('#add_leaves').modal('hide');
                            form.trigger('reset');
                            $('#file-preview').empty();
                        } else {
                            // Backend returned status: false
                            showToast(response.message || 'Failed to submit leave request.', 'warning');
                        }
                    },
                    error: function(xhr) {
                        let res = xhr.responseJSON;

                        if (res?.errors) {
                            // Validation errors
                            let errorMessages = Object.values(res.errors).flat().join('\n');
                            showToast('Validation errors:\n' + errorMessages, 'warning');
                        } else if (res?.extra_leave) {
                            // Insufficient leave balance -> show modal
                            $('#unpaidLeaveInfo').html(`
                    Requested days: <b>${res.requested}</b><br>
                    Available balance: <b>${res.available}</b><br>
                    Difference (unpaid days): <b>${res.difference}</b><br><br>
                    Do you want to proceed as unpaid leave?
                `);

                            // Show the modal
                            let unpaidModal = new bootstrap.Modal(document.getElementById(
                                'unpaidLeaveModal'));
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
                                        'X-CSRF-TOKEN': $(
                                            'meta[name="csrf-token"]').attr(
                                            'content')
                                    },
                                    success: function(resp) {
                                        alert(resp.message ||
                                            'Leave request submitted successfully as unpaid leave!'
                                            );
                                        $('#add_leaves').modal('hide');
                                        form.trigger('reset');
                                        $('#file-preview').empty();
                                        unpaidModal.hide();
                                    },
                                    error: function(err) {
                                        alert(err.responseJSON?.message ||
                                            'Something went wrong.');
                                    }
                                });
                            });
                        } else if (res?.message) {
                            showToast(res.message, 'warning');
                        } else {
                            showToast('Something went wrong. Please try again.', 'warning');
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

            if ($('#leaves_chart').length > 0) {
                const donutChartOptions = {
                    chart: {
                        height: 255,
                        type: 'donut',
                        toolbar: {
                            show: false,
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '50%'
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    series: [0, 0, 0, 0, 0, 0, 0, 0], // Initial empty values
                    colors: ['#F26522', '#FFC107', '#E70D0D', '#03C95A', '#0C4B5E', '#17a2b8', '#dc3545', '#ffc107'],
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200
                            },
                            legend: {
                                show: false
                            }
                        }
                    }],
                    legend: {
                        show: false
                    }
                };

                // Create global chart instance
                donut = new ApexCharts(
                    document.querySelector("#leaves_chart"),
                    donutChartOptions
                );

                donut.render();
            }

            // Handle Year Change
            dropdownMenu.on('click', '.dropdown-item', function() {
                const year = $(this).data('year');
                generateYearDropdown(year);
                fetchDashboardStats(year);
            });



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
            initializeDataTable();
            fetchAttendance();

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
                // Temporarily bypass all location tracking as requested
                showToast('Location tracking bypassed. Punching in/out...', 'info');

                payload.latitude = null;
                payload.longitude = null;
                payload.location = '';

                $.post('{{ route('mark.attendace', ['box_slug' => request()->route('box_slug')]) }}',
                        payload)
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
                            $('#attendanceTable').DataTable().ajax.reload(null,
                                false); // false = stay on current page
                        }
                        fetchAttendance();
                    })
                    .fail((xhr) => {
                        let message = 'An error occurred';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                message = response.message;
                            }
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
                    showToast('Failed to update break status.', 'warning');
                    console.error(xhr.responseText);
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
            function initializeDataTable() {
                $('#attendanceTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('fetch.check.attendance', ['box_slug' => request()->route('box_slug')]) }}',
                        type: 'GET'
                    },
                    columns: [{
                            data: null,
                            name: 'serial_no',
                            orderable: false,
                            searchable: false,
                            render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart +
                                1
                        },
                        {
                            data: 'date',
                            name: 'attendance_date'
                        },
                        {
                            data: 'check_in',
                            name: 'check_in'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'check_out',
                            name: 'check_out'
                        },
                        {
                            data: 'break_duration',
                            name: 'break_minutes'
                        },
                        {
                            data: 'late',
                            name: 'late_minutes'
                        },
                        {
                            data: 'over_time',
                            name: 'overtime_minutes'
                        },
                        {
                            data: 'production_hours',
                            name: 'production_hours',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    columnDefs: [{
                        targets: [3],
                        className: 'text-center'
                    }]
                });
            }

            function startStopwatch(startTime = null) {
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
                        console.log('Stopping stopwatch after reaching max allowed seconds (handled by cron job).');
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
            // Fetch Attendance
            function fetchAttendance() {
                $.get('{{ route('check.attendance', ['box_slug' => request()->route('box_slug')]) }}')
                    .done((res) => {
                        if (!res.success) return;

                        const data = res.data;
                        const attendance = data.attendance || {};
                        const status = data.status || 'Absent';
                        const lowerStatus = status.toLowerCase();
                        const isPunchInAllowed = (
                            lowerStatus === 'absent' ||
                            lowerStatus.startsWith('holiday') ||
                            lowerStatus === 'weekly off' ||
                            lowerStatus === 'not a working day'
                        );

                        const checkInTime = attendance.check_in ?? '--';
                        const checkOutTime = attendance.check_out ?? '--';
                        const production = attendance.production_hours?.split(':').slice(0, 2).join(':') ||
                            '00:00';
                        const attendanceId = attendance.id;
                        const daily_hours = data.daily_hours;

                        // Next break info
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

                        // If today is a leave day, disable everything & hide timers
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

                        // If status is "Punch Out Miss", stop stopwatch & lock actions (handled by cron)
                        if (lowerStatus === 'punch out miss') {
                            stopStopwatch();
                            clearBreakData();
                            localStorage.removeItem('attendance_id');

                            $punchBtn
                                .text('Punch Out Miss')
                                .prop('disabled', true)
                                .removeClass('btn-success btn-danger btn-secondary d-none')
                                .addClass('btn-secondary disabled');

                            $breakBtn.addClass('d-none');

                            $('#production_hours').show().text(production);
                            $('#production_stopwatch').hide();

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
                            // Punch Out logic
                            $punchBtn
                                .text('Punch Out')
                                .removeClass('btn-success')
                                .addClass('btn-danger')
                                .data('status', 'Present')
                                .removeClass('d-none');

                            if (checkOutTime !== '--') {
                                // Already punched out (including Punch Out Miss) – hard stop timers
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

                        // Fetch summary to render bar
                        $.get(
                                '{{ route('check.attendance.summary', ['box_slug' => request()->route('box_slug')]) }}'
                                )
                            .done((res) => {
                                if (res.status) {
                                    initializeSegmentsFromHourlyChart(
                                        res.data.hourly_chart,
                                        res.data.shift_start_time,
                                        res.data.shift_end_time,
                                        res.data.intime,
                                        res.data.outTime
                                    );
                                    renderRealtimeProgressBar();
                                }
                            });
                    })
                    .fail(() => {
                        $('#attendance_result').html(
                            `<span class="text-danger">Unable to load attendance.</span>`
                        );
                    });
            }

            /**
             * Dynamic Notifications Fetcher
             */
            function fetchLatestNotifications() {
                $.get('{{ route('employee.dashboard.notifications', ['box_slug' => request()->route('box_slug')]) }}')
                    .done(function(res) {
                        if (res.status && res.data.length > 0) {
                            let html = '';
                            res.data.forEach(function(item) {
                                let initial = (item.data.title || item.data.message || 'N').charAt(0).toUpperCase();
                                html += `
                                    <div class="d-flex align-items-start mb-4">
                                        <div class="avatar flex-shrink-0">
                                            <div class="letter-avatar avatar avatar-rounded d-flex align-items-center justify-content-center border border-2" 
                                                 style="background-color: #f3f4f6; color: #374151; width: 40px; height: 40px; font-weight: 600;">
                                                ${initial}
                                            </div>
                                        </div>
                                        <div class="ms-2">
                                            <h6 class="fs-14 fw-medium text-truncate mb-1">${item.data.message || 'New Notification'}</h6>
                                            <p class="fs-13 mb-0">${item.created_at}</p>
                                        </div>
                                    </div>`;
                            });
                            $('#notification-list').html(html);
                        } else if (res.status && res.data.length === 0) {
                             $('#notification-list').html('<div class="text-center py-4"><p class="text-muted">No new notifications</p></div>');
                        }
                    })
                    .fail(function() {
                        console.error('Failed to fetch notifications');
                    });
            }

            // Fetch every 30 seconds
            setInterval(fetchLatestNotifications, 30000);
            
            // Optional: Fetch on initial load after a short delay
            setTimeout(fetchLatestNotifications, 5000);

            // Attendance Overview dynamically
            function generateOverviewYearDropdown() {
                const yearSelect = $('#overviewYearSelect');
                yearSelect.empty();
                for (let y = currentYear + 1; y >= startYear; y--) {
                    const isSelected = (y === currentYear) ? 'selected' : '';
                    yearSelect.append(`<option value="${y}" ${isSelected}>${y}</option>`);
                }
            }

            function fetchAttendanceOverview() {
                const year = $('#overviewYearSelect').val();
                const month = $('#overviewMonthSelect').val();
                
                $.get('{{ route('employess.dashboard.overview', ['box_slug' => request()->route('box_slug')]) }}', { year, month })
                    .done(function(res) {
                        if (res.success) {
                            const data = res.data;
                            $('#total_attendance').text(data.total_attendance);
                            $('#overview_present').text(data.raw_counts.present);
                            $('#overview_punchoutmiss').text(data.raw_counts.punch_out_miss);
                            $('#overview_absent').text(data.raw_counts.absent);
                            $('#overview_weeklyoff').text(data.raw_counts.weekly_off);
                            $('#overview_holiday').text(data.raw_counts.holiday);
                            $('#overview_halfday').text(data.raw_counts.half_day);
                            $('#overview_shortleave').text(data.raw_counts.short_leave);
                            
                            if (typeof attendanceChart !== 'undefined' && attendanceChart) {
                                attendanceChart.data.datasets[0].data = [
                                    data.raw_counts.present, 
                                    data.raw_counts.punch_out_miss, 
                                    data.raw_counts.absent, 
                                    data.raw_counts.weekly_off,
                                    data.raw_counts.holiday,
                                    data.raw_counts.half_day,
                                    data.raw_counts.short_leave
                                ];
                                attendanceChart.update();
                            }
                        }
                    })
                    .fail(function() {
                        console.error('Failed to fetch attendance overview');
                    });
            }

            generateOverviewYearDropdown();
            const currentMonthStr = String(new Date().getMonth() + 1).padStart(2, '0');
            $('#overviewMonthSelect').val(currentMonthStr);
            
            // Wait slightly for the chart to initialize if it's below
            setTimeout(fetchAttendanceOverview, 500);

            $('#overviewYearSelect, #overviewMonthSelect').on('change', fetchAttendanceOverview);

        });
    </script>


<script>
if ($('#attendance').length > 0) {
                    const ctx = document.getElementById('attendance').getContext('2d');
                    // ✅ Modern way to destroy existing chart on this canvas
                    let existingAttendanceChart = Chart.getChart("attendance");
                    if (existingAttendanceChart) {
                        existingAttendanceChart.destroy();
                    }
                    
                    attendanceChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Present', 'Punch Out Miss', 'Absent', 'Weekly Off', 'Holiday', 'Half Day', 'Short Leave'],
                            datasets: [{
                                label: 'Semi Donut',
                                data: [0, 0, 0, 0, 0, 0, 0], // Initial zero values
                                backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d', '#17a2b8', '#0d6efd', '#d63384'],
                                borderWidth: 5,
                                borderRadius: 10,
                                borderColor: '#fff',
                                hoverBorderWidth: 0,
                                cutout: '60%',
                            }]
                        },
                        options: {
                            rotation: -100,
                            circumference: 200,
                            layout: {
                                padding: {
                                    top: -20,
                                    bottom: -20
                                }
                            },
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                        }
                    });
                }
</script>
@endpush
@endsection
