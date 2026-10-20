@extends('layouts.main')
@section('content')
@section('title','Subscription')

<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									<h4 class="page-title">Subscription<span class="count-title">{{ $totalSubscribers ?? 0 }}</span></h4>
								</div>
								<div class="col-4 text-end">
									<div class="head-icons">
										<a href="subscription.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

						<div class="row">
							<!-- All Subscriptions -->
							<div class="col-xl-3 col-md-4 col-sm-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body">
										<div class="d-flex align-items-center justify-content-between mb-3">
											<div>
												<span class="fs-14 fw-normal text-truncate mb-1 d-block">All Subscriptions</span>
												<h4>{{ $totalSubscribers }}</h4>
											</div>
											<div class="avatar avatar-md bg-primary-light rounded-circle">
												<i class="ti ti-users fs-20 text-primary"></i>
											</div>
										</div>
										<p class="fs-12 fw-normal d-flex align-items-center text-truncate">
											<span class="{{ $totalSubscribersPercent >= 0 ? 'text-success' : 'text-danger' }} fs-12 d-flex align-items-center me-1">
												<i class="ti ti-arrow-wave-{{ $totalSubscribersPercent >= 0 ? 'right-up' : 'right-down' }} me-1"></i>
												{{ $totalSubscribersPercent >= 0 ? '+' : '' }}{{ $totalSubscribersPercent }}%
											</span>from last week
										</p>
									</div>
								</div>
							</div>

							<!-- Total / Overall Amount -->
							<div class="col-xl-3 col-md-4 col-sm-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body">
										<div class="d-flex align-items-center justify-content-between mb-3">
											<div>
												<span class="fs-14 fw-normal text-truncate mb-1 d-block">Overall Amount</span>
												<h4>${{ number_format($paidAmount, 2) }}</h4>
											</div>
											<div class="avatar avatar-md bg-purple-light rounded-circle">
												<i class="ti ti-report-money fs-20 text-purple"></i>
											</div>
										</div>
										<p class="fs-12 fw-normal d-flex align-items-center text-truncate">
											<span class="{{ $paidAmountPercent >= 0 ? 'text-success' : 'text-danger' }} fs-12 d-flex align-items-center me-1">
												<i class="ti ti-arrow-wave-{{ $paidAmountPercent >= 0 ? 'right-up' : 'right-down' }} me-1"></i>
												{{ $paidAmountPercent >= 0 ? '+' : '' }}{{ $paidAmountPercent }}%
											</span>from last week
										</p>
									</div>
								</div>
							</div>

							<!-- Online Amount -->
							<div class="col-xl-3 col-md-4 col-sm-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body">
										<div class="d-flex align-items-center justify-content-between mb-3">
											<div>
												<span class="fs-14 fw-normal text-truncate mb-1 d-block">Online Amount</span>
												<h4>${{ number_format($onlineAmount, 2) }}</h4>
											</div>
											<div class="avatar avatar-md bg-success-light rounded-circle">
												<i class="ti ti-currency-dollar fs-20 text-success"></i>
											</div>
										</div>
										<p class="fs-12 fw-normal d-flex align-items-center text-truncate">
											<span class="{{ $onlineAmountPercent >= 0 ? 'text-success' : 'text-danger' }} fs-12 d-flex align-items-center me-1">
												<i class="ti ti-arrow-wave-{{ $onlineAmountPercent >= 0 ? 'right-up' : 'right-down' }} me-1"></i>
												{{ $onlineAmountPercent >= 0 ? '+' : '' }}{{ $onlineAmountPercent }}%
											</span>from last week
										</p>
									</div>
								</div>
							</div>

							<!-- Offline Amount -->
							<div class="col-xl-3 col-md-4 col-sm-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body">
										<div class="d-flex align-items-center justify-content-between mb-3">
											<div>
												<span class="fs-14 fw-normal text-truncate mb-1 d-block">Offline Amount</span>
												<h4>${{ number_format($offlineAmount, 2) }}</h4>
											</div>
											<div class="avatar avatar-md bg-info-light rounded-circle">
												<i class="ti ti-cash fs-20 text-info"></i>
											</div>
										</div>
										<p class="fs-12 fw-normal d-flex align-items-center text-truncate">
											<span class="{{ $offlineAmountPercent >= 0 ? 'text-success' : 'text-danger' }} fs-12 d-flex align-items-center me-1">
												<i class="ti ti-arrow-wave-{{ $offlineAmountPercent >= 0 ? 'right-up' : 'right-down' }} me-1"></i>
												{{ $offlineAmountPercent >= 0 ? '+' : '' }}{{ $offlineAmountPercent }}%
											</span>from last week
										</p>
									</div>
								</div>
							</div>

							<!-- Active -->
							<div class="col-xl-3 col-md-4 col-sm-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body">
										<div class="d-flex align-items-center justify-content-between mb-3">
											<div>
												<span class="fs-14 fw-normal text-truncate mb-1 d-block">Active</span>
												<h4>{{ $activeSubscribers }}</h4>
											</div>
											<div class="avatar avatar-md bg-success-light rounded-circle">
												<i class="ti ti-user-check fs-20 text-success"></i>
											</div>
										</div>
										<p class="fs-12 fw-normal d-flex align-items-center text-truncate">
											<span class="{{ $activeSubscribersPercent >= 0 ? 'text-success' : 'text-danger' }} fs-12 d-flex align-items-center me-1">
												<i class="ti ti-arrow-wave-{{ $activeSubscribersPercent >= 0 ? 'right-up' : 'right-down' }} me-1"></i>
												{{ $activeSubscribersPercent >= 0 ? '+' : '' }}{{ $activeSubscribersPercent }}%
											</span>from last week
										</p>
									</div>
								</div>
							</div>

							<!-- Inactive -->
							<div class="col-xl-3 col-md-4 col-sm-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body">
										<div class="d-flex align-items-center justify-content-between mb-3">
											<div>
												<span class="fs-14 fw-normal text-truncate mb-1 d-block">Inactive</span>
												<h4>{{ $inactiveSubscribers }}</h4>
											</div>
											<div class="avatar avatar-md bg-secondary-light rounded-circle">
												<i class="ti ti-user-off fs-20 text-secondary"></i>
											</div>
										</div>
										<p class="fs-12 fw-normal d-flex align-items-center text-truncate">
											<span class="{{ $inactiveSubscribersPercent >= 0 ? 'text-success' : 'text-danger' }} fs-12 d-flex align-items-center me-1">
												<i class="ti ti-arrow-wave-{{ $inactiveSubscribersPercent >= 0 ? 'right-up' : 'right-down' }} me-1"></i>
												{{ $inactiveSubscribersPercent >= 0 ? '+' : '' }}{{ $inactiveSubscribersPercent }}%
											</span>from last week
										</p>
									</div>
								</div>
							</div>

							<!-- Cancelled -->
							<div class="col-xl-3 col-md-4 col-sm-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body">
										<div class="d-flex align-items-center justify-content-between mb-3">
											<div>
												<span class="fs-14 fw-normal text-truncate mb-1 d-block">Cancelled</span>
												<h4>{{ $cancelledSubscribers }}</h4>
											</div>
											<div class="avatar avatar-md bg-danger-light rounded-circle">
												<i class="ti ti-user-cancel fs-20 text-danger"></i>
											</div>
										</div>
										<p class="fs-12 fw-normal d-flex align-items-center text-truncate">
											<span class="{{ $cancelledSubscribersPercent >= 0 ? 'text-success' : 'text-danger' }} fs-12 d-flex align-items-center me-1">
												<i class="ti ti-arrow-wave-{{ $cancelledSubscribersPercent >= 0 ? 'right-up' : 'right-down' }} me-1"></i>
												{{ $cancelledSubscribersPercent >= 0 ? '+' : '' }}{{ $cancelledSubscribersPercent }}%
											</span>from last week
										</p>
									</div>
								</div>
							</div>

							<!-- Overdue -->
							<div class="col-xl-3 col-md-4 col-sm-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body">
										<div class="d-flex align-items-center justify-content-between mb-3">
											<div>
												<span class="fs-14 fw-normal text-truncate mb-1 d-block">Overdue</span>
												<h4>{{ $overdueSubscribers }}</h4>
											</div>
											<div class="avatar avatar-md bg-warning-light rounded-circle">
												<i class="ti ti-clock-exclamation fs-20 text-warning"></i>
											</div>
										</div>
										<p class="fs-12 fw-normal d-flex align-items-center text-truncate">
											<span class="{{ $overdueSubscribersPercent >= 0 ? 'text-success' : 'text-danger' }} fs-12 d-flex align-items-center me-1">
												<i class="ti ti-arrow-wave-{{ $overdueSubscribersPercent >= 0 ? 'right-up' : 'right-down' }} me-1"></i>
												{{ $overdueSubscribersPercent >= 0 ? '+' : '' }}{{ $overdueSubscribersPercent }}%
											</span>from last week
										</p>
									</div>
								</div>
							</div>
						</div>

						<div class="card ">
							<div class="card-header">
								<!-- Search -->
								<div class="row align-items-center">
									<div class="col-sm-4">
										<div class="icon-form mb-3 mb-sm-0">
											<span class="form-icon"><i class="ti ti-search"></i></span>
											<input type="text" class="form-control" id="custom-search-input" placeholder="Search">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
											<a href="#" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#bulk_assign_offline_plan_modal"><i class="ti ti-users-group me-2"></i>Bulk Assign Offline Plan</a>
											<a href="#" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#assign_offline_plan_modal"><i class="ti ti-plus me-2"></i>Assign Offline Plan</a>
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
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-value="Ascending">
															<i class="ti ti-circle-chevron-right me-1"></i>Ascending
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-value="Descending">
															<i class="ti ti-circle-chevron-right me-1"></i>Descending
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-value="Recently Viewed">
															<i class="ti ti-circle-chevron-right me-1"></i>Recently
															Viewed
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-value="Recently Added">
															<i class="ti ti-circle-chevron-right me-1"></i>Recently
															Added
														</a>
													</li>
												</ul>
                                                <input type="hidden" id="sort_value" value="">
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
																								<div class="border-top pt-3" id="manage-columns-list">
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Subscriber</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-subscriber" class="check toggle-vis" data-column="2" checked>
															<label for="col-subscriber" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Plan</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-plan" class="check toggle-vis" data-column="3" checked>
															<label for="col-plan" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Billing Cycle</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-billing" class="check toggle-vis" data-column="4" checked>
															<label for="col-billing" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Payment Method</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-payment" class="check toggle-vis" data-column="5" checked>
															<label for="col-payment" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Amount</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-amount" class="check toggle-vis" data-column="6" checked>
															<label for="col-amount" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Created Date</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-created" class="check toggle-vis" data-column="7" checked>
															<label for="col-created" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Expiring On</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-expiring" class="check toggle-vis" data-column="8" checked>
															<label for="col-expiring" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Status</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-status" class="check toggle-vis" data-column="9" checked>
															<label for="col-status" class="checktoggle"></label>
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
																					Paid
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Unpaid
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">Payment</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="collapseOne" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
																					<span class="checkmarks"></span>
																					Credit Card
																				</label>
																			</div>

																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Paypal
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Debit Card
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">Plan</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="collapseThree" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
																					<span class="checkmarks"></span>
																					Enterprise
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Advanced
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox">
																					<span class="checkmarks"></span>
																					Basic
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
																<a href="subscription.html" class="btn btn-primary">Filter</a>
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
									<table class="table" id="subscription-table">
										<thead class="thead-light">
											<tr>
												<th class="no-sort">
													<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
												</th>
												<th class="no-sort"></th>
												<th>Subscriber</th>
												<th>Plan</th>
												<th>Billing Cycle</th>
												<th>Payment Method</th>
												<th>Amount</th>
												<th>Created Date</th>
												<th>Expiring On</th>
												<th>Status</th>
												<th class="text-end">Action</th>
											</tr>
										</thead>
																				<tbody id="subscription-tbody"></tbody>
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

		<!-- View Invoice -->
		<div class="modal fade" id="view_invoice">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-body p-5">
						<div class="row justify-content-between align-items-center mb-3">
							<div class="col-md-6">
								<div class="mb-4">
									<img src="assets/img/logo.svg" class="img-fluid" alt="logo">
								</div>
							</div>
							<div class="col-md-6">
								<div class=" text-end mb-3">
									<h5 class="text-dark mb-1">Invoice</h5>
									<p class="mb-1 fw-normal"><i class="ti ti-file-invoice me-1"></i>INV0287</p>
									<p class="mb-1 fw-normal"><i class="ti ti-calendar me-1"></i>Issue date : 12 Sep 2024 </p>
									<p class="fw-normal"><i class="ti ti-calendar me-1"></i>Due date : 12 Oct 2024 </p>
								</div>
							</div>
						</div>
						<div class="row mb-3 d-flex justify-content-between">
							<div class="col-md-7">
								<p class="text-dark mb-2 fw-medium fs-16">Invoice From :</p>
								<div>
									<p class="mb-1">Crms</p>
									<p class="mb-1">797 Hilltop Street, Springfield</p>
									<p class="mb-1"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="492a3b243a092c31282439252c672a2624">[email&#160;protected]</a></p>
								</div>
							</div>
							<div class="col-md-5">
								<p class="text-dark mb-2 fw-medium fs-16">Invoice To :</p>
								<div>
									<p class="mb-1">BrightWave Innovations</p>
									<p class="mb-1">367 Hillcrest Lane, Irvine, California,
										United States</p>
									<p class="mb-1"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="a0cdc9c3c8c1c5cce0c5d8c1cdd0ccc58ec3cfcd">[email&#160;protected]</a></p>
								</div>
							</div>
						</div>
						<div class="mb-4">
							<div class="table-responsive mb-3">
								<table class="table">
									<thead class="thead-light">
										<tr>
											<th>Plan</th>
											<th>Billing Cycle</th>
											<th>Created Date</th>
											<th>Expiring On</th>
											<th>Amount</th>
										</tr>
									</thead>
																			<tbody id="subscription-tbody"></tbody>
								</table>
							</div>
						</div>
						<div class="row mb-3 d-flex justify-content-between">
							<div class="col-md-4">
								<div>
									<h6 class="mb-4">Payment info:</h6>
									<p class="mb-0">Credit Card - 123***********789</p>
									<div class="d-flex justify-content-between align-items-center mb-2 pe-3">
										<p class="mb-0">Amount</p>
										<p class="text-dark fw-medium mb-2">$200.00</p>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="d-flex justify-content-between align-items-center pe-3">
									<p class="text-dark fw-medium mb-0">Sub Total</p>
									<p class="mb-2">$200.00</p>
								</div>
								<div class="d-flex justify-content-between align-items-center pe-3">
									<p class="text-dark fw-medium mb-0">Tax </p>
									<p class="mb-2">$0.00</p>
								</div>
								<div class="d-flex justify-content-between align-items-center pe-3">
									<p class="text-dark fw-medium mb-0">Total</p>
									<p class="text-dark fw-medium mb-2">$200.00</p>
								</div>
							</div>
						</div>
						<div class="card border mb-0">
							<div class="card-body">
								<p class="text-dark fw-medium mb-2">Terms & Conditions:</p>
								<p class="fs-12 fw-normal d-flex align-items-baseline mb-2"><i class="ti ti-point-filled text-primary me-1"></i>All payments must be made according to the agreed schedule. Late payments may incur additional fees.</p>
								<p class="fs-12 fw-normal d-flex align-items-baseline"><i class="ti ti-point-filled text-primary me-1"></i>We are not liable for any indirect, incidental, or consequential damages, including loss of profits, revenue, or data.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /View Invoice -->

		<!-- Delete Company -->
		<div class="modal fade" id="delete_company" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Subscription?</h4>
							<p class="mb-0">Are you sure you want to remove <br> company you selected.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="subscription.html" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Company -->
		<div class="modal fade" id="preview_subscription_modal" role="dialog">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Subscription Details</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
					<div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p class="text-muted mb-1 fs-13">Subscriber Name</p>
                                <h6 class="fw-semibold" id="preview_user_name">--</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="text-muted mb-1 fs-13">Email</p>
                                <h6 class="fw-semibold" id="preview_user_email">--</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="text-muted mb-1 fs-13">Contact</p>
                                <h6 class="fw-semibold" id="preview_user_contact">--</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="text-muted mb-1 fs-13">Plan Name</p>
                                <h6 class="fw-semibold" id="preview_plan_name">--</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="text-muted mb-1 fs-13">Status</p>
                                <h6 class="fw-semibold"><span class="badge bg-primary" id="preview_status">--</span></h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="text-muted mb-1 fs-13">Start Date</p>
                                <h6 class="fw-semibold" id="preview_starts_at">--</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="text-muted mb-1 fs-13">End Date</p>
                                <h6 class="fw-semibold" id="preview_ends_at">--</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="text-muted mb-1 fs-13">Next Billing Date</p>
                                <h6 class="fw-semibold" id="preview_next_billing_at">--</h6>
                            </div>
                        </div>
                        <hr>
                        <div class="row align-items-center bg-light p-3 rounded">
                            <div class="col-md-6">
                                <p class="text-muted mb-1 fs-13">Number of Payments</p>
                                <h6 class="fw-semibold mb-0" id="preview_paid_count">--</h6>
                            </div>
                            <div class="col-md-6 text-end">
                                <p class="text-muted mb-1 fs-13">Total Revenue Collected</p>
                                <h4 class="fw-bold text-success mb-0" id="preview_total_amount">--</h4>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		</div>
<style>
	#assign_offline_plan_modal .select2-container .select2-selection--single {
		height: 42px;
		display: flex;
		align-items: center;
		border-color: #e2e8f0;
	}
	#assign_offline_plan_modal .select2-container--default .select2-selection--single .select2-selection__rendered {
		line-height: 40px;
		color: #1e293b;
		font-weight: 500;
	}
	#assign_offline_plan_modal .select2-container--default .select2-selection--single .select2-selection__arrow {
		height: 40px;
	}
	.company-opt-item {
		transition: all 0.15s ease;
	}
	.select2-container--default .select2-results__option--highlighted[aria-selected] .company-opt-item .text-dark,
	.select2-container--default .select2-results__option--highlighted[aria-selected] .company-opt-item .text-muted,
	.select2-container--default .select2-results__option--highlighted[aria-selected] .company-opt-item .text-primary,
	.select2-container--default .select2-results__option--highlighted[aria-selected] .company-opt-item .text-success,
	.select2-container--default .select2-results__option--highlighted[aria-selected] .company-opt-item .text-info {
		color: #ffffff !important;
	}
	.select2-container--default .select2-results__option--highlighted[aria-selected] .company-opt-item .badge {
		background-color: rgba(255, 255, 255, 0.25) !important;
		color: #ffffff !important;
		border-color: rgba(255, 255, 255, 0.4) !important;
	}
