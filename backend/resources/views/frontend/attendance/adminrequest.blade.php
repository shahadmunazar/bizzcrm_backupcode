@extends('layouts.main')
@section('content')
@section('title', 'Admin Requests')

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
                {{-- <div class="mb-2">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#add_leaves"
                        class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add
                        Leave </a>
                </div> --}}
                <div class="head-icons ms-2">
                    <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-original-title="Collapse" id="collapse-header">
                        <i class="ti ti-chevrons-up"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- /Breadcrumb -->


        <!-- new card -->

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
                                                 <h4 id="summary-total">{{ $stats['total'] }} </h4>
                                             </div>
											 <div class="leave-report-icon">
												 <a href="#"><span class="p-2 border border-primary bg-transparent-primary rounded-circle d-flex align-items-center justify-content-center"><i class="ti ti-calendar-x text-primary"></i></span></a>
											 </div>
                                         </div>
										 <div class="p-2 bg-gray-100 br-5">
											 <div class="d-flex align-items-center justify-content-between">
												 <p class="fs-12 fw-normal mb-0">Last Month </p>
												 <span class="fs-12 fw-normal text-{{ $stats['total_trend'] >= 0 ? 'success' : 'danger' }} d-flex align-items-center">
                                                     <i class="ti ti-arrow-wave-right-{{ $stats['total_trend'] >= 0 ? 'up' : 'down' }} text-{{ $stats['total_trend'] >= 0 ? 'success' : 'danger' }} me-1"></i>
                                                     {{ $stats['total_trend'] >= 0 ? '+' : '' }}{{ $stats['total_trend'] }}% 
                                                 </span>
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
                                                 <h4 id="summary-approved">{{ $stats['approved'] }} </h4>
                                             </div>
											 <div class="leave-report-icon">
												 <a href="#"><span class="p-2 border border-success bg-transparent-success rounded-circle d-flex align-items-center justify-content-center"><i class="ti ti-calendar-x text-success"></i></span></a>
											 </div>
                                         </div>
										 <div class="p-2 bg-gray-100 br-5">
											 <div class="d-flex align-items-center justify-content-between">
												 <p class="fs-12 fw-normal mb-0">Last Month </p>
												 <span class="fs-12 fw-normal text-{{ $stats['approved_trend'] >= 0 ? 'success' : 'danger' }} d-flex align-items-center">
                                                     <i class="ti ti-arrow-wave-right-{{ $stats['approved_trend'] >= 0 ? 'up' : 'down' }} text-{{ $stats['approved_trend'] >= 0 ? 'success' : 'danger' }} me-1"></i>
                                                     {{ $stats['approved_trend'] >= 0 ? '+' : '' }}{{ $stats['approved_trend'] }}% 
                                                 </span>
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
                                                 <h4 id="summary-pending">{{ $stats['pending'] }} </h4>
                                             </div>
											 <div class="leave-report-icon">
												 <a href="#"><span class="p-2 border border-skyblue bg-transparent-skyblue rounded-circle d-flex align-items-center justify-content-center"><i class="ti ti-calendar-x text-skyblue"></i></span></a>
											 </div>
                                         </div>
										 <div class="p-2 bg-gray-100 br-5">
											 <div class="d-flex align-items-center justify-content-between">
												 <p class="fs-12 fw-normal mb-0">Last Month </p>
												 <span class="fs-12 fw-normal text-{{ $stats['pending_trend'] >= 0 ? 'success' : 'danger' }} d-flex align-items-center">
                                                     <i class="ti ti-arrow-wave-right-{{ $stats['pending_trend'] >= 0 ? 'up' : 'down' }} text-{{ $stats['pending_trend'] >= 0 ? 'success' : 'danger' }} me-1"></i>
                                                     {{ $stats['pending_trend'] >= 0 ? '+' : '' }}{{ $stats['pending_trend'] }}% 
                                                 </span>
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
                                                 <h4 id="summary-declined">{{ $stats['declined'] }} </h4>
                                             </div>
											 <div class="leave-report-icon">
												 <a href="#"><span class="p-2 border border-danger bg-transparent-danger rounded-circle d-flex align-items-center justify-content-center"><i class="ti ti-calendar-x text-danger"></i></span></a>
											 </div>
                                         </div>
										 <div class="p-2 bg-gray-100 br-5">
											 <div class="d-flex align-items-center justify-content-between">
												 <p class="fs-12 fw-normal mb-0">Last Month </p>
												 <span class="fs-12 fw-normal text-{{ $stats['declined_trend'] >= 0 ? 'success' : 'danger' }} d-flex align-items-center">
                                                     <i class="ti ti-arrow-wave-right-{{ $stats['declined_trend'] >= 0 ? 'up' : 'down' }} text-{{ $stats['declined_trend'] >= 0 ? 'success' : 'danger' }} me-1"></i>
                                                     {{ $stats['declined_trend'] >= 0 ? '+' : '' }}{{ $stats['declined_trend'] }}% 
                                                 </span>
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
									 <div class="d-flex align-items-center">
                                         @php
                                             $colors = ['#03C95A', '#FFC107', '#0C4B5E', '#F26522', '#7638ff', '#fda600']; // Matching colors in JS
                                         @endphp
                                         @foreach ($formattedSeries as $index => $s)
                                             <p class="d-inline-flex align-items-center me-2 mb-0">
                                                 <i class="ti ti-square-filled fs-12 me-2" style="color: {{ $colors[$index % count($colors)] }}"></i>
                                                 {{ $s['name'] }}
                                             </p>
                                         @endforeach
									 </div>
									 <div class="dropdown">
										 <a href="javascript:void(0);" class="dropdown-toggle btn btn-sm fs-12 btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
											This Year
										 </a>
										 <ul class="dropdown-menu  dropdown-menu-end p-2">
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
                             <div class="card-body py-0">
								 <div id="leave-report">  </div>
							 </div>
                         </div>
                     </div>
				 </div>










        <!-- Leaves Info -->
       <!-- <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-black-le">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="text-start">
                                <p class="mb-1">Total Request</p>
                                <h4 id="summary-total">00</h4>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <span class="avatar avatar-md d-flex">
                                        <i class="ti ti-calendar-event fs-32"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-blue-le">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="text-start">
                                <p class="mb-1">Approved leaves </p>
                                <h4 id="summary-approved">00</h4>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <span class="avatar avatar-md d-flex">
                                        <i class="ti ti-vaccine fs-32"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-purple-le">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="text-start">
                                <p class="mb-1">Pending Leaves </p>
                                <h4 id="summary-pending">00</h4>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <span class="avatar avatar-md d-flex">
                                        <i class="ti ti-hexagon-letter-c fs-32"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-pink-le">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="text-start">
                                <p class="mb-1">Declined Leaves </p>
                                <h4 id="summary-declined">00</h4>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <span class="avatar avatar-md d-flex">
                                        <i class="ti ti-hexagonal-prism-plus fs-32"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- /Leaves Info -->

        <!-- Leaves list -->
        {{-- <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">

                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="me-3">
                        <div class="input-icon-end position-relative">
                            <input type="text" class="form-control date-range bookingrange"
                                placeholder="dd/mm/yyyy - dd/mm/yyyy" />
                            <span class="input-icon-addon">
                                <i class="ti ti-chevron-down"></i>
                            </span>
                        </div>
                    </div>
                    <div class="dropdown me-3">
    <a href="javascript:void(0);"
       class="dropdown-toggle btn btn-sm btn-white d-inline-flex align-items-center"
       data-bs-toggle="dropdown">
        Leave Type
    </a>
    <ul class="dropdown-menu dropdown-menu-end p-3" id="leave-type-dropdown">
        <li><span class="text-muted">Loading...</span></li>
    </ul>
</div>

                    <div class="dropdown me-3">
                        <a href="javascript:void(0);"
                            class="dropdown-toggle btn btn-sm btn-white d-inline-flex align-items-center"
                            data-bs-toggle="dropdown">
                            Select Status
                        </a>
                        <ul class="dropdown-menu  dropdown-menu-end p-3">
                            <li>
                                <a href="javascript:void(0);"
                                    class="dropdown-item rounded-1 d-flex justify-content-start align-items-center"><span
                                        class="rounded-circle bg-transparent-success d-flex justify-content-center align-items-center me-2"><i
                                            class="ti ti-point-filled text-success"></i></span>Approved </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);"
                                    class="dropdown-item rounded-1 d-flex justify-content-start align-items-center"><span
                                        class="rounded-circle bg-transparent-danger d-flex justify-content-center align-items-center me-2"><i
                                            class="ti ti-point-filled text-danger"></i></span>Declined </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);"
                                    class="dropdown-item rounded-1 d-flex justify-content-start align-items-center"><span
                                        class="rounded-circle bg-transparent-purple d-flex justify-content-center align-items-center me-2"><i
                                            class="ti ti-point-filled text-purple"></i></span>Pending </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div> --}}
            <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
        <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">

            <!-- Date Range -->
            <div class="me-3 d-flex align-items-center">
                <div class="input-icon-end position-relative">
                    <input type="text" id="date-range" class="form-control date-range bookingrange"
                           placeholder="dd/mm/yyyy - dd/mm/yyyy" />
                    <span class="input-icon-addon"><i class="ti ti-chevron-down"></i></span>
                </div>
                <button class="btn btn-sm btn-link text-danger p-0 ms-1 d-none" id="clear-date-range" title="Clear Date">
                    &times;
                </button>
            </div>

            <!-- Leave Type -->
            <div class="dropdown me-3 d-flex align-items-center">
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

            <!-- Status -->
            <div class="dropdown me-3 d-flex align-items-center">
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

            <!-- Manage Columns -->
            <div class="me-3 d-flex align-items-center">
                <button type="button" class="btn btn-sm btn-white d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                    <i class="ti ti-columns me-1"></i>Manage Columns
                </button>
            </div>
        </div>
    </div>
