@extends('layouts.main')
@section('title', 'Projects')
@section('description', 'Manage and track your ongoing projects.')
@section('content')
<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-4">
									<h4 class="page-title">Projects<span class="count-title">{{ $totalProjects ?? 0 }}</span></h4>
								</div>
								<div class="col-8 text-end">
									<div class="head-icons">
										<a href="javascript:void(0);" onclick="$('#projects-datatable').DataTable().ajax.reload(null, false);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
											<i class="ti ti-refresh-dot"></i>
										</a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
											<i class="ti ti-chevrons-up"></i>
										</a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

						<div class="card">
							<div class="card-header">
								<!-- Search -->

								<div class="row align-items-center">
									<div class="col-sm-4">
										<div class="icon-form mb-3 mb-sm-0">
											<span class="form-icon"><i class="ti ti-search"></i></span>
											<input type="text" class="form-control" placeholder="Search Projects">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
											<div class="dropdown me-2">
												<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
												<div class="dropdown-menu  dropdown-menu-end">
													<ul>
														<li>
															<a href="{{ box_route('manage_users.projects.export.pdf') }}" class="dropdown-item"><i class="ti ti-file-type-pdf text-danger me-1"></i>Export
																as PDF</a>
														</li>
														<li>
															<a href="{{ box_route('manage_users.projects.export.excel') }}" class="dropdown-item"><i class="ti ti-file-type-xls text-green me-1"></i>Export
																as Excel </a>
														</li>
													</ul>
												</div>
											</div>
											<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-square-rounded-plus me-2"></i>Add New Project</a>
										</div>
									</div>
								</div>
								<!-- /Search -->
							</div>

							<div class="card-body">
								<!-- Filter -->
								<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<div class="dropdown me-2">
											<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" id="sortDropdownToggle"><i class="ti ti-sort-ascending-2 me-2"></i><span>Sort</span>
											</a>
											<div class="dropdown-menu  dropdown-menu-start">
												<ul>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="asc">
															<i class="ti ti-circle-chevron-right me-1"></i>Ascending
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="desc">
															<i class="ti ti-circle-chevron-right me-1"></i>Descending
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="recent">
															<i class="ti ti-circle-chevron-right me-1"></i>Recently Viewed
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="added">
															<i class="ti ti-circle-chevron-right me-1"></i>Recently Added
														</a>
													</li>
												</ul>
											</div>
										</div>
										<div class="icon-form">
											<span class="form-icon"><i class="ti ti-calendar"></i></span>
											<input type="text" class="form-control bookingrange" placeholder="">
										</div>

									</div>
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<div class="dropdown me-2">
											<a href="javascript:void(0);" class="btn bg-soft-purple text-purple" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-columns-3 me-2"></i>Manage Columns</a>
											<div class="dropdown-menu  dropdown-menu-md-end dropdown-md p-3">
												<h4 class="mb-2 fw-semibold">Want to manage datatables?</h4>
												<p class="mb-3">Please drag and drop your column to reorder your table
													and enable see option as you want.</p>
													<div class="border-top pt-3">
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Name</p>
															<div class="status-toggle">
																<input type="checkbox" id="col_1" class="check column-toggle" data-column-index="1" checked>
																<label for="col_1" class="checktoggle"></label>
															</div>
														</div>
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Client</p>
															<div class="status-toggle">
																<input type="checkbox" id="col_2" class="check column-toggle" data-column-index="2" checked>
																<label for="col_2" class="checktoggle"></label>
															</div>
														</div>
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Priority</p>
															<div class="status-toggle">
																<input type="checkbox" id="col_3" class="check column-toggle" data-column-index="3" checked>
																<label for="col_3" class="checktoggle"></label>
															</div>
														</div>
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Start Date</p>
															<div class="status-toggle">
																<input type="checkbox" id="col_4" class="check column-toggle datePicker" data-column-index="4" checked>
																<label for="col_4" class="checktoggle"></label>
															</div>
														</div>
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Due Date</p>
															<div class="status-toggle">
																<input type="checkbox" id="col_5" class="check column-toggle datePicker" data-column-index="5" checked>
																<label for="col_5" class="checktoggle"></label>
															</div>
														</div>
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Type</p>
															<div class="status-toggle">
																<input type="checkbox" id="col_6" class="check column-toggle" data-column-index="6" checked>
																<label for="col_6" class="checktoggle"></label>
															</div>
														</div>
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Status</p>
															<div class="status-toggle">
																<input type="checkbox" id="col_7" class="check column-toggle" data-column-index="7" checked>
																<label for="col_7" class="checktoggle"></label>
															</div>
														</div>
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Team Members</p>
															<div class="status-toggle">
																<input type="checkbox" id="col_8" class="check column-toggle" data-column-index="8" checked>
																<label for="col_8" class="checktoggle"></label>
															</div>
														</div>
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Action</p>
															<div class="status-toggle">
																<input type="checkbox" id="col_9" class="check column-toggle" data-column-index="9" checked>
																<label for="col_9" class="checktoggle"></label>
															</div>
														</div>
													</div>
											</div>
										</div>
										<div class="view-icons">
											<a href="projects.html" class="active"><i class="ti ti-list-tree"></i></a>
											<a href="project-grid.html"><i class="ti ti-grid-dots"></i></a>
										</div>
									</div>
								</div>
								<!-- /Filter -->

								<!-- Projects List -->
								<div class="table-responsive custom-table">
									<table class="table" id="projects-datatable">
										<thead class="thead-light">
											<tr>
												<th>Sr. No.</th>
												<th>Name</th>
												<th>Client</th>
												<th>Priority</th>
												<th>Start Date</th>
												<th>End Date</th>
												<th>Type</th>
												<th>Status</th>
												<th>Team Members</th>
												<th class="text-end">Action</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
								<div class="row align-items-center">
									<div class="col-md-6">
										<div class="datatable-length"></div>
									</div>
									<div class="col-md-6">
										<div class="datatable-paginate"></div>
									</div>
								</div>
								<!-- /Projects List -->

							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Add New Project -->
		<div class="offcanvas offcanvas-end offcanvas-large" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h4>Add New Project</h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form id="addProjectForm" onsubmit="return false;">
					@csrf
					<div>
						<div class="row">
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Name <span class="text-danger">*</span></label>
									<input type="text" name="name" class="form-control">
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Project ID<span class="text-danger"> *</span></label>
									<input class="form-control" name="project_custom_id" type="text">
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Project Type <span class="text-danger">*</span></label>
									<select class="select2" name="project_type_id"><option value="">Choose</option></select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Client <span class="text-danger">*</span></label>
									<select class="select2" name="client_name"><option value="">Select</option></select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Category <span class="text-danger">*</span></label>
									<select class="select2" name="project_category_id">
										<option value="">Select</option>
									</select>
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Project Timing <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="project_timing" placeholder="e.g. 1 Month, 2 Weeks, Hourly">
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Price <span class="text-danger">*</span></label>
									<input class="form-control" type="text" step="0.01" name="price">
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Amount <span class="text-danger">*</span></label>
									<input class="form-control" type="text" step="0.01" name="amount">
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Total <span class="text-danger">*</span></label>
									<input class="form-control" type="text" step="0.01" name="total">
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Responsible Persons <span class="text-danger">*</span></label>
									<select class="multiple-img" multiple="multiple" name="responsible_persons[]"></select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Team Leader <span class="text-danger">*</span></label>
									<select class="multiple-img" multiple="multiple" name="team_leaders[]"></select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Start Date <span class="text-danger">*</span></label>
									<div class="icon-form">
										<span class="form-icon"><i class="ti ti-calendar-event"></i></span>
										<input type="text" class="form-control datePicker" name="start_date" placeholder="dd/mm/yyyy">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Due Date <span class="text-danger">*</span></label>
									<div class="icon-form">
										<span class="form-icon"><i class="ti ti-calendar-event"></i></span>
										<input type="text" class="form-control datePicker" name="due_date" placeholder="dd/mm/yyyy">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Priority</label>
									<select class="select" name="priority">
										<option>Select</option>
										<option>High</option>
										<option>Low</option>
										<option>Medium</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Status</label>
									<select class="select" name="status">
										<option>Select</option>
										<option>Active</option>
										<option>Inactive</option>
									</select>
								</div>
							</div>
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Description <span class="text-danger">*</span></label>
									<textarea class="form-control" name="description" rows="4"></textarea>
								</div>
							</div>
						</div>
												<div class="col-md-12 mb-3">
								<div class="d-flex justify-content-between align-items-center mb-2">
									<h5 class="mb-0">Project Contacts</h5>
									<button type="button" class="btn btn-sm btn-primary add-contact-btn"><i class="ti ti-plus"></i> Add</button>
								</div>
								<div class="table-responsive">
									<table class="table table-bordered table-sm contacts-table" id="addContactsTable">
										<thead class="thead-light">
											<tr>
												<th>Name <span class="text-danger">*</span></th>
												<th>Email</th>
												<th>Phone</th>
												<th>Designation</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td><input type="text" name="contacts[0][name]" class="form-control form-control-sm" required></td>
												<td><input type="email" name="contacts[0][email]" class="form-control form-control-sm"></td>
												<td><input type="text" name="contacts[0][phone]" class="form-control form-control-sm"></td>
												<td><input type="text" name="contacts[0][designation]" class="form-control form-control-sm"></td>
												<td><button type="button" class="btn btn-sm btn-danger remove-contact-btn"><i class="ti ti-trash"></i></button></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="d-flex align-items-center justify-content-end">
							<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
							<button type="submit" class="btn btn-primary">Create</button>
					</div>
				</form>
			</div>
		</div>
		<!-- /Add New Project -->

		<!-- Edit Project -->
		<div class="offcanvas offcanvas-end offcanvas-large" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom">
				<h4>Edit Project</h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form id="editProjectForm" onsubmit="return false;">
					@csrf
					<input type="hidden" name="id" id="edit_id">
					<div>
						<div class="row">
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Name <span class="text-danger">*</span></label>
									<input type="text" name="name" class="form-control">
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Project ID<span class="text-danger"> *</span></label>
									<input class="form-control" name="project_custom_id" type="text">
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Project Type <span class="text-danger">*</span></label>
									<select class="select2" name="project_type_id" >
										<option>Choose</option>
										@if(isset($projectTypes))
											@foreach($projectTypes as $type)
												<option value="{{ $type->id }}">{{ $type->name }}</option>
											@endforeach
										@endif
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Client <span class="text-danger">*</span></label>
									<select class="select2" name="client_name"><option value="">Select</option></select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Category <span class="text-danger">*</span></label>
									<select class="select2" name="project_category_id" id="edit_category">
										<option value="">Select</option>
									</select>
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Project Timing <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="project_timing" placeholder="e.g. 1 Month, 2 Weeks, Hourly">
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Price <span class="text-danger">*</span></label>
									<input class="form-control" type="number" step="0.01" name="price">
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Amount <span class="text-danger">*</span></label>
									<input class="form-control" type="number" step="0.01" name="amount">
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Total <span class="text-danger">*</span></label>
									<input class="form-control" type="number" step="0.01" name="total">
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Responsible Persons <span class="text-danger">*</span></label>
									<select class="multiple-img" multiple="multiple" name="responsible_persons[]"></select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Team Leader <span class="text-danger">*</span></label>
									<select class="multiple-img" multiple="multiple" name="team_leaders[]"></select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Start Date <span class="text-danger">*</span></label>
									<div class="icon-form">
										<span class="form-icon"><i class="ti ti-calendar-event"></i></span>
										<input type="text" class="form-control datetimepicker" name="start_date" placeholder="dd/mm/yyyy">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Due Date <span class="text-danger">*</span></label>
									<div class="icon-form">
										<span class="form-icon"><i class="ti ti-calendar-event"></i></span>
										<input type="text" class="form-control datetimepicker" name="due_date" placeholder="dd/mm/yyyy">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Priority</label>
									<select class="select" name="priority">
										<option>Select</option>
										<option selected="">High</option>
										<option>Low</option>
										<option>Medium</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Status</label>
									<select class="select" name="status">
										<option>Select</option>
										<option selected="">Active</option>
										<option>Inactive</option>
									</select>
								</div>
							</div>
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Description <span class="text-danger">*</span></label>
									<textarea class="form-control" name="description" rows="4"></textarea>
								</div>
							</div>
						</div>
												<div class="col-md-12 mb-3">
								<div class="d-flex justify-content-between align-items-center mb-2">
									<h5 class="mb-0">Project Contacts</h5>
									<button type="button" class="btn btn-sm btn-primary add-contact-btn-edit"><i class="ti ti-plus"></i> Add</button>
								</div>
								<div class="table-responsive">
									<table class="table table-bordered table-sm contacts-table" id="editContactsTable">
										<thead class="thead-light">
											<tr>
												<th>Name <span class="text-danger">*</span></th>
												<th>Email</th>
												<th>Phone</th>
												<th>Designation</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<!-- Dynamic rows will be appended here via JS -->
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="d-flex align-items-center justify-content-end">
							<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
							<button type="submit" class="btn btn-primary">Save Changes</button>
					</div>
				</form>
			</div>
		</div>
		<!-- /Edit Project -->

		<!-- Delete Project -->
		<div class="modal fade" id="delete_project" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Project?</h4>
							<p class="mb-0">Are you sure you want to remove <br> project you selected.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="javascript:void(0);" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<button type="button" id="confirm_delete_project_btn" class="btn btn-danger">Yes, Delete it</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Project -->

		<!-- Create Project -->
		<div class="modal custom-modal fade" id="create_project" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header border-0 m-0 justify-content-end">
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div class="success-message text-center">
							<div class="success-popup-icon bg-light-blue">
								<i class="ti ti-atom-2"></i>
							</div>
							<h3>Project Created Successfully!!!</h3>
							<p>View the details of project, created</p>
							<div class="col-lg-12 text-center modal-btn">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<a href="project-details.html" class="btn btn-primary">View Details</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Create Project -->

		<!-- Add New View -->
		<div class="modal custom-modal fade" id="save_view" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add New View</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="projects.html">
							<div class="mb-3">
								<label class="col-form-label">View Name</label>
								<input type="text" class="form-control">
							</div>
							<div class="modal-btn text-end">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-danger">Save</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Add New View -->

	</div>

