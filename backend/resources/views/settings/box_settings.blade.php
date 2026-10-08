@extends('layouts.main')
@section('content')
@section('title', 'Box Settings')
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                @include('settings.partials.nav')

                <div class="row">
                    <div class="col-xl-3 col-lg-12 theiaStickySidebar">
                        @include('settings.partials.sidebar')
                    </div>
                    <div class="col-xl-9 col-lg-12">

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Set Work Time & Notification</h5>
                    </div>
                    <div class="card-body">
                        <form id="work-time-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Slug <span class="text-danger">*</span></label>
                                        <input type="text" name="slug" id="slug" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Send Reminder Before <span
                                                class="text-danger">*</span></label>
                                        <select class="select2" name="reminder_before" id="reminder_before">
                                            <option value="5">5 Min</option>
                                            <option value="10">10 Min</option>
                                            <option value="15">15 Min</option>
                                            <option value="40">40 Min</option>
                                            <option value="55">55 Min</option>
                                            <option value="60">1 Hour</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">Daily Reminder Time <span
                                            class="text-danger">*</span></label>
                                    <div class="mb-3 icon-form">
                                        <span class="form-icon"><i class="ti ti-calendar-check"></i></span>
                                        <input type="text" id="daily_reminder_time" name="daily_reminder_time"
                                            class="form-control timepicker">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Set Radius Tracking (Meters)</label>
                                        <input type="text" name="customer_location_radius" id="customer_location_radius" class="form-control" value="30" min="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Lead Unique Field</label>
                                        <select class="select2" name="lead_required_field" id="lead_required_field">
                                            <option selected disabled>-- Select --</option>
                                            <option value="email">Email</option>
                                            <option value="phone">Phone</option>
                                            <option value="both">Both (Email & Phone)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Working Days <span
                                                class="text-danger">*</span></label>
                                        <select class="select2" multiple name="working_days[]" id="working_days">
                                            <option value="Sunday">Sunday</option>
                                            <option value="Monday">Monday</option>
                                            <option value="Tuesday">Tuesday</option>
                                            <option value="Wednesday">Wednesday</option>
                                            <option value="Thursday">Thursday</option>
                                            <option value="Friday">Friday</option>
                                            <option value="Saturday">Saturday</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Round Robin Lead Allocation <span
                                                class="text-danger">*</span></label>
                                        <select class="select2" name="round_robin" id="round_robin">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6" id="round_robin_users_group" style="display: none;">
                                    <div class="mb-3">
                                        <label class="col-form-label">Round Robin Users</label>
                                        <select class="select2" multiple name="round_robin_users[]"
                                            id="round_robin_users">
                                            <option value="Shitender (shitender08@gmail.com)">Shitender
                                                (shitender08@gmail.com)</option>
                                            <option value="Demo (demo@bizzdigital.in)">Demo (demo@bizzdigital.in)
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Can user use admin mail <span
                                                class="text-danger">*</span></label>
                                        <select class="select2" name="can_use_admin_mail" id="can_use_admin_mail">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6" id="admin_mail_users_group" style="display: none;">
                                    <div class="mb-3">
                                        <label class="col-form-label">Who can use admin emails</label>
                                        <select class="select2" multiple name="admin_mail_users[]"
                                            id="admin_mail_users">
                                            <option value="Shitender (shitender08@gmail.com)">Shitender
                                                (shitender08@gmail.com)</option>
                                            <option value="Demo (demo@bizzdigital.in)">Demo (demo@bizzdigital.in)
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">Day Start Time <span
                                            class="text-danger">*</span></label>
                                    <div class="mb-3 icon-form">
                                        <span class="form-icon"><i class="ti ti-calendar-check"></i></span>
                                        <input type="text" id="day_start_time" name="day_start_time"
                                            class="form-control timepicker">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">Day End Time <span
                                            class="text-danger">*</span></label>
                                    <div class="mb-3 icon-form">
                                        <span class="form-icon"><i class="ti ti-calendar-check"></i></span>
                                        <input type="text" id="day_end_time" name="day_end_time"
                                            class="form-control timepicker">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Bulk Lead Assignment Notification / Email <span
                                                class="text-danger">*</span></label>
                                        <select class="select2" name="bulk_lead_assignment"
                                            id="bulk_lead_assignment">
                                            <option value="Disabled">Disabled</option>
                                            <option value="Enable Only Notification">Enable Only Notification</option>
                                            <option value="Enable Only Email">Enable Only Email</option>
                                            <option value="Enable Both">Enable Both</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Lead Assignment Notification / Email <span
                                                class="text-danger">*</span></label>
                                        <select class="select2" name="lead_assignment" id="lead_assignment">
                                            <option value="Disabled">Disabled</option>
                                            <option value="Enable Only Notification">Enable Only Notification</option>
                                            <option value="Enable Only Email">Enable Only Email</option>
                                            <option value="Enable Both">Enable Both</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Change Lead Assigned to if Admin Lead Owner <span
                                                class="text-danger">*</span></label>
                                        <select class="select2" name="change_lead_assigned_if_admin"
                                            id="change_lead_assigned_if_admin">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Change Lead Status if Admin Lead Owner <span
                                                class="text-danger">*</span></label>
                                        <select class="select2" name="change_lead_status_if_admin"
                                            id="change_lead_status_if_admin">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Change Lead Source if Admin Lead Owner <span
                                                class="text-danger">*</span></label>
                                        <select class="select2" name="change_lead_source_if_admin"
                                            id="change_lead_source_if_admin">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Activity Reminder Notification / Email</label>
                                        <select class="select2" name="activity_reminder" id="activity_reminder">
                                            <option value="Disabled">Disabled</option>
                                            <option value="Enable Only Notification">Enable Only Notification</option>
                                            <option value="Enable Only Email">Enable Only Email</option>
                                            <option value="Enable Both">Enable Both</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Allow Duplicate Manual Leads <span
                                                class="text-danger">*</span></label>
                                        <select class="select2" name="allow_duplicate_leads"
                                            id="allow_duplicate_leads">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Auto Allow Duplicate Leads <span
                                                class="text-danger">*</span></label>
                                        <select class="select2" name="allow_duplicate_leads_auto"
                                            id="allow_duplicate_leads_auto">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Enable Auto Close Task <span
                                                class="text-danger">*</span></label>
                                        <select class="select2" name="auto_task_close" id="auto_task_close">
                                            <option value="No">No</option>
                                            <option value="Yes">Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6" id="auto_task_close_days_group" style="display: none;">
                                    <div class="mb-3">
                                        <label class="col-form-label">Auto Close Task in days after due date <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="auto_task_close_days" id="auto_task_close_days"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Custom Employe Id<span
                                                class="text-danger">*</span></label>
                                        <select class="select2" name="dynamic_employee_id" id="dynamic_employee_id">
                                            <option value="No">No</option>
                                            <option value="Yes">Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label">Last Task Calculation Time</label>
                                    <div class="mb-3 icon-form">
                                        <span class="form-icon"><i class="ti ti-calendar-check"></i></span>
                                        <input type="text" id="last_task_calculation_time" name="last_task_calculation_time"
                                            class="form-control timepicker">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Allowed IP (X.X.X.X and press enter) <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="allowed_ip" id="allowed_ip"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <h4 class="text-end">Your Current IP - {{ request()->ip() }}</h4>
                                </div>
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>


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

