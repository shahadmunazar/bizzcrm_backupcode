@extends('layouts.main')
@section('content')
@section('title', 'Salary Report')


<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Dynamic Toast -->
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
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

                @php
                    $user = Auth::user();
                    $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
                    $adminBoxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
                    $isBoxAdmin = $adminBoxUser && strtolower($adminBoxUser->staff_role ?? '') === 'admin';
                    $canReadReport = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'report');
                @endphp
                @if($isGlobalAdmin || $isBoxAdmin || $canReadReport)
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
                @endif

                <!-- Salary generation removed - Salary should be generated from Employee Salary page -->

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
                <div>
                    <h5>Salary Reports </h5>
                    <p class="text-muted mb-0 small">Note: Salary should be generated from <a href="{{ route('payroll.employee.salary', ['box_slug' => request()->route('box_slug')]) }}" class="text-primary">Employee Salary</a> page. All generated salaries will be displayed here.</p>
                </div>
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
                            data-bs-toggle="dropdown">
                            Designation
                        </a>
                        <ul class="dropdown-menu  dropdown-menu-end p-3">
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1">Finance </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1">Developer </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1">Executive </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1">Manager </a>
                            </li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <a href="javascript:void(0);"
                            class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                            data-bs-toggle="dropdown">
                            Sort By : Last 7 Days
                        </a>
                        <ul class="dropdown-menu  dropdown-menu-end p-3">
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1">Recently Added </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1">Ascending </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1">Desending </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1">Last Month </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1">Last 7 Days </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="custom-datatable-filter table-responsive">
                    <table class="table" id="salaryReportTable">
                        <thead class="thead-light">
                            <tr>
                                <th></th>
                                <th>Emp ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Department</th>
                                <th>Joining Date</th>
                                <th>Month/Year</th>
                                <th>Holidays</th>
                                <th>Final Salary</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>

    </div>

    

</div>
<!-- /Page Wrapper -->


    <!-- Manage Columns Modal -->
    <!-- Manage Columns Modal -->
    <div class="modal fade" id="manageColumnsModal" tabindex="-1" aria-labelledby="manageColumnsModalLabel"
        aria-hidden="true">
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

    <!-- Export Salary Modal -->
    <div class="modal fade" id="exportSalaryModal" tabindex="-1" aria-labelledby="exportSalaryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportSalaryModalLabel">Export Salary Reports</h5>
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

    <!-- Salary Report Modal (Improved for Readability) -->
<div class="modal fade" id="salaryReportModal" tabindex="-1" aria-labelledby="salaryReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-14" id="salaryReportModalLabel">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Salary Report Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="max-height: 70vh; overflow-y: auto;"> <!-- Scrollable body if needed, but compact content -->
                <div id="salaryDetailsContent" class="p-4" style="font-size: 1rem; line-height: 1.6;"> <!-- Larger font (16px base), better line-height for readability -->
                    <!-- Loading state -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary mb-2" role="status" style="width: 3rem; height: 3rem;"></div>
                        <p class="mb-0">Loading details...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
                <button type="button" class="btn btn-primary" onclick="printSalaryReport()">
                    <i class="fas fa-print me-1"></i>Print Report
                </button>
            </div>
        </div>
    </div>
</div>



<!-- Salary generation modal removed - Salary should be generated from Employee Salary page -->



<!-- Add Termination -->
<div class="modal fade" id="new-employee-salary">
    <div class="modal-dialog modal-dialog-centered modal-lg">
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
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Employee Name  </label>
                                <select class="select" id="employeeSelect" name="box_user_id">
                                    <option value="">Select Employee</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <label class="form-label">Net Salary  </label>
                            <input type="text" class="form-control" name="net_salary" />
                        </div>
                    </div>
                    <div class="row earning-row">
                        <div class="d-flex justify-content-between mb-3">
                            <label class="form-label mb-0">Earnings</label>
                            <a href="#" id="toggleSalarySettingBtn" class="text-primary">
                                <i class="ti ti-plus me-1"></i>Add New
                            </a>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Basic </label>
                                <input type="text" name="basic" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">DA </label>
                                <input type="text" name="da" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">DA Sum Amount </label>
                                <input type="text" name="da_sum_amount" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">HRA </label>
                                <input type="text" name="hra" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">HRA Sum Amount </label>
                                <input type="text" name="hra_sum_amount" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Conveyance </label>
                                <input type="text" name="conveyance" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Allowance  </label>
                                <input type="text" name="allowance" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Medical Allowance </label>
                                <input type="text" name="medical_allowance" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Others </label>
                                <input type="text" name="others" class="form-control" />
                            </div>
                        </div>

                        <div id="extraSalaryFields" style="display: none;">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Salary Setting </label>
                                    <select class="select" id="salary_setting_type" name="salary_settings_id">
                                        <option value="">Select Salary Setting</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Payroll Addition </label>
                                    <select class="select" id="salary_payroll_addition_id"
                                        name="salary_payroll_addition_id">
                                        <option value="">Select Salary Setting</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Payroll Overtime </label>
                                    <select class="select" id="salary_payroll_overtime_id"
                                        name="salary_payroll_overtime_id">
                                        <option value="">Select Salary Setting</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row deduction-row">
                        <div class="d-flex justify-content-between mb-3">
                            <label class="form-label mb-0">Earnings</label>
                            <a href="#" id="deducttoggleSalarySettingBtn" class="text-primary">
                                <i class="ti ti-plus me-1"></i>Add New
                            </a>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">TDS (10%) </label>
                                <input type="text" name="tds" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">TDS Sum Amount </label>
                                <input type="text" name="tds_sum_amount" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">ESI (1.75%) </label>
                                <input type="text" name="esi" class="form-control" />

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">ESI Sum Amount </label>
                                <input type="text" name="esi_sum_amount" class="form-control" />

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">PF (12%) </label>
                                <input type="text" name="pf" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">PF Sum Amount </label>
                                <input type="text" name="pf_sum_amount" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Leave </label>
                                <input type="text" name="leave" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Prof.Tax </label>
                                <input type="text" name="prof_tax" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Labour Welfare </label>
                                <input type="text" name="labour_welfare" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Others </label>
                                <input type="text" name="othersdeductions" class="form-control" />

                            </div>
                        </div>

                        <div id="deductionextraSalaryFields" style="display: none">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Payroll Deduction </label>
                                    <select class="select" id="salary_payroll_deduction_id"
                                        name="salary_payroll_deduction_id">
                                        <option value="">Select Salary Setting</option>
                                    </select>
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
                            <label class="form-label">Net Salary</label>
                            <input type="text" class="form-control" id="net_salary" name="net_salary" />
                        </div>
                    </div>

                    <input type="hidden" id="salary_id" name="salary_id">

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

                    <div class="row deduction-row">
                        <div class="d-flex justify-content-between mb-3">
                            <label class="form-label mb-0">Earnings</label>
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
                    <button type="button" class="btn btn-white border me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Employee Salary</button>
                </div>
            </form>
        </div>
    </div>
</div>
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


