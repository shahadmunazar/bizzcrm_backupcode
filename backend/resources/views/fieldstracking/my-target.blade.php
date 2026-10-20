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
						<div class="col-8 d-flex align-items-center flex-wrap gap-2">
							<h4 class="page-title mb-0">My Target</h4>
							<span class="badge bg-soft-info text-info px-3 py-2 rounded-pill fs-13 d-inline-flex align-items-center" id="calculation_period_badge">
								<i class="ti ti-calendar-check me-1 fs-15"></i> Calculation Period: <strong class="ms-1 text-dark" id="calculation_period_text">All Months (Jan 2026 - Oct 2026)</strong>
							</span>
						</div>
						<div class="col-4 text-end">
							<div class="head-icons">
								<a href="javascript:void(0);" id="btn_refresh_my_targets" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
								<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
							</div>
						</div>
					</div>
				</div>
				<!-- /Page Header -->

				<!-- Top Performance Summary Row -->
				<div class="row mb-4" id="target_top_performance_section">
					<!-- Top Performer Card (Matching media_1791779663493.png and media_1791780271778.png) -->
					<div class="col-xl-4 col-lg-5 col-md-12 mb-3 mb-xl-0">
						<div class="card h-100 shadow-sm border-0">
							<div class="card-body p-3">
								<div class="d-flex align-items-center justify-content-between mb-2">
									<h6 class="mb-0 fw-bold fs-14 d-flex align-items-center text-dark">
										<i class="ti ti-crown text-warning me-1 fs-18"></i> Top Performer
									</h6>
									<ul class="nav nav-pills nav-sm" id="topPerformerPeriodTabs" role="tablist">
										<li class="nav-item" role="presentation">
											<button class="nav-link active px-2 py-1 fs-11" id="btn_top_this_month" type="button">This Month</button>
										</li>
										<li class="nav-item" role="presentation">
											<button class="nav-link px-2 py-1 fs-11" id="btn_top_last_month" type="button">Last Month</button>
										</li>
									</ul>
								</div>

								<!-- Top Performer Inner Card with Border matching Screenshot -->
								<div class="p-3 d-flex align-items-center justify-content-between rounded-3" id="top_performer_card_box" style="border: 1.5px solid #ff4d4f !important; background-color: #fff9f9;">
									<div class="d-flex align-items-center overflow-hidden">
										<span class="me-2 text-danger flex-shrink-0">
											<i class="ti ti-award-filled fs-28"></i>
										</span>
										<div class="avatar avatar-md me-2 flex-shrink-0" id="top_performer_avatar_wrap">
											<span class="avatar-title rounded-circle bg-success text-white fw-bold fs-13" id="top_performer_avatar_initials" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; background-color: #00d284 !important;">
												<i class="ti ti-user fs-18"></i>
											</span>
										</div>
										<div class="overflow-hidden">
											<h6 class="text-truncate mb-0 fs-14 fw-bold text-dark" id="top_performer_name">Loading Top Performer...</h6>
											<p class="fs-12 mb-0 text-muted text-truncate" id="top_performer_role">Calculating score</p>
											<span class="badge bg-soft-primary text-primary fs-10 mt-1" id="top_performer_month_badge">Selected Period</span>
										</div>
									</div>
									<div class="text-end ps-2 flex-shrink-0">
										<p class="fs-12 mb-0 text-muted fw-medium">Performance</p>
										<h4 class="text-danger mb-0 fw-bold fs-20" id="top_performer_score">--%</h4>
									</div>
								</div>

								<div class="mt-2 text-center">
									<a href="javascript:void(0);" class="btn btn-light btn-sm w-100 fs-12 fw-medium text-dark border disabled" id="btn_filter_by_top_performer">
										<i class="ti ti-user-check me-1 text-primary"></i> View Top Performer's Targets
									</a>
								</div>
							</div>
						</div>
					</div>

					<!-- Overall Performance & Target Stats Cards -->
					<div class="col-xl-8 col-lg-7 col-md-12">
						<div class="row h-100">
							<!-- Team Average Achievement -->
							<div class="col-sm-6 col-md-4 mb-3 mb-md-0">
								<div class="card h-100 shadow-sm border-0">
									<div class="card-body p-3 d-flex flex-column justify-content-between">
										<div class="d-flex align-items-center justify-content-between mb-2">
											<span class="fs-12 text-muted fw-medium">Avg Achievement</span>
											<span class="badge bg-soft-success text-success p-1 rounded-circle"><i class="ti ti-chart-arrows fs-16"></i></span>
										</div>
										<div>
											<h3 class="mb-1 fw-bold text-dark" id="kpi_team_avg_score">--%</h3>
											<div class="progress progress-xs mb-1" style="height: 5px;">
												<div class="progress-bar bg-success" id="kpi_team_avg_bar" style="width: 0%"></div>
											</div>
											<small class="text-muted fs-11" id="kpi_team_avg_subtitle">Across active targets</small>
										</div>
									</div>
								</div>
							</div>

							<!-- Target Completion Ratio -->
							<div class="col-sm-6 col-md-4 mb-3 mb-md-0">
								<div class="card h-100 shadow-sm border-0">
									<div class="card-body p-3 d-flex flex-column justify-content-between">
										<div class="d-flex align-items-center justify-content-between mb-2">
											<span class="fs-12 text-muted fw-medium">Achieved / Targets</span>
											<span class="badge bg-soft-info text-info p-1 rounded-circle"><i class="ti ti-target fs-16"></i></span>
										</div>
										<div>
											<h3 class="mb-1 fw-bold text-dark" id="kpi_target_ratio">-- / --</h3>
											<span class="badge bg-soft-primary text-primary fs-11" id="kpi_target_completion_pct">--% Achieved</span>
											<small class="text-muted fs-11 d-block mt-1" id="kpi_target_period_label">Calculating...</small>
										</div>
									</div>
								</div>
							</div>

							<!-- Tasks & Activities Widget (Matching media_1791780482961.png) -->
							<div class="col-sm-12 col-md-4">
								<div class="card h-100 shadow-sm border-0 bg-dark text-white">
									<div class="card-body p-3 d-flex flex-column justify-content-between">
										<div class="d-flex align-items-center justify-content-between mb-2">
											<span class="fs-12 text-light fw-medium">Spent on Overall Tasks</span>
											<span class="badge bg-secondary text-white p-1 rounded-circle"><i class="ti ti-activity fs-16"></i></span>
										</div>
										<div>
											<h3 class="text-success mb-1 fw-bold fs-18" id="kpi_tasks_closed_stat">-- Tasks Closed</h3>
											<p class="fs-12 mb-0 text-white-50" id="kpi_tasks_subtext">Completed Activities</p>
											<small class="text-white-50 fs-11 d-block mt-1" id="kpi_leads_matured_stat">-- Leads Matured</small>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /Top Performance Summary Row -->

				<div class="card">
					<div class="card-header">
						<!-- Search & Action Toolbar -->
						<div class="row align-items-center">
							<div class="col-sm-4">
								<div class="icon-form mb-3 mb-sm-0">
									<span class="form-icon"><i class="ti ti-search"></i></span>
									<input type="text" class="form-control global-search" placeholder="Search For Target or Employee">
								</div>
							</div>
							<div class="col-sm-8">
								<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
									<!-- Month Selector -->
									<div class="me-2" style="min-width: 200px;">
										<select class="select" id="select_target_month">
											@if(isset($availableMonths))
												@foreach($availableMonths as $m)
													<option value="{{ $m['key'] }}" {{ (isset($selectedMonth) && $selectedMonth === $m['key']) ? 'selected' : '' }}>
														{{ $m['label'] }} {{ $m['is_current'] ? '(Current)' : '' }}
													</option>
												@endforeach
											@endif
										</select>
									</div>

									@if(isset($isAdmin) && $isAdmin && isset($employees) && $employees->count() > 0)
										<div class="me-2" style="min-width: 240px;">
											<select class="select" id="select_my_target_employee">
												<option value="all" selected>All Assigned Targets (All Employees)</option>
												<option value="{{ $boxUser->id }}">My Targets ({{ $boxUser->user->name ?? 'Admin' }})</option>
												<optgroup label="Filter by Employee">
													@foreach($employees as $emp)
														@if($emp->id != $boxUser->id)
															@php
																$empName = trim(($emp->first_name ?? '') . ' ' . ($emp->last_name ?? ''));
																if (!$empName && $emp->user) $empName = $emp->user->name;
																if (!$empName) $empName = 'Employee #' . $emp->id;
															@endphp
															<option value="{{ $emp->id }}">{{ $empName }}</option>
														@endif
													@endforeach
												</optgroup>
											</select>
										</div>
									@endif
									<a href="{{ route('fieldstracking.target-template', ['box_slug' => $boxSlug]) }}" class="btn btn-outline-primary me-2"><i class="ti ti-settings me-1"></i> Manage Templates</a>
								</div>
							</div>
						</div>
						<!-- /Search & Action Toolbar -->
					</div>
					<div class="card-body">
						<!-- Filter Toolbar -->
						<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
							<div class="d-flex align-items-center flex-wrap row-gap-2">
								<div class="dropdown me-2">
									<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" id="sort_dropdown_label"><i class="ti ti-sort-ascending-2 me-2"></i>Sort </a>
									<div class="dropdown-menu dropdown-menu-start">
										<ul>
											<li>
												<a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="recent_add">
													<i class="ti ti-circle-chevron-right me-1"></i>Recently Added
												</a>
											</li>
											<li>
												<a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="name_asc">
													<i class="ti ti-circle-chevron-right me-1"></i>Name Ascending
												</a>
											</li>
											<li>
												<a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="name_desc">
													<i class="ti ti-circle-chevron-right me-1"></i>Name Descending
												</a>
											</li>
										</ul>
									</div>
								</div>
								<div class="icon-form">
									<span class="form-icon"><i class="ti ti-calendar"></i></span>
									<input type="text" class="form-control bookingrange" placeholder="Filter by date range">
								</div>
							</div>
							<div class="d-flex align-items-center flex-wrap row-gap-2">
								<!-- Manage Columns Dropdown -->
								<div class="dropdown me-2">
									<a href="javascript:void(0);" class="btn bg-soft-purple text-purple" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-columns-3 me-2"></i>Manage Columns</a>
									<div class="dropdown-menu dropdown-menu-md-end dropdown-md p-3">
										<h4 class="mb-2 fw-semibold">Manage Columns</h4>
										<p class="mb-3">Toggle columns to show or hide them in the table.</p>
										<div class="border-top pt-3">
											@if(isset($isAdmin) && $isAdmin)
												<div class="d-flex align-items-center justify-content-between mb-3">
													<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Assigned Employee</p>
													<div class="status-toggle">
														<input type="checkbox" id="col-emp" class="check col-toggle" data-column="1" checked>
														<label for="col-emp" class="checktoggle"></label>
													</div>
												</div>
											@endif
											<div class="d-flex align-items-center justify-content-between mb-3">
												<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Target Name</p>
												<div class="status-toggle">
													<input type="checkbox" id="col-target-name" class="check col-toggle" data-column="{{ (isset($isAdmin) && $isAdmin) ? '2' : '1' }}" checked>
													<label for="col-target-name" class="checktoggle"></label>
												</div>
											</div>
											<div class="d-flex align-items-center justify-content-between mb-3">
												<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Month & Year</p>
												<div class="status-toggle">
													<input type="checkbox" id="col-month" class="check col-toggle" data-column="{{ (isset($isAdmin) && $isAdmin) ? '3' : '2' }}" checked>
													<label for="col-month" class="checktoggle"></label>
												</div>
											</div>
											<div class="d-flex align-items-center justify-content-between mb-3">
												<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Frequency</p>
												<div class="status-toggle">
													<input type="checkbox" id="col-frequency" class="check col-toggle" data-column="{{ (isset($isAdmin) && $isAdmin) ? '4' : '3' }}" checked>
													<label for="col-frequency" class="checktoggle"></label>
												</div>
											</div>
											<div class="d-flex align-items-center justify-content-between mb-3">
												<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Target vs Achieved</p>
												<div class="status-toggle">
													<input type="checkbox" id="col-metrics" class="check col-toggle" data-column="{{ (isset($isAdmin) && $isAdmin) ? '5' : '4' }}" checked>
													<label for="col-metrics" class="checktoggle"></label>
												</div>
											</div>
											<div class="d-flex align-items-center justify-content-between mb-3">
												<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Score & Performance</p>
												<div class="status-toggle">
													<input type="checkbox" id="col-score" class="check col-toggle" data-column="{{ (isset($isAdmin) && $isAdmin) ? '6' : '5' }}" checked>
													<label for="col-score" class="checktoggle"></label>
												</div>
											</div>
											<div class="d-flex align-items-center justify-content-between mb-3">
												<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Status</p>
												<div class="status-toggle">
													<input type="checkbox" id="col-status" class="check col-toggle" data-column="{{ (isset($isAdmin) && $isAdmin) ? '7' : '6' }}" checked>
													<label for="col-status" class="checktoggle"></label>
												</div>
											</div>
											<div class="d-flex align-items-center justify-content-between">
												<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Action</p>
												<div class="status-toggle">
													<input type="checkbox" id="col-action" class="check col-toggle" data-column="{{ (isset($isAdmin) && $isAdmin) ? '8' : '7' }}" checked>
													<label for="col-action" class="checktoggle"></label>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- /Manage Columns Dropdown -->

								<!-- Filter Dropdown -->
								<div class="form-sorts dropdown">
									<a href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-filter-share"></i>Filter</a>
									<div class="filter-dropdown-menu dropdown-menu dropdown-menu-md-end p-3" style="min-width: 280px;">
										<div class="filter-set-view">
											<div class="filter-set-head pb-2 border-bottom mb-3">
												<h4 class="fs-16 fw-semibold"><i class="ti ti-filter-share me-1"></i>Filter Targets</h4>
											</div>
											<div class="accordion" id="myFilterAccordion">
												<!-- Frequency Filter -->
												<div class="filter-set-content mb-3">
													<div class="filter-set-content-head mb-2">
														<a href="#" data-bs-toggle="collapse" data-bs-target="#myFilterFreq" aria-expanded="true" aria-controls="myFilterFreq" class="fw-medium text-dark">
															Frequency
														</a>
													</div>
													<div class="filter-set-contents accordion-collapse collapse show" id="myFilterFreq" data-bs-parent="#myFilterAccordion">
														<div class="filter-content-list">
															<ul class="list-unstyled mb-0">
																@foreach(['Daily', 'Weekly', 'Monthly', 'Quarterly', 'Yearly'] as $freq)
																	<li class="mb-2">
																		<div class="filter-checks">
																			<label class="checkboxs">
																				<input type="checkbox" class="filter-freq-check" value="{{ $freq }}">
																				<span class="checkmarks"></span>
																				{{ $freq }}
																			</label>
																		</div>
																	</li>
																@endforeach
															</ul>
														</div>
													</div>
												</div>

												<!-- Status Filter -->
												<div class="filter-set-content mb-3">
													<div class="filter-set-content-head mb-2">
														<a href="#" class="collapsed fw-medium text-dark" data-bs-toggle="collapse" data-bs-target="#myFilterStatus" aria-expanded="false" aria-controls="myFilterStatus">
															Status
														</a>
													</div>
													<div class="filter-set-contents accordion-collapse collapse" id="myFilterStatus" data-bs-parent="#myFilterAccordion">
														<div class="filter-content-list">
															<ul class="list-unstyled mb-0">
																@foreach(['Active', 'Inactive'] as $statusOption)
																	<li class="mb-2">
																		<div class="filter-checks">
																			<label class="checkboxs">
																				<input type="checkbox" class="filter-status-check" value="{{ $statusOption }}">
																				<span class="checkmarks"></span>
																				{{ $statusOption }}
																			</label>
																		</div>
																	</li>
																@endforeach
															</ul>
														</div>
													</div>
												</div>

												<!-- Filter Action Buttons -->
												<div class="filter-reset-btns pt-3 border-top">
													<div class="row">
														<div class="col-6">
															<a href="javascript:void(0);" id="btn_reset_my_filter" class="btn btn-light w-100">Reset</a>
														</div>
														<div class="col-6">
															<a href="javascript:void(0);" id="btn_apply_my_filter" class="btn btn-primary w-100">Filter</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- /Filter Dropdown -->
							</div>
						</div>
						<!-- /Filter Toolbar -->

						<!-- My Target Table -->
						<div class="table-responsive custom-table">
							<table class="table" id="my_target_table">
								<thead class="thead-light">
									<tr>
										<th class="no-sort">
											<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
										</th>
										@if(isset($isAdmin) && $isAdmin)
											<th>Assigned Employee</th>
										@endif
										<th>Target Name</th>
										<th>Month & Year</th>
										<th>Frequency</th>
										<th>Target vs Achieved</th>
										<th>Score & Performance</th>
										<th>Status</th>
										<th class="text-end">Action</th>
									</tr>
								</thead>
								<tbody id="my_targets_tbody">
									<!-- Loaded via Yajra DataTables AJAX -->
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
						<!-- /My Target Table -->

					</div>
				</div>

			</div>
		</div>

	</div>
