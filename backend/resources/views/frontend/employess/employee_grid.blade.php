@extends('layouts.main')
@section('content')
@section('title','Employee Grid')

<style>
 .letter-avatar {
    width: 55px;
    height: 55px;
    font-size: 18px;
    font-weight: bold;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-transform: uppercase;
    background-color: #00bcd4; /* default before JS assigns */
}




</style>
<!-- Page Wrapper -->
		 <div class="page-wrapper">
			 <div class="content">

				 <!-- Breadcrumb -->
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
								 <li class="breadcrumb-item active" aria-current="page">Employee Grid </li>
							 </ol>
						 </nav>
					 </div>
					 <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
						 <div class="me-2 mb-2">
							 <div class="d-flex align-items-center border bg-white rounded p-1 me-2 icon-list">
								 <a href="{{ box_route('manage_users.employee_list') }}" class="btn btn-icon btn-sm me-1"><i class="ti ti-list-tree"></i></a>
								 <a href="{{ box_route('employee.grid') }}" class="btn btn-icon btn-sm active bg-primary text-white"><i class="ti ti-layout-grid"></i></a>
							 </div>
						 </div>
						 <div class="me-2 mb-2">
							 <div class="dropdown">
								 <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
									 <i class="ti ti-file-export me-1"></i>Export
								 </a>
								 <ul class="dropdown-menu  dropdown-menu-end p-3">
									 <li>
										 <a href="javascript:void(0);" class="dropdown-item rounded-1"><i class="ti ti-file-type-pdf me-1"></i>Export as PDF </a>
									 </li>
									 <li>
										 <a href="javascript:void(0);" class="dropdown-item rounded-1"><i class="ti ti-file-type-xls me-1"></i>Export as Excel  </a>
									 </li>
								 </ul>
							 </div>
						 </div>
						 <div class="mb-2">
							 <a href="#" data-bs-toggle="modal" data-bs-target="#add_employee" class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add Employee </a>
						 </div>
						 <div class="head-icons ms-2">
							 <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
								 <i class="ti ti-chevrons-up"></i>
							 </a>
						 </div>
					 </div>
				 </div>
				 <!-- /Breadcrumb -->

				 <div class="row">

					 <!-- Total Plans -->
					 <div class="col-lg-3 col-md-6 d-flex">
						 <div class="card flex-fill">
							 <div class="card-body d-flex align-items-center justify-content-between">
								 <div class="d-flex align-items-center overflow-hidden">
									 <div>
										 <span class="avatar avatar-lg bg-dark rounded-circle"><i class="ti ti-users"></i></span>
									 </div>
									 <div class="ms-2 overflow-hidden">
										 <p class="fs-12 fw-medium mb-1 text-truncate">Total Employee </p>
										 <h4 id="recordsTotal">.</h4>
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
										 <span class="avatar avatar-lg bg-success rounded-circle"><i class="ti ti-user-share"></i></span>
									 </div>
									 <div class="ms-2 overflow-hidden">
										 <p class="fs-12 fw-medium mb-1 text-truncate">Active </p>
										 <h4 id="active_users" >.. </h4>
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
										 <span class="avatar avatar-lg bg-danger rounded-circle"><i class="ti ti-user-pause"></i></span>
									 </div>
									 <div class="ms-2 overflow-hidden">
										 <p class="fs-12 fw-medium mb-1 text-truncate">InActive </p>
										 <h4 id="inactive_users" >... </h4>
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
										 <span class="avatar avatar-lg bg-info rounded-circle"><i class="ti ti-user-plus"></i></span>
									 </div>
									 <div class="ms-2 overflow-hidden">
										 <p class="fs-12 fw-medium mb-1 text-truncate">New Joiners </p>
										 <h4 id="new_joinees">....</h4>
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
					 <div class="card-body p-3">
						 <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
							 <h5>Employees Grid </h5>
							 <div class="d-flex align-items-center flex-wrap row-gap-3">
								 <div class="dropdown me-3">
                                     <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                        Designation
                                     </a>
                                     <ul class="dropdown-menu  dropdown-menu-end p-3">
                                         <li>
                                             <a href="javascript:void(0);" class="dropdown-item rounded-1">Finance </a>
                                         </li>
                                         <li>
                                             <a href="javascript:void(0);" class="dropdown-item rounded-1">Developer </a>
                                         </li>
                                         <li>
                                             <a href="javascript:void(0);" class="dropdown-item rounded-1">Executive </a>
                                         </li>
                                     </ul>
                                 </div>
								 <div class="dropdown">
									 <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
										Sort By : Last 7 Days
									 </a>
									 <ul class="dropdown-menu  dropdown-menu-end p-3">
										 <li>
											 <a href="javascript:void(0);" class="dropdown-item rounded-1">Last 7 Days </a>
										 </li>
										 <li>
											 <a href="javascript:void(0);" class="dropdown-item rounded-1">Ascending </a>
										 </li>
									 </ul>
								 </div>
							 </div>
						 </div>
					 </div>
				 </div>

				 <!-- Clients Grid -->
				 <div class="row" id="employee-container"></div>

