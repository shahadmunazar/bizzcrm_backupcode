@extends('layouts.main')
@section('content')
@section('title', 'Industry Register')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <div class="row">
            <div class="col-md-12">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">Industry Register</h4>
                        </div>
                        <div class="col-4 text-end">
                            <div class="head-icons">
                                <a href="javascript:void(0);" onclick="window.location.reload();" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
                                    <i class="ti ti-refresh-dot"></i>
                                </a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
                                    <i class="ti ti-chevrons-up"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Statistics Widgets -->
                <div class="row">
                    <!-- Total Master Industries -->
                    <div class="col-lg-4 col-md-6 d-flex">
                        <div class="card flex-fill widget-card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center overflow-hidden">
                                    <div>
                                        <p class="fs-12 fw-medium mb-1 text-truncate">Total Master Industries</p>
                                        <h4 id="global-industries-count">0</h4>
                                    </div>
                                </div>
                                <div>
                                    <span class="avatar avatar-lg bg-info flex-shrink-0 rounded-circle">
                                        <i class="ti ti-world fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Statistics Widgets -->

                <!-- Master Industry Catalog -->
                <div class="card">
                    <div class="card-header border-bottom pb-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                            <div class="d-flex align-items-center mb-2">
                                <h5 class="fw-semibold mb-0"><i class="ti ti-world me-2 text-indigo"></i>Master Industry Catalog</h5>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <button type="button" class="btn btn-primary" data-bs-target="#offcanvas_add_master" data-bs-toggle="offcanvas">
                                    <i class="ti ti-square-rounded-plus me-2"></i>Add Master Industry
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-3">
                        <div class="row align-items-center mb-3">
                            <div class="col-sm-4">
                                <div class="icon-form">
                                    <span class="form-icon"><i class="ti ti-search"></i></span>
                                    <input type="text" class="form-control" id="master-search-input" placeholder="Search Master Catalog">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive custom-table">
                            <table class="table" id="master-industry-table" style="width: 100%;">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 40px;" class="no-sort">
                                            <label class="checkboxs">
                                                <input type="checkbox" id="select-all">
                                                <span class="checkmarks"></span>
                                            </label>
                                        </th>
                                        <th>Name</th>
                                        <th>Created Date</th>
                                        <th>Status</th>
                                        <th style="width: 80px;" class="text-end no-sort">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- AJAX dynamic load -->
                                </tbody>
                            </table>
                        </div>
                        <div class="row align-items-center mt-3">
                            <div class="col-md-6">
                                <div class="master-datatable-length"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="master-datatable-paginate d-flex justify-content-end"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Master Industry Catalog -->

            </div>
        </div>

    </div>
</div>
<!-- /Page Wrapper -->

<!-- Offcanvas: Add Master Industry -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add_master">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Add New Master Industry</h5>
        <button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('superadmin.master_industry.store', ['box_slug' => $box_slug]) }}" method="POST" id="add_master_industry_form">
            @csrf
            <div class="card border-0 shadow-none mb-0">
                <div class="card-body p-0">
                    <div class="row">
                        <!-- Industry Name -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Master Industry Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" placeholder="e.g. Artificial Intelligence, Cloud Services" required>
                        </div>
                        
                        <!-- Status -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="select form-control" name="status" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end border-top pt-3 mt-4">
                <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Master Industry</button>
            </div>
        </form>
    </div>
</div>

<!-- Offcanvas: Edit Master Industry -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit_master">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Edit Master Industry</h5>
        <button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <form action="" method="POST" id="edit_master_industry_form">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_master_id">
            <div class="card border-0 shadow-none mb-0">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Master Industry Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_master_name" name="name" required>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="select form-control" id="edit_master_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end border-top pt-3 mt-4">
                <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Unified Delete Confirmation Modal -->
