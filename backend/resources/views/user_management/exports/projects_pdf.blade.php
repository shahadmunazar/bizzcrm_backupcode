<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Projects Report</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Client Name</th>
                <th>Priority</th>
                <th>Start Date</th>
                <th>Due Date</th>
                <th>Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $project)
                <tr>
                    <td>{{ $project->id }}</td>
                    <td>{{ $project->name }}</td>
                    <td>{{ $project->client_name ?? 'N/A' }}</td>
                    <td>{{ $project->priority }}</td>
                    <td>{{ $project->start_date ?? '---' }}</td>
                    <td>{{ $project->due_date ?? '---' }}</td>
                    <td>{{ $project->projectType ? $project->projectType->name : 'N/A' }}</td>
                    <td>{{ $project->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
