@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@section('title', 'Bank Account')
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
                                    <h4 class="fs-20 fw-semibold">Bank Accounts</h4>
                                    @php
                                        $isPrivilegedUser = \App\Helpers\PermissionHelper::isPrivilegedUser();
                                        $canCreate = $isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute('financial.settings.bank_accounts.store');
                                        $canUpdate = $isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute('financial.settings.bank_accounts.update');
                                        $canDelete = $isPrivilegedUser || \App\Helpers\PermissionHelper::canAccessRoute('financial.settings.bank_accounts.destroy');
                                    @endphp
                                    @if ($canCreate)
                                        <a href="javascript:void(0)" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal"
                                            data-bs-target="#add_bank">
                                            <i class="ti ti-square-rounded-plus me-2 fs-18"></i>Add Bank Account
                                        </a>
                                    @endif
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="banks_table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="no-sort">
                                                    <label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
                                                </th>
                                                <th>Sr. No.</th>
                                                <th>Bank Name</th>
                                                <th>Account No</th>
                                                <th>Company</th>
                                                <th>IFSC</th>
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

<!-- Add Bank Account Modal -->
<div class="modal fade" id="add_bank" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold">Add Bank Account</h5>
                <button type="button" class="btn-close custom-btn-close border p-1" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="addBankForm" onsubmit="return false;">
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
                            <label class="form-label fw-medium">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. HDFC Bank" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Account Number <span class="text-danger">*</span></label>
                            <input type="text" name="account_no" class="form-control" placeholder="Enter Full Account Number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">IFSC Code <span class="text-danger">*</span></label>
                            <input type="text" name="ifsc" class="form-control" placeholder="Enter IFSC Code" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Branch / Address <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control" placeholder="Enter Branch Name or Address" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Display in Invoice(s)</label>
                            <select name="show_pdf" class="form-select select">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
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
                    <button type="submit" id="saveAddBankBtn" class="btn btn-primary fw-medium px-4">Save Bank Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Bank Account Modal -->

<!-- Edit Bank Account Modal -->
<div class="modal fade" id="edit_bank" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold">Edit Bank Account</h5>
                <button type="button" class="btn-close custom-btn-close border p-1" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="editBankForm" onsubmit="return false;">
                @csrf
                <input type="hidden" name="id" id="edit_bank_id">
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
                            <label class="form-label fw-medium">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" placeholder="e.g. HDFC Bank" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Account Number <span class="text-danger">*</span></label>
                            <input type="text" name="account_no" id="edit_account_no" class="form-control" placeholder="Enter Full Account Number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">IFSC Code <span class="text-danger">*</span></label>
                            <input type="text" name="ifsc" id="edit_ifsc" class="form-control" placeholder="Enter IFSC Code" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Branch / Address <span class="text-danger">*</span></label>
                            <input type="text" name="address" id="edit_address" class="form-control" placeholder="Enter Branch Name or Address" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Display in Invoice(s)</label>
                            <select name="show_pdf" id="edit_show_pdf" class="form-select select">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
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
                    <button type="submit" id="saveEditBankBtn" class="btn btn-primary fw-medium px-4">Update Bank Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Edit Bank Account Modal -->

