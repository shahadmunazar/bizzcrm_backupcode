@extends('layouts.main')
@section('content')
@section('title', 'Salary Settings')
<div class="page-wrapper">
    <div class="content">
        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Salary Settings</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/"><i class="ti ti-smart-home"></i></a></li>
                        <li class="breadcrumb-item">HR</li>
                        <li class="breadcrumb-item active" aria-current="page">Salary Settings</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                <div class="mb-2">
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="ti ti-file-export me-1"></i>Export
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-3">
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1"><i class="ti ti-file-type-pdf me-1"></i>Export as PDF</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1"><i class="ti ti-file-type-xls me-1"></i>Export as Excel</a></li>
                        </ul>
                    </div>
                </div>
                </div>
            </div>

        <div class="d-flex my-xl-auto right-content align-items-center flex-wrap justify-content-end mb-3">
            <div class="mb-2">
                <a href="#" data-bs-toggle="modal" data-bs-target="#add_salary_setting" class="btn btn-primary d-flex align-items-center">
                    <i class="ti ti-circle-plus me-2"></i>Create Salary Structure
                </a>
            </div>
        </div>

        <!-- Salary Setting List -->
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                <h5>Salary Setting List</h5>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="me-3">
                        <button type="button" class="btn btn-white d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                            <i class="ti ti-columns me-1"></i>Manage Columns
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="custom-datatable-filter table-responsive">
                    <table class="table" id="SalarySettings">
                        <thead class="thead-light">
                            <tr>
                                <th class="no-sort"><div class="form-check form-check-md"><input class="form-check-input" type="checkbox" id="select-all" /></div></th>
                                <th>Frequency</th>
                                <th>Salary Structure Name</th>
                                <th>Status</th>
                                <th class="no-sort">Details</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

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

<!-- Add Salary Setting Modal -->
<div class="modal fade" id="add_salary_setting" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h4 class="modal-title">Create Salary Structure</h4>
                <button type="button" class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="addSalaryFormCustom" novalidate>
                @csrf
                <div class="modal-body pt-0">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded mb-0" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#add-general">General</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#add-earnings">Earnings</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#add-reimbursements">Reimbursements</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#add-contributions">Contributions</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#add-deductions">Deductions</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#add-onetime">One-Time</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#add-variable">Variable</a></li>
                        </ul>
                        <div class="form-check form-switch ms-3">
                            <input class="form-check-input global-type-toggle" type="checkbox" id="addGlobalPercentage">
                            <label class="form-check-label fw-bold" for="addGlobalPercentage">Set All to Percentage (%)</label>
                            </div>
                        </div>

                    <div class="tab-content">
                        <!-- General Tab -->
                        <div id="add-general" class="tab-pane active">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Salary Calculation Frequency <span class="text-danger">*</span></label>
                                    <select name="frequency" class="form-select" required>
                                        <option value="" disabled selected>Select salary Calculation Frequency</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="daily">Daily</option>
                                        <option value="hourly">Hourly</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Salary Structure Name <span class="text-danger">*</span></label>
                                    <input type="text" name="salary_structure_name" class="form-control" placeholder="Enter Salary Structure Name" value="">
                                </div>
                                <div class="col-md-6 mb-3 monthly-options-area d-none">
                                    <label class="form-label fw-bold">Monthly Options</label>
                                    <div class="d-flex gap-3 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="monthly_option" value="calendar" checked>
                                            <label class="form-check-label">Calendar</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="monthly_option" value="specific">
                                            <label class="form-check-label">Specific Date</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Comp Off <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-3 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="comp_off_type" value="paid" checked>
                                            <label class="form-check-label">Paid</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="comp_off_type" value="unpaid">
                                            <label class="form-check-label">Unpaid</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 monthly-options-area d-none">
                                    <div class="specific-date-range d-none border p-3 rounded bg-light mb-3">
                                        <label class="form-label">Select Day Range (1–31)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="start_date" min="1" max="31" placeholder="Start day">
                                            <span class="input-group-text">to</span>
                                            <input type="number" class="form-control" name="end_date" min="1" max="31" placeholder="End day">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="status" checked>
                                        <label class="form-check-label">Active Status</label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary next-tab" data-modal="#add_salary_setting">Next <i class="ti ti-arrow-right ms-1"></i></button>
                            </div>
                        </div>

                        @php
                            $earnings = [
                                'basic' => 'Basic', 'da' => 'DA', 'hra' => 'HRA', 'conveyance' => 'Conveyance',
                                'allowance' => 'Allowance', 'medical_allowance' => 'Medical Allowance', 'special_allowance' => 'Special Allowance',
                                'leave_travel_allowance' => 'Leave Travel Allowance', 'city_compensatory_allowance' => 'City Compensatory Allowance',
                                'sales_target_incentives' => 'Sales Incentives', 'shift_allowance' => 'Shift Allowance',
                                'transport_allowance' => 'Transport Allowance', 'food_allowance' => 'Food Allowance',
                                'overtime_allowance' => 'Overtime Allowance (OT)', 'uniform_allowance' => 'Uniform Allowance',
                                'mobile_internet_allowance' => 'Mobile / Internet Allowance', 'project_allowance' => 'Project Allowance',
                                'education_allowance' => 'Education Allowance', 'professional_allowance' => 'Professional Allowance',
                                'arrears_earnings' => 'Arrears (Earnings)', 'others' => 'Others'
                            ];
                            $reimbursements = [
                                'travel_reimbursement' => 'Travel Reimbursement', 'fuel_reimbursement' => 'Petrol / Fuel Reimbursement',
                                'telephone_reimbursement' => 'Telephone / Data Reimbursement', 'driver_reimbursement' => 'Driver Salary Reimbursement',
                                'medical_reimbursement' => 'Medical Reimbursement', 'uniform_reimbursement' => 'Uniform / Equipment Reimbursement'
                            ];
                            $contributions = [
                                'employer_pf' => 'Employer PF', 'employer_esi' => 'Employer ESI', 'gratuity' => 'Gratuity',
                                'superannuation' => 'Superannuation', 'employer_lwf' => 'Labor Welfare Fund (Employer)'
                            ];
                            $deductions = [
                                'employee_pf' => 'Employee PF Contribution', 'employee_esi' => 'Employee ESI Contribution',
                                'professional_tax' => 'Professional Tax (PT)', 'tds' => 'TDS', 'salary_advance_recovery' => 'Salary Advance Recovery',
                                'loan_emi_recovery' => 'Loan EMI Recovery', 'canteen_deduction' => 'Food / Canteen Deduction',
                                'attendance_loss' => 'Late Coming / Attendance Loss', 'notice_period_recovery' => 'Notice Period Recovery',
                                'arrears_recovery' => 'Arrears (Recovery)', 'insurance_premium_employee' => 'Insurance Premium (Employee)'
                            ];
                            $onetime = [
                                'joining_bonus' => 'Joining Bonus', 'retention_bonus' => 'Retention Bonus', 'performance_bonus' => 'Performance Bonus',
                                'ex_gratia' => 'Ex-gratia', 'full_final_settlement' => 'Full & Final Settlement',
                                'leave_encashment' => 'Leave Encashment', 'gratuity_payout' => 'Gratuity Payout', 'severance_compensation' => 'Severance Compensation'
                            ];
                        @endphp

                        <!-- Earnings Tab -->
                        <div id="add-earnings" class="tab-pane fade">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Earnings Components</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary open-add-field" data-tab="earnings"><i class="ti ti-plus me-1"></i> Add Field</button>
                            </div>
                            <p class="text-muted small mb-3"><i class="ti ti-info-circle me-1"></i> Note: Percentage calculations for earnings are based on the <strong>Monthly Gross Salary</strong>.</p>
                            <div class="row">
                                @foreach($earnings as $name => $label)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ $label }}</label>
                                    <div class="input-group">
                                        <input type="number" name="{{ $name }}" class="form-control percentage-input" value="0" step="0.01" min="0" max="100">
                                        <select name="{{ $name }}_type" class="form-select component-type-select" style="max-width: 80px;">
                                            <option value="fixed">{{ $box->currency_symbol ?? '₹' }}</option>
                                            <option value="percentage">%</option>
                                        </select>
                        </div>
                                    <div class="max-amount-field mt-2" style="display: none;">
                                        <label class="form-label small text-muted">Max Amount ({{ $box->currency_symbol ?? '₹' }})</label>
                                        <input type="number" name="{{ $name }}_max" class="form-control form-control-sm" value="0" step="0.01" placeholder="Enter max amount">
                                    </div>
                                    <div class="invalid-feedback percentage-error" style="display: none;">Percentage cannot exceed 100%</div>
                            </div>
                                @endforeach
                        </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-white border prev-tab" data-modal="#add_salary_setting"><i class="ti ti-arrow-left me-1"></i> Previous</button>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary next-tab" data-modal="#add_salary_setting">Next <i class="ti ti-arrow-right ms-1"></i></button>
                            </div>
                            </div>
                        </div>

                        <!-- Reimbursements Tab -->
                        <div id="add-reimbursements" class="tab-pane fade">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Reimbursement Components</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary open-add-field" data-tab="reimbursements"><i class="ti ti-plus me-1"></i> Add Field</button>
                                </div>
                            <div class="row">
                                @foreach($reimbursements as $name => $label)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ $label }}</label>
                                    <div class="input-group">
                                        <input type="number" name="{{ $name }}" class="form-control percentage-input" value="0" step="0.01" min="0" max="100">
                                        <select name="{{ $name }}_type" class="form-select component-type-select" style="max-width: 80px;">
                                            <option value="fixed">{{ $box->currency_symbol ?? '₹' }}</option>
                                            <option value="percentage">%</option>
                                    </select>
                                </div>
                                    <div class="max-amount-field mt-2" style="display: none;">
                                        <label class="form-label small text-muted">Max Amount ({{ $box->currency_symbol ?? '₹' }})</label>
                                        <input type="number" name="{{ $name }}_max" class="form-control form-control-sm" value="0" step="0.01" placeholder="Enter max amount">
                                    </div>
                                    <div class="invalid-feedback percentage-error" style="display: none;">Percentage cannot exceed 100%</div>
                            </div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-white border prev-tab" data-modal="#add_salary_setting"><i class="ti ti-arrow-left me-1"></i> Previous</button>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary next-tab" data-modal="#add_salary_setting">Next <i class="ti ti-arrow-right ms-1"></i></button>
                                </div>
                            </div>
                        </div>

                        <!-- Contributions Tab -->
                        <div id="add-contributions" class="tab-pane fade">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Contribution Components</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary open-add-field" data-tab="contributions"><i class="ti ti-plus me-1"></i> Add Field</button>
                    </div>
                            <div class="row">
                                @foreach($contributions as $name => $label)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ $label }}</label>
                                    <div class="input-group">
                                        <input type="number" name="{{ $name }}" class="form-control percentage-input" value="0" step="0.01" min="0" max="100">
                                        <select name="{{ $name }}_type" class="form-select component-type-select" style="max-width: 80px;">
                                            <option value="fixed">{{ $box->currency_symbol ?? '₹' }}</option>
                                            <option value="percentage">%</option>
                                        </select>
                        </div>
                                    <div class="max-amount-field mt-2" style="display: none;">
                                        <label class="form-label small text-muted">Max Amount ({{ $box->currency_symbol ?? '₹' }})</label>
                                        <input type="number" name="{{ $name }}_max" class="form-control form-control-sm" value="0" step="0.01" placeholder="Enter max amount">
                                    </div>
                                    <div class="invalid-feedback percentage-error" style="display: none;">Percentage cannot exceed 100%</div>
                            </div>
                                @endforeach
                        </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-white border prev-tab" data-modal="#add_salary_setting"><i class="ti ti-arrow-left me-1"></i> Previous</button>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary next-tab" data-modal="#add_salary_setting">Next <i class="ti ti-arrow-right ms-1"></i></button>
                            </div>
                        </div>
                            </div>

                        <!-- Deductions Tab -->
                        <div id="add-deductions" class="tab-pane fade">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Deduction Components</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary open-add-field" data-tab="deductions"><i class="ti ti-plus me-1"></i> Add Field</button>
                            </div>
                            <div class="row">
                                @foreach($deductions as $name => $label)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ $label }}</label>
                                    <div class="input-group">
                                        <input type="number" name="{{ $name }}" class="form-control percentage-input" value="0" step="0.01" min="0" max="100">
                                        <select name="{{ $name }}_type" class="form-select component-type-select" style="max-width: 80px;">
                                            <option value="fixed">{{ $box->currency_symbol ?? '₹' }}</option>
                                            <option value="percentage">%</option>
                                        </select>
                        </div>
                                    <div class="max-amount-field mt-2" style="display: none;">
                                        <label class="form-label small text-muted">Max Amount ({{ $box->currency_symbol ?? '₹' }})</label>
                                        <input type="number" name="{{ $name }}_max" class="form-control form-control-sm" value="0" step="0.01" placeholder="Enter max amount">
                                    </div>
                                    <div class="invalid-feedback percentage-error" style="display: none;">Percentage cannot exceed 100%</div>
                            </div>
                                @endforeach
                        </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-white border prev-tab" data-modal="#add_salary_setting"><i class="ti ti-arrow-left me-1"></i> Previous</button>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary next-tab" data-modal="#add_salary_setting">Next <i class="ti ti-arrow-right ms-1"></i></button>
                            </div>
                        </div>
                            </div>

                        <!-- One-Time Tab -->
                        <div id="add-onetime" class="tab-pane fade">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">One-Time Components</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary open-add-field" data-tab="onetime"><i class="ti ti-plus me-1"></i> Add Field</button>
                            </div>
                            <div class="row">
                                @foreach($onetime as $name => $label)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ $label }}</label>
                                    <div class="input-group">
                                        <input type="number" name="{{ $name }}" class="form-control percentage-input" value="0" step="0.01" min="0" max="100">
                                        <select name="{{ $name }}_type" class="form-select component-type-select" style="max-width: 80px;">
                                            <option value="fixed">{{ $box->currency_symbol ?? '₹' }}</option>
                                            <option value="percentage">%</option>
                                    </select>
                                </div>
                                    <div class="max-amount-field mt-2" style="display: none;">
                                        <label class="form-label small text-muted">Max Amount ({{ $box->currency_symbol ?? '₹' }})</label>
                                        <input type="number" name="{{ $name }}_max" class="form-control form-control-sm" value="0" step="0.01" placeholder="Enter max amount">
                                    </div>
                                    <div class="invalid-feedback percentage-error" style="display: none;">Percentage cannot exceed 100%</div>
                            </div>
                                @endforeach
                        </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-white border prev-tab" data-modal="#add_salary_setting"><i class="ti ti-arrow-left me-1"></i> Previous</button>
                                <button type="button" class="btn btn-primary next-tab" data-modal="#add_salary_setting">Next <i class="ti ti-arrow-right ms-1"></i></button>
                            </div>
                        </div>

                        <!-- Variable Tab -->
                        <div id="add-variable" class="tab-pane fade">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Variable Components</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary open-add-field" data-tab="variable"><i class="ti ti-plus me-1"></i> Add Field</button>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Variable Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="variable_amount" class="form-control percentage-input" value="0" step="0.01" min="0" max="100" required>
                                        <select name="variable_amount_type" class="form-select component-type-select" style="max-width: 80px;">
                                            <option value="fixed">{{ $box->currency_symbol ?? '₹' }}</option>
                                            <option value="percentage">%</option>
                                        </select>
                                    </div>
                                    <div class="max-amount-field mt-2" style="display: none;">
                                        <label class="form-label small text-muted">Max Amount ({{ $box->currency_symbol ?? '₹' }})</label>
                                        <input type="number" name="variable_amount_max" class="form-control form-control-sm" value="0" step="0.01" placeholder="Enter max amount">
                                    </div>
                                    <div class="invalid-feedback percentage-error" style="display: none;">Percentage cannot exceed 100%</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Variable Label</label>
                                    <input type="text" name="variable_label" class="form-control" value="" placeholder="Enter variable label">
                                </div>
                        </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-white border prev-tab" data-modal="#add_salary_setting"><i class="ti ti-arrow-left me-1"></i> Previous</button>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success">Save Salary Setting <i class="ti ti-check ms-1"></i></button>
                    </div>
                </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Salary Setting Modal -->
