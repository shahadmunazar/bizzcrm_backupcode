@extends('layouts.main')
@section('title', 'Loan Settings')
@section('content')
<div class="page-wrapper cardhead">
			<div class="content">

				<!-- Page Header -->
				<div class="page-header">

					<div class="row align-items-center">
								<div class="col-4">
									<h3 class="page-title">Loan Type</h3>
								</div>
								<div class="col-8 text-end">
									<div class="head-icons">
										<a href="#" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
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
											<input type="text" class="form-control" placeholder="Search...">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
											<div class="dropdown me-2">
												<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
												<div class="dropdown-menu  dropdown-menu-end">
													<ul>
														<li>
															<a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-file-type-pdf text-danger me-1"></i>Export
																as PDF</a>
														</li>
														<li>
															<a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-file-type-xls text-green me-1"></i>Export
																as Excel </a>
														</li>
													</ul>
												</div>
											</div>
											<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loan_category"><i class="ti ti-square-rounded-plus me-2"></i>Add Loan Type</a>
										</div>
									</div>
								</div>
								<!-- /Search -->
							</div>
				</div>



				<div class="card">

					<div class="card-body">
								<!-- Filter -->

								<!-- Contact List -->
								<div class="table-responsive custom-table">
									<table class="table" border="1" id="">
										<thead class="thead-light">
											<tr>
												<th class="no-sort">
													<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
												</th>
												<th>Loan Type</th>
												<th class="text-start">Action</th>
											</tr>

										</thead>
										<tbody>
											<tr>
											    <td class="sorting_1"><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
											    <td>Business Loan</td>
												<td>
													<div class="hstack gap-2 fs-15">
														<a class="btn btn-icon btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#edit_loan_category" href="#"><i class="ti ti-edit text-blue"></i></a>
														<a class="btn btn-icon btn-sm btn-light" href="#" data-bs-toggle="modal" data-bs-target="#delete_loan"><i class="ti ti-trash text-danger"></i></a>
													</div>
												</td>
											</tr>
											<tr>
											    <td class="sorting_1"><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
											    <td>Study Loan</td>
												<td>
													<div class="hstack gap-2 fs-15">
														<a class="btn btn-icon btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#edit_loan_category" href="#"><i class="ti ti-edit text-blue"></i></a>
														<a class="btn btn-icon btn-sm btn-light" href="#" data-bs-toggle="modal" data-bs-target="#delete_loan"><i class="ti ti-trash text-danger"></i></a>
													</div>
												</td>
											</tr>
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
								<!-- /Contact List -->

							</div>












				</div>

			</div>
		</div>


	<div class="modal custom-modal fade" id="loan_category" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add Loan Type</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="leads.html">
							<div class="mb-3">
								<label class="col-form-label">Loan Category <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
							<div class="modal-btn text-end">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-danger">Add</button>
							</div>
						</form>
					</div>
				</div>
			</div>
	</div>

	<div class="modal custom-modal fade" id="edit_loan_category" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Edit Loan Type</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="leads.html">
							<div class="mb-3">
								<label class="col-form-label">Loan Category <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
							<div class="modal-btn text-end">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-danger">Add</button>
							</div>
						</form>
					</div>
				</div>
			</div>
	</div>




	<!-- Delete -->
		<div class="modal fade" id="delete_loan" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove ?</h4>
							<p class="mb-0">Are you sure you want to remove <br> you selected.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="leads.html" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete -->

@endsection