</style>
<!-- Assign Offline Plan Modal -->
		<div class="modal fade" id="assign_offline_plan_modal" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Assign Offline Plan</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="assign_offline_plan_form">
							@csrf
							<div class="mb-3">
								<label class="form-label">Company (Box) <span class="text-danger">*</span></label>
								<select class="form-select" name="box_id" id="assign_box_id" required>
									<option value="">Select Company</option>
								</select>
								<div class="form-text text-muted small mt-1">
									<i class="ti ti-search me-1"></i>Search company by name, slug, email, or phone (sorted newest first)
								</div>
							</div>
							<div class="mb-3">
								<label class="form-label">Plan</label>
								<select class="form-select" name="plan_id" id="assign_plan_id" required>
									<option value="">Select Plan</option>
								</select>
							</div>
							<div class="mb-3">
								<label class="form-label">Start Date</label>
								<input type="date" class="form-control" name="starts_at" required value="{{ date('Y-m-d') }}">
							</div>
							<div class="mb-3">
								<label class="form-label">End Date</label>
								<input type="date" class="form-control" name="ends_at" required value="{{ date('Y-m-d', strtotime('+1 year')) }}">
							</div>
							<div class="mb-3">
								<label class="form-label">Status</label>
								<select class="form-select" name="status" required>
									<option value="active">Active</option>
									<option value="inactive">Inactive</option>
								</select>
							</div>
							<div class="text-end">
								<button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
								<button type="submit" class="btn btn-primary" id="btn_assign_offline">Assign Plan</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Assign Offline Plan Modal -->

		<!-- Bulk Assign Offline Plan Modal -->
		<div class="modal fade" id="bulk_assign_offline_plan_modal" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Bulk Assign Offline Plan</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="bulk_assign_offline_plan_form">
							@csrf
							<div class="mb-3">
								<label class="form-label">Plan to Assign to All</label>
								<select class="form-select" name="plan_id" id="bulk_assign_plan_id" required>
									<option value="">Select Plan</option>
								</select>
							</div>
							<div class="mb-3">
								<label class="form-label">Status</label>
								<select class="form-select" name="status" required>
									<option value="active">Active</option>
									<option value="inactive">Inactive</option>
								</select>
							</div>
							<div class="alert alert-info">
								<i class="ti ti-info-circle me-1"></i> Start and End dates will be taken from each company's <strong>start_date</strong> and <strong>end_date</strong>. If missing, it will automatically be set from <strong>today until next month</strong>.
							</div>
							<div class="text-end">
								<button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
								<button type="submit" class="btn btn-primary" id="btn_bulk_assign_offline">Bulk Assign</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Bulk Assign Offline Plan Modal -->
