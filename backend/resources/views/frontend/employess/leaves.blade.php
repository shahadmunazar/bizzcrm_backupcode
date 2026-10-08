@extends('layouts.main')
@section('content')
@section('title', 'Leaves')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Leaves </h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="index.html"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            Employee
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Leaves </li>
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
                <div class="mb-2">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#add_leaves"
                        class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Apply
                        Leave </a>
                </div>
                <div class="head-icons ms-2">
                    <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-original-title="Collapse" id="collapse-header">
                        <i class="ti ti-chevrons-up"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- /Breadcrumb -->




        <!-- new cards -->

        <div class="row">
                     <div class="col-xl-6 d-flex">
                         <div class="row flex-fill">
                             <!-- Total Companies -->
                             <div class="col-lg-6 col-md-6 d-flex">
                                 <div class="card flex-fill">
                                     <div class="card-body">
                                         <div class="d-flex align-items-center justify-content-between mb-2 overflow-hidden">
                                              <div>
                                                  <p class="fs-12 fw-normal mb-1 text-truncate">Total Leaves </p>
                                                  <h4 id="stat-total-leaves">0 </h4>
                                              </div>
											 <div class="leave-report-icon">
												 <a href="#"><span class="p-2 border border-danger bg-transparent-danger rounded-circle d-flex align-items-center justify-content-center"><i class="ti ti-calendar-x text-danger"></i></span></a>
											 </div>
                                         </div>
										 <div class="p-2 bg-gray-100 br-5">
											 <div class="d-flex align-items-center justify-content-between">
												 <p class="fs-12 fw-normal mb-0 comparison-label">Last Month </p>
												 <span class="fs-12 fw-normal text-success d-flex align-items-center" id="stat-total-leaves-growth"><i class="ti ti-arrow-wave-right-up text-success me-1"></i>+0% </span>
											 </div>
                                             <div class="progress progress-xs mt-2" style="height: 5px;">
                                                <div class="progress-bar bg-danger" id="stat-total-leaves-progress" role="progressbar" style="width: 100%"></div>
                                             </div>
										 </div>
                                     </div>
                                 </div>
                             </div>
                             <!-- /Total Companies -->

                              <!-- Total Companies -->
                             <div class="col-lg-6 col-md-6 d-flex">
                                 <div class="card flex-fill">
                                     <div class="card-body">
                                         <div class="d-flex align-items-center justify-content-between mb-2 overflow-hidden">
                                              <div>
                                                  <p class="fs-12 fw-normal mb-1 text-truncate">Approved Leaves </p>
                                                  <h4 id="stat-approved-leaves">0 </h4>
                                              </div>
											 <div class="leave-report-icon">
												 <a href="#"><span class="p-2 border border-success bg-transparent-success rounded-circle d-flex align-items-center justify-content-center"><i class="ti ti-calendar-x text-success"></i></span></a>
											 </div>
                                         </div>
										 <div class="p-2 bg-gray-100 br-5">
											 <div class="d-flex align-items-center justify-content-between">
												 <p class="fs-12 fw-normal mb-0 comparison-label">Last Month </p>
												 <span class="fs-12 fw-normal text-success d-flex align-items-center" id="stat-approved-leaves-growth"><i class="ti ti-arrow-wave-right-up text-success me-1"></i>+0% </span>
											 </div>
                                             <div class="progress progress-xs mt-2" style="height: 5px;">
                                                <div class="progress-bar bg-success" id="stat-approved-leaves-progress" role="progressbar" style="width: 0%"></div>
                                             </div>
										 </div>

                                     </div>
                                 </div>
                             </div>
                             <!-- /Total Companies -->

                             <!-- Inactive Companies -->
							 <div class="col-lg-6 col-md-6 d-flex">
                                 <div class="card flex-fill">
                                     <div class="card-body">
                                         <div class="d-flex align-items-center justify-content-between mb-2 overflow-hidden">
                                              <div>
                                                  <p class="fs-12 fw-normal mb-1 text-truncate">Pending Requests </p>
                                                  <h4 id="stat-pending-requests">0 </h4>
                                              </div>
											 <div class="leave-report-icon">
												 <a href="#"><span class="p-2 border border-dark bg-transparent-dark rounded-circle d-flex align-items-center justify-content-center"><i class="ti ti-calendar-x text-dark"></i></span></a>
											 </div>
                                         </div>
										 <div class="p-2 bg-gray-100 br-5">
											 <div class="d-flex align-items-center justify-content-between">
												 <p class="fs-12 fw-normal mb-0 comparison-label">Last Month </p>
												 <span class="fs-12 fw-normal text-success d-flex align-items-center" id="stat-pending-requests-growth"><i class="ti ti-arrow-wave-right-up text-success me-1"></i>+0% </span>
											 </div>
                                             <div class="progress progress-xs mt-2" style="height: 5px;">
                                                <div class="progress-bar bg-secondary" id="stat-pending-requests-progress" role="progressbar" style="width: 0%"></div>
                                             </div>
										 </div>

                                     </div>
                                 </div>
                             </div>
                             <!-- /Inactive Companies -->

                             <!-- Company Location -->
                             <div class="col-lg-6 col-md-6 d-flex">
                                 <div class="card flex-fill">
                                     <div class="card-body">
                                         <div class="d-flex align-items-center justify-content-between mb-2 overflow-hidden">
                                              <div>
                                                  <p class="fs-12 fw-normal mb-1 text-truncate">Rejected Leaves </p>
                                                  <h4 id="stat-rejected-leaves">0 </h4>
                                              </div>
											 <div class="leave-report-icon">
												 <a href="#"><span class="p-2 border border-danger bg-transparent-danger rounded-circle d-flex align-items-center justify-content-center"><i class="ti ti-calendar-x text-danger"></i></span></a>
											 </div>
                                         </div>
										 <div class="p-2 bg-gray-100 br-5">
											 <div class="d-flex align-items-center justify-content-between">
												 <p class="fs-12 fw-normal mb-0 comparison-label">Last Month </p>
												 <span class="fs-12 fw-normal text-success d-flex align-items-center" id="stat-rejected-leaves-growth"><i class="ti ti-arrow-wave-right-up text-success me-1"></i>+0% </span>
											 </div>
                                             <div class="progress progress-xs mt-2" style="height: 5px;">
                                                <div class="progress-bar bg-danger" id="stat-rejected-leaves-progress" role="progressbar" style="width: 0%"></div>
                                             </div>
										 </div>

                                     </div>
                                 </div>
                             </div>
                             <!-- /Company Location -->
                         </div>
                     </div>
					 <div class="col-xl-6 d-flex">
                         <div class="card flex-fill">
							 <div class="card-header border-0 pb-0">
								 <div class="d-flex flex-wrap justify-content-between align-items-center row-gap-2">
									 <div class="d-flex align-items-center ">
										 <span class="me-2"><i class="ti ti-chart-bar text-danger"></i></span>
										 <h5>Leaves  </h5>
									 </div>
									 <div class="d-flex align-items-center" id="chart-legend">
										 <!-- Legend will be populated dynamically -->
									 </div>
									 <div class="dropdown">
										 <a href="javascript:void(0);" class="dropdown-toggle btn btn-sm fs-12 btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" id="selected-year-text">
											{{ date('Y') }}
										 </a>
										 <ul class="dropdown-menu  dropdown-menu-end p-2" id="year-dropdown">
											 <li>
												 <a href="javascript:void(0);" class="dropdown-item rounded-1 year-filter" data-year="2025">2025 </a>
											 </li>
											 <li>
												 <a href="javascript:void(0);" class="dropdown-item rounded-1 year-filter" data-year="2024">2024 </a>
											 </li>
											 <li>
												 <a href="javascript:void(0);" class="dropdown-item rounded-1 year-filter" data-year="2023">2023 </a>
											 </li>
										 </ul>
									 </div>
								 </div>
							 </div>
                             <div class="card-body py-0">
								 <div id="leave-report-user">  </div>
							 </div>
                         </div>
                     </div>
				 </div>


        <!-- Leaves Info -->
       <!-- <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-green-img">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <span
                                        class="avatar avatar-md rounded-circle bg-white d-flex align-items-center justify-content-center">
                                        <i class="ti ti-user-check text-success fs-18"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="text-end">
                                <p class="mb-1">Total Present </p>
                                <h4>180/200 </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-pink-img">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <span
                                        class="avatar avatar-md rounded-circle bg-white d-flex align-items-center justify-content-center">
                                        <i class="ti ti-user-edit text-pink fs-18"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="text-end">
                                <p class="mb-1">Planned Leaves </p>
                                <h4>10 </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-yellow-img">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <span
                                        class="avatar avatar-md rounded-circle bg-white d-flex align-items-center justify-content-center">
                                        <i class="ti ti-user-exclamation text-warning fs-18"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="text-end">
                                <p class="mb-1">Unplanned Leaves </p>
                                <h4>10 </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-blue-img">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <span
                                        class="avatar avatar-md rounded-circle bg-white d-flex align-items-center justify-content-center">
                                        <i class="ti ti-user-question text-info fs-18"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="text-end">
                                <p class="mb-1">Pending Requests </p>
                                <h4>15 </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- /Leaves Info -->

        <!-- Leaves list -->
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                <h5>Leave List </h5>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="me-3">
                        <button type="button" class="btn btn-white d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                            <i class="ti ti-columns me-1"></i>Manage Columns
                        </button>
                    </div>
                    <div class="me-3">
                        <div class="input-icon-end position-relative">
                            <div class="input-icon-end position-relative">
                    <input type="text" id="date-range" class="form-control date-range bookingrange"
                           placeholder="dd/mm/yyyy - dd/mm/yyyy" />
                    <span class="input-icon-addon"><i class="ti ti-chevron-down"></i></span>
                </div>
                <button class="btn btn-sm btn-link text-danger p-0 ms-1 d-none" id="clear-date-range" title="Clear Date">
                    &times;
                </button>
                        </div>
                    </div>
                    <div class="dropdown me-3">
