@extends('layouts.main')
@section('content')
@section('title','Activity Settings')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content">

            <div class="row">
                    <!-- Page Header -->
                <div class="col-md-12">
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col-sm-8">
                                <h4 class="page-title">Activity Settings <span class="count-title" id="calls-id">123</span></h4>
                            </div>
                            <div class="col-sm-4 text-sm-end">
                                <div class="head-icons">
                                    <a href="javascript:void(0);" onclick="location.reload();" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
                                    <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-original-title="Collapse" id="collapse-header"><i
                                            class="ti ti-chevrons-up"></i></a>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Page Header -->

                <!-- Section 1: Activity Settings -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <!-- Search -->
                            <div class="row align-items-center">
                                <div class="col-sm-4">
                                    <div class="icon-form mb-3 mb-sm-0">
                                        <span class="form-icon"><i class="ti ti-search"></i></span>
                                        <input type="text" class="form-control" id="callsSearchInput"
                                            placeholder="Search Activity">
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="text-sm-end">
                                        <button type="button" class="btn btn-danger me-2" id="delete-selected-calls" style="display: none;"><i class="ti ti-trash me-2"></i>Delete Selected</button>
                                        <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#add_calls"><i class="ti ti-square-rounded-plus me-2"></i>Add New Activity</a>
                                    </div>
                                </div>
                            </div>
                            <!-- /Search -->
                        </div>
                        <div class="card-body">
                            <!-- Calls List -->
                            <div class="table-responsive custom-table">
                                <table class="table" id="calls-list">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="no-sort" width="30">
                                                <div class="form-check">
                                                    <input class="form-check-input check-all-calls" type="checkbox">
                                                </div>
                                            </th>
                                            <th>Title</th>
                                            <th>Created at</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="datatable-length"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="datatable-paginate"></div>
                                </div>
                            </div>
                            <!-- /Calls List -->
                        </div>
                    </div>
                </div>

                <!-- Section 2: Activity Types -->
                <div class="col-md-12" id="activity-types-section" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center mb-2">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Activity Types</h5>
                                        <button type="button" class="btn btn-sm btn-light" id="hide-activity-types-btn" title="Hide Activity Types">
                                            <i class="ti ti-chevron-up"></i> Hide
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Search -->
                            <div class="row align-items-center">
                                <div class="col-sm-4">
                                    <div class="icon-form mb-3 mb-sm-0">
                                        <span class="form-icon"><i class="ti ti-search"></i></span>
                                        <input type="text" class="form-control" id="activityTypeSearchInput"
                                            placeholder="Search Activity Type">
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="text-sm-end">
                                        <button type="button" class="btn btn-danger me-2" id="delete-selected-activity-type" style="display: none;"><i class="ti ti-trash me-2"></i>Delete Selected</button>
                                        <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#add_activity_type"><i class="ti ti-square-rounded-plus me-2"></i>Add New Activity Type</a>
                                    </div>
                                </div>
                            </div>
                            <!-- /Search -->
                        </div>
                        <div class="card-body">
                            <div class="table-responsive custom-table">
                                <table class="table" id="activity-list-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="no-sort" width="30">
                                                <div class="form-check">
                                                    <input class="form-check-input check-all-types" type="checkbox">
                                                </div>
                                            </th>
                                            <th>Activity Type</th>
                                            <th>Activity Setting</th>
                                            <th>Target Type</th>
                                            <th>Created at</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="datatable-length-type"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="datatable-paginate-type"></div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>

                <!-- Section 3: Activity Statuses -->
                <div class="col-md-12" id="activity-statuses-section" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center mb-2">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Activity Statuses</h5>
                                        <button type="button" class="btn btn-sm btn-light" id="hide-activity-statuses-btn" title="Hide Activity Statuses">
                                            <i class="ti ti-chevron-up"></i> Hide
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Search -->
                            <div class="row align-items-center">
                                <div class="col-sm-4">
                                    <div class="icon-form mb-3 mb-sm-0">
                                        <span class="form-icon"><i class="ti ti-search"></i></span>
                                        <input type="text" class="form-control" id="activityStatusSearchInput"
                                            placeholder="Search Activity Status">
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="text-sm-end">
                                        <button type="button" class="btn btn-danger me-2" id="delete-selected-activity-status" style="display: none;"><i class="ti ti-trash me-2"></i>Delete Selected</button>
                                        <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#add_activity_status"><i class="ti ti-square-rounded-plus me-2"></i>Add New Activity Status</a>
                                    </div>
                                </div>
                            </div>
                            <!-- /Search -->
                        </div>
                        <div class="card-body">
                            <div class="table-responsive custom-table">
                                <table class="table" id="activity-status-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="no-sort" width="30">
                                                <div class="form-check">
                                                    <input class="form-check-input check-all-statuses" type="checkbox">
                                                </div>
                                            </th>
                                            <th>Activity Status</th>
                                            <th>Activity Setting</th>
                                            <th>Activity Type</th>
                                            <th>Created at</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="datatable-length-status"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="datatable-paginate-status"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /Page Wrapper -->

    <!-- Add New Calls -->
    <div class="modal fade" id="add_calls" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Activity</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="add_calls_form" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="col-form-label">Activity Name (Type and press Enter) <span class="text-danger">*</span></label>
                            <div class="tag-input-container form-control" style="min-height: 80px; height: auto; cursor: text;">
                                <div class="d-flex flex-wrap gap-1 tags-container" id="activity-tags-container"></div>
                                <input type="text" class="border-0 bg-transparent py-1 w-100 tag-input-field" placeholder="Type activity name..." style="outline: none; box-shadow: none;">
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="col-form-label">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <input type="radio" class="status-radio" id="add-active" name="status"
                                        checked="">
                                    <label for="add-active">Active</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="add-inactive" name="status">
                                    <label for="add-inactive">Inactive</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex align-items-center justify-content-end m-0">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add New Calls -->

    <!-- Edit Calls -->
    <div class="modal fade" id="edit_calls" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Activity</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="editContactStageForm" method="POST">
                    <input type="hidden" id="editContactStageId" name="id" />
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="col-form-label">Activity Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editContactStageName" name="name"
                                value="">
                        </div>
                        <div class="mb-0">
                            <label class="col-form-label">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <input type="radio" class="status-radio" id="edit-active" name="status"
                                        value="1">
                                    <label for="edit-active">Active</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="edit-inactive" name="status"
                                        value="0">
                                    <label for="edit-inactive">Inactive</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex align-items-center justify-content-end m-0">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- /Edit Calls -->

    <!-- Delete Calls -->
    <div class="modal fade" id="delete_calls" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <input type="hidden" id="deleteCallReasonId">
                <div class="modal-body">
                    <div class="text-center">
                        <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                            <i class="ti ti-trash-x fs-36 text-danger"></i>
                        </div>
                        <h4 class="mb-2">Remove Activity?</h4>
                        <p class="mb-0">Are you sure you want to remove it?</p>
                        <div class="mt-3">
                            <label class="custom_check text-danger d-inline-block text-start">
                                <input type="checkbox" id="forceDeleteCheckbox">
                                <span class="checkmark"></span> <strong>Force delete ALL related data</strong> <br><small class="text-muted">(Activities, Activity Types, and Activity Statuses)</small>
                            </label>
                        </div>
                        <div class="d-flex align-items-center justify-content-center mt-4">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="button" class="btn btn-danger" id="confirmDeleteCallReason">Yes, Delete it</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete Calls -->

    <!-- Force Delete Confirm -->
    <div class="modal fade" id="force_delete_confirm_modal" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center">
                        <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                            <i class="ti ti-alert-triangle fs-36 text-danger"></i>
                        </div>
                        <h4 class="mb-2 text-danger">Warning: Destructive Action</h4>
                        <p class="mb-0">You have selected to <strong>Force Delete</strong> this Activity Setting. This action cannot be undone and will permanently wipe out all related data.</p>
                        <p class="mt-2 mb-0">Are you absolutely sure you want to proceed?</p>
                        <div class="d-flex align-items-center justify-content-center mt-4">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="button" class="btn btn-danger" id="finalForceDeleteBtn">Yes, Permanently Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Force Delete Confirm -->

    <!-- Add Activity Type Modal -->
    <div class="modal fade" id="add_activity_type" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Activity Type</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="AddActivityTypeForm" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="col-form-label">Activity Type (Type and press Enter) <span class="text-danger">*</span></label>
                            <div class="tag-input-container form-control" style="min-height: 80px; height: auto; cursor: text;">
                                <div class="d-flex flex-wrap gap-1 tags-container" id="type-tags-container"></div>
                                <input type="text" class="border-0 bg-transparent py-1 w-100 tag-input-field" placeholder="Type activity type..." style="outline: none; box-shadow: none;">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label">Select Activities <span class="text-danger">*</span></label>
                            <select class="form-control" name="activity_setting_ids[]" id="activity_setting_multi_select" multiple size="5" style="width: 100%;">
                                <!-- populated by js -->
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label">Target Type</label>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <input type="radio" class="status-radio" id="add-target-both" value="both" name="target_type" checked>
                                    <label for="add-target-both">Both</label>
                                </div>
                                <div class="me-3">
                                    <input type="radio" class="status-radio" id="add-target-leads" value="leads" name="target_type">
                                    <label for="add-target-leads">Leads</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="add-target-customers" value="customers" name="target_type">
                                    <label for="add-target-customers">Customers</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="col-form-label">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <input type="radio" class="status-radio" id="add-type-active" value="1" name="status" checked>
                                    <label for="add-type-active">Active</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="add-type-inactive" value="0" name="status">
                                    <label for="add-type-inactive">Inactive</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex align-items-center justify-content-end m-0">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Activity Type Modal -->
    <div class="modal fade" id="edit_activity_type" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Activity Type</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="EditActivityTypeForm" method="POST">
                    <input type="hidden" id="editActivityTypeId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="col-form-label">Activity Type <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editActivityTypeName" name="title">
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label">Target Type</label>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <input type="radio" class="status-radio" id="edit-target-both" value="both" name="target_type">
                                    <label for="edit-target-both">Both</label>
                                </div>
                                <div class="me-3">
                                    <input type="radio" class="status-radio" id="edit-target-leads" value="leads" name="target_type">
                                    <label for="edit-target-leads">Leads</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="edit-target-customers" value="customers" name="target_type">
                                    <label for="edit-target-customers">Customers</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="col-form-label">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <input type="radio" class="status-radio" id="edit-type-active" name="status" value="1">
                                    <label for="edit-type-active">Active</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="edit-type-inactive" name="status" value="0">
                                    <label for="edit-type-inactive">Inactive</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex align-items-center justify-content-end m-0">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Activity Type Modal -->
    <div class="modal fade" id="delete_activity_type" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <input type="hidden" id="deleteActivityTypeId">
                <div class="modal-body">
                    <div class="text-center">
                        <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                            <i class="ti ti-trash-x fs-36 text-danger"></i>
                        </div>
                        <h4 class="mb-2">Remove Activity Type?</h4>
                        <p class="mb-0">Are you sure you want to remove it?</p>
                        <div class="d-flex align-items-center justify-content-center mt-4">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="button" class="btn btn-danger" id="confirmDeleteActivityType">Yes, Delete it</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Activity Status Modal -->
    <div class="modal fade" id="add_activity_status" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Activity Status</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="AddActivityStatusForm" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="col-form-label">Activity Status (Type and press Enter) <span class="text-danger">*</span></label>
                            <div class="tag-input-container form-control" style="min-height: 80px; height: auto; cursor: text;">
                                <div class="d-flex flex-wrap gap-1 tags-container" id="status-tags-container"></div>
                                <input type="text" class="border-0 bg-transparent py-1 w-100 tag-input-field" placeholder="Type activity status..." style="outline: none; box-shadow: none;">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label">Select Activity Types <span class="text-danger">*</span></label>
                            <select class="form-control" name="activity_type_ids[]" id="activity_type_multi_select" multiple size="5" style="width: 100%;">
                                <!-- populated by js -->
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                        </div>
                        <div class="mb-0">
                            <label class="col-form-label">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <input type="radio" class="status-radio" id="add-status-active" value="1" name="status" checked>
                                    <label for="add-status-active">Active</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="add-status-inactive" value="0" name="status">
                                    <label for="add-status-inactive">Inactive</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex align-items-center justify-content-end m-0">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Activity Status Modal -->
    <div class="modal fade" id="edit_activity_status" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Activity Status</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="EditActivityStatusForm" method="POST">
                    <input type="hidden" id="editActivityStatusId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="col-form-label">Activity Status <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editActivityStatusName" name="title">
                        </div>
                        <div class="mb-0">
                            <label class="col-form-label">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <input type="radio" class="status-radio" id="edit-status-active" name="status" value="1">
                                    <label for="edit-status-active">Active</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="edit-status-inactive" name="status" value="0">
                                    <label for="edit-status-inactive">Inactive</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex align-items-center justify-content-end m-0">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Activity Status Modal -->
    <div class="modal fade" id="delete_activity_status" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <input type="hidden" id="deleteActivityStatusId">
                <div class="modal-body">
                    <div class="text-center">
                        <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                            <i class="ti ti-trash-x fs-36 text-danger"></i>
                        </div>
                        <h4 class="mb-2">Remove Activity Status?</h4>
                        <p class="mb-0">Are you sure you want to remove it?</p>
                        <div class="d-flex align-items-center justify-content-center mt-4">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="button" class="btn btn-danger" id="confirmDeleteActivityStatus">Yes, Delete it</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <div id="dynamicToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="dynamicToastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Mass Delete Modal -->
    <div class="modal fade" id="mass_delete_modal" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center">
                        <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                            <i class="ti ti-trash-x fs-36 text-danger"></i>
                        </div>
                        <h4 class="mb-2">Delete Selected Items?</h4>
                        <p class="mb-0" id="mass-delete-count-msg">Are you sure you want to delete these items?</p>
                        <div class="d-flex align-items-center justify-content-center mt-4">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="button" class="btn btn-danger" id="confirmMassDeleteBtn">Yes, Delete it</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')



    <script>
    const activityTypeUrlBase = "{{ route('activity-type.show',['box_slug' => request()->route('box_slug')]) }}";
