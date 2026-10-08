@extends('layouts.main')
@section('content')
@section('title', 'Realtime Logs')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <div class="row">
            <div class="col-md-12">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">Realtime Logs (Daily Summary)</h4>
                        </div>
                        <div class="col-4 text-end">
                            <div class="head-icons">
                                <a href="{{ box_route('superadmin.realtime_logs') }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="card ">
                    <div class="card-body">
                        <!-- Contact List -->
                        <div class="table-responsive custom-table">
                            <table class="table" id="realtimeLogsDataTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Assigned Machine</th>
                                        <th>Device ID</th>
                                        <th>Emp ID</th>
                                        <th>User Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>User ID</th>
                                        <th>Box Name</th>
                                        <th>Date</th>
                                        <th>Total Punches</th>
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
<!-- /Page Wrapper -->

<!-- Punch History Modal -->
<div class="modal fade custom-modal" id="punchHistoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Punch History - <span id="modalUserName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Date:</strong> <span id="modalDate"></span> &nbsp;|&nbsp; <strong>Device ID:</strong> <span id="modalDevice"></span></p>
                <div class="table-responsive">
                    <table class="table table-striped custom-table" id="punchHistoryTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>IO Time</th>
                                <th>IO Mode</th>
                                <th>Verify Mode</th>
                                <th>Received At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data injected via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Punch History Modal -->

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#realtimeLogsDataTable').DataTable({
            processing: true,
            serverSide: true,
            order: [[9, 'desc']], // Order by Date descending
            ajax: "{{ box_route('superadmin.realtime_logs_data') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'machine_name', name: 'bm.machine_name', defaultContent: '-' },
                { data: 'device_id', name: 'realtime_logs.device_id', defaultContent: '-' },
                { data: 'employee_id', name: 'bu.employee_id', defaultContent: '-' },
                { data: 'full_name', name: 'full_name', defaultContent: '-' },
                { data: 'email', name: 'u.email', defaultContent: '-' },
                { data: 'phone', name: 'bu.phone', defaultContent: '-' },
                { data: 'user_id', name: 'realtime_logs.user_id', defaultContent: '-' },
                { data: 'box_name', name: 'b.name', defaultContent: '-' },
                { data: 'punch_date_formatted', name: 'punch_date', defaultContent: '-' },
                { data: 'punch_count', name: 'punch_count', defaultContent: '-' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Handle View Punches Click
        $('#realtimeLogsDataTable').on('click', '.view-punches', function() {
            var deviceId = $(this).data('device');
            var userId = $(this).data('user');
            var date = $(this).data('date');
            var name = $(this).data('name');

            $('#modalUserName').text(name);
            $('#modalDate').text(date);
            $('#modalDevice').text(deviceId);
            
            // Clear previous table
            $('#punchHistoryTable tbody').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
            
            // Open Modal
            $('#punchHistoryModal').modal('show');

            $.ajax({
                url: "{{ box_route('superadmin.realtime_logs_history') }}",
                type: "GET",
                data: {
                    device_id: deviceId,
                    user_id: userId,
                    date: date
                },
                success: function(response) {
                    if (response.success) {
                        var tbody = '';
                        if (response.data.length > 0) {
                            $.each(response.data, function(index, log) {
                                tbody += '<tr>';
                                tbody += '<td>' + (index + 1) + '</td>';
                                tbody += '<td>' + log.io_time + '</td>';
                                tbody += '<td>' + log.io_mode + '</td>';
                                tbody += '<td>' + log.verify_mode + '</td>';
                                tbody += '<td>' + log.received_at + '</td>';
                                tbody += '</tr>';
                            });
                        } else {
                            tbody = '<tr><td colspan="5" class="text-center">No punches found.</td></tr>';
                        }
                        $('#punchHistoryTable tbody').html(tbody);
                    } else {
                        $('#punchHistoryTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load data.</td></tr>');
                    }
                },
                error: function() {
                    $('#punchHistoryTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data.</td></tr>');
                }
            });
        });
    });
</script>
@endpush