<div class="modal fade" id="delete_confirm_modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3 d-inline-flex align-items-center justify-content-center">
                        <i class="ti ti-trash-x fs-36 text-danger"></i>
                    </div>
                    <h4 class="mb-2">Delete Industry?</h4>
                    <p class="mb-0 text-muted">Are you sure you want to delete this master industry record?<br>This action is irreversible and may affect associated components.</p>
                    <div class="d-flex align-items-center justify-content-center mt-4">
                        <button type="button" class="btn btn-light me-2 shadow-none" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirm_delete_btn" class="btn btn-danger shadow-none">Yes, Delete it</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 11000;">
    <div id="toastMessage" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastBody"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<style>
    .bg-soft-indigo {
        background-color: rgba(102, 16, 242, 0.1) !important;
    }
    .text-indigo {
        color: #6610f2 !important;
    }
    .widget-card {
        border-radius: 12px;
        transition: transform 0.2s;
    }
    .widget-card:hover {
        transform: translateY(-2px);
    }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        let masterTable = null;
        let deleteTargetId = null;

        // Toast Helpers
        function showSuccessToast(message) {
            const $toast = $('#toastMessage');
            const $toastBody = $('#toastBody');
            $toast.removeClass('bg-danger bg-success').addClass('bg-success');
            $toastBody.html(`
                <div class="d-flex align-items-center">
                    <i class="ti ti-check-circle me-2 fs-5"></i>
                    <div class="flex-grow-1">
                        <strong>Success!</strong><br>
                        <small>${message}</small>
                    </div>
                </div>
            `);
            new bootstrap.Toast($toast[0], { autohide: true, delay: 5000 }).show();
        }

        function showErrorToast(message) {
            const $toast = $('#toastMessage');
            const $toastBody = $('#toastBody');
            $toast.removeClass('bg-danger bg-success').addClass('bg-danger');
            $toastBody.html(`
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-circle me-2 fs-5"></i>
                    <div class="flex-grow-1">
                        <strong>Error!</strong><br>
                        <small>${message}</small>
                    </div>
                </div>
            `);
            new bootstrap.Toast($toast[0], { autohide: true, delay: 5000 }).show();
        }

        // Setup CSRF token for Ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // 1. Initialize Master Industry DataTable
        masterTable = $('#master-industry-table').DataTable({
            ajax: {
                url: '{{ route('superadmin.get_master_industry_list', ['box_slug' => $box_slug]) }}',
                dataSrc: function(json) {
                    if (json.success) {
                        $('#global-industries-count').text(json.data.length);
                        return json.data;
                    }
                    return [];
                }
            },
            columns: [
                {
                    data: null,
                    render: () => `
                        <label class="checkboxs">
                            <input type="checkbox">
                            <span class="checkmarks"></span>
                        </label>
                    `,
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    render: (name) => `<h6 class="fw-semibold mb-0 text-indigo"><i class="ti ti-world me-1 fs-14"></i> ${name}</h6>`
                },
                {
                    data: 'created_at'
                },
                {
                    data: 'status',
                    render: (status) => {
                        const badgeCls = status === 'Active' ? 'bg-success' : 'bg-danger';
                        return `<span class="badge badge-pill badge-status ${badgeCls}">${status}</span>`;
                    }
                },
                {
                    data: null,
                    className: 'text-end',
                    render: (data, type, row) => `
                        <div class="dropdown table-action">
                            <a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-ellipsis-v"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item edit-master-btn" href="#" data-id="${row.id}" data-name="${row.name}" data-status="${row.status}">
                                    <i class="ti ti-edit text-blue"></i> Edit
                                </a>
                                <a class="dropdown-item delete-btn" href="#" data-id="${row.id}">
                                    <i class="ti ti-trash text-danger"></i> Delete
                                </a>
                            </div>
                        </div>
                    `,
                    orderable: false,
                    searchable: false
                }
            ],
            ordering: true,
            language: {
                search: "",
                searchPlaceholder: "Search master catalog...",
                paginate: {
                    next: 'Next <i class="fa fa-angle-right"></i>',
                    previous: '<i class="fa fa-angle-left"></i> Prev'
                }
            },
            initComplete: function() {
                $(".dataTables_paginate").appendTo(".master-datatable-paginate");
                $(".dataTables_length").appendTo(".master-datatable-length");
            }
        });

        // Search Input listener
        $('#master-search-input').on('input', function() {
            masterTable.search(this.value).draw();
        });

        // Handle Form submits
        $('#add_master_industry_form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        showSuccessToast(response.message);
                        form[0].reset();
                        bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('offcanvas_add_master')).hide();
                        masterTable.ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    showErrorToast(xhr.responseJSON?.message || 'Failed to create Master Industry.');
                }
            });
        });

        // Edit Master trigger click
        $(document).on('click', '.edit-master-btn', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const name = $(this).data('name');
            const status = $(this).data('status');

            $('#edit_master_id').val(id);
            $('#edit_master_name').val(name);
            $('#edit_master_status').val(status === 'Active' ? 'active' : 'inactive').trigger('change');

            let actionUrl = "{{ route('superadmin.master_industry.update', ['box_slug' => $box_slug, 'id' => ':id']) }}".replace(':id', id);
            $('#edit_master_industry_form').attr('action', actionUrl);

            bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('offcanvas_edit_master')).show();
        });

        // Edit Form Submit
        $('#edit_master_industry_form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        showSuccessToast(response.message);
                        bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('offcanvas_edit_master')).hide();
                        masterTable.ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    showErrorToast(xhr.responseJSON?.message || 'Failed to update Master Industry.');
                }
            });
        });

        // Delete Handler
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            deleteTargetId = $(this).data('id');
            $('#delete_confirm_modal').modal('show');
        });

        // Confirm Delete Submit
        $('#confirm_delete_btn').on('click', function() {
            if (!deleteTargetId) return;

            let deleteUrl = "{{ route('superadmin.master_industry.destroy', ['box_slug' => $box_slug, 'id' => ':id']) }}";
            deleteUrl = deleteUrl.replace(':id', deleteTargetId);

            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        showSuccessToast(response.message);
                        $('#delete_confirm_modal').modal('hide');
                        masterTable.ajax.reload(null, false);
                        deleteTargetId = null;
                    }
                },
                error: function() {
                    showErrorToast('Failed to delete master industry record.');
                }
            });
        });

        // Select-all checkbox
        $('#select-all').on('click', function() {
            const isChecked = this.checked;
            $('#master-industry-table tbody input[type="checkbox"]').prop('checked', isChecked);
        });
    });
</script>
@endpush
@endsection
