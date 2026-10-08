@extends('layouts.main')
@section('content')
@section('title','Purchase Transaction')
<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									<h4 class="page-title">Purchase Transaction<span class="count-title">{{ $totalInvoices ?? 0 }}</span></h4>
								</div>
								<div class="col-4 text-end">
									<div class="head-icons">
										<a href="companies.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

						<div class="card ">
							<div class="card-header">
								<!-- Search -->
								<div class="row align-items-center">
									<div class="col-sm-4">
										<div class="icon-form mb-3 mb-sm-0">
											<span class="form-icon"><i class="ti ti-search"></i></span>
											<input type="text" class="form-control" id="custom-search-input" placeholder="Search Companies">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">

										    <div class="btn-group my-1 me-2">
												<button class="btn btn-lg btn-success btn-lg dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
													Create Document
												</button>
												<ul class="dropdown-menu">
													<li><a class="dropdown-item" href="javascript:void(0);">Purchase Order</a></li>
													<li><a class="dropdown-item" href="javascript:void(0);">Service Order</a></li>
													<li><a class="dropdown-item" href="javascript:void(0);">Order Confirmation</a></li>
													<li><a class="dropdown-item" href="javascript:void(0);">Service Confirmation</a></li>
													<li><a class="dropdown-item" href="javascript:void(0);">Invoice</a></li>
													<li><a class="dropdown-item" href="javascript:void(0);">Adhoc Invoice</a></li>
												</ul>
											</div>


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
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-value="Ascending">
															<i class="ti ti-circle-chevron-right me-1"></i>Ascending
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-value="Descending">
															<i class="ti ti-circle-chevron-right me-1"></i>Descending
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-value="Recently Viewed">
															<i class="ti ti-circle-chevron-right me-1"></i>Recently
															Viewed
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-value="Recently Added">
															<i class="ti ti-circle-chevron-right me-1"></i>Recently
															Added
														</a>
													</li>
												</ul>
											</div>
										</div>
										<input type="hidden" id="sort_value">
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
												<div class="border-top pt-3" id="manage-columns-list">
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Company Name</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-company" class="check toggle-vis" data-column="1" checked>
															<label for="col-company" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Company Email</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-company_email" class="check toggle-vis" data-column="2" checked>
															<label for="col-company_email" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Box Slug</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-box_slug" class="check toggle-vis" data-column="3" checked>
															<label for="col-box_slug" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Transaction Name</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-transaction" class="check toggle-vis" data-column="4" checked>
															<label for="col-transaction" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Document Number</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-document" class="check toggle-vis" data-column="5" checked>
															<label for="col-document" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Goods Status</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-goods_status" class="check toggle-vis" data-column="6" checked>
															<label for="col-goods_status" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Amount</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-amount" class="check toggle-vis" data-column="7" checked>
															<label for="col-amount" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Created Date</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-created_date" class="check toggle-vis" data-column="8" checked>
															<label for="col-created_date" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Action</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-action" class="check toggle-vis" data-column="9" checked>
															<label for="col-action" class="checktoggle"></label>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="form-sorts dropdown">
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
																<a href="company.html" class="btn btn-primary">Filter</a>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- /Filter -->

								<!-- Contact List -->
								<div class="table-responsive custom-table">
									<table class="table" id="purchase-table">
										<thead class="thead-light">
											<tr>
												<th class="no-sort">
													<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
												</th>

												<th>Company Name</th>
												<th>Company Email</th>
												<th>Box Slug</th>
												<th>Transaction Name</th>
												<th>Document Number</th>
												<th>Goods Status</th>
												<th>Amount</th>
												<th>Created Date</th>
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


		<!-- Add Company -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Add New Company</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="company.html">
					<div class="card">
						<div class="card-body">
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
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Company Name <span class="text-danger"> *</span></label>
										<input type="text" class="form-control">
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Email Address</label>
										<input type="email" class="form-control">
									</div>
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Account URL</label>
										<input type="text" class="form-control">
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Phone Number <span class="text-danger"> *</span></label>
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
									<div class="mb-3 ">
										<label class="col-form-label">Password <span class="text-danger"> *</span></label>
										<div class="pass-group">
											<input type="password" class="pass-input form-control">
											<span class="ti toggle-password ti-eye-off"></span>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="col-form-label">Confirm Password <span class="text-danger"> *</span></label>
										<div class="pass-group">
											<input type="password" class="pass-inputs form-control">
											<span class="ti toggle-passwords ti-eye-off"></span>
										</div>
									</div>
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Address</label>
										<input type="text" class="form-control">
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="col-form-label">Plan Name <span class="text-danger"> *</span></label>
										<select class="select">
											<option>Select</option>
											<option>Advanced</option>
											<option>Basic</option>
											<option>Enterprise</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="col-form-label">Plan Type <span class="text-danger"> *</span></label>
										<select class="select">
											<option>Select</option>
											<option>Monthly</option>
											<option>Yearly</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="col-form-label">Currency <span class="text-danger"> *</span></label>
										<select class="select">
											<option>Choose</option>
											<option>Dollar</option>
											<option>Euro</option>
											<option>Pound</option>
											<option>Rupee</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="col-form-label">Language <span class="text-danger"> *</span></label>
										<select class="select">
											<option>Choose</option>
											<option>English</option>
											<option>Arabic</option>
											<option>French</option>
											<option>German</option>
										</select>
									</div>
								</div>
								<div class="col-md-12">
									<div class="mb-0">
										<div class="radio-wrap">
											<label class="col-form-label">Status</label>
											<div class="d-flex flex-wrap">
												<div class="me-2">
													<input type="radio" class="status-radio" id="person" name="leave" checked="">
													<label for="person">Active</label>
												</div>
												<div>
													<input type="radio" class="status-radio" id="Organization" name="leave">
													<label for="Organization">Inactive</label>
												</div>
											</div>
										</div>
									</div>
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
		<!-- /Add Company -->




		<!-- Edit Company -->
		<!-- Edit Company -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Edit Company</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="company.html">
					<div class="card">
						<div class="card-body">
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
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Company Name <span class="text-danger"> *</span></label>
										<input type="text" class="form-control" value="NovaWave LLC">
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Email Address</label>
										<input type="email" class="form-control" value="robertson@gmail.com">
									</div>
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Account URL</label>
										<input type="text" class="form-control" value="nw.example.com">
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Phone Number <span class="text-danger"> *</span></label>
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
									<div class="mb-3 ">
										<label class="col-form-label">Password <span class="text-danger"> *</span></label>
										<div class="pass-group">
											<input type="password" class="pass-input form-control">
											<span class="ti toggle-password ti-eye-off"></span>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="col-form-label">Confirm Password <span class="text-danger"> *</span></label>
										<div class="pass-group">
											<input type="password" class="pass-inputs form-control">
											<span class="ti toggle-passwords ti-eye-off"></span>
										</div>
									</div>
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Address</label>
										<input type="text" class="form-control">
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="col-form-label">Plan Name <span class="text-danger"> *</span></label>
										<select class="select">
											<option>Select</option>
											<option selected="">Advanced</option>
											<option>Basic</option>
											<option>Enterprise</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="col-form-label">Plan Type <span class="text-danger"> *</span></label>
										<select class="select">
											<option>Select</option>
											<option>Monthly</option>
											<option>Yearly</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Currency <span class="text-danger"> *</span></label>
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
										<label class="col-form-label">Language <span class="text-danger"> *</span></label>
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
										<div class="radio-wrap">
											<label class="col-form-label">Status</label>
											<div class="d-flex flex-wrap">
												<div class="me-2">
													<input type="radio" class="status-radio" id="person2" name="leave" checked="">
													<label for="person2">Active</label>
												</div>
												<div>
													<input type="radio" class="status-radio" id="Organization2" name="leave">
													<label for="Organization2">Inactive</label>
												</div>
											</div>
										</div>
									</div>
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
		<!-- /Edit Company -->
		<!-- /Edit Company -->


		<!-- /Upgrade Plane -->

		<!-- Delete Company -->
		<div class="modal fade" id="delete_company" role="dialog">
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
								<a href="company.html" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Company -->

		<!-- Company Detail -->
		<div class="modal fade custom-modal" id="company_detail">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Company Detail</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body pb-0">
						<div class="mb-3">
							<div class="d-flex justify-content-between align-items-center rounded bg-light-400 p-3">
								<div class="file-name-icon d-flex align-items-center">
									<a href="#" class="avatar avatar-sm border rounded p-1 me-2 flex-shrink-0">
										<img src="assets/img/icons/company-icon-01.svg" class="w-auto h-auto" alt="img">
									</a>
									<div>
										<p class="text-gray-9 fw-medium mb-0">NovaWave LLC</p>
									</div>
								</div>
								<span class="badge badge-success">Active</span>
							</div>
						</div>
						<div>
							<p class="text-gray-9 fw-medium">Basic Info</p>
							<div class="border-bottom mb-3">
								<div class="row align-items-center">
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Account URL</p>
											<p class="text-gray-9">nw.example.com</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Phone Number</p>
											<p class="text-gray-9">+1 875455453</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Website</p>
											<p class="text-gray-9">www.exmple.com</p>
										</div>
									</div>
								</div>
								<div class="row align-items-center">
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Currency</p>
											<p class="text-gray-9">United Stated Dollar (USD)</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Language</p>
											<p class="text-gray-9">English</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Addresss</p>
											<p class="text-gray-9">3705 Lynn Avenue, Phelps, WI 54554</p>
										</div>
									</div>
								</div>
							</div>
							<p class="text-gray-9 fw-medium">Plan Details</p>
							<div>
								<div class="row align-items-center">
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Plan Name</p>
											<p class="text-gray-9">Advanced</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Plan Type</p>
											<p class="text-gray-9">Monthly</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Price</p>
											<p class="text-gray-9">$200</p>
										</div>
									</div>
								</div>
								<div class="row align-items-center">
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Register Date</p>
											<p class="text-gray-9">12 Sep 2024</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Expiring On</p>
											<p class="text-gray-9">11 Oct 2024</p>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>


				</div>
			</div>
		</div>
		<!-- /Company Detail -->
