@extends('layouts.main')
@section('content')
@section('title', 'Permissions Management')
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">User Management</h4>
                        </div>
                    </div>
                </div>

                @php
                    use App\Helpers\PermissionHelper;
                    $isPrivilegedUser = PermissionHelper::isPrivilegedUser();
                @endphp
                <div class="row">


                    <!-- Main Content Column -->
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Role Permissions</h5>
                                <div class="d-flex gap-2">
                                    <form action="{{ box_route('user.permissions.sync') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-info btn-sm">
                                            <i class="ti ti-refresh me-1"></i>Sync System Permissions
                                        </button>
                                    </form>
                                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add_permission">
                                        <i class="ti ti-plus me-1"></i>Add Action
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Tabs for Roles -->
                                <ul class="nav nav-tabs nav-tabs-bottom mb-4" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#role_tab_employee" role="tab" onclick="switchRoleTab('employee')">
                                            <i class="ti ti-users me-1"></i>Employee Permissions
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" data-bs-toggle="tab" href="#role_tab_admin" role="tab" onclick="switchRoleTab('admin')">
                                            <i class="ti ti-shield-lock me-1"></i>Admin Permissions
                                        </a>
                                    </li>
                                </ul>

                                <!-- Hidden Inputs for Current Context -->
                                <input type="hidden" id="current_role_name" value="employee">

                                <!-- Filters (Only for Employee Tab) -->
                                <div id="employee_filters" class="mb-4 p-3 bg-light rounded border">
                                    <div class="row align-items-center">
                                        <div class="col-md-5">
                                            <label class="form-label fw-bold">Specific Department/Team (Optional)</label>
                                            <div class="d-flex gap-2">
                                                <select name="department_id[]" class="form-select select2" multiple="multiple" data-placeholder="-- All Departments --">
                                                </select>

                                                <select name="team_id[]" class="form-select select2" multiple="multiple" data-placeholder="-- All Teams --">
                                                </select>
                                            </div>
                                            <small class="text-muted">Refine permissions for specific dept/team.</small>
                                        </div>
                                        <div class="col-md-7 text-end">
                                            <div class="d-flex justify-content-end gap-2 align-items-center h-100">
                                                <div class="me-3">
                                                         <span class="badge bg-primary-light text-primary p-2" id="editing_badge_text">
                                                            <i class="ti ti-lock me-1"></i>
                                                            Editing: <strong>Employee Role</strong>
                                                        </span>
                                                </div>
                                                 <div class="form-check form-switch pt-2 me-3">
                                                    <input class="form-check-input" type="checkbox" id="select_all_global">
                                                    <label class="form-check-label fw-bold" for="select_all_global">Enable All</label>
                                                </div>
                                                <button type="button" id="save_permissions_btn" class="btn btn-primary">
                                                    <i class="ti ti-device-floppy me-1"></i>Save Permissions
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Container for AJAX content -->
                                <div id="permissions_form_container" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading permissions...</span>
                                    </div>
                                    <p class="text-muted mt-2">Loading permissions...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="hidden_submission_form" action="{{ box_route('user.permissions.updateRole') }}" method="POST" style="display:none;">
    @csrf
    <!-- Hidden inputs are appended dynamically -->
    <div id="permission_inputs_container"></div>
</form>

