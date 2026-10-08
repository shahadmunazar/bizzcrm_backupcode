    @extends('layouts.main')
    @section('content')
    @section('title', 'Attendance Shift')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col-4">
                                <h4 class="page-title">Shift Master</h4>
                            </div>
                            <div class="col-8 text-end">
                                <div class="head-icons">
                                    <a href="activities.html" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-original-title="Refresh">
                                        <i class="ti ti-refresh-dot"></i>
                                    </a>
                                    <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-original-title="Collapse" id="collapse-header">
                                        <i class="ti ti-chevrons-up"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-sm-4">
                                    <div class="icon-form mb-3 mb-sm-0">
                                        <span class="form-icon"><i class="ti ti-search"></i></span>
                                        <input type="text" class="form-control" id="SearchShitt"
                                            placeholder="Search Shift">
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="text-sm-end">
                                        <button type="button" id="generateShiftTrigger" class="btn bg-soft-purple text-purple">
                                            <i class="ti ti-square-rounded-plus me-2"></i>Generate Shift For All Employee
                                        </button>
                                        <div class="form-check form-check-inline align-items-center ms-3">
                                            <input class="form-check-input" type="checkbox" id="confirmGenerateShift">
                                            <label class="form-check-label fs-13 mb-0" for="confirmGenerateShift">
                                                I confirm attendance regeneration
                                            </label>
                                        </div>
                                        <a href="javascript:void(0);" id="clearForm" class="btn btn-primary"
                                            data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i
                                                class="ti ti-square-rounded-plus me-2"></i>Add Shift</a>
                                    </div>
                                </div>
                            </div>
                            <!-- /Search -->
                        </div>

                        <div class="card-body">
                            <!-- Filter -->
                            <div class="d-flex align-items-center justify-content-between flex-wrap mb-4 row-gap-2">
                                <div class="d-flex align-items-center flex-wrap row-gap-2">
                                    <div class="d-flex align-items-center flex-wrap row-gap-2">
                                    </div>
                                </div>
                                <div class="d-flex align-items-center flex-wrap row-gap-2">
                                    <button type="button" class="btn btn-sm btn-white d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                                        <i class="ti ti-columns me-1"></i>Manage Columns
                                    </button>
                                </div>
                            </div>
                            <!-- /Filter -->
                            <!-- goo List -->
                            <table id="shifts-table">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Sr No.</th>
                                        <th>Shift Code</th>
                                        <th>Shift Name</th>
                                        <th>Shift In</th>
                                        <th>Shift Out</th>
                                        <th>Shift Hrs.</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Shift Enabled</th>
                                        <th>Rest Enabled</th>
                                        <th>Lunch Out</th>
                                        <th>Lunch In</th>
                                        <th>Lunch Hours</th>
                                        <th>Short Break Enabled</th>
                                        <th>Break Out</th>
                                        <th>Break In</th>
                                        <th>Break Hours</th>
                                        <th>Break 2 Enabled</th>
                                        <th>Break 2 Out</th>
                                        <th>Break 2 In</th>
                                        <th>Break 2 Hours</th>
                                        <th>Half Day Enabled</th>
                                        <th>Half Day Start</th>
                                        <th>Half Day End</th>
                                        <th>Half Day Hours</th>
                                        <th>Absent Below</th>
                                        <th>Half Day Below</th>
                                        <th>Absent Deduction</th>
                                        <th>Half Day Deduction</th>
                                        <th>Short Leave Deduction</th>
                                        <th>Short Leave Below</th>
                                        <th>Overtime Allowed</th>
                                        <th>Weekly Off 1</th>
                                        <th>Weekly Off 2</th>
                                        <th>Weekly Off 3</th>
                                        <th>Weekly Off 4</th>
                                        <th>Check Over Time</th>
                                        <th>Saturday Offs</th>
                                        <th>Saturday Half Days</th>
                                        <th>Half Day Time</th>
                                        <th>Early Login Punch In</th>
                                        <th>Early Login Minutes</th>
                                        <th>Max Punch In Allowed</th>
                                        <th>Auto Punchout Time</th>
                                        <th>All In Out Calc</th>
                                        <th>First In Last Out Calc</th>
                                        <th>Relaxation Time</th>
                                        <th>Max Relaxations</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>

                            <!-- /Activity List -->

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /Page Wrapper -->

    <!-- Shift -->
    <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
        <div class="offcanvas-header border-bottom">
            <h4>Add Shift Information Entry </h4>
            <button type="button"
                class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
                data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form id="activities_shift" data-weekly-off-form method="POST">
                <div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">

                                        <div class="card-body d-sm-flex align-item-center justify-content-between"
                                            style="padding-left:0px; padding-right:0;">
                                            <h5 class="mb-2">Shift Time</h5>
                                            <div class="swithc-btns">
                                                <div class="form-check form-check-md form-switch"
                                                    style="cursor:pointer;">
                                                    <input type="hidden" name="shift_enabled" value="0">
                                                    <label class="form-check-label float-start" for="switch-md1"
                                                        style="margin-right: 50px; margin-top:0; cursor:pointer;">Disable</label>
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        name="shift_enabled" value="1" id="shift_enableds">
                                                    <label class="form-check-label float-end" for="switch-md1"
                                                        style="margin-left:13px; margin-top:0; cursor:pointer;">Enable</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Code </label>
                                                    <input type="text" name="code" class="form-control"
                                                        placeholder="X">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Shift </label>
                                                    <input type="text" name="shift_name" id=""
                                                        class="form-control">

                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">In Time </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!--<input type="text" id="in_times" name="in_time"
                                                            class="form-control datetimepicker-time"> -->
                                                        <input type="text" id="in_times" name="in_time"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Out Time </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!--<input type="text" id="out_times" name="out_time"
                                                            class="form-control datetimepicker-time"> -->
                                                        <input type="text" id="out_times" name="out_time"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Hrs. </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <input type="text" id="shift_hourss" name="shift_hours"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-3">Effective Period</h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="col-form-label">Start From</label>
                                                <input type="text" name="start_from_date" class="form-control datePicker">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-form-label">End Date</label>
                                                <input type="text" name="end_date" class="form-control datePicker">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-body d-sm-flex align-item-center justify-content-between"
                                            style="padding-left:0px; padding-right:0;">
                                            <h5 class="mb-2">Rest Time</h5>
                                            <div class="swithc-btns">
                                                <div class="form-check form-check-md form-switch">
                                                    <!-- Hidden input ensures 0 is sent when unchecked -->
                                                    <input type="hidden" name="rest_enabled" value="0">
                                                    <label class="form-check-label float-start" for="switch-md2"
                                                        style="margin-right: 50px; margin-top:0; cursor:pointer;">Disable</label>
                                                    <input class="form-check-input" name="rest_enabled"
                                                        id="rest_timeEnable" type="checkbox" value="1"
                                                        role="switch" id="switch-md2">
                                                    <label class="form-check-label float-end" for="switch-md2"
                                                        style="margin-left:13px; margin-top:0; cursor:pointer;">Enable</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Lunch Out </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!--<input type="text" name="lunch_out" id="lunch_outs"
                                                            class="form-control datetimepicker-time">-->
                                                        <input type="text" name="lunch_out" id="lunch_outs"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Lunch In </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!--<input type="text" name="lunch_in" id="lunch_ins"
                                                            class="form-control datetimepicker-time"> -->
                                                        <input type="text" name="lunch_in" id="lunch_ins"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Hrs. </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <input type="text" name="lunch_hours" id="lunch_hourss"
                                                            class="form-control datetimepicker-time">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-body d-sm-flex align-item-center justify-content-between"
                                            style="padding-left:0px; padding-right:0;">
                                            <h5 class="mb-2">Short Break</h5>
                                            <div class="swithc-btns">
                                                <div class="form-check form-check-md form-switch"
                                                    style="cursor:pointer;">
                                                    <input type="hidden" name="short_break_enabled" value="0">
                                                    <label class="form-check-label float-start" for="switch-md3"
                                                        style="margin-right: 50px; margin-top:0; cursor:pointer;">Disable</label>
                                                    <input class="form-check-input" value="1"
                                                        name="short_break_enabled" type="checkbox" role="switch"
                                                        id="short_break_enable">
                                                    <label class="form-check-label float-end" for="switch-md3"
                                                        style="margin-left:13px; margin-top:0; cursor:pointer;">Enable</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Short Break 1 Out</label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!-- <input type="text" name="short_break_out"
                                                            id="short_break_outss"
                                                            class="form-control datetimepicker-time">-->
                                                        <input type="text" name="short_break_out"
                                                            id="short_break_outss"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Short Break 1 In </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!-- <input type="text" name="short_break_in"
                                                            id="short_break_inss"
                                                            class="form-control datetimepicker-time"> -->
                                                        <input type="text" name="short_break_in"
                                                            id="short_break_inss"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Hrs. </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <input type="text" name="short_break_hours"
                                                            id="short_break_hourss" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-body d-sm-flex align-item-center justify-content-between"
                                            style="padding-left:0px; padding-right:0;">
                                            <h5 class="mb-2">2nd Short Break</h5>
                                            <div class="swithc-btns">
                                                <div class="form-check form-check-md form-switch"
                                                    style="cursor:pointer;">
                                                    <input type="hidden" name="short_break_enabled_2"
                                                        value="0">
                                                    <label class="form-check-label float-start"
                                                        for="short_break_enable_2"
                                                        style="margin-right: 50px; margin-top:0; cursor:pointer;">Disable</label>
                                                    <input class="form-check-input" value="1"
                                                        name="short_break_enabled_2" type="checkbox" role="switch"
                                                        id="short_break_enable_2">
                                                    <label class="form-check-label float-end"
                                                        for="short_break_enable_2"
                                                        style="margin-left:13px; margin-top:0; cursor:pointer;">Enable</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Short Break 2 Out</label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!-- <input type="text" name="short_break_out_2"
                                                            id="short_break_out_2"
                                                            class="form-control datetimepicker-time"> -->
                                                        <input type="text" name="short_break_out_2"
                                                            id="short_break_out_2"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Short Break 2 In</label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!--<input type="text" name="short_break_in_2"
                                                            id="short_break_in_2"
                                                            class="form-control datetimepicker-time">-->
                                                        <input type="text" name="short_break_in_2"
                                                            id="short_break_in_2"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Hrs.</label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <input type="text" name="short_break_hours_2"
                                                            id="short_break_hours_2" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-body d-sm-flex align-item-center justify-content-between"
                                            style="padding-left:0px; padding-right:0;">
                                            <h5 class="mb-2">Half Day</h5>
                                            <div class="swithc-btns">
                                                <div class="form-check form-check-md form-switch"
                                                    style="cursor:pointer;">
                                                    <input type="hidden" name="half_day_enabled" value="0">
                                                    <label class="form-check-label float-start" for="switch-md4"
                                                        style="margin-right: 50px;margin-top:0;cursor:pointer;">Disable</label>
                                                    <input class="form-check-input" name="half_day_enabled"
                                                        value="1" type="checkbox" role="switch"
                                                        id="half_dayEnable">
                                                    <label class="form-check-label float-end" for="switch-md4"
                                                        style="margin-left:13px; margin-top:0;cursor:pointer;">Enable</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Start Time </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!-- <input type="text" name="half_day_start_time"
                                                            id="half_day_start_timess"
                                                            class="form-control datetimepicker-time"> -->
                                                        <input type="text" name="half_day_start_time"
                                                            id="half_day_start_timess"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">End Time </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!-- <input type="text" name="half_day_end_time"
                                                            id="half_day_end_timess"
                                                            class="form-control datetimepicker-time"> -->
                                                        <input type="text" name="half_day_end_time"
                                                            id="half_day_end_timess"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Hrs. </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <input type="text" name="half_day_hours"
                                                            id="half_day_hourss" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-3 fw-bold">Deduction Rules Based on Working Hours</h5>

                                        <!-- Absent Section -->
                                        <div class="row g-3 align-items-end mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    Mark as <strong>Absent</strong> if working hours are less than:
                                                </label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="input-group">
                                                            <input type="number" name="absent_below_hours" class="form-control" placeholder="Hours" min="0">
                                                            <span class="input-group-text">H</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="input-group">
                                                            <input type="number" name="absent_below_minutes" class="form-control" placeholder="Min" min="0" max="59">
                                                            <span class="input-group-text">M</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    Attendance deduction for <strong>Absence</strong> (in %):
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" name="absent_deduction"
                                                        class="form-control" placeholder="Enter like 1.0 for 1%"
                                                        pattern="^\d+(\.\d+)?$"
                                                        title="Enter a decimal value. Example: 1.0 for 1% deduction, 0.5 for 0.5%">
                                                    <span class="input-group-text"><i
                                                            class="ti ti-percentage"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Half Day Section -->
                                        <div class="row g-3 align-items-end mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    Mark as <strong>Half Day Present</strong> if working hours are less
                                                    than:
                                                </label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="input-group">
                                                            <input type="number" name="half_day_below_hours" class="form-control" placeholder="Hours" min="0">
                                                            <span class="input-group-text">H</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="input-group">
                                                            <input type="number" name="half_day_below_minutes" class="form-control" placeholder="Min" min="0" max="59">
                                                            <span class="input-group-text">M</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    Attendance deduction for <strong>Half Day</strong> (in %):
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" name="half_day_deduction"
                                                        class="form-control" placeholder="Enter like 0.5 for 0.5%"
                                                        pattern="^\d+(\.\d+)?$"
                                                        title="Enter a decimal value. Example: 0.5 for 0.5% deduction">
                                                    <span class="input-group-text"><i
                                                            class="ti ti-percentage"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Short Leave Section -->
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    Mark as <strong>Short Leave</strong> if working hours are less than:
                                                </label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="input-group">
                                                            <input type="number" name="short_leave_below_hours" class="form-control" placeholder="Hours" min="0">
                                                            <span class="input-group-text">H</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="input-group">
                                                            <input type="number" name="short_leave_below_minutes" class="form-control" placeholder="Min" min="0" max="59">
                                                            <span class="input-group-text">M</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    Attendance deduction for <strong>Short Leave</strong> (in %):
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" name="short_leave_deduction"
                                                        class="form-control" placeholder="Enter like 0.25 for 0.25%"
                                                        pattern="^\d+(\.\d+)?$"
                                                        title="Enter a decimal value. Example: 0.25 for 0.25% deduction">
                                                    <span class="input-group-text"><i
                                                            class="ti ti-percentage"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2">Relaxation Time</h5>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-form-label">Relaxation Time</label>
                                                <div class="mb-3 icon-form">
                                                    
                                                    <span class="form-icon"><i class="ti ti-clock-hour-10"></i></span>
                                                    <input type="text" name="relaxation_time"
                                                        id="relaxation_times" class="form-control MyDurationPicker">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-form-label">Max Relaxations Allowed</label>
                                                <div class="mb-3 icon-form">
                                                    <span class="form-icon"><i class="ti ti-list-numbers"></i></span>
                                                    <input type="number" min="0" max="31" name="max_relaxations_allowed"
                                                        id="max_relaxations_allowed_add" class="form-control" placeholder="e.g. 4">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2">Overtime Allow</h5>
                                        <div class="form-check form-switch mb-4">
                                            <input type="hidden" name="check_over_time" value="0">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="check_over_time" id="allowOvertimeSwitch">
                                            <label class="form-check-label" for="allowOvertimeSwitch">Enable Overtime
                                                Fields</label>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Max Overtime Allowed</label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <input type="text" name="overtime_allowed"
                                                            id="overtimeAllowedInput" class="form-control MyDurationPicker" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-3">Auto Punch Out Time After Shift End</h5>
                                        <p class="mb-3 text-muted">
                                            If an employee forgets to punch out, automatically punch them out after this
                                            time.
                                        </p>
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Auto Punch Out Time (Exact Time)</label>
                                                <input type="time" id="auto_punchout_time"
                                                    name="auto_punchout_time" class="form-control"
                                                    placeholder="Enter time" step="1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-3">Max Punch In Allowed</h5>
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Maximum Punch Ins Allowed Per Day</label>
                                                <input type="text" id="max_punch_in_allowed"
                                                    name="max_punch_in_allowed" class="form-control"
                                                    min="1" max="100" step="1"
                                                    placeholder="Enter max punch-ins allowed per day">
                                                <small class="text-muted">Maximum number of punch-ins allowed per day (e.g., 10, 20, 50)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2">Punch Calculation Mode <span class="text-danger">*</span></h5>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" value="all_in_out"
                                                name="punch_calculation_mode" id="allInOutCalculation" required>
                                            <label class="form-check-label" for="allInOutCalculation">Calculate All In/Out (Last as Out)</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" value="first_in_last_out"
                                                name="punch_calculation_mode" id="firstInLastOutCalculation" required>
                                            <label class="form-check-label" for="firstInLastOutCalculation">Calculate First In / Last Out</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2">Weekly Off</h5>
                                        <div id="weeklyOffContainer" class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="col-form-label">1st Weekly Off</label>
                                                    <select class="form-select" name="weekly_off_1">
                                                        <option value="" disabled selected>-- Select Day --
                                                        </option>
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
                                                <div class="mb-3">
                                                    <label class="col-form-label">2nd Weekly Off</label>
                                                    <select class="form-select" name="weekly_off_2">
                                                        <option value="" disabled selected>-- Select Day --
                                                        </option>
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
                                                <div class="mb-3">
                                                    <label class="col-form-label">3rd Weekly Off</label>
                                                    <select class="form-select" name="weekly_off_3">
                                                        <option value="" disabled selected>-- Select Day --
                                                        </option>
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
                                                <div class="mb-3">
                                                    <label class="col-form-label">4th Weekly Off</label>
                                                    <select class="form-select" name="weekly_off_4">
                                                        <option value="" disabled selected>-- Select Day --
                                                        </option>
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
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2" id="saturdayHalfDayHeading"></h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="d-flex flex-wrap">
                                                        <div class="me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="saturday_offs[]" value="1st" id="">
                                                            <label for=""> 1st</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="saturday_offs[]" value="2nd" id="">
                                                            <label for=""> 2nd</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="saturday_offs[]" value="3rd" id="">
                                                            <label for=""> 3rd</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="saturday_offs[]" value="4th" id="">
                                                            <label for=""> 4th</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="saturday_offs[]" value="5th" id="">
                                                            <label for=""> 5th</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2" id="saturdayDayHeading">Saturday Half Day</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="d-flex flex-wrap">
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="saturday_half_days[]" type="checkbox"
                                                                value="1st" id="">
                                                            <label for=""> 1st</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="saturday_half_days[]" type="checkbox"
                                                                value="2nd" id="">
                                                            <label for=""> 2nd</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="saturday_half_days[]" type="checkbox"
                                                                value="3rd" id="">
                                                            <label for=""> 3rd</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="saturday_half_days[]" type="checkbox"
                                                                value="4th" id="">
                                                            <label for=""> 4th</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="saturday_half_days[]" type="checkbox"
                                                                value="5th" id="">
                                                            <label for=""> 5th</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2" id="saturdayDayHeading">Half Day From</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="d-flex flex-wrap">
                                                        <div class="me-3">
                                                            <input class="form-check-input" name="half_day_time"
                                                                type="radio" value="1st half" id="">
                                                            <label for="">1st half</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input" name="half_day_time"
                                                                type="radio" value="2nd half" id="">
                                                            <label for="">2nd Half</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5 class="mb-2" id="saturdayDayHeading">Early Login Punch In Allowed</h5>
                                                <div class="mb-3">
                                                    <div class="d-flex flex-wrap">
                                                        <div class="me-3">
                                                            <input class="form-check-input" name="early_login_punchIn"
                                                                type="radio" value="yes" id="earlyYes">
                                                            <label for="earlyYes">Yes</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input" name="early_login_punchIn"
                                                                type="radio" value="no" id="earlyNo">
                                                            <label for="earlyNo">No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6" id="earlyMinutesContainer" style="display: none;">
                                                <div class="mb-3">
                                                    <label class="col-form-label" for="early_minutes">If Yes, enter minutes allowed</label>
                                                    <input type="text" id="early_minutes"
                                                        name="early_login_minutes" class="form-control MyDurationPicker"
                                                        placeholder="Enter minutes">
                                                    <small class="text-muted">Enter how many minutes early punch-in is
                                                        allowed.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>







                        </div>

                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-end">
                    <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>



    <!-- Shift -->
    <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
        <div class="offcanvas-header border-bottom">
            <h4>Shift Information Entry/Edits </h4>
            <button type="button"
                class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
                data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form id="activities_shift_edit" data-weekly-off-form method="POST">
                <div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <input type="hidden" name="shift_id" id="id" value="">

                                        <div class="card-body d-sm-flex align-item-center justify-content-between"
                                            style="padding-left:0px; padding-right:0;">
                                            <h5 class="mb-2">Shift Time</h5>
                                            <div class="swithc-btns">
                                                <div class="form-check form-check-md form-switch"
                                                    style="cursor:pointer;">
                                                    <input type="hidden" name="shift_enabled" value="0">
                                                    <label class="form-check-label float-start" for="switch-md5"
                                                        style="margin-right: 50px;margin-top:0;cursor:pointer;">Disable</label>
                                                    <input class="form-check-input" id="shift_enabled"
                                                        name="shift_enabled" value="1" type="checkbox"
                                                        role="switch">
                                                    <label class="form-check-label float-end" for="switch-md5"
                                                        style="margin-left:13px; margin-top:0;cursor:pointer;">Enable</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Code </label>
                                                    <input type="text" id="shift_code" name="shift_code"
                                                        class="form-control" placeholder="X">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Shift </label>
                                                    <input type="text" name="shift_type" id="shift_type"
                                                        class="form-control">
                                                    {{-- <select class="select" id="shift_type" name="shift_type">
                                                        <option value="General Shift">General Shift</option>
                                                        <option value="Night Shift">Night Shift</option>
                                                    </select> --}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">In Time </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!--<input type="text" id="shift_in_time" name="shift_in_time"
                                                            class="form-control datetimepicker-time"> -->
                                                        <input type="text" id="shift_in_time" name="shift_in_time"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Out Time </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!--<input type="text" id="shift_out_time"
                                                            name="shift_out_time"
                                                            class="form-control datetimepicker-time"> -->
                                                        <input type="text" id="shift_out_time"
                                                            name="shift_out_time"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Hrs. </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <input type="text" id="shift_hours" name="shift_hours"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-3">Effective Period</h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="col-form-label">Start From</label>
                                                <input type="text" name="start_from_date" id="start_from_date_edit"
                                                    class="form-control datePicker">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-form-label">End Date</label>
                                                <input type="text" name="end_date" id="end_date_edit"
                                                    class="form-control datePicker">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-body d-sm-flex align-item-center justify-content-between"
                                            style="padding-left:0px; padding-right:0;">
                                            <h5 class="mb-2">Rest Time</h5>
                                            <div class="swithc-btns">
                                                <div class="form-check form-check-md form-switch">
                                                    <input type="hidden" name="rest_enabled" value="0">
                                                    <label class="form-check-label float-start" for="switch-md6"
                                                        style="margin-right: 50px;margin-top:0;cursor:pointer;">Disable</label>
                                                    <input class="form-check-input" id="rest_enabled"
                                                        name="rest_enabled" type="checkbox" value="1"
                                                        role="switch">
                                                    <label class="form-check-label float-end" for="switch-md6"
                                                        style="margin-left:13px; margin-top:0;cursor:pointer;">Enable</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Lunch Out </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!-- <input type="text" id="lunch_out" name="lunch_out"
                                                            class="form-control datetimepicker-time"> -->
                                                        <input type="text" id="lunch_out" name="lunch_out"
                                                            class="form-control MyTimePicker">

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Lunch In </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!--<input type="text" id="lunch_in" name="lunch_in"
                                                            class="form-control datetimepicker-time"> -->
                                                        <input type="text" id="lunch_in" name="lunch_in"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Hrs. </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <input type="text" id="lunch_hours" name="lunch_hours"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-body d-sm-flex align-item-center justify-content-between"
                                            style="padding-left:0px; padding-right:0;">
                                            <h5 class="mb-2">Short Break</h5>
                                            <div class="swithc-btns">
                                                <div class="form-check form-check-md form-switch"
                                                    style="cursor:pointer;">
                                                    <input type="hidden" name="short_break_enabled" value="0">
                                                    <label class="form-check-label float-start" for="switch-md7"
                                                        style="margin-right: 50px;margin-top:0;cursor:pointer;">Disable</label>
                                                    <input class="form-check-input" id="short_break_enabled"
                                                        name="short_break_enabled" value="1" type="checkbox"
                                                        role="switch">

                                                    <label class="form-check-label float-end" for="switch-md7"
                                                        style="margin-left:13px; margin-top:0;cursor:pointer;">Enable</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Short Break 1 Out</label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!-- <input type="text" id="short_break_outs"
                                                            name="short_break_out"
                                                            class="form-control datetimepicker-time"> -->
                                                        <input type="text" id="short_break_outs"
                                                            name="short_break_out"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Short Break 1 In </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!--<input type="text" id="short_break_in"
                                                            name="short_break_in"
                                                            class="form-control datetimepicker-time">-->
                                                        <input type="text" id="short_break_in"
                                                            name="short_break_in"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Hrs. </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <input type="text" class="form-control"
                                                            id="short_break_hours" name="short_break_hours">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-body d-sm-flex align-item-center justify-content-between"
                                            style="padding-left:0px; padding-right:0;">
                                            <h5 class="mb-2">2nd Short Break</h5>
                                            <div class="swithc-btns">
                                                <div class="form-check form-check-md form-switch"
                                                    style="cursor:pointer;">
                                                    <input type="hidden" name="short_break_enabled_2"
                                                        value="0">
                                                    <label class="form-check-label float-start" for="switch-md8"
                                                        style="margin-right: 50px;margin-top:0;cursor:pointer;">Disable</label>
                                                    <input class="form-check-input" id="short_break_enableds_2"
                                                        name="short_break_enabled_2" value="1" type="checkbox"
                                                        role="switch">
                                                    <label class="form-check-label float-end" for="switch-md8"
                                                        style="margin-left:13px; margin-top:0;cursor:pointer;">Enable</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Short Break 2 Out</label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!--<input type="text" id="short_break_outs_2"
                                                            name="short_break_outs_2"
                                                            class="form-control datetimepicker-time">-->
                                                        <input type="text" id="short_break_outs_2"
                                                            name="short_break_outs_2"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Short Break 2 In</label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!--<input type="text" id="short_break_ins_2"
                                                            name="short_break_ins_2"
                                                            class="form-control datetimepicker-time">-->
                                                        <input type="text" id="short_break_ins_2"
                                                            name="short_break_ins_2"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Hrs.</label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <input type="text" class="form-control"
                                                            id="short_break_hourss_2" name="short_break_hours_2">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>





                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-body d-sm-flex align-item-center justify-content-between"
                                            style="padding-left:0px; padding-right:0;">
                                            <h5 class="mb-2">Half Day</h5>
                                            <div class="swithc-btns">
                                                <div class="form-check form-check-md form-switch"
                                                    style="cursor:pointer;">
                                                    <input type="hidden" name="half_day_enabled" value="0">

                                                    <label class="form-check-label float-start" for="switch-md8"
                                                        style="margin-right: 50px;margin-top:0;cursor:pointer;">Disable</label>
                                                    <input class="form-check-input" id="half_day_enabled"
                                                        name="half_day_enabled" value="1" type="checkbox"
                                                        role="switch">
                                                    <label class="form-check-label float-end" for="switch-md8"
                                                        style="margin-left:13px; margin-top:0;cursor:pointer;">Enable</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Start Time </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!--<input type="text" id="half_day_start_time"
                                                            name="half_day_start_time"
                                                            class="form-control datetimepicker-time">-->
                                                        <input type="text" id="half_day_start_time"
                                                            name="half_day_start_time"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">End Time </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <!--<input type="text" id="half_day_end_time"
                                                            name="half_day_end_time"
                                                            class="form-control datetimepicker-time">-->
                                                        <input type="text" id="half_day_end_time"
                                                            name="half_day_end_time"
                                                            class="form-control MyTimePicker">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Hrs. </label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <input type="text" id="half_day_hours"
                                                            name="half_day_hours" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            {{-- <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2">Deduction Details</h5>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Mark As Absent For Working Hours
                                                        Less
                                                        Than</label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <input type="time" id="absent_hours"
                                                            name="absent_hours" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Mark As Half Day Present For Working
                                                        Hours Less Than</label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <input type="time" id="half_day_limit"
                                                            name="half_day_limit" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Mark As Short Leave If Working Hours
                                                        Less
                                                        Than</label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <input type="time" id="short_leave_limit"
                                                            name="short_leave_limit" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}



                            {{-- //new  --}}
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-3 fw-bold">Deduction Details</h5>
                                        <!-- Absent Section -->
                                        <div class="row g-3 align-items-end mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Mark As Absent For Working Hours Less
                                                    Than</label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="input-group">
                                                            <input type="number" name="absent_below_hours" id="absent_below_hours" class="form-control" placeholder="H" min="0">
                                                            <span class="input-group-text">H</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="input-group">
                                                            <input type="number" name="absent_below_minutes" id="absent_below_minutes" class="form-control" placeholder="M" min="0" max="59">
                                                            <span class="input-group-text">M</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Deduction In Attendance by day of</label>
                                                <div class="input-group">
                                                    <input type="text" name="absent_deduction"
                                                        id="absent_deduction" class="form-control">
                                                    <span class="input-group-text"><i
                                                            class="ti ti-percentage"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Half Day Section -->
                                        <div class="row g-3 align-items-end mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Mark As Half Day Present For Working Hours
                                                    Less Than</label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="input-group">
                                                            <input type="number" name="half_day_limit_hours" id="half_day_limit_hours" class="form-control" placeholder="H" min="0">
                                                            <span class="input-group-text">H</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="input-group">
                                                            <input type="number" name="half_day_limit_minutes" id="half_day_limit_minutes" class="form-control" placeholder="M" min="0" max="59">
                                                            <span class="input-group-text">M</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Deduction In Attendance by day of</label>
                                                <div class="input-group">
                                                    <input type="text" name="half_day_deduction"
                                                        id="half_day_deduction" class="form-control">
                                                    <span class="input-group-text"><i
                                                            class="ti ti-percentage"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Short Leave Section -->
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-6">
                                                <label class="form-label">Mark As Short Leave If Working Hours Less
                                                    Than</label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="input-group">
                                                            <input type="number" name="short_leave_limit_hours" id="short_leave_limit_hours" class="form-control" placeholder="H" min="0">
                                                            <span class="input-group-text">H</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="input-group">
                                                            <input type="number" name="short_leave_limit_minutes" id="short_leave_limit_minutes" class="form-control" placeholder="M" min="0" max="59">
                                                            <span class="input-group-text">M</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Deduction In Attendance by day of</label>
                                                <div class="input-group">
                                                    <input type="text" name="short_leave_deduction"
                                                        id="short_leave_deduction" class="form-control">
                                                    <span class="input-group-text"><i
                                                            class="ti ti-percentage"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2">Relaxation Time</h5>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-form-label">Relaxation Time</label>
                                                <div class="mb-3 icon-form">
                                                    <span class="form-icon"><i
                                                            class="ti ti-clock-hour-10"></i></span>
                                                    <input type="text" name="relaxation_time"
                                                        id="relaxation_time" class="form-control MyDurationPicker">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-form-label">Max Relaxations Allowed</label>
                                                <div class="mb-3 icon-form">
                                                    <span class="form-icon"><i class="ti ti-list-numbers"></i></span>
                                                    <input type="number" min="0" max="31" name="max_relaxations_allowed"
                                                        id="max_relaxations_allowed_edit" class="form-control" placeholder="e.g. 4">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2">Overtime Allow</h5>
                                        <div class="form-check form-switch mb-4">
                                            <input type="hidden" name="check_over_time" value="0">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="check_over_time" id="check_over_time">
                                            <label class="form-check-label" for="allowOvertimeSwitchs">Enable
                                                Overtime
                                                Fields</label>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Max Overtime Allowed</label>
                                                    <div class="mb-3 icon-form">
                                                        <span class="form-icon"><i
                                                                class="ti ti-clock-hour-10"></i></span>
                                                        <input type="text" name="overtime_allowed"
                                                            id="overtime_allowed" class="form-control MyDurationPicker" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>



                            <!-- new field -->
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-3">Auto Punch Out Time After Shift End</h5>
                                        <p class="mb-3 text-muted">
                                            If an employee forgets to punch out, automatically punch them out after this
                                            time.
                                        </p>
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Auto Punch Out Time (Exact Time)</label>
                                                <input type="text" id="editauto_punchout_time"
                                                    name="auto_punchout_time" class="form-control MyTimePicker"
                                                    placeholder="Enter time">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-3">Max Punch In Allowed</h5>
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Maximum Punch Ins Allowed Per Day</label>
                                                <input type="text" id="editmax_punch_in_allowed"
                                                    name="max_punch_in_allowed" class="form-control"
                                                    min="1" max="100" step="1"
                                                    placeholder="Enter max punch-ins allowed per day">
                                                <small class="text-muted">Maximum number of punch-ins allowed per day (e.g., 10, 20, 50)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2">Punch Calculation Mode <span class="text-danger">*</span></h5>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" value="all_in_out"
                                                name="punch_calculation_mode" id="editAllInOutCalculation" required>
                                            <label class="form-check-label" for="editAllInOutCalculation">Calculate All In/Out (Last as Out)</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" value="first_in_last_out"
                                                name="punch_calculation_mode" id="editFirstInLastOutCalculation" required>
                                            <label class="form-check-label" for="editFirstInLastOutCalculation">Calculate First In / Last Out</label>
                                        </div>
                                    </div>
                                </div>
                            </div>






                            {{--
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2">Weekly Off</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="col-form-label">1st Weekly Off</label>
                                                    <select class="select" id="weekly_off_1" name="weekly_off_1">
                                                        <option value="" disabled selected>-- Select Day --</option>
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
                                                <div class="mb-3">
                                                    <label class="col-form-label">2nd Weekly Off </label>
                                                    <select class="select" id="weekly_off_2" name="weekly_off_2">
                                                        <option value="" disabled selected>-- Select Day --</option>
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
                                                <div class="mb-3">
                                                    <label class="col-form-label">3rd Weekly Off</label>
                                                    <select class="form-select" name="weekly_off_3" id="weekly_off_3">
                                                        <option value="" disabled selected>-- Select Day --</option>
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
                                                <div class="mb-3">
                                                    <label class="col-form-label">4th Weekly Off</label>
                                                    <select class="form-select" name="weekly_off_4" id="weekly_off_4">
                                                        <option value="" disabled selected>-- Select Day --</option>
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
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2">Saturday Off</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="d-flex flex-wrap">
                                                        @foreach (['1st', '2nd', '3rd', '4th', '5th'] as $week)
                                                            <div class="me-3">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="saturday_offs[]" value="{{ $week }}"
                                                                    id="saturday_off_{{ $loop->index }}">
                                                                <label
                                                                    for="saturday_off_{{ $loop->index }}">{{ $week }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2">Saturday Half Day</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="d-flex flex-wrap">
                                                        @foreach (['1st', '2nd', '3rd', '4th', '5th'] as $week)
                                                            <div class="me-3">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="saturday_half_days[]"
                                                                    value="{{ $week }}"
                                                                    id="saturday_half_{{ $loop->index }}">
                                                                <label
                                                                    for="saturday_half_{{ $loop->index }}">{{ $week }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2">Weekly Off</h5>
                                        <div id="weeklyOffContainer" class="row g-3">

                                            {{-- 1st Weekly Off --}}
                                            <div class="col-md-6">
                                                <label class="col-form-label">1st Weekly Off</label>
                                                <div class="input-group">
                                                    <select class="form-select weekly_off_select"
                                                        name="weekly_off_1" id="weekly_off_1">
                                                        <option value="">-- Select Day --</option>
                                                        <option value="Sunday">Sunday</option>
                                                        <option value="Monday">Monday</option>
                                                        <option value="Tuesday">Tuesday</option>
                                                        <option value="Wednesday">Wednesday</option>
                                                        <option value="Thursday">Thursday</option>
                                                        <option value="Friday">Friday</option>
                                                        <option value="Saturday">Saturday</option>
                                                    </select>
                                                    <button type="button"
                                                        class="btn btn-outline-danger remove-weekly-off"
                                                        data-target="weekly_off_1">
                                                        ✕
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- 2nd Weekly Off --}}
                                            <div class="col-md-6">
                                                <label class="col-form-label">2nd Weekly Off</label>
                                                <div class="input-group">
                                                    <select class="form-select weekly_off_select"
                                                        name="weekly_off_2" id="weekly_off_2">
                                                        <option value="">-- Select Day --</option>
                                                        <option value="Sunday">Sunday</option>
                                                        <option value="Monday">Monday</option>
                                                        <option value="Tuesday">Tuesday</option>
                                                        <option value="Wednesday">Wednesday</option>
                                                        <option value="Thursday">Thursday</option>
                                                        <option value="Friday">Friday</option>
                                                        <option value="Saturday">Saturday</option>
                                                    </select>
                                                    <button type="button"
                                                        class="btn btn-outline-danger remove-weekly-off"
                                                        data-target="weekly_off_2">
                                                        ✕
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- 3rd Weekly Off --}}
                                            <div class="col-md-6">
                                                <label class="col-form-label">3rd Weekly Off</label>
                                                <div class="input-group">
                                                    <select class="form-select weekly_off_select"
                                                        name="weekly_off_3" id="weekly_off_3">
                                                        <option value="">-- Select Day --</option>
                                                        <option value="Sunday">Sunday</option>
                                                        <option value="Monday">Monday</option>
                                                        <option value="Tuesday">Tuesday</option>
                                                        <option value="Wednesday">Wednesday</option>
                                                        <option value="Thursday">Thursday</option>
                                                        <option value="Friday">Friday</option>
                                                        <option value="Saturday">Saturday</option>
                                                    </select>
                                                    <button type="button"
                                                        class="btn btn-outline-danger remove-weekly-off"
                                                        data-target="weekly_off_3">
                                                        ✕
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- 4th Weekly Off --}}
                                            <div class="col-md-6">
                                                <label class="col-form-label">4th Weekly Off</label>
                                                <div class="input-group">
                                                    <select class="form-select weekly_off_select"
                                                        name="weekly_off_4" id="weekly_off_4">
                                                        <option value="">-- Select Day --</option>
                                                        <option value="Sunday">Sunday</option>
                                                        <option value="Monday">Monday</option>
                                                        <option value="Tuesday">Tuesday</option>
                                                        <option value="Wednesday">Wednesday</option>
                                                        <option value="Thursday">Thursday</option>
                                                        <option value="Friday">Friday</option>
                                                        <option value="Saturday">Saturday</option>
                                                    </select>
                                                    <button type="button"
                                                        class="btn btn-outline-danger remove-weekly-off"
                                                        data-target="weekly_off_4">
                                                        ✕
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>





                            {{-- <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2" id="saturdayHalfDayHeading"></h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="d-flex flex-wrap">
                                                        <div class="me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="saturday_offs[]" value="1st"
                                                                id="saturday_offs[]">
                                                            <label for=""> 1st</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="saturday_offs[]" value="2nd"
                                                                id="saturday_offs[]">
                                                            <label for=""> 2nd</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="saturday_offs[]" value="3rd"
                                                                id="saturday_offs[]">
                                                            <label for=""> 3rd</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="saturday_offs[]" value="4th"
                                                                id="saturday_offs[]">
                                                            <label for=""> 4th</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="saturday_offs[]" value="5th"
                                                                id="saturday_offs[]">
                                                            <label for=""> 5th</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2" id="saturdayDayHeading">Saturday Half Day</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="d-flex flex-wrap">
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="saturday_half_days[]" type="checkbox"
                                                                value="1st" id="saturday_half_days[]">
                                                            <label for=""> 1st</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="saturday_half_days[]" type="checkbox"
                                                                value="2nd" id="saturday_half_days[]">
                                                            <label for=""> 2nd</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="saturday_half_days[]" type="checkbox"
                                                                value="3rd" id="saturday_half_days[]">
                                                            <label for=""> 3rd</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="saturday_half_days[]" type="checkbox"
                                                                value="4th" id="saturday_half_days[]">
                                                            <label for=""> 4th</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="saturday_half_days[]" type="checkbox"
                                                                value="5th" id="saturday_half_days[]">
                                                            <label for=""> 5th</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            <!-- Saturday Offs -->
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2" id="saturdayHalfDayHeading">Saturday Off</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="d-flex flex-wrap">
                                                        <div class="me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="saturday_offs[]" value="1st"
                                                                id="saturday_off_1st">
                                                            <label for="saturday_off_1st"> 1st</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="saturday_offs[]" value="2nd"
                                                                id="saturday_off_2nd">
                                                            <label for="saturday_off_2nd"> 2nd</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="saturday_offs[]" value="3rd"
                                                                id="saturday_off_3rd">
                                                            <label for="saturday_off_3rd"> 3rd</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="saturday_offs[]" value="4th"
                                                                id="saturday_off_4th">
                                                            <label for="saturday_off_4th"> 4th</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="saturday_offs[]" value="5th"
                                                                id="saturday_off_5th">
                                                            <label for="saturday_off_5th"> 5th</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Saturday Half Days -->
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2" id="saturdayDayHeading">Saturday Half Day</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="d-flex flex-wrap">
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="saturday_half_days[]" type="checkbox"
                                                                value="1st" id="saturday_half_day_1st">
                                                            <label for="saturday_half_day_1st"> 1st</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="saturday_half_days[]" type="checkbox"
                                                                value="2nd" id="saturday_half_day_2nd">
                                                            <label for="saturday_half_day_2nd"> 2nd</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="saturday_half_days[]" type="checkbox"
                                                                value="3rd" id="saturday_half_day_3rd">
                                                            <label for="saturday_half_day_3rd"> 3rd</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="saturday_half_days[]" type="checkbox"
                                                                value="4th" id="saturday_half_day_4th">
                                                            <label for="saturday_half_day_4th"> 4th</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="saturday_half_days[]" type="checkbox"
                                                                value="5th" id="saturday_half_day_5th">
                                                            <label for="saturday_half_day_5th"> 5th</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2" id="saturdayDayHeading">Half Day From</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="d-flex flex-wrap">
                                                        <div class="me-3">
                                                            <input class="form-check-input" name="half_day_time"
                                                                type="radio" value="1st half"
                                                                id="half_day_time">
                                                            <label for="">1st half</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input" name="half_day_time"
                                                                type="radio" value="2nd half"
                                                                id="half_day_time">
                                                            <label for="">2nd Half</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2" id="saturdayDayHeading">Early Login Punch In Allowed
                                        </h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="d-flex flex-wrap">
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="early_login_punchIn" type="radio"
                                                                value="yes" id="early_login_punchIn">
                                                            <label for="">Yes</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="early_login_punchIn" type="radio"
                                                                value="no" id="early_login_punchIn">
                                                            <label for="">No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-2" id="saturdayDayHeading">Early Login Punch In Allowed</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="d-flex flex-wrap">
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="early_login_punchIn" type="radio"
                                                                value="yes" id="editearlyYes">
                                                            <label for="earlyYes">Yes</label>
                                                        </div>
                                                        <div class="me-3">
                                                            <input class="form-check-input"
                                                                name="early_login_punchIn" type="radio"
                                                                value="no" id="editearlyNo">
                                                            <label for="earlyNo">No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6" id="editearlyMinutesContainer"
                                                style="display: none;">
                                                <div class="mb-3">
                                                    <label for="early_minutes">If Yes, enter minutes allowed</label>
                                                    <input type="text" id="editearly_minutes"
                                                        name="early_login_minutes" class="form-control MyDurationPicker"
                                                        placeholder="Enter minutes">
                                                    <small class="text-muted">Enter how many minutes early punch-in is
                                                        allowed.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-end">
                    <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Delete Activity -->
    <div class="modal fade" id="delete_shift" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                        <i class="ti ti-trash-x fs-36 text-danger"></i>
                    </div>
                    <h4 class="mb-2">Remove Shift?</h4>
                    <p class="mb-0">Are you sure you want to remove <br> the shift you selected?</p>
                    <div class="d-flex align-items-center justify-content-center mt-4">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmDeleteShift" class="btn btn-danger">Yes, Delete
                            it</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete-->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <div id="dynamicToast" class="toast align-items-center text-white border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="dynamicToastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const isAdmin = true;
            const canEditShift = true;
            const canDeleteShift = true;
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.remove-weekly-off').forEach(button => {
                    button.addEventListener('click', function() {
                        const targetId = this.getAttribute('data-target');
                        const select = document.getElementById(targetId);
                        if (select) {
                            select.value = '';
                        }
                    });
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                const $generateShiftConfirm = $('#confirmGenerateShift');
                const $generateShiftTrigger = $('#generateShiftTrigger');
                const $generateShiftModal = $('#generateShiftModal');
                const $confirmGenerateShiftBtn = $('#confirmGenerateShiftBtn');

                // Open modal when generate button is clicked
                $generateShiftTrigger.on('click', function(e) {
                        e.preventDefault();
                    if (! $generateShiftConfirm.is(':checked')) {
                        if (typeof showToast === 'function') {
                            showToast('Please confirm attendance regen before generating shifts.', 'warning');
                        }
                        return;
                    }

                    // Check if at least one shift is selected
                    const selectedShifts = [];
                    $('#shifts-table tbody input.shift-select:checked').each(function() {
                        const shiftId = $(this).data('shift');
                        if (shiftId) {
                            selectedShifts.push(shiftId);
                        }
                    });

                    if (selectedShifts.length === 0) {
                        if (typeof showToast === 'function') {
                            showToast('Please select at least one shift from the table before generating.', 'error');
                        }
                        return;
                    }

                    // Set current month and year as default if not already set
                    const currentDate = new Date();
                    const currentMonth = String(currentDate.getMonth() + 1).padStart(2, '0');
                    const currentYear = currentDate.getFullYear();

                    if (!$('#generate_shift_month').val()) {
                        $('#generate_shift_month').val(currentMonth);
                    }
                    if (!$('#generate_shift_year').val()) {
                        $('#generate_shift_year').val(currentYear);
                    }

                    // Open the modal
                    const modal = new bootstrap.Modal($generateShiftModal[0]);
                    modal.show();
                });

                // Handle generate shift button click
                $confirmGenerateShiftBtn.on('click', function() {
                    const month = $('#generate_shift_month').val();
                    const year = $('#generate_shift_year').val();

                    if (!month || !year) {
                        if (typeof showToast === 'function') {
                            showToast('Please select both month and year.', 'error');
                        }
                        return;
                    }

                    // Get the selected shift ID from the table using data-shift attribute
                    const selectedShifts = [];
                    $('#shifts-table tbody input.shift-select:checked').each(function() {
                        const shiftId = $(this).data('shift');
                        if (shiftId) {
                            selectedShifts.push(shiftId);
                        }
                    });

                    // Double check (should not happen since modal only opens if shifts are selected)
                    if (selectedShifts.length === 0) {
                        if (typeof showToast === 'function') {
                            showToast('Please select at least one shift from the table.', 'error');
                        }
                        $generateShiftModal.modal('hide');
                        return;
                    }

                    // Disable button and show loading
                    const $btn = $(this);
                    const $spinner = $btn.find('.spinner-border');
                    $btn.prop('disabled', true);
                    $spinner.removeClass('d-none');

                    // Send AJAX request for each selected shift
                    let completed = 0;
                    let errors = 0;
                    const totalShifts = selectedShifts.length;
                    const errorMessages = [];

                    if (totalShifts === 0) {
                        $btn.prop('disabled', false);
                        $spinner.addClass('d-none');
                        return;
                    }

                    selectedShifts.forEach(function(shiftId) {
                        $.ajax({
                            url: "{{ route('generate.shift.employees', ['box_slug' => request()->route('box_slug')]) }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                shift_id: shiftId,
                                month: month,
                                year: year
                            },
                            success: function(response) {
                                completed++;
                                if (completed === totalShifts) {
                                    $generateShiftModal.modal('hide');
                                    if (typeof showToast === 'function') {
                                        showToast(response.message || 'Shift generated successfully for all employees.', 'success');
                                    }
                                    // Reset form
                                    $('#generateShiftForm')[0].reset();
                                    // Reload table
                                    $('#shifts-table').DataTable().ajax.reload(null, false);
                                }
                            },
                            error: function(xhr) {
                                errors++;
                                const message = xhr.responseJSON?.message || 'Failed to generate shift.';
                                errorMessages.push(message);
                                if (completed + errors === totalShifts) {
                                    $generateShiftModal.modal('hide');
                                    if (typeof showToast === 'function') {
                                        const finalMessage = errors === totalShifts
                                            ? errorMessages[0]
                                            : `${completed} shift(s) generated successfully, ${errors} failed.`;
                                        showToast(finalMessage, errors === totalShifts ? 'error' : 'warning');
                                    }
                                }
                            },
                            complete: function() {
                                if (completed + errors === totalShifts) {
                                    $btn.prop('disabled', false);
                                    $spinner.addClass('d-none');
                                }
                            }
                        });
                    });
                });
                $("#earlyMinutesContainer").hide();
                $("input[name='early_login_punchIn']").change(function() {
                    if ($(this).val() === "yes") {
                        $("#earlyMinutesContainer").slideDown();
                    } else {
                        $("#earlyMinutesContainer").slideUp();
                        $("#early_minutes").val('');
                    }
                });

                function populateWeeklyOffs(weeklyOffs) {

                    for (let i = 1; i <= 4; i++) {
                        const day = weeklyOffs[i - 1] || '';
                        $(`#weekly_off_${i}`).val(day);
                    }
                }

                function clearWeeklyOffs() {
                    for (let i = 3; i <= 4; i++) {
                        $(`#weekly_off_${i}`).val('');
                    }
                }

                function removeWeeklyOffSection() {
                    $('#weeklyOffSection').remove();
                }

                function initWeeklyOffLogic($form) {
                    const $weeklyOff1 = $form.find('select[name="weekly_off_1"]');
                    const $weeklyOff2 = $form.find('select[name="weekly_off_2"]');
                    const $weeklyOff3 = $form.find('select[name="weekly_off_3"]');
                    const $weeklyOff4 = $form.find('select[name="weekly_off_4"]');
                    const $saturdayOffs = $form.find('input[name="saturday_offs[]"]');
                    const $saturdayHalfDays = $form.find('input[name="saturday_half_days[]"]');
                    const $heading = $form.find('#saturdayHalfDayHeading').eq(0);
                    const $heading2 = $form.find('#saturdayDayHeading').eq(0);

                    function updateSaturdayHeadings() {
                        const third = $weeklyOff3.val();
                        const fourth = $weeklyOff4.val();
                        $heading.text(third ? '3rd - ' + third : '');
                        $heading2.text(fourth ? '4th Half - ' + fourth : '');
                    }

                    function updateWeeklyOffOptions() {
                        const selectedValues = [
                            $weeklyOff1.val(),
                            $weeklyOff2.val(),
                            $weeklyOff3.val(),
                            $weeklyOff4.val()
                        ].filter(val => val);
                        $form.find('select[name^="weekly_off_"]').each(function() {
                            const $select = $(this);
                            const currentVal = $select.val();
                            $select.find('option').each(function() {
                                const val = $(this).val();
                                if (!val) return;
                                const isSelectedElsewhere = selectedValues.includes(val) && currentVal !== val;
                                $(this).prop('disabled', isSelectedElsewhere);
                            });
                        });
                        updateSaturdayCheckboxLogic();
                    }

                    function clearSaturdayCheckboxes() {
                        $saturdayOffs.prop('checked', false);
                        $saturdayHalfDays.prop('checked', false);
                    }

                    function updateSaturdayCheckboxLogic() {
                        const selectedOffs = $saturdayOffs.filter(':checked').map(function() {
                            return $(this).val();
                        }).get();
                        const selectedHalfDays = $saturdayHalfDays.filter(':checked').map(function() {
                            return $(this).val();
                        }).get();
                        $saturdayOffs.prop('disabled', false);
                        $saturdayHalfDays.prop('disabled', false);

                        $saturdayOffs.each(function() {
                            const week = $(this).val();
                            if (selectedHalfDays.includes(week)) {
                                $(this).prop('disabled', true).prop('checked', false);
                            }
                        });

                        $saturdayHalfDays.each(function() {
                            const week = $(this).val();
                            if (selectedOffs.includes(week)) {
                                $(this).prop('disabled', true).prop('checked', false);
                            }
                        });
                    }
                    $weeklyOff1.on('change', function() {
                        updateWeeklyOffOptions();
                        // clearSaturdayCheckboxes();
                        updateSaturdayCheckboxLogic();
                    });
                    $weeklyOff2.on('change', function() {
                        updateWeeklyOffOptions();
                        // clearSaturdayCheckboxes();
                        updateSaturdayCheckboxLogic();
                    });
                    $weeklyOff3.on('change', function() {
                        updateSaturdayHeadings();
                        updateWeeklyOffOptions();
                        clearSaturdayCheckboxes();
                        updateSaturdayCheckboxLogic();
                    });
                    $weeklyOff4.on('change', function() {
                        updateSaturdayHeadings();
                        updateWeeklyOffOptions();
                        clearSaturdayCheckboxes();
                        updateSaturdayCheckboxLogic();
                    });
                    $saturdayOffs.on('change', updateSaturdayCheckboxLogic);
                    $saturdayHalfDays.on('change', updateSaturdayCheckboxLogic);
                    updateSaturdayHeadings();
                    updateWeeklyOffOptions();
                }

                $('[data-weekly-off-form]').each(function() {
                    initWeeklyOffLogic($(this));
                });


                function toggleFields(selectors, enabled, clear = true) {
                    selectors.forEach(sel => {
                        const $el = $(sel);
                        $el.prop('disabled', !enabled);
                        if (!enabled && clear) {
                            $el.val('');
                        }
                    });
                }

                function initSwitch(toggleSelector, fieldSelectors, clear = true) {
                    const $toggle = $(toggleSelector);
                    toggleFields(fieldSelectors, $toggle.is(':checked'), clear);
                    $toggle.on('change', function() {
                        toggleFields(fieldSelectors, $(this).is(':checked'), clear);
                    });
                }
                // Shift Time Toggle
                initSwitch('#shift_enableds', [
                    '[name="code"]',
                    '[name="shift_name"]',
                    '[name="in_time"]',
                    '[name="out_time"]',
                    '[name="shift_hours"]'
                ]);
                // Rest Time Toggle
                initSwitch('#rest_timeEnable', [
                    '#lunch_outs',
                    '#lunch_ins',
                    '#lunch_hourss'
                ]);
                // Short Break Toggle
                initSwitch('#short_break_enable', [
                    '#short_break_outss',
                    '#short_break_inss',
                    '#short_break_hourss'
                ]);
                //short break toggle second

                initSwitch('#short_break_enable_2', [
                    '#short_break_out_2',
                    '#short_break_in_2',
                    '#short_break_hours_2'
                ]);


                // Overtime Toggle
                $('#allowOvertimeSwitch').on('change', function() {
                    const isChecked = $(this).is(':checked');
                    toggleFields(['#absentBelowInput', '#overtimeAllowedInput'], isChecked);
                });
                // Half Day Toggle
                initSwitch('#half_dayEnable', [
                    '#half_day_start_timess',
                    '#half_day_end_timess',
                    '#half_day_hourss'
                ]);
                // Toast notification function
                function showToast(message, type = 'success') {
                    const $toast = $('#dynamicToast');
                    const $message = $('#dynamicToastMessage');
                    $message.text(message);
                    $toast.removeClass('bg-success bg-danger bg-warning');

                    switch (type) {
                        case 'success':
                            $toast.addClass('bg-success');
                            break;
                        case 'error':
                            $toast.addClass('bg-danger');
                            break;
                        default:
                            $toast.addClass('bg-warning');
                    }
                    const toastInstance = bootstrap.Toast.getOrCreateInstance($toast[0]);
                    toastInstance.show();
                }
                // Convert time to 24-hour format
                function convertTo24HourFormat(timeStr) {
                    if (!timeStr) return '';
                    const date = new Date('1970-01-01 ' + timeStr);
                    if (isNaN(date.getTime())) return '';
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    return `${hours}:${minutes}`;
                }
                // Normalize all time fields before submission
                function normalizeTimeFields(selector = '') {
                    $(`${selector} .datetimepicker-time`).each(function() {
                        const originalVal = $(this).val();
                        const convertedVal = convertTo24HourFormat(originalVal);
                        $(this).val(convertedVal);
                    });
                }


                // Store shift (create)
                function submitShiftForm() {
                    $('#activities_shift').on('submit', function(e) {
                        e.preventDefault();
                        normalizeTimeFields('#activities_shift');
                        const formData = $(this).serialize();
                        $.ajax({
                            url: '{{ route('shifts.store', ['box_slug' => request()->route('box_slug')]) }}',
                            method: 'POST',
                            data: formData,
                            success: function(response) {
                                showToast('Shift saved successfully!', 'success');
                                $('#activities_shift')[0].reset();
                                hideOffcanvas('#offcanvas_add');
                                $('#shifts-table').DataTable().ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                console.error(xhr.responseJSON);
                                showToast('Something went wrong!', 'error');
                            }
                        });
                    });
                }
                // Hide offcanvas utility
                function hideOffcanvas(selector) {
                    const offcanvasElement = document.querySelector(selector);
                    const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    if (offcanvasInstance) {
                        offcanvasInstance.hide();
                    }
                }
                // Column Definitions for Manage Columns feature
                                const columnDefinitions = [
                    { index: 0, name: 'Select', visible: true },
                    { index: 1, name: 'Sr No.', visible: true },
                    { index: 2, name: 'Shift Code', visible: true },
                    { index: 3, name: 'Shift Name', visible: true },
                    { index: 4, name: 'Shift In', visible: true },
                    { index: 5, name: 'Shift Out', visible: true },
                    { index: 6, name: 'Shift Hrs.', visible: true },
                    { index: 7, name: 'Start Date', visible: true },
                    { index: 8, name: 'End Date', visible: true },
                    { index: 9, name: 'Shift Enabled', visible: false },
                    { index: 10, name: 'Rest Enabled', visible: false },
                    { index: 11, name: 'Lunch Out', visible: false },
                    { index: 12, name: 'Lunch In', visible: false },
                    { index: 13, name: 'Lunch Hours', visible: false },
                    { index: 14, name: 'Short Break Enabled', visible: false },
                    { index: 15, name: 'Break Out', visible: false },
                    { index: 16, name: 'Break In', visible: false },
                    { index: 17, name: 'Break Hours', visible: false },
                    { index: 18, name: 'Break 2 Enabled', visible: false },
                    { index: 19, name: 'Break 2 Out', visible: false },
                    { index: 20, name: 'Break 2 In', visible: false },
                    { index: 21, name: 'Break 2 Hours', visible: false },
                    { index: 22, name: 'Half Day Enabled', visible: false },
                    { index: 23, name: 'Half Day Start', visible: false },
                    { index: 24, name: 'Half Day End', visible: false },
                    { index: 25, name: 'Half Day Hours', visible: false },
                    { index: 26, name: 'Absent Below', visible: false },
                    { index: 27, name: 'Half Day Below', visible: false },
                    { index: 28, name: 'Absent Deduction', visible: false },
                    { index: 29, name: 'Half Day Deduction', visible: false },
                    { index: 30, name: 'Short Leave Deduction', visible: false },
                    { index: 31, name: 'Short Leave Below', visible: false },
                    { index: 32, name: 'Overtime Allowed', visible: false },
                    { index: 33, name: 'Weekly Off 1', visible: false },
                    { index: 34, name: 'Weekly Off 2', visible: false },
                    { index: 35, name: 'Weekly Off 3', visible: false },
                    { index: 36, name: 'Weekly Off 4', visible: false },
                    { index: 37, name: 'Check Over Time', visible: false },
                    { index: 38, name: 'Saturday Offs', visible: false },
                    { index: 39, name: 'Saturday Half Days', visible: false },
                    { index: 40, name: 'Half Day Time', visible: false },
                    { index: 41, name: 'Early Login Punch In', visible: false },
                    { index: 42, name: 'Early Login Minutes', visible: false },
                    { index: 43, name: 'Max Punch In Allowed', visible: false },
                    { index: 44, name: 'Auto Punchout Time', visible: false },
                    { index: 45, name: 'All In Out Calc', visible: false },
                    { index: 46, name: 'First In Last Out Calc', visible: false },
                    { index: 47, name: 'Relaxation Time', visible: false },
                    { index: 48, name: 'Max Relaxations', visible: false },
                    { index: 49, name: 'Created At', visible: false },
                    { index: 50, name: 'Action', visible: true }
                ];

                // Page identifier for column preferences
                const pageIdentifier = 'attendance-shift';

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
                            console.error('Error loading column preferences:', error);
                            // Fallback: show all columns
                            columnDefinitions.forEach(col => col.visible = true);
                            if (callback) callback();
                        }
                    });
                }

                // Save column preferences to database
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

                // Global table variable
                let shiftTable;

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

                // Initialize datatable
                loadColumnPreferences(function() {
                    shiftTable = initializeShiftTable();
                    setupSearchInput(shiftTable);
                });

                function initializeShiftTable() {
                    return $('#shifts-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: '{{ route('get.shift.data', ['box_slug' => request()->route('box_slug')]) }}',
                            type: 'POST',
                            data: function(d) {
                                d._token = $('meta[name="csrf-token"]').attr('content');
                                d.search_shift = $('#SearchShitt').val();
                            }
                        },
                        columns: [
                            {
                                data: null,
                                orderable: false,
                                searchable: false,
                                visible: columnDefinitions[0].visible,
                                render: function(data) {
                                    return `<input type="checkbox" class="shift-select" data-shift="${data.id}">`;
                                }
                            },
                            {
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false,
                                visible: columnDefinitions[1].visible
                            },
                            {
                                data: 'code',
                                name: 'code',
                                visible: columnDefinitions[2].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'shift_name',
                                name: 'shift_name',
                                visible: columnDefinitions[3].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'in_time',
                                name: 'in_time',
                                visible: columnDefinitions[4].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'out_time',
                                name: 'out_time',
                                visible: columnDefinitions[5].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'shift_hours',
                                name: 'shift_hours',
                                visible: columnDefinitions[6].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'start_from_date',
                                name: 'start_from_date',
                                visible: columnDefinitions[7].visible,
                                render: function(data) {
                                    if (!data) return '--';
                                    const date = new Date(data);
                                    if (isNaN(date.getTime())) return data;
                                    return date.toLocaleDateString('en-GB');
                                }
                            },
                            {
                                data: 'end_date',
                                name: 'end_date',
                                visible: columnDefinitions[8].visible,
                                render: function(data) {
                                    if (!data) return '--';
                                    const date = new Date(data);
                                    if (isNaN(date.getTime())) return data;
                                    return date.toLocaleDateString('en-GB');
                                }
                            },
                            {
                                data: 'shift_enabled',
                                name: 'shift_enabled',
                                visible: columnDefinitions[9].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'rest_enabled',
                                name: 'rest_enabled',
                                visible: columnDefinitions[10].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'lunch_out',
                                name: 'lunch_out',
                                visible: columnDefinitions[11].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'lunch_in',
                                name: 'lunch_in',
                                visible: columnDefinitions[12].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'lunch_hours',
                                name: 'lunch_hours',
                                visible: columnDefinitions[13].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'short_break_enabled',
                                name: 'short_break_enabled',
                                visible: columnDefinitions[14].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'short_break_out',
                                name: 'short_break_out',
                                visible: columnDefinitions[15].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'short_break_in',
                                name: 'short_break_in',
                                visible: columnDefinitions[16].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'short_break_hours',
                                name: 'short_break_hours',
                                visible: columnDefinitions[17].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'short_break_enabled_2',
                                name: 'short_break_enabled_2',
                                visible: columnDefinitions[18].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'short_break_out_2',
                                name: 'short_break_out_2',
                                visible: columnDefinitions[19].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'short_break_in_2',
                                name: 'short_break_in_2',
                                visible: columnDefinitions[20].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'short_break_hours_2',
                                name: 'short_break_hours_2',
                                visible: columnDefinitions[21].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'half_day_enabled',
                                name: 'half_day_enabled',
                                visible: columnDefinitions[22].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'half_day_start_time',
                                name: 'half_day_start_time',
                                visible: columnDefinitions[23].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'half_day_end_time',
                                name: 'half_day_end_time',
                                visible: columnDefinitions[24].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'half_day_hours',
                                name: 'half_day_hours',
                                visible: columnDefinitions[25].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'absent_below',
                                name: 'absent_below',
                                visible: columnDefinitions[26].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'half_day_below',
                                name: 'half_day_below',
                                visible: columnDefinitions[27].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'absent_deduction',
                                name: 'absent_deduction',
                                visible: columnDefinitions[28].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'half_day_deduction',
                                name: 'half_day_deduction',
                                visible: columnDefinitions[29].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'short_leave_deduction',
                                name: 'short_leave_deduction',
                                visible: columnDefinitions[30].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'short_leave_below',
                                name: 'short_leave_below',
                                visible: columnDefinitions[31].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'overtime_allowed',
                                name: 'overtime_allowed',
                                visible: columnDefinitions[32].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'weekly_off_1',
                                name: 'weekly_off_1',
                                visible: columnDefinitions[33].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'weekly_off_2',
                                name: 'weekly_off_2',
                                visible: columnDefinitions[34].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'weekly_off_3',
                                name: 'weekly_off_3',
                                visible: columnDefinitions[35].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'weekly_off_4',
                                name: 'weekly_off_4',
                                visible: columnDefinitions[36].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'check_over_time',
                                name: 'check_over_time',
                                visible: columnDefinitions[37].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'saturday_offs',
                                name: 'saturday_offs',
                                visible: columnDefinitions[38].visible,
                                render: function(data) {
                                    if (Array.isArray(data)) return data.join(', ');
                                    return data ? data : '--';
                                }
                            },
                            {
                                data: 'saturday_half_days',
                                name: 'saturday_half_days',
                                visible: columnDefinitions[39].visible,
                                render: function(data) {
                                    if (Array.isArray(data)) return data.join(', ');
                                    return data ? data : '--';
                                }
                            },
                            {
                                data: 'half_day_time',
                                name: 'half_day_time',
                                visible: columnDefinitions[40].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'early_login_punchIn',
                                name: 'early_login_punchIn',
                                visible: columnDefinitions[41].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'early_login_minutes',
                                name: 'early_login_minutes',
                                visible: columnDefinitions[42].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'max_punch_in_allowed',
                                name: 'max_punch_in_allowed',
                                visible: columnDefinitions[43].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'auto_punchout_time',
                                name: 'auto_punchout_time',
                                visible: columnDefinitions[44].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'all_in_out_calculation',
                                name: 'all_in_out_calculation',
                                visible: columnDefinitions[45].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'first_in_last_out_calculation',
                                name: 'first_in_last_out_calculation',
                                visible: columnDefinitions[46].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'relaxation_time',
                                name: 'relaxation_time',
                                visible: columnDefinitions[47].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'max_relaxations_allowed',
                                name: 'max_relaxations_allowed',
                                visible: columnDefinitions[48].visible,
                                render: function(data) {
                                    if (data === true || data === 1 || data === '1' || data === 'yes') return 'Yes';
                                    if (data === false || data === 0 || data === '0' || data === 'no') return 'No';
                                    return data !== null && data !== undefined && data !== '' ? data : '--';
                                }
                            },
                            {
                                data: 'created_at',
                                name: 'created_at',
                                visible: columnDefinitions[49].visible,
                                render: function(data) {
                                    if (!data) return '--';
                                    const date = new Date(data);
                                    if (isNaN(date.getTime())) return data;
                                    return date.toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                                }
                            },
                            {
                                data: 'id',
                                name: 'actions',
                                orderable: false,
                                searchable: false,
                                visible: columnDefinitions[50].visible,
                                render: function(data, type, row) {
                                    return `
                                        <div class="action-icon d-inline-flex">
                                            <a href="#" class="me-2 edit-shift" data-id="${data}" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <a href="#" class="delete-shift" data-id="${data}">
                                                <i class="ti ti-trash"></i>
                                            </a>
                                        </div>
                                    `;
                                }
                            }
                        ]
                    });
                }

                function setupSearchInput(tableInstance) {
                    let searchTimeout;
                    $('#SearchShitt').on('keyup', function() {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => {
                            tableInstance.ajax.reload();
                        }, 500);
                    });
                }
                // Helper function to convert time from HH:mm:ss to HH:mm format for time inputs
                function formatTimeForInput(timeStr) {
                    if (!timeStr) return '';
                    // If already in HH:mm format, return as is
                    if (timeStr.match(/^\d{2}:\d{2}$/)) return timeStr;
                    // If in HH:mm:ss format, extract HH:mm
                    if (timeStr.match(/^\d{2}:\d{2}:\d{2}$/)) {
                        return timeStr.substring(0, 5);
                    }
                    // If in other formats, try to parse
                    const parts = timeStr.split(':');
                    if (parts.length >= 2) {
                        return parts[0].padStart(2, '0') + ':' + parts[1].padStart(2, '0');
                    }
                    return '';
                }

                function timeToDecimal(timeStr) {
                    if (!timeStr) return '';
                    const parts = timeStr.split(':');
                    if (parts.length < 2) return timeStr;
                    const hours = parseInt(parts[0], 10);
                    const minutes = parseInt(parts[1], 10);
                    const decimal = hours + (minutes / 60);
                    return parseFloat(decimal.toFixed(2));
                }

                function splitTime(timeStr) {
                    if (!timeStr) return { h: '', m: '' };
                    const parts = timeStr.split(':');
                    return {
                        h: parseInt(parts[0], 10) || 0,
                        m: parseInt(parts[1], 10) || 0
                    };
                }

                // Helper function to extract date from various formats (ISO, datetime string, etc.)
                // Defined outside populateEditForm so it can be reused
                function extractDateForInput(dateValue) {
                    if (!dateValue) return '';

                    try {
                        const dateObj = new Date(dateValue);
                        if (!isNaN(dateObj.getTime())) {
                            // Format as MM/DD/YYYY for datePicker
                            const year = dateObj.getFullYear();
                            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                            const day = String(dateObj.getDate()).padStart(2, '0');
                            return `${month}/${day}/${year}`;
                        }
                    } catch (e) {
                        return '';
                    }

                    return '';
                }

                // Fill form for editing shift
                function populateEditForm(shiftId) {
                    const table = $('#shifts-table').DataTable();
                    const rowData = table.rows().data().toArray().find(row => row.id == shiftId);
                    if (!rowData) {
                        showToast('Shift data not found', 'error');
                        return;
                    }

                    $('#activities_shift_edit #id').val(shiftId);
                    $('#shift_code').val(rowData.code);
                    $('#shift_type').val(rowData.shift_name);
                    // Format time fields to HH:mm (remove seconds and AM/PM)
                    $('#shift_in_time').val(formatTimeForInput(rowData.in_time));
                    $('#shift_out_time').val(formatTimeForInput(rowData.out_time));
                    $('#shift_hours').val(rowData.shift_hours);

                    // Populate effective period dates
                    console.log('Row data for effective period:', {
                        start_from_date: rowData.start_from_date,
                        end_date: rowData.end_date
                    });

                    // Handle start_from_date using the helper function
                    const startDate = extractDateForInput(rowData.start_from_date);
                    console.log('Extracted start_from_date:', startDate, 'from:', rowData.start_from_date);

                    // Handle end_date using the helper function
                    const endDate = extractDateForInput(rowData.end_date);
                    console.log('Extracted end_date:', endDate, 'from:', rowData.end_date);

                    // Verify fields exist before setting
                    const $startField = $('#start_from_date_edit');
                    const $endField = $('#end_date_edit');

                    if ($startField.length === 0) {
                        console.error('Field #start_from_date_edit not found!');
                    } else {
                        $startField.val(startDate);
                        console.log('Set start_from_date_edit to:', startDate, 'Current value:', $startField.val());
                    }

                    if ($endField.length === 0) {
                        console.error('Field #end_date_edit not found!');
                    } else {
                        $endField.val(endDate);
                        console.log('Set end_date_edit to:', endDate, 'Current value:', $endField.val());
                    }
                    $('#lunch_out').val(formatTimeForInput(rowData.lunch_out));
                    $('#lunch_in').val(formatTimeForInput(rowData.lunch_in));
                    $('#lunch_hours').val(rowData.lunch_hours || '');
                    $('#short_break_outs').val(formatTimeForInput(rowData.short_break_out || ''));
                    $('#short_break_in').val(formatTimeForInput(rowData.short_break_in || ''));
                    $('#short_break_hours').val(rowData.short_break_hours || '');
                    $('#short_break_outs_2').val(formatTimeForInput(rowData.short_break_out_2 || ''));
                    $('#short_break_ins_2').val(formatTimeForInput(rowData.short_break_in_2 || ''));
                    $('#short_break_hourss_2').val(rowData.short_break_hours_2 || '');
                    $('#half_day_start_time').val(formatTimeForInput(rowData.half_day_start_time));
                    $('#half_day_end_time').val(formatTimeForInput(rowData.half_day_end_time));
                    $('#half_day_hours').val(rowData.half_day_hours || '');
                    // Populate thresholds with split hours and minutes
                    const absentSplit = splitTime(rowData.absent_below);
                    $('#absent_below_hours').val(absentSplit.h || '');
                    $('#absent_below_minutes').val(absentSplit.m || '');

                    const halfDaySplit = splitTime(rowData.half_day_below);
                    $('#half_day_limit_hours').val(halfDaySplit.h || '');
                    $('#half_day_limit_minutes').val(halfDaySplit.m || '');

                    const shortLeaveSplit = splitTime(rowData.short_leave_below);
                    $('#short_leave_limit_hours').val(shortLeaveSplit.h || '');
                    $('#short_leave_limit_minutes').val(shortLeaveSplit.m || '');
                    $('#short_leave_deduction').val(rowData.short_leave_deduction || '');
                    $('#half_day_deduction').val(rowData.half_day_deduction || '');
                    $('#absent_deduction').val(rowData.absent_deduction || '');
                    $('#relaxation_time').val(formatTimeForInput(rowData.relaxation_time || ''));
                    $('#max_relaxations_allowed_edit').val(rowData.max_relaxations_allowed || '');
                    $('#overtime_allowed').val(formatTimeForInput(rowData.overtime_allowed || ''));

                    var timeValue = formatTimeForInput(rowData.auto_punchout_time || '');
                    $('#editauto_punchout_time').val(timeValue);

                    if (rowData.all_in_out_calculation == 1) {
                        $('#editAllInOutCalculation').prop('checked', true);
                    } else if (rowData.first_in_last_out_calculation == 1) {
                        $('#editFirstInLastOutCalculation').prop('checked', true);
                    } else {
                         // Default fallback if neither is set (should not happen if mandatory)
                         $('input[name="punch_calculation_mode"]').prop('checked', false);
                    }

                    $('#activities_shift_edit [name="weekly_off_1"]').val(rowData.weekly_off_1 || '').trigger('change');
                    $('#activities_shift_edit [name="weekly_off_2"]').val(rowData.weekly_off_2 || '').trigger('change');
                    $('#activities_shift_edit [name="weekly_off_3"]').val(rowData.weekly_off_3 || '').trigger('change');
                    $('#activities_shift_edit [name="weekly_off_4"]').val(rowData.weekly_off_4 || '').trigger('change');
                    // Set checkboxes
                    $('#check_over_time').prop('checked', rowData.check_over_time == '1' || rowData.check_over_time ===
                        true);
                    $('#shift_enabled').prop('checked', rowData.shift_enabled === true);
                    $('#rest_enabled').prop('checked', rowData.rest_enabled === true);
                    $('#short_break_enabled').prop('checked', rowData.short_break_enabled === true);
                    $('#short_break_enableds_2').prop('checked', rowData.short_break_enabled_2 == '1');
                    $('#half_day_enabled').prop('checked', rowData.half_day_enabled === true);
                    $(`input[name="half_day_time"][value="${rowData.half_day_time}"]`).prop('checked', true);
                    // Format auto punchout time to HH:mm (remove seconds and AM/PM)
                    var timeValue = formatTimeForInput(rowData.auto_punchout_time || '');
                    $('#editauto_punchout_time').val(timeValue);
                    $(`input[name="early_login_punchIn"][value="${rowData.early_login_punchIn}"]`).prop('checked',
                        true);
                    if (rowData.early_login_punchIn === "yes") {
                        $("#editearlyMinutesContainer").show();
                        // Format early login minutes to HH:mm if it's a time value
                        const earlyMinutesValue = rowData.early_login_minutes || '';
                        $("#editearly_minutes").val(formatTimeForInput(earlyMinutesValue));

                        // Populate max punch in allowed
                        const maxPunchInAllowed = rowData.max_punch_in_allowed || '';
                        $("#editmax_punch_in_allowed").val(maxPunchInAllowed);
                    } else {
                        $("#editearlyMinutesContainer").hide();
                        $("#editearly_minutes").val('');
                    }
                    $("input[name='early_login_punchIn']").change(function() {
                        if ($(this).val() === "yes") {
                            $("#editearlyMinutesContainer").slideDown();
                        } else {
                            $("#editearlyMinutesContainer").slideUp();
                            $("#editearly_minutes").val('');
                        }
                    });

                    $('#overtime_allowed').prop('disabled', !$('#check_over_time').prop('checked'));
                    const shiftEnabled = $('#shift_enabled').prop('checked');
                    $('#shift_in_time').prop('disabled', !shiftEnabled);
                    $('#shift_out_time').prop('disabled', !shiftEnabled);
                    $('#shift_hours').prop('disabled', !shiftEnabled);
                    const restEnabled = $('#rest_enabled').prop('checked');
                    $('#lunch_in').prop('disabled', !restEnabled);
                    $('#lunch_out').prop('disabled', !restEnabled);
                    $('#lunch_hours').prop('disabled', !restEnabled);

                    const shortBreakEnabled = $('#short_break_enabled').prop('checked');
                    $('#short_break_in').prop('disabled', !shortBreakEnabled);
                    $('#short_break_outs').prop('disabled', !shortBreakEnabled);
                    $('#short_break_hours').prop('disabled', !shortBreakEnabled);

                    const shortBreakEnabled2 = $('#short_break_enableds_2').prop('checked');
                    $('#short_break_outs_2').prop('disabled', !shortBreakEnabled2);
                    $('#short_break_ins_2').prop('disabled', !shortBreakEnabled2);
                    $('#short_break_hourss_2').prop('disabled', !shortBreakEnabled2);
                    // Half day related fields
                    const halfDayEnabled = $('#half_day_enabled').prop('checked');
                    $('#half_day_start_time').prop('disabled', !halfDayEnabled);
                    $('#half_day_end_time').prop('disabled', !halfDayEnabled);
                    $('#half_day_hours').prop('disabled', !halfDayEnabled);
                    $('#half_day_limit').prop('disabled', !halfDayEnabled);
                    // Clear all Saturday-related checkboxes by ID prefix
                    $('#activities_shift_edit input[name="saturday_offs[]"]').prop('checked', false);
                    $('#activities_shift_edit input[name="saturday_half_days[]"]').prop('checked', false);
                    if (Array.isArray(rowData.saturday_offs)) {
                        rowData.saturday_offs.forEach(off => {
                            $(`#activities_shift_edit input[name="saturday_offs[]"][value="${off}"]`).prop('checked', true).trigger('change');
                        });
                    }
                    if (Array.isArray(rowData.saturday_half_days)) {
                        rowData.saturday_half_days.forEach(half => {
                            $(`#activities_shift_edit input[name="saturday_half_days[]"][value="${half}"]`).prop('checked', true).trigger('change');
                        });
                    }

                    // Open offcanvas first, then set date values after it's shown
                    const offcanvasElement = document.getElementById('offcanvas_edit');
                    const offcanvas = new bootstrap.Offcanvas(offcanvasElement);

                    // Function to set date values
                    const setDateValues = () => {
                        // Set effective period dates using the helper function
                        const startDate = extractDateForInput(rowData.start_from_date);
                        const endDate = extractDateForInput(rowData.end_date);

                        const $startField = $('#start_from_date_edit');
                        const $endField = $('#end_date_edit');

                        if ($startField.length > 0) {
                            $startField.val(startDate);
                            console.log('Setting start_from_date_edit:', startDate, 'Current value:', $startField.val());
                        } else {
                            console.error('Field #start_from_date_edit not found!');
                        }

                        if ($endField.length > 0) {
                            $endField.val(endDate);
                            console.log('Setting end_date_edit:', endDate, 'Current value:', $endField.val());
                        } else {
                            console.error('Field #end_date_edit not found!');
                        }
                    };

                    // Set dates immediately (in case offcanvas is already in DOM)
                    setDateValues();

                    // Also set after offcanvas is shown (in case fields weren't accessible)
                    offcanvasElement.addEventListener('shown.bs.offcanvas', setDateValues, { once: true });

                    // Fallback: set again after a short delay
                    setTimeout(setDateValues, 100);
                    setTimeout(setDateValues, 300);

                    offcanvas.show();
                }

                $(document).on('change', '#check_over_time', function() {
                    $('#overtime_allowed').prop('disabled', !this.checked);
                });

                $(document).on('change', '#shift_enabled', function() {
                    const isEnabled = $(this).is(':checked');
                    $('#shift_in_time, #shift_code, #shift_type, #shift_out_time, #shift_hours').prop(
                        'disabled', !isEnabled);
                });
                $(document).on('change', '#rest_enabled', function() {
                    $('#lunch_in, #lunch_out, #lunch_hours').prop('disabled', !this.checked);
                });

                $(document).on('change', '#short_break_enabled', function() {
                    $('#short_break_in, #short_break_outs, #short_break_hours').prop('disabled', !this.checked);
                });
                $(document).on('change', '#short_break_enableds_2', function() {
                    $('#short_break_ins_2, #short_break_outs_2, #short_break_hourss_2').prop('disabled', !this
                        .checked);
                });

                $(document).on('change', '#half_day_enabled', function() {
                    $('#half_day_start_time, #half_day_end_time, #half_day_hours, #half_day_limit').prop(
                        'disabled', !this.checked);
                });


                // Submit edited shift
                function editShiftForm() {
                    $('#activities_shift_edit').on('submit', function(e) {

                        e.preventDefault();
                        normalizeTimeFields('#activities_shift_edit');
                        const formData = $(this).serialize();
                        $.ajax({
                            url: `{{ route('Update.shift', ['box_slug' => request()->route('box_slug')]) }}`,
                            method: 'PUT',
                            data: formData,
                            success: function(response) {
                                showToast(response.message || 'Shift updated successfully!',
                                    'success');
                                $('#activities_shift_edit')[0].reset();
                                hideOffcanvas('#offcanvas_edit');
                                $('#shifts-table').DataTable().ajax.reload(null, false);
                            },

                            error: function(xhr) {
                                console.error(xhr.responseJSON);
                                showToast('Something went wrong while updating the shift!',
                                    'error');
                            }
                        });
                    });
                }

                // Delete shift handling
                let shiftIdToDelete = null;
                $(document).on('click', '.delete-shift', function() {
                    shiftIdToDelete = $(this).data('id');
                    $('#delete_shift').modal('show');
                });
                $('#confirmDeleteShift').on('click', function() {
                    if (!shiftIdToDelete) return;
                    $.ajax({
                        url: `{{ route('delete.shift', ['box_slug' => request()->route('box_slug')]) }}`,
                        method: 'POST',
                        data: {
                            shift_id: shiftIdToDelete,
                            _method: 'DELETE',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            showToast(response.message || 'Shift deleted successfully!', 'success');
                            $('#delete_shift').modal('hide');
                            $('#shifts-table').DataTable().ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            console.error(xhr.responseJSON);
                            showToast('Failed to delete shift.', 'error');
                        }
                    });
                });
                // Init everything
                // Table is already initialized in loadColumnPreferences callback above
                submitShiftForm();
                editShiftForm();

                let selectedShiftId = null;
                $('#shifts-table').on('change', '.shift-select', function() {
                    $('.shift-select').not(this).prop('checked', false);
                    selectedShiftId = $(this).is(':checked') ? $(this).data('shift') : null;
                });

                // REMOVED: Old duplicate handler that was calling API directly on button click
                // The main handler at line 2180 opens the modal instead
                // API is now only called from the modal's confirm button (line 2223) after month/year selection

                const sections = [{
                        start: 'in_times',
                        end: 'out_times',
                        result: 'shift_hourss'
                    },
                    {
                        start: 'lunch_outs',
                        end: 'lunch_ins',
                        result: 'lunch_hourss'
                    },
                    {
                        start: 'short_break_outss',
                        end: 'short_break_inss',
                        result: 'short_break_hourss'
                    },
                    {
                        start: 'short_break_out_2',
                        end: 'short_break_in_2',
                        result: 'short_break_hours_2'
                    },
                    {
                        start: 'half_day_start_timess',
                        end: 'half_day_end_timess',
                        result: 'half_day_hourss'
                    }
                ];

                function parseTime(timeStr) {
                    const time = timeStr.trim().toUpperCase();
                    const match = time.match(/^(\d{1,2}):(\d{2})\s?(AM|PM)?$/);
                    if (!match) return null;
                    let [_, hours, minutes, meridian] = match;
                    hours = parseInt(hours, 10);
                    minutes = parseInt(minutes, 10);
                    if (meridian == 'PM' && hours < 12) hours += 12;
                    if (meridian == 'AM' && hours == 12) hours = 0;
                    return {
                        hours,
                        minutes
                    };
                }

                function calculateTimeDifference(startId, endId, outputId) {
                    const startTimeStr = $('#' + startId).val();
                    const endTimeStr = $('#' + endId).val();
                    if (!startTimeStr || !endTimeStr) {
                        $('#' + outputId).val('');
                        return;
                    }
                    const startParsed = parseTime(startTimeStr);
                    const endParsed = parseTime(endTimeStr);
                    if (!startParsed || !endParsed) {
                        $('#' + outputId).val('');
                        return;
                    }
                    const start = new Date();
                    const end = new Date();
                    start.setHours(startParsed.hours, startParsed.minutes, 0);
                    end.setHours(endParsed.hours, endParsed.minutes, 0);
                    if (end <= start) {
                        end.setDate(end.getDate() + 1);
                    }
                    const diffMs = end - start;
                    const hours = Math.floor(diffMs / (1000 * 60 * 60));
                    const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                    const result = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
                    $('#' + outputId).val(result);
                }
                sections.forEach(section => {
                    $(document).on('change keyup blur', `#${section.start}, #${section.end}`, function() {
                        console.log(`Calculating: ${section.start} → ${section.end}`);
                        calculateTimeDifference(section.start, section.end, section.result);
                    });
                });
                const sectionsedit = [{
                        start: 'shift_in_time',
                        end: 'shift_out_time',
                        result: 'shift_hours'
                    },
                    {
                        start: 'lunch_out',
                        end: 'lunch_in',
                        result: 'lunch_hours'
                    },
                    {
                        start: 'short_break_outs',
                        end: 'short_break_in',
                        result: 'short_break_hours'
                    },
                    {
                        start: 'short_break_outs_2',
                        end: 'short_break_ins_2',
                        result: 'short_break_hourss_2'
                    },
                    {
                        start: 'half_day_start_time',
                        end: 'half_day_end_time',
                        result: 'half_day_hours'
                    }
                ];

                function parseTimeedit(timeStr) {
                    const time = timeStr.trim().toUpperCase();
                    const match = time.match(/^(\d{1,2}):(\d{2})\s?(AM|PM)?$/);
                    if (!match) return null;
                    let [_, hours, minutes, meridian] = match;
                    hours = parseInt(hours, 10);
                    minutes = parseInt(minutes, 10);
                    if (meridian == 'PM' && hours < 12) hours += 12;
                    if (meridian == 'AM' && hours == 12) hours = 0;
                    return {
                        hours,
                        minutes
                    };
                }

                function calculateTimeDifferenceedit(startId, endId, outputId) {
                    const startTimeStr = $('#' + startId).val();
                    const endTimeStr = $('#' + endId).val();
                    if (!startTimeStr || !endTimeStr) {
                        $('#' + outputId).val('');
                        return;
                    }
                    const startParsed = parseTimeedit(startTimeStr);
                    const endParsed = parseTimeedit(endTimeStr);
                    if (!startParsed || !endParsed) {
                        $('#' + outputId).val('');
                        return;
                    }
                    const start = new Date();
                    const end = new Date();
                    start.setHours(startParsed.hours, startParsed.minutes, 0);
                    end.setHours(endParsed.hours, endParsed.minutes, 0);
                    // If end is earlier than start, assume next day
                    if (end <= start) {
                        end.setDate(end.getDate() + 1);
                    }
                    const diffMs = end - start;
                    const hours = Math.floor(diffMs / (1000 * 60 * 60));
                    const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                    const result = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
                    $('#' + outputId).val(result);
                }
                sectionsedit.forEach(section => {
                    $(document).on('change keyup blur', `#${section.start}, #${section.end}`, function() {
                        console.log(`Calculating: ${section.start} → ${section.end}`);
                        calculateTimeDifferenceedit(section.start, section.end, section.result);
                    });
                });
                $(document).on('click', '.edit-shift', function(e) {
                    e.preventDefault();
                    const shiftId = $(this).data('id');
                    populateEditForm(shiftId);
                    setTimeout(() => {
                        updateSaturdayHeadings();
                        updateWeeklyOffOptions();
                        updateSaturdayCheckboxLogic();
                    }, 50);
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

                    // Apply column visibility and redraw table immediately
                    if (shiftTable) {
                        columnDefinitions.forEach(col => {
                            try {
                                shiftTable.column(col.index).visible(col.visible, false);
                            } catch (e) {
                                console.error('Error applying column visibility:', e);
                            }
                        });
                        shiftTable.columns.adjust();
                        // Redraw only once after all changes
                        shiftTable.draw(false);
                    }

                    // Close modal
                    $('#manageColumnsModal').modal('hide');
                });

            });
        </script>
    @endpush

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

<!-- Generate Shift Modal -->
<div class="modal fade" id="generateShiftModal" tabindex="-1" role="dialog" aria-labelledby="generateShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateShiftModalLabel">Generate Shift for Employees</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="generateShiftForm">
                    <div class="mb-3">
                        <label for="generate_shift_month" class="form-label">Select Month</label>
                        <select class="form-select" id="generate_shift_month" name="month" required>
                            <option value="">Select Month</option>
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
                    <div class="mb-3">
                        <label for="generate_shift_year" class="form-label">Select Year</label>
                        <select class="form-select" id="generate_shift_year" name="year" required>
                            <option value="">Select Year</option>
                            @php
                                $currentYear = date('Y');
                                $startYear = $currentYear - 5;
                                $endYear = $currentYear;
                            @endphp
                            @for($year = $endYear; $year >= $startYear; $year--)
                                <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmGenerateShiftBtn">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    Generate Shift
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
