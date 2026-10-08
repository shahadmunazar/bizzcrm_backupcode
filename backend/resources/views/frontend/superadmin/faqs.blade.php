@extends('layouts.main')
@section('content')
@section('title','FAQs')

<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">FAQs</h4>
                        </div>
                        <div class="col-4 text-end">
                            <div class="head-icons">
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh" onclick="window.location.reload();"><i class="ti ti-refresh-dot"></i></a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- FAQ List -->
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-sm-4">
                                <div class="icon-form mb-3 mb-sm-0">
                                    <span class="form-icon"><i class="ti ti-search"></i></span>
                                    <input type="text" class="form-control" id="custom-search" placeholder="Search FAQs">
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
                                    <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#add_faq"><i class="ti ti-square-rounded-plus me-2"></i>Add New FAQ</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive custom-table">
                            <table class="table" id="faq-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Order</th>
                                        <th>Question</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /FAQ List -->
            </div>
        </div>
    </div>
</div>

<!-- Add FAQ Offcanvas -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="add_faq">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Add New FAQ</h5>
        <button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close"><i class="ti ti-x"></i></button>
    </div>
    <div class="offcanvas-body">
        <form id="add-faq-form" action="{{ box_route('superadmin.faqs.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="col-form-label">Question <span class="text-danger">*</span></label>
                <input type="text" name="question" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="col-form-label">Answer <span class="text-danger">*</span></label>
                <textarea name="answer" class="form-control summernote" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                <label class="col-form-label">Order</label>
                <input type="number" name="order" class="form-control" value="0">
            </div>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" checked>
                    <label class="form-check-label">Active Status</label>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-light me-2" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
<!-- /Add FAQ Offcanvas -->

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#faq-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ box_route("superadmin.faqs") }}',
        columns: [
            { data: 'order', name: 'order', className: 'reorder-handle' },
            { data: 'question', name: 'question' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        drawCallback: function(settings) {
            var api = this.api();

            // Remove any previously appended edit offcanvases to avoid duplicates
            $('body > .offcanvas[id^="edit_faq_"]').remove();
            // Move new dynamically generated offcanvases out of the DataTable to prevent full-width CSS bugs
            $('#faq-table .offcanvas[id^="edit_faq_"]').appendTo('body');
            
            // Re-initialize summernote inside dynamically loaded modals
            if ($('.summernote').length > 0) {
                $('.summernote').summernote({
                    height: 200,
                    minHeight: null,
                    maxHeight: null,
                    focus: false
                });
            }

            // Init sortable for drag and drop reordering
            $('#faq-table tbody').sortable({
                handle: '.reorder-handle',
                helper: function(e, ui) {
                    ui.children().each(function() {
                        $(this).width($(this).width());
                    });
                    return ui;
                },
                update: function(event, ui) {
                    var orders = [];
                    var startIndex = api.page.info().start;
                    
                    $('#faq-table tbody tr').each(function(index) {
                        var id = $(this).attr('data-id');
                        if (id) {
                            // Visually update the order number in the first column
                            $(this).find('td:first').text(startIndex + index + 1);
                            
                            orders.push({
                                id: id,
                                order: startIndex + index + 1
                            });
                        }
                    });

                    if (orders.length > 0) {
                        $.ajax({
                            url: '{{ box_route("superadmin.faqs.reorder") }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                orders: orders
                            },
                            success: function(response) {
                                if(response.success && typeof showToast === 'function') {
                                    showToast(response.message, 'success');
                                }
                            },
                            error: function() {
                                if (typeof showToast === 'function') showToast('Failed to save order', 'danger');
                            }
                        });
                    }
                }
            });
        }
    });

    $(document).on('change', '.toggle-status', function() {
        let id = $(this).data('id');
        $.ajax({
            url: '{{ box_route("superadmin.faqs.toggle-status", ":id") }}'.replace(':id', id),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (typeof showToast === 'function') {
                    showToast(response.message, 'success');
                }
            },
            error: function(err) {
                if (typeof showToast === 'function') {
                    showToast('Failed to update status', 'danger');
                }
            }
        });
    });

    // Handle Add FAQ form via AJAX
    $('#add-faq-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: form.serialize(),
            success: function(response) {
                if(response.success) {
                    if (typeof showToast === 'function') showToast(response.message, 'success');
                    // Close offcanvas
                    var offcanvasElement = document.getElementById('add_faq');
                    var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    if(offcanvas) offcanvas.hide();
                    // Reset form and summernote
                    form[0].reset();
                    form.find('.summernote').summernote('code', '');
                    table.ajax.reload(null, false);
                }
            },
            error: function(xhr) {
                if (typeof showToast === 'function') showToast(xhr.responseJSON?.message || 'Error adding FAQ', 'danger');
            }
        });
    });

    // Handle Edit FAQ form via AJAX
    $(document).on('submit', '.edit-faq-form', function(e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: form.serialize(),
            success: function(response) {
                if(response.success) {
                    if (typeof showToast === 'function') showToast(response.message, 'success');
                    // Close offcanvas
                    var offcanvasElement = form.closest('.offcanvas')[0];
                    var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    if(offcanvas) offcanvas.hide();
                    table.ajax.reload(null, false);
                }
            },
            error: function(xhr) {
                if (typeof showToast === 'function') showToast(xhr.responseJSON?.message || 'Error updating FAQ', 'danger');
            }
        });
    });

    // Handle Delete via AJAX
    $(document).on('click', '.delete-faq', function(e) {
        e.preventDefault();
        if(confirm('Are you sure you want to delete this FAQ?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '{{ box_route("superadmin.faqs.destroy", ":id") }}'.replace(':id', id),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function(response) {
                    if(response.success) {
                        if (typeof showToast === 'function') showToast(response.message, 'success');
                        table.ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    if (typeof showToast === 'function') showToast(xhr.responseJSON?.message || 'Error deleting FAQ', 'danger');
                }
            });
        }
    });
});
</script>
@endpush