</script>
    <script>
const CallManager = {
    sourceTable: null,

    init() {
        if (!this.validateTable()) return;
        this.bindEvents();
        this.initDataTable();
    },

    validateTable() {
        const tableEl = $('#calls-list');
        if (tableEl.length === 0) {
            console.warn('Table with ID #calls-list not found.');
            return false;
        }
        if (tableEl.prop('tagName') !== 'TABLE') {
            console.error('#calls-list is not a <table> element.');
            return false;
        }
        return true;
    },

    getRouteUrl() {
        return "{{ route('get.calls', ['box_slug' => request()->route('box_slug')]) }}";
    },

    reloadTable() {
        if (this.sourceTable) {
            this.sourceTable.ajax.reload(null, false);
        }
    },

    showToast(message, type = 'success') {
        const $toast = $('#dynamicToast');
        const $message = $('#dynamicToastMessage');
        $message.text(message);
        $toast.removeClass('bg-success bg-danger bg-warning');

        switch (type) {
            case 'success': $toast.addClass('bg-success'); break;
            case 'error':   $toast.addClass('bg-danger'); break;
            default:        $toast.addClass('bg-warning');
        }

        new bootstrap.Toast($toast[0]).show();
    },

    bindEvents() {
        let debounceTimer;
        $('#callsSearchInput').on('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                CallManager.reloadTable();
            }, 300);
        });

        $('#add_calls_form').on('submit', this.handleAddFormSubmit);
        $(document).on('click', '.edit_calls-btn', this.populateEditModal);
        $('#editContactStageForm').on('submit', this.handleEditFormSubmit);
        $(document).on('click', '.delete_reason-btn', function () {
            $('#deleteCallReasonId').val($(this).data('id'));
            $('#forceDeleteCheckbox').prop('checked', false); // Reset checkbox on open
        });
        $('#confirmDeleteCallReason').on('click', this.handleDelete);
        $('#finalForceDeleteBtn').on('click', function() {
            const id = $('#deleteCallReasonId').val();
            CallManager.executeDelete(id, 1);
        });

        $(document).on('change', '.toggle-status', this.toggleStatus);
        $(document).on('click', '.clickable-row', function(e) {
            e.stopPropagation();
            const activityId = $(this).data('id');
            // Remove highlight from all rows
            $('#calls-list tbody tr').removeClass('table-active bg-light');
            // Add highlight to clicked row
            $(this).closest('tr').addClass('table-active bg-light');
            UnifiedActivityManager.selectActivitySetting(activityId);
        });
    },

    initDataTable() {
    const self = this;

    this.sourceTable = $('#calls-list').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: self.getRouteUrl(),
            type: 'GET',
            data: function (d) {
                d.search = {
                    value: $('#callsSearchInput').val(),
                    regex: false
                };
            },
            error: xhr => {
                console.error("DataTable Load Error:", xhr.responseText);
                self.showToast("Failed to load data.", "error");
            }
        },
        ordering: true,
        bFilter: false,
        bInfo: false,
        columns: [
            {
                data: "id",
                orderable: false,
                searchable: false,
                render: (data, type, row) => `
                    <div class="form-check">
                        <input class="form-check-input check-item-calls" type="checkbox" value="${row.id}">
                    </div>`
            },
            {
                data: "title",
                render: (data, type, row) => `<span class="title-name clickable-row" style="cursor: pointer; color: #007bff;" data-id="${row.id}">${data}</span>`
            },
            {
                data: "created_at",
                render: data => data || '-'
            },
            {
                data: "status",
                render: data =>
                    `<span class="badge badge-pill badge-status ${data == "1" ? "bg-success" : "bg-danger"}">
                        ${data == "1" ? "Active" : "Inactive"}
                    </span>`
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                visible: true,
                render: (data, type, row) => {
                    return `
                            <div class="dropdown table-action">
                                <a href="#" class="action-icon" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item edit_calls-btn" href="#"
                                        data-id="${row.id}"
                                        data-title="${row.title}"
                                        data-status="${row.status}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#edit_calls">
                                        <i class="ti ti-edit text-blue"></i> Edit
                                    </a>
                                    <a class="dropdown-item delete_reason-btn" href="#"
                                        data-id="${row.id}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#delete_calls">
                                        <i class="ti ti-trash text-danger"></i> Delete
                                    </a>
                            </div>
                        </div>`;
                }
            }
        ],
        language: {
            search: " ",
            searchPlaceholder: "Search",
            paginate: {
                next: 'Next <i class="fa fa-angle-right"></i>',
                previous: '<i class="fa fa-angle-left"></i> Prev'
            }
        },
        initComplete: function(settings, json) {
            const api = this.api();
            const tableNode = $(api.table().node());
            tableNode.closest('.card-body').find('.datatable-paginate').html($(api.table().container()).find('.dataTables_paginate'));
            tableNode.closest('.card-body').find('.datatable-length').html($(api.table().container()).find('.dataTables_length'));
            // Highlight selected Activity Setting row from localStorage or URL
            const activityId = new URLSearchParams(window.location.search).get('activity_id') || localStorage.getItem('selectedActivityId');
            if (activityId) {
                $(`#calls-list tbody tr .clickable-row[data-id="${activityId}"]`).closest('tr').addClass('table-active bg-light');
            }
        }
    });

    // Debounced external search input
    let debounceTimer;
    $('#callsSearchInput').on('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            self.sourceTable.ajax.reload();
        }, 300);
    });
}
,

    handleAddFormSubmit(e) {
        e.preventDefault();
        const $form = $(this);
        const url = "{{ route('store.source', ['box_slug' => request()->route('box_slug')]) }}";
        
        
        // Collect tags
        const names = [];
        $('#activity-tags-container .badge span').each(function() {
            names.push($(this).text().trim());
        });
        
        // Also check if there is pending text in the input
        const pendingText = $form.find('.tag-input-field').val().trim();
        if (pendingText) {
            names.push(pendingText);
        }

        if (names.length === 0) {
            CallManager.showToast('Please enter at least one name.', 'error');
            return;
        }

        const status = $('#add-active').is(':checked') ? 1 : 0;
        const type = 'call_reason';
        
        // Create bulk payload
        $.post(url, {
            names: names,
            status: status,
            type: type,
            _token: $('meta[name="csrf-token"]').attr("content")
        }).done(res => {
             if (res.success) {
                 $('#add_calls').modal('hide');
                 $form[0].reset();
                 $('#activity-tags-container').empty(); // Clear tags
                 CallManager.showToast(res.message, 'success');
                 CallManager.reloadTable();
                 
                 // Select the last added one if available (API returns created object or array)
                 // If array, select the last one
                 const data = Array.isArray(res.data) ? res.data[res.data.length - 1] : res.data;
                 if (data && data.id) {
                     UnifiedActivityManager.selectActivitySetting(data.id);
                 }
             }
        }).fail(xhr => {
             const res = xhr.responseJSON;
             let msg = 'Add failed.';
             if (xhr.status === 409) {
                 msg = res.message; // Duplicate message
             } else if (xhr.status === 422 && res.errors) {
                 msg = Object.values(res.errors).flat().join(' ');
             } else if (res && res.message) {
                 msg = res.message;
             }
             CallManager.showToast(msg, 'error');
        });
    },

    populateEditModal() {
        $('#editContactStageId').val($(this).data('id'));
        $('#editContactStageName').val($(this).data('title'));
        $(`#edit_calls input[name="status"][value="${$(this).data('status')}"]`).prop('checked', true);
    },

    handleEditFormSubmit(e) {
        e.preventDefault();
        const id = $('#editContactStageId').val();
        const name = $('#editContactStageName').val().trim();
        const status = $('#edit_calls input[name="status"]:checked').val();

        $.post("{{ route('source.update', ['box_slug' => request()->route('box_slug')]) }}", {
            id, name, status, type: 'call_reason',
            _token: $('meta[name="csrf-token"]').attr("content")
        }).done(res => {
            $('#edit_calls').modal('hide');
            CallManager.showToast(res.message || 'Updated.', res.success ? 'success' : 'error');
            if (res.success) CallManager.reloadTable();
        }).fail(xhr => {
            const res = xhr.responseJSON;
            const errorMsg = res?.message || 'Update failed.';
            CallManager.showToast(errorMsg, 'error');
        });
    },

    handleDelete() {
        const id = $('#deleteCallReasonId').val();
        const force = $('#forceDeleteCheckbox').is(':checked') ? 1 : 0;
        if (!id) return CallManager.showToast('Invalid ID.', 'error');

        if (force === 1) {
            $('#delete_calls').modal('hide');
            $('#force_delete_confirm_modal').modal('show');
            return;
        }

        CallManager.executeDelete(id, 0);
    },

    executeDelete(id, force) {
        $.ajax({
            url: "{{ route('source.delete', ['box_slug' => request()->route('box_slug')]) }}",
            method: 'DELETE',
            data: {
                id,
                type: 'call_reason',
                force,
                _token: $('meta[name="csrf-token"]').attr("content")
            },
            success(res) {
                $('#delete_calls').modal('hide');
                $('#force_delete_confirm_modal').modal('hide');
                CallManager.showToast(res.message || 'Deleted.', res.success ? 'success' : 'error');
                if (res.success) {
                    CallManager.reloadTable();
                    if(force === 1 && typeof UnifiedActivityManager !== 'undefined') {
                        if(UnifiedActivityManager.typeTable) UnifiedActivityManager.typeTable.ajax.reload(null, false);
                        if(UnifiedActivityManager.statusTable) UnifiedActivityManager.statusTable.ajax.reload(null, false);
                    }
                }
            },
            error(xhr) {
                $('#delete_calls').modal('hide');
                $('#force_delete_confirm_modal').modal('hide');
                const res = xhr.responseJSON;
                const errorMsg = (xhr.status === 422 && res?.errors) ?
                    Object.values(res.errors).flat().join(' ') :
                    res?.message || 'Delete failed.';
                CallManager.showToast(errorMsg, 'error');
            }
        });
    },

    toggleStatus() {
        const id = $(this).data("id");
        const status = $(this).is(":checked") ? 1 : 0;

        $.post("{{ route('source.delete', ['box_slug' => request()->route('box_slug')]) }}", {
            id, status,
            _token: $('meta[name="csrf-token"]').attr("content")
        }).done(res => {
            CallManager.showToast(res.message || 'Status updated.', res.success ? 'success' : 'error');
            if (res.success) CallManager.reloadTable();
        }).fail(() => {
            CallManager.showToast('Status update failed.', 'error');
        });
    },
};

// Unified Activity Manager to handle navigation between sections
const UnifiedActivityManager = {
    updateUrl(activityId, activityTypeId) {
        const params = new URLSearchParams();
        if (activityId) params.set('activity_id', activityId);
        if (activityTypeId) params.set('activity_type_id', activityTypeId);
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', newUrl);
    },

    selectActivitySetting(activityId) {
        if (!activityId) return;
        this.updateUrl(activityId, null);
        // Store in localStorage
        localStorage.setItem('selectedActivityId', activityId);
        localStorage.removeItem('selectedActivityTypeId');
        $('#activity-types-section').slideDown();
        $('#activity-statuses-section').slideUp();
        // Store activity_id in the add form for Activity Types
        $('#AddActivityTypeForm').data('activity-setting-id', activityId);
        // Highlight the selected Activity Setting row
        $('#calls-list tbody tr').removeClass('table-active bg-light');
        setTimeout(() => {
            $(`#calls-list tbody tr .clickable-row[data-id="${activityId}"]`).closest('tr').addClass('table-active bg-light');
        }, 100);
        if (ActivityTypeManager) {
            ActivityTypeManager.initDataTable();
        }
    },

    selectActivityType(activityTypeId) {
        if (!activityTypeId) return;
        const activityId = new URLSearchParams(window.location.search).get('activity_id');
        this.updateUrl(activityId, activityTypeId);
        // Store in localStorage
        localStorage.setItem('selectedActivityTypeId', activityTypeId);
        $('#activity-statuses-section').slideDown();
        // Store activity_type_id in the add form for Activity Statuses
        $('#AddActivityStatusForm').data('activity-type-id', activityTypeId);
        // Highlight the selected Activity Type row
        $('#activity-list-table tbody tr').removeClass('table-active bg-light');
        setTimeout(() => {
            $(`#activity-list-table tbody tr .clickable-type-row[data-id="${activityTypeId}"]`).closest('tr').addClass('table-active bg-light');
        }, 100);
        if (ActivityStatusManager) {
            ActivityStatusManager.initDataTable();
        }
    }
};

// Activity Type Manager
const ActivityTypeManager = {
    sourceTable: null,

    getRouteUrl() {
        const activityId = new URLSearchParams(window.location.search).get('activity_id') || '';
        return `{{ route('getActivtiyType', ['box_slug' => request()->route('box_slug')]) }}?activity_setting_id=${activityId}`;
    },

    reloadTable() {
        if (this.sourceTable) {
            this.sourceTable.ajax.reload(null, false);
        }
    },

    showToast(message, type = 'success') {
        const $toast = $('#dynamicToast');
        const $message = $('#dynamicToastMessage');
        $message.text(message);
        $toast.removeClass('bg-success bg-danger bg-warning');
        switch (type) {
            case 'success': $toast.addClass('bg-success'); break;
            case 'error': $toast.addClass('bg-danger'); break;
            default: $toast.addClass('bg-warning');
        }
        new bootstrap.Toast($toast[0]).show();
    },

    initDataTable() {
        const tableEl = $('#activity-list-table');
        if ($.fn.DataTable.isDataTable(tableEl)) {
            tableEl.DataTable().clear().destroy();
        }

        this.sourceTable = tableEl.DataTable({
            ajax: {
                url: this.getRouteUrl(),
                data: d => {
                    d.search = $('#activityTypeSearchInput').val();
                },
                dataSrc: json => json.success ? json.data : [],
                error: xhr => {
                    console.error("DataTable Error:", xhr.responseText);
                    this.showToast("Failed to load data.", "error");
                }
            },
            ordering: true,
            bFilter: false,
            bInfo: false,
            columns: [{
                data: "id",
                orderable: false,
                searchable: false,
                render: (data, type, row) => `
                    <div class="form-check">
                        <input class="form-check-input check-item-types" type="checkbox" value="${row.id}">
                    </div>`
            }, {
                data: "title",
                render: (data, type, row) => `<span class="clickable-type-row" style="cursor: pointer; color: #007bff;" data-id="${row.id}">${data}</span>`
            }, {
                data: "setting_name",
                render: data => `<span class="badge bg-soft-info text-info">${data || '---'}</span>`
            }, {
                data: "target_type",
                render: data => `<span class="badge ${data === 'customers' ? 'bg-soft-primary text-primary' : (data === 'leads' ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary')}">${data ? data.charAt(0).toUpperCase() + data.slice(1) : 'Both'}</span>`
            }, {
                data: "created_date",
                render: data => data || '-'
            }, {
                data: "status",
                render: data => `<span class="badge badge-pill ${data == 1 ? "bg-success" : "bg-danger"}">${data == 1 ? "Active" : "Inactive"}</span>`
            },             {
                orderable: false,
                visible: true,
                render: (data, type, row) => {
                    return `
                        <div class="dropdown table-action">
                            <a href="#" class="action-icon" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item edit-type-btn" href="#" data-id="${row.id}" data-title="${row.title}" data-status="${row.status}" data-target-type="${row.target_type}" data-bs-toggle="modal" data-bs-target="#edit_activity_type">
                                    <i class="ti ti-edit text-blue"></i> Edit
                                </a>
                                <a class="dropdown-item delete-type-btn" href="#" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#delete_activity_type">
                                    <i class="ti ti-trash text-danger"></i> Delete
                                </a>
                            </div>
                        </div>`;
                }
            }],
            initComplete: function(settings, json) {
                const api = this.api();
                const tableNode = $(api.table().node());
                tableNode.closest('.card-body').find('.datatable-paginate-type').html($(api.table().container()).find('.dataTables_paginate'));
                tableNode.closest('.card-body').find('.datatable-length-type').html($(api.table().container()).find('.dataTables_length'));
                // Highlight selected Activity Type row from localStorage or URL
                const activityTypeId = new URLSearchParams(window.location.search).get('activity_type_id') || localStorage.getItem('selectedActivityTypeId');
                if (activityTypeId) {
                    $(`#activity-list-table tbody tr .clickable-type-row[data-id="${activityTypeId}"]`).closest('tr').addClass('table-active bg-light');
                }
            },
            language: {
                search: " ",
                searchPlaceholder: "Search",
                paginate: {
                    next: 'Next <i class="fa fa-angle-right"></i>',
                    previous: '<i class="fa fa-angle-left"></i> Prev'
                }
            }
        });

        let debounceTimer;
        $('#activityTypeSearchInput').off('input').on('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => this.reloadTable(), 300);
        });

        $(document).off('click', '.clickable-type-row').on('click', '.clickable-type-row', function(e) {
            e.stopPropagation();
            const typeId = $(this).data('id');
            // Remove highlight from all Activity Type rows
            $('#activity-list-table tbody tr').removeClass('table-active bg-light');
            // Add highlight to clicked row
            $(this).closest('tr').addClass('table-active bg-light');
            UnifiedActivityManager.selectActivityType(typeId);
        });

        $(document).off('click', '.edit-type-btn').on('click', '.edit-type-btn', function() {
            $('#editActivityTypeId').val($(this).data('id'));
            $('#editActivityTypeName').val($(this).data('title'));
            $(`#edit_activity_type input[name="status"][value="${$(this).data('status')}"]`).prop('checked', true);
            $(`#edit_activity_type input[name="target_type"][value="${$(this).data('target-type')}"]`).prop('checked', true);
        });

        $(document).off('click', '.delete-type-btn').on('click', '.delete-type-btn', function() {
            $('#deleteActivityTypeId').val($(this).data('id'));
        });
    }
};

// Activity Status Manager
const ActivityStatusManager = {
    sourceTable: null,

    getRouteUrl() {
        const activityTypeId = new URLSearchParams(window.location.search).get('activity_type_id') || '';
        return `{{ route('getActivtiyStatus', ['box_slug' => request()->route('box_slug')]) }}?activity_type_id=${activityTypeId}`;
    },

    reloadTable() {
        if (this.sourceTable) {
            this.sourceTable.ajax.reload(null, false);
        }
    },

    showToast(message, type = 'success') {
        const $toast = $('#dynamicToast');
        const $message = $('#dynamicToastMessage');
        $message.text(message);
        $toast.removeClass('bg-success bg-danger bg-warning');
        switch (type) {
            case 'success': $toast.addClass('bg-success'); break;
            case 'error': $toast.addClass('bg-danger'); break;
            default: $toast.addClass('bg-warning');
        }
        new bootstrap.Toast($toast[0]).show();
    },

    initDataTable() {
        const tableEl = $('#activity-status-table');
        if ($.fn.DataTable.isDataTable(tableEl)) {
            tableEl.DataTable().clear().destroy();
        }

        this.sourceTable = tableEl.DataTable({
            ajax: {
                url: this.getRouteUrl(),
                data: d => {
                    d.search = $('#activityStatusSearchInput').val();
                },
                dataSrc: json => json.success ? json.data : [],
                error: xhr => {
                    console.error("DataTable Error:", xhr.responseText);
                    this.showToast("Failed to load data.", "error");
                }
            },
            ordering: true,
            bFilter: false,
            bInfo: false,
            columns: [{
                data: "id",
                orderable: false,
                searchable: false,
                render: (data, type, row) => `
                    <div class="form-check">
                        <input class="form-check-input check-item-statuses" type="checkbox" value="${row.id}">
                    </div>`
            }, {
                data: "title",
                render: data => `<span>${data}</span>`
            }, {
                data: "setting_name",
                render: data => `<span class="badge bg-soft-info text-info">${data || '---'}</span>`
            }, {
                data: "type_name",
                render: data => `<span class="badge bg-soft-warning text-warning">${data || '---'}</span>`
            }, {
                data: "created_date",
                render: data => data || '-'
            }, {
                data: "status",
                render: data => `<span class="badge badge-pill ${data == 1 ? "bg-success" : "bg-danger"}">${data == 1 ? "Active" : "Inactive"}</span>`
            }, {
                orderable: false,
                visible: true,
                render: (data, type, row) => {
                    return `
                        <div class="dropdown table-action">
                            <a href="#" class="action-icon" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item edit-status-btn" href="#" data-id="${row.id}" data-title="${row.title}" data-status="${row.status}" data-bs-toggle="modal" data-bs-target="#edit_activity_status">
                                    <i class="ti ti-edit text-blue"></i> Edit
                                </a>
                                <a class="dropdown-item delete-status-btn" href="#" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#delete_activity_status">
                                    <i class="ti ti-trash text-danger"></i> Delete
                                </a>
                            </div>
                        </div>`;
                }
            }],
            initComplete: function(settings, json) {
                const api = this.api();
                const tableNode = $(api.table().node());
                tableNode.closest('.card-body').find('.datatable-paginate-status').html($(api.table().container()).find('.dataTables_paginate'));
                tableNode.closest('.card-body').find('.datatable-length-status').html($(api.table().container()).find('.dataTables_length'));
            },
            language: {
                search: " ",
                searchPlaceholder: "Search",
                paginate: {
                    next: 'Next <i class="fa fa-angle-right"></i>',
                    previous: '<i class="fa fa-angle-left"></i> Prev'
                }
            }
        });

        let debounceTimer;
        $('#activityStatusSearchInput').off('input').on('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => this.reloadTable(), 300);
        });

        $(document).off('click', '.edit-status-btn').on('click', '.edit-status-btn', function() {
            $('#editActivityStatusId').val($(this).data('id'));
            $('#editActivityStatusName').val($(this).data('title'));
            $(`#edit_activity_status input[name="status"][value="${$(this).data('status')}"]`).prop('checked', true);
        });

        $(document).off('click', '.delete-status-btn').on('click', '.delete-status-btn', function() {
            $('#deleteActivityStatusId').val($(this).data('id'));
        });
    }
};