{{-- <div id="salarySlipContainer" style="display: none;">
    <div class="card">
        <div class="card-body">
            <div class="Salary-slip-box">
                <div class="table-responsive">
                    <table class="table table-bordered" style="border-collapse:collapse;" cellspacing="0">
                        <tr style="height:15pt">
                            <td style="width:468pt; " colspan="8">
                                <p class="s1" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    Company Name : MEDSYN LAB BIOTECH
                                </p>
                            </td>
                            <td style="width:95pt; " colspan="2">
                                <p class="s2" style="padding-top: 1pt;padding-left: 2pt;text-indent: 0pt;text-align: left;">
                                    Date : <span id="slipDate">09-18-2025</span>
                                </p>
                            </td>
                        </tr>
                        <tr style="height:14pt">
                            <td style="width:80pt; ">
                                <p class="s1" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    Emp.Code
                                </p>
                            </td>
                            <td style="width:118pt; " colspan="2">
                                <p class="s2" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    <span id="slipEmpCode">13</span>
                                </p>
                            </td>
                            <td style="width:60pt; ">
                                <p class="s1" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    Emp.Name
                                </p>
                            </td>
                            <td style="width:162pt; " colspan="3">
                                <p class="s2" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    <span id="slipEmpName">HEMLATA</span>
                                </p>
                            </td>
                            <td style="width:143pt; " colspan="3">
                                <p class="s2" style="padding-top: 1pt;padding-left: 2pt;text-indent: 0pt;text-align: left;">
                                    Dept. : <span id="slipDept">HOUSE KEEPING</span>
                                </p>
                            </td>
                        </tr>
                        <tr style="height:14pt">
                            <td style="width:80pt; ">
                                <p class="s1" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    Designation
                                </p>
                            </td>
                            <td style="width:118pt; " colspan="2">
                                <p class="s2" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    <span id="slipDesignation">EXECUTIVE</span>
                                </p>
                            </td>
                            <td style="width:365pt; " colspan="7">
                                <p class="s2" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    Father Name :<span id="slipFatherName">MR.OM PARKASH</span>
                                </p>
                            </td>
                        </tr>
                        <tr style="height:13pt">
                            <td style="width:137pt; " colspan="2">
                                <p class="s1" style="padding-top: 1pt;padding-left: 27pt;text-indent: 0pt;text-align: left;">
                                    Attendance Detail
                                </p>
                            </td>
                            <td style="width:121pt; " colspan="2">
                                <p class="s1" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    Rate Of Wages
                                </p>
                            </td>
                            <td style="width:108pt; " colspan="2">
                                <p class="s1" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    Amt.Payable
                                </p>
                            </td>
                            <td style="width:102pt; " colspan="2">
                                <p class="s1" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    other Deduction
                                </p>
                            </td>
                            <td style="width:95pt; " colspan="2">
                                <p class="s1" style="padding-top: 1pt;padding-left: 2pt;text-indent: 0pt;text-align: left;">
                                    Pf &amp; Esi Deduction
                                </p>
                            </td>
                        </tr>
                        <tr style="height:14pt">
                            <td style="width:80pt; ">
                                <p class="s1" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    Present
                                </p>
                            </td>
                            <td style="width:57pt; ">
                                <p class="s2" style="padding-top: 1pt;padding-right: 1pt;text-indent: 0pt;text-align: right;">
                                    <span id="slipPresent">8</span>
                                </p>
                            </td>
                            <td style="width:61pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:60pt; ">
                                <p class="s2" style="padding-top: 1pt;padding-right: 1pt;text-indent: 0pt;text-align: right;">
                                    <span id="slipRate">9000</span>
                                </p>
                            </td>
                            <td style="width:60pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:48pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:54pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:48pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:49pt; ">
                                <p class="s1" style="padding-top: 1pt;padding-left: 2pt;text-indent: 0pt;text-align: left;">
                                    Pf
                                </p>
                            </td>
                            <td style="width:46pt; ">
                                <p class="s2" style="padding-top: 1pt;text-indent: 0pt;text-align: right;">
                                    <span id="slipPF">0</span>
                                </p>
                            </td>
                        </tr>
                        <tr style="height:13pt">
                            <td style="width:80pt; ">
                                <p class="s1" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    Absent
                                </p>
                            </td>
                            <td style="width:57pt; ">
                                <p class="s2" style="padding-top: 1pt;padding-right: 1pt;text-indent: 0pt;text-align: right;">
                                    <span id="slipAbsent">17</span>
                                </p>
                            </td>
                            <td style="width:61pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:60pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:60pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:48pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:54pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:48pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:49pt; ">
                                <p class="s1" style="padding-top: 1pt;padding-left: 2pt;text-indent: 0pt;text-align: left;">
                                    Esi
                                </p>
                            </td>
                            <td style="width:46pt; ">
                                <p class="s2" style="padding-top: 1pt;text-indent: 0pt;text-align: right;">
                                    <span id="slipESI">0</span>
                                </p>
                            </td>
                        </tr>
                        <tr style="height:13pt">
                            <td style="width:80pt; ">
                                <p class="s1" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    HoliDay
                                </p>
                            </td>
                            <td style="width:57pt; ">
                                <p class="s2" style="padding-top: 1pt;padding-right: 1pt;text-indent: 0pt;text-align: right;">
                                    <span id="slipHoliday">1</span>
                                </p>
                            </td>
                            <td style="width:61pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:60pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:60pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:48pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:54pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:48pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:49pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:46pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                        </tr>
                        <tr style="height:13pt">
                            <td style="width:80pt; ">
                                <p class="s1" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    Leave
                                </p>
                            </td>
                            <td style="width:57pt; ">
                                <p class="s2" style="padding-top: 1pt;padding-right: 1pt;text-indent: 0pt;text-align: right;">
                                    <span id="slipLeave">0</span>
                                </p>
                            </td>
                            <td style="width:61pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:60pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:60pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:48pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:54pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:48pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:49pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:46pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                        </tr>
                        <tr style="height:13pt">
                            <td style="width:80pt; ">
                                <p class="s1" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    Week Off
                                </p>
                            </td>
                            <td style="width:57pt; ">
                                <p class="s2" style="padding-top: 1pt;padding-right: 1pt;text-indent: 0pt;text-align: right;">
                                    <span id="slipWeekOff">5</span>
                                </p>
                            </td>
                            <td style="width:61pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:60pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:60pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:48pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:54pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:48pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:49pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:46pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                        </tr>
                        <tr style="height:13pt">
                            <td style="width:80pt; ">
                                <p class="s1" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    Paid Days
                                </p>
                            </td>
                            <td style="width:57pt; ">
                                <p class="s2" style="padding-top: 1pt;padding-right: 1pt;text-indent: 0pt;text-align: right;">
                                    <span id="slipPaidDays">14</span>
                                </p>
                            </td>
                            <td style="width:61pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:60pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:60pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:48pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:54pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:48pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:49pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:46pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                        </tr>
                        <tr style="height:11pt">
                            <td style="width:80pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:57pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                        </tr>
                        <tr style="height:12pt">
                            <td style="width:80pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:57pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:61pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:60pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:60pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:48pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:54pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:48pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:49pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:46pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                        </tr>
                        <tr style="height:22pt">
                            <td style="width:80pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:57pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:61pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:60pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:60pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:48pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:54pt; ">
                                <p class="s1" style="padding-top: 1pt;padding-left: 1pt;padding-right: 1pt;text-indent: 0pt;text-align: left;">
                                    Loan/Advance
                                </p>
                            </td>
                            <td style="width:48pt; ">
                                <p class="s2" style="padding-top: 1pt;padding-right: 1pt;text-indent: 0pt;text-align: right;">
                                    <span id="slipLoan">0</span>
                                </p>
                            </td>
                            <td style="width:49pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:46pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                        </tr>
                        <tr style="height:13pt">
                            <td style="width:80pt; ">
                                <p class="s1" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    WORK,OT Hours
                                </p>
                            </td>
                            <td style="width:57pt; ">
                                <p class="s2" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    <span id="slipWorkHours">61:13 , 0:0</span>
                                </p>
                            </td>
                            <td style="width:61pt; ">
                                <p class="s1" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    OT Rate
                                </p>
                            </td>
                            <td style="width:60pt; ">
                                <p class="s2" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    <span id="slipOTRate">0</span>
                                </p>
                            </td>
                            <td style="width:60pt; ">
                                <p class="s1" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    OT Amount
                                </p>
                            </td>
                            <td style="width:48pt; ">
                                <p class="s2" style="padding-top: 1pt;padding-right: 1pt;text-indent: 0pt;text-align: right;">
                                    <span id="slipOTAmount">0</span>
                                </p>
                            </td>
                            <td style="width:54pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:48pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:49pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                            <td style="width:46pt; ">
                                <p style="text-indent: 0pt;text-align: left;"><br /></p>
                            </td>
                        </tr>
                        <tr style="height:12pt">
                            <td style="width:318pt; " colspan="5">
                                <p class="s1" style="padding-top: 1pt;text-indent: 0pt;line-height: 10pt;text-align: right;">
                                    Total Earning
                                </p>
                            </td>
                            <td style="width:48pt; ">
                                <p class="s2" style="padding-top: 1pt;padding-right: 1pt;text-indent: 0pt;line-height: 10pt;text-align: right;">
                                    <span id="slipTotalEarning">4065</span>
                                </p>
                            </td>
                            <td style="width:102pt; " colspan="2">
                                <p class="s1" style="padding-top: 1pt;padding-left: 29pt;text-indent: 0pt;line-height: 10pt;text-align: left;">
                                    Total Deduction
                                </p>
                            </td>
                            <td style="width:95pt; " colspan="2">
                                <p class="s2" style="padding-top: 1pt;text-indent: 0pt;line-height: 10pt;text-align: right;">
                                    <span id="slipTotalDeduction">0</span>
                                </p>
                            </td>
                        </tr>
                        <tr style="height:15pt">
                            <td style="width:366pt; " colspan="6">
                                <p class="s2" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                                    Total Amount in Word : <span id="slipAmountWords">(Four Thousand and Sixty Five)</span>
                                </p>
                            </td>
                            <td style="width:102pt; " colspan="2">
                                <p class="s1" style="padding-top: 1pt;padding-left: 51pt;text-indent: 0pt;text-align: left;">
                                    Net Salary
                                </p>
                            </td>
                            <td style="width:95pt; " colspan="2">
                                <p class="s1" style="padding-top: 1pt;text-indent: 0pt;text-align: right;">
                                    <span id="slipNetSalary">4065</span>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> --}}

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        const currencySymbol = "{{ $box->currency_symbol ?? '₹' }}";
        const currencyCode = "{{ $box->currency_code ?? 'INR' }}";
        $(document).ready(function() {
            let salaryReportTable;

            // Column definitions for Manage Columns
            const columnDefinitions = [
                { index: 0, name: 'Checkbox', visible: true },
                { index: 1, name: 'Emp ID', visible: true },
                { index: 2, name: 'Name', visible: true },
                { index: 3, name: 'Email', visible: true },
                { index: 4, name: 'Phone', visible: true },
                { index: 5, name: 'Department', visible: true },
                { index: 6, name: 'Joining Date', visible: true },
                { index: 7, name: 'Month/Year', visible: true },
                { index: 8, name: 'Holidays', visible: true },
                { index: 9, name: 'Final Salary', visible: true },
                { index: 10, name: 'Status', visible: true },
                { index: 11, name: 'Action', visible: true }
            ];

            const pageIdentifier = 'salary_report_page';

            // Load column visibility preferences
            function loadColumnPreferences(callback) {
                $.ajax({
                    url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug")]) }}',
                    type: 'GET',
                    data: { page_identifier: pageIdentifier },
                    async: false,
                    success: function(response) {
                        if (response.success && response.data && Array.isArray(response.data) && response.data.length > 0) {
                            response.data.forEach(pref => {
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
                        }
                        if (callback) callback();
                    },
                    error: function() {
                        if (callback) callback();
                    }
                });
            }

            // Save column visibility preferences
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
                            console.log('Column preferences saved');
                        }
                    }
                });
            }

            // Apply column visibility
            function applyColumnVisibility() {
                if ($.fn.DataTable.isDataTable('#salaryReportTable')) {
                    const table = $('#salaryReportTable').DataTable();
                    columnDefinitions.forEach(col => {
                        table.column(col.index).visible(col.visible);
                    });
                }
            }

            // Populate columns modal
            function populateColumnsModal() {
                const columnsList = $('#columnsList');
                columnsList.empty();
                columnDefinitions.forEach(col => {
                    if (col.name) {
                        columnsList.append(`
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
                    }
                });
            }

            // Load preferences and then initialize table
            loadColumnPreferences(function() {
                initializeSalaryReportTable();
            });

            function initializeSalaryReportTable() {
                salaryReportTable = $('#salaryReportTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('get.salary.report', ['box_slug' => request()->route('box_slug')]) }}",
                    columns: [{
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            visible: Boolean(columnDefinitions[0].visible),
                            render: function() {
                                return `
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox" />
                                    </div>`;
                            }
                        },
                        {
                            data: 'emp_id',
                            defaultContent: 'N/A',
                            visible: Boolean(columnDefinitions[1].visible)
                        },
                        {
                            data: 'name',
                            visible: Boolean(columnDefinitions[2].visible),
                            render: function(data, type, row) {
                                const name = data || 'Unknown';
                                const designation = row.box_user?.designation_relation?.name || 'N/A';
                                const image = row.box_user?.profile_image_path ?
                                    `/storage/${row.box_user.profile_image_path}` :
                                    null;

                                const initial = name.charAt(0).toUpperCase();
                                const color = getColorFromName(name);

                                if (image) {
                                    return `
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="avatar avatar-md">
                                                <img src="${image}" class="img-fluid rounded-circle" alt="${name}">
                                            </a>
                                            <div class="ms-2">
                                                <h6 class="fw-medium mb-0">${name}</h6>
                                                <span class="text-muted small">${designation}</span>
                                            </div>
                                        </div>`;
                                }

                                return `
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md letter-avatar"
                                             style="background-color: ${color}; color: white;
                                             display:flex;align-items:center;justify-content:center;
                                             border-radius:50%;width:40px;height:40px;">
                                            ${initial}
                                        </div>
                                        <div class="ms-2">
                                            <h6 class="fw-medium mb-0">${name}</h6>
                                            <span class="text-muted small">${designation}</span>
                                        </div>
                                    </div>`;
                            }
                        },
                        {
                            data: 'email',
                            defaultContent: 'N/A',
                            visible: Boolean(columnDefinitions[3].visible)
                        },
                        {
                            data: 'phone',
                            defaultContent: 'N/A',
                            visible: Boolean(columnDefinitions[4].visible)
                        },
                        {
                            data: 'department',
                            defaultContent: 'N/A',
                            title: 'Department',
                            visible: Boolean(columnDefinitions[5].visible)
                        },
                        {
                            data: 'joining_date',
                            visible: Boolean(columnDefinitions[6].visible),
                            render: function(data) {
                                return data ? new Date(data).toLocaleDateString() : 'N/A';
                            }
                        },
                        {
                            data: 'month_year',
                            visible: Boolean(columnDefinitions[7].visible),
                            render: function(data) {
                                return data || 'N/A';
                            }
                        },
                        {
                            data: 'total_holidays',
                            visible: Boolean(columnDefinitions[8].visible),
                            render: function(data) {
                                return data !== undefined && data !== null ? parseFloat(data) : 0;
                            }
                        },
                        {
                            data: 'final_salary',
                            visible: Boolean(columnDefinitions[9].visible),
                            render: function(data) {
                                if (!data || data === '0' || data === '0.00') {
                                    return currencySymbol + '0.00';
                                }
                                const amount = parseFloat(data);
                                return currencySymbol + amount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        },
                        {
                            data: 'transaction_status',
                            visible: Boolean(columnDefinitions[10].visible),
                            render: function(data) {
                                const status = data?.toLowerCase();
                                if (status === 'completed' || status === 'paid') {
                                    return '<span class="badge bg-success">Paid</span>';
                                } else if (status === 'pending') {
                                    return '<span class="badge bg-warning text-dark">Pending</span>';
                                }
                                return '<span class="badge bg-secondary">N/A</span>';
                            }
                        },
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false,
                            visible: Boolean(columnDefinitions[11].visible),
                            render: function(data, type, row) {
                                const payslipUrl = data?.payslip_url || `{{ url('/') }}/{{ request()->route('box_slug') }}/salary-slip?report_id=${row.id}`;
                                return `
                                    <div class="action-icon d-inline-flex">
                                        <a href="javascript:void(0)"
                                           class="me-2 view-report-btn"
                                           data-id="${row.id}"
                                           title="View Report">
                                           <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="${payslipUrl}"
                                           class="me-2 view-slip-btn"
                                           title="View Payslip"
                                           data-id="${row.id}">
                                           <i class="ti ti-file-text"></i>
                                        </a>
                                        <a href="javascript:void(0)"
                                           class="me-2 delete-report-btn"
                                           data-id="${row.id}"
                                           title="Delete Report">
                                           <i class="ti ti-trash text-danger"></i>
                                        </a>
                                    </div>`;
                            }
                        }
                    ]
                });
            }

            // Manage Columns Event Handlers
            $('#manageColumnsModal').on('show.bs.modal', function() {
                populateColumnsModal();
            });

            $('#showAllColumns').on('click', function() {
                $('.column-toggle').prop('checked', true);
            });

            $('#hideAllColumns').on('click', function() {
                $('.column-toggle').prop('checked', false);
            });

            $('#applyColumnChanges').on('click', function() {
                $('.column-toggle').each(function() {
                    const index = parseInt($(this).data('column-index'));
                    const isVisible = $(this).is(':checked');
                    const col = columnDefinitions.find(c => c.index === index);
                    if (col) col.visible = isVisible;
                });
                saveColumnPreferences();
                applyColumnVisibility();
                $('#manageColumnsModal').modal('hide');
            });

            // Export Functionality
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
                    id: 'month_year',
                    name: 'Month/Year'
                },
                {
                    id: 'total_holidays',
                    name: 'Holidays'
                },
                {
                    id: 'gross_salary',
                    name: 'Gross Salary'
                },
                {
                    id: 'net_salary',
                    name: 'Final Salary'
                },
                {
                    id: 'total_deductions',
                    name: 'Total Deductions'
                },
                {
                    id: 'paid_days',
                    name: 'Paid Days'
                },
                {
                    id: 'status',
                    name: 'Status'
                },
                {
                    id: 'created_at',
                    name: 'Created Date'
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
                params.append('date_range', $('.bookingrange').val() || '');
                // Ensure salaryReportTable is defined or remove this line if not needed/available
                // Assuming it was present in original code
                params.append('search', typeof salaryReportTable !== 'undefined' ? salaryReportTable.search() : ($('#globalSearch').val() || ''));

                // Add Month and Year from modal filters
                params.append('export_month', $('#export_month').val() || '');
                params.append('export_year', $('#export_year').val() || '');

                selectedFields.forEach(field => {
                    params.append('fields[]', field);
                });

                $('#exportSalaryModal').modal('hide');
                showSuccessToast('Preparing your ' + exportType.toUpperCase() + ' export...');

                $.ajax({
                    url: '{{ route("salary.report.export", ["box_slug" => request()->route("box_slug")]) }}',
                    method: 'GET',
                    data: params.toString(),
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(blob, status, xhr) {
                        var contentType = xhr.getResponseHeader('content-type');
                        if (contentType && contentType.indexOf('application/json') !== -1) {
                            var reader = new FileReader();
                            reader.onload = function() {
                                try {
                                    var response = JSON.parse(reader.result);
                                    showErrorToast(response.message || 'Export failed.');
                                } catch(e) {
                                    showErrorToast('An error occurred processing the response.');
                                }
                            };
                            reader.readAsText(blob);
                        } else {
                            var filename = "";
                            var disposition = xhr.getResponseHeader('Content-Disposition');
                            if (disposition && disposition.indexOf('attachment') !== -1) {
                                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                                var matches = filenameRegex.exec(disposition);
                                if (matches != null && matches[1]) { 
                                    filename = matches[1].replace(/['"]/g, '');
                                }
                            }
                            if(!filename) filename = 'Salary_Report.' + (exportType === 'pdf' ? 'pdf' : 'xlsx');

                            var url = window.URL.createObjectURL(blob);
                            var a = document.createElement('a');
                            a.href = url;
                            a.download = filename;
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                            a.remove();
                            showSuccessToast('Export downloaded successfully.');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.response instanceof Blob) {
                            var reader = new FileReader();
                            reader.onload = function() {
                                try {
                                    var response = JSON.parse(reader.result);
                                    showErrorToast(response.message || 'Export failed.');
                                } catch(e) {
                                    showErrorToast('An error occurred.');
                                }
                            };
                            reader.readAsText(xhr.response);
                        } else {
                           showErrorToast('An error occurred during export.');
                        }
                    }
                });
            });

            // Global Search Functionality
            $('#searchBtn').on('click', function() {
                const searchValue = $('#globalSearch').val();
                if ($.fn.DataTable.isDataTable('#salaryReportTable')) {
                    $('#salaryReportTable').DataTable().search(searchValue).draw();
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

            // Salary generation removed - Salary should be generated from Employee Salary page
            // All generated salaries will be displayed here automatically

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
                    const empName = emp.user?.name || 'Unnamed';
                    const department = emp.department_relation?.name || 'No Dept';
                    $employeeSelect.append(
                        `<option value="${emp.id}" data-name="${empName.toLowerCase()}" data-dept="${department.toLowerCase()}">
                ${empName} - ${department}
            </option>`
                    );
                });

                (response.data.salary_settings || []).forEach(sal => {
                    const formattedName = sal.name.charAt(0).toUpperCase() + sal.name.slice(1);
                    $salarySelect.append(`<option value="${sal.id}">${formattedName}</option>`);
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
                enableSelect2($('#salary_payroll_deduction_id'), 'Select Deduction');
                enableSelect2($('#salary_payroll_overtime_id'), 'Select Overtime');
                enableSelect2($('#salary_payroll_addition_id'), 'Select Addition');
            }

            function enableSelect2($element, placeholder) {
                if ($element.data('select2')) {
                    $element.select2('destroy');
                }

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
            }


            $('#new-employee-salary').on('show.bs.modal', function() {
                fetchAndStoreSalaryData();
            });

            $(window).on('beforeunload', function() {
                localStorage.removeItem('salaryData');
            });

            // edittoggleSalarySettingBtn click handlers were here...

            // Auto-refresh table every 30 seconds to show newly generated salaries
            setInterval(function() {
                if ($.fn.DataTable.isDataTable('#salaryReportTable')) {
                    $('#salaryReportTable').DataTable().ajax.reload(null, false);
                }
            }, 30000); // 30 seconds

            $(document).on('click', '.edit-salary-btn', function() {
                let empId = $(this).data('id');
                const $salarySelect = $('#edit-employee-salary select#salary_setting_types');
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
                    $salarySelect.empty().append('<option value="">Select Salary Setting</option>');
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
                            $salarySelect.append(
                                `<option value="${sal.id}" ${isSelected}>${sal.name.charAt(0).toUpperCase() + sal.name.slice(1)}</option>`
                            );
                        });
                    } else {
                        $salarySelect.append('<option value="">No salary settings found</option>');
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
                                $('#edit-employee-salary select#emp_id').empty().append(option)
                                    .trigger('change');
                                $('#edit-employee-salary select#emp_id').prop('disabled', true);
                            }
                            //                 $('#edit-employee-salary input#salary_payroll_deduction_ids').val(data.id);
                            // $('#edit-employee-salary input#salary_payroll_overtime_ids').val(data.id);
                            // $('#edit-employee-salary input#salary_payroll_overtime_ids').val(data.id);

                            $('#edit-employee-salary input#salary_id').val(data.id);
                            $('#edit-employee-salary input#net_salary').val(data.net_salary);
                            $('#edit-employee-salary input#basic').val(data.earnings.basic);
                            $('#edit-employee-salary input#da').val(data.earnings.da);
                            $('#edit-employee-salary input#hra').val(data.earnings.hra);
                            $('#edit-employee-salary input#da_sum_amount').val(data.earnings
                                .da_sum_amount);
                            $('#edit-employee-salary input#hra_sum_amount').val(data.earnings
                                .hra_sum_amount);



                            $('#edit-employee-salary input#conveyance').val(data.earnings
                                .conveyance);
                            $('#edit-employee-salary input#allowance').val(data.earnings
                                .allowance);
                            $('#edit-employee-salary input#medical_allowance').val(data.earnings
                                .medical_allowance);
                            $('#edit-employee-salary input#earning_others').val(data.earnings
                                .others);
                            $('#edit-employee-salary input#tds').val(data.deductions.tds);
                            $('#edit-employee-salary input#esi').val(data.deductions.esi);
                            $('#edit-employee-salary input#pf').val(data.deductions.pf);
                            $('#edit-employee-salary input#tds_sum_amount').val(data.deductions
                                .tds_sum_amount);
                            $('#edit-employee-salary input#esi_sum_amount').val(data.deductions
                                .esi_sum_amount);
                            $('#edit-employee-salary input#pf_sum_amount').val(data.deductions
                                .pf_sum_amount);
                            $('#edit-employee-salary input#leave').val(data.deductions.leave);
                            $('#edit-employee-salary input#prof_tax').val(data.deductions
                                .prof_tax);
                            $('#edit-employee-salary input#labour_welfare').val(data.deductions
                                .labour_welfare);
                            $('#edit-employee-salary input#deduction_others').val(data
                                .deductions.othersdeductions);
                            if (data.salary_settings_id) {
                                $salarySelect.val(data.salary_settings_id).trigger('change');
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


            $(document).on('click', '.view-slip-btn', function () {
    const table = $('#salaryReportTable').DataTable();
    const rowData = table.row($(this).closest('tr')).data();

    if (!rowData) {
        alert('Unable to fetch salary data.');
        return;
    }

    // Save data in localStorage
    localStorage.setItem('salarySlipData', JSON.stringify(rowData));

    // Redirect using Laravel route helper
    window.location.href = "{{ box_route('payroll.salary.slip') }}";
});





//             $(document).on('click', '.download-slip-btn', function () {
//     const table = $('#salaryReportTable').DataTable();
//     const rowData = table.row($(this).closest('tr')).data();

//     if (!rowData) {
//         alert('Unable to fetch details.');
//         return;
//     }

//     // Parse numeric values
//     const grossStr = rowData.gross_salary || '0';
//     const netStr = rowData.net_salary || '0';
//     const grossNum = parseFloat(grossStr.replace(/,/g, ''));
//     const netNum = parseFloat(netStr.replace(/,/g, ''));
//     const presentDays = rowData.earnings_breakdown?.present_days || 0;
//     const absentDays = rowData.deductions_breakdown?.absent_days || 0;
//     const workingDays = parseFloat(rowData.total_working_days || '0');
//     const paidDays = presentDays; // Based on earnings calculation
//     const weekOffs = parseFloat(rowData.total_weekly_offs || '0');
//     const holidays = parseFloat(rowData.total_holidays || '0');
//     const leaveDays = parseFloat(rowData.leave_days || '0');
//     const perDay = rowData.earnings_breakdown?.per_day_salary || (workingDays > 0 ? grossNum / workingDays : 0);
//     const calculatedEarning = presentDays * perDay;

//     // Use calculated or net for total earning (match example style: reduced for present, deductions 0)
//     const totalEarning = Math.round(calculatedEarning * 100) / 100; // Round to 2 decimals

//     // ✅ Fill only available data dynamically; leave others as default/empty if not found
//     const paymentDate = rowData.transactions?.[0]?.payment_date ? new Date(rowData.transactions[0].payment_date).toLocaleDateString() : '';
//     if (paymentDate) $('#slipDate').text(paymentDate);

//     if (rowData.box_user?.employee_id) $('#slipEmpCode').text(rowData.box_user.employee_id);
//     if (rowData.box_user?.user?.name) $('#slipEmpName').text(rowData.box_user.user.name);
//     if (rowData.box_user?.department_relation?.name) $('#slipDept').text(rowData.box_user.department_relation.name);
//     if (rowData.box_user?.designation_relation?.name) $('#slipDesignation').text(rowData.box_user.designation_relation.name);
//     // Father name not available in data, so leave as default static

//     $('#slipPresent').text(presentDays);
//     $('#slipAbsent').text(absentDays);
//     $('#slipHoliday').text(holidays.toFixed(2));
//     $('#slipLeave').text(leaveDays.toFixed(2));
//     $('#slipWeekOff').text(weekOffs.toFixed(2));
//     $('#slipPaidDays').text(paidDays);

//     $('#slipRate').text(grossNum.toFixed(2)); // Monthly gross as rate
//     $('#slipPF').text('0.00'); // No data, default 0
//     $('#slipESI').text('0.00'); // No data, default 0
//     $('#slipLoan').text('0.00'); // No data, default 0
//     $('#slipWorkHours').text(`${Math.floor(presentDays * 8)}:00 , 0:0`); // Estimated, no exact data
//     $('#slipOTRate').text('0.00'); // No data
//     $('#slipOTAmount').text('0.00'); // No data

//     $('#slipTotalEarning').text(totalEarning.toFixed(2));
//     $('#slipTotalDeduction').text('0.00'); // Match example: absent reduces earning, not shown as deduction
//     $('#slipNetSalary').text(netNum.toFixed(2));

//     // Amount to words only if net > 0
//     if (netNum > 0) {
//         const numToWords = (num) => {
//             const a = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
//                 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
//             const b = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
//             if ((num = Math.abs(num)) < 20) return a[num];
//             if (num < 100) return b[Math.floor(num / 10)] + (num % 10 ? ' ' + a[num % 10] : '');
//             if (num < 1000) return a[Math.floor(num / 100)] + ' Hundred ' + numToWords(num % 100);
//             if (num < 100000) return numToWords(Math.floor(num / 1000)) + ' Thousand ' + numToWords(num % 1000);
//             if (num < 10000000) return numToWords(Math.floor(num / 100000)) + ' Lakh ' + numToWords(num % 100000);
//             return num;
//         };
//         const netForWords = Math.floor(netNum);
//         $('#slipAmountWords').text(`(${numToWords(netForWords)})`);
//     }

//     const slipElement = document.getElementById('salarySlipContainer');
//     slipElement.style.display = 'block';

//     const opt = {
//         margin: 0.5,
//         filename: `Salary_Slip_${rowData.box_user?.user?.name ?? 'Employee'}_${rowData.month_year ?? ''}.pdf`,
//         image: { type: 'jpeg', quality: 0.98 },
//         html2canvas: { scale: 2, useCORS: true },
//         jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
//     };

//     html2pdf()
//         .set(opt)
//         .from(slipElement)
//         .toPdf()
//         .get('pdf')
//         .then(function (pdf) {
//             const blob = pdf.output('blob');
//             const url = URL.createObjectURL(blob);
//             const newTab = window.open(url);
//             setTimeout(() => {
//                 URL.revokeObjectURL(url);
//             }, 2000);
//             slipElement.style.display = 'none';
//         })
//         .catch((err) => {
//             console.error('PDF generation error:', err);
//             alert('Error generating PDF. Check console for details.');
//             slipElement.style.display = 'none';
//         });
// });


// Print function
function printSalaryReport() {
    // Hide modal header/footer for clean print
    $('#salaryReportModal .modal-header, #salaryReportModal .modal-footer').hide();
    window.print();
    $('#salaryReportModal .modal-header, #salaryReportModal .modal-footer').show();
}

// View all transactions (placeholder)
function viewAllTransactions() {
    alert('Full transaction list - implement as needed (e.g., new modal or page)');
}

// Improved event handler with structured, readable HTML - Fetch full details from API
let isLoadingReport = false; // Flag to prevent multiple simultaneous calls

$(document).on('click', '.view-report-btn', function(e) {
    e.preventDefault();

    // Prevent multiple clicks
    if (isLoadingReport) {
        console.log('Already loading report, please wait...');
        return;
    }

    // Get report ID from data attribute or table row
    let reportId = $(this).data('id');

    if (!reportId) {
        const table = $('#salaryReportTable').DataTable();
        const rowData = table.row($(this).closest('tr')).data();
        if (!rowData || !rowData.id) {
            alert('Unable to fetch details. Report ID not found.');
            return;
        }
        reportId = rowData.id;
    }

    console.log('Fetching report details for ID:', reportId);
    isLoadingReport = true;

    // Show loading state
    $('#salaryDetailsContent').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-2" role="status" style="width: 3rem; height: 3rem;"></div>
            <p class="mb-0">Loading details...</p>
        </div>
    `);
    $('#salaryReportModal').modal('show');

    // Fetch full details from API
    $.ajax({
        url: '{{ route("salary.report.details", ["box_slug" => request()->route("box_slug")]) }}',
        method: 'GET',
        data: { report_id: reportId },
        dataType: 'json',
        success: function(response) {
            isLoadingReport = false;
            console.log('API Response:', response);

            // Handle both response structures: data.report or data directly
            let data = null;
            let salarySettings = null;

            if (response.success && response.data) {
                // Check if data has a 'report' key (new structure)
                if (response.data.report) {
                    data = response.data.report;
                    // Priority: Use snapshot from salary if available, otherwise use salary_settings
                    if (data.salary && data.salary.salary_settings_snapshot) {
                        salarySettings = data.salary.salary_settings_snapshot;
                        console.log('Using salary settings snapshot from salary record');
                    } else {
                        salarySettings = response.data.salary_settings || {};
                    }
                }
                // Otherwise, data itself is the report (old structure or direct response)
                else if (response.data.id) {
                    data = response.data;
                    // Priority: Use snapshot from salary if available
                    if (data.salary && data.salary.salary_settings_snapshot) {
                        salarySettings = data.salary.salary_settings_snapshot;
                        console.log('Using salary settings snapshot from salary record');
                    } else {
                        salarySettings = response.data.salary_settings || {};
                    }
                }
            }

            if (data) {
                try {

                    // Get earnings and deductions from salary settings (not from salary.earnings/deductions)
                    let earnings = {};
                    let deductions = {};

                    // First try to get from salary settings (this is the primary source)
                    if (salarySettings) {
                        // Earnings from salary settings
                        if (salarySettings.basic !== undefined) earnings.basic = salarySettings.basic;
                        if (salarySettings.da !== undefined) earnings.da = salarySettings.da;
                        if (salarySettings.hra !== undefined) earnings.hra = salarySettings.hra;
                        if (salarySettings.conveyance !== undefined) earnings.conveyance = salarySettings.conveyance;
                        if (salarySettings.allowance !== undefined) earnings.allowance = salarySettings.allowance;
                        if (salarySettings.medical_allowance !== undefined) earnings.medical_allowance = salarySettings.medical_allowance;
                        if (salarySettings.special_allowance !== undefined) earnings.special_allowance = salarySettings.special_allowance;
                        if (salarySettings.others !== undefined) earnings.others = salarySettings.others;

                        // Deductions from salary settings
                        if (salarySettings.pf !== undefined) deductions.pf = salarySettings.pf;
                        if (salarySettings.esi !== undefined) deductions.esi = salarySettings.esi;
                        if (salarySettings.tds !== undefined) deductions.tds = salarySettings.tds;
                        if (salarySettings.prof_tax !== undefined) deductions.prof_tax = salarySettings.prof_tax;
                        if (salarySettings.leave !== undefined) deductions.leave = salarySettings.leave;
                        if (salarySettings.labour_welfare !== undefined) deductions.labour_welfare = salarySettings.labour_welfare;
                        if (salarySettings.othersdeductions !== undefined) deductions.othersdeductions = salarySettings.othersdeductions;
                    }

                    // Fallback: Try to get from salary.earnings/deductions if salary settings don't have them
                    if (Object.keys(earnings).length === 0 && data.salary) {
                        if (data.salary.earnings) {
                            if (Array.isArray(data.salary.earnings)) {
                                earnings = data.salary.earnings[0] || {};
                            } else {
                                earnings = data.salary.earnings;
                            }
                        }
                    }

                    if (Object.keys(deductions).length === 0 && data.salary) {
                        if (data.salary.deductions) {
                            if (Array.isArray(data.salary.deductions)) {
                                deductions = data.salary.deductions[0] || {};
                            } else {
                                deductions = data.salary.deductions;
                            }
                        }
                    }

                    const earningsBreakdown = data.earnings_breakdown || {};
                    const deductionsBreakdown = data.deductions_breakdown || {};

                    console.log('Earnings Breakdown:', earningsBreakdown);
                    console.log('Deductions Breakdown:', deductionsBreakdown);

                    // Extract custom fields from salary settings (exclude standard fields)
                    const standardFields = ['id', 'box_id', 'name', 'created_at', 'updated_at', 'deleted_at',
                        'component_types', 'custom_fields_max_amounts', 'custom_fields_definitions',
                        'basic', 'da', 'hra', 'conveyance', 'allowance', 'medical_allowance', 'special_allowance', 'others',
                        'pf', 'esi', 'tds', 'prof_tax', 'leave', 'labour_welfare', 'othersdeductions'];
                    const customFields = {};
                    const customFieldsDefinitions = salarySettings.custom_fields_definitions || [];

                    if (salarySettings && Object.keys(salarySettings).length > 0) {
                        Object.keys(salarySettings).forEach(key => {
                            if (!standardFields.includes(key) && salarySettings[key] !== null && salarySettings[key] !== '') {
                                customFields[key] = salarySettings[key];
                            }
                        });
                    }

                // Safe property access helper
                const safeGet = (obj, path, defaultValue = 'N/A') => {
                    return path.split('.').reduce((o, p) => o ? o[p] : null, obj) ?? defaultValue;
                };

                // Helper to format currency
                const formatCurrency = (value) => {
                    const amount = parseFloat(value || 0);
                    return currencySymbol + amount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                };

                // Helper to format number
                const formatNumber = (value, decimals = 2) => {
                    return parseFloat(value || 0).toFixed(decimals);
                };

                // Get month name
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                const monthYear = monthNames[parseInt(data.month) - 1] + ' ' + data.year;

                // 1. Employee & Salary Info Section
                const infoSection = `
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-2 border-bottom pb-1">
                                <i class="fas fa-user me-1"></i>Employee Information
                            </h6>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-5 mb-0">Name:</label>
                                <span class="col-7">${safeGet(data, 'box_user.user.name') || safeGet(data, 'box_user.first_name')}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-5 mb-0">Employee ID:</label>
                                <span class="col-7">${safeGet(data, 'box_user.employee_id')}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-5 mb-0">Email:</label>
                                <span class="col-7">${safeGet(data, 'box_user.user.email')}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-5 mb-0">Phone:</label>
                                <span class="col-7">${safeGet(data, 'box_user.phone')}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-5 mb-0">Department:</label>
                                <span class="col-7">${safeGet(data, 'box_user.department_relation.name')}</span>
                            </div>
                            <div class="d-flex align-items-center mb-0">
                                <label class="form-label fw-semibold text-muted col-5 mb-0">Designation:</label>
                                <span class="col-7">${safeGet(data, 'box_user.designation_relation.name')}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success mb-2 border-bottom pb-1">
                                <i class="ti ${currencyCode === 'INR' ? 'ti-currency-rupee' : 'ti-coins'} me-1"></i>Salary Summary
                            </h6>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-5 mb-0">Month/Year:</label>
                                <span class="col-7 fw-medium">${monthYear}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-5 mb-0">Gross Salary:</label>
                                <span class="col-7"><span class="badge bg-success fs-14">${formatCurrency(data.gross_salary)}</span></span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-5 mb-0">Net Salary (from settings):</label>
                                <span class="col-7"><span class="badge bg-info fs-14">${formatCurrency(data.gross_salary)}</span></span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-5 mb-0">Total Deductions:</label>
                                <span class="col-7"><span class="badge bg-danger fs-14">${formatCurrency(data.total_deductions)}</span></span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-5 mb-0">Final Salary:</label>
                                <span class="col-7"><span class="badge bg-primary fs-14 fw-bold">${formatCurrency(data.net_salary)}</span></span>
                            </div>
                        </div>
                    </div>
                `;

                // 2. Salary Settings Earnings (from salary_settings table)
                let earningsSettingsHtml = '';
                console.log('Earnings data:', earnings);
                console.log('Salary Settings:', salarySettings);

                if (earnings && Object.keys(earnings).length > 0) {
                    const earningsList = [];
                    // Check if value exists (including 0)
                    if (earnings.basic !== undefined && earnings.basic !== null) earningsList.push({ label: 'Basic', value: earnings.basic });
                    if (earnings.da !== undefined && earnings.da !== null) earningsList.push({ label: 'DA', value: earnings.da });
                    if (earnings.hra !== undefined && earnings.hra !== null) earningsList.push({ label: 'HRA', value: earnings.hra });
                    if (earnings.conveyance !== undefined && earnings.conveyance !== null) earningsList.push({ label: 'Conveyance', value: earnings.conveyance });
                    if (earnings.allowance !== undefined && earnings.allowance !== null) earningsList.push({ label: 'Allowance', value: earnings.allowance });
                    if (earnings.medical_allowance !== undefined && earnings.medical_allowance !== null) earningsList.push({ label: 'Medical Allowance', value: earnings.medical_allowance });
                    if (earnings.special_allowance !== undefined && earnings.special_allowance !== null) earningsList.push({ label: 'Special Allowance', value: earnings.special_allowance });
                    if (earnings.others !== undefined && earnings.others !== null) earningsList.push({ label: 'Others', value: earnings.others });

                    if (earningsList.length > 0) {
                        earningsSettingsHtml = earningsList.map(item => `
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">${item.label}:</label>
                                <span class="col-6 badge bg-light text-dark fs-14">${formatCurrency(item.value)}</span>
                            </div>
                        `).join('');
                    }
                }

                // If no earnings from salary, try to show from salary settings directly
                if (!earningsSettingsHtml && salarySettings) {
                    const earningsList = [];
                    if (salarySettings.basic !== undefined && salarySettings.basic !== null) earningsList.push({ label: 'Basic', value: salarySettings.basic });
                    if (salarySettings.da !== undefined && salarySettings.da !== null) earningsList.push({ label: 'DA', value: salarySettings.da });
                    if (salarySettings.hra !== undefined && salarySettings.hra !== null) earningsList.push({ label: 'HRA', value: salarySettings.hra });
                    if (salarySettings.conveyance !== undefined && salarySettings.conveyance !== null) earningsList.push({ label: 'Conveyance', value: salarySettings.conveyance });
                    if (salarySettings.allowance !== undefined && salarySettings.allowance !== null) earningsList.push({ label: 'Allowance', value: salarySettings.allowance });
                    if (salarySettings.medical_allowance !== undefined && salarySettings.medical_allowance !== null) earningsList.push({ label: 'Medical Allowance', value: salarySettings.medical_allowance });
                    if (salarySettings.special_allowance !== undefined && salarySettings.special_allowance !== null) earningsList.push({ label: 'Special Allowance', value: salarySettings.special_allowance });
                    if (salarySettings.others !== undefined && salarySettings.others !== null) earningsList.push({ label: 'Others', value: salarySettings.others });

                    if (earningsList.length > 0) {
                        earningsSettingsHtml = earningsList.map(item => `
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">${item.label}:</label>
                                <span class="col-6 badge bg-light text-dark fs-14">${formatCurrency(item.value)}</span>
                            </div>
                        `).join('');
                    }
                }

                // 3. Salary Settings Deductions (from salary_settings table)
                let deductionsSettingsHtml = '';
                console.log('Deductions data:', deductions);

                if (deductions && Object.keys(deductions).length > 0) {
                    const deductionsList = [];
                    // Check if value exists (including 0)
                    if (deductions.pf !== undefined && deductions.pf !== null) deductionsList.push({ label: 'PF', value: deductions.pf });
                    if (deductions.esi !== undefined && deductions.esi !== null) deductionsList.push({ label: 'ESI', value: deductions.esi });
                    if (deductions.tds !== undefined && deductions.tds !== null) deductionsList.push({ label: 'TDS', value: deductions.tds });
                    if (deductions.prof_tax !== undefined && deductions.prof_tax !== null) deductionsList.push({ label: 'Professional Tax', value: deductions.prof_tax });
                    if (deductions.leave !== undefined && deductions.leave !== null) deductionsList.push({ label: 'Leave', value: deductions.leave });
                    if (deductions.labour_welfare !== undefined && deductions.labour_welfare !== null) deductionsList.push({ label: 'Labour Welfare', value: deductions.labour_welfare });
                    if (deductions.othersdeductions !== undefined && deductions.othersdeductions !== null) deductionsList.push({ label: 'Others', value: deductions.othersdeductions });

                    if (deductionsList.length > 0) {
                        deductionsSettingsHtml = deductionsList.map(item => `
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">${item.label}:</label>
                                <span class="col-6 badge bg-light text-dark fs-14">${formatCurrency(item.value)}</span>
                            </div>
                        `).join('');
                    }
                }

                // If no deductions from salary, try to show from salary settings directly
                if (!deductionsSettingsHtml && salarySettings) {
                    const deductionsList = [];
                    if (salarySettings.pf !== undefined && salarySettings.pf !== null) deductionsList.push({ label: 'PF', value: salarySettings.pf });
                    if (salarySettings.esi !== undefined && salarySettings.esi !== null) deductionsList.push({ label: 'ESI', value: salarySettings.esi });
                    if (salarySettings.tds !== undefined && salarySettings.tds !== null) deductionsList.push({ label: 'TDS', value: salarySettings.tds });
                    if (salarySettings.prof_tax !== undefined && salarySettings.prof_tax !== null) deductionsList.push({ label: 'Professional Tax', value: salarySettings.prof_tax });
                    if (salarySettings.leave !== undefined && salarySettings.leave !== null) deductionsList.push({ label: 'Leave', value: salarySettings.leave });
                    if (salarySettings.labour_welfare !== undefined && salarySettings.labour_welfare !== null) deductionsList.push({ label: 'Labour Welfare', value: salarySettings.labour_welfare });
                    if (salarySettings.othersdeductions !== undefined && salarySettings.othersdeductions !== null) deductionsList.push({ label: 'Others', value: salarySettings.othersdeductions });

                    if (deductionsList.length > 0) {
                        deductionsSettingsHtml = deductionsList.map(item => `
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">${item.label}:</label>
                                <span class="col-6 badge bg-light text-dark fs-14">${formatCurrency(item.value)}</span>
                            </div>
                        `).join('');
                    }
                }

                // 4. Salary Configuration Breakdown - ORGANIZED BY COMPONENT CATEGORIES
                let salarySettingsSection = '';
                if (salarySettings && Object.keys(salarySettings).length > 0) {
                    const calculatedAmounts = salarySettings._calculated_amounts || {};

                    // Define categories to show
                    const categories = [
                        { key: 'earnings', title: 'Earnings Components', color: 'text-success', icon: 'ti-coins' },
                        { key: 'reimbursements', title: 'Reimbursements Components', color: 'text-info', icon: 'ti-receipt' },
                        { key: 'contributions', title: 'Contributions Components', color: 'text-warning', icon: 'ti-building-bank' },
                        { key: 'deductions', title: 'Deductions Components', color: 'text-danger', icon: 'ti-minus-circle' },
                        { key: 'onetime', title: 'One-Time Components', color: 'text-secondary', icon: 'ti-calendar-event' },
                        { key: 'variable', title: 'Variable Components', color: 'text-primary', icon: 'ti-chart-arrows' }
                    ];

                    let categoriesHtml = '';

                    // Mapping for standard labels
                    const standardLabels = {
                        'basic': 'Basic Salary',
                        'da': 'Dearness Allowance',
                        'hra': 'House Rent Allowance',
                        'conveyance': 'Conveyance Allowance',
                        'allowance': 'Other Allowance',
                        'medical_allowance': 'Medical Allowance',
                        'special_allowance': 'Special Allowance',
                        'others': 'Other Earnings',
                        'pf': 'Provident Fund',
                        'esi': 'Employee State Insurance',
                        'tds': 'Income Tax (TDS)',
                        'prof_tax': 'Professional Tax',
                        'leave': 'Leave Deduction',
                        'labour_welfare': 'Labour Welfare Fund',
                        'othersdeductions': 'Other Deductions',
                        'employee_pf': 'Employee PF Contribution',
                        'employer_pf': 'Employer PF Contribution',
                        'employee_esi': 'Employee ESI Contribution',
                        'employer_esi': 'Employer ESI Contribution'
                    };

                    categories.forEach(cat => {
                        let components = calculatedAmounts[cat.key] || {};

                        // Special handling for Variable Components if not in _calculated_amounts but in snapshot
                        if (cat.key === 'variable' && Object.keys(components).length === 0) {
                            if (salarySettings.variable_amount && salarySettings.variable_amount != 0) {
                                components = {
                                    [salarySettings.variable_label || 'Variable Amount']: salarySettings.variable_amount
                                };
                            }
                        }

                        const componentEntries = Object.entries(components);

                        if (componentEntries.length > 0) {
                            let itemsHtml = '';
                            componentEntries.forEach(([key, values]) => {
                                // Skip metadata or non-component keys
                                if (key === 'total' || key === '_metadata') return;

                                // Get label
                                let label = standardLabels[key] || key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

                                // Check custom field definitions
                                if (customFieldsDefinitions && customFieldsDefinitions.length > 0) {
                                    const def = customFieldsDefinitions.find(f => f.name === key || f.col_name === key);
                                    if (def) label = def.label || def.name;
                                }

                                // Get amount (prefer monthly)
                                const amount = typeof values === 'object' ? (values.monthly || values.amount || 0) : values;

                                if (amount != 0) {
                                    itemsHtml += `
                                        <div class="col-md-4 mb-2">
                                            <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded border-start border-3 border-${cat.color.replace('text-', '')}">
                                                <span class="small text-muted text-truncate me-2" title="${label}">${label}</span>
                                                <span class="fw-bold ${cat.color}">${formatCurrency(amount)}</span>
                                            </div>
                                        </div>
                                    `;
                                }
                            });

                            if (itemsHtml) {
                                categoriesHtml += `
                                    <div class="mb-4">
                                        <h6 class="fw-bold ${cat.color} mb-3 border-bottom pb-2">
                                            <i class="ti ${cat.icon} me-1"></i>${cat.title}
                                        </h6>
                                        <div class="row g-2">
                                            ${itemsHtml}
                                        </div>
                                    </div>
                                `;
                            }
                        }
                    });

                    salarySettingsSection = `
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <div class="card border-0 shadow-none bg-transparent">
                                    <div class="card-header bg-transparent border-0 p-0 mb-3">
                                        <h5 class="fw-bold text-dark mb-0">
                                            <i class="ti ti-settings me-1"></i>Salary Configuration Breakdown
                                            <span class="badge bg-soft-primary text-primary fs-12 ms-2">Structure ID: ${salarySettings.id || 'N/A'}</span>
                                        </h5>
                                    </div>
                                    <div class="card-body p-0">
                                        ${categoriesHtml || '<p class="text-muted text-center py-3">No component data available in this snapshot.</p>'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }

                // 5. Attendance & Calculation Breakdown
                const attendanceBreakdownSection = `
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-secondary mb-2 border-bottom pb-1">
                                <i class="fas fa-calendar-alt me-1"></i>Attendance Breakdown
                            </h6>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">Present Days:</label>
                                <span class="col-6 badge bg-success fs-14">${formatNumber(earningsBreakdown.present_days || data.present_days || 0, 0)}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">Comp Off Days:</label>
                                <span class="col-6 badge bg-info fs-14">${formatNumber(earningsBreakdown.comp_off_days || data.comp_off_days || 0, 0)}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">Half Days:</label>
                                <span class="col-6 badge bg-warning fs-14">${formatNumber(earningsBreakdown.half_days || 0, 0)} (Value: ${formatNumber(earningsBreakdown.half_days_value || 0)})</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">Short Leave:</label>
                                <span class="col-6 badge bg-warning fs-14">${formatNumber(earningsBreakdown.short_leave_days || 0, 0)} (Value: ${formatNumber(earningsBreakdown.short_leave_days_value || 0)})</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">Punch Out Miss:</label>
                                <span class="col-6 badge bg-info fs-14">${formatNumber(earningsBreakdown.punch_out_miss_days || 0, 0)}</span>
                            </div>
                            <div class="d-flex align-items-center mb-0">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">Paid Days:</label>
                                <span class="col-6 badge bg-success fs-14 fw-bold">${formatNumber(earningsBreakdown.paid_days || data.paid_days || 0)}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-danger mb-2 border-bottom pb-1">
                                <i class="fas fa-calculator me-1"></i>Calculation Details
                            </h6>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">Total Days in Month:</label>
                                <span class="col-6 badge bg-secondary fs-14">${formatNumber(earningsBreakdown.total_days_in_month || data.total_days_in_month || 0, 0)}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">Total Working Days:</label>
                                <span class="col-6 badge bg-info fs-14">${formatNumber(earningsBreakdown.total_working_days_in_month || data.total_working_days || 0, 0)}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">Weekly Off Days:</label>
                                <span class="col-6 badge bg-info fs-14">${formatNumber(earningsBreakdown.weekly_off_days || data.total_weekly_offs || 0, 0)}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">Holiday Days:</label>
                                <span class="col-6 badge bg-warning fs-14">${formatNumber(earningsBreakdown.holiday_days || data.total_holidays || 0, 0)}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">Leave Days:</label>
                                <span class="col-6 badge bg-primary fs-14">${formatNumber(earningsBreakdown.leave_days || data.leave_days || 0, 0)}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">Per Day Salary:</label>
                                <span class="col-6 badge bg-warning fs-14">${formatCurrency(earningsBreakdown.per_day_salary || 0)}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">Calculation Base Days:</label>
                                <span class="col-6 badge bg-secondary fs-14">${formatNumber(earningsBreakdown.calculation_base_days || 30, 0)}</span>
                            </div>
                            <div class="d-flex align-items-center mb-0">
                                <label class="form-label fw-semibold text-muted col-6 mb-0">Absent Days:</label>
                                <span class="col-6 badge bg-danger fs-14">${formatNumber(deductionsBreakdown.absent_days_from_attendance || deductionsBreakdown.absent_days || 0, 0)}</span>
                            </div>
                        </div>
                    </div>
                `;

                // 6. Deduction Details
                const deductionDetailsSection = `
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <h6 class="fw-bold text-danger mb-2 border-bottom pb-1">
                                <i class="fas fa-minus-circle me-1"></i>Deduction Details
                            </h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <label class="form-label fw-semibold text-muted col-6 mb-0">Deduction Days:</label>
                                        <span class="col-6 badge bg-danger fs-14">${formatNumber(deductionsBreakdown.deduction_days || 0, 0)}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <label class="form-label fw-semibold text-muted col-6 mb-0">Deduction Per Day:</label>
                                        <span class="col-6 badge bg-warning fs-14">${formatCurrency(deductionsBreakdown.deduction_per_day || 0)}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <label class="form-label fw-semibold text-muted col-6 mb-0">Total Deduction:</label>
                                        <span class="col-6 badge bg-danger fs-14 fw-bold">${formatCurrency(deductionsBreakdown.deduction_amount || data.total_deductions || 0)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // 7. Transaction History
                const transactions = data.transactions || [];
                const transactionsHtml = transactions.length
                    ? transactions.map(t => `
                        <tr>
                            <td class="py-1 small">${new Date(t.payment_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' })}</td>
                            <td class="py-1 small fw-bold text-success">${formatCurrency(t.amount)}</td>
                            <td class="py-1 small">
                                <span class="badge ${t.payment_method === 'Cash' ? 'bg-warning' : 'bg-primary'}">${t.payment_method || 'N/A'}</span>
                            </td>
                            <td class="py-1 small">
                                <span class="badge ${t.status === 'paid' ? 'bg-success' : t.status === 'pending' ? 'bg-warning' : 'bg-danger'}">${t.status || 'N/A'}</span>
                            </td>
                            <td class="py-1 small">${t.transaction_ref || 'N/A'}</td>
                        </tr>
                    `).join('')
                    : '<tr><td colspan="5" class="text-center py-3 text-muted"><i class="fas fa-history fa-lg mb-2 opacity-75"></i><br class="my-1">No transactions available</td></tr>';

                const transactionSection = `
                    <div class="border-top pt-3">
                        <h6 class="fw-bold mb-2 fs-14 text-dark">
                            <i class="fas fa-history me-1"></i>Transaction History
                        </h6>
                        <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th class="small py-1 text-center">Date</th>
                                        <th class="small py-1 text-center">Amount</th>
                                        <th class="small py-1 text-center">Method</th>
                                        <th class="small py-1 text-center">Status</th>
                                        <th class="small py-1 text-center">Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${transactionsHtml}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;

                    // Combine all sections
                    const detailsHtml = `
                        <div class="container-fluid p-0">
                            ${infoSection}
                            <hr class="my-3 border-1 border-secondary opacity-25">
                            ${salarySettingsSection ? salarySettingsSection + '<hr class="my-3 border-1 border-secondary opacity-25">' : ''}
                            ${attendanceBreakdownSection}
                            <hr class="my-3 border-1 border-secondary opacity-25">
                            ${deductionDetailsSection}
                            <hr class="my-3 border-1 border-secondary opacity-25">
                            ${transactionSection}
                        </div>
                    `;

                    $('#salaryDetailsContent').html(detailsHtml);
                } catch (error) {
                    console.error('Error processing response:', error);
                    $('#salaryDetailsContent').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>Error processing salary report details.
                            <br><small>${error.message}</small>
                        </div>
                    `);
                }
            } else {
                console.error('Invalid response structure:', response);
                $('#salaryDetailsContent').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Failed to load salary report details. Invalid response structure.
                        <br><small>Response: ${JSON.stringify(response).substring(0, 200)}...</small>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            isLoadingReport = false;
            console.error('Error fetching salary report details:', {
                status: status,
                error: error,
                response: xhr.responseText,
                statusCode: xhr.status
            });

            let errorMessage = 'Error loading salary report details. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                errorMessage = 'Salary report not found.';
            } else if (xhr.status === 403) {
                errorMessage = 'You do not have permission to view this report.';
            } else if (xhr.status === 500) {
                errorMessage = 'Server error. Please contact administrator.';
            }

            $('#salaryDetailsContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>${errorMessage}
                    <br><small>Status: ${xhr.status} | Error: ${error}</small>
                </div>
            `);
        },
        complete: function() {
            isLoadingReport = false;
            console.log('API call completed');
        }
    });
});


// $(document).on('click', '.view-report-btn', function() {
//     const table = $('#salaryReportTable').DataTable();
//     const rowData = table.row($(this).closest('tr')).data();

//     if (!rowData) {
//         alert('Unable to fetch details.');
//         return;
//     }

//     // Safe property access helper
//     const safeGet = (obj, path, defaultValue = 'N/A') => {
//         return path.split('.').reduce((o, p) => o ? o[p] : null, obj) ?? defaultValue;
//     };

//     // Generate Employee Info Section
//     const employeeSection = `
//         <div class="row mb-3">
//             <div class="col-md-6">
//                 <h6 class="fw-bold text-primary mb-2">
//                     <i class="fas fa-user me-2"></i>Employee Info
//                 </h6>
//                 <p class="mb-1"><strong>Name:</strong> ${safeGet(rowData, 'box_user.user.name')}</p>
//                 <p class="mb-1"><strong>Employee ID:</strong> ${safeGet(rowData, 'box_user.employee_id')}</p>
//                 <p class="mb-1"><strong>Department:</strong> ${safeGet(rowData, 'box_user.department_relation.name')}</p>
//                 <p class="mb-0"><strong>Designation:</strong> ${safeGet(rowData, 'box_user.designation_relation.name')}</p>
//             </div>
//             <div class="col-md-6">
//                 <h6 class="fw-bold text-success mb-2">
//                     <i class="fas fa-dollar-sign me-2"></i>Salary Info
//                 </h6>
//                 <p class="mb-1"><strong>Month:</strong> ${safeGet(rowData, 'month_year')}</p>
//                 <p class="mb-1"><strong>Gross Salary:</strong> <span class="badge bg-success">$${safeGet(rowData, 'gross_salary')}</span></p>
//                 <p class="mb-1"><strong>Net Salary:</strong> <span class="badge bg-info">$${safeGet(rowData, 'net_salary')}</span></p>
//                 <p class="mb-0"><strong>Deductions:</strong> <span class="badge bg-danger">$${safeGet(rowData, 'total_deductions')}</span></p>
//             </div>
//         </div>
//     `;

//     // Generate Earnings/Deductions Breakdown
//     const breakdownSection = `
//         <hr class="my-4">
//         <div class="row mb-3">
//             <div class="col-md-6">
//                 <h6 class="fw-bold text-info mb-2">
//                     <i class="fas fa-chart-line me-2"></i>Earnings Breakdown
//                 </h6>
//                 <p class="mb-1"><strong>Present Days:</strong> <span class="badge bg-light text-dark">${safeGet(rowData, 'earnings_breakdown.present_days', 0)}</span></p>
//                 <p class="mb-0"><strong>Per Day Salary:</strong> <span class="badge bg-warning">$${safeGet(rowData, 'earnings_breakdown.per_day_salary', 0)}</span></p>
//             </div>
//             <div class="col-md-6">
//                 <h6 class="fw-bold text-warning mb-2">
//                     <i class="fas fa-minus-circle me-2"></i>Deductions Breakdown
//                 </h6>
//                 <p class="mb-1"><strong>Absent Days:</strong> <span class="badge bg-light text-dark">${safeGet(rowData, 'deductions_breakdown.absent_days', 0)}</span></p>
//                 <p class="mb-0"><strong>Deduction Amount:</strong> <span class="badge bg-danger">$${safeGet(rowData, 'deductions_breakdown.deduction_amount', 0)}</span></p>
//             </div>
//         </div>
//     `;

//     // Generate Attendance Summary
//     const attendanceSection = `
//         <hr class="my-4">
//         <div class="row mb-3">
//             <div class="col-md-6">
//                 <h6 class="fw-bold text-secondary mb-2">
//                     <i class="fas fa-calendar-alt me-2"></i>Attendance Summary
//                 </h6>
//                 <p class="mb-1"><strong>Total Working Days:</strong> <span class="badge bg-secondary">${safeGet(rowData, 'total_working_days', 0)}</span></p>
//                 <p class="mb-1"><strong>Total Weekly Offs:</strong> <span class="badge bg-info">${safeGet(rowData, 'total_weekly_offs', 0)}</span></p>
//             </div>
//             <div class="col-md-6">
//                 <p class="mb-1"><strong>Total Holidays:</strong> <span class="badge bg-warning">${safeGet(rowData, 'total_holidays', 0)}</span></p>
//                 <p class="mb-1"><strong>Leave Days:</strong> <span class="badge bg-primary">${safeGet(rowData, 'leave_days', 0)}</span></p>
//                 <p class="mb-0"><strong>Paid Days:</strong> <span class="badge bg-success">${safeGet(rowData, 'paid_days', 0)}</span></p>
//             </div>
//         </div>
//     `;

//     // Generate Transaction History Table
//     const transactionsHtml = rowData.transactions?.length
//         ? rowData.transactions.map(t => `
//             <tr>
//                 <td>${new Date(t.payment_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
//                 <td><strong>$${t.amount.toLocaleString()}</strong></td>
//                 <td><span class="badge ${t.payment_method === 'Cash' ? 'bg-warning' : 'bg-primary'}">${t.payment_method}</span></td>
//                 <td>
//                     <span class="badge ${t.status === 'Paid' ? 'bg-success' : t.status === 'Pending' ? 'bg-warning' : 'bg-danger'}">
//                         ${t.status}
//                     </span>
//                 </td>
//             </tr>
//         `).join('')
//         : '<tr><td colspan="4" class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2"></i><br>No transactions found</td></tr>';

//     const transactionSection = `
//         <hr class="my-4">
//         <h6 class="fw-bold mb-3">
//             <i class="fas fa-history me-2"></i>Transaction History
//         </h6>
//         <div class="table-responsive">
//             <table class="table table-sm table-bordered table-hover">
//                 <thead class="table-light">
//                     <tr>
//                         <th>Payment Date</th>
//                         <th>Amount</th>
//                         <th>Method</th>
//                         <th>Status</th>
//                     </tr>
//                 </thead>
//                 <tbody>
//                     ${transactionsHtml}
//                 </tbody>
//             </table>
//         </div>
//     `;

//     // Combine all sections
//     const detailsHtml = `
//         ${employeeSection}
//         ${breakdownSection}
//         ${attendanceSection}
//         ${transactionSection}
//     `;

//     $('#salaryDetailsContent').html(detailsHtml);
//     $('#salaryReportModal').modal('show');
// });



            let deleteId = null;

    function showSuccessToast(message, title = 'Success!') {
        const $toast = $('#toastMessage');
        const $toastBody = $('#toastBody');

        $toast.removeClass('bg-danger bg-warning').addClass('bg-success');
        $toastBody.html(`
            <div class="d-flex align-items-center">
                <i class="ti ti-check-circle me-3 fs-4"></i>
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

    function showErrorToast(message, title = 'Error!') {
        const $toast = $('#toastMessage');
        const $toastBody = $('#toastBody');

        $toast.removeClass('bg-success bg-warning').addClass('bg-danger');
        $toastBody.html(`
            <div class="d-flex align-items-center">
                <i class="ti ti-alert-circle me-3 fs-4"></i>
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

            // Open modal and store ID
            $(document).on('click', '.delete-report-btn', function() {
                deleteId = $(this).data('id');
                $('#delete_modal .modal-body p').text(
                    `You want to delete this salary report. This action cannot be undone.`);
                $('#delete_modal').modal('show');
            });

            // Confirm delete
            $('#confirmDeleteBtn').click(function() {
                if (!deleteId) return;

                $.ajax({
                    url: '{{ route('salary.report.delete', ['box_slug' => request()->route('box_slug')]) }}', // Laravel route
                    type: 'POST',
                    data: {
                        id: deleteId,
                        _token: '{{ csrf_token() }}' // CSRF token
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#delete_modal').modal('hide');
                            if ($.fn.DataTable.isDataTable('#salaryReportTable')) {
                                $('#salaryReportTable').DataTable().ajax.reload(null, false);
                            }
                            showSuccessToast(response.message);
                        } else {
                            showErrorToast(response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        showErrorToast('Error deleting salary report.');
                    }
                });
            });



            $('#edit-salary-form').on('submit', function(e) {
                e.preventDefault();
                let salaryId = $('#salary_id').val();
                let formData = {
                    _token: '{{ csrf_token() }}',
                    salary_id: salaryId,
                    emp_id: $('#emp_id').val(),
                    net_salary: $('#net_salary').val(),
                    basic: $('#basic').val(),
                    da: $('#da').val(),
                    hra: $('#hra').val(),
                    conveyance: $('#conveyance').val(),
                    allowance: $('#allowance').val(),
                    medical_allowance: $('#medical_allowance').val(),
                    others: $('#others').val(),
                    tds: $('#tds').val(),
                    esi: $('#esi').val(),
                    pf: $('#pf').val(),
                    leave: $('#leave').val(),
                    prof_tax: $('#prof_tax').val(),
                    labour_welfare: $('#labour_welfare').val(),
                    deduction_others: $('#deduction_others').val()
                };

                $.ajax({
                    url: '{{ route('salary.update', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'PUT',
                    data: formData,
                    success: function(res) {
                        if (res.success) {
                            showSuccessToast('Salary updated successfully!');
                            $('#edit-employee-salary').modal('hide');
                            // Optional: reload table or update row dynamically
                        } else {
                            showErrorToast('Failed to update salary: ' + res.message);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        showErrorToast('An error occurred while updating salary.');
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


            $('#employeeSalaryForm').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var formData = form.serialize();
                $.ajax({
                    url: "{{ route('salaries.store', ['box_slug' => request()->route('box_slug')]) }}",
                    type: "POST",
                    data: formData,
                    beforeSend: function() {
                        form.find('button[type="submit"]').attr('disabled', true).text(
                            'Processing...');
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            form[0].reset();
                            $('#new-employee-salary').modal('hide');
                        } else {
                            alert(response.message || 'Something went wrong!');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessages = '';
                            $.each(errors, function(key, value) {
                                errorMessages += value + '\n';
                            });
                            alert(errorMessages);
                        } else {
                            alert('An error occurred: ' + xhr.responseJSON.message);
                        }
                    },
                    complete: function() {
                        form.find('button[type="submit"]').attr('disabled', false).text(
                            'Add Employee Salary');
                    }
                });
            });

        });
    </script>
@endpush


@endsection
