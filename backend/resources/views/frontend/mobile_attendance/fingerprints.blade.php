@extends('layouts.main')
@section('content')
@section('title', 'Registered Fingerprints')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Registered Fingerprints</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="#"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Registered Fingerprints</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                <h5>List Of Registered Fingerprints</h5>
            </div>
            <div class="card-body">
                <div class="custom-datatable-filter table-responsive">
                    <table class="table" id="fingerprints-table">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Employee Name</th>
                                <th>Device Name</th>
                                <th>Device ID</th>
                                <th>Status</th>
                                <th>Enrolled At</th>
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
<!-- /Page Wrapper -->

<!-- Revoke Modal -->
<div class="modal fade" id="revokeModal" tabindex="-1" aria-labelledby="revokeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="revokeModalLabel">Revoke Fingerprint</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to revoke this fingerprint? The user will have to enroll again to punch from this device.
                <input type="hidden" id="revoke_fingerprint_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" id="confirmRevokeBtn">Yes, Revoke</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Fingerprint</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to completely delete this fingerprint record? This action cannot be undone.
                <input type="hidden" id="delete_fingerprint_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div id="dynamicToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="dynamicToastMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    function showToast(message, type = 'success') {
        const $toast = $('#dynamicToast');
        const $message = $('#dynamicToastMessage');

        $message.text(message);
        $toast.removeClass('bg-success bg-danger bg-warning')
            .addClass(
                type === 'success' ? 'bg-success' :
                type === 'error' ? 'bg-danger' : 'bg-warning'
            );

        new bootstrap.Toast($toast[0]).show();
    }

    var table = $('#fingerprints-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('mobile-attendance.fingerprints', ['box_slug' => request()->route('box_slug')]) }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'user_name', name: 'users.name'},
            {data: 'device_name', name: 'device_registers.device_name'},
            {data: 'device_id', name: 'attendance_fingerprints.device_id'},
            {data: 'status', name: 'attendance_fingerprints.status'},
            {
                data: 'enrolled_at', 
                name: 'attendance_fingerprints.enrolled_at',
                render: function(data) {
                    if(!data) return '';
                    return moment(data).format('YYYY-MM-DD HH:mm:ss');
                }
            },
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    // Revoke Logic
    $('body').on('click', '.revoke-fingerprint-btn', function () {
        var id = $(this).data("id");
        $('#revoke_fingerprint_id').val(id);
        $('#revokeModal').modal('show');
    });

    $('#confirmRevokeBtn').on('click', function () {
        var id = $('#revoke_fingerprint_id').val();
        var revokeUrl = "{{ route('mobile-attendance.fingerprints.revoke', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}";
        revokeUrl = revokeUrl.replace(':id', id);
        
        $.ajax({
            type: "POST",
            url: revokeUrl,
            data: {
                "_token": "{{ csrf_token() }}",
            },
            success: function (data) {
                $('#revokeModal').modal('hide');
                table.ajax.reload();
                showToast(data.success);
            },
            error: function (data) {
                $('#revokeModal').modal('hide');
                showToast('Something went wrong', 'error');
            }
        });
    });

    // Delete Logic
    $('body').on('click', '.delete-fingerprint-btn', function () {
        var id = $(this).data("id");
        $('#delete_fingerprint_id').val(id);
        $('#deleteModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function () {
        var id = $('#delete_fingerprint_id').val();
        var deleteUrl = "{{ route('mobile-attendance.fingerprints.destroy', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}";
        deleteUrl = deleteUrl.replace(':id', id);
        
        $.ajax({
            type: "DELETE",
            url: deleteUrl,
            data: {
                "_token": "{{ csrf_token() }}",
            },
            success: function (data) {
                $('#deleteModal').modal('hide');
                table.ajax.reload();
                showToast(data.success);
            },
            error: function (data) {
                $('#deleteModal').modal('hide');
                showToast('Something went wrong', 'error');
            }
        });
    });
});
</script>
@endpush
@endsection