<div class="col-md-12">
    <div class="text-center mb-4">
        <button id="load-more" class="btn btn-primary">
            <i class="ti ti-loader-3 me-1"></i>Load More
        </button>
    </div>
</div>

				 <!-- /Clients Grid -->

			 </div>
			 
		 </div>
		 <!-- /Page Wrapper -->

		 <!-- Add Employee -->
		 <div class="modal fade" id="add_employee">
			 <div class="modal-dialog modal-dialog-centered modal-lg">
				 <div class="modal-content">
					 <div class="modal-header">
						 <div class="d-flex align-items-center">
							 <h4 class="modal-title me-2 form-title">Add New Employee </h4><span>Employee  ID : ___ -0024 </span>
						 </div>
						 <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							 <i class="ti ti-x"></i>
						 </button>
					 </div>
					 <form action="employees-grid.html">
						 <div class="contact-grids-tab">
							 <ul class="nav nav-underline" id="myTab" role="tablist">
								 <li class="nav-item" role="presentation">
								   <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#basic-info" type="button" role="tab" aria-selected="true">Basic Information </button>
								 </li>
								 <li class="nav-item" role="presentation">
								   <button class="nav-link" id="address-tab" data-bs-toggle="tab" data-bs-target="#address" type="button" role="tab" aria-selected="false">Permissions </button>
								 </li>
							 </ul>
						 </div>
						 <div class="tab-content" id="myTabContent">
							 <div class="tab-pane fade show active" id="basic-info" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
									 <div class="modal-body pb-0 ">
										 <div class="row">
											 <div class="col-md-12">
												 <div class="d-flex align-items-center flex-wrap row-gap-3 bg-light w-100 rounded p-3 mb-4">
													 <div class="d-flex align-items-center justify-content-center avatar avatar-xxl rounded-circle border border-dashed me-2 flex-shrink-0 text-dark frames">
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
																 <input type="file" class="form-control image-sign" multiple="" />
															 </div>
															 <a href="javascript:void(0);" class="btn btn-light btn-sm">Cancel </a>
														 </div>

													 </div>
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">First Name  <span class="text-danger"> * </span></label>
													 <input type="text" class="form-control" />
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Last Name </label>
													 <input type="email" class="form-control" />
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Employee ID  <span class="text-danger"> * </span></label>
													 <input type="text" class="form-control" />
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Joining Date  <span class="text-danger"> * </span></label>
													 <div class="input-icon-end position-relative">
														 <input type="text" class="form-control datetimepicker" placeholder="dd/mm/yyyy" />
														 <span class="input-icon-addon">
															 <i class="ti ti-calendar text-gray-7"></i>
														 </span>
													 </div>
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Username  <span class="text-danger"> * </span></label>
													 <input type="text" class="form-control" />
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Email  <span class="text-danger"> * </span></label>
													 <input type="email" class="form-control" />
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3 ">
													 <label class="form-label">Password  <span class="text-danger"> * </span></label>
													 <div class="pass-group">
														 <input type="password" class="pass-input form-control" />
														 <span class="ti toggle-password ti-eye-off"></span>
													 </div>
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3 ">
													 <label class="form-label">Confirm Password  <span class="text-danger"> * </span></label>
													 <div class="pass-group">
														 <input type="password" class="pass-inputs form-control" />
														 <span class="ti toggle-passwords ti-eye-off"></span>
													 </div>
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Phone Number  <span class="text-danger"> * </span></label>
													 <input type="text" class="form-control" />
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Company <span class="text-danger"> * </span></label>
													 <input type="text" class="form-control" />
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Department </label>
													 <select class="select" name="department" id="department">
														 <option value="">Select Department</option>
													 </select>
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Designation </label>
													 <select class="select" name="designation" id="designation">
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
											 <div class="col-md-12">
												 <div class="mb-3">
													 <label class="form-label">About  <span class="text-danger"> * </span></label>
													 <textarea class="form-control" rows="3"></textarea>
												 </div>
											 </div>
										 </div>
									 </div>
									 <div class="modal-footer">
										 <button type="button" class="btn btn-outline-light border me-2" data-bs-dismiss="modal">Cancel </button>
										 <button type="submit" class="btn btn-primary">Save  </button>
									 </div>
							 </div>
							 <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab" tabindex="0">
								 <div class="modal-body">
									 <div class="card bg-light-500 shadow-none">
										 <div class="card-body d-flex align-items-center justify-content-between flex-wrap row-gap-3">
											 <h6>Enable Options </h6>
											 <div class="d-flex align-items-center justify-content-end">
												 <div class="form-check form-switch me-2">
													 <label class="form-check-label mt-0">
													 <input class="form-check-input me-2" type="checkbox" role="switch" />
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
															 <input class="form-check-input me-2" type="checkbox" role="switch" checked="" />
																Holidays
															 </label>
														 </div>
													 </td>
													 <td>
														 <div class="form-check d-flex align-items-center">
															 <label class="form-check-label mt-0">
																 <input class="form-check-input" type="checkbox" checked="" />
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
																 <input class="form-check-input" type="checkbox" checked="" />
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
															 <input class="form-check-input me-2" type="checkbox" role="switch" />
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
															 <input class="form-check-input me-2" type="checkbox" role="switch" />
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
															 <input class="form-check-input me-2" type="checkbox" role="switch" />
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
															 <input class="form-check-input me-2" type="checkbox" role="switch" />
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
															 <input class="form-check-input me-2" type="checkbox" role="switch" />
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
															 <input class="form-check-input me-2" type="checkbox" role="switch" checked="" />
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
																 <input class="form-check-input" type="checkbox" checked="" />
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
																 <input class="form-check-input" type="checkbox" checked="" />
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
															 <input class="form-check-input me-2" type="checkbox" role="switch" />
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
									 <button type="button" class="btn btn-outline-light border me-2" data-bs-dismiss="modal">Cancel </button>
									 <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#success_modal">Save  </button>
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
							 <h4 class="modal-title me-2">Edit Employee </h4><span>Employee  ID : ___ -0024 </span>
						 </div>
						 <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							 <i class="ti ti-x"></i>
						 </button>
					 </div>
					 <form action="employees-grid.html">
						 <div class="contact-grids-tab">
							 <ul class="nav nav-underline" id="myTab2" role="tablist">
								 <li class="nav-item" role="presentation">
								   <button class="nav-link active" id="info-tab2" data-bs-toggle="tab" data-bs-target="#basic-info2" type="button" role="tab" aria-selected="true">Basic Information </button>
								 </li>
								 <li class="nav-item" role="presentation">
								   <button class="nav-link" id="address-tab2" data-bs-toggle="tab" data-bs-target="#address2" type="button" role="tab" aria-selected="false">Permissions </button>
								 </li>
							 </ul>
						 </div>
						 <div class="tab-content" id="myTabContent2">
							 <div class="tab-pane fade show active" id="basic-info2" role="tabpanel" aria-labelledby="info-tab2" tabindex="0">
									 <div class="modal-body pb-0 ">
										 <div class="row">
											 <div class="col-md-12">
												 <div class="d-flex align-items-center flex-wrap row-gap-3 bg-light w-100 rounded p-3 mb-4">
													 <div class="d-flex align-items-center justify-content-center avatar avatar-xxl rounded-circle border border-dashed me-2 flex-shrink-0 text-dark frames">
														 <img src="{{ asset('assets/img/users/user-13.jpg') }}" alt="img" class="rounded-circle" />
													 </div>
													 <div class="profile-upload">
														 <div class="mb-2">
															 <h6 class="mb-1">Upload Profile Image </h6>
															 <p class="fs-12">Image should be below 4 mb </p>
														 </div>
														 <div class="profile-uploader d-flex align-items-center">
															 <div class="drag-upload-btn btn btn-sm btn-primary me-2">
																Upload
																 <input type="file" class="form-control image-sign" multiple="" />
															 </div>
															 <a href="javascript:void(0);" class="btn btn-light btn-sm">Cancel </a>
														 </div>

													 </div>
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">First Name  <span class="text-danger"> * </span></label>
													 <input type="text" class="form-control" value="Anthony" />
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Last Name </label>
													 <input type="email" class="form-control" value="Lewis" />
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Employee ID  <span class="text-danger"> * </span></label>
													 <input type="text" class="form-control" value="Emp-001" />
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Joining Date  <span class="text-danger"> * </span></label>
													 <div class="input-icon-end position-relative">
														 <input type="text" class="form-control datetimepicker" placeholder="dd/mm/yyyy" value="17-10-2022" />
														 <span class="input-icon-addon">
															 <i class="ti ti-calendar text-gray-7"></i>
														 </span>
													 </div>
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Username  <span class="text-danger"> * </span></label>
													 <input type="text" class="form-control" value="Anthony" />
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Email  <span class="text-danger"> * </span></label>
													 <input type="email" class="form-control" value="anthony@example.com	" />
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3 ">
													 <label class="form-label">Password  <span class="text-danger"> * </span></label>
													 <div class="pass-group">
														 <input type="password" class="pass-input form-control" />
														 <span class="ti toggle-password ti-eye-off"></span>
													 </div>
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3 ">
													 <label class="form-label">Confirm Password  <span class="text-danger"> * </span></label>
													 <div class="pass-group">
														 <input type="password" class="pass-inputs form-control" />
														 <span class="ti toggle-passwords ti-eye-off"></span>
													 </div>
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Phone Number  <span class="text-danger"> * </span></label>
													 <input type="text" class="form-control" value="(123) 4567 890" />
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Company <span class="text-danger"> * </span></label>
													 <input type="text" class="form-control" value="Abac Company" />
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Department </label>
													 <select class="select" name="department" id="departmentedit">
														 <option value="">Select Department</option>
													 </select>
												 </div>
											 </div>
											 <div class="col-md-6">
												 <div class="mb-3">
													 <label class="form-label">Designation </label>
													 <select class="select" name="designation" id="designationedit">
														 <option value="">Select Designation</option>
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
											 <div class="col-md-12">
												 <div class="mb-3">
													 <label class="form-label">About  <span class="text-danger"> * </span></label>
													 <textarea class="form-control" rows="3"></textarea>
												 </div>
											 </div>
										 </div>
									 </div>
									 <div class="modal-footer">
										 <button type="button" class="btn btn-outline-light border me-2" data-bs-dismiss="modal">Cancel </button>
										 <button type="submit" class="btn btn-primary">Save  </button>
									 </div>
							 </div>
							 <div class="tab-pane fade" id="address2" role="tabpanel" aria-labelledby="address-tab2" tabindex="0">
								 <div class="modal-body">
									 <div class="card bg-light-500 shadow-none">
										 <div class="card-body d-flex align-items-center justify-content-between flex-wrap row-gap-3">
											 <h6>Enable Options </h6>
											 <div class="d-flex align-items-center justify-content-end">
												 <div class="form-check form-switch me-2">
													 <label class="form-check-label mt-0">
													 <input class="form-check-input me-2" type="checkbox" role="switch" />
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
															 <input class="form-check-input me-2" type="checkbox" role="switch" checked="" />
																Holidays
															 </label>
														 </div>
													 </td>
													 <td>
														 <div class="form-check d-flex align-items-center">
															 <label class="form-check-label mt-0">
																 <input class="form-check-input" type="checkbox" checked="" />
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
																 <input class="form-check-input" type="checkbox" checked="" />
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
															 <input class="form-check-input me-2" type="checkbox" role="switch" />
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
															 <input class="form-check-input me-2" type="checkbox" role="switch" />
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
															 <input class="form-check-input me-2" type="checkbox" role="switch" />
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
															 <input class="form-check-input me-2" type="checkbox" role="switch" />
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
															 <input class="form-check-input me-2" type="checkbox" role="switch" />
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
															 <input class="form-check-input me-2" type="checkbox" role="switch" checked="" />
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
																 <input class="form-check-input" type="checkbox" checked="" />
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
																 <input class="form-check-input" type="checkbox" checked="" />
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
															 <input class="form-check-input me-2" type="checkbox" role="switch" />
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
									 <button type="button" class="btn btn-outline-light border me-2" data-bs-dismiss="modal">Cancel </button>
									 <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#success_modal">Save  </button>
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
							 <span class="avatar avatar-lg avatar-rounded bg-success mb-3"><i class="ti ti-check fs-24"></i></span>
							 <h5 class="mb-2">Employee Added Successfully </h5>
							 <p class="mb-3">Stephan Peralt has been _____ with Client ID :  <span class="text-primary">#EMP - 0001 </span>
							 </p>
							 <div>
								 <div class="row g-2">
									 <div class="col-6">
										 <a href="employees.html" class="btn btn-dark w-100">Back to List </a>
									 </div>
									 <div class="col-6">
										 <a href="employee-details.html" class="btn btn-primary w-100">Detail Page </a>
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
		 <div class="modal fade" id="delete_modal">
			 <div class="modal-dialog modal-dialog-centered modal-sm">
				 <div class="modal-content">
					 <div class="modal-body text-center">
						 <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
							 <i class="ti ti-trash-x fs-36"></i>
						 </span>
						 <h4 class="mb-1">Confirm Delete </h4>
						 <p class="mb-3">You want to delete ___ the marked items, this ____ be undone once you ______. </p>
						 <div class="d-flex justify-content-center">
							 <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel </a>
							 <a href="employees-grid.html" class="btn btn-danger">Yes, Delete </a>
						 </div>
					 </div>
				 </div>
			 </div>
		 </div>
		 <!-- /Delete Modal -->


         @push('scripts')
