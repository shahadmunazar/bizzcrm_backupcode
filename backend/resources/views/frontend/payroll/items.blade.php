@extends('layouts.main')
@section('content')
@section('title', 'PayRoll Items')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Payroll Items </h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="#"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            HR
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Payroll Items </li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                <div class="mb-2">
                    <div class="dropdown">
                        <a href="javascript:void(0);"
                            class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                            data-bs-toggle="dropdown">
                            <i class="ti ti-file-export me-1"></i>Export
                        </a>
                        <ul class="dropdown-menu  dropdown-menu-end p-3">
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1"><i
                                        class="ti ti-file-type-pdf me-1"></i>Export as PDF </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1"><i
                                        class="ti ti-file-type-xls me-1"></i>Export as Excel </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="head-icons ms-2">
                    <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-original-title="Collapse" id="collapse-header">
                        <i class="ti ti-chevrons-up"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap gy-2 justify-content-between my-4">
            <div class="payroll-btns">
                <a href="{{ box_route('payroll.payroll.items') }}" class="btn btn-white active border me-2">Additions
                </a>
                <a href="{{ box_route('payroll.over.time') }}" class="btn btn-white border me-2">Overtime </a>
                <a href="{{ box_route('payroll.deduction') }}" class="btn btn-white border">Deductions </a>
            </div>
            <div class="mb-2">
                <a href="#" data-bs-toggle="modal" data-bs-target="#add_payroll"
                    class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add Addition
                </a>
            </div>
        </div>

        <!-- /Breadcrumb -->

        <!-- Payroll list -->
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                <h5>Additions List </h5>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">

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
                        </ul>
                    </div>
                </div>
            </div>
            {{-- <div class="card-body p-0">
						 <div class="custom-datatable-filter table-responsive">
							 <table class="table datatable">
								 <thead class="thead-light">
									 <tr>
										 <th class="no-sort">
											 <div class="form-check form-check-md">
												 <input class="form-check-input" type="checkbox" id="select-all" />
											 </div>
										 </th>
										 <th>Name </th>
										 <th>Category </th>
										 <th>Default / Unit Amount </th>
										 <th></th>
									 </tr>
								 </thead>
								 <tbody>
									 <tr>
										 <td>
											 <div class="form-check form-check-md">
												 <input class="form-check-input" type="checkbox" />
											 </div>
										 </td>
										 <td>
											 <h6 class="fs-14 fw-medium text-gray-9">Leave Balance Amount </h6>
										 </td>
										 <td>Monthly Remuneration </td>
										 <td>
											$5
										 </td>
										 <td>
											 <div class="action-icon d-inline-flex">
												 <a href="#" class="me-2" data-bs-toggle="modal" data-bs-target="#edit_payroll"><i class="ti ti-edit"></i></a>
												 <a href="#" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
											 </div>
										 </td>
									 </tr>
									 <tr>
										 <td>
											 <div class="form-check form-check-md">
												 <input class="form-check-input" type="checkbox" />
											 </div>
										 </td>
										 <td>
											 <h6 class="fs-14 fw-medium text-gray-9">Arrears of Salary </h6>
										 </td>
										 <td>Additional  Remuneration </td>
										 <td>
											$8
										 </td>
										 <td>
											 <div class="action-icon d-inline-flex">
												 <a href="#" class="me-2" data-bs-toggle="modal" data-bs-target="#edit_payroll"><i class="ti ti-edit"></i></a>
												 <a href="#" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
											 </div>
										 </td>
									 </tr>
									 <tr>
										 <td>
											 <div class="form-check form-check-md">
												 <input class="form-check-input" type="checkbox" />
											 </div>
										 </td>
										 <td>
											 <h6 class="fs-14 fw-medium text-gray-9">Gratuity </h6>
										 </td>
										 <td>Monthly Remuneration </td>
										 <td>
											$20
										 </td>
										 <td>
											 <div class="action-icon d-inline-flex">
												 <a href="#" class="me-2" data-bs-toggle="modal" data-bs-target="#edit_payroll"><i class="ti ti-edit"></i></a>
												 <a href="#" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
											 </div>
										 </td>
									 </tr>
								 </tbody>
							 </table>
						 </div>
					 </div> --}}
            <table class="table" id="payroll-table">
                <thead class="thead-light">
                    <tr>
                        <th class="no-sort"><input type="checkbox" id="select-all"></th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Default / Unit Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>

        </div>
        <!-- /Payroll list -->

    </div>

    

