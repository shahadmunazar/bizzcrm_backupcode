@extends('layouts.main')
@section('title', 'WhatsApp Create Template')
@section('content')

    
		<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-4">
									<h4 class="page-title">WhatsApp Templates</h4>
								</div>
								<div class="col-8 text-end">
									<div class="head-icons">
										<a href="{{ box_route('whatsapp.campaign') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Go to Campaigns">
											<i class="ti ti-brand-campaignmonitor"></i>
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
										<div class="col-sm-5">
											 <div class="d-flex align-items-center">
												<div class="icon-form mb-3 mb-sm-0 flex-fill me-2">
													<span class="form-icon"><i class="ti ti-search"></i></span>
													<input type="text" id="searchTemplate" class="form-control" placeholder="Search Template">
												</div>
												<div class="dropdown">
													<button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
														<i class="ti ti-filter me-1"></i>Filters
													</button>
													<div class="dropdown-menu p-3" style="min-width: 300px;">
														<h6 class="mb-3">Filter Templates</h6>
														<div class="mb-3">
															<label class="form-label small">Account</label>
															<select class="form-control form-control-sm filter-input" id="filter_waba_id">
																<option value="all">All Accounts</option>
																@foreach($accounts as $account)
																	<option value="{{ $account->waba_id }}">{{ $account->name }}</option>
																@endforeach
															</select>
														</div>
														<div class="mb-3">
															<label class="form-label small">Category</label>
															<select class="form-control form-control-sm filter-input" id="filter_category">
																<option value="all">All Categories</option>
																<option value="MARKETING">Marketing</option>
																<option value="UTILITY">Utility</option>
																<option value="AUTHENTICATION">Authentication</option>
															</select>
														</div>
														<div class="d-flex justify-content-between align-items-center mt-3">
															<button class="btn btn-link btn-sm p-0 text-muted" id="btnClearFilters">Clear All</button>
															<button class="btn btn-primary btn-sm" id="btnApplyFilters">Apply</button>
														</div>
													</div>
												</div>
											</div> 
										</div>
										<div class="col-sm-7">
											<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
												<div class="dropdown me-2">
													<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
													<div class="dropdown-menu  dropdown-menu-end">
														<ul>
															<li>
																<a href="{{ box_route('whatsapp.business.template.export.pdf') }}" class="dropdown-item"><i class="ti ti-file-type-pdf text-danger me-1"></i>Export
																	as PDF</a>
															</li>
															<li>
																<a href="{{ box_route('whatsapp.business.template.export.excel') }}" class="dropdown-item"><i class="ti ti-file-type-xls text-green me-1"></i>Export
																	as Excel </a>
															</li>
														</ul>
													</div>
												</div>
												<div class="dropdown me-3">
													<button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
														<i class="ti ti-brand-meta me-2"></i>Meta Library
													</button>
													<div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 250px;">
														<label class="form-label small">Select Meta Account</label>
														<select class="form-control form-control-sm mb-2" id="sync_waba_id">
															@foreach($accounts as $account)
																<option value="{{ $account->waba_id }}">{{ $account->name }}</option>
															@endforeach
														</select>
                                                        <div class="d-grid gap-2">
														    <button class="btn btn-primary btn-sm" id="btnSyncTemplates"><i class="ti ti-refresh me-1"></i>Sync All</button>
														    <button class="btn btn-outline-primary btn-sm" id="btnExploreMeta"><i class="ti ti-world-download me-1"></i>Explore & Fetch</button>
                                                        </div>
													</div>
												</div>
												<div class="me-2">
													<button type="button" class="btn btn-light d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
														<i class="ti ti-columns me-1"></i>Manage Columns
													</button>
												</div>
												<a href="{{ box_route('whatsapp.business.template.create') }}" class="btn btn-primary"><i class="ti ti-square-rounded-plus me-2"></i>New Template</a>
											</div>
										</div>
									</div>
									
							</div>
						</div> 
						
						
						
						

						<!-- Campaign Tab -->
						<div class="campaign-tab">
							<ul class="nav">
								<li class=""><a class="active" href="#bottom-justified-tab1" data-bs-toggle="tab" data-status="all">All</a></li>
								<li class=""><a class="" href="#bottom-justified-tab2" data-bs-toggle="tab" data-status="draft">Draft</a></li>
								<li class=""><a class="" href="#bottom-justified-tab3" data-bs-toggle="tab" data-status="pending">Pending</a></li>
								<li class=""><a class="" href="#bottom-justified-tab4" data-bs-toggle="tab" data-status="approved">Approved</a></li>
								<li class=""><a class="" href="#bottom-justified-tab5" data-bs-toggle="tab" data-status="rejected">Rejected</a></li>
								<li class=""><a class="" href="#bottom-justified-tab-explore" data-bs-toggle="tab" data-status="explore">Explore Template</a></li>
							</ul>
						</div>
						<!-- Campaign Tab -->
						
						
						
					<div class="tab-content">
					<div class="tab-pane show active" id="bottom-justified-tab1">
						<div class="card">
							
							<div class="card-body">
								
								<!-- /Filter -->

								<!-- Campaign List -->
								<div class="table-responsive custom-table">
									<table class="table" id="">
										<thead class="thead-light">
										<tr>
												<th class="no-sort col-checkbox">
													<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
												</th>
												<th class="col-star"></th>
												<th class="col-name">Name</th>
												<th class="col-account">Account</th>
												<th class="col-phone">Phone Number</th>
												<th class="col-category">Category</th>
												<th class="col-status">Status</th>
												<th class="col-message">Message Body</th>
												<th class="col-meta-id">Meta ID</th>
												<th class="col-created">Created At</th>
												<th class="col-approved">Approved At</th>
												<th class="text-start col-action">Action</th>
										</tr>
										
										</thead>
										<tbody class="templateTableBody">
											<tr>
												<td colspan="11" class="text-center p-5">
													<div class="spinner-border text-primary" role="status"></div>
													<div class="mt-2">Loading templates...</div>
												</td>
											</tr>
										</tbody>
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
								

								<!-- Campaign List -->
								<div class="table-responsive custom-table">
									<table class="table" id="">
										<thead class="thead-light">
										<tr>
												<th class="no-sort">
													<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
												</th>
												<th></th>
												<th>Name</th>
												<th>Category</th>
												<th>Status</th>
												<th>Type</th>
												<th>Health</th>
												<th>Created At</th>
												<th>Approved At</th>
												<th class="text-start">Action</th>
										</tr>
										
										<tbody class="templateTableBody">
										</tbody>
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
								

								<!-- Campaign List -->
								<div class="table-responsive custom-table">
									<table class="table" id="">
										<thead class="thead-light">
										<tr>
												<th class="no-sort">
													<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
												</th>
												<th></th>
												<th>Name</th>
												<th>Category</th>
												<th>Status</th>
												<th>Type</th>
												<th>Health</th>
												<th>Created At</th>
												<th>Approved At</th>
												<th class="text-start">Action</th>
										</tr>
										
										<tbody class="templateTableBody">
										</tbody>
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
								

								<!-- Campaign List -->
								<div class="table-responsive custom-table">
									<table class="table" id="">
										<thead class="thead-light">
										<tr>
												<th class="no-sort">
													<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
												</th>
												<th></th>
												<th>Name</th>
												<th>Category</th>
												<th>Status</th>
												<th>Type</th>
												<th>Health</th>
												<th>Created At</th>
												<th>Approved At</th>
												<th class="text-start">Action</th>
										</tr>
										
										<tbody class="templateTableBody">
										</tbody>
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
								

							</div>
						</div>
						</div>
						<div class="tab-pane" id="bottom-justified-tab5">
							<div class="card">
								<div class="card-body">
									<div class="table-responsive custom-table">
										<table class="table" id="">
											<thead class="thead-light">
												<tr>
													<th class="no-sort">
														<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
													</th>
													<th></th>
													<th>Name</th>
													<th>Category</th>
													<th>Status</th>
													<th>Type</th>
													<th>Health</th>
													<th>Created At</th>
													<th>Approved At</th>
													<th class="text-start">Action</th>
												</tr>
											</thead>
											<tbody class="templateTableBody"></tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="bottom-justified-tab-explore">
							<div class="row">
								<!-- Sidebar Filters -->
								<div class="col-md-3">
									<div class="card sticky-top" style="top: 20px;">
										<div class="card-header bg-white border-bottom">
											<h6 class="mb-0">Library Filters</h6>
										</div>
										<div class="card-body p-0">
											<div class="p-3 border-bottom d-flex justify-content-between align-items-center">
												<label class="form-label fw-bold small text-uppercase mb-0">Meta Account</label>
												<button class="btn btn-link btn-sm p-0 text-primary text-decoration-none" id="btnResetExplore"><i class="ti ti-refresh-dot me-1"></i>Reset</button>
											</div>
											<div class="p-3 border-bottom">
												<select class="form-control form-control-sm" id="explore_tab_waba_id">
													<option value="all">🌏 All Linked Accounts</option>
													@foreach($accounts as $account)
														<option value="{{ $account->waba_id }}">{{ $account->name }}</option>
													@endforeach
												</select>
											</div>

											<div class="p-3 border-bottom">
												<label class="form-label fw-bold text-dark fs-15 mb-3">Category</label>
												<div class="category-filters">
													<div class="form-check mb-3 d-flex align-items-center">
														<input class="form-check-input explore-filter me-2" type="radio" name="explore_category" value="MARKETING" id="cat_marketing" checked style="width: 20px; height: 20px;">
														<label class="form-check-label fs-14" for="cat_marketing">Marketing <span class="text-muted small" id="cnt_cat_marketing"></span></label>
													</div>
													<div class="form-check mb-3 d-flex align-items-center">
														<input class="form-check-input explore-filter me-2" type="radio" name="explore_category" value="UTILITY" id="cat_utility" style="width: 20px; height: 20px;">
														<label class="form-check-label fs-14" for="cat_utility">Utility <span class="text-muted small" id="cnt_cat_utility"></span></label>
													</div>
													<div class="form-check mb-3 d-flex align-items-center">
														<input class="form-check-input explore-filter me-2" type="radio" name="explore_category" value="AUTHENTICATION" id="cat_auth" style="width: 20px; height: 20px;">
														<label class="form-check-label fs-14" for="cat_auth">Authentication <span class="text-muted small" id="cnt_cat_auth"></span></label>
													</div>
												</div>
											</div>

											<!-- Industry Accordion -->
											<div class="accordion accordion-flush" id="filterAccordion">
												<div class="accordion-item border-bottom">
													<h2 class="accordion-header" id="headingIndustry">
														<button class="accordion-button collapsed fw-bold text-dark fs-15 flex-row-reverse justify-content-between p-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseIndustry" aria-expanded="false" aria-controls="collapseIndustry" style="box-shadow: none; background: transparent;">
															Industry
														</button>
													</h2>
													<div id="collapseIndustry" class="accordion-collapse collapse" aria-labelledby="headingIndustry" data-bs-parent="#filterAccordion">
														<div class="accordion-body p-3 pt-0">
															<div class="form-check mb-3">
																<input class="form-check-input explore-filter" type="checkbox" value="e-commerce" id="ind_ecommerce" style="width: 18px; height: 18px;">
																<label class="form-check-label ms-1" for="ind_ecommerce">E-commerce <span class="text-muted small" id="cnt_ind_e-commerce"></span></label>
															</div>
															<div class="form-check mb-3">
																<input class="form-check-input explore-filter" type="checkbox" value="financial" id="ind_financial" style="width: 18px; height: 18px;">
																<label class="form-check-label ms-1" for="ind_financial">Financial services <span class="text-muted small" id="cnt_ind_financial"></span></label>
															</div>
															<div class="form-check mb-3">
																<input class="form-check-input explore-filter" type="checkbox" value="telecom" id="ind_telecom" style="width: 18px; height: 18px;">
																<label class="form-check-label ms-1" for="ind_telecom">Telecommunication <span class="text-muted small" id="cnt_ind_telecom"></span></label>
															</div>
														</div>
													</div>
												</div>

												<!-- Feature Accordion -->
												<div class="accordion-item border-bottom filter-cat-utility">
													<h2 class="accordion-header" id="headingFeature">
														<button class="accordion-button collapsed fw-bold text-dark fs-15 flex-row-reverse justify-content-between p-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFeature" aria-expanded="false" aria-controls="collapseFeature" style="box-shadow: none; background: transparent;">
															Feature
														</button>
													</h2>
													<div id="collapseFeature" class="accordion-collapse collapse show" aria-labelledby="headingFeature">
														<div class="accordion-body p-0 pt-0" style="max-height: 480px; overflow-y: auto;">
															<div class="accordion accordion-flush" id="nestedFeatureAccordion">
																<!-- Account or product protection -->
																<div class="accordion-item border-0">
																	<h2 class="accordion-header" id="hdg_feat_acc_prot">
																		<button class="accordion-button collapsed fw-bold text-dark fs-14 flex-row-reverse justify-content-between p-3" type="button" data-bs-toggle="collapse" data-bs-target="#col_feat_acc_prot" aria-expanded="false" aria-controls="col_feat_acc_prot" style="box-shadow: none; background: transparent; padding-top: 10px !important; padding-bottom: 10px !important;">
																			Account or product protection
																		</button>
																	</h2>
																	<div id="col_feat_acc_prot" class="accordion-collapse collapse" aria-labelledby="hdg_feat_acc_prot" data-bs-parent="#nestedFeatureAccordion">
																		<div class="accordion-body p-3 pt-0 pb-2">
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Fraud awareness" id="sub_1"><label class="form-check-label ms-1" for="sub_1">Fraud awareness <span class="text-muted small" id="cnt_sub_fraud_awareness"></span></label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Product recalls" id="sub_2"><label class="form-check-label ms-1" for="sub_2">Product recalls <span class="text-muted small" id="cnt_sub_product_recalls"></span></label></div>
																			<div class="form-check mb-0"><input class="form-check-input explore-filter" type="checkbox" value="Warranty alerts" id="sub_3"><label class="form-check-label ms-1" for="sub_3">Warranty alerts <span class="text-muted small" id="cnt_sub_warranty_alerts"></span></label></div>
																		</div>
																	</div>
																</div>

																<!-- Account updates -->
																<div class="accordion-item border-0">
																	<h2 class="accordion-header" id="hdg_feat_acc_upd">
																		<button class="accordion-button collapsed fw-bold text-dark fs-14 flex-row-reverse justify-content-between p-3" type="button" data-bs-toggle="collapse" data-bs-target="#col_feat_acc_upd" aria-expanded="false" aria-controls="col_feat_acc_upd" style="box-shadow: none; background: transparent; padding-top: 10px !important; padding-bottom: 10px !important;">
																			Account updates
																		</button>
																	</h2>
																	<div id="col_feat_acc_upd" class="accordion-collapse collapse" aria-labelledby="hdg_feat_acc_upd" data-bs-parent="#nestedFeatureAccordion">
																		<div class="accordion-body p-3 pt-0 pb-2">
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Account creation confirmation" id="sub_4"><label class="form-check-label ms-1" for="sub_4">Account creation confirmation (1)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Cancellation confirmation" id="sub_5"><label class="form-check-label ms-1" for="sub_5">Cancellation confirmation (1)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Network troubleshooting" id="sub_6"><label class="form-check-label ms-1" for="sub_6">Network troubleshooting (1)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Renewal reminder" id="sub_7"><label class="form-check-label ms-1" for="sub_7">Renewal reminder (2)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Renewal confirmation" id="sub_8"><label class="form-check-label ms-1" for="sub_8">Renewal confirmation (1)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Roaming reminder" id="sub_9"><label class="form-check-label ms-1" for="sub_9">Roaming reminder (1)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Service disruption" id="sub_10"><label class="form-check-label ms-1" for="sub_10">Service disruption (1)</label></div>
																			<div class="form-check mb-0"><input class="form-check-input explore-filter" type="checkbox" value="Upgrade confirmation" id="sub_11"><label class="form-check-label ms-1" for="sub_11">Upgrade confirmation (1)</label></div>
																		</div>
																	</div>
																</div>

																<!-- Call permissions -->
																<div class="accordion-item border-0">
																	<h2 class="accordion-header" id="hdg_feat_call">
																		<button class="accordion-button collapsed fw-bold text-dark fs-14 flex-row-reverse justify-content-between p-3" type="button" data-bs-toggle="collapse" data-bs-target="#col_feat_call" aria-expanded="false" aria-controls="col_feat_call" style="box-shadow: none; background: transparent; padding-top: 10px !important; padding-bottom: 10px !important;">
																			Call permissions
																		</button>
																	</h2>
																	<div id="col_feat_call" class="accordion-collapse collapse" aria-labelledby="hdg_feat_call" data-bs-parent="#nestedFeatureAccordion">
																		<div class="accordion-body p-3 pt-0 pb-2">
																			<div class="form-check mb-0"><input class="form-check-input explore-filter" type="checkbox" value="Call permission request" id="sub_12"><label class="form-check-label ms-1" for="sub_12">Call permission request (1)</label></div>
																		</div>
																	</div>
																</div>

																<!-- Customer feedback -->
																<div class="accordion-item border-0">
																	<h2 class="accordion-header" id="hdg_feat_feedback">
																		<button class="accordion-button collapsed fw-bold text-dark fs-14 flex-row-reverse justify-content-between p-3" type="button" data-bs-toggle="collapse" data-bs-target="#col_feat_feedback" aria-expanded="false" aria-controls="col_feat_feedback" style="box-shadow: none; background: transparent; padding-top: 10px !important; padding-bottom: 10px !important;">
																			Customer feedback
																		</button>
																	</h2>
																	<div id="col_feat_feedback" class="accordion-collapse collapse" aria-labelledby="hdg_feat_feedback" data-bs-parent="#nestedFeatureAccordion">
																		<div class="accordion-body p-3 pt-0 pb-2">
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Feedback survey" id="sub_13"><label class="form-check-label ms-1" for="sub_13">Feedback survey (5)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Follow up missed calls" id="sub_14"><label class="form-check-label ms-1" for="sub_14">Follow up missed calls (1)</label></div>
																			<div class="form-check mb-0"><input class="form-check-input explore-filter" type="checkbox" value="Ticket acknowledgement" id="sub_15"><label class="form-check-label ms-1" for="sub_15">Ticket acknowledgement (1)</label></div>
																		</div>
																	</div>
																</div>

																<!-- Event reminder -->
																<div class="accordion-item border-0">
																	<h2 class="accordion-header" id="hdg_feat_event">
																		<button class="accordion-button collapsed fw-bold text-dark fs-14 flex-row-reverse justify-content-between p-3" type="button" data-bs-toggle="collapse" data-bs-target="#col_feat_event" aria-expanded="false" aria-controls="col_feat_event" style="box-shadow: none; background: transparent; padding-top: 10px !important; padding-bottom: 10px !important;">
																			Event reminder
																		</button>
																	</h2>
																	<div id="col_feat_event" class="accordion-collapse collapse" aria-labelledby="hdg_feat_event" data-bs-parent="#nestedFeatureAccordion">
																		<div class="accordion-body p-3 pt-0 pb-2">
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Appointment cancellation" id="sub_16"><label class="form-check-label ms-1" for="sub_16">Appointment cancellation (2)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Appointment confirmation" id="sub_17"><label class="form-check-label ms-1" for="sub_17">Appointment confirmation (2)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Appointment reminder" id="sub_18"><label class="form-check-label ms-1" for="sub_18">Appointment reminder (2)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Appointment scheduling" id="sub_19"><label class="form-check-label ms-1" for="sub_19">Appointment scheduling (4)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Event details reminder" id="sub_20"><label class="form-check-label ms-1" for="sub_20">Event details reminder (2)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Event RSVP confirmation" id="sub_21"><label class="form-check-label ms-1" for="sub_21">Event RSVP confirmation (2)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Appointment missed" id="sub_22"><label class="form-check-label ms-1" for="sub_22">Appointment missed (1)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="(1)" id="sub_23"><label class="form-check-label ms-1" for="sub_23">(1)</label></div>
																			<div class="form-check mb-0"><input class="form-check-input explore-filter" type="checkbox" value="Technician arrival" id="sub_24"><label class="form-check-label ms-1" for="sub_24">Technician arrival (1)</label></div>
																		</div>
																	</div>
																</div>

																<!-- Group invitation link -->
																<div class="accordion-item border-0">
																	<h2 class="accordion-header" id="hdg_feat_group">
																		<button class="accordion-button collapsed fw-bold text-dark fs-14 flex-row-reverse justify-content-between p-3" type="button" data-bs-toggle="collapse" data-bs-target="#col_feat_group" aria-expanded="false" aria-controls="col_feat_group" style="box-shadow: none; background: transparent; padding-top: 10px !important; padding-bottom: 10px !important;">
																			Group invitation link
																		</button>
																	</h2>
																	<div id="col_feat_group" class="accordion-collapse collapse" aria-labelledby="hdg_feat_group" data-bs-parent="#nestedFeatureAccordion">
																		<div class="accordion-body p-3 pt-0 pb-2">
																			<div class="form-check mb-0"><input class="form-check-input explore-filter" type="checkbox" value="Group invitation upon request" id="sub_25"><label class="form-check-label ms-1" for="sub_25">Group invitation upon request (3)</label></div>
																		</div>
																	</div>
																</div>

																<!-- Legal/regulatory compliance -->
																<div class="accordion-item border-0">
																	<h2 class="accordion-header" id="hdg_feat_legal">
																		<button class="accordion-button collapsed fw-bold text-dark fs-14 flex-row-reverse justify-content-between p-3" type="button" data-bs-toggle="collapse" data-bs-target="#col_feat_legal" aria-expanded="false" aria-controls="col_feat_legal" style="box-shadow: none; background: transparent; padding-top: 10px !important; padding-bottom: 10px !important;">
																			Legal/regulatory compliance
																		</button>
																	</h2>
																	<div id="col_feat_legal" class="accordion-collapse collapse" aria-labelledby="hdg_feat_legal" data-bs-parent="#nestedFeatureAccordion">
																		<div class="accordion-body p-3 pt-0 pb-2">
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Identity compliance" id="sub_26"><label class="form-check-label ms-1" for="sub_26">Identity compliance (2)</label></div>
																			<div class="form-check mb-0"><input class="form-check-input explore-filter" type="checkbox" value="Privacy disclosures" id="sub_27"><label class="form-check-label ms-1" for="sub_27">Privacy disclosures (1)</label></div>
																		</div>
																	</div>
																</div>

																<!-- Order management -->
																<div class="accordion-item border-0">
																	<h2 class="accordion-header" id="hdg_feat_order">
																		<button class="accordion-button collapsed fw-bold text-dark fs-14 flex-row-reverse justify-content-between p-3" type="button" data-bs-toggle="collapse" data-bs-target="#col_feat_order" aria-expanded="false" aria-controls="col_feat_order" style="box-shadow: none; background: transparent; padding-top: 10px !important; padding-bottom: 10px !important;">
																			Order management
																		</button>
																	</h2>
																	<div id="col_feat_order" class="accordion-collapse collapse" aria-labelledby="hdg_feat_order" data-bs-parent="#nestedFeatureAccordion">
																		<div class="accordion-body p-3 pt-0 pb-2">
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Delivery update" id="sub_28"><label class="form-check-label ms-1" for="sub_28">Delivery update (7)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Delivery confirmation" id="sub_29"><label class="form-check-label ms-1" for="sub_29">Delivery confirmation (6)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Delivery failed" id="sub_30"><label class="form-check-label ms-1" for="sub_30">Delivery failed (3)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Return confirmation" id="sub_31"><label class="form-check-label ms-1" for="sub_31">Return confirmation (3)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Order confirmation" id="sub_32"><label class="form-check-label ms-1" for="sub_32">Order confirmation (12)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Order action needed" id="sub_33"><label class="form-check-label ms-1" for="sub_33">Order action needed (2)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Order or transaction cancel" id="sub_34"><label class="form-check-label ms-1" for="sub_34">Order or transaction cancel (4)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Order delay" id="sub_35"><label class="form-check-label ms-1" for="sub_35">Order delay (2)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Order pick-up" id="sub_36"><label class="form-check-label ms-1" for="sub_36">Order pick-up (4)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Shipment confirmation" id="sub_37"><label class="form-check-label ms-1" for="sub_37">Shipment confirmation (6)</label></div>
																			<div class="form-check mb-0"><input class="form-check-input explore-filter" type="checkbox" value="Order refund" id="sub_38"><label class="form-check-label ms-1" for="sub_38">Order refund (1)</label></div>
																		</div>
																	</div>
																</div>

																<!-- Payments -->
																<div class="accordion-item border-0">
																	<h2 class="accordion-header" id="hdg_feat_payments">
																		<button class="accordion-button collapsed fw-bold text-dark fs-14 flex-row-reverse justify-content-between p-3" type="button" data-bs-toggle="collapse" data-bs-target="#col_feat_payments" aria-expanded="false" aria-controls="col_feat_payments" style="box-shadow: none; background: transparent; padding-top: 10px !important; padding-bottom: 10px !important;">
																			Payments
																		</button>
																	</h2>
																	<div id="col_feat_payments" class="accordion-collapse collapse" aria-labelledby="hdg_feat_payments" data-bs-parent="#nestedFeatureAccordion">
																		<div class="accordion-body p-3 pt-0 pb-2">
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Auto-pay reminder" id="sub_39"><label class="form-check-label ms-1" for="sub_39">Auto-pay reminder (3)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Transaction alert" id="sub_40"><label class="form-check-label ms-1" for="sub_40">Transaction alert (3)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Fraud alert" id="sub_41"><label class="form-check-label ms-1" for="sub_41">Fraud alert (4)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Low balance warning" id="sub_42"><label class="form-check-label ms-1" for="sub_42">Low balance warning (3)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Payment action required" id="sub_43"><label class="form-check-label ms-1" for="sub_43">Payment action required (3)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Payment confirmation" id="sub_44"><label class="form-check-label ms-1" for="sub_44">Payment confirmation (4)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Payment due reminder" id="sub_45"><label class="form-check-label ms-1" for="sub_45">Payment due reminder (10)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Payment reject/fail" id="sub_46"><label class="form-check-label ms-1" for="sub_46">Payment reject/fail (4)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Payment notice" id="sub_47"><label class="form-check-label ms-1" for="sub_47">Payment notice (3)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Payment overdue" id="sub_48"><label class="form-check-label ms-1" for="sub_48">Payment overdue (6)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Payment scheduled" id="sub_49"><label class="form-check-label ms-1" for="sub_49">Payment scheduled (3)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Payment successful" id="sub_50"><label class="form-check-label ms-1" for="sub_50">Payment successful (1)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Receipt attachment" id="sub_51"><label class="form-check-label ms-1" for="sub_51">Receipt attachment (3)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Recharge reject/fail" id="sub_52"><label class="form-check-label ms-1" for="sub_52">Recharge reject/fail (1)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Recharge confirmation" id="sub_53_a"><label class="form-check-label ms-1" for="sub_53_a">Recharge confirmation (1)</label></div>
																			<div class="form-check mb-0"><input class="form-check-input explore-filter" type="checkbox" value="Statement available" id="sub_53_b"><label class="form-check-label ms-1" for="sub_53_b">Statement available (2)</label></div>
																		</div>
																	</div>
																</div>

																<div class="accordion-item border-0">
																	<h2 class="accordion-header" id="hdg_feat_pub_disrupt">
																		<button class="accordion-button collapsed fw-bold text-dark fs-14 flex-row-reverse justify-content-between p-3" type="button" data-bs-toggle="collapse" data-bs-target="#col_feat_pub_disrupt" aria-expanded="false" aria-controls="col_feat_pub_disrupt" style="box-shadow: none; background: transparent; padding-top: 10px !important; padding-bottom: 10px !important;">
																			Public disruption
																		</button>
																	</h2>
																	<div id="col_feat_pub_disrupt" class="accordion-collapse collapse" aria-labelledby="hdg_feat_pub_disrupt" data-bs-parent="#nestedFeatureAccordion">
																		<div class="accordion-body p-3 pt-0 pb-2">
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Operation disruption" id="sub_54_a"><label class="form-check-label ms-1" for="sub_54_a">Operation disruption (2)</label></div>
																			<div class="form-check mb-0"><input class="form-check-input explore-filter" type="checkbox" value="System outages" id="sub_54_b"><label class="form-check-label ms-1" for="sub_54_b">System outages (2)</label></div>
																		</div>
																	</div>
																</div>

																<div class="accordion-item border-0">
																	<h2 class="accordion-header" id="hdg_feat_pub_safety">
																		<button class="accordion-button collapsed fw-bold text-dark fs-14 flex-row-reverse justify-content-between p-3" type="button" data-bs-toggle="collapse" data-bs-target="#col_feat_pub_safety" aria-expanded="false" aria-controls="col_feat_pub_safety" style="box-shadow: none; background: transparent; padding-top: 10px !important; padding-bottom: 10px !important;">
																			Public safety
																		</button>
																	</h2>
																	<div id="col_feat_pub_safety" class="accordion-collapse collapse" aria-labelledby="hdg_feat_pub_safety" data-bs-parent="#nestedFeatureAccordion">
																		<div class="accordion-body p-3 pt-0 pb-2">
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Crisis response" id="sub_55_a"><label class="form-check-label ms-1" for="sub_55_a">Crisis response (2)</label></div>
																			<div class="form-check mb-0"><input class="form-check-input explore-filter" type="checkbox" value="Severe weather" id="sub_55_b"><label class="form-check-label ms-1" for="sub_55_b">Severe weather (2)</label></div>
																		</div>
																	</div>
																</div>

																<div class="accordion-item border-0">
																	<h2 class="accordion-header" id="hdg_feat_pub_service">
																		<button class="accordion-button collapsed fw-bold text-dark fs-14 flex-row-reverse justify-content-between p-3" type="button" data-bs-toggle="collapse" data-bs-target="#col_feat_pub_service" aria-expanded="false" aria-controls="col_feat_pub_service" style="box-shadow: none; background: transparent; padding-top: 10px !important; padding-bottom: 10px !important;">
																			Public service
																		</button>
																	</h2>
																	<div id="col_feat_pub_service" class="accordion-collapse collapse" aria-labelledby="hdg_feat_pub_service" data-bs-parent="#nestedFeatureAccordion">
																		<div class="accordion-body p-3 pt-0 pb-2">
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Disbursements" id="sub_56_a"><label class="form-check-label ms-1" for="sub_56_a">Disbursements (2)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Health awareness" id="sub_56_b"><label class="form-check-label ms-1" for="sub_56_b">Health awareness (1)</label></div>
																			<div class="form-check mb-3"><input class="form-check-input explore-filter" type="checkbox" value="Health emergency" id="sub_56_c"><label class="form-check-label ms-1" for="sub_56_c">Health emergency (2)</label></div>
																			<div class="form-check mb-0"><input class="form-check-input explore-filter" type="checkbox" value="Voting registration" id="sub_56_d"><label class="form-check-label ms-1" for="sub_56_d">Voting registration (1)</label></div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>

												<!-- Auth: Identity Verification Accordion -->
												<div class="accordion-item border-bottom filter-cat-auth d-none">
													<h2 class="accordion-header" id="headingIdentity">
														<button class="accordion-button collapsed fw-bold text-dark fs-15 flex-row-reverse justify-content-between p-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseIdentity" aria-expanded="false" aria-controls="collapseIdentity" style="box-shadow: none; background: transparent;">
															Identity verification
														</button>
													</h2>
													<div id="collapseIdentity" class="accordion-collapse collapse" aria-labelledby="headingIdentity" data-bs-parent="#filterAccordion">
														<div class="accordion-body p-3 pt-0">
															<div class="form-check mb-3">
																<input class="form-check-input explore-filter" type="checkbox" value="In-person verification" id="auth_id_1" style="width: 18px; height: 18px;">
																<label class="form-check-label ms-1" for="auth_id_1">In-person verification (6)</label>
															</div>
															<div class="form-check mb-3">
																<input class="form-check-input explore-filter" type="checkbox" value="Verify user" id="auth_id_2" style="width: 18px; height: 18px;">
																<label class="form-check-label ms-1" for="auth_id_2">Verify user (7)</label>
															</div>
															<div class="form-check mb-0">
																<input class="form-check-input explore-filter" type="checkbox" value="Verify transaction" id="auth_id_3" style="width: 18px; height: 18px;">
																<label class="form-check-label ms-1" for="auth_id_3">Verify transaction (6)</label>
															</div>
														</div>
													</div>
												</div>

												<!-- Auth: Payments Accordion -->
												<div class="accordion-item border-bottom filter-cat-auth d-none">
													<h2 class="accordion-header" id="headingAuthPayments">
														<button class="accordion-button collapsed fw-bold text-dark fs-15 flex-row-reverse justify-content-between p-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAuthPayments" aria-expanded="false" aria-controls="collapseAuthPayments" style="box-shadow: none; background: transparent;">
															Payments
														</button>
													</h2>
													<div id="collapseAuthPayments" class="accordion-collapse collapse" aria-labelledby="headingAuthPayments" data-bs-parent="#filterAccordion">
														<div class="accordion-body p-3 pt-0">
															<div class="form-check mb-0">
																<input class="form-check-input explore-filter" type="checkbox" value="In-person verification" id="auth_pay_1" style="width: 18px; height: 18px;">
																<label class="form-check-label ms-1" for="auth_pay_1">In-person verification (1)</label>
															</div>
														</div>
													</div>
												</div>
											</div>

											<div class="p-3">
												<label class="form-label fw-bold small text-uppercase mb-2">Health (Quality)</label>
												<div class="health-filters d-flex flex-wrap gap-2">
													<div class="form-check form-check-inline me-0">
														<input class="form-check-input explore-filter" type="checkbox" value="GREEN" id="health_high">
														<label class="form-check-label small" for="health_high">High</label>
													</div>
													<div class="form-check form-check-inline me-0">
														<input class="form-check-input explore-filter" type="checkbox" value="YELLOW" id="health_med">
														<label class="form-check-label small" for="health_med">Medium</label>
													</div>
													<div class="form-check form-check-inline me-0">
														<input class="form-check-input explore-filter" type="checkbox" value="RED" id="health_low">
														<label class="form-check-label small" for="health_low">Low</label>
													</div>
												</div>
											</div>

											<div class="d-grid mt-2 mb-3 px-3">
												<button class="btn btn-primary" id="btnRefreshExplore">
													<i class="ti ti-refresh me-1"></i>Apply & Fetch
												</button>
											</div>
										</div>
									</div>
								</div>

								<!-- Library Content -->
								<div class="col-md-9">
									<div class="card">
										<div class="card-header bg-white d-flex align-items-center justify-content-between">
											<div class="d-flex align-items-center">
												<h5 class="card-title mb-0 me-3">Live Facebook Account Library <span class="badge bg-soft-primary text-primary ms-2" id="exploreCount">0</span></h5>
												<button class="btn btn-soft-success btn-sm" id="btnSyncAllExplore">
													<i class="ti ti-refresh me-1"></i>Sync All to CRM
												</button>
											</div>
											<div class="col-sm-4">
												<div class="icon-form">
													<span class="form-icon"><i class="ti ti-search"></i></span>
													<input type="text" id="searchExplore" class="form-control form-control-sm" placeholder="Search official documents...">
												</div>
											</div>
										</div>
										<div class="card-body p-4 bg-light" style="min-height: 500px;">
											<div class="d-flex justify-content-between align-items-center mb-4">
												<span class="text-muted fs-14">Showing <span id="exploreShowingCount">0</span> of <span id="exploreTotalCount">0</span> results</span>
											</div>
											
											<!-- Templates Grid -->
											<div class="row g-4" id="exploreTabBody">
												<div class="col-12 text-center p-5 text-muted">
													Choose an account and apply filters to explore the Meta template library.
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
		<!-- /Page Wrapper -->

		<!-- /Add New  -->

		<!-- Edit  -->
		<!-- Edit Template Offcanvas -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom">
				<h4>Edit Template</h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form id="editTemplateForm">
					@csrf
					<input type="hidden" name="id" id="edit_template_id">
					<div class="row">
						<div class="col-md-7 border-end">
							<div class="pro-create">
								<div class="row">
									<div class="col-md-12">
										<div class="mb-3">
											<label class="col-form-label">Template Title</label>
											<input class="form-control" type="text" id="edit_name" readonly disabled>
											<small class="text-muted">Template names cannot be changed after creation.</small>
										</div>
										
										<div class="mb-3">
											<label class="col-form-label">Template Category <span class="text-danger">*</span></label>
											<select class="form-control" name="category" id="edit_category" required>
												<option value="MARKETING">Marketing</option>
												<option value="UTILITY">Utility</option>
												<option value="AUTHENTICATION">Authentication</option>
											</select>
										</div>

										<div class="mb-3">
											<label class="col-form-label">Header Text (Optional)</label>
											<input class="form-control" type="text" name="header_text" id="edit_header_text" placeholder="Enter header text" maxlength="60">
										</div>

										<div class="mb-3">
											<label class="col-form-label">Template Body <span class="text-danger">*</span></label>
											<textarea class="form-control" name="body_text" id="edit_body_text" rows="6" required></textarea>
											<small class="text-muted">Editing the body will trigger a re-approval process on Meta.</small>
										</div>

										<div class="mb-3">
											<label class="col-form-label">Footer Text (Optional)</label>
											<input class="form-control" type="text" name="footer_text" id="edit_footer_text" placeholder="Enter footer text" maxlength="60">
										</div>
									</div>
								</div>
							</div>
							<div class="d-flex align-items-center justify-content-end mt-4">
								<button type="submit" class="btn btn-primary" id="btnUpdateTemplate">Update & Sync</button>
							</div>
						</div>
						<div class="col-md-5 bg-light-gray p-4 d-flex justify-content-center">
							<div class="sticky-top" style="top: 20px; width: 100%;">
								<h6 class="fw-bold mb-3">Live Preview</h6>
								<div class="whatsapp-preview-container rounded-4 shadow-sm overflow-hidden border mb-3" style="max-width: 100%;">
									<div class="preview-header bg-dark p-2 d-flex justify-content-between align-items-center" style="height: 45px;">
										<div class="d-flex align-items-center">
											<div class="avatar avatar-xs rounded-circle bg-light me-2">
												<i class="ti ti-brand-whatsapp text-success fs-12"></i>
											</div>
											<span class="text-white mb-0 fs-12">WhatsApp Preview</span>
										</div>
									</div>
									
									<div class="preview-body p-3" style="background-color: #e5ddd5; min-height: 250px; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-size: cover;">
										<div class="preview-bubble bg-white rounded-3 shadow-sm p-2 px-3" style="position: relative; max-width: 95%;">
											<div id="editPreviewHeader" class="fw-bold mb-1 fs-13 text-dark"></div>
											<div id="editPreviewBody" class="fs-13 text-dark" style="white-space: pre-wrap;"></div>
											<div id="editPreviewFooter" class="text-muted fs-10 mt-1"></div>
											
											<div class="text-end mt-1">
												<small class="text-muted" style="font-size: 9px;">{{ now()->format('H:i') }} <i class="ti ti-check-double text-info ms-1"></i></small>
											</div>
										</div>
									</div>
								</div>
								<p class="small text-muted"><i class="ti ti-info-circle me-1"></i> This identifies how the message will appear to the receiver.</p>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<!-- Preview Template Offcanvas -->
		<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvas_preview" style="width: 400px;">
			<div class="offcanvas-header border-bottom">
				<h4 id="preview_title">Template Preview</h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body bg-light">
				<div class="d-flex flex-column align-items-center justify-content-center h-100">
					<div class="whatsapp-preview-container rounded-4 shadow-sm overflow-hidden border mb-3 w-100">
						<div class="preview-header bg-dark p-3 d-flex justify-content-between align-items-center" style="height: 60px;">
							<div class="d-flex align-items-center">
								<div class="avatar avatar-sm rounded-circle bg-light me-2">
									<i class="ti ti-brand-whatsapp text-success fs-18"></i>
								</div>
								<h6 class="text-white mb-0 fs-14">WhatsApp Business</h6>
							</div>
						</div>
						
						<div class="preview-body p-4" style="background-color: #e5ddd5; min-height: 350px; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-size: cover;">
							<div class="preview-bubble bg-white rounded-3 shadow-sm p-3" style="position: relative; max-width: 100%;">
								<div id="quickPreviewHeader" class="fw-bold mb-2 fs-15 text-dark"></div>
								<div id="quickPreviewBody" class="fs-14 text-dark" style="white-space: pre-wrap;"></div>
								<div id="quickPreviewFooter" class="text-muted fs-11 mt-2"></div>
								
								<div class="text-end mt-2">
									<small class="text-muted" style="font-size: 10px;">{{ now()->format('H:i') }} <i class="ti ti-check-double text-info ms-1"></i></small>
								</div>
							</div>
						</div>
					</div>
					<div class="card w-100 shadow-none border">
						<div class="card-body p-3">
							<div class="d-flex align-items-center justify-content-between mb-2">
								<span class="text-muted small">Category:</span>
								<span class="badge bg-soft-primary text-primary" id="preview_category_badge">MARKETING</span>
							</div>
							<div class="d-flex align-items-center justify-content-between">
								<span class="text-muted small">Language:</span>
								<span class="fw-medium small" id="preview_language_label">en_US</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- /Edit Campaign -->

		<!-- Delete Campaign -->
		<div class="modal fade" id="delete_template_modal" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Template?</h4>
							<p class="mb-0">Are you sure you want to remove this template? This will delete it from both ZipZapGo and the Meta API.</p>
							<input type="hidden" name="id" id="delete_target_id">
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="javascript:void(0);" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete it</button>
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
						<form action="{{ box_route('whatsapp.campaign') }}">
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
		<!-- Explore Meta Modal -->
		<div class="modal fade" id="explore_meta_modal" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog modal-xl modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Explore Meta Template Library</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body p-0">
						<div class="table-responsive" style="max-height: 600px;">
							<table class="table mb-0">
								<thead class="sticky-top bg-white">
									<tr>
										<th>Template Name</th>
										<th>Category</th>
										<th>Language</th>
										<th>Preview</th>
										<th>Status</th>
										<th class="text-end">Action</th>
									</tr>
								</thead>
								<tbody id="exploreMetaBody">
									<tr>
										<td colspan="6" class="text-center p-5">
											<div class="spinner-border text-primary" role="status"></div>
											<div class="mt-2">Connecting to Meta Library...</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Explore Meta Modal -->
    <!-- /Page Wrapper -->

