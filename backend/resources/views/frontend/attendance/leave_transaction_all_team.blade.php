@extends('layouts.main')
@section('content')
@section('title', 'Team Leave Transactions')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Calculate Team Leave Transactions</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="index.html"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            Employee
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Team Leave Transactions</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <div class="card">
            <div class="card-body">
                <div class="border-bottom d-flex align-items-center justify-content-between pb-3 mb-3">
                    <h4>Calculate Leave Transactions</h4>
                    <button type="button" class="btn btn-primary" id="addLeaveTransactionBtn" data-bs-toggle="modal" data-bs-target="#addLeaveTransactionModal">
                        <i class="ti ti-plus me-2"></i>Add Leave Transaction
                    </button>
                </div>

                <!-- Month/Year Selector -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Select Month (Optional)</label>
                        <select class="form-select" id="selectMonth">
                            <option value="">All Months</option>
                            <option value="01">January</option>
                            <option value="02">February</option>
                            <option value="03">March</option>
                            <option value="04">April</option>
                            <option value="05">May</option>
                            <option value="06">June</option>
                            <option value="07">July</option>
                            <option value="08">August</option>
                            <option value="09">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Select Year (Optional)</label>
                        <select class="form-select" id="selectYear">
                            <option value="">All Years</option>
                            @php
                                $currentYear = date('Y');
                                $startYear = $currentYear - 2;
                                $endYear = $currentYear + 2;
                            @endphp
                            @for($year = $endYear; $year >= $startYear; $year--)
                                <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="button" class="btn btn-primary" id="calculateBtn">
                            <i class="ti ti-calculator me-2"></i>Calculate/Filter
                        </button>
                        <button type="button" class="btn btn-secondary ms-2" id="loadAllBtn">
                            <i class="ti ti-reload me-2"></i>Load All
                                </button>
                            </div>
                        </div>

                <!-- Summary Section -->
                <div class="card mb-4" id="summaryCard" style="display: none;">
                    <div class="card-header">
                        <h5 class="mb-0">Leave Credits Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="summaryTable" class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Employee Name</th>
                                        <th>Total Credits</th>
                                        <th>Credit Count</th>
                                        <th>Current Balances</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Summary data will be populated here -->
                                </tbody>
                            </table>
                                            </div>
                    </div>
                </div>

                <!-- Results Table -->
                <div class="table-responsive">
                    <table id="leaveTransactionsTable" class="table">
                        <thead class="thead-light">
                            <tr>
                                <th>Sr No.</th>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Joining Date</th>
                                        <th>Leave Type</th>
                                        <th>Credit Frequency</th>
                                        <th>Credit Days</th>
                                <th>Transaction Date</th>
                                <th>Credit Leave</th>
                                <th>Previous Balance</th>
                                <th>New Balance</th>
                                        <th>Status</th>
                                <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                            <!-- Data will be populated here -->
                                </tbody>
                            </table>
                </div>
            </div>
        </div>
    </div>
    
</div>
<!-- /Page Wrapper -->

