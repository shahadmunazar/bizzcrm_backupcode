<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Performance Report</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 landscape;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8pt;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .report-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #4a90e2;
            padding-bottom: 10px;
        }
        .report-header h3 {
            color: #4a90e2;
            margin: 0 0 5px 0;
            font-size: 16pt;
        }
        .report-header p {
            margin: 0;
            font-size: 10pt;
        }
        .employee-card {
            margin-bottom: 30px;
            page-break-inside: avoid;
            border: 1px solid #ddd;
            padding: 10px;
        }
        .employee-info {
            margin-bottom: 10px;
            background-color: #f9f9f9;
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .employee-info table {
            width: 100%;
            border: none;
        }
        .employee-info td {
            border: none;
            padding: 2px 5px;
        }
        .summary-table, .day-wise-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .summary-table th, .summary-table td, 
        .day-wise-table th, .day-wise-table td {
            border: 1px solid #ccc;
            padding: 4px 1px;
            text-align: center;
            font-size: 6pt;
        }
        .summary-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .day-wise-table th {
            background-color: #e9e9e9;
            font-weight: bold;
        }
        .label-col {
            text-align: left !important;
            padding-left: 5px !important;
            font-weight: bold;
            background-color: #f9f9f9;
            width: 80px;
            font-size: 7pt;
        }
        .day-col {
            width: 18px;
        }
        .status-P { color: green; font-weight: bold; }
        .status-A { color: red; font-weight: bold; }
        .status-H { color: blue; }
        .status-WO { color: #f39c12; }
        .status-CO { color: #8e44ad; }
        .status-LV { color: #27ae60; }
    </style>
</head>
<body>
    <div class="report-header">
        <h3>{{ $data['box']['name'] ?? 'Company Name' }}</h3>
        <p>
            <strong>Attendance Performance Report</strong><br>
            Period: {{ $data['from_date'] }} to {{ $data['to_date'] }}
        </p>
    </div>

    @foreach($data['performance_data'] as $employee)
    <div class="employee-card">
        <div class="employee-info">
            <table>
                <tr>
                    <td><strong>Department:</strong> {{ $employee['department'] }}</td>
                    <td><strong>Designation:</strong> {{ $employee['designation'] }}</td>
                    <td><strong>Shift:</strong> {{ $employee['shift_name'] }}</td>
                </tr>
            </table>
        </div>

        <table class="summary-table">
            <thead>
                <tr>
                    <th>EmpCode</th>
                    <th>Name</th>
                    <th>Card No.</th>
                    <th>Present</th>
                    <th>WO</th>
                    <th>HL</th>
                    <th>Comp Off</th>
                    <th>Absent</th>
                    <th>Paid_Lv</th>
                    <th>Unpaid_Lv</th>
                    <th>Tot.Leave</th>
                   
                    <th>Paid_Days</th>
                     <th>Days_In_Month</th>
                    <th>LateHrs</th>
                    <th>EarlHrs</th>
                    <th>Work Hrs</th>
                    <th>Toatl Work Hrs</th>
                    <th>Short_Hrs</th>
                    <th>OvTim</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $employee['employee_id'] }}</td>
                    <td>{{ $employee['employee_name'] }}</td>
                    <td>00000020</td>
                    <td>{{ $employee['summary']['total_present'] }}</td>
                    <td>{{ $employee['summary']['total_wo'] }}</td>
                    <td>{{ $employee['summary']['total_holidays'] }}</td>
                    <td>{{ $employee['summary']['total_comp_off'] ?? 0 }}</td>
                    <td>{{ $employee['summary']['total_absent'] }}</td>
                    <td>{{ $employee['summary']['paid_days'] }}</td>
                    <td>{{ $employee['summary']['unpaidDays'] }}</td>
                    <td>{{ $employee['summary']['total_leave'] }}</td>
                    <td>{{ $employee['summary']['totalPaidDays'] }}</td>
                    <td>{{ $employee['summary']['daysInMonth'] }}</td>
                    <td>{{ $employee['summary']['total_late_duration'] }}</td>
                    <td>{{ $employee['summary']['total_early_checkout'] }}</td>
                    <td>{{ $employee['summary']['total_production_hours'] }}</td>
                    <td>{{ $employee['summary']['expected_hours'] }}</td>
                    <td>{{ $employee['summary']['short_hours'] }}</td>
                    <td>{{ $employee['summary']['total_overtime_hours'] }}</td>
                </tr>
            </tbody>
        </table>

        <table class="day-wise-table">
            <thead>
                <tr>
                    <th class="label-col">Day</th>
                    @foreach($employee['day_wise_status'] as $day)
                    <th class="day-col">{{ $day['day_number'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="label-col">Arrival</td>
                    @foreach($employee['day_wise_status'] as $day)
                    <td>{{ $day['check_in'] ? date('H:i', strtotime($day['check_in'])) : '--:--' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="label-col">Departure</td>
                    @foreach($employee['day_wise_status'] as $day)
                    <td>{{ $day['check_out'] ? date('H:i', strtotime($day['check_out'])) : '--:--' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="label-col">Work Hrs</td>
                    @foreach($employee['day_wise_status'] as $day)
                    <td>{{ $day['working_hours'] == '--:--' ? '00:00' : $day['working_hours'] }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="label-col">O.Time</td>
                    @foreach($employee['day_wise_status'] as $day)
                    <td>{{ $day['over_time'] == '--:--' ? '00:00' : $day['over_time'] }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="label-col">Status</td>
                    @foreach($employee['day_wise_status'] as $day)
                    <td class="status-{{ $day['status'] }}">{{ $day['status'] }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
    @endforeach
</body>
</html>
