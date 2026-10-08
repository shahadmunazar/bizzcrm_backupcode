<!DOCTYPE html>
<html>
<head>
    <title>WhatsApp Campaigns Export</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .mb-4 { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2 class="text-center mb-4">WhatsApp Campaigns Report</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Template</th>
                <th>Status</th>
                <th>Messages Sent</th>
                <th>Schedule At</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($campaigns as $campaign)
            <tr>
                <td>{{ $campaign->id }}</td>
                <td>{{ $campaign->name }}</td>
                <td>{{ $campaign->template ? $campaign->template->name : 'N/A' }}</td>
                <td>{{ $campaign->status }}</td>
                <td>{{ $campaign->messages_count ?? 0 }}</td>
                <td>{{ $campaign->schedule_at ? $campaign->schedule_at : 'Immediate' }}</td>
                <td>{{ $campaign->created_at ? $campaign->created_at->format('Y-m-d H:i:s') : '---' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