@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#purchase-table').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            bFilter: false,
            bInfo: false,
            autoWidth: true,
            ordering: true,
            language: {
                search: ' ',
                sLengthMenu: '_MENU_',
                searchPlaceholder: "Search",
                info: "_START_ - _END_ of _TOTAL_ items",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    next: 'Next <i class=" fa fa-angle-double-right ms-2"></i>',
                    previous: '<i class="fa fa-angle-double-left me-2"></i> Previous'
                },
            },
            initComplete: (settings, json) => {
                $('.dataTables_paginate').appendTo('.datatable-paginate');
                $('.dataTables_length').appendTo('.datatable-length');
            },
            ajax: {
                url: "{{ route('superadmin.purchase_transaction', ['box_slug' => request()->route('box_slug') ?? request('box_slug') ?? session('box_slug')]) }}",
                data: function(d) {
                    d.sort = $('#sort_value').val();
                    d.date_range = $('.bookingrange').val();
                }
            },
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'company_name', name: 'company_name', orderable: false, searchable: true},
                {data: 'company_email', name: 'company_email', orderable: false, searchable: true},
                {data: 'box_slug', name: 'box_slug', orderable: false, searchable: true},
                {data: 'transaction_name', name: 'transaction_name', orderable: false, searchable: true},
                {data: 'document_number', name: 'document_number', orderable: false, searchable: true},
                {data: 'goods_status', name: 'goods_status', orderable: true, searchable: true},
                {data: 'amount', name: 'amount', orderable: true, searchable: false},
                {data: 'created_date', name: 'created_date', orderable: true, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            order: [[8, 'desc']] // Order by created date desc by default
        });

        // Search Filter
        var searchTimeout;
        $('#custom-search-input').on('keyup', function() {
            var val = this.value;
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                if ($.fn.DataTable.isDataTable('#purchase-table')) {
                    $('#purchase-table').DataTable().search(val).draw();
                }
            }, 300);
        });

        // Sort Filter
        $('.sort-item').on('click', function(e) {
            e.preventDefault();
            $('#sort_value').val($(this).data('value'));
            if ($.fn.DataTable.isDataTable('#purchase-table')) {
                $('#purchase-table').DataTable().ajax.reload();
            }
        });

        // Date Range Filter
        $('.bookingrange').on('apply.daterangepicker change', function() {
            if ($.fn.DataTable.isDataTable('#purchase-table')) {
                $('#purchase-table').DataTable().ajax.reload();
            }
        });

        // Toggle Column Visibility
        $(document).on('change', '.toggle-vis', function (e) {
            e.preventDefault();
            var column = $('#purchase-table').DataTable().column($(this).attr('data-column'));
            column.visible(!column.visible());
        });
    });
</script>
@endpush
@endsection