@endsection
@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    $(document).ready(function() {

        // Fix Select2 search focus inside Offcanvas
        $('#offcanvas_add .select2').select2({ dropdownParent: $('#offcanvas_add') });
        $('#offcanvas_edit .select2').select2({ dropdownParent: $('#offcanvas_edit') });

        $.get("{{ box_route('manage_users.projects.options.project_types') }}", function(res) {
            if(res.success) {
                let options = '<option value="">Choose</option>';
                res.data.forEach(function(item) {
                    options += '<option value="'+item.id+'">'+item.name+'</option>';
                });
                $('select[name="project_type_id"]').html(options).trigger('change');
            }
        });

        $.get("{{ box_route('manage_users.projects.options.project_categories') }}", function(res) {
            if(res.success) {
                let options = '<option value="">Select</option>';
                res.data.forEach(function(item) {
                    options += '<option value="'+item.id+'">'+item.name+'</option>';
                });
                $('select[name="project_category_id"]').html(options).trigger('change');
            }
        });

        $.get("{{ box_route('manage_users.projects.options.leads') }}", function(res) {
            if(res.success) {
                let options = '<option value="">Select</option>';
                res.data.forEach(function(item) {
                    let name = (item.name || '') + ' ' + (item.last_name || '');
                    options += '<option value="'+name.trim()+'">'+name.trim()+'</option>';
                });
                $('select[name="client_name"]').html(options).trigger('change');
            }
        });

        $.get("{{ box_route('manage_users.projects.options.users') }}", function(res) {
            if(res.success) {
                let options = '';
                res.data.forEach(function(item) {
                    let name = item.name || '';
                    let img = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name);
                    options += '<option value="'+item.id+'" data-image="'+img+'">'+name.trim()+'</option>';
                });
                
                function formatState (state) {
                    if (!state.id) { return state.text; }
                    var $state = $(
                        '<span><img src="' + $(state.element).attr('data-image') + '" class="img-flag" width="16px"> ' + state.text + '</span>'
                    );
                    return $state;
                }

                // Safe Initialization for Add Form
                $('#offcanvas_add select[name="responsible_persons[]"], #offcanvas_add select[name="team_leaders[]"]').each(function() {
                    let $el = $(this);
                    if ($el.hasClass("select2-hidden-accessible")) {
                        $el.select2('destroy');
                    }
                    // Remove any leftover ghost select2 containers just in case
                    $el.next('.select2-container').remove();
                    $el.html(options).select2({
                        dropdownParent: $('#offcanvas_add'),
                        templateResult: formatState,
                        templateSelection: formatState
                    });
                });

                // Safe Initialization for Edit Form
                $('#offcanvas_edit select[name="responsible_persons[]"], #offcanvas_edit select[name="team_leaders[]"]').each(function() {
                    let $el = $(this);
                    if ($el.hasClass("select2-hidden-accessible")) {
                        $el.select2('destroy');
                    }
                    // Remove any leftover ghost select2 containers just in case
                    $el.next('.select2-container').remove();
                    $el.html(options).select2({
                        dropdownParent: $('#offcanvas_edit'),
                        templateResult: formatState,
                        templateSelection: formatState
                    });
                });
            }
        });

    // Project Contacts Dynamic Rows
    let contactIndex = 1;
    $('.add-contact-btn').on('click', function() {
        let row = `<tr>
            <td><input type="text" name="contacts[${contactIndex}][name]" class="form-control form-control-sm" required></td>
            <td><input type="email" name="contacts[${contactIndex}][email]" class="form-control form-control-sm"></td>
            <td><input type="text" name="contacts[${contactIndex}][phone]" class="form-control form-control-sm"></td>
            <td><input type="text" name="contacts[${contactIndex}][designation]" class="form-control form-control-sm"></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-contact-btn"><i class="ti ti-trash"></i></button></td>
        </tr>`;
        $('#addContactsTable tbody').append(row);
        contactIndex++;
    });

    let editContactIndex = 100; // Start high to avoid conflicts if there are many existing
    $('.add-contact-btn-edit').on('click', function() {
        let row = `<tr>
            <td><input type="text" name="contacts[${editContactIndex}][name]" class="form-control form-control-sm" required></td>
            <td><input type="email" name="contacts[${editContactIndex}][email]" class="form-control form-control-sm"></td>
            <td><input type="text" name="contacts[${editContactIndex}][phone]" class="form-control form-control-sm"></td>
            <td><input type="text" name="contacts[${editContactIndex}][designation]" class="form-control form-control-sm"></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-contact-btn"><i class="ti ti-trash"></i></button></td>
        </tr>`;
        $('#editContactsTable tbody').append(row);
        editContactIndex++;
    });

    $(document).on('click', '.remove-contact-btn', function() {
        $(this).closest('tr').remove();
    });
    
    // Clear add form contacts on close/open
    $('#offcanvas_add').on('hidden.bs.offcanvas', function () {
        $('#addContactsTable tbody').html(`<tr>
            <td><input type="text" name="contacts[0][name]" class="form-control form-control-sm" required></td>
            <td><input type="email" name="contacts[0][email]" class="form-control form-control-sm"></td>
            <td><input type="text" name="contacts[0][phone]" class="form-control form-control-sm"></td>
            <td><input type="text" name="contacts[0][designation]" class="form-control form-control-sm"></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-contact-btn"><i class="ti ti-trash"></i></button></td>
        </tr>`);
        contactIndex = 1;
    });

    // Populate contacts on edit
    });
