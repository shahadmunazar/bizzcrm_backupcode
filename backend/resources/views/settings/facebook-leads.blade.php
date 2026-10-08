@extends('layouts.main')

@section('title', 'Facebook Leads')

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-8">
                    <h4 class="page-title">Facebook Leads</h4>
                    <p class="text-muted">Manage and track all synchronized leads from Facebook.</p>
                </div>
                <div class="col-4 text-end">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <button type="button" class="btn btn-danger" id="bulk-delete-btn" style="display: none;" onclick="bulkDeleteLeads()">
                            <i class="ti ti-trash"></i> Bulk Delete (<span id="selected-count">0</span>)
                        </button>
                        <button type="button" class="btn btn-primary" id="sync-leads-btn" onclick="syncFacebookLeads()">
                            <i class="ti ti-refresh"></i> Sync Facebook Leads
                        </button>
                        <a href="{{ route('settings.integrations.facebook.form', ['box_slug' => $box->slug]) }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left"></i> Back to Forms
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Statistics Cards -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar"><i class="ti ti-users"></i></span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Total Leads</div>
                                <div class="text-muted" id="stat-total-leads">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-success text-white avatar"><i class="ti ti-check"></i></span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Processed</div>
                                <div class="text-muted" id="stat-processed-leads">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-warning text-white avatar"><i class="ti ti-clock"></i></span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Pending</div>
                                <div class="text-muted" id="stat-pending-leads">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Leads History</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="facebook-leads-table" class="table table-vcenter card-table datatable">
                                <thead>
                                    <tr>
                                        <th style="width: 30px;">
                                            <input type="checkbox" class="form-check-input" id="check-all">
                                        </th>
                                        <th>Lead ID</th>
                                        <th>Page</th>
                                        <th>Form Name</th>
                                        <th>Lead Details</th>
                                        <th>Status</th>
                                        <th>Synced At</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#facebook-leads-table')) {
            $('#facebook-leads-table').DataTable().destroy();
        }

        $('#facebook-leads-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('facebook.leads.list', ['box_slug' => $box->slug]) }}",
            drawCallback: function(settings) {
                var json = settings.json;
                if (json && json.stats) {
                    $('#stat-total-leads').text(json.stats.total_leads);
                    $('#stat-processed-leads').text(json.stats.processed_leads);
                    $('#stat-pending-leads').text(json.stats.pending_leads);
                }
            },
            columns: [
                { data: 'select', name: 'select', orderable: false, searchable: false },
                { data: 'leadgen_id', name: 'leadgen_id' },
                { data: 'page.name', name: 'page.name' },
                { data: 'form_name', name: 'form_name', orderable: false, searchable: false },
                { data: 'lead_details', name: 'lead_details', orderable: false, searchable: false },
                { data: 'status', name: 'status' },
                { data: 'created_time', name: 'created_time' }
            ],
            order: [[6, 'desc']],
            language: {
                processing: '<div class="spinner-border text-primary" role="status"></div>'
            }
        });

        // Check/Uncheck all
        $('#check-all').on('click', function() {
            $('.lead-checkbox').prop('checked', this.checked);
            updateBulkDeleteButton();
        });

        // Individual checkbox click
        $(document).on('click', '.lead-checkbox', function() {
            if ($('.lead-checkbox:checked').length === $('.lead-checkbox').length) {
                $('#check-all').prop('checked', true);
            } else {
                $('#check-all').prop('checked', false);
            }
            updateBulkDeleteButton();
        });

        function updateBulkDeleteButton() {
            const selectedCount = $('.lead-checkbox:checked').length;
            $('#selected-count').text(selectedCount);
            if (selectedCount > 0) {
                $('#bulk-delete-btn').fadeIn();
            } else {
                $('#bulk-delete-btn').fadeOut();
            }
        }
    });

    function syncFacebookLeads() {
        const btn = $('#sync-leads-btn');
        const originalHtml = btn.html();
        
        btn.prop('disabled', true).html('<i class="ti ti-loader rotate"></i> Syncing...');
        
        $.ajax({
            url: "{{ route('facebook.sync-leads', ['box_slug' => $box->slug]) }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Reload table to see new leads
                    $('#facebook-leads-table').DataTable().ajax.reload();
                } else {
                    toastr.error(response.message);
                }
                btn.prop('disabled', false).html(originalHtml);
            },
            error: function() {
                toastr.error('An error occurred while starting sync.');
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    }

    function bulkDeleteLeads() {
        const selectedIds = [];
        $('.lead-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) return;

        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to delete " + selectedIds.length + " leads. This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete them!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('facebook.leads.delete', ['box_slug' => $box->slug]) }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#facebook-leads-table').DataTable().ajax.reload();
                            $('#check-all').prop('checked', false);
                            $('#bulk-delete-btn').fadeOut();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('An error occurred while deleting leads.');
                    }
                });
            }
        });
    }
</script>
@endpush
