<!-- Add Leaves -->
<div class="modal fade" id="add_leaves">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Leave </h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="leaves_apply" method="POST">
                @csrf
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Leave Type </label>
                                <select class="select" id="select_leave_type" name="leave_type">
                                    <option disabled selected>Select Option</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Leave Balance</label>
                                <input type="text" class="form-control" name="leave_balance" readonly />
                                <span id="leave_balance_msg" class="text-danger small"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Applied For </label>
                                <select class="select" id="applied_for" name="applied_for">
                                    <option disabled>Applied For</option>
                                    <option value="full_day" selected>Full Day</option>
                                    <option value="half_day">Half Day</option>
                                    <option value="short_leave">Short Leave</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">From Date </label>
                                <div class="input-icon-end position-relative">
                                    <input type="date" class="form-control datePicker" id="from_date"
                                        name="from_date" placeholder="MM/DD/YYYY" readonly>
                                    <span class="input-icon-addon">
                                        <i class="ti ti-calendar text-gray-7"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">To Date </label>
                                <div class="input-icon-end position-relative">
                                    <input type="date" class="form-control datePicker" name="to_date"
                                        placeholder="MM/DD/YYYY" readonly>
                                    <span class="input-icon-addon">
                                        <i class="ti ti-calendar text-gray-7"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total Days </label>
                                <input type="text" class="form-control" name="total_days" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Contact During Leave </label>
                                <input type="text" class="form-control" name="contact_during_leave" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Reason for Leave </label>
                                <textarea class="form-control" name="reason" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Attachment (PDF, DOC, DOCX, JPG, PNG) </label>
                                <input type="file" class="form-control" name="attachment"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <div id="file-preview" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel </button>
                    <button type="submit" class="btn btn-primary">Apply </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Leaves -->
