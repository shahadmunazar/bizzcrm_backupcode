@php
    if (!isset($contactStages)) {
        $userId = \Illuminate\Support\Facades\Auth::id();
        $boxUser = \App\Models\BoxUser::where('user_id', $userId)->first();
        if ($boxUser) {
            $contactStages = \App\Models\ContactStage::where('box_id', $boxUser->box_id)->where('status', 1)->get();
        } else {
            $contactStages = collect();
        }
    }
@endphp

<!-- Add New Calls -->
    <div class="modal fade" id="global_add_leadsubstatus" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Lead Sub Status</h5>
                    <button class="btn-close custom-btn-close border p-1 me-0 text-dark" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form id="global_Add_leadsubstatus" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="col-form-label">Select Lead Status <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="leads_status_ids[]" multiple="multiple" style="width: 100%;">
                                @if(isset($contactStages))
                                    @foreach($contactStages as $stage)
                                        <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label">Lead Sub Status Name <span class="text-danger">*</span></label>
                            <div id="global_activity_type_container">
                                <div class="d-flex align-items-center mb-2 activity-type-row">
                                    <input type="text" class="form-control me-2" name="titles[]" placeholder="Enter Name">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-primary add-activity-row"><i class="ti ti-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="col-form-label">Requires Approval</label>
                                <select class="form-select" name="requires_approval" id="global_add_substatus_requires_approval">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="global_add_substatus_approval_filters" style="display: none;">
                                <label class="col-form-label">Approval Department</label>
                                <select class="form-select" id="global_add_substatus_approval_department" name="approval_department_id">
                                    <option value="">Select Department</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="global_add_substatus_approval_designation_wrapper" style="display: none;">
                                <label class="col-form-label">Approval Designation</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="global_add_substatus_approval_designation_visible" placeholder="Select Designation" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                    <input type="hidden" id="global_add_substatus_approval_designation" name="approval_designation_id">
                                    <button class="btn btn-outline-secondary" type="button" onclick="if($('#global_add_substatus_approval_department').val()) { window.openDesignationTree($('#global_add_substatus_approval_department').val(), '#global_add_substatus_approval_designation', '#global_add_substatus_approval_designation_visible'); } else { showToast('Please select a department first', 'error'); }"><i class="ti ti-sitemap"></i> Select</button>
                                </div>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="col-form-label">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <input type="radio" class="status-radio" id="global-add-substatus-active" value="1" name="status"
                                        checked="">
                                    <label for="global-add-substatus-active">Active</label>
                                </div>
                                <div>
                                    <input type="radio" class="status-radio" id="global-add-substatus-inactive" value="0"
                                        name="status">
                                    <label for="global-add-substatus-inactive">Inactive</label>
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

