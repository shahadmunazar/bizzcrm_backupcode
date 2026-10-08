@extends('layouts.main')
@section('content')
@section('title','Source')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="page-title">Sources<span class="count-title" id="source-id">0</span></h4>
                            </div>
                            <div class="col-4 text-end">
                                <div class="head-icons">
                                    <a href="sources.html" data-bs-toggle="tooltip" data-bs-placement="top"
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
                                        <input type="text" class="form-control" id="sourceSearchInput"
                                            placeholder="Search Source">
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="text-sm-end">
                                        <a href="javascript:void(0);" class="btn btn-primary " data-bs-toggle="modal"
                                            data-bs-target="#global_add_source"><i class="ti ti-square-rounded-plus me-2"></i>Add
                                            New Sources</a>
                                    </div>
                                </div>
                            </div>
                            <!-- /Search -->
                        </div>
                        <div class="card-body">



                            <!-- Contact List -->
                            <div class="table-responsive custom-table">
                                <table class="table" id="source_list">
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
                            <!-- /Contact List -->

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <!-- /Page Wrapper -->

    <!-- Global Add Source Modal Included in Header -->

    <!-- Edit Source -->
    <div class="modal fade" id="edit_source" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Source</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="editSourceForm" method="POST">
                    <input type="hidden" id="editSourceId" name="id" value="" />
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="col-form-label">Source Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editSourceName" name="name"
                                value="Phone Calls">
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
    <!-- /Edit Source -->

    <!-- Delete Source -->
    <div class="modal fade" id="delete_source" tabindex="-1" aria-labelledby="deleteSourceLabel" aria-hidden="true"
        role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <input type="hidden" id="deleteSourceId" />
                    <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                        <i class="ti ti-trash-x fs-36 text-danger"></i>
                    </div>
                    <h4 class="mb-2">Remove Source?</h4>
                    <p class="mb-0">Are you sure you want to remove it?</p>
                    <div class="d-flex align-items-center justify-content-center mt-4">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Yes, Delete it</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete Source -->
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
            return "{{ route('get.source', ['box_slug' => request()->route('box_slug')]) }}";
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
            // Initialize DataTable - use global sourceTable variable
            if ($('#source_list').length) {
                sourceTable = $('#source_list').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: getSourceRouteUrl(),
            data: function (d) {
                // Correct way to send search value
                d.search = {
                    value: $('#sourceSearchInput').val(),
                    regex: false
                };
            },
            error: function (xhr) {
                console.error("AJAX Load Error:", xhr.responseText);
            }
        },
        ordering: true,
        bFilter: false, // disable default search box
        bInfo: false,
        language: {
            search: " ",
            sLengthMenu: "MENU",
            searchPlaceholder: "Search",
            info: "START - END of TOTAL items",
            lengthMenu: "Show MENU entries",
            paginate: {
                next: 'Next <i class="fa fa-angle-right"></i>',
                previous: '<i class="fa fa-angle-left"></i> Prev'
            }
        },
        initComplete: function () {
            $(".dataTables_paginate").appendTo(".datatable-paginate");
            $(".dataTables_length").appendTo(".datatable-length");
        },
        columns: [
            {
                data: null,
                orderable: false,
                render: () =>
                    '<label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label>'
            },
            {
                data: 'title',
                render: data => `<span class="title-name">${data}</span>`
            },
            {
                data: 'created_date'
            },
            {
                data: 'status_badge'
            },
            {
                data: 'actions',
                orderable: false
            }
        ]
    });

    // ✅ Debounced search
    let debounceTimer;
    $('#sourceSearchInput').on('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            if (sourceTable) sourceTable.draw(); // ⬅️ important: use `.draw()`, not `.ajax.reload()`
        }, 300);
    });
}


            // Add Source form submission
            $('#add_source form').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const url = "{{ route('store.source', ['box_slug' => request()->route('box_slug')]) }}";

                const data = {
                    name: $form.find('input[type="text"]').val().trim(),
                    type: 'source',
                    status: $form.find('input[name="status"]:checked').val() === "1" ? 1 : 0
                };

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#add_source').modal('hide');
                            $form[0].reset();
                            showToast(response.message || 'Source added successfully!',
                                'success');
                            reloadSourceTable();
                        }
                    },
                    error: function(xhr) {
                        const res = xhr.responseJSON;
                        if (xhr.status === 422 && res?.errors) {
                            const messages = Object.values(res.errors).flat().join(' ');
                            showToast(messages, 'error');
                        } else {
                            showToast(res?.message || 'Failed to add source.', 'error');
                        }
                    }
                });
            });

            // Populate Edit Modal when clicking edit
            $(document).on('click', '.edit-source-btn', function() {
                $('#editSourceId').val($(this).data('id'));
                $('#editSourceName').val($(this).data('name'));
                $(`#edit_source input[name="status"][value="${$(this).data('status')}"]`).prop('checked',
                    true);
            });

            // Update Source form submission
            $('#editSourceForm').on('submit', function(e) {
                e.preventDefault();

                const id = $('#editSourceId').val();
                const name = $('#editSourceName').val().trim();
                const status = $('#edit_source input[name="status"]:checked').val();
                const type = 'source';
                $.ajax({
                    url: "{{ route('source.update', ['box_slug' => request()->route('box_slug')]) }}",
                    type: 'POST',
                    data: {
                        id,
                        name,
                        type,
                        status,
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(res) {
                        $('#edit_source').modal('hide');
                        if (res.success) {
                            showToast(res.message, 'success');
                            reloadSourceTable();
                        } else {
                            showToast(res.message || 'Update failed.', 'error');
                        }
                    },
                    error: function() {
                        showToast('Error updating source.', 'error');
                    }
                });
            });

            // Prepare Delete Modal on delete button click
            $(document).on('click', '.delete-source-btn', function() {
                const sourceId = $(this).data('id');
                $('#deleteSourceId').val(sourceId);
            });

            // Confirm Delete
            $('#confirmDeleteBtn').on('click', function() {
                const id = $('#deleteSourceId').val();
                const type = 'source';
                if (!id) {
                    showToast('Invalid source ID.', 'error');
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
                            showToast(res.message || 'Source deleted successfully.', 'success');
                            $('#delete_source').modal('hide');
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

            // Toggle Status checkbox change event
            $(document).on('change', '.toggle-status', function() {
                const id = $(this).data("id");
                const status = $(this).is(":checked") ? 1 : 0;

                $.ajax({
                    url: "{{ route('source.toggle', ['box_slug' => request()->route('box_slug')]) }}",
                    type: 'POST',
                    data: {
                        id,
                        status,
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
