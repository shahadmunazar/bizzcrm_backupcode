@extends('layouts.main')
@section('content')
@section('title', 'Email Messages')
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">Synchronized Emails</h4>
                            <p class="text-muted mb-0">Review and manage all incoming communication from connected Gmail accounts.</p>
                        </div>
                    </div>
                </div>

                @include('settings.partials.nav')

                <div class="row">
                    <div class="col-xl-3 col-lg-12 theiaStickySidebar">
                        @include('settings.partials.sidebar')
                    </div>
                    <div class="col-xl-9 col-lg-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
                                <div class="d-flex align-items-center">
                                    <h5 class="card-title mb-0 fw-bold me-3">Recent Messages</h5>
                                    <select id="inbox_filter" class="form-select form-select-sm rounded-pill px-3" style="width: 250px;">
                                        <option value="">All Inboxes</option>
                                        @foreach($inboxes as $inbox)
                                            <option value="{{ $inbox->id }}">{{ $inbox->email }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn btn-outline-primary btn-sm rounded-pill" onclick="reloadData()">
                                    <i class="ti ti-refresh me-1"></i>Refresh List
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive remove-scroll">
                                    <table class="table table-hover mb-0" id="emails-datatable">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Activity Created</th>
                                                <th>Received From</th>
                                                <th>Subject & Preview</th>
                                                <th>Project Inbox</th>
                                                <th>Status</th>
                                                <th>Received Date</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Content Modal -->
<div class="modal fade" id="email_content_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-lg bg-soft-primary text-primary rounded-circle me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold;">
                        <span id="sender_initials">?</span>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modal_subject" style="font-size: 1.25rem;">Email Details</h5>
                        <p class="text-muted small mb-0" id="modal_from_sub"></p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="card border-0 bg-light mb-4" style="border-radius: 15px;">
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 1px;">From</label>
                                <div class="fw-bold text-dark" id="modal_from"></div>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <label class="text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 1px;">Received Date</label>
                                <div class="text-dark" id="modal_date"></div>
                            </div>
                            <div class="col-12 border-top pt-2 mt-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <label class="text-uppercase text-muted fw-bold d-block" style="font-size: 10px; letter-spacing: 1px;">To Project Inbox</label>
                                        <span id="modal_to" class="badge bg-white text-info border border-info-subtle rounded-pill px-3 mt-1"></span>
                                    </div>
                                    <div id="status_container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <label class="text-uppercase text-muted fw-bold mb-2 d-block" style="font-size: 10px; letter-spacing: 1px;">Message Content</label>
                <div class="email-body-container bg-white border rounded-4 p-4" id="modal_body" style="min-height: 250px; max-height: 500px; overflow-y: auto; font-family: 'Inter', sans-serif; line-height: 1.6; color: #333; font-size: 15px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
                </div>

                <div id="attachments_section" class="mt-4" style="display: none;">
                    <label class="text-uppercase text-muted fw-bold mb-2 d-block" style="font-size: 10px; letter-spacing: 1px;">Attachments</label>
                    <div id="attachments_list" class="d-flex flex-wrap gap-2"></div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0 justify-content-between">
                <div>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="copyEmailBody()">
                        <i class="ti ti-copy me-1"></i>Copy Body
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" id="btn_process_lead" style="display: none;">
                        <i class="ti ti-user-plus me-1"></i>Add as Lead
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    let table;
    let currentMessageId = null;

    $(document).ready(function() {
        table = $('#emails-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ box_route('settings.get.email_messages') }}",
                data: function(d) {
                    d.inbox_id = $('#inbox_filter').val();
                }
            },
            order: [[5, 'desc']], // Default order by received_at DESC
            columns: [
                { data: 'is_processed', name: 'is_processed', searchable: false },
                { data: 'from', name: 'from_name' },
                { data: 'subject_snippet', name: 'subject' },
                { data: 'inbox.email', name: 'inbox.email', defaultContent: 'N/A' },
                { data: 'is_processed', name: 'is_processed', searchable: false },
                { data: 'received_at', name: 'received_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                processing: '<div class="spinner-border text-primary" role="status"></div>',
                emptyTable: "No emails synchronized yet.",
                search: "_INPUT_",
                searchPlaceholder: "Search sender or subject...",
                lengthMenu: "Show _MENU_ entries"
            },
            pageLength: 25,
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm justify-content-end');
                $('.dataTables_filter input').addClass('form-control form-control-sm rounded-pill px-3');
            }
        });

        $('#inbox_filter').on('change', function() {
            table.ajax.reload();
        });
    });

    function reloadData() {
        table.ajax.reload();
        toastr.success('List updated successfully');
    }

    function viewEmailDetails(id) {
        currentMessageId = id;
        // Show loading state in modal or use a global loader
        $('#modal_body').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2 text-muted">Fetching email content...</p></div>');
        $('#modal_subject').text('Loading...');
        $('#btn_process_lead').hide();
        
        var modalObj = new bootstrap.Modal(document.getElementById('email_content_modal'));
        modalObj.show();

        $.ajax({
            url: "{{ box_route('settings.get.email_message_detail', ['id' => ':id']) }}".replace(':id', id),
            type: "GET",
            success: function(response) {
                console.log('Email Details received:', response);
                if (response.success) {
                    const msg = response.data;
                    $('#modal_subject').text(msg.subject || '(No Subject)');
                    $('#modal_from').text((msg.from_name || 'Unknown'));
                    $('#modal_from_sub').text('<' + msg.from_email + '>');
                    $('#modal_date').text(response.received_at_formatted);
                    $('#modal_to').text(msg.inbox ? msg.inbox.email : 'N/A');
                    $('#modal_body').html(msg.body || '(No Content available for ID: ' + id + ')');
                    
                    // Handle Initials
                    const initials = (msg.from_name || msg.from_email || '?').charAt(0).toUpperCase();
                    $('#sender_initials').text(initials);

                    // Handle Status in Modal
                    let statusHtml = '';
                    if (msg.activity_status === 'Uploaded' || (msg.is_processed && msg.lead_id)) {
                        statusHtml = `<span class="badge bg-soft-success text-success rounded-pill px-3"><i class="ti ti-check me-1"></i>Uploaded</span>`;
                        $('#btn_process_lead').hide();
                    } else {
                        statusHtml = `<span class="badge bg-soft-warning text-warning rounded-pill px-3">Skip</span>`;
                        $('#btn_process_lead').show();
                    }
                    $('#status_container').html(statusHtml);

                    // Handle Attachments
                    if (response.attachments && response.attachments.length > 0) {
                        $('#attachments_section').show();
                        let attachHtml = '';
                        response.attachments.forEach(att => {
                            const downloadUrl = "{{ box_route('google.download_attachment', ['message_id' => ':mid', 'attachment_id' => ':aid', 'filename' => ':fname']) }}"
                                .replace(':mid', msg.message_id)
                                .replace(':aid', att.attachmentId)
                                .replace(':fname', encodeURIComponent(att.filename));
                            
                            attachHtml += `
                                <a href="${downloadUrl}" class="btn btn-outline-light btn-sm text-dark border d-flex align-items-center gap-2 px-3 py-2 rounded-3" target="_blank" style="background: #f8f9fa;">
                                    <i class="ti ti-paperclip text-primary"></i>
                                    <div>
                                        <div class="fw-bold small mb-0">${att.filename}</div>
                                        <div class="text-muted tiny" style="font-size: 10px;">${(att.size / 1024).toFixed(1)} KB</div>
                                    </div>
                                    <i class="ti ti-download ms-2"></i>
                                </a>`;
                        });
                        $('#attachments_list').html(attachHtml);
                    } else {
                        $('#attachments_section').hide();
                    }
                }
            },
            error: function(xhr) {
                console.error('Email Details AJAX error:', xhr);
                let errorMsg = 'Failed to load email details. Please try again.';
                if (xhr.status === 404 && xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.status === 500) {
                    errorMsg = 'Internal Server Error. Please check logs.';
                }
                $('#modal_body').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                $('#modal_subject').text('Error');
            }
        });
    }

    $('#btn_process_lead').on('click', function() {
        if (!currentMessageId) return;

        const btn = $(this);
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

        $.ajax({
            url: "{{ box_route('settings.post.add_gmail_as_lead') }}",
            type: "POST",
            data: {
                message_id: currentMessageId,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    bootstrap.Modal.getInstance(document.getElementById('email_content_modal')).hide();
                    table.ajax.reload(null, false); // Reload table preserving pagination
                } else {
                    toastr.error(response.message || 'Failed to process lead.');
                    btn.prop('disabled', false).html(originalHtml);
                }
            },
            error: function(xhr) {
                let msg = 'Error processing lead.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                toastr.error(msg);
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    function copyEmailBody() {
        const bodyText = $('#modal_body')[0].innerText; // Get plain text for clipboard
        const el = document.createElement('textarea');
        el.value = bodyText;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        toastr.success('Email body copied to clipboard!');
    }
</script>

<style>
    #emails-datatable thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        color: #777;
        border-bottom-width: 1px;
    }
    #emails-datatable tbody td {
        vertical-align: middle;
        padding-top: 15px;
        padding-bottom: 15px;
    }
    .bg-soft-success { background-color: rgba(30, 206, 124, 0.1) !important; }
    .bg-soft-warning { background-color: rgba(255, 152, 0, 0.1) !important; }
    .bg-soft-info { background-color: rgba(0, 184, 216, 0.1) !important; }
    .dataTables_wrapper .dataTables_processing {
        background: rgba(255,255,255,0.8);
        box-shadow: none;
        border: none;
    }
    .modal-content { border-radius: 15px; overflow: hidden; }
    .modal-header { padding: 20px 25px; }
    .email-details-header { border: 1px solid #eee; }
    #modal_body img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 10px 0;
        display: block;
    }
</style>
@endpush
@endsection