@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#subscription-table').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            bFilter: false,
            bInfo: false,
            autoWidth: true,
            ordering: true,
            language: {
                search: ' ',
                sLengthMenu: '_MENU_',
                searchPlaceholder: "Search",
                info: "_START_ - _END_ of _TOTAL_ items",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    next: 'Next <i class=" fa fa-angle-double-right ms-2"></i>',
                    previous: '<i class="fa fa-angle-double-left me-2"></i> Previous'
                },
            },
            initComplete: (settings, json) => {
                $('.dataTables_paginate').appendTo('.datatable-paginate');
                $('.dataTables_length').appendTo('.datatable-length');
            },
            ajax: {
                url: "{{ route('superadmin.subscription', ['box_slug' => request()->route('box_slug') ?? request('box_slug') ?? session('box_slug')]) }}",
                data: function(d) {
                    d.sort = $('#sort_value').val();
                    d.date_range = $('.bookingrange').val();
                }
            },
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'star', name: 'star', orderable: false, searchable: false},
                {data: 'subscriber', name: 'subscriber', orderable: false, searchable: false},
                {data: 'plan', name: 'plan', orderable: false, searchable: false},
                {data: 'billing_cycle', name: 'billing_cycle', orderable: false, searchable: false},
                {data: 'payment_method', name: 'payment_method', orderable: false, searchable: false},
                {data: 'amount', name: 'amount', orderable: false, searchable: false},
                {data: 'starts_at', name: 'starts_at', orderable: true, searchable: false},
                {data: 'ends_at', name: 'ends_at', orderable: true, searchable: false},
                {data: 'status', name: 'status', orderable: true, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            order: [[7, 'desc']]
        });
    });