</div>

            <div class="card-body p-0">
                <div class="custom-datatable-filter table-responsive">
                    <table id="leaveRequestsTabledata" class="table">
                        <thead class="thead-light">
                            <tr>
                                <th class="no-sort">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox" id="select-all" />
                                    </div>
                                </th>
                                <th>Leave Type </th>
                                <th>Employee </th>
                                <th>Approved By</th>
                                <th>Employee Id</th>
                                <th>Desigination</th>
                                <th>From </th>
                                <th>To </th>
                                <th>No of Days </th>
                                <th>Contact During</th>
                                <th>Status</th>
                                <th>Reason for Leave</th>
                                <th>Applied For</th>
                                <th>Attachment</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- DataTables will fill this via ajax --}}
                        </tbody>
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
                    <a href="javascript:void(0);" class="btn btn-danger confirm-delete-leave">Yes, Delete </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-labelledby="statusUpdateLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="statusUpdateForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusUpdateLabel">Update Leave Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="leaveRequestId">
                    <input type="hidden" id="newStatus">
                    <div class="mb-3">
                        <label class="form-label">Selected Status:</label>
                        <div id="displayStatus" class="fw-bold text-capitalize text-primary"></div>
                    </div>
                    <div class="mb-3">
                        <label for="statusReason" class="form-label">Reason</label>
                        <textarea id="statusReason" class="form-control" rows="3" placeholder="Enter reason here..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="attachmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attachment Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="attachmentPreview"></div>
            </div>
        </div>
    </div>
