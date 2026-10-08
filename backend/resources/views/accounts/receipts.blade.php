@extends('layouts.main')
@section('content')
@section('title', 'Receipts')
		<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">
				<div class="row">
					<div class="col-md-12">
						<!-- Page Header -->
						<div class="page-header">
							<div class="row align-items-center">
								<div class="col-4">
									<h4 class="page-title">Receipt</h4>
								</div>
								<div class="col-8 text-end">
									<div class="head-icons">
										<a href="invoices.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
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
										<div class="icon-form mb-3 mb-sm-0">
											<span class="form-icon"><i class="ti ti-search"></i></span>
											<input type="text" class="form-control" id="search_global" placeholder="Search Receipts">
										</div>
									</div>
									<div class="col-sm-8">
										<div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
											<div class="dropdown me-2">
												<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
												<div class="dropdown-menu  dropdown-menu-end">
													<ul>
														<li>
															<a href="javascript:void(0);" class="dropdown-item export-btn" data-format="pdf" data-bs-toggle="modal" data-bs-target="#export_receipts_modal"><i class="ti ti-file-type-pdf text-danger me-1"></i>Export as PDF</a>
														</li>
														<li>
															<a href="javascript:void(0);" class="dropdown-item export-btn" data-format="excel" data-bs-toggle="modal" data-bs-target="#export_receipts_modal"><i class="ti ti-file-type-xls text-green me-1"></i>Export as Excel </a>
														</li>
													</ul>
												</div>
											</div>
											<a href="javascript:void(0);" class="btn btn-primary btn-add-receipt" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i class="ti ti-square-rounded-plus me-2"></i>Create Receipt</a>
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
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-column="5" data-dir="asc">
															<i class="ti ti-circle-chevron-right me-1"></i>Ascending (Date)
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-column="5" data-dir="desc">
															<i class="ti ti-circle-chevron-right me-1"></i>Descending (Date)
														</a>
													</li>
													<li>
														<a href="javascript:void(0);" class="dropdown-item sort-item" data-column="0" data-dir="desc">
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
										<div class="me-2">
											<a href="javascript:void(0);" class="btn bg-soft-purple text-purple" data-bs-toggle="modal" data-bs-target="#manageColumnsModal"><i class="ti ti-columns-3 me-2"></i>Manage Columns</a>
										</div>
										<div class="form-sorts dropdown">
											<a href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-filter-share"></i>Filter</a>
											<div class="filter-dropdown-menu dropdown-menu  dropdown-menu-md-end p-4">
												<div class="filter-set-view">
													<div class="filter-set-head">
														<h4><i class="ti ti-filter-share"></i>Filter</h4>
													</div>
													<div class="row custom-filter-row">
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Date Range</label>
                                                            <div class="input-group">
                                                                <input type="date" class="form-control" id="filter_date_from">
                                                                <span class="input-group-text">to</span>
                                                                <input type="date" class="form-control" id="filter_date_to">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Lead/Customer Name</label>
                                                            <input type="text" class="form-control" id="filter_lead_name" placeholder="Search by lead name">
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Company</label>
                                                            <select class="form-select" id="filter_company">
                                                                <option value="">All Companies</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Payment Mode</label>
                                                             <select class="form-select" id="filter_payment_mode">
                                                                <option value="">All Modes</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    
													<div class="filter-reset-btns">
														<div class="row">
															<div class="col-6">
																<a href="javascript:void(0);" class="btn btn-light" id="btn-reset">Reset</a>
															</div>
															<div class="col-6">
																<a href="javascript:void(0);" class="btn btn-primary" id="btn-filter">Filter</a>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- /Filter -->

								<!-- Projects List -->
								<div class="table-responsive custom-table">
									<table class="table" id="receipts-table">
										<thead class="thead-light">
											<tr>
												<th class="no-sort">
													<label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
												</th>
												<th>Sr. No.</th>
												<th>Invoice No.</th>
												<th>Lead Name</th>
												<th>Company</th>
												<th>Receipt Date</th>
												<th>Amount</th>
												<th>Payment Mode</th>
												<th>Bank</th>
												<th>Reference</th>
												<th class="text-end">Action</th>
											</tr>
										</thead>
										<tbody>
										<tbody>
                                            <!-- DataTables will populate this -->
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
								<!-- /Projects List -->

							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Add New Receipt -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h4>Create New Receipt</h4>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="{{ route('accounts.receipts.store', ['box_slug' => request()->route('box_slug') ?? 'default']) }}" method="POST" id="add_receipt_form">
                    @csrf
					<div>
						<div class="row">
						
						    <div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Lead <span class="text-danger">*</span></label>
									<select class="select" id="lead_select" name="lead_id">
										<option selected disabled>-- Choose --</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Company <span class="text-danger">*</span></label>
									<select class="select" id="company_select" name="company_id">
										<option value="" disabled {{ !isset($company) ? 'selected' : '' }}>-- Choose --</option>
                                        @if(isset($company))
                                            <option value="{{ $company->id }}" selected>{{ $company->bill_name ?? $company->name }}</option>
                                        @endif
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Receipt Date<span class="text-danger"> *</span></label>
									<input type="text" class="MyDatePicker form-control" placeholder="MM/DD/YYYY" readonly name="receipt_date">
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
									<input class="form-control" type="text" name="amount">
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Payment Mode <span class="text-danger">*</span></label>
									<select class="select" name="payment_mode" id="payment_mode_select">
										<option selected disabled>-- Choose --</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Bank <span class="text-danger">*</span></label>
									<select class="select" id="bank_select" name="bank_id">
										<option selected disabled>-- Choose --</option>
									</select>
								</div>
							</div>
                            <div class="col-md-6">
								<div class="mb-3">
									<label class="col-form-label">Division <span class="text-danger">*</span></label>
									<select class="select" id="division_select" name="division_id">
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
					<div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary">Submit</button>
					</div>
				</form>
			</div>
		</div>
		<!-- /Add New Receipt -->



		<!-- Delete receipt -->
		<div class="modal fade" id="delete_invoices" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Receipt?</h4>
							<p class="mb-0">Are you sure you want to remove <br> receipt you selected.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="receipt.html" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Invoices -->

		<!-- Add New View -->
		<div class="modal custom-modal fade" id="add_deal" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form action="invoices.html">
							<div class="mb-3">
								<label class="col-form-label">Name</label>
								<input type="text" class="form-control" placeholder="Enter Name">
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
        
        <!-- Export Modal -->
		<div class="modal custom-modal fade" id="export_receipts_modal" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Export Receipts</h5>
						<button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<form id="export_receipts_form">
                            <div class="mb-3">
                                <label class="form-label">Export Format</label>
                                <div class="d-flex align-items-center">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="export_format" id="fmt_pdf" value="pdf">
                                        <label class="form-check-label" for="fmt_pdf">PDF</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="export_format" id="fmt_excel" value="excel" checked>
                                        <label class="form-check-label" for="fmt_excel">Excel</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date Range</label>
                                <div class="icon-form">
                                    <span class="form-icon"><i class="ti ti-calendar"></i></span>
                                    <input type="text" class="form-control" id="export_date_range" placeholder="Select Date Range">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Start Export</button>
                            </div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Export Modal -->


        <!-- Manage Columns Modal -->
        <div class="modal fade" id="manageColumnsModal" tabindex="-1" aria-labelledby="manageColumnsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title" id="manageColumnsModalLabel">Manage Columns</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <p class="text-muted mb-0">Select columns to display in the table.</p>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="showAllColumns">Show All</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="hideAllColumns">Hide All</button>
                            </div>
                        </div>
                        <div class="list-group border-0" id="columnsList">
                            <!-- Columns will be populated dynamically -->
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="applyReceiptColumnChanges">Apply Changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Container -->
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div id="leadToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="toastMessage">
                        <!-- Toast Message -->
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <!-- Progress Toast for Export -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="exportProgressToast" class="toast border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
                <div class="toast-header bg-primary text-white">
                    <i class="ti ti-download me-2"></i>
                    <strong class="me-auto">Export Progress</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <p id="export-status-text" class="mb-2">Preparing your file...</p>
                    <div class="progress" style="height: 10px;">
                        <div id="export-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>