<!-- Add Permission Modal -->
<div class="modal fade" id="add_permission" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ box_route('user.permissions.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Main Module <span class="text-danger">*</span></label>
                            <input list="module_list" name="module" class="form-control" placeholder="e.g., leads, attendance, payroll" required>
                            <datalist id="module_list">
                            </datalist>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Sub-Module / Page</label>
                            <input list="submodule_list" name="submodule" class="form-control" placeholder="e.g., list, details, salary (Optional)">
                            <datalist id="submodule_list">
                            </datalist>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label d-block text-dark fw-medium mb-2">Action Permissions <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-3 border p-3 rounded bg-light">
                                @php
                                    $defaultActions = ['read', 'create', 'update', 'delete', 'import', 'export', 'generate', 'assigned', 'approved', 'disapproved'];
                                @endphp
                                @foreach($defaultActions as $defAction)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="actions[]" value="{{ $defAction }}" id="def_{{ $defAction }}">
                                        <label class="form-check-label" for="def_{{ $defAction }}">
                                            {{ ucfirst($defAction) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2">
                                <label class="form-label small text-muted">Or add custom action (comma separated)</label>
                                <input type="text" name="custom_actions" class="form-control form-control-sm" placeholder="e.g., approve, cancel, refund">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Guard Name</label>
                            <input type="text" name="guard_name" class="form-control" value="web">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light shadow-none" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.custom-permission-table .table-active {
    background-color: rgba(0,0,0,.03);
}
.permission-item-checkbox:checked + .form-check-label {
    color: var(--bs-primary);
    font-weight: 500;
}
</style>

<!-- Custom Toast Notification Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div id="dynamicToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="dynamicToastMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toast Notification Helper
    function showToast(message, type = 'success') {
        const $toast = $('#dynamicToast');
        const $message = $('#dynamicToastMessage');
        $message.text(message);
        $toast.removeClass('bg-success bg-danger bg-warning')
            .addClass(type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-warning');
        new bootstrap.Toast($toast[0]).show();
    }

    // Global State
    let currentRole = 'employee';
    let currentDeptId = '';
    let currentTeamId = '';
    // Role maps populated by AJAX
    let roleMap = {
        'employee': 0,
        'admin': 0
    };

    $(document).ready(function() {
        if($('.select2').length > 0) {
            $('.select2').select2({ width: '100%' });
        }

        // Initial Load
        loadPermissions();

        // ---------------------------------------------------------------------
        // 1. Handle Filter Changes (Dept/Team) - ONLY for Employee Tab
        // ---------------------------------------------------------------------
        $('select[name="department_id[]"]').on('change', function() {
            currentDeptId = $(this).val();
            // Reset Team
            $('select[name="team_id[]"]').val(null).trigger('change.select2');
            currentTeamId = [];

            loadPermissions();
        });

        $('select[name="team_id[]"]').on('change', function() {
            currentTeamId = $(this).val();
            loadPermissions();
        });

        // ---------------------------------------------------------------------
        // 2. Save Permissions
        // ---------------------------------------------------------------------
        $('#save_permissions_btn').click(function() {
            // Collect all checked permissions from the ajax loaded table
            // The table is inside #permissions_form_container
            // We need to move them to our hidden form

            const inputs = $('#permissions_form_container').find('input[name="permissions[]"]:checked').clone();
            $('#permission_inputs_container').html(inputs);

            // Set IDs
            $('#hidden_submission_form').find('.dynamic-input').remove();
            
            if (currentRole === 'admin') {
                $('#hidden_submission_form').append(`<input type="hidden" class="dynamic-input" name="role_id" value="${roleMap['admin']}">`);
            } else {
                // Employee Tab
                if (currentTeamId && currentTeamId.length > 0) {
                    const tIds = Array.isArray(currentTeamId) ? currentTeamId : [currentTeamId];
                    tIds.forEach(id => {
                        $('#hidden_submission_form').append(`<input type="hidden" class="dynamic-input" name="team_id[]" value="${id}">`);
                    });
                } else if (currentDeptId && currentDeptId.length > 0) {
                    const dIds = Array.isArray(currentDeptId) ? currentDeptId : [currentDeptId];
                    dIds.forEach(id => {
                        $('#hidden_submission_form').append(`<input type="hidden" class="dynamic-input" name="department_id[]" value="${id}">`);
                    });
                } else {
                    $('#hidden_submission_form').append(`<input type="hidden" class="dynamic-input" name="role_id" value="${roleMap['employee']}">`);
                }
            }

            // Submit via AJAX
            const form = $('#hidden_submission_form');
            const formData = form.serialize();
            const btn = $(this);
            const originalText = btn.html();
            
            btn.html('<i class="spinner-border spinner-border-sm me-1"></i>Saving...');
            btn.prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: formData,
                success: function(response) {
                    btn.html(originalText);
                    btn.prop('disabled', false);
                    
                    if (response.status || response.success) {
                        showToast(response.message || 'Permissions saved successfully', 'success');
                    } else {
                        showToast(response.message || 'Failed to save permissions', 'error');
                    }
                },
                error: function() {
                    btn.html(originalText);
                    btn.prop('disabled', false);
                    
                    showToast('An error occurred while saving permissions.', 'error');
                }
            });
        });
    });

    // Initialize listeners on page load
    initializeCheckboxListeners();

    // ---------------------------------------------------------------------
    // 3. Tab Switch Handler (Attached to window for inline onclick)
    // ---------------------------------------------------------------------
    window.switchRoleTab = function(roleName) {
        if (currentRole === roleName) return;
        currentRole = roleName;
        
        // Update UI
        if (roleName === 'admin') {
            $('#employee_filters').hide();
             $('#editing_badge_text').html(`<i class="ti ti-lock me-1"></i>Editing: <strong>Admin Role</strong>`);
        } else {
            $('#employee_filters').show();
            updateBadgeText();
        }
        
        loadPermissions();
    }

    function updateBadgeText() {
        if (currentRole === 'admin') {
             $('#editing_badge_text').html(`<i class="ti ti-lock me-1"></i>Editing: <strong>Admin Role</strong>`);
             return;
        }
        
        // Employee Logic
        let text = 'Employee Role (Global)';
        if (currentTeamId && currentTeamId.length > 0) {
            const teamSelect = $('select[name="team_id[]"]');
            const selectedOptions = teamSelect.find('option:selected');
            if (selectedOptions.length === 1) {
                text = selectedOptions.text();
            } else {
                text = `${selectedOptions.length} Teams Selected`;
            }
        } else if (currentDeptId && currentDeptId.length > 0) {
            const deptSelect = $('select[name="department_id[]"]');
            const selectedOptions = deptSelect.find('option:selected');
            if (selectedOptions.length === 1) {
                text = selectedOptions.text();
            } else {
                text = `${selectedOptions.length} Departments Selected`;
            }
        }
        $('#editing_badge_text').html(`<i class="ti ti-lock me-1"></i>Editing: <strong>${text}</strong>`);
    }

    // ---------------------------------------------------------------------
    // 4. Core Load Function
    // ---------------------------------------------------------------------
    function loadPermissions() {
        $('#permissions_form_container').css('opacity', '0.5');
        
        // Determine what ID to send
        let reqRoleId = '';
        let reqDeptId = '';
        let reqTeamId = '';
        
        if (currentRole === 'admin') {
            reqRoleId = roleMap['admin'];
        } else {
            // Employee
            // Always send Department ID if we have it, so the backend knows which teams to return
            if (currentDeptId) {
                reqDeptId = currentDeptId;
            }

            if (currentTeamId) {
                reqTeamId = currentTeamId;
            } else if (!currentDeptId) {
                // If no Dept and no Team, fall back to generic Employee role
                reqRoleId = roleMap['employee'];
            }
        }
        
        $.ajax({
            url: "{{ box_route('manage_users.permissions') }}",
            method: "GET",
            data: {
                role_id: reqRoleId,
                department_id: reqDeptId,
                team_id: reqTeamId,
                ajax_load: true
            },
            success: function(response) {
                $('#permissions_form_container').css('opacity', '1');
                if (response.html) {
                    $('#permissions_form_container').html(response.html).removeClass('text-center py-5');
                    
                     updateBadgeText();

                    // Sync Teams Dropdown
                    // Sync Departments Dropdown
                    if (response.departments) {
                         const deptSelect = $('select[name="department_id[]"]');
                         // Extract properly whether it's an array or string
                         let currentDeptVals = deptSelect.val() || [];
                         if (!Array.isArray(currentDeptVals)) currentDeptVals = [currentDeptVals];
                         
                         deptSelect.empty();
                         response.departments.forEach(d => {
                             const sel = currentDeptVals.includes(d.id.toString()) ? 'selected' : '';
                             deptSelect.append(`<option value="${d.id}" ${sel}>Department: ${d.name}</option>`);
                         });
                    }

                    // Sync Teams Dropdown
                    if (response.teams) {
                         const teamSelect = $('select[name="team_id[]"]');
                         let currentVals = teamSelect.val() || []; 
                         if (!Array.isArray(currentVals)) currentVals = [currentVals];
                         
                         teamSelect.empty();
                         response.teams.forEach(t => {
                             const sel = currentVals.includes(t.id.toString()) ? 'selected' : '';
                             teamSelect.append(`<option value="${t.id}" ${sel}>Team: ${t.name}</option>`);
                         });
                    }

                    // Sync Roles Map explicitly
                    if (response.roles) {
                        roleMap = response.roles;
                    }

                    // Sync Modal Add Datalists
                    if (response.available_modules) {
                        const mList = $('#module_list');
                        mList.empty();
                        response.available_modules.forEach(m => {
                            mList.append(`<option value="${m}">`);
                        });
                    }
                    if (response.available_submodules) {
                        const sList = $('#submodule_list');
                        sList.empty();
                        response.available_submodules.forEach(s => {
                            sList.append(`<option value="${s}">`);
                        });
                    }

                    initializeCheckboxListeners();
                }
            },
            error: function() {
                $('#permissions_form_container').css('opacity', '1');
                $('#permissions_form_container').html('<div class="alert alert-danger">Error loading permissions.</div>');
            }
        });
    }

    function initializeCheckboxListeners() {
        // 1. Downward Propagation (Parent -> Children)
        $(document).off('change', '.module-checkbox').on('change', '.module-checkbox', function() {
            const module = $(this).data('module');
            const isChecked = $(this).is(':checked');
            // Check all permissions under this module
            $(`.module-${module}`).prop('checked', isChecked);
            // Check all submodules under this module
            $(`input[data-module="${module}"].submodule-checkbox`).prop('checked', isChecked);
            
            checkGlobalState();
        });

        $(document).off('change', '.submodule-checkbox').on('change', '.submodule-checkbox', function() {
            const module = $(this).data('module');
            const submodule = $(this).data('submodule');
            const isChecked = $(this).is(':checked');
            // Check all permissions under this submodule
            $(`.submodule-${module}-${submodule}`).prop('checked', isChecked);
            
            // Trigger upward check for module
            checkModuleState(module);
            
            checkGlobalState();
        });

        $('#select_all_global').off('change').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.permission-item-checkbox, .module-checkbox, .submodule-checkbox').prop('checked', isChecked);
        });

        // 2. Upward Propagation (Children -> Parent)
        $(document).off('change', '.permission-item-checkbox').on('change', '.permission-item-checkbox', function() {
            const module = $(this).data('module');
            const submodule = $(this).data('submodule');
            
            checkSubmoduleState(module, submodule);
            checkModuleState(module);
            
            checkGlobalState();
        });

        // Initial sync on load
        updateParentCheckboxes();
    }

    function checkSubmoduleState(module, submodule) {
        if (!module || !submodule) return;
        const total = $(`.submodule-${module}-${submodule}`).length;
        const checked = $(`.submodule-${module}-${submodule}:checked`).length;
        // Check if all are checked
        const allChecked = total > 0 && total === checked;
        $(`#sub_${module}_${submodule}`).prop('checked', allChecked);
    }

    function checkModuleState(module) {
        if (!module) return;
        // Check all submodules under this module
        // We can check either the submodule checkboxes OR all individual permissions.
        // Checking individual permissions is more robust if submodule checkboxes are hidden/missing.
        const total = $(`.module-${module}`).length;
        const checked = $(`.module-${module}:checked`).length;
        
        const allChecked = total > 0 && total === checked;
        $(`#mod_${module}`).prop('checked', allChecked);
    }

    function updateParentCheckboxes() {
        // Iterate all visible modules
        $('.module-checkbox').each(function() {
            const module = $(this).data('module');
            checkModuleState(module);
        });

        // Iterate all visible submodules
        $('.submodule-checkbox').each(function() {
            const module = $(this).data('module');
            const submodule = $(this).data('submodule');
            checkSubmoduleState(module, submodule);
        });
        
        checkGlobalState();
    }
    
    function checkGlobalState() {
        const total = $('.permission-item-checkbox').length;
        const checked = $('.permission-item-checkbox:checked').length;
        const allChecked = total > 0 && total === checked;
        $('#select_all_global').prop('checked', allChecked);
    }
</script>
@endpush
