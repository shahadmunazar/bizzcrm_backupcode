@extends('layouts.main')
@section('content')
@section('title', 'Performance Report')
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-sm-8">
                            <h4 class="page-title">Performance Report </h4>
                        </div>
                        <div class="col-sm-4 text-sm-end">
                            <div class="head-icons">
                                <a href="javascript:void(0);" id="download-pdf-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Download PDF"><i class="ti ti-file-type-pdf"></i></a>
                                <a href="calls.html" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-original-title="Collapse" id="collapse-header"><i
                                        class="ti ti-chevrons-up"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="attendance-container"></div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            var attendanceReportStr = localStorage.getItem('attendanceReport');
            var attendanceReport;
            try {
                attendanceReport = attendanceReportStr ? JSON.parse(attendanceReportStr) : {
                    performance_data: []
                };
            } catch (e) {
                attendanceReport = {
                    performance_data: []
                };
            }
            var data = attendanceReport.performance_data || [];
            var companyName = attendanceReport.box?.name || 'Your Company Name';
            var fromDate = attendanceReport.from_date || '';
            var toDate = attendanceReport.to_date || '';
            var container = $('#attendance-container');
            container.empty();
            container.append(`
    <div class="report-header mb-4">
        <h3 class="mb-2 text-primary fw-bold">${companyName}</h3>
        <p class="mb-0">
            <strong>Report Period:</strong> ${fromDate} <strong>to</strong> ${toDate}
        </p>
    </div>
`);

            data.forEach(function(employee) {

                var trDays = $('<tr style="height:17pt"></tr>');
                trDays.append(`<td style="width:42pt;"><p class="s4"></p></td>`);
                (employee.day_wise_status || []).forEach(function(day, index) {
                    var width = index % 2 === 0 ? "25pt" : "24pt";
                    trDays.append(
                        `<td style="width:${width};"><p class="s4">${day.day_number}</p></td>`);
                });


                var trArrival = $('<tr style="height:13pt"></tr>');
                trArrival.append(`<td style="width:42pt;"><p class="s5">Arrival Time</p></td>`);
                (employee.day_wise_status || []).forEach(function(day, index) {
                    var width = index % 2 === 0 ? "25pt" : "24pt";
                    var checkIn = day.check_in || "--:--";
                    trArrival.append(
                        `<td style="width:${width};"><p class="s5">${checkIn}</p></td>`);
                });

                // 3. Departure Time
                var trDeparture = $('<tr style="height:9pt"></tr>');
                trDeparture.append(`<td style="width:42pt;"><p class="s5">Dep. Time</p></td>`);
                (employee.day_wise_status || []).forEach(function(day, index) {
                    var width = index % 2 === 0 ? "25pt" : "24pt";
                    var checkOut = day.check_out || "--:--";
                    trDeparture.append(
                        `<td style="width:${width};"><p class="s5">${checkOut}</p></td>`);
                });

                // 4. Working Hours
                var trWorkHrs = $('<tr style="height:9pt"></tr>');
                trWorkHrs.append(`<td style="width:42pt;"><p class="s5">Working Hrs</p></td>`);
                (employee.day_wise_status || []).forEach(function(day, index) {
                    var width = index % 2 === 0 ? "25pt" : "24pt";
                    var workingHours = day.working_hours || "--:--";
                    trWorkHrs.append(
                        `<td style="width:${width};"><p class="s5">${workingHours}</p></td>`);
                });

                // 5. Overtime Hours
                var trOT = $('<tr style="height:9pt"></tr>');
                trOT.append(
                    `<td style="width:42pt;"><p class="s5" style="padding-top:1pt;">O.Time Hrs</p></td>`
                    );
                (employee.day_wise_status || []).forEach(function(day, index) {
                    var width = index % 2 === 0 ? "25pt" : "24pt";
                    var overTime = day.over_time || "--:--";
                    trOT.append(`<td style="width:${width};"><p class="s5">${overTime}</p></td>`);
                });

                // 6. Status
                var trStatus = $('<tr style="height:9pt"></tr>');
                trStatus.append(`<td style="width:42pt;"><p class="s5">Status</p></td>`);
                (employee.day_wise_status || []).forEach(function(day, index) {
                    var width = index % 2 === 0 ? "25pt" : "24pt";
                    var status = day.status || "--";
                    trStatus.append(`<td style="width:${width};"><p class="s5">${status}</p></td>`);
                });

                // 7. Break Duration
                var trBreak = $('<tr style="height:13pt"></tr>');
                trBreak.append(`<td style="width:42pt;"><p class="s5">Break</p></td>`);
                (employee.day_wise_status || []).forEach(function(day, index) {
                    var width = index % 2 === 0 ? "25pt" : "24pt";
                    var breakDuration = day.break_duration || "0.00";
                    trBreak.append(
                        `<td style="width:${width};"><p class="s5">${breakDuration}</p></td>`);
                });

                // Employee card with header and daywise table
                var empCard = $(`
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-body">
                    <div class="Salary-slip-box">
                        <div class="table-responsive">
     <div class="row mb-2">
    <div class="col-md-6">
        <h4>
            <span>Department:</span>
            <span>${employee.department || ''}</span>
        </h4>
    </div>
    <div class="col-md-6">
        <h4>
            <span>Designation:</span>
            <span>${employee.designation || ''}</span>
        </h4>
    </div>
</div>



                            <table class="w-100 table table-bordered" style="border-collapse:collapse;" cellspacing="0">
                                <tr>
                                    <td><p class="s1">EmpCode</p></td>
                                    <td><p class="s1">Name</p></td>
                                    <td><p class="s1">Card No.</p></td>
                                    <td><p class="s1">Present</p></td>
                                    <td><p class="s1">WO</p></td>
                                    <td><p class="s1">HL</p></td>
                                    <td><p class="s1">Comp Off</p></td>
                                    <td><p class="s1">Absent</p></td>
                                    <td><p class="s1">Paid_Lv</p></td>
                                    <td><p class="s1">Unpaid_Lv</p></td>
                                    <td><p class="s1">Tot.Leave</p></td>
                                    
                                  
                                    <td><p class="s1">Paid_Days</p></td>
                                      <td><p class="s1">Days_In_Month</p></td>
                                    <td><p class="s1">LateHrs</p></td>
                                    <td><p class="s1">EarlHrs</p></td>
                                    <td><p class="s1">Work Hrs</p></td>
                                    <td><p class="s1">Toatl Work Hrs</p></td>
                                    <td><p class="s1">Short_Hrs</p></td>
                                    <td><p class="s1">OvTim</p></td>
                                </tr>
                                <tr>
                                    <td><p class="s3">${employee.employee_id}</p></td>
                                    <td><p class="s3">${employee.employee_name}</p></td>
                                    <td><p class="s3">00000020</p></td>
                                    <td><p class="s3">${employee?.summary?.total_present || 0}</p></td>
                                    <td><p class="s3">${employee?.summary?.total_wo || 0}</p></td>
                                    <td><p class="s3">${employee?.summary?.total_holidays}</p></td>
                                    <td><p class="s3">${employee?.summary?.total_comp_off || 0}</p></td>
                                    <td><p class="s3">${employee?.summary?.total_absent || 0}</p></td>
                                    <td><p class="s3">${employee?.summary?.paid_days}</p></td>
                                    <td><p class="s3">${employee?.summary?.unpaidDays}</p></td>
                                    <td><p class="s3">${employee?.summary?.total_leave}</p></td>
                                    <td><p class="s3">${employee?.summary?.totalPaidDays}</p></td>
                                    <td><p class="s3">${employee?.summary?.daysInMonth}</p></td>
                                    <td><p class="s3">${employee?.summary?.total_late_duration}</p></td>
                                    <td><p class="s3">${employee?.summary?.total_early_checkout}</p></td>
                                    <td><p class="s3">${employee?.summary?.total_production_hours}</p></td>
                                    <td><p class="s3">${employee?.summary?.expected_hours}</p></td>
                                    <td><p class="s3">${employee?.summary?.short_hours}</p></td>
                                    <td><p class="s3">${employee?.summary?.total_overtime_hours}</p></td>
                                </tr>
                            </table>

                            <table class="w-100 table table-bordered" style="border-collapse:collapse;" cellspacing="0"></table>
                        </div>
                    </div>
                </div>
            </div>
        `);
                var dayTable = empCard.find('table').last();
                dayTable.append(trDays);
                dayTable.append(trArrival);
                dayTable.append(trDeparture);
                dayTable.append(trWorkHrs);
                dayTable.append(trOT);
                dayTable.append(trStatus);
                dayTable.append(trBreak);
                container.append(empCard);
            });

            $(window).on('beforeunload', function() {
                localStorage.removeItem('attendanceReport');
            });

            $('#download-pdf-btn').on('click', function() {
                var month = attendanceReport.month;
                var year = attendanceReport.year;

                if (!month || !year) {
                    alert('Report data not found.');
                    return;
                }

                // Create a hidden form to submit the request
                var form = $('<form>', {
                    action: "{{ route('export.attendace.pdf', ['box_slug' => request()->route('box_slug')]) }}",
                    method: 'POST',
                    target: '_blank'
                });

                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: '{{ csrf_token() }}'
                }));
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'month',
                    value: month
                }));
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'year',
                    value: year
                }));
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'is_download',
                    value: '1'
                }));

                $('body').append(form);
                form.submit();
                form.remove();
            });
        });
    </script>
@endpush
@endsection
