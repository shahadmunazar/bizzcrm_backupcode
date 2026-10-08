@extends('layouts.main')
@section('content')
<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">
						<div class="page-header">
							<div class="row align-items-center ">
								<div class="col-md-4">
									<h3 class="page-title">Project Dashboard</h3>
								</div>
								<div class="col-md-8 float-end ms-auto">
									<div class="d-flex title-head">
										<div class="daterange-picker d-flex align-items-center justify-content-center">
											<div class="form-sort me-2">
												<i class="ti ti-calendar"></i>
												<input type="text" class="form-control  date-range bookingrange">
											</div>
											<div class="head-icons mb-0">
												<a href="project-dashboard.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
												<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>



					</div>
				</div>

				<div class="row">
					<div class="col-md-7">
						<div class="card">
							<div class="card-header border-0 pb-0">
								<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
									<h4><i class="ti ti-grip-vertical me-1"></i>Recent Projects</h4>
									<div class="d-flex align-items-center flex-wrap row-gap-3">
										<div class="dropdown me-2">
											<a class="dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);">
												<i class="ti ti-calendar-check me-2"></i>Last 7 days
											</a>
											<div class="dropdown-menu dropdown-menu-end">
												<a href="javascript:void(0);" class="dropdown-item">
													Last 15 days
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Last 30 days
												</a>
											</div>
										</div>
										<a class="btn btn-primary" href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add">
											<i class="ti ti-square-rounded-plus me-1"></i>Add Project
										</a>
									</div>
								</div>

							</div>
							<div class="card-body">

								<div class="table-responsive custom-table">
									<table class="table" id="recent-project">
										<thead class="thead-light">
											<tr>
												<th>Priority</th>
												<th>Name</th>
												<th>Client</th>
												<th>Due Date</th>
											</tr>
										</thead>
										<tbody>

										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-5 d-flex">
						<div class="card w-100">
							<div class="card-header border-0 pb-0">
								<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
									<h4><i class="ti ti-grip-vertical me-1"></i>Project By Stage</h4>
									<div class="dropdown">
										<a class="dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);">
											<i class="ti ti-calendar-check me-2"></i>Last 30 days
										</a>
										<div class="dropdown-menu dropdown-menu-end">
											<a href="javascript:void(0);" class="dropdown-item">
												Last 15 days
											</a>
											<a href="javascript:void(0);" class="dropdown-item">
												Last 30 days
											</a>
										</div>
									</div>
								</div>

							</div>
							<div class="card-body">

								<div id="contacts-analysis"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6 d-flex">
						<div class="card w-100">
							<div class="card-header border-0 pb-0">
								<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
									<h4><i class="ti ti-grip-vertical me-1"></i>Projects By Stage</h4>
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<div class="dropdown me-2">
											<a class="dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);">
												Sales Pipeline
											</a>
											<div class="dropdown-menu dropdown-menu-end">
												<a href="javascript:void(0);" class="dropdown-item">
													Marketing Pipeline
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Sales Pipeline
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Email
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Chats
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Operational
												</a>
											</div>
										</div>
										<div class="dropdown">
											<a class="dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);">
												Last 3 months
											</a>
											<div class="dropdown-menu dropdown-menu-end">
												<a href="javascript:void(0);" class="dropdown-item">
													Last 30 Days
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Last 15 Days
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Last 7 Days
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="card-body">

								<div id="project-stage"></div>
							</div>
						</div>
					</div>
					<div class="col-md-6 ">
						<div class="card">
							<div class="card-header border-0 pb-0">
								<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
									<h4><i class="ti ti-grip-vertical me-1"></i>Leads By Stage</h4>
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<div class="dropdown me-2">
											<a class="dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);">
												Marketing Pipeline
											</a>
											<div class="dropdown-menu dropdown-menu-end">
												<a href="javascript:void(0);" class="dropdown-item">
													Marketing Pipeline
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Sales Pipeline
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Email
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Chats
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Operational
												</a>
											</div>
										</div>
										<div class="dropdown">
											<a class="dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);">
												Last 3 months
											</a>
											<div class="dropdown-menu dropdown-menu-end">
												<a href="javascript:void(0);" class="dropdown-item">
													Last 30 Days
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Last 15 Days
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Last 7 Days
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="card-body">

								<div id="last-chart"></div>
							</div>
						</div>
						<div class="card">
							<div class="card-header border-0 pb-0">
								<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
									<h4><i class="ti ti-grip-vertical me-1"></i>Won Deals Stage</h4>
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<div class="dropdown me-2">
											<a class="dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);">
												Marketing Pipeline
											</a>
											<div class="dropdown-menu dropdown-menu-end">
												<a href="javascript:void(0);" class="dropdown-item">
													Marketing Pipeline
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Sales Pipeline
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Email
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Chats
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Operational
												</a>
											</div>
										</div>
										<div class="dropdown">
											<a class="dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);">
												Last 3 months
											</a>
											<div class="dropdown-menu dropdown-menu-end">
												<a href="javascript:void(0);" class="dropdown-item">
													Last 30 Days
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Last 15 Days
												</a>
												<a href="javascript:void(0);" class="dropdown-item">
													Last 7 Days
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="card-body ">

								<div id="won-chart"></div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Add New Project -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h4>Add New Project</h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="project-dashboard.html">
					<div>
						<div class="row">
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Name <span class="text-danger">*</span></label>
									<input type="text" class="form-control">
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Project ID<span class="text-danger"> *</span></label>
									<input class="form-control" type="text">
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Project Type <span class="text-danger">*</span></label>
									<select class="select2">
										<option>Choose</option>
										<option>Mobile App</option>
										<option>Meeting</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Client <span class="text-danger">*</span></label>
									<select class="select">
										<option>Select</option>
										<option>NovaWave LLC</option>
										<option>Silver Hawk</option>
										<option>Harbor View</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Category <span class="text-danger">*</span></label>
									<select class="select">
										<option>Select</option>
										<option>Harbor View</option>
										<option>LLC</option>
									</select>
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Project Timing <span class="text-danger">*</span></label>
									<select class="select">
										<option>Select</option>
										<option>Hourly</option>
										<option>Weekly</option>
										<option>Monthly</option>
										<option>Less than 1 Month</option>
										<option>Less than 3 months</option>
									</select>
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Price <span class="text-danger">*</span></label>
									<input class="form-control" type="text">
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Amount <span class="text-danger">*</span></label>
									<input class="form-control" type="text">
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Total <span class="text-danger">*</span></label>
									<input class="form-control" type="text">
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Responsible Persons <span class="text-danger">*</span></label>
									<select class="multiple-img" multiple="multiple">
										<option data-image="assets/img/profiles/avatar-19.jpg" selected="">Darlee Robertson
										</option>
										<option data-image="assets/img/profiles/avatar-20.jpg">Sharon Roy</option>
										<option data-image="assets/img/profiles/avatar-21.jpg">Vaughan Lewis</option>
										<option data-image="assets/img/profiles/avatar-23.jpg">Jessica Louise</option>
										<option data-image="assets/img/profiles/avatar-16.jpg">Carol Thomas</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Team Leader <span class="text-danger">*</span></label>
									<select class="multiple-img" multiple="multiple">
										<option data-image="assets/img/profiles/avatar-19.jpg">Darlee Robertson</option>
										<option data-image="assets/img/profiles/avatar-20.jpg" selected="">Sharon Roy
										</option>
										<option data-image="assets/img/profiles/avatar-21.jpg">Vaughan Lewis</option>
										<option data-image="assets/img/profiles/avatar-23.jpg">Jessica Louise</option>
										<option data-image="assets/img/profiles/avatar-16.jpg">Carol Thomas</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Start Date <span class="text-danger">*</span></label>
									<div class="icon-form">
										<span class="form-icon"><i class="ti ti-calendar-event"></i></span>
										<input type="text" class="form-control datetimepicker" value="29-02-2020">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Due Date <span class="text-danger">*</span></label>
									<div class="icon-form">
										<span class="form-icon"><i class="ti ti-calendar-event"></i></span>
										<input type="text" class="form-control datetimepicker" value="29-02-2020">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Priority</label>
									<select class="select">
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
									<select class="select">
										<option>Select</option>
										<option>Active</option>
										<option>Inactive</option>
									</select>
								</div>
							</div>
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Description <span class="text-danger">*</span></label>
									<textarea class="form-control" rows="4"></textarea>
								</div>
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
@endsection
