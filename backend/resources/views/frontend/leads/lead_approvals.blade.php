@extends('layouts.main')
@section('title', 'Lead Approvals')

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
                                <h4 class="page-title">Lead Approvals</h4>
                            </div>
                        </div>
                    </div>
                    <!-- /Page Header -->

                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-sm-4">
                                </div>
                                <div class="col-sm-8">
                                    <div class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
                                        <div class="dropdown me-2">
                                            <select id="requestedByFilter" class="form-select">
                                                <option value="">All Requesters</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="dropdown me-2">
                                            <select id="statusFilter" class="form-select">
                                                <option value="" selected>All Statuses</option>
                                                <option value="pending">Pending</option>
                                                <option value="approved">Approved</option>
                                                <option value="disapproved">Rejected</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover custom-table w-100" id="approvalsTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Lead Name</th>
                                            <th>Requested By</th>
                                            <th>Requested Status</th>
                                            <th>Requested Sub Status</th>
                                            <th>Requested At</th>
                                            <th>Action</th>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#approvalsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('get.lead.approvals', ['box_slug' => request()->route('box_slug')]) }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.status_filter = $('#statusFilter').val();
                    d.requested_by_filter = $('#requestedByFilter').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'lead_name', name: 'lead_name', orderable: false, searchable: false },
                { data: 'requested_by_name', name: 'requested_by_name', orderable: false, searchable: false },
                { data: 'status_change', name: 'status_change', orderable: false, searchable: false },
                { data: 'sub_status_change', name: 'sub_status_change', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at', render: function(data) {
                    return data ? new Date(data).toLocaleString() : '-';
                }},
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']]
        });

        $('#statusFilter, #requestedByFilter').change(function() {
            table.ajax.reload();
        });

        $(document).on('click', '.approve-btn', function() {
            var id = $(this).data('id');
            if (confirm('Are you sure you want to approve this request?')) {
                $.ajax({
                    url: "{{ route('lead.approvals.approve', ['box_slug' => request()->route('box_slug')]) }}",
                    type: "POST",
                    data: {
                        id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            if(typeof showToast === 'function') showToast(response.message, 'success');
                            table.ajax.reload();
                        } else {
                            if(typeof showToast === 'function') showToast(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        if(typeof showToast === 'function') showToast('An error occurred', 'error');
                    }
                });
            }
        });
        
        $(document).on('click', '.reject-btn', function() {
            var id = $(this).data('id');
            if (confirm('Are you sure you want to reject this request?')) {
                $.ajax({
                    url: "{{ route('lead.approvals.reject', ['box_slug' => request()->route('box_slug')]) }}",
                    type: "POST",
                    data: {
                        id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            if(typeof showToast === 'function') showToast(response.message, 'success');
                            table.ajax.reload();
                        } else {
                            if(typeof showToast === 'function') showToast(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        if(typeof showToast === 'function') showToast('An error occurred', 'error');
                    }
                });
            }
        });
    });
</script>
@endpush
