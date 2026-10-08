@extends('layouts.main')
@section('content')
@section('title', 'Mobile Attendance Punches')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Mobile Attendance Punches</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="#"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Mobile Punches</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                <h5>List Of Mobile Punches</h5>
            </div>
            <div class="card-body">
                <div class="custom-datatable-filter table-responsive">
                    <table class="table" id="punches-table">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Employee Name</th>
                                <th>Device Name</th>
                                <th>Punch Time</th>
                                <th>Action</th>
                                <th>Location (Lat/Lng)</th>
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
<!-- /Page Wrapper -->

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#punches-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('mobile-attendance.punches', ['box_slug' => request()->route('box_slug')]) }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'user_name', name: 'users.name'},
            {data: 'device_name', name: 'device_registers.device_name'},
            {
                data: 'punched_at', 
                name: 'attendance_punches.punched_at',
                render: function(data) {
                    if(!data) return '';
                    return moment(data).format('YYYY-MM-DD HH:mm:ss');
                }
            },
            {data: 'action', name: 'attendance_punches.action'},
            {
                data: null, 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    if(row.latitude && row.longitude) {
                        return row.latitude + ', ' + row.longitude;
                    }
                    return 'N/A';
                }
            },
        ]
    });
});
</script>
@endpush
@endsection
