@extends('layouts.main')
@section('content')
@section('title','Lead Sub Status')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col-sm-8">
                                <h4 class="page-title">Lead Sub Status </h4>
                            </div>
                            <div class="col-sm-4 text-sm-end">
                                <div class="head-icons">
                                    <a href="calls.html" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
                                    <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-original-title="Collapse" id="collapse-header"><i
                                            class="ti ti-chevrons-up"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- /Page Header -->

                    <div class="card">
                        <div class="card-header">
                            <!-- Search -->
                            <div class="row align-items-center">
                                <div class="col-sm-4">
                                    <div class="icon-form mb-3 mb-sm-0">
                                        <span class="form-icon"><i class="ti ti-search"></i></span>
                                        <input type="text" class="form-control" id="LeadSubStatusSearchInput" placeholder="Search Activity">
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="text-sm-end">

                                        <button type="button" class="btn btn-sm btn-white d-inline-flex align-items-center me-2" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                                            <i class="ti ti-columns me-1"></i>Manage Columns
                                        </button>
                                        @if ($isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute('Leadsubstatus.store'))
                                            <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#global_add_leadsubstatus"><i class="ti ti-square-rounded-plus me-2"></i>Add
                                                New</a>
                                        @endif


                                    </div>





                                </div>
                            </div>
                            <!-- /Search -->
                        </div>
                        <div class="card-body">





                            <!-- Calls List -->
                            <div class="table-responsive custom-table">
                                <table class="table" id="activity-list-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="no-sort">
                                                <label class="checkboxs">
                                                    <input type="checkbox" id="select-all"><span class="checkmarks"></span>
                                                </label>
                                            </th>
                                            <th>Lead Status</th>
                                            <th>Lead Sub Status</th>
                                            <th>Approval</th>
                                            <th>Department</th>
                                            <th>Designation</th>
                                            <th>Created at</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>



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
                            <!-- /Calls List -->

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <!-- /Page Wrapper -->

    <!-- Global Add Lead Sub Status Modal Included in Header -->

    <!-- Edit Calls -->
    <div class="modal fade" id="edit_calls" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Lead Sub Status</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>

                <form id="EditActivityForm" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="editContactStageId" name="id">

                        <div class="mb-3">
                            <label class="col-form-label">Lead Status <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="leads_status_id" id="editLeadStatusId" style="width: 100%;">
                                @if(isset($contactStages))
                                    @foreach($contactStages as $stage)
                                        <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Lead Sub Status Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" id="editContactStageName">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="col-form-label">Requires Approval</label>
                                <select class="form-select" name="requires_approval" id="editRequiresApproval">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            </div>
                            <div class="col-md-6 mb-3" id="editApprovalFilters" style="display: none;">
                                <label class="col-form-label">Approval Department</label>
                                <select class="form-select" id="editApprovalDepartment" name="approval_department_id">
                                    <option value="">Select Department</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="editApprovalDesignationWrapper" style="display: none;">
                                <label class="col-form-label">Approval Designation</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="editApprovalDesignation_visible" placeholder="Select Designation" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                    <input type="hidden" id="editApprovalDesignation" name="approval_designation_id">
                                    <button class="btn btn-outline-secondary" type="button" onclick="if($('#editApprovalDepartment').val()) { window.openDesignationTree($('#editApprovalDepartment').val(), '#editApprovalDesignation'); } else { showToast('Please select a department first', 'error'); }"><i class="ti ti-sitemap"></i> Select</button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="col-form-label">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <input type="radio" class="status-radio" id="edit-active" name="status"
                                        value="1">
                                    <label for="edit-active">Active</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="edit-inactive" name="status"
                                        value="0">
                                    <label for="edit-inactive">Inactive</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex align-items-center justify-content-end m-0">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <div class="modal fade" id="delete_lead_status" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <input type="hidden" id="deleteLeadSubStatus">
                <div class="modal-body">
                    <div class="text-center">
                        <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                            <i class="ti ti-trash-x fs-36 text-danger"></i>
                        </div>
                        <h4 class="mb-2">Remove Call Reason?</h4>
                        <p class="mb-0">Are you sure you want to remove it?</p>
                        <div class="d-flex align-items-center justify-content-center mt-4">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="button" class="btn btn-danger" id="confirmdeleteLeadSubStatus">Yes, Delete
                                it</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <div id="dynamicToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="dynamicToastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
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

