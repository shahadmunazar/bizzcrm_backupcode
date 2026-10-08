@extends('layouts.main')
@section('content')
@section('title','Companies')


<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

                <!-- Dynamic Toast -->
                <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                    <div id="toastMessage" class="toast align-items-center text-white bg-success border-0" role="alert"
                        aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body" id="toastBody">
                                Success!
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                                aria-label="Close"></button>
                        </div>
                    </div>
                </div>

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									<h4 class="page-title">Companies<span class="count-title">{{ $totalCompanies ?? 0 }}</span></h4>
								</div>
								<div class="col-4 text-end">
									<div class="head-icons">
										<a href="companies.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
									</div>
								</div>
							</div>
						</div>
						@include('settings.partials.nav')

						<div class="row">
							<div class="col-xl-3 col-lg-12 theiaStickySidebar">
								@include('settings.partials.sidebar')
							</div>
							<div class="col-xl-9 col-lg-12">
								<div class="card ">
							<div class="card-header">
								<!-- Search -->
								<div class="row align-items-center">
									<div class="col-sm-4">
										<div class="icon-form mb-3 mb-sm-0">
											<span class="form-icon"><i class="ti ti-search"></i></span>
											<input type="text" id="custom-search-input" class="form-control" placeholder="Search Companies">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
											<div class="dropdown me-2">
												<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
												<div class="dropdown-menu  dropdown-menu-end">
													<ul>
														<li>
															<a href="javascript:void(0);" id="export-pdf" class="dropdown-item"><i class="ti ti-file-type-pdf text-danger me-1"></i>Export
																as PDF</a>
														</li>
														<li>
															<a href="javascript:void(0);" id="export-excel" class="dropdown-item"><i class="ti ti-file-type-xls text-green me-1"></i>Export
																as Excel </a>
														</li>
													</ul>
												</div>
											</div>
											<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-square-rounded-plus me-2"></i>Add Company</a>
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
											<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-sort-ascending-2 me-2"></i>Sort </a>
											<div class="dropdown-menu  dropdown-menu-start">
												<ul>
													<li>
														<a href="javascript:void(0);" class="dropdown-item">
													<li>
														<a href="javascript:void(0);" id="sort-asc" class="dropdown-item">
															<i class="ti ti-circle-chevron-right me-1"></i>Ascending
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" id="sort-desc" class="dropdown-item">
															<i class="ti ti-circle-chevron-right me-1"></i>Descending
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" id="sort-recent-viewed" class="dropdown-item">
															<i class="ti ti-circle-chevron-right me-1"></i>Recently
															Viewed
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" id="sort-recent" class="dropdown-item">
															<i class="ti ti-circle-chevron-right me-1"></i>Recently
															Added
														</a>
													</li>
												</ul>
											</div>
										</div>
										<div class="icon-form">
											<span class="form-icon"><i class="ti ti-calendar"></i></span>
											<input type="text" id="custom-date-range" class="form-control bookingrange" placeholder="">
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
															<input type="checkbox" id="col-name" class="check">
															<label for="col-name" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Phone</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-phone" class="check">
															<label for="col-phone" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Email</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-email" class="check">
															<label for="col-email" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Tags</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-tag" class="check">
															<label for="col-tag" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Location</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-loc" class="check">
															<label for="col-loc" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Rating</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-rate" class="check">
															<label for="col-rate" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Owner</p>
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
<!-- 										<div class="form-sorts dropdown me-2">
											<a href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-filter-share"></i>Filter</a>
											<div class="filter-dropdown-menu dropdown-menu  dropdown-menu-md-end p-3">
												<div class="filter-set-view">
													<div class="filter-set-head">
														<h4><i class="ti ti-filter-share"></i>Filter</h4>
													</div>
													<div class="accordion" id="accordionExample">
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">Country</a>
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
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					India
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					USA
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					France
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					United Kingdom
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					UAE
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Italy
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Martin Lewis
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Germany
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
																					Active
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Inactive
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
																<a href="companies.html" class="btn btn-primary">Filter</a>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div> -->
										<div class="view-icons">
											<a href="{{ box_route('settings.general.companies') }}" class="active"><i class="ti ti-list-tree"></i></a>
											<a href="{{ box_route('settings.general.companies_grid') }}"><i class="ti ti-grid-dots"></i></a>
										</div>
									</div>
								</div>
								<!-- /Filter -->

								<!-- Contact List -->
								<div class="table-responsive custom-table">
									<table class="table" id="companieslist">
										<thead class="thead-light">
											<tr>
												<th class="no-sort">
													<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
												</th>
												<th class="no-sort"></th>
												<th>Name</th>
												<th>Phone</th>
												<th>Email</th>
												<th>Tags</th>
												<th>Location</th>
												<th>Rating</th>
												<th>Owner</th>
												<th>Contact </th>
												<th>Status</th>
												<th class="text-end">Action</th>
                                                <th class="d-none">Created At</th>
											</tr>
										</thead>
										<tbody id="companies-table-body">

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

	</div>