</div>
<!-- /Page Wrapper -->

<div class="modal fade" id="add_payroll" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title">Add Payroll Addition</h5>
                <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form id="addPayrollForm">
                @csrf
                <div class="modal-body">
                    <!-- Name -->
                    <div class="mb-3">
                        <label for="addition_name" class="form-label fw-semibold">Name</label>
                        <input type="text" name="name" id="addition_name" class="form-control"
                            placeholder="Enter addition name" required>
                    </div>

                    <!-- Category -->
                    <!--<div class="mb-3">
                        <label for="category_name" class="form-label fw-semibold">Category</label>
                        <select name="category_name" id="category_name" class="form-select" required>
                            <option value="">Select Category</option>
                            <option value="Monthly Remuneration">Monthly Remuneration</option>
                            <option value="Additional Remuneration">Additional Remuneration</option>
                        </select>
                    </div>-->

                    <div class="mb-3">
                        <label class="col-form-label">Input Type <span class="text-danger">*</span></label>
                        <select class="select">
							<option disabled selected>Select input Type</option>
							<option>Amount</option>
							<option>Percentage</option>
						</select>
                    </div>

                    <div class="mb-3">
                            <label for="addition_amount" class="form-label fw-semibold">Value</label>
                            <input type="text" placeholder="Value" class="form-control">
                    </div>

                    <div class="row">
                        <!-- Amount -->
                        <!--<div class="col-md-8 mb-3">
                            <label for="addition_amount" class="form-label fw-semibold">Amount</label>
                            <input type="number" name="amount" id="addition_amount" class="form-control"
                                min="0" step="0.01" placeholder="Enter amount" required>
                        </div> -->

                        <!-- Unit Calculation -->
                        <!--<div class="col-md-4 mb-3 d-flex align-items-center">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_unit_calculation"
                                    id="is_unit_calculation">
                                <label class="form-check-label fw-semibold" for="is_unit_calculation">Unit
                                    Calculation</label>
                            </div>
                        </div> -->
                    </div>

                    <!-- Assignee -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assignee</label>
                        <div class="d-flex gap-3 flex-wrap">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="assignee_type"
                                    value="no_assignee" checked>
                                <label class="form-check-label">No Assignee</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="assignee_type"
                                    value="all_employees">
                                <label class="form-check-label">All Employees</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="assignee_type"
                                    value="select_employee">
                                <label class="form-check-label">Select Employee</label>
                            </div>
                        </div>
                    </div>
                

                <div class="mb-3" id="select_employee_field" style="display: none;">
                    <label for="select_employees" class="form-label fw-semibold">Select Employees</label>
                    <select name="select_employees[]" id="select_employees" class="form-select" multiple></select>
                    <div id="selected_employees" class="mt-2"></div>
                </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="">Select</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Addition</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- /Add Payroll -->

<!-- Edit  Payroll -->
<div class="modal fade" id="edit_payroll" tabindex="-1" aria-labelledby="editPayrollLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editPayrollLabel">Edit Addition</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="edit_payroll_form">
                <div class="modal-body pb-0">
                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="payroll_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="payroll_name" name="name"
                                    placeholder="Enter name" />
                            </div>
                        </div>
                        <!-- Category -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="payroll_category" class="form-label">Category Name</label>
                                <select class="form-select" id="payroll_category" name="category_name">
                                    <option value="Monthly Remuneration">Monthly Remuneration</option>
                                    <option value="Additional Remuneration">Additional Remuneration</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <!-- Amount & Unit Calculation -->
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="payroll_amount" class="form-label">Amount</label>
                                <input type="text" class="form-control" id="payroll_amount" name="amount"
                                    placeholder="Enter amount" />
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-center">
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="payroll_unit_calc" name="is_unit_calculation">
                                <label class="form-check-label fs-12 fw-normal" for="payroll_unit_calc">Unit
                                    Calculation</label>
                            </div>
                        </div>
                        <!-- Assignee Type -->
                        <div class="col-md-12 mt-2">
                            <div class="mb-3">
                                <label class="form-label">Assignee Type</label>
                                <div class="d-flex">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" name="assignee_type"
                                            id="assignee_none" value="no_assignee">
                                        <label class="form-check-label fs-14 fw-medium text-dark"
                                            for="assignee_none">No Assignee</label>
                                    </div>
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" name="assignee_type"
                                            id="assignee_all" value="all_employees">
                                        <label class="form-check-label fs-14 fw-medium text-dark"
                                            for="assignee_all">All Employees</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="assignee_type"
                                            id="assignee_select" value="select_employee">
                                        <label class="form-check-label fs-14 fw-medium text-dark"
                                            for="assignee_select">Select Employee</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3" id="select_employee_field" style="display: none;">
                            <label for="select_employees" class="form-label fw-semibold">Select Employees</label>
                            <select name="select_employees[]" id="select_employees" class="form-select" multiple>

                            </select>
                            <div id="selected_employees" class="mt-2"></div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="">Select</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-white border me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- /Edit  Payroll -->

