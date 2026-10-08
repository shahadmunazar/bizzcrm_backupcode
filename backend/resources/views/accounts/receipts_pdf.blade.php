<!DOCTYPE html>
<html>
<head>
    <title>Receipts Export</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Receipts List</h2>
        <p>Exported on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Invoice No</th>
                <th>Lead/Customer</th>
                <th>Company</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Mode</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receipts as $receipt)
            <tr>
                <td>{{ $receipt->id }}</td>
                <td>{{ $receipt->invoice_number }}</td>
                <td>{{ $receipt->lead->name ?? '-' }}</td>
                <td>{{ $receipt->company->bill_name ?? '-' }}</td>
                <td>{{ $receipt->receipt_date }}</td>
                <td>{{ number_format($receipt->amount, 2) }}</td>
                <td>{{ $receipt->payment_mode }}</td>
                <td>{{ $receipt->reference }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
