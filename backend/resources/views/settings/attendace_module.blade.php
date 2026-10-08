@extends('layouts.main')
@section('content')
@section('title', 'Attendance Module')
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="page-title">Settings</h4>
                        </div>
                        <div class="col-4 text-end">
                            <div class="head-icons">
                                <a href="company-settings.html" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Collapse" id="collapse-header"><i
                                        class="ti ti-chevrons-up"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                @include('settings.partials.nav')

                <div class="row">
                    <div class="col-xl-3 col-lg-12 theiaStickySidebar">

                        @include('settings.partials.sidebar')

                    </div>

                    <div class="col-xl-9 col-lg-12">

                        <!-- Attendance Module Settings -->
                        <div class="card">
                            <div class="card-body">
                                <h4 class="fw-semibold mb-4">Attendance Module Settings</h4>
                                 <form id="attendanceModuleForm" action="" method="POST" novalidate>
                                    @csrf

                                    <!-- Select Date & Time Section -->
                                    <div class="border-bottom mb-4 pb-4">
                                        <h5 class="fw-semibold mb-3">Select Date & Time</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="dateTimePicker" class="form-label">Date & Time <span
                                                            class="text-danger">*</span></label>
                                                    <!--<input type="text" id="dateTimePicker" name="date_time"
                                                        class="form-control" placeholder="MM/DD/YYYY hh:mm AM/PM"
                                                        readonly required> -->

                                                    <input type="text" id="dateTimePicker" name="date_time"
                                                        class="MyDateTimePicker form-control" placeholder="MM/DD/YYYY hh:mm AM/PM"
                                                        readonly required>


                                                    <div class="form-text">Select the date and time for attendance
                                                        settings</div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="fromDayInput" class="form-label">From Day</label>
                                                    <input type="number" id="fromDayInput" name="from_date"
                                                        class="form-control" min="1" max="30"
                                                        placeholder="Enter day (1-30)">
                                                    <div class="form-text">Enter the start day number (1-30) for
                                                        attendance module</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="toDayInput" class="form-label">To Day</label>
                                                    <input type="number" id="toDayInput" name="to_date"
                                                        class="form-control" min="1" max="30"
                                                        placeholder="Enter day (1-30)">
                                                    <div class="form-text">Enter the end day number (1-30) for
                                                        attendance module</div>
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>

                                    <!-- Leave Credit Settings Section -->
                                    <div class="border-bottom mb-4 pb-4">
                                        <h5 class="fw-semibold mb-3">Leave Credit Settings</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="creditModeSelect" class="form-label">Select Credit
                                                        Mode <span class="text-danger">*</span></label>
                                                    <select name="credit_mode" id="creditModeSelect"
                                                        class="form-select" required>
                                                        <option value="">Select Credit Mode</option>
                                                        <option value="last_day_of_every_month">Last Day Of Every Month
                                                        </option>
                                                        <option value="start_day_of_every_month">Start Day Of Every
                                                            Month</option>
                                                        <option value="specific_number">Specific Number</option>
                                                    </select>
                                                    <div class="form-text">Choose when leave credits should be added
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="creditTimePicker" class="form-label">Select Time <span
                                                            class="text-danger">*</span></label>
                                                    <!--<input type="text" id="creditTimePicker" name="credit_time"
                                                        class="form-control" placeholder="hh:mm AM/PM" readonly
                                                        required> -->

                                                    <input type="text" id="creditTimePicker" name="credit_time"
                                                        class="MyTimePicker form-control" placeholder="hh:mm AM/PM" readonly
                                                        required>

                                                    <div class="form-text">Time when leave credits will be added</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Start Day of Every Month Field (shown only when start_day_of_every_month is selected) -->
                                        <div id="startDayContainer" class="mt-3" style="display: none;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="startDayInput" class="form-label">Start Day of
                                                            Every Month <span class="text-danger">*</span></label>
                                                     <input type="number" id="startDayInput"
                                                             name="start_day_of_every_month" class="form-control"
                                                             min="1" max="31"
                                                             placeholder="Enter day (1-31)">
                                                        <div class="form-text">Enter the day number (1-31) when leave
                                                            credits should be added at the start of every month</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Last Day of Every Month Field (shown only when last_day_of_every_month is selected) -->
                                        <div id="lastDayContainer" class="mt-3" style="display: none;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="lastDayInput" class="form-label">Last Day of Every
                                                            Month <span class="text-danger">*</span></label>
                                                         <input type="number" id="lastDayInput"
                                                             name="last_day_of_every_month" class="form-control"
                                                             min="1" max="31"
                                                             placeholder="Enter day (1-31)">
                                                        <div class="form-text">Enter the day number (1-31) when leave
                                                            credits should be added at the end of every month</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Number Cards Container (shown only when specific_number is selected) -->
                                        <div id="specificNumberCards" class="mt-3" style="display: none;">
                                            <div class="row">
                                                <div class="col-12">
                                                    <label class="form-label mb-3 fw-semibold">Select Day Number (1-30)
                                                        <span class="text-danger">*</span></label>
                                                    <div class="alert alert-primary mb-3">
                                                        <i class="ti ti-info-circle me-2"></i>Click on a number to
                                                        select the day when leave credits should be added. Only one day
                                                        can be selected.
                                                    </div>
                                                    <div class="row g-2 mb-3" id="numberCardsContainer">
                                                        <!-- Cards will be generated by JavaScript -->
                                                    </div>
                                                    <div class="mb-2">
                                                        <button type="button" id="clearSelectionBtn"
                                                            class="btn btn-sm btn-outline-secondary">Clear
                                                            Selection</button>
                                                    </div>
                                                    <div class="text-muted small">
                                                        <strong>Selected Day:</strong> <span
                                                            id="selectedDayDisplay">None</span>
                                                    </div>
                                                    <!-- Hidden input to store selected number (radio button behavior - only one) -->
                                                    <input type="hidden" name="leave_credit_selected_days"
                                                        id="selectedDayInput" value="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Form Actions -->
                                    <div class="mt-4">
                                        <button type="button" class="btn btn-light me-2"
                                            onclick="resetForm()">Cancel</button>
                                        <button type="submit" id="saveSettingsBtn" class="btn btn-primary">
                                            <i class="ti ti-device-floppy me-2"></i>Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- /Company Settings -->

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