@endsection

@push('scripts')
<script>
    // Debounce Helper
    function debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };

    // Toast Function
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

    // Column Definitions for Manage Columns feature
    const columnDefinitions = [
        { index: 0, name: 'Checkbox', visible: true },
        { index: 1, name: 'SL', visible: true },
        { index: 2, name: 'Invoice No', visible: true },
        { index: 3, name: 'Lead Name', visible: true },
        { index: 4, name: 'Company', visible: true },
        { index: 5, name: 'Date', visible: true },
        { index: 6, name: 'Amount', visible: true },
        { index: 7, name: 'Payment Mode', visible: true },
        { index: 8, name: 'Bank', visible: true },
        { index: 9, name: 'Reference', visible: true },
        { index: 10, name: 'Action', visible: true }
    ];

    // Page identifier for column preferences
    const pageIdentifier = 'receipts-list';

    // Load column visibility preferences from database
    function loadColumnPreferences(callback) {
        $.ajax({
            url: "{{ route('settings.column.preferences.get', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
            type: 'GET',
            data: {
                page_identifier: pageIdentifier
            },
            async: false,
            success: function(response) {
                if (response.success && response.data && Array.isArray(response.data)) {
                    if (response.data.length > 0) {
                        try {
                            const preferences = response.data;
                            preferences.forEach(pref => {
                                const prefIndex = parseInt(pref.index);
                                const col = columnDefinitions.find(c => c.index === prefIndex);
                                if (col) {
                                    if (typeof pref.visible === 'string') {
                                        col.visible = pref.visible === 'true' || pref.visible === '1';
                                    } else {
                                        col.visible = pref.visible !== undefined ? Boolean(pref.visible) : true;
                                    }
                                }
                            });
                        } catch (e) {
                            console.error('Error parsing column preferences:', e);
                            columnDefinitions.forEach(col => col.visible = true);
                        }
                    } else {
                        // Empty array, use defaults
                        columnDefinitions.forEach(col => col.visible = true);
                    }
                    if (callback) callback();
                } else {
                    // response.data is null or not an array, use defaults
                    columnDefinitions.forEach(col => col.visible = true);
                    if (callback) callback();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading column preferences:', error);
                columnDefinitions.forEach(col => col.visible = true);
                if (callback) callback();
            }
        });
    }

    // AJAX Setup for CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Save column preferences to database
    function saveColumnPreferences() {
        // Try to get box_slug from URL if possible, fallback to Blade
        var pathParts = window.location.pathname.split('/');
        var boxSlugFromUrl = pathParts[1] || 'default';
        var saveUrl = "{{ route('settings.column.preferences.save', ['box_slug' => ':box_slug']) }}".replace(':box_slug', boxSlugFromUrl);
        
        console.log('--- Saving Preferences ---');
        console.log('Page:', pageIdentifier);
        console.log('URL:', saveUrl);
        console.log('Payload:', columnDefinitions);

        $.ajax({
            url: saveUrl,
            type: 'POST',
            data: {
                page_identifier: pageIdentifier,
                column_preferences: columnDefinitions
            },
            success: function(response) {
                console.log('Save Success:', response);
                if (response.success) {
                    showToast('Settings saved successfully.', 'success');
                } else {
                    showToast('Save failed: ' + (response.message || 'Unknown error'), 'warning');
                }
            },
            error: function(xhr, status, error) {
                console.error('Save Error Details:');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response Text:', xhr.responseText);
                
                if (xhr.status === 419) {
                    showToast('Session expired. Please refresh the page.', 'danger');
                } else {
                    showToast('Failed to save settings (Status ' + xhr.status + ').', 'danger');
                }
            }
        });
    }



    // Populate columns modal
    function populateColumnsModal() {
        const columnsList = $('#columnsList');
        columnsList.empty();

        columnDefinitions.forEach(col => {
            // Don't allow hiding Checkbox, SL, or Action for base functionality if desired, 
            // but we'll follow the leave-type model which allows it.
            const listItem = $(`
                <div class="list-group-item border-0 px-0 py-2">
                    <div class="form-check form-switch">
                        <input class="form-check-input column-toggle" type="checkbox"
                               data-column-index="${col.index}"
                               id="col_${col.index}"
                               ${col.visible ? 'checked' : ''}>
                        <label class="form-check-label fw-medium" for="col_${col.index}">
                            ${col.name}
                        </label>
                    </div>
                </div>
            `);
            columnsList.append(listItem);
        });
    }

    $(document).ready(function() {
        
        // Initialize Select2 for all select elements
        $('.select').select2({
            dropdownParent: $('#offcanvas_add'), // Ensure dropdown works inside offcanvas
            width: '100%'
        });

        // Clear default date range to show ALL receipts by default
        $('.bookingrange').val('');
        $('.bookingrange').attr('placeholder', 'Select Date Range');

        // Re-initialize Select2 when offcanvas opens to ensure correct rendering
        $('#offcanvas_add').on('shown.bs.offcanvas', function () {
             $('.select').select2({
                dropdownParent: $('#offcanvas_add'),
                width: '100%'
            });
        });


        // Fetch Initial Data (Leads and Companies)
        $.ajax({
            url: "{{ route('accounts.receipts.form-data', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
            type: "GET",
            data: { fetch_initial: 1 },
            success: function(response) {
                if(response.leads) {
                    var leadSelect = $('#lead_select');
                    response.leads.forEach(function(lead) {
                        // Format: [#ID] Name - Email
                        var displayText = `[#${lead.id}] ${lead.name} ${lead.last_name || ''} - ${lead.email || ''}`;
                        leadSelect.append(`<option value="${lead.id}" data-company-name="${lead.company_name || ''}">${displayText}</option>`);
                    });
                    // Trigger change to update Select2
                     leadSelect.trigger('change.select2'); 
                }
                if(response.companies) {
                    var companySelect = $('#company_select');
                    var filterCompanySelect = $('#filter_company');
                    response.companies.forEach(function(company) {
                        companySelect.append(`<option value="${company.id}">${company.bill_name}</option>`);
                        filterCompanySelect.append(`<option value="${company.id}">${company.bill_name}</option>`);
                    });
                     companySelect.trigger('change.select2');
                }
                if(response.payment_modes) {
                    var modeSelect = $('#payment_mode_select');
                    var filterModeSelect = $('#filter_payment_mode');
                    response.payment_modes.forEach(function(mode) {
                        modeSelect.append(`<option value="${mode.name}">${mode.name}</option>`);
                        filterModeSelect.append(`<option value="${mode.name}">${mode.name}</option>`);
                    });
                    modeSelect.trigger('change.select2');
                }
            },

            error: function(xhr) {
                showToast('Error fetching initial data', 'danger');
            }
        });

        // Handle Lead Selection
        $('#lead_select').on('select2:select change', function() {
            var selectedOption = $(this).find('option:selected');
            var companyName = selectedOption.data('company-name');
            
            if (companyName) {
                var found = false;
                $('#company_select option').each(function() {
                     if ($(this).text().trim().toLowerCase() === companyName.trim().toLowerCase()) {
                         // Update Select2 value
                         $('#company_select').val($(this).val()).trigger('change');
                         found = true;
                         return false; 
                     }
                });
            }
        });

        // Handle Company Selection
        $('#company_select').on('select2:select change', function() {
            var companyId = $(this).val();
            
            if(!companyId) return;

            // Fetch Dependent Data (Banks and Divisions)
            $.ajax({
                url: "{{ route('accounts.receipts.form-data', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
                type: "GET",
                data: { company_id: companyId },
                success: function(response) {
                    var bankSelect = $('#bank_select');
                    bankSelect.empty();
                    bankSelect.append('<option selected disabled>-- Choose --</option>');
                    if(response.banks) {
                        response.banks.forEach(function(bank) {
                            bankSelect.append(`<option value="${bank.id}">${bank.name}</option>`);
                        });
                    }
                    bankSelect.trigger('change'); // Update Select2

                    var divisionSelect = $('#division_select');
                    divisionSelect.empty();
                    divisionSelect.append('<option selected disabled>-- Choose --</option>');
                    if(response.divisions) {
                        response.divisions.forEach(function(div) {
                            divisionSelect.append(`<option value="${div.id}">${div.name}</option>`);
                        });
                    }
                    divisionSelect.trigger('change'); // Update Select2
                },
                error: function(xhr) {
                    showToast('Error fetching dependent data', 'danger'); 
                }
            });
        });

        // Initialize DataTable
        var table;
        loadColumnPreferences(function() {
            table = $('#receipts-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('accounts.receipts', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
                    data: function (d) {
                        d.search_global = $('#search_global').val();
                        d.start_date = $('#filter_date_from').val();
                        d.end_date = $('#filter_date_to').val();
                        d.lead_name = $('#filter_lead_name').val();
                        d.company_id = $('#filter_company').val() || '{{ $company_id ?? "" }}';
                        d.payment_mode = $('#filter_payment_mode').val();
                        d.bookingrange = $('.bookingrange').val();
                    }
                },
                order: [[5, 'desc']],
                columns: [
                    { data: 'id', name: 'id', render: function(data, type, row) {
                        return '<label class="checkboxs"><input type="checkbox" value="' + data + '"><span class="checkmarks"></span></label>';
                    }, searchable: false, visible: columnDefinitions[0].visible },
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, visible: columnDefinitions[1].visible },
                    { data: 'invoice_number', name: 'invoice_number', visible: columnDefinitions[2].visible },
                    { data: 'lead.name', name: 'lead.name', defaultContent: '-', visible: columnDefinitions[3].visible, render: function(data, type, row) {
                        if(data && row.lead && row.lead.id) {
                            var fullName = data + (row.lead.last_name ? ' ' + row.lead.last_name : '');
                            return '<a href="{{ route("LeadsDetailsPage", ["box_slug" => request()->route("box_slug") ?? "default"]) }}?lead_id=' + row.lead.id + '" class="text-primary fw-bold text-decoration-underline">' + fullName + '</a>';
                        }
                        return data || '-';
                    } },
                    { data: 'company_name', name: 'company.bill_name', visible: columnDefinitions[4].visible },
                    { data: 'receipt_date', name: 'receipt_date', visible: columnDefinitions[5].visible },
                    { data: 'amount', name: 'amount', visible: columnDefinitions[6].visible },
                    { data: 'payment_mode', name: 'payment_mode', visible: columnDefinitions[7].visible },
                    { data: 'bank_name', name: 'bank.name', visible: columnDefinitions[8].visible },
                    { data: 'reference', name: 'reference', visible: columnDefinitions[9].visible },
                    { data: 'action', name: 'action', orderable: false, searchable: false, visible: columnDefinitions[10].visible }
                ],
                drawCallback: function(settings) {
                    var api = this.api();
                    var json = api.ajax.json();
                    
                    $('#receipts_total_amount_display').remove();
                    if(json && json.total_amount !== undefined) {
                        var totalFormatted = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(json.total_amount);
                        var totalHtml = '<div id="receipts_total_amount_display" class="alert alert-info mt-3 fw-bold d-flex justify-content-between">' +
                                        '<span>Total Amount:</span><span>' + totalFormatted + '</span></div>';
                        $(api.table().container()).append(totalHtml);
                    }
                }
            });
        });

        


        // Handle Add Receipt Button Click (Reset form)
        $('.btn-add-receipt').click(function() {
             $('#add_receipt_form')[0].reset();
             $('.text-danger').remove();
             $('#offcanvas_add .offcanvas-header h4').text('Create New Receipt');
             $('#add_receipt_form').attr('action', "{{ route('accounts.receipts.store', ['box_slug' => request()->route('box_slug') ?? 'default']) }}");
             $('input[name="_method"]').remove();
             $('.select').val(null).trigger('change');
        });

        
        // Handle Edit Receipt
        // Handle Edit Receipt
        $(document).on('click', '.edit-receipt', function() {
            var receiptId = $(this).data('id');
            var url = "{{ route('accounts.receipts.show', ['box_slug' => request()->route('box_slug') ?? 'default']) }}";
            var updateUrl = "{{ route('accounts.receipts.update', ['box_slug' => request()->route('box_slug') ?? 'default']) }}";
            
            var offcanvasElement = document.getElementById('offcanvas_add');
            var bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasElement);
            bsOffcanvas.show();

            // Reset Form first
            $('#add_receipt_form')[0].reset();
            $('.text-danger').remove();
            $('#offcanvas_add .offcanvas-header h4').text('Edit Receipt');
            $('#add_receipt_form').attr('action', updateUrl);
            
            // For update, we need to pass the ID. Since it's a POST request (simulate PUT/Update logic but actually just POST endpoint handling ID), 
            // we will add a hidden input for ID.
            if ($('input[name="id"]').length === 0) {
                $('#add_receipt_form').append('<input type="hidden" name="id" value="' + receiptId + '">');
            } else {
                $('input[name="id"]').val(receiptId);
            }
            
            // Remove _method PUT because we are using standard POST to a route that handles update logic based on ID in body
            $('input[name="_method"]').remove(); 

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    id: receiptId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        // Populate fields
                        $('input[name="receipt_date"]').val(moment(data.receipt_date).format('MM/DD/YYYY')); 
                        $('input[name="invoice_number"]').val(data.invoice_number);
                        $('input[name="amount"]').val(data.amount);
                        $('select[name="payment_mode"]').val(data.payment_mode).trigger('change');
                        $('input[name="reference"]').val(data.reference);

                    // Set Lead
                    if(data.lead_id) {
                         $('#lead_select').val(data.lead_id).trigger('change.select2');
                    }
                    
                    // Set Company
                    if(data.company_id) {
                        $('#company_select').val(data.company_id).trigger('change.select2');
                        
                         $.ajax({
                            url: "{{ route('accounts.receipts.form-data', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
                            type: "GET",
                            data: { company_id: data.company_id },
                            success: function(response) {
                                var bankSelect = $('#bank_select');
                                bankSelect.empty();
                                bankSelect.append('<option selected disabled>-- Choose --</option>');
                                if(response.banks) {
                                    response.banks.forEach(function(bank) {
                                        var selected = (bank.id == data.bank_id) ? 'selected' : '';
                                        bankSelect.append(`<option value="${bank.id}" ${selected}>${bank.name}</option>`);
                                    });
                                }
                                bankSelect.trigger('change.select2');

                                var divisionSelect = $('#division_select');
                                divisionSelect.empty();
                                divisionSelect.append('<option selected disabled>-- Choose --</option>');
                                if(response.divisions) {
                                    response.divisions.forEach(function(div) {
                                        var selected = (div.id == data.division_id) ? 'selected' : '';
                                        divisionSelect.append(`<option value="${div.id}" ${selected}>${div.name}</option>`);
                                    });
                                }
                                divisionSelect.trigger('change.select2');
                            }
                        });
                    }
                    }
                }
            });
        });

        // Handle Add Receipt Button Click (Reset form)
        $('.btn-add-receipt').click(function() {
             $('#add_receipt_form')[0].reset();
             $('.text-danger').remove();
             $('#offcanvas_add .offcanvas-header h4').text('Create New Receipt');
             $('#add_receipt_form').attr('action', "{{ route('accounts.receipts.store', ['box_slug' => request()->route('box_slug') ?? 'default']) }}");
             $('input[name="_method"]').remove();
             $('input[name="id"]').remove();
             $('.select').val(null).trigger('change');
        });

        // AJAX Form Submission
        $('#add_receipt_form').submit(function(e) {
            e.preventDefault();
            
            // Basic Clear Errors
            $('.text-danger').remove();

            var formData = $(this).serialize();

            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        var offcanvasElement = document.getElementById('offcanvas_add');
                        var bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasElement);
                        bsOffcanvas.hide();
                        $('#add_receipt_form')[0].reset();
                        // Reset Select2s
                        $('#add_receipt_form .select').val(null).trigger('change');
                        
                        table.ajax.reload();
                        
                        // Show success message (using Toast or Alert)
                        showToast(response.message, 'success');  
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            var input = $('[name="' + key + '"]');
                            input.after('<div class="text-danger">' + value[0] + '</div>');
                        });
                    } else {
                        showToast('Something went wrong. Please try again.', 'danger');
                    }
                }
            });
        });

        // Search & Filter Triggers (Debounced search, instant selection change)
        const debouncedDraw = debounce(function() {
            if (table) table.draw();
        }, 500);

        $('#search_global, #filter_lead_name').on('keyup', debouncedDraw);
        
        $('#filter_date_from, #filter_date_to, #filter_company, #filter_payment_mode').on('change', function() {
            if (table) table.draw();
        });

        $('.bookingrange').on('change apply.daterangepicker', function() {
            if (table) table.draw();
        });

        // Sort items click
        $(document).on('click', '.sort-item', function() {
            var col = $(this).data('column');
            var dir = $(this).data('dir');
            if (table) table.order([col, dir]).draw();
        });

        // Filter Button (optional if change is instant, but good to keep)
        $('#btn-filter').click(function() {
            if (table) table.draw();
        });

        // Reset Button
        $('#btn-reset').click(function() {
            $('#search_global').val('');
            $('#filter_date_from').val('');
            $('#filter_date_to').val('');
            $('#filter_lead_name').val('');
            $('#filter_company').val('');
            $('#filter_payment_mode').val('');
            $('.bookingrange').val('');
            if (table) table.order([5, 'desc']).draw(); // Reset to default sort (Date Desc)
        });

        // Manage Columns Modal Listeners
        $('#manageColumnsModal').on('show.bs.modal', function() {
            populateColumnsModal();
        });

        $('#showAllColumns').on('click', function() {
            $('.column-toggle').prop('checked', true);
        });

        $('#hideAllColumns').on('click', function() {
            $('.column-toggle').prop('checked', false);
        });

        $('#applyReceiptColumnChanges').on('click', function() {
            $('.column-toggle').each(function() {
                const columnIndex = parseInt($(this).data('column-index'));
                const isVisible = $(this).is(':checked');
                const col = columnDefinitions.find(c => c.index === columnIndex);
                if (col) {
                    col.visible = isVisible;
                }
            });

            saveColumnPreferences();

            if (table) {
                columnDefinitions.forEach(col => {
                    try {
                        table.column(col.index).visible(col.visible, false);
                    } catch (e) {
                        console.error('Error applying column visibility:', e);
                    }
                });
                table.columns.adjust().draw(false);
            }
            $('#manageColumnsModal').modal('hide');
        });

        // Initialize Daterangepicker for Export Modal
        if ($('#export_date_range').length > 0) {
            $('#export_date_range').daterangepicker({
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });
        }

        // Export Modal Handling
        $(document).on('click', '.export-btn', function() {
            var format = $(this).data('format');
            if (format === 'pdf') {
                $('#fmt_pdf').prop('checked', true);
            } else {
                $('#fmt_excel').prop('checked', true);
            }
            
            // Sync with global bookingrange if it has a value
            var currentGlobalRange = $('.bookingrange').val();
            if (currentGlobalRange) {
                $('#export_date_range').val(currentGlobalRange);
            }
        });

        $('#export_receipts_form').on('submit', function(e) {
            e.preventDefault();
            
            var format = $('input[name="export_format"]:checked').val();
            var bookingRange = $('#export_date_range').val();
            
            var filters = {
                format: format,
                search_global: $('#search_global').val(),
                start_date: $('#filter_date_from').val(),
                end_date: $('#filter_date_to').val(),
                bookingrange: bookingRange,
                lead_name: $('#filter_lead_name').val(),
                company_id: $('#filter_company').val(),
                payment_mode: $('#filter_payment_mode').val(),
                _token: "{{ csrf_token() }}"
            };

            $.ajax({
                url: "{{ route('accounts.receipts.export', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
                type: "POST",
                data: filters,
                beforeSend: function() {
                    $('#export_receipts_modal').modal('hide');
                    showToast('Exporting data, please wait...', 'info');
                },
                success: function(response) {
                    if (response.success && response.file_name) {
                        showToast(response.message, 'success');
                        pollExportStatus(response.file_name);
                    }
                },
                error: function() {
                    showToast('Failed to start export process.', 'danger');
                }
            });
        });

        function pollExportStatus(fileName) {
            var progress = 0;
            const progressToastEl = document.getElementById('exportProgressToast');
            const bsProgressToast = new bootstrap.Toast(progressToastEl);
            const progressBar = $('#export-progress-bar');
            const statusText = $('#export-status-text');
            
            progressBar.css('width', '0%').attr('aria-valuenow', 0);
            statusText.text('Our system is generating your export...');
            bsProgressToast.show();

            var pollInterval = setInterval(function() {
                // Simulate progressive loading for visual feel
                if (progress < 90) {
                    progress += Math.floor(Math.random() * 10) + 5;
                    if (progress > 90) progress = 90;
                    progressBar.css('width', progress + '%').attr('aria-valuenow', progress);
                }

                $.ajax({
                    url: "{{ route('accounts.receipts.export.status', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
                    type: "GET",
                    data: { file_name: fileName },
                    success: function(statusResponse) {
                        if (statusResponse.ready) {
                            clearInterval(pollInterval);
                            progress = 100;
                            progressBar.css('width', '100%').attr('aria-valuenow', 100).removeClass('progress-bar-animated');
                            statusText.text('Export complete! Starting download...');
                            
                            setTimeout(function() {
                                window.location.href = "{{ route('accounts.receipts.export.download', ['box_slug' => request()->route('box_slug') ?? 'default']) }}?file_name=" + fileName;
                                setTimeout(() => bsProgressToast.hide(), 2000);
                            }, 1000);
                        }
                    },
                    error: function() {
                        clearInterval(pollInterval);
                        bsProgressToast.hide();
                        showToast('Failed to track export progress.', 'danger');
                    }
                });
            }, 2500); // Check every 2.5 seconds
        }


    });
</script>
@endpush