<a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white d-inline-flex align-items-center"
                   data-bs-toggle="dropdown" id="selected-leave-type">
                    Leave Type
                </a>
                <button class="btn btn-sm btn-link text-danger p-0 ms-1 d-none" id="clear-leave-type" title="Clear Leave Type">
                    &times;
                </button>
                <ul class="dropdown-menu dropdown-menu-end p-3" id="leave-type-dropdown">
                    <li><span class="text-muted">Loading...</span></li>
                </ul>
                    </div>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white d-inline-flex align-items-center"
                   data-bs-toggle="dropdown" id="selected-status">
                    Select Status
                </a>
                <button class="btn btn-sm btn-link text-danger p-0 ms-1 d-none" id="clear-status" title="Clear Status">
                    &times;
                </button>
                <ul class="dropdown-menu dropdown-menu-end p-3" id="status-dropdown">
                    <li><a href="javascript:void(0);" class="dropdown-item status-filter" data-status="approved">Approved</a></li>
                    <li><a href="javascript:void(0);" class="dropdown-item status-filter" data-status="declined">Declined</a></li>
                    <li><a href="javascript:void(0);" class="dropdown-item status-filter" data-status="pending">Pending</a></li>
                </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="custom-datatable-filter table-responsive">
                    <table id="leaveRequestsEmployeeTable" class="table">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Approved By</th>
                                <th>Leave Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th>No of Days</th>
                                <th>Contact During</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Attachment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>


                </div>
            </div>
        </div>
        <!-- /Leaves list -->

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

