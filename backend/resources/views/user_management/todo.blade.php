@extends('layouts.main')
@section('title', 'To-Do')
@section('description', 'Manage your daily tasks and activities.')
@section('content')
<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-4">
									<h4 class="page-title">To-Do<span class="count-title">123</span></h4>
								</div>
								<div class="col-8 text-end">
									<div class="head-icons">
										<a href="tasks.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
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

						<div class="card ">
							<div class="card-header">
								<!-- Search -->
								<div class="row align-items-center">
									<div class="col-sm-4">
										<div class="icon-form mb-sm-0">
											<span class="form-icon"><i class="ti ti-search"></i></span>
											<input type="text" class="form-control" placeholder="Search Task">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end mb-3 mb-sm-0">
											<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-square-rounded-plus me-2"></i>Add New Tasks</a>
										</div>
									</div>
								</div>
								<!-- /Search -->
							</div>
							<div class="card-body">
								<!-- Filter -->
								<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
									<div class="sort-dropdown drop-down task-drops">
										<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown">All Tasks </a>
										<div class="dropdown-menu  dropdown-menu-start">
											<ul>
												<li>
													<a href="tasks.html">
														<i class="ti ti-dots-vertical"></i>All Tasks
													</a>
												</li>
												<li>
													<a href="tasks-important.html">
														<i class="ti ti-dots-vertical"></i>Important
													</a>
												</li>
												<li>
													<a href="tasks-completed.html">
														<i class="ti ti-dots-vertical"></i>Completed
													</a>
												</li>
											</ul>
										</div>
									</div>
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<div class="all-read me-2 ">
											<label class="checkboxs">
												<input type="checkbox" id="mark-all-read-checkbox">
												<span class="checkmarks"></span>
												Mark all as read
											</label>
										</div>
										<div class="dropdown me-2">
											<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-sort-ascending-2 me-2"></i>Sort </a>
											<div class="dropdown-menu  dropdown-menu-start">
												<ul>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-btn" data-sort="asc">
															<i class="ti ti-circle-chevron-right me-1"></i>Ascending
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-btn" data-sort="desc">
															<i class="ti ti-circle-chevron-right me-1"></i>Descending
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-btn" data-sort="recent_viewed">
															<i class="ti ti-circle-chevron-right me-1"></i>Recently
															Viewed
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-btn" data-sort="recent_added">
															<i class="ti ti-circle-chevron-right me-1"></i>Recently
															Added
														</a>
													</li>
												</ul>
											</div>
										</div>
										<div class="icon-form me-2">
											<span class="form-icon"><i class="ti ti-calendar"></i></span>
											<input type="text" class="form-control bookingrange" id="date-range-filter" placeholder="">
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
																<a href="#" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">Task Name</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse show" id="collapseTwo" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<div class="mb-2 icon-form">
																		<span class="form-icon"><i class="ti ti-search"></i></span>
																		<input type="text" class="form-control" placeholder="Search Task">
																	</div>
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
																					<span class="checkmarks"></span>
																					Add a form to Update Task
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Add a form to Update Task
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Update orginal content
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Use only component colours
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#owner" aria-expanded="false" aria-controls="owner">Task Type</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="owner" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<div class="mb-2 icon-form">
																		<span class="form-icon"><i class="ti ti-search"></i></span>
																		<input type="text" class="form-control" placeholder="Search Client">
																	</div>
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
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseone" aria-expanded="false" aria-controls="collapseone">Tags</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="collapseone" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
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
																					Rated
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Promotion
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">Created By</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="collapseThree" data-bs-parent="#accordionExample">
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
																					23 Oct 2023
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					29 Sep 2023
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					24 Oct 2023
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					25 Oct 2023
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
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
																<a href="tasks.html" class="btn btn-primary">Filter</a>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- /Filter -->

								<!-- Task List Groups -->
								<div id="todo-groups-container">
									<p class="p-3 text-center">Loading tasks...</p>
								</div>
								<!-- /Task List Groups -->

							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Add New Task -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h4>Add New Task</h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="{{ box_route('manage_users.todo.store') }}" method="POST" id="add-todo-form">
					@csrf
					<div>
						<div class="row">
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Title <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="title" required>
								</div>
								<div class="mb-3">
									<label class="col-form-label">Category</label>
									<select class="select" name="category">
										<option>Choose</option>
										<option>Calls</option>
										<option>Email</option>
										<option>Email</option>
										<option>Meeting</option>
									</select>
								</div>
								<div class="mb-3">
									<label class="col-form-label">Responsible Persons <span class="text-danger">*</span></label>
									<select class="multiple-img" multiple="multiple" name="responsible_persons[]">
										@foreach($boxUsers as $bUser)
											<option data-image="{{ $bUser->profile_image_path ? asset($bUser->profile_image_path) : asset('assets/img/profiles/avatar-19.jpg') }}" value="{{ $bUser->id }}">
												{{ trim($bUser->first_name . ' ' . $bUser->last_name) }}
											</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<label class="col-form-label">Start Date <span class="text-danger">*</span></label>
								<div class="mb-3 icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="form-control datePicker" name="start_date" required>
								</div>
							</div>
							<div class="col-md-6">
								<label class="col-form-label">Due Date <span class="text-danger">*</span></label>
								<div class="mb-3 icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="form-control datePicker" name="due_date" required>
								</div>
							</div>
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Tags <span class="text-danger">*</span></label>
									<input class="input-tags form-control" type="text" data-role="tagsinput" name="tags" value="Promotion, Collab">
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Priority</label>
									<div class="select-priority">
										<span class="select-icon"><i class="ti ti-square-rounded-filled"></i></span>
										<select class="select" name="priority">
											<option>Select</option>
											<option>High</option>
											<option>Low</option>
											<option>Medium</option>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Status</label>
									<select class="select" name="status">
										<option value="Pending" selected>Pending</option>
										<option value="In Progress">In Progress</option>
										<option value="Completed">Completed</option>
									</select>
								</div>
							</div>
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Description <span class="text-danger">*</span></label>
									<textarea class="form-control summernote" name="description" required></textarea>
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
		<!-- /Add New Task -->

		<!-- Edit Task -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom">
				<h4>Edit Task</h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="" method="POST" id="edit-todo-form">
					@csrf
					@method('PUT')
					<input type="hidden" name="id" id="edit_todo_id">
					<div>
						<div class="row">
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Title <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="title" id="edit_title" required>
								</div>
								<div class="mb-3">
									<label class="col-form-label">Category</label>
									<select class="select" name="category" id="edit_category">
										<option value="">Choose</option>
										<option value="Calls">Calls</option>
										<option value="Email">Email</option>
										<option value="Meeting">Meeting</option>
									</select>
								</div>
								<div class="mb-3">
									<label class="col-form-label">Responsible Persons <span class="text-danger">*</span></label>
									<select class="multiple-img" multiple="multiple" name="responsible_persons[]" id="edit_responsible_persons">
										@foreach($boxUsers as $bUser)
											<option data-image="{{ $bUser->profile_image_path ? asset($bUser->profile_image_path) : asset('assets/img/profiles/avatar-19.jpg') }}" value="{{ $bUser->id }}">
												{{ trim($bUser->first_name . ' ' . $bUser->last_name) }}
											</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<label class="col-form-label">Start Date <span class="text-danger">*</span></label>
								<div class="mb-3 icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="form-control datetimepicker" name="start_date" id="edit_start_date" required>
								</div>
							</div>
							<div class="col-md-6">
								<label class="col-form-label">Due Date <span class="text-danger">*</span></label>
								<div class="mb-3 icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="form-control datetimepicker" name="due_date" id="edit_due_date" required>
								</div>
							</div>
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Tags <span class="text-danger">*</span></label>
									<input class="input-tags form-control" type="text" data-role="tagsinput" name="tags" id="edit_tags">
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Priority</label>
									<div class="select-priority">
										<span class="select-icon"><i class="ti ti-square-rounded-filled"></i></span>
										<select class="select" name="priority" id="edit_priority">
											<option value="">Select</option>
											<option value="High">High</option>
											<option value="Medium">Medium</option>
											<option value="Low">Low</option>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Status</label>
									<select class="select" name="status" id="edit_status">
										<option value="Pending">Pending</option>
										<option value="In Progress">In Progress</option>
										<option value="Completed">Completed</option>
									</select>
								</div>
							</div>
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Description <span class="text-danger">*</span></label>
									<textarea class="form-control summernote" name="description" id="edit_description" required></textarea>
								</div>
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
		<!-- /Edit Task -->

		<!-- Delete Task -->
		<div class="modal fade" id="delete_activity" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Task?</h4>
							<p class="mb-0">Are you sure you want to remove <br> task you selected.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<button type="button" class="btn btn-danger" id="confirm_delete_btn">Yes, Delete it</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Task -->

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

		<!-- Task Stats Modal -->
		<div class="modal custom-modal fade" id="task_stats_modal" role="dialog">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Task Statistics by User</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>User</th>
										<th class="text-center">Pending</th>
										<th class="text-center">In Progress</th>
										<th class="text-center">Completed</th>
										<th class="text-center">Total</th>
									</tr>
								</thead>
								<tbody id="task-stats-tbody">
									<tr><td colspan="5" class="text-center">Loading stats...</td></tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Task Stats Modal -->

	</div>
