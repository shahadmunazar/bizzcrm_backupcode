@extends('layouts.main')
@section('title', 'Target Template')
@section('content')
<div class="page-wrapper">
	<div class="content">

		<div class="row">
			<div class="col-md-12">

				<!-- Page Header -->
				<div class="page-header">
					<div class="row align-items-center">
						<div class="col-8">
							<h4 class="page-title">Target Template</h4>
						</div>
						<div class="col-4 text-end">
							<div class="head-icons">
								<a href="javascript:void(0);" id="btn_refresh_templates" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
								<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
							</div>
						</div>
					</div>
				</div>
				<!-- /Page Header -->

				<div class="card">
					<div class="card-header">
						<!-- Search & Action Buttons -->
						<div class="row align-items-center">
							<div class="col-sm-4">
								<div class="icon-form mb-3 mb-sm-0">
									<span class="form-icon"><i class="ti ti-search"></i></span>
									<input type="text" class="form-control global-search" placeholder="Search For Target">
								</div>
							</div>
							<div class="col-sm-8">
								<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
									<a href="javascript:void(0);" class="btn btn-dark me-2" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-file-check me-2"></i> Create New Tamplate</a>
									<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_assign"><i class="ti ti-square-rounded-plus me-2"></i> Assign Target</a>
								</div>
							</div>
						</div>
						<!-- /Search & Action Buttons -->
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
												<a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="recent_view">
													<i class="ti ti-circle-chevron-right me-1"></i>Recently Viewed
												</a>
											</li>
											<li>
												<a href="javascript:void(0);" class="dropdown-item sort-item" data-sort="recent_add">
													<i class="ti ti-circle-chevron-right me-1"></i>Recently Added
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
								<!-- Manage Columns Dropdown -->
								<div class="dropdown me-2">
									<a href="javascript:void(0);" class="btn bg-soft-purple text-purple" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-columns-3 me-2"></i>Manage Columns</a>
									<div class="dropdown-menu dropdown-menu-md-end dropdown-md p-3">
										<h4 class="mb-2 fw-semibold">Manage Columns</h4>
										<p class="mb-3">Toggle columns to show or hide them in the table.</p>
										<div class="border-top pt-3">
											<div class="d-flex align-items-center justify-content-between mb-3">
												<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Target Name</p>
												<div class="status-toggle">
													<input type="checkbox" id="col-target-name" class="check col-toggle" data-column="1" checked>
													<label for="col-target-name" class="checktoggle"></label>
												</div>
											</div>
											<div class="d-flex align-items-center justify-content-between mb-3">
												<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Target Type</p>
												<div class="status-toggle">
													<input type="checkbox" id="col-target-type" class="check col-toggle" data-column="2" checked>
													<label for="col-target-type" class="checktoggle"></label>
												</div>
											</div>
											<div class="d-flex align-items-center justify-content-between mb-3">
												<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Calculation Method</p>
												<div class="status-toggle">
													<input type="checkbox" id="col-calc-method" class="check col-toggle" data-column="3" checked>
													<label for="col-calc-method" class="checktoggle"></label>
												</div>
											</div>
											<div class="d-flex align-items-center justify-content-between mb-3">
												<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Frequency</p>
												<div class="status-toggle">
													<input type="checkbox" id="col-frequency" class="check col-toggle" data-column="4" checked>
													<label for="col-frequency" class="checktoggle"></label>
												</div>
											</div>
											<div class="d-flex align-items-center justify-content-between mb-3">
												<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Metrics</p>
												<div class="status-toggle">
													<input type="checkbox" id="col-metrics" class="check col-toggle" data-column="5" checked>
													<label for="col-metrics" class="checktoggle"></label>
												</div>
											</div>
											<div class="d-flex align-items-center justify-content-between mb-3">
												<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Status</p>
												<div class="status-toggle">
													<input type="checkbox" id="col-status" class="check col-toggle" data-column="6" checked>
													<label for="col-status" class="checktoggle"></label>
												</div>
											</div>
											<div class="d-flex align-items-center justify-content-between">
												<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Action</p>
												<div class="status-toggle">
													<input type="checkbox" id="col-action" class="check col-toggle" data-column="7" checked>
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
												<h4 class="fs-16 fw-semibold"><i class="ti ti-filter-share me-1"></i>Filter Templates</h4>
											</div>
											<div class="accordion" id="filterAccordion">
												<!-- Frequency Filter -->
												<div class="filter-set-content mb-3">
													<div class="filter-set-content-head mb-2">
														<a href="#" data-bs-toggle="collapse" data-bs-target="#filterFrequency" aria-expanded="true" aria-controls="filterFrequency" class="fw-medium text-dark">
															Frequency
														</a>
													</div>
													<div class="filter-set-contents accordion-collapse collapse show" id="filterFrequency" data-bs-parent="#filterAccordion">
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

												<!-- Calculation Method Filter -->
												<div class="filter-set-content mb-3">
													<div class="filter-set-content-head mb-2">
														<a href="#" class="collapsed fw-medium text-dark" data-bs-toggle="collapse" data-bs-target="#filterMethod" aria-expanded="false" aria-controls="filterMethod">
															Calculation Method
														</a>
													</div>
													<div class="filter-set-contents accordion-collapse collapse" id="filterMethod" data-bs-parent="#filterAccordion">
														<div class="filter-content-list">
															<ul class="list-unstyled mb-0">
																@foreach(['Manual', 'Salary Based', 'Hybrid'] as $method)
																	<li class="mb-2">
																		<div class="filter-checks">
																			<label class="checkboxs">
																				<input type="checkbox" class="filter-method-check" value="{{ $method }}">
																				<span class="checkmarks"></span>
																				{{ $method }}
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
														<a href="#" class="collapsed fw-medium text-dark" data-bs-toggle="collapse" data-bs-target="#filterStatus" aria-expanded="false" aria-controls="filterStatus">
															Status
														</a>
													</div>
													<div class="filter-set-contents accordion-collapse collapse" id="filterStatus" data-bs-parent="#filterAccordion">
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
															<a href="javascript:void(0);" id="btn_reset_filter" class="btn btn-light w-100">Reset</a>
														</div>
														<div class="col-6">
															<a href="javascript:void(0);" id="btn_apply_filter" class="btn btn-primary w-100">Filter</a>
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

						<!-- Target Template Table -->
						<div class="table-responsive custom-table">
							<table class="table" id="target_template_table">
								<thead class="thead-light">
									<tr>
										<th class="no-sort">
											<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
										</th>
										<th>Target Name</th>
										<th>Target Type</th>
										<th>Calculation Method</th>
										<th>Frequency</th>
										<th>Metrics</th>
										<th>Status</th>
										<th class="text-end">Action</th>
									</tr>
								</thead>
								<tbody id="target_templates_tbody">
									@forelse($templates as $tpl)
										@php
											$calcMethod = e($tpl->calculation_method ?? '');
											if ($tpl->calculation_method === 'Salary Based' && $tpl->salary_multiplier) {
												$calcMethod .= '<br><small class="text-muted">(x' . e($tpl->salary_multiplier) . ')</small>';
											}
											$status = $tpl->status ?? 'Active';
											$statusBadge = ($status === 'Inactive') ? 'bg-danger' : 'bg-success';
										@endphp
										<tr data-id="{{ $tpl->id }}">
											<td>
												<label class="checkboxs"><input type="checkbox" class="template-checkbox" value="{{ $tpl->id }}"><span class="checkmarks"></span></label>
											</td>
											<td class="fw-medium text-dark">{{ $tpl->name }}</td>
											<td>{{ $tpl->target_type ?? 'Generic' }}</td>
											<td>{!! $calcMethod !!}</td>
											<td>{{ $tpl->frequency }}</td>
											<td>
												@if($tpl->metrics && $tpl->metrics->count() > 0)
													@foreach($tpl->metrics as $metric)
														<span class="badge badge-pill badge-info me-1 mb-1">{{ $metric->metric_name }}</span>
													@endforeach
												@else
													<span class="text-muted">-</span>
												@endif
											</td>
											<td>
												<div class="d-flex align-items-center">
													<div class="status-toggle me-2">
														<input type="checkbox" id="tpl_status_{{ $tpl->id }}" class="check toggle-template-status" data-id="{{ $tpl->id }}" {{ $status === 'Active' ? 'checked' : '' }}>
														<label for="tpl_status_{{ $tpl->id }}" class="checktoggle"></label>
													</div>
													<span class="badge badge-pill {{ $statusBadge }} toggle-template-badge" data-id="{{ $tpl->id }}" style="cursor: pointer; user-select: none;" title="Click to toggle status">{{ $status }}</span>
												</div>
											</td>
											<td class="text-end">
												<div class="dropdown table-action">
													<a href="javascript:void(0);" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false">
														<i class="fa fa-ellipsis-v"></i>
													</a>
													<div class="dropdown-menu dropdown-menu-right">
														<a class="dropdown-item toggle-status-action" href="javascript:void(0);" data-id="{{ $tpl->id }}">
															<i class="ti {{ $status === 'Active' ? 'ti-toggle-left text-warning' : 'ti-toggle-right text-success' }} me-1"></i>
															<span>{{ $status === 'Active' ? 'Mark Inactive' : 'Mark Active' }}</span>
														</a>
														<a class="dropdown-item" href="javascript:void(0);" onclick="editTemplate({{ $tpl->id }})">
															<i class="ti ti-edit text-blue me-1"></i>Edit
														</a>
														<a class="dropdown-item" href="javascript:void(0);" onclick="confirmDeleteTemplate({{ $tpl->id }})">
															<i class="ti ti-trash text-danger me-1"></i>Delete
														</a>
													</div>
												</div>
											</td>
										</tr>
									@empty
										<tr>
											<td colspan="8" class="text-center py-4 text-muted">
												<i class="ti ti-file-alert fs-24 d-block mb-1"></i>
												No target templates found
											</td>
										</tr>
									@endforelse
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
						<!-- /Target Template Table -->

					</div>
				</div>

			</div>
		</div>

	</div>
</div>
<!-- /Page Wrapper -->

@include("fieldstracking.partials.target-modals")

@endsection
