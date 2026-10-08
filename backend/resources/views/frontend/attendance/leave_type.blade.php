@extends('layouts.main')
@section('content')
@section('title', 'Leave Type')

<style>
    #iconPreview i {
        color: #000;
        font-size: 32px;
    }

    #bgColorPreview {
        min-height: 50px;
        font-weight: 500;
    }
</style>
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Leave Type </h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="index.html"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            Employee
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Leave Type </li>
                    </ol>
                </nav>
            </div>

        </div>
        <!-- /Breadcrumb -->
        <div class="card">
            <div class="card-body">
                <div class="border-bottom d-flex align-items-center justify-content-between pb-3 mb-3">
                    <h4>Leave Type</h4>

                    <div class="d-flex gap-2">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#add_leaves" data-action="add"
                            class="btn btn-primary d-flex align-items-center open-leave-modal">
                            <i class="ti ti-circle-plus me-2"></i>Add Leave Type
                        </a>

                        <a href="#" data-bs-toggle="modal" data-bs-target="#credit_leaves" data-action="credit"
                            class="btn btn-primary d-flex align-items-center open-leave-modal">
                            <i class="ti ti-circle-plus me-2"></i>Credit Leave
                        </a>
                    </div>
                </div>



                <div class="card-body p-0">
                    <div class="card mb-0">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h6>Leave Type List </h6>
                            <div>
                                <button type="button" class="btn btn-sm btn-white d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                                    <i class="ti ti-columns me-1"></i>Manage Columns
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="leaveTypesTable" class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort">
                                            <div class="form-check form-check-md">
                                                <input class="form-check-input" type="checkbox" id="select-all" />
                                            </div>
                                        </th>
                                        <th>Leave Type</th>
                                        <th>Leave Days</th>
                                        <th>Credit Frequency</th>
                                        <th>Credit Days</th>
                                        <th>Leave Code</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTables will inject rows here -->
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

<!-- Manage Columns Modal -->
<div class="modal fade" id="manageColumnsModal" tabindex="-1" aria-labelledby="manageColumnsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageColumnsModalLabel">Manage Columns</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="showAllColumns">Show All</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="hideAllColumns">Hide All</button>
                </div>
                <div class="list-group" id="columnsList">
                    <!-- Columns will be populated dynamically -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="applyColumnChanges">Apply Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Leaves -->
