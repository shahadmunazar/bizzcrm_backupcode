@extends('layouts.main')
@section('title', 'Quick Replies')
@section('content')

    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content">

            <div class="row">
                <div class="col-md-12">

                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="page-title">Quick Replies</h4>
                            </div>
                            <div class="col-4 text-end">
                                <div class="head-icons">
                                    <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh" onclick="$('#quick-replies-list').DataTable().ajax.reload();">
                                        <i class="ti ti-refresh-dot"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Page Header -->

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap row-gap-3">
                            <h5 class="card-title mb-0">Manage Quick Replies</h5>
                            <div class="d-flex align-items-center flex-wrap row-gap-2">
                                <div class="icon-form me-2">
                                    <span class="form-icon"><i class="ti ti-search"></i></span>
                                    <input type="text" id="custom-search" class="form-control" placeholder="Search...">
                                </div>
                                <div class="dropdown me-2">
                                    <button type="button" class="btn bg-soft-purple text-purple d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                                        <i class="ti ti-columns-3 me-2"></i>Manage Columns
                                    </button>
                                </div>
                                <button class="btn btn-primary btn-sm" id="btn-add-reply"><i class="ti ti-plus me-1"></i> Add Quick Reply</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Quick Replies List -->
                            <div class="table-responsive custom-table">
                                <table class="table" id="quick-replies-list" style="width:100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Title (Shortcut)</th>
                                            <th>Message</th>
                                            <th>Status</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /Quick Replies List -->
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <!-- /Page Wrapper -->

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="replyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="replyModalTitle">Add Quick Reply</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="replyForm">
                    <div class="modal-body">
                        <input type="hidden" id="reply_id" name="id">
                        <div class="mb-3">
                            <label class="form-label">Title / Shortcut <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reply_title" name="title" placeholder="e.g. /hello" required>
                            <small class="text-muted">A short title or command to identify this reply.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reply_message" name="message" rows="5" placeholder="Enter the full message here..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="reply_status" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-reply">Save Quick Reply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add/Edit Modal -->

    <!-- Delete Confirmation Modal -->
    <div class="modal custom-modal fade" id="delete_reply_modal" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="success-message text-center">
                        <div class="success-popup-icon bg-danger">
                            <i class="ti ti-trash-x"></i>
                        </div>
                        <h3>Delete Quick Reply?</h3>
                        <p>Are you sure you want to delete this quick reply? This action cannot be undone.</p>
                        <div class="col-lg-12 text-center form-wizard-button">
                            <button href="#" class="button btn-lights" data-bs-dismiss="modal">Cancel</button>
                            <button href="#" class="btn btn-primary" id="confirm-delete-btn">Yes, Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Columns Modal -->
    <div class="modal custom-modal fade" id="manageColumnsModal" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Columns</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input column-toggle" type="checkbox" value="0" id="col_index" checked disabled>
                        <label class="form-check-label" for="col_index">#</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input column-toggle" type="checkbox" value="1" id="col_title" checked>
                        <label class="form-check-label" for="col_title">Title (Shortcut)</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input column-toggle" type="checkbox" value="2" id="col_message" checked>
                        <label class="form-check-label" for="col_message">Message</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input column-toggle" type="checkbox" value="3" id="col_status" checked>
                        <label class="form-check-label" for="col_status">Status</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input column-toggle" type="checkbox" value="4" id="col_action" checked disabled>
                        <label class="form-check-label" for="col_action">Action</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Manage Columns Modal -->

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let table = $('#quick-replies-list').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ box_route('whatsapp.quick_replies.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'title', name: 'title' },
            { data: 'message', name: 'message' },
            { data: 'status_badge', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
        ],
        order: [[1, 'asc']]
    });

    // Custom Search
    $('#custom-search').keyup(function(){
        table.search($(this).val()).draw();
    });

    // Manage Columns Toggle
    $('.column-toggle').on('change', function (e) {
        let column = table.column($(this).val());
        column.visible(!column.visible());
    });

    // Show Add Modal
    $('#btn-add-reply').on('click', function() {
        $('#replyForm')[0].reset();
        $('#reply_id').val('');
        $('#reply_status').val(1);
        $('#replyModalTitle').text('Add Quick Reply');
        $('#replyModal').modal('show');
    });

    // Save/Update Form Submit
    $('#replyForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#reply_id').val();
        let url = id ? "{{ box_route('whatsapp.quick_replies.update', ':id') }}".replace(':id', id) : "{{ box_route('whatsapp.quick_replies.store') }}";
        let type = id ? "PUT" : "POST";
        let data = $(this).serialize();

        $('#btn-save-reply').prop('disabled', true).text('Saving...');

        $.ajax({
            url: url,
            type: type,
            data: data,
            success: function(response) {
                $('#btn-save-reply').prop('disabled', false).text('Save Quick Reply');
                if (response.success) {
                    $('#replyModal').modal('hide');
                    table.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error('Something went wrong!');
                }
            },
            error: function(xhr) {
                $('#btn-save-reply').prop('disabled', false).text('Save Quick Reply');
                let errors = xhr.responseJSON.errors;
                if (errors) {
                    let firstError = Object.values(errors)[0][0];
                    toastr.error(firstError);
                } else {
                    toastr.error('An error occurred while saving.');
                }
            }
        });
    });

    // Show Edit Modal
    $(document).on('click', '.btn-edit-reply', function() {
        let id = $(this).data('id');
        let row = table.row($(this).parents('tr')).data();
        
        $('#reply_id').val(id);
        $('#reply_title').val(row.title);
        $('#reply_message').val(row.message);
        $('#reply_status').val(row.status);
        
        $('#replyModalTitle').text('Edit Quick Reply');
        $('#replyModal').modal('show');
    });

    // Show Delete Modal
    $(document).on('click', '.btn-delete-reply', function() {
        let id = $(this).data('id');
        $('#confirm-delete-btn').data('id', id);
        $('#delete_reply_modal').modal('show');
    });

    // Confirm Delete
    $('#confirm-delete-btn').on('click', function() {
        let id = $(this).data('id');
        let url = "{{ box_route('whatsapp.quick_replies.destroy', ':id') }}".replace(':id', id);

        $(this).prop('disabled', true).text('Deleting...');

        $.ajax({
            url: url,
            type: 'DELETE',
            success: function(response) {
                $('#confirm-delete-btn').prop('disabled', false).text('Yes, Delete');
                if (response.success) {
                    $('#delete_reply_modal').modal('hide');
                    table.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error('Failed to delete.');
                }
            },
            error: function(xhr) {
                $('#confirm-delete-btn').prop('disabled', false).text('Yes, Delete');
                toastr.error('An error occurred.');
            }
        });
    });
});
</script>
@endpush
