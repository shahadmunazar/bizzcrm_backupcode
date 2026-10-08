@extends('layouts.main')
@section('title', 'Completed Campaigns')
@section('content')

    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content">

            <div class="row">
                <div class="col-md-12">

                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col-4">
                                <h4 class="page-title">Completed<span class="count-title" id="count-header">{{ $completed ?? 0 }}</span></h4>
                            </div>
                            <div class="col-8 text-end">
                                <div class="head-icons">
                                    <a href="{{ box_route('whatsapp.campaign_complete') }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
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

                    <!-- Campaign Status -->
                    <div class="row">
                        <div class="col-xl-3 col-lg-6">
                            <div class="campaign-box bg-danger-light">
                                <div class="campaign-img">
                                    <span><i class="ti ti-brand-campaignmonitor"></i></span>
                                    <p>Total Completed</p>
                                </div>
                                <h2>{{ $totalCompleted ?? 0 }}</h2>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="campaign-box bg-warning-light">
                                <div class="campaign-img">
                                    <span><i class="ti ti-send"></i></span>
                                    <p>Total Sent</p>
                                </div>
                                <h2>{{ $totalSent ?? 0 }}</h2>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="campaign-box bg-purple-light">
                                <div class="campaign-img">
                                    <span><i class="ti ti-circle-check"></i></span>
                                    <p>Delivered</p>
                                </div>
                                <h2>{{ $totalDelivered ?? 0 }}</h2>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="campaign-box bg-success-light">
                                <div class="campaign-img">
                                    <span><i class="ti ti-clock-pause"></i></span>
                                    <p>Pending</p>
                                </div>
                                <h2>{{ $totalPending ?? 0 }}</h2>
                            </div>
                        </div>
                    </div>
                    <!-- /Campaign Status -->

                    <!-- Campaign Tab -->
                    <div class="campaign-tab">
                        <ul class="nav">
                            <li>
                                <a href="{{ box_route('whatsapp.campaign') }}">Active Campaign</a>
                            </li>
                            <li>
                                <a href="{{ box_route('whatsapp.campaign_complete') }}" class="active">Completed Campaign</a>
                            </li>
                            <li>
                                <a href="{{ box_route('whatsapp.campaign_archieve') }}">Archived Campaign</a>
                            </li>
                        </ul>
                    </div>
                    <!-- Campaign Tab -->

                    <div class="card">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                                <h5 class="fw-bold text-dark mb-0"><i class="ti ti-circle-check me-2 text-success"></i>History</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="dropdown me-2">
                                        <select class="form-select" id="project_filter" aria-label="Filter by Project">
                                            <option value="">All Projects</option>
                                            @foreach($projects as $project)
                                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="dropdown me-2">
                                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white border-1 px-3 py-1" data-bs-toggle="dropdown"><i class="ti ti-package-export me-2"></i>Export</a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0);" data-url="{{ route('whatsapp.campaign.export.pdf', ['box_slug' => $box->slug, 'status' => 'COMPLETED']) }}" class="dropdown-item btn-export-pdf"><i class="ti ti-file-type-pdf text-danger me-1"></i>Export as PDF</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);" data-url="{{ route('whatsapp.campaign.export.excel', ['box_slug' => $box->slug, 'status' => 'COMPLETED']) }}" class="dropdown-item btn-export-excel"><i class="ti ti-file-type-xls text-green me-1"></i>Export as Excel</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div id="bulk-actions" style="display: none;">
                                        <button id="btn-bulk-delete" class="btn btn-soft-danger btn-sm px-3 rounded-pill">
                                            <i class="ti ti-trash me-1"></i>Delete Selected
                                        </button>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#upload-campaign-modal"><i class="ti ti-square-rounded-plus me-1"></i>Add New Campaign</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive p-3">
                                <table class="table table-hover align-middle mb-0" id="campaign-complete-table" style="width: 100%;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="no-sort" style="width: 40px;">
                                                <label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
                                            </th>
                                            <th style="width: 50px;"></th>
                                            <th>Name</th>
                                            <th>Template</th>
                                            <th>Performance</th>
                                            <th>Completed On</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="delete_campaign" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                        <i class="ti ti-trash-x fs-36 text-danger"></i>
                    </div>
                    <h4 class="mb-2">Remove campaign?</h4>
                    <p class="mb-0">This will permanently delete the campaign history.</p>
                    <div class="d-flex align-items-center justify-content-center mt-4">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirm-delete-btn" class="btn btn-danger">Yes, Delete it</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap');
        body { font-family: 'Outfit', sans-serif !important; }
        .cursor-pointer { cursor: pointer; }
        .campaign-row.selected { background-color: rgba(67, 97, 238, 0.05) !important; }
    </style>

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    let selectedIds = new Set();

    const table = $('#campaign-complete-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('whatsapp.campaign_complete', $box->slug) }}",
            data: function(d) {
                var projectId = $('#project_filter').val();
                if (projectId) d.project_id = projectId;
            }
        },
        columns: [
            { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
            { data: 'icon', name: 'icon', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'template', name: 'template.name' },
            { data: 'sent', name: 'messages_count' },
            { data: 'schedule_at', name: 'schedule_at' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: { search: "", searchPlaceholder: "Search history..." },
        drawCallback: function() {
            $('.campaign-checkbox').each(function() {
                if (selectedIds.has($(this).val())) $(this).prop('checked', true).closest('tr').addClass('selected');
            });
            $('#selected-count').text(selectedIds.size);
            if (selectedIds.size > 0) $('#bulk-actions').fadeIn(); else $('#bulk-actions').fadeOut();
        },
        createdRow: function(row) { $(row).addClass('campaign-row'); }
    });

    // Handle project filter change
    $('#project_filter').on('change', function() {
        table.ajax.reload();
    });

    // Selection Logic
    $(document).on('click', '.campaign-row', function(e) {
        if ($(e.target).closest('.btn, a, input, i').length) return;
        const cb = $(this).find('.campaign-checkbox');
        const id = cb.val();
        const checked = !cb.prop('checked');
        cb.prop('checked', checked);
        if (checked) { selectedIds.add(id); $(this).addClass('selected'); } 
        else { selectedIds.delete(id); $(this).removeClass('selected'); $('#select-all').prop('checked', false); }
        $('#selected-count').text(selectedIds.size);
        if (selectedIds.size > 0) $('#bulk-actions').fadeIn(); else $('#bulk-actions').fadeOut();
    });

    $('#select-all').on('change', function() {
        const checked = $(this).prop('checked');
        $('.campaign-checkbox').each(function() {
            const id = $(this).val();
            $(this).prop('checked', checked);
            if (checked) { selectedIds.add(id); $(this).closest('tr').addClass('selected'); } 
            else { selectedIds.delete(id); $(this).closest('tr').removeClass('selected'); }
        });
        if (selectedIds.size > 0) $('#bulk-actions').fadeIn(); else $('#bulk-actions').fadeOut();
    });

    // Actions
    $(document).on('click', '.btn-delete-campaign', function() {
        $('#confirm-delete-btn').removeData('bulk');
        $('#confirm-delete-btn').data('id', $(this).data('id'));
        $('#delete_campaign').modal('show');
    });

    $('#btn-bulk-delete').on('click', function() {
        $('#confirm-delete-btn').data('bulk', true);
        $('#delete_campaign').modal('show');
    });

    $('#confirm-delete-btn').on('click', function() {
        const isBulk = $(this).data('bulk');
        if (isBulk) {
            if (selectedIds.size === 0) return;
            $.ajax({
                url: "{{ route('whatsapp.campaign.bulk-delete', $box->slug) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: Array.from(selectedIds)
                },
                success: function(response) {
                    $('#delete_campaign').modal('hide');
                    selectedIds.clear();
                    $('#select-all').prop('checked', false);
                    $('#bulk-actions').fadeOut();
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    $('#delete_campaign').modal('hide');
                    alert(xhr.responseJSON?.message || "Failed to bulk delete campaigns.");
                }
            });
        } else {
            const id = $(this).data('id');
            $.ajax({
                url: "{{ route('whatsapp.campaign.destroy', ['box_slug' => $box->slug, 'id' => ':id']) }}".replace(':id', id),
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function() {
                    $('#delete_campaign').modal('hide');
                    table.ajax.reload(null, false);
                }
            });
        }
    });

    $('.btn-export-pdf, .btn-export-excel').on('click', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        
        showToast('Download started...', 'info');
        window.location.href = url;
    });

    @include('whatsapp.partials.upload_campaign_js')
});
</script>
@endpush

@include('whatsapp.partials.upload_campaign_modal')

@endsection