</div>
<!-- /Page Wrapper -->

<!-- Target Details & Monthly History Offcanvas -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_target_details" style="width: 780px;">
	<div class="offcanvas-header border-bottom">
		<div>
			<h5 class="fw-semibold mb-0" id="details_target_title">Target Details & Monthly History</h5>
			<small class="text-muted" id="details_target_subtitle"></small>
		</div>
		<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
			<i class="ti ti-x"></i>
		</button>
	</div>
	<div class="offcanvas-body" id="details_target_content">
		<!-- Dynamic Content Loaded via AJAX -->
	</div>
</div>

@push('scripts')
<script>
    (function initMyTarget() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined' || !$.fn.DataTable) {
            setTimeout(initMyTarget, 50);
            return;
        }

        jQuery(function($) {
            const boxSlug = '{{ $boxSlug ?? request()->route("box_slug") }}';
            const isAdmin = {{ (isset($isAdmin) && $isAdmin) ? 'true' : 'false' }};
            let startDate = '';
            let endDate = '';
            let currentSort = 'recent_add';

            let columns = [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false }
            ];

            if (isAdmin) {
                columns.push({ data: 'assigned_member', name: 'assigned_member', orderable: false });
            }

            columns.push(
                { data: 'target_name', name: 'target_name' },
                { data: 'month_year', name: 'month_year' },
                { data: 'frequency', name: 'frequency' },
                { data: 'target_vs_achieved', name: 'target_vs_achieved', orderable: false },
                { data: 'score_performance', name: 'score_performance' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
            );

            // Initialize Yajra DataTables with AJAX
            let myDataTable = $('#my_target_table').DataTable({
                processing: true,
                serverSide: true,
                bFilter: false,
                autoWidth: false,
                ordering: true,
                order: [],
                ajax: {
                    url: "{{ route('fieldstracking.target-api.my-assignments', ['box_slug' => $boxSlug ?? request()->route('box_slug')]) }}",
                    type: 'GET',
                    data: function(d) {
                        d.month = $('#select_target_month').val();
                        let empId = $('#select_my_target_employee').val();
                        if (empId && empId !== 'all') d.employee_id = empId;
                        d.search_query = $('.global-search').val();
                        d.start_date = startDate;
                        d.end_date = endDate;
                        d.custom_sort = currentSort;
                        d.frequency = $('.filter-freq-check:checked').map(function() { return $(this).val(); }).get();
                        d.status = $('.filter-status-check:checked').map(function() { return $(this).val(); }).get();
                    },
                    error: function(xhr, err, thrown) {
                        console.error('My Target DataTables Ajax Error:', xhr.responseText || thrown);
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Error loading targets data.');
                        }
                    }
                },
                columns: columns,
                language: {
                    search: ' ',
                    sLengthMenu: '_MENU_',
                    info: "_START_ - _END_ of _TOTAL_ targets",
                    lengthMenu: "Show _MENU_ entries",
                    emptyTable: '<i class="ti ti-target-off fs-24 d-block mb-1"></i> No targets assigned for this period',
                    paginate: {
                        next: 'Next <i class="fa fa-angle-right"></i>',
                        previous: '<i class="fa fa-angle-left"></i> Prev'
                    },
                },
                initComplete: function(settings, json) {
                    $('.dataTables_paginate').appendTo('.datatable-paginate');
                    $('.dataTables_length').appendTo('.datatable-length');
                },
                drawCallback: function(settings) {
                    $('.dataTables_paginate').appendTo('.datatable-paginate');
                    $('.dataTables_length').appendTo('.datatable-length');
                    if (settings.json) {
                        updateTopPerformerUI(settings.json.top_performer, settings.json.summary);
                    }
                }
            });

            myDataTable.on('xhr.dt', function(e, settings, json) {
                if (json) {
                    updateTopPerformerUI(json.top_performer, json.summary);
                }
            });

            const currentMonthKey = '{{ now()->format("Y-m") }}';
            const lastMonthKey = '{{ now()->subMonth()->format("Y-m") }}';

            function updateTopPerformerUI(tp, sm) {
                if (tp) {
                    $('#top_performer_name').text(tp.name || 'Team Member');
                    $('#top_performer_role').text(tp.role || 'Sales / Field Staff');
                    $('#top_performer_score').text(tp.score_formatted || ((tp.score || 0) + '%'));
                    $('#top_performer_month_badge').text(tp.month_label || '');
                    if (tp.id) {
                        $('#btn_filter_by_top_performer').attr('data-emp-id', tp.id).removeClass('disabled');
                    } else {
                        $('#btn_filter_by_top_performer').removeAttr('data-emp-id').addClass('disabled');
                    }

                    if (tp.avatar) {
                        $('#top_performer_avatar_wrap').html('<img src="' + tp.avatar + '" class="rounded-circle border border-white" style="width: 40px; height: 40px; object-fit: cover;" alt="Avatar">');
                    } else {
                        let initials = tp.initials || 'TP';
                        $('#top_performer_avatar_wrap').html('<span class="avatar-title rounded-circle bg-success text-white fw-bold fs-13" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; background-color: #00d284 !important;">' + initials + '</span>');
                    }
                } else {
                    $('#top_performer_name').text('No Target Assigned');
                    $('#top_performer_role').text('No Data');
                    $('#top_performer_score').text('0%');
                    $('#btn_filter_by_top_performer').removeAttr('data-emp-id').addClass('disabled');
                    $('#top_performer_avatar_wrap').html('<span class="avatar-title rounded-circle bg-secondary text-white fw-bold fs-13" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">--</span>');
                }

                if (sm) {
                    let avgScore = sm.team_avg_score || 0;
                    $('#kpi_team_avg_score').text(avgScore + '%');
                    $('#kpi_team_avg_bar').css('width', Math.min(100, Math.max(0, avgScore)) + '%');
                    
                    let achieved = sm.targets_achieved || 0;
                    let total = sm.total_assignments || 0;
                    $('#kpi_target_ratio').text(achieved + ' / ' + total);
                    $('#kpi_target_completion_pct').text((sm.completion_rate || 0) + '% Achieved');
                    $('#kpi_target_period_label').text('For ' + (sm.month_label || 'Selected Period'));

                    let closed = (sm.tasks_closed || 0).toLocaleString();
                    let matured = (sm.leads_matured || 0).toLocaleString();
                    $('#kpi_tasks_closed_stat').text(closed + ' Tasks Closed');
                    $('#kpi_leads_matured_stat').text(matured + ' Leads Matured');

                    if (sm.month_label) {
                        $('#calculation_period_text').text(sm.month_label);
                    }
                }
            }

            // Month pill toggles (This Month / Last Month)
            $('#btn_top_this_month').on('click', function(e) {
                e.preventDefault();
                $('#btn_top_this_month').addClass('active');
                $('#btn_top_last_month').removeClass('active');
                if ($('#select_target_month').val() !== currentMonthKey) {
                    $('#select_target_month').val(currentMonthKey).trigger('change');
                }
            });

            $('#btn_top_last_month').on('click', function(e) {
                e.preventDefault();
                $('#btn_top_last_month').addClass('active');
                $('#btn_top_this_month').removeClass('active');
                if ($('#select_target_month').val() !== lastMonthKey) {
                    $('#select_target_month').val(lastMonthKey).trigger('change');
                }
            });

            // Filter by Top Performer button
            $('#btn_filter_by_top_performer').on('click', function(e) {
                e.preventDefault();
                let empId = $(this).attr('data-emp-id');
                if (!empId) return;
                if ($('#select_my_target_employee').length) {
                    $('#select_my_target_employee').val(empId).trigger('change');
                    if (typeof toastr !== 'undefined') {
                        toastr.info('Filtering targets for top performer');
                    }
                }
            });

            // Reload helper (Zero Page Reload)
            function reloadMyTable() {
                myDataTable.ajax.reload(null, false);
            }

            // Month Selector Change Handler
            $('#select_target_month').on('change', function() {
                let selVal = $(this).val();
                let selText = $(this).find('option:selected').text().trim();
                if (selVal === 'all') {
                    $('#calculation_period_text').text('All Months (Jan 2026 - Oct 2026)');
                } else {
                    $('#calculation_period_text').text(selText);
                }

                if (selVal === currentMonthKey) {
                    $('#btn_top_this_month').addClass('active');
                    $('#btn_top_last_month').removeClass('active');
                } else if (selVal === lastMonthKey) {
                    $('#btn_top_last_month').addClass('active');
                    $('#btn_top_this_month').removeClass('active');
                } else {
                    $('#btn_top_this_month').removeClass('active');
                    $('#btn_top_last_month').removeClass('active');
                }
                reloadMyTable();
            });

            // Employee Selector Change Handler
            $('#select_my_target_employee').on('change', function() {
                reloadMyTable();
            });

            // Header Refresh Button
            $('#btn_refresh_my_targets').on('click', function(e) {
                e.preventDefault();
                let icon = $(this).find('i');
                icon.addClass('fa-spin');
                myDataTable.ajax.reload(function() {
                    icon.removeClass('fa-spin');
                    if (typeof toastr !== 'undefined') {
                        toastr.info('Targets refreshed');
                    }
                }, false);
            });

            // Sort handler
            $('.sort-item').on('click', function(e) {
                e.preventDefault();
                currentSort = $(this).data('sort');
                $('#sort_dropdown_label').html('<i class="ti ti-sort-ascending-2 me-2"></i>Sort: ' + $(this).text().trim());
                reloadMyTable();
            });

            // Search (Debounced 300ms)
            let searchTimeout = null;
            $('.global-search').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    reloadMyTable();
                }, 300);
            });

            // Date Range Picker
            $('.bookingrange').off('apply.daterangepicker cancel.daterangepicker');
            $('.bookingrange').on('apply.daterangepicker', function(ev, picker) {
                startDate = picker.startDate.format('YYYY-MM-DD');
                endDate = picker.endDate.format('YYYY-MM-DD');
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                reloadMyTable();
            });
            $('.bookingrange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                startDate = '';
                endDate = '';
                reloadMyTable();
            });

            // Filter Apply & Reset
            $('#btn_apply_my_filter').on('click', function(e) {
                e.preventDefault();
                reloadMyTable();
                let filterDropdown = bootstrap.Dropdown.getInstance($('.form-sorts > a')[0]);
                if (filterDropdown) filterDropdown.hide();
            });

            $('#btn_reset_my_filter').on('click', function(e) {
                e.preventDefault();
                $('.filter-freq-check, .filter-status-check').prop('checked', false);
                reloadMyTable();
                let filterDropdown = bootstrap.Dropdown.getInstance($('.form-sorts > a')[0]);
                if (filterDropdown) filterDropdown.hide();
            });

            // Manage Columns
            $(document).on('change', '.col-toggle', function() {
                let colIdx = parseInt($(this).data('column'));
                let column = myDataTable.column(colIdx);
                column.visible($(this).is(':checked'));
            });

            // Select All Checkbox
            $(document).on('change', '#select-all', function() {
                $('.target-checkbox').prop('checked', $(this).is(':checked'));
            });
            $(document).on('change', '.target-checkbox', function() {
                let total = $('.target-checkbox').length;
                let checked = $('.target-checkbox:checked').length;
                $('#select-all').prop('checked', total > 0 && total === checked);
            });

            // View Details & Monthly History Offcanvas Handler
            $(document).on('click', '.btn-view-target-details', function(e) {
                e.preventDefault();
                let assignId = $(this).data('id');

                var detailsOffcanvasEl = document.getElementById('offcanvas_target_details');
                var detailsOffcanvas = bootstrap.Offcanvas.getInstance(detailsOffcanvasEl) || new bootstrap.Offcanvas(detailsOffcanvasEl);
                detailsOffcanvas.show();

                $('#details_target_content').html(`
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="text-muted mt-2">Calculating monthly performance breakdown...</p>
                    </div>
                `);

                function escapeHtml(text) {
                    if (!text) return '';
                    return $('<div>').text(text).html();
                }

                $.get('/' + boxSlug + '/fieldstracking/target-api/assignment-history/' + assignId, function(res) {
                    if (!res.success) {
                        $('#details_target_content').html('<div class="alert alert-danger">Failed to load history.</div>');
                        return;
                    }

                    let tpl = res.assignment ? (res.assignment.template || {}) : {};
                    let assignedName = 'Team Member';
                    if (res.assignment && res.assignment.box_user) {
                        let bu = res.assignment.box_user;
                        assignedName = (bu.first_name || '') + ' ' + (bu.last_name || '');
                        if (!assignedName.trim() && bu.user) assignedName = bu.user.name;
                    }

                    $('#details_target_title').text(tpl.name || 'Target Details');
                    $('#details_target_subtitle').text('Assigned to: ' + assignedName);

                    let historyItems = res.history || [];

                    let historyRowsHtml = '';
                    historyItems.forEach(item => {
                        let statusColor = 'bg-secondary';
                        if (item.status === 'Outstanding') statusColor = 'bg-success';
                        else if (item.status === 'Excellent') statusColor = 'bg-primary';
                        else if (item.status === 'Good') statusColor = 'bg-info';
                        else if (item.status === 'Average') statusColor = 'bg-warning';
                        else if (item.status === 'Needs Improvement') statusColor = 'bg-dark';
                        else if (item.status === 'Poor') statusColor = 'bg-danger';

                        let scoreBadge = `<span class="badge badge-pill bg-soft-success text-success fw-bold">${item.score}%</span> <span class="badge badge-pill ${statusColor}">${item.status}</span>`;

                        let metricsSubList = (item.metrics || []).map(m => {
                            let curr = (m.type === 'Currency') ? '₹' : '';
                            let isReached = (m.actual_value >= m.target_value);
                            return `<span class="badge badge-pill ${isReached ? 'badge-success' : 'badge-soft-info text-info'} me-1 mb-1">${escapeHtml(m.name)}: ${curr}${m.actual_value.toLocaleString('en-IN')} / ${curr}${m.target_value.toLocaleString('en-IN')}</span>`;
                        }).join('');

                        historyRowsHtml += `
                            <tr>
                                <td class="fw-semibold text-dark">${escapeHtml(item.month_label)}</td>
                                <td>${scoreBadge}</td>
                                <td>${metricsSubList}</td>
                            </tr>
                        `;
                    });

                    let html = `
                        <div class="mb-4">
                            <!-- Template Info Box -->
                            <div class="border rounded p-3 bg-light mb-4">
                                <div class="row">
                                    <div class="col-sm-6 mb-2">
                                        <small class="text-muted d-block">Target Template</small>
                                        <span class="fw-semibold text-dark fs-15">${escapeHtml(tpl.name || 'Target Template')}</span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <small class="text-muted d-block">Assigned Member</small>
                                        <span class="fw-semibold text-primary fs-15">${escapeHtml(assignedName)}</span>
                                    </div>
                                    <div class="col-sm-3">
                                        <small class="text-muted d-block">Frequency</small>
                                        <span class="fw-medium">${escapeHtml(tpl.frequency || 'Monthly')}</span>
                                    </div>
                                    <div class="col-sm-3">
                                        <small class="text-muted d-block">Calculation Method</small>
                                        <span class="fw-medium">${escapeHtml(tpl.calculation_method || 'Fixed Target')}</span>
                                    </div>
                                    <div class="col-sm-3">
                                        <small class="text-muted d-block">Start Date</small>
                                        <span class="fw-medium">${tpl.start_date || 'N/A'}</span>
                                    </div>
                                    <div class="col-sm-3">
                                        <small class="text-muted d-block">End Date</small>
                                        <span class="fw-medium">${tpl.end_date || 'N/A'}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabs -->
                            <ul class="nav nav-tabs nav-tabs-bottom mb-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#tab_monthly_history" role="tab">
                                        <i class="ti ti-calendar-time me-1"></i> All Months Historical Performance (${historyItems.length} Months)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tab_metrics_config" role="tab">
                                        <i class="ti ti-list-details me-1"></i> Target Metrics & Weights
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <!-- History Tab -->
                                <div class="tab-pane show active" id="tab_monthly_history" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped align-middle">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 22%;">Month / Period</th>
                                                    <th style="width: 28%;">Achievement Score</th>
                                                    <th style="width: 50%;">Actual Achieved vs Target</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${historyRowsHtml}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Metrics Config Tab -->
                                <div class="tab-pane" id="tab_metrics_config" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Metric Name</th>
                                                    <th>Target Value</th>
                                                    <th>Weight (%)</th>
                                                    <th>Type</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${(tpl.metrics || []).map(m => `
                                                    <tr>
                                                        <td class="fw-medium">${escapeHtml(m.metric_name)}</td>
                                                        <td>${(m.metric_type === 'Currency' ? '₹' : '') + parseFloat(m.target_value || 0).toLocaleString('en-IN')}</td>
                                                        <td><span class="badge bg-soft-primary text-primary">${m.weight || 0}%</span></td>
                                                        <td>${escapeHtml(m.metric_type || 'Number')}</td>
                                                    </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#details_target_content').html(html);
                }).fail(function() {
                    $('#details_target_content').html('<div class="alert alert-danger">Error loading monthly performance history.</div>');
                });
            });
        });
    })();
</script>
@endpush
@endsection
