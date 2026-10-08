@extends('layouts.main')
@section('content')
@section('title', 'Employee Salary')

@php
    $earnings = [
        'basic' => 'Basic',
        'da' => 'DA',
        'hra' => 'HRA',
        'conveyance' => 'Conveyance',
        'allowance' => 'Allowance',
        'medical_allowance' => 'Medical Allowance',
        'special_allowance' => 'Special Allowance',
        'leave_travel_allowance' => 'Leave Travel Allowance',
        'city_compensatory_allowance' => 'City Compensatory Allowance',
        'sales_target_incentives' => 'Sales Incentives',
        'shift_allowance' => 'Shift Allowance',
        'transport_allowance' => 'Transport Allowance',
        'food_allowance' => 'Food Allowance',
        'overtime_allowance' => 'Overtime Allowance (OT)',
        'uniform_allowance' => 'Uniform Allowance',
        'mobile_internet_allowance' => 'Mobile / Internet Allowance',
        'project_allowance' => 'Project Allowance',
        'education_allowance' => 'Education Allowance',
        'professional_allowance' => 'Professional Allowance',
        'arrears_earnings' => 'Arrears (Earnings)',
        'others' => 'Others',
    ];
    $reimbursements = [
        'travel_reimbursement' => 'Travel Reimbursement',
        'fuel_reimbursement' => 'Petrol / Fuel Reimbursement',
        'telephone_reimbursement' => 'Telephone / Data Reimbursement',
        'driver_reimbursement' => 'Driver Salary Reimbursement',
        'medical_reimbursement' => 'Medical Reimbursement',
        'uniform_reimbursement' => 'Uniform / Equipment Reimbursement',
    ];
    $contributions = [
        'employer_pf' => 'Employer PF',
        'employer_esi' => 'Employer ESI',
        'gratuity' => 'Gratuity',
        'superannuation' => 'Superannuation',
        'employer_lwf' => 'Labor Welfare Fund (Employer)',
    ];
    $deductions = [
        'employee_pf' => 'Employee PF Contribution',
        'employee_esi' => 'Employee ESI Contribution',
        'professional_tax' => 'Professional Tax (PT)',
        'tds' => 'TDS',
        'salary_advance_recovery' => 'Salary Advance Recovery',
        'loan_emi_recovery' => 'Loan EMI Recovery',
        'canteen_deduction' => 'Food / Canteen Deduction',
        'attendance_loss' => 'Late Coming / Attendance Loss',
        'notice_period_recovery' => 'Notice Period Recovery',
        'arrears_recovery' => 'Arrears (Recovery)',
        'insurance_premium_employee' => 'Insurance Premium (Employee)',
    ];
    $onetime = [
        'joining_bonus' => 'Joining Bonus',
        'retention_bonus' => 'Retention Bonus',
        'performance_bonus' => 'Performance Bonus',
        'ex_gratia' => 'Ex-gratia',
        'full_final_settlement' => 'Full & Final Settlement',
        'leave_encashment' => 'Leave Encashment',
        'gratuity_payout' => 'Gratuity Payout',
        'severance_compensation' => 'Severance Compensation',
    ];
@endphp

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Employee Salary </h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="index.html"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            HR
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Employee Salary </li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">

                <div class="me-2 mb-2">
                    <div class="dropdown">
                        <a href="javascript:void(0);"
                            class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                            data-bs-toggle="dropdown">
                            <i class="ti ti-file-export me-1"></i>Export
                        </a>
                        <ul class="dropdown-menu  dropdown-menu-end p-3">
                            <li>
                                <a href="javascript:void(0);" id="exportPdfBtn" class="dropdown-item rounded-1"><i
                                        class="ti ti-file-type-pdf me-1"></i>Export as PDF </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" id="exportExcelBtn" class="dropdown-item rounded-1"><i
                                        class="ti ti-file-type-xls me-1"></i>Export as Excel </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mb-2">
                    <!--<a href="#" data-bs-toggle="modal" data-bs-target="#new-employee-salary"
                        class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add
                        Salary </a> -->
                    <a href="#" data-bs-toggle="offcanvas" data-bs-target="#new-employee-salary"
                        class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add
                        Salary </a>


                </div>
                <div class="head-icons ms-2">
                    <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-original-title="Collapse" id="collapse-header">
                        <i class="ti ti-chevrons-up"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- /Breadcrumb -->



        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                <h5>Employee Salary List </h5>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="me-3">
                        <div class="input-group">
                            <input type="text" id="globalSearch" class="form-control" placeholder="Search...">
                            <button class="btn btn-primary" type="button" id="searchBtn">
                                <i class="ti ti-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="me-3">
                        <button type="button" class="btn btn-white d-inline-flex align-items-center"
                            data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                            <i class="ti ti-columns me-1"></i>Manage Columns
                        </button>
                    </div>
                    <div class="me-3">
                        <button type="button" id="generateSlipTrigger" class="btn bg-soft-purple text-purple">
                            <i class="ti ti-square-rounded-plus me-2"></i>Generate Slip
                        </button>
                        <div class="form-check form-check-inline align-items-center ms-3">
                            <input class="form-check-input" type="checkbox" id="confirmGenerateSlip">
                            <label class="form-check-label fs-13 mb-0" for="confirmGenerateSlip">
                                I confirm salary slip generation
                            </label>
                        </div>
                    </div>
                    <div class="me-3">
                        <div class="input-icon-end position-relative">
                            <input type="text" class="form-control date-range bookingrange"
                                placeholder="dd/mm/yyyy - dd/mm/yyyy" />
                            <span class="input-icon-addon">
                                <i class="ti ti-chevron-down"></i>
                            </span>
                        </div>
                    </div>
                    <div class="dropdown me-3">
                        <a href="javascript:void(0);"
                            class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                            id="departmentFilterBtn" data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i>Department: All
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-3" id="departmentFilterMenu"
                            style="max-height: 300px; overflow-y: auto;">
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1 department-filter-item"
                                    data-id="">All Departments</a>
                            </li>
                            <!-- Dynamic departments will be loaded here -->
                        </ul>
                    </div>
                    <div class="dropdown me-3">
                        <a href="javascript:void(0);"
                            class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                            id="designationFilterBtn" data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i>Designation: All
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-3" id="designationFilterMenu"
                            style="max-height: 300px; overflow-y: auto;">
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1 designation-filter-item"
                                    data-id="">All Designations</a>
                            </li>
                            <!-- Dynamic designations will be loaded here -->
                        </ul>
                    </div>
                    <div class="dropdown me-3">
                        <a href="javascript:void(0);"
                            class="dropdown-toggle btn btn-white d-inline-flex align-items-center" id="sortFilterBtn"
                            data-bs-toggle="dropdown">
                            <i class="ti ti-arrow-up-down me-1"></i>Sort: All
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-3" id="sortFilterMenu">
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1 sort-filter-item"
                                    data-sort="">All</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1 sort-filter-item"
                                    data-sort="recent">Recent</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1 sort-filter-item"
                                    data-sort="descending">Descending</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1 sort-filter-item"
                                    data-sort="ascending">Ascending</a>
                            </li>
                        </ul>
                    </div>
                    <div class="me-3" id="activeFiltersContainer">
                        <!-- Active filters will be shown here as badges -->
                    </div>
                    <div class="me-3">
                        <button type="button" class="btn btn-white d-inline-flex align-items-center"
                            id="clearFiltersBtn">
                            <i class="ti ti-x me-1"></i>Clear All
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="custom-datatable-filter table-responsive">
                    <table class="table" id="salaryTable">
                        <thead class="thead-light">
                            <tr>
                                <th class="no-sort">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox" id="select-all" />
                                    </div>
                                </th>
                                <th>Emp ID </th>
                                <th>Name </th>
                                <th>Email </th>
                                <th>Phone </th>
                                <th>Designation </th>
                                <th>Joining Date </th>
                                <th>Salary </th>
                                <th>Final Salary </th>
                                <th>Created Date </th>
                                <th>Updated Date </th>
                                <th>Created By </th>
                                <th>Updated By </th>
                                <th>Payslip </th>
                                <th></th>

                            </tr>
                        </thead>
                        <tbody>


                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Manage Columns Modal -->
        <div class="modal fade" id="manageColumnsModal" tabindex="-1" aria-labelledby="manageColumnsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="manageColumnsModalLabel">Manage Columns</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="showAllColumns">Show
                                All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="hideAllColumns">Hide
                                All</button>
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

        <!-- Export Salary Modal -->
        <div class="modal fade" id="exportSalaryModal" tabindex="-1" aria-labelledby="exportSalaryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportSalaryModalLabel">Export Salary Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Month</label>
                                <select class="select form-control" id="export_month">
                                    <option value="">All Months</option>
                                    @foreach (range(1, 12) as $m)
                                        <option value="{{ $m }}">
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Year</label>
                                <select class="select form-control" id="export_year">
                                    <option value="">All Years</option>
                                    @foreach (range(now()->year - 2, now()->year + 1) as $y)
                                        <option value="{{ $y }}">
                                            {{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Fields to Export</label>
                            <div class="mb-2">
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    id="selectAllExportFields">Select All</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    id="deselectAllExportFields">Deselect All</button>
                            </div>
                            <div class="row" id="exportFieldsList">
                                <!-- Fields will be populated here -->
                            </div>
                        </div>
                        <input type="hidden" id="exportType" value="excel">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="confirmExportBtn">Export</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Generate Slip Modal -->
        <div class="modal fade" id="generateSlipModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Salary Slip</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Month</label>
                            <select class="select form-control" id="slip_month">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Year</label>
                            <select class="select form-control" id="slip_year">
                                @foreach (range(now()->year - 1, now()->year + 1) as $y)
                                    <option value="{{ $y }}" {{ now()->year == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-end">
                            <span id="selectedCount" class="text-muted me-2">0 employees selected</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="confirmGenerateSlipBtn">Generate</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    

</div>
<!-- /Page Wrapper -->


<script>
    const toggleButtons = document.querySelectorAll('.toggle-btn');

    toggleButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            toggleButtons.forEach(b => b.classList.remove('active')); // deactivate all
            btn.classList.add('active'); // activate clicked
        });
    });
</script>



<!--Add salary -->
<div class="offcanvas offcanvas-end offcanvas-large offcanvas-large-xl" tabindex="-1" id="new-employee-salary">
    <div class="offcanvas-header border-bottom">
        <h4 id="employeeSalaryFormTitle">Add Employee Salary </h4>
        <button type="button"
            class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
            data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <form id="employeeSalaryForm" method="POST">
            @csrf
            <input type="hidden" id="edit_salary_id" name="salary_id" value="">
            <div class="modal-body p-0" style="max-height: inherit; overflow:inherit;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Employee Name <span class="text-danger">*</span></label>
                                <select class="select" id="employeeSelect" name="box_user_id" required>
                                    <option value="">Select Employee</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="ctc_input">Enter Annual CTC <span
                                    class="text-danger">*</span></label>
                                <input type="number" id="ctc_input" name="gross_salary"
                                    placeholder="Enter Annual CTC Amount" class="form-control" required />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Salary Setting Template <span
                                        class="text-danger">*</span></label>
                                <select class="select" id="salary_setting_type" name="salary_settings_id" required>
                                    <option value="">Select Salary Setting</option>
                                </select>
                            </div>
                        </div>

                        <!-- Calculation Period and Calculate Button -->
                        <div class="col-md-12 mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Calculation Period</label>
                                    <div class="btn-group w-100" role="group">
                                        <button type="button"
                                            class="btn btn-outline-primary calculate-period-btn active"
                                            data-period="monthly">Monthly</button>
                                        {{-- <button type="button" class="btn btn-outline-primary calculate-period-btn"
                                            data-period="yearly">Yearly</button> --}}
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <button type="button" id="calculateSalaryBtn" class="btn btn-primary">
                                            <i class="ti ti-calculator me-1"></i>Calculate Salary
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-white border me-2" data-bs-dismiss="offcanvas">Cancel </button>
                <button type="submit" class="btn btn-primary" id="employeeSalaryFormSubmitBtn">Add Employee Salary
                </button>
            </div>
        </form>
    </div>
</div>
<!-- /Add salary -->


<!-- Success Modal -->
<div class="modal fade" id="salarySuccessModal" tabindex="-1" aria-labelledby="salarySuccessModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <span class="avatar avatar-lg avatar-rounded bg-success mb-3">
                        <i class="ti ti-check fs-24 text-white"></i>
                    </span>
                </div>
                <h5 class="mb-2">Salary Assigned Successfully</h5>
                <p class="mb-4 text-muted" id="successMessage">Employee salary has been assigned successfully.</p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container for Confirmation and Messages -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    <div id="toastMessage" class="toast align-items-center text-white bg-danger border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastBody"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
</form>
</div>
</div>