</script>

<script>
    var table;
    // Column Definitions for Manage Columns feature
    const columnDefinitions = [
        { index: 1, visible: true },
        { index: 2, visible: true },
        { index: 3, visible: true },
        { index: 4, visible: true },
        { index: 5, visible: true },
        { index: 6, visible: true },
        { index: 7, visible: true },
        { index: 8, visible: true },
        { index: 9, visible: true }
    ];

    const pageIdentifier = 'projects';

    function loadColumnPreferences(callback) {
        $.ajax({
            url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug")]) }}',
            type: 'GET',
            data: { page_identifier: pageIdentifier },
            async: false,
            success: function(response) {
                if (response.success && response.data && Array.isArray(response.data) && response.data.length > 0) {
                    try {
                        const preferences = response.data;
                        preferences.forEach(pref => {
                            const prefIndex = parseInt(pref.index);
                            const col = columnDefinitions.find(c => c.index === prefIndex);
                            if (col) {
                                if (typeof pref.visible === 'string') {
                                    col.visible = pref.visible === 'true' || pref.visible === '1';
                                } else {
                                    col.visible = pref.visible !== undefined ? Boolean(pref.visible) : true;
                                }
                                // Update checkbox state
                                $(`#col_${col.index}`).prop('checked', col.visible);
                            }
                        });
                        if (callback) callback();
                    } catch (e) {
                        console.error('Error loading column preferences:', e);
                        if (callback) callback();
                    }
                } else {
                    if (callback) callback();
                }
            },
            error: function() {
                if (callback) callback();
            }
        });
    }

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
            }
        });
    }

    $(document).ready(function() {

        // Fix Select2 search focus inside Offcanvas
        $('#offcanvas_add .select2').select2({ dropdownParent: $('#offcanvas_add') });
        $('#offcanvas_edit .select2').select2({ dropdownParent: $('#offcanvas_edit') });
        
        // Initialize priority and status correctly for Add form safely
        $('#offcanvas_add select[name="priority"], #offcanvas_add select[name="status"]').each(function() {
            let $el = $(this);
            if ($el.hasClass("select2-hidden-accessible")) { $el.select2('destroy'); }
            $el.next('.select2-container').remove();
            $el.select2({
                dropdownParent: $('#offcanvas_add'),
                minimumResultsForSearch: Infinity,
                width: '100%'
            });
        });

        // Initialize priority and status correctly for Edit form safely
        $('#offcanvas_edit select[name="priority"], #offcanvas_edit select[name="status"]').each(function() {
            let $el = $(this);
            if ($el.hasClass("select2-hidden-accessible")) { $el.select2('destroy'); }
            $el.next('.select2-container').remove();
            $el.select2({
                dropdownParent: $('#offcanvas_edit'),
                minimumResultsForSearch: Infinity,
                width: '100%'
            });
        });

        if ($.fn.DataTable.isDataTable('#projects-datatable')) {
            $('#projects-datatable').DataTable().destroy();
        }
        
        var currentSort = '';
        var currentDateRange = '';

        loadColumnPreferences(function() {}); // Load preferences before table initialization

        table = $('#projects-datatable').DataTable({
            processing: true,
            serverSide: true,
            order: [], // Disable default datatables sorting so custom sort works
            ajax: {
                url: "{{ box_route('manage_users.projects') }}",
                data: function (d) {
                    d.sort_by = currentSort;
                    d.date_range = currentDateRange;
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name_link', name: 'name'},
                {data: 'client_name', name: 'client_name'},
                {data: 'priority', name: 'priority'},
                {data: 'start_date', name: 'start_date'},
                {data: 'due_date', name: 'due_date'},
                {data: 'project_type.name', name: 'projectType.name', defaultContent: 'N/A'},
                {data: 'status', name: 'status'},
                {data: 'team_members', name: 'team_members', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end'},
            ],
            initComplete: function(settings, json) {
                columnDefinitions.forEach(col => {
                    table.column(col.index).visible(col.visible, false);
                });
                table.columns.adjust().draw(false);
            }
        });

        // Handle Manage Columns Toggle
        $('.column-toggle').on('change', function() {
            const columnIndex = parseInt($(this).data('column-index'));
            const isVisible = $(this).is(':checked');
            
            const col = columnDefinitions.find(c => c.index === columnIndex);
            if (col) {
                col.visible = isVisible;
            }
            
            if (table) {
                table.column(columnIndex).visible(isVisible);
                table.columns.adjust().draw(false);
            }
            
            saveColumnPreferences();
        });

        $('.sort-item').on('click', function(e) {
            e.preventDefault();
            // Visual feedback for active sort option
            $('.sort-item i').removeClass('ti-check text-primary').addClass('ti-circle-chevron-right');
            $(this).find('i').removeClass('ti-circle-chevron-right').addClass('ti-check text-primary');
            
            currentSort = $(this).data('sort');
            $('#sortDropdownToggle span').text($(this).text().trim());
            table.ajax.reload();
        });

        // Date range picker updates the input value, listen to change
        $('.bookingrange').on('apply.daterangepicker', function(ev, picker) {
            currentDateRange = picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY');
            table.ajax.reload();
        });

        $('.bookingrange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            currentDateRange = '';
            table.ajax.reload();
        });

        // Add Project
        $('#addProjectForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: "{{ box_route('manage_users.projects.store') }}",
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        $('#offcanvas_add').offcanvas('hide');
                        form[0].reset();
                        table.ajax.reload(null, false);
                    } else {
                        showToast(response.message || 'Error occurred', 'danger');
                    }
                },
                error: function(xhr) {
                    showToast('Something went wrong. Please check your inputs.', 'danger');
                }
            });
        });

        // Edit Project Click
        $(document).on('click', '.edit-project-btn', function() {
            var id = $(this).data('id');
            $('#edit_id').val(id);
            
            $.ajax({
                url: "{{ box_route('manage_users.projects') }}/" + id,
                type: 'GET',
                success: function(response) {
                    if (response.status) {
                        var data = response.data;
                        $('#editProjectForm input[name="name"]').val(data.name);
                        $('#editProjectForm input[name="project_custom_id"]').val(data.project_custom_id);
                        $('#editProjectForm select[name="project_type_id"]').val(data.project_type_id).trigger('change');
                        $('#editProjectForm select[name="client_name"]').val(data.client_name).trigger('change');
                        $('#editProjectForm select[name="project_category_id"]').val(data.project_category_id).trigger('change');
                        $('#editProjectForm input[name="project_timing"]').val(data.project_timing);
                        $('#editProjectForm input[name="price"]').val(data.price);
                        $('#editProjectForm input[name="amount"]').val(data.amount);
                        $('#editProjectForm input[name="total"]').val(data.total);
                        $('#editProjectForm input[name="start_date"]').val(data.start_date);
                        $('#editProjectForm input[name="due_date"]').val(data.due_date);
                        $('#editProjectForm select[name="priority"]').val(data.priority).trigger('change');
                        $('#editProjectForm select[name="status"]').val(data.status).trigger('change');
                        $('#editProjectForm textarea[name="description"]').val(data.description);
                        $('#editProjectForm select[name="responsible_persons[]"]').val(response.responsible_persons).trigger('change');
                        $('#editProjectForm select[name="team_leaders[]"]').val(response.team_leaders).trigger('change');
                        
                        // Populate contacts
                        $('#editContactsTable tbody').empty();
                        if (data.contacts && data.contacts.length > 0) {
                            data.contacts.forEach((contact, index) => {
                                let row = `<tr>
                                    <input type="hidden" name="contacts[${index}][id]" value="${contact.id}">
                                    <td><input type="text" name="contacts[${index}][name]" value="${contact.name}" class="form-control form-control-sm" required></td>
                                    <td><input type="email" name="contacts[${index}][email]" value="${contact.email || ''}" class="form-control form-control-sm"></td>
                                    <td><input type="text" name="contacts[${index}][phone]" value="${contact.phone || ''}" class="form-control form-control-sm"></td>
                                    <td><input type="text" name="contacts[${index}][designation]" value="${contact.designation || ''}" class="form-control form-control-sm"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-contact-btn"><i class="ti ti-trash"></i></button></td>
                                </tr>`;
                                $('#editContactsTable tbody').append(row);
                            });
                            editContactIndex = data.contacts.length;
                        } else {
                            editContactIndex = 0; // Empty
                        }

                        $('#offcanvas_edit').offcanvas('show');
                    }
                }
            });
        });

        // Update Project
        $('#editProjectForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var id = $('#edit_id').val();
            $.ajax({
                url: "{{ box_route('manage_users.projects') }}/" + id,
                type: 'PUT',
                data: form.serialize(),
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        $('#offcanvas_edit').offcanvas('hide');
                        table.ajax.reload(null, false);
                    } else {
                        showToast(response.message || 'Error occurred', 'danger');
                    }
                },
                error: function(xhr) {
                    showToast('Something went wrong. Please check your inputs.', 'danger');
                }
            });
        });

        // Delete Project
        $(document).on('click', '.delete-project-btn', function() {
            var id = $(this).data('id');
            $('#confirm_delete_project_btn').data('id', id);
            $('#delete_project').modal('show');
        });

        $('#confirm_delete_project_btn').on('click', function() {
            var id = $(this).data('id');
            var $btn = $(this);
            $btn.prop('disabled', true).text('Deleting...');
            $.ajax({
                url: "{{ box_route('manage_users.projects') }}/" + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status) {
                        showToast(response.message, 'success');
                        table.ajax.reload(null, false);
                    } else {
                        showToast(response.message || 'Error occurred', 'danger');
                    }
                    $('#delete_project').modal('hide');
                },
                error: function(xhr) {
                    showToast('Something went wrong. Please check your inputs.', 'danger');
                    $('#delete_project').modal('hide');
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Yes, Delete it');
                }
            });
        });
    });
</script>
@endsection

