@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@section('title', 'Divisions')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">Settings</h4>
                        </div>
                        <div class="col-4 text-end">
                            <div class="head-icons">
                                <a href="javascript:void(0);" onclick="reloadTable();" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-original-title="Refresh"><i
                                        class="ti ti-refresh-dot"></i></a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Collapse" id="collapse-header"><i
                                        class="ti ti-chevrons-up"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                @include('settings.partials.nav')

                <div class="row">
                    <div class="col-xl-3 col-lg-12 theiaStickySidebar">
                        @include('settings.partials.sidebar')
                    </div>

                    <div class="col-xl-9 col-lg-12">

                        <!-- Table Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <h4 class="fs-20 fw-semibold">Divisions</h4>
                                    <a href="javascript:void(0)" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal"
                                        data-bs-target="#add_division">
                                        <i class="ti ti-square-rounded-plus me-2 fs-18"></i>Add Division
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="divisions_table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="no-sort">
                                                    <label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
                                                </th>
                                                <th>Sr. No.</th>
                                                <th>Division Name</th>
                                                <th>Company</th>
                                                <th>Auto Map</th>
                                                <th>Limit Member</th>
                                                <th>Max Members</th>
                                                <th>Status</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /Table Card -->

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<!-- Add Division Modal -->
<div class="modal fade" id="add_division" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold">Add Division</h5>
                <button type="button" class="btn-close custom-btn-close border p-1" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="addDivisionForm" onsubmit="return false;">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-medium">Select Company <span class="text-danger">*</span></label>
                            <select name="company_id" class="form-select select" required>
                                <option value="">-- Select Company --</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->bill_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Division Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Sales Division" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Auto Map Location</label>
                            <select name="auto_map_location" class="form-select select">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Limit Members</label>
                            <select name="limit_member" class="form-select select">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Max Members</label>
                            <input type="number" name="max_members" class="form-control" placeholder="0" min="0">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-medium">Status</label>
                            <select name="status" class="form-select select">
                                <option value="Enable">Enable</option>
                                <option value="Disable">Disable</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top p-3">
                    <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="saveAddDivisionBtn" class="btn btn-primary fw-medium px-4">Save Division</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Division Modal -->

<!-- Edit Division Modal -->
<div class="modal fade" id="edit_division" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold">Edit Division</h5>
                <button type="button" class="btn-close custom-btn-close border p-1" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="editDivisionForm" onsubmit="return false;">
                @csrf
                <input type="hidden" name="id" id="edit_division_id">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-medium">Select Company <span class="text-danger">*</span></label>
                            <select name="company_id" id="edit_company_id" class="form-select select" required>
                                <option value="">-- Select Company --</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->bill_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Division Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" placeholder="e.g. Sales Division" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Auto Map Location</label>
                            <select name="auto_map_location" id="edit_auto_map_location" class="form-select select">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Limit Members</label>
                            <select name="limit_member" id="edit_limit_member" class="form-select select">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Max Members</label>
                            <input type="number" name="max_members" id="edit_max_members" class="form-control" placeholder="0" min="0">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-medium">Status</label>
                            <select name="status" id="edit_status" class="form-select select">
                                <option value="Enable">Enable</option>
                                <option value="Disable">Disable</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top p-3">
                    <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="saveEditDivisionBtn" class="btn btn-primary fw-medium px-4">Update Division</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Edit Division Modal -->