<!-- Salary Calculator Modal -->
<div class="modal fade" id="salaryCalculatorModal" tabindex="-1" aria-labelledby="salaryCalculatorModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-lg-down modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px !important; overflow: hidden;">
            <div class="modal-header border-0 pb-0"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem 1.75rem;">
                <div class="d-flex align-items-center">
                    <div id="salaryCalculatorAvatar" class="bg-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px; flex-shrink: 0; overflow: hidden;">
                        <span id="salaryCalculatorEmployeeInitial" class="fw-bold" style="font-size: 28px; color: #764ba2; text-transform: uppercase; display: none; line-height: 1;"></span>
                        <i id="salaryCalculatorIcon" class="ti ti-calculator" style="font-size: 28px; color: #764ba2;"></i>
                    </div>
                    <div>
                        <h4 class="modal-title text-white mb-0" id="salaryCalculatorModalLabel"
                            style="font-weight: 600;">
                            Salary Calculator
                        </h4>
                        <p class="text-white-50 mb-0 small">Detailed salary breakdown and calculation</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"
                    aria-label="Close" style="opacity: 0.8;"></button>
            </div>
            <div class="modal-body bg-light"
                style="max-height: calc(100vh - 180px); overflow-y: auto; padding: 1.75rem;">
                <!-- Loading State -->
                <div id="calculatorLoading" class="text-center p-5" style="display: none;">
                    <div class="spinner-border text-primary" role="status"
                        style="width: 3.5rem; height: 3.5rem; border-width: 0.35rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-4 mb-0 fw-semibold text-muted" style="font-size: 1.1rem;">Calculating salary
                        breakdown...</p>
                    <p class="text-muted small mt-2">Please wait while we process your data</p>
                </div>

                <!-- Results Content -->
                <div id="calculatorResults" style="display: none;">
                    <!-- Summary Cards - Row 1 -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px !important;">
                                <div class="card-body p-4 text-white">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="bg-white bg-opacity-20 rounded-circle p-2">
                                            <i class="ti ti-calculator fs-5"></i>
                                        </div>
                                        <span class="badge bg-white bg-opacity-20">Monthly</span>
                                    </div>
                                    <h6 class="text-white-50 mb-2 fw-normal"
                                        style="font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Monthly CTC</h6>
                                    <h3 class="mb-0 fw-bold" id="result_monthly_ctc" style="font-size: 1.75rem;">
                                        {{ $box->currency_symbol ?? '₹' }}0.00</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100"
                                style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px !important;">
                                <div class="card-body p-4 text-white">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="bg-white bg-opacity-20 rounded-circle p-2">
                                            <i class="ti {{ ($box->currency_code ?? 'INR') === 'INR' ? 'ti-currency-rupee' : 'ti-coins' }} fs-5"></i>
                                        </div>
                                        <span class="badge bg-white bg-opacity-20">Monthly</span>
                                    </div>
                                    <h6 class="text-white-50 mb-2 fw-normal"
                                        style="font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Monthly Gross</h6>
                                    <h3 class="mb-0 fw-bold" id="result_monthly_gross" style="font-size: 1.75rem;">
                                        {{ $box->currency_symbol ?? '₹' }}0.00</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100"
                                style="background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%); border-radius: 12px !important;">
                                <div class="card-body p-4 text-white">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="bg-white bg-opacity-20 rounded-circle p-2">
                                            <i class="ti ti-minus-circle fs-5"></i>
                                        </div>
                                        <span class="badge bg-white bg-opacity-20">Monthly</span>
                                    </div>
                                    <h6 class="text-white-50 mb-2 fw-normal"
                                        style="font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Monthly Deductions</h6>
                                    <h3 class="mb-0 fw-bold" id="result_monthly_deductions"
                                        style="font-size: 1.75rem;">{{ $box->currency_symbol ?? '₹' }}0.00</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Cards - Row 2 -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100"
                                style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 12px !important;">
                                <div class="card-body p-4 text-white">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="bg-white bg-opacity-20 rounded-circle p-2">
                                            <i class="ti ti-calendar-event fs-5"></i>
                                        </div>
                                        <span class="badge bg-white bg-opacity-20">Yearly</span>
                                    </div>
                                    <h6 class="text-white-50 mb-2 fw-normal"
                                        style="font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Yearly CTC</h6>
                                    <h3 class="mb-0 fw-bold" id="result_yearly_ctc" style="font-size: 1.75rem;">{{ $box->currency_symbol ?? '₹' }}0.00
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100"
                                style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px !important;">
                                <div class="card-body p-4 text-white">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="bg-white bg-opacity-20 rounded-circle p-2">
                                            <i class="ti {{ ($box->currency_code ?? 'INR') === 'INR' ? 'ti-currency-rupee' : 'ti-coins' }} fs-5"></i>
                                        </div>
                                        <span class="badge bg-white bg-opacity-20">Yearly</span>
                                    </div>
                                    <h6 class="text-white-50 mb-2 fw-normal"
                                        style="font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Yearly Gross</h6>
                                    <h3 class="mb-0 fw-bold" id="result_yearly_gross" style="font-size: 1.75rem;">
                                        {{ $box->currency_symbol ?? '₹' }}0.00</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100"
                                style="background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%); border-radius: 12px !important;">
                                <div class="card-body p-4 text-white">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="bg-white bg-opacity-20 rounded-circle p-2">
                                            <i class="ti ti-wallet fs-5"></i>
                                        </div>
                                        <span class="badge bg-white bg-opacity-20">Net Salary</span>
                                    </div>
                                    <h6 class="text-white-50 mb-2 fw-normal"
                                        style="font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Monthly Net</h6>
                                    <h3 class="mb-0 fw-bold" id="result_monthly_net" style="font-size: 1.75rem;">
                                        {{ $box->currency_symbol ?? '₹' }}0.00</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Breakdown -->
                    <div class="row g-3">
                        <!-- Earnings -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px !important;">
                                <div class="card-header border-0 pb-2 pt-3"
                                    style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 12px 12px 0 0 !important;">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white bg-opacity-20 rounded-circle p-2 me-2">
                                            <i class="ti ti-trending-up text-white"></i>
                                        </div>
                                        <h5 class="mb-0 text-white fw-semibold">Earnings</h5>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-3"
                                                        style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Component</th>
                                                    <th class="text-end pe-3"
                                                        style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Monthly</th>
                                                    <th class="text-end pe-3"
                                                        style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Yearly</th>
                                                </tr>
                                            </thead>
                                            <tbody id="results_earnings" style="font-size: 0.95rem;">
                                                <!-- Will be populated by JavaScript -->
                                            </tbody>
                                            <tfoot class="table-success" style="border-top: 2px solid #28a745;">
                                                <tr>
                                                    <td class="ps-3 fw-bold">Total Earnings</td>
                                                    <td class="text-end pe-3 fw-bold" id="total_earnings_monthly">
                                                        {{ $box->currency_symbol ?? '₹' }}0.00</td>
                                                    <td class="text-end pe-3 fw-bold" id="total_earnings_yearly">{{ $box->currency_symbol ?? '₹' }}0.00
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reimbursements -->
                        <div class="col-md-6" id="section_reimbursements" style="display: none;">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px !important;">
                                <div class="card-header border-0 pb-2 pt-3"
                                    style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%); border-radius: 12px 12px 0 0 !important;">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white bg-opacity-20 rounded-circle p-2 me-2">
                                            <i class="ti ti-receipt text-white"></i>
                                        </div>
                                        <h5 class="mb-0 text-white fw-semibold">Reimbursements</h5>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-3"
                                                        style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Component</th>
                                                    <th class="text-end pe-3"
                                                        style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Monthly</th>
                                                    <th class="text-end pe-3"
                                                        style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Yearly</th>
                                                </tr>
                                            </thead>
                                            <tbody id="results_reimbursements" style="font-size: 0.95rem;">
                                                <!-- Will be populated by JavaScript -->
                                            </tbody>
                                            <tfoot class="table-info" style="border-top: 2px solid #17a2b8;">
                                                <tr>
                                                    <td class="ps-3 fw-bold">Total Reimbursements</td>
                                                    <td class="text-end pe-3 fw-bold"
                                                        id="total_reimbursements_monthly">{{ $box->currency_symbol ?? '₹' }}0.00</td>
                                                    <td class="text-end pe-3 fw-bold"
                                                        id="total_reimbursements_yearly">{{ $box->currency_symbol ?? '₹' }}0.00</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contributions -->
                        <div class="col-md-6" id="section_contributions" style="display: none;">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px !important;">
                                <div class="card-header border-0 pb-2 pt-3"
                                    style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); border-radius: 12px 12px 0 0 !important;">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white bg-opacity-20 rounded-circle p-2 me-2">
                                            <i class="ti ti-building-bank text-white"></i>
                                        </div>
                                        <h5 class="mb-0 text-white fw-semibold">Contributions (Employer)</h5>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-3"
                                                        style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Component</th>
                                                    <th class="text-end pe-3"
                                                        style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Monthly</th>
                                                    <th class="text-end pe-3"
                                                        style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Yearly</th>
                                                </tr>
                                            </thead>
                                            <tbody id="results_contributions" style="font-size: 0.95rem;">
                                                <!-- Will be populated by JavaScript -->
                                            </tbody>
                                            <tfoot class="table-warning" style="border-top: 2px solid #ffc107;">
                                                <tr>
                                                    <td class="ps-3 fw-bold">Total Contributions</td>
                                                    <td class="text-end pe-3 fw-bold"
                                                        id="total_contributions_monthly">{{ $box->currency_symbol ?? '₹' }}0.00</td>
                                                    <td class="text-end pe-3 fw-bold" id="total_contributions_yearly">
                                                        {{ $box->currency_symbol ?? '₹' }}0.00</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Deductions -->
                        <div class="col-md-6" id="section_deductions" style="display: none;">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px !important;">
                                <div class="card-header border-0 pb-2 pt-3"
                                    style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%); border-radius: 12px 12px 0 0 !important;">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white bg-opacity-20 rounded-circle p-2 me-2">
                                            <i class="ti ti-minus-circle text-white"></i>
                                        </div>
                                        <h5 class="mb-0 text-white fw-semibold">Deductions</h5>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-3"
                                                        style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Component</th>
                                                    <th class="text-end pe-3"
                                                        style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Monthly</th>
                                                    <th class="text-end pe-3"
                                                        style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Yearly</th>
                                                </tr>
                                            </thead>
                                            <tbody id="results_deductions" style="font-size: 0.95rem;">
                                                <!-- Will be populated by JavaScript -->
                                            </tbody>
                                            <tfoot class="table-danger" style="border-top: 2px solid #dc3545;">
                                                <tr>
                                                    <td class="ps-3 fw-bold">Total Deductions</td>
                                                    <td class="text-end pe-3 fw-bold" id="total_deductions_monthly">
                                                        {{ $box->currency_symbol ?? '₹' }}0.00</td>
                                                    <td class="text-end pe-3 fw-bold" id="total_deductions_yearly">
                                                        {{ $box->currency_symbol ?? '₹' }}0.00</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- One-Time -->
                        <div class="col-md-12">
                            <div class="card border-0 shadow-sm" style="border-radius: 12px !important;">
                                <div class="card-header border-0 pb-2 pt-3"
                                    style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); border-radius: 12px 12px 0 0 !important;">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white bg-opacity-20 rounded-circle p-2 me-2">
                                            <i class="ti ti-gift text-white"></i>
                                        </div>
                                        <h5 class="mb-0 text-white fw-semibold">One-Time Components</h5>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-3"
                                                        style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Component</th>
                                                    <th class="text-end pe-3"
                                                        style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Monthly</th>
                                                    <th class="text-end pe-3"
                                                        style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Yearly</th>
                                                </tr>
                                            </thead>
                                            <tbody id="results_onetime" style="font-size: 0.95rem;">
                                                <!-- Will be populated by JavaScript -->
                                            </tbody>
                                            <tfoot class="table-secondary" style="border-top: 2px solid #6c757d;">
                                                <tr>
                                                    <td class="ps-3 fw-bold">Total One-Time</td>
                                                    <td class="text-end pe-3 fw-bold" id="total_onetime_monthly">{{ $box->currency_symbol ?? '₹' }}0.00
                                                    </td>
                                                    <td class="text-end pe-3 fw-bold" id="total_onetime_yearly">{{ $box->currency_symbol ?? '₹' }}0.00
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-white px-4 py-3" style="border-radius: 0 0 16px 16px;">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Close
                </button>
                <div class="ms-auto">
                    <button type="button" class="btn btn-info me-2" id="useCalculatedValuesBtn"
                        style="display: none;" data-bs-dismiss="modal">
                        <i class="ti ti-check me-1"></i>Use These Values
                    </button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="ti ti-printer me-1"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Termination  do not us this popup-->
<div class="modal fade" id="">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Employee Salary </h4>
                <button class="btn btn-outline-primary float-end" type="button" data-bs-toggle="modal"
                    data-bs-target="#add_fields_earning">Add Item <i class="fa-solid fa-plus"></i></button>

            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Basic Salary</label>
                    <input type="text" placeholder="" class="form-control" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">HRA</label>
                    <input type="text" placeholder="%" class="form-control" />
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">DA</label>
                    <input type="text" placeholder="" class="form-control" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">LTA</label>
                    <input type="text" placeholder="%" class="form-control" />
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Special Allowance</label>
                    <input type="text" placeholder="" class="form-control" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Performance Bonus</label>
                    <input type="text" placeholder="%" class="form-control" />
                </div>
            </div>



            <div class="col-md-12 mb-4 mt-4">
                <h4 class="mb-2 d-inline">Deductions</h4>
                <!-- <button class="btn btn-outline-primary float-end" type="button" data-bs-toggle="modal" data-bs-target="#add_fields">Add Item <i class="fa-solid fa-plus"></i></button> -->
                <button class="btn btn-outline-primary float-end" type="button" data-bs-toggle="modal"
                    data-bs-target="#add_fields_deduction">Add Item <i class="fa-solid fa-plus"></i></button>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">EPF</label>
                    <input type="text" placeholder="%" class="form-control" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">TDS</label>
                    <input type="text" placeholder="%" class="form-control" />
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">ESI</label>
                    <input type="text" placeholder="%" class="form-control" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Professional Tax</label>
                    <input type="text" placeholder="%" class="form-control" />
                </div>
            </div>







        </div>

        <div class="col-md-6">
            <div class="card shadow-sm" style="background:#f9fafb;">
                <div class="card-body">

                    <!-- Net Salary Section -->
                    <div class="card-header bg-light text-white text-center mb-4">
                        <div class="row text-center">
                            <div class="col-md-6 border-end">
                                <h6 class="">Net Annual Equivalent</h6>
                                <p class="fw-bold text-primary" style="font-size: 20px;">{{ $box->currency_symbol ?? '₹' }}3,32,100</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Net Monthly Salary</h6>
                                <p class="fw-bold text-primary" style="font-size: 20px;">{{ $box->currency_symbol ?? '₹' }}27,675</p>
                            </div>
                        </div>
                    </div>

                    <!-- Earnings -->
                    <h5 class="border-bottom pb-2">Earnings</h5>
                    <table class="table align-middle">
                        <tbody>
                            <tr>
                                <td>Basic Salary</td>
                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}12,000</td>
                            </tr>
                            <tr>
                                <td>HRA</td>
                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}6,000</td>
                            </tr>
                            <tr>
                                <td>DA</td>
                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}3,000</td>
                            </tr>
                            <tr>
                                <td>LTA</td>
                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}1,500</td>
                            </tr>
                            <tr>
                                <td>Special Allowance</td>
                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}4,500</td>
                            </tr>
                            <tr>
                                <td>Performance Bonus</td>
                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}3,000</td>
                            </tr>
                            <tr class="table-secondary fw-bold">
                                <td>Gross Salary</td>
                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}30,000</td>
                            </tr>
                            <tr>
                                <td>Standard Deduction</td>
                                <td class="text-end text-danger">-{{ $box->currency_symbol ?? '₹' }}4,167</td>
                            </tr>
                            <tr class="table-info fw-bold">
                                <td>Taxable Income (before other deductions)</td>
                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}25,833</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Deductions -->
                    <h5 class="border-bottom pb-2 mt-4">Deductions</h5>
                    <table class="table align-middle">
                        <tbody>
                            <tr>
                                <td>EPF (Employee)</td>
                                <td class="text-end text-danger">-{{ $box->currency_symbol ?? '₹' }}1,440</td>
                            </tr>
                            <tr>
                                <td>EPF (Employer)</td>
                                <td class="text-end text-success">{{ $box->currency_symbol ?? '₹' }}1,440</td>
                            </tr>
                            <tr>
                                <td>ESI (Employee)</td>
                                <td class="text-end text-danger">-{{ $box->currency_symbol ?? '₹' }}525</td>
                            </tr>
                            <tr>
                                <td>ESI (Employer)</td>
                                <td class="text-end text-success">{{ $box->currency_symbol ?? '₹' }}975</td>
                            </tr>
                            <tr>
                                <td>Professional Tax</td>
                                <td class="text-end text-danger">-{{ $box->currency_symbol ?? '₹' }}100</td>
                            </tr>
                            <tr>
                                <td>Income Tax</td>
                                <td class="text-end text-danger">-{{ $box->currency_symbol ?? '₹' }}260</td>
                            </tr>
                            <tr class="table-secondary fw-bold">
                                <td>Total Deductions</td>
                                <td class="text-end text-danger">-{{ $box->currency_symbol ?? '₹' }}2,325</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- CTC Summary -->
                    <h5 class="border-bottom pb-2 mt-4">Cost to Company (CTC) Breakup</h5>
                    <table class="table align-middle">
                        <tbody>
                            <tr>
                                <td>Gross Salary</td>
                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}30,000</td>
                            </tr>
                            <tr>
                                <td>Employer EPF Contribution</td>
                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}1,440</td>
                            </tr>
                            <tr>
                                <td>Employer ESI Contribution</td>
                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}975</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <div class="card-footer text-center text-muted">
                    <small>All values are in {{ $box->currency_code ?? 'INR' }} ({{ $box->currency_symbol ?? '₹' }}). Calculations based on Old Tax Regime.</small>
                </div>
            </div>
        </div>




    </div>


</div>


{{-- <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2" data-bs-dismiss="modal">Cancel </button>
                    <button type="submit" class="btn btn-primary">Add Employee Salary </button>
                </div> --}}
</form>
</div>
</div>
















<!-- Add Termination  do not us this popup-->
<div class="modal fade" id="">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Employee Salary </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="employeeSalaryForm" method="POST">
                @csrf

                <div class="modal-body pb-0" style="max-height: inherit;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Employee Name  </label>
                                    <select class="select" id="" name="box_user_id">
                                        <option value="">Select Employee</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Enter Annual CTC</label>
                                    <input type="number" placeholder="Enter Annual CTC Amount"
                                        class="form-control" />
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-grid gap-2 mb-4">
                                        <button class="btn btn-outline-primary" type="button">Monthly</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-grid gap-2 mb-4">
                                        <button class="btn btn-outline-primary" type="button">Yearly</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <h4 class="mb-2 d-inline">Earnings</h4>
                                <button class="btn btn-outline-primary float-end" type="button"
                                    data-bs-toggle="modal" data-bs-target="#add_fields">Add Item <i
                                        class="fa-solid fa-plus"></i></button>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Basic Salary</label>
                                    <input type="text" placeholder="" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">HRA</label>
                                    <input type="text" placeholder="%" class="form-control" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">DA</label>
                                    <input type="text" placeholder="" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">LTA</label>
                                    <input type="text" placeholder="%" class="form-control" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Special Allowance</label>
                                    <input type="text" placeholder="" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Performance Bonus</label>
                                    <input type="text" placeholder="%" class="form-control" />
                                </div>
                            </div>



                            <div class="col-md-12 mb-4 mt-4">
                                <h4 class="mb-2 d-inline">Deductions</h4>
                                <button class="btn btn-outline-primary float-end" type="button"
                                    data-bs-toggle="modal" data-bs-target="#add_fields">Add Item <i
                                        class="fa-solid fa-plus"></i></button>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">EPF</label>
                                    <input type="text" placeholder="%" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">TDS</label>
                                    <input type="text" placeholder="%" class="form-control" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">ESI</label>
                                    <input type="text" placeholder="%" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Professional Tax</label>
                                    <input type="text" placeholder="%" class="form-control" />
                                </div>
                            </div>







                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-sm" style="background:#f9fafb;">
                                <div class="card-body">

                                    <!-- Net Salary Section -->
                                    <div class="card-header bg-light text-white text-center mb-4">
                                        <div class="row text-center">
                                            <div class="col-md-6 border-end">
                                                <h5 class="">Net Annual Equivalent</h5>
                                                <p class="fw-bold text-primary" style="font-size: 20px;">{{ $box->currency_symbol ?? '₹' }}3,32,100</p>
                                            </div>
                                            <div class="col-md-6">
                                                <h5>Net Monthly Salary</h5>
                                                <p class="fw-bold text-primary" style="font-size: 20px;">{{ $box->currency_symbol ?? '₹' }}27,675</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Earnings -->
                                    <h5 class="border-bottom pb-2">Earnings</h5>
                                    <table class="table align-middle">
                                        <tbody>
                                            <tr>
                                                <td>Basic Salary</td>
                                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}12,000</td>
                                            </tr>
                                            <tr>
                                                <td>HRA</td>
                                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}6,000</td>
                                            </tr>
                                            <tr>
                                                <td>DA</td>
                                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}3,000</td>
                                            </tr>
                                            <tr>
                                                <td>LTA</td>
                                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}1,500</td>
                                            </tr>
                                            <tr>
                                                <td>Special Allowance</td>
                                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}4,500</td>
                                            </tr>
                                            <tr>
                                                <td>Performance Bonus</td>
                                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}3,000</td>
                                            </tr>
                                            <tr class="table-secondary fw-bold">
                                                <td>Gross Salary</td>
                                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}30,000</td>
                                            </tr>
                                            <tr>
                                                <td>Standard Deduction</td>
                                                <td class="text-end text-danger">-{{ $box->currency_symbol ?? '₹' }}4,167</td>
                                            </tr>
                                            <tr class="table-info fw-bold">
                                                <td>Taxable Income (before other deductions)</td>
                                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}25,833</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Deductions -->
                                    <h5 class="border-bottom pb-2 mt-4">Deductions</h5>
                                    <table class="table align-middle">
                                        <tbody>
                                            <tr>
                                                <td>EPF (Employee)</td>
                                                <td class="text-end text-danger">-{{ $box->currency_symbol ?? '₹' }}1,440</td>
                                            </tr>
                                            <tr>
                                                <td>EPF (Employer)</td>
                                                <td class="text-end text-success">{{ $box->currency_symbol ?? '₹' }}1,440</td>
                                            </tr>
                                            <tr>
                                                <td>ESI (Employee)</td>
                                                <td class="text-end text-danger">-{{ $box->currency_symbol ?? '₹' }}525</td>
                                            </tr>
                                            <tr>
                                                <td>ESI (Employer)</td>
                                                <td class="text-end text-success">{{ $box->currency_symbol ?? '₹' }}975</td>
                                            </tr>
                                            <tr>
                                                <td>Professional Tax</td>
                                                <td class="text-end text-danger">-{{ $box->currency_symbol ?? '₹' }}100</td>
                                            </tr>
                                            <tr>
                                                <td>Income Tax</td>
                                                <td class="text-end text-danger">-{{ $box->currency_symbol ?? '₹' }}260</td>
                                            </tr>
                                            <tr class="table-secondary fw-bold">
                                                <td>Total Deductions</td>
                                                <td class="text-end text-danger">-{{ $box->currency_symbol ?? '₹' }}2,325</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- CTC Summary -->
                                    <h5 class="border-bottom pb-2 mt-4">Cost to Company (CTC) Breakup</h5>
                                    <table class="table align-middle">
                                        <tbody>
                                            <tr>
                                                <td>Gross Salary</td>
                                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}30,000</td>
                                            </tr>
                                            <tr>
                                                <td>Employer EPF Contribution</td>
                                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}1,440</td>
                                            </tr>
                                            <tr>
                                                <td>Employer ESI Contribution</td>
                                                <td class="text-end">{{ $box->currency_symbol ?? '₹' }}975</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                                <div class="card-footer text-center text-muted">
                                    <small>All values are in {{ $box->currency_code ?? 'INR' }} ({{ $box->currency_symbol ?? '₹' }}). Calculations based on Old Tax Regime.</small>
                                </div>
                            </div>
                        </div>




                    </div>


                </div>




                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2" data-bs-dismiss="modal">Cancel </button>
                    <button type="submit" class="btn btn-primary">Add Employee Salary </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Termination -->
