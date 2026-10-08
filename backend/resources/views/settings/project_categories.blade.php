@extends('layouts.main')
@section('content')
@section('title', 'Project Categories')
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

                <!-- Table Card -->
                <div class="card">
                    <div class="card-header border-bottom-0">
                        <div class="row align-items-center">
                            <div class="col-sm-4">
                                <div class="icon-form mb-3 mb-sm-0">
                                    <span class="form-icon"><i class="ti ti-search"></i></span>
                                    <input type="text" class="form-control" id="searchCategories" placeholder="Search Project Categories">
                                </div>
                            </div>
                            <div class="col-sm-8 text-sm-end">
                                <div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
                                    <a href="javascript:void(0)" class="btn btn-primary d-flex align-items-center" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_category">
                                        <i class="ti ti-square-rounded-plus me-2 fs-18"></i>Add Category
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive custom-table">
                            <table class="table" id="categories_table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th>Updated By</th>
                                        <th>Action</th>
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
<!-- /Page Wrapper -->

<!-- Add Category Offcanvas -->
<div class="offcanvas offcanvas-end offcanvas-large" id="offcanvas_add_category">
    <div class="offcanvas-header border-bottom">
        <h4>Add Project Category</h4>
        <button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <form id="addCategoryForm" onsubmit="return false;">
            @csrf
            <div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-medium">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Internal" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-medium">Status</label>
                        <select name="status" class="form-select select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                <button type="submit" id="saveAddCategoryBtn" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>
<!-- /Add Category Offcanvas -->

<!-- Edit Category Offcanvas -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit_category">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Edit Project Category</h5>
        <button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ti ti-x"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <form id="editCategoryForm" onsubmit="return false;">
            @csrf
            <input type="hidden" name="id" id="edit_id">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-medium">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="edit_name" class="form-control" placeholder="e.g. Internal" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-medium">Status</label>
                    <select name="status" id="edit_status" class="form-select select">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                <button type="submit" id="saveEditCategoryBtn" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
<!-- /Edit Category Offcanvas -->

<!-- Delete Modal -->
<div class="modal fade" id="delete_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1" id="delete_title">Confirm Delete</h4>
                <p class="mb-3">
                    You want to delete the marked item. This action <strong>cannot</strong> be undone once you proceed.
                </p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete_btn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Modal -->

@endsection

@push('scripts')
<script>
    
    var table;
    $(document).ready(function() {
        table = $('#categories_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ box_route('settings.project_categories') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {
                    data: 'status', 
                    name: 'status',
                    render: function(data) {
                        return data == 1 ? '<span class="badge badge-pill bg-success">Active</span>' : '<span class="badge badge-pill bg-danger">Inactive</span>';
                    }
                },
                {data: 'created_by', name: 'created_by'},
                {data: 'updated_by', name: 'updated_by'},
                {
                    data: 'id',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var btn = '<div class="d-flex align-items-center">';
                        btn += '<a href="javascript:void(0);" class="btn btn-primary btn-sm edit-category-btn me-2" data-id="' + data + '" data-name="' + row.name + '" data-status="' + row.status + '"><i class="ti ti-edit"></i></a>';
                        btn += '<a href="javascript:void(0);" class="btn btn-danger btn-sm delete-category-btn" data-id="' + data + '"><i class="ti ti-trash"></i></a>';
                        btn += '</div>';
                        return btn;
                    }
                },
            ],
            language: {
                search: "",
                searchPlaceholder: "Search...",
            },
            bFilter: true
        });

        $('#searchCategories').on('keyup', function() {
            table.search($(this).val()).draw();
        });

        // Save Add Category
        $('#addCategoryForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = $('#saveAddCategoryBtn');
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            
            $.ajax({
                url: "{{ box_route('settings.project_categories.store') }}",
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    btn.prop('disabled', false).html('Create');
                    if (response.status) {
                        toastr.success(response.message);
                        $('#offcanvas_add_category').offcanvas('hide');
                        form[0].reset();
                        table.ajax.reload(null, false);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('Create');
                    toastr.error('Something went wrong.');
                }
            });
        });

        // Edit Category Modal populate
        $(document).on('click', '.edit-category-btn', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var status = $(this).data('status');
            
            $('#edit_id').val(id);
            $('#edit_name').val(name);
            $('#edit_status').val(status).trigger('change');
            
            $('#offcanvas_edit_category').offcanvas('show');
        });

        // Save Edit Category
        $('#editCategoryForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = $('#saveEditCategoryBtn');
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            
            $.ajax({
                url: "{{ box_route('settings.project_categories.update') }}",
                type: 'PUT',
                data: form.serialize(),
                success: function(response) {
                    btn.prop('disabled', false).html('Save Changes');
                    if (response.status) {
                        toastr.success(response.message);
                        $('#offcanvas_edit_category').offcanvas('hide');
                        table.ajax.reload(null, false);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('Save Changes');
                    toastr.error('Something went wrong.');
                }
            });
        });

        // Delete Category
        $(document).on('click', '.delete-category-btn', function() {
            var id = $(this).data('id');
            $('#confirm_delete_btn').data('id', id);
            $('#delete_modal').modal('show');
        });

        $('#confirm_delete_btn').on('click', function() {
            var id = $(this).data('id');
            var $btn = $(this);
            $btn.prop('disabled', true).text('Deleting...');
            $.ajax({
                url: "{{ box_route('settings.project_categories.destroy') }}",
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        table.ajax.reload(null, false);
                    } else {
                        toastr.error(response.message);
                    }
                    $('#delete_modal').modal('hide');
                },
                error: function(xhr) {
                    toastr.error('Something went wrong.');
                    $('#delete_modal').modal('hide');
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Yes, Delete');
                }
            });
        });
    });

    function reloadTable() {
        table.ajax.reload(null, false);
    }

    function reloadTable() {
        table.ajax.reload(null, false);
    }
</script>
@endpush
