@extends('layouts.main')
@section('title', 'Admin Dashboard')
@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content">
            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Admin Dashboard </h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="index.html"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                Dashboard
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Admin Dashboard </li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">

                    <div class="mb-2">
                        <div class="input-icon position-relative" style="width: 220px;">
                            <span class="input-icon-addon">
                                <i class="ti ti-calendar text-gray-9"></i>
                            </span>
                            <input type="text" class="form-control bookingrange" id="dashboardDateRange" />
                        </div>
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
            @php
                $user = Auth::user();
                $boxUser = $user->box_user_new;
                $profileImage = $boxUser?->profile_image_path;
                $name = $user->name ?? 'U';
                $initial = strtoupper(substr($name, 0, 1));
            @endphp
            <!-- Welcome Wrap -->
            <div class="card border-0">
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap pb-1">
                    <div class="d-flex align-items-center mb-3">
                        <span class="avatar avatar-xl flex-shrink-0">
                            @if ($profileImage)
                                <img src="{{ asset('storage/' . $profileImage) }}" alt="Profile"
                                    class="img-fluid rounded-circle" width="40" height="40" />
                            @else
                                <div class="letter-avatar rounded-circle text-white text-uppercase d-flex justify-content-center align-items-center"
                                    title="{{ $name }}"
                                    style="width: 40px; height: 40px; background-color: {{ getColorFromNameBladesAdmin($name) }}; font-size: 18px; font-weight: bold;">
                                    {{ $initial }}
                                </div>
                            @endif
                        </span>
                        <div class="ms-3">
                            <h3 class="mb-2">Welcome Back, {{ Auth::check() ? Auth::user()->name : 'Guest' }} <a
                                    href="javascript:void(0);" class="edit-icon"><i class="ti ti-edit fs-14"></i></a></h3>
                            <p>
                                You have
                                <a href="{{ box_route('employee.attendace.admin') }}" id="pendingCorrectionsLink"
                                    class="text-primary text-decoration-underline">
                                    <span id="pendingCorrectionsCount">0</span>
                                </a>
                                Pending Approvals &
                                <a href="{{ box_route('employee.leave.admin') }}" id="pendingLeaveRequestsLink"
                                    class="text-primary text-decoration-underline">
                                    <span id="pendingLeaveRequestsCount">0</span>
                                </a>
                                Leave Requests
                            </p>


                        </div>
                    </div>
                    <div class="d-flex align-items-center flex-wrap mb-1">
                        <a href="#" class="btn btn-info btn-md me-2 mb-2" data-bs-toggle="modal"
                            data-bs-target="#send_overall_notification"><i class="ti ti-bell-ringing me-1"></i>Send Notification </a>
                        <a href="#" class="btn btn-secondary btn-md me-2 mb-2" data-bs-toggle="modal"
                            data-bs-target="#add_project"><i class="ti ti-square-rounded-plus me-1"></i>Add Project </a>
                        <a href="#" class="btn btn-primary btn-md mb-2" data-bs-toggle="modal"
                            data-bs-target="#add_leaves"><i class="ti ti-square-rounded-plus me-1"></i>Add Requests </a>
                    </div>
                </div>
            </div>

            @php
                function getColorFromNameBladesAdmin($name)
                {
                    $colors = [
                        '#f44336',
                        '#e91e63',
                        '#9c27b0',
                        '#673ab7',
                        '#3f51b5',
                        '#2196f3',
                        '#03a9f4',
                        '#00bcd4',
                        '#009688',
                        '#4caf50',
                        '#8bc34a',
                        '#cddc39',
                        '#ffc107',
                        '#ff9800',
                        '#ff5722',
                    ];
                    $char = strtoupper(trim($name)[0] ?? 'A');
                    $index = ord($char) % count($colors);
                    return $colors[$index];
                }
            @endphp
            <!-- /Welcome Wrap -->

            <div class="row">

                <!-- Widget Info -->
                <div class="col-lg-12 d-flex">
                    <div class="row flex-fill">
                        <div class="col-md-3 col-6 d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <span class="avatar rounded-circle bg-primary mb-2">
                                        <i class="ti ti-calendar-share fs-16"></i>
                                    </span>
                                    <h6 class="fs-13 fw-medium text-default mb-1">Attendance Overview </h6>
                                    <h3 class="mb-3">
                                        <span id="present_count">0</span>/<span id="total_count">0</span>
                                        <span id="percent_change_wrapper">
                                            <span id="percent_change" class="fs-12 fw-medium text-success">
                                                <i id="percent_icon" class="fa-solid fa-caret-up me-1"></i>
                                                +0.0%
                                            </span>
                                        </span>
                                    </h3>
                                    <a href="{{ box_route('employee.attendace.admin') }}" class="link-default">View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-md-3 d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <span class="avatar rounded-circle bg-secondary mb-2">
                                        <i class="ti ti-browser fs-16"></i>
                                    </span>
                                    <h6 class="fs-13 fw-medium text-default mb-1">Total No of Project's </h6>
                                    <h3 class="mb-3">90/125 <span class="fs-12 fw-medium text-danger"><i
                                                class="fa-solid fa-caret-down me-1"></i>-2.1% </span></h3>
                                    <a href="projects.html" class="link-default">View All </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <span class="avatar rounded-circle bg-info mb-2">
                                        <i class="ti ti-users-group fs-16"></i>
                                    </span>
                                    <h6 class="fs-13 fw-medium text-default mb-1">Total No of Clients </h6>
                                    <h3 class="mb-3">69/86 <span class="fs-12 fw-medium text-danger"><i
                                                class="fa-solid fa-caret-down me-1"></i>-11.2% </span></h3>
                                    <a href="clients.html" class="link-default">View All </a>
                                </div>
                            </div>
                        </div> -->
                        <div class="col-md-3 col-6 d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="avatar rounded-circle bg-pink me-2">
                                            <i class="ti ti-checklist fs-16"></i>
                                        </span>
                                        <h6 class="fs-13 fw-medium text-default mb-0">Task Overview </h6>
                                    </div>
                                    <h3 class="mb-3">
                                        <a href="{{ box_route('reports.ReminderList') }}?status=Open" class="text-success d-inline-block me-1" title="Open Tasks">
                                            <span id="open_tasks_widget_top">0</span>
                                        </a>
                                        <span class="text-muted">/</span>
                                        <a href="{{ box_route('reports.AllDSR') }}?status=Closed" class="text-danger d-inline-block ms-1" title="Closed Tasks">
                                            <span id="closed_tasks_widget_top">0</span>
                                        </a>
                                        <span id="tasks_percentage_widget" class="fs-12 fw-medium text-success ms-2">
                                            <i class="fa-solid fa-caret-up me-1"></i>0%
                                        </span>
                                    </h3>
                                    <a href="{{ box_route('reports.ReminderList') }}" class="link-default">View All </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="avatar rounded-circle bg-warning me-2">
                                            <i class="ti ti-checklist fs-16"></i>
                                        </span>
                                        <h6 class="fs-13 fw-medium text-default mb-0">Open Task </h6>
                                    </div>
                                    <h3 class="mb-2"><span id="open_tasks_widget">0</span></h3>
                                    <div class="progress progress-xs mb-3" style="height: 4px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%"></div>
                                    </div>
                                    <a href="{{ box_route('reports.ReminderList') }}?status=Open" class="link-default text-warning">View Open </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-6 d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <span class="avatar rounded-circle bg-success mb-2">
                                        <i class="ti ti-users-group fs-16"></i>
                                    </span>
                                    <h6 class="fs-13 fw-medium text-default mb-1">Job Applicants </h6>
                                    <h3 class="mb-3">{{ $jobApplicantsCount ?? 0 }} <span class="fs-12 fw-medium text-success"><i
                                                class="fa-solid fa-caret-up me-1"></i>+0.0% </span></h3>
                                    <a href="{{ box_route('hr.job.applicants.index') }}" class="link-default">View All </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <!-- ALL TIME TOTAL TASKS -->
                <div class="col-lg-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header border-bottom-0 pb-0 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="avatar rounded-circle bg-danger me-2">
                                    <i class="ti ti-checklist fs-16 text-white"></i>
                                </span>
                                <h6 class="fs-13 fw-medium text-default mb-0">Total Tasks</h6>
                            </div>
                            <div class="dropdown">
                                <a href="#" class="text-muted" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-filter"></i></a>
                                <div class="dropdown-menu dropdown-menu-end p-2" style="width: 250px;">
                                    <div class="mb-2">
                                        <label class="form-label fs-12">User</label>
                                        <select class="form-select form-select-sm" id="widgetUserFilter">
                                            <option value="">All Users</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fs-12">Date Range</label>
                                        <input type="text" class="form-control form-control-sm" id="widgetDateFilter" placeholder="Select Date Range">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0 mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fs-12 fw-medium text-muted">Open Tasks (<span id="widget_open_tasks_text">0</span>)</span>
                                <span class="fs-12 text-muted" id="widget_open_tasks_percent">0%</span>
                            </div>
                            <div class="progress progress-sm mb-3">
                                <div class="progress-bar bg-success" id="widget_open_tasks_bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fs-12 fw-medium text-muted">Closed Tasks (<span id="widget_closed_tasks_text">0</span>)</span>
                                <span class="fs-12 text-muted" id="widget_closed_tasks_percent">0%</span>
                            </div>
                            <div class="progress progress-sm mb-3">
                                <div class="progress-bar bg-danger" id="widget_closed_tasks_bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-2 mt-3 pt-2 border-top">
                                <h6 class="mb-0 fs-14 fw-bold text-dark">Total Tasks</h6>
                                <h6 class="mb-0 fs-14 fw-bold text-danger" id="widget_all_time_total_tasks">0</h6>
                            </div>
                            
                            <div class="text-center mt-3">
                                <a href="{{ route('users.task.progress', ['box_slug' => request()->route('box_slug')]) }}" class="link-default fw-medium">View All Users</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /ALL TIME TOTAL TASKS -->
                <!-- /Widget Info -->

                <!-- Employees By Department -->
                <div class="col-lg-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Employees By Department </h5>
                            <div class="dropdown mb-2">
                                <a href="javascript:void(0);"
                                    class="btn btn-white border btn-sm d-inline-flex align-items-center"
                                    data-bs-toggle="dropdown">
                                    <i class="ti ti-calendar me-1"></i>This Week
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end p-3">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">This Month </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">This Week </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Last Week </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="emp-department-chart"></div>

                        </div>
                    </div>
                </div>
                <!-- /Employees By Department -->

            </div>

            <div class="row">

                <!-- Total Employee -->
                <div class="col-lg-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Employee Status </h5>
                            <div class="dropdown mb-2">
                                <a href="javascript:void(0);"
                                    class="btn btn-white border btn-sm d-inline-flex align-items-center"
                                    data-bs-toggle="dropdown">
                                    <i class="ti ti-calendar me-1"></i>This Week
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end p-3">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">This Month </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">This Week </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Today </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <p class="fs-13 mb-3">Total Employee </p>
                                <h3 class="mb-3" id="total_employees_count_stat">--</h3>
                            </div>
                            <div class="progress-stacked emp-stack mb-3" id="employee_types_progress_bar">
                                <!-- Dynamic Progress Bar -->
                            </div>
                            <div class="border mb-3">
                                <div class="row gx-0" id="employee_types_stats_grid">
                                    <!-- Dynamic Stats Grid -->
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="mb-0">Top Performer</h6>
                                <ul class="nav nav-pills nav-sm" id="adminTopPerformerTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active px-2 py-1 fs-11" id="pills-admin-this-month-tab" data-bs-toggle="pill" data-bs-target="#pills-admin-this-month" type="button" role="tab">This Month</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link px-2 py-1 fs-11" id="pills-admin-last-month-tab" data-bs-toggle="pill" data-bs-target="#pills-admin-last-month" type="button" role="tab">Last Month</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content mb-3" id="adminTopPerformerTabContent">
                                <!-- This Month Tab -->
                                <div class="tab-pane fade show active" id="pills-admin-this-month" role="tabpanel">
                                    <div class="p-2 d-flex align-items-center justify-content-between border border-primary bg-primary-100 br-5" id="top_performer_container">
                                        <div class="d-flex align-items-center overflow-hidden">
                                            <span class="me-2">
                                                <i class="ti ti-award-filled text-primary fs-24"></i>
                                            </span>
                                            <a href="javascript:void(0);" class="avatar avatar-md me-2">
                                                <img src="https://ui-avatars.com/api/?name=User&background=random" id="top_performer_avatar" class="rounded-circle border border-white" alt="img" />
                                            </a>
                                            <div class="overflow-hidden">
                                                <h6 class="text-truncate mb-1 fs-14 fw-medium"><a href="javascript:void(0);" id="top_performer_name">Loading...</a></h6>
                                                <p class="fs-13 mb-0 text-truncate" id="top_performer_role">-</p>
                                            </div>
                                        </div>
                                        <div class="text-end ps-2">
                                            <p class="fs-13 mb-1">Performance</p>
                                            <h5 class="text-primary mb-0" id="top_performer_score">0%</h5>
                                        </div>
                                    </div>
                                </div>
                                <!-- Last Month Tab -->
                                <div class="tab-pane fade" id="pills-admin-last-month" role="tabpanel">
                                    <div class="p-2 d-flex align-items-center justify-content-between border border-warning bg-warning-100 br-5" id="lm_top_performer_container">
                                        <div class="d-flex align-items-center overflow-hidden">
                                            <span class="me-2">
                                                <i class="ti ti-medal text-warning fs-24"></i>
                                            </span>
                                            <a href="javascript:void(0);" class="avatar avatar-md me-2">
                                                <img src="https://ui-avatars.com/api/?name=User&background=random" id="lm_top_performer_avatar" class="rounded-circle border border-white" alt="img" />
                                            </a>
                                            <div class="overflow-hidden">
                                                <h6 class="text-truncate mb-1 fs-14 fw-medium"><a href="javascript:void(0);" id="lm_top_performer_name">Loading...</a></h6>
                                                <p class="fs-13 mb-0 text-truncate" id="lm_top_performer_role">Previous Month Star</p>
                                            </div>
                                        </div>
                                        <div class="text-end ps-2">
                                            <p class="fs-13 mb-1 text-warning">Status</p>
                                            <span class="badge badge-soft-warning fs-11" id="lm_top_performer_badge">Last Month</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('fieldstracking.performance-dashboard', ['box_slug' => request()->route('box_slug')]) }}" class="btn btn-light btn-md w-100">View Performance Dashboard </a>
                        </div>
                    </div>
                </div>
                <!-- /Total Employee -->

                <!-- Attendance Overview -->
                <div class="col-lg-4 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Attendance Overview </h5>
                            <div class="dropdown mb-2">
                                <a href="javascript:void(0);"
                                    class="btn btn-white border btn-sm d-inline-flex align-items-center"
                                    data-bs-toggle="dropdown">
                                    <i class="ti ti-calendar me-1"></i>Today
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end p-3">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">This Month </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">This Week </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Today </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        {{-- <div class="card-body">
                            <div class="chartjs-wrapper-demo position-relative mb-4">
                                <canvas id="attendance" height="200"></canvas>
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
                                <p id="present_percentage" class="f-13 fw-medium text-gray-9 mb-2">59%</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-secondary-2 me-1"></i>Late </p>
                                <p id="late_percentage" class="f-13 fw-medium text-gray-9 mb-2">21%</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-warning me-1"></i>Permission </p>
                                <p id="permission_percentage" class="f-13 fw-medium text-gray-9 mb-2">2%</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-danger me-1"></i>Absent </p>
                                <p id="absent_percentage" class="f-13 fw-medium text-gray-9 mb-2">15%</p>
                            </div>
                            
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-purple me-1"></i>Weekly Off </p>
                                <p class="f-13 fw-medium text-gray-9 mb-2">3%</p>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-info me-1"></i>Holiday </p>
                                <p class="f-13 fw-medium text-gray-9 mb-2">0%</p>
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

                <!-- Clock-In/Out -->
                <div class="col-lg-4 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Clock-In/Out </h5>
                            <div class="d-flex align-items-center">
                                <div class="dropdown mb-2">
                                    <a href="javascript:void(0);"
                                        class="dropdown-toggle btn btn-white btn-sm d-inline-flex align-items-center border-0 fs-13 me-2"
                                        data-bs-toggle="dropdown">
                                        All Departments
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Finance </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Development </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Marketing </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="dropdown mb-2">
                                    <a href="javascript:void(0);"
                                        class="btn btn-white border btn-sm d-inline-flex align-items-center"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-calendar me-1"></i>Today
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">This Month </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">This Week </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Today </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="attendance-cards">
                                {{-- <div
                                    class="d-flex align-items-center justify-content-between mb-3 p-2 border border-dashed br-5">
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar flex-shrink-0">
                                            <img src="" class="rounded-circle border border-2" alt="img" />
                                        </a>
                                        <div class="ms-2">
                                            <h6 class="fs-14 fw-medium text-truncate">Daniel Esbella </h6>
                                            <p class="fs-13">UI/UX Designer </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="link-default me-2"><i
                                                class="ti ti-clock-share"></i></a>
                                        <span
                                            class="fs-10 fw-medium d-inline-flex align-items-center badge badge-success"><i
                                                class="ti ti-circle-filled fs-5 me-1"></i>09:15 </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-3 p-2 border br-5">
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar flex-shrink-0">
                                            <img src="" class="rounded-circle border border-2" alt="img" />
                                        </a>
                                        <div class="ms-2">
                                            <h6 class="fs-14 fw-medium">Doglas Martini </h6>
                                            <p class="fs-13">Project Manager </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="link-default me-2"><i
                                                class="ti ti-clock-share"></i></a>
                                        <span
                                            class="fs-10 fw-medium d-inline-flex align-items-center badge badge-success"><i
                                                class="ti ti-circle-filled fs-5 me-1"></i>09:36 </span>
                                    </div>
                                </div>
                                <div class="mb-3 p-2 border br-5">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="avatar flex-shrink-0">
                                                <img src="" class="rounded-circle border border-2" alt="img" />
                                            </a>
                                            <div class="ms-2">
                                                <h6 class="fs-14 fw-medium text-truncate">Brian Villalobos </h6>
                                                <p class="fs-13">PHP Developer </p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="link-default me-2"><i
                                                    class="ti ti-clock-share"></i></a>
                                            <span
                                                class="fs-10 fw-medium d-inline-flex align-items-center badge badge-success"><i
                                                    class="ti ti-circle-filled fs-5 me-1"></i>09:15 </span>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center justify-content-between flex-wrap mt-2 border br-5 p-2 pb-0">
                                        <div>
                                            <p class="mb-1 d-inline-flex align-items-center"><i
                                                    class="ti ti-circle-filled text-success fs-5 me-1"></i>Clock In </p>
                                            <h6 class="fs-13 fw-normal mb-2">10:30 AM </h6>
                                        </div>
                                        <div>
                                            <p class="mb-1 d-inline-flex align-items-center"><i
                                                    class="ti ti-circle-filled text-danger fs-5 me-1"></i>Clock Out </p>
                                            <h6 class="fs-13 fw-normal mb-2">09:45 AM </h6>
                                        </div>
                                        <div>
                                            <p class="mb-1 d-inline-flex align-items-center"><i
                                                    class="ti ti-circle-filled text-warning fs-5 me-1"></i>Production </p>
                                            <h6 class="fs-13 fw-normal mb-2">09:21 Hrs </h6>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                            <h6 class="mb-2">Late </h6>
                            {{-- <div
                                class="d-flex align-items-center justify-content-between mb-3 p-2 border border-dashed br-5">
                                <div class="d-flex align-items-center">
                                    <span class="avatar flex-shrink-0">
                                        <img src="" class="rounded-circle border border-2" alt="img" />
                                    </span>
                                    <div class="ms-2">
                                        <h6 class="fs-14 fw-medium text-truncate">Anthony Lewis <span
                                                class="fs-10 fw-medium d-inline-flex align-items-center badge badge-success"><i
                                                    class="ti ti-clock-hour-11 me-1"></i>30 Min </span></h6>
                                        <p class="fs-13">Marketing Head </p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0);" class="link-default me-2"><i
                                            class="ti ti-clock-share"></i></a>
                                    <span class="fs-10 fw-medium d-inline-flex align-items-center badge badge-danger"><i
                                            class="ti ti-circle-filled fs-5 me-1"></i>08:35 </span>
                                </div>
                            </div> --}}
                            <div id="latest-late-employee-card"></div>

                            <a href="{{ box_route('employee.attendace.admin') }}" class="btn btn-light btn-md w-100">View
                                All Attendance </a>
                        </div>
                    </div>
                </div>
                <!-- /Clock-In/Out -->

            </div>



            









            <div class="row">

                <!-- Jobs Applicants -->
                <!-- <div class="col-lg-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Jobs Applicants </h5>
                            <a href="job-list.html" class="btn btn-light btn-md mb-2">View All </a>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs tab-style-1 nav-justified d-sm-flex d-block p-0 mb-4" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link fw-medium" data-bs-toggle="tab" data-bs-target="#openings"
                                        aria-current="page" href="#openings" aria-selected="true" role="tab">Openings </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link fw-medium active" data-bs-toggle="tab" data-bs-target="#applicants"
                                        href="#applicants" aria-selected="false" tabindex="-1" role="tab">Applicants </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade" id="openings">
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="avatar overflow-hidden flex-shrink-0 bg-gray-100">
                                                <img src="" class="img-fluid rounded-circle w-auto h-auto" alt="img" />
                                            </a>
                                            <div class="ms-2 overflow-hidden">
                                                <p class="text-dark fw-medium text-truncate mb-0"><a
                                                        href="javascript:void(0);">Senior IOS Developer </a></p>
                                                <span class="fs-12">No of Openings : 25 </span>
                                            </div>
                                        </div>
                                        <a href="javascript:void(0);"
                                            class="btn btn-light btn-sm p-0 btn-icon d-flex align-items-center justify-content-center"><i
                                                class="ti ti-edit"></i></a>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="avatar overflow-hidden flex-shrink-0 bg-gray-100">
                                                <img src="" class="img-fluid w-auto h-auto" alt="img" />
                                            </a>
                                            <div class="ms-2 overflow-hidden">
                                                <p class="text-dark fw-medium text-truncate mb-0"><a
                                                        href="javascript:void(0);">Junior PHP Developer </a></p>
                                                <span class="fs-12">No of Openings : 20 </span>
                                            </div>
                                        </div>
                                        <a href="javascript:void(0);"
                                            class="btn btn-light btn-sm p-0 btn-icon d-flex align-items-center justify-content-center"><i
                                                class="ti ti-edit"></i></a>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="avatar overflow-hidden flex-shrink-0 bg-gray-100">
                                                <img src="" class="img-fluid w-auto h-auto" alt="img" />
                                            </a>
                                            <div class="ms-2 overflow-hidden">
                                                <p class="text-dark fw-medium text-truncate mb-0"><a
                                                        href="javascript:void(0);">Junior React Developer </a></p>
                                                <span class="fs-12">No of Openings : 30 </span>
                                            </div>
                                        </div>
                                        <a href="javascript:void(0);"
                                            class="btn btn-light btn-sm p-0 btn-icon d-flex align-items-center justify-content-center"><i
                                                class="ti ti-edit"></i></a>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-0">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="avatar overflow-hidden flex-shrink-0 bg-gray-100">
                                                <img src="" class="img-fluid w-auto h-auto" alt="img" />
                                            </a>
                                            <div class="ms-2 overflow-hidden">
                                                <p class="text-dark fw-medium text-truncate mb-0"><a
                                                        href="javascript:void(0);">Senior Laravel Developer </a></p>
                                                <span class="fs-12">No of Openings : 40 </span>
                                            </div>
                                        </div>
                                        <a href="javascript:void(0);"
                                            class="btn btn-light btn-sm p-0 btn-icon d-flex align-items-center justify-content-center"><i
                                                class="ti ti-edit"></i></a>
                                    </div>
                                </div>
                                <div class="tab-pane fade show active" id="applicants">
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="avatar overflow-hidden flex-shrink-0">
                                                <img src="" class="img-fluid rounded-circle" alt="img" />
                                            </a>
                                            <div class="ms-2 overflow-hidden">
                                                <p class="text-dark fw-medium text-truncate mb-0"><a href="#">Brian
                                                        Villalobos </a></p>
                                                <span class="fs-13 d-inline-flex align-items-center">Exp : 5+ Years <i
                                                        class="ti ti-circle-filled fs-4 mx-2 text-primary"></i>USA </span>
                                            </div>
                                        </div>
                                        <span class="badge badge-secondary badge-xs">UI/UX Designer </span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="avatar overflow-hidden flex-shrink-0">
                                                <img src="" class="img-fluid rounded-circle" alt="img" />
                                            </a>
                                            <div class="ms-2 overflow-hidden">
                                                <p class="text-dark fw-medium text-truncate mb-0"><a href="#">Anthony Lewis
                                                    </a></p>
                                                <span class="fs-13 d-inline-flex align-items-center">Exp : 4+ Years <i
                                                        class="ti ti-circle-filled fs-4 mx-2 text-primary"></i>USA </span>
                                            </div>
                                        </div>
                                        <span class="badge badge-info badge-xs">Python Developer </span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="avatar overflow-hidden flex-shrink-0">
                                                <img src="" class="img-fluid rounded-circle" alt="img" />
                                            </a>
                                            <div class="ms-2 overflow-hidden">
                                                <p class="text-dark fw-medium text-truncate mb-0"><a href="#">Stephan Peralt
                                                    </a></p>
                                                <span class="fs-13 d-inline-flex align-items-center">Exp : 6+ Years <i
                                                        class="ti ti-circle-filled fs-4 mx-2 text-primary"></i>USA </span>
                                            </div>
                                        </div>
                                        <span class="badge badge-pink badge-xs">Android Developer </span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-0">
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="avatar overflow-hidden flex-shrink-0">
                                                <img src="" class="img-fluid rounded-circle" alt="img" />
                                            </a>
                                            <div class="ms-2 overflow-hidden">
                                                <p class="text-dark fw-medium text-truncate mb-0"><a
                                                        href="javascript:void(0);">Doglas Martini </a></p>
                                                <span class="fs-13 d-inline-flex align-items-center">Exp : 2+ Years <i
                                                        class="ti ti-circle-filled fs-4 mx-2 text-primary"></i>USA </span>
                                            </div>
                                        </div>
                                        <span class="badge badge-purple badge-xs">React Developer </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- /Jobs Applicants -->

                <!-- Employees -->
                <div class="col-lg-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Employees </h5>
                            <a href="{{ box_route('manage_users.employee_list') }}" class="btn btn-light btn-md mb-2">View All </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th>Name </th>
                                            <th>Department </th>
                                            <th>Date Time</th>
                                        </tr>
                                    </thead>
                                    <tbody id="employee-table-body"></tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Employees -->

                <!-- Pending Attendance Corrections -->
                <div class="col-lg-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Pending Attendance Corrections </h5>
                            <a href="{{ box_route('employee.attendace.admin') }}?status_correction=Pending"
                                class="btn btn-light btn-md mb-2"
                                onclick="localStorage.setItem('fromDashboard', 'true')">View All </a>
                        </div>
                        <div class="card-body">
                            <div id="pending-corrections-list">
                                <!-- Dynamic List -->
                                <p class="text-center text-muted py-3">No pending corrections found.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Pending Attendance Corrections -->

            </div>

            <div class="row">

                <!-- Sales Overview -->
               <!-- <div class="col-xl-7 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Sales Overview </h5>
                            <div class="d-flex align-items-center">
                                <div class="dropdown mb-2">
                                    <a href="javascript:void(0);"
                                        class="dropdown-toggle btn btn-white border-0 btn-sm d-inline-flex align-items-center fs-13 me-2"
                                        data-bs-toggle="dropdown">
                                        All Departments
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">UI/UX Designer
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">HR Manager </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Junior Tester
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pb-0">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div class="d-flex align-items-center mb-1">
                                    <p class="fs-13 text-gray-9 me-3 mb-0"><i
                                            class="ti ti-square-filled me-2 text-primary"></i>Income </p>
                                    <p class="fs-13 text-gray-9 mb-0"><i
                                            class="ti ti-square-filled me-2 text-gray-2"></i>Expenses </p>
                                </div>
                                <p class="fs-13 mb-1">Last Updated at 11:30PM </p>
                            </div>
                            <div id="sales-income"></div>
                        </div>
                    </div>
                </div> -->
                <!-- /Sales Overview -->

                <!-- Invoices -->
                <!-- <div class="col-xl-5 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Invoices </h5>
                            <div class="d-flex align-items-center">
                                <div class="dropdown mb-2">
                                    <a href="javascript:void(0);"
                                        class="dropdown-toggle btn btn-white btn-sm d-inline-flex align-items-center fs-13 me-2 border-0"
                                        data-bs-toggle="dropdown">
                                        Invoices
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Invoices </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Paid </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Unpaid </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="dropdown mb-2">
                                    <a href="javascript:void(0);"
                                        class="btn btn-white border btn-sm d-inline-flex align-items-center"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-calendar me-1"></i>This Week
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">This Month </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">This Week </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Today </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-2">
                            <div class="table-responsive pt-1">
                                <table class="table table-nowrap table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="px-0">
                                                <div class="d-flex align-items-center">
                                                    <a href="invoice-details.html" class="avatar">
                                                        <img src="" class="img-fluid rounded-circle" alt="img" />
                                                    </a>
                                                    <div class="ms-2">
                                                        <h6 class="fw-medium"><a href="invoice-details.html">Redesign
                                                                Website </a></h6>
                                                        <span class="fs-13 d-inline-flex align-items-center">#INVOO2 <i
                                                                class="ti ti-circle-filled fs-4 mx-1 text-primary"></i>Logistics
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="fs-13 mb-1">Payment </p>
                                                <h6 class="fw-medium">$3560 </h6>
                                            </td>
                                            <td class="px-0 text-end">
                                                <span
                                                    class="badge badge-danger-transparent badge-xs d-inline-flex align-items-center"><i
                                                        class="ti ti-circle-filled fs-5 me-1"></i>Unpaid </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-0">
                                                <div class="d-flex align-items-center">
                                                    <a href="invoice-details.html" class="avatar">
                                                        <img src="" class="img-fluid rounded-circle" alt="img" />
                                                    </a>
                                                    <div class="ms-2">
                                                        <h6 class="fw-medium"><a href="invoice-details.html">Module
                                                                Completion </a></h6>
                                                        <span class="fs-13 d-inline-flex align-items-center">#INVOO5 <i
                                                                class="ti ti-circle-filled fs-4 mx-1 text-primary"></i>Yip
                                                            Corp </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="fs-13 mb-1">Payment </p>
                                                <h6 class="fw-medium">$4175 </h6>
                                            </td>
                                            <td class="px-0 text-end">
                                                <span
                                                    class="badge badge-danger-transparent badge-xs d-inline-flex align-items-center"><i
                                                        class="ti ti-circle-filled fs-5 me-1"></i>Unpaid </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-0">
                                                <div class="d-flex align-items-center">
                                                    <a href="invoice-details.html" class="avatar">
                                                        <img src="" class="img-fluid rounded-circle" alt="img" />
                                                    </a>
                                                    <div class="ms-2">
                                                        <h6 class="fw-medium"><a href="invoice-details.html">Change on Emp
                                                                Module </a></h6>
                                                        <span class="fs-13 d-inline-flex align-items-center">#INVOO3 <i
                                                                class="ti ti-circle-filled fs-4 mx-1 text-primary"></i>Ignis
                                                            LLP </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="fs-13 mb-1">Payment </p>
                                                <h6 class="fw-medium">$6985 </h6>
                                            </td>
                                            <td class="px-0 text-end">
                                                <span
                                                    class="badge badge-danger-transparent badge-xs d-inline-flex align-items-center"><i
                                                        class="ti ti-circle-filled fs-5 me-1"></i>Unpaid </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-0">
                                                <div class="d-flex align-items-center">
                                                    <a href="invoice-details.html" class="avatar">
                                                        <img src="" class="img-fluid rounded-circle" alt="img" />
                                                    </a>
                                                    <div class="ms-2">
                                                        <h6 class="fw-medium"><a href="invoice-details.html">Changes on
                                                                the Board </a></h6>
                                                        <span class="fs-13 d-inline-flex align-items-center">#INVOO2 <i
                                                                class="ti ti-circle-filled fs-4 mx-1 text-primary"></i>Ignis
                                                            LLP </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="fs-13 mb-1">Payment </p>
                                                <h6 class="fw-medium">$1457 </h6>
                                            </td>
                                            <td class="px-0 text-end">
                                                <span
                                                    class="badge badge-danger-transparent badge-xs d-inline-flex align-items-center"><i
                                                        class="ti ti-circle-filled fs-5 me-1"></i>Unpaid </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-0">
                                                <div class="d-flex align-items-center">
                                                    <a href="invoice-details.html" class="avatar">
                                                        <img src="" class="img-fluid rounded-circle" alt="img" />
                                                    </a>
                                                    <div class="ms-2">
                                                        <h6 class="fw-medium"><a href="invoice-details.html">Hospital
                                                                Management </a></h6>
                                                        <span class="fs-13 d-inline-flex align-items-center">#INVOO6 <i
                                                                class="ti ti-circle-filled fs-4 mx-1 text-primary"></i>HCL
                                                            Corp </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="fs-13 mb-1">Payment </p>
                                                <h6 class="fw-medium">$6458 </h6>
                                            </td>
                                            <td class="px-0 text-end">
                                                <span
                                                    class="badge badge-success-transparent badge-xs d-inline-flex align-items-center"><i
                                                        class="ti ti-circle-filled fs-5 me-1"></i>Paid </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <a href="invoice.html" class="btn btn-light btn-md w-100 mt-2">View All </a>
                        </div>
                    </div>
                </div> -->
                <!-- /Invoices -->

            </div>

            <div class="row">

                <!-- Projects -->
               <!-- <div class="col-xxl-8 col-xl-7 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Projects </h5>
                            <div class="d-flex align-items-center">
                                <div class="dropdown mb-2">
                                    <a href="javascript:void(0);"
                                        class="btn btn-white border btn-sm d-inline-flex align-items-center"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-calendar me-1"></i>This Week
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">This Month </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">This Week </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Today </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID </th>
                                            <th>Name </th>
                                            <th>Team </th>
                                            <th>Hours </th>
                                            <th>Deadline </th>
                                            <th>Priority </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><a href="project-details.html" class="link-default">PRO-001 </a></td>
                                            <td>
                                                <h6 class="fw-medium"><a href="project-details.html">Office Management App
                                                    </a></h6>
                                            </td>
                                            <td>
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="mb-1">15/255 Hrs </p>
                                                <div class="progress progress-xs w-100" role="progressbar"
                                                    aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-primary" style="width: 40%"></div>
                                                </div>
                                            </td>
                                            <td>12 Sep 2024 </td>
                                            <td>
                                                <span class="badge badge-danger d-inline-flex align-items-center badge-xs">
                                                    <i class="ti ti-point-filled me-1"></i>High
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><a href="project-details.html" class="link-default">PRO-002 </a></td>
                                            <td>
                                                <h6 class="fw-medium"><a href="project-details.html">Clinic Management
                                                    </a></h6>
                                            </td>
                                            <td>
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                    <a class="avatar bg-primary avatar-rounded text-fixed-white fs-10 fw-medium"
                                                        href="javascript:void(0);">
                                                        +1
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="mb-1">15/255 Hrs </p>
                                                <div class="progress progress-xs w-100" role="progressbar"
                                                    aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-primary" style="width: 40%"></div>
                                                </div>
                                            </td>
                                            <td>24 Oct 2024 </td>
                                            <td>
                                                <span class="badge badge-success d-inline-flex align-items-center badge-xs">
                                                    <i class="ti ti-point-filled me-1"></i>Low
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><a href="project-details.html" class="link-default">PRO-003 </a></td>
                                            <td>
                                                <h6 class="fw-medium"><a href="project-details.html">Educational Platform
                                                    </a></h6>
                                            </td>
                                            <td>
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="mb-1">40/255 Hrs </p>
                                                <div class="progress progress-xs w-100" role="progressbar"
                                                    aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-primary" style="width: 50%"></div>
                                                </div>
                                            </td>
                                            <td>18 Feb 2024 </td>
                                            <td>
                                                <span class="badge badge-pink d-inline-flex align-items-center badge-xs">
                                                    <i class="ti ti-point-filled me-1"></i>Medium
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><a href="project-details.html" class="link-default">PRO-004 </a></td>
                                            <td>
                                                <h6 class="fw-medium"><a href="project-details.html">Chat & Call Mobile
                                                        ___ </a></h6>
                                            </td>
                                            <td>
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="mb-1">35/155 Hrs </p>
                                                <div class="progress progress-xs w-100" role="progressbar"
                                                    aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-primary" style="width: 50%"></div>
                                                </div>
                                            </td>
                                            <td>19 Feb 2024 </td>
                                            <td>
                                                <span class="badge badge-danger d-inline-flex align-items-center badge-xs">
                                                    <i class="ti ti-point-filled me-1"></i>High
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><a href="project-details.html" class="link-default">PRO-005 </a></td>
                                            <td>
                                                <h6 class="fw-medium"><a href="project-details.html">Travel Planning
                                                        Website </a></h6>
                                            </td>
                                            <td>
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="mb-1">50/235 Hrs </p>
                                                <div class="progress progress-xs w-100" role="progressbar"
                                                    aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-primary" style="width: 50%"></div>
                                                </div>
                                            </td>
                                            <td>18 Feb 2024 </td>
                                            <td>
                                                <span class="badge badge-pink d-inline-flex align-items-center badge-xs">
                                                    <i class="ti ti-point-filled me-1"></i>Medium
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><a href="project-details.html" class="link-default">PRO-006 </a></td>
                                            <td>
                                                <h6 class="fw-medium"><a href="project-details.html">Service Booking
                                                        Software </a></h6>
                                            </td>
                                            <td>
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="mb-1">40/255 Hrs </p>
                                                <div class="progress progress-xs w-100" role="progressbar"
                                                    aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-primary" style="width: 50%"></div>
                                                </div>
                                            </td>
                                            <td>20 Feb 2024 </td>
                                            <td>
                                                <span class="badge badge-success d-inline-flex align-items-center badge-xs">
                                                    <i class="ti ti-point-filled me-1"></i>Low
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border-0"><a href="project-details.html" class="link-default">PRO-008
                                                </a></td>
                                            <td class="border-0">
                                                <h6 class="fw-medium"><a href="project-details.html">Travel Planning
                                                        Website </a></h6>
                                            </td>
                                            <td class="border-0">
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="" alt="img" />
                                                    </span>
                                                    <a class="avatar bg-primary avatar-rounded text-fixed-white fs-10 fw-medium"
                                                        href="javascript:void(0);">
                                                        +2
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="border-0">
                                                <p class="mb-1">15/255 Hrs </p>
                                                <div class="progress progress-xs w-100" role="progressbar"
                                                    aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-primary" style="width: 45%"></div>
                                                </div>
                                            </td>
                                            <td class="border-0">17 Oct 2024 </td>
                                            <td class="border-0">
                                                <span class="badge badge-pink d-inline-flex align-items-center badge-xs">
                                                    <i class="ti ti-point-filled me-1"></i>Medium
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- /Projects -->

                

            </div>

            <div class="row">

                <!-- Schedules -->
               <!-- <div class="col-lg-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Schedules </h5>
                            <a href="candidates.html" class="btn btn-light btn-md mb-2">View All </a>
                        </div>
                        <div class="card-body">
                            <div class="bg-light p-3 br-5 mb-4">
                                <span class="badge badge-secondary badge-xs mb-1">UI/ UX Designer </span>
                                <h6 class="mb-2 text-truncate">Interview Candidates - UI/UX ________ </h6>
                                <div class="d-flex align-items-center flex-wrap">
                                    <p class="fs-13 mb-1 me-2"><i class="ti ti-calendar-event me-2"></i>Thu, 15 Feb 2025
                                    </p>
                                    <p class="fs-13 mb-1"><i class="ti ti-clock-hour-11 me-2"></i>01:00 PM - 02:20 __
                                    </p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between border-top mt-2 pt-3">
                                    <div class="avatar-list-stacked avatar-group-sm">
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="" alt="img" />
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="" alt="img" />
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            {{-- <img class="border border-white" src="{{  aseet('11') }}" alt="img" /> --}}
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="" alt="img" />
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="" alt="img" />
                                        </span>
                                        <a class="avatar bg-primary avatar-rounded text-fixed-white fs-10 fw-medium"
                                            href="javascript:void(0);">
                                            +3
                                        </a>
                                    </div>
                                    <a href="#" class="btn btn-primary btn-xs">Join Meeting </a>
                                </div>
                            </div>
                            <div class="bg-light p-3 br-5 mb-0">
                                <span class="badge badge-dark badge-xs mb-1">IOS Developer </span>
                                <h6 class="mb-2 text-truncate">Interview Candidates - IOS _________ </h6>
                                <div class="d-flex align-items-center flex-wrap">
                                    <p class="fs-13 mb-1 me-2"><i class="ti ti-calendar-event me-2"></i>Thu, 15 Feb 2025
                                    </p>
                                    <p class="fs-13 mb-1"><i class="ti ti-clock-hour-11 me-2"></i>02:00 PM - 04:20 __
                                    </p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between border-top mt-2 pt-3">
                                    <div class="avatar-list-stacked avatar-group-sm">
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="" alt="img" />
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="" alt="img" />
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="" alt="img" />
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="" alt="img" />
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="" alt="img" />
                                        </span>
                                        <a class="avatar bg-primary avatar-rounded text-fixed-white fs-10 fw-medium"
                                            href="javascript:void(0);">
                                            +3
                                        </a>
                                    </div>
                                    <a href="#" class="btn btn-primary btn-xs">Join Meeting </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- /Schedules -->


            <!-- Tasks Statistics -->
                <div class="col-lg-4 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Tasks Statistics </h5>
                            <div class="d-flex align-items-center">
                                <div class="dropdown mb-2">
                                    <a href="javascript:void(0);"
                                        class="btn btn-white border btn-sm d-inline-flex align-items-center"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-calendar me-1"></i>This Week
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">This Month </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">This Week </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Today </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="chartjs-wrapper-demo position-relative mb-4">
                                <canvas id="mySemiDonutChart" height="190"></canvas>
                                <div class="position-absolute text-center attendance-canvas">
                                    <p class="fs-13 mb-1">Failed Tasks </p>
                                    <h3 id="task_stat_failed">0</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center flex-wrap">
                                <div class="border-end text-center me-2 pe-1 mb-3">
                                    <p class="fs-13 d-inline-flex align-items-center mb-1"><i
                                            class="ti ti-circle-filled fs-10 me-1 text-warning"></i>Ongoing </p>
                                    <h5 id="task_percent_ongoing">0% </h5>
                                </div>
                                <div class="border-end text-center me-2 pe-1 mb-3">
                                    <p class="fs-13 d-inline-flex align-items-center mb-1"><i
                                            class="ti ti-circle-filled fs-10 me-1 text-info"></i>On Hold </p>
                                    <h5 id="task_percent_onhold">0% </h5>
                                </div>
                                <div class="border-end text-center me-2 pe-1 mb-3">
                                    <p class="fs-13 d-inline-flex align-items-center mb-1"><i
                                            class="ti ti-circle-filled fs-10 me-1 text-danger"></i>Overdue </p>
                                    <h5 id="task_percent_overdue">0% </h5>
                                </div>
                                <div class="border-end text-center me-2 pe-1 mb-3">
                                    <p class="fs-13 d-inline-flex align-items-center mb-1"><i
                                            class="ti ti-circle-filled fs-10 me-1 text-success"></i>Completed </p>
                                    <h5 id="task_percent_completed">0% </h5>
                                </div>
                                <div class="text-center mb-3">
                                    <p class="fs-13 d-inline-flex align-items-center mb-1"><i
                                            class="ti ti-circle-filled fs-10 me-1 text-secondary"></i>Late </p>
                                    <h5 id="task_percent_late">0% </h5>
                                </div>
                            </div>
                            <div class="bg-dark br-5 p-3 pb-0 d-flex align-items-center justify-content-between">
                                <div class="mb-2">
                                    <h4 class="text-success mb-1" id="task_spent_hours_text">0 hrs</h4>
                                    <p class="fs-13 mb-0 text-white" id="task_spent_subtext">Spent on Overall Tasks This Week</p>
                                </div>
                                <a href="{{ route('tasks.index', ['box_slug' => request()->route('box_slug')]) }}" class="btn btn-sm btn-light mb-2 text-nowrap">View All</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Tasks Statistics -->







                <!-- Recent Activities -->
                <div class="col-lg-4 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Recent Activities </h5>
                            <a href="activity.html" class="btn btn-light btn-md mb-2">View All </a>
                        </div>
                        <div class="card-body" id="recent-activity-list">
                            <!-- Dynamic recent activities will be loaded here -->
                        </div>
                    </div>
                </div>
                <!-- /Recent Activities -->

                <!-- Birthdays -->
                {{-- <div class="col-lg-4 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Birthdays </h5>
                            <a href="javascript:void(0);" class="btn btn-light btn-md mb-2">View All </a>
                        </div>
                        <div class="card-body pb-1">
                            <h6 class="mb-2">Today </h6>
                            <div class="bg-light p-2 border border-dashed rounded-top mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar">
                                            <img src="" class="rounded-circle" alt="img" />
                                        </a>
                                        <div class="ms-2 overflow-hidden">
                                            <h6 class="fs-medium ">Andrew Jermia </h6>
                                            <p class="fs-13">IOS Developer </p>
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-secondary btn-xs"><i
                                            class="ti ti-cake me-1"></i>Send </a>
                                </div>
                            </div>
                            <h6 class="mb-2">Tomorow </h6>
                            <div class="bg-light p-2 border border-dashed rounded-top mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar">
                                            <img src="" class="rounded-circle" alt="img" />
                                        </a>
                                        <div class="ms-2 overflow-hidden">
                                            <h6 class="fs-medium"><a href="javascript:void(0);">Mary Zeen </a></h6>
                                            <p class="fs-13">UI/UX Designer </p>
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-secondary btn-xs"><i
                                            class="ti ti-cake me-1"></i>Send </a>
                                </div>
                            </div>
                            <div class="bg-light p-2 border border-dashed rounded-top mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar">
                                            <img src="" class="rounded-circle" alt="img" />
                                        </a>
                                        <div class="ms-2 overflow-hidden">
                                            <h6 class="fs-medium "><a href="javascript:void(0);">Antony Lewis </a></h6>
                                            <p class="fs-13">Android Developer </p>
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-secondary btn-xs"><i
                                            class="ti ti-cake me-1"></i>Send </a>
                                </div>
                            </div>
                            <h6 class="mb-2">25 Jan 2025 </h6>
                            <div class="bg-light p-2 border border-dashed rounded-top mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar">
                                            <img src="" class="rounded-circle" alt="img" />
                                        </span>
                                        <div class="ms-2 overflow-hidden">
                                            <h6 class="fs-medium ">Doglas Martini </h6>
                                            <p class="fs-13">.Net Developer </p>
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-secondary btn-xs"><i
                                            class="ti ti-cake me-1"></i>Send </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <!-- Birthdays -->
                <div class="col-lg-4 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Birthdays </h5>
                            <a href="javascript:void(0);" class="btn btn-light btn-md mb-2">View All </a>
                        </div>
                        <div class="card-body pb-1" id="birthday-list">
                            <!-- Dynamic birthday groups will be rendered here -->
                        </div>
                    </div>
                </div>


                <!-- /Birthdays -->

            </div>

        </div>

        

    </div>
    <!-- /Page Wrapper -->

    <!-- Add Todo -->
    <div class="modal fade" id="add_todo">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Todo </h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form action="index.html">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Todo Title </label>
                                    <input type="text" class="form-control" />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Tag </label>
                                    <select class="select">
                                        <option />Select
                                        <option />Internal
                                        <option />Projects
                                        <option />Meetings
                                        <option />Reminder
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Priority </label>
                                    <select class="select">
                                        <option />Select
                                        <option />Medium
                                        <option />High
                                        <option />Low
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Descriptions </label>
                                    <div class="summernote"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Add Assignee </label>
                                    <select class="select">
                                        <option />Select
                                        <option />Sophie
                                        <option />Cameron
                                        <option />Doris
                                        <option />Rufana
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-0">
                                    <label class="form-label">Status </label>
                                    <select class="select">
                                        <option />Select
                                        <option />Completed
                                        <option />Pending
                                        <option />Onhold
                                        <option />Inprogress
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel </button>
                        <button type="submit" class="btn btn-primary">Add New Todo </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Todo -->

    <!-- Send Overall Notification -->
    <div class="modal fade" id="send_overall_notification" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex align-items-center">
                        <h5 class="modal-title me-2">Send Overall Notification</h5>
                    </div>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form action="{{ route('notifications.send_overall', request()->route('box_slug') ?? 'default') }}" method="POST">
                    @csrf
                    <div class="modal-body pb-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Notification Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" required placeholder="e.g. System Update">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Message <span class="text-danger">*</span></label>
                                    <textarea name="message" class="form-control" rows="4" required placeholder="Enter the notification message..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Send Notification</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Send Overall Notification -->

    <!-- Add Project -->
    <div class="modal fade" id="add_project" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header header-border align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <h5 class="modal-title me-2">Add Project </h5>
                        <p class="text-dark">Project ID : PRO-0004 </p>
                    </div>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="add-info-fieldset">
                    <div class="add-details-wizard p-3 pb-0">
                        <ul class="progress-bar-wizard d-flex align-items-center border-bottom">
                            <li class="active p-2 pt-0">
                                <h6 class="fw-medium">Basic Information </h6>
                            </li>
                            <li class="p-2 pt-0">
                                <h6 class="fw-medium">Members </h6>
                            </li>
                        </ul>
                    </div>
                    <fieldset id="first-field-file">
                        <form action="projects.html">
                            <div class="modal-body">
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
                                                    <h6 class="mb-1">Upload Project Logo </h6>
                                                    <p class="fs-12">Image should be below 4 mb </p>
                                                </div>
                                                <div class="profile-uploader d-flex align-items-center">
                                                    <div class="drag-upload-btn btn btn-sm btn-primary me-2">
                                                        Upload
                                                        <input type="file" class="form-control image-sign" multiple="" />
                                                    </div>
                                                    <a href="javascript:void(0);" class="btn btn-light btn-sm">Cancel
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Project Name </label>
                                            <input type="text" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Client </label>
                                            <select class="select">
                                                <option />Select
                                                <option />Anthony Lewis
                                                <option />Brian Villalobos
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Start Date </label>
                                                    <div class="input-icon-end position-relative">
                                                        <input type="text" class="form-control datetimepicker"
                                                            placeholder="dd/mm/yyyy" value="02-05-2024" />
                                                        <span class="input-icon-addon">
                                                            <i class="ti ti-calendar text-gray-7"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">End Date </label>
                                                    <div class="input-icon-end position-relative">
                                                        <input type="text" class="form-control datetimepicker"
                                                            placeholder="dd/mm/yyyy" value="02-05-2024" />
                                                        <span class="input-icon-addon">
                                                            <i class="ti ti-calendar text-gray-7"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Priority </label>
                                                    <select class="select">
                                                        <option />Select
                                                        <option />High
                                                        <option />Medium
                                                        <option />Low
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Project Value </label>
                                                    <input type="text" class="form-control" value="$" />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Total Working Hours </label>
                                                    <div class="input-icon-end position-relative">
                                                        <input type="text" class="form-control timepicker"
                                                            placeholder="-- : -- : --" value="02-05-2024" />
                                                        <span class="input-icon-addon">
                                                            <i class="ti ti-clock-hour-3 text-gray-7"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Extra Time </label>
                                                    <input type="text" class="form-control" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-0">
                                            <label class="form-label">Description </label>
                                            <div class="summernote"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="d-flex align-items-center justify-content-end">
                                    <button type="button" class="btn btn-outline-light border me-2"
                                        data-bs-dismiss="modal">Cancel </button>
                                    <button class="btn btn-primary wizard-next-btn" type="button">Add Team Member
                                    </button>
                                </div>
                            </div>
                        </form>
                    </fieldset>
                    <fieldset>
                        <form action="projects.html">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label me-2">Team Members </label>
                                            <input class="input-tags form-control" placeholder="Add new" type="text"
                                                data-role="tagsinput" name="Label" value="Jerald,Andrew,Philip,Davis" />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label me-2">Team Leader </label>
                                            <input class="input-tags form-control" placeholder="Add new" type="text"
                                                data-role="tagsinput" name="Label" value="Hendry,James" />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label me-2">Project Manager </label>
                                            <input class="input-tags form-control" placeholder="Add new" type="text"
                                                data-role="tagsinput" name="Label" value="Dwight" />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Status </label>
                                            <select class="select">
                                                <option />Select
                                                <option />Active
                                                <option />Inactive
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div>
                                            <label class="form-label">Tags </label>
                                            <select class="select">
                                                <option />Select
                                                <option />High
                                                <option />Low
                                                <option />Medium
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="d-flex align-items-center justify-content-end">
                                    <button type="button" class="btn btn-outline-light border me-2"
                                        data-bs-dismiss="modal">Cancel </button>
                                    <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                        data-bs-target="#success_modal">Save </button>
                                </div>
                            </div>
                        </form>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
    <!-- /Add Project -->

    <!-- Add Leaves -->
    <div class="modal fade" id="add_leaves">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Leave Request </h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="add_leaves_form">
                    <div class="modal-body pb-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Employee Name </label>
                                    <select class="select" name="employee_id" required>
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->user->name ?? 'Unknown' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Leave Type </label>
                                    <select class="select" name="leave_type" id="add_leave_type" required>
                                        <option value="">Select Leave Type</option>
                                        @foreach($leaveTypes as $leaveType)
                                            <option value="{{ $leaveType->id }}">{{ $leaveType->leave_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">From </label>
                                    <div class="input-icon-end position-relative">
                                        <input type="text" class="form-control datetimepicker" name="from_date" id="add_from_date" placeholder="dd/mm/yyyy" required />
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar text-gray-7"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">To </label>
                                    <div class="input-icon-end position-relative">
                                        <input type="text" class="form-control datetimepicker" name="to_date" id="add_to_date" placeholder="dd/mm/yyyy" required />
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar text-gray-7"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">No of Days </label>
                                    <input type="text" class="form-control" name="total_days" id="add_total_days" readonly required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Remaining Days </label>
                                    <input type="text" class="form-control" name="leave_balance" id="add_leave_balance" readonly />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Reason </label>
                                    <textarea class="form-control" name="reason" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel </button>
                        <button type="submit" class="btn btn-primary">Add Leaves </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="attendance_report">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Attendance</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
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
    <!-- /Add Leaves -->
    @push('scripts')
        <script>
            $(document).ready(function () {
                function getColorFromName(name) {
                    const colors = [
                        "#f44336", "#e91e63", "#9c27b0", "#673ab7", "#3f51b5",
                        "#2196f3", "#03a9f4", "#00bcd4", "#009688", "#4caf50",
                        "#8bc34a", "#cddc39", "#ffc107", "#ff9800", "#ff5722"
                    ];
                    const index = name ? name.charCodeAt(0) % colors.length : 0;
                    return colors[index];
                }

                $('#pendingCorrectionsLink').on('click', function () {
                    localStorage.setItem('fromDashboard', 'pending_corrections');
                });

                $('#pendingLeaveRequestsLink').on('click', function () {
                    localStorage.setItem('fromDashboard', 'pending_leave_requests');
                });

                // Renders list of all employees
                function renderAttendanceCards(employees) {
                    const container = $('#attendance-cards');
                    container.empty(); // Clear previous entries

                    employees.forEach(employee => {
                        const name = employee.employee_name ?? 'N/A';
                        const designation = employee.designation ?? 'N/A';
                        const checkInTime = employee.check_in ?? '--:--';
                        const production = employee.working_hours ?? '0.00 Hrs';
                        const profileImage = employee.profile_image;
                        const attendanceId = employee.attendace_id;

                        // Avatar generation
                        let avatarHtml = '';
                        if (profileImage) {
                            avatarHtml =
                                `<img src="${profileImage}" class="rounded-circle border border-2" alt="${name}" style="width: 40px; height: 40px;" />`;
                        } else {
                            const firstLetter = name.charAt(0).toUpperCase();
                            const bgColor = getColorFromName(name);
                            avatarHtml = `
                        <div class="rounded-circle border border-2 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px; background-color: ${bgColor}; color: white; font-weight: 600; font-size: 18px;">
                            ${firstLetter}
                        </div>`;
                        }

                        const card = `
                    <div class="mb-3 p-2 border br-5">
                        <div class="d-flex align-items-center justify-content-between">
                            <!-- Left: Avatar & Info -->
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2">
                                    ${avatarHtml}
                                </div>
                                <div>
                                    <h6 class="fs-14 fw-medium text-truncate mb-0">${name}</h6>
                                    <p class="fs-13 text-muted mb-0">${designation}</p>
                                </div>
                            </div>

                            <!-- Right: Time badges -->
                            <div class="d-flex flex-column align-items-end">

                                <span
                                    class="badge badge-success d-inline-flex align-items-center clickable-production mt-1"
                                    data-id="${attendanceId}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#attendance_report"
                                    style="cursor: pointer;"
                                >
                                    <i class="ti ti-clock-hour-11 me-1"></i>${checkInTime}
                                </span>
                            </div>
                        </div>
                    </div>
                `;

                        container.append(card);
                    });
                }




                // Renders the latest late employee
                function renderLatestLateEmployee(employee) {
                    const name = employee.employee_name ?? 'N/A';
                    const designation = employee.designation ?? 'N/A';
                    const checkInTime = employee.check_in ?? '--:--';
                    const lateBy = employee.late_by ?? '--';
                    const profileImage = employee.profile_image;

                    let avatarHtml = '';
                    if (profileImage) {
                        avatarHtml =
                            `<img src="${profileImage}" class="rounded-circle border border-2" alt="${name}" />`;
                    } else {
                        const firstLetter = name.charAt(0).toUpperCase();
                        const bgColor = getColorFromName(name);
                        avatarHtml = `
                        <div class="letter-avatar rounded-circle border border-2 d-flex align-items-center justify-content-center"
                            style="width:40px; height:40px; background-color: ${bgColor}; color: white; font-weight: 600; font-size: 18px;">
                            ${firstLetter}
                        </div>
                    `;
                    }

                    const card = `
                    <div class="d-flex align-items-center justify-content-between mb-3 p-2 border border-dashed br-5 dd">
                        <div class="d-flex align-items-center">
                            <span class="avatar flex-shrink-0">
                                ${avatarHtml}
                            </span>
                            <div class="ms-2">
                                <h6 class="fs-14 fw-medium text-truncate">
                                    ${name}


                                </h6>
                                <p class="fs-13">${designation}</p>
                            </div>
                        </div>
                        <a href="javascript:void(0);" class="link-default me-2"><i class="ti ti-clock-share"></i></a>
                        <div class="d-grid">
                            <span class="fs-10 fw-medium d-inline-flex align-items-center badge badge-success" style="margin-bottom: 6px;">
                                 <i class="ti ti-clock-hour-11 me-1"></i>${lateBy}
                            </span>
                            <span class="fs-10 fw-medium d-inline-flex align-items-center badge badge-danger">
                                <i class="ti ti-circle-filled fs-5 me-1"></i>${checkInTime}
                            </span>
                        </div>
                    </div>
                `;

                    $('#latest-late-employee-card').html(card); // make sure this div exists in your HTML
                }

                function renderEmployeeTypesStats(stats, totalCount) {
                    $('#total_employees_count_stat').text(totalCount);

                    const progressBarContainer = $('#employee_types_progress_bar');
                    const statsGridContainer = $('#employee_types_stats_grid');

                    progressBarContainer.empty();
                    statsGridContainer.empty();

                    if (!stats || stats.length === 0) {
                        statsGridContainer.html('<div class="col-12 p-3 text-center text-muted">No employee data available.</div>');
                        return;
                    }

                    const colors = ['warning', 'secondary', 'danger', 'pink', 'success', 'info', 'primary', 'dark', 'purple'];

                    stats.forEach((stat, index) => {
                        const color = colors[index % colors.length];

                        // Progress Bar Segment
                        const progressSegment = `
                                     <div class="progress" role="progressbar" aria-label="${stat.name}" aria-valuenow="${stat.percentage}"
                                         aria-valuemin="0" aria-valuemax="100" style="width: ${stat.percentage}%">
                                         <div class="progress-bar bg-${color}"></div>
                                     </div>
                                 `;
                        progressBarContainer.append(progressSegment);

                        // Stats Grid Item
                        const countStats = stats.length;
                        const lastRowStartIndex = countStats - (countStats % 2 === 0 ? 2 : 1);
                        const isLastRow = index >= lastRowStartIndex;

                        const borderEnd = index % 2 === 0 ? 'border-end' : '';
                        const borderBottom = !isLastRow ? 'border-bottom' : '';
                        const textEnd = index % 2 !== 0 ? 'text-end' : '';

                        const gridItem = `
                                     <div class="col-6">
                                         <div class="p-2 flex-fill ${borderEnd} ${borderBottom} ${textEnd}">
                                             <p class="fs-13 mb-2"><i
                                                     class="ti ti-square-filled text-${color} fs-12 me-2"></i>${stat.name} <span
                                                     class="text-gray-9">(${stat.percentage}%) </span></p>
                                             <h2 class="display-1">${stat.count} </h2>
                                         </div>
                                     </div>
                                 `;
                        statsGridContainer.append(gridItem);
                    });
                }
                function renderPendingCorrections(corrections) {
                    const container = $('#pending-corrections-list');
                    container.empty();

                    if (!corrections || corrections.length === 0) {
                        container.html('<p class="text-center text-muted py-3">No pending corrections found.</p>');
                        return;
                    }

                    // Only take max 5 records
                    const limitedCorrections = corrections.slice(0, 5);

                    limitedCorrections.forEach(cor => {
                        const itemHtml = `
                                    <div class="d-flex align-items-center justify-content-between mb-3 p-2 border border-dashed br-5">
                                        <div class="d-flex align-items-center overflow-hidden">
                                            <div class="me-2">
                                                <div class="avatar avatar-md bg-primary-transparent text-primary rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-file-text fs-18"></i>
                                                </div>
                                            </div>
                                            <div class="overflow-hidden">
                                                <h6 class="fs-14 fw-medium text-truncate mb-0">${cor.user_name}</h6>
                                                <p class="fs-12 text-muted mb-0">${cor.type} | ${cor.date}</p>
                                            </div>
                                        </div>
                                        <div class="text-end ps-2">
                                            <span class="badge badge-warning-transparent badge-xs mb-1">Pending</span>
                                            <p class="fs-11 text-muted mb-0">${cor.created_at}</p>
                                        </div>
                                    </div>
                                `;
                        container.append(itemHtml);
                    });
                }

                // Main fetch function
                
                function fetchIndependentTotalTasks() {
                    let startDate = "";
                    let endDate = "";
                    let dateVal = $("#widgetDateFilter").val();
                    if (dateVal && $("#widgetDateFilter").data("daterangepicker")) {
                        startDate = $("#widgetDateFilter").data("daterangepicker").startDate.format("YYYY-MM-DD");
                        endDate = $("#widgetDateFilter").data("daterangepicker").endDate.format("YYYY-MM-DD");
                    }
                    let userId = $("#widgetUserFilter").val();
                    let requestData = {};
                    if (startDate && endDate) {
                        requestData.start_date = startDate;
                        requestData.end_date = endDate;
                    }
                    if (userId) {
                        requestData.user_id = userId;
                    }

                    $.ajax({
                        url: "{{ route('admin.total_tasks.stats', ['box_slug' => request()->route('box_slug')]) }}",
                        method: "GET",
                        data: requestData,
                        success: function(res) {
                            if (res.success && res.data) {
                                let total = res.data.total;
                                let open = res.data.open_count;
                                let closed = res.data.closed_count;
                                $("#widget_all_time_total_tasks").text(total);
                                
                                let openPercent = total > 0 ? Math.round((open / total) * 100) : 0;
                                let closedPercent = total > 0 ? Math.round((closed / total) * 100) : 0;

                                $("#widget_open_tasks_text").text(open);
                                $("#widget_closed_tasks_text").text(closed);
                                $("#widget_open_tasks_percent").text(openPercent + "%");
                                $("#widget_closed_tasks_percent").text(closedPercent + "%");
                                $("#widget_open_tasks_bar").css("width", openPercent + "%").attr("aria-valuenow", openPercent);
                                $("#widget_closed_tasks_bar").css("width", closedPercent + "%").attr("aria-valuenow", closedPercent);
                            }
                            if (res.users && $("#widgetUserFilter option").length <= 1) {
                                res.users.forEach(emp => {
                                    $("#widgetUserFilter").append(`<option value="${emp.user_id}">${emp.name || emp.user_id}</option>`);
                                });
                            }
                        }
                    });
                }

                $(document).ready(function() {

                    // Init Widget Date Range Picker
                    if ($("#widgetDateFilter").length) {
                        $("#widgetDateFilter").daterangepicker({
                            autoUpdateInput: false,
                            locale: { cancelLabel: "Clear" }
                        });
                        $("#widgetDateFilter").on("apply.daterangepicker", function(ev, picker) {
                            $(this).val(picker.startDate.format("YYYY-MM-DD") + " - " + picker.endDate.format("YYYY-MM-DD"));
                            fetchIndependentTotalTasks();
                        });
                        $("#widgetDateFilter").on("cancel.daterangepicker", function(ev, picker) {
                            $(this).val("");
                            fetchIndependentTotalTasks();
                        });
                    }

                    $("#widgetUserFilter").on("change", function() {
                        fetchIndependentTotalTasks();
                    });

                    // Fetch initial data
                    fetchIndependentTotalTasks();
                    fetchPerformanceTopPerformer();
                });

                function fetchPerformanceTopPerformer() {
                    const boxSlug = '{{ request()->route('box_slug') }}';
                    const url = '/' + boxSlug + '/fieldstracking/dashboard-api/performance?t=' + new Date().getTime();

                    $.get(url, function(response) {
                        if (!response || !response.stats) return;

                        let highestScore = -1;
                        let highestPerformer = null;

                        const allPerformers = response.stats.all_performers || [];
                        allPerformers.forEach(p => {
                            let score = parseFloat(p.avg_score);
                            if (score > highestScore) {
                                highestScore = score;
                                highestPerformer = p;
                            }
                        });

                        const teamPerformers = response.stats.team_performers || [];
                        teamPerformers.forEach(p => {
                            let score = parseFloat(p.avg_score);
                            if (score > highestScore) {
                                highestScore = score;
                                highestPerformer = p;
                            }
                        });

                        // 1. This Month Top Performer
                        if (highestPerformer && highestScore >= 0) {
                            $('#top_performer_name').text(highestPerformer.name);
                            $('#top_performer_role').text(highestPerformer.designation || highestPerformer.department || 'Employee');
                            $('#top_performer_score').text(highestScore.toFixed(1) + '%');
                            $('#top_performer_avatar').attr('src', 'https://ui-avatars.com/api/?name=' + encodeURIComponent(highestPerformer.name) + '&background=random');
                        } else {
                            $('#top_performer_name').text('No Data');
                            $('#top_performer_role').text('-');
                            $('#top_performer_score').text('0%');
                            $('#top_performer_avatar').attr('src', 'https://ui-avatars.com/api/?name=No+Data&background=E0E0E0&color=666');
                        }

                        // 2. Last Month Top Performer
                        if (response.stats.last_month_performer) {
                            $('#lm_top_performer_name').text(response.stats.last_month_performer);
                            $('#lm_top_performer_avatar').attr('src', 'https://ui-avatars.com/api/?name=' + encodeURIComponent(response.stats.last_month_performer) + '&background=random');
                            $('#lm_top_performer_badge').text('Last Month Star');
                        } else {
                            $('#lm_top_performer_name').text('No Data');
                            $('#lm_top_performer_role').text('-');
                            $('#lm_top_performer_avatar').attr('src', 'https://ui-avatars.com/api/?name=No+Data&background=E0E0E0&color=666');
                            $('#lm_top_performer_badge').text('No Data');
                        }
                    }).fail(function(err) {
                        $('#top_performer_name').text('No Data');
                        $('#top_performer_role').text('-');
                        $('#top_performer_score').text('0%');
                        $('#lm_top_performer_name').text('No Data');
                    });
                }

function fetchAttendance() {
                    let startDate = '';
                    let endDate = '';
                    if ($('#dashboardDateRange').data('daterangepicker')) {
                        startDate = $('#dashboardDateRange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        endDate = $('#dashboardDateRange').data('daterangepicker').endDate.format('YYYY-MM-DD');

                        // Update links to reports to maintain date filtering
                        $('a[href*="reminder-list"], a[href*="all-dsr"]').each(function() {
                            let url = new URL($(this).attr('href'), window.location.origin);
                            url.searchParams.set('start_date', startDate);
                            url.searchParams.set('end_date', endDate);
                            $(this).attr('href', url.href);
                        });
                    }

                    $.ajax({
                        url: "{{ route('admin.attendance.list', ['box_slug' => request()->route('box_slug')]) }}",
                        method: 'GET',
                        data: {
                            start_date: startDate,
                            end_date: endDate
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                const employees = response.data ?? [];
                                const latestLate = response.latest_late_employee ?? null;
                                const summary = response.attendance_summary ?? null;
                                const attendance_breakdown = response.attendance_breakdown ?? null;
                                const birthdays = response.upcomingBirthdays ?? [];
                                const allemployees = response.all_employess ?? [];

                                const employeeTypesStats = response.employee_types_stats ?? [];
                                const totalActiveEmployees = response.total_active_employees ?? 0;

                                const pending_requests = response.pending_requests ?? {};
                                const pending_correction = pending_requests.attendance_corrections ?? 0;
                                const pending_leaverequest = pending_requests.leave_requests ?? 0;

                                $('#pendingCorrectionsCount').text(pending_correction);
                                $('#pendingLeaveRequestsCount').text(pending_leaverequest);

                                renderEmployeeTypesStats(employeeTypesStats, totalActiveEmployees);
                                renderAttendanceCards(employees);
                                if (latestLate) {
                                    renderLatestLateEmployee(latestLate);
                                } else {
                                    $('#latest-late-employee-card').html(
                                        `<p class="text-muted">No late employee today.</p>`);
                                }
                                if (summary) {
                                    const present = summary.present ?? 0;
                                    const total = summary.total ?? 0;
                                    const diff = Math.abs(summary.percentage_diff ?? 0).toFixed(2);
                                    const isIncrease = summary.is_increase;
                                    $('#present_count').text(present);
                                    $('#total_count').text(total);
                                    $('#percent_change').text(`${isIncrease ? '+' : '-'}${diff}%`);
                                    $('#percent_icon')
                                        .removeClass('fa-caret-up fa-caret-down')
                                        .addClass(isIncrease ? 'fa-caret-up' : 'fa-caret-down');
                                    $('#percent_change')
                                        .removeClass('text-success text-danger')
                                        .addClass(isIncrease ? 'text-success' : 'text-danger');
                                } else {
                                    $('#present_count').text('0');
                                    $('#total_count').text('0');
                                    $('#percent_change').text('+0.0%');
                                    $('#percent_change').removeClass('text-success text-danger').addClass(
                                        'text-muted');
                                    $('#percent_icon').removeClass('fa-caret-up fa-caret-down').addClass(
                                        'fa-minus');
                                }

                                updateAttendanceBreakdown(attendance_breakdown);
                                renderUpcomingBirthdays(birthdays);
                                renderEmployees(allemployees);
                    // Removed redundant widgetUserFilter population
                                if (response.department_counts) {
                                    renderDepartmentChart(response.department_counts);
                                }
                                if (response.recent_activities) {
                                    renderRecentActivities(response.recent_activities);
                                }
                                if (response.pending_corrections_list) {
                                    renderPendingCorrections(response.pending_corrections_list);
                                }

                                
                                

if (response.task_stats) {
                                    updateTaskStatistics(response.task_stats);
                                }


                            } else {
                                console.error('Error:', response.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX error:', error);
                        }
                    });
                }

                function renderEmployees(allemployees) {
                    const tbody = $('#employee-table-body');
                    tbody.empty();

                    allemployees.forEach((employee, index) => {
                        const profileImage = employee.profile_image;
                        let avatarHtml;

                        if (profileImage) {
                            avatarHtml =
                                `<img src="${profileImage}" class="img-fluid rounded-circle" alt="img" />`;
                        } else {
                            const bgColor = getColorFromName(employee.name);
                            const firstChar = employee.name.charAt(0).toUpperCase();
                            avatarHtml = `
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="background-color: ${bgColor}; width: 40px; height: 40px; font-weight: 600; color: white; font-size: 18px;">
                            ${firstChar}
                        </div>`;
                        }
                        const rowHtml = `
                <tr${index === allemployees.length - 1 ? ' class="border-0"' : ''}>
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="javascript:void(0);" class="avatar me-2" style="width: 40px; height: 40px;">
                                ${avatarHtml}
                                <i class="ti ti-circle-filled fs-10 text-success ac-status" style="position: absolute; top:-3px; border: 3px solid #fff; border-radius: 50px; left: -7px;"></i>
                            </a>
                            <div class="ms-2">
                                <h6 class="fw-medium"><a href="javascript:void(0);">${employee.name}</a></h6>
                                <span class="fs-12">${employee.designation || 'N/A'}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="fs-12">${employee.department || 'N/A'}</span>
                    </td>
                    <td>
                    <span class="fs-12">${employee.date || 'N/A'} <br>${employee.time || ''}</span>
                    </td>

                </tr>`;
                        tbody.append(rowHtml);
                    });
                }

                function renderUpcomingBirthdays(birthdays) {
                    const container = $('#birthday-list');
                    container.empty();

                    // Group birthdays by birthday_display (Today, Tomorrow, or date)
                    const grouped = birthdays.reduce((acc, curr) => {
                        acc[curr.birthday_display] = acc[curr.birthday_display] || [];
                        acc[curr.birthday_display].push(curr);
                        return acc;
                    }, {});

                    Object.keys(grouped).forEach(groupTitle => {
                        // Add header for group (h6.mb-2)
                        container.append(`<h6 class="mb-2">${groupTitle}</h6>`);

                        // For each person in group, add the card with your exact markup & classes
                        grouped[groupTitle].forEach(person => {
                            const profileImage = person.profile_image;
                            let avatarHtml;

                            if (profileImage) {
                                avatarHtml =
                                    `<img src="${profileImage}" class="rounded-circle" alt="img" />`;
                            } else {
                                // fallback colored circle with first letter
                                const bgColor = getColorFromName(person.name);
                                const firstChar = person.name.charAt(0).toUpperCase();
                                avatarHtml = `
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="background-color: ${bgColor}; width: 40px; height: 40px; font-weight: 600; color: white; font-size: 18px;">
                            ${firstChar}
                        </div>`;
                            }

                            const cardHtml = `
                    <div class="bg-light p-2 border border-dashed rounded-top mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <a href="javascript:void(0);" class="avatar me-2" style="width: 40px; height: 40px;">
                                    ${avatarHtml}
                                </a>
                                <div class="ms-2 overflow-hidden">
                                    <h6 class="fs-medium">${person.name}</h6>
                                    <p class="fs-13">${person.designation || 'N/A'}</p>
                                </div>
                            </div>
                            <a href="javascript:void(0);" class="btn btn-secondary btn-xs"><i class="ti ti-cake me-1"></i>Send</a>
                        </div>
                    </div>`;

                            container.append(cardHtml);
                        });
                    });
                }

                var attendanceChart = null;
                var taskStatisticsChart = null;

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
                            labels: ['Late', 'Present', 'Permission', 'Absent'],
                            datasets: [{
                                label: 'Semi Donut',
                                data: [0, 0, 0, 0], // Initial zero values
                                backgroundColor: ['#0C4B5E', '#03C95A', '#FFC107', '#E70D0D'],
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

                if ($('#mySemiDonutChart').length > 0) {
                    const ctxTask = document.getElementById('mySemiDonutChart').getContext('2d');
                    // ✅ Modern way to destroy existing chart on this canvas (fix for "Canvas already in use")
                    let existingTaskChart = Chart.getChart("mySemiDonutChart");
                    if (existingTaskChart) {
                        existingTaskChart.destroy();
                    }
                    
                    taskStatisticsChart = new Chart(ctxTask, {
                        type: 'doughnut',
                        data: {
                            labels: ['Ongoing', 'On Hold', 'Overdue', 'Completed', 'Late'],
                            datasets: [{
                                label: 'Tasks',
                                data: [0, 0, 0, 0, 0],
                                backgroundColor: ['#FFC107', '#17A2B8', '#DC3545', '#28A745', '#6C757D'],
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

                function updateAttendanceBreakdown(breakdown) {
                    if (!breakdown) return;

                    const totalCount = Object.values(breakdown).reduce((sum, item) => sum + (item.count || 0), 0);
                    $('#total_attendance').text(totalCount);

                    $('#present_percentage').text(`${breakdown.present?.percentage.toFixed(2) ?? 0}%`);
                    $('#late_percentage').text(`${breakdown.late?.percentage.toFixed(2) ?? 0}%`);
                    $('#permission_percentage').text(`${breakdown.permission?.percentage.toFixed(2) ?? 0}%`);
                    $('#absent_percentage').text(`${breakdown.absent?.percentage.toFixed(2) ?? 0}%`);

                    // ✅ Update the chart data dynamically
                    if (attendanceChart) {
                        attendanceChart.data.datasets[0].data = [
                            breakdown.late?.count || 0,
                            breakdown.present?.count || 0,
                            breakdown.permission?.count || 0,
                            breakdown.absent?.count || 0
                        ];
                        attendanceChart.update();
                    }
                }

                function updateTaskStatistics(stats) {
                    if (!stats) return;

                    const total = stats.total || 0;
                    const completed = stats.completed || 0;
                    const ongoing = stats.ongoing || 0;
                    const onHold = stats.on_hold || 0;
                    const overdue = stats.overdue || 0;
                    const lateTask = stats.late_task || 0;

                    const failedTask = stats.failed_task || 0;

                    const open = stats.open_count || 0;
                    const totalClosed = completed + lateTask;
                    const totalTasksForPercent = open + totalClosed;

                    const completedPercent = total > 0 ? ((completed / total) * 100).toFixed(0) : 0;
                    const ongoingPercent = total > 0 ? ((ongoing / total) * 100).toFixed(0) : 0;
                    const onHoldPercent = total > 0 ? ((onHold / total) * 100).toFixed(0) : 0;
                    const overduePercent = total > 0 ? ((overdue / total) * 100).toFixed(0) : 0;
                    const latePercent = total > 0 ? ((lateTask / total) * 100).toFixed(0) : 0;
                    
                    const overviewPercent = totalTasksForPercent > 0 ? ((totalClosed / totalTasksForPercent) * 100).toFixed(0) : 0;

                    // Update widget (top row): Show "Open / Closed"
                    $('#open_tasks_widget_top').text(open);
                    $('#closed_tasks_widget_top').text(totalClosed);
                    $('#open_tasks_widget').text(open);
                    $('#tasks_percentage_widget').html(`<i class="fa-solid fa-caret-up me-1"></i>${overviewPercent}%`);

                    // Update main statistics card
                    $('#task_stat_failed').text(failedTask);
                    $('#task_percent_ongoing').text(`${ongoingPercent}%`);
                    $('#task_percent_onhold').text(`${onHoldPercent}%`);
                    $('#task_percent_overdue').text(`${overduePercent}%`);
                    $('#task_percent_completed').text(`${completedPercent}%`);
                    $('#task_percent_late').text(`${latePercent}%`);

                    // Update Chart
                    if (taskStatisticsChart) {
                        taskStatisticsChart.data.datasets[0].data = [ongoing, onHold, overdue, completed, lateTask];
                        taskStatisticsChart.update();
                    }

                    // Dynamically calculate Spent on Overall Tasks (Hours spent on completed vs total tasks)
                    // Assuming average task duration standard is 1 hour or based on closed tasks
                    const spentHours = (completed + lateTask);
                    const totalEstimatedHours = total > 0 ? total : open;
                    if (totalEstimatedHours > 0) {
                        $('#task_spent_hours_text').text(`${spentHours}/${totalEstimatedHours} hrs`);
                    } else {
                        $('#task_spent_hours_text').text(`${spentHours} hrs`);
                    }
                    
                    const activePeriodText = $('#dashboardDateRange').val() ? 'Selected Period' : 'This Week';
                    $('#task_spent_subtext').text(`Spent on Overall Tasks ${activePeriodText}`);
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
                        success: function (res) {
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
                            initializeSegmentsFromHourlyChart(hourlyChart, shiftStart, shiftEnd, inTime,
                                outTime);

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

                            // Set profile image or fallback to letter avatar
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
                        error: function () {
                            $('#attendance_report .modal-body').html(
                                '<p class="text-danger">Failed to load data.</p>');
                        }
                    });
                }

                $(document).on('click', '.custom-btn-close', function () {
                    const modalId = '#attendance_report';
                    const modalEl = document.querySelector(modalId);
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                    setTimeout(() => {
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                    }, 100);
                });

                $(document).on('click', '.clickable-production', function () {
                    const attendanceId = $(this).data('id');
                    const modal = new bootstrap.Modal(document.getElementById('attendance_report'));
                    modal.show();
                    renderRealtimeProgressBar(attendanceId);
                });





                function renderRecentActivities(activities) {
                    if (!activities || activities.length === 0) {
                        $('#recent-activity-list').html('<p class="text-center text-muted p-2">No recent activities</p>');
                        return;
                    }

                    let html = '';
                    activities.forEach(activity => {
                        // Use a sane default or UI Avatar if no image
                        let imgSrc = activity.user_image ? activity.user_image : `https://ui-avatars.com/api/?name=${encodeURIComponent(activity.user_name)}&background=random`;

                        html += `
                                    <div class="recent-item mb-3">
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex align-items-center w-100">
                                                <a href="javascript:void(0);" class="avatar flex-shrink-0">
                                                    <img src="${imgSrc}" class="rounded-circle" alt="img" />
                                                </a>
                                                <div class="ms-2 flex-fill">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <h6 class="fs-medium text-truncate"><a href="javascript:void(0);">${activity.user_name}</a></h6>
                                                        <p class="fs-13 text-muted">${activity.time}</p>
                                                    </div>
                                                    <p class="fs-13 mb-0 text-truncate" title="${activity.subject}">${activity.subject}</p>
                                                    <small class="text-muted">${activity.date}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                    });

                    $('#recent-activity-list').html(html);
                }

                function renderDepartmentChart(counts) {
                    if (!counts || Object.keys(counts).length === 0) {
                        $('#emp-department-chart').html('<p class="text-center text-muted p-3">No data available</p>');
                        return;
                    }

                    const labels = Object.keys(counts);
                    const data = Object.values(counts);

                    var sBar = {
                        chart: {
                            height: 220,
                            type: 'bar',
                            padding: {
                                top: 0,
                                left: 0,
                                right: 0,
                                bottom: 0
                            },
                            toolbar: {
                                show: false,
                            }
                        },
                        colors: ['#FF6F28'],
                        grid: {
                            borderColor: '#E5E7EB',
                            strokeDashArray: 5,
                            padding: {
                                top: -20,
                                left: 0,
                                right: 0,
                                bottom: 0
                            }
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 5,
                                horizontal: true,
                                barHeight: '35%',
                                endingShape: 'rounded'
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        series: [{
                            data: data,
                            name: 'Employees'
                        }],
                        xaxis: {
                            categories: labels,
                            labels: {
                                style: {
                                    colors: '#111827',
                                    fontSize: '13px',
                                },
                                formatter: function (val) {
                                    return Math.floor(val);
                                }
                            },
                            min: 0,
                            max: Math.max(100, Math.max(...data)),
                            tickAmount: 5
                        }
                    };

                    document.querySelector("#emp-department-chart").innerHTML = "";
                    var chart = new ApexCharts(document.querySelector("#emp-department-chart"), sBar);
                    chart.render();
                }

                // Initial load
                if ($('.bookingrange').length > 0) {
                    var start = moment();
                    var end = moment();

                    function cb(start, end) {
                        $('#dashboardDateRange').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
                    }

                    $('.bookingrange').daterangepicker({
                        startDate: start,
                        endDate: end,
                        ranges: {
                            'Today': [moment(), moment()],
                            'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
                            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                            'This Year': [moment().startOf('year'), moment().endOf('year')]
                        }
                    }, cb);

                    cb(start, end);

                    $('.bookingrange').on('apply.daterangepicker', function(ev, picker) {
                        fetchAttendance();
                    });
                    
                    // Call immediately on page load
                    fetchAttendance();
                } else {
                    fetchAttendance();
                }

                // Leave logic
                function calculateTotalDays(fromSelector, toSelector, totalSelector) {
                    let fromDate = $(fromSelector).val();
                    let toDate = $(toSelector).val();

                    if (fromDate && toDate) {
                        let start = moment(fromDate, 'DD/MM/YYYY');
                        let end = moment(toDate, 'DD/MM/YYYY');
                        if (end.isSameOrAfter(start)) {
                            let days = end.diff(start, 'days') + 1;
                            $(totalSelector).val(days);
                        } else {
                            $(totalSelector).val(0);
                        }
                    } else {
                        $(totalSelector).val('');
                    }
                }

                $('#add_leaves').on('change', '#add_from_date, #add_to_date', function () {
                    calculateTotalDays("#add_from_date", "#add_to_date", "#add_total_days");
                });
                
                $('#add_leaves').on('dp.change', '#add_from_date, #add_to_date', function () {
                    calculateTotalDays("#add_from_date", "#add_to_date", "#add_total_days");
                });
                
                $('#add_leave_type, select[name="employee_id"]').on('change', function () {
                    let leaveTypeId = $('#add_leave_type').val();
                    let employeeId = $('select[name="employee_id"]').val();
                    if (!leaveTypeId || !employeeId) {
                        $('#add_leave_balance').val('');
                        return;
                    }
                    $.ajax({
                        url: '{{ route('get.leave.balance', ['box_slug' => request()->route('box_slug')]) }}',
                        method: 'GET',
                        data: { leave_type_id: leaveTypeId, employee_id: employeeId },
                        success: function (res) {
                            if (res.status && res.data) {
                                $('#add_leave_balance').val(res.data.balance_leave || '0');
                            } else {
                                $('#add_leave_balance').val('0');
                            }
                        },
                        error: function () { $('#add_leave_balance').val('0'); }
                    });
                });

                $('#add_leaves_form').on('submit', function (e) {
                    e.preventDefault();
                    let form = $(this);
                    
                    // Check if extra leave logic should proceed
                    let formData = new FormData(form[0]);

                    $.ajax({
                        type: 'POST',
                        url: '{{ route('leave.request.store', ['box_slug' => request()->route('box_slug')]) }}',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(res) {
                            if (res.status || res.success) {
                                showToast(res.message || 'Leave request submitted successfully!', 'success');
                                $('#add_leaves').modal('hide');
                                form.trigger('reset');
                                setTimeout(function() { location.reload(); }, 1000);
                            } else {
                                showToast(res.message || 'Failed to submit leave request.', 'error');
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422 && xhr.responseJSON.extra_leave) {
                                if (confirm(xhr.responseJSON.message + "\nDo you want to proceed as unpaid leave?")) {
                                    formData.append('proceed_as_unpaid', 1);
                                    $.ajax({
                                        type: 'POST',
                                        url: '{{ route('leave.request.store', ['box_slug' => request()->route('box_slug')]) }}',
                                        data: formData,
                                        processData: false,
                                        contentType: false,
                                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                        success: function(resp) {
                                            if (resp.status || resp.success) {
                                                showToast(resp.message || 'Leave request submitted successfully as unpaid leave!', 'success');
                                                $('#add_leaves').modal('hide');
                                                form.trigger('reset');
                                                setTimeout(function() { location.reload(); }, 1000);
                                            } else {
                                                showToast(resp.message || 'Failed to submit leave request.', 'error');
                                            }
                                        }
                                    });
                                }
                            } else {
                                showToast(xhr.responseJSON?.message || 'Failed to submit leave request.', 'error');
                            }
                        }
                    });
                });
            });
        </script>
    @endpush

@endsection