<div class="modal fade" id="add_leaves">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Leave Type </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>

            </div>
            <form id="addleave_type" method="POST">
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Leave Type <span class="text-danger">* </span></label>
                                <input type="text" class="form-control" name="leave_type" id="leave_type" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Credit Condition Comp Off <span
                                        class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="holiday_off"
                                                id="holiday_off" value="1">
                                            <label class="form-check-label" for="holiday_off">Holiday Off</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="weekly_off"
                                                id="weekly_off" value="1">
                                            <label class="form-check-label" for="weekly_off">Weekly Off</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="overtime_equal_shift"
                                                id="overtime_equal_shift" value="1">
                                            <label class="form-check-label" for="overtime_equal_shift">Overtime Equal to
                                                Shift Hours</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3" id="holiday_off_container" style="display:none;">
                            <label for="holiday_off_paid">Holiday Off Details</label>
                            <input type="text" class="form-control" id="holiday_off_paid"
                                name="holiday_off_details" placeholder="e.g. 1x (Holiday Pay Multiplier)" disabled>
                        </div>
                        <div class="mt-3" id="weekly_off_container" style="display:none;">
                            <label for="weekly_off_paid">Weekly Off Details</label>
                            <input type="text" class="form-control" id="weekly_off_paid"
                                name="weekly_off_details" placeholder="e.g. 1.5x (Weekly Off Pay Multiplier)"
                                disabled>
                        </div>
                        <div class="mt-3" id="overtime_equal_shift_container" style="display:none;">
                            <label for="overtime_equal_shift_paid">Overtime Details</label>
                            <input type="text" class="form-control" id="overtime_equal_shift_paid"
                                name="overtime_equal_shift_details" placeholder="e.g. 2x (Overtime Pay Multiplier)"
                                disabled>
                        </div>


                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="credit_frequency" class="form-label">
                                    Credit Frequency <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select" name="credit_frequency" id="credit_frequency">
                                    <option value="">Select Credit Frequency</option>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Alternate Month">Alternate Month</option>
                                    <option value="Quarterly">Quarterly</option>
                                    <option value="Half Yearly">Half Yearly</option>
                                    <option value="Yearly">Yearly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="credit_days" class="form-label">
                                    Credit Days <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" name="credit_days" id="credit_days">
                                    <option value="">Select Credit Days</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Number of days <span class="text-danger">* </span></label>
                                <input type="text" class="form-control" id="number_of_days"
                                    name="number_of_days" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Leave Code <span class="text-danger">* </span></label>
                                <input type="text" class="form-control" id="leave_code" name="leave_code" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Leave Credit Type</label>
                                <select class="form-select" name="leave_credit_type" id="leave_credit_type">
                                    <option value="">Select Leave Credit Type</option>
                                    <option value="paid">Paid Leave</option>
                                    <option value="unpaid">Unpaid Leave</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Carry Forward</label>
                                <select class="form-select carry_forward_select" name="carry_forward" id="carry_forward">
                                    <option value="">Select Option</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 carry_forward_days_container" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">Number of Days to Carry Forward</label>
                                <input type="number" class="form-control carry_forward_days" name="carry_forward_days" min="0">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="leave_iconSelect" class="form-label">Select Leave Icon</label>
                                <select id="leave_icon" name="leave_icon" class="form-select">
                                    <option value="">--Select an Icon--</option>
                                    <option value="ti ti-calendar-event fs-32">Annual Leave</option>
                                    <option value="ti ti-calendar-check fs-32">Paid Leave/Fixed Holiday</option>
                                    <!-- Sick Leave Icons -->
                                    <option value="ti ti-ambulance fs-32">Medical Leave</option>
                                    <option value="ti ti-pill fs-32">Sick leave</option>
                                    <!-- Medical Leave Icons -->
                                    <option value="ti ti-vaccine fs-32">Medical leave</option>
                                    <option value="ti ti-hospital fs-32">Sick Leave</option>
                                    <option value="ti ti-first-aid fs-32">Medical Leave</option>
                                    <!-- Casual Leave Icons -->
                                    <option value="ti ti-hexagon-letter-c fs-32">Casual Leave</option>
                                    <option value="ti ti-cup fs-32">Casual Leave</option>
                                    <option value="ti ti-emoji-smile fs-32">Casual Leave</option>
                                    <!-- Maternity Leave Icons -->
                                    <option value="ti ti-pregnant fs-32">Maternity Leave</option>
                                    <option value="ti ti-baby fs-32">Maternity Leave</option>
                                    <option value="ti ti-heart fs-32">Maternity Leave</option>
                                    <!-- Paternity Leave Icons -->
                                    <option value="ti ti-baby-carriage fs-32">Paternity Leave</option>
                                    <!-- Bereavement Leave Icons -->
                                    <option value="ti ti-heart-broken fs-32">Bereavement Leave</option>
                                    <option value="ti ti-cross fs-32">Bereavement Leave</option>
                                    <option value="ti ti-pray fs-32">Bereavement Leave</option>
                                    <!-- Unpaid Leave Icons -->
                                    <option value="ti ti-ban fs-32">Unpaid Leave</option>
                                </select>
                            </div>
                            <!-- Preview -->
                            <div class="mb-3">
                                <label class="form-label">Selected Icon Preview:</label>
                                <div id="iconPreview"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="leave_color" class="form-label">Leave Color</label>
                                <select id="leave_color" name="leave_color" class="form-select">
                                    <option value="">-- Select Background Color --</option>
                                    <option value="bg-black-le">bg-black-le</option>
                                    <option value="bg-blue-le">bg-dark-le</option>
                                    <option value="bg-purple-le">bg-gray-le</option>
                                    <option value="bg-pink-le">bg-red-le</option>
                                </select>
                            </div>

                            <div id="bgColorPreview" class="mt-2 p-3 text-white" style="border-radius: 5px;">
                                Preview Background Color
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" id="status" required>
                                    <option value="">Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel </button>
                    <button type="submit" class="btn btn-primary">Add Leave </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Leaves -->
<!-- Edit Leaves -->

{{-- confirm modal --}}

<!-- Credit Leave Button -->

<!-- Step 1: Select Month Modal -->
<div class="modal fade" id="credit_leaves" tabindex="-1" aria-labelledby="creditLeaveModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="creditLeaveModalLabel">Credit Leave</h5>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="creditLeaveForm">
                    <div class="mb-3">
                        <label class="form-label">Select Month</label>
                        <input type="month" class="form-control" id="creditMonth" name="month">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="nextConfirmLeaveBtn">Next</button>
            </div>
        </div>
    </div>
</div>

