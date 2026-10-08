@extends('layouts.main')
@section('content')
@section('title','Company Overview')

	<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-sm-4">
									<h4 class="page-title">Company Overview</h4>
								</div>
								<div class="col-sm-8 text-sm-end">
									<div class="head-icons">
										<a href="company-details.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

					</div>
				</div>

				<div class="row">
					<div class="col-md-12">

						<!-- Contact User -->
						<div class="contact-head">
							<div class="row align-items-center">
								<div class="col-sm-6">
									<ul class="contact-breadcrumb">
										<li><a href="companies.html"><i class="ti ti-arrow-narrow-left"></i>Company</a>
										</li>
										<li>NovaWave LLC</li>
									</ul>
								</div>
								<div class="col-sm-6 text-sm-end">
									<div class="contact-pagination">
										<p>1 of 40</p>
										<ul>
											<li>
												<a href="company-details.html"><i class="ti ti-chevron-left"></i></a>
											</li>
											<li>
												<a href="company-details.html"><i class="ti ti-chevron-right"></i></a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>

						<div class="card">
							<div class="card-body pb-2">
								<div class="d-flex align-items-center justify-content-between flex-wrap">
									<div class="d-flex align-items-center mb-2">
										<div class="avatar avatar-xxl me-3 flex-shrink-0 border p-2">
											<img src="assets/img/icons/company-icon-01.svg" alt="Image">
										</div>
										<div>
											<h5 class="mb-1">NovaWave LLC</h5>
											<p class="mb-2"><i class="ti ti-map-pin-pin me-1"></i>22, Ave Street,
												Newyork, USA</p>
											<p class="d-inline-flex align-items-center mb-0"><i class="fa-solid fa-star text-warning me-1"></i> 5.0</p>
										</div>
									</div>
									<div class="contacts-action">
										<a href="#" class="btn-icon rating"><i class="fa-solid fa-star"></i></a>
										<a href="#" class="btn btn-danger" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_2">
											<i class="ti ti-circle-plus"></i>Add Deal
										</a>
										<a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_compose">
											<i class="ti ti-mail"></i>Send Email
										</a>
										<a href="chat.html" class="btn-icon">
											<i class="ti ti-brand-hipchat"></i>
										</a>
										<a href="#" class="btn-icon" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit"><i class="ti ti-edit-circle"></i></a>
										<div class="act-dropdown">
											<a href="#" data-bs-toggle="dropdown" aria-expanded="false">
												<i class="ti ti-dots-vertical"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-right">
												<a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_contact"><i class="ti ti-trash text-danger"></i>Delete</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- /Contact User -->

					</div>

					<!-- Contact Sidebar -->
					<div class="col-xl-3 theiaStickySidebar">
						<div class="card">
							<div class="card-body p-3">
								<h6 class="mb-3 fw-semibold">Basic Information</h6>
								<div class="mb-3">
									<div class="d-flex align-items-center mb-3">
										<span class="avatar avatar-xs bg-light-300 p-0 flex-shrink-0 rounded-circle text-dark me-2">
											<i class="ti ti-mail"></i>
										</span>
										<p><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="16587960774156736e777b667a733875797b">[email&#160;protected]</a></p>
									</div>
									<div class="d-flex align-items-center mb-3">
										<span class="avatar avatar-xs bg-light-300 p-0 flex-shrink-0 rounded-circle text-dark me-2">
											<i class="ti ti-phone"></i>
										</span>
										<p>+1 12445-47878</p>
									</div>
									<div class="d-flex align-items-center mb-3">
										<span class="avatar avatar-xs bg-light-300 p-0 flex-shrink-0 rounded-circle text-dark me-2">
											<i class="ti ti-calendar-exclamation"></i>
										</span>
										<p>Created on 5 Jan 2024, 10:30 am</p>
									</div>
								</div>
								<hr>
								<h6 class="mb-3 fw-semibold">Other Information</h6>
								<ul>
									<li class="row mb-3"><span class="col-6">Language</span><span class="col-6">English</span></li>
									<li class="row mb-3"><span class="col-6">Currency</span><span class="col-6">United
											States dollar</span></li>
									<li class="row mb-3"><span class="col-6">Last Modified</span><span class="col-6">27
											Sep 2023, 11:45 pm</span></li>
									<li class="row mb-3"><span class="col-6">Source</span><span class="col-6">Paid
											Campaign</span></li>
								</ul>
								<hr>
								<h6 class="mb-3 fw-semibold">Tags</h6>
								<div class="mb-3">
									<a href="javascript:void(0);" class="badge badge-soft-success fw-medium me-2">Collab</a>
									<a href="javascript:void(0);" class="badge badge-soft-warning fw-medium mb-0">Rated</a>
								</div>
								<hr>
								<div class="d-flex align-items-center justify-content-between flex-wrap">
									<h6 class="mb-3 fw-semibold">Company</h6>
									<a href="javascript:void(0);" class="com-add link-purple fw-medium mb-3" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-circle-plus me-1"></i>Add New</a>
								</div>
								<div class="d-flex align-items-center mb-3">
									<span class="avatar avatar-sm border rounded-circle p-1 me-2">
										<img src="assets/img/icons/company-icon-01.svg" alt="Image">
									</span>
									<div>
										<h6 class="fw-medium">NovaWaveLLC</h6>
										<p><i class="fa-solid fa-star me-1 text-warning me-1"></i>3.5</p>
									</div>
								</div>
								<div class="d-flex align-items-center mb-0">
									<span class="avatar avatar-sm border rounded-circle p-1 me-2">
										<img src="assets/img/icons/company-icon-02.svg" alt="Image">
									</span>
									<div>
										<h6 class="fw-medium">BlueSky Industries</h6>
										<p><i class="fa-solid fa-star me-1 text-warning me-1"></i>4.2</p>
									</div>
								</div>
								<hr>
								<h6 class="mb-3 fw-semibold">Social Profile</h6>
								<ul class="social-info">
									<li>
										<a href="javascript:void(0);"><i class="fa-brands fa-youtube"></i></a>
									</li>
									<li>
										<a href="javascript:void(0);"><i class="fa-brands fa-facebook-f"></i></a>
									</li>
									<li>
										<a href="javascript:void(0);"><i class="fa-brands fa-instagram"></i></a>
									</li>
									<li>
										<a href="javascript:void(0);"><i class="fa-brands fa-whatsapp"></i></a>
									</li>
									<li>
										<a href="javascript:void(0);"><i class="fa-brands fa-pinterest"></i></a>
									</li>
									<li>
										<a href="javascript:void(0);"><i class="fa-brands fa-linkedin"></i></a>
									</li>
								</ul>
								<hr>
								<h6 class="mb-3 fw-semibold">Settings</h6>
								<a href="javascript:void(0);" class="d-block mb-3"><i class="ti ti-share-2 me-1"></i>Share Company</a>
								<a href="javascript:void(0);" class="d-block mb-3"><i class="ti ti-star me-1"></i>Add to
									Favourite</a>
								<a href="javascript:void(0);" class="d-block mb-0" data-bs-toggle="modal" data-bs-target="#delete_contact"><i class="ti ti-trash-x me-1"></i>Delete
									Company</a>
							</div>
						</div>
					</div>
					<!-- /Contact Sidebar -->

					<!-- Contact Details -->
					<div class="col-xl-9">
						<div class="card mb-3">
							<div class="card-body pb-0 pt-2">
								<ul class="nav nav-tabs nav-tabs-bottom" role="tablist">
									<li class="nav-item" role="presentation">
										<a href="#" data-bs-toggle="tab" data-bs-target="#activities" class="nav-link active"><i class="ti ti-alarm-minus me-1"></i>Activities</a>
									</li>
									<li class="nav-item" role="presentation">
										<a href="#" data-bs-toggle="tab" data-bs-target="#notes" class="nav-link"><i class="ti ti-notes me-1"></i>Notes</a>
									</li>
									<li class="nav-item" role="presentation">
										<a href="#" data-bs-toggle="tab" data-bs-target="#calls" class="nav-link"><i class="ti ti-phone me-1"></i>Calls</a>
									</li>
									<li class="nav-item" role="presentation">
										<a href="#" data-bs-toggle="tab" data-bs-target="#files" class="nav-link"><i class="ti ti-file me-1"></i>Files</a>
									</li>
									<li class="nav-item" role="presentation">
										<a href="#" data-bs-toggle="tab" data-bs-target="#email" class="nav-link"><i class="ti ti-mail-check me-1"></i>Email</a>
									</li>
								</ul>
							</div>
						</div>

						<!-- Tab Content -->
						<div class="tab-content pt-0">

							<!-- Activities -->
							<div class="tab-pane active show" id="activities">
								<div class="card">
									<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
										<h4 class="fw-semibold">Activities</h4>
										<div>
											<div class="form-sort mt-0">
												<i class="ti ti-sort-ascending-2"></i>
												<select class="select">
													<option>Sort By Date</option>
													<option>Ascending</option>
													<option>Descending</option>
												</select>
											</div>
										</div>
									</div>
									<div class="card-body">
										<div class="contact-activity">
											<div class="badge badge-soft-purple fs-14 fw-normal shadow-none mb-3"><i class="ti ti-calendar-check me-1"></i>29 Aug 2023</div>
											<div class="card border shadow-none mb-3">
												<div class="card-body p-3">
													<div class="d-flex">
														<span class="avatar avatar-md flex-shrink-0 rounded me-2 bg-pending">
															<i class="ti ti-mail-code"></i>
														</span>
														<div>
															<h6 class="fw-medium mb-1">You sent 1 Message to the
																contact.</h6>
															<p>10:25 pm</p>
														</div>
													</div>
												</div>
											</div>
											<div class="card border shadow-none mb-3">
												<div class="card-body p-3">
													<div class="d-flex">
														<span class="avatar avatar-md flex-shrink-0 rounded me-2 bg-secondary-success">
															<i class="ti ti-phone"></i>
														</span>
														<div>
															<h6 class="fw-medium mb-1">Denwar responded to your
																appointment schedule question by call at 09:30pm.</h6>
															<p>09:25 pm</p>
														</div>
													</div>
												</div>
											</div>
											<div class="card border shadow-none mb-3">
												<div class="card-body p-3">
													<div class="d-flex">
														<span class="avatar avatar-md flex-shrink-0 rounded me-2 bg-orange">
															<i class="ti ti-notes"></i>
														</span>
														<div>
															<h6 class="fw-medium mb-1">Notes added by Antony</h6>
															<p class="mb-1">Please accept my apologies for the
																inconvenience caused. It would be much appreciated if
																it's possible to reschedule to 6:00 PM, or any other day
																that week.</p>
															<p>10.00 pm</p>
														</div>
													</div>
												</div>
											</div>
											<div class="badge badge-soft-purple fs-14 fw-normal shadow-none mb-3"><i class="ti ti-calendar-check me-1"></i>28 Feb 2024</div>
											<div class="card border shadow-none mb-3">
												<div class="card-body p-3">
													<div class="d-flex">
														<span class="avatar avatar-md flex-shrink-0 rounded me-2 bg-info">
															<i class="ti ti-user-pin"></i>
														</span>
														<div>
															<h6 class="fw-medium mb-1 d-inline-flex align-items-center flex-wrap">
																Meeting With <span class="avatar avatar-xs mx-2"><img src="assets/img/profiles/avatar-19.jpg" alt="img"></span> Abraham</h6>
															<p>Schedueled on 05:00 pm</p>
														</div>
													</div>
												</div>
											</div>
											<div class="card border shadow-none mb-3">
												<div class="card-body p-3">
													<div class="d-flex">
														<span class="avatar avatar-md flex-shrink-0 rounded me-2 bg-secondary-success">
															<i class="ti ti-notes"></i>
														</span>
														<div>
															<h6 class="fw-medium mb-1">Drain responded to your
																appointment schedule question.</h6>
															<p>09:25 pm</p>
														</div>
													</div>
												</div>
											</div>
											<div class="badge badge-soft-purple fs-14 fw-normal shadow-none mb-3"><i class="ti ti-calendar-check me-1"></i>Upcoming Activity</div>
											<div class="card border shadow-none mb-0">
												<div class="card-body p-3">
													<div class="d-flex">
														<span class="avatar avatar-md flex-shrink-0 rounded me-2 bg-info">
															<i class="ti ti-user-pin"></i>
														</span>
														<div>
															<h6 class="fw-medium mb-1">Product Meeting</h6>
															<p class="mb-1">A product team meeting is a gathering of the
																cross-functional product team — ideally including team
																members from product, engineering, marketing, and
																customer support.</p>
															<p>25 Jul 2023, 05:00 pm</p>
															<div class="upcoming-info">
																<div class="row">
																	<div class="col-sm-4">
																		<p>Reminder</p>
																		<div class="dropdown">
																			<a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-clock-edit me-1"></i>Reminder<i class="ti ti-chevron-down ms-1"></i></a>
																			<div class="dropdown-menu dropdown-menu-right">
																				<a class="dropdown-item" href="javascript:void(0);">Remainder</a>
																				<a class="dropdown-item" href="javascript:void(0);">1 hr</a>
																				<a class="dropdown-item" href="javascript:void(0);">10 hr</a>
																			</div>
																		</div>
																	</div>
																	<div class="col-sm-4">
																		<p>Task Priority</p>
																		<div class="dropdown">
																			<a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-square-rounded-filled me-1 text-danger circle"></i>High<i class="ti ti-chevron-down ms-1"></i></a>
																			<div class="dropdown-menu dropdown-menu-right">
																				<a class="dropdown-item" href="javascript:void(0);">
																					<i class="ti ti-square-rounded-filled me-1 text-danger circle"></i>High
																				</a>
																				<a class="dropdown-item" href="javascript:void(0);">
																					<i class="ti ti-square-rounded-filled me-1 text-success circle"></i>Low
																				</a>
																			</div>
																		</div>
																	</div>
																	<div class="col-sm-4">
																		<p>Assigned to</p>
																		<div class="dropdown">
																			<a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><img src="assets/img/profiles/avatar-19.jpg" alt="img" class="avatar-xs">John<i class="ti ti-chevron-down ms-1"></i></a>
																			<div class="dropdown-menu dropdown-menu-right">
																				<a class="dropdown-item" href="javascript:void(0);">
																					<img src="assets/img/profiles/avatar-19.jpg" alt="img" class="avatar-xs">John
																				</a>
																				<a class="dropdown-item" href="javascript:void(0);">
																					<img src="assets/img/profiles/avatar-15.jpg" alt="img" class="avatar-xs">Peter
																				</a>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- /Activities -->

							<!-- Notes -->
							<div class="tab-pane fade" id="notes">
								<div class="card">
									<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
										<h4 class="fw-semibold">Notes</h4>
										<div class="d-inline-flex align-items-center">
											<div class="form-sort me-3 mt-0">
												<i class="ti ti-sort-ascending-2"></i>
												<select class="select">
													<option>Sort By Date</option>
													<option>Ascending</option>
													<option>Descending</option>
												</select>
											</div>
											<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add_notes" class="com-add"><i class="ti ti-circle-plus me-1"></i>Add New</a>
										</div>
									</div>
									<div class="card-body">
										<div class="notes-activity">
											<div class="card mb-3">
												<div class="card-body">
													<div class="d-flex align-items-center justify-content-between pb-2">
														<div class="d-inline-flex align-items-center mb-2">
															<span class="avatar avatar-md me-2 flex-shrink-0">
																<img src="assets/img/profiles/avatar-19.jpg" alt="img">
															</span>
															<div>
																<h6 class="fw-medium mb-1">Darlee Robertson</h6>
																<p class="mb-0">15 Sep 2023, 12:10 pm</p>
															</div>
														</div>
														<div class="mb-2">
															<div class="dropdown">
																<a href="#" class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></a>
																<div class="dropdown-menu dropdown-menu-right">
																	<a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-edit text-blue me-1"></i>Edit</a>
																	<a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-trash text-danger me-1"></i>Delete</a>
																</div>
															</div>
														</div>
													</div>
													<h5 class="fw-medium mb-1">Notes added by Antony</h5>
													<p>A project review evaluates the success of an initiative and
														identifies areas for improvement. It can also evaluate a current
														project to determine whether it's on the right track. Or, it can
														determine the success of a completed project. </p>
													<div class="d-inline-flex align-items-center flex-wrap">
														<div class="note-download me-3">
															<div class="note-info">
																<span class="note-icon bg-secondary-success">
																	<i class="ti ti-file-spreadsheet"></i>
																</span>
																<div>
																	<h6 class="fw-medium mb-1">Project Specs.xls</h6>
																	<p>365 KB</p>
																</div>
															</div>
															<a href="javascript:void(0);"><i class="ti ti-arrow-down"></i></a>
														</div>
														<div class="note-download">
															<div class="note-info">
																<span class="note-icon">
																	<img src="assets/img/media/media-35.jpg" alt="img">
																</span>
																<div>
																	<h6 class="fw-medium mb-1">090224.jpg</h6>
																	<p>365 KB</p>
																</div>
															</div>
															<a href="javascript:void(0);"><i class="ti ti-arrow-down"></i></a>
														</div>
													</div>
													<div class="notes-editor">
														<div class="note-edit-wrap">
															<div class="summernote">Write a new comment, send your team
																notification by typing @ followed by their name</div>
															<div class="text-end note-btns">
																<a href="javascript:void(0);" class="btn btn-light add-cancel">Cancel</a>
																<a href="javascript:void(0);" class="btn btn-primary">Save</a>
															</div>
														</div>
														<div class="text-end">
															<a href="javascript:void(0);" class="add-comment link-purple fw-medium"><i class="ti ti-square-plus me-1"></i>Add Comment</a>
														</div>
													</div>
												</div>
											</div>
											<div class="card mb-3">
												<div class="card-body">
													<div class="d-flex align-items-center justify-content-between pb-2">
														<div class="d-inline-flex align-items-center mb-2">
															<span class="avatar avatar-md me-2 flex-shrink-0">
																<img src="assets/img/profiles/avatar-20.jpg" alt="img">
															</span>
															<div>
																<h6 class="fw-medium mb-1">Sharon Roy</h6>
																<p class="mb-0">18 Sep 2023, 09:52 am</p>
															</div>
														</div>
														<div class="mb-2">
															<div class="dropdown">
																<a href="#" class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></a>
																<div class="dropdown-menu dropdown-menu-right">
																	<a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-edit text-blue me-1"></i>Edit</a>
																	<a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-trash text-danger me-1"></i>Delete</a>
																</div>
															</div>
														</div>
													</div>
													<h5 class="fw-medium mb-1">Notes added by Antony</h5>
													<p>A project plan typically contains a list of the essential
														elements of a project, such as stakeholders, scope, timelines,
														estimated cost and communication methods. The project manager
														typically lists the information based on the assignment.</p>
													<div class="d-inline-flex align-items-center flex-wrap">
														<div class="note-download me-3">
															<div class="note-info">
																<span class="note-icon bg-secondary-success">
																	<i class="ti ti-file-text"></i>
																</span>
																<div>
																	<h6 class="fw-medium mb-1">Andrewpass.txt</h6>
																	<p>365 KB</p>
																</div>
															</div>
															<a href="javascript:void(0);"><i class="ti ti-arrow-down"></i></a>
														</div>
													</div>
													<div class="reply-box">
														<p>The best way to get a project done faster is to start sooner.
															A goal without a timeline is just a dream.The goal you set
															must be challenging. At the same time, it should be
															realistic and attainable, not impossible to reach.</p>
														<p>Commented by <span class="text-purple">Aeron</span> on 15 Sep
															2023, 11:15 pm</p>
														<a href="#" class="btn"><i class="ti ti-arrow-back-up-double"></i>Reply</a>
													</div>
													<div class="notes-editor">
														<div class="note-edit-wrap">
															<div class="summernote">Write a new comment, send your team
																notification by typing @ followed by their name</div>
															<div class="text-end note-btns">
																<a href="javascript:void(0);" class="btn btn-light add-cancel">Cancel</a>
																<a href="javascript:void(0);" class="btn btn-primary">Save</a>
															</div>
														</div>
														<div class="text-end">
															<a href="javascript:void(0);" class="add-comment link-purple fw-medium"><i class="ti ti-square-plus me-1"></i>Add Comment</a>
														</div>
													</div>
												</div>
											</div>
											<div class="card mb-0">
												<div class="card-body">
													<div class="d-flex align-items-center justify-content-between pb-2">
														<div class="d-inline-flex align-items-center mb-2">
															<span class="avatar avatar-md me-2 flex-shrink-0">
																<img src="assets/img/profiles/avatar-21.jpg" alt="img">
															</span>
															<div>
																<h6 class="fw-medium mb-1">Vaughan Lewis</h6>
																<p class="mb-0">20 Sep 2023, 10:26 pm</p>
															</div>
														</div>
														<div class="mb-2">
															<div class="dropdown">
																<a href="#" class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></a>
																<div class="dropdown-menu dropdown-menu-right">
																	<a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-edit text-blue me-1"></i>Edit</a>
																	<a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-trash text-danger me-1"></i>Delete</a>
																</div>
															</div>
														</div>
													</div>
													<p>Projects play a crucial role in the success of organizations, and
														their importance cannot be overstated. Whether it's launching a
														new product, improving an existing</p>
													<div class="notes-editor">
														<div class="note-edit-wrap">
															<div class="summernote">Write a new comment, send your team
																notification by typing @ followed by their name</div>
															<div class="text-end note-btns">
																<a href="javascript:void(0);" class="btn btn-light add-cancel">Cancel</a>
																<a href="javascript:void(0);" class="btn btn-primary">Save</a>
															</div>
														</div>
														<div class="text-end">
															<a href="javascript:void(0);" class="add-comment link-purple fw-medium"><i class="ti ti-square-plus me-1"></i>Add Comment</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- /Notes -->

							<!-- Calls -->
							<div class="tab-pane fade" id="calls">
								<div class="card">
									<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
										<h4 class="fw-semibold">Calls</h4>
										<div class="d-inline-flex align-items-center">
											<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create_call" class="com-add"><i class="ti ti-circle-plus me-1"></i>Add New</a>
										</div>
									</div>
									<div class="card-body">
										<div class="card mb-3">
											<div class="card-body">
												<div class="d-sm-flex align-items-center justify-content-between pb-2">
													<div class="d-flex align-items-center mb-2">
														<span class="avatar avatar-md me-2 flex-shrink-0">
															<img src="assets/img/profiles/avatar-19.jpg" alt="img">
														</span>
														<p><span class="text-dark fw-medium">Darlee Robertson</span>
															logged a call on 23 Jul 2023, 10:00 pm</p>
													</div>
													<div class="d-inline-flex align-items-center mb-2">
														<div class="dropdown me-2">
															<a href="#" class="bg-danger py-1" data-bs-toggle="dropdown" aria-expanded="false">Busy<i class="ti ti-chevron-down ms-2"></i></a>
															<div class="dropdown-menu dropdown-menu-right">
																<a class="dropdown-item" href="javascript:void(0);">Busy</a>
																<a class="dropdown-item" href="javascript:void(0);">No
																	Answer</a>
																<a class="dropdown-item" href="javascript:void(0);">Unavailable</a>
																<a class="dropdown-item" href="javascript:void(0);">Wrong Number</a>
																<a class="dropdown-item" href="javascript:void(0);">Left
																	Voice Message</a>
																<a class="dropdown-item" href="javascript:void(0);">Moving Forward</a>
															</div>
														</div>
														<div class="dropdown">
															<a href="#" class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></a>
															<div class="dropdown-menu dropdown-menu-right">
																<a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-edit text-blue me-1"></i>Edit</a>
																<a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-trash text-danger me-1"></i>Delete</a>
															</div>
														</div>
													</div>
												</div>
												<p>A project review evaluates the success of an initiative and
													identifies areas for improvement. It can also evaluate a current
													project to determine whether it's on the right track. Or, it can
													determine the success of a completed project. </p>
											</div>
										</div>
										<div class="card mb-3">
											<div class="card-body">
												<div class="d-sm-flex align-items-center justify-content-between pb-2">
													<div class="d-flex align-items-center mb-2">
														<span class="avatar avatar-md me-2 flex-shrink-0">
															<img src="assets/img/profiles/avatar-20.jpg" alt="img">
														</span>
														<p><span class="text-dark fw-medium">Sharon Roy</span> logged a
															call on 28 Jul 2023, 09:00 pm</p>
													</div>
													<div class="d-inline-flex align-items-center mb-2">
														<div class="dropdown me-2">
															<a href="#" class="bg-pending py-1" data-bs-toggle="dropdown" aria-expanded="false">No
																Answer<i class="ti ti-chevron-down ms-2"></i></a>
															<div class="dropdown-menu dropdown-menu-right">
																<a class="dropdown-item" href="javascript:void(0);">Busy</a>
																<a class="dropdown-item" href="javascript:void(0);">No
																	Answer</a>
																<a class="dropdown-item" href="javascript:void(0);">Unavailable</a>
																<a class="dropdown-item" href="javascript:void(0);">Wrong Number</a>
																<a class="dropdown-item" href="javascript:void(0);">Left
																	Voice Message</a>
																<a class="dropdown-item" href="javascript:void(0);">Moving Forward</a>
															</div>
														</div>
														<div class="dropdown">
															<a href="#" class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></a>
															<div class="dropdown-menu dropdown-menu-right">
																<a class="dropdown-item" href="javascript:void(0);">
																	<i class="ti ti-edit text-blue me-1"></i>Edit
																</a>
																<a class="dropdown-item" href="javascript:void(0);">
																	<i class="ti ti-trash text-danger me-1"></i>Delete
																</a>
															</div>
														</div>
													</div>
												</div>
												<p>A project plan typically contains a list of the essential elements of
													a project, such as stakeholders, scope, timelines, estimated cost
													and communication methods. The project manager typically lists the
													information based on the assignment.</p>
											</div>
										</div>
										<div class="card mb-0">
											<div class="card-body">
												<div class="d-sm-flex align-items-center justify-content-between pb-2">
													<div class="d-flex align-items-center mb-2">
														<span class="avatar avatar-md me-2 flex-shrink-0">
															<img src="assets/img/profiles/avatar-21.jpg" alt="img">
														</span>
														<p><span class="text-dark fw-medium">Vaughan Lewis</span> logged a
															call on 30 Jul 2023, 08:00 pm</p>
													</div>
													<div class="d-inline-flex align-items-center mb-2">
														<div class="dropdown me-2">
															<a href="#" class="bg-pending py-1" data-bs-toggle="dropdown" aria-expanded="false">No
																Answer<i class="ti ti-chevron-down ms-2"></i></a>
															<div class="dropdown-menu dropdown-menu-right">
																<a class="dropdown-item" href="javascript:void(0);">Busy</a>
																<a class="dropdown-item" href="javascript:void(0);">No
																	Answer</a>
																<a class="dropdown-item" href="javascript:void(0);">Unavailable</a>
																<a class="dropdown-item" href="javascript:void(0);">Wrong Number</a>
																<a class="dropdown-item" href="javascript:void(0);">Left
																	Voice Message</a>
																<a class="dropdown-item" href="javascript:void(0);">Moving Forward</a>
															</div>
														</div>
														<div class="dropdown">
															<a href="#" class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></a>
															<div class="dropdown-menu dropdown-menu-right">
																<a class="dropdown-item" href="javascript:void(0);">
																	<i class="ti ti-edit text-blue me-1"></i>Edit
																</a>
																<a class="dropdown-item" href="javascript:void(0);">
																	<i class="ti ti-trash text-danger me-1"></i>Delete
																</a>
															</div>
														</div>
													</div>
												</div>
												<p>Projects play a crucial role in the success of organizations, and
													their importance cannot be overstated. Whether it's launching a new
													product, improving an existing</p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- /Calls -->

							<!-- Files -->
							<div class="tab-pane fade" id="files">
								<div class="card">
									<div class="card-header">
										<h4 class="fw-semibold">Files</h4>
									</div>
									<div class="card-body">
										<div class="card border mb-3">
											<div class="card-body pb-0">
												<div class="row align-items-center">
													<div class="col-md-8">
														<div class="mb-3">
															<h4 class="fw-medium mb-1">Manage Documents</h4>
															<p>Send customizable quotes, proposals and contracts to
																close deals faster.</p>
														</div>
													</div>
													<div class="col-md-4 text-md-end">
														<div class="mb-3">
															<a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#new_file">Create Document</a>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card border shadow-none mb-3">
											<div class="card-body pb-0">
												<div class="row align-items-center">
													<div class="col-md-8">
														<div class="mb-3">
															<h4 class="fw-medium mb-1">Collier-Turner Proposal</h4>
															<p>Send customizable quotes, proposals and contracts to
																close deals faster.</p>
															<div class="d-flex align-items-center">
																<span class="avatar avatar-md me-2 flex-shrink-0">
																	<img src="assets/img/profiles/avatar-21.jpg" alt="img" class="rounded-circle">
																</span>
																<div>
																	<span class="fs-12">Owner</span>
																	<p>Vaughan Lewis</p>
																</div>
															</div>
														</div>
													</div>
													<div class="col-md-4 text-md-end">
														<div class="mb-3 d-inline-flex align-items-center">
															<span class="badge badge-danger-light me-1">Proposal</span>
															<span class="badge bg-pending priority-badge me-1">Draft</span>
															<div class="dropdown">
																<a href="#" class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></a>
																<div class="dropdown-menu dropdown-menu-right">
																	<a class="dropdown-item" href="javascript:void(0);">
																		<i class="ti ti-edit text-blue me-1"></i>Edit
																	</a>
																	<a class="dropdown-item" href="javascript:void(0);">
																		<i class="ti ti-trash text-danger me-1"></i>Delete
																	</a>
																	<a class="dropdown-item" href="javascript:void(0);">
																		<i class="ti ti-download text-info me-1"></i>Download
																	</a>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card border shadow-none mb-3">
											<div class="card-body pb-0">
												<div class="row align-items-center">
													<div class="col-md-8">
														<div class="mb-3">
															<h4 class="fw-medium mb-1">Collier-Turner Proposal</h4>
															<p>Send customizable quotes, proposals and contracts to
																close deals faster.</p>
															<div class="d-flex align-items-center">
																<span class="avatar avatar-md me-2 flex-shrink-0">
																	<img src="assets/img/profiles/avatar-01.jpg" alt="img" class="rounded-circle">
																</span>
																<div>
																	<span class="fs-12">Owner</span>
																	<p>Jessica Louise</p>
																</div>
															</div>
														</div>
													</div>
													<div class="col-md-4 text-md-end">
														<div class="mb-3 d-inline-flex align-items-center">
															<span class="badge badge-purple-light me-1">Quote</span>
															<span class="badge bg-success me-1">Sent</span>
															<div class="dropdown">
																<a href="#" class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></a>
																<div class="dropdown-menu dropdown-menu-right">
																	<a class="dropdown-item" href="javascript:void(0);">
																		<i class="ti ti-edit text-blue me-1"></i>Edit
																	</a>
																	<a class="dropdown-item" href="javascript:void(0);">
																		<i class="ti ti-trash text-danger me-1"></i>Delete
																	</a>
																	<a class="dropdown-item" href="javascript:void(0);">
																		<i class="ti ti-download text-info me-1"></i>Download
																	</a>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card border shadow-none mb-0">
											<div class="card-body pb-0">
												<div class="row align-items-center">
													<div class="col-md-8">
														<div class="mb-3">
															<h4 class="fw-medium mb-1">Collier-Turner Proposal</h4>
															<p>Send customizable quotes, proposals and contracts to
																close deals faster.</p>
															<div class="d-flex align-items-center">
																<span class="avatar avatar-md me-2 flex-shrink-0">
																	<img src="assets/img/profiles/avatar-22.jpg" alt="img" class="rounded-circle">
																</span>
																<div>
																	<span class="fs-12">Owner</span>
																	<p>Dawn Merhca</p>
																</div>
															</div>
														</div>
													</div>
													<div class="col-md-4 text-md-end">
														<div class="mb-3 d-inline-flex align-items-center">
															<span class="badge badge-danger-light me-1">Proposal</span>
															<span class="badge bg-pending priority-badge me-1">Draft</span>
															<div class="dropdown">
																<a href="#" class="p-0 btn btn-icon btn-sm d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></a>
																<div class="dropdown-menu dropdown-menu-right">
																	<a class="dropdown-item" href="javascript:void(0);">
																		<i class="ti ti-edit text-blue me-1"></i>Edit
																	</a>
																	<a class="dropdown-item" href="javascript:void(0);">
																		<i class="ti ti-trash text-danger me-1"></i>Delete
																	</a>
																	<a class="dropdown-item" href="javascript:void(0);">
																		<i class="ti ti-download text-info me-1"></i>Download
																	</a>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- /Files -->

							<!-- Email -->
							<div class="tab-pane fade" id="email">
								<div class="card">
									<div class="card-header d-flex align-items-center justify-content-between flex-wrap">
										<h4 class="fw-semibold">Email</h4>
										<div class="d-inline-flex align-items-center">
											<a href="javascript:void(0);" class="link-purple fw-medium" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-custom-class="tooltip-dark" data-bs-original-title="There are no email accounts configured, Please configured your email account in order to Send/ Create EMails"><i class="ti ti-circle-plus me-1"></i>Create Email</a>
										</div>
									</div>
									<div class="card-body">
										<div class="card border mb-0">
											<div class="card-body pb-0">
												<div class="row align-items-center">
													<div class="col-md-8">
														<div class="mb-3">
															<h4 class="fw-medium mb-1">Manage Emails</h4>
															<p>You can send and reply to emails directly via this
																section.</p>
														</div>
													</div>
													<div class="col-md-4 text-md-end">
														<div class="mb-3">
															<a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create_email">Connect Account</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- /Email -->

						</div>
						<!-- /Tab Content -->

					</div>
					<!-- /Contact Details -->

				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->

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
								<i class="ti ti-building"></i>
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

		<!-- Add Note -->
		<div class="modal custom-modal fade modal-padding" id="add_notes" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add New Notes</h5>
						<button type="button" class="btn-close position-static" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<form action="company-details.html">
							<div class="mb-3">
								<label class="col-form-label">Title <span class="text-danger"> *</span></label>
								<input class="form-control" type="text">
							</div>
							<div class="mb-3">
								<label class="col-form-label">Note <span class="text-danger"> *</span></label>
								<textarea class="form-control" rows="4"></textarea>
							</div>
							<div class="mb-3">
								<label class="col-form-label">Attachment <span class="text-danger"> *</span></label>
								<div class="drag-attach">
									<input type="file">
									<div class="img-upload">
										<i class="ti ti-file-broken"></i>Upload File
									</div>
								</div>
							</div>
							<div class="mb-3">
								<label class="col-form-label">Uploaded Files</label>
								<div class="upload-file">
									<h6>Projectneonals teyys.xls</h6>
									<p>4.25 MB</p>
									<div class="progress">
										<div class="progress-bar bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<p class="black-text">45%</p>
								</div>
								<div class="upload-file upload-list">
									<div>
										<h6>tes.txt</h6>
										<p>4.25 MB</p>
									</div>
									<a href="javascript:void(0);" class="text-danger"><i class="ti ti-trash-x"></i></a>
								</div>
							</div>
							<div class="col-lg-12 text-end modal-btn">
								<a class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<button class="btn btn-primary" type="submit">Confirm</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Add Note -->

		<!-- Create Call Log -->
		<div class="modal custom-modal fade modal-padding" id="create_call" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Create Call Log</h5>
						<button type="button" class="btn-close position-static" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<form action="company-details.html">
							<div class="row">
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Status <span class="text-danger"> *</span></label>
										<select class="select">
											<option>Busy</option>
											<option>Unavailable</option>
											<option>No Answer</option>
											<option>Wrong Number</option>
											<option>Left Voice Message</option>
											<option>Moving Forward</option>
										</select>
									</div>
									<div class="mb-3">
										<label class="col-form-label">Follow Up Date <span class="text-danger">
												*</span></label>
										<div class="icon-form">
											<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
											<input type="text" class="form-control datetimepicker" placeholder="">
										</div>
									</div>
									<div class="mb-3">
										<label class="col-form-label">Note <span class="text-danger"> *</span></label>
										<textarea class="form-control" rows="4" placeholder="Add text"></textarea>
									</div>
									<div class="mb-3">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span> Create a followup task
										</label>
									</div>
									<div class="text-end modal-btn">
										<a class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
										<button class="btn btn-primary" type="submit">Confirm</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Create Call Log -->

		<!-- Add File -->
		<div class="modal custom-modal fade custom-modal-two modal-padding" id="new_file" role="dialog">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Create New File</h5>
						<button type="button" class="btn-close position-static" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="add-info-fieldset">

							<div class="add-details-wizard">
								<ul class="progress-bar-wizard">
									<li class="active">
										<span><i class="ti ti-file"></i></span>
										<div class="multi-step-info">
											<h6>Basic Info</h6>
										</div>
									</li>
									<li>
										<span><i class="ti ti-circle-plus"></i></span>
										<div class="multi-step-info">
											<h6>Add Recipient</h6>
										</div>
									</li>
								</ul>
							</div>
							<fieldset id="first-field-file">
								<form action="company-details.html">
									<div class="contact-input-set">
										<div class="row">
											<div class="col-md-12">
												<div class="mb-3">
													<label class="col-form-label">Choose Deal <span class="text-danger">*</span></label>
													<select class="select">
														<option>Select</option>
														<option>Collins</option>
														<option>Wisozk</option>
														<option>Walter</option>
													</select>
												</div>
											</div>
											<div class="col-md-6">
												<div class="mb-3">
													<label class="col-form-label">Document Type <span class="text-danger">*</span></label>
													<select class="select">
														<option>Select</option>
														<option>Contract</option>
														<option>Proposal</option>
														<option>Quote</option>
													</select>
												</div>
											</div>
											<div class="col-md-6">
												<div class="mb-3">
													<label class="col-form-label">Owner <span class="text-danger">*</span></label>
													<select class="select">
														<option>Select</option>
														<option>Admin</option>
														<option>Jackson Daniel Byrne</option>
													</select>
												</div>
											</div>
											<div class="col-md-12">
												<div class="mb-3">
													<label class="col-form-label">Title <span class="text-danger">
															*</span></label>
													<input class="form-control" type="text">
												</div>
											</div>
											<div class="col-lg-12">
												<div class="signature-wrap">
													<h4>Signature</h4>
													<ul class="nav sign-item">
														<li class="nav-item">
															<span class=" mb-0" data-bs-toggle="tab" data-bs-target="#nosign">
																<input type="radio" class="status-radio" id="sign1" name="email">
																<label for="sign1"><span class="sign-title">No
																		Signature</span>This document does not require a
																	signature before acceptance.</label>
															</span>
														</li>
														<li class="nav-item">
															<span class="active mb-0" data-bs-toggle="tab" data-bs-target="#use-esign">
																<input type="radio" class="status-radio" id="sign2" name="email" checked="">
																<label for="sign2"><span class="sign-title">Use
																		e-signature</span>This document require
																	e-signature before acceptance.</label>
															</span>
														</li>
													</ul>
													<div class="tab-content">
														<div class="tab-pane show active" id="use-esign">
															<div class="input-block mb-0">
																<label class="col-form-label">Document Signers <span class="text-danger">*</span></label>
															</div>
															<div class="sign-content">
																<div class="row">
																	<div class="col-md-6">
																		<div class="mb-3">
																			<input class="form-control" type="text" placeholder="Enter Name">
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="d-flex align-items-center">
																			<div class="float-none mb-3 me-3 w-100">
																				<input class="form-control" type="text" placeholder="Email Address">
																			</div>
																			<div class="input-btn mb-3">
																				<a href="javascript:void(0);" class="add-sign"><i class="ti ti-circle-plus"></i></a>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="col-md-12">
												<div class="input-block mb-3">
													<label class="col-form-label">Content <span class="text-danger">
															*</span></label>
													<textarea class="form-control" rows="3" placeholder="Add Content"></textarea>
												</div>
											</div>
											<div class="col-lg-12 text-end form-wizard-button modal-btn">
												<button class="btn btn-light" type="reset">Reset</button>
												<button class="btn btn-primary wizard-next-btn" type="button">Next</button>
											</div>
										</div>
									</div>
								</form>
							</fieldset>
							<fieldset>
								<form action="company-details.html">
									<div class="contact-input-set">
										<div class="row">
											<div class="col-lg-12">
												<div class="signature-wrap">
													<h4 class="mb-2">Send the document to following signers</h4>
													<p>In order to send the document to the signers</p>
													<div class="input-block mb-0">
														<label class="col-form-label">Recipients (Additional
															recipients)</label>
													</div>
													<div class="sign-content">
														<div class="row">
															<div class="col-md-6">
																<div class="mb-3">
																	<input class="form-control" type="text" placeholder="Enter Name">
																</div>
															</div>
															<div class="col-md-6">
																<div class="d-flex align-items-center">
																	<div class="float-none mb-3 me-3 w-100">
																		<input class="form-control" type="text" placeholder="Email Address">
																	</div>
																	<div class="input-btn mb-3">
																		<a href="javascript:void(0);" class="add-sign"><i class="ti ti-circle-plus"></i></a>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="col-lg-12">
												<div class="mb-3">
													<label class="col-form-label">Message Subject <span class="text-danger"> *</span></label>
													<input class="form-control" type="text" placeholder="Enter Subject">
												</div>
												<div class="mb-3">
													<label class="col-form-label">Message Text <span class="text-danger"> *</span></label>
													<textarea class="form-control" rows="3" placeholder="Your document is ready"></textarea>
												</div>
												<button class="btn btn-light mb-3">Send Now</button>
												<div class="send-success">
													<p><i class="ti ti-circle-check"></i> Document sent successfully to
														the selected recipients</p>
												</div>
											</div>
											<div class="col-lg-12 text-end form-wizard-button modal-btn">
												<button class="btn btn-light" type="reset">Cancel</button>
												<button class="btn btn-primary" type="button" data-bs-dismiss="modal">Save & Next</button>
											</div>
										</div>
									</div>
								</form>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Add File -->

		<!-- Connect Account -->
		<div class="modal custom-modal fade" id="create_email" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Connect Account</h5>
						<button type="button" class="btn-close position-static" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="mb-3">
							<label class="col-form-label">Account type <span class="text-danger"> *</span></label>
							<select class="select">
								<option>Gmail</option>
								<option>Outlook</option>
								<option>Imap</option>
							</select>
						</div>
						<div class="mb-3">
							<h5 class="form-title">Sync emails from</h5>
							<div class="sync-radio">
								<div class="radio-item">
									<input type="radio" class="status-radio" id="test1" name="radio-group" checked="">
									<label for="test1">Now</label>
								</div>
								<div class="radio-item">
									<input type="radio" class="status-radio" id="test2" name="radio-group">
									<label for="test2">1 Month Ago</label>
								</div>
								<div class="radio-item">
									<input type="radio" class="status-radio" id="test3" name="radio-group">
									<label for="test3">3 Month Ago</label>
								</div>
								<div class="radio-item">
									<input type="radio" class="status-radio" id="test4" name="radio-group">
									<label for="test4">6 Month Ago</label>
								</div>
							</div>
						</div>
						<div class="col-lg-12 text-end modal-btn">
							<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
							<button class="btn btn-primary" data-bs-target="#success_mail" data-bs-toggle="modal" data-bs-dismiss="modal">Connect Account</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Connect Account -->

		<!-- Success Company -->
		<div class="modal custom-modal fade" id="success_mail" role="dialog">
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
								<i class="ti ti-mail-opened"></i>
							</div>
							<h3>Email Connected Successfully!!!</h3>
							<p>Email Account is configured with “<a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="412439202c312d24012439202c312d246f222e2c">[email&#160;protected]</a>”. Now you can access email.</p>
							<div class="col-lg-12 text-center modal-btn">
								<a href="company-details.html" class="btn btn-primary">Go to email</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Success Company -->

		<!-- Access -->
		<div class="modal custom-modal fade" id="access_view" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Access For</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="company-details.html">
							<div class="mb-2 icon-form">
								<span class="form-icon"><i class="ti ti-search"></i></span>
								<input type="text" class="form-control" placeholder="Search">
							</div>
							<div class="access-wrap">
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
							<div class="modal-btn text-end">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-primary">Confirm</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Access -->

		<!-- Add Compose -->
		<div class="modal custom-modal fade" id="add_compose" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add Compose</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="#">
							<div class="mb-3">
								<input type="email" placeholder="To" class="form-control">
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="mb-3">
										<input type="email" placeholder="Cc" class="form-control">
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3">
										<input type="email" placeholder="Bcc" class="form-control">
									</div>
								</div>
							</div>
							<div class="mb-3">
								<input type="text" placeholder="Subject" class="form-control">
							</div>
							<div class="mb-3">
								<div class="summernote"></div>
							</div>
							<div class="mb-3">
								<div class="text-center">
									<button class="btn btn-primary"><span>Send</span><i class="fa-solid fa-paper-plane ms-1"></i></button>
									<button class="btn btn-primary" type="button"><span>Draft</span> <i class="fa-regular fa-floppy-disk ms-1"></i></button>
									<button class="btn btn-primary" type="button"><span>Delete</span> <i class="fa-regular fa-trash-can ms-1"></i></button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Add Compose -->

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
													<option>English</option>
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

		<!-- Add Company -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Add New Company</h5>
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
												<input type="text" class="form-control">
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
												<input type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">Phone 1 <span class="text-danger">*</span></label>
												<input type="text" class="form-control">
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
													<option>Collins</option>
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
													<option>Dollar</option>
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
													<option>English</option>
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
												<input type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<label class="col-form-label">Country</label>
												<select class="select">
													<option>Choose</option>
													<option>USA</option>
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
													<option>California</option>
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
													<option>Los Angeles</option>
													<option>San Diego</option>
													<option>Fresno</option>
													<option>San Francisco</option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-0">
												<label class="col-form-label">Zipcode </label>
												<input type="text" class="form-control">
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
												<input type="text" class="form-control">
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
						<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create_success">Create</button>
					</div>
				</form>
			</div>
		</div>
		<!-- /Add Company -->

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
						<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create_success">Create</button>
					</div>
				</form>
			</div>

		</div>
		<!-- /Add New Deals -->

@endsection