<div class="modal fade" id="editSalaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h4 class="modal-title">Edit Salary Setting</h4>
                <button type="button" class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="editSalaryForm" novalidate>
                @csrf
                <input type="hidden" name="id" />
                <div class="modal-body pt-0">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded mb-0" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#edit-general">General</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#edit-earnings">Earnings</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#edit-reimbursements">Reimbursements</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#edit-contributions">Contributions</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#edit-deductions">Deductions</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#edit-onetime">One-Time</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#edit-variable">Variable</a></li>
                        </ul>
                        <div class="form-check form-switch ms-3">
                            <input class="form-check-input global-type-toggle" type="checkbox" id="editGlobalPercentage">
                            <label class="form-check-label fw-bold" for="editGlobalPercentage">Set All to Percentage (%)</label>
                    </div>
                </div>

                    <div class="tab-content">
                        <!-- General Tab -->
                        <div id="edit-general" class="tab-pane active">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Salary Calculation Frequency <span class="text-danger">*</span></label>
                                    <select name="frequency" class="form-select" required>
                                        <option value="" disabled>Select salary Calculation Frequency</option>
                            <option value="monthly">Monthly</option>
                            <option value="weekly">Weekly</option>
                            <option value="daily">Daily</option>
                            <option value="hourly">Hourly</option>
                        </select>
                    </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Salary Structure Name <span class="text-danger">*</span></label>
                                    <input type="text" name="salary_structure_name" class="form-control" placeholder="Enter Salary Structure Name" value="">
                                </div>
                                <div class="col-md-6 mb-3 monthly-options-area d-none">
                                    <label class="form-label fw-bold">Monthly Options</label>
                                    <div class="d-flex gap-3 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="monthly_option" value="calendar">
                                            <label class="form-check-label">Calendar</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="monthly_option" value="specific">
                                            <label class="form-check-label">Specific Date</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Comp Off <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-3 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="comp_off_type" value="paid">
                                            <label class="form-check-label">Paid</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="comp_off_type" value="unpaid">
                                            <label class="form-check-label">Unpaid</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 monthly-options-area d-none">
                                    <div class="specific-date-range d-none border p-3 rounded bg-light mb-3">
                                        <label class="form-label">Select Day Range (1–31)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="start_date" min="1" max="31">
                                            <span class="input-group-text">to</span>
                                            <input type="number" class="form-control" name="end_date" min="1" max="31">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="status">
                                        <label class="form-check-label">Active Status</label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary next-tab" data-modal="#editSalaryModal">Next <i class="ti ti-arrow-right ms-1"></i></button>
                        </div>
                            </div>

                        <!-- Earnings Tab -->
                        <div id="edit-earnings" class="tab-pane fade">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Earnings Components</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary open-add-field" data-tab="earnings"><i class="ti ti-plus me-1"></i> Add Field</button>
                        </div>
                            <p class="text-muted small mb-3"><i class="ti ti-info-circle me-1"></i> Note: Percentage calculations for earnings are based on the <strong>Monthly Gross Salary</strong> set in the General tab.</p>
                            <div class="row">
                                @foreach($earnings as $name => $label)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ $label }}</label>
                                    <div class="input-group">
                                        <input type="number" name="{{ $name }}" class="form-control percentage-input" step="0.01" value="0" min="0" max="100">
                                        <select name="{{ $name }}_type" class="form-select component-type-select" style="max-width: 80px;">
                                            <option value="fixed">{{ $box->currency_symbol ?? '₹' }}</option>
                                            <option value="percentage">%</option>
                                        </select>
                            </div>
                                    <div class="max-amount-field mt-2" style="display: none;">
                                        <label class="form-label small text-muted">Max Amount ({{ $box->currency_symbol ?? '₹' }})</label>
                                        <input type="number" name="{{ $name }}_max" class="form-control form-control-sm" value="0" step="0.01" placeholder="Enter max amount">
                                    </div>
                                    <div class="invalid-feedback percentage-error" style="display: none;">Percentage cannot exceed 100%</div>
                        </div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-white border prev-tab" data-modal="#editSalaryModal"><i class="ti ti-arrow-left me-1"></i> Previous</button>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary next-tab" data-modal="#editSalaryModal">Next <i class="ti ti-arrow-right ms-1"></i></button>
                        </div>
                            </div>
                        </div>

                        <!-- Reimbursements Tab -->
                        <div id="edit-reimbursements" class="tab-pane fade">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Reimbursement Components</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary open-add-field" data-tab="reimbursements"><i class="ti ti-plus me-1"></i> Add Field</button>
                                </div>
                            <div class="row">
                                @foreach($reimbursements as $name => $label)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ $label }}</label>
                                    <div class="input-group">
                                        <input type="number" name="{{ $name }}" class="form-control percentage-input" step="0.01" value="" min="0" max="100">
                                        <select name="{{ $name }}_type" class="form-select component-type-select" style="max-width: 80px;">
                                            <option value="fixed">{{ $box->currency_symbol ?? '₹' }}</option>
                                            <option value="percentage">%</option>
                                    </select>
                                </div>
                                    <div class="max-amount-field mt-2" style="display: none;">
                                        <label class="form-label small text-muted">Max Amount ({{ $box->currency_symbol ?? '₹' }})</label>
                                        <input type="number" name="{{ $name }}_max" class="form-control form-control-sm" value="0" step="0.01" placeholder="Enter max amount">
                                    </div>
                                    <div class="invalid-feedback percentage-error" style="display: none;">Percentage cannot exceed 100%</div>
                            </div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-white border prev-tab" data-modal="#editSalaryModal"><i class="ti ti-arrow-left me-1"></i> Previous</button>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary next-tab" data-modal="#editSalaryModal">Next <i class="ti ti-arrow-right ms-1"></i></button>
                                </div>
                            </div>
                        </div>

                        <!-- Contributions Tab -->
                        <div id="edit-contributions" class="tab-pane fade">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Contribution Components</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary open-add-field" data-tab="contributions"><i class="ti ti-plus me-1"></i> Add Field</button>
                    </div>
                            <div class="row">
                                @foreach($contributions as $name => $label)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ $label }}</label>
                                    <div class="input-group">
                                        <input type="number" name="{{ $name }}" class="form-control percentage-input" step="0.01" value="" min="0" max="100">
                                        <select name="{{ $name }}_type" class="form-select component-type-select" style="max-width: 80px;">
                                            <option value="fixed">{{ $box->currency_symbol ?? '₹' }}</option>
                                            <option value="percentage">%</option>
                                        </select>
                        </div>
                                    <div class="max-amount-field mt-2" style="display: none;">
                                        <label class="form-label small text-muted">Max Amount ({{ $box->currency_symbol ?? '₹' }})</label>
                                        <input type="number" name="{{ $name }}_max" class="form-control form-control-sm" value="0" step="0.01" placeholder="Enter max amount">
                                    </div>
                                    <div class="invalid-feedback percentage-error" style="display: none;">Percentage cannot exceed 100%</div>
                            </div>
                                @endforeach
                        </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-white border prev-tab" data-modal="#editSalaryModal"><i class="ti ti-arrow-left me-1"></i> Previous</button>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary next-tab" data-modal="#editSalaryModal">Next <i class="ti ti-arrow-right ms-1"></i></button>
                            </div>
                        </div>
                            </div>

                        <!-- Deductions Tab -->
                        <div id="edit-deductions" class="tab-pane fade">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Deduction Components</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary open-add-field" data-tab="deductions"><i class="ti ti-plus me-1"></i> Add Field</button>
                            </div>
                            <div class="row">
                                @foreach($deductions as $name => $label)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ $label }}</label>
                                    <div class="input-group">
                                        <input type="number" name="{{ $name }}" class="form-control percentage-input" step="0.01" value="" min="0" max="100">
                                        <select name="{{ $name }}_type" class="form-select component-type-select" style="max-width: 80px;">
                                            <option value="fixed">{{ $box->currency_symbol ?? '₹' }}</option>
                                            <option value="percentage">%</option>
                                        </select>
                        </div>
                                    <div class="max-amount-field mt-2" style="display: none;">
                                        <label class="form-label small text-muted">Max Amount ({{ $box->currency_symbol ?? '₹' }})</label>
                                        <input type="number" name="{{ $name }}_max" class="form-control form-control-sm" value="0" step="0.01" placeholder="Enter max amount">
                                    </div>
                                    <div class="invalid-feedback percentage-error" style="display: none;">Percentage cannot exceed 100%</div>
                            </div>
                                @endforeach
                        </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-white border prev-tab" data-modal="#editSalaryModal"><i class="ti ti-arrow-left me-1"></i> Previous</button>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary next-tab" data-modal="#editSalaryModal">Next <i class="ti ti-arrow-right ms-1"></i></button>
                            </div>
                        </div>
                            </div>

                        <!-- One-Time Tab -->
                        <div id="edit-onetime" class="tab-pane fade">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">One-Time Components</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary open-add-field" data-tab="onetime"><i class="ti ti-plus me-1"></i> Add Field</button>
                        </div>
                            <div class="row">
                                @foreach($onetime as $name => $label)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ $label }}</label>
                                    <div class="input-group">
                                        <input type="number" name="{{ $name }}" class="form-control percentage-input" step="0.01" value="" min="0" max="100">
                                        <select name="{{ $name }}_type" class="form-select component-type-select" style="max-width: 80px;">
                                            <option value="fixed">{{ $box->currency_symbol ?? '₹' }}</option>
                                            <option value="percentage">%</option>
                                        </select>
                            </div>
                                    <div class="max-amount-field mt-2" style="display: none;">
                                        <label class="form-label small text-muted">Max Amount ({{ $box->currency_symbol ?? '₹' }})</label>
                                        <input type="number" name="{{ $name }}_max" class="form-control form-control-sm" value="0" step="0.01" placeholder="Enter max amount">
                                    </div>
                                    <div class="invalid-feedback percentage-error" style="display: none;">Percentage cannot exceed 100%</div>
                        </div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-white border prev-tab" data-modal="#editSalaryModal"><i class="ti ti-arrow-left me-1"></i> Previous</button>
                                <button type="button" class="btn btn-primary next-tab" data-modal="#editSalaryModal">Next <i class="ti ti-arrow-right ms-1"></i></button>
                            </div>
                        </div>

                        <!-- Variable Tab -->
                        <div id="edit-variable" class="tab-pane fade">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Variable Components</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary open-add-field" data-tab="variable"><i class="ti ti-plus me-1"></i> Add Field</button>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Variable Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="variable_amount" class="form-control percentage-input" step="0.01" value="" min="0" max="100" required>
                                        <select name="variable_amount_type" class="form-select component-type-select" style="max-width: 80px;">
                                            <option value="fixed">{{ $box->currency_symbol ?? '₹' }}</option>
                                            <option value="percentage">%</option>
                                        </select>
                                    </div>
                                    <div class="max-amount-field mt-2" style="display: none;">
                                        <label class="form-label small text-muted">Max Amount ({{ $box->currency_symbol ?? '₹' }})</label>
                                        <input type="number" name="variable_amount_max" class="form-control form-control-sm" value="0" step="0.01" placeholder="Enter max amount">
                                    </div>
                                    <div class="invalid-feedback percentage-error" style="display: none;">Percentage cannot exceed 100%</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Variable Label</label>
                                    <input type="text" name="variable_label" class="form-control" value="" placeholder="Enter variable label">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-white border prev-tab" data-modal="#editSalaryModal"><i class="ti ti-arrow-left me-1"></i> Previous</button>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success">Save Changes <i class="ti ti-check ms-1"></i></button>
                        </div>
                            </div>
                        </div>
                                </div>
                </div>
            </form>
                            </div>
                        </div>
                    </div>