<script>
let page = 1;
let loading = false;

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

function loadEmployees(page) {
    if (loading) return;
    loading = true;

    $.ajax({
        url: `{{ route('gridfetch.employee', ['box_slug' => request()->route('box_slug')]) }}?page=${page}`,
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            const employees = response.data;
            const nextPage = response.next_page;
            const summarystats = response.summary;


            if (summarystats) {
                $('#recordsTotal').text(summarystats.recordsTotal);
                $('#active_users').text(summarystats.active_users);
                $('#inactive_users').text(summarystats.inactive_users);
                $('#new_joinees').text(summarystats.new_joinees);
            }
            if (!employees || employees.length === 0) {
                $('#load-more').hide();
                return;
            }
employees.forEach(employee => {
    const projects = 8;
    const done = 5;
    const progress = 3;
    const productivity = Math.floor(Math.random() * 100);
    const productivityColor = productivity >= 60 ? 'purple' : productivity >= 30 ? 'warning' : 'danger';
    const initial = employee.name ? employee.name.charAt(0).toUpperCase() : '?';

    const hasImage = employee.profile_image_url && employee.profile_image_url !== '';

    const imageHtml = hasImage
        ? `<img src="${employee.profile_image_url}" class="img-fluid h-auto w-auto rounded-circle" alt="img" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');" />`
        : '';

    const fallbackAvatar = `
        <div class="letter-avatar ${hasImage ? 'd-none' : 'd-flex'}" data-name="${employee.name}">
            ${initial}
        </div>
    `;
    const isEnabled = employee.status === 'Enabled';
    const dotColor = isEnabled ? 'green' : 'red';
    const avatarClass = `avatar-status-${Math.random().toString(36).substr(2, 9)}`;
    const internalStyle = `
        <style>
            .${avatarClass} {
                position: relative;
            }
            .${avatarClass}::after {
                content: '';
                position: absolute;
                bottom: 2px;
                right: 2px;
                width: 12px;
                height: 12px;
                background-color: ${dotColor};
                border: 2px solid white;
                border-radius: 50%;
                z-index: 10;
            }
        </style>
    `;
const employeeDetailsRoute =
                    '{{ route('employee.details', ['box_slug' => request()->route('box_slug')]) }}';

    const card = `
        ${internalStyle}
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="form-check form-check-md">
                            <input class="form-check-input" type="checkbox" />
                        </div>
                        <div>
                            <a href="${employeeDetailsRoute}?emp_id=${employee.id}"
                               class="avatar avatar-xl avatar-rounded ${avatarClass} border p-1 border-primary rounded-circle"
                               style="width: 64px; height: 64px; display: inline-block; overflow: hidden;">
                                ${imageHtml}
                                ${fallbackAvatar}
                            </a>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-icon btn-sm rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end p-3">
                                <li>
                                    <a class="dropdown-item rounded-1" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit_employee">
                                        <i class="ti ti-edit me-1"></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item rounded-1" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_modal">
                                        <i class="ti ti-trash me-1"></i>Delete
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="text-center mb-3">
                        <h6 class="mb-1"><a href="${employeeDetailsRoute}?emp_id=${employee.id}">${employee.name}</a></h6>
                        <span class="badge bg-pink-transparent fs-10 fw-medium">${employee.staff_role === 'admin' ? 'Owner' : (employee.department_name || '')}</span>
                    </div>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="mb-3">
                                <span class="fs-12">Projects</span>
                                <h6 class="fw-medium">${projects}</h6>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <span class="fs-12">Done</span>
                                <h6 class="fw-medium">${done}</h6>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <span class="fs-12">Progress</span>
                                <h6 class="fw-medium">${progress}</h6>
                            </div>
                        </div>
                    </div>
                    <p class="mb-2 text-center">Productivity: <span class="text-${productivityColor}">${productivity}%</span></p>
                    <div class="progress progress-xs mb-2">
                        <div class="progress-bar bg-${productivityColor}" role="progressbar" style="width: ${productivity}%"></div>
                    </div>
                </div>
            </div>
        </div>`;

    $('#employee-container').append(card);
});

            assignAvatarColors(); // Apply dynamic colors
            if (!nextPage) {
                $('#load-more').hide();
            } else {
                page = nextPage;
            }

            loading = false;
        },
        error: function (error) {
            console.error('Error fetching employees:', error);
            loading = false;
        }
    });
}
$(document).ready(function () {
    loadEmployees(page);

    $('#load-more').on('click', function () {
        loadEmployees(page);
    });

    // Hierarchical Reporting Manager logic
    let departmentOptions = {};
    let teamOptions = {};

    function fetchDepartmentsAndTeams(callback = () => {}) {
        $.ajax({
            url: '{{ route('department.manage', ['box_slug' => request()->route('box_slug')]) }}',
            method: 'GET',
            success: function(response) {
                if (response.status === 200) {
                    const departments = response.data;
                    departmentOptions = {};
                    teamOptions = {};
                    $('#department, #departmentedit').empty().append('<option value="">Select Department</option>');
                    departments.forEach(dept => {
                        departmentOptions[dept.id] = dept.name;
                        teamOptions[dept.id] = dept.all_teams || [];
                        $('#department, #departmentedit').append(`<option value="${dept.id}">${dept.name}</option>`);
                    });
                    callback();
                }
            }
        });
    }

    function populateTeams(departmentId, $select, selectedTeamId = '') {
        $select.empty().append('<option value="">Select Designation</option>');
        const teams = teamOptions[departmentId] || [];
        teams.forEach(team => {
            const selected = team.id == selectedTeamId ? 'selected' : '';
            $select.append(`<option value="${team.id}" ${selected}>${team.name}</option>`);
        });
    }

    fetchDepartmentsAndTeams();

    $('#department').on('change', function() {
        const deptId = $(this).val();
        populateTeams(deptId, $('#designation'));
        populateReportingManagerSelect($('#reporting_manager_add'), deptId || null, null, null);
    });

    $('#designation').on('change', function() {
        const deptId = $('#department').val();
        const desigId = $(this).val();
        populateReportingManagerSelect($('#reporting_manager_add'), deptId || null, desigId || null, null);
    });

    $('#departmentedit').on('change', function() {
        const deptId = $(this).val();
        populateTeams(deptId, $('#designationedit'));
        const currentSelected = $('#reporting_manager_edit').val();
        populateReportingManagerSelect($('#reporting_manager_edit'), deptId || null, null, currentSelected);
    });

    $('#designationedit').on('change', function() {
        const deptId = $('#departmentedit').val();
        const desigId = $(this).val();
        const currentSelected = $('#reporting_manager_edit').val();
        populateReportingManagerSelect($('#reporting_manager_edit'), deptId || null, desigId || null, currentSelected);
    });

    function populateReportingManagerSelect($select, filterDept = null, filterDesignation = null, selectedId = null) {
        $select.prop('disabled', true).empty().append('<option value="">Loading...</option>');
        const currentEditingEmpId = $('#edit_employee_id').val();
        $.ajax({
            url: '{{ route('employee.get.box.users', ['box_slug' => request()->route('box_slug')]) }}',
            method: 'GET',
            data: {
                department: filterDept,
                superior_to_designation: filterDesignation,
                exclude_id: currentEditingEmpId || null
            },
            success: function(response) {
                $select.prop('disabled', false);
                if (response.status && Array.isArray(response.data)) {
                    let selectedDepth = null;
                    if (filterDesignation && teamOptions[filterDept]) {
                        const found = teamOptions[filterDept].find(t => String(t.id) === String(filterDesignation));
                        if (found) {
                            selectedDepth = (found.depth !== undefined && found.depth !== null) ? parseInt(found.depth) : null;
                        }
                    }
                    renderReportingManagerOptions($select, response.data, selectedId, filterDesignation, selectedDepth);
                } else {
                    $select.empty().append('<option value="">No superiors found</option>');
                }
            }
        });
    }

    function renderReportingManagerOptions($select, users, selectedId = null, activeFilterDesignation = null, selectedDepth = null) {
        $select.empty().append('<option value="">Select Reporting Manager</option>');
        let hierarchyRec = [];
        let others = [];
        const currentEditingEmpId = $('#edit_employee_id').val();
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
            const $hGroup = $('<optgroup label="Direct Hierarchy"></optgroup>');
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
            let bgColor = highlightType === "immediate" ? "#e8f5e9" : highlightType === "admin" ? "#f3e5f5" : "#fff3e0";
            let borderColor = highlightType === "immediate" ? "#4caf50" : highlightType === "admin" ? "#9c27b0" : "#ff9800";
            let badgeText = highlightType === "immediate" ? "Immediate Superior" : highlightType === "admin" ? "Administrator" : "Hierarchy Parent";
            let badgeClass = highlightType === "immediate" ? "bg-success" : highlightType === "admin" ? "bg-secondary" : "bg-warning";
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
@endpush

@endsection
