@extends('layouts.main')
@section('content')
@section('title','Lost Reason')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content">

            <div class="row">
                <div class="col-md-12">

                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col-sm-8">
                                <h4 class="page-title">Lost Reason <span class="count-title" id="lost-id">123</span></h4>
                            </div>
                            <div class="col-sm-4 text-sm-end">
                                <div class="head-icons">
                                    <a href="lost-reason.html" data-bs-toggle="tooltip" data-bs-placement="top"
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
                                        <input type="text" class="form-control" id="lostSearchInput"
                                            placeholder="Search Lost Reason">
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="text-sm-end">
                                        <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#add_reason"><i class="ti ti-square-rounded-plus me-2"></i>Add
                                            New Reason</a>
                                    </div>
                                </div>
                            </div>
                            <!-- /Search -->
                        </div>
                        <div class="card-body">
                            <!-- Reason List -->
                            <div class="table-responsive custom-table">
                                <table class="table" id="reason-list">
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
                            <!-- /Reason List -->

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <!-- /Page Wrapper -->

    <!-- Add New Reason -->
    <div class="modal fade" id="add_reason" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Reason</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="addlostReason" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="col-form-label">Reason <span class="text-danger">*</span></label>
                            <input type="text" name="reason" class="form-control">
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
    <!-- /Add New Reason -->

    <!-- Edit Reason -->
    <div class="modal fade" id="edit_reason" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Reason</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                edit_reason<form id="editReasonForm">
                    <input type="hidden" id="editReasonId"> <!-- Hidden ID -->
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="col-form-label">Reason <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editReasonName">
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
                        <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- /Edit Reason -->

    <!-- Delete Reason -->
    {{-- <div class="modal fade" id="delete_reason" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
                                            <input type="hidden" id="deleteSourceId" />
						<div class="text-center">
							<div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
								<i class="ti ti-trash-x fs-36 text-danger"></i>
							</div>
							<h4 class="mb-2">Remove Reason?</h4>
							<p class="mb-0">AAre you sure you want to remove it.</p>
							<div class="d-flex align-items-center justify-content-center mt-4">
								<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
								<a href="lost-reason.html" class="btn btn-danger">Yes, Delete it</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> --}}
    <div class="modal fade" id="delete_reason" tabindex="-1" aria-labelledby="deleteSourceLabel" aria-hidden="true"
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
    <!-- /Delete Reason -->


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

        // Get route URL for source listing
        function getSourceRouteUrl() {
            return "{{ route('get.lost_reason', ['box_slug' => request()->route('box_slug')]) }}";
        }

        // Reload DataTable without resetting pagination
        function reloadSourceTable() {
            if (sourceTable) {
                sourceTable.ajax.reload(null, false);
            }
        }

        // Show Bootstrap toast messages
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

            let reasonTable;

if ($('#reason-list').length) {
    reasonTable = $('#reason-list').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: getSourceRouteUrl(),
            data: function (d) {
                // ✅ Set only the nested search.value key
                d.search.value = $('#lostSearchInput').val();
            },
            error: function (xhr) {
                console.error("DataTable Load Error:", xhr.responseText);
            }
        },
        bFilter: false, // hide default search
        ordering: true,
        bInfo: false,
        language: {
            search: " ",
            searchPlaceholder: "Search",
            lengthMenu: "Show MENU entries",
            paginate: {
                next: 'Next <i class="fa fa-angle-right"></i>',
                previous: '<i class="fa fa-angle-left"></i> Prev'
            }
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

    // Debounced search
    let debounceTimer;
    $('#lostSearchInput').on('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            if (reasonTable) reasonTable.draw(); // triggers AJAX with updated d.search.value
        }, 300);
    });
}




            $('#addlostReason').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                const url = "{{ route('store.source', ['box_slug' => request()->route('box_slug')]) }}";
                const data = {
                    name: $form.find('input[type="text"]').val().trim(),
                    status: $form.find('input[name="status"]:checked').val() === "1" ? 1 : 0,
                    type: 'lost_reason'
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
                            $('#add_reason').modal('hide');
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
            $(document).on('click', '.edit_reason-btn', function() {
                $('#editReasonId').val($(this).data('id'));
                $('#editReasonName').val($(this).data('title'));
                const status = $(this).data('status');
                $(`#edit_reason input[name="status"][value="${status}"]`).prop('checked', true);
            });


            // Update Source form submission
            $('#editReasonForm').on('submit', function(e) {
                e.preventDefault();

                const id = $('#editReasonId').val();
                const name = $('#editReasonName').val().trim();
                const status = $('#edit_reason input[name="status"]:checked').val();
                const type = 'lost_reason';

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
                        $('#edit_reason').modal('hide');
                        if (res.success) {
                            showToast(res.message, 'success');
                            reloadSourceTable(); // Assuming same table reload applies
                        } else {
                            showToast(res.message || 'Update failed.', 'error');
                        }
                    },
                    error: function() {
                        showToast('Error updating reason.', 'error');
                    }
                });
            });


            // Prepare Delete Modal on delete button click
            $(document).on('click', '.delete_reason-btn', function() {
                const sourceId = $(this).data('id');
                $('#deleteSourceId').val(sourceId);
            });

            // Confirm Delete
            $('#confirmDeleteBtn').on('click', function() {
                const id = $('#deleteSourceId').val();
                const type = 'lost_reason';
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
                            $('#delete_reason').modal('hide');
                            if (sourceTable) sourceTable.ajax.reload(null, false);
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