</div>

<!-- /Delete Modal -->
@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.view-attachment', function(e) {
        e.preventDefault();
        const fileName = $(this).data('filename');
        const fileUrl = $(this).data('url');
        const fileType = fileName.split('.').pop().toLowerCase();
        const preview = $('#attachmentPreview');
        preview.empty();
        let content = '';
        if (['jpg', 'jpeg', 'png', 'gif'].includes(fileType)) {
            content = `<img src="${fileUrl}" class="img-fluid" alt="${fileName}" style="max-width: 100%; height: auto;">`;
        } else if (fileType === 'pdf') {
            content = `<iframe src="${fileUrl}" width="100%" height="500px"></iframe>`;
        } else {
            content = `
                <div class="text-center">
                    <i class="ti ti-file fs-1 text-muted mb-2"></i>
                    <p class="mb-2">Preview not available for this file type.</p>
                    <a href="${fileUrl}" target="_blank" class="btn btn-primary">Download File</a>
                </div>
            `;
        }

        preview.html(content);
        $('#attachmentModal').modal('show');
    });

    // ======================
    // Dynamic Leave Report Chart
    // ======================
    if ($('#leave-report').length > 0) {
        var leaveReportOptions = {
            series: @json($formattedSeries),
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
                    legend: { position: 'bottom', offsetX: -10, offsetY: 0 }
                }
            }],
            xaxis: {
                categories: @json($categories)
            },
            yaxis: {
                labels: { offsetX: -15 }
            },
            fill: { opacity: 1 },
            legend: { show: false },
            colors: ['#03C95A', '#FFC107', '#0C4B5E', '#F26522', '#7638ff', '#fda600'],
            dataLabels: { enabled: false }
        };

        // We use a small delay or check to ensure we override any potential default init from chart-data.js
        setTimeout(function() {
            var chartDiv = document.querySelector("#leave-report");
            if (chartDiv) {
                chartDiv.innerHTML = ''; // Clear any existing chart
                var leaveReportChart = new ApexCharts(chartDiv, leaveReportOptions);
                leaveReportChart.render();
            }
        }, 100);
    }



    let selectedLeaveType = '';
