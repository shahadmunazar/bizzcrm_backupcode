<!-- Add New Contact Stage -->
		<div class="modal fade" id="global_add_contact_stage" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add New Contact Stage</h5>
						<button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form id="global_add_contact_stageForm" method="POST">
						<div class="modal-body">
							<div class="mb-3">
								<label class="col-form-label">Contact Stage <span class="text-danger">*</span></label>
								<input type="text" class="form-control" name="stage" >
							</div>
							<div class="mb-3">
								<label class="col-form-label">Color</label>
								<input type="color" class="form-control form-control-color" name="color" value="#000000" title="Choose color">
							</div>
							<div class="mb-3">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" name="is_task_priority" id="global_add_is_task_priority" value="1">
									<label class="form-check-label" for="global_add_is_task_priority">
										Create Task with Priority
									</label>
								</div>
							</div>
							<div class="mb-3" id="global_add_priority_wrapper" style="display: none;">
								<div class="d-flex justify-content-between align-items-center">
									<label class="col-form-label">Task Priority</label>
									<a href="javascript:void(0);" class="fs-12 select-all-priorities" data-target="global_add_priority_select">Select All</a>
								</div>
								<select class="form-control select2-priority" id="global_add_priority_select" name="task_priority[]" multiple="multiple">
									<option value="Low">Low</option>
									<option value="Medium">Medium</option>
									<option value="High">High</option>
								</select>
							</div>
							<div class="row">
								<div class="col-md-6 mb-3">
									<label class="col-form-label">Priority</label>
									<input type="number" class="form-control" name="priority" value="0">
								</div>
								<div class="col-md-6 mb-3">
									<label class="col-form-label">Reminder Mandatory</label>
									<select class="form-select" name="reminder_mandatory">
										<option value="No">No</option>
										<option value="Yes">Yes</option>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6 mb-3">
									<label class="col-form-label">Notification</label>
									<select class="form-select" name="notification">
										<option value="No">No</option>
										<option value="Yes">Yes</option>
									</select>
								</div>
								<div class="col-md-6 mb-3">
									<label class="col-form-label">Is Default</label>
									<select class="form-select" name="is_default">
										<option value="No">No</option>
										<option value="Yes">Yes</option>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6 mb-3">
									<label class="col-form-label">Is Final</label>
									<select class="form-select" name="is_final">
										<option value="No">No</option>
										<option value="Yes">Yes</option>
									</select>
								</div>
								<div class="col-md-6 mb-3">
									<label class="col-form-label">Display Amount Field</label>
									<select class="form-select" name="display_amount_field">
										<option value="No">No</option>
										<option value="Yes">Yes</option>
									</select>
								</div>
								<div class="col-md-6 mb-3">
									<label class="col-form-label">No Activity Alert</label>
									<div class="input-group">
										<input type="number" class="form-control" name="no_activity_alert" value="0">
										<select class="form-select" name="no_activity_alert_unit" style="max-width: 100px;">
											<option value="Days">Days</option>
											<option value="Hours">Hours</option>
											<option value="Minutes">Minutes</option>
										</select>
									</div>
								</div>
							</div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
									<label class="col-form-label">Requires Approval</label>
									<select class="form-select" name="requires_approval" id="global_add_requires_approval">
										<option value="0">No</option>
										<option value="1">Yes</option>
									</select>
								</div>
                            </div>
                            <div class="row" id="global_add_approval_filters" style="display: none;">
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Approval Department</label>
                                    <select class="form-select" id="global_add_approval_department" name="approval_department_id">
                                        <option value="">Select Department</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Approval Designation</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="global_add_approval_designation_visible" placeholder="Select Designation" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                        <input type="hidden" id="global_add_approval_designation" name="approval_designation_id">
                                        <button class="btn btn-outline-secondary" type="button" onclick="if($('#global_add_approval_department').val()) { window.openDesignationTree($('#global_add_approval_department').val(), '#global_add_approval_designation'); } else { showToast('Please select a department first', 'error'); }"><i class="ti ti-sitemap"></i> Select</button>
                                    </div>
                                </div>
                            </div>
							<div class="mb-0">
								<label class="col-form-label">Status</label>
								<div class="d-flex align-items-center">
									<div class="me-2">
										<input type="radio" class="status-radio" id="global-add-active" name="status" checked="">
										<label for="global-add-active">Active</label>
									</div>
									<div>
										<input type="radio" class="status-radio" id="global-add-inactive" name="status">
										<label for="global-add-inactive">Inactive</label>
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
		<!-- /Add New Contact Stage -->

