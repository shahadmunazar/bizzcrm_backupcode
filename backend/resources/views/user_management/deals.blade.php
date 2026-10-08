@extends('layouts.main')
@section('title', 'Deals')
@section('description', 'Manage all your deals and opportunities.')
@section('content')
<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									<h4 class="page-title">Deals <span class="count-title">{{ $totalDeals ?? 0 }}</span></h4>
								</div>
								<div class="col-4 text-end">
									<div class="head-icons">
										<a href="javascript:void(0);" onclick="$('#deal_list_table').DataTable().ajax.reload(null, false);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
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
											<input type="text" id="customDealSearch" class="form-control" placeholder="Search Deals">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
											<div class="dropdown me-2">
												<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
												<div class="dropdown-menu  dropdown-menu-end">
													<ul>
														<li>
															<a href="javascript:void(0);" data-type="pdf" class="dropdown-item export-deals-btn"><i class="ti ti-file-type-pdf text-danger me-1"></i>Export
																as PDF</a>
														</li>
														<li>
															<a href="javascript:void(0);" data-type="xlsx" class="dropdown-item export-deals-btn"><i class="ti ti-file-type-xls text-green me-1"></i>Export
																as Excel </a>
														</li>
													</ul>
												</div>
											</div>
											<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-square-rounded-plus me-2"></i>Add Deals</a>
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
											<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-sort-ascending-2 me-2"></i>Sort
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
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="recently-viewed">
															<i class="ti ti-circle-chevron-right me-1"></i>Recently
															Viewed
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="recently-added">
															<i class="ti ti-circle-chevron-right me-1"></i>Recently
															Added
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
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Deal Name</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-name" class="check column-toggle" data-column="2" checked>
															<label for="col-name" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Pipeline</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-pipeline" class="check column-toggle" data-column="3" checked>
															<label for="col-pipeline" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Stage</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-stage" class="check column-toggle" data-column="4" checked>
															<label for="col-stage" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Deal Value</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-value" class="check column-toggle" data-column="5" checked>
															<label for="col-value" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Contact</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-contact" class="check column-toggle" data-column="6" checked>
															<label for="col-contact" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Project</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-project" class="check column-toggle" data-column="7" checked>
															<label for="col-project" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Period</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-period" class="check column-toggle" data-column="8" checked>
															<label for="col-period" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Source</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-source" class="check column-toggle" data-column="9" checked>
															<label for="col-source" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Priority</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-priority" class="check column-toggle" data-column="10" checked>
															<label for="col-priority" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Tags</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-tags" class="check column-toggle" data-column="11" checked>
															<label for="col-tags" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Due Date</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-due" class="check column-toggle" data-column="12" checked>
															<label for="col-due" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Expected Close Date</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-expected" class="check column-toggle" data-column="13" checked>
															<label for="col-expected" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Owner</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-owner" class="check column-toggle" data-column="14" checked>
															<label for="col-owner" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Status</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-status" class="check column-toggle" data-column="15" checked>
															<label for="col-status" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Action</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-action" class="check column-toggle" data-column="16" checked>
															<label for="col-action" class="checktoggle"></label>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="form-sorts dropdown me-2">
											<a href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-filter-share"></i>Filter</a>
											<div class="filter-dropdown-menu dropdown-menu  dropdown-menu-md-end p-3">
												<div class="filter-set-view">
													<div class="filter-set-head">
														<h4><i class="ti ti-filter-share"></i>Filter</h4>
													</div>
													<div class="accordion" id="accordionExample">
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">Deals Name</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse show" id="collapseTwo" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<div class="mb-2 icon-form">
																		<span class="form-icon"><i class="ti ti-search"></i></span>
																		<input type="text" class="form-control" placeholder="Search Country">
																	</div>
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
																					<span class="checkmarks"></span>
																					Collins
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Konopelski
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Adams
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Gutkowski
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Walter
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#owner" aria-expanded="false" aria-controls="owner">Owner</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="owner" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<div class="mb-2 icon-form">
																		<span class="form-icon"><i class="ti ti-search"></i></span>
																		<input type="text" class="form-control" placeholder="Search Owner">
																	</div>
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
																					<span class="checkmarks"></span>
																					Hendry Milner
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Guilory Berggren
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Jami Carlile
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Theresa Nelson
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Smith Cooper
																				</label>
																			</div>
																		</li>

																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#Status" aria-expanded="false" aria-controls="Status">Status</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="Status" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
																					<span class="checkmarks"></span>
																					Won
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Open
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Lost
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">Rating</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="collapseOne" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
																					<span class="checkmarks"></span>
																					<span class="rating">
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<span>5.0</span>
																					</span>
																				</label>
																			</div>

																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					<span class="rating">
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star"></i>
																						<span>4.0</span>
																					</span>
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					<span class="rating">
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<span>3.0</span>
																					</span>
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					<span class="rating">
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<span>2.0</span>
																					</span>
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					<span class="rating">
																						<i class="fa fa-star filled"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<span>1.0</span>
																					</span>
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">Tags</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="collapseThree" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
																					<span class="checkmarks"></span>
																					Promotion
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Rated
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Rejected
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Collab
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Calls
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
													</div>
													<div class="filter-reset-btns">
														<div class="row">
															<div class="col-6">
																<a href="#" class="btn btn-light">Reset</a>
															</div>
															<div class="col-6">
																<a href="deals.html" class="btn btn-primary">Filter</a>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="view-icons">
											<a href="deals.html" class="active"><i class="ti ti-list-tree"></i></a>
											<a href="deals-kanban.html"><i class="ti ti-grid-dots"></i></a>
										</div>
									</div>
								</div>
								<!-- /Filter -->


								<!-- Contact List -->
								<div class="table-responsive custom-table">
									<table class="table" id="deal_list_table">
										<thead class="thead-light">
											<tr>
												<th class="no-sort">
													<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
												</th>
												<th class="no-sort"></th>
												<th>Deal Name</th>
												<th>Pipeline</th>
												<th>Stage</th>
												<th>Deal Value</th>
												<th>Contact</th>
												<th>Project</th>
												<th>Period</th>
												<th>Source</th>
												<th>Priority</th>
												<th>Tags</th>
												<th>Due Date</th>
												<th>Expected Close Date</th>
												<th>Owner</th>
												<th>Status</th>
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
								<!-- /Contact List -->

							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Add New Deals -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Add New Deals</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form id="addDealForm">
					<div class="row">
						<div class="col-md-12">
							<div class="mb-3">
								<label class="col-form-label">Deal Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control" name="name">
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<div class="d-flex align-items-center justify-content-between">
									<label class="col-form-label">Pipeine <span class="text-danger">*</span></label>
									<a href="" class="label-add " data-bs-toggle="offcanvas" data-bs-target="#offcanvas_pipeline">
										<i class="ti ti-square-rounded-plus"></i>
										Add New
									</a>
								</div>
								<select class="select2" name="pipeline_id" id="addDealPipeline"><option value="">Choose Pipeline</option></select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Status <span class="text-danger">*</span></label>
								<select class="select2" name="status" id="addDealStatus"><option value="">Choose Status</option></select>
							</div>
						</div>
						<div class="col-lg-3 col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Deal Value<span class="text-danger"> *</span></label>
								<input class="form-control" type="text" name="deal_value">
							</div>
						</div>
						<div class="col-lg-3 col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Currency <span class="text-danger">*</span></label>
								<select class="select">
									<option>Choose</option>
									<option>Dollar</option>
									<option>Euro</option>
									<option>Pound</option>
									<option>Rupee</option>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Period <span class="text-danger">*</span></label>
								<select class="select2" name="period" id="addDealPeriod"><option value="">Choose</option></select>
							</div>
						</div>
						<div class="col-lg-3 col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Period Value <span class="text-danger">*</span></label>
								<input class="form-control" type="text" name="period_value">
							</div>
						</div>
						<div class="col-md-12">
							<div class="mb-3">
								<label class="col-form-label">Lead <span class="text-danger">*</span></label>
								<select class="select2 lead-select" name="lead_id" id="addDealLead"><option value="">Choose Lead</option></select>
							</div>
							<div class="mb-3">
								<label class="col-form-label">Contact <span class="text-danger">*</span></label>
								<select class="select2 contact-select" name="contact_id" id="addDealContact"><option value="">Choose Contact</option></select>
							</div>
							<div class="mb-3">
								<label class="col-form-label">Project <span class="text-danger">*</span></label>
								<select class="select2 project-select" name="project_id" id="addDealProject"><option value="">Choose Project</option></select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Due Date <span class="text-danger">*</span></label>
								<div class="icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="form-control datetimepicker" placeholder="" name="due_date">
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Expected Closing Date <span class="text-danger">*</span></label>
								<div class="icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="form-control datetimepicker" placeholder="" name="expected_close_date">
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="mb-3">
								<label class="col-form-label">Assignee <span class="text-danger">*</span></label>
								<select class="select2 multiple-img" multiple="multiple" name="assignees[]" id="addAssignees"></select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Follow Up Date <span class="text-danger">*</span></label>
								<div class="icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="form-control datetimepicker" placeholder="">
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Source <span class="text-danger">*</span></label>
								<select class="select2" name="source" id="addDealSource"><option value="">Choose Source</option></select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Tags <span class="text-danger">*</span></label>
								<select class="select2" multiple="multiple" name="tags[]" id="addDealTags"></select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Priority <span class="text-danger">*</span></label>
								<select class="select2" name="priority" id="addDealPriority"><option value="">Choose Priority</option></select>
							</div>
						</div>
						<div class="col-lg-12">
							<div class="mb-3">
								<label class="col-form-label">Description <span class="text-danger">*</span></label>
								<div class="summernote"></div>
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
		<!-- /Add New Deals -->

		<!-- Edit Deals -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Edit Deals</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form id="editDealForm">
                    <input type="hidden" name="id" id="editDealId">
					<div class="row">
						<div class="col-md-12">
							<div class="mb-3">
								<label class="col-form-label">Deal Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control" name="name" id="editDealName">
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<div class="d-flex align-items-center justify-content-between">
									<label class="col-form-label">Pipeine <span class="text-danger">*</span></label>
									<a href="" class="label-add " data-bs-toggle="offcanvas" data-bs-target="#offcanvas_pipeline">
										<i class="ti ti-square-rounded-plus"></i>
										Add New
									</a>
								</div>
								<select class="select2" name="pipeline_id" id="editDealPipeline"><option value="">Choose Pipeline</option></select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Status <span class="text-danger">*</span></label>
								<select class="select2" name="status" id="editDealStatus"><option value="">Choose Status</option></select>
							</div>
						</div>
						<div class="col-lg-3 col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Deal Value<span class="text-danger"> *</span></label>
								<input class="form-control" type="text" name="deal_value" id="editDealValue">
							</div>
						</div>
						<div class="col-lg-3 col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Currency <span class="text-danger">*</span></label>
								<select class="select">
									<option>Choose</option>
									<option>Dollar</option>
									<option>Euro</option>
									<option>Pound</option>
									<option>Rupee</option>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Period <span class="text-danger">*</span></label>
								<select class="select2" name="period" id="editDealPeriod"><option value="">Choose</option></select>
							</div>
						</div>
						<div class="col-lg-3 col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Period Value <span class="text-danger">*</span></label>
								<input class="form-control" type="text" name="period_value" id="editDealPeriodValue">
							</div>
						</div>
						<div class="col-md-12">
							<div class="mb-3">
								<label class="col-form-label">Lead <span class="text-danger">*</span></label>
								<select class="select2 lead-select" name="lead_id" id="editDealLead"><option value="">Choose Lead</option></select>
							</div>
							<div class="mb-3">
								<label class="col-form-label">Contact <span class="text-danger">*</span></label>
								<select class="select2" name="contact_id" id="editDealContact">
									<option value="">Choose Contact</option>
								</select>
							</div>
							<div class="mb-3">
								<label class="col-form-label">Project <span class="text-danger">*</span></label>
								<select class="select2" name="project_id" id="editDealProject">
									<option value="">Choose Project</option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Due Date <span class="text-danger">*</span></label>
								<div class="icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="form-control datetimepicker" name="due_date" id="editDealDueDate" placeholder="">
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Expected Closing Date <span class="text-danger">*</span></label>
								<div class="icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="form-control datetimepicker" name="expected_close_date" id="editDealExpectedCloseDate" placeholder="">
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="mb-3">
								<label class="col-form-label">Assignee <span class="text-danger">*</span></label>
								<select class="select2" multiple="multiple" name="assignees[]" id="editAssignees">
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Source <span class="text-danger">*</span></label>
								<select class="select2" name="source" id="editDealSource"><option value="">Choose Source</option></select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Tags <span class="text-danger">*</span></label>
								<select class="select2" multiple="multiple" name="tags[]" id="editDealTags"></select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Priority <span class="text-danger">*</span></label>
								<select class="select2" name="priority" id="editDealPriority"><option value="">Choose Priority</option></select>
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
		<!-- /Edit Deals -->

		<!-- Delete Deal -->
		<div class="modal fade" id="delete_deal" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Deal?</h4>
							<p class="mb-0">Are you sure you want to remove <br> deal you selected.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="deals.html" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Deal -->

		<!-- Create Deal -->
		<div class="modal custom-modal fade" id="create_success" role="dialog">
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
								<i class="ti ti-medal"></i>
							</div>
							<h3>Deal Created Successfully!!!</h3>
							<p>View the details of deal, created</p>
							<div class="col-lg-12 text-center modal-btn">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<a href="deals-details.html" class="btn btn-primary">View Details</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Create Deal -->

		<!-- Add New Pipeline -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_pipeline">
			<div class="offcanvas-header border-bottom">
				<h4>Add New Pipeline</h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form id="addPipelineForm">
					<div>
						<div class="mb-3">
							<label class="col-form-label">Pipeline Name <span class="text-danger">*</span></label>
							<input class="form-control" type="text" name="name" required>
						</div>
						<div class="mb-3">
							<div class="pipe-title d-flex align-items-center justify-content-between">
								<h5 class="form-title">Pipeline Stages</h5>
								<a href="#" class="add-stage" data-bs-toggle="modal" data-bs-target="#add_stage"><i class="ti ti-square-rounded-plus"></i>Add New</a>
							</div>
							<div class="pipeline-listing" id="pipeline-stages-container">
								<div class="pipeline-item">
                                    <input type="hidden" name="stages[]" value="Inpipeline">
									<p><i class="ti ti-grip-vertical"></i> Inpipeline</p>
									<div class="action-pipeline">
										<a href="javascript:void(0);" onclick="$(this).closest('.pipeline-item').remove()"><i class="ti ti-trash text-danger"></i>Delete</a>
									</div>
								</div>
								<div class="pipeline-item">
                                    <input type="hidden" name="stages[]" value="Follow Up">
									<p><i class="ti ti-grip-vertical"></i> Follow Up</p>
									<div class="action-pipeline">
										<a href="javascript:void(0);" onclick="$(this).closest('.pipeline-item').remove()"><i class="ti ti-trash text-danger"></i>Delete</a>
									</div>
								</div>
								<div class="pipeline-item">
                                    <input type="hidden" name="stages[]" value="Schedule Service">
									<p><i class="ti ti-grip-vertical"></i> Schedule Service</p>
									<div class="action-pipeline">
										<a href="javascript:void(0);" onclick="$(this).closest('.pipeline-item').remove()"><i class="ti ti-trash text-danger"></i>Delete</a>
									</div>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<h5 class="form-title">Access</h5>
							<div class="d-flex flex-wrap access-item nav">
								<div class="radio-btn" data-bs-toggle="tab" data-bs-target="#all">
									<input type="radio" class="status-radio" id="all" name="access" value="all" checked="">
									<label for="all">All</label>
								</div>
								<div class="radio-btn" data-bs-toggle="tab" data-bs-target="#select-person">
									<input type="radio" class="status-radio" id="select" name="access" value="specific">
									<label for="select">Select Person</label>
								</div>
							</div>
							<div class="tab-content mb-3">
								<div class="tab-pane fade" id="select-person">
									<div class="access-wrapper">
                                        <select class="form-control select2" multiple="multiple" name="access_users[]" id="pipeline_access_users" style="width: 100%;">
                                        </select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary" id="btnCreatePipeline">Create</button>
					</div>
				</form>
			</div>

		</div>
		<!-- /Add New Pipeline -->

		<!-- Delete Stage -->
		<div class="modal fade" id="delete_stage" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Stage?</h4>
							<p class="mb-0">Are you sure you want to remove <br> stage you selected.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="deals.html" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Stage -->

		<!-- Add New Stage -->
		<div class="modal custom-modal fade" id="add_stage" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add New Stage</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form id="addStageForm">
							<div class="mb-3">
								<label class="col-form-label">Stage Name *</label>
								<input type="text" class="form-control" name="stage_name" id="new_stage_name" required>
							</div>
							<div class="modal-btn text-end">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-danger">Save Changes</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Add New Stage -->

		<!-- Edit Stage -->
		<div class="modal custom-modal fade" id="edit_stage" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Edit Stage</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="deals.html">
							<div class="mb-3">
								<label class="col-form-label">Stage Name *</label>
								<input type="text" class="form-control" value="Inpipeline">
							</div>
							<div class="modal-btn text-end">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-danger">Save Changes</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Edit Stage -->

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
						<form action="deals.html">
							<div class="mb-3">
								<label class="col-form-label">View Name</label>
								<input type="text" class="form-control" name="name">
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
@section('scripts')
<script>
    $(document).ready(function() {
        const pageIdentifier = 'deals-list';
        
        // Load column visibility preferences from database
        function loadColumnPreferences() {
            $.ajax({
                url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug") ?? ""]) }}',
                type: 'GET',
                data: { page_identifier: pageIdentifier },
                async: false,
                success: function(response) {
                    if (response.success && response.data && Array.isArray(response.data) && response.data.length > 0) {
                        response.data.forEach(pref => {
                            const prefIndex = parseInt(pref.index);
                            const visible = (typeof pref.visible === 'string') ? (pref.visible === 'true' || pref.visible === '1') : Boolean(pref.visible);
                            $('.column-toggle[data-column="'+prefIndex+'"]').prop('checked', visible);
                        });
                    }
                }
            });
        }
        
        // Save column visibility preferences to database
        function saveColumnPreferences() {
            let columnDefinitions = [];
            $('.column-toggle').each(function() {
                columnDefinitions.push({
                    index: parseInt($(this).data('column')),
                    visible: $(this).is(':checked')
                });
            });
            $.ajax({
                url: '{{ route("settings.column.preferences.save", ["box_slug" => request()->route("box_slug") ?? ""]) }}',
                type: 'POST',
                data: {
                    page_identifier: pageIdentifier,
                    column_preferences: columnDefinitions,
                    _token: '{{ csrf_token() }}'
                }
            });
        }

        // Run load before datatable init
        loadColumnPreferences();

        // Initialize DataTable
        if($.fn.DataTable.isDataTable('#deal_list_table')) {
            $('#deal_list_table').DataTable().destroy();
        }

        $('#deal_list_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('manage_users.deals.json', ['box_slug' => request()->route('box_slug') ?? '']) }}",
                data: function(d) {
                    let dateRange = $('.bookingrange').val();
                    if(dateRange && dateRange.includes(' - ')) {
                        let dates = dateRange.split(' - ');
                        d.start_date = dates[0];
                        d.end_date = dates[1];
                    }
                }
            },
            columns: [
                {data: 'id', name: 'id', orderable: false, searchable: false, render: function(data) {
                    return '<label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label>';
                }},
                {data: 'id', name: 'id', orderable: false, searchable: false, render: function() { return ''; }},
                {data: 'deal_name', name: 'name'},
                {data: 'pipeline_name', name: 'pipeline.name', orderable: false},
                {data: 'stage_name', name: 'stage'},
                {data: 'formatted_deal_value', name: 'deal_value'},
                {data: 'contact_name', name: 'contact.name', orderable: false},
                {data: 'project_name', name: 'project.name', orderable: false},
                {data: 'period_name', name: 'dealPeriod.name', orderable: false},
                {data: 'source_name', name: 'dealSource.name', orderable: false},
                {data: 'priority_name', name: 'dealPriority.name', orderable: false},
                {data: 'formatted_tags', name: 'tags', orderable: false},
                {data: 'due_date', name: 'due_date'},
                {data: 'expected_close_date', name: 'expected_close_date'},
                {data: 'creator_name', name: 'creator.name', orderable: false},
                {data: 'status_id', name: 'status_id', render: function(data, type, row) {
                    return row.deal_status ? '<span class="badge bg-inverse-success">' + row.deal_status.name + '</span>' : '<span class="badge bg-inverse-warning">Open</span>';
                }},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end'}
            ],
            initComplete: function(settings, json) {
                let table = this.api();
                $('.column-toggle').each(function() {
                    let columnIdx = $(this).data('column');
                    let isVisible = $(this).is(':checked');
                    table.column(columnIdx).visible(isVisible, false);
                });
                table.columns.adjust().draw(false);
            }
        });

        // Load Options via AJAX for Add Deal Modal
        function loadAddDealOptions() {
            let url = "{{ route('manage_users.deals.form_options', ['box_slug' => request()->route('box_slug') ?? '']) }}";
            $.get(url, function(res) {
                if(res.success) {
                    let populate = (id, data, defaultOption) => {
                        let html = `<option value="">${defaultOption}</option>`;
                        data.forEach(item => {
                            html += `<option value="${item.id}">${item.name}</option>`;
                        });
                        $(`#${id}`).html(html).trigger('change');
                    };

                    populate('addDealPipeline', res.pipelines, 'Choose Pipeline');
                    populate('addDealStatus', res.statuses, 'Choose Status');
                    populate('addDealProject', res.projects, 'Choose Project');
                    populate('addDealContact', res.contacts, 'Choose Contact');
                    populate('addDealSource', res.sources, 'Choose Source');
                    populate('addDealPriority', res.priorities, 'Choose Priority');
                    populate('addDealPeriod', res.periods, 'Choose Period');
                    
                    populate('editDealPipeline', res.pipelines, 'Choose Pipeline');
                    populate('editDealStatus', res.statuses, 'Choose Status');
                    populate('editDealProject', res.projects, 'Choose Project');
                    populate('editDealContact', res.contacts, 'Choose Contact');
                    populate('editDealSource', res.sources, 'Choose Source');
                    populate('editDealPriority', res.priorities, 'Choose Priority');
                    populate('editDealPeriod', res.periods, 'Choose Period');
                    
                    let tagHtml = '';
                    res.tags.forEach(item => { tagHtml += `<option value="${item.id}">${item.name}</option>`; });
                    $('#addDealTags').html(tagHtml).trigger('change');
                    $('#editDealTags').html(tagHtml).trigger('change');

                    let userHtml = '';
                    res.users.forEach(item => { userHtml += `<option value="${item.id}">${item.name}</option>`; });
                    $('#addAssignees').html(userHtml).trigger('change');
                    $('#editAssignees').html(userHtml).trigger('change');
                    $('#pipeline_access_users').html(userHtml).trigger('change');
                }
            });
        }

        let ajaxSettings = {
            url: "{{ route('manage_users.deals.leads_search', ['box_slug' => request()->route('box_slug') ?? '']) }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        };

        $('#addDealLead').select2({
            ajax: ajaxSettings,
            placeholder: 'Search for a Lead',
            minimumInputLength: 0,
            dropdownParent: $('#offcanvas_add')
        });

        $('#editDealLead').select2({
            ajax: ajaxSettings,
            placeholder: 'Search for a Lead',
            minimumInputLength: 0,
            dropdownParent: $('#offcanvas_edit')
        });
        
        loadAddDealOptions();

        // Handle Add Stage Form
        $('#addStageForm').on('submit', function(e) {
            e.preventDefault();
            let stageName = $('#new_stage_name').val().trim();
            if(stageName) {
                let html = `
                <div class="pipeline-item">
                    <input type="hidden" name="stages[]" value="${stageName}">
                    <p><i class="ti ti-grip-vertical"></i> ${stageName}</p>
                    <div class="action-pipeline">
                        <a href="javascript:void(0);" onclick="$(this).closest('.pipeline-item').remove()"><i class="ti ti-trash text-danger"></i>Delete</a>
                    </div>
                </div>`;
                $('#pipeline-stages-container').append(html);
                $('#add_stage').modal('hide');
                $('#new_stage_name').val('');
            }
        });

        // Handle Add Pipeline Form
        $('#addPipelineForm').on('submit', function(e) {
            e.preventDefault();
            let btn = $('#btnCreatePipeline');
            btn.prop('disabled', true).text('Creating...');
            
            $.ajax({
                url: "{{ route('manage_users.pipelines.store', ['box_slug' => request()->route('box_slug') ?? '']) }}",
                type: 'POST',
                data: $(this).serialize() + '&_token={{ csrf_token() }}',
                success: function(res) {
                    if(res.success) {
                        $('#offcanvas_pipeline').offcanvas('hide');
                        $('#addPipelineForm')[0].reset();
                        showToast('Pipeline created successfully!', 'success');
                        loadAddDealOptions(); // Reload pipelines list
                    } else {
                        showToast(res.message || 'Error creating pipeline.', 'danger');
                    }
                },
                error: function(err) {
                    showToast('An error occurred. Check console for details.', 'danger');
                    console.error(err);
                },
                complete: function() {
                    btn.prop('disabled', false).text('Create');
                }
            });
        });

        // Edit Deal Handler
        $('#deal_list_table').on('click', '.edit-btn', function(e) {
            e.preventDefault();
            let rowData = $('#deal_list_table').DataTable().row($(this).closest('tr')).data();
            
            $('#editDealId').val(rowData.id);
            $('#editDealName').val(rowData.name);
            $('#editDealValue').val(rowData.deal_value);
            $('#editDealPeriodValue').val(rowData.period_value);
            $('#editDealDueDate').val(rowData.due_date);
            $('#editDealExpectedCloseDate').val(rowData.expected_close_date);
            
            $('#editDealPipeline').val(rowData.pipeline_id).trigger('change');
            $('#editDealStatus').val(rowData.status_id).trigger('change');
            $('#editDealProject').val(rowData.project_id).trigger('change');
            $('#editDealContact').val(rowData.contact_id).trigger('change');
            if(rowData.lead) {
                let leadOption = new Option(rowData.lead.name + (rowData.lead.phone ? ' (' + rowData.lead.phone + ')' : ''), rowData.lead.id, true, true);
                $('#editDealLead').append(leadOption).trigger('change');
            } else {
                $('#editDealLead').val(null).trigger('change');
            }
            $('#editDealSource').val(rowData.source_id).trigger('change');
            $('#editDealPriority').val(rowData.priority_id).trigger('change');
            $('#editDealPeriod').val(rowData.period_id).trigger('change');
            
            if(rowData.tags) {
                let tags = rowData.tags;
                if(typeof tags === 'string') try { tags = JSON.parse(tags); } catch(e) {}
                $('#editDealTags').val(tags).trigger('change');
            }
            if(rowData.assignees) {
                let assignees = rowData.assignees;
                if(typeof assignees === 'string') try { assignees = JSON.parse(assignees); } catch(e) {}
                $('#editAssignees').val(assignees).trigger('change');
            }
            
            $('#offcanvas_edit').offcanvas('show');
        });

        // Handle Edit Deal Form
        $('#editDealForm').on('submit', function(e) {
            e.preventDefault();
            let btn = $(this).find('button[type="submit"]');
            let originalText = btn.text();
            btn.prop('disabled', true).text('Saving...');
            
            let dealId = $('#editDealId').val();
            let url = "{{ route('manage_users.deals.update', ['box_slug' => request()->route('box_slug') ?? '', 'id' => ':id']) }}".replace(':id', dealId);

            $.ajax({
                url: url,
                type: 'PUT',
                data: $(this).serialize() + '&_token={{ csrf_token() }}',
                success: function(res) {
                    if(res.success) {
                        $('#offcanvas_edit').offcanvas('hide');
                        showToast('Deal updated successfully!', 'success');
                        $('#deal_list_table').DataTable().ajax.reload(null, false);
                    } else {
                        showToast(res.message || 'Error updating deal.', 'danger');
                    }
                },
                error: function(err) {
                    showToast('An error occurred. Check console for details.', 'danger');
                    console.error(err);
                },
                complete: function() {
                    btn.prop('disabled', false).text(originalText);
                }
            });
        });
        
        // Handle Add Deal Form
        $('#addDealForm').on('submit', function(e) {
            e.preventDefault();
            let btn = $(this).find('button[type="submit"]');
            let originalText = btn.text();
            btn.prop('disabled', true).text('Creating...');
            
            $.ajax({
                url: "{{ route('manage_users.deals.store', ['box_slug' => request()->route('box_slug') ?? '']) }}",
                type: 'POST',
                data: $(this).serialize() + '&_token={{ csrf_token() }}',
                success: function(res) {
                    if(res.success) {
                        $('#offcanvas_add').offcanvas('hide');
                        $('#addDealForm')[0].reset();
                        $('#addDealForm .select2').val('').trigger('change');
                        showToast('Deal created successfully!', 'success');
                        $('#deal_list_table').DataTable().ajax.reload(null, false);
                    } else {
                        showToast(res.message || 'Error creating deal.', 'danger');
                    }
                },
                error: function(err) {
                    showToast('An error occurred. Check console for details.', 'danger');
                    console.error(err);
                },
                complete: function() {
                    btn.prop('disabled', false).text(originalText);
                }
            });
        });

        // Global Search
        $('#customDealSearch').on('keyup', function() {
            $('#deal_list_table').DataTable().search(this.value).draw();
        });

        // Date Range filter trigger
        $('.bookingrange').on('apply.daterangepicker', function(ev, picker) {
            $('#deal_list_table').DataTable().ajax.reload();
        });
        $('.bookingrange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#deal_list_table').DataTable().ajax.reload();
        });

        // Sort filter
        $(document).on('click', '.sort-item', function(e) {
            e.preventDefault();
            let sortType = $(this).data('sort');
            let table = $('#deal_list_table').DataTable();
            if(sortType === 'asc') {
                table.order([2, 'asc']).draw();
            } else if(sortType === 'desc') {
                table.order([2, 'desc']).draw();
            } else if(sortType === 'recently-viewed' || sortType === 'recently-added') {
                table.order([0, 'desc']).draw(); // Assuming col 0 is ID (or created_at proxy)
            }
        });

        // Manage Columns
        $(document).on('change', '.column-toggle', function() {
            let columnIdx = $(this).data('column');
            let column = $('#deal_list_table').DataTable().column(columnIdx);
            column.visible($(this).is(':checked'));
            saveColumnPreferences();
        });

        // Export Deals
        $(document).on('click', '.export-deals-btn', function(e) {
            e.preventDefault();
            let type = $(this).data('type');
            let dateRange = $('.bookingrange').val();
            let startDate = '';
            let endDate = '';
            
            if (dateRange && dateRange.includes(' - ')) {
                let dates = dateRange.split(' - ');
                startDate = dates[0];
                endDate = dates[1];
            }

            let url = "{{ route('manage_users.deals.export', ['box_slug' => request()->route('box_slug') ?? '']) }}?type=" + type;
            if (startDate && endDate) {
                url += "&start_date=" + encodeURIComponent(startDate) + "&end_date=" + encodeURIComponent(endDate);
            }
            window.location.href = url;
        });
        
    });
</script>
@endsection
@endsection