<!-- Step 2: Confirmation Modal -->
<div class="modal fade" id="creditLeaveConfirmModal" tabindex="-1" aria-labelledby="creditLeaveConfirmModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="creditLeaveConfirmModalLabel">Confirm Credit Leave</h5>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to credit leave for
                <strong id="confirmMonthLabel"></strong>?
                <br><br>
                This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmCreditLeaveBtn">Yes, Credit Leave</button>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="edit_leaves">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Leave Type </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="editleavetype" method="POST">
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Leave Type <span class="text-danger">* </span></label>
                                <input type="text" class="form-control" id="leave_type" name="leave_type" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Credit Condition Comp Off <span
                                        class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="holiday_off"
                                                id="holiday_offedit" value="1">
                                            <label class="form-check-label" for="holiday_off">Holiday Off</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="weekly_off"
                                                id="weekly_offedit" value="1">
                                            <label class="form-check-label" for="weekly_off">Weekly Off</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox"
                                                name="overtime_equal_shift" id="overtime_equal_shiftedit"
                                                value="1">
                                            <label class="form-check-label" for="overtime_equal_shift">Overtime Equal
                                                to
                                                Shift Hours</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3" id="holiday_off_containeredit" style="display:none;">
                            <label for="holiday_off_paid">Holiday Off Details</label>
                            <input type="text" class="form-control" id="holiday_off_paidedit"
                                name="holiday_off_details" placeholder="e.g. 1x (Holiday Pay Multiplier)" disabled>
                        </div>

                        <!-- Weekly Off Details -->
                        <div class="mt-3" id="weekly_off_containeredit" style="display:none;">
                            <label for="weekly_off_paid">Weekly Off Details</label>
                            <input type="text" class="form-control" id="weekly_off_paidedit"
                                name="weekly_off_details" placeholder="e.g. 1.5x (Weekly Off Pay Multiplier)"
                                disabled>
                        </div>

                        <!-- Overtime Details -->
                        <div class="mt-3" id="overtime_equal_shift_containeredit" style="display:none;">
                            <label for="overtime_equal_shift_paid">Overtime Details</label>
                            <input type="text" class="form-control" id="overtime_equal_shift_paidedit"
                                name="overtime_equal_shift_details" disabled>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Credit Frequency <span class="text-danger">* </span></label>
                                <select class="select" name="credit_frequency" id="credit_frequency">
                                    <option value="">Select Credit Frequency</option>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Alternate Month">Alternate Month</option>
                                    <option value="Quarterly">Quarterly</option>
                                    <option value="Half Yearly">Half Yearly</option>
                                    <option value="Yearly">Yearly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Credit Days <span class="text-danger">* </span></label>
                                <select class="select" name="credit_days" id="credit_days">
                                    <option value="">Select Credit Days</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Number of days <span class="text-danger">* </span></label>
                                <input type="text" class="form-control" id="number_of_days"
                                    name="number_of_days" />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Leave Code <span class="text-danger">* </span></label>
                                <input type="text" class="form-control" id="leave_code" name="leave_code" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Carry Forward</label>
                                <select class="form-select carry_forward_select" name="carry_forward" id="carry_forward_edit">
                                    <option value="">Select Option</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 carry_forward_days_container" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">Number of Days to Carry Forward</label>
                                <input type="number" class="form-control carry_forward_days" id="carry_forward_days_edit" name="carry_forward_days" min="0">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="leave_iconSelect" class="form-label">Select Leave Icon</label>
                                <select id="leave_iconsedit" name="leave_icon" class="form-select">
                                    <option value="">--Select an Icon--</option>
                                    <option value="ti ti-calendar-event fs-32">Annual Leave</option>
                                    <option value="ti ti-calendar-check fs-32">Paid Leave/Fixed Holiday</option>
                                    <!-- Sick Leave Icons -->
                                    <option value="ti ti-ambulance fs-32">Medical Leave</option>
                                    <option value="ti ti-pill fs-32">Sick leave</option>
                                    <!-- Medical Leave Icons -->
                                    <option value="ti ti-vaccine fs-32">Medical leave</option>
                                    <option value="ti ti-hospital fs-32">Sick Leave</option>
                                    <option value="ti ti-first-aid fs-32">Medical Leave</option>
                                    <!-- Casual Leave Icons -->
                                    <option value="ti ti-hexagon-letter-c fs-32">Casual Leave</option>
                                    <option value="ti ti-cup fs-32">Casual Leave</option>
                                    <option value="ti ti-emoji-smile fs-32">Casual Leave</option>
                                    <!-- Maternity Leave Icons -->
                                    <option value="ti ti-pregnant fs-32">Maternity Leave</option>
                                    <option value="ti ti-baby fs-32">Maternity Leave</option>
                                    <option value="ti ti-heart fs-32">Maternity Leave</option>
                                    <!-- Paternity Leave Icons -->
                                    <option value="ti ti-baby-carriage fs-32">Paternity Leave</option>
                                    <!-- Bereavement Leave Icons -->
                                    <option value="ti ti-heart-broken fs-32">Bereavement Leave</option>
                                    <option value="ti ti-cross fs-32">Bereavement Leave</option>
                                    <option value="ti ti-pray fs-32">Bereavement Leave</option>
                                    <!-- Unpaid Leave Icons -->
                                    <option value="ti ti-ban fs-32">Unpaid Leave</option>
                                </select>
                            </div>
                            <!-- Preview -->
                            <div class="mb-3">
                                <label class="form-label">Selected Icon Preview:</label>
                                <div id="iconPreview"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="leave_color" class="form-label">Leave Color</label>
                                <select id="leave_color" name="leave_color" class="form-select">
                                    <option value="">-- Select Background Color --</option>
                                    <option value="bg-black-le">bg-black-le</option>
                                    <option value="bg-blue-le">bg-dark-le</option>
                                    <option value="bg-purple-le">bg-gray-le</option>
                                    <option value="bg-pink-le">bg-red-le</option>
                                </select>
                            </div>

                            <div id="bgColorPreview" class="mt-2 p-3 text-white" style="border-radius: 5px;">
                                Preview Background Color
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="">Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel </button>
                    <button type="submit" class="btn btn-primary">Save Changes </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Delete Modal -->