$(document).ready(() => {
    CallManager.init();
    
    // Hide buttons for sections
    $(document).on('click', '#hide-activity-types-btn', function() {
        $('#activity-types-section').slideUp();
        $('#activity-statuses-section').slideUp();
        // Clear activity_id and activity_type_id from URL
        const params = new URLSearchParams(window.location.search);
        params.delete('activity_id');
        params.delete('activity_type_id');
        window.history.pushState({}, '', window.location.pathname + (params.toString() ? '?' + params.toString() : ''));
        // Clear localStorage
        localStorage.removeItem('selectedActivityId');
        localStorage.removeItem('selectedActivityTypeId');
        // Remove highlight from Activity Setting rows
        $('#calls-list tbody tr').removeClass('table-active bg-light');
    });

    $(document).on('click', '#hide-activity-statuses-btn', function() {
        $('#activity-statuses-section').slideUp();
        // Clear activity_type_id from URL but keep activity_id
        const params = new URLSearchParams(window.location.search);
        params.delete('activity_type_id');
        window.history.pushState({}, '', window.location.pathname + (params.toString() ? '?' + params.toString() : ''));
        // Clear activity_type_id from localStorage
        localStorage.removeItem('selectedActivityTypeId');
        // Remove highlight from Activity Type rows
        $('#activity-list-table tbody tr').removeClass('table-active bg-light');
    });
    
    // Initialize Activity Types if activity_id is in URL or localStorage
    const urlParams = new URLSearchParams(window.location.search);
    const urlActivityId = urlParams.get('activity_id');
    const urlActivityTypeId = urlParams.get('activity_type_id');
    const storedActivityId = localStorage.getItem('selectedActivityId');
    const storedActivityTypeId = localStorage.getItem('selectedActivityTypeId');
    
    // Prioritize URL params, fall back to localStorage
    const activityId = urlActivityId || storedActivityId;
    const activityTypeId = urlActivityTypeId || storedActivityTypeId;
    
    if (activityId) {
        // If URL params exist, sync them to localStorage
        if (urlActivityId) {
            localStorage.setItem('selectedActivityId', urlActivityId);
        }
        // Update URL if we're using stored value (no URL param but localStorage has value)
        if (!urlActivityId && storedActivityId) {
            UnifiedActivityManager.updateUrl(storedActivityId, storedActivityTypeId || null);
        }
        UnifiedActivityManager.selectActivitySetting(activityId);
        // Highlighting will be done in DataTable's initComplete callback
        if (activityTypeId) {
            // If URL param exists, sync it to localStorage
            if (urlActivityTypeId) {
                localStorage.setItem('selectedActivityTypeId', urlActivityTypeId);
            }
            UnifiedActivityManager.selectActivityType(activityTypeId);
            // Highlighting will be done in DataTable's initComplete callback
        }
    }

    // Form handlers for Activity Types
    $('#AddActivityTypeForm').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const url = `{{ route('activityType.store', ['box_slug' => request()->route('box_slug')]) }}`;
        
        // Get selected activities
        let activityIds = $('#activity_setting_multi_select').val();
         // Fallback to current context
        if (!activityIds || activityIds.length === 0) {
             const currentId = new URLSearchParams(window.location.search).get('activity_id') || localStorage.getItem('selectedActivityId');
             if (currentId) activityIds = [currentId];
        }

        if (!activityIds || activityIds.length === 0) {
            ActivityTypeManager.showToast('Please select at least one Activity.', 'error');
            return;
        }

        const titles = [];
        $('#type-tags-container .badge span').each(function() {
            titles.push($(this).text().trim());
        });
        const pendingText = $form.find('.tag-input-field').val().trim();
        if (pendingText) titles.push(pendingText);

        if (titles.length === 0) {
            ActivityTypeManager.showToast('Please enter at least one type.', 'error');
            return;
        }

        const status = $form.find('input[name="status"]:checked').val();
        const targetType = $form.find('input[name="target_type"]:checked').val();
        
        // Single Bulk Request
        $.post(url, {
            titles: titles,
            status: status,
            target_type: targetType,
            activity_setting_ids: activityIds,
            _token: $('meta[name="csrf-token"]').attr("content")
        }).done(res => {
             if (res.success) {
                 $('#add_activity_type').modal('hide');
                 $form[0].reset();
                 $('#type-tags-container').empty(); // Clear tags
                 ActivityTypeManager.showToast(res.message);
                 ActivityTypeManager.reloadTable();
                 
                 // Select the last added one
                 const data = Array.isArray(res.data) ? res.data[res.data.length - 1] : res.data;
                 if (data && data.id) {
                     UnifiedActivityManager.selectActivityType(data.id);
                 }
             }
        }).fail(xhr => {
             const res = xhr.responseJSON;
             let msg = 'Add failed.';
             if (xhr.status === 409) {
                 msg = res.message;
             } else if (xhr.status === 422 && res.errors) {
                 msg = Object.values(res.errors).flat().join(' ');
             } else if (res && res.message) {
                 msg = res.message;
             }
             ActivityTypeManager.showToast(msg, 'error');
        });
    });

    $('#EditActivityTypeForm').on('submit', function(e) {
        e.preventDefault();
        $.post("{{ route('activityType.update', ['box_slug' => request()->route('box_slug')]) }}", {
            id: $('#editActivityTypeId').val(),
            title: $('#editActivityTypeName').val().trim(),
            status: $('#edit_activity_type input[name="status"]:checked').val(),
            target_type: $('#edit_activity_type input[name="target_type"]:checked').val(),
            _token: $('meta[name="csrf-token"]').attr("content")
        }).done(res => {
            $('#edit_activity_type').modal('hide');
            ActivityTypeManager.showToast(res.message || 'Updated.', res.success ? 'success' : 'error');
            if (res.success) ActivityTypeManager.reloadTable();
        }).fail(() => ActivityTypeManager.showToast('Update failed.', 'error'));
    });

    $('#confirmDeleteActivityType').on('click', function() {
        const id = $('#deleteActivityTypeId').val();
        if (!id) return ActivityTypeManager.showToast('Invalid ID.', 'error');
        $.ajax({
            url: `{{ route('activityType.delete', ['box_slug' => request()->route('box_slug')]) }}`,
            method: 'DELETE',
            data: { id, _token: $('meta[name="csrf-token"]').attr("content") },
            success: (res) => {
                $('#delete_activity_type').modal('hide');
                ActivityTypeManager.showToast(res.message || 'Deleted.', res.success ? 'success' : 'error');
                if (res.success) ActivityTypeManager.reloadTable();
            },
            error: (xhr) => {
                const res = xhr.responseJSON;
                const errorMsg = res?.message || 'Delete failed.';
                ActivityTypeManager.showToast(errorMsg, 'error');
            }
        });
    });

    // Form handlers for Activity Statuses
    $('#AddActivityStatusForm').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const url = `{{ route('ActivitStatus.store', ['box_slug' => request()->route('box_slug')]) }}`;
        
        // Get selected types
        let activityTypeIds = $('#activity_type_multi_select').val();
        
        // Fallback to current context if none selected (though UI requires it)
        if (!activityTypeIds || activityTypeIds.length === 0) {
             const currentId = new URLSearchParams(window.location.search).get('activity_type_id') || $form.data('activity-type-id');
             if (currentId) activityTypeIds = [currentId];
        }

        if (!activityTypeIds || activityTypeIds.length === 0) {
            ActivityStatusManager.showToast('Please select at least one Activity Type.', 'error');
            return;
        }
        
        const titles = [];
        $('#status-tags-container .badge span').each(function() {
            titles.push($(this).text().trim());
        });
        const pendingText = $form.find('.tag-input-field').val().trim();
        if (pendingText) titles.push(pendingText);

        if (titles.length === 0) {
            ActivityStatusManager.showToast('Please enter at least one status.', 'error');
            return;
        }

        const status = $form.find('input[name="status"]:checked').val();
        
        $.post(url, {
            titles: titles,
            status: status,
            activity_type_ids: activityTypeIds,
            _token: $('meta[name="csrf-token"]').attr("content")
        }).done(res => {
             if (res.success) {
                 $('#add_activity_status').modal('hide');
                 $form[0].reset();
                 $('#status-tags-container').empty(); // Clear tags
                 ActivityStatusManager.showToast(res.message);
                 ActivityStatusManager.reloadTable();
             }
        }).fail(xhr => {
             const res = xhr.responseJSON;
             let msg = 'Add failed.';
             if (xhr.status === 409) {
                 msg = res.message;
             } else if (xhr.status === 422 && res.errors) {
                 msg = Object.values(res.errors).flat().join(' ');
             } else if (res && res.message) {
                 msg = res.message;
             }
             ActivityStatusManager.showToast(msg, 'error');
        });
    });

    $('#EditActivityStatusForm').on('submit', function(e) {
        e.preventDefault();
        $.post("{{ route('activityStatus.update', ['box_slug' => request()->route('box_slug')]) }}", {
            id: $('#editActivityStatusId').val(),
            title: $('#editActivityStatusName').val().trim(),
            status: $('#edit_activity_status input[name="status"]:checked').val(),
            _token: $('meta[name="csrf-token"]').attr("content")
        }).done(res => {
            $('#edit_activity_status').modal('hide');
            ActivityStatusManager.showToast(res.message || 'Updated.', res.success ? 'success' : 'error');
            if (res.success) ActivityStatusManager.reloadTable();
        }).fail(() => ActivityStatusManager.showToast('Update failed.', 'error'));
    });

    $('#confirmDeleteActivityStatus').on('click', function() {
        const id = $('#deleteActivityStatusId').val();
        if (!id) return ActivityStatusManager.showToast('Invalid ID.', 'error');
        $.ajax({
            url: `{{ route('activityStatus.delete', ['box_slug' => request()->route('box_slug')]) }}`,
            method: 'DELETE',
            data: { id, _token: $('meta[name="csrf-token"]').attr("content") },
            success: (res) => {
                $('#delete_activity_status').modal('hide');
                ActivityStatusManager.showToast(res.message || 'Deleted.', res.success ? 'success' : 'error');
                if (res.success) ActivityStatusManager.reloadTable();
            },
            error: (xhr) => {
                const res = xhr.responseJSON;
                const errorMsg = res?.message || 'Delete failed.';
                ActivityStatusManager.showToast(errorMsg, 'error');
            }
        });
    });

    // --- Tag Input Logic ---
    $(document).on('click', '.tag-input-container', function() {
        $(this).find('input').focus();
    });

    $(document).on('keydown', '.tag-input-field', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = $(this).val().trim();
            if (val) {
                const $container = $(this).siblings('.tags-container');
                const tagHtml = `
                    <div class="badge bg-danger d-flex align-items-center p-2 me-1 mb-1">
                        <span class="me-1 border-0 bg-transparent text-white" style="font-size: 0.9rem;">${val}</span>
                        <i class="ti ti-x remove-tag pointer" style="cursor: pointer; font-size: 1rem;"></i>
                    </div>
                `;
                $container.append(tagHtml);
                $(this).val('');
            }
        }
    });

    $(document).on('click', '.remove-tag', function(e) {
        e.stopPropagation(); // Prevent focusing the input
        $(this).closest('.badge').remove();
    });

    // Populate Activities when Add Type Modal is opened
    $('#add_activity_type').on('show.bs.modal', function () {
        const currentId = new URLSearchParams(window.location.search).get('activity_id') || localStorage.getItem('selectedActivityId');
        
        // Only load if empty or refresh needed
        const url = `{{ route('get.activities.list', ['box_slug' => request()->route('box_slug')]) }}`;
        $.get(url, function(res) {
            if(res.success) {
                const $select = $('#activity_setting_multi_select');
                $select.empty();
                res.data.forEach(act => {
                     const isSelected = (currentId && act.id == currentId) ? 'selected' : '';
                     $select.append(`<option value="${act.id}" ${isSelected}>${act.title}</option>`);
                });
            }
        });
    });
    
    // Populate Activity Types when Add Status Modal is opened
    $('#add_activity_status').on('show.bs.modal', function () {
        const activityId = new URLSearchParams(window.location.search).get('activity_id') || localStorage.getItem('selectedActivityId');
        if (!activityId) return;

        const currentTypeId = new URLSearchParams(window.location.search).get('activity_type_id') || localStorage.getItem('selectedActivityTypeId');

        const url = `{{ route('getActivtiyType', ['box_slug' => request()->route('box_slug')]) }}`;
        $.get(url, { activity_setting_id: activityId }, function(res) {
            if(res.success) {
                const $select = $('#activity_type_multi_select');
                $select.empty();
                res.data.forEach(type => {
                     const isSelected = (currentTypeId && type.id == currentTypeId) ? 'selected' : '';
                     $select.append(`<option value="${type.id}" ${isSelected}>${type.title}</option>`);
                });
            }
        });
    });
    // Mass Delete Headers
    function setupMassDelete(checkAllClass, checkItemClass, buttonId, tableId, type) {
        // Check All
        $(document).on('change', `.${checkAllClass}`, function() {
            const isChecked = $(this).is(':checked');
            $(`.${checkItemClass}`).prop('checked', isChecked);
            toggleDeleteButton(checkItemClass, buttonId);
        });

        // Individual Check
        $(document).on('change', `.${checkItemClass}`, function() {
            const allChecked = $(`.${checkItemClass}`).length === $(`.${checkItemClass}:checked`).length;
            $(`.${checkAllClass}`).prop('checked', allChecked);
            toggleDeleteButton(checkItemClass, buttonId);
        });

        // Delete Button Click
        $(document).on('click', `#${buttonId}`, function() {
            const ids = [];
            $(`.${checkItemClass}:checked`).each(function() {
                ids.push($(this).val());
            });

            if (ids.length === 0) return;

            // Update modal text
            $('#mass-delete-count-msg').text(`Are you sure you want to delete ${ids.length} item(s)?`);
            
            // Show modal
            $('#mass_delete_modal').modal('show');

            // Handle "Yes, Delete it" button
            $('#confirmMassDeleteBtn').off('click').on('click', function() {
                // Disable button to prevent double submit
                $(this).prop('disabled', true);

                $.post("{{ route('mass.delete', ['box_slug' => request()->route('box_slug')]) }}", {
                    ids: ids,
                    type: type,
                    _token: $('meta[name="csrf-token"]').attr("content")
                }).done(res => {
                    const toastType = (res.failed_count && res.failed_count > 0) ? 'warning' : 'success';
                    CallManager.showToast(res.message, toastType);
                    
                    // Reload appropriate table
                    if (type === 'call_reason') CallManager.reloadTable();
                    if (type === 'activity_type') ActivityTypeManager.reloadTable();
                    if (type === 'activity_status') ActivityStatusManager.reloadTable();
                    
                    // Hide button and reset state
                    $(`#${buttonId}`).hide();
                    $(`.${checkAllClass}`).prop('checked', false);
                    $('#mass_delete_modal').modal('hide');
                }).fail(xhr => {
                    CallManager.showToast('Mass delete failed.', 'error');
                }).always(() => {
                    // Re-enable button
                    $('#confirmMassDeleteBtn').prop('disabled', false);
                });
            });
        });
    }

    function toggleDeleteButton(checkItemClass, buttonId) {
        const checkedCount = $(`.${checkItemClass}:checked`).length;
        if (checkedCount > 0) {
            $(`#${buttonId}`).fadeIn();
        } else {
            $(`#${buttonId}`).fadeOut();
        }
    }

    setupMassDelete('check-all-calls', 'check-item-calls', 'delete-selected-calls', '#calls-list', 'call_reason');
    setupMassDelete('check-all-types', 'check-item-types', 'delete-selected-activity-type', '#activity-list-table', 'activity_type');
    setupMassDelete('check-all-statuses', 'check-item-statuses', 'delete-selected-activity-status', '#activity-status-table', 'activity_status');

});
</script>
@endpush
@endsection
