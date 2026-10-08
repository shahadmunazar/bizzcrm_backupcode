@extends('layouts.main')
@section('content')
@section('title','Activities')
<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-4">
									<h4 class="page-title">Activities<span class="count-title">123</span></h4>
								</div>
								<div class="col-8 text-end">
									<div class="head-icons">
										<a href="activities.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
											<i class="ti ti-refresh-dot"></i>
										</a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
											<i class="ti ti-chevrons-up"></i>
										</a>
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
								<div class="card">

							<div class="card-header">
								<!-- Search -->
								<div class="row align-items-center">
									<div class="col-sm-4">
										<div class="icon-form mb-3 mb-sm-0">
											<span class="form-icon"><i class="ti ti-search"></i></span>
											<input type="text" class="form-control" placeholder="Search Activities">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="text-sm-end">
											<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-square-rounded-plus me-2"></i>Add New Activity</a>
										</div>
									</div>
								</div>
								<!-- /Search -->
							</div>

							<div class="card-body">

								<!-- Filter -->
								<div class="d-flex align-items-center justify-content-between flex-wrap mb-4 row-gap-2">
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<div class="d-flex align-items-center flex-wrap row-gap-2">
											<h4 class="mb-0 me-3">All Activity</h4>
											<div class="active-list">
												<ul class="mb-0">
													<li>
														<a href="activity-calls.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Calls"><i class="ti ti-phone"></i></a>
													</li>
													<li>
														<a href="activity-mail.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Mail"><i class="ti ti-mail"></i></a>
													</li>
													<li>
														<a href="activity-task.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Task"><i class="ti ti-subtask"></i></a>
													</li>
													<li>
														<a href="activity-meeting.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Meeting"><i class="ti ti-user-share"></i></a>
													</li>
												</ul>
											</div>
										</div>
									</div>
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
										<div class="dropdown me-2">
											<a href="javascript:void(0);" class="btn bg-soft-purple text-purple" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-columns-3 me-2"></i>Manage Columns</a>
											<div class="dropdown-menu  dropdown-menu-md-end dropdown-md p-3">
												<h4 class="mb-2 fw-semibold">Want to manage datatables?</h4>
												<p class="mb-3">Please drag and drop your column to reorder your table
													and enable see option as you want.</p>
												<div class="border-top pt-3">
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Title</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-name" class="check">
															<label for="col-name" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Activity Type</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-phone" class="check">
															<label for="col-phone" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Due Date</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-email" class="check">
															<label for="col-email" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Owner</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-tag" class="check">
															<label for="col-tag" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Created at</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-loc" class="check">
															<label for="col-loc" class="checktoggle"></label>
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
											<div class="filter-dropdown-menu dropdown-menu  dropdown-menu-md-end p-4">
												<div class="filter-set-view">
													<div class="filter-set-head">
														<h4><i class="ti ti-filter-share"></i>Filter</h4>
													</div>
													<div class="accordion" id="accordionExample">
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" data-bs-toggle="collapse" data-bs-target="#collapse" aria-expanded="true" aria-controls="collapse">Title</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse show" id="collapse" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<div class="mb-2 icon-form">
																		<span class="form-icon"><i class="ti ti-search"></i></span>
																		<input type="text" class="form-control" placeholder="Search Name">
																	</div>
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
																					<span class="checkmarks"></span>
																					We scheduled a meeting
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Store and manage contact
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Built landing pages
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Discussed budget proposal
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Discussed about team
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">Activity Type</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="collapseThree" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
																					<span class="checkmarks"></span>
																					Meeting
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
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Task
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Email
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseone" aria-expanded="false" aria-controls="collapseone">Due Date</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="collapseone" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
																					<span class="checkmarks"></span>
																					25 Sep 2023, 12:12 pm
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					29 Sep 2023, 04:12 pm
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					11 Oct 2023, 05:00 pm
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					19 Oct 2023, 02:35 pm
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
																					Monty Beer
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Bradtke
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Swaniawski
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapsethree" aria-expanded="false" aria-controls="collapsethree">Created Date</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="collapsethree" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
																					<span class="checkmarks"></span>
																					22 Sep 2023, 10:14 am
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					27 Sep 2023, 03:26 pm
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					03 Oct 2023, 03:53 pm
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
																<a href="activities.html" class="btn btn-primary">Filter</a>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- /Filter -->

								<!-- Activity List -->
								<div class="table-responsive custom-table">
									<table class="table" id="activity-list">
										<thead class="thead-light">
											<tr>
												<th class="no-sort">
													<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
												</th>
												<th>Title</th>
												<th>Activity Type</th>
												<th>Due Date</th>
												<th>Owner</th>
												<th>Created At</th>
												<th>Action</th>
										</tr></thead>
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
								<!-- /Activity List -->

							</div>
						</div>
					</div>
				</div>

			</div>
		</div>

	</div>
