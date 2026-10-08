@extends('layouts.main')
@section('content')
@section('title','Industy')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content">

            <div class="row">
                <div class="col-md-12">

                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col-sm-8">
                                <h4 class="page-title">Industry <span class="count-title" id="Industry-id">0</span></h4>
                            </div>
                            <div class="col-sm-4 text-sm-end">
                                <div class="head-icons">
                                    <a href="industry.html" data-bs-toggle="tooltip" data-bs-placement="top"
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
                                        <input type="text" class="form-control" id="sourceSearchInputIndu"
                                            placeholder="Search Industry">
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="text-sm-end">
                                        <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#add_industry"><i class="ti ti-square-rounded-plus me-2"></i>Add
                                            New Industry</a>
                                    </div>
                                </div>
                            </div>
                            <!-- /Search -->
                        </div>
                        <div class="card-body">
                            <!-- Industry List -->
                            <div class="table-responsive custom-table">
                                <table class="table" id="industry-list">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="no-sort">
                                                <label class="checkboxs">
                                                    <input type="checkbox" id="select-all"><span class="checkmarks"></span>
                                                </label>
                                            </th>
                                            <th>Title</th>
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
                            <!-- /Industry List -->

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <!-- /Page Wrapper -->

    <!-- Add New Industry -->
    <div class="modal fade" id="add_industry" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Industry</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="add_industryForm" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="col-form-label">Industry Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name">
                        </div>
                        <div class="mb-0">
                            <label class="col-form-label">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <input type="radio" class="status-radio" id="add-active" name="status"
                                        checked="">
                                    <label for="add-active">Active</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="add-inactive" name="status">
                                    <label for="add-inactive">Inactive</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex align-items-center justify-content-end m-0">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add New Industry -->

    <!-- Edit Industry -->
    <div class="modal fade" id="edit_industry" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Industry</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="editIndustryForm" method="POST">
                    <input type="hidden" id="editIndustryId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="col-form-label">Industry Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editIndustryName" name="name"
                                value="Retail Industry">
                        </div>
                        <div class="mb-0">
                            <label class="col-form-label">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <input type="radio" class="status-radio" id="edit-active" value="1"
                                        name="status" checked="">
                                    <label for="edit-active">Active</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="edit-inactive" value="0"
                                        name="status">
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
    <!-- /Edit Industry -->

    <!-- Delete Industry -->
    <!-- Delete Modal -->
    <div class="modal fade" id="delete_source_modal" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <input type="hidden" id="deleteSourceId" />
                    <input type="hidden" id="deleteSourceType" />
                    <div class="text-center">
                        <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                            <i class="ti ti-trash-x fs-36 text-danger"></i>
                        </div>
                        <h4 class="mb-2">Remove Item?</h4>
                        <p class="mb-0">Are you sure you want to remove it?</p>
                        <div class="d-flex align-items-center justify-content-center mt-4">
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Yes, Delete it</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- /Delete Industry -->



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
    @push('scripts')


    <script>
        let sourceTable;

        function getSourceRouteUrl() {
            return "{{ route('get.get_industry', ['box_slug' => request()->route('box_slug')]) }}";
        }

        function reloadSourceTable() {
            if (sourceTable) {
                sourceTable.ajax.reload(null, false);
            }
        }

        function showToast(message, type = 'success') {
            const $toast = $('#dynamicToast');
            const $message = $('#dynamicToastMessage');

            $message.text(message);
            $toast.removeClass('bg-success bg-danger bg-warning');

            if (type === 'success') $toast.addClass('bg-success');
            else if (type === 'error') $toast.addClass('bg-danger');
            else $toast.addClass('bg-warning');

            new bootstrap.Toast($toast[0]).show();
        }

        $(document).ready(function() {
            // Initialize DataTable
            if ($('#industry-list').length) {
    sourceTable = $('#industry-list').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: getSourceRouteUrl(),
            type: 'GET',
            // No need for manual data/search param; DataTables sends it automatically
            error: xhr => console.error("DataTable Load Error:", xhr.responseText)
        },
        ordering: true,
        bFilter: true,  // Enable DataTables built-in search box if you want
        bInfo: true,
        language: {
            searchPlaceholder: "Search industries...",
            paginate: {
                next: 'Next <i class="fa fa-angle-right"></i>',
                previous: '<i class="fa fa-angle-left"></i> Prev'
            }
        },
        initComplete: function() {
            $(".dataTables_paginate").appendTo(".datatable-paginate");
            $(".dataTables_length").appendTo(".datatable-length");
        },
        columns: [
            {
                render: () =>
                    '<label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label>',
                orderable: false,
                searchable: false
            },
            { data: "title" },
            { data: "created_at" },
            {
                data: "status",
                render: data => `
                    <span class="badge badge-pill badge-status ${data === "Active" ? "bg-success" : "bg-danger"}">
                        ${data}
                    </span>`
            },
            {
                orderable: false,
                searchable: false,
                render: (data, type, row) => `
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="dropdown table-action">
                            <a href="#" class="action-icon" data-bs-toggle="dropdown">
                                <i class="fa fa-ellipsis-v"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item edit_industry-btn" href="#"
                                    data-id="${row.id}"
                                    data-name="${row.title}"
                                    data-status="${row.status}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#edit_industry">
                                    <i class="ti ti-edit text-blue"></i> Edit
                                </a>
                                <a class="dropdown-item delete_reason-btn" href="#"
                                    data-id="${row.id}"
                                    data-type="industry"
                                    data-bs-toggle="modal"
                                    data-bs-target="#delete_source_modal">
                                    <i class="ti ti-trash text-danger"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>`
            }
        ]
    });
}
let debounceTimer;
$('#sourceSearchInputIndu').on('input', function() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        sourceTable.search(this.value).draw();
    }, 300);
});

            // Add Industry
            $('#add_industryForm').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);

                $.ajax({
                    url: "{{ route('store.source', ['box_slug' => request()->route('box_slug')]) }}",
                    type: 'POST',
                    data: {
                        name: $form.find('input[name="name"]').val().trim(),
                        status: $('#add-active').is(':checked') ? 1 : 0,
                        type: 'industry',
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#add_industry').modal('hide');
                            $form[0].reset();
                            showToast(res.message || 'Industry added successfully!', 'success');
                            reloadSourceTable();
                        }
                    },
                    error: function(xhr) {
                        const res = xhr.responseJSON;
                        if (xhr.status === 422 && res?.errors) {
                            const messages = Object.values(res.errors).flat().join(' ');
                            showToast(messages, 'error');
                        } else {
                            showToast(res?.message || 'Failed to add industry.', 'error');
                        }
                    }
                });
            });

            // Edit Industry
            $(document).on('click', '.edit_industry-btn', function() {
                $('#editIndustryId').val($(this).data('id'));
                $('#editIndustryName').val($(this).data('name'));
                $(`#edit_industry input[name="status"][value="${$(this).data('status')}"]`).prop('checked',
                    true);
            });

            // Edit Industry
            $('#editIndustryForm').on('submit', function(e) {
                e.preventDefault();

                const id = $('#editIndustryId').val();
                const name = $('#editIndustryName').val().trim();
                const status = $('#edit_industry input[name="status"]:checked').val();

                $.ajax({
                    url: "{{ route('source.update', ['box_slug' => request()->route('box_slug')]) }}",
                    type: 'POST',
                    data: {
                        id,
                        name,
                        status,
                        type: 'industry',
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(res) {
                        $('#edit_industry').modal('hide');
                        if (res.success) {
                            showToast(res.message || 'Industry updated successfully!',
                                'success');
                            reloadSourceTable();
                        } else {
                            showToast(res.message || 'Update failed.', 'error');
                        }
                    },
                    error: function() {
                        showToast('Error updating industry.', 'error');
                    }
                });
            });


            // Open Delete Modal
            $(document).on('click', '.delete_reason-btn', function() {
                const id = $(this).data('id');
                const type = $(this).data('type') || 'industry'

                $('#deleteSourceId').val(id);
                $('#deleteSourceType').val(type);
            });

            // Confirm Deletion
            $('#confirmDeleteBtn').on('click', function() {
                const id = $('#deleteSourceId').val();
                const type = $('#deleteSourceType').val();

                if (!id || !type) {
                    showToast('Missing ID or type for deletion.', 'error');
                    return;
                }

                $.ajax({
                    url: "{{ route('source.delete', ['box_slug' => request()->route('box_slug')]) }}",
                    method: 'DELETE',
                    data: {
                        id: id,
                        type: type,
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(res) {
                        if (res.success) {
                            showToast(res.message ||
                                `${type.replace('_', ' ')} deleted successfully.`, 'success'
                            );
                            $('#delete_source_modal').modal('hide');
                            reloadSourceTable();
                        } else {
                            showToast(res.message || 'Delete failed.', 'error');
                        }
                    },
                    error: function() {
                        showToast('Server error while deleting.', 'error');
                    }
                });
            });


            // Toggle status (if needed in future)
            $(document).on('change', '.toggle-status', function() {
                const id = $(this).data("id");
                const status = $(this).is(":checked") ? 1 : 0;

                $.ajax({
                    url: "{{ route('source.delete', ['box_slug' => request()->route('box_slug')]) }}",
                    type: 'POST',
                    data: {
                        id,
                        status,
                        type: 'industry',
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(res) {
                        if (res.success) {
                            showToast(res.message || 'Status updated.', 'success');
                            reloadSourceTable();
                        } else {
                            showToast(res.message || 'Toggle failed.', 'warning');
                        }
                    },
                    error: function(err) {
                        console.error('Toggle error:', err.responseText);
                        showToast('Toggle AJAX error. Please try again.', 'error');
                    }
                });
            });
        });
    </script>
     @endpush
@endsection
