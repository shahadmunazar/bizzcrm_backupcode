@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@section('title', 'Payment Modes')
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
                                <div class="d-flex align-items-center justify-content-between mb-4 remove-flex">
                                    <h4 class="fs-20 fw-semibold mb-3">Payment Modes</h4>
                                    @php
                                        $isPrivilegedUser = \App\Helpers\PermissionHelper::isPrivilegedUser();
                                        $canCreate = $isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute('financial.settings.payment_modes.store');
                                        $canUpdate = $isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute('financial.settings.payment_modes.update');
                                        $canDelete = $isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute('financial.settings.payment_modes.destroy');
                                    @endphp
                                    @if ($canCreate)
                                        <a href="javascript:void(0)" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal"
                                            data-bs-target="#add_payment_mode">
                                            <i class="ti ti-square-rounded-plus me-2 fs-18"></i>Add Payment Mode
                                        </a>
                                    @endif
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="payment_modes_table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="no-sort">
                                                    <label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
                                                </th>
                                                <th>Sr. No.</th>
                                                <th>Name</th>
                                                <th>Status</th>
                                                <th>Added By</th>
                                                <th>Updated By</th>
                                                @if($canUpdate || $canDelete)
                                                    <th class="text-end">Action</th>
                                                @endif
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

<!-- Add Payment Mode Modal -->
<div class="modal fade" id="add_payment_mode" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold">Add Payment Mode</h5>
                <button type="button" class="btn-close custom-btn-close border p-1" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="addPaymentModeForm" onsubmit="return false;">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-medium">Payment Mode Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Cash, UPI, Bank Transfer" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-medium">Status</label>
                            <select name="status" class="form-select select">
                                <option value="Enabled">Enabled</option>
                                <option value="Disabled">Disabled</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top p-3">
                    <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="saveAddPaymentModeBtn" class="btn btn-primary fw-medium px-4">Save Payment Mode</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Payment Mode Modal -->

<!-- Edit Payment Mode Modal -->
<div class="modal fade" id="edit_payment_mode" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold">Edit Payment Mode</h5>
                <button type="button" class="btn-close custom-btn-close border p-1" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="editPaymentModeForm" onsubmit="return false;">
                @csrf
                <input type="hidden" name="id" id="edit_payment_mode_id">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-medium">Payment Mode Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" placeholder="e.g. Cash" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-medium">Status</label>
                            <select name="status" id="edit_status" class="form-select select">
                                <option value="Enabled">Enabled</option>
                                <option value="Disabled">Disabled</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top p-3">
                    <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="saveEditPaymentModeBtn" class="btn btn-primary fw-medium px-4">Update Payment Mode</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Edit Payment Mode Modal -->

<!-- Delete Payment Mode Modal -->
<div class="modal fade" id="delete_payment_mode_modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-body p-5">
                <div class="text-center">
                    <div class="avatar avatar-xxl bg-danger-light rounded-circle mb-4 mx-auto d-flex align-items-center justify-content-center">
                        <i class="ti ti-trash-x fs-48 text-danger"></i>
                    </div>
                    <h3 class="mb-3 fw-bold">Remove Payment Mode?</h3>
                    <p class="text-muted mb-4 fs-16">Are you sure you want to remove this payment mode? This action cannot be undone.</p>
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <button type="button" class="btn btn-light btn-lg fw-medium px-5" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmDeletePaymentMode" class="btn btn-danger btn-lg fw-medium px-5">Yes, Delete It</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Payment Mode Modal -->

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    let paymentModesTable;

    function reloadTable() {
        if (paymentModesTable) {
            paymentModesTable.ajax.reload(null, false);
        }
    }

    $(document).ready(function() {
        const canUpdate = @json($canUpdate);
        const canDelete = @json($canDelete);

        const routes = {
            index: "{{ box_route('financial.settings.payment_modes') }}",
            store: "{{ box_route('financial.settings.payment_modes.store') }}",
            update: "{{ box_route('financial.settings.payment_modes.update') }}",
            destroy: "{{ box_route('financial.settings.payment_modes.destroy') }}"
        };

        // Initialize DataTable
        paymentModesTable = $('#payment_modes_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: routes.index,
            columns: [
                { data: 'id', name: 'id', orderable: false, searchable: false, render: function(data, type, row) {
                    return '<label class="checkboxs"><input type="checkbox" value="' + data + '"><span class="checkmarks"></span></label>';
                }},
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
                { data: 'added_by', name: 'added_by' },
                { data: 'updated_by', name: 'updated_by' },
                { 
                    data: 'actions', 
                    name: 'actions', 
                    orderable: false, 
                    searchable: false, 
                    className: 'text-end',
                    visible: (canUpdate || canDelete)
                }
            ],
            language: {
                search: "",
                searchPlaceholder: "Search Payment Modes...",
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
            $('#payment_modes_table tbody input[type="checkbox"]').prop('checked', isChecked);
        });

        // Search Custom Styling
        $('.dataTables_filter input').addClass('form-control form-control-sm border-20 p-2 px-3').css('width', '250px');

        // Reset Add Modal on Open
        $('#add_payment_mode').on('show.bs.modal', function() {
            $('#addPaymentModeForm')[0].reset();
            $(this).find('select[name="status"]').val('Enabled').trigger('change');
        });

        // Trigger Edit Modal via Delegation
        $(document).on('click', '.edit-payment-mode-btn', function() {
            const btn = $(this);
            $('#edit_payment_mode_id').val(btn.attr('data-id'));
            $('#edit_name').val(btn.attr('data-name'));
            $('#edit_status').val(btn.attr('data-status')).trigger('change');
            $('#edit_payment_mode').modal('show');
        });

        // Handle ADD Form Submission
        $('#addPaymentModeForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#saveAddPaymentModeBtn');
            const formData = $(this).serialize();

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

            $.ajax({
                url: routes.store,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        $('#add_payment_mode').modal('hide');
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
                    btn.prop('disabled', false).text('Save Payment Mode');
                }
            });
        });

        // Handle EDIT Form Submission
        $('#editPaymentModeForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#saveEditPaymentModeBtn');
            const formData = $(this).serialize();

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

            $.ajax({
                url: routes.update,
                type: 'PUT',
                data: formData,
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        $('#edit_payment_mode').modal('hide');
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
                    btn.prop('disabled', false).text('Update Payment Mode');
                }
            });
        });

        // Delete Logic
        let deleteId = null;
        $(document).on('click', '.delete-payment-mode-btn', function() {
            deleteId = $(this).data('id');
            $('#delete_payment_mode_modal').modal('show');
        });

        $('#confirmDeletePaymentMode').on('click', function() {
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
                        $('#delete_payment_mode_modal').modal('hide');
                        reloadTable();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    const res = xhr.responseJSON;
                    toastr.error('Error: ' + (res ? res.message : 'Failed to delete payment mode.'));
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
