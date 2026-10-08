@extends('layouts.main')
@section('content')
@section('title', 'Attendance Calendar')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="row align-items-center w-100">
                <div class="col-lg-8 col-sm-12">
                    <h3 class="page-title">Attendance Calendar</h3>
                </div>

            </div>
        </div>
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<div id="leave_modal" class="modal modal-xl fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attendence</h5>
                <!-- <span class="ms-2 me-2">|</span> -->

                <div class="btn-group ms-4">
                    <button class="btn btn-outline-primary dropdown-toggle btn-sm" type="button" id="defaultDropdown"
                        data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                        Raise a Requests
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#attendance_request"
                                class="dropdown-item" href="javascript:void(0);">Attendance Request</a></li>
                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#add_leaves" class="dropdown-item"
                                href="javascript:void(0);">Apply Leave</a></li>
                    </ul>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <h5 class="modal-title mb-2 d-flex justify-content-between align-items-center">
                        <span id="calendarModalDateLabel">Attendance for --</span>
                        <span id="calendarModalStatusLabel" class="badge bg-soft-primary ms-3">--</span>
                    </h5>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <tbody>
                                            <tr>
                                                <td><span
                                                        style="width: 122px; display: inline-block;font-weight: 700;">Punch
                                                        in :</span> &nbsp; &nbsp; &nbsp; <span
                                                        class="badge bg-soft-danger fs-14">--:--:--</span>
                                                </td>

                                                <td><span
                                                        style="width: 122px; display: inline-block;font-weight: 700;">Punch
                                                        Out :</span> &nbsp; &nbsp; &nbsp; <span
                                                        class="badge bg-soft-danger fs-14">--:--:--</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span
                                                        style="width: 122px; display: inline-block;font-weight: 700;">Break
                                                        1 Out :</span> &nbsp; &nbsp; &nbsp; <span
                                                        class="badge bg-soft-danger fs-14">--:--:--</span>
                                                </td>
                                                <td><span
                                                        style="width: 122px; display: inline-block;font-weight: 700;">Break
                                                        1 In :</span> &nbsp; &nbsp; &nbsp; <span
                                                        class="badge bg-soft-danger fs-14">--:--:--</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span
                                                        style="width: 122px; display: inline-block;font-weight: 700;">Lunch
                                                        Out :</span> &nbsp; &nbsp; &nbsp; <span
                                                        class="badge bg-soft-danger fs-14">--:--:--</span>
                                                </td>
                                                <td><span
                                                        style="width: 122px; display: inline-block;font-weight: 700;">Lunch
                                                        In :</span> &nbsp; &nbsp; &nbsp; <span
                                                        class="badge bg-soft-danger fs-14">--:--:--</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span
                                                        style="width: 122px; display: inline-block;font-weight: 700;">Break
                                                        2 Out :</span> &nbsp; &nbsp; &nbsp; <span
                                                        class="badge bg-soft-danger fs-14">--:--:--</span>
                                                </td>
                                                <td><span
                                                        style="width: 122px; display: inline-block;font-weight: 700;">Break
                                                        2 In :</span> &nbsp; &nbsp; &nbsp; <span
                                                        class="badge bg-soft-danger fs-14">--:--:--</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-3">
                                        <div class="mb-4">
                                            <p class="d-flex align-items-center mb-1"><i
                                                    class="ti ti-point-filled text-dark-transparent me-1"></i>Total
                                                Working hours </p>
                                            <h3 id="totalWorkingHours">--:--</h3>
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="mb-4">
                                            <p class="d-flex align-items-center mb-1"><i
                                                    class="ti ti-point-filled text-success me-1"></i>Productive Hours
                                            </p>
                                            <h3 id="productiveHours">--:--</h3>
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="mb-4">
                                            <p class="d-flex align-items-center mb-1"><i
                                                    class="ti ti-point-filled text-warning me-1"></i>Break Hours </p>
                                            <h3 id="breakHours">--:--</h3>
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="mb-4">
                                            <p class="d-flex align-items-center mb-1"><i
                                                    class="ti ti-point-filled text-info me-1"></i>Overtime </p>
                                            <h3 id="overtimeHours">--:--</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mx-auto">
                                        <div id="attendanceProgressBar" class="progress bg-transparent-dark mb-3"
                                            style="height: 24px;">
                                            <!-- Progress segments will be appended here -->
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div id="timeLabels"
                                            class="d-flex align-items-center justify-content-between fs-10">
                                            <!-- Hour labels will be appended here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Leaves -->
