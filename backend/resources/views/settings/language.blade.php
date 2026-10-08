@extends('layouts.main')
@section('content')
@section('title','Language')
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
										<a href="language.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
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

								<!-- Custom Fields -->
								<div class="card">
									<div class="card-body">
										<h4 class="fw-semibold mb-3">Language</h4>
										<!-- Search -->
										<div class="row">
											<div class="col-xl-3 col-md-3 col-lg-5">
												<div class="mb-3 icon-form">
													<span class="form-icon"><i class="ti ti-search"></i></span>
													<input type="text" class="form-control" placeholder="Search Language">
												</div>
											</div>
											<div class="col-xl-9 col-md-9 col-lg-12">
												<div class="d-flex align-items-center flex-wrap justify-content-sm-end">
													<div class="dropdown me-2 mb-3">
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
													<a href="javascript:void(0);" class="btn btn-primary me-2 mb-3" data-bs-toggle="modal" data-bs-target="#import_sample"><i class="ti ti-download me-2"></i>Import Sample</a>
													<a href="javascript:void(0);" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#add_translation"><i class="ti ti-square-rounded-plus me-2"></i>Add
														Translation</a>
												</div>
											</div>
										</div>
										<!-- /Search -->

										<!-- Filter -->
										<div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
											<div class="dropdown mb-3">
												<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-sort-ascending-2 me-2"></i>Sort </a>
												<div class="dropdown-menu  dropdown-menu-start">
													<ul>
														<li>
															<a href="javascript:void(0);" class="dropdown-item">
																<i class="ti ti-circle-chevron-right me-1"></i>Ascending
															</a>
														</li>
														<li>
															<a href="javascript:void(0);" class="dropdown-item">
																<i class="ti ti-circle-chevron-right me-1"></i>Descending
															</a>
														</li>
														<li>
															<a href="javascript:void(0);" class="dropdown-item">
																<i class="ti ti-circle-chevron-right me-1"></i>Recently
																Viewed
															</a>
														</li>
														<li>
															<a href="javascript:void(0);" class="dropdown-item">
																<i class="ti ti-circle-chevron-right me-1"></i>Recently
																Added
															</a>
														</li>
													</ul>
												</div>
											</div>
											<div class="dropdown mb-3">
												<a href="javascript:void(0);" class="btn bg-soft-purple text-purple" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-columns-3 me-2"></i>Manage Columns</a>
												<div class="dropdown-menu  dropdown-menu-md-end dropdown-md p-3">
													<h4 class="mb-2 fw-semibold">Want to manage datatables?</h4>
													<p class="mb-3">Please drag and drop your column to reorder your
														table and enable see option as you want.</p>
													<div class="border-top pt-3">
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Language</p>
															<div class="status-toggle">
																<input type="checkbox" id="col-name" class="check">
																<label for="col-name" class="checktoggle"></label>
															</div>
														</div>
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Code</p>
															<div class="status-toggle">
																<input type="checkbox" id="col-phone" class="check">
																<label for="col-phone" class="checktoggle"></label>
															</div>
														</div>
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>RTL</p>
															<div class="status-toggle">
																<input type="checkbox" id="col-email" class="check">
																<label for="col-email" class="checktoggle"></label>
															</div>
														</div>
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Total</p>
															<div class="status-toggle">
																<input type="checkbox" id="col-tag" class="check">
																<label for="col-tag" class="checktoggle"></label>
															</div>
														</div>
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Done</p>
															<div class="status-toggle">
																<input type="checkbox" id="col-loc" class="check">
																<label for="col-loc" class="checktoggle"></label>
															</div>
														</div>
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Progress</p>
															<div class="status-toggle">
																<input type="checkbox" id="col-rate" class="check">
																<label for="col-rate" class="checktoggle"></label>
															</div>
														</div>
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Status</p>
															<div class="status-toggle">
																<input type="checkbox" id="col-owner" class="check">
																<label for="col-owner" class="checktoggle"></label>
															</div>
														</div>
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Contact</p>
															<div class="status-toggle">
																<input type="checkbox" id="col-contact" class="check" checked="">
																<label for="col-contact" class="checktoggle"></label>
															</div>
														</div>
														<div class="d-flex align-items-center justify-content-between mb-3">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Status</p>
															<div class="status-toggle">
																<input type="checkbox" id="col-status" class="check">
																<label for="col-status" class="checktoggle"></label>
															</div>
														</div>
														<div class="d-flex align-items-center justify-content-between">
															<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Action</p>
															<div class="status-toggle">
																<input type="checkbox" id="col-action" class="check">
																<label for="col-action" class="checktoggle"></label>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<!-- /Filter -->

										<!-- Contact List -->
										<div class="table-responsive custom-table">
											<table class="table" id="language-list">
												<thead class="thead-light">
													<tr>
														<th class="no-sort"></th>
														<th class="no-sort"></th>
														<th>Language</th>
														<th>Code</th>
														<th>RTL</th>
														<th>Total</th>
														<th>Done</th>
														<th>Progress</th>
														<th>Status</th>
														<th>Action</th>
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
										<!-- /Contact List -->

									</div>
								</div>
								<!-- /Custom Fields -->

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Add Translation -->
		<div class="modal fade" id="add_translation" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add Translation</h5>
						<button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form action="language.html">
						<div class="modal-body">
							<div class="mb-3">
								<label class="form-label">Language <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
							<div class="mb-3">
								<label class="form-label">Code <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
							<div class="mb-0">
								<label class="form-label">Status</label>
								<div class="radio-wrap">
									<div class="d-flex flex-wrap">
										<div class="radio-btn">
											<input type="radio" class="status-radio" id="add-active" name="status" checked="">
											<label for="add-active">Active</label>
										</div>
										<div class="radio-btn">
											<input type="radio" class="status-radio" id="add-inactive" name="status">
											<label for="add-inactive">Inactive</label>
										</div>
									</div>
								</div>
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
		<!-- /Add Translation -->

		<!-- Edit Translation -->
		<div class="modal fade" id="edit_translation" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Edit Translation</h5>
						<button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form action="language.html">
						<div class="modal-body">
							<div class="mb-3">
								<label class="form-label">Language <span class="text-danger">*</span></label>
								<input type="text" class="form-control" value="English">
							</div>
							<div class="mb-3">
								<label class="form-label">Code <span class="text-danger">*</span></label>
								<input type="text" class="form-control" value="en">
							</div>
							<div class="mb-0">
								<label class="form-label">Status</label>
								<div class="radio-wrap">
									<div class="d-flex flex-wrap">
										<div class="radio-btn">
											<input type="radio" class="status-radio" id="edit-active" name="status" checked="">
											<label for="edit-active">Active</label>
										</div>
										<div class="radio-btn">
											<input type="radio" class="status-radio" id="edit-inactive" name="status">
											<label for="edit-inactive">Inactive</label>
										</div>
									</div>
								</div>
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
		<!-- /Edit Translation -->

		<!-- Import Sample -->
		<div class="modal fade" id="import_sample" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Import Sample</h5>
						<button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form action="language.html">
						<div class="modal-body">
							<div class="mb-3">
								<label class="form-label">File <span class="text-danger">*</span></label>
								<select class="select">
									<option>Inventory</option>
									<option>Expense</option>
									<option>Product</option>
								</select>
							</div>
							<div class="mb-3">
								<label class="form-label">Language <span class="text-danger">*</span></label>
								<select class="select">
									<option>English</option>
									<option>German</option>
									<option>French</option>
								</select>
							</div>
							<div class="mb-3">
								<div class="drag-attach">
									<input type="file">
									<div class="img-upload">
										<i class="ti ti-file-broken"></i>Upload File
									</div>
								</div>
							</div>
							<div class="mb-0">
								<label class="form-label">Uploaded Files</label>
								<div class="upload-file">
									<h6>Projectneonals teyys.xls</h6>
									<p>4.25 MB</p>
									<div class="progress">
										<div class="progress-bar bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<p class="black-text">45%</p>
								</div>
								<div class="upload-file d-flex align-items-center">
									<div>
										<h6 class="fw-medium">tes.txt</h6>
										<p>4.25 MB</p>
									</div>
									<a href="javascript:void(0);" class="text-danger"><i class="ti ti-trash-x"></i></a>
								</div>
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
		<!-- /Import Sample -->

		<!-- Delete Translation -->
		<div class="modal fade" id="delete_translation" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<form action="language.html">
							<div class="text-center">
								<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
									<i class="ti ti-trash-x fs-36 text-danger"></i>
								</div>
								<h4 class="mb-2">Remove Translation?</h4>
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
		<!-- /Delete Translation -->

        @endsection