<!-- Add Leaves -->
@include('frontend.attendance.partials.add_leave_modal')
<!-- /Add Leaves -->

<!-- Edit Leaves -->
<div class="modal fade" id="edit_leaves">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Leave </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="edit_leaves_apply" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body pb-0">
                    <input type="hidden" name="leave_request_id" id="leave_request_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Leave Type </label>
                                <select class="select" id="edit_leave_type" name="leave_type">
                                    <option disabled selected>Select Option</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Leave Balance</label>
                                <input type="text" class="form-control" name="leave_balance" id="leave_balance" readonly />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Applied For </label>
                                <select class="select" id="applied_for" name="applied_for">
                                    <option disabled>Applied For</option>
                                    <option value="full_day" selected>Full Day</option>
                                    <option value="half_day">Half Day</option>
                                    <option value="short_leave">Short Leave</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">From Date </label>
                                <div class="input-icon-end position-relative">
                                    <input type="date" class="form-control datePicker" id="from_date" name="from_date"
                                        placeholder="MM/DD/YYYY" readonly>
                                    <span class="input-icon-addon">
                                        <i class="ti ti-calendar text-gray-7"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">To Date </label>
                                <div class="input-icon-end position-relative">
                                    <input type="date" class="form-control datePicker" id="to_date" name="to_date"
                                        placeholder="MM/DD/YYYY" readonly>
                                    <span class="input-icon-addon">
                                        <i class="ti ti-calendar text-gray-7"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total Days </label>
                                <input type="text" class="form-control" id="total_days" name="total_days" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Contact During Leave </label>
                                <input type="text" class="form-control" name="contact_during_leave" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Reason for Leave </label>
                                <textarea class="form-control" name="reason" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Attachment (PDF, DOC, DOCX, JPG, PNG) </label>
                                <input type="file" class="form-control" name="attachment" id="attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <div id="file-preview" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel </button>
                    <button type="submit" class="btn btn-primary">Apply </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- /Edit Leaves -->

