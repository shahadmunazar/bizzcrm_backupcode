<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Employees Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
            table-layout: fixed; /* Fix column widths */
        }
        th, td {
            border: 1px solid #888;
            padding: 6px 8px;
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap; /* Prevent text wrapping */
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-img {
            max-width: 50px;
            max-height: 50px;
        }

        /* Set column widths for better layout */
        th:nth-child(1), td:nth-child(1) { width: 3%; }  /* ID */
        th:nth-child(2), td:nth-child(2) { width: 5%; }  /* User ID */
        th:nth-child(3), td:nth-child(3) { width: 10%; } /* Name */
        th:nth-child(4), td:nth-child(4) { width: 6%; }  /* PAN */
        th:nth-child(5), td:nth-child(5) { width: 6%; }  /* Emp. ID */
        th:nth-child(6), td:nth-child(6) { width: 7%; }  /* Phone */
        th:nth-child(7), td:nth-child(7) { width: 12%; } /* Email */
        th:nth-child(8), td:nth-child(8) { width: 7%; }  /* Join Date */
        th:nth-child(9), td:nth-child(9) { width: 7%; }  /* Department */
        th:nth-child(10), td:nth-child(10) { width: 7%; } /* Profile Image */
        th:nth-child(11), td:nth-child(11) { width: 7%; } /* Tour */
        th:nth-child(12), td:nth-child(12) { width: 7%; } /* Customer */
        th:nth-child(13), td:nth-child(13) { width: 5%; } /* Status */
        th:nth-child(14), td:nth-child(14) { width: 7%; } /* Bank Name */
        th:nth-child(15), td:nth-child(15) { width: 7%; } /* Account No */
        th:nth-child(16), td:nth-child(16) { width: 7%; } /* Branch */
        th:nth-child(17), td:nth-child(17) { width: 7%; } /* IFSC */
    </style>
</head>
<body>

    <div class="header">
        <h2>Employees Report</h2>
        <p>Generated on: {{ date('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Name</th>
                <th>PAN</th>
                <th>Emp. ID</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Join Date</th>
                <th>Department</th>
                <th>Profile Image</th>
                <th>Tour</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Bank Name</th>
                <th>Account No</th>
                <th>Branch</th>
                <th>IFSC</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
                <tr>
                    <td>{{ $employee->id }}</td>
                    <td>{{ $employee->user_id }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->pan_number }}</td>
                    <td>{{ $employee->employee_id }}</td>
                    <td>{{ $employee->phone }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>{{ \Carbon\Carbon::parse($employee->joining_date)->format('d M Y') }}</td>
                    <td>{{ $employee->department }}</td>
                    <td>
                        @if($employee->profile_image_path)
                            <img src="{{ public_path($employee->profile_image_path) }}" alt="Profile" class="profile-img" />
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $employee->assign_tour ?? 'N/A' }}</td>
                    <td>{{ $employee->assign_customer ?? 'N/A' }}</td>
                    <td>{{ $employee->status }}</td>
                    <td>{{ optional($employee->bankDetail)->bank_name ?? 'N/A' }}</td>
                    <td>{{ optional($employee->bankDetail)->account_number ?? 'N/A' }}</td>
                    <td>{{ optional($employee->bankDetail)->branch ?? 'N/A' }}</td>
                    <td>{{ optional($employee->bankDetail)->ifsc_code ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