<div class="modal fade" id="confirmDefaultEmployeeIdModal" tabindex="-1"
    aria-labelledby="confirmDefaultEmployeeIdModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDefaultEmployeeIdModalLabel">Confirm Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to switch to the default Employee ID format?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelChangeEmployeeId">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmChangeEmployeeId">Yes, Change</button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Employee ID Modal -->
<div class="modal fade" id="customEmployeeIdModal" tabindex="-1" aria-labelledby="customEmployeeIdModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form id="customEmployeeIdForm" class="modal-content" novalidate>
            <div class="modal-header">
                <h5 class="modal-title" id="customEmployeeIdModalLabel">Custom Employee ID</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Please enter a unique and valid Employee ID.</p>
                <div class="mb-3">
                    <label for="customEmployeeIdInput" class="form-label">Employee ID <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="customEmployeeIdInput" name="custom_employee_id"
                        required minlength="2" placeholder="Enter custom Employee ID" />
                    <div class="invalid-feedback">
                        Employee ID is required and must be at least 2 characters.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Employee ID</button>
            </div>
        </form>
    </div>
</div>
<select id="dynamic_employee_id" name="dynamic_employee_id" class="form-select">
    <option value="No">No</option>
    <option value="Yes">Yes</option>
</select>

<input type="hidden" id="custom_employee_id_hidden" name="custom_employee_id" value="">
<div class="modal fade" id="existingEmployeePrefixModal" tabindex="-1"
    aria-labelledby="existingEmployeePrefixModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="existingEmployeePrefixModalLabel">Confirm Employee ID Prefix</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>The current Employee ID prefix is: <strong id="employeePrefixDisplay"></strong></p>
                <p>Do you want to proceed with this change?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelPrefixChange">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmPrefixChange">Proceed</button>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        $(document).ready(function() {
            const form = $('#work-time-form');

            function toggleVisibility(selector, expectedValue, targetSelector) {
                const value = $(selector).val();
                $(targetSelector).toggle(value == expectedValue);
            }

            function handleFormToggles() {
                toggleVisibility('#round_robin', '1', '#round_robin_users_group');
                toggleVisibility('#can_use_admin_mail', '1', '#admin_mail_users_group');
                toggleVisibility('#auto_task_close', 'Yes', '#auto_task_close_days_group');
            }

            // Set values in multi-select
            function setMultiSelect(selector, values) {
                if (!Array.isArray(values)) values = [];
                $(selector).val(values).trigger('change.select2');
            }

            function updateEmployeeId(data, callback) {
                data._token = '{{ csrf_token() }}';
                $.ajax({
                    url: "{{ route('updateEmployeeId', ['box_slug' => request()->route('box_slug')]) }}",
                    method: 'PUT',
                    data,
                    success: callback,
                    error: function() {
                        alert('Server error on updating Employee ID.');
                        callback({
                            status: false
                        });
                    }
                });
            }

            function revertSelect() {
                ignoreDynamicEmployeeIdChange = true;
                $('#dynamic_employee_id').val(previousDynamicEmployeeIdValue).trigger('change.select2');
                ignoreDynamicEmployeeIdChange = false;
            }

            // Load settings from server
            function loadWorkTimeSettings() {
                $.get("{{ route('settings.worktime.get', ['box_slug' => request()->route('box_slug')]) }}",
                    function(response) {
                        if (response.success) {
                            const settings = response.data;
                            $('#name').val(settings.box_settings?.name ?? '');
                            $('#slug').val(settings.box_settings?.slug ?? '');
                            $('#reminder_before').val(settings.box_settings?.reminder_before ?? '').trigger('change');
                            $('#customer_location_radius').val(settings.box_settings?.customer_location_radius ?? '30');
                            $('#daily_reminder_time').val(settings.box_settings?.daily_reminder_time ?? '');
                            $('#lead_required_field').val(settings.box_settings?.lead_required_field ?? '')
                                .trigger('change');
                            $('#round_robin').val(settings.box_settings?.round_robin ?? '0').trigger('change');
                            $('#can_use_admin_mail').val(settings.box_settings?.can_use_admin_mail ? '1' : '0')
                                .trigger('change');
                            $('#auto_task_close').val(settings.box_settings?.auto_task_close === 'Yes' ? 'Yes' :
                                'No').trigger('change');
                            $('#day_start_time').val(settings.box_settings?.day_start_time ?? '');
                            $('#day_end_time').val(settings.box_settings?.day_end_time ?? '');
                            $('#last_task_calculation_time').val(settings.box_settings?.last_task_calculation_time ?? '');
                            $('#bulk_lead_assignment').val(settings.box_settings?.bulk_lead_assignment ?? '')
                                .trigger('change');
                            $('#lead_assignment').val(settings.box_settings?.lead_assignment ?? '').trigger(
                                'change');
                            $('#change_lead_assigned_if_admin').val(settings.box_settings
                                ?.change_lead_assigned_if_admin ?? '').trigger('change');
                            $('#change_lead_status_if_admin').val(settings.box_settings
                                ?.change_lead_status_if_admin ?? '').trigger('change');
                            $('#change_lead_source_if_admin').val(settings.box_settings
                                ?.change_lead_source_if_admin ?? '').trigger('change');
                            $('#activity_reminder').val(settings.box_settings?.activity_reminder ?? '').trigger(
                                'change');
                            $('#allow_duplicate_leads').val(settings.box_settings?.allow_duplicate_leads ?? '0')
                                .trigger('change');
                            $('#allow_duplicate_leads_auto').val(settings.box_settings?.allow_duplicate_leads_auto ?? '0')
                                .trigger('change');
                            $('#auto_task_close_days').val(settings.box_settings?.auto_task_close_days ?? '');

                            const dynamicEmployeeId = settings.box_settings?.dynamic_employee_id;
                            if (dynamicEmployeeId && dynamicEmployeeId !== '0') {
                                $('#dynamic_employee_id').val('Yes').trigger('change.select2');
                                previousDynamicEmployeeIdValue = 'Yes';
                            } else {
                                $('#dynamic_employee_id').val('No').trigger('change.select2');
                                previousDynamicEmployeeIdValue = 'No';
                            }
                            const rrSelect = $('#round_robin_users');
                            const adminMailSelect = $('#admin_mail_users');
                            rrSelect.empty();
                            adminMailSelect.empty();
                            const users = settings.box_settings?.users ?? [];
                            if (users.length > 0) {
                                users.forEach(user => {
                                    const label =
                                        `${user.first_name ?? 'User'} (${user.username ?? ''})`;
                                    rrSelect.append(new Option(label, label));
                                    adminMailSelect.append(new Option(label, label));
                                });
                            } else {
                                rrSelect.append(new Option('No users found', ''));
                                adminMailSelect.append(new Option('No users found', ''));
                            }

                            setMultiSelect('#round_robin_users', settings.box_settings?.round_robin_users ??
                        []);
                            setMultiSelect('#admin_mail_users', settings.box_settings?.admin_mail_users ?? []);
                            const validWorkingDays = [
                                "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
                            ];
                            const selectedDays = settings.box_settings?.working_days ?? [];
                            const filteredDays = selectedDays.filter(day => validWorkingDays.includes(day));
                            setMultiSelect('#working_days', filteredDays);
                            handleFormToggles();
                        } else {
                            console.error('Failed to load box settings');
                        }
                    }).fail(function(xhr) {
                    console.error("Error loading data:", xhr.responseText);
                });
            }

            // Clear form (optional utility)
            function clearForm() {
                form[0].reset();
                setMultiSelect('#round_robin_users', []);
                setMultiSelect('#admin_mail_users', []);
                setMultiSelect('#working_days', []);
                handleFormToggles();
            }

            // Handle form submission
            form.on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('settings.worktime.save', ['box_slug' => request()->route('box_slug')]) }}",
                    method: "POST",
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success) {
                            showToast('Work time settings saved successfully!');
                            loadWorkTimeSettings();
                        } else {
                            showToast('Something went wrong.', true);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        showToast('Error saving data.', true);
                    }
                });
            });

            // Show toast
            function showToast(message, isError = false) {
                $('#dynamicToastMessage').text(message);
                const toast = new bootstrap.Toast(document.getElementById('dynamicToast'));
                $('#dynamicToast').removeClass('bg-danger bg-success').addClass(isError ? 'bg-danger' :
                    'bg-success');
                toast.show();
            }

            // Watch for dynamic toggles
            $('#round_robin, #can_use_admin_mail, #auto_task_close').on('change', handleFormToggles);

            // Init Select2
            $('.select2').select2({
                width: '100%'
            });

            let previousDynamicEmployeeIdValue = $('#dynamic_employee_id').val();
            let employeeIdChangeConfirmed = false;
            let ignoreDynamicEmployeeIdChange = false;

            // --- Event Handlers ---

            // Updated change handler to block immediate value change and show modals instead
            $('#dynamic_employee_id').on('change', function() {
                if (ignoreDynamicEmployeeIdChange) return;

                const selectedValue = $(this).val();

                // Revert select back immediately to previous value to block change
                ignoreDynamicEmployeeIdChange = true;
                $('#dynamic_employee_id').val(previousDynamicEmployeeIdValue).trigger('change.select2');
                ignoreDynamicEmployeeIdChange = false;

                // Show appropriate modal
                if (selectedValue === 'Yes') {
                    new bootstrap.Modal('#customEmployeeIdModal').show();
                } else if (selectedValue === 'No') {
                    employeeIdChangeConfirmed = false;
                    new bootstrap.Modal('#confirmDefaultEmployeeIdModal').show();
                }
            });

            // Confirm reverting to default employee ID (No)
            $('#confirmChangeEmployeeId').on('click', function() {
                employeeIdChangeConfirmed = true;
                bootstrap.Modal.getInstance(document.getElementById('confirmDefaultEmployeeIdModal'))
            .hide();

                updateEmployeeId({
                    dynamic_employee_id: 0
                }, function(res) {
                    if (res.status) {
                        previousDynamicEmployeeIdValue = 'No';

                        $('#employeePrefixDisplay').text(res.saved_prefix || 'N/A');
                        new bootstrap.Modal('#existingEmployeePrefixModal').show();

                        // Update select after success
                        ignoreDynamicEmployeeIdChange = true;
                        $('#dynamic_employee_id').val('No').trigger('change.select2');
                        ignoreDynamicEmployeeIdChange = false;
                    } else {
                        alert('Error updating Employee ID.');
                        revertSelect();
                    }
                });
            });

            // Cancel reverting to default employee ID (No)
            $('#cancelChangeEmployeeId').on('click', function() {
                employeeIdChangeConfirmed = false;
                bootstrap.Modal.getInstance(document.getElementById('confirmDefaultEmployeeIdModal'))
            .hide();
                revertSelect();
            });

            $('#confirmDefaultEmployeeIdModal').on('hidden.bs.modal', function() {
                if (!employeeIdChangeConfirmed) {
                    revertSelect();
                }
            });

            // Submit custom employee ID (Yes)
            $('#customEmployeeIdForm').on('submit', function(e) {
                e.preventDefault();
                const customId = $('#customEmployeeIdInput').val().trim();
                if (customId.length < 2) {
                    $('#customEmployeeIdInput').addClass('is-invalid');
                    return;
                }
                $('#customEmployeeIdInput').removeClass('is-invalid');

                updateEmployeeId({
                    dynamic_employee_id: 1,
                    custom_employee_id: customId
                }, function(res) {
                    if (res.status) {
                        previousDynamicEmployeeIdValue = 'Yes';

                        $('#custom_employee_id_hidden').val(customId);
                        $('#employeePrefixDisplay').text(customId);
                        bootstrap.Modal.getInstance(document.getElementById(
                            'customEmployeeIdModal')).hide();
                        new bootstrap.Modal('#existingEmployeePrefixModal').show();

                        // Update select after success
                        ignoreDynamicEmployeeIdChange = true;
                        $('#dynamic_employee_id').val('Yes').trigger('change.select2');
                        ignoreDynamicEmployeeIdChange = false;
                    } else {
                        alert('Error updating Employee ID.');
                        revertSelect();
                    }
                });
            });

            $('#confirmPrefixChange').on('click', function() {
                bootstrap.Modal.getInstance(document.getElementById('existingEmployeePrefixModal')).hide();
                updateEmployeeId({
                    change_dynamic_prefix: 1
                }, function(res) {
                    if (res.status) {
                        alert('Employee ID prefix changed successfully!');
                    } else {
                        alert('Failed to update change_dynamic_prefix flag.');
                    }
                });
            });

            $('#cancelPrefixChange').on('click', function() {
                bootstrap.Modal.getInstance(document.getElementById('existingEmployeePrefixModal')).hide();
                revertSelect();
            });
            // Initial load
            loadWorkTimeSettings();
        });
    </script>
@endpush


@endsection
