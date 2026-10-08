@extends('layouts.main')
@section('title', 'Finance Settings')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h4 class="page-title">Finance Settings</h4>
        </div>

        <!-- Settings Navigation Tabs -->
        <ul class="nav nav-tabs nav-tabs-solid mb-4" id="settingsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="allowance-tab" data-bs-toggle="tab" data-bs-target="#allowance-content" type="button" role="tab">
                    <i class="ti ti-wallet"></i> Allowance Types
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="category-tab" data-bs-toggle="tab" data-bs-target="#category-content" type="button" role="tab">
                    <i class="ti ti-category"></i> Expense Categories
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="rules-tab" data-bs-toggle="tab" data-bs-target="#rules-content" type="button" role="tab">
                    <i class="ti ti-checkup-list"></i> Approval Rules
                </button>
            </li>
        </ul>

        <div class="tab-content" id="settingsTabContent">
            
            <!-- ALLOWANCE TYPES TAB -->
            <div class="tab-pane fade show active" id="allowance-content" role="tabpanel">
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAllowanceModal">
                        <i class="ti ti-plus"></i> Add Allowance Type
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table custom-table w-100" id="allowanceTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Eligible Roles</th>
                                        <th>Calculation</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Added By</th>
                                        <th>Updated By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EXPENSE CATEGORIES TAB -->
            <div class="tab-pane fade" id="category-content" role="tabpanel">
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="ti ti-plus"></i> Add Category
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table custom-table w-100" id="categoryTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Added By</th>
                                        <th>Updated By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- APPROVAL RULES TAB -->
            <div class="tab-pane fade" id="rules-content" role="tabpanel">
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRuleModal">
                        <i class="ti ti-plus"></i> Add Rule
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table custom-table w-100" id="ruleTable">
                                <thead>
                                    <tr>
                                        <th>Min Amount (₹)</th>
                                        <th>Max Amount (₹)</th>
                                        <th>Required Roles</th>
                                        <th>Status</th>
                                        <th>Added By</th>
                                        <th>Updated By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- End Tab Content -->
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- Allowance Modal -->
<div class="modal fade" id="addAllowanceModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="allowanceForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allowanceModalTitle">Add Allowance Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="allowance_id" name="id">
                    
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="a_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Calculation Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="a_calculation_type" name="calculation_type" required>
                            <option value="">Select Type</option>
                            <option value="Fixed">Fixed</option>
                            <option value="Per KM">Per KM</option>
                            <option value="Per Day">Per Day</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="a_amount" name="amount" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Eligible Roles <span class="text-danger">*</span></label>
                        <select class="form-select" id="a_roles" name="roles[]" multiple required style="height: 120px;">
                            <option value="manager">Manager</option>
                            <option value="field">Field Employee</option>
                            <option value="finance">Finance</option>
                            <option value="admin">Admin</option>
                        </select>
                        <small class="text-muted">Hold CTRL/CMD to select multiple.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="a_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveAllowanceBtn">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="categoryForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalTitle">Add Expense Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="category_id" name="id">
                    
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="c_name" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveCategoryBtn">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Rule Modal -->
<div class="modal fade" id="addRuleModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="ruleForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ruleModalTitle">Add Approval Rule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="rule_id" name="id">
                    
                    <div class="mb-3">
                        <label class="form-label">Min Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="r_min_amount" name="min_amount" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Max Amount (₹) <span class="text-muted">(Leave empty for unlimited)</span></label>
                        <input type="number" step="0.01" class="form-control" id="r_max_amount" name="max_amount">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Required Roles <span class="text-danger">*</span></label>
                        <select class="form-select" id="r_required_roles" name="required_roles[]" multiple required style="height: 120px;">
                            <option value="manager">Manager</option>
                            <option value="finance">Finance</option>
                            <option value="admin">Admin</option>
                        </select>
                        <small class="text-muted">Hold CTRL/CMD to select multiple.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveRuleBtn">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="delete_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1">Confirm Delete</h4>
                <p class="mb-3">Are you sure you want to delete this record? This action cannot be undone.</p>
                <div class="d-flex justify-content-center">
                    <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="status_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-warning text-warning mb-3">
                    <i class="ti ti-alert-circle fs-36"></i>
                </span>
                <h4 class="mb-1">Confirm Status Change</h4>
                <p class="mb-3">Are you sure you want to change the status of this record?</p>
                <div class="d-flex justify-content-center">
                    <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                    <button type="button" class="btn btn-warning" id="confirmStatusBtn">Yes, Change</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let boxSlug = "{{ session('box_slug', request()->route('box_slug')) }}";

    function showToast(message, type = 'success') {
        const $toast = $('#dynamicToast');
        const $message = $('#dynamicToastMessage');
        if($toast.length === 0) return; // safety
        $message.text(message);
        $toast.removeClass('bg-success bg-danger bg-warning')
            .addClass(type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-warning');
        const toast = new bootstrap.Toast($toast[0]);
        toast.show();
    }

    let deleteAction = null;
    let statusAction = null;

    $('#confirmDeleteBtn').on('click', function() {
        if (deleteAction) deleteAction();
        $('#delete_modal').modal('hide');
    });

    $('#confirmStatusBtn').on('click', function() {
        if (statusAction) statusAction();
        $('#status_modal').modal('hide');
    });

    // -----------------------------------------------------
    // ALLOWANCE TYPES logic
    // -----------------------------------------------------
    let allowanceTable = $('#allowanceTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: { url: "{{ route('finance.allowance-types.data', ['box_slug' => session('box_slug', request()->route('box_slug'))]) }}", type: "GET" },
        columns: [
            { data: 'name', name: 'name' },
            { 
                data: 'policies', 
                render: function(data) {
                    if (!data || data.length === 0) return '';
                    return data.map(p => `<span class="badge bg-info me-1">${p.role_name}</span>`).join('');
                }
            },
            { 
                data: 'calculation_type', 
                render: function(data) { return `<span class="badge bg-secondary">${data}</span>`; }
            },
            { 
                data: 'amount', 
                render: function(data) { return '₹' + parseFloat(data).toFixed(2); }
            },
            { 
                data: 'is_active', 
                render: function(data) {
                    return data ? `<span class="badge bg-success">Active</span>` : `<span class="badge bg-danger">Inactive</span>`;
                }
            },
            {
                data: 'creator.name',
                name: 'creator.name',
                defaultContent: 'System'
            },
            {
                data: 'updater.name',
                name: 'updater.name',
                defaultContent: 'System'
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    let btnClass = row.is_active ? 'btn-danger' : 'btn-success';
                    let icon = row.is_active ? 'ti-toggle-left' : 'ti-toggle-right';
                    return `
                        <button class="btn btn-sm btn-info edit-allowance-btn" data-id="${row.id}" title="Edit">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm ${btnClass} toggle-allowance-btn" data-id="${row.id}" title="Toggle Status">
                            <i class="ti ${icon}"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-allowance-btn" data-id="${row.id}" title="Delete">
                            <i class="ti ti-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    $('#allowanceForm').on('submit', function(e) {
        e.preventDefault();
        $('#saveAllowanceBtn').prop('disabled', true).text('Saving...');
        let id = $('#allowance_id').val();
        let updateRoute = "{{ route('finance.allowance-types.update', ['box_slug' => ':slug', 'allowance_type' => ':id']) }}";
        let storeRoute = "{{ route('finance.allowance-types.store', ['box_slug' => ':slug']) }}";
        let url = id ? updateRoute.replace(':slug', boxSlug).replace(':id', id) : storeRoute.replace(':slug', boxSlug);
        
        $.ajax({
            url: url,
            type: id ? 'PUT' : 'POST',
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                if (response.success) {
                    $('#addAllowanceModal').modal('hide');
                    allowanceTable.ajax.reload();
                    showToast(response.message || 'Saved successfully.', 'success');
                }
            },
            error: function() { showToast('Error saving data.', 'error'); },
            complete: function() { $('#saveAllowanceBtn').prop('disabled', false).text('Save'); }
        });
    });

    $('#addAllowanceModal').on('hidden.bs.modal', function () {
        $('#allowanceForm')[0].reset();
        $('#allowance_id').val('');
        $('#a_roles').val([]);
        $('#allowanceModalTitle').text('Add Allowance Type');
    });

    $('#allowanceTable tbody').on('click', '.edit-allowance-btn', function() {
        let data = allowanceTable.row($(this).parents('tr')).data();
        $('#allowance_id').val(data.id);
        $('#a_name').val(data.name);
        $('#a_calculation_type').val(data.calculation_type);
        $('#a_amount').val(data.amount);
        $('#a_description').val(data.description);
        
        let roles = data.policies ? data.policies.map(p => p.role_name) : [];
        $('#a_roles').val(roles);

        $('#allowanceModalTitle').text('Edit Allowance Type');
        $('#addAllowanceModal').modal('show');
    });

    $('#allowanceTable tbody').on('click', '.toggle-allowance-btn', function() {
        let id = $(this).data('id');
        let statusRoute = "{{ route('finance.allowance-types.status', ['box_slug' => ':slug', 'id' => ':id']) }}";
        
        statusAction = function() {
            $.ajax({
                url: statusRoute.replace(':slug', boxSlug).replace(':id', id),
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(res) { 
                    if (res.success) {
                        allowanceTable.ajax.reload();
                        showToast(res.message || 'Status updated.', 'success');
                    }
                }
            });
        };
        $('#status_modal').modal('show');
    });

    $('#allowanceTable tbody').on('click', '.delete-allowance-btn', function() {
        let id = $(this).data('id');
        let destroyRoute = "{{ route('finance.allowance-types.destroy', ['box_slug' => ':slug', 'allowance_type' => ':id']) }}";
        
        deleteAction = function() {
            $.ajax({
                url: destroyRoute.replace(':slug', boxSlug).replace(':id', id),
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(res) { 
                    if (res.success) {
                        allowanceTable.ajax.reload();
                        showToast(res.message || 'Deleted successfully.', 'success');
                    }
                }
            });
        };
        $('#delete_modal').modal('show');
    });

    // -----------------------------------------------------
    // EXPENSE CATEGORIES logic
    // -----------------------------------------------------
    let categoryTable = $('#categoryTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: { url: "{{ route('finance.expense-categories.data', ['box_slug' => session('box_slug', request()->route('box_slug'))]) }}", type: "GET" },
        columns: [
            { data: 'name', name: 'name' },
            { 
                data: 'is_active', 
                render: function(data) {
                    return data ? `<span class="badge bg-success">Active</span>` : `<span class="badge bg-danger">Inactive</span>`;
                }
            },
            {
                data: 'creator.name',
                name: 'creator.name',
                defaultContent: 'System'
            },
            {
                data: 'updater.name',
                name: 'updater.name',
                defaultContent: 'System'
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    let btnClass = row.is_active ? 'btn-danger' : 'btn-success';
                    let icon = row.is_active ? 'ti-toggle-left' : 'ti-toggle-right';
                    return `
                        <button class="btn btn-sm btn-info edit-category-btn" data-id="${row.id}" title="Edit">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm ${btnClass} toggle-category-btn" data-id="${row.id}" title="Toggle Status">
                            <i class="ti ${icon}"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-category-btn" data-id="${row.id}" title="Delete">
                            <i class="ti ti-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    $('#categoryForm').on('submit', function(e) {
        e.preventDefault();
        $('#saveCategoryBtn').prop('disabled', true).text('Saving...');
        let id = $('#category_id').val();
        let updateRoute = "{{ route('finance.expense-categories.update', ['box_slug' => ':slug', 'expense_category' => ':id']) }}";
        let storeRoute = "{{ route('finance.expense-categories.store', ['box_slug' => ':slug']) }}";
        let url = id ? updateRoute.replace(':slug', boxSlug).replace(':id', id) : storeRoute.replace(':slug', boxSlug);
        
        $.ajax({
            url: url,
            type: id ? 'PUT' : 'POST',
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                if (response.success) {
                    $('#addCategoryModal').modal('hide');
                    categoryTable.ajax.reload();
                    showToast(response.message || 'Saved successfully.', 'success');
                }
            },
            error: function() { showToast('Error saving data.', 'error'); },
            complete: function() { $('#saveCategoryBtn').prop('disabled', false).text('Save'); }
        });
    });

    $('#addCategoryModal').on('hidden.bs.modal', function () {
        $('#categoryForm')[0].reset();
        $('#category_id').val('');
        $('#categoryModalTitle').text('Add Expense Category');
    });

    $('#categoryTable tbody').on('click', '.edit-category-btn', function() {
        let data = categoryTable.row($(this).parents('tr')).data();
        $('#category_id').val(data.id);
        $('#c_name').val(data.name);
        $('#categoryModalTitle').text('Edit Expense Category');
        $('#addCategoryModal').modal('show');
    });

    $('#categoryTable tbody').on('click', '.toggle-category-btn', function() {
        let id = $(this).data('id');
        let statusRoute = "{{ route('finance.expense-categories.status', ['box_slug' => ':slug', 'id' => ':id']) }}";
        
        statusAction = function() {
            $.ajax({
                url: statusRoute.replace(':slug', boxSlug).replace(':id', id),
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(res) { 
                    if (res.success) {
                        categoryTable.ajax.reload();
                        showToast(res.message || 'Status updated.', 'success');
                    }
                }
            });
        };
        $('#status_modal').modal('show');
    });

    $('#categoryTable tbody').on('click', '.delete-category-btn', function() {
        let id = $(this).data('id');
        let destroyRoute = "{{ route('finance.expense-categories.destroy', ['box_slug' => ':slug', 'expense_category' => ':id']) }}";
        
        deleteAction = function() {
            $.ajax({
                url: destroyRoute.replace(':slug', boxSlug).replace(':id', id),
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(res) { 
                    if (res.success) {
                        categoryTable.ajax.reload();
                        showToast(res.message || 'Deleted successfully.', 'success');
                    }
                }
            });
        };
        $('#delete_modal').modal('show');
    });

    // -----------------------------------------------------
    // APPROVAL RULES logic
    // -----------------------------------------------------
    let ruleTable = $('#ruleTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: { url: "{{ route('finance.approval-rules.data', ['box_slug' => session('box_slug', request()->route('box_slug'))]) }}", type: "GET" },
        columns: [
            { 
                data: 'min_amount', 
                render: function(data) { return '₹' + parseFloat(data).toFixed(2); }
            },
            { 
                data: 'max_amount', 
                render: function(data) { return data ? '₹' + parseFloat(data).toFixed(2) : 'No Limit'; }
            },
            { 
                data: 'roles_list', 
                render: function(data) {
                    if (!data) return '';
                    return data.split(',').map(r => `<span class="badge bg-info me-1">${r.trim()}</span>`).join('');
                }
            },
            { 
                data: 'is_active', 
                render: function(data) {
                    return data ? `<span class="badge bg-success">Active</span>` : `<span class="badge bg-danger">Inactive</span>`;
                }
            },
            {
                data: 'creator.name',
                name: 'creator.name',
                defaultContent: 'System'
            },
            {
                data: 'updater.name',
                name: 'updater.name',
                defaultContent: 'System'
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    let btnClass = row.is_active ? 'btn-danger' : 'btn-success';
                    let icon = row.is_active ? 'ti-toggle-left' : 'ti-toggle-right';
                    return `
                        <button class="btn btn-sm btn-info edit-rule-btn" data-id="${row.id}" title="Edit">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm ${btnClass} toggle-rule-btn" data-id="${row.id}" title="Toggle Status">
                            <i class="ti ${icon}"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-rule-btn" data-id="${row.id}" title="Delete">
                            <i class="ti ti-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    $('#ruleForm').on('submit', function(e) {
        e.preventDefault();
        $('#saveRuleBtn').prop('disabled', true).text('Saving...');
        let id = $('#rule_id').val();
        let updateRoute = "{{ route('finance.approval-rules.update', ['box_slug' => ':slug', 'approval_rule' => ':id']) }}";
        let storeRoute = "{{ route('finance.approval-rules.store', ['box_slug' => ':slug']) }}";
        let url = id ? updateRoute.replace(':slug', boxSlug).replace(':id', id) : storeRoute.replace(':slug', boxSlug);
        
        $.ajax({
            url: url,
            type: id ? 'PUT' : 'POST',
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                if (response.success) {
                    $('#addRuleModal').modal('hide');
                    ruleTable.ajax.reload();
                    showToast(response.message || 'Saved successfully.', 'success');
                }
            },
            error: function() { showToast('Error saving data.', 'error'); },
            complete: function() { $('#saveRuleBtn').prop('disabled', false).text('Save'); }
        });
    });

    $('#addRuleModal').on('hidden.bs.modal', function () {
        $('#ruleForm')[0].reset();
        $('#rule_id').val('');
        $('#r_required_roles').val([]);
        $('#ruleModalTitle').text('Add Approval Rule');
    });

    $('#ruleTable tbody').on('click', '.edit-rule-btn', function() {
        let data = ruleTable.row($(this).parents('tr')).data();
        $('#rule_id').val(data.id);
        $('#r_min_amount').val(data.min_amount);
        $('#r_max_amount').val(data.max_amount);
        
        if (data.required_roles && Array.isArray(data.required_roles)) {
            $('#r_required_roles').val(data.required_roles);
        }
        $('#ruleModalTitle').text('Edit Approval Rule');
        $('#addRuleModal').modal('show');
    });

    $('#ruleTable tbody').on('click', '.toggle-rule-btn', function() {
        let id = $(this).data('id');
        let statusRoute = "{{ route('finance.approval-rules.status', ['box_slug' => ':slug', 'id' => ':id']) }}";
        
        statusAction = function() {
            $.ajax({
                url: statusRoute.replace(':slug', boxSlug).replace(':id', id),
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(res) { 
                    if (res.success) {
                        ruleTable.ajax.reload();
                        showToast(res.message || 'Status updated.', 'success');
                    }
                }
            });
        };
        $('#status_modal').modal('show');
    });

    $('#ruleTable tbody').on('click', '.delete-rule-btn', function() {
        let id = $(this).data('id');
        let destroyRoute = "{{ route('finance.approval-rules.destroy', ['box_slug' => ':slug', 'approval_rule' => ':id']) }}";
        
        deleteAction = function() {
            $.ajax({
                url: destroyRoute.replace(':slug', boxSlug).replace(':id', id),
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(res) { 
                    if (res.success) {
                        ruleTable.ajax.reload();
                        showToast(res.message || 'Deleted successfully.', 'success');
                    }
                }
            });
        };
        $('#delete_modal').modal('show');
    });
});
</script>
@endpush
@endsection
