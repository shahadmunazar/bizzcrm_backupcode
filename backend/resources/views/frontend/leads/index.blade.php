@extends('layouts.main')
@section('title', 'Leads Dashboard')
@section('description', 'Overview of lead performance, pipeline stages, and recent activities.')

@section('content')
	<!-- Page Wrapper -->
	<div class="page-wrapper">
		<div class="content">

			<!-- Breadcrumb -->
			<div class="d-md-flex d-block align-items-center justify-content-between mb-3">
				<div class="my-auto mb-2">
					<h2 class="mb-1">Leads Dashboard </h2>
					<nav>
						<ol class="breadcrumb mb-0">
							<li class="breadcrumb-item">
								<a href="index.html"><i class="ti ti-smart-home"></i></a>
							</li>
							<li class="breadcrumb-item">
								Dashboard
							</li>
							<li class="breadcrumb-item active" aria-current="page">Leads Dashboard </li>
						</ol>
					</nav>
				</div>
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
					<div class="input-icon mb-2 position-relative">
						<span class="input-icon-addon">
							<i class="ti ti-calendar text-gray-9"></i>
						</span>
						<input type="text" class="form-control date-range bookingrange"
							placeholder="dd/mm/yyyy - dd/mm/yyyy" />
					</div>
					<div class="ms-2 head-icons">
						<a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top"
							data-bs-original-title="Collapse" id="collapse-header">
							<i class="ti ti-chevrons-up"></i>
						</a>
					</div>
				</div>
			</div>
			<!-- /Breadcrumb -->

			<x-preference-indicator module="leads" />

			<div class="row">
				<div class="col-xl-2 col-lg-4 col-sm-6 col-6">
					<div class="card position-relative cursor-pointer" style="cursor: pointer;"
						onclick="window.location.href='{{ route('leads.details', ['box_slug' => request()->route('box_slug')]) }}'">
						<div class="card-body">
							<div class="d-flex align-items-center mb-3">
								<div class="avatar avatar-md br-10 icon-rotate bg-primary flex-shrink-0">
									<span class="d-flex align-items-center"><i
											class="ti ti-delta text-white fs-16"></i></span>
								</div>
								<div class="ms-3 overflow-hidden w-100">
									<p class="fw-medium text-truncate mb-1">Total Leads </p>
									<h5><span id="total-leads-count">...</span></h5>
								</div>
							</div>
							<div class="progress progress-xs mb-2">
								<div class="progress-bar bg-primary" role="progressbar" style="width: 40%"></div>
							</div>
							<p class="fw-medium fs-13 mb-0" id="total-leads-change-container"><span
									class="text-danger fs-12"><i class="ti ti-arrow-wave-right-up me-1"></i>...% </span>
								from last week </p>
						</div>
					</div>
				</div>
				<div class="col-xl-2 col-lg-4 col-sm-6 col-6">
					<div class="card position-relative cursor-pointer" style="cursor: pointer;"
						onclick="window.location.href='{{ route('leads.details', ['box_slug' => request()->route('box_slug')]) }}?filter=new'">
						<div class="card-body">
							<div class="d-flex align-items-center mb-3">
								<div class="avatar avatar-md br-10 icon-rotate bg-secondary flex-shrink-0">
									<span class="d-flex align-items-center"><i
											class="ti ti-currency text-white fs-16"></i></span>
								</div>
								<div class="ms-3 overflow-hidden w-100">
									<p class="fw-medium text-truncate mb-1">New Leads </p>
									<h5><span id="new-leads-count">...</span></h5>
								</div>
							</div>
							<div class="progress progress-xs mb-2">
								<div class="progress-bar bg-secondary" role="progressbar" style="width: 40%"></div>
							</div>
							<p class="fw-medium fs-13 mb-0" id="new-leads-change-container"><span
									class="text-success fs-12"><i class="ti ti-arrow-wave-right-up me-1"></i>...% </span>
								from last week </p>
						</div>
					</div>
				</div>
				<div class="col-xl-2 col-lg-4 col-sm-6 col-6">
					<div class="card position-relative cursor-pointer" style="cursor: pointer;"
						onclick="window.location.href='{{ route('leads.details', ['box_slug' => request()->route('box_slug')]) }}?filter=lost'">
						<div class="card-body">
							<div class="d-flex align-items-center mb-3">
								<div class="avatar avatar-md br-10 icon-rotate bg-danger flex-shrink-0">
									<span class="d-flex align-items-center"><i
											class="ti ti-stairs-up text-white fs-16"></i></span>
								</div>
								<div class="ms-3 overflow-hidden w-100">
									<p class="fw-medium text-truncate mb-1">Lost Leads </p>
									<h5><span id="lost-leads-count">...</span></h5>
								</div>
							</div>
							<div class="progress progress-xs mb-2">
								<div class="progress-bar bg-pink" role="progressbar" style="width: 40%"></div>
							</div>
							<p class="fw-medium fs-13 mb-0" id="lost-leads-change-container"><span
									class="text-success fs-12"><i class="ti ti-arrow-wave-right-up me-1"></i>...% </span>
								from last week </p>
						</div>
					</div>
				</div>
				<div class="col-xl-2 col-lg-4 col-sm-6 col-6">
					<div class="card position-relative cursor-pointer" style="cursor: pointer;"
						onclick="window.location.href='{{ route('reports.AllActivityLeads', ['box_slug' => request()->route('box_slug')]) }}'">
						<div class="card-body">
							<div class="d-flex align-items-center mb-3">
								<div class="avatar avatar-md br-10 icon-rotate bg-purple flex-shrink-0">
									<span class="d-flex align-items-center"><i
											class="ti ti-users-group text-white fs-16"></i></span>
								</div>
								<div class="ms-3 overflow-hidden w-100">
									<p class="fw-medium text-truncate mb-1">Total Activities</p>
									<h5><span id="total-activities-count">...</span></h5>
								</div>
							</div>
							<div class="progress progress-xs mb-2">
								<div class="progress-bar bg-purple" role="progressbar" style="width: 40%"></div>
							</div>
							<p class="fw-medium fs-13 mb-0" id="total-activities-change-container"><span
									class="text-danger fs-12"><i class="ti ti-arrow-wave-right-up me-1"></i>...% </span>
								from last week </p>
						</div>
					</div>
				</div>

				<div class="col-xl-2 col-lg-4 col-sm-6 col-6">
					<div class="card position-relative cursor-pointer" style="cursor: pointer;"
						onclick="window.location.href='{{ route('leads.details', ['box_slug' => request()->route('box_slug')]) }}?filter=unassigned'">
						<div class="card-body">
							<div class="d-flex align-items-center mb-3">
								<div class="avatar avatar-md br-10 icon-rotate bg-warning flex-shrink-0">
									<span class="d-flex align-items-center"><i
											class="ti ti-user-x text-white fs-16"></i></span>
								</div>
								<div class="ms-3 overflow-hidden w-100">
									<p class="fw-medium text-truncate mb-1">Unassigned Leads </p>
									<h5><span id="unassigned-leads-count">...</span></h5>
								</div>
							</div>
							<div class="progress progress-xs mb-2">
								<div class="progress-bar bg-warning" role="progressbar" style="width: 40%"></div>
							</div>
							<p class="fw-medium fs-13 mb-0" id="unassigned-leads-change-container">
								<a href="{{ route('leads.details', ['box_slug' => request()->route('box_slug')]) }}?filter=unassigned"
									class="text-primary">View Unassigned</a>
							</p>
						</div>
					</div>
				</div>
				<div class="col-xl-2 col-lg-4 col-sm-6 col-6">
					<div class="card position-relative cursor-pointer" style="cursor: pointer;"
						onclick="window.location.href='{{ route('leads.details.trash', ['box_slug' => request()->route('box_slug')]) }}'">
						<div class="card-body">
							<div class="d-flex align-items-center mb-3">
								<div class="avatar avatar-md br-10 icon-rotate bg-dark flex-shrink-0">
									<span class="d-flex align-items-center"><i
											class="ti ti-trash text-white fs-16"></i></span>
								</div>
								<div class="ms-3 overflow-hidden w-100">
									<p class="fw-medium text-truncate mb-1">Trash Leads </p>
									<h5><span id="trash-leads-count">...</span></h5>
								</div>
							</div>
							<div class="progress progress-xs mb-2">
								<div class="progress-bar bg-dark" role="progressbar" style="width: 40%"></div>
							</div>
							<p class="fw-medium fs-13 mb-0" id="trash-leads-change-container">
								<a href="{{ route('leads.details.trash', ['box_slug' => request()->route('box_slug')]) }}"
									class="text-primary">View Trash</a>
							</p>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xl-2 col-lg-4 col-sm-6 col-6">
					<div class="card position-relative cursor-pointer"
						onclick="window.location.href='{{ route('leads.details.duplicate', ['box_slug' => request()->route('box_slug')]) }}'">
						<div class="card-body">
							<div class="d-flex align-items-center mb-3">
								<div class="avatar avatar-md br-10 icon-rotate bg-info flex-shrink-0">
									<span class="d-flex align-items-center"><i
											class="ti ti-copy text-white fs-16"></i></span>
								</div>
								<div class="ms-3 overflow-hidden w-100">
									<p class="fw-medium text-truncate mb-1">Duplicate Leads </p>
									<h5><span id="duplicate-leads-count">...</span></h5>
								</div>
							</div>
							<div class="progress progress-xs mb-2">
								<div class="progress-bar bg-info" role="progressbar" style="width: 40%"></div>
							</div>
							<p class="fw-medium fs-13 mb-0" id="duplicate-leads-change-container">
								<a href="{{ route('leads.details.duplicate', ['box_slug' => request()->route('box_slug')]) }}"
									class="text-primary">View Duplicates</a>
							</p>
						</div>
					</div>
				</div>

				<!-- Today's Tasks -->
				<div class="col-xl-2 col-lg-4 col-sm-6 col-6">
					<div class="card position-relative cursor-pointer"
						onclick="window.location.href='{{ route('reports.TodaysFollowUp', ['box_slug' => request()->route('box_slug')]) }}'">
						<div class="card-body">
							<div class="d-flex align-items-center mb-3">
								<div class="avatar avatar-md br-10 icon-rotate bg-info flex-shrink-0">
									<span class="d-flex align-items-center"><i
											class="fa-solid fa-calendar-day text-white fs-16"></i></span>
								</div>
								<div class="ms-3 overflow-hidden w-100">
									<p class="fw-medium text-truncate mb-1">Today's Task </p>
									<h5><span id="task_today-count"><i class="ti ti-loader rotate"></i></span></h5>
								</div>
							</div>
							<div class="progress progress-xs mb-2">
								<div class="progress-bar bg-info" role="progressbar" style="width: 100%"></div>
							</div>
							<p class="fw-medium fs-13 mb-0">
								<a href="{{ route('reports.TodaysFollowUp', ['box_slug' => request()->route('box_slug')]) }}"
									class="text-primary">View Today</a>
							</p>
						</div>
					</div>
				</div>

				<!-- Open Tasks -->
				<div class="col-xl-2 col-lg-4 col-sm-6 col-6">
					<div class="card position-relative cursor-pointer"
						onclick="window.location.href='{{ route('reports.ReminderList', ['box_slug' => request()->route('box_slug')]) }}'">
						<div class="card-body">
							<div class="d-flex align-items-center mb-3">
								<div class="avatar avatar-md br-10 icon-rotate bg-warning flex-shrink-0">
									<span class="d-flex align-items-center"><i
											class="fa-solid fa-folder-open text-white fs-16"></i></span>
								</div>
								<div class="ms-3 overflow-hidden w-100">
									<p class="fw-medium text-truncate mb-1">Open Task </p>
									<h5><span id="task_open-count"><i class="ti ti-loader rotate"></i></span></h5>
								</div>
							</div>
							<div class="progress progress-xs mb-2">
								<div class="progress-bar bg-warning" role="progressbar" style="width: 100%"></div>
							</div>
							<p class="fw-medium fs-13 mb-0">
								<a href="{{ route('reports.ReminderList', ['box_slug' => request()->route('box_slug')]) }}"
									class="text-primary">View Open</a>
							</p>
						</div>
					</div>
				</div>

				<!-- Closed on Time -->
				<div class="col-xl-2 col-lg-4 col-sm-6 col-6">
					<div class="card position-relative cursor-pointer" id="card_closed_ontime"
						onclick="window.location.href='{{ route('reports.AllDSR', ['box_slug' => request()->route('box_slug')]) }}?status=Closed&task_timing=on_time'">
						<div class="card-body">
							<div class="d-flex align-items-center mb-3">
								<div class="avatar avatar-md br-10 icon-rotate bg-success flex-shrink-0">
									<span class="d-flex align-items-center"><i
											class="fa-solid fa-clock-rotate-left text-white fs-16"></i></span>
								</div>
								<div class="ms-3 overflow-hidden w-100">
									<p class="fw-medium mb-1" style="white-space: nowrap;">Closed on time </p>
									<h5><span id="task_closed_ontime-count"><i class="ti ti-loader rotate"></i></span></h5>
								</div>
							</div>
							<div class="progress progress-xs mb-2">
								<div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
							</div>
							<p class="fw-medium fs-13 mb-0">
								<a id="link_closed_ontime" href="{{ route('reports.AllDSR', ['box_slug' => request()->route('box_slug')]) }}?status=Closed&task_timing=on_time"
									class="text-primary">View Closed</a>
							</p>
						</div>
					</div>
				</div>

				<!-- Closed Late -->
				<div class="col-xl-2 col-lg-4 col-sm-6 col-6">
					<div class="card position-relative cursor-pointer" id="card_closed_late"
						onclick="window.location.href='{{ route('reports.AllDSR', ['box_slug' => request()->route('box_slug')]) }}?status=Closed&task_timing=late'">
						<div class="card-body">
							<div class="d-flex align-items-center mb-3">
								<div class="avatar avatar-md br-10 icon-rotate bg-danger flex-shrink-0">
									<span class="d-flex align-items-center"><i
											class="fa-solid fa-circle-exclamation text-white fs-16"></i></span>
								</div>
								<div class="ms-3 overflow-hidden w-100">
									<p class="fw-medium mb-1" style="white-space: nowrap;">Closed late </p>
									<h5><span id="task_closed_late-count"><i class="ti ti-loader rotate"></i></span></h5>
								</div>
							</div>
							<div class="progress progress-xs mb-2">
								<div class="progress-bar bg-danger" role="progressbar" style="width: 100%"></div>
							</div>
							<p class="fw-medium fs-13 mb-0">
								<a id="link_closed_late" href="{{ route('reports.AllDSR', ['box_slug' => request()->route('box_slug')]) }}?status=Closed&task_timing=late"
									class="text-primary">View Late</a>
							</p>
						</div>
					</div>
				</div>

				<!-- Failed Tasks -->
				<div class="col-xl-2 col-lg-4 col-sm-6 col-6">
					<div class="card position-relative cursor-pointer" id="card_failed"
						onclick="window.location.href='{{ route('reports.AllDSR', ['box_slug' => request()->route('box_slug')]) }}?status=Closed&task_timing=failed'">
						<div class="card-body">
							<div class="d-flex align-items-center mb-3">
								<div class="avatar avatar-md br-10 icon-rotate bg-warning flex-shrink-0">
									<span class="d-flex align-items-center"><i
											class="fa-solid fa-xmark text-white fs-16"></i></span>
								</div>
								<div class="ms-3 overflow-hidden w-100">
									<p class="fw-medium mb-1" style="white-space: nowrap;">Failed Task </p>
									<h5><span id="task_failed-count"><i class="ti ti-loader rotate"></i></span></h5>
								</div>
							</div>
							<div class="progress progress-xs mb-2">
								<div class="progress-bar bg-warning" role="progressbar" style="width: 100%"></div>
							</div>
							<p class="fw-medium fs-13 mb-0">
								<a id="link_failed" href="{{ route('reports.AllDSR', ['box_slug' => request()->route('box_slug')]) }}?status=Closed&task_timing=failed"
									class="text-primary">View Failed</a>
							</p>
						</div>
					</div>
				</div>
			</div>


			<div class="row">
				<div class="col-xl-12 d-flex">
					<div class="card flex-fill">
						<div class="card-header">
							<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
								<h5>Lead Status </h5>

							</div>
						</div>
						<div class="card-body pb-0">




							<div class="row flex-fill" id="lead-status-boxes">
								<div class="col-12 text-center p-4">
									<span class="spinner-border spinner-border-sm text-primary" role="status"
										aria-hidden="true"></span> Loading status data...
								</div>
							</div>

						</div>
					</div>
				</div>

			</div>




			<div class="row">
				<div class="col-xl-8 d-flex">
					<div class="card flex-fill">
						<div class="card-header">
							<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
								<h5>Pipeline Stages </h5>
								<div class="dropdown">
									<a href="javascript:void(0);"
										class="btn btn-white border btn-sm d-inline-flex align-items-center chart-year-display"
										data-bs-toggle="dropdown">
										<i class="ti ti-calendar me-1"></i>{{ $defaultYear }}
									</a>
									<ul class="dropdown-menu dropdown-menu-end p-3 chart-year-selector">
										@foreach ($availableYears as $year)
											<li>
												<a href="javascript:void(0);"
													class="dropdown-item rounded-1">{{ $year }}</a>
											</li>
										@endforeach
									</ul>
								</div>
							</div>
						</div>
						<div class="card-body pb-0">
							<div class="row g-2 justify-content-center mb-3" id="pipeline-stages-container">
								<div class="col-md col-sm-4 col-6">
									<div class="border rounded p-2">
										<p class="mb-1"><i class="ti ti-point-filled text-primary"></i>Contacted </p>
										<h6>50000 </h6>
									</div>
								</div>
								<div class="col-md col-sm-4 col-6">
									<div class="border rounded p-2">
										<p class="mb-1"><i class="ti ti-point-filled text-primary"></i>Oppurtunity </p>
										<h6>25985 </h6>
									</div>
								</div>
								<div class="col-md col-sm-4 col-6">
									<div class="border rounded p-2">
										<p class="mb-1"><i class="ti ti-point-filled text-primary"></i>Not Contacted </p>
										<h6>12566 </h6>
									</div>
								</div>
								<div class="col-md col-sm-4 col-6">
									<div class="border rounded p-2">
										<p class="mb-1"><i class="ti ti-point-filled text-primary"></i>Closed </p>
										<h6>8965 </h6>
									</div>
								</div>
								<div class="col-md col-sm-4 col-6">
									<div class="border rounded p-2">
										<p class="mb-1"><i class="ti ti-point-filled text-primary"></i>Lost </p>
										<h6>2452 </h6>
									</div>
								</div>
							</div>
							<div id="pipeline-stages-chart"></div>
						</div>
					</div>
				</div>
				<div class="col-xl-4 d-flex">
					<div class="card flex-fill">
						<div class="card-header">
							<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
								<h5>New Leads </h5>
								<div class="dropdown">
									<a href="javascript:void(0);"
										class="btn btn-white border btn-sm d-inline-flex align-items-center"
										data-bs-toggle="dropdown">
										<i class="ti ti-calendar me-1"></i>This Week
									</a>
									<ul class="dropdown-menu  dropdown-menu-end p-3">
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1">This Week </a>
										</li>
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1">This Month </a>
										</li>
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1">This Year </a>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="card-body pb-0">
							<div id="heat_chart"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xl-4 d-flex">
					<div class="card flex-fill">
						<div class="card-header">
							<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
								<h5>Lost Leads By Reason </h5>
								<div class="dropdown">
									<a href="javascript:void(0);"
										class="btn btn-white border-0 dropdown-toggle btn-sm d-inline-flex align-items-center"
										data-bs-toggle="dropdown" aria-expanded="false">
										Sales Pipeline
									</a>
									<ul class="dropdown-menu dropdown-menu-end p-3">
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1">This Month </a>
										</li>
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1">This Week </a>
										</li>
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1">Last Week </a>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="card-body py-0">
							<div>
								<div id="leads_stage"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-4 d-flex">
					<div class="card flex-fill">
						<div class="card-header">
							<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
								<h5>Leads By Companies </h5>
								<div class="dropdown">
									<a href="javascript:void(0);"
										class="btn btn-white border btn-sm d-inline-flex align-items-center"
										data-bs-toggle="dropdown">
										<i class="ti ti-calendar me-1"></i>This Week
									</a>
									<ul class="dropdown-menu  dropdown-menu-end p-3">
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1">This Month </a>
										</li>
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1">This Week </a>
										</li>
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1">Last Week </a>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="card-body">
							<div id="leads-by-companies">
								<!-- Will be loaded dynamically -->
								<div class="text-center p-3 text-muted">
									<i class="ti ti-loader rotate font-20"></i> Loading company data...
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-4 d-flex">
					<div class="card flex-fill">
						<div class="card-header">
							<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
								<h5>Leads by Source </h5>
								<div class="dropdown">
									<a href="javascript:void(0);"
										class="btn btn-white border btn-sm d-inline-flex align-items-center"
										data-bs-toggle="dropdown">
										<i class="ti ti-calendar me-1"></i>This Week
									</a>
									<ul class="dropdown-menu  dropdown-menu-end p-3">
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1">This Month </a>
										</li>
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1">This Week </a>
										</li>
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1">Last Week </a>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="card-body">
							<div id="donut-chart-2"></div>
							<div id="leads-source-list">
								<h6 class="mb-3">Status </h6>
								<div class="d-flex align-items-center justify-content-between mb-2">
									<p class="f-13 mb-0"><i class="ti ti-circle-filled text-secondary me-1"></i>Google </p>
									<p class="f-13 fw-medium text-gray-9">40% </p>
								</div>
								<div class="d-flex align-items-center justify-content-between mb-2">
									<p class="f-13 mb-0"><i class="ti ti-circle-filled text-warning me-1"></i>Paid </p>
									<p class="f-13 fw-medium text-gray-9">35% </p>
								</div>
								<div class="d-flex align-items-center justify-content-between mb-2">
									<p class="f-13 mb-0"><i class="ti ti-circle-filled text-pink me-1"></i>Campaigns </p>
									<p class="f-13 fw-medium text-gray-9">15% </p>
								</div>
								<div class="d-flex align-items-center justify-content-between">
									<p class="f-13 mb-0"><i class="ti ti-circle-filled text-purple me-1"></i>Referals </p>
									<p class="f-13 fw-medium text-gray-9">10% </p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>






			<!--<div class="row">

				
				<div class="col-md-6">
					<div class="card">
						<div class="card-header">
							<h5 class="card-title">Apex Simple</h5>
						</div>
						<div class="card-body">
							<div id="s-line" class="chart-set"></div>
						</div>
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="card">
						<div class="card-header">
							<h5 class="card-title">Area Chart</h5>
						</div>
						<div class="card-body">
							<div id="s-line-area" class="chart-set"></div>
						</div>
					</div>
				</div>
				

			</div> -->





			<div class="row">
				<!-- Chart -->
				<div class="col-md-6">
					<div class="card">
						<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-2">
							<h5 class="card-title">Leads By Status Contacted or Not Contacted</h5>
							<div class="dropdown">
								<a href="javascript:void(0);"
									class="btn btn-white border btn-sm d-inline-flex align-items-center chart-year-display"
									data-bs-toggle="dropdown">
									<i class="ti ti-calendar me-1"></i>{{ $defaultYear }}
								</a>
								<ul class="dropdown-menu dropdown-menu-end p-3 chart-year-selector">
									@foreach ($availableYears as $year)
										<li>
											<a href="javascript:void(0);"
												class="dropdown-item rounded-1">{{ $year }}</a>
										</li>
									@endforeach
								</ul>
							</div>
						</div>
						<div class="card-body">
							<div id="contacted-status-chart" class="chart-set"></div>
						</div>
					</div>
				</div>
				<!-- /Chart -->

				<!-- Chart -->
				<div class="col-md-6">
					<div class="card">
						<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-2">
							<h5 class="card-title">Leads By Sources</h5>
							<div class="dropdown">
								<a href="javascript:void(0);"
									class="btn btn-white border btn-sm d-inline-flex align-items-center chart-year-display"
									data-bs-toggle="dropdown">
									<i class="ti ti-calendar me-1"></i>{{ $defaultYear }}
								</a>
								<ul class="dropdown-menu dropdown-menu-end p-3 chart-year-selector">
									@foreach ($availableYears as $year)
										<li>
											<a href="javascript:void(0);"
												class="dropdown-item rounded-1">{{ $year }}</a>
										</li>
									@endforeach
								</ul>
							</div>
						</div>
						<div class="card-body">
							<div id="s-col-stacked" class="chart-set"></div>
						</div>
					</div>
				</div>
				<!-- /Chart -->
			</div>

			<div class="row">
				<!-- Assigned Leads By User Chart -->
				<div class="col-md-12">
					<div class="card">
						<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-2">
							<h5 class="card-title">Assigned Leads By User (Monthly)</h5>
							<div class="dropdown">
								<a href="javascript:void(0);"
									class="btn btn-white border btn-sm d-inline-flex align-items-center chart-year-display"
									data-bs-toggle="dropdown">
									<i class="ti ti-calendar me-1"></i>{{ $defaultYear }}
								</a>
								<ul class="dropdown-menu dropdown-menu-end p-3 chart-year-selector">
									@foreach ($availableYears as $year)
										<li>
											<a href="javascript:void(0);"
												class="dropdown-item rounded-1">{{ $year }}</a>
										</li>
									@endforeach
								</ul>
							</div>
						</div>
						<div class="card-body">
							<div id="assigned-leads-user-chart" class="chart-set"></div>
						</div>
					</div>
				</div>
				<!-- /Chart -->
			</div>










			<div class="row">
				<div class="col-xl-4 d-flex">
					<div class="card flex-fill">
						<div class="card-header">
							<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
								<h5>Recent Follow Up </h5>
								<div>
									<a href="javascript:void(0);" class="btn btn-light btn-sm px-3">View All </a>
								</div>
							</div>
						</div>
						<div class="card-body" id="recent-follow-up-list">
							<!-- Will be loaded dynamically -->
							<div class="text-center p-3 text-muted">
								<i class="ti ti-loader rotate font-20"></i> Loading follow-ups...
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-4 d-flex">
					<div class="card flex-fill">
						<div class="card-header">
							<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
								<h5>Recent Activities </h5>
								<div>
									<a href="javascript:void(0);" class="btn btn-sm btn-light px-3">View All </a>
								</div>
							</div>
						</div>
						<div class="card-body schedule-timeline activity-timeline" id="recent-activities-list">
							<div class="d-flex align-items-start">
								<div class="avatar avatar-md avatar-rounded bg-success flex-shrink-0">
									<i class="ti ti-phone fs-20"></i>
								</div>
								<div class="flex-fill ps-3 pb-4 timeline-flow">
									<p class="fw-medium text-gray-9 mb-1"><a href="javascript:void(0);">Drain responded to
											your
											___________ schedule question. </a></p>
									<span>09:25 PM </span>
								</div>
							</div>
							<div class="d-flex align-items-start">
								<div class="avatar avatar-md avatar-rounded bg-info flex-shrink-0">
									<i class="ti ti-message-circle-2 fs-20"></i>
								</div>
								<div class="flex-fill ps-3 pb-4 timeline-flow">
									<p class="fw-medium text-gray-9 mb-1"><a href="javascript:void(0);">You sent 1 Message
											__ the
											James. </a></p>
									<span>10:25 PM </span>
								</div>
							</div>
							<div class="d-flex align-items-start">
								<div class="avatar avatar-md avatar-rounded bg-success flex-shrink-0">
									<i class="ti ti-phone fs-20"></i>
								</div>
								<div class="flex-fill ps-3 pb-4 timeline-flow">
									<p class="fw-medium text-gray-9 mb-1"><a href="javascript:void(0);">Denwar responded to
											your
											___________ on 25 Jan 2025, 08:15 PM </a></p>
									<span>09:25 PM </span>
								</div>
							</div>
							<div class="d-flex align-items-start">
								<div class="avatar avatar-md avatar-rounded bg-purple flex-shrink-0">
									<i class="ti ti-user-circle fs-20"></i>
								</div>
								<div class="flex-fill ps-3 timeline-flow">
									<p class="fw-medium text-gray-9 mb-1"><a href="javascript:void(0);"
											class="d-flex align-items-center">Meeting With Abraham </a></p>
									<span>09:25 PM </span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-4 d-flex">
					<div class="card flex-fill">
						<div class="card-header">
							<div class="d-flex align-items-center justify-content-between flex-wrap">
								<h5>Notifications </h5>
								<div>
									<a href="javascript:void(0);" class="btn btn-light btn-sm px-3">View All </a>
								</div>
							</div>
						</div>
						<div class="card-body" id="dashboard-notifications-container">
							<!-- Notifications will be loaded here -->
							<div class="text-center">
								<span class="spinner-border spinner-border-sm text-primary" role="status"
									aria-hidden="true"></span> Loading...
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Check if JQuery is loaded, if not we might need to rely on the main layout's jquery calling this script or put this in a push stack. 
																												  Safest is to put the script at the end of the section or rely on document ready. -->






			<div class="row">
				<!-- Row 1: Countries & Created Leads -->
				<div class="col-xl-5 d-flex">
					<div class="card flex-fill">
						<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-2">
							<h5>Top Countries </h5>
						</div>
						<div class="card-body">
							<div class="row align-items-center">
								<div class="col-xxl-5 col-sm-6">
									<div class="pe-3 border-end" id="top-countries-list">
										<div class="text-center p-3 text-muted">
											<i class="ti ti-loader rotate font-20"></i> Loading...
										</div>
									</div>
								</div>
								<div class="col-xxl-7 col-sm-6">
									<div id="donut-chart-3"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-7 d-flex">
					<div class="card flex-fill">
						<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-2">
							<h5>Recently Created Leads </h5>
							<div class="d-flex align-items-center">
								<div>
									<a href="{{ route('leads.details', ['box_slug' => request()->route('box_slug')]) }}"
										class="btn btn-sm btn-light px-3">View All </a>
								</div>
							</div>
						</div>
						<div class="card-body p-0">
							<div class="table-responsive">
								<table class="table table-nowrap dashboard-table mb-0">
									<thead>
										<tr>
											<th>Name </th>
											<th>Company Name </th>
											<th>Created On </th>
											<th>Created From </th>
											<th>Assigned To </th>
										</tr>
									</thead>
									<tbody id="recent-leads-grid">
										<tr>
											<td colspan="5" class="text-center p-4">
												<i class="ti ti-loader rotate font-20"></i> Loading recent leads...
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<!-- Row 2: States & Assigned Leads -->
				<div class="col-xl-5 d-flex">
					<div class="card flex-fill">
						<div class="card-header">
							<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
								<h5>Top States </h5>
							</div>
						</div>
						<div class="card-body">
							<div class="row align-items-center">
								<div class="col-xxl-5 col-sm-6">
									<div class="pe-3 border-end" id="top-states-list">
										<div class="text-center p-3 text-muted">
											<i class="ti ti-loader rotate font-20"></i> Loading...
										</div>
									</div>
								</div>
								<div class="col-xxl-7 col-sm-6">
									<div id="donut-chart-states"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-7 d-flex">
					<div class="card flex-fill">
						<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-2">
							<h5>Recently Assigned Leads </h5>
							<div class="d-flex align-items-center">
								<div>
									<a href="{{ route('leads.details', ['box_slug' => request()->route('box_slug')]) }}"
										class="btn btn-sm btn-light px-3">View All </a>
								</div>
							</div>
						</div>
						<div class="card-body p-0">
							<div class="table-responsive">
								<table class="table table-nowrap dashboard-table mb-0">
									<thead>
										<tr>
											<th>Lead Name </th>
											<th>Assigned By </th>
											<th>Assigned At </th>
											<th>Current Status </th>
											<th>Action </th>
										</tr>
									</thead>
									<tbody id="recent-assigned-leads-grid">
										<tr>
											<td colspan="5" class="text-center p-3">
												<span class="spinner-border spinner-border-sm text-primary" role="status"
													aria-hidden="true"></span> Loading...
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		

	</div>
	<!-- /Page Wrapper -->