</div>
		<!-- /Page Wrapper -->

		<!-- Add New Activity -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h4>Add New Activity </h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="activities.html">
					<div>
						<div class="row">
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Title <span class="text-danger">*</span></label>
									<input type="text" class="form-control">
								</div>
							</div>
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Activity Type <span class="text-danger">*</span></label>
									<ul class="radio-activity">
										<li>
											<div class="active-type">
												<input type="radio" id="call" name="status" checked="">
												<label for="call"><i class="ti ti-phone"></i>Calls</label>
											</div>
										</li>
										<li>
											<div class="active-type">
												<input type="radio" id="mail" name="status">
												<label for="mail"><i class="ti ti-mail"></i>Email</label>
											</div>
										</li>
										<li>
											<div class="active-type">
												<input type="radio" id="task" name="status">
												<label for="task"><i class="ti ti-subtask"></i>Task</label>
											</div>
										</li>
										<li>
											<div class="active-type">
												<input type="radio" id="shares" name="status">
												<label for="shares"><i class="ti ti-user-share"></i>Meeting</label>
											</div>
										</li>
									</ul>
								</div>
							</div>
							<div class="col-md-6">
								<label class="col-form-label">Due Date <span class="text-danger">*</span></label>
								<div class="mb-3 icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="form-control datetimepicker">
								</div>
							</div>
							<div class="col-md-6">
								<label class="col-form-label">Time <span class="text-danger">*</span></label>
								<div class="mb-3 icon-form">
									<span class="form-icon"><i class="ti ti-clock-hour-10"></i></span>
									<input type="text" class="form-control datetimepicker-time">
								</div>
							</div>
							<div class="col-md-6">
								<label class="col-form-label">Reminder <span class="text-danger">*</span></label>
								<div class="mb-3 icon-form">
									<span class="form-icon"><i class="ti ti-bell-ringing"></i></span>
									<input type="text" class="form-control">
								</div>
							</div>
							<div class="col-md-6">
								<div class="d-flex align-items-center">
									<div class="mb-3 w-100">
										<label class="col-form-label">&nbsp;</label>
										<select class="select">
											<option>Select</option>
											<option>Minutes</option>
											<option>Hours</option>
										</select>
									</div>
									<div class="mb-3 time-text">
										<label class="col-form-label">&nbsp;</label>
										<p>Before Due</p>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Owner <span class="text-danger">*</span></label>
									<select class="select">
										<option>Select</option>
										<option>Hendry Milner</option>
										<option>Bradtke</option>
										<option>Sally</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Guests <span class="text-danger">*</span></label>
									<select class="multiple-img" multiple="multiple">
										<option data-image="assets/img/profiles/avatar-19.jpg" selected="">Darlee Robertson
										</option>
										<option data-image="assets/img/profiles/avatar-20.jpg">Sharon Roy</option>
										<option data-image="assets/img/profiles/avatar-21.jpg">Vaughan Lewis</option>
										<option data-image="assets/img/profiles/avatar-23.jpg">Jessica Louise</option>
										<option data-image="assets/img/profiles/avatar-16.jpg">Carol Thomas</option>
									</select>
								</div>
							</div>
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Description <span class="text-danger">*</span></label>
									<div class="summernote"></div>
								</div>
								<div class="mb-3">
									<div class="d-flex align-items-center justify-content-between">
										<label class="col-form-label">Deals</label>
										<a href="#" class="label-add" data-bs-toggle="modal" data-bs-target="#add_deal"><i class="ti ti-square-rounded-plus"></i>Add
											New</a>
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
								<div class="mb-3">
									<div class="d-flex align-items-center justify-content-between">
										<label class="col-form-label">Contacts</label>
										<a href="#" class="label-add" data-bs-toggle="modal" data-bs-target="#add_contacts"><i class="ti ti-square-rounded-plus"></i>Add
											New</a>
									</div>
									<select class="select">
										<option>Select</option>
										<option>Collins</option>
										<option>Konopelski </option>
										<option>Adams</option>
									</select>
								</div>
								<div class="mb-3">
									<div class="d-flex align-items-center justify-content-between">
										<label class="col-form-label">Companies</label>
										<a href="#" class="label-add" data-bs-toggle="modal" data-bs-target="#add_company"><i class="ti ti-square-rounded-plus"></i>Add
											New</a>
									</div>
									<select class="select">
										<option>Select</option>
										<option>NovaWave LLC</option>
										<option>Silver Hawk</option>
										<option>Harbor View</option>
									</select>
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
		<!-- /Add New Activity -->

		<!-- Edit Activity -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom">
				<div>
					<h4 class="mb-2">We scheduled a meeting for next week</h4>
					<p>Commented by <span>Aeron</span> on 15 Sep 2023, 11:15 pm</p>
				</div>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="activity-calls.html">
					<div class="pro-create">
						<div class="tab-activity">
							<ul class="nav">
								<li>
									<a href="#" data-bs-toggle="tab" data-bs-target="#activity" class="active">Activity</a>
								</li>
								<li>
									<a href="#" data-bs-toggle="tab" data-bs-target="#comments">Comments<span>1</span></a>
								</li>
							</ul>
						</div>
						<div class="tab-content">
							<div class="tab-pane fade" id="comments">
								<div class="comment-wrap mb-4">
									<h6>The best way to get a project done faster is to start sooner. A goal without a
										timeline is just a dream.The goal you set must be challenging. At the same time,
										it should be realistic and attainable, not impossible to reach.</h6>
									<p>Commented by <span>Aeron</span> on 15 Sep 2023, 11:15 pm</p>
								</div>
							</div>
							<div class="tab-pane show active" id="activity">
								<div class="row">
									<div class="col-md-12">
										<div class="mb-3">
											<label class="col-form-label">Title <span class="text-danger">*</span></label>
											<input type="text" class="form-control">
										</div>
									</div>
									<div class="col-md-12">
										<div class="mb-3">
											<label class="col-form-label">Activity Type <span class="text-danger">*</span></label>
											<ul class="radio-activity">
												<li>
													<div class="active-type">
														<input type="radio" id="call2" name="status" checked="">
														<label for="call2"><i class="ti ti-phone"></i>Calls</label>
													</div>
												</li>
												<li>
													<div class="active-type">
														<input type="radio" id="mail2" name="status">
														<label for="mail2"><i class="ti ti-mail"></i>Email</label>
													</div>
												</li>
												<li>
													<div class="active-type">
														<input type="radio" id="task2" name="status">
														<label for="task2"><i class="ti ti-subtask"></i>Task</label>
													</div>
												</li>
												<li>
													<div class="active-type">
														<input type="radio" id="shares2" name="status">
														<label for="shares2"><i class="ti ti-user-share"></i>Meeting</label>
													</div>
												</li>
											</ul>
										</div>
									</div>
									<div class="col-md-6">
										<label class="col-form-label">Due Date <span class="text-danger">*</span></label>
										<div class="mb-3 icon-form">
											<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
											<input type="text" class="form-control datetimepicker">
										</div>
									</div>
									<div class="col-md-6">
										<label class="col-form-label">Time <span class="text-danger">*</span></label>
										<div class="mb-3 icon-form">
											<span class="form-icon"><i class="ti ti-clock-hour-10"></i></span>
											<input type="text" class="form-control datetimepicker-time">
										</div>
									</div>
									<div class="col-md-6">
										<label class="col-form-label">Reminder <span class="text-danger">*</span></label>
										<div class="mb-3 icon-form">
											<span class="form-icon"><i class="ti ti-bell-ringing"></i></span>
											<input type="text" class="form-control">
										</div>
									</div>
									<div class="col-md-6">
										<div class="d-flex align-items-center">
											<div class="mb-3 w-100">
												<label class="col-form-label">&nbsp;</label>
												<select class="select">
													<option>Select</option>
													<option>Minutes</option>
													<option>Hours</option>
												</select>
											</div>
											<div class="mb-3 time-text">
												<label class="col-form-label">&nbsp;</label>
												<p>Before Due</p>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label class="col-form-label">Owner <span class="text-danger">*</span></label>
											<select class="select">
												<option>Select</option>
												<option>Hendry Milner</option>
												<option>Bradtke</option>
												<option>Sally</option>
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label class="col-form-label">Guests <span class="text-danger">*</span></label>
											<select class="select" multiple="multiple">
												<option selected="">Darlee Robertson</option>
												<option>Collins</option>
												<option>Sally</option>
											</select>
										</div>
									</div>
									<div class="col-md-12">
										<div class="mb-3">
											<label class="col-form-label">Description <span class="text-danger">*</span></label>
											<div class="summernote"></div>
										</div>
										<div class="mb-3">
											<div class="d-flex align-items-center justify-content-between">
												<label class="col-form-label">Deals</label>
												<a href="#" class="label-add" data-bs-toggle="modal" data-bs-target="#add_deal"><i class="ti ti-square-rounded-plus"></i>Add New</a>
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
										<div class="mb-3">
											<div class="d-flex align-items-center justify-content-between">
												<label class="col-form-label">Contacts</label>
												<a href="#" class="label-add" data-bs-toggle="modal" data-bs-target="#add_contacts"><i class="ti ti-square-rounded-plus"></i>Add New</a>
											</div>
											<select class="select">
												<option>Select</option>
												<option>Darlee Robertson</option>
												<option>Sharon Roy</option>
												<option>Jessica Louise</option>
											</select>
										</div>
										<div class="mb-3">
											<div class="d-flex align-items-center justify-content-between">
												<label class="col-form-label">Companies</label>
												<a href="#" class="label-add" data-bs-toggle="modal" data-bs-target="#add_company"><i class="ti ti-square-rounded-plus"></i>Add New</a>
											</div>
											<select class="select">
												<option>Select</option>
												<option>NovaWave LLC</option>
												<option>Silver Hawk</option>
												<option>Harbor View</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="button" class="btn btn-primary">Save Changes</button>
					</div>
				</form>
			</div>
		</div>
		<!-- /Edit Activity -->

		<!-- Delete Activity -->
		<div class="modal fade" id="delete_activity" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Activity?</h4>
							<p class="mb-0">Are you sure you want to remove <br> activity you selected.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="activities.html" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Activity -->

		<!-- Add Contacts -->
		<div class="modal custom-modal fade" id="add_contacts" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add Contacts</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="activities.html">
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
		<!-- /Add Contacts -->

		<!-- Add Deals -->
		<div class="modal custom-modal fade" id="add_deal" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add Deals</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="activities.html">
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
												<a href="#">Collins</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<a href="#">Konopelski</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<a href="#">Adams</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<a href="#">Schumm</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<a href="#">Wisozk</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
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
		<!-- /Add Deals -->

		<!-- Add Company -->
		<div class="modal custom-modal fade" id="add_company" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add Company</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="activities.html">
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
												<img src="assets/img/icons/company-icon-01.svg" alt="">
												<a href="#">NovaWave LLC</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<img src="assets/img/icons/company-icon-02.svg" alt="">
												<a href="#">BlueSky Industries</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<img src="assets/img/icons/company-icon-03.svg" alt="">
												<a href="#">Silver Hawk</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<img src="assets/img/icons/company-icon-04.svg" alt="">
												<a href="#">Summit Peak</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<img src="assets/img/icons/company-icon-05.svg" alt="">
												<a href="#">RiverStone Ventur</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<img src="assets/img/icons/company-icon-06.svg" alt="">
												<a href="#">Bright Bridge Grp</a>
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
		<!-- /Add Company -->

        @endsection