@include('frontend.attendance.partials.add_leave_modal')
<!-- /Add Leaves -->
<!-- attendance request -->
@include('frontend.attendance.partials.attendance_correction_modal')
<!-- attendance request -->
<!-- Add Event Modal -->
<div id="add_event" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <form action="calendar.html">
                    <div class="input-blocks">
                        <label>Event Name <span class="text-danger">*</span></label>
                        <input class="form-control" type="text">
                    </div>
                    <div class="input-blocks">
                        <label>Event Date <span class="text-danger">*</span></label>
                        <div class="cal-icon">
                            <input class="form-control " type="text">
                        </div>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Event Modal -->
<!-- Add Event Modal -->
<div class="modal custom-modal fade none-border" id="my_event">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Event</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <form action="calendar.html">
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-success save-event submit-btn">Create
                            event</button>
                        <button type="submit" class="btn btn-danger delete-event submit-btn"
                            data-dismiss="modal">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Event Modal -->
<!-- Add Category Modal -->
<div class="modal custom-modal fade" id="add_new_event">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Category</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"><span
                        aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <form action="calendar.html">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input class="form-control form-white" placeholder="Enter name" type="text"
                            name="category-name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Choose Category Color</label>
                        <select class="form-control form-white" data-placeholder="Choose a color..."
                            name="category-color">
                            <option value="success">Success</option>
                            <option value="danger">Danger</option>
                            <option value="info">Info</option>
                            <option value="primary">Primary</option>
                            <option value="warning">Warning</option>
                            <option value="inverse">Inverse</option>
                        </select>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary save-category submit-btn"
                            data-dismiss="modal">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="unpaidLeaveModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Unpaid Leave</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p id="unpaidLeaveInfo"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmUnpaidLeave" class="btn btn-primary">Proceed as Unpaid Leave</button>
      </div>
    </div>
  </div>
</div>



<style>
    .new-bg-green {
        background-color: #0e934a
    }

    .new-bg-orange {
        background-color: #f37f0b
    }

    .new-bg-yellow {
        background-color: #ffd401
    }

    .new-bg-blue {
        background-color: #4a00e5
    }

    .new-bg-skyblue {
        background-color: #14bcee
    }

    .new-bg-purple {
        background-color: #b60adc
    }
