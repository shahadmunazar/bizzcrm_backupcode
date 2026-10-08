<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Leads Export</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; margin-top: 20px; table-layout: fixed; word-wrap: break-word; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; vertical-align: top; }
        th { background: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .date { text-align: right; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Leads Export Report</h2>
    </div>
    <div class="date">
        Generated on: {{ now()->format('Y-m-d H:i:s') }}
    </div>
    <table>
        <thead>
            <tr>
                @foreach($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($leads as $lead)
                <tr>
                    @foreach($export->map($lead) as $value)
                        <td>{!! nl2br(e($value)) !!}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