</script>
<script>
    $(document).on('click', '.btn-preview', function() {
        var id = $(this).data('id');
        var boxSlug = "{{ request()->route('box_slug') ?? request('box_slug') ?? session('box_slug') }}";
        var url = "/" + boxSlug + "/superadmin/subscription/preview/" + id;
        
        // Show loading state optionally
        $('#preview_subscription_modal').modal('show');
        $('#preview_subscription_modal h6').not('.modal-title').text('Loading...');
        
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    var data = response.data;
                    $('#preview_user_name').text(data.user_name);
                    $('#preview_user_email').text(data.user_email);
                    $('#preview_user_contact').text(data.user_contact);
                    $('#preview_plan_name').text(data.plan_name + ' ($' + data.plan_price + ')');
                    $('#preview_status').text(data.status);
                    $('#preview_starts_at').text(data.starts_at);
                    $('#preview_ends_at').text(data.ends_at);
                    $('#preview_next_billing_at').text(data.next_billing_at);
                    $('#preview_paid_count').text(data.paid_count);
                    $('#preview_total_amount').text('$' + parseFloat(data.total_amount).toFixed(2));
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                showToast('Failed to load subscription details.', 'danger');
            }
        });
    });
</script>
<script>
    $(document).on('change', '.toggle-vis', function (e) {
        e.preventDefault();
        var column = $('#subscription-table').DataTable().column($(this).attr('data-column'));
        column.visible(!column.visible());
    });
