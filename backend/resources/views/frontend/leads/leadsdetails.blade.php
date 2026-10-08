@extends('layouts.main')
@section('title', isset($lead) ? $lead->name . ' - Lead Details' : 'Lead Details')
@section('description', isset($lead) ? 'View details for lead ' . $lead->name : 'View and manage lead details.')
@section('content')
@push('css')
<style>
    /* Pipeline Status CSS */
    .pipeline-list {
        margin: 20px 0;
        padding: 0;
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 10px;
    }
    .pipeline-list ul {
        display: flex;
        align-items: center;
        list-style: none;
        padding: 0;
        margin: 0;
        gap: 0;
    }
    .pipeline-list li {
        position: relative;
        display: flex;
        align-items: center;
        margin: 0;
        padding: 0;
    }
    .pipeline-list li a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 25px 8px 30px;
        font-size: 13px;
        font-weight: 500;
        color: #333;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
        min-width: 140px;
        text-align: center;
        clip-path: polygon(calc(100% - 15px) 0%, 100% 50%, calc(100% - 15px) 100%, 0% 100%, 15px 50%, 0% 0%);
        margin-left: -12px;
        border: none !important;
    }
    .pipeline-list li:first-child a {
        margin-left: 0;
        clip-path: polygon(calc(100% - 15px) 0%, 100% 50%, calc(100% - 15px) 100%, 0% 100%, 0% 50%, 0% 0%);
        padding-left: 15px;
    }
    .pipeline-list li:last-child a {
        clip-path: polygon(100% 0%, 100% 50%, 100% 100%, 0% 100%, 15px 50%, 0% 0%);
    }

    .pipeline-list li::after {
        display: none !important;
    }
    
    /* Status Colors */
    .pipeline-list li a.bg-pending { background-color: #fff9e6; color: #ff9800; border-color: #ffe082; }
    .pipeline-list li a.bg-info { background-color: #e3f2fd; color: #2196f3; border-color: #90caf9; }
    .pipeline-list li a.bg-success { background-color: #e8f5e9; color: #4caf50; border-color: #a5d6a7; }
    .pipeline-list li a.bg-danger { background-color: #ffebee; color: #f44336; border-color: #ef9a9a; }
    .pipeline-list li a.bg-primary { background-color: #e8eaf6; color: #3f51b5; border-color: #9fa8da; }
    .pipeline-list li a.bg-light { background-color: #f5f5f5; color: #757575; border-color: #bdbdbd; }

    /* Visited Status (Completed) */
    .pipeline-list li.visited a {
        background-color: #f1f8e9 !important;
        border-color: #c5e1a5 !important;
        color: #689f38 !important;
    }
    .pipeline-list li.visited::after {
        background-color: #8bc34a;
    }

    /* Active Status (Current) */
    .pipeline-list li.active a {
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        transform: translateY(-2px);
        font-weight: 700 !important;
        border-width: 2px !important;
    }

    /* Sticky Sidebar for Contact Details */
    .lead-sidebar-sticky {
        position: sticky;
        top: 80px;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
    }
    
    /* Scrollbar styling for the sticky sidebar to make it look clean */
    .lead-sidebar-sticky::-webkit-scrollbar {
        width: 6px;
    }
    .lead-sidebar-sticky::-webkit-scrollbar-track {
        background: #f1f1f1; 
        border-radius: 4px;
    }
    .lead-sidebar-sticky::-webkit-scrollbar-thumb {
        background: #c1c1c1; 
        border-radius: 4px;
    }
    .lead-sidebar-sticky::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8; 
    }
</style>
@endpush
		<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-sm-4">
									<h4 class="page-title">Leads Overview </h4>
								</div>
								<div class="col-sm-8 text-sm-end">
									<div class="head-icons">
										<a href="leads-details.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
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

						<!-- Leads User -->
						<div class="contact-head">
							<div class="row align-items-center">
								<div class="col-sm-6">
									<ul class="contact-breadcrumb">
										<li><a href="{{ route('leads.details', ['box_slug' => request()->route('box_slug')]) }}"><i class="ti ti-arrow-narrow-left"></i>Leads</a></li>
										<li id="lead-breadcrumb-name">Loading...</li>
									</ul>
								</div>
								<div class="col-sm-6 text-sm-end">
									<div class="contact-pagination">
										<p id="lead-pagination">{{ isset($paginationText) ? $paginationText : '1 of 1' }}</p>
										<ul>
											<li>
												@if(isset($prevLeadId))
													<a href="{{ route('LeadsDetailsPage', ['box_slug' => request()->route('box_slug')]) }}?lead_id={{ $prevLeadId }}{{ isset($bulkUploadId) ? '&bulk_upload_id='.$bulkUploadId : '' }}" id="prev-lead" title="Previous Lead"><i class="ti ti-chevron-left"></i></a>
												@else
													<a href="javascript:void(0);" id="prev-lead" style="opacity:0.5; cursor:not-allowed;" title="No Previous Lead"><i class="ti ti-chevron-left"></i></a>
												@endif
											</li>
											<li>
												@if(isset($nextLeadId))
													<a href="{{ route('LeadsDetailsPage', ['box_slug' => request()->route('box_slug')]) }}?lead_id={{ $nextLeadId }}{{ isset($bulkUploadId) ? '&bulk_upload_id='.$bulkUploadId : '' }}" id="next-lead" title="Next Lead"><i class="ti ti-chevron-right"></i></a>
												@else
													<a href="javascript:void(0);" id="next-lead" style="opacity:0.5; cursor:not-allowed;" title="No Next Lead"><i class="ti ti-chevron-right"></i></a>
												@endif
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
										<div class="avatar avatar-xxl me-3 flex-shrink-0 border p-2" id="lead-avatar" style="display: flex; align-items: center; justify-content: center;">
											<h6 class="text-white fw-medium" id="lead-avatar-initials" style="margin: 0; font-weight: bold;">--</h6>
										</div>
										<div>
											<h5 class="mb-1" id="lead-name-header">Loading... <span class="star-icon"><i class="fa-solid fa-star"></i></span></h5>
											<p class="mb-1" id="lead-company"><i class="ti ti-building me-1"></i>--</p>
											<p class="mb-0" id="lead-location"><i class="ti ti-map-pin-pin me-1"></i>--</p>
										</div>
									</div>




									<div class="contacts-action">
									    <a style="padding:0.18rem 0.5rem;" href="javascript:void(0);" class="btn btn-primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-square-rounded-plus me-2"></i>Add New Activity</a>
										<span class="badge badge-light"><i class="ti ti-lock"></i>Private</span>

										<div class="dropdown mb-2">
											<a href="#" class="bg-success text-white py-1 px-2" data-bs-toggle="dropdown" aria-expanded="false">
												Closed<i class="ti ti-chevron-down ms-2"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-right">
												<a class="dropdown-item" href="javascript:void(0);"><span>Closed</span></a>
												<a class="dropdown-item" href="javascript:void(0);"><span>Opened</span></a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- /Leads User -->

					</div>

					<!-- Leads Sidebar -->
					<div class="col-xl-3">
						<div class="card lead-sidebar-sticky">
							<div class="card-body p-3">
							<div class="d-flex align-items-center justify-content-between flex-wrap">
								<h6 class="mb-3 fw-semibold">Information</h6>
								<a href="javascript:void(0);" class="link-primary mb-3 fw-medium" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_quick_edit"><i class="ti ti-edit me-1"></i>Quick Edit</a>
							</div>
								<ul id="lead-info-list">
									<li class="row mb-3"><span class="col-6">Date Created</span><span class="col-6">--</span></li>
									<li class="row mb-3"><span class="col-6">Value</span><span class="col-6">--</span></li>
									<li class="row mb-3"><span class="col-6">Total Received</span><span class="col-6">--</span></li>
									<li class="row mb-3"><span class="col-6">Last Received</span><span class="col-6">--</span></li>
									<li class="row mb-3"><span class="col-6">Lead Type</span><span class="col-6">--</span></li>
									<li class="row mb-3"><span class="col-6">Source</span><span class="col-6">--</span></li>
									<li class="row mb-3"><span class="col-6">Status</span><span class="col-6">--</span></li>
									<li class="row mb-3"><span class="col-6">Lead Sub Status</span><span class="col-6">--</span></li>
								</ul>
								<div id="lead-assignees-list">
									@php
										// Show all unique users who have been involved in assignments for this lead
										$historyAssignees = $assignmentHistory->unique('user_id')->pluck('user_id')->toArray();
										
										// Also include current assignees from pivot if any are missing from history
										$currentAssigneeIds = $lead->assignees->pluck('id')->toArray();
										$allUniqueIds = array_unique(array_merge($historyAssignees, $currentAssigneeIds));
										
										$finalAssignees = \App\Models\User::whereIn('id', $allUniqueIds)->get();
									@endphp
									@forelse($finalAssignees as $member)
										@php
											$memberName = $member->name ?? 'Unknown';
											$memberBoxUser = $boxUsers->get($member->id);
											$initials = '';
											$words = explode(' ', $memberName);
											foreach($words as $w) {
												if(!empty($w)) $initials .= strtoupper(substr($w, 0, 1));
											}
											$initials = substr($initials, 0, 2);
											
											$colors = ['#7367F0', '#28C76F', '#EA5455', '#FF9F43', '#00CFDD', '#4B4B46', '#1E90FF', '#FF1493', '#FFD700', '#8A2BE2'];
											$hash = array_sum(array_map('ord', str_split($memberName)));
											$bgColor = $colors[$hash % count($colors)];
										@endphp
										<h6 class="mb-3 fw-semibold">Assigned To - {{ $memberBoxUser && $memberBoxUser->departmentRelation ? $memberBoxUser->departmentRelation->name : '--' }}</h6>
										<div class="d-flex align-items-center mb-3">
											<div class="avatar avatar-md me-2" style="display: flex; align-items: center; justify-content: center; background-color: {{ $bgColor }}; border-radius: 50%; min-width: 45px; height: 45px;">
												@if($memberBoxUser && $memberBoxUser->profile_image_path)
													<img src="{{ asset($memberBoxUser->profile_image_path) }}" alt="Image" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
												@else
													<span class="text-white fw-bold">{{ $initials }}</span>
												@endif
											</div>
											<div>
												<p class="mb-0 fs-16 fw-medium text-dark">{{ $memberName }}</p>
												@if($memberBoxUser && $memberBoxUser->designationRelation)
													<p class="mb-0 text-muted fs-12">{{ $memberBoxUser->designationRelation->name }}</p>
												@endif
											</div>
										</div>
									@empty
										<h6 class="mb-3 fw-semibold">Assigned To</h6>
										<p class="text-muted">Unassigned</p>
									@endforelse
								</div>
								<hr>
								<h6 class="mb-3 fw-semibold">Tags</h6>
								<div id="lead-tags">
									<span class="text-muted">No tags</span>
								</div>
								<hr>
								<h6 class="mb-3 fw-semibold">Projects</h6>
								<a href="javascript:void(0);" class="badge bg-light-300 text-default me-2 mb-2">Devops
									Design</a>
								<a href="javascript:void(0);" class="badge bg-light-300 text-default mb-2">Margrate
									Design</a>
								<hr>
								<h6 class="mb-3 fw-semibold">Priority</h6>
								<div class="priority-info">
									<div class="dropdown">
										<a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><span><i class="ti ti-square-rounded-filled me-1 text-danger circle"></i>High</span><i class="ti ti-chevron-down me-1"></i></a>
										<div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item" href="javascript:void(0);">
												<span><i class="ti ti-square-rounded-filled me-1 text-danger circle"></i>High</span>
											</a>
											<a class="dropdown-item" href="javascript:void(0);">
												<span><i class="ti ti-square-rounded-filled me-1 text-success circle"></i>Low</span>
											</a>
										</div>
									</div>
								</div>
								<hr>
								<div class="d-flex align-items-center justify-content-between flex-wrap">
									<h6 class="mb-3 fw-semibold">Contacts</h6>
									<a href="javascript:void(0);" class="link-purple mb-3 fw-medium" data-bs-toggle="modal" data-bs-target="#add_contact"><i class="ti ti-circle-plus me-1"></i>Add New</a>
								</div>
								<div class="d-flex align-items-center mb-0">
									<div class="avatar avatar-md me-2">
										<!-- <img src="assets/img/profiles/avatar-01.jpg" alt="Image"> -->
									</div>
									<p>Jessica Louise</p>
								</div>
								<hr>
								<h6 class="mb-3 fw-semibold">Contact Information</h6>
								<ul id="lead-contact-info">
									<li class="row mb-3"><span class="col-6">Email</span><span class="col-6">--</span></li>
									<li class="row mb-3"><span class="col-6">Phone</span><span class="col-6">--</span></li>
									<li class="row mb-3"><span class="col-6">Phone Type</span><span class="col-6">--</span></li>
									<li class="row mb-3"><span class="col-6">Industry</span><span class="col-6">--</span></li>
									<li class="row mb-3"><span class="col-6">Country</span><span class="col-6">--</span></li>
									<li class="row mb-3"><span class="col-6">State</span><span class="col-6">--</span></li>
									<li class="row mb-3"><span class="col-6">City</span><span class="col-6">--</span></li>
								</ul>
								<hr>
								<h6 class="mb-3 fw-semibold">Working Locations</h6>
								<ul id="lead-working-locations">
									<li class="text-muted">No working locations</li>
								</ul>
								<div id="custom-fields-section" style="display: none;">
									<hr>
									<h6 class="mb-3 fw-semibold">Custom Fields</h6>
									<ul id="lead-custom-fields">
										<li class="text-muted">No custom fields</li>
									</ul>
								</div>
								<hr>
								<ul>
									<li class="row mb-3"><span class="col-6">Last Modified</span><span class="col-6" id="lead-last-modified">--</span></li>
									<li class="row mb-0"><span class="col-6">Modified By</span>
										<span class="col-6" id="lead-modified-by">
											<div class="d-flex align-items-center">
												<div class="avatar avatar-xs me-1" id="lead-modified-avatar" style="display: flex; align-items: center; justify-content: center;">
													<img src="" alt="Image" id="lead-modified-image" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 50%;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
													<span id="lead-modified-initials" class="text-white fw-medium" style="display: none; align-items: center; justify-content: center; width: 100%; height: 100%; border-radius: 50%; font-weight: bold;">--</span>
												</div>
												<span id="lead-modified-name">--</span>
											</div>
										</span>
									</li>
								</ul>
								<hr>
								<div class="row g-2 mt-4">
									<div class="col-6">
										<button type="button" class="btn w-100 h-100 py-2 px-1 fw-bold text-white" style="font-size: 11px; background-color: #e02a11; border-color: #e02a11;" data-bs-toggle="modal" data-bs-target="#working_location">Working Location</button>
									</div>
									<div class="col-6">
										<button type="button" class="btn w-100 h-100 py-2 px-1 fw-bold text-white" style="font-size: 11px; background-color: #4f00e5; border-color: #4f00e5;" id="btn_create_receipt"><i class="ti ti-circle-plus me-1"></i>Create Receipt</button>
									</div>
									<div class="col-12">
										<a href="{{ box_route('accounts.ledger.show', ['lead_id' => $lead->id]) }}" class="btn btn-primary w-100 h-100 py-2 px-1 fw-medium" style="font-size: 11px;">Ledger {{ $lead->company_name ?? '' }}</a>
									</div>
									<div class="col-6">
										<a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_invoice" class="btn btn-primary w-100 h-100 py-2 px-1 fw-medium" style="font-size: 11px;">Create Invoice</a>
									</div>
									<div class="col-6">
										<a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_invoice" class="btn btn-primary w-100 h-100 py-2 px-1 fw-medium" style="font-size: 11px;">Create Performa Invoice</a>
									</div>
								</div>




							</div>
						</div>
					</div>
					<!-- /Leads Sidebar -->

					<!-- Leads Details -->
					<div class="col-xl-9">

						<div class="card mb-3">
							<div class="card-body pb-0">
								<h4 class="fw-semibold mb-3">Lead Pipeline Status</h4>
								<div class="pipeline-list">
									<ul>
										
									</ul>
								</div>
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
										<a href="#" data-bs-toggle="tab" data-bs-target="#meetings" class="nav-link"><i class="ti ti-calendar me-1"></i>Meetings</a>
									</li>
									<li class="nav-item" role="presentation">
										<a href="#" data-bs-toggle="tab" data-bs-target="#files" class="nav-link"><i class="ti ti-file me-1"></i>Files</a>
									</li>
									<li class="nav-item" role="presentation">
										<a href="#" data-bs-toggle="tab" data-bs-target="#email" class="nav-link"><i class="ti ti-mail-check me-1"></i>Email</a>
									</li>
									<li class="nav-item" role="presentation">
										<a href="#" data-bs-toggle="tab" data-bs-target="#wapp" class="nav-link"><i class="fa-brands fa-whatsapp me-1"></i>WhatsApp</a>
									</li>
									<li class="nav-item" role="presentation">
										<a href="#" data-bs-toggle="tab" data-bs-target="#general" class="nav-link"><i class="ti ti-file-text me-1"></i>General</a>
									</li>
									<li class="nav-item" role="presentation">
										<a href="#" data-bs-toggle="tab" data-bs-target="#assignment_history" class="nav-link"><i class="ti ti-history me-1"></i>Assignment History</a>
									</li>
									<li class="nav-item" role="presentation">
										<a href="#" data-bs-toggle="tab" data-bs-target="#receipts_history" class="nav-link"><i class="ti ti-receipt me-1"></i>Receipt History</a>
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
											<div class="form-sort">
												<i class="ti ti-sort-ascending-2"></i>
												<select class="select activity-sort-select" data-filter="all" data-container="#activities-container">
													<option value="desc">Sort By Date</option>
													<option value="asc">Ascending</option>
													<option value="desc">Descending</option>
												</select>
											</div>
										</div>
									</div>
									<div class="card-body">
										<div class="contact-activity" id="activities-container">
                                            <!-- Dynamic Activities will be loaded here -->
                                            <div class="text-center p-3">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
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
										<div>
											<div class="form-sort">
												<i class="ti ti-sort-ascending-2"></i>
												<select class="select activity-sort-select" data-filter="note" data-container="#notes-container">
													<option value="desc">Sort By Date</option>
													<option value="asc">Ascending</option>
													<option value="desc">Descending</option>
												</select>
											</div>
										</div>
									</div>
									<div class="card-body">
										<div class="contact-activity" id="notes-container">
                                              <!-- Dynamic Notes will be loaded here -->
                                             <div class="text-center p-3">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
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
											<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create_call" class="link-purple fw-medium"><i class="ti ti-circle-plus me-1"></i>Add New</a>
										</div>
									</div>
									<div class="card-body">
										<div class="contact-activity" id="calls-container">
                                            <!-- Dynamic Calls will be loaded here -->
                                             <div class="text-center p-3">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
										</div>
									</div>
								</div>
							</div>
							<!-- /Calls -->


							<!-- Meetings -->
							<div class="tab-pane fade" id="meetings">
								<div class="card">
									<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
										<h4 class="fw-semibold">Meetings</h4>
										<div>
											<div class="form-sort">
												<i class="ti ti-sort-ascending-2"></i>
												<select class="select activity-sort-select" data-filter="meeting" data-container="#meetings-container">
													<option value="desc">Sort By Date</option>
													<option value="asc">Ascending</option>
													<option value="desc">Descending</option>
												</select>
											</div>
										</div>
									</div>
									<div class="card-body">
										<div class="contact-activity" id="meetings-container">
                                              <!-- Dynamic Meetings will be loaded here -->
                                             <div class="text-center p-3">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
										</div>
									</div>
								</div>
							</div>
							<!-- /Meetings -->



							<!-- Files -->
							<div class="tab-pane fade" id="files">
								<div class="card">
									<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
										<h4 class="fw-semibold">Files</h4>
										<div>
											<div class="form-sort">
												<i class="ti ti-sort-ascending-2"></i>
												<select class="select activity-sort-select" data-filter="files" data-container="#files-container">
													<option value="desc">Sort By Date</option>
													<option value="asc">Ascending</option>
													<option value="desc">Descending</option>
												</select>
											</div>
										</div>
									</div>
									<div class="card-body">
                                        <div class="contact-activity" id="files-container">
                                              <!-- Dynamic Files will be loaded here -->
                                             <div class="text-center p-3">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
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
									<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
										<h4 class="fw-semibold">Email</h4>
										<div class="d-inline-flex align-items-center">
											<div class="form-sort me-3">
												<i class="ti ti-sort-ascending-2"></i>
												<select class="select activity-sort-select" data-filter="email" data-container="#emails-container">
													<option value="desc">Sort By Date</option>
													<option value="asc">Ascending</option>
													<option value="desc">Descending</option>
												</select>
											</div>
											<a href="javascript:void(0);" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#compose_email_modal" onclick="$('#compose_subject').val(''); $('#compose_body').val('');"><i class="ti ti-mail-forward me-1"></i>Compose Email</a>
										</div>
									</div>
									<div class="card-body">
										<div class="contact-activity" id="emails-container">
                                            <!-- Dynamic Emails will be loaded here -->
                                             <div class="text-center p-3">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
										</div>
									</div>
								</div>
							</div>
							<!-- /Email -->
							<!-- WhatsApp -->
							<div class="tab-pane fade" id="wapp">
								<div class="card">
									<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
										<h4 class="fw-semibold">WhatsApp Messages</h4>
										<div class="d-inline-flex align-items-center gap-2">
											<form id="sendWhatsAppTemplateForm" class="d-flex align-items-center gap-2 m-0">
												@csrf
												<input type="hidden" name="lead_id" value="{{ $leadId }}">
												<select name="template_id" class="form-select form-select-sm" required style="width: 200px;">
													<option value="">Select Template...</option>
													@if(isset($whatsAppTemplates))
														@foreach($whatsAppTemplates as $template)
															<option value="{{ $template->id }}">{{ $template->name }}</option>
														@endforeach
													@endif
												</select>
												<button type="submit" class="btn btn-sm btn-primary">Send</button>
											</form>
											<a href="{{ box_route('whatsapp.index', ['lead_id' => $leadId]) }}" class="btn btn-sm btn-success">
												<i class="ti ti-brand-whatsapp me-1"></i>Open Chat Workspace
											</a>
										</div>
									</div>
									<div class="card-body">
										<div class="contact-activity" id="whatsapp-container">
                                              <!-- Dynamic WhatsApp messages will be loaded here -->
                                             <div class="text-center p-3">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
										</div>
									</div>
								</div>
							</div>
							<!-- /WhatsApp -->

							<!-- General -->
							<div class="tab-pane fade" id="general">
								<div class="card">
									<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
										<h4 class="fw-semibold">General Activities</h4>
									</div>
									<div class="card-body">
										<div class="contact-activity" id="general-container">
                                              <!-- Dynamic General activities will be loaded here -->
                                             <div class="text-center p-3">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
										</div>
									</div>
								</div>
							</div>
							<!-- /General -->
							
							<!-- Assignment History -->
							<div class="tab-pane fade" id="assignment_history">
								<div class="card">
									<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
										<h4 class="fw-semibold">Assignment History</h4>
									</div>
									<div class="card-body p-0">
										<div class="table-responsive">
											<table class="table table-nowrap mb-0">
												<thead class="thead-light">
													<tr>
														<th>Assigned To</th>
														<th>Department / Designation</th>
														<th>Action</th>
														<th>Assigned By</th>
														<th>Assigned At</th>
													</tr>
												</thead>
												<tbody>
													@forelse($assignmentHistory as $history)
														@php
															$toBoxUser = $boxUsers->get($history->user_id);
															$byBoxUser = $boxUsers->get($history->assigned_by);
														@endphp
														<tr>
															<td>
																<div class="d-flex align-items-center">
																	<div class="avatar avatar-sm me-2 flex-shrink-0" style="display: flex; align-items: center; justify-content: center;">
																		@if($toBoxUser && $toBoxUser->profile_image_path)
																			<img src="{{ asset($toBoxUser->profile_image_path) }}" class="rounded-circle" alt="User Image" style="object-fit: cover; width: 100%; height: 100%;">
																		@else
																			<span class="avatar-title rounded-circle bg-primary-transparent text-primary d-flex align-items-center justify-content-center" style="width: 100%; height: 100%; font-size: 16px; font-weight: 600;">{{ $history->user->name ? strtoupper(substr($history->user->name, 0, 1)) : 'U' }}</span>
																		@endif
																	</div>
																	<div class="h6 mb-0">{{ $history->user->name ?? 'N/A' }}</div>
																</div>
															</td>
															<td>
																<div>{{ $toBoxUser->departmentRelation->name ?? 'N/A' }}</div>
																<small class="text-muted">{{ $toBoxUser->designationRelation->name ?? 'N/A' }}</small>
															</td>
															<td>
																<span class="badge {{ $history->action == 'assigned' ? 'bg-success' : 'bg-danger' }}">
																	{{ ucfirst($history->action) }}
																</span>
															</td>
															<td>
																<div class="d-flex align-items-center">
																	<div class="h6 mb-0">{{ $history->assignedBy->name ?? 'System' }}</div>
																</div>
																@if($byBoxUser)
																	<small class="text-muted">{{ $byBoxUser->departmentRelation->name ?? '' }} - {{ $byBoxUser->designationRelation->name ?? '' }}</small>
																@endif
															</td>
															<td>{{ $history->created_at->format('d M Y, h:i A') }}</td>
														</tr>
													@empty
														<tr>
															<td colspan="5" class="text-center p-4">
																<div class="text-muted">No assignment history found.</div>
															</td>
														</tr>
													@endforelse
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<!-- /Assignment History -->

							<!-- Receipts History -->
							<div class="tab-pane fade" id="receipts_history">
								<div class="card">
									<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
										<h4 class="fw-semibold">Receipt History</h4>
									</div>
									<div class="card-body p-0">
										<div id="receipts-container">
                                              <!-- Dynamic Receipts will be loaded here -->
                                             <div class="text-center p-3">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
										</div>
									</div>
								</div>
							</div>
							<!-- /Receipts History -->







						</div>
						<!-- /Tab Content -->

					</div>
					<!-- /Leads Details -->

				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->
	<!-- Quick Edit Lead -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_quick_edit">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Quick Edit Lead</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form id="quickEditForm" method="POST">
					@csrf
					<div id="sortable-fields-edit" class="sortable-container">
						<div class="text-center py-4">
							<div class="spinner-border text-primary" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
						</div>
					</div>

					<div class="d-flex align-items-center justify-content-end mt-4">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary">Update</button>
					</div>
				</form>
			</div>

		</div>
		<!-- /Add Lead -->
	<!-- Add New Activity -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h4>Add New Activity </h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form id="addActivityForm">
					<div>
						<div class="row">
							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Activity Type <span class="text-danger">*</span></label>
									<ul class="radio-activity">
										<li>
											<div class="active-type">
												<input type="radio" id="type_call" name="type" value="call" checked="">
												<label for="type_call"><i class="ti ti-phone"></i>Calls</label>
											</div>
										</li>
										<li>
											<div class="active-type">
												<input type="radio" id="type_email" name="type" value="email">
												<label for="type_email"><i class="ti ti-mail"></i>Email</label>
											</div>
										</li>
										<li>
											<div class="active-type">
												<input type="radio" id="type_meeting" name="type" value="meeting">
												<label for="type_meeting"><i class="ti ti-user-share"></i>Meeting</label>
											</div>
										</li>
										<li>
											<div class="active-type">
												<input type="radio" id="type_whatsapp" name="type" value="whatsapp">
												<label for="type_whatsapp"><i class="ti ti-brand-whatsapp"></i>WhatsApp</label>
											</div>
										</li>
                                        <li>
                                            <div class="active-type">
                                                <input type="radio" id="type_note" name="type" value="note">
                                                <label for="type_note"><i class="ti ti-notes"></i>Notes</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="active-type">
                                                <input type="radio" id="type_general" name="type" value="general">
                                                <label for="type_general"><i class="ti ti-file-text"></i>General</label>
                                            </div>
                                        </li>
									</ul>
								</div>
							</div>

                            <!-- Compose Email Fields for "Email" Activity Type -->
                            <div class="col-md-12 field-compose-email" style="display:none;">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="col-form-label">From <span class="text-danger">*</span></label>
                                        <select class="form-select" name="from_email_id" id="add_from_email">
                                            @if(isset($connectedEmails) && count($connectedEmails) > 0)
                                                @foreach($connectedEmails as $emailAccount)
                                                    <option value="{{ $emailAccount->id }}">{{ $emailAccount->email }}</option>
                                                @endforeach
                                            @else
                                                <option value="">No connected email accounts</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="col-form-label">To <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="to_email" id="add_to_email" value="{{ $lead->email ?? '' }}">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="col-form-label">Subject <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="subject" id="add_subject" placeholder="Enter Subject">
                                </div>
                                <div class="mb-3">
                                    <label class="col-form-label">Message <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="body" id="add_body" rows="4" placeholder="Type your message here..."></textarea>
                                    <input type="file" id="summernote_file_input" style="display: none;" />
                                </div>
                            </div>

                            <!-- Compose WhatsApp Fields for "WhatsApp" Activity Type -->
                            <div class="col-md-12 field-compose-whatsapp" style="display:none;">
                                <div class="mb-3">
                                    <label class="col-form-label">WhatsApp Template <span class="text-info">(Optional)</span></label>
                                    <select class="form-select" name="whatsapp_template_id" id="add_whatsapp_template_id">
                                        <option value="">Select Template...</option>
                                        @if(isset($whatsAppTemplates))
                                            @foreach($whatsAppTemplates as $template)
                                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <small class="text-muted mt-1 d-block">Selecting a template will immediately send it when this activity is created.</small>
                                </div>
                            </div>

							<!-- Fields for Call type -->
							<div class="col-md-6 field-call field-meeting">
								<div class="mb-3">
									<label class="col-form-label">Activity <span class="text-danger">*</span></label>
									<select class="form-control form-select" id="activity_setting_id" name="activity_setting_id">
										<option value="">Select Activity</option>
									</select>
								</div>
							</div>

							<div class="col-md-6 field-call field-meeting">
								<div class="mb-3">
									<label class="col-form-label">Activity Type <span class="text-danger">*</span></label>
									<select class="form-control form-select" id="activity_type" name="activity_type">
										<option value="">Select Activity Type</option>
									</select>
								</div>
							</div>

							<div class="col-md-6 field-call field-meeting">
								<div class="mb-3">
									<label class="col-form-label">Activity Status</label>
									<select class="select form-control form-select" id="activity_status_id" name="activity_status_id">
										<option value="">Select Status</option>
									</select>
								</div>
							</div>

                            <div class="col-md-6 field-meeting" id="done_with_wrapper" style="display:none;">
								<div class="mb-3">
									<label class="col-form-label">Done With</label>
									<select class="form-control" id="done_with" name="done_with">
										<option value="">Select User</option>
									</select>
								</div>
							</div>

							<div class="col-md-6 field-call field-meeting field-email field-whatsapp field-general">
								<div class="mb-3">
									<label class="col-form-label">lead Assigned To</label>
									<select class="form-control form-select" id="assigned_to_assigned_to" name="assigned_to_assigned_to" style="display: block !important; visibility: visible !important;">
										<option value="">Select User</option>
									</select>
								</div>
							</div>

                            <div class="col-md-6 field-call field-meeting field-email field-whatsapp field-general">
								<div class="mb-3">
									<label class="col-form-label">Lead Status <span class="text-danger">*</span></label>
									<select class="form-control form-select" id="activity_lead_status_id" name="lead_status_id" style="display: block !important; visibility: visible !important;">
										<option value="">Select Lead Status</option>
									</select>
								</div>
							</div>

							<div class="col-md-6 field-call field-meeting field-email field-whatsapp field-general" id="activity_lead_sub_status_wrapper">
								<div class="mb-3">
									<label class="col-form-label">Lead Sub Status</label>
									<select class="form-control form-select" id="activity_lead_sub_status_id" name="lead_sub_status_id">
										<option value="">Select Lead Sub Status</option>
									</select>
								</div>
							</div>



							<div class="col-md-6 field-meeting">
								<div class="mb-3">
									<label class="col-form-label">Meeting Date <span class="text-danger">*</span></label>
									<div class="icon-form">
										<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
										<input type="text" class="MyDateTimePicker form-control" id="meeting_date" name="meeting_date" placeholder="MM/DD/YYYY hh:mm AM/PM" readonly>
                                        <!--<input type="datetime-local" class="form-control" id="meeting_date" name="meeting_date">-->
									</div>
								</div>
							</div>

                            <!-- Subject Commented Out -->
                            <!--
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Subject</label>
									<input type="text" class="form-control" id="subject" name="subject" placeholder="Enter subject">
								</div>
							</div>
                            -->

                            <!-- Activity Date & Reminder Date Commented Out -->
                             <input type="hidden" id="activity_date" name="activity_date">
                            <!--
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Activity Date <span class="text-danger">*</span></label>
									<div class="icon-form">
										<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
										<input type="datetime-local" class="form-control" id="activity_date_disabled" name="activity_date_disabled">
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Reminder Date</label>
									<div class="icon-form">
										<span class="form-icon"><i class="ti ti-clock"></i></span>
										<input type="datetime-local" class="form-control" id="reminder_date" name="reminder_date">
									</div>
								</div>
							</div>
                            -->

                            <!-- Label Commented Out -->
                            <!--
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Label</label>
									<input type="text" class="form-control" id="label" name="label" placeholder="Enter label">
								</div>
							</div>
                            -->


							<div class="col-md-12">
							    <div class="mb-3">
									<div class="form-check form-check-md d-flex align-items-center">
											<input class="form-check-input" name="followuptask" type="checkbox" value="1" id="followuptask_checkbox">
											<label class="form-check-label" for="followuptask_checkbox">
												Create a follow-up task
											</label>
									</div>
								</div>
							</div>


							<div id="followupTaskSection" style="display:none">
							<div class="row">
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Task <span class="text-danger">*</span></label>
										<select class="select" id="followup_task_type" name="followup_task_type">
											<option value="">Select Task</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
								<label class="col-form-label">Task Date <span class="text-danger">*</span></label>
								<div class="mb-3 icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="MyDateTimePicker form-control" id="followup_task_date" name="followup_task_date" placeholder="MM/DD/YYYY hh:mm AM/PM" readonly>
                                    <!--<input type="datetime-local" class="form-control" id="followup_task_date" name="followup_task_date">-->
								</div>
							    </div>

								<div class="col-md-6" id="followup_meeting_date_field" style="display:none">
								<label class="col-form-label">Meeting Date <span class="text-danger">*</span></label>
								<div class="mb-3 icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="MyDateTimePicker form-control" id="followup_meeting_date" name="followup_meeting_date" placeholder="MM/DD/YYYY hh:mm AM/PM" readonly>
                                    <!--<input type="datetime-local" class="form-control" id="followup_meeting_date" name="followup_meeting_date">-->
								</div>
							    </div>

								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Task Assign To</label>
										<select class="form-control form-select" id="followup_task_assign_to" name="followup_task_assign_to">
											<option value="">Select User</option>
										</select>
									</div>
								</div>

								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Task Priority <span class="text-danger" id="followup_task_priority_asterisk">*</span></label>
										<select class="select" id="followup_task_priority" name="followup_task_priority">
											<option value="low">Low</option>
											<option value="medium">Medium</option>
											<option value="high">High</option>
										</select>
									</div>
								</div>




							</div>
							</div>


							<div class="col-lg-12">
								<div class="mb-3">
									<label class="col-form-label">Remarks</label>
									<textarea class="form-control" rows="4" id="description" name="description" placeholder="Enter remarks"></textarea>
								</div>
							</div>

							<div class="col-lg-12">
								<div class="mb-3">
									<label class="col-form-label">Attachment</label>
									<div class="profile-upload">
										<div class="profile-upload-content">
											<label class="profile-upload-btn">
												<i class="ti ti-file-broken"></i> Upload File
												<input type="file" class="input-img" id="activity_file" name="file" multiple accept="*/*" style="display: none;">
											</label>
											<p class="mt-2 mb-0">You can add multiple attachments. File should be less than 5MB each.</p>
											<div id="file-list" class="mt-2"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary" id="submitActivityBtn">Create</button>
					</div>
				</form>
			</div>
		</div>
		<!-- /Add New Activity -->
		<!-- Edit Activity -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom">
				<h4>Edit Activity </h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form id="editActivityForm">
                    <input type="hidden" id="edit_activity_id" name="activity_id">
					<div>
						<div class="row">

							<div class="col-md-12">
								<div class="mb-3">
									<label class="col-form-label">Activity Type <span class="text-danger">*</span></label>
									<ul class="radio-activity">
										<li>
											<div class="active-type">
												<input type="radio" id="edit_type_call" name="type" value="call">
												<label for="edit_type_call"><i class="ti ti-phone"></i>Calls</label>
											</div>
										</li>
										<li>
											<div class="active-type">
												<input type="radio" id="edit_type_email" name="type" value="email">
												<label for="edit_type_email"><i class="ti ti-mail"></i>Email</label>
											</div>
										</li>
										<li>
											<div class="active-type">
												<input type="radio" id="edit_type_meeting" name="type" value="meeting">
												<label for="edit_type_meeting"><i class="ti ti-user-share"></i>Meeting</label>
											</div>
										</li>
										<li>
											<div class="active-type">
												<input type="radio" id="edit_type_whatsapp" name="type" value="whatsapp">
												<label for="edit_type_whatsapp"><i class="ti ti-brand-whatsapp"></i>WhatsApp</label>
											</div>
										</li>
                                        <li>
                                            <div class="active-type">
                                                <input type="radio" id="edit_type_note" name="type" value="note">
                                                <label for="edit_type_note"><i class="ti ti-notes"></i>Notes</label>
                                            </div>
                                        </li>
									</ul>
								</div>
							</div>

                            <!-- Dynamic fields based on type -->
                            <div class="col-md-6 field-call field-meeting field-whatsapp" style="display:none">
								<div class="mb-3">
									<label class="col-form-label">Activity Setting <span class="text-danger">*</span></label>
									<select class="form-select" id="edit_activity_setting_id" name="activity_setting_id">
										<option value="">Select Activity</option>
                                        @foreach($activitySettings as $setting)
                                            @if(strtolower($setting->title) !== 'task')
                                                <option value="{{ $setting->id }}">{{ $setting->title }}</option>
                                            @endif
                                        @endforeach
									</select>
								</div>
							</div>
                            <div class="col-md-6 field-call field-meeting field-whatsapp" style="display:none">
								<div class="mb-3">
									<label class="col-form-label">Activity Type <span class="text-danger">*</span></label>
									<select class="form-select" id="edit_activity_type" name="activity_type">
										<option value="">Select Activity Type</option>
									</select>
								</div>
							</div>
                            <div class="col-md-6 field-call field-meeting field-whatsapp" style="display:none">
								<div class="mb-3">
									<label class="col-form-label">Activity Status</label>
									<select class="form-select" id="edit_activity_status_id" name="activity_status_id">
										<option value="">Select Status</option>
									</select>
								</div>
							</div>
                            <div class="col-md-6 field-call field-meeting field-whatsapp" style="display:none">
                                <div class="mb-3">
                                    <label class="col-form-label">Date <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="edit_activity_date" name="activity_date">
                                </div>
                            </div>
                            <!-- Meeting specific -->
                            <div class="col-md-12 field-meeting" style="display:none">
                                <div class="mb-3">
                                    <label class="col-form-label">Guests (Emails)</label>
                                    <input type="text" class="form-control" id="edit_guests" name="guests" placeholder="Add emails separate by comma">
                                </div>
                            </div>
                            <div class="col-md-12 field-meeting" style="display:none">
                                <div class="mb-3">
                                    <label class="col-form-label">Location</label>
                                    <input type="text" class="form-control" id="edit_location" name="location">
                                </div>
                            </div>

							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Assign To</label>
									<select class="select" id="edit_assigned_to" name="assigned_to">
										<option value="">Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
									</select>
								</div>
							</div>

							<div class="col-lg-12">
								<div class="mb-3">
									<label class="col-form-label">Subject</label>
									<input type="text" class="form-control" id="edit_subject" name="subject">
								</div>
							</div>

							<div class="col-lg-12">
								<div class="mb-3">
									<label class="col-form-label">Remarks</label>
									<textarea class="form-control" rows="4" id="edit_description" name="description" placeholder="Enter remarks"></textarea>
								</div>
							</div>
						</div>
					</div>
						<div class="col-md-6 field-call field-meeting field-email field-whatsapp field-general">
								<div class="mb-3">
									<label class="col-form-label">Lead Status <span class="text-danger">*</span></label>
									<select class="form-control" id="edit_activity_lead_status_id" name="lead_status_id">
										<option value="">Select Lead Status</option>
                                        @foreach($leadStatus as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                        @endforeach
									</select>
								</div>
							</div>

							<div class="col-md-6 field-call field-meeting field-email field-whatsapp field-general" id="edit_activity_lead_sub_status_wrapper">
								<div class="mb-3">
									<label class="col-form-label">Lead Sub Status</label>
									<select class="form-control" id="edit_activity_lead_sub_status_id" name="lead_sub_status_id">
										<option value="">Select Lead Sub Status</option>
									</select>
								</div>
							</div>

							<div class="col-md-12">
							    <div class="mb-3">
									<div class="form-check form-check-md d-flex align-items-center">
											<input class="form-check-input" name="create_followup" type="checkbox" value="1" id="edit_create_followup">
											<label class="form-check-label" for="edit_create_followup">
												Create a follow-up task
											</label>
									</div>
								</div>
							</div>

							<div id="edit_followup_section" style="display:none">
							<div class="row">
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Task <span class="text-danger">*</span></label>
										<select class="select" id="edit_followup_type" name="followup_type">
											<option value="call">Call</option>
											<option value="meeting">Meeting</option>
                                            <option value="email">Email</option>
                                            <option value="task">Task</option>
                                            <option value="whatsapp">WhatsApp</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
								<label class="col-form-label">Task Date <span class="text-danger">*</span></label>
								<div class="mb-3 icon-form">
									<span class="form-icon"><i class="ti ti-calendar-check"></i></span>
									<input type="text" class="MyDateTimePicker form-control" id="edit_followup_date" name="followup_date" placeholder="MM/DD/YYYY hh:mm AM/PM" readonly>
                                    <!-- <input type="datetime-local" class="form-control" id="edit_followup_date" name="followup_date">-->
								</div>
							    </div>

								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Task Assign To</label>
										<select class="select" id="edit_followup_assigned_to" name="followup_assigned_to">
											<option value="">Select User</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ Auth::id() == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
										</select>
									</div>
								</div>

                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="col-form-label">Remarks</label>
                                        <textarea class="form-control" rows="4" id="edit_followup_remarks" name="followup_remarks" placeholder="Enter remarks"></textarea>
                                    </div>
                                </div>

							</div>
							</div>
					</div>
					<div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary">Update</button>
					</div>
				</form>
			</div>
		</div>
		<!-- /Edit Activity -->
		<div class="modal fade modal-xl" id="working_location" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
				    <div class="modal-header">
						<h5 class="modal-title">Working Location</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body" style="max-height:inherit;">
					    <form action="#" id="workingLocationForm">
						<div class="row">
								<div class="col-md-4">
									<div class="mb-2">
										<label class="col-form-label">State <span class="text-danger">*</span></label>
										<select class="select" id="working_state_id" name="state_id">
                                            <option value="">-- Select State --</option>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="mb-2">
										<label class="col-form-label">City <span class="text-danger">*</span></label>
										<select class="select" id="working_city_id" name="city_id">
											<option value="">-- Select City --</option>
										</select>
									</div>
								</div>
                                <div class="col-md-3">
                                    <div class="mb-2 ps-2" style="margin-top: 35px;">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="working_loc_type" id="working_loc_type_radius" value="radius" checked>
                                            <label class="form-check-label" for="working_loc_type_radius">By Radius</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="working_loc_type" id="working_loc_type_area" value="area">
                                            <label class="form-check-label" for="working_loc_type_area">By Area</label>
                                        </div>
                                    </div>
                                </div>
								<div class="col-md-3">
									<div class="mb-2">
										<label class="col-form-label">Division <span class="text-danger">*</span></label>
										<select class="select" id="working_division_id" name="division_id">
											<option value="">-- Select Division --</option>
										</select>
									</div>
								</div>
								<div class="col-md-12">
									<div class="text-end mb-2">
										<button type="button" class="btn btn-primary" id="working_location_search_btn">Search</button>
                                        <button type="button" class="btn btn-success ms-2" id="working_location_save_btn">Add Division</button>
									</div>
								</div>
								<div class="col-md-12 mb-2">
								    <div id="working_loc_radius_help">
								        <p>Default location radius 25 km you can increase by dragging circle pointers </p>
								    </div>
								</div>
								<div class="col-md-12">
								    <div id="working_location_map" style="height: 400px; width: 100%; border: 1px solid #ccc; border-radius: 4px;"></div>
								</div>
								<div class="col-md-12">
								    <div class="table-responsive">
									    <table class="table" id="working_location_table">
                                            <thead>
                                                <tr>
                                                        <th>State</th>
                                                        <th>City</th>
                                                        <th>Division</th>
                                                        <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="working_location_table_body">
                                                <!-- Dynamic rows will be added here -->
                                            </tbody>
										</table>
									</div>
								</div>
								<div class="col-lg-12 mt-2 text-end modal-btn">
								<a class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<button class="btn btn-primary" type="submit">Save</button>
								</div>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
        <!-- View Full Email Modal -->
        <div class="modal fade" id="view_full_email_modal" role="dialog" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h4 class="modal-title">Email Content</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="p-3">
                            <iframe id="view_email_iframe" src="" style="width: 100%; height: 75vh; border: 1px solid #dee2e6; border-radius: 8px;"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
		<!-- Delete Working Location -->
		<div class="modal fade" id="delete_working_location_modal" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Location?</h4>
							<p class="mb-0">Are you sure you want to remove <br> this working location?</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="javascript:void(0);" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<button type="button" class="btn btn-danger" id="confirm_delete_location_btn">Yes, Delete it</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Working Location -->
		<!-- Create Leads -->
		<div class="modal custom-modal fade" id="create_contact" role="dialog">
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
								<i class="ti ti-user-plus"></i>
							</div>
							<h3>Contact Created Successfully!!!</h3>
							<p>View the details of contact, created</p>
							<div class="col-lg-12 text-center modal-btn">
								<a href="#" class="btn btn-light" data-bs-dismiss="modal">Cancel</a>
								<a href="leads-details.html" class="btn btn-primary">View Details</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Create Leads -->
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
						<form action="leads-details.html">
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
											<input type="text" class="MyDatePicker form-control" placeholder="MM/DD/YYYY" readonly>
                                            <!--<input type="text" class="form-control datetimepicker" placeholder="">-->
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
								<form action="leads-details.html">
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
								<form action="leads-details.html">
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
        <!-- Create Receipt Offcanvas -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_receipt_add">
			<div class="offcanvas-header border-bottom">
				<h4 class="offcanvas-title" id="receiptOffcanvasTitle">Create New Receipt</h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="{{ route('accounts.receipts.store', ['box_slug' => request()->route('box_slug') ?? 'default']) }}" method="POST" id="receipt_add_form">
                    @csrf
                    <input type="hidden" name="id" id="receipt_id">
                    <input type="hidden" name="action_type" id="receipt_action_type" value="store">
					<div class="row">
						    <div class="col-md-6 d-none">
								<div class="mb-3">
									<label class="col-form-label">Lead <span class="text-danger">*</span></label>
									<select class="select" id="receipt_lead_select" name="lead_id">
										<option selected disabled>-- Choose --</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Company <span class="text-danger">*</span></label>
									<select class="select" id="receipt_company_select" name="company_id">
										<option selected disabled>-- Choose --</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Receipt Date<span class="text-danger"> *</span></label>
									<input type="text" class="MyDatePicker form-control" name="receipt_date"placeholder="MM/DD/YYYY" readonly required>
                                    <!--<input type="date" class="form-control" name="receipt_date" required>-->
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Invoice Number</label>
									<input class="form-control" type="text" name="invoice_number">
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Amount<span class="text-danger"> *</span></label>
									<input class="form-control" type="text" name="amount" required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Payment Mode <span class="text-danger">*</span></label>
									<select class="select" name="payment_mode" id="receipt_payment_mode" required>
										<option selected disabled>-- Choose --</option>
										<option value="Bank Transfer">Bank Transfer</option>
										<option value="UPI">UPI</option>
										<option value="NEFT">NEFT</option>
										<option value="GPay">GPay</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Bank <span class="text-danger">*</span></label>
									<select class="select" id="receipt_bank_select" name="bank_id" required>
										<option selected disabled>-- Choose --</option>
									</select>
								</div>
							</div>
                            <div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Division <span class="text-danger">*</span></label>
									<select class="select" id="receipt_division_select" name="division_id" required>
										<option selected disabled>-- Choose --</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Reference</label>
									<input class="form-control" type="text" name="reference">
								</div>
							</div>
						</div>
					</div>
					<div class="d-flex align-items-center justify-content-end mt-3">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary" id="receipt_submit_btn">Submit</button>
					</div>
				</form>
			</div>
		</div>
        <!-- Mark Activity Done Modal -->
        <div class="modal custom-modal fade" id="mark_activity_done_modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white">Mark Activity as Done</h5>
                        <button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="markActivityDoneForm">
                            <input type="hidden" id="mark_done_activity_id" name="activity_id">

                            <!-- Activity Dropdowns Row -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Activity Setting <span class="text-danger">*</span></label>
                                    <select class="form-select" id="mark_done_activity_setting" name="activity_setting_id" required>
                                        <option value="">--Select--</option>
                                        <!-- Options populated dynamically -->
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Activity Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="mark_done_activity_type" name="activity_type" required>
                                        <option value="">--Select--</option>
                                        <!-- Options populated dynamically -->
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-danger">Activity Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="mark_done_activity_status" name="activity_status_id" required>
                                        <option value="">--Select--</option>
                                        <!-- Options populated dynamically -->
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lead Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="mark_done_lead_status" name="lead_status_id" required>
                                        <option value="">--Select--</option>
                                        @foreach($leadStatus as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lead Sub Status</label>
                                    <select class="form-select" id="mark_done_lead_sub_status" name="lead_sub_status_id">
                                        <option value="">--Select--</option>
                                    </select>
                                </div>

                            </div>

                            <!-- Follow Up Section -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="mark_done_create_followup" name="create_followup">
                                <label class="form-check-label" for="mark_done_create_followup">Create a follow-up task</label>
                            </div>

                            <div id="mark_done_followup_section" style="display: none;" class="mb-3 border p-3 rounded bg-light">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Task <span class="text-danger">*</span></label>
                                        <select class="form-select select" id="mark_done_followup_type" name="followup_type">
                                            <option value="">Select Task</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Task Date <span class="text-danger">*</span></label>
                                        <div class="icon-form">
                                            <span class="form-icon"><i class="ti ti-calendar-check"></i></span>
                                            <input type="text" class="MyDateTimePicker form-control" id="mark_done_followup_date" name="followup_date" placeholder="MM/DD/YYYY hh:mm AM/PM" readonly>
                                            <!-- <input type="datetime-local" class="form-control" id="mark_done_followup_date" name="followup_date"> -->
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3" id="mark_done_followup_meeting_date_field" style="display:none">
                                        <label class="form-label">Meeting Date <span class="text-danger">*</span></label>
                                        <div class="icon-form">
                                            <span class="form-icon"><i class="ti ti-calendar-check"></i></span>
                                            <input type="text" class="MyDateTimePicker form-control" id="mark_done_followup_meeting_date" name="followup_meeting_date" placeholder="MM/DD/YYYY hh:mm AM/PM" readonly>
                                            <!--<input type="datetime-local" class="form-control" id="mark_done_followup_meeting_date" name="followup_meeting_date">-->
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Task Assign To</label>
                                        <select class="form-select" id="mark_done_followup_assigned_to" name="followup_assigned_to">
                                            <option value="">Select User</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- <div class="col-md-12 mb-0">
                                        <label class="form-label">Task Remarks</label>
                                        <textarea class="form-control" id="mark_done_followup_remarks" name="followup_remarks" rows="2"></textarea>
                                    </div> -->
                                </div>
                            </div>

                            <!-- Main Remarks (Closure Remarks) -->
                            <div class="mb-3">
                                <label class="form-label">Remarks</label>
                                <textarea class="form-control" id="mark_done_remarks" name="remarks" rows="3" placeholder="Enter remarks for closing this activity..."></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCEL</button>
                                <button type="submit" class="btn btn-info text-white" id="mark_done_submit_btn">SAVE</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Mark Activity Done Modal -->
        <!-- Toast Container -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1060;">
            <div id="leadToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="toastMessage">
                        <!-- Message -->
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
		<!-- Compose Email Modal -->
		<div class="modal custom-modal fade" id="compose_email_modal" role="dialog">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Compose Email</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form id="composeEmailForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="lead_id" value="{{ $leadId }}">
							<div class="mb-3">
								<label class="col-form-label">From <span class="text-danger">*</span></label>
								<select class="form-select" name="from_email_id" id="compose_from_email" required>
									@if(isset($connectedEmails) && count($connectedEmails) > 0)
										@foreach($connectedEmails as $emailAccount)
											<option value="{{ $emailAccount->id }}">{{ $emailAccount->email }}</option>
										@endforeach
									@else
										<option value="">No connected email accounts</option>
									@endif
								</select>
							</div>
							<div class="mb-3">
								<label class="col-form-label">To <span class="text-danger">*</span></label>
								<input type="email" class="form-control" name="to_email" id="compose_to_email" value="{{ $lead->email }}" required>
							</div>
							<div class="mb-3">
								<label class="col-form-label">Subject <span class="text-danger">*</span></label>
								<input type="text" class="form-control" name="subject" id="compose_subject" required>
							</div>
							<div class="mb-3">
								<label class="col-form-label">Message <span class="text-danger">*</span></label>
								<textarea class="form-control" name="body" id="compose_body" rows="6"></textarea>
							</div>
                            <div class="mb-3">
                                <label class="col-form-label">Attachments (Optional)</label>
                                <input type="file" class="form-control" name="attachments[]" id="compose_attachments" multiple>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-check-md d-flex align-items-center">
                                    <input class="form-check-input" name="create_followup" type="checkbox" value="1" id="compose_create_followup">
                                    <label class="form-check-label" for="compose_create_followup">
                                        Create a follow-up task
                                    </label>
                                </div>
                            </div>

                            <div id="compose_followup_section" style="display:none">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="col-form-label">Task <span class="text-danger">*</span></label>
                                            <select class="form-select" id="compose_followup_type" name="followup_type">
                                                <option value="call">Call</option>
                                                <option value="meeting">Meeting</option>
                                                <option value="email">Email</option>
                                                <option value="task">Task</option>
                                                <option value="whatsapp">WhatsApp</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-form-label">Task Date <span class="text-danger">*</span></label>
                                        <div class="mb-3 icon-form">
                                            <span class="form-icon"><i class="ti ti-calendar-check"></i></span>
                                            <input type="text" class="MyDateTimePicker form-control" id="compose_followup_date" name="followup_date" placeholder="MM/DD/YYYY hh:mm AM/PM" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="col-form-label">Task Assign To</label>
                                            <select class="form-select" id="compose_followup_assigned_to" name="followup_assigned_to">
                                                <option value="">Select User</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" {{ Auth::id() == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label class="col-form-label">Remarks</label>
                                            <textarea class="form-control" rows="4" id="compose_followup_remarks" name="followup_remarks" placeholder="Enter remarks"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<div class="text-end">
								<button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
								<button type="submit" class="btn btn-primary" id="btnSendEmail">
                                    <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                                    Send Email
                                </button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Compose Email Modal -->
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
		<!-- Success Email -->
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
							<p>Email Account is configured with “<a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="016479606c716d64416479606c716d642f626e6c">[email&#160;protected]</a>”. Now you can access email.</p>
							<div class="col-lg-12 text-center modal-btn">
								<a href="leads-details.html" class="btn btn-primary">Go to email</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Success Email -->
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
						<form action="leads-details.html">
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
												<!-- <img src="assets/img/profiles/avatar-19.jpg" alt=""> -->
												<a href="#">Darlee Robertson</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<!-- <img src="assets/img/profiles/avatar-20.jpg" alt=""> -->
												<a href="#">Sharon Roy</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<!-- <img src="assets/img/profiles/avatar-21.jpg" alt=""> -->
												<a href="#">Vaughan Lewis</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<!-- <img src="assets/img/profiles/avatar-01.jpg" alt=""> -->
												<a href="#">Jessica Louise</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<!-- <img src="assets/img/profiles/avatar-16.jpg" alt=""> -->
												<a href="#">Carol Thomas</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<!-- <img src="assets/img/profiles/avatar-22.jpg" alt=""> -->
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
		<!-- Add Contact -->
		<div class="modal custom-modal fade" id="add_contact" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add Contact</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="deals-details.html">
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
												<!-- <img src="assets/img/profiles/avatar-19.jpg" alt=""> -->
												<a href="#">Darlee Robertson</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<!-- <img src="assets/img/profiles/avatar-20.jpg" alt=""> -->
												<a href="#">Sharon Roy</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<!-- <img src="assets/img/profiles/avatar-21.jpg" alt=""> -->
												<a href="#">Vaughan Lewis</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<!-- <img src="assets/img/profiles/avatar-01.jpg" alt=""> -->
												<a href="#">Jessica Louise</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<!-- <img src="assets/img/profiles/avatar-16.jpg" alt=""> -->
												<a href="#">Carol Thomas</a>
											</span>
										</label>
									</li>
									<li class="select-people-checkbox">
										<label class="checkboxs">
											<input type="checkbox">
											<span class="checkmarks"></span>
											<span class="people-profile">
												<!-- <img src="assets/img/profiles/avatar-22.jpg" alt=""> -->
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
@push('scripts')
<script>
    window.globalLeadsStatuses = @json($leadsStatus);
    window.globalLeadStatusId = null;
    window.globalLeadSubStatusId = null;
    window.currentLeadData = null; // Defined globally
    $(document).ready(function() {
        // Get lead_id from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const leadId = urlParams.get('lead_id') || @json($leadId ?? null);
        if (leadId) {
            // Fetch lead details via AJAX
            $.ajax({
                url: "{{ route('get.leads.details', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'GET',
                data: { lead_id: leadId },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.status && res.data && res.data.lead) {
                        window.currentLeadData = res.data; // Store for Quick Edit
                        window.globalLeadStatusId = res.data.lead.lead_status_id;
                        window.globalLeadSubStatusId = res.data.lead.lead_sub_status_id;
                        console.log('Global IDs set from details:', window.globalLeadStatusId, window.globalLeadSubStatusId);
                        populateLeadDetails(res.data.lead, res.data.custom_data, res.data.pipeline_stages);
                    } else {
                        console.error('Failed to load lead:', res.message || 'Unknown error');
                        showError('Failed to load lead details');
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching lead details:', xhr.responseText);
                }
            });
        }
        // Function to populate lead details dynamically
        function populateLeadDetails(lead, customData, pipelineStages) {
            // Render Pipeline Status
            if (pipelineStages && pipelineStages.length > 0) {
                 // User requested: Only show stages that are in lead_status_histories
                 // (Simplified sorting logic removed in favor of history-based ordering below)

                 // Support both snake_case and camelCase (depending on serialization)
                 let histories = lead.status_histories || lead.statusHistories || [];
                 
                 // If no history, but we have a lead_status_id, treat it as the "first" stage reached
                 if (histories.length === 0 && (lead.lead_status_id || (lead.lead_status && lead.lead_status.id))) {
                     histories = [{
                         new_status_id: lead.lead_status_id || lead.lead_status.id,
                         created_at: lead.created_at,
                         box_id: lead.box_id
                     }];
                 }

                 const historyStatusIds = histories.map(h => h.new_status_id || h.newStatusId);
                 
                 // Filter to only show reached stages and sort them chronologically by history
                 let displayStages = pipelineStages.filter(stage => historyStatusIds.includes(stage.id));
                 displayStages.sort((a, b) => {
                     const aReach = histories.find(h => (h.new_status_id || h.newStatusId) == a.id);
                     const bReach = histories.find(h => (h.new_status_id || h.newStatusId) == b.id);
                     return new Date(aReach ? aReach.created_at : 0) - new Date(bReach ? bReach.created_at : 0);
                 });

                 let pipelineHtml = '<ul>';
                 displayStages.forEach(function(stage, index) {
                      let colorClass = 'bg-light';
                      let activeStyle = '';
                      const name = (stage.name || '').toLowerCase();
                      
                      // Priority for database-defined color
                      if (stage.color) {
                          activeStyle = `background-color: ${stage.color} !important; border-color: ${stage.color} !important;`;
                          colorClass = ''; 
                      } else {
                          if (name.includes('not contacted') || name.includes('new')) colorClass = 'bg-pending';
                          else if (name.includes('contacted')) colorClass = 'bg-info';
                          else if (name.includes('closed') || name.includes('won')) colorClass = 'bg-success';
                          else if (name.includes('lost')) colorClass = 'bg-danger';
                          else colorClass = 'bg-primary';
                      }

                      let checkIcon = '';
                      let isCurrent = (lead.lead_status_id == stage.id || (lead.lead_status && lead.lead_status.id == stage.id));
                      let isVisited = historyStatusIds.includes(stage.id);
                      
                      // Metadata for tooltips
                      let tooltipTitle = stage.name;
                      const reachPoint = histories.filter(h => (h.new_status_id || h.newStatusId) == stage.id).sort((a,b) => new Date(b.created_at) - new Date(a.created_at))[0];
                      
                      if (reachPoint) {
                          const date = new Date(reachPoint.created_at).toLocaleDateString('en-IN', {
                              day: 'numeric', month: 'short', year: 'numeric',
                              hour: '2-digit', minute: '2-digit'
                          });
                          tooltipTitle += ` (Reached: ${date})`;
                      }

                      let liClass = '';
                      if (isCurrent) {
                          liClass = 'active';
                          checkIcon = '<i class="ti ti-loader me-1 fa-spin"></i>'; 
                      } else if (isVisited) {
                          liClass = 'visited';
                          checkIcon = '<i class="ti ti-check me-1"></i>'; 
                      } else {
                          let initials = (stage.name || 'U').charAt(0).toUpperCase();
                          checkIcon = `<span class="me-1 fw-bold" style="background: rgba(255,255,255,0.25); color: #fff; border-radius: 50%; min-width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; font-size: 9px;">${initials}</span>`;
                      }

                      pipelineHtml += `<li class="${liClass}" data-bs-toggle="tooltip" title="${tooltipTitle}">
                         <a href="javascript:void(0);" class="${colorClass}" style="${activeStyle}">${checkIcon}${stage.name}</a>
                      </li>`;
                 });
                 pipelineHtml += '</ul>';
                 $('.pipeline-list').html(pipelineHtml);
                 
                 // Initialize tooltips for the new elements
                 const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                 tooltipTriggerList.map(function (tooltipTriggerEl) {
                     return new bootstrap.Tooltip(tooltipTriggerEl);
                 });
            }
            customData = customData || {};

            // Helper function to format field names
            function formatFieldName(name) {
                return name.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
            }

            // Helper function to get value or return null (don't update if null)
            function getValue(value) {
                if (value === null || value === undefined || value === '') {
                    return null;
                }
                return value;
            }

            // Helper function to get initials from name (first letter only)
            function getInitials(name) {
                if (!name) return '--';
                const trimmedName = name.trim();
                if (trimmedName.length > 0) {
                    return trimmedName.charAt(0).toUpperCase();
                }
                return '--';
            }

            // Helper function to get color from name (first letter)
            function getColorFromName(name) {
                if (!name) return '#f44336';
                const letter = name.trim().charAt(0).toUpperCase();
                const colors = [
                    '#f44336', '#e91e63', '#9c27b0', '#673ab7', '#3f51b5',
                    '#2196f3', '#03a9f4', '#00bcd4', '#009688', '#4caf50',
                    '#8bc34a', '#cddc39', '#ffc107', '#ff9800', '#ff5722'
                ];
                return colors[letter.charCodeAt(0) % colors.length];
            }

            // Update header section - only if data exists
            if (lead.name || lead.last_name) {
                const fullName = (lead.name || '') + (lead.last_name ? ' ' + lead.last_name : '');
                $('#lead-breadcrumb-name').text(fullName);
                $('.page-title').text('Lead: ' + fullName);
                const initials = getInitials(fullName);
                $('#lead-avatar-initials').text(initials)
                    .css('background-color', getColorFromName(fullName))
                    .css('color', '#fff')
                    .css('width', '100%')
                    .css('height', '100%')
                    .css('display', 'flex')
                    .css('align-items', 'center')
                    .css('justify-content', 'center')
                    .css('border-radius', '50%');
                $('#lead-name-header').html(fullName + ' <span class="star-icon"><i class="fa-solid fa-star"></i></span>');
            }

            if (lead.company_name) {
                $('#lead-company').html('<i class="ti ti-building me-1"></i>' + lead.company_name);
            }

            // Build location string - only if data exists
            const locationParts = [];
            if (lead.city && lead.city.name) locationParts.push(lead.city.name);
            if (lead.state && lead.state.name) locationParts.push(lead.state.name);
            if (lead.country && lead.country.name) locationParts.push(lead.country.name);
            if (locationParts.length > 0) {
                $('#lead-location').html('<i class="ti ti-map-pin-pin me-1"></i>' + locationParts.join(', '));
            }
            // Update Lead Information sidebar - only update items that have data
            const infoList = $('#lead-info-list');
            const existingItems = infoList.find('li');
            existingItems.each(function() {
                const $item = $(this);
                const labelSpan = $item.find('span.col-6').first();
                const valueSpan = $item.find('span.col-6').last();
                const label = labelSpan.text().trim();
                let value = null;
                if (label === 'Date Created' && lead.formatted_created_at) {
                    value = lead.formatted_created_at;
                } else if (label === 'Value' && lead.amount !== null && lead.amount !== undefined) {
                    const currency = '{!! \App\Models\Box::where("slug", request()->route("box_slug"))->value("currency_symbol") ?? "₹" !!}';
                    value = currency + ' ' + parseFloat(lead.amount).toLocaleString();
                } else if (label === 'Total Received' && lead.receipts_sum_amount !== null && lead.receipts_sum_amount !== undefined) {
                    const currency = '{!! \App\Models\Box::where("slug", request()->route("box_slug"))->value("currency_symbol") ?? "₹" !!}';
                    value = currency + ' ' + parseFloat(lead.receipts_sum_amount).toLocaleString();
                } else if (label === 'Last Received' && lead.latest_receipt && lead.latest_receipt.amount !== null && lead.latest_receipt.amount !== undefined) {
                    const currency = '{!! \App\Models\Box::where("slug", request()->route("box_slug"))->value("currency_symbol") ?? "₹" !!}';
                    value = currency + ' ' + parseFloat(lead.latest_receipt.amount).toLocaleString();
                } else if (label === 'Lead Type' && lead.lead_type) {
                    value = lead.lead_type;
                } else if (label === 'Source') {
                    if (lead.lead_source && lead.lead_source.name) {
                        value = lead.lead_source.name;
                    } else if (lead.lead_source_id) {
                        value = 'Source ID: ' + lead.lead_source_id;
                    }
                } else if (label === 'Status') {
                    if (lead.lead_status && lead.lead_status.name) {
                        value = lead.lead_status.name;
                    } else if (lead.lead_status_id) {
                        value = 'Status ID: ' + lead.lead_status_id;
                    }
                } else if (label === 'Lead Sub Status') {
                    if (lead.lead_sub_status && lead.lead_sub_status.name) {
                        value = lead.lead_sub_status.name;
                    } else if (lead.lead_sub_status_id) {
                        value = 'Sub Status ID: ' + lead.lead_sub_status_id;
                    } else {
                        value = '--';
                    }
                }
                if (value !== null) {
                    valueSpan.text(value);
                }
            });

            // Update Assignees
            const assigneesList = $('#lead-assignees-list');
            assigneesList.empty();
            let assigneesToProcess = [];
            
            if (lead.assignees && Array.isArray(lead.assignees) && lead.assignees.length > 0) {
                assigneesToProcess = lead.assignees;
            } else if (lead.assigned_to) {
                assigneesToProcess = [{
                    id: lead.assigned_to,
                    name: lead.assigned_to_name || (lead.assigned_to ? lead.assigned_to.name : null) || 'User ID: ' + lead.assigned_to,
                    profile_image_path: lead.assigned_to_image || (lead.assigned_to ? lead.assigned_to.profile_image_path : null) || null
                }];
            }

            if (assigneesToProcess.length > 0) {
                assigneesToProcess.forEach(function(member) {
                    const memberName = member.name || 'Unknown';
                    const memberPrefix = memberName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
                    const colors = ['#7367F0', '#28C76F', '#EA5455', '#FF9F43', '#00CFDD', '#4B4B46', '#1E90FF', '#FF1493', '#FFD700', '#8A2BE2'];
                    const hash = memberName.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0);
                    const bgColor = colors[hash % colors.length];
                    const memberImage = member.profile_image_path || member.image || member.avatar || null;
                    
                    let avatarHtml = '';
                    if (memberImage) {
                        avatarHtml = `<img src="/${memberImage.replace(/^\//,'')}" alt="Image" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
                    } else {
                        avatarHtml = `<span class="text-white fw-bold">${memberPrefix}</span>`;
                    }

                    const html = `
                        <h6 class="mb-3 fw-semibold">Assigned To - ${member.department || '--'}</h6>
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-md me-2" style="display: flex; align-items: center; justify-content: center; background-color: ${bgColor}; border-radius: 50%; min-width: 45px; height: 45px;">
                                ${avatarHtml}
                            </div>
                            <div>
                                <p class="mb-0 fs-16 fw-medium text-dark">${memberName}</p>
                                ${member.designation ? `<p class="mb-0 text-muted fs-12">${member.designation}</p>` : ''}
                            </div>
                        </div>
                    `;
                    assigneesList.append(html);
                });
            } else {
                assigneesList.append('<p class="text-muted">Unassigned</p>');
            }

            // Update Tags - only if tags exist
            if (lead.tags) {
                const tagsContainer = $('#lead-tags');
                tagsContainer.empty();
                const tags = lead.tags.split(',').map(function(tag) { return tag.trim(); });
                tags.forEach(function(tag) {
                    if (tag) {
                        tagsContainer.append('<a href="javascript:void(0);" class="badge badge-soft-success fw-medium me-2">' + tag + '</a>');
                    }
                });
            }

            // Update Contact Information - only update items that have data
            const contactList = $('#lead-contact-info');
            const contactItems = contactList.find('li');

            contactItems.each(function() {
                const $item = $(this);
                const labelSpan = $item.find('span.col-6').first();
                const valueSpan = $item.find('span.col-6').last();
                const label = labelSpan.text().trim();

                let value = null;

                if (label === 'Email' && lead.email) {
                    value = '<a href="mailto:' + lead.email + '">' + lead.email + '</a>';
                } else if (label === 'Phone' && lead.phone) {
                    var boxSlug = "{{ request()->route('box_slug') }}";
                    var whatsappUrl = "/" + boxSlug + "/whatsapp/chats/" + lead.id;
                    value = '<a href="tel:' + lead.phone + '">' + lead.phone + '</a>' + 
                            ' <a href="' + whatsappUrl + '" class="ms-1 text-success" title="Chat on WhatsApp"><i class="ti ti-brand-whatsapp fs-16"></i></a>';
                } else if (label === 'Phone Type' && lead.phone_type) {
                    value = lead.phone_type;
                } else if (label === 'Industry') {
                    if (lead.industry_name) {
                        value = lead.industry_name;
                    } else if (lead.industry) {
                        value = lead.industry;
                    }
                } else if (label === 'Country' && lead.country && lead.country.name) {
                    value = lead.country.name;
                } else if (label === 'State' && lead.state && lead.state.name) {
                    value = lead.state.name;
                } else if (label === 'City' && lead.city && lead.city.name) {
                    value = lead.city.name;
                }

                if (value !== null) {
                    valueSpan.html(value);
                }
            });

            // Update Custom Fields - only if custom data exists
            const customFieldsList = $('#lead-custom-fields');
            const customFieldsSection = $('#custom-fields-section');
            const customKeys = Object.keys(customData);
            let hasValidCustomField = false;
            
            if (customKeys.length > 0) {
                customFieldsList.empty();
                customKeys.forEach(function(key) {
                    const value = customData[key];
                    if (value !== null && value !== undefined && value !== '') {
                        customFieldsList.append('<li class="row mb-3"><span class="col-6">' + formatFieldName(key) + '</span><span class="col-6">' + value + '</span></li>');
                        hasValidCustomField = true;
                    }
                });
            }
            
            if (hasValidCustomField) {
                customFieldsSection.show();
            } else {
                customFieldsSection.hide();
            }

            // Update Working Locations
            const workingLocsList = $('#lead-working-locations');
            if (lead.division_areas && lead.division_areas.length > 0) {
                workingLocsList.empty();
                lead.division_areas.forEach(function(da) {
                    let stateName = da.state ? da.state.name : '';
                    let cityName = da.city ? da.city.name : '';
                    let locName = '';
                    if (stateName && cityName) {
                        locName = stateName + ' - ' + cityName;
                    } else if (stateName || cityName) {
                        locName = stateName || cityName;
                    } else {
                        locName = 'Unknown Location';
                    }

                    let companyName = (da.division && da.division.company) ? da.division.company.name : '';
                    let divisionName = da.division ? da.division.name : 'No Division';
                    
                    let companyDivName = '';
                    if (companyName && divisionName !== 'No Division') {
                        companyDivName = companyName + ' (' + divisionName + ')';
                    } else if (divisionName !== 'No Division') {
                        companyDivName = divisionName;
                    } else if (companyName) {
                        companyDivName = companyName;
                    } else {
                        companyDivName = 'No Division/Company';
                    }

                    workingLocsList.append('<li class="row mb-3"><span class="col-6 text-muted">' + companyDivName + '</span><span class="col-6">' + locName + '</span></li>');
                });
            } else {
                workingLocsList.html('<li class="text-muted">No working locations</li>');
            }

            // Update Last Modified - only if data exists
            if (lead.formatted_updated_at) {
                $('#lead-last-modified').text(lead.formatted_updated_at);
            }

            // Update Modified By - only if data exists
            if (lead.last_updated_by) {
                let modifiedByName = '';
                let modifiedByImage = null;

                if (lead.updated_by_name) {
                    modifiedByName = lead.updated_by_name.trim();
                    modifiedByImage = lead.updated_by_image || null;
                } else if (lead.updated_by && lead.updated_by.name) {
                    modifiedByName = lead.updated_by.name;
                    modifiedByImage = lead.updated_by.image || lead.updated_by.avatar || lead.updated_by.profile_picture || null;
                } else {
                    modifiedByName = 'User ID: ' + lead.last_updated_by;
                }

                if (modifiedByName) {
                    $('#lead-modified-name').text(modifiedByName);

                    // Set avatar - image or initials
                    const modifiedImg = $('#lead-modified-image');
                    const modifiedInitials = $('#lead-modified-initials');

                    if (modifiedByImage) {
                        modifiedImg.attr('src', modifiedByImage).show();
                        modifiedInitials.hide();
                        modifiedImg.on('error', function() {
                            $(this).hide();
                            const initials = getInitials(modifiedByName);
                            modifiedInitials.text(initials)
                                .css('background-color', getColorFromName(modifiedByName))
                                .css('color', '#fff')
                                .css('display', 'flex') // Ensure flex is used for centering
                                .show();
                        });
                    } else {
                        modifiedImg.hide();
                        const initials = getInitials(modifiedByName);
                        modifiedInitials.text(initials)
                            .css('background-color', getColorFromName(modifiedByName))
                            .css('color', '#fff')
                            .css('display', 'flex') // Ensure flex is used for centering
                            .show();
                    }
                }
            }

            // Update visibility badge - only if data exists
            if (lead.visibility) {
                const visibilityBadge = $('.badge-light');
                if (lead.visibility.toLowerCase() === 'private') {
                    visibilityBadge.html('<i class="ti ti-lock"></i> Private');
                } else {
                    visibilityBadge.html('<i class="ti ti-world"></i> ' + lead.visibility);
                }
            }

            // Update status dropdown - only if data exists
            if (lead.status) {
                const statusDropdown = $('.bg-success');
                statusDropdown.text(lead.status);
            }
        }

        // Function to show error message
        function showError(message) {
            showToast(message, 'danger'); // Replaced alert with toast
        }

        // Store current lead data globally
        // window.currentLeadData is defined at the top

        // Handle Quick Edit button click
        $(document).on('click', '[data-bs-target="#offcanvas_quick_edit"]', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const leadId = urlParams.get('lead_id') || @json($leadId ?? null);

            if (leadId) {
                ensureEditFormLoaded(function() {
                    // Fetch lead details if not already loaded
                    if (!currentLeadData) {
                        $.ajax({
                            url: "{{ route('get.leads.details', ['box_slug' => request()->route('box_slug')]) }}",
                            type: 'GET',
                            data: { lead_id: leadId },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                if (res.status && res.data && res.data.lead) {
                                    window.currentLeadData = res.data;
                                    populateQuickEditForm(res.data);
                                } else {
                                    showError('Failed to load lead details');
                                }
                            },
                            error: function(xhr) {
                                console.error('Error fetching lead details:', xhr.responseText);
                                showError('Error fetching lead details');
                            }
                        });
                    } else {
                        populateQuickEditForm(currentLeadData);
                    }
                });
            }
        });

        // Function to populate Quick Edit form (similar to populateEditForm from leadslist)
        function populateQuickEditForm(data) {
            const lead = data.lead || {};
            const custom = data.custom_data || {};
            const form = document.getElementById('quickEditForm');
            if (!form) return;

            // Field mappings: form field name => database column name
            const fieldMappings = {
                'lead_name': 'name',
                'name': 'name',
                'company_name': 'company_name',
                'email': 'email',
                'phone': 'phone',
                'lead_type': 'lead_type',
                'amount': 'amount',
                'amount': 'amount',
                'source': 'lead_source_id',
                'lead_source_id': 'lead_source_id',
                'lead_status': 'lead_status_id',
                'lead_status_id': 'lead_status_id',
                'lead_sub_status_id': 'lead_sub_status_id',
                'owner_id': 'assigned_to',
                'assigned_to': 'assigned_to',
                'country_id': 'country_id',
                'state_id': 'state_id',
                'city_id': 'city_id'
            };

            // Reverse mappings: database column => form field name
            const reverseMappings = {};
            Object.keys(fieldMappings).forEach(function(k) {
                reverseMappings[fieldMappings[k]] = k;
            });

            // Hidden ID field
            if (lead.id) {
                let idField = form.querySelector('[name="id"]');
                if (!idField) {
                    idField = document.createElement('input');
                    idField.type = 'hidden';
                    idField.name = 'id';
                    form.appendChild(idField);
                }
                idField.value = lead.id;
            }

            // Helper function to set select field value with retry logic
            function setSelectValue(fieldName, value, maxAttempts) {
                maxAttempts = maxAttempts || 10;
                if (!value && value !== 0) return; // Skip if no value

                let field = form.querySelector('[name="' + fieldName + '"]');
                if (!field) {
                    field = form.querySelector('[name="' + fieldName + '[]"]');
                }
                if (!field || field.tagName.toLowerCase() !== 'select') return;

                let attempts = 0;

                const trySetSelect = function() {
                    attempts++;

                    // Check if select has options loaded
                    if (field.options && field.options.length > 0) {
                        // Try to find matching option (check both string and number)
                        let found = false;
                        let matchingIndex = -1;
                        
                        // If value is array, check against first item for data attributes
                        let searchVal = Array.isArray(value) ? value[0] : value;
                        let searchString = String(searchVal);
                        let searchNum = Number(searchVal);

                        for (let i = 0; i < field.options.length; i++) {
                            const optValue = field.options[i].value;
                            // Match as string or number
                            if (optValue === searchString || optValue === searchVal ||
                                Number(optValue) === searchNum || optValue === String(searchNum)) {
                                matchingIndex = i;
                                found = true;
                                break;
                            }
                        }

                        if (found && matchingIndex >= 0) {
                            const selectedOption = field.options[matchingIndex];
                            // Set the value
                            field.selectedIndex = matchingIndex;
                            field.value = selectedOption.value;

                            // Also use jQuery
                            let valToSet = Array.isArray(value) ? value : selectedOption.value;
                            if (field.multiple && !Array.isArray(valToSet)) {
                                valToSet = [valToSet];
                            }
                            $(field).val(valToSet).trigger('change');

                            // Auto-populate Department and Designation if they exist
                            if (fieldName === 'assigned_to' || fieldName === 'owner_id') {
                                const deptId = selectedOption.getAttribute('data-department-id');
                                const desigId = selectedOption.getAttribute('data-designation-id');
                                if (deptId) {
                                    const $deptSel = $('#edit_department_' + fieldName);
                                    console.log('Setting department:', deptId, 'Options count:', $deptSel.find('option').length);
                                    if ($deptSel.find('option').length <= 1) {
                                        $deptSel.attr('data-pre-val', deptId);
                                    }
                                    $deptSel.val(deptId).trigger('change', ['skip-clear']);
                                    $deptSel.val(deptId).trigger('change', ['skip-clear']);
                                }
                                if (desigId) {
                                    $('#edit_designation_' + fieldName).val(desigId);
                                    $('#edit_designation_visible_' + fieldName).val('ID: ' + desigId);
                                }
                            }

                            // Verify it was set
                            if (field.value === selectedOption.value) {
                                return true; // Success
                            }
                        }
                    }

                    // If not set yet and haven't exceeded max attempts, try again
                    if (attempts < maxAttempts) {
                        setTimeout(trySetSelect, 100);
                    }
                    return false;
                };

                // Start trying after a short delay
                setTimeout(trySetSelect, 50);
            }

            // Helper function to set field value based on field type
            function setFieldValue(field, value) {
                if (!field || ((value === null || value === undefined || value === '') && value !== 0)) return;

                const tag = field.tagName ? field.tagName.toLowerCase() : '';
                const type = field.type ? field.type.toLowerCase() : '';

                if (tag === 'select') {
                    // Convert value to string for comparison
                    const stringValue = String(value);
                    // Set the value
                    field.value = stringValue;
                    // Also use jQuery to ensure it's set
                    $(field).val(stringValue).trigger('change');
                    // Double-check the value was set
                    if (field.value !== stringValue) {
                        // Try setting each option
                        for (let i = 0; i < field.options.length; i++) {
                            if (field.options[i].value === stringValue) {
                                field.selectedIndex = i;
                                break;
                            }
                        }
                    }
                } else if (tag === 'textarea') {
                    field.value = value || '';
                } else if (type === 'radio') {
                    const radio = form.querySelector('[name="' + field.name + '"][value="' + value + '"]');
                    if (radio) {
                        radio.checked = true;
                    }
                } else if (type === 'checkbox') {
                    field.checked = (value == 1 || value === true || value === '1' || value === 'true');
                } else {
                    field.value = value || '';
                }
            }

            // Populate standard mapped fields - use retry logic for all select fields
            Object.keys(fieldMappings).forEach(function(formField) {
                const dbField = fieldMappings[formField];
                let fieldValue = lead[dbField];

                // Special handling for assigned_to - extract ID if it's an object
                if (dbField === 'assigned_to') {
                    if (fieldValue && typeof fieldValue === 'object' && fieldValue.id !== undefined) {
                        // assigned_to is an object with id property
                        fieldValue = fieldValue.id;
                    } else if (fieldValue && typeof fieldValue === 'object' && fieldValue !== null) {
                        // Try to get id from object (handle different structures)
                        fieldValue = fieldValue.id || fieldValue.user_id || null;
                    }
                    // If it's already a number/string, use it as is
                    
                    // Fallback to currently logged in user if empty
                    if (!fieldValue) {
                        fieldValue = "{{ auth()->id() }}";
                    }
                }

                if (fieldValue !== null && fieldValue !== undefined && fieldValue !== '') {
                    let field = form.querySelector('[name="' + formField + '"]');
                    if (!field) {
                        field = form.querySelector('[name="' + formField + '[]"]');
                    }
                    if (field) {
                        if (field.tagName && field.tagName.toLowerCase() === 'select') {
                            // Use retry logic for select fields
                            setSelectValue(formField, fieldValue);
                        } else {
                            // Use regular setFieldValue for other field types
                            setFieldValue(field, fieldValue);
                        }
                    }
                }
            });

            // Populate system fields that are directly in leads table
            const systemFields = ['tags', 'description', 'visibility', 'status', 'last_name', 'currency', 'phone_type', 'industry'];
            systemFields.forEach(function(f) {
                if (lead[f] !== null && lead[f] !== undefined && lead[f] !== '') {
                    const field = form.querySelector('[name="' + f + '"]');
                    if (field) {
                        if (field.tagName && field.tagName.toLowerCase() === 'select') {
                            // Use retry logic for select fields
                            setSelectValue(f, lead[f]);
                        } else {
                            // Use regular setFieldValue for other field types
                            setFieldValue(field, lead[f]);
                        }
                    }
                }
            });

            // Handle dependent dropdowns (country → state → city)
            const loadDependentData = function (parentField, parentId, dependentFieldName, callback) {
                if (!parentId || !dependentFieldName) {
                    if (callback) callback();
                    return;
                }
                $.ajax({
                    url: "{{ route('gets.depenedent', ['box_slug' => request()->route('box_slug')]) }}",
                    method: "GET",
                    data: {
                        parent_field: parentField,
                        parent_id: parentId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        const depField = form.querySelector('[name="' + dependentFieldName + '"]');
                        if (depField && res.data) {
                            $(depField).empty().append('<option value="">Choose</option>');
                            $.each(res.data, function(k, v) {
                                $(depField).append('<option value="' + v.id + '">' + v.name + '</option>');
                            });
                            // Set the value after populating options using retry logic
                            if (dependentFieldName === 'state_id' && lead.state_id) {
                                setSelectValue('state_id', lead.state_id);
                            } else if (dependentFieldName === 'city_id' && lead.city_id) {
                                setSelectValue('city_id', lead.city_id);
                            }
                        }
                        if (callback) callback();
                    },
                    error: function() {
                        if (callback) callback();
                    }
                });
            };

            // Load dependent dropdowns in sequence
            if (lead.country_id) {
                setSelectValue('country_id', lead.country_id, 15);
                // Wait a bit for country to be set, then load state
                setTimeout(function() {
                    loadDependentData('country_id', lead.country_id, 'state_id', function() {
                        if (lead.state_id) {
                            setSelectValue('state_id', lead.state_id, 15);
                            // Wait a bit for state to be set, then load city
                            setTimeout(function() {
                                loadDependentData('state_id', lead.state_id, 'city_id', function() {
                                    if (lead.city_id) {
                                        setSelectValue('city_id', lead.city_id, 15);
                                    }
                                });
                            }, 200);
                        }
                    });
                }, 200);
            } else {
                // If no country_id, still try to set state and city if they exist
                if (lead.state_id) {
                    setSelectValue('state_id', lead.state_id);
                }
                if (lead.city_id) {
                    setSelectValue('city_id', lead.city_id);
                }
            }

            // Populate custom fields from custom_data
            Object.keys(custom).forEach(function(key) {
                if (custom[key] === null || custom[key] === undefined) return;

                // Try to find field by exact name first
                let field = form.querySelector('[name="' + key + '"]');

                // If not found, try with array notation (for checkboxes)
                if (!field) {
                    field = form.querySelector('[name="' + key + '[]"]');
                }

                if (field) {
                    const fieldType = field.type ? field.type.toLowerCase() : '';
                    const fieldTag = field.tagName ? field.tagName.toLowerCase() : '';

                    // Handle checkbox arrays (multiple values)
                    if (fieldType === 'checkbox' && Array.isArray(custom[key])) {
                        custom[key].forEach(function(val) {
                            const checkbox = form.querySelector('[name="' + key + '[]"][value="' + val + '"]');
                            if (checkbox) {
                                checkbox.checked = true;
                            }
                        });
                    } else {
                        // Check if it's a select field and use retry logic
                        if (field.tagName && field.tagName.toLowerCase() === 'select') {
                            setSelectValue(key, custom[key]);
                        } else {
                            setFieldValue(field, custom[key]);
                        }
                    }
                }
            });

            // Populate any remaining lead fields that might not be mapped
            Object.keys(lead).forEach(function(key) {
                // Skip already handled fields
                if (fieldMappings[key] || systemFields.indexOf(key) !== -1) return;
                // Skip relationship objects and formatted dates
                if (typeof lead[key] === 'object' && lead[key] !== null) return;
                if (key.includes('_at') && key.startsWith('formatted_')) return;
                if (key === 'id' || key === 'box_id' || key === 'created_at' || key === 'updated_at') return;

                // Try to find and populate the field
                const field = form.querySelector('[name="' + key + '"]');
                if (field) {
                    if (field.tagName && field.tagName.toLowerCase() === 'select') {
                        // Use retry logic for select fields
                        setSelectValue(key, lead[key]);
                    } else {
                        setFieldValue(field, lead[key]);
                    }
                }
            });

            // Reset the populating flag after all dependents have loaded
            setTimeout(() => {
                window.isQuickEditPopulating = false;
            }, 1500);
        }

        // Handle Quick Edit form submission
        $('#quickEditForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="ti ti-loader"></i> Updating...');

            // Sanitize numeric fields - remove empty strings for numeric fields
            const numericFields = ['value', 'amount', 'assigned_to', 'country_id', 'state_id', 'city_id'];
            numericFields.forEach(field => {
                if (formData.has(field) && formData.get(field) === '') {
                    formData.delete(field);
                }
            });

            $.ajax({
                url: "{{ route('leads.update', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                        // Close offcanvas
                        const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvas_quick_edit'));
                        if (offcanvas) {
                            offcanvas.hide();
                        }

                        // Reload lead details
                        const urlParams = new URLSearchParams(window.location.search);
                        const leadId = urlParams.get('lead_id');
                        if (leadId) {
                            $.ajax({
                                url: "{{ route('get.leads.details', ['box_slug' => request()->route('box_slug')]) }}",
                                type: 'GET',
                                data: { lead_id: leadId },
                                success: function(res) {
                                    if (res.status && res.data && res.data.lead) {
                                        currentLeadData = res.data;
                                        populateLeadDetails(res.data.lead, res.data.custom_data, res.data.pipeline_stages);
                                        
                                        // Refresh activities and notes to show history of changes
                                        if (typeof loadActivitiesForLead === 'function') {
                                            loadActivitiesForLead('all', '#activities-container');
                                        }
                                        if (typeof loadNotesForLead === 'function') {
                                            loadNotesForLead('#notes-container');
                                        }
                                        
                                        // Show success message
                                        showToast('Lead updated successfully!', 'success');
                                    }
                                }
                            });
                        } else {
                            showToast('Lead updated successfully!', 'success');
                        }
                    } else {
                        showError(response.message || 'Failed to update lead');
                    }
                    submitBtn.prop('disabled', false).html(originalText);
                },
                error: function(xhr) {
                    // Clear previous error styles
                    $('#quickEditForm .is-invalid').removeClass('is-invalid');
                    $('#quickEditForm .invalid-feedback').remove();

                    let errorMsg = 'Failed to update lead';
                    
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        let errorList = [];
                        
                        $.each(errors, function(field, messages) {
                            errorList.push(messages[0]);
                            
                            // Highlight the field in the form
                            let input = $('#quickEditForm').find('[name="' + field + '"]');
                            if (!input.length) {
                                // For custom fields they might be named like custom_fields[vegitarian]
                                input = $('#quickEditForm').find('[name="custom_fields[' + field + ']"]');
                            }
                            
                            if (input.length) {
                                input.addClass('is-invalid');
                                input.after('<div class="invalid-feedback d-block">' + messages[0] + '</div>');
                            }
                        });
                        
                        errorMsg = errorList.join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = JSON.stringify(xhr.responseJSON.errors);
                    }
                    
                    showError(errorMsg);
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Handle dependent dropdowns in Quick Edit form
        $(document).on('change', '#quickEditForm .dynamic-select', function() {
            if (window.isQuickEditPopulating) return; // Prevent race conditions on initial load

            const selectedId = $(this).val();
            const dependentField = $(this).data('dependent');
            const token = $('meta[name="csrf-csrf-token"]').attr('content');
            const $form = $(this).closest('form');

            if (dependentField && selectedId) {
                $.ajax({
                    url: "{{ route('gets.depenedent', ['box_slug' => request()->route('box_slug')]) }}",
                    method: "GET",
                    data: {
                        parent_field: $(this).attr('name').replace('[]', ''),
                        parent_id: selectedId,
                        _token: token
                    },
                    success: function (response) {
                        const $dependentSelect = $form.find('[name="' + dependentField + '"]');
                        $dependentSelect.empty().append('<option value="">Choose</option>');
                        if (response.data) {
                            $.each(response.data, function (key, value) {
                                $dependentSelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                        }
                    }
                });
            }
        });

        // Helper for Lead Sub Status population
        function populateActivitySubStatus(statusId, $subStatusSelect, currentSubStatusId = null) {
            console.log('Populating Sub Status for Status ID:', statusId, 'Targeting Select:', $subStatusSelect.attr('id'), 'Trying to pre-select:', currentSubStatusId);
            $subStatusSelect.empty().append('<option value="">-- Select Sub Status --</option>');
            
            const $wrapper = $subStatusSelect.closest('.col-md-6');
            
            if (statusId) {
                $.ajax({
                    url: "{{ route('get.lead.substatus.list', ['box_slug' => request()->route('box_slug')]) }}",
                    type: 'GET',
                    data: { lead_status_id: statusId },
                    success: function(response) {
                        console.log('Sub Status Response:', response);
                        if (response.status && response.data && response.data.length > 0) {
                            $wrapper.removeClass('d-none');
                            $.each(response.data, function(key, val) {
                                const isSelected = (currentSubStatusId && val.id == currentSubStatusId);
                                const selectedAttr = isSelected ? 'selected' : '';
                                $subStatusSelect.append('<option value="'+val.id+'" '+selectedAttr+'>'+val.name+'</option>');
                            });
                            if (currentSubStatusId) {
                                console.log('Manually setting Sub Status value to:', currentSubStatusId);
                                $subStatusSelect.val(currentSubStatusId).trigger('change');
                            }
                        } else {
                            $wrapper.addClass('d-none');
                        }
                    }
                });
            } else {
                 $wrapper.addClass('d-none');
            }
        }

        // Quick Edit Lead Sub Status Logic
        $(document).on('change', '#quickEditForm select[name="lead_status"], #quickEditForm select[name="lead_status_id"]', function() {
            const $form = $(this).closest('form');
            const $targetSelect = $form.find('[name="lead_sub_status_id"]');
            populateActivitySubStatus($(this).val(), $targetSelect, (typeof currentLeadData !== 'undefined' ? currentLeadData.lead.lead_sub_status_id : null));
        });

        function enforceReminderMandatory($dropdown, $checkbox) {
            let isMandatory = false;
            let statusId = $dropdown.val();
            
            // Check data attribute first
            let $selectedOption = $dropdown.find('option:selected');
            if ($selectedOption.length && $selectedOption.attr('data-reminder-mandatory') !== undefined) {
                let val = $selectedOption.attr('data-reminder-mandatory');
                isMandatory = (val == 'Yes' || val == 1 || val == '1');
            } 
            // Fallback to global statuses
            else if (window.globalLeadsStatuses && statusId) {
                const status = window.globalLeadsStatuses.find(s => s.id == statusId);
                if (status && (status.reminder_mandatory == 'Yes' || status.reminder_mandatory == 1)) {
                    isMandatory = true;
                }
            }

            console.log("Enforcing Reminder Mandatory for " + $dropdown.attr('id') + ": ", isMandatory);

            if (isMandatory) {
                $checkbox.prop('checked', true).trigger('change');
                
                // Explicitly show the section to ensure it is visible
                if ($checkbox.attr('id') === 'followuptask_checkbox') {
                    $('#followupTaskSection').slideDown();
                    $('#followup_task_type, #followup_task_date, #followup_task_assign_to').prop('required', true);
                } else if ($checkbox.attr('id') === 'edit_create_followup') {
                    $('#edit_followup_section').slideDown();
                } else if ($checkbox.attr('id') === 'mark_done_create_followup') {
                    $('#mark_done_followup_section').slideDown();
                }

                // Prevent unchecking
                $checkbox.attr('onclick', 'return false;').css('pointer-events', 'none').css('opacity', '0.6');
            } else {
                // Unlock the checkbox
                $checkbox.removeAttr('onclick').css('pointer-events', 'auto').css('opacity', '1');
                
                // Uncheck and hide the section automatically when switching to a non-mandatory status
                if ($checkbox.is(':checked')) {
                    $checkbox.prop('checked', false).trigger('change');
                }
                
                // Explicitly hide the section
                if ($checkbox.attr('id') === 'followuptask_checkbox') {
                    $('#followupTaskSection').slideUp();
                    $('#followup_task_type, #followup_task_date, #followup_task_assign_to').prop('required', false);
                } else if ($checkbox.attr('id') === 'edit_create_followup') {
                    $('#edit_followup_section').slideUp();
                } else if ($checkbox.attr('id') === 'mark_done_create_followup') {
                    $('#mark_done_followup_section').slideUp();
                }
            }
        }

        // Add Activity Lead Sub Status Logic
        $(document).on('change', '#activity_lead_status_id', function(e, subStatusId) {
            const statusId = $(this).val();
            populateActivitySubStatus(statusId, $('#activity_lead_sub_status_id'), subStatusId || null);
            enforceReminderMandatory($(this), $('#followuptask_checkbox'));
        });

        // Edit Activity Lead Sub Status Logic
        $(document).on('change', '#edit_activity_lead_status_id', function(e, subStatusId) {
            const statusId = $(this).val();
            populateActivitySubStatus(statusId, $('#edit_activity_lead_sub_status_id'), subStatusId || null);
            enforceReminderMandatory($(this), $('#edit_create_followup'));
        });

        // Mark Done Lead Status Logic
        $(document).on('change', '#mark_done_lead_status', function(e, subStatusId) {
            const statusId = $(this).val();
            populateActivitySubStatus(statusId, $('#mark_done_lead_sub_status'), subStatusId || null);
            enforceReminderMandatory($(this), $('#mark_done_create_followup'));
        });
    });

    // Activity Management
    let activityOptions = null;
    let currentLeadIdForActivity = @json($leadId ?? null);
    window.allLeadActivities = []; // Global variable to store all activities

    // Get lead ID from URL
    const urlParamsForActivity = new URLSearchParams(window.location.search);
    currentLeadIdForActivity = urlParamsForActivity.get('lead_id') || @json($leadId ?? null);

    // Reset Form when Offcanvas is hidden (Cancel/Close)
    $('#offcanvas_add').on('hidden.bs.offcanvas', function() {
        // Reset form
        $('#addActivityForm')[0].reset();
        $('#file-list').empty();

        // Reset conditional visibility
        $('.field-call, .field-meeting, .field-email, .field-whatsapp').hide();
        $('#followupTaskSection').hide();
        $('#followup_meeting_date_field').hide();
        $('#followuptask_checkbox').prop('checked', false).trigger('change');
        $('#followup_meeting_date').val('').prop('required', false);

        // Reset to default activity type (call)
        $('#type_call').prop('checked', true);

        // Clear Select2 values if any
        $('select').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
               // $(this).val('').trigger('change'); // Optional: Clear select2 too
            }
        });
    });

    // Load activity options when Add Activity offcanvas opens
    $('#offcanvas_add').on('shown.bs.offcanvas', function() {
        // Set current time for hidden activity_date input
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        $('#activity_date').val(now.toISOString().slice(0, 16));

        if (!currentLeadIdForActivity) {
            showToast('Lead ID not found. Please refresh the page.', 'danger');
            return;
        }
        // Ensure Assign To and Lead Status fields are visible before loading options
        $('#assigned_to_assigned_to').closest('.col-md-6').show();
        $('#activity_lead_status_id').closest('.col-md-6').show();

        // Add data-no-select2 attribute to prevent global Select2 initialization
        $('#assigned_to_assigned_to, #activity_lead_status_id').attr('data-no-select2', 'true');

        // Remove Select2 classes if they were already added
        if ($('#assigned_to_assigned_to').hasClass('select2-hidden-accessible')) {
             $('#assigned_to_assigned_to').select2('destroy');
        }
        if ($('#activity_lead_status_id').hasClass('select2-hidden-accessible')) {
             $('#activity_lead_status_id').select2('destroy');
        }

        // Auto-select radio button based on the currently active tab
        const activeTabTarget = $('.nav-link.active[data-bs-toggle="tab"]').attr('data-bs-target');
        if (activeTabTarget === '#calls') {
            $('#type_call').prop('checked', true);
        } else if (activeTabTarget === '#meetings') {
            $('#type_meeting').prop('checked', true);
        } else if (activeTabTarget === '#email') {
            $('#type_email').prop('checked', true);
        } else if (activeTabTarget === '#wapp') {
            $('#type_whatsapp').prop('checked', true);
        } else if (activeTabTarget === '#notes') {
            $('#type_note').prop('checked', true);
        } else {
            $('#type_call').prop('checked', true); // Default
        }

        // Set up initial visibility based on selected activity type
        handleActivityTypeChange();

        // Load options
        loadActivityOptions();
        loadActivityUsers();
        loadLeadStatuses();
    });

    // Select2 prevention removed

    // Observers removed as we want Select2 to modify classes

    // Start observing when offcanvas opens
    $('#offcanvas_add').on('show.bs.offcanvas', function() {
        // No observer needed as we want Select2 to be applied
    });

    // Stop observing when offcanvas closes
    $('#offcanvas_add').on('hide.bs.offcanvas', function() {
        // Observer removed
    });

    // Load activity options
    function loadActivityOptions() {
        $.ajax({
            url: "{{ route('activities.options', ['box_slug' => request()->route('box_slug')]) }}",
            type: 'GET',
            data: {
                lead_id: currentLeadIdForActivity
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                console.log('Activity options response:', res);
                console.log('Current lead data:', res.data?.current_lead);
                if (res.success && res.data) {
                    // Ensure fields are visible before populating
                    $('#assigned_to_assigned_to').closest('.col-md-6').show();
                    $('#activity_lead_status_id').closest('.col-md-6').show();
                    // Populate Activity Settings
                    const $activitySetting = $('#activity_setting_id');
                    const $followupTaskType = $('#followup_task_type');
                    const $markDoneFollowupType = $('#mark_done_followup_type');

                    // Destroy Select2 if already initialized
                    if ($activitySetting.hasClass('select2-hidden-accessible')) {
                        $activitySetting.select2('destroy');
                    }
                    if ($followupTaskType.hasClass('select2-hidden-accessible')) {
                        $followupTaskType.select2('destroy');
                    }
                    if ($markDoneFollowupType.hasClass('select2-hidden-accessible')) {
                        $markDoneFollowupType.select2('destroy');
                    }

                    $activitySetting.empty().append('<option value="">Select Activity</option>');
                    $followupTaskType.empty().append('<option value="">Select Task</option>');
                    $markDoneFollowupType.empty().append('<option value="">Select Task</option>');

                    if (res.data.activity_settings && res.data.activity_settings.length > 0) {
                        res.data.activity_settings.forEach(function(setting) {
                            // Filter out 'All' option if present
                            // Also filter out 'Task' from being shown in the list
                            if(setting.id !== 'all' && setting.title.toLowerCase() !== 'task') {
                                const option = '<option value="' + setting.id + '">' + setting.title + '</option>';
                                $activitySetting.append(option);
                                $followupTaskType.append(option);
                                $markDoneFollowupType.append(option);
                            }
                        });
                    } else {
                        $activitySetting.append('<option value="" disabled>No activity settings found</option>');
                        $followupTaskType.append('<option value="" disabled>No tasks found</option>');
                        $markDoneFollowupType.append('<option value="" disabled>No tasks found</option>');
                    }

                    // Reinitialize Select2
                    if ($activitySetting.hasClass('select')) {
                        $activitySetting.select2({
                            minimumResultsForSearch: -1,
                            width: '100%',
                        });
                    }
                    if ($followupTaskType.hasClass('select')) {
                        $followupTaskType.select2({
                            minimumResultsForSearch: -1,
                            width: '100%',
                        });
                    }
                    if ($markDoneFollowupType.hasClass('select')) {
                        $markDoneFollowupType.select2({
                            minimumResultsForSearch: -1,
                            width: '100%',
                        });
                    }

                    // Force sync with default radio button to ensure correct ID is selected
                    handleActivityTypeChange();
                }
            },
            error: function(xhr) {
                console.error('Error loading activity options:', xhr.responseText);
            }
        });
    }

    // Load Activity Users
    function loadActivityUsers() {
        $.ajax({
            url: "{{ route('activities.users', ['box_slug' => request()->route('box_slug')]) }}",
            type: 'GET',
            data: { lead_id: currentLeadIdForActivity },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                if (res.success && res.data) {
                    const $assignedTo = $('#assigned_to_assigned_to');
                    const $followupAssignTo = $('#followup_task_assign_to');
                    const $doneWith = $('#done_with');

                    // Clear and populate Assign To
                    $assignedTo.empty().append('<option value="">Select User</option>');

                    if (res.data.users && res.data.users.length > 0) {
                        res.data.users.forEach(function(user) {
                            if (user.id !== 'self') {
                                $assignedTo.append('<option value="' + user.id + '">' + user.name + '</option>');
                            }
                        });

                         // Populate Follow-up Assign To separately
                         $followupAssignTo.empty().append('<option value="">Select User</option>');
                         $doneWith.empty().append('<option value="">Select User</option>');
                         $doneWith.append('<option value="self">Self</option>');

                         res.data.users.forEach(function(user) {
                             if(user.id !== 'self') {
                                 const option = '<option value="' + user.id + '">' + user.name + '</option>';
                                 $followupAssignTo.append(option);
                                 $doneWith.append(option);
                             }
                        });
                    } else {
                        $assignedTo.append('<option value="" disabled>No users found</option>');
                        $followupAssignTo.append('<option value="" disabled>No users found</option>');
                    }

                    // Use timeout to ensure DOM is ready and any Select2 init doesn't override val
                    setTimeout(() => {
                        if (res.data.current_lead && res.data.current_lead.assigned_to) {
                             let assignedToVal = res.data.current_lead.assigned_to;
                             if (typeof assignedToVal === 'object' && assignedToVal !== null) {
                                 assignedToVal = assignedToVal.id || assignedToVal.user_id;
                             }
                             $assignedTo.val(assignedToVal);
                             if ($assignedTo.hasClass('select2-hidden-accessible')) {
                                 $assignedTo.trigger('change');
                             }
                        }

                        $followupAssignTo.val("{{ Auth::id() }}");
                        if ($followupAssignTo.hasClass('select2-hidden-accessible')) {
                             $followupAssignTo.trigger('change');
                        }
                    }, 100);

                    // Force destroy Select2 to ensure native select usage
                    $assignedTo.removeClass('select2-hidden-accessible');
                    $assignedTo.next('.select2-container').remove();
                    $assignedTo.show();

                    $followupAssignTo.removeClass('select2-hidden-accessible');
                    $followupAssignTo.next('.select2-container').remove();
                    $followupAssignTo.show();

                    $doneWith.removeClass('select2-hidden-accessible');
                    $doneWith.next('.select2-container').remove();
                    $doneWith.show();
                }
            }
        });
    }

    // Load Lead Statuses
    function loadLeadStatuses() {
        console.log('Loading Lead Statuses for Lead ID:', currentLeadIdForActivity);
        $.ajax({
            url: "{{ route('activities.lead_statuses', ['box_slug' => request()->route('box_slug')]) }}",
            type: 'GET',
            data: { lead_id: currentLeadIdForActivity },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                 console.log('Lead Statuses Response:', res);
                 if (res.success && res.data) {
                    const $leadStatus = $('#activity_lead_status_id');
                    console.log('Lead Status Element Length:', $leadStatus.length);

                    // Remove inline styles to allow standard select to function
                    $leadStatus.removeAttr('style');
                    $leadStatus.removeClass('select2-hidden-accessible');

                    // Clear and populate
                    $leadStatus.empty().append('<option value="">Select Lead Status</option>');

                    if (res.data.lead_statuses && res.data.lead_statuses.length > 0) {
                        console.log('Found ' + res.data.lead_statuses.length + ' statuses. Populating...');
                        res.data.lead_statuses.forEach(function(status) {
                            // Convert task_priority to string safely
                            let priorities = '[]';
                            if (status.task_priority) {
                                try {
                                    priorities = JSON.stringify(status.task_priority);
                                    // Escape single quotes for HTML attribute
                                    priorities = priorities.replace(/'/g, "&apos;");
                                } catch (e) {
                                    console.error('Error stringifying task priority', e);
                                }
                            }
                            $leadStatus.append(`<option value="${status.id}" data-priorities='${priorities}'>${status.name}</option>`);
                        });
                    } else {
                        console.warn('No lead statuses found in response data.');
                        $leadStatus.append('<option value="" disabled>No lead statuses found</option>');
                        if (res.data.debug) {
                            console.log('Debug info:', res.data.debug);
                        }
                    }

                    // Pre-select lead status
                    if (res.data.current_lead && res.data.current_lead.lead_status_id) {
                       console.log('Pre-selecting lead status:', res.data.current_lead.lead_status_id);
                       window.globalLeadStatusId = res.data.current_lead.lead_status_id;
                       window.globalLeadSubStatusId = res.data.current_lead.lead_sub_status_id;
                       $leadStatus.val(res.data.current_lead.lead_status_id).trigger('change', [res.data.current_lead.lead_sub_status_id]);
                    }

                    // Remove Select2 classes/attributes and ensure visibility
                    $leadStatus.removeClass('select2-hidden-accessible');
                    $leadStatus.next('.select2-container').remove();
                    $leadStatus.show();
                 } else {
                     console.error('Lead Status response was not successful or missing data.');
                 }
            },
            error: function(xhr) {
                console.error('Error loading lead statuses:', xhr.responseText);
            }
        });
    }


    // Handle activity type change for conditional field visibility
    function handleActivityTypeChange($form = null) {
        // Use provided form or fallback to #addActivityForm for initial load
        const $scope = $form || $('#addActivityForm');
        const selectedType = $scope.find('input[name="type"]:checked').val();

        console.log('Handling Type Change for form:', $scope.attr('id'), 'Selected Type:', selectedType);

        // Hide only fields within this scope
        $scope.find('.field-call, .field-meeting, .field-email, .field-whatsapp, .field-note, .field-general, .field-compose-email, .field-compose-whatsapp').hide();
        $scope.find('.field-call, .field-meeting, .field-email, .field-whatsapp, .field-note, .field-general, .field-compose-email, .field-compose-whatsapp').find('select, input').prop('required', false);

        // Reset Activity Setting and related fields within this scope
        const $settingSelect = $scope.find('select[name="activity_setting_id"]');
        const $typeSelect = $scope.find('select[name="activity_type"]');
        const $statusSelect = $scope.find('select[name="activity_status_id"]');
        const $leadStatusSelect = $scope.find('select[name="lead_status_id"]');

        // Auto-select matching Activity Setting based on selected type
        if (selectedType) {
            let targetTitle = selectedType.toLowerCase();
            let matchedValue = null;
            $settingSelect.find('option').each(function() {
                let optionText = $(this).text().trim().toLowerCase();
                if (optionText === targetTitle || optionText.includes(targetTitle) || targetTitle.includes(optionText)) {
                    matchedValue = $(this).val();
                }
            });
            if (matchedValue) {
                $settingSelect.val(matchedValue).trigger('change');
            }
        }

        // Show fields based on selected type
        if (selectedType === 'call' || selectedType === 'whatsapp') {
            $scope.find('.field-call').show();
            if (selectedType === 'whatsapp' && $scope.attr('id') === 'addActivityForm') {
                $scope.find('.field-compose-whatsapp').show();
            }
            $settingSelect.prop('required', true);
            $typeSelect.prop('required', true);
            $leadStatusSelect.prop('required', true);
        } else if (selectedType === 'meeting') {
            $scope.find('.field-meeting').show();
            $settingSelect.prop('required', true);
            $typeSelect.prop('required', true);
            $scope.find('input[name="activity_date"]').prop('required', true);
            $leadStatusSelect.prop('required', true);
        } else if (selectedType === 'email') {
            if ($scope.attr('id') === 'addActivityForm') {
                $scope.find('.field-compose-email').show();
                $scope.find('#add_from_email').prop('required', true);
                $scope.find('#add_to_email').prop('required', true);
                $scope.find('#add_subject').prop('required', true);
                
                if (!$scope.find('#add_body').next('.note-editor').length) {
                    var AttachButton = function (context) {
                        var ui = $.summernote.ui;
                        var button = ui.button({
                            contents: '<i class="ti ti-paperclip"></i> Attach',
                            tooltip: 'Attach File (Max 5MB)',
                            click: function () {
                                $('#summernote_file_input').data('target', '#add_body').click();
                            }
                        });
                        return button.render();
                    };

                    $scope.find('#add_body').summernote({
                        placeholder: 'Type your message here...',
                        height: 200,
                        toolbar: [
                            ['style', ['style']],
                            ['font', ['bold', 'italic', 'underline', 'clear']],
                            ['fontname', ['fontname']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['table', ['table']],
                            ['insert', ['link', 'picture', 'video', 'attach']],
                            ['view', ['fullscreen', 'codeview', 'help']]
                        ],
                        buttons: {
                            attach: AttachButton
                        }
                    });

                    // Add AJAX upload logic for custom attach button
                    $('#summernote_file_input').off('change').on('change', function() {
                        var file = this.files[0];
                        if (!file) return;

                        var targetEditor = $(this).data('target') || '#add_body';

                        if (file.size > 5 * 1024 * 1024) {
                            if (typeof showToast === 'function') {
                                showToast('File size must be less than 5MB.', 'danger');
                            } else {
                                alert('File size must be less than 5MB.');
                            }
                            this.value = '';
                            return;
                        }

                        var formData = new FormData();
                        formData.append('file', file);
                        
                        if (typeof showToast === 'function') {
                            showToast('Uploading attachment...', 'info');
                        }

                        $.ajax({
                            url: '{{ route("leads.upload.summernote.file", ["box_slug" => request()->route("box_slug")]) }}',
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if(response.success) {
                                    var linkNode = document.createElement('a');
                                    linkNode.setAttribute('href', response.url);
                                    linkNode.setAttribute('target', '_blank');
                                    linkNode.setAttribute('style', 'display: inline-block; padding: 5px 10px; background: #f4f4f4; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #333; margin: 2px;');
                                    linkNode.innerHTML = '📎 ' + response.filename;
                                    
                                    $(targetEditor).summernote('insertNode', linkNode);
                                    $(targetEditor).summernote('insertText', ' '); // insert space after

                                    if (typeof showToast === 'function') {
                                        showToast('Attachment inserted successfully.', 'success');
                                    }
                                } else {
                                    if (typeof showToast === 'function') {
                                        showToast(response.message || 'Upload failed.', 'danger');
                                    } else {
                                        alert(response.message || 'Upload failed.');
                                    }
                                }
                            },
                            error: function(xhr) {
                                if (typeof showToast === 'function') {
                                    showToast('Upload failed: ' + (xhr.responseJSON?.message || xhr.statusText), 'danger');
                                } else {
                                    alert('Upload failed: ' + (xhr.responseJSON?.message || xhr.statusText));
                                }
                            },
                            complete: function() {
                                $('#summernote_file_input').val(''); // Reset input
                            }
                        });
                    });
                }
            } else {
                $scope.find('.field-email').show();
                $leadStatusSelect.prop('required', true);
            }
        } else if (selectedType === 'general') {
            $scope.find('.field-general').show();
            $settingSelect.prop('required', false);
            $leadStatusSelect.prop('required', true);
        } else if (selectedType === 'note') {
            $scope.find('.field-note').show();
            $leadStatusSelect.prop('required', false);
        }

        // Handle Lead Status and Assign To visibility
        if (selectedType !== 'note') {
            $leadStatusSelect.closest('.col-md-6').show();
            $scope.find('.field-lead-sub-status').show();
            $scope.find('#activity_lead_sub_status_wrapper, #edit_activity_lead_sub_status_wrapper').show();
        } else {
            $leadStatusSelect.closest('.col-md-6').hide();
            $scope.find('#activity_lead_sub_status_wrapper, #edit_activity_lead_sub_status_wrapper').hide();
        }
        $scope.find('select[name="assigned_to"]').closest('.col-md-6').show();
        
        // Hide Follow-up task for Note and General types
        if (selectedType === 'note' || selectedType === 'general') {
            $scope.find('input[name="followuptask"], input[name="create_followup"]').closest('.col-md-12').hide();
            $scope.find('input[name="followuptask"], input[name="create_followup"]').prop('checked', false).trigger('change');
        } else {
            $scope.find('input[name="followuptask"], input[name="create_followup"]').closest('.col-md-12').show();
        }

        // Hide generic Attachment field for Note, General, and Email types
        if (selectedType === 'note' || selectedType === 'general' || selectedType === 'email') {
            $scope.find('input[type="file"]').not('#summernote_file_input').closest('.col-lg-12').hide();
        } else {
            $scope.find('input[type="file"]').not('#summernote_file_input').closest('.col-lg-12').show();
        }
    }

    // Handle follow-up task checkbox
    $('#followuptask_checkbox').on('change', function() {
        if ($(this).is(':checked')) {
            $('#followupTaskSection').slideDown();
            $('#followup_task_type, #followup_task_date, #followup_task_assign_to').prop('required', true);
            
            // Only require priority if it has options
            if ($('#followup_task_priority option').length > 0) {
                $('#followup_task_priority').prop('required', true);
                $('#followup_task_priority_asterisk').show();
            } else {
                $('#followup_task_priority').prop('required', false);
                $('#followup_task_priority_asterisk').hide();
            }
            // Check if meeting is selected to show meeting date
            handleFollowupTaskTypeChange();
        } else {
            $('#followupTaskSection').slideUp();
            $('#followup_task_type, #followup_task_date, #followup_meeting_date, #followup_task_assign_to, #followup_task_priority').prop('required', false);
            $('#followup_meeting_date_field').hide();
        }
    });

    // Handle follow-up task type change
    function handleFollowupTaskTypeChange() {
        // Always hide meeting date field
        $('#followup_meeting_date_field').slideUp();
        $('#followup_meeting_date').prop('required', false).val('');
    }

    // Handle follow-up task type dropdown change
    $(document).on('change', '#followup_task_type', function() {
        handleFollowupTaskTypeChange();
        // Reinitialize Select2 if needed
        const $followupTaskType = $(this);
        if ($followupTaskType.hasClass('select') && !$followupTaskType.hasClass('select2-hidden-accessible')) {
            $followupTaskType.select2({
                minimumResultsForSearch: -1,
                width: '100%'
            });
        }
    });

    // Handle activity type radio button change
    $('input[name="type"]').on('change', function() {
        handleActivityTypeChange($(this).closest('form'));
    });

    // Toggle Summernote for Notes in Add Activity Form
    $('#addActivityForm input[name="type"]').on('change', function() {
        const type = $(this).val();
        const $desc = $('#description');

        if (type === 'note') {
             if (!$desc.next('.note-editor').length) {
                var AttachButton = function (context) {
                    var ui = $.summernote.ui;
                    var button = ui.button({
                        contents: '<i class="ti ti-paperclip"></i> Attach',
                        tooltip: 'Attach File (Max 5MB)',
                        click: function () {
                            // Reuse the same input and logic, but we need to know WHICH editor to insert to!
                            // We can set a data attribute on the file input to know the target.
                            $('#summernote_file_input').data('target', '#description').click();
                        }
                    });
                    return button.render();
                };

                // Initialize Summernote
                $desc.summernote({
                    placeholder: 'Enter remarks',
                    height: 150,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['attach']],
                        ['height', ['height']]
                    ],
                    buttons: {
                        attach: AttachButton
                    },
                    disableDragAndDrop: true
                });
             }
             // Ensure it's blank
             $desc.summernote('code', '');
        } else {
            // Destroy Summernote if switching away from Note
            if ($desc.next('.note-editor').length) {
                 $desc.summernote('destroy');
                 $desc.show(); // Ensure visibility of original textarea
            }
            // Ensure no artifacts like <p><br></p> remain
            $desc.val('');
        }
    });

    // Initialize visibility on page load (but don't run on initial load to avoid hiding fields before they're populated)
    // handleActivityTypeChange();

    // Handle Activity Setting change - load Activity Types dynamically
    $(document).on('change', '#activity_setting_id', function() {
        const settingId = $(this).val();
        const $activityType = $('#activity_type');
        const $activityStatus = $('#activity_status_id');

        // Destroy Select2 if already initialized
        if ($activityType.hasClass('select2-hidden-accessible')) {
            $activityType.select2('destroy');
        }
        if ($activityStatus.hasClass('select2-hidden-accessible')) {
            $activityStatus.select2('destroy');
        }

        $activityType.empty().append('<option value="">Select Activity Type</option>');
        $activityStatus.empty().append('<option value="">Select Status</option>');

        if (settingId) {
            // Fetch activity types from the same endpoint as activity-settings page
            $.ajax({
                url: "{{ route('activities.types', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'GET',
                data: { activity_setting_id: settingId },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.success && res.data && res.data.length > 0) {
                        res.data.forEach(function(type) {
                            $activityType.append('<option value="' + type.id + '">' + type.title + '</option>');
                        });

                        const currentSelectedRadio = $('input[name="type"]:checked').val();
                        if (currentSelectedRadio) {
                            let targetTitle = currentSelectedRadio.toLowerCase();
                            let matchedValue = null;
                            $activityType.find('option').each(function() {
                                let optionText = $(this).text().trim().toLowerCase();
                                if (optionText === targetTitle || optionText.includes(targetTitle) || targetTitle.includes(optionText)) {
                                    matchedValue = $(this).val();
                                }
                            });
                            if (matchedValue) {
                                $activityType.val(matchedValue).trigger('change');
                            } else if (res.data.length === 1) {
                                $activityType.val(res.data[0].id).trigger('change');
                            }
                        } else if (res.data.length === 1) {
                            $activityType.val(res.data[0].id).trigger('change');
                        }
                    } else {
                        $activityType.append('<option value="" disabled>No activity types found</option>');
                    }
                    // Reinitialize Select2
                    if ($activityType.hasClass('select')) {
                        $activityType.select2({
                            minimumResultsForSearch: -1,
                            width: '100%'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error loading activity types:', xhr.responseText);
                    // Reinitialize Select2 even on error
                    if ($activityType.hasClass('select')) {
                        $activityType.select2({
                            minimumResultsForSearch: -1,
                            width: '100%'
                        });
                    }
                }
            });
        } else {
            // Reinitialize Select2 even when no setting is selected
            if ($activityType.hasClass('select')) {
                $activityType.select2({
                    minimumResultsForSearch: -1,
                    width: '100%'
                });
            }
            if ($activityStatus.hasClass('select')) {
                $activityStatus.select2({
                    minimumResultsForSearch: -1,
                    width: '100%'
                });
            }
        }
    });

    // Handle Activity Type change - load Activity Statuses dynamically
    $(document).on('change', '#activity_type', function() {
        const typeId = $(this).val();
        const $activityStatus = $('#activity_status_id');

        // Destroy Select2 if already initialized
        if ($activityStatus.hasClass('select2-hidden-accessible')) {
            $activityStatus.select2('destroy');
        }

        $activityStatus.empty().append('<option value="">Select Status</option>');

        if (typeId) {
            // Fetch activity statuses from the same endpoint as activity-settings page
            $.ajax({
                url: "{{ route('activities.statuses', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'GET',
                data: { activity_type_id: typeId },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.success && res.data && res.data.length > 0) {
                        res.data.forEach(function(status) {
                            $activityStatus.append('<option value="' + status.id + '">' + status.title + '</option>');
                        });
                    } else {
                        $activityStatus.append('<option value="" disabled>No activity statuses found</option>');
                    }
                    // Reinitialize Select2
                    if ($activityStatus.hasClass('select')) {
                        $activityStatus.select2({
                            minimumResultsForSearch: -1,
                            width: '100%'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error loading activity statuses:', xhr.responseText);
                    // Reinitialize Select2 even on error
                    if ($activityStatus.hasClass('select')) {
                        $activityStatus.select2({
                            minimumResultsForSearch: -1,
                            width: '100%'
                        });
                    }
                }
            });
        } else {
            // Reinitialize Select2 even when no type is selected
            if ($activityStatus.hasClass('select')) {
                $activityStatus.select2({
                    minimumResultsForSearch: -1,
                    width: '100%'
                });
            }
        }
    });

    // Handle file input change - show selected files
    $(document).on('change', '#activity_file', function() {
        const files = this.files;
        const $fileList = $('#file-list');
        $fileList.empty();

        if (files.length > 0) {
            let html = '<div class="list-group">';
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
                html += '<div class="list-group-item d-flex justify-content-between align-items-center">';
                html += '<div>';
                html += '<i class="ti ti-file me-2"></i>';
                html += '<span>' + file.name + '</span>';
                html += '<small class="text-muted ms-2">(' + fileSize + ' MB)</small>';
                html += '</div>';
                html += '<button type="button" class="btn btn-sm btn-danger remove-file" data-index="' + i + '"><i class="ti ti-x"></i></button>';
                html += '</div>';
            }
            html += '</div>';
            $fileList.html(html);
        }
    });

    // Handle remove file button
    $(document).on('click', '.remove-file', function() {
        const index = $(this).data('index');
        const input = document.getElementById('activity_file');
        const dt = new DataTransfer();

        for (let i = 0; i < input.files.length; i++) {
            if (i !== index) {
                dt.items.add(input.files[i]);
            }
        }
        input.files = dt.files;
        $(input).trigger('change');
    });

    // Dependent logic for Edit Form Setting -> Type
    $(document).on('change', '#edit_activity_setting_id', function(e, preselectedTypeId = null, preselectedStatusId = null) {
        const settingId = $(this).val();
        const typeSelect = $('#edit_activity_type');
        typeSelect.empty().append('<option value="">Select Activity Type</option>');
        $('#edit_activity_status_id').empty().append('<option value="">Select Status</option>');

        if (settingId) {
             $.ajax({
                url: "{{ route('activities.types', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'GET',
                data: { activity_setting_id: settingId },
                success: function(res) {
                    if(res.success && res.data) {
                         $.each(res.data, function(key, item) {
                            typeSelect.append('<option value="'+item.id+'">'+item.title+'</option>');
                        });
                    }
                    if (preselectedTypeId) {
                        console.log('Pre-selecting Type in Edit:', preselectedTypeId);
                        typeSelect.val(preselectedTypeId).trigger('change', [preselectedStatusId]);
                    }
                }
            });
        }
    });

    // Dependent logic for Edit Form Type -> Status
    $(document).on('change', '#edit_activity_type', function(e, preselectedStatusId = null) {
        const typeId = $(this).val();
        const statusSelect = $('#edit_activity_status_id');
        statusSelect.empty().append('<option value="">Select Status</option>');

        if (typeId) {
             $.ajax({
                url: "{{ route('activities.statuses', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'GET',
                data: { activity_type_id: typeId },
                success: function(res) {
                    if(res.success && res.data) {
                        $.each(res.data, function(key, item) {
                            statusSelect.append('<option value="'+item.id+'">'+item.title+'</option>');
                        });
                    }
                    if (preselectedStatusId) {
                        console.log('Pre-selecting Status in Edit:', preselectedStatusId);
                        statusSelect.val(preselectedStatusId).trigger('change');
                    }
                }
            });
        }
    });

    // Check Done With visibility (Scoped)
    function checkDoneWithVisibility($form = null) {
        const $scope = $form || $('#addActivityForm');
        setTimeout(() => {
            const type = $scope.find('input[name="type"]:checked').val();
            const $statusSelect = $scope.find('select[name="activity_status_id"]');
            let statusText = '';

            if ($statusSelect.hasClass('select2-hidden-accessible')) {
                const data = $statusSelect.select2('data');
                statusText = data && data[0] ? data[0].text : '';
            } else {
                statusText = $statusSelect.find('option:selected').text().trim();
            }

            if (type === 'meeting' && statusText.toLowerCase() === 'done') {
                $scope.find('.field-meeting[id$="_with_wrapper"]').show();
            } else {
                $scope.find('.field-meeting[id$="_with_wrapper"]').hide();
                $scope.find('select[name="done_with"]').val('');
            }
        }, 100);
    }

    $(document).on('change', 'select[name="activity_status_id"]', function() {
        checkDoneWithVisibility($(this).closest('form'));
    });

    $(document).on('change', 'input[name="type"]', function() {
        checkDoneWithVisibility($(this).closest('form'));
    });

    // Handle Lead Status change to update Task Priority options
    $(document).on('change', '#activity_lead_status_id', function() {
        console.log('Lead Status Changed:', $(this).val());

        const selectedOption = $(this).find('option:selected');
        const prioritiesData = selectedOption.data('priorities');
        const $prioritySelect = $('#followup_task_priority');

        console.log('Priorities Data:', prioritiesData);

        let priorities = [];
        // If data attribute is auto-parsed by jQuery to object/array, use it.
        // Otherwise parse string.
        if (prioritiesData) {
             if (typeof prioritiesData === 'string') {
                 try {
                     priorities = JSON.parse(prioritiesData);
                 } catch(e) {
                     console.error('Error parsing priorities data', e);
                 }
             } else {
                 priorities = prioritiesData;
             }
        }

        // Destroy Select2 before modifying
        if ($prioritySelect.hasClass('select2-hidden-accessible')) {
            $prioritySelect.select2('destroy');
        }

        $prioritySelect.empty();

        if (priorities && Array.isArray(priorities) && priorities.length > 0) {
            console.log('Populating ' + priorities.length + ' priorities');
            priorities.forEach(function(priority) {
                // Ensure priority is properly formatted
                let val = priority.toLowerCase();
                let label = priority.charAt(0).toUpperCase() + priority.slice(1);
                $prioritySelect.append(`<option value="${val}">${label}</option>`);
            });
            
            // If the section is currently active, enforce requirement
            if ($('#followuptask_checkbox').is(':checked')) {
                $prioritySelect.prop('required', true);
                $('#followup_task_priority_asterisk').show();
            }
        } else {
            console.log('No specific priorities found, leaving empty');
            // Remove requirement since there are no options
            $prioritySelect.prop('required', false);
            $('#followup_task_priority_asterisk').hide();
        }

        // Re-initialize Select2
        if ($prioritySelect.hasClass('select')) {
             $prioritySelect.select2({
                minimumResultsForSearch: -1,
                width: '100%'
            });
        }
    });

    // Handle Add Activity Form Submission
    $('#addActivityForm').on('submit', function(e) {
        e.preventDefault();

        if (!currentLeadIdForActivity) {
            showToast('Lead ID not found. Please refresh the page.', 'danger');
            return;
        }

        const type = $('input[name="type"]:checked', this).val();

        // -------------------------
        // COMPOSE EMAIL INTERCEPT
        // -------------------------
        if (type === 'email') {
            const fromEmailId = $('#add_from_email').val();
            const toEmail = $('#add_to_email').val();
            const subject = $('#add_subject').val();
            let body = $('#add_body').val();
            if ($.fn.summernote && $('#add_body').next('.note-editor').length) {
                body = $('#add_body').summernote('code');
                $('#add_body').val(body);
            }

            if (!fromEmailId || !toEmail || !subject || !body || body === '<p><br></p>') {
                showToast('Please fill in all email fields (From, To, Subject, Message).', 'danger');
                return;
            }

            const emailFormData = new FormData();
            emailFormData.append('lead_id', currentLeadIdForActivity);
            emailFormData.append('from_email_id', fromEmailId);
            emailFormData.append('to_email', toEmail);
            emailFormData.append('subject', subject);
            emailFormData.append('body', body);
            emailFormData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            const fileInput = document.getElementById('activity_file');
            if (fileInput && fileInput.files.length > 0) {
                for (let i = 0; i < fileInput.files.length; i++) {
                    emailFormData.append('attachments[]', fileInput.files[i]);
                }
            }

            const $submitBtn = $('#submitActivityBtn');
            $submitBtn.prop('disabled', true).text('Sending...');

            $.ajax({
                url: "{{ route('google.send_email', request()->route('box_slug')) }}",
                type: 'POST',
                data: emailFormData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $submitBtn.prop('disabled', false).text('Save');
                    if (res.success) {
                        showToast('Email sent and activity logged successfully!', 'success');
                        const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvas_add'));
                        if (offcanvas) offcanvas.hide();
                        $('#addActivityForm')[0].reset();
                        if ($('#add_body').next('.note-editor').length) {
                            $('#add_body').summernote('reset');
                        }
                        // Refresh email tab if it's active
                        if ($('#email').hasClass('active') || $('.nav-link[data-bs-target="#email"]').hasClass('active')) {
                            loadActivitiesForLead('email', '#emails-container');
                        }
                    } else {
                        showToast(res.message || 'Failed to send email.', 'danger');
                    }
                },
                error: function(xhr) {
                    $submitBtn.prop('disabled', false).text('Save');
                    showToast('Failed to send email. Please try again.', 'danger');
                }
            });
            return; // Stop normal activity store execution
        }
        // -------------------------

        // Validate required fields
        const activityDate = $('#activity_date').val();
        const leadStatusId = $('#activity_lead_status_id').val();

        if (!type || !activityDate) {
            showToast('Please fill in all required fields.', 'danger');
            return;
        }

        // Validate fields based on activity type
        if (type === 'call' || type === 'whatsapp' || type === 'meeting') {
            const activitySettingId = $('#activity_setting_id').val();
            const activityType = $('#activity_type').val();
            if (!activitySettingId || !activityType || !leadStatusId) {
                showToast('Please fill in all required fields for ' + type + ' activity.', 'danger');
                return;
            }
            if (type === 'meeting') {
                const meetingDate = $('#meeting_date').val();
                if (!meetingDate) {
                    showToast('Please select a meeting date.', 'danger');
                    return;
                }
            }
        } else if (type === 'email') {
            if (!leadStatusId) {
                showToast('Please select a lead status.', 'danger');
                return;
            }
        }

        // Validate follow-up task if checked
        if ($('#followuptask_checkbox').is(':checked')) {
            const followupTaskType = $('#followup_task_type').val();
            const followupTaskDate = $('#followup_task_date').val();
            const followupTaskAssignTo = $('#followup_task_assign_to').val();
            if (!followupTaskType || !followupTaskDate || !followupTaskAssignTo) {
                showToast('Please fill in all required follow-up task fields.', 'danger');
                return;
            }
            // Validate meeting date if meeting is selected
            if (followupTaskType === 'meeting') {
                // const followupMeetingDate = $('#followup_meeting_date').val();
                // if (!followupMeetingDate) {
                //     showToast('Please select a meeting date for the follow-up task.', 'danger');
                //     return;
                // }
            }
        }

        // Create FormData for file upload
        const formData = new FormData();
        formData.append('lead_id', currentLeadIdForActivity);
        formData.append('type', type);

        // Add fields based on activity type
        if (type === 'call' || type === 'whatsapp' || type === 'meeting') {
            formData.append('activity_setting_id', $('#activity_setting_id').val() || '');
            formData.append('activity_type', $('#activity_type').val() || '');
            formData.append('activity_status_id', $('#activity_status_id').val() || '');
        }

        formData.append('assigned_to', $('#assigned_to_assigned_to').val() || '');
        formData.append('lead_status_id', $('#activity_lead_status_id').val() || '');
        formData.append('lead_sub_status_id', $('#activity_lead_sub_status_id').val() || '');

        if (type === 'meeting') {
            formData.append('meeting_date', $('#meeting_date').val() || '');
        }

        formData.append('subject', $('#subject').val() || '');
        formData.append('description', $('#description').val() || '');
        formData.append('activity_date', activityDate);
        formData.append('reminder_date', $('#reminder_date').val() || '');
        formData.append('label', $('#label').val() || '');

        // Add follow-up task fields if checked
        if ($('#followuptask_checkbox').is(':checked')) {
            formData.append('followup_task', '1');
            formData.append('followup_task_type', $('#followup_task_type').val() || '');
            formData.append('followup_task_date', $('#followup_task_date').val() || '');
            formData.append('followup_task_assign_to', $('#followup_task_assign_to').val() || '');
            formData.append('followup_task_priority', $('#followup_task_priority').val() || '');
            // Add meeting date if meeting is selected
            if ($('#followup_task_type').val() === 'meeting') {
                // formData.append('followup_meeting_date', $('#followup_meeting_date').val() || '');
            }
        }

        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Append files
        const fileInput = document.getElementById('activity_file');
        if (fileInput.files.length > 0) {
            for (let i = 0; i < fileInput.files.length; i++) {
                formData.append('files[]', fileInput.files[i]);
            }
        }

        // Disable submit button
        const $submitBtn = $('#submitActivityBtn');
        $submitBtn.prop('disabled', true).text('Creating...');
        const whatsappTemplateId = $('#add_whatsapp_template_id').val();

        $.ajax({
            url: "{{ route('activities.store', ['box_slug' => request()->route('box_slug')]) }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.success) {
                    // Close offcanvas safely
                    const offcanvasEl = document.getElementById('offcanvas_add');
                    if (offcanvasEl) {
                        let offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                        if (offcanvas) {
                            offcanvas.hide();
                        } else {
                            // Fallback if not initialized
                            $(offcanvasEl).removeClass('show');
                            $('.offcanvas-backdrop').remove();
                            $('body').removeClass('modal-open').css('overflow', '');
                        }
                    }

                    // Reset form
                    $('#addActivityForm')[0].reset();
                    $('#file-list').empty();

                    // Reset conditional visibility
                    $('.field-call, .field-meeting, .field-email, .field-whatsapp').hide();
                    $('#followupTaskSection').hide();
                    $('#followup_meeting_date_field').hide();
                    $('#followuptask_checkbox').prop('checked', false);
                    $('#followup_meeting_date').val('').prop('required', false);

                    // Reset to default activity type (call)
                    $('#type_call').prop('checked', true);
                    handleActivityTypeChange();

                    // Clear global activities cache to force reload
                    window.allLeadActivities = [];
                    
                    // ALWAYS force reload the main activities container
                    if ($('#activities-container').length) {
                        loadActivitiesForLead('all', '#activities-container');
                    }
                    
                    // Also reload specific sub-tabs if they are currently active
                    if ($('#calls').hasClass('active') || $('.nav-link[data-bs-target="#calls"]').hasClass('active')) {
                        loadActivitiesForLead('call', '#calls-container');
                    } 
                    if ($('#meetings').hasClass('active') || $('.nav-link[data-bs-target="#meetings"]').hasClass('active')) {
                        loadActivitiesForLead('meeting', '#meetings-container');
                    } 
                    if ($('#email').hasClass('active') || $('.nav-link[data-bs-target="#email"]').hasClass('active')) {
                        loadActivitiesForLead('email', '#emails-container');
                    } 
                    if ($('#wapp').hasClass('active') || $('.nav-link[data-bs-target="#wapp"]').hasClass('active')) {
                        loadActivitiesForLead('whatsapp', '#whatsapp-container');
                    } 
                    if ($('#receipts_history').hasClass('active') || $('.nav-link[data-bs-target="#receipts_history"]').hasClass('active')) {
                        loadReceiptsForLead();
                    }

                    // Check if WhatsApp template needs to be sent
                    if (type === 'whatsapp' && whatsappTemplateId) {
                        $.ajax({
                            url: '{{ box_route('whatsapp.send_template_direct') }}',
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                lead_id: currentLeadIdForActivity,
                                template_id: whatsappTemplateId
                            },
                            success: function(waRes) {
                                if (waRes.success) {
                                    showToast('WhatsApp template sent successfully!', 'success');
                                    if (activeTab === '#whatsapp') {
                                        loadActivitiesForLead('whatsapp', '#whatsapp-container');
                                    }
                                } else {
                                    showToast(waRes.message || 'Failed to send WhatsApp template.', 'danger');
                                }
                            }
                        });
                    }

                    // Show success message
                    showToast('Activity created successfully!', 'success');
                } else {
                    showToast(res.message || 'Failed to create activity.', 'danger');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Failed to create activity.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg = errors.join(', ');
                }
                showToast(errorMsg, 'danger');
            },
            complete: function() {
                $submitBtn.prop('disabled', false).text('Create');
            }
        });
    });

    // Load and render activities
    function loadReceiptsForLead() {
        if (!currentLeadIdForActivity) return;
        
        const $container = $('#receipts-container');
        if ($container.length === 0) return;

        $container.html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

        $.ajax({
            url: "{{ route('receipts.by_lead', ['box_slug' => request()->route('box_slug') ?? '']) }}",
            type: 'GET',
            data: { lead_id: currentLeadIdForActivity },
            success: function(res) {
                if (res.success && res.data && res.data.length > 0) {
                    let html = '<div class="table-responsive"><table class="table table-nowrap mb-0">';
                    html += '<thead class="thead-light"><tr>';
                    html += '<th>Receipt No</th>';
                    html += '<th>Company</th>';
                    html += '<th>Division</th>';
                    html += '<th>Amount</th>';
                    html += '<th>Payment Mode</th>';
                    html += '<th>Bank</th>';
                    html += '<th>Reference</th>';
                    html += '<th>Status</th>';
                    html += '<th>Date</th>';
                    html += '<th>Created By</th>';
                    html += '<th>Action</th>';
                    html += '</tr></thead><tbody>';
                    
                    res.data.forEach(function(receipt) {
                        html += '<tr>';
                        html += '<td><div class="h6 mb-0">' + (receipt.invoice_number || 'N/A') + '</div></td>';
                        html += '<td>' + (receipt.company ? receipt.company.bill_name : 'N/A') + '</td>';
                        html += '<td>' + (receipt.division ? receipt.division.name : 'N/A') + '</td>';
                        html += '<td>' + parseFloat(receipt.amount).toFixed(2) + '</td>';
                        html += '<td>' + (receipt.payment_mode ? receipt.payment_mode.replace('_', ' ').toUpperCase() : 'N/A') + '</td>';
                        html += '<td>' + (receipt.bank ? receipt.bank.name : 'N/A') + '</td>';
                        html += '<td>' + (receipt.reference || 'N/A') + '</td>';
                        let rawStatus = (receipt.status !== null && receipt.status !== undefined) ? receipt.status : 'Active';
                        let statusStr = String(rawStatus);
                        let badgeClass = 'bg-danger';
                        let displayStatus = statusStr;
                        
                        if (statusStr.toLowerCase() === 'active' || statusStr === '1') {
                            displayStatus = 'Active';
                            badgeClass = 'bg-success';
                        } else if (statusStr === '0') {
                            displayStatus = 'Inactive';
                        }
                        
                        html += '<td><span class="badge ' + badgeClass + '">' + displayStatus + '</span></td>';
                        
                        let rDate = receipt.receipt_date;
                        if (rDate && typeof rDate === 'string') rDate = rDate.replace(' ', 'T');
                        const dateObj = new Date(rDate);
                        const displayDate = isNaN(dateObj) ? rDate : dateObj.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                        
                        html += '<td>' + displayDate + '</td>';
                        html += '<td><div class="d-flex align-items-center"><div class="h6 mb-0">' + (receipt.creator ? receipt.creator.name : 'System') + '</div></div></td>';
                        html += `<td>
                            <div class="d-flex gap-2">
                                <a href="javascript:void(0);" class="btn btn-sm btn-icon btn-outline-primary btn-edit-receipt" data-id="${receipt.id}" data-receipt='${JSON.stringify(receipt).replace(/'/g, "&apos;")}' title="Edit Receipt"><i class="ti ti-edit"></i></a>
                                <a href="javascript:void(0);" class="btn btn-sm btn-icon btn-outline-danger btn-delete-receipt" data-id="${receipt.id}" title="Delete Receipt"><i class="ti ti-trash"></i></a>
                            </div>
                        </td>`;
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table></div>';
                    $container.html(html);
                } else {
                    $container.html('<div class="text-center py-5"><p class="text-muted">No receipts found.</p></div>');
                }
            },
            error: function(xhr) {
                $container.html('<div class="text-center py-5"><p class="text-danger">Failed to load receipts.</p></div>');
            }
        });
    }

    function loadActivitiesForLead(filterType = 'all', containerId = '#activities-container') {
        if (!currentLeadIdForActivity) return;

        const $container = $(containerId);
        if ($container.length === 0) return;

        $container.html('<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

        // If we already have the global activities list, filter from it instead of re-fetching
        if (window.allLeadActivities && window.allLeadActivities.length > 0) {
            renderActivitiesForLead(window.allLeadActivities, filterType, containerId);
            return;
        }

        $.ajax({
            url: "{{ route('activities.get', ['box_slug' => request()->route('box_slug')]) }}",
            type: 'GET',
            cache: false, // Prevent browser caching so new activities show up immediately
            data: { lead_id: currentLeadIdForActivity },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.success && res.data) {
                     window.allLeadActivities = res.data;
                     renderActivitiesForLead(res.data, filterType, containerId);
                } else {
                    $container.html('<div class="text-center py-5"><p class="text-muted">No activities found.</p></div>');
                }
            },
            error: function(xhr) {
                console.error('Error loading activities:', xhr.responseText);
                $container.html('<div class="text-center py-5"><p class="text-danger">Failed to load activities.</p></div>');
            }
        });
    }

    // Render activities with filter
    function renderActivitiesForLead(activities, filterType = 'all', containerId = '#activities-container', sortOrder = 'desc') {
        const $container = $(containerId);

        if (!activities || activities.length === 0) {
            $container.html('<div class="text-center py-5"><p class="text-muted">No activities found.</p></div>');
            return;
        }

        // Special handling for Files tab - aggregate all files from all activities
        if (filterType === 'files') {
            const allFiles = [];
            activities.forEach(function(activity) {
                if (activity.params) {
                    try {
                        const params = typeof activity.params === 'string' ? JSON.parse(activity.params) : activity.params;
                        if (params.files && params.files.length > 0) {
                            params.files.forEach(function(file) {
                                allFiles.push({
                                    file: file,
                                    activity: activity
                                });
                            });
                        }
                    } catch (e) {
                        console.error('Error parsing params for file extraction:', e);
                    }
                }
            });

            if (allFiles.length === 0) {
                 $container.html('<div class="text-center py-5"><p class="text-muted">No files found.</p></div>');
                 return;
            }

            // Render Files List
            let html = '';
            // Sort by activity date
            allFiles.sort((a, b) => {
                const timeA = new Date(a.activity.created_at).getTime();
                const timeB = new Date(b.activity.created_at).getTime();
                return sortOrder === 'asc' ? timeA - timeB : timeB - timeA;
            });

            allFiles.forEach(function(item) {
                const file = item.file;
                const activity = item.activity;
                const fileSize = (file.file_size / 1024).toFixed(2) + ' KB';
                const boxSlug = "{{ request()->route('box_slug') }}";
                const fileUrl = '/' + boxSlug + '/activity-file/' + file.file_path.split('/').map(encodeURIComponent).join('/');
                const rawExtension = file.file_path.split('.').pop();
                const fileExtension = rawExtension.toUpperCase();
                const uploadDate = new Date(activity.created_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                const isImage = ['JPG', 'JPEG', 'PNG', 'GIF', 'WEBP', 'BMP'].includes(fileExtension);

                html += '<div class="card border mb-3">';
                html += '<div class="card-body">';
                html += '<div class="d-flex align-items-center">';

                if (isImage) {
                    html += '<a href="' + fileUrl + '" target="_blank" class="avatar avatar-md me-3">';
                    html += '<img src="' + fileUrl + '" alt="' + file.original_name + '" class="rounded" style="width: 100%; height: 100%; object-fit: cover;">';
                    html += '</a>';
                } else {
                    html += '<span class="avatar avatar-md bg-light text-primary me-3"><i class="ti ti-file fs-20"></i></span>';
                }

                html += '<div class="flex-grow-1">';
                html += '<h6 class="fw-medium mb-1"><a href="' + fileUrl + '" target="_blank">' + file.original_name + '</a></h6>';
                const activityName = activity.subject || (activity.type.charAt(0).toUpperCase() + activity.type.slice(1));
                html += '<p class="text-muted fs-12 mb-0">' + fileSize + ' • ' + fileExtension + ' • ' + uploadDate + ' • via ' + activityName + '</p>';
                html += '</div>';
                html += '<div class="d-flex align-items-center">';
                 html += '<a href="' + fileUrl + '?download=1" target="_blank" class="btn btn-sm btn-light-primary"><i class="ti ti-download me-1"></i>Download</a>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            });
            $container.html(html);
            return;
        }

        // Normal activity filtering logic below
        let filteredActivities = activities;
        if (filterType !== 'all') {
            filteredActivities = activities.filter(activity => activity.type === filterType);
        }

        if (filteredActivities.length === 0) {
             const typeName = filterType.charAt(0).toUpperCase() + filterType.slice(1);
             $container.html('<div class="text-center py-5"><p class="text-muted">No ' + typeName + 's found.</p></div>');
             return;
        }

        // Group activities by date string formatted to ISO to sort properly
        const grouped = {};
        filteredActivities.forEach(function(activity) {
            let dateStr = activity.activity_date || activity.created_at;
            if (dateStr && typeof dateStr === 'string') dateStr = dateStr.replace(' ', 'T');
            const date = new Date(dateStr);
            const displayLabel = date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
            // Using YYYYMMDD for reliable sorting
            const sortKey = date.getFullYear() + ('0' + (date.getMonth() + 1)).slice(-2) + ('0' + date.getDate()).slice(-2);

            if (!grouped[sortKey]) {
                grouped[sortKey] = {
                    label: displayLabel,
                    items: []
                };
            }
            grouped[sortKey].items.push(activity);
        });

        let keys = Object.keys(grouped).sort();
        if (sortOrder === 'desc') keys.reverse();

        let html = '';
        keys.forEach(function(sortKey) {
            const group = grouped[sortKey];
            html += '<div class="badge badge-soft-purple fs-14 fw-normal shadow-none mb-3"><i class="ti ti-calendar-check me-1"></i>' + group.label + '</div>';

            let groupItems = group.items;
            groupItems.sort(function(a, b) {
                let dateAStr = a.activity_date || a.created_at;
                if (dateAStr && typeof dateAStr === 'string') dateAStr = dateAStr.replace(' ', 'T');
                let dateBStr = b.activity_date || b.created_at;
                if (dateBStr && typeof dateBStr === 'string') dateBStr = dateBStr.replace(' ', 'T');
                const timeA = new Date(dateAStr).getTime();
                const timeB = new Date(dateBStr).getTime();
                return sortOrder === 'asc' ? timeA - timeB : timeB - timeA;
            });

            groupItems.forEach(function(activity) {
                const typeIcons = {
                    'call': 'ti-phone',
                    'email': 'ti-mail',
                    'meeting': 'ti-user-share',
                    'task': 'ti-subtask',
                    'note': 'ti-notes',
                    'query': 'ti-help',
                    'history': 'ti-history',
                    'sms': 'ti-message',
                    'general': 'ti-file',
                    'pdf': 'ti-file-type-pdf',
                    'receipt': 'ti-receipt',
                    'whatsapp': 'ti-brand-whatsapp'
                };

                const typeColors = {
                    'call': 'bg-secondary-success',
                    'email': 'bg-pending',
                    'meeting': 'bg-info',
                    'task': 'bg-secondary-success',
                    'note': 'bg-orange',
                    'query': 'bg-warning',
                    'history': 'bg-secondary',
                    'sms': 'bg-success',
                    'general': 'bg-secondary',
                    'pdf': 'bg-danger',
                    'receipt': 'bg-success',
                    'whatsapp': 'bg-success'
                };

                const icon = typeIcons[activity.type] || 'ti-file';
                const color = typeColors[activity.type] || 'bg-secondary';
                let adStr = activity.activity_date || activity.created_at;
                if (adStr && typeof adStr === 'string') adStr = adStr.replace(' ', 'T');
                let caStr = activity.created_at;
                if (caStr && typeof caStr === 'string') caStr = caStr.replace(' ', 'T');
                const time = new Date(adStr).toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
                const createdAtFull = new Date(caStr).toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                const isFollowUp = activity.subject && activity.subject.includes('Follow up');
                const statusClass = activity.status === 'Close' ? 'bg-success' : activity.status === 'Failed' ? 'bg-danger' : 'bg-warning';

                html += '<div class="card border shadow-none mb-3">';
                html += '<div class="card-body p-3">';
                html += '<div class="d-flex">';
                html += '<span class="avatar avatar-md flex-shrink-0 rounded me-2 ' + color + '">';
                html += '<i class="ti ' + icon + '"></i>';
                html += '</span>';
                html += '<div class="flex-grow-1">';
                let dynamicSubject = activity.subject;
                if (!dynamicSubject && activity.type !== 'history') {
                    let addedByName = activity.added_by_user ? activity.added_by_user.name : 'Unknown';
                    let typeName = activity.type ? activity.type : '';
                    let taskTypeStr = activity.task_type ? activity.task_type : '';
                    let activityTypeCombined = taskTypeStr ? (typeName + ' ' + taskTypeStr) : typeName;
                    
                    let actStatus = activity.activity_type_status ? activity.activity_type_status : (activity.activity_status ? activity.activity_status.activity_status : null);
                    
                    let assignedToName = activity.assigned_user ? activity.assigned_user.name : 'Unknown';
                    if (activity.assigned_user && activity.added_by_user && activity.assigned_user.id === activity.added_by_user.id) {
                        assignedToName = 'Self';
                    } else if (activity.assigned_to === activity.added_by) {
                        assignedToName = 'Self';
                    }
                    
                    let doneWithName = '';
                    if (activity.done_with_user) {
                         doneWithName = activity.done_with_user.name;
                    } else if (activity.done_with === activity.added_by) {
                         doneWithName = addedByName;
                    } else if (activity.done_with === activity.assigned_to) {
                         doneWithName = activity.assigned_user ? activity.assigned_user.name : '';
                    }

                    let constructedSubject = addedByName + ' created a ' + activityTypeCombined;
                    if (actStatus && actStatus !== 'Pending' && actStatus !== 'None') {
                        constructedSubject += ' with status ' + actStatus;
                    }
                    if (doneWithName) {
                        constructedSubject += ' along with ' + doneWithName;
                    }
                    if (assignedToName && assignedToName !== 'Self') {
                        constructedSubject += ' and assigned to ' + assignedToName;
                    }
                    dynamicSubject = constructedSubject;
                } else if (!dynamicSubject) {
                    if (activity.type === 'history') {
                        let addedByName = activity.added_by_user ? activity.added_by_user.name : 'System';
                        dynamicSubject = addedByName + ' made some changes';
                    } else {
                        dynamicSubject = activity.type.charAt(0).toUpperCase() + activity.type.slice(1);
                    }
                }

                if (activity.created_from === 'app') {
                    dynamicSubject += ' <span class="badge bg-primary-subtle text-primary ms-1" style="font-size: 10px;">Created via App</span>';
                }

                html += '<h6 class="fw-medium mb-1">' + dynamicSubject + '</h6>';

                // Highlighted Time Display (Under Subject)
                html += '<div class="mt-2 mb-2 d-flex flex-wrap gap-2">';
                if (isFollowUp) {
                    html += '<span class="badge bg-soft-info text-info border fs-11 fw-normal"><i class="ti ti-calendar-time me-1"></i>Follow-up Time: ' + time + '</span>';
                } else if (activity.type === 'history') {
                    html += '<span class="badge bg-light text-muted border fs-11 fw-normal"><i class="ti ti-clock me-1"></i>Logged At: ' + createdAtFull + '</span>';
                }
                html += '</div>';

                // Lead Status & Activity Status Definitions
                const lStatusName = (activity.lead_status ? activity.lead_status.name : null);
                const lSubStatusName = (activity.lead_sub_status ? activity.lead_sub_status.name : null);
                let legacyStatusName = null;
                let legacySubStatusName = null;

                if (activity.params) {
                    try {
                        const params = typeof activity.params === 'string' ? JSON.parse(activity.params) : activity.params;
                        legacyStatusName = params.lead_status_name;
                        legacySubStatusName = params.lead_sub_status_name;
                    } catch(e) {}
                }

                const displayStatus = lStatusName || legacyStatusName;
                const displaySubStatus = lSubStatusName || legacySubStatusName;
                const activityStatusName = activity.activity_status ? activity.activity_status.activity_status : null;

                // Assignment Change Styled Row (Matching User Image Design)
                let assignmentRowHtml = '';
                if (activity.params) {
                    try {
                        const params = typeof activity.params === 'string' ? JSON.parse(activity.params) : activity.params;
                        if (params.previous_assignee_name && params.current_assignee_name) {
                             assignmentRowHtml += '<div class="mt-2 mb-2 fs-13 d-flex align-items-center">';
                             assignmentRowHtml += '<i class="ti ti-user-share me-2 text-warning fs-14"></i>';
                             assignmentRowHtml += '<span style="color: #fb8c00;" class="fw-medium">Assigned To Changed from </span>';
                             assignmentRowHtml += '<span class="fw-bold text-dark ms-1"> ' + params.previous_assignee_name + ' </span>';
                             assignmentRowHtml += '<span style="color: #fb8c00;" class="fw-medium ms-1"> to </span>';
                             assignmentRowHtml += '<span class="fw-bold text-dark ms-1"> ' + params.current_assignee_name + ' </span>';
                             assignmentRowHtml += '</div>';
                        }
                    } catch(e) {}
                }
                if (activity.description) {
                    if (activity.type === 'history') {
                        const assignmentText = "Assigned To changed from";
                        const hasOtherInfo = activity.description.includes("Lead Status") || activity.description.includes("Lead Sub Status");

                        if (!assignmentRowHtml || hasOtherInfo) {
                            let displayDesc = activity.description;
                            if (assignmentRowHtml && displayDesc.includes(assignmentText)) {
                                displayDesc = displayDesc.split('\n').filter(line => !line.includes(assignmentText)).join('\n');
                            }
                            if (displayDesc.trim()) {
                                html += '<div class="p-2 rounded mb-0 pb-0 ms-1">';
                                html += '<p class="mb-0 fs-13 text-dark italic">' + displayDesc.replace(/\n/g, '<br>') + '</p>';
                                html += '</div>';
                            }
                        }
                    } else {
                        // "make message according to id also" -> maybe remove the grey box to match Image 2 exactly
                        html += '<div class="p-0 rounded mb-2 ms-0">';
                        html += '<p class="mb-0 fs-13 text-dark">' + activity.description.replace(/\n/g, '<br>') + '</p>';
                        html += '</div>';
                    }
                }

                // Inject the styled assignment row if it exists (Only for History type to avoid duplication)
                if (assignmentRowHtml && activity.type === 'history') {
                    html += assignmentRowHtml;
                }

                if (activity.type !== 'history') {
                    if (displayStatus || displaySubStatus || activityStatusName) {
                         html += '<div class="mt-2 mb-2 fs-12">';
                         if (activityStatusName) {
                             html += '<span class="text-muted"><i class="ti ti-activity me-1"></i>Activity Status: </span>';
                             html += '<span class="badge bg-soft-success text-success me-2">' + activityStatusName + '</span>';
                         }
                         if (displayStatus || displaySubStatus) {
                             html += '<span class="text-muted"><i class="ti ti-arrow-right-circle me-1"></i>Lead Status: </span>';
                             if (displayStatus) {
                                 html += '<span class="badge bg-soft-info text-info me-1">' + displayStatus + '</span>';
                             }
                             if (displaySubStatus) {
                                 html += '<span class="badge bg-soft-secondary text-secondary">' + displaySubStatus + '</span>';
                             }
                         }
                         html += '</div>';
                    }

                    // Status Change Badges (Information is now handled by separate history card)
                }

                // Display attached files
                if (activity.params) {
                    try {
                        const params = typeof activity.params === 'string' ? JSON.parse(activity.params) : activity.params;
                        if (params.files && params.files.length > 0) {
                            html += '<div class="mt-2 mb-2">';
                            html += '<small class="text-muted d-block mb-1"><i class="ti ti-paperclip me-1"></i>Attachments:</small>';
                            params.files.forEach(function(file) {
                                const fileSize = (file.file_size / 1024).toFixed(2) + ' KB';
                                const boxSlug = "{{ request()->route('box_slug') }}";
                                const fileUrl = '/' + boxSlug + '/activity-file/' + file.file_path.split('/').map(encodeURIComponent).join('/');
                                const extension = file.file_path.split('.').pop().toLowerCase();
                                const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(extension);

                                if (isImage) {
                                     html += '<a href="' + fileUrl + '" target="_blank" class="d-inline-block me-2 mt-2" title="' + file.original_name + ' (' + fileSize + ')">';
                                     html += '<img src="' + fileUrl + '" class="rounded border shadow-sm" style="height: 60px; width: auto; max-width: 100px; object-fit: cover;" alt="' + file.original_name + '">';
                                     html += '</a>';
                                } else {
                                     html += '<a href="' + fileUrl + '" target="_blank" class="badge bg-light text-dark me-1 mb-1" title="' + file.original_name + ' (' + fileSize + ')">';
                                     html += '<i class="ti ti-file me-1"></i>' + file.original_name;
                                     html += '</a>';
                                }
                            });
                            html += '</div>';
                        }
                    } catch (e) {
                         // ignore error
                    }
                }

                if (activity.type !== 'history') {
                    html += '<div class="d-flex align-items-center justify-content-end st-btn">';
                    html += '<span class="badge ' + statusClass + '">' + activity.status + '</span>';
                    html += '</div>';
                }

                if (activity.type !== 'history') {
                    let actDateStr = activity.activity_date || activity.created_at;
                    if (actDateStr) {
                        let actDateObj = new Date((typeof actDateStr === 'string') ? actDateStr.replace(' ', 'T') : actDateStr);
                        if (!isNaN(actDateObj.getTime())) {
                            let timeString = actDateObj.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                            let dateString = actDateObj.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                            html += '<div class="mt-3 mb-1 text-muted" style="font-size: 13px;">';
                            html += '<i class="ti ti-calendar-time me-1"></i>Task Date: <span class="fw-medium text-dark">' + dateString + ' ' + timeString + '</span>';
                            html += '</div>';
                        }
                    }
                }

                if (activity.type !== 'history') {
                    if (activity.status !== 'Close') {
                        html += '<div class="btn-list mt-2">';
                        
                        let showMarkAsDone = true;
                        if (activity.type === 'task' && activity.status !== 'Open') {
                            showMarkAsDone = false;
                        }
                        
                        if (showMarkAsDone) {
                            html += '<button type="button" class="btn btn-sm btn-success mark-activity-done" data-id="' + activity.id + '" data-type="' + activity.type + '">Mark as done</button>';
                        }
                        
                        html += '<button type="button" class="btn btn-sm btn-primary edit-activity" data-id="' + activity.id + '">Edit</button>';
                        html += '<button type="button" class="btn btn-sm btn-danger delete-activity" data-id="' + activity.id + '">Delete</button>';
                        html += '</div>';
                    } else {
                        // Display Closed By Info
                        if (activity.closed_by_user || activity.completed_date) {
                             let closedText = 'Closed';
                             if (activity.closed_by_user) {
                                 closedText += ' by <span class="fw-medium text-dark">' + activity.closed_by_user.name + '</span>';
                             }
                             if (activity.completed_date) {
                                 const closedDate = new Date(activity.completed_date);
                                 const dateStr = closedDate.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                                 const timeStr = closedDate.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: true });
                                 closedText += ' on ' + dateStr + ' ' + timeStr;
                             }
                             html += '<div class="mt-0 text-muted fs-12 bg-light p-2 rounded d-inline-block">';
                             html += '<i class="ti ti-check me-1 text-success"></i>' + closedText;
                             html += '</div>';
                        }
                    }
                }

                html += '</div>'; // Close card-body
                if (activity.type === 'email' && activity.description) {
                     const emailMatch = activity.description.match(/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/);
                     if (emailMatch) {
                         const senderEmail = emailMatch[0];
                         let subjectText = activity.subject || 'Email';
                         if (subjectText.startsWith('Email Received: ')) {
                             subjectText = subjectText.replace('Email Received: ', '');
                         }
                         if (subjectText.startsWith('Email Sent: ')) {
                             subjectText = subjectText.replace('Email Sent: ', '');
                         }
                         if (subjectText.startsWith('Replied Back: ')) {
                             subjectText = subjectText.replace('Replied Back: ', '');
                         }
                         const replySubject = subjectText.startsWith('Re:') ? subjectText : 'Re: ' + subjectText;
                         html += '<div class="mt-3 mb-1 d-flex align-items-center">';
                         html += `<button type="button" class="btn btn-sm btn-outline-primary" onclick='$("#compose_to_email").val("${senderEmail}"); $("#compose_subject").val("${replySubject.replace(/"/g, "&quot;")}"); $("#compose_email_modal").modal("show");'><i class="ti ti-arrow-back-up me-1"></i>Reply</button>`;
                         
                         let paramsObj = {};
                         try {
                             paramsObj = typeof activity.params === 'string' ? JSON.parse(activity.params) : (activity.params || {});
                         } catch(e) {}
                         
                         if (paramsObj.gmail_message_id) {
                             html += `<button type="button" class="btn btn-sm btn-outline-info ms-2" style="white-space:nowrap;" onclick="viewEmailHtml(${paramsObj.gmail_message_id})"><i class="ti ti-eye me-1"></i>View Full HTML Email</button>`;
                         }
                         html += '</div>';
                     }
                }
                html += '</div>'; // Close card
                html += '</div>'; // Close flex-grow-1
                html += '</div>'; // Close filteredActivities loop item
            });
        });

        $container.html(html);
    }

    // Load activities when page loads or activities tab is clicked
    $(document).ready(function() {
        // Tab shown event handler
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr("data-bs-target"); // activated tab

            if (target === '#activities') {
                loadActivitiesForLead('all', '#activities-container');
            } else if (target === '#notes') {
                loadActivitiesForLead('note', '#notes-container');
            } else if (target === '#calls') {
                loadActivitiesForLead('call', '#calls-container');
            } else if (target === '#meetings') {
                loadActivitiesForLead('meeting', '#meetings-container');
            } else if (target === '#email') {
                loadActivitiesForLead('email', '#emails-container');
            } else if (target === '#files') {
                loadActivitiesForLead('files', '#files-container');
            } else if (target === '#wapp') { // Corrected ID for WhatsApp tab
                loadActivitiesForLead('whatsapp', '#whatsapp-container');
            } else if (target === '#general') {
                loadActivitiesForLead('general', '#general-container');
            } else if (target === '#receipts_history') {
                loadReceiptsForLead();
            }
        });

        if (currentLeadIdForActivity) {
            // Check active tab on load
            if ($('#activities').hasClass('active') || $('.nav-link[data-bs-target="#activities"]').hasClass('active')) {
                loadActivitiesForLead('all', '#activities-container');
            } else if ($('#notes').hasClass('active') || $('.nav-link[data-bs-target="#notes"]').hasClass('active')) {
                loadActivitiesForLead('note', '#notes-container');
            } else if ($('#calls').hasClass('active') || $('.nav-link[data-bs-target="#calls"]').hasClass('active')) {
                loadActivitiesForLead('call', '#calls-container');
            } else if ($('#files').hasClass('active') || $('.nav-link[data-bs-target="#files"]').hasClass('active')) {
                loadActivitiesForLead('files', '#files-container');
            }
            // Add initial load for other tabs if they might be active by default
            else if ($('#meetings').hasClass('active') || $('.nav-link[data-bs-target="#meetings"]').hasClass('active')) {
                loadActivitiesForLead('meeting', '#meetings-container');
            }
            else if ($('#email').hasClass('active') || $('.nav-link[data-bs-target="#email"]').hasClass('active')) {
                loadActivitiesForLead('email', '#emails-container');
            }
            else if ($('#wapp').hasClass('active') || $('.nav-link[data-bs-target="#wapp"]').hasClass('active')) {
                loadActivitiesForLead('whatsapp', '#whatsapp-container');
            }
            else if ($('#general').hasClass('active') || $('.nav-link[data-bs-target="#general"]').hasClass('active')) {
                loadActivitiesForLead('general', '#general-container');
            }
            else if ($('#receipts_history').hasClass('active') || $('.nav-link[data-bs-target="#receipts_history"]').hasClass('active')) {
                loadReceiptsForLead();
            }
        }
    });

    // Handle mark as done click
    $(document).on('click', '.mark-activity-done', function() {
        const activityId = $(this).data('id');
        console.log('Mark Activity Done Clicked. ID:', activityId);

        // Find full activity details from global cache
        const activity = window.allLeadActivities ? window.allLeadActivities.find(a => a.id == activityId) : null;
        console.log('Activity Found:', activity); // Debug log

        if (activity) {
            $('#mark_done_activity_id').val(activity.id);
            $('#mark_done_remarks').val(activity.description || activity.remarks || '');



            // Sync options from main form
            // Sync options from main form
            const $mainStatus = $('#activity_lead_status_id');
            if ($mainStatus.length && $mainStatus.children('option').length > 1) {
                 $('#mark_done_lead_status').html($mainStatus.html());
            }

            // Populate Lead Status
            let leadStatusId = null;
            if (typeof window.globalLeadStatusId !== 'undefined' && window.globalLeadStatusId) {
                leadStatusId = window.globalLeadStatusId;
            }
            if (!leadStatusId && typeof currentLeadData !== 'undefined' && currentLeadData && currentLeadData.lead) {
                leadStatusId = currentLeadData.lead.lead_status_id || currentLeadData.lead.status_id;
            }
            if (!leadStatusId) {
                leadStatusId = $('#activity_lead_status_id').val();
            }

            console.log('Populating Lead Status with ID:', leadStatusId);

            if (leadStatusId) {
                 $('#mark_done_lead_status').val(leadStatusId).trigger('change', [window.globalLeadSubStatusId]);
            } else {
                $('#mark_done_lead_status').val('').trigger('change');
            }

            // Determine Activity Setting ID
            let settingId = null;
            if (activity.activity_type_data && activity.activity_type_data.activity_setting_id) {
                 settingId = activity.activity_type_data.activity_setting_id;
            } else if (activity.activity_setting_id) {
                 settingId = activity.activity_setting_id;
            }
            console.log('Derived Setting ID:', settingId); // Debug log

            // Fetch Activity Settings
            $.ajax({
                url: "{{ route('activities.options', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'GET',
                data: { lead_id: activity.lead_id },
                success: function(res) {
                    const settingSelect = $('#mark_done_activity_setting');
                    const followupTypeSelect = $('#mark_done_followup_type');

                    settingSelect.empty().append('<option value="">--Select--</option>');
                    followupTypeSelect.empty().append('<option value="">Select Task</option>');

                    if(res.success && res.data && res.data.activity_settings) {
                        $.each(res.data.activity_settings, function(key, item) {
                             if(item.id !== 'all' && item.title.toLowerCase() !== 'task') {
                                const option = '<option value="'+item.id+'">'+item.title+'</option>';
                                settingSelect.append(option);
                                followupTypeSelect.append(option);
                             }
                        });
                    }

                    // Select Setting and trigger chain
                    let matched = false;
                    if(settingId) {
                        settingSelect.val(String(settingId)); // Force string
                        if (settingSelect.val()) {
                             console.log('Triggering Setting Change for ID:', settingId);
                             // Pass ID, Status (null), and Type Text (activity.type)
                             settingSelect.trigger('change', [activity.activity_type, null, activity.type]);
                             matched = true;
                        }
                    }

                    // Fallback: Match by Text (Type)
                    if (!matched && activity.type) {
                        const typeText = activity.type.toLowerCase();
                        settingSelect.find('option').each(function() {
                             if ($(this).text().trim().toLowerCase().includes(typeText)) {
                                 $(this).prop('selected', true);
                                 settingSelect.trigger('change', [activity.activity_type, null, activity.type]);
                                 return false; // break
                             }
                        });
                    }
                },
                error: function(err) {
                     console.error('Error fetching settings:', err);
                }
            });

        } else {
            console.warn('Activity cache missing for ID:', activityId);
            // Fallback strategy if not found in cache
            $('#mark_done_activity_id').val(activityId);
            const activityType = $(this).data('type') || '';
            // Subject field removed

            $('#mark_done_activity_setting').empty().append('<option value="">--Select--</option>');
            $('#mark_done_activity_type').empty().append('<option value="">--Select--</option>');
            $('#mark_done_activity_status').empty().append('<option value="">--Select--</option>');
        }

        // Reset valid/invalid classes
        $('#markActivityDoneForm .is-invalid').removeClass('is-invalid');
        $('#mark_done_create_followup').prop('checked', false);
        $('#mark_done_followup_section').hide();
        // $('#mark_done_submit_btn').hide();

        $('#mark_activity_done_modal').modal('show');
    });

    // Handle Activity Setting Change
    $('#mark_done_activity_setting').on('change', function(e, preselectedTypeId = null, preselectedStatusId = null, preselectedTypeText = null) {
        const settingId = $(this).val();
        console.log('Setting Changed. ID:', settingId, 'PreSelectType:', preselectedTypeId, 'PreSelectText:', preselectedTypeText);

        const typeSelect = $('#mark_done_activity_type');
        typeSelect.empty().append('<option value="">--Select--</option>');
        $('#mark_done_activity_status').empty().append('<option value="">--Select--</option>');

        if (settingId) {
             $.ajax({
                url: "{{ route('activities.types', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'GET',
                data: { activity_setting_id: settingId },
                success: function(res) {
                    if(res.success && res.data) {
                         $.each(res.data, function(key, item) {
                            typeSelect.append('<option value="'+item.id+'">'+item.title+'</option>');
                        });
                    }
                    let typeMatched = false;
                    // Try ID Match
                    if (preselectedTypeId) {
                        typeSelect.val(String(preselectedTypeId));
                        if(typeSelect.val()) {
                            typeSelect.trigger('change', [preselectedStatusId]);
                            typeMatched = true;
                        }
                    }
                    // Try Text Match (passed explicit text)
                    if (!typeMatched && preselectedTypeText) {
                         const typeText = preselectedTypeText.toLowerCase();
                         typeSelect.find('option').each(function() {
                             if ($(this).text().trim().toLowerCase().includes(typeText)) {
                                 $(this).prop('selected', true);
                                 typeSelect.trigger('change', [preselectedStatusId]);
                                 typeMatched = true;
                                 return false;
                             }
                         });
                    }
                    // Legacy Fallback (if ID was string)
                    if (!typeMatched && preselectedTypeId && typeof preselectedTypeId === 'string') {
                         const typeText = preselectedTypeId.toLowerCase();
                         typeSelect.find('option').each(function() {
                             if ($(this).text().trim().toLowerCase().includes(typeText)) {
                                 $(this).prop('selected', true);
                                 typeSelect.trigger('change', [preselectedStatusId]);
                                 return false;
                             }
                         });
                    }
                }
            });
        }
    });

    // Handle Activity Type Change
    $('#mark_done_activity_type').on('change', function(e, preselectedStatusId = null) {
        const typeId = $(this).val();
        console.log('Type Changed. ID:', typeId, 'PreSelectStatus:', preselectedStatusId);

        const statusSelect = $('#mark_done_activity_status');
        statusSelect.empty().append('<option value="">--Select--</option>');

        if (typeId) {
             $.ajax({
                url: "{{ route('activities.statuses', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'GET',
                data: { activity_type_id: typeId },
                success: function(res) {
                    if(res.success && res.data) {
                        $.each(res.data, function(key, item) {
                            statusSelect.append('<option value="'+item.id+'">'+item.title+'</option>');
                        });
                    }
                    if (preselectedStatusId) {
                        statusSelect.val(String(preselectedStatusId)).trigger('change');
                    }
                }
            });
        }
    });

    // Handle Follow-up Type Change (Show/Hide Meeting Date)
    // Handle Follow-up Type Change (Show/Hide Meeting Date)
    $(document).on('change', '#mark_done_followup_type', function() {
        $('#mark_done_followup_meeting_date_field').slideUp();
        $('#mark_done_followup_meeting_date').val('');
    });

    // Toggle follow up section
    $('#mark_done_create_followup').on('change', function() {
        if ($(this).is(':checked')) {
            $('#mark_done_followup_section').slideDown();
        } else {
            $('#mark_done_followup_section').slideUp();
        }
    });

    // Handle Mark Done Form Submit
    $('#markActivityDoneForm').on('submit', function(e) {
        e.preventDefault();

        const activityId = $('#mark_done_activity_id').val();
         const boxSlug = "{{ request()->route('box_slug') }}";
        const url = `/${boxSlug}/activities/${activityId}`;

        const formData = {
            status: 'Close',
            activity_setting_id: $('#mark_done_activity_setting').val(),
            activity_type: $('#mark_done_activity_type').val(),
            activity_status_id: $('#mark_done_activity_status').val(),
            status: 'Close',
            lead_status_id: $('#mark_done_lead_status').val(),
            lead_sub_status_id: $('#mark_done_lead_sub_status').val(),
            create_followup: $('#mark_done_create_followup').is(':checked'),

            // New Follow-up fields using done_ prefix (renamed to mark_done_)
            followup_type: $('#mark_done_followup_type').val(),
            followup_date: $('#mark_done_followup_date').val(),
            followup_meeting_date: $('#mark_done_followup_meeting_date').val(),
            followup_assigned_to: $('#mark_done_followup_assigned_to').val(),
            followup_remarks: $('#mark_done_followup_remarks').val(),

            remarks: $('#mark_done_remarks').val(),

            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'PUT'
        };

        if (formData.create_followup) {
             if (!formData.followup_type) {
                showToast('Please select a task type for the follow-up.', 'danger');
                return;
             }
             if (!formData.followup_date) {
                showToast('Please select a date for the follow-up task.', 'danger');
                return;
             }
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(res) {
                if (res.success) {
                    $('#mark_activity_done_modal').modal('hide');
                    showToast('Activity has been updated successfully!', 'success');
                    refreshCurrentActivityTab();
                } else {
                    showToast('Failed to update activity: ' + res.message, 'danger');
                }
            },
            error: function(xhr) {
                console.error(xhr);
                showToast('An error occurred while updating the activity.', 'danger');
            }
        });
    });

    // Handle delete activity
    $(document).on('click', '.delete-activity', function() {
        const activityId = $(this).data('id');
        if (confirm('Are you sure you want to delete this activity?')) {
            const boxSlug = "{{ request()->route('box_slug') }}";
            const deleteUrl = '/' + boxSlug + '/activities/' + activityId;
            $.ajax({
                url: deleteUrl,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    _method: 'DELETE'
                },
                success: function(res) {
                    if (res.success) {
                        refreshCurrentActivityTab();
                        showToast('Activity deleted successfully!', 'success');
                    }
                },
                error: function(xhr) {
                    console.error('Delete activity error:', xhr);
                    showToast('Failed to delete activity. ' + (xhr.responseJSON?.message || xhr.statusText), 'danger');
                }
            });
        }
    });

    // Handle Edit activity button click
    $(document).on('click', '.edit-activity', function() {
        const activityId = $(this).data('id');
        const activity = window.allLeadActivities.find(a => a.id == activityId);

        if (!activity) return;

        console.log('Populating Edit Form for Activity:', activity);

        $('#edit_activity_id').val(activity.id);
        $('#edit_assigned_to').val(activity.assigned_to).trigger('change');
        $('#edit_subject').val(activity.subject);
        $('#edit_description').val(activity.description);

        // Determine setting ID (Try activity_setting_id first, then from activity_type_data)
        let settingId = activity.activity_setting_id;
        if (!settingId && activity.activity_type_data) {
             settingId = activity.activity_type_data.activity_setting_id;
        }

        // Trigger chain for Setting -> Type -> Status
        if (settingId) {
             console.log('Setting Setting ID for Edit:', settingId);
             $('#edit_activity_setting_id').val(settingId).trigger('change', [activity.activity_type, activity.activity_status_id]);
        }

        // Populate Lead Status and Sub Status
        let leadStatusId = activity.lead_status_id;
        let leadSubStatusId = activity.lead_sub_status_id;

        if (activity.params) {
            try {
                const params = typeof activity.params === 'string' ? JSON.parse(activity.params) : activity.params;
                if (!leadStatusId && params.lead_status_id) leadStatusId = params.lead_status_id;
                if (!leadSubStatusId && params.lead_sub_status_id) leadSubStatusId = params.lead_sub_status_id;
            } catch(e) {
                console.error('Error parsing activity params for status:', e);
            }
        }

        console.log('Edit Activity Clicked. IDs to populate:', {
            activityId: activity.id,
            leadStatusId: leadStatusId,
            leadSubStatusId: leadSubStatusId,
            currentLeadStatus: currentLeadData?.lead?.lead_status_id
        });

        if (leadStatusId) {
             $('#edit_activity_lead_status_id').val(leadStatusId).trigger('change', [leadSubStatusId]);
        } else if (typeof currentLeadData !== 'undefined' && currentLeadData && currentLeadData.lead && currentLeadData.lead.lead_status_id) {
             console.log('Falling back to lead status from currentLeadData:', currentLeadData.lead.lead_status_id);
             $('#edit_activity_lead_status_id').val(currentLeadData.lead.lead_status_id).trigger('change', [currentLeadData.lead.lead_sub_status_id]);
        } else {
             $('#edit_activity_lead_status_id').val('').trigger('change');
        }

         // Format date for datetime-local input (YYYY-MM-DDTHH:mm)
        if (activity.activity_date) {
            const date = new Date(activity.activity_date);
            const formattedDate = date.toISOString().slice(0, 16);
            $('#edit_activity_date').val(formattedDate);
        }

        if (activity.params) {
             try {
                const params = typeof activity.params === 'string' ? JSON.parse(activity.params) : activity.params;
                $('#edit_guests').val(params.guests || '');
                $('#edit_location').val(params.location || '');
             } catch(e) {}
        }

        // Select radio button
        $('input[name="type"][value="' + activity.type + '"]').prop('checked', true).trigger('change');

        // Reset Follow up
        $('#edit_create_followup').prop('checked', false);
        $('#edit_followup_section').hide();
        $('#edit_followup_type').val('call').trigger('change');
        $('#edit_followup_date').val('');
        $('#edit_followup_assigned_to').val('').trigger('change');
        $('#edit_followup_remarks').val('');

        // Show offcanvas
        var offcanvasElement = document.getElementById('offcanvas_edit');
        var bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
        bsOffcanvas.show();
    });

    // Handle Edit Form Follow up Checkbox
    $('#edit_create_followup').on('change', function() {
        if ($(this).is(':checked')) {
            $('#edit_followup_section').slideDown();
        } else {
             $('#edit_followup_section').slideUp();
        }
    });

    // Handle Edit Form Submission
    $('#editActivityForm').on('submit', function(e) {
        e.preventDefault();

        const activityId = $('#edit_activity_id').val();
        const boxSlug = "{{ request()->route('box_slug') }}";
        const url = `/${boxSlug}/activities/${activityId}`;

        var formData = new FormData(this);
        formData.append('_method', 'PUT');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
             headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.success) {
                    var offcanvasElement = document.getElementById('offcanvas_edit');
                    var bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    if (bsOffcanvas) bsOffcanvas.hide();

                    refreshCurrentActivityTab();
                    showToast('Activity updated successfully!', 'success');
                } else {
                    showToast(res.message, 'danger');
                }
            },
            error: function(xhr) {
                 showToast('Failed to update: ' + (xhr.responseJSON?.message || xhr.statusText), 'danger');
            }
        });
    });

    // Handle Edit Activity Type Change to show/hide fields (similar to Add)
     $('#editActivityForm input[name="type"]').on('change', function() {
        const type = $(this).val();
        $('#editActivityForm .field-call, #editActivityForm .field-meeting, #editActivityForm .field-whatsapp, #editActivityForm .field-email, #editActivityForm .field-note, #editActivityForm .field-general').hide();

        // Always show these
        $('#editActivityForm #edit_subject').closest('.col-lg-12').show();
        $('#editActivityForm #edit_description').closest('.col-lg-12').show();
         $('#editActivityForm #edit_assigned_to').closest('.col-md-6').show();
         $('#edit_activity_date').closest('.col-md-6').show();

        // Type specific logic matching the Add form structure (simplified here based on provided layout)
        if (type === 'call') {
            $('#editActivityForm .field-call').show();
        } else if (type === 'meeting') {
            $('#editActivityForm .field-meeting').show();
        } else if (type === 'whatsapp') {
            $('#editActivityForm .field-whatsapp').show();
        } else if (type === 'note') {
            $('#editActivityForm .field-note').show();
        } else if (type === 'email') {
            // ...
        } else if (type === 'note') {
             // ...
        }
    });

    // Handle Add Activity Offcanvas Show - Set Defaults
    var offcanvasAdd = document.getElementById('offcanvas_add');
    if (offcanvasAdd) {
        offcanvasAdd.addEventListener('show.bs.offcanvas', function () {
            // Ensure Activity Status is empty (null)
            $('#activity_status_id').val('').trigger('change');

            // Pre-select Lead Status and Sub Status from global variables
            if (typeof window.globalLeadStatusId !== 'undefined' && window.globalLeadStatusId) {
                 $('#activity_lead_status_id').val(window.globalLeadStatusId).trigger('change', [window.globalLeadSubStatusId]);
            }
        });
    }

    // Variables moved to top
</script>
@endpush

@include('frontend.employess.components.designation_tree_modal')
@include('frontend.leads.partials.add_lead_form_js')
@include('accounts.partials.add_invoice_modal', ['prefill_lead' => $lead])
@include('accounts.partials.invoice_scripts', ['onSuccess' => 'window.location.reload();'])

@endsection


@push('scripts')
<script>
    function showToast(message, type) {
        type = type || 'success';
        const toastEl = document.getElementById('leadToast');
        const toastBody = document.getElementById('toastMessage');
        if (toastEl && toastBody) {
             toastBody.textContent = message;
             toastEl.className = 'toast align-items-center text-bg-' + type + ' border-0';
             const toast = new bootstrap.Toast(toastEl);
             toast.show();
        }
    }

    function refreshCurrentActivityTab() {
        window.allLeadActivities = []; // Clear cache
        const activeTab = $('.nav-link.active[data-bs-toggle="tab"]').attr('data-bs-target');
        if (activeTab === '#calls') {
            loadActivitiesForLead('call', '#calls-container');
        } else if (activeTab === '#meetings') {
            loadActivitiesForLead('meeting', '#meetings-container');
        } else if (activeTab === '#email') {
            loadActivitiesForLead('email', '#emails-container');
        } else if (activeTab === '#whatsapp') {
            loadActivitiesForLead('whatsapp', '#whatsapp-container');
        } else if (activeTab === '#receipts_history') {
            loadReceiptsForLead();
        } else {
             loadActivitiesForLead('all', '#activities-container');
        }
    }
</script>
<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCix326VJYzGXyMzF7_yTD_vbn5DBMHZL4&libraries=places,drawing,geometry&language=en" async defer></script>

<script>
    function getDistanceInMeters(lat1, lon1, lat2, lon2) {
      if ((lat1 == lat2) && (lon1 == lon2)) return 0;
      var radlat1 = Math.PI * lat1/180;
      var radlat2 = Math.PI * lat2/180;
      var theta = lon1-lon2;
      var radtheta = Math.PI * theta/180;
      var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
      if (dist > 1) dist = 1;
      dist = Math.acos(dist);
      dist = dist * 180/Math.PI;
      dist = dist * 60 * 1.1515;
      dist = dist * 1.609344; 
      return dist * 1000;
    }
</script>

<script>
    // Working Location Script
    $(document).ready(function() {
        const WORKING_COUNTRY_ID = 101; // India ID
        const BLUE_MARKER_ICON = 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png';
        
        let map = null;
        let drawingManager = null;
        let drawnShape = null; // Store manually drawn shape
        let searchMarker = null;
        let searchCircle = null; // Can be Circle or Polygon from search OR drawing
        let infoWindow = null;
        let savedObjects = []; // Markers, Circles
        let savedDataFeatures = []; // Data Layer Features (Polygons)
        let pendingObjects = []; 
        let locationDataFromSearch = null;
        let bounds = null;
        let currentSearchRequest = null;

        function clearMapObjects(objectsArray) {
            if (objectsArray) {
                objectsArray.forEach(function(obj) { obj.setMap(null); });
                objectsArray.length = 0;
            }
        }
        
        function clearDataFeatures(featuresArray) {
            if (featuresArray && map) {
                featuresArray.forEach(function(f) { map.data.remove(f); });
                featuresArray.length = 0;
            }
        }
        
        function clearSearchAndDrawn() {
            if (searchMarker) { searchMarker.setMap(null); searchMarker = null; }
            if (searchCircle) { searchCircle.setMap(null); searchCircle = null; }
            if (drawnShape) { drawnShape.setMap(null); drawnShape = null; }
            map.data.forEach(function(feature) { if (!savedDataFeatures.includes(feature)) { map.data.remove(feature); } });
            locationDataFromSearch = null;
        }

        function createCustomMarker(position, color, label) {
             return new google.maps.Marker({
                position: position,
                map: map,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 8,
                    fillColor: color,
                    fillOpacity: 1,
                    strokeWeight: 2,
                    strokeColor: '#FFFFFF'
                },
                title: label || ''
            });
        }
        
        function initDrawingManager() {
            if (!map) return;
            
            drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: null, // Start with no drawing mode
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [
                        google.maps.drawing.OverlayType.CIRCLE,
                        google.maps.drawing.OverlayType.POLYGON,
                    ],
                },
                circleOptions: {
                    fillColor: '#FFFF00',
                    fillOpacity: 0.15,
                    strokeColor: '#FFFF00',
                    strokeWeight: 2,
                    clickable: false,
                    editable: true,
                    zIndex: 1
                },
                polygonOptions: {
                    fillColor: '#FFFF00',
                    fillOpacity: 0.15,
                    strokeColor: '#FFFF00',
                    strokeWeight: 2,
                    clickable: false,
                    editable: true,
                    zIndex: 1
                }
            });
            drawingManager.setMap(map);

            google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
                clearSearchAndDrawn(); // Clear previous
                
                drawnShape = event.overlay;
                var type = event.type;
                
                if (type === google.maps.drawing.OverlayType.CIRCLE) {
                    var center = drawnShape.getCenter();
                    var radius = drawnShape.getRadius();
                    locationDataFromSearch = { lat: center.lat(), lng: center.lng() };
                    
                    // Assign to global var expected by Save logic
                    searchCircle = drawnShape;
                    
                    // Add center marker
                    searchMarker = new google.maps.Marker({
                        position: center,
                        map: map,
                        icon: BLUE_MARKER_ICON,
                        draggable: false
                    });

                } else if (type === google.maps.drawing.OverlayType.POLYGON) {
                    // For Polygon, calculating center is trickier. Use bounds center.
                    var bounds = new google.maps.LatLngBounds();
                    drawnShape.getPath().forEach(function(latLng) {
                        bounds.extend(latLng);
                    });
                    var center = bounds.getCenter();
                    locationDataFromSearch = { lat: center.lat(), lng: center.lng() };
                    
                    // Assign to global usually used for Circle, but here it's the shape
                    searchCircle = drawnShape; // Logic adaptation
                }
                
                // Switch back to non-drawing mode after one shape
                drawingManager.setDrawingMode(null);
            });
        }

        // ... existing functions (fetchAndRenderWorkingLocations) ...
        
        // Listen to Radio Changes to set Drawing Mode preference
        $('input[name="working_loc_type"]').on('change', function() {
            var val = $(this).val();
            if (drawingManager) {
                if (val === 'radius') {
                    drawingManager.setDrawingMode(google.maps.drawing.OverlayType.CIRCLE);
                } else if (val === 'area') {
                    drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
                } else {
                    drawingManager.setDrawingMode(null);
                }
            }
            startWorkingLocationSearch(); // Re-trigger search logic if needed
        });


        function fetchAndRenderWorkingLocations() {
             var leadId = typeof currentLeadIdForActivity !== 'undefined' && currentLeadIdForActivity
                         ? currentLeadIdForActivity
                         : (new URLSearchParams(window.location.search)).get('lead_id');
            
            clearMapObjects(savedObjects);
            clearDataFeatures(savedDataFeatures);
            bounds = new google.maps.LatLngBounds();

            $.ajax({
                url: "{{ route('activities.get_working_data', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'GET',
                data: { 
                    country_id: WORKING_COUNTRY_ID,
                    lead_id: leadId
                },
                success: function(res) {
                     if (res.success && res.data) {
                         // 1. Populate States (if empty)
                         if (res.data.states && $('#working_state_id').children('option').length <= 1) {
                             const stateSelect = $('#working_state_id');
                             stateSelect.empty().append('<option value="">-- Select State --</option>');
                             $.each(res.data.states, function(index, item) {
                                  stateSelect.append('<option value="'+item.id+'">'+item.name+'</option>');
                             });
                             
                             let defaultStateId = null;
                             if (typeof currentLeadData !== 'undefined' && currentLeadData && currentLeadData.lead && currentLeadData.lead.state_id) {
                                 defaultStateId = currentLeadData.lead.state_id;
                             }
                             stateSelect.val(defaultStateId).trigger('change');
                         }

                         // 2. Populate Divisions (if empty)
                         if (res.data.divisions && $('#working_division_id').children('option').length <= 1) {
                             const divisionSelect = $('#working_division_id');
                             divisionSelect.empty().append('<option value="">-- Select Division --</option>');
                             $.each(res.data.divisions, function(index, item) {
                                  divisionSelect.append('<option value="'+item.id+'">'+item.name+'</option>');
                             });
                             divisionSelect.val(null).trigger('change');
                         }

                         // 3. Render Saved Locations
                         const tableBody = $('#working_location_table_body');
                         tableBody.empty();

                         if (res.data.locations && res.data.locations.length > 0) {
                            res.data.locations.forEach(function(loc) {
                                if (loc.is_current_lead) {
                                    const stateName = (loc.state) ? loc.state.name : '--';
                                    const cityName = (loc.city) ? loc.city.name : '--';
                                    const divName = (loc.division) ? loc.division.name : '--';

                                    tableBody.append(`<tr>
                                        <td>${stateName}</td>
                                        <td>${cityName}</td>
                                        <td>${divName}</td>
                                        <td><button type="button" class="btn btn-sm btn-danger delete-working-location-btn" data-id="${loc.id}">Delete</button></td>
                                    </tr>`);
                                }

                                var lat = parseFloat(loc.lat);
                                var lng = parseFloat(loc.lng);
                                var radius = parseFloat(loc.radius);

                                if (!isNaN(lat) && !isNaN(lng) && (lat !== 0 || lng !== 0)) {
                                    var pos = { lat: lat, lng: lng };
                                    bounds.extend(pos);

                                    var divisionName = loc.division ? loc.division.name : 'Unknown Division';
                                    var companyName = (loc.division && loc.division.company) ? loc.division.company.bill_name : '';
                                    var details = '<div style="color:#333; font-size:12px;">';
                                    if (companyName) details += '<b>Company:</b> ' + companyName + '<br>';
                                    details += '<b>Division:</b> ' + divisionName;
                                    if (loc.name) details += '<br><b>Location:</b> ' + loc.name;
                                    details += '<br><b>Radius:</b> ' + radius + 'm</div>';

                                    var isCurrent = loc.is_current_lead;
                                    var color = isCurrent ? '#28a745' : '#dc3545';

                                    var m = createCustomMarker(pos, color, divisionName);
                                    m.addListener('mouseover', function() {
                                        if (infoWindow) infoWindow.close();
                                        infoWindow = new google.maps.InfoWindow({ content: details });
                                        infoWindow.open(map, m);
                                    });
                                    m.addListener('mouseout', function() { if (infoWindow) infoWindow.close(); });
                                    savedObjects.push(m);

                                    var locType = loc.location_type || 'radius';
                                    if (locType !== 'area' && !isNaN(radius) && radius > 0) {
                                        var c = new google.maps.Circle({
                                            map: map,
                                            center: pos,
                                            radius: radius,
                                            strokeColor: color,
                                            strokeOpacity: 0.8,
                                            strokeWeight: 2,
                                            fillColor: color,
                                            fillOpacity: 0.15
                                        });
                                        savedObjects.push(c);
                                    } else if (locType === 'area') {
                                        // 1. Try to use saved Polygon Path
                                        if (loc.polygon_paths) {
                                            try {
                                                var paths = JSON.parse(loc.polygon_paths);
                                                if (paths && paths.length > 0) {
                                                     var savedPoly = new google.maps.Polygon({
                                                        paths: paths,
                                                        strokeColor: color, // Green/Red
                                                        strokeOpacity: 0.8,
                                                        strokeWeight: 2,
                                                        fillColor: color,
                                                        fillOpacity: 0.15,
                                                        map: map,
                                                        editable: false,
                                                        zIndex: isCurrent ? 10 : 1 // Highlight current lead
                                                    });
                                                    
                                                    // Extend bounds
                                                    if (savedPoly.getPath()) {
                                                        savedPoly.getPath().forEach(function(latLng) {
                                                            bounds.extend(latLng);
                                                        });
                                                    }
                                                    savedObjects.push(savedPoly);
                                                }
                                            } catch(e) { console.error('Error parsing saved polygon', e); }
                                        } else {
                                            // 2. Fallback: Fetch geometry if not stored
                                            var areaParts = [];
                                            if (loc.city && loc.city.name) areaParts.push(loc.city.name);
                                            if (loc.state && loc.state.name) areaParts.push(loc.state.name);
                                            areaParts.push('India');
                                            
                                            if (areaParts.length > 1) {
                                                 $.get('https://nominatim.openstreetmap.org/search', {
                                                    q: areaParts.join(', '),
                                                    format: 'json',
                                                    limit: 1,
                                                    polygon_geojson: 1
                                                }, function(data) {
                                                    if (data && data.length > 0 && data[0].geojson) {
                                                        try {
                                                            var features = map.data.addGeoJson(data[0].geojson);
                                                            features.forEach(function(f) {
                                                                f.setProperty('fillColor', color);
                                                                f.setProperty('strokeColor', color);
                                                                savedDataFeatures.push(f);
                                                            });
                                                        } catch(e) {}
                                                    }
                                                });
                                            }
                                        }
                                    }
                                }
                            });
                            if (!bounds.isEmpty()) {
                                map.fitBounds(bounds);
                            }
                         }
                      }
                },
                error: function(err) { showToast('Failed to load working data.', 'danger'); }
            });
        }

        $('#working_location').on('show.bs.modal', function () {
             $('#working_state_id, #working_city_id, #working_division_id').select2({
                 width: '100%',
                 dropdownParent: $('#working_location')
            });

            if (!map) {
                map = new google.maps.Map(document.getElementById('working_location_map'), {
                    center: { lat: 20.5937, lng: 78.9629 },
                    zoom: 5,
                    mapTypeControl: false,
                    streetViewControl: false
                });
                
                // Configure Dynamic Data Layer Style
                map.data.setStyle(function(feature) {
                    return {
                        fillColor: feature.getProperty('fillColor') || 'yellow',
                        strokeColor: feature.getProperty('strokeColor') || 'yellow',
                        strokeWeight: 2,
                        fillOpacity: 0.15
                    };
                });
                
                initDrawingManager(); // Initialize Drawing Tools
            }

            fetchAndRenderWorkingLocations();
        });

        $('#working_location').on('shown.bs.modal', function () {
            if (map) {
                google.maps.event.trigger(map, "resize");
                if (bounds && !bounds.isEmpty()) {
                    map.fitBounds(bounds);
                }
            }
        });

        $('#working_state_id').on('change', function() {
            var stateIds = $(this).val();
            if (!stateIds || stateIds.length === 0) {
                 $('#working_city_id').empty().trigger('change');
                 return;
            }
            var stateIdParam = Array.isArray(stateIds) ? stateIds.join(',') : stateIds;
            $.ajax({
                url: "{{ route('company.city.select', ['box_slug' => request()->route('box_slug')]) }}", 
                type: 'POST',
                data: { state_id: stateIdParam, _token: $('meta[name="csrf-token"]').attr('content') },
                success: function(res) {
                     const citySelect = $('#working_city_id');
                     citySelect.empty().append('<option value="">-- Select City --</option>');
                     var cities = res.data || res; 
                     if (cities) {
                         $.each(cities, function(key, value) {
                              var id = (typeof value === 'object' && value !== null) ? value.id : key;
                              var name = (typeof value === 'object' && value !== null) ? value.name : value;
                              citySelect.append('<option value="'+id+'">'+name+'</option>');
                         });
                     }
                     
                     let defaultCityId = null;
                     if (typeof currentLeadData !== 'undefined' && currentLeadData && currentLeadData.lead && currentLeadData.lead.city_id) {
                         defaultCityId = currentLeadData.lead.city_id;
                     }
                     citySelect.val(defaultCityId).trigger('change');
                }
            });
        });

        var currentSearchSequence = 0;

        function startWorkingLocationSearch() {
             var stateData = $('#working_state_id').select2('data');
             var cityData  = $('#working_city_id').select2('data');
             var state = (stateData && stateData.length > 0) ? stateData[0].text : '';
             var city  = (cityData && cityData.length > 0) ? cityData[0].text : '';
             if (!state || state.includes('-- Select')) state = '';
             if (!city || city.includes('-- Select')) city = '';
             var locationParts = [];
             if (city) locationParts.push(city);
             if (state) locationParts.push(state);
             locationParts.push('India');

             if (locationParts.length > 1) {
                  var searchQuery = locationParts.join(', ');
                  var locType = $('input[name="working_loc_type"]:checked').val();
                  
                  // Toggle help text
                  if (locType === 'radius') {
                      $('#working_loc_radius_help').show();
                  } else {
                      $('#working_loc_radius_help').hide();
                  }
                  
                  // Use helper to clear all previous visualizations (Search or Drawn)
                  clearSearchAndDrawn();
                  
                  // Temporarily hide saved circles/markers to focus on search? No, keep them.
                  
                  var mySearchSequence = ++currentSearchSequence;
                  
                  if (locType === 'area') {
                       // Use Nominatim for Polygon GeoJSON
                       if (currentSearchRequest) { currentSearchRequest.abort(); }
                       currentSearchRequest = $.get('https://nominatim.openstreetmap.org/search', {
                           q: searchQuery,
                           format: 'json',
                           limit: 1,
                           polygon_geojson: 1
                       }, function(data) {
                           if (mySearchSequence !== currentSearchSequence) return; // Prevent async race conditions
                           
                           if (data && data.length > 0) {
                               var lat = parseFloat(data[0].lat);
                               var lon = parseFloat(data[0].lon);
                               var latLng = { lat: lat, lng: lon };
                               locationDataFromSearch = latLng; 

                               // Center marker (Blue)
                               searchMarker = new google.maps.Marker({
                                   position: latLng,
                                   map: map,
                                   icon: BLUE_MARKER_ICON,
                                   draggable: false, // Center of admin area usually shouldn't move
                                   animation: google.maps.Animation.DROP
                               });
                               
                               if (data[0].geojson && (data[0].geojson.type === 'Polygon' || data[0].geojson.type === 'MultiPolygon')) {
                                   var paths = [];
                                   
                                   // Helper to convert [lon, lat] to {lat, lng}
                                   var convertCoords = function(coords) {
                                       return coords.map(function(c) { return { lat: c[1], lng: c[0] }; });
                                   };

                                   if (data[0].geojson.type === 'Polygon') {
                                       // GeoJSON Polygon: [ [ [lon, lat], ... ], [hole] ]
                                       // Usually index 0 is the outer ring
                                       data[0].geojson.coordinates.forEach(function(ring) {
                                            paths.push(convertCoords(ring));
                                       });
                                   } else if (data[0].geojson.type === 'MultiPolygon') {
                                       // MultiPolygon: [ [ [ [lon, lat] ] ] ]
                                       data[0].geojson.coordinates.forEach(function(polygon) {
                                           polygon.forEach(function(ring) {
                                               paths.push(convertCoords(ring));
                                           });
                                       });
                                   }
                                   
                                   try {
                                       // Create Polygon with user requested style (Yellow)
                                       searchCircle = new google.maps.Polygon({
                                           paths: paths,
                                           strokeColor: "#FFFF00", 
                                           strokeOpacity: 0.8,
                                           strokeWeight: 2,
                                           fillColor: "#FFFF00",
                                           fillOpacity: 0.2,
                                           map: map,
                                           editable: false // Disable editable to avoid messy vertex handles on complex admin boundaries
                                       });

                                       // Fit bounds
                                       var bounds = new google.maps.LatLngBounds();
                                       searchCircle.getPaths().forEach(function(path) {
                                           path.forEach(function(latlng) {
                                               bounds.extend(latlng);
                                           });
                                       });
                                       map.fitBounds(bounds);

                                   } catch(e) { 
                                       console.error('Polygon Error', e); 
                                       map.setCenter(latLng); 
                                       map.setZoom(12);
                                   }
                               } else {
                                   map.setCenter(latLng);
                                   map.setZoom(12);
                               }
                           } else {
                               showToast('Location not found.', 'warning');
                           }
                       }).fail(function(jqXHR, textStatus) { if (textStatus !== 'abort' && mySearchSequence === currentSearchSequence) showToast('Error searching for area.', 'danger'); });
                  } else {
                       // Standard Google Geocoding for Radius
                       var geocoder = new google.maps.Geocoder();
                       geocoder.geocode({ address: searchQuery }, function(results, status) {
                           if (mySearchSequence !== currentSearchSequence) return; // Prevent async race conditions
                           
                           if (status === 'OK') {
                               var pos = results[0].geometry.location;
                               locationDataFromSearch = { lat: pos.lat(), lng: pos.lng() };
                               
                               searchMarker = new google.maps.Marker({
                                   position: pos,
                                   map: map,
                                   icon: BLUE_MARKER_ICON, // Blue Marker
                                   draggable: true,
                                   animation: google.maps.Animation.DROP
                               });
                               
                               searchMarker.addListener('dragend', function() {
                                   locationDataFromSearch = { lat: searchMarker.getPosition().lat(), lng: searchMarker.getPosition().lng() };
                                   if (searchCircle) searchCircle.setCenter(searchMarker.getPosition());
                               });

                               var radius = 25000; 
                               searchCircle = new google.maps.Circle({
                                    map: map,
                                    center: pos,
                                    radius: radius,
                                    fillColor: '#FFFF00', // Yellow Fill
                                    fillOpacity: 0.15,
                                    strokeColor: '#FFFF00', // Yellow Stroke
                                    strokeWeight: 2,
                                    editable: true
                               });
                               map.fitBounds(searchCircle.getBounds());
                           } else {
                               showToast('Location not found.', 'warning');
                           }
                       });
                  }
             }
        }

        $('#working_location_search_btn').on('click', startWorkingLocationSearch);
        $('#working_city_id').on('change', startWorkingLocationSearch);
        $('#working_state_id').on('change', function() { setTimeout(startWorkingLocationSearch, 500); });

        $(document).on('click', '.delete-working-location-btn', function() {
             window.locationToDeleteId = $(this).data('id'); 
             $('#delete_working_location_modal').modal('show');
        });

        $('#confirm_delete_location_btn').on('click', function() {
             var id = window.locationToDeleteId;
             if (!id) return;
             var btn = $(this);
             btn.prop('disabled', true).text('Deleting...');
             $.ajax({
                 url: "{{ route('activities.delete_location', ['box_slug' => request()->route('box_slug')]) }}",
                 type: 'DELETE',
                 data: { id: id, _token: '{{ csrf_token() }}' },
                 success: function(res) {
                     if (res.success) {
                         showToast('Location deleted successfully.', 'success');
                         fetchAndRenderWorkingLocations(); 
                         $('#delete_working_location_modal').modal('hide');
                     } else { showToast(res.message || 'Failed to delete.', 'danger'); }
                     btn.prop('disabled', false).text('Yes, Delete it');
                 },
                 error: function() { showToast('Error deleting location.', 'danger'); btn.prop('disabled', false).text('Yes, Delete it'); }
             });
        });

        var pendingLocations = [];

        $('#working_location_save_btn').on('click', function() {
            var stateIds    = $('#working_state_id').val();
            var cityIds     = $('#working_city_id').val();
            var divisionIds = $('#working_division_id').val();
            var stateName   = $('#working_state_id option:selected').text();
            var cityName    = $('#working_city_id option:selected').text();
            var divisionName= $('#working_division_id option:selected').text();
            
            if (!stateIds || !cityIds || !divisionIds) {
                showToast('State, City and Division are required.', 'danger');
                return;
            }

            var latLng = searchMarker ? { lat: searchMarker.getPosition().lat(), lng: searchMarker.getPosition().lng() } : locationDataFromSearch;
            
            var radius = 25000;
            var polygonPath = null;

            if (searchCircle) {
                if (typeof searchCircle.getRadius === 'function') {
                    // Circle
                    radius = searchCircle.getRadius();
                } else {
                    // Polygon
                    radius = 0; 

                    if (typeof searchCircle.getPaths === 'function') {
                        // Handle Multi-paths (Holes or MultiPolygon)
                        var allPaths = [];
                        searchCircle.getPaths().forEach(function(path) {
                            var pathCoords = [];
                            path.forEach(function(latLng) {
                                pathCoords.push({ lat: latLng.lat(), lng: latLng.lng() });
                            });
                            allPaths.push(pathCoords);
                        });
                        polygonPath = JSON.stringify(allPaths);
                    } else if (typeof searchCircle.getPath === 'function') {
                        // Fallback for simple path
                         var pathArr = [];
                         searchCircle.getPath().forEach(function(latLng) {
                             pathArr.push({ lat: latLng.lat(), lng: latLng.lng() });
                         });
                         polygonPath = JSON.stringify(pathArr);
                    }
                }
            }
            
            if (!latLng || !latLng.lat) {
                  showToast('Please search location on map.', 'warning');
                  return;
            }

            var leadId = typeof currentLeadIdForActivity !== 'undefined' && currentLeadIdForActivity
                         ? currentLeadIdForActivity
                         : (new URLSearchParams(window.location.search)).get('lead_id');
            
            if (!leadId || leadId === "null") {
                showToast('Lead ID missing.', 'danger');
                return;
            }

            var btn = $(this);
            btn.prop('disabled', true).text('Adding...');
            var locType = $('input[name="working_loc_type"]:checked').val();

            $.ajax({
                url: "{{ route('activities.add_location', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'POST',
                data: {
                    lead_id: leadId,
                    state_id: stateIds,
                    city_id: cityIds,
                    division_id: divisionIds,
                    lat: latLng.lat,
                    lng: latLng.lng,
                    radius: radius,
                    location_type: locType, // Renaming to location_type to be safer or keep 'type'? Let's keep 'type' if that's what backend expects, but user asked for 'complete changes'. 
                    // Let's assume 'type' is correct based on previous code usually using 'type'. But wait, previous code had 'type: locType'.
                    type: locType,
                    polygon_paths: polygonPath, // Sending the path
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                     if (res.success) {
                         showToast('Division Area added.', 'success');
                         pendingLocations.push({
                            state_id: stateIds,
                            city_id: cityIds,
                            point: { division: { id: divisionIds }, id: null, division_area_id: res.data.id },
                            lat: latLng.lat,
                            lng: latLng.lng,
                            radius: radius,
                            polygon_paths: polygonPath,
                            type: locType
                         });

                         $('#working_location_table_body').append(`<tr>
                             <td>${stateName}</td>
                             <td>${cityName}</td>
                             <td>${divisionName} <span class="badge bg-warning">Unsaved</span></td>
                         </tr>`);

                         var pMarker = createCustomMarker(latLng, 'orange', divisionName);
                         pendingObjects.push(pMarker);
                         
                         if (searchCircle) {
                             if (typeof searchCircle.getPaths === 'function') {
                                 // It's a Polygon
                                 var pPolygon = new google.maps.Polygon({
                                     paths: searchCircle.getPaths(),
                                     strokeColor: 'orange', 
                                     fillColor: 'orange', 
                                     fillOpacity: 0.1,
                                     map: map
                                 });
                                 pendingObjects.push(pPolygon);
                             } else {
                                 // It's a Circle
                                 var pCircle = new google.maps.Circle({
                                     map: map, center: latLng, radius: radius,
                                     strokeColor: 'orange', fillColor: 'orange', fillOpacity: 0.1
                                 });
                                 pendingObjects.push(pCircle);
                             }
                         }

                         if (searchMarker) { searchMarker.setMap(null); searchMarker = null; }
                         if (searchCircle) { searchCircle.setMap(null); searchCircle = null; }
                         locationDataFromSearch = null;

                         $('#working_state_id').val(null).trigger('change');
                         $('#working_division_id').val(null).trigger('change');
                         
                     } else { showToast(res.message || 'Failed to add.', 'danger'); }
                     btn.prop('disabled', false).text('Add Division');
                },
                error: function() { showToast('Error adding division.', 'danger'); btn.prop('disabled', false).text('Add Division'); }
            });
        });

        $('#workingLocationForm').on('submit', function(e) {
            e.preventDefault();
             if (pendingLocations.length === 0) { $('#working_location').modal('hide'); return; }

            var leadId = typeof currentLeadIdForActivity !== 'undefined' && currentLeadIdForActivity
                         ? currentLeadIdForActivity
                         : (new URLSearchParams(window.location.search)).get('lead_id');

            var btn = $(this).find('button[type="submit"]');
            var originalText = btn.text();
            btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ route('activities.save_location', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'POST',
                data: {
                    lead_id: leadId,
                    location_data: pendingLocations,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                     if (res.success) {
                         showToast('Saved successfully!', 'success');
                         pendingLocations = [];
                         clearMapObjects(pendingObjects);
                         fetchAndRenderWorkingLocations();
                         $('#working_location').modal('hide');
                     } else { showToast(res.message || 'Failed to save.', 'danger'); }
                     btn.prop('disabled', false).text(originalText);
                }
            });
        });

        // --- Receipt Creation Logic ---
        $('#btn_create_receipt').on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const leadId = urlParams.get('lead_id') || @json($leadId ?? null);
            
            if (!leadId) {
                showToast('Lead ID not found.', 'danger');
                return;
            }

            // Reset state for Create
            $('#receiptOffcanvasTitle').text('Create New Receipt');
            $('#receipt_add_form').attr('action', "{{ route('accounts.receipts.store', ['box_slug' => request()->route('box_slug') ?? 'default']) }}");
            $('#receipt_action_type').val('store');
            $('#receipt_id').val('');

            // Show offcanvas
            const offcanvasEl = document.getElementById('offcanvas_receipt_add');
            const bsOffcanvas = new bootstrap.Offcanvas(offcanvasEl);
            bsOffcanvas.show();

            // Reset Form
            $('#receipt_add_form')[0].reset();
            $('#receipt_add_form .select').val(null).trigger('change');
            
            // Set Lead ID
            const leadSelect = $('#receipt_lead_select');
            leadSelect.empty().append(`<option value="${leadId}" selected>Current Lead</option>`).trigger('change');

            // Fetch Companies
            $.ajax({
                url: "{{ route('accounts.receipts.form-data', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
                type: "GET",
                data: { fetch_initial: 1 },
                success: function(response) {
                    const companySelect = $('#receipt_company_select');
                    companySelect.empty().append('<option selected disabled>-- Choose --</option>');
                    if(response.companies) {
                        response.companies.forEach(function(company) {
                            companySelect.append(`<option value="${company.id}">${company.bill_name}</option>`);
                        });
                    }
                    companySelect;
                }
            });
        });

        // Initialize Select2 for Receipts
        $('#offcanvas_receipt_add .select').select2({
            dropdownParent: $('#offcanvas_receipt_add'),
            width: '100%'
        });

        // Handle Company Change for Bank/Division
        $('#receipt_company_select').on('change', function() {
            const companyId = $(this).val();
            if(!companyId) return;

            $.ajax({
                url: "{{ route('accounts.receipts.form-data', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
                type: "GET",
                data: { company_id: companyId },
                success: function(response) {
                    const bankSelect = $('#receipt_bank_select');
                    bankSelect.empty().append('<option selected disabled>-- Choose --</option>');
                    if(response.banks) {
                        response.banks.forEach(function(bank) {
                            bankSelect.append(`<option value="${bank.id}">${bank.name}</option>`);
                        });
                    }
                    bankSelect;

                    const divisionSelect = $('#receipt_division_select');
                    divisionSelect.empty().append('<option selected disabled>-- Choose --</option>');
                    if(response.divisions) {
                        response.divisions.forEach(function(div) {
                            divisionSelect.append(`<option value="${div.id}">${div.name}</option>`);
                        });
                    }
                    divisionSelect;
                }
            });
        });

        // Submit Receipt Form
        $('#receipt_add_form').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#receipt_submit_btn');
            btn.prop('disabled', true).text('Submitting...');

            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        bootstrap.Offcanvas.getInstance(document.getElementById('offcanvas_receipt_add')).hide();
                        // Reload activities to show the new receipt if it's logged as activity
                        if (typeof loadActivitiesForLead === 'function') {
                            loadActivitiesForLead('all', '#activities-container');
                        }
                    } else {
                        showToast(response.message || 'Failed to create receipt.', 'danger');
                    }
                    btn.prop('disabled', false).text('Submit');
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error occurred.';
                    showToast(msg, 'danger');
                    btn.prop('disabled', false).text('Submit');
                }
            });
        });

        // Edit Receipt Handler
        $(document).on('click', '.btn-edit-receipt', function(e) {
            e.preventDefault();
            const receiptId = $(this).data('id');
            const data = typeof $(this).data('receipt') === 'string' ? JSON.parse($(this).data('receipt')) : $(this).data('receipt');

            // Set state for Edit
            $('#receiptOffcanvasTitle').text('Update Receipt');
            $('#receipt_add_form').attr('action', "{{ route('accounts.receipts.update', ['box_slug' => request()->route('box_slug') ?? 'default']) }}");
            $('#receipt_action_type').val('update');
            $('#receipt_id').val(receiptId);
            
            // Reset form
            $('#receipt_add_form')[0].reset();
            $('#receipt_add_form .select').val(null).trigger('change');

            // Populate form fields manually (inject options since we have the data)
            const leadSelect = $('#receipt_lead_select');
            leadSelect.empty().append(`<option value="${data.lead_id}" selected>${"{{ $lead->name ?? '' }}"}</option>`);
            // We intentionally don't trigger change here to avoid overriding our manual population below

            const companySelect = $('#receipt_company_select');
            if (data.company) {
                companySelect.empty().append(`<option value="${data.company_id}" selected>${data.company.bill_name || data.company.company_name}</option>`);
            } else {
                companySelect.val(data.company_id);
            }

            const divisionSelect = $('#receipt_division_select');
            if (data.division) {
                divisionSelect.empty().append(`<option value="${data.division_id}" selected>${data.division.name}</option>`);
            } else {
                divisionSelect.val(data.division_id);
            }

            // Date formatting
            if (data.receipt_date) {
                let rDate = data.receipt_date;
                if (rDate.includes('T')) rDate = rDate.split('T')[0];
                else if (rDate.includes(' ')) rDate = rDate.split(' ')[0]; 
                
                // Convert YYYY-MM-DD to DD/MM/YYYY for flatpickr or direct assignment
                let parts = rDate.split('-');
                if (parts.length === 3) {
                    rDate = parts[1] + '/' + parts[2] + '/' + parts[0]; // MM/DD/YYYY
                }
                $('input[name="receipt_date"]').val(rDate);
            }

            $('input[name="amount"]').val(data.amount);
            $('input[name="invoice_number"]').val(data.invoice_number);
            $('#receipt_payment_mode').val(data.payment_mode).trigger('change');
            
            if (data.bank_id && data.bank) {
                $('#receipt_bank_select').empty().append(`<option value="${data.bank_id}" selected>${data.bank.name}</option>`);
            } else if (data.bank_id) {
                $('#receipt_bank_select').val(data.bank_id).trigger('change');
            }
            
            $('input[name="reference"]').val(data.reference);
            $('textarea[name="note"]').val(data.note);

            // Show offcanvas
            const offcanvasEl = document.getElementById('offcanvas_receipt_add');
            const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
            offcanvas.show();
        });

        // Delete Receipt Handler
        $(document).on('click', '.btn-delete-receipt', function(e) {
            e.preventDefault();
            const receiptId = $(this).data('id');
            const url = "{{ route('accounts.receipts.destroy', ['box_slug' => request()->route('box_slug') ?? 'default']) }}";

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: { id: receiptId },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            if (response.success) {
                                showToast(response.message || 'Deleted successfully', 'success');
                                loadReceiptsForLead();
                                if (typeof fetchAndPopulateReceiptsChart === 'function') fetchAndPopulateReceiptsChart(); 
                            } else {
                                showToast(response.message || 'Failed to delete', 'danger');
                            }
                        },
                        error: function(xhr) {
                            showToast('Error deleting receipt', 'danger');
                        }
                    });
                }
            });
        });
    });
        $(document).on('submit', '#composeEmailForm', function(e) {
            e.preventDefault();
            if ($.fn.summernote) {
                $('#compose_body').val($('#compose_body').summernote('code'));
            }
            
            const bodyContent = $('#compose_body').val();
            if (!bodyContent || bodyContent.trim() === '' || bodyContent === '<p><br></p>') {
                showToast('Please enter a message.', 'danger');
                return;
            }
            
            const $btn = $('#btnSendEmail');
            const originalText = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending...');
            
            $.ajax({
                url: "{{ route('google.send_email', request()->route('box_slug')) }}",
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        $('#compose_email_modal').modal('hide');
                        $('#composeEmailForm')[0].reset();
                        if ($.fn.summernote) {
                            $('#compose_body').summernote('code', '');
                        }
                        $('#compose_followup_section').hide();
                        loadActivitiesForLead('email', '#emails-container');
                        loadActivitiesForLead('all', '#activities-container');
                        showToast(res.message, 'success');
                    } else {
                        showToast(res.message, 'danger');
                    }
                },
                error: function(err) {
                    const msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'An error occurred';
                    showToast(msg, 'danger');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });
function viewEmailHtml(gmailMessageId) {
    const boxSlug = "{{ request()->route('box_slug') }}";
    const iframe = document.getElementById('view_email_iframe');
    iframe.src = '/' + boxSlug + '/google/view-email-html/' + gmailMessageId;
    $('#view_full_email_modal').modal('show');
}

$(document).ready(function() {
    if ($.fn.summernote) {
        $('#compose_body').summernote({
            height: 250,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    }
});

$(document).on('change', '#compose_create_followup', function() {
    if ($(this).is(':checked')) {
        $('#compose_followup_section').slideDown();
    } else {
        $('#compose_followup_section').slideUp();
    }
});

$(document).on('change', '.activity-sort-select', function() {
    const filterType = $(this).data('filter');
    const containerId = $(this).data('container');
    const sortOrder = $(this).val();
    if (window.allLeadActivities && window.allLeadActivities.length > 0) {
        renderActivitiesForLead(window.allLeadActivities, filterType, containerId, sortOrder);
    }
});

$(document).on('submit', '#sendWhatsAppTemplateForm', function(e) {
    e.preventDefault();
    let form = $(this);
    let submitBtn = form.find('button[type="submit"]');
    let templateId = form.find('select[name="template_id"]').val();
    let leadId = form.find('input[name="lead_id"]').val();
    
    if(!templateId) return;
    
    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');
    
    $.ajax({
        url: '{{ box_route('whatsapp.send_template_direct') }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            lead_id: leadId,
            template_id: templateId
        },
        success: function(response) {
            submitBtn.prop('disabled', false).html('Send');
            if (response.success) {
                toastr.success(response.message);
                loadActivitiesForLead('whatsapp', '#whatsapp-container'); // Reload activities
            } else {
                toastr.error(response.message || 'Failed to send template.');
            }
        },
        error: function(xhr) {
            submitBtn.prop('disabled', false).html('Send');
            toastr.error('An error occurred. Please try again later.');
        }
    });
});
</script>
@endpush
