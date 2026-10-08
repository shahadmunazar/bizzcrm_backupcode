@extends('layouts.main')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Package Features</h4>
                        <h6>Manage master list of features for subscription plans</h6>
                    </div>
                </div>
                <ul class="table-top-head">
                    <li>
                        <a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh" id="refresh-features"><i class="ti ti-refresh-dot"></i></a>
                    </li>
                    <li>
                        <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
                    </li>
                </ul>
                <div class="page-btn d-flex align-items-center">
                    <select id="filter_feature_type" class="form-select me-3" style="width: 200px;">
                        <option value="">All Features</option>
                        <option value="CRM">CRM Features</option>
                        <option value="WhatsApp">WhatsApp Features</option>
                    </select>
                    <a href="javascript:void(0);" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#modal_add_feature"><i class="ti ti-square-rounded-plus me-2"></i>Add New Feature</a>
                </div>
            </div>

            <!-- Features list -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable" id="features-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Feature Name</th>
                                    <th>Slug</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Type</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody id="features-table-body">
                                @foreach($features as $feature)
                                <tr>
                                    <td><h6 class="fw-medium">{{ $feature->name }}</h6></td>
                                    <td>{{ $feature->slug }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input toggle-status" type="checkbox" data-id="{{ $feature->id }}" {{ $feature->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>{{ $feature->created_at->format('d M Y') }}</td>
                                    <td>
                                        @if($feature->feature_type === 'whatsapp')
                                            <span class="badge bg-soft-success text-success"><i class="fa fa-whatsapp"></i> WhatsApp</span>
                                        @else
                                            <span class="badge bg-soft-primary text-primary">CRM</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown table-action">
                                            <a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item edit-feature-btn" href="javascript:void(0);" data-id="{{ $feature->id }}" data-name="{{ $feature->name }}" data-status="{{ $feature->is_active }}">
                                                    <i class="ti ti-edit text-blue me-1"></i> Edit
                                                </a>
                                                <a class="dropdown-item delete-feature-btn text-danger" href="javascript:void(0);" data-id="{{ $feature->id }}">
                                                    <i class="ti ti-trash me-1"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Feature Modal -->
    <div class="modal fade" id="modal_add_feature" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Feature</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add_feature_form">
                        <div class="mb-3">
                            <label class="form-label">Feature Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required placeholder="e.g. AI Powered Chat">
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Feature</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Feature Modal -->
    <div class="modal fade" id="modal_edit_feature" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Feature</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit_feature_form">
                        <input type="hidden" id="edit_feature_id">
                        <div class="mb-3">
                            <label class="form-label">Feature Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_feature_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-control" id="edit_feature_status" name="is_active" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Feature</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Feature Confirmation Modal -->
    <div class="modal fade" id="delete_feature_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center">
                        <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                            <i class="ti ti-trash-x fs-36 text-danger"></i>
                        </div>
                        <h4 class="mb-2">Remove Feature?</h4>
                        <p class="mb-0">Are you sure you want to remove the feature <br> you selected?</p>
                        <div class="d-flex align-items-center justify-content-center mt-4">
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="confirm_delete_feature" class="btn btn-danger">Yes, Delete it</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 11000;">
        <div id="toastMessage" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastBody"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    $(document).ready(function() {
        // CSRF Token Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Toast Helpers
        function showToast(message, type = 'success') {
            const $toast = $('#toastMessage');
            const $toastBody = $('#toastBody');
            $toast.removeClass('bg-danger bg-success bg-warning').addClass('bg-' + type);
            $toastBody.html(message);
            const toast = new bootstrap.Toast($toast[0]);
            toast.show();
        }

        // Filter Feature Type
        let currentType = "{{ request()->get('type') }}";
        if (currentType === 'whatsapp') {
            $('#filter_feature_type').val('WhatsApp');
        } else if (currentType === 'crm') {
            $('#filter_feature_type').val('CRM');
        } else {
            $('#filter_feature_type').val('');
        }
        
        $('#filter_feature_type').on('change', function() {
            let type = $(this).val();
            let url = new URL(window.location.href);
            if(type) {
                url.searchParams.set('type', type.toLowerCase());
            } else {
                url.searchParams.delete('type');
            }
            window.location.href = url.toString();
        });

        // Add Feature
        $('#add_feature_form').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ box_route('superadmin.package-features.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        showToast(response.message);
                        $('#modal_add_feature').modal('hide');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Error adding feature', 'danger');
                }
            });
        });

        // Edit Feature Click
        $(document).on('click', '.edit-feature-btn', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let status = $(this).data('status');
            $('#edit_feature_id').val(id);
            $('#edit_feature_name').val(name);
            $('#edit_feature_status').val(status);
            $('#modal_edit_feature').modal('show');
        });

        // Update Feature
        $('#edit_feature_form').on('submit', function(e) {
            e.preventDefault();
            let id = $('#edit_feature_id').val();
            let url = "{{ box_route('superadmin.package-features.update', ['id' => ':id']) }}".replace(':id', id);
            $.ajax({
                url: url,
                type: 'PUT',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        showToast(response.message);
                        $('#modal_edit_feature').modal('hide');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Error updating feature', 'danger');
                }
            });
        });

        // Delete Feature (Open Modal)
        let featureIdToDelete = null;
        $(document).on('click', '.delete-feature-btn', function() {
            featureIdToDelete = $(this).data('id');
            $('#delete_feature_modal').modal('show');
        });

        // Confirm Delete Feature
        $('#confirm_delete_feature').on('click', function() {
            if (!featureIdToDelete) return;
            
            let url = "{{ box_route('superadmin.package-features.destroy', ['id' => ':id']) }}".replace(':id', featureIdToDelete);
            $.ajax({
                url: url,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        $('#delete_feature_modal').modal('hide');
                        showToast(response.message);
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function() {
                    $('#delete_feature_modal').modal('hide');
                    showToast('Error deleting feature', 'danger');
                }
            });
        });

        // Toggle Status
        $(document).on('change', '.toggle-status', function() {
            let id = $(this).data('id');
            let url = "{{ box_route('superadmin.package-features.toggle-status', ['id' => ':id']) }}".replace(':id', id);
            $.ajax({
                url: url,
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        showToast(response.message);
                    }
                },
                error: function() {
                    showToast('Error updating status', 'danger');
                }
            });
        });

        $('#refresh-features').on('click', function() {
            location.reload();
        });
    });
</script>
@endpush
@endsection