@push('scripts')
<script>
$(document).ready(function() {
    $(document).on('submit', '#global_add_contact_stageForm', function (e) {
    e.preventDefault();
    const $form = $(this);

    const url = "{{ route('store.source', ['box_slug' => request()->route('box_slug') ?? session('box_slug') ?? 'default']) }}";
    const data = {
        name: $form.find('input[name="stage"]').val().trim(),
        status: $('#global-add-active').is(':checked') ? 1 : 0,
        type: 'contact_stage',
        color: $form.find('input[name="color"]').val(),
        is_task_priority: $('#global_add_is_task_priority').is(':checked') ? 1 : 0,
        task_priority: $('#global_add_is_task_priority').is(':checked') ? $form.find('select[name="task_priority[]"]').val() : null,
        reminder_mandatory: $form.find('select[name="reminder_mandatory"]').val(),
        notification: $form.find('select[name="notification"]').val(),
        priority: $form.find('input[name="priority"]').val(),
        is_default: $form.find('select[name="is_default"]').val(),
        is_final: $form.find('select[name="is_final"]').val(),
        display_amount_field: $form.find('select[name="display_amount_field"]').val(),
        no_activity_alert: $form.find('input[name="no_activity_alert"]').val(),
        no_activity_alert_unit: $form.find('select[name="no_activity_alert_unit"]').val(),
        requires_approval: $('#global_add_requires_approval').val(),
        approval_department_id: $('#global_add_approval_department').val(),
        approval_designation_id: $('#global_add_approval_designation').val(),
    };

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
        success: function (response) {
            if (response.success) {
                $('#global_add_contact_stage').modal('hide');
                $form[0].reset();
                showToast(response.message || 'Contact stage added!', 'success');
                if (typeof reloadSourceTable === 'function') {
                    reloadSourceTable();
                } else if (typeof loadContactStages === 'function') {
                    loadContactStages();
                } else {
                    setTimeout(function(){window.location.reload();}, 1500);
                }
            }
        },
        error: function (xhr) {
            const res = xhr.responseJSON;
            if (xhr.status === 422 && res?.errors) {
                const messages = Object.values(res.errors).flat().join(' ');
                showToast(messages, 'error');
            } else {
                showToast(res?.message || 'Failed to add contact stage.', 'error');
            }
        }
    });
    });
});
</script>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#global_add_is_task_priority').change(function() {
        if($(this).is(':checked')) {
            $('#global_add_priority_wrapper').show();
        } else {
            $('#global_add_priority_wrapper').hide();
        }
    });
    
    $('#global_add_requires_approval').change(function() {
        if($(this).val() == '1') {
            $('#global_add_approval_filters').show();
        } else {
            $('#global_add_approval_filters').hide();
            $('#global_add_approval_department').val('');
            $('#global_add_approval_designation').val('');
            $('#global_add_approval_designation_visible').val('');
        }
    });
    
    // Initialize Select2 for multi-select inside modal dynamically when opened
    $('#global_add_contact_stage').on('shown.bs.modal', function () {
        if ($('#global_add_priority_select').length) {
            $('#global_add_priority_select').select2({
                placeholder: "Select Priorities",
                width: '100%',
                allowClear: true,
                dropdownParent: $('#global_add_contact_stage')
            });
        }
        if ($('#global_add_approval_roles').length) {
            $('#global_add_approval_roles').select2({
                placeholder: "Select Roles",
                width: '100%',
                allowClear: true,
                dropdownParent: $('#global_add_contact_stage')
            });
        }
    });

    // Select All Priorities Logic for Global Modal
    $(document).on('click', '.select-all-priorities[data-target="global_add_priority_select"]', function() {
        const targetId = $(this).data('target');
        const $select = $('#' + targetId);
        const allValues = $select.find('option').map(function() { return $(this).val(); }).get();
        
        if ($select.val() && $select.val().length === allValues.length) {
            $select.val([]).trigger('change');
            $(this).text('Select All');
        } else {
            $select.val(allValues).trigger('change');
            $(this).text('Deselect All');
        }
    });
});
</script>
@endpush