<!-- View Salary Breakdown Modal -->
<div class="modal fade" id="viewSalaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h4 class="modal-title">Salary Structure Breakdown</h4>
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-primary print-section-modal">
                        <i class="ti ti-printer me-1"></i> Print
                    </button>
                    <button type="button" class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                        </div>
                        </div>
            <div class="modal-body pt-0" id="salaryBreakdownContent" style="max-height: 70vh; overflow-y: auto;">
                <!-- Content injected by JS -->
                    </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i> Close
                </button>
                        </div>
                    </div>
                    </div>
                </div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="delete_modal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header border-0 bg-danger text-white">
                <h5 class="modal-title fw-bold" id="deleteModalLabel">
                    <i class="ti ti-alert-triangle me-2"></i>Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Step 1: Initial Confirmation -->
                <div id="delete_step_1">
                    <p class="mb-3">Are you sure you want to delete this salary structure?</p>
                    <div class="alert alert-info">
                        <strong id="delete_salary_name"></strong>
                    </div>
                </div>

                <!-- Step 2: Custom Fields Warning -->
                <div id="delete_step_2" style="display: none;">
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="ti ti-alert-circle me-2"></i>Custom Fields Detected
                        </h6>
                        <p class="mb-2">This salary structure has custom fields associated with it:</p>
                        <ul id="custom_fields_list" class="mb-2"></ul>
                        <p class="mb-0"><strong>What would you like to do?</strong></p>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="delete_option" id="delete_option_remove" value="remove" checked>
                        <label class="form-check-label" for="delete_option_remove">
                            <strong>Remove custom fields first</strong> (Recommended)
                            <br><small class="text-muted">Delete custom field columns from the database. This action cannot be undone.</small>
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="delete_option" id="delete_option_force" value="force">
                        <label class="form-check-label" for="delete_option_force">
                            <strong>Force delete</strong> (Delete structure and remove custom field columns)
                            <br><small class="text-muted">This will delete the salary structure and remove all custom field columns.</small>
                        </label>
                    </div>
                </div>

                <!-- Step 3: Data Warning -->
                <div id="delete_step_3" style="display: none;">
                    <div class="alert alert-danger">
                        <h6 class="alert-heading">
                            <i class="ti ti-alert-triangle me-2"></i>Data Found in Custom Fields
                        </h6>
                        <p class="mb-2">The following custom fields contain data:</p>
                        <ul id="custom_fields_with_data" class="mb-2"></ul>
                        <p class="mb-0"><strong>Deleting will permanently remove this data. This action cannot be undone!</strong></p>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirm_data_delete">
                        <label class="form-check-label" for="confirm_data_delete">
                            I understand that all custom field data will be permanently deleted
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="ti ti-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
                </div>

