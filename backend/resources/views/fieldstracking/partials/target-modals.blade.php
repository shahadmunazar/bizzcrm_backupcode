@php
    $currentBoxSlug = $boxSlug ?? request()->route('box_slug') ?? request()->segment(1) ?? 'medsyn-lab-biotech';
@endphp
		<!-- Create target template -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Create Target Template</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="{{ route('fieldstracking.target-template.store', ['box_slug' => $currentBoxSlug]) }}" method="POST" id="createTargetForm">
                    @csrf
					
                    <!-- Section 1: Basic Info -->
                    <h6 class="mb-3">1. Basic Information</h6>
                    <div class="row mb-4">
                        <div class="col-md-12 mb-3">
                            <label class="col-form-label">Template Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. Standard Sales Template">
                        </div>
                    </div>

                    <!-- Section 2: Target Period -->
                    <h6 class="mb-3">2. Target Period</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="col-form-label">Frequency <span class="text-danger">*</span></label>
                            <select name="frequency" class="select" id="target_frequency" required>
                                <option value="Monthly">Monthly</option>
                                <option value="Quarterly">Quarterly</option>
                                <option value="Yearly">Yearly</option>
                                <option value="Custom">Custom</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="date_range_group">
                            <div class="row">
                                <div class="col-6">
                                    <label class="col-form-label">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control">
                                </div>
                                <div class="col-6">
                                    <label class="col-form-label">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Calculation Method -->
                    <h6 class="mb-3">3. Target Calculation</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="col-form-label">Calculation Method <span class="text-danger">*</span></label>
                            <select name="calculation_method" class="select" id="calculation_method" required>
                                <option value="Fixed Target">Fixed Target</option>
                                <option value="Salary Based">Salary Based</option>
                                <option value="Previous Performance Based">Previous Performance Based</option>
                                <option value="Hybrid">Hybrid</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="salary_multiplier_group" style="display:none;">
                            <label class="col-form-label">Salary Multiplier <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="salary_multiplier" id="salary_multiplier_input" class="form-control" placeholder="e.g. 20">
                            <small class="text-muted">Target = Employee Monthly Salary × Multiplier</small>
                        </div>
                    </div>

                    <!-- Section 4 & 5: Metrics & Weights -->
                    <h6 class="mb-3">4. Target Metrics & Weights</h6>
                    <div class="card mb-4">
                        <div class="card-body" id="metrics_container">
                            <div class="row mb-2">
                                <div class="col-4 fw-bold">Metric Name</div>
                                <div class="col-3 fw-bold">Target Value</div>
                                <div class="col-3 fw-bold">Weight %</div>
                                <div class="col-2"></div>
                            </div>
                            
                            <!-- Initial Metric Row -->
                            <div class="row mb-3 metric-row">
                                <div class="col-4">
                                    <input type="hidden" name="metrics[0][type]" value="Currency">
                                    <select name="metrics[0][name]" class="form-control" required>
                                        <option value="Sales Amount">Sales Amount</option>
                                        <option value="Payment Collection">Payment Collection</option>
                                        <option value="New Lead">New Lead</option>
                                        <option value="New Customer">New Customer</option>
                                        <option value="Customer Visit">Customer Visit</option>
                                        <option value="No of Matured">No of Matured</option>
                                        <option value="No of task Closed manually">No of task Closed manually</option>
                                        <option value="Amount of Matured">Amount of Matured</option>
                                        <option value="No of Call Outgoing + Incoming">No of Call Outgoing + Incoming</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <input type="number" name="metrics[0][target_value]" class="form-control target-val-input" placeholder="Value">
                                </div>
                                <div class="col-3">
                                    <input type="number" name="metrics[0][weight]" class="form-control weight-input" placeholder="%" max="100" required>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-metric"><i class="ti ti-trash"></i></button>
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add_metric_btn">+ Add Another Metric</button>
                            <div class="mt-3 text-end fw-bold">
                                Total Weight: <span id="total_weight">0</span>%
                                <div id="weight_error" class="text-danger" style="display:none; font-size: 0.85em;">Total weight must equal 100%</div>
                            </div>
                        </div>
                    </div>

			        <div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary" id="saveTargetBtn">Create Template</button>
					</div>
				</form>		
			</div>
		</div>

		<!-- Assign Target Template -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_assign">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Assign Target to Employees</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="{{ route('fieldstracking.target-assignment.store', ['box_slug' => $currentBoxSlug]) }}" method="POST" id="assignTargetForm">
                    @csrf
					<div class="mb-3">
						<label class="col-form-label">Select Target Template <span class="text-danger">*</span></label>
						<select name="target_template_id" class="select" id="assign_target_template" required>
							<option value="">Choose Template</option>
                            @if(isset($templates))
                                @foreach($templates as $tpl)
                                    <option value="{{ $tpl->id }}">{{ $tpl->name }} ({{ $tpl->frequency }})</option>
                                @endforeach
                            @endif
						</select>
					</div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="col-form-label">Target Type <span class="text-danger">*</span></label>
                            <select name="target_type" class="select" id="assign_target_type" required>
                                <option value="Employee Target">Individual Employee(s)</option>
                                <option value="All Employees">All Employees (Entire Company / Box)</option>
                                <option value="Department Target">Department Target</option>
                                <option value="Team Target">Team Target</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3" id="assign_department_group" style="display:none;">
                            <label class="col-form-label">Select Department <span class="text-danger">*</span></label>
                            <select name="department_id" class="select" id="assign_department_select">
                                <option value="">Select Department</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3" id="assign_team_group" style="display:none;">
						<label class="col-form-label">Select Teams <span class="text-danger">*</span></label>
						<select name="team_ids[]" class="select" id="assign_team_select" multiple>
						</select>
					</div>

					<div class="mb-3" id="assign_employee_group" style="display:none;">
						<div class="d-flex align-items-center justify-content-between mb-1">
							<label class="col-form-label mb-0">Select Employees <span class="text-danger">*</span></label>
							<div>
								<button type="button" class="btn btn-xs btn-outline-primary py-0 px-2" id="btn_select_all_employees" style="font-size:11px;">Select All</button>
								<button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 ms-1" id="btn_deselect_all_employees" style="font-size:11px;">Clear</button>
							</div>
						</div>
						<select name="employee_ids[]" class="select" id="assign_employee_select" multiple>
						</select>
					</div>

                    <div class="alert alert-info py-2 px-3 mb-3" id="assign_all_employees_alert" style="display:none; font-size:13px;">
                        <i class="ti ti-info-circle me-1"></i> This target template will automatically be applied to <strong>all current active employees</strong> in this box.
                    </div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="col-form-label">Start Date <span class="text-danger">*</span></label>
							<input type="date" name="start_date" class="form-control" required value="{{ date('Y-m-01') }}">
						</div>
						<div class="col-md-6 mb-3">
							<label class="col-form-label">End Date <span class="text-danger">*</span></label>
							<input type="date" name="end_date" class="form-control" required value="{{ date('Y-m-t') }}">
						</div>
					</div>

					<div class="mb-4">
						<label class="col-form-label">Remarks / Instructions (Optional)</label>
						<textarea name="remarks" class="form-control" rows="2" placeholder="e.g. Q3 Sales push targets..."></textarea>
					</div>

			        <div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary">Assign Target</button>
					</div>
				</form>		
			</div>
		</div>

		<!-- Edit Target Template -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit_template">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Edit Target Template</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="" method="POST" id="editTargetForm">
                    @csrf
					
                    <!-- Section 1: Basic Info -->
                    <h6 class="mb-3">1. Basic Information</h6>
                    <div class="row mb-4">
                        <div class="col-md-12 mb-3">
                            <label class="col-form-label">Template Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_template_name" class="form-control" required placeholder="e.g. Standard Sales Template">
                        </div>
                    </div>

                    <!-- Section 2: Target Period -->
                    <h6 class="mb-3">2. Target Period</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="col-form-label">Frequency <span class="text-danger">*</span></label>
                            <select name="frequency" class="select" id="edit_target_frequency" required>
                                <option value="Monthly">Monthly</option>
                                <option value="Quarterly">Quarterly</option>
                                <option value="Yearly">Yearly</option>
                                <option value="Custom">Custom</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="edit_date_range_group">
                            <div class="row">
                                <div class="col-6">
                                    <label class="col-form-label">Start Date</label>
                                    <input type="date" name="start_date" id="edit_start_date" class="form-control">
                                </div>
                                <div class="col-6">
                                    <label class="col-form-label">End Date</label>
                                    <input type="date" name="end_date" id="edit_end_date" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Calculation Method -->
                    <h6 class="mb-3">3. Target Calculation</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="col-form-label">Calculation Method <span class="text-danger">*</span></label>
                            <select name="calculation_method" class="select" id="edit_calculation_method" required>
                                <option value="Fixed Target">Fixed Target</option>
                                <option value="Salary Based">Salary Based</option>
                                <option value="Previous Performance Based">Previous Performance Based</option>
                                <option value="Hybrid">Hybrid</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="edit_salary_multiplier_group" style="display:none;">
                            <label class="col-form-label">Salary Multiplier <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="salary_multiplier" id="edit_salary_multiplier_input" class="form-control" placeholder="e.g. 20">
                            <small class="text-muted">Target = Employee Monthly Salary × Multiplier</small>
                        </div>
                    </div>

                    <!-- Section 4 & 5: Metrics & Weights -->
                    <h6 class="mb-3">4. Target Metrics & Weights</h6>
                    <div class="card mb-4">
                        <div class="card-body" id="edit_metrics_container">
                            <div class="row mb-2">
                                <div class="col-4 fw-bold">Metric Name</div>
                                <div class="col-3 fw-bold">Target Value</div>
                                <div class="col-3 fw-bold">Weight %</div>
                                <div class="col-2"></div>
                            </div>
                            
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="edit_add_metric_btn">+ Add Another Metric</button>
                            <div class="mt-3 text-end fw-bold">
                                Total Weight: <span id="edit_total_weight">0</span>%
                                <div id="edit_weight_error" class="text-danger" style="display:none; font-size: 0.85em;">Total weight must equal 100%</div>
                            </div>
                        </div>
                    </div>

			        <div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary" id="editSaveTargetBtn">Update Template</button>
					</div>
				</form>		
			</div>
		</div>

		<!-- Delete Template -->
		<div class="modal fade" id="delete_template" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Delete Template?</h4>
							<p class="mb-0">Are you sure you want to remove <br> this template?</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="javascript:void(0);" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="javascript:void(0);" class="btn btn-danger" id="confirmDeleteTemplateBtn">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Template -->