</style>
@push('scripts')
    <script>
        const calendarDataUrl = "{{ route('get.calender.data', ['box_slug' => request()->route('box_slug')]) }}";
    </script>
    <script>
        ! function($) {
            "use strict";

            var CalendarApp = function() {
                this.$body = $("body");
                this.$calendar = $('#calendar');
                this.$event = $('#calendar-events div.calendar-events');
                this.$categoryForm = $('#add_new_event form');
                this.$extEvents = $('#calendar-events');
                this.$modal = $('#leave_modal'); // Use leave_modal instead of my_event
                this.$saveCategoryBtn = $('.save-category');
                this.$calendarObj = null;
            };

            /* on drop */
            CalendarApp.prototype.onDrop = function(eventObj, date) {
                var $this = this;
                var originalEventObject = eventObj.data('eventObject');
                var $categoryClass = eventObj.attr('data-class');
                var copiedEventObject = $.extend({}, originalEventObject);
                copiedEventObject.start = date;
                if ($categoryClass) {
                    copiedEventObject['className'] = [$categoryClass];
                }
                $this.$calendar.fullCalendar('renderEvent', copiedEventObject, true);
                if ($('#drop-remove').is(':checked')) {
                    eventObj.remove();
                }
            };





            /* on click on event */
            CalendarApp.prototype.onEventClick = function(calEvent, jsEvent, view) {
                var $this = this;
                window.selectcheckhalfWeekly = calEvent.title || 'N/A';
                window.selectedAttendanceStart = calEvent.start || null;

                console.log("✅ Title from event click:", window.selectcheckhalfWeekly);
                console.log("✅ Start from event click:", window.selectedAttendanceStart);

                if (!calEvent.attendance_id) {
                    console.warn("⚠️ No attendance ID found for this event.");
                    return;
                }

                window.selectedAttendanceId = calEvent.attendance_id;
                // Show modal and fetch attendance summary
                const $bar = $('#attendanceProgressBar');
                const $timeLabels = $('#timeLabels');
                $bar.empty().css('display', 'flex');
                $timeLabels.empty().css('display', 'flex');
                $.ajax({
                    url: '{{ route('individual.attendance.summary', ['box_slug' => request()->route('box_slug')]) }}',
                    method: 'GET',
                    data: {
                        attendance_id: calEvent.attendance_id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (!res.status) {
                            $this.$modal.find('.modal-body').html(
                                '<p class="text-danger">Failed to load data.</p>');
                            $this.$modal.modal({
                                backdrop: 'static'
                            });
                            return;
                        }

                        const {
                            time_segments: apiSegments = [],
                            hour_labels: hourLabels = [],
                            total_working_hours: totalWorkingHours = '00h 00m 00s',
                            productive_hours: productiveHours = '00h 00m 00s',
                            break_hours: breakHours = '00h 00m 00s',
                            overtime: overtime = '00h 00m 00s',
                            shift_start_time: shiftStart,
                            shift_end_time: shiftEnd,
                            intime,
                            outTime,
                            status,
                            late_duration: lateDuration = null,
                            attendance_date,
                            user_name,
                            department_name,
                            designation_name,
                            profile_image,
                            hourly_chart: hourlyChart
                        } = res.data;
                        const intimeFormatted = intime ? moment(intime).format('HH:mm:ss') : null;
                        const punchOutFormatted = outTime ? moment(outTime).format('HH:mm:ss') : null;
                        initializeSegmentsFromHourlyChart(hourlyChart, shiftStart, shiftEnd, intime,
                            outTime);
                        const formattedDate = moment(attendance_date).format('DD-MM-YYYY');
                        $('#calendarModalDateLabel').text('Attendance for ' + formattedDate);
                        $('#calendarModalStatusLabel').text(status || '-');
                        const table = $this.$modal.find('.table tbody');
                        table.empty();
                        table.append(`
    <tr>
        <td><span style="width: 122px; display: inline-block; font-weight: 700;">Punch In:</span> &nbsp;&nbsp;&nbsp;
            <span class="badge bg-soft-danger fs-14">${intimeFormatted || '-'}</span>
        </td>
        <td><span style="width: 122px; display: inline-block; font-weight: 700;">Punch Out:</span> &nbsp;&nbsp;&nbsp;
            <span class="badge bg-soft-danger fs-14">${punchOutFormatted || '-'}</span>
        </td>
    </tr>
`);
                        if (Array.isArray(res.data.breaks) && res.data.breaks.length > 0) {
                            for (let i = 0; i < res.data.breaks.length; i += 2) {
                                const breakOut = res.data.breaks[i];
                                const breakIn = res.data.breaks[i + 1];
                                const breakOutLabel = breakOut?.label || '-';
                                const breakOutTime = breakOut?.time || '-';
                                const breakInLabel = breakIn?.label || '-';
                                const breakInTime = breakIn?.time || '-';

                                table.append(`
            <tr>
                <td><span style="width: 122px; display: inline-block; font-weight: 700;">${breakOutLabel}:</span> &nbsp;&nbsp;&nbsp;
                    <span class="badge bg-soft-danger fs-14">${breakOutTime}</span>
                </td>
                <td><span style="width: 122px; display: inline-block; font-weight: 700;">${breakInLabel}:</span> &nbsp;&nbsp;&nbsp;
                    <span class="badge bg-soft-danger fs-14">${breakInTime}</span>
                </td>
            </tr>
        `);
                            }
                        }
                        table.append(`
    <tr>
        <td><span style="width: 122px; display: inline-block; font-weight: 700;">Break Duration:</span> &nbsp;&nbsp;&nbsp;
            <span class="badge bg-soft-danger fs-14">${breakHours || '-'}</span>
        </td>
        <td><span style="width: 122px; display: inline-block; font-weight: 700;">Late Duration:</span> &nbsp;&nbsp;&nbsp;
            <span class="badge bg-soft-danger fs-14">${lateDuration || '00:00:00'}</span>
        </td>
    </tr>
    <tr>
        <td><span style="width: 122px; display: inline-block; font-weight: 700;">Status:</span> &nbsp;&nbsp;&nbsp;
            <span class="badge bg-soft-primary fs-14">${status || '-'}</span>
        </td>
        <td></td>
    </tr>
`);




                        // Populate summary fields
                        $('#totalWorkingHours').text(totalWorkingHours);
                        $('#productiveHours').text(productiveHours);
                        $('#breakHours').text(breakHours);
                        $('#overtimeHours').text(overtime);
                        $('#modalDate').text('Date: ' + moment(attendance_date).format('DD-MM-YYYY'));
                        const labelWidth = (100 / hourLabels.length).toFixed(2);
                        hourLabels.forEach(label => {
                            $timeLabels.append(
                                `<span class="fs-10 text-center" style="flex: 0 0 ${labelWidth}%;">${label}</span>`
                            );
                        });
                        apiSegments.forEach(item => {
                            const colorClass = item.type === 'productive' ? 'bg-success' :
                                item.type === 'break' ? 'bg-warning' :
                                item.type === 'overtime' ? 'bg-info' :
                                'bg-light';
                            $bar.append(`
                        <div class="progress-bar ${colorClass} rounded me-1"
                             role="progressbar"
                             style="flex: 0 0 ${item.percentage}%"
                             title="${item.start} - ${item.end || 'now'} (${item.type})">
                        </div>
                    `);
                        });
                        $this.$modal.modal({
                            backdrop: 'static'
                        });
                    },
                    error: function(xhr) {
                        console.error('Error fetching attendance summary:', xhr.responseText);
                        $this.$modal.find('.modal-body').html(
                            '<p class="text-danger">Failed to load data.</p>');
                        $this.$modal.modal({
                            backdrop: 'static'
                        });
                    }
                });
            };

            /* on select */
            CalendarApp.prototype.onSelect = function(start, end, allDay) {
                var $this = this;
                var addEventModal = $('#add_new_event');
                addEventModal.modal({
                    backdrop: 'static'
                });
                var form = addEventModal.find('form');
                form.find("input[name='category-name']").val('');
                form.find("select[name='category-color']").val('success');

                form.off('submit').on('submit', function(e) {
                    e.preventDefault();
                    var title = form.find("input[name='category-name']").val();
                    var categoryClass = form.find("select[name='category-color'] option:checked").val();
                    if (title !== null && title.length !== 0) {
                        $this.$calendarObj.fullCalendar('renderEvent', {
                            title: title,
                            start: start,
                            end: end,
                            allDay: false,
                            className: 'bg-' + categoryClass
                        }, true);
                        addEventModal.modal('hide');
                    } else {
                        window.showToast('You have to give a title to your event', 'warning');
                    }
                    return false;
                });
                $this.$calendarObj.fullCalendar('unselect');
            };

            CalendarApp.prototype.enableDrag = function() {
                $(this.$event).each(function() {
                    var eventObject = {
                        title: $.trim($(this).text())
                    };
                    $(this).data('eventObject', eventObject);
                    $(this).draggable({
                        zIndex: 999,
                        revert: true,
                        revertDuration: 0
                    });
                });
            };

            function initializeSegmentsFromHourlyChart(hourlyChart, shiftStart, shiftEnd, inTime, outTime) {
                var timeSegments = [];
                if (!hourlyChart || !hourlyChart.length) return timeSegments;
                const today = new Date().toISOString().split('T')[0];
                const shiftStartTs = parseTime(shiftStart);
                const shiftEndTs = parseTime(shiftEnd);
                const inTimeTs = inTime ? parseTime(inTime) : null;

                if (!inTimeTs) {
                    hourlyChart.forEach(item => {
                        const segStart = parseTime(`${today}T${item.label}:00.000Z`);
                        timeSegments.push({
                            type: item.type,
                            start: moment(segStart).format('HH:mm'),
                            end: moment(segStart + (item.duration_minutes * 60 * 1000)).format('HH:mm'),
                            percentage: (item.duration_minutes / 60) * 100 / 24
                        });
                    });
                    return timeSegments;
                }

                if (inTimeTs > shiftStartTs) {
                    timeSegments.push({
                        type: 'absent',
                        start: moment(shiftStartTs).format('HH:mm'),
                        end: moment(inTimeTs).format('HH:mm'),
                        percentage: ((inTimeTs - shiftStartTs) / (1000 * 60 * 60)) * 100 / 24
                    });
                }

                let currentTime = inTimeTs;
                hourlyChart.forEach(item => {
                    const segStart = parseTime(`${today}T${item.label}:00.000Z`);
                    if (segStart < currentTime) return;
                    const segEnd = segStart + (item.duration_minutes * 60 * 1000);
                    timeSegments.push({
                        type: item.type,
                        start: moment(currentTime).format('HH:mm'),
                        end: moment(segEnd).format('HH:mm'),
                        percentage: (item.duration_minutes / 60) * 100 / 24
                    });
                    currentTime = segEnd;
                });

                if (!outTime) {
                    timeSegments.push({
                        type: 'productive',
                        start: moment(currentTime).format('HH:mm'),
                        end: 'now',
                        percentage: ((new Date().getTime() - currentTime) / (1000 * 60 * 60)) * 100 / 24
                    });
                }

                return timeSegments;
            }

            function parseTime(timeStr) {
                if (!timeStr) return null;
                const today = new Date().toISOString().split('T')[0];
                let date;
                if (timeStr.includes('T')) {
                    date = new Date(timeStr);
                } else {
                    const [hours, minutes, seconds = 0] = timeStr.split(':').map(Number);
                    date = new Date(
                        `${today}T${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}.000Z`
                    );
                }
                return date.getTime() + (5.5 * 60 * 60 * 1000); // Adjust to IST
            }

            /* Initializing */
            CalendarApp.prototype.init = function() {
                this.enableDrag();
                var today = new Date();
                var $this = this;

                $this.$calendarObj = $this.$calendar.fullCalendar({
                    slotDuration: '00:15:00',
                    minTime: '08:00:00',
                    maxTime: '19:00:00',
                    defaultView: 'month',
                    handleWindowResize: true,
                    header: {
                        right: 'prev,next',
                        left: 'title'
                    },
                    events: function(start, end, timezone, callback) {
                        $.ajax({
                            url: calendarDataUrl,
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                start: start.format('YYYY-MM-DD'),
                                end: end.format('YYYY-MM-DD')
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                var events = [];
                                if (response.data && response.data.length > 0) {
                                    events = response.data.map(function(item) {
                                        let classList = [];

                                        // If backend gave string className (e.g., "bg-danger"), turn it into array
                                        if (typeof item.className === 'string') {
                                            classList = item.className.split(' ');
                                        } else if (Array.isArray(item.className)) {
                                            classList = item.className;
                                        }
                                        if (item.can_edit === false || item.can_edit ===
                                            "false") {
                                            classList.push('event-disabled');
                                        }

                                        return {
                                            title: item.title || 'No Status',
                                            start: item.start,
                                            className: classList, // <-- now an array of classes
                                            attendance_id: item.attendance_id,
                                            check_in: item.check_in,
                                            check_out: item.check_out,
                                            break_duration: item.break_duration,
                                            late_duration: item.late_duration,
                                            production_hours: item.production_hours,
                                            working_hours: item.working_hours,
                                            over_time: item.over_time,
                                            can_edit: item.can_edit
                                        };
                                    });
                                }

                                events.push({
                                    title: 'Today Date',
                                    start: today,
                                    end: today,
                                    className: 'bg-success'
                                });
                                callback(events);
                            },
                            error: function(xhr) {
                                console.error('Failed to fetch calendar data:', xhr
                                    .responseText);
                                callback([]);
                            }
                        });
                    },
                    showNonCurrentDates: false,
                    editable: false,
                    droppable: false,
                    eventLimit: true,
                    selectable: true,
                    contentHeight: 400,
                    drop: function(date) {
                        $this.onDrop($(this), date);
                    },
                    select: function(start, end, allDay) {
                        $this.onSelect(start, end, allDay);
                    },
                    eventClick: function(calEvent, jsEvent, view) {
                        $this.onEventClick(calEvent, jsEvent, view);
                    }
                });
                this.$saveCategoryBtn.on('click', function() {
                    var categoryName = $this.$categoryForm.find("input[name='category-name']").val();
                    var categoryColor = $this.$categoryForm.find("select[name='category-color']").val();
                    if (categoryName !== null && categoryName.length !== 0) {
                        $this.$extEvents.append('<div class="calendar-events" data-class="bg-' + categoryColor +
                            '" style="position: relative;"><i class="fas fa-circle text-' + categoryColor +
                            '"></i>' + categoryName + '</div>');
                        $this.enableDrag();
                    }
                });
            };

            $.CalendarApp = new CalendarApp();
            $.CalendarApp.Constructor = CalendarApp;

        }(window.jQuery);

        // Initialize CalendarApp and Date/Time Pickers
        ! function($) {
            "use strict";
            $.CalendarApp.init();
            $('#add_leaves .datetimepicker').datepicker({
                format: 'mm/dd/yyyy',
                autoclose: true
            });
            $('#attendance_request .timepicker').timepicker({
                defaultTime: false,
                showMeridian: true,
                minuteStep: 1
            });
            $('#add_leaves .datetimepicker').on('change', function() {
                var fromDate = $('#add_leaves .datetimepicker').eq(0).datepicker('getDate');
                var toDate = $('#add_leaves .datetimepicker').eq(1).datepicker('getDate');
                if (fromDate && toDate) {
                    var diff = Math.floor((toDate - fromDate) / (1000 * 60 * 60 * 24)) + 1;
                    $('#add_leaves input[name="total_days"]').val(diff >= 0 ? diff : 0);
                }
            });
        }(window.jQuery);


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
@endpush
@endsection