@push('scripts')
    <script>
        let selectedNumber = null; // Only one number can be selected (radio button behavior)

        $(document).ready(function() {

            // Notification function for better UX
            function showNotification(type, message) {
                showtoast(message, type !== 'success');
            }

            // Load attendance module settings via AJAX
            function loadAttendanceModuleSettings() {

                $.ajax({
                    url: '{{ route('website.settings.attendance.module.get', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        // Show loading indicator if needed
                        // You can add a loading overlay here
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            const data = response.data;

                            // Populate date & time
                            if (data.attendace_module_date) {
                                $('#dateTimePicker').val(data.attendace_module_date);
                            }

                            // Populate from_day and to_day - Commented out since section is hidden
                            // if (data.attendace_module_from_date) {
                            //     $('#fromDayInput').val(data.attendace_module_from_date);
                            // }
                            // if (data.attendace_module_to_date) {
                            //     $('#toDayInput').val(data.attendace_module_to_date);
                            // }

                            // Populate credit mode and trigger change
                            if (data.leave_credit_mode) {
                                $('#creditModeSelect').val(data.leave_credit_mode);
                                $('#creditModeSelect').trigger('change');

                                // Populate mode-specific fields after a delay
                                setTimeout(function() {
                                    if (data.leave_credit_time) {
                                        $('#creditTimePicker').val(data.leave_credit_time);
                                    }

                                    // Populate start_day_of_every_month
                                    if (data.leave_credit_mode === 'start_day_of_every_month' &&
                                        data.start_day_of_every_month) {
                                        $('#startDayInput').val(data.start_day_of_every_month);
                                    }

                                    // Populate last_day_of_every_month
                                    if (data.leave_credit_mode === 'last_day_of_every_month' &&
                                        data.last_day_of_every_month) {
                                        $('#lastDayInput').val(data.last_day_of_every_month);
                                    }
                                }, 200);
                            }

                            // Populate selected day number if specific_number mode
                            // Check attendace_module_from_date first (new way), then leave_credit_selected_days (backward compatibility)
                            if (data.leave_credit_mode === 'specific_number') {
                                const savedDay = data.attendace_module_from_date || data.leave_credit_selected_days;
                                if (savedDay) {
                                setTimeout(function() {
                                        const savedNumber = parseInt(savedDay);
                                    if (!isNaN(savedNumber) && savedNumber >= 1 &&
                                        savedNumber <= 30) {
                                        const $card = $(
                                            `.number-card[data-number="${savedNumber}"]`);
                                        if ($card.length) {
                                            selectedNumber = savedNumber;
                                            $card.addClass('bg-primary');
                                            $card.find('span').addClass('text-white');
                                            updateSelectedDaysDisplay();
                                        }
                                    }
                                }, 300);
                                }
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading attendance module settings:', xhr);
                        // Don't show error on initial load, just log it
                    }
                });
            }

            // Generate number cards (1-30) with radio button behavior
            function generateNumberCards() {
                const container = $('#numberCardsContainer');
                container.empty();

                for (let i = 1; i <= 30; i++) {
                    const card = $(`
                    <div class="col-2 col-md-1 mb-2">
                        <div class="number-card text-center p-3 border rounded"
                             data-number="${i}">
                            <span class="fw-semibold">${i}</span>
                        </div>
                    </div>
                `);

                    // Add click handler - radio button behavior (only one selection)
                    card.find('.number-card').on('click', function() {
                        const number = parseInt($(this).data('number'));
                        selectNumber(number, $(this));
                    });

                    container.append(card);
                }
            }

            // Select number (radio button behavior - only one at a time)
            function selectNumber(number, $card) {
                // If clicking the same number, deselect it
                if (selectedNumber === number) {
                    selectedNumber = null;
                    $card.removeClass('bg-primary');
                    $card.find('span').removeClass('text-white');
                } else {
                    // Deselect previously selected card
                    if (selectedNumber !== null) {
                        $(`.number-card[data-number="${selectedNumber}"]`).removeClass('bg-primary');
                        $(`.number-card[data-number="${selectedNumber}"] span`).removeClass('text-white');
                    }

                    // Select new number
                    selectedNumber = number;
                    $card.addClass('bg-primary');
                    $card.find('span').addClass('text-white');
                }

                updateSelectedDaysDisplay();
            }

            // Update selected days display and hidden input
            function updateSelectedDaysDisplay() {
                if (selectedNumber !== null) {
                    $('#selectedDayDisplay').text(selectedNumber);
                    $('#selectedDayInput').val(selectedNumber);
                } else {
                    $('#selectedDayDisplay').text('None');
                    $('#selectedDayInput').val('');
                }
            }

            // Clear selection
            $('#clearSelectionBtn').on('click', function() {
                if (selectedNumber !== null) {
                    $(`.number-card[data-number="${selectedNumber}"]`).removeClass('bg-primary');
                    $(`.number-card[data-number="${selectedNumber}"] span`).removeClass('text-white');
                    selectedNumber = null;
                    updateSelectedDaysDisplay();
                }
            });

            // Generate cards on page load
            generateNumberCards();

            // Load settings via AJAX on page load
            loadAttendanceModuleSettings();

            // Show/hide fields based on credit mode selection
            $('#creditModeSelect').on('change', function() {
                const selectedValue = $(this).val();

                // Hide all conditional fields first
                $('#specificNumberCards').slideUp(300);

                // Remove required attributes
                $('#selectedDayInput').removeAttr('required');

                // Reset selected card
                if (selectedNumber !== null) {
                    $(`.number-card[data-number="${selectedNumber}"]`).removeClass('bg-primary');
                    $(`.number-card[data-number="${selectedNumber}"] span`).removeClass('text-white');
                }
                selectedNumber = null;
                updateSelectedDaysDisplay();

                // Show appropriate fields based on selection
                if (selectedValue === 'specific_number') {
                    // Show number cards (only for specific_number mode)
                    $('#specificNumberCards').slideDown(300);
                    $('#selectedDayInput').attr('required', 'required');
                }

                // Time field is always required for all modes
                $('#creditTimePicker').attr('required', 'required');
            });

            // Form submission (all validation handled in backend)
            $('#attendanceModuleForm').on('submit', function(e) {
                e.preventDefault();

                // Get form values
                const dateTime = $('#dateTimePicker').val();
                const creditMode = $('#creditModeSelect').val();
                const creditTime = $('#creditTimePicker').val();
                // const fromDay = $('#fromDayInput').val(); // Commented out - From Day section is hidden
                // const toDay = $('#toDayInput').val(); // Commented out - To Day section is hidden
                const startDay = $('#startDayInput').val();
                const lastDay = $('#lastDayInput').val();

                // Disable submit button
                const $submitBtn = $('#saveSettingsBtn');
                $submitBtn.prop('disabled', true).html('<i class="ti ti-loader spin me-2"></i>Saving...');

                // Prepare form data
                // When specific_number is selected, save selectedNumber to from_date
                const formData = {
                    date_time: dateTime,
                    from_date: creditMode === 'specific_number' ? selectedNumber : null, // Save selected day to from_date when specific_number
                    to_date: null, // Always null since To Day section is commented out
                    credit_mode: creditMode,
                    credit_time: creditTime,
                    start_day_of_every_month: creditMode === 'start_day_of_every_month' ? startDay :
                        null,
                    last_day_of_every_month: creditMode === 'last_day_of_every_month' ? lastDay : null,
                    selected_days: creditMode === 'specific_number' ? selectedNumber : null,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                // Submit via AJAX with enhanced UX
                $.ajax({
                    url: '{{ route('website.settings.attendance.module.save', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        // Show loading state
                        $submitBtn.prop('disabled', true).html(
                            '<i class="ti ti-loader spin me-2"></i>Saving...');
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success notification
                            showNotification('success', response.message ||
                                'Attendance module settings saved successfully!');

                            // Reload settings data via AJAX to reflect changes
                            loadAttendanceModuleSettings();
                        } else {
                            showNotification('error', response.message ||
                                'Failed to save settings.');
                        }
                        $submitBtn.prop('disabled', false).html(
                            '<i class="ti ti-device-floppy me-2"></i>Save Changes');
                    },
                    error: function(xhr) {
                        let message = 'An error occurred while saving.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                const errors = Object.values(xhr.responseJSON.errors).flat();
                                message = errors.join('\n');
                            }
                        }
                        showNotification('error', 'Error: ' + message);
                        $submitBtn.prop('disabled', false).html(
                            '<i class="ti ti-device-floppy me-2"></i>Save Changes');
                    }
                });
            });

            // Initialize time picker
            if ($('#creditTimePicker').length) {
                $('#creditTimePicker').timepicker({
                    defaultTime: false,
                    showMeridian: true,
                    minuteStep: 1
                });
            }

            // Initialize datetime picker
            if ($('#dateTimePicker').length) {
                $('#dateTimePicker').datetimepicker({
                    format: 'MM/DD/YYYY hh:mm A',
                    showMeridian: true,
                    autoclose: true
                });
            }

            // Day inputs (from_day and to_day) are already number inputs, no initialization needed

            // Populate form with existing data
            @if (isset($box))
                // Populate date & time
                @if ($box->attendace_module_date)
                    $('#dateTimePicker').val('{{ $box->attendace_module_date }}');
                @endif

                // Populate from_day and to_day - Commented out since section is hidden
                // @if ($box->attendace_module_from_date)
                //     $('#fromDayInput').val('{{ $box->attendace_module_from_date }}');
                // @endif

                // @if ($box->attendace_module_to_date)
                //     $('#toDayInput').val('{{ $box->attendace_module_to_date }}');
                // @endif

                // Populate credit mode and trigger change to show/hide fields
                @if ($box->leave_credit_mode)
                    $('#creditModeSelect').val('{{ $box->leave_credit_mode }}');

                    // Trigger change to show appropriate fields
                    $('#creditModeSelect').trigger('change');

                    // Populate time after a short delay to ensure field is visible
                    setTimeout(function() {
                        @if ($box->leave_credit_time)
                            $('#creditTimePicker').val('{{ $box->leave_credit_time }}');
                        @endif

                        // Populate start_day_of_every_month if mode is start_day_of_every_month
                        @if ($box->leave_credit_mode === 'start_day_of_every_month' && $box->start_day_of_every_month)
                            $('#startDayInput').val('{{ $box->start_day_of_every_month }}');
                        @endif

                        // Populate last_day_of_every_month if mode is last_day_of_every_month
                        @if ($box->leave_credit_mode === 'last_day_of_every_month' && $box->last_day_of_every_month)
                            $('#lastDayInput').val('{{ $box->last_day_of_every_month }}');
                        @endif
                    }, 200);
                @endif

                // Populate selected day number if specific_number mode
                // Check attendace_module_from_date first (new way), then leave_credit_selected_days (backward compatibility)
                @if ($box->leave_credit_mode === 'specific_number')
                    @php
                        $savedDay = $box->attendace_module_from_date ?? $box->leave_credit_selected_days;
                    @endphp
                    @if ($savedDay)
                    // Wait for cards to be generated and modal to show, then select the number
                    setTimeout(function() {
                            const savedNumber = parseInt('{{ $savedDay }}');
                        if (!isNaN(savedNumber) && savedNumber >= 1 && savedNumber <= 30) {
                            const $card = $(`.number-card[data-number="${savedNumber}"]`);
                            if ($card.length) {
                                selectedNumber = savedNumber;
                                $card.addClass('bg-primary');
                                $card.find('span').addClass('text-white');
                                updateSelectedDaysDisplay();
                            }
                        }
                    }, 300);
                    @endif
                @endif
            @endif
        });
        
        function showtoast(message, isError = false) {
            $('#dynamicToastMessage').text(message);
            const toastElement = document.getElementById('dynamicToast');
            const toast = new bootstrap.Toast(toastElement);
            $('#dynamicToast').removeClass('bg-danger bg-success').addClass(isError ? 'bg-danger' :
                'bg-success');
            toast.show();
        }

        // Reset form function
        function resetForm() {
            $('#attendanceModuleForm')[0].reset();
            $('#specificNumberCards').hide();
            $('#startDayContainer').hide();
            $('#lastDayContainer').hide();
            if (selectedNumber !== null) {
                $(`.number-card[data-number="${selectedNumber}"]`).removeClass('bg-primary');
                $(`.number-card[data-number="${selectedNumber}"] span`).removeClass('text-white');
            }
            selectedNumber = null;
            $('#selectedDayDisplay').text('None');
            $('#selectedDayInput').val('');
            $('#startDayInput').val('');
            $('#lastDayInput').val('');
            // $('#fromDayInput').val(''); // Commented out - From Day section is hidden
            // $('#toDayInput').val(''); // Commented out - To Day section is hidden
        }
    </script>
    <style>
        .number-card {
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            user-select: none;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #fff;
            border: 2px solid #dee2e6;
        }

        .number-card:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            background-color: #f0f0f0;
        }

        .spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
            border-color: #0d6efd;
        }

        /*.number-card.bg-primary {
            background-color: #0d6efd !important;
            color: white !important;
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
            border-color: #0d6efd;
        }

        .number-card.bg-primary:hover {
            background-color: #0b5ed7 !important;
            border-color: #0b5ed7;
        } */

        .number-card span {
            transition: color 0.3s ease;
        }

        .ti-loader.spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        #selectedDayDisplay {
            font-weight: 600;
            color: #e41f07;
        }
    </style>
@endpush

@endsection