</script>
<!-- Edit Status Modal -->
<div class="modal fade" id="edit_status_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Subscription Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_status_form">
                    @csrf
                    <input type="hidden" name="subscription_id" id="edit_subscription_id">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="edit_subscription_status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="expired">Expired</option>
                            <option value="authenticated">Authenticated</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Status Modal -->

<!-- Confirm Status Modal -->
<div class="modal fade" id="confirm_status_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="avatar avatar-xl bg-warning-light rounded-circle mb-4">
                    <i class="ti ti-alert-circle fs-36 text-warning"></i>
                </div>
                <h4 class="mb-3">Confirm Status Change</h4>
                <p class="mb-4 text-muted">Are you absolutely sure you want to change the status of this subscription to <strong id="confirm_status_text" class="text-dark"></strong>?</p>
                <div class="d-flex align-items-center justify-content-center">
                    <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal" id="btn_cancel_confirm">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btn_confirm_save">Yes, Change Status</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Confirm Status Modal -->

<script>
    $(document).on('click', '.btn-edit-status', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var status = $(this).data('status');
        
        $('#edit_subscription_id').val(id);
        $('#edit_subscription_status').val(status).trigger('change');
        
        $('#edit_status_modal').modal('show');
    });

    $('#edit_status_form').on('submit', function(e) {
        e.preventDefault();
        
        // Hide the first modal and show the confirmation modal
        var newStatus = $('#edit_subscription_status option:selected').text();
        $('#confirm_status_text').text(newStatus);
        
        $('#edit_status_modal').modal('hide');
        $('#confirm_status_modal').modal('show');
    });
    
    // If they cancel the confirmation, optional: show the first modal again
    $('#btn_cancel_confirm').on('click', function() {
        // Uncomment the next line if you want the first modal to reappear on cancel
        // $('#edit_status_modal').modal('show');
    });

    $('#btn_confirm_save').on('click', function() {
        var id = $('#edit_subscription_id').val();
        var status = $('#edit_subscription_status').val();
        var boxSlug = "{{ request()->route('box_slug') ?? request('box_slug') ?? session('box_slug') }}";
        var url = "/" + boxSlug + "/superadmin/subscription/update-status/" + id;
        
        var $submitBtn = $(this);
        var originalText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                subscription_id: id,
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    $('#confirm_status_modal').modal('hide');
                    $('#subscription-table').DataTable().ajax.reload(null, false);
                }
            },
            error: function(xhr) {
                console.error("Error toggling subscription status:", xhr.responseText);
                showToast('An error occurred while updating the status.', 'danger');
            },
            complete: function() {
                $submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Filters
    $(document).ready(function() {
        $('.sort-item').on('click', function(e) {
            e.preventDefault();
            $('#sort_value').val($(this).data('value'));
            if ($.fn.DataTable.isDataTable('#subscription-table')) {
                $('#subscription-table').DataTable().ajax.reload();
            }
        });

        $('.bookingrange').on('apply.daterangepicker change', function() {
            if ($.fn.DataTable.isDataTable('#subscription-table')) {
                $('#subscription-table').DataTable().ajax.reload();
            }
        });

        var searchTimeout;
        $('#custom-search-input').on('keyup', function() {
            var val = this.value;
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                if ($.fn.DataTable.isDataTable('#subscription-table')) {
                    $('#subscription-table').DataTable().search(val).draw();
                }
            }, 300);
        });
    });