<div class="modal fade" id="delete_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                    <i class="ti ti-trash-x fs-36"></i>
                </span>
                <h4 class="mb-1">Confirm Delete</h4>
                <p class="mb-3">You want to delete the marked item(s). This action cannot be undone.</p>
                <input type="hidden" id="delete_leave_id" />
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirm_delete" class="btn btn-danger">Yes, Delete</button>
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


@push('scripts')
    <script>
        $(document).on("change", ".carry_forward_select", function() {
            let value = $(this).val();
            let container = $(this).closest("form").find(".carry_forward_days_container");
            let input = $(this).closest("form").find(".carry_forward_days");

            if (value === "yes") {
                container.show();
                input.prop("disabled", false);
            } else {
                container.hide();
                input.val("").prop("disabled", true);
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            const iconSelect = document.getElementById("leave_icon");
            const iconPreview = document.getElementById("iconPreview");
            const colorSelect = document.getElementById("leave_color");
            const bgColorPreview = document.getElementById("bgColorPreview");

            if (iconSelect) {
                iconSelect.addEventListener("change", function() {
                    iconPreview.innerHTML = this.value ? `<i class="${this.value}"></i>` : "";
                });
            }

            if (colorSelect) {
                colorSelect.addEventListener("change", function() {
                    bgColorPreview.className = "mt-2 p-3 text-white";
                    if (this.value) {
                        bgColorPreview.classList.add(this.value);
                        bgColorPreview.textContent = this.value;
                    } else {
                        bgColorPreview.textContent = "Preview Background Color";
                    }
                });
            }
        });


        document.addEventListener('DOMContentLoaded', function() {
            // Get all comp-off checkboxes in edit form
            const compOffCheckboxesEdit = document.querySelectorAll(
                '#holiday_offedit, #weekly_offedit, #overtime_equal_shiftedit'
            );

            // Mapping for comp-off fields
            const fieldMapEdit = {
                'holiday_offedit': document.querySelector('#holiday_off_containeredit'),
                'weekly_offedit': document.querySelector('#weekly_off_containeredit'),
                'overtime_equal_shiftedit': document.querySelector('#overtime_equal_shift_containeredit')
            };

            // Fields to hide when any comp-off checkbox is checked
            const conditionalFieldsEdit = [
                document.querySelector('#credit_frequency_edit')?.closest('.col-md-12'),
                document.querySelector('#credit_days_edit')?.closest('.col-md-12'),
                document.querySelector('#number_of_days_edit')?.closest('.col-md-12'),
                document.querySelector('#leave_credit_type_edit')?.closest('.col-md-12'),
                document.querySelector('#carry_forward_edit')?.closest('.col-md-12')
            ].filter(Boolean);

            // Function to toggle conditional fields
            function toggleConditionalFieldsEdit() {
                const anyChecked = Array.from(compOffCheckboxesEdit).some(cb => cb.checked);

                // Hide/show other fields
                conditionalFieldsEdit.forEach(field => {
                    field.style.display = anyChecked ? 'none' : '';
                });

                // Show/hide input fields and labels based on checkbox state
                compOffCheckboxesEdit.forEach(cb => {
                    const container = fieldMapEdit[cb.id];
                    if (!container) return;
                    const input = container.querySelector('input');
                    if (cb.checked) {
                        container.style.display = 'block';
                        input.disabled = false;
                    } else {
                        container.style.display = 'none';
                        input.disabled = true;
                    }
                });
            }

            // Attach change listeners
            compOffCheckboxesEdit.forEach(cb => cb.addEventListener('change', toggleConditionalFieldsEdit));

            // Initial toggle
            toggleConditionalFieldsEdit();

            // Run again after form data is populated dynamically
            document.addEventListener('formPopulated', toggleConditionalFieldsEdit);
        });

        document.addEventListener('DOMContentLoaded', function() {
            const compOffCheckboxes = document.querySelectorAll('#holiday_off, #weekly_off, #overtime_equal_shift');

            const fieldMap = {
                'holiday_off': document.querySelector('#holiday_off_container'),
                'weekly_off': document.querySelector('#weekly_off_container'),
                'overtime_equal_shift': document.querySelector('#overtime_equal_shift_container')
            };

            // Other fields to hide when any checkbox is checked
            const conditionalFields = [
                document.querySelector('#credit_frequency')?.closest('.col-md-12'),
                document.querySelector('#credit_days')?.closest('.col-md-12'),
                document.querySelector('#number_of_days')?.closest('.col-md-12'),
                document.querySelector('#leave_credit_type')?.closest('.col-md-12'),
                document.querySelector('#carry_forward')?.closest('.col-md-12')
            ].filter(Boolean);

            function toggleConditionalFields() {
                const anyChecked = Array.from(compOffCheckboxes).some(cb => cb.checked);

                // Hide/show other fields
                conditionalFields.forEach(field => {
                    field.style.display = anyChecked ? 'none' : '';
                });

                // Show/hide input fields and labels based on checkboxes
                compOffCheckboxes.forEach(cb => {
                    const container = fieldMap[cb.id];
                    const input = container.querySelector('input');
                    if (cb.checked) {
                        container.style.display = 'block';
                        input.disabled = false;
                    } else {
                        container.style.display = 'none';
                        input.disabled = true;
                    }
                });
            }

            compOffCheckboxes.forEach(cb => cb.addEventListener('change', toggleConditionalFields));

            toggleConditionalFields();
        });



        // Column Definitions for Manage Columns feature
        const columnDefinitions = [
            { index: 0, name: 'Checkbox', visible: true },
            { index: 1, name: 'Leave Type', visible: true },
            { index: 2, name: 'Leave Days', visible: true },
            { index: 3, name: 'Credit Frequency', visible: true },
            { index: 4, name: 'Credit Days', visible: true },
            { index: 5, name: 'Leave Code', visible: true },
            { index: 6, name: 'Status', visible: true },
            { index: 7, name: 'Action', visible: true }
        ];

        // Page identifier for column preferences
        const pageIdentifier = 'leave-type';

        // Load column visibility preferences from database
        function loadColumnPreferences(callback) {
            $.ajax({
                url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug")]) }}',
                type: 'GET',
                data: {
                    page_identifier: pageIdentifier
                },
                async: false, // Make synchronous to ensure preferences are loaded before table initialization
                success: function(response) {
                    if (response.success && response.data && Array.isArray(response.data) && response.data.length > 0) {
                        try {
                            const preferences = response.data;
                            preferences.forEach(pref => {
                                // Convert string index to number
                                const prefIndex = parseInt(pref.index);
                                const col = columnDefinitions.find(c => c.index === prefIndex);
                                if (col) {
                                    // Handle both boolean and string values ("true"/"false")
                                    if (typeof pref.visible === 'string') {
                                        col.visible = pref.visible === 'true' || pref.visible === '1';
                                    } else {
                                        col.visible = pref.visible !== undefined ? Boolean(pref.visible) : true;
                                    }
                                }
                            });
                            if (callback) callback();
                        } catch (e) {
                            console.error('Error loading column preferences:', e);
                            // Fallback: show all columns
                            columnDefinitions.forEach(col => col.visible = true);
                            if (callback) callback();
                        }
                    } else {
                        // No preferences found or empty response - show all columns by default
                        columnDefinitions.forEach(col => col.visible = true);
                        if (callback) callback();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading column preferences:', error);
                    // Fallback: show all columns
                    columnDefinitions.forEach(col => col.visible = true);
                    if (callback) callback();
                }
            });
        }

        // Save column preferences to database
        function saveColumnPreferences() {
            $.ajax({
                url: '{{ route("settings.column.preferences.save", ["box_slug" => request()->route("box_slug")]) }}',
                type: 'POST',
                data: {
                    page_identifier: pageIdentifier,
                    column_preferences: columnDefinitions,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Column preferences saved successfully');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saving column preferences:', error);
                }
            });
        }

        // Global table variable
        let leaveTable;

        // Populate columns modal
        function populateColumnsModal() {
            const columnsList = $('#columnsList');
            columnsList.empty();

            columnDefinitions.forEach(col => {
                const listItem = $(`
                    <div class="list-group-item">
                        <div class="form-check">
                            <input class="form-check-input column-toggle" type="checkbox"
                                   data-column-index="${col.index}"
                                   id="col_${col.index}"
                                   ${col.visible ? 'checked' : ''}>
                            <label class="form-check-label" for="col_${col.index}">
                                ${col.name}
                            </label>
                        </div>
                    </div>
                `);
                columnsList.append(listItem);
            });
        }

        $(document).ready(function() {
            // Load preferences before initializing table
            loadColumnPreferences(function() {
                leaveTable = initDataTable();

                // Setup functions after table is initialized
            setupFormSubmissions(leaveTable);
            setupEditAction(leaveTable);
            setupDeleteAction(leaveTable);
            });

            setupToggleStatus();
            setupLiveValidationClear();
            setupCreditLeaveFlow();

            // Open Manage Columns Modal
            $('#manageColumnsModal').on('show.bs.modal', function() {
                populateColumnsModal();
            });

            // Show All Columns
            $('#showAllColumns').on('click', function() {
                $('.column-toggle').prop('checked', true);
            });

            // Hide All Columns
            $('#hideAllColumns').on('click', function() {
                $('.column-toggle').prop('checked', false);
            });

            // Apply Column Changes
            $('#applyColumnChanges').on('click', function() {
                $('.column-toggle').each(function() {
                    const columnIndex = parseInt($(this).data('column-index'));
                    const isVisible = $(this).is(':checked');
                    const col = columnDefinitions.find(c => c.index === columnIndex);
                    if (col) {
                        col.visible = isVisible;
                    }
                });

                saveColumnPreferences();

                // Apply column visibility and redraw table immediately
                if (leaveTable) {
                    columnDefinitions.forEach(col => {
                        try {
                            leaveTable.column(col.index).visible(col.visible, false);
                        } catch (e) {
                            console.error('Error applying column visibility:', e);
                        }
                    });
                    leaveTable.columns.adjust();
                    // Redraw only once after all changes
                    leaveTable.draw(false);
                }

                // Close modal
                $('#manageColumnsModal').modal('hide');
            });
        });

        function initDataTable() {
            return $('#leaveTypesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('leave-types.data', ['box_slug' => request()->route('box_slug')]) }}',
                    type: 'GET',
                },
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        visible: columnDefinitions[0].visible,
                        render: (data) => `
                        <div class="form-check form-check-md">
                            <input class="form-check-input" type="checkbox" value="${data.id}" />
                        </div>`
                    },
                    {
                        data: 'leave_type',
                        name: 'leave_type',
                        visible: columnDefinitions[1].visible
                    },
                    {
                        data: 'number_of_days',
                        name: 'number_of_days',
                        visible: columnDefinitions[2].visible
                    },
                    {
                        data: 'credit_frequency',
                        name: 'credit_frequency',
                        visible: columnDefinitions[3].visible
                    },
                    {
                        data: 'credit_days',
                        name: 'credit_days',
                        visible: columnDefinitions[4].visible
                    },
                    {
                        data: 'leave_code',
                        name: 'leave_code',
                        visible: columnDefinitions[5].visible
                    },
                    {
                        data: 'status',
                        name: 'status',
                        visible: columnDefinitions[6].visible,
                        render: (data, type, row) => {
                            const btnClass = data === 'Active' ? 'btn-success' : 'btn-danger';
                            const newStatus = data === 'Active' ? 'Inactive' : 'Active';
                            return `
                            <button class="btn btn-sm ${btnClass} toggle-status-btn"
                                    data-id="${row.id}"
                                    data-status="${newStatus}">
                                <i class="ti ti-point-filled"></i> ${data}
                            </button>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        visible: columnDefinitions[7].visible,
                        render: (data) => `
                        <div class="action-icon d-inline-flex">
                            <a href="#" class="me-2 edit-leave" data-id="${data.id}" data-bs-toggle="modal" data-bs-target="#edit_leaves">
                                <i class="ti ti-edit"></i>
                            </a>
                            <a href="#" class="delete-leave" data-id="${data.id}" data-bs-toggle="modal" data-bs-target="#delete_modal">
                                <i class="ti ti-trash"></i>
                            </a>
                        </div>`
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                lengthMenu: [10, 25, 50],
            });
        }

        function setupToggleStatus() {
            $(document).on('click', '.toggle-status-btn', function() {
                const button = $(this);
                $.ajax({
                    url: '{{ route('toggle.status.leave.typpe', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'POST',
                    data: {
                        id: button.data('id'),
                        status: button.data('status'),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#leaveTypesTable').DataTable().ajax.reload(null, false);
                        toastr.success(response.message);
                    },
                    error: () => toastr.error('Failed to change status.')
                });
            });
        }

        function setupFormSubmissions(leaveTable) {
            $('#addleave_type').on('submit', function(e) {
                e.preventDefault();
                if (!validateFormFields(this)) return;
                const formData = new FormData(this);

                sendAjaxForm(
                    "{{ route('leave-types.store', ['box_slug' => request()->route('box_slug')]) }}",
                    'POST',
                    formData,
                    () => {
                        this.reset();
                        $('#add_leaves').modal('hide');
                        leaveTable.ajax.reload(null, false);
                    }
                );
            });

            $('#editleavetype').on('submit', function(e) {
                e.preventDefault();
                if (!validateFormFields(this)) return;

                sendAjaxForm(
                    "{{ route('leave-types.update', ['box_slug' => request()->route('box_slug')]) }}",
                    'POST',
                    $(this).serialize(),
                    () => {
                        $('#edit_leaves').modal('hide');
                        leaveTable.ajax.reload(null, false);
                    }
                );
            });
        }

        function validateFormFields(form) {
            let isValid = true;
            $(form).find('.is-invalid').removeClass('is-invalid');
            $(form).find('.validation-error').remove();

            const required = ['leave_type', 'leave_code', 'status'];
            required.forEach(id => {
                const $field = $(form).find(`#${id}`);
                if (!$field.val()?.trim()) {
                    isValid = false;
                    $field.addClass('is-invalid')
                        .after('<div class="text-danger mt-1 validation-error">This field is required.</div>');
                }
            });

            return isValid;
        }

        function setupLiveValidationClear() {
            $(document).on('input change', 'input, select', function() {
                $(this).removeClass('is-invalid').next('.validation-error').remove();
            });
        }

        function sendAjaxForm(url, method, data, onSuccess) {
            $.ajax({
                url,
                type: method,
                data,
                processData: !(data instanceof FormData),
                contentType: data instanceof FormData ? false : 'application/x-www-form-urlencoded; charset=UTF-8',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    showToast(response.message || 'Operation successful!', 'success');
                    if (onSuccess) onSuccess();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors || {};
                        let msg = '';
                        Object.keys(errors).forEach(function(key) {
                            if (Array.isArray(errors[key])) {
                                msg += errors[key].join('\n') + '\n';
                            } else {
                                msg += errors[key] + '\n';
                            }
                        });
                        if (xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message + '\n' + msg;
                        }
                        showToast(msg.trim(), 'error');
                    } else {
                        showToast('Something went wrong. Please try again.', 'error');
                    }
                }
            });
        }



        function showToast(message, type = 'success') {
            const $toast = $('#dynamicToast');
            const $message = $('#dynamicToastMessage');
            $message.text(message);
            $toast.removeClass('bg-success bg-danger bg-warning').addClass(
                type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-warning'
            );
            bootstrap.Toast.getOrCreateInstance($toast[0]).show();
        }

        function setupEditAction(leaveTable) {
            $(document).on('click', '.edit-leave', function(e) {
                e.preventDefault();

                const rowData = leaveTable.row($(this).closest('tr')).data();
                if (!rowData) return alert('Failed to get leave data.');

                const $form = $('#editleavetype');
                $form[0].reset();
                $form.find('input[name="leave_id"]').remove();

                // Populate standard fields
                $form.find('#leave_type').val(rowData.leave_type);
                $form.find('#credit_frequency').val(rowData.credit_frequency).trigger('change');
                $form.find('#credit_days').val(rowData.credit_days).trigger('change');
                $form.find('#number_of_days').val(rowData.number_of_days);
                $form.find('#leave_code').val(rowData.leave_code);
                $form.find('#status').val(rowData.status).trigger('change');
                $form.find('#leave_color').val(rowData.leave_color).trigger('change');
                $form.find('#leave_iconsedit').val(rowData.leave_icon).trigger('change');
                $form.append(`<input type="hidden" name="leave_id" value="${rowData.id}">`);
                // ---- Carry Forward Logic ----
$form.find('#carry_forward_edit').val(rowData.carry_forward).trigger('change');

if (rowData.carry_forward === 'yes') {
    $form.find('#carry_forward_days_container').show();
    $form.find('#carry_forward_days_edit').val(rowData.carry_forward_days);
} else {
    $form.find('#carry_forward_days_container').hide();
    $form.find('#carry_forward_days_edit').val('');
}

                // Map all comp-off fields (with your corrected IDs)
                const compOffFields = [{
                        key: 'holiday_off',
                        cb: '#holiday_offedit',
                        input: '#holiday_off_paidedit',
                        container: '#holiday_off_containeredit'
                    },
                    {
                        key: 'weekly_off',
                        cb: '#weekly_offedit',
                        input: '#weekly_off_paidedit',
                        container: '#weekly_off_containeredit'
                    },
                    {
                        key: 'overtime_equal_shift',
                        cb: '#overtime_equal_shiftedit',
                        input: '#overtime_equal_shift_paidedit',
                        container: '#overtime_equal_shift_containeredit'
                    }
                ];

                // Reset comp-off containers
                compOffFields.forEach(f => {
                    const $cb = $form.find(f.cb);
                    const $input = $form.find(f.input);
                    const $container = $form.find(f.container);
                    $cb.prop('checked', false);
                    $input.val('').prop('disabled', true);
                    $container.hide();
                });

                // Populate based on DB data
                let anyChecked = false;
                compOffFields.forEach(f => {
                    if (rowData[f.key] == 1) {
                        anyChecked = true;
                        const $cb = $form.find(f.cb);
                        const $input = $form.find(f.input);
                        const $container = $form.find(f.container);
                        $cb.prop('checked', true);
                        $container.show();
                        $input.prop('disabled', false).val(rowData[`${f.key}_details`] || '');
                    }
                });

                // Credit-related fields to toggle
                const creditRelatedFields = [
                    '#credit_frequency',
                    '#credit_days',
                    '#number_of_days',
                    '#leave_credit_type',
                    '#carry_forward'
                ];

                // Function to toggle credit fields visibility
                function toggleCreditFields() {
                    const anyCheckedNow =
                        $('#holiday_offedit').is(':checked') ||
                        $('#weekly_offedit').is(':checked') ||
                        $('#overtime_equal_shiftedit').is(':checked');

                    creditRelatedFields.forEach(sel => {
                        const $field = $form.find(sel).closest('.col-md-12');
                        $field.css('display', anyCheckedNow ? 'none' : '');
                    });
                }

                // Initial state (from DB)
                toggleCreditFields();

                // Bind live toggle listeners
                $('#holiday_offedit, #weekly_offedit, #overtime_equal_shiftedit')
                    .off('change')
                    .on('change', function() {
                        const id = $(this).attr('id');
                        let inputSel, containerSel;

                        if (id === 'holiday_offedit') {
                            inputSel = '#holiday_off_paidedit';
                            containerSel = '#holiday_off_containeredit';
                        } else if (id === 'weekly_offedit') {
                            inputSel = '#weekly_off_paidedit';
                            containerSel = '#weekly_off_containeredit';
                        } else {
                            inputSel = '#overtime_equal_shift_paidedit';
                            containerSel = '#overtime_equal_shift_containeredit';
                        }

                        const $input = $form.find(inputSel);
                        const $container = $form.find(containerSel);

                        if ($(this).is(':checked')) {
                            $container.show();
                            $input.prop('disabled', false);
                        } else {
                            $container.hide();
                            $input.prop('disabled', true).val('');
                        }
                        toggleCreditFields();
                    });
            });
        }

        function setupDeleteAction(leaveTable) {
            $(document).on('click', '.delete-leave', function(e) {
                e.preventDefault();
                $('#delete_leave_id').val($(this).data('id'));
            });
            $('#confirm_delete').on('click', function() {
                const leaveId = $('#delete_leave_id').val();
                if (!leaveId) return alert('No leave type selected for deletion.');

                $.ajax({
                    url: "{{ route('leave-types.destroy', ['box_slug' => request()->route('box_slug')]) }}",
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        id: leaveId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        showToast(response.message || 'Leave type deleted successfully!', 'success');
                        $('#delete_modal').modal('hide');
                        leaveTable.ajax.reload(null, false);
                    },
                    error: () => showToast('Failed to delete leave type.', 'error')
                });
            });
        }

        function setupCreditLeaveFlow() {
            $('#nextConfirmLeaveBtn').on('click', function() {
                const month = $('#creditMonth').val();
                if (!month) return alert('Please select a month before continuing.');

                const [year, monthNum] = month.split('-');
                const monthName = new Date(year, monthNum - 1).toLocaleString('default', {
                    month: 'long'
                });

                $('#confirmMonthLabel').text(`${monthName} ${year}`);
                $('#credit_leaves').modal('hide');
                $('#creditLeaveConfirmModal').modal('show');
            });

            $('#confirmCreditLeaveBtn').on('click', function() {
                const month = $('#creditMonth').val();
                $.ajax({
                    url: '{{ route('credit.leaves', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        month
                    },
                    success: function(res) {
                        if (res.status) {
                            alert(res.message || 'Leave credited successfully!');
                            $('#creditLeaveConfirmModal').modal('hide');
                            $('#creditLeaveForm')[0].reset();
                        } else {
                            alert('Failed: ' + (res.message || 'Unknown error.'));
                        }
                    },
                    error: (xhr) => alert(xhr.responseJSON?.message || 'Something went wrong')
                });
            });
        }
    </script>
@endpush


@endsection
