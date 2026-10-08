
@extends('layouts.main')
@section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="d-md-flex align-items-center justify-content-between mb-3">
            <div class="my-auto mb-2">
                <h3 class="page-title mb-1">Assets</h3>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item active">Assets</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="campaign-tab">
            <ul class="nav">
                <li class=""><a class="active" href="#bottom-justified-tab2" data-bs-toggle="tab">Asset Types</a></li>
                <li class=""><a class="" href="#bottom-justified-tab3" data-bs-toggle="tab">Assets</a></li>
                <li class=""><a class="" href="#bottom-justified-tab1" data-bs-toggle="tab">Asset Management</a></li>
            </ul>
        </div>

        <div class="tab-content">
            <!-- Asset Management (History) -->
            <div class="tab-pane" id="bottom-justified-tab1">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-end mb-4">
                            <a href="javascript:void(0);" class="btn btn-primary" id="btn_open_assign_modal">
                                <i class="ti ti-square-rounded-plus me-2"></i>Assign Asset
                            </a>
                        </div>
                        <div class="table-responsive custom-table">
                            <table class="table" id="asset-history-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Asset</th>
                                        <th>Assigned To</th>
                                        <th>Assigned Date</th>
                                        <th>Returned Date</th>
                                        <th class="text-start">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignmentHistory as $log)
                                    <tr>
                                        <td>{{ $log->asset->asset_name ?? 'N/A' }} ({{ $log->asset->serial_number ?? 'N/A' }})</td>
                                        <td>{{ $log->boxUser->user->name ?? (($log->boxUser->first_name ?? '') . ' ' . ($log->boxUser->last_name ?? 'N/A')) }}</td>
                                        <td>{{ $log->assigned_date?->format('d-m-Y H:i:s') ?? 'N/A' }}</td>
                                        <td>
                                            @if($log->returned_date)
                                                <span class="text-success">{{ $log->returned_date->format('d-m-Y H:i:s') }}</span>
                                            @else
                                                <span class="badge bg-warning">Currently Assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="hstack gap-2 fs-15">
                                                <a class="btn btn-icon btn-sm btn-light edit-history" data-id="{{ $log->id }}" data-assigned_date="{{ $log->assigned_date?->format('Y-m-d') }}" data-returned_date="{{ $log->returned_date?->format('Y-m-d') }}" data-bs-toggle="modal" data-bs-target="#modal_edit_history" href="#"><i class="ti ti-edit text-blue"></i></a>
                                                <a class="btn btn-icon btn-sm btn-light delete-history" data-id="{{ $log->id }}" href="javascript:void(0);"><i class="ti ti-trash text-danger"></i></a>
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

            <!-- Asset Types -->
            <div class="tab-pane show active" id="bottom-justified-tab2">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-end mb-4">
                            <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_add_type">
                                <i class="ti ti-square-rounded-plus me-2"></i>Add Asset Type
                            </a>
                        </div>
                        <div class="table-responsive custom-table">
                            <table class="table" id="asset-types-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Asset Type</th>
                                        <th>Status</th>
                                        <th class="text-start">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assetTypes as $type)
                                    <tr>
                                        <td>{{ $type->id }}</td>
                                        <td>{{ $type->name }}</td>
                                        <td>
                                            <span class="badge badge-pill {{ $type->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                                {{ ucfirst($type->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="hstack gap-2 fs-15">
                                                <a class="btn btn-icon btn-sm btn-light edit-asset-type" data-id="{{ $type->id }}" data-name="{{ $type->name }}" data-status="{{ $type->status }}" data-bs-toggle="modal" data-bs-target="#modal_edit_type" href="#"><i class="ti ti-edit text-blue"></i></a>
                                                <a class="btn btn-icon btn-sm btn-light delete-asset-type" data-id="{{ $type->id }}" href="javascript:void(0);"><i class="ti ti-trash text-danger"></i></a>
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

            <!-- Assets -->
            <div class="tab-pane" id="bottom-justified-tab3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-end mb-4">
                            <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_add_asset">
                                <i class="ti ti-square-rounded-plus me-2"></i>Add Asset
                            </a>
                        </div>
                        <div class="table-responsive custom-table">
                            <table class="table" id="assets-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Device Type</th>
                                        <th>Device Name</th>
                                        <th>Sr. No.</th>
                                        <th>Assigned To</th>
                                        <th>Status</th>
                                        <th class="text-start">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assets as $asset)
                                    <tr>
                                        <td>{{ $asset->assetType->name ?? 'N/A' }}</td>
                                        <td>{{ $asset->asset_name }}</td>
                                        <td>{{ $asset->serial_number }}</td>
                                        <td>{{ $asset->assignedToBoxUser->user->name ?? (($asset->assignedToBoxUser->first_name ?? '') . ' ' . ($asset->assignedToBoxUser->last_name ?? 'Unassigned')) }}</td>
                                        <td>
                                            <span class="badge badge-pill {{ $asset->status == 'available' ? 'bg-success' : 'bg-warning' }}">
                                                {{ ucfirst($asset->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="hstack gap-2 fs-15">
                                                <a class="btn btn-icon btn-sm btn-light edit-asset" 
                                                   data-id="{{ $asset->id }}" 
                                                   data-asset_name="{{ $asset->asset_name }}" 
                                                   data-asset_type_id="{{ $asset->asset_type_id }}" 
                                                   data-serial_number="{{ $asset->serial_number }}"
                                                   data-brand="{{ $asset->brand }}"
                                                   data-model="{{ $asset->model }}"
                                                   data-vendor="{{ $asset->vendor }}"
                                                   data-purchase_date="{{ $asset->purchase_date?->format('Y-m-d') }}"
                                                   data-cost="{{ $asset->cost }}"
                                                   data-warranty_end_date="{{ $asset->warranty_end_date?->format('Y-m-d') }}"
                                                   data-description="{{ $asset->description }}"
                                                   data-assigned_to="{{ $asset->assigned_to }}"
                                                   data-assigned_date="{{ $asset->assigned_date?->format('Y-m-d') }}"
                                                   data-bs-toggle="modal" data-bs-target="#modal_edit_asset" href="#"><i class="ti ti-edit text-blue"></i></a>
                                                <a class="btn btn-icon btn-sm btn-light delete-asset" data-id="{{ $asset->id }}" href="javascript:void(0);"><i class="ti ti-trash text-danger"></i></a>
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
    </div>
</div>

<!-- Add Asset Type Modal -->
<div class="modal fade" id="modal_add_type" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Asset Type</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_asset_type_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="col-form-label">Type Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Laptop, Mobile" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="col-form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-light me-2">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Asset Type Modal -->
<div class="modal fade" id="modal_edit_type" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Asset Type</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit_asset_type_form">
                <input type="hidden" name="id" id="edit_type_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="col-form-label">Type Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edit_type_name" class="form-control" placeholder="e.g. Laptop, Mobile" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="col-form-label">Status</label>
                                <select name="status" id="edit_type_status" class="form-control">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-light me-2">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Asset Modal -->
<div class="modal fade" id="modal_add_asset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Asset</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_asset_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Asset Name <span class="text-danger">*</span></label>
                                <input type="text" name="asset_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Asset Type <span class="text-danger">*</span></label>
                                <select class="select form-control" name="asset_type_id" required>
                                    <option value="">Choose</option>
                                    @foreach($assetTypes as $at)
                                        <option value="{{ $at->id }}">{{ $at->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Brand</label>
                                <input type="text" name="brand" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Model</label>
                                <input type="text" name="model" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Serial No. <span class="text-danger">*</span></label>
                                <input type="text" name="serial_number" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Vendor</label>
                                <input type="text" name="vendor" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="col-form-label">Purchase Date</label>
                                <input type="date" name="purchase_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="col-form-label">Cost</label>
                                <input type="number" step="0.01" name="cost" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="col-form-label">Warranty End</label>
                                <input type="date" name="warranty_end_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="col-form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Assigned To</label>
                                <select class="select form-control" name="assigned_to">
                                    <option value="">Unassigned</option>
                                    @foreach($boxUsers as $bu)
                                        <option value="{{ $bu->id }}">{{ $bu->user->name ?? ($bu->first_name . ' ' . $bu->last_name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Assigned Date</label>
                                <input type="date" name="assigned_date" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-light me-2">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Asset Modal -->
<div class="modal fade" id="modal_edit_asset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Asset</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit_asset_form">
                <input type="hidden" name="id" id="edit_asset_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Asset Name <span class="text-danger">*</span></label>
                                <input type="text" name="asset_name" id="edit_asset_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Asset Type <span class="text-danger">*</span></label>
                                <select class="select form-control" name="asset_type_id" id="edit_asset_type_id" required>
                                    <option value="">Choose</option>
                                    @foreach($assetTypes as $at)
                                        <option value="{{ $at->id }}">{{ $at->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Brand</label>
                                <input type="text" name="brand" id="edit_brand" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Model</label>
                                <input type="text" name="model" id="edit_model" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Serial No. <span class="text-danger">*</span></label>
                                <input type="text" name="serial_number" id="edit_serial_number" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Vendor</label>
                                <input type="text" name="vendor" id="edit_vendor" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="col-form-label">Purchase Date</label>
                                <input type="date" name="purchase_date" id="edit_purchase_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="col-form-label">Cost</label>
                                <input type="number" step="0.01" name="cost" id="edit_cost" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="col-form-label">Warranty End</label>
                                <input type="date" name="warranty_end_date" id="edit_warranty_end_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="col-form-label">Description</label>
                                <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Assigned To</label>
                                <select class="select form-control" name="assigned_to" id="edit_assigned_to">
                                    <option value="">Unassigned</option>
                                    @foreach($boxUsers as $bu)
                                        <option value="{{ $bu->id }}">{{ $bu->user->name ?? ($bu->first_name . ' ' . $bu->last_name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Assigned Date</label>
                                <input type="date" name="assigned_date" id="edit_assigned_date" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-light me-2">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

    </div>
</div>

<!-- Assign Asset Modal -->
<div class="modal fade" id="modal_assign_asset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Quick Asset Assignment</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quick_assign_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="col-form-label">Available Asset <span class="text-danger">*</span></label>
                                <select class="select form-control" name="asset_id" id="available_asset_id" required>
                                    <option value="">Loading available assets...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="col-form-label">Assign To Employee <span class="text-danger">*</span></label>
                                <select class="select form-control" name="box_user_id" required>
                                    <option value="">Choose Employee</option>
                                    @foreach($boxUsers as $bu)
                                        <option value="{{ $bu->id }}">{{ $bu->user->name ?? ($bu->first_name . ' ' . $bu->last_name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="col-form-label">Assigned Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="assigned_date" class="form-control" value="{{ date('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-light me-2">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

    </div>
</div>

<!-- Edit History Modal -->
<div class="modal fade" id="modal_edit_history" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Assignment Log</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit_history_form">
                <input type="hidden" name="id" id="edit_history_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Assigned Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="assigned_date" id="edit_history_assigned_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="col-form-label">Returned Date</label>
                                <input type="datetime-local" name="returned_date" id="edit_history_returned_date" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-light me-2">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Log</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="delete_modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3 border p-3 d-inline-flex align-items-center justify-content-center">
                        <i class="ti ti-trash-x fs-36 text-danger"></i>
                    </div>
                    <h4 class="mb-2">Remove Record?</h4>
                    <p class="mb-0">Are you sure you want to remove the record you selected?</p>
                    <div class="d-flex align-items-center justify-content-center mt-4">
                        <button class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button id="confirm-delete-btn" class="btn btn-danger">Yes, Delete it</button>
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function loadAssetTypes() {
        $.ajax({
            url: "{{ route('hr.asset.type.list', ['box_slug' => request()->segment(1)]) }}",
            type: "GET",
            success: function(response) {
                if(response.success) {
                    var html = '';
                    var options = '<option value="">Choose</option>';
                    $.each(response.data, function(i, type) {
                        var statusBadge = type.status == 'active' ? '<span class="badge badge-pill bg-success">Active</span>' : '<span class="badge badge-pill bg-danger">Inactive</span>';
                        html += '<tr>' +
                            '<td>' + type.id + '</td>' +
                            '<td>' + type.name + '</td>' +
                            '<td>' + statusBadge + '</td>' +
                            '<td>' +
                                '<div class="hstack gap-2 fs-15">' +
                                    '<a class="btn btn-icon btn-sm btn-light edit-asset-type" data-id="' + type.id + '" data-name="' + type.name + '" data-status="' + type.status + '" data-bs-toggle="modal" data-bs-target="#modal_edit_type" href="#"><i class="ti ti-edit text-blue"></i></a>' +
                                    '<a class="btn btn-icon btn-sm btn-light delete-asset-type" data-id="' + type.id + '" href="javascript:void(0);"><i class="ti ti-trash text-danger"></i></a>' +
                                '</div>' +
                            '</td>' +
                        '</tr>';
                        options += '<option value="' + type.id + '">' + type.name + '</option>';
                    });
                    $('#asset-types-table tbody').html(html);
                    $('select[name="asset_type_id"]').html(options);
                }
            }
        });
    }

    function loadAssets() {
        $.ajax({
            url: "{{ route('hr.asset.list', ['box_slug' => request()->segment(1)]) }}",
            type: "GET",
            success: function(response) {
                if(response.success) {
                    var html = '';
                    $.each(response.data, function(i, asset) {
                        var typeName = asset.asset_type ? asset.asset_type.name : 'N/A';
                        var assignedUser = 'Unassigned';
                        if (asset.assigned_to_box_user) {
                            assignedUser = asset.assigned_to_box_user.user ? asset.assigned_to_box_user.user.name : (asset.assigned_to_box_user.first_name + ' ' + asset.assigned_to_box_user.last_name);
                        }
                        var statusClass = asset.status == 'available' ? 'bg-success' : 'bg-warning';
                        html += '<tr>' +
                            '<td>' + typeName + '</td>' +
                            '<td>' + asset.asset_name + '</td>' +
                            '<td>' + asset.serial_number + '</td>' +
                            '<td>' + assignedUser + '</td>' +
                            '<td>' +
                                '<span class=\"badge badge-pill ' + statusClass + '\">' + (asset.status.charAt(0).toUpperCase() + asset.status.slice(1)) + '</span>' +
                            '</td>' +
                            '<td>' +
                                '<div class=\"hstack gap-2 fs-15\">' +
                                    '<a class=\"btn btn-icon btn-sm btn-light edit-asset\" data-id=\"' + asset.id + '\" data-asset_name=\"' + asset.asset_name + '\" data-asset_type_id=\"' + asset.asset_type_id + '\" data-serial_number=\"' + asset.serial_number + '\" data-brand=\"' + (asset.brand || '') + '\" data-model=\"' + (asset.model || '') + '\" data-vendor=\"' + (asset.vendor || '') + '\" data-purchase_date=\"' + (asset.purchase_date || '') + '\" data-cost=\"' + (asset.cost || '') + '\" data-warranty_end_date=\"' + (asset.warranty_end_date || '') + '\" data-description=\"' + (asset.description || '') + '\" data-assigned_to=\"' + (asset.assigned_to || '') + '\" data-assigned_date=\"' + (asset.assigned_date || '') + '\" data-bs-toggle=\"modal\" data-bs-target=\"#modal_edit_asset\" href=\"#\"><i class=\"ti ti-edit text-blue\"></i></a>' +
                                    '<a class=\"btn btn-icon btn-sm btn-light delete-asset\" data-id=\"' + asset.id + '\" href=\"javascript:void(0);\"><i class=\"ti ti-trash text-danger\"></i></a>' +
                                '</div>' +
                            '</td>' +
                        '</tr>';
                    });
                    $('#assets-table tbody').html(html);
                }
            }
        });
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        var d = new Date(dateString);
        var day = String(d.getDate()).padStart(2, '0');
        var month = String(d.getMonth() + 1).padStart(2, '0');
        var year = d.getFullYear();
        var hours = String(d.getHours()).padStart(2, '0');
        var minutes = String(d.getMinutes()).padStart(2, '0');
        var seconds = String(d.getSeconds()).padStart(2, '0');
        return day + '-' + month + '-' + year + ' ' + hours + ':' + minutes + ':' + seconds;
    }

    function loadHistory() {
        $.ajax({
            url: "{{ route('hr.asset.history', ['box_slug' => request()->segment(1)]) }}",
            type: "GET",
            success: function(response) {
                if(response.success) {
                    var html = '';
                    $.each(response.data, function(i, log) {
                        var assetName = log.asset ? log.asset.asset_name : 'N/A';
                        var serial = log.asset ? ' (' + log.asset.serial_number + ')' : '';
                        var userName = 'N/A';
                        if (log.box_user) {
                            userName = log.box_user.user ? log.box_user.user.name : (log.box_user.first_name + ' ' + log.box_user.last_name);
                        }
                        var assignedDate = formatDate(log.assigned_date);
                        var returnDate = log.returned_date ? '<span class="text-success">' + formatDate(log.returned_date) + '</span>' : '<span class="badge bg-warning">Currently Assigned</span>';
                        html += '<tr>' +
                            '<td>' + assetName + serial + '</td>' +
                            '<td>' + userName + '</td>' +
                            '<td>' + assignedDate + '</td>' +
                            '<td>' + returnDate + '</td>' +
                            '<td>' +
                                '<div class=\"hstack gap-2 fs-15\">' +
                                    '<a class=\"btn btn-icon btn-sm btn-light edit-history\" data-id=\"' + log.id + '\" data-assigned_date=\"' + (log.assigned_date || '') + '\" data-returned_date=\"' + (log.returned_date || '') + '\" data-bs-toggle=\"modal\" data-bs-target=\"#modal_edit_history\" href=\"#\"><i class=\"ti ti-edit text-blue\"></i></a>' +
                                    '<a class=\"btn btn-icon btn-sm btn-light delete-history\" data-id=\"' + log.id + '\" href=\"javascript:void(0);\"><i class=\"ti ti-trash text-danger\"></i></a>' +
                                '</div>' +
                            '</td>' +
                        '</tr>';
                    });
                    $('#asset-history-table tbody').html(html);
                }
            }
        });
    }

    // Add Asset Type
    $('#add_asset_type_form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "{{ route('hr.asset.type.store', ['box_slug' => request()->segment(1)]) }}",
            type: "POST",
            data: formData,
            success: function(response) {
                if(response.success) {
                    showToast(response.message, 'success');
                    $('#modal_add_type').modal('hide');
                    $('#add_asset_type_form')[0].reset();
                    loadAssetTypes();
                    loadHistory();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                var res = xhr.responseJSON;
                showToast(res && res.message ? res.message : 'Failed to add Asset Type.', 'error');
            }
        });
    });

    // Populate Edit Modal
    $(document).on('click', '.edit-asset-type', function() {
        $('#edit_type_id').val($(this).data('id'));
        $('#edit_type_name').val($(this).data('name'));
        $('#edit_type_status').val($(this).data('status'));
    });

    // Update Asset Type
    $('#edit_asset_type_form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "{{ route('hr.asset.type.update', ['box_slug' => request()->segment(1)]) }}",
            type: "PUT",
            data: formData,
            success: function(response) {
                if(response.success) {
                    showToast(response.message, 'success');
                    $('#modal_edit_type').modal('hide');
                    loadAssetTypes();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                showToast('Failed to update Asset Type.', 'error');
            }
        });
    });

    // Add Asset
    $('#add_asset_form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "{{ route('hr.asset.store', ['box_slug' => request()->segment(1)]) }}",
            type: "POST",
            data: formData,
            success: function(response) {
                if(response.success) {
                    showToast(response.message, 'success');
                    $('#modal_add_asset').modal('hide');
                    $('#add_asset_form')[0].reset();
                    loadAssets();
                    loadHistory();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                showToast('Failed to add Asset.', 'error');
            }
        });
    });

    // Populate Edit Asset Modal
    
    $(document).on('click', '.edit-asset', function() {
        $('#edit_asset_id').val($(this).data('id'));
        $('#edit_asset_name').val($(this).data('asset_name'));
        $('#edit_asset_type_id').val($(this).data('asset_type_id'));
        $('#edit_serial_number').val($(this).data('serial_number'));
        $('#edit_brand').val($(this).data('brand'));
        $('#edit_model').val($(this).data('model'));
        $('#edit_vendor').val($(this).data('vendor'));
        $('#edit_purchase_date').val($(this).data('purchase_date'));
        $('#edit_cost').val($(this).data('cost'));
        $('#edit_warranty_end_date').val($(this).data('warranty_end_date'));
        $('#edit_description').val($(this).data('description'));
        $('#edit_assigned_to').val($(this).data('assigned_to'));
        $('#edit_assigned_date').val($(this).data('assigned_date'));
    });


    // Update Asset
    $('#edit_asset_form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "{{ route('hr.asset.update', ['box_slug' => request()->segment(1)]) }}",
            type: "PUT",
            data: formData,
            success: function(response) {
                if(response.success) {
                    showToast(response.message, 'success');
                    $('#modal_edit_asset').modal('hide');
                    loadAssets();
                    loadHistory();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                showToast('Failed to update Asset.', 'error');
            }
        });
    });

    $(document).on('click', '#btn_open_assign_modal', function() {
        $.ajax({
            url: "{{ route('hr.asset.available', ['box_slug' => request()->segment(1)]) }}",
            type: "GET",
            success: function(response) {
                if(response.success) {
                    var options = '<option value="">Choose Available Asset</option>';
                    $.each(response.data, function(i, asset) {
                        options += '<option value="' + asset.id + '">' + asset.asset_name + ' (' + asset.serial_number + ')</option>';
                    });
                    $('#available_asset_id').html(options);
                    $('#modal_assign_asset').modal('show');
                }
            }
        });
    });

    $('#quick_assign_form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "{{ route('hr.asset.assign', ['box_slug' => request()->segment(1)]) }}",
            type: "POST",
            data: formData,
            success: function(response) {
                if(response.success) {
                    showToast(response.message, 'success');
                    $('#modal_assign_asset').modal('hide');
                    $('#quick_assign_form')[0].reset();
                    loadAssets();
                    loadHistory();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                var res = xhr.responseJSON;
                showToast(res && res.message ? res.message : 'Failed to assign Asset.', 'error');
            }
        });
    });

    var deleteId = null;
    var deleteType = null;

    // Delete Asset Type Click
    $(document).on('click', '.delete-asset-type', function() {
        deleteId = $(this).data('id');
        deleteType = 'type';
        $('#delete_modal').modal('show');
    });

    // Delete Asset Click
    $(document).on('click', '.delete-asset', function() {
        deleteId = $(this).data('id');
        deleteType = 'asset';
        $('#delete_modal').modal('show');
    });

    $(document).on('click', '.edit-history', function() {
        $('#edit_history_id').val($(this).data('id'));
        $('#edit_history_assigned_date').val($(this).data('assigned_date') ? $(this).data('assigned_date').slice(0, 16) : '');
        $('#edit_history_returned_date').val($(this).data('returned_date') ? $(this).data('returned_date').slice(0, 16) : '');
    });

    $('#edit_history_form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "{{ route('hr.asset.history.update', ['box_slug' => request()->segment(1)]) }}",
            type: "PUT",
            data: formData,
            success: function(response) {
                if(response.success) {
                    showToast(response.message, 'success');
                    $('#modal_edit_history').modal('hide');
                    loadHistory();
                } else {
                    showToast(response.message, 'error');
                }
            }
        });
    });

    $(document).on('click', '.delete-history', function() {
        deleteId = $(this).data('id');
        deleteType = 'history';
        $('#delete_modal').modal('show');
    });

    // Confirm Delete
    $('#confirm-delete-btn').on('click', function() {
        var url = '';
        var data = { id: deleteId };
        var method = 'DELETE';

        if(deleteType == 'type') {
            url = "{{ route('hr.asset.type.delete', ['box_slug' => request()->segment(1)]) }}";
        } else if(deleteType == 'asset') {
            url = "{{ route('hr.asset.delete', ['box_slug' => request()->segment(1)]) }}";
        } else if(deleteType == 'history') {
            url = "{{ route('hr.asset.history.delete', ['box_slug' => request()->segment(1)]) }}";
        }

        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function(response) {
                if(response.success) {
                    showToast(response.message, 'success');
                    $('#delete_modal').modal('hide');
                    if(deleteType == 'type') loadAssetTypes();
                    else if(deleteType == 'asset') loadAssets();
                    else if(deleteType == 'history') loadHistory();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                showToast('Failed to delete record.', 'error');
            }
        });
    });
});
</script>
@endpush