</script>
<script>
    function escapeHtml(text) {
        if (!text) return '';
        return $('<div>').text(text).html();
    }

    function matchCompany(params, data) {
        if ($.trim(params.term) === '') {
            return data;
        }
        if (typeof data.text === 'undefined') {
            return null;
        }
        var term = params.term.toLowerCase().trim();
        var cleanTermDigits = term.replace(/[^0-9]/g, '');

        var $el = $(data.element);
        var company = ($el.attr('data-company') || '').toLowerCase();
        var slug = ($el.attr('data-slug') || '').toLowerCase();
        var admin = ($el.attr('data-admin') || '').toLowerCase();
        var email = ($el.attr('data-email') || '').toLowerCase();
        var phone = ($el.attr('data-phone') || '').toLowerCase();
        var cleanPhone = phone.replace(/[^0-9]/g, '');
        var fullText = (data.text || '').toLowerCase();

        if (
            fullText.indexOf(term) > -1 ||
            company.indexOf(term) > -1 ||
            slug.indexOf(term) > -1 ||
            admin.indexOf(term) > -1 ||
            email.indexOf(term) > -1 ||
            phone.indexOf(term) > -1 ||
            (cleanTermDigits.length >= 3 && cleanPhone.indexOf(cleanTermDigits) > -1)
        ) {
            return data;
        }
        return null;
    }

    function formatCompanyOption(box) {
        if (!box.id) {
            return box.text;
        }
        var $el = $(box.element);
        var company = $el.attr('data-company') || box.text;
        var slug = $el.attr('data-slug') || '';
        var admin = $el.attr('data-admin') || '';
        var email = $el.attr('data-email') || '';
        var phone = $el.attr('data-phone') || '';

        var html = '<div class="company-opt-item py-1">' +
            '<div class="d-flex justify-content-between align-items-center mb-1">' +
                '<span class="fw-semibold text-dark fs-14">' + escapeHtml(company) + '</span>' +
                (slug ? '<span class="badge bg-light text-muted border fs-11">' + escapeHtml(slug) + '</span>' : '') +
            '</div>' +
            '<div class="text-muted fs-12 d-flex flex-wrap gap-2 align-items-center">' +
                (admin && admin !== 'Unknown' ? '<span><i class="ti ti-user me-1 text-primary"></i>' + escapeHtml(admin) + '</span>' : '') +
                (email ? '<span class="text-secondary">•</span><span><i class="ti ti-mail me-1 text-success"></i>' + escapeHtml(email) + '</span>' : '') +
                (phone ? '<span class="text-secondary">•</span><span><i class="ti ti-phone me-1 text-info"></i>' + escapeHtml(phone) + '</span>' : '') +
            '</div>' +
        '</div>';
        return $(html);
    }

    function formatCompanySelection(box) {
        if (!box.id) {
            return box.text;
        }
        var $el = $(box.element);
        var company = $el.attr('data-company') || box.text;
        var email = $el.attr('data-email');
        var phone = $el.attr('data-phone');
        var details = [];
        if (email) details.push(email);
        if (phone) details.push(phone);
        return company + (details.length ? ' (' + details.join(' | ') + ')' : '');
    }

    $(document).ready(function() {
        var boxSlug = "{{ request()->route('box_slug') ?? request('box_slug') ?? session('box_slug') }}";

        $('#assign_offline_plan_modal, #bulk_assign_offline_plan_modal').on('show.bs.modal', function () {
            // Load companies and plans
            $.ajax({
                url: "/" + boxSlug + "/superadmin/subscription/data-for-assignment",
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var boxSelect = $('#assign_box_id');
                        var planSelect = $('#assign_plan_id');
                        var bulkPlanSelect = $('#bulk_assign_plan_id');
                        
                        boxSelect.empty().append('<option value="">Select Company</option>');
                        planSelect.empty().append('<option value="">Select Plan</option>');
                        bulkPlanSelect.empty().append('<option value="">Select Plan</option>');

                        $.each(response.boxes, function(i, box) {
                            var opt = $('<option></option>')
                                .val(box.id)
                                .text(box.name)
                                .attr('data-company', box.company_name || '')
                                .attr('data-slug', box.slug || '')
                                .attr('data-admin', box.admin_name || '')
                                .attr('data-email', box.email || '')
                                .attr('data-phone', box.phone || '');
                            if (box.last_active_end_date) {
                                opt.attr('data-expiry', box.last_active_end_date);
                            }
                            boxSelect.append(opt);
                        });

                        $.each(response.plans, function(i, plan) {
                            planSelect.append('<option value="'+plan.id+'" data-type="'+plan.type+'">'+plan.name+'</option>');
                            bulkPlanSelect.append('<option value="'+plan.id+'" data-type="'+plan.type+'">'+plan.name+'</option>');
                        });

                        // Initialize Select2 for the Company and Plan selects to enable typeahead search
                        if ($.fn.select2) {
                            if ($('#assign_box_id').hasClass("select2-hidden-accessible")) {
                                $('#assign_box_id').select2('destroy');
                            }
                            $('#assign_box_id').select2({
                                dropdownParent: $('#assign_offline_plan_modal'),
                                width: '100%',
                                placeholder: 'Search Company by Name, Email, or Phone...',
                                matcher: matchCompany,
                                templateResult: formatCompanyOption,
                                templateSelection: formatCompanySelection
                            });

                            if ($('#assign_plan_id').hasClass("select2-hidden-accessible")) {
                                $('#assign_plan_id').select2('destroy');
                            }
                            $('#assign_plan_id').select2({
                                dropdownParent: $('#assign_offline_plan_modal'),
                                width: '100%',
                                placeholder: 'Search Plan...'
                            });

                            if ($('#bulk_assign_plan_id').hasClass("select2-hidden-accessible")) {
                                $('#bulk_assign_plan_id').select2('destroy');
                            }
                            $('#bulk_assign_plan_id').select2({
                                dropdownParent: $('#bulk_assign_offline_plan_modal'),
                                width: '100%',
                                placeholder: 'Search Plan...'
                            });
                        }
                    }
                }
            });
        });

        $('#assign_offline_plan_modal').on('hidden.bs.modal', function () {
            $('#assign_offline_plan_form')[0].reset();
            if ($.fn.select2) {
                $('#assign_box_id').val('').trigger('change');
                $('#assign_plan_id').val('').trigger('change');
            }
        });

        // When company is selected, check if they have a future expiry date and auto-fill starts_at
        $('#assign_box_id').on('change', function() {
            var selectedOpt = $(this).find(':selected');
            var expiryDate = selectedOpt.data('expiry') || selectedOpt.attr('data-expiry');
            
            if (expiryDate) {
                $('input[name="starts_at"]').val(expiryDate);
            } else {
                // Default to today if no active subscription found
                var today = new Date();
                var yyyy = today.getFullYear();
                var mm = String(today.getMonth() + 1).padStart(2, '0');
                var dd = String(today.getDate()).padStart(2, '0');
                $('input[name="starts_at"]').val(yyyy + '-' + mm + '-' + dd);
            }
            // Trigger end date update
            updateEndDate();
        });

        function updateEndDate() {
            var planOption = $('#assign_plan_id').find(':selected');
            var planType = planOption.data('type');
            var startDateStr = $('input[name="starts_at"]').val();
            
            if (planType && startDateStr) {
                var startDate = new Date(startDateStr);
                if (!isNaN(startDate.getTime())) {
                    if (planType === 'yearly') {
                        startDate.setFullYear(startDate.getFullYear() + 1);
                    } else if (planType === 'monthly') {
                        startDate.setMonth(startDate.getMonth() + 1);
                    }
                    // Adjust to local date string instead of UTC which toISOString uses
                    var year = startDate.getFullYear();
                    var month = String(startDate.getMonth() + 1).padStart(2, '0');
                    var day = String(startDate.getDate()).padStart(2, '0');
                    $('input[name="ends_at"]').val(year + '-' + month + '-' + day);
                }
            }
        }

        $('#assign_plan_id').on('change', updateEndDate);
        $('input[name="starts_at"]').on('change', updateEndDate);

        $('#assign_offline_plan_form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            var submitBtn = $('#btn_assign_offline');
            var originalText = submitBtn.text();
            
            submitBtn.text('Assigning...').prop('disabled', true);
            
            $.ajax({
                url: "/" + boxSlug + "/superadmin/subscription/assign-offline",
                type: 'POST',
                data: formData,
                success: function(response) {
                    submitBtn.text(originalText).prop('disabled', false);
                    if (response.success) {
                        $('#assign_offline_plan_modal').modal('hide');
                        $('#assign_offline_plan_form')[0].reset();
                        
                        showToast(response.message, 'success');

                        if ($.fn.DataTable.isDataTable('#subscription-table')) {
                            $('#subscription-table').DataTable().ajax.reload();
                        }
                    }
                },
                error: function(xhr) {
                    submitBtn.text(originalText).prop('disabled', false);
                    var errorMessage = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showToast(errorMessage, 'danger');
                }
            });
        });

        $('#bulk_assign_offline_plan_form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            var submitBtn = $('#btn_bulk_assign_offline');
            var originalText = submitBtn.text();
            
            submitBtn.text('Assigning...').prop('disabled', true);
            
            $.ajax({
                url: "/" + boxSlug + "/superadmin/subscription/bulk-assign-offline",
                type: 'POST',
                data: formData,
                success: function(response) {
                    submitBtn.text(originalText).prop('disabled', false);
                    if (response.success) {
                        $('#bulk_assign_offline_plan_modal').modal('hide');
                        $('#bulk_assign_offline_plan_form')[0].reset();
                        if ($.fn.select2) {
                            $('#bulk_assign_plan_id').val(null).trigger('change');
                        }
                        
                        showToast(response.message, 'success');

                        if ($.fn.DataTable.isDataTable('#subscription-table')) {
                            $('#subscription-table').DataTable().ajax.reload();
                        }
                    }
                },
                error: function(xhr) {
                    submitBtn.text(originalText).prop('disabled', false);
                    var errorMessage = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showToast(errorMessage, 'danger');
                }
            });
        });
    });
</script>
@endpush
@endsection