let selectedStatus = '';
let selectedDateRange = '';

const fromDashboardFlag = localStorage.getItem('fromDashboard');
    if (fromDashboardFlag) {
        selectedStatus = 'Pending';
        $('#selected-status').text('Pending');
        $('#clear-status').removeClass('d-none');
        localStorage.removeItem('fromDashboard');
    }
// ======================
// Load Leave Types from API
// ======================
$.ajax({
    url: "{{ route('leave.type.request', ['box_slug' => request()->route('box_slug')]) }}",
    type: 'GET',
    success: function(response) {
        const dropdown = $('#leave-type-dropdown');
        dropdown.empty();

        const leaveTypes = response?.data || [];

        if (leaveTypes.length > 0) {
            leaveTypes.forEach(function(type) {
                dropdown.append(`
                    <li>
                        <a href="javascript:void(0);" class="dropdown-item leave-type-option rounded-1" data-id="${type.id}">
                            ${type.leave_type}
                        </a>
                    </li>
                `);
            });
        } else {
            dropdown.append('<li><span class="text-muted">No Leave Types Found</span></li>');
        }
    },
    error: function(xhr) {
        $('#leave-type-dropdown').html('<li><span class="text-danger">Failed to load</span></li>');
        console.error("Error fetching leave types:", xhr.responseText);
    }
});

// ======================
// Leave Type Selection
// ======================
$(document).on('click', '.leave-type-option', function() {
    selectedLeaveType = $(this).data('id');
    $('#selected-leave-type').text($(this).text());
    $('#clear-leave-type').removeClass('d-none');
    $('#leaveRequestsTabledata').DataTable().ajax.reload();
});

$('#clear-leave-type').on('click', function() {
    selectedLeaveType = '';
    $('#selected-leave-type').text('Leave Type');
    $(this).addClass('d-none');
    $('#leaveRequestsTabledata').DataTable().ajax.reload();
});

// ======================
// Status Selection
// ======================
$(document).on('click', '.status-filter', function() {
    selectedStatus = $(this).data('status');
    $('#selected-status').text($(this).text());
    $('#clear-status').removeClass('d-none');
    $('#leaveRequestsTabledata').DataTable().ajax.reload();
});

$('#clear-status').on('click', function() {
    selectedStatus = '';
    $('#selected-status').text('Select Status');
    $(this).addClass('d-none');
    $('#leaveRequestsTabledata').DataTable().ajax.reload();
});

// ======================
// Date Range Picker
// ======================
$('#date-range').on('change', function () {
    selectedDateRange = $(this).val();
    $('#clear-date-range').removeClass('d-none');
    $('#leaveRequestsTabledata').DataTable().ajax.reload();
});

$('#clear-date-range').on('click', function () {
    selectedDateRange = '';
    $('#date-range').val('');
    $(this).addClass('d-none');
    $('#leaveRequestsTabledata').DataTable().ajax.reload();
});

// Column Definitions for Manage Columns feature
const columnDefinitions = [
    { index: 0, name: 'Checkbox', visible: true },
    { index: 1, name: 'Leave Type', visible: true },
    { index: 2, name: 'Employee', visible: true },
    { index: 3, name: 'Approved By', visible: true },
    { index: 4, name: 'Employee Id', visible: true },
    { index: 5, name: 'Designation', visible: true },
    { index: 6, name: 'From', visible: true },
    { index: 7, name: 'To', visible: true },
    { index: 8, name: 'No of Days', visible: true },
    { index: 9, name: 'Contact During', visible: true },
    { index: 10, name: 'Status', visible: true },
    { index: 11, name: 'Reason for Leave', visible: true },
    { index: 12, name: 'Applied For', visible: true },
    { index: 13, name: 'Attachment', visible: true },
    { index: 14, name: 'Actions', visible: true }
];

// Page identifier for column preferences
const pageIdentifier = 'leaves-admin';

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
let leaveRequestsTable;

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

// Initialize DataTable and store instance
loadColumnPreferences(function() {
    leaveRequestsTable = $('#leaveRequestsTabledata').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('get.data.admin.request', ['box_slug' => request()->route('box_slug')]) }}",
        type: 'GET',
        data: function(d) {
            d.leave_type = selectedLeaveType;
            d.status = selectedStatus;
            d.date_range = selectedDateRange;
        },
        dataSrc: function(json) {
            if (json.summary) {
                $('#summary-total').text(json.summary.total);
                $('#summary-approved').text(json.summary.approved);
                $('#summary-pending').text(json.summary.pending);
                $('#summary-declined').text(json.summary.declined);
            }
            return json.data;
        },
        error: function(xhr, error, thrown) {
            let errorMsg = 'An unexpected error occurred while loading data.';

            if (xhr.status === 404) {
                errorMsg = 'Data not found for the selected box. Please check the box slug.';
            } else if (xhr.status === 500) {
                try {
                    const responseJson = JSON.parse(xhr.responseText);
                    if (responseJson.error) {
                        errorMsg = responseJson.error;
                    }
                } catch (e) {
                    errorMsg = 'Server error: Failed to fetch data. Please try again later.';
                }
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            }
            showToast('Error: ' + errorMsg, 'error');
            console.error('DataTables Ajax Error:', { status: xhr.status, response: xhr.responseText, thrown });
        }
    },
    columns: [
        {
            data: null,
            orderable: false,
            searchable: false,
            visible: columnDefinitions[0].visible,
            render: function(data, type, row) {
                return `<div class="form-check form-check-md">
                    <input class="form-check-input" type="checkbox" value="${row.id}" />
                </div>`;
            }
        },
        { data: 'leave_type', name: 'leave_type', visible: columnDefinitions[1].visible },
        {
            data: 'employee',
            name: 'employee',
            orderable: false,
            searchable: false,
            visible: columnDefinitions[2].visible,
            render: function(data, type, row) {
                const name = data?.name || 'N/A';
                const department = data?.department || 'N/A';
                const image = data?.image;
                const initials = name.split(' ').map(word => word[0]).join('').substring(0, 2).toUpperCase();

                let avatarHTML = image && image !== 'null'
                    ? `<a href="javascript:void(0);" class="avatar avatar-md border avatar-rounded">
                            <img src="${image}" class="img-fluid" alt="${name}" />
                       </a>`
                    : `<span class="avatar avatar-md text-white rounded-circle letter-avatar" data-name="${name}">
                            ${initials}
                       </span>`;

                return `
                    <div class="d-flex align-items-center file-name-icon">
                        ${avatarHTML}
                        <div class="ms-2">
                            <h6 class="fw-medium mb-0"><a href="javascript:void(0);">${name}</a></h6>
                            <span class="fs-12 fw-normal">${department}</span>
                        </div>
                    </div>`;
            }
        },
        {
            data: 'approved_by_user.name',
            name: 'approved_by_user.name',
            visible: columnDefinitions[3].visible,
            render: function(data) {
                return data || 'N/A';
            }
        },
        {
            data: 'employee.employee_id',
            name: 'boxUser.employee_id',
            visible: columnDefinitions[4].visible,
            render: function(data) {
                return data || 'N/A';
            }
        },
        {
            data: 'employee.designation',
            name: 'boxUser.designationRelation.name',
            visible: columnDefinitions[5].visible,
            render: function(data) {
                return data || 'N/A';
            }
        },
        { data: 'from', name: 'from', visible: columnDefinitions[6].visible },
        { data: 'to', name: 'to', visible: columnDefinitions[7].visible },
        { data: 'no_of_days', name: 'no_of_days', visible: columnDefinitions[8].visible },
        {
            data: 'contact_during_leave',
            name: 'contact_during_leave',
            visible: columnDefinitions[9].visible,
            render: function(data) {
                return data || 'N/A';
            }
        },
        {
    data: 'status',
    name: 'status',
    visible: columnDefinitions[10].visible,
    render: function(data, type, row) {
        let statusClass = '';
        const currentStatus = data?.toLowerCase();
        switch (currentStatus) {
            case 'approved':
                statusClass = 'text-success';
                break;
            case 'declined':
            case 'rejected':
                statusClass = 'text-danger';
                break;
            case 'pending':
                statusClass = 'text-purple';
                break;
            default:
                statusClass = 'text-secondary';
        }
        const isPending = currentStatus === 'pending';
        const disabledClass = isPending ? '' : 'disabled text-muted';
        const tooltipAttr = isPending ? '' : 'title="Status can only be changed when it is Pending"';

        return `
            <div class="dropdown">
                <a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                    <span class="rounded-circle bg-transparent d-flex justify-content-center align-items-center me-2">
                        <i class="ti ti-point-filled ${statusClass}"></i>
                    </span> ${data}
                </a>
                <ul class="dropdown-menu dropdown-menu-end p-3">
                    <li>
                        <a href="javascript:void(0);" class="dropdown-item rounded-1 open-status-modal ${disabledClass}"
                           data-id="${row.id}" data-status="approved" ${tooltipAttr}>
                            <span class="rounded-circle bg-transparent-success me-2">
                                <i class="ti ti-point-filled text-success"></i>
                            </span> Approved
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="dropdown-item rounded-1 open-status-modal ${disabledClass}"
                           data-id="${row.id}" data-status="rejected" ${tooltipAttr}>
                            <span class="rounded-circle bg-transparent-danger me-2">
                                <i class="ti ti-point-filled text-danger"></i>
                            </span> Rejected
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="dropdown-item rounded-1 open-status-modal ${disabledClass}"
                           data-id="${row.id}" data-status="pending" ${tooltipAttr}>
                            <span class="rounded-circle bg-transparent-purple me-2">
                                <i class="ti ti-point-filled text-purple"></i>
                            </span> Pending
                        </a>
                    </li>
                </ul>
            </div>
        `;
    }
},
        {
            data: 'reason',
            name: 'reason',
            visible: columnDefinitions[11].visible,
            render: function(data) {
                return data ? `<span>${data}</span>` : `<span class="text-muted">N/A</span>`;
            }
        },
        {
            data: 'applied_for',
            name: 'applied_for',
            visible: columnDefinitions[12].visible,
            render: function(data) {
                return data ? `<span>${data}</span>` : `<span class="text-muted">N/A</span>`;
            }
        },
        {
            data: 'attachment',
            name: 'attachment',
            orderable: false,
            searchable: false,
            visible: columnDefinitions[13].visible,
            render: function(data, type, row) {
                if (!data || data === 'N/A' || data === null || data === '') {
                    return '<span class="text-muted">N/A</span>';
                }
                const attachmentUrl = `${data}`;
                return `
                    <div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary view-attachment" data-filename="${data}" data-url="${attachmentUrl}">View Attachment</a>
                    </div>
                `;
            }
        },
        {
            data: 'action',
            orderable: false,
            searchable: false,
            visible: columnDefinitions[14].visible,
            render: function(data, type, row) {
                const rowData = encodeURIComponent(JSON.stringify(row));
                const editBtn = `<a href="javascript:void(0);" class="btn btn-sm btn-primary me-2 edit-leave-btn" data-row="${rowData}">Edit</a>`;
                return `
                    <div class="text-end">
                        ${editBtn}
                        <a href="javascript:void(0);" class="btn btn-sm btn-danger delete-leave-request" data-id="${row.id}">Delete</a>
                    </div>`;
            }
        }
    ],
    order: [[1, 'desc']],
    drawCallback: function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
        assignAvatarColors();
    },
    searchDelay: 9999999
    });
}); // End of loadColumnPreferences callback

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
    if (leaveRequestsTable) {
        columnDefinitions.forEach(col => {
            try {
                leaveRequestsTable.column(col.index).visible(col.visible, false);
            } catch (e) {
                console.error('Error applying column visibility:', e);
            }
        });
        leaveRequestsTable.columns.adjust();
        // Redraw only once after all changes
        leaveRequestsTable.draw(false);
    }

    // Close modal
    $('#manageColumnsModal').modal('hide');
});

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}
$('#leaveRequestsTabledata_filter input').off();
$('#leaveRequestsTabledata_filter input').on('input', debounce(function() {
    leaveRequestsTable.search(this.value).draw();
}, 500));