<!-- Add Custom Field Modal -->
<div class="modal fade" id="add_fields" tabindex="-1" aria-labelledby="addFieldsLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content rounded-3 shadow">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold" id="addFieldsLabel">Add Custom Field</h5>
        <button type="button" class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
      </div>

      <form id="customFieldForm">
        <div class="modal-body">
          <input type="hidden" name="target_tab" id="target_tab_input">
          <div class="mb-3">
            <label class="form-label">Field Label <span class="text-danger">*</span></label>
            <input type="text" name="label" class="form-control" placeholder="e.g. Bonus Amount" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Input Type <span class="text-danger">*</span></label>
            <select name="type" class="form-select" required>
              <option value="">Choose Type</option>
              <option value="text">Text</option>
              <option value="number">Number</option>
              <option value="textarea">Text Area</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Default Value</label>
            <input type="text" name="default_value" class="form-control" placeholder="Optional">
          </div>

          <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="required" id="requiredField">
            <label class="form-check-label" for="requiredField">Required Field</label>
          </div>

          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="status" id="statusField" checked>
            <label class="form-check-label" for="statusField">Active</label>
          </div>
        </div>

        <div class="modal-footer border-0">
          <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Field</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Toast container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    <div id="toastMessage" class="toast align-items-center text-white bg-success border-0" role="alert"
        aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastBody">
                Success!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>

    @push('scripts')
    <script>
        const currencySymbol = "{{ $box->currency_symbol ?? '₹' }}";
        const currencyCode = "{{ $box->currency_code ?? 'INR' }}";
        $(document).ready(function() {
            let deleteId = null;
            let salaryTable;

            function showToast(message, type = 'success', title = null) {
                const $toast = $('#toastMessage');
                const $toastBody = $('#toastBody');

                if (!title) {
                    title = type === 'success' ? 'Success!' : 'Error!';
                }

                $toast.removeClass('bg-danger bg-warning').addClass(type === 'success' ? 'bg-success' : 'bg-danger');
                $toastBody.html(`
                    <div class="d-flex align-items-center">
                        <i class="ti ti-${type === 'success' ? 'check-circle' : 'alert-circle'} me-3 fs-4"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 text-white fw-bold">${title}</h6>
                            <small class="text-white-50">${message}</small>
                        </div>
                    </div>
                `);

                const toast = new bootstrap.Toast($toast[0], {
                    autohide: true,
                    delay: 5000
                });
                toast.show();
            }

            function displayErrors(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let messages = '';
                    $.each(xhr.responseJSON.errors, function(key, value) { messages += value + '\n'; });
                    alert(messages);
                } else {
                    alert(xhr.responseJSON?.message || 'Something went wrong!');
                }
            }

            const sections = {
                earnings: {!! json_encode($earnings) !!},
                reimbursements: {!! json_encode($reimbursements) !!},
                contributions: {!! json_encode($contributions) !!},
                deductions: {!! json_encode($deductions) !!},
                onetime: {!! json_encode($onetime) !!},
                variable: {
                    variable_amount: 'Variable Amount',
                    variable_label: 'Variable Label'
                }
            };

            // Column definitions with names
            const columnDefinitions = [
                { index: 0, name: 'Checkbox', visible: true },
                { index: 1, name: 'Frequency', visible: true },
                { index: 2, name: 'Salary Structure Name', visible: true },
                { index: 3, name: 'Status', visible: true },
                { index: 4, name: 'Details', visible: true },
                { index: 5, name: 'Action', visible: true }
            ];

            // Page identifier for column preferences
            const pageIdentifier = 'salary_settings';

            // Load column visibility preferences from database
            function loadColumnPreferences(callback) {
                $.ajax({
                    url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug")]) }}',
                    type: 'GET',
                    data: {
                        page_identifier: pageIdentifier
                    },
                    async: false, // Make synchronous to ensure preferences are loaded before table initialization
                    success: function(response) {
                        if (response.success && response.data && Array.isArray(response.data) && response.data.length > 0) {
                            try {
                                const preferences = response.data;
                                preferences.forEach(pref => {
                                    // Convert string index to number
                                    const prefIndex = parseInt(pref.index);
                                    const col = columnDefinitions.find(c => c.index === prefIndex);
                                    if (col) {
                                        // Handle both boolean and string values ("true"/"false")
                                        if (typeof pref.visible === 'string') {
                                            col.visible = pref.visible === 'true' || pref.visible === '1';
                                        } else {
                                            col.visible = pref.visible !== undefined ? Boolean(pref.visible) : true;
                                        }
                                    }
                                });
                                if (callback) callback();
                            } catch (e) {
                                console.error('Error loading column preferences:', e);
                                // Fallback: show all columns
                                columnDefinitions.forEach(col => col.visible = true);
                                if (callback) callback();
                            }
                        } else {
                            // No preferences found or empty response - show all columns by default
                            columnDefinitions.forEach(col => col.visible = true);
                            if (callback) callback();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching column preferences:', error);
                        // Fallback: show all columns on error
                        columnDefinitions.forEach(col => col.visible = true);
                        if (callback) callback();
                    }
                });
            }

            // Save column visibility preferences to database
            function saveColumnPreferences() {
                $.ajax({
                    url: '{{ route("settings.column.preferences.save", ["box_slug" => request()->route("box_slug")]) }}',
                    type: 'POST',
                    data: {
                        page_identifier: pageIdentifier,
                        column_preferences: columnDefinitions,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('Column preferences saved successfully');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error saving column preferences:', error);
                    }
                });
            }

            // Apply column visibility (without triggering drawCallback)
            function applyColumnVisibility() {
                if (typeof salaryTable !== 'undefined' && salaryTable && $.fn.DataTable.isDataTable('#SalarySettings')) {
                    columnDefinitions.forEach(col => {
                        try {
                            salaryTable.column(col.index).visible(col.visible, false);
                        } catch (e) {
                            console.error('Error applying column visibility:', e);
                        }
                    });
                    // Only adjust columns, don't redraw to avoid infinite loop
                    salaryTable.columns.adjust();
                }
            }

            // Populate columns modal
            function populateColumnsModal() {
                const columnsList = $('#columnsList');
                columnsList.empty();

                columnDefinitions.forEach(col => {
                    const listItem = $(`
                        <div class="list-group-item">
                            <div class="form-check">
                                <input class="form-check-input column-toggle" type="checkbox"
                                       data-column-index="${col.index}"
                                       id="col_${col.index}"
                                       ${col.visible ? 'checked' : ''}>
                                <label class="form-check-label" for="col_${col.index}">
                                    ${col.name}
                                </label>
                            </div>
                        </div>
                    `);
                    columnsList.append(listItem);
                });
            }

            // Load preferences on page load
            // Load preferences BEFORE table initialization
            loadColumnPreferences(function() {
                // Preferences loaded, now initialize table
                listTable();
            });

            function listTable() {
                if ($.fn.DataTable.isDataTable('#SalarySettings')) {
                    salaryTable.ajax.reload(null, false);
                } else {
                    salaryTable = $('#SalarySettings').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: "{{ route('salary-settings.list', ['box_slug' => request()->route('box_slug')]) }}",
                        columns: [
                            { data: 'id', render: (data) => `<div class="form-check form-check-md"><input class="form-check-input" type="checkbox" value="${data}"/></div>`, orderable: false, visible: Boolean(columnDefinitions[0] && columnDefinitions[0].visible) },
                            { data: 'frequency', render: (data) => `<span class="text-capitalize">${data}</span>`, visible: Boolean(columnDefinitions[1] && columnDefinitions[1].visible) },
                            { data: 'salary_structure_name', visible: Boolean(columnDefinitions[2] && columnDefinitions[2].visible) },
                            { data: 'status_label', orderable: false, visible: Boolean(columnDefinitions[3] && columnDefinitions[3].visible) },
                            {
                                data: null,
                                render: () => `<button class="btn btn-sm btn-outline-info view-breakdown"><i class="ti ti-eye"></i> View</button>`,
                                orderable: false,
                                visible: Boolean(columnDefinitions[4] && columnDefinitions[4].visible)
                            },
                            { data: null, render: (data, type, row) => `
                                <button class="btn btn-sm btn-primary editSalary me-1" data-id="${row.id}"><i class="ti ti-edit"></i></button>
                                <button class="btn btn-sm btn-danger deleteSalary" data-id="${row.id}"><i class="ti ti-trash"></i></button>`,
                                orderable: false,
                                visible: Boolean(columnDefinitions[5] && columnDefinitions[5].visible)
                            }
                        ],
                        order: [[1, 'asc']],
                        drawCallback: function() {
                            // Note: Don't call applyColumnVisibility here to avoid infinite loop
                            $('.editSalary').off('click').on('click', function() { fetchSalary($(this).data('id')); });
                            $('.deleteSalary').off('click').on('click', function() {
                                deleteId = $(this).data('id');
                                deleteStep = 1;
                                deleteCheckData = null;

                                // Reset modal
                                $('#delete_step_1').show();
                                $('#delete_step_2').hide();
                                $('#delete_step_3').hide();
                                $('#confirm_data_delete').prop('checked', false);
                                $('input[name="delete_option"]').prop('checked', false);
                                $('#delete_option_remove').prop('checked', true);

                                // Check for custom fields before showing modal
                                $.post("{{ route('salary-settings.check-delete', ['box_slug' => request()->route('box_slug')]) }}", {
                                    _token: '{{ csrf_token() }}',
                                    id: deleteId
                                })
                                .done(function(res) {
                                    if (res.success) {
                                        $('#delete_salary_name').text(res.salary_structure_name);

                                        if (res.has_custom_fields) {
                                            // Show custom fields warning
                                            let fieldsList = '';
                                            res.custom_fields.forEach(function(field) {
                                                fieldsList += '<li><strong>' + field.name + '</strong> (' + field.col_name + ')</li>';
                                            });
                                            $('#custom_fields_list').html(fieldsList);
                                            deleteCheckData = res;
                                        }

                                        $('#delete_modal').modal('show');
                                    }
                                })
                                .fail(displayErrors);
                            });

                            $('.view-breakdown').off('click').on('click', function() {
                                const rowData = salaryTable.row($(this).closest('tr')).data();

                                // Show loading state
                                $('#salaryBreakdownContent').html(`
                                    <div class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Loading salary structure details...</p>
                                    </div>
                                `);
                                $('#viewSalaryModal').modal('show');

                                // Ensure custom fields are loaded first (same as edit form)
                                function loadAndDisplayBreakdown() {
                                    // Fetch full details including custom fields using edit route (same as edit form)
                                    $.post("{{ route('salary-settings.edit', ['box_slug' => request()->route('box_slug')]) }}", {
                                        _token: '{{ csrf_token() }}',
                                        id: rowData.id
                                    })
                                    .done(function(response) {
                                        // Handle both direct data and wrapped response (same as fetchSalary)
                                        let data = response;
                                        if (response && response.success && response.data) {
                                            data = response.data;
                                        }

                                        // Merge rowData with fetched data to get all custom fields
                                        const mergedData = { ...rowData, ...data };
                                        const breakdownHtml = formatBreakdown(mergedData);
                                        $('#salaryBreakdownContent').html(breakdownHtml);
                                    })
                                    .fail(function(xhr) {
                                        console.error('Error fetching salary details:', xhr);
                                        $('#salaryBreakdownContent').html(`
                                            <div class="alert alert-danger">
                                                <i class="ti ti-alert-circle me-2"></i>
                                                <strong>Error:</strong> Failed to load salary structure details. Please try again.
                                            </div>
                                        `);
                                    });
                                }

                                // Load custom fields if not already loaded, then display breakdown
                                if (globalCustomFields.length === 0) {
                                    $.get("{{ route('app.settings.custom_fields.salary', ['box_slug' => request()->route('box_slug')]) }}")
                                        .done(function(res) {
                                            if (res.success && res.data) {
                                                globalCustomFields = res.data;
                                            }
                                            loadAndDisplayBreakdown();
                                        })
                                        .fail(function() {
                                            // Continue even if custom fields fail to load
                                            loadAndDisplayBreakdown();
                                        });
                                } else {
                                    loadAndDisplayBreakdown();
                                }
                            });
                        }
                    });
                }
            }

            function formatBreakdown(data) {
                const types = typeof data.component_types === 'string' ? JSON.parse(data.component_types) : (data.component_types || {});

                let html = '<div class="section-to-print">';

                // Header Section with Structure Info
                html += '<div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">';
                html += '<div class="card-body p-4">';
                html += '<div class="row align-items-center">';
                html += '<div class="col-md-8">';
                html += '<h4 class="mb-2 fw-bold"><i class="ti ti-file-text me-2"></i>' + (data.salary_structure_name || 'N/A') + '</h4>';
                html += '<div class="d-flex flex-wrap gap-3 mt-3">';
                html += '<div><i class="ti ti-calendar me-2"></i><strong>Frequency:</strong> <span class="text-capitalize">' + (data.frequency || 'N/A') + '</span></div>';
                if (data.monthly_option) {
                    html += '<div><i class="ti ti-settings me-2"></i><strong>Monthly Option:</strong> <span class="text-capitalize">' + data.monthly_option + '</span></div>';
                }
                if (data.status !== undefined) {
                    html += '<div>' + (data.status ? '<span class="badge bg-success bg-opacity-75"><i class="ti ti-check me-1"></i>Active</span>' : '<span class="badge bg-danger bg-opacity-75"><i class="ti ti-x me-1"></i>Inactive</span>') + '</div>';
                }
                html += '</div>';
                html += '</div>';
                html += '<div class="col-md-4 text-md-end">';
                html += '<div class="bg-white bg-opacity-20 rounded p-3">';
                html += '<div class="small text-white-50 mb-1">Total Components</div>';
                html += '<div class="h3 mb-0 fw-bold">' + Object.keys(data).filter(k => !k.includes('_type') && !k.includes('_max') && !['id', 'frequency', 'salary_structure_name', 'monthly_option', 'status', 'component_types', 'box_id', 'box_user_id', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'].includes(k) && data[k] !== null && data[k] !== undefined && data[k] !== '' && data[k] != 0).length + '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';

                // Process each section - Show all in one scrollable view
                const sectionOrder = ['earnings', 'reimbursements', 'contributions', 'deductions', 'onetime', 'variable'];
                const sectionColors = {
                    'earnings': { bg: 'bg-success bg-opacity-10', border: 'border-success', icon: currencyCode === 'INR' ? 'ti-currency-rupee' : 'ti-coins', color: 'text-success' },
                    'reimbursements': { bg: 'bg-info bg-opacity-10', border: 'border-info', icon: 'ti-receipt', color: 'text-info' },
                    'contributions': { bg: 'bg-primary bg-opacity-10', border: 'border-primary', icon: 'ti-building', color: 'text-primary' },
                    'deductions': { bg: 'bg-danger bg-opacity-10', border: 'border-danger', icon: 'ti-minus', color: 'text-danger' },
                    'onetime': { bg: 'bg-warning bg-opacity-10', border: 'border-warning', icon: 'ti-gift', color: 'text-warning' },
                    'variable': { bg: 'bg-purple bg-opacity-10', border: 'border-purple', icon: 'ti-chart-line', color: 'text-purple' }
                };

                sectionOrder.forEach(sectionKey => {
                    const fields = sections[sectionKey] || {};
                    let sectionTitle = sectionKey.charAt(0).toUpperCase() + sectionKey.slice(1);
                    if (sectionKey === 'onetime') {
                        sectionTitle = 'One-Time';
                    }

                    const sectionStyle = sectionColors[sectionKey] || { bg: 'bg-light', border: 'border-secondary', icon: 'ti-file', color: 'text-secondary' };

                    html += '<div class="card border-0 shadow-sm mb-4 ' + sectionStyle.border + ' border-start border-4">';
                    html += '<div class="card-header ' + sectionStyle.bg + ' border-0 py-3">';
                    html += '<h5 class="mb-0 fw-bold ' + sectionStyle.color + '" style="font-size: 1.4rem; font-weight: 700;">';
                    html += '<i class="ti ' + sectionStyle.icon + ' me-2"></i>' + sectionTitle + ' Components';
                    html += '</h5>';
                    html += '</div>';
                    html += '<div class="card-body p-4">';

                    let hasData = false;
                    let sectionHtml = '<div class="row g-3">';

                    // Standard fields - only show if they have values
                    Object.keys(fields).forEach(field => {
                        const val = data[field];
                        // Handle variable_label (text field) differently
                        if (field === 'variable_label') {
                            if (val !== null && val !== undefined && val !== '') {
                                hasData = true;
                                sectionHtml += '<div class="col-md-6 col-lg-4">';
                                sectionHtml += '<div class="p-3 bg-light rounded border">';
                                sectionHtml += '<div class="small text-muted mb-1">' + fields[field] + '</div>';
                                sectionHtml += '<div class="fw-bold fs-6">' + val + '</div>';
                                sectionHtml += '</div>';
                                sectionHtml += '</div>';
                            }
                        } else if (val !== null && val !== undefined && val !== '' && val != 0) {
                            hasData = true;
                            const type = (types && types[field] === 'percentage') ? '%' : currencySymbol;
                            const maxVal = data[field + '_max'];
                            const maxDisplay = (maxVal && maxVal > 0 && types && types[field] === 'percentage')
                                ? ` <small class="text-muted">(Max: ${currencySymbol}${parseFloat(maxVal).toLocaleString('en-IN')})</small>` : '';

                            sectionHtml += '<div class="col-md-6 col-lg-4">';
                            sectionHtml += '<div class="p-3 bg-light rounded border h-100">';
                            sectionHtml += '<div class="text-muted mb-2 fw-semibold" style="font-size: 0.95rem; font-weight: 600;">' + fields[field] + '</div>';
                            sectionHtml += '<div class="fw-bold ' + sectionStyle.color + '" style="font-size: 1.3rem; line-height: 1.5; font-weight: 700;">' + type + parseFloat(val).toLocaleString('en-IN') + maxDisplay + '</div>';
                            sectionHtml += '</div>';
                            sectionHtml += '</div>';
                        }
                    });

                    // Custom fields for this section - only show if they have values
                    const sectionCustomFields = (globalCustomFields && globalCustomFields.length > 0)
                        ? globalCustomFields.filter(f => f.object === 'salary_' + sectionKey && f.status === 'Enabled')
                        : [];

                    sectionCustomFields.forEach(field => {
                        const val = data[field.col_name];
                        if (val !== null && val !== undefined && val !== '' && val != 0) {
                            hasData = true;
                            const type = (types && types[field.col_name] === 'percentage') ? '%' : (field.type === 'number' ? currencySymbol : '');
                            const maxVal = data[field.col_name + '_max'];
                            const maxDisplay = (maxVal && maxVal > 0 && types && types[field.col_name] === 'percentage')
                                ? ` <small class="text-muted">(Max: ${currencySymbol}${parseFloat(maxVal).toLocaleString('en-IN')})</small>` : '';

                            sectionHtml += '<div class="col-md-6 col-lg-4">';
                            sectionHtml += '<div class="p-3 bg-light rounded border h-100 position-relative">';
                            sectionHtml += '<span class="badge bg-info position-absolute top-0 end-0 m-2">Custom</span>';
                            sectionHtml += '<div class="text-muted mb-2 fw-semibold" style="font-size: 0.95rem; font-weight: 600;">' + field.name + '</div>';
                            sectionHtml += '<div class="fw-bold ' + sectionStyle.color + '" style="font-size: 1.3rem; line-height: 1.5; font-weight: 700;">' + (field.type === 'number' ? type + parseFloat(val).toLocaleString('en-IN') : val) + maxDisplay + '</div>';
                            sectionHtml += '</div>';
                            sectionHtml += '</div>';
                        }
                    });

                    sectionHtml += '</div>';

                    // If no data in this section, show message
                    if (!hasData) {
                        sectionHtml = '<div class="text-center py-4">';
                        sectionHtml += '<div class="text-muted">';
                        sectionHtml += '<i class="ti ti-info-circle fs-1 mb-3 d-block"></i>';
                        sectionHtml += '<p class="mb-0">No components configured for this section.</p>';
                        sectionHtml += '</div>';
                        sectionHtml += '</div>';
                    }

                    html += sectionHtml;
                    html += '</div>'; // End card-body
                    html += '</div>'; // End card
                });

                html += '</div>'; // End section-to-print

                return html;
            }

            $(document).on('click', '.print-section-modal', function() {
                const content = $('#salaryBreakdownContent').clone();
                const printWindow = window.open('', '_blank');
                printWindow.document.write('<html><head><title>Salary Breakdown</title>');
                printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
                printWindow.document.write('<style>body{padding:40px;} .card{margin-bottom:20px; border:1px solid #eee; break-inside: avoid;} .card-header{background:#f8f9fa; font-weight:bold;} .bg-soft-primary{background-color: #e7f1ff;} .bg-soft-secondary{background-color: #f0f2f5;} .border-dashed{border-bottom: 1px dashed #eee !important;} .text-capitalize{text-transform:capitalize;}</style>');
                printWindow.document.write('</head><body>');
                printWindow.document.write('<div class="text-center mb-4"><h2>Salary Structure Report</h2><hr></div>');
                printWindow.document.write(content.html());
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 500);
            });

            // Note: Column visibility is already set during DataTable initialization via visible: columnDefinitions[X].visible
            // listTable() is already called in loadColumnPreferences callback, so no need to call it again here
            // No need to call applyColumnVisibility here as it would be redundant

            // Open Manage Columns Modal
            $('#manageColumnsModal').on('show.bs.modal', function() {
                populateColumnsModal();
            });

            // Show All Columns
            $('#showAllColumns').on('click', function() {
                $('.column-toggle').prop('checked', true);
            });

            // Hide All Columns
            $('#hideAllColumns').on('click', function() {
                $('.column-toggle').prop('checked', false);
            });

            // Apply Column Changes
            $('#applyColumnChanges').on('click', function() {
                $('.column-toggle').each(function() {
                    const columnIndex = parseInt($(this).data('column-index'));
                    const isVisible = $(this).is(':checked');
                    const col = columnDefinitions.find(c => c.index === columnIndex);
                    if (col) {
                        col.visible = isVisible;
                    }
                });

                saveColumnPreferences();
                
                // Apply column visibility and redraw table immediately
                if (salaryTable && $.fn.DataTable.isDataTable('#SalarySettings')) {
                    columnDefinitions.forEach(col => {
                        try {
                            salaryTable.column(col.index).visible(col.visible, false);
                        } catch (e) {
                            console.error('Error applying column visibility:', e);
                        }
                    });
                    salaryTable.columns.adjust();
                    // Redraw only once after all changes
                    salaryTable.draw(false);
                }

                // Close modal
                $('#manageColumnsModal').modal('hide');
            });

            // Next/Previous Tab Navigation
            $('.next-tab').on('click', function() {
                const $modal = $($(this).data('modal'));
                const $activeTab = $modal.find('.nav-link.active');
                const $nextTab = $activeTab.parent().next().find('.nav-link');
                if ($nextTab.length) {
                    const tab = new bootstrap.Tab($nextTab[0]);
                    tab.show();
                }
            });

            $('.prev-tab').on('click', function() {
                const $modal = $($(this).data('modal'));
                const $activeTab = $modal.find('.nav-link.active');
                const $prevTab = $activeTab.parent().prev().find('.nav-link');
                if ($prevTab.length) {
                    const tab = new bootstrap.Tab($prevTab[0]);
                    tab.show();
                }
            });

            // Global Type Toggle Logic
            $(document).on('change', '.global-type-toggle', function() {
                const $modal = $(this).closest('.modal');
                const isPercentage = $(this).is(':checked');
                const $selects = $modal.find('.component-type-select');

                if (isPercentage) {
                    $selects.val('percentage').prop('disabled', true);
                    // Also update any already selected values in the UI for clarity
                    $modal.find('.component-type-select').val('percentage');
                    // Show all max amount fields
                    $modal.find('.max-amount-field').slideDown();
                } else {
                    $selects.prop('disabled', false);
                    // Hide all max amount fields
                    $modal.find('.max-amount-field').slideUp();
                }
            });

            // Show/Hide Max Amount Field when percentage is selected
            $(document).on('change', '.component-type-select', function() {
                const $select = $(this);
                const $fieldContainer = getFieldContainer($select);

                // Check if container was found
                if ($fieldContainer.length === 0) {
                    console.warn('Field container not found for:', $select.attr('name'));
                    return;
                }

                const $maxAmountField = $fieldContainer.find('.max-amount-field');
                const $maxAmountInput = $maxAmountField.find('input');
                const $input = $fieldContainer.find('.percentage-input, input[type="number"]');
                const $errorDiv = $fieldContainer.find('.percentage-error');
                const selectedValue = $select.val();

                // Get the field name to identify which field is being changed
                const fieldName = $select.attr('name').replace('_type', '');

                if (selectedValue === 'percentage') {
                    // Show max amount field
                    if ($maxAmountField.length) {
                        $maxAmountField.slideDown(200);
                    } else {
                        // If max amount field doesn't exist, show it immediately
                        $maxAmountField.show();
                    }
                    // Set max attribute for percentage validation
                    if ($input.length) {
                        $input.attr('max', '100');
                        // Validate current value immediately
                        validatePercentage($input, $errorDiv);
                    }
                } else {
                    // When switching from percentage to fixed
                    if ($maxAmountField.length) {
                        $maxAmountField.slideUp(200);
                    } else {
                        $maxAmountField.hide();
                    }
                    // Clear max amount value (set to empty/null) when switching to fixed
                    if ($maxAmountInput.length) {
                        $maxAmountInput.val('');
                    }
                    // Also clear any existing max value in the form data
                    // This ensures it will be saved as null/0 in the database
                    const $maxField = $fieldContainer.find(`input[name="${fieldName}_max"]`);
                    if ($maxField.length) {
                        $maxField.val('');
                    }
                    // Remove max constraint for fixed amounts
                    if ($input.length) {
                        $input.removeAttr('max');
                    }
                    if ($errorDiv.length) {
                        $errorDiv.hide();
                    }
                    if ($input.length) {
                        $input.removeClass('is-invalid');
                    }
                }
            });

            // Validate percentage input on change - Live validation
            function validatePercentage($input, $errorDiv) {
                const value = parseFloat($input.val());

                // Check if value is empty or NaN
                if (isNaN(value) || $input.val() === '' || $input.val() === null) {
                    $input.removeClass('is-invalid');
                    $errorDiv.hide();
                    return true; // Allow empty values
                }

                if (value > 100) {
                    $input.addClass('is-invalid');
                    $errorDiv.text('Percentage cannot exceed 100%').show();
                    return false;
                } else if (value < 0) {
                    $input.addClass('is-invalid');
                    $errorDiv.text('Percentage cannot be negative').show();
                    return false;
                } else {
                    $input.removeClass('is-invalid');
                    $errorDiv.hide();
                    return true;
                }
            }

            // Helper function to find field container (handles both col-md-4 and col-md-6)
            function getFieldContainer($input) {
                // First check for custom-field-row (custom fields)
                let $container = $input.closest('.custom-field-row');
                if ($container.length > 0) {
                    return $container;
                }

                // Try col-md-4 first (standard fields and custom fields)
                $container = $input.closest('.col-md-4');
                if ($container.length > 0) {
                    return $container;
                }

                // Try col-md-6 (variable field)
                $container = $input.closest('.col-md-6');
                if ($container.length > 0) {
                    return $container;
                }

                // Fallback to parent mb-3 div
                $container = $input.closest('.mb-3');
                if ($container.length > 0) {
                    return $container;
                }

                // Last resort: find parent div with the field structure
                $container = $input.parent().parent();
                return $container;
            }

            // Live validation on input (as user types) - Real-time validation
            $(document).on('input keyup paste change', '.percentage-input', function() {
                const $input = $(this);
                const $fieldContainer = getFieldContainer($input);
                const $select = $fieldContainer.find('.component-type-select');
                const $errorDiv = $fieldContainer.find('.percentage-error');

                if ($select.length > 0 && $select.val() === 'percentage') {
                    validatePercentage($input, $errorDiv);
                } else {
                    // For fixed amounts, just remove any validation errors
                    $input.removeClass('is-invalid');
                    $errorDiv.hide();
                }
            });

            // Validate on blur (when user leaves the field)
            $(document).on('blur', '.percentage-input', function() {
                const $input = $(this);
                const $fieldContainer = getFieldContainer($input);
                const $select = $fieldContainer.find('.component-type-select');
                const $errorDiv = $fieldContainer.find('.percentage-error');

                if ($select.length > 0 && $select.val() === 'percentage') {
                    validatePercentage($input, $errorDiv);
                }
            });

            // Prevent form submission if there are validation errors
            $('#addSalaryFormCustom, #editSalaryForm').on('submit', function(e) {
                let hasErrors = false;
                let firstErrorField = null;

                $(this).find('.percentage-input').each(function() {
                    const $input = $(this);
                    const $fieldContainer = getFieldContainer($input);
                    const $select = $fieldContainer.find('.component-type-select');
                    const $errorDiv = $fieldContainer.find('.percentage-error');

                    if ($select.length > 0 && $select.val() === 'percentage') {
                        if (!validatePercentage($input, $errorDiv)) {
                            hasErrors = true;
                            if (!firstErrorField) {
                                firstErrorField = $input;
                            }
                        }
                    }
                });

                if (hasErrors) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (firstErrorField) {
                        firstErrorField.focus();
                        // Scroll to the error field
                        $('html, body').animate({
                            scrollTop: firstErrorField.offset().top - 100
                        }, 500);
                    }
                    alert('Please fix the validation errors before submitting. Percentage values cannot exceed 100%.');
                    return false;
                }
            });

            // Initialize max amount fields visibility on page load
            function initializeMaxAmountFields() {
                $('.component-type-select').each(function() {
                    const $select = $(this);
                    const $fieldContainer = getFieldContainer($select);
                    if ($fieldContainer.length === 0) return; // Skip if container not found

                    const $maxAmountField = $fieldContainer.find('.max-amount-field');
                    const $input = $fieldContainer.find('.percentage-input');
                    const $errorDiv = $fieldContainer.find('.percentage-error');
                    const selectedValue = $select.val();

                    if (selectedValue === 'percentage') {
                        $maxAmountField.show(); // Use show() instead of slideDown for immediate display
                        // Set max attribute for percentage validation
                        $input.attr('max', '100');
                        // Validate current value
                        if ($input.length) {
                            validatePercentage($input, $errorDiv);
                        }
                    } else {
                        $maxAmountField.hide(); // Use hide() instead of slideUp for immediate hide
                        // Remove max constraint for fixed amounts
                        $input.removeAttr('max');
                        if ($errorDiv.length) {
                            $errorDiv.hide();
                        }
                        $input.removeClass('is-invalid');
                    }
                });
            }

            // Initialize on modal open
            $('#add_salary_setting').on('shown.bs.modal', function() {
                // Ensure salary structure name field is empty with placeholder
                $('#add_salary_setting input[name="salary_structure_name"]').val('').attr('placeholder', 'Enter Salary Structure Name');
                // Clear any previous data
                window.currentSalaryData = null;
                // Reload custom fields to ensure they're up to date
                loadCustomFields();
                initializeMaxAmountFields();
            });

            $('#editSalaryModal').on('shown.bs.modal', function() {
                initializeMaxAmountFields();
            });

            function handleMonthlyOptions($modal) {
                const $select = $modal.find('select[name="frequency"]');
                const $options = $modal.find('.monthly-options-area');
                const $range = $modal.find('.specific-date-range');

                $select.on('change', function() {
                    if ($(this).val() === 'monthly') $options.removeClass('d-none');
                    else { $options.addClass('d-none'); $range.addClass('d-none'); }
                });

                $modal.find('input[name="monthly_option"]').on('change', function() {
                    if ($(this).val() === 'specific') $range.removeClass('d-none');
                    else $range.addClass('d-none');
                });
            }
            handleMonthlyOptions($('#add_salary_setting'));
            handleMonthlyOptions($('#editSalaryModal'));

            function fetchSalary(id) {
                $.post("{{ route('salary-settings.edit', ['box_slug' => request()->route('box_slug')]) }}", { _token: '{{ csrf_token() }}', id: id })
                    .done(function(response) {
                        // Handle both direct data and wrapped response
                        let data = response;
                        if (response && response.success && response.data) {
                            data = response.data;
                        }

                        // Store data globally for custom fields to access max amounts
                        window.currentSalaryData = data;

                        const $form = $('#editSalaryForm');
                        const $modal = $('#editSalaryModal');
                        $form[0].reset();
                        $form.find('input[name="id"]').val(data.id);
                        $form.find('select[name="frequency"]').val(data.frequency).trigger('change');
                        $form.find('input[name="salary_structure_name"]').val(data.salary_structure_name);
                        $form.find('input[name="status"]').prop('checked', data.status == 1);

                        // Reset global toggle
                        $modal.find('.global-type-toggle').prop('checked', false).trigger('change');

                        // Ensure custom fields are rendered before populating
                        // Load custom fields and wait for them to render, then populate
                        $.get("{{ route('app.settings.custom_fields.salary', ['box_slug' => request()->route('box_slug')]) }}")
                            .done(res => {
                                if (res.success) {
                                    globalCustomFields = res.data;
                                    renderCustomFields(res.data);
                                    // Wait a bit for custom fields to be fully rendered in DOM
                                    setTimeout(() => {
                                        // Now populate all fields including custom fields
                                        populateFormFields($form, data);
                                    }, 200);
                                } else {
                                    populateFormFields($form, data);
                                }
                            })
                            .fail(() => {
                                populateFormFields($form, data);
                            });
                    }).fail(function(xhr) {
                        console.error('Error fetching salary data:', xhr);
                        displayErrors(xhr);
                    });
            }

            function populateFormFields($form, data) {

                        // Populate all other fields
                        Object.keys(data).forEach(key => {
                            if (key === 'component_types' && data[key]) {
                                try {
                                    const types = typeof data[key] === 'string' ? JSON.parse(data[key]) : data[key];
                                    Object.keys(types).forEach(field => {
                                const $typeSelect = $form.find(`select[name="${field}_type"]`);
                                if ($typeSelect.length) {
                                    $typeSelect.val(types[field]);
                                    // Small delay to ensure DOM is ready, then trigger change
                                    setTimeout(() => {
                                        $typeSelect.trigger('change');
                                    }, 50);
                                }
                                    });
                                } catch (e) { console.error('Error parsing component_types', e); }
                } else {
                        // Skip system fields
                        if (['frequency', 'status', 'id', 'monthly_option', 'comp_off_type', 'start_date', 'end_date', 'salary_structure_name', 'component_types', 'created_at', 'updated_at', 'box_id', 'box_user_id', 'created_by', 'updated_by'].includes(key)) {
                            return;
                        }

                        // Handle max amount fields
                        if (key.endsWith('_max')) {
                            const $maxField = $form.find(`input[name="${key}"]`);
                            if ($maxField.length) {
                                const value = data[key];
                                $maxField.val(value !== null && value !== undefined && value !== '' ? parseFloat(value) : '0');
                            }
                            return;
                        }

                        // Check if this is a custom field (not a standard field)
                        const $field = $form.find(`input[name="${key}"], select[name="${key}"]`);
                        if ($field.length) {
                            const value = data[key];
                            // Handle number fields - set to 0 if null/undefined, otherwise use the value
                            if ($field.attr('type') === 'number') {
                                $field.val(value !== null && value !== undefined && value !== '' ? parseFloat(value) : '0');
                            } else {
                                $field.val(value || '');
                            }

                            // If this is a custom field input, also check if we need to update its type select
                            // This ensures custom fields are properly populated
                            if ($field.hasClass('percentage-input') || $field.closest('.custom-field-row').length > 0) {
                                // This is likely a custom field - ensure its type is set correctly
                                const fieldBaseName = key.replace('_max', '').replace('_type', '');
                                if (data.component_types && data.component_types[fieldBaseName]) {
                                    const $typeSelect = $form.find(`select[name="${fieldBaseName}_type"]`);
                                    if ($typeSelect.length) {
                                        $typeSelect.val(data.component_types[fieldBaseName]);
                                        // Trigger change to show/hide max amount field
                                        setTimeout(() => {
                                            $typeSelect.trigger('change');
                                        }, 100);
                                    }
                                }
                            }
                                }
                            }
                        });

                // Initialize max amount fields after populating data
                // Use a longer timeout to ensure all fields are rendered and populated
                setTimeout(function() {
                    // First initialize all max amount fields
                    initializeMaxAmountFields();
                    // Then trigger change on all percentage selects to ensure max fields are shown
                    // This is especially important for custom fields
                    $form.find('.component-type-select').each(function() {
                        const $select = $(this);
                        if ($select.val() === 'percentage') {
                            // Trigger change to show max amount field
                            $select.trigger('change');
                        }
                    });
                }, 400);

                const $modal = $('#editSalaryModal');
                        if (data.frequency === 'monthly') {
                            $form.find(`input[name="monthly_option"][value="${data.monthly_option}"]`).prop('checked', true).trigger('change');
                            if (data.monthly_option === 'specific') {
                                $form.find('input[name="start_date"]').val(data.start_date);
                                $form.find('input[name="end_date"]').val(data.end_date);
                            }
                        }

                        if (data.comp_off_type) {
                            $form.find(`input[name="comp_off_type"][value="${data.comp_off_type}"]`).prop('checked', true);
                        }
                        $modal.modal('show');
            }

            $('#addSalaryFormCustom').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);

                // Ensure all fields are enabled and visible for submission
                // Set empty number fields to 0 to ensure they're included
                $form.find('input[type="number"]').each(function() {
                    const $input = $(this);
                    const value = $input.val();
                    // If empty, set to 0 so it gets submitted
                    if (value === '' || value === null || value === undefined) {
                        $input.val('0');
                    }
                    $input.prop('disabled', false);
                });

                // Enable disabled selects before serializing so they are included
                $form.find('.component-type-select').prop('disabled', false);

                // Collect all form data including hidden tabs
                // Build formData object manually to ensure all fields are included
                const monthlyOption = $form.find('input[name="monthly_option"]:checked').val() || '';
                const formData = {
                    _token: '{{ csrf_token() }}',
                    frequency: $form.find('select[name="frequency"]').val() || '',
                    salary_structure_name: $form.find('input[name="salary_structure_name"]').val() || '',
                    status: $form.find('input[name="status"]').is(':checked') ? 1 : 0,
                    monthly_option: monthlyOption,
                    comp_off_type: $form.find('input[name="comp_off_type"]:checked').val() || 'paid'
                };

                // Handle start_date and end_date based on monthly_option
                if (monthlyOption === 'specific') {
                    const startDay = $form.find('input[name="start_date"]').val();
                    const endDay = $form.find('input[name="end_date"]').val();
                    formData.start_date = startDay || null;
                    formData.end_date = endDay || null;
                } else {
                    formData.start_date = null;
                    formData.end_date = null;
                }

                // Explicitly collect all number inputs from ALL tabs (excluding start_date and end_date)
                $form.find('input[type="number"]').each(function() {
                    const $input = $(this);
                    const name = $input.attr('name');
                    // Skip start_date and end_date as they're handled separately above
                    if (name && name !== 'start_date' && name !== 'end_date') {
                        let value = $input.val();
                        // Convert empty/null/undefined to '0' for proper submission
                        if (value === '' || value === null || value === undefined || isNaN(value)) {
                            value = '0';
                        }
                        formData[name] = value;
                    }
                });

                // Collect all type selects from ALL tabs
                $form.find('.component-type-select').each(function() {
                    const $select = $(this);
                    const name = $select.attr('name');
                    if (name) {
                        formData[name] = $select.val() || 'fixed';
                    }
                });

                // Re-disable if global is checked
                if ($form.find('.global-type-toggle').is(':checked')) {
                    $form.find('.component-type-select').prop('disabled', true);
                }

                $.post("{{ route('salary-settings.store', ['box_slug' => request()->route('box_slug')]) }}", formData)
                    .done(res => { 
                        $('#add_salary_setting').modal('hide'); 
                        this.reset(); 
                        // Ensure salary structure name field is cleared and placeholder is visible
                        $('#add_salary_setting input[name="salary_structure_name"]').val('').attr('placeholder', 'Enter Salary Structure Name');
                        showToast(res.success); 
                        listTable(); 
                    })
                    .fail(displayErrors);
            });

            $('#editSalaryForm').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);

                // Ensure all fields are enabled and visible for submission
                // Set empty number fields to 0 to ensure they're included
                $form.find('input[type="number"]').each(function() {
                    const $input = $(this);
                    const value = $input.val();
                    // If empty, set to 0 so it gets submitted
                    if (value === '' || value === null || value === undefined) {
                        $input.val('0');
                    }
                    $input.prop('disabled', false);
                });

                // Enable all component type selects
                $form.find('.component-type-select').prop('disabled', false);

                // Collect all form data including hidden tabs
                // Build formData object manually to ensure all fields are included
                const monthlyOption = $form.find('input[name="monthly_option"]:checked').val() || '';
                const formData = {
                    _token: '{{ csrf_token() }}',
                    id: $form.find('input[name="id"]').val(),
                    frequency: $form.find('select[name="frequency"]').val() || '',
                    salary_structure_name: $form.find('input[name="salary_structure_name"]').val() || '',
                    status: $form.find('input[name="status"]').is(':checked') ? 1 : 0,
                    monthly_option: monthlyOption,
                    comp_off_type: $form.find('input[name="comp_off_type"]:checked').val() || 'paid'
                };

                // Handle start_date and end_date based on monthly_option
                if (monthlyOption === 'specific') {
                    const startDay = $form.find('input[name="start_date"]').val();
                    const endDay = $form.find('input[name="end_date"]').val();
                    formData.start_date = startDay || null;
                    formData.end_date = endDay || null;
                } else {
                    formData.start_date = null;
                    formData.end_date = null;
                }

                // Explicitly collect all number inputs from ALL tabs (excluding start_date and end_date)
                $form.find('input[type="number"]').each(function() {
                    const $input = $(this);
                    const name = $input.attr('name');
                    // Skip start_date and end_date as they're handled separately above
                    if (name && name !== 'start_date' && name !== 'end_date') {
                        let value = $input.val();
                        // Convert empty/null/undefined to '0' for proper submission
                        if (value === '' || value === null || value === undefined || isNaN(value)) {
                            value = '0';
                        }
                        formData[name] = value;
                    }
                });

                // Collect all type selects from ALL tabs
                $form.find('.component-type-select').each(function() {
                    const $select = $(this);
                    const name = $select.attr('name');
                    if (name) {
                        formData[name] = $select.val() || 'fixed';
                    }
                });

                if ($form.find('.global-type-toggle').is(':checked')) {
                    $form.find('.component-type-select').prop('disabled', true);
                }

                $.post("{{ route('salary-settings.update', ['box_slug' => request()->route('box_slug')]) }}", formData)
                    .done(res => {
                        $('#editSalaryModal').modal('hide');
                        showToast(res.success);
                        listTable();
                    })
                    .fail(displayErrors);
            });

            let deleteStep = 1;
            let deleteCheckData = null;

            // Handle confirm delete button
            $('#confirmDelete').on('click', function() {
                if (deleteStep === 1) {
                    // Step 1: Check if there are custom fields
                    if (deleteCheckData && deleteCheckData.has_custom_fields) {
                        // Move to step 2
                        deleteStep = 2;
                        $('#delete_step_1').hide();
                        $('#delete_step_2').show();
                        return;
                    } else {
                        // No custom fields, proceed with deletion
                        performDelete(false);
                    }
                } else if (deleteStep === 2) {
                    // Step 2: Check if there's data in custom fields
                    const deleteOption = $('input[name="delete_option"]:checked').val();

                    if (deleteCheckData && deleteCheckData.has_data) {
                        // Move to step 3 (data confirmation)
                        deleteStep = 3;
                        $('#delete_step_2').hide();
                        $('#delete_step_3').show();

                        // Show fields with data
                        let dataFieldsList = '';
                        deleteCheckData.custom_fields.forEach(function(field) {
                            if (field.has_value || field.has_max) {
                                dataFieldsList += '<li><strong>' + field.name + '</strong>';
                                if (field.has_value) dataFieldsList += ' (has value)';
                                if (field.has_max) dataFieldsList += ' (has max amount)';
                                dataFieldsList += '</li>';
                            }
                        });
                        $('#custom_fields_with_data').html(dataFieldsList);
                    } else {
                        // No data, proceed with deletion based on option
                        const forceDelete = (deleteOption === 'force');
                        performDelete(forceDelete);
                    }
                } else if (deleteStep === 3) {
                    // Step 3: Final confirmation
                    if (!$('#confirm_data_delete').is(':checked')) {
                        showToast('Please confirm that you understand the data will be permanently deleted', 'error');
                        return;
                    }

                    const deleteOption = $('input[name="delete_option"]:checked').val();
                    const forceDelete = (deleteOption === 'force');
                    performDelete(forceDelete);
                }
            });

            function performDelete(forceDelete) {
                const deleteData = {
                    _token: '{{ csrf_token() }}',
                    id: deleteId
                };

                if (forceDelete) {
                    deleteData.force_delete = 1;
                }

                $.post("{{ route('salary-settings.destroy', ['box_slug' => request()->route('box_slug')]) }}", deleteData)
                    .done(function(res) {
                        $('#delete_modal').modal('hide');
                        showToast(res.success);
                        listTable();
                        // Reset
                        deleteStep = 1;
                        deleteCheckData = null;
                    })
                    .fail(displayErrors);
            }

            // Custom Field Logic
            $(document).on('click', '.open-add-field', function() {
                const tab = $(this).data('tab');
                $('#target_tab_input').val(tab);

                // Default to 'number' for financial tabs
                const financialTabs = ['earnings', 'reimbursements', 'contributions', 'deductions', 'onetime', 'variable'];
                if (financialTabs.includes(tab)) {
                    $('#add_fields').find('select[name="type"]').val('number');
                } else {
                    $('#add_fields').find('select[name="type"]').val('text');
                }

                $('#add_fields').modal('show');
            });

            $('#customFieldForm').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                const label = $form.find('input[name="label"]').val();
                const tab = $('#target_tab_input').val();

                // Generate col_name
                const col_name = label.trim().toLowerCase()
                    .replace(/\s+/g, '_')
                    .replace(/[^\w]/g, '');

                const formData = {
                        _token: '{{ csrf_token() }}',
                    custom_type: 'salary_' + tab,
                    type: $form.find('select[name="type"]').val(),
                    name: label,
                    col_name: col_name,
                    default_value: $form.find('input[name="default_value"]').val(),
                    required: $form.find('input[name="required"]').is(':checked') ? 1 : 0,
                    status: $form.find('input[name="status"]').is(':checked') ? 'Enabled' : 'Disabled',
                    quick_edit: 1
                };

                $.post("{{ route('settings.custom_fields.store', ['box_slug' => request()->route('box_slug')]) }}", formData)
                    .done(res => {
                        $('#add_fields').modal('hide');
                        showToast('Custom field added successfully!');
                        $form[0].reset();
                        // Reload custom fields
                        loadCustomFields();
                    })
                    .fail(displayErrors);
            });

            let globalCustomFields = [];

            function loadCustomFields() {
                $.get("{{ route('app.settings.custom_fields.salary', ['box_slug' => request()->route('box_slug')]) }}")
                    .done(res => {
                        if (res.success) {
                            globalCustomFields = res.data;
                            renderCustomFields(res.data);
                        }
                    });
            }

            function renderCustomFields(fields) {
                // Clear existing custom fields first
                $('.custom-field-row').remove();

                fields.forEach(field => {
                    if (!field.object.startsWith('salary_')) return;

                    const tab = field.object.replace('salary_', '');
                    const $addContainer = $(`#add-${tab} .row`);
                    const $editContainer = $(`#edit-${tab} .row`);

                    // Render in both add and edit forms
                    [$addContainer, $editContainer].forEach($container => {
                    if ($container.length) {
                        let inputHtml = '';
                        const isPercentage = $('#addGlobalPercentage').is(':checked') || $('#editGlobalPercentage').is(':checked');
                        const isDisabledStatus = field.status === 'Disabled';
                        const disabledAttr = (isPercentage || isDisabledStatus) ? 'disabled' : '';
                        const typeVal = isPercentage ? 'percentage' : 'fixed';

                            const financialTabs = ['earnings', 'reimbursements', 'contributions', 'deductions', 'onetime', 'variable'];
                        const isFinancial = financialTabs.includes(tab);

                        if (field.type === 'number' || (isFinancial && field.type === 'text')) {
                                const maxFieldDisplay = typeVal === 'percentage' ? '' : 'style="display: none;"';
                                // Get max amount value if exists (from custom_fields_max_amounts JSON)
                                // This will be populated when editing - check if data has this field's max amount
                                let maxAmountValue = 0;
                                let fieldValue = field.default_value || 0;
                                let fieldTypeValue = typeVal;

                                // If editing, get values from currentSalaryData
                                if (typeof window.currentSalaryData !== 'undefined' && window.currentSalaryData) {
                                    if (window.currentSalaryData[field.col_name] !== undefined) {
                                        fieldValue = window.currentSalaryData[field.col_name] || 0;
                                    }
                                    if (window.currentSalaryData[field.col_name + '_max'] !== undefined) {
                                        maxAmountValue = window.currentSalaryData[field.col_name + '_max'] || 0;
                                    }
                                    // Get type from component_types
                                    const types = typeof window.currentSalaryData.component_types === 'string'
                                        ? JSON.parse(window.currentSalaryData.component_types)
                                        : window.currentSalaryData.component_types;
                                    if (types && types[field.col_name]) {
                                        fieldTypeValue = types[field.col_name];
                                    }
                                }

                            inputHtml = `
                                <div class="input-group">
                                        <input type="${field.type === 'number' ? 'number' : 'text'}" name="${field.col_name}" class="form-control percentage-input" value="${fieldValue}" ${isDisabledStatus ? 'disabled' : ''} step="0.01" min="0" max="100">
                                    <select name="${field.col_name}_type" class="form-select component-type-select" style="max-width: 80px;" ${disabledAttr}>
                                            <option value="fixed" ${fieldTypeValue === 'fixed' ? 'selected' : ''}>${currencySymbol}</option>
                                            <option value="percentage" ${fieldTypeValue === 'percentage' ? 'selected' : ''}>%</option>
                                    </select>
                                </div>
                                    <div class="max-amount-field mt-2" style="display: ${fieldTypeValue === 'percentage' ? 'block' : 'none'};">
                                        <label class="form-label small text-muted">Max Amount (${currencySymbol})</label>
                                        <input type="number" name="${field.col_name}_max" class="form-control form-control-sm" value="${maxAmountValue}" step="0.01" placeholder="Enter max amount">
                                    </div>
                                    <div class="invalid-feedback percentage-error" style="display: none;">Percentage cannot exceed 100%</div>
                            `;
                        } else if (field.type === 'textarea') {
                                let fieldValue = field.default_value || '';
                                if (typeof window.currentSalaryData !== 'undefined' && window.currentSalaryData && window.currentSalaryData[field.col_name] !== undefined) {
                                    fieldValue = window.currentSalaryData[field.col_name] || '';
                                }
                                inputHtml = `<textarea name="${field.col_name}" class="form-control" ${isDisabledStatus ? 'disabled' : ''}>${fieldValue}</textarea>`;
                        } else {
                                let fieldValue = field.default_value || '';
                                if (typeof window.currentSalaryData !== 'undefined' && window.currentSalaryData && window.currentSalaryData[field.col_name] !== undefined) {
                                    fieldValue = window.currentSalaryData[field.col_name] || '';
                                }
                                inputHtml = `<input type="${field.type}" name="${field.col_name}" class="form-control" value="${fieldValue}" ${isDisabledStatus ? 'disabled' : ''}>`;
                        }

                        const fieldHtml = `
                            <div class="col-md-4 mb-3 custom-field-row ${field.status === 'Disabled' ? 'opacity-50' : ''}" data-field-id="${field.id}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label mb-0">${field.name} ${field.required ? '<span class="text-danger">*</span>' : ''}</label>
                                    <div class="custom-field-actions">
                                        <a href="javascript:void(0);" class="text-primary me-2 toggle-field" data-id="${field.id}" title="${field.status === 'Enabled' ? 'Disable' : 'Enable'}">
                                            <i class="ti ${field.status === 'Enabled' ? 'ti-eye' : 'ti-eye-off'}"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="text-danger delete-custom-field" data-id="${field.id}" title="Delete Field">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                    </div>
                                </div>
                                ${inputHtml}
                            </div>
                        `;
                        $container.append(fieldHtml);
                    }
                });
                });

                // Initialize max amount fields visibility after rendering
                // This ensures custom fields max amount fields are shown if percentage is selected
                setTimeout(function() {
                    // First initialize all max amount fields (including custom fields)
                    initializeMaxAmountFields();
                    // Then explicitly trigger change events for ALL percentage selects (including custom fields)
                    // This ensures max amount fields are shown for custom fields when editing
                    $('.component-type-select').each(function() {
                        const $select = $(this);
                        const selectedValue = $select.val();
                        const $fieldContainer = getFieldContainer($select);
                        const $maxAmountField = $fieldContainer.find('.max-amount-field');

                        if (selectedValue === 'percentage') {
                            // Show max amount field immediately for percentage
                            if ($maxAmountField.length) {
                                $maxAmountField.show();
                            }
                            // Use a small delay to ensure DOM is ready, then trigger change
                            setTimeout(() => {
                                $select.trigger('change');
                            }, 50);
                        } else {
                            // Hide max amount field for fixed
                            if ($maxAmountField.length) {
                                $maxAmountField.hide();
                            }
                        }
                    });
                }, 250);
            }

            loadCustomFields();

            // Toggle Field Status
            $(document).on('click', '.toggle-field', function() {
                const id = $(this).data('id');
                const $btn = $(this);
                const $row = $btn.closest('.custom-field-row');

                $.post("{{ route('settings.custom.toggle', ['box_slug' => request()->route('box_slug')]) }}", {
                    _token: '{{ csrf_token() }}',
                    id: id
                }).done(res => {
                    if (res.success) {
                        const isEnabled = res.status === 'Enabled';
                        $btn.find('i').attr('class', 'ti ' + (isEnabled ? 'ti-eye' : 'ti-eye-off'));
                        $btn.attr('title', isEnabled ? 'Disable' : 'Enable');
                        if (isEnabled) {
                            $row.removeClass('opacity-50').find('input, select, textarea').prop('disabled', false);
                            // If global percentage is on, keep the type select disabled
                            if ($('#addGlobalPercentage').is(':checked') || $('#editGlobalPercentage').is(':checked')) {
                                $row.find('.component-type-select').prop('disabled', true);
                            }
                        } else {
                            $row.addClass('opacity-50').find('input, select, textarea').prop('disabled', true);
                        }
                        showToast(res.message);
                    }
                }).fail(displayErrors);
            });

            // Delete Custom Field with double confirmation
            $(document).on('click', '.delete-custom-field', function() {
                const id = $(this).data('id');
                if (confirm("Are you sure you want to remove this custom field? This will delete the database column as well.")) {
                    deleteField(id);
                }
            });

            function deleteField(id, force = false) {
                const params = {
                    _token: '{{ csrf_token() }}',
                    id: id
                };
                if (force) params.force_delete = 1;

                $.ajax({
                    url: "{{ route('settings.custom.delete', ['box_slug' => request()->route('box_slug')]) }}",
                    type: 'DELETE',
                    data: params,
                    success: function(res) {
                        if (res.success) {
                            showToast(res.message);
                            // Reload custom fields in both add and edit forms
                            loadCustomFields();
                            // If edit modal is open, reload the form to reflect deleted fields
                            if ($('#editSalaryModal').hasClass('show')) {
                                const salaryId = $('#editSalaryForm').find('input[name="id"]').val();
                                if (salaryId) {
                                    fetchSalary(salaryId);
                                }
                            }
                        } else if (res.has_data) {
                            if (confirm(res.message)) {
                                deleteField(id, true); // Second confirmation (force delete)
                            }
                        }
                    },
                    error: displayErrors
                });
            }

            // Auto-remove 0 on focus/hover
            $(document).on('focus mouseenter', '#add_salary_setting input, #editSalaryModal input', function() {
                if ($(this).val() == '0') {
                    $(this).val('');
                }
            });

            $(document).on('blur mouseleave', '#add_salary_setting input, #editSalaryModal input', function() {
                if ($(this).val() == '' && !$(this).is(':focus')) {
                    $(this).val('0');
                }
            });
        });
    </script>
@endpush
@endsection