<!-- Delete Modal -->
<div class="modal fade" id="delete_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1">Confirm Delete</h4>
                <p class="mb-3">You want to delete this item? This action cannot be undone.</p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirm_delete" class="btn btn-danger">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    <div id="dynamicToast" class="toast align-items-center text-white bg-success border-0" role="alert"
        aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="dynamicToastMessage">
                Success!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>
<!-- /Delete Modal -->

@push('scripts')
    <script>
        function showToast(message, type = 'success') {
            const toastEl = $('#dynamicToast');
            const toast = new bootstrap.Toast(toastEl[0]);
            const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
            toastEl.removeClass('bg-success bg-danger').addClass(bgClass);
            $('#dynamicToastMessage').text(message);
            toast.show();
        }

        // -----------------------------
        // Debounce helper
        // -----------------------------
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                const context = this;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }

        $(document).ready(function() {
            // Fetch employees from API
            function loadEmployees() {
                $.ajax({
                    url: '{{ route('get.all.select.employee', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                    success: function(res) {
                        const $select = $('#select_employees');
                        $select.empty();
                        console.log('res', res);

                        if (res && res.data && res.data.length) {
                            res.data.forEach(emp => {
                                const name = emp.user ? emp.user.name :
                                `User ID ${emp.user_id}`;
                                $select.append(`<option value="${emp.id}">${name}</option>`);
                            });
                        }
                    },
                    error: function() {
                        alert('Failed to load employees.');
                    }
                });
            }


            loadEmployees();

            // Show/hide employee select field based on radio
            $('input[name="assignee_type"]').on('change', function() {
                const value = $(this).val();
                const $selectField = $('#select_employee_field');
                const $select = $('#select_employees');
                const $container = $('#selected_employees');

                if (value === 'select_employee') {
                    $selectField.slideDown();
                    $select.val([]).trigger('change'); // reset
                    $container.empty();
                } else if (value === 'all_employees') {
                    $selectField.slideDown();
                    // Select all options
                    const allValues = $select.find('option').map(function() {
                        return $(this).val();
                    }).get();
                    $select.val(allValues).trigger('change');
                } else { // no_assignee
                    $selectField.slideUp();
                    $select.val([]).trigger('change');
                    $container.empty();
                }
            });

            // Display selected employees as badges
            $('#select_employees').on('change', function() {
                const selected = $(this).val() || [];
                const $container = $('#selected_employees');
                $container.empty();

                selected.forEach(function(val) {
                    const text = $('#select_employees option[value="' + val + '"]').text();
                    const $badge = $(`
                <span class="badge bg-primary me-1 mb-1">
                    ${text} <a href="#" class="text-white ms-1 remove-employee" data-id="${val}">&times;</a>
                </span>
            `);
                    $container.append($badge);
                });
            });

            // Remove employee badge
            $(document).on('click', '.remove-employee', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const $select = $('#select_employees');
                const values = $select.val() || [];
                const newValues = values.filter(v => v != id);
                $select.val(newValues).trigger('change');
            });






            // -----------------------------
            // DataTable initialization
            // -----------------------------
            var table = $('#payroll-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('payroll.list', ['box_slug' => request()->route('box_slug')]) }}',
                columns: [{
                        data: 'id',
                        render: function(data) {
                            return '<input type="checkbox" class="form-check-input" value="' +
                                data + '">';
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            return data === '1' ?
                                '<span class="badge bg-success">Active</span>' :
                                '<span class="badge bg-warning">Inactive</span>';
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id',
                        render: function(data) {
                            return `
                    <div class="action-icon d-inline-flex">
                        <a href="javascript:void(0)" class="me-2 edit-payroll"><i class="ti ti-edit"></i></a>
                        <a href="javascript:void(0)" class="text-danger delete-payroll"><i class="ti ti-trash"></i></a>
                    </div>
                `;
                        },
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // -----------------------------
            // Apply debounced search
            // -----------------------------
            $('#payroll-table_filter input').off('keyup').on('keyup', debounce(function() {
                table.search(this.value).draw();
            }, 500)); // 500ms delay after user stops typing


            function populatePayrollModal(modalSelector, rowData) {
                const $modal = $(modalSelector);

                // Basic fields
                $modal.find('input[name="name"]').val(rowData.name);
                $modal.find('select[name="category_name"]').val(rowData.category_name);
                $modal.find('input[name="amount"]').val(rowData.amount);
                $modal.find('input[name="is_unit_calculation"]').prop('checked', rowData.is_unit_calculation == 1);

                $modal.find('input[name="assignee_type"]').prop('checked', false);
                $modal.find(`input[name="assignee_type"][value="${rowData.assignee_type}"]`).prop('checked', true)
                    .trigger('change');

                const $selectField = $modal.find('#select_employee_field');
                const $select = $modal.find('#select_employees');
                const $badgeContainer = $modal.find('#selected_employees');

                if (rowData.assignee_type == 'select_employee' || rowData.assignee_type == 'all_employees') {
                    $selectField.show();

                    $.ajax({
                        url: '{{ route('get.all.select.employee', ['box_slug' => request()->route('box_slug')]) }}',
                        type: 'GET',
                        success: function(res) {
                            $select.empty();
                            $badgeContainer.empty();

                            if (res && res.data && res.data.length) {
                                const employeeMap = {};
                                res.data.forEach(emp => {
                                    employeeMap[emp.id] = emp.user?.name || emp.first_name ||
                                        'Unknown';
                                    $select.append(
                                        `<option value="${emp.id}">${employeeMap[emp.id]}</option>`
                                        );
                                });

                                let selectedIds = [];

                                if (rowData.assignee_type === 'all_employees') {
                                    selectedIds = res.data.map(emp => emp.id.toString());
                                } else if (rowData.assignee_type === 'select_employee' && rowData
                                    .employee_ids) {
                                    const decoded = rowData.employee_ids.replace(/&quot;/g, '"');
                                    try {
                                        const parsedIds = JSON.parse(decoded);
                                        selectedIds = parsedIds
                                            .map(id => id.toString())
                                            .filter(id => employeeMap.hasOwnProperty(Number(id)));
                                    } catch (err) {
                                        console.error('Failed to parse employee_ids:', err);
                                        selectedIds = [];
                                    }
                                }

                                $select.val(selectedIds).trigger('change'); // triggers badge update
                            }
                        },
                        error: function() {
                            console.error('Failed to fetch employees.');
                        }
                    });
                } else {
                    $selectField.hide();
                    $select.val([]).trigger('change');
                    $badgeContainer.empty();
                }

                $modal.find('select[name="status"]').val(rowData.status).trigger('change');
                $modal.data('id', rowData.id);
                $modal.modal('show');
            }

            // When user changes selection in the multi-select, update badges
            $(document).on('change', '#select_employees', function() {
                const $modal = $(this).closest('.modal');
                const selected = $(this).val() || [];
                const $container = $modal.find('#selected_employees');
                $container.empty();

                selected.forEach(val => {
                    const text = $(this).find(`option[value="${val}"]`).text();
                    const $tag = $(`
            <span class="badge bg-primary me-1 mb-1">
                ${text} <a href="#" class="text-white ms-1 remove-employee" data-id="${val}">&times;</a>
            </span>
        `);
                    $container.append($tag);
                });
            });

            // When user clicks the '×' on a badge
            $(document).on('click', '.remove-employee', function(e) {
                e.preventDefault();

                const $modal = $(this).closest('.modal');
                const idToRemove = $(this).data('id').toString();
                const $select = $modal.find('#select_employees');
                const currentSelected = $select.val() || [];
                const updatedSelection = currentSelected.filter(id => id !== idToRemove);
                $select.val(updatedSelection).trigger('change');
            });





            // Show badges for selected employees
            $('#select_employees_edit').on('change', function() {
                const selected = $(this).val();
                const $container = $('#selected_employees');
                $container.empty();

                if (selected && selected.length > 0) {
                    selected.forEach(function(val) {
                        const text = $('#select_employees_edit option[value="' + val + '"]').text();
                        const $tag = $(`
                <span class="badge bg-primary me-1 mb-1">
                    ${text} <a href="#" class="text-white ms-1 remove-employee" data-id="${val}">&times;</a>
                </span>
            `);
                        $container.append($tag);
                    });
                }
            });





            $('#select_employees_edit').on('change', function() {
                const selected = $(this).val();
                const $container = $('#select_employees_edit');
                $container.empty();

                if (selected && selected.length > 0) {
                    selected.forEach(function(val) {
                        const text = $('#select_employees_edit option[value="' + val + '"]').text();
                        const $tag = $(`
                <span class="badge bg-primary me-1 mb-1">
                    ${text} <a href="#" class="text-white ms-1 remove-employee" data-id="${val}">&times;</a>
                </span>
            `);
                        $container.append($tag);
                    });
                }
            });


            // Attach click event to edit buttons
            $('#payroll-table tbody').on('click', '.edit-payroll', function() {
                var rowData = table.row($(this).closest('tr')).data();
                populatePayrollModal('#edit_payroll', rowData);
            });

            // Submit edit payroll form
            $('#edit_payroll_form').on('submit', function(e) {
                e.preventDefault();

                const $modal = $('#edit_payroll');
                const id = $modal.data('id');

                const formData = {
                    id: id,
                    name: $modal.find('input[name="name"]').val(),
                    category_name: $modal.find('select[name="category_name"]').val(),
                    amount: $modal.find('input[name="amount"]').val(),
                    is_unit_calculation: $modal.find('input[name="is_unit_calculation"]').is(
                        ':checked') ? 1 : 0,
                    assignee_type: $modal.find('input[name="assignee_type"]:checked').val(),
                    status: $modal.find('select[name="status"]').val(), // Added status
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.ajax({
                    url: "{{ route('payroll.update', ['box_slug' => request()->route('box_slug')]) }}",
                    type: 'PUT',
                    data: formData,
                    success: function(response) {
                        $modal.modal('hide');
                        table.ajax.reload(null,
                        false); // reload datatable without resetting pagination
                        showToast('Payroll updated successfully!', 'success');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        showToast('Failed to update payroll.', 'error');
                    }
                });
            });


            // Delete Payroll
            var deleteId = null;
            $('#payroll-table tbody').on('click', '.delete-payroll', function() {
                var rowData = table.row($(this).closest('tr')).data();
                deleteId = rowData.id;
                $('#delete_modal').modal('show');
            });

            $('#confirm_delete').on('click', function() {
                if (!deleteId) return;

                $.ajax({
                    url: "{{ route('payroll.destroy', ['box_slug' => request()->route('box_slug')]) }}",
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: deleteId
                    },
                    success: function(response) {
                        $('#delete_modal').modal('hide');
                        table.ajax.reload(null, false);
                        showToast('Payroll deleted successfully!', 'success');
                        deleteId = null;
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        showToast('Failed to delete payroll.', 'error');
                    }
                });
            });

            // Add Payroll
            $('#addPayrollForm').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('store.payroll.addition', ['box_slug' => request()->route('box_slug')]) }}",
                    method: "POST",
                    data: formData,
                    dataType: "json",
                    beforeSend: function() {
                        $('#addPayrollForm button[type="submit"]').prop('disabled', true).text(
                            'Adding...');
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, 'success');
                            $('#addPayrollForm')[0].reset();
                            $('#add_payroll').modal('hide');
                            table.ajax.reload(null, false);
                        } else {
                            $.each(response.errors, function(key, value) {
                                showToast(value, 'error');
                            });
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        showToast('Something went wrong. Please try again.', 'error');
                    },
                    complete: function() {
                        $('#addPayrollForm button[type="submit"]').prop('disabled', false).text(
                            'Add Addition');
                    }
                });
            });
        });
    </script>
@endpush


@endsection