@endsection

@push('css')
<style>
.tasks-activity.tasks>ul .task-wrap.in-progress::before {
    background-color: #FF9800; /* Orange for in-progress */
}
</style>
@endpush

@push('scripts')
<script>
var currentSort = '';
var currentDateRange = '';

function fetchTodos() {
    $.ajax({
        url: "{{ box_route('manage_users.todo') }}",
        type: 'GET',
        data: {
            sort: currentSort,
            date_range: currentDateRange
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var todos = response.data;
                var html = '';
                if (todos.length === 0) {
                    html = '<p class="p-3 text-center">No tasks found. Click "Add New Task" to create one.</p>';
                } else {
                    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                    var assetUrl = "{{ asset('') }}";
                    
                    var groups = {};
                    $.each(todos, function(index, todo) {
                        var groupKey = 'No Due Date';
                        var groupTitle = 'No Due Date';
                        if (todo.due_date) {
                            var d = new Date(todo.due_date);
                            var today = new Date();
                            var yesterday = new Date();
                            yesterday.setDate(yesterday.getDate() - 1);
                            
                            var dateString = d.getFullYear() + '-' + (d.getMonth()+1).toString().padStart(2, '0') + '-' + d.getDate().toString().padStart(2, '0');
                            var todayString = today.getFullYear() + '-' + (today.getMonth()+1).toString().padStart(2, '0') + '-' + today.getDate().toString().padStart(2, '0');
                            var yesterdayString = yesterday.getFullYear() + '-' + (yesterday.getMonth()+1).toString().padStart(2, '0') + '-' + yesterday.getDate().toString().padStart(2, '0');
                            
                            if (dateString === todayString) {
                                groupKey = 'Today';
                                groupTitle = 'Recent';
                            } else if (dateString === yesterdayString) {
                                groupKey = 'Yesterday';
                                groupTitle = 'Yesterday';
                            } else {
                                groupKey = dateString;
                                groupTitle = d.getDate() + ' ' + monthNames[d.getMonth()] + ' ' + d.getFullYear();
                            }
                        }
                        
                        if (!groups[groupKey]) {
                            groups[groupKey] = {
                                title: groupTitle,
                                items: []
                            };
                        }
                        groups[groupKey].items.push(todo);
                    });

                    var groupedArray = [];
                    for (var key in groups) {
                        groupedArray.push({
                            key: key,
                            title: groups[key].title,
                            items: groups[key].items
                        });
                    }

                    // Sort groups
                    groupedArray.sort(function(a, b) {
                        if (a.key === 'Today') return -1;
                        if (b.key === 'Today') return 1;
                        if (a.key === 'Yesterday') return -1;
                        if (b.key === 'Yesterday') return 1;
                        if (a.key === 'No Due Date') return 1;
                        if (b.key === 'No Due Date') return -1;
                        return b.key.localeCompare(a.key); // Descending (latest first)
                    });

                    $.each(groupedArray, function(gIndex, group) {
                        var groupId = 'task-group-' + gIndex;
                        html += '<div class="task-wrapper">';
                        html += '<a href="#" class="task-accordion" data-bs-toggle="collapse" data-bs-target="#' + groupId + '">';
                        html += '<h4>' + group.title + ' <span>' + group.items.length + '</span></h4>';
                        html += '</a>';
                        html += '<div class="tasks-activity tasks collapse show" id="' + groupId + '">';
                        html += '<ul>';

                        $.each(group.items, function(index, todo) {
                            var escapeHtml = function(text) {
                                return $('<div>').text(text).html();
                            };
                            
                            var safeTitle = todo.title ? escapeHtml(todo.title) : '';
                            var safeCategory = todo.category ? escapeHtml(todo.category) : '';
                            var safeStatus = todo.status ? escapeHtml(todo.status) : 'Pending';
                            
                            var isCompleted = (safeStatus.toLowerCase() === 'completed');
                            var isInProgress = (safeStatus.toLowerCase() === 'in progress');
                            
                            var statusClass = 'pending';
                            if (isCompleted) {
                                statusClass = 'success';
                            } else if (isInProgress) {
                                statusClass = 'in-progress';
                            }
                            
                            var checkAttr = isCompleted ? 'checked' : '';
                            var starClass = (todo.priority === 'High') ? 'text-warning' : '';
                            var titleHtml = isCompleted ? '<del>' + safeTitle + '</del>' : safeTitle;
                            
                            var categoryHtml = '';
                            if (safeCategory) {
                                categoryHtml = '<span class="badge badge-pill badge-status bg-green"><i class="ti ti-tag"></i>' + safeCategory + '</span>';
                            }
                            
                            var tagBadgeClass = 'bg-pending';
                            if (isCompleted) {
                                tagBadgeClass = 'bg-success';
                            } else if (isInProgress) {
                                tagBadgeClass = 'bg-info';
                            } else {
                                tagBadgeClass = 'bg-warning';
                            }
                            var statusBadge = '<span class="badge badge-tag ' + tagBadgeClass + '">' + safeStatus + '</span>';
                            
                            var tagsHtml = '';
                            if (todo.tags) {
                                var tagsArr = typeof todo.tags === 'string' ? todo.tags.split(',') : todo.tags;
                                $.each(tagsArr, function(i, tag) {
                                    tagsHtml += '<span class="badge badge-tag badge-purple-light">' + escapeHtml($.trim(tag)) + '</span> ';
                                });
                            }
                            
                            var dueDateStr = 'No Due Date';
                            if (todo.due_date) {
                                var d = new Date(todo.due_date);
                                dueDateStr = d.getDate() + ' ' + monthNames[d.getMonth()] + ' ' + d.getFullYear();
                            }

                            var creatorAvatar = 'assets/img/profiles/avatar-14.jpg';
                            var creatorName = 'User';
                            if (todo.creator) {
                                creatorAvatar = todo.creator.profile_image_path || creatorAvatar;
                                creatorName = todo.creator.first_name || creatorName;
                            }
                            
                            var fullAvatarUrl = creatorAvatar.startsWith('http') ? creatorAvatar : (assetUrl + creatorAvatar);
                            
                            html += '<li class="task-wrap ' + statusClass + '">';
                            html += '<div class="task-info">';
                            html += '<span class="task-icon"><i class="ti ti-grip-vertical"></i></span>';
                            html += '<div class="task-checkbox"><label class="checkboxs"><input type="checkbox" class="task-status-checkbox" data-id="' + todo.id + '" ' + checkAttr + '><span class="checkmarks"></span></label></div>';
                            html += '<div class="set-star rating-select"><i class="fa fa-star ' + starClass + '"></i></div>';
                            html += '<p>' + titleHtml + '</p>';
                            html += categoryHtml;
                            html += statusBadge;
                            html += '</div>';
                            
                            html += '<div class="task-actions"><ul>';
                            html += '<li class="task-time">' + tagsHtml + '</li>';
                            html += '<li class="task-date"><i class="ti ti-calendar-exclamation"></i>' + dueDateStr + '</li>';
                            html += '<li class="task-owner"><div class="task-user"><img src="' + escapeHtml(fullAvatarUrl) + '" alt="img" title="' + escapeHtml(creatorName) + '"></div>';
                            html += '<div class="dropdown table-action"><a href="#" class="action-icon " data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>';
                            var escapedTodo = escapeHtml(JSON.stringify(todo));
                            html += '<div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item edit-todo-btn" href="#" data-todo=\'' + JSON.stringify(todo).replace(/'/g, "&apos;") + '\' data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit"><i class="ti ti-edit text-blue"></i> Edit</a><a class="dropdown-item delete-todo-btn" href="#" data-id="' + todo.id + '" data-bs-toggle="modal" data-bs-target="#delete_activity"><i class="ti ti-trash text-danger"></i> Delete</a></div>';
                            html += '</div></li></ul></div>';
                            html += '</li>';
                        });

                        html += '</ul></div></div>';
                    });
                }
                $('#todo-groups-container').html(html);
            }
        },
        error: function() {
                $('#todo-groups-container').html('<p class="p-3 text-center text-danger">Failed to load tasks.</p>');
            }
    });
}