@push('scripts')
    <script>
        const canUpdate = {{ ($isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute('LeadSubStatus.update')) ? 'true' : 'false' }};
        const canDelete = {{ ($isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute('LeadSubStatys.delete')) ? 'true' : 'false' }};

        const activityTypeUrlBase = "{{ route('get.GetLeadSubStatus', ['box_slug' => request()->route('box_slug')]) }}";

    const columnDefinitions = [
        { index: 0, name: 'Checkbox', visible: true },
        { index: 1, name: 'Lead Status', visible: true },
        { index: 2, name: 'Lead Sub Status', visible: true },
        { index: 3, name: 'Approval', visible: true },
        { index: 4, name: 'Department', visible: true },
        { index: 5, name: 'Designation', visible: true },
        { index: 6, name: 'Status', visible: true },
        { index: 7, name: 'Created at', visible: true },
        { index: 8, name: 'Action', visible: true }
    ];

    // Page identifier for column preferences
    const pageIdentifier = 'lead-sub-status';

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
                console.error('Error loading column preferences:', error);
                // Fallback: show all columns
                columnDefinitions.forEach(col => col.visible = true);
                if (callback) callback();
            }
        });
    }

    // Save column preferences to database
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

    const ActivityManager = {
        sourceTable: null,

        init() {
            if (!this.validateTable()) return;
            this.bindEvents();
            
            // Manage Columns Event Listeners
            $('#manageColumnsModal').on('show.bs.modal', function() {
                populateColumnsModal();
            });

            $('#showAllColumns').on('click', function() {
                $('.column-toggle').prop('checked', true);
            });

            $('#hideAllColumns').on('click', function() {
                $('.column-toggle').prop('checked', false);
            });

            $('#applyColumnChanges').on('click', () => {
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
                if (this.sourceTable) {
                    columnDefinitions.forEach(col => {
                        try {
                            this.sourceTable.column(col.index).visible(col.visible, false);
                        } catch (e) {
                            console.error('Error applying column visibility:', e);
                        }
                    });
                    this.sourceTable.columns.adjust();
                    this.sourceTable.draw(false);
                }

                $('#manageColumnsModal').modal('hide');
            });

            loadColumnPreferences(() => {
                this.initDataTable();
            });
        },

        validateTable() {
            const tableEl = $('#activity-list-table');
            return tableEl.length > 0 && tableEl.prop('tagName') === 'TABLE';
        },

        getRouteUrl() {
            const activityId = new URLSearchParams(window.location.search).get('lead_status_id') || '';
            return `{{ route('get.GetLeadSubStatus', ['box_slug' => request()->route('box_slug')]) }}?lead_status_id=${activityId}`;
        },

        reloadTable() {
            if (this.sourceTable) {
                this.sourceTable.ajax.reload(null, false);
            }
        },

        showToast(message, type = 'success') {
            const $toast = $('#dynamicToast');
            const $message = $('#dynamicToastMessage');
            $message.text(message);
            $toast.removeClass('bg-success bg-danger bg-warning')
                .addClass(type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-warning');
            new bootstrap.Toast($toast[0]).show();
        },

        bindEvents() {
            let debounceTimer;
            $('#LeadSubStatusSearchInput').on('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => this.reloadTable(), 300);
            });

            // Dynamic Rows
            // Dynamic Rows logic
            this.addDynamicRow = function() {
                const row = `
                    <div class="d-flex align-items-center mb-2 activity-type-row">
                        <input type="text" class="form-control me-2" name="titles[]" placeholder="Enter Name">
                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-activity-row"><i class="ti ti-minus"></i></button>
                    </div>
                `;
                $('#activity-type-container').append(row);
                return $('#activity-type-container input[name="titles[]"]').last();
            };

            $(document).on('click', '.add-activity-row', () => {
                this.addDynamicRow();
            });

            // Ctrl + Enter to add new row
            $(document).on('keydown', 'input[name="titles[]"]', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    e.preventDefault();
                    const newInput = this.addDynamicRow();
                    newInput.focus();
                }
            });

            $(document).on('click', '.remove-activity-row', function() {
                $(this).closest('.activity-type-row').remove();
            });

             // Initialize Select2 on modal show
            $('#add_leadsubstatus').on('shown.bs.modal', function () {
                $(this).find('.select2').select2({
                    dropdownParent: $('#add_leadsubstatus'),
                    width: '100%'
                });
                // Pre-select if URL has parameter
                const currentStatusId = new URLSearchParams(window.location.search).get('lead_status_id');
                if(currentStatusId) {
                     const selected = $(this).find('.select2').val() || [];
                     if(!selected.includes(currentStatusId)){
                         selected.push(currentStatusId);
                         $(this).find('.select2').val(selected).trigger('change');
                     }
                }
            });

            $('#edit_calls').on('shown.bs.modal', function () {
                $(this).find('.select2').select2({
                    dropdownParent: $('#edit_calls'),
                    width: '100%'
                });
            });

            $('#Add_leadsubstatus').on('submit', this.handleAddFormSubmit.bind(this));
            $('#EditActivityForm').on('submit', this.handleEditFormSubmit.bind(this));

            $(document).on('click', '.edit_calls-btn', this.populateEditModal.bind(this));
            $(document).on('click', '.delete_reason-btn', function() {
                $('#deleteLeadSubStatus').val($(this).data('id'));
            });
            $('#confirmdeleteLeadSubStatus').on('click', this.handleDelete.bind(this));
            
            $('#editRequiresApproval').change(function() {
                if ($(this).val() == '1') {
                    $('#editApprovalFilters').slideDown();
                    $('#editApprovalDesignationWrapper').slideDown();
                } else {
                    $('#editApprovalFilters').slideUp();
                    $('#editApprovalDesignationWrapper').slideUp();
                    $('#editApprovalDepartment').val('');
                    $('#editApprovalDesignation').val('');
                    $('#editApprovalDesignationVisible').val('');
                }
            });

            $('#edit_calls').on('shown.bs.modal', function () {
                $.ajax({
                    url: "{{ route('department.manage', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 200 && response.data) {
                            const $deptSelect = $('#editApprovalDepartment');
                            const currentVal = $deptSelect.data('selected-id');
                            $deptSelect.empty().append('<option value="">Select Department</option>');
                            window.teamOptions = window.teamOptions || {};
                            response.data.forEach(dept => {
                                window.teamOptions[dept.id] = dept.all_teams || [];
                                $deptSelect.append(`<option value="${dept.id}">${dept.name} (Dept)</option>`);
                            });
                            if(currentVal) {
                                $deptSelect.val(currentVal).trigger('change');
                            }
                        }
                    }
                });
            });
        },

        initDataTable() {
            const tableEl = $('#activity-list-table');
            const urlParams = new URLSearchParams(window.location.search);
            const yourLeadStatusIdVariable = urlParams.get('lead_status_id') || 0;
            if ($.fn.DataTable.isDataTable(tableEl)) {
                tableEl.DataTable().clear().destroy();
            }
            this.sourceTable = tableEl.DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: this.getRouteUrl(),
                    type: 'GET',
                    data: function(d) {
                        d.lead_status_id = yourLeadStatusIdVariable;
                        d.search.value = $('#LeadSubStatusSearchInput').val();
                    }
                },
                order: [[2, 'desc']],
                columns: [
                    {
                        data: null,
                        orderable: false,
                        visible: columnDefinitions[0].visible,
                        render: () => '<input type="checkbox" class="select-row">',
                    },
                    {
                        data: 'parent_status',
                        visible: columnDefinitions[1].visible,
                        render: data => `<span class="title-name">${data || '-'}</span>`
                    },
                    {
                        data: 'name',
                        visible: columnDefinitions[2].visible,
                        render: data => `<span class="title-name">${data}</span>`
                    },
                    {
                        data: 'requires_approval_badge',
                        visible: columnDefinitions[3].visible
                    },
                    {
                        data: 'department_name',
                        visible: columnDefinitions[4].visible
                    },
                    {
                        data: 'designation_name',
                        visible: columnDefinitions[5].visible
                    },
                    {
                        data: 'status',
                        visible: columnDefinitions[6].visible,
                        render: data => {
                            return data == 1
                                ? '<span class="badge badge-pill bg-success">Active</span>'
                                : '<span class="badge badge-pill bg-danger">Inactive</span>';
                        }
                    },
                    {
                        data: 'created_at',
                        visible: columnDefinitions[7].visible,
                        render: data => {
                            const d = new Date(data);
                            const options = { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
                            return d.toLocaleDateString('en-US', options);
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        visible: columnDefinitions[8].visible,
                        render: (data, type, row) => {
                            const editBtn = canUpdate ? `
                                <a href="#" class="edit_calls-btn" data-id="${row.id}" data-title="${row.name}" data-status="${row.status}" data-leads_status_id="${row.leads_status_id}" data-requires_approval="${row.requires_approval || 0}" data-approval_department_id="${row.approval_department_id || ''}" data-approval_designation_id="${row.approval_designation_id || ''}" data-bs-toggle="modal" data-bs-target="#edit_calls">
                                    <i class="ti ti-edit text-blue"></i> Edit
                                </a>` : '';
                            const deleteBtn = canDelete ? `
                                <a href="#" class="delete_reason-btn" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#delete_lead_status">
                                    <i class="ti ti-trash text-danger"></i> Delete
                                </a>` : '';
                            
                            if (!editBtn && !deleteBtn) return '';

                            return `
                                <div class="dropdown table-action">
                                    <a href="#" class="action-icon" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        ${editBtn}
                                        ${deleteBtn}
                                    </div>
                                </div>`;
                        }
                    }
                ],
                bFilter: false,
                bInfo: false,
                language: {
                    search: " ",
                    searchPlaceholder: "Search",
                    paginate: {
                        next: 'Next <i class="fa fa-angle-right"></i>',
                        previous: '<i class="fa fa-angle-left"></i> Prev'
                    }
                },
                initComplete: function() {
                    $(".dataTables_paginate").appendTo(".datatable-paginate");
                    $(".dataTables_length").appendTo(".datatable-length");
                }
            });

            let debounceTimer;
            $('#LeadSubStatusSearchInput').on('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    this.sourceTable.ajax.reload();
                }, 300);
            });
        },

        handleAddFormSubmit(e) {
            e.preventDefault();
            const $form = $(e.currentTarget);
            const url = `{{ route('Leadsubstatus.store', ['box_slug' => request()->route('box_slug')]) }}`;

            const titles = [];
            $form.find('input[name="titles[]"]').each(function() {
                if($(this).val().trim() !== '') {
                    titles.push($(this).val().trim());
                }
            });

            const leads_status_ids = $form.find('select[name="leads_status_ids[]"]').val();

            if (titles.length === 0) {
                this.showToast('Please enter at least one Activity Type name.', 'error');
                return;
            }
             if (!leads_status_ids || leads_status_ids.length === 0) {
                this.showToast('Please select at least one Lead Status.', 'error');
                return;
            }

            const data = {
                titles: titles,
                leads_status_ids: leads_status_ids,
                status: $form.find('input[name="status"]:checked').val(),
                _token: $('meta[name="csrf-token"]').attr("content")
            };

            $.post(url, data).done(res => {
                if (res.success) {
                    $('#add_leadsubstatus').modal('hide');
                    $form[0].reset();
                    $form.find('.select2').val(null).trigger('change');
                     // Reset dynamic rows
                     $('#activity-type-container').html(`
                        <div class="d-flex align-items-center mb-2 activity-type-row">
                            <input type="text" class="form-control me-2" name="titles[]" placeholder="Enter Name">
                            <button type="button" class="btn btn-sm btn-icon btn-outline-primary add-activity-row"><i class="ti ti-plus"></i></button>
                        </div>
                     `);
                    this.showToast(res.message || 'Activities added!');
                    this.reloadTable();
                }
            }).fail(xhr => {
                const res = xhr.responseJSON;
                const errorMsg = (xhr.status === 409) ? res.message : ((xhr.status === 422 && res?.errors) ?
                    Object.values(res.errors).flat().join(' ') :
                    res?.message || 'Add failed.');
                this.showToast(errorMsg, 'error');
            });
        },

        populateEditModal(e) {
            const btn = $(e.currentTarget);
            $('#editContactStageId').val(btn.data('id'));
            $('#editContactStageName').val(btn.data('title'));
            $('#editLeadStatusId').val(btn.attr('data-leads_status_id')).trigger('change');
            $(`#edit_calls input[name="status"][value="${btn.data('status')}"]`).prop('checked', true);
            
            $('#editRequiresApproval').val(btn.data('requires_approval') ? 1 : 0).trigger('change');
        
            const approvalDepartmentId = btn.data('approval_department_id');
            const approvalDesignationId = btn.data('approval_designation_id');
            
            if(approvalDepartmentId) {
                $('#editApprovalDepartment').data('selected-id', approvalDepartmentId);
                $('#editApprovalDepartment').val(approvalDepartmentId).trigger('change');
                $('#editApprovalDesignation').val(approvalDesignationId);
                $('#editApprovalDesignation_visible').val(approvalDesignationId ? 'Designation Selected' : '');
            } else {
                $('#editApprovalDepartment').data('selected-id', '');
                $('#editApprovalDepartment').val('').trigger('change');
                $('#editApprovalDesignation').val('');
                $('#editApprovalDesignation_visible').val('');
            }
        },

        handleEditFormSubmit(e) {
            e.preventDefault();
            const id = $('#editContactStageId').val();
            const title = $('#editContactStageName').val().trim();
            const leads_status_id = $('#editLeadStatusId').val();
            const status = $('#edit_calls input[name="status"]:checked').val();
            const requires_approval = $('#editRequiresApproval').val();
            const approval_department_id = $('#editApprovalDepartment').val();
            const approval_designation_id = $('#editApprovalDesignation').val();

            $.ajax({
                url: "{{ route('LeadSubStatus.update', ['box_slug' => request()->route('box_slug')]) }}",
                type: "POST",
                data: {
                    id,
                    title,
                    status,
                    leads_status_id,
                    requires_approval,
                    approval_department_id,
                    approval_designation_id,
                    _token: $('meta[name="csrf-token"]').attr("content")
                }
            }).done(res => {
                $('#edit_calls').modal('hide');
                this.showToast(res.message || 'Updated.', res.success ? 'success' : 'error');
                if (res.success) this.reloadTable();
            }).fail(() => {
                this.showToast('Update failed.', 'error');
            });
        },

        handleDelete() {
            const id = $('#deleteLeadSubStatus').val();
            if (!id) return this.showToast('Invalid ID.', 'error');

            $.ajax({
                url: `{{ route('LeadSubStatys.delete', ['box_slug' => request()->route('box_slug')]) }}`,
                method: 'DELETE',
                data: {
                    id,
                    _token: $('meta[name="csrf-token"]').attr("content")
                },
                success: (res) => {
                    $('#delete_lead_status').modal('hide');
                    this.showToast(res.message || 'Deleted.', res.success ? 'success' : 'error');
                    if (res.success) this.reloadTable();
                },
                error: () => {
                    this.showToast('Delete failed.', 'error');
                }
            });
        }
    };

    $(document).ready(() => {
        ActivityManager.init();
    });
        </script>
    @endpush
@endsection