</div>
		<!-- /Page Wrapper -->

		@include("partials.add_company_offcanvas")
		<!-- Edit Company -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Edit Company</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="companies.html">
					<div class="accordion" id="main_accordion">
						<!-- Basic Info -->
						<div class="accordion-item rounded mb-3">
							<div class="accordion-header">
								<a href="#" class="accordion-button accordion-custom-button bg-white rounded fw-medium text-dark" data-bs-toggle="collapse" data-bs-target="#basic">
									<span class="avatar avatar-md rounded text-dark border me-2"><i class="ti ti-user-plus fs-20"></i></span>
									Basic Info
								</a>
							</div>
							<div class="accordion-collapse collapse show" id="basic" data-bs-parent="#main_accordion">
								<div class="accordion-body border-top">
									<div class="row">
										<div class="col-md-12">
											<div class="mb-3">
												<div class="profile-upload">
													<div class="profile-upload-img">
														<span><i class="ti ti-photo"></i></span>
														<img src="assets/img/profiles/avatar-20.jpg" alt="img" class="preview1">
														<button type="button" class="profile-remove">
															<i class="ti ti-x"></i>
														</button>
													</div>
													<div class="profile-upload-content">
														<label class="profile-upload-btn">
															<i class="ti ti-file-broken"></i> Upload File
															<input type="file" class="input-img">
														</label>
														<p>JPG, GIF or PNG. Max size of 800K</p>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="mb-3">
												<label class="col-form-label">Company Name</label>
												<input type="text" class="form-control" value="NovaWave LLC">
											</div>
										</div>
										<div class="col-md-12">
											<div class="mb-3">
												<div class="d-flex justify-content-between align-items-center">
													<label class="col-form-label">Email <span class="text-danger">*</span></label>
													<div class="status-toggle small-toggle-btn d-flex align-items-center">
														<span class="me-2 label-text">Email Opt Out</span>
														<input type="checkbox" id="user" class="check" checked="">
														<label for="user" class="checktoggle"></label>
													</div>
												</div>
												<input type="text" class="form-control" value="robertson@example.com">
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">Phone 1 <span class="text-danger">*</span></label>
												<input type="text" class="form-control" value="1234567890">
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">Phone 2</label>
												<input type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">Fax</label>
												<input type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">Website</label>
												<input type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">Ratings</label>
												<div class="icon-form-end">
													<span class="form-icon"><i class="ti ti-star"></i></span>
													<input type="text" class="form-control" placeholder="4.2">
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="fmb-3">
												<label class="col-form-label">Owner</label>
												<select class="select-img">
													<option data-image="assets/img/profiles/avatar-14.jpg" selected="">
														Hendry Milner</option>
													<option data-image="assets/img/profiles/avatar-15.jpg">Guilory Berggren
													</option>
													<option data-image="assets/img/profiles/avatar-16.jpg">Jami Carlile</option>
													<option data-image="assets/img/profiles/avatar-13.jpg">Theresa Nelson
													</option>
													<option data-image="assets/img/profiles/avatar-24.jpg">Smith Cooper
													</option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">Tags </label>
												<input class="input-tags form-control" type="text" data-role="tagsinput" name="Label" value="Collab">
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<div class="d-flex align-items-center justify-content-between">
													<label class="col-form-label">Deals</label>
													<a href="#" class="label-add" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_2"><i class="ti ti-square-rounded-plus"></i>Add New</a>
												</div>
												<select class="select2">
													<option>Choose</option>
													<option selected="">Collins</option>
													<option>Konopelski</option>
													<option>Adams</option>
													<option>Schumm</option>
													<option>Wisozk</option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">Source <span class="text-danger">*</span></label>
												<select class="select2">
													<option>Choose</option>
													<option>Phone Calls</option>
													<option>Social Media</option>
													<option>Referral Sites</option>
													<option>Web Analytics</option>
													<option>Previous Purchases</option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">Industry <span class="text-danger">*</span></label>
												<select class="select">
													<option>Choose</option>
													<option>Retail Industry</option>
													<option>Banking</option>
													<option>Hotels</option>
													<option>Financial Services</option>
													<option>Insurance</option>
												</select>
											</div>
										</div>
										<div class="col-md-12">
											<div class="mb-3">
												<label class="col-form-label">Contacts</label>
												<select class="multiple-img" multiple="multiple">
													<option data-image="assets/img/profiles/avatar-19.jpg">Darlee
														Robertson</option>
													<option data-image="assets/img/profiles/avatar-20.jpg" selected="">
														Sharon Roy</option>
													<option data-image="assets/img/profiles/avatar-21.jpg">Vaughan Lewis
													</option>
													<option data-image="assets/img/profiles/avatar-23.jpg">Jessica Louise
													</option>
													<option data-image="assets/img/profiles/avatar-16.jpg">Carol Thomas
													</option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">Currency <span class="text-danger">*</span></label>
												<select class="select">
													<option>Choose</option>
													<option selected="">Dollar</option>
													<option>Euro</option>
													<option>Pound</option>
													<option>Rupee</option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">Language <span class="text-danger">*</span></label>
												<select class="select">
													<option>Choose</option>
													<option selected="">English</option>
													<option>Arabic</option>
													<option>French</option>
													<option>German</option>
												</select>
											</div>
										</div>
										<div class="col-md-12">
											<div class="mb-0">
												<label class="col-form-label">Description <span class="text-danger">*</span></label>
												<textarea class="form-control" rows="5"></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- /Basic Info -->

						<!-- Address Info -->
						<div class="accordion-item border-top rounded mb-3">
							<div class="accordion-header">
								<a href="#" class="accordion-button accordion-custom-button rounded bg-white fw-medium text-dark" data-bs-toggle="collapse" data-bs-target="#address">
									<span class="avatar avatar-md rounded text-dark border me-2"><i class="ti ti-map-pin-cog fs-20"></i></span>
									Address Info
								</a>
							</div>
							<div class="accordion-collapse collapse" id="address" data-bs-parent="#main_accordion">
								<div class="accordion-body border-top">
									<div class="row">
										<div class="col-md-12">
											<div class="mb-3">
												<label class="col-form-label">Street Address </label>
												<input type="text" class="form-control" value="87 Griffin Street">
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">Country</label>
												<select class="select">
													<option>Choose</option>
													<option selected="">USA</option>
													<option>Canada</option>
													<option>Germany</option>
													<option>France</option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">State / Province </label>
												<select class="select">
													<option>Choose</option>
													<option selected="">California</option>
													<option>New York</option>
													<option>Texas</option>
													<option>Florida</option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3 mb-md-0">
												<label class="col-form-label">City </label>
												<select class="select">
													<option>Choose</option>
													<option selected="">Los Angeles</option>
													<option>San Diego</option>
													<option>Fresno</option>
													<option>San Francisco</option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-0">
												<label class="col-form-label">Zipcode </label>
												<input type="text" class="form-control" value="90001">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- /Address Info -->

						<!-- Social Profile -->
						<div class="accordion-item border-top rounded mb-3">
							<div class="accordion-header">
								<a href="#" class="accordion-button accordion-custom-button rounded bg-white fw-medium text-dark" data-bs-toggle="collapse" data-bs-target="#social">
									<span class="avatar avatar-md rounded text-dark border me-2"><i class="ti ti-social fs-20"></i></span>
									Social Profile
								</a>
							</div>
							<div class="accordion-collapse collapse" id="social" data-bs-parent="#main_accordion">
								<div class="accordion-body border-top">
									<div class="row">
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">Facebook</label>
												<input type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">Skype </label>
												<input type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">Linkedin </label>
												<input type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">Twitter</label>
												<input type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3 mb-md-0">
												<label class="col-form-label">Whatsapp</label>
												<input type="text" class="form-control" value="1234567890">
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-0">
												<label class="col-form-label">Instagram</label>
												<input type="text" class="form-control">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- /Social Profile -->

						<!-- Access -->
						<div class="accordion-item border-top rounded mb-3">
							<div class="accordion-header">
								<a href="#" class="accordion-button accordion-custom-button rounded bg-white fw-medium text-dark" data-bs-toggle="collapse" data-bs-target="#access-info">
									<span class="avatar avatar-md rounded text-dark border me-2"><i class="ti ti-accessible fs-20"></i></span>
									Access
								</a>
							</div>
							<div class="accordion-collapse collapse" id="access-info" data-bs-parent="#main_accordion">
								<div class="accordion-body border-top">
									<div class="row">
										<div class="col-md-12">
											<div class="mb-3">
												<label class="col-form-label">Visibility</label>
												<div class="d-flex flex-wrap">
													<div class="me-2">
														<input type="radio" class="status-radio" id="public" name="visible">
														<label for="public">Public</label>
													</div>
													<div class="me-2">
														<input type="radio" class="status-radio" id="private" name="visible">
														<label for="private">Private</label>
													</div>
													<div data-bs-toggle="modal" data-bs-target="#access_view">
														<input type="radio" class="status-radio" id="people" name="visible">
														<label for="people">Select People</label>
													</div>
												</div>
											</div>
											<div class="mb-0">
												<label class="col-form-label">Status</label>
												<div class="d-flex flex-wrap">
													<div class="me-2">
														<input type="radio" class="status-radio" id="active" name="status" checked="">
														<label for="active">Active</label>
													</div>
													<div>
														<input type="radio" class="status-radio" id="inactive" name="status">
														<label for="inactive">Inactive</label>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- /Access -->
					</div>
					<div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="button" class="btn btn-primary">Save Changes</button>
					</div>
				</form>
			</div>
		</div>
		<!-- /Edit Company -->

		<!-- Delete Company -->
		<div class="modal fade" id="delete_contact" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Company?</h4>
							<p class="mb-0">Are you sure you want to remove <br> company you selected.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="companies.html" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Company -->

		<!-- Create Company -->
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
								<i class="ti ti-building"></i>
							</div>
							<h3>Company Created Successfully!!!</h3>
							<p>View the details of company, created</p>
							<div class="col-lg-12 text-center modal-btn">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<a href="company-details.html" class="btn btn-primary">View Details</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Create Company -->

		<!-- Create Deal -->
		<div class="modal custom-modal fade" id="create_success_1" role="dialog">
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
						<form action="companies.html">
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

		<!-- Access -->
		<div class="modal fade" id="access_view" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Access For</h5>
						<button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form action="companies.html">
						<div class="modal-body">
							<div class="icon-form mb-3">
								<span class="form-icon"><i class="ti ti-search"></i></span>
								<input type="text" class="form-control" placeholder="Search">
							</div>
							<div class="access-wrap mb-0">
								<ul>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<img src="assets/img/profiles/avatar-19.jpg" alt="">
												<a href="#">Darlee Robertson</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<img src="assets/img/profiles/avatar-20.jpg" alt="">
												<a href="#">Sharon Roy</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<img src="assets/img/profiles/avatar-21.jpg" alt="">
												<a href="#">Vaughan Lewis</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<img src="assets/img/profiles/avatar-01.jpg" alt="">
												<a href="#">Jessica Louise</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<img src="assets/img/profiles/avatar-16.jpg" alt="">
												<a href="#">Carol Thomas</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<img src="assets/img/profiles/avatar-22.jpg" alt="">
												<a href="#">Dawn Mercha</a>
											</span>
										</label>
									</li>
								</ul>
							</div>
						</div>
						<div class="modal-footer">
							<div class="d-flex align-items-center justify-content-end m-0">
								<button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
								<button type="submit" class="btn btn-primary">Confirm</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /Access -->

		<!-- Add New Deals -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add_2">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Add New Deals</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="contacts.html">
					<div class="row">
						<div class="col-md-12">
							<div class="mb-3">
								<label class="col-form-label">Deal Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<div class="d-flex align-items-center justify-content-between">
									<label class="col-form-label">Pipeine <span class="text-danger">*</span></label>
								</div>
								<select class="select2">
									<option>Choose</option>
									<option>Sales</option>
									<option>Marketing</option>
									<option>Calls</option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Status <span class="text-danger">*</span></label>
								<select class="select2">
									<option>Choose</option>
									<option>Open</option>
									<option>Lost</option>
									<option>Won</option>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Deal Value<span class="text-danger"> *</span></label>
								<input class="form-control" type="text">
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
								<select class="select">
									<option>Choose</option>
									<option>Days</option>
									<option>Month</option>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Period Value <span class="text-danger">*</span></label>
								<input class="form-control" type="text">
							</div>
						</div>
						<div class="col-md-12">
							<div class="mb-3">
								<label class="col-form-label">Contact <span class="text-danger">*</span></label>
								<select class="multiple-img" multiple="multiple">
									<option data-image="assets/img/profiles/avatar-19.jpg" selected="">Darlee Robertson
									</option>
									<option data-image="assets/img/profiles/avatar-20.jpg">Sharon Roy</option>
									<option data-image="assets/img/profiles/avatar-21.jpg">Vaughan Lewis</option>
									<option data-image="assets/img/profiles/avatar-23.jpg">Jessica Louise</option>
									<option data-image="assets/img/profiles/avatar-16.jpg">Carol Thomas</option>
								</select>
							</div>
							<div class="mb-3">
								<label class="col-form-label">Project <span class="text-danger">*</span></label>
								<select class="select" multiple="multiple">
									<option selected="">Devops Design</option>
									<option selected="">MargrateDesign</option>
									<option selected="">UI for Chat</option>
									<option>Web Chat</option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Due Date <span class="text-danger">*</span></label>
								<div class="icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="form-control datetimepicker" placeholder="">
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Expected Closing Date <span class="text-danger">*</span></label>
								<div class="icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="form-control datetimepicker" placeholder="">
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="mb-3">
								<label class="col-form-label">Assignee <span class="text-danger">*</span></label>
								<select class="multiple-img" multiple="multiple">
									<option data-image="assets/img/profiles/avatar-19.jpg">Darlee Robertson</option>
									<option data-image="assets/img/profiles/avatar-20.jpg" selected="">Sharon Roy</option>
									<option data-image="assets/img/profiles/avatar-21.jpg">Vaughan Lewis</option>
									<option data-image="assets/img/profiles/avatar-23.jpg">Jessica Louise</option>
									<option data-image="assets/img/profiles/avatar-16.jpg">Carol Thomas</option>
								</select>
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
								<select class="select">
									<option>Select</option>
									<option>Google</option>
									<option>Social Media</option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Tags <span class="text-danger">*</span></label>
								<input class="input-tags form-control" type="text" data-role="tagsinput" name="Label" value="Collab, Rated">
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="col-form-label">Priority <span class="text-danger">*</span></label>
								<select class="select">
									<option>Select</option>
									<option>High</option>
									<option>Low</option>
									<option>Medium</option>
								</select>
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
						<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create_success_1">Create</button>
					</div>
				</form>
			</div>
		</div>
		<!-- /Add New Deals -->

    <!-- Edit Company Modal - Reusing Add Modal for simplicity, or we can use the Edit one if we updated it. 
         For now we will use the Add Modal logic for both by populating it. 
         We are ignoring the static Edit Modal in HTML 
    -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Routes for AJAX
            const routes = {
                states: "{{ route('activities.states', ['box_slug' => request()->route('box_slug')]) }}",
                cities: "{{ route('activities.cities', ['box_slug' => request()->route('box_slug')]) }}"
            };

            loadCompanies();
            loadStates(); // Load states on page load (India - 101)

            // Clear modal on open
            $('#offcanvas_add').on('show.bs.offcanvas', function () {
                // Init Select2 inside offcanvas to handle z-index and focus
                $('#bill_state_id, #bill_city_id').select2({
                    dropdownParent: $('#offcanvas_add'),
                    placeholder: "--Select--",
                    allowClear: true,
                    width: '100%'
                });

                if(!$(this).data('is-edit')) {
                    $('#add-company-form')[0].reset();
                    $('#btn-save-company').text('Create');
                    $('#offcanvas_add .fw-semibold').text('Add New Company');
                    $('#add-company-form').removeData('id');
                    
                    // Reset selects
                    $('#bill_state_id').val('').trigger('change');
                    $('#bill_city_id').empty().append('<option value="">--Select--</option>').trigger('change');
                }
                $(this).data('is-edit', false);
            });

            // Load Companies
            function loadCompanies() {
                $.ajax({
                    url: "{{ route('settings.general.companies.data', ['box_slug' => request()->route('box_slug')]) }}",
                    type: "GET",
                    success: function(response) {
                        let table = $('#companieslist');
                        
                        // If DataTable exists, destroy it but keep the DOM for repopulation
                        if ($.fn.DataTable.isDataTable('#companieslist')) {
                            table.DataTable().destroy();
                        }

                        let tbody = $('#companies-table-body');
                        tbody.empty();
                        if(response.data.length > 0) {
                            $.each(response.data, function(index, company) {
                                let statusBadge = company.status === 'Enable' 
                                    ? '<span class="badge badge-pill badge-status bg-success">Active</span>' 
                                    : '<span class="badge badge-pill badge-status bg-danger">Inactive</span>';
                                
                                let tr = `
                                    <tr>
                                        <td class="no-sort">
                                            <label class="checkboxs"><input type="checkbox" name="ids[]" value="${company.id}"><span class="checkmarks"></span></label>
                                        </td>
                                        <td class="no-sort"></td>
                                        <td>${company.bill_name || ''}</td>
                                        <td>${company.bill_phone || ''}</td>
                                        <td>${company.email || ''}</td>
                                        <td></td> <!-- Tags -->
                                        <td>${company.bill_address || ''}</td>
                                        <td></td> <!-- Rating -->
                                        <td>${company.creator ? company.creator.name : ''}</td>
                                        <td></td> <!-- Contact -->
                                        <td>${statusBadge}</td>
                                        <td class="text-end">
                                            <div class="dropdown table-action">
                                                <a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item edit-company" href="#" data-id="${company.id}" data-json='${JSON.stringify(company)}'><i class="fa-solid fa-pencil text-blue"></i> Edit</a>
                                                    <a class="dropdown-item delete-company" href="#" data-id="${company.id}"><i class="fa-regular fa-trash-can text-danger"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="d-none">${company.created_at || ''}</td>
                                    </tr>
                                `;
                                tbody.append(tr);
                            });
                        } else {
                            tbody.append('<tr><td colspan="13" class="text-center">No companies found</td></tr>');
                        }

                        // Re-initialize DataTable
                        window.companiesTable = table.DataTable({
                            "bFilter": true,
                            "bInfo": false,
                            "autoWidth": true,
                            "ordering": true,
                            "language": {
                                search: ' ',
                                sLengthMenu: '_MENU_',
                                searchPlaceholder: "Search",
                                info: "_START_ - _END_ of _TOTAL_ items",
                                "lengthMenu": "Show _MENU_ entries",
                                paginate: {
                                    next: 'Next <i class=" fa fa-angle-right"></i> ',
                                    previous: '<i class="fa fa-angle-left"></i> Prev '
                                },
                            },
                            initComplete: (settings, json)=>{
                                $('.dataTables_filter').hide();
                                $('.dataTables_paginate').appendTo('.datatable-paginate');
                                $('.dataTables_length').appendTo('.datatable-length');
                            },
                        });

                        // Column visibility mapping
                        const colMap = {
                            'col-name': 2,
                            'col-phone': 3,
                            'col-email': 4,
                            'col-tag': 5,
                            'col-loc': 6,
                            'col-rate': 7,
                            'col-owner': 8,
                            'col-contact': 9,
                            'col-status': 10,
                            'col-action': 11
                        };

                        const pageIdentifier = 'settings-companies';
                        let columnPreferences = [];
                        for (let id in colMap) {
                            columnPreferences.push({ index: colMap[id], visible: true });
                        }

                        // Load preferences synchronously
                        $.ajax({
                            url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug")]) }}',
                            type: 'GET',
                            data: { page_identifier: pageIdentifier },
                            async: false,
                            success: function(response) {
                                if (response.success && response.data && Array.isArray(response.data)) {
                                    response.data.forEach(pref => {
                                        const prefIndex = parseInt(pref.index);
                                        const col = columnPreferences.find(c => c.index === prefIndex);
                                        if (col) {
                                            col.visible = (typeof pref.visible === 'string') ? (pref.visible === 'true' || pref.visible === '1') : !!pref.visible;
                                        }
                                    });
                                }
                            }
                        });

                        function saveColumnPreferences() {
                            $.ajax({
                                url: '{{ route("settings.column.preferences.save", ["box_slug" => request()->route("box_slug")]) }}',
                                type: 'POST',
                                data: {
                                    page_identifier: pageIdentifier,
                                    column_preferences: columnPreferences,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {}
                            });
                        }

                        // Apply loaded preferences to table and checkboxes
                        for (let id in colMap) {
                            let colIdx = colMap[id];
                            let pref = columnPreferences.find(c => c.index === colIdx);
                            let isVisible = pref ? pref.visible : true;
                            
                            window.companiesTable.column(colIdx).visible(isVisible, false);
                            $('#' + id).prop('checked', isVisible);
                        }
                        window.companiesTable.columns.adjust().draw();

                        // Bind change event
                        $('.check').off('change').on('change', function() {
                            let id = $(this).attr('id');
                            if (colMap[id] !== undefined && window.companiesTable) {
                                let isChecked = $(this).is(':checked');
                                window.companiesTable.column(colMap[id]).visible(isChecked);
                                
                                let pref = columnPreferences.find(c => c.index === colMap[id]);
                                if (pref) pref.visible = isChecked;
                                
                                saveColumnPreferences();
                            }
                        });
                    }
                });
            }

            // Filter Logic
            $.fn.dataTable.ext.search.push(
                function( settings, data, dataIndex ) {
                    if (settings.nTable.id !== 'companieslist') return true;
                    
                    let minDateStr = $('#custom-date-range').data('startDate');
                    let maxDateStr = $('#custom-date-range').data('endDate');
                    if (!minDateStr || !maxDateStr) return true;
                    
                    let rowDateStr = data[12]; // index 12 is 'Created At' (0-indexed)
                    if (!rowDateStr) return false;
                    
                    let rowDate = new Date(rowDateStr);
                    let minDate = new Date(minDateStr);
                    let maxDate = new Date(maxDateStr);
                    
                    minDate.setHours(0,0,0,0);
                    maxDate.setHours(23,59,59,999);
                    
                    return rowDate >= minDate && rowDate <= maxDate;
                }
            );

            $('#custom-search-input').on('keyup', function() {
                if (window.companiesTable) {
                    window.companiesTable.search(this.value).draw();
                }
            });

            $('#sort-asc').on('click', function() {
                if (window.companiesTable) {
                    window.companiesTable.order([2, 'asc']).draw(); // Sort by Name
                }
            });

            $('#sort-desc').on('click', function() {
                if (window.companiesTable) {
                    window.companiesTable.order([2, 'desc']).draw(); // Sort by Name
                }
            });

            $('#sort-recent-viewed').on('click', function() {
                if (window.companiesTable) {
                    window.companiesTable.order([12, 'desc']).draw(); // Sort by Created At descending
                }
            });
            
            // Wait for daterangepicker to initialize, then bind
            setTimeout(function() {
                $('#custom-date-range').on('apply.daterangepicker', function(ev, picker) {
                    $(this).data('startDate', picker.startDate.format('YYYY-MM-DD'));
                    $(this).data('endDate', picker.endDate.format('YYYY-MM-DD'));
                    if (window.companiesTable) window.companiesTable.draw();
                });
                
                $('#custom-date-range').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                    $(this).data('startDate', '');
                    $(this).data('endDate', '');
                    if (window.companiesTable) window.companiesTable.draw();
                });
            }, 500);

            // Edit Company Click
            $(document).on('click', '.edit-company', function() {
                let company = $(this).data('json');
                let form = $('#add-company-form');
                
                // Reset form first
                form[0].reset();
                $('#bill_city_id').empty().append('<option value="">--Select--</option>');
                $('#existing-logo-preview').remove(); // Remove old preview if any

                // Populate form
                form.find('input[name="bill_name"]').val(company.bill_name);
                form.find('input[name="bill_phone"]').val(company.bill_phone);
                form.find('input[name="pan"]').val(company.pan);
                form.find('input[name="gstin"]').val(company.gstin);
                form.find('textarea[name="bill_address"]').val(company.bill_address);
                form.find('input[name="bill_pin_code"]').val(company.bill_pin_code);
                
                // Select State
                if(company.bill_state_id) {
                    form.find('select[name="bill_state_id"]').val(company.bill_state_id).trigger('change');
                    // Load cities and then select city
                    loadCities(company.bill_state_id, company.bill_city_id);
                } else {
                     form.find('select[name="bill_state_id"]').val('').trigger('change');
                }

                // Set Status
                $('#status_select').val(company.status);



                form.data('id', company.id);
                $('#btn-save-company').text('Update');
                $('#offcanvas_add .fw-semibold').text('Edit Company');
                $('#offcanvas_add').data('is-edit', true);
                $('#offcanvas_add').offcanvas('show');
            });

            function showSuccessToast(message) {
                var toastEl = document.getElementById('toastMessage');
                toastEl.classList.remove('bg-danger');
                toastEl.classList.add('bg-success');
                document.getElementById('toastBody').innerText = message;
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            }

            function showErrorToast(message) {
                var toastEl = document.getElementById('toastMessage');
                toastEl.classList.remove('bg-success');
                toastEl.classList.add('bg-danger');
                document.getElementById('toastBody').innerText = message;
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            }

            // Delete Company
            $(document).on('click', '.delete-company', function() {
                if(confirm('Are you sure you want to delete this company?')) {
                    let id = $(this).data('id');
                    let url = "{{ route('settings.general.companies.destroy', ['box_slug' => request()->route('box_slug')]) }}";
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}', id: id },
                        success: function(response) {
                            loadCompanies();
                            showSuccessToast('Company deleted successfully.');
                        },
                        error: function(xhr) {
                            showErrorToast('Error deleting company.');
                        }
                    });
                }
            });

            // Export Companies
            $('#export-pdf, #export-excel').on('click', function(e) {
                e.preventDefault();
                let type = $(this).attr('id') === 'export-pdf' ? 'pdf' : 'excel';
                let url = "{{ route('settings.general.companies.export', ['box_slug' => request()->route('box_slug')]) }}?type=" + type;
                
                showSuccessToast('Preparing ' + type.toUpperCase() + ' export...');

                $.ajax({
                    url: url,
                    method: 'GET',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(blob, status, xhr) {
                        var contentType = xhr.getResponseHeader('content-type');
                        if (contentType && contentType.indexOf('application/json') !== -1) {
                            var reader = new FileReader();
                            reader.onload = function() {
                                try {
                                    var response = JSON.parse(reader.result);
                                    showErrorToast(response.message || 'Export failed.');
                                } catch(e) {
                                    showErrorToast('An error occurred processing the response.');
                                }
                            };
                            reader.readAsText(blob);
                        } else {
                            var filename = "";
                            var disposition = xhr.getResponseHeader('Content-Disposition');
                            if (disposition && disposition.indexOf('attachment') !== -1) {
                                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                                var matches = filenameRegex.exec(disposition);
                                if (matches != null && matches[1]) { 
                                    filename = matches[1].replace(/['"]/g, '');
                                }
                            }
                            if(!filename) filename = 'Companies_Export.' + (type === 'pdf' ? 'pdf' : 'xlsx');

                            var url = window.URL.createObjectURL(blob);
                            var a = document.createElement('a');
                            a.href = url;
                            a.download = filename;
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                            a.remove();
                            showSuccessToast('Export downloaded successfully.');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.response instanceof Blob) {
                            var reader = new FileReader();
                            reader.onload = function() {
                                try {
                                    var response = JSON.parse(reader.result);
                                    showErrorToast(response.message || 'Export failed.');
                                } catch(e) {
                                    showErrorToast('An error occurred.');
                                }
                            };
                            reader.readAsText(xhr.response);
                        } else {
                            showErrorToast('An error occurred during export.');
                        }
                    }
                });
            });
        });
    </script>
@endsection

@include("partials.add_company_scripts")
