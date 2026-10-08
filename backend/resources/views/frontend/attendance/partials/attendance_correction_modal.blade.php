<div class="modal fade" id="attendance_request">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Attendance Correction </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form action="attendace_correction" method="POST">
                @csrf
                <div class="modal-body pb-0">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-5">
                            <div class="mb-3">
                                <div class="form-check mt-md-4 pe-md-3" style="border-right: 1px solid #dee2e6;">
                                    <input class="form-check-input" type="checkbox" name="punch_out" value="1" id="punchout">
                                    <label class="form-check-label" for="punchout">
                                        Punch Out
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-7">
                            <div class="mb-3">
                                <label class="form-label">Time </label>
                                <div class="input-icon-end position-relative time-picker-wrapper" style="pointer-events: none; opacity: 0.5;">
                                    <input type="text" id="punchOutTimePicker" name="punch_out_time" class="MyTimePicker form-control timepicker" placeholder="hh:mm AM/PM">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-5">
                            <div class="mb-3">
                                <div class="form-check mt-md-4 pe-md-3" style="border-right: 1px solid #dee2e6;">
                                    <input class="form-check-input" type="checkbox" name="punch_in" value="1" id="punchin">
                                    <label class="form-check-label" for="punchin">
                                        Punch In
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-7">
                            <div class="mb-3">
                                <label class="form-label">Time </label>
                                <div class="input-icon-end position-relative time-picker-wrapper" style="pointer-events: none; opacity: 0.5;">
                                    <input type="text" id="punchInTimePicker" name="punch_in_time" class="MyTimePicker form-control timepicker" placeholder="hh:mm AM/PM">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Reason </label>
                                <div class="input-icon-end position-relative">
                                    <input type="text" name="reason" class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="attendanceCorrectionSubmitBtn" class="btn btn-primary" disabled>Submit </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize bootstrap-timepicker
        if ($.fn.timepicker) {
            $('#attendance_request .timepicker').timepicker({
                defaultTime: false,
                showMeridian: true,
                minuteStep: 1
            });
        }

        // Validation function for attendance correction form
        function validateAttendanceCorrection() {
            const punchOutChecked = $('#punchout').is(':checked');
            const punchInChecked = $('#punchin').is(':checked');
            const punchOutTime = $('#punchOutTimePicker').val().trim();
            const punchInTime = $('#punchInTimePicker').val().trim();
            const $submitBtn = $('#attendanceCorrectionSubmitBtn');

            // At least one checkbox must be checked
            if (!punchOutChecked && !punchInChecked) {
                $submitBtn.prop('disabled', true);
                return false;
            }

            // If punch_out is checked, punch_out_time must be filled
            if (punchOutChecked && !punchOutTime) {
                $submitBtn.prop('disabled', true);
                return false;
            }

            // If punch_in is checked, punch_in_time must be filled
            if (punchInChecked && !punchInTime) {
                $submitBtn.prop('disabled', true);
                return false;
            }

            // All validations passed
            $submitBtn.prop('disabled', false);
            return true;
        }

        // Initialize: disable submit button and time inputs
        $('#attendanceCorrectionSubmitBtn').prop('disabled', true);
        $('#punchOutTimePicker, #punchInTimePicker').closest('.time-picker-wrapper').css({ 'pointer-events': 'none', 'opacity': '0.5' });

        // Reset form when modal is closed
        $('#attendance_request').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
            $('#attendanceCorrectionSubmitBtn').prop('disabled', true);
            $('#punchOutTimePicker, #punchInTimePicker').closest('.time-picker-wrapper').css({ 'pointer-events': 'none', 'opacity': '0.5' });
            if ($('#punchOutTimePicker')[0]._flatpickr) $('#punchOutTimePicker')[0]._flatpickr.clear();
            if ($('#punchInTimePicker')[0]._flatpickr) $('#punchInTimePicker')[0]._flatpickr.clear();
        });

        // Enable/disable time inputs based on checkbox state
        $('#punchout').on('change', function() {
            if ($(this).is(':checked')) {
                $('#punchOutTimePicker').closest('.time-picker-wrapper').css({ 'pointer-events': 'auto', 'opacity': '1' });
            } else {
                $('#punchOutTimePicker').closest('.time-picker-wrapper').css({ 'pointer-events': 'none', 'opacity': '0.5' });
                if ($('#punchOutTimePicker')[0]._flatpickr) $('#punchOutTimePicker')[0]._flatpickr.clear();
            }
            validateAttendanceCorrection();
        });

        $('#punchin').on('change', function() {
            if ($(this).is(':checked')) {
                $('#punchInTimePicker').closest('.time-picker-wrapper').css({ 'pointer-events': 'auto', 'opacity': '1' });
            } else {
                $('#punchInTimePicker').closest('.time-picker-wrapper').css({ 'pointer-events': 'none', 'opacity': '0.5' });
                if ($('#punchInTimePicker')[0]._flatpickr) $('#punchInTimePicker')[0]._flatpickr.clear();
            }
            validateAttendanceCorrection();
        });

        // Validate on time input change
        $('#punchOutTimePicker, #punchInTimePicker').on('change blur input', function() {
            validateAttendanceCorrection();
        });

        // Initialize when modal is shown
        $('#attendance_request').on('shown.bs.modal', function() {
            // Ensure time inputs are disabled if checkboxes are unchecked
            if (!$('#punchout').is(':checked')) {
                $('#punchOutTimePicker').closest('.time-picker-wrapper').css({ 'pointer-events': 'none', 'opacity': '0.5' });
            }
            if (!$('#punchin').is(':checked')) {
                $('#punchInTimePicker').closest('.time-picker-wrapper').css({ 'pointer-events': 'none', 'opacity': '0.5' });
            }
            validateAttendanceCorrection();
        });
        
        // Handle form submission via AJAX for Attendance Correction
        $('#attendance_request form').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            // Validate before submitting
            if (!validateAttendanceCorrection()) {
                if (typeof window.showToast === 'function') {
                    window.showToast('Please check at least one option and fill in the corresponding time field.', 'warning');
                } else if (typeof showToast === 'function') {
                    showToast('Please check at least one option and fill in the corresponding time field.', 'warning');
                }
                return;
            }

            var $form = $(this);
            var formData = $form.serializeArray();
            var $submitBtn = $form.find('button[type="submit"]');

            // Add dynamic attendance_id from selected calendar event or data attribute
            if (!window.selectedAttendanceId) {
                if (typeof window.showToast === 'function') {
                    window.showToast("Attendance ID missing, cannot submit correction.", 'danger');
                } else if (typeof showToast === 'function') {
                    showToast("Attendance ID missing, cannot submit correction.", 'danger');
                }
                return;
            }

            formData.push({
                name: 'attendace_id',
                value: window.selectedAttendanceId
            });

            // Disable button and show loading text
            $submitBtn.prop('disabled', true).html('<i class="ti ti-loader spin me-2"></i>Submitting...');

            $.ajax({
                url: "{{ route('saved.correction', ['box_slug' => request()->route('box_slug') ?? 'medsyn-lab-biotech']) }}",
                method: 'POST',
                data: $.param(formData),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.status) {
                        if (typeof window.showToast === 'function') {
                            window.showToast(res.message || 'Attendance correction saved!', 'success');
                        } else if (typeof showToast === 'function') {
                            showToast(res.message || 'Attendance correction saved!', 'success');
                        }
                        $('#attendance_request').modal('hide');
                        $form[0].reset();
                        $('#punchOutTimePicker, #punchInTimePicker').closest('.time-picker-wrapper').css({ 'pointer-events': 'none', 'opacity': '0.5' });
                        if ($('#punchOutTimePicker')[0]._flatpickr) $('#punchOutTimePicker')[0]._flatpickr.clear();
                        if ($('#punchInTimePicker')[0]._flatpickr) $('#punchInTimePicker')[0]._flatpickr.clear();
                    } else {
                        if (typeof window.showToast === 'function') {
                            window.showToast('Failed: ' + (res.message || 'Unknown error'), 'danger');
                        } else if (typeof showToast === 'function') {
                            showToast('Failed: ' + (res.message || 'Unknown error'), 'danger');
                        }
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON?.errors || {};
                    let message = xhr.responseJSON?.message || 'An error occurred';
                    if (typeof window.showToast === 'function') {
                        window.showToast(message + '\n' + JSON.stringify(errors), 'danger');
                    } else if (typeof showToast === 'function') {
                        showToast(message + '\n' + JSON.stringify(errors), 'danger');
                    }
                },
                complete: function() {
                    // Restore original text
                    $submitBtn.html('Submit');
                    // Re-validate to set correct disabled/enabled state
                    validateAttendanceCorrection();
                }
            });
        });
    });
</script>