$(document).on('click', '#leave-type-dropdown a', function() {
    const selectedId = $(this).data('id');
    const selectedText = $(this).text().trim();
    console.log("Selected Leave Type:", selectedText, "ID:", selectedId);
});

            $(document).on('click', '.open-status-modal', function() {
                const leaveId = $(this).data('id');
                const newStatus = $(this).data('status');
                $('#leaveRequestId').val(leaveId);
                $('#newStatus').val(newStatus);
                $('#statusReason').val('');
                const capitalizedStatus = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                $('#displayStatus').text(capitalizedStatus);
                if (newStatus === 'rejected') {
                    $('label[for="statusReason"]').html('Reason <span class="text-danger">*</span>');
                    $('#statusReason').prop('required', true);
                } else {
                    $('label[for="statusReason"]').text('Reason');
                    $('#statusReason').prop('required', false);
                }
                $('#statusUpdateModal').modal('show');
            });

            $('#statusUpdateForm').on('submit', function(e) {
                e.preventDefault();
                const leaveId = $('#leaveRequestId').val();
                const newStatus = $('#newStatus').val();
                const reason = $('#statusReason').val();
                $.ajax({
                    url: '{{ route('admin.leave.update.status', ['box_slug' => request()->route('box_slug')]) }}', // Update if different
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        leave_id: leaveId,
                        status: newStatus,
                        reason: reason
                    },
                    success: function(response) {
                        $('#statusUpdateModal').modal('hide');
                        $('#leaveRequestsTabledata').DataTable().ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        showToast(xhr.responseJSON?.message || 'Failed to update status.', 'error');
                    }
                });
            });
            let deleteLeaveId = null;

            $(document).on('click', '.delete-leave-request', function() {
                deleteLeaveId = $(this).data('id');
                $('#delete_modal').modal('show');
            });

            $(document).on('click', '.confirm-delete-leave', function(e) {
                e.preventDefault();
                if (!deleteLeaveId) return;

                $.ajax({
                    url: '{{ route('leave.request.delete', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: deleteLeaveId
                    },
                    success: function(response) {
                        $('#delete_modal').modal('hide');
                        $('#leaveRequestsTabledata').DataTable().ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        showToast(xhr.responseJSON?.message || 'Failed to delete leave request.', 'error');
                    }
                });
            });

    // Utility Functions from leaves.blade.php
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

    $('#edit_leave_type').on('change', function () {
        let leaveTypeId = $(this).val();
        fetchLeaveBalance(leaveTypeId, '#edit_leaves input[name="leave_balance"]');
    });

    $(document).on('click', '.edit-leave-btn', function() {
        const row = JSON.parse(decodeURIComponent($(this).data('row')));
        openEditModal(row);
    });

    window.openEditModal = function (row) {
        $('#edit_leaves').modal('show');
        loadLeaveTypes('edit_leave_type', row.leave_type_id);
        $('#edit_leaves input[name="leave_request_id"]').val(row.id);
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

    $('#edit_leaves').on('change', '#from_date, #to_date, #applied_for', function () {
        calculateTotalDays("#edit_leaves #from_date", "#edit_leaves #to_date", "#edit_leaves #applied_for", "#edit_leaves #total_days");
    });

    handleFilePreview('#edit_leaves input[name="attachment"]', '#edit_leaves #file-preview');

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
            success: function(res) {
                if (res.status || res.success) {
                    showToast(res.message || 'Leave request updated successfully!', 'success');
                    $('#edit_leaves').modal('hide');
                    form.trigger('reset');
                    $('#file-preview').empty();
                    leaveRequestsTable.ajax.reload(null, false);
                } else {
                    showToast(res.message || 'Failed to update leave request.', 'error');
                }
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Failed to update leave request.', 'error');
            }
        });
    });


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
        });
    </script>
@endpush
@endsection
