@extends('layouts.main')
@section('content')
@section('title','Packages')

<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-8">
									<h4 class="page-title">Packages</h4>
								</div>
								<div class="col-4 text-end">
									<div class="head-icons">
										<a href="cities.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
										<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
									</div>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

						<div class="row">

							<!-- Total Plans -->
							<div class="col-lg-3 col-md-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body d-flex align-items-center justify-content-between">
										<div class="d-flex align-items-center overflow-hidden">
											<div>
												<p class="fs-12 fw-medium mb-1 text-truncate">Total Plans</p>
												<h4 id="total-plans-count">{{ count($plans) }}</h4>
											</div>
										</div>
										<div>
											<span class="avatar avatar-lg bg-primary flex-shrink-0">
												<i class="ti ti-box fs-16"></i>
											</span>
										</div>
									</div>
								</div>
							</div>
							<!-- /Total Plans -->

							<!-- Total Plans -->
							<div class="col-lg-3 col-md-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body d-flex align-items-center justify-content-between">
										<div class="d-flex align-items-center overflow-hidden">
											<div>
												<p class="fs-12 fw-medium mb-1 text-truncate">Active Plans</p>
												<h4 id="active-plans-count">{{ $plans->where('status', 'active')->count() }}</h4>
											</div>
										</div>
										<div>
											<span class="avatar avatar-lg bg-success flex-shrink-0">
												<i class="ti ti-activity-heartbeat fs-16"></i>
											</span>
										</div>
									</div>
								</div>
							</div>
							<!-- /Total Plans -->

							<!-- Inactive Plans -->
							<div class="col-lg-3 col-md-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body d-flex align-items-center justify-content-between">
										<div class="d-flex align-items-center overflow-hidden">
											<div>
												<p class="fs-12 fw-medium mb-1 text-truncate">Inactive Plans</p>
												<h4 id="inactive-plans-count">{{ $plans->where('status', 'inactive')->count() }}</h4>
											</div>
										</div>
										<div>
											<span class="avatar avatar-lg bg-danger flex-shrink-0">
												<i class="ti ti-player-pause fs-16"></i>
											</span>
										</div>
									</div>
								</div>
							</div>
							<!-- /Inactive Companies -->

							<!-- No of Plans  -->
							<div class="col-lg-3 col-md-6 d-flex">
								<div class="card flex-fill">
									<div class="card-body d-flex align-items-center justify-content-between">
										<div class="d-flex align-items-center overflow-hidden">
											<div>
												<p class="fs-12 fw-medium mb-1 text-truncate">No of Plan Types</p>
												<h4 id="plan-types-count">{{ $plans->unique('type')->count() }}</h4>
											</div>
										</div>
										<div>
											<span class="avatar avatar-lg bg-skyblue flex-shrink-0">
												<i class="ti ti-mask fs-16"></i>
											</span>
										</div>
									</div>
								</div>
							</div>
							<!-- /No of Plans -->

						</div>

						<div class="card">

							<div class="card-header">
								<!-- Search -->
								<div class="row align-items-center">
									<div class="col-sm-4">
										<div class="icon-form mb-3 mb-sm-0">
											<span class="form-icon"><i class="ti ti-search"></i></span>
											<input type="text" class="form-control" id="plan-search-input" placeholder="Search Plan">
										</div>
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
											<button id="bulk-delete-btn" class="btn btn-danger me-2" style="display: none;"><i class="ti ti-trash me-1"></i>Delete Selected</button>
											<a href="{{ box_route('superadmin.package-features.page') }}" class="btn btn-outline-primary me-2"><i class="ti ti-settings me-1"></i>Manage Features</a>
											<a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-square-rounded-plus me-2"></i>Add New Plan</a>
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
									<div class="d-flex align-items-center flex-wrap row-gap-2">
										<div class="dropdown me-2">
											<a href="javascript:void(0);" class="btn bg-soft-purple text-purple" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-columns-3 me-2"></i>Manage Columns</a>
											<div class="dropdown-menu  dropdown-menu-md-end dropdown-md p-3">
												<h4 class="mb-2 fw-semibold">Want to manage datatables?</h4>
												<p class="mb-3">Please drag and drop your column to reorder your table
													and enable see option as you want.</p>
												<div class="border-top pt-3">
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Plan Name</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-name" class="check">
															<label for="col-name" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Plan Type</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-phone" class="check">
															<label for="col-phone" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Total Subscribers</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-email" class="check">
															<label for="col-email" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Price</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-tag" class="check">
															<label for="col-tag" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Created Date</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-date" class="check">
															<label for="col-date" class="checktoggle"></label>
														</div>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<p class="mb-0 d-flex align-items-center"><i class="ti ti-grip-vertical me-2"></i>Status</p>
														<div class="status-toggle">
															<input type="checkbox" id="col-activity" class="check">
															<label for="col-activity" class="checktoggle"></label>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="form-sorts dropdown me-2">
											<a href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-filter-share"></i>Filter</a>
											<div class="filter-dropdown-menu dropdown-menu  dropdown-menu-md-end p-3">
												<div class="filter-set-view">
													<div class="filter-set-head">
														<h4><i class="ti ti-filter-share"></i>Filter</h4>
													</div>
													<div class="accordion" id="accordionExample">
														<div class="filter-set-content">
															<div class="filter-set-content-head">
																<a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#location" aria-expanded="false" aria-controls="location">Select Plan</a>
															</div>
															<div class="filter-set-contents accordion-collapse collapse" id="location" data-bs-parent="#accordionExample">
																<div class="filter-content-list">
																	<ul>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
																					<span class="checkmarks"></span>
																					Monthly
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
																					<span class="checkmarks"></span>
																					Yearly
																				</label>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
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
																					Active
																				</label>
																			</div>
																		</li>
																		<li>
																			<div class="filter-checks">
																				<label class="checkboxs">
																					<input type="checkbox" checked="">
																					<span class="checkmarks"></span>
																					Inactive
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
																<a href="packages.html" class="btn btn-primary">Filter</a>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="view-icons">
											<a href="packages.html" class="active"><i class="ti ti-list-tree"></i></a>
											<a href="packages-grid.html"><i class="ti ti-grid-dots"></i></a>
										</div>
									</div>
								</div>
								<!-- /Filter -->

								<!-- Manage Users List -->
								<div id="package-list-container">
                                    @include('frontend.superadmin.partials.package_list')
                                </div>
								<div class="row align-items-center">
									<div class="col-md-6">
										<div class="datatable-length"></div>
									</div>
									<div class="col-md-6">
										<div class="datatable-paginate"></div>
									</div>
								</div>
								<!-- /Manage Users List -->

							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Add Plan -->
		<div class="offcanvas offcanvas-end offcanvas-large" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Add New Plan</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="{{ box_route('superadmin.packages.store') }}" method="POST" id="add_plan_form">
                    @csrf
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									<div class="mb-3">
										<label class="form-label">Plan Name<span class="text-danger"> *</span></label>
										<input type="text" class="form-control" name="name" required>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Plan Type<span class="text-danger"> *</span></label>
										<select class="select form-control" name="type" required>
											<option value="monthly">Monthly</option>
											<option value="yearly">Yearly</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Plan Category<span class="text-danger"> *</span></label>
										<select class="select form-control" name="plan_type" id="add_plan_type" required>
											<option value="subscription">Subscription</option>
											<option value="topup">Topup</option>
											<option value="whatsapp">WhatsApp Plan</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Payment Mode<span class="text-danger"> *</span></label>
										<select class="select form-control" name="payment_mode" required>
											<option value="online">Online</option>
											<option value="offline">Offline</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Price<span class="text-danger"> *</span></label>
										<input type="number" step="0.01" class="form-control" name="price" required>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Currency<span class="text-danger"> *</span></label>
										<select class="select form-control" name="currency" required>
                                            <option value="AED">AED (د.إ) - United Arab Emirates dirham</option>
                                            <option value="AUD">AUD ($) - Australian dollar</option>
                                            <option value="BGN">BGN (лв) - Bulgarian lev</option>
                                            <option value="BRL">BRL (R$) - Brazilian real</option>
                                            <option value="CAD">CAD ($) - Canadian dollar</option>
                                            <option value="CHF">CHF (Fr.) - Swiss franc</option>
                                            <option value="CNY">CNY (¥) - Chinese yuan</option>
                                            <option value="EUR">EUR (€) - Euro</option>
                                            <option value="GBP">GBP (£) - British pound</option>
                                            <option value="HKD">HKD ($) - Hong Kong dollar</option>
                                            <option value="IDR">IDR (Rp) - Indonesian rupiah</option>
                                            <option value="ILS">ILS (₪) - Israeli new shekel</option>
                                            <option value="INR" selected>INR (₹) - Indian rupee</option>
                                            <option value="JPY">JPY (¥) - Japanese yen</option>
                                            <option value="KES">KES (Sh) - Kenyan shilling</option>
                                            <option value="KRW">KRW (₩) - South Korean won</option>
                                            <option value="MXN">MXN ($) - Mexican peso</option>
                                            <option value="MYR">MYR (RM) - Malaysian ringgit</option>
                                            <option value="NGN">NGN (₦) - Nigerian naira</option>
                                            <option value="NZD">NZD ($) - New Zealand dollar</option>
                                            <option value="PHP">PHP (₱) - Philippine peso</option>
                                            <option value="PLN">PLN (zł) - Polish złoty</option>
                                            <option value="RUB">RUB (₽) - Russian ruble</option>
                                            <option value="SAR">SAR (ر.س) - Saudi riyal</option>
                                            <option value="SGD">SGD ($) - Singapore dollar</option>
                                            <option value="THB">THB (฿) - Thai baht</option>
                                            <option value="TRY">TRY (₺) - Turkish lira</option>
                                            <option value="TWD">TWD (NT$) - New Taiwan dollar</option>
                                            <option value="UAH">UAH (₴) - Ukrainian hryvnia</option>
                                            <option value="UGX">UGX (Sh) - Ugandan shilling</option>
                                            <option value="USD">USD ($) - United States dollar</option>
                                            <option value="UYU">UYU ($) - Uruguayan peso</option>
                                            <option value="UZS">UZS (so'm) - Uzbekistani soʻm</option>
                                            <option value="VES">VES (Bs.S.) - Venezuelan bolívar soberano</option>
                                            <option value="VND">VND (₫) - Vietnamese đồng</option>
                                            <option value="ZAR">ZAR (R) - South African rand</option>
										</select>
									</div>
								</div>
                                <div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Trial Period (Days)</label>
										<input type="number" class="form-control" name="trial_period_days" value="0" min="0">
									</div>
								</div>
                                <div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Grace Period (Days)</label>
										<input type="number" class="form-control" name="grace_period_days" value="0" min="0">
									</div>
								</div>
                                <div class="col-md-6">
									<div class="mb-3 ">
										<label class="form-label">Status<span class="text-danger"> *</span></label>
										<select class="select form-control" name="status" required>
											<option value="active" selected>Active</option>
											<option value="inactive">Inactive</option>
										</select>
									</div>
								</div>

								<div class="col-md-12 whatsapp-fields" style="display:none;">
									<h6 class="mb-3 border-bottom pb-2">WhatsApp Plan Details</h6>
								</div>
								<div class="col-md-6 whatsapp-fields" style="display:none;">
									<div class="mb-3">
										<label class="form-label">Discount Badge</label>
										<input type="text" class="form-control" name="discount_badge" placeholder="e.g. Save 20%">
									</div>
								</div>
								<div class="col-md-6 whatsapp-fields" style="display:none;">
									<div class="mb-3">
										<label class="form-label">Savings Text</label>
										<input type="text" class="form-control" name="savings_text" placeholder="e.g. You save ₹1500">
									</div>
								</div>
								<div class="col-md-6 whatsapp-fields" style="display:none;">
									<div class="mb-3">
										<label class="form-label">Original Price</label>
										<input type="number" step="0.01" class="form-control" name="original_price">
									</div>
								</div>
								<div class="col-md-6 whatsapp-fields" style="display:none;">
									<div class="mb-3">
										<label class="form-label">Billing Cycle</label>
										<input type="text" class="form-control" name="billing_cycle" placeholder="e.g. /Month">
									</div>
								</div>
								<div class="col-md-6 whatsapp-fields" style="display:none;">
									<div class="mb-3">
										<label class="form-label">Marketing Msg Cost</label>
										<input type="text" class="form-control" name="marketing_msg_cost" placeholder="e.g. ₹0.72">
									</div>
								</div>
								<div class="col-md-6 whatsapp-fields" style="display:none;">
									<div class="mb-3">
										<label class="form-label">Utility Msg Cost</label>
										<input type="text" class="form-control" name="utility_msg_cost" placeholder="e.g. ₹0.30">
									</div>
								</div>
								<div class="col-md-6 whatsapp-fields" style="display:none;">
									<div class="mb-3">
										<label class="form-label">Button Text</label>
										<input type="text" class="form-control" name="button_text" placeholder="e.g. Subscribe Now">
									</div>
								</div>

                                <div class="col-md-12">
									<div class="mb-3">
										<label class="form-label">Plan Features</label>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th style="width: 50px;" class="text-center">Add</th>
                                                        <th>Feature</th>
                                                        <th>Value / Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="add_plan_features_list">
                                                    @foreach($masterFeatures as $feature)
                                                    <tr class="feature-row" data-type="{{ $feature->feature_type ?? 'crm' }}" style="{{ ($feature->feature_type ?? 'crm') == 'whatsapp' ? 'display:none;' : '' }}">
                                                        <td class="text-center">
                                                            <input type="checkbox" class="form-check-input feature-checkbox" checked>
                                                        </td>
                                                        <td>{{ $feature->name }}</td>
                                                        <td>
                                                            <input type="text" class="form-control form-control-sm feature-value-input" name="features[{{ $feature->name }}]" placeholder="e.g. ✅, ❌, 5 Users, ₹2500 / Year">
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
									</div>
								</div>
                                <div class="col-md-12">
									<div class="mb-3 ">
										<label class="form-label">Description</label>
										<textarea class="form-control" name="description" rows="3"></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary">Create Plan</button>
					</div>
				</form>
			</div>
		</div>
		<!-- /Add Plan -->


    <!-- Toast Notification Container -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 11000;">
        <div id="toastMessage" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastBody">
                    <!-- Toast message will be inserted here -->
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>




		<!-- Delete Company -->
		<div class="modal fade" id="delete_invoices" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Plan?</h4>
							<p class="mb-0">Are you sure you want to remove the plan <br> you selected?</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
								<button type="button" id="confirm_delete_plan" class="btn btn-danger">Yes, Delete it</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Company -->

<style>
    .bg-soft-success {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }
    .smaller {
        font-size: 0.75rem;
    }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        let allPlans = [];

        function showSuccessToast(message) {
            const $toast = $('#toastMessage');
            const $toastBody = $('#toastBody');
            $toast.removeClass('bg-danger bg-success bg-warning').addClass('bg-success');
            $toastBody.html(`
                <div class="d-flex align-items-center">
                    <i class="ti ti-check-circle me-2 fs-5"></i>
                    <div class="flex-grow-1">
                        <strong>Success!</strong><br>
                        <small>${message}</small>
                    </div>
                </div>
            `);
            if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                const toast = new bootstrap.Toast($toast[0], { autohide: true, delay: 5000 });
                toast.show();
            } else {
                alert(message);
            }
        }

        function showErrorToast(message) {
            const $toast = $('#toastMessage');
            const $toastBody = $('#toastBody');
            $toast.removeClass('bg-danger bg-success bg-warning').addClass('bg-danger');
            $toastBody.html(`
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-circle me-2 fs-5"></i>
                    <div class="flex-grow-1">
                        <strong>Error!</strong><br>
                        <small>${message}</small>
                    </div>
                </div>
            `);
            if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                const toast = new bootstrap.Toast($toast[0], { autohide: true, delay: 5000 });
                toast.show();
            } else {
                alert(message);
            }
        }

        let masterFeatures = {!! json_encode($masterFeatures) !!};

        // Toast Helpers
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Helper function for box route equivalent in JS
        function getBoxRoute(id, action) {
            let baseUrl = "{{ box_route('superadmin.packages.store') }}";
            // The above route is usually something like /box_slug/superadmin/packages
            // We need to construct update/delete routes which are /box_slug/superadmin/packages/{id}
            if (action === 'update' || action === 'destroy') {
                return baseUrl + '/' + id;
            }
            return baseUrl;
        }

        // Load Package List AJAX
        function loadPackages() {
            $.ajax({
                url: window.location.href,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        allPlans = response.plans;
                        if(response.masterFeatures) {
                            masterFeatures = response.masterFeatures;
                            renderMasterFeatures();
                        }
                        renderTable(allPlans);
                    }
                },
                error: function() {
                    showErrorToast('Failed to load package list.');
                }
            });
        }

        function renderMasterFeatures() {
            // Populate Add Plan feature inputs
            let addList = $('#add_plan_features_list');
            addList.empty();
            masterFeatures.forEach(feature => {
                let featureType = feature.feature_type || 'crm';
                addList.append(`
                    <tr class="feature-row" data-type="${featureType}">
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input feature-checkbox" checked>
                        </td>
                        <td>${feature.name}</td>
                        <td>
                            <input type="text" class="form-control form-control-sm feature-value-input" name="features[${feature.name}]" placeholder="e.g. ✅, ❌, 5 Users">
                        </td>
                    </tr>
                `);
            });
            toggleAddFeatures($('#add_plan_type').val() === 'whatsapp' ? 'whatsapp' : 'crm');
        }

        function toggleAddFeatures(type) {
            $('#add_plan_features_list .feature-row').hide();
            $('#add_plan_features_list .feature-row[data-type="' + type + '"]').show();
        }

        function renderTable(plans) {
            let tbody = $('#package-table-body');
            tbody.empty();

            // Update Stats
            $('#total-plans-count').text(plans.length);
            $('#active-plans-count').text(plans.filter(p => p.status === 'active').length);
            $('#inactive-plans-count').text(plans.filter(p => p.status === 'inactive').length);
            $('#plan-types-count').text([...new Set(plans.map(p => p.type))].length);

            if (plans.length === 0) {
                tbody.append('<tr><td colspan="9" class="text-center">No plans found.</td></tr>');
                return;
            }

            plans.forEach(plan => {
                let createdAt = new Date(plan.created_at);
                let formattedDate = createdAt.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                
                let row = `
                    <tr>
                        <td>
                            <label class="checkboxs"><input type="checkbox" class="row-checkbox" value="${plan.id}"><span class="checkmarks"></span></label>
                        </td>
                        <td>
                            <div class="set-star rating-select"><i class="fa fa-star"></i></div>
                        </td>
                        <td>
                            <h6 class="fw-medium mb-1"><a href="#">${plan.name}</a></h6>
                            ${plan.trial_period_days > 0 ? `<span class="badge bg-soft-success text-success smaller"><i class="ti ti-gift me-1"></i> ${plan.trial_period_days} Days Trial</span>` : ''}
                        </td>
                        <td>
                            ${plan.plan_type === 'whatsapp' 
                                ? '<span class="badge bg-soft-success text-success"><i class="fa fa-whatsapp"></i> WhatsApp</span>' 
                                : (plan.plan_type ? plan.plan_type.charAt(0).toUpperCase() + plan.plan_type.slice(1) : 'Subscription')}
                        </td>
                        <td>${plan.payment_mode ? plan.payment_mode.charAt(0).toUpperCase() + plan.payment_mode.slice(1) : 'Online'}</td>
                        <td>${plan.type.charAt(0).toUpperCase() + plan.type.slice(1)}</td>
                        <td>${plan.subscriptions_count || 0}</td>
                        <td>${plan.currency} ${plan.price}</td>
                        <td>
                            ${plan.razorpay_plan_id ? `<span class="badge bg-outline-secondary">${plan.razorpay_plan_id}</span>` : '<span class="text-warning small"><i class="ti ti-alert-circle"></i> Unsynced</span>'}
                        </td>
                        <td>${formattedDate}</td>
                        <td>
                            <span class="badge badge-pill badge-status ${plan.status === 'active' ? 'bg-success' : 'bg-danger'}">${plan.status.charAt(0).toUpperCase() + plan.status.slice(1)}</span>
                        </td>
                        <td>
                            <div class="dropdown table-action">
                                <a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item edit-plan-btn" href="#" data-id="${plan.id}">
                                        <i class="ti ti-edit text-blue"></i> Edit
                                    </a>
                                    <a class="dropdown-item delete-plan-btn" href="#" data-id="${plan.id}">
                                        <i class="ti ti-trash text-danger"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
            
            // Re-initialize Select2 if needed
            if ($('.select').length > 0) {
                $('.select').select2();
            }
        }

        // Initialize table
        renderMasterFeatures();
        loadPackages();



        // Toggle WhatsApp fields for Add Form
        $('#add_plan_type').on('change', function() {
            if ($(this).val() === 'whatsapp') {
                $('.whatsapp-fields').show();
                toggleAddFeatures('whatsapp');
            } else {
                $('.whatsapp-fields').hide();
                toggleAddFeatures('crm');
            }
        });

        function toggleEditFeatures(type) {
            $('#edit_plan_features_list .edit-feature-row').hide();
            $('#edit_plan_features_list .edit-feature-row[data-type="' + type + '"]').show();
        }

        // Toggle WhatsApp fields for Edit Form
        $('#edit_plan_category').on('change', function() {
            if ($(this).val() === 'whatsapp') {
                $('.edit-whatsapp-fields').show();
                toggleEditFeatures('whatsapp');
            } else {
                $('.edit-whatsapp-fields').hide();
                toggleEditFeatures('crm');
            }
        });

        // AJAX Store Plan
        $('#add_plan_form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var formData = form.serialize();

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showSuccessToast(response.message);
                        form[0].reset();
                        bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('offcanvas_add')).hide();
                        
                        allPlans.push(response.plan);
                        renderTable(allPlans);
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON?.errors;
                    if (errors) {
                        let firstError = Object.values(errors)[0][0];
                        showErrorToast(firstError);
                    } else {
                        showErrorToast('Something went wrong. Please try again.');
                    }
                }
            });
        });

        // Edit Plan Click (Populate and Show)
        $(document).on('click', '.edit-plan-btn', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let plan = allPlans.find(p => p.id == id);
            
            if (plan) {
                $('#edit_plan_id').val(plan.id);
                $('#edit_plan_name').val(plan.name);
                $('#edit_plan_type').val(plan.type).trigger('change');
                $('#edit_plan_category').val(plan.plan_type).trigger('change');
                $('#edit_plan_payment_mode').val(plan.payment_mode).trigger('change');
                $('#edit_plan_price').val(plan.price);
                $('#edit_plan_currency').val(plan.currency).trigger('change');
                $('#edit_plan_trial_period').val(plan.trial_period_days);
                $('#edit_plan_grace_period').val(plan.grace_period_days);
                $('#edit_plan_status').val(plan.status).trigger('change');
                $('#edit_plan_description').val(plan.description);
                $('#edit_plan_discount_badge').val(plan.discount_badge);
                $('#edit_plan_savings_text').val(plan.savings_text);
                $('#edit_plan_original_price').val(plan.original_price);
                $('#edit_plan_billing_cycle').val(plan.billing_cycle);
                $('#edit_plan_marketing_msg_cost').val(plan.marketing_msg_cost);
                $('#edit_plan_utility_msg_cost').val(plan.utility_msg_cost);
                $('#edit_plan_button_text').val(plan.button_text);
                
                // Populate features table
                let editList = $('#edit_plan_features_list');
                editList.empty();
                masterFeatures.forEach(feature => {
                    let featureType = feature.feature_type || 'crm';
                    let value = plan.features && plan.features[feature.name] ? plan.features[feature.name] : '';
                    let isChecked = value && value !== '❌' ? 'checked' : '';
                    editList.append(`
                        <tr class="edit-feature-row" data-type="${featureType}">
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input feature-checkbox" ${isChecked}>
                            </td>
                            <td>${feature.name}</td>
                            <td>
                                <input type="text" class="form-control form-control-sm feature-value-input" name="features[${feature.name}]" value="${value}" placeholder="e.g. ✅, ❌, 5 Users">
                            </td>
                        </tr>
                    `);
                });
                toggleEditFeatures($('#edit_plan_category').val() === 'whatsapp' ? 'whatsapp' : 'crm');

                $('#edit_plan_form').attr('action', getBoxRoute(plan.id, 'update'));
                bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('offcanvas_edit')).show();
            }
        });



        // Bulk Delete Logic
        function toggleBulkDeleteButton() {
            var selectedCount = $('.row-checkbox:checked').length;
            if (selectedCount > 0) {
                $('#bulk-delete-btn').show();
            } else {
                $('#bulk-delete-btn').hide();
            }
        }

        $(document).on('change', '#select-all', function() {
            $('.row-checkbox').prop('checked', $(this).prop('checked'));
            toggleBulkDeleteButton();
        });

        $(document).on('change', '.row-checkbox', function() {
            if ($('.row-checkbox:checked').length == $('.row-checkbox').length) {
                $('#select-all').prop('checked', true);
            } else {
                $('#select-all').prop('checked', false);
            }
            toggleBulkDeleteButton();
        });

        let deleteMode = 'single';

        $('#bulk-delete-btn').on('click', function(e) {
            e.preventDefault();
            var selectedIds = [];
            $('.row-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) return;

            planIdToDelete = selectedIds;
            deleteMode = 'bulk';
            $('#delete_invoices').modal('show');
        });

        // AJAX Update Plan
        $('#edit_plan_form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var formData = form.serialize();

            $.ajax({
                url: url,
                type: 'POST', // Method spoofed to PUT via _method in form
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showSuccessToast(response.message);
                        bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('offcanvas_edit')).hide();
                        
                        let index = allPlans.findIndex(p => p.id == response.plan.id);
                        if (index !== -1) {
                            allPlans[index] = response.plan;
                        }
                        renderTable(allPlans);
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON?.errors;
                    if (errors) {
                        let firstError = Object.values(errors)[0][0];
                        showErrorToast(firstError);
                    } else {
                        showErrorToast('Failed to update plan.');
                    }
                }
            });
        });

        // AJAX Delete Plan (Open Modal)
        let planIdToDelete = null;
        $(document).on('click', '.delete-plan-btn', function(e) {
            e.preventDefault();
            planIdToDelete = $(this).data('id');
            deleteMode = 'single';
            $('#delete_invoices').modal('show');
        });

        // Confirm Delete Plan
        $('#confirm_delete_plan').on('click', function() {
            if(!planIdToDelete) return;

            if (deleteMode === 'bulk') {
                $.ajax({
                    url: "{{ route('superadmin.packages.bulk_destroy', ['box_slug' => request()->route('box_slug')]) }}",
                    type: "POST",
                    data: { ids: planIdToDelete },
                    success: function(response) {
                        if(response.success) {
                            $('#delete_invoices').modal('hide');
                            showSuccessToast(response.message);
                            $('#select-all').prop('checked', false);
                            toggleBulkDeleteButton();
                            loadPackages();
                        }
                    },
                    error: function() {
                        $('#delete_invoices').modal('hide');
                        showErrorToast('Failed to delete selected plans.');
                    }
                });
            } else {            
                $.ajax({
                    url: getBoxRoute(planIdToDelete, 'destroy'),
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            $('#delete_invoices').modal('hide');
                            showSuccessToast(response.message);
                            allPlans = allPlans.filter(p => p.id != planIdToDelete);
                            renderTable(allPlans);
                            planIdToDelete = null;
                        }
                    },
                    error: function() {
                        $('#delete_invoices').modal('hide');
                        showErrorToast('Failed to delete plan.');
                    }
                });
            }
        });

        // Search Functionality
        $('#plan-search-input').on('keyup', function() {
            let value = $(this).val().toLowerCase();
            let filteredPlans = allPlans.filter(plan => {
                return plan.name.toLowerCase().includes(value) || 
                       plan.type.toLowerCase().includes(value) || 
                       plan.price.toString().includes(value);
            });
            renderTable(filteredPlans);
        });
        // Handle feature checkbox toggle
        $(document).on('change', '.feature-checkbox', function() {
            let row = $(this).closest('tr');
            let input = row.find('.feature-value-input');
            if ($(this).is(':checked')) {
                input.prop('disabled', false);
                if (input.val() === '❌' || input.val() === '') {
                    input.val('✅');
                }
            } else {
                input.val('❌');
                input.prop('disabled', true);
            }
        });

        // Ensure features have correct ✅ or ❌ values on submit
        $('#add_plan_form, #edit_plan_form').on('submit', function() {
            $(this).find('.feature-checkbox').each(function() {
                let row = $(this).closest('tr');
                let input = row.find('.feature-value-input');
                if ($(this).is(':checked')) {
                    input.prop('disabled', false);
                    if (input.val().trim() === '' || input.val() === '❌') {
                        input.val('✅');
                    }
                } else {
                    input.prop('disabled', false); // Enable so it's included in serialize()
                    input.val('❌');
                }
            });
        });
        // Ensure Select2 search works inside Bootstrap Offcanvas by setting dropdownParent
        $('#offcanvas_add').on('shown.bs.offcanvas', function () {
            $(this).find('.select').each(function() {
                $(this).select2({ dropdownParent: $('#offcanvas_add') });
            });
        });
        $('#offcanvas_edit').on('shown.bs.offcanvas', function () {
            $(this).find('.select').each(function() {
                $(this).select2({ dropdownParent: $('#offcanvas_edit') });
            });
        });

    });
</script>
@endpush
@endsection

