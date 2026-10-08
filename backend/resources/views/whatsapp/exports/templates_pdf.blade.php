<!DOCTYPE html>
<html>
<head>
    <title>WhatsApp Templates</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px; }
        th { background-color: #f4f4f4; }
        .header { text-align: center; margin-bottom: 20px; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 10px; color: #fff; }
        .bg-success { background-color: #28a745; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-danger { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="header">
        <h2>WhatsApp Templates List</h2>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Status</th>
                <th>Meta ID</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($templates as $t)
            <tr>
                <td>{{ $t->name }}</td>
                <td>{{ $t->category }}</td>
                <td>{{ $t->whats_app_status }}</td>
                <td>{{ $t->whats_app_id }}</td>
                <td>{{ $t->created_at ? $t->created_at->format('Y-m-d') : '---' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