<!-- Add Leave Transaction Modal -->
<div class="modal fade" id="addLeaveTransactionModal" tabindex="-1" aria-labelledby="addLeaveTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLeaveTransactionModalLabel">Add Leave Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addLeaveTransactionForm">
                <div class="modal-body">
                    <div class="row">
                        <!-- Select User -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Select Employee <span class="text-danger">*</span></label>
                            <select class="form-select" id="selectEmployee" name="box_user_id" required>
                                <option value="">-- Select Employee --</option>
                                <!-- Will be populated dynamically -->
                            </select>
                        </div>

                        <!-- Select Leave Type -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Select Leave Type(s) <span class="text-danger">*</span></label>
                            <select class="form-select" id="selectLeaveType" multiple size="5">
                                <option value="">-- Select Leave Type --</option>
                                <!-- Will be populated dynamically -->
                            </select>
                            <small class="text-muted">You can select multiple leave types. Selected types will be removed from the list.</small>
                        </div>

                        <!-- Selected Leave Types Container -->
                        <div class="col-md-12" id="selectedLeaveTypesContainer">
                            <!-- Dynamic input fields will be added here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Leave Transaction Modal -->

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable - make it accessible globally within this scope
            let transactionsTable;

            // Initialize DataTable
            function initDataTable() {
                if ($.fn.DataTable.isDataTable('#leaveTransactionsTable')) {
                    $('#leaveTransactionsTable').DataTable().destroy();
                }

                transactionsTable = $('#leaveTransactionsTable').DataTable({
                    processing: false,
                    serverSide: false,
                    data: [],
                    columns: [
                        { data: null, render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }},
                        { data: 'employee_id' },
                        { data: 'employee_name' },
                        { data: 'department' },
                        { data: 'designation' },
                        { data: 'joining_date', render: function(data) {
                            if (!data) return 'N/A';
                            const date = new Date(data);
                            return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                        }},
                        { data: 'leave_type' },
                        { data: 'credit_frequency' },
                        { data: 'credit_days' },
                        { data: 'transaction_date', render: function(data) {
                            if (!data) return 'N/A';
                            const date = new Date(data);
                            return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                        }},
                        { data: 'credit_leave', render: function(data) {
                            return parseFloat(data || 0).toFixed(2);
                        }},
                        { data: 'previous_balance', render: function(data) {
                            return parseFloat(data || 0).toFixed(2);
                        }},
                        { data: 'new_balance', render: function(data) {
                            return parseFloat(data || 0).toFixed(2);
                        }},
                        { data: 'already_exists', render: function(data) {
                            if (data) {
                                return '<span class="badge bg-warning">Already Exists</span>';
                            }
                            return '<span class="badge bg-success">New</span>';
                        }},
                        { data: 'transaction_remarks' }
                    ],
                    order: [[0, 'asc']],
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    language: {
                        emptyTable: "No data available in table",
                        zeroRecords: "No matching records found",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "Showing 0 to 0 of 0 entries",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        search: "Search:",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    }
                });
            }

            // Initialize empty table
            initDataTable();

            // Small delay to ensure table is fully initialized before loading data
            setTimeout(function() {
                loadAllTransactions();
            }, 100);

            // Initialize Add Leave Transaction Modal
            initializeAddLeaveTransactionModal();

            // Load all button click handler
            $('#loadAllBtn').on('click', function() {
                $('#selectMonth').val('');
                $('#selectYear').val('');
                loadAllTransactions();
            });

            // Calculate/Filter button click handler
            $('#calculateBtn').on('click', function() {
                const month = $('#selectMonth').val();
                const year = $('#selectYear').val();

                if (!month || !year) {
                    alert('Please select both month and year to calculate.');
                    return;
                }

                const $btn = $(this);
                const originalText = $btn.html();
                $btn.prop('disabled', true).html('<i class="ti ti-loader me-2"></i>Calculating...');

            $.ajax({
                    url: "{{ route('calculate.team_leave_transactions.all', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'GET',
                data: {
                        month: month,
                        year: year
                },
                    success: function(response) {
                        console.log('Response received:', response);
                        // Get table instance
                        const table = $('#leaveTransactionsTable').DataTable();

                        if (response.status && response.data && Array.isArray(response.data)) {
                            // Clear and populate table
                            table.clear();

                            if (response.data.length > 0) {
                                console.log('Adding data to table:', response.data.length, 'rows');
                                console.log('First row sample:', response.data[0]);

                                // Ensure all required fields exist
                                const formattedData = response.data.map(function(row, index) {
                                    return {
                                        employee_id: row.employee_id || 'N/A',
                                        employee_name: row.employee_name || 'N/A',
                                        department: row.department || 'N/A',
                                        designation: row.designation || 'N/A',
                                        joining_date: row.joining_date || null,
                                        leave_type: row.leave_type || 'N/A',
                                        credit_frequency: row.credit_frequency || 'N/A',
                                        credit_days: row.credit_days || 0,
                                        transaction_date: row.transaction_date || null,
                                        credit_leave: row.credit_leave || 0,
                                        previous_balance: row.previous_balance || 0,
                                        new_balance: row.new_balance || 0,
                                        already_exists: row.already_exists || false,
                                        transaction_remarks: row.transaction_remarks || ''
                                    };
                                });

                                table.rows.add(formattedData);
                                table.columns.adjust().draw();
                            } else {
                                table.draw();
                            }

                            // Show summary if available
                            if (response.summary && response.summary.length > 0) {
                                displaySummary(response.summary, response.is_calculated);
                            }

                            // Show success message
                            const message = response.is_calculated
                                ? `Calculated ${response.total || 0} leave transactions for selected month/year.`
                                : `Loaded ${response.total || 0} existing leave transactions.`;
                            showToast(message, 'success');
                        } else {
                            console.log('No data or empty array:', response);
                            if (response.data && response.data.length === 0) {
                                // Empty array - show empty table message
                                table.clear().draw();
                            } else {
                                showToast(response.message || 'No transactions found.', 'warning');
                                table.clear().draw();
                            }
                            $('#summaryCard').hide();
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Failed to calculate leave transactions.';
                        showToast(message, 'error');
                        const table = $('#leaveTransactionsTable').DataTable();
                        table.clear().draw();
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Load all existing transactions
            function loadAllTransactions() {
                const $btn = $('#loadAllBtn');
            const originalText = $btn.html();
            $btn.prop('disabled', true).html('<i class="ti ti-loader me-2"></i>Loading...');

            $.ajax({
                url: "{{ route('calculate.team_leave_transactions.all', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'GET',
                success: function(response) {
                    console.log('Load All Response:', response);
                    // Get table instance
                    const table = $('#leaveTransactionsTable').DataTable();

                    if (response.status && response.data && Array.isArray(response.data)) {
                        table.clear();

                        if (response.data.length > 0) {
                            console.log('Adding data to table:', response.data.length, 'rows');
                            console.log('First row sample:', response.data[0]);

                            // Ensure all required fields exist
                            const formattedData = response.data.map(function(row, index) {
                                return {
                                    employee_id: row.employee_id || 'N/A',
                                    employee_name: row.employee_name || 'N/A',
                                    department: row.department || 'N/A',
                                    designation: row.designation || 'N/A',
                                    joining_date: row.joining_date || null,
                                    leave_type: row.leave_type || 'N/A',
                                    credit_frequency: row.credit_frequency || 'N/A',
                                    credit_days: row.credit_days || 0,
                                    transaction_date: row.transaction_date || null,
                                    credit_leave: row.credit_leave || 0,
                                    previous_balance: row.previous_balance || 0,
                                    new_balance: row.new_balance || 0,
                                    already_exists: row.already_exists || false,
                                    transaction_remarks: row.transaction_remarks || ''
                                };
                            });

                            table.rows.add(formattedData);
                            table.columns.adjust().draw();
                        } else {
                            table.draw();
                        }

                        if (response.summary && response.summary.length > 0) {
                            displaySummary(response.summary, false);
                        }

                        showToast(`Loaded ${response.total || 0} existing leave transactions.`, 'success');
                    } else {
                        console.log('No data or empty array:', response);
                        if (response.data && response.data.length === 0) {
                            // Empty array - show empty table message
                            table.clear().draw();
                        } else {
                            showToast(response.message || 'No transactions found.', 'warning');
                            table.clear().draw();
                        }
                        $('#summaryCard').hide();
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Failed to load leave transactions.';
                    showToast(message, 'error');
                    const table = $('#leaveTransactionsTable').DataTable();
                    table.clear().draw();
                    $('#summaryCard').hide();
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalText);
                }
            });
            }

            // Display summary table
            function displaySummary(summary, isCalculated) {
            const $summaryBody = $('#summaryTable tbody');
            $summaryBody.empty();

            if (summary.length === 0) {
                $('#summaryCard').hide();
                return;
            }

            summary.forEach(function(item) {
                let balancesHtml = '';
                if (item.current_balances && Object.keys(item.current_balances).length > 0) {
                    balancesHtml = Object.entries(item.current_balances)
                        .map(([typeId, balance]) => `<span class="badge bg-info me-1">${parseFloat(balance).toFixed(2)}</span>`)
                        .join('');
                        } else {
                    balancesHtml = '<span class="text-muted">N/A</span>';
                }

                const row = `
                    <tr>
                        <td>${item.employee_id}</td>
                        <td>${item.employee_name}</td>
                        <td><strong>${parseFloat(item.total_credits || 0).toFixed(2)}</strong></td>
                        <td>${item.transaction_count || 0}</td>
                        <td>${balancesHtml}</td>
                    </tr>
                `;
                $summaryBody.append(row);
            });

            $('#summaryCard').show();
            }

            // Toast function
            function showToast(message, type = 'success') {
                const toastClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-warning';
                const $toast = $(`
                    <div class="toast align-items-center text-white ${toastClass} border-0" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                        <div class="d-flex">
                            <div class="toast-body">${message}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `);
                $('body').append($toast);
                const toast = new bootstrap.Toast($toast[0]);
                toast.show();
                $toast.on('hidden.bs.toast', function() {
                    $toast.remove();
                });
            }

            // Initialize Add Leave Transaction Modal
            function initializeAddLeaveTransactionModal() {
                let allLeaveTypes = [];
                let selectedLeaveTypes = [];

                // Load employees and leave types when modal opens
                $('#addLeaveTransactionModal').on('show.bs.modal', function() {
                    loadEmployeesForModal();
                    loadLeaveTypesForModal();
                    selectedLeaveTypes = [];
                    $('#selectedLeaveTypesContainer').empty();
                    $('#selectEmployee').val('');
                    $('#selectLeaveType').val(null);
                });

                // Load employees
                function loadEmployeesForModal() {
                    $.ajax({
                        url: "{{ route('employee.get.box.users', ['box_slug' => request()->route('box_slug')]) }}?team_only=true",
                        type: 'GET',
                        success: function(response) {
                            if (response.status && response.data) {
                                const $select = $('#selectEmployee');
                                $select.empty().append('<option value="">-- Select Employee --</option>');
                                response.data.forEach(function(emp) {
                                    $select.append(`<option value="${emp.id}">${emp.name} (${emp.employee_id})</option>`);
                                });
                            }
                        },
                        error: function() {
                            showToast('Failed to load employees.', 'error');
                        }
                    });
                }

                // Load leave types
                function loadLeaveTypesForModal() {
                    $.ajax({
                        url: "{{ route('leave-types.data', ['box_slug' => request()->route('box_slug')]) }}",
                        type: 'GET',
                        data: { fetch_all: true },
                        success: function(response) {
                            if (response.data && Array.isArray(response.data)) {
                                allLeaveTypes = response.data.filter(lt => lt.status === 'Active');
                                updateLeaveTypeDropdown();
                            } else if (response.aaData && Array.isArray(response.aaData)) {
                                // Handle DataTables response format
                                allLeaveTypes = response.aaData.filter(lt => lt.status === 'Active');
                                updateLeaveTypeDropdown();
                            }
                        },
                        error: function() {
                            showToast('Failed to load leave types.', 'error');
                        }
                    });
                }

                // Update leave type dropdown (remove selected ones)
                function updateLeaveTypeDropdown() {
                    const $select = $('#selectLeaveType');
                    const currentValue = $select.val() || [];
                    $select.empty();

                    // Add available leave types (not yet selected)
                    allLeaveTypes.forEach(function(lt) {
                        if (!selectedLeaveTypes.find(st => st.id === lt.id)) {
                            $select.append(`<option value="${lt.id}">${lt.leave_type} (${lt.leave_code || 'N/A'})</option>`);
                        }
                    });

                    // Restore current selection if any
                    if (currentValue.length > 0) {
                        $select.val(currentValue);
                    }
                }

                // Handle leave type selection
                $('#selectLeaveType').on('change', function() {
                    const selectedIds = $(this).val() || [];
                    const newSelections = [];

                    // Find newly selected leave types
                    selectedIds.forEach(function(id) {
                        if (!selectedLeaveTypes.find(st => st.id == id)) {
                            const leaveType = allLeaveTypes.find(lt => lt.id == id);
                            if (leaveType) {
                                selectedLeaveTypes.push(leaveType);
                                newSelections.push(leaveType);
                            }
                        }
                    });

                    // Add input fields for newly selected leave types
                    newSelections.forEach(function(leaveType) {
                        addLeaveTypeInputField(leaveType);
                    });

                    // Update dropdown to remove selected items
                    updateLeaveTypeDropdown();
                });

                // Add input field for a leave type
                function addLeaveTypeInputField(leaveType) {
                    const fieldId = `leave_type_${leaveType.id}`;
                    const fieldHtml = `
                        <div class="col-md-12 mb-3 leave-type-field" data-leave-type-id="${leaveType.id}">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">
                                            <strong>${leaveType.leave_type}</strong> (${leaveType.leave_code || 'N/A'})
                                        </label>
                                        <button type="button" class="btn btn-sm btn-danger remove-leave-type" data-leave-type-id="${leaveType.id}">
                                            <i class="ti ti-x"></i> Remove
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Credit Leave Days <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0" class="form-control"
                                                   name="leave_types[${leaveType.id}][credit_leave]"
                                                   id="${fieldId}_credit"
                                                   placeholder="Enter credit days" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Transaction Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control"
                                                   name="leave_types[${leaveType.id}][transaction_date]"
                                                   id="${fieldId}_date"
                                                   value="${new Date().toISOString().split('T')[0]}" required>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <label class="form-label">Remarks</label>
                                            <textarea class="form-control" rows="2"
                                                      name="leave_types[${leaveType.id}][remarks]"
                                                      id="${fieldId}_remarks"
                                                      placeholder="Enter transaction remarks"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#selectedLeaveTypesContainer').append(fieldHtml);
                }

                // Handle remove leave type
                $(document).on('click', '.remove-leave-type', function() {
                    const leaveTypeId = $(this).data('leave-type-id');

                    // Remove from selected list
                    selectedLeaveTypes = selectedLeaveTypes.filter(st => st.id != leaveTypeId);

                    // Remove the input field
                    $(`.leave-type-field[data-leave-type-id="${leaveTypeId}"]`).remove();

                    // Update dropdown to include this leave type again
                    updateLeaveTypeDropdown();
                });

                // Handle form submission
                $('#addLeaveTransactionForm').on('submit', function(e) {
                    e.preventDefault();

                    const boxUserId = $('#selectEmployee').val();
                    if (!boxUserId) {
                        showToast('Please select an employee.', 'error');
                        return;
                    }

                    if (selectedLeaveTypes.length === 0) {
                        showToast('Please select at least one leave type.', 'error');
                        return;
                    }

                    // Collect form data
                    const formData = {
                        box_user_id: boxUserId,
                        leave_types: {}
                    };

                    let hasError = false;
                    selectedLeaveTypes.forEach(function(lt) {
                        const creditLeave = $(`#leave_type_${lt.id}_credit`).val();
                        const transactionDate = $(`#leave_type_${lt.id}_date`).val();
                        const remarks = $(`#leave_type_${lt.id}_remarks`).val();

                        if (!creditLeave || !transactionDate) {
                            showToast(`Please fill all required fields for ${lt.leave_type}.`, 'error');
                            hasError = true;
                            return;
                        }

                        formData.leave_types[lt.id] = {
                            credit_leave: parseFloat(creditLeave),
                            transaction_date: transactionDate,
                            remarks: remarks || ''
                        };
                    });

                    if (hasError) return;

                    // Submit to backend
                    const $submitBtn = $(this).find('button[type="submit"]');
                    const originalText = $submitBtn.html();
                    $submitBtn.prop('disabled', true).html('<i class="ti ti-loader me-2"></i>Saving...');

                    $.ajax({
                        url: "{{ route('leave.transaction.store', ['box_slug' => request()->route('box_slug')]) }}",
                        type: 'POST',
                        data: {
                            ...formData,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status) {
                                showToast(response.message || 'Leave transactions added successfully.', 'success');
                                $('#addLeaveTransactionModal').modal('hide');
                                $('#addLeaveTransactionForm')[0].reset();
                                selectedLeaveTypes = [];
                                $('#selectedLeaveTypesContainer').empty();

                                // Reload table
                                loadAllTransactions();
                            } else {
                                showToast(response.message || 'Failed to add leave transactions.', 'error');
                            }
                        },
                        error: function(xhr) {
                            const message = xhr.responseJSON?.message || 'Failed to add leave transactions.';
                            showToast(message, 'error');
                        },
                        complete: function() {
                            $submitBtn.prop('disabled', false).html(originalText);
                        }
                    });
                });
            }
        });
    </script>
@endpush

@endsection
