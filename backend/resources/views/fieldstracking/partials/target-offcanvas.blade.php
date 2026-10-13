		<!-- Create target template -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Create Target Template</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="{{ route('fieldstracking.target-template.store', ['box_slug' => request()->route('box_slug')]) }}" method="POST" id="createTargetForm">
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
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                                <div class="col-6">
                                    <label class="col-form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control">
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
                            
                            <!-- Default Metric Row -->
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

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const calcSelect = $('#calculation_method');
                const multiGroup = $('#salary_multiplier_group');
                const multiInput = $('#salary_multiplier_input');
                
                calcSelect.on('change', function() {
                    if (this.value === 'Salary Based' || this.value === 'Hybrid') {
                        multiGroup.show();
                        multiInput.attr('required', 'required');
                    } else {
                        multiGroup.hide();
                        multiInput.removeAttr('required');
                    }
                });

                let metricIndex = 1;
                document.getElementById('add_metric_btn').addEventListener('click', function() {
                    const container = document.getElementById('metrics_container');
                    const row = document.createElement('div');
                    row.className = 'row mb-3 metric-row';
                    row.innerHTML = `
                        <div class="col-4">
                            <input type="hidden" name="metrics[${metricIndex}][type]" value="Number">
                            <select name="metrics[${metricIndex}][name]" class="form-control" required>
                                <option value="Payment Collection">Payment Collection</option>
                                <option value="New Lead">New Lead</option>
                                <option value="New Customer">New Customer</option>
                                <option value="Customer Visit">Customer Visit</option>
                                <option value="Sales Amount">Sales Amount</option>
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
                    `;
                    // Insert before the add button
                    container.insertBefore(row, this);
                    metricIndex++;
                    bindWeightEvents();
                });

                function bindWeightEvents() {
                    document.querySelectorAll('.weight-input').forEach(input => {
                        input.removeEventListener('input', calculateTotalWeight);
                        input.addEventListener('input', calculateTotalWeight);
                    });
                    document.querySelectorAll('.remove-metric').forEach(btn => {
                        btn.removeEventListener('click', removeMetricRow);
                        btn.addEventListener('click', removeMetricRow);
                    });
                }

                function removeMetricRow(e) {
                    e.target.closest('.metric-row').remove();
                    calculateTotalWeight();
                }

                function calculateTotalWeight() {
                    let total = 0;
                    document.querySelectorAll('.weight-input').forEach(input => {
                        total += parseFloat(input.value) || 0;
                    });
                    document.getElementById('total_weight').textContent = total;
                    
                    const btn = document.getElementById('saveTargetBtn');
                    const error = document.getElementById('weight_error');
                    
                    if (total !== 100) {
                        // Just show a warning instead of disabling the button, for easier testing
                        error.style.display = 'block';
                    } else {
                        error.style.display = 'none';
                    }
                }
                
                bindWeightEvents();

                // AJAX Form Submission for Create Template
                $('#createTargetForm').on('submit', function(e) {
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
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                // location.reload(); // Un-comment to auto-reload
                                $('#offcanvas_add').offcanvas('hide');
                                form[0].reset();
                                setTimeout(() => location.reload(), 500); // quick reload to update table
                            }
                        },
                        error: function(error) {
                            alert('An error occurred. Please try again.');
                            console.error(error);
                        },
                        complete: function() {
                            submitBtn.prop('disabled', false).text('Create Template');
                        }
                    });
                });

                // AJAX Form Submission for Assign Target
                
                // Dynamic Assignment Fields Toggle & AJAX loading
                const assignTargetType = $('#assign_target_type');
                const assignDeptGroup = $('#assign_department_group');
                const assignTeamGroup = $('#assign_team_group');
                const assignEmpGroup = $('#assign_employee_group');
                const assignDeptSelect = $('#assign_department_select');
                const assignTeamSelect = $('#assign_team_select');
                const assignEmpSelect = $('#assign_employee_select');
                const assignTargetTemplate = $('#assign_target_template');
                
                const boxSlug = '{{ request()->route("box_slug") }}';

                function loadTemplates() {
                    $.get('/' + boxSlug + '/fieldstracking/target-api/templates', function(data) {
                        // Populate Dropdown
                        assignTargetTemplate.empty().append('<option value="">Choose Template</option>');
                        
                        // Populate Datatable
                        let tbody = $('#target_templates_tbody');
                        tbody.empty();

                        data.forEach(tpl => {
                            assignTargetTemplate.append(`<option value="${tpl.id}">${tpl.name} (${tpl.frequency})</option>`);
                            
                            let calcMethod = tpl.calculation_method;
                            if (calcMethod === 'Salary Based') {
                                calcMethod += `<br><small class="text-muted">(x${tpl.salary_multiplier})</small>`;
                            }

                            let row = `
                                <tr>
                                    <td><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
                                    <td>${tpl.name}</td>
                                    <td>${tpl.target_type}</td>
                                    <td>${calcMethod}</td>
                                    <td>${tpl.frequency}</td>
                                    <td><span class="badge badge-pill badge-primary">${tpl.metrics ? tpl.metrics.length : 0}</span></td>
                                    <td><span class="badge badge-pill bg-success">${tpl.status}</span></td>
                                    <td>
                                        <div class="dropdown table-action">
                                            <a href="#" class="action-icon " data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
                                                    <i class="ti ti-edit text-blue"></i>Edit
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_company">
                                                    <i class="ti ti-trash text-danger"></i>Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                    });
                }
                
                // Load templates on page load
                loadTemplates();

                function loadDepartments() {
                    $.get('/' + boxSlug + '/fieldstracking/target-api/departments', function(data) {
                        assignDeptSelect.empty().append('<option value="">Select Department</option>');
                        data.forEach(dept => {
                            assignDeptSelect.append(`<option value="${dept.id}">${dept.name || dept.department_name}</option>`);
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
                            assignEmpSelect.append(`<option value="${emp.id}">${name.trim()}</option>`);
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
                            assignTeamSelect.append(`<option value="${team.id}">${team.name || team.team_name}</option>`);
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
                    
                    // Reset required and hide all
                    assignDeptSelect.removeAttr('required');
                    assignTeamSelect.removeAttr('required');
                    assignEmpSelect.removeAttr('required');
                    assignDeptGroup.hide();
                    assignTeamGroup.hide();
                    assignEmpGroup.hide();

                    if (type === 'Employee Target') {
                        assignEmpGroup.show();
                        assignEmpSelect.attr('required', 'required');
                        loadEmployees();
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
                        assignTeamSelect.empty(); // Clear until dept is selected
                    }
                }

                assignTargetType.on('change', toggleAssignFields);
                toggleAssignFields(); // Initial state

                $('#assignTargetForm').on('submit', function(e) {
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
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                $('#offcanvas_assign').offcanvas('hide');
                                form[0].reset();
                            }
                        },
                        error: function(error) {
                            alert('An error occurred. Please try again.');
                            console.error(error);
                        },
                        complete: function() {
                            submitBtn.prop('disabled', false).text('Assign Targets');
                        }
                    });
                });
            });
        </script>
		<!-- /Add Company -->
		<!-- Assign Target Template -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_assign">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Assign Target to Employees</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="{{ route('fieldstracking.target-assignment.store', ['box_slug' => request()->route('box_slug')]) }}" method="POST" id="assignTargetForm">
                    @csrf
					<div class="mb-3">
						<label class="col-form-label">Select Target Template <span class="text-danger">*</span></label>
						<select name="target_template_id" class="select" id="assign_target_template" required>
							<option value="">Choose Template</option>
						</select>
					</div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="col-form-label">Target Type <span class="text-danger">*</span></label>
                            <select name="target_type" class="select" id="assign_target_type" required>
                                <option value="Employee Target">Employee Target</option>
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
						<label class="col-form-label">Select Team <span class="text-danger">*</span></label>
						<select name="team_ids[]" class="select" id="assign_team_select" multiple>
						</select>
					</div>

					<div class="mb-3" id="assign_employee_group" style="display:none;">
						<label class="col-form-label">Select Employees <span class="text-danger">*</span></label>
						<select name="employee_ids[]" class="select" id="assign_employee_select" multiple>
						</select>
					</div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="col-form-label">Target Month <span class="text-danger">*</span></label>
                            <select name="target_month" class="select" required>
                                @for($m=1; $m<=12; $m++)
                                    <option value="{{ $m }}" {{ date('m') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="col-form-label">Target Year <span class="text-danger">*</span></label>
                            <select name="target_year" class="select" required>
                                @for($y=date('Y'); $y<=date('Y')+2; $y++)
                                    <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

			        <div class="d-flex align-items-center justify-content-end mt-4">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary">Assign Target</button>
					</div>
				</form>		
			</div>
		</div>
		
		
		

		<!-- Edit Company -->
		<!-- Edit Company -->
		<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
			<div class="offcanvas-header border-bottom">
				<h5 class="fw-semibold">Edit Beat List</h5>
				<button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
					<i class="ti ti-x"></i>
				</button>
			</div>
			<div class="offcanvas-body">
				<form action="company.html">
					<div class="card">
						<div class="card-body">
							<div class="row">
							
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Beat Name <span class="text-danger"> *</span></label>
										<input type="text" class="form-control">
									</div>									
								</div>
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Add Town/City</label>
										<input type="email" class="form-control">
									</div>									
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Add Territory</label>
										<input type="text" class="form-control">
									</div>									
								</div>
							</div>
                        </div>
                    </div>						
					<div class="card">
						<div class="card-body">
							<div class="row">			
								<h5 class="mb-2">Assign Staff</h5>
								<div class="col-md-12">
									<div class="mb-3">
										<select class="select" multiple>
											<option value="Shitender Kumar">Shitender Kumar</option>
											<option value="Shefali Sharma">Shefali Sharma</option>
											<option value="Munish Malhotra">Munish Malhotra</option>
										</select>
									</div>									
								</div>
					        </div>
                        </div>
                    </div>
					
					<div class="card">
						<div class="card-body">
							<div class="row">	
							
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Customer Level</label>
										<select class="select">
											<option disabled>Select Customer Level</option>
											<option>Primary Customer</option>
											<option>secondary Customer</option>
										</select>
									</div>
									<div class="mb-3">
									    <label class="col-form-label">Secondary Customer</label>
										<select class="select">
											<option value="" disabled>Select Secondary Customer</option>
											<option value="">Ganesh Tradors</option>
											<option>Medizia Biotech</option>
										</select>
									</div>
                                    <div class="mb-3">
										<label class="col-form-label">Add Customer</label>
										<select class="select">
											<option>0 Customer Selected</option>
											<option>0</option>
											<option>1</option>
										</select>
									</div> 
									<div class="mb-3">
										<label class="col-form-label">Add Lead</label>
										<select class="select">
											<option>0 Lead Selected</option>
											<option>0</option>
											<option>1</option>
										</select>
									</div> 


									
								</div>
								
							</div>
							
					    </div>
                    </div>
                    <div class="card">
						<div class="card-body">
								
                            <div class="row">   
								<h4 class="mb-2">Add Frequency</h4>
								<div class="col-md-6">
								    
									<div class="mb-3">
										<select class="select">
											<option value="Weekly">Weekly</option>
											<option value="Monthly">Monthly</option>
										</select>
									</div>									
								</div>

								<div class="col-md-6">
									<div class="mb-3">
										<input type="text" placeholder="Enter Frequency" class="form-control">
									</div>									
								</div>
								
								<div class="col-md-12">
									<div class="mb-3">
									    <input type="text" placeholder="Define TC in a day" class="form-control">
									</div>
								</div>
								
								
							</div>	
								
					</div>
					</div>
			        <div class="d-flex align-items-center justify-content-end">
						<button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
							
			</div>
		</div>
					
				</form>
			</div>
		</div>
		<!-- /Edit Company -->
		<!-- /Edit Company -->
		 
		
		<!-- /Upgrade Plane -->

		<!-- Delete Company -->
		<div class="modal fade" id="delete_company" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Company?</h4>
							<p class="mb-0">Are you sure you want to remove <br> company you selected.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="company.html" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete Company -->

		<!-- Company Detail -->
		<div class="modal fade custom-modal" id="company_detail">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Company Detail</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body pb-0">
						<div class="mb-3">
							<div class="d-flex justify-content-between align-items-center rounded bg-light-400 p-3">                                                
								<div class="file-name-icon d-flex align-items-center">
									<a href="#" class="avatar avatar-sm border rounded p-1 me-2 flex-shrink-0">
										<img src="assets/img/icons/company-icon-01.svg" class="w-auto h-auto" alt="img">
									</a> 
									<div>
										<p class="text-gray-9 fw-medium mb-0">NovaWave LLC</p>
									</div>
								</div>                                        
								<span class="badge badge-success">Active</span>
							</div>
						</div>
						<div>                                                
							<p class="text-gray-9 fw-medium">Basic Info</p>                                            
							<div class="border-bottom mb-3">
								<div class="row align-items-center">
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Account URL</p>
											<p class="text-gray-9">nw.example.com</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Phone Number</p>
											<p class="text-gray-9">+1 875455453</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Website</p>
											<p class="text-gray-9">www.exmple.com</p>
										</div>
									</div>
								</div>
								<div class="row align-items-center">
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Currency</p>
											<p class="text-gray-9">United Stated Dollar (USD)</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Language</p>
											<p class="text-gray-9">English</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Addresss</p>
											<p class="text-gray-9">3705 Lynn Avenue, Phelps, WI 54554</p>
										</div>
									</div>
								</div>
							</div>
							<p class="text-gray-9 fw-medium">Plan Details</p>                                            
							<div>
								<div class="row align-items-center">
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Plan Name</p>
											<p class="text-gray-9">Advanced</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Plan Type</p>
											<p class="text-gray-9">Monthly</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Price</p>
											<p class="text-gray-9">$200</p>
										</div>
									</div>
								</div>
								<div class="row align-items-center">
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Register Date</p>
											<p class="text-gray-9">12 Sep 2024</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<p class="fs-12 mb-0">Expiring On</p>
											<p class="text-gray-9">11 Oct 2024</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						
					</div>
					
					
				</div>
			</div>
		</div>
		<!-- /Company Detail -->

	</div>
	
