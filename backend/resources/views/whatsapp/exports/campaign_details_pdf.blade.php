<!DOCTYPE html>
<html>
<head>
    <title>Campaign Details Export</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .mb-4 { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2 class="text-center mb-4">Campaign Details: {{ $campaign->name ?? 'N/A' }}</h2>
    <table>
        <thead>
            <tr>
                @foreach($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                @foreach($row as $col)
                    <td>{{ $col }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
