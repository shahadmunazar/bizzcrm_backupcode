@extends('layouts.main')
@section('content')
@section('title','Role And Permission')
	<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									<h4 class="page-title">Roles</h4>
								</div>
								<div class="col-4 text-end">
									<div class="head-icons">
										<a href="roles-permissions.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
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
											<input type="text" class="form-control" placeholder="Search Roles">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="text-sm-end">

											<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_role"><i class="ti ti-square-rounded-plus me-2"></i>Add New Roles</a>
										</div>
									</div>
								</div>
								<!-- /Search -->
							</div>
							<div class="card-body">
								<!-- Roles List -->
								<div class="table-responsive custom-table">
									<table class="table" id="roles_list">
										<thead class="thead-light">
											<tr>
												<th class="no-sort">
													<label class="checkboxs">
														<input type="checkbox" id="select-all"><span class="checkmarks"></span>
													</label>
												</th>
												<th>Role Name</th>
												<th>Created at</th>
												<th class="no-sort">Action</th>
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
								<!-- /Roles List -->

							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Add Role -->
		<div class="modal fade" id="add_role" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add Role</h5>
						<button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form action="roles-permissions.html">
						<div class="modal-body">
							<div class="mb-0">
								<label class="col-form-label">Role Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
						</div>
						<div class="modal-footer">
							<div class="d-flex align-items-center justify-content-end m-0">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-primary">Create</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /Add Role -->

		<!-- Edit Role -->
		<div class="modal fade" id="edit_role" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Edit Role</h5>
						<button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form action="roles-permissions.html">
						<div class="modal-body">
							<div class="mb-0">
								<label class="col-form-label">Role Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control" value="Admin">
							</div>
						</div>
						<div class="modal-footer">
							<div class="d-flex align-items-center justify-content-end m-0">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-primary">Save Changes</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /Edit Role -->
@endsection