<!-- Delete Modal -->
<!-- Modal (keep in your HTML) -->
<div class="modal fade" id="delete_modal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center">
        <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
          <i class="ti ti-trash-x fs-36"></i>
        </span>
        <h4 class="mb-1">Cancel Leave Request</h4>
        <p class="mb-3">
          Are you sure you want to cancel <strong id="delete_item_name">this leave request</strong>? This action <strong>cannot</strong> be undone.
        </p>
        <div class="d-flex justify-content-center">
          <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
          <a href="#" class="btn btn-danger" id="confirm_delete_btn">Yes, Cancel Request</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- /Delete Modal -->
@push('scripts')
<script>
$(document).ready(function () {
    let selectedLeaveType = '';
    let selectedStatus = '';
    let selectedDateRange = '';
    let selectedYear = new Date().getFullYear();
    let leaveChart;


    // Initialize Date Range Picker
    if ($('.bookingrange').length > 0) {
        $('.bookingrange').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            autoUpdateInput: false,
            locale: {
                format: 'DD/MM/YYYY',
                cancelLabel: 'Clear'
            }
        });

        $('.bookingrange').on('apply.daterangepicker', function(ev, picker) {
            selectedDateRange = picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY');
            $(this).val(selectedDateRange);
            $('#clear-date-range').removeClass('d-none');
            table.ajax.reload();
        });

        $('.bookingrange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            selectedDateRange = '';
            $('#clear-date-range').addClass('d-none');
            table.ajax.reload();
        });
    }

    // Utility Functions
    function getColorFromName(name) {
        const colors = [
            "#f44336","#e91e63","#9c27b0","#673ab7","#3f51b5",
            "#2196f3","#03a9f4","#00bcd4","#009688","#4caf50",
            "#8bc34a","#cddc39","#ffc107","#ff9800","#ff5722"
        ];
        return name ? colors[name.charCodeAt(0) % colors.length] : colors[0];
    }

    function getInitials(name) {
        if (!name) return '';
        const parts = name.trim().split(' ');
        return parts.length === 1
            ? parts[0].charAt(0).toUpperCase()
            : (parts[0].charAt(0) + parts[1].charAt(0)).toUpperCase();
    }

    function debounce(fn, delay) {
        let timer = null;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    function formatAvatar(data) {
        if (!data) return 'N/A';
        const name = data.name || 'N/A';
        const image = data.image;
        const initials = getInitials(name);
        return image && image !== 'null'
            ? `<a href="javascript:void(0);" class="avatar avatar-md border avatar-rounded">
                    <img src="${image}" class="img-fluid rounded-circle" alt="${name}" width="40" height="40" />
               </a>`
            : `<span class="avatar avatar-md text-white rounded-circle letter-avatar"
                     style="background-color:${getColorFromName(name)};width:40px;height:40px;
                     display:flex;align-items:center;justify-content:center;font-weight:bold;">
                     ${initials}
               </span>`;
    }

    function calculateTotalDays(fromSelector, toSelector, appliedForSelector, targetSelector) {
        let appliedFor = $(appliedForSelector).val();
        if (!appliedFor) return $(targetSelector).val('');

        let factor = appliedFor === 'half_day' ? 0.5 : (appliedFor === 'short_leave' ? 0.25 : 1);
        let fromDate = new Date($(fromSelector).val());
        let toDate = new Date($(toSelector).val());

        if (!isNaN(fromDate.getTime()) && !isNaN(toDate.getTime())) {
            let dayDiff = Math.ceil((toDate - fromDate) / (1000 * 3600 * 24)) + 1;
            $(targetSelector).val(dayDiff > 0 ? (dayDiff * factor).toFixed(2) : '');
        } else {
            $(targetSelector).val('');
        }
    }

    function handleFilePreview(inputSelector, previewSelector) {
        $(document).on('change', inputSelector, function (e) {
            const file = e.target.files[0];
            const preview = $(previewSelector);
            preview.empty();
            if (!file) return;
            const fileType = file.type;
            const fileName = file.name;
            const fileSize = (file.size / 1024).toFixed(2);
            let previewHtml = '';
            if (fileType.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewHtml = `<div class="border p-2 rounded position-relative">
                        <img src="${e.target.result}" alt="${fileName}" style="max-width:200px;max-height:200px;">
                        <p class="mb-1 mt-1"><small><strong>${fileName}</strong> (${fileSize} KB)</small></p>
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-file">×</button>
                    </div>`;
                    preview.html(previewHtml);
                };
                reader.readAsDataURL(file);
            } else {
                let iconClass = 'ti ti-file fs-2 text-secondary';
                let typeLabel = 'File';
                if (fileType === 'application/pdf') {
                    iconClass = 'ti ti-file-text fs-2 text-primary';
                    typeLabel = 'PDF';
                } else if (fileType.includes('word')) {
                    iconClass = 'ti ti-file-word fs-2 text-blue';
                    typeLabel = 'Document';
                }
                previewHtml = `<div class="border p-2 rounded d-flex align-items-center position-relative">
                    <i class="${iconClass} me-2"></i>
                    <div>
                        <p class="mb-1"><strong>${fileName}</strong></p>
                        <small class="text-muted">${fileSize} KB - ${typeLabel}</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger ms-auto remove-file">Remove</button>
                </div>`;
                preview.html(previewHtml);
            }
        });

        $(document).on('click', `${previewSelector} .remove-file`, function () {
            $(inputSelector).val('');
            $(previewSelector).empty();
        });
    }
    function formatAvatarWithInfo(data) {
    if (!data) return 'N/A';
    const avatarHTML = formatAvatar(data);
    return `
        <div class="d-flex align-items-center file-name-icon">
            ${avatarHTML}
            <div class="ms-2">
                <h6 class="fw-medium mb-0"><a href="javascript:void(0);">${data.name || 'N/A'}</a></h6>
                <span class="fs-12 fw-normal">ID: ${data.employee_id ?? 'N/A'}</span><br>
                <span class="fs-12 fw-normal">${data.department ?? 'N/A'}</span>
            </div>
        </div>
    `;
}

    // Column definitions with names
    const columnDefinitions = [
        { index: 0, name: '#', visible: true },
        { index: 1, name: 'Employee', visible: true },
        { index: 2, name: 'Approved By', visible: true },
        { index: 3, name: 'Leave Type', visible: true },
        { index: 4, name: 'From', visible: true },
        { index: 5, name: 'To', visible: true },
        { index: 6, name: 'No of Days', visible: true },
        { index: 7, name: 'Contact During', visible: true },
        { index: 8, name: 'Reason', visible: true },
        { index: 9, name: 'Status', visible: true },
        { index: 10, name: 'Attachment', visible: true },
        { index: 11, name: 'Actions', visible: true }
    ];

    // Page identifier for column preferences
    const pageIdentifier = 'leaves';

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

    // Global table variable
    let table;

    // Apply column visibility (without triggering drawCallback)
    function applyColumnVisibility() {
        if (table && $.fn.DataTable.isDataTable('#leaveRequestsEmployeeTable')) {
            columnDefinitions.forEach(col => {
                try {
                    table.column(col.index).visible(col.visible, false);
                } catch (e) {
                    console.error('Error applying column visibility:', e);
                }
            });
            // Only adjust columns, don't redraw to avoid infinite loop
            table.columns.adjust();
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

    // DataTable Initialization function
    function initializeDataTable() {
        table = $('#leaveRequestsEmployeeTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('get.data.employee.request', ['box_slug' => request()->route('box_slug')]) }}",
            data: function (d) {
                d.leave_type = selectedLeaveType;
                d.status = selectedStatus;
                d.date_range = selectedDateRange;
                d.year = selectedYear;
            },
            dataSrc: function (json) {
                if (json.summary) {
                    $('#stat-total-leaves').text(json.summary.total || 0);
                    $('#stat-approved-leaves').text(json.summary.approved || 0);
                    $('#stat-pending-requests').text(json.summary.pending || 0);
                    $('#stat-rejected-leaves').text(json.summary.rejected || 0);

                    // Update Progress Bars
                    const total = json.summary.total || 1; // avoid division by zero
                    $('#stat-total-leaves-progress').css('width', '100%');
                    $('#stat-approved-leaves-progress').css('width', ((json.summary.approved / total) * 100) + '%');
                    $('#stat-pending-requests-progress').css('width', ((json.summary.pending / total) * 100) + '%');
                    $('#stat-rejected-leaves-progress').css('width', ((json.summary.rejected / total) * 100) + '%');
                }
                if (json.comparisonLabel) {
                    $('.comparison-label').text(json.comparisonLabel);
                }
                if (json.growth) {
                    updateGrowth('#stat-total-leaves-growth', json.growth.total);
                    updateGrowth('#stat-approved-leaves-growth', json.growth.approved);
                    updateGrowth('#stat-pending-requests-growth', json.growth.pending);
                    updateGrowth('#stat-rejected-leaves-growth', json.growth.rejected);
                }
                if (json.chartData) {
                    updateChart(json.chartData);
                    updateLegend(json.chartData);
                }
                return json.data;
            }
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false, visible: columnDefinitions[0].visible },
            {
                data: 'employee',
                render: function (data) {
                    if (!data) {
                        return '<span class="text-muted">N/A</span>';
                    }
                    return `<div class="d-flex align-items-center file-name-icon">
                        ${formatAvatar(data)}
                        <div class="ms-2">
                            <h6 class="fw-medium mb-0"><a href="javascript:void(0);">${data.name || 'N/A'}</a></h6>
                            <span class="fs-12 fw-normal">ID: ${data.employee_id ?? 'N/A'}</span><br>
                            <span class="fs-12 fw-normal">${data.department ?? 'N/A'}</span>
                        </div>
                    </div>`;
                },
                orderable: false,
                searchable: false,
                visible: columnDefinitions[1].visible
            },
            { data: 'approved_by', render: function(data){ return formatAvatarWithInfo(data); }, orderable: false, searchable: false, visible: columnDefinitions[2].visible },
            { data: 'leave_type', name: 'leave_type', visible: columnDefinitions[3].visible },
            { data: 'from', name: 'from_date', visible: columnDefinitions[4].visible },
            { data: 'to', name: 'to_date', visible: columnDefinitions[5].visible },
            { data: 'no_of_days', name: 'total_days', visible: columnDefinitions[6].visible },
            { data: 'contact_during_leave', name: 'contact_during_leave', visible: columnDefinitions[7].visible },
            { data: 'reason', name: 'reason', visible: columnDefinitions[8].visible },
            { data: 'status', name: 'status', visible: columnDefinitions[9].visible },
            { data: 'attachment', render: function(data){ return data ? `<a href="${data}" target="_blank" class="btn btn-sm btn-outline-secondary">View</a>` : '—'; }, orderable:false, searchable:false, visible: columnDefinitions[10].visible },
 {
    data: 'action',
    render: function(data, type, row) {
        const rowData = encodeURIComponent(JSON.stringify(row));
        const isPending = row.status === 'Pending';
        const editBtn = `<button class="btn btn-sm btn-primary edit-leave-btn" data-row="${rowData}" ${isPending ? '' : 'disabled'}>Edit</button>`;
        const deleteBtn = `<button class="btn btn-sm btn-danger" onclick="openDeleteModal(${row.id})" ${isPending ? '' : 'disabled'}>Cancel</button>`;

        return `${editBtn} ${deleteBtn}`;
    },
    orderable: false,
    searchable: false,
    visible: columnDefinitions[11].visible
}


        ]
    });
    } // End of initializeDataTable function

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
        if (table) {
            columnDefinitions.forEach(col => {
                try {
                    table.column(col.index).visible(col.visible, false);
                } catch (e) {
                    console.error('Error applying column visibility:', e);
                }
            });
            table.columns.adjust();
            // Redraw only once after all changes
            table.draw(false);
        }

        // Close modal
        $('#manageColumnsModal').modal('hide');
    });

    $('#leaveRequestsEmployeeTable_filter input').off().on('keyup', debounce(function () {
        table.search(this.value).draw();
    }, 500));
    // Load Leave Types
    function loadLeaveTypes(selectElementId, selectedId = null) {
        return $.ajax({
            url: '{{ route('leave.type.request', ['box_slug' => request()->route('box_slug')]) }}',
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                if (response.status) {
                    let optionsHtml = '<option disabled selected>Select Option</option>';
                    response.data.forEach(function (leaveType) {
                        optionsHtml += `<option value="${leaveType.id}">${leaveType.leave_type}</option>`;
                    });
                    $(`#${selectElementId}`).html(optionsHtml);
                    if (selectedId) {
                        $(`#${selectElementId}`).val(selectedId).trigger('change');
                    } else {
                        $(`#${selectElementId}`).trigger('change');
                    }
                } else {
                    showToast('Failed to load leave data.', 'error');
                }
            },
            error: function(xhr) {
                console.error("Error loading leave type data:", xhr);
                showToast('Failed to load leave data.', 'error');
            }
        });
    }

    // Leave Balance Fetch
    function fetchLeaveBalance(leaveTypeId, targetSelector) {
        if (!leaveTypeId) return $(targetSelector).val('');
        $.ajax({
            url: '{{ route('get.leave.balance', ['box_slug' => request()->route('box_slug')]) }}',
            method: 'GET',
            data: { leave_type_id: leaveTypeId },
            success: function (res) {
                if (res.status && res.data) {
                    $(targetSelector).val(res.data.balance_leave || '');
                } else {
                    $(targetSelector).val('');
                }
            },
            error: function () { $(targetSelector).val(''); }
        });
    }

    // Add Leave Modal
    $('#add_leaves').on('show.bs.modal', function () {
        loadLeaveTypes('select_leave_type');
        $('#select_leave_type').val('');
        $('#add_leaves input[name="leave_balance"]').val('');
        $('#add_leaves input[name="from_date"], #add_leaves input[name="to_date"], #applied_for').val('');
        $('#add_leaves input[name="total_days"]').val('');
        $('#file-preview').empty();
    });
    $('#select_leave_type').on('change', function () {
        let leaveTypeId = $(this).val();
        fetchLeaveBalance(leaveTypeId, '#add_leaves input[name="leave_balance"]');
    });
    $('#edit_leave_type').on('change', function () {
        let leaveTypeId = $(this).val();
        fetchLeaveBalance(leaveTypeId, '#edit_leaves input[name="leave_balance"]');
    });

    $(document).on('click', '.edit-leave-btn', function() {
        const row = JSON.parse(decodeURIComponent($(this).data('row')));
        openEditModal(row);
    });

    // Edit Leave Modal
    window.openEditModal = function (row) {
        $('#edit_leaves').modal('show');
        loadLeaveTypes('edit_leave_type', row.leave_type_id);
            $('#edit_leaves input[name="leave_request_id"]').val(row.id); // set hidden input in form
        fetchLeaveBalance(row.leave_type_id, '#edit_leaves input[name="leave_balance"]');
        $('#edit_leaves input[name="from_date"]').val(row.from || row.from_date || '');
        $('#edit_leaves input[name="to_date"]').val(row.to || row.to_date || '');
        $('#edit_leaves input[name="total_days"]').val(row.no_of_days || row.total_days || '');
        $('#edit_leaves input[name="contact_during_leave"]').val(row.contact_during_leave || '');
        $('#edit_leaves textarea[name="reason"]').val(row.reason || '');
        if (row.attachment) {
            $('#edit_leaves #file-preview').html(`<a href="${row.attachment}" target="_blank" class="btn btn-sm btn-outline-primary">View Current Attachment</a>`);
        } else {
            $('#edit_leaves #file-preview').empty();
        }
        $('#edit_leaves select[name="applied_for"]').val(row.applied_for || '').trigger('change');
    };

    // Total Days Calculation
    $("input[name='from_date'], input[name='to_date'], #applied_for").on('change', function () {
        calculateTotalDays("input[name='from_date']", "input[name='to_date']", "#applied_for", "input[name='total_days']");
    });
    $('#edit_leaves').on('change', '#from_date, #to_date, #applied_for', function () {
        calculateTotalDays("#edit_leaves #from_date", "#edit_leaves #to_date", "#edit_leaves #applied_for", "#edit_leaves #total_days");
    });

    // File Preview
    handleFilePreview('#add_leaves input[name="attachment"]', '#file-preview');
    handleFilePreview('#edit_leaves input[name="attachment"]', '#edit_leaves #file-preview');

    // Form Submission
    $('#edit_leaves_apply').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let url = '{{ route('leave.request.update', ['box_slug' => request()->route('box_slug')]) }}';
        $.ajax({
            type: 'POST',
            url: url,
            data: new FormData(form[0]),
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                if (res.status) {
                    showToast(res.message || 'Leave request updated successfully!', 'success');
                    $('#edit_leaves').modal('hide');
                    form.trigger('reset');
                    $('#file-preview').empty();
                    table.ajax.reload(null, false);
                } else {
                    showToast(res.message || 'Failed to update leave request.', 'error');
                }
            },
            error: function (xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) showToast(Object.values(errors).flat().join('\n'), 'error');
                else showToast(xhr.responseJSON?.message || 'Something went wrong.', 'error');
            }
        });
    });

    $('#leaves_apply').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let url = '{{ route('leave.request.store', ['box_slug' => request()->route('box_slug')]) }}';
        $.ajax({
            type: 'POST',
            url: url,
            data: new FormData(form[0]),
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                if (res.status) {
                    showToast(res.message || 'Leave request submitted successfully!', 'success');
                    $('#add_leaves').modal('hide');
                    form.trigger('reset');
                    $('#file-preview').empty();
                    table.ajax.reload(null, false);
                } else {
                    showToast(res.message || 'Failed to submit leave request.', 'error');
                }
            },
            error: function (xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) showToast(Object.values(errors).flat().join('\n'), 'error');
                else showToast(xhr.responseJSON?.message || 'Something went wrong.', 'error');
            }
        });
    });
    window.openDeleteModal = function(id) {
        deleteId = id;
        $('#delete_item_name').text(`Item #${id}`);
        const deleteModal = new bootstrap.Modal(document.getElementById('delete_modal'));
        deleteModal.show();
    };


    $('#confirm_delete_btn').on('click', function(e) {
        e.preventDefault();
        if (deleteId) {
            $.ajax({
                url: '{{ route('leave.request.delete', ['box_slug' => request()->route('box_slug')]) }}',
                type: 'DELETE',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: JSON.stringify({ id: deleteId }),
                success: function (response) {
                    $('#delete_modal').modal('hide');
                    if(response.success) {
                        showToast('Leave request cancelled successfully', 'success');
                        table.ajax.reload(null, false);
                    } else {
                        showToast('Error cancelling leave request', 'error');
                    }
                },
                error: function(xhr) {
                    showToast('Error cancelling leave request', 'error');
                }
            });
        }
    });

    // Chart Initialization
    function initChart(seriesData = []) {
        if (typeof ApexCharts === 'undefined') {
            console.error('ApexCharts is not defined');
            return;
        }
        const options = {
            series: seriesData,
            chart: {
                type: 'bar',
                height: 210,
                stacked: true,
                stackType: '100%',
                toolbar: { show: false }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    borderRadius: 5
                },
            },
            dataLabels: { enabled: false },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            },
            yaxis: {
                labels: {
                    offsetX: -15,
                }
            },
            fill: { opacity: 1 },
            legend: { show: false },
            colors: ['#03C95A', '#FFC107', '#0C4B5E', '#F26522'],
        };

        if (leaveChart) {
            leaveChart.destroy();
        }
        leaveChart = new ApexCharts(document.querySelector("#leave-report-user"), options);
        leaveChart.render();
    }

    function updateChart(newSeries) {
        if (!leaveChart) {
            initChart(newSeries);
        } else {
            leaveChart.updateSeries(newSeries);
        }
    }

    function updateLegend(seriesData) {
        const legendContainer = $('#chart-legend');
        legendContainer.empty();
        
        const colors = ['#03C95A', '#FFC107', '#0C4B5E', '#F26522'];
        
        seriesData.forEach((series, index) => {
            const color = colors[index % colors.length];
            const legendItem = `
                <p class="d-inline-flex align-items-center me-2 mb-0">
                    <i class="ti ti-square-filled fs-12 me-2" style="color: ${color}"></i>
                    ${series.name}
                </p>
            `;
            legendContainer.append(legendItem);
        });
    }

    function updateGrowth(selector, value) {
        const element = $(selector);
        const icon = value >= 0 ? 'ti-arrow-wave-right-up' : 'ti-arrow-wave-right-down';
        const textColor = value >= 0 ? 'text-success' : 'text-danger';
        const prefix = value >= 0 ? '+' : '';
        
        element.removeClass('text-success text-danger').addClass(textColor);
        element.html(`<i class="ti ${icon} ${textColor} me-1"></i>${prefix}${value}%`);
    }

    // Filter Listeners
    $(document).on('click', '.year-filter', function() {
        selectedYear = $(this).data('year');
        $('#selected-year-text').text(selectedYear);
        table.ajax.reload();
    });

    $(document).on('click', '.status-filter', function() {
        selectedStatus = $(this).data('status');
        $('#selected-status').text($(this).text());
        $('#clear-status').removeClass('d-none');
        table.ajax.reload();
    });

    $('#clear-status').on('click', function() {
        selectedStatus = '';
        $('#selected-status').text('Select Status');
        $(this).addClass('d-none');
        table.ajax.reload();
    });

    $(document).on('click', '.leave-type-filter', function() {
        selectedLeaveType = $(this).data('id');
        $('#selected-leave-type').text($(this).text());
        $('#clear-leave-type').removeClass('d-none');
        table.ajax.reload();
    });

    $('#clear-leave-type').on('click', function() {
        selectedLeaveType = '';
        $('#selected-leave-type').text('Leave Type');
        $(this).addClass('d-none');
        table.ajax.reload();
    });

        function populateLeaveTypeFilter() {
        $.ajax({
            url: '{{ route('leave.type.request', ['box_slug' => request()->route('box_slug')]) }}',
            type: 'GET',
            success: function(response) {
                if (response.status) {
                    let html = '';
                    response.data.forEach(type => {
                        html += `<li><a href="javascript:void(0);" class="dropdown-item leave-type-filter" data-id="${type.id}">${type.leave_type}</a></li>`;
                    });
                    $('#leave-type-dropdown').html(html);
                }
            }
        });
    }
    populateLeaveTypeFilter();
    initChart();

}); // End of $(document).ready

</script>
@endpush


@endsection