<script>
        $(document).ready(function() {


            $('#add_leaves').on('show.bs.modal', function() {
                const title = window.selectcheckhalfWeekly || '';
                if (title.includes('1st half') || title.includes('2nd half')) {
                    $('select[name="applied_for"]').val('half_day').trigger('change');
                    console.log("Selected Weekly Off Type:", title);
                } else {

                    console.log("Not a 1st or 2nd half Weekly Off — ignoring title.");
                }

                if (window.selectedAttendanceStart) {
                    let startTime = window.selectedAttendanceStart;
                    console.log("Selected attendance start time:", startTime);
                    if (typeof startTime !== 'string') {
                        startTime = startTime.toISOString ? startTime.toISOString() : startTime.toString();
                    }
                    const fromDate = startTime.substring(0, 10);
                    $('#from_date').val(fromDate);
                    $('input[name="to_date"]').val('');

                } else {
                    $('#from_date').val('');
                    console.log("No selected attendance start time found.");
                }

                $.ajax({
                    url: '{{ route('leave.type.request', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log("Leave types response:", response);

                        if (response.status) {
                            let optionsHtml =
                                '<option disabled selected>Select Option</option>';
                            response.data.forEach(function(leaveType) {
                                optionsHtml +=
                                    `<option value="${leaveType.id}">${leaveType.leave_type}</option>`;
                            });
                            $('#select_leave_type').html(optionsHtml);
                        } else {
                            console.warn('Failed to load leave data.');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading leave data:', xhr.responseText);
                    }
                });
            });

            $('#select_leave_type').on('change', function() {
                let selectedLeaveTypeId = $(this).val();
                $.ajax({
                    url: '{{ route('get.leave.balance', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'GET',
                    data: {
                        leave_type_id: selectedLeaveTypeId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#leave_balance_msg').text('');
                        if (response.status) {
                            $('#add_leaves input[name="leave_balance"]').val(response.data
                                .balance_leave || '0');
                        } else {
                            $('#leave_balance_msg').text(response.message || 'No balance found');
                            $('#add_leaves input[name="leave_balance"]').val('0');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching leave details:', xhr.responseText);
                        $('#leave_balance_msg').text('Error fetching balance');
                        $('#add_leaves input[name="leave_balance"]').val('0');
                    }
                });
            });
            // $('#leaves_apply').on('submit', function(e) {
            //     e.preventDefault();

            //     let form = $(this);
            //     let url =
            //         '{{ route('leave.request.store', ['box_slug' => request()->route('box_slug')]) }}';
            //     let formData = new FormData(form[0]);

            //     $.ajax({
            //         type: 'POST',
            //         url: url,
            //         data: formData,
            //         processData: false,
            //         contentType: false,
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         },
            //         success: function(response) {
            //             if (response.status) {
            //                 alert(response.message || 'Leave request submitted successfully!');
            //                 $('#add_leaves').modal('hide');
            //                 form.trigger('reset');
            //                 $('#file-preview').empty(); // Clear preview on success
            //                 // Optionally refresh your leave requests list here
            //             } else {
            //                 // Show the exact error message from the server response
            //                 alert(response.message || 'Failed to submit leave request.');
            //             }
            //         },
            //         error: function(xhr) {
            //             let res = xhr.responseJSON;
            //             if (res?.errors) {
            //                 // Validation errors
            //                 let errorMessages = Object.values(res.errors).flat().join('\n');
            //                 alert('Validation errors:\n' + errorMessages);
            //             } else if (res?.message) {
            //                 // Show the exact error message returned by the server
            //                 alert(res.message);
            //             } else {
            //                 alert('Something went wrong. Please try again.');
            //             }
            //         }
            //     });
            // });
            $('#leaves_apply').on('submit', function(e) {
    e.preventDefault();
    let form = $(this);
    let url = '{{ route('leave.request.store', ['box_slug' => request()->route('box_slug')]) }}';
    let formData = new FormData(form[0]);

    $.ajax({
        type: 'POST',
        url: url,
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status) {
                window.showToast(response.message || 'Leave request submitted successfully!', 'success');
                $('#add_leaves').modal('hide');
                form.trigger('reset');
                $('#file-preview').empty();
            } else {
                // Backend returned status: false
                window.showToast(response.message || 'Failed to submit leave request.', 'danger');
            }
        },
        error: function(xhr) {
            let res = xhr.responseJSON;

            if (res?.errors) {
                // Validation errors
                let errorMessages = Object.values(res.errors).flat().join('\n');
                window.showToast('Validation errors:\n' + errorMessages, 'danger');
            } else if (res?.extra_leave) {
                // Insufficient leave balance -> show modal
                $('#unpaidLeaveInfo').html(`
                    Requested days: <b>${res.requested}</b><br>
                    Available balance: <b>${res.available}</b><br>
                    Difference (unpaid days): <b>${res.difference}</b><br><br>
                    Do you want to proceed as unpaid leave?
                `);

                // Show the modal
                let unpaidModal = new bootstrap.Modal(document.getElementById('unpaidLeaveModal'));
                unpaidModal.show();

                // Confirm button click
                $('#confirmUnpaidLeave').off('click').on('click', function() {
                    formData.append('proceed_as_unpaid', 1);

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(resp) {
                            window.showToast(resp.message || 'Leave request submitted successfully as unpaid leave!', 'success');
                            $('#add_leaves').modal('hide');
                            form.trigger('reset');
                            $('#file-preview').empty();
                            unpaidModal.hide();
                        },
                        error: function(err) {
                            window.showToast(err.responseJSON?.message || 'Something went wrong.', 'danger');
                        }
                    });
                });
            } else if (res?.message) {
                window.showToast(res.message, 'danger');
            } else {
                window.showToast('Something went wrong. Please try again.', 'danger');
            }
        }
    });
});


            $('input[name="attachment"]').on('change', function(e) {
                const file = e.target.files[0];
                const preview = $('#file-preview');
                preview.empty();
                if (file) {
                    const fileType = file.type;
                    const fileName = file.name;
                    const fileSize = (file.size / 1024).toFixed(5);

                    let previewHtml = '';
                    if (fileType.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewHtml = `
                        <div class="border p-2 rounded position-relative">
                            <img src="${e.target.result}" alt="${fileName}" style="max-width: 200px; max-height: 200px;">
                            <p class="mb-1 mt-1"><small><strong>${fileName}</strong> (${fileSize} KB)</small></p>
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-file">×</button>
                        </div>
                    `;
                            preview.html(previewHtml);
                        };
                        reader.readAsDataURL(file);
                    } else if (fileType === 'application/pdf') {
                        previewHtml = `
                    <div class="border p-2 rounded d-flex align-items-center position-relative">
                        <i class="ti ti-file-text fs-2 text-primary me-2"></i>
                        <div>
                            <p class="mb-1"><strong>${fileName}</strong></p>
                            <small class="text-muted">${fileSize} KB - PDF</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger ms-auto remove-file">Remove</button>
                    </div>
                `;
                        preview.html(previewHtml);
                    } else if (fileType === 'application/msword' || fileType ===
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                        previewHtml = `
                    <div class="border p-2 rounded d-flex align-items-center position-relative">
                        <i class="ti ti-file-word fs-2 text-blue me-2"></i>
                        <div>
                            <p class="mb-1"><strong>${fileName}</strong></p>
                            <small class="text-muted">${fileSize} KB - Document</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger ms-auto remove-file">Remove</button>
                    </div>
                `;
                        preview.html(previewHtml);
                    } else {
                        previewHtml = `
                    <div class="border p-2 rounded d-flex align-items-center position-relative">
                        <i class="ti ti-file fs-2 text-secondary me-2"></i>
                        <div>
                            <p class="mb-1"><strong>${fileName}</strong></p>
                            <small class="text-muted">${fileSize} KB</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger ms-auto remove-file">Remove</button>
                    </div>
                `;
                        preview.html(previewHtml);
                    }
                }
            });
            $(document).on('click', '.remove-file', function() {
                $('input[name="attachment"]').val('');
                $('#file-preview').empty();
            });

            function calculateTotalDays() {
                let appliedFor = $('#applied_for').val();
                if (!appliedFor) {
                    $("input[name='total_days']").val('');
                    return;
                }

                let factor = 1;
                if (appliedFor === 'half_day') factor = 0.5;
                else if (appliedFor === 'short_leave') factor = 0.25;

                let fromDate = new Date($("input[name='from_date']").val());
                let toDate = new Date($("input[name='to_date']").val());
                if (!isNaN(fromDate.getTime()) && !isNaN(toDate.getTime())) {
                    let timeDiff = toDate.getTime() - fromDate.getTime();
                    let dayDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
                    if (dayDiff > 0) {
                        let total = dayDiff * factor;
                        $("input[name='total_days']").val(total.toFixed(2));
                    } else {
                        $("input[name='total_days']").val('');
                    }
                } else {
                    $("input[name='total_days']").val('');
                }
            }

            $("input[name='from_date'], input[name='to_date'], #applied_for").on('change', function() {
                calculateTotalDays();
            });
        });
</script>