@endsection

@push('scripts')
	<script>
		$(document).ready(function () {
			const boxSlug = "{{ request()->route('box_slug') ?? 'default' }}";
			let dashboardCharts = {};

			// Global Apex defaults
			Apex = {
				colors: ['#22C55E', '#EF4444', '#FF9F43', '#7367F0', '#00CFDD']
			};

			function fetchDashboardData(year = null) {
				if (!year) year = $('.chart-year-display').first().text().trim();

				// Sync all year displays
				$('.chart-year-display').html('<i class="ti ti-calendar me-1"></i>' + year);

                var startDate = null;
                var endDate = null;
                
                if ($('.bookingrange').length > 0 && $('.bookingrange').data('daterangepicker')) {
                    startDate = $('.bookingrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    endDate = $('.bookingrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
                } else {
                    // Fallback to today if not initialized yet
                    startDate = moment().format('YYYY-MM-DD');
                    endDate = moment().format('YYYY-MM-DD');
                }

				$.ajax({
					url: "{{ box_route('leads.dashboard.data') }}",
					type: "GET",
					data: { start_date: startDate, end_date: endDate, chart_year: year },
					dataType: "json",
					success: function (response) {
						// Helper to update KPI Card Percentage
						function updateKpiCard(containerId, countId, count, change) {
							$('#' + countId).text(count);
							var container = $('#' + containerId);
							var changeVal = parseFloat(change).toFixed(2);
							var colorClass = changeVal >= 0 ? 'text-success' : 'text-danger';
							// Always use arrow-wave-right-up as per theme, just change color
							var iconClass = 'ti ti-arrow-wave-right-up';

							var html = `<span class="${colorClass} fs-12"><i class="${iconClass} me-1"></i>${changeVal > 0 ? '+' : ''}${changeVal}%  </span> from last week`;
							container.html(html);

							var progressBar = container.closest('.card-body').find('.progress-bar');
							// Keeping the original HTML classes for progress bar colors (bg-primary, bg-secondary, etc)

							// Update Progress Bar
							// Set width based on the absolute value of the change percentage, capped at 100%
							var progressWidth = Math.min(Math.abs(changeVal), 100);
							// Ensure a minimum width so it's visible even if change is small (e.g., 5% min)
							if (progressWidth < 5) progressWidth = 5;

							progressBar.css('width', progressWidth + '%');
						}

						// 1. KPI Cards
						updateKpiCard('total-leads-change-container', 'total-leads-count', response.totalLeads, response.totalLeadsChange);
						updateKpiCard('new-leads-change-container', 'new-leads-count', response.newLeads, response.newLeadsChange);
						updateKpiCard('lost-leads-change-container', 'lost-leads-count', response.lostLeads, response.lostLeadsChange);
						updateKpiCard('total-activities-change-container', 'total-activities-count', response.totalActivities, response.totalActivitiesChange);

						// Update New Counter Cards
						$('#unassigned-leads-count').text(response.unassignedLeads);
						$('#trash-leads-count').text(response.trashLeads);
						$('#duplicate-leads-count').text(response.duplicateLeads);

						// Update Task Counter Cards
						$('#task_today-count').text(response.todaysTasks);
						$('#task_open-count').text(response.openTasks);
						$('#task_closed_ontime-count').text(response.closedOnTimeTasks);
						$('#task_closed_late-count').text(response.closedLateTasks);
						$('#task_failed-count').text(response.failedTasks);

						let baseDsrUrl = "{{ route('reports.AllDSR', ['box_slug' => request()->route('box_slug')]) }}";
						let dateParams = `&start_date=${startDate}&end_date=${endDate}`;

						$('#link_closed_ontime').attr('href', baseDsrUrl + '?status=Closed&task_timing=on_time' + dateParams);
						$('#card_closed_ontime').attr('onclick', `window.location.href='${baseDsrUrl}?status=Closed&task_timing=on_time${dateParams}'`);

						$('#link_closed_late').attr('href', baseDsrUrl + '?status=Closed&task_timing=late' + dateParams);
						$('#card_closed_late').attr('onclick', `window.location.href='${baseDsrUrl}?status=Closed&task_timing=late${dateParams}'`);

						$('#link_failed').attr('href', baseDsrUrl + '?status=Closed&task_timing=failed' + dateParams);
						$('#card_failed').attr('onclick', `window.location.href='${baseDsrUrl}?status=Closed&task_timing=failed${dateParams}'`);

						// 1.1 Lead Status Boxes
						var statusBoxesContainer = $('#lead-status-boxes');
						statusBoxesContainer.empty();
						if (response.statusData && response.statusData.length > 0) {
							$.each(response.statusData, function (index, status) {
								var statusHtml = `
																													<div class="col-md-2 col-6 d-flex">
																														<a href="{{ route('leads.details', ['box_slug' => request()->route('box_slug')]) }}?lead_status_id=${status.id}&status_name=${encodeURIComponent(status.name)}" class="card flex-fill mb-2" style="background-color: ${status.color} !important; text-decoration: none; border: none; border-radius: 4px;">
																															<div class="card-body pt-2 pb-2 position-relative">
																																<h3 class="mb-0 text-white">${status.count}</h3>
																																<h6 class="fs-13 fw-medium text-white mb-1 text-truncate">${status.name}</h6>
																																<i class="fa-solid fa-chart-simple position-absolute" style="right: 15px; bottom: 15px; font-size: 20px; opacity: 0.3; color: white;"></i>
																															</div>
																														</a>
																													</div>
																												`;
								statusBoxesContainer.append(statusHtml);
							});
						} else {
							statusBoxesContainer.html('<div class="col-12 text-center p-3">No status data available</div>');
						}

						// 2. New Leads Chart (Heat Chart Replacement)
						if (document.querySelector("#heat_chart")) {
							if (dashboardCharts.heat) dashboardCharts.heat.destroy();
							var optionsHeat = {
								series: [{
									name: "New Leads",
									data: response.newLeadsChartData.data
								}],
								chart: {
									height: 350,
									type: 'area',
									toolbar: { show: false },
									zoom: { enabled: false }
								},
								dataLabels: { enabled: false },
								stroke: { curve: 'smooth', width: 2 },
								xaxis: {
									categories: response.newLeadsChartData.labels,
								},
								tooltip: {
									x: { format: 'dd/MM/yy' },
								},
								colors: ['#FF9F43'],
								fill: {
									type: 'gradient',
									gradient: {
										shadeIntensity: 1,
										opacityFrom: 0.7,
										opacityTo: 0.9,
										stops: [0, 90, 100]
									}
								}
							};
							dashboardCharts.heat = new ApexCharts(document.querySelector("#heat_chart"), optionsHeat);
							dashboardCharts.heat.render();
						}



						// 2.1 Lost Leads By Reason Chart
						if (document.querySelector("#leads_stage")) {
							if (dashboardCharts.lost) dashboardCharts.lost.destroy();
							var lostReasonLabels = [];
							var lostReasonCounts = [];
							if (response.lostReasonData && response.lostReasonData.length > 0) {
								response.lostReasonData.forEach(function (item) {
									lostReasonLabels.push(item.name);
									lostReasonCounts.push(item.count);
								});
							} else {
								// Fallback sample data or empty state
								lostReasonLabels = ['No Data'];
								lostReasonCounts = [0];
							}

							var optionsLost = {
								series: [{
									name: 'Lost Leads',
									data: lostReasonCounts
								}],
								chart: {
									type: 'bar',
									height: 350,
									toolbar: { show: false }
								},
								plotOptions: {
									bar: {
										borderRadius: 4,
										horizontal: true,
									}
								},
								dataLabels: { enabled: false },
								xaxis: {
									categories: lostReasonLabels,
								},
								colors: ['#EA5455'] // Red for Lost
							};
							dashboardCharts.lost = new ApexCharts(document.querySelector("#leads_stage"), optionsLost);
							dashboardCharts.lost.render();
						}

						// 3. Pipeline Stages (Chart & Breakdown)
						var pipelineContainer = $('#pipeline-stages-container');
						pipelineContainer.empty();
						var pipelineLabels = [];
						var pipelineCounts = [];

						if (response.pipelineData && response.pipelineData.length > 0) {
							$.each(response.pipelineData, function (index, item) {
								pipelineLabels.push(item.name);
								pipelineCounts.push(item.count);

								var html = `
																													<div class="col-md col-sm-4 col-6">
																														<div class="border rounded p-2">
																															<p class="mb-1"><i class="ti ti-point-filled text-primary"></i>${item.name}</p>
																															<h6>${item.count}</h6>
																														</div>
																													</div>
																												`;
								pipelineContainer.append(html);
							});
						} else {
							pipelineContainer.html('<div class="col-12 text-center">No data available</div>');
						}

						if (document.querySelector("#pipeline-stages-chart")) {
							if (dashboardCharts.pipeline) dashboardCharts.pipeline.destroy();
							var optionsPipeline = {
								series: [{
									name: 'Leads',
									data: pipelineCounts
								}],
								chart: {
									type: 'bar',
									height: 350,
									toolbar: { show: false }
								},
								plotOptions: {
									bar: {
										borderRadius: 4,
										horizontal: false,
										columnWidth: '55%',
										endingShape: 'rounded'
									}
								},
								dataLabels: { enabled: false },
								stroke: { show: true, width: 2, colors: ['transparent'] },
								xaxis: { categories: pipelineLabels },
								fill: { opacity: 1 },
								colors: ['#7367F0']
							};
							dashboardCharts.pipeline = new ApexCharts(document.querySelector("#pipeline-stages-chart"), optionsPipeline);
							dashboardCharts.pipeline.render();
						}

						// 3. Leads by Source (Chart & List)
						var sourceList = $('#leads-source-list');
						sourceList.empty();
						sourceList.append('<h6 class="mb-3">Sources</h6>');
						var sourceLabels = [];
						var sourceCounts = [];
						var colors = ['#28C76F', '#FF9F43', '#EA5455', '#7367F0', '#00CFDD', '#4B4B46', '#1E90FF', '#FF1493', '#FFD700', '#8A2BE2'];

						if (response.sourceData && response.sourceData.length > 0) {
							$.each(response.sourceData, function (index, item) {
								sourceLabels.push(item.name);
								sourceCounts.push(item.count);
								var color = colors[index % colors.length];

								var html = `
																													<div class="d-flex align-items-center justify-content-between mb-2">
																														<p class="f-13 mb-0"><i class="ti ti-circle-filled me-1" style="color: ${color}"></i>${item.name}</p>
																														<div class="text-end">
																															<p class="f-13 fw-medium text-gray-9 mb-0">${item.count}</p>
																															<span class="fs-12 text-muted">$${parseFloat(item.value).toFixed(2)}</span>
																														</div>
																													</div>
																												`;
								sourceList.append(html);
							});
						} else {
							sourceList.append('<p class="text-center">No data available</p>');
							sourceLabels = ['No Data'];
							sourceCounts = [1];
						}

						if (document.querySelector("#donut-chart-2")) {
							if (dashboardCharts.source) dashboardCharts.source.destroy();
							var optionsSource = {
								series: sourceCounts,
								chart: {
									width: 380,
									type: 'donut',
								},
								labels: sourceLabels,
								responsive: [{
									breakpoint: 480,
									options: {
										chart: { width: 200 },
										legend: { position: 'bottom' }
									}
								}],
								colors: colors
							};
							dashboardCharts.source = new ApexCharts(document.querySelector("#donut-chart-2"), optionsSource);
							dashboardCharts.source.render();
						}

						// 4. Leads by Company
						var companiesContainer = $('#leads-by-companies');
						companiesContainer.empty();
						if (response.companiesData && response.companiesData.length > 0) {
							$.each(response.companiesData, function (index, company) {
								var initials = company.company_name ? company.company_name.trim().charAt(0).toUpperCase() : '?';
								var html = `
																													<div class="border border-dashed bg-transparent-light rounded p-2 mb-2">
																														<div class="d-flex align-items-center justify-content-between">
																															<div class="d-flex align-items-center">
																																<div class="avatar avatar-md rounded-circle bg-gray-100 flex-shrink-0 me-2 d-flex align-items-center justify-content-center border">
																																	<span class="fs-14 fw-bold text-dark">${initials}</span>
																																</div>
																																<div>
																																	<h6 class="fw-medium mb-1"><a href="#">${company.company_name}</a></h6>
																																	<p class="text-truncate">Value : $${parseFloat(company.value).toFixed(2)}</p>
																																</div>
																															</div>
																														</div>
																													</div>
																												`;
								companiesContainer.append(html);
							});
						} else {
							companiesContainer.html('<div class="text-center p-3">No company data found</div>');
						}

						// 5. Recent Activities
						var activitiesContainer = $('#recent-activities-list');
						activitiesContainer.empty();
						if (response.recentActivities && response.recentActivities.length > 0) {
							$.each(response.recentActivities, function (index, activity) {
								var iconClass = 'ti-activity';
								var bgClass = 'bg-primary';
								if (activity.type === 'call') { iconClass = 'ti-phone'; bgClass = 'bg-success'; }
								else if (activity.type === 'email') { iconClass = 'ti-mail'; bgClass = 'bg-warning'; }
								else if (activity.type === 'meeting') { iconClass = 'ti-calendar'; bgClass = 'bg-info'; }
								else if (activity.type === 'note') { iconClass = 'ti-notebook'; bgClass = 'bg-secondary'; }

								var description = activity.description || 'No Query';
								var words = description.split(/\s+/);
								var wordLimit = 20; // Maximum three lines approx
								var descHtml = '';

								if (words.length > wordLimit) {
									var shortText = words.slice(0, wordLimit).join(' ');
									descHtml = `
																															<span class="desc-short">${shortText}...</span>
																															<span class="desc-full d-none">${description}</span>
																															<a href="javascript:void(0);" class="text-primary ms-1 show-toggle" data-state="short">Show More</a>
																														`;
								} else {
									descHtml = description;
								}

								var html = `
																													<div class="d-flex align-items-start">
																														<div class="avatar avatar-md avatar-rounded ${bgClass} flex-shrink-0">
																															<i class="ti ${iconClass} fs-20"></i>
																														</div>
																														<div class="flex-fill ps-3 pb-4 timeline-flow">
																															<p class="fw-medium text-gray-9 mb-1">${descHtml}</p>
																															<span>${moment(activity.created_at).fromNow()}</span>
																														</div>
																													</div>
																												`;
								activitiesContainer.append(html);
							});
						} else {
							activitiesContainer.html('<div class="text-center p-3">No recent activities</div>');
						}

						// 6. Recent Leads Table
						var leadsContainer = $('#recent-leads-grid');
						leadsContainer.empty();
						if (response.recentLeads && response.recentLeads.length > 0) {
							$.each(response.recentLeads, function (index, lead) {
								var leadName = (lead.name || '') + (lead.last_name ? ' ' + lead.last_name : '');
								var companyName = lead.company_name || 'N/A';
								var createdDate = lead.created_at ? moment(lead.created_at).format('DD MMM YYYY') : '-';
								var owner = lead.assigned_to ? (lead.assigned_to.name || 'Unknown') : 'Unassigned';
								var createdFrom = lead.created_from || 'Direct';

								// Get Initials for Avatar (Prefer Company Name first letter as requested)
								var initials = '';
								if (companyName && companyName !== 'N/A') {
									initials = companyName.trim().charAt(0).toUpperCase();
								} else {
									initials = leadName.trim().charAt(0).toUpperCase() || '?';
								}

								// Check if lead has an image (future-proofing)
								var leadImage = null; // Removed broken static asset paths

								var html = `
																													<tr>
																														<td>
																															<div class="d-flex align-items-center">
																																<div class="avatar avatar-sm rounded-circle bg-primary-transparent me-2 d-flex align-items-center justify-content-center">
																																	${leadImage ? `<img src="${leadImage}" class="img-fluid rounded-circle" alt="img">` : `<span class="fs-12 fw-bold text-primary">${initials}</span>`}
																																</div>
																																<h6 class="fw-medium mb-0"><a href="/${boxSlug}/leads-details?lead_id=${lead.id}">${leadName}</a></h6>
																															</div>
																														</td>
																														<td>${companyName}</td>
																														<td>${createdDate}</td>
																														<td>
																															<span class="badge badge-soft-info">${createdFrom}</span>
																														</td>
																														<td>
																															<div class="d-flex align-items-center">
																																<i class="ti ti-user-circle me-1 text-muted"></i>
																																<span>${owner}</span>
																															</div>
																														</td>
																													</tr>
																												`;
								leadsContainer.append(html);
							});
						} else {
							leadsContainer.html('<tr><td colspan="5" class="text-center">No recent leads found</td></tr>');
						}

						// 6.1 Recent Assigned Leads Table
						var assignedLeadsContainer = $('#recent-assigned-leads-grid');
						assignedLeadsContainer.empty();
						if (response.recentAssignedLeads && response.recentAssignedLeads.length > 0) {
							$.each(response.recentAssignedLeads, function (index, history) {
								var lead = history.lead || {};
								var leadName = (lead.name || '') + (lead.last_name ? ' ' + lead.last_name : '');
								var assignedBy = history.assigned_by ? (history.assigned_by.name || 'System') : 'System';
								var assignedAt = history.created_at ? moment(history.created_at).format('DD MMM YYYY, hh:mm A') : '-';
								var currentStatus = lead.lead_status ? lead.lead_status.name : 'N/A';

								var initials = leadName.trim().charAt(0).toUpperCase() || '?';

								var html = `
																													<tr>
																														<td>
																															<div class="d-flex align-items-center">
																																<div class="avatar avatar-sm rounded-circle bg-success-transparent me-2 d-flex align-items-center justify-content-center">
																																	<span class="fs-12 fw-bold text-success">${initials}</span>
																																</div>
																																<h6 class="fw-medium mb-0"><a href="/${boxSlug}/leads-details?lead_id=${lead.id}">${leadName}</a></h6>
																															</div>
																														</td>
																														<td>
																															 <div class="d-flex align-items-center">
																																<i class="ti ti-user-share me-1 text-muted"></i>
																																<span>${assignedBy}</span>
																															</div>
																														</td>
																														<td>${assignedAt}</td>
																														<td>
																															<span class="badge badge-soft-primary">${currentStatus}</span>
																														</td>
																														<td>
																															<a href="/${boxSlug}/leads-details?lead_id=${lead.id}" class="btn btn-sm btn-primary">Details</a>
																														</td>
																													</tr>
																												`;
								assignedLeadsContainer.append(html);
							});
						} else {
							assignedLeadsContainer.html('<tr><td colspan="5" class="text-center">No recently assigned leads found</td></tr>');
						}

						// 7. Recent Follow Up List
						var followUpContainer = $('#recent-follow-up-list');
						followUpContainer.empty();
						if (response.followUpData && response.followUpData.length > 0) {
							$.each(response.followUpData, function (index, activity) {
								var leadName = activity.lead ? (activity.lead.name + (activity.lead.last_name ? ' ' + activity.lead.last_name : '')) : 'Unknown Lead';
								var designation = activity.lead && activity.lead.designation ? activity.lead.designation : 'N/A';
								var initials = leadName.trim().charAt(0).toUpperCase() || '?';
								var typeIcon = 'ti-phone'; // Default
								if (activity.type == 'email') typeIcon = 'ti-mail';
								else if (activity.type == 'meeting') typeIcon = 'ti-calendar';

								var html = `
																													 <div class="d-flex align-items-center justify-content-between mb-4">
																														 <div class="d-flex align-items-center">
																															 <div class="avatar avatar-md rounded-circle bg-light flex-shrink-0 d-flex align-items-center justify-content-center border">
																																 <span class="fs-14 fw-bold text-dark">${initials}</span>
																															 </div>
																															 <div class="ms-2">
																																 <h6 class="fs-14 fw-medium text-truncate mb-1"><a href="#">${leadName}</a></h6>
																																 <p class="fs-13">${designation}</p>
																															 </div>
																														 </div>
																														 <div class="d-flex align-items-center">
																															 <a href="#" class="btn btn-light btn-icon btn-sm"><i class="ti ${typeIcon} fs-16"></i></a>
																														 </div>
																													 </div>
																												 `;
								followUpContainer.append(html);
							});
						} else {
							followUpContainer.html('<div class="text-center p-3">No pending follow-ups</div>');
						}

						// 8. Top Countries (List & Chart)
						var countriesList = $('#top-countries-list');
						countriesList.empty();
						var countryLabels = [];
						var countryCounts = [];
						// Use same colors array as Source
						// var colors is already defined above

						if (response.topCountriesData && response.topCountriesData.length > 0) {
							var topHexColors = [
								"#EF4444", // Red
								"#F97316", // Orange
								"#3B82F6", // Blue
								"#10B981", // Emerald
								"#EAB308", // Yellow
								"#8B5CF6", // Violet
								"#14B8A6", // Teal
								"#F43F5E", // Rose
								"#6366F1", // Indigo
								"#D946EF", // Fuchsia
								"#84CC16", // Lime
								"#0EA5E9", // Light Blue
								"#A855F7", // Purple
								"#EC4899", // Pink
								"#06B6D4", // Cyan
								"#F59E0B", // Amber
								"#22C55E"  // Green
							];
							$.each(response.topCountriesData, function (index, country) {
								countryLabels.push(country.name);
								countryCounts.push(country.count);
								var hexColor = topHexColors[index % topHexColors.length];

								var html = `
																													 <div class="d-flex align-items-center mb-4">
																														 <span class="me-2"><i class="ti ti-point-filled fs-16" style="color: ${hexColor}"></i></span>
																														 <div class="avatar rounded-circle flex-shrink-0 border bg-light d-flex align-items-center justify-content-center">
																															 <i class="ti ti-world text-muted font-20"></i>
																														 </div>
																														 <div class="ms-2">
																															 <h6 class="fw-medium text-truncate mb-1"><a href="#">${country.name}</a></h6>
																															 <span class="fs-13 text-truncate">Leads : ${country.count} </span>
																														 </div>
																													 </div>
																												 `;
								countriesList.append(html);
							});

							if (document.querySelector("#donut-chart-3")) {
								if (dashboardCharts.country) dashboardCharts.country.destroy();
								var optionsCountry = {
									series: countryCounts,
									chart: {
										width: 250,
										type: 'donut',
									},
									labels: countryLabels,
									responsive: [{
										breakpoint: 480,
										options: {
											chart: { width: 200 },
											legend: { position: 'bottom' }
										}
									}],
									colors: topHexColors.slice(0, countryCounts.length),
									legend: { position: 'bottom' }
								};
								dashboardCharts.country = new ApexCharts(document.querySelector("#donut-chart-3"), optionsCountry);
								dashboardCharts.country.render();
							}

						} else {
							countriesList.html('<div class="text-center p-3">No country data</div>');
							if (document.querySelector("#donut-chart-3")) {
								document.querySelector("#donut-chart-3").innerHTML = '';
							}
						}

						// 8.1 Top States (List & Chart)
						var statesList = $('#top-states-list');
						statesList.empty();
						var stateLabels = [];
						var stateCounts = [];

						if (response.topStatesData && response.topStatesData.length > 0) {
							$.each(response.topStatesData, function (index, state) {
								stateLabels.push(state.name);
								stateCounts.push(state.count);
								var hexColor = topHexColors[index % topHexColors.length];

								var html = `
																													 <div class="d-flex align-items-center mb-4">
																														 <span class="me-2"><i class="ti ti-point-filled fs-16" style="color: ${hexColor}"></i></span>
																														 <div class="avatar rounded-circle flex-shrink-0 border bg-light d-flex align-items-center justify-content-center">
																															 <i class="ti ti-map-pin text-muted font-20"></i>
																														 </div>
																														 <div class="ms-2">
																															 <h6 class="fw-medium text-truncate mb-1"><a href="#">${state.name}</a></h6>
																															 <span class="fs-13 text-truncate">Leads : ${state.count} </span>
																														 </div>
																													 </div>
																												 `;
								statesList.append(html);
							});

							if (document.querySelector("#donut-chart-states")) {
								if (dashboardCharts.state) dashboardCharts.state.destroy();
								var optionsState = {
									series: stateCounts,
									chart: {
										width: 250,
										type: 'donut',
									},
									labels: stateLabels,
									responsive: [{
										breakpoint: 480,
										options: {
											chart: { width: 200 },
											legend: { position: 'bottom' }
										}
									}],
									colors: topHexColors.slice(0, stateCounts.length),
									legend: { position: 'bottom' }
								};
								dashboardCharts.state = new ApexCharts(document.querySelector("#donut-chart-states"), optionsState);
								dashboardCharts.state.render();
							}

						} else {
							statesList.html('<div class="text-center p-3">No state data</div>');
							if (document.querySelector("#donut-chart-states")) {
								document.querySelector("#donut-chart-states").innerHTML = '';
							}
						}

						// 9. Leads By Contact Status Chart (Column - Multi-series)
						if (document.querySelector("#contacted-status-chart") && response.contactStatusData) {
							console.log('this is requested data', response.contactStatusData.colors);
							const colors = response.contactStatusData.colors;
							if (dashboardCharts.contact) dashboardCharts.contact.destroy();
							var optionsContact = {
								chart: {
									height: 350,
									type: 'bar',
									toolbar: {
										show: false,
									},
									animations: {
										enabled: true,
										easing: 'easeinout',
										speed: 800,
									}
								},
								plotOptions: {
									bar: {
										horizontal: false,
										columnWidth: '55%',
										endingShape: 'rounded',
										borderRadius: 4,
										distributed: false, // For multi-series we want consistent colors per series
									},
								},
								dataLabels: {
									enabled: false
								},
								stroke: {
									show: true,
									width: 2,
									colors: ['transparent']
								},
								series: response.contactStatusData.series,
								xaxis: {
									categories: response.contactStatusData.labels,
									axisBorder: {
										show: false
									},
								},
								yaxis: {
									title: {
										text: 'Number of Leads'
									}
								},
								fill: {
									opacity: 1
								},
								tooltip: {
									y: {
										formatter: function (val) {
											return val + " Leads"
										}
									}
								},
								colors: ['#22C55E', '#EF4444'], // Static Green for Contacted, Red for Not Contacted
								grid: {
									borderColor: '#f1f1f1',
								},
								legend: {
									position: 'top',
									horizontalAlign: 'right',
								}
							};
							dashboardCharts.contact = new ApexCharts(document.querySelector("#contacted-status-chart"), optionsContact);
							dashboardCharts.contact.render();
						}

						// 10. Leads By Sources Chart (Stacked Bar - Multi-series)
						if (document.querySelector("#s-col-stacked") && response.sourceStatusData) {
							if (dashboardCharts.sourcesBar) dashboardCharts.sourcesBar.destroy();

							var optionsSources = {
								series: response.sourceStatusData.series,
								chart: {
									height: 350,
									type: 'bar',
									stacked: true,
									toolbar: {
										show: false,
									},
									animations: {
										enabled: true,
										easing: 'easeinout',
										speed: 800,
									}
								},
								plotOptions: {
									bar: {
										horizontal: false,
										columnWidth: '55%',
										borderRadius: 4,
									},
								},
								dataLabels: {
									enabled: true,
									formatter: function (val) {
										return val > 0 ? val : '';
									},
									style: {
										fontSize: '10px',
										colors: ["#fff"]
									}
								},
								xaxis: {
									categories: response.sourceStatusData.labels,
									axisBorder: {
										show: false
									},
								},
								yaxis: {
									title: {
										text: 'Number of Leads'
									}
								},
								fill: {
									opacity: 1
								},
								colors: response.sourceStatusData.colors,
								grid: {
									borderColor: '#f1f1f1',
								},
								legend: {
									position: 'top',
									horizontalAlign: 'right',
								},
								tooltip: {
									y: {
										formatter: function (val) {
											return val + " Leads"
										}
									}
								}
							};
							dashboardCharts.sourcesBar = new ApexCharts(document.querySelector("#s-col-stacked"), optionsSources);
							dashboardCharts.sourcesBar.render();
						}

						// 11. Assigned Leads By User Monthly Chart (Stacked Bar - Multi-series)
						if (document.querySelector("#assigned-leads-user-chart") && response.assignedByUserMonthlyData) {
							if (dashboardCharts.assignedUserBar) dashboardCharts.assignedUserBar.destroy();

							var optionsAssignedUser = {
								series: response.assignedByUserMonthlyData.series,
								chart: {
									height: 350,
									type: 'bar',
									stacked: true,
									toolbar: {
										show: false,
									},
									animations: {
										enabled: true,
										easing: 'easeinout',
										speed: 800,
									}
								},
								plotOptions: {
									bar: {
										horizontal: false,
										columnWidth: '55%',
										borderRadius: 4,
									},
								},
								dataLabels: {
									enabled: true,
									formatter: function (val) {
										return val > 0 ? val : '';
									},
									style: {
										fontSize: '10px',
										colors: ["#fff"]
									}
								},
								xaxis: {
									categories: response.assignedByUserMonthlyData.labels,
									axisBorder: {
										show: false
									},
								},
								yaxis: {
									title: {
										text: 'Number of Leads'
									}
								},
								fill: {
									opacity: 1
								},
								colors: response.assignedByUserMonthlyData.colors,
								grid: {
									borderColor: '#f1f1f1',
								},
								legend: {
									position: 'top',
									horizontalAlign: 'right',
								},
								tooltip: {
									y: {
										formatter: function (val) {
											return val + " Leads"
										}
									}
								}
							};
							dashboardCharts.assignedUserBar = new ApexCharts(document.querySelector("#assigned-leads-user-chart"), optionsAssignedUser);
							dashboardCharts.assignedUserBar.render();
						}

					}
				});
			}

			// Initial Load with DateRangePicker
			if ($('.bookingrange').length > 0) {
				var start = moment();
				var end = moment();

				function cb(start, end) {
					$('.bookingrange').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
				}

				$('.bookingrange').daterangepicker({
					startDate: start,
					endDate: end,
					ranges: {
						'Today': [moment(), moment()],
						'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
						'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
						'Last 7 Days': [moment().subtract(6, 'days'), moment()],
						'Last 30 Days': [moment().subtract(29, 'days'), moment()],
						'This Month': [moment().startOf('month'), moment().endOf('month')],
						'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
						'This Year': [moment().startOf('year'), moment().endOf('year')]
					}
				}, cb);

				cb(start, end);

				$('.bookingrange').on('apply.daterangepicker', function(ev, picker) {
					fetchDashboardData();
				});
				
				// Call immediately on page load
				fetchDashboardData();
			} else {
				fetchDashboardData();
			}

			// Handle Year Select for Charts
			$(document).on('click', '.chart-year-selector .dropdown-item', function () {
				var year = $(this).text().trim();
				fetchDashboardData(year);
			});

			// Handle Show More/Hide for Activities
			$(document).on('click', '.show-toggle', function (e) {
				e.preventDefault();
				e.stopPropagation();
				var $this = $(this);
				var $parent = $this.closest('p');
				var isShort = $this.attr('data-state') === 'short';

				if (isShort) {
					$parent.find('.desc-short').addClass('d-none');
					$parent.find('.desc-full').removeClass('d-none');
					$this.text('Show Less').attr('data-state', 'full');
				} else {
					$parent.find('.desc-short').removeClass('d-none');
					$parent.find('.desc-full').addClass('d-none');
					$this.text('Show More').attr('data-state', 'short');
				}
			});

			// Fetch Dashboard Notifications
			fetchDashboardNotifications();
		});

		function fetchDashboardNotifications() {
			const boxSlug = "{{ request()->route('box_slug') ?? 'default' }}";
			const url = "{{ route('notifications.get', ['box_slug' => ':box_slug']) }}".replace(':box_slug', boxSlug);

			$.ajax({
				url: url,
				method: 'GET',
				success: function (response) {
					const container = $('#dashboard-notifications-container');
					container.empty();

					if (response.success && response.data.length > 0) {
						const notifications = response.data.slice(0, 5);

						notifications.forEach(notification => {
							const data = notification.data;
							const created = new Date(notification.created_at).toLocaleString();
							const detailsUrl = `/${boxSlug}/leads-details?lead_id=${data.lead_id}`;

							const item = `
																													<div class="d-flex align-items-start mb-4">
																														<a href="${detailsUrl}" class="avatar flex-shrink-0 bg-primary-transparent">
																															<i class="ti ${data.icon || 'ti-bell'} fs-18"></i>
																														</a>
																														<div class="ms-2">
																															<h6 class="fs-14 fw-medium text-truncate mb-1">
																																<a href="${detailsUrl}" class="text-dark">${data.title}</a>
																															</h6>
																															<p class="fs-13 mb-1 text-muted">${data.message}</p>
																															<p class="fs-12 mb-0 text-muted">${created}</p>
																														</div>
																													</div>
																												`;
							container.append(item);
						});
					} else {
						container.html('<div class="text-center p-3">No new notifications</div>');
					}
				},
				error: function () {
					$('#dashboard-notifications-container').html('<div class="text-center p-3 text-danger">Failed to load notifications</div>');
				}
			});
		}
	</script>
@endpush