@push('scripts')
<script>
$(document).ready(function() {
    // Dynamic Activity Row logic for Global Lead Sub Status
    $(document).on('click', '#global_activity_type_container .add-activity-row', function() {
        const row = `
            <div class="d-flex align-items-center mb-2 activity-type-row">
                <input type="text" class="form-control me-2" name="titles[]" placeholder="Enter Name">
                <button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-activity-row"><i class="ti ti-trash"></i></button>
            </div>
        `;
        $('#global_activity_type_container').append(row);
    });

    $(document).on('click', '#global_activity_type_container .remove-activity-row', function() {
        $(this).closest('.activity-type-row').remove();
    });

    // Select2 logic
    $('#global_add_leadsubstatus').on('shown.bs.modal', function () {
        $(this).find('.select2').select2({
            dropdownParent: $('#global_add_leadsubstatus'),
            width: '100%'
        });
        
        // Pre-select if URL has parameter
        const currentStatusId = new URLSearchParams(window.location.search).get('lead_status_id');
        if(currentStatusId) {
             const selected = $(this).find('.select2').val() || [];
             if(!selected.includes(currentStatusId)){
                 selected.push(currentStatusId);
                 $(this).find('.select2').val(selected).trigger('change');
             }
        }
    });


    $('#global_add_substatus_requires_approval').change(function() {
        if ($(this).val() == '1') {
            $('#global_add_substatus_approval_filters').show();
            $('#global_add_substatus_approval_designation_wrapper').show();
        } else {
            $('#global_add_substatus_approval_filters').hide();
            $('#global_add_substatus_approval_designation_wrapper').hide();
            $('#global_add_substatus_approval_department').val('');
            $('#global_add_substatus_approval_designation').val('');
            $('#global_add_substatus_approval_designation_visible').val('');
        }
    });

    // Populate departments on modal open
    $('#global_add_leadsubstatus').on('shown.bs.modal', function () {
        $.ajax({
            url: "{{ route('department.manage', ['box_slug' => request()->route('box_slug') ?? 'default']) }}",
            method: 'GET',
            success: function(response) {
                if (response.status === 200 && response.data) {
                    const $deptSelect = $('#global_add_substatus_approval_department');
                    $deptSelect.empty().append('<option value="">Select Department</option>');
                    window.teamOptions = window.teamOptions || {};
                    response.data.forEach(dept => {
                        window.teamOptions[dept.id] = dept.all_teams || [];
                        $deptSelect.append(`<option value="${dept.id}">${dept.name} (Dept)</option>`);
                    });
                    $deptSelect.trigger('change');
                }
            }
        });
    });

    // Form submission
    $(document).on('submit', '#global_Add_leadsubstatus', function (e) {
        e.preventDefault();
        const $form = $(this);
        
        const url = "{{ route('Leadsubstatus.store', ['box_slug' => request()->route('box_slug') ?? session('box_slug') ?? 'default']) }}";
        
        // Validation
        const titles = $form.find('input[name="titles[]"]').map(function() { return $(this).val().trim(); }).get().filter(t => t !== '');
        const leads_status_ids = $form.find('select[name="leads_status_ids[]"]').val();

        if (titles.length === 0) {
            if(typeof showToast === 'function') showToast("Please enter at least one lead sub status name.", 'error');
            return;
        }

        if (!leads_status_ids || leads_status_ids.length === 0) {
            if(typeof showToast === 'function') showToast("Please select at least one lead status.", 'error');
            return;
        }

        const data = {
            titles: titles,
            status: $('#global-add-substatus-active').is(':checked') ? 1 : 0,
            leads_status_ids: leads_status_ids,
            requires_approval: $('#global_add_substatus_requires_approval').val(),
            approval_department_id: $('#global_add_substatus_approval_department').val(),
            approval_designation_id: $('#global_add_substatus_approval_designation').val()
        };
        
        const submitBtn = $form.find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: url,
            type: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                submitBtn.prop('disabled', false).text(originalText);
                if (res.success) {
                    $('#global_add_leadsubstatus').modal('hide');
                    $form[0].reset();
                    $form.find('.select2').val(null).trigger('change');
                    $('#global_activity_type_container').html(`
                        <div class="d-flex align-items-center mb-2 activity-type-row">
                            <input type="text" class="form-control me-2" name="titles[]" placeholder="Enter Name">
                            <button type="button" class="btn btn-sm btn-icon btn-outline-primary add-activity-row"><i class="ti ti-plus"></i></button>
                        </div>
                    `);
                    
                    if(typeof showToast === 'function') showToast(res.message || 'Lead sub status added!', 'success');
                    
                    if (typeof reloadSourceTable === 'function') { // just in case it is defined on the page
                        try { reloadSourceTable(); } catch(e) {}
                    }
                    if ($('#activity-list-table').length) {
                        try { $('#activity-list-table').DataTable().ajax.reload(); } catch(e) {}
                    } else if (typeof fetchEmployeesForDropdowns === 'function') { // assume we are on lead add page, though sub status doesn't have a fetch function yet. We will just reload.
                        setTimeout(function(){window.location.reload();}, 1500);
                    } else {
                        setTimeout(function(){window.location.reload();}, 1500);
                    }
                }
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false).text(originalText);
                const res = xhr.responseJSON;
                const errorMsg = (xhr.status === 409) ? res.message : ((xhr.status === 422 && res?.errors) ?
                    Object.values(res.errors).flat().join(' ') :
                    res?.message || 'Add failed.');
                if(typeof showToast === 'function') showToast(errorMsg, 'error');
            }
        });
    });
});
</script>
@endpush
