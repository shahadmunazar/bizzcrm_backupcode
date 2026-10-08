@extends('layouts.main')
@section('content')
@section('title', 'Integration')


		<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-4">
									<h4 class="page-title">Campaign</h4>
								</div>
								<div class="col-8 text-end">
									<div class="head-icons">
										<a href="campaign.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
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

						<!-- Campaign Status -->
						<!-- <div class="row">
							<div class="col-xl-3 col-lg-6">
								<div class="campaign-box bg-danger-light">
									<div class="campaign-img">
										<span>
											<i class="ti ti-brand-campaignmonitor"></i>
										</span>
										<p>Campaign</p>
									</div>
									<h2>474</h2>
								</div>
							</div>
							<div class="col-xl-3 col-lg-6">
								<div class="campaign-box bg-warning-light">
									<div class="campaign-img">
										<span>
											<i class="ti ti-send"></i>
										</span>
										<p>Sent</p>
									</div>
									<h2>454</h2>
								</div>
							</div>
							<div class="col-xl-3 col-lg-6">
								<div class="campaign-box bg-purple-light">
									<div class="campaign-img">
										<span>
											<i class="ti ti-brand-feedly"></i>
										</span>
										<p>Opened</p>
									</div>
									<h2>658</h2>
								</div>
							</div>
							<div class="col-xl-3 col-lg-6">
								<div class="campaign-box bg-success-light">
									<div class="campaign-img">
										<span>
											<i class="ti ti-brand-pocket"></i>
										</span>
										<p>Completed</p>
									</div>
									<h2>747</h2>
								</div>
							</div>
						</div> -->
						<!-- /Campaign Status -->

						 <div class="card">
							<div class="card-header">

									<div class="row align-items-center">
										<div class="col-sm-4">
											<!-- <div class="icon-form mb-3 mb-sm-0">
												<span class="form-icon"><i class="ti ti-search"></i></span>
												<input type="text" class="form-control" placeholder="Search Campaign">
											</div> -->
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
												<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-square-rounded-plus me-2"></i>Add New Campaign</a>
											</div>
										</div>
									</div>

							</div>
						</div>





						<!-- Campaign Tab -->
						<div class="campaign-tab">
							<ul class="nav">
								<li class=""><a class="active" href="#bottom-justified-tab1" data-bs-toggle="tab">All</a></li>
									<li class=""><a class="" href="#bottom-justified-tab2" data-bs-toggle="tab">Broadcast</a></li>
									<li class=""><a class="" href="#bottom-justified-tab3" data-bs-toggle="tab">API</a></li>
									<li class=""><a class="" href="#bottom-justified-tab4" data-bs-toggle="tab">Scheduled</a></li>
							</ul>
						</div>
						<!-- Campaign Tab -->



					<div class="tab-content">
					<div class="tab-pane show active" id="bottom-justified-tab1">
						<div class="card">

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

									<div class="col-sm-4">
											<div class="icon-form mb-3 mb-sm-0">
												<span class="form-icon"><i class="ti ti-search"></i></span>
												<input type="text" class="form-control" placeholder="Search Campaign">
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
												<th></th>
												<th>Campaign</th>
												<th>Type</th>
												<th>Progress</th>
												<th>Members</th>
												<th>Start Date</th>
												<th>End Date</th>
												<th>Status</th>
												<th>Created</th>
												<th class="text-end">Action</th>
										</tr>

										<tr class="odd">
											<td class="sorting_1">
												<label class="checkboxs">
													<input type="checkbox"><span class="checkmarks"></span></label>
											</td>
											<td>
												<div class="set-star rating-select"><i class="fa fa-star"></i></div>
											</td>
											<td>Distribution</td>
											<td>Public Relations</td>
											<td>
												<ul class="list-progress">
													<li>
														<h6>40.5%</h6>
														<p>Opened</p>
													</li>
													<li>
														<h6>20.5%</h6>
														<p>Closed</p>
													</li>
													<li>
														<h6>30.5%</h6>
														<p>Unsubscribe</p>
													</li>
													<li>
														<h6>70.5%</h6>
														<p>Delivered</p>
													</li>
													<li>
														<h6>35.0%</h6>
														<p>Conversation</p>
													</li>
												</ul>
											</td>
											<td>
												<ul class="project-mem">
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-14.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-15.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-16.jpg" alt="img"></a>
													</li>
													<li class="more-set"><a href="#">3+</a></li>
												</ul>
											</td>
											<td>25 Sep 2023</td>
											<td>29 Sep 2023</td>
											<td><span class="badge badge-pill badge-status bg-success">Success</span></td>
											<td>25 Sep 2023</td>
											<td>
												<div class="dropdown table-action"><a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
													<div class="dropdown-menu dropdown-menu-right" style=""><a class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit" href="#"><i class="ti ti-edit text-blue"></i> Edit</a><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_campaign"><i class="ti ti-trash text-danger"></i> Delete</a></div>
												</div>
											</td>
										</tr>

										<tr class="even">
											<td class="sorting_1">
												<label class="checkboxs">
													<input type="checkbox"><span class="checkmarks"></span></label>
											</td>
											<td>
												<div class="set-star rating-select"><i class="fa fa-star"></i></div>
											</td>
											<td>Merchandising</td>
											<td>Content Marketing</td>
											<td>
												<ul class="list-progress">
													<li>
														<h6>65.5%</h6>
														<p>Opened</p>
													</li>
													<li>
														<h6>83.5%</h6>
														<p>Closed</p>
													</li>
													<li>
														<h6>67.5%</h6>
														<p>Unsubscribe</p>
													</li>
													<li>
														<h6>32.0%</h6>
														<p>Delivered</p>
													</li>
													<li>
														<h6>22.5%</h6>
														<p>Conversation</p>
													</li>
												</ul>
											</td>
											<td>
												<ul class="project-mem">
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-03.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-05.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-06.jpg" alt="img"></a>
													</li>
													<li class="more-set"><a href="#">3+</a></li>
												</ul>
											</td>
											<td>03 Oct 2023</td>
											<td>16 Oct 2023</td>
											<td><span class="badge badge-pill badge-status bg-warning">Pending</span></td>
											<td>03 Oct 2023</td>
											<td>
												<div class="dropdown table-action"><a href="#" class="action-icon " data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
													<div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit" href="#"><i class="ti ti-edit text-blue"></i> Edit</a><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_campaign"><i class="ti ti-trash text-danger"></i> Delete</a></div>
												</div>
											</td>
										</tr>

										<tr class="odd">
											<td class="sorting_1">
												<label class="checkboxs">
													<input type="checkbox"><span class="checkmarks"></span></label>
											</td>
											<td>
												<div class="set-star rating-select"><i class="fa fa-star"></i></div>
											</td>
											<td>Distribution</td>
											<td>Public Relations</td>
											<td>
												<ul class="list-progress">
													<li>
														<h6>40.5%</h6>
														<p>Opened</p>
													</li>
													<li>
														<h6>20.5%</h6>
														<p>Closed</p>
													</li>
													<li>
														<h6>30.5%</h6>
														<p>Unsubscribe</p>
													</li>
													<li>
														<h6>70.5%</h6>
														<p>Delivered</p>
													</li>
													<li>
														<h6>35.0%</h6>
														<p>Conversation</p>
													</li>
												</ul>
											</td>
											<td>
												<ul class="project-mem">
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-14.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-15.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-16.jpg" alt="img"></a>
													</li>
													<li class="more-set"><a href="#">3+</a></li>
												</ul>
											</td>
											<td>25 Sep 2023</td>
											<td>29 Sep 2023</td>
											<td><span class="badge badge-pill badge-status bg-success">Success</span></td>
											<td>25 Sep 2023</td>
											<td>
												<div class="dropdown table-action"><a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
													<div class="dropdown-menu dropdown-menu-right" style=""><a class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit" href="#"><i class="ti ti-edit text-blue"></i> Edit</a><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_campaign"><i class="ti ti-trash text-danger"></i> Delete</a></div>
												</div>
											</td>
										</tr>

										<tr class="even">
											<td class="sorting_1">
												<label class="checkboxs">
													<input type="checkbox"><span class="checkmarks"></span></label>
											</td>
											<td>
												<div class="set-star rating-select"><i class="fa fa-star"></i></div>
											</td>
											<td>Merchandising</td>
											<td>Content Marketing</td>
											<td>
												<ul class="list-progress">
													<li>
														<h6>65.5%</h6>
														<p>Opened</p>
													</li>
													<li>
														<h6>83.5%</h6>
														<p>Closed</p>
													</li>
													<li>
														<h6>67.5%</h6>
														<p>Unsubscribe</p>
													</li>
													<li>
														<h6>32.0%</h6>
														<p>Delivered</p>
													</li>
													<li>
														<h6>22.5%</h6>
														<p>Conversation</p>
													</li>
												</ul>
											</td>
											<td>
												<ul class="project-mem">
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-03.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-05.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-06.jpg" alt="img"></a>
													</li>
													<li class="more-set"><a href="#">3+</a></li>
												</ul>
											</td>
											<td>03 Oct 2023</td>
											<td>16 Oct 2023</td>
											<td><span class="badge badge-pill badge-status bg-warning">Pending</span></td>
											<td>03 Oct 2023</td>
											<td>
												<div class="dropdown table-action"><a href="#" class="action-icon " data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
													<div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit" href="#"><i class="ti ti-edit text-blue"></i> Edit</a><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_campaign"><i class="ti ti-trash text-danger"></i> Delete</a></div>
												</div>
											</td>
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
								<!-- /Campaign List -->

							</div>
						</div>



						</div>







						<div class="tab-pane" id="bottom-justified-tab2">
							<div class="card">

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

									<div class="col-sm-4">
											<div class="icon-form mb-3 mb-sm-0">
												<span class="form-icon"><i class="ti ti-search"></i></span>
												<input type="text" class="form-control" placeholder="Search Campaign">
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
												<th></th>
												<th>Campaign</th>
												<th>Type</th>
												<th>Progress</th>
												<th>Members</th>
												<th>Start Date</th>
												<th>End Date</th>
												<th>Status</th>
												<th>Created</th>
												<th class="text-end">Action</th>
										</tr>

										<tr class="odd">
											<td class="sorting_1">
												<label class="checkboxs">
													<input type="checkbox"><span class="checkmarks"></span></label>
											</td>
											<td>
												<div class="set-star rating-select"><i class="fa fa-star"></i></div>
											</td>
											<td>Distribution</td>
											<td>Public Relations</td>
											<td>
												<ul class="list-progress">
													<li>
														<h6>40.5%</h6>
														<p>Opened</p>
													</li>
													<li>
														<h6>20.5%</h6>
														<p>Closed</p>
													</li>
													<li>
														<h6>30.5%</h6>
														<p>Unsubscribe</p>
													</li>
													<li>
														<h6>70.5%</h6>
														<p>Delivered</p>
													</li>
													<li>
														<h6>35.0%</h6>
														<p>Conversation</p>
													</li>
												</ul>
											</td>
											<td>
												<ul class="project-mem">
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-14.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-15.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-16.jpg" alt="img"></a>
													</li>
													<li class="more-set"><a href="#">3+</a></li>
												</ul>
											</td>
											<td>25 Sep 2023</td>
											<td>29 Sep 2023</td>
											<td><span class="badge badge-pill badge-status bg-success">Success</span></td>
											<td>25 Sep 2023</td>
											<td>
												<div class="dropdown table-action"><a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
													<div class="dropdown-menu dropdown-menu-right" style=""><a class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit" href="#"><i class="ti ti-edit text-blue"></i> Edit</a><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_campaign"><i class="ti ti-trash text-danger"></i> Delete</a></div>
												</div>
											</td>
										</tr>

										<tr class="even">
											<td class="sorting_1">
												<label class="checkboxs">
													<input type="checkbox"><span class="checkmarks"></span></label>
											</td>
											<td>
												<div class="set-star rating-select"><i class="fa fa-star"></i></div>
											</td>
											<td>Merchandising</td>
											<td>Content Marketing</td>
											<td>
												<ul class="list-progress">
													<li>
														<h6>65.5%</h6>
														<p>Opened</p>
													</li>
													<li>
														<h6>83.5%</h6>
														<p>Closed</p>
													</li>
													<li>
														<h6>67.5%</h6>
														<p>Unsubscribe</p>
													</li>
													<li>
														<h6>32.0%</h6>
														<p>Delivered</p>
													</li>
													<li>
														<h6>22.5%</h6>
														<p>Conversation</p>
													</li>
												</ul>
											</td>
											<td>
												<ul class="project-mem">
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-03.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-05.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-06.jpg" alt="img"></a>
													</li>
													<li class="more-set"><a href="#">3+</a></li>
												</ul>
											</td>
											<td>03 Oct 2023</td>
											<td>16 Oct 2023</td>
											<td><span class="badge badge-pill badge-status bg-warning">Pending</span></td>
											<td>03 Oct 2023</td>
											<td>
												<div class="dropdown table-action"><a href="#" class="action-icon " data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
													<div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit" href="#"><i class="ti ti-edit text-blue"></i> Edit</a><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_campaign"><i class="ti ti-trash text-danger"></i> Delete</a></div>
												</div>
											</td>
										</tr>

										<tr class="odd">
											<td class="sorting_1">
												<label class="checkboxs">
													<input type="checkbox"><span class="checkmarks"></span></label>
											</td>
											<td>
												<div class="set-star rating-select"><i class="fa fa-star"></i></div>
											</td>
											<td>Distribution</td>
											<td>Public Relations</td>
											<td>
												<ul class="list-progress">
													<li>
														<h6>40.5%</h6>
														<p>Opened</p>
													</li>
													<li>
														<h6>20.5%</h6>
														<p>Closed</p>
													</li>
													<li>
														<h6>30.5%</h6>
														<p>Unsubscribe</p>
													</li>
													<li>
														<h6>70.5%</h6>
														<p>Delivered</p>
													</li>
													<li>
														<h6>35.0%</h6>
														<p>Conversation</p>
													</li>
												</ul>
											</td>
											<td>
												<ul class="project-mem">
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-14.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-15.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-16.jpg" alt="img"></a>
													</li>
													<li class="more-set"><a href="#">3+</a></li>
												</ul>
											</td>
											<td>25 Sep 2023</td>
											<td>29 Sep 2023</td>
											<td><span class="badge badge-pill badge-status bg-success">Success</span></td>
											<td>25 Sep 2023</td>
											<td>
												<div class="dropdown table-action"><a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
													<div class="dropdown-menu dropdown-menu-right" style=""><a class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit" href="#"><i class="ti ti-edit text-blue"></i> Edit</a><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_campaign"><i class="ti ti-trash text-danger"></i> Delete</a></div>
												</div>
											</td>
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
								<!-- /Campaign List -->

							</div>
						</div>
						</div>
						<div class="tab-pane" id="bottom-justified-tab3">
							<div class="card">

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

									<div class="col-sm-4">
											<div class="icon-form mb-3 mb-sm-0">
												<span class="form-icon"><i class="ti ti-search"></i></span>
												<input type="text" class="form-control" placeholder="Search Campaign">
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
												<th></th>
												<th>Campaign</th>
												<th>Type</th>
												<th>Progress</th>
												<th>Members</th>
												<th>Start Date</th>
												<th>End Date</th>
												<th>Status</th>
												<th>Created</th>
												<th class="text-end">Action</th>
										</tr>

										<tr class="odd">
											<td class="sorting_1">
												<label class="checkboxs">
													<input type="checkbox"><span class="checkmarks"></span></label>
											</td>
											<td>
												<div class="set-star rating-select"><i class="fa fa-star"></i></div>
											</td>
											<td>Distribution</td>
											<td>Public Relations</td>
											<td>
												<ul class="list-progress">
													<li>
														<h6>40.5%</h6>
														<p>Opened</p>
													</li>
													<li>
														<h6>20.5%</h6>
														<p>Closed</p>
													</li>
													<li>
														<h6>30.5%</h6>
														<p>Unsubscribe</p>
													</li>
													<li>
														<h6>70.5%</h6>
														<p>Delivered</p>
													</li>
													<li>
														<h6>35.0%</h6>
														<p>Conversation</p>
													</li>
												</ul>
											</td>
											<td>
												<ul class="project-mem">
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-14.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-15.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-16.jpg" alt="img"></a>
													</li>
													<li class="more-set"><a href="#">3+</a></li>
												</ul>
											</td>
											<td>25 Sep 2023</td>
											<td>29 Sep 2023</td>
											<td><span class="badge badge-pill badge-status bg-success">Success</span></td>
											<td>25 Sep 2023</td>
											<td>
												<div class="dropdown table-action"><a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
													<div class="dropdown-menu dropdown-menu-right" style=""><a class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit" href="#"><i class="ti ti-edit text-blue"></i> Edit</a><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_campaign"><i class="ti ti-trash text-danger"></i> Delete</a></div>
												</div>
											</td>
										</tr>

										<tr class="even">
											<td class="sorting_1">
												<label class="checkboxs">
													<input type="checkbox"><span class="checkmarks"></span></label>
											</td>
											<td>
												<div class="set-star rating-select"><i class="fa fa-star"></i></div>
											</td>
											<td>Merchandising</td>
											<td>Content Marketing</td>
											<td>
												<ul class="list-progress">
													<li>
														<h6>65.5%</h6>
														<p>Opened</p>
													</li>
													<li>
														<h6>83.5%</h6>
														<p>Closed</p>
													</li>
													<li>
														<h6>67.5%</h6>
														<p>Unsubscribe</p>
													</li>
													<li>
														<h6>32.0%</h6>
														<p>Delivered</p>
													</li>
													<li>
														<h6>22.5%</h6>
														<p>Conversation</p>
													</li>
												</ul>
											</td>
											<td>
												<ul class="project-mem">
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-03.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-05.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-06.jpg" alt="img"></a>
													</li>
													<li class="more-set"><a href="#">3+</a></li>
												</ul>
											</td>
											<td>03 Oct 2023</td>
											<td>16 Oct 2023</td>
											<td><span class="badge badge-pill badge-status bg-warning">Pending</span></td>
											<td>03 Oct 2023</td>
											<td>
												<div class="dropdown table-action"><a href="#" class="action-icon " data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
													<div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit" href="#"><i class="ti ti-edit text-blue"></i> Edit</a><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_campaign"><i class="ti ti-trash text-danger"></i> Delete</a></div>
												</div>
											</td>
										</tr>

										<tr class="odd">
											<td class="sorting_1">
												<label class="checkboxs">
													<input type="checkbox"><span class="checkmarks"></span></label>
											</td>
											<td>
												<div class="set-star rating-select"><i class="fa fa-star"></i></div>
											</td>
											<td>Distribution</td>
											<td>Public Relations</td>
											<td>
												<ul class="list-progress">
													<li>
														<h6>40.5%</h6>
														<p>Opened</p>
													</li>
													<li>
														<h6>20.5%</h6>
														<p>Closed</p>
													</li>
													<li>
														<h6>30.5%</h6>
														<p>Unsubscribe</p>
													</li>
													<li>
														<h6>70.5%</h6>
														<p>Delivered</p>
													</li>
													<li>
														<h6>35.0%</h6>
														<p>Conversation</p>
													</li>
												</ul>
											</td>
											<td>
												<ul class="project-mem">
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-14.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-15.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-16.jpg" alt="img"></a>
													</li>
													<li class="more-set"><a href="#">3+</a></li>
												</ul>
											</td>
											<td>25 Sep 2023</td>
											<td>29 Sep 2023</td>
											<td><span class="badge badge-pill badge-status bg-success">Success</span></td>
											<td>25 Sep 2023</td>
											<td>
												<div class="dropdown table-action"><a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
													<div class="dropdown-menu dropdown-menu-right" style=""><a class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit" href="#"><i class="ti ti-edit text-blue"></i> Edit</a><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_campaign"><i class="ti ti-trash text-danger"></i> Delete</a></div>
												</div>
											</td>
										</tr>

										<tr class="even">
											<td class="sorting_1">
												<label class="checkboxs">
													<input type="checkbox"><span class="checkmarks"></span></label>
											</td>
											<td>
												<div class="set-star rating-select"><i class="fa fa-star"></i></div>
											</td>
											<td>Merchandising</td>
											<td>Content Marketing</td>
											<td>
												<ul class="list-progress">
													<li>
														<h6>65.5%</h6>
														<p>Opened</p>
													</li>
													<li>
														<h6>83.5%</h6>
														<p>Closed</p>
													</li>
													<li>
														<h6>67.5%</h6>
														<p>Unsubscribe</p>
													</li>
													<li>
														<h6>32.0%</h6>
														<p>Delivered</p>
													</li>
													<li>
														<h6>22.5%</h6>
														<p>Conversation</p>
													</li>
												</ul>
											</td>
											<td>
												<ul class="project-mem">
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-03.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-05.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-06.jpg" alt="img"></a>
													</li>
													<li class="more-set"><a href="#">3+</a></li>
												</ul>
											</td>
											<td>03 Oct 2023</td>
											<td>16 Oct 2023</td>
											<td><span class="badge badge-pill badge-status bg-warning">Pending</span></td>
											<td>03 Oct 2023</td>
											<td>
												<div class="dropdown table-action"><a href="#" class="action-icon " data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
													<div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit" href="#"><i class="ti ti-edit text-blue"></i> Edit</a><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_campaign"><i class="ti ti-trash text-danger"></i> Delete</a></div>
												</div>
											</td>
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
								<!-- /Campaign List -->

							</div>
						</div>
						</div>
						<div class="tab-pane" id="bottom-justified-tab4">
							<div class="card">

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

									<div class="col-sm-4">
											<div class="icon-form mb-3 mb-sm-0">
												<span class="form-icon"><i class="ti ti-search"></i></span>
												<input type="text" class="form-control" placeholder="Search Campaign">
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
												<th></th>
												<th>Campaign</th>
												<th>Type</th>
												<th>Progress</th>
												<th>Members</th>
												<th>Start Date</th>
												<th>End Date</th>
												<th>Status</th>
												<th>Created</th>
												<th class="text-end">Action</th>
										</tr>

										<tr class="odd">
											<td class="sorting_1">
												<label class="checkboxs">
													<input type="checkbox"><span class="checkmarks"></span></label>
											</td>
											<td>
												<div class="set-star rating-select"><i class="fa fa-star"></i></div>
											</td>
											<td>Distribution</td>
											<td>Public Relations</td>
											<td>
												<ul class="list-progress">
													<li>
														<h6>40.5%</h6>
														<p>Opened</p>
													</li>
													<li>
														<h6>20.5%</h6>
														<p>Closed</p>
													</li>
													<li>
														<h6>30.5%</h6>
														<p>Unsubscribe</p>
													</li>
													<li>
														<h6>70.5%</h6>
														<p>Delivered</p>
													</li>
													<li>
														<h6>35.0%</h6>
														<p>Conversation</p>
													</li>
												</ul>
											</td>
											<td>
												<ul class="project-mem">
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-14.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-15.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-16.jpg" alt="img"></a>
													</li>
													<li class="more-set"><a href="#">3+</a></li>
												</ul>
											</td>
											<td>25 Sep 2023</td>
											<td>29 Sep 2023</td>
											<td><span class="badge badge-pill badge-status bg-success">Success</span></td>
											<td>25 Sep 2023</td>
											<td>
												<div class="dropdown table-action"><a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
													<div class="dropdown-menu dropdown-menu-right" style=""><a class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit" href="#"><i class="ti ti-edit text-blue"></i> Edit</a><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_campaign"><i class="ti ti-trash text-danger"></i> Delete</a></div>
												</div>
											</td>
										</tr>

										<tr class="even">
											<td class="sorting_1">
												<label class="checkboxs">
													<input type="checkbox"><span class="checkmarks"></span></label>
											</td>
											<td>
												<div class="set-star rating-select"><i class="fa fa-star"></i></div>
											</td>
											<td>Merchandising</td>
											<td>Content Marketing</td>
											<td>
												<ul class="list-progress">
													<li>
														<h6>65.5%</h6>
														<p>Opened</p>
													</li>
													<li>
														<h6>83.5%</h6>
														<p>Closed</p>
													</li>
													<li>
														<h6>67.5%</h6>
														<p>Unsubscribe</p>
													</li>
													<li>
														<h6>32.0%</h6>
														<p>Delivered</p>
													</li>
													<li>
														<h6>22.5%</h6>
														<p>Conversation</p>
													</li>
												</ul>
											</td>
											<td>
												<ul class="project-mem">
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-03.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-05.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-06.jpg" alt="img"></a>
													</li>
													<li class="more-set"><a href="#">3+</a></li>
												</ul>
											</td>
											<td>03 Oct 2023</td>
											<td>16 Oct 2023</td>
											<td><span class="badge badge-pill badge-status bg-warning">Pending</span></td>
											<td>03 Oct 2023</td>
											<td>
												<div class="dropdown table-action"><a href="#" class="action-icon " data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
													<div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit" href="#"><i class="ti ti-edit text-blue"></i> Edit</a><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_campaign"><i class="ti ti-trash text-danger"></i> Delete</a></div>
												</div>
											</td>
										</tr>

										<tr class="odd">
											<td class="sorting_1">
												<label class="checkboxs">
													<input type="checkbox"><span class="checkmarks"></span></label>
											</td>
											<td>
												<div class="set-star rating-select"><i class="fa fa-star"></i></div>
											</td>
											<td>Distribution</td>
											<td>Public Relations</td>
											<td>
												<ul class="list-progress">
													<li>
														<h6>40.5%</h6>
														<p>Opened</p>
													</li>
													<li>
														<h6>20.5%</h6>
														<p>Closed</p>
													</li>
													<li>
														<h6>30.5%</h6>
														<p>Unsubscribe</p>
													</li>
													<li>
														<h6>70.5%</h6>
														<p>Delivered</p>
													</li>
													<li>
														<h6>35.0%</h6>
														<p>Conversation</p>
													</li>
												</ul>
											</td>
											<td>
												<ul class="project-mem">
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-14.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-15.jpg" alt="img"></a>
													</li>
													<li>
														<a href="#"><img src="assets/img/profiles/avatar-16.jpg" alt="img"></a>
													</li>
													<li class="more-set"><a href="#">3+</a></li>
												</ul>
											</td>
											<td>25 Sep 2023</td>
											<td>29 Sep 2023</td>
											<td><span class="badge badge-pill badge-status bg-success">Success</span></td>
											<td>25 Sep 2023</td>
											<td>
												<div class="dropdown table-action"><a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
													<div class="dropdown-menu dropdown-menu-right" style=""><a class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit" href="#"><i class="ti ti-edit text-blue"></i> Edit</a><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_campaign"><i class="ti ti-trash text-danger"></i> Delete</a></div>
												</div>
											</td>
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
								<!-- /Campaign List -->

							</div>
						</div>
						</div>


					</div>




					</div>
				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Add New Campaign -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h4>Add New Campaign</h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="campaign-archieve.html">
					<div>
						<div class="row">
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Name <span class="text-danger">*</span></label>
									<input type="text" class="form-control">
								</div>
								<div class="mb-3">
									<label class="col-form-label">Campaign Type <span class="text-danger">*</span></label>
									<select class="select2">
										<option>Choose</option>
										<option>Public Relations</option>
										<option>Brand</option>
										<option>Media</option>
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
									<input class="form-control" type="text">
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Period Value <span class="text-danger">*</span></label>
									<input class="form-control" type="text">
								</div>
							</div>
							<div class="col-lg-12">
								<div class="mb-3">
									<label class="col-form-label">Target Audience <span class="text-danger">*</span></label>
									<select class="select" multiple="multiple">
										<option selected="">Small Business</option>
										<option selected="">Corporate Companies</option>
										<option selected="">Urban Apartment</option>
										<option>Business</option>
									</select>
								</div>
								<div class="mb-3">
									<label class="col-form-label">Description <span class="text-danger">*</span></label>
									<textarea class="form-control" rows="4"></textarea>
								</div>
								<div class="mb-3">
									<label class="col-form-label">Attachment <span class="text-danger">*</span></label>
									<div class="drag-attach">
										<input type="file">
										<div class="img-upload">
											<i class="ti ti-file-broken"></i>Upload File
										</div>
									</div>
								</div>
								<div class="mb-3">
									<label class="col-form-label">Uploaded Files</label>
									<div class="upload-file upload-list">
										<div>
											<h6>tes.txt</h6>
											<p>4.25 MB</p>
										</div>
										<a href="javascript:void(0);" class="text-danger"><i class="ti ti-trash-x"></i></a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="button" class="btn btn-primary">Create</button>
					</div>
				</form>
			</div>

		</div>
		<!-- /Add New Campaign -->

		<!-- Edit Campaign -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom">
				<h4>Edit Campaign</h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="campaign-archieve.html">
					<div class="pro-create">
						<div class="row">
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Name <span class="text-danger">*</span></label>
									<input type="text" class="form-control" value="Enhanced brand">
								</div>
								<div class="mb-3">
									<label class="col-form-label">Campaign Type <span class="text-danger">*</span></label>
									<select class="select2">
										<option>Choose</option>
										<option>Public Relations</option>
										<option>Brand</option>
										<option>Media</option>
										<option selected="">Sales</option>
									</select>
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Deal Value<span class="text-danger"> *</span></label>
									<input class="form-control" type="text" value="$12,989">
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
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
							<div class="col-lg-3 col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Period <span class="text-danger">*</span></label>
									<input class="form-control" type="text">
								</div>
							</div>
							<div class="col-lg-3 col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Period Value <span class="text-danger">*</span></label>
									<input class="form-control" type="text">
								</div>
							</div>
							<div class="col-lg-12">
								<div class="mb-3">
									<label class="col-form-label">Target Audience <span class="text-danger">*</span></label>
									<select class="select" multiple="multiple">
										<option selected="">Small Business</option>
										<option selected="">Corporate Companies</option>
										<option selected="">Urban Apartment</option>
										<option>Business</option>
									</select>
								</div>
								<div class="mb-3">
									<label class="col-form-label">Description <span class="text-danger">*</span></label>
									<textarea class="form-control" rows="4"></textarea>
								</div>
								<div class="mb-3">
									<label class="col-form-label">Attachment <span class="text-danger">*</span></label>
									<div class="drag-attach">
										<input type="file">
										<div class="img-upload">
											<i class="ti ti-file-broken"></i>Upload File
										</div>
									</div>
								</div>
								<div class="mb-3">
									<label class="col-form-label">Uploaded Files</label>
									<div class="upload-file upload-list">
										<div>
											<h6>tes.txt</h6>
											<p>4.25 MB</p>
										</div>
										<a href="javascript:void(0);" class="text-danger"><i class="ti ti-trash-x"></i></a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="button" class="btn btn-primary">Create</button>
					</div>
				</form>
			</div>

		</div>
		<!-- /Edit Campaign -->

		<!-- Delete Campaign -->
		<div class="modal fade" id="delete_campaign" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove campaign?</h4>
							<p class="mb-0">Are you sure you want to remove <br> campaign you selected.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="campaign.html" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Campaign -->

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
						<form action="campaign.html">
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


@endsection
