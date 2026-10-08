<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Biometric Machines</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        h2 { text-align: center; margin-bottom: 20px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .status-active { color: green; font-weight: bold; }
        .status-offline { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Biometric Machines List</h2>
    <table>
        <thead>
            <tr>
                <th>Machine No</th>
                <th>Type</th>
                <th>Serial No</th>
                <th>Name</th>
                <th>Branch</th>
                <th>Location</th>
                <th>Status</th>
                <th>Last Sync</th>
            </tr>
        </thead>
        <tbody>
            @foreach($machines as $machine)
            @php
                $statusClass = $machine->sync_status === 'green' ? 'status-active' : 'status-offline';
                $statusText = $machine->sync_status === 'green' ? 'Active' : 'Offline';
                $branchName = $machine->branch ? $machine->branch->name : '-';
                
                $locationParts = [];
                if ($machine->city) $locationParts[] = $machine->city->name;
                elseif ($machine->branch && $machine->branch->city) $locationParts[] = $machine->branch->city->name;
                
                if ($machine->state) $locationParts[] = $machine->state->name;
                elseif ($machine->branch && $machine->branch->state) $locationParts[] = $machine->branch->state->name;
                
                $location = !empty($locationParts) ? implode(', ', $locationParts) : '-';
            @endphp
            <tr>
                <td>{{ $machine->machine_no }}</td>
                <td>{{ $machine->machine_type }}</td>
                <td>{{ $machine->serial_no }}</td>
                <td>{{ $machine->machine_name }}</td>
                <td>{{ $branchName }}</td>
                <td>{{ $location }}</td>
                <td class="{{ $statusClass }}">{{ $statusText }}</td>
                <td>{{ $machine->last_sync ? \Carbon\Carbon::parse($machine->last_sync)->format('Y-m-d H:i') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
