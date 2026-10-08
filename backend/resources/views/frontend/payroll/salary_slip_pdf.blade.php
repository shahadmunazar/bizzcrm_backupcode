<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Salary Slip</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .Salary-slip-box {
            border: 1px solid #000;
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        table, th, td {
            border: 1px solid #000;
        }
        td {
            padding: 4px;
            vertical-align: top;
        }
        .s1 {
            font-weight: bold;
            font-size: 9pt;
        }
        .s2 {
            font-size: 9pt;
        }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bg-grey { background-color: #f2f2f2; }
        
        /* Matching the specific styles from the web view */
        p { margin: 0; padding: 0; }
    </style>
</head>
<body>
    <table style="width: 100%; margin-bottom: 20px; border: none; border-collapse: collapse;">
        <tr>
            <td style="width: 25%; text-align: left; border: none; vertical-align: middle;">
                @php
                    $box = $report->box ?? $report->boxUser->box ?? null;
                    $logoPath = null;
                    if ($box) {
                        // Prioritize company_logo since it is managed in /settings/company-settings
                        if (!empty($box->company_logo)) $logoPath = $box->company_logo;
                        elseif (!empty($box->params['pdf_logo'])) $logoPath = $box->params['pdf_logo'];
                        elseif (!empty($box->logo)) $logoPath = $box->logo;
                    }
                @endphp
                @if($logoPath)
                    @php
                        $imagePath = public_path('storage/' . $logoPath);
                        $imgSrc = asset('storage/' . $logoPath);
                        
                        // Use base64 to bypass URL/ngrok issues and DOMPDF http blocks
                        if (file_exists($imagePath)) {
                            $type = pathinfo($imagePath, PATHINFO_EXTENSION);
                            $data = file_get_contents($imagePath);
                            $imgSrc = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        }
                    @endphp
                    <img src="{{ $imgSrc }}" height="60" style="max-height: 60px;">
                @else
                    <!-- No Logo -->
                @endif
            </td>
            <td style="width: 50%; text-align: center; border: none; vertical-align: middle;">
                <h2 style="margin: 0; font-size: 18pt; text-transform: uppercase; color: #000;">{{ $box->name ?? 'N/A' }}</h2>
                <h3 style="margin: 5px 0; font-size: 12pt; color: #333;">SALARY SLIP FOR {{ strtoupper(\Carbon\Carbon::create($report->year, $report->month, 1)->format('F Y')) }}</h3>
            </td>
            <td style="width: 25%; text-align: right; border: none; vertical-align: middle;">
                <!-- Right placeholder for balance -->
            </td>
        </tr>
    </table>

    <div class="Salary-slip-box">
        <table>
            <tr style="height:15pt">
                <td colspan="6">
                    <p class="s1">Company Name : {{ $report->box->name ?? $report->boxUser->box->name ?? 'N/A' }}</p>
                </td>
                <td colspan="2" class="text-right">
                    <p class="s2">Date : {{ date('d/m/Y') }}</p>
                </td>
            </tr>
            <tr style="height:14pt">
                <td style="width:80pt;"><p class="s1">Emp.Code</p></td>
                <td colspan="2"><p class="s2">{{ $report->boxUser->employee_id ?? 'N/A' }}</p></td>
                <td style="width:60pt;"><p class="s1">Emp.Name</p></td>
                <td colspan="2"><p class="s2">{{ $report->boxUser->user->name ?? 'N/A' }}</p></td>
                <td colspan="2" class="text-right"><p class="s2">Dept. : {{ $report->boxUser->departmentRelation->name ?? 'N/A' }}</p></td>
            </tr>
            
            @php
                $attRows = [
                    ['label' => 'Present', 'value' => $report->earnings_breakdown['present_days'] ?? $report->total_working_days ?? 0],
                    ['label' => 'Absent', 'value' => $report->deductions_breakdown['absent_days'] ?? $report->deductions_breakdown['absent_days_from_attendance'] ?? $report->deductions_breakdown['deduction_days'] ?? 0],
                    ['label' => 'HoliDay', 'value' => $report->total_holidays ?? $report->earnings_breakdown['holiday_days'] ?? 0],
                    ['label' => 'Leave', 'value' => $report->leave_days ?? $report->earnings_breakdown['leave_days'] ?? 0],
                    ['label' => 'Week Off', 'value' => $report->total_weekly_offs ?? $report->earnings_breakdown['weekly_off_days'] ?? 0],
                    ['label' => 'Comp Off', 'value' => $report->comp_off_days ?? $report->earnings_breakdown['comp_off_days'] ?? 0],
                    ['label' => 'Paid Days', 'value' => $report->dynamic_paid_days ?? $report->earnings_breakdown['dynamic_paid_days'] ?? $report->paid_days ?? $report->earnings_breakdown['paid_days'] ?? 0],
                    ['label' => 'Std. Days', 'value' => $report->calculation_base_days ?? $report->earnings_breakdown['calculation_base_days'] ?? 30],
                    ['label' => 'Prod. Hrs', 'value' => $totalProductionHours ?? '00:00'],
                    ['label' => 'OT Hrs', 'value' => $totalOTHours ?? '00:00'],
                ];
            @endphp
            
            <!-- ATTENDANCE ROW -->
            <tr style="height:25pt; background-color: #f9f9f9;">
                <td colspan="8" style="padding: 5px 0;">
                    <table style="width: 100%; border: none;">
                        <tr>
                            @foreach($attRows as $index => $att)
                            <td style="border: none; text-align: center; {{ $index < count($attRows) - 1 ? 'border-right: 1px solid #ccc;' : '' }}">
                                <p class="s1" style="font-size: 7.5pt;">{{ $att['label'] }}</p>
                                <p class="s2" style="font-size: 8.5pt; font-weight: bold; margin-top: 2px;">{{ $att['value'] }}</p>
                            </td>
                            @endforeach
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr style="height:14pt">
                <td><p class="s1">Designation</p></td>
                <td colspan="3"><p class="s2">{{ $report->boxUser->designationRelation->name ?? 'N/A' }}</p></td>
                <td colspan="4"><p class="s2">Father Name : {{ $report->boxUser->father_name ?? 'N/A' }}</p></td>
            </tr>
            
            <tr style="height:18pt; background-color: #eeeeee;">
                <td colspan="4" class="text-center" style="border-bottom: 1px solid #000;"><p class="s1" style="font-size: 10pt;">SALARY ADDITIONS</p></td>
                <td colspan="4" class="text-center" style="border-bottom: 1px solid #000;"><p class="s1" style="font-size: 10pt;">SALARY DEDUCTIONS</p></td>
            </tr>
            <tr style="height:14pt; background-color: #f5f5f5;">
                <td colspan="2"><p class="s1">Salary Head</p></td>
                <td class="text-right"><p class="s1">Rate</p></td>
                <td class="text-right"><p class="s1">Amount</p></td>
                
                <td colspan="3"><p class="s1">Deduction Head</p></td>
                <td class="text-right"><p class="s1">Amount</p></td>
            </tr>
            
            @php
                $earnings = $report->earnings_breakdown['structure_earnings'] ?? [];
                $earningKeys = array_keys($earnings);
                
                $deductions = $report->earnings_breakdown['structure_deductions'] ?? [];
                $deductionKeys = array_keys($deductions);
                
                $totalRows = max(count($earningKeys), count($deductionKeys), 1);
            @endphp
            
            @for ($i = 0; $i < $totalRows; $i++)
            <tr style="height:14pt">
                <!-- Earnings -->
                @php
                    $eKey = $i < count($earningKeys) ? $earningKeys[$i] : null;
                    $eLabel = $eKey ? ucwords(str_replace('_', ' ', $eKey)) : '';
                    $eRate = $eKey && isset($earnings[$eKey]['actual']) ? number_format($earnings[$eKey]['actual'], 2) : '';
                    $ePayable = $eKey && isset($earnings[$eKey]['prorated']) ? number_format($earnings[$eKey]['prorated'], 2) : '';
                @endphp
                <td colspan="2"><p class="s1">{{ $eLabel }}</p></td>
                <td class="text-right"><p class="s2">{{ $eRate }}</p></td>
                <td class="text-right"><p class="s2">{{ $ePayable }}</p></td>
                
                <!-- Deductions -->
                @php
                    $dKey = $i < count($deductionKeys) ? $deductionKeys[$i] : null;
                    $dLabel = $dKey ? strtoupper(str_replace(['_', 'deductions'], [' ', ''], $dKey)) : '';
                    $dAmount = $dKey && isset($deductions[$dKey]['prorated']) ? number_format($deductions[$dKey]['prorated'], 2) : '';
                @endphp
                <td colspan="3"><p class="s1">{{ $dLabel }}</p></td>
                <td class="text-right"><p class="s2">{{ $dAmount }}</p></td>
            </tr>
            @endfor            
            <tr style="height:22pt">
                <td colspan="8"></td>
            </tr>
            <tr style="height:12pt">
                <td colspan="3" class="text-right"><p class="s1">Total Earning</p></td>
                <td class="text-right"><p class="s2">{{ number_format(($report->total_earnings ?? $report->gross_salary ?? 0) - ($report->earnings_breakdown['comp_off_encash_amount'] ?? 0), 2) }}</p></td>
                <td colspan="3" class="text-right"><p class="s1">Total Deduction</p></td>
                <td class="text-right"><p class="s2">{{ number_format($report->total_deductions ?? 0, 2) }}</p></td>
            </tr>
            <tr style="height:15pt">
                <td colspan="4" rowspan="3" style="vertical-align: top; padding-top: 5pt;">
                    <p class="s2">Total Amount in Word : <br>({{ $amountInWords }} Rupees Only)</p>
                </td>
                <td colspan="3" class="text-right"><p class="s1" style="color:#555; font-weight: normal;">Net Salary</p></td>
                <td class="text-right"><p class="s1" style="color:#555; font-weight: normal;">{{ number_format(($report->net_salary ?? 0) - ($report->earnings_breakdown['comp_off_encash_amount'] ?? 0), 2) }}</p></td>
            </tr>
            <tr style="height:15pt">
                <td colspan="3" class="text-right"><p class="s1" style="color:#555; font-weight: normal;">Comp Off Amount</p></td>
                <td class="text-right"><p class="s1" style="color:#555; font-weight: normal;">{{ number_format($report->earnings_breakdown['comp_off_encash_amount'] ?? 0, 2) }}</p></td>
            </tr>
            <tr style="height:15pt">
                <td colspan="3" class="text-right"><p class="s1">Total Salary</p></td>
                <td class="text-right"><p class="s1">{{ number_format($report->net_salary ?? 0, 2) }}</p></td>
            </tr>
        </table>
    </div>
    
    <div style="text-align: center; margin-top: 20px; font-size: 8pt; color: #666;">
        <p>This is a computer-generated salary slip and does not require a signature.</p>
        <p>Generated on: {{ date('d-m-Y H:i:s') }}</p>
    </div>
</body>
</html>
