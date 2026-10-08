@extends('layouts.main')
@section('content')
@section('title','Printers')
<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">
				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									<h4 class="page-title">Settings</h4>
								</div>
								<div class="col-4 text-end">
									<div class="head-icons">
										<a href="printers.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

						@include('settings.partials.nav')

						<div class="row">
							<div class="col-xl-3 col-lg-12 theiaStickySidebar">

								@include('settings.partials.sidebar')

							</div>

							<div class="col-xl-9 col-lg-12">

								<!-- Printers -->
								<div class="card">
									<div class="card-body">
										<div class="d-flex align-items-center justify-content-between mb-3">
											<h4>Printer</h4>
											<a href="javascript:void(0)" class="btn btn-icon btn-sm border btn-outline-light" data-bs-toggle="modal" data-bs-target="#add_printer"><i class="ti ti-plus"></i></a>
										</div>
										<form action="printers.html">
											<!-- Hp -->
											<div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
												<div>
													<h5 class="fw-semibold mb-1">Hp</h5>
													<p>Shows the details of Hp printers</p>
												</div>
												<div class="dropdown">
													<a href="#" class="btn btn-icon btn-sm border btn-outline-light d-inline-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
													<div class="dropdown-menu dropdown-menu-right">
														<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_printer"><i class="fa-solid fa-pencil text-blue me-1"></i>Edit</a>
														<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_printer"><i class="fa-regular fa-trash-can text-danger me-1"></i>Delete</a>
													</div>
												</div>
											</div>
											<div class="border-bottom mb-3">
												<div class="row">
													<div class="col-md-3 col-sm-6">
														<div class="mb-3">
															<label class="form-label">Printer Name </label>
															<input type="text" class="form-control" value="Hp printer">
														</div>
													</div>
													<div class="col-md-3 col-sm-6">
														<div class="mb-3">
															<label class="form-label">Connection Type</label>
															<input type="text" class="form-control" value="Network">
														</div>
													</div>
													<div class="col-md-3 col-sm-6">
														<div class="mb-3">
															<label class="form-label">IP Address</label>
															<input type="text" class="form-control" value="192.168.0.1">
														</div>
													</div>
													<div class="col-md-3 col-sm-6">
														<div class="mb-3">
															<label class="form-label">Port</label>
															<input type="text" class="form-control" value="900">
														</div>
													</div>
												</div>
											</div>
											<!-- /Euro -->

											<!-- Epson -->
											<div class="d-flex align-items-center justify-content-between border-bottom mb-3 pb-3">
												<div>
													<h5 class="fw-semibold mb-1">Epson</h5>
													<p>Shows the details of Epson printers</p>
												</div>
												<div class="dropdown">
													<a href="#" class="btn btn-icon btn-sm border btn-outline-light d-inline-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
													<div class="dropdown-menu dropdown-menu-right">
														<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_printer"><i class="fa-solid fa-pencil text-blue me-1"></i>Edit</a>
														<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_printer"><i class="fa-regular fa-trash-can text-danger me-1"></i>Delete</a>
													</div>
												</div>
											</div>
											<div class="mb-0">
												<div class="row">
													<div class="col-md-3 col-sm-6">
														<div class="mb-3">
															<label class="form-label">Printer Name </label>
															<input type="text" class="form-control" value="Epson">
														</div>
													</div>
													<div class="col-md-3 col-sm-6">
														<div class="mb-3">
															<label class="form-label">Connection Type</label>
															<input type="text" class="form-control" value="Network">
														</div>
													</div>
													<div class="col-md-3 col-sm-6">
														<div class="mb-3">
															<label class="form-label">IP Address</label>
															<input type="text" class="form-control" value="192.168.0.1">
														</div>
													</div>
													<div class="col-md-3 col-sm-6">
														<div class="mb-3">
															<label class="form-label">Port</label>
															<input type="text" class="form-control" value="900">
														</div>
													</div>
												</div>
											</div>
											<!-- /Epson -->

											<div>
												<a href="#" class="btn btn-light me-2">Cancel</a>
												<button type="submit" class="btn btn-primary">Save Changes</button>
											</div>
										</form>

									</div>
								</div>
								<!-- /Printers -->

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Add Printer -->
		<div class="modal fade" id="add_printer" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add Printer</h5>
						<button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form action="printers.html">
						<div class="modal-body">
							<div class="mb-3">
								<label class="form-label">Printer Company <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
							<div class="mb-3">
								<label class="form-label">Printer Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
							<div class="mb-3">
								<label class="form-label">Connection Type <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
							<div class="mb-3">
								<label class="form-label">IP Address <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
							<div class="mb-0">
								<label class="form-label">Port <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
						</div>
						<div class="modal-footer">
							<div class="d-flex align-items-center">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-primary">Save</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /Add Currency -->

		<!-- Edit Printer -->
		<div class="modal fade" id="edit_printer" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Edit Printer</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form action="printers.html">
						<div class="modal-body">
							<div class="mb-3">
								<label class="form-label">Printer Company <span class="text-danger">*</span></label>
								<input type="text" class="form-control" value="Hp">
							</div>
							<div class="mb-3">
								<label class="form-label">Printer Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control" value="Hp printer">
							</div>
							<div class="mb-3">
								<label class="form-label">Connection Type <span class="text-danger">*</span></label>
								<input type="text" class="form-control" value="Network">
							</div>
							<div class="mb-3">
								<label class="form-label">IP Address <span class="text-danger">*</span></label>
								<input type="text" class="form-control" value="198.162.0.1">
							</div>
							<div class="mb-0">
								<label class="form-label">Port <span class="text-danger">*</span></label>
								<input type="text" class="form-control" value="900">
							</div>
						</div>
						<div class="modal-footer">
							<div class="d-flex align-items-center">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-primary">Save</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /Edit Printer -->

		<!-- Delete Printer -->
		<div class="modal fade" id="delete_printer" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<form action="printers.html">
							<div class="text-center">
								<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
									<i class="ti ti-trash-x fs-36 text-danger"></i>
								</div>
								<h4 class="mb-2">Remove Printer?</h4>
								<p class="mb-0">Are you sure you want to remove it.</p>
								<div class="d-flex align-items-center justify-content-center mt-4">
									<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
									<button type="submit" class="btn btn-danger">Yes, Delete it</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Printer -->

        @endsection