<!-- Delete Bank Account Modal -->
<div class="modal fade" id="delete_bank_modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-body p-5">
                <div class="text-center">
                    <div class="avatar avatar-xxl bg-danger-light rounded-circle mb-4 mx-auto d-flex align-items-center justify-content-center">
                        <i class="ti ti-trash-x fs-48 text-danger"></i>
                    </div>
                    <h3 class="mb-3 fw-bold">Remove Bank Account?</h3>
                    <p class="text-muted mb-4 fs-16">Are you sure you want to remove this bank account? This action cannot be undone.</p>
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <button type="button" class="btn btn-light btn-lg fw-medium px-5" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmDeleteBank" class="btn btn-danger btn-lg fw-medium px-5">Yes, Delete It</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Bank Account Modal -->

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    let banksTable;

    function reloadTable() {
        if (banksTable) {
            banksTable.ajax.reload(null, false);
        }
    }

    $(document).ready(function() {
        const routes = {
            index: "{{ box_route('financial.settings.bank_accounts') }}",
            store: "{{ box_route('financial.settings.bank_accounts.store') }}",
            update: "{{ box_route('financial.settings.bank_accounts.update') }}",
            destroy: "{{ box_route('financial.settings.bank_accounts.destroy') }}"
        };

        const canUpdate = {{ $canUpdate ? 'true' : 'false' }};
        const canDelete = {{ $canDelete ? 'true' : 'false' }};

        // Initialize DataTable
        banksTable = $('#banks_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: routes.index,
            columns: [
                { data: 'id', name: 'id', orderable: false, searchable: false, render: function(data, type, row) {
                    return '<label class="checkboxs"><input type="checkbox" value="' + data + '"><span class="checkmarks"></span></label>';
                }},
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'account_no', name: 'account_no' },
                { data: 'company_name', name: 'company_name' },
                { data: 'ifsc', name: 'ifsc' },
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
                searchPlaceholder: "Search Bank Accounts...",
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
            $('#banks_table tbody input[type="checkbox"]').prop('checked', isChecked);
        });

        // Search Custom Styling
        $('.dataTables_filter input').addClass('form-control form-control-sm border-20 p-2 px-3').css('width', '250px');

        // Reset Add Modal on Open
        $('#add_bank').on('show.bs.modal', function() {
            $('#addBankForm')[0].reset();
            // Reset selects if needed, or rely on form reset
            $(this).find('select').each(function() {
                $(this).val($(this).find('option:first').val()).trigger('change');
            });
             // Set defaults specifically
            $(this).find('select[name="show_pdf"]').val('No').trigger('change');
            $(this).find('select[name="status"]').val('Enabled').trigger('change');
            $(this).find('select[name="company_id"]').val('').trigger('change');
        });

        // Trigger Edit Modal via Delegation
        $(document).on('click', '.edit-bank-btn', function() {
            const btn = $(this);
            // Populate Edit Form using .attr() to ensure we get raw string values
            $('#edit_bank_id').val(btn.attr('data-id'));
            $('#edit_company_id').val(btn.attr('data-company-id')).trigger('change');
            $('#edit_name').val(btn.attr('data-name'));
            $('#edit_account_no').val(btn.attr('data-account-no'));
            $('#edit_ifsc').val(btn.attr('data-ifsc'));
            $('#edit_address').val(btn.attr('data-address'));
            $('#edit_show_pdf').val(btn.attr('data-show-pdf')).trigger('change');
            $('#edit_status').val(btn.attr('data-status')).trigger('change');

            $('#edit_bank').modal('show');
        });

        // Handle ADD Form Submission
        $('#addBankForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#saveAddBankBtn');
            const formData = $(this).serialize();

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

            $.ajax({
                url: routes.store,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        $('#add_bank').modal('hide');
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
                    btn.prop('disabled', false).text('Save Bank Account');
                }
            });
        });

        // Handle EDIT Form Submission
        $('#editBankForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#saveEditBankBtn');
            const formData = $(this).serialize();

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

            $.ajax({
                url: routes.update,
                type: 'PUT',
                data: formData,
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        $('#edit_bank').modal('hide');
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
                    btn.prop('disabled', false).text('Update Bank Account');
                }
            });
        });

        // Delete Logic
        let deleteId = null;
        $(document).on('click', '.delete-bank-btn', function() {
            deleteId = $(this).data('id');
            $('#delete_bank_modal').modal('show');
        });

        $('#confirmDeleteBank').on('click', function() {
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
                        $('#delete_bank_modal').modal('hide');
                        reloadTable();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    const res = xhr.responseJSON;
                    toastr.error('Error: ' + (res ? res.message : 'Failed to delete bank account.'));
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
