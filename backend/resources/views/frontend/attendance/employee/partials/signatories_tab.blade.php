						<div class="tab-pane" id="bottom-justified-tab5">

						 <div class="card">
							<div class="card-header">

									<div class="row align-items-center">
										<div class="col-sm-4">

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
												<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_signatory"><i class="ti ti-square-rounded-plus me-2"></i>Add Signatory</a>
											</div>
										</div>
									</div>

							</div>
						</div>


							<div class="card">

							<div class="card-body">
								<!-- Filter -->
								<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<h4 class="page-title">Authorized Signatory</h4>
									</div>

									<div class="col-sm-4">
											<div class="icon-form mb-3 mb-sm-0">
												<span class="form-icon"><i class="ti ti-search"></i></span>
												<input type="text" class="form-control" placeholder="Search...">
											</div>
										</div>
								</div>
								<!-- /Filter -->

								<!-- Campaign List -->
								<div class="table-responsive custom-table">
									<table class="table" id="">
										<thead class="thead-light">
										<tr>
												<th class="no-sort">
													<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
												</th>
												<th>Employee Id</th>
												<th>Authorized Signatory</th>
												<th>Status</th>
												<th class="text-start">Action</th>
										</tr>
										</thead>
										<tbody id="signatories-tbody">
											<!-- Data will be loaded via jQuery AJAX on page load -->
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
								<!-- /Campaign List -->

							</div>
						</div>
</div>