@endsection

@push('modals')
<!-- Manage Columns Modal -->
<div class="modal fade" id="manageColumnsModal" tabindex="-1" aria-labelledby="manageColumnsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageColumnsModalLabel">Manage Columns</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="showAllColumns">Show All</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="hideAllColumns">Hide All</button>
                </div>
                <div class="list-group" id="columnsList">
                    <!-- Columns will be populated dynamically -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="applyColumnChanges">Apply Changes</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    // Column Definitions for Manage Columns feature
    const columnDefinitions = [
        { index: 0, class: 'col-checkbox', name: 'Checkbox', visible: true },
        { index: 1, class: 'col-star', name: 'Star', visible: true },
        { index: 2, class: 'col-name', name: 'Template Name', visible: true },
        { index: 3, class: 'col-account', name: 'Account', visible: true },
        { index: 11, class: 'col-phone', name: 'Phone Number', visible: true },
        { index: 4, class: 'col-category', name: 'Category', visible: true },
        { index: 5, class: 'col-status', name: 'Status', visible: true },
        { index: 6, class: 'col-message', name: 'Message Body', visible: true },
        { index: 7, class: 'col-meta-id', name: 'Meta ID', visible: true },
        { index: 8, class: 'col-created', name: 'Created At', visible: true },
        { index: 9, class: 'col-approved', name: 'Approved At', visible: true },
        { index: 10, class: 'col-action', name: 'Action', visible: true }
    ];

    const pageIdentifier = 'whatsapp-templates';

    function loadColumnPreferences(callback) {
        $.ajax({
            url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug")]) }}',
            type: 'GET',
            data: { page_identifier: pageIdentifier },
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    response.data.forEach(pref => {
                        const col = columnDefinitions.find(c => c.index === parseInt(pref.index));
                        if (col) col.visible = (pref.visible === 'true' || pref.visible === true || pref.visible === 1 || pref.visible === '1');
                    });
                }
                if (callback) callback();
            },
            error: function() {
                if (callback) callback();
            }
        });
    }

    function saveColumnPreferences() {
        $.ajax({
            url: '{{ route("settings.column.preferences.save", ["box_slug" => request()->route("box_slug")]) }}',
            type: 'POST',
            data: {
                page_identifier: pageIdentifier,
                column_preferences: columnDefinitions,
                _token: '{{ csrf_token() }}'
            }
        });
    }

    function populateColumnsModal() {
        const list = $('#columnsList');
        list.empty();
        columnDefinitions.forEach(col => {
            list.append(`
                <div class="list-group-item">
                    <div class="form-check">
                        <input class="form-check-input column-toggle" type="checkbox" data-index="${col.index}" id="col_${col.index}" ${col.visible ? 'checked' : ''}>
                        <label class="form-check-label" for="col_${col.index}">${col.name}</label>
                    </div>
                </div>
            `);
        });
    }

    function applyColumnVisibility() {
        columnDefinitions.forEach(col => {
            if (col.visible) {
                $(`.${col.class}`).show();
            } else {
                $(`.${col.class}`).hide();
            }
        });
    }

    $(document).ready(function() {
        loadColumnPreferences(function() {
            fetchTemplates();
        });

        $('#manageColumnsModal').on('show.bs.modal', populateColumnsModal);
        
        $('#showAllColumns').on('click', () => $('.column-toggle').prop('checked', true));
        $('#hideAllColumns').on('click', () => $('.column-toggle').prop('checked', false));

        $('#applyColumnChanges').on('click', function() {
            $('.column-toggle').each(function() {
                const index = $(this).data('index');
                const visible = $(this).is(':checked');
                const col = columnDefinitions.find(c => c.index === index);
                if (col) col.visible = visible;
            });
            saveColumnPreferences();
            applyColumnVisibility();
            $('#manageColumnsModal').modal('hide');
        });
    });

    function fetchTemplates(status = 'all') {
        let activePane = $('.tab-pane.active');
        let targetBody = activePane.find('.templateTableBody');
        let searchQuery = $('#searchTemplate').val();
        let wabaId = $('#filter_waba_id').val();
        let category = $('#filter_category').val();
        
        targetBody.html('<tr><td colspan="11" class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><div class="mt-2">Loading templates...</div></td></tr>');

        $.ajax({
            url: "{{ box_route('whatsapp.business.template.list') }}",
            type: "GET",
            data: { 
                status: status,
                search: searchQuery,
                waba_id: wabaId,
                category: category
            },
            success: function(res) {
                if (res.success) {
                    let html = '';
                    if (res.templates.length === 0) {
                        html = '<tr><td colspan="11" class="text-center p-4">No templates found for this status. Click "Sync from Meta" to fetch your latest templates.</td></tr>';
                    } else {
                        res.templates.forEach(t => {
                            let statusClass = 'bg-warning text-dark';
                            if (t.whats_app_status === 'APPROVED') statusClass = 'bg-success';
                            if (t.whats_app_status === 'REJECTED') statusClass = 'bg-danger';
                            if (t.whats_app_status === 'PENDING') statusClass = 'bg-info';

                            // Extract Body Text
                            let comps = t.components;
                            if (typeof comps === 'string') comps = JSON.parse(comps);
                            let bodyComp = (comps || []).find(c => c.type === 'BODY');
                            let bodyText = bodyComp ? bodyComp.text : '---';

                            html += `
                                <tr data-id="${t.id}">
                                    <td class="col-checkbox"><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
                                    <td class="col-star"><div class="set-star rating-select"><i class="fa fa-star"></i></div></td>
                                    <td class="col-name"><strong>${t.name}</strong></td>
                                    <td class="col-account"><span class="text-primary fw-bold">${t.account_name || '---'}</span></td>
                                    <td class="col-phone">
                                        <div class="d-flex flex-column">
                                            <span>${t.phone_number || '---'}</span>
                                        </div>
                                    </td>
                                    <td class="col-category">${t.category}</td>
                                    <td class="col-status"><span class="badge badge-pill badge-status ${statusClass}">${t.whats_app_status}</span></td>
                                    <td class="col-message">
                                        <div class="text-truncate" style="max-width: 250px;" title="${bodyText}">
                                            ${bodyText}
                                        </div>
                                    </td>
                                    <td class="col-meta-id"><small class="text-muted">${t.whats_app_id}</small></td>
                                    <td class="col-created">
                                        <div class="d-flex flex-column text-nowrap">
                                            <span>${new Date(t.created_at).toLocaleDateString()}</span>
                                            <small class="text-muted">${new Date(t.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</small>
                                        </div>
                                    </td>
                                    <td class="col-approved">
                                        <div class="d-flex flex-column text-nowrap">
                                            ${t.approved_at ? 
                                                `<span>${new Date(t.approved_at).toLocaleDateString()}</span>
                                                 <small class="text-muted">${new Date(t.approved_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</small>` : 
                                                '<span class="text-muted">---</span>'
                                            }
                                        </div>
                                    </td>
                                    <td class="col-action">
                                        <div class="hstack gap-2 fs-15">
                                            <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-light btn-preview-template" data-id="${t.id}" title="Quick Preview"><i class="ti ti-eye"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-light btn-edit-template" data-id="${t.id}" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a>
                                            ${JSON.stringify(t.components).includes('{' + '{') ? 
                                                `<a href="${"{{ box_route('whatsapp.business.template.mapping', ['id' => 'TEMP_ID']) }}".replace('TEMP_ID', t.id)}" class="btn btn-icon btn-sm btn-light text-primary" title="Variable Mapping"><i class="fa-solid fa-link"></i></a>` : 
                                                ''
                                            }
                                            <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-light btn-delete-template" data-id="${t.id}" title="Delete"><i class="fa-solid fa-trash"></i></a>
                                        </div>
                                    </td>

                                </tr>
                            `;
                        });
                    }
                    targetBody.html(html);
                    applyColumnVisibility();
                }
            }
        });
    }

    // Fetch on tab change
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        let status = $(e.target).data('status');
        if (status === 'explore') {
            loadExploreTab();
        } else {
            fetchTemplates(status);
        }
    });

    function loadExploreTab() {
        let wabaId = $('#explore_tab_waba_id').val();
        if (!wabaId) return;

        let category = $('input[name="explore_category"]:checked').val() || null;
        
        let healths = [];
        $('.health-filters input:checked').each(function() { healths.push($(this).val()); });

        let selIndustries = [];
        $('#collapseIndustry input:checked').each(function() { selIndustries.push($(this).val()); });

        let selFeatures = [];
        $('#collapseFeature input:checked, #collapseIdentity input:checked, #collapseAuthPayments input:checked').each(function() { 
            let val = $(this).val().toLowerCase().replace(/ /g, '_');
            selFeatures.push(val);
        });

        $('#exploreTabBody').html('<div class="col-12 text-center p-5"><div class="spinner-border text-primary" role="status"></div><div class="mt-2">Fetching from Meta Official Library...</div></div>');

        $.ajax({
            url: "{{ box_route('whatsapp.business.template.meta-library') }}",
            type: "GET",
            data: { waba_id: wabaId },
            success: function(res) {
                // console.log('Meta Library Response:', res);
                if (res.success) {
                    window.exploreTemplates = res.templates || [];
                    let fullTemplates = res.templates || [];
                    let searchTerm = $('#searchExplore').val().toLowerCase();
                    
                    $('#exploreTotalCount').text(fullTemplates.length);

                    // Client-side filtering
                    let filtered = fullTemplates.filter(t => {
                        let cat = String(t.category || '').toUpperCase();
                        let matchesCategory = !category || cat === category;
                        
                        let rawHealth = t.quality_score;
                        let health = 'UNKNOWN';
                        if (typeof rawHealth === 'string') {
                            health = rawHealth;
                        } else if (rawHealth && typeof rawHealth === 'object' && rawHealth.score) {
                            health = rawHealth.score;
                        }
                        health = String(health).toUpperCase();

                        // Map synonyms
                        if (health === 'HIGH') health = 'GREEN';
                        if (health === 'MEDIUM') health = 'YELLOW';
                        if (health === 'LOW') health = 'RED';
                        
                        let matchesHealth = healths.length === 0 || healths.includes(health);
                        
                        let matchesSearch = !searchTerm || t.name.toLowerCase().includes(searchTerm) || (t.components || []).some(o => o.text && o.text.toLowerCase().includes(searchTerm));
                        
                        // Check Industry
                        let matchesIndustry = true;
                        if (selIndustries.length > 0) {
                            let tempInd = t.industry ? t.industry.toLowerCase() : '';
                            matchesIndustry = selIndustries.includes(tempInd) || 
                                              selIndustries.some(ind => t.name.toLowerCase().includes(ind));
                        }

                        // Check Feature
                        let matchesFeature = true;
                        if (selFeatures.length > 0) {
                            matchesFeature = selFeatures.some(feat => t.name.toLowerCase().includes(feat));
                        }
                        
                        return matchesCategory && matchesHealth && matchesSearch && matchesIndustry && matchesFeature;
                    });

                    $('#exploreCount').text(filtered.length);
                    $('#exploreShowingCount').text(filtered.length);
                    
                    // --- UPDATE SIDEBAR COUNTS ---
                    // Reset counts
                    $('[id^="cnt_cat_"]').text('');
                    $('[id^="cnt_ind_"]').text('');
                    $('[id^="cnt_sub_"]').text('');

                    // Calculate Summary Stats
                    let catCounts = { MARKETING: 0, UTILITY: 0, AUTHENTICATION: 0 };
                    let indCounts = {};
                    let featCounts = {};
                    let features = [
                        'fraud_awareness', 'product_recalls', 'warranty_alerts', 'account_creation_confirmation',
                        'cancellation_confirmation', 'network_troubleshooting', 'renewal_reminder', 'renewal_confirmation',
                        'roaming_reminder', 'service_disruption', 'upgrade_confirmation', 'call_permission_request',
                        'feedback_survey', 'follow_up_missed_calls', 'ticket_acknowledgement', 'appointment_cancellation',
                        'appointment_confirmation', 'appointment_reminder', 'appointment_scheduling', 'event_details_reminder',
                        'event_rsvp_confirmation', 'appointment_missed', 'technician_arrival', 'group_invitation_upon_request',
                        'identity_compliance', 'privacy_disclosures', 'delivery_update', 'delivery_confirmation',
                        'delivery_failed', 'return_confirmation', 'order_confirmation', 'order_action_needed',
                        'order_or_transaction_cancel', 'order_delay', 'order_pick_up', 'shipment_confirmation',
                        'order_refund', 'auto_pay_reminder', 'transaction_alert', 'fraud_alert', 'low_balance_warning',
                        'payment_action_required', 'payment_confirmation', 'payment_due_reminder', 'payment_reject_fail',
                        'payment_notice', 'payment_overdue', 'payment_scheduled', 'payment_successful', 'receipt_attachment',
                        'recharge_reject_fail', 'recharge_confirmation', 'statement_available', 'operation_disruption',
                        'system_outages', 'crisis_response', 'severe_weather', 'public_service_announcement', 'disbursements', 
                        'health_awareness', 'health_emergency', 'voting_registration',
                        'in_person_verification', 'verify_user', 'verify_transaction'
                    ];

                    fullTemplates.forEach(t => {
                        let c = String(t.category || '').toUpperCase();
                        if(catCounts[c] !== undefined) catCounts[c]++;
                        
                        if(t.industry) {
                            let ind = t.industry.toLowerCase();
                            indCounts[ind] = (indCounts[ind] || 0) + 1;
                        }

                        // Extract features from name for realistic counts
                        features.forEach(f => {
                            if(t.name.toLowerCase().includes(f)) {
                                f = f.replace(/ /g, '_');
                                featCounts[f] = (featCounts[f] || 0) + 1;
                            }
                        });
                    });

                    // Update UI Labels
                    $('#cnt_cat_marketing').text(`(${catCounts.MARKETING})`);
                    $('#cnt_cat_utility').text(`(${catCounts.UTILITY})`);
                    $('#cnt_cat_auth').text(`(${catCounts.AUTHENTICATION})`);

                    // Update Sub-Labels if they exist
                    for(let ind in indCounts) {
                       $(`#cnt_ind_${ind.replace(/ /g, '_')}`).text(`(${indCounts[ind]})`);
                    }
                    for(let f in featCounts) {
                       $(`#cnt_sub_${f.replace(/ /g, '_')}`).text(`(${featCounts[f]})`);
                    }
                    
                    let html = '';
                    if (fullTemplates.length === 0) {
                       html = '<div class="col-12 text-center p-5 text-muted">No templates found in this Meta account. Make sure your account has message templates in the WhatsApp Manager.</div>';
                    } else if (filtered.length === 0) {
                        html = '<div class="col-12 text-center p-5 text-muted">No templates match your current filters. Try adjusting your filters.</div>';
                    } else {
                        filtered.forEach(t => {
                            let bodyComp = (t.components || []).find(c => c.type === 'BODY');
                            let bodyText = bodyComp ? bodyComp.text : '';
                            let formattedBody = bodyText
                                .replace(/\*(.*?)\*/g, '<b>$1</b>')
                                .replace(/_(.*?)_/g, '<i>$1</i>')
                                .replace(/~(.*?)~/g, '<strike>$1</strike>')
                                .replace(/\{\{(.*?)\}\}/g, '<span class="text-success">@{{$1}}</span>')
                                .replace(/\n/g, '<br>');
                                
                            let headerText = '';
                            let headerComp = (t.components || []).find(c => c.type === 'HEADER');
                            if(headerComp && headerComp.text) {
                                headerText = headerComp.text.replace(/\{\{(.*?)\}\}/g, '<span class="text-success">@{{$1}}</span>');
                            }

                            let buttonsHtml = '';
                            let buttonComp = (t.components || []).find(c => c.type === 'BUTTONS');
                            if (buttonComp && buttonComp.buttons && buttonComp.buttons.length > 0) {
                                buttonsHtml = '<div class="mt-2">';
                                buttonComp.buttons.forEach((btn, index) => {
                                    let icon = 'ti ti-hand-click';
                                    if(btn.type === 'URL') icon = 'ti ti-external-link';
                                    if(btn.type === 'PHONE_NUMBER') icon = 'ti ti-phone';
                                    
                                    buttonsHtml += `
                                        <div class="d-flex align-items-center justify-content-center border-top pt-2 ${index > 0 ? 'mt-2' : ''}">
                                            <span class="text-decoration-none d-flex align-items-center" style="color: #00a884; font-weight: 500;">
                                                <i class="${icon} me-1 fs-5"></i> ${btn.text}
                                            </span>
                                        </div>
                                    `;
                                });
                                buttonsHtml += '</div>';
                            }

                            html += `
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 shadow-sm border-0 template-card-ui" style="background-color: #e5ddd5; position: relative;">
                                        <!-- WhatsApp Background Texture elements -->
                                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.06; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-size: cover; pointer-events: none; border-radius: 8px;"></div>
                                        
                                        <div class="card-body position-relative d-flex flex-column" style="z-index: 1;">
                                            <div class="bg-white rounded p-3 mb-auto" style="box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                                                ${headerText ? `<div class="fw-bold mb-2 text-dark" style="font-size: 15px;">${headerText}</div>` : ''}
                                                <div class="text-dark" style="font-size: 14px; line-height: 1.5;">${formattedBody}</div>
                                                
                                                <!-- Action Buttons inside bubble -->
                                                ${buttonsHtml}
                                                <div class="text-end mt-1">
                                                    <small class="text-muted" style="font-size: 11px;">04:36</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card-footer bg-white border-top-0 py-2 px-3 d-flex justify-content-between align-items-center" style="z-index: 1;">
                                            <div class="text-muted text-truncate" style="font-size: 13px; max-width: 70%;" title="${t.name}">
                                                ${t.name}
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <a href="https://business.facebook.com/latest/whatsapp_manager/template_library?business_id=${t.account_waba_id}" target="_blank" class="btn btn-icon btn-sm btn-light me-1" title="View Official Document">
                                                    <i class="ti ti-external-link text-muted"></i>
                                                </a>
                                                ${t.is_synced ? 
                                                    '<span class="text-success small fw-bold d-none d-md-inline" title="Library Synced"><i class="ti ti-check bg-success-light p-1 rounded-circle"></i></span>' : 
                                                    `<button class="btn btn-icon btn-sm btn-primary btn-sync-single" data-name="${t.name}" data-waba="${t.account_waba_id}" title="Fetch & Use"><i class="ti ti-download"></i></button>`
                                                }
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    $('#exploreTabBody').html(html);
                } else {
                    $('#exploreTabBody').html(`<div class="col-12 text-center text-danger p-4">${res.message}</div>`);
                }
            },
            error: function(xhr) {
                console.error('Meta Explorer Error:', xhr);
                $('#exploreTabBody').html('<tr><td colspan="6" class="text-center text-danger p-4">Failed to fetch data from Meta. Please check your API connection and permissions.</td></tr>');
            }
        });
    }

    $('#btnRefreshExplore').on('click', loadExploreTab);
    $('#explore_tab_waba_id, .explore-filter').on('change', loadExploreTab);
    $('#searchExplore').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(loadExploreTab, 300);
    });

    $('input[name="explore_category"]').on('change', function() {
        let cat = $(this).val();
        if(cat === 'AUTHENTICATION') {
            $('.filter-cat-utility').addClass('d-none');
            $('.filter-cat-auth').removeClass('d-none');
        } else if(cat === 'UTILITY') {
            $('.filter-cat-utility').removeClass('d-none');
            $('.filter-cat-auth').addClass('d-none');
        } else {
            $('.filter-cat-utility').addClass('d-none');
            $('.filter-cat-auth').addClass('d-none');
        }
    });

    // Run once on load to set correct visibility
    setTimeout(function() {
        $('input[name="explore_category"]:checked').trigger('change');
    }, 100);

    // Sync All from Explore Tab
    $('#btnSyncAllExplore').on('click', function() {
        let wabaId = $('#explore_tab_waba_id').val();
        if (!wabaId) {
            showToast('Please select an account first.', 'warning');
            return;
        }

        let btn = $(this);
        let originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>Syncing All...');

        $.ajax({
            url: "{{ box_route('whatsapp.business.template.sync') }}",
            type: "GET",
            data: { waba_id: wabaId },
            success: function(res) {
                btn.prop('disabled', false).html(originalText);
                if (res.success) {
                    showToast(res.message, 'success');
                    loadExploreTab();
                } else {
                    showToast(res.message, 'danger');
                }
            },
            error: function() {
                btn.prop('disabled', false).html(originalText);
                showToast('Bulk sync failed.', 'danger');
            }
        });
    });

    // Sync Single from Tab
    $(document).on('click', '.btn-sync-single', function() {
        let name = $(this).data('name');
        let wabaId = $(this).data('waba') || $('#explore_tab_waba_id').val();
        let btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: "{{ box_route('whatsapp.business.template.sync') }}",
            type: "GET",
            data: { waba_id: wabaId },
            success: function(res) {
                if (res.success) {
                    showToast('Library synced successfully.', 'success');
                    loadExploreTab();
                } else {
                    showToast(res.message, 'danger');
                    btn.prop('disabled', false).text('Fetch & Use');
                }
            }
        });
    });

    // Handle Search input
    let searchTimer;
    $('#searchTemplate').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            let status = $('.nav-link.active').data('status') || 'all';
            fetchTemplates(status);
        }, 500);
    });

    // Handle Filters
    $('#btnApplyFilters').on('click', function() {
        let status = $('.nav-link.active').data('status') || 'all';
        fetchTemplates(status);
        bootstrap.Dropdown.getInstance($(this).closest('.dropdown').find('.dropdown-toggle')[0]).hide();
    });

    $('#btnClearFilters').on('click', function() {
        $('#filter_waba_id').val('all');
        $('#filter_category').val('all');
        $('#searchTemplate').val('');
        let status = $('.nav-link.active').data('status') || 'all';
        fetchTemplates(status);
        bootstrap.Dropdown.getInstance($(this).closest('.dropdown').find('.dropdown-toggle')[0]).hide();
    });

    // Explore Meta Library
    $('#btnExploreMeta').on('click', function() {
        let wabaId = $('#sync_waba_id').val();
        if (!wabaId) {
            showToast('Please select an account.', 'warning');
            return;
        }

        $('#exploreMetaBody').html('<tr><td colspan="6" class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><div class="mt-2">Fetching templates from Meta...</div></td></tr>');
        $('#explore_meta_modal').modal('show');

        $.ajax({
            url: "{{ box_route('whatsapp.business.template.meta-library') }}",
            type: "GET",
            data: { waba_id: wabaId },
            success: function(res) {
                if (res.success) {
                    let html = '';
                    if (res.templates.length === 0) {
                        html = '<tr><td colspan="6" class="text-center p-4">No templates found in this Meta account.</td></tr>';
                    } else {
                        res.templates.forEach(t => {
                            let bodyComp = (t.components || []).find(c => c.type === 'BODY');
                            let bodyText = bodyComp ? bodyComp.text : '---';
                            
                            html += `
                                <tr>
                                    <td><strong>${t.name}</strong></td>
                                    <td>${t.category}</td>
                                    <td>${t.language}</td>
                                    <td><div class="text-truncate" style="max-width: 200px;" title="${bodyText}">${bodyText}</div></td>
                                    <td><span class="badge ${t.status === 'APPROVED' ? 'bg-success' : 'bg-warning'}">${t.status}</span></td>
                                    <td class="text-end">
                                        ${t.is_synced ? 
                                            '<span class="text-success"><i class="ti ti-check me-1"></i>Synced</span>' : 
                                            `<button class="btn btn-primary btn-sm btn-fetch-single" data-name="${t.name}">Fetch</button>`
                                        }
                                    </td>
                                </tr>
                            `;
                        });
                    }
                    $('#exploreMetaBody').html(html);
                } else {
                    $('#exploreMetaBody').html(`<tr><td colspan="6" class="text-center text-danger p-4">${res.message}</td></tr>`);
                }
            },
            error: function() {
                $('#exploreMetaBody').html('<tr><td colspan="6" class="text-center text-danger p-4">Failed to connect to Meta API.</td></tr>');
            }
        });
    });

    // Single Fetch
    $(document).on('click', '.btn-fetch-single', function() {
        let name = $(this).data('name');
        let wabaId = $('#sync_waba_id').val();
        let btn = $(this);

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: "{{ box_route('whatsapp.business.template.sync') }}",
            type: "GET",
            data: { waba_id: wabaId }, // Currently syncs all, but we could optimize to sync single if needed
            success: function(res) {
                if (res.success) {
                    showToast('Template synced successfully.', 'success');
                    btn.replaceWith('<span class="text-success"><i class="ti ti-check me-1"></i>Synced</span>');
                    fetchTemplates();
                } else {
                    showToast(res.message, 'danger');
                    btn.prop('disabled', false).text('Fetch');
                }
            }
        });
    });

    // Handle Form Submit
    $('#createTemplateFormPage').on('submit', function(e) {
        e.preventDefault();
        let btn = $('#btnSubmitTemplate');
        let originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

        $.ajax({
            url: "{{ box_route('whatsapp.business.template.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                btn.prop('disabled', false).html(originalText);
                if (res.success) {
                    showToast(res.message, 'success');
                    $('#createTemplateFormPage')[0].reset();
                    bootstrap.Offcanvas.getInstance(document.getElementById('offcanvas_add')).hide();
                    fetchTemplates();
                } else {
                    let errorMsg = res.message;
                    if (res.error && res.error.error) {
                        if (res.error.error.error_user_msg) {
                            errorMsg = res.error.error.error_user_msg;
                        } else if (res.error.error.message) {
                            errorMsg = res.error.error.message;
                        }
                    }
                    showToast(errorMsg, 'danger');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalText);
                let text = "An error occurred";
                if(xhr.responseJSON && xhr.responseJSON.message) text = xhr.responseJSON.message;
                showToast(text, 'danger');
            }
        });
    });

    // Handle Sync
    $('#btnSyncTemplates').on('click', function() {
        let wabaId = $('#sync_waba_id').val();
        let btn = $(this);
        let originalText = btn.html();

        if (!wabaId) {
            showToast('Please select an account to sync.', 'warning');
            return;
        }

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Syncing...');

        $.ajax({
            url: "{{ box_route('whatsapp.business.template.sync') }}",
            type: "GET",
            data: { waba_id: wabaId },
            success: function(res) {
                btn.prop('disabled', false).html(originalText);
                if (res.success) {
                    showToast(res.message, 'success');
                    fetchTemplates();
                } else {
                    showToast(res.message, 'danger');
                }
            },
            error: function() {
                btn.prop('disabled', false).html(originalText);
                showToast('Sync failed.', 'danger');
            }
        });
    });

    // Edit Template Click
    $(document).on('click', '.btn-edit-template', function() {
        let id = $(this).data('id');
        let offcanvasElt = document.getElementById('offcanvas_edit');
        let offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasElt);
        
        $.ajax({
            url: "{{ box_route('whatsapp.business.template.show') }}",
            type: "POST",
            data: { id: id, _token: "{{ csrf_token() }}" },
            success: function(res) {
                if (res.success) {
                    let t = res.template;
                    $('#edit_template_id').val(t.id);
                    $('#edit_name').val(t.name);
                    $('#edit_category').val(t.category);
                    $('#edit_language').val(t.language);
                    
                    // Body text logic
                    let comps = t.components;
                    if (typeof comps === 'string') comps = JSON.parse(comps);
                    comps = comps || [];
                    $('#edit_template_id').val(t.id);
                    $('#edit_name').val(t.name);
                    $('#edit_category').val(t.category);
                    
                    let components = t.components || [];
                    let bodyComp = components.find(c => c.type === 'BODY');
                    let headerComp = components.find(c => c.type === 'HEADER');
                    let footerComp = components.find(c => c.type === 'FOOTER');
                    
                    $('#edit_body_text').val(bodyComp ? bodyComp.text : '');
                    $('#edit_header_text').val(headerComp ? headerComp.text : '');
                    $('#edit_footer_text').val(footerComp ? footerComp.text : '');

                    offcanvas.show();
                    updateEditPreview();
                } else {
                    showToast(res.message, 'danger');
                }
            },
            error: function(err) {
                console.error('Edit fetch error:', err);
                showToast('Failed to fetch template details.', 'danger');
            }
        });
    });

    // Update Template Submit
    $('#editTemplateForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#edit_template_id').val();
        let btn = $('#btnUpdateTemplate');
        let originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');

        $.ajax({
            url: "{{ box_route('whatsapp.business.template.update') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                btn.prop('disabled', false).html(originalText);
                if (res.success) {
                    showToast(res.message, 'success');
                    bootstrap.Offcanvas.getInstance(document.getElementById('offcanvas_edit')).hide();
                    fetchTemplates($('.nav-link.active').data('status'));
                } else {
                    showToast(res.message, 'danger');
                }
            }
        });
    });

    // Delete Template Click
    $(document).on('click', '.btn-delete-template', function() {
        let id = $(this).data('id');
        $('#delete_target_id').val(id);
        $('#delete_template_modal').modal('show');
    });

    // Delete Template Confirm
    $('#confirmDeleteBtn').on('click', function() {
        let id = $('#delete_target_id').val();
        let btn = $(this);
        let originalText = btn.html();

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');

        $.ajax({
            url: "{{ box_route('whatsapp.business.template.destroy') }}",
            type: "POST",
            data: { id: id, _token: "{{ csrf_token() }}" },
            success: function(res) {
                btn.prop('disabled', false).html(originalText);
                if (res.success) {
                    showToast(res.message, 'success');
                    $('#delete_template_modal').modal('hide');
                    fetchTemplates($('.nav-link.active').data('status'));
                } else {
                    showToast(res.message, 'danger');
                }
            }
        });
    });

    // Preview Template Function
    function showQuickPreview(template) {
        $('#preview_title').text(template.name);
        $('#preview_category_badge').text(template.category);
        $('#preview_language_label').text(template.language);

        let components = template.components;
        if (typeof components === 'string') components = JSON.parse(components);
        components = components || [];

        let headerComp = components.find(c => c.type === 'HEADER');
        let bodyComp = components.find(c => c.type === 'BODY');
        let footerComp = components.find(c => c.type === 'FOOTER');

        $('#quickPreviewHeader').text(headerComp ? headerComp.text : '').toggleClass('d-none', !headerComp);
        
        let bodyText = bodyComp ? bodyComp.text : 'No body content';
        let formattedBody = bodyText
            .replace(/\*(.*?)\*/g, '<b>$1</b>')
            .replace(/_(.*?)_/g, '<i>$1</i>')
            .replace(/~(.*?)~/g, '<strike>$1</strike>')
            .replace(/\n/g, '<br>');
        $('#quickPreviewBody').html(formattedBody);
        
        $('#quickPreviewFooter').text(footerComp ? footerComp.text : '').toggleClass('d-none', !footerComp);

        let offcanvas = bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('offcanvas_preview'));
        offcanvas.show();
    }

    // Preview Click (Local List)
    $(document).on('click', '.btn-preview-template', function() {
        let id = $(this).data('id');
        let tr = $(this).closest('tr');
        
        // Find template in current list
        // Since we don't store global list, we fetch details
        $.ajax({
            url: "{{ box_route('whatsapp.business.template.show') }}",
            type: "POST",
            data: { id: id, _token: "{{ csrf_token() }}" },
            success: function(res) {
                if (res.success) {
                    showQuickPreview(res.template);
                } else {
                    showToast('Failed to fetch preview.', 'danger');
                }
            }
        });
    });

    // Preview Click (Meta Explore)
    $(document).on('click', '.btn-preview-meta', function() {
        let name = $(this).data('name');
        // We know the template is in the explore board
        // To avoid re-fetching, we'll try to find it in the current explore DOM or a cached array
        // But the simplest is to just fetch it if needed. 
        // Actually, let's just use the data we already have if we can.
        // For now, simpler to show what we have.
        // In loadExploreTab, we could store the response globally.
        if(window.exploreTemplates) {
            let t = window.exploreTemplates.find(x => x.name === name);
            if(t) {
                showQuickPreview(t);
                return;
            }
        }
        showToast('Preview data unavailable.', 'warning');
    });

    // Global variable to store explore templates for preview
    window.exploreTemplates = [];

    // Reset Explore Filters
    $('#btnResetExplore').on('click', function() {
        $('#cat_marketing').prop('checked', true);
        $('#explore_tab_waba_id').val('all');
        $('#searchExplore').val('');
        $('.explore-filter:checkbox').prop('checked', false);
        loadExploreTab();
    });

    // Auto-load explore tab when switching to it
    $('a[data-status="explore"]').on('shown.bs.tab', function() {
        if(window.exploreTemplates.length === 0) {
            loadExploreTab();
        }
    });
</script>
@endpush