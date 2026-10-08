@extends('layouts.main')
@section('content')
@section('title', 'Mobile Device Register')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Mobile Device Register </h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="#"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Mobile Device Register </li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                <div class="mb-2">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#add_device"
                        class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add
                        Mobile Device </a>
                </div>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <!-- Device list -->
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                <h5>List Of Devices </h5>
            </div>
            <div class="card-body">
                <div class="custom-datatable-filter table-responsive">
                    <table class="table" id="device-table">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Device Name</th>
                                <th>Platform</th>
                                <th>OS Version</th>
                                <th>Brand</th>
                                <th>Model</th>
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
        <!-- /Device list -->

    </div>
</div>
<!-- /Page Wrapper -->

<!-- Add Device Modal -->
<div class="modal fade" id="add_device">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Mobile Device </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="addDeviceForm" method="POST">
                @csrf
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Device Name *</label>
                                <input type="text" class="form-control" name="device_name" required/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Platform *</label>
                                <input type="text" class="form-control" name="platform" required/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">OS Version</label>
                                <input type="text" class="form-control" name="os_version" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">App Version</label>
                                <input type="text" class="form-control" name="app_version" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Brand</label>
                                <input type="text" class="form-control" name="brand" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Model</label>
                                <input type="text" class="form-control" name="model" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status *</label>
                                <select class="form-select" name="status" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <!-- Extra technical fields collapsed or just below -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Device ID</label>
                                <input type="text" class="form-control" name="device_id" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Public Key</label>
                                <textarea class="form-control" name="public_key"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Key Algorithm</label>
                                <input type="text" class="form-control" name="key_algorithm" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Challenge ID</label>
                                <input type="text" class="form-control" name="challenge_id" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Signed Payload</label>
                                <textarea class="form-control" name="signed_payload"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Signature</label>
                                <textarea class="form-control" name="signature"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary me-2">Save </button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Pending Approve Modal -->
<div class="modal fade" id="pendingApproveModal" tabindex="-1" aria-labelledby="pendingApproveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pendingApproveModalLabel">Approve Device Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to approve this new device? It will be marked as Active.
                <input type="hidden" id="pending_approve_device_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirmPendingApproveBtn">Yes, Approve</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Activate Device</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to mark this device as Active?
                <input type="hidden" id="approve_device_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="confirmApproveBtn">Yes, Activate</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Revoke Modal -->
<div class="modal fade" id="revokeModal" tabindex="-1" aria-labelledby="revokeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="revokeModalLabel">Deactivate Device</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to mark this device as Inactive? The user will no longer be able to punch attendance from it.
                <input type="hidden" id="revoke_device_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" id="confirmRevokeBtn">Yes, Deactivate</button>
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
                <h5 class="modal-title" id="deleteModalLabel">Delete Device</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this device? This action cannot be undone.
                <input type="hidden" id="delete_device_id">
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

    var table = $('#device-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('device-register.index', ['box_slug' => request()->route('box_slug')]) }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'device_name', name: 'device_name'},
            {data: 'platform', name: 'platform'},
            {data: 'os_version', name: 'os_version'},
            {data: 'brand', name: 'brand'},
            {data: 'model', name: 'model'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    $('#addDeviceForm').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url: "{{ route('device-register.store', ['box_slug' => request()->route('box_slug')]) }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                $('#add_device').modal('hide');
                $('#addDeviceForm')[0].reset();
                table.ajax.reload();
                showToast(response.success);
            },
            error: function(error) {
                showToast('Something went wrong', 'error');
            }
        });
    });


    // Pending Approve Device logic
    $('body').on('click', '.pending-approve-btn', function () {
        var id = $(this).data("id");
        $('#pending_approve_device_id').val(id);
        $('#pendingApproveModal').modal('show');
    });

    $('#confirmPendingApproveBtn').on('click', function () {
        var id = $('#pending_approve_device_id').val();
        var approveUrl = "{{ route('device-register.approve', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}";
        approveUrl = approveUrl.replace(':id', id);
        
        $.ajax({
            type: "POST",
            url: approveUrl,
            data: {
                "_token": "{{ csrf_token() }}",
            },
            success: function (data) {
                $('#pendingApproveModal').modal('hide');
                table.ajax.reload();
                showToast(data.success);
            },
            error: function (data) {
                $('#pendingApproveModal').modal('hide');
                showToast('Something went wrong', 'error');
            }
        });
    });

    // Approve Device logic
    $('body').on('click', '.approve-device-btn', function () {
        var id = $(this).data("id");
        $('#approve_device_id').val(id);
        $('#approveModal').modal('show');
    });

    $('#confirmApproveBtn').on('click', function () {
        var id = $('#approve_device_id').val();
        var approveUrl = "{{ route('device-register.approve', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}";
        approveUrl = approveUrl.replace(':id', id);
        
        $.ajax({
            type: "POST",
            url: approveUrl,
            data: {
                "_token": "{{ csrf_token() }}",
            },
            success: function (data) {
                $('#approveModal').modal('hide');
                table.ajax.reload();
                showToast(data.success);
            },
            error: function (data) {
                $('#approveModal').modal('hide');
                showToast('Something went wrong', 'error');
            }
        });
    });

    // Revoke Device logic
    $('body').on('click', '.revoke-device-btn', function () {
        var id = $(this).data("id");
        $('#revoke_device_id').val(id);
        $('#revokeModal').modal('show');
    });

    $('#confirmRevokeBtn').on('click', function () {
        var id = $('#revoke_device_id').val();
        var revokeUrl = "{{ route('device-register.revoke', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}";
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

    // Delete Device logic
    $('body').on('click', '.delete-device', function () {
        var id = $(this).data("id");
        $('#delete_device_id').val(id);
        $('#deleteModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function () {
        var id = $('#delete_device_id').val();
        var deleteUrl = "{{ route('device-register.destroy', ['box_slug' => request()->route('box_slug'), 'id' => ':id']) }}";
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
