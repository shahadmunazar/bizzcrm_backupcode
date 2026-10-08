<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report - {{ $month }}/{{ $year }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid #999;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        th, td {
            padding: 6px;
            text-align: center;
        }
        .summary {
            margin-top: 10px;
            border: 1px solid #999;
            padding: 8px;
        }
        .summary h4 {
            margin: 0 0 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $box['name'] }} - Attendance Report</h2>
        <p>Month: {{ $month }} / {{ $year }}</p>
    </div>

    @foreach ($performance_data as $employee)
        <h3>{{ $employee['employee_name'] }} ({{ $employee['employee_id'] }})</h3>
        <p>
            Department: {{ $employee['department'] ?? 'N/A' }} |
            Designation: {{ $employee['designation'] ?? 'N/A' }} |
            Shift: {{ $employee['shift_name'] ?? 'N/A' }}
        </p>

        <table>
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Working Hours</th>
                    <th>Overtime</th>
                    <th>Late Duration</th>
                    <th>Early Login</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employee['day_wise_status'] as $day)
                    <tr>
                        <td>{{ $day['day_number'] }}</td>
                        <td>{{ $day['date'] }}</td>
                        <td>{{ $day['status'] }}</td>
                        <td>{{ $day['check_in'] ?? '--' }}</td>
                        <td>{{ $day['check_out'] ?? '--' }}</td>
                        <td>{{ $day['working_hours'] }}</td>
                        <td>{{ $day['over_time'] }}</td>
                        <td>{{ $day['late_duration'] }}</td>
                        <td>{{ $day['early_login'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <h4>Monthly Summary</h4>
            <p>
                Present: {{ $employee['summary']['total_present'] }} |
                Absent: {{ $employee['summary']['total_absent'] }} |
                Leave: {{ $employee['summary']['total_leave'] }} |
                Holidays: {{ $employee['summary']['total_holidays'] }} |
                WO: {{ $employee['summary']['total_wo'] }}
            </p>
            <p>
                Expected Hours: {{ $employee['summary']['expected_hours'] }} |
                Production Hours: {{ $employee['summary']['total_production_hours'] }} |
                OT Hours: {{ $employee['summary']['total_overtime_hours'] }}
            </p>
            <p>
                Late Duration: {{ $employee['summary']['total_late_duration'] }} |
                Early Login: {{ $employee['summary']['total_early_login'] }}
            </p>
            <p>
                Efficiency: {{ $employee['summary']['efficiency_percent'] }}%
            </p>
        </div>
    @endforeach

</body>
</html>
