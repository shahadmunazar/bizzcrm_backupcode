@extends('layouts.main')
@section('title', 'Product HSNs')
@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Product HSNs</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ box_route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Product HSNs</li>
                    </ul>
                </div>
                <div class="col-auto float-end ms-auto">
                    <button type="button" class="btn btn-danger me-2 d-none" id="bulkDeleteBtn">
                        <i class="fa fa-trash"></i> Bulk Delete
                    </button>
                    <a href="javascript:void(0);" class="btn add-btn me-2" data-bs-toggle="modal" data-bs-target="#manageColumnsModal"><i class="fa fa-columns me-1"></i> Manage Columns</a>
                    <button type="button" class="btn add-btn me-2" data-bs-toggle="modal" data-bs-target="#importHsnModal">
                        <i class="fa fa-upload"></i> Import HSNs
                    </button>
                    <button type="button" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#createHsnModal">
                        <i class="fa fa-plus"></i> Add HSN
                    </button>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('import_errors') && is_array(session('import_errors')))
            <div class="alert alert-danger">
                <strong>Import Errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach(session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="hsnTable" class="table table-striped custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAllHsn">
                                        </th>
                                        <th>ID</th>
                                        <th>Code</th>
                                        <th>Short Name</th>
                                        <th>SGST %</th>
                                        <th>CGST %</th>
                                        <th>IGST %</th>
                                        <th>Type</th>
                                        <th>UQC</th>
                                        <th>CESS %</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th>Updated By</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Deleted By</th>
                                        <th>Deleted At</th>
                                        <th class="text-end">Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<!-- Include Generic Add HSN Modal -->
@include('partials.add_hsn_modal')

<!-- Edit HSN Modal -->
<div id="editHsnModal" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit HSN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editHsnForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_hsn_id">
                    <input type="hidden" name="company_id" value="{{ request('company_id') }}">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>HSN/SAC (Code) <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="code" id="edit_hsn_code">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Short Name</label>
                            <input class="form-control" type="text" name="short_name" id="edit_hsn_short_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>SGST % <span class="text-danger">*</span></label>
                            <input class="form-control" type="number" step="0.01" name="sgst" id="edit_hsn_sgst" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>CGST % <span class="text-danger">*</span></label>
                            <input class="form-control" type="number" step="0.01" name="cgst" id="edit_hsn_cgst" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>IGST % <span class="text-danger">*</span></label>
                            <input class="form-control" type="number" step="0.01" name="igst" id="edit_hsn_igst" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>TYPE <span class="text-danger">*</span></label>
                            <select class="form-control" name="type" id="edit_hsn_type" required>
                                <option value="Goods">Goods</option>
                                <option value="Services">Services</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>UQC (Unit)</label>
                            <input class="form-control" type="text" name="uqc" id="edit_hsn_uqc">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>CESS % <span class="text-danger">*</span></label>
                            <input class="form-control" type="number" step="0.01" name="cess" id="edit_hsn_cess" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Effective Date From</label>
                            <input class="form-control" type="date" name="from_date" id="edit_hsn_from_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Effective Date To</label>
                            <input class="form-control" type="date" name="to_date" id="edit_hsn_to_date">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" id="edit_hsn_status" required>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit HSN Modal -->

<!-- Import Progress Modal -->
<div id="importProgressModal" class="modal custom-modal fade" role="dialog" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importProgressModalTitle"><i class="fa fa-spinner fa-spin me-2"></i> Bulk Import in Progress...</h5>
                <button type="button" class="btn-close d-none" id="importProgressCloseBtn" data-bs-dismiss="modal" aria-label="Close" onclick="table.ajax.reload(null, false);">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3 text-center">
                    <div class="col-3">
                        <h6 class="text-muted">Total Records</h6>
                        <h4 id="importTotalCount" class="text-primary">Calculating...</h4>
                    </div>
                    <div class="col-3">
                        <h6 class="text-muted">Inserted</h6>
                        <h4 id="importSuccessCount" class="text-success">0</h4>
                    </div>
                    <div class="col-3">
                        <h6 class="text-muted">Skipped</h6>
                        <h4 id="importSkippedCount" class="text-warning">0</h4>
                    </div>
                    <div class="col-3">
                        <h6 class="text-muted">Failed</h6>
                        <h4 id="importFailedCount" class="text-danger">0</h4>
                    </div>
                </div>
                
                <div class="progress mb-3" style="height: 25px;">
                    <div id="importProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%">0%</div>
                </div>

                <div id="importErrorsList" class="d-none mt-3">
                    <strong class="text-danger">Errors encountered:</strong>
                    <div class="border p-2 mt-1 bg-light" style="max-height: 200px; overflow-y: auto;">
                        <ul class="mb-0 text-danger small" id="importErrorsUl"></ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-none" id="importProgressFooter">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="table.ajax.reload(null, false);">Close & Refresh</button>
            </div>
        </div>
    </div>
</div>
<!-- /Import Progress Modal -->

<!-- Include Generic Audit History Modal -->
@include('partials.audit_history_modal')

<!-- Delete HSN Modal -->
<div class="modal fade" id="deleteHsnModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1">Delete HSN</h4>
                <p class="mb-3">
                    Are you sure you want to delete this HSN? This action <strong>cannot</strong> be undone.
                </p>
                <div class="d-flex justify-content-center">
                    <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                    <a href="javascript:void(0);" class="btn btn-danger" id="confirmDeleteHsnBtn">Yes, Delete</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete HSN Modal -->

<!-- Bulk Delete HSN Modal -->
<div class="modal fade" id="bulkDeleteHsnModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1">Bulk Delete HSNs</h4>
                <p class="mb-3">
                    Are you sure you want to delete <strong id="bulkDeleteCount">0</strong> HSN(s)? This action <strong>cannot</strong> be undone.
                </p>
                <div class="d-flex justify-content-center">
                    <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                    <a href="javascript:void(0);" class="btn btn-danger" id="confirmBulkDeleteHsnBtn">Yes, Delete them!</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Bulk Delete HSN Modal -->

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
<!-- /Manage Columns Modal -->

@endsection

@push('scripts')
<script>
    let columnDefinitions = [
        { index: 0, name: 'Checkbox', visible: true, disableToggle: true },
        { index: 1, name: 'ID', visible: true },
        { index: 2, name: 'Code', visible: true },
        { index: 3, name: 'Short Name', visible: true },
        { index: 4, name: 'SGST %', visible: true },
        { index: 5, name: 'CGST %', visible: true },
        { index: 6, name: 'IGST %', visible: true },
        { index: 7, name: 'Type', visible: true },
        { index: 8, name: 'UQC', visible: true },
        { index: 9, name: 'CESS %', visible: true },
        { index: 10, name: 'From Date', visible: true },
        { index: 11, name: 'To Date', visible: true },
        { index: 12, name: 'Status', visible: true },
        { index: 13, name: 'Created By', visible: true },
        { index: 14, name: 'Updated By', visible: true },
        { index: 15, name: 'Created At', visible: true },
        { index: 16, name: 'Updated At', visible: true },
        { index: 17, name: 'Deleted By', visible: true },
        { index: 18, name: 'Deleted At', visible: true },
        { index: 19, name: 'Manage (Action)', visible: true }
    ];

    const pageIdentifier = 'product-hsns';

    function loadColumnPreferences(callback) {
        $.ajax({
            url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug")]) }}',
            type: 'GET',
            data: { page_identifier: pageIdentifier },
            async: false,
            success: function(response) {
                if (response.success && response.data && Array.isArray(response.data) && response.data.length > 0) {
                    try {
                        const preferences = response.data;
                        preferences.forEach(pref => {
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
                        if (callback) callback();
                    } catch (e) {
                        console.error('Error loading column preferences:', e);
                        columnDefinitions.forEach(col => col.visible = true);
                        if (callback) callback();
                    }
                } else {
                    columnDefinitions.forEach(col => col.visible = true);
                    if (callback) callback();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading column preferences:', error);
                columnDefinitions.forEach(col => col.visible = true);
                if (callback) callback();
            }
        });
    }

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

    let table;

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

$(document).ready(function() {
    loadColumnPreferences(function() {
        table = $('#hsnTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ box_route('accounts.product_hsns.index', ['company_id' => $company_id]) }}",
            columns: [
                {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, visible: columnDefinitions[0].visible},
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, visible: columnDefinitions[1].visible},
                {data: 'code', name: 'code', visible: columnDefinitions[2].visible},
                {data: 'short_name', name: 'short_name', visible: columnDefinitions[3].visible},
                {data: 'sgst', name: 'sgst', visible: columnDefinitions[4].visible},
                {data: 'cgst', name: 'cgst', visible: columnDefinitions[5].visible},
                {data: 'igst', name: 'igst', visible: columnDefinitions[6].visible},
                {data: 'type', name: 'type', visible: columnDefinitions[7].visible},
                {data: 'uqc', name: 'uqc', visible: columnDefinitions[8].visible},
                {data: 'cess', name: 'cess', visible: columnDefinitions[9].visible},
                {data: 'from_date', name: 'from_date', visible: columnDefinitions[10].visible},
                {data: 'to_date', name: 'to_date', visible: columnDefinitions[11].visible},
                {data: 'status', name: 'status', visible: columnDefinitions[12].visible},
                {data: 'created_by_name', name: 'created_by_name', visible: columnDefinitions[13].visible},
                {data: 'updated_by_name', name: 'updated_by_name', visible: columnDefinitions[14].visible},
                {data: 'created_at', name: 'created_at', visible: columnDefinitions[15].visible},
                {data: 'updated_at', name: 'updated_at', visible: columnDefinitions[16].visible},
                {data: 'deleted_by_name', name: 'deleted_by_name', visible: columnDefinitions[17].visible},
                {data: 'deleted_at', name: 'deleted_at', visible: columnDefinitions[18].visible},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end', visible: columnDefinitions[19].visible},
            ]
        });
        
        // Expose table globally for the add_hsn_modal generic partial
        window.hsnTable = table;
    });

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
            const columnIndex = parseInt($(this).data('column-index'));
            const isVisible = $(this).is(':checked');
            const col = columnDefinitions.find(c => c.index === columnIndex);
            if (col) {
                col.visible = isVisible;
            }
        });

        saveColumnPreferences();

        if (table) {
            columnDefinitions.forEach(col => {
                try {
                    table.column(col.index).visible(col.visible, false);
                } catch (e) {
                    console.error('Error applying column visibility:', e);
                }
            });
            table.columns.adjust();
            table.draw(false);
        }

        $('#manageColumnsModal').modal('hide');
    });

    // Populate Edit Modal
    $(document).on('click', '.edit-hsn-btn', function() {
        $('#edit_hsn_id').val($(this).data('id'));
        $('#edit_hsn_code').val($(this).data('code'));
        $('#edit_hsn_short_name').val($(this).data('short_name'));
        $('#edit_hsn_sgst').val($(this).data('sgst'));
        $('#edit_hsn_cgst').val($(this).data('cgst'));
        $('#edit_hsn_igst').val($(this).data('igst'));
        $('#edit_hsn_type').val($(this).data('type'));
        $('#edit_hsn_uqc').val($(this).data('uqc'));
        $('#edit_hsn_cess').val($(this).data('cess'));
        $('#edit_hsn_from_date').val($(this).data('from_date'));
        $('#edit_hsn_to_date').val($(this).data('to_date'));
        $('#edit_hsn_status').val($(this).data('status'));
    });

    // Update HSN
    $('#editHsnForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#edit_hsn_id').val();
        var formData = $(this).serialize();
        var url = "{{ box_route('accounts.product_hsns.update', ':id') }}";
        url = url.replace(':id', id);

        $('#editHsnForm .invalid-feedback').remove();
        $('#editHsnForm .is-invalid').removeClass('is-invalid');
        $.ajax({
            url: url,
            type: "POST", // using PUT method in data
            data: formData,
            success: function(response) {
                if(response.success) {
                    $('#editHsnModal').modal('hide');
                    showToast(response.message || 'HSN updated successfully.', 'success');
                    table.ajax.reload(null, false);
                } else {
                    showToast('Error updating HSN', 'error');
                }
            },
            error: function(xhr) {
                if(xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        var input = $('#editHsnForm [name="' + key + '"]');
                        if (input.length) {
                            input.addClass('is-invalid');
                            input.after('<div class="invalid-feedback d-block">' + value[0] + '</div>');
                        } else {
                            showToast(value[0], 'error');
                        }
                    });
                } else {
                    showToast('Something went wrong', 'error');
                }
            }
        });
    });

    // Auto calculate GST fields for edit form
    function calculateEditIgst() {
        var sgst = parseFloat($('#editHsnForm input[name="sgst"]').val()) || 0;
        var cgst = parseFloat($('#editHsnForm input[name="cgst"]').val()) || 0;
        $('#editHsnForm input[name="igst"]').val((sgst + cgst).toFixed(2));
    }

    $('#editHsnForm input[name="sgst"], #editHsnForm input[name="cgst"]').on('input', calculateEditIgst);

    var deleteHsnId = null;

    // Delete HSN Click
    $(document).on('click', '.delete-hsn-btn', function(e) {
        e.preventDefault();
        deleteHsnId = $(this).data('id');
        $('#deleteHsnModal').modal('show');
    });

    // Confirm Delete
    $('#confirmDeleteHsnBtn').on('click', function() {
        if(deleteHsnId) {
            var url = "{{ box_route('accounts.product_hsns.destroy', ':id') }}";
            url = url.replace(':id', deleteHsnId);

            $.ajax({
                url: url,
                type: "DELETE",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#deleteHsnModal').modal('hide');
                    if(response.success) {
                        showToast(response.message || 'HSN deleted successfully.', 'success');
                        table.ajax.reload(null, false);
                        deleteHsnId = null;
                    } else {
                        showToast(response.message || 'Error deleting HSN', 'error');
                    }
                },
                error: function(xhr) {
                    $('#deleteHsnModal').modal('hide');
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong';
                    showToast(msg, 'error');
                }
            });
        }
    });

    // Handle Select All checkbox
    $('#selectAllHsn').on('click', function() {
        var isChecked = $(this).prop('checked');
        $('.hsn-checkbox').prop('checked', isChecked);
        toggleBulkDeleteBtn();
    });

    // Handle individual checkbox change
    $(document).on('change', '.hsn-checkbox', function() {
        if ($('.hsn-checkbox:checked').length === $('.hsn-checkbox').length) {
            $('#selectAllHsn').prop('checked', true);
        } else {
            $('#selectAllHsn').prop('checked', false);
        }
        toggleBulkDeleteBtn();
    });

    // Show/hide bulk delete button
    function toggleBulkDeleteBtn() {
        if ($('.hsn-checkbox:checked').length > 0) {
            $('#bulkDeleteBtn').removeClass('d-none');
        } else {
            $('#bulkDeleteBtn').addClass('d-none');
        }
    }

    var bulkDeleteIds = [];

    // Handle Bulk Delete click
    $('#bulkDeleteBtn').on('click', function() {
        bulkDeleteIds = [];
        $('.hsn-checkbox:checked').each(function() {
            bulkDeleteIds.push($(this).val());
        });

        if (bulkDeleteIds.length > 0) {
            $('#bulkDeleteCount').text(bulkDeleteIds.length);
            $('#bulkDeleteHsnModal').modal('show');
        }
    });

    // Confirm Bulk Delete click
    $('#confirmBulkDeleteHsnBtn').on('click', function() {
        if (bulkDeleteIds.length > 0) {
            $.ajax({
                url: "{{ box_route('accounts.product_hsns.bulk_destroy') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: bulkDeleteIds
                },
                success: function(response) {
                    $('#bulkDeleteHsnModal').modal('hide');
                    if (response.success) {
                        showToast(response.message, 'success');
                        table.ajax.reload(null, false);
                        $('#selectAllHsn').prop('checked', false);
                        toggleBulkDeleteBtn();
                        bulkDeleteIds = [];
                    } else {
                        showToast(response.message, 'error');
                    }
                },
                error: function(xhr) {
                    $('#bulkDeleteHsnModal').modal('hide');
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong';
                    showToast(msg, 'error');
                    table.ajax.reload(null, false);
                    $('#selectAllHsn').prop('checked', false);
                    toggleBulkDeleteBtn();
                    bulkDeleteIds = [];
                }
            });
        }
    });

    // Uncheck select all on table redraw
    table.on('draw', function() {
        $('#selectAllHsn').prop('checked', false);
        toggleBulkDeleteBtn();
    });

    // Bulk Import Polling
    let importPollingInterval = null;

    function checkImportStatus() {
        $.ajax({
            url: "{{ box_route('accounts.imports.status', 'ProductHsn') }}",
            type: "GET",
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;

                    let total = data.total_rows || 0;
                    let processed = data.processed_rows || 0;
                    let success = data.success_rows || 0;
                    let skipped = data.skipped_rows || 0;
                    let failed = data.failed_rows || 0;

                    let percent = 0;
                    if (total > 0) {
                        percent = Math.min(100, Math.round((processed / total) * 100));
                    }

                    $('#importTotalCount').text(total > 0 ? total : 'Calculating...');
                    $('#importSuccessCount').text(success);
                    $('#importSkippedCount').text(skipped);
                    $('#importFailedCount').text(failed);
                    
                    $('#importProgressBar').css('width', percent + '%').text(percent + '%');

                    if (data.status === 'completed' || data.status === 'failed') {
                        clearInterval(importPollingInterval);
                        
                        $('#importProgressBar').removeClass('progress-bar-animated progress-bar-striped');
                        
                        if (data.status === 'completed') {
                            $('#importProgressModalTitle').html('<i class="fa fa-check-circle me-2 text-success"></i> Import Completed Successfully');
                            $('#importProgressBar').removeClass('bg-primary').addClass('bg-success');
                        } else {
                            $('#importProgressModalTitle').html('<i class="fa fa-times-circle me-2 text-danger"></i> Import Failed');
                            $('#importProgressBar').removeClass('bg-primary').addClass('bg-danger');
                        }

                        if (data.errors && data.errors.length > 0) {
                            $('#importErrorsList').removeClass('d-none');
                            $('#importErrorsUl').empty();
                            data.errors.forEach(function(err) {
                                $('#importErrorsUl').append(`<li>${err}</li>`);
                            });
                        }
                        
                        $('#importProgressCloseBtn').removeClass('d-none');
                        $('#importProgressFooter').removeClass('d-none');
                    }
                }
            }
        });
    }

    // Handle AJAX import submission
    $('#importHsnForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var submitBtn = $(this).find('.submit-btn');
        var originalText = submitBtn.text();
        
        submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Uploading...').prop('disabled', true);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                submitBtn.html(originalText).prop('disabled', false);
                $('#importHsnModal').modal('hide');
                
                if (response.success) {
                    // Reset modal state
                    $('#importProgressModalTitle').html('<i class="fa fa-spinner fa-spin me-2"></i> Bulk Import in Progress...');
                    $('#importTotalCount').text('Calculating...');
                    $('#importSuccessCount').text('0');
                    $('#importSkippedCount').text('0');
                    $('#importFailedCount').text('0');
                    $('#importProgressBar').css('width', '0%').text('0%').removeClass('bg-success bg-danger').addClass('bg-primary progress-bar-animated progress-bar-striped');
                    $('#importErrorsList').addClass('d-none');
                    $('#importProgressCloseBtn').addClass('d-none');
                    $('#importProgressFooter').addClass('d-none');

                    // Show Progress Modal
                    $('#importProgressModal').modal('show');
                    
                    if (!importPollingInterval) {
                        importPollingInterval = setInterval(checkImportStatus, 3000);
                        checkImportStatus();
                    }
                    
                    $('#importHsnForm')[0].reset();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                submitBtn.html(originalText).prop('disabled', false);
                $('#importHsnModal').modal('hide');
                
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong';
                showToast(msg, 'error');
            }
        });
    });

});
</script>

<!-- Import HSN Modal -->
<div id="importHsnModal" class="modal custom-modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Import HSNs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="importHsnForm" action="{{ box_route('accounts.product_hsns.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label>Upload Excel/CSV File <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" name="file" accept=".csv, .xlsx, .xls" required>
                        <small class="text-muted mt-1 d-block">Required headers: code, sgst, cgst, igst. Optional headers: short_name, cess, type, uqc, effective_date_from, effective_date_to.</small>
                        <div class="mt-2">
                            <a href="{{ box_route('accounts.product_hsns.download_template') }}" class="text-primary"><i class="fa-solid fa-download me-1"></i> Download Sample Template</a>
                        </div>
                    </div>
                    <div class="submit-section mt-4 text-end">
                        <button type="submit" class="btn btn-primary submit-btn">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endpush
