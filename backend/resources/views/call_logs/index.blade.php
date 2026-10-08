@extends('layouts.main')
@section('title', 'Call Logs')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Call Logs</h3>
                </div>
                <div class="col-auto float-end ms-auto d-flex gap-2">
                    <a href="{{ route('call.dashboard', ['box_slug' => request()->route('box_slug')]) }}" class="btn btn-primary d-flex align-items-center"><i class="fa fa-dashboard me-1"></i> Call Dashboard</a>
                    <div class="dropdown">
                        <button class="btn btn-white border dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-download"></i> Export
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                            <li><a class="dropdown-item export-option" href="#" data-type="excel"><i class="fa fa-file-excel-o text-success me-2"></i> Export to Excel</a></li>
                            <li><a class="dropdown-item export-option" href="#" data-type="pdf"><i class="fa fa-file-pdf-o text-danger me-2"></i> Export to PDF</a></li>
                        </ul>
                    </div>
                    <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#addCallLogModal"><i class="fa fa-plus"></i> Add Call Log</a>
                </div>
            </div>
        </div>

        <!-- Filter Row -->
        <div class="row filter-row mb-4">
            <div class="col-sm-6 col-md-3">
                <div class="form-group form-focus select-focus">
                    <select class="form-select select2" id="userFilter" multiple data-placeholder="Select Users">
                        @foreach($boxUsers as $bUser)
                            <option value="{{ $bUser->id }}">{{ $bUser->name }} ({{ $bUser->email }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="form-group form-focus">
                    <input type="text" class="form-control custom-daterange" id="dateRangeFilter" placeholder="Select Date Range" title="Date Range">
                </div>
            </div>
            <div class="col-sm-6 col-md-2">
                <div class="form-group form-focus select-focus">
                    <select class="form-select" id="sortOrderFilter">
                        <option value="desc">Desc (Newest)</option>
                        <option value="asc">Asc (Oldest)</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <button class="btn btn-success btn-block w-100" id="filterBtn"> Filter </button>
            </div>
        </div>
        <!-- /Filter Row -->

        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover custom-table w-100" id="callLogsDataTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>User Name</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Duration (sec)</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Start Lat</th>
                                        <th>Start Long</th>
                                        <th>End Lat</th>
                                        <th>End Long</th>
                                        <th>Raw Timestamp</th>
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

<!-- Add Call Log Modal -->
<div id="addCallLogModal" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Call Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addCallLogForm">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Name</label>
                                <input class="form-control" type="text" name="name" placeholder="e.g. Mummy">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>From Number <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="from" required placeholder="e.g. +917275143075">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>To Number <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="to" required placeholder="e.g. +917275143075">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="type" required>
                                    <option value="incoming">Incoming</option>
                                    <option value="outgoing">Outgoing</option>
                                    <option value="missed">Missed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" required>
                                    <option value="connected">Connected</option>
                                    <option value="no_answer">No Answer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Duration (Seconds)</label>
                                <input class="form-control" type="number" name="duration" id="duration_input" value="0">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Start Time</label>
                                <div class="input-group">
                                    <input class="form-control" type="datetime-local" name="start_time" id="start_time_input" onchange="calculateDuration()">
                                    <button class="btn btn-outline-secondary" type="button" onclick="setNow('start_time_input')">Now</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>End Time</label>
                                <div class="input-group">
                                    <input class="form-control" type="datetime-local" name="end_time" id="end_time_input" onchange="calculateDuration()">
                                    <button class="btn btn-outline-secondary" type="button" onclick="setNow('end_time_input')">Now</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Start Lat</label>
                                <input class="form-control" type="text" name="start_lat" placeholder="25.3586874">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Start Long</label>
                                <input class="form-control" type="text" name="start_long" placeholder="82.9986244">
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function setNow(inputId) {
        let now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById(inputId).value = now.toISOString().slice(0, 16);
        calculateDuration();
    }

    function calculateDuration() {
        let start = document.getElementById('start_time_input').value;
        let end = document.getElementById('end_time_input').value;
        if (start && end) {
            let diff = Math.round((new Date(end) - new Date(start)) / 1000);
            if (diff >= 0) {
                document.getElementById('duration_input').value = diff;
            }
        }
    }

    $(document).ready(function() {
        if ($('.select2').length > 0) {
            $('.select2').select2({
                width: '100%'
            });
        }

        $('#callLogsDataTable').DataTable({
            processing: true,
            serverSide: true,
            order: [[8, 'desc']], // Set default sort to Start Time (Desc)
            ajax: {
                url: "{{ route('call_logs.data', ['box_slug' => $box->slug]) }}",
                data: function(d) {
                    d.user_ids = $('#userFilter').val();
                    let dateRange = $('#dateRangeFilter').val();
                    if(dateRange) {
                        let dates = dateRange.split(' - ');
                        d.from_date = dates[0];
                        d.to_date = dates[1];
                    }
                    d.sort_order = $('#sortOrderFilter').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name', defaultContent: '-' },
                { data: 'creator_name', name: 'creator.name', defaultContent: '-' },
                { data: 'from', name: 'from', defaultContent: '-' },
                { data: 'to', name: 'to', defaultContent: '-' },
                { data: 'type', name: 'type', defaultContent: '-' },
                { data: 'status', name: 'status', defaultContent: '-' },
                { data: 'duration', name: 'duration', defaultContent: '-' },
                { data: 'start_time', name: 'start_time', defaultContent: '-' },
                { data: 'end_time', name: 'end_time', defaultContent: '-' },
                { data: 'start_lat', name: 'start_lat', defaultContent: '-' },
                { data: 'start_long', name: 'start_long', defaultContent: '-' },
                { data: 'end_lat', name: 'end_lat', defaultContent: '-' },
                { data: 'end_long', name: 'end_long', defaultContent: '-' },
                { data: 'rawTimestamp', name: 'rawTimestamp', defaultContent: '-' },
            ]
        });
        $('#addCallLogForm').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();
            $.ajax({
                url: "{{ route('call_logs.store', ['box_slug' => $box->slug]) }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    if(response.success) {
                        $('#addCallLogModal').modal('hide');
                        $('#addCallLogForm')[0].reset();
                        $('#callLogsDataTable').DataTable().ajax.reload();
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('Something went wrong. Please check your inputs.');
                }
            });
        });

        $('#filterBtn').on('click', function() {
            $('#callLogsDataTable').DataTable().ajax.reload();
        });

        $('.export-option').on('click', function(e) {
            e.preventDefault();
            let exportType = $(this).data('type');
            let userIds = $('#userFilter').val() || [];
            let sortOrder = $('#sortOrderFilter').val();
            let dateRange = $('#dateRangeFilter').val();
            
            let url = "{{ route('call_logs.export', ['box_slug' => $box->slug]) }}";
            let params = new URLSearchParams();
            
            params.append('export_type', exportType);

            if (userIds.length > 0) {
                userIds.forEach(id => params.append('user_ids[]', id));
            }
            if (dateRange) {
                let dates = dateRange.split(' - ');
                params.append('from_date', dates[0]);
                params.append('to_date', dates[1]);
            }
            if (sortOrder) params.append('sort_order', sortOrder);
            
            if (params.toString().length > 0) {
                url += '?' + params.toString();
            }
            window.location.href = url;
        });

        if ($('.custom-daterange').length > 0) {
            $('.custom-daterange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $('.custom-daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            });

            $('.custom-daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        }
    });
</script>
@endpush