<!-- Delete Division Modal -->
<div class="modal fade" id="delete_division_modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-body p-5">
                <div class="text-center">
                    <div class="avatar avatar-xxl bg-danger-light rounded-circle mb-4 mx-auto d-flex align-items-center justify-content-center">
                        <i class="ti ti-trash-x fs-48 text-danger"></i>
                    </div>
                    <h3 class="mb-3 fw-bold">Remove Division?</h3>
                    <p class="text-muted mb-4 fs-16">Are you sure you want to remove this division? This action cannot be undone.</p>
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <button type="button" class="btn btn-light btn-lg fw-medium px-5" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmDeleteDivision" class="btn btn-danger btn-lg fw-medium px-5">Yes, Delete It</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Division Modal -->

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    let divisionsTable;

    function reloadTable() {
        if (divisionsTable) {
            divisionsTable.ajax.reload(null, false);
        }
    }

    $(document).ready(function() {
        const routes = {
            index: "{{ box_route('financial.settings.divisions') }}",
            store: "{{ box_route('financial.settings.divisions.store') }}",
            update: "{{ box_route('financial.settings.divisions.update') }}",
            destroy: "{{ box_route('financial.settings.divisions.destroy') }}"
        };

        // Initialize DataTable
        divisionsTable = $('#divisions_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: routes.index,
            columns: [
                { data: 'id', name: 'id', orderable: false, searchable: false, render: function(data, type, row) {
                    return '<label class="checkboxs"><input type="checkbox" value="' + data + '"><span class="checkmarks"></span></label>';
                }},
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'company_name', name: 'company_name' },
                { 
                    data: 'auto_map_location', 
                    name: 'auto_map_location',
                    render: function(data) { return data == 1 ? 'Yes' : 'No'; }
                },
                { 
                    data: 'limit_member', 
                    name: 'limit_member',
                    render: function(data) { return data == 1 ? 'Yes' : 'No'; }
                 },
                { data: 'max_members', name: 'max_members' },
                { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
            ],
            language: {
                search: "",
                searchPlaceholder: "Search Divisions...",
                lengthMenu: "_MENU_",
                paginate: {
                    next: '<i class="ti ti-chevron-right"></i>',
                    previous: '<i class="ti ti-chevron-left"></i>'
                },
                processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
            },
            pageLength: 10,
            order: [[0, 'asc']],
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm mb-0 justify-content-end');
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });

        // Select All Checkbox
        $('#select-all').on('click', function() {
            const isChecked = $(this).prop('checked');
            $('#divisions_table tbody input[type="checkbox"]').prop('checked', isChecked);
        });

        // Search Custom Styling
        $('.dataTables_filter input').addClass('form-control form-control-sm border-20 p-2 px-3').css('width', '250px');

        // Reset Add Modal on Open
        $('#add_division').on('show.bs.modal', function() {
            $('#addDivisionForm')[0].reset();
            $(this).find('select').each(function() {
                 $(this).val($(this).find('option:first').val()).trigger('change');
            });
            // Defaults
            $(this).find('select[name="status"]').val('Enable').trigger('change');
            $(this).find('select[name="auto_map_location"]').val('0').trigger('change');
            $(this).find('select[name="limit_member"]').val('0').trigger('change');
        });

        // Trigger Edit Modal via Delegation
        $(document).on('click', '.edit-division-btn', function() {
            const btn = $(this);
            // Populate Edit Form via .attr()
            $('#edit_division_id').val(btn.attr('data-id'));
            $('#edit_company_id').val(btn.attr('data-company-id')).trigger('change');
            $('#edit_name').val(btn.attr('data-name'));
            $('#edit_auto_map_location').val(btn.attr('data-auto-map-location')).trigger('change');
            $('#edit_limit_member').val(btn.attr('data-limit-member')).trigger('change');
            $('#edit_max_members').val(btn.attr('data-max-members'));
            $('#edit_status').val(btn.attr('data-status')).trigger('change');

            $('#edit_division').modal('show');
        });

        // Handle ADD Form Submission
        $('#addDivisionForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#saveAddDivisionBtn');
            const formData = $(this).serialize();

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

            $.ajax({
                url: routes.store,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        $('#add_division').modal('hide');
                        reloadTable();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    const res = xhr.responseJSON;
                    if (res && res.errors) {
                        Object.values(res.errors).forEach(err => toastr.error(err[0]));
                    } else {
                        toastr.error('Error: ' + (res ? res.message : 'Unknown error occurred.'));
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).text('Save Division');
                }
            });
        });

        // Handle EDIT Form Submission
        $('#editDivisionForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#saveEditDivisionBtn');
            const formData = $(this).serialize();

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

            $.ajax({
                url: routes.update,
                type: 'PUT',
                data: formData,
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        $('#edit_division').modal('hide');
                        reloadTable();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    const res = xhr.responseJSON;
                    if (res && res.errors) {
                        Object.values(res.errors).forEach(err => toastr.error(err[0]));
                    } else {
                        toastr.error('Error: ' + (res ? res.message : 'Unknown error occurred.'));
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).text('Update Division');
                }
            });
        });

        // Delete Logic
        let deleteId = null;
        $(document).on('click', '.delete-division-btn', function() {
            deleteId = $(this).data('id');
            $('#delete_division_modal').modal('show');
        });

        $('#confirmDeleteDivision').on('click', function() {
            if (!deleteId) return;
            const btn = $(this);

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Deleting...');

            $.ajax({
                url: routes.destroy,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: deleteId
                },
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        $('#delete_division_modal').modal('hide');
                        reloadTable();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Failed to delete division.');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Yes, Delete It');
                }
            });
        });
    });
</script>
@endpush
@endsection
