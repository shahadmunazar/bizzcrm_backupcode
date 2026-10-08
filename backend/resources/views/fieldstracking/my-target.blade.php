@extends('layouts.main')
@section('title', 'My Target')
@section('content')
<div class="page-wrapper">
			<div class="content">
				<div class="row">
					<div class="col-md-12">
						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									<h4 class="page-title">My Target</h4>
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
											<input type="text" class="form-control" placeholder="Search For Target">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">

										    <a href="javascript:void(0);" class="btn btn-dark me-2" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-file-check me-2"></i> Create New Tamplate</a>
											<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-square-rounded-plus me-2"></i> Assign Target</a>
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
									<table class="table datatable">
										<thead class="thead-light">
											<tr>
												<th class="no-sort">
													<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
												</th>
												<th>Target Name</th>
												<th>Duration</th>
												<th>Sales Amount</th>
												<th>Payment Collection</th>
												<th>Leads</th>
												<th>New Customes</th>
												<th>Customer Visit</th>
												<th class="text-end">Action</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
												<td>April Target</td>
												<td>Calendar Week</td>
												<td>₹50,000</td>
												<td>5,00000</td>
												<td>0</td>
												<td>0</td>
												<td>0</td>
												<td>
													<div class="dropdown table-action">
														<a href="#" class="action-icon " data-bs-toggle="dropdown" aria-expanded="false">
															<i class="fa fa-ellipsis-v"></i>
														</a>
														<div class="dropdown-menu dropdown-menu-right">
															<a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
																<i class="ti ti-edit text-blue"></i>Edit
															</a>
															<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_company">
																<i class="ti ti-trash text-danger"></i>Delete
															</a>
														</div>
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

			</div>
		</div>
		<!-- /Page Wrapper -->
        
		
		<!-- Create target template -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Create Target Template</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="company.html">
					
							<div class="row">
							    <div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Target Name<span style="Color:red;">*</span></label>
										<input type="text" placeholder="Enter Name" class="form-control">
									</div>									
								</div>
							</div>
							
							<hr>
							<div class="card" style="background:none; box-shadow:inherit;">
							<div class="card-body">
								    <h5 class="mb-3" style="margin-left:-15px;">Target</h5>
									
									<div class="mb-3 row">
										<label class="col-form-label col-lg-5"><input type="checkbox" name="checkbox" class="me-2"> Sales Amount</label>
										<div class="col-lg-7">
											<div class="input-group">
												<span class="input-group-text" id="basic-addon1">₹</span>
												<input type="text" class="form-control" placeholder="0" aria-label="Amount" aria-describedby="basic-addon1">
											</div>
										</div>
									</div>
									<div class="mb-3 row">
										<label class="col-form-label col-lg-5"><input type="checkbox" name="checkbox" class="me-2"> Payment Collection</label>
										<div class="col-lg-7">
											<div class="input-group">
												<span class="input-group-text" id="basic-addon1">₹</span>
												<input type="text" class="form-control" placeholder="0" aria-label="Amount" aria-describedby="basic-addon1">
											</div>
										</div>
									</div>
									<div class="mb-3 row">
										<label class="col-form-label col-lg-5"><input type="checkbox" name="checkbox" class="me-2"> New Lead</label>
										<div class="col-lg-7">
											<div class="input-group">
												<input type="text" class="form-control" placeholder="0">
											</div>
										</div>
									</div>
									<div class="mb-3 row">
										<label class="col-form-label col-lg-5"><input type="checkbox" name="checkbox" class="me-2"> New Customer</label>
										<div class="col-lg-7">
											<div class="input-group">
												<input type="text" class="form-control" placeholder="0">
											</div>
										</div>
									</div>
									<div class="mb-3 row">
										<label class="col-form-label col-lg-5"><input type="checkbox" name="checkbox" class="me-2"> Customer Visit</label>
										<div class="col-lg-7">
											<div class="input-group">
												<input type="text" class="form-control" placeholder="0">
											</div>
										</div>
									</div>

									
									
									
									
									
							
							</div>
							</div>
							
							
							
							
							
							
							
							
							<div class="row">
							    <div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Day / Duration<span style="Color:red;">*</span></label>
										<select class="select">
											<option disabled selected>Select Customer Level</option>
											<option>Primary Customer</option>
											<option>secondary Customer</option>
										</select>
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
					
				
			</div>
		</div>
		<!-- /Add Company -->
		
		
		

		<!-- Edit Company -->
		<!-- Edit Company -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Edit Beat List</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="company.html">
					<div class="card">
						<div class="card-body">
							<div class="row">
							
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Beat Name <span class="text-danger"> *</span></label>
										<input type="text" class="form-control">
									</div>									
								</div>
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Add Town/City</label>
										<input type="email" class="form-control">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Add Territory</label>
										<input type="text" class="form-control">
									</div>									
								</div>
							</div>
                        </div>
                    </div>						
					<div class="card">
						<div class="card-body">
							<div class="row">			
								<h5 class="mb-2">Assign Staff</h5>
								<div class="col-md-12">
									<div class="mb-3">
										<select class="select" multiple>
											<option value="Shitender Kumar">Shitender Kumar</option>
											<option value="Shefali Sharma">Shefali Sharma</option>
											<option value="Munish Malhotra">Munish Malhotra</option>
										</select>
									</div>									
								</div>
					        </div>
                        </div>
                    </div>
					
					<div class="card">
						<div class="card-body">
							<div class="row">	
							
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Customer Level</label>
										<select class="select">
											<option disabled>Select Customer Level</option>
											<option>Primary Customer</option>
											<option>secondary Customer</option>
										</select>
									</div>
									<div class="mb-3">
									    <label class="col-form-label">Secondary Customer</label>
										<select class="select">
											<option value="" disabled>Select Secondary Customer</option>
											<option value="">Ganesh Tradors</option>
											<option>Medizia Biotech</option>
										</select>
									</div>
                                    <div class="mb-3">
										<label class="col-form-label">Add Customer</label>
										<select class="select">
											<option>0 Customer Selected</option>
											<option>0</option>
											<option>1</option>
										</select>
									</div> 
									<div class="mb-3">
										<label class="col-form-label">Add Lead</label>
										<select class="select">
											<option>0 Lead Selected</option>
											<option>0</option>
											<option>1</option>
										</select>
									</div> 


									
								</div>
								
							</div>
							
					    </div>
                    </div>
                    <div class="card">
						<div class="card-body">
								
                            <div class="row">   
								<h4 class="mb-2">Add Frequency</h4>
								<div class="col-md-6">
								    
									<div class="mb-3">
										<select class="select">
											<option value="Weekly">Weekly</option>
											<option value="Monthly">Monthly</option>
										</select>
									</div>									
								</div>

								<div class="col-md-6">
									<div class="mb-3">
										<input type="text" placeholder="Enter Frequency" class="form-control">
									</div>									
								</div>
								
								<div class="col-md-12">
									<div class="mb-3">
									    <input type="text" placeholder="Define TC in a day" class="form-control">
									</div>
								</div>
								
								
							</div>	
								
					</div>
					</div>
			        <div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
							
			</div>
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

	</div>
	
@endsection