<div class="modal fade" id="edit-employee-salary">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Employee Salary</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="edit-salary-form">
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Employee Name</label>
                                <select class="select" id="emp_id" name="emp_id">
                                    <option value="">Select Users</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Gross Salary (CTC) <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_gross_salary"
                                    name="gross_salary" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Salary Setting Template <span
                                        class="text-danger">*</span></label>
                                <select class="select" id="edit_salary_setting_type" name="salary_settings_id"
                                    required>
                                    <option value="">Select Salary Setting</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="salary_id" name="salary_id">
                    <input type="hidden" id="edit_box_user_id" name="box_user_id">

                    <div class="row earning-row">
                        <div class="d-flex justify-content-between mb-3">
                            <label class="form-label mb-0">Earnings</label>
                            <a href="#" id="edittoggleSalarySettingBtn" class="text-primary">
                                <i class="ti ti-plus me-1"></i>Add New
                            </a>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Basic</label>
                                <input type="text" class="form-control" id="basic" name="basic" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">DA</label>
                                <input type="text" class="form-control" id="da" name="da" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">DA Sum Amount </label>
                                <input type="text" name="da_sum_amount" id="da_sum_amount"
                                    class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">HRA</label>
                                <input type="text" class="form-control" id="hra" name="hra" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">HRA Sum Amount </label>
                                <input type="text" name="hra_sum_amount" id="hra_sum_amount"
                                    class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Conveyance</label>
                                <input type="text" class="form-control" id="conveyance" name="conveyance" />
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Allowance</label>
                                <input type="text" class="form-control" id="allowance" name="allowance" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Medical Allowance</label>
                                <input type="text" class="form-control" id="medical_allowance"
                                    name="medical_allowance" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Others</label>
                                <input type="text" class="form-control" id="earning_others"
                                    name="earning_others" />
                            </div>
                        </div>

                        <div id="editsalaryextrafields" style="display: none">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Salary Setting </label>
                                    <select class="select" id="salary_setting_types" name="salary_settings_id">
                                        <option value="">Select Salary Setting</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Payroll Addition </label>
                                    <select class="select" id="salary_payroll_addition_ids"
                                        name="salary_payroll_addition_id">
                                        <option value="">Select Salary Setting</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Payroll Overtime </label>
                                    <select class="select" id="salary_payroll_overtime_ids"
                                        name="salary_payroll_overtime_id">
                                        <option value="">Select Salary Setting</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row deduction-row" style="display: none;">
                        <div class="d-flex justify-content-between mb-3">
                            <label class="form-label mb-0">Deductions</label>
                            <a href="#" id="editdeducttoggleSalarySettingBtn" class="text-primary">
                                <i class="ti ti-plus me-1"></i>Add New
                            </a>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">TDS</label>
                                <input type="text" class="form-control" id="tds" name="tds" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">TDS Sum Amount </label>
                                <input type="text" name="tds_sum_amount" id="tds_sum_amount"
                                    class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">ESI</label>
                                <input type="text" class="form-control" id="esi" name="esi" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">ESI Sum Amount </label>
                                <input type="text" name="esi_sum_amount" id="esi_sum_amount"
                                    class="form-control" />

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">PF</label>
                                <input type="text" class="form-control" id="pf" name="pf" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">PF Sum Amount </label>
                                <input type="text" name="pf_sum_amount" id="pf_sum_amount"
                                    class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Leave</label>
                                <input type="text" class="form-control" id="leave" name="leave" />
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Prof. Tax</label>
                                <input type="text" class="form-control" id="prof_tax" name="prof_tax" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Labour Welfare</label>
                                <input type="text" class="form-control" id="labour_welfare"
                                    name="labour_welfare" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Others</label>
                                <input type="text" class="form-control" id="deduction_others"
                                    name="deduction_others" />
                            </div>
                        </div>
                        <div id="editdeductionextraSalaryFields" style="display: none">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Payroll Deduction </label>
                                    <select class="select" id="salary_payroll_deduction_ids"
                                        name="salary_payroll_deduction_id">
                                        <option value="">Select Salary Setting</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-info me-2" id="recalculateSalaryBtn"
                        style="display: none;">
                        <i class="ti ti-calculator me-1"></i>Recalculate
                    </button>
                    <button type="submit" class="btn btn-primary">Update Employee Salary</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- /Edit Termination -->



<!-- Add Custom Earning Fields -->
<div class="modal fade" id="add_fields_earning" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Custom Fields</h5>
                <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form action="custom-fields.html">
                <div class="modal-body pb-0">
                    <div class="mb-3">
                        <label class="col-form-label">Custom Fields For <span class="text-danger">*</span></label>
                        <select class="select">
                            <option disabled selected>Select field</option>
                            <option>Basic Salary</option>
                            <option>HRA</option>
                            <option>DA</option>
                            <option>LTA</option>
                            <option>Special Allowance</option>
                            <option>Performance Bonus</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Input Type <span class="text-danger">*</span></label>
                        <select class="select">
                            <option>Choose</option>
                            <option>Text</option>
                            <option>Select</option>
                            <option>Text Area</option>
                            <option>Number</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">Default Value</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="d-flex align-items-center ">
                        <div class="mb-3 me-4">
                            <label class="col-form-label">Required <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap">
                                <div class="me-2">
                                    <input type="radio" class="status-radio" id="yes" name="status"
                                        checked="">
                                    <label for="yes">Yes</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="no" name="status">
                                    <label for="no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label">Status <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap">
                                <div class="me-2">
                                    <input type="radio" class="status-radio" id="active" name="status2"
                                        checked="">
                                    <label for="active">Active</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="inactive" name="status2">
                                    <label for="inactive">Inactive</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center">
                        <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Custom Fields -->




<!-- Add Custom Deduction Fields -->
<div class="modal fade" id="add_fields_deduction" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Custom Fields</h5>
                <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form action="custom-fields.html">
                <div class="modal-body pb-0">
                    <div class="mb-3">
                        <label class="col-form-label">Custom Fields For <span class="text-danger">*</span></label>
                        <select class="select">
                            <option disabled selected>Select field</option>
                            <option>EPF</option>
                            <option>TDS</option>
                            <option>ESI</option>
                            <option>Professional Tax</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Input Type <span class="text-danger">*</span></label>
                        <select class="select">
                            <option>Choose</option>
                            <option>Text</option>
                            <option>Select</option>
                            <option>Text Area</option>
                            <option>Number</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">Default Value</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="d-flex align-items-center ">
                        <div class="mb-3 me-4">
                            <label class="col-form-label">Required <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap">
                                <div class="me-2">
                                    <input type="radio" class="status-radio" id="yes" name="status"
                                        checked="">
                                    <label for="yes">Yes</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="no" name="status">
                                    <label for="no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label">Status <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap">
                                <div class="me-2">
                                    <input type="radio" class="status-radio" id="active" name="status2"
                                        checked="">
                                    <label for="active">Active</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="inactive" name="status2">
                                    <label for="inactive">Inactive</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center">
                        <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Custom Fields -->