$(document).ready(function() {
    // Fetch initial tasks
    fetchTodos();

    $('#mark-all-read-checkbox').on('change', function(e) {
        if ($(this).is(':checked')) {
            // Open modal
            $('#task_stats_modal').modal('show');
            $('#task-stats-tbody').html('<tr><td colspan="5" class="text-center">Loading stats...</td></tr>');
            
            // Fetch stats
            $.ajax({
                url: "{{ box_route('manage_users.todo.stats') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var html = '';
                        if (response.data.length === 0) {
                            html = '<tr><td colspan="5" class="text-center">No users found.</td></tr>';
                        } else {
                            $.each(response.data, function(i, stat) {
                                html += '<tr>';
                                html += '<td><div class="d-flex align-items-center"><img src="' + stat.avatar + '" class="rounded-circle me-2" width="30" height="30" alt=""> <span>' + stat.user_name + '</span></div></td>';
                                html += '<td class="text-center"><span class="badge bg-warning">' + stat.pending + '</span></td>';
                                html += '<td class="text-center"><span class="badge bg-info">' + stat.in_progress + '</span></td>';
                                html += '<td class="text-center"><span class="badge bg-success">' + stat.completed + '</span></td>';
                                html += '<td class="text-center fw-bold">' + stat.total + '</td>';
                                html += '</tr>';
                            });
                        }
                        $('#task-stats-tbody').html(html);
                    }
                },
                error: function() {
                    $('#task-stats-tbody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load statistics.</td></tr>');
                }
            });
        }
    });
    
    // Sort logic
    $('.sort-btn').on('click', function(e) {
        e.preventDefault();
        currentSort = $(this).data('sort');
        fetchTodos();
    });

    // Date range logic
    $('#date-range-filter').on('apply.daterangepicker', function(ev, picker) {
        currentDateRange = picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY');
        fetchTodos();
    });

    $('#date-range-filter').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        currentDateRange = '';
        fetchTodos();
    });

    $('#add-todo-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var url = form.attr('action');
        var formData = form.serialize();
        var submitBtn = form.find('button[type="submit"]');
        var originalBtnText = submitBtn.text();
        
        submitBtn.prop('disabled', true).text('Creating...');
        
        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    } else {
                        alert(response.message);
                    }
                    
                    form[0].reset();
                    form.find('.select').val('').trigger('change');
                    form.find('.multiple-img').val(null).trigger('change');
                    if (form.find('.input-tags').length > 0) {
                        form.find('.input-tags').tagsinput('removeAll');
                    }
                    if (form.find('.summernote').length > 0) {
                        form.find('.summernote').summernote('code', '');
                    }
                    // Close the offcanvas
                    var offcanvasElement = document.getElementById('offcanvas_add');
                    var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    if(offcanvas) {
                        offcanvas.hide();
                    }
                    
                    // Fetch the updated list of tasks instead of reloading the page
                    fetchTodos();
                    submitBtn.prop('disabled', false).text(originalBtnText);
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).text(originalBtnText);
                
                var errors = xhr.responseJSON.errors;
                if(errors) {
                    var errorMsg = '';
                    $.each(errors, function(key, value) {
                        errorMsg += value[0] + '\n';
                    });
                    alert(errorMsg);
                } else if(xhr.responseJSON.message) {
                    alert(xhr.responseJSON.message);
                } else {
                    alert('An error occurred. Please try again.');
                }
            }
        });
    });
    // Edit Button Click
    $(document).on('click', '.edit-todo-btn', function() {
        var todo = $(this).data('todo');
        $('#edit-todo-form').attr('action', "{{ box_route('manage_users.todo') }}/" + todo.id);
        $('#edit_todo_id').val(todo.id);
        $('#edit_title').val(todo.title);
        $('#edit_category').val(todo.category).trigger('change');
        $('#edit_priority').val(todo.priority).trigger('change');
        $('#edit_status').val(todo.status).trigger('change');
        
        if (todo.start_date) {
            var sParts = todo.start_date.split(' ')[0].split('-');
            $('#edit_start_date').val(sParts[2] + '-' + sParts[1] + '-' + sParts[0]);
        }
        if (todo.due_date) {
            var dParts = todo.due_date.split(' ')[0].split('-');
            $('#edit_due_date').val(dParts[2] + '-' + dParts[1] + '-' + dParts[0]);
        }
        
        // Handle tags
        $('#edit_tags').tagsinput('removeAll');
        if (todo.tags) {
            var tagsArr = typeof todo.tags === 'string' ? todo.tags.split(',') : todo.tags;
            $.each(tagsArr, function(i, tag) {
                $('#edit_tags').tagsinput('add', tag);
            });
        }
        
        $('#edit_description').summernote('code', todo.description);
        
        // Handle responsible persons (assuming JSON array or comma separated string)
        if (todo.responsible_persons) {
            var rp = typeof todo.responsible_persons === 'string' ? JSON.parse(todo.responsible_persons) : todo.responsible_persons;
            $('#edit_responsible_persons').val(rp).trigger('change');
        } else {
            $('#edit_responsible_persons').val(null).trigger('change');
        }
    });

    // Edit Form Submit
    $('#edit-todo-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var formData = form.serialize();
        var submitBtn = form.find('button[type="submit"]');
        var originalBtnText = submitBtn.text();
        
        submitBtn.prop('disabled', true).text('Saving...');
        
        $.ajax({
            type: 'POST', // Using POST with _method=PUT from the form
            url: url,
            data: formData,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    } else {
                        alert(response.message);
                    }
                    var offcanvasElement = document.getElementById('offcanvas_edit');
                    var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    if(offcanvas) {
                        offcanvas.hide();
                    }
                    fetchTodos();
                    submitBtn.prop('disabled', false).text(originalBtnText);
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).text(originalBtnText);
                var errors = xhr.responseJSON.errors;
                if(errors) {
                    var errorMsg = '';
                    $.each(errors, function(key, value) {
                        errorMsg += value[0] + '\n';
                    });
                    alert(errorMsg);
                } else if(xhr.responseJSON.message) {
                    alert(xhr.responseJSON.message);
                } else {
                    alert('An error occurred. Please try again.');
                }
            }
        });
    });

    // Delete Button Click
    var deleteId = null;
    $(document).on('click', '.delete-todo-btn', function() {
        deleteId = $(this).data('id');
    });

    // Confirm Delete
    $('#confirm_delete_btn').on('click', function() {
        if (!deleteId) return;
        var btn = $(this);
        var originalText = btn.text();
        btn.prop('disabled', true).text('Deleting...');
        
        $.ajax({
            type: 'DELETE',
            url: "{{ box_route('manage_users.todo') }}/" + deleteId,
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if(response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    } else {
                        alert(response.message);
                    }
                    $('#delete_activity').modal('hide');
                    fetchTodos();
                }
                btn.prop('disabled', false).text(originalText);
            },
            error: function(xhr) {
                btn.prop('disabled', false).text(originalText);
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    alert(xhr.responseJSON.message);
                } else {
                    alert('An error occurred. Please try again.');
                }
            }
        });
    });
});
</script>
@endpush
