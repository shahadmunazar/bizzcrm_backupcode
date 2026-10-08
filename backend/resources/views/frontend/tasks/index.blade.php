@extends('layouts.main')
@section('content')
@section('title', 'Tasks')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content">

            <div class="row">
                <div class="col-md-12">
                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col-4">
                                <h4 class="page-title">
                                    <a href="javascript:history.back()" class="me-2 text-muted"><i
                                            class="ti ti-arrow-narrow-left"></i></a>
                                    My Activities / Follow Ups
                                </h4>
                            </div>
                        </div>
                    </div>
                    <!-- /Page Header -->

                    <div class="card">
                        <div class="card-header border-bottom-0 pb-0">
                            <ul class="nav nav-tabs nav-tabs-bottom">
                                <li class="nav-item"><a class="nav-link active filter-tab" href="javascript:void(0);" data-filter="today">Today</a></li>
                                <li class="nav-item"><a class="nav-link filter-tab" href="javascript:void(0);" data-filter="tomorrow">Tomorrow</a></li>
                                <li class="nav-item"><a class="nav-link filter-tab" href="javascript:void(0);" data-filter="this_week">This Week</a></li>
                                <li class="nav-item"><a class="nav-link filter-tab" href="javascript:void(0);" data-filter="this_month">This Month</a></li>
                                <li class="nav-item"><a class="nav-link filter-tab" href="javascript:void(0);" data-filter="upcoming">Upcoming</a></li>
                                <li class="nav-item"><a class="nav-link filter-tab" href="javascript:void(0);" data-filter="pending">Pending / Overdue</a></li>
                                <li class="nav-item"><a class="nav-link filter-tab" href="javascript:void(0);" data-filter="completed">Completed</a></li>
                                <li class="nav-item"><a class="nav-link filter-tab" href="javascript:void(0);" data-filter="all">All Open</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="tasks-table">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th>Type</th>
                                            <th>Lead Name</th>
                                            <th>Assigned To</th>
                                            <th>Date & Time</th>
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
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var currentFilter = 'today';

        $('.filter-tab').on('click', function(e) {
            e.preventDefault();
            $('.filter-tab').removeClass('active');
            $(this).addClass('active');
            currentFilter = $(this).data('filter');
            tasksTable.ajax.reload();
        });

        var tasksTable = $('#tasks-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ box_route('get.tasks') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.filter = currentFilter;
                }
            },
            columns: [
                { data: 'subject', name: 'subject', defaultContent: '--' },
                { data: 'type', name: 'type', render: function(data, type, row) {
                    if (data) return data.charAt(0).toUpperCase() + data.slice(1);
                    return '--';
                } },
                { data: 'lead_name', name: 'lead_name', orderable: false, searchable: false },
                { data: 'assigned_to_name', name: 'assigned_to_name', orderable: false, searchable: false },
                { data: 'activity_date_formatted', name: 'activity_date_formatted', orderable: false, searchable: false },
                { data: 'status', name: 'status', render: function(data, type, row) {
                    let badgeClass = 'badge-soft-primary';
                    if (data === 'Completed') badgeClass = 'badge-soft-success';
                    else if (data === 'Open' || data === 'Pending') badgeClass = 'badge-soft-warning';
                    return '<span class="badge ' + badgeClass + '">' + (data || 'Pending') + '</span>';
                } },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endpush
