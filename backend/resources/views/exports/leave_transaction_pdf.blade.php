<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Leave Transaction Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            color: #000;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 15px;
        }
        .meta-info td {
            vertical-align: top;
            padding: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #000;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Leave Transaction Report</h1>
        <p>{{ $box->name ?? 'Company Name' }}</p>
    </div>

    <table class="meta-info">
        <tr>
            <td width="50%">
                <strong>Employee:</strong> {{ $user->name }}<br>
                <strong>Email:</strong> {{ $user->email }}
            </td>
            <td width="50%" class="text-right">
                <strong>Generated On:</strong> {{ now()->format('d M Y H:i:s') }}
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="10%">Date</th>
                <th width="10%">Applied</th>
                <th width="10%">Approved</th>
                <th width="12%">Leave Type</th>
                <th width="23%">Transaction Remarks</th>
                <th width="7%" class="text-right">Credit</th>
                <th width="7%" class="text-right">Debit</th>
                <th width="7%" class="text-right">Lapsed</th>
                <th width="7%" class="text-right">C.F.</th>
                <th width="7%" class="text-right">Bal</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalCredit = 0;
                $totalDebit = 0;
                $totalLapsed = 0;
                $totalCF = 0;
            @endphp
            @forelse($transactions as $tx)
                @php
                    $credit = floatval($tx['credit_leave']);
                    $debit = floatval($tx['debit_leave']);
                    $lapsed = floatval($tx['lapsed_leave'] ?? 0);
                    $cf = floatval($tx['carried_forward_leave'] ?? 0);
                    
                    $totalCredit += $credit;
                    $totalDebit += $debit;
                    $totalLapsed += $lapsed;
                    $totalCF += $cf;
                @endphp
                <tr>
                    <td>{{ $tx['date'] }}</td>
                    <td>{{ $tx['applied_date'] }}</td>
                    <td>{{ $tx['approved_date'] }}</td>
                    <td>{{ $tx['leave_type'] }}</td>
                    <td>{{ $tx['transaction_remarks'] }}</td>
                    <td class="text-right">{{ $credit > 0 ? number_format($credit, 2) : '-' }}</td>
                    <td class="text-right">{{ $debit > 0 ? number_format($debit, 2) : '-' }}</td>
                    <td class="text-right">{{ $lapsed > 0 ? number_format($lapsed, 2) : '-' }}</td>
                    <td class="text-right">{{ $cf > 0 ? number_format($cf, 2) : '-' }}</td>
                    <td class="text-right fw-bold">{{ number_format($tx['balance_leave'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">No transactions found.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f9f9f9;">
                <td colspan="5" class="text-right fw-bold">Total:</td>
                <td class="text-right fw-bold">{{ number_format($totalCredit, 2) }}</td>
                <td class="text-right fw-bold">{{ number_format($totalDebit, 2) }}</td>
                <td class="text-right fw-bold">{{ number_format($totalLapsed, 2) }}</td>
                <td class="text-right fw-bold">{{ number_format($totalCF, 2) }}</td>
                <td class="text-right fw-bold">-</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Generated by System
    </div>
</body>
</html>