<!-- Delete Modal -->
<div class="modal fade" id="delete_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1">Confirm Delete</h4>
                <p class="mb-3">You want to delete this item. This action cannot be undone.</p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .offcanvas-end.offcanvas-large.offcanvas-large-xl {
        max-width: 950px !important;
        width: 100% !important;
    }

    /* Read-only calculated fields styling */
    #calculatedResults input[readonly] {
        background-color: #f8f9fa;
        cursor: not-allowed;
        font-weight: 600;
        font-size: 16px;
    }

    #calculatedResults .text-success {
        color: #198754 !important;
    }

    #calculatedResults .text-danger {
        color: #dc3545 !important;
    }

    .calculate-period-btn.active {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
</style>


<!-- /Delete Modal -->
    @push('scripts')
    <script>
        const currencySymbol = "{{ $box->currency_symbol ?? '₹' }}";
        const currencyCode = "{{ $box->currency_code ?? 'INR' }}";
        $(document).ready(function() {
            // Section mappings from PHP
            const sections = {
                earnings: {!! json_encode($earnings) !!},
                reimbursements: {!! json_encode($reimbursements) !!},
                contributions: {!! json_encode($contributions) !!},
                deductions: {!! json_encode($deductions) !!},
                onetime: {!! json_encode($onetime) !!}
            };

            // Handle salary setting selection
            $(document).on('change', '#salary_setting_type', function() {
                const settingId = $(this).val();

                // Reset calculation data
                currentSalarySettingData = null;

                if (!settingId) {
                    return;
                }

                // Fetch salary setting details from API (for future use if needed)
                $.ajax({
                    url: '{{ route('salary-settings.get-details', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'GET',
                    data: {
                        id: settingId
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            currentSalarySettingData = response.data;
                        }
                    },
                    error: function(xhr) {
                        console.error("Failed to fetch salary setting details:", xhr);
                    }
                });
            });

            // Generate fields dynamically from API response
            function generateFieldsFromApiResponse(data) {
                // Clear all existing fields
                clearAllFields();

                const componentTypes = data.component_types || {};

                // Process each section
                Object.keys(sections).forEach(sectionKey => {
                    const sectionFields = sections[sectionKey];
                    const $sectionContainer = $(`#${sectionKey}Section`);
                    const $fieldsContainer = $(`#${sectionKey}Fields`);
                    $fieldsContainer.empty();

                    let hasFields = false;

                    // Check each field in the section
                    Object.keys(sectionFields).forEach(fieldName => {
                        const fieldValue = parseFloat(data[fieldName]) || 0;
                        const fieldType = componentTypes[fieldName] || 'fixed';

                        // Show field if it has a value or is in component_types
                        if (fieldValue > 0 || componentTypes[fieldName]) {
                            hasFields = true;
                            const fieldLabel = sectionFields[fieldName];
                            const fieldHtml = generateFieldHtml(fieldName, fieldLabel, fieldValue,
                                fieldType);
                            $fieldsContainer.append(fieldHtml);
                        }
                    });

                    // Show/hide section based on whether it has fields
                    if (hasFields) {
                        $sectionContainer.show();
                    } else {
                        $sectionContainer.hide();
                    }
                });
            }

            // Generate HTML for a single field
            function generateFieldHtml(fieldName, fieldLabel, defaultValue, fieldType) {
                const isPercentage = fieldType === 'percentage';
                const value = defaultValue || 0;

                return `
                <div class="col-md-4 mb-3">
                    <label class="form-label">${fieldLabel}</label>
                    <div class="input-group">
                        <input type="number"
                               step="0.01"
                               name="${fieldName}"
                               class="form-control"
                               value="${value}"
                               placeholder="${isPercentage ? '%' : 'Amount'}" />
                        <select name="${fieldName}_type" class="form-select component-type-select" style="max-width: 80px;">
                            <option value="fixed" ${!isPercentage ? 'selected' : ''}>${currencySymbol}</option>
                            <option value="percentage" ${isPercentage ? 'selected' : ''}>%</option>
                        </select>
                    </div>
                    ${fieldName === 'hra' || fieldName === 'da' || fieldName === 'pf' || fieldName === 'esi' || fieldName === 'tds' ?
                        `<input type="hidden" name="${fieldName}_sum_amount" value="0" />` : ''}
                </div>
            `;
            }

            // Clear all dynamically generated fields (kept for compatibility)
            function clearAllFields() {
                // Fields removed - function kept for compatibility
            }

            // Track calculation period
            let calculationPeriod = 'monthly';
            let currentSalarySettingData = null;

            // Handle period selection
            $(document).on('click', '.calculate-period-btn', function() {
                const newPeriod = $(this).data('period');
                if (newPeriod === calculationPeriod) return; // No change

                // Update UI state
                $('.calculate-period-btn').removeClass('active');
                $(this).addClass('active');

                // Convert CTC value if present
                const $ctcInput = $('#ctc_input');
                const currentCtc = parseFloat($ctcInput.val());

                if (!isNaN(currentCtc) && currentCtc > 0) {
                    if (calculationPeriod === 'monthly' && newPeriod === 'yearly') {
                        // Convert Monthly to Yearly
                        $ctcInput.val((currentCtc * 12).toFixed(2));
                    } else if (calculationPeriod === 'yearly' && newPeriod === 'monthly') {
                        // Convert Yearly to Monthly
                        $ctcInput.val((currentCtc / 12).toFixed(2));
                    }
                }

                calculationPeriod = newPeriod;
                updateCtcLabel();

                // Auto-recalculate if results are visible
                if ($('#salaryCalculationResults').is(':visible')) {
                    const ctc = parseFloat($ctcInput.val()) || 0;
                    const settingId = $('#salary_setting_type').val();
                    if (ctc > 0 && settingId) {
                        calculateSalary();
                    }
                }
            });

            function updateCtcLabel() {
                // Always show "Enter Annual CTC" regardless of calculation period
                const labelText = 'Enter Annual CTC <span class="text-danger">*</span>';
                const placeholderText = 'Enter Annual CTC Amount';

                $('label[for="ctc_input"]').html(labelText);
                $('#ctc_input').attr('placeholder', placeholderText);
            }

            // Initialize label
            updateCtcLabel();

            // Auto-calculate when CTC changes (if salary setting is selected) - removed for now
            // User needs to click Calculate button explicitly

            // Handle calculate button click
            $(document).on('click', '#calculateSalaryBtn', function() {
                calculateSalary();
            });

            // Function to set employee initial in the salary calculator modal
            window.setSalaryCalculatorEmployeeName = function(name, imageUrl = null) {
                const $avatarContainer = $('#salaryCalculatorAvatar');
                const $initial = $('#salaryCalculatorEmployeeInitial');
                const $icon = $('#salaryCalculatorIcon');
                
                // If there's an existing image, remove it
                $avatarContainer.find('img').remove();

                if (imageUrl && imageUrl !== '') {
                    $initial.hide();
                    $icon.hide();
                    
                    const $img = $(`<img src="${imageUrl}" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;" alt="${name}">`);
                    
                    // Add onerror handler to fallback to initial if image fails to load
                    $img.on('error', function() {
                        $(this).remove();
                        if (name && name.trim() !== '' && name.trim() !== 'Select Employee') {
                            $initial.text(name.trim().charAt(0).toUpperCase()).show();
                        } else {
                            $icon.show();
                        }
                    });
                    
                    $avatarContainer.append($img);
                } else if (name && name.trim() !== '' && name.trim() !== 'Select Employee') {
                    $initial.text(name.trim().charAt(0).toUpperCase()).show();
                    $icon.hide();
                } else {
                    $initial.hide();
                    $icon.show();
                }
            };

            // Calculate salary based on CTC and salary setting - Backend API call
            function calculateSalary() {
                const ctc = parseFloat($('#ctc_input').val()) || 0;
                const settingId = $('#salary_setting_type').val();
                const period = calculationPeriod || 'monthly';

                if (!ctc || ctc <= 0) {
                    showErrorToast('Please enter a valid CTC amount');
                    return;
                }

                if (!settingId) {
                    showErrorToast('Please select a Salary Setting Template');
                    return;
                }

                // Open modal and show loading
                const employeeOption = $('#employeeSelect option:selected');
                setSalaryCalculatorEmployeeName(employeeOption.text(), employeeOption.data('image'));
                const modal = new bootstrap.Modal(document.getElementById('salaryCalculatorModal'));
                modal.show();
                $('#calculatorLoading').show();
                $('#calculatorResults').hide();
                $('#calculateSalaryBtn').prop('disabled', true).html(
                    '<i class="ti ti-loader me-1"></i>Calculating...');

                // First, fetch salary setting details using get-details API
                $.ajax({
                    url: '{{ route('salary-settings.get-details', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'GET',
                    data: {
                        id: settingId
                    },
                    success: function(getDetailsResponse) {
                        if (getDetailsResponse.success && getDetailsResponse.data) {
                            // Store salary setting details
                            currentSalarySettingData = getDetailsResponse.data;

                            // Now call the calculate API
                            $.ajax({
                                url: '{{ route('salary-settings.calculate', ['box_slug' => request()->route('box_slug')]) }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    ctc: ctc,
                                    period: period,
                                    salary_setting_id: settingId
                                },
                                success: function(response) {
                                    $('#calculatorLoading').hide();
                                    if (response.success && response.data) {
                                        // Display calculation results first
                                        displayCalculationResults(response.data);

                                        // Then call get-details API to fetch and display salary setting details
                                        $.ajax({
                                            url: '{{ route('salary-settings.get-details', ['box_slug' => request()->route('box_slug')]) }}',
                                            method: 'GET',
                                            data: {
                                                id: settingId
                                            },
                                            success: function(getDetailsResponse) {
                                                if (getDetailsResponse
                                                    .success &&
                                                    getDetailsResponse.data) {
                                                    // Store and display salary setting details
                                                    currentSalarySettingData =
                                                        getDetailsResponse.data;
                                                    displaySalarySettingDetails(
                                                        getDetailsResponse
                                                        .data);
                                                    console.log(
                                                        'Salary Setting Details:',
                                                        getDetailsResponse
                                                        .data);
                                                }
                                            },
                                            error: function(xhr) {
                                                console.error(
                                                    "Failed to fetch salary setting details:",
                                                    xhr);
                                                // Don't show error to user, just log it
                                            }
                                        });

                                        $('#calculatorResults').show();
                                        // Button will be shown by displayCalculationResults
                                    } else {
                                        $('#calculatorResults').html(
                                            '<div class="alert alert-danger">Failed to calculate salary. Please try again.</div>'
                                            ).show();
                                        $('#useCalculatedValuesBtn').hide();
                                    }
                                },
                                error: function(xhr) {
                                    console.error("Failed to calculate salary:", xhr);
                                    $('#calculatorLoading').hide();
                                    $('#useCalculatedValuesBtn').hide();
                                    let errorMsg =
                                        'Failed to calculate salary. Please try again.';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMsg = xhr.responseJSON.message;
                                    }
                                    $('#calculatorResults').html(
                                        '<div class="alert alert-danger">' + errorMsg +
                                        '</div>').show();
                                },
                                complete: function() {
                                    $('#calculateSalaryBtn').prop('disabled', false).html(
                                        '<i class="ti ti-calculator me-1"></i>Calculate Salary'
                                        );
                                }
                            });
                        } else {
                            $('#calculatorLoading').hide();
                            $('#calculateSalaryBtn').prop('disabled', false).html(
                                '<i class="ti ti-calculator me-1"></i>Calculate Salary');
                            $('#calculatorResults').html(
                                '<div class="alert alert-danger">Failed to fetch salary setting details. Please try again.</div>'
                                ).show();
                        }
                    },
                    error: function(xhr) {
                        console.error("Failed to fetch salary setting details:", xhr);
                        $('#calculatorLoading').hide();
                        $('#calculateSalaryBtn').prop('disabled', false).html(
                            '<i class="ti ti-calculator me-1"></i>Calculate Salary');
                        $('#calculatorResults').html(
                            '<div class="alert alert-danger">Failed to fetch salary setting details. Please try again.</div>'
                            ).show();
                    }
                });
            }

            // Custom fields cache
            let customFieldsMap = {};

            // Load custom fields to get proper labels
            function loadCustomFieldsMap() {
                $.get(
                        '{{ route('app.settings.custom_fields.salary', ['box_slug' => request()->route('box_slug')]) }}')
                    .done(function(res) {
                        if (res.success && res.data) {
                            res.data.forEach(field => {
                                customFieldsMap[field.col_name] = field.name;
                            });
                        }
                    })
                    .fail(function() {
                        console.warn('Failed to load custom fields');
                    });
            }

            // Load custom fields on page load
            loadCustomFieldsMap();

            // Icon mapping for different components
            const componentIcons = {
                // Earnings
                'basic': (typeof currencyCode !== 'undefined' && currencyCode === 'INR') || "{{ $box->currency_code ?? 'INR' }}" === 'INR' ? 'ti ti-currency-rupee' : 'ti ti-coins',
                'da': 'ti ti-percentage',
                'hra': 'ti ti-home',
                'conveyance': 'ti ti-car',
                'allowance': 'ti ti-wallet',
                'medical_allowance': 'ti ti-heart',
                'special_allowance': 'ti ti-star',
                'leave_travel_allowance': 'ti ti-plane',
                'city_compensatory_allowance': 'ti ti-building',
                'sales_target_incentives': 'ti ti-target',
                'shift_allowance': 'ti ti-clock',
                'transport_allowance': 'ti ti-bus',
                'food_allowance': 'ti ti-tools-kitchen',
                'overtime_allowance': 'ti ti-time-management',
                'uniform_allowance': 'ti ti-shirt',
                'mobile_internet_allowance': 'ti ti-device-mobile',
                'project_allowance': 'ti ti-briefcase',
                'education_allowance': 'ti ti-school',
                'professional_allowance': 'ti ti-certificate',
                'arrears_earnings': 'ti ti-calendar-time',
                'others': 'ti ti-dots',
                // Reimbursements
                'travel_reimbursement': 'ti ti-map-pin',
                'fuel_reimbursement': 'ti ti-gas-station',
                'telephone_reimbursement': 'ti ti-phone',
                'driver_reimbursement': 'ti ti-user',
                'medical_reimbursement': 'ti ti-medical-cross',
                'uniform_reimbursement': 'ti ti-tshirt',
                // Contributions
                'employer_pf': 'ti ti-building-bank',
                'employer_esi': 'ti ti-shield',
                'gratuity': 'ti ti-gift',
                'superannuation': 'ti ti-piggy-bank',
                'employer_lwf': 'ti ti-hand-helping',
                // Deductions
                'employee_pf': 'ti ti-building-bank',
                'employee_esi': 'ti ti-shield',
                'professional_tax': 'ti ti-file-invoice',
                'tds': 'ti ti-receipt-tax',
                'salary_advance_recovery': 'ti ti-arrow-back',
                'loan_emi_recovery': 'ti ti-credit-card',
                'canteen_deduction': 'ti ti-tools-kitchen',
                'attendance_loss': 'ti ti-clock-off',
                'notice_period_recovery': 'ti ti-calendar-off',
                'arrears_recovery': 'ti ti-calendar-time',
                'insurance_premium_employee': 'ti ti-shield-check',
                // One-Time
                'joining_bonus': 'ti ti-hand-rock',
                'retention_bonus': 'ti ti-lock',
                'performance_bonus': 'ti ti-trophy',
                'ex_gratia': 'ti ti-gift',
                'full_final_settlement': 'ti ti-file-check',
                'leave_encashment': 'ti ti-cash',
                'gratuity_payout': 'ti ti-wallet',
                'severance_compensation': 'ti ti-handshake'
            };

            // Get icon for component
            function getComponentIcon(fieldName) {
                return componentIcons[fieldName] || 'ti ti-circle';
            }

            // Display calculation results in the results section
            function displayCalculationResults(data) {
                // Update totals - CTC values from metadata
                const monthlyCtc = parseFloat(data.metadata?.monthly_ctc || 0);
                const yearlyCtc = parseFloat(data.metadata?.yearly_ctc || 0);

                $('#result_monthly_ctc').text(currencySymbol + monthlyCtc.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
                $('#result_yearly_ctc').text(currencySymbol + yearlyCtc.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

                // Update gross, contributions, deductions, and net
                $('#result_monthly_gross').text(currencySymbol + parseFloat(data.totals.monthly_gross).toLocaleString(
                'en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
                $('#result_yearly_gross').text(currencySymbol + parseFloat(data.totals.yearly_gross).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
                $('#result_monthly_contributions').text(currencySymbol + parseFloat(data.totals
                    .monthly_employer_contributions || 0).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
                $('#result_monthly_deductions').text(currencySymbol + parseFloat(data.totals.monthly_deductions)
                    .toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                $('#result_monthly_net').text(currencySymbol + parseFloat(data.totals.monthly_net).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

                // Display Earnings
                let earningsHtml = '';
                if (Object.keys(data.earnings).length > 0) {
                    Object.keys(data.earnings).forEach(field => {
                        const label = sections.earnings[field] || customFieldsMap[field] || field.replace(
                            /_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        const monthly = parseFloat(data.earnings[field].monthly) || 0;
                        const yearly = parseFloat(data.earnings[field].yearly) || 0;
                        const icon = getComponentIcon(field);
                        earningsHtml += `<tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="${icon} me-2 text-success fs-5"></i>
                                <span>${label}</span>
                            </div>
                        </td>
                        <td class="text-end fw-semibold text-success">${currencySymbol}${monthly.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td class="text-end fw-semibold text-success">${currencySymbol}${yearly.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    </tr>`;
                    });
                } else {
                    earningsHtml =
                        '<tr><td colspan="3" class="text-center text-muted py-4"><i class="ti ti-inbox fs-1 d-block mb-2"></i>No earnings found</td></tr>';
                }
                $('#results_earnings').html(earningsHtml);
                // Display total earnings from backend
                const totalEarningsMonthly = parseFloat(data.totals.monthly_total_earnings || 0);
                const totalEarningsYearly = parseFloat(data.totals.yearly_total_earnings || 0);
                $('#total_earnings_monthly').text(currencySymbol + totalEarningsMonthly.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
                $('#total_earnings_yearly').text(currencySymbol + totalEarningsYearly.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

                // Display Reimbursements
                let reimbursementsHtml = '';
                const hasReimbursements = Object.keys(data.reimbursements).length > 0;
                if (hasReimbursements) {
                    Object.keys(data.reimbursements).forEach(field => {
                        const label = sections.reimbursements[field] || customFieldsMap[field] || field
                            .replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        const monthly = parseFloat(data.reimbursements[field].monthly) || 0;
                        const yearly = parseFloat(data.reimbursements[field].yearly) || 0;
                        const icon = getComponentIcon(field);
                        reimbursementsHtml += `<tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="${icon} me-2 text-info fs-5"></i>
                                <span>${label}</span>
                            </div>
                        </td>
                        <td class="text-end fw-semibold text-info">${currencySymbol}${monthly.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td class="text-end fw-semibold text-info">${currencySymbol}${yearly.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    </tr>`;
                    });
                }
                $('#results_reimbursements').html(reimbursementsHtml);
                // Show/hide reimbursements section based on data
                if (hasReimbursements) {
                    $('#section_reimbursements').show();
                    const totalReimbursementsMonthly = parseFloat(data.totals.monthly_total_reimbursements || 0);
                    const totalReimbursementsYearly = parseFloat(data.totals.yearly_total_reimbursements || 0);
                    $('#total_reimbursements_monthly').text(currencySymbol + totalReimbursementsMonthly.toLocaleString(
                    'en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    $('#total_reimbursements_yearly').text(currencySymbol + totalReimbursementsYearly.toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                } else {
                    $('#section_reimbursements').hide();
                }

                // Display Contributions
                let contributionsHtml = '';
                const hasContributions = Object.keys(data.contributions).length > 0;
                if (hasContributions) {
                    Object.keys(data.contributions).forEach(field => {
                        const label = sections.contributions[field] || customFieldsMap[field] || field
                            .replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        const monthly = parseFloat(data.contributions[field].monthly) || 0;
                        const yearly = parseFloat(data.contributions[field].yearly) || 0;
                        const icon = getComponentIcon(field);
                        contributionsHtml += `<tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="${icon} me-2 text-warning fs-5"></i>
                                <span>${label}</span>
                        </div>
                        </td>
                        <td class="text-end fw-semibold text-warning">${currencySymbol}${monthly.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td class="text-end fw-semibold text-warning">${currencySymbol}${yearly.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    </tr>`;
                    });
                }
                $('#results_contributions').html(contributionsHtml);
                // Show/hide contributions section based on data
                if (hasContributions) {
                    $('#section_contributions').show();
                    const totalContributionsMonthly = parseFloat(data.totals.monthly_employer_contributions || 0);
                    const totalContributionsYearly = parseFloat(data.totals.yearly_employer_contributions || 0);
                    $('#total_contributions_monthly').text(currencySymbol + totalContributionsMonthly.toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    $('#total_contributions_yearly').text(currencySymbol + totalContributionsYearly.toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                } else {
                    $('#section_contributions').hide();
                }

                // Display Deductions
                let deductionsHtml = '';
                const hasDeductions = Object.keys(data.deductions).length > 0;
                if (hasDeductions) {
                    Object.keys(data.deductions).forEach(field => {
                        const label = sections.deductions[field] || customFieldsMap[field] || field.replace(
                            /_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        const monthly = parseFloat(data.deductions[field].monthly) || 0;
                        const yearly = parseFloat(data.deductions[field].yearly) || 0;
                        const icon = getComponentIcon(field);
                        deductionsHtml += `<tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="${icon} me-2 text-danger fs-5"></i>
                                <span>${label}</span>
                            </div>
                        </td>
                        <td class="text-end fw-semibold text-danger">${currencySymbol}${monthly.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td class="text-end fw-semibold text-danger">${currencySymbol}${yearly.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    </tr>`;
                    });
                }
                $('#results_deductions').html(deductionsHtml);
                // Show/hide deductions section based on data
                if (hasDeductions) {
                    $('#section_deductions').show();
                    const totalDeductionsMonthly = parseFloat(data.totals.monthly_deductions || 0);
                    const totalDeductionsYearly = parseFloat(data.totals.yearly_deductions || 0);
                    $('#total_deductions_monthly').text(currencySymbol + totalDeductionsMonthly.toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    $('#total_deductions_yearly').text(currencySymbol + totalDeductionsYearly.toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                } else {
                    $('#section_deductions').hide();
                }

                // Display One-Time
                let onetimeHtml = '';
                if (Object.keys(data.onetime).length > 0) {
                    Object.keys(data.onetime).forEach(field => {
                        const label = sections.onetime[field] || customFieldsMap[field] || field.replace(
                            /_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        const monthly = parseFloat(data.onetime[field].monthly) || 0;
                        const yearly = parseFloat(data.onetime[field].yearly) || 0;
                        const icon = getComponentIcon(field);
                        onetimeHtml += `<tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="${icon} me-2 text-secondary fs-5"></i>
                                <span>${label}</span>
                            </div>
                        </td>
                        <td class="text-end fw-semibold text-secondary">${currencySymbol}${monthly.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td class="text-end fw-semibold text-secondary">${currencySymbol}${yearly.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    </tr>`;
                    });
                } else {
                    onetimeHtml =
                        '<tr><td colspan="3" class="text-center text-muted py-4"><i class="ti ti-inbox fs-1 d-block mb-2"></i>No one-time components found</td></tr>';
                }
                $('#results_onetime').html(onetimeHtml);
                // Display total one-time from backend
                const totalOnetimeMonthly = parseFloat(data.totals.monthly_total_onetime || 0);
                const totalOnetimeYearly = parseFloat(data.totals.yearly_total_onetime || 0);
                $('#total_onetime_monthly').text(currencySymbol + totalOnetimeMonthly.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
                $('#total_onetime_yearly').text(currencySymbol + totalOnetimeYearly.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

                // Scroll to top of modal content
                $('.modal-body').animate({
                    scrollTop: 0
                }, 300);

                // Populate form fields with calculated amounts
                populateSalaryFormFromCalculation(data);

                // Show "Use These Values" button after successful calculation
                $('#useCalculatedValuesBtn').show();
            }

            // Populate salary form fields from calculator results
            function populateSalaryFormFromCalculation(data) {
                // Map calculator field names to form field names
                const fieldMapping = {
                    // Earnings - direct matches
                    'basic': 'basic',
                    'da': 'da',
                    'hra': 'hra',
                    'conveyance': 'conveyance',
                    'allowance': 'allowance',
                    'medical_allowance': 'medical_allowance',
                    'special_allowance': 'special_allowance',
                    'leave_travel_allowance': 'leave_travel_allowance',
                    'city_compensatory_allowance': 'city_compensatory_allowance',
                    'sales_target_incentives': 'sales_target_incentives',
                    'shift_allowance': 'shift_allowance',
                    'transport_allowance': 'transport_allowance',
                    'food_allowance': 'food_allowance',
                    'overtime_allowance': 'overtime_allowance',
                    'uniform_allowance': 'uniform_allowance',
                    'mobile_internet_allowance': 'mobile_internet_allowance',
                    'project_allowance': 'project_allowance',
                    'education_allowance': 'education_allowance',
                    'professional_allowance': 'professional_allowance',
                    'arrears_earnings': 'arrears_earnings',
                    'others': 'earning_others', // Form uses earning_others for earnings others, but backend expects 'others'

                    // Deductions - need mapping for employee_pf -> pf, employee_esi -> esi
                    'employee_pf': 'pf',
                    'employee_esi': 'esi',
                    'professional_tax': 'prof_tax', // Form uses prof_tax
                    'tds': 'tds',
                    'salary_advance_recovery': 'salary_advance_recovery',
                    'loan_emi_recovery': 'loan_emi_recovery',
                    'canteen_deduction': 'canteen_deduction',
                    'attendance_loss': 'attendance_loss',
                    'notice_period_recovery': 'notice_period_recovery',
                    'arrears_recovery': 'arrears_recovery',
                    'insurance_premium_employee': 'insurance_premium_employee'
                };

                // Populate earnings fields
                if (data.earnings) {
                    Object.keys(data.earnings).forEach(calcField => {
                        const formField = fieldMapping[calcField] || calcField;
                        const monthlyAmount = parseFloat(data.earnings[calcField].monthly) || 0;

                        // Set the main field value - check both new and edit forms
                        const $field = $(`input[name="${formField}"], #${formField}, input#${formField}`);
                        if ($field.length) {
                            $field.val(monthlyAmount.toFixed(2));
                        }

                        // Handle special cases for da and hra (they have _sum_amount fields)
                        if (calcField === 'da') {
                            const $daSumField = $('input[name="da_sum_amount"], #da_sum_amount');
                            if ($daSumField.length) {
                                $daSumField.val(monthlyAmount.toFixed(2));
                            }
                        }
                        if (calcField === 'hra') {
                            const $hraSumField = $('input[name="hra_sum_amount"], #hra_sum_amount');
                            if ($hraSumField.length) {
                                $hraSumField.val(monthlyAmount.toFixed(2));
                            }
                        }
                    });
                }

                // Populate deductions fields
                if (data.deductions) {
                    Object.keys(data.deductions).forEach(calcField => {
                        const formField = fieldMapping[calcField] || calcField;
                        const monthlyAmount = parseFloat(data.deductions[calcField].monthly) || 0;

                        // Set the main field value - check both new and edit forms
                        const $field = $(`input[name="${formField}"], #${formField}, input#${formField}`);
                        if ($field.length) {
                            $field.val(monthlyAmount.toFixed(2));
                        }

                        // Handle special cases for tds, esi, pf (they have _sum_amount fields)
                        if (calcField === 'tds') {
                            const $tdsSumField = $('input[name="tds_sum_amount"], #tds_sum_amount');
                            if ($tdsSumField.length) {
                                $tdsSumField.val(monthlyAmount.toFixed(2));
                            }
                        }
                        if (calcField === 'employee_esi') {
                            const $esiSumField = $('input[name="esi_sum_amount"], #esi_sum_amount');
                            if ($esiSumField.length) {
                                $esiSumField.val(monthlyAmount.toFixed(2));
                            }
                        }
                        if (calcField === 'employee_pf') {
                            const $pfSumField = $('input[name="pf_sum_amount"], #pf_sum_amount');
                            if ($pfSumField.length) {
                                $pfSumField.val(monthlyAmount.toFixed(2));
                            }
                        }
                    });
                }

                // Set salary_setting_id in the form (both new and edit forms)
                if (data.metadata && data.metadata.salary_setting_id) {
                    const $salarySettingSelect = $('#salary_setting_type, #salary_setting_types');
                    if ($salarySettingSelect.length) {
                        $salarySettingSelect.val(data.metadata.salary_setting_id).trigger('change');
                    }
                }

                // Handle custom fields if they exist
                // Custom fields in earnings
                if (data.earnings) {
                    Object.keys(data.earnings).forEach(field => {
                        if (!fieldMapping[field]) {
                            // This might be a custom field
                            const monthlyAmount = parseFloat(data.earnings[field].monthly) || 0;
                            const $customField = $(`input[name="${field}"]`);
                            if ($customField.length) {
                                $customField.val(monthlyAmount.toFixed(2));
                            }
                        }
                    });
                }

                // Custom fields in deductions
                if (data.deductions) {
                    Object.keys(data.deductions).forEach(field => {
                        if (!fieldMapping[field]) {
                            // This might be a custom field
                            const monthlyAmount = parseFloat(data.deductions[field].monthly) || 0;
                            const $customField = $(`input[name="${field}"]`);
                            if ($customField.length) {
                                $customField.val(monthlyAmount.toFixed(2));
                            }
                        }
                    });
                }
            }

            // Display salary setting details in the modal
            function displaySalarySettingDetails(settingData) {
                // Create or update the salary setting details section
                let detailsHtml = `
                <div class="card mt-3 mb-3 border-primary" id="salarySettingDetailsCard">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="ti ti-settings me-2"></i>Salary Setting Details (ID: ${settingData.id || 'N/A'})</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p class="mb-2"><strong>Template Name:</strong> <span class="text-primary">${settingData.salary_structure_name || 'N/A'}</span></p>
                                <p class="mb-2"><strong>Frequency:</strong> ${settingData.frequency || 'N/A'}</p>
                                <p class="mb-2"><strong>Status:</strong> ${settingData.status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-2"><strong>Setting ID:</strong> ${settingData.id || 'N/A'}</p>
                                ${settingData.monthly_option ? `<p class="mb-2"><strong>Monthly Option:</strong> ${settingData.monthly_option}</p>` : ''}
                                ${settingData.start_date && settingData.end_date ? `<p class="mb-2"><strong>Period:</strong> ${settingData.start_date} to ${settingData.end_date}</p>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;

                // Check if salary setting details section already exists
                if ($('#salarySettingDetailsCard').length > 0) {
                    $('#salarySettingDetailsCard').replaceWith(detailsHtml);
                } else {
                    // Prepend it to the calculator results (at the top, before summary cards)
                    $('#calculatorResults').prepend(detailsHtml);
                }
            }

            // Perform the actual calculation
            function performCalculation(ctc, salarySetting) {
                const componentTypes = salarySetting.component_types || {};
                let totalEarnings = 0;
                let totalDeductions = 0;
                let basicAmount = 0;

                // Adjust CTC based on period
                // If yearly is selected, convert to monthly first for calculation
                const monthlyCtc = calculationPeriod === 'yearly' ? ctc / 12 : ctc;
                const yearlyCtc = calculationPeriod === 'yearly' ? ctc : ctc * 12;

                // Calculate Basic Salary
                const basicValue = parseFloat(salarySetting.basic) || 0;
                if (basicValue > 0 || componentTypes['basic']) {
                    const basicType = componentTypes['basic'] || 'fixed';
                    if (basicType === 'percentage') {
                        basicAmount = (monthlyCtc * basicValue) / 100;
                    } else {
                        basicAmount = basicValue;
                    }
                    totalEarnings += basicAmount;
                }

                // Calculate all earnings and update form fields
                Object.keys(sections.earnings).forEach(fieldName => {
                    if (fieldName === 'basic') {
                        // Update basic field
                        $(`input[name="${fieldName}"]`).val(basicAmount.toFixed(2));
                        return;
                    }

                    const fieldValue = parseFloat(salarySetting[fieldName]) || 0;
                    const fieldType = componentTypes[fieldName] || 'fixed';

                    if (fieldValue > 0 || componentTypes[fieldName]) {
                        let calculatedValue = 0;
                        if (fieldType === 'percentage') {
                            calculatedValue = (basicAmount * fieldValue) / 100;
                        } else {
                            calculatedValue = fieldValue;
                        }
                        totalEarnings += calculatedValue;

                        // Update form field with calculated value
                        const $field = $(`input[name="${fieldName}"]`);
                        if ($field.length) {
                            $field.val(calculatedValue.toFixed(2));
                            // Update sum amount if it exists
                            const $sumField = $(`input[name="${fieldName}_sum_amount"]`);
                            if ($sumField.length) {
                                $sumField.val(calculatedValue.toFixed(2));
                            }
                        }
                    }
                });

                // Calculate reimbursements (usually fixed amounts)
                Object.keys(sections.reimbursements).forEach(fieldName => {
                    const fieldValue = parseFloat(salarySetting[fieldName]) || 0;
                    const fieldType = componentTypes[fieldName] || 'fixed';

                    if (fieldValue > 0 || componentTypes[fieldName]) {
                        let calculatedValue = 0;
                        if (fieldType === 'percentage') {
                            calculatedValue = (basicAmount * fieldValue) / 100;
                        } else {
                            calculatedValue = fieldValue;
                        }
                        totalEarnings += calculatedValue;
                    }
                });

                // Calculate contributions (employer side - not deducted from employee)
                // These are usually not part of net salary calculation but part of CTC

                // Calculate deductions and update form fields
                Object.keys(sections.deductions).forEach(fieldName => {
                    const fieldValue = parseFloat(salarySetting[fieldName]) || 0;
                    const fieldType = componentTypes[fieldName] || 'fixed';

                    if (fieldValue > 0 || componentTypes[fieldName]) {
                        let calculatedValue = 0;
                        if (fieldType === 'percentage') {
                            calculatedValue = (basicAmount * fieldValue) / 100;
                        } else {
                            calculatedValue = fieldValue;
                        }
                        totalDeductions += calculatedValue;

                        // Update form field with calculated value
                        const $field = $(`input[name="${fieldName}"]`);
                        if ($field.length) {
                            $field.val(calculatedValue.toFixed(2));
                            // Update sum amount if it exists
                            const $sumField = $(`input[name="${fieldName}_sum_amount"]`);
                            if ($sumField.length) {
                                $sumField.val(calculatedValue.toFixed(2));
                            }
                        }
                    }
                });

                // Calculate reimbursements and update form fields
                Object.keys(sections.reimbursements).forEach(fieldName => {
                    const fieldValue = parseFloat(salarySetting[fieldName]) || 0;
                    const fieldType = componentTypes[fieldName] || 'fixed';

                    if (fieldValue > 0 || componentTypes[fieldName]) {
                        let calculatedValue = 0;
                        if (fieldType === 'percentage') {
                            calculatedValue = (basicAmount * fieldValue) / 100;
                        } else {
                            calculatedValue = fieldValue;
                        }
                        totalEarnings += calculatedValue;

                        // Update form field
                        const $field = $(`input[name="${fieldName}"]`);
                        if ($field.length) {
                            $field.val(calculatedValue.toFixed(2));
                        }
                    }
                });

                // Calculate one-time components (usually not part of regular salary)
                // These are typically bonuses or settlements

                // Calculate net salary
                const monthlyNet = totalEarnings - totalDeductions;
                const yearlyNet = monthlyNet * 12;
                const monthlyGross = totalEarnings;
                const yearlyGross = monthlyGross * 12;
                const monthlyDeductions = totalDeductions;
                const yearlyDeductions = monthlyDeductions * 12;

                // Display results
                $('#calculated_monthly_gross').val(currencySymbol + monthlyGross.toFixed(2));
                $('#calculated_yearly_gross').val(currencySymbol + yearlyGross.toFixed(2));
                $('#calculated_monthly_deductions').val(currencySymbol + monthlyDeductions.toFixed(2));
                $('#calculated_yearly_deductions').val(currencySymbol + yearlyDeductions.toFixed(2));
                $('#calculated_monthly_net').val(currencySymbol + monthlyNet.toFixed(2));
                $('#calculated_yearly_net').val(currencySymbol + yearlyNet.toFixed(2));

                // Results now shown in modal
            }

            function fetchAndStoreSalaryData() {
                const $employeeSelect = $('#employeeSelect');
                const $salarySelect = $('#salary_setting_type');
                const $selectDeduction = $('#salary_payroll_deduction_id');
                const $selectOvertime = $('#salary_payroll_overtime_id');
                const $selectAddition = $('#salary_payroll_addition_id');

                // Show loading placeholders
                const selects = [$employeeSelect, $salarySelect, $selectDeduction, $selectOvertime,
                    $selectAddition
                ];
                selects.forEach($s => $s.html('<option value="">Loading...</option>'));

                // Check localStorage cache
                const storedData = localStorage.getItem('salaryData');
                if (storedData) {
                    const response = JSON.parse(storedData);
                    populateAllSelects(response);
                    enableAllSelectSearch();
                    return;
                }

                // Otherwise, fetch from backend
                $.ajax({
                    url: '{{ route('get.salary.employee', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            localStorage.setItem('salaryData', JSON.stringify(response));
                        }
                        populateAllSelects(response);
                        enableAllSelectSearch();
                    },
                    error: function(xhr) {
                        console.error("Error fetching data:", xhr);
                        selects.forEach($s => $s.html('<option value="">Failed to load data</option>'));
                    }
                });
            }
            //editsalaryextrafields

            $("#edittoggleSalarySettingBtn").on("click", function(e) {
                e.preventDefault();
                const $fields = $("#editsalaryextrafields");

                if ($fields.is(":visible")) {
                    // Hide
                    $fields.slideUp();
                    $(this).html('<i class="ti ti-plus me-1"></i>Add New');
                } else {
                    // Show
                    $fields.slideDown();
                    $(this).html('<i class="ti ti-minus me-1"></i>Hide Fields');
                }
            });
            $("#editdeducttoggleSalarySettingBtn").on("click", function(e) {
                e.preventDefault();
                const $fields = $("#editdeductionextraSalaryFields");

                if ($fields.is(":visible")) {
                    // Hide
                    $fields.slideUp();
                    $(this).html('<i class="ti ti-plus me-1"></i>Add New');
                } else {
                    // Show
                    $fields.slideDown();
                    $(this).html('<i class="ti ti-minus me-1"></i>Hide Fields');
                }
            });


            $("#toggleSalarySettingBtn").on("click", function(e) {
                e.preventDefault();
                const $fields = $("#extraSalaryFields");

                if ($fields.is(":visible")) {
                    // Hide
                    $fields.slideUp();
                    $(this).html('<i class="ti ti-plus me-1"></i>Add New');
                } else {
                    // Show
                    $fields.slideDown();
                    $(this).html('<i class="ti ti-minus me-1"></i>Hide Fields');
                }
            });
            $("#deducttoggleSalarySettingBtn").on("click", function(e) {
                e.preventDefault();
                const $fields = $("#deductionextraSalaryFields");

                if ($fields.is(":visible")) {
                    // Hide
                    $fields.slideUp();
                    $(this).html('<i class="ti ti-plus me-1"></i>Add New');
                } else {
                    // Show
                    $fields.slideDown();
                    $(this).html('<i class="ti ti-minus me-1"></i>Hide Fields');
                }
            });

            // 🧩 Populate all dropdowns
            function populateAllSelects(response) {
                const $employeeSelect = $('#employeeSelect');
                const $salarySelect = $('#salary_setting_type');
                const $selectDeduction = $('#salary_payroll_deduction_id');
                const $selectOvertime = $('#salary_payroll_overtime_id');
                const $selectAddition = $('#salary_payroll_addition_id');

                $employeeSelect.empty().append('<option value="">Select Employee</option>');
                $salarySelect.empty().append('<option value="">Select Salary Setting</option>');
                $selectDeduction.empty().append('<option value="">Select Deduction</option>');
                $selectOvertime.empty().append('<option value="">Select Overtime</option>');
                $selectAddition.empty().append('<option value="">Select Addition</option>');

                if (!response.status || !response.data) return;

                // 👤 Employees
                (response.data.employees || []).forEach(emp => {
                    const empName = emp.user?.name || emp.first_name || emp.name || 'Unnamed';
                    const department = emp.department_relation?.name || emp.department || 'No Dept';
                    const profileImage = emp.profile_image_path ? `{{ asset('storage') }}/${emp.profile_image_path}` : '';
                    $employeeSelect.append(
                        `<option value="${emp.id}" data-name="${empName.toLowerCase()}" data-dept="${department.toLowerCase()}" data-image="${profileImage}">
            ${empName} - ${department}
        </option>`
                    );
                });

                (response.data.salary_settings || []).forEach(sal => {
                    const settingName = sal.salary_structure_name || sal.name || 'Unnamed';
                    $salarySelect.append(`<option value="${sal.id}">${settingName}</option>`);
                });
                (response.data.getDeduction || []).forEach(ded => {
                    $selectDeduction.append(`<option value="${ded.id}">${ded.name}</option>`);
                });
                (response.data.GetOvertime || []).forEach(ot => {
                    $selectOvertime.append(`<option value="${ot.id}">${ot.name}</option>`);
                });
                (response.data.getAddition || []).forEach(add => {
                    $selectAddition.append(`<option value="${add.id}">${add.name}</option>`);
                });
            }

            function enableAllSelectSearch() {
                enableSelect2($('#employeeSelect'), 'Search by name or department...');
                enableSelect2($('#salary_setting_type'), 'Select Salary Setting');
                enableSelect2($('#salary_payroll_deduction_id, #salary_payroll_deduction_ids'), 'Select Deduction');
                enableSelect2($('#salary_payroll_overtime_id, #salary_payroll_overtime_ids'), 'Select Overtime');
                enableSelect2($('#salary_payroll_addition_id, #salary_payroll_addition_ids'), 'Select Addition');
            }

            function enableSelect2($element, placeholder) {
                if (!$element.length) {
                    // console.warn('Element not found for Select2:', placeholder);
                    return;
                }

                // Destroy existing Select2 instance if any
                if ($element.data('select2')) {
                    try {
                        $element.select2('destroy');
                    } catch (e) {
                        console.warn('Error destroying Select2:', e);
                    }
                }

                // Remove 'select' class if it exists (might conflict with other libraries)
                $element.removeClass('select');

                try {
                    $element.select2({
                        placeholder: placeholder,
                        width: '100%',
                        dropdownParent: $('#new-employee-salary'),
                        allowClear: true,
                        matcher: function(params, data) {
                            if ($.trim(params.term) === '') return data;
                            const term = params.term.toLowerCase();
                            const name = $(data.element).data('name') || '';
                            const dept = $(data.element).data('dept') || '';
                            const text = data.text.toLowerCase();
                            return (name.includes(term) || dept.includes(term) || text.includes(term)) ?
                                data : null;
                        }
                    });
                } catch (e) {
                    console.error('Error initializing Select2:', e);
                }
            }


            // Initialize when offcanvas opens
            $('#new-employee-salary').on('show.bs.offcanvas', function() {
                // Only reset if not in edit mode
                const salaryId = $('#edit_salary_id').val();
                if (!salaryId || salaryId === '') {
                    // Hide fields initially
                    clearAllFields();
                    currentSalarySettingData = null;
                    calculationPeriod = 'monthly';
                    $('.calculate-period-btn').removeClass('active');
                    $('.calculate-period-btn[data-period="monthly"]').addClass('active');
                    $('#salary_setting_type').val('').trigger('change');
                    $('#employeeSalaryFormTitle').text('Add Employee Salary');
                    $('#employeeSalaryFormSubmitBtn').text('Add Employee Salary');
                    $('#employeeSelect').prop('disabled', false);
                    // Fetch employee and salary setting data
                    fetchAndStoreSalaryData();
                } else {
                    // In edit mode, just ensure dropdowns are populated (they should already be)
                    // Don't reset the form values
                    const $salarySelect = $('#salary_setting_type');
                    if ($salarySelect.find('option').length <= 1) {
                        // Dropdown not populated yet, fetch it
                        fetchAndStoreSalaryData();
                    }
                }
            });


            $(window).on('beforeunload', function() {
                localStorage.removeItem('salaryData');
            });

            // Filter state
            let filterState = {
                department_id: '',
                designation_id: '',
                sort_order: '', // Clear default sort order
                date_range: ''
            };

            // Clear date range on initial load to ensure no default filter is applied
            $('.bookingrange').val('');

            // Store filter names for display
            let filterNames = {
                department_name: '',
                designation_name: ''
            };

            // Load filter options (departments and designations)
            function loadFilterOptions() {
                $.ajax({
                    url: "{{ route('salaries.filter.options', ['box_slug' => request()->route('box_slug')]) }}",
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.data) {
                            // Populate departments
                            const departmentMenu = $('#departmentFilterMenu');
                            departmentMenu.find('.department-filter-item').not(':first').remove();
                            response.data.departments.forEach(function(dept) {
                                departmentMenu.append(`
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1 department-filter-item" data-id="${dept.id}" data-name="${dept.name}">${dept.name}</a>
                            </li>
                        `);
                            });

                            // Populate designations
                            const designationMenu = $('#designationFilterMenu');
                            designationMenu.find('.designation-filter-item').not(':first').remove();
                            response.data.designations.forEach(function(desig) {
                                designationMenu.append(`
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1 designation-filter-item" data-id="${desig.id}" data-name="${desig.name}">${desig.name}</a>
                            </li>
                        `);
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading filter options:', error);
                    }
                });
            }

            // Update active filters display
            function updateActiveFilters() {
                const container = $('#activeFiltersContainer');
                container.empty();

                // Department filter badge
                if (filterState.department_id) {
                    container.append(`
                <span class="badge badge-primary me-2 d-inline-flex align-items-center" style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">
                    <i class="ti ti-filter me-1"></i>Department: ${filterNames.department_name}
                    <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.6rem;" data-filter="department" aria-label="Remove"></button>
                </span>
            `);
                }

                // Designation filter badge
                if (filterState.designation_id) {
                    container.append(`
                <span class="badge badge-primary me-2 d-inline-flex align-items-center" style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">
                    <i class="ti ti-filter me-1"></i>Designation: ${filterNames.designation_name}
                    <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.6rem;" data-filter="designation" aria-label="Remove"></button>
                </span>
            `);
                }

                // Date range filter badge
                const dateRange = $('.bookingrange').val();
                if (dateRange && dateRange.trim() !== '') {
                    container.append(`
                <span class="badge badge-primary me-2 d-inline-flex align-items-center" style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">
                    <i class="ti ti-calendar me-1"></i>Date: ${dateRange}
                    <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.6rem;" data-filter="date" aria-label="Remove"></button>
                </span>
            `);
                }
            }

            // Load filter options on page load
            loadFilterOptions();

            // Department filter click handler
            $(document).on('click', '.department-filter-item', function() {
                const departmentId = $(this).data('id');
                const departmentName = $(this).data('name') || $(this).text().trim();

                if (departmentId === '') {
                    filterState.department_id = '';
                    filterNames.department_name = '';
                    $('#departmentFilterBtn').html('<i class="ti ti-filter me-1"></i>Department: All');
                } else {
                    filterState.department_id = departmentId;
                    filterNames.department_name = departmentName;
                    $('#departmentFilterBtn').html(
                        `<i class="ti ti-filter me-1"></i>Department: ${departmentName}`);
                }

                updateActiveFilters();
                salaryTable.draw();
            });

            // Designation filter click handler
            $(document).on('click', '.designation-filter-item', function() {
                const designationId = $(this).data('id');
                const designationName = $(this).data('name') || $(this).text().trim();

                if (designationId === '') {
                    filterState.designation_id = '';
                    filterNames.designation_name = '';
                    $('#designationFilterBtn').html('<i class="ti ti-filter me-1"></i>Designation: All');
                } else {
                    filterState.designation_id = designationId;
                    filterNames.designation_name = designationName;
                    $('#designationFilterBtn').html(
                        `<i class="ti ti-filter me-1"></i>Designation: ${designationName}`);
                }

                updateActiveFilters();
                salaryTable.draw();
            });

            // Sort filter click handler
            $(document).on('click', '.sort-filter-item', function() {
                const sortOrder = $(this).data('sort');
                const sortText = $(this).text().trim();
                filterState.sort_order = sortOrder;

                $('#sortFilterBtn').html(`<i class="ti ti-arrow-up-down me-1"></i>Sort: ${sortText}`);
                salaryTable.draw();
            });

            // Individual filter remove handler
            $(document).on('click', '.btn-close[data-filter]', function() {
                const filterType = $(this).data('filter');

                if (filterType === 'department') {
                    filterState.department_id = '';
                    filterNames.department_name = '';
                    $('#departmentFilterBtn').html('<i class="ti ti-filter me-1"></i>Department: All');
                } else if (filterType === 'designation') {
                    filterState.designation_id = '';
                    filterNames.designation_name = '';
                    $('#designationFilterBtn').html('<i class="ti ti-filter me-1"></i>Designation: All');
                } else if (filterType === 'date') {
                    if ($('.bookingrange').data('daterangepicker')) {
                        $('.bookingrange').data('daterangepicker').setStartDate(moment());
                        $('.bookingrange').data('daterangepicker').setEndDate(moment());
                    }
                    $('.bookingrange').val('');
                }

                updateActiveFilters();
                salaryTable.draw();
            });

            // Clear all filters
            $('#clearFiltersBtn').on('click', function() {
                filterState.department_id = '';
                filterState.designation_id = '';
                filterState.sort_order = '';
                filterState.date_range = '';
                filterNames.department_name = '';
                filterNames.designation_name = '';

                // Clear date range picker
                if ($('.bookingrange').data('daterangepicker')) {
                    $('.bookingrange').data('daterangepicker').setStartDate(moment());
                    $('.bookingrange').data('daterangepicker').setEndDate(moment());
                }
                $('.bookingrange').val('');

                // Reset filter button texts
                $('#departmentFilterBtn').html('<i class="ti ti-filter me-1"></i>Department: All');
                $('#designationFilterBtn').html('<i class="ti ti-filter me-1"></i>Designation: All');
                $('#sortFilterBtn').html('<i class="ti ti-arrow-up-down me-1"></i>Sort: All');

                updateActiveFilters();
                salaryTable.draw();
            });

            // Column definitions with names
            const columnDefinitions = [{
                    index: 0,
                    name: 'Checkbox',
                    visible: true
                },
                {
                    index: 1,
                    name: 'Emp ID',
                    visible: true
                },
                {
                    index: 2,
                    name: 'Name',
                    visible: true
                },
                {
                    index: 3,
                    name: 'Email',
                    visible: true
                },
                {
                    index: 4,
                    name: 'Phone',
                    visible: true
                },
                {
                    index: 5,
                    name: 'Designation',
                    visible: true
                },
                {
                    index: 6,
                    name: 'Joining Date',
                    visible: true
                },
                {
                    index: 7,
                    name: 'Salary',
                    visible: true
                },
                {
                    index: 8,
                    name: 'Final Salary',
                    visible: true
                },
                {
                    index: 9,
                    name: 'Created Date',
                    visible: true
                },
                {
                    index: 10,
                    name: 'Updated Date',
                    visible: true
                },
                {
                    index: 11,
                    name: 'Created By',
                    visible: true
                },
                {
                    index: 12,
                    name: 'Updated By',
                    visible: true
                },
                {
                    index: 13,
                    name: 'Payslip',
                    visible: true
                },
                {
                    index: 14,
                    name: 'Actions',
                    visible: true
                }
            ];

            // Page identifier for column preferences
            const pageIdentifier = 'employee_salary';

            // Load column visibility preferences from database
            function loadColumnPreferences(callback) {
                $.ajax({
                    url: '{{ route('settings.column.preferences.get', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                    data: {
                        page_identifier: pageIdentifier
                    },
                    async: false, // Make synchronous to ensure preferences are loaded before table initialization
                    success: function(response) {
                        if (response.success && response.data && Array.isArray(response.data) &&
                            response.data.length > 0) {
                            try {
                                const preferences = response.data;
                                preferences.forEach(pref => {
                                    // Convert string index to number
                                    const prefIndex = parseInt(pref.index);
                                    const col = columnDefinitions.find(c => c.index ===
                                        prefIndex);
                                    if (col) {
                                        // Handle both boolean and string values ("true"/"false")
                                        let newVisible;
                                        if (typeof pref.visible === 'string') {
                                            // Convert string "true"/"false" to boolean
                                            // "true" or "1" → true, anything else (including "false") → false
                                            newVisible = (pref.visible.toLowerCase() ===
                                                'true' || pref.visible === '1');
                                        } else if (typeof pref.visible === 'boolean') {
                                            newVisible = pref.visible;
                                        } else {
                                            newVisible = pref.visible !== undefined ? Boolean(
                                                pref.visible) : true;
                                        }
                                        col.visible = newVisible;
                                        console.log(
                                            `Column ${prefIndex} (${col.name}): visible = ${col.visible} (boolean: ${typeof col.visible === 'boolean'}) (from API: "${pref.visible}", type: ${typeof pref.visible})`
                                            );
                                    } else {
                                        console.warn(
                                            `Column with index ${prefIndex} not found in columnDefinitions`
                                            );
                                    }
                                });
                                console.log('Updated columnDefinitions after API load:', JSON.stringify(
                                    columnDefinitions, null, 2));
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
                    url: '{{ route('settings.column.preferences.save', ['box_slug' => request()->route('box_slug')]) }}',
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
                if (typeof salaryTable !== 'undefined' && salaryTable && $.fn.DataTable.isDataTable(
                    '#salaryTable')) {
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

            // Global salaryTable variable - must be declared before any functions that use it
            let salaryTable;

            // Load preferences on page load BEFORE table initialization (synchronous)
            loadColumnPreferences(function() {
                // Preferences loaded, now initialize table
                initializeSalaryTable();
            });

            // DataTable Initialization function
            function initializeSalaryTable() {
                // Check if DataTable already exists and destroy it first
                if ($.fn.DataTable.isDataTable('#salaryTable')) {
                    $('#salaryTable').DataTable().destroy();
                }

                // Log column visibility before initialization to verify preferences were loaded
                console.log('Initializing DataTable with column visibility:', columnDefinitions.map(c => ({
                    index: c.index,
                    name: c.name,
                    visible: c.visible
                })));
                console.log('Column 0 (Checkbox) visible value:', columnDefinitions[0].visible, 'Type:',
                    typeof columnDefinitions[0].visible, 'Is boolean:', typeof columnDefinitions[0].visible ===
                    'boolean');
                console.log('Column 1 (Emp ID) visible value:', columnDefinitions[1].visible, 'Type:',
                    typeof columnDefinitions[1].visible, 'Is boolean:', typeof columnDefinitions[1].visible ===
                    'boolean');

                // Ensure all values are proper booleans - directly use the converted values from columnDefinitions
                const col0Visible = Boolean(columnDefinitions[0].visible);
                const col1Visible = Boolean(columnDefinitions[1].visible);
                console.log('Column 0 final boolean value:', col0Visible, 'Type:', typeof col0Visible);
                console.log('Column 1 final boolean value:', col1Visible, 'Type:', typeof col1Visible);

                // Create columns array with explicit boolean values to ensure DataTable receives correct types
                const columnsConfig = [{
                        data: 'id',
                        orderable: false,
                        visible: col0Visible, // Use the boolean value directly
                        render: function(data, type, row) {
                            // Use row.emp_id which contains the box_user_id
                            return `<div class="form-check form-check-md">
                                <input class="form-check-input salary-checkbox" type="checkbox" value="${row.emp_id}" />
                            </div>`;
                        }
                    },
                    {
                        data: 'emp_id',
                        visible: col1Visible
                    }, // Use the boolean value directly
                    {
                        data: 'name',
                        visible: Boolean(columnDefinitions[2].visible),
                        render: function(data, type, row) {
                            const initial = data ? data.charAt(0).toUpperCase() : '?';
                            if (row.full_profile_image) {
                                return `<div class="d-flex align-items-center file-name-icon">
                                    <a href="#" class="avatar avatar-md">
                                        <img src="${row.full_profile_image}" class="img-fluid rounded-circle" alt="img" />
                                    </a>
                                    <div class="ms-2">
                                        <h6 class="fw-medium"><a href="#">${data}</a></h6>
                                        <span class="d-block mt-1">${row.designation}</span>
                                    </div>
                                </div>`;
                            } else {
                                const color = getColorFromName(data);
                                return `<div class="d-flex align-items-center file-name-icon">
                                    <div class="avatar avatar-md letter-avatar"
                                         style="background-color: ${color}; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; border-radius: 50%; width: 40px; height: 40px;">
                                        ${initial}
                                    </div>
                                    <div class="ms-2">
                                        <h6 class="fw-medium"><a href="#">${data}</a></h6>
                                        <span class="d-block mt-1">${row.designation}</span>
                                    </div>
                                </div>`;
                            }
                        }
                    },
                    {
                        data: 'email',
                        visible: Boolean(columnDefinitions[3].visible)
                    },
                    {
                        data: 'phone',
                        visible: Boolean(columnDefinitions[4].visible)
                    },
                    {
                        data: 'designation',
                        visible: Boolean(columnDefinitions[5].visible)
                    },
                    {
                        data: 'joining_date',
                        visible: Boolean(columnDefinitions[6].visible)
                    },
                    {
                        data: 'net_salary',
                        visible: Boolean(columnDefinitions[7].visible),
                        render: function(data) {
                            return currencySymbol + data;
                        }
                    },
                    {
                        data: 'final_salary',
                        visible: Boolean(columnDefinitions[8].visible),
                        render: function(data) {
                            return data && data !== '-' ? currencySymbol + data : '-';
                        }
                    },
                    {
                        data: 'created_at',
                        visible: Boolean(columnDefinitions[9].visible),
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'updated_at',
                        visible: Boolean(columnDefinitions[10].visible),
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'created_by',
                        visible: Boolean(columnDefinitions[11].visible),
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'updated_by',
                        visible: Boolean(columnDefinitions[12].visible),
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'salary_exists',
                        visible: Boolean(columnDefinitions[13].visible),
                        render: function(data, type, row) {
                            // Since we're querying from salaries table, salary always exists
                            return `<a href="javascript:void(0);" class="badge badge-dark badge-md generate-slip-individual-btn" data-user-id="${row.box_user_id}">Generate Slip</a>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        visible: Boolean(columnDefinitions[14].visible),
                        render: function(data, type, row) {
                            // Since we're querying from salaries table, salary always exists
                            const salaryId = row.id;
                            const salarySettingId = row.salary_settings_id || (row.latest_salary ? row
                                .latest_salary.salary_settings_id : null);

                            return `<div class="action-icon d-inline-flex">
                                    <a href="#"
                                       class="me-2 view-salary-details-btn"
                                   data-id="${salaryId}"
                                   data-salary-setting-id="${salarySettingId}"
                                   data-emp-name="${row.name || ''}"
                                   data-image="${row.full_profile_image || ''}"
                                       title="View Salary Details">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="#"
                                       class="me-2 edit-salary-btn"
                                   data-id="${salaryId}"
                                       data-bs-toggle="offcanvas"
                                       data-bs-target="#new-employee-salary"
                                       title="Edit Salary">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <a href="javascript:void(0);"
                                       class="text-danger delete-salary-btn"
                                   data-id="${salaryId}"
                                       data-bs-toggle="modal"
                                       data-bs-target="#delete_modal"
                                       title="Delete Salary">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                </div>`;
                        }
                    }
                ];

                console.log('Columns config with visibility:', columnsConfig.map(c => ({
                    data: c.data,
                    visible: c.visible,
                    type: typeof c.visible
                })));

                salaryTable = $('#salaryTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('salaries.list', ['box_slug' => request()->route('box_slug')]) }}",
                        data: function(d) {
                            // Get date range from the date picker
                            const dateRange = $('.bookingrange').val();
                            if (dateRange && dateRange.includes(' - ')) {
                                const dates = dateRange.split(' - ');
                                if (dates.length === 2) {
                                    // Convert date format from M/D/YYYY to yyyy-mm-dd
                                    // Handle both M/D/YYYY and MM/DD/YYYY formats
                                    const formatDate = function(dateStr) {
                                        const parts = dateStr.trim().split('/');
                                        if (parts.length === 3) {
                                            const month = parts[0].padStart(2, '0');
                                            const day = parts[1].padStart(2, '0');
                                            const year = parts[2];
                                            return `${year}-${month}-${day}`;
                                        }
                                        return null;
                                    };
                                    const fromDate = formatDate(dates[0]);
                                    const toDate = formatDate(dates[1]);
                                    if (fromDate) d.from_date = fromDate;
                                    if (toDate) d.to_date = toDate;
                                }
                            }

                            // Add filter parameters
                            if (filterState.department_id) {
                                d.department_id = filterState.department_id;
                            }
                            if (filterState.designation_id) {
                                d.designation_id = filterState.designation_id;
                            }
                            if (filterState.sort_order) {
                                d.sort_order = filterState.sort_order;
                            }
                        }
                    },
                    columns: columnsConfig,
                    drawCallback: function() {
                        // Update active filters display after each draw
                        updateActiveFilters();
                        // Note: Don't call applyColumnVisibility here to avoid infinite loop
                    }
                });
            } // End of initializeSalaryTable function

            // Note: Column visibility is already set during DataTable initialization via visible: columnDefinitions[X].visible
            // No need to call applyColumnVisibility here as it would be redundant
            // Only update active filters after table is ready
            setTimeout(function() {
                updateActiveFilters();
            }, 100);


            let debounceTimeout = null;
            $('#salaryTable_filter input').unbind().bind('keyup', function() {
                const searchTerm = this.value;

                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(function() {
                    salaryTable.search(searchTerm).draw();
                }, 500);
            });

            // Reload DataTable when date range changes
            $(document).on('apply.daterangepicker', '.bookingrange', function(ev, picker) {
                updateActiveFilters();
                salaryTable.draw();
            });

            $(document).on('cancel.daterangepicker', '.bookingrange', function(ev, picker) {
                updateActiveFilters();
                salaryTable.draw();
            });

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
                if (salaryTable && $.fn.DataTable.isDataTable('#salaryTable')) {
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

            // Global Search Functionality
            $('#searchBtn').on('click', function() {
                const searchValue = $('#globalSearch').val();
                if (salaryTable) {
                    salaryTable.search(searchValue).draw();
                }
            });

            $('#globalSearch').on('keyup', function(e) {
                if (e.key === 'Enter') {
                    $('#searchBtn').trigger('click');
                }
                // Optional: Live search
                if ($(this).val() === '') {
                    $('#searchBtn').trigger('click');
                }
            });

            // Export Salary Data
            const exportFields = [{
                    id: 'emp_id',
                    name: 'Emp ID'
                },
                {
                    id: 'name',
                    name: 'Name'
                },
                {
                    id: 'email',
                    name: 'Email'
                },
                {
                    id: 'phone',
                    name: 'Phone'
                },
                {
                    id: 'designation',
                    name: 'Designation'
                },
                {
                    id: 'joining_date',
                    name: 'Joining Date'
                },
                {
                    id: 'salary',
                    name: 'Annual CTC'
                },
                {
                    id: 'final_salary',
                    name: 'Final Salary'
                },
                {
                    id: 'created_at',
                    name: 'Created Date'
                },
                {
                    id: 'updated_at',
                    name: 'Updated Date'
                },
                {
                    id: 'created_by',
                    name: 'Created By'
                },
                {
                    id: 'updated_by',
                    name: 'Updated By'
                }
            ];

            function populateExportFieldsModal() {
                const list = $('#exportFieldsList');
                list.empty();
                exportFields.forEach(field => {
                    list.append(`
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input export-field-toggle" type="checkbox"
                                    value="${field.id}" id="export_field_${field.id}" checked>
                                <label class="form-check-label" for="export_field_${field.id}">
                                    ${field.name}
                                </label>
                            </div>
                        </div>
                    `);
                });
            }

            $('#exportPdfBtn').on('click', function() {
                $('#exportType').val('pdf');
                populateExportFieldsModal();
                $('#exportSalaryModal').modal('show');
            });

            $('#exportExcelBtn').on('click', function() {
                $('#exportType').val('excel');
                populateExportFieldsModal();
                $('#exportSalaryModal').modal('show');
            });

            $('#selectAllExportFields').on('click', function() {
                $('.export-field-toggle').prop('checked', true);
            });

            $('#deselectAllExportFields').on('click', function() {
                $('.export-field-toggle').prop('checked', false);
            });

            $('#confirmExportBtn').on('click', function(e) {
                const selectedFields = [];
                $('.export-field-toggle:checked').each(function() {
                    selectedFields.push($(this).val());
                });

                if (selectedFields.length === 0) {
                    showErrorToast('Please select at least one field to export.');
                    return;
                }

                const exportType = $('#exportType').val();

                // Build query parameters
                const params = new URLSearchParams();
                params.append('export_type', exportType);
                params.append('department_id', filterState.department_id || '');
                params.append('designation_id', filterState.designation_id || '');
                params.append('sort_order', filterState.sort_order || '');
                params.append('date_range', $('.bookingrange').val() || '');
                params.append('search', salaryTable ? salaryTable.search() : '');

                // Add Month and Year from modal filters
                params.append('export_month', $('#export_month').val() || '');
                params.append('export_year', $('#export_year').val() || '');

                selectedFields.forEach(field => {
                    params.append('fields[]', field);
                });

                const exportUrl = '{{ route("employe.salary.export", ["box_slug" => request()->route("box_slug")]) }}' + '?' + params.toString();

                $('#exportSalaryModal').modal('hide');
                showSuccessToast('Preparing your ' + exportType.toUpperCase() + ' export. The download will start shortly.');

                // Trigger download
                window.location.href = exportUrl;
            });


            // View Salary Details - Open Calculator Modal with saved CTC and Salary Setting
            $(document).on('click', '.view-salary-details-btn', function() {
                const salaryId = $(this).data('id');
                const empName = $(this).data('emp-name');
                const empImage = $(this).data('image');
                setSalaryCalculatorEmployeeName(empName, empImage);


                // Fetch salary details to get the CTC value and salary setting
                $.ajax({
                    url: '{{ route('employe.salary.details', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                    data: {
                        emp_id: salaryId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success && res.data) {
                            const data = res.data;

                            // Get saved values from database
                            let ctcAmount = null;
                            let period = 'monthly'; // Default period

                            // Priority 1: Check snapshot for original calculation data
                            if (data.salary_settings_snapshot && data.salary_settings_snapshot._calculated_amounts) {
                                const calcData = data.salary_settings_snapshot._calculated_amounts;
                                if (calcData.ctc_amount) {
                                    ctcAmount = parseFloat(calcData.ctc_amount);
                                    period = calcData.period || 'monthly';
                                }
                            }

                            // Priority 2: Use ctc_amount from salary record
                            if (!ctcAmount || ctcAmount <= 0) {
                                if (data.ctc_amount !== null && data.ctc_amount !== undefined &&
                                    data.ctc_amount !== '') {
                                    ctcAmount = parseFloat(data.ctc_amount);
                                    if (isNaN(ctcAmount)) {
                                        ctcAmount = null;
                                    }
                                }
                            }

                            const salarySettingId = data.salary_settings_id;
                            const monthlyGross = parseFloat(data.gross_salary) || 0;

                            // Priority 3: If ctc_amount is still missing, use gross_salary * 12 * 1.13 as fallback
                            if (!ctcAmount || ctcAmount <= 0) {
                                if (monthlyGross > 0) {
                                    ctcAmount = monthlyGross * 12 * 1.13; // Approximate yearly CTC from monthly gross
                                    period = 'yearly'; // Fallback is yearly
                                } else {
                                    showErrorToast('Salary data is incomplete. CTC amount is missing. Please edit the salary and save it with a valid CTC amount.');
                                    return;
                                }
                            }

                            if (!salarySettingId) {
                                showErrorToast('Salary data is incomplete. Salary setting template is missing. Please edit the salary and select a salary setting template.');
                                return;
                            }

                            // Determine the period to send to API
                            // Use period from snapshot if available, otherwise infer from value
                            let periodToSend = period;

                            // If period not determined from snapshot, infer from ctc_amount value
                            if (!periodToSend || periodToSend === 'monthly') {
                                // If ctc_amount >= 100000, assume it's yearly
                                if (ctcAmount >= 100000) {
                                    periodToSend = 'yearly';
                                } else {
                                    // Less than 100000, assume monthly
                                    periodToSend = 'monthly';
                                }
                            }

                            // Send the original ctc_amount value (API will handle conversion internally)
                            const ctcToSend = ctcAmount;

                            // Show loading in calculator modal
                            const modal = new bootstrap.Modal(document.getElementById(
                                'salaryCalculatorModal'));
                            modal.show();
                            $('#calculatorLoading').show();
                            $('#calculatorResults').hide();

                            // Call calculate API with ctc_amount from salary table
                            $.ajax({
                                url: '{{ route('salary-settings.calculate', ['box_slug' => request()->route('box_slug')]) }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    ctc: ctcToSend, // Use the determined CTC value
                                    period: periodToSend, // Use the determined period (always monthly for API)
                                    salary_setting_id: salarySettingId
                                },
                                success: function(response) {
                                    $('#calculatorLoading').hide();
                                    if (response.success && response.data) {
                                        // Display calculation results
                                        displayCalculationResults(response.data);

                                        // Hide "Use These Values" button when viewing details (read-only mode)
                                        $('#useCalculatedValuesBtn').hide();

                                        // Show the results container
                                        $('#calculatorResults').show();

                                        // Then call get-details API to fetch and display salary setting details
                                        $.ajax({
                                            url: '{{ route('salary-settings.get-details', ['box_slug' => request()->route('box_slug')]) }}',
                                            method: 'GET',
                                            data: {
                                                id: salarySettingId
                                            },
                                            success: function(
                                                getDetailsResponse) {
                                                if (getDetailsResponse
                                                    .success &&
                                                    getDetailsResponse
                                                    .data) {
                                                    // Store and display salary setting details
                                                    currentSalarySettingData
                                                        =
                                                        getDetailsResponse
                                                        .data;
                                                    displaySalarySettingDetails
                                                        (getDetailsResponse
                                                            .data);
                                                    console.log(
                                                        'Salary Setting Details:',
                                                        getDetailsResponse
                                                        .data);
                                                }
                                            },
                                            error: function(xhr) {
                                                console.error(
                                                    "Failed to fetch salary setting details:",
                                                    xhr);
                                                // Don't show error to user, just log it
                                            }
                                        });
                                    } else {
                                        $('#calculatorResults').html(
                                            '<div class="alert alert-danger">Failed to calculate salary. Please try again.</div>'
                                            ).show();
                                    }
                                },
                                error: function(xhr) {
                                    $('#calculatorLoading').hide();
                                    console.error("Failed to calculate salary:",
                                        xhr);
                                    let errorMsg =
                                        'Failed to calculate salary. Please try again.';
                                    if (xhr.responseJSON && xhr.responseJSON
                                        .message) {
                                        errorMsg = xhr.responseJSON.message;
                                    }
                                    $('#calculatorResults').html(
                                        '<div class="alert alert-danger">' +
                                        errorMsg + '</div>').show();
                                }
                            });
                        } else {
                            showErrorToast('Failed to load salary details.');
                        }
                    },
                    error: function() {
                        showErrorToast('An error occurred while loading salary details.');
                    }
                });
            });

            // Edit Salary - Open Add Modal with Edit Mode
            $(document).on('click', '.edit-salary-btn', function(e) {
                e.preventDefault();
                const salaryId = $(this).data('id');

                // Reset form to add mode first
                $('#employeeSalaryFormTitle').text('Add Employee Salary');
                $('#employeeSalaryFormSubmitBtn').text('Add Employee Salary');
                $('#edit_salary_id').val('');
                $('#employeeSelect').val('').trigger('change');
                $('#ctc_input').val('');
                $('#salary_setting_type').val('').trigger('change');

                // First, ensure salary settings dropdown is populated
                function populateSalarySettingAndOpen(salarySettingsId) {
                    // Check if dropdown already has options (from localStorage or already loaded)
                    const $salarySelect = $('#salary_setting_type');
                    const hasOptions = $salarySelect.find('option').length >
                    1; // More than just the default option

                    if (hasOptions) {
                        // Dropdown already populated, set the value
                        $salarySelect.val(salarySettingsId).trigger('change');
                        openOffcanvas();
                    } else {
                        // Dropdown not populated yet, fetch and populate first
                        // Check localStorage first
                        const storedData = localStorage.getItem('salaryData');
                        if (storedData) {
                            try {
                                const response = JSON.parse(storedData);
                                if (response.status && response.data) {
                                    populateAllSelects(response);
                                    enableAllSelectSearch();
                                    $salarySelect.val(salarySettingsId).trigger('change');
                                    openOffcanvas();
                                    return;
                                }
                            } catch (e) {
                                console.error('Error parsing stored salary data:', e);
                            }
                        }

                        // Fetch from API
                        $.ajax({
                            url: '{{ route('get.salary.employee', ['box_slug' => request()->route('box_slug')]) }}',
                            method: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                if (response.status) {
                                    localStorage.setItem('salaryData', JSON.stringify(
                                    response));
                                }
                                populateAllSelects(response);
                                enableAllSelectSearch();
                                $salarySelect.val(salarySettingsId).trigger('change');
                                openOffcanvas();
                            },
                            error: function(xhr) {
                                console.error("Error fetching salary settings:", xhr);
                                alert('Failed to load salary settings. Please try again.');
                            }
                        });
                    }
                }

                function openOffcanvas() {
                    // Open offcanvas
                    const offcanvas = new bootstrap.Offcanvas(document.getElementById(
                        'new-employee-salary'));
                    offcanvas.show();
                }

                // Fetch salary details
                $.ajax({
                    url: '{{ route('employe.salary.details', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                    data: {
                        emp_id: salaryId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success && res.data) {
                            const data = res.data;
                            const user = data.boxuser?.user;

                            // Change to edit mode
                            $('#employeeSalaryFormTitle').text('Edit Employee Salary');
                            $('#employeeSalaryFormSubmitBtn').text('Update Employee Salary');
                            $('#edit_salary_id').val(data.id);

                            // Populate employee
                            if (user && data.box_user_id) {
                                const option = new Option(user.name, data.box_user_id, true,
                                    true);
                                const profileImage = data.boxuser?.profile_image_path ? `{{ asset('storage') }}/${data.boxuser.profile_image_path}` : '';
                                $(option).attr('data-image', profileImage);
                                $('#employeeSelect').empty().append(option).trigger('change');
                                $('#employeeSelect').prop('disabled',
                                true); // Disable in edit mode
                            }

                            // Populate CTC amount from saved value
                            const ctcAmount = parseFloat(data.ctc_amount) || 0;
                            if (ctcAmount > 0) {
                                $('#ctc_input').val(ctcAmount.toFixed(2));
                            } else {
                                // Fallback: if ctc_amount is missing, use gross_salary * 12 * 1.13 as approximate yearly CTC
                                const monthlyGross = parseFloat(data.gross_salary) || 0;
                                const approximateYearlyCTC = monthlyGross * 12 * 1.13;
                                $('#ctc_input').val(approximateYearlyCTC.toFixed(2));
                            }

                            // Populate salary setting dropdown first, then set value and open
                            if (data.salary_settings_id) {
                                populateSalarySettingAndOpen(data.salary_settings_id);
                            } else {
                                openOffcanvas();
                            }
                        } else {
                            showErrorToast('Failed to load salary details.');
                        }
                    },
                    error: function() {
                        showErrorToast('An error occurred while loading salary details.');
                    }
                });
            });

            // Old edit handler - keeping for reference but not used
            $(document).on('click', '.edit-salary-btn-old', function() {
                let empId = $(this).data('id');
                const $salarySelect = $('#edit-employee-salary select#edit_salary_setting_type');
                const $select_deduction_ids = $(
                    '#edit-employee-salary select#salary_payroll_deduction_ids');
                const $select_overtime_ids = $('#edit-employee-salary select#salary_payroll_overtime_ids');
                const $select_addition_id = $('#edit-employee-salary select#salary_payroll_addition_ids');

                function populateSalarySettings(data, selectedIds = {}) {
                    // selectedIds = { salarySettingId, deductionId, overtimeId, additionId }
                    console.log('setting', data);

                    const settings = data.salary_settings || [];
                    const getAddition = data.getAddition || [];
                    const getDeduction = data.getDeduction || [];
                    const GetOvertime = data.GetOvertime || [];

                    // Clear and set default options
                    $('#edit-employee-salary select#edit_salary_setting_type').empty().append(
                        '<option value="">Select Salary Setting</option>');
                    $select_deduction_ids.empty().append(
                        '<option value="">Select Deduction Setting</option>');
                    $select_overtime_ids.empty().append(
                        '<option value="">Select Overtime Setting</option>');
                    $select_addition_id.empty().append('<option value="">Select Addition Setting</option>');

                    // Populate Salary Settings
                    if (settings.length > 0) {
                        settings.forEach(function(sal) {
                            const isSelected = sal.id == selectedIds.salarySettingId ? 'selected' :
                                '';
                            $('#edit-employee-salary select#edit_salary_setting_type').append(
                                `<option value="${sal.id}" ${isSelected}>${sal.name.charAt(0).toUpperCase() + sal.name.slice(1)}</option>`
                            );
                        });
                    } else {
                        $('#edit-employee-salary select#edit_salary_setting_type').append(
                            '<option value="">No salary settings found</option>');
                    }

                    // Populate Deductions
                    if (getDeduction.length > 0) {
                        getDeduction.forEach(function(ded) {
                            const isSelected = ded.id == selectedIds.deductionId ? 'selected' : '';
                            $select_deduction_ids.append(
                                `<option value="${ded.id}" ${isSelected}>${ded.name.charAt(0).toUpperCase() + ded.name.slice(1)}</option>`
                            );
                        });
                    } else {
                        $select_deduction_ids.append('<option value="">No deduction found</option>');
                    }

                    // Populate Overtime
                    if (GetOvertime.length > 0) {
                        GetOvertime.forEach(function(ot) {
                            const isSelected = ot.id == selectedIds.overtimeId ? 'selected' : '';
                            $select_overtime_ids.append(
                                `<option value="${ot.id}" ${isSelected}>${ot.name.charAt(0).toUpperCase() + ot.name.slice(1)}</option>`
                            );
                        });
                    } else {
                        $select_overtime_ids.append('<option value="">No overtime found</option>');
                    }

                    // Populate Addition
                    if (getAddition.length > 0) {
                        getAddition.forEach(function(add) {
                            const isSelected = add.id == selectedIds.additionId ? 'selected' : '';
                            $select_addition_id.append(
                                `<option value="${add.id}" ${isSelected}>${add.name.charAt(0).toUpperCase() + add.name.slice(1)}</option>`
                            );
                        });
                    } else {
                        $select_addition_id.append('<option value="">No addition found</option>');
                    }

                    // Trigger change in case of select plugins (Select2/Bootstrap Select)

                }

                let storedData = localStorage.getItem('salaryData');
                if (storedData) {
                    try {
                        const response = JSON.parse(storedData);
                        if (response.status && response.data) {
                            populateSalarySettings(response.data);
                        } else {
                            populateSalarySettings([]);
                            fetchSalarySettingsFromAPI();
                        }
                    } catch (e) {
                        fetchSalarySettingsFromAPI();
                    }
                } else {
                    fetchSalarySettingsFromAPI();
                }

                function fetchSalarySettingsFromAPI() {
                    $.ajax({
                        url: '{{ route('get.salary.employee', ['box_slug' => request()->route('box_slug')]) }}',
                        type: 'GET',
                        success: function(res) {
                            if (res.status && res.data) {
                                populateSalarySettings(res.data);
                                localStorage.setItem('salaryData', JSON.stringify(res));
                            } else {
                                populateSalarySettings([]);
                            }
                        },
                        error: function() {
                            $salarySelect.html(
                                '<option value="">Failed to load salary settings</option>');
                        }
                    });
                }
                $.ajax({
                    url: '{{ route('employe.salary.details', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                    data: {
                        emp_id: empId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success) {
                            const data = res.data;
                            const user = data.boxuser?.user;
                            if (user) {
                                const option = new Option(user.name, data.boxuser.id, true,
                                    true);
                                const profileImage = data.boxuser?.profile_image_path ? `{{ asset('storage') }}/${data.boxuser.profile_image_path}` : '';
                                $(option).attr('data-image', profileImage);
                                $('#edit-employee-salary select#emp_id').empty().append(option)
                                    .trigger('change');
                                $('#edit-employee-salary select#emp_id').prop('disabled', true);
                            }
                            //                 $('#edit-employee-salary input#salary_payroll_deduction_ids').val(data.id);
                            // $('#edit-employee-salary input#salary_payroll_overtime_ids').val(data.id);
                            // $('#edit-employee-salary input#salary_payroll_overtime_ids').val(data.id);

                            $('#edit-employee-salary input#salary_id').val(data.id);
                            $('#edit-employee-salary input#edit_box_user_id').val(data
                                .box_user_id);

                            // Calculate approximate yearly CTC from saved monthly gross
                            // Since we store monthly_gross, and typically CTC = monthly_gross + monthly_contributions
                            // We'll approximate: monthly_gross * 12 * 1.15 (assuming ~15% employer contributions)
                            // Or simpler: just use gross_salary * 12 (monthly to yearly conversion)
                            const monthlyGross = parseFloat(data.gross_salary) || 0;
                            // For simplicity, assume contributions add ~12-15%, so use 1.13 multiplier
                            const approximateYearlyCTC = monthlyGross * 12 * 1.13;
                            $('#edit-employee-salary input#edit_gross_salary').val(
                                approximateYearlyCTC.toFixed(2));

                            // Populate salary settings dropdown
                            if (data.salary_settings_id) {
                                $('#edit_salary_setting_type').val(data.salary_settings_id)
                                    .trigger('change');
                            }
                            if (data.salary_payroll_deduction_id) {
                                $select_deduction_ids.val(data.salary_payroll_deduction_id)
                                    .trigger('change');
                            }
                            if (data.salary_payroll_overtime_id) {
                                $select_overtime_ids.val(data.salary_payroll_overtime_id)
                                    .trigger('change');
                            }
                            if (data.salary_payroll_addition_id) {
                                $select_addition_id.val(data.salary_payroll_addition_id)
                                    .trigger('change');
                            }

                            $('#edit-employee-salary').modal('show');
                        } else {
                            alert('Salary record not found.');
                        }
                    },
                    error: function() {
                        alert('Failed to fetch employee salary data.');
                    }
                });
            });






            let deleteId = null;

            // Open modal and store ID
            $(document).on('click', '.delete-salary-btn', function() {
                deleteId = $(this).data('id');
                $('#delete_modal .modal-body p').text(
                    `You want to delete salary with ID ${deleteId}. This action cannot be undone.`);
            });

            // Confirm delete
            $('#confirmDeleteBtn').click(function() {
                if (!deleteId) return;

                $.ajax({
                    url: '{{ route('salary.delete', ['box_slug' => request()->route('box_slug')]) }}', // Laravel route
                    type: 'POST',
                    data: {
                        id: deleteId,
                        _token: '{{ csrf_token() }}' // CSRF token
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#delete_modal').modal('hide');
                            salaryTable.ajax.reload(null,
                            false); // Reload table without resetting pagination
                            // Show a success toast
                            showSuccessToast(response.message);
                        } else {
                            showErrorToast(response.message);
                        }
                    },
                    error: function(xhr) {
                        showErrorToast('Error deleting salary.');
                    }
                });
            });



            // Handle calculate button in edit form
            $(document).on('click', '#editCalculateSalaryBtn', function() {
                const ctc = parseFloat($('#edit_gross_salary').val()) || 0;
                const settingId = $('#edit_salary_setting_type').val();

                if (!ctc || ctc <= 0) {
                    showErrorToast('Please enter a valid Gross Salary (CTC) amount');
                    return;
                }

                if (!settingId) {
                    showErrorToast('Please select a Salary Setting Template');
                    return;
                }

                // Determine period (monthly if < 100000, else yearly)
                const period = (ctc >= 100000) ? 'yearly' : 'monthly';

                // Call backend API for calculation
                $.ajax({
                    url: '{{ route('salary-settings.calculate', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ctc: ctc,
                        period: period,
                        salary_setting_id: settingId
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            // Display calculation results in the calculator modal
                            displayCalculationResults(response.data);

                            // Then call get-details API to fetch and display salary setting details
                            $.ajax({
                                url: '{{ route('salary-settings.get-details', ['box_slug' => request()->route('box_slug')]) }}',
                                method: 'GET',
                                data: {
                                    id: settingId
                                },
                                success: function(getDetailsResponse) {
                                    if (getDetailsResponse.success &&
                                        getDetailsResponse.data) {
                                        // Store and display salary setting details
                                        currentSalarySettingData =
                                            getDetailsResponse.data;
                                        displaySalarySettingDetails(
                                            getDetailsResponse.data);
                                        console.log('Salary Setting Details:',
                                            getDetailsResponse.data);
                                    }
                                },
                                error: function(xhr) {
                                    console.error(
                                        "Failed to fetch salary setting details:",
                                        xhr);
                                    // Don't show error to user, just log it
                                }
                            });

                            // Show the calculator modal
                            const employeeOption = $('#employeeSelect option:selected');
                            setSalaryCalculatorEmployeeName(employeeOption.text(), employeeOption.data('image'));
                            $('#salaryCalculatorModal').modal('show');
                        } else {
                            showErrorToast('Failed to calculate salary. Please try again.');
                        }
                    },
                    error: function(xhr) {
                        showErrorToast('An error occurred while calculating salary.');
                    }
                });
            });

            $('#edit-salary-form').on('submit', function(e) {
                e.preventDefault();
                let salaryId = $('#salary_id').val();
                let formData = {
                    _token: '{{ csrf_token() }}',
                    salary_id: salaryId,
                    box_user_id: $('#edit_box_user_id').val(),
                    gross_salary: $('#edit_gross_salary').val(),
                    salary_settings_id: $('#edit_salary_setting_type').val(),
                    salary_payroll_deduction_id: $('#salary_payroll_deduction_ids').val() || null,
                    salary_payroll_overtime_id: $('#salary_payroll_overtime_ids').val() || null,
                    salary_payroll_addition_id: $('#salary_payroll_addition_ids').val() || null,
                };

                $.ajax({
                    url: '{{ route('salary.update', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'PUT',
                    data: formData,
                    success: function(res) {
                        if (res.success) {
                            // Remove any modal backdrops that might cause blur
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open').css('padding-right', '');

                            // Close edit modal
                            $('#edit-employee-salary').modal('hide');

                            // Show success toast
                            const successMessage = res.message ||
                            'Salary updated successfully!';
                            showSuccessToast(successMessage);

                            // Reload table
                            if (typeof salaryTable !== 'undefined') {
                                salaryTable.ajax.reload(null, false);
                            }
                        } else {
                            showErrorToast('Failed to update salary: ' + (res.message ||
                                'Unknown error'));
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        // Remove any modal backdrops that might cause blur
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css('padding-right', '');

                        let errorMessage = 'An error occurred while updating salary.';
                        if (err.responseJSON && err.responseJSON.message) {
                            errorMessage = err.responseJSON.message;
                        } else if (err.responseJSON && err.responseJSON.errors) {
                            const errors = err.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join(', ');
                        }
                        showErrorToast(errorMessage);
                    }
                });
            });





            function getColorFromName(name) {
                const colors = ["#f44336", "#e91e63", "#9c27b0", "#673ab7", "#3f51b5", "#2196f3", "#03a9f4",
                    "#00bcd4", "#009688", "#4caf50", "#8bc34a", "#8bc34a", "#cddc39", "#ffc107", "#ff9800",
                    "#ff5722"
                ];
                const index = name ? name.charCodeAt(0) % colors.length : 0;
                return colors[index];
            }




            // function calculateNetSalary(modalSelector = '') {
            //     // ===== Basic =====
            //     let basic = parseFloat($(`${modalSelector} input[name="basic"]`).val()) || 0;

            //     // ===== Percentage-Based Earnings =====
            //     let daPercent = parseFloat($(`${modalSelector} input[name="da"]`).val()) || 0;
            //     let hraPercent = parseFloat($(`${modalSelector} input[name="hra"]`).val()) || 0;

            //     let da = (basic * daPercent) / 100;
            //     let hra = (basic * hraPercent) / 100;
            //     let conveyance = parseFloat($(`${modalSelector} input[name="conveyance"]`).val()) || 0;
            //     let allowance = parseFloat($(`${modalSelector} input[name="allowance"]`).val()) || 0;
            //     let medical_allowance = parseFloat($(`${modalSelector} input[name="medical_allowance"]`).val()) ||
            //         0;
            //     let others = parseFloat($(
            //             `${modalSelector} input[name="others"], ${modalSelector} input[name="earning_others"]`)
            //         .val()) || 0;
            //     let grossSalary = basic + da + hra + conveyance + allowance + medical_allowance + others;
            //     let tdsPercent = parseFloat($(`${modalSelector} input[name="tds"]`).val()) || 0;
            //     let esiPercent = parseFloat($(`${modalSelector} input[name="esi"]`).val()) || 0;
            //     let pfPercent = parseFloat($(`${modalSelector} input[name="pf"]`).val()) || 0;
            //     let tds = (basic * tdsPercent) / 100;
            //     let esi = (basic * esiPercent) / 100;
            //     let pf = (basic * pfPercent) / 100;
            //     let leave = parseFloat($(`${modalSelector} input[name="leave"]`).val()) || 0;
            //     let prof_tax = parseFloat($(`${modalSelector} input[name="prof_tax"]`).val()) || 0;
            //     let labour_welfare = parseFloat($(`${modalSelector} input[name="labour_welfare"]`).val()) || 0;
            //     let othersdeductions = parseFloat($(
            //         `${modalSelector} input[name="othersdeductions"], ${modalSelector} input[name="deduction_others"]`
            //     ).val()) || 0;
            //     let totalDeductions = tds + esi + pf + leave + prof_tax + labour_welfare + othersdeductions;
            //     let netSalary = grossSalary - totalDeductions;
            //     $(`${modalSelector} input[name="net_salary"]`).val(netSalary.toFixed(2));
            // }

            // $(document).on('input', '.earning-row input, .deduction-row input', function() {
            //     let modal = $(this).closest('.modal');
            //     let modalSelector = modal.length ? `#${modal.attr('id')}` : '';
            //     calculateNetSalary(modalSelector);
            // });

            function calculateNetSalary(modalSelector = '') {
                // ===== Basic =====
                let basic = parseFloat($(`${modalSelector} input[name="basic"]`).val()) || 0;

                // ===== Percentage-Based Earnings =====
                let daPercent = parseFloat($(`${modalSelector} input[name="da"]`).val()) || 0;
                let hraPercent = parseFloat($(`${modalSelector} input[name="hra"]`).val()) || 0;

                let da = (basic * daPercent) / 100;
                let hra = (basic * hraPercent) / 100;

                // Fill the sum amount fields
                $(`${modalSelector} input[name="da_sum_amount"]`).val(da.toFixed(2));
                $(`${modalSelector} input[name="hra_sum_amount"]`).val(hra.toFixed(2));

                let conveyance = parseFloat($(`${modalSelector} input[name="conveyance"]`).val()) || 0;
                let allowance = parseFloat($(`${modalSelector} input[name="allowance"]`).val()) || 0;
                let medical_allowance = parseFloat($(`${modalSelector} input[name="medical_allowance"]`).val()) ||
                    0;
                let others = parseFloat($(
                        `${modalSelector} input[name="others"], ${modalSelector} input[name="earning_others"]`)
                    .val()) || 0;

                let grossSalary = basic + da + hra + conveyance + allowance + medical_allowance + others;

                // ===== Percentage-Based Deductions =====
                let tdsPercent = parseFloat($(`${modalSelector} input[name="tds"]`).val()) || 0;
                let esiPercent = parseFloat($(`${modalSelector} input[name="esi"]`).val()) || 0;
                let pfPercent = parseFloat($(`${modalSelector} input[name="pf"]`).val()) || 0;

                let tds = (basic * tdsPercent) / 100;
                let esi = (basic * esiPercent) / 100;
                let pf = (basic * pfPercent) / 100;

                // Fill the sum amount fields
                $(`${modalSelector} input[name="tds_sum_amount"]`).val(tds.toFixed(2));
                $(`${modalSelector} input[name="esi_sum_amount"]`).val(esi.toFixed(2));
                $(`${modalSelector} input[name="pf_sum_amount"]`).val(pf.toFixed(2));

                // ===== Other Deductions =====
                let leave = parseFloat($(`${modalSelector} input[name="leave"]`).val()) || 0;
                let prof_tax = parseFloat($(`${modalSelector} input[name="prof_tax"]`).val()) || 0;
                let labour_welfare = parseFloat($(`${modalSelector} input[name="labour_welfare"]`).val()) || 0;
                let othersdeductions = parseFloat($(
                    `${modalSelector} input[name="othersdeductions"], ${modalSelector} input[name="deduction_others"]`
                ).val()) || 0;
                let totalDeductions = tds + esi + pf + leave + prof_tax + labour_welfare + othersdeductions;
                let netSalary = grossSalary - totalDeductions;
                $(`${modalSelector} input[name="net_salary"]`).val(netSalary.toFixed(2));
            }


            $(document).on('input', '.earning-row input, .deduction-row input', function() {
                let modal = $(this).closest('.modal');
                let modalSelector = modal.length ? `#${modal.attr('id')}` : '';
                calculateNetSalary(modalSelector);
            });

            $('#edit-employee-salary').on('shown.bs.modal', function() {
                calculateNetSalary('#edit-employee-salary');
            });


            // Store form data for later submission
            let pendingFormData = null;
            let pendingForm = null;

            $('#employeeSalaryForm').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);

                // Check if this is an update (has edit_salary_id)
                const salaryId = $('#edit_salary_id').val();
                const isUpdate = salaryId && salaryId !== '';

                // Get box_user_id from employeeSelect (even if disabled)
                const boxUserId = $('#employeeSelect').val();
                if (!boxUserId) {
                    showErrorToast('Please select an employee.');
                    return;
                }

                // Serialize form data
                var formData = form.serialize();

                // If employeeSelect is disabled, it won't be included in serialize(), so add it manually
                if ($('#employeeSelect').prop('disabled')) {
                    // Parse the serialized data and add box_user_id
                    const formDataObj = new URLSearchParams(formData);
                    formDataObj.set('box_user_id', boxUserId);
                    formData = formDataObj.toString();
                }

                // For update, also ensure salary_id is included
                if (isUpdate) {
                    const formDataObj = new URLSearchParams(formData);
                    formDataObj.set('salary_id', salaryId);
                    formData = formDataObj.toString();
                }

                // Store form data and form reference
                pendingFormData = formData;
                pendingForm = form;

                // Show confirmation toast
                showConfirmToast();
            });

            // Function to show confirmation toast
            function showConfirmToast() {
                const $toast = $('#toastMessage');
                const $toastBody = $('#toastBody');

                // Change toast to warning style for confirmation
                $toast.removeClass('bg-danger bg-success bg-warning').addClass('bg-warning');

                // Create confirmation message with buttons
                $toastBody.html(`
            <div class="d-flex align-items-center">
                <i class="ti ti-alert-circle me-2 fs-5"></i>
                <div class="flex-grow-1">
                    <strong>Confirm Assignment</strong><br>
                    <small>Are you sure you want to assign salary to this employee? This action will create a salary record.</small>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="button" class="btn btn-sm btn-light" id="cancelSalarySubmit">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary" id="confirmSalarySubmitToast">Confirm</button>
            </div>
        `);

                // Show toast
                const toast = new bootstrap.Toast($toast[0], {
                    autohide: false // Don't auto-hide for confirmation
                });
                toast.show();

                // Handle cancel button
                $(document).off('click', '#cancelSalarySubmit').on('click', '#cancelSalarySubmit', function() {
                    toast.hide();
                    pendingFormData = null;
                    pendingForm = null;
                });
            }

            // Function to show success toast
            function showSuccessToast(message, title = 'Success!') {
                const $toast = $('#toastMessage');
                const $toastBody = $('#toastBody');

                // Change toast to success style
                $toast.removeClass('bg-danger bg-warning').addClass('bg-success');

                // Set success message
                $toastBody.html(`
                    <div class="d-flex align-items-center">
                        <i class="ti ti-check-circle me-3 fs-4"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 text-white fw-bold">${title}</h6>
                            <small class="text-white-50">${message}</small>
                        </div>
                    </div>
                `);

                // Show toast with auto-hide
                const toast = new bootstrap.Toast($toast[0], {
                    autohide: true,
                    delay: 5000 // 5 seconds
                });
                toast.show();
            }

            // Function to show error toast
            function showErrorToast(message, title = 'Error!') {
                const $toast = $('#toastMessage');
                const $toastBody = $('#toastBody');

                // Change toast to danger style
                $toast.removeClass('bg-success bg-warning').addClass('bg-danger');

                // Set error message
                $toastBody.html(`
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-circle me-3 fs-4"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 text-white fw-bold">${title}</h6>
                            <small class="text-white-50">${message}</small>
                        </div>
                    </div>
                `);

                // Show toast with auto-hide
                const toast = new bootstrap.Toast($toast[0], {
                    autohide: true,
                    delay: 5000 // 5 seconds
                });
                toast.show();
            }

            // Handle confirmation button click (from toast)
            $(document).on('click', '#confirmSalarySubmitToast', function() {
                // Hide toast
                const toastElement = document.getElementById('toastMessage');
                const toastInstance = bootstrap.Toast.getInstance(toastElement);
                if (toastInstance) {
                    toastInstance.hide();
                }

                if (!pendingFormData || !pendingForm) {
                    return;
                }

                // Check if this is an update (has edit_salary_id)
                const salaryId = $('#edit_salary_id').val();
                const isUpdate = salaryId && salaryId !== '';

                // Determine URL and method
                let url, method, submitData;
                if (isUpdate) {
                    url = "{{ route('salary.update', ['box_slug' => request()->route('box_slug')]) }}";
                    method = "PUT";
                    // For update, ensure box_user_id is included (even if employeeSelect is disabled)
                    const boxUserId = $('#employeeSelect').val();
                    if (!boxUserId) {
                        showErrorToast('Employee selection is required.');
                        return;
                    }
                    // Parse form data and ensure box_user_id and salary_id are included
                    const formDataObj = new URLSearchParams(pendingFormData);
                    formDataObj.set('box_user_id', boxUserId);
                    formDataObj.set('salary_id', salaryId);
                    submitData = formDataObj.toString();
                } else {
                    url = "{{ route('salaries.store', ['box_slug' => request()->route('box_slug')]) }}";
                    method = "POST";
                    submitData = pendingFormData;
                }

                $.ajax({
                    url: url,
                    type: method,
                    data: submitData,
                    beforeSend: function() {
                        pendingForm.find('button[type="submit"]').attr('disabled', true).text(
                            'Processing...');
                    },
                    success: function(response) {
                        if (response.status === 'success' || response.success) {
                            // Close the offcanvas first
                            const offcanvasElement = document.getElementById(
                                'new-employee-salary');
                            if (offcanvasElement) {
                                const offcanvasInstance = bootstrap.Offcanvas.getInstance(
                                    offcanvasElement);
                                if (offcanvasInstance) {
                                    offcanvasInstance.hide();
                                } else {
                                    // If instance doesn't exist, try to hide using jQuery
                                    $('#new-employee-salary').offcanvas('hide');
                                }
                            }

                            // Remove any modal backdrops that might cause blur
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open').css('padding-right', '');

                            // Reset form
                            pendingForm[0].reset();

                            // Reset form title and button text
                            $('#employeeSalaryFormTitle').text('Add Employee Salary');
                            $('#employeeSalaryFormSubmitBtn').text('Add Employee Salary');
                            $('#edit_salary_id').val('');
                            $('#employeeSelect').prop('disabled', false);

                            // Show success toast after a short delay to ensure offcanvas is closed
                            setTimeout(function() {
                                const successMessage = response.message ||
                                    'Employee salary has been assigned successfully.';
                                showSuccessToast(successMessage);
                            }, 300);

                            // Reload table
                            if (typeof salaryTable !== 'undefined') {
                                salaryTable.ajax.reload(null, false);
                            }
                        } else {
                            showErrorToast(response.message || 'Something went wrong!');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessages = '';
                            $.each(errors, function(key, value) {
                                errorMessages += value + '\n';
                            });
                            showErrorToast(errorMessages);
                        } else {
                            showErrorToast('An error occurred: ' + (xhr.responseJSON?.message ||
                                'Unknown error'));
                        }
                    },
                    complete: function() {
                        pendingForm.find('button[type="submit"]').attr('disabled', false).text(
                            'Add Employee Salary');
                        // Clear pending data
                        pendingFormData = null;
                        pendingForm = null;

                        // Remove any modal backdrops that might cause blur
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css('padding-right', '');
                    }
                });
            });
            // Generate Slip Logic
            let selectedEmployeeIds = new Set();
            const $generateSlipTrigger = $('#generateSlipTrigger');
            const $confirmGenerateSlip = $('#confirmGenerateSlip');
            const $selectAll = $('#select-all');

            function updateGenerateButtonState() {
                const hasSelection = selectedEmployeeIds.size > 0;
                const isConfirmed = $confirmGenerateSlip.is(':checked');

                if (hasSelection && isConfirmed) {
                    $generateSlipTrigger.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
                } else {
                    $generateSlipTrigger.prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
                }

                // Update selection count text
                $('#selectedCount').text(`${selectedEmployeeIds.size} employees selected`);
            }

            // Initialize button state
            updateGenerateButtonState();

            // Checkbox change handler (delegated for DataTable)
            $(document).on('change', '.salary-checkbox', function() {
                const id = $(this).val();
                if ($(this).is(':checked')) {
                    selectedEmployeeIds.add(id);
                } else {
                    selectedEmployeeIds.delete(id);
                    // Uncheck select all if any individual is unchecked
                    $selectAll.prop('checked', false);
                }
                updateGenerateButtonState();
            });

            // Select All handler
            $selectAll.on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.salary-checkbox').each(function() {
                    const id = $(this).val();
                    $(this).prop('checked', isChecked);
                    if (isChecked) {
                        selectedEmployeeIds.add(id);
                    } else {
                        selectedEmployeeIds.delete(id);
                    }
                });
                updateGenerateButtonState();
            });

            // Handle page changes/redraws in DataTable
            $('#salaryTable').on('draw.dt', function() {
                // Restore checkbox states
                $('.salary-checkbox').each(function() {
                    const id = $(this).val();
                    if (selectedEmployeeIds.has(id)) {
                        $(this).prop('checked', true);
                    }
                });

                // Update select all checkbox state
                const allVisibleChecked = $('.salary-checkbox').length > 0 &&
                    $('.salary-checkbox').length === $('.salary-checkbox:checked').length;
                $selectAll.prop('checked', allVisibleChecked);
            });

            // Trigger button click (Bulk)
            $generateSlipTrigger.on('click', function() {
                if ($confirmGenerateSlip.is(':checked') && selectedEmployeeIds.size > 0) {
                    $('#generateSlipModal').modal('show');
                } else if (!$confirmGenerateSlip.is(':checked')) {
                    if (typeof showErrorToast === 'function') {
                        showErrorToast('Please confirm salary slip generation.');
                    }
                }
            });

            // Trigger button click (Individual)
            $(document).on('click', '.generate-slip-individual-btn', function() {
                const boxUserId = String($(this).data('user-id'));
                
                // Clear existing selections
                selectedEmployeeIds.clear();
                $('.salary-checkbox').prop('checked', false);
                $selectAll.prop('checked', false);
                
                // Add to Set
                selectedEmployeeIds.add(boxUserId);
                
                // Update checkbox in UI if it's currently visible
                $(`.salary-checkbox[value="${boxUserId}"]`).prop('checked', true);
                
                // Automatically check confirmation or leave it to the user in the modal
                // Since bulk trigger required confirmation checkbox checked before opening,
                // here we just open the modal. User can click Generate.
                
                updateGenerateButtonState();
                $('#generateSlipModal').modal('show');
            });

            // Confirmation Checkbox Change
            $confirmGenerateSlip.on('change', updateGenerateButtonState);

            // Modal Generate Button
            $('#confirmGenerateSlipBtn').on('click', function() {
                const $btn = $(this);
                const month = $('#slip_month').val();
                const year = $('#slip_year').val();

                $btn.prop('disabled', true).text('Generating...');

                $.ajax({
                    url: "{{ route('payroll.generate.slip', ['box_slug' => request()->route('box_slug')]) }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        box_user_ids: Array.from(selectedEmployeeIds),
                        month: month,
                        year: year
                    },
                    success: function(response) {
                        $('#generateSlipModal').modal('hide');
                        if (typeof showSuccessToast === 'function') {
                            showSuccessToast(response.message ||
                                'Salary slips generated successfully.');
                        }

                        // Clear selections
                        selectedEmployeeIds.clear();
                        $('.salary-checkbox').prop('checked', false);
                        $selectAll.prop('checked', false);
                        $confirmGenerateSlip.prop('checked', false);
                        updateGenerateButtonState();
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message ||
                            'Failed to generate salary slips.';
                        if (typeof showErrorToast === 'function') {
                            showErrorToast(message);
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Generate');
                    }
                });
            });

            // Global cleanup for modals and offcanvas to fix "stuck backdrop" or "blur" issues
            $(document).on('hidden.bs.modal hidden.bs.offcanvas', function () {
                $('.modal-backdrop').remove();
                $('.offcanvas-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
            });

        });
    </script>
@endpush
@endsection