@push('scripts')
<script>
    (function initTargetTemplate() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            setTimeout(initTargetTemplate, 50);
            return;
        }

        jQuery(function($) {
            const boxSlug = '{{ $currentBoxSlug }}';
            let startDate = '';
            let endDate = '';
            let searchQuery = '';
            let currentSort = 'recent_add';

            // Helper: Escape HTML
            function escapeHtml(text) {
                if (!text) return '';
                return $('<div>').text(text).html();
            }

            // Helper: Calculate Total Weight for Create Form
            function calculateTotalWeight() {
                let total = 0;
                $('#metrics_container .weight-input').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#total_weight').text(total);
                if (total !== 100) {
                    $('#weight_error').show();
                } else {
                    $('#weight_error').hide();
                }
            }

            function bindWeightEvents() {
                $('#metrics_container .weight-input').off('input').on('input', calculateTotalWeight);
                $('#metrics_container .remove-metric').off('click').on('click', function() {
                    $(this).closest('.metric-row').remove();
                    calculateTotalWeight();
                });
            }

            // Metric dynamic rows for Create Form
            let metricIndex = 1;
            $('#add_metric_btn').off('click').on('click', function() {
                const row = `
                    <div class="row mb-3 metric-row">
                        <div class="col-4">
                            <input type="hidden" name="metrics[${metricIndex}][type]" value="Number">
                            <select name="metrics[${metricIndex}][name]" class="form-control" required>
                                <option value="Sales Amount">Sales Amount</option>
                                <option value="Payment Collection">Payment Collection</option>
                                <option value="New Lead">New Lead</option>
                                <option value="New Customer">New Customer</option>
                                <option value="Customer Visit">Customer Visit</option>
                                <option value="No of Matured">No of Matured</option>
                                <option value="No of task Closed manually">No of task Closed manually</option>
                                <option value="Amount of Matured">Amount of Matured</option>
                                <option value="No of Call Outgoing + Incoming">No of Call Outgoing + Incoming</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <input type="number" name="metrics[${metricIndex}][target_value]" class="form-control target-val-input" placeholder="Value">
                        </div>
                        <div class="col-3">
                            <input type="number" name="metrics[${metricIndex}][weight]" class="form-control weight-input" placeholder="%" max="100" required>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-danger btn-sm remove-metric"><i class="ti ti-trash"></i></button>
                        </div>
                    </div>
                `;
                $(row).insertBefore(this);
                metricIndex++;
                bindWeightEvents();
            });

            bindWeightEvents();

            // Toggle Salary Multiplier on Calculation Method
            $('#calculation_method').on('change', function() {
                if (this.value === 'Salary Based' || this.value === 'Hybrid') {
                    $('#salary_multiplier_group').show();
                    $('#salary_multiplier_input').attr('required', 'required');
                } else {
                    $('#salary_multiplier_group').hide();
                    $('#salary_multiplier_input').removeAttr('required');
                }
            });

            // Helper: Reset Create Form
            function resetCreateForm() {
                $('#createTargetForm')[0].reset();
                $('#metrics_container .metric-row:not(:first)').remove();
                let firstRow = $('#metrics_container .metric-row:first');
                firstRow.find('input.target-val-input').val('');
                firstRow.find('input.weight-input').val('');
                firstRow.find('select').prop('selectedIndex', 0);
                $('#salary_multiplier_group').hide();
                $('#salary_multiplier_input').removeAttr('required');
                calculateTotalWeight();
            }

            // 1. AJAX CREATE TEMPLATE (Without Page Reload)
            $('#createTargetForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let url = form.attr('action');
                let submitBtn = form.find('button[type="submit"]');
                submitBtn.prop('disabled', true).text('Creating...');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || 'Target Template created successfully.');
                            }
                            var offcanvasEl = document.getElementById('offcanvas_add');
                            var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
                            offcanvas.hide();
                            resetCreateForm();
                            loadTemplates();
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message || 'Failed to create template.');
                            }
                        }
                    },
                    error: function(xhr) {
                        let msg = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        if (typeof toastr !== 'undefined') {
                            toastr.error(msg);
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text('Create Template');
                    }
                });
            });

            // 2. AJAX LOAD TEMPLATES (Table + Dropdown)
            window.loadTemplates = function(callback) {
                let url = '/' + boxSlug + '/fieldstracking/target-api/templates';
                let tbody = $('#target_templates_tbody');
                let params = [];

                if (startDate && endDate) {
                    params.push('start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate));
                }
                if (searchQuery) {
                    params.push('search=' + encodeURIComponent(searchQuery));
                }
                if (currentSort) {
                    params.push('sort=' + encodeURIComponent(currentSort));
                }

                let frequencies = $('.filter-freq-check:checked').map(function() { return $(this).val(); }).get();
                let methods = $('.filter-method-check:checked').map(function() { return $(this).val(); }).get();
                let statuses = $('.filter-status-check:checked').map(function() { return $(this).val(); }).get();

                frequencies.forEach(f => params.push('frequency[]=' + encodeURIComponent(f)));
                methods.forEach(m => params.push('calculation_method[]=' + encodeURIComponent(m)));
                statuses.forEach(s => params.push('status[]=' + encodeURIComponent(s)));

                if (params.length > 0) {
                    url += '?' + params.join('&');
                }

                if (tbody.length > 0) {
                    tbody.css('opacity', '0.5');
                }

                $.get(url, function(data) {
                    // Populate Assign Target Dropdown
                    let assignTargetTemplate = $('#assign_target_template');
                    if (assignTargetTemplate.length > 0) {
                        let currentVal = assignTargetTemplate.val();
                        assignTargetTemplate.empty().append('<option value="">Choose Template</option>');
                        data.forEach(tpl => {
                            assignTargetTemplate.append(`<option value="${tpl.id}">${escapeHtml(tpl.name)} (${escapeHtml(tpl.frequency)})</option>`);
                        });
                        if (currentVal) assignTargetTemplate.val(currentVal);
                    }

                    // Populate Table
                    if (tbody.length > 0) {
                        renderTemplatesTable(data);
                        tbody.css('opacity', '1');
                    }

                    if (typeof callback === 'function') {
                        callback(data);
                    }
                }).fail(function() {
                    if (tbody.length > 0) tbody.css('opacity', '1');
                    if (typeof toastr !== 'undefined') toastr.error('Failed to load templates.');
                });
            };

            // Render Data to Table and Initialize DataTable
            function renderTemplatesTable(data) {
                let tbody = $('#target_templates_tbody');

                if ($.fn.DataTable.isDataTable('#target_template_table')) {
                    $('#target_template_table').DataTable().destroy();
                }
                $('.datatable-paginate').empty();
                $('.datatable-length').empty();

                tbody.empty();

                if (!data || data.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="ti ti-file-alert fs-24 d-block mb-1"></i>
                                No target templates found
                            </td>
                        </tr>
                    `);
                    return;
                }

                data.forEach(tpl => {
                    let calcMethod = escapeHtml(tpl.calculation_method || '');
                    if (tpl.calculation_method === 'Salary Based' && tpl.salary_multiplier) {
                        calcMethod += `<br><small class="text-muted">(x${escapeHtml(tpl.salary_multiplier)})</small>`;
                    }

                    let metricsDisplay = '<span class="text-muted">-</span>';
                    if (tpl.metrics && tpl.metrics.length > 0) {
                        metricsDisplay = tpl.metrics.map(m => `<span class="badge badge-pill badge-info me-1 mb-1">${escapeHtml(m.metric_name)}</span>`).join('');
                    }

                    let status = tpl.status || 'Active';
                    let isActive = (status === 'Active');
                    let statusBadge = isActive ? 'bg-success' : 'bg-danger';

                    let row = `
                        <tr data-id="${tpl.id}">
                            <td>
                                <label class="checkboxs"><input type="checkbox" class="template-checkbox" value="${tpl.id}"><span class="checkmarks"></span></label>
                            </td>
                            <td class="fw-medium text-dark">${escapeHtml(tpl.name)}</td>
                            <td>${escapeHtml(tpl.target_type || 'Generic')}</td>
                            <td>${calcMethod}</td>
                            <td>${escapeHtml(tpl.frequency || '')}</td>
                            <td>${metricsDisplay}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="status-toggle me-2">
                                        <input type="checkbox" id="tpl_status_${tpl.id}" class="check toggle-template-status" data-id="${tpl.id}" ${isActive ? 'checked' : ''}>
                                        <label for="tpl_status_${tpl.id}" class="checktoggle"></label>
                                    </div>
                                    <span class="badge badge-pill ${statusBadge} toggle-template-badge" data-id="${tpl.id}" style="cursor: pointer; user-select: none;" title="Click to toggle status">${escapeHtml(status)}</span>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="dropdown table-action">
                                    <a href="javascript:void(0);" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item toggle-status-action" href="javascript:void(0);" data-id="${tpl.id}">
                                            <i class="ti ${isActive ? 'ti-toggle-left text-warning' : 'ti-toggle-right text-success'} me-1"></i>
                                            <span>${isActive ? 'Mark Inactive' : 'Mark Active'}</span>
                                        </a>
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="editTemplate(${tpl.id})">
                                            <i class="ti ti-edit text-blue me-1"></i>Edit
                                        </a>
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="confirmDeleteTemplate(${tpl.id})">
                                            <i class="ti ti-trash text-danger me-1"></i>Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });

                initTemplateDataTable();
            }

            function initTemplateDataTable() {
                if ($.fn.DataTable.isDataTable('#target_template_table')) {
                    $('#target_template_table').DataTable().destroy();
                }
                if ($('#target_templates_tbody tr').length > 0 && !$('#target_templates_tbody tr td[colspan]').length) {
                    $('#target_template_table').DataTable({
                        "bFilter": false,
                        "bInfo": true,
                        "autoWidth": true,
                        "ordering": true,
                        "order": [],
                        "columnDefs": [
                            { "orderable": false, "targets": [0, 5, 7] }
                        ],
                        "language": {
                            search: ' ',
                            sLengthMenu: '_MENU_',
                            searchPlaceholder: "Search",
                            info: "_START_ - _END_ of _TOTAL_ templates",
                            lengthMenu: "Show _MENU_ entries",
                            paginate: {
                                next: 'Next <i class="fa fa-angle-right"></i>',
                                previous: '<i class="fa fa-angle-left"></i> Prev'
                            },
                        },
                        initComplete: (settings, json) => {
                            $('.dataTables_paginate').appendTo('.datatable-paginate');
                            $('.dataTables_length').appendTo('.datatable-length');
                        },
                    });
                }
            }

            // Initial DataTable initialization
            initTemplateDataTable();

            // 3. EDIT TEMPLATE (Without Page Reload)
            window.editTemplate = function(id) {
                let url = '/' + boxSlug + '/fieldstracking/target-api/templates/' + id;
                $.get(url, function(response) {
                    if (response.success) {
                        let tpl = response.template;
                        $('#editTargetForm').attr('action', '/' + boxSlug + '/fieldstracking/target-template/update/' + id);
                        $('#edit_template_name').val(tpl.name);
                        $('#edit_target_frequency').val(tpl.frequency).trigger('change');
                        $('#edit_start_date').val(tpl.start_date);
                        $('#edit_end_date').val(tpl.end_date);
                        $('#edit_calculation_method').val(tpl.calculation_method).trigger('change');
                        $('#edit_salary_multiplier_input').val(tpl.salary_multiplier);

                        if (tpl.calculation_method === 'Salary Based' || tpl.calculation_method === 'Hybrid') {
                            $('#edit_salary_multiplier_group').show();
                            $('#edit_salary_multiplier_input').attr('required', 'required');
                        } else {
                            $('#edit_salary_multiplier_group').hide();
                            $('#edit_salary_multiplier_input').removeAttr('required');
                        }

                        let container = $('#edit_metrics_container');
                        container.find('.metric-row').remove();

                        if (tpl.metrics && tpl.metrics.length > 0) {
                            tpl.metrics.forEach((m, index) => {
                                let row = `
                                    <div class="row mb-3 metric-row">
                                        <div class="col-4">
                                            <input type="hidden" name="metrics[${index}][type]" value="${escapeHtml(m.metric_type)}">
                                            <select name="metrics[${index}][name]" class="form-control" required>
                                                <option value="Sales Amount" ${m.metric_name === 'Sales Amount' ? 'selected' : ''}>Sales Amount</option>
                                                <option value="Payment Collection" ${m.metric_name === 'Payment Collection' ? 'selected' : ''}>Payment Collection</option>
                                                <option value="New Lead" ${m.metric_name === 'New Lead' ? 'selected' : ''}>New Lead</option>
                                                <option value="New Customer" ${m.metric_name === 'New Customer' ? 'selected' : ''}>New Customer</option>
                                                <option value="Customer Visit" ${m.metric_name === 'Customer Visit' ? 'selected' : ''}>Customer Visit</option>
                                                <option value="No of Matured" ${m.metric_name === 'No of Matured' ? 'selected' : ''}>No of Matured</option>
                                                <option value="No of task Closed manually" ${m.metric_name === 'No of task Closed manually' ? 'selected' : ''}>No of task Closed manually</option>
                                                <option value="Amount of Matured" ${m.metric_name === 'Amount of Matured' ? 'selected' : ''}>Amount of Matured</option>
                                                <option value="No of Call Outgoing + Incoming" ${m.metric_name === 'No of Call Outgoing + Incoming' ? 'selected' : ''}>No of Call Outgoing + Incoming</option>
                                            </select>
                                        </div>
                                        <div class="col-3">
                                            <input type="number" name="metrics[${index}][target_value]" class="form-control target-val-input" value="${m.target_value || 0}">
                                        </div>
                                        <div class="col-3">
                                            <input type="number" name="metrics[${index}][weight]" class="form-control weight-input" value="${m.weight || 0}" max="100" required>
                                        </div>
                                        <div class="col-2">
                                            <button type="button" class="btn btn-danger btn-sm remove-metric"><i class="ti ti-trash"></i></button>
                                        </div>
                                    </div>
                                `;
                                $(row).insertBefore('#edit_add_metric_btn');
                            });
                        }

                        bindEditWeightEvents();
                        calculateEditTotalWeight();

                        var editOffcanvasEl = document.getElementById('offcanvas_edit_template');
                        var editOffcanvas = bootstrap.Offcanvas.getInstance(editOffcanvasEl) || new bootstrap.Offcanvas(editOffcanvasEl);
                        editOffcanvas.show();
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.message || 'Error fetching template');
                        }
                    }
                }).fail(function() {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to load template data');
                    }
                });
            };

            let editMetricIndex = 100;
            $('#edit_add_metric_btn').off('click').on('click', function() {
                const row = `
                    <div class="row mb-3 metric-row">
                        <div class="col-4">
                            <input type="hidden" name="metrics[${editMetricIndex}][type]" value="Currency">
                            <select name="metrics[${editMetricIndex}][name]" class="form-control" required>
                                <option value="Sales Amount">Sales Amount</option>
                                <option value="Payment Collection">Payment Collection</option>
                                <option value="New Lead">New Lead</option>
                                <option value="New Customer">New Customer</option>
                                <option value="Customer Visit">Customer Visit</option>
                                <option value="No of Matured">No of Matured</option>
                                <option value="No of task Closed manually">No of task Closed manually</option>
                                <option value="Amount of Matured">Amount of Matured</option>
                                <option value="No of Call Outgoing + Incoming">No of Call Outgoing + Incoming</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <input type="number" name="metrics[${editMetricIndex}][target_value]" class="form-control target-val-input" placeholder="Value">
                        </div>
                        <div class="col-3">
                            <input type="number" name="metrics[${editMetricIndex}][weight]" class="form-control weight-input" placeholder="%" max="100" required>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-danger btn-sm remove-metric"><i class="ti ti-trash"></i></button>
                        </div>
                    </div>
                `;
                $(row).insertBefore(this);
                editMetricIndex++;
                bindEditWeightEvents();
            });

            function bindEditWeightEvents() {
                $('#edit_metrics_container .weight-input').off('input').on('input', calculateEditTotalWeight);
                $('#edit_metrics_container .remove-metric').off('click').on('click', function() {
                    $(this).closest('.metric-row').remove();
                    calculateEditTotalWeight();
                });
            }

            function calculateEditTotalWeight() {
                let total = 0;
                $('#edit_metrics_container .weight-input').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#edit_total_weight').text(total);
                if (total !== 100) {
                    $('#edit_weight_error').show();
                } else {
                    $('#edit_weight_error').hide();
                }
            }

            $('#edit_calculation_method').on('change', function() {
                if (this.value === 'Salary Based' || this.value === 'Hybrid') {
                    $('#edit_salary_multiplier_group').show();
                    $('#edit_salary_multiplier_input').attr('required', 'required');
                } else {
                    $('#edit_salary_multiplier_group').hide();
                    $('#edit_salary_multiplier_input').removeAttr('required');
                }
            });

            // 4. AJAX SUBMIT EDIT TEMPLATE (Without Page Reload)
            $('#editTargetForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let url = form.attr('action');
                let submitBtn = $('#editSaveTargetBtn');
                submitBtn.prop('disabled', true).text('Updating...');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || 'Target Template updated successfully.');
                            }
                            var editOffcanvasEl = document.getElementById('offcanvas_edit_template');
                            var editOffcanvas = bootstrap.Offcanvas.getInstance(editOffcanvasEl) || new bootstrap.Offcanvas(editOffcanvasEl);
                            editOffcanvas.hide();
                            loadTemplates();
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message || 'Failed to update template.');
                            }
                        }
                    },
                    error: function(xhr) {
                        let msg = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        if (typeof toastr !== 'undefined') {
                            toastr.error(msg);
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text('Update Template');
                    }
                });
            });

            // 5. DELETE TEMPLATE (Without Page Reload)
            let templateToDelete = null;
            window.confirmDeleteTemplate = function(id) {
                templateToDelete = id;
                var modalEl = document.getElementById('delete_template');
                var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.show();
            };

            $('#confirmDeleteTemplateBtn').off('click').on('click', function(e) {
                e.preventDefault();
                if (!templateToDelete) return;
                let btn = $(this);
                btn.prop('disabled', true).text('Deleting...');

                $.ajax({
                    url: '/' + boxSlug + '/fieldstracking/target-template/delete/' + templateToDelete,
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val() || '{{ csrf_token() }}'
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || 'Template deleted successfully.');
                            }
                            var modalEl = document.getElementById('delete_template');
                            var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                            modal.hide();
                            loadTemplates();
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message || 'Error deleting template');
                            }
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Failed to delete template.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        if (typeof toastr !== 'undefined') {
                            toastr.error(msg);
                        }
                    },
                    complete: function() {
                        btn.prop('disabled', false).text('Yes, Delete it');
                        templateToDelete = null;
                    }
                });
            });

            // 6. ASSIGN TARGETS (Without Page Reload)
            const assignTargetType = $('#assign_target_type');
            const assignDeptGroup = $('#assign_department_group');
            const assignTeamGroup = $('#assign_team_group');
            const assignEmpGroup = $('#assign_employee_group');
            const assignDeptSelect = $('#assign_department_select');
            const assignTeamSelect = $('#assign_team_select');
            const assignEmpSelect = $('#assign_employee_select');

            function loadDepartments() {
                $.get('/' + boxSlug + '/fieldstracking/target-api/departments', function(data) {
                    assignDeptSelect.empty().append('<option value="">Select Department</option>');
                    data.forEach(dept => {
                        assignDeptSelect.append(`<option value="${dept.id}">${escapeHtml(dept.name || dept.department_name)}</option>`);
                    });
                });
            }

            function loadEmployees() {
                $.get('/' + boxSlug + '/fieldstracking/target-api/employees', function(data) {
                    assignEmpSelect.empty();
                    data.forEach(emp => {
                        let name = (emp.first_name || '') + ' ' + (emp.last_name || '');
                        if (!name.trim() && emp.user) {
                            name = emp.user.name || '';
                        }
                        if (!name.trim()) name = 'Unknown Employee';
                        assignEmpSelect.append(`<option value="${emp.id}">${escapeHtml(name.trim())}</option>`);
                    });
                    if (assignEmpSelect.hasClass('select2-hidden-accessible')) {
                        assignEmpSelect.select2('destroy');
                    }
                    assignEmpSelect.select2({
                        placeholder: "Select Employees",
                        width: '100%',
                        closeOnSelect: false
                    });
                });
            }

            function loadTeams(deptId) {
                assignTeamSelect.empty();
                if (!deptId) return;
                $.get('/' + boxSlug + '/fieldstracking/target-api/teams?department_id=' + deptId, function(data) {
                    data.forEach(team => {
                        assignTeamSelect.append(`<option value="${team.id}">${escapeHtml(team.name || team.team_name)}</option>`);
                    });
                    if (assignTeamSelect.hasClass('select2-hidden-accessible')) {
                        assignTeamSelect.select2('destroy');
                    }
                    assignTeamSelect.select2({
                        placeholder: "Select Teams",
                        width: '100%',
                        closeOnSelect: false
                    });
                });
            }

            assignDeptSelect.on('change', function() {
                if (assignTargetType.val() === 'Team Target') {
                    loadTeams($(this).val());
                }
            });

            function toggleAssignFields() {
                let type = assignTargetType.val();
                assignDeptSelect.removeAttr('required');
                assignTeamSelect.removeAttr('required');
                assignEmpSelect.removeAttr('required');
                assignDeptGroup.hide();
                assignTeamGroup.hide();
                assignEmpGroup.hide();
                $('#assign_all_employees_alert').hide();

                if (type === 'Employee Target') {
                    assignEmpGroup.show();
                    assignEmpSelect.attr('required', 'required');
                    loadEmployees();
                } else if (type === 'All Employees') {
                    $('#assign_all_employees_alert').show();
                } else if (type === 'Department Target') {
                    assignDeptGroup.show();
                    assignDeptSelect.attr('required', 'required');
                    loadDepartments();
                } else if (type === 'Team Target') {
                    assignDeptGroup.show();
                    assignDeptSelect.attr('required', 'required');
                    assignTeamGroup.show();
                    assignTeamSelect.attr('required', 'required');
                    loadDepartments();
                    assignTeamSelect.empty();
                }
            }

            $('#btn_select_all_employees').off('click').on('click', function(e) {
                e.preventDefault();
                $('#assign_employee_select option').prop('selected', true);
                $('#assign_employee_select').trigger('change');
            });

            $('#btn_deselect_all_employees').off('click').on('click', function(e) {
                e.preventDefault();
                $('#assign_employee_select option').prop('selected', false);
                $('#assign_employee_select').trigger('change');
            });

            assignTargetType.on('change', toggleAssignFields);
            toggleAssignFields();

            $('#assignTargetForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let url = form.attr('action');
                let submitBtn = form.find('button[type="submit"]');
                submitBtn.prop('disabled', true).text('Assigning...');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || 'Targets assigned successfully.');
                            }
                            var offcanvasEl = document.getElementById('offcanvas_assign');
                            var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
                            offcanvas.hide();
                            form[0].reset();
                            if ($('#assign_employee_select').length) {
                                $('#assign_employee_select').val(null).trigger('change');
                            }
                            if ($('#assign_team_select').length) {
                                $('#assign_team_select').val(null).trigger('change');
                            }
                            toggleAssignFields();
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message || 'Failed to assign targets.');
                            }
                        }
                    },
                    error: function(xhr) {
                        let msg = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        if (typeof toastr !== 'undefined') {
                            toastr.error(msg);
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text('Assign Target');
                    }
                });
            });

            // 7. HEADER REFRESH BUTTON (AJAX)
            $('#btn_refresh_templates').off('click').on('click', function(e) {
                e.preventDefault();
                let icon = $(this).find('i');
                icon.addClass('fa-spin');
                loadTemplates(function() {
                    icon.removeClass('fa-spin');
                    if (typeof toastr !== 'undefined') {
                        toastr.info('Target templates refreshed');
                    }
                });
            });

            // 8. GLOBAL SEARCH (AJAX DEBOUNCED)
            let searchTimeout = null;
            $('.global-search').off('input').on('input', function() {
                searchQuery = $(this).val();
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    loadTemplates();
                }, 300);
            });

            // 9. DATE RANGE PICKER (AJAX)
            $('.bookingrange').off('apply.daterangepicker cancel.daterangepicker');
            $('.bookingrange').on('apply.daterangepicker', function(ev, picker) {
                startDate = picker.startDate.format('YYYY-MM-DD');
                endDate = picker.endDate.format('YYYY-MM-DD');
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                loadTemplates();
            });
            $('.bookingrange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                startDate = '';
                endDate = '';
                loadTemplates();
            });

            // 10. SORT DROPDOWN (AJAX)
            $(document).on('click', '.sort-item', function(e) {
                e.preventDefault();
                currentSort = $(this).data('sort');
                $('#sort_dropdown_label').html('<i class="ti ti-sort-ascending-2 me-2"></i>' + $(this).text().trim());
                loadTemplates();
            });

            // 11. FILTER DROPDOWN (AJAX)
            $('#btn_apply_filter').off('click').on('click', function(e) {
                e.preventDefault();
                loadTemplates();
                let filterDropdown = bootstrap.Dropdown.getInstance($('.form-sorts > a')[0]);
                if (filterDropdown) filterDropdown.hide();
            });

            $('#btn_reset_filter').off('click').on('click', function(e) {
                e.preventDefault();
                $('.filter-freq-check, .filter-method-check, .filter-status-check').prop('checked', false);
                loadTemplates();
                let filterDropdown = bootstrap.Dropdown.getInstance($('.form-sorts > a')[0]);
                if (filterDropdown) filterDropdown.hide();
            });

            // 12. SELECT ALL CHECKBOX
            $(document).on('change', '#select-all', function() {
                $('.template-checkbox').prop('checked', $(this).is(':checked'));
            });
            $(document).on('change', '.template-checkbox', function() {
                let total = $('.template-checkbox').length;
                let checked = $('.template-checkbox:checked').length;
                $('#select-all').prop('checked', total > 0 && total === checked);
            });

            // 13. MANAGE COLUMNS TOGGLES
            $(document).on('change', '.col-toggle', function() {
                let colIdx = parseInt($(this).data('column'));
                if ($.fn.DataTable.isDataTable('#target_template_table')) {
                    let table = $('#target_template_table').DataTable();
                    let column = table.column(colIdx);
                    column.visible($(this).is(':checked'));
                }
            });

            // 14. TOGGLE TEMPLATE STATUS (Without Page Reload)
            $(document).on('change', '.toggle-template-status', function(e) {
                let tplId = $(this).data('id');
                let isChecked = $(this).is(':checked');
                toggleTemplateStatus(tplId, isChecked ? 'Active' : 'Inactive');
            });

            $(document).on('click', '.toggle-template-badge', function(e) {
                e.preventDefault();
                let tplId = $(this).data('id');
                let checkbox = $(`#tpl_status_${tplId}`);
                let newChecked = !checkbox.is(':checked');
                checkbox.prop('checked', newChecked);
                toggleTemplateStatus(tplId, newChecked ? 'Active' : 'Inactive');
            });

            $(document).on('click', '.toggle-status-action', function(e) {
                e.preventDefault();
                let tplId = $(this).data('id');
                let checkbox = $(`#tpl_status_${tplId}`);
                let newChecked = !checkbox.is(':checked');
                checkbox.prop('checked', newChecked);
                toggleTemplateStatus(tplId, newChecked ? 'Active' : 'Inactive');
            });

            function toggleTemplateStatus(templateId, desiredStatus) {
                let row = $(`tr[data-id="${templateId}"]`);
                let checkbox = $(`#tpl_status_${templateId}`);
                let badge = row.find('.toggle-template-badge');
                let actionItem = row.find('.toggle-status-action');

                badge.css('opacity', '0.5');

                $.ajax({
                    url: '/' + boxSlug + '/fieldstracking/target-template/toggle-status/' + templateId,
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val() || '{{ csrf_token() }}',
                        status: desiredStatus || ''
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        badge.css('opacity', '1');
                        if (response.success) {
                            let isActive = response.is_active;
                            let newStatus = response.status;
                            let newBadgeClass = isActive ? 'bg-success' : 'bg-danger';

                            checkbox.prop('checked', isActive);
                            badge.removeClass('bg-success bg-danger').addClass(newBadgeClass).text(newStatus);
                            actionItem.html(`
                                <i class="ti ${isActive ? 'ti-toggle-left text-warning' : 'ti-toggle-right text-success'} me-1"></i>
                                <span>${isActive ? 'Mark Inactive' : 'Mark Active'}</span>
                            `);

                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || `Template marked as ${newStatus}.`);
                            }
                        } else {
                            checkbox.prop('checked', !checkbox.is(':checked'));
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message || 'Failed to update status.');
                            }
                        }
                    },
                    error: function(xhr) {
                        badge.css('opacity', '1');
                        checkbox.prop('checked', !checkbox.is(':checked'));
                        let msg = 'Failed to update template status.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        if (typeof toastr !== 'undefined') {
                            toastr.error(msg);
                        }
                    }
                });
            }

            // Initial load: if table body has no rows, fetch via AJAX
            if ($('#target_templates_tbody tr').length === 0) {
                loadTemplates();
            }
        });
    })();
</script>
